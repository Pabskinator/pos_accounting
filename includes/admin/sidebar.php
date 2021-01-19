<?php
	$cf = new Custom_field();
	$getstationdet = $cf->getcustomform('stations',$user->data()->company_id);
	$custom_station_name = isset($getstationdet->label_name)? strtoupper($getstationdet->label_name):'STATION';
	$custom_station_name = ucfirst(strtolower($custom_station_name));
?>
<div id="navhider" ><i class='fa fa-gear'></i></div>
<div id="sidebar-wrapper">
<div style='width:220px;padding-bottom:40px; '>
<br/>
<div class="panel-group" id="accordion">
<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title" style=''>
			<a  class='navPage' data-loc='index.php'><span class="fa fa-dashboard">
                            </span> Dashboard
			</a>
		</h4>
	</div>
</div>

<?php

	if($user->hasPermission('item') || $user->hasPermission('category') || $user->hasPermission('characteristics') || $user->hasPermission('unit') || $user->hasPermission('queue') || $user->hasPermission('item_adj')) { ?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo"><span class="fa fa-barcode">
                            </span> Products </a>
			</h4>
		</div>
		<div id="collapseTwo" class="panel-collapse collapse" >
			<div class="panel-body">
				<table class="table">
					<?php  if($user->hasPermission('item')) {  ?>
						<tr>
							<td>
								<a class='navPage' data-loc='product.php'>Product list</a>
							</td>
						</tr>
					<?php } ?>
					<?php  if($user->hasPermission('item_post') ) {  ?>
						<tr>
							<td>
								<a class='navPage' data-loc='item_posting.php'>Post Item</a>
							</td>
						</tr>
						<tr>
							<td>
								<a class='navPage' data-loc='item_spec.php'>Item Specification</a>
							</td>
						</tr>
					<?php } ?>
					<?php  if($user->hasPermission('category')) {  ?>
						<tr>
							<td>
								<a class='navPage' data-loc="category.php">Categories</a>
							</td>
						</tr>
					<?php } ?>
					<?php  if($user->hasPermission('characteristics')) {  ?>
						<tr>
							<td>
								<a class='navPage' data-loc="characteristics.php">Characteristics</a>
							</td>
						</tr>
					<?php } ?>
					<?php  if($user->hasPermission('unit')) {  ?>
						<tr>
							<td>
								<a class='navPage' data-loc="unit.php">Units</a>
							</td>
						</tr>
					<?php } ?>
					<?php  if($user->hasPermission('queue')) {  ?>
						<tr>
							<td>
								<a class='navPage' data-loc="queu.php">Queues</a>
							</td>
						</tr>
					<?php } ?>
					<?php  if($user->hasPermission('item_adj')) {  ?>
						<tr>
							<td>
								<a class='navPage' data-loc="item-price-adjustment.php">Pricelist</a>
							</td>
						</tr>
					<?php } ?>
					<?php  if($user->hasPermission('pr_adj_categ')) {  ?>
						<tr>
							<td>
								<a class='navPage' data-loc="member_category_discount.php">Adjustment By Category</a>
							</td>
						</tr>
					<?php } ?>

					<?php  if($user->hasPermission('group_adjustment')) {  ?>
						<tr>
							<td>
								<a class='navPage' data-loc="item_group_adjustment.php">Adjustment by Area</a>
							</td>
						</tr>
					<?php } ?>
					<?php  if($user->hasPermission('bundles')) {  ?>
						<tr>
							<td>
								<a class='navPage' data-loc='bundle_list.php'>Bundles</a>
							</td>
						</tr>
					<?php } ?>
					<?php  if($user->hasPermission('price_group')) {  ?>
						<tr>
							<td>
								<a class='navPage' data-loc='price_group.php'>Price Group</a>
							</td>
						</tr>
					<?php } ?>
					<?php  if($user->hasPermission('branch_group')) {  ?>
						<tr>
							<td>
								<a class='navPage' data-loc='branch_group_pricelist.php'>Branch Group</a>
							</td>
						</tr>
					<?php } ?>

					<?php  if($user->hasPermission('freebie')) {  ?>
						<tr>
							<td>
								<a class='navPage' data-loc='item_freebie.php'>Item Freebie</a>
							</td>
						</tr>
					<?php } ?>
					<?php  if($user->hasPermission('quotation')) {  ?>
						<tr>
							<td>
								<a class='navPage' data-loc='quotation.php'>Quotation</a>
							</td>
						</tr>
					<?php } ?>
					<?php  if($user->hasPermission('item_commission')) {  ?>
						<tr>
							<td>
								<a class='navPage' data-loc='commission_item.php'>Item commission</a>
							</td>
						</tr>
					<?php } ?>
				</table>
			</div>
		</div>
	</div>

<?php } ?>
<?php if($user->hasPermission('inventory') ||  $user->hasPermission('inventory_transfer')  ||  $user->hasPermission('inventory_receive')  ||  $user->hasPermission('order_inv_m') ||  $user->hasPermission('pickup_inv') || $user->hasPermission('spare_part') || $user->hasPermission('inventory_issues') || $user->hasPermission('req_sup') || $user->hasPermission('serials')) { ?>

	<div class="panel panel-default" >
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion" href="#collapseThree"><span class="fa fa-tags">
                            </span>  Inventories</a>
			</h4>
		</div>
		<div id="collapseThree" class="panel-collapse collapse">
			<div class="panel-body">
				<table class="table">
					<?php if($user->hasPermission('inventory') ){
						?>
						<tr>
							<td>
								<a class='navPage' data-loc="inventory.php"> Manage Inventory</a>
							</td>
						</tr>

						<?php
					}?>

					<?php if($user->hasPermission('inventory_transfer')) { ?>
					<tr>
						<td>
							<a class='navPage' data-loc="transfer.php"> <?php echo TRANSFER_LABEL; ?></a>
						</td>
					</tr>
					<?php } ?>
					<?php if($user->hasPermission('inventory_receive')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="transfer_monitoring.php"> <?php echo REC_INV_LABEL; ?></a>
							</td>
						</tr>
					<?php } ?>
					<?php if($user->hasPermission('bad_order')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="bad_order.php"> Bad Order</a>
							</td>
						</tr>
					<?php } ?>
					<?php if($user->hasPermission('pickup_inv')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="pickup_mon.php"> Item Pickup</a>
							</td>
						</tr>
					<?php } ?>
					<?php if($user->hasPermission('spare_part')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="spare-parts.php"> <?php echo SPAREPART_LABEL; ?></a>
							</td>
						</tr>
					<?php } ?>
					<?php if($user->hasPermission('inventory_issues')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="inventory_issues.php">Item issues</a>
							</td>
						</tr>
					<?php } ?>
					<?php if($user->hasPermission('req_sup')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="supplies.php">Supplies</a>
							</td>
						</tr>
					<?php } ?>
					<?php if($user->hasPermission('item_swap')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="swap.php">Swapping</a>
							</td>
						</tr>
					<?php } ?>
					<?php if($user->hasPermission('inv_rep')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="warehouse_reports.php">Report</a>
							</td>
						</tr>
					<?php } ?>
					<?php if($user->hasPermission('serials') ){
						?>
						<tr>
							<td>
								<a class='navPage' data-loc="serials.php"> Serial</a>
							</td>
						</tr>

						<?php
					}?>

					<?php if($user->hasPermission('mem_equipment') ){
						?>
						<tr>
							<td>
								<a class='navPage' data-loc="member_equipment.php"> Borrowed Item</a>
							</td>
						</tr>

						<?php
					}?>
					<?php if($user->hasPermission('inv_forecast') ){
						?>
						<tr>
							<td>
								<a class='navPage' data-loc="critical_report_custom.php">Forecast</a>
							</td>
						</tr>

						<?php
					}?>
				</table>
			</div>
		</div>
	</div>

<?php  } ?>

