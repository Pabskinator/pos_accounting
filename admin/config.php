<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';

	if(!$user->hasPermission('config')) {
		// redirect to denied page
		Redirect::to(1);
	}

	$branchcls = new Branch();

	$branches = $branchcls->get_active('branches', array('company_id' ,'=',$user->data()->company_id));


?>

	<!-- Page content -->
	<div id="page-content-wrapper">
		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					CONFIGURATIONS
				</h1>
			</div>
			<div class="alert alert-danger">
				<p>Please don't change configuration values unless you know exactly what you're doing.</p>
			</div>
			<form class="form-horizontal" id='form_config' action="" method="POST">

				<fieldset>
					<h4>Labels</h4>
					<div class="form-group">

						<div class="col-md-3">
							<strong>Sub Company Label</strong>
							<input id="sub_company" name="sub_company" placeholder="" class="form-control input-md" type="text" value="<?php echo Configuration::getValue('sub_company'); ?>">
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Spare part Label</strong>
							<input id="spare_part" name="spare_part" placeholder="" class="form-control input-md" type="text" value="<?php echo Configuration::getValue('spare_part'); ?>">
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Assemble Label</strong>
							<input id="assemble" name="assemble" placeholder="" class="form-control input-md" type="text" value="<?php echo Configuration::getValue('assemble'); ?>">
							<span class="help-block"></span>
						</div>

						<div class="col-md-3">
							<strong>Disassemble Label</strong>
							<input id="disassemble" name="disassemble" placeholder="" class="form-control input-md" type="text" value="<?php echo Configuration::getValue('disassemble'); ?>">
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Assemble Step 1</strong>
							<input id="a_step1" name="a_step1" placeholder="" class="form-control input-md" type="text" value="<?php echo Configuration::getValue('a_step1'); ?>">
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Assemble Step 2 </strong>
							<input id="a_step2" name="a_step2" placeholder="" class="form-control input-md" type="text" value="<?php echo Configuration::getValue('a_step2'); ?>">
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Assemble Step 3</strong>
							<input id="a_step3" name="a_step3" placeholder="" class="form-control input-md" type="text" value="<?php echo Configuration::getValue('a_step3'); ?>">
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Supply Label</strong>
							<input id="supply_label" name="supply_label" placeholder="" class="form-control input-md" type="text" value="<?php echo Configuration::getValue('supply_label'); ?>">
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Invoice</strong>
							<input id="invoice_label" name="invoice_label" placeholder="" class="form-control input-md" type="text" value="<?php echo Configuration::getValue('invoice_label'); ?>">
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Invoice Prefix</strong>
							<input id="invpref_label" name="invpref_label" placeholder="" class="form-control input-md" type="text" value="<?php echo Configuration::getValue('invpref_label'); ?>">
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>DR</strong>
							<input id="dr_label" name="dr_label" placeholder="" class="form-control input-md" type="text" value="<?php echo Configuration::getValue('dr_label'); ?>">
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Dr Prefix</strong>
							<input id="drpref_label" name="drpref_label" placeholder="" class="form-control input-md" type="text" value="<?php echo Configuration::getValue('drpref_label'); ?>">
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>PR</strong>
							<input id="pr_label" name="pr_label" placeholder="" class="form-control input-md" type="text" value="<?php echo Configuration::getValue('pr_label'); ?>">
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>PR Prefix</strong>
							<input id="prpref_label" name="prpref_label" placeholder="" class="form-control input-md" type="text" value="<?php echo Configuration::getValue('prpref_label'); ?>">
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Damage</strong>
							<input id="damage_lbl" name="damage_lbl" placeholder="" class="form-control input-md" type="text" value="<?php echo Configuration::getValue('damage_lbl'); ?>">
							<span class="help-block">Damage label</span>
						</div>
						<div class="col-md-3">
							<strong>Incomplete</strong>
							<input id="inc_lbl" name="inc_lbl" placeholder="" class="form-control input-md" type="text" value="<?php echo Configuration::getValue('inc_lbl'); ?>">
							<span class="help-block">Incomplete label</span>
						</div>
						<div class="col-md-3">
							<strong>Missing</strong>
							<input id="missing_lbl" name="missing_lbl" placeholder="" class="form-control input-md" type="text" value="<?php echo Configuration::getValue('missing_lbl'); ?>">
							<span class="help-block">Missing label</span>
						</div>
						<div class="col-md-3">
							<strong>Other Issue Tag Label</strong>
							<input id="other_issue_lbl" name="other_issue_lbl" placeholder="" class="form-control input-md" type="text" value="<?php echo Configuration::getValue('other_issue_lbl'); ?>">
							<span class="help-block">Leave blank if not applicable</span>
						</div>
					</div>
					<h4>Members/Client</h4>
					<div class="form-group">
						<div class="col-md-3">
							<strong>Member label</strong>
							<input id="member_label" name="member_label" placeholder="" class="form-control input-md" type="text" value="<?php echo Configuration::getValue('member_label'); ?>">
							<span class="help-block">Member label</span>
						</div>
					</div>
					<h4>POS</h4>
					<div class="form-group">
						<div class="col-md-3">
							<strong>Receipt # of Copies</strong>
							<input id="rec_num_copy" name="rec_num_copy" placeholder="" class="form-control input-md" type="text" value="<?php echo Configuration::getValue('rec_num_copy'); ?>">
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>News print</strong>
							<select name="news_print" id="news_print" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('news_print') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('news_print') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>
					</div>

					<h4>Warehouse Order</h4>
					<div class="form-group">
						<div class="col-md-3">
							<strong>Transfer Inv Label</strong>
							<input id="transfer_inv_label" name="transfer_inv_label" placeholder="" class="form-control input-md" type="text" value="<?php echo Configuration::getValue('transfer_inv_label'); ?>">
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Receive Inv Label</strong>
							<input id="receive_inv_label" name="receive_inv_label" placeholder="" class="form-control input-md" type="text" value="<?php echo Configuration::getValue('receive_inv_label'); ?>">
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Shipping Label</strong>
							<input id="shipping_lbl" name="shipping_lbl" placeholder="" class="form-control input-md" type="text" value="<?php echo Configuration::getValue('shipping_lbl'); ?>">
							<span class="help-block">Shipping label</span>
						</div>
						<div class="col-md-3">
							<strong>Days for re-scheduling</strong>
							<input id="reschedule_order" name="reschedule_order" placeholder="" class="form-control input-md" type="text" value="<?php echo Configuration::getValue('reschedule_order'); ?>">
							<span class="help-block">Number of days allowed for an order to be re-schedule</span>
						</div>
						<div class="col-md-3">
							<strong>Client branch order</strong>
							<select name="client_order" id="client_order" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('client_order') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('client_order') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Get Stock Order</strong>
							<select name="get_stock_order" id="get_stock_order" class='form-control'>
								<option value="0" <?php echo (Configuration::getValue('get_stock_order') == 0) ? 'selected' : ''; ?> >Alphabetical asc</option>
								<option value="1" <?php echo (Configuration::getValue('get_stock_order') == 1) ? 'selected' : ''; ?> >Quantity asc</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Inventory Status</strong>
							<select name="inv_strict" id="inv_strict" class='form-control'>
								<option value="0" <?php echo (Configuration::getValue('inv_strict') == 0) ? 'selected' : ''; ?> >Strict</option>
								<option value="1" <?php echo (Configuration::getValue('inv_strict') == 1) ? 'selected' : ''; ?> >Not Strict</option>
							</select>
							<span class="help-block">Allow request to process even if it has insufficient stock.</span>
						</div>
						<div class="col-md-3">
							<strong>Assemble Status</strong>
							<select name="assemble_strict" id="assemble_strict" class='form-control'>
								<option value="1" <?php echo (Configuration::getValue('assemble_strict') == 1) ? 'selected' : ''; ?> >Strict</option>
								<option value="2" <?php echo (Configuration::getValue('assemble_strict') == 2) ? 'selected' : ''; ?> >Not Strict</option>
							</select>
							<span class="help-block">Allow request to process even if it has insufficient stock.</span>
						</div>
						<div class="col-md-3">
							<strong>Hide Price Order</strong>
							<select name="branch_show_price" id="branch_show_price" class='form-control' multiple>
								<option value=""></option>
								<?php
									foreach($branches as $b){
										echo "<option value='$b->id'>$b->name</option>";
									}
								?>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Hide Price Inventory</strong>
							<select name="hide_price_inv" id="hide_price_inv" class='form-control' multiple>
								<option value=""></option>
								<?php
									foreach($branches as $b){
										echo "<option value='$b->id'>$b->name</option>";
									}
								?>
							</select>
							<span class="help-block">Hide Price in Inventory Page</span>
						</div>
						<div class="col-md-3">
							<strong>Can Add Inventory</strong>
							<select name="can_add_inventory" id="can_add_inventory" class='form-control' multiple>
								<option value=""></option>
								<?php
									foreach($branches as $b){
										echo "<option value='$b->id'>$b->name</option>";
									}
								?>
							</select>
							<span class="help-block">Hide Price in Inventory Page</span>
						</div>
						<div class="col-md-3">
							<strong>Hide/Show Disassemble</strong>
							<select name="disassemble_view" id="disassemble_view" class='form-control'>
								<option value="1" <?php echo (Configuration::getValue('disassemble_view') == 1) ? 'selected' : ''; ?> >Show</option>
								<option value="2" <?php echo (Configuration::getValue('disassemble_view') == 2) ? 'selected' : ''; ?> >Hide</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Hide/Show Form Style</strong>
							<select name="form_style" id="form_style" class='form-control'>
								<option value="1" <?php echo (Configuration::getValue('form_style') == 1) ? 'selected' : ''; ?> >Show</option>
								<option value="2" <?php echo (Configuration::getValue('form_style') == 2) ? 'selected' : ''; ?> >Hide</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Points</strong>
							<select name="points" id="points" class='form-control'>
								<option value="1" <?php echo (Configuration::getValue('points') == 1) ? 'selected' : ''; ?> >On</option>
								<option value="2" <?php echo (Configuration::getValue('points') == 2) ? 'selected' : ''; ?> >Off</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>E Wallet</strong>
							<select name="wallet" id="wallet" class='form-control'>
								<option value="1" <?php echo (Configuration::getValue('wallet') == 1) ? 'selected' : ''; ?> >On</option>
								<option value="2" <?php echo (Configuration::getValue('wallet') == 2) ? 'selected' : ''; ?> >Off</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Mem Adjustment</strong>
							<select name="mem_adj" id="mem_adj" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('mem_adj') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('mem_adj') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Round</strong>
							<input id="mem_adj_round" name="mem_adj_round" placeholder="" class="form-control input-md" type="text" value="<?php echo Configuration::getValue('mem_adj_round'); ?>">
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Strict Order</strong>
							<select name="strict_order" id="strict_order" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('strict_order') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('strict_order') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Branch Tagging</strong>
							<select name="branch_tag" id="branch_tag" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('branch_tag') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('branch_tag') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Order Limit</strong>
							<input id="order_limit" name="order_limit" placeholder="" class="form-control input-md" type="text" value="<?php echo Configuration::getValue('order_limit'); ?>">
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Billing Extra Message</strong>
							<input id="billing_remarks" name="billing_remarks" placeholder="" class="form-control input-md" type="text" value="<?php echo Configuration::getValue('billing_remarks'); ?>">
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Order has attachment</strong>
							<select name="order_reservation_attachment" id="order_reservation_attachment" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('order_reservation_attachment') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('order_reservation_attachment') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Save Pending Member</strong>
							<select name="order_pending_member" id="order_pending_member" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('order_pending_member') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('order_pending_member') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Order has station</strong>
							<select name="order_has_station" id="order_has_station" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('order_has_station') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('order_has_station') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Order skip reserved</strong>
							<select name="order_skip_reserve" id="order_skip_reserve" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('order_skip_reserve') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('order_skip_reserve') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Adjustment Default</strong>
							<select name="adjustment_default" id="adjustment_default" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('adjustment_default') == 1) ? 'selected' : ''; ?>>Add</option>
								<option value="2" <?php echo (Configuration::getValue('adjustment_default') == 2) ? 'selected' : ''; ?>>Deduct</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Walk In Approval</strong>
							<select name="walkin_app" id="walkin_app" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('walkin_app') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('walkin_app') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>From Service/MAIN option</strong>
							<select name="service_main_option" id="service_main_option" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('service_main_option') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('service_main_option') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Order Item addtl discount</strong>
							<select name="addtl_disc" id="addtl_disc" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('addtl_disc') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('addtl_disc') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Auto Deduct Inv POS</strong>
							<select name="auto_dededuct_inv" id="auto_dededuct_inv" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('auto_dededuct_inv') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('auto_dededuct_inv') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Cashier Transaction</strong>
							<select name="c_trans" id="c_trans" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('c_trans') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('c_trans') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Specific Station/Sales type</strong>
							<select name="spec_stats" id="spec_stats" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('spec_stats') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('spec_stats') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Checker Inv</strong>
							<select name="inv_check" id="inv_check" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('inv_check') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('inv_check') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Has SV</strong>
							<select name="has_sv" id="has_sv" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('has_sv') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('has_sv') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>

						<div class="col-md-3">
							<strong>Auto Member Credit</strong>
							<select name="auto_member_credit" id="auto_member_credit" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('auto_member_credit') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('auto_member_credit') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Price Group</strong>
							<select name="price_group" id="price_group" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('price_group') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('price_group') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>

						<div class="col-md-3">
							<strong>Credit approval</strong>
							<select name="credit_approval" id="credit_approval" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('credit_approval') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('credit_approval') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Different unit</strong>
							<select name="d_unit" id="d_unit" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('d_unit') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('d_unit') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Has Surplus Rack Monitoring</strong>
							<select name="surplus_rack" id="surplus_rack" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('surplus_rack') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('surplus_rack') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Has BO Rack Monitoring</strong>
							<select name="bo_rack" id="bo_rack" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('bo_rack') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('bo_rack') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Has AR Commissionable</strong>
							<select name="ar_commissionable" id="ar_commissionable" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('ar_commissionable') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('ar_commissionable') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Show Doc Util in Credit</strong>
							<select name="doc_util" id="doc_util" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('doc_util') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('doc_util') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Has charge label</strong>
							<select name="charge_label" id="charge_label" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('charge_label') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('charge_label') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Email Rush Supplier Order</strong>
							<input id="email_rush" name="email_rush" placeholder="" class="form-control input-md" type="text" value="<?php echo Configuration::getValue('email_rush'); ?>">
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>With PO Digital Signature</strong>
							<select name="digital_sign" id="digital_sign" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('digital_sign') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('digital_sign') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>With Open Bundle</strong>
							<select name="open_bundle" id="open_bundle" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('open_bundle') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('open_bundle') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Discount Label</strong>
							<select name="discount_label" id="discount_label" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('discount_label') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('discount_label') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Member Discount By Category</strong>
							<select name="discount_by_category" id="discount_by_category" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('discount_by_category') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('discount_by_category') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Group Adjustment</strong>
							<select name="group_adjustment_optional" id="group_adjustment_optional" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('group_adjustment_optional') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('group_adjustment_optional') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Deposits On Collection</strong>
							<select name="deposits_collection" id="deposits_collection" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('deposits_collection') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('deposits_collection') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>

						<div class="col-md-3">
							<strong>Has SR</strong>
							<select name="has_sr" id="has_sr" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('has_sr') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('has_sr') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Has TS</strong>
							<select name="has_ts" id="has_ts" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('has_ts') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('has_ts') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>
						<div class="col-md-3">
							<strong>Simple Timelog</strong>
							<select name="simple_timelog" id="simple_timelog" class="form-control">
								<option value="1" <?php echo (Configuration::getValue('simple_timelog') == 1) ? 'selected' : ''; ?>>Yes</option>
								<option value="2" <?php echo (Configuration::getValue('simple_timelog') == 2) ? 'selected' : ''; ?>>No</option>
							</select>
							<span class="help-block"></span>
						</div>
					</div>
					<div id="test"></div>
					<div class="form-group text-right">
						<button class='btn btn-default' id='btnSave'>SAVE</button>
					</div>
				</fieldset>
			</form>

		</div>
	</div> <!-- end page content wrapper-->

	<script>
		$(function(){
			var branch_show_price = "<?php echo Configuration::getValue('branch_show_price');  ?>";
			var bsp = [];
			if(branch_show_price.indexOf(',') >0){
				bsp = branch_show_price.split(',');
			} else {
				if(branch_show_price){
				bsp.push(branch_show_price);
				}
			}
			$('#branch_show_price').select2({
				allowClear: true
			}).select2('val',bsp);

			var branch_show_price_inv = "<?php echo Configuration::getValue('hide_price_inv');  ?>";
			var bsp2 = [];
			if(branch_show_price_inv.indexOf(',') >0){
				bsp2 = branch_show_price_inv.split(',');
			} else {
				if(branch_show_price_inv){
					bsp2.push(branch_show_price_inv);
				}
			}

			$('#hide_price_inv').select2({
				allowClear: true
			}).select2('val',bsp2);

			var can_add_inventory = "<?php echo Configuration::getValue('can_add_inventory');  ?>";
			var bsp3 = [];
			if(can_add_inventory.indexOf(',') >0){
				bsp3 = can_add_inventory.split(',');
			} else {
				if(can_add_inventory){
					bsp3.push(can_add_inventory);
				}
			}

			$('#can_add_inventory').select2({
				allowClear: true
			}).select2('val',bsp3);


			$('body').on('click','#btnSave',function(e){
				e.preventDefault();
				var btncon = $(this);
				var btnoldval = btncon.html();
				btncon.attr('disabled',true);
				btncon.html('Loading...');

				var form_config = $('#form_config').serializeArray();
				$.ajax({
				    url:'../ajax/ajax_query2.php',
				    type:'POST',
				    data: {functionName:'saveConfigurations', form_config:JSON.stringify(form_config)},
				    success: function(data){

				        alertify.alert(data,function(){
					       location.href='config.php';
				        });
				    },
				    error:function(){

				    }
				})
			});
		})
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?><?php
	/**
	 * Created by PhpStorm.
	 * User: temp
	 * Date: 4/26/2016
	 * Time: 1:35 PM
	 */