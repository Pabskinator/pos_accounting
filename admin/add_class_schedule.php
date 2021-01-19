<?php
	// $user have all the properties and method of the current user
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('branch')) {
		// redirect to denied page
		Redirect::to(1);
	}

	if(isset($_GET['edit'])) {
		$editid = $_GET['edit'];
	} else {
		$editid = 0;
	}

	if(isset($editid) && !empty($editid)) {
		// edit
		$id = Encryption::encrypt_decrypt('decrypt', $editid);
		// get the data base on branch id
		$class_schedule = new Class_schedule($id);

	}
	// if submitted
	if (Input::exists()){
		// check token if match to our token
		if(Token::check(Input::get('token'))){

			$validation_list = array(
				'day_of_the_week' => array(
					'required'=> true
				),
				'time_of_the_day' => array(
					'required'=> true
				),
				'class_id' => array(
					'required'=> true
				),
				'coach_id' => array(
					'required'=> true
				)
			);
			// get id in update

			if(!Input::get('edit')) {

			}


			$validate = new Validate();
			$validate->check($_POST, $validation_list);
			if($validate->passed()){
				$os = new Class_schedule();
				//edit codes
				if(Input::get('edit')){
					$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
					try{
						$ex = explode('-',Input::get('time_of_the_day'));
						$hr_from = strtotime($ex[0]);
						$arrupdate = array(
							'day_of_the_week' => Input::get('day_of_the_week'),
							'time_of_the_day' => Input::get('time_of_the_day'),
							'class_id' => Input::get('class_id'),
							'class_type' => Input::get('class_type'),
							'is_pt' => Input::get('is_pt'),
							'coach_id' => Input::get('coach_id'),
							'time_start' => $hr_from
						);

						$os->update($arrupdate, $id);
						Session::flash('flash','Information has been successfully updated');
						Redirect::to('class_schedule.php');
					} catch(Exception $e) {
						die($e->getMessage());
					}
				} else {
					// insert codes
					try {
						$ex = explode('-',Input::get('time_of_the_day'));
						$hr_from = strtotime($ex[0]);
						$inserarr = array(
							'day_of_the_week' => Input::get('day_of_the_week'),
							'time_of_the_day' => Input::get('time_of_the_day'),
							'class_id' => Input::get('class_id'),
							'created' => strtotime(date('Y/m/d H:i:s')),
							'is_active' => 1,
							'company_id' => $user->data()->company_id,
							'class_type' => Input::get('class_type'),
							'is_pt' => Input::get('is_pt'),
							'coach_id' => Input::get('coach_id'),
							'time_start' => $hr_from
						);
						$os->create($inserarr);
					} catch(Exception $e){
						die($e);
					}
					Session::flash('flash','You have successfully added a schedule');
					Redirect::to('class_schedule.php');
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
	<!-- Page content -->
	<div id="page-content-wrapper">
		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					<?php echo isset($editid) && !empty($editid) ? "EDIT SCHEDULE" : "ADD SCHEDULE"; ?>
				</h1>
			</div>

			<div class="row">
				<div class="col-md-12">
					<form class="form-horizontal" action="" method="POST">
						<fieldset>
							<legend>Coach</legend>
							<div class="form-group">
								<label class="col-md-4 control-label" for="day_of_the_week">Day of the week</label>
								<div class="col-md-4">
									<input id="day_of_the_week" name="day_of_the_week" placeholder="Enter Day" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($class_schedule->data()->day_of_the_week) : escape(Input::get('day_of_the_week')); ?>">
									<span class="help-block">(Ex. Friday)</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="time_of_the_day">Time of the day</label>
								<div class="col-md-4">
									<input id="time_of_the_day" name="time_of_the_day" placeholder="Enter Time" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($class_schedule->data()->time_of_the_day) : escape(Input::get('time_of_the_day')); ?>">
									<span class="help-block">Military time (Ex. 14:30:00)</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="class_type">Type</label>
								<div class="col-md-4">
									<?php
										$class_type = isset($id) ? $class_schedule->data()->class_type : escape(Input::get('class_type'));
									?>
									<select name="class_type" id="class_type" class='form-control'>
										<option value="1"  <?php echo ($class_type == 1 ) ? 'selected' : ''; ?>>Turf Area</option>
										<option value="2"  <?php echo ($class_type == 2 ) ? 'selected' : ''; ?>>Matted Area</option>
									</select>

								</div>
							</div>
							<?php
								$coach = new Coach();
								$coaches = $coach->get_active('coaches',[1,'=',1]);

							?>
							<div class="form-group">
								<label class="col-md-4 control-label" for="coach_id">Coach</label>
								<div class="col-md-4">
									<?php
										$coach_id = isset($id) ? $class_schedule->data()->coach_id : escape(Input::get('coach_id'));
									?>
									<select name="coach_id" id="coach_id" class='form-control'>
										<?php
											foreach($coaches as $co){
												$sel="";
												if($co->id == $coach_id) $sel = "selected";

												echo "<option value='$co->id' $sel>$co->name</option>";
											}
										?>
									</select>

								</div>
							</div>




									<input type='hidden' name="is_pt" id="is_pt" value='1'>


							<div class="form-group">
								<label class="col-md-4 control-label" for="class_id">Discipline</label>
								<div class="col-md-4">
									<select id="class_id" name="class_id" class="form-control">
										<option value=''>--Select Discipline--</option>
										<?php
											$offered = new Offered_service();
											$offered_services =  $offered->get_active('offered_services',array('company_id' ,'=',$user->data()->company_id));
											foreach($offered_services as $os){
												$a = isset($id) ? $class_schedule->data()->class_id : escape(Input::get('class_id'));

												if($a==$os->id){
													$selected='selected';
												} else {
													$selected='';
												}
												?>
												<option value='<?php echo $os->id ?>' <?php echo $selected ?>><?php echo $os->name;?> </option>
												<?php
											}
										?>
									</select>
									<span class="help-block">Class name</span>
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
	<script>
		$(function(){

		});
	</script>
<?php
	require_once '../includes/admin/page_tail2.php';