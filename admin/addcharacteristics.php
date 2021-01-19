<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('characteristics')) {
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
				<h1 class='hidden-xs'>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					<?php echo isset($editid) && !empty($editid) ? "EDIT CHARACTERISTICS" : "ADD CHARACTERISTICS"; ?>
				</h1>
				<p class='visible-xs'>Add Record</p>
			</div>
			<div class="row">
				<div class="col-md-12">

					<?php
						if(isset($editid) && !empty($editid)) {
							// edit
							$id = Encryption::encrypt_decrypt('decrypt', $editid);
							// get the data base on branch id
							$editChar = new Characteristics($id);
						}

						// if submitted
						if (Input::exists()){
							// check token if match to our token
							if(Token::check(Input::get('token'))){
								$validation_list = array(
									'name' => array(
										'required'=> true,
										'max' => 50
									)

								);
								if(!Input::get('edit')) {
									$additionalvalidation = array('unique' => 'characteristics');
									$finalvalidation=array_merge($validation_list['name'],$additionalvalidation);
									$validation_list['name'] = $finalvalidation;
								}
								$validate = new Validate();
								$validate->check($_POST, $validation_list);
								if($validate->passed()){
									$newchar = new Characteristics();
									//edit codes
									if(Input::get('edit')){
										$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
										try{
											$newchar->update(array(
												'name' => Input::get('name'),
												'modified' => strtotime(date('Y/m/d H:i:s'))
											), $id);
											Log::addLog($user->data()->id,$user->data()->company_id,"Update product characteristics ||characteristics:".$id,'admin/addcharacteristics.php');
											Session::flash('characteristicsflash','Terminal information has been successfully updated');
											Redirect::to('characteristics.php');
										} catch(Exception $e) {
											die($e->getMessage());
										}
									} else {
										// insert codes
										try {

											$newchar->create(array(
												'name' => Input::get('name'),
												'company_id' => $user->data()->company_id,
												'is_active' => 1,
												'created' => strtotime(date('Y/m/d H:i:s')),
												'modified' => strtotime(date('Y/m/d H:i:s'))
											));
											$lastid = $newchar->getInsertedId();
											Log::addLog($user->data()->id,$user->data()->company_id,"Insert product characteristics ||characteristics:".$lastid,'admin/addcharacteristics.php');

										} catch(Exception $e){
											die($e);
										}
										Session::flash('characteristicsflash','You have successfully added a Terminal');
										Redirect::to('characteristics.php');
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


							<legend>Characteristics Information</legend>


							<div class="form-group">
								<label class="col-md-4 control-label" for="name">Characteristics Name</label>
								<div class="col-md-4">
									<input id="name" name="name" placeholder="Characteristics Name" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($editChar->data()->name) : escape(Input::get('name')); ?>">
									<span class="help-block">Name of the Characteristics</span>
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