
<!-- Page content -->
<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					<span class='hidden-xs'>
					<?php echo isset($editid) && !empty($editid) ? "EDIT DISPLAY LOCATION" : "ADD DISPLAY LOCATION"; ?>
					</span>
			</h1>
		</div>
		<?php include 'includes/product_nav.php'; ?>
		<div class="row">
			<div class="col-md-12">

				<?php
					if(isset($editid) && !empty($editid)) {
						// edit
						$id = Encryption::encrypt_decrypt('decrypt', $editid);
						// get the data base on branch id
						$display = new Display_location($id);
					}

					// if submitted
					if (Input::exists()){
						// check token if match to our token
						if(Token::check(Input::get('token'))){

							$validation_list = array(
								'name' => array(
									'required'=> true,
									'max' => 75
								),
								'description' => array(
									'required'=> true,
									'max' => 200
								)
							);
							if(!Input::get('edit')) {
								$additionalvalidation = array('unique' => 'display_location');
								$finalvalidation=array_merge($validation_list['name'],$additionalvalidation);
								$validation_list['name'] = $finalvalidation;
							}


							$validate = new Validate();
							$validate->check($_POST, $validation_list);
							if($validate->passed()){
								$display = new Display_location();
								//edit codes
								if(Input::get('edit')){
									$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
									try{

										$display->update(array(
											'name' => Input::get('name'),
											'description' => Input::get('description'),
											'modified' => strtotime(date('Y/m/d H:i:s'))
										), $id);
										Log::addLog($user->data()->id,$user->data()->company_id,"Update display location ||display_location:".$id,'admin/adddisplay');
										Session::flash('flash','Display location information has been successfully updated');
										Redirect::to('display.php');
									} catch(Exception $e) {
										die($e->getMessage());
									}
								} else {
									// insert codes
									try {
										$display->create(array(
											'name' => Input::get('name'),
											'description' => Input::get('description'),
											'created' => strtotime(date('Y/m/d H:i:s')),
											'company_id' => $user->data()->company_id,
											'modified' => strtotime(date('Y/m/d H:i:s')),
											'is_active' => 1
										));
										$lastid = $display->getInsertedId();
										Log::addLog($user->data()->id,$user->data()->company_id,"Insert display location ||display_location:".$lastid,'admin/adddisplay');

									} catch(Exception $e){
										die($e);
									}

									Session::flash('flash', 'You have successfully added display location.');
									Redirect::to('display.php');

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


						<legend>Display Information</legend>


						<div class="form-group">
							<label class="col-md-4 control-label" for="name">Location Name</label>
							<div class="col-md-4">
								<input id="name" name="name" placeholder="Location Name" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($display->data()->name) : escape(Input::get('name')); ?>">
								<span class="help-block">Alpha numeric, maximum of 75 characters</span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label" for="description">Description</label>
							<div class="col-md-4">
								<input id="description" name="description" placeholder="Description" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($display->data()->description) : escape(Input::get('description')); ?>">
								<span class="help-block">Alpha numeric, maximum of 200 characters</span>
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