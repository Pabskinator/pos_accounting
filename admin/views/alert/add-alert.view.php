
<!-- Page content -->
<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				<?php echo isset($editid) && !empty($editid) ? "EDIT ALERT" : "ADD ALERT"; ?>
			</h1>
		</div>
		<div class="row">
			<div class="col-md-12">
				<?php include 'includes/product_nav.php'; ?>

				<?php

					if(isset($editid) && !empty($editid)) {
						// edit
						$id = Encryption::encrypt_decrypt('decrypt', $editid);
						// get the data base on branch id
						$alertcls = new Alert_item($id);
					}

					// if submitted
					if (Input::exists()){
						// check token if match to our token
						print_r($_POST);
						if(Token::check(Input::get('token'))){

							$validation_list = array(
								'item_id' => array(
									'required'=> true
								),
								'alert_days' => array(
									'required'=> true
								),
								'alert_msg' => array(
									'required'=> true
								)

							);


							$validate = new Validate();
							$validate->check($_POST, $validation_list);
							if($validate->passed()){
								$alertcls = new Alert_item();
								//edit codes
								if(Input::get('edit')){
									$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
									try{

										$pp = '';
										foreach(Input::get('position_id') as $indp){
											$pp .= $indp. ",";
										}
										$pp = rtrim($pp,',');
										$alertcls->update(array(
											'item_id' => Input::get('item_id'),
											'alert_days' => Input::get('alert_days'),
											'alert_msg' => Input::get('alert_msg'),
											'position_id' => $pp,
											'modified' => strtotime(date('Y/m/d H:i:s')),
										), $id);
										Log::addLog($user->data()->id,$user->data()->company_id,"Update alerts for ||items:".Input::get('item_id'),'admin/addalert.php');
										Session::flash('flash','Alert information has been successfully updated');
										Redirect::to('manage-alert.php');
									} catch(Exception $e) {
										die($e->getMessage());
									}
								} else {
									// insert codes

									try {

										$pp = '';
										foreach(Input::get('position_id') as $indp){
											$pp .= $indp. ",";
										}
										$pp = rtrim($pp,',');
										echo $ad = (string)  Input::get('alert_days');
										$alertcls->create(array(
											'item_id' => Input::get('item_id'),
											'alert_days' => $ad,
											'alert_msg' => Input::get('alert_msg'),
											'position_id' => $pp,
											'modified' => strtotime(date('Y/m/d H:i:s')),
											'created' => strtotime(date('Y/m/d H:i:s')),
											'is_active' => 1,
											'company_id' => $user->data()->company_id
										));

									} catch(Exception $e){
										die($e->getMessage());
									}
									Log::addLog($user->data()->id,$user->data()->company_id,"Insert alerts for ||items:".Input::get('item_id'),'admin/addalert.php');
									Session::flash('flash','You have successfully added a branch');
									Redirect::to('manage-alert.php');
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

						<legend>Alert Information</legend>
						<div class="form-group">
							<label class="col-md-4 control-label" for="item_id">Item</label>
							<div class="col-md-4">
								<input name="item_id" id="item_id" class='form-control selectitem'>
								<span class="help-block">Please choose an item</span>
							</div>
						</div>

						<!-- Text input-->
						<div class="form-group">
							<label class="col-md-4 control-label" for="alert_days">Alert Days</label>
							<div class="col-md-4">
								<input type="text" id="alert_days" name="alert_days" class='form-control' value="<?php echo isset($id) ? escape($alertcls->data()->alert_days) :  escape(Input::get('alert_days')); ?>">
								<span class="help-block">You can type any number of days.</span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label" for="alert_msg">Alert Message</label>
							<div class="col-md-4">
								<input id="alert_msg" name="alert_msg" autocomplete="off" placeholder="Alert Message" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($alertcls->data()->alert_msg) :  escape(Input::get('alert_msg')); ?>">
								<span class="help-block">Alert Message</span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label" for="position_id">Position</label>
							<div class="col-md-4">
								<select name="position_id[]" id="position_id" class='form-control' multiple>
									<option value=""></option>
									<?php
										$positioncls = new Product();
										$positions = $positioncls->get_active('positions',array('company_id','=',$user->data()->company_id));
										foreach($positions as $position){
											$a = isset($id) ? $alertcls->data()->position_id : escape(Input::get('position_id'));
											$a = explode(',',$a);
											if(in_array($position->id,$a)){
												$selected='selected';
											} else {
												$selected='';
											}
											echo "<option value='$position->id' $selected>$position->position</option>";
										}
									?>
								</select>
								<span class="help-block">Position(s) who will see the alerts</span>
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

		$('#position_id').select2({
			placeholder:'Choose Position'
		});
		/*	$("#alert_days").select2({
				placeholder:'Alerted Days',
				tags: ["10", "20", "30"],
				tokenSeparators: [',', ' ']

			});*/
	});
</script>