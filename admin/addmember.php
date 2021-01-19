<?php
	// $user have all the properties and method of the current user
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('member')) {
		// redirect to denied page
		Redirect::to(1);
	}
	$withUserAccount = false;
	if(isset($_GET['edit'])) {
		$editid = $_GET['edit'];
		$hasMem = new Member();
		$hasUserMember = $hasMem->hasUserMember(Encryption::encrypt_decrypt('decrypt',$editid));
		if($hasUserMember->cnt > 0){
			$withUserAccount = true;
		}
	} else {
		$editid = 0;
	}

	$memberlbl = MEMBER_LABEL;

	$cf = new Custom_field();
	$cfd = new Custom_field_details();
	$getmember = $cf->getcustomform('members',$user->data()->company_id);
	$otherfield = isset($getmember->other_field)?$getmember->other_field:'';
	if($otherfield){
		$otherfield = json_decode($otherfield,true);
	}

	$alldata = $cfd->getAllData($user->data()->company_id,$getmember->id);
	foreach($alldata as $data){
		$f_label = $data->field_label;
		$c_visible = $data->is_visible;
		$name = $data->field_name;
		$f = "f_".$name;
		$c= "c_".$name;
		$$f = $f_label;
		$$c = $c_visible;
	}





?>

	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span> <?php echo isset($editid) && !empty($editid) ? "Edit " . $memberlbl : "Add " . $memberlbl; ?>
				</h1>
			</div>
			<div class="row">
				<div class="col-md-12">

					<?php

						if(isset($editid) && !empty($editid)) {
							// edit
							$id = Encryption::encrypt_decrypt('decrypt', $editid);
							// get the data base on branch id
							$editMem = new Member($id);
							$editChar = new Member_characteristics();
							$myChar = $editChar->getMyCharacteristicsd($id);
						}

						// if submitted
						if(Input::exists()) {

							// check token if match to our token
							if(Token::check(Input::get('token'))) {
								$validation_list = array(
									'lastname' => array('required' => true, 'max' => 350),
									'firstname' => array('max' => 50),
									'middlename' => array('max' => 50),
									'birthdate' => array('max' => 50),
									'email' => array('max' => 50),
									'personal_address' => array('max' => 200),
									'contact_number' => array('max' => 50),
									'sg_year' => array('isnumber' => true),
									'retype_password' => array('matches' => 'password'),
									'username' => array('max' => 50),
									'password' => array('max' => 50),
									'branch_id' => array('max' => 50)
								);
								if(Configuration::thisCompany('cebuhiq')){

									$validation_list['salestype'] = array('required' => true);

								}
								if(!$withUserAccount && Input::get('username')){
									$additionalvalidation = array('unique' => 'users');
									$finalvalidation=array_merge($validation_list['username'],$additionalvalidation);
									$validation_list['username'] = $finalvalidation;
									$additionalvalidation = array('required' => true);
									$finalvalidation=array_merge($validation_list['password'],$additionalvalidation);
									$validation_list['password'] = $finalvalidation;
									$additionalvalidation = array('required' => true);
									$finalvalidation=array_merge($validation_list['retype_password'],$additionalvalidation);
									$validation_list['retype_password'] = $finalvalidation;
									$additionalvalidation = array('required' => true);
									$finalvalidation=array_merge($validation_list['branch_id'],$additionalvalidation);
									$validation_list['branch_id'] = $finalvalidation;
								}
								$jsonstringother ='';
								if($otherfield){
									$jsonstringother ='{';
									foreach($otherfield as $cfield){
										if($cfield['field-visibility'] == 1){
											$jsonstringother .= '"'.$cfield['field-id'].'":"'.Input::get($cfield['field-id']).'",';
										}
									}
									$jsonstringother = rtrim($jsonstringother,',');
									$jsonstringother .='}';
								}
								$validate = new Validate();
								$validate->check($_POST, $validation_list);

								if($validate->passed()) {
									$newmem = new Member();
									$memChar = new Member_characteristics();
									//edit codes
									$agent_id = (Input::get('agent_id')) ? implode(',',Input::get('agent_id')) : '';
									if(Input::get('edit')) {
										$id = Encryption::encrypt_decrypt('decrypt', Input::get('edit'));
										try {

											$newmem->update(
												array('lastname' => Input::get('lastname'),
													'firstname' => Input::get('firstname'),
													'middlename' => Input::get('middlename'),
													'birthdate' => strtotime(Input::get('birthdate')),
													'personal_address' => Input::get('personal_address'),
													'email' => Input::get('email'),
													'sg_year' => Input::get('sg_year'),
													'terms' => Input::get('terms'),
													'contact_number' => Input::get('contact_number'),
													'modified' => strtotime(date('Y/m/d H:i:s')),
													'area_code1' => Input::get('area_code1'),
													'area_code2' => Input::get('area_code2'),
													'fax_number' => Input::get('fax_number'),
													'cel_number' => Input::get('cel_number'),
													'cp_lastname' => Input::get('cp_lastname'),
													'cp_firstname' => Input::get('cp_firstname'),
													'payment_type' => Input::get('payment_type'),
													'credit_limit' => Input::get('credit_limit'),
													'tin_no' => Input::get('tin_no'),
													'remarks' => Input::get('remarks'),
													'member_since' => strtotime(Input::get('member_since')),
													'agent_id' => $agent_id,
													'with_inv' => Input::get('with_inv'),
													'salestype' => Input::get('salestype'),
													'tax_type' => Input::get('tax_type'),
													'member_num' => Input::get('member_num'),
													'k_type' => Input::get('k_type'),
													'region' => Input::get('region'),
													'jsonfield' => $jsonstringother
												), $id);

											Log::addLog(
												$user->data()->id,
												$user->data()->company_id,
												"Update Member " . Input::get('lastname'),
												"addterminal.php"
											);

											if($memChar->deleteMyCharacteristics($id)) {
												foreach(Input::get('char') as $c) {
													$memChar->create(array('member_id' => $id, 'mem_char_id' => $c));
												}
											}
											if(Input::get('username') && Input::get('password')) {
												$newUser = new User();
												$newUser->create(array(
													'lastname' => Input::get('lastname'),
													'firstname' => Input::get('firstname'),
													'middlename' => Input::get('middlename'),
													'username' => Input::get('username'),
													'password' => Hash::make(Input::get('password')),
													'is_active' => 1,
													'position_id' => Input::get('position'),
													'branch_id' => Input::get('branch_id'),
													'is_member' => 1,
													'company_id' => $user->data()->company_id,
													'created' => strtotime(date('Y/m/d H:i:s')),
													'modified' => strtotime(date('Y/m/d H:i:s')),
													'member_id' =>$id
												));
											}
											Session::flash('flash', $memberlbl.' information has been successfully updated');
											Redirect::to('members.php');
										} catch(Exception $e) {
											die($e->getMessage());
										}
									} else {
										// insert codes
										try {


											$newmemarr = array(
												'lastname' => Input::get('lastname'),
												'firstname' => Input::get('firstname'),
												'middlename' => Input::get('middlename'),
												'personal_address' => Input::get('personal_address'),
												'email' => Input::get('email'),
												'sg_year' => Input::get('sg_year'),
												'terms' => Input::get('terms'),
												'contact_number' => Input::get('contact_number'),
												'birthdate' => strtotime(Input::get('birthdate')),

												'company_id' => $user->data()->company_id,
												'is_active' => 1,
												'created' => strtotime(date('Y/m/d H:i:s')),
												'modified' => strtotime(date('Y/m/d H:i:s')),
												'area_code1' => Input::get('area_code1'),
												'area_code2' => Input::get('area_code2'),
												'fax_number' => Input::get('fax_number'),
												'cel_number' => Input::get('cel_number'),
												'cp_lastname' => Input::get('cp_lastname'),
												'cp_firstname' => Input::get('cp_firstname'),
												'payment_type' => Input::get('payment_type'),
												'credit_limit' => Input::get('credit_limit'),
												'tin_no' => Input::get('tin_no'),
												'remarks' => Input::get('remarks'),
												'member_since' => strtotime(Input::get('member_since')),
												'agent_id' => $agent_id,
												'with_inv' => Input::get('with_inv'),
												'salestype' => Input::get('salestype'),
												'tax_type' => Input::get('tax_type'),
												'member_num' => Input::get('member_num'),
												'k_type' => Input::get('k_type'),
												'region' => Input::get('region'),
												'jsonfield' => $jsonstringother
											);
											$newmem->create($newmemarr);

											Log::addLog(
												$user->data()->id,
												$user->data()->company_id,
												"Add Member " . Input::get('lastname'),
												"addterminal.php"
											);

											$lastid = $newmem->getInsertedId();
											foreach(Input::get('char') as $c) {
												$memChar->create(array('member_id' => $lastid, 'mem_char_id' => $c));
											}
											if(Input::get('username') && Input::get('password')) {
												$newUser = new User();
												$newUser->create(array(
													'lastname' => Input::get('lastname'),
													'firstname' => Input::get('firstname'),
													'middlename' => Input::get('middlename'),
													'username' => Input::get('username'),
													'password' => Hash::make(Input::get('password')),
													'is_active' => 1,
													'position_id' => Input::get('position'),
													'branch_id' => Input::get('branch_id'),
													'is_member' => 1,
													'company_id' => $user->data()->company_id,
													'created' => strtotime(date('Y/m/d H:i:s')),
													'modified' => strtotime(date('Y/m/d H:i:s')),
													'member_id' => $lastid,
												));
											}
										} catch(Exception $e) {
											die($e);
										}
										Session::flash('flash', 'You have successfully added a ' . $memberlbl);
										Redirect::to('members.php');
									}
									dump($_POST);
								} else {
									$el = '';
									echo "<div class='alert alert-danger'>";
									foreach($validate->errors() as $error) {
										$el .= escape($error) . "<br/>";
									}
									echo "$el</div>";
								}
							}
						}
					?>

					<form  action="" method="POST">
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
									Customer Name
									<input id="lastname" name="lastname" placeholder="Customer/Company Name" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($editMem->data()->lastname) : escape(Input::get('lastname')); ?>">
									<span class="help-block">Customer/Company Name</span>
									</div>
								</div>
								<div class="col-md-12" style='<?php echo (isset($c_address) && !empty($c_address)) ? '' :'display:none'; ?>'>
									<div class="form-group">
										Address
										<input  id="personal_address" name="personal_address" placeholder="Address" class="form-control" type="text" value="<?php echo isset($id) ? $editMem->data()->personal_address : escape(Input::get('personal_address')); ?>">
									</div>
								</div>
								<div style='<?php echo (isset($c_telephone) && !empty($c_telephone)) ? '' :'display:none'; ?>'>
								<div class="col-md-3" >
									<div class="form-group">
										Area Code
										<input id="area_code1" name="area_code1" placeholder="Area Code Tel" class="form-control" type="text" value="<?php echo isset($id) ? $editMem->data()->area_code1 : escape(Input::get('area_code1')); ?>">
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										Telephone number
										<input id="contact_number" name="contact_number" placeholder="Contact Number" class="form-control" type="text" value="<?php echo isset($id) ? $editMem->data()->contact_number : escape(Input::get('contact_number')); ?>">
									</div>
								</div>
								</div>
								<div style='<?php echo (isset($c_fax) && !empty($c_fax)) ? '' :'display:none'; ?>'>
									<div class="col-md-3">
										<div class="form-group">
											Area Code
											<input id="area_code2" name="area_code2" placeholder="Area Code Fax" class="form-control" type="text" value="<?php echo isset($id) ? $editMem->data()->area_code2 : escape(Input::get('area_code2')); ?>">
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											Fax number
											<input id="fax_number" name="fax_number" placeholder="Fax Number" class="form-control" type="text" value="<?php echo isset($id) ? $editMem->data()->fax_number : escape(Input::get('fax_number')); ?>">

										</div>
									</div>
								</div>
								<div class="col-md-3" style='<?php echo (isset($c_cellphone) && !empty($c_cellphone)) ? '' :'display:none'; ?>'>
									<div class="form-group">
										Cellphone Number
										<input id="cel_number" name="cel_number" placeholder="Cellphone Number" class="form-control" type="text" value="<?php echo isset($id) ? $editMem->data()->cel_number : escape(Input::get('cel_number')); ?>">
									</div>
								</div>
								<div class="col-md-3" style='<?php echo (isset($c_cellphone) && !empty($c_cellphone)) ? '' :'display:none'; ?>'>
									<div class="form-group">
										Gender
										<select name="gender" id="gender" class='form-control'>
											<option value="">Choose Gender</option>
											<option <?php echo (isset($id) && $editMem->data()->gender=='Male') ? 'selected' : ''; ?> value="Male">Male</option>
											<option <?php echo (isset($id) && $editMem->data()->gender=='Female') ? 'selected' : ''; ?> value="Female">Female</option>
										</select>
									</div>
								</div>
								<div class="col-md-3" style='<?php echo (isset($c_cellphone) && !empty($c_cellphone)) ? '' :'display:none'; ?>'>
									<div class="form-group">
										Birth Date
										<input id="birthdate" name="birthdate" placeholder="Birth Date" class="form-control" type="text" value="<?php echo isset($id) ? Date('m/d/Y',$editMem->data()->birthdate) : escape(Input::get('birthdate')); ?>">
									</div>
								</div>

							</div>
							<div class="row" style='<?php echo (isset($c_contact1) && !empty($c_contact1)) ? '' :'display:none'; ?>'>
								<h4>Contact person 1</h4>
								<div class="col-md-4">
									<div class="form-group">
										First Name
										<input id="firstname" name="firstname" placeholder="First Name" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($editMem->data()->firstname) : escape(Input::get('firstname')); ?>">
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										Last Name
										<input id="middlename" name="middlename" placeholder="Last Name" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($editMem->data()->middlename) : escape(Input::get('middlename')); ?>">
									</div>
								</div>
							</div>
							<div class="row"  style='<?php echo (isset($c_contact2) && !empty($c_contact2)) ? '' :'display:none'; ?>'>
								<h4>Contact person 2</h4>
								<div class="col-md-4">
									<div class="form-group">
										First Name
										<input id="cp_firstname" name="cp_firstname" placeholder="First Name" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($editMem->data()->cp_firstname) : escape(Input::get('cp_firstname')); ?>">
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										Last Name
										<input id="cp_lastname" name="cp_lastname" placeholder="Last Name" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($editMem->data()->cp_lastname) : escape(Input::get('cp_lastname')); ?>">
									</div>
								</div>
							</div>
							<div class="row">
								<h4>Other details</h4>
								<div class="col-md-4" style='<?php echo (isset($c_terms) && !empty($c_terms)) ? '' :'display:none'; ?>'>
									<div class="form-group">
										Terms
										<input id="terms" name="terms" placeholder="Terms in days" class="form-control input-md" type="text" value="<?php echo isset($id) ? $editMem->data()->terms : escape(Input::get('terms')); ?>">
									</div>
								</div>
								<div class="col-md-4" style='<?php echo (isset($c_payment_type) && !empty($c_payment_type)) ? '' :'display:none'; ?>'>
									<div class="form-group">
									Payment type
									<input id="payment_type" name="payment_type" placeholder="Payment type" class="form-control input-md" type="text" value="<?php echo isset($id) ? $editMem->data()->payment_type: escape(Input::get('payment_type')); ?>">
									</div>
								</div>
								<div class="col-md-4" style='<?php echo (isset($c_credit_limit) && !empty($c_credit_limit)) ? '' :'display:none'; ?>'>
									<div class="form-group">
										Credit limit
										<input id="credit_limit" name="credit_limit" placeholder="Payment type" class="form-control input-md" type="text" value="<?php echo isset($id) ? $editMem->data()->credit_limit: escape(Input::get('credit_limit')); ?>">
									</div>
								</div>
								<div class="col-md-4" style='<?php echo (isset($c_tin) && !empty($c_tin)) ? '' :'display:none'; ?>'>
									<div class="form-group">
										TIN
										<input id="tin_no" name="tin_no" placeholder="TIN" class="form-control input-md" type="text" value="<?php echo isset($id) ? $editMem->data()->tin_no: escape(Input::get('tin_no')); ?>">
									</div>
								</div>
								<div class="col-md-8" style='<?php echo (isset($c_remarks) && !empty($c_remarks)) ? '' :'display:none'; ?>'>
									<div class="form-group">
										Remarks
										<input id="remarks" name="remarks" placeholder="Remarks" class="form-control input-md" type="text" value="<?php echo isset($id) ? $editMem->data()->remarks: escape(Input::get('remarks')); ?>">
									</div>
								</div>
								<div class="col-md-4" style='<?php echo (isset($c_email) && !empty($c_email)) ? '' :'display:none'; ?>'>
									<div class="form-group">
										Email
										<input id="email" name="email" placeholder="Email" class="form-control input-md" type="text" value="<?php echo isset($id) ? $editMem->data()->email : escape(Input::get('email')); ?>">
									</div>
								</div>
								<div class="col-md-4" style='<?php echo (isset($c_member_since) && !empty($c_member_since)) ? '' :'display:none'; ?>'>
									<div class="form-group">
										<?php echo $memberlbl; ?> Since
										<input id="member_since" name="member_since" placeholder="(mm/dd/yyyy)" class="form-control input-md" type="text" value="<?php echo isset($id) ? date('m/d/Y', $editMem->data()->member_since) : escape(Input::get('member_since')); ?>">
									</div>
								</div>
								<div class="col-md-4" style='<?php echo (isset($c_agent) && !empty($c_agent)) ? '' :'display:none'; ?>'>
									<div class="form-group">
										Agent
										<?php
											$crudcls = new Crud();
											$allusers = $crudcls->get_active('users', array('company_id', '=', $user->data()->company_id));
											if($allusers) {
												?>
												<select name="agent_id[]" id="agent_id" class='form-control input-md' multiple>
													<option value=""></option> <?php
														foreach($allusers as $p) {
																$pos_cur = new Position($p->position_id);
																$curpermissions = json_decode($pos_cur->data()->permisions,true);
																if(!isset($curpermissions['wh_agent'])) continue;
															?>
															<option  value="<?php echo $p->id; ?>"> <?php echo ucwords($p->lastname . ", " . $p->firstname); ?></option>								<?php
														}
													?>
												</select>
											<?php } ?>
									</div>
								</div>
								<div class="col-md-4" style='<?php echo (isset($c_invoice) && !empty($c_invoice)) ? '' :'display:none'; ?>'>
									<div class="form-group">
										Invoice
										<select name="with_inv" id="with_inv" class='form-control input-md'>
											<option value="0"
												<?php
													if(isset($id)) {
														echo (isset($editMem->data()->with_inv) && $editMem->data()->with_inv == '0') ? ' selected' : '';
													}
												?>
												>Without Invoice</option>
											<option value="1"
												<?php
													if(isset($id)) {
														echo (isset($editMem->data()->with_inv) && $editMem->data()->with_inv == '1') ? ' selected' : '';
													}
												?>
												>With Invoice</option>
										</select>
									</div>
								</div>
								<div class="col-md-4" style='<?php echo (isset($c_sales_man) && !empty($c_sales_man)) ? '' :'display:none'; ?>'>
									<div class="form-group">
										Sales Type
										<select name="salestype" id="salestype" class='form-control'>
											<?php
												$sales_type = new Sales_type();
												$sales_types = $sales_type->get_active('salestypes',array('company_id','=',$user->data()->company_id));
												foreach($sales_types as $st){
													$curid = (isset($id)) ?  $editMem->data()->salestype : 0;
													if($st->id == $curid) {
														$selected = 'selected';
													} else {
														$selected = '';
													}
													echo  "<option value='$st->id' $selected>$st->name</option>";
												}
											?>
										</select>
									</div>
								</div>
								<div class="col-md-4" style='<?php echo (isset($c_tax_type) && !empty($c_tax_type)) ? '' :'display:none'; ?>'>
									<div class="form-group">
										Tax Type
										<input id="tax_type" name="tax_type" placeholder="Tax Type" class="form-control input-md" type="text" value="<?php echo isset($id) ?  $editMem->data()->tax_type : escape(Input::get('tax_type')); ?>">
									</div>
								</div>

								<div class="col-md-4" style='<?php echo (isset($c_member_num) && !empty($c_member_num)) ? '' :'display:none'; ?>'>
									<div class="form-group">
										Member Code
										<input id="member_num" name="member_num" placeholder="Member Code" class="form-control input-md" type="text" value="<?php echo isset($id) ?  $editMem->data()->member_num : escape(Input::get('member_num')); ?>">
									</div>
								</div>
								<div class="col-md-4" style='<?php echo (isset($c_member_num) && !empty($c_member_num)) ? '' :'display:none'; ?>'>
									<div class="form-group">
										Type
										<select class='form-control' name="k_type" id="k_type">
											<option value=""></option>
											<option <?php
												        if(isset($id)) {
													        echo (isset($editMem->data()->k_type) && $editMem->data()->k_type == '1') ? ' selected' : '';
												        }
											        ?> value="1">Distributor</option>
											<option <?php
												if(isset($id)) {
													echo (isset($editMem->data()->k_type) && $editMem->data()->k_type == '2') ? ' selected' : '';
												}
											?> value="2">Franchisee</option>
											<option <?php
												        if(isset($id)) {
													        echo (isset($editMem->data()->k_type) && $editMem->data()->k_type == '3') ? ' selected' : '';
												        }
											        ?> value="3">Agent</option>
											<option <?php
												        if(isset($id)) {
													        echo (isset($editMem->data()->k_type) && $editMem->data()->k_type == '4') ? ' selected' : '';
												        }
											        ?> value="4">Affiliate</option>
											<option <?php
												if(isset($id)) {
													echo (isset($editMem->data()->k_type) && $editMem->data()->k_type == '5') ? ' selected' : '';
												}
											?> value="5">Supplementary</option>
										</select>

									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										Area/Region
										<input id="region" name="region" placeholder="Area" class="form-control input-md" type="text" value="<?php echo isset($id) ?  $editMem->data()->region : escape(Input::get('region')); ?>">
									</div>
								</div>
								<?php
									if($otherfield){
										foreach($otherfield as $cfield){
											if($cfield['field-visibility'] == 1){
												if(isset($id)){
													$jsonind = json_decode($editMem->data()->jsonfield,true);
												}
												?>
													<div class="col-md-4">
														<div class="form-group">
														<?php echo  $cfield['field-label']; ?>
														<input id="<?php echo $cfield['field-id']?>" name="<?php echo $cfield['field-id']?>" placeholder="<?php echo $cfield['field-label']?>" class="form-control input-md" type="text" value="<?php echo isset($jsonind[$cfield['field-id']]) ?  $jsonind[$cfield['field-id']] : ''; ?>">
														</div>
													</div>

												<?php
											}
										}
									}

								?>
							</div>
						<div class="form-group">

							<label style='display:none;' class="col-md-1 control-label" for="sg_year">Agreement Years</label>
							<div style='display:none;'  class="col-md-3">
								<input id="sg_year" name="sg_year" placeholder="Agreement Years" class="form-control input-md" type="text" value="<?php echo isset($id) ? $editMem->data()->sg_year : escape(Input::get('sg_year')); ?>">
								<span class="help-block"></span>
							</div>
						</div>






							<div style="clear:both;"></div>
							<legend><?php echo $memberlbl; ?> Characteristics</legend>
							<div class="form-group">
								<?php
									$char = new Member_char_list();
									$chars = $char->get_active('member_characteristics_list', array('company_id', '=', $user->data()->company_id));
									if($chars) {
										foreach($chars as $c):
											?>
											<div class="col-md-3">
												<label class="checkbox-inline" for="<?php echo $c->id; ?>">
													<input class='charcheckbox' name="char[]" id="<?php echo $c->id; ?>" value="<?php echo $c->id; ?>" type="checkbox" <?php
														if(isset($myChar)) {
															foreach($myChar as $cc) {
																echo ($cc->mem_char_id == $c->id) ? 'checked' : '';
															}
														}
													?> > <span><?php echo $c->name; ?></span> </label>
											</div>				<?php
										endforeach;
									} else {
										?>
										<div class="alert alert-info">No Characteristics Yet</div>			<?php
									}
								?>
							</div>
							<br><br> <?php if(!$withUserAccount) {
								?>
								<legend>Create User Account(Optional)</legend>
								<div class="form-group">
									<label class="col-md-1 control-label" for="username">Username</label>

									<div class="col-md-3">
										<input id="username" name="username" placeholder="Username" class="form-control input-md" type="text" value="<?php echo escape(Input::get('username')); ?>">
										<span class="help-block">Enter Username</span>
									</div>
									<label class="col-md-1 control-label" for="password">Password</label>

									<div class="col-md-3">
										<input id="password" name="password" placeholder="Password" class="form-control input-md" type="password" value="">
										<span class="help-block">Enter Password</span>
									</div>
									<label class="col-md-1 control-label" for="retype_password">Confirm Password</label>

									<div class="col-md-3">
										<input id="retype_password" name="retype_password" placeholder="Confirm Password" class="form-control input-md" type="password" value="">
										<span class="help-block">Confirm Password</span>
									</div>

								</div>
								<div class="form-group">
									<label class="col-md-1 control-label" for="position">Position</label>

									<div class="col-md-3">
										<?php
											$listPosition = new Position();
											$positions = $listPosition->get_active('positions', array('company_id', '=', $user->data()->company_id));
											if($positions) {
												?>
												<select readonly="true" name="position" id="position" class='form-control input-md'>
													<option value="">--Choose position--</option> <?php
														foreach($positions as $p) {
															if(strtolower(trim($p->position)) == 'client' || strtolower(trim($p->position)) == 'member' ) {
																$selected = 'selected';
															} else {
																$selected = '';
															}
															?>
															<option  value="<?php echo $p->id; ?>" <?php echo $selected; ?> > <?php echo $p->position; ?></option>								<?php
														}
													?>
												</select>				<?php } else {
												echo "<a href='addposition.php'>PLEASE CREATE POSITION FIRST</a>";
											} ?> <span class="help-block"></span>
									</div>
									<label class="col-md-1 control-label" for="branch_id">Branch</label>
									<div class="col-md-3">
										<select id="branch_id" name="branch_id" class="form-control">

											<?php
												$branch = new Branch();
												$branches =  $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));
												foreach($branches as $b){
													$a = isset($id) ? $edituser->branch_id : escape(Input::get('branch_id'));

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
								</div>		<?php
							}
							?> <!-- Button (Double) -->
							<div class="form-group">
								<div class="col-md-8">
									<input type='submit' class='btn btn-success' name='btnSave' value='SAVE' />
									<input type='hidden' name='token' value=<?php echo Token::generate(); ?>>
									<input type='hidden' name='edit' value=<?php echo isset($id) ? escape(Encryption::encrypt_decrypt('encrypt', $id)) : 0; ?>>
								</div>
							</div>

					</form>
				</div>

			</div>
		</div>
	</div> <!-- end page content wrapper-->

	<script>
		$(function(){
			var agent_edit = "<?php echo (isset($id)) ?  $editMem->data()->agent_id : ''  ?>";
			var agent_arr = [];
			if(agent_edit.indexOf(',') >0){
				agent_arr = agent_edit.split(',');
			} else {
				if(agent_edit){
					agent_arr.push(agent_edit);
				}
			}

			$('#agent_id').select2({
				allowClear:true,
				placeholder:'Select Agent'
			}).select2('val',agent_arr);
			$('body').on('change','#agent_id',function(){

			});
		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>