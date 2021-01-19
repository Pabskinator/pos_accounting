<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	error_reporting(0);
	if(!$user->hasPermission('position')) {
		// redirect to denied page
		Redirect::to(1);
	}
	if(isset($_GET['edit'])) {
		$editid = $_GET['edit'];
	} else {
		$editid = 0;
	}

	$arr_allowed = ['sms'];
?>

	<style>
		#access_img{
			position: fixed;
			top:0px;
			right:0px;
			opacity: 0.8;
			width:480px;
			height:230px;
			display:none;
		}
	</style>
	<!-- Page content -->
	<div id="page-content-wrapper" style='overflow-x:hidden;'>

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
	<div class="content-header">
		<h1>
			<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
			<?php echo isset($editid) && !empty($editid) ? "EDIT POSITION" : "ADD POSITION"; ?>
		</h1>
	</div>


	<?php
		if(isset($editid) && !empty($editid)) {
			// edit
			$id = Encryption::encrypt_decrypt('decrypt', $editid);
			// get the data base on branch id
			if(!is_numeric($id)){
				Redirect::to('position.php');
			}
			$editPosition = new Position($id);
			$permissions = json_decode($editPosition->data()->permisions, true);


		}

		// if submitted
		if(Input::exists()) {
			// check token if match to our token
			if(Token::check(Input::get('token'))) {
				//validation
				$validation_list = array('position' => array('required' => true));
				$validate = new Validate();
				$validate->check($_POST, $validation_list);
				Log::addLog(
					$user->data()->id,
					$user->data()->company_id,
					"Update Position " .Input::get('position'),
					'addposition.php'
				);
				$hasp = true;
				if(!is_array(Input::get('permissions'))) {
					$validate->addError("There should be atleast 1 Access permission");
					$hasp = false;
				}

				if($validate->passed() && $hasp) {
					$newPosition = new Position();
					//edit codes
					if(Input::get('edit')) {
						$id = Encryption::encrypt_decrypt('decrypt', Input::get('edit'));
						$json = "{";
						$json .= "\"dashboard\":1,";
						foreach(Input::get('permissions') as $p) {
							$json .= "\"$p\":1,";
						}
						$json = rtrim($json, ",");
						$json .= "}";
						$positionInfo = array('position' => Input::get('position'), 'permisions' => $json, 'modified' => strtotime(date('Y/m/d H:i:s')));
						$newPosition->update($positionInfo, $id);
						Session::flash('positionflash', 'You have successfully Updated a Position');
						Redirect::to('position.php');
					} else {
						// insert codes
						$json = "{";
						$json .= "\"dashboard\":1,";
						foreach(Input::get('permissions') as $p) {
							$json .= "\"$p\":1,";
						}
						$json = rtrim($json, ",");
						$json .= "}";

						try {
							$newPosition->create(array('position' => Input::get('position'), 'permisions' => $json, 'created' => strtotime(date('Y/m/d H:i:s')), 'modified' => strtotime(date('Y/m/d H:i:s')), 'is_active' => 1, 'company_id' => $user->data()->company_id));

						} catch(Exception $e) {
							die($e);
						}
						Session::flash('positionflash', 'You have successfully added a Position');
						Redirect::to('position.php');
					}
				} else {
					$el = '';
					echo "<div class='alert alert-danger'>";
					foreach($validate->errors() as $error) {
						$el .= escape($error) . "<br/>";
					}
					echo "$el</div>";
				}
			}
		}
	?>

	<form class="form-horizontal" action="" method="POST">
	<fieldset>

	<div class="form-group">
		<label class="col-md-4 control-label" for="name">Position Name</label>

		<div class="col-md-4">
			<input id="position" name="position" placeholder="Position Name" class="form-control input-md" type="text" value="<?php echo isset($id) ? $editPosition->data()->position : escape(Input::get('position')); ?>">
			<span class="help-block">Name of the position</span>
		</div>
	</div>
		<legend>Access Permissions</legend>
		<div>
			<!-- Nav tabs -->
			<ul class="nav nav-tabs" role="tablist">
				<li role="presentation"  class='active'><a href="#nav_pos" aria-controls="nav_pos" role="tab" data-toggle="tab">POS</a></li>
				<li role="presentation"><a href="#nav_branch" aria-controls="nav_branch" role="tab" data-toggle="tab">Branch</a></li>
				<li role="presentation"><a href="#nav_users" aria-controls="nav_users" role="tab" data-toggle="tab">Users</a></li>
				<li role="presentation"><a href="#nav_inventory" aria-controls="nav_inventory" role="tab" data-toggle="tab">Inventory</a></li>
				<?php if($thiscompany->plan_id == 2){ ?>
				<li role="presentation"><a href="#nav_supplier" aria-controls="nav_supplier" role="tab" data-toggle="tab">Supplier</a></li>
				<?php } ?>
				<li role="presentation"><a href="#nav_item" aria-controls="nav_item" role="tab" data-toggle="tab">Item</a></li>
				<li role="presentation"><a href="#nav_member" aria-controls="nav_member" role="tab" data-toggle="tab">Member</a></li>
				<li role="presentation"><a href="#nav_sales" aria-controls="nav_sales" role="tab" data-toggle="tab">Sales</a></li>
				<!-- for pro plan only -->
				<?php if($thiscompany->plan_id == 2){ ?>
					<li role="presentation"><a href="#nav_caravan" aria-controls="nav_caravan" role="tab" data-toggle="tab">Caravan</a></li>
				<?php } ?>
				<?php if(Configuration::allowedPermission('point_system')){ ?>
					<li role="presentation"><a href="#nav_point" aria-controls="nav_point" role="tab" data-toggle="tab">Points</a></li>
				<?php } ?>
				<?php if(Configuration::allowedPermission('med')){ ?>
				<li role="presentation"><a href="#nav_medic" aria-controls="nav_medic" role="tab" data-toggle="tab">Medical</a></li>
				<?php } ?>
				<?php if(Configuration::allowedPermission('gym')){ ?>
					<li role="presentation"><a href="#nav_gym" aria-controls="nav_gym" role="tab" data-toggle="tab">Gym</a></li>
				<?php } ?>
				<li role="presentation"><a href="#nav_settings" aria-controls="nav_settings" role="tab" data-toggle="tab">Settings</a></li>
			</ul>
			<!-- Tab panes -->
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane active" id="nav_pos">

					<div class="col-md-12">
						<br>
						<div class="col-md-3">
							<label class="checkbox-inline" for="mainpos">
								<input name="permissions[]" id="mainpos" value="mainpos" type="checkbox" <?php echo isset($permissions['mainpos']) ? 'checked' : ''; ?>
									<?php  echo (in_array('mainpos', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Main POS </label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="fm_view">
								<input name="permissions[]" id="fm_view" value="fm_view" type="checkbox" <?php echo isset($permissions['fm_view']) ? 'checked' : ''; ?>
									<?php  echo (in_array('fm_view', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> View File Manager </label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="fm_manage">
								<input name="permissions[]" id="fm_manage" value="fm_manage" type="checkbox" <?php echo isset($permissions['fm_manage']) ? 'checked' : ''; ?>
									<?php  echo (in_array('fm_manage', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage File Manager </label>
						</div>

						<div style='visibility:hidden;'>
						<div class="col-md-3">
							<label class="checkbox-inline" for="mainpos_sr">
								<input name="permissions[]" id="mainpos_sr" value="mainpos_sr" type="checkbox" <?php echo isset($permissions['mainpos_sr']) ? 'checked' : ''; ?>
									<?php echo (in_array('mainpos_sr', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Sales History </label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="mainpos_ar">
								<input name="permissions[]" id="mainpos_ar" value="mainpos_ar" type="checkbox" <?php echo isset($permissions['mainpos_ar']) ? 'checked' : ''; ?>
									<?php echo (in_array('mainpos_ar', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Add reservation </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="mainpos_mr">
								<input name="permissions[]" id="mainpos_mr" value="mainpos_mr" type="checkbox" <?php echo isset($permissions['mainpos_mr']) ? 'checked' : ''; ?>
									<?php echo (in_array('mainpos_mr', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage reservation </label>
						</div>
						</div>
					</div>
				</div>
				<div role="tabpanel" class="tab-pane" id="nav_branch">
					<div class="col-md-12">
						<br>
						<div class="col-md-3">
							<label class="checkbox-inline" for="branch">
								<input name="permissions[]" id="branch" value="branch" type="checkbox" <?php echo isset($permissions['branch']) ? 'checked' : ''; ?>
									<?php echo (in_array('branch', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> View Branch </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="branch_m">
								<input name="permissions[]" id="branch_m" value="branch_m" type="checkbox" <?php echo isset($permissions['branch_m']) ? 'checked' : ''; ?>
									<?php echo (in_array('branch_m', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Branch </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="subcom">
								<input name="permissions[]" id="subcom" value="subcom" type="checkbox" <?php echo isset($permissions['subcom']) ? 'checked' : ''; ?>
									<?php echo (in_array('subcom', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> View Sub Company </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="subcom_m">
								<input name="permissions[]" id="subcom_m" value="subcom_m" type="checkbox" <?php echo isset($permissions['subcom_m']) ? 'checked' : ''; ?>
									<?php echo (in_array('subcom_m', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Sub Company </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="terminal">
								<input name="permissions[]" id="terminal" value="terminal" type="checkbox" <?php echo isset($permissions['terminal']) ? 'checked' : ''; ?>
									<?php echo (in_array('terminal', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> View Terminal </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="terminal_m">
								<input name="permissions[]" id="terminal_m" value="terminal_m" type="checkbox" <?php echo isset($permissions['terminal_m']) ? 'checked' : ''; ?>
									<?php echo (in_array('terminal_m', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Terminal </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="deposit_add_m">
								<input name="permissions[]" id="deposit_add_m" value="deposit_add_m" type="checkbox" <?php echo isset($permissions['deposit_add_m']) ? 'checked' : ''; ?>
									<?php echo (in_array('deposit_add_m', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Turn Over/ Add </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="terminal_mon">
								<input name="permissions[]" id="terminal_mon" value="terminal_mon" type="checkbox" <?php echo isset($permissions['terminal_mon']) ? 'checked' : ''; ?>
									<?php echo (in_array('terminal_mon', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Terminal Monitoring </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="pettycash">
								<input name="permissions[]" id="pettycash" value="pettycash" type="checkbox" <?php echo isset($permissions['pettycash']) ? 'checked' : ''; ?>
									<?php echo (in_array('pettycash', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> View Petty Cash </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="pettycash_r">
								<input name="permissions[]" id="pettycash_r" value="pettycash_r" type="checkbox" <?php echo isset($permissions['pettycash_r']) ? 'checked' : ''; ?>
									<?php echo (in_array('pettycash_r', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Request Petty Cash </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="pettycash_m">
								<input name="permissions[]" id="pettycash_m" value="pettycash_m" type="checkbox" <?php echo isset($permissions['pettycash_m']) ? 'checked' : ''; ?>
									<?php echo (in_array('pettycash_m', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Petty Cash Request </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="pettycash_l">
								<input name="permissions[]" id="pettycash_l" value="pettycash_l" type="checkbox" <?php echo isset($permissions['pettycash_l']) ? 'checked' : ''; ?>
									<?php echo (in_array('pettycash_l', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									>Petty Cash Log </label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="acc_v">
								<input name="permissions[]" id="acc_v" value="acc_v" type="checkbox" <?php echo isset($permissions['acc_v']) ? 'checked' : ''; ?>
									<?php echo (in_array('acc_v', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> View Account Title</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="acc_m">
								<input name="permissions[]" id="acc_m" value="acc_m" type="checkbox" <?php echo isset($permissions['acc_m']) ? 'checked' : ''; ?>
									<?php echo (in_array('acc_m', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Account Title</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="ship_v">
								<input name="permissions[]" id="ship_v" value="ship_v" type="checkbox" <?php echo isset($permissions['ship_v']) ? 'checked' : ''; ?>
									<?php echo (in_array('ship_v', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> View Shipping Company</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="ship_m">
								<input name="permissions[]" id="ship_m" value="ship_m" type="checkbox" <?php echo isset($permissions['ship_m']) ? 'checked' : ''; ?>
									<?php echo (in_array('ship_m', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Shipping Company</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="city_m">
								<input name="permissions[]" id="city_m" value="city_m" type="checkbox" <?php echo isset($permissions['city_m']) ? 'checked' : ''; ?>
									<?php echo (in_array('city_m', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Cities</label>
						</div>
					</div>
				</div>
				<div role="tabpanel" class="tab-pane" id="nav_users">
					<div class="col-md-12">
						<br>
						<div class="col-md-3">
							<label class="checkbox-inline" for="user">
								<input name="permissions[]" id="user" value="user" type="checkbox" <?php echo isset($permissions['user']) ? 'checked' : ''; ?>
									<?php echo (in_array('user', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> View Users </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="user_m">
								<input name="permissions[]" id="user_m" value="user_m" type="checkbox" <?php echo isset($permissions['user_m']) ? 'checked' : ''; ?>
									<?php echo (in_array('user_m', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Users </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="pw_reset">
								<input name="permissions[]" id="pw_reset" value="pw_reset" type="checkbox" <?php echo isset($permissions['pw_reset']) ? 'checked' : ''; ?>
									<?php echo (in_array('pw_reset', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Password Reset </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="position">
								<input name="permissions[]" id="position" value="position" type="checkbox" <?php echo isset($permissions['position']) ? 'checked' : ''; ?>
									<?php echo (in_array('position', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> View Position </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="position_m">
								<input name="permissions[]" id="position_m" value="position_m" type="checkbox" <?php echo isset($permissions['position_m']) ? 'checked' : ''; ?>
									<?php echo (in_array('position_m', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Position </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="is_franchisee">
								<input name="permissions[]" id="is_franchisee" value="is_franchisee" type="checkbox" <?php echo isset($permissions['is_franchisee']) ? 'checked' : ''; ?>
									<?php echo (in_array('is_franchisee', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Is Franchisee </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="department">
								<input name="permissions[]" id="department" value="department" type="checkbox" <?php echo isset($permissions['department']) ? 'checked' : ''; ?>
                    <?php echo (in_array('department', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
								> View Departments </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="department_m">
								<input name="permissions[]" id="department_m" value="department_m" type="checkbox" <?php echo isset($permissions['department_m']) ? 'checked' : ''; ?>
                    <?php echo (in_array('department_m', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
								> Manage Departments </label>
						</div>

					</div>
				</div>
				<div role="tabpanel" class="tab-pane" id="nav_inventory">
					<div class="col-md-12">
						<br>
						<div class="col-md-3">
							<label class="checkbox-inline" for="inventory">
								<input name="permissions[]" id="inventory" value="inventory" type="checkbox" <?php echo isset($permissions['inventory']) ? 'checked' : ''; ?>
									<?php echo (in_array('inventory', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> View Inventory </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="inventory_all">
								<input name="permissions[]" id="inventory_all" value="inventory_all" type="checkbox" <?php echo isset($permissions['inventory_all']) ? 'checked' : ''; ?>
									<?php echo (in_array('inventory_all', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> All Branch </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="inventory_add">
								<input name="permissions[]" id="inventory_add" value="inventory_add" type="checkbox" <?php echo isset($permissions['inventory_add']) ? 'checked' : ''; ?>
									<?php echo (in_array('inventory_add', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Add Inventory </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="inventory_app">
								<input name="permissions[]" id="inventory_app" value="inventory_app" type="checkbox" <?php echo isset($permissions['inventory_app']) ? 'checked' : ''; ?>
									<?php echo (in_array('inventory_app', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Approve Inventory </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="inventory_transfer">
								<input name="permissions[]" id="inventory_transfer" value="inventory_transfer" type="checkbox" <?php echo isset($permissions['inventory_transfer']) ? 'checked' : ''; ?>
									<?php echo (in_array('inventory_transfer', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Transfer Inventory </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="inventory_all_rack">
								<input name="permissions[]" id="inventory_all_rack" value="inventory_all_rack" type="checkbox" <?php echo isset($permissions['inventory_all_rack']) ? 'checked' : ''; ?>
									<?php echo (in_array('inventory_all_rack', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Receive from all racks </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="inventory_receive">
								<input name="permissions[]" id="inventory_receive" value="inventory_receive" type="checkbox" <?php echo isset($permissions['inventory_receive']) ? 'checked' : ''; ?>
									<?php echo (in_array('inventory_receive', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Receive Inventory </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="inventory_ref_number">
								<input name="permissions[]" id="inventory_ref_number" value="inventory_ref_number" type="checkbox" <?php echo isset($permissions['inventory_ref_number']) ? 'checked' : ''; ?>
									<?php echo (in_array('inventory_ref_number', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Update Ref Number </label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="serials">
								<input name="permissions[]" id="serials" value="serials" type="checkbox" <?php echo isset($permissions['serials']) ? 'checked' : ''; ?>
									<?php echo (in_array('serials', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Serials </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="serial_assembly">
								<input name="permissions[]" id="serial_assembly" value="serial_assembly" type="checkbox" <?php echo isset($permissions['serial_assembly']) ? 'checked' : ''; ?>
									<?php echo (in_array('serial_assembly', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Assembly Serial Only </label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="inv_forecast">
								<input name="permissions[]" id="inv_forecast" value="inv_forecast" type="checkbox" <?php echo isset($permissions['inv_forecast']) ? 'checked' : ''; ?>
									<?php echo (in_array('inv_forecast', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Forecast Inventory </label>
						</div>

					</div>

					<div class="col-md-12">
						<div class="col-md-3">
							<label class="checkbox-inline" for="inventory_adj">
								<input name="permissions[]" id="inventory_adj" value="inventory_adj" type="checkbox" <?php echo isset($permissions['inventory_adj']) ? 'checked' : ''; ?>
									<?php echo (in_array('inventory_adj', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Inventory Adjustment </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="bad_order">
								<input name="permissions[]" id="bad_order" value="bad_order" type="checkbox" <?php echo isset($permissions['bad_order']) ? 'checked' : ''; ?>
									<?php echo (in_array('bad_order', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									>Bad Order  </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="order_inv_m">
								<input name="permissions[]" id="order_inv_m" value="order_inv_m" type="checkbox" <?php echo isset($permissions['order_inv_m']) ? 'checked' : ''; ?>
									<?php echo (in_array('order_inv_m', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									>Order Inventory </label>
						</div>
						<?php if($thiscompany->plan_id == 2){ ?>
						<div class="col-md-3">
							<label class="checkbox-inline" for="pickup_inv">
								<input name="permissions[]" id="pickup_inv" value="pickup_inv" type="checkbox" <?php echo isset($permissions['pickup_inv']) ? 'checked' : ''; ?>
									<?php echo (in_array('pickup_inv', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									>Pickup Item </label>
						</div>
						<?php } ?>

						<div class="col-md-3">
							<label class="checkbox-inline" for="inv_mon">
								<input name="permissions[]" id="inv_mon" value="inv_mon" type="checkbox" <?php echo isset($permissions['inv_mon']) ? 'checked' : ''; ?>
									<?php echo (in_array('inv_mon', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									>Inventory Monitoring </label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="up_releasing">
								<input name="permissions[]" id="up_releasing" value="up_releasing" type="checkbox" <?php echo isset($permissions['up_releasing']) ? 'checked' : ''; ?>
									<?php echo (in_array('up_releasing', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Update releasing </label>
						</div>
					</div>
					<div class="col-md-12">

						<div class="col-md-3">
							<label class="checkbox-inline" for="rack">
								<input name="permissions[]" id="rack" value="rack" type="checkbox" <?php echo isset($permissions['rack']) ? 'checked' : ''; ?>
									<?php echo (in_array('rack', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> View Rack </label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="rack_m">
								<input name="permissions[]" id="rack_m" value="rack_m" type="checkbox" <?php echo isset($permissions['rack_m']) ? 'checked' : ''; ?>
									<?php echo (in_array('rack_m', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Rack </label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="rack_display">
								<input name="permissions[]" id="rack_display" value="rack_display" type="checkbox" <?php echo isset($permissions['rack_display']) ? 'checked' : ''; ?>
									<?php echo (in_array('rack_display', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Rack Display </label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="rack_other">
								<input name="permissions[]" id="rack_other" value="rack_other" type="checkbox" <?php echo isset($permissions['rack_other']) ? 'checked' : ''; ?>
									<?php echo (in_array('rack_other', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage WH Rack  </label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="in_out">
								<input name="permissions[]" id="in_out" value="in_out" type="checkbox" <?php echo isset($permissions['in_out']) ? 'checked' : ''; ?>
									<?php echo (in_array('in_out', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> In/Out Report  </label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="witness">
								<input name="permissions[]" id="witness" value="witness" type="checkbox" <?php echo isset($permissions['witness']) ? 'checked' : ''; ?>
									<?php echo (in_array('witness', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> View Witness </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="witness_m">
								<input name="permissions[]" id="witness_m" value="witness_m" type="checkbox" <?php echo isset($permissions['witness_m']) ? 'checked' : ''; ?>
									<?php echo (in_array('witness_m', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Witness </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="display_location_m">
								<input name="permissions[]" id="display_location_m" value="display_location_m" type="checkbox" <?php echo isset($permissions['display_location_m']) ? 'checked' : ''; ?>
									<?php echo (in_array('display_location_m', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									>Display Location </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="inventory_issues">
								<input name="permissions[]" id="inventory_issues" value="inventory_issues" type="checkbox" <?php echo isset($permissions['inventory_issues']) ? 'checked' : ''; ?>
									<?php echo (in_array('inventory_issues', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Inventory issues</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="manage_issues">
								<input name="permissions[]" id="manage_issues" value="manage_issues" type="checkbox" <?php echo isset($permissions['manage_issues']) ? 'checked' : ''; ?>
									<?php echo (in_array('manage_issues', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Issues</label>
						</div>
					</div>
					<?php if($thiscompany->plan_id == 2){ ?>
						<div class="col-md-12">
							<div class="col-md-3">
								<label class="checkbox-inline" for="orderpoint">
									<input name="permissions[]" id="orderpoint" value="orderpoint" type="checkbox" <?php echo isset($permissions['orderpoint']) ? 'checked' : ''; ?>
										<?php echo (in_array('orderpoint', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										> View Orderpoint </label>
							</div>
							<div class="col-md-3">
								<label class="checkbox-inline" for="orderpoint_m">
									<input name="permissions[]" id="orderpoint_m" value="orderpoint_m" type="checkbox" <?php echo isset($permissions['orderpoint_m']) ? 'checked' : ''; ?>
										<?php echo (in_array('orderpoint_m', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										> Manage Orderpoint </label>
							</div>
							<div class="col-md-3">
								<label class="checkbox-inline" for="orderpoint_p">
									<input name="permissions[]" id="orderpoint_p" value="orderpoint_p" type="checkbox" <?php echo isset($permissions['orderpoint_p']) ? 'checked' : ''; ?>
										<?php echo (in_array('orderpoint_p', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										>  Order/Critical Item </label>
							</div>

							<div class="col-md-3">
								<label class="checkbox-inline" for="order_item">
									<input name="permissions[]" id="order_item" value="order_item" type="checkbox" <?php echo isset($permissions['order_item']) ? 'checked' : ''; ?>
										<?php echo (in_array('order_item', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										>  Order Item </label>
							</div>
							<div class="col-md-3">
								<label class="checkbox-inline" for="item_swap">
									<input name="permissions[]" id="item_swap" value="item_swap" type="checkbox" <?php echo isset($permissions['item_swap']) ? 'checked' : ''; ?>
										<?php echo (in_array('item_swap', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										>Item Swapping</label>
							</div>
							<?php
								if(Configuration::allowedPermission('consume_supply')){
									?>
									<div class="col-md-3">
										<label class="checkbox-inline" for="req_sup">
											<input name="permissions[]" id="req_sup" value="req_sup" type="checkbox" <?php echo isset($permissions['req_sup']) ? 'checked' : ''; ?>
												<?php echo (in_array('req_sup', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
												>Request <?php echo SUPPLY_LABEL; ?></label>
									</div>
									<div class="col-md-3">
										<label class="checkbox-inline" for="app_sup">
											<input name="permissions[]" id="app_sup" value="app_sup" type="checkbox" <?php echo isset($permissions['app_sup']) ? 'checked' : ''; ?>
												<?php echo (in_array('app_sup', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
												>Approve <?php echo SUPPLY_LABEL; ?></label>
									</div>
									<div class="col-md-3">
										<label class="checkbox-inline" for="liq_sup">
											<input name="permissions[]" id="liq_sup" value="liq_sup" type="checkbox" <?php echo isset($permissions['liq_sup']) ? 'checked' : ''; ?>
												<?php echo (in_array('liq_sup', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
												>Liquidate <?php echo SUPPLY_LABEL; ?></label>
									</div>
									<div class="col-md-3">
										<label class="checkbox-inline" for="log_sup">
											<input name="permissions[]" id="log_sup" value="log_sup" type="checkbox" <?php echo isset($permissions['log_sup']) ? 'checked' : ''; ?>
												<?php echo (in_array('log_sup', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
												>Log <?php echo SUPPLY_LABEL; ?></label>
									</div>
									<?php
								}
							?>
						<?php
							if(Configuration::allowedPermission('equipment')){
						?>
						<div class="col-md-3">
							<label class="checkbox-inline" for="mem_equipment">
								<input name="permissions[]" id="mem_equipment" value="mem_equipment" type="checkbox" <?php echo isset($permissions['mem_equipment']) ? 'checked' : ''; ?>
									<?php echo (in_array('log_sup', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									>Member Borrowed Item</label>
						</div>
						<?php } // end equipment ?>
						</div>

						<div class="col-md-12">
						<hr>
						<div class="col-md-3">
							<label class="checkbox-inline" for="wh_agent">
								<input name="permissions[]" id="wh_agent" value="wh_agent" type="checkbox" <?php echo isset($permissions['wh_agent']) ? 'checked' : ''; ?>
									<?php echo (in_array('wh_agent', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									>Agent Privilege</label>
						</div>
							<div class="col-md-3">
								<label class="checkbox-inline" for="wh_member">
									<input name="permissions[]" id="wh_member" value="wh_member" type="checkbox" <?php echo isset($permissions['wh_member']) ? 'checked' : ''; ?>
										<?php echo (in_array('wh_member', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										>Member Privilege</label>
							</div>
							<div class="col-md-3">
								<label class="checkbox-inline" for="wh_all_member">
									<input name="permissions[]" id="wh_all_member" value="wh_all_member" type="checkbox" <?php echo isset($permissions['wh_all_member']) ? 'checked' : ''; ?>
										<?php echo (in_array('wh_all_member', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										>Order All Clients</label>
							</div>
							<div class="col-md-3">
								<label class="checkbox-inline" for="wh_order_all">
									<input name="permissions[]" id="wh_order_all" value="wh_order_all" type="checkbox" <?php echo isset($permissions['wh_order_all']) ? 'checked' : ''; ?>
										<?php echo (in_array('wh_order_all', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										>Show all order</label>
							</div>

							<div class="col-md-3">
								<label class="checkbox-inline" for="wh_reports">
									<input name="permissions[]" id="wh_reports" value="wh_reports" type="checkbox" <?php echo isset($permissions['wh_reports']) ? 'checked' : ''; ?>
										<?php echo (in_array('wh_reports', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										>Order Reports</label>
							</div>

							<div class="col-md-3">
								<label class="checkbox-inline" for="wh_order_item_summary">
									<input name="permissions[]" id="wh_order_item_summary" value="wh_order_item_summary" type="checkbox" <?php echo isset($permissions['wh_order_item_summary']) ? 'checked' : ''; ?>
										<?php echo (in_array('wh_order_item_summary', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										>Order Item Summary</label>
							</div>

						<div class="col-md-3">
								<label class="checkbox-inline" for="wh_request">
									<input name="permissions[]" id="wh_request" value="wh_request" type="checkbox" <?php echo isset($permissions['wh_request']) ? 'checked' : ''; ?>
										<?php echo (in_array('wh_request', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										>  Order Request</label>
							</div>
							<div class="col-md-3">
								<label class="checkbox-inline" for="wh_approval">
									<input name="permissions[]" id="wh_approval" value="wh_approval" type="checkbox" <?php echo isset($permissions['wh_approval']) ? 'checked' : ''; ?>
										<?php echo (in_array('wh_approval', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										>  View Order For Approval</label>
							</div>

							<div class="col-md-3">
								<label class="checkbox-inline" for="wh_warehouse">
									<input name="permissions[]" id="wh_warehouse" value="wh_warehouse" type="checkbox" <?php echo isset($permissions['wh_warehouse']) ? 'checked' : ''; ?>
										<?php echo (in_array('wh_warehouse', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										>View Warehouse</label>
							</div>
							<div class="col-md-3">
								<label class="checkbox-inline" for="wh_shipping">
									<input name="permissions[]" id="wh_shipping" value="wh_shipping" type="checkbox" <?php echo isset($permissions['wh_shipping']) ? 'checked' : ''; ?>
										<?php echo (in_array('wh_shipping', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										>View Shipping</label>
							</div>
							<div class="col-md-3">
								<label class="checkbox-inline" for="wh_log">
									<input name="permissions[]" id="wh_log" value="wh_log" type="checkbox" <?php echo isset($permissions['wh_log']) ? 'checked' : ''; ?>
										<?php echo (in_array('wh_log', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										>View Order Log</label>
							</div>
							<div class="col-md-3">
								<label class="checkbox-inline" for="wh_approval_v">
									<input name="permissions[]" id="wh_approval_v" value="wh_approval_v" type="checkbox" <?php echo isset($permissions['wh_approval_v']) ? 'checked' : ''; ?>
										<?php echo (in_array('wh_approval_v', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										>Approved Orders</label>
							</div>

							<div class="col-md-3">
								<label class="checkbox-inline" for="wh_approval_p">
									<input name="permissions[]" id="wh_approval_p" value="wh_approval_p" type="checkbox" <?php echo isset($permissions['wh_approval_p']) ? 'checked' : ''; ?>
										<?php echo (in_array('wh_approval_p', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										>Process Request Warehouse</label>
							</div>
							<div class="col-md-3">
								<label class="checkbox-inline" for="wh_approval_s">
									<input name="permissions[]" id="wh_approval_s" value="wh_approval_s" type="checkbox" <?php echo isset($permissions['wh_approval_s']) ? 'checked' : ''; ?>
										<?php echo (in_array('wh_approval_s', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										>Get Stock Warehouse</label>
							</div>
							<div class="col-md-3">
								<label class="checkbox-inline" for="wh_payment">
									<input name="permissions[]" id="wh_payment" value="wh_payment" type="checkbox" <?php echo isset($permissions['wh_payment']) ? 'checked' : ''; ?>
										<?php echo (in_array('wh_payment', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										>Add payment</label>
							</div>
							<div class="col-md-3">
								<label class="checkbox-inline" for="wh_invdr">
									<input name="permissions[]" id="wh_invdr" value="wh_invdr" type="checkbox" <?php echo isset($permissions['wh_invdr']) ? 'checked' : ''; ?>
										<?php echo (in_array('wh_invdr', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										>Invoice/Dr Issue</label>
							</div>
							<div class="col-md-3">
								<label class="checkbox-inline" for="wh_schedule">
									<input name="permissions[]" id="wh_schedule" value="wh_schedule" type="checkbox" <?php echo isset($permissions['wh_schedule']) ? 'checked' : ''; ?>
										<?php echo (in_array('wh_schedule', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										>Set Schedule</label>
							</div>
							<div class="col-md-3">
								<label class="checkbox-inline" for="truck">
									<input name="permissions[]" id="truck" value="truck" type="checkbox" <?php echo isset($permissions['truck']) ? 'checked' : ''; ?>
										<?php echo (in_array('truck', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										>Manage Trucks</label>
							</div>
							<div class="col-md-3">
								<label class="checkbox-inline" for="del_helper">
									<input name="permissions[]" id="del_helper" value="del_helper" type="checkbox" <?php echo isset($permissions['del_helper']) ? 'checked' : ''; ?>
										<?php echo (in_array('del_helper', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										>Manage Delivery Helper</label>
							</div>
							<div class="col-md-3">
								<label class="checkbox-inline" for="inv_rep">
									<input name="permissions[]" id="inv_rep" value="inv_rep" type="checkbox" <?php echo isset($permissions['inv_rep']) ? 'checked' : ''; ?>
										<?php echo (in_array('inv_rep', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										>Warehouse Report</label>
							</div>
							<div class="col-md-3">
								<label class="checkbox-inline" for="wh_update_details">
									<input name="permissions[]" id="wh_update_details" value="wh_update_details" type="checkbox" <?php echo isset($permissions['wh_update_details']) ? 'checked' : ''; ?>
										<?php echo (in_array('wh_update_details', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										>Update order details</label>
							</div>
							<div class="col-md-3">
								<label class="checkbox-inline" for="wh_item_details">
									<input name="permissions[]" id="wh_item_details" value="wh_item_details" type="checkbox" <?php echo isset($permissions['wh_item_details']) ? 'checked' : ''; ?>
										<?php echo (in_array('wh_item_details', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										>Update Item details</label>
							</div>
							<div class="col-md-3">
								<label class="checkbox-inline" for="wh_res">
									<input name="permissions[]" id="wh_res" value="wh_res" type="checkbox" <?php echo isset($permissions['wh_res']) ? 'checked' : ''; ?>
										<?php echo (in_array('wh_res', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										>Request For Reservation</label>
							</div>
							<div class="col-md-3">
								<label class="checkbox-inline" for="wh_app_walkin">
									<input name="permissions[]" id="wh_app_walkin" value="wh_app_walkin" type="checkbox" <?php echo isset($permissions['wh_app_walkin']) ? 'checked' : ''; ?>
										<?php echo (in_array('wh_app_walkin', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										>Approve Walk In Order</label>
							</div>
							<div class="col-md-3">
								<label class="checkbox-inline" for="c_helper">
									<input name="permissions[]" id="c_helper" value="c_helper" type="checkbox" <?php echo isset($permissions['c_helper']) ? 'checked' : ''; ?>
										<?php echo (in_array('c_helper', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										>Reserve Item Order</label>
							</div>
							<div class="col-md-3">
								<label class="checkbox-inline" for="wh_remove_ship">
									<input name="permissions[]" id="wh_remove_ship" value="wh_remove_ship" type="checkbox" <?php echo isset($permissions['wh_remove_ship']) ? 'checked' : ''; ?>
										<?php echo (in_array('wh_remove_ship', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										>Remove Ship Items</label>
							</div>
						</div>
					<?php } ?>

				</div>
			<?php if($thiscompany->plan_id == 2){ ?>
				<div role="tabpanel" class="tab-pane" id="nav_supplier">
					<div class="col-md-12">
						<br>
						<div class="col-md-3">
							<label class="checkbox-inline" for="supplier">
								<input name="permissions[]" id="supplier" value="supplier" type="checkbox" <?php echo isset($permissions['supplier']) ? 'checked' : ''; ?>
									<?php echo (in_array('supplier', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> View Supplier </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="supplier_m">
								<input name="permissions[]" id="supplier_m" value="supplier_m" type="checkbox" <?php echo isset($permissions['supplier_m']) ? 'checked' : ''; ?>
									<?php echo (in_array('supplier_m', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Supplier </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="supplier_o">
								<input name="permissions[]" id="supplier_o" value="supplier_o" type="checkbox" <?php echo isset($permissions['supplier_o']) ? 'checked' : ''; ?>
									<?php echo (in_array('supplier_o', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Supplier Order Item  </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="supplier_ol">
								<input name="permissions[]" id="supplier_ol" value="supplier_ol" type="checkbox" <?php echo isset($permissions['supplier_ol']) ? 'checked' : ''; ?>
									<?php echo (in_array('supplier_ol', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Supplier Order List </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="supplier_si">
								<input name="permissions[]" id="supplier_si" value="supplier_si" type="checkbox" <?php echo isset($permissions['supplier_si']) ? 'checked' : ''; ?>
									<?php echo (in_array('supplier_si', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									>View Supplier Item</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="supplier_sim">
								<input name="permissions[]" id="supplier_sim" value="supplier_sim" type="checkbox" <?php echo isset($permissions['supplier_sim']) ? 'checked' : ''; ?>
									<?php echo (in_array('supplier_si', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Supplier Item</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="supplier_order_app">
								<input name="permissions[]" id="supplier_order_app" value="supplier_order_app" type="checkbox" <?php echo isset($permissions['supplier_order_app']) ? 'checked' : ''; ?>
									<?php echo (in_array('supplier_order_app', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									>Approve supplier order</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="supplier_order_rec">
								<input name="permissions[]" id="supplier_order_rec" value="supplier_order_rec" type="checkbox" <?php echo isset($permissions['supplier_order_rec']) ? 'checked' : ''; ?>
									<?php echo (in_array('supplier_order_rec', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									>Receive supplier order</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="supplier_item_price_show">
								<input name="permissions[]" id="supplier_item_price_show" value="supplier_item_price_show" type="checkbox" <?php echo isset($permissions['supplier_item_price_show']) ? 'checked' : ''; ?>
									<?php echo (in_array('supplier_item_price_show', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									>Supplier item show price</label>
						</div>
					</div>
				</div>
			<?php } ?>
				<div role="tabpanel" class="tab-pane" id="nav_item">
					<div class="col-md-12">
						<br>
						<div class="col-md-3">
							<label class="checkbox-inline" for="item">
								<input name="permissions[]" id="item" value="item" type="checkbox" <?php echo isset($permissions['item']) ? 'checked' : ''; ?>
									<?php echo (in_array('item', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> View Item </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="item_m">
								<input name="permissions[]" id="item_m" value="item_m" type="checkbox" <?php echo isset($permissions['item_m']) ? 'checked' : ''; ?>
									<?php echo (in_array('item_m', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Item </label>
						</div>
<?php
	if(Configuration::allowedPermission('item_post')){
		?>
						<div class="col-md-3">
							<label class="checkbox-inline" for="item_post">
								<input name="permissions[]" id="item_post" value="item_post" type="checkbox" <?php echo isset($permissions['item_post']) ? 'checked' : ''; ?>
									<?php echo (in_array('item_post', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Post Item </label>
						</div>

						<?php } ?>
						<div class="col-md-3">

							<label class="checkbox-inline" for="category">
								<input name="permissions[]" id="category" value="category" type="checkbox" <?php echo isset($permissions['category']) ? 'checked' : ''; ?>
									<?php echo (in_array('category', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> View Category </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="category_m">
								<input name="permissions[]" id="category_m" value="category_m" type="checkbox" <?php echo isset($permissions['category_m']) ? 'checked' : ''; ?>
									<?php echo (in_array('category_m', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Category </label>
						</div>
					</div>
					<div class="col-md-12">

						<div class="col-md-3">
							<label class="checkbox-inline" for="characteristics">
								<input name="permissions[]" id="characteristics" value="characteristics" type="checkbox" <?php echo isset($permissions['characteristics']) ? 'checked' : ''; ?>
									<?php echo (in_array('characteristics', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> View Characteristics </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="characteristics_m">
								<input name="permissions[]" id="characteristics_m" value="characteristics_m" type="checkbox" <?php echo isset($permissions['characteristics_m']) ? 'checked' : ''; ?>
									<?php echo (in_array('characteristics_m', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Characteristics </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="unit">
								<input name="permissions[]" id="unit" value="unit" type="checkbox" <?php echo isset($permissions['unit']) ? 'checked' : ''; ?>
									<?php echo (in_array('unit', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> View Unit </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="unit_m">
								<input name="permissions[]" id="unit_m" value="unit_m" type="checkbox" <?php echo isset($permissions['unit_m']) ? 'checked' : ''; ?>
									<?php echo (in_array('unit_m', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Unit </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="barcode_m">
								<input name="permissions[]" id="barcode_m" value="barcode_m" type="checkbox" <?php echo isset($permissions['barcode_m']) ? 'checked' : ''; ?>
									<?php echo (in_array('barcode_m', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									>Manage Barcode </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="barcode_p">
								<input name="permissions[]" id="barcode_p" value="barcode_p" type="checkbox" <?php echo isset($permissions['barcode_p']) ? 'checked' : ''; ?>
									<?php echo (in_array('barcode_p', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Print Barcode </label>
						</div>
					<?php if($thiscompany->plan_id == 2){ ?>
						<div class="col-md-3">
							<label class="checkbox-inline" for="alert">
								<input name="permissions[]" id="alert" value="alert" type="checkbox" <?php echo isset($permissions['alert']) ? 'checked' : ''; ?>
									<?php echo (in_array('alert', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> View Item Alert </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="alert_m">
								<input name="permissions[]" id="alert_m" value="alert_m" type="checkbox" <?php echo isset($permissions['alert_m']) ? 'checked' : ''; ?>
									<?php echo (in_array('alert_m', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Item Alert </label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="notification">
								<input name="permissions[]" id="notification" value="notification" type="checkbox" <?php echo isset($permissions['notification']) ? 'checked' : ''; ?>
									<?php echo (in_array('notification', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> See Notification </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="notification_rm">
								<input name="permissions[]" id="notification_rm" value="notification_rm" type="checkbox" <?php echo isset($permissions['notification_rm']) ? 'checked' : ''; ?>
									<?php echo (in_array('notification_rm', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Add Notification Remarks </label>
						</div>
					<?php } ?>
						<div class="col-md-3">
							<label class="checkbox-inline" for="queue">
								<input name="permissions[]" id="queue" value="queue" type="checkbox" <?php echo isset($permissions['queue']) ? 'checked' : ''; ?>
									<?php echo (in_array('queue', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> View Queue </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="queue_m">
								<input name="permissions[]" id="queue_m" value="queue_m" type="checkbox" <?php echo isset($permissions['queue_m']) ? 'checked' : ''; ?>
									<?php echo (in_array('queue_m', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Queue </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="item_adj">
								<input name="permissions[]" id="item_adj" value="item_adj" type="checkbox" <?php echo isset($permissions['item_adj']) ? 'checked' : ''; ?>
									<?php echo (in_array('item_adj', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> View Item Adjustment </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="item_adj_m">
								<input name="permissions[]" id="item_adj_m" value="item_adj_m" type="checkbox" <?php echo isset($permissions['item_adj_m']) ? 'checked' : ''; ?>
									<?php echo (in_array('item_adj_m', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Item Adjustment </label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="pbrand">
								<input name="permissions[]" id="pbrand" value="pbrand" type="checkbox" <?php echo isset($permissions['pbrand']) ? 'checked' : ''; ?>
									<?php echo (in_array('pbrand', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Item Brand</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="bundles">
								<input name="permissions[]" id="bundles" value="bundles" type="checkbox" <?php echo isset($permissions['bundles']) ? 'checked' : ''; ?>
									<?php echo (in_array('bundles', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> View Bundles </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="bundles_m">
								<input name="permissions[]" id="bundles_m" value="bundles_m" type="checkbox" <?php echo isset($permissions['bundles_m']) ? 'checked' : ''; ?>
									<?php echo (in_array('bundles_m', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Bundles </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="branch_group">
								<input name="permissions[]" id="branch_group" value="branch_group" type="checkbox" <?php echo isset($permissions['branch_group']) ? 'checked' : ''; ?>
									<?php echo (in_array('branch_group', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Branch Group </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="price_group">
								<input name="permissions[]" id="price_group" value="price_group" type="checkbox" <?php echo isset($permissions['price_group']) ? 'checked' : ''; ?>
									<?php echo (in_array('price_group', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Price Group </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="price_group_flex">
								<input name="permissions[]" id="price_group_flex" value="price_group_flex" type="checkbox" <?php echo isset($permissions['price_group_flex']) ? 'checked' : ''; ?>
									<?php echo (in_array('price_group_flex', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Flexible Price Group </label>
						</div>
						<?php if(Configuration::getValue('group_adjustment_optional')){
							?>
							<div class="col-md-3">
								<label class="checkbox-inline" for="group_adjustment">
									<input name="permissions[]" id="group_adjustment" value="group_adjustment" type="checkbox" <?php echo isset($permissions['group_adjustment']) ? 'checked' : ''; ?>
										<?php echo (in_array('group_adjustment', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										> Optional Group Adjustment </label>
							</div>
							<?php
						} ?>

						<div class="col-md-3">
							<label class="checkbox-inline" for="freebie">
								<input name="permissions[]" id="freebie" value="freebie" type="checkbox" <?php echo isset($permissions['freebie']) ? 'checked' : ''; ?>
									<?php echo (in_array('freebie', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Item Freebie </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="quotation">
								<input name="permissions[]" id="quotation" value="quotation" type="checkbox" <?php echo isset($permissions['quotation']) ? 'checked' : ''; ?>
									<?php echo (in_array('quotation', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Add Quotation </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="quotation_m">
								<input name="permissions[]" id="quotation_m" value="quotation_m" type="checkbox" <?php echo isset($permissions['quotation_m']) ? 'checked' : ''; ?>
									<?php echo (in_array('quotation_m', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Quotation </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="item_commission">
								<input name="permissions[]" id="item_commission" value="item_commission" type="checkbox" <?php echo isset($permissions['item_commission']) ? 'checked' : ''; ?>
									<?php echo (in_array('item_commission', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Item commission </label>
						</div>
					</div>

					<div class="col-md-12">
						<hr>
						<div class="col-md-3">
							<label class="checkbox-inline" for="spare_part">
								<input name="permissions[]" id="spare_part" value="spare_part" type="checkbox" <?php echo isset($permissions['spare_part']) ? 'checked' : ''; ?>
									<?php echo (in_array('spare_part', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									>  Spare part</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="spare_part_add">
								<input name="permissions[]" id="spare_part_add" value="spare_part_add" type="checkbox" <?php echo isset($permissions['spare_part_add']) ? 'checked' : ''; ?>
									<?php echo (in_array('spare_part_add', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									>  Spare part Add/Update</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="spare_part_sim">
								<input name="permissions[]" id="spare_part_sim" value="spare_part_sim" type="checkbox" <?php echo isset($permissions['spare_part_sim']) ? 'checked' : ''; ?>
									<?php echo (in_array('spare_part_sim', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									>  Similar spare part</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="spare_part_a">
								<input name="permissions[]" id="spare_part_a" value="spare_part_a" type="checkbox" <?php echo isset($permissions['spare_part_a']) ? 'checked' : ''; ?>
									<?php echo (in_array('spare_part_a', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									>  Assemble Spare Part</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="assemble_items">
								<input name="permissions[]" id="assemble_items" value="assemble_items" type="checkbox" <?php echo isset($permissions['assemble_items']) ? 'checked' : ''; ?>
									<?php echo (in_array('assemble_items', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									>  Assemble History</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="spare_part_d">
								<input name="permissions[]" id="spare_part_d" value="spare_part_d" type="checkbox" <?php echo isset($permissions['spare_part_d']) ? 'checked' : ''; ?>
									<?php echo (in_array('spare_part_d', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Disassemble Spare Part</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="borrow_part">
								<input name="permissions[]" id="borrow_part" value="borrow_part" type="checkbox" <?php echo isset($permissions['borrow_part']) ? 'checked' : ''; ?>
									<?php echo (in_array('borrow_part', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Borrow Parts</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="sp_forecast">
								<input name="permissions[]" id="sp_forecast" value="sp_forecast" type="checkbox" <?php echo isset($permissions['sp_forecast']) ? 'checked' : ''; ?>
									<?php echo (in_array('spare_part', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									>  Forecast Spare part</label>
						</div>
					</div>
					<div class="col-md-12">
						<hr>
						<div class="col-md-3">
							<label class="checkbox-inline" for="item_service_r">
								<input name="permissions[]" id="item_service_r" value="item_service_r" type="checkbox" <?php echo isset($permissions['item_service_r']) ? 'checked' : ''; ?>
									<?php echo (in_array('item_service_r', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Item Service Request </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="item_service_t">
								<input name="permissions[]" id="item_service_t" value="item_service_t" type="checkbox" <?php echo isset($permissions['item_service_t']) ? 'checked' : ''; ?>
									<?php echo (in_array('item_service_t', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Technician </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="item_service_pr">
								<input name="permissions[]" id="item_service_pr" value="item_service_pr" type="checkbox" <?php echo isset($permissions['item_service_pr']) ? 'checked' : ''; ?>
									<?php echo (in_array('item_service_pr', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Process Service </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="item_service_s">
								<input name="permissions[]" id="item_service_s" value="item_service_s" type="checkbox" <?php echo isset($permissions['item_service_s']) ? 'checked' : ''; ?>
									<?php echo (in_array('item_service_s', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Service Request Item </label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="item_service_rem">
								<input name="permissions[]" id="item_service_rem" value="item_service_rem" type="checkbox" <?php echo isset($permissions['item_service_rem']) ? 'checked' : ''; ?>
									<?php echo (in_array('item_service_rem', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Service Add Remarks </label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="item_service_ap">
								<input name="permissions[]" id="item_service_ap" value="item_service_ap" type="checkbox" <?php echo isset($permissions['item_service_ap']) ? 'checked' : ''; ?>
									<?php echo (in_array('item_service_ap', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Service Release Item </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="p_credit_memo">
								<input name="permissions[]" id="p_credit_memo" value="p_credit_memo" type="checkbox" <?php echo isset($permissions['p_credit_memo']) ? 'checked' : ''; ?>
									<?php echo (in_array('p_credit_memo', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Print Credit Memo </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="item_service_con">
								<input name="permissions[]" id="item_service_con" value="item_service_con" type="checkbox" <?php echo isset($permissions['item_service_con']) ? 'checked' : ''; ?>
									<?php echo (in_array('item_service_con', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Consolidated Info </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="item_service_p">
								<input name="permissions[]" id="item_service_p" value="item_service_p" type="checkbox" <?php echo isset($permissions['item_service_p']) ? 'checked' : ''; ?>
									<?php echo (in_array('item_service_p', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Item Service Payment </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="item_service_l">
								<input name="permissions[]" id="item_service_l" value="item_service_l" type="checkbox" <?php echo isset($permissions['item_service_l']) ? 'checked' : ''; ?>
									<?php echo (in_array('item_service_l', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Item Service Log </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="item_service_overwrite">
								<input name="permissions[]" id="item_service_overwrite" value="item_service_overwrite" type="checkbox" <?php echo isset($permissions['item_service_overwrite']) ? 'checked' : ''; ?>
									<?php echo (in_array('item_service_overwrite', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Item Service Overwrite Price </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="measure">
								<input name="permissions[]" id="measure" value="measure" type="checkbox" <?php echo isset($permissions['measure']) ? 'checked' : ''; ?>
									<?php echo (in_array('measure', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Measurement </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="service_sales">
								<input name="permissions[]" id="service_sales" value="service_sales" type="checkbox" <?php echo isset($permissions['service_sales']) ? 'checked' : ''; ?>
									<?php echo (in_array('service_sales', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Service Sales </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="assembly_sales">
								<input name="permissions[]" id="assembly_sales" value="assembly_sales" type="checkbox" <?php echo isset($permissions['assembly_sales']) ? 'checked' : ''; ?>
									<?php echo (in_array('assembly_sales', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Assembly Sales </label>
						</div>
						<?php
							if(Configuration::isAquabest()){
								?>
								<div class="col-md-3">
									<label class="checkbox-inline" for="service_step_1">
										<input name="permissions[]" id="service_step_1" value="service_step_1" type="checkbox" <?php echo isset($permissions['service_step_1']) ? 'checked' : ''; ?>
											<?php echo (in_array('service_step_1', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
											> Service Validation Schedule </label>
								</div>
								<div class="col-md-3">
									<label class="checkbox-inline" for="service_step_2">
										<input name="permissions[]" id="service_step_2" value="service_step_2" type="checkbox" <?php echo isset($permissions['service_step_2']) ? 'checked' : ''; ?>
											<?php echo (in_array('service_step_2', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
											> SO Creation and Dispatching </label>
								</div>
								<div class="col-md-3">
									<label class="checkbox-inline" for="service_step_3">
										<input name="permissions[]" id="service_step_3" value="service_step_3" type="checkbox" <?php echo isset($permissions['service_step_3']) ? 'checked' : ''; ?>
											<?php echo (in_array('service_step_3', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
											> For Reporting </label>
								</div>
								<div class="col-md-3">
									<label class="checkbox-inline" for="service_step_4">
										<input name="permissions[]" id="service_step_4" value="service_step_4" type="checkbox" <?php echo isset($permissions['service_step_4']) ? 'checked' : ''; ?>
											<?php echo (in_array('service_step_4', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
											> CCD Verification </label>
								</div>
								<?php
							}
						?>
						<?php if(Configuration::thisCompany('cebuhiq')) { ?>
							<div class="col-md-3">
								<label class="checkbox-inline" for="print_scs">
									<input name="permissions[]" id="print_scs" value="print_scs" type="checkbox" <?php echo isset($permissions['print_scs']) ? 'checked' : ''; ?>
										<?php echo (in_array('print_scs', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										> Print SCS</label>
							</div>
							<div class="col-md-3">
								<label class="checkbox-inline" for="print_sar">
									<input name="permissions[]" id="print_sar" value="print_sar" type="checkbox" <?php echo isset($permissions['print_sar']) ? 'checked' : ''; ?>
										<?php echo (in_array('print_sar', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										> Print SAR</label>
							</div>
						<?php } ?>
						<div class="col-md-3">
							<label class="checkbox-inline" for="ass_branch">
								<input name="permissions[]" id="ass_branch" value="ass_branch" type="checkbox" <?php echo isset($permissions['ass_branch']) ? 'checked' : ''; ?>
									<?php echo (in_array('ass_branch', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Assign Branch </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="ass_tech">
								<input name="permissions[]" id="ass_tech" value="ass_tech" type="checkbox" <?php echo isset($permissions['ass_tech']) ? 'checked' : ''; ?>
									<?php echo (in_array('ass_tech', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Assign Technician </label>
						</div>

					</div>
				</div>
				<div role="tabpanel" class="tab-pane" id="nav_member">
					<div class="col-md-12">
						<br>
						<div class="col-md-3">
							<label class="checkbox-inline" for="member">
								<input name="permissions[]" id="member" value="member" type="checkbox" <?php echo isset($permissions['member']) ? 'checked' : ''; ?>
									<?php echo (in_array('member', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> View Member </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="member_m">
								<input name="permissions[]" id="member_m" value="member_m" type="checkbox" <?php echo isset($permissions['member_m']) ? 'checked' : ''; ?>
									<?php echo (in_array('member_m', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Member </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="affiliate">
								<input name="permissions[]" id="affiliate" value="affiliate" type="checkbox" <?php echo isset($permissions['affiliate']) ? 'checked' : ''; ?>
									<?php echo (in_array('affiliate', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Affiliate </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="m_char">
								<input name="permissions[]" id="m_char" value="m_char" type="checkbox" <?php echo isset($permissions['m_char']) ? 'checked' : ''; ?>
									<?php echo (in_array('m_char', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> View Member Characteristics </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="m_char_m">
								<input name="permissions[]" id="m_char_m" value="m_char_m" type="checkbox" <?php echo isset($permissions['m_char_m']) ? 'checked' : ''; ?>
									<?php echo (in_array('m_char_m', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Member Characteristics </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="m_terms">
								<input name="permissions[]" id="m_terms" value="m_terms" type="checkbox" <?php echo isset($permissions['m_terms']) ? 'checked' : ''; ?>
									<?php echo (in_array('m_terms', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Member Terms</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="pr_adj_categ">
								<input name="permissions[]" id="pr_adj_categ" value="pr_adj_categ" type="checkbox" <?php echo isset($permissions['pr_adj_categ']) ? 'checked' : ''; ?>
									<?php echo (in_array('pr_adj_categ', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Price Adjustment By Categ</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="m_terms_request">
								<input name="permissions[]" id="m_terms_request" value="m_terms_request" type="checkbox" <?php echo isset($permissions['m_terms_request']) ? 'checked' : ''; ?>
									<?php echo (in_array('m_terms_request', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Member Terms Request</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="e_bills_request">
								<input name="permissions[]" id="e_bills_request" value="e_bills_request" type="checkbox" <?php echo isset($permissions['e_bills_request']) ? 'checked' : ''; ?>
									<?php echo (in_array('e_bills_request', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> E-Bills Request </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="tblast">
								<input name="permissions[]" id="tblast" value="tblast" type="checkbox" <?php echo isset($permissions['tblast']) ? 'checked' : ''; ?>
									<?php echo (in_array('tblast', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Text Blast</label>
						</div>
					</div>

					<div class="col-md-12">
						<div class="col-md-3">
							<label class="checkbox-inline" for="station">
								<input name="permissions[]" id="station" value="station" type="checkbox" <?php echo isset($permissions['station']) ? 'checked' : ''; ?>
									<?php echo (in_array('station', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> View Station </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="station_m">
								<input name="permissions[]" id="station_m" value="station_m" type="checkbox" <?php echo isset($permissions['station_m']) ? 'checked' : ''; ?>
									<?php echo (in_array('station_m', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Station </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="subscription">
								<input name="permissions[]" id="subscription" value="subscription" type="checkbox" <?php echo isset($permissions['subscription']) ? 'checked' : ''; ?>
									<?php echo (in_array('subscription', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Subscription </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="package">
								<input name="permissions[]" id="package" value="package" type="checkbox" <?php echo isset($permissions['package']) ? 'checked' : ''; ?>
									<?php echo (in_array('package', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Package </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="brand">
								<input name="permissions[]" id="brand" value="brand" type="checkbox" <?php echo isset($permissions['brand']) ? 'checked' : ''; ?>
									<?php echo (in_array('brand', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Brand </label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="m_dues">
								<input name="permissions[]" id="m_dues" value="m_dues" type="checkbox" <?php echo isset($permissions['m_dues']) ? 'checked' : ''; ?>
									<?php echo (in_array('m_dues', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> View Member Dues</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="a_dues">
								<input name="permissions[]" id="a_dues" value="a_dues" type="checkbox" <?php echo isset($permissions['a_dues']) ? 'checked' : ''; ?>
									<?php echo (in_array('a_dues', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Add Member Dues</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="u_dues">
								<input name="permissions[]" id="u_dues" value="u_dues" type="checkbox" <?php echo isset($permissions['u_dues']) ? 'checked' : ''; ?>
									<?php echo (in_array('m_dues', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Member Dues</label>
						</div>

						<?php if(Configuration::isGym()){
							?>
							<div class="col-md-3">
								<label class="checkbox-inline" for="body_measure">
									<input name="permissions[]" id="body_measure" value="body_measure" type="checkbox" <?php echo isset($permissions['body_measure']) ? 'checked' : ''; ?>
										<?php echo (in_array('brand', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										> Body Measurement </label>
							</div>
	<?php
						} ?>
					</div>
			    </div>
				<div role="tabpanel" class="tab-pane" id="nav_sales">
					<div class="col-md-12">
						<br>
						<div class="col-md-3">
							<label class="checkbox-inline" for="sales">
								<input name="permissions[]" id="sales" value="sales" type="checkbox" <?php echo isset($permissions['sales']) ? 'checked' : ''; ?>
									<?php echo (in_array('sales', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Sales </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="order">
								<input name="permissions[]" id="order" value="order" type="checkbox" <?php echo isset($permissions['order']) ? 'checked' : ''; ?>
									<?php echo (in_array('order', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Reservation </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="createorder">
								<input name="permissions[]" id="createorder" value="createorder" type="checkbox" <?php echo isset($permissions['createorder']) ? 'checked' : ''; ?>
									<?php echo (in_array('createorder', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Create Reservation </label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="dr_layout">
								<input name="permissions[]" id="dr_layout" value="dr_layout" type="checkbox" <?php echo isset($permissions['dr_layout']) ? 'checked' : ''; ?>
									<?php echo (in_array('dr_layout', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Dr Layout</label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="pr_layout">
								<input name="permissions[]" id="pr_layout" value="pr_layout" type="checkbox" <?php echo isset($permissions['pr_layout']) ? 'checked' : ''; ?>
									<?php echo (in_array('pr_layout', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> PR Layout</label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="sv_layout">
								<input name="permissions[]" id="sv_layout" value="sv_layout" type="checkbox" <?php echo isset($permissions['sv_layout']) ? 'checked' : ''; ?>
									<?php echo (in_array('sv_layout', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> SV Layout</label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="invoice_layout">
								<input name="permissions[]" id="invoice_layout" value="invoice_layout" type="checkbox" <?php echo isset($permissions['invoice_layout']) ? 'checked' : ''; ?>
									<?php echo (in_array('invoice_layout', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Invoice Layout</label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="sales_type">
								<input name="permissions[]" id="sales_type" value="sales_type" type="checkbox" <?php echo isset($permissions['sales_type']) ? 'checked' : ''; ?>
									<?php echo (in_array('sales_type', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> View Sales Type</label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="sales_type_m">
								<input name="permissions[]" id="sales_type_m" value="sales_type_m" type="checkbox" <?php echo isset($permissions['sales_type_m']) ? 'checked' : ''; ?>
									<?php echo (in_array('sales_type_m', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Sales Type</label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="cheque_monitoring">
								<input name="permissions[]" id="cheque_monitoring" value="cheque_monitoring" type="checkbox" <?php echo isset($permissions['cheque_monitoring']) ? 'checked' : ''; ?>
									<?php echo (in_array('cheque_monitoring', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Cheque Monitoring</label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="refund">
								<input name="permissions[]" id="refund" value="refund" type="checkbox" <?php echo isset($permissions['refund']) ? 'checked' : ''; ?>
									<?php echo (in_array('refund', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Refund Monitoring</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="credit_monitoring">
								<input name="permissions[]" id="credit_monitoring" value="credit_monitoring" type="checkbox" <?php echo isset($permissions['credit_monitoring']) ? 'checked' : ''; ?>
									<?php echo (in_array('credit_monitoring', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Member Credit Monitoring</label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="credit_all">
								<input name="permissions[]" id="credit_all" value="credit_all" type="checkbox" <?php echo isset($permissions['credit_all']) ? 'checked' : ''; ?>
									<?php echo (in_array('credit_all', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> All Member Credit </label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="member_credit_payment">
								<input name="permissions[]" id="member_credit_payment" value="member_credit_payment" type="checkbox" <?php echo isset($permissions['member_credit_payment']) ? 'checked' : ''; ?>
									<?php echo (in_array('member_credit_payment', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									>Accept Payment for Member Credit </label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="doc_util">
								<input name="permissions[]" id="doc_util" value="doc_util" type="checkbox" <?php echo isset($permissions['doc_util']) ? 'checked' : ''; ?>
									<?php echo (in_array('doc_util', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Doc Utilities</label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="lock_doc_util">
								<input name="permissions[]" id="lock_doc_util" value="lock_doc_util" type="checkbox" <?php echo isset($permissions['lock_doc_util']) ? 'checked' : ''; ?>
									<?php echo (in_array('lock_doc_util', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Lock Doc Utilities</label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="discount">
								<input name="permissions[]" id="discount" value="discount" type="checkbox" <?php echo isset($permissions['discount']) ? 'checked' : ''; ?>
									<?php echo (in_array('discount', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> View Discount</label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="discount_m">
								<input name="permissions[]" id="discount_m" value="discount_m" type="checkbox" <?php echo isset($permissions['discount_m']) ? 'checked' : ''; ?>
									<?php echo (in_array('discount_m', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Discount</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="ar">
								<input name="permissions[]" id="ar" value="ar" type="checkbox" <?php echo isset($permissions['ar']) ? 'checked' : ''; ?>
									<?php echo (in_array('ar', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> AR </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="cr_delete">
								<input name="permissions[]" id="cr_delete" value="cr_delete" type="checkbox" <?php echo isset($permissions['cr_delete']) ? 'checked' : ''; ?>
									<?php echo (in_array('cr_delete', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Delete CR </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="cr_agent">
								<input name="permissions[]" id="cr_agent" value="cr_agent" type="checkbox" <?php echo isset($permissions['cr_agent']) ? 'checked' : ''; ?>
									<?php echo (in_array('cr_agent', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Collection Report Agent </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="freight">
								<input name="permissions[]" id="freight" value="freight" type="checkbox" <?php echo isset($permissions['freight']) ? 'checked' : ''; ?>
									<?php echo (in_array('freight', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Freight</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="billing_print">
								<input name="permissions[]" id="billing_print" value="billing_print" type="checkbox" <?php echo isset($permissions['billing_print']) ? 'checked' : ''; ?>
									<?php echo (in_array('billing_print', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Print Billing Statement</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="deduction_type">
								<input name="permissions[]" id="deduction_type" value="deduction_type" type="checkbox" <?php echo isset($permissions['deduction_type']) ? 'checked' : ''; ?>
									<?php echo (in_array('deduction_type', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Deduction Type</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="deductions">
								<input name="permissions[]" id="deductions" value="deductions" type="checkbox" <?php echo isset($permissions['deductions']) ? 'checked' : ''; ?>
									<?php echo (in_array('deductions', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Deduction list</label>
						</div>



						<div class="col-md-3">
							<label class="checkbox-inline" for="agent_sales">
								<input name="permissions[]" id="agent_sales" value="agent_sales" type="checkbox" <?php echo isset($permissions['agent_sales']) ? 'checked' : ''; ?>
									<?php echo (in_array('agent_sales', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Agent sales</label>
						</div>
					<?php
						if(Configuration::allowedPermission('sms')){
							?>
							<div class="col-md-3">
								<label class="checkbox-inline" for="sms_num">
									<input name="permissions[]" id="sms_num" value="sms_num" type="checkbox" <?php echo isset($permissions['sms_num']) ? 'checked' : ''; ?>
										<?php echo (in_array('sms_num', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										> Sms number</label>
							</div>
							<div class="col-md-3">
								<label class="checkbox-inline" for="sms_log">
									<input name="permissions[]" id="sms_log" value="sms_log" type="checkbox" <?php echo isset($permissions['sms_log']) ? 'checked' : ''; ?>
										<?php echo (in_array('sms_log', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										> Sms log</label>
							</div>
							<?php
						}
					?>
					<?php
						if(Configuration::allowedPermission('vit')){
							?>
							<div class="col-md-3">
								<label class="checkbox-inline" for="cnp">
									<input name="permissions[]" id="cnp" value="cnp" type="checkbox" <?php echo isset($permissions['cnp']) ? 'checked' : ''; ?>
										<?php echo (in_array('cnp', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										> CNP Sales</label>
							</div>
							<div class="col-md-3">
								<label class="checkbox-inline" for="daina">
									<input name="permissions[]" id="daina" value="daina" type="checkbox" <?php echo isset($permissions['daina']) ? 'checked' : ''; ?>
										<?php echo (in_array('daina', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										> Sodaina Sales</label>
							</div>
							<div class="col-md-3">
								<label class="checkbox-inline" for="tedela">
									<input name="permissions[]" id="tedela" value="tedela" type="checkbox" <?php echo isset($permissions['tedela']) ? 'checked' : ''; ?>
										<?php echo (in_array('tedella', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										> Aqua Tedela</label>
							</div>
							<div class="col-md-3">
								<label class="checkbox-inline" for="mastra">
									<input name="permissions[]" id="mastra" value="mastra" type="checkbox" <?php echo isset($permissions['mastra']) ? 'checked' : ''; ?>
										<?php echo (in_array('mastra', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										> Mastra Sales</label>
							</div>
							<div class="col-md-3">
								<label class="checkbox-inline" for="black_samurai">
									<input name="permissions[]" id="black_samurai" value="black_samurai" type="checkbox" <?php echo isset($permissions['black_samurai']) ? 'checked' : ''; ?>
										<?php echo (in_array('black_samurai', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										> Black Samurai</label>
							</div>
							<div class="col-md-3">
								<label class="checkbox-inline" for="ebara">
									<input name="permissions[]" id="ebara" value="ebara" type="checkbox" <?php echo isset($permissions['ebara']) ? 'checked' : ''; ?>
										<?php echo (in_array('ebara', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
										> Ebara</label>
							</div>
							<?php
						}
					?>
					</div>
				</div>
				<?php if($thiscompany->plan_id == 2){ ?>
				<div role="tabpanel" class="tab-pane" id="nav_caravan">
					<div class="col-md-12">
						<br>
						<div class="col-md-3">
							<label class="checkbox-inline" for="caravan_request">
								<input name="permissions[]" id="caravan_request" value="caravan_request" type="checkbox" <?php echo isset($permissions['caravan_request']) ? 'checked' : ''; ?>
									<?php echo (in_array('caravan_request', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Create Request Caravan </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="caravan_manage">
								<input name="permissions[]" id="caravan_manage" value="caravan_manage" type="checkbox" <?php echo isset($permissions['caravan_manage']) ? 'checked' : ''; ?>
									<?php echo (in_array('caravan_manage', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Request Caravan </label>
						</div>
					</div>
					<div class="col-md-12">
						<div class="col-md-3">
							<label class="checkbox-inline" for="mc_pending">
								<input name="permissions[]" id="mc_pending" value="mc_pending" type="checkbox" <?php echo isset($permissions['mc_pending']) ? 'checked' : ''; ?>
									<?php echo (in_array('mc_pending', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> For approval </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="mc_approve">
								<input name="permissions[]" id="mc_approve" value="mc_approve" type="checkbox" <?php echo isset($permissions['mc_approve']) ? 'checked' : ''; ?>
									<?php echo (in_array('mc_approve', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> For releasing </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="mc_processed">
								<input name="permissions[]" id="mc_processed" value="mc_processed" type="checkbox" <?php echo isset($permissions['mc_processed']) ? 'checked' : ''; ?>
									<?php echo (in_array('mc_processed', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> For liquidation </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="mc_liquidate_sales">
								<input name="permissions[]" id="mc_liquidate_sales" value="mc_liquidate_sales" type="checkbox" <?php echo isset($permissions['mc_liquidate_sales']) ? 'checked' : ''; ?>
									<?php echo (in_array('mc_liquidate_sales', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Liquidate Sales </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="mc_liquidate_item">
								<input name="permissions[]" id="mc_liquidate_item" value="mc_liquidate_item" type="checkbox" <?php echo isset($permissions['mc_liquidate_item']) ? 'checked' : ''; ?>
									<?php echo (in_array('mc_liquidate_item', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Liquidate Item </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="mc_verify">
								<input name="permissions[]" id="mc_verify" value="mc_verify" type="checkbox" <?php echo isset($permissions['mc_verify']) ? 'checked' : ''; ?>
									<?php echo (in_array('mc_verify', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Caravan history </label>
						</div>
					</div>
				</div>
				<?php } ?>
				<div role="tabpanel" class="tab-pane" id="nav_settings">
					<div class="col-md-12">
						<br>
						<div class="col-md-3">
							<label class="checkbox-inline" for="call_log">
								<input name="permissions[]" id="call_log" value="call_log" type="checkbox" <?php echo isset($permissions['call_log']) ? 'checked' : ''; ?>
									<?php echo (in_array('call_log', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Phone Call Monitoring </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="settings">
								<input name="permissions[]" id="settings" value="settings" type="checkbox" <?php echo isset($permissions['settings']) ? 'checked' : ''; ?>
									<?php echo (in_array('settings', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Settings </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="inbox">
								<input name="permissions[]" id="inbox" value="inbox" type="checkbox" <?php echo isset($permissions['inbox']) ? 'checked' : ''; ?>
									<?php echo (in_array('inbox', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									>  Inbox </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="config">
								<input name="permissions[]" id="config" value="config" type="checkbox" <?php echo isset($permissions['config']) ? 'checked' : ''; ?>
									<?php echo (in_array('config', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Configurations </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="sales_crud">
								<input name="permissions[]" id="sales_crud" value="sales_crud" type="checkbox" <?php echo isset($permissions['sales_crud']) ? 'checked' : ''; ?>
									<?php echo (in_array('sales_crud', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Sales CRUD </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="reports">
								<input name="permissions[]" id="reports" value="reports" type="checkbox" <?php echo isset($permissions['reports']) ? 'checked' : ''; ?>
									<?php echo (in_array('reports', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Reports </label>
						</div>


					</div>
					<div class="col-md-12">
						<div class="col-md-3">
							<label class="checkbox-inline" for="station_settings">
								<input name="permissions[]" id="station_settings" value="station_settings" type="checkbox" <?php echo isset($permissions['station_settings']) ? 'checked' : ''; ?>
									<?php echo (in_array('station_settings', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Station Settings </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="supplier_settings">
								<input name="permissions[]" id="supplier_settings" value="supplier_settings" type="checkbox" <?php echo isset($permissions['supplier_settings']) ? 'checked' : ''; ?>
									<?php echo (in_array('supplier_settings', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Supplier Settings </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="themes">
								<input name="permissions[]" id="themes" value="themes" type="checkbox" <?php echo isset($permissions['themes']) ? 'checked' : ''; ?>
									<?php echo (in_array('themes', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Themes</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="recycle">
								<input name="permissions[]" id="recycle" value="recycle" type="checkbox" <?php echo isset($permissions['recycle']) ? 'checked' : ''; ?>
									<?php echo (in_array('recycle', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Recycle Bin</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="tools_mon">
								<input name="permissions[]" id="tools_mon" value="tools_mon" type="checkbox" <?php echo isset($permissions['tools_mon']) ? 'checked' : ''; ?>
									<?php echo (in_array('tools_mon', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Tools</label>
						</div>
					</div>
					<div class="col-md-12">

						<div class="col-md-3">
							<label class="checkbox-inline" for="consumable_admin">
								<input name="permissions[]" id="consumable_admin" value="consumable_admin" type="checkbox" <?php echo isset($permissions['consumable_admin']) ? 'checked' : ''; ?>
									<?php echo (in_array('consumable_admin', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Consumable Admin </label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="consumablefree_admin">
								<input name="permissions[]" id="consumablefree_admin" value="consumablefree_admin" type="checkbox" <?php echo isset($permissions['consumablefree_admin']) ? 'checked' : ''; ?>
									<?php echo (in_array('consumablefree_admin', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Consumable Freebies Admin </label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="dl_prod">
								<input name="permissions[]" id="dl_prod" value="dl_prod" type="checkbox" <?php echo isset($permissions['dl_prod']) ? 'checked' : ''; ?>
									<?php echo (in_array('dl_prod', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Download Product </label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="dl_price">
								<input name="permissions[]" id="dl_price" value="dl_price" type="checkbox" <?php echo isset($permissions['dl_price']) ? 'checked' : ''; ?>
									<?php echo (in_array('dl_price', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Download Pricelist </label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="dl_inv">
								<input name="permissions[]" id="dl_inv" value="dl_inv" type="checkbox" <?php echo isset($permissions['dl_inv']) ? 'checked' : ''; ?>
									<?php echo (in_array('dl_inv', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Download Inventory </label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="dl_inv_pr">
								<input name="permissions[]" id="dl_inv_pr" value="dl_inv_pr" type="checkbox" <?php echo isset($permissions['dl_inv_pr']) ? 'checked' : ''; ?>
									<?php echo (in_array('dl_inv_pr', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Download Inventory With Price </label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="dl_inv_mon">
								<input name="permissions[]" id="dl_inv_mon" value="dl_inv_mon" type="checkbox" <?php echo isset($permissions['dl_inv_mon']) ? 'checked' : ''; ?>
									<?php echo (in_array('dl_inv_mon', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Download Inventory Monitoring</label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="dl_inv_report">
								<input name="permissions[]" id="dl_inv_report" value="dl_inv_report" type="checkbox" <?php echo isset($permissions['dl_inv_report']) ? 'checked' : ''; ?>
									<?php echo (in_array('dl_inv_report', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Download Inventory Report</label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="dl_inv_audit">
								<input name="permissions[]" id="dl_inv_audit" value="dl_inv_audit" type="checkbox" <?php echo isset($permissions['dl_inv_audit']) ? 'checked' : ''; ?>
									<?php echo (in_array('dl_inv_audit', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Download Inventory Audit</label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="dl_member">
								<input name="permissions[]" id="dl_member" value="dl_member" type="checkbox" <?php echo isset($permissions['dl_member']) ? 'checked' : ''; ?>
									<?php echo (in_array('dl_member', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Download Member</label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="dl_sales">
								<input name="permissions[]" id="dl_sales" value="dl_sales" type="checkbox" <?php echo isset($permissions['dl_sales']) ? 'checked' : ''; ?>
									<?php echo (in_array('dl_sales', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Download Sales</label>
						</div>

					</div>
				<div class="row">
					<div class="col-md-12">
						<br><br>	<h5>Other reports</h5>
					</div>

					<div class="col-md-12">
						<div class="col-md-3">
							<label class="checkbox-inline" for="r_item">
								<input name="permissions[]" id="r_item" value="r_item" type="checkbox" <?php echo isset($permissions['r_item']) ? 'checked' : ''; ?>
									<?php echo (in_array('r_item', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Item report </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="daily_sales">
								<input name="permissions[]" id="daily_sales" value="daily_sales" type="checkbox" <?php echo isset($permissions['daily_sales']) ? 'checked' : ''; ?>
									<?php echo (in_array('daily_sales', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Daily summary</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="st_sum_sales">
								<input name="permissions[]" id="st_sum_sales" value="st_sum_sales" type="checkbox" <?php echo isset($permissions['st_sum_sales']) ? 'checked' : ''; ?>
									<?php echo (in_array('st_sum_sales', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Sales type summary</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="deduction_summary">
								<input name="permissions[]" id="deduction_summary" value="deduction_summary" type="checkbox" <?php echo isset($permissions['deduction_summary']) ? 'checked' : ''; ?>
									<?php echo (in_array('deduction_summary', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Deduction summary</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="r_quota">
								<input name="permissions[]" id="r_quota" value="r_quota" type="checkbox" <?php echo isset($permissions['r_quota']) ? 'checked' : ''; ?>
									<?php echo (in_array('r_quota', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Quota report </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="r_service_only">
								<input name="permissions[]" id="r_service_only" value="r_service_only" type="checkbox" <?php echo isset($permissions['r_service_only']) ? 'checked' : ''; ?>
									<?php echo (in_array('r_item', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Service only report </label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="r_item_no">
								<input name="permissions[]" id="r_item_no" value="r_item_no" type="checkbox" <?php echo isset($permissions['r_item_no']) ? 'checked' : ''; ?>
									<?php echo (in_array('r_item_no', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Item no price
							</label>
						</div>

						<div class="col-md-3">
							<label class="checkbox-inline" for="r_order">
								<input name="permissions[]" id="r_order" value="r_order" type="checkbox" <?php echo isset($permissions['r_order']) ? 'checked' : ''; ?>
									<?php echo (in_array('r_order', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Order report </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="r_client">
								<input name="permissions[]" id="r_client" value="r_client" type="checkbox" <?php echo isset($permissions['r_client']) ? 'checked' : ''; ?>
									<?php echo (in_array('r_client', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Client report </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="r_freebie">
								<input name="permissions[]" id="r_freebie" value="r_freebie" type="checkbox" <?php echo isset($permissions['r_freebie']) ? 'checked' : ''; ?>
									<?php echo (in_array('r_freebie', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Freebie report </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="dashboard_tm">
								<input name="permissions[]" id="dashboard_tm" value="dashboard_tm" type="checkbox" <?php echo isset($permissions['dashboard_tm']) ? 'checked' : ''; ?>
									<?php echo (in_array('dashboard_tm', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Dashboard Top Management</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="collection_tm">
								<input name="permissions[]" id="collection_tm" value="collection_tm" type="checkbox" <?php echo isset($permissions['collection_tm']) ? 'checked' : ''; ?>
									<?php echo (in_array('collection_tm', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Collection Top Management</label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="other_income">
								<input name="permissions[]" id="other_income" value="other_income" type="checkbox" <?php echo isset($permissions['other_income']) ? 'checked' : ''; ?>
									<?php echo (in_array('other_income', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Other Income</label>
						</div>
					</div>
				</div>
				</div>
				<?php
					if(Configuration::allowedPermission('point_system')){
						?>
						<div role="tabpanel" class="tab-pane" id="nav_point">
							<div class="col-md-12">
								<br>
								<div class="col-md-3">
									<label class="checkbox-inline" for="p_point">
										<input name="permissions[]" id="p_point" value="p_point" type="checkbox" <?php echo isset($permissions['p_point']) ? 'checked' : ''; ?>
											<?php echo (in_array('p_point', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
											> Points  </label>
								</div>
								<div class="col-md-3">
									<label class="checkbox-inline" for="p_point_manage">
										<input name="permissions[]" id="p_point_manage" value="p_point_manage" type="checkbox" <?php echo isset($permissions['p_point_manage']) ? 'checked' : ''; ?>
											<?php echo (in_array('p_point_manage', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
											> Manage Points/Membership  </label>
								</div>
								<div class="col-md-3">
									<label class="checkbox-inline" for="p_point_transfer">
										<input name="permissions[]" id="p_point_transfer" value="p_point_transfer" type="checkbox" <?php echo isset($permissions['p_point_transfer']) ? 'checked' : ''; ?>
											<?php echo (in_array('p_point_transfer', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
											> Transfer Points  </label>
								</div>
								<div class="col-md-3">
									<label class="checkbox-inline" for="p_point_sell">
										<input name="permissions[]" id="p_point_sell" value="p_point_sell" type="checkbox" <?php echo isset($permissions['p_point_sell']) ? 'checked' : ''; ?>
											<?php echo (in_array('p_point_sell', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
											> Sell/Buy Points  </label>
								</div>
								<div class="col-md-3">
									<label class="checkbox-inline" for="wallet_manage">
										<input name="permissions[]" id="wallet_manage" value="wallet_manage" type="checkbox" <?php echo isset($permissions['wallet_manage']) ? 'checked' : ''; ?>
											<?php echo (in_array('wallet_manage', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
											> Manage E-Wallet</label>
								</div>
								<div class="col-md-3">
									<label class="checkbox-inline" for="wallet_req">
										<input name="permissions[]" id="wallet_req" value="wallet_req" type="checkbox" <?php echo isset($permissions['wallet_req']) ? 'checked' : ''; ?>
											<?php echo (in_array('wallet_req', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
											> E-Wallet</label>
								</div>
								<div class="col-md-3">
									<label class="checkbox-inline" for="ez_bills">
										<input name="permissions[]" id="ez_bills" value="ez_bills" type="checkbox" <?php echo isset($permissions['ez_bills']) ? 'checked' : ''; ?>
											<?php echo (in_array('ez_bills', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
											> Pay Bills  </label>
								</div>
								<div class="col-md-3">
									<label class="checkbox-inline" for="ez_bills_process">
										<input name="permissions[]" id="ez_bills_process" value="ez_bills_process" type="checkbox" <?php echo isset($permissions['ez_bills_process']) ? 'checked' : ''; ?>
											<?php echo (in_array('ez_bills_process', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
											> Process Pay Bills  </label>
								</div>
								<div class="col-md-3">
									<label class="checkbox-inline" for="ez_bills_categ">
										<input name="permissions[]" id="ez_bills_categ" value="ez_bills_categ" type="checkbox" <?php echo isset($permissions['ez_bills_categ']) ? 'checked' : ''; ?>
											<?php echo (in_array('ez_bills_categ', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
											> Manage Bills Category </label>
								</div>
								<div class="col-md-3">
									<label class="checkbox-inline" for="ez_bills_company">
										<input name="permissions[]" id="ez_bills_company" value="ez_bills_company" type="checkbox" <?php echo isset($permissions['ez_bills_company']) ? 'checked' : ''; ?>
											<?php echo (in_array('ez_bills_company', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
											> Manage Company </label>
								</div>
							</div>
						</div>
					<?php }?>

					<?php
					if(Configuration::allowedPermission('med')){
					?>
				<div role="tabpanel" class="tab-pane" id="nav_medic">
					<div class="col-md-12">
						<br>
						<div class="col-md-3">
							<label class="checkbox-inline" for="med_doctor">
								<input name="permissions[]" id="med_doctor" value="med_doctor" type="checkbox" <?php echo isset($permissions['med_doctor']) ? 'checked' : ''; ?>
									<?php echo (in_array('med_doctor', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Doctor  </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="med_nurse">
								<input name="permissions[]" id="med_nurse" value="med_nurse" type="checkbox" <?php echo isset($permissions['med_nurse']) ? 'checked' : ''; ?>
									<?php echo (in_array('med_nurse', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Nurse  </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="med_history">
								<input name="permissions[]" id="med_history" value="med_history" type="checkbox" <?php echo isset($permissions['med_history']) ? 'checked' : ''; ?>
									<?php echo (in_array('med_history', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage History  </label>
						</div>
						<div class="col-md-3">
							<label class="checkbox-inline" for="med_diag">
								<input name="permissions[]" id="med_diag" value="med_diag" type="checkbox" <?php echo isset($permissions['med_diag']) ? 'checked' : ''; ?>
									<?php echo (in_array('med_diag', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
									> Manage Diagnosis  </label>
						</div>
					</div>
				</div>
					<?php }?>
				<?php
					if(Configuration::allowedPermission('gym')){
						?>
						<div role="tabpanel" class="tab-pane" id="nav_gym">
							<div class="col-md-12">
								<br>
								<div class="col-md-3">
									<label class="checkbox-inline" for="exp_tbl">
										<input name="permissions[]" id="exp_tbl" value="exp_tbl" type="checkbox" <?php echo isset($permissions['exp_tbl']) ? 'checked' : ''; ?>
											<?php echo (in_array('exp_tbl', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
											> Experience Table  </label>
								</div>
								<div class="col-md-3">
									<label class="checkbox-inline" for="c_rates">
										<input name="permissions[]" id="c_rates" value="c_rates" type="checkbox" <?php echo isset($permissions['c_rates']) ? 'checked' : ''; ?>
											<?php echo (in_array('c_rates', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
											> Coaches Rates  </label>
								</div>
								<div class="col-md-3">
									<label class="checkbox-inline" for="wo_mod">
										<input name="permissions[]" id="wo_mod" value="wo_mod" type="checkbox" <?php echo isset($permissions['wo_mod']) ? 'checked' : ''; ?>
											<?php echo (in_array('wo_mod', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
											> Work out Module </label>
								</div>
								<div class="col-md-3">
									<label class="checkbox-inline" for="m_exp">
										<input name="permissions[]" id="m_exp" value="m_exp" type="checkbox" <?php echo isset($permissions['m_exp']) ? 'checked' : ''; ?>
											<?php echo (in_array('m_exp', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
											> Manage Experience </label>
								</div>
								<div class="col-md-3">
									<label class="checkbox-inline" for="m_ref">
										<input name="permissions[]" id="m_ref" value="m_ref" type="checkbox" <?php echo isset($permissions['m_ref']) ? 'checked' : ''; ?>
											<?php echo (in_array('m_ref', Input::get('permissions')) && Input::get('permissions') != '') ? 'checked' : ''; ?>
											> Referrals </label>
								</div>
							</div>
						</div>
					<?php } ?>
			</div>
		</div>



	<hr />
	<!-- Button (Double) -->
	<div class="form-group">
		<label class="col-md-4 control-label" for="button1id"></label>

		<div class="col-md-8">
			<input type='submit' style='position:fixed;top:90%;right:5px;opacity:0.8;border-radius:20px;' class='btn btn-primary' name='btnSave' value='SAVE' />
			<input type='hidden' name='token' value=<?php echo Token::generate(); ?>>
			<input type='hidden' name='edit' value=<?php echo isset($id) ? escape(Encryption::encrypt_decrypt('encrypt', $id)) : 0; ?>>

		</div>
	</div>

	</fieldset>
	</form>
	</div>

	</div> <!-- end page content wrapper-->
	<div id='access_img'>
		<div>
			<img style='width:100%; height:auto; overflow-y: hidden' id='access_img_holder'>
		</div>
		<div class='text-right text-danger'>
			<a href="#" id='img-view' style='margin-right:10px;'>view</a>
			<a href="#" id='img-close' style='margin-right:10px;'>close</a>

		</div>

	</div>


<?php require_once '../includes/admin/page_tail2.php'; ?>