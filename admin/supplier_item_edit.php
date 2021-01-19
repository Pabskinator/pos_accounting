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
			<div style='margin:5px;' class="btn-group" role="group" aria-label="...">
				<a class='btn btn-default' href='supplier.php'>
					Supplier
				</a>
				<a class='btn btn-default' href='addsupplier.php'>
					Add Supplier
				</a>
				<a class='btn btn-default'  href='supplier_receive_order.php'>
					Order List
				</a>
				<a class='btn btn-default'  href='supplier_order.php'>
					Order item
				</a>
				<a class='btn btn-default'  href='supplier_item.php'>
					Supplier Item
				</a>
				<a class='btn btn-default'  href='supplier_item_add.php'>
					Add Supplier Item
				</a>
			</div>
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

					<form class="form-horizontal" action="" method="POST">
						<fieldset>


							<legend>Item Information</legend>
							<?php
								$supplier = new Supplier();
								$suppliers = $supplier->get_active('suppliers',array('company_id' ,'=',$user->data()->company_id));
								$cur_item = new Product();
								$cur_items = $cur_item->get_active('items',array('company_id' ,'=',$user->data()->company_id));

							?>
							<div class="form-group">
								<label class="col-md-4 control-label" for="supplier_id">Supplier</label>
								<div class="col-md-4">
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
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="item_id">Item</label>
								<div class="col-md-4">
									<select name="item_id" id="item_id" class='form-control'>
										<option value=""></option>
										<?php
											foreach($cur_items as $i):
												if($i->item_type != -1) continue;
												$a = isset($id) ? $item->data()->item_id : escape(Input::get('item_id'));
												if($a==$i->id){
													$selected='selected';
												} else {
													$selected='';
												}
												?>

												<option value="<?php echo $i->id; ?>" <?php echo $selected; ?>><?php echo $i->barcode . ":" .$i->item_code . ":" .  $i->description; ?></option>
											<?php endforeach;?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="item_code">Item Code</label>
								<div class="col-md-4">
									<input id="item_code" name="item_code" placeholder="Item code" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($item->data()->item_code) : escape(Input::get('item_code')); ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="description">Description</label>
								<div class="col-md-4">
									<input id="description" name="description" placeholder="Description" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($item->data()->description) : escape(Input::get('description')); ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="purchase_price">Purchase Price</label>
								<div class="col-md-4">
									<input id="purchase_price" name="purchase_price" placeholder="Purchase Price" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($item->data()->purchase_price) : escape(Input::get('purchase_price')); ?>">
								</div>
							</div>

							<div class="form-group">
								<label class="col-md-4 control-label" for="min_qty">Min Quantity</label>
								<div class="col-md-4">
									<input id="min_qty" name="min_qty" placeholder="Min Quantity" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($item->data()->min_qty) : escape(Input::get('min_qty')); ?>">
								</div>
							</div>

							<!-- Button (Double) -->
							<div class="form-group">
								<label class="col-md-4 control-label" for="button1id"></label>
								<div class="col-md-8">
									<input type='submit' class='btn btn-success' name='btnSave' value='SAVE'/>
									<input type='hidden' name='token' value=<?php echo Token::generate(); ?>>
									<input type='hidden' name='edit' value=<?php echo isset($id) ? escape(Encryption::encrypt_decrypt('encrypt',$id)): 0; ?>>

								</div>
							</div>

						</fieldset>
					</form>
				</div>

			</div>
			<div id="imagecon">
				<img src="" alt="Image" />
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

			$("#item_id").select2({
				placeholder: 'Choose Item',
				allowClear: true,
				formatResult: formatItem,
				formatSelection: formatItem,
				escapeMarkup: function(m) {
					return m;
				}
			}).on("select2-close", function(e) {
				// fired to the original element when the dropdown closes

				setTimeout(function() {
					$('#imagecon').fadeOut();
				}, 300);
			}).on("select2-highlight", function(e) {
				console.log("highlighted val=" + e.val + " choice=" + e.choice.text);
				var itemid =  e.choice.id;
				var itemjpg = itemid +".jpg";
				var opt = $(this);

				$.ajax({
					url:'../item_images/'+itemjpg,
					type:'HEAD',
					error: function()
					{
						$('#imagecon').fadeOut();
					},
					success: function()
					{
						$('#imagecon  img').attr('src','../item_images/'+itemjpg);
						$('#imagecon').fadeIn();
					}
				});

			});
		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>