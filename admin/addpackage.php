<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('package')) {
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
					<?php echo isset($editid) && !empty($editid) ? "EDIT PACKAGE" : "ADD PACKAGE"; ?>
				</h1>
			</div>
			<div class="row">
				<div class="col-md-12">

					<?php
						if(isset($editid) && !empty($editid)) {
							// edit
							$id = Encryption::encrypt_decrypt('decrypt', $editid);

							$package = new Package($id);
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
									)
								);
								// get id in update

								if(!Input::get('edit')) {
									$additionalvalidation = array('unique' => 'packages');
									$finalvalidation=array_merge($validation_list['name'],$additionalvalidation);
									$validation_list['name'] = $finalvalidation;
								}

								$validate = new Validate();
								$validate->check($_POST, $validation_list);
								if($validate->passed()){
									$package = new Package();
									//edit codes
									if(Input::get('edit')){
										$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
										try{
											$package->update(array(
												'name' => Input::get('name'),
												'description' => Input::get('description')
											), $id);
											Session::flash('flash','Package information has been successfully updated');
											Redirect::to('package.php');
										} catch(Exception $e) {
											die($e->getMessage());
										}
									} else {
										// insert codes
										try {
											$package->create(array(
												'name' => Input::get('name'),
												'description' => Input::get('description'),
												'created' => strtotime(date('Y/m/d H:i:s')),
												'modified' => strtotime(date('Y/m/d H:i:s')),
												'company_id' => $user->data()->company_id,
												'is_active' => 1
											));
										} catch(Exception $e){
											die($e);
										}
										Session::flash('flash','You have successfully added a package');
										Redirect::to('package.php');
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


							<legend>Package Information</legend>


							<div class="form-group">
								<label class="col-md-4 control-label" for="name">Package</label>
								<div class="col-md-4">
									<input id="packageName" name="name" placeholder="Package Name" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($package->data()->name) : escape(Input::get('name')); ?>">
									<span class="help-block">Package name</span>
								</div>
							</div>

							<!-- Text input-->
							<div class="form-group">
								<label class="col-md-4 control-label" for="d">Package Description</label>
								<div class="col-md-4">
									<input id="packageDescription" name="description" placeholder="Description" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($package->data()->description) :  escape(Input::get('description')); ?>">
									<span class="help-block">Description for your package</span>
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