<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(false) {
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
					<?php echo isset($editid) && !empty($editid) ? "EDIT AUTH" : "ADD AUTH"; ?>
				</h1>
			</div>
			<div class="row">
				<div class="col-md-12">

					<?php
						$user_info = [];
						$branch_info = [];
						if(isset($editid) && !empty($editid)) {
							// edit
							$id = Encryption::encrypt_decrypt('decrypt', $editid);
							// get the data base on branch id
							$auth = new Approval_auth($id);
							$user_name = "";
							$user_id = "";
							$update_user = new User($auth->data()->user_id);
							$user_name = ucwords($update_user->data()->firstname . " "  . $update_user->data()->lastname);
							$user_id = $update_user->data()->id;
							$user_info = [ 'id'=>  $user_id ,  'text' => $user_name ];
							$exbranch = explode(",",$auth->data()->ref_values);
							$branch_info = [];
							foreach($exbranch as $ex){
								$binfo = new Branch($ex);
								$branch_info[] = ['id' => $binfo->data()->id,'text' => $binfo->data()->name];
							}



						}

						// if submitted
						if (Input::exists()){
							// check token if match to our token
							if(Token::check(Input::get('token'))){

								$validation_list = array(
									'user_id' => array(
										'required'=> true
									),
									'ref_values' => array(
										'required'=> true
									)

								);


								$validate = new Validate();
								$validate->check($_POST, $validation_list);
								if($validate->passed()){
									$auth = new Approval_auth();
									//edit codes
									if(Input::get('edit')){
										$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
										try{
											$authupdate = array(
												'user_id' => Input::get('user_id'),
												'ref_values' => Input::get('ref_values')
											);

											$auth->update($authupdate, $id);
											Session::flash('flash','Auth information has been successfully updated');
											Redirect::to('approval_auth.php');
										} catch(Exception $e) {
											die($e->getMessage());
										}
									} else {
										// insert codes
										try {
											$checker = $auth->getMyAuth(Input::get('user_id'));
										
											if(isset($checker) && $checker->id){
												$el ='';
												echo "<div class='alert alert-danger'>";

												$el.= "Record already exists<br/>" ;

												echo "$el</div>";
											} else {
												$inserarr = array(
													'user_id' => Input::get('user_id'),
													'ref_values' => Input::get('ref_values'),
													'ref_table' => 'wh',
													'created' => strtotime(date('Y/m/d H:i:s')),
													'company_id' => $user->data()->company_id,
													'is_active'=>1
												);

												$auth->create($inserarr);
												Session::flash('flash','You have successfully added auth to a user');
												Redirect::to('approval_auth.php');
											}

										} catch(Exception $e){
											die($e);
										}

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


							<legend>Auth Information</legend>


							<div class="form-group">
								<label class="col-md-4 control-label" for="user_id">User</label>
								<div class="col-md-4">
									<input type="text" id='user_id' name='user_id' class='form-control input-md'>
								</div>
							</div>

							<!-- Text input-->
							<div class="form-group">
								<label class="col-md-4 control-label" for="ref_values">Manage Branch</label>
								<div class="col-md-4">
									<input id="ref_values" name="ref_values" placeholder="Manage" class="form-control input-md" type="text">

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
			var ref_values = $('#ref_values');

			var user_info = '<?php echo json_encode($user_info); ?>';
			var branch_info = '<?php echo json_encode($branch_info); ?>';

			user_id.select2({
				placeholder: 'Search user',
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
			ref_values.select2({
				placeholder: 'Search Branch',
				allowClear: true,
				multiple: true,
				minimumInputLength: 2,
				ajax: {
					url: '../ajax/ajax_json.php',
					dataType: 'json',
					type: "POST",
					quietMillis: 50,
					data: function (term) {
						return {
							q: term,
							functionName:'branches'
						};
					},
					results: function (data) {
						return {
							results: $.map(data, function (item) {
								return {
									text: item.name,
									slug: item.name,
									id: item.id
								}
							})
						};
					}
				}
			});

			try{
				user_info = JSON.parse(user_info);
				if(user_info.id){
					console.log(user_info);
					user_id.select2('data',{id: user_info.id,text: user_info.text});
				}
				branch_info = JSON.parse(branch_info);
				if(branch_info.length){
					console.log(branch_info);
					ref_values.select2('data',branch_info);
				}


			} catch(e){

			}


		});
	</script>

<?php require_once '../includes/admin/page_tail2.php'; ?>