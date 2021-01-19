<?php
	// $user have all the properties and method of the current user
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('wo_mod')) {
		// redirect to denied page
		Redirect::to(1);
	}

	if(isset($_GET['edit'])) {
		$editid = $_GET['edit'];
	} else {
		$editid = 0;
	}

	$workout_module = new Workout_module();
	$workout_modules = $workout_module->get_active('workout_module',array('company_id','=',$user->data()->company_id));


	$member_id =0;
	$member_name ='';
	$module_id =0;
	$module_name ='';

	if(isset($editid) && !empty($editid)) {
		// edit
		$id = Encryption::encrypt_decrypt('decrypt', $editid);
		// get the data base on branch id
		$mod = new Workout_module_member($id);

		if($mod->data()->member_id){
			$member_id = $mod->data()->member_id;
			$member_details = new Member($member_id);
			$member_name = $member_details->data()->lastname;
		}
		if($mod->data()->module_id){
			$module_id = $mod->data()->module_id;
			$module_details = new Workout_module($module_id);
			$module_name = $module_details->data()->name;
		}
	}
	// if submitted
	if (Input::exists()){
		// check token if match to our token
		if(Token::check(Input::get('token'))){

			$validation_list = array(
				'member_id' => array(
					'required'=> true
				),
				'module_id' => array(
					'required'=> true
				)
			);
			// get id in update

			$validate = new Validate();
			$validate->check($_POST, $validation_list);
			if($validate->passed()){
				$mod = new Workout_module_member();
				//edit codes
				if(Input::get('edit')){
					$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
					try{
						$arrupdate = array(
							'member_id' => Input::get('member_id'),
							'module_id' => Input::get('module_id')
						);
						$mod->update($arrupdate, $id);
						Session::flash('flash','Module information has been successfully updated');
						Redirect::to('price_group_member.php');
					} catch(Exception $e) {
						die($e->getMessage());
					}
				} else {
					// insert codes
					try {
						$inserarr = array(
							'member_id' => Input::get('member_id'),
							'module_id' => Input::get('module_id'),
							'created' => strtotime(date('Y/m/d H:i:s')),
							'company_id' => $user->data()->company_id,
							'is_active' => 1
						);


						$mod->create($inserarr);

					} catch(Exception $e){
						die($e);
					}
					Session::flash('flash','You have successfully added a module for member');
					Redirect::to('price_group_member.php');
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
	<div id="page-content-wrapper">


		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					Module Assignment
				</h1>

			</div>
<div class="row">
	<div class="col-md-12">
		<form class="form-horizontal" action="" method="POST">
			<fieldset>
				<legend>Module Information</legend>


				<?php
					if($workout_modules > 0){
						?>

						<div class="form-group">
							<label class="col-md-4 control-label" for="module_id">Module Name</label>
							<div class="col-md-4">
								<select id="module_id" name="module_id" class="form-control">
									<option value=''>--Select Module--</option>
									<?php
										foreach($workout_modules as $b){
											$a = isset($id) ? $mod->data()->module_id : escape(Input::get('module_id'));

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
						<?php
					}
				?>
				<div class="form-group">
					<label class="col-md-4 control-label" for="member_id"><?php echo MEMBER_LABEL; ?></label>
					<div class="col-md-4">
						<input id="member_id" name="member_id" placeholder="<?php echo MEMBER_LABEL; ?>" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($mod->data()->member_id) :  escape(Input::get('member_id')); ?>">
						<span class="help-block">Name of the Client</span>
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

		var mem_select2 = $('#member_id');
		var member_id = '<?php echo $member_id; ?>';
		var member_name = '<?php echo $member_name; ?>';
		var MEMBER_LABEL = $('#MEMBER_LABEL').val();
		mem_select2.select2({
			placeholder: 'Search ' +MEMBER_LABEL,
			allowClear: true,
			minimumInputLength: 2,
			ajax: {
				url: '../ajax/ajax_json.php',
				dataType: 'json',
				type: "POST",
				quietMillis: 50,
				data: function (term) {
					return {
						q: term,
						functionName:'members'
					};
				},
				results: function (data) {
					return {
						results: $.map(data, function (item) {
							return {
								text: item.lastname + ", " + item.firstname + " " + item.middlename,
								slug: item.lastname + ", " + item.firstname + " " + item.middlename,
								id: item.id
							}
						})
					};
				}
			}
		});
		if(member_id != '0'){
			mem_select2.select2('data',{ id: member_id, text: member_name });
		}
	});
</script>
<?php	require_once '../includes/admin/page_tail2.php';