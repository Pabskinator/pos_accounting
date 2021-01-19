<?php
	// $user have all the properties and method of the current user
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require_once '../includes/monitoring/page_head.php';
	if(!$user->hasPermission('inventory')) {
	// redirect to denied page
	//	Redirect::to(1);
	}


?>

	<!-- Sidebar -->
<?php include_once '../includes/monitoring/sidebar.php'; ?>
	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					Add Request Form
				</h1>
			</div>
			<?php
				// get flash message if add or edited successfully
				if(Session::exists('formflash')) {
					echo "<br/><div class='alert alert-danger' style='width:90%;margin:0 auto'>" . Session::flash('formflash') . "</div>";
				}
			?>
			<div class="row">
				<div class="col-md-12">

					<?php


						// if submitted
						if (Input::exists()){
							// check token if match to our token
							if(Token::check(Input::get('token'))){
							
							$whocanreq = '';
							
							 foreach ($_POST['who_can_request']  as $value) {
							 		$whocanreq.= $value . ",";
							 }
							 	$whocanreq = rtrim($whocanreq, ",");

							 $length =count($_POST['element_name']);
							for($i=0;$i< $length; $i++){
								$process_id =  $_POST['process_id'][0];
								$who_can_request = $whocanreq;
								$step_id = 0;
								$element_name =  $_POST['element_name'][$i];
								$data_type =  $_POST['data_type'][$i];
								$min_length =  $_POST['min_length'][$i];
								$max_length =  $_POST['max_length'][$i];
								$label =  $_POST['label'][$i];
								$is_required =  $_POST['is_required'][$i];
								$choices =  $_POST['choices'][$i];
								$fm = new FormRequest();

								$fm->create(array(
											
												'process_id' => $process_id,
												'who_can_request' => $who_can_request,
												'step_id' => $step_id,
												'element_name' => $element_name,
												'data_type' => $data_type,
												'min_length' => $min_length,
												'max_length' => $max_length,
												'label' => $label,
												'company_id' => $user->data()->company_id,
												'is_required' => $is_required,
												'choices' => $choices,
												'created' => strtotime(date('Y/m/d H:i:s')),
												'modified' => strtotime(date('Y/m/d H:i:s')),
												'is_active' => 1
											));
								
							}
								Session::flash('formflash','You have successfully added a request form');
								Redirect::to('requestForm.php');
							}

						}
					?>

					<form class="form-horizontal" action="" method="POST">
						<fieldset>


							<legend></legend>
								<div class="form-group">
								<label class="col-md-1 control-label" for="process_id">Process</label>
								<div class="col-md-3">
									<select id="process_id" name="process_id[]" class="form-control">
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
							<span class="help-block">Choose what process</span>
						</div>
							<label class="col-md-1 control-label" for="who_can_request">Who can Request</label>
								<div class="col-md-7">

									<?php
										$pp = new Position();
										$spositions = $pp->get_active('positions', array('company_id','=',$user->data()->company_id));

									?>
									<select name="who_can_request[]" id="who_can_request" class="form-control" multiple>

										<?php
											foreach($spositions as $sp){
												?>
												<option value="<?php echo $sp->id ?>"><?php echo $sp->position ?></option>
											<?php
											}
										?>
									</select>
									<span class="help-block"><input type='checkbox' id='chkAll'> <label for="chkAll">Select all</label></span>
								</div>
						</div>
							<div id="clonethis">
							<div class="form-group">
							
							

								<label class="col-md-1 control-label" for="element_name">Element</label>
								<div class="col-md-3">
									<select id="element_name" name="element_name[]" class="form-control element_name">
										<option value=''>--Select Element--</option>
										<option value='text'>text</option>
										<option value='textarea'>textarea</option>
										<option value='select'>select</option>
										<option value='radio'>radio</option>
										<option value='form_label'>form label</option>
									</select>
									<span class="help-block">Choose element name</span>
								</div>
								<label class="col-md-1 control-label" for="choices">Choices</label>
								<div class="col-md-7">
									<input type='text' id="choices" name="choices[]"  class="form-control choices">
										
									<span class="help-block">Choices, comma separated</span>
								</div>
								
								<label class="col-md-1 control-label" for="data_type[]">Type</label>
								<div class="col-md-3">
										<select id="data_type[]" name="data_type[]" class="form-control">
										<option value=''>--Select Type--</option>
										<option value='int'>Number</option>
										<option value='string'>String</option>
										<option value='date'>Date</option>
									</select><span class="help-block">Choose the data type</span>
								</div>
								<label class="col-md-1 control-label" for="max_length">Max Length</label>
								<div class="col-md-3">
									<input id="max_length"  name="max_length[]" placeholder="Max Length" class="form-control input-md " type="text">
									<span class="help-block">Maximun length</span>
								</div>
								<label class="col-md-1 control-label" for="min_length">Min Length</label>
								<div class="col-md-3">
									<input id="min_length"  name="min_length[]" placeholder="Min Length" class="form-control input-md " type="text">
									<span class="help-block">Minimun length</span>
								</div>
									<label class="col-md-1 control-label" for="label">Label</label>
								<div class="col-md-3">
									<input id="label"  name="label[]" placeholder="Label" class="form-control input-md " type="text">
									<span class="help-block">Enter label</span>
								</div>
								<label class="col-md-1 control-label" for="is_required">Required?</label>
								<div class="col-md-3">
									<select id="is_required[]" name="is_required[]" class="form-control">
			
										<option value='1'>Required</option>
										<option value='0'>Not Required</option>
									</select><span class="help-block">is data required?</span>
								</div>
							</div>
			
							</div> <!-- Clone end -->
							<div id="appendclone"></div>
							<div class="form-group" id='addmore'>
							</div>
							<input type="button" id='btnAdd' value='Add more item' class='btn btn-default pull-right'/>

						


							<!-- Button (Double) -->
							<div class="form-group">
								<label class="col-md-1 control-label" for="button1id"></label>
								<div class="col-md-8">
									<input type='submit' class='btn btn-success' name='btnSave' value='SAVE'/>
									<input type='hidden' name='token' value=<?php echo Token::generate(); ?>>
								</div>
							</div>

						</fieldset>
					</form>
				</div>

			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<script>
		$(document).ready(function() {

				$("#chkAll").click(function(){
					if($("#chkAll").is(':checked') ){
						$("#who_can_request > option").prop("selected","selected");// Select All Options
						$("#who_can_request").trigger("change");// Trigger change to select 2
					}else{
						$("#who_can_request > option").removeAttr("selected");
						$("#who_can_request").trigger("change");// Trigger change to select 2
					}
				});


			$('#btnAdd').click(function(){
			
			$( "#clonethis" ).clone().appendTo( "#appendclone" );
			});
			// select2
			$("#who_can_request").select2({
				placeholder: 'Who can request',
				allowClear: true
			});
		});
	</script>

<?php require_once '../includes/monitoring/page_tail.php'; ?>