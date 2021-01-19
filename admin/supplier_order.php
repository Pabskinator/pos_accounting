<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('supplier_o')) {
		// redirect to denied page
		Redirect::to(1);
	}


?>

	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
				<?php include 'includes/supplier_nav.php'; ?>
			<div class="content-header">

			</div>

			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
					<?php
						$supplier = new Supplier();
						$suppliers = $supplier->get_active('suppliers',array('company_id','=',$user->data()->company_id));
					?>
					<select name="supplier_id" id="supplier_id" class='form-control'>
						<option value=""></option>
					<?php
						foreach($suppliers as $s){
							?>
							<option value="<?php echo $s->id; ?>"><?php echo $s->name; ?></option>
							<?php
						}
					?>
					</select>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<?php
							$branch = new Branch();
							$branches = $branch->get_active('branches',array('company_id','=',$user->data()->company_id));
							function getOptionBranch($branches){
								if($branches){
									foreach($branches as $b){
										?>
										<option value="<?php echo $b->id; ?>"><?php echo $b->name; ?></option>
									<?php
									}
								}
							}
						?>
					<select name="branch_to" id="branch_to" class='form-control'>
						<option value=""></option>
						<?php  getOptionBranch($branches); ?>
					</select>
						</div>
				</div>

				<div class="col-md-3">
					<div class="form-group">
						<select name="is_rush" id="is_rush" class='form-control'>
							<option value="0">Regular order</option>
							<option value="1">Rush order</option>
						</select>
						<span class='help-block'>Order type</span>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<input type="text" id='ship_to' placeholder="Ship To" class='form-control'>
						<span class='help-block'>Please enter name of the recipient</span>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<input type="text" id='terms' placeholder="Terms" class='form-control'>
						<span class='help-block'>Terms in days</span>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<input type="text" id='delivery_date' placeholder="Expected Delivery" class='form-control'>
						<span class='help-block'>Delivery Date</span>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<input type="text" id='remarks' placeholder="Remarks" class='form-control'>
						<span class='help-block'>Remarks</span>
					</div>
				</div>

			</div>

				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
						<select class='form-control' name="supplier_item" id="supplier_item">
							<option value=""></option>
						</select>
						</div>
					</div>
					<div class="col-md-3" id='item_unit_container' style='display: none;'>
						<div class="form-group">
						<select class='form-control' name="item_unit" id="item_unit">

						</select>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
						<input type="text" class='form-control' placeholder='Quantity' id='qty' />
							</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<input type="button" value='Add' id='additem' class='btn btn-default'/>
							</div>
					</div>
				</div>
			</div>
			<hr />
			<div id="no-more-tables">
			<table id='cart' class="table">
				<thead>
				<tr><th>Item</th><th>Purchase Price</th><th>Quantity</th><th>Total</th><th></th></tr>
				</thead>
				<tbody></tbody>
			</table>
			</div>
			<div class="row">
				<div class="col-md-8">

				</div>
				<div class="col-md-4">
					<input type="button" id='void' value='VOID' class='btn btn-danger' />
					<input type="button" id='save' value='SAVE' class='btn btn-success' />
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<!-- Modal -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h3></h3>
				</div>
				<div class="modal-body">
					<form class="form-horizontal">
					<fieldset>
					<div class="form-group">
						<label class="col-md-4 control-label" for="n_item_code">Item Code</label>
						<div class="col-md-8">
							<input id="n_item_code" name="n_item_code" placeholder="Item code" class="form-control input-md" type="text">
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4 control-label" for="n_description">Description</label>
						<div class="col-md-8">
							<input id="n_description" name="n_description" placeholder="Description" class="form-control input-md" type="text" >
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4 control-label" for="n_purchase_price">Purchase Price</label>
						<div class="col-md-8">
							<input id="n_purchase_price" name="n_purchase_price" placeholder="Purchase Price" class="form-control input-md" type="text" >
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-4 control-label" for="min_qty">Min Quantity</label>
						<div class="col-md-8">
							<input id="n_min_qty" name="n_min_qty" placeholder="Min Quantity" class="form-control input-md" type="text" >
						</div>
					</div>

					<!-- Button (Double) -->
					<div class="form-group">
						<label class="col-md-4 control-label" for="btnSaveNew"></label>
						<div class="col-md-8">
							<input type='button' class='btn btn-success' id='btnSaveNew' value='Add item'/>
						</div>
					</div>
				</fieldset>
					</form>
				</div>
			</div>
		</div>
	</div>
	<script>
		$(function(){

			$('#supplier_id').select2({
				placeholder: 'Choose Supplier',
				allowClear: true
			});

			$('#branch_to').select2({
				placeholder: 'Choose Branch',
				allowClear: true
			});

			$('#supplier_item').select2({
				placeholder: 'Choose Item',
				allowClear: true
			});

			$('#supplier_item').change(function(){
				var a = $(this).val();
				if(a == -1){
					$('#myModal').modal('show');
				}
				var id = $('#supplier_item option:selected').attr('data-item_id');

				$.ajax({
					url:'../ajax/ajax_wh_order.php',
					type:'POST',
					dataType:'json',
					data: {functionName:'getUnits',item_id:id},
					success: function(data){
						if(data.length){
							$('#item_unit_container').show();
							var html = "<option value=''>Choose Unit</option>";
							for(var i in data){
								html += "<option value='"+data[i].qty+"'>"+data[i].unit_name+"</option>";
							}
							$('#item_unit').html(html);
						} else {
							$('#item_unit_container').hide();
							$('#item_unit').html("<option value=''>Choose Unit</option>");
						}
					},
					error:function(){
						$('#item_unit_container').hide();
						$('#item_unit').html("<option value=''>Choose Unit</option>");
					}
				});

			});

			$('#supplier_id').change(function(){
				var a = $(this).val();

				$.ajax({
				    url:'../ajax/ajax_query.php',
				    type:'post',
				    data: {sup_id:a,functionName:'getSuppliersItem'},
				    success: function(data){
					    $('#supplier_item').html("<option value=''></option>");
					    $('#supplier_item').append(data);
				    },
				    error:function(){
					    alertify.alert('Error Occur. Due to slow internet. The page will be refresh.',function(){
						    location.href='supplier_order.php';
					    });
				    }
				});

			});

			var ajaxOnProgress = false;

			noItemInCart();

			function noItemInCart() {
				if(!$("#cart tbody").children().length) {
					$("#cart tbody").append("<td colspan='3' id='noitem' style='padding-top:10px;' ><span class='text-danger'>NO ITEMS IN CART</span></td>");
				}
			}

			function disabledSelectButton(){
				if($("#cart tbody").children().length) {
					$('#supplier_id').select2("enable",false);
					$('#branch_to').select2("enable",false);
				} else {
					$('#supplier_id').select2("enable",true);
					$('#branch_to').select2("enable",true);
				}
			}
			$('body').on('click', '.removeItem', function() {
				$(this).parents('tr').remove();
				disabledSelectButton();
				noItemInCart();

			});
			$('#void').click(function() {
				$("#cart").find("tr:gt(0)").remove();
				disabledSelectButton();
				noItemInCart();
			});
			function removeNoItemLabel() {
				$("#noitem").remove();
			}

			$("#btnSaveNew").click(function() {
				$('.loading').show();
				var supplier_id = $("#supplier_id").val();
				var branch_to = $("#branch_to").val();
				var supplier_item = 0;
				var qty =  1;
				if(!supplier_id || !branch_to  || !qty) {
					tempToast('error','<p>Please complete the form first</p>','<h3>WARNING!</h3>');
					$('.loading').hide();
				} else if(isNaN($('#n_min_qty').val())){
					tempToast('error','<p>Invalid Minimun quantity value</p>','<h3>WARNING!</h3>');
					$('.loading').hide();
					return;
				} else if (!$('#n_purchase_price').val() || isNaN($('#n_purchase_price').val())){
					tempToast('error','<p>Invalid Purchase price value</p>','<h3>WARNING!</h3>');
					$('.loading').hide();
					return;
				} else {

					removeNoItemLabel();

					var item_id = 0;
					var description = $('#n_description').val();
					var purchase_price = $('#n_purchase_price').val();
					var item_code = $('#n_item_code').val();
					var min_qty =$('#n_min_qty').val();

					var totalprice = parseFloat(qty) * parseFloat(purchase_price);

					$('#cart > tbody').append("<tr data-new='1' data-description='"+description+"' data-min_qty='"+min_qty+"' data-item_code='"+item_code+"' data-item_price='"+purchase_price+"' data-item_id='"+item_id+"' id='" + supplier_item + "'><td data-title='Item'>" + description + "</td><td data-title='Price'>" + number_format(purchase_price,2) + "</td><td data-title='Quantity'><input type='text' class='form-control  qty' value='"+qty+"' style='width:80px;'></td><td data-title='Total'>"+number_format(totalprice,2)+"</td><td data-title='Action'><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
					$('#supplier_item').select2('val','');
					$('#qty').val('');

					$('#n_item_code').val('');
					$('#n_min_qty').val('');
					$('#n_purchase_price').val('');
					$('#n_description').val('');

					$('.loading').hide();

				}

				disabledSelectButton();
				$('#myModal').modal('hide');

			});

			$('#additem').click(function(){
				$('.loading').show();
				var supplier_id = $("#supplier_id").val();
				var branch_to = $("#branch_to").val();
				var supplier_item =  $("#supplier_item").val();
				var qty =  $("#qty").val();
				var allqty = 0;
				var isoncart = false;

				$('#cart >tbody > tr').each(function(){
					var row_id = $(this).attr('id');
					if(row_id == supplier_item){
						isoncart = true;
						return;
					}
				});

				if(isoncart){
					tempToast('error','<p>Item is already in cart</p>','<h3>WARNING!</h3>');
					$('.loading').hide();
					return;
				}
				if(!supplier_id || !branch_to || !supplier_item || !qty) {
					tempToast('error','<p>Please complete the form first</p>','<h3>WARNING!</h3>');
					$('.loading').hide();
				} else {

					var item_context = $('#supplier_item :selected');
					removeNoItemLabel();
					var item_id = item_context.attr('data-item_id');
					var description = item_context.text();
					var itemsinfo = description.split(':');
					var purchase_price =item_context.attr('data-purchase_price');
					var item_code = item_context.attr('data-item_code');
					var min_qty = item_context.attr('data-min_qty');

					var item_unit = $('#item_unit').val();
					if(item_unit && !isNaN(item_unit)){
						qty = parseFloat(qty) * parseFloat(item_unit);
					}
					var totalprice = parseFloat(qty) * parseFloat(purchase_price);
					$('#cart > tbody').append("<tr data-is_decimal='"+item_context.attr('data-is_decimal')+"' data-new='0' data-description='' data-min_qty='"+min_qty+"' data-item_code='"+item_code+"' data-item_price='"+purchase_price+"' data-item_id='"+item_id+"' id='" + supplier_item + "'><td data-title='Item'>" + itemsinfo[0] + " <br> "+itemsinfo[1]+" "+itemsinfo[2]+"</td><td data-title='Price'>" + number_format(purchase_price,2) + "</td><td data-title='Quantity'><input type='text' class='form-control  qty' value='"+qty+"' style='width:80px;'></td><td data-title='Total'>"+number_format(totalprice,2)+"</td><td data-title='Action'><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
					$('#supplier_item').select2('val','');
					$('#qty').val('');
					$('.loading').hide();
					disabledSelectButton();

				}
			});

			$('body').on('keyup','.qty',function(){
				var qty =$(this).val();
				if(!qty || isNaN(qty) || parseInt(qty) < 1 ){
					alertify.alert('Invalid quantity');
					$(this).val(1);
					return;
				}
				var row = $(this).parents('tr');
				var price = row.attr('data-item_price');
				var total = parseFloat(qty) * parseFloat(price);
				row.children().eq(3).text(number_format(total,2));

			});
			function updatetotalvalue(){


			}
			$('#save').click(function(){
				if($("#cart tbody tr").children().length) {
					var supplier_id = $("#supplier_id").val();
					var branch_to = $("#branch_to").val();
					var is_rush = $("#is_rush").val();
					var ship_to = $("#ship_to").val();
					var terms = $("#terms").val();
					var deliver_date = $("#delivery_date").val();
					var remarks = $("#remarks").val();

					var toOrder = new Array();
					$('#cart >tbody > tr').each(function(index) {
						var row = $(this);
						var item_id = row.attr('data-item_id');
						var supplier_item_id = row.attr('id');
						var is_new = row.attr('data-new');
						var min_qty = row.attr('data-min_qty');
						var purchase_price = row.attr('data-item_price');
						var description = row.attr('data-description');
						var item_code = row.attr('data-item_code');
						var qty = row.children().eq(2).find('input').val();

						toOrder[index] = {
							item_id: item_id,
							is_new: is_new,
							min_qty : min_qty,
							purchase_price: purchase_price,
							description:description,
							qty:qty,
							item_code:item_code,
							supplier_item_id:supplier_item_id

						}
					});
					if(toOrder.length > 0){
						toOrder = JSON.stringify(toOrder);
						$.ajax({
						    url:'../ajax/ajax_query.php',
						    type:'post',
						    data: {toOrder:toOrder,remarks:remarks,terms:terms,delivery_date:deliver_date, ship_to:ship_to,is_rush:is_rush,supplier_id:supplier_id,branch_to:branch_to, functionName:'orderToSupplier'},
						    success: function(data){
								alertify.alert(data,function(){
									location.href='supplier_order.php';
								});

						    },
						    error:function(){
							    alertify.alert('Error Occur. Due to slow internet. The page will be refresh.',function(){
								    location.href='supplier_order.php';
							    });
						    }
						})
					}
				}
			});
			function formatItem(o) {
				if (!o.id)
					return o.text; // optgroup
				else {
					var r = o.text.split(':');
					if(r[0] == 'New Item Supplier'){
						return "<span> "+r[0]+"</span> <span style='margin-left:10px'></span><span style='display:block;margin-top:5px;'  class='text-danger'><small class='testspanclass'>Choose if new supplier item</small></span>";
					} else {
						return "<span> "+r[0]+"</span> <span style='margin-left:10px'>" + r[1] + "</span><span style='display:block;margin-top:5px;'  class='text-danger'><small class='testspanclass'>"+r[2]+"</small></span>";
					}
				}
			}
			$("#supplier_item").select2({
				placeholder: 'Supplier Item',
				allowClear: true,
				formatResult: formatItem,
				formatSelection: formatItem,
				escapeMarkup: function(m) {
					return m;
				}
			});
		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>