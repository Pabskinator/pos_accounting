<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('order_inv_m')) {
		// redirect to denied page
		Redirect::to(1);
	}

	$crud = new Crud();

	$branches = $crud->get_active('branches',array('company_id','=',$user->data()->company_id));
	//$suppliers = $crud->get_active('suppliers',array('company_id','=',$user->data()->company_id));
	//$items = $crud->get_active('items',array('company_id','=',$user->data()->company_id));
?>


	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Order Inventory </h1>
			</div>
			<?php
				// get flash message if add or edited successfully
				if(Session::exists('flash')) {
					echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
				}

			?>
			<?php
				function getOptionSupplier($suppliers){
					if($suppliers){
						foreach($suppliers as $b){
							?>
							<option data-sup="1" value="<?php echo $b->id; ?>"><?php echo $b->name; ?></option>
							<?php
						}
					}
				}
			?>
		<!--	<div class="row">
				<div class="col-md-4">
					<div class="form-group">
						<select name="sup_from" id="sup_from" class='form-control'>
							<option value=""></option>
							<?php getOptionSupplier($suppliers); ?>
						</select>
					</div>
				</div>
			</div> -->
			<div class="row">

				<div class="col-md-4">
					<div class="form-group">
					<select name="branch_from" id="branch_from" class='form-control'>
						<option value=""></option>

						<?php
							if($branches){
								foreach($branches as $b){
									if($b->id == $user->data()->branch_id) continue;
									?>
									<option value="<?php echo $b->id; ?>"><?php echo $b->name; ?></option>
									<?php
								}
							}
						?>
					</select>
						<input type="hidden" name='is_sup' id='is_sup'/>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
					<input type='hidden' value='<?php echo $user->data()->branch_id; ?>' name="branch_to" id="branch_to" >
					</div>
				</div>
				<div class="col-md-4">

				</div>
			</div>


				<div class="row">

					<div class="col-md-4">
						<div class="form-group">
						<input name="item" id="item" class='selectitem'>

					</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
						<input type="text" class="form-control" id='qty' placeholder="Quantity"/>
							</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
						<button class="btn btn-default" id='add' ><span class='glyphicon glyphicon-plus'></span> ADD</button>
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
	</div> <!-- end page content wrapper-->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style='width:70%;' >
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
	<div class="hidden-xs">
		<div id="imagecon">
			<span style='cursor:pointer; position:absolute;right:2px;top:2px;font-size:1.1em;' class='glyphicon glyphicon-remove-sign removeImage'></span>
			<img src="" alt="Image" />
		</div>
	</div>
	<script>
		$(document).ready(function() {
			function formatItem(o) {
				if (!o.id)
					return o.text; // optgroup
				else {
					var r = o.text.split(':');
					return "<span> "+r[0]+"</span> <span style='margin-left:10px'>" + r[1] + "</span><span style='display:block;margin-top:5px;'  class='text-danger'><small class='testspanclass'>"+r[2]+"</small></span>";
				}
			}
			$('#branch_from').change(function(){
				$('#sup_from').select2('val',null);
				$('#is_sup').val(0);

			});
			$('#sup_from').change(function(){
				$('#branch_from').select2('val',null);
				$('#is_sup').val(1);
			});
			var ajaxOnProgress = false;
			$("#branch_to,#branch_from").change(function(){
				if($(this).val()){
					if($('#branch_to').val() == $('#branch_from').val()){
						tempToast('error','<p>Invalid branch. Make sure From what branch and To what branch are not the same.</p>','<h3>WARNING!</h3>')
						$(this).select2('val',null);
					}
				}
			});




			$("#branch_from").select2({
				placeholder: 'From branch'
			});
			$("#sup_from").select2({
				placeholder: 'From Supplier'
			});

			//cart

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

			$("#add").click(function() {

				var branch_to = $("#branch_to").val();
				var branch_from;
				if($("#branch_from").val()){
					branch_from = $("#branch_from").val();
				} else if ($("#sup_from").val()){
					branch_from = $("#sup_from").val();
				}
				var item_id = $("#item").val();
				var qty = $("#qty").val();
				var isoncart = false;
				var allqty = 0;
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
				if(branch_from == '' || !branch_to || !item_id || !qty) {
					tempToast('error','<p>Please complete the form first</p>','<h3>WARNING!</h3>');

				} else {
					var sdata =  $('#item').select2('data');
					var item_code =sdata.text;
					var arrcode = item_code.split(':');
					removeNoItemLabel();
					var item_bc = arrcode[0];
					$('#cart > tbody').append("<tr id='" + item_id + "'><td data-title='Barcode'>" + item_bc + "</td><td data-title='Item'>" + arrcode[1] + "<br><small class='text-danger'>"+arrcode[2]+"</small></td><td data-title='Quantity'>"+qty+"</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
				}
				$("#item").select2("val", "");
				$("#qty").val('');
			});

			$('#save').click(function(){
				if($("#cart tbody tr").children().length) {
					var branch_from;
					if($("#branch_from").val()){
						 branch_from = $("#branch_from").val();
					} else if ($("#sup_from").val()){
						branch_from = $("#sup_from").val();
					}
					var branch_to = $('#branch_to').val();
					var is_sup = $('#is_sup').val();
					var foundNoqty =0;
					if(branch_from && branch_to) {
						var toOrder = new Array();

						$('#cart >tbody > tr').each(function(index) {
							var row = $(this);
							var item_id = $(this).prop('id');
							var qty = row.children().eq(2).text();

							if(qty == '' || qty == undefined){
								qty= 0;
							}
							if(isNaN(qty) || qty == 0){
								foundNoqty = parseInt(foundNoqty) + 1;
							}

							toOrder[index] = {
								item_id: item_id, qty: qty
							}
						});
						if(foundNoqty > 0) {
							tempToast('error','<p>Please Indicate the Quantity of the items</p>','<h3>WARNING!</h3>')
						} else {
							$('.loading').show();
							toOrder = JSON.stringify(toOrder);
							if(ajaxOnProgress) {
								return;
							}
							ajaxOnProgress = true;
							$.ajax({
								url: "../ajax/ajax_query.php",
								type: "POST",
								data: {
									toOrder: toOrder,
									branch_from: branch_from,
									branch_to:branch_to,
									is_sup:is_sup,
									functionName:'orderInventory'
								},
								success: function(data) {
									alertify.alert(data,function(){
										location.href = "inv-order.php";
									});
								},
								error: function() {
									// save in local storage
									alert('Saving transaction error');
									ajaxOnProgress = false;
									location.href = "inv-order.php";
								}
							});
						}
					} else {

						tempToast('error','<p>Please choose branches first</p>','<h3>WARNING!</h3>')
					}
				} else {
					tempToast('error','<p>No items in cart</p>','<h3>WARNING!</h3>')
				}
			});
		});
	</script>

<?php require_once '../includes/admin/page_tail2.php'; ?>