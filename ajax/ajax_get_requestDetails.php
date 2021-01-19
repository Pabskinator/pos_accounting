<?php
	include 'ajax_connection.php';

	$functionName = Input::get('functionName');
	$functionName();

	function getSelectRackOption($all_racks,$select_id=0){
		$rackallSelect = "<select class='form-control rack_all_select'>";
		foreach($all_racks as $allrind) {
			if($allrind->id == $select_id){
				$selectled_option = "selected";
			} else {
				$selectled_option= "";
			}
			$rackallSelect .= "<option value='".$allrind->id."' $selectled_option>".$allrind->rack."</option>";
		}
		return $rackallSelect .= "</select>";
	}

	function getRequestDetails() {
		$user = new User();
		$req_id = Input::get('id');
		$bid = Input::get('branch_id');
		$bid = (int) $bid;
		$od = new Agent_request_details();
		$details = $od->get_active('agent_request_details', array('request_id', "=", $req_id));
		$myReq = new Agent_request($req_id);
		$whoreq = new User($myReq->data()->user_id);
		$disabledbtn ='';
		$rembid='';
		if($bid != $myReq->data()->branch_id){
			$disabledbtn =' disabled ';
			$rembid = '<p class="text-danger">This request does not belong to your branch</p>';
		}
		$rack_all = new Rack();
		$all_racks = $rack_all->get_active('racks',array('company_id','=',$whoreq->data()->company_id));
		$rackallSelect = "<select class='form-control rack_all_select'>";
		foreach($all_racks as $allrind) {
			$rackallSelect .= "<option value='".$allrind->id."'>".$allrind->rack."</option>";
		}
		$rackallSelect .= "</select>";

		?>
		<div>
			<!-- Nav tabs -->
			<ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active"><a href="#tab_details" aria-controls="home" role="tab" data-toggle="tab">Request Details</a></li>
				<?php if($myReq->data()->status == 3 || $myReq->data()->status == 4 || $myReq->data()->status == 5) { ?>
				<li role="presentation"><a href="#tab_liquidation" aria-controls="profile" role="tab" data-toggle="tab">Liquidation</a></li>

					<?php if ($myReq->data()->status == 3)
						{
					?>
							<li role="presentation"><a href="#tab_receive" aria-controls="messages" role="tab" data-toggle="tab">Receiving</a></li>
					<?php
						}
					?>

				<?php } ?>
			</ul>

			<!-- Tab panes -->
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane active" id="tab_details">
					<br>
					<?php echo $rembid; ?>
					<?php if($myReq->data()->status == 1)
					{
					?>
					<div class='row'>
						<div class="col-md-4"><input type="text" class='form-control' id='new_item_id'></div>
						<div class="col-md-4"><input type="text" class='form-control' id='new_qty' placeholder='Quantity'></div>
						<div class="col-md-4"><button data-id='<?php echo $myReq->data()->id; ?>' class='btn btn-default' id='btnAddNew'>Add Item</button></div>
					</div>
					<?php
					}
					?>


					<div id='no-more-tables'>
						<div id='printtblorder'>
							<table id='tblorder' class="table">
								<thead>
								<tr>
									<th>Barcode</th>
									<th>Item Code</th>
									<th>Price</th>
									<th>Quantity</th>
									<th>
										<?php
											if($myReq->data()->status == 6){
												echo 'Rack';
											}
										?>
									</th>
									<th>
										<?php
											if($myReq->data()->status == 6 || $myReq->data()->status == 1){
												echo 'Stock';
											}
										?>
									</th>
									<th>Total</th>
								</tr>
								</thead>
								<tbody>
								<?php
									$total = 0;
									$process = true;
									foreach($details as $index => $d) {

										$inventory = new Inventory();
										$rack = new Rack();

										$item = new Product($d->item_id);
										$rackwithinv = $inventory->allStockBaseOnItem($item->data()->id,$user->data()->company_id,$myReq->data()->branch_id);


										$price = $item->getPrice($item->data()->id);

										?>
										<tr data-item_id='<?php echo $d->item_id; ?>'>
											<td data-title='Barcode'>
												<?php
													if($myReq->data()->status == 1)
													{
												?>
													<button data-req_id='<?php echo $myReq->data()->id; ?>'data-id='<?php echo $d->id; ?>'  class='btn btn-danger btn-sm btnDeleteItem'><i class='fa fa-remove'></i></button>
												<?php
													}
												?>


												<?php echo $item->data()->barcode; ?>
											</td>
											<td data-title='Item'><?php echo $item->data()->item_code; ?><br><small class='text-danger'><?php echo escape($item->data()->description); ?></small></td>
											<td data-title='Price'><?php echo number_format($price->price,2) ?></td>
											<td data-title='Qty'>
												<?php

													if(($myReq->data()->status == 1 || $myReq->data()->status == 6) && $user->hasPermission("caravan_manage")){
														echo "<input class='form-control editItemNewQty' style='width:80px;' data-specid='".$d->id."' type='text'  data-prev_qty='".formatQuantity($d->qty,true)."' value='". formatQuantity($d->qty,true) ."' />";
														echo "<button class='btn btn-default editItemSave' style='display:none;margin-top:3px;width:80px;' data-req_id='$req_id' data-branch='$bid'><i class='fa fa-save'></i> Save</button>";
													}else {
														echo "<strong > " . formatQuantity($d->qty,true) . "</strong>";
													}

												?>
											</td>
											<td <?php
												if($myReq->data()->status == 6){
													echo "data-title='Rack'";
												}
											?>>
												<?php
													if($myReq->data()->status == 6){
														if($rackwithinv){
															echo "<select id='rack".$d->item_id."' class='form-control rackallocation'>";
															echo "<option value=''>--Choose Rack--</option>";
															foreach($rackwithinv as $rowrackinv){
																echo "<option data-qty=".$rowrackinv->qty." value='".$rowrackinv->rack_id."'>$rowrackinv->rack</option>";
															}
														} else {
															echo "No available stocks";
														}
													}

												?>
											</td>
											<td <?php
												if($myReq->data()->status == 6){
													echo "data-title='Stock'";
												}
											?>>
												<?php
													if($myReq->data()->status == 6){
														echo 0;
													}
													if($myReq->data()->status == 1){
														$total_rack_inv = 0;
														if($rackwithinv){
															foreach($rackwithinv as $rowrackinv){
																$total_rack_inv += $rowrackinv->qty;
																echo "$rowrackinv->rack  <span class='badge pull-right'> " . formatQuantity($rowrackinv->qty,true) ."</span><br>";
															}
														} else {
															echo "No available stocks";
														}
														echo "<input type='hidden' value='$total_rack_inv'>";
													}
												?>
											</td>
											<td data-title='Total'>
												<?php
													echo number_format($d->qty * $price->price,2);
													$total += $d->qty * $price->price;
												?>
											</td>
										</tr>
										<?php
									}
								?>
								<tr>

									<td colspan='7' class='well'>Total: <strong class='text-danger'> <?php echo number_format($total,2); ?> </strong></td>
								</tr>
								</tbody>
							</table></div></div>
				</div>
				<?php if($myReq->data()->status == 3 || $myReq->data()->status == 4 || $myReq->data()->status == 5) { ?>
				<div role="tabpanel" class="tab-pane" id="tab_liquidation">
					<h3>Liquidation</h3>
					<?php
						$mycaravans = new Caravan_liquidation();
						$mylist = $mycaravans->get_caravan_request($req_id);
						if($mylist) {
							?>
							<div id="no-more-tables">
								<table  class="table" style='margin: 0 auto;'>
									<thead>
									<tr>
										<th>SR</th>
										<th>Barcode</th>
										<th>Item Code</th>
										<th>Price</th>
										<th>Qty</th>
										<th>Discount</th>
										<th>Total</th>
										<th>Sold to</th>
									</tr>
									</thead>
									<tbody>
									<?php
										$totalcaravansale = 0;
										$totalcaravanunsold = 0;
										$toreturn = array();
										foreach($mylist as $m) {
											$mproduct = new Product($m->item_id);
											$mprice = $mproduct->getPrice($m->item_id);

											?>
											<tr >
												<td data-title='SR'><?php echo $m->sr ?></td>
												<td data-title='Barcode'><?php echo $mproduct->data()->barcode; ?></td>
												<td data-title='Item code'><?php echo $mproduct->data()->item_code; ?></td>
												<td data-title='Price'><?php echo $mprice->price ?></td>
												<td data-title='Qty'><?php echo $m->qty ?></td>
												<td data-title='Discount'><?php echo $m->discount ?></td>
												<td data-title='Total'><?php echo number_format(($m->qty * $mprice->price)- $m->discount,2) ?> </td>
												<td data-title='Remarks' style='width:30%;'>
													<?php
														if($m->member_id) {
															$mmember = new Station($m->member_id);
															echo isset($mmember->data()->name) ? $mmember->data()->name : 'Not Avail.';
															$totalcaravansale += ($m->qty * $mprice->price) - $m->discount;
														} else {
															$tret['id'] = $mproduct->data()->id;
															$tret['barcode'] =$mproduct->data()->barcode;
															$tret['item_code'] =$mproduct->data()->item_code;
															$tret['qty'] =$m->qty;
															$toreturn[] = $tret;
															$totalcaravanunsold += ($m->qty * $mprice->price) - $m->discount;
															$label = ($myReq->data()->status == 3) ? "Not Sold. Need to re-enter in inventory" : "Not Sold.";
															echo "<span class='text-danger'>$label";
														}
													?>
												</td>
											</tr>
											<?php
										}
									?>
									<tr><td  class='text-left  well' colspan='8'>Total Sold Item: <strong class='text-danger'><?php echo number_format($totalcaravansale,2); ?></strong> Total Unsold Item:<strong class='text-danger'><?php echo  number_format($totalcaravanunsold,2); ?></strong></td></tr>
									</tbody>
								</table>
							</div>
							<?php
						}
					?>
				</div> <!-- end tab liquidation -->
				<?php if ($myReq->data()->status == 3) {
				?>
				<div role="tabpanel" class="tab-pane" id="tab_receive">
				<?php if($myReq->data()->status == 3) {
					if($toreturn){

					?>
					<h3>Items to Receive</h3>
					<table id='tblToReturn' class='table' >
						<thead>
						<tr><th>Barcode</th><th>Item code</th><th>Qty</th><th>Damage</th><th>Missing</th><th></th></tr>
						</thead>
						<tbody>
						<?php
							$agent_request_details_cls = new Agent_request_details();
							foreach ($toreturn as $value) {
								$rack_agent_details= $agent_request_details_cls->getRack($req_id,$value['id']);
								$rack_agent_details_id =0;
								if(isset($rack_agent_details->rack_id) && $rack_agent_details->rack_id){
									$rack_agent_details_id = $rack_agent_details->rack_id;
								}
							?>
							<tr data-id='<?php echo $value['id']; ?>' >
								<td><?php echo $value['barcode']; ?></td>
								<td><?php echo $value['item_code']; ?></td>
								<td><?php echo $value['qty']; ?></td>
								<td><?php echo "<input type='text' placeholder='Damage Qty' class='form-control rdamageqty' value=''>"?></td>
								<td><?php echo "<input type='text' placeholder='Missing Qty' class='form-control rmissingqty' value=''>"?></td>
								<td><?php echo  getSelectRackOption($all_racks,$rack_agent_details_id); ?></td>
							</tr>
							<?php
							}

						?>
						</tbody>
					</table>

						<p class='text-left' style='color:#777'>By verifying this request. You're receiving the items specified on the list.</p>
					<?php
					} else {
						?>
						<br><p class='text-left' style='color:#777'>No item to receive. </p>
						<?php
					}
					?>
					<div class='text-right' style='margin-top:3px;'>
						<button <?php echo $disabledbtn; ?> data-request_id='<?php echo $req_id; ?>'  id='verify' class='btn btn-success'><span class='glyphicon glyphicon-ok'></span> <span class='hidden-xs'>Verify</span></button>
					</div>
				<?php
				} ?>
				</div> <!-- end tab receive -->
					<?php
				} ?>
			</div>
		</div>
		<?php
	} ?>
		<hr />
		<?php if($myReq->data()->status == 3 || $myReq->data()->status == 4 || $myReq->data()->status == 5) { ?>

			<?php    if($user->hasPermission("caravan_manage")) {
					  // end status 3
					 if($myReq->data()->status == 5) {
					?>

					<hr>
					<div class='text-right'>
						<p class='text-left' style='color:#777'>By verifying this request. You're receiving the amount specified on the liquidation.</p>

						<button <?php echo $disabledbtn; ?> data-request_id='<?php echo $req_id; ?>'  id='verifySale' class='btn btn-success' ><span class='glyphicon glyphicon-ok'></span> <span class='hidden-xs'>Verify</span></button>
					</div>
				<?php
				}
			} else if($myReq->data()->status == 4) {

				?>
				<hr />
				<div class="text-right">
					<p class='text-left' style='color:#777'>Verified</p>
				</div>

			<?php
			} else {
				?>
				<hr />
				<div class="text-right">
					<p class='text-left' style='color:#777'>Waiting for verification</p>
				</div>

			<?php
			} ?>
		<?php } ?>

		<div class='text-right'>
			<?php
				if($myReq->data()->status == 6) {
					if($user->hasPermission("caravan_manage")) {
						if($process) {
							?>
							<p class='text-left' style='color:#777'> After clicking the Release Item button, the item(s) here will be deducted in the inventory.</p>
							<button <?php echo $disabledbtn; ?>  class='btn btn-default pull-left' id='printorder' data-name="<?php echo escape(ucwords($whoreq->data()->lastname . ", " .$whoreq->data()->firstname . " " . $whoreq->data()->middlename));  ?>" data-remarks="<?php echo escape($myReq->data()->remarks) ?>" data-witness="<?php echo escape($myReq->data()->witness) ?>" data-order_id="<?php echo $req_id; ?>" ><span class='glyphicon glyphicon-print'></span> <span class='hidden-xs'>Print</span></button>
							<button <?php echo $disabledbtn; ?>  class='btn btn-danger' id='returnOrder' data-order_id="<?php echo $req_id; ?>"><span class='glyphicon glyphicon-refresh'></span> <span class='hidden-xs'>Return Request</span></button>
							<button <?php echo $disabledbtn; ?>  class='btn btn-success' id='processOrder' data-order_id="<?php echo $req_id; ?>"><span class='glyphicon glyphicon-ok'></span> <span class='hidden-xs'>Release Item</span></button>

						<?php
						} else {
							?>
							<p class='text-left' style='color:#777'> Unable to process. Not Enough Stock</p>
						<?php
						}
						?>
						
					<?php
					} else {
						?>
					<p class='text-left' style='color:#777'>Waiting for item's processing</p>
					<?php
					}
					?>
				<?php
				} else if($myReq->data()->status == 1) {
					
					if($user->hasPermission("caravan_request")) {
							if($myReq->data()->user_id == $user->data()->id) {
						?>
						<p class='text-left' style='color:#777'>Waiting for  manager's approval</p>
					<?php
						}
		
					}
					if($user->hasPermission("caravan_manage")) {
						?>


						<button <?php echo $disabledbtn; ?> class='btn btn-primary' id='approveorder' data-order_id="<?php echo $req_id; ?>"><span class='glyphicon glyphicon-ok'></span> <span class='hidden-xs'>Approve Request</span></button>
						<button <?php echo $disabledbtn; ?>  class='btn btn-danger' id='declineorder' data-order_id="<?php echo $req_id; ?>"><span class='glyphicon glyphicon-remove'></span> <span class='hidden-xs'>Decline Request</span></button>
						<?php
					}
				} else if($myReq->data()->status == 2) {
					if($user->hasPermission("caravan_request") && ($myReq->data()->is_approve_liq == 0 || $myReq->data()->is_approve_liq == -1)) {
						if($myReq->data()->user_id == $user->data()->id) {
							?>
							<p class='text-left' style='color:#777'>All items in your possession must be liquidated</p>
							<input <?php echo $disabledbtn; ?> type="button" class='btn btn-success' id='liquidateRequest' data-order_id="<?php echo $req_id; ?>" value="Liquidate" />
						<?php
						}
					} else {
						?>
						<p class='text-left' style='color:#777'>Waiting for verification</p>
						<?php
					}
					if($user->hasPermission("caravan_manage")  && $myReq->data()->is_approve_liq == 0) {
						?>
						<p class='text-left' style='color:#777'>Waiting for liquidation</p>
					<?php
					} else if ($user->hasPermission("caravan_manage")  && $myReq->data()->is_approve_liq == 1){
						?>
						<p class='text-left' style='color:#777'>Verify if all items are liquidated properly</p>
						<input <?php echo $disabledbtn; ?> type="button" class='btn btn-success' id='liquidateRequestVerify' data-order_id="<?php echo $req_id; ?>" value="Verify liquidation" />
						<?php
					}
				}
			?>
		</div>
		<script>
			$('.editItemNewQty').keyup(function(){
					var thiscontext = $(this);
					var row = $(this).parents('tr');
					var prevqty = parseInt(thiscontext.attr('data-prev_qty'));
					var newqty = parseInt(thiscontext.val());
					var stock = parseInt(row.children().eq(5).find('input').val());
					var next = $(this).next();
					if (isNaN(newqty) || newqty < 1 || newqty > stock){
							alertify.alert('Invalid quantity');
							thiscontext.val(prevqty);
							next.hide();
							return;
					}

					if(prevqty != newqty){
						next.show();
					} else {
						next.hide();
					}
			});
			$('.editItemSave').click(function(){
				var thiscontext = $(this);
				var prev = $(this).prev();
				var editid = prev.attr('data-specid');
				var newqty = prev.val();
				var reqid = thiscontext.attr('data-req_id');
				var bid = thiscontext.attr('data-branch');
				$.ajax({
					url: '../ajax/ajax_query.php',
					type: 'post',
					data: {functionName: 'updateCaravanOrderQty', id: editid,qty:newqty,reqid:reqid},
					success: function(data) {
						$.ajax({
							url: '../ajax/ajax_get_requestDetails.php',
							type:'POST',
							data:{functionName:"getRequestDetails",id:reqid,branch_id:bid},
							success:function(data){
								$('#mbody').html(data);
							}
						});
					}
				});

			});
			$("#processOrder").click(function() {
				var btncontext = $(this);
				var oldbtnvalue =btncontext.html();
				btncontext.attr('disabled',true);
				btncontext.html('Loading...');
				var id = $(this).attr('data-order_id');
				alertify.confirm('Are you sure you want to process this request',function(e){
					if(e) {
						var arrjson = [];
						var valid = true;
						$('#tblorder > tbody > tr').each(function(){
							var row = $(this);
							var item_id = row.attr('data-item_id');
							if(item_id){
								var rack_id = row.children().eq(4).find('select').val();
								var qty = row.children().eq(3).find('input').val();
								if(!rack_id) valid = false;
								arrjson.push({
									item_id:item_id,
									rack_id:rack_id,
									qty:qty
								})
							}
						});
						if(valid){

							$('.loading').show();
							$.ajax({
								url: '../ajax/ajax_get_requestDetails.php',
								type: 'post',
								data: {functionName: 'processRequest', id: id,arrjson:JSON.stringify(arrjson)},
								success: function(data) {
									alertify.alert(data,function(){
										location.reload();
										btncontext.attr('disabled',false);
										btncontext.html(oldbtnvalue);
										$('.loading').show();
									});

								},
								error:function(){
									location.reload();
									btncontext.attr('disabled',false);
									btncontext.html(oldbtnvalue);
									$('.loading').show();
								}
							});
						} else {
							alertify.alert('Invalid Order');
							btncontext.attr('disabled',false);
							btncontext.html(oldbtnvalue);
							$('.loading').hide();
						}

					}else {
						btncontext.attr('disabled',false);
						btncontext.html(oldbtnvalue);
					}
				});


			});

			$("#declineorder").click(function() {
				var btncontext = $(this);
				var oldbtnval = btncontext.html();
				btncontext.attr('disabled',true);
				btncontext.html('Loading...');
				var id = $(this).attr('data-order_id');
				alertify.confirm('Are you sure you want to decline this request?',function(e){
					if(e) {
						$('.loading').show();
						$.ajax({
							url: '../ajax/ajax_changestatus.php',
							type: 'post',
							data: {class: 'Agent_request', status: '-1', id: id},
							success: function(data) {
								location.reload();
								btncontext.attr('disabled',false);
								btncontext.html(oldbtnval);
								$('.loading').hide();
							},
							error: function(){
								location.reload();
								btncontext.attr('disabled',false);
								btncontext.html(oldbtnval);
								$('.loading').hide();
							}
						});
					} else {
						btncontext.attr('disabled',false);
						btncontext.html(oldbtnval);
					}
				});

			});

				$("#approveorder").click(function() {
					var btncontext=$(this);
					var oldbtnval = btncontext.html();
					btncontext.attr('disabled',true);
					btncontext.html('Loading...');
					var id = $(this).attr('data-order_id');
					alertify.confirm('Are you sure you want to approve this request?',function(e){
						if(e) {

							$('.loading').show();
							$.ajax({
								url: '../ajax/ajax_changestatus.php',
								type: 'post',
								data: {class: 'Agent_request', status: '6', id: id},
								success: function(data) {
									location.reload();
									btncontext.attr('disabled',false);
									btncontext.html(oldbtnval);
								},
								error: function(data){
									location.reload();
									btncontext.attr('disabled',false);
									btncontext.html(oldbtnval);
								}
							});
						}else {
							btncontext.attr('disabled',false);
							btncontext.html(oldbtnval);
						}
					});

			});
			$("#liquidateRequest").click(function() {
				var id = $(this).attr('data-order_id');
				location.href = "caravan_liquidation.php?id=" + id;
			});
			$("#liquidateRequestVerify").click(function() {
				var id = $(this).attr('data-order_id');
				location.href = "caravan_liquidation.php?id=" + id;
			});
			$('.rdamageqty , .rmissingqty').keyup(function(){
					var row = $(this).parents('tr');
					var goodqty = row.children().eq(2).text();
					var damageqty = row.children().eq(3).find('input').val();
					var missingqty = row.children().eq(4).find('input').val();
					if(!damageqty || damageqty == '' || damageqty == undefined){
						damageqty = 0;
					}
					if(!missingqty || missingqty == '' || missingqty == undefined){
						missingqty = 0;
					}
					if (parseInt(goodqty) < (parseInt(missingqty) + parseInt(damageqty))){
						alertify.alert('Invalid quantity');
						$(this).val('');
					}

			});
			$("#verify").click(function() {

					var btncontext = $(this);
					var oldbtnval = btncontext.html();
					btncontext.attr('disabled',true);
					btncontext.html('Loading...');
					var req_id = $(this).attr('data-request_id');
					alertify.confirm('Are you sure you want to approve this request?',function(e){
						if(e){
							$('.loading').show();
							var toret = new Array();
							if ($('#tblToReturn').length > 0){
								$('#tblToReturn > tbody > tr').each(function(index){
									var row = $(this);
									var item_id = row.attr('data-id');
									var goodqty = row.children().eq(2).text();
									var damageqty = row.children().eq(3).find('input').val();
									var missingqty = row.children().eq(4).find('input').val();
									var racking = row.children().eq(5).find('select').val();
									if(!damageqty || damageqty == '' || damageqty == undefined){
										damageqty = 0;
									}
									if(!missingqty || missingqty == '' || missingqty == undefined){
										missingqty = 0;
									}
									toret[index] = {
										goodqty : goodqty,
										damageqty : damageqty,
										missingqty : missingqty,
										item_id : item_id,
										racking:racking
									}
								});

							}
							toret = JSON.stringify(toret);


							$.ajax({
								url: '../ajax/ajax_get_requestDetails.php',
								type: 'post',
								data: {functionName: 'verifyRequest', id: req_id,toret:toret},
								success: function(data) {
									alertify.alert(data,function(){
										location.reload();
										btncontext.attr('disabled',false);
										btncontext.html(oldbtnval);
									});
								},
								error: function(){
									btncontext.attr('disabled',false);
									btncontext.html(oldbtnval);
								}
							});
						} else {
							btncontext.attr('disabled',false);
							btncontext.html(oldbtnval);
						}
					});

			});
			$("#verifySale").click(function() {
				var btncontext = $(this);
				var oldbtnval = btncontext.html();

				btncontext.attr('disabled',true);
				btncontext.html('Loading...');

				if(confirm("Are you sure you want to continue this action?")) {
					var req_id = $(this).attr('data-request_id');
					$('.loading').show();
					$.ajax({
						url: '../ajax/ajax_get_requestDetails.php',
						type: 'post',
						data: {functionName: 'verifySale', id: req_id},
						success: function(data) {
							alertify.alert(data,function(){
								location.reload();
								btncontext.attr('disabled',false);
								btncontext.html(oldbtnval);
							});
						},
						error: function(){
							btncontext.attr('disabled',false);
							btncontext.html(oldbtnval);
						}
					});
				}else {
					btncontext.attr('disabled',false);
					btncontext.html(oldbtnval);
				}
			});

		</script>
	<?php
	}

	function processRequest() {
		$user = new User();
		$req_id = Input::get('id');
		$arrjson = json_decode(Input::get('arrjson'));
		$myReq = new Agent_request($req_id);
		$rack = new Rack();
		$rack_id = $rack->getRackForSelling($user->data()->branch_id);

		if($myReq->data()->status == 6){ // counter check if 6 padin , baka naprocess n ng iba
			$valid = true;
			foreach($arrjson as $d) {

				$inventory = new Inventory();
				if(!$user->hasPermission('rack_display') && $rack_id->id ==   $d->rack_id) $valid = false;
				if(!$user->hasPermission('rack_other') && $rack_id->id !=   $d->rack_id) $valid = false;
				$curinventory = $inventory->getQty($d->item_id, $myReq->data()->branch_id, $d->rack_id);

				if($curinventory) {
					// check inventory
					if($d->qty > $curinventory->qty) {
						$valid = false;
						break;
					}
				}else{
					$valid = false;
					break;
				}

			}
			if($valid == true) {
				$details_req = new Agent_request_details();
				foreach($arrjson as  $d) {
					$inventory = new Inventory();
					$details_req->updateRack($req_id,$d->item_id,$d->rack_id);
					$inv_mon = new Inventory_monitoring();
					$curinventoryDis = $inventory->getQty($d->item_id,$myReq->data()->branch_id, $d->rack_id);
					$newqtyDis = $curinventoryDis->qty - $d->qty;
					$inventory->subtractInventory($d->item_id, $myReq->data()->branch_id, $d->qty, $d->rack_id);
					$inv_mon->create(array(
						'item_id' => $d->item_id,
						'rack_id' =>  $d->rack_id,
						'branch_id' => $myReq->data()->branch_id,
						'page' => 'admin/manage_caravan.php',
						'action' => 'Update',
						'prev_qty' => $curinventoryDis->qty,
						'qty_di' => 2,
						'qty' => $d->qty,
						'new_qty' => $newqtyDis,
						'created' => time(),
						'user_id' => $user->data()->id,
						'remarks' => 'Deduct inventory caravan (Caravan id #'.$req_id.')',
						'is_active' => 1,
						'company_id' => $user->data()->company_id
					));

				}
				$updatereq = new Agent_request();
				$updatereq->update(array('status' => 2), $req_id);

				$req_mon = new Request_monitoring();
				$req_mon->create(array(
					'agent_request_id' => $req_id,
					'status' => 2,
					'user_id' =>$user->data()->id,
					'date_approved' => strtotime(date('Y/m/d H:i:s')),
					'is_active' => 1,
					'company_id' => $user->data()->company_id,
					'remarks' => 'Released By'
				));
				echo "Request has been processed successfully";
			} else {
				echo "Unable to process due to insufficient stock or lack of authentication for that rack.";
			}
		} else {
			echo "Someone processed it already. Check time log of this request.";
		}
	}

	function verifyRequest() {
		$req_id = Input::get('id');
		$toret = json_decode(Input::get('toret'));
		$inv_issues = new Inventory_issue();
		$inv_issues_mon = new Inventory_issues_monitoring();

		$myReq = new Agent_request($req_id);
		if($myReq->data()->status == 3) { // counter check if already processed by someone else
			$inv = new Inventory();
			$user = new User();
			foreach($toret as $l) {
				$goodqty = $l->goodqty;
				$damageqty = $l->damageqty;
				$missingqty = $l->missingqty;
				$totalissues = $damageqty + $missingqty;
				$left = $goodqty - $totalissues;
				if($totalissues > 0) {
					$unliq = new Unliquidated();
					$now = time();
					$rack_id = $l->racking;

					if($damageqty){
						$unliq->create(array('item_id' => $l->item_id, 'qty' => $damageqty, 'request_id' => $req_id, 'created' => $now, 'modified' => $now, 'status' => 1, 'is_active' => 1,'issues_type'=>1));
						// status 1 = damage
						$curinvissues = $inv_issues->getQty($l->item_id,$myReq->data()->branch_id,$rack_id,1);
						if(isset($curinvissues->qty)){
							$cur_issues = $curinvissues->qty;
						} else {
							$cur_issues = 0;
						}
						if($inv_issues->checkIfItemExist($l->item_id,$myReq->data()->branch_id,$user->data()->company_id,$rack_id,1)){

							$inv_issues->addInventory($l->item_id,$myReq->data()->branch_id,$damageqty,false,$rack_id,1);
						} else {
							$inv_issues->addInventory($l->item_id,$myReq->data()->branch_id,$damageqty,true,$rack_id,1);
						}
						$new_issues = $cur_issues + $damageqty;
						$inv_issues_mon->create(array(
							'item_id' => $l->item_id,
							'rack_id' => $rack_id,
							'branch_id' => $myReq->data()->branch_id,
							'page' => 'admin/inventory_adjustments.php',
							'action' => 'Update',
							'prev_qty' => $cur_issues,
							'qty_di' => 1,
							'qty' => $damageqty,
							'new_qty' => $new_issues,
							'created' => time(),
							'user_id' => $user->data()->id,
							'remarks' => 'Add issues item from caravan',
							'is_active' => 1,
							'company_id' => $user->data()->company_id,
							'type' => 1
						));
					}
					if($missingqty){
						$unliq->create(array('item_id' => $l->item_id, 'qty' =>$missingqty, 'request_id' => $req_id, 'created' => $now, 'modified' => $now, 'status' => 1, 'is_active' => 1,'issues_type'=>2));
						// status 2 = missing
						 $curinvissues = $inv_issues->getQty($l->item_id,$myReq->data()->branch_id,$rack_id,2);
						if(isset($curinvissues->qty)){
							$cur_issues = $curinvissues->qty;
						} else {
							$cur_issues = 0;
						}
						if($inv_issues->checkIfItemExist($l->item_id,$myReq->data()->branch_id,$user->data()->company_id,$rack_id,2)){
							$inv_issues->addInventory($l->item_id,$myReq->data()->branch_id,$missingqty,false,$rack_id,2);
						} else {
							$inv_issues->addInventory($l->item_id,$myReq->data()->branch_id,$missingqty,true,$rack_id,2);
						}
						$new_issues = $cur_issues + $missingqty;
						$inv_issues_mon->create(array(
							'item_id' => $l->item_id,
							'rack_id' => $rack_id,
							'branch_id' => $myReq->data()->branch_id,
							'page' => 'admin/inventory_adjustments.php',
							'action' => 'Update',
							'prev_qty' => $cur_issues,
							'qty_di' => 1,
							'qty' => $missingqty,
							'new_qty' => $new_issues,
							'created' => time(),
							'user_id' => $user->data()->id,
							'remarks' => 'Add missing item from caravan',
							'is_active' => 1,
							'company_id' => $user->data()->company_id,
							'type' => 2
						));

					}
				}
				if($left > 0) {
					$rack = new Rack();
					$rack_id = $l->racking;
					if($inv->checkIfItemExist($l->item_id, $myReq->data()->branch_id, $user->data()->company_id, $rack_id)) {
						//	echo "UPDATE";

						$inv_mon = new Inventory_monitoring();
						$curinventoryDis = $inv->getQty($l->item_id,$myReq->data()->branch_id, $rack_id);
						$currentInvCheck = 0;
						if($curinventoryDis){
							$currentInvCheck = $curinventoryDis->qty;
						}
						$newqtyDis = $currentInvCheck + $left;
						$inv->addInventory($l->item_id, $myReq->data()->branch_id, $left, false, $rack_id);
						$inv_mon->create(array(
							'item_id' => $l->item_id,
							'rack_id' => $rack_id,
							'branch_id' => $myReq->data()->branch_id,
							'page' => 'admin/manage_caravan.php',
							'action' => 'Update',
							'prev_qty' => $currentInvCheck,
							'qty_di' => 1,
							'qty' =>$left,
							'new_qty' => $newqtyDis,
							'created' => time(),
							'user_id' => $user->data()->id,
							'remarks' => 'Add return inventory caravan (Caravan id #'.$req_id.')',
							'is_active' => 1,
							'company_id' => $user->data()->company_id
						));

					} else {
						//	echo "INSERT";

						$inv_mon = new Inventory_monitoring();
						$curinventoryDis = $inv->getQty($l->item_id,$myReq->data()->branch_id, $rack_id);
						$currentInvCheck = 0;
						if($curinventoryDis){
							$currentInvCheck = $curinventoryDis->qty;
						}
						$newqtyDis = $currentInvCheck + $left;
						$inv->addInventory($l->item_id, $myReq->data()->branch_id, $left, true, $rack_id);
						$inv_mon->create(array(
							'item_id' => $l->item_id,
							'rack_id' => $rack_id,
							'branch_id' => $myReq->data()->branch_id,
							'page' => 'admin/manage_caravan.php',
							'action' => 'Update',
							'prev_qty' => $currentInvCheck,
							'qty_di' => 1,
							'qty' =>$left,
							'new_qty' => $newqtyDis,
							'created' => time(),
							'user_id' => $user->data()->id,
							'remarks' => 'Add return inventory caravan (Caravan id #'.$req_id.')',
							'is_active' => 1,
							'company_id' => $user->data()->company_id
						));

					}
				}
			}

			$updatereq = new Agent_request();
			$updatereq->update(array('status' => 4), $req_id);
			$req_mon = new Request_monitoring();
			$req_mon->create(array('agent_request_id' => $req_id, 'status' => 4, 'user_id' => $user->data()->id, 'date_approved' => strtotime(date('Y/m/d H:i:s')), 'is_active' => 1, 'company_id' => $user->data()->company_id, 'remarks' => 'Received Item'));
			echo "Liquidation has been verified successfully.";
		} else {
			echo "This is already processed by someone else. Please refresh your page and check the timelog";
		}
	}

	function verifySale() {
		$user = new User();
		$req_id = Input::get('id');
		$updatereq = new Agent_request($req_id);
		if($updatereq->data()->status == 5){ // counter check if already processed by someone else
			$updatereq->update(array('status' => 3), $req_id);
			$req_mon = new Request_monitoring();
			$req_mon->create(array(
				'agent_request_id' =>$req_id,
				'status' => 3,
				'user_id' =>$user->data()->id,
				'date_approved' => strtotime(date('Y/m/d H:i:s')),
				'is_active' => 1,
				'company_id' => $user->data()->company_id,
				'remarks' => 'Received Sale'
			));
			echo "Liquidation has been verified successfully.";
		} else {
			echo "This is already processed by someone else. Please refresh your page and check the timelog";
		}

	}

	function getTimelog(){
			$req_id = Input::get('id');
			$req_monitoring = new Request_monitoring();
			$monitorings =  $req_monitoring->get_active('request_monitoring', array('agent_request_id','=',$req_id));
		
				if($monitorings){
					
			?>
			<h3>Time Log</h3>
			<div id="no-more-tables">
			<table class='table'>
					<thead>
			<tr><th>Date</th><th>Status</th><th>User</th></tr>
					</thead>
					<tbody>
			<?php 
					foreach ($monitorings as $value) {
						$whoapp = new User($value->user_id);
						?>
					<tr><td data-title='Date' class='text-danger'><?php echo date('m/d/Y H:i:s A',$value->date_approved); ?></td><td data-title='Remarks'><?php echo $value->remarks ?></td><td data-title='User'><?php echo ucwords($whoapp->data()->lastname . ", " . $whoapp->data()->firstname . " " . $whoapp->data()->middlename); ?></td></tr>
					<?php 
					}
				} else {
					echo "No monitoring";
				}
			?>
		</tbody>
			</table>
		</div>
			<?php
	}
?>