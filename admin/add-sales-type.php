<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';

	if(!$user->hasPermission('sales')) {
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
					<?php echo isset($editid) && !empty($editid) ? "EDIT TYPE" : "ADD TYPE"; ?>
				</h1>
			</div>
			<div class="row">
				<div class="col-md-12">
					<?php
						$user_list = [];
						if(isset($editid) && !empty($editid)) {
							// edit
							$id = Encryption::encrypt_decrypt('decrypt', $editid);
							// get the data base on branch id
							$salestype = new Sales_type($id);
							$user_id = $salestype->data()->user_id;

							$arr_user = explode(',',$user_id);

							foreach($arr_user as $u){
								if($u){
									$uind = new User($u);
									$user_list[] = ['text' => $uind->data()->lastname . ", " . $uind->data()->firstname, 'id' => $u];

								}
							}


						}

						// if submitted
						if (Input::exists()){
							// check token if match to our token

							if(Token::check(Input::get('token'))){

								$validation_list = array(
									'name' => array(
										'required'=> true,
										'max' => 100
									),
									'description' => array(
										'required'=> true,
										'min' => 6,
										'max' => 200
									)
								);
								// get id in update

								if(!Input::get('edit')) {
									$additionalvalidation = array('unique' => 'salestypes');
									$finalvalidation=array_merge($validation_list['name'],$additionalvalidation);
									$validation_list['name'] = $finalvalidation;
								}

								$validate = new Validate();
								$validate->check($_POST, $validation_list);
								if($validate->passed()){
									$salestype = new Sales_type();
									//edit codes
									if(Input::get('edit')){
										$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
										try{
											if(Input::get('is_default') == 1){
												$salestype->salesTypeDefault($user->data()->company_id);
											}
											$salestype->update(array(
												'name' => Input::get('name'),
												'description' => Input::get('description'),
												'is_default' => Input::get('is_default'),
												'user_id' => Input::get('user_id')
											), $id);
											Session::flash('flash','Sales type information has been successfully updated');
											Redirect::to('sales-type.php');
										} catch(Exception $e) {
											die($e->getMessage());
										}
									} else {
										// insert codes
										try {
											if(Input::get('is_default') == 1){
												$salestype->salesTypeDefault($user->data()->company_id);
											}
											$salestype->create(array(
												'name' => Input::get('name'),
												'description' => Input::get('description'),
												'created' => strtotime(date('Y/m/d H:i:s')),
												'modified' => strtotime(date('Y/m/d H:i:s')),
												'company_id' => $user->data()->company_id,
												'is_active' => 1,
												'is_default' => Input::get('is_default'),
												'user_id' => Input::get('user_id')
											));
										} catch(Exception $e){
											die($e);
										}
										Session::flash('flash','You have successfully added a sales type');
										Redirect::to('sales-type.php');
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


							<legend>Type Information</legend>

							<div class="form-group">
								<label class="col-md-4 control-label" for="is_default">Default</label>
								<div class="col-md-4">
									<input type='radio' value='1' name='is_default'
										<?php
											if (isset($id)) {
												if ($salestype->data()->is_default == 1)
												{
													echo 'checked';
												} else
												{
													echo '';
												}
											} else
											{
												echo '';
											}
										?> > Yes
									<input type='radio' value='0' name='is_default'
										<?php
											if (isset($id))
											{
												if ($salestype->data()->is_default == 0) {
													echo 'checked';
												} else {
													echo '';
												}
											} else {
												echo 'checked';
											} ?> > No
									<span class="help-block">Pre-selected option on select box</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="name">Name</label>
								<div class="col-md-4">
									<input id="name" name="name" placeholder="Name" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($salestype->data()->name) : escape(Input::get('name')); ?>">
									<span class="help-block">Sales type name</span>
								</div>
							</div>

							<!-- Text input-->
							<div class="form-group">
								<label class="col-md-4 control-label" for="description">Description</label>
								<div class="col-md-4">
									<input id="description" name="description" placeholder="Description" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($salestype->data()->description) :  escape(Input::get('description')); ?>">
									<span class="help-block">Sales type description</span>
								</div>
							</div>

							<div class="form-group">
								<label class="col-md-4 control-label" for="user_id">User</label>
								<div class="col-md-4">
									<input type="text" id='user_id' name='user_id' class='form-control input-md'>
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
			var user_id = $('#user_id');
			var cur = '<?php echo json_encode($user_list) ?>';



			user_id.select2({
				placeholder: 'Search user',
				allowClear: true,
				minimumInputLength: 2,
				multiple: true,
				ajax: {
					url: '../ajax/ajax_json.php',
					dataType: 'json',
					type: "POST",
					quietMillis: 50,
					data: function (term) {
						return {
							q: term,
							functionName:'users'
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
			try{
				var json = JSON.parse(cur);
				if(json.length){
					user_id.select2('data',json);
				}
			} catch(e){

			}
		});
	</script>

<?php require_once '../includes/admin/page_tail2.php'; ?>