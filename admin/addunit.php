<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('unit')) {
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
					<?php echo isset($editid) && !empty($editid) ? "EDIT UNIT" : "ADD UNIT"; ?>
				</h1>
			</div>
			<div class="row">
				<div class="col-md-12">

					<?php
						if(isset($editid) && !empty($editid)) {
							// edit
							$id = Encryption::encrypt_decrypt('decrypt', $editid);
							// get the data base on branch id
							$editUnit = new Unit($id);
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
									$additionalvalidation = array('unique' => 'units');
									$finalvalidation=array_merge($validation_list['name'],$additionalvalidation);
									$validation_list['name'] = $finalvalidation;
								}
								$validate = new Validate();
								$validate->check($_POST, $validation_list);
								if($validate->passed()){
									$newUnit = new Unit();
									//edit codes
									if(Input::get('edit')){
										$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
										try{
											$newUnit->update(array(
												'name' => Input::get('name'),
												'is_decimal' => Input::get('is_decimal'),
												'modified' => strtotime(date('Y/m/d H:i:s'))
											), $id);
											Log::addLog($user->data()->id,$user->data()->company_id,"Update product units ||units:".$id,'admin/addunits.php');

											Session::flash('unitflash','Unit information has been successfully updated');
											Redirect::to('unit.php');
										} catch(Exception $e) {
											die($e->getMessage());
										}
									} else {
										// insert codes
										try {

											$newUnit->create(array(
												'name' => Input::get('name'),
												'company_id' => $user->data()->company_id,
												'is_active' => 1,
												'is_decimal' => Input::get('is_decimal'),
												'created' => strtotime(date('Y/m/d H:i:s')),
												'modified' => strtotime(date('Y/m/d H:i:s'))
											));
											$lastid = $newUnit->getInsertedId();
											Log::addLog($user->data()->id,$user->data()->company_id,"Insert product units ||units:".$lastid,'admin/addunit.php');
											Session::flash('unitflash','You have successfully added a Unit');
											Redirect::to('unit.php');

										} catch(Exception $e){
											die($e);
										}

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


							<legend>Unit Information</legend>


							<div class="form-group">
								<label class="col-md-4 control-label" for="name">Unit Name</label>
								<div class="col-md-4">
									<input id="name" name="name" placeholder="Unit Name" class="form-control input-md" type="text" value="<?php echo isset($id) ? $editUnit->data()->name : escape(Input::get('name')); ?>">
									<span class="help-block">Name of the Unit (Pcs, Dozen, Hour etc.)</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="is_decimal">Has decimal</label>
								<div class="col-md-4">
									<select id="is_decimal" name="is_decimal" class="form-control">
										<option value=''>--Choose Item--</option>
										<?php
											?>
										<option value="1"
											<?php
												if(isset($id)){
													echo (isset($editUnit->data()->is_decimal) && $editUnit->data()->is_decimal ==1) ? ' selected' : '';
												}
											?>
											>Yes</option>
										<option value="0"
											<?php
												if(isset($id)){
													echo (isset($editUnit->data()->is_decimal) && $editUnit->data()->is_decimal ==0) ? ' selected' : '';
												}
											?>
											>No</option>
											<?php
										?>
									</select>
									<span class="help-block">Has decimal</span>
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