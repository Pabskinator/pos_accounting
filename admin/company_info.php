<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('item')) {
		// redirect to denied page
		Redirect::to(1);
	}
	if(isset($_GET['edit'])) {
		$editid = $_GET['edit'];
	} else {
		$editid = 0;
	}

	if (Input::exists()){
		// check token if match to our token
		if(Token::check(Input::get('token'))){

			$validation_list = array(
				'name' => array(
					'required'=> true,
					'max' => 50
				),
				'description' => array(
					'required'=> true,
					'min' => 6,
					'max'=> 100
				),
				'address' => array(
					'required'=> true,
					'max'=> 200
				),
				'bc_prefix' => array(
					'required'=> true,
					'max' => 20
				)
			);

			$validate = new Validate();
			$validate->check($_POST, $validation_list);
			if($validate->passed()){
				if(!empty($_FILES["login_bg"]["name"])){
					$target_dir = "../css/img/";
					$target_file_bg = $target_dir. basename($_FILES["login_bg"]["name"]);
					$uploadOk = 1;
					$imageFileType = pathinfo($target_file_bg,PATHINFO_EXTENSION);
					$errorfile ="";
					if ($_FILES["login_bg"]["size"] > 800000) { // 800kb
						$errorfile .= "<p>Sorry, your file is too large.</p>";
						$uploadOk = 0;
					}
					if($imageFileType != "jpg" ) {
						$errorfile .= "<p>Sorry, only JPG files are allowed.</p>";
						$uploadOk = 0;
					}
					if ($uploadOk == 0) {
						//echo $errorfile;
					} else {
						move_uploaded_file($_FILES["login_bg"]["tmp_name"], $target_dir ."login_bg.jpg");
					}
				}
				if(!empty($_FILES["favicon"]["name"])){
					$target_dir = "../css/img/";
					$target_file_favicon = $target_dir. basename($_FILES["favicon"]["name"]);
					$uploadOk = 1;
					$imageFileType = pathinfo($target_file_favicon,PATHINFO_EXTENSION);
					$errorfile ="";
					if ($_FILES["favicon"]["size"] > 50000) { // 50KB
						$errorfile .= "<p>Sorry, your favicon is too large.</p>";
						$uploadOk = 0;
					}
					if($imageFileType != "jpg" ) {
						$errorfile .= "<p>Sorry, only JPG files are allowed.</p>";
						$uploadOk = 0;
					}
					if ($uploadOk == 0) {
						echo $errorfile;
					} else {
						if(move_uploaded_file($_FILES["favicon"]["tmp_name"], $target_dir ."logo.jpg")){
							echo "ok";
						} else {
							echo "Not ok";
						}
					}
				}

				$editc = new Company();
				$toupdate = array(
					'name' => Input::get('name'),
					'description' => Input::get('description'),
					'address' =>  Input::get('address'),
					'bc_prefix' => Input::get('bc_prefix'),
					'web_address' => Input::get('web_address'),
					'email' => Input::get('email'),
					'contact_number' => Input::get('contact_number'),
					'signatory' => Input::get('signatory'),
				);
				$editc->update($toupdate,Input::get('edit'));

			}
		}
	}
	$company_id = $user->data()->company_id;
	$co = new Company($company_id);

?>

	<!-- Page content -->
	<div id="page-content-wrapper">
		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					COMPANY INFORMATION
				</h1>
			</div>

				<form class="form-horizontal" action="" method="POST" enctype="multipart/form-data">
					<fieldset>


						<legend>Company Information</legend>
						<?php
							if ($uploadOk == 0) echo $errorfile;
						?>
						<div class="form-group">
							<label class="col-md-4 control-label" for="name">Name</label>
							<div class="col-md-4">
								<input id="name" name="name" placeholder="Company Name" class="form-control input-md" type="text" value="<?php echo $co->data()->name; ?>">
								<span class="help-block"></span>
							</div>
						</div>

						<!-- Text input-->
						<div class="form-group">
							<label class="col-md-4 control-label" for="description">Description</label>
							<div class="col-md-4">
								<input id="description" name="description" placeholder="Description" class="form-control input-md" type="text" value="<?php echo $co->data()->description; ?>">
								<span class="help-block"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label" for="address">Address</label>
							<div class="col-md-4">
								<input id="address" name="address" placeholder="Address" class="form-control input-md" type="text" value="<?php echo $co->data()->address; ?>">
								<span class="help-block">Company Address</span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label" for="web_address">Website</label>
							<div class="col-md-4">
								<input id="web_address" name="web_address" placeholder="Website" class="form-control input-md" type="text" value="<?php echo $co->data()->web_address; ?>">
								<span class="help-block">Website</span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label" for="email">Email</label>
							<div class="col-md-4">
								<input id="email" name="email" placeholder="Email" class="form-control input-md" type="text" value="<?php echo $co->data()->email; ?>">
								<span class="help-block">Email</span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label" for="contact_number">Contact Number</label>
							<div class="col-md-4">
								<input id="contact_number" name="contact_number" placeholder="Contact Number" class="form-control input-md" type="text" value="<?php echo $co->data()->contact_number; ?>">
								<span class="help-block">Contact Number</span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label" for="signatory">Name of signatory</label>
							<div class="col-md-4">
								<input id="signatory" name="signatory" placeholder="Name" class="form-control input-md" type="text" value="<?php echo $co->data()->signatory; ?>">
								<span class="help-block">Enter name</span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label" for="description">Barcode Prefix</label>
							<div class="col-md-4">
								<input id="bc_prefix" name="bc_prefix" placeholder="Barcode Prefix" class="form-control input-md" type="text" value="<?php echo $co->data()->bc_prefix; ?>">
								<span class="help-block"></span>
							</div>
						</div>
						<div class="form-group">
							<?php if(file_exists("../css/img/login_bg.jpg")){
								?>
								<div style='margin-bottom:20px;' class='text-center'>
									<img style="width:300px;height:auto;" src="../css/img/login_bg.jpg?<?php echo uniqid(); ?>" alt="Background">
								</div>
								<?php
							}?>
							<label class="col-md-4 control-label" for="description">Login Background</label>
							<div class="col-md-4">
								<input id="login_bg" name="login_bg" placeholder="Login Background" class="form-control input-md" type="file" >
								<span class="help-block">Only accepting .jpg format. Max file size is 800KB</span>
							</div>
						</div>
						<div class="form-group">
							<?php if(file_exists("../css/img/logo.jpg")){
								?>
								<div style='margin-bottom:20px;' class='text-center'>
									<img style="width:32px;height:auto;" src="../css/img/logo.jpg?<?php echo uniqid(); ?>" alt="Background">
								</div>
								<?php
							}?>
							<label class="col-md-4 control-label" for="description">Favicon</label>
							<div class="col-md-4">
								<input id="favicon" name="favicon" placeholder="Favicon" class="form-control input-md" type="file" >
								<span class="help-block">Only accepting .jpg format. Max file size is 50KB. Preferably 32px by 32px</span>
							</div>
						</div>

						<!-- Button (Double) -->
						<div class="form-group">
							<label class="col-md-4 control-label" for="button1id"></label>
							<div class="col-md-8">
								<input type='submit' class='btn btn-success' name='btnSave' value='SAVE'/>
								<input type='hidden' name='token' value=<?php echo Token::generate(); ?>>
								<input type='hidden' name='edit' value=<?php echo $co->data()->id; ?>>

							</div>
						</div>
					</fieldset>
				</form>

		</div>
	</div> <!-- end page content wrapper-->


<?php require_once '../includes/admin/page_tail2.php'; ?>