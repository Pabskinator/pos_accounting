<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('inventory')) {
		// redirect to denied page
		Redirect::to(1);
	}

	if(isset($_GET['edit'])) {
		$editid = $_GET['edit'];
	} else {
		$editid = 0;
	}


?>

	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<?php include 'includes/supplier_nav.php'; ?>
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					<?php echo isset($editid) && !empty($editid) ? "EDIT SUPPLIER ITEM" : "ADD SUPPLIER ITEM"; ?>
				</h1>
			</div>
			<div class="row">
				<div class="col-md-12">

					<?php
						if(isset($editid) && !empty($editid)) {
							// edit
							$id = Encryption::encrypt_decrypt('decrypt', $editid);
							// get the data base on branch id
							$item = new Supplier_item($id);
						}

						// if submitted
						if (Input::exists()){
							// check token if match to our token
							if(Token::check(Input::get('token'))){

								$validation_list = array(
									'item_code' => array(
										'required'=> true,
										'max' => 50
									),
									'description' => array(
										'required'=> true,
										'max' => 100
									),
									'purchase_price' => array(
										'required'=> true,
										'isnumber' => true
									),
									'min_qty' => array(
										'required'=> true,
										'isnumber' => true
									),
									'supplier_id' => array(
										'required'=> true
									)
								);
								// get id in update


								$validate = new Validate();
								$validate->check($_POST, $validation_list);
								if($validate->passed()){
									$itemcls = new Supplier_item();
									//edit codes
									if(Input::get('edit')){
										$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
										try{

											$itemcls->update(array(
												'item_code' => Input::get('item_code'),
												'description' => Input::get('description'),
												'min_qty' => Input::get('min_qty'),
												'purchase_price' => Input::get('purchase_price'),
												'supplier_id' => Input::get('supplier_id'),
												'is_active' => 1,
												'modified' => time()
											), $id);

											Log::addLog($user->data()->id,$user->data()->company_id,"Update Supplier Item ". Input::get('item_code'),"supplier_item_add.php");


											Session::flash('flash','Supplier item has been successfully updated');
											Redirect::to('supplier_item.php');
										} catch(Exception $e) {
											die($e->getMessage());
										}
									} else {
										// insert codes
										$isExists = $itemcls->checkIfItemOnSupExists($user->data()->company_id,Input::get('supplier_id'),Input::get('item_id'));

										if($isExists->cnt > 0){
											echo "<div class='alert alert-danger'>Item Already Exists</div>";
										} else {
											try {
											$itemcls->create(array(
													'item_code' => Input::get('item_code'),
													'description' => Input::get('description'),
													'min_qty' => Input::get('min_qty'),
													'purchase_price' => Input::get('purchase_price'),
													'supplier_id' => Input::get('supplier_id'),
													'is_active' => 1,
													'created' => time(),
													'modified' => time(),
													'company_id' => $user->data()->company_id,
													'item_id' => Input::get('item_id')
												));
												Log::addLog($user->data()->id,$user->data()->company_id,"Add Supplier Item ". Input::get('item_code'),"supplier_item_add.php");

											} catch(Exception $e){
												die($e);
											}
											Session::flash('flash','You have successfully added an item');
											Redirect::to('supplier_item.php');
										}
									}
								} else {
									$el ='';
									echo "<div class='alert alert-danger'>";
									foreach($validate->errors() as $error){
										$el.= escape($error) . "<br/>" ;
									}
									echo "$el</div>";
								}
							}
						}
					?>


							<?php
								$supplier = new Supplier();
								$suppliers = $supplier->get_active('suppliers',array('company_id' ,'=',$user->data()->company_id));

							?>
							<div class="row">

								<div class="col-md-3">
									<strong>Supplier</strong>
									<select name="supplier_id" id="supplier_id" class='form-control'>
										<option value=""></option>
										<?php
											foreach($suppliers as $sup):
												$a = isset($id) ? $item->data()->supplier_id : escape(Input::get('supplier_id'));

												if($a==$sup->id){
													$selected='selected';
												} else {
													$selected='';
												}
											?>
											<option value="<?php echo $sup->id; ?>" <?php echo $selected; ?>><?php echo $sup->name; ?></option>
										<?php endforeach;?>
									</select>
								</div>
								<div class="col-md-3">
									<strong><?php echo $thiscompany->name; ?> Item</strong>
									<input name="item_id" id="item_id" class='selectitem'>

								</div>
								<div class="col-md-3">
									<strong>Supplier Item</strong>
									<input id="item_code" name="item_code" placeholder="Item code" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($item->data()->item_code) : escape(Input::get('item_code')); ?>">
								</div>
								<div class="col-md-3">
									<strong>Supplier Item Description</strong>
									<input id="description" name="description" placeholder="Description" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($item->data()->description) : escape(Input::get('description')); ?>">
								</div>
							</div>
							<div class="row">

								<div class="col-md-3">
									<strong>Purchase Price</strong>
									<input id="purchase_price" name="purchase_price" placeholder="Purchase Price" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($item->data()->purchase_price) : escape(Input::get('purchase_price')); ?>">
								</div>
								<div class="col-md-3">
									<strong>Min Quantity</strong>
									<input id="min_qty" name="min_qty" placeholder="Min Quantity" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($item->data()->min_qty) : escape(Input::get('min_qty')); ?>">
								</div>
								<div class="col-md-3">
									<br>
									<button id='additem' class='btn btn-default'>ADD ITEM</button>
								</div>
							</div>
				</div>

				<div class="container-fluid">
					<br>
					<div class="panel panel-default">
						<div class="panel-body">
							<div id="no-more-tables">
							<table class="table" id='cart'>
								<thead>
								<tr>
									<th>Supplier</th>
									<th><?php echo $thiscompany->name; ?> Item</th>
									<th>Supplier Item</th>
									<th>Supplier Description</th>
									<th>Purchase Price</th>
									<th>Minimum Order Quantity</th>
									<th></th>
								</tr>
								</thead>
								<tbody>

								</tbody>
							</table>
							</div>
							<div class="text-right">
								<button class='btn btn-default' id='btnSave'>SAVE</button>
							</div>
						</div>
					</div>
				</div>
				<hr>
			</div>
			<div class="hidden-xs">
			<div id="imagecon">
				<img src="" alt="Image" />
			</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->

	<script>
		$(function(){
			$("#supplier_id").select2({
				placeholder: 'Choose Supplier',
				allowClear: true
			});
			function formatItem(o) {
				if (!o.id)
					return o.text; // optgroup
				else {
					var r = o.text.split(':');
					return "<span> "+r[0]+"</span> <span style='margin-left:10px'>" + r[1] + "</span><span style='display:block;margin-top:5px;'  class='text-danger'><small class='testspanclass'>"+r[2]+"</small></span>";
				}
			}



			$("#item_id").change(function(){
					var data = $('#item_id').select2('data');
					var opttxt = data.text;
					var arr = opttxt.split(':');
					var itemcode = arr[1];
					var desc = arr[2];
					$('#item_code').val(itemcode);
					$('#description').val(desc);
			});

			$('#item_code').click(function(){
					$(this).select();
			});
			$('#description').click(function(){
				$(this).select();
			});
			noItemInCart();
			function noItemInCart() {
				if(!$("#cart tbody").children().length) {
					$("#cart tbody").append("<td data-title='Remarks' colspan='4' id='noitem' style='padding-top:10px;' ><span class='text-danger'>NO ITEMS IN CART</span></td>");
				}
			}
			function removeNoItemLabel() {
				$("#noitem").remove();
			}
			$('body').on('click', '.removeItem', function() {
				$(this).parents('tr').remove();
				noItemInCart();
			});
			$('#additem').click(function(e){
				e.preventDefault();
				var supcon =  $('#supplier_id');
				var supplier_id =supcon.val();
				var supname= $('#supplier_id option:selected').text();
				var item_id =  $('#item_id').val();
				var itemdata = $('#item_id').select2('data');
				var itemopt = itemdata.text;
				var itemarr = itemopt.split(':');
				var item_code = itemarr[1];
				var item_desc = itemarr[2];
				var s_item_code = $('#item_code').val();
				var s_description = $('#description').val();
				var purchase_price = $('#purchase_price').val();
				var min_qty = $('#min_qty').val();
				if(!supplier_id || !item_id || !min_qty || !s_description || !s_item_code || !purchase_price){
					alertify.alert('Please complete the form first');
					return;
				}
				if($('#'+ supplier_id + item_id).length > 0){
					alertify.alert('Item already in cart');
					return;
				}
				$('#cart > tbody').append("<tr data-purchase_price='"+purchase_price+"' data-min_qty='"+min_qty+"'  data-supplier_id='"+supplier_id+"' data-item_id='"+item_id+"' id='" + supplier_id + item_id+"'><td data-title='Supplier'>"+supname+"</td><td  data-title='Item'>" + item_code + "<br><small class='text-danger'>"+item_desc+"</small></td><td  data-title='Supplier Item'>"+s_item_code+"</td><td  data-title='Supplier Desc'>"+s_description+"</td><td  data-title='Price'>" + number_format(purchase_price,2) + "</td><td  data-title='Min qty'>"+number_format(min_qty)+"</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
				removeNoItemLabel();
				supcon.select2('val',null);
				$('#item_id').select2('val',null);
				$('#item_code').val('');
				$('#description').val('');
				$('#purchase_price').val('');
				$('#min_qty').val('');
			});
			$('#btnSave').click(function(){
				if($("#cart tbody tr").children().length) {
					var arr = new Array();
					$('#cart > tbody > tr').each(function(){
						var row= $(this);
						var item_id = row.attr('data-item_id');
						var supplier_id = row.attr('data-supplier_id');
						var item_code = row.children().eq(2).text();
						var description = row.children().eq(3).text();
						var min_qty =  row.attr('data-min_qty');
						var purchase_price =  row.attr('data-purchase_price');
						arr.push({
							supplier_id:supplier_id,
							item_id:item_id,
							item_code:item_code,
							description:description,
							min_qty:min_qty,
							purchase_price:purchase_price
						});
					});
					if(arr.length > 0){
						arr = JSON.stringify(arr);
						$.ajax({
						    url:'../ajax/ajax_query2.php',
						    type:'post',
						    data: {functionName:'saveSupplierItem',jsondata:arr},
						    success: function(data){
						        alertify.alert(data);
							    location.href='supplier_item_add.php';
						    },
						    error:function(){

						    }
						})
					}
				} else {
					alertify.alert('No item in cart');
				}
			});


		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>