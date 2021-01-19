<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';

	if(!$user->hasPermission('wh_request')) {
		// redirect to denied page
		Redirect::to(1);
	}

	$reserve_only = 0;

	if($user->hasPermission('wh_res')) {
		$reserve_only = 1;
	}

	$crud = new Crud();

	$branches = $crud->get_active('branches',array('company_id','=',$user->data()->company_id));
	$shipping_companies = $crud->get_active('shipping_companies',array('company_id','=',$user->data()->company_id));
	$auth_approval = new Approval_auth();
	$my_auth = $auth_approval->getMyAuth($user->data()->id);

	if(isset($my_auth->ref_values) && !empty($my_auth->ref_values)){
		$my_auth = $my_auth->ref_values;
	} else {
		$my_auth = 0;
	}
	$addtl_discount = (Configuration::getValue('addtl_disc')) ? Configuration::getValue('addtl_disc') : 0;

	function getOptionShipping($shipping_companies){
		if($shipping_companies){
			foreach($shipping_companies as $b){
					?>
					<option value="<?php echo $b->id; ?>"><?php echo $b->name; ?></option>
					<?php
			}
		}
	}

	function getOptionMembers($members){
		if($members){
			foreach($members as $mem){
				?>
				<option value="<?php echo $mem->id; ?>"><?php echo ucwords($mem->lastname . ", ". $mem->firstname); ?></option>
				<?php
			}
		}
	}

	$dontshowprice = Configuration::getValue('branch_show_price');
	$order_branch_to = Configuration::getValue('client_order');
	if($dontshowprice){
		if(strpos($dontshowprice,',') > 0){
			$dontshowprice = explode(',',$dontshowprice);
		} else {
			$dontshowprice = [];
			$dontshowprice[]	= $dontshowprice;
		}
	} else {
		$dontshowprice = [];
	}

	if($user->data()->member_id != 0){
		$branchesmem = $crud->get_active('branches',array('member_id','=',$user->data()->member_id));
	} else {
		$branchesmem = $branches;
	}

	function getFinalAmount($n=0){
		if($n && is_numeric($n)){
			$whole = floor($n);
			$fraction = number_format($n - $whole,2);
			return ['whole'=>$whole,'decimal'=>$fraction];
		}
	}

	$has_update_details = $user->hasPermission('wh_update_details');

	$has_consumable_check = false;

	$client_has_branch = false;

	$http_host = $_SERVER['HTTP_HOST'];



	$company_name = Configuration::companyName();

	if($http_host == 'pw.apollosystems.com.ph' || $http_host == 'dev.apollo.ph:81' || $http_host == 'kababayan.apollosystems.com.ph' || $http_host == '10.168.1.34:81'){ //
		$client_has_branch = true;
	}

	if($http_host == 'aquabest.apollosystems.com.ph' || $http_host == 'dev.apollo.ph:81'){
		$has_consumable_check = true;
	}


	if(!$client_has_branch){
		$toggle_validation_member = "v-show='request.branch_id_to == 0'";
		$toggle_validation_branch_to = "v-show='request.member_id == 0'";
	}

	if(Configuration::getValue('order_skip_reserve') == 1){
		$hide_reserve = "v-show='false'";
	} else {
		$hide_reserve ='';
	}

	if(Configuration::getValue('walkin_app') == 1){
		$show_walkin_app = "";
	} else {
		$show_walkin_app ="v-show='false'";
	}

	if(Configuration::getValue('adjustment_default') == 2){
		$adjustment_default = 2;
	} else {
		$adjustment_default =1;
	}

	$cashier_transaction = false;
	if(Configuration::getValue('c_trans') == 1){
		$cashier_transaction = true;
	}

	$cashier_helper = 0;
	if($user->hasPermission('c_helper')){
		$cashier_helper = 1;
	}

	$order_for_all_clients = 0;
	if(Configuration::allowedPermission('item_post') || $user->hasPermission('wh_all_member')){
		$order_for_all_clients = 1;
	}

//	if(!$user->hasPermission('wh_agent') && !$user->hasPermission('wh_member')){
//		$order_for_all_clients = 1;
//	}

	$different_unit = 0;
	if(Configuration::getValue('d_unit') == 1){
		$different_unit = 1;
	}

	$charge_label  = 0;
	if(Configuration::getValue('charge_label') == 1){
		$charge_label = 1;
	}

	$surplus_rack = 0;

	if(Configuration::getValue('surplus_rack') == 1){
		$surplus_rack = 1;
	}

	$salestype = new Sales_type();
	$salestypes = $salestype->getSalesType();

