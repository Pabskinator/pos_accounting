<?php
	// $user have all the properties and method of the current user
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('member')) {
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
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span> <?php echo isset($editid) && !empty($editid) ? "Edit Affiliate"  : "Add Affiliate"; ?>
				</h1>
			</div>
			<div class="row">
				<div class="col-md-12">

					<?php

						if(isset($editid) && !empty($editid)) {
							// edit
							$id = Encryption::encrypt_decrypt('decrypt', $editid);
							// get the data base on branch id
							$edit_affiliate = new Affiliate($id);
						}

						// if submitted
						if(Input::exists()) {

							// check token if match to our token
							if(Token::check(Input::get('token'))) {
								$validation_list = array(
									'name' => array('required' => true, 'max' => 50),
									'description' => array('max' => 50),
									'street_no' => array('max' => 50),
									'brgy' => array('max' => 50),
									'city' => array('max' => 50),
									'province' => array('max' => 50),
									'region' => array('max' => 200),
									'lat_long' => array('max' => 50)
								);
								if(!Input::get('edit')) {
									$additionalvalidation = array('unique' => 'affiliates');
									$finalvalidation=array_merge($validation_list['name'],$additionalvalidation);
									$validation_list['name'] = $finalvalidation;

								}
								$validate = new Validate();
								$validate->check($_POST, $validation_list);

								if($validate->passed()) {
									$affiliate = new Affiliate();
									if(Input::get('edit')) {
										$id = Encryption::encrypt_decrypt('decrypt', Input::get('edit'));
										try {

											$affiliate->update(
												array('name' => Input::get('name'),
													'description' => Input::get('description'),
													'street_no' => Input::get('street_no'),
													'brgy' => Input::get('brgy'),
													'city' => Input::get('city'),
													'province' => Input::get('province'),
													'region' => Input::get('region'),
													'email' => Input::get('email'),
													'lat_long' => Input::get('lat_long'),
													'current_wallet' => Input::get('current_wallet')
												), $id);
											Session::flash('flash', 'Affiliate information has been successfully updated');
											Redirect::to('affiliates.php');
										} catch(Exception $e) {
											die($e->getMessage());
										}
									} else {
										// insert codes
										try {
											$affiliate_new = array(
												'name' => Input::get('name'),
												'description' => Input::get('description'),
												'street_no' => Input::get('street_no'),
												'brgy' => Input::get('brgy'),
												'city' => Input::get('city'),
												'province' => Input::get('province'),
												'region' => Input::get('region'),
												'lat_long' => Input::get('lat_long'),
												'current_wallet' => Input::get('current_wallet'),
												'email' => Input::get('email'),
												'company_id' => $user->data()->company_id,
												'is_active' => 1,
												'created' => strtotime(date('Y/m/d H:i:s')),
												'security_code' => Input::get('security_code')
											);
											$affiliate->create($affiliate_new);

										} catch(Exception $e) {
											die($e);
										}
										Session::flash('flash', 'You have successfully added affiliate ');
										Redirect::to('affiliates.php');
									}

								} else {
									$el = '';
									echo "<div class='alert alert-danger'>";
									foreach($validate->errors() as $error) {
										$el .= escape($error) . "<br/>";
									}
									echo "$el</div>";
								}
							}
						}
						$digits = 4;
						$auto_generated_security_code = rand(pow(10, $digits-1), pow(10, $digits)-1);
					?>

					<form  action="" method="POST">
						<?php if(!isset($id)){
							?>
							<h4>Auto-generated security code: <strong class='text-danger'><?php echo $auto_generated_security_code; ?></strong></h4>
							<input type="hidden" value='<?php echo $auto_generated_security_code; ?>' name='security_code'>
							<?php
						} ?>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									Name
									<input id="name" name="name" placeholder="Name" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($edit_affiliate->data()->name) : escape(Input::get('name')); ?>">
									<span class="help-block"></span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									Email
									<input id="email" name="email" placeholder="Email" class="form-control input-md" type="email" value="<?php echo isset($id) ? escape($edit_affiliate->data()->email) : escape(Input::get('email')); ?>">
									<span class="help-block"></span>
								</div>
							</div>
							<div class="col-md-12">
								<div class="form-group">
									Description
									<input id="description" name="description" placeholder="Description" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($edit_affiliate->data()->description) : escape(Input::get('description')); ?>">
									<span class="help-block"></span>
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group">
									Street #/ Lot #
									<input  id="street_no" name="street_no" placeholder="Street #/ Lot #" class="form-control" type="text" value="<?php echo isset($id) ? $edit_affiliate->data()->street_no : escape(Input::get('street_no')); ?>">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									Barangay
									<input  id="brgy" name="brgy" placeholder="Barangay" class="form-control" type="text" value="<?php echo isset($id) ? $edit_affiliate->data()->brgy : escape(Input::get('brgy')); ?>">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									City
									<input  id="city" name="city" placeholder="City" class="form-control" type="text" value="<?php echo isset($id) ? $edit_affiliate->data()->city : escape(Input::get('city')); ?>">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									Province
									<input  id="province" name="province" placeholder="Province" class="form-control" type="text" value="<?php echo isset($id) ? $edit_affiliate->data()->province : escape(Input::get('province')); ?>">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									Region 
									<select name="region" id="region" class='form-control'>
										<?php echo getRegionOpt(isset($id) ? $edit_affiliate->data()->region : escape(Input::get('region'))); ?>
									</select>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									Current Wallet Value
									<input  id="current_wallet" name="current_wallet" placeholder="Value" class="form-control" type="text" value="<?php echo isset($id) ? $edit_affiliate->data()->current_wallet : escape(Input::get('current_wallet')); ?>">
								</div>
							</div>


						<div class="form-group">
							<div class="col-md-8">
								<input type='submit' class='btn btn-success' name='btnSave' value='SAVE' />
								<input type='hidden' name='token' value=<?php echo Token::generate(); ?>>
								<input type='hidden' name='edit' value=<?php echo isset($id) ? escape(Encryption::encrypt_decrypt('encrypt', $id)) : 0; ?>>
							</div>
						</div>

					</form>
				</div>

			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<script>
		$(function(){

		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>