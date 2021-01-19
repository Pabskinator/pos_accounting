<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('inventory')) {
		// redirect to denied page
		Redirect::to(1);
	}
	$ti = new Transfer_inventory_mon();
	$branch_id = 0;
	if(isset($_POST['btnSubmit'])){
		$branch_id = Input::get('branch_id');
		if(!$branch_id)  $branch_id = 0;

	}
	$pending_transfer = $ti->getStatusOne($user->data()->company_id,$branch_id);
	$pending_transfer_from = $ti->getStatusOneFrom($user->data()->company_id,$user->data()->branch_id);

	$barcodeClass = new Barcode();
	$barcode_format = $barcodeClass->getFormat($user->data()->company_id,"ORDER");

	$order_styles =  $barcode_format->styling;

	$branch_cls = new Branch();
	$branch_list = $branch_cls->get_active('branches',[1,'=',1]);



?>


	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Receive Inventory </h1>
			</div>
			<?php
				// get flash message if add or edited successfully
				if(Session::exists('flash')) {
					echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
				}
			?>
			<div class="btn-group hidden-xs" role="group" aria-label="..." style='margin-bottom: 5px;'>
				<button type="button" class="btn btn-default" id='navIn' title='Transfer In'><i class='fa fa-arrow-down'></i> <span class='hidden-xs'>Transfer In</span></button>
				<button type="button" class="btn btn-default" id='navOut' title='Transfer Out'><i class='fa fa-arrow-up'></i> <span class='hidden-xs'>Transfer Out</span></button>
				<button type="button" class="btn btn-default" id='navTransfered' title='Transferred'><i class='fa fa-list-alt'></i> <span class='hidden-xs'>Transferred</span></button>
				<button type="button" class="btn btn-default" id='navPending' title='Pending'><i class='fa fa-dashboard'></i> <span class='hidden-xs'>Pending for review</span></button>
			</div>
			<div class='visible-xs'>
				<button id='btnShowNavigationContainer' class='btn btn-default'><i class='fa fa-bars'></i></button>
				<div class='card-nav card-nav-2' id='secondNavigationContainer' style='display:none;'>
					<button class='btn btn-default btn-sm' id='btnRemoveSecondNavigationContainer'><i class='fa fa-remove'></i></button>

					<button type="button" class="btn btn-default btn-second-nav" id='navIn2' title='Transfer In'><i class='fa fa-arrow-down'></i> <span class='title'>Transfer In</span></button>
					<button type="button" class="btn btn-default btn-second-nav" id='navOut2' title='Transfer Out'><i class='fa fa-arrow-up'></i> <span class='title'>Transfer Out</span></button>
					<button type="button" class="btn btn-default btn-second-nav" id='navTransfered2' title='Transferred'><i class='fa fa-list-alt'></i> <span class='title'>Transferred</span></button>
					<button type="button" class="btn btn-default btn-second-nav" id='navPending2' title='Pending'><i class='fa fa-dashboard'></i> <span class='title'>Pending for review</span></button>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">

							<div class="panel panel-primary">
								<!-- Default panel contents -->
								<div class="panel-heading"></div>
								<div class="panel-body">

									<div id="tobranch">
										<form action="" method="POST">
										<div class="row">
											<div class="col-md-3">
												<strong>Search Branch</strong>
												<select class='form-control' name="branch_id" id="branch_id">
													<option value="">All</option>
													<?php if($branch_list){
														foreach($branch_list as $bl){
															echo "<option value='$bl->id'>$bl->name</option>";
														}
													}?>
												</select>
											</div>
											<div class="col-md-3">
												<br>
												<input type="submit" class='btn btn-default'value='Submit' name='btnSubmit'>
											</div>
										</div>
										</form>
										<br>
										<?php
											if($pending_transfer) {
										?>
										<div id="no-more-tables">
											<table class='table' id='tblTransferTo'>
												<thead>
												<tr>
													<TH>Id</TH>
													<TH>Branch</TH>
													<TH>Data Created</TH>
													<th>Remarks</th>
													<th>Details</th>
												</tr>
												</thead>
												<tbody>
												<?php

													foreach($pending_transfer as $pt){
															if(!$user->hasPermission('inventory_all')){
																if($pt->branch_id != $user->data()->branch_id){
																	continue;
																}
															}
														?>
														<tr>
															<td data-title='Id'><?php echo escape($pt->id); ?></td>
															<td data-title='Branch'><?php echo escape($pt->name); ?></td>
															<td data-title='Created'>
																<?php echo escape(date('m/d/Y H:i:s A',$pt->created)); ?>

															</td>
															<td data-title='Remarks'>
																<?php
																	echo escape($pt->from_where);
																	$is_back = "0";
																	if($pt->from_where == "From Order"){
																		if($pt->branch_from){
																			echo "<br><small class='text-danger'>Branch: ". escape($pt->name2)."</small>";
																		}
																		if($pt->supplier_id){
																			$pt->from_where = "From Supplier";
																			echo "<br><small class='text-danger'>Supplier: ". escape($pt->supname)."</small>";
																		}
																		if($pt->del_schedule){
																			echo "<br><small class='text-danger'>Schedule: ". escape(date('m/d/Y',$pt->del_schedule))."</small>";
																		}
																		if($pt->wh_id){
																			echo "<br><small class='text-danger'>Order ID: ". escape($pt->wh_id)."</small>";
																		}
																	} else if($pt->from_where == "From backload"){
																		$is_back = "1";
																		echo "<br><small class='text-danger'>Invoice: $pt->invoice DR: $pt->dr PR: $pt->pr</small>";
																	} else if ($pt->from_where == 'From Service Liquidation' || $pt->from_where == 'From service return item' ){
																		if($pt->payment_id){
																			echo "<small class='span-block text-danger'>Service ID: $pt->payment_id</small>";
																		}

																	}
																?>
																<?php
																	if($pt->remarks){
																		echo "<strong class='span span-block'>".$pt->remarks."</strong>";
																	}
																?>
															</td>
															<td>
																<button data-from='<?php echo $pt->from_where; ?>' data-transfer_id='<?php echo escape($pt->id); ?>' class='btn btn-default btnDetails' title='Details'><span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>Details</span></button>
																<?php if($pt->from_where == "From Order" || $pt->from_where == "From transfer" || $pt->from_where == "From backload"):?>
																<button style='margin-left:3px'  data-is_backload='<?php echo $is_back; ?>' data-transfer_id='<?php echo escape($pt->id); ?>' class='btn btn-default btnPrint' title='Print'><span class='glyphicon glyphicon-print'></span> <span class='hidden-xs'>Print</span></button>
																<?php endif; ?>
															</td>
														</tr>
														<?php
													}
												?>
												</tbody>
											</table>
										</div>
											<?php
												} else {
												?>
												<div class="alert alert-info">No Record found.</div>
												<?php
											}
											?>

									</div>
									<div id="frombranch" style='display:none;'>
										<?php
											if($pending_transfer_from) {
												?>
												<div class="no-more-tables">
												<table id='tblTransferFrom' class='table' >
													<thead>
													<tr>
														<TH>Id</TH>
														<TH>Branch</TH>
														<TH>Data Created</TH>
														<th>Remarks</th>
														<th>Details</th>
													</tr>
													</thead>
													<tbody>
													<?php
														foreach($pending_transfer_from as $pt){
															?>
															<tr>
																<td data-title='Id'><?php echo escape($pt->id); ?></td>
																<td data-title='Branch'>
																	<?php echo escape($pt->name); ?> <br>
																	<div>Driver: <small class='text-danger span-block'><?php echo escape(($pt->driver) ? $pt->driver : 'Not set' )?></small></div>
																	<div>Helper:
																		<?php
																			if($pt->helpers){
																				if(strpos($pt->helpers,"|") > 0){
																					$exploded = explode('|',$pt->helpers);
																					foreach($exploded as $ex){
																						echo "<small class='span-block text-danger'>$ex</small>";
																					}
																				} else {
																					echo "<small class='span-block text-danger'>$pt->helpers</small>";
																				}
																			} else {
																				echo "<small class='span-block text-danger'>Not Set</small>";
																			}
																		?>
																	</div>
																</td>
																<td data-title='Created'>
																	<?php echo escape(date('m/d/Y H:i:s A',$pt->created)); ?>
																</td>
																<td data-title='Remarks'>
																	<?php
																		echo escape($pt->from_where);
																		$is_back = "0";
																		if($pt->from_where == "From Order"){
																			if($pt->branch_from){
																				echo "<br><small class='text-danger'>Branch: ". escape($pt->name2)."</small>";
																			}
																			if($pt->supplier_id){
																				echo "<br><small class='text-danger'>Supplier: ". escape($pt->supname)."</small>";
																			}
																			if($pt->del_schedule){
																				echo "<br><small class='text-danger'>Schedule: ". escape(date('m/d/Y',$pt->del_schedule))."</small>";
																			}
																		}else if($pt->from_where == "From backload"){
																			$is_back = "1";
																			echo "<br><small class='text-danger'>Invoice: $pt->invoice DR: $pt->dr PR: $pt->pr</small>";
																		}
																	?>
																	<?php
																		if($pt->remarks){
																			echo "<span class='span span-block'>".$pt->remarks."</span>";
																		}
																	?>
																</td>
																<td>
																	<button  data-transfer_id='<?php echo escape($pt->id); ?>' class='btn btn-default btnDetails' title='Details'><span class="glyphicon glyphicon-details"></span> <span class='hidden-xs'>Details</span></button>
																	<?php if($pt->from_where == "From Order" || $pt->from_where == "From transfer"  || $pt->from_where == "From backload"):?>
																	<button style='margin-left:3px' data-is_backload='<?php echo $is_back; ?>' data-transfer_id='<?php echo escape($pt->id); ?>' class='btn btn-default btnPrint' title='Print'><span class="glyphicon glyphicon-print"></span> Print</button>
																<?php endif; ?>
																</td>
															</tr>
														<?php
														}
													?>
													</tbody>
												</table>
												</div>
											<?php
											} else {
												?>
												<div class="alert alert-info">No Record found.</div>
											<?php
											}
										?>
									</div>
									<div id="transferred" style='display:none;'>
										<div class="row">
											<div class="col-md-3">
												<select name="status" id="status" class='form-control'>
													<option value="2">Transferred</option>
													<option value="3">Cancelled</option>
												</select>
											</div>
										</div>
										<div class="row">
											<div class="col-md-12">
												<input type="hidden" id="hiddenpage" />
												<div id="holder"></div>
											</div>
										</div>
									</div>
									<div id="con_pending" style='display:none;'>

										<div id="pendingController">
											<div v-show='!pending_list.length' class='alert alert-info'>No record found.</div>
											<div v-show='pending_list.length'  class="panel panel-default">
												<div class="panel-body">
													<h4>{{title}}</h4>
													<p>
														<span class='text-muted span-block'>* Change quantity of item.</span>
														<span class='text-muted span-block'>* Remove wrong entry.</span>
														<span class='text-muted span-block'>* <a href='transfer.php'>Add</a> item that is not on the list.</span>
													</p>
													<div id="no-more-tables">
													<table class='table' >
														<thead>
														<tr>
															<th>Items</th>
															<th>From</th>
															<th>To</th>
															<th>Qty</th>
															<th></th>
														</tr>
														</thead>
														<tbody>
														<tr  v-for="item in pending_list">
															<td data-title='Item'>{{item.item_code}} <small class='text-danger span-block'>{{item.item_description}}</td>
															<td data-title='From'>{{item.rack_name_from}}</td>
															<td data-title='To'>{{item.rack_name_to}}</td>
															<td data-title='Qty'><input type="text" class='form-control' v-model="item.qty" value="{{item.qty}}"></td>
															<td><button class='btn btn-danger btn-sm' @click="removeItem($index)"><i class='fa fa-remove'></i></button></td>
														</tr>
														</tbody>
													</table>
													</div>

													<div class="text-right">
														<button id='saveTrans' class='btn btn-default' @click="saveTrans">SAVE</button>
													</div>
												</div>
											</div>
										</div>


									</div>
								</div>
							</div>

				</div>

			</div>
		</div>

	</div> <!-- end page content wrapper-->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style='width:95%;' >
			<div class="modal-content"  >
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id='mtitle'></h4>
				</div>
				<div class="modal-body" id='mbody'>

				</div>

			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<div class="loading" style='display:none;'>Loading&#8230;</div>
	<script src="../js/vue.js"></script>
	<script>
		$(document).ready(function() {
			var vm = new Vue({
				el : "#pendingController",
				data: {
					title:'Pending transfer for review',pending:[]
				},
				ready: function(){
					if(localStorage['trans_inventory_local']){
						this.pending = JSON.parse(localStorage['trans_inventory_local']);
					} else {
						this.pending = [];
					}
				},
				computed:{
					pending_list: function(){
						return this.pending;
					}
				},
				methods:{
					saveTrans: function(){
						var con = $('#saveTrans');
						button_action.start_loading(con);
						var pending = this.pending;
						var vuecon = this;
						$.ajax({
						    url:'../ajax/ajax_wh_order.php',
						    type:'POST',
						    data: {functionName:'saveTrans',pending:JSON.stringify(pending)},
						    success: function(data){
						        alertify.alert(data);
							    vuecon.pending=[];
							    button_action.end_loading(con);
							    localStorage.removeItem('trans_inventory_local');
						    },
						    error:function(){
							    button_action.end_loading(con);
						    }
						});
					},
					removeItem: function(i){
						this.pending.splice(i,1);
						localStorage['trans_inventory_local'] = JSON.stringify(this.pending);
					}
				}
			});
			$('body').on('click','#btnSaveRef',function(){
				var ref_id = $('#ref_number').val();
				var id = $(this).attr('data-id');
				$.ajax({
				    url:'../ajax/ajax_transfer.php',
				    type:'POST',
				    data: {functionName:'updateRemarks',id:id,ref:ref_id},
				    success: function(data){
				        alertify.alert(data);
				    },
				    error:function(){
				        
				    }
				});
			});
			var order_style = '<?php echo $order_styles; ?>';
			if(localStorage['trans_nav'] && localStorage['trans_nav']==4){
				$('#tobranch').hide();
				$('#frombranch').hide();
				$('#transferred').hide();
				$('#con_pending').fadeIn();
				localStorage.removeItem('trans_nav');
			}
			$('#navIn,#navIn2').click(function(){
				$('#frombranch').hide();
				$('#transferred').hide();
				$('#tobranch').fadeIn();
				$('#con_pending').hide();
				$('#secondNavigationContainer').hide();
			});
			$('#navOut,#navOut2').click(function(){
				$('#tobranch').hide();
				$('#transferred').hide();
				$('#frombranch').fadeIn();
				$('#con_pending').hide();
				$('#secondNavigationContainer').hide();

			});
			$('#navTransfered,#navTransfered2').click(function(){
				$('#tobranch').hide();
				$('#frombranch').hide();
				$('#transferred').fadeIn();
				$('#con_pending').hide();
				$('#secondNavigationContainer').hide();
			});
			$('#navPending,#navPending2').click(function(){
				$('#tobranch').hide();
				$('#frombranch').hide();
				$('#transferred').hide();
				$('#con_pending').fadeIn();
				$('#secondNavigationContainer').hide();
			});
			$('body').on('click','.print_rack_transfer',function(){
				var tid = $(this).attr('data-transfer_id');
				var from = $(this).attr('data-from');
				var branch_name = $(this).attr('data-branch_name');
				var remarks = $(this).attr('data-remarks');
				remarks = (remarks) ? remarks :'None';
					var date_obj = new Date();
					var  curDate = date_obj.getMonth() + "/" + date_obj.getDay() + "/" + date_obj.getFullYear();
					var page = "<div class='perpage' style='page-break-after:always;' >";
					page += "<h1 class='text-center'>"+localStorage['company_name']+"</h1>";
					page += "<p class='text-center text-muted'></p>";
					page += "<p class='text-right'>Transfer Id# <span style='width:80px;display:inline-block;margin-left:5px;' class='text-left'>" +tid+"</span></p>";
					page += "<div class=''>";
					page += "<div class='pull-right'>";
					page += "<p>Date: <span style='width:270px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:13px;'>" + curDate + "</span></p>";
					page += "</div>";
					page += "<p>Branch: <span style='width:270px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:13px;'>   "+branch_name+"</span></p>";
					page += "</div>";
					page += "<div>";
					page += "<p>Remarks: <span style='width:270px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:13px;'>" + remarks + "</span></p>";
					page += "</div>";

					page += "<div style='clear:both;'></div>";
					page += "<table class='table table-bordered' style='font-size:10px;'>";
					page += "<tr><th>Item</th><th>Qty</th><th>From</th><th>To</th></tr>";

					var pageitem = [];
					var ctr = 1;
					var strholder = '';
					var arrStockman = [];
					var by_page = 15;
					$('#tblTransfer tbody tr').each(function(){

						var row = $(this);
						var item_code = row.children().eq(0).text();
						var from  = row.children().eq(1).text();
						var to  = row.children().eq(2).find('select option:selected').text();
						var qty  =row.children().eq(3).text();
						strholder += "<tr style='min-height:25px;'><td style='width:250px;'>" +item_code+ "</small></td><td>"+qty+"</td><td>"+from+"</td><td>"+to+"</td>";
						strholder += "</tr>";
						if(ctr % by_page == 0) {
							pageitem.push(strholder);
							strholder = '';
						}
						ctr += 1;

					});
					var num = Math.ceil((ctr / by_page)) * by_page;


					if(parseFloat(ctr) < parseFloat(by_page)) {

						while(ctr != num + 1) {
							console.log(ctr + " = " + num);
							strholder += "<tr style='height:25px;'><td></td><td></td><td></td><td></td></tr>";
							ctr += 1;
						}
						pageitem.push(strholder);
						strholder = '';

					} else {
						while(ctr != num + 1) {
							console.log(ctr + " == " + num);
							strholder += "<tr style='height:25px;'><td></td><td></td><td></td><td></td></tr>";
							ctr += 1;
						}
						pageitem.push(strholder);
						strholder = '';
					}

					var endtable = '</table>';
					var pageend = "";
					pageend += "<br><p>Processed By: <span style='width:300px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:5px;'></span></p>";
					pageend += "<br><p>Received By: <span style='width:300px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:5px;'></span></p>";
					//	pageend += "<p>Received By: <span style='width:300px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:13px;'></span></p>";
					pageend += "</div>";
					var countpages = pageitem.length;
					var pageof = 1;
					var finalhtml = "";
					for(var j in pageitem) {
						finalhtml += page;
						finalhtml += pageitem[j];
						finalhtml += endtable;
						finalhtml +=  "<p class='text-center' style='color:#ccc;font-size:0.8em;'>Page "+pageof+" of "+countpages+"</p>";
						pageof += 1;
						finalhtml += pageend;
					}
					console.log(finalhtml);

					popUpPrintWithStyle(finalhtml);
			});
			$('body').on('click','.btnDetails',function(){
				var tid = $(this).attr('data-transfer_id');
				var from = $(this).attr('data-from');
				$('.loading').show();
				$.ajax({
					url:'../ajax/ajax_query.php',
					type:'post',
					data: {functionName:'getTransferDetails',id:tid,from:from},
					success: function(data){
						$('#myModal').modal('show');
						$('#mbody').html(data);
						$('.torack').select2({
							allowClear:true,
							placeholder:'Choose Rack'
						});
						if($('#init_sched_el').val() == 1){
							$('#truck_id').select2({
								allowClear:true,
								placeholder:'Choose truck'
							});
							$('#s_driver').select2({
								allowClear:true,
								placeholder:'Choose Driver'
							});
							$('#s_helper').select2({
								allowClear:true,
								placeholder:'Choose Helper'
							});

							$('#dt_sched').datepicker({
								autoclose:true
							}).on('changeDate', function(ev){
								$('#dt_sched').datepicker('hide');
							});
						}
						$('.loading').hide();
					},
					error:function(){

						$('.loading').hide();
					}
				});
			});
			$('body').on('click','#btnGetStocks',function(){
				var btncon = $(this);
				var btnoldval = btncon.html();
				btncon.attr('disabled',true);
				btncon.html('Loading...');
				var id = btncon.attr('data-id');

				$.ajax({
				    url:'../ajax/ajax_query.php',
				    type:'POST',
				    data: {functionName:'deductStockFromTransfer',id:id},
				    success: function(data){
						alertify.alert(data,function(){
							location.reload();
						});

				    },
				    error:function(){

				    }
				});
			});
			$('body').on('click','#btnAddSchedule',function(){
				var btncon = $(this);
				var btnoldval = btncon.html();
				//btncon.attr('disabled',true);
				//btncon.html('Loading...');
				var id = btncon.attr('data-id');
				var dt_sched = $('#dt_sched').val();
				var driver = $('#s_driver').val();
				var helper = $('#s_helper').val();
				var truck_id = $('#truck_id').val();
				helper = (helper) ? JSON.stringify(helper) : '[]';

				if(!dt_sched){
					alertify.alert("Date schedule is required");
					return;
				}
				var id = btncon.attr('data-id');
				$.ajax({
					url:'../ajax/ajax_query.php',
					type:'POST',
					data: {functionName:'updateDelSchedTransfer',id:id,dt_sched:dt_sched,driver:driver,helper:helper,truck_id:truck_id},
					success: function(data){
						alertify.alert(data,function(){
							location.reload();
						});

					},
					error:function(){

						location.reload();
					}
				});
			});
			//btnCancelTransfer

			$('body').on('click','#btnCancelTransfer',function(){
				if($('#tblTransfer').length){
					var btncon= $(this);
					var fromwhat = btncon.attr('data-from');
					btncon.attr('disabled',true);
					btncon.val('Loading..');
					var tid = $('#tblTransfer').attr('data-tid');
					alertify.confirm("Are you sure you want to cancel this request?",function(e){
						if(e){
							$.ajax({
								url:'../ajax/ajax_query.php',
								type:'post',
								data: {functionName:'cancelTransferMon',id:tid},
								success: function(data){
									if(data == '1'){
										alertify.alert('Cancelled Successfully',function(){
											location.reload();
										});
									} else {
										$('.loading').hide();
										alertify.alert(data,function(){
											btncon.attr('disabled',false);
											btncon.val('Cancel');
										});


									}
								},
								error:function(){

									location.reload();
								}
							});
						} else {
							btncon.attr('disabled',false);
							btncon.val('Cancel');
						}
					});

				}
			});
			$('body').on('click','#btnTransfer',function(){
				if($('#tblTransfer').length){
					var btncon= $(this);
					var fromwhat = btncon.attr('data-from');
					btncon.attr('disabled',true);
					btncon.val('Loading..');
					$('.loading').show();
					var tid = $('#tblTransfer').attr('data-tid');
					var arr = [];
					var isValid = true;
					$('#tblTransfer tbody tr').each(function(){
							var row= $(this);
							var rack_from = row.attr('data-from');
							var rack_to = row.children().eq(2).find('select').val();
							var item_id = row.attr('data-item_id');
							var qty = row.attr('data-qty');
							if(!rack_to){
								isValid = false;
							}
							var ob = {
								rack_from : rack_from,
								rack_to : rack_to,
								item_id : item_id,
								qty:qty
							};
							arr.push(ob);
					});
					if(isValid){
						arr= JSON.stringify(arr);
						$.ajax({
							url:'../ajax/ajax_query.php',
							type:'post',
							data: {functionName:'transferMonitoring',id:tid,fromwhat:fromwhat,jsondet :arr},
							success: function(data){
								if(data == '1'){
									alertify.alert('Transfer Complete',function(){
										location.reload();
									});
								} else {
									$('.loading').hide();
									alertify.alert(data,function(){
										btncon.attr('disabled',false);
										btncon.val('Transfer');
									});


								}
							},
							error:function(){

								location.reload();
							}
						});
					} else {
						alertify.alert("Invalid rack. Please choose rack first.");
						btncon.attr('disabled',false);
						btncon.val('Transfer');
					}

				}
			});
			function Popup(data)
			{
				var mywindow = window.open('', 'new div', '');
				mywindow.document.write('<html><head><title></title>');
				mywindow.document.write('<style>table, th, td {border: 1px solid black;} table { border-collapse: collapse; } table, th, td { border: 1px solid black; padding:4px; } th {height: 50px;}</style>');
				/*optional stylesheet*/
				mywindow.document.write('</head><body style="padding:0px;margin:0px;">');
				mywindow.document.write(data);
				mywindow.document.write('</body></html>');

					mywindow.print();
					mywindow.close();
					return true;

			}
			function popUpPrintWithStyle(data) {
				var mywindow = window.open('', 'new div', '');
				mywindow.document.write('<html><head><title></title><style></style>');
				mywindow.document.write('<link rel="stylesheet" href="../css/bootstrap.css" type="text/css" />');
				mywindow.document.write('</head><body style="padding:0;margin:0;;font-family: Arial, Helvetica, sans-serif;">');
				mywindow.document.write(data);
				mywindow.document.write('</body></html>');
				setTimeout(function() {
					mywindow.print();
					mywindow.close();

				}, 1000);

			}
			$('body').on('click','.btnPrint',function(){
				var transfer_id = $(this).attr('data-transfer_id');
				var is_backload = $(this).attr('data-is_backload');

				$.ajax({
				    url:'../ajax/ajax_query2.php',
				    type:'post',
				    data: {transfer_id:transfer_id,functionName:'printOrderInventory',is_backload:is_backload},
				    success: function(data){
					   var data = JSON.parse(data);
					    PrintElemDr(data);
				    },
				    error:function(){

				    }
				});
			});

			function PrintElemDr(data)
			{
				var data_info = data.main;
				var data_details = data.details;
				var branch_from = data_info.branch_from;
				var branch_from_address = data_info.branch_from_address;

				var branch_to  =data_info.branch_to;
				var branch_to_address=data_info.branch_to_address;
				var ref_number = data_info.ref_number;
				var output = data_info.date;
				var drnumctr =  data_info.id;
				var company_name = data_info.company_name;
				var company_address = data_info.company_address;
				var remarks = data_info.wh_remarks;
				remarks = (remarks) ? remarks : 'None';
				if(data_info.is_backload == '1'){
					remarks = "Backload Item";
				}

				var img = "<div class='text-center' style='margin-botton:0px;'><img height='40' width='40' style='' src='"+data_info.logo+"'/></div>";

				var printhtml= img + "<h3 style='margin-top:0px;'  class='text-center'>"+company_name+"<span style='display:block;text-align: center;font-size:12px;'>"+company_address+"</span></h3>";

				printhtml += "<table class='table table-condensed'>";
				printhtml += "<tr><td style='border-top: 0px;'>From:</td><td style='border-top: 0px;'>"+branch_to+"</td><td style='border-top: 0px;'>"+drnumctr+"</td></tr>";
				printhtml += "<tr><td style='border-top: 0px;'>Address:</td><td style='border-top: 0px;'>"+branch_to_address+"</td><td style='border-top: 0px;'>"+output+"</td></tr>";
				printhtml += "<tr><td style='border-top: 0px;'>To:</td><td style='border-top: 0px;'>"+branch_from+"</td><td style='border-top: 0px;'></td></tr>";
				printhtml += "<tr><td style='border-top: 0px;'>Address:</td><td style='border-top: 0px;'>"+branch_from_address+"</td><td style='border-top: 0px;'></td></tr>";
				printhtml += "<tr><td style='border-top: 0px;'>Remarks:</td><td style='border-top: 0px;'>"+remarks+"</td><td style='border-top: 0px;'></td></tr>";
				printhtml += "</table>"

				printhtml= printhtml + "<table id='itemscon' class='table table-bordered'>";
				printhtml= printhtml + "<tr><th>Qty</th><th>Unit</th><th>Item</th></tr>";


				var row = "";
				for(var i in data_details){
					var itemcode = data_details[i].item_code;
					var description = data_details[i].description;
					var qty = data_details[i].qty ;
					row = row + "<tr ><td >"+qty+"</td><td>pc(s)</td><td > "+ description +" <span style='padding-left:20px;'></span> </td></tr>";

				}
				printhtml += row;
				printhtml += "</table>";

				printhtml += "<table class='table'>";
				printhtml += "<tr ><td style='border-top: 0px;'>Approved by:</td><td style='border-top: 0px;'>_____________________________</td></tr>";
				printhtml += "<tr><td style='border-top: 0px;'>Received by:</td><td style='border-top: 0px;'>_____________________________</td></tr>";
				printhtml += "</table>";

				popUpPrintWithStyle(printhtml);



			}
			getPage(0);
			$('body').on('click','.paging',function(){
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});
			$('body').on('change','#status',function(){
				getPage(0);
			});
			function getPage(p){
				var status = $('#status').val();

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{status:status,page:p,functionName:'transferPaginate',cid: <?php echo $user->data()->company_id; ?>},
					success: function(data){
						$('#holder').html(data);
					}
				});
			}

		});
	</script>

<?php require_once '../includes/admin/page_tail2.php'; ?>