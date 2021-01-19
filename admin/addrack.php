<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('rack')) {
		// redirect to denied page
		Redirect::to(1);
	}
	if(isset($_GET['edit'])) {
		$editid = $_GET['edit'];
	} else {
		$editid = 0;
	}
	$user_cls = new User();
	$all_users = $user_cls->get_active('users',array('company_id','=',$user->data()->company_id));

	$rack_tag = new Rack_tag();
	$all_tags = $user_cls->get_active('rack_tags',array('company_id','=',$user->data()->company_id));

?>

	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					<?php echo isset($editid) && !empty($editid) ? "EDIT RACK" : "ADD RACK"; ?>
				</h1>
			</div>
			<div class="row">
				<div class="col-md-12">

					<?php
						if(isset($editid) && !empty($editid)) {
							// edit
							$id = Encryption::encrypt_decrypt('decrypt', $editid);
							// get the data base on branch id
							$rack = new Rack($id);
						}

						// if submitted
						if (Input::exists()){

							// check token if match to our token
							if(Token::check(Input::get('token'))){

								$validation_list = array(
									'rack' => array(
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
									$rack = new Rack();

									//edit codes
									if(Input::get('edit')){
										$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));

										try{
											if(Input::get('is_default')){
												$isdef = 1;
												$rack->updateRackDefault(Input::get('branch_id'));
											} else {
												$isdef = 0;
											}

											$rack->update(array(
												'rack' => Input::get('rack'),
												'modified' => strtotime(date('Y/m/d H:i:s')),
												'user_id' => Input::get('select_user'),
												'rack_tag' => Input::get('rack_tag'),
												'description' => Input::get('description'),
												'stock_man' => Input::get('stock_man'),
												'branch_id' => Input::get('branch_id'),
												'is_default' => $isdef
											), $id);

											Log::addLog($user->data()->id,$user->data()->company_id,"Update Rack ". Input::get('rack'),"addrack.php");

											Session::flash('rackflash','Rack information has been successfully updated');
											Redirect::to('rack.php');
										} catch(Exception $e) {
											die($e->getMessage());
										}
									} else {
										// insert codes
										try {
											if(Input::get('is_default')){
												$isdef = 1;
												$rack->updateRackDefault(Input::get('branch_id'));

											} else {
												$isdef = 0;
											}
											$checkRack = new Inventory();
											$hasRack = $checkRack->getRackName(Input::get('rack'),$user->data()->company_id,Input::get('branch_id'));
											if($hasRack){
												//$validate->errors()[] = "Rack name already exists";
												$rackexists = "Rack name already exists";
											} else {
												$rackexists='';

												$rack->create(array(
													'rack' => Input::get('rack'),
													'created' => strtotime(date('Y/m/d H:i:s')),
													'company_id' => $user->data()->company_id,
													'modified' => strtotime(date('Y/m/d H:i:s')),
													'is_active' => 1,
													'user_id' => Input::get('select_user'),
													'rack_tag' => Input::get('rack_tag'),
													'description' => Input::get('description'),
													'stock_man' => Input::get('stock_man'),
													'branch_id' => Input::get('branch_id'),
													'is_default' => $isdef
												));

												Log::addLog($user->data()->id,$user->data()->company_id,"Insert Rack ". Input::get('rack'),"addrack.php");

											}
										} catch(Exception $e){
											die($e);
										}
										if($rackexists){
											$el ='';
											echo "<div class='alert alert-danger'>";
											$el =$rackexists;
											echo "$el</div>";
										} else {
											Session::flash('rackflash', 'You have successfully added a Rack');
											Redirect::to('rack.php');
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


							<legend>Rack Information</legend>

							<div class="form-group">
								<label class="col-md-4 control-label" for="is_default">Assign as default</label>
								<div class="col-md-4">
										<input type="checkbox" name='is_default' id='is_default' <?php echo (isset($id) && $rack->data()->is_default == 1 ) ? 'checked' : ''; ?>> Check to mark this rack as default
								</div>
							</div>

							<div class="form-group">
								<label class="col-md-4 control-label" for="name">Rack Name</label>
								<div class="col-md-4">
									<input <?php  echo (isset($id)) ? 'readonly' : ''; ?> id="rackName" name="rack" placeholder="Rack Name" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($rack->data()->rack) : escape(Input::get('rack')); ?>">
									<span class="help-block">Alpha numeric, maximum of 50 characters</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="description">Description</label>
								<div class="col-md-4">
									<input id="description" name="description" placeholder="Description" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($rack->data()->description) : escape(Input::get('description')); ?>">
									<span class="help-block">Alpha numeric, maximum of 100 characters</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="select_user">Assign to (Optional)</label>
								<div class="col-md-4">
									<select name="select_user" id="select_user" class='form-control'>
										<option value=""></option>
										<?php
											if(count($all_users) > 0){
												foreach($all_users as $u){

													$a = isset($id) ? $rack->data()->user_id : escape(Input::get('select_user'));

													if($a==$u->id){
														$selected='selected';
													} else {
														$selected='';
													}

													?>
													<option value="<?php echo escape($u->id); ?>" <?php echo $selected; ?> ><?php echo escape(ucwords($u->lastname . ", " . $u->firstname .  " " . $u->middlename)); ?></option>
													<?php
												}
											}
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="rack_tag">Tag</label>
								<div class="col-md-4">
									<select name="rack_tag" id="rack_tag" class='form-control'>
										<option value=""></option>
										<?php
											if(count($all_tags) > 0){
												foreach($all_tags as $t){

													$a = isset($id) ? $rack->data()->rack_tag : escape(Input::get('rack_tag'));

													if($a==$t->id){
														$selected='selected';
													} else {
														$selected='';
													}

													?>
													<option value="<?php echo escape($t->id); ?>" <?php echo $selected; ?> ><?php echo escape(ucwords($t->tag_name)); ?></option>
													<?php
												}
											}
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="stock_man">Stock Man</label>
								<div class="col-md-4">
									<input id="stock_man" name="stock_man" placeholder="Stock Man" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($rack->data()->stock_man) : escape(Input::get('stock_man')); ?>">
									<span class="help-block"></span>
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
												$a = isset($id) ? $rack->data()->branch_id : escape(Input::get('branch_id'));

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
									<span class="help-block"></span>
								</div>
							</div>

							</div>
							<!-- Button (Double) -->
							<div class="form-group">
								<label class="col-md-4 control-label" for="button1id">&nbsp;</label>
								<div class="col-md-4">
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