<?php if($user->hasPermission('ship_v') || $user->hasPermission('branch') || $user->hasPermission('supplier') || $user->hasPermission('terminal') || $user->hasPermission('subcom') || $user->hasPermission('pettycash')) { ?>

	<div class="panel panel-default" >
		<div class="panel-heading" >
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion" href="#collapseFour"><span class="fa fa-map-marker">
                            </span> Branch</a>
			</h4>
		</div>
		<div id="collapseFour" class="panel-collapse collapse" >
			<div class="panel-body">
				<table class="table">
					<?php if($user->hasPermission('branch')) { ?>
					<tr>
						<td>
							<a class='navPage' data-loc="branch.php"> Manage Branch</a>
						</td>
					</tr>
					<?php } ?>
					<?php if($user->hasPermission('supplier')) { ?>
					<tr>
						<td>
							<a class='navPage' data-loc="supplier.php"> Manage Supplier</a>
						</td>
					</tr>
					<?php } ?>
					<?php if($user->hasPermission('terminal')) { ?>
					<tr>
						<td>
							<a class='navPage' data-loc="terminal.php"> Manage Terminal</a>
						</td>
					</tr>
					<?php } ?>
					<?php if($user->hasPermission('subcom')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="sub-company.php"> Manage <?php echo Configuration::getValue('sub_company'); ?></a>
							</td>
						</tr>
					<?php } ?>

					<?php if($user->hasPermission('pettycash')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="pettycash.php"> Petty Cash</a>
							</td>
						</tr>
					<?php } ?>
					<?php if($user->hasPermission('ship_v')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="shipping-company.php"> Shipping Company</a>
							</td>
						</tr>
					<?php } ?>
					<?php if($user->hasPermission('city_m')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="delivery_charges_matrix.php"> Manage Cities</a>
							</td>
						</tr>
					<?php } ?>
				</table>
			</div>
		</div>
	</div>

<?php } ?>


