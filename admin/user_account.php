<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('dashboard')) {
		// redirect to denied page
		Redirect::to(1);
	}


?>


	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					User account
				</h1>
			</div>
			<div class="row">
				<div class="col-md-12">
					<?php
								// get flash message if add or edited successfully
								if(Session::exists('flash')){
									echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('flash')."</div>";
								}
					?>
					<?php
						

						// if submitted
						if (Input::exists()){
							// check token if match to our token
							if(Token::check(Input::get('token'))){
								$validation_list = array(
									'lastname' => array(
										'required'=> true,
										'max' => 50
									),
									'firstname' => array(
										'required'=> true,
										'max' => 50
									),
									'middlename' => array(
										'required'=> true,
										'max' => 50
									),
									'new_password' => array(
										'min' => 6
									),
									'retype_new_password' => array(
										'matches' => 'new_password'
									)
								);
							
								$validate = new Validate();
								$validate->check($_POST, $validation_list);
								if($validate->passed()){
									$curp = Input::get('cur_password');
									$newp = Input::get('new_password');
									if(isset($curp) && !empty($curp) && isset($newp) && !empty($newp)){

									if(trim(Hash::make($curp)) == trim($user->data()->password)){
										$user->update(
											array(
												'lastname' => Input::get('lastname'),
												'firstname' => Input::get('firstname'),
												'middlename' => Input::get('middlename'),
												'password' => Hash::make($newp),
											),$user->data()->id);
										Session::flash('flash','User information has been successfully updated');
										Redirect::to('user_account.php');
									}  else {
										
										echo "<p class='text-danger'>Current Password is incorrect</p>";
									} 

								} else {
									$user->update(
											array(
												'lastname' => Input::get('lastname'),
												'firstname' => Input::get('firstname'),
												'middlename' => Input::get('middlename')
											),$user->data()->id);
										Session::flash('flash','User information has been successfully updated');
										Redirect::to('user_account.php');
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

							<hr>
		<p>User Information</p>

							<div class="form-group">
								<label class="col-md-4 control-label" for="username">User Name</label>
								<div class="col-md-4">
								<input disabled id="username" name="username" placeholder="User Name" class="form-control input-md" type="text" value="<?php echo $user->data()->username; ?>">
								
								</div>
							</div>

							<div class="form-group">
								<label class="col-md-4 control-label" for="lastname">Last Name</label>
								<div class="col-md-4">
								<input  id="lastname" name="lastname" placeholder="Last Name" class="form-control input-md" type="text" value="<?php echo $user->data()->lastname; ?>">
								
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="firstname">First Name</label>
								<div class="col-md-4">
								<input  id="firstname" name="firstname" placeholder="First Name" class="form-control input-md" type="text" value="<?php echo $user->data()->firstname; ?>">
								
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="middlename">Middle Name</label>
								<div class="col-md-4">
								<input  id="middlename" name="middlename" placeholder="Middle Name" class="form-control input-md" type="text" value="<?php echo $user->data()->middlename; ?>">
								
								</div>
							</div>

							<hr>
							<p>Change Password</p>
							<div class="form-group">
								<label class="col-md-4 control-label" for="cur_password">Current Password</label>
								<div class="col-md-4">
								<input  id="cur_password" name="cur_password" placeholder="Current Password" class="form-control input-md" type="password" value="">
								
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="new_password">New Password</label>
								<div class="col-md-4">
								<input  id="new_password" name="new_password" placeholder="New Password" class="form-control input-md" type="password" value="">
							
								</div>
							</div>
								<div class="form-group">
								<label class="col-md-4 control-label" for="new_password">Retype New Password</label>
								<div class="col-md-4">
								<input  id="retype_new_password" name="retype_new_password" placeholder="Retype New Password" class="form-control input-md" type="password" value="">
							
								</div>
							</div>
							<!-- Button (Double) -->
							<div class="form-group">
								<label class="col-md-4 control-label" for="button1id"></label>
								<div class="col-md-8">
									<input type='submit' class='btn btn-success' name='btnSave' value='SAVE'/>
									<input type='hidden' name='token' value=<?php echo Token::generate(); ?>>
								

								</div>
							</div>

						</fieldset>
					</form>
				</div>

			</div>
		</div>
	</div> <!-- end page content wrapper-->


<?php require_once '../includes/admin/page_tail2.php'; ?>