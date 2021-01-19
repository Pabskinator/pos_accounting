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

?>

	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					<?php echo isset($editid) && !empty($editid) ? "EDIT STYLES" : "ADD STYLES"; ?>
				</h1>
			</div>
			<div class="row">
				<div class="col-md-12">

					<?php
						if(isset($editid) && !empty($editid)) {
							// edit
							$id = Encryption::encrypt_decrypt('decrypt', $editid);
							// get the data base on branch id
							$stylecls = new Style($id);
							$decoded = json_decode($stylecls->data()->styles);
						}

						// if submitted
						if (Input::exists()){
							// check token if match to our token

							if(Token::check(Input::get('token'))){

								$validation_list = array(
									'name' => array(
										'required'=> true,
										'max' => 50
									)
								);
								// get id in update

								if(!Input::get('edit')) {
									$additionalvalidation = array('unique' => 'styles');
									$finalvalidation=array_merge($validation_list['name'],$additionalvalidation);
									$validation_list['name'] = $finalvalidation;
								}

								$validate = new Validate();
								$validate->check($_POST, $validation_list);
								if($validate->passed()){
									$stylecls = new Style();
									//edit codes
									if(Input::get('edit')){
										$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
										$stylearr = [];
										$stylearr['sidebar_background_color'] = Input::get('sidebar_background_color');
										$stylearr['sidebar_text_color'] = Input::get('sidebar_text_color');
										$stylearr['sidebar_link_color'] = Input::get('sidebar_link_color');
										$stylearr['header_background_color'] = Input::get('header_background_color');
										$stylearr['header_link_color'] = Input::get('header_link_color');
										$stylearr['header_hover_color'] = Input::get('header_hover_color');
										$stylearr['panel_head_color'] = Input::get('panel_head_color');
										$stylearr['panel_border_color'] = Input::get('panel_border_color');
										$stylearr['btnp_background_color'] = Input::get('btnp_background_color');
										$stylearr['btnp_hover_color'] = Input::get('btnp_hover_color');
										$encoded = json_encode($stylearr);
										try{
											$stylecls->update(array(
												'name' => Input::get('name'),
												'styles' => $encoded
											), $id);
											Session::flash('flash','Themes information has been successfully updated');
											Redirect::to('style_config.php');
										} catch(Exception $e) {
											die($e->getMessage());
										}
									} else {
										// insert codes
										$stylearr = [];
										$stylearr['sidebar_background_color'] = Input::get('sidebar_background_color');
										$stylearr['sidebar_text_color'] = Input::get('sidebar_text_color');
										$stylearr['sidebar_link_color'] = Input::get('sidebar_link_color');
										$stylearr['header_background_color'] = Input::get('header_background_color');
										$stylearr['header_link_color'] = Input::get('header_link_color');
										$stylearr['header_hover_color'] = Input::get('header_hover_color');
										$stylearr['panel_head_color'] = Input::get('panel_head_color');
										$stylearr['panel_border_color'] = Input::get('panel_border_color');
										$stylearr['btnp_background_color'] = Input::get('btnp_background_color');
										$stylearr['btnp_hover_color'] = Input::get('btnp_hover_color');

										$encoded = json_encode($stylearr);
										try {
											$stylecls->create(array(
												'name' => Input::get('name'),
												'is_active' => 1,
												'created' => strtotime(date('Y/m/d H:i:s')),
												'modified' => strtotime(date('Y/m/d H:i:s')),
												'styles' => $encoded,
												'company_id' => $user->data()->company_id
											));
										} catch(Exception $e){
											die($e);
										}
										Session::flash('flash','You have successfully added a theme');
										Redirect::to('style_config.php');
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


							<legend>Styles Information</legend>

							<div class="form-group">
								<label class="col-md-4 control-label" for="name">Theme name</label>
								<div class="col-md-4">
									<input id="branchName" name="name" placeholder="Theme Name" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($stylecls->data()->name) : escape(Input::get('name')); ?>">
									<span class="help-block"></span>
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-3">
									<strong>Header Background Color</strong>
									<input  class='pull-right' type="color" id="header_background_color" name="header_background_color" value="<?php echo isset($id) ? escape($decoded->header_background_color) : escape(Input::get('header_background_color')); ?>" >
									<span class="help-block"></span>
								</div>
								<div class="col-md-3">
									<strong>Header Link Color</strong>
									<input class='pull-right'  type="color" id="header_link_color" name="header_link_color" value="<?php echo isset($id) ? escape($decoded->header_link_color) : escape(Input::get('header_link_color')); ?>" >
									<span class="help-block"></span>
								</div>
								<div class="col-md-3">
									<strong>Header Hover Link Color</strong>
									<input class='pull-right'   type="color" id="header_hover_color" name="header_hover_color" value="<?php echo isset($id) ? escape($decoded->header_hover_color) : escape(Input::get('header_hover_color')); ?>" >
									<span class="help-block"></span>
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-3">
									<strong>Sidebar Background Color</strong>
									<input  class='pull-right'  type="color" id="sidebar_background_color" name="sidebar_background_color" value="<?php echo isset($id) ? escape($decoded->sidebar_background_color) : escape(Input::get('sidebar_background_color')); ?>" >
									<span class="help-block"></span>
								</div>
								<div class="col-md-3">
									<strong>Sidebar Text Color</strong>
									<input  class='pull-right'  type="color" id="sidebar_text_color" name="sidebar_text_color" value="<?php echo isset($id) ? escape($decoded->sidebar_text_color) : escape(Input::get('sidebar_text_color')); ?>" >
									<span class="help-block"></span>
								</div>
								<div class="col-md-3">
									<strong>Sidebar Link Color</strong>
									<input class='pull-right'  type="color" id="sidebar_link_color" name="sidebar_link_color" value="<?php echo isset($id) ? escape($decoded->sidebar_link_color) : escape(Input::get('sidebar_link_color')); ?>" >
									<span class="help-block"></span>
								</div>

							</div>
							<div class="form-group">
								<div class="col-md-3">
									<strong>Panel Head Color</strong>
									<input  class='pull-right'  type="color" id="panel_head_color" name="panel_head_color" value="<?php echo isset($id) ? escape($decoded->panel_head_color) : escape(Input::get('panel_head_color')); ?>" >
									<span class="help-block"></span>
								</div>
								<div class="col-md-3">
									<strong>Panel Border Color</strong>
									<input class='pull-right'  type="color" id="panel_border_color" name="panel_border_color" value="<?php echo isset($id) ? escape($decoded->panel_border_color) : escape(Input::get('panel_border_color')); ?>" >
									<span class="help-block"></span>
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-3">
									<strong>Button Primary</strong>
									<input  class='pull-right'  type="color" id="btnp_background_color" name="btnp_background_color" value="<?php echo isset($id) ? escape($decoded->btnp_background_color) : escape(Input::get('btnp_background_color')); ?>" >
									<span class="help-block"></span>
								</div>
								<div class="col-md-3">
									<strong>Button Primary Hover</strong>
									<input class='pull-right'  type="color" id="btnp_hover_color" name="btnp_hover_color" value="<?php echo isset($id) ? escape($decoded->btnp_hover_color) : escape(Input::get('btnp_hover_color')); ?>" >
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