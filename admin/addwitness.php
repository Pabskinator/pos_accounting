<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('witness_m')) {
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
			<?php echo isset($editid) && !empty($editid) ? "EDIT WITNESS" : "ADD WITNESS"; ?>
		</h1>
	</div>
	<div class="row">
	<div class="col-md-12">

	<?php
		if(isset($editid) && !empty($editid)) {
			// edit
			$id = Encryption::encrypt_decrypt('decrypt', $editid);
			// get the data base on branch id
			$editWitness= new Witness($id);

		}

		// if submitted
		if (Input::exists()){
			// check token if match to our token
			if(Token::check(Input::get('token'))){
				$validation_list = array(
					'lastname' => array(
						'required'=> true,
						'max' => 50
					),
					'firstname' => array(
						'required'=> true,
						'max' => 50
					),
					'middlename' => array(
						'required'=> true,
						'max' => 50
					)
				);

				$validate = new Validate();
				$validate->check($_POST, $validation_list);
				if($validate->passed()){
					$newwitness = new Witness();

					if(Input::get('edit')){
						$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
						try{
							$newwitness->update(array(
								'lastname' => Input::get('lastname'),
								'firstname' => Input::get('firstname'),
								'middlename' => Input::get('middlename'),
								'modified' => strtotime(date('Y/m/d H:i:s'))
							), $id);
							Log::addLog($user->data()->id,$user->data()->company_id,"Update Witness " . Input::get('firstname') . " " . Input::get('lastname') ,"addwitness.php");

							Session::flash('flash','Witness information has been successfully updated');
							Redirect::to('witness.php');
						} catch(Exception $e) {
							die($e->getMessage());
						}
					} else {
						// insert codes
						try {

							$newwitness->create(array(
								'lastname' => Input::get('lastname'),
								'firstname' => Input::get('firstname'),
								'middlename' => Input::get('middlename'),
								'company_id' => $user->data()->company_id,
								'is_active' => 1,
								'created' => strtotime(date('Y/m/d H:i:s')),
								'modified' => strtotime(date('Y/m/d H:i:s'))
							));

							Log::addLog($user->data()->id,$user->data()->company_id,"Insert Witness " . Input::get('firstname') . " " . Input::get('lastname') ,"addwitness.php");

						} catch(Exception $e){
							die($e);
						}
						Session::flash('flash','You have successfully added a witness');
						Redirect::to('witness.php');
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


	<legend>Witness Information</legend>


		<div class="form-group">
		<label class="col-md-4 control-label" for="lastname">Last Name</label>
		<div class="col-md-4">
			<input id="lastname" name="lastname" placeholder="Last Name" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($editWitness->data()->lastname) : escape(Input::get('lastname')); ?>">
			<span class="help-block">Last name of the member</span>
		</div>
		</div>
		<div class="form-group">
		<label class="col-md-4 control-label" for="firstname">First Name</label>
		<div class="col-md-4">
			<input id="firstname" name="firstname" placeholder="First Name" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($editWitness->data()->firstname) : escape(Input::get('firstname')); ?>">
			<span class="help-block">First name of the member</span>
		</div>
		</div>
		<div class="form-group">
		<label class="col-md-4 control-label" for="middlename">Middle Name</label>
		<div class="col-md-4">
			<input id="middlename" name="middlename" placeholder="Middle Name" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($editWitness->data()->middlename) : escape(Input::get('middlename')); ?>">
			<span class="help-block">Middle name of the member</span>
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