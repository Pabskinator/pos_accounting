<?php
	// $user have all the properties and method of the current user


	require_once '../includes/admin/page_head2.php';

	if(!$user->hasPermission('member')) {
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

	$getstationdet = $cf->getcustomform('stations',$user->data()->company_id);

	$label_name = isset($getstationdet->label_name)? strtoupper($getstationdet->label_name):'STATION';
	$description = $cfd->getIndData('description',$user->data()->company_id,$getstationdet->id);
	$region = $cfd->getIndData('region',$user->data()->company_id,$getstationdet->id);
	$brand = $cfd->getIndData('brand',$user->data()->company_id,$getstationdet->id);
	$package = $cfd->getIndData('package',$user->data()->company_id,$getstationdet->id);
	 $otherfield = isset($getstationdet->other_field)?$getstationdet->other_field:'';
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

	if(($region)){
		if($region->field_label){
			$region_label = $region->field_label;
		}
		if($region->is_visible == 0){
			$region_visible = 'display:none;';
		}
	}
	if(!$region_label){
		$region_label = 'Region';
	}
	if(!$region_visible){
		$region_visible = '';
	}

	if(($brand)){
		if($brand->field_label){
			$brand_label = $brand->field_label;
		}
		if($brand->is_visible == 0){
			$brand_visible = 'display:none;';
		}else {

		}
	}
	if(!$brand_label){
		$brand_label = 'Brand';
	}
	if(!$brand_visible){
		$brand_visible = '';
	}

	if(($package)){
		if($package->field_label){
			$package_label = $package->field_label;
		}
		if($package->is_visible == 0) {
			$package_visible = 'display:none;';
		}
	}
	if(!$package_label){
		$package_label = 'Package';
	}
	if(!$package_visible){
		$package_visible = '';
	}

?>

	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					<?php echo isset($editid) && !empty($editid) ? "EDIT $label_name" : "ADD $label_name"; ?>
				</h1>
			</div>
			<?php include 'includes/station_nav.php'; ?> <br> <br>
			<div class="row">
				<div class="col-md-12">

					<?php
						if(isset($editid) && !empty($editid)) {
							// edit
							$id = Encryption::encrypt_decrypt('decrypt', $editid);
							// get the data base on branch id
							$station = new Station($id);
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
									'member_id' => array(
										'required'=> true,
										'isnumber' => true
									)

								);
								if(isset($description->is_visible)){
									if($description->is_visible == 1){
										$valdesc = array('required'=> true,'max' => 200);
									}
								}else {
									$valdesc = array('required'=> true,'max' => 200);
								}
								if($valdesc){
									$validation_list['address'] = $valdesc;
								}
								if(isset($region->is_visible)){
									if($region->is_visible == 1){
										$valreg = array('required'=> true,'max' => 50);
									}
								}else {
									$valreg = array('required'=> true,'max' => 50);
								}
								if($valreg){
									$validation_list['region'] = $valreg;
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
								if($validate->passed()){
									$station = new Station();
									//edit codes
									if(Input::get('edit')){
										$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
										try{
											$updatearray= array(
												'name' => Input::get('name'),
												'member_id' => Input::get('member_id'),
												'modified' => strtotime(date('Y/m/d H:i:s')),
												'brand' => Input::get('brand'),
												'store_type' => Input::get('store_type'),
												'package' => Input::get('package'),
												'jsonfield'=>$jsonstringother
											);
											if(isset($description->is_visible)){
												if($description->is_visible == 1){
													$updatearray['address'] = Input::get('address');
												}
											} else {
												$updatearray['address'] = Input::get('address');
											}
											if(isset($description->is_visible)){
												if($description->is_visible == 1){
													$updatearray['region'] = Input::get('region');
												}
											} else {
												$updatearray['region'] = Input::get('region');
											}
											$station->update($updatearray, $id);
											Session::flash('stationflash','Station information has been successfully updated');
											Redirect::to('station.php');
										} catch(Exception $e) {
											die($e->getMessage());
										}
									} else {
										// insert codes
										try {
											$insertarray= array(
												'name' => Input::get('name'),
												'member_id' => Input::get('member_id'),
												'created' => strtotime(date('Y/m/d H:i:s')),
												'modified' => strtotime(date('Y/m/d H:i:s')),
												'is_active' => 1,
												'company_id' => $user->data()->company_id,
												'package' => Input::get('package'),
												'brand' => Input::get('brand'),
												'store_type' => Input::get('store_type'),
												'jsonfield'=>$jsonstringother
											);
											if(isset($description->is_visible)){
												if($description->is_visible == 1){
													$insertarray['address'] = Input::get('address');
												}
											} else {
												$insertarray['address'] = Input::get('address');
											}
											if(isset($region->is_visible)){
												if($region->is_visible == 1){
													$insertarray['region'] = Input::get('region');
												}
											} else {
												$insertarray['region'] = Input::get('region');
											}
											  $station->create($insertarray);

										} catch(Exception $e){
											die($e);
										}
										Session::flash('stationflash','You have successfully added a Station');
										Redirect::to('station.php');
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


							<legend><?php echo $label_name; ?> Information</legend>

							
							<div class="form-group">
								<label class="col-md-4 control-label" for="name">Name</label>
								<div class="col-md-4">
									<input id="stationname" name="name" placeholder="Name" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($station->data()->name) : escape(Input::get('name')); ?>">
									<span class="help-block"></span>
								</div>
							</div>
							<div class="form-group" style='<?php echo $desc_visible; ?>'>
								<label class="col-md-4 control-label" for="address"><?php echo $desc_label; ?> </label>
								<div class="col-md-4">
									<input id="address" name="address" placeholder="<?php echo $desc_label; ?> " class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($station->data()->address) : escape(Input::get('address')); ?>">
									<span class="help-block"></span>
								</div>
							</div>
							<div class="form-group" style='<?php echo $region_visible; ?>'>
								<label class="col-md-4 control-label" for="region"><?php echo $region_label; ?></label>
								<div class="col-md-4">
									<select class='form-control' id='region' name='region'>
										<option value=''>--Select Region--</option>
										<option value='NCR'
											<?php
												if(isset($id)){
													echo (isset($station->data()->region) && $station->data()->region == 'NCR') ? ' selected' : '';
												}
											?>
											>NCR</option>
										<option value='CAR'
											<?php
												if(isset($id)){
													echo (isset($station->data()->region) && $station->data()->region == 'CAR') ? ' selected' : '';
												}
											?>
											>CAR</option>
										<option value='REGION I'
											<?php
												if(isset($id)){
													echo (isset($station->data()->region) && $station->data()->region == 'REGION I') ? ' selected' : '';
												}
											?>
											>REGION I</option>
										<option value='REGION II'
											<?php
												if(isset($id)){
													echo (isset($station->data()->region) && $station->data()->region == 'REGION II') ? ' selected' : '';
												}
											?>
											>REGION II</option>
										<option value='REGION III'
											<?php
												if(isset($id)){
													echo (isset($station->data()->region) && $station->data()->region == 'REGION III') ? ' selected' : '';
												}
											?>
											>REGION III</option>
										<option value='REGION IV-A'
											<?php
												if(isset($id)){
													echo (isset($station->data()->region) && $station->data()->region == 'REGION IV-A') ? ' selected' : '';
												}
											?>
											>REGION IV-A</option>
										<option value='REGION IV-B'
											<?php
												if(isset($id)){
													echo (isset($station->data()->region) && $station->data()->region == 'REGION IV-B') ? ' selected' : '';
												}
											?>
											>REGION IV-B</option>
										<option value='REGION V'
											<?php
												if(isset($id)){
													echo (isset($station->data()->region) && $station->data()->region == 'REGION V') ? ' selected' : '';
												}
											?>
											>REGION V</option>
										<option value='REGION VI'
											<?php
												if(isset($id)){
													echo (isset($station->data()->region) && $station->data()->region == 'REGION VI') ? ' selected' : '';
												}
											?>
											>REGION VI</option>
										<option value='REGION VII'
											<?php
												if(isset($id)){
													echo (isset($station->data()->region) && $station->data()->region == 'REGION VII') ? ' selected' : '';
												}
											?>
											>REGION VII</option>
										<option value='REGION VIII'
											<?php
												if(isset($id)){
													echo (isset($station->data()->region) && $station->data()->region == 'REGION VIII') ? ' selected' : '';
												}
											?>
											>REGION VIII</option>
										<option value='REGION IX'
											<?php
												if(isset($id)){
													echo (isset($station->data()->region) && $station->data()->region == 'REGION IX') ? ' selected' : '';
												}
											?>
											>REGION IX</option>
										<option value='REGION X'
											<?php
												if(isset($id)){
													echo (isset($station->data()->region) && $station->data()->region == 'REGION X') ? ' selected' : '';
												}
											?>
											>REGION X</option>
										<option value='REGION XI'
											<?php
												if(isset($id)){
													echo (isset($station->data()->region) && $station->data()->region == 'REGION XI') ? ' selected' : '';
												}
											?>
											>REGION X</option>
										<option value='REGION XII'
											<?php
												if(isset($id)){
													echo (isset($station->data()->region) && $station->data()->region == 'REGION XII') ? ' selected' : '';
												}
											?>
											>REGION XII</option>
										<option value='REGION XIII'
											<?php
												if(isset($id)){
													echo (isset($station->data()->region) && $station->data()->region == 'REGION XIII') ? ' selected' : '';
												}
											?>
											>REGION XIII</option>
										<option value='ARMM'
											<?php
												if(isset($id)){
													echo (isset($station->data()->region) && $station->data()->region == 'ARMM') ? ' selected' : '';
												}
											?>
											>ARMM</option>

									</select>
									<span class="help-block"></span>
								</div>

							</div>


							<div class="form-group">
								<label class="col-md-4 control-label" for="member_id"><?php echo MEMBER_LABEL; ?></label>
								<div class="col-md-4">
									<select id="member_id" name="member_id" class="form-control">
										<option value=''>--Select <?php echo MEMBER_LABEL; ?>--</option>
										<?php
											$mlist = new Member();
											$members =  $mlist->get_active('members',array('company_id' ,'=',$user->data()->company_id));
											foreach($members as $b){
												$a = isset($id) ? $station->data()->member_id : escape(Input::get('member_id'));

												if($a==$b->id){
													$selected='selected';
												} else {
													$selected='';
												}
											?>
											<option value='<?php echo $b->id ?>' <?php echo $selected ?>><?php echo escape($b->lastname . ", " . $b->firstname);?> </option>
											<?php
											}
										?>
									</select>
									<span class="help-block"></span>
								</div>

							</div>
							<div class="form-group" style='<?php echo $brand_visible; ?>'>
								<label class="col-md-4 control-label" for="store_type">Store Type</label>
								<div class="col-md-4">
									<select id="store_type" name="store_type" class="form-control">
										<option value=''>Select type</option>
										<option value="1"
											<?php
												if(isset($id)){
													echo (isset($station->data()->store_type) && $station->data()->store_type == 1) ? ' selected' : '';
												}
											?>
											>
											WRS
										</option>
										<option value="2"
											<?php
												if(isset($id)){
													echo (isset($station->data()->store_type) && $station->data()->store_type == 2) ? ' selected' : '';
												}
											?>
											>
											Laundry Best
										</option>
									</select>
									<span class="help-block"></span>
								</div>

							</div>
							<div class="form-group" style='<?php echo $brand_visible; ?>'>
								<label class="col-md-4 control-label" for="brand"><?php echo $brand_label; ?></label>
								<div class="col-md-4">
									<?php
										$brandcls = new Brand();
										$brandlist = $brandcls->get_active('brands',array('company_id','=',$user->data()->company_id));
									?>
									<select class='form-control' name="brand" id="brand">
										<option value="">--Select <?php echo $brand_label; ?></option>
										<?php
											if(isset($id)){
												$editbra = $station->data()->brand;
											} else {
												$editbra=Input::get('brand');
											}
											foreach($brandlist as $bra){
												$selectbra ='';
												if($bra->name == $editbra){
													$selectbra = 'selected';
												}
												?>
												<option value="<?php echo escape($bra->name); ?>" <?php echo $selectbra; ?>><?php echo escape($bra->name); ?></option>
												<?php
											}
										?>
									</select>
									<span class="help-block"></span>
								</div>
							</div>
							<div class="form-group"  style='<?php echo $package_visible; ?>'>
								<label class="col-md-4 control-label" for="package"><?php echo $package_label; ?></label>
								<div class="col-md-4">
									<?php
										$packagecls = new Package();
										$packagelist = $packagecls->get_active('packages',array('company_id','=',$user->data()->company_id));
									?>
									<select class='form-control' name="package" id="package">
										<option value="">--Select <?php echo $package_label;?></option>
										<?php
											if(isset($id)){
												$editpac = $station->data()->package;
											} else {
												$editpac=Input::get('package');
											}
											foreach($packagelist as $pac){
												$selectpac ='';
												if($pac->name == $editpac){
													$selectpac = 'selected';
												}
												?>
												<option value="<?php echo escape($pac->name); ?>" <?php echo $selectpac; ?>><?php echo escape($pac->name); ?></option>
												<?php
											}
										?>
									</select>
									<span class="help-block"></span>
							</div>
							</div>
							<?php
								if($otherfield){
									foreach($otherfield as $cfield){
										if($cfield['field-visibility'] == 1){
											if(isset($id)){
												$jsonind = json_decode($station->data()->jsonfield,true);

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