<?php if($user->hasPermission('member') || $user->hasPermission('subscription') || $user->hasPermission('station') || $user->hasPermission('m_char') || $user->hasPermission('m_terms')|| $user->hasPermission('m_terms_request') || $user->hasPermission('med_doctor')) { ?>

	<div class="panel panel-default">
		<div class="panel-heading" >
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion" href="#collapseNine"><span class="fa fa-users"></span>
					<?php echo MEMBER_LABEL; ?>
				</a>
			</h4>
		</div>
		<div id="collapseNine" class="panel-collapse collapse">
			<div class="panel-body">
				<table class="table">
					<?php  if(false) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="subscription.php"> <?php echo  MEMBER_LABEL; ?> Classes</a>
							</td>
						</tr>
						<tr>
							<td>
								<a class='navPage' data-loc="member_consumable.php"> <?php echo  MEMBER_LABEL; ?> Private Training</a>
							</td>
						</tr>
					<?php } ?>

					<?php  if($user->hasPermission('member')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="members.php"> <?php echo MEMBER_LABEL; ?> List</a>
							</td>
						</tr>
					<?php } ?>
					<?php  if($user->hasPermission('tblast')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="sms_module.php"> Text Blast</a>
							</td>
						</tr>
					<?php } ?>

					<?php  if($user->hasPermission('m_ref')) { ?>
							<tr>
								<td>
									<a class='navPage' data-loc="member-report.php">Referrals</a>
								</td>
							</tr>
						<tr>
							<td>
								<a class='navPage' data-loc="member-attendance-summary.php"> Attendance summary</a>
							</td>
						</tr>
					<?php  }?>
					<?php  if($user->hasPermission('m_exp')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="expi_adj.php"> Experience Adjustment</a>
							</td>
						</tr>


					<?php } ?>
					<?php  if($user->hasPermission('exp_tbl')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="expi_table.php"> Experience Table</a>
							</td>
						</tr>
					<?php } ?>

					<?php  if($user->hasPermission('wo_mod')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="workout_module.php"> Workout Module</a>
							</td>
						</tr>
						<tr>
							<td>
								<a class='navPage' data-loc="assessment_list.php"> Assessment</a>
							</td>
						</tr>
					<?php } ?>
					<?php  if($user->hasPermission('affiliate')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="affiliates.php"> Affiliate </a>
							</td>
						</tr>
					<?php } ?>
					<?php  if($user->hasPermission('station')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="station.php"> <?php echo $custom_station_name; ?></a>
							</td>
						</tr>
					<?php } ?>

					<?php  if($user->hasPermission('m_char')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="member_char.php"> Characteristics</a>
							</td>
						</tr>
					<?php } ?>
					<?php  if($user->hasPermission('m_terms_request')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="member_terms.php"> Terms</a>
							</td>
						</tr>
					<?php } ?>
					<?php  if($user->hasPermission('med_doctor')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="med_doctor.php"> Doctor</a>
							</td>
						</tr>
					<?php } ?>

					<?php  if($user->hasPermission('med_nurse')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="med_nurse.php"> Nurse</a>
							</td>
						</tr>
					<?php } ?>
					<?php  if($user->hasPermission('med_history')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="med_history.php"> History</a>
							</td>
						</tr>
					<?php } ?>

					<?php  if($user->hasPermission('e_bills_request')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="e_bills.php"> Easy Bills Pay</a>
							</td>
						</tr>
					<?php } ?>
					<?php  if($user->hasPermission('m_dues')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="monthly_due.php">Monthly Dues</a>
							</td>
						</tr>
					<?php } ?>

				</table>
			</div>
		</div>
	</div>

