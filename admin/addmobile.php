<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';

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

	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					<?php echo isset($editid) && !empty($editid) ? "EDIT MOBILE" : "ADD MOBILE"; ?>
				</h1>
			</div>

			<div class="row">
				<div class="col-md-12">

					<?php


						if(isset($editid) && !empty($editid)) {
							// edit
							$id = Encryption::encrypt_decrypt('decrypt', $editid);
							// get the data base on branch id
							$sms_gateway = new Sms_gateway($id);
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
									'mobile_number' => array(
										'required'=> true
									)
								);



								$validate = new Validate();
								$validate->check($_POST, $validation_list);

								if($validate->passed()){
									$sms_gateway = new Sms_gateway();
									//edit codes
									if(Input::get('edit')){
										$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
										try{
											$terminal_ids = implode(",",Input::get('terminal_id'));
											$sms_gateway->update(array(
												'name' => Input::get('name'),
												'terminal_id' => $terminal_ids,
												'mobile_number' => Input::get('mobile_number')
											), $id);
											Session::flash('flash','Information has been successfully updated');
											Redirect::to('sms_mobile.php');
										} catch(Exception $e) {
											die($e->getMessage());
										}
									} else {
										// insert codes
										try {
											$terminal_ids = implode(",",Input::get('terminal_id'));
											$sms_gateway->create(array(
												'name' => Input::get('name'),
												'terminal_id' => $terminal_ids,
												'mobile_number' => Input::get('mobile_number'),
												'created' => strtotime(date('Y/m/d H:i:s')),
												'company_id' => $user->data()->company_id,
												'is_active' => 1
											));


										} catch(Exception $e){
											die($e);
										}
										Session::flash('flash','Information was inserted successfully.');
										Redirect::to('sms_mobile.php');
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


							<legend>Information</legend>
							<div class="form-group">

								<label class="col-md-4 control-label" for="name">Name</label>
								<div class="col-md-4">
									<input id="name" name="name" placeholder="Name" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($sms_gateway->data()->name) : escape(Input::get('name')); ?>">
									<span class="help-block">Name </span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="terminal_id">Terminal</label>
								<div class="col-md-4">
									<select id="terminal_id" name="terminal_id[]" class="form-control" multiple>
										<option value=''></option>
										<?php
											$terminal = new Terminal();
											$terminals =  $terminal->get_active('terminals',array('1' ,'=',1));
											foreach($terminals as $b){
												$a = isset($id) ? $sms_gateway->data()->terminal_id : escape(Input::get('terminal_id'));

												if($a==$b->id){
													$selected='selected';
												} else {
													$selected='';
												}
												?>
												<option value='<?php echo $b->id ?>' <?php echo $selected; ?>><?php echo $b->name;?> </option>
												<?php
											}
										?>
									</select>
									<span class="help-block">From what terminal</span>
								</div>
							</div>


							<div class="form-group">
								<label class="col-md-4 control-label" for="mobile_number">Number</label>
								<div class="col-md-4">
									<input id="mobile_number" name="mobile_number" placeholder="Mobile number" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($sms_gateway->data()->mobile_number) : escape(Input::get('mobile_number')); ?>">
									<span class="help-block">Mobile number</span>
								</div>
							</div>


							<!-- Button (Double) -->
							<div class="form-group">

								<div class="col-md-4"></div>
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

	<script>
		$(function(){
			var tid = [];
			<?php if(isset($id)) {
			?>
				var terminal_ids = "<?php echo $sms_gateway->data()->terminal_id;  ?>";

				if(terminal_ids.indexOf(',') >0){
					tid = terminal_ids.split(',');
				} else {
					if(terminal_ids){
						tid.push(terminal_ids);
					}
				}
			<?php
			}?>
			$('#terminal_id').select2({
				allowClear: true,
				placeholder: "Select Terminal"
			}).select2('val',tid);
		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>