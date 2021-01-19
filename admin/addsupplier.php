<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('supplier_m')){
		// redirect to denied page
		Redirect::to(1);
	}
	if(isset($_GET['edit'])) {
		$editid = $_GET['edit'];
	} else {
		$editid = 0;
	}
	$cf = new Custom_field();
	$cfd = new Custom_field_details();
	$getsupplierdet = $cf->getcustomform('suppliers',$user->data()->company_id);
	$label_name = isset($getsupplierdet->label_name)? strtoupper($getsupplierdet->label_name):'Supplier';
	$description = $cfd->getIndData('description',$user->data()->company_id,$getsupplierdet->id);
	$otherfield = isset($getsupplierdet->other_field)?$getsupplierdet->other_field:'';
	if($otherfield){
		$otherfield = json_decode($otherfield,true);
	}

	if(($description)){
		if($description->field_label){
			$desc_label = $description->field_label;
		}
		if($description->is_visible == 0){
			$desc_visible = 'display:none;';
		}
	}
	if(!$desc_label){
		$desc_label = 'Address';
	}
	if(!$desc_visible){
		$desc_visible = '';
	}


?>

	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<?php include 'includes/supplier_nav.php'; ?>
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					<?php echo isset($editid) && !empty($editid) ? "EDIT $label_name" : "ADD $label_name"; ?>
				</h1>
			</div>
			<div class="row">
				<div class="col-md-12">

					<?php
						if(isset($editid) && !empty($editid)) {
							// edit
							$id = Encryption::encrypt_decrypt('decrypt', $editid);
							// get the data base on branch id
							$supplier = new Supplier($id);
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
								);
								// get id in update
								if(isset($description->is_visible)){
									if($description->is_visible == 1){
										$valdesc = array('required'=> true,'max' => 200);
									}
								}else {
									$valdesc = array('required'=> true,'max' => 200);
								}
								if($valdesc){
									$validation_list['description'] = $valdesc;
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

								if(!Input::get('edit')) {
									$additionalvalidation = array('unique' => 'suppliers');
									$finalvalidation=array_merge($validation_list['name'],$additionalvalidation);
									$validation_list['name'] = $finalvalidation;
								}

								$validate = new Validate();
								$validate->check($_POST, $validation_list);
								if($validate->passed()){
									$supplier = new Supplier();
									//edit codes
									if(Input::get('edit')){
										$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
										try{
											$supplier->update(array(
												'name' => Input::get('name'),
												'description' => Input::get('description'),
												'sup_type' => Input::get('sup_type'),
												'contact_person' => Input::get('contact_person'),
												'contact_number' => Input::get('contact_number'),
												'jsonfield'=>$jsonstringother
											), $id);
											Log::addLog($user->data()->id,$user->data()->company_id,"Update Supplier ".Input::get('name') ,"addsupplier.php");

											Session::flash('flash','Supplier information has been successfully updated');
											Redirect::to('supplier.php');
										} catch(Exception $e) {
											die($e->getMessage());
										}
									} else {
										// insert codes
										try {
											$supplier->create(array(
												'name' => Input::get('name'),
												'description' => Input::get('description'),
												'jsonfield'=>$jsonstringother,
												'created' => strtotime(date('Y/m/d H:i:s')),
												'modified' => strtotime(date('Y/m/d H:i:s')),
												'sup_type' => Input::get('sup_type'),
												'contact_person' => Input::get('contact_person'),
												'contact_number' => Input::get('contact_number'),
												'company_id' => $user->data()->company_id,
												'is_active' => 1
											));

											Log::addLog($user->data()->id,$user->data()->company_id,"Insert Supplier ".Input::get('name') ,"addsupplier.php");
										} catch(Exception $e){
											die($e);
										}
										Session::flash('flash','You have successfully added a supplier');
										Redirect::to('supplier.php');
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


							<legend><?php echo $label_name=  ucfirst(strtolower($label_name)); ?> Information</legend>


							<div class="form-group">
								<label class="col-md-4 control-label" for="name"><?php echo $label_name; ?></label>
								<div class="col-md-4">
									<input id="name" name="name" placeholder="<?php echo $label_name; ?> Name" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($supplier->data()->name) : escape(Input::get('name')); ?>">
									<span class="help-block">The name of the <?php echo $label_name; ?> can consists of letters and numbers</span>
								</div>
							</div>

							<!-- Text input-->
							<div class="form-group">
								<label class="col-md-4 control-label" for="description"><?php echo $label_name; ?> <?php echo $desc_label; ?></label>
								<div class="col-md-4">
									<input id="description" name="description" placeholder="<?php echo $desc_label; ?>" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($supplier->data()->description) :  escape(Input::get('description')); ?>">
									<span class="help-block"><?php echo $desc_label; ?> of the <?php echo $label_name; ?></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="sup_type">Type</label>
								<div class="col-md-4">
									<select name="sup_type" id="sup_type" class='form-control'>
										<option value="1"
											<?php
												if(isset($id)) {
													echo (isset($supplier->data()->sup_type) && $supplier->data()->sup_type == '1') ? ' selected' : '';
												}
											?>
											>
											International
										</option>
										<option value="0"
											<?php
												if(isset($id)) {
													echo (isset($supplier->data()->sup_type) && $supplier->data()->sup_type == '0') ? ' selected' : '';
												}
											?>
											>Local</option>
									</select>
								</div>
							</div>
							<!-- Text input-->
							<div class="form-group">
								<label class="col-md-4 control-label" for="contact_person">Contact Person</label>
								<div class="col-md-4">
									<input id="contact_person" name="contact_person" placeholder="Contact Person" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($supplier->data()->contact_person) :  escape(Input::get('contact_person')); ?>">
									<span class="help-block">Enter Contact Person</span>
								</div>
							</div>	<!-- Text input-->
							<div class="form-group">
								<label class="col-md-4 control-label" for="contact_number">Contact Number</label>
								<div class="col-md-4">
									<input id="contact_number" name="contact_number" placeholder="Contact Number" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($supplier->data()->contact_number) :  escape(Input::get('contact_number')); ?>">
									<span class="help-block">Enter Contact Number</span>
								</div>
							</div>
							<?php
								if($otherfield){


									foreach($otherfield as $cfield){
										if($cfield['field-visibility'] == 1){
											if(isset($id)){
												$jsonind = json_decode($supplier->data()->jsonfield,true);

											}
											?>
											<div class="form-group">
												<label class="col-md-4 control-label" for="<?php echo $cfield['field-id']?>"><?php echo  $cfield['field-label']; ?></label>
												<div class="col-md-4">
													<input id="<?php echo $cfield['field-id']?>" name="<?php echo $cfield['field-id']?>" placeholder="<?php echo $cfield['field-label']?>" class="form-control input-md" type="text" value="<?php echo isset($jsonind[$cfield['field-id']]) ?  $jsonind[$cfield['field-id']] : ''; ?>">
												</div>
											</div>
											<?php
										}
									}
								}
							?>
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