<?php } ?>

<?php if($user->hasPermission('user') || $user->hasPermission('position')) { ?>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion" href="#collapseSix"><span class="fa fa-user">
                            </span> Users</a>
			</h4>
		</div>
		<div id="collapseSix" class="panel-collapse collapse">
			<div class="panel-body">
				<table class="table">
					<?php if($user->hasPermission('user')) { ?>
					<tr>
						<td>
							<a class='navPage' data-loc="user.php"> Manage User</a>
						</td>
					</tr>
					<?php } ?>
					<?php if($user->hasPermission('position')) { ?>
					<tr>
						<td>
							<a class='navPage' data-loc="position.php"> Manage Position</a>
						</td>
					</tr>
					<?php } ?>
					<?php if($user->hasPermission('department_m')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="department.php"> Manage Departments</a>
							</td>
						</tr>
					<?php } ?>
				</table>
			</div>
		</div>
	</div>

<?php } ?>

<?php if($user->hasPermission('orderpoint') || $user->hasPermission('wh_request') || $user->hasPermission('item_service_r') || $user->hasPermission('item_service_s') || $user->hasPermission('item_service_p') || $user->hasPermission('item_service_l') ) { ?>
	<div class="panel panel-default" >
		<div class="panel-heading" >
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion" href="#collapseOrder"><span class="fa fa-list">
                            </span> Orders</a>
			</h4>
		</div>
		<div id="collapseOrder" class="panel-collapse collapse" >
			<div class="panel-body">
				<table class="table">
					<?php if($user->hasPermission('orderpoint'))
					{
						?>
						<tr>
							<td>
								<a class='navPage' data-loc="orderpoint.php"> Manage Order Point</a>
							</td>
						</tr>
					<?php } ?>


					<?php 
						$checkPendingOrderPoint = new Reorder_item();
						$pending_reorder_item = $checkPendingOrderPoint->countPending($user->data()->company_id);
						if($pending_reorder_item){
							$pending_reorder_item= " <span class='badge'>" . $pending_reorder_item->cnt ."</span>";
						} else {
							$pending_reorder_item = " <span class='badge'>0</span>";
						}
					?>
					<?php if($user->hasPermission('orderpoint_p')) { ?>

					<tr>
						<td>
							<a class='navPage' data-loc="to_order.php"> Critical Order </a>
						</td>
					</tr>
					<?php } ?>
					<?php if($user->hasPermission('wh_request')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="wh-order.php"> Order Item</a>
							</td>
						</tr>
					<?php } ?>
					<?php if($user->hasPermission('item_service_r') || $user->hasPermission('item_service_p') || $user->hasPermission('item_service_s')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="item-service.php">Item Service</a>
							</td>
						</tr>
					<?php } ?>
					<?php if($user->hasPermission('wh_reports')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="wh_reports.php"> Order Reports</a>
							</td>
						</tr>
						<tr>
							<td>
								<a class='navPage' data-loc="truck-report.php"> Truck Reports</a>
							</td>
						</tr>
					<?php } ?>
					<?php if($user->hasPermission('truck')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="truck.php"> Manage Trucks</a>
							</td>
						</tr>
					<?php } ?>

					<?php if($user->hasPermission('wh_order_item_summary')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="wh-by-item-summary.php"> Item Summary</a>
							</td>
						</tr>
					<?php } ?>
					<?php if($user->hasPermission('del_helper')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="delivery_helper.php"> Deliver Helper</a>
							</td>
						</tr>
					<?php } ?>
					<?php if($user->hasPermission('del_helper')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="driver.php"> Driver</a>
							</td>
						</tr>
					<?php } ?>
				</table>
			</div>
		</div>
	</div>

<?php } ?>
	<?php if($user->hasPermission('p_point') || $user->hasPermission('wallet_req')|| $user->hasPermission('ez_bills')) { ?>
		<div class="panel panel-default" >
			<div class="panel-heading" >
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion" href="#collapseEasyBills"><span class="fa fa-book">
                            </span> Easy Bills</a>
				</h4>
			</div>
			<div id="collapseEasyBills" class="panel-collapse collapse">
				<div class="panel-body">
					<table class="table">
						<?php  if($user->hasPermission('p_point')) { ?>
							<tr>
								<td>
									<a class='navPage' data-loc="my-points.php"> My Points</a>
								</td>
							</tr>
						<?php } ?>
						<?php  if($user->hasPermission('p_point_manage')) { ?>
							<tr>
								<td>
									<a class='navPage' data-loc="point.php"> Manage Points</a>
								</td>
							</tr>
						<?php } ?>

						<?php  if($user->hasPermission('ez_bills')) { ?>
							<tr>
								<td>
									<a class='navPage' data-loc="e_bills.php"> Pay Bills</a>
								</td>
							</tr>
						<?php } ?>
						<?php  if($user->hasPermission('wallet_req')) { ?>
							<tr>
								<td>
									<a class='navPage' data-loc="wallet_user.php"> My Wallet</a>
								</td>
							</tr>
						<?php } ?>

						<?php  if($user->hasPermission('wallet_manage')) { ?>
							<tr>
								<td>
									<a class='navPage' data-loc="wallet_company.php"> Manage Wallet</a>
								</td>
							</tr>
						<?php } ?>
					</table>
				</div>
			</div>
		</div>

	<?php } ?>
