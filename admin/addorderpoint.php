<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('orderpoint_m')) {
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
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					<?php echo isset($editid) && !empty($editid) ? "EDIT ORDER POINT" : "ADD ORDER POINT"; ?>
				</h1>
			</div>
			<div class="row">
				<div class="col-md-12">

					<?php
						if(isset($editid) && !empty($editid)) {
							// edit
							$id = Encryption::encrypt_decrypt('decrypt', $editid);
							// get the data base on branch id
							$order_point = new Reorder_point($id);
						}

						// if submitted
						if (Input::exists()){
							// check token if match to our token
							if(Token::check(Input::get('token'))){

								$validation_list = array(
									'item_id' => array(
										'required'=> true,
									),
									'orderby_branch_id' => array(
										'required'=> true
									),
									'orderby_branch_id' => array(
										'notmatches' => 'orderto_branch_id'
									),
								);

								if(isset($id)){
									$validation_list['month'] = array('required'=> true);
									$validation_list['order_point'] = array(
										'required'=> true,
										'isnumber'=> true
									);
									$validation_list['order_qty'] = array(
										'required'=> true,
										'isnumber'=> true
									);
								}


								$validate = new Validate();
								$validate->check($_POST, $validation_list);
								if(isset($id)){
									if($order_point->data()->orderby_branch_id != Input::get('orderby_branch_id')
										|| $order_point->data()->month != Input::get('month')
									){
										$checkOrderPoints = new Reorder_point();
										$checkExistBa = $checkOrderPoints->pointExists(Input::get('item_id'),Input::get('orderby_branch_id'),$user->data()->company_id,Input::get('month'));
										if($checkExistBa->cnt > 0){
											$validate->addError("This order point already exists");
											$validate->_passed = false;
										}
									}
								} else {
									$months = Input::get('chMonths');
									if(!$months) {
										$validate->addError("Please enter atleast one month");
										$validate->_passed = false;
									}
								}

								if($validate->passed()){
									$op = new Reorder_point();
									//edit codes
									if(Input::get('edit')){
										$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
										try{
											if(Input::get('is_supplier') == 1){
												$ordertobranch = 0;
												$ordertosupplier = Input::get('is_supplier');
												$assemble = 0;
											} else if(Input::get('orderto_branch_id') == -1){
												$ordertobranch = 0;
												$ordertosupplier = 0;
												$assemble = 1;
											} else if(Input::get('orderto_branch_id') == -2) {
												$ordertobranch = -2;
												$ordertosupplier =  0;
												$assemble = 0;
											}
											$op->update(array(
												'orderby_branch_id' => Input::get('orderby_branch_id'),
												'for_assemble' => $assemble,
												'orderto_supplier_id' => $ordertosupplier,
												'order_point' => Input::get('order_point'),
												'order_qty' => Input::get('order_qty'),
												'month' => Input::get('month'),
											), $id);
											Session::flash('flash','Order Point information has been successfully updated');
											Redirect::to('orderpoint.php');
										} catch(Exception $e) {
											die($e->getMessage());
										}
									} else {
										// insert codes

											foreach($months as $m){
												try {
													if($m == 13) continue;
													// get item id base on code
													$item_id = Input::get('item_id');
													$op->deleteOrderPoint($item_id,Input::get('orderby_branch_id'),0,$m);
													$orderpointind = Input::get('orderpoint'.$m);
													$orderqtyind = Input::get('orderqty'.$m);
													if(!$orderpointind || !$orderqtyind) continue;
													if(Input::get('is_supplier') == 1){
														$ordertobranch = 0;
														$ordertosupplier = Input::get('orderto_branch_id');
													} else {
														$ordertobranch = Input::get('orderto_branch_id');
														$ordertosupplier =  0;
													}
													$op->create(array(
														'item_id' => $item_id,
														'orderby_branch_id' => Input::get('orderby_branch_id'),
														'orderto_branch_id' => $ordertobranch,
														'orderto_supplier_id' => $ordertosupplier,
														'order_point' => $orderpointind,
														'order_qty' => $orderqtyind,
														'is_active' => 1,
														'month' => $m,
														'company_id' => $user->data()->company_id,
														'created' => strtotime(date('Y/m/d H:i:s')),
														'modified' => strtotime(date('Y/m/d H:i:s')),
													));
												} catch(Exception $e){
													die($e);
												}
											}



										Session::flash('flash','You have successfully added an Order Point');
										Redirect::to('orderpoint.php');
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


							<legend>Order Point Information</legend>

							<div class="form-group">
								<label class="col-md-4 control-label" for="orderby_branch_id">Branch</label>
								<div class="col-md-4">
									<select id="orderby_branch_id" name="orderby_branch_id" class="form-control">
										<option value=''>--Select Branch--</option>
										<?php
											$branch = new Branch();
											$branches =  $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));
											foreach($branches as $b){
												$a = isset($id) ? escape($order_point->data()->orderby_branch_id) : escape(Input::get('orderby_branch_id'));

												if($a==$b->id){
													$selected='selected';
												} else {
													$selected='';
												}
												?>
												<option value='<?php echo $b->id ?>' <?php echo $selected ?>><?php echo escape($b->name);?> </option>
											<?php
											}
											
										?>
									</select>
									<span class="help-block">From what branch</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="orderto_branch_id">Order To</label>
								<div class="col-md-4">
									<input type="hidden" name='is_supplier' id='is_supplier' value='<?php if($id){ echo ($order_point->data()->orderto_branch_id) ? 0: 1; } else { echo 0; }?>'>
									<select id="orderto_branch_id" name="orderto_branch_id" class="form-control">
										<option value=''>--Select Branch/Supplier--</option>
										<?php
											$branch = new Branch();
											$branches =  $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));
											foreach($branches as $b){
												$a = isset($id) ? escape($order_point->data()->orderto_branch_id) : escape(Input::get('orderto_branch_id'));

												if($a==$b->id){
													$selected='selected';
												} else {
													$selected='';
												}
												?>
												<option  data-sup='0' style='padding:5px;' value='<?php echo $b->id ?>' <?php echo $selected ?>><?php echo escape($b->name);?> </option>
											<?php
											}
										?>
											<option data-sup='1' style='' value='1'>Supplier</option>
											<option value='-2'>Other branch</option>
											<option value='-1'><?php echo Configuration::getValue('assemble'); ?></option>

										?>
									</select>
									<span class="help-block">Order to what branch?</span>
								</div>
							</div>

									<?php if($id){
										?>
										<input type="hidden" value='<?php echo $id; ?>' name='item_id'>
										<?php
									} else {
										?>
								<div class="form-group">
									<label class="col-md-4 control-label" for="item_id">Item Code</label>
									<div class="col-md-4">
											<input type="text" style='width:100%;' class='selectitem' id='item_id' name='item_id'>
										<span class="help-block">Item to order</span>
									</div>
								</div>
									<?php
							}
									?>



							<?php if (isset($id)){
							?>

								<div class="form-group">
								<label class="col-md-4 control-label" for="order_point">Order Point</label>
								<div class="col-md-4">
									<input id="order_point" name="order_point" placeholder="Order Point" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($order_point->data()->order_point) : escape(Input::get('order_point')); ?>">
									<span class="help-block">Order point that will trigger automatic ordering</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="order_qty">Order Quantity</label>
								<div class="col-md-4">
									<input id="order_qty" name="order_qty" placeholder="Order Quantity" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($order_point->data()->order_qty) : escape(Input::get('order_qty')); ?>">
									<span class="help-block">Order quantity when inventory reach the order point</span>
								</div>
							</div>
								<div class="form-group">
									<label class="col-md-4 control-label" for="month">Month</label>
									<div class="col-md-4">
										<select class="form-control" name="month" id="month">
											<option value="">--Select Month--</option>
											<option value="13" <?php  if (isset($order_point)) echo($order_point->data()->month==13)? 'selected' : ''; ?>>ALL</option>
											<option value="1" <?php if (isset($order_point)) echo ($order_point->data()->month==1)? 'selected' : ''; ?>>January</option>
											<option value="2" <?php if (isset($order_point)) echo ($order_point->data()->month==2)? 'selected' : ''; ?>>February</option>
											<option value="3" <?php if (isset($order_point)) echo ($order_point->data()->month==3)? 'selected' : ''; ?>>March</option>
											<option value="4" <?php if (isset($order_point)) echo ($order_point->data()->month==4)? 'selected' : ''; ?>>April</option>
											<option value="5" <?php if (isset($order_point)) echo ($order_point->data()->month==5)? 'selected' : ''; ?>>May</option>
											<option value="6" <?php if (isset($order_point)) echo ($order_point->data()->month==6)? 'selected' : ''; ?>>June</option>
											<option value="7" <?php if (isset($order_point)) echo ($order_point->data()->month==7)? 'selected' : ''; ?>>July</option>
											<option value="8" <?php if (isset($order_point)) echo ($order_point->data()->month==8)? 'selected' : ''; ?>>August</option>
											<option value="9" <?php if (isset($order_point)) echo ($order_point->data()->month==9)? 'selected' : ''; ?>>September</option>
											<option value="10" <?php if (isset($order_point)) echo ($order_point->data()->month==10)? 'selected' : ''; ?>>October</option>
											<option value="11" <?php if (isset($order_point)) echo ($order_point->data()->month==11)? 'selected' : ''; ?>>November</option>
											<option value="12" <?php if (isset($order_point)) echo ($order_point->data()->month==12)? 'selected' : ''; ?>>December</option>

										</select>
										<span class="help-block">Apply to what month?</span>
									</div>
								</div>
								<?php
							} else { ?>
							<div class="form-group">
								<h3>Months</h3>

								<div class="col-md-6" style='margin-bottom:5px;'>
									<div class='row'>
										<div class="col-md-4">
										<label class="checkbox-inline" for="jan"><input type="checkbox" class='checkMonth' name='chMonths[]' id='jan' value='1' /> January </label>
										</div>
										<div class="col-md-4">
											<input type='text' disabled class='form-control od' placeholder='Order Point' name='orderpoint1' id='orderpoint1'>
										</div>
										<div class="col-md-4">
											<input type='text' disabled  class='form-control oq' placeholder='Order Quantity' name='orderqty1' id='orderqty1'>
										</div>
									</div>
								</div>
								<div class="col-md-6"  style='margin-bottom:5px;'>
									<div class='row'>
										<div class="col-md-4">
											<label class="checkbox-inline" for="feb"><input type="checkbox" class='checkMonth' name='chMonths[]' id='feb' value='2' /> February </label>
										</div>
										<div class="col-md-4">
											<input type='text' disabled class='form-control od' placeholder='Order Point' name='orderpoint2' id='orderpoint2'>
										</div>
										<div class="col-md-4">
											<input type='text' disabled class='form-control oq' placeholder='Order Quantity' name='orderqty2' id='orderqty2'>
										</div>
									</div>
								</div>

								<div class="col-md-6" style='margin-bottom:5px;'>
									<div class='row'>
										<div class="col-md-4">
											<label class="checkbox-inline" for="mar"><input type="checkbox" class='checkMonth' name='chMonths[]' id='mar' value='3' /> March </label>
										</div>
										<div class="col-md-4">
											<input type='text' disabled class='form-control od' placeholder='Order Point' name='orderpoint3' id='orderpoint3'>
										</div>
										<div class="col-md-4">
											<input type='text' disabled class='form-control oq' placeholder='Order Quantity' name='orderqty3' id='orderqty3'>
										</div>
									</div>
								</div>
								<div class="col-md-6" style='margin-bottom:5px;'>
									<div class='row'>
										<div class="col-md-4">
											<label class="checkbox-inline" for="apr"><input type="checkbox" class='checkMonth' name='chMonths[]' id='apr' value='4' /> April </label>
										</div>
										<div class="col-md-4">
											<input type='text' disabled class='form-control od' placeholder='Order Point' name='orderpoint4' id='orderpoint4'>
										</div>
										<div class="col-md-4">
											<input type='text' disabled class='form-control oq' placeholder='Order Quantity' name='orderqty4' id='orderqty4'>
										</div>
									</div>
								</div>
								<div class="col-md-6" style='margin-bottom:5px;'>
									<div class='row'>
										<div class="col-md-4">
											<label class="checkbox-inline" for="may"><input type="checkbox" class='checkMonth' name='chMonths[]' id='may' value='5' /> May </label>
										</div>
										<div class="col-md-4">
											<input type='text' disabled class='form-control od' placeholder='Order Point' name='orderpoint5'  id='orderpoint5'>
										</div>
										<div class="col-md-4">
											<input type='text' disabled class='form-control oq' placeholder='Order Quantity' name='orderqty5'  id='orderqty5'>
										</div>
									</div>
								</div>
								<div class="col-md-6" style='margin-bottom:5px;'>
									<div class='row'>
										<div class="col-md-4">
											<label class="checkbox-inline" for="jun"><input type="checkbox" class='checkMonth' name='chMonths[]' id='jun' value='6' /> June </label>
										</div>
										<div class="col-md-4">
											<input type='text' disabled class='form-control od' placeholder='Order Point' name='orderpoint6' id='orderpoint6'>
										</div>
										<div class="col-md-4">
											<input type='text' disabled class='form-control oq' placeholder='Order Quantity' name='orderqty6'  id='orderqty6'>
										</div>
									</div>

								</div>
								<div class="col-md-6" style='margin-bottom:5px;'>
									<div class='row'>
										<div class="col-md-4">
											<label class="checkbox-inline" for="jul"><input type="checkbox" class='checkMonth' name='chMonths[]' id='jul' value='7' /> July </label>
										</div>
										<div class="col-md-4">
											<input type='text' disabled class='form-control od' placeholder='Order Point' name='orderpoint7'  id='orderpoint7'>
										</div>
										<div class="col-md-4">
											<input type='text' disabled class='form-control oq' placeholder='Order Quantity' name='orderqty7' id='orderqty7'>
										</div>
									</div>
								</div>
								<div class="col-md-6" style='margin-bottom:5px;'>
									<div class='row'>
										<div class="col-md-4">
											<label class="checkbox-inline" for="aug"><input type="checkbox" class='checkMonth' name='chMonths[]' id='aug' value='8' /> August </label>
										</div>
										<div class="col-md-4">
											<input type='text' disabled class='form-control od' placeholder='Order Point' name='orderpoint8' id='orderpoint8'>
										</div>
										<div class="col-md-4">
											<input type='text' disabled class='form-control oq' placeholder='Order Quantity' name='orderqty8' id='orderqty8'>
										</div>
									</div>

								</div>
								<div class="col-md-6" style='margin-bottom:5px;'>
									<div class='row'>
										<div class="col-md-4">
											<label class="checkbox-inline" for="sep"><input type="checkbox" class='checkMonth' name='chMonths[]' id='sep' value='9' /> September </label>
										</div>
										<div class="col-md-4">
											<input type='text' disabled class='form-control od' placeholder='Order Point' name='orderpoint9' id='orderpoint9'>
										</div>
										<div class="col-md-4">
											<input type='text' disabled class='form-control oq' placeholder='Order Quantity' name='orderqty9' id='orderqty9'>
										</div>
									</div>
								</div>
								<div class="col-md-6" style='margin-bottom:5px;'>
									<div class='row'>
										<div class="col-md-4">
											<label class="checkbox-inline" for="oct"><input type="checkbox" class='checkMonth' name='chMonths[]' id='oct' value='10' /> October </label>
										</div>
										<div class="col-md-4">
											<input type='text' disabled class='form-control od' placeholder='Order Point' name='orderpoint10' id='orderpoint10'>
										</div>
										<div class="col-md-4">
											<input type='text' disabled class='form-control oq' placeholder='Order Quantity' name='orderqty10' id='orderqty10'>
										</div>
									</div>
								</div>
								<div class="col-md-6" style='margin-bottom:5px;'>
									<div class='row'>
										<div class="col-md-4">
											<label class="checkbox-inline" for="nov"><input type="checkbox" class='checkMonth' name='chMonths[]' id='nov' value='11' /> November </label>
										</div>
										<div class="col-md-4">
											<input type='text' disabled class='form-control od' placeholder='Order Point' name='orderpoint11' id='orderpoint11'>
										</div>
										<div class="col-md-4">
											<input type='text' disabled class='form-control oq' placeholder='Order Quantity' name='orderqty11' id='orderqty11'>
										</div>
									</div>
								</div>
								<div class="col-md-6" style='margin-bottom:5px;'>
									<div class='row'>
										<div class="col-md-4">
											<label class="checkbox-inline" for="dec"><input type="checkbox" class='checkMonth' name='chMonths[]' id='dec' value='12' /> December </label>
										</div>
										<div class="col-md-4">
											<input type='text' disabled class='form-control od' placeholder='Order Point' name='orderpoint12' id='orderpoint12'>
										</div>
										<div class="col-md-4">
											<input type='text' disabled class='form-control oq' placeholder='Order Quantity' name='orderqty12' id='orderqty12'>
										</div>
									</div>

								</div>
								<div class="col-md-6">
									<div class='row'>
										<div class="col-md-4">
											<label class="checkbox-inline" for="all"><input type="checkbox" class='checkMonth' name='chMonths[]' id='all' value='13' /> All </label>
										</div>
										<div class="col-md-4">
											<input type='text' disabled class='form-control' placeholder='Order Point' name='orderpoint13' id='orderpoint13'>
										</div>
										<div class="col-md-4">
											<input type='text' disabled class='form-control' placeholder='Order Quantity' name='orderqty13' id='orderqty13'>
										</div>
									</div>

								</div>
							</div>
				<?php } ?>
							<!-- Button (Double) -->
							<div class="form-group">
								<label class="col-md-4 control-label" for="button1id"></label>
								<div class="col-md-8 text-right">
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
				<span style='cursor:pointer; position:absolute;right:2px;top:2px;font-size:1.1em;' class='glyphicon glyphicon-remove-sign removeImage'></span>
				<img src="" alt="Image" />
			</div>
		</div>
	</div> <!-- end page content wrapper-->




	<script>
		$(function(){

			function formatItem(o) {
				if (!o.id)
					return o.text; // optgroup
				else {
					var r = o.text.split(':');
					return "<span> "+r[0]+"</span> <span style='margin-left:10px'>" + r[1] + "</span><span style='display:block;margin-top:5px;'  class='text-danger'><small class='testspanclass'>"+r[2]+"</small></span>";
				}
			}

		});
		$('#orderto_branch_id').change(function(){
			var issup = $('#orderto_branch_id option:selected').attr('data-sup');

			if(issup == 1){
				$('#is_supplier').val('1');
			}    else {
				$('#is_supplier').val('0');
			}

		});

		$(".checkMonth").change(function(){
			var checkitem = $(this).val();
			console.log(checkitem);
			if(checkitem == '13'){
				$(".checkMonth").each(function(){
					var cur_val = $(this).val();
					if(cur_val != '13'){
						$(this).attr('checked',true);
						$('#orderpoint'+cur_val).attr('disabled',false);
						$('#orderqty'+cur_val).attr('disabled',false);
					}
				});
			} else {
				$(".checkMonth").each(function(){
					if($(this).val() == '13'){
						$(this).attr('checked',false);
					}
				});
			}
			if($(this).is(':checked')){
				console.log('checked');
				$('#orderpoint'+checkitem).attr('disabled',false);
				$('#orderqty'+checkitem).attr('disabled',false);
				$('#orderpoint'+checkitem).focus();
			} else {
				console.log('not checked');
				$('#orderpoint'+checkitem).val('');
				$('#orderqty'+checkitem).val('');
				$('#orderpoint'+checkitem).attr('disabled',true);
				$('#orderqty'+checkitem).attr('disabled',true);
			}
		});
		$('body').on('keyup','#orderpoint13',function(){
			$('.od').val($(this).val());
		});
		$('body').on('keyup','#orderqty13',function(){
			$('.oq').val($(this).val());
		});

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>