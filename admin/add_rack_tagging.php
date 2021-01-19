<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!true) {
		// redirect to denied page
		Redirect::to(1);
	}
	if(isset($_GET['edit'])) {
		$editid = $_GET['edit'];
	} else {
		$editid = 0;
	}
	$user_cls = new User();
	$user_list = $user_cls->get_active('users',['company_id','=',$user->data()->company_id]);
?>

	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					<?php echo isset($editid) && !empty($editid) ? "EDIT TAG" : "ADD TAG"; ?>
				</h1>
			</div>
			<div class="row">
				<div class="col-md-12">

					<?php
						$member_id =0;
						$member_name ='';
						if(isset($editid) && !empty($editid)) {
							// edit
							$id = Encryption::encrypt_decrypt('decrypt', $editid);
							// get the data base on branch id
							$rack_tag = new Rack_tag($id);

						}

						// if submitted
						if (Input::exists()){

							// check token if match to our token
							if(Token::check(Input::get('token'))){

								$validation_list = array(
									'tag_name' => array(
										'required'=> true,
										'max' => 50
									)
								);
								// get id in update

								if(!Input::get('edit')) {
									$additionalvalidation = array('unique' => 'rack_tags');
									$finalvalidation=array_merge($validation_list['tag_name'],$additionalvalidation);
									$validation_list['tag_name'] = $finalvalidation;
								}


								$validate = new Validate();
								$validate->check($_POST, $validation_list);

								if($validate->passed()){
									$rack_tag = new Rack_tag();
									//edit codes
									if(Input::get('edit')){
										$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
										try{
											$arrupdate = array(
												'tag_name' => Input::get('tag_name'),
												'assign_to' =>  implode(',',Input::get('assign_to'))
											);

											$rack_tag->update($arrupdate, $id);

											Log::addLog($user->data()->id,$user->data()->company_id,"Update Rack Tag " . Input::get('tag_name'),"add_rack_tagging.php");

											Session::flash('flash','Item has been successfully updated');
											Redirect::to('rack_tagging.php');
										} catch(Exception $e) {
											die($e->getMessage());
										}
									} else {
										// insert codes
										try {
											$inserarr = array(
												'tag_name' => Input::get('tag_name'),
												'assign_to' => implode(',',Input::get('assign_to')),
												'created' => strtotime(date('Y/m/d H:i:s')),
												'is_active' => 1,
												'company_id' => $user->data()->company_id
											);
											Log::addLog($user->data()->id,$user->data()->company_id,"Add Rack Tag " . Input::get('tag_name'),"add_rack_tagging.php");
											$rack_tag->create($inserarr);
										} catch(Exception $e){
											die($e);
										}
										Session::flash('flash','You have successfully added a tag');
										Redirect::to('rack_tagging.php');
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


							<legend>Tag Information</legend>


							<div class="form-group">
								<label class="col-md-4 control-label" for="tag_name">Tag name</label>
								<div class="col-md-4">
									<input id="tag_name" name="tag_name" placeholder="Tag name" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($rack_tag->data()->tag_name) : escape(Input::get('tag_name')); ?>">
									<span class="help-block"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="assign_to">Assign To</label>
								<div class="col-md-4">

									<select name="assign_to[]" id="assign_to" class='form-control' multiple>
										<option value=""></option>
										<?php
											foreach($user_list as $ind_user){
												echo "<option value='$ind_user->id'>".capitalize($ind_user->lastname . " " . $ind_user->firstname)."</option>";
											}
										?>
									</select>
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

			var assign_to = "<?php echo isset($id) ? $rack_tag->data()->assign_to : '' ; ?>";

			var bsp = [];
			if(assign_to.indexOf(',') >0){
				bsp = assign_to.split(',');
			} else {
				if(assign_to){
					bsp.push(assign_to);
				}
			}
			$('#assign_to').select2({
				allowClear: true,
				placeholder:'Select User'
			}).select2('val',bsp);
		});
	</script>

<?php require_once '../includes/admin/page_tail2.php'; ?>