<?php if($user->hasPermission('caravan_request') || $user->hasPermission('caravan_manage')) { ?>
	<div class="panel panel-default" >
		<div class="panel-heading" >
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion" href="#collapseCaravan"><span class="fa fa-globe">
                            </span> Caravan</a>
			</h4>
		</div>
		<div id="collapseCaravan" class="panel-collapse collapse">
			<div class="panel-body">
				<table class="table">
					<?php  if($user->hasPermission('caravan_request')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="caravan_request.php"> Request Item</a>
							</td>
						</tr>
					<?php } ?>
					<?php 
						if($user->hasPermission('caravan_manage')){
							$checkpending = new Agent_request();
							$caravan_pending = $checkpending->countPending($user->data()->company_id);
							if($caravan_pending){
								$caravan_pending = " <span class='badge'>".$caravan_pending->cnt."</span>";
							} else {
								$caravan_pending = " <span class='badge'>0</span>";
							}
						} else {
							$caravan_pending = "";
						}
					?>
					<tr>
						<td>
							<a class='navPage' data-loc="manage_caravan.php"> Caravans<?php echo $caravan_pending; ?></a>
						</td>
					</tr>
					<?php
						if($user->hasPermission('mc_liquidate_sales') && $user->hasPermission('caravan_manage')) {
							?>
							<tr>
								<td>
									<a class='navPage' data-loc="caravan_issues.php"> Caravan Issues</a>
								</td>
							</tr>

						<?php
						}
					?>
				</table>
			</div>
		</div>
	</div>

<?php } ?>

