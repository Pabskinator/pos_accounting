<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';
	if(false) {
		// redirect to denied page
		Redirect::to(1);
	}


?>


	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<?php include 'includes/bad_order_nav.php' ?>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}
		?>
		<div class="row">
			<div class="col-md-12">

				<!-- REQUEST -->
				<div id="con_req" style='display:none;'>
					<h3>Request Bad Order</h3>
					<div class="row">

						<div class="col-md-3">
							<div class="form-group">
								<select name="supplier_id" id="supplier_id" class='form-control'>
									<option value="">Choose Supplier</option>
									<?php
										$supcls = new Supplier();
										$suppliers = $supcls->get_active('suppliers',array('company_id' ,'=',$user->data()->company_id));
									?>
									<?php
										foreach($suppliers as $sup):
											?>
											<option value="<?php echo escape($sup->id); ?>"><?php echo escape($sup->name); ?></option>
										<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<input type="text" class='form-control' id='branch_id'>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<input type="text" placeholder='Supplier Order Id' class='form-control' id='supplier_order_id'>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<input type="text" class='form-control' id='remarks' placeholder='Remarks'>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<input type="text" class='form-control selectitem' id='item_id'>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<input type="text" class='form-control' id='rack_id'>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<input type="text" class='form-control' id='qty' placeholder='Quantity'>
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<button class='btn btn-default' id='btnAdd'>Add Item</button>
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
					<hr />
					<div class="text-right">
						<button id='save' class='btn btn-success'><span class='glyphicon glyphicon-save'></span> SAVE</button>
					</div>
					<div id="test"></div>
				</div>
				<!-- END REQUEST -->
				<!-- FOR APPROVAL -->
				<div id="con_app" style='display:none;'>
					<div class="panel panel-primary">
						<div class="panel-heading">For approval</div>
						<div class="panel-body">
							<div id="content_app"></div>
						</div>
					</div>
				</div>
				<!-- END FOR APPROVAL -->

				<div id="con_log" style='display:none;'>
					<div class="panel panel-primary">
						<div class="panel-heading">Log</div>
						<div class="panel-body">
							<div id="content_log"></div>
						</div>
					</div>
				</div>


			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id='mtitle'></h4>
				</div>
				<div class="modal-body" id='mbody'>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<script>

		$(document).ready(function() {

			showContainer(true,false,false);
			function showContainer(c1,c2,c3){
				var con_req = $('#con_req');
				var con_app = $('#con_app');
				var con_log= $('#con_log');
				con_req.hide();
				con_app.hide();
				con_log.hide();
				if(c1){
					con_req.fadeIn(300);
				} else if(c2){
					con_app.fadeIn(300);
					getForApproval();
				} else if(c3){
					con_log.fadeIn(300);
					getLog();
				}
			}
			$('body').on('click','.btn_nav',function(e){
				e.preventDefault();
				var con = $(this).attr('data-con');
				if(con == 1){
					showContainer(true,false,false);
				} else if(con == 2){
					showContainer(false,true,false);
				}else if(con == 3){
					showContainer(false,false,true);
				}
				$('#secondNavigationContainer').hide();
			});


			$('#branch_id').select2({
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
			$('#rack_id').select2({
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
							functionName:'racks'
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
			// cart
			//cart

			noItemInCart();
			function noItemInCart() {
				if(!$("#cart tbody").children().length) {
					$("#cart tbody").append("<td colspan='4' id='noitem' style='padding-top:10px;' data-title='Item'><span class='text-danger'>NO ITEMS IN CART</span></td>");
				}
			}
			function removeNoItemLabel() {
				$("#noitem").remove();
			}
			$('body').on('click', '.removeItem', function() {
				$(this).parents('tr').remove();
				noItemInCart();
			});

			$("#btnAdd").click(function() {
				var btn = $(this);
				var btnoldval = btn.html();
				btn.attr('disabled',true);
				btn.html('Checking Stocks...');

				var supplier_id = $('#supplier_id').val();
				var branch_id = $('#branch_id').val();
				var remarks = $('#remarks').val();
				var item_id_con =  $('#item_id');
				var qty_con =  $('#qty');
				var rack_con =  $('#rack_id');
				var item_id = item_id_con.val();
				var qty = qty_con.val();
				var rack_id = rack_con.val();
				var isoncart = false;
				// check item availability
				if(branch_id && item_id && rack_id && qty){
					$.ajax({
					    url:'../ajax/ajax_query.php',
					    type:'POST',
					    data: {functionName:'checkItemAvailability',branch_id:branch_id,item_id:item_id,rack_id:rack_id,qty:qty},
					    success: function(data){
							if(data == 1){
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

									if(!qty || isNaN(qty) || parseFloat(qty) < 0){
										tempToast('error','<p>Invalid quantity</p>','<h3>WARNING!</h3>')
										return;
									}
									var sdata =  item_id_con.select2('data');
									var item_code =sdata.text;
									var arrcode = item_code.split(':');
									removeNoItemLabel();
									var item_bc = arrcode[0];
									var rack_name = rack_con.select2('data').text;
									$('#cart > tbody').append("<tr data-rack_id='"+rack_id+"' id='" + item_id + "'><td data-title='Barcode'>" + item_bc + "</td><td data-title='Item'>" + arrcode[1] + "<br><small class='text-danger'>"+arrcode[2]+"</small></td><td data-title='Quantity'>"+qty+"</td><td data-title='Rack'>"+rack_name+"</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
									item_id_con.select2('val',null);
									qty_con.val('');
									rack_con.select2('val',null);
								} else {
									tempToast('error','<p>Invalid item</p>','<h3>WARNING!</h3>')
								}
						    btn.attr('disabled',false);
						    btn.html(btnoldval);
					    },
					    error:function(){
					        
					    }
					});
				} else {
					tempToast('error','<p>Please complete the form</p>','<h3>WARNING!</h3>');
					btn.attr('disabled',false);
					btn.html(btnoldval);
				}
			});

			$('#save').click(function(){
				if($("#cart tbody tr").children().length) {
					var supplier_id = $('#supplier_id').val();
					var branch_id = $('#branch_id').val();
					var supplier_order_id = $('#supplier_order_id').val();
					var remarks = $('#remarks').val();

					if((supplier_id) && branch_id ){
						var toOrder =[];
						var foundNoqty =0;
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
						if(foundNoqty > 0) {
							tempToast('error','<p>Please Indicate the Quantity of the items</p>','<h3>WARNING!</h3>')
						} else {
							$('.loading').show();
							toOrder = JSON.stringify(toOrder);
							$.ajax({
								url: "../ajax/ajax_query.php",
								type: "POST",
								data: {
									toOrder: toOrder,
									supplier_id: supplier_id,
									branch_id: branch_id,
									supplier_order_id: supplier_order_id,
									remarks: remarks,
									functionName:'requestBadOrder'
								},
								success: function(data) {
									alertify.alert(data,function(){
										location.href = "bad_order.php";
									});
								},
								error: function() {
									alert('Saving transaction error');
									location.href = "bad_order.php";
								}
							});
						}
					} else {
						tempToast('error','<p>Please choose supplier</p>','<h3>WARNING!</h3>')
					}
				} else {
					tempToast('error','<p>No items in cart</p>','<h3>WARNING!</h3>')
				}
			});

			/* FOR APPROVAL */
			function getForApproval(){
				$.ajax({
					url:'../ajax/ajax_query.php',
					type:'POST',
					beforeSend: function(){
						$('#content_app').html("Loading content. Please wait...");
					},
					data: {functionName:'getBadorder',status:1},
					success: function(data){
						$('#content_app').html(data);
					},
					error:function(){

					}
				});
			}

			function getLog(){
				$.ajax({
					url:'../ajax/ajax_query.php',
					type:'POST',
					beforeSend: function(){
						$('#content_log').html("Loading content. Please wait...");
					},
					data: {functionName:'getBadorder',status:2},
					success: function(data){
						$('#content_log').html(data);
					},
					error:function(){

					}
				});
			}
			$('body').on('click','.btnDetails',function(){
				var id = $(this).attr('data-id');
				$('#myModal').modal('show');
				var mbody = $('#mbody');
				mbody.html('Loading content. Please Wait...');
				$.ajax({
					url:'../ajax/ajax_query.php',
					type:'POST',
					data: {functionName:'getBadOrderDetails',id:id},
					success: function(data){
						mbody.html(data);
					},
					error:function(){


					}
				})
			});
			$('body').on('click','#btnApproveRequest',function(){
				var btncon = $(this);
				var btnoldval = btncon.html();
				var id = btncon.attr('data-id');
				var racks = btncon.attr('data-racks');
				btncon.attr('disabled',true);
				btncon.html('Loading...');
				if(id){
					alertify.confirm("Are you sure you want to approve this request?",function(e){
						if(e){
							$.ajax({
							    url:'../ajax/ajax_query.php',
							    type:'POST',
							    data: {functionName:'approveRequestBadOrder',id:id},
							    success: function(data){
								    tempToast('info','<p>'+data+'</p>','<h3>Info!</h3>');
								    $('#myModal').modal('hide');
								    getForApproval();

							    },
							    error:function(){
							        
							    }
							})
						} else {
							btncon.attr('disabled',false);
							btncon.html(btnoldval);
						}
					});

				} else {
					tempToast('error','<p>Invalid details</p>','<h3>Warning!</h3>');
				}
			});

			$('body').on('click','#btnDeclineRequest',function(){
				var btncon = $(this);
				var btnoldval = btncon.html();
				var id = btncon.attr('data-id');
				btncon.attr('disabled',true);
				btncon.html('Loading...');
				if(id){
					alertify.confirm("Are you sure you want to approve this request?",function(e){
						if(e){

						} else {
							btncon.attr('disabled',false);
							btncon.html(btnoldval);
						}
					});

				}
			});

			$('body').on('keyup','#qty',function(){

			});
		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>