<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('pettycash')) {
		// redirect to denied page
		Redirect::to(1);
	}

	$myBranch= new Branch($user->data()->branch_id);
?>

	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					Request Petty Cash
				</h1>
			</div>
			<?php include 'includes/petty_nav.php'; ?>
			<div class="row">
				<div class="col-md-12">

					<?php


						// if submitted
						if (Input::exists()){
							// check token if match to our token
							if(Token::check(Input::get('token'))){

								$validation_list = array(
									'branch_id' => array(
										'required'=> true
									),
									'amount' => array(
										'required'=> true
									),
								);



								$validate = new Validate();
								$validate->check($_POST, $validation_list);
								if($validate->passed()){
									$pettycash_request = new Pettycash_request();
										try {
											$amount = Input::get('amount');
											$branch_id = Input::get('branch_id');
											if(is_numeric($branch_id) && is_numeric($amount)){
												// create a request
												$petty_request = new Pettycash_request();
												$now = time();
												$petty_request->create(array(
													'company_id' => $user->data()->company_id,
													'branch_id' => $branch_id,
													'amount' => $amount,
													'created' => $now,
													'modified' => $now,
													'status' => 1,
													'is_active' => 1,
													'user_id' =>  $user->data()->id,
													'is_starting' => 1
												));
											}
										} catch(Exception $e){
											die($e);
										}
										Session::flash('flash','Request submitted successfully.');
										Redirect::to('pettycash_approval.php');
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


							<legend>Request Information</legend>


							<div class="form-group">
								<label class="col-md-4 control-label" for="branch_id">Branch</label>
								<div class="col-md-4">
									<input type="hidden" id='branch_id' name="branch_id" value="<?php echo $myBranch->data()->id; ?>">
									<select id="branch_lbl" name="branch_lbl" class="form-control" disabled>
										<option value="<?php echo $myBranch->data()->id; ?>"><?php echo $myBranch->data()->name; ?></option>
									</select>
									<span class="help-block"></span>
								</div>
							</div>

							<!-- Text input-->
							<div class="form-group">
								<label class="col-md-4 control-label" for="amount">Amount</label>
								<div class="col-md-4">
									<input id="amount" name="amount" placeholder="Amount to request" class="form-control input-md" type="text" value=''>
									<span class="help-block"></span>
								</div>
							</div>

							<!-- Button (Double) -->
							<div class="form-group">
								<label class="col-md-4 control-label" for="button1id"></label>
								<div class="col-md-8">
									<input type='submit' class='btn btn-success' name='btnSave' value='Request Starting Petty Cash'/>
									<input type='hidden' name='token' value=<?php echo Token::generate(); ?>>
								</div>
							</div>

						</fieldset>
					</form>
				</div>

			</div>
		</div>
	</div> <!-- end page content wrapper-->


<?php require_once '../includes/admin/page_tail2.php'; ?>