<?php if($user->hasPermission('sales') || $user->hasPermission('order') || $user->hasPermission('createorder') || $user->hasPermission('discount') || $user->hasPermission('agent_sales')) { ?>
	<div class="panel panel-default">
		<div class="panel-heading"  >
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion" href="#collapseSeven"><span class="fa fa-ruble">
                            </span> Sales</a>
			</h4>
		</div>
		<div id="collapseSeven" class="panel-collapse collapse" >
			<div class="panel-body">
				<table class="table">
					<?php if($user->hasPermission('sales') || $user->hasPermission('cheque_monitoring') ||$user->hasPermission('credit_monitoring') ) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="sales.php"> Manage Sales</a>
							</td>
						</tr>
						<?php
							if(Configuration::allowedPermission('vit') && ($user->hasPermission('cnp') || $user->hasPermission('daina') || $user->hasPermission('mastra') || $user->hasPermission('service_sales') || $user->hasPermission('assembly_sales'))){
						?>
							<tr>
								<td>
									<a class='navPage' data-loc="custom_reports.php"> Custom Report</a>
								</td>
							</tr>
						<?php
							}
						?>
						<?php
							if($user->hasPermission('collection_tm')){
						?>
						<tr>
							<td><a class='navPage' data-loc="collection_tm.php"> Collection</a></td>
						</tr>
						<?php
							}
						?>

						<tr>
							<?php if($user->hasPermission('credit_monitoring')){
								?>
								<td>
									<a class='navPage' data-loc="member_credits.php"> Credits</a>
								</td>
								<?php
							} ?>
						</tr>
						<tr>
							<?php if($user->hasPermission('refund')){
								?>
								<td>
									<a class='navPage' data-loc="refund.php"> Refund</a>
								</td>
								<?php
							} ?>
						</tr>

							<?php if($user->hasPermission('cheque_monitoring')){
								?>
							<tr>
								<td>
									<a class='navPage' data-loc="cheque_monitoring.php"> Cheque Monitoring</a>
								</td>
							</tr>
							<tr>
								<td>
									<a class='navPage' data-loc="cash_monitoring.php"> Cash Monitoring</a>
								</td>
							</tr>
							<tr>
								<td>
									<a class='navPage' data-loc="credit_monitoring.php"> Credit Card Monitoring</a>
								</td>
							</tr>
							<tr>
							<td>
								<a class='navPage' data-loc="bank_monitoring.php"> Bank Transfer Monitoring</a>
							</td>
							</tr>


							<?php

							}?>


					<?php } ?>
					<?php if($user->hasPermission('createorder') || $user->hasPermission('order')) { ?>
					<?php 
						if ($user->hasPermission('order')){

							$checkpendingReservation = new Order();
							$reservation_pending = $checkpendingReservation->countPending($user->data()->company_id);
							if($reservation_pending){
								$reservation_pending = " <span class='badge'>".$reservation_pending->cnt."</span>";
							} else {
								$reservation_pending = " <span class='badge'>0</span>";
							} 
						} else {
							$reservation_pending = "";
						}
					?>	
						<tr style='display:none;'>
							<td>
								<a  class='navPage' data-loc="manageorder.php"> Manage Reservation <?php echo $reservation_pending; ?> </a>
							</td>
						</tr>
					<?php } ?>
					<?php if($user->hasPermission('ar')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="accounting.php"> Accounts Receivable</a>
							</td>
						</tr>
					<?php } ?>
					<?php if($user->hasPermission('cr_agent')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="cr_agent.php"> Agent CR</a>
							</td>
						</tr>
					<?php } ?>
					<?php if($user->hasPermission('discount')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="discount.php"> Discount list</a>
							</td>
						</tr>
					<?php } ?>
					<?php if($user->hasPermission('agent_sales')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="by-agent.php"> Agent sales</a>
							</td>
						</tr>
					<?php } ?>
					<?php if($user->hasPermission('reports')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="reports2.php"> Reports</a>
							</td>
						</tr>
					<?php } ?>
					<?php if($user->hasPermission('deduction_type')) { ?>
					<tr>
						<td>
							<a class='navPage' data-loc="deduction_list.php"> Deduction type</a>
						</td>
					</tr>
					<?php } ?>

					<?php if($user->hasPermission('deductions')) { ?>
					<tr>
						<td>
							<a class='navPage' data-loc="deductions.php"> Deduction list</a>
						</td>
					</tr>
					<?php } ?>
					<?php  if($user->hasPermission('m_terms_request')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="deposits.php"> Deposits</a>
							</td>
						</tr>
					<?php } ?>

					<?php  if($_SERVER['HTTP_HOST'] == 'demo.apollosystems.com.ph') { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="upload_sales.php"> Upload Sales</a>
							</td>
						</tr>
					<?php } ?>
				</table>
			</div>
		</div>
	</div>

