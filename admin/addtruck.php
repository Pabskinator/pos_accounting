<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('truck')) {
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
					<?php echo isset($editid) && !empty($editid) ? "EDIT TRUCK" : "ADD TRUCK"; ?>
				</h1>
			</div>
			<div class="row">
				<div class="col-md-12">

					<?php
						if(isset($editid) && !empty($editid)) {
							// edit
							$id = Encryption::encrypt_decrypt('decrypt', $editid);
							// get the data base on branch id
							$truck = new Truck($id);
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
										'max' => 150
									),
									'cbm' => array(
										'max' => 120
									)

								);



								$validate = new Validate();
								$validate->check($_POST, $validation_list);
								if($validate->passed()){
									$truck = new Truck();
									//edit codes
									if(Input::get('edit')){
										$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
										try{

											$truck->update(array(
												'name' => Input::get('name'),
												'modified' => strtotime(date('Y/m/d H:i:s')),
												'description' => Input::get('description'),
												'cbm' => Input::get('cbm')
											), $id);
											Session::flash('flash','Truck information has been successfully updated');
											Redirect::to('truck.php');
										} catch(Exception $e) {
											die($e->getMessage());
										}
									} else {
										// insert codes
										try {

												$truck->create(array(
													'name' => Input::get('name'),
													'created' => strtotime(date('Y/m/d H:i:s')),
													'company_id' => $user->data()->company_id,
													'modified' => strtotime(date('Y/m/d H:i:s')),
													'is_active' => 1,
													'description' => Input::get('description'),
													'cbm' => Input::get('cbm')
												));

										} catch(Exception $e){
											die($e);
										}
										Session::flash('flash', 'You have successfully added a truck');
										Redirect::to('truck.php');

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


							<legend>Truck Information</legend>


							<div class="form-group">
								<label class="col-md-4 control-label" for="name">Truck Name</label>
								<div class="col-md-4">
									<input id="name" name="name" placeholder="Name" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($truck->data()->name) : escape(Input::get('name')); ?>">
									<span class="help-block">Alpha numeric, maximum of 50 characters</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="description">Description</label>
								<div class="col-md-4">
									<input id="description" name="description" placeholder="Description" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($truck->data()->description) : escape(Input::get('description')); ?>">
									<span class="help-block">Alpha numeric, maximum of 150 characters</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="cbm">CBM</label>
								<div class="col-md-4">
									<input id="cbm" name="cbm" placeholder="CBM " class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($truck->data()->cbm) : escape(Input::get('cbm')); ?>">
									<span class="help-block">Optional</span>
								</div>
							</div>
							<div class="form-group">

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