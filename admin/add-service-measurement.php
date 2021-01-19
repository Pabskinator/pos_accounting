<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('measure')) {
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
					<?php echo isset($editid) && !empty($editid) ? "EDIT MEASUREMENT" : "ADD MEASUREMENT"; ?>
				</h1>
			</div>
			<div class="row">
				<div class="col-md-12">

					<?php
						$user_list = [];
						if(isset($editid) && !empty($editid)) {
							// edit
							$id = Encryption::encrypt_decrypt('decrypt', $editid);
							// get the data base on branch id
							$service_measurement = new Service_measurement($id);



						}

						// if submitted
						if (Input::exists()){
							// check token if match to our token

							if(Token::check(Input::get('token'))){

								$validation_list = array(
									'name' => array(
										'required'=> true,
										'max' => 100
									)
								);
								// get id in update

								if(!Input::get('edit')) {
									$additionalvalidation = array('unique' => 'service_measurements');
									$finalvalidation=array_merge($validation_list['name'],$additionalvalidation);
									$validation_list['name'] = $finalvalidation;
								}

								$validate = new Validate();
								$validate->check($_POST, $validation_list);
								if($validate->passed()){
									$service_measurement = new Service_measurement();
									//edit codes
									if(Input::get('edit')){
										$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
										try{

											$service_measurement->update(array(
												'name' => Input::get('name'),
												'grp' => Input::get('grp')
											), $id);
											Session::flash('flash','Service measurement information has been successfully updated');
											Redirect::to('service_measurement.php');
										} catch(Exception $e) {
											die($e->getMessage());
										}
									} else {
										// insert codes
										try {

											$service_measurement->create(array(
												'name' => Input::get('name'),
												'grp' => Input::get('grp'),
												'created' => strtotime(date('Y/m/d H:i:s')),
												'company_id' => $user->data()->company_id,
												'is_active' => 1
											));
										} catch(Exception $e){
											die($e);
										}
										Session::flash('flash','You have successfully added a service measurement');
										Redirect::to('service_measurement.php');
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


							<legend>Information</legend>


							<div class="form-group">
								<label class="col-md-4 control-label" for="grp">Group</label>
								<div class="col-md-4">
									<input id="grp" name="grp" placeholder="Group" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($service_measurement->data()->grp) : escape(Input::get('grp')); ?>">
									<span class="help-block">Measurement group</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="name">Name</label>
								<div class="col-md-4">
									<input id="name" name="name" placeholder="Name" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($service_measurement->data()->name) : escape(Input::get('name')); ?>">
									<span class="help-block">Measurement name</span>
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

		});
	</script>

<?php require_once '../includes/admin/page_tail2.php'; ?>