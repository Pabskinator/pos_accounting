<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('inventory_issues')) {
		// redirect to denied page
		Redirect::to(1);
	}

	$rackcls = new Rack();
	$allrack =  $rackcls->get_active('racks', array('company_id','=',$user->data()->company_id));
	$optracks = "<select class='form-control' id='c_des_rack_id'>";
	if($allrack){
		foreach($allrack as $rack){
			$optracks.="<option value='".$rack->id."'>".$rack->rack."</option>";
		}
	}
	$optracks.="</select>";
?>

	<link rel="stylesheet" href="../css/swipebox.css">

	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>Inventories Issues</h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}
		?>
		<?php include 'includes/issues_nav.php'; ?>
		<div class="row">
			<div class="col-md-12">

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">
						<div class="row">
							<div class="col-md-6">
								Inventory Issues
							</div>
							<div class="col-md-6 text-right">
								<button class='btn btn-default' id='btnDownload'><i class='fa fa-download'></i></button>
							</div>
						</div>
					</div>
					<div class="panel-body">
						<div class="row">

							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon"><span class='glyphicon glyphicon-search'></span></span>
										<input type="text" id="searchSales" class='form-control' placeholder='Search..'/>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select id="branch_id" name="branch_id" class="form-control">
										<option value=''></option>
										<?php
											$branch = new Branch();
											$branches =  $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));
											foreach($branches as $b){
												$a = isset($id) ? $terminal->data()->branch_id : escape(Input::get('branch_id'));

												if($a==$b->id){
													$selected='selected';
												} else {
													$selected='';
												}
												?>
												<option value='<?php echo $b->id ?>' <?php echo $selected ?>><?php echo $b->name;?> </option>
												<?php
											}
										?>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select id="rack_id" name="rack_id" class="form-control">
										<option value=''></option>
										<option value='-1'>Warehouse racks</option>
										<?php
											$rack = new Rack();
											$racks =  $branch->get_active('racks',array('company_id' ,'=',$user->data()->company_id));
											foreach($racks as $b){
												?>
												<option value='<?php echo $b->id ?>'><?php echo $b->rack;?> </option>
												<?php
											}
										?>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select id="type" name="type" class="form-control">
										<option value=''></option>
										<option value='1'><?php echo DAMAGE_LABEL; ?></option>
										<option value='2'><?php echo MISSING_LABEL; ?></option>
										<option value='4'><?php echo INCOMPLETE_LABEL; ?></option>
										<?php
											if(trim(OTHER_ISSUE_LABEL)){
												?>
												<option value='5'><?php echo OTHER_ISSUE_LABEL; ?></option>
												<?PHP
											}
										?>
										<option value='3'>Disposed</option>
									</select>
								</div>
							</div>

						</div>

						<input type="hidden" id="hiddenpage" />
						<div id="holder"></div>
					</div>


				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id='mtitle'>Convert</h4>
					</div>
					<div class="modal-body" id='mbody'>
						<input type="hidden" id='c_item_id'>
						<input type="hidden" id='c_rack_id'>
						<input type="hidden" id='c_branch_id'>


						<div class="form-group">
							<strong>Type:</strong>

							<select id="c_type" name="c_type" class="form-control" disabled>
								<option value='1'>Damage</option>
								<option value='2'>Missing</option>
							</select>
						</div>
						<div class="form-group">
							<strong>Item code:</strong>
							<input type="text" class='form-control' id='c_item_code' disabled>
						</div>
						<div class="form-group">
							<strong>Description:</strong>
							<input type="text" class='form-control' id='c_description' disabled>
						</div>
						<div class="form-group">
							<strong>Current Qty:</strong>
							<input type="text" class='form-control' id='c_qty' disabled>
						</div>

						<div class="form-group">
							<strong>Converty To:</strong>
							<select id="c_type_to" name="c_type_to" class="form-control">
							</select>
						</div>
						<div class="form-group">
							<strong>Converty Qty:</strong>
							<input type="text" class='form-control' id='c_convert_qty'>
						</div>
						<div class="form-group">
							<strong>Rack:</strong> <input type="text" id='c_to_rack' class='form-control'>
						</div>
						<hr>
						<div class="form-group">
							<button id='btnConvertItem' class='btn btn-primary'>Submit</button>
						</div>
					</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<div class="modal fade" id="myModalInc" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id='ititle'></h4>
					</div>
					<div class="modal-body" id='ibody'>
						<input type="hidden" class='form-control' id='inc_item_id'>
						<input type="hidden" class='form-control'  id='inc_rack_id'>
						<input type="hidden" class='form-control'  id='inc_branch_id'>
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
								<input type="text" class='form-control' disabled id='inc_item_code'>
									<span class='help-block'>Item Code</span>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<input type="text" class='form-control' disabled id='inc_description'>
									<span class='help-block'>Description</span>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
								<input type="text" class='form-control' placeholder='Qty to convert' id='inc_qty'>
								<input type="hidden" class='form-control'  id='orig_inc_qty'>
									<span class='help-block'>Surplus Qty To Convert</span>
								</div>
							</div>

						</div>
						<h4 class='text-success'><strong>Converted to Spareparts/Set:</strong></h4>
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control selectitem' id='inc_spare'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' placeholder='Quantity' id='inc_spare_qty'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type='text'  id="inc_to_rack_id" class='form-control rackselect'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<button class='btn btn-default' id='btnAddItemInc'>Add Item</button>
								</div>
							</div>
						</div>
						<div id="no-more-tables">
							<table id='cart' class='table' style='font-size:1em'>
								<thead>
								<tr>
									<th>BARCODE</th>
									<th>ITEM CODE</th>
									<th>QTY</th>
									<th>RACK</th>
									<th></th>
								</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
						<hr>
						<br>
						<h4 class='text-success'><strong>Item Used:</strong></h4>
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control selectitem' id='inc_used'>
									<span class='help-block'>Item Used</span>
								</div>
							</div>
							<div class="col-md-3">
								<select name="rackFrom" id="rack_from" class='form-control rack_from'>
								</select>
								<span class='help-block'>Rack Location</span>
							</div>
							<div class="col-md-3">
								<input type="text" class='form-control' id='rack_qty' disabled>
								<span class='help-block'>Rack Qty</span>
							</div>
							<div class="col-md-3">
								<input type="text" class='form-control' id='rack_qty_used' >
								<span class='help-block'>Qty Used</span>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3">
								<button id='btnAddItemUsed' class='btn btn-default'>Add Used Item</button>
							</div>
						</div>
						<div id="no-more-tables">
							<table id='cart_used' class='table' style='font-size:1em'>
								<thead>
								<tr>
									<th>BARCODE</th>
									<th>ITEM CODE</th>
									<th>QTY</th>
									<th>RACK</th>
									<th></th>
								</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
						<hr>
						<div class="text-right">
							<button class='btn btn-default' id='btnConvertInc'>Convert</button>
						</div>
					</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<script src='../js/swipebox.js'></script>
	<script>

		$(document).ready(function() {
			$('#branch_id').select2({
				placeholder:'Choose branch',
				allowClear:true
			});
			$('#rack_id').select2({
				placeholder:'Choose rack',
				allowClear:true
			});
			$('#type').select2({
				placeholder:'Choose type',
				allowClear:true
			});

			$('body').on('click','#btnDownload',function(){

				var search = $('#searchSales').val();
				var b = $('#branch_id').val();
				var r = $('#rack_id').val();
				var t = $('#type').val();

				window.open(
					'excel_downloader_2.php?downloadName=inventoriesissues&search='+search+'&b='+b+'&r='+r+'&t='+t,
					'_blank' //
				);

			});

			$('body').on('change','#inc_used',function(){

				var v = $(this).val();
				var branch_id = $('#inc_branch_id').val();

				$.ajax({
					url: "../ajax/ajax_get_rack.php",
					type: "POST",
					data: {item_id: v, branch_id: branch_id,rack_id:0},
					success: function(data) {
						$("#rack_from").html(data);
						showRackQty();
					}
				});

			});
			$('body').on('change','#rack_from',function(){
				showRackQty();
			});
			function showRackQty(){
				var rack = $('#rack_from').val();
				var splitted = rack.split(',');
				$('#rack_qty').val(splitted[1]);
			}




			getPage(0);
			$('#c_to_rack').select2({
				placeholder: 'Search rack',
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
							functionName:'racks',
							branch_id: $('#c_branch_id').val()
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

			$('body').on('click','.paging',function(e){
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});

			$("#searchSales").keyup(function(){
				getPage(0);
			});

			function getPage(p){
				var search = $('#searchSales').val();
				var b = $('#branch_id').val();
				var r = $('#rack_id').val();
				var t = $('#type').val();
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,t:t,functionName:'inventoryIssuesPaginate',cid: <?php echo $user->data()->company_id; ?>,search:search,b:b,r:r},
					success: function(data){
						$('#holder').html(data);
					}
				});
			}


			$("#btnAddItemUsed").click(function() {

				var item_id_con =  $('#inc_used');
				var qty_con =  $('#rack_qty_used');
				var rack_id_con =  $('#rack_from');
				var item_id = item_id_con.val();
				var rack = rack_id_con.val();
				var rack_split =rack.split(',');
				var rack_id = rack_split[0];

				var qty = qty_con.val();
				var isoncart = false;

				$('#cart_used >tbody > tr').each(function(){
					var row_id = $(this).attr('id');
					if(row_id == item_id){
						isoncart = true;
						return;
					}
				});
				if(isoncart){
					tempToast('error','<p>Item is already in cart</p>','<h3>WARNING!</h3>');
					return;
				}
				if(item_id && qty){
					if(!qty || isNaN(qty) || parseFloat(qty) < 0){
						tempToast('error','<p>Invalid quantity</p>','<h3>WARNING!</h3>')
						return;
					}
					var sdata =  item_id_con.select2('data');
					var item_code =sdata.text;
					var rack_name = $('#rack_from option:selected').text();
					var arrcode = item_code.split(':');
					removeNoItemLabelUsed();
					var item_bc = arrcode[0];
					$('#cart_used > tbody').append("<tr data-rack_id='"+rack_id+"' id='" + item_id + "'><td data-title='Barcode'>" + item_bc + "</td><td data-title='Item'>" + arrcode[1] + "<br><small class='text-danger'>"+arrcode[2]+"</small></td><td data-title='Quantity'>"+qty+"</td><td>"+rack_name+"</td><td><span  class='glyphicon glyphicon-remove-sign removeItemUsed'></span></td></tr>");
					item_id_con.select2('val',null);
					$('#rack_from').html('');
					$('#rack_qty').val('');
					qty_con.val('');
				} else {
					tempToast('error','<p>Please complete the form</p>','<h3>WARNING!</h3>')
				}
			});

			noItemInCartUsed();
			function noItemInCartUsed() {
				if(!$("#cart_used tbody").children().length) {
					$("#cart_used tbody").append("<td colspan='3' id='noitemused' style='padding-top:10px;' data-title='Item'><span class='text-danger'>NO ITEMS IN CART</span></td>");
				}
			}
			function removeNoItemLabelUsed() {
				$("#noitemused").remove();
			}
			$('body').on('click', '.removeItemUsed', function() {
				$(this).parents('tr').remove();
				noItemInCartUsed();
			});



			noItemInCart();
			function noItemInCart() {
				if(!$("#cart tbody").children().length) {
					$("#cart tbody").append("<td colspan='3' id='noitem' style='padding-top:10px;' data-title='Item'><span class='text-danger'>NO ITEMS IN CART</span></td>");
				}
			}
			function removeNoItemLabel() {
				$("#noitem").remove();
			}
			$('body').on('click', '.removeItem', function() {
				$(this).parents('tr').remove();
				noItemInCart();
			});


			$("#btnAddItemInc").click(function() {

				var item_id_con =  $('#inc_spare');
				var qty_con =  $('#inc_spare_qty');
				var rack_id_con =  $('#inc_to_rack_id');
				var item_id = item_id_con.val();
				var rack_id = rack_id_con.val();
				var qty = qty_con.val();
				var isoncart = false;

				$('#cart >tbody > tr').each(function(){
					var row_id = $(this).attr('id');
					if(row_id == item_id){
						isoncart = true;
						return;
					}
				});
				if(isoncart){
					tempToast('error','<p>Item is already in cart</p>','<h3>WARNING!</h3>');
					return;
				}
				if(item_id && qty){
					if(!qty || isNaN(qty) || parseFloat(qty) < 0){
						tempToast('error','<p>Invalid quantity</p>','<h3>WARNING!</h3>')
						return;
					}
					var sdata =  item_id_con.select2('data');
					var item_code =sdata.text;
					var rack_name =rack_id_con.select2('data').text;
					var arrcode = item_code.split(':');
					removeNoItemLabel();
					var item_bc = arrcode[0];
					$('#cart > tbody').append("<tr data-rack_id='"+rack_id+"' id='" + item_id + "'><td data-title='Barcode'>" + item_bc + "</td><td data-title='Item'>" + arrcode[1] + "<br><small class='text-danger'>"+arrcode[2]+"</small></td><td data-title='Quantity'>"+qty+"</td><td>"+rack_name+"</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
					item_id_con.select2('val',null);
					$('#inc_to_rack_id').select2('val',null);
					qty_con.val('');
				} else {
					tempToast('error','<p>Please complete the form</p>','<h3>WARNING!</h3>')
				}
			});

			$('body').on('click','#btnConvertInc',function(){
				var inc_item_id = $('#inc_item_id').val();
				var inc_rack_id = $('#inc_rack_id').val();
				var inc_branch_id = $('#inc_branch_id').val();
				var inc_qty = $('#inc_qty').val();
				var orig_inc_qty = $('#orig_inc_qty').val();
				var con = $(this);
				button_action.start_loading(con);
				if($("#cart tbody tr").children().length) {
					if(!inc_qty || isNaN(inc_qty) || parseFloat(inc_qty) > parseFloat(orig_inc_qty)){
						tempToast('error','<p>Invalid quantity</p>','<h3>WARNING!</h3>');
						button_action.end_loading(con);
						return;
					}
					var toOrder =[];
					var foundNoqty =0;
					var item_used = [];

					$('#cart >tbody > tr').each(function(index) {
						var row = $(this);
						var item_id = $(this).prop('id');
						var qty = row.children().eq(2).text();
						var rack_id = row.attr('data-rack_id');
						if(qty == '' || qty == undefined){
							qty= 0;
						}
						if(isNaN(qty) || qty == 0){
							foundNoqty = parseInt(foundNoqty) + 1;
						}

						toOrder[index] = {
							item_id: item_id, qty: qty,rack_id:rack_id
						}
					});

					$('#cart_used >tbody > tr').each(function(index) {
						var row = $(this);
						var item_id = $(this).prop('id');
						var qty = row.children().eq(2).text();
						var rack_id = row.attr('data-rack_id');
						if(qty == '' || qty == undefined){
							qty= 0;
						}

						item_used[index] = {
							item_id: item_id, qty: qty,rack_id:rack_id
						}
					});


					if(foundNoqty > 0) {
						tempToast('error','<p>Please Indicate the Quantity of the items</p>','<h3>WARNING!</h3>')
						button_action.end_loading(con);
					} else {
						$.ajax({
						    url:'../ajax/ajax_query.php',
						    type:'POST',
						    data: {functionName:'convertIncomplete',toConvert: JSON.stringify(toOrder),itemUsed: JSON.stringify(item_used),inc_item_id:inc_item_id,inc_qty:inc_qty,inc_rack_id:inc_rack_id,inc_branch_id:inc_branch_id},
						    success: function(data){
						       tempToast('info','<p>'+data+'</p>','<h4>Information!</h4>');
							    $('#myModal').modal('hide');
							    var page = $('#hiddenpage').val();
							    button_action.end_loading(con);

							    getPage(page);
						    },
						    error:function(){
							    button_action.end_loading(con);
						    }
						})
					}
				} else {
					tempToast('error','<p>No item in cart.</p>','<h3>WARNING!</h3>');
					button_action.end_loading(con);
				}
			});


			$('body').on('change','#type,#rack_id,#branch_id',function(){
				getPage(0);
			});
			$('body').on('click','#btnConvertItem',function(){
				var rack_id = $('#c_rack_id').val();
				var branch_id = $('#c_branch_id').val();
				var item_id = $('#c_item_id').val();
				var orig_qty = 	$('#c_qty').val();
				var orig_type = $('#c_type').val();
				var convert_qty = 	$('#c_convert_qty').val();
				var convert_type = $('#c_type_to').val();
				var c_des_rack_id = $('#c_to_rack').val();
				var btncon = $(this);
				var btnoldval = btncon.html();
				if(!convert_qty || isNaN(convert_qty) || parseFloat(convert_qty) < 0){
					tempToast('error','<p>Invalid quantity</p>','<h4>Warning!</h4>')
					return;
				}
				btncon.html('Loading...');
				btncon.attr('disabled',true);
				$.ajax({
				    url:'../ajax/ajax_query2.php',
				    type:'POST',
				    data: {functionName:'convertIssues',des_rack_id:c_des_rack_id,rack_id:rack_id,item_id:item_id,orig_qty:orig_qty,orig_type:orig_type,convert_qty:convert_qty,convert_type:convert_type,branch_id:branch_id},
				    success: function(data){
				             alertify.alert(data,function(){
					             btncon.html(btnoldval);
					             btncon.attr('disabled',false);
					             $('#myModal').modal('hide');
					             var page = $('#hiddenpage').val();
					             getPage(page);
				             });

				    },
				    error:function(){
					    btncon.html(btnoldval);
					    btncon.attr('disabled',false);
				    }
				});
			});

			$('body').on('click','.btnConvert',function(){
				var btn = $(this);
				var item_code = btn.attr('data-item_code');
				var description = btn.attr('data-description');
				var rack_id = btn.attr('data-rack_id');
				var item_id = btn.attr('data-item_id');
				var qty = btn.attr('data-qty');
				var status = btn.attr('data-status');
				var branch_id = btn.attr('data-branch_id');
				var statuses = [];
				statuses[0] = 'Good', statuses[1] = 'Damage',statuses[2] = 'Missing',statuses[3] = 'Disposed',statuses[4] = 'Incomplete';
				var retoption = '';
				for(var j in statuses){
					if(j != status){
						retoption += "<option value='"+j+"'>"+statuses[j]+"</option>";
					}
				}
				if(status != 4){
					$('#c_type_to').html(retoption);
					$('#c_rack_id').val(rack_id);
					$('#c_item_id').val(item_id);
					$('#c_type').val(status);
					$('#c_item_code').val(item_code);
					$('#c_description').val(description);
					$('#c_qty').val(qty);
					$('#c_branch_id').val(branch_id);
					$('#myModal').modal('show');
				} else {
					$('#inc_item_id').val(item_id);
					$('#inc_rack_id').val(rack_id);
					$('#inc_branch_id').val(branch_id);
					$('#inc_item_code').val(item_code);
					$('#inc_description').val(description);
					$('#inc_qty').val(qty);
					$('#orig_inc_qty').val(qty);

					$('#inc_spare').select2('val',null);
					$('#inc_spare_qty').val('');
					$('#inc_to_rack_id').select2('val',null);
					$('#myModalInc').modal('show');
					$("#cart").find("tr:gt(0)").remove();
					noItemInCart();
				}

			});
			$('body').on('click','.btnAtt',function(){
				var btn = $(this);
				var rack_id = btn.attr('data-rack_id');
				var item_id = btn.attr('data-item_id');
				var btnoldval= btn.html();
				btn.attr('disabled',true);
				btn.html('Loading...');
				// get attachment list
				$.ajax({
				    url:'../ajax/ajax_query.php',
				    type:'POST',
				    data: {functionName:'getAttachmentIssues', item_id:item_id,rack_id:rack_id},
				    success: function(data){
					    try{
						    btn.attr('disabled',false);
						    btn.html(btnoldval);
						    var json = JSON.parse(data);
						    console.log(json);
						    if(json){
							    var ob = [];
							    for(var i in json){
								    ob.push({href:json[i].path,title:''});
							    }
							    $.swipebox(ob);
							    btn.attr('disabled',false);
							    btn.html(btnoldval);
						    } else {
							    tempToast('error',"<p>No attachment found.</p>","<h4>Info!</h4>");
						    }
					    } catch(e){
						    tempToast('error',"<p>No attachment found.</p>","<h4>Info!</h4>");
						    btn.attr('disabled',false);
						    btn.html(btnoldval);
					    }

				    },
				    error:function(){

				    }
				});

			});
			$('body').on('change','#c_type_to',function(){
				if($('#c_type').val() == $('#c_type_to').val()){
					tempToast('error',"<p>Cannot convert on the same type.</p>","<h4>Warning</h4>")
					$('#c_type_to').val(0);
				}
			});
			$('.rackselect').select2({
				placeholder: 'Search rack',
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
							functionName:'racks',
							branch_id: $('#inc_branch_id').val()
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


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>