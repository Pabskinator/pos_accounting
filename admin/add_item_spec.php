<?php
	// $user have all the properties and method of the current user
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('branch')) {
		// redirect to denied page
		Redirect::to(1);
	}

	if(isset($_GET['edit'])) {
		$editid = $_GET['edit'];
	} else {
		$editid = 0;
	}

	if(isset($editid) && !empty($editid)) {
		// edit
		$id = Encryption::encrypt_decrypt('decrypt', $editid);
		// get the data base on branch id
		$item_spec = new Item_specification($id);

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
				'spec_type' => array(
					'required'=> true,
					'max' => 50
				)
			);
			// get id in update




			$validate = new Validate();
			$validate->check($_POST, $validation_list);
			if($validate->passed()){
				$os = new Item_specification();
				//edit codes
				if(Input::get('edit')){
					$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
					try{
						$arrupdate = array(
							'name' => Input::get('name'),
							'spec_type' => strtolower(Input::get('spec_type'))
						);

						$os->update($arrupdate, $id);
						Session::flash('flash','Information has been successfully updated');
						Redirect::to('item_spec.php');
					} catch(Exception $e) {
						die($e->getMessage());
					}
				} else {
					// insert codes
					try {
						$inserarr = array(
							'name' => Input::get('name'),
							'spec_type' => strtolower(Input::get('spec_type')),
							'created' => strtotime(date('Y/m/d H:i:s')),
							'is_active' => 1,
							'company_id' => $user->data()->company_id
						);
						$os->create($inserarr);
					} catch(Exception $e){
						die($e);
					}
					Session::flash('flash','You have successfully added a record');
					Redirect::to('item_spec.php');
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
	<!-- Page content -->
	<div id="page-content-wrapper">
		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					<?php echo isset($editid) && !empty($editid) ? "EDIT RECORD" : "ADD RECORD"; ?>
				</h1>
			</div>

			<div class="row">
				<div class="col-md-12">
					<form class="form-horizontal" action="" method="POST">
						<fieldset>
							<legend></legend>
							<div class="form-group">
								<label class="col-md-4 control-label" for="name">Name</label>
								<div class="col-md-4">
									<input id="name" name="name" placeholder="Enter Name" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($item_spec->data()->name) : escape(Input::get('name')); ?>">
									<span class="help-block"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="spec_type">Spec Type</label>
								<div class="col-md-4">
									<input id="spec_type" name="spec_type" placeholder="Enter Type" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($item_spec->data()->spec_type) : escape(Input::get('spec_type')); ?>">
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
	<script>
		$(function(){

		});
	</script>
<?php
	require_once '../includes/admin/page_tail2.php';