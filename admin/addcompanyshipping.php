<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('ship_m')) {
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
					<?php echo isset($editid) && !empty($editid) ? "EDIT SHIPPING COMPANY" : "ADD SHIPPING COMPANY"; ?>
				</h1>
			</div>
			<div class="row">
				<div class="col-md-12">

					<?php
						if(isset($editid) && !empty($editid)) {
							// edit
							$id = Encryption::encrypt_decrypt('decrypt', $editid);
							// get the data base on branch id
							$shipping_company = new Shipping_company($id);
						}

						// if submitted
						if (Input::exists()){
							// check token if match to our token
							if(Token::check(Input::get('token'))){

								$validation_list = array(
									'name' => array(
										'required'=> true,
										'max' => 50
									),
									'description' => array(
										'required'=> true,
										'min' => 6,
										'max'=>200
									),
									'address' => array(
										'required'=> true,
										'min' => 6,
										'max'=>200
									)
								);
								// get id in update



								$validate = new Validate();
								$validate->check($_POST, $validation_list);
								if($validate->passed()){
									$shipping_company = new Shipping_company();
									//edit codes
									if(Input::get('edit')){
										$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
										try{
											$arrupdate = array(
												'name' => Input::get('name'),
												'description' => Input::get('description'),
												'address' => Input::get('address')
											);

											$shipping_company->update($arrupdate, $id);
											Session::flash('flash','Shipping information has been successfully updated');
											Redirect::to('shipping-company.php');
										} catch(Exception $e) {
											die($e->getMessage());
										}
									} else {
										// insert codes
										try {
											$inserarr = array(
												'name' => Input::get('name'),
												'description' => Input::get('description'),
												'address' => Input::get('address'),
												'created' => strtotime(date('Y/m/d H:i:s')),
												'modified' => strtotime(date('Y/m/d H:i:s')),
												'company_id' => $user->data()->company_id,
												'is_active' => 1
											);
											$shipping_company->create($inserarr);
										} catch(Exception $e){
											die($e);
										}
										Session::flash('flash','You have successfully added a shipping company');
										Redirect::to('shipping-company.php');
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


							<legend>Shipping company Information</legend>


							<div class="form-group">
								<label class="col-md-4 control-label" for="name">Name</label>
								<div class="col-md-4">
									<input id="" name="name" placeholder="Shipping Company" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($shipping_company->data()->name) : escape(Input::get('name')); ?>">
									<span class="help-block">The name of the shipping company</span>
								</div>
							</div>

							<!-- Text input-->
							<div class="form-group">
								<label class="col-md-4 control-label" for="">Description</label>
								<div class="col-md-4">
									<input id="" name="description" placeholder="Description" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($shipping_company->data()->description) :  escape(Input::get('description')); ?>">
									<span class="help-block">Description for the company</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="address">Address</label>
								<div class="col-md-4">
									<input id="address" name="address" placeholder="Address" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($shipping_company->data()->address) :  escape(Input::get('address')); ?>">
									<span class="help-block">Address/Location of the company</span>
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


<?php require_once '../includes/admin/page_tail2.php'; ?>