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
					<?php echo isset($editid) && !empty($editid) ? "EDIT DOCUMENT" : "ADD DOCUMENT"; ?>
				</h1>
			</div>
			<div class="row">
				<div class="col-md-12">

					<?php
						if(isset($editid) && !empty($editid)) {
							// edit
							$id = Encryption::encrypt_decrypt('decrypt', $editid);
							// get the data base on branch id
							$editDoc = new Doc_color($id);
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
									'doc_type' => array(
										'required'=> true
									)
								);

								$validate = new Validate();
								$validate->check($_POST, $validation_list);
								if($validate->passed()){
									$newchar = new Doc_color();
									//edit codes
									if(Input::get('edit')){
										$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
										try{
											$newchar->update(array(
												'name' => Input::get('name'),
												'doc_type' => Input::get('doc_type')
											), $id);
											Session::flash('characteristicsflash','Document information has been successfully updated');
											Redirect::to('doc-color.php');
										} catch(Exception $e) {
											die($e->getMessage());
										}
									} else {
										// insert codes
										try {

											$newchar->create(array(
												'name' => Input::get('name'),
												'company_id' => $user->data()->company_id,
												'doc_type' => Input::get('doc_type'),
												'is_active' => 1,
												'created' => strtotime(date('Y/m/d H:i:s')),
											));
											$lastid = $newchar->getInsertedId();

										} catch(Exception $e){
											die($e);
										}
										Session::flash('flash','You have successfully added an item');
										Redirect::to('doc-color.php');
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


							<legend>Form Information</legend>

							<div class="form-group">
								<label class="col-md-4 control-label" for="doc_type">Type</label>
								<div class="col-md-4">
									<select name="doc_type" id="doc_type" class="form-control">
										<option value="">Select Type</option>
										<option <?php echo (isset($editDoc) && $editDoc->data()->doc_type == 1) ? 'selected' : ''; ?> value="1">Invoice</option>
										<option <?php echo (isset($editDoc) &&  $editDoc->data()->doc_type == 2) ? 'selected' : ''; ?> value="2">DR</option>
										<option <?php echo (isset($editDoc) &&  $editDoc->data()->doc_type == 3) ? 'selected' : ''; ?> value="3">IR</option>

									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="name">Name</label>
								<div class="col-md-4">
									<input id="name" name="name" placeholder="Characteristics Name" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($editDoc->data()->name) : escape(Input::get('name')); ?>">
									<span class="help-block"></span>
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


<?php require_once '../includes/admin/page_tail2.php'; ?>