<?php } ?>
	<?php if($user->hasPermission('sms_num') || $user->hasPermission('sms_log')) { ?>
		<div class="panel panel-default">
			<div class="panel-heading"  >
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion" href="#collapseSMS"><span class="fa fa-mobile">
                            </span> SMS</a>
				</h4>
			</div>
			<div id="collapseSMS" class="panel-collapse collapse" >
				<div class="panel-body">
					<table class="table">
						<?php if($user->hasPermission('sms_num')) { ?>
							<tr>
								<td>
									<a class='navPage' data-loc="sms_mobile.php"> Sms Number</a>
								</td>
							</tr>
							<?php
						} ?>
							<tr>
								<?php if($user->hasPermission('sms_log')){
									?>
									<td>
										<a class='navPage' data-loc="sms_log.php"> Log</a>
									</td>
									<?php
								} ?>
							</tr>
						<?php if($user->hasPermission('sms_num')) { ?>
							<tr>
								<td>
									<a class='navPage' data-loc="senior.php"> Senior Discount</a>
								</td>
							</tr>
							<?php
						} ?>
						<?php if($user->hasPermission('sms_num')) { ?>
							<tr>
								<td>
									<a class='navPage' data-loc="dicer_bo.php"> Bad order</a>
								</td>
							</tr>
							<?php
						} ?>
						<?php if($user->hasPermission('sms_num')) { ?>
							<tr>
								<td>
									<a class='navPage' data-loc="sms_report.php"> Summary</a>
								</td>
							</tr>
							<?php
						} ?>
						<tr>
							<?php if($user->hasPermission('sms_log')){
								?>
								<td>
									<a class='navPage' data-loc="dicer_deposit.php"> Dicer deposits</a>
								</td>
								<?php
							} ?>
						</tr>
						<tr>
							<?php if($user->hasPermission('sms_log')){
								?>
								<td>
									<a class='navPage' data-loc="sms_ro_report.php"> Dicer Received Order</a>
								</td>
								<?php
							} ?>
						</tr>
						<tr>
							<?php if($user->hasPermission('sms_log')){
								?>
								<td>
									<a class='navPage' data-loc="sms_no_report.php"> No Report Monitoring</a>
								</td>
								<?php
							} ?>

						</tr>
						<tr>
							<?php if($user->hasPermission('sms_log')){
								?>
								<td>
									<a class='navPage' data-loc="report-category.php"> Reports</a>
								</td>
								<?php
							} ?>

						</tr>
					</table>
				</div>
			</div>
		</div>

	<?php } ?>

<?php if($user->hasPermission('settings') || $user->hasPermission('inbox')) { ?>

	<div class="panel panel-default">
		<div class="panel-heading" >
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion" href="#collapseEight"> <span class='fa fa-wrench'></span> Settings</a>
			</h4>
		</div>
		<div id="collapseEight" class="panel-collapse collapse">
			<div class="panel-body">
				<table class="table">
					<!-- <tr>
						<td>
							<a href="addcompany.php"> Add Company</a>
						</td>
					</tr> -->

					<?php if($user->hasPermission('config')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="company_info.php"> Company Info</a>
							</td>
						</tr>
						<tr>
							<td>
								<a class='navPage' data-loc="config.php">Configurations</a>
							</td>
						</tr>
					<?php } ?>
					<?php if($user->hasPermission('inbox')) { ?>
					<tr>
						<td>
							<a class='navPage' data-loc="contact-us.php"> Inbox</a>
						</td>
					</tr>
					<?php } ?>
					<?php if($user->hasPermission('p_point_manage')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="point.php">Points</a>
							</td>
						</tr>
					<?php } ?>
					<?php if($user->hasPermission('themes')) { ?>
					<tr>
						<td>
							<a class='navPage' data-loc="style_config.php"> Themes</a>
						</td>
					</tr>
					<?php } ?>
					<?php if($user->hasPermission('station_settings')) { ?>
					<tr>
						<td>
							<a class='navPage' data-loc="station-settings.php"> Station settings</a>
						</td>
					</tr>
					<?php } ?>
					<?php if($user->hasPermission('station_settings')) { ?>
						<tr>>
							<td>
								<a class='navPage' data-loc="member-settings.php"> Member settings</a>
							</td>
						</tr>
					<?php } ?>
					<?php if($user->hasPermission('supplier_settings')) { ?>
					<tr>
						<td>
							<a class='navPage' data-loc="supplier-settings.php"> Supplier settings</a>
						</td>
					</tr>
					<?php } ?>
					<?php if($user->hasPermission('recycle')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="recycle_bin.php">Recycle bin</a>
							</td>
						</tr>
					<?php } ?>
					<?php if($user->hasPermission('consumable_admin')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="consumable-admin.php">Consumable</a>
							</td>
						</tr>
					<?php } ?>
					<?php if($user->hasPermission('consumablefree_admin')) { ?>
						<tr>
							<td>
								<a class='navPage' data-loc="consumablefree-admin.php">Freebies Admin</a>
							</td>
						</tr>
						<tr>
							<td>
								<a class='navPage' data-loc="upload_utilities.php">Upload utilities</a>
							</td>
						</tr>
					<?php } ?>

				</table>
			</div>
		</div>
	</div>

<?php } ?>

</div>
</div>

</div>
