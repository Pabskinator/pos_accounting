<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('queue')) {
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
					<?php echo isset($editid) && !empty($editid) ? "EDIT QUEUE" : "ADD QUEUE"; ?>
				</h1>
			</div>
			<div class="row">
				<div class="col-md-12">

					<?php
						if(isset($editid) && !empty($editid)) {
							// edit
							$id = Encryption::encrypt_decrypt('decrypt', $editid);
							// get the data base on branch id
							$queu = new Queu($id);
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
									'branch_id' => array(
										'required'=> true
									)
								);



								$validate = new Validate();
								$validate->check($_POST, $validation_list);
								if($validate->passed()){
									$newqueue = new Queu();
									//edit codes
									if(Input::get('edit')){
										$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
										try{

											$newqueue->update(array(
												'name' => Input::get('name'),
												'branch_id' => Input::get('branch_id'),
												'modified' => strtotime(date('Y/m/d H:i:s'))
											), $id);
											Log::addLog($user->data()->id,$user->data()->company_id,"Update queue ||queue:".$id,'admin/addqueue.php');
											Session::flash('queueflash','Queue information has been successfully updated');
											Redirect::to('queu.php');
										} catch(Exception $e) {
											die($e->getMessage());
										}
									} else {
										// insert codes
										try {
											$newqueue->create(array(
												'name' => Input::get('name'),
												'branch_id' => Input::get('branch_id'),
												'created' => strtotime(date('Y/m/d H:i:s')),
												'modified' => strtotime(date('Y/m/d H:i:s')),
												'is_active'=> 1
											));
											$lastid = $newqueue->getInsertedId();
											Log::addLog($user->data()->id,$user->data()->company_id,"Insert queue ||queue:".$lastid,'admin/addqueue.php');

										} catch(Exception $e){
											die($e);
										}
										Session::flash('queueflash','You have successfully added a Queue');
										Redirect::to('queu.php');
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


							<legend>Queu Information</legend>


							<div class="form-group">
								<label class="col-md-4 control-label" for="name">Queue Name</label>
								<div class="col-md-4">
									<input id="branchName" name="name" placeholder="Queue Name" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($queu->data()->name) : escape(Input::get('name')); ?>">
									<span class="help-block">Name of the Queue</span>
								</div>
							</div>



							<div class="form-group">
								<label class="col-md-4 control-label" for="branch_id">Branch</label>
								<div class="col-md-4">
									<select id="branch_id" name="branch_id" class="form-control">
										<option value=''>--Select Branch--</option>
										<?php
											$branch = new Branch();
											$branches =  $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));
											foreach($branches as $b){
												$a = isset($id) ? $queu->data()->branch_id : escape(Input::get('branch_id'));

												if($a==$b->id){
													$selected='selected';
												} else {
													$selected='';
												}
												?>
												<option value='<?php echo $b->id ?>' <?php echo $selected ?>><?php echo escape($b->name);?> </option>
											<?php
											}
										?>
									</select>
									<span class="help-block">From what branch</span>
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