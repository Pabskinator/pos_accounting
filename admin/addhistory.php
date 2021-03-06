<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('med_history')) {
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
					<?php echo isset($editid) && !empty($editid) ? "EDIT HISTORY" : "ADD HISTORY"; ?>
				</h1>
			</div>
			<div class="row">
				<div class="col-md-12">

					<?php
						if(isset($editid) && !empty($editid)) {
							// edit
							$id = Encryption::encrypt_decrypt('decrypt', $editid);
							// get the data base on branch id
							$history = new Med_history($id);
						}

						// if submitted
						if (Input::exists()){
							// check token if match to our token
							if(Token::check(Input::get('token'))){

								$validation_list = array(
									'name' => array(
										'required'=> true,
										'max' => 50
									),'grp' => array(
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
									$additionalvalidation = array('unique' => 'med_histories');
									$finalvalidation=array_merge($validation_list['name'],$additionalvalidation);
									$validation_list['name'] = $finalvalidation;
								}
								if($count_sub_companies > 0){
									$additionalvalidation = array('required' => true);
									$finalvalidation=array_merge($validation_list['sub_company'],$additionalvalidation);
									$validation_list['sub_company'] = $finalvalidation;
								}

								$validate = new Validate();
								$validate->check($_POST, $validation_list);
								if($validate->passed()){
									$history = new Med_history();
									//edit codes
									if(Input::get('edit')){
										$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
										try{
											$arrupdate = array(
												'name' => Input::get('name'),
												'grp' => Input::get('grp'),
												'description' => Input::get('description')
											);

											$history->update($arrupdate, $id);
											Session::flash('flash','History information has been successfully updated');
											Redirect::to('med_history.php');
										} catch(Exception $e) {
											die($e->getMessage());
										}
									} else {
										// insert codes
										try {
											$inserarr = array(
												'name' => Input::get('name'),
												'description' => Input::get('description'),
												'grp' => Input::get('grp'),
												'created' => strtotime(date('Y/m/d H:i:s')),
												'company_id' => $user->data()->company_id,
												'is_active' => 1
											);

											$history->create($inserarr);
										} catch(Exception $e){
											die($e);
										}
										Session::flash('flash','You have successfully added a history');
										Redirect::to('med_history.php');
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


							<legend>History Information</legend>


							<div class="form-group">
								<label class="col-md-4 control-label" for="name">Name</label>
								<div class="col-md-4">
									<input id="name" name="name" placeholder="Name" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($history->data()->name) : escape(Input::get('name')); ?>">
									<span class="help-block"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="grp">Group</label>
								<div class="col-md-4">
									<input id="grp" name="grp" placeholder="Group" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($history->data()->grp) : escape(Input::get('grp')); ?>">
									<span class="help-block"></span>
								</div>
							</div>

							<!-- Text input-->
							<div class="form-group">
								<label class="col-md-4 control-label" for="description">Description</label>
								<div class="col-md-4">
									<input id="description" name="description" placeholder="Description" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($history->data()->description) :  escape(Input::get('description')); ?>">
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


<?php require_once '../includes/admin/page_tail2.php'; ?>