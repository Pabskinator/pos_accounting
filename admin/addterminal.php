<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';

	if(!$user->hasPermission('terminal')) {
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
					<?php echo isset($editid) && !empty($editid) ? "EDIT TERMINAL" : "ADD TERMINAL"; ?>
				</h1>
			</div>
			<?php include 'includes/terminal_nav.php'; ?> <br><br>
			<div class="row">
				<div class="col-md-12">

					<?php


						if(isset($editid) && !empty($editid)) {
							// edit
							$id = Encryption::encrypt_decrypt('decrypt', $editid);
							// get the data base on branch id
							$terminal = new Terminal($id);
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
									'branch_id' => array(
										'required'=> true,
										'isnumber' => true
									),
									'invoice' => array(
										'required'=> true,
										'isnumber' => true
									),
									'end_invoice' => array(
										'required'=> true,
										'isnumber' => true
									),
									'dr' => array(
										'required'=> true,
										'isnumber' => true
									),
									'end_dr' => array(
										'required'=> true,
										'isnumber' => true
									),
									'invoice_limit' => array(
										'required'=> true,
										'isnumber' => true
									),
									'dr_limit' => array(
										'required'=> true,
										'isnumber' => true
									),
									'ir' => array(
										'required'=> true,
										'isnumber' => true
									),
									'end_ir' => array(
										'required'=> true,
										'isnumber' => true
									),
									'ir_limit' => array(
										'required'=> true,
										'isnumber' => true
									)
								);



								$validate = new Validate();
								$validate->check($_POST, $validation_list);
								if($validate->passed()){
									$terminal = new Terminal();
									//edit codes
									if(Input::get('edit')){
										$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
										try{
											$startinvoice = Input::get('invoice') - 1;
											$startdr = Input::get('dr') -1;
											$startir = Input::get('ir') -1;
											$startsv = Input::get('sv') -1;
											$startsr = Input::get('sr') -1;
											$startts = Input::get('ts') -1;
											//$is_assigned = (Input::get('is_assigned')) ? 1 : 0;

											$is_assigned = 0;

											$arr = array(
												'name' => Input::get('name'),
												'branch_id' => Input::get('branch_id'),
												'modified' => strtotime(date('Y/m/d H:i:s')),
												'is_assigned' => $is_assigned,
												'invoice' => $startinvoice,
												'end_invoice' => Input::get('end_invoice'),
												'dr' => $startdr,
												'end_dr' => Input::get('end_dr'),
												'ir' => $startir,
												'end_ir' => Input::get('end_ir'),
												'sv' => $startsv,
												'end_sv' => Input::get('end_sv'),
												'invoice_limit' => Input::get('invoice_limit'),
												'dr_limit' => Input::get('dr_limit'),
												'ir_limit' => Input::get('ir_limit'),
												'sv_limit' => Input::get('sv_limit'),
												'speed_opt' => Input::get('speed_opt'),
												'use_printer' => Input::get('use_printer'),
												'data_sync' => Input::get('data_sync'),
												'news_print' => Input::get('news_print'),
												'print_inv' => Input::get('print_inv'),
												'print_dr' => Input::get('print_dr'),
												'print_ir' => Input::get('print_ir'),
												'pref_inv' => Input::get('pref_inv'),
												'pref_dr' => Input::get('pref_dr'),
												'pref_ir' => Input::get('pref_ir'),
												'pref_sv' => Input::get('pref_sv'),
												'suf_inv' => Input::get('suf_inv'),
												'suf_dr' => Input::get('suf_dr'),
												'suf_ir' => Input::get('suf_ir'),
												'suf_sv' => Input::get('suf_sv')
											);
											if(Configuration::getValue('has_sv') == 1){
												$arr_sv = [
													'pref_sv' => Input::get('pref_sv'),
													'sv' => $startsv,
													'end_sv' => Input::get('end_sv'),
													'sv_limit' => Input::get('sv_limit'),
													'suf_sv' => Input::get('suf_sv')
												];
												$arr = array_merge($arr,$arr_sv);
											}
											if(Configuration::getValue('has_sr') == 1){
												$arr_sr = [
													'pref_sr' => Input::get('pref_sr'),
													'sr' => $startsr,
													'end_sr' => Input::get('end_sr'),
													'sr_limit' => Input::get('sr_limit'),
													'suf_sr' => Input::get('suf_sr')
												];
												$arr = array_merge($arr,$arr_sr);
											}
											if(Configuration::getValue('has_ts') == 1){
												$arr_ts = [
													'pref_ts' => Input::get('pref_ts'),
													'ts' => $startts,
													'end_ts' => Input::get('end_ts'),
													'ts_limit' => Input::get('ts_limit'),
													'suf_ts' => Input::get('suf_ts')
												];
												$arr = array_merge($arr,$arr_ts);
											}

											$terminal->update($arr, $id);

											Log::addLog(
												$user->data()->id,
												$user->data()->company_id,
												"Update Terminal " . Input::get('name'),
												"addterminal.php"
											);


											Session::flash('terminalflash','Terminal information has been successfully updated');
											Redirect::to('terminal.php');
										} catch(Exception $e) {
											die($e->getMessage());
										}
									} else {
										// insert codes
										try {
											$startinvoice = Input::get('invoice') - 1;
											$startdr = Input::get('dr') -1;
											$startir = Input::get('ir') -1;
											$startsv = Input::get('sv') -1;
											$startsr = Input::get('sr') -1;
											$startts = Input::get('ts') -1;


											$arr = array(
												'name' => Input::get('name'),
												'branch_id' => Input::get('branch_id'),
												'created' => strtotime(date('Y/m/d H:i:s')),
												'modified' => strtotime(date('Y/m/d H:i:s')),
												'pref_inv' => Input::get('pref_inv'),
												'pref_dr' => Input::get('pref_dr'),
												'pref_ir' => Input::get('pref_ir'),
												'invoice' => $startinvoice,
												'end_invoice' => Input::get('end_invoice'),
												'dr' => $startdr,
												'end_dr' => Input::get('end_dr'),
												'ir' => $startir,
												'end_ir' => Input::get('end_ir'),
												'invoice_limit' => Input::get('invoice_limit'),
												'dr_limit' => Input::get('dr_limit'),
												'ir_limit' => Input::get('ir_limit'),
												'speed_opt' => Input::get('speed_opt'),
												'use_printer' => Input::get('use_printer'),
												'data_sync' => Input::get('data_sync'),
												'news_print' => Input::get('news_print'),
												'print_inv' => Input::get('print_inv'),
												'print_dr' => Input::get('print_dr'),
												'print_ir' => Input::get('print_ir'),
												'suf_inv' => Input::get('suf_inv'),
												'suf_dr' => Input::get('suf_dr'),
												'suf_ir' => Input::get('suf_ir')
											);

											if(Configuration::getValue('has_sv') == 1){
												$arr_sv = [
													'pref_sv' => Input::get('pref_sv'),
													'sv' => $startsv,
													'end_sv' => Input::get('end_sv'),
													'sv_limit' => Input::get('sv_limit'),
													'suf_sv' => Input::get('suf_sv')
												];
												$arr = array_merge($arr,$arr_sv);
											}
											if(Configuration::getValue('has_sr') == 1){
												$arr_sr = [
													'pref_sr' => Input::get('pref_sr'),
													'sr' => $startsr,
													'end_sr' => Input::get('end_sr'),
													'sr_limit' => Input::get('sr_limit'),
													'suf_sr' => Input::get('suf_sr')
												];
												$arr = array_merge($arr,$arr_sr);
											}
											if(Configuration::getValue('has_ts') == 1){
												$arr_ts = [
													'pref_ts' => Input::get('pref_ts'),
													'ts' => $startts,
													'end_ts' => Input::get('end_ts'),
													'ts_limit' => Input::get('ts_limit'),
													'suf_ts' => Input::get('suf_ts')
												];
												$arr = array_merge($arr,$arr_ts);
											}
											$terminal->create($arr);

											Log::addLog(
												$user->data()->id,
												$user->data()->company_id,
												"Add Terminal " . Input::get('name'),
												"addterminal.php"
											);

										} catch(Exception $e){
											die($e);
										}
										Session::flash('terminalflash','You have successfully added a Terminal');
										Redirect::to('terminal.php');
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


							<legend>Terminal Information</legend>
							<div class="form-group">
							<?php if(isset($id)){
							?>
									<label class="col-md-1 control-label" for="checkboxes">Is assigned?</label>
									<div class="col-md-3">
										<label class="checkbox-inline" for="is_assigned">
											<input name="is_assigned" id="is_assigned" value="1" type="checkbox"
												<?php
													echo ($terminal->data()->is_assigned) ? "checked" : "";
												?>
												>
											Uncheck to free this terminal
										</label>
									</div>
							<?php
							}
							?>
								<label class="col-md-1 control-label" for="name">Terminal Name</label>
								<div class="col-md-3">
									<input id="branchName" name="name" placeholder="Terminal Name" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($terminal->data()->name) : escape(Input::get('name')); ?>">
									<span class="help-block">Name of the terminal</span>
								</div>
								<label class="col-md-1 control-label" for="branch_id">Branch</label>
								<div class="col-md-3">
									<select id="branch_id" name="branch_id" class="form-control">
										<option value=''>--Select Branch--</option>
										<?php
											$branch = new Branch();
											if($user->hasPermission('is_franchisee')){
												$branches =  $branch->get_active('branches',array('id' ,'=',$user->data()->branch_id));
											} else {
												$branches =  $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));
											}

											foreach($branches as $b){
												$a = isset($id) ? $terminal->data()->branch_id : escape(Input::get('branch_id'));

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
									<span class="help-block">From what branch</span>
								</div>
							</div>

							<div class="form-group">
								<label class="col-md-1 control-label" for="pref_inv">Prefix <?php echo INVOICE_LABEL; ?></label>
								<div class="col-md-3">
									<input id="pref_inv" name="pref_inv" placeholder="Prefix <?php echo INVOICE_LABEL; ?>" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($terminal->data()->pref_inv) : escape(Input::get('pref_inv')); ?>">
									<span class="help-block"></span>
								</div>
								<label class="col-md-1 control-label" for="suf_inv">Suffix <?php echo INVOICE_LABEL; ?></label>
								<div class="col-md-3">
									<input id="suf_inv" name="suf_inv" placeholder="Suffix <?php echo INVOICE_LABEL; ?>" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($terminal->data()->suf_inv) : escape(Input::get('suf_inv')); ?>">
									<span class="help-block"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-1 control-label" for="invoice">Start <?php echo INVOICE_LABEL; ?></label>
								<div class="col-md-3">
									<input id="invoice" name="invoice" placeholder="Start <?php echo INVOICE_LABEL; ?>" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($terminal->data()->invoice+1) : escape(Input::get('invoice')); ?>">
									<span class="help-block">Start of <?php echo INVOICE_LABEL; ?> number</span>
								</div>
								<label class="col-md-1 control-label" for="end_invoice">End <?php echo INVOICE_LABEL; ?></label>
								<div class="col-md-3">
									<input id="end_invoice" name="end_invoice" placeholder="End <?php echo INVOICE_LABEL; ?>" class="form-control input-md" type="text" value="<?php echo isset($id) ? $terminal->data()->end_invoice : escape(Input::get('end_invoice')); ?>">
									<span class="help-block">End of <?php echo INVOICE_LABEL; ?> number</span>
								</div>
								<label class="col-md-1 control-label" for="invoice_limit"><?php echo INVOICE_LABEL; ?> Limit</label>
								<div class="col-md-3">
									<input id="invoice_limit" name="invoice_limit" placeholder="<?php echo INVOICE_LABEL; ?> Limit" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($terminal->data()->invoice_limit) : escape(Input::get('invoice_limit')); ?>">
									<span class="help-block">Number of items per <?php echo INVOICE_LABEL; ?></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-1 control-label" for="pref_dr">Prefix <?php echo DR_LABEL; ?></label>
								<div class="col-md-3">
									<input id="pref_dr" name="pref_dr" placeholder="Prefix <?php echo DR_LABEL; ?>" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($terminal->data()->pref_dr) : escape(Input::get('pref_dr')); ?>">
									<span class="help-block"></span>
								</div>
								<label class="col-md-1 control-label" for="suf_dr">Suffix <?php echo DR_LABEL; ?></label>
								<div class="col-md-3">
									<input id="suf_dr" name="suf_dr" placeholder="Suffix <?php echo DR_LABEL; ?>" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($terminal->data()->suf_dr) : escape(Input::get('suf_dr')); ?>">
									<span class="help-block"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-1 control-label" for="dr">Start <?php echo DR_LABEL; ?></label>
								<div class="col-md-3">
									<input id="dr" name="dr" placeholder="Start <?php echo DR_LABEL; ?>" class="form-control input-md" type="text" value="<?php echo isset($id) ? $terminal->data()->dr+1 : escape(Input::get('dr')); ?>">
									<span class="help-block">Start of <?php echo DR_LABEL; ?> number</span>
								</div>
								<label class="col-md-1 control-label" for="end_dr">End <?php echo DR_LABEL; ?></label>
								<div class="col-md-3">
									<input id="end_dr" name="end_dr" placeholder="End <?php echo DR_LABEL; ?>" class="form-control input-md" type="text" value="<?php echo isset($id) ? $terminal->data()->end_dr : escape(Input::get('end_dr')); ?>">
									<span class="help-block">End of <?php echo DR_LABEL; ?> number</span>
								</div>
								<label class="col-md-1 control-label" for="dr_limit"><?php echo DR_LABEL; ?></label>
								<div class="col-md-3">
									<input id="dr_limit" name="dr_limit" placeholder="<?php echo DR_LABEL; ?> Limit" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($terminal->data()->dr_limit) : escape(Input::get('dr_limit')); ?>">
									<span class="help-block">Number of items per <?php echo DR_LABEL; ?></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-1 control-label" for="pref_ir">Prefix <?php echo PR_LABEL; ?></label>
								<div class="col-md-3">
									<input id="pref_ir" name="pref_ir" placeholder="Prefix <?php echo PR_LABEL; ?>" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($terminal->data()->pref_ir) : escape(Input::get('pref_ir')); ?>">
									<span class="help-block"></span>
								</div>
								<label class="col-md-1 control-label" for="suf_ir">Suffix <?php echo PR_LABEL; ?></label>
								<div class="col-md-3">
									<input id="suf_ir" name="suf_ir" placeholder="Suffix <?php echo PR_LABEL; ?>" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($terminal->data()->suf_ir) : escape(Input::get('suf_ir')); ?>">
									<span class="help-block"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-1 control-label" for="ir">Start <?php echo PR_LABEL; ?></label>
								<div class="col-md-3">
									<input id="ir" name="ir" placeholder="Start <?php echo PR_LABEL; ?>" class="form-control input-md" type="text" value="<?php echo isset($id) ? $terminal->data()->ir+1 : escape(Input::get('ir')); ?>">
									<span class="help-block">Start of <?php echo PR_LABEL; ?> number</span>
								</div>
								<label class="col-md-1 control-label" for="end_dr">End <?php echo PR_LABEL; ?></label>
								<div class="col-md-3">
									<input id="end_ir" name="end_ir" placeholder="End <?php echo PR_LABEL; ?>" class="form-control input-md" type="text" value="<?php echo isset($id) ? $terminal->data()->end_ir : escape(Input::get('end_ir')); ?>">
									<span class="help-block">End of <?php echo PR_LABEL; ?> number</span>
								</div>
								<label class="col-md-1 control-label" for="ir_limit"><?php echo PR_LABEL; ?> Limit</label>
								<div class="col-md-3">
									<input id="ir_limit" name="ir_limit" placeholder="<?php echo PR_LABEL; ?> Limit" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($terminal->data()->ir_limit) : escape(Input::get('ir_limit')); ?>">
									<span class="help-block">Number of items per <?php echo PR_LABEL; ?></span>
								</div>
							</div>
							<?php if(Configuration::getValue('has_sv') == 1){
								?>
								<div class="form-group">
									<label class="col-md-1 control-label" for="pref_sv">Prefix SV</label>
									<div class="col-md-3">
										<input id="pref_sv" name="pref_sv" placeholder="Prefix SV" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($terminal->data()->pref_sv) : escape(Input::get('pref_sv')); ?>">
										<span class="help-block"></span>
									</div>
									<label class="col-md-1 control-label" for="suf_sv">Suffix SV</label>
									<div class="col-md-3">
										<input id="suf_sv" name="suf_sv" placeholder="Suffix SV" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($terminal->data()->suf_sv) : escape(Input::get('suf_sv')); ?>">
										<span class="help-block"></span>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-1 control-label" for="sv">Start SV</label>
									<div class="col-md-3">
										<input id="sv" name="sv" placeholder="Start SV" class="form-control input-md" type="text" value="<?php echo isset($id) ? $terminal->data()->sv+1 : escape(Input::get('sv')); ?>">
										<span class="help-block">Start of SV number</span>
									</div>
									<label class="col-md-1 control-label" for="end_sv">End SV</label>
									<div class="col-md-3">
										<input id="end_sv" name="end_sv" placeholder="End SV" class="form-control input-md" type="text" value="<?php echo isset($id) ? $terminal->data()->end_sv : escape(Input::get('end_sv')); ?>">
										<span class="help-block">End of SV number</span>
									</div>
									<label class="col-md-1 control-label" for="sv_limit">SV Limit</label>
									<div class="col-md-3">
										<input id="sv_limit" name="sv_limit" placeholder="SV Limit" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($terminal->data()->sv_limit) : escape(Input::get('sv_limit')); ?>">
										<span class="help-block">Number of items per SV</span>
									</div>
								</div>
								<?php
							} ?>
							<?php if(Configuration::getValue('has_sr') == 1){
								?>
								<div class="form-group">
									<label class="col-md-1 control-label" for="pref_sr">Prefix SR</label>
									<div class="col-md-3">
										<input id="pref_sr" name="pref_sr" placeholder="Prefix SR" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($terminal->data()->pref_sr) : escape(Input::get('pref_sr')); ?>">
										<span class="help-block"></span>
									</div>
									<label class="col-md-1 control-label" for="suf_sr">Suffix SR</label>
									<div class="col-md-3">
										<input id="suf_sr" name="suf_sr" placeholder="Suffix SR" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($terminal->data()->suf_sr) : escape(Input::get('suf_sr')); ?>">
										<span class="help-block"></span>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-1 control-label" for="sr">Start SR</label>
									<div class="col-md-3">
										<input id="sr" name="sr" placeholder="Start SR" class="form-control input-md" type="text" value="<?php echo isset($id) ? $terminal->data()->sr+1 : escape(Input::get('sr')); ?>">
										<span class="help-block">Start of SR number</span>
									</div>
									<label class="col-md-1 control-label" for="end_sr">End SR</label>
									<div class="col-md-3">
										<input id="end_sr" name="end_sr" placeholder="End SR" class="form-control input-md" type="text" value="<?php echo isset($id) ? $terminal->data()->end_sr : escape(Input::get('end_sr')); ?>">
										<span class="help-block">End of SR number</span>
									</div>
									<label class="col-md-1 control-label" for="sr_limit">SR Limit</label>
									<div class="col-md-3">
										<input id="sr_limit" name="sr_limit" placeholder="SR Limit" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($terminal->data()->sr_limit) : escape(Input::get('sr_limit')); ?>">
										<span class="help-block">Number of items per SR</span>
									</div>
								</div>
								<?php
							} ?>

							<?php if(Configuration::getValue('has_ts') == 1){
								?>
								<div class="form-group">
									<label class="col-md-1 control-label" for="pref_ts">Prefix TS</label>
									<div class="col-md-3">
										<input id="pref_ts" name="pref_ts" placeholder="Prefix TS" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($terminal->data()->pref_ts) : escape(Input::get('pref_ts')); ?>">
										<span class="help-block"></span>
									</div>
									<label class="col-md-1 control-label" for="suf_ts">Suffix TS</label>
									<div class="col-md-3">
										<input id="suf_ts" name="suf_ts" placeholder="Suffix TS" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($terminal->data()->suf_ts) : escape(Input::get('suf_ts')); ?>">
										<span class="help-block"></span>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-1 control-label" for="ts">Start TS</label>
									<div class="col-md-3">
										<input id="ts" name="ts" placeholder="Start TS" class="form-control input-md" type="text" value="<?php echo isset($id) ? $terminal->data()->ts+1 : escape(Input::get('ts')); ?>">
										<span class="help-block">Start of TS number</span>
									</div>
									<label class="col-md-1 control-label" for="end_ts">End TS</label>
									<div class="col-md-3">
										<input id="end_ts" name="end_ts" placeholder="End TS" class="form-control input-md" type="text" value="<?php echo isset($id) ? $terminal->data()->end_ts : escape(Input::get('end_ts')); ?>">
										<span class="help-block">End of TS number</span>
									</div>
									<label class="col-md-1 control-label" for="ts_limit">TS Limit</label>
									<div class="col-md-3">
										<input id="ts_limit" name="ts_limit" placeholder="TS Limit" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($terminal->data()->ts_limit) : escape(Input::get('ts_limit')); ?>">
										<span class="help-block">Number of items per TS</span>
									</div>
								</div>
								<?php
							} ?>


							<div class="form-group">

								<input type='hidden' name="speed_opt" id="speed_opt" value='0'>
								<input name="data_sync" id="data_sync" type='hidden' class='form-control'>
								<label class="col-md-1 control-label" for="dr_limit">Use Printer</label>
								<div class="col-md-3">
									<select name="use_printer" id="use_printer" class='form-control'>
										<option value="1"
											<?php
												if(isset($id)) {
													echo (isset($terminal->data()->use_printer) && $terminal->data()->use_printer == '1') ? ' selected' : '';
												}
											?>
											>Yes
										</option>
										<option value="0"
											<?php
												if(isset($id)) {
													echo (isset($terminal->data()->use_printer) && $terminal->data()->use_printer == '0') ? ' selected' : '';
												}
											?>
											>No</option>
									</select>
									<span class="help-block">Print Invoice/DR after every transaction</span>
								</div>





							</div>
							<div class="form-group">
								<label class="col-md-1 control-label" for="news_print">Issue News Print</label>
								<div class="col-md-3">
									<select name="news_print" id="news_print" class='form-control'>
										<option value="1"
											<?php
												if(isset($id)) {
													echo (isset($terminal->data()->news_print) && $terminal->data()->news_print == '1') ? ' selected' : '';
												}
											?>
											>
											Yes
										</option>
										<option value="0"
											<?php
												if(isset($id)) {
													echo (isset($terminal->data()->news_print) && $terminal->data()->news_print == '0') ? ' selected' : '';
												}
											?>
											>No</option>
									</select>
								</div>
								<label class="col-md-1 control-label" for="print_inv">Print <?php echo INVOICE_LABEL; ?></label>
								<div class="col-md-3">
									<select name="print_inv" id="print_inv" class='form-control'>
										<option value="1"
											<?php
												if(isset($id)) {
													echo (isset($terminal->data()->print_inv) && $terminal->data()->print_inv == '1') ? ' selected' : '';
												}
											?>
											>
											Yes
										</option>
										<option value="0"
											<?php
												if(isset($id)) {
													echo (isset($terminal->data()->print_inv) && $terminal->data()->print_inv == '0') ? ' selected' : '';
												}
											?>
											>No</option>
									</select>
								</div>
								<label class="col-md-1 control-label" for="print_dr">Print <?php echo DR_LABEL; ?></label>
								<div class="col-md-3">
									<select name="print_dr" id="print_dr" class='form-control'>
										<option value="1"
											<?php
												if(isset($id)) {
													echo (isset($terminal->data()->print_dr) && $terminal->data()->print_dr == '1') ? ' selected' : '';
												}
											?>
											>
											Yes
										</option>
										<option value="0"
											<?php
												if(isset($id)) {
													echo (isset($terminal->data()->print_dr) && $terminal->data()->print_dr == '0') ? ' selected' : '';
												}
											?>
											>No</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-1 control-label" for="print_ir">Print <?php echo PR_LABEL; ?></label>
								<div class="col-md-3">
									<select name="print_ir" id="print_ir" class='form-control'>
										<option value="1"
											<?php
												if(isset($id)) {
													echo (isset($terminal->data()->print_ir) && $terminal->data()->print_ir == '1') ? ' selected' : '';
												}
											?>
											>
											Yes
										</option>
										<option value="0"
											<?php
												if(isset($id)) {
													echo (isset($terminal->data()->print_ir) && $terminal->data()->print_ir == '0') ? ' selected' : '';
												}
											?>
											>No</option>
									</select>
								</div>
							</div>
							<!-- Button (Double) -->
							<div class="form-group">

								<div class="col-md-1"></div>
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

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>