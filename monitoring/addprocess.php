<?php
	// $user have all the properties and method of the current user
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require_once '../includes/monitoring/page_head.php';
	if(!$user->hasPermission('branch')) {
		// redirect to denied page
		//Redirect::to(1);
	}
	if(isset($_GET['edit'])) {
		$editid = $_GET['edit'];
	} else {
		$editid = 0;
	}

?>
<?php require_once '../includes/monitoring/page_head.php'; ?>
	<!-- Sidebar -->
<?php include_once '../includes/monitoring/sidebar.php'; ?>
	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					<?php echo isset($editid) && !empty($editid) ? "EDIT PROCESS" : "ADD PROCESS"; ?>
				</h1>
			</div>
			<div class="row">
				<div class="col-md-12">

					<?php
						if(isset($editid) && !empty($editid)) {
							// edit
							$id = Encryption::encrypt_decrypt('decrypt', $editid);
							// get the data base on branch id
							$process = new Process($id);
						}

						// if submitted
						if (Input::exists()){
							// check token if match to our token
							if(Token::check(Input::get('token'))){

								$validation_list = array(
									'name' => array(
										'required'=> true,
										'min' => 6,
										'max' => 50
									),
									'description' => array(
										'required'=> true,
										'min' => 6,
										'max' => 200
									),
									'steps' => array(
										'required'=> true,
										'isnumber'=> true
									)
								);
								// get id in update

								if(!Input::get('edit')) {
								//	$additionalvalidation = array('unique' => 'processes');
								//	$finalvalidation=array_merge($validation_list['name'],$additionalvalidation);
								//	$validation_list['name'] = $finalvalidation;
								}

								$validate = new Validate();
								$validate->check($_POST, $validation_list);
								if($validate->passed()){
									$process = new Process();
									//edit codes
									if(Input::get('edit')){
										$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
										try{
											$process->update(array(
												'name' => Input::get('name'),
												'description' => Input::get('description'),
												'steps' => Input::get('steps'),
												'modified' => strtotime(date('Y/m/d H:i:s'))
											), $id);
											Session::flash('processflash','Process information has been successfully updated');
											Redirect::to('process.php');
										} catch(Exception $e) {
											die($e->getMessage());
										}
									} else {
									// insert codes
									try {
										$process->create(array(
											'name' => Input::get('name'),
											'description' => Input::get('description'),
											'steps' => Input::get('steps'),
											'company_id' => $user->data()->company_id,
											'is_active' => 1,
											'created' => strtotime(date('Y/m/d H:i:s')),
											'modified' => strtotime(date('Y/m/d H:i:s'))
											
										));
									} catch(Exception $e){
										die($e);
									}
									Session::flash('processflash','You have successfully added a process');
									Redirect::to('process.php');
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


							<legend>Process Information</legend>


							<div class="form-group">
								<label class="col-md-4 control-label" for="name">Process Name</label>
								<div class="col-md-4">
									<input id="processName" name="name" placeholder="Process Name" class="form-control input-md" type="text" value="<?php echo isset($id) ? $process->data()->name : escape(Input::get('name')); ?>">
									<span class="help-block">The name of the process can consists of letters and numbers</span>
								</div>
							</div>

							<!-- Text input-->
							<div class="form-group">
								<label class="col-md-4 control-label" for="d">Process Description</label>
								<div class="col-md-4">
									<input id="processDescription" name="description" placeholder="Description" class="form-control input-md" type="text" value="<?php echo isset($id) ? $process->data()->description :  escape(Input::get('description')); ?>">
									<span class="help-block">Description for your Branch</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="d">Number of Steps</label>
								<div class="col-md-4">
									<input id="processSteps" name="steps" placeholder="Steps" class="form-control input-md" type="text" value="<?php echo isset($id) ? $process->data()->steps :  escape(Input::get('steps')); ?>">
									<span class="help-block">Number of steps of this process</span>
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


<?php require_once '../includes/monitoring/page_tail.php'; ?>