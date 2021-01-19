<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('inventory')) {
		// redirect to denied page
		Redirect::to(1);
	}

	$user_permbranch = $user->hasPermission('inventory_all');
?>


	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					<?php echo TRANSFER_LABEL; ?>
				</h1>
			</div>
			<?php
				// get flash message if add or edited successfully
				if(Session::exists('flash')) {
					echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
				}
			?>
			<div class="row">
				<div class="col-md-12">

					<?php


						// if submitted
						if (Input::exists()){
							// check token if match to our token
							if(Token::check(Input::get('token'))){
								$formlenght = Input::get('formlength');
								$errors = array();
								$bid = Input::get('bname');

								if(Input::get('dtcreated')){
									$dtcreated= strtotime(Input::get('dtcreated'));
								} else {
									$dtcreated = time();
								}
								// transfer mon
								$tranfer_mon = new Transfer_inventory_mon();
								$tranfer_mon->create(array(
									'status' => 1,
									'is_active' =>1,
									'branch_id' =>$bid,
									'company_id' =>$user->data()->company_id,
									'created' => $dtcreated,
									'modified' => time(),
									'from_where' => 'From transfer',
									'remarks' => Input::get('remarks')
								));

								$lastid = $tranfer_mon->getInsertedId();

								$is_inserted = false;
								for($i=1;$i<=$formlenght;$i++){
									$itemname = Input::get('barcodeName'.$i);
									$rackname=  Input::get('rackName'.$i);
									$transferqty = Input::get('transferQty'.$i);
									$torack = Input::get('rnameTo'.$i);
									if($itemname && $rackname && $transferqty && $torack){

										$r = explode(',',$rackname);
										$rackid = $r[0];
										$curqty = $r[1];


										$itemid = $itemname;
										$product = new Product($itemid);
										$unit =  new Unit($product->data()->unit_id);
										if($unit->data()->is_decimal == 0 && is_decimal($transferqty) ){
											$errors[] = "<p>" . $product->data()->item_code . ": Quantity should be a whole number</p>";
										}
										$inventory = new Inventory();
										if(!$itemid){
											$errors[] = "Error on item code on this product : ".$product->data()->item_code ."<br/>";
										}
										if(!$rackid){
											$errors[] = "Error on Rack on this product : ". $product->data()->item_code ."<br/>";
										}
										if($transferqty > $curqty){
											$errors[] = "Not enough stock to transfer on this product : ". $product->data()->item_code ."<br/>";
										}
										if(count($errors) == 0) {
											$tranfer_mon_details = new Transfer_inventory_details();
											$tranfer_mon_details->create(array(
													'transfer_inventory_id' => $lastid,
													'rack_id_from' => $rackid,
													'rack_id_to' => $torack,
													'item_id' =>$itemid,
													'qty' => $transferqty,
													'is_active' => 1
											));
											$is_inserted = true;
											// uncomment old codes
											/*
											if($inventory->checkIfItemExist($itemid,$bid,$user->data()->company_id,$torack)){
												$inv_mon = new Inventory_monitoring();
											//	echo "UPDATE";
												$curinventoryDis = $inventory->getQty($itemid,$bid,$torack);
												$inventory->addInventory($itemid,$bid,$transferqty,false,$torack);
												// monitoring

												$newqtyDis = $curinventoryDis->qty + $transferqty;
												$inv_mon->create(array(
													'item_id' => $itemid,
													'rack_id' => $torack,
													'branch_id' => $bid,
													'page' => 'admin/transfer.php',
													'action' => 'Update',
													'prev_qty' => $curinventoryDis->qty,
													'qty_di' => 1,
													'qty' => $transferqty,
													'new_qty' => $newqtyDis,
													'created' => time(),
													'user_id' => $user->data()->id,
													'remarks' => 'Add inventory to rack (transfer)',
													'is_active' => 1,
													'company_id' => $user->data()->company_id
												));

												$curinventoryFrom = $inventory->getQty($itemid,$bid,$rackid);
												$inventory->subtractInventory($itemid,$bid,$transferqty,$rackid);

												// monitoring
												$newqtyFrom = $curinventoryFrom->qty - $transferqty;
												$inv_mon->create(array(
													'item_id' => $itemid,
													'rack_id' => $rackid,
													'branch_id' => $bid,
													'page' => 'admin/transfer.php',
													'action' => 'Update',
													'prev_qty' => $curinventoryFrom->qty,
													'qty_di' => 2,
													'qty' => $transferqty,
													'new_qty' => $newqtyFrom,
													'created' => time(),
													'user_id' => $user->data()->id,
													'remarks' => 'Deduct inventory to rack (transfer)',
													'is_active' => 1,
													'company_id' => $user->data()->company_id
												));
											} else {
													$inv_mon = new Inventory_monitoring();
											//	echo "INSERT";
												$curinventoryDis = 0;
												$inventory->addInventory($itemid,$bid,$transferqty,true,$torack);
												//monitoring
												$newqtyDis = $curinventoryDis + $transferqty;
												$inv_mon->create(array(
													'item_id' => $itemid,
													'rack_id' => $torack,
													'branch_id' => $bid,
													'page' => 'admin/transfer.php',
													'action' => 'Insert',
													'prev_qty' => $curinventoryDis,
													'qty_di' => 1,
													'qty' => $transferqty,
													'new_qty' => $newqtyDis,
													'created' => time(),
													'user_id' => $user->data()->id,
													'remarks' => 'Add inventory to rack (transfer)',
													'is_active' => 1,
													'company_id' => $user->data()->company_id
												));


												$curinventoryFrom = $inventory->getQty($itemid,$bid,$rackid);
												$inventory->subtractInventory($itemid,$bid,$transferqty,$rackid);
												// monitoring
												$newqtyFrom = $curinventoryFrom->qty - $transferqty;
												$inv_mon->create(array(
													'item_id' => $itemid,
													'rack_id' => $rackid,
													'branch_id' => $bid,
													'page' => 'admin/transfer.php',
													'action' => 'Insert',
													'prev_qty' => $curinventoryFrom->qty,
													'qty_di' => 2,
													'qty' => $transferqty,
													'new_qty' => $newqtyFrom,
													'created' => time(),
													'user_id' => $user->data()->id,
													'remarks' => 'Deduct inventory to rack (transfer)',
													'is_active' => 1,
													'company_id' => $user->data()->company_id
												));
											}
											*/
										}
									}
								}

								$e='';
								if(count($errors) > 0 ){
									foreach($errors as $error){
										$e.=$error;
									}
								}

								if(!$is_inserted){
									$tranfer_mon->update(array('is_active'=>0),$lastid);
									Session::flash('flash','Request failed'.$e);
									Redirect::to('transfer.php');
								} else {
									Session::flash('flash','You have successfully updated the inventory '.$e);
									Redirect::to('transfer.php');
								}

							}
							 //
						}
					?>

					<form id='formInventory' class="form-horizontal" action="" method="POST">


						<fieldset>

							<!-- Form Name -->
							<legend></legend>

							<!-- Select Basic -->
							<div class="row">
								<?php
									$is_all = '0';
									$b_name = '';
									$b_id ='';
									if($user->hasPermission('inventory_all')){
										$bb = new Branch($user->data()->branch_id);
										$is_all = 1;
										$b_name =$bb->data()->name;
										$b_id =$bb->data()->id;
										?>
										<label class="col-md-1 control-label" for="bid">Branch</label>
									<div class="col-md-3">
										<input type="text" id='bid' name='bname' class='form-control'>
									</div>
										<?php
									} else {
										?>
										<input type="hidden" value='<?php echo $user->data()->branch_id; ?>' id='bid' name='bname'>
										<?php
									}
								?>

								
								<label class="col-md-1 control-label" for="dtcreated">Date</label>
								<div class="col-md-3">
									<input type="text" class='form-control' id='dtcreated' name='dtcreated' placeholder='Date (optional)'/>
									<span class="help-block">Transfer date</span>
								</div>
								<label class="col-md-1 control-label" for="remarks">Remarks</label>
								<div class="col-md-3">
									<input type="text" class='form-control' id='remarks' name='remarks' placeholder='Remarks (optional)'/>
									<span class="help-block"></span>
								</div>

							</div>

							<div class="form-group">

								<input type="hidden" id='formlength' name='formlength' value='5'/>
								<?php for($i=1;$i<=5;$i++):?>
									<div class='row'>
									<div class="col-md-3">
										<input class="selectitem mobile-input" name="barcodeName<?php echo $i; ?>" id="barcodeName<?php echo $i; ?>">
									</div>
									<div class="col-md-3 mobile-input">
										<select id="rackName<?php echo $i; ?>" name="rackName<?php echo $i; ?>" class="form-control rackname ">
											<option value=""></option>
										</select>
									</div>
									<div class="col-md-3">
										<p class="text-center" style="padding:5px;">Quantity <span  class='badge' id="qtlLabel<?php echo $i; ?>">0</span></p>
										<input type='hidden' value="0" name="curQty<?php echo $i; ?>" id="curQty<?php echo $i; ?>">
									</div>
									</div>
									<div class='row'>
									<div class="col-md-3 mobile-input">
										<input data-i='<?php echo $i; ?>' type='text' name="rnameTo<?php echo $i; ?>" id="rnameTo<?php echo $i; ?>" class='form-control rackselect2 mobile-input'>
									</div>
									<div class="col-md-3">
										<input id="transferQty<?php echo $i; ?>"  placeholder="Transfer Qty" name="transferQty<?php echo $i; ?>" class="form-control input-md transferqty mobile-input" type="text" >
										<span class="help-block">Qty of the item to transfer to display</span>
									</div>
									</div>
									<hr>
								<?php endfor; ?>
							</div>
							<div class="form-group" id='addmore'>

							</div>
							<input type="button" id='btnAdd' value='Add more item' class='btn btn-default pull-right'/>
							<div class="form-group">
								<label class="col-md-1 control-label" for="button1id"></label>
								<div class="col-md-8">
									<input type='submit' class='btn btn-success' name='btnSave' value='SAVE TRANSFER'/>
									<input type='submit' class='btn btn-danger' id='btnSaveLocally' value='SAVE LOCALLY'/>
									<input type='hidden' name='token' value=<?php echo Token::generate(); ?>>
								</div>
							</div>
						</fieldset>

					</form>
				</div>
				<div id="imagecon">
					<span style='cursor:pointer; position:absolute;right:2px;top:2px;font-size:1.1em;' class='glyphicon glyphicon-remove-sign removeImage'></span>
					<img src="" alt="Image" />
				</div>
			</div>
		</div>

	</div> <!-- end page content wrapper-->
	<script>
		$(document).ready(function() {
			var is_all = '<?php echo $is_all; ?>';
			var b_name = '<?php echo $b_name; ?>';
			var b_id = '<?php echo $b_id; ?>';
			function formatItem(o) {
				if (!o.id)
					return o.text; // optgroup
				else {
					var r = o.text.split(':');
					return "<span> "+r[0]+"</span> <span style='margin-left:10px'>" + r[1] + "</span><span style='display:block;margin-top:5px;'  class='text-danger'><small class='testspanclass'>"+r[2]+"</small></span>";
				}
			}


			if(is_all == '1'){
				$('#bid').select2({
					placeholder: 'Branch',
					allowClear: true,
					minimumInputLength: 2,
					ajax: {
						url: '../ajax/ajax_json.php',
						dataType: 'json',
						type: "POST",
						quietMillis: 50,
						data: function (term) {
							return {
								q: term,
								functionName:'branches'
							};
						},
						results: function (data) {
							return {
								results: $.map(data, function (item) {
									return {
										text: item.name ,
										slug: item.name ,
										id: item.id
									}
								})
							};
						}
					}
				});
				$('#bid').select2('data', {id: b_id, text: b_name});
				$('.rackselect2').select2({
					placeholder: 'Search Rack To',
					allowClear: true,
					minimumInputLength: 2,
					ajax: {
						url: '../ajax/ajax_json.php',
						dataType: 'json',
						type: "POST",
						quietMillis: 50,
						data: function (term) {
							return {
								q: term,
								functionName:'racksBranchFilter',
								branch_id: b_id
							};
						},
						results: function (data) {
							return {
								results: $.map(data, function (item) {
									return {
										text: item.rack,
										slug: item.rack,
										id: item.id
									}
								})
							};
						}
					}
				});
			} else {
				$('.rackselect2').select2({
					placeholder: 'Search Rack To',
					allowClear: true,
					minimumInputLength: 2,
					ajax: {
						url: '../ajax/ajax_json.php',
						dataType: 'json',
						type: "POST",
						quietMillis: 50,
						data: function (term) {
							return {
								q: term,
								functionName:'racksBranchFilter',
								branch_id: '<?php echo $user->data()->branch_id; ?>'
							};
						},
						results: function (data) {
							return {
								results: $.map(data, function (item) {
									return {
										text: item.rack,
										slug: item.rack,
										id: item.id
									}
								})
							};
						}
					}
				});
			}
			$('body').on('change','#bid',function(){
				$(".selectitem").select2("val", null);
				$(".rackname").select2("val", null);
				$('.rackselect2').select2({
					placeholder: 'Search Rack To',
					allowClear: true,
					minimumInputLength: 2,
					ajax: {
						url: '../ajax/ajax_json.php',
						dataType: 'json',
						type: "POST",
						quietMillis: 50,
						data: function (term) {
							return {
								q: term,
								functionName:'racksBranchFilter',
								branch_id: $('#bid').val()
							};
						},
						results: function (data) {
							return {
								results: $.map(data, function (item) {
									return {
										text: item.rack,
										slug: item.rack,
										id: item.id
									}
								})
							};
						}
					}
				});
			});
			// this
			var start=6;
			$('#btnAdd').click(function(){
				var last = start - 1;
				var clonelast = $('#barcodeName'+last).clone();
				var conaddmore = $('#addmore');
				var htmlret = "";
				htmlret += ('<div class="row">');
				htmlret += ('<div class="col-md-3"><input class="selectitem" name="barcodeName'+start+'" id="barcodeName'+start+'"></div>');
				htmlret += ('<div class="col-md-3"><select id="rackName'+start+'" name="rackName'+start+'" class="form-control rackname"><option value=""></option></select></div>');
				htmlret += ('<div class="col-md-3"><p  class="text-center" style="padding:5px;">Quantity <span class="badge" id="qtlLabel'+start+'">0</span></p><input type="hidden" value="0" name="curQty'+start+'" id="curQty'+start+'"></div>');
				htmlret += ('</div>');
				htmlret += ('<div class="row">');
				htmlret += ('<div class="col-md-3"><input data-i="'+start+'" type="text" name="rnameTo'+start+'" id="rnameTo'+start+'" class="form-control rackselect2"></div>');
				htmlret += ('<div class="col-md-3"><input id="transferQty'+start+'"  placeholder="Transfer Qty" name="transferQty'+start+'" class="form-control input-md transferqty" type="text" ><span class="help-block">Qty of the item to transfer to display</span></div>');
				htmlret += ('</div>');
				htmlret += ('<hr>');
				$('#addmore').append(htmlret);

				$("#rackName"+start).select2({
					placeholder : "Select Rack From",
					allowClear: true
				});
				$('#rnameTo'+start).select2({
					placeholder: 'Search Rack To',
					allowClear: true,
					minimumInputLength: 2,
					ajax: {
						url: '../ajax/ajax_json.php',
						dataType: 'json',
						type: "POST",
						quietMillis: 50,
						data: function (term) {
							return {
								q: term,
								functionName:'racksBranchFilter',
								branch_id:$('#bid').val()
							};
						},
						results: function (data) {
							return {
								results: $.map(data, function (item) {
									return {
										text: item.rack,
										slug: item.rack,
										id: item.id
									}
								})
							};
						}
					}
				});
				$("#barcodeName"+start).select2({
					placeholder: 'Item code',
					allowClear: true,
					minimumInputLength: 2,
					formatResult: formatItem,
					formatSelection: formatItem,
					escapeMarkup: function(m) {
						return m;
					},
					ajax: {
						url: '../ajax/ajax_query.php',
						dataType: 'json',
						type: "POST",
						quietMillis: 50,
						data: function (term) {
							return {
								search: term,
								functionName:'searchItemJSON'
							};
						},
						results: function (data) {
							return {
								results: $.map(data, function (item) {
									return {
										text: item.barcode + ":" + item.item_code + ":" +item.description,
										slug: item.description,
										id: item.id
									}
								})
							};
						}

					}
				}).on("select2-close", function(e) {

				}).on("select2-highlight", function(e) {

				});
				$('#formlength').val(start);
				start+=1;
			});
			$('.rackname').select2({
				placeholder : "Select Rack From",
				allowClear: true
			});
			$("#rack_id").change(function(){
				$(".selectitem").select2("val", null);
				$(".badge").text('0');
				$('.rackname').select2('val',null);

			});


			$('body').on('change','.selectitem',function(){
				var codeid = $(this).prop("id");
				var num = codeid.substring(11, codeid.length);
				var thiscontext = $(this);
					if(!$(this).val()){ return;}
					var branch = $("#bid").val();
					var rack_id = $("#rack_id").val(); // change this
					var rackname = $("#rack_id option:selected").text();

					if(!branch && !rack_id ) {
						alert('Please Choose branch  first');
						location.href ='transfer.php';
					} else {

						var endval = $(this).val();
						//alert("test");
						$.ajax({
							url: "../ajax/ajax_get_rack.php",
							type: "POST",
							async: false,
							data: {item_id: endval, branch_id: branch,rack_id:rack_id},
							success: function(data) {
								$("#rackName" + num).empty();
								$("#rackName" + num).append("<option value=''></option>");
								$("#rackName" + num).append(data);
								if(!data){
										alertify.alert("No available stock");
										thiscontext.select2("val", null);
								}
								$("#rackName" + num).select2({
									placeholder : "Select Rack From",
									allowClear: true
								});


							},
							error: function() {
								// save in local storage
								alert('wtf happen');
							}
						});
					}

			});
			$('body').on('change','.rackname',function(){

				var codeid = $(this).prop("id");
				var num = codeid.substring(8, codeid.length);
				var res = $(this).val().split(",");
				$("#qtlLabel" + num).empty();
				$("#qtlLabel" + num).append(res[1]);

			});

			$('body').on('keyup','.transferqty',function(){

				var codeid = $(this).prop("id");
				if(isNaN($(this).val()) || parseInt($(this).val()) < 0 ){
					alert('Invalid quantity');
					$(this).val('');
					return;

				}
				var num = codeid.substring(codeid.length-1, codeid.length);
				var cur = $("#qtlLabel"+num).text();

				if(parseInt(cur) < parseInt($(this).val())){
					alert('Not enough item on rack');
					$(this).val('');
				}
			});
			$('.transferqty').keydown(function(event){
				if(event.keyCode == 13) {
					event.preventDefault();
					$('input,select,textarea')[$('input,select,textarea').index(this)+1].focus();
					return false;
				}
			});
			$('#dtcreated').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dtcreated').datepicker('hide');
			});
			$('body').on('click','#btnSaveLocally',function(e){
				e.preventDefault();
				var json = $('#formInventory').serializeArray();
				var ctr = 0;
				var finalList = [];
				var item = {rack_id_from:0,item_id:0,rack_id_to:0,qty:0,item_code:'',item_description:'',rack_name_from:'',rack_name_to:''};
				var item_ctr = 0;

				for(var i in json){
					if(ctr > 3){
						if(json[i].value){
							if(item_ctr == 0){
								if($("#"+json[i].name).select2('data')){
									item.item_id = json[i].value;
									var item_name =  $("#"+json[i].name).select2('data').text;

									item_name = (item_name).split(':');
									item.item_code = item_name[0];
									item.item_description = item_name[1];
								}
								item_ctr++;

							} else if(item_ctr == 1){
								if($("#"+json[i].name).select2('data')){
									item.rack_id_from = json[i].value;
									item.rack_name_from =  $("#"+json[i].name).select2('data').text;
								}
								item_ctr++;
							}else if(item_ctr == 2){

								item_ctr++;
							}else if(item_ctr == 3){
								if($("#"+json[i].name).select2('data')){
									item.rack_id_to = json[i].value;
									item.rack_name_to =  $("#"+json[i].name).select2('data').text;
								}
								item_ctr++;
							}else if(item_ctr == 4){
								item.qty = json[i].value;
								if(item.rack_id_to && item.rack_id_from && item.item_id && item.qty){
									finalList.push(item);
								}
								item =  {rack_id_from:0,item_id:0,rack_id_to:0,qty:0,item_code:'',item_description:'',rack_name_from:'',rack_name_to:''};
								item_ctr = 0;
							}
						} else {
							item_ctr++;
						}

					}
					ctr++;
				}

				var cur = localStorage['trans_inventory_local'];
				if(cur){
					cur = JSON.parse(cur);
					for(var i in finalList){
						cur.push(finalList[i]);
					}

					localStorage['trans_inventory_local'] = JSON.stringify(cur);
					alertify.alert("Stored locally.",function(){
						location.href='transfer_monitoring.php';
						localStorage['trans_nav'] = 4;
					});
				} else {

					localStorage['trans_inventory_local'] = JSON.stringify(finalList);
					alertify.alert("Stored locally.",function(){
						location.href='transfer_monitoring.php';
						localStorage['trans_nav'] = 4;
					});
				}

			});

		});
	</script>

<?php require_once '../includes/admin/page_tail2.php'; ?>