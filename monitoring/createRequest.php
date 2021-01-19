<?php

	
    require_once '../includes/monitoring/page_head.php';
	if(!$user->hasPermission('dashboard')){
		// redirect to denied page
	//	Redirect::to(1);
	}
	if(isset($_GET['process'])) {
		$process_id = $_GET['process'];
	} else {
		$process_id = 0;

	}
	error_reporting(0);

?>


		<!-- Sidebar -->
		<?php include_once '../includes/monitoring/sidebar.php';?>
		<!-- Page content -->
		<div id="page-content-wrapper">

			<!-- Keep all page content within the page-content inset div! -->
			<div class="container-fluid" style='padding:20px;'>
				<?php 
					if(Session::exists('flash')){
					echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('flash')."</div>";
					}	
					$fr = new FormRequest();
					$forms = $fr->getForms($process_id);
				

					if($forms){
						?>
					
					<form class="form-horizontal" action="" method="POST">
						<fieldset>
						<legend>Create Request</legend>
						<?php
						$arrOldVal = [];
						$arrhaserror = [];
					if (Input::exists()){
							// check token if match to our token
						if(Token::check(Input::get('token'))){
							$errors = array();

							unset($_POST['btnSave']);
							unset($_POST['edit']); 
							unset($_POST['token']);

							foreach ($_POST as $key => $value) {
								$checkForm = new FormRequest($key);
								$arrOldVal[$key] = $value;
								if($checkForm->data()->is_required == 1 && !$value){
									$errors[] = $checkForm->data()->label . " is required";
									$arrhaserror[] = $key;
								}
								if($checkForm->data()->data_type == 'date' && $value){ // check if date
									if(strpos($value, '/') >0){
									$dts = explode('/', $value);
									if(checkdate($dts[0], $dts[1], $dts[2])){
										
									}else {
										$errors[] = $checkForm->data()->label . " is not a valid Date";
										$arrhaserror[] = $key;
									}
									} else {
										$errors[] = $checkForm->data()->label . " is not a valid Date";
										$arrhaserror[] = $key;
									}					
								}
								if($checkForm->data()->data_type == 'int'){
									if(!is_numeric($value) && $value){
										$errors[] = $checkForm->data()->label . " is not a valid number";
										$arrhaserror[] = $key;
									} 
								}
								if($checkForm->data()->max_length < strlen($value) && $checkForm->data()->max_length != 0 && $value){
										$errors[] = $checkForm->data()->label . " must be a maximun of " .$checkForm->data()->max_length . " characters";
										$arrhaserror[] = $key;
								}
								if($checkForm->data()->min_length > strlen($value) && $checkForm->data()->min_length != 0 && $value){
										$errors[] = $checkForm->data()->label . " must be a minimun of " .$checkForm->data()->min_length . " characters";
										$arrhaserror[] = $key;
								}
							}
							if(count($errors) > 0){
								echo "<p><strong>Error(s): </strong></p>";
								echo "<ul>";
								foreach($errors	 as $error){
								echo "<li class='text-danger'>" . $error . "</li>";
							}
								echo "</ul>";
							} else {
								// save
								$newMonitoring = new Monitoring();
								$newMonitoring->create(array(
									'process_id' => Input::get('process_id'),
									'company_id' => $user->data()->company_id,
									'current_step' => 1,
									'created' => strtotime(date('Y/m/d H:i:s')),
									'modified' => strtotime(date('Y/m/d H:i:s')),
									'is_active' => 1,
									'from_step' => 1,
									'who_request' => $user->data()->id

								));
								$lastMonitoringId = $newMonitoring->getInsertedId();

								foreach ($_POST as $key => $value) {
									// save to monitoring
									if($key == 'process_id') continue;
									$newData = new Data();
									$newData->create(array(
										'monitoring_id' => $lastMonitoringId,
										'request_form_id'=> $key,
										'content' => $value
									));
								}
								Session::flash('flash','You have successfully created a request');
								Redirect::to('createRequest.php?process='.Input::get('process_id'));
							}
							
						}
					}
						?>
						<?php

						foreach($forms as $f){
								if ($f->element_name == 'form_label') {
									?>
									<div class="form-group">
									<h3 class="col-md-8 text-center" ><?php echo $f->label ?></h3>
									</div>
									<?php
									continue;
								}
								?>
								<div class="form-group <?php echo in_array($f->id,$arrhaserror) ?  'has-error' : ''; ?>">
								<label class="col-md-4 control-label" for="<?php echo $f->id ?>"><?php echo $f->label ?></label>
								<div class="col-md-4">
									<?php if ($f->element_name == 'text') { ?>
									<input value='<?php echo (isset($arrOldVal[$f->id]) && ! empty($arrOldVal[$f->id])) ? $arrOldVal[$f->id] : ''; ?>' <?php echo ($f->is_required== 1) ? 'required' : ''; ?> id="<?php echo $f->id ?>" name="<?php echo $f->id ?>" placeholder="<?php echo $f->label ?>" class="form-control input-md <?php echo($f->data_type=='date') ? 'dts' : ''; ?>" type="<?php echo $f->element_name ?>">
									<?php } else if ($f->element_name == 'textarea') { ?>
									<<?php echo $f->element_name; ?> <?php echo ($f->is_required== 1) ? 'required' : ''; ?> id="<?php echo $f->id ?>" name="<?php echo $f->id ?>" class='form-control'><?php echo (isset($arrOldVal[$f->id]) && ! empty($arrOldVal[$f->id])) ? $arrOldVal[$f->id] : ''; ?></<?php echo $f->element_name; ?>>
									<?php } else if($f->element_name == 'select') { ?>
									<<?php echo $f->element_name; ?> class='form-control' <?php echo ($f->is_required== 1) ? 'required' : ''; ?> id="<?php echo $f->id ?>" name="<?php echo $f->id ?>">
									<?php 
										$choices = explode(",",$f->choices);
										foreach($choices as $c){
											$ischeckedopt = '';
											if (isset($arrOldVal[$f->id]) && ! empty($arrOldVal[$f->id]) && $arrOldVal[$f->id] == $c ){
												$ischeckedopt ='selected';
											}
											?>
											<option value ='<?php  echo $c; ?>' <?php echo $ischeckedopt; ?>> <?php  echo $c; ?></option>
											<?php 
										}
									?>
									</<?php echo $f->element_name?>>	
									<?php } else if ($f->element_name == 'radio'){ ?>
										<?php 
										$choices = explode(",",$f->choices);
										foreach($choices as $c){
											$ischecked = '';
											if (isset($arrOldVal[$f->id]) && ! empty($arrOldVal[$f->id]) && $arrOldVal[$f->id] == $c ){
												$ischecked ='checked';
											}
											?>
												<input <?php echo $ischecked; ?> type='<?php echo $f->element_name; ?>' value='<?php echo $c; ?>' <?php echo ($f->is_required== 1) ? 'required' : ''; ?> id="<?php echo $f->id ?>" name="<?php echo $f->id ?>"> <?php echo $c; ?>
											<?php
										}
										?>
									<?php } ?>
									<span class="help-block"></span>
								</div>
							</div>
								<?php 
								}
						?>
							<div class="form-group">
								<label class="col-md-4 control-label" for="button1id"></label>
								<div class="col-md-8">
									<input type='submit' class='btn btn-success'  id='btnSave' name='btnSave' value='SAVE'/>
									<input type='hidden' name='token' value=<?php echo Token::generate(); ?>>
									<input type='hidden' name='process_id' value=<?php echo isset($process_id) ? $process_id : 0; ?>>

								</div>
							</div>
						</form>
						<?php
					} else {
						echo "We don't have an item for your request";
					}
				?>
				</div>
		</div>

	<script type="text/javascript">

		$('.dts').datepicker({
			autoclose:true
		}) .on('changeDate', function(ev){
			$('.dts').datepicker('hide');
		});
	</script>


<?php require_once '../includes/monitoring/page_tail.php'; ?>