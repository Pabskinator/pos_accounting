<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('discount_m')) {
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
					<?php echo isset($editid) && !empty($editid) ? "EDIT DISCOUNT" : "ADD DISCOUNT"; ?>
				</h1>
			</div>
			<div class="row">
				<div class="col-md-12">

					<?php
						if(isset($editid) && !empty($editid)) {
							// edit
							$id = Encryption::encrypt_decrypt('decrypt', $editid);
							// get the data base on branch id
							$discount = new Discount($id);
						}

						// if submitted
						if (Input::exists()){
							// check token if match to our token
							if(Token::check(Input::get('token'))){

								$validation_list = array(
									'item_id' => array(
										'required'=> true
									),
									'date_start' => array(
										'required'=> true
									),
									'date_end' => array(
										'required'=> true
									),
									'for_every' => array(
										'required'=> true
									),
									'type' => array(
										'required'=> true
									),
									'branch_id' => array(
										'required'=> true
									)
								);



								$validate = new Validate();
								$validate->check($_POST, $validation_list);
								if($validate->passed()){
									$discount = new Discount();
									//edit codes
									if(Input::get('edit')){
										$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
										try{
											$discount->update(array(
												'item_id' => Input::get('item_id'),
												'date_start' => strtotime(Input::get('date_start')),
												'date_end' => strtotime(Input::get('date_end')),
												'branch_id' => Input::get('branch_id'),
												'amount' => Input::get('amount'),
												'for_every' => Input::get('for_every'),
												'type' => Input::get('type')
											), $id);
											Session::flash('flash','Discount has been successfully updated');
											Redirect::to('discount.php');
										} catch(Exception $e) {
											die($e->getMessage());
										}
									} else {
										// insert codes
										try {
											$discount->create(array(
												'item_id' => Input::get('item_id'),
												'date_start' => strtotime(Input::get('date_start')),
												'date_end' => strtotime(Input::get('date_end')),
												'branch_id' => Input::get('branch_id'),
												'amount' => Input::get('amount'),
												'for_every' => Input::get('for_every'),
												'type' => Input::get('type'),
												'is_active' => 1,
												'company_id' => $user->data()->company_id,
												'created' => time()
											));
										} catch(Exception $e){
											die($e);
										}
										Session::flash('flash','You have successfully added a discount');
										Redirect::to('discount.php');
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


							<legend>Discount Information</legend>


							<div class="form-group">
								<?php 	if(isset($id)){
									$editprod = new Product($discount->data()->item_id);
									?>
									<p class='text-center'><strong><?php echo $editprod->data()->item_code . " " . $editprod->data()->description; ?></strong></p>
									<input id="item_id" name="item_id" placeholder="Item" class="" type="hidden" value="<?php echo  escape($discount->data()->item_id); ?>">
									<?php
								} else {
									?>
									<label class="col-md-4 control-label" for="item_id">Item</label>
									<div class="col-md-4">
										<input id="item_id" name="item_id" placeholder="Item" class="form-control input-md selectitem" type="text" value="<?php echo isset($id) ? escape($discount->data()->item_id) : escape(Input::get('item_id')); ?>">
										<span class="help-block"></span>
									</div>
								<?php
								}?>

							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="branch_id">Branch</label>
								<div class="col-md-4">
									<select id="branch_id" name="branch_id" class="form-control">
										<option value=''>--Select Branch--</option>
										<?php
											$branch = new Branch();
											$branches =  $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));
											foreach($branches as $b){
												$a = isset($id) ? $discount->data()->branch_id : escape(Input::get('branch_id'));

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
									<span class="help-block"></span>
								</div>
							</div>
							<!-- Text input-->
							<div class="form-group">
								<label class="col-md-4 control-label" for="date_start">Date Start</label>
								<div class="col-md-4">
									<input id="date_start" name="date_start" placeholder="Date Start" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape(date('m/d/Y',$discount->data()->date_start)) :  escape(Input::get('date_start')); ?>">
									<span class="help-block"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="date_end">Date End</label>
								<div class="col-md-4">
									<input id="date_end" name="date_end" placeholder="Date End" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape(date('m/d/Y',$discount->data()->date_end)) :  escape(Input::get('date_end')); ?>">
									<span class="help-block"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="amount">Amount</label>
								<div class="col-md-4">
									<input id="amount" name="amount" placeholder="Amount" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($discount->data()->amount) :  escape(Input::get('amount')); ?>">
									<span class="help-block"></span>
								</div>
							</div>

							<div class="form-group">
								<label class="col-md-4 control-label" for="for_every">Quantity</label>
								<div class="col-md-4">
									<input id="for_every" name="for_every" placeholder="Quantity" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($discount->data()->for_every) :  escape(Input::get('for_every')); ?>">
									<span class="help-block"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="type">Type</label>
								<div class="col-md-4">
									<select name="type" id="type" class='form-control'>
										<option value=""></option>
										<option value="1"
											<?php
												if(isset($id)){
													echo (isset($discount->data()->type) && $discount->data()->type == 1) ? ' selected' : '';
												}
											?>
											>For every</option>
										<option value="2"
											<?php
												if(isset($id)){
													echo (isset($discount->data()->type) && $discount->data()->type == 2) ? ' selected' : '';
												}
											?>
											>Above</option>
									</select>
									<span class="help-block"></span>
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
		</div>
	</div> <!-- end page content wrapper-->
	<script>
		$(function(){
			$('#date_start').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#date_start').datepicker('hide');
			});
			$('#date_end').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#date_end').datepicker('hide');
			});
		});
	</script>

<?php require_once '../includes/admin/page_tail2.php'; ?>