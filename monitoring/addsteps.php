<?php
	// $user have all the properties and method of the current user
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require_once '../includes/monitoring/page_head.php';
	if(!$user->hasPermission('terminal')) {
		// redirect to denied page
		Redirect::to(1);
	}
	if(isset($_GET['edit'])) {
		$editid = $_GET['edit'];
	} else {
		$editid = 0;
	}

?>
<?php require_once '../includes/monitoring/page_head.php'; ?>
	<!-- Sidebar -->
<?php include_once '../includes/monitoring/sidebar.php'; ?>
	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					<?php echo isset($editid) && !empty($editid) ? "EDIT STEPS" : "ADD STEPS"; ?>
				</h1>
			</div>
			<div class="row">
				<div class="col-md-12">

					<?php
						if(isset($editid) && !empty($editid)) {
							// edit
							$id = Encryption::encrypt_decrypt('decrypt', $editid);
							// get the data base on branch id
							$step = new Steps($id);
						}

						// if submitted
						if (Input::exists()){
							// check token if match to our token
							if(Token::check(Input::get('token'))){

								$validation_list = array(
									'name' => array(
										'required'=> true,
										'max' => 50,
										'min'=> 6
									),
									'process_id' => array(
										'required'=> true,
										'isnumber'=> true
									),
									'step_number' => array(
										'required'=> true,
										'isnumber'=> true
									)
								);



								$validate = new Validate();
								$validate->check($_POST, $validation_list);
								if($validate->passed()){
									$step = new Steps();
									//edit codes
									if(Input::get('edit')){
										$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
										try{
												$positionstring = '';
												foreach(Input::get('positions') as $c){
													$positionstring.= $c . ',';
												}
												$positionstring  = rtrim($positionstring ,",");
											$has_attachment = (Input::get('has_attachment')) ? 1 : 0;
											if($has_attachment == 1){
												$rd_req = Input::get('rdReq');
											} else {
												$rd_req = 0;
											}
											$has_report = (Input::get('has_report')) ? 1 : 0;
											if($has_report == 1){
												$rd_req2 = Input::get('rdReq2');
											} else {
												$rd_req2 = 0;
											}
											$step->update(array(
												'name' => Input::get('name'),
												'process_id' => Input::get('process_id'),
												'modified' => strtotime(date('Y/m/d H:i:s')),
												'has_attachment' => $has_attachment,
												'is_required' => $rd_req,
												'has_report' => $has_report,
												'is_report_required' => $rd_req2,
												'whos_responsible' => $positionstring,
												'step_number' => Input::get('step_number'),

											), $id);
											Session::flash('stepsflash','Step information has been successfully updated');
											Redirect::to('steps.php');
										} catch(Exception $e) {
											die($e->getMessage());
										}
									} else {
										// insert codes
										try {
											$positionstring = '';
												foreach(Input::get('positions') as $c){
													$positionstring.= $c . ',';
												}
												$positionstring  = rtrim($positionstring ,",");
											$has_attachment = (Input::get('has_attachment')) ? 1 : 0;
											if($has_attachment == 1){
												$rd_req = Input::get('rdReq');
											} else {
												$rd_req = 0;
											}
											$has_report = (Input::get('has_report')) ? 1 : 0;
											if($has_report == 1){
												$rd_req2 = Input::get('rdReq2');
											} else {
												$rd_req2 = 0;
											}
											  $step->create(array(
												'name' => Input::get('name'),
												'process_id' => Input::get('process_id'),
												'whos_responsible' => $positionstring,
												'has_attachment' => $has_attachment,
												'step_number' => Input::get('step_number'),
												'is_required' =>$rd_req,
											  'has_report' => $has_report,
											  'is_report_required' => $rd_req2,
												'company_id' => $user->data()->company_id,
												'created' => strtotime(date('Y/m/d H:i:s')),
												'modified' => strtotime(date('Y/m/d H:i:s')),
												'is_active' => 1
											));


										} catch(Exception $e){
											die($e);
										}
										Session::flash('stepsflash','You have successfully added a Step');
										Redirect::to('steps.php');
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


							<legend>Step Information</legend>

						
								<div class="form-group">
									<label class="col-md-4 control-label" for="checkboxes">Has Attachment?</label>
									<div class="col-md-4">
										<label class="checkbox-inline" for="has_attachment">
											<input name="has_attachment" id="has_attachment" value="1" type="checkbox"
												<?php
													if(isset($id))
													echo ($step->data()->has_attachment) ? "checked" : "";
												?>
												>
											Click this if the step requires attachment
										</label>
									</div>
								</div>
								<div class="form-group" id='rdContainer' style='<?php if(isset($id)) echo ($step->data()->has_attachment) ? "" : "display:none;"; ?>'>
									<label class="col-md-4 control-label" for="rdRequired"></label>
									<div class="col-md-4">
										<label class="checkbox-inline" for="rd1">
											<input name="rdReq" id='rd1' value="1" type="radio"
												<?php
													if(isset($id)) echo ($step->data()->is_required == 1) ? "checked" : "";
													else echo "checked";
												?>
												>
											Required
										</label>
										<label class="checkbox-inline" for="rd2">
											<input name="rdReq" id='rd2' value="0" type="radio"
												<?php
													if(isset($id)) echo ($step->data()->is_required == 0) ? "checked" : "";
												?>
												>
											Optional
										</label>
									</div>
								</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="checkboxes">Has Report?</label>
								<div class="col-md-4">
									<label class="checkbox-inline" for="has_report">
										<input name="has_report" id="has_report" value="1" type="checkbox"
											<?php
												if(isset($id))
													echo ($step->data()->has_report) ? "checked" : "";
											?>
											>
										Click this if the step requires a report
									</label>
								</div>
							</div>
							<div class="form-group" id='rdContainer2' style='<?php if(isset($id)) echo ($step->data()->has_report) ? "" : "display:none;"; ?>'>
								<label class="col-md-4 control-label" for=""></label>
								<div class="col-md-4">
									<label class="checkbox-inline" for="rep1">
										<input name="rdReq2" id='rep1' value="1" type="radio"
											<?php
												if(isset($id)) echo ($step->data()->is_report_required == 1) ? "checked" : "";
												else echo "checked";
											?>
											>
										Required
									</label>
									<label class="checkbox-inline" for="rep2">
										<input name="rdReq2" id='rep2' value="0" type="radio"
											<?php
												if(isset($id)) echo ($step->data()->is_report_required == 0) ? "checked" : "";
											?>
											>
										Optional
									</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="name">Step Name</label>
								<div class="col-md-4">
									<input id="stepName" name="name" placeholder="Step Name" class="form-control input-md" type="text" value="<?php echo isset($id) ? $step->data()->name : escape(Input::get('name')); ?>">
									<span class="help-block">Name of the Step</span>
								</div>
							</div>



							<div class="form-group">
								<label class="col-md-4 control-label" for="process_id">Process</label>
								<div class="col-md-4">
									<select id="process_id" name="process_id" class="form-control">
										<option value=''>--Select Process--</option>
										<?php
											$process = new Process();
											$processes =  $process->get_active('processes',array('1' ,'=',1));
											foreach($processes as $b){
												$a = isset($id) ? $step->data()->process_id : escape(Input::get('process_id'));

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
									<span class="help-block">Choose process</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="step_number">Step Number</label>
								<div class="col-md-4">
									<input id="step_number" name="step_number" placeholder="Step Number" class="form-control input-md" type="text" value="<?php echo isset($id) ? $step->data()->step_number : escape(Input::get('step_number')); ?>">
									<span class="help-block">Step number</span>
								</div>
							</div>
					
								<div style="clear:both;"></div>
							<legend>Who will Approve This Step</legend>
							<div class="form-group">
								<?php

									$pp = new Position();
										$spositions = $pp->get_active('positions', array('company_id','=',$user->data()->company_id));
										if($spositions){
										$arr_pos_id = [];
											if(isset($id)){
												$arr_pos_id = explode(',',$step->data()->whos_responsible);
											}
											foreach($spositions as $sp){

											?>
											<div class="col-md-3">
												<label class="checkbox-inline" for="<?php echo $sp->id; ?>">
													<input class='charcheckbox' name="positions[]" id="<?php echo $sp->id; ?>" value="<?php echo $sp->id; ?>" type="checkbox"
														<?php
															if(in_array($sp->id,$arr_pos_id)){
																echo "checked";
															}
														// selected algo here
														// ?>
														>
													<span><?php echo $sp->position; ?></span>
												</label>
											</div>
										<?php
										}
										?>
										<div class="col-md-3">
												<label class="checkbox-inline" for="<?php echo -1; ?>">
													<input class='charcheckbox' name="positions[]" id="<?php echo -1; ?>" value="<?php echo -1; ?>" type="checkbox"
														<?php // selected algo here ?>
														>
													<span><?php echo 'All'; ?></span>
												</label>
										</div>
										<?php
									} else {
										?>
										<div class="alert alert-info">No Characteristics Yet</div>
									<?php
									}
								?>
							</div>

							<br/>

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

<script type="text/javascript">
	$(".charcheckbox").change(function(){

				var checkitem = $(this).next().text();
			
				if(checkitem == 'All'){
					$(".charcheckbox").each(function(){
						if($(this).next().text() != 'All'){
							$(this).attr('checked',false);
						}
					});
				} else {
					$(".charcheckbox").each(function(){
						if($(this).next().text() == 'All'){
							$(this).attr('checked',false);
						}
					});
				}
			});
		$(function(){
			$('body').on('change','#has_attachment',function(){
				if($(this).is(":checked")){
					$('#rdContainer').show();
				} else {
					$('#rdContainer').hide();
				}
			});
			$('body').on('change','#has_report',function(){
				if($(this).is(":checked")){
					$('#rdContainer2').show();
				} else {
					$('#rdContainer2').hide();
				}
			});
		});
</script>

<?php require_once '../includes/monitoring/page_tail.php'; ?>