?>
	<link rel="stylesheet" href="../css/swipebox.css">
	<input type="hidden" id='SURPLUS_RACK' value='<?php echo $surplus_rack;?>'>
	<input type="hidden" id='MEMBER_LABEL' value='<?php echo MEMBER_LABEL;?>'>
	<input type="hidden" id='INVOICE_PREFIX' value='<?php echo INVOICE_PREFIX;?>'>
	<input type="hidden" id='DR_PREFIX' value='<?php echo DR_PREFIX;?>'>
	<input type="hidden" id='PR_PREFIX' value='<?php echo PR_PREFIX;?>'>
	<input type="hidden" id='INVOICE_LABEL' value='<?php echo INVOICE_LABEL;?>'>
	<input type="hidden" id='DR_LABEL' value='<?php echo DR_LABEL;?>'>
	<input type="hidden" id='PR_LABEL' value='<?php echo PR_LABEL;?>'>
	<input type="hidden" id='APPROVAL_AUTH' value='<?php echo $my_auth;?>'>
	<input type="hidden" id='HAS_SV' value='<?php echo (Configuration::getValue('has_sv')) ? Configuration::getValue('has_sv') : 0 ;?>'>
	<input type="hidden" id='CONS_ROUND' value='<?php echo Configuration::getValue('mem_adj_round');?>'>
	<input type="hidden" id='ORDER_LIMIT' value='<?php echo Configuration::getValue('order_limit');?>'>
	<input type="hidden" id='PENDING_MEMBER' value='<?php echo Configuration::getValue('order_pending_member');?>'>
	<input type="hidden" id='ADJUSTMENT_DEFAULT' value='<?php echo $adjustment_default; ?>'>
	<input type="hidden" id='RESERVE_ONLY' value='<?php echo $reserve_only; ?>'>
	<input type="hidden" id='ADDTL_VIEW' value='<?php echo ($addtl_discount != 1) ? '0' : '1'; ?>'>
	<input type="hidden" id='CASHIER_HELPER' value='<?php echo $cashier_helper; ?>'>
	<input type="hidden" id='ORDER_FOR_ALL' value='<?php echo $order_for_all_clients; ?>'>
	<input type="hidden" id='DIFFERENT_UNIT' value='<?php echo $different_unit; ?>'>
	<input type="hidden" id='config_company_name' value='<?php echo $company_name; ?>'>
	<input type="hidden" id='CHARGE_LABEL' value='<?php echo $charge_label; ?>'>
	<input type="hidden" id='AUTO_MEMBER_CREDIT' value='<?php echo (Configuration::getValue('auto_member_credit')) ? Configuration::getValue('auto_member_credit') : 0 ;?>'>
	<!-- Page content -->

	<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<div class="row">
				<div class="col-md-6">
					<h1>
						<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Orders
					</h1>
				</div>
				<div class="col-md-6 text-right">
					<?php if(Configuration::thisCompany('avision')) { ?>
						<a  class='btn btn-primary btn-sm' href="upload_avision.php" v-show="current_is_member != 1">Import Sales</a>
						<a  class='btn btn-primary btn-sm' href="batch_view.php" v-show="current_is_member != 1">Batch View</a>
						<a  class='btn btn-primary btn-sm' href="avision_rebate_report.php">Rebate Report</a>
					<?php } ?>
					<?php if(Configuration::getValue('service_main_option') == 1) { ?>
						<a  class='btn btn-primary btn-sm' href="service_notif.php" v-show="current_is_member != 1">Pending At Service</a>
					<?php } ?>
					<a  class='btn btn-primary btn-sm' href="item-reserved.php" v-show="current_is_member != 1">Pending Items</a>
				</div>
			</div>
		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}
		?>
		<div id='WarehouseController'>
			<input type="hidden" value="<?php echo $user->data()->is_member; ?>" id='is_member' v-model='is_member'>
			<input type="hidden" value="<?php echo $user->data()->member_id; ?>" id='user_member_id'>
			<input type="hidden" value="<?php echo $user->data()->id; ?>" id='CURRENT_USER_ID'>
			<input type="hidden" value="<?php echo $user->data()->lastname; ?>" id='user_fullname'>
			<input type="hidden" value="<?php echo $thiscompany->name; ?>" id='co_name'>
			<input type="hidden" value="<?php echo $thiscompany->description; ?>" id='co_desc'>
			<input type="hidden" value="<?php echo  $thiscompany->address; ?>" id='co_address'>
			<input type="hidden" value="<?php echo  $http_host; ?>" id='_HOST'>
			<input type="hidden" value="<?php echo  (Configuration::getValue('form_style') && Configuration::getValue('form_style') == 1) ? 1 : 0; ?>" id='with_form_style'>
			<input type="hidden" value="<?php echo  (Configuration::getValue('order_reservation_attachment') && Configuration::getValue('order_reservation_attachment') == 1) ? 1 : 0; ?>" id='order_has_attachment'>
			<div class="row">
				<div class="col-md-12">
					<?php include 'includes/wh_nav.php'; ?>
					<div class="panel panel-primary">
						<!-- Default panel contents -->
						<div class="panel-heading">Warehouse <?php echo date('m/d/Y H:i:s A')?></div>
						<div class="panel-body">
							<div v-show='container.requestView'>
								<div class="row">
									<div class="col-md-6">
										<button style='display:none;' @click="showPendingCredit" class='btn btn-danger' v-show="current_credit_list.length > 0">Member has unpaid transaction</button>
										<h3>Request Order</h3>
									</div>
									<div class="col-md-6 text-right ">
										Re-user Order <input type="text" class='txt-oval' placeholder='Enter order ID' v-model='order_id_to_use'>
										<button id='btnUserOrder' @click="btnUserOrder" class='btn btn-default btn-sm'>Use</button>
									</div>
								</div>
								<!-- 	<li v-show='!validation.member_id'>Client is required</li> -->
								<div class="alert alert-danger" v-show="!isValid">
									<ul>
										<li v-show='!validation.branch_id'>Branch is required</li>
										<li v-show='!validation.item_id'>Item is required</li>
										<li v-show='!validation.qty'>Quantity is required and must be a number</li>
										<li v-show='!validation.for_pickup'>Enter order type</li>
										<li v-show='!validation.is_reserve'>Enter request type</li>
									</ul>
								</div>

								<div class="alert alert-success" v-show='isSuccess'>
									Item added in cart.
								</div>
								<div class='well' v-show="request.member_id">
									<div class="row">
										<div class="col-md-6">Address: <strong class='text-danger'>{{member_info.personal_address}}</strong></div>
										<div class="col-md-3">Region: <strong class='text-danger'>{{member_info.region}}</strong></div>

										<div class="col-md-3">Credit limit: <strong class='text-danger'>{{member_info.credit_limit}}</strong></div>
									</div>
									<div class="row">
										<div class="col-md-6">Contact: <strong class='text-danger'>{{member_info.contact_number}}</strong></div>
										<div class="col-md-3">Terms: <strong class='text-danger'>{{member_info.terms}}</strong></div>
										<div class="col-md-3"></div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-3" <?php echo $toggle_validation_member; ?>>
										<div class="form-group">
											<input  name="member_id" id="member_id" v-model="request.member_id" class='form-control'>
											<span class='help-block'>Name of the <?php echo MEMBER_LABEL; ?></span>
											<div class='alert alert-danger' v-show="is_hold == 1">
												<strong>You can't create request because this client is currently on hold. Please ask accounting department for assistance.</strong>
											</div>
										</div>
									</div>
									<?php if(($order_branch_to == 1 && $user->hasPermission('inventory_transfer')) || $user->data()->member_id){
										?>
										<div class="col-md-3" <?php echo $toggle_validation_branch_to; ?>>
											<div class="form-group">
												<select  name="branch_id_to" id="branch_id_to" v-model="request.branch_id_to" class='form-control'>
													<option value=""></option>
													<?php
														if($branchesmem){
															$bmfirst = true;
															foreach($branchesmem as $b){
																//if($b->id == $user->data()->branch_id) continue;
																$bmselected ='';
																if($bmfirst){
																	$bmfirst = false;
																	//$bmselected='selected';
																}
																?>
																<option <?php echo $bmselected; ?> value="<?php echo $b->id; ?>"><?php echo $b->name; ?></option>
																<?php
															}
														}
													?>
												</select>
												<span class='help-block'>Destination branch</span>
											</div>
										</div>
										<?php
										}
									?>
									<div class="col-md-3">

										<div class="form-group">

											<select name="branch_id" id="branch_id" v-model="request.branch_id" class='form-control'>
												<option value=""></option>
												<?php
													if($branches){
														foreach($branches as $b){
															if($b->id == $user->data()->branch_id){
																$bselected="selected";
															} else {
																$bselected="";
															}
															?>
															<option <?php echo $bselected; ?> value="<?php echo $b->id; ?>"><?php echo $b->name; ?></option>
															<?php
														}
													}
												?>
											</select>
											<span class='help-block'>Order from what branch</span>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<input type="text" v-model="request.remarks" class="form-control" placeholder='Remarks'>
											<span class='help-block'>Additional Information</span>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-3">
										<div class="form-group">
											<select name="shipping_company_id" id="shipping_company_id" v-model="request.shipping_company_id" class='form-control'>
												<option value=""></option>
												<?php  getOptionShipping($shipping_companies); ?>
											</select>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<input type="text" v-model="request.client_po" class="form-control" placeholder='PO number'>
										</div>
									</div>
									<?php
										if(Configuration::getValue('order_has_station') == 1){
											?>
											<div class="col-md-3">
												<div class="form-group">
													<select class='form-control' name="station_id" id="station_id" v-model="request.station_id">
														<option value='0'>No Station</option>
													</select>
													<span class='help-block'>Member station</span>
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<select class='form-control' name="gen_sales_type" id="gen_sales_type" v-model="request.gen_sales_type">
														<?php

															echo "<option value='0'>Choose General sales type</option>";
															foreach($salestypes as $get_type){
																echo "<option value='{$get_type->id}'>$get_type->name</option>";
															}
														?>
													</select>
													<span class='help-block'>General Sales Type</span>
												</div>
											</div>
											<?php
										}
									?>
									<?php
										if(Configuration::getValue('price_group') == 1 && $user->hasPermission('price_group_flex')){
											?>
											<div class="col-md-3">
												<div class="form-group">
													<select  class='form-control' name="price_group_id" id="price_group_id" v-model="request.price_group_id">
														<?php
															$price_group_cls = new Price_group();
															$price_groups = $price_group_cls->get_active('price_groups',[1,'=',1]);

															echo "<option value='0'>Pick up Price</option>";
															foreach($price_groups as $price_group){
																echo "<option value='{$price_group->id}'>$price_group->name</option>";
															}
														?>
													</select>
													<span class='help-block'>Member group</span>
												</div>
											</div>
											<?php
										}
									?>

								</div>

								<div class="row">

									<div class="col-md-5">
										<div class="form-group">
											<input type="radio" id="fp0" value="0" v-model="request.for_pickup" >
											<label for="fp0">For Deliver</label>
											<input type="radio" id="fp1" value="1" v-model="request.for_pickup">
											<label for="fp1">For Pick up</label>
											<?php if($cashier_transaction) {
												?>
												<input type="radio" id="fp2" value="2" v-model="request.for_pickup">
												<label for="fp2">Cashier Transaction</label>
												<?php
											}?>


											<span class='help-block'>Order Type</span>
										</div>
									</div>
									<div class="col-md-5">
										<div class="form-group">
											<input type="radio" id="fr0" value="0" v-model="request.is_reserve">
											<label for="fr0">For Order</label>
											<input type="radio" id="fr1" value="1" v-model="request.is_reserve">
											<label for="fr1">For Reservation</label>
											<?php
												if(Configuration::getValue('walkin_app') == 1){
													?>
													<input type="radio" id="fr2" value="2" v-model="request.is_reserve">
													<label for="fr2">For Walk In Approval</label>
													<?php
												}
											?>
											<span class='help-block'>Request Type</span>
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group">
											<?php
												if(Configuration::getValue('service_main_option') == 1){
													?>
											<input type="checkbox" id="chkFromService" >
											<label for="chkFromService">From Service</label>
											<span class='help-block'>Is Service Item?</span>
													<?php
												}
											?>
										</div>
										<div class="form-group">
											<?php
												if(Configuration::getValue('service_main_option') == 1){
													?>
													<input type="checkbox" id="chkFromNotif" >
													<label for="chkFromNotif">For Notif</label>
													<span class='help-block'>Service Notification</span>
													<?php
												}
											?>
										</div>
									</div>

								</div>
								<?php
									if(Configuration::getValue('order_reservation_attachment') == 1){
									?>
									<div class="row">
										<div class="col-md-3"  v-show="request.is_reserve == 1">
											<div class="form-group">
												<input type="file" class='form-control' name='requestAttachment' id='requestAttachment'>
												<span class='help-block'>Upload reservation attachment</span>
											</div>
										</div>
									</div>
								<?php
								 }
								?>

										<?php
											if(Configuration::getValue('spec_stats') == 1){
										?>
												<div class="row">
												<div class="col-md-3">
													<div class="form-group">
														<select class='form-control' name="spec_station_id" v-model="request.spec_station_id" id="spec_station_id">
															<option value='0'>No Station</option>
														</select>
													</div>
												</div>
												<div class="col-md-3">
													<div class="form-group">
														<select class='form-control' name="spec_sales_type" id="spec_sales_type" v-model="request.spec_sales_type">
															<?php
																echo "<option value='0'>Choose Specific sales type</option>";
																foreach($salestypes as $get_type){

																	echo "<option value='{$get_type->id}'>$get_type->name</option>";
																}
															?>
														</select>
													</div>
												</div>
												</div>
										<?php } ?>
								<div class="row">
									<div class="col-md-3">
										<div class="form-group">
											<input name="item_id" id="item_id" v-model='request.item_id' class='selectitem'>
										</div>
									</div>

									<?php
										if($different_unit == 1){
											?>
											<div class="col-md-3" v-show="multiplier_qty.length">
												<div class="form-group">
													<select name="dif_qty" id="dif_qty" v-model="dif_qty" class='form-control'>
														<option v-for="qtys in multiplier_qty" v-bind:value="qtys.qty">{{qtys.unit_name}}</option>
													</select>
												</div>
											</div>
											<?php
										}
									?>
									<div class="col-md-3">
										<div class="form-group">
											<input type="text" v-model="request.qty" class="form-control" placeholder='Quantity'>
										</div>
									</div>
									<?php
										if($surplus_rack == 1){
											?>
											<div class="col-md-3" v-show="surplus_allowed">
												<div class="form-group">
													<select class='form-control' name="get_from_surplus" id="get_from_surplus" v-model='request.is_surplus'>
														<option value="0">Don't Get From Surplus</option>
														<option value="1">Get From Surplus</option>
													</select>
												</div>
											</div>
											<?php
										}
									?>

									<div class='col-md-3' v-show="ADDTL_VIEW == 1 || request.is_surplus == 1" >
										<div class='form-group'>
											<input type="text" v-model="request.addtl_disc" class="form-control" placeholder='Discount'>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<button id='btnAdd' v-show="is_hold == 0" :disabled="!isValid" class='btn btn-primary hvr-underline-from-left' v-on:click="appendItem">Add Item</button>
										</div>
									</div>
								</div>

								<!-- Item to order-->

								<p class='text-success'>Item in Cart  <strong>{{ cart_item_ctr }}</strong></p>
								<table class="table table-bordered">
									<thead>
									<tr>
										<th>Item</th>
										<th>Order Qty</th>
										<th>Available</th>
										<th>Price</th>
										<th>Total</th>
										<th></th>
									</tr>
									</thead>
									<tbody>
									<tr class='item_row' v-for="item in items" v-bind:class="{'bg-danger' : item.is_surplus == '1'}">
										<td>
											<div class="row">
												<div class="col-md-2 text-center">
													<div style='margin:0 auto;height:50px;width:50px;'>
														<img  class='opennewtabimage' style='max-width:100%;max-height:100%;' src="../item_images/{{item.item_id}}.jpg" alt="No Image">
													</div>
												</div>
												<div class="col-md-10">
													<strong v-html="item.item_code"></strong>
												</div>
											</div>
										</td>
										<td>
											{{ item.qty }} <sub><small>Pc(s)</small></sub>
											<span v-show="item.qty != item.orig_qty" class='text-danger span-block'>{{item.orig_qty}} <sub><small>{{item.preferred_unit}}</small></sub></span>
										</td>
										<td>
											{{ item.remaining }}
										</td>
										<td>
											{{ item.price }}
											 <select v-model='item.group_adjustment_selected' v-show="item.group_adjustment.length">
												<option value="">Choose Adjustment</option>
												<option v-bind:value="grp_adj.adjustment" v-for="grp_adj in item.group_adjustment">
													{{ grp_adj.name}}
												</option>
												</select>
										</td>
										<td>{{ item.total }}
											<span style='display:block'>
												{{ (item.adjustmentmem  != 0 ? '(' + item.adjustmentmem + ')' : '') }}
											</span>
											<span style='display:block' v-show="item.group_adjustment_selected">
												({{item.group_adjustment_selected * item.qty}})
												<br>
												{{ parseFloat(item.orig_total) + parseFloat(item.group_adjustment_selected * item.qty) }}
											</span>
										</td>
										<td>
											<button class='btn btn-danger btn-sm' v-on:click="removeItem(item)">Remove</button>
											<button v-show="item.is_bundle == 1" class='btn btn-primary btn-sm' v-on:click="bundleDetails(item)">Bundle</button>
										</td>
									</tr>
									</tbody>
								</table>
								<div>

									<?php
										if(Configuration::thisCompany('cebuhiq')){
											?>
											<p v-show='items.length'>Gross: <strong>{{ grossAmount }}</strong></p>
											<p v-show='items.length'>Disc/Adj: <strong>{{ totalAdjustment }}</strong></p>
											<?php
										}
									?>
									<p v-show='items.length'>Total: <strong>{{ totalAmount }}</strong></p>
								</div>

								<div class="alert alert-info" v-show='!items.length'>
									No item yet.
								</div>
								<div>
									<button class='btn btn-success' :disabled="!items.length" v-on:click="submitItem" v-show="is_hold == 0"><span>Submit</span></button> <button class='btn btn-danger' :disabled="!items.length" v-on:click="removeAll">Remove All</button>
								</div>
							</div>
							<div v-show='container.approvalView'>
								<div class="row" style='margin-bottom:5px;'>
									<div class="col-md-2">
										<button id='btnOverPayment' v-show="current_is_member != 1" class='btn btn-default'>Add Deposit</button>
									</div>
									<div class="col-md-2">
										<select  v-model="assemble_filter" class='form-control' v-on:change="fetchedOrder(1,1)">
											<option value="">Select Item type</option>
											<option value="1"><?php echo SPAREPART_LABEL; ?></option>
											<option value="2">Set</option>
										</select>
									</div>
									<div class="col-md-2">
										<select  v-model="for_pickup_filter" class='form-control' v-on:change="fetchedOrder(1,1)">
											<option value="">Select Order type</option>
											<option value="1">For Pick up</option>
											<option value="2">Deliveries</option>


											<?php if($cashier_transaction) {
												?>
												<option value="4">For Pick up and Deliveries</option>
												<option value="3">Cashier Transaction</option>

												<?php
											}?>
										</select>

									</div>
									<div class="col-md-2">
										<select  v-model="branch_id_filter" class='form-control' v-on:change="fetchedOrder(1,1)">
											<option value="">Filter Branch</option>
											<?php
												if($branchesmem){
													$bmfirst = true;
													foreach($branchesmem as $b){
														//if($b->id == $user->data()->branch_id) continue;
														$bmselected ='';
														if($bmfirst){
															$bmfirst = false;
															//$bmselected='selected';
														}
														?>
														<option <?php echo $bmselected; ?> value="<?php echo $b->id; ?>"><?php echo $b->name; ?></option>
														<?php
													}
												}
											?>
										</select>
									</div>
									<div class="col-md-2">
										<div class="form-group">
											<select class='form-control' name="salestype_filter" id="salestype_filter" v-model='salestype_filter' v-on:change="fetchedOrder(1,1)">
												<option value="">Filter Sales Type</option>
											<?php
												foreach($salestypes as $get_type){
													echo "<option value='{$get_type->id}'>$get_type->name</option>";
												}
											?>
											</select>
										</div>
									</div>
								</div>



									<!-- Nav tabs -->
									<ul class="nav nav-tabs" role="tablist">
										<li role="presentation" class="active"><a href="#tab_for_approval" aria-controls="home" role="tab" data-toggle="tab">Ready for approval <span class='badge'>{{pending_for_approval.length }}</span></a></li>
										<li role="presentation" ><a href="#tab_for_reserve" aria-controls="profile" role="tab" data-toggle="tab">Reserved <span class='badge'>{{pending_for_approval_reserved.length }}</span></a></li>
										<li role="presentation" <?php echo $hide_reserve; ?>><a href="#tab_for_reserve_pending" aria-controls="profile" role="tab" data-toggle="tab">Pending Reserve <span class='badge'>{{pending_for_approval_reserved_pending.length }}</span></a></li>
										<li role="presentation" <?php echo $show_walkin_app; ?>><a href="#tab_for_walkin_app" aria-controls="profile" role="tab" data-toggle="tab">Walk In Approval <span class='badge'>{{pending_for_approval_walkin.length }}</span></a></li>
									</ul>

									<!-- Tab panes -->
									<div class="tab-content" style='margin-top:10px;'>
										<div role="tabpanel" class="tab-pane active" id="tab_for_approval">
											<div v-show="cur_terminal_id != 0" class='hide-without-terminal'>
												<div class="row" id='conOverride'>

													<div class="col-md-12">
														<div class="row">

															<div class="col-md-2">
																<input type="text" id='custom_date' class='form-control' placeholder='Override Date'>
																<span class="help-block">Override date</span>
															</div>
															<div class="col-md-2">
																<input type="text" id='custom_invoice' class='form-control' v-model='invoice' placeholder='Override <?php echo INVOICE_LABEL; ?>'>
																<span class="help-block">Override <?php echo INVOICE_LABEL; ?></span>
															</div>
															<div class="col-md-2">
																<input type="text" id='custom_dr' class='form-control' v-model='dr' placeholder='Override <?php echo DR_LABEL; ?>'>
																<span class="help-block">Override <?php echo DR_LABEL; ?></span>
															</div>
															<div class="col-md-2">
																<input type="text" id='custom_pr' class='form-control' v-model='pr' placeholder='Override <?php echo PR_LABEL; ?>'>
																<span class="help-block">Override <?php echo PR_LABEL; ?></span>
															</div>
															<?php
																if(Configuration::getValue('has_sv') == 1){
																	?>
																	<div class="col-md-2">
																		<input type="text" id='custom_sv' class='form-control' v-model='sv' placeholder='Override SV'>
																		<span class="help-block">Override SV</span>
																	</div>
																	<?php
																}
															?>
															<?php
																if(Configuration::getValue('has_sr') == 1){
																	?>
																	<div class="col-md-2">
																		<input type="text" id='custom_sr' class='form-control' v-model='sr' placeholder='Override SR'>
																		<span class="help-block">Override SR</span>
																	</div>
																	<?php
																}
															?>

															<?php
																if(Configuration::getValue('has_ts') == 1){
																	?>
																	<div class="col-md-2">
																		<input type="text" id='custom_ts' class='form-control' v-model='ts' placeholder='Override TS'>
																		<span class="help-block">Override TS</span>
																	</div>
																	<?php
																}
															?>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-md-6">

													</div>
													<div class="col-md-6 text-right">
														<p v-show="current_is_member != 1">

															<input type="checkbox" id="printWithPrice"  v-model="printWithPrice">
															<label for="printWithPrice">Hide price on print</label>

															<span>Next <?php echo INVOICE_LABEL; ?>: </span>  <strong> {{  (invoice) ? invoice : 'No terminal set up.' }} </strong>
															<span>Next <?php echo DR_LABEL; ?>: </span>  <strong> {{ (dr) ? dr : 'No terminal set up.' }} </strong>
															<span>Next <?php echo PR_LABEL; ?>: </span>  <strong> {{ (pr) ? pr : 'No terminal set up.' }} </strong>
															<?php
																if(Configuration::getValue('has_sv') == 1){
																	?>
																	<span>Next SV: </span>  <strong> {{ (sv) ? sv : 'No terminal set up.' }} </strong>
																	<?php
																}
															?>
														</p>
													</div>
												</div>
											</div>

											<div v-show="pending_for_approval.length">
												<?php if($user->hasPermission('wh_approval_v')){
													?>
													<button class='btn btn-primary' @click="showBatch">Batch Process</button>
													<?php
												}?>
												<div class="nav_order"></div>
												<table id='tblForApproval' class='table'>
													<thead>
													<tr>
														<th>Id</th>
														<th>Branch</th>
														<th>Created By</th>
														<th>To</th>
														<th>Ordered Date</th>
														<th class='text-right'>Total</th>
														<th>Remarks</th>
														<th><?php echo INVOICE_LABEL; ?></th>
														<th><?php echo DR_LABEL; ?></th>
														<th><?php echo PR_LABEL; ?></th>
														<?php if(Configuration::getValue('has_sv') == 1){
															?>
															<th>SV</th>
															<?php
														}?>
														<?php if(Configuration::getValue('has_sr') == 1){
															?>
															<th>SR</th>
															<?php
														}?>
														<?php if(Configuration::getValue('has_ts') == 1){
															?>
															<th>TS</th>
															<?php
														}?>
														<th>Details</th>
													</tr>
													</thead>
													<tbody>
													<tr track-by="id" v-bind:class="[order.is_priority == 1 ? 'bg-danger' : '', order.from_service != 0 ? 'bg-warning' : '']"  data-total="{{ order.total_price }}" data-id="{{ order.id }}" v-for="order in pending_for_approval">
														<td class='text-danger'>
															<i v-show='order.has_assemble_item == 1' class='fa fa-wrench'></i> <strong>{{ order.id }}</strong>
															<span  v-show='order.is_for_pickup == 2'><i class='fa fa-money fa-2x'></i>  </span>

															<input class='span-block chkBatch' v-bind:data-id="order.id" type="checkbox" v-show='order.payment_id != 0 && (order.invoice != 0 || order.dr != 0 || order.pr != 0)'>
														</td>
														<td>{{ order.branch_name }}</td>
														<td>{{ order.fullnameUser }} <small class='text-danger span-block'>{{ order.sales_type_name}}</small></td>
														<td><span v-html="order.fullname"></span>
															<small class='text-danger span-block'>{{ order.personal_address}}</small>
															<small class='span-block'> {{ order.shipping_name}}</small>
															<small class='span-block text-muted'>{{ order.client_po}}</small>
															<small class='span-block text-danger'>{{ order.for_pickup}}</small>
															<strong class='span-block text-danger' v-html="order.pref_payment"></strong>
														</td>
														<td>
															{{ order.ordered_date }}
															<span v-show="order.delivery_date" class='text-danger'>Delivery Date: {{ order.delivery_date }}</span>
														</td>
														<td class='text-right text-danger'>
															<strong>{{ order.total_price }}</strong>
															<small class='text-danger span-block'>{{ order.with_inv}}</small>
															<small class='text-danger span-block'>{{ order.price_group_name }}</small>
														</td>
														<td class='order-remarks'>
															<span v-html="order.remarks"></span>
															<div v-show="order.time_diff > 7">
																<br>
																<div  class="alert alert-danger"><i class='fa fa-warning'></i> Pending For More Than 7 Days</div>
															</div>

														</td>
														<td><span v-html="invoice_prefix+order.invoice"></span></td>
														<td><span v-html="dr_prefix+order.dr"></span></td>
														<td><span v-html="pr_prefix+order.pr"></span></td>
														<?php if(Configuration::getValue('has_sv') == 1){
															?>
															<td>{{ order.sv }}</td>
															<?php
														}?>
														<?php if(Configuration::getValue('has_sr') == 1){
															?>
															<td>{{ order.sr }}</td>
															<?php
														}?>
														<?php if(Configuration::getValue('has_ts') == 1){
															?>
															<td>{{ order.ts }}</td>
															<?php
														}?>

														<td>

															<?php
																if($user->hasPermission('wh_payment') || $user->hasPermission('wh_invdr')){
																	?>

																	<div class="btn-group" v-show="current_is_member != 1">
																		<button type="button" class="btn btn-default dropdown-toggle btn-icon-small" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																			<i class='fa fa-gear'></i>
																		</button>
																		<ul class="dropdown-menu">
																			<?php
																				if($user->hasPermission('wh_payment')) {
																					?>
																					<li>
																						<a data-member_id='{{order.member_id}}' data-for_pick_up='{{order.is_for_pickup}}' class='btnPayment' v-show='order.payment_id == 0' href='#'><span>Get Payment</span></a>
																					</li>
																					<li>
																						<a v-on:click="paymentDetails(order,$event)"  v-show='order.payment_id != 0' href='#'><span>Payment Details</span></a>
																					</li>
																				<?php } ?>
																			<?php
																				if($user->hasPermission('wh_invdr')) {
																					?>
																					<li>

																						<a v-show='order.payment_id != 0 && order.invoice == 0'   v-on:click="printInvoice(order,1)" href='#'><span><?php echo INVOICE_LABEL; ?></span></a>
																					</li>
																					<li>
																						<a   v-show='order.payment_id != 0 && order.dr == 0'   v-on:click="printInvoice(order,2)" href='#'><?php echo DR_LABEL; ?></a>
																					</li>
																					<li>
																						<a   v-show='order.payment_id != 0 && order.pr == 0'   v-on:click="printInvoice(order,3)" href='#'><?php echo PR_LABEL; ?></a>
																					</li>
																					<?php if(Configuration::getValue('has_sv') == 1){
																						?>
																						<li>
																							<a   v-show='order.payment_id != 0 && order.sv == 0'   v-on:click="printInvoice(order,4)" href='#'>SV</a>
																						</li>
																						<?php
																					}?>
																					<?php if(Configuration::getValue('has_sr') == 1){
																						?>
																						<li>
																							<a   v-show='order.payment_id != 0 && order.sr == 0'   v-on:click="printInvoice(order,6)" href='#'>SR</a>
																						</li>
																						<?php
																					}?>
																					<?php if(Configuration::getValue('has_ts') == 1){
																						?>
																						<li>
																							<a   v-show='order.payment_id != 0 && order.ts == 0'   v-on:click="printInvoice(order,7)" href='#'>TS</a>
																						</li>
																						<?php
																					}?>

																					<li>
																						<a   v-show='order.invoice != 0'   v-on:click="rePrintInvoice(order,1)" href='#'><span>Re-print <?php echo INVOICE_LABEL; ?></span></a>
																					</li>
																					<li>
																						<a  v-show='order.dr != 0'   v-on:click="rePrintInvoice(order,2)" href='#'>Re-print <?php echo DR_LABEL; ?></a>
																					</li>
																					<li>
																						<a  v-show='order.pr != 0'   v-on:click="rePrintInvoice(order,3)" href='#'>Re-print <?php echo PR_LABEL; ?></a>
																					</li>
																					<?php if(Configuration::getValue('has_sv') == 1){
																						?>
																					<li>
																						<a  v-show='order.sv != 0'   v-on:click="rePrintInvoice(order,4)" href='#'>Re-print SV</a>
																					</li>
																						<?php
																					}?>

																					<li  v-show='order.payment_id == 0'>Waiting for payment</li>
																				<?php } ?>
																		</ul>
																	</div>
																<?php	} ?>
															<button  v-bind:class="order.stock_out == 1 ? 'btn btn-success btnLoading btn-icon-small' : 'btn btn-default btn-icon-small'" v-on:click="getOrderDetails(order)"><i class='fa fa-list'></i></button>
															<a class='btn btn-default btn-icon-small' title='Attachment' target='_blank' v-show="order.file_name != ''" href="{{order.file_name}}"><i class='fa fa-paperclip'></i></a>
															<?php if($has_update_details){
																?>
																<button  class='btn btn-default btn-icon-small' v-on:click="updateOrderInfo(order)"><i class='fa fa-pencil'></i></button>
																<?php
															} ?>
															<?php if($has_consumable_check){
																?>
																<button  class='btn btn-default btn-icon-small' v-on:click="getPrevConsumable(order)"><i class='fa fa-money'></i></button>
																<?php
															} ?>
														</td>
													</tr>
													</tbody>
												</table>
											</div>
											<div v-show="!pending_for_approval.length && showLoading == false">
												<div class="alert alert-info">No record yet.</div>
											</div>
											<div v-show="showLoading">
												<div class="alert alert-info">Loading...</div>
											</div>
										</div>
										<div role="tabpanel" class="tab-pane" id="tab_for_reserve" >
											<div v-show="pending_for_approval_reserved.length">
												<table id='tblForApproval' class='table'>
													<thead>
													<tr>
														<th>Id</th>
														<th>Branch</th>
														<th>Created By</th>
														<th>To</th>
														<th>Ordered Date</th>
														<th class='text-right'>Total</th>
														<th>Remarks</th>
														<th><?php echo INVOICE_LABEL; ?></th>
														<th><?php echo DR_LABEL; ?></th>
														<th><?php echo PR_LABEL; ?></th>
														<th>Details</th>
													</tr>
													</thead>
													<tbody>
													<tr track-by="id" v-bind:class="[order.is_priority == 1 ? 'bg-danger' : '']"  data-total="{{ order.total_price }}" data-id="{{ order.id }}" v-for="order in pending_for_approval_reserved">
														<td class='text-danger'><i v-show='order.has_assemble_item == 1' class='fa fa-wrench'></i> <strong>{{ order.id }}</strong></td>
														<td>{{ order.branch_name }}</td>
														<td>{{ order.fullnameUser }} <small class='text-danger span-block'>{{ order.sales_type_name}}</small></td>
														<td><span v-html="order.fullname"></span>
															<small class='text-danger span-block'>{{ order.personal_address}}</small>
															<small class='span-block'> {{ order.shipping_name}}</small>
															<small class='span-block text-muted'>{{ order.client_po}}</small>
															<small class='span-block text-danger'>{{ order.for_pickup}}</small>
														</td>
														<td>{{ order.ordered_date }}</td>
														<td class='text-right text-danger'>
															<strong>{{ order.total_price }}</strong>
															<small class='text-danger span-block'>{{ order.with_inv}}</small>
															<small class='text-danger span-block'>{{ order.price_group_name }}</small>
														</td>
														<td class='order-remarks'><span v-html="order.remarks"></span> <br> Pending for: {{order.time_diff}} day(s)</td>
														<td><span v-html="invoice_prefix+order.invoice"></span></td>
														<td><span v-html="dr_prefix+order.dr"></span></td>
														<td><span v-html="pr_prefix+order.pr"></span></td>
														<td>
															<button  v-bind:class="order.stock_out == 1 ? 'btn btn-success btnLoading btn-icon-small' : 'btn btn-default btn-icon-small'" v-on:click="getOrderDetails(order)"><i class='fa fa-list'></i></button>
															<a title='Attachment' class='btn btn-default btn-icon-small' target='_blank' v-show="order.file_name != '' " href="../uploads/{{order.file_name}}"><i class='fa fa-file'></i></a>
														</td>
													</tr>
													</tbody>
												</table>
											</div>
											<div v-show="!pending_for_approval_reserved.length && showLoading == false">
												<div class="alert alert-info">No record yet.</div>
											</div>
											<div v-show="showLoading">
												<div class="alert alert-info">Loading...</div>
											</div>
										</div>
										<div role="tabpanel" class="tab-pane" id="tab_for_reserve_pending" <?php echo $hide_reserve; ?>>
											<div v-show="pending_for_approval_reserved_pending.length">
												<table id='tblForApproval' class='table'>
													<thead>
													<tr>
														<th>Id</th>
														<th>Branch</th>
														<th>Created By</th>
														<th>To</th>
														<th>Ordered Date</th>
														<th class='text-right'>Total</th>
														<th>Remarks</th>
														<th><?php echo INVOICE_LABEL; ?></th>
														<th><?php echo DR_LABEL; ?></th>
														<th><?php echo PR_LABEL; ?></th>
														<th>Details</th>
													</tr>
													</thead>
													<tbody>
													<tr track-by="id" v-bind:class="[order.is_priority == 1 ? 'bg-danger' : '']"  data-total="{{ order.total_price }}" data-id="{{ order.id }}" v-for="order in pending_for_approval_reserved_pending">
														<td class='text-danger'><i v-show='order.has_assemble_item == 1' class='fa fa-wrench'></i> <strong>{{ order.id }}</strong></td>
														<td>{{ order.branch_name }}</td>
														<td>{{ order.fullnameUser }} <small class='text-danger span-block'>{{ order.sales_type_name}}</small></td>
														<td><span v-html="order.fullname"></span>
															<small class='text-danger span-block'>{{ order.personal_address}}</small>
															<small class='span-block'> {{ order.shipping_name}}</small>
															<small class='span-block text-muted'>{{ order.client_po}}</small>
															<small class='span-block text-danger'>{{ order.for_pickup}}</small>
														</td>
														<td>{{ order.ordered_date }}</td>
														<td class='text-right text-danger'><strong>{{ order.total_price }}</strong>
															<small class='text-danger span-block'>{{ order.with_inv}}</small>
															<small class='text-danger span-block'>{{ order.price_group_name }}</small>
														</td>
														<td class='order-remarks'><span v-html="order.remarks"></span> <br> Pending for: {{order.time_diff}} day(s)</td>
														<td><span v-html="invoice_prefix+order.invoice"></span></td>
														<td><span v-html="dr_prefix+order.dr"></span></td>
														<td><span v-html="pr_prefix+order.pr"></span></td>
														<td>
															<button  v-bind:class="order.stock_out == 1 ? 'btn btn-success btnLoading btn-icon-small' : 'btn btn-default btn-icon-small'" v-on:click="getOrderDetails(order)"><i class='fa fa-list'></i></button>
															<a title='Attachment' class='btn btn-default btn-icon-small' target='_blank' v-show="order.file_name != '' " href="../uploads/{{order.file_name}}"><i class='fa fa-file'></i></a>
														</td>
													</tr>
													</tbody>
												</table>
											</div>
											<div v-show="!pending_for_approval_reserved_pending.length && showLoading == false">
												<div class="alert alert-info">No record yet.</div>
											</div>
											<div v-show="showLoading">
												<div class="alert alert-info">Loading...</div>
											</div>
										</div>
										<div role="tabpanel" class="tab-pane" id="tab_for_walkin_app" <?php echo $show_walkin_app; ?>>
											<div v-show="pending_for_approval_walkin.length">
												<table  class='table'>
													<thead>
													<tr>
														<th>Id</th>
														<th>Branch</th>
														<th>Created By</th>
														<th>To</th>
														<th>Ordered Date</th>
														<th class='text-right'>Total</th>
														<th>Remarks</th>
														<th><?php echo INVOICE_LABEL; ?></th>
														<th><?php echo DR_LABEL; ?></th>
														<th><?php echo PR_LABEL; ?></th>
														<th>Details</th>
													</tr>
													</thead>
													<tbody>
													<tr track-by="id" v-bind:class="[order.is_priority == 1 ? 'bg-danger' : '' , order.from_service != 0 ? 'bg-warning' : '']"  data-total="{{ order.total_price }}" data-id="{{ order.id }}" v-for="order in pending_for_approval_walkin">
														<td class='text-danger'><i v-show='order.has_assemble_item == 1' class='fa fa-wrench'></i> <strong>{{ order.id }}</strong></td>
														<td>{{ order.branch_name }}</td>
														<td>{{ order.fullnameUser }} <small class='text-danger span-block'>{{ order.sales_type_name}}</small></td>
														<td><span v-html="order.fullname"></span>
															<small class='text-danger span-block'>{{ order.personal_address}}</small>
															<small class='span-block'> {{ order.shipping_name}}</small>
															<small class='span-block text-muted'>{{ order.client_po}}</small>
															<small class='span-block text-danger'>{{ order.for_pickup}}</small>
														</td>
														<td>{{ order.ordered_date }}</td>
														<td class='text-right text-danger'><strong>{{ order.total_price }}</strong>
															<small class='text-danger span-block'>{{ order.with_inv}}</small>
															<small class='text-danger span-block'>{{ order.price_group_name }}</small>
														</td>
														<td class='order-remarks'><span v-html="order.remarks"></span> <br> Pending for: {{order.time_diff}} day(s)</td>
														<td><span v-html="invoice_prefix+order.invoice"></span></td>
														<td><span v-html="dr_prefix+order.dr"></span></td>
														<td><span v-html="pr_prefix+order.pr"></span></td>
														<td>
															<button  v-bind:class="order.stock_out == 1 ? 'btn btn-success btnLoading btn-icon-small' : 'btn btn-default btn-icon-small'" v-on:click="getOrderDetails(order)"><i class='fa fa-list'></i></button>
														</td>
													</tr>
													</tbody>
												</table>
											</div>
											<div v-show="!pending_for_approval_walkin.length && showLoading == false">
												<div class="alert alert-info">No record yet.</div>
											</div>
											<div v-show="showLoading">
												<div class="alert alert-info">Loading...</div>
											</div>
										</div>
									</div>
							</div>
							<div v-show='container.showApproved'>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<input type="text" placeholder='Search...' class='form-control' v-model=search_text>
										</div>
									</div>
								</div>
								<div class="row">

									<div class="col-md-3">
										<select name="assemble_filter" id="assemble_filter" v-model="assemble_filter" class='form-control' v-on:change="fetchedOrder(3,1)">
											<option value="">Select Item type</option>
											<option value="1"><?php echo SPAREPART_LABEL; ?></option>
											<option value="2">Set</option>

										</select>
									</div>
									<div class="col-md-3">
										<select name="for_pickup_filter" id="for_pickup_filter" v-model="for_pickup_filter" class='form-control' v-on:change="fetchedOrder(3,1)">
											<option value="">Select Order type</option>
											<option value="1">For Pick up</option>
											<option value="2">Deliveries</option>
											<?php if($cashier_transaction) {
												?>
												<option value="4">For Pick up and Deliveries</option>
												<option value="3">Cashier Transaction</option>
												<?php
											} ?>
										</select>
									</div>
									<div class="col-md-3">
										<select name="branch_id_filter" id="branch_id_filter" v-model="branch_id_filter" class='form-control' v-on:change="fetchedOrder(3,1)">
											<option value="">Filter Branch</option>
											<?php
												if($branchesmem){
													$bmfirst = true;
													foreach($branchesmem as $b){
														//if($b->id == $user->data()->branch_id) continue;
														$bmselected ='';
														if($bmfirst){
															$bmfirst = false;
															//$bmselected='selected';
														}
														?>
														<option <?php echo $bmselected; ?> value="<?php echo $b->id; ?>"><?php echo $b->name; ?></option>
														<?php
													}
												}
											?>
										</select>
									</div>
									<div class="col-md-3">

											<div class="form-group">
												<select class='form-control' name="salestype_filter" id="salestype_filter2" v-model='salestype_filter' v-on:change="fetchedOrder(1,1)">
													<option value="">Filter Sales Type</option>
													<?php
														foreach($salestypes as $get_type){
															echo "<option value='{$get_type->id}'>$get_type->name</option>";
														}
													?>
												</select>
											</div>

									</div>
								</div>
								<br>
								<div class="row">
									<div class="col-md-3">
										<div class="form-group">
										<input type="text" placeholder='Enter date from' class='form-control' id='warehouse_dt1' v-model='warehouse_dt1'>
										<span class='help-block'>Filter date for faster results</span>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<input type="text"  placeholder='Enter date to' class='form-control' id='warehouse_dt2' v-model='warehouse_dt2'>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<button class='btn btn-default' id='btnWarehouseSearchRecord' @click="warehouseSearchRecord">Filter</button>
											<span class='help-block'>Displaying the past 7 days by default</span>
										</div>
									</div>
									<div class="col-md-3 text-right">
										<div class="form-group">
											<button class='btn btn-primary' @click="warehouseShowAll">Show All</button>
											<span class='help-block'>Might take a few minutes</span>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-4">
										<h3>Warehouse</h3>
									</div>
									<div class="col-md-8 text-right">
										<span>Next <?php echo INVOICE_LABEL; ?>: </span>  <strong> {{  (invoice) ? invoice : 'No terminal set up.' }} </strong>
										<span>Next <?php echo DR_LABEL; ?>: </span>  <strong> {{ (dr) ? dr : 'No terminal set up.' }} </strong>
										<span>Next <?php echo PR_LABEL; ?>: </span>  <strong> {{ (pr) ? pr : 'No terminal set up.' }} </strong>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<?php
											if(Configuration::thisCompany('avision')){
												// print button

												// save query as search
												// get the id
												// make carrier detailed and / summary
												echo "<button class='btn btn-default' @click='printCarrierManifest'>Carrier Manifest</button><br><span v-show='salestype_filter'><input type='checkbox' id='chkToggleManifest' > Select All</span>";
											}
										?>
									</div>
									<div class="col-md-6"><div class='text-right'><button v-on:click="printWarehouse"><i class='fa fa-download'></i></button></div></div>
								</div>

								<div v-show="pending_approved.length">
									<div class="nav_order"></div>
									<table id='tblWarehouse' class='table'>
										<thead>
										<tr>
											<th>Id</th>
											<th>Branch</th>
											<th>Created By</th>
											<th>To</th>
											<th>Ordered Date</th>
											<th>Delivery Schedule</th>
											<th>Remarks</th>
											<th><?php echo INVOICE_LABEL; ?></th>
											<th><?php echo DR_LABEL; ?></th>
											<th><?php echo PR_LABEL; ?></th>
											<th>Details</th>
										</tr>
										</thead>
										<tbody>

										<tr v-bind:class="[order.is_current == 1 ? 'bg-current' : '']" v-for="order in pending_approved">
											<td  class='text-danger'>

													<?php if(Configuration::thisCompany('avision')){
														?>
														<input type="checkbox" class='chkCarrier' v-bind:data-id="order.client_po" >
														<?php
													} ?>

													<?php ?>
													<i v-show='order.has_assemble_item == 1' class='fa fa-wrench'></i> <strong> {{ order.id }}
													</strong>
													<small class='span-block'>{{(order.is_cod == 1) ? "Cash on Delivery" : "" }}
													</small>
													<span  v-show='order.is_for_pickup == 2'><i class='fa fa-money fa-2x'></i>  </span>

											</td>
											<td>{{ order.branch_name }}</td>
											<td>
												{{ order.fullnameUser }}
												<?php
													if(Configuration::thisCompany('avision')){
													?>
														<small class='text-danger span-block'>{{ order.sales_type_name}}</small>
												<?php } ?>
											</td>
											<td><span v-html="order.fullname"></span>
												<small class='text-danger span-block'>{{ order.personal_address}}</small>
												<small class='span-block'> {{ order.shipping_name}}</small>
												<small class='span-block text-danger'>{{ order.for_pickup}}</small>
												<strong class='span-block text-danger'><span v-html="order.pref_payment"></span></strong>
												<?php if(Configuration::thisCompany('avision')){ ?>
													<small class='text-danger span-block'>{{ order.client_po}}</small>
												<?php } ?>
											</td>
											<td>{{ order.ordered_date }}</td>
											<td>{{ order.is_scheduled }}</td>
											<td class='order-remarks'><span v-html="order.remarks"></span> <small class='span-block'>Date approved:
													<br>{{order.approved_date}}</small></td>
											<td><span v-html="invoice_prefix+order.invoice"></span></td>
											<td><span v-html="dr_prefix+order.dr"></span></td>
											<td><span v-html="pr_prefix+order.pr"></span></td>
											<td>
												<button  v-bind:class="order.stock_out == 1 ? 'btn btn-success' : 'btn btn-default'"  v-on:click="getOrderDetails(order)"><i class='fa fa-list'></i></button>
												<?php if($has_update_details && Configuration::thisCompany('avision')){
													?>
													<button  class='btn btn-default btn-icon-small' v-on:click="updateOrderInfo(order)"><i class='fa fa-pencil'></i></button>
													<?php
												} ?>
												<div v-show="order.stock_out == 1">
												<?php
													if(Configuration::thisCompany('avision')){
														if($user->hasPermission('wh_payment') || $user->hasPermission('wh_invdr')){
													?>

														<div class="btn-group" v-show="current_is_member != 1">
															<button type="button" class="btn btn-default dropdown-toggle btn-icon-small" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																<i class='fa fa-gear'></i>
															</button>
															<ul class="dropdown-menu">
																<?php
																	if($user->hasPermission('wh_invdr')) {
																		?>

																		<li>
																			<a   v-show='order.payment_id != 0 && order.dr == 0'   v-on:click="printInvoice(order,2)" href='#'><?php echo DR_LABEL; ?></a>
																		</li>
																		<li>
																			<a  v-show='order.dr != 0'   v-on:click="rePrintInvoice(order,2)" href='#'>Re-print <?php echo DR_LABEL; ?></a>
																		</li>

																	<?php } ?>
															</ul>
														</div>
													<?php	} } ?>
												</div>
											</td>
										</tr>
										<!--
										<tr v-bind:class="[order.is_priority == 1 ? 'bg-danger' : '']"  data-total="{{ order.total_price }}" data-id="{{ order.id }}" v-for="order in pending_approved">
											<td><strong class='text-danger'>{{ order.id }}</strong></td>
											<td>{{ order.branch_name }}</td>
											<td>{{ order.fullnameUser }}</td>
											<td>{{ order.fullname }}</td>
											<td>{{ order.ordered_date }}</td>
											<td>
												<button  v-bind:class="order.stock_out == 1 ? 'btn btn-success' : 'btn btn-default'"  v-on:click="getOrderDetails(order)"><i class='fa fa-list'></i></button>
											</td>
										</tr> -->
										</tbody>
									</table>
								</div>
								<div v-show="!pending_approved.length && showLoading == false">
									<div class="alert alert-info">No record yet.</div>
								</div>
								<div v-show="showLoading">
									<div class="alert alert-info">Loading...</div>
								</div>
								<div id="test"></div>
							</div>
							<div v-show='container.showShipping'>
								<h3><?php echo (Configuration::getValue('shipping_lbl')) ? Configuration::getValue('shipping_lbl') : 'Shipping';?></h3>
								<div class="row">
									<div class="col-md-3">
										<select  v-model="branch_id_filter" class='form-control' v-on:change="fetchedOrder(2,1)">
											<option value="">Filter Branch</option>
											<?php
												if($branchesmem){
													$bmfirst = true;
													foreach($branchesmem as $b){
														//if($b->id == $user->data()->branch_id) continue;
														$bmselected ='';
														if($bmfirst){
															$bmfirst = false;
															//$bmselected='selected';
														}
														?>
														<option <?php echo $bmselected; ?> value="<?php echo $b->id; ?>"><?php echo $b->name; ?></option>
														<?php
													}
												}
											?>
										</select>
									</div>
									<div class="col-md-3"></div>
									<div class="col-md-3"></div>
									<div class="col-md-3 text-right">
										<input type="text" placeholder='Search...' class='form-control' v-model='shippingSearchTxt'>
									</div>
								</div>
								<div v-show="pending_shipping.length">
									<div class="nav_order"></div>
									<table id='tblShipping' class='table'>
										<thead>
										<tr>
											<th>Id</th>
											<th>Branch</th>
											<th>Created By</th>
											<th>To</th>
											<th>Ordered Date</th>
											<th class='text-right'>Total</th>
											<th>Remarks</th>
											<th><?php echo INVOICE_LABEL; ?></th>
											<th><?php echo DR_LABEL; ?></th>
											<th><?php echo PR_LABEL; ?></th>
											<th>Details</th>
										</tr>
										</thead>
										<tbody>
										<tr v-bind:class="[order.is_priority == 1 ? 'bg-danger' : '']"   v-for="order in pending_shipping | filterBy shippingSearchTxt in 'id' 'invoice' 'dr' 'pr' 'fullname' " ">
											<td class='text-danger'><i v-show='order.has_assemble_item == 1' class='fa fa-wrench'></i> <strong>{{ order.id }}</strong><small class='span-block'>{{(order.is_cod == 1) ? "Cash on Delivery" : "" }}</small></td>
											<td>{{ order.branch_name }}</td>
											<td>{{ order.fullnameUser }}</td>
											<td><span v-html="order.fullname"></span>
												<small class='text-danger span-block'>{{ order.personal_address}}</small>
												<small class='span-block'> {{ order.shipping_name}}</small>

												<small class='span-block text-muted'>{{ order.client_po}}</small>
												<small class='span-block text-danger'>{{ order.for_pickup}}</small>
												<strong class='span-block text-danger'><span v-html="order.pref_payment"></span></strong>
											</td>
											<td>{{ order.ordered_date }}</td>
											<td class='text-right text-danger'>
												<strong>{{ order.total_price }}</strong>
												<small class='text-danger span-block'>{{ order.price_group_name }}</small>
											</td>
											<td class='order-remarks'><span v-html="order.remarks"></span></td>
											<td><span v-html="invoice_prefix+order.invoice"></span></td>
											<td><span v-html="dr_prefix+order.dr"></span></td>
											<td><span v-html="pr_prefix+order.pr"></span></td>
											<td>

												<button class='btn btn-default' v-on:click="getOrderDetails(order)"><i class='fa fa-list'></i></button>
												<?php if($has_update_details){
													?>
													<button  class='btn btn-default btn-icon-small' v-on:click="updateOrderInfo(order)"><i class='fa fa-pencil'></i></button>
													<?php
												} ?>
											</td>
										</tr>
										</tbody>
									</table>
								</div>
								<div v-show="!pending_shipping.length && showLoading == false">
									<div class="alert alert-info">No record yet.</div>
								</div>
								<div v-show="showLoading">
									<div class="alert alert-info">Loading...</div>
								</div>
							</div>
							<div v-show='container.showLog'>
								<div class="row">
									<div class="col-md-3"></div>
									<div class="col-md-3"></div>
									<div class="col-md-3"></div>
									<div class="col-md-3">
										<select  v-model="del_filter_type" class='form-control' v-on:change="fetchedOrderLog">
											<option value="">Select Order type</option>
											<option value="1">Branch to Branch</option>
											<option value="2">Branch to <?php echo MEMBER_LABEL; ?></option>
										</select>
									</div>
								</div>
								<h3>Deliveries</h3>
								<div class="row">
									<div class="col-md-12">
										<div class="row">
											<div class="col-md-2">
												<input type="text" id='log_search' class='form-control' v-model="log_search" placeholder='Search'>
											</div>
											<div class="col-md-2">
												<input type="text" id='log_from' class='form-control' v-model="log_from" placeholder='Date From'>
											</div>
											<div class="col-md-2">
												<input type="text" id='log_to'  class='form-control' v-model="log_to" placeholder='Date To'>
											</div>
											<div class="col-md-2">
												<select class='form-control' name="log_truck_id" id="log_truck_id" v-model='log_truck_id'>
													<option value="">Select Truck</option>
													<option v-for="truck in trucks" v-bind:value="truck.id">
														{{ truck.name }} - {{ truck.description }}
													</option>
												</select>
											</div>
											<div class="col-md-4">
												<button class='btn btn-default' v-on:click="filterDelLog">Filter</button>
												<button class='btn btn-default' v-on:click="printDelLog">Print</button>
											</div>
										</div>
									</div>
								</div>
								<hr>
								<div v-show="orders_log.length">
									<table id='tblDeliveries' class='table'>
										<thead>
										<tr>
											<th>Id</th>
											<th>Branch</th>
											<th>Created By</th>
											<th>To</th>
											<th>Ordered Date</th>
											<th>Total</th>
											<th>Delivery Schedule</th>
											<th>Remarks</th>
											<th><?php echo INVOICE_LABEL; ?></th>
											<th><?php echo DR_LABEL; ?></th>
											<th><?php echo PR_LABEL; ?></th>
											<th>Truck</th>
											<th>Details</th>
										</tr>
										</thead>
										<tbody>
										<tr v-bind:class="[order.is_current == 1 ? 'bg-current' : '']" v-for="order in orders_log">
											<td class='text-danger'><strong>{{ order.id }}</strong></td>
											<td>{{ order.branch_name }}</td>
											<td>{{ order.fullnameUser }}</td>
											<td><span v-html="order.fullname"></span>
												<small class='text-danger span-block'>{{ order.personal_address}}</small>
												<small class='span-block'> {{ order.shipping_name}}</small>
												<small class='span-block text-danger'>{{ order.for_pickup}}</small>
												<strong class='span-block text-danger'><span v-html="order.pref_payment"></span></strong>
											</td>
											<td>{{ order.ordered_date }}</td>
											<td class='text-right text-danger'>
												{{ order.total_price }}
												<small class='text-danger span-block'>{{ order.price_group_name }}</small>
											</td>
											<td>{{ order.is_scheduled }}</td>
											<td class='order-remarks'><span v-html="order.remarks"></span><small>Approved date: <br> {{order.approved_date}}</small></td>
											<td><span v-html="invoice_prefix+order.invoice"></span></td>
											<td><span v-html="dr_prefix+order.dr"></span></td>
											<td><span v-html="pr_prefix+order.pr"></span></td>
											<td><span v-html="order.truck"></span> <br>Driver:<br><span v-html="order.driver"></span> <br>Helpers: <br> <span v-html="order.helpers"></span></td>
											<td>
												<?php
													if($user->hasPermission('wh_payment') || $user->hasPermission('wh_invdr')){
														?>

														<div class="btn-group" v-show="current_is_member != 1">
															<button type="button" class="btn btn-default dropdown-toggle btn-icon-small" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																<i class='fa fa-gear'></i>
															</button>
															<ul class="dropdown-menu">
																<?php
																	if($user->hasPermission('wh_payment')) {
																		?>
																		<li>
																			<a data-member_id='{{order.member_id}}' data-for_pick_up='{{order.is_for_pickup}}' class='btnPayment' v-show='order.payment_id == 0' href='#'><span>Get Payment</span></a>
																		</li>
																		<li>
																			<a v-on:click="paymentDetails(order,$event)"  v-show='order.payment_id != 0' href='#'><span>Payment Details</span></a>
																		</li>
																	<?php } ?>
																<?php
																	if($user->hasPermission('wh_invdr')) {
																		?>
																		<li>

																			<a v-show='order.payment_id != 0 && order.invoice == 0'   v-on:click="printInvoice(order,1)" href='#'><span><?php echo INVOICE_LABEL; ?></span></a>
																		</li>
																		<li>
																			<a   v-show='order.payment_id != 0 && order.dr == 0'   v-on:click="printInvoice(order,2)" href='#'><?php echo DR_LABEL; ?></a>
																		</li>
																		<li>
																			<a   v-show='order.payment_id != 0 && order.pr == 0'   v-on:click="printInvoice(order,3)" href='#'><?php echo PR_LABEL; ?></a>
																		</li>
																		<?php if(Configuration::getValue('has_sv') == 1){
																			?>
																			<li>
																				<a   v-show='order.payment_id != 0 && order.sv == 0'   v-on:click="printInvoice(order,4)" href='#'>SV</a>
																			</li>
																			<?php
																		}?>
																		<?php if(Configuration::getValue('has_sr') == 1){
																			?>
																			<li>
																				<a   v-show='order.payment_id != 0 && order.sr == 0'   v-on:click="printInvoice(order,6)" href='#'>SR</a>
																			</li>
																			<?php
																		}?>
																		<?php if(Configuration::getValue('has_ts') == 1){
																			?>
																			<li>
																				<a   v-show='order.payment_id != 0 && order.ts == 0'   v-on:click="printInvoice(order,7)" href='#'>TS</a>
																			</li>
																			<?php
																		}?>
																		<li>
																			<a   v-show='order.invoice != 0'   v-on:click="rePrintInvoice(order,1)" href='#'><span>Re-print <?php echo INVOICE_LABEL; ?></span></a>
																		</li>
																		<li>
																			<a  v-show='order.dr != 0'   v-on:click="rePrintInvoice(order,2)" href='#'>Re-print <?php echo DR_LABEL; ?></a>
																		</li>
																		<li>
																			<a  v-show='order.pr != 0'   v-on:click="rePrintInvoice(order,3)" href='#'>Re-print <?php echo PR_LABEL; ?></a>
																		</li>
																		<li  v-show='order.payment_id == 0'>Waiting for payment</li>
																	<?php } ?>
															</ul>
														</div>
													<?php	} ?>
												<button class='btn btn-default btn-icon-small' v-on:click="getOrderDetails(order)"><i class='fa fa-list'></i></button>
												<button v-show="order.for_pickup == ''" v-show="current_is_member != 1" class='btn btn-default btn-icon-small' v-on:click="getOrderDates(order)">
													<i class="fa fa-calendar"></i>
												</button>

												<button  v-show="current_is_member != 1" class='btn btn-default btn-icon-small' v-on:click="backload(order)">
													<i class="fa fa-refresh"></i>
												</button>
											</td>
										</tr>
										</tbody>
									</table>
								</div>
								<div v-show="!orders_log.length && showLoading == false">
									<div class="alert alert-info">No record yet.</div>
								</div>
								<div v-show="showLoading">
									<div class="alert alert-info">Loading...</div>
								</div>
							</div>
							<div v-show='container.showPickup'>
								<div class="row">
									<div class="col-md-3"></div>
									<div class="col-md-3"></div>
									<div class="col-md-3"></div>
									<div class="col-md-3">
										<select  v-model="pickup_filter_type" class='form-control' v-on:change="fetchedOrderPickup">
											<option value="">Select Order type</option>
											<option value="1">Branch to Branch</option>
											<option value="2">Branch to <?php echo MEMBER_LABEL; ?></option>
										</select>
									</div>
								</div>
								<h3>Pickups</h3>
								<div class="row">
									<div class="col-md-8">
										<div class="row">
											<div class="col-md-3">
												<input type="text" id='log_search_pickup' class='form-control' v-model="log_search_pickup" placeholder='Search'>
											</div>
											<div class="col-md-3">
												<input type="text" id='log_from_pickup' class='form-control' v-model="log_from_pickup" placeholder='Date From'>
											</div>
											<div class="col-md-3">
												<input type="text" id='log_to_pickup'  class='form-control' v-model="log_to_pickup" placeholder='Date To'>
											</div>
											<div class="col-md-3">
												<button class='btn btn-default' v-on:click="filterPickupLog">Filter</button>
											</div>
										</div>
									</div>
									<div class="col-md-4 text-right">

									</div>
								</div>
								<hr>
								<div v-show="orders_pickup.length">
									<table id='tblDeliveries' class='table'>
										<thead>
										<tr>
											<th>Id</th>
											<th>Branch</th>
											<th>Created By</th>
											<th>To</th>
											<th>Ordered Date</th>
											<th>Pick up Schedule</th>
											<th>Remarks</th>
											<th><?php echo INVOICE_LABEL; ?></th>
											<th><?php echo DR_LABEL; ?></th>
											<th><?php echo PR_LABEL; ?></th>
											<th>Details</th>
										</tr>
										</thead>
										<tbody>
										<tr v-bind:class="[order.is_current == 1 ? 'bg-current' : '']" v-for="order in orders_pickup">
											<td class='text-danger'><strong>{{ order.id }}</strong></td>
											<td>{{ order.branch_name }}</td>
											<td>{{ order.fullnameUser }}</td>
											<td><span v-html="order.fullname"></span>
												<small class='text-danger span-block'>{{ order.personal_address}}</small>
												<small class='span-block'> {{ order.shipping_name}}</small>
												<small class='span-block text-danger'>{{ order.for_pickup}}</small>
											</td>
											<td>{{ order.ordered_date }}</td>
											<td>{{ order.is_scheduled }}</td>
											<td class='order-remarks'><span v-html="order.remarks"></span> <small>Approved date: <br> {{order.approved_date}}</small></td>
											<td><span v-html="invoice_prefix+order.invoice"></span></td>
											<td><span v-html="dr_prefix+order.dr"></span></td>
											<td><span v-html="pr_prefix+order.pr"></span></td>
											<td>
												<?php
													if($user->hasPermission('wh_payment') || $user->hasPermission('wh_invdr')){
														?>

														<div class="btn-group" v-show="current_is_member != 1">
															<button type="button" class="btn btn-default dropdown-toggle btn-icon-small" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																<i class='fa fa-gear'></i>
															</button>
															<ul class="dropdown-menu">
																<?php
																	if($user->hasPermission('wh_payment')) {
																		?>
																		<li>
																			<a data-member_id='{{order.member_id}}' data-for_pick_up='{{order.is_for_pickup}}' class='btnPayment' v-show='order.payment_id == 0' href='#'><span>Get Payment</span></a>
																		</li>
																		<li>
																			<a v-on:click="paymentDetails(order,$event)"  v-show='order.payment_id != 0' href='#'><span>Payment Details</span></a>
																		</li>
																	<?php } ?>
																<?php
																	if($user->hasPermission('wh_invdr')) {
																		?>
																		<li>

																			<a v-show='order.payment_id != 0 && order.invoice == 0'   v-on:click="printInvoice(order,1)" href='#'><span><?php echo INVOICE_LABEL; ?></span></a>
																		</li>
																		<li>
																			<a   v-show='order.payment_id != 0 && order.dr == 0'   v-on:click="printInvoice(order,2)" href='#'><?php echo DR_LABEL; ?></a>
																		</li>
																		<li>
																			<a   v-show='order.payment_id != 0 && order.pr == 0'   v-on:click="printInvoice(order,3)" href='#'><?php echo PR_LABEL; ?></a>
																		</li>
																		<?php if(Configuration::getValue('has_sv') == 1){
																			?>
																			<li>
																				<a   v-show='order.payment_id != 0 && order.sv == 0'   v-on:click="printInvoice(order,4)" href='#'>SV</a>
																			</li>
																			<?php
																		}?>
																		<?php if(Configuration::getValue('has_sr') == 1){
																			?>
																			<li>
																				<a   v-show='order.payment_id != 0 && order.sr == 0'   v-on:click="printInvoice(order,6)" href='#'>SR</a>
																			</li>
																			<?php
																		}?>
																		<?php if(Configuration::getValue('has_ts') == 1){
																			?>
																			<li>
																				<a   v-show='order.payment_id != 0 && order.ts == 0'   v-on:click="printInvoice(order,7)" href='#'>TS</a>
																			</li>
																			<?php
																		}?>
																		<li>
																			<a   v-show='order.invoice != 0'   v-on:click="rePrintInvoice(order,1)" href='#'><span>Re-print <?php echo INVOICE_LABEL; ?></span></a>
																		</li>
																		<li>
																			<a  v-show='order.dr != 0'   v-on:click="rePrintInvoice(order,2)" href='#'>Re-print <?php echo DR_LABEL; ?></a>
																		</li>
																		<li>
																			<a  v-show='order.pr != 0'   v-on:click="rePrintInvoice(order,3)" href='#'>Re-print <?php echo PR_LABEL; ?></a>
																		</li>
																		<li  v-show='order.payment_id == 0'>Waiting for payment</li>
																	<?php } ?>
															</ul>
														</div>
													<?php	} ?>
												<button class='btn btn-default btn-icon-small' v-on:click="getOrderDetails(order)"><i class='fa fa-list'></i></button>
												<button v-show="current_is_member != 1" class='btn btn-default btn-icon-small' v-on:click="getOrderDates(order)">
													<i class="fa fa-calendar"></i>
												</button>
												<button  v-show="current_is_member != 1" class='btn btn-default btn-icon-small' v-on:click="backload(order)">
													<i class="fa fa-refresh"></i>
												</button>
											</td>
										</tr>
										</tbody>
									</table>
								</div>
								<div v-show="!orders_pickup.length && showLoading == false">
									<div class="alert alert-info">No record yet.</div>
								</div>
								<div v-show="showLoading">
									<div class="alert alert-info">Loading...</div>
								</div>
							</div>

							<!-- Service Start -->
							<div v-show='container.showService'>

								<h3>Service</h3>
								<hr>
								<div v-show="orders_service.length">
									<table id='tblDeliveries' class='table'>
										<thead>
										<tr>
											<th>Id</th>
											<th>Branch</th>
											<th>Created By</th>
											<th>To</th>
											<th>Ordered Date</th>
											<th>Pick up Schedule</th>
											<th>Remarks</th>
											<th><?php echo INVOICE_LABEL; ?></th>
											<th><?php echo DR_LABEL; ?></th>
											<th><?php echo PR_LABEL; ?></th>
											<th>Details</th>
										</tr>
										</thead>
										<tbody>
										<tr v-bind:class="[order.is_current == 1 ? 'bg-current' : '']" v-for="order in orders_service">
											<td class='text-danger'><strong>{{ order.id }}</strong></td>
											<td>{{ order.branch_name }}</td>
											<td>{{ order.fullnameUser }}</td>
											<td><span v-html="order.fullname"></span>
												<small class='text-danger span-block'>{{ order.personal_address}}</small>
												<small class='span-block'> {{ order.shipping_name}}</small>
												<small class='span-block text-danger'>{{ order.for_pickup}}</small>
											</td>
											<td>{{ order.ordered_date }}</td>
											<td>{{ order.is_scheduled }}</td>
											<td class='order-remarks'><span v-html="order.remarks"></span> <small>Approved date: <br> {{order.approved_date}}</small></td>
											<td><span v-html="invoice_prefix+order.invoice"></span></td>
											<td><span v-html="dr_prefix+order.dr"></span></td>
											<td><span v-html="pr_prefix+order.pr"></span></td>
											<td>
												<?php
													if($user->hasPermission('wh_payment') || $user->hasPermission('wh_invdr')){
														?>

														<div class="btn-group" v-show="current_is_member != 1">
															<button type="button" class="btn btn-default dropdown-toggle btn-icon-small" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																<i class='fa fa-gear'></i>
															</button>
															<ul class="dropdown-menu">
																<?php
																	if($user->hasPermission('wh_payment')) {
																		?>
																		<li>
																			<a data-member_id='{{order.member_id}}' data-for_pick_up='{{order.is_for_pickup}}' class='btnPayment' v-show='order.payment_id == 0' href='#'><span>Get Payment</span></a>
																		</li>
																		<li>
																			<a v-on:click="paymentDetails(order,$event)"  v-show='order.payment_id != 0' href='#'><span>Payment Details</span></a>
																		</li>
																	<?php } ?>
																<?php
																	if($user->hasPermission('wh_invdr')) {
																		?>
																		<li>

																			<a v-show='order.payment_id != 0 && order.invoice == 0'   v-on:click="printInvoice(order,1)" href='#'><span><?php echo INVOICE_LABEL; ?></span></a>
																		</li>
																		<li>
																			<a   v-show='order.payment_id != 0 && order.dr == 0'   v-on:click="printInvoice(order,2)" href='#'><?php echo DR_LABEL; ?></a>
																		</li>
																		<li>
																			<a   v-show='order.payment_id != 0 && order.pr == 0'   v-on:click="printInvoice(order,3)" href='#'><?php echo PR_LABEL; ?></a>
																		</li>
																		<?php if(Configuration::getValue('has_sv') == 1){
																			?>
																			<li>
																				<a   v-show='order.payment_id != 0 && order.sv == 0'   v-on:click="printInvoice(order,4)" href='#'>SV</a>
																			</li>
																			<?php
																		}?>
																		<?php if(Configuration::getValue('has_sr') == 1){
																			?>
																			<li>
																				<a   v-show='order.payment_id != 0 && order.sr == 0'   v-on:click="printInvoice(order,6)" href='#'>SR</a>
																			</li>
																			<?php
																		}?>
																		<?php if(Configuration::getValue('has_ts') == 1){
																			?>
																			<li>
																				<a   v-show='order.payment_id != 0 && order.ts == 0'   v-on:click="printInvoice(order,7)" href='#'>TS</a>
																			</li>
																			<?php
																		}?>
																		<li>
																			<a   v-show='order.invoice != 0'   v-on:click="rePrintInvoice(order,1)" href='#'><span>Re-print <?php echo INVOICE_LABEL; ?></span></a>
																		</li>
																		<li>
																			<a  v-show='order.dr != 0'   v-on:click="rePrintInvoice(order,2)" href='#'>Re-print <?php echo DR_LABEL; ?></a>
																		</li>
																		<li>
																			<a  v-show='order.pr != 0'   v-on:click="rePrintInvoice(order,3)" href='#'>Re-print <?php echo PR_LABEL; ?></a>
																		</li>
																		<li  v-show='order.payment_id == 0'>Waiting for payment</li>
																	<?php } ?>
															</ul>
														</div>
													<?php	} ?>
												<button class='btn btn-default btn-icon-small' v-on:click="getOrderDetails(order)"><i class='fa fa-list'></i></button>
												<button v-show="current_is_member != 1" class='btn btn-default btn-icon-small' v-on:click="getOrderDates(order)">
													<i class="fa fa-calendar"></i>
												</button>
												<button  v-show="current_is_member != 1" class='btn btn-default btn-icon-small' v-on:click="backload(order)">
													<i class="fa fa-refresh"></i>
												</button>
											</td>
										</tr>
										</tbody>
									</table>
								</div>
								<div v-show="!orders_service.length && showLoading == false">
									<div class="alert alert-info">No record yet.</div>
								</div>
								<div v-show="showLoading">
									<div class="alert alert-info">Loading...</div>
								</div>
							</div>
							<!-- Service End -->
						</div>
					</div>
				</div>
			</div>
			<div class="modal fade" id="myModalUpdateInfo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4 class="modal-title">Update Order Information</h4>
							</div>
							<div class="modal-body">
								<div class="form-group">
									<strong>Shipping Company</strong>
									<select name="update_shipping_company" id="update_shipping_company" v-model="order_info.shipping_company_id" class='form-control'>
										<option value=""></option>
										<?php  getOptionShipping($shipping_companies,0); ?>
									</select>
								</div>
								<div class="form-group">
									<strong>Remarks</strong>
									<input type="text" class='form-control' v-model="order_info.remarks">
								</div>
								<div class="form-group">
									<strong>Client PO</strong>
									<input type="text" class='form-control' v-model="order_info.client_po">
								</div>
								<div class="form-group">
									<strong>Delivery Date</strong>
									<input type="text" class='form-control' id='update_info_del_date' v-model="order_info.delivery_date">
								</div>
								<div class="form-group">
									<strong>Delivery type</strong>
									<select class='form-control' v-model="order_info.is_for_pickup">
										<option value="0">For Delivery</option>
										<option value="1">For Pickup</option>
										<?php if($cashier_transaction == 1){
											?>
											<option value="2">Cashier Transaction</option>
											<?php
										} ?>

									</select>
								</div>
								<div class="form-group">
									<strong>Warranty Card Number (Optional)</strong>
									<input type="text" class='form-control' id='update_warranty_card_number' v-model="order_info.warranty_card_number">
								</div>
								<div class="form-group">
									<strong>Rebate</strong>
									<input type="text" class='form-control' id='update_rebate' v-model="order_info.rebate">
								</div>
								<div class="form-group">
									<button id='btnUpdateOrderInfoSave' class='btn btn-default' v-on:click="updateOrderInfoSave">SAVE</button>
								</div>
							</div>
					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
			<div class="modal fade" id="myModalConsumable" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title">Previous Transaction</h4>
						</div>
						<div class="modal-body" id='body_consumable'>
						</div>
					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
			<div class="modal fade" id="myModalCredit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title">Unpaid Transaction</h4>
						</div>
						<div class="modal-body" id='body_credit'>
							<table class='table'id ='tblCreditList'>
								<thead>
								<tr><th>Invoice</th><th>Dr</th><th>PR</th><th>Total</th><th>Paid</th><th>Remaining</th></tr>
								</thead>
								<tbody>
								<tr v-for="credit in current_credit_list">
									<td>{{credit.invoice}}</td>
									<td>{{credit.dr}}</td>
									<td>{{credit.ir}}</td>
									<td>{{credit.amount}}</td>
									<td>{{credit.amount_paid}}</td>
									<td class='text-danger'>{{credit.remaining}}</td>
								</tr>
								</tbody>
							</table>
						</div>
					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
			<div class="modal fade" id="back-load-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id='bl-title'></h4>
						</div>
						<div class="modal-body" id='bl-body'>
							<table class='table' v-if="backload_data.length">
								<thead>
								<tr>
									<th></th>
									<th>Item</th>
									<th>Order Qty</th>
									<th>Returned Qty</th>
									<th>Remarks</th>
									<th>Qty to Return</th>
									<th></th>
								</tr>
								</thead>
								<tbody>
								<tr v-for="order in backload_data" >
									<td class='bo'>
									</td>
									<td> {{ order.item_code }} <span class='text-danger' style='display:block;'>{{ order.description}}</span>
									</td>
									<td>
										{{ order.qty }}
									</td>
									<td>
										{{ order.backload_qty }}
									</td>
									<td>
										<input type="text" value="{{order.backload_remarks}}" v-model="order.backload_remarks" placeholder='Remarks'>
									</td>
									<td >
										<input type="text" v-on:keyup="checkBackQty(order.qty,order.back_qty,order.backload_qty,order)" value="{{order.back_qty}}" v-model="order.back_qty">
									</td>

									<td>
										<span v-html="order.rackhtml"></span>
									</td>
								</tr>
								</tbody>
							</table>
							<div class="text-right">
								<button  class='btn btn-default' v-on:click="printBackload()">Print</button>
								<button id='btnSaveBackload' class='btn btn-default' v-on:click="saveBackload()">Return Item</button>
							</div>
						</div>
					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->

			<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

							<h4 class="modal-title" id='mtitle'>Details</h4>
							<p>
								Order Id: <span class='text-danger'>{{current_order_det.id}}</span> Client Name:  <span class='text-danger'>{{current_order_det.fullname}}</span>
							</p>
							<p>
								<span v-show="current_order_det.invoice != '0'"> <?php echo INVOICE_LABEL; ?>: <span class='text-danger'>{{ current_order_det.invoice }}</span></span>
								<span v-show="current_order_det.dr != '0'"> <?php echo DR_LABEL; ?>: <span class='text-danger'>{{ current_order_det.dr }}</span></span>
								<span v-show="current_order_det.pr != '0'"> <?php echo PR_LABEL; ?>: <span class='text-danger'>{{ current_order_det.pr }}</span></span>
							</p>
							<input type="hidden" v-model='current_order'>
						</div>
						<div class="modal-body" id='mbody'>
							<div  v-show='details_ready'>

								<div>
									<div class="row">
										<div class="col-md-4"></div>
										<div class="col-md-4"></div>
										<div class="col-md-4">
											<div class="form-group">
												<input type="text" placeholder='Search category' class='form-control' v-model='filter_order_details'>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-10">
										<div v-show='current_updating == 1'>
											<div class="row">
												<div class="col-md-3">
													<input type="text"  class='form-control selectitem' id='new_item_order' v-model='new_item_order'>
												</div>
												<?php
													if($different_unit == 1){
														?>
														<div class="col-md-3" v-show="multiplier_qty.length">

																<select name="dif_qty" id="dif_qty2" v-model="dif_qty" class='form-control'>
																	<option v-for="qtys in multiplier_qty" v-bind:value="qtys.qty">{{qtys.unit_name}}</option>
																</select>

														</div>
														<?php
													}
												?>
												<div class="col-md-3">
													<input type="text" placeholder='Quantity' class='form-control' id='new_qty_order' v-model='new_qty_order'>
												</div>
												<div class="col-md-3">
													<button class='btn btn-default' v-on:click="addOrderDetails">Add Item</button>
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-2">
										<div class='text-right'>
											<?php
												$extra_auth_update='';
												if(!$user->hasPermission('inventory_all')){
													$extra_auth_update = "same_branch &&";
												}
											?>

											<?php if($user->hasPermission('wh_item_details')){
												?>
												<button style='display:none;' id='btnToUpdateDetails' class='btn btn-primary' v-show="<?php echo $extra_auth_update; ?> (computed_order.member_id == 0 || computed_order.payment_id == 0) && current_item_stock_out == 0" v-on:click="updateDetails">
													{{ (current_updating == 1 ) ? 'Save' : 'Update' }}
												</button>
												<?php
											}?>

										</div>
									</div>
								</div>


								<table id='whorderdetails' class='table'>
									<thead>
									<tr>
										<th></th>
										<th>Item</th>
										<?php if(!in_array($user->data()->branch_id,$dontshowprice)){?>
											<th  v-show="current_item_status != 2 && current_item_status != 3 && current_item_status != 4">Price</th>
										<?php } ?>

										<th>Qty</th>
										<?php if(!in_array($user->data()->branch_id,$dontshowprice)){?>
											<th v-show="current_item_status != 2 && current_item_status != 3  ">Total</th>
										<?php } ?>
										<th></th>
									</tr>
									</thead>

									<tbody>
									<tr v-for="order in orderDetails | filterBy filter_order_details in 'category_name'" v-bind:class="[order.is_check == 1 ? 'bg-info' : '']">
										<td class='bo'>

											<input  v-on:change="toggleCheckItem(order)" v-show="current_item_status==1 && current_is_member != 1" type="checkbox" v-model="order.is_check == 1 ? true : false">
											<button v-show="current_updating==1"   v-on:click="deleteOrderDetails(order)" class='btn btn-danger btn-sm'><i class='fa fa-close'></i></button>
											<button v-bind:class="[order.done_serial == 1 ? 'btn-success' : 'btn-default']"  v-show="order.has_serial==1  && current_item_stock_out==1 && (current_item_status==3 || current_item_status==4 || current_item_status==2)"   v-on:click="showSerialForm(order)" class='btn btn-sm'><i class='fa fa-list'></i></button>
											<?php if($user->hasPermission('wh_schedule') && $user->hasPermission('wh_remove_ship')){ ?>
											<span v-show="current_item_status==2 && current_order_det.member_id == 0" class='span-block' style='margin-top:10px;'>
											<input     type="checkbox" v-model="order.to_exclude"> <span class='text-danger' >Remove</span>
											</span>
											<?php } ?>
										</td>
										<td>
											<i v-show='order.machine == 1' class='fa fa-wrench text-danger'></i>
											{{ order.item_code }}
											<span class='text-danger' style='display:block;'>{{ order.description}}</span>
											<small class='text-danger' style='display:block;'>{{ order.category_name}}</small>
											<small class='span-block' v-show="order.station_name">{{order.station_name}}</small>
											<small class='span-block' v-show="order.sales_type_name">{{order.sales_type_name}}</small>
											<strong class='span-block text-danger' v-show="order.is_freebie == 1">FREE</strong>
											<strong class='span-block text-danger' v-show="order.is_surplus == 1">SURPLUS</strong>
										</td>
										<?php if(!in_array($user->data()->branch_id,$dontshowprice)){?>
										<td v-show="current_item_status != 2 && current_item_status != 3 && current_item_status != 4" v-bind:title="order.last_sold_info">
											{{ order.adjusted_price }}
											<?php  if(Configuration::thisCompany('cebuhiq')){
												?>
												<?php if(Configuration::getValue('mem_adj') == 1){?>
													<div v-show="current_updating == 1"><input placeholder='Override Price Here' type="text" @keyup="overridePrice(order)" v-model="order.override_price"></div>
												<?php
													}
												?>
												<?php
											}?>

											<?php if(!Configuration::getValue('mem_adj_round')){ ?>

											<small class='span-block text-danger' v-show="current_updating == 0 && order.adjustment_date">
												Adjustment: {{ order.ind_discount }} <br>
												Adjusted Price: {{ order.ind_price }} <br>
												Adjusted Date: {{ order.adjustment_date }}
											</small>

											<small class='span-block text-danger' v-show="current_updating == 0 && order.last_sold_date">
												Last Sold Date: {{ order.last_sold_date }} <br>
												Last Sold Price: {{ order.last_sold_amount }} <br>

											</small>

										<?php } ?>
										</td>
										<?php } ?>
										<td>
											<span v-show="current_updating==0">{{ order.qty }}</span>

											<input type='text' v-show="current_updating==1" v-model='order.qty' value="{{order.qty}}"/>
										</td>
										<?php if(!in_array($user->data()->branch_id,$dontshowprice)){?>
										<td v-show="current_item_status != 2 && current_item_status != 3 ">

										<?php if(Configuration::getValue('mem_adj') == 1){?>
											<input style='display:block;margin-bottom: 5px;' placeholder='Adjustment' type="text" v-if="current_updating==1" value='{{ (order.member_adjustment * 1) ? order.member_adjustment : order.member_adjustment_round }}' v-model='order.member_adjustment'>

										<?php } ?>
											{{ order.total }}
											<span v-show="order.member_adjustment != 0" style='display:block'>
												<br>({{order.member_adjustment}})<br>
													<span  v-show="current_updating==1">
												{{ total_current_adjustment(order) }}
														</span>
												<span  v-show="current_updating==0 && adjustment_default == 2" >
													{{ (order.total * 1) - (order.member_adjustment * 1) }}
												</span>
												<span  v-show="current_updating==0 && adjustment_default == 1" >
													{{ (order.total * 1) + (order.member_adjustment * 1) }}
												</span>
											</span>
											<div>
												<input  v-on:change="toggleHideDiscount(order)" v-show="current_item_status==1 && current_is_member != 1" type="checkbox" v-model="order.hide_discount == 1 ? true : false"> <span v-on:click="toggleHideDiscount(order)">Hide Discount</span>
											</div>
										</td>
										<?php } ?>
										<td >
											<div v-show="bundles[order.item_id].length">
												<p v-for="bundle in bundles[order.item_id]">{{ bundle.description}}  <strong class='text-danger pull-right'><i class='fa fa-long-arrow-right'></i> {{  order.qty *  bundle.child_qty}}</strong>
													<span v-html="bundle.rackhtml"></span>
												</p>
											</div>
										</td>
										<td v-show="current_is_member != 1">
											<div v-show="rackings[order.item_id].length">
												<p v-for="rack in rackings[order.item_id]">{{ rack.rack}}  <strong class='text-danger pull-right'><i class='fa fa-long-arrow-right'></i> {{ rack.qty}}</strong></p>
											</div>
										</td>
									</tr>
									</tbody>
								</table>
								<hr>
								<div  v-show="current_is_member != 1">

									<?php if($user->hasPermission('wh_approval_v')){ ?>
										<div class='text-right' v-show="current_item_status == 1">
											<span class='pull-left'>
												<button class='btn btn-default' v-on:click="togglePriorityOrder">Toggle Priority</button>
												<button class='btn btn-danger' v-on:click="declineOrder">Decline Order</button>
											</span>
											<div v-show="current_order_det.invoice != 0 || current_order_det.dr !=0  || current_order_det.pr !=0 || current_order_det.member_id == 0 ">
												<button v-show='current_auth' class='btn btn-primary' v-on:click="approveOrder">Approve Order</button>
											</div>
											<div v-show="current_order_det.is_reserve == 1 && current_order_det.reserved_date != 0">
												<button  class='btn btn-primary' v-on:click="toggleReserve">Send Order</button>
											</div>
											<div v-show="current_order_det.is_reserve == 1 && current_order_det.reserved_date == 0">
												<button  class='btn btn-primary' v-on:click="approveReserveOrder">Approve Reserve Order</button>
											</div>
											<?php if($user->hasPermission('wh_app_walkin')){ ?>
											<div v-show="current_order_det.for_approval_walkin == 1">
												<button  class='btn btn-primary' v-on:click="approveWalkInOrder">Approve Walk In Order</button>
											</div>
												<?php
											}
											?>
											&nbsp;
									</div>
										<?php
									}
									?>
									<div class='text-right' v-show="current_item_status == 3">
										<div class='alert alert-danger text-left' v-show="insufficient">Insufficient stocks.</div>
										<?php if($user->hasPermission('wh_approval_s')){
												if(!$user->hasPermission('inventory_all')){
													$extra_auth = " && same_branch";
												}
											?>
											<button v-show="current_item_stock_out == 0" class='btn btn-danger pull-left' v-on:click="declineOrder">Decline Order</button>
											<button id='btnGetStock' class='btn btn-primary' v-show="!insufficient<?php echo $extra_auth?>" v-on:click="getStocks" v-if="current_item_stock_out == 0">Get Stocks</button>
											<div class='text-left' v-show="insufficient && current_order_det.has_assemble_item == 1 && same_branch"> <button id='btnAssembleItem' class='btn btn-default' v-on:click="assembleItem"> Assemble</button> </div>
											<?php
										} ?>
										<?php if($user->hasPermission('wh_approval_p')){
											?>
											<button id='btnReturnStocks' class='btn btn-default' v-on:click="returnStocks" v-show="current_item_stock_out == 1<?php echo $extra_auth?>">Return Stocks</button>
											<button id='btnPrintRackLocation' class='btn btn-default' v-on:click="printRackLocation" v-show="current_item_stock_out == 1<?php echo $extra_auth?>">Print Rack Location</button>
											<button id='btnProcessToShipping' class='btn btn-primary' v-on:click="processToShipping" v-show="current_item_stock_out == 1<?php echo $extra_auth?>">Process Request</button>
										<?php } ?>
										<div class="clearfix"></div>
									</div>

									<?php if($user->hasPermission('wh_schedule')){ ?>
										<div class="text-right" v-show="current_item_status == 2 && current_item_isScheduled == 0 <?php echo $extra_auth?>">
											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<input type="text" class='form-control' placeholder='Schedule Date' v-model='schedule_date' id='schedule_date'>
													</div>
												</div>
												<div class="col-md-12">
													<div class="form-group">
														<select class='form-control' name="truck_id" id="truck_id" v-model='truck_id'>
															<option value="">Select Truck</option>
															<option v-for="truck in trucks" v-bind:value="truck.id">
																{{ truck.name }} - {{ truck.description }}
															</option>
														</select>
													</div>
												</div>
												<div class="col-md-12">
													<div class="form-group">
														<select class='form-control' name="driver_id" id="driver_id" v-model='driver_id'>
															<option value="">Choose Driver</option>
															<option v-for="driver in drivers" v-bind:value="driver.id">
																{{ driver.name }}
															</option>
														</select>
													</div>
												</div>
												<div class="col-md-12">
													<div class="form-group">
														<select class='form-control' name="helper_id" id="helper_id" v-model='helper_id' multiple>
															<option value=""></option>
															<option v-for="helper in helpers" v-bind:value="helper.id">
																{{ helper.name }}
															</option>
														</select>
													</div>
												</div>

												<div class="col-md-12">
													<div class="form-group">
														<button id='btnScheduleOrder' class='btn btn-primary' v-on:click="scheduleOrder">Schedule Order</button>
														<button id='btnBackToWarehouse' class='btn btn-danger' v-on:click="backToWarehouse">Back to warehouse</button>
													</div>
												</div>
											</div>
										</div>
									<div class="text-right" v-show="current_item_status == 2 && current_item_isScheduled != 0 <?php echo $extra_auth?>">
										<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<button id='btnShipOrder' class='btn btn-primary' v-on:click="shipOrder">Ship Order</button>
											</div>
											<div style='clear: both;'></div>
										</div>
										</div>
									</div>
										<div class="text-right" v-show="current_item_status == 4 <?php echo $extra_auth?>">
											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<button id='btnBackToWarehouse' class='btn btn-primary' v-on:click="backToWarehouse">Back to warehouse</button>
													</div>
													<div style='clear: both;'></div>
												</div>
											</div>
										</div>
									<?php } ?>
								</div>
							</div>
							<div v-show='!details_ready' class='alert alert-info'>Fetching records. Please wait...</div>
						</div>
					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->

			<div class="modal fade" id="myModalBundle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4 class="modal-title" id='btitle'></h4>
							</div>
							<div class="modal-body" id='bbody'>
							</div>
					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->

			<div class="modal fade" id="myModalMemberPending" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id=''></h4>
						</div>
						<div class="modal-body" id='member_pending_body'>
							<table class='table'>
								<tr><th>Use</th><th>Item</th><th>Description</th><th>Qty</th></tr>
								<tr v-for="member_pending_item in member_pending_items">
									<td><input type="checkbox" v-model="member_pending_item.is_use"></td>
									<td>{{member_pending_item.item_code}}</td>
									<td>{{member_pending_item.description}}</td>
									<td><input type="text" v-model="member_pending_item.qty"></td>
								</tr>
							</table>
							<div class='text-right'><br> <button id='btnSavePendingMember' class='btn btn-primary' v-on:click='saveTablePending'>Save</button></div>
						</div>
					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->

			<div class="modal fade" id="myModalSerial" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id='serialtitle'>Add Serial</h4>
						</div>
						<div class="modal-body" id='serialbody'>

							<table class='table'>
								<tr v-for="s in serials">
									<td>{{$index + 1}}</td>
									<td><input type="text" class='form-control' v-model="s.serial_no" value="{{s.serial_no}}"></td>
								</tr>
							</table>
							<div class='text-right'><button id='saveSerials' class='btn btn-default' v-on:click="saveSerials" >Save</button></div>
						</div>
					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->

			<div class="modal fade" id="myModalDates" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id='dtitle'>Schedule Date</h4>
							<input type="hidden" v-model='current_order'>
						</div>
						<div class="modal-body" id='dbody'>
							<div  v-show='dates_ready'>
								<div v-show="current_item_canBeResched">
								<input type="radio" id="re_for_del" value="0" v-model="re_for_pick_up">
								<label for="re_for_del">For Deliver</label>

								<input type="radio" id="ref_for_pick_up" value="1" v-model="re_for_pick_up">
								<label for="ref_for_pick_up">For Pickup</label>
								</div>
								<table class='table'>
									<thead>
									<tr>
										<th>Schedule Date</th>
										<th>Processed By</th>
										<th></th>
									</tr>
									</thead>
									<tbody>
									<tr v-for="dt in scheduleDates">
										<td class='text-success'><strong>{{ dt.sched_date }}</strong></td>
										<td>{{ dt.fullname }}</td>
										<td class='text-danger'>
											<strong>{{ (dt.isFirst == true) ? 'Current Schedule' : '' }}</strong>
										</td>
									</tr>
									</tbody>
								</table>


								<hr>
								<div class="row" v-show="current_item_canBeResched">
									<div class="col-md-12">
										<div class="form-group">
											<input type="text" class='form-control' placeholder='Schedule Date' v-model='re_schedule_date' id='re_schedule_date'>
										</div>
									</div>
									<div class="col-md-12">
										<div class="form-group">
											<select v-show="re_for_pick_up == 0" class='form-control' name="re_truck_id" id="re_truck_id" v-model='re_truck_id'>
												<option value="">Select Truck</option>
												<option v-for="truck in trucks" v-bind:value="truck.id">
													{{ truck.name }} - {{ truck.description }}
												</option>
											</select>
										</div>
									</div>
									<div class="col-md-12">
										<div class="form-group">
											<select v-show="re_for_pick_up == 0" class='form-control' name="re_driver_id" id="re_driver_id" v-model='re_driver_id'>
												<option value="">Choose Driver</option>
												<option v-for="driver in drivers" v-bind:value="driver.name">
													{{ driver.name }}
												</option>
											</select>
										</div>
									</div>
									<div class="col-md-12">
										<div class="form-group">
											<select v-show="re_for_pick_up == 0" class='form-control' name="re_helper_id" id="re_helper_id" v-model='re_helper_id' multiple>
												<option value=""></option>
												<option v-for="helper in helpers" v-bind:value="helper.name">
													{{ helper.name }}
												</option>
											</select>
										</div>
									</div>
									<div class="col-md-12">
										<button id='btnReScheduleOrder' class='btn btn-primary' v-on:click="reScheduleOrder">Reschedule Order</button>
									</div>
								</div>
							</div>
							<div v-show='!dates_ready' class='alert alert-info'>Fetching records. Please wait...</div>
						</div>
					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
			<div class="modal fade" id="myModalBatch" tabindex="-1" role="dialog" aria-labelledby="myModalBatch" aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title">Process Request</h4>
						</div>
						<div class="modal-body" id='body_batch'>

							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<strong>Truck(optional): </strong>
										<select class='form-control' name="batch_truck_id" id="batch_truck_id" v-model='batch_truck_id'>
											<option value="">Select Truck</option>
											<option v-for="truck in trucks" v-bind:value="truck.id">
												{{ truck.name }} - {{ truck.description }}
											</option>
										</select>
									</div>
								</div>
								<div class='col-md-12'>
									<div class="form-group">
										<strong>Driver(optional): </strong>
										<select class='form-control' name="batch_driver_id" id="batch_driver_id" v-model='batch_driver_id'>
											<option value="">Choose Driver</option>
											<option v-for="driver in drivers" v-bind:value="driver.name">
												{{ driver.name }}
											</option>
										</select>
									</div>
								</div>
								<div class='col-md-12'>
									<div class="form-group">
										<strong>Date(optional): </strong>
										<input type="text" class='form-control' id='batch_date' placeholder='Schedule (mm/dd/yyyy)'>
									</div>
								</div>
								<div class="col-md-12">
									<button class='btn btn-default' id='btnSaveBatch' @click="submitBatch">Approve</button> <button class='btn btn-danger' id='btnDeclineBatch' @click="declineBatch">Decline</button>
								</div>
							</div>
						</div>
					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->

			<div class="modal fade" id="getpricemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
				<div class="modal-dialog" style='width:95%'>
					<div class="modal-content">
						<div class="modal-body">
							<div id='paymethods'>
								<div class="row">
									<div class="col-md-6">
										<div id="over_payment_holder"></div>
										<input type="hidden" id='payment_order_id'>
										<input type="hidden" id="hid_sr_cur_payment" />
										<input type="hidden" id="hidcashpayment" />
										<input type="hidden" id="hidcreditpayment" />
										<input type="hidden" id="hidbanktransferpayment" />
										<input type="hidden" id="hidchequepayment" />
										<input type="hidden" id="hidconsumablepayment" />
										<input type="hidden" id="hidconsumablepaymentfreebies" />
										<input type="hidden" id="hidmembercredit" />
										<input type="hidden" id="hidmemberdeduction" />
										<span id='totalOfAllPayment' style='padding-left:10px;'></span>
										<input type="hidden" id="hidTotalOfAllPayment" />
										<span  id='amountdue' style='padding-left:10px;'></span>
										<input type="hidden" id="hidamountdue" />

									</div>
									<div class="col-md-2"><button style='display:none;' id='use_user_overpayment' class='btn btn-default btn-sm'>Deposits</button></div>
									<div class="col-md-4">

											<input type="text" id='override_payment_date' v-model='override_payment_date' class='form-control' placeholder='Override date (optional)'>
											<span class='help-block'>Use only if you want to override the date of payment.</span>

									</div>
								</div>
								<div class="row">
									<p><strong>Issue: </strong></p>
									<div class="row">
										<div class="col-md-2">
											<input type="checkbox"  id='chkInvoice'> <label for="chkInvoice"><?php echo INVOICE_LABEL; ?> # {{invoice}} </label>
										</div>
										<div class="col-md-2">
											<input type="checkbox" id='chkDr'> <label for="chkDr"><?php echo DR_LABEL; ?> # {{dr}} </label>
										</div>
										<div class="col-md-2">
											<input type="checkbox" id='chkPr'> <label for="chkPr"><?php echo PR_LABEL; ?> # {{pr}} </label>
										</div>
										<?php
											if(Configuration::getValue('has_sv') == 1) {
												?>
												<div class="col-md-2">
													<input type="checkbox" id='chkSv'>
													<label for="chkSv"><?php echo "SV"; ?> # {{sv}}
													</label>
												</div>
												<?php
											}
										?>

										<?php
											if(Configuration::getValue('has_ts') == 1) {
												?>
												<div class="col-md-2">
													<input type="checkbox" id='chkTs'>
													<label for="chkTs"><?php echo "TS"; ?> # {{ts}}
													</label>
												</div>
												<?php
											}
										?>
										<?php
											if(Configuration::getValue('has_sr') == 1) {
												?>
												<div class="col-md-2">
													<input type="checkbox" id='chkSr'>
													<label for="chkSr"><?php echo "SR"; ?> # {{sr}}
													</label>
												</div>
												<?php
											}
										?>
									</div>
								</div>
							</div>
								<div class="row">
									<div class="col-md-4"></div>
									<div class="col-md-4"></div>

								</div>
							<hr>
							<ul class="nav nav-tabs">
								<li class="tab_nav_a active"><a href="#tab_a" data-toggle="tab">Cash <span id='totalcashpayment' class='badge'></span></a></li>
								<li class='tab_nav_b'><a href="#tab_b" data-toggle="tab">Credit Card <span id='totalcreditpayment' class='badge'></span></a></li>
								<li class='tab_nav_c'><a href="#tab_c" data-toggle="tab">Bank Transfer <span id='totalbanktransferpayment' class='badge'></span></a></li>
								<li class='tab_nav_d'><a href="#tab_d" data-toggle="tab">Check 	<span id='totalchequepayment' class='badge'></span></a></li>
								<li class='tab_nav_e'><a href="#tab_e" data-toggle="tab">Consumable Amount <span id='totalconsumablepayment' class='badge'></span> </a></li>
								<li class='tab_nav_f'><a href="#tab_f" data-toggle="tab">Consumable Freebies <span id='totalconsumablepaymentfreebies' class='badge'></span> </a></li>
								<li class='tab_nav_g'><a href="#tab_g" data-toggle="tab">Credit <span id='totalmembercredit' class='badge'></span> </a></li>
								<li class='tab_nav_h notcashlist'><a href="#tab_h" data-toggle="tab">Deduction <span id='totalmemberdeduction' class='badge'></span> </a></li>
							</ul>
							<div class="tab-content" style='margin-top:10px;'>
								<?php include 'includes/payment_module.php'; ?>
							</div><!-- tab content -->
						</div>
						</div>

					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->


			<div class="modal fade" id="modalPayment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id='ptitle'></h4>
						</div>
						<div class="modal-body" id='pbody'>
						</div>
					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->


			<div class="modal fade" id="modalOverPayment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<h3>Over payment</h3>
						</div>
						<div class="modal-body" >
							<div class="">
								<div class="row">
									<div class="col-md-4"></div>
									<div class="col-md-4">
										<input type="text" id="op_remarks" placeholder='Remarks' class='form-control' >
									</div>
									<div class="col-md-4">
										<input class='form-control' type="text" id='op_member_id'>
									</div>
								</div>
							</div>
							<?php include "includes/modal_op.php"; ?>
						</div>
					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
		</div>
	</div> <!-- end page content wrapper-->

	<script src='../js/swipebox.js'></script>
	<script src='../js/vue.js'></script>
	<script src='../js/wh-order-v19.js?v=16'></script>
	<script src='../js/wh.js?v=11111111122283'></script>

<?php require_once '../includes/admin/page_tail2.php'; ?>