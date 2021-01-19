<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';

	if(!$user->hasPermission('user')) {
    // redirect to denied page
    Redirect::to(1);
}

	//redirect when called
	if(isset($_GET['page'])) {

			$action = $_GET['page'];

			if($action == 'refresh'){
          Session::flash('userflash', 'User information has been successfully updated');
          Redirect::to('user.php');
			}else{
          Session::flash('userflash','You have successfully added a User');
          Redirect::to('user.php');
			}

	}

	if(isset($_GET['edit'])) {
		$editid = $_GET['edit'];
	} else {
		$editid = 0;
	}

	$listPosition = new Position();
	$positions = $listPosition->getAllPositions();
	$has_wallet = false;
	if(Configuration::getValue('wallet') == 1){
		$has_wallet = true;
	}

?>

	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				<?php echo isset($editid) && !empty($editid) ? "EDIT USER" : "ADD USER"; ?>
			</h1>
		</div>
			<div class="row">
				<div class="col-md-12">
					<?php
						if(isset($editid) && !empty($editid)) {
							// edit
							$id = Encryption::encrypt_decrypt('decrypt', $editid);


							// get the data base on branch id
							$edituser = new user();
							$editusers = $edituser->getAllUsers($user->data()->company_id,$id);

						}
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
					'max' => 50
				),
				'username' => array(
					'required'=> true,
					'min' => 3,
					'max' => 50
				),
				'password' => array(
					'min' => 6
				),
				'retype_password' => array(
					'matches' => 'password'
				),
				'position' => array(
					'required'=> true
				),
				'branch_id' => array(
					'required'=> true
				),
				'department_id' => array(
            'required'=> true
        ),
				'accounting_role_id' => array(
            'required'=> true
        )

			);
			if(!Input::get('edit')) {
				$additionalvalidation = array('unique' => 'users');
				$finalvalidation=array_merge($validation_list['username'],$additionalvalidation);
				$validation_list['username'] = $finalvalidation;
				$additionalvalidation = array('required' => true);
				$finalvalidation=array_merge($validation_list['password'],$additionalvalidation);
				$validation_list['password'] = $finalvalidation;
				$finalvalidation=array_merge($validation_list['retype_password'],$additionalvalidation);
				$validation_list['retype_password'] = $finalvalidation;
			} else {
				if(Input::get('password')){
					$additionalvalidation = array('required' => true);
					$finalvalidation=array_merge($validation_list['retype_password'],$additionalvalidation);
					$validation_list['retype_password'] = $finalvalidation;
				}
			}
			$validate = new Validate();
			$validate->check($_POST, $validation_list);


			if($validate->passed()){
				$newUser = new User();

				//edit codes
				if(Input::get('edit')){
					// parse user id, position id
					// update here
					// edit here


					 $id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));

					try{

						$userInfo = array(
							'lastname' => Input::get('lastname'),
							'firstname' => Input::get('firstname'),
							'middlename' => Input::get('middlename'),
							'username' => Input::get('username'),
							'is_active' => 1,
							'position_id' => Input::get('position'),
							'branch_id' => Input::get('branch_id'),
							'department_id' => Input::get('department_id'),
							'company_id' => $user->data()->company_id,
							'modified' => strtotime(date('Y/m/d H:i:s'))
						);
						/*if($has_wallet){
							$array_wallet =  array('wallet' => Input::get('wallet'));
							$userInfo=array_merge($userInfo,$array_wallet);
						}*/
						if(Input::get('password')){
							$arraypw =  array('password' => Hash::make(Input::get('password')));
							$userInfo=array_merge($userInfo,$arraypw);
						}

						if(Configuration::getValue('simple_timelog') == 1){
							$arraysalary =  array(
									'emp_id' => Input::get('emp_id'),
									'salary_rate' => Input::get('salary_rate'),
									'break_hrs' => Input::get('break_hrs'),
									'attendance_commission' => Input::get('attendance_commission'),

							);
							$userInfo=array_merge($userInfo,$arraysalary);
						}

						Log::addLog($user->data()->id,$user->data()->company_id,"Update User ". Input::get('firstname') . " " . Input::get('lastname'),"adduser.php");

						//update user
						if(!$newUser->update($userInfo, $id)) {

                ?>

								<script type="text/javascript">
									
										var user_id = <?php echo json_encode($id) ?>;
										var user_roles = <?php echo json_encode(Input::get('accounting_role_id')) ?>

										updateRoles(user_id, user_roles);

                    // edit or update roles
                    function updateRoles(id, roles){

                        $.ajax({

                            url: "/dunsk/accounting/public/api/update_user_role",
                            type: "POST",
                            data: {
                                user_id: id,
																roles: roles,
                            },
                            success: function(data){

                                window.location.href = "adduser.php?page=refresh";

                            },
                            error: function(){

                            }

                        });

                    }

								</script>

                <?php

            }
					} catch(Exception $e) {
						die($e->getMessage());
					}
				} else {
					// insert codes


					try {
							$userInfo = array(
								'lastname' => Input::get('lastname'),
								'firstname' => Input::get('firstname'),
								'middlename' => Input::get('middlename'),
								'username' => Input::get('username'),
								'password' => Hash::make(Input::get('password')),
								'is_active' => 1,
								'position_id' => Input::get('position'),
								'branch_id' => Input::get('branch_id'),
								'department_id' => Input::get('department_id'),
								'company_id' => $user->data()->company_id,
								'created' => strtotime(date('Y/m/d H:i:s')),
								'modified' => strtotime(date('Y/m/d H:i:s'))
								);

							/*if($has_wallet){
								$array_wallet =  array('wallet' => Input::get('wallet'));
								$userInfo=array_merge($userInfo,$array_wallet);
							}*/
							if(Configuration::getValue('simple_timelog') == 1){
								$arraysalary =  array(
										'emp_id' => Input::get('emp_id'),
										'salary_rate' => Input::get('salary_rate'),
										'break_hrs' => Input::get('break_hrs'),
										'attendance_commission' => Input::get('attendance_commission'),
								);
								$userInfo=array_merge($userInfo,$arraysalary);
							}
							Log::addLog($user->data()->id,$user->data()->company_id,
							"Insert User ". Input::get('firstname') . " " . Input::get('lastname'),"adduser.php");

							//add user
							if(!$newUser->create($userInfo)){

									?>

											<script>

                          var user_roles = <?php echo json_encode(Input::get('accounting_role_id')) ?>;
                          var user = <?php echo json_encode($newUser->getInsertedId()) ?>;

													addRoles(user_roles, user)

                          function addRoles(roles, user_id){

                              $.ajax({
                                  url: "/dunsk/accounting/public/api/add_user_role",
                                  type: "POST",
                                  data: {
																			roles: roles,
																			user_id: user_id,
                                  },
                                  success: function(data){
                                      window.location.href = "adduser.php?page=add";
                                  },
                                  error: function(){

                                  }
                              });

                          }

											</script>

									<?php

							}


						} catch(Exception $e){
							die($e);
						}

				}
			}else {
				$el ='';
				echo "<div class='alert alert-danger'>";
				foreach($validate->errors() as $error){
					$el.=escape($error) . "<br/>" ;
				}
				echo "$el</div>";
			}
		}
	}

					?>
					<form class="form-horizontal" method='POST' action=''>
						<fieldset>

							<!-- Form Name -->
							<legend>User Information</legend>

							<!-- Text input-->
							<div class="form-group" >
								<label class="col-md-1 control-label" for="lastname" >Lastname</label>
								<div class="col-md-3">
									<input id="lastname" name="lastname" placeholder="Last Name" class="form-control input-md" type="text" value='<?php echo isset($id) ? escape($editusers->lastname) : escape(Input::get('lastname')); ?>'>
									<span class="help-block"></span>
								</div>
								<label class="col-md-1 control-label" for="firstname" >Firstname</label>
								<div class="col-md-3">
									<input id="firstname" name="firstname" placeholder="First Name" class="form-control input-md" type="text" value='<?php echo isset($id) ? escape($editusers->firstname) : escape(Input::get('firstname')); ?>'>
									<span class="help-block"></span>
								</div>
								<label class="col-md-1 control-label" for="middlename" >Middle</label>
								<div class="col-md-3">
									<input id="middlename" name="middlename" placeholder="Middle Name" class="form-control input-md" type="text" value='<?php echo isset($id) ? escape($editusers->middlename) : escape(Input::get('middlename')); ?>'>
									<span class="help-block"></span>
								</div>
							</div>
							<div class="form-group" >
								<label class="col-md-1 control-label" for="username" >Username</label>
								<div class="col-md-3">
									<input id="username" name="username" placeholder="Username" class="form-control input-md" type="text" value='<?php echo isset($id) ? escape($editusers->username) : escape(Input::get('username')); ?>'>
									<span class="help-block"></span>
								</div>
								<label class="col-md-1 control-label" for="password" >Password</label>
								<div class="col-md-3">
									<input id="password" name="password" placeholder="password" class="form-control input-md" type="password">
									<span class="help-block"></span>
								</div>
								<label class="col-md-1 control-label" for="retype_password" >Retype Password</label>
								<div class="col-md-3">
									<input id="retype_password" name="retype_password" placeholder="Retype password" class="form-control input-md" type="password">
									<span class="help-block"></span>
								</div>
							</div>
							<div class="form-group" >
								<label class="col-md-1 control-label" for="position" >Position</label>
								<div class="col-md-3">
									<?php if($positions){ ?>
									<select name="position" id="position" class='form-control input-md'>
										<option value="">--Choose position--</option>
										<?php
											foreach($positions as $p){
												$a = isset($id) ? $editusers->position_id : escape(Input::get('position_id'));

												if($a==$p->id){
													$selected='selected';
												} else {
													$selected='';
												}
												?>
												<option value="<?php echo $p->id; ?>" <?php echo $selected; ?> > <?php echo $p->position; ?></option>
												<?php
											}
										?>
									</select>
											<?php } else {
										echo "<a href='addposition.php'>PLEASE CREATE POSITION FIRST</a>";
									}?>
									<span class="help-block"></span>
								</div>
								<label class="col-md-1 control-label" for="accounting_role_id">Accounting Role</label>
								<div class="col-md-3">
									<select id="accounting_role_id" name="accounting_role_id" class="form-control">
										<option value=''>--Select Accounting Role--</option>

									</select>
								</div>
								<label class="col-md-1 control-label" for="department_id">Department</label>
								<div class="col-md-3">
									<select id="department_id" name="department_id" class="form-control">
										<option value=''>--Select Department--</option>

									</select>
								</div>
								<?php if(false){
									?>
									<label class="col-md-1 control-label" for="wallet" >E-wallet</label>
									<div class="col-md-3">
									<input id="wallet" name="wallet" value='<?php echo isset($id) ? escape($editusers->wallet) : escape(Input::get('wallet')); ?>'  placeholder="Enter amount" class="form-control input-md" type="text">
									<span class="help-block"></span>
									</div>
									<?php
								} ?>
							</div>
							<div class="form-group">
								<label class="col-md-1 control-label" for="branch_id">Branch</label>
								<div class="col-md-3">
									<select id="branch_id" name="branch_id" class="form-control">
										<option value=''>--Select Branch--</option>
                      <?php
                      $branch = new Branch();
                      $branches =  $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));
                      foreach($branches as $b){
                          $a = isset($id) ? $editusers->branch_id : escape(Input::get('branch_id'));

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
									<span class="help-block">From what branch</span>
								</div>
							</div>
						</div>
						<?php
							if(Configuration::getValue('simple_timelog') == 1){
								?>
								<div class="row">
									<div class="col-md-12 text-right">
											<label class="col-md-1 control-label" for="emp_id" >Employee ID</label>
											<div class="col-md-3">
												<div class="form-group">
													<input type="text" class='form-control' name='emp_id' id='emp_id' placeholder='Employee ID'>
												</div>
											</div>
											<label class="col-md-1 control-label" for="salary_rate" >Hrly Rate</label>
											<div class="col-md-3">
												<div class="form-group">
													<input type="number" class='form-control' name='salary_rate' id='salary_rate' placeholder='Rate'>
												</div>
											</div>
											<label class="col-md-1 control-label" for="break_hrs" >Break Hrs</label>
											<div class="col-md-3">
												<div class="form-group">
													<input type="number" class='form-control' name='break_hrs' id='break_hrs' placeholder='Break'>
												</div>
											</div>
											<label class="col-md-1 control-label" for="attendance_commission" >Attendance commission</label>
											<div class="col-md-3">
												<div class="form-group">
													<input type="number" class='form-control' name='attendance_commission' id='attendance_commission' placeholder='Amount'>
												</div>
											</div>
									</div>
								</div>
								<?php
							}
						?>
							<hr>
						<div class="form-group">
								<div class="col-md-4">
									<input id="btnSave" name="btnSave" class="btn btn-success" type='submit' value='SAVE'>
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

	<script type="text/javascript">

      $( document ).ready(function() {

          getRoles();

      });

      //get roles
      function getRoles(){

          $.ajax({
							url: "/dunsk/accounting/public/api/role",
							type: "GET",
							data: {

							},
							success: function(data){

							    var roles = data.roles;
									var depts = data.departments;
									var edited_val = <?php echo json_encode($editusers) ?>;
									var edited_dep = '';
									var edited_role = '';

                  <?php

											$a = isset($id) ? $editusers->department_id : escape(Input::get('department_id'));
                  		$b = isset($id) ? $editusers->accounting_role_id : escape(Input::get('accounting_role_id'));

											echo "edited_dep = {$a} \n";
											echo "edited_role = {$b}";

                  ?>

							    roles.forEach(function (item){

                      var o = new Option(item.name, item.id);
                      $(o).html(item.name);
                      $("#accounting_role_id").append(o);

                      if(item.id == edited_role){
                          $('#accounting_role_id').val(edited_role).prop('selected', true);
                      }

									});

                  depts.forEach(function (item){

                      var o = new Option(item.name, item.id);
                      $(o).html(item.name);
                      $("#department_id").append(o);

                      if(item.id == edited_dep){
                          $('#department_id').val(edited_dep).prop('selected', true);
                      }

                  });

							},
							error: function(){

							}
          });

			}

	</script>


<?php require_once '../includes/admin/page_tail2.php'; ?>