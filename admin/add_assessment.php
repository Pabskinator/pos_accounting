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
		$assessment = new Assessment_list($id);

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
				'grp' => array(
					'required'=> true,
					'max' => 50
				),
				'disc_id' => array(
					'required'=> true
				),
			);
			// get id in update


			$validate = new Validate();
			$validate->check($_POST, $validation_list);
			if($validate->passed()){
				$os = new Assessment_list();
				//edit codes
				if(Input::get('edit')){
					$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
					try{
						$arrupdate = array(
							'name' => Input::get('name'),
							'grp' => Input::get('grp'),
							'disc_id' => Input::get('disc_id')
						);

						$os->update($arrupdate, $id);
						Session::flash('flash','Information has been successfully updated');
						Redirect::to('assessment_list.php');
					} catch(Exception $e) {
						die($e->getMessage());
					}
				} else {
					// insert codes
					try {
						$inserarr = array(
							'name' => Input::get('name'),
							'grp' => Input::get('grp'),
							'disc_id' => Input::get('disc_id'),
							'created' => strtotime(date('Y/m/d H:i:s')),
							'is_active' => 1,
							'company_id' => $user->data()->company_id
						);
						$os->create($inserarr);
					} catch(Exception $e){
						die($e);
					}
					Session::flash('flash','You have successfully added a record');
					Redirect::to('assessment_list.php');
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
					<?php echo isset($editid) && !empty($editid) ? "EDIT RECORD" : "ADD RECORD"; ?>
				</h1>
			</div>

			<div class="row">
				<div class="col-md-12">
					<?php include_once "includes/assessment_nav.php" ?>
					<form class="form-horizontal" action="" method="POST">
						<fieldset>
							<legend>Information</legend>
							<div class="form-group">
								<label class="col-md-4 control-label" for="name">Drills</label>
								<div class="col-md-4">
									<input id="branchName" name="name" placeholder="Enter Name" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($assessment->data()->name) : escape(Input::get('name')); ?>">
									<span class="help-block"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="grp">Group</label>
								<div class="col-md-4">
									<select name="grp" id="grp" class='form-control'>
										<option value="Conditioning">Conditioning</option>
										<option value="Techniques">Techniques</option>
									</select>

									<span class="help-block"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="disc_id">Discipline</label>
								<div class="col-md-4">
									<select id="disc_id" name="disc_id" class="form-control">
										<option value=''>--Select Discipline--</option>
										<?php
											$offered = new Offered_service();
											$offered = $offered->get_active('offered_services',array('1' ,'=','1'));
											foreach($offered as $b){
												$a = isset($id) ? $assessment->data()->disc_id : escape(Input::get('disc_id'));

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
									<span class="help-block">Module where this member belong</span>
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