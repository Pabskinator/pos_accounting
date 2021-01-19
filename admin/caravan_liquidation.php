<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('caravan_request')) {
		// redirect to denied page
		Redirect::to(1);
	}


?>

	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					Liquidate
				</h1>
			</div>
			<div class="row">
				<div class="col-md-12">

					<?php
						if(intval(Input::get('id'))){
							$id = 	Input::get('id');
							?>

							<?php
							$myReq = new Agent_request($id);
							$disnone = "";
							if($myReq->data()->is_approve_liq == 1){
								$disnone = "display:none;";
							}
							if($myReq->data()->status == 2 && ($user->data()->id == $myReq->data()->user_id || $user->hasPermission("caravan_manage"))){
								$od = new Agent_request_details();
								$details = $od->get_active('agent_request_details', array('request_id', "=", $id));
								$arrayname = array();
								?>
								<input type="hidden" value='<?php echo $id; ?>' id='request_id'/>
								<div class='well well-sm text-left '><strong>Items to liquidate</strong></div>
								<table class="table" id='main_table'>
									<thead>
									<tr>
										<th>Item </th>
										<th>Price</th>
										<th>Total Quantity</th>
										<th>Quantity not yet allocated</th>
									</tr>
									</thead>
									<tbody>
									<?php
										foreach ($details as $d) {
											$itemname = new Product($d->item_id);
											$price = $itemname->getPrice($d->item_id);
											$arrayname[$d->item_id] = $itemname->data()->barcode.":".$itemname->data()->item_code.":".$itemname->data()->description;
											?>

											<tr id='<?php echo $d->item_id?>' data-price_id='<?php echo $price->id ?>'>
												<td><?php echo $itemname->data()->item_code . "<br><small>".$itemname->data()->description."</small>"; ?></td>
												<td><?php echo $price->price; ?></td>
												<td><?php echo formatQuantity($d->qty,true); ?></td>
												<td class='text-danger'><?php echo formatQuantity($d->qty,true); ?></td>
											</tr>
										<?php
										}
									?>
									</tbody>
								</table>
								<hr>
								<input type="hidden" id='is_approve_liq' value='<?php echo $myReq->data()->is_approve_liq; ?>'>
								<input type="hidden" id='cache_liquidation' value='<?php echo $myReq->data()->cache_liquidation; ?>'>
								<input type="hidden" id='cache_payment' value='<?php echo $myReq->data()->cache_payment; ?>'>
								<div class='well well-sm text-left' style='<?php echo $disnone; ?>'><strong>Please fill up the form to liquidate the items.</strong></div>
								
								<div id="main">
									<div class='row' style='<?php echo $disnone; ?>'>
									<div class="col-md-6">
										<div class="form-group">
										<select id='select_member' class='form-control'>
											<option></option>
												<?php
													$mem = new Station();
													$members = $mem->getAllStation($user->data()->company_id);
													foreach ($members as $m):
														$mn = ucwords( $m->mln . ", " . $m->mfn);
												?>
												<option value='<?php echo $m->id ?>'><?php echo $m->name .  ": " . $mn;  ?> </option>
												<?php
														endforeach;
												?>

										</select>
										</div>
									</div>


									</div>

									<div class='row' style='<?php echo $disnone; ?>'>
										<div class="col-md-3">
											<div class="form-group">
											<input type="text" id='sold_date' class='form-control' placeholder='Sold date'/>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<input type="text" id='sr' class='form-control' placeholder='SR'/>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<input type="text" id='remarks' class='form-control' placeholder='Remarks (Optional)'/>
											</div>
										</div>
									</div>
										<div class='row' style='<?php echo $disnone; ?>'>
										<div class="col-md-3">
											<div class="form-group">
											<select id='select_item' class="form-control">
												<option></option>
												<?php
													foreach ($arrayname as $id => $code) {
														$itemp = new Product($id);
														$price = $itemp->getPrice($id);
														?>
														<option data-price_id='<?php echo $price->id ?>' data-price='<?php echo $price->price ?>' value='<?php echo $id?>'><?php echo $code?></option>
													<?php
													}
												?>
											</select>
											</div>
										</div>
									<div class="col-md-3" >
										<div class="form-group">
										<input type="text" id='item_qty' class='form-control' placeholder='QTY'/>
											</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<input type="text" id='item_discount' class='form-control' placeholder='Discount (Optional)'/>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
										<input type="button" id='addtolist' class='btn btn-default' value='Add'/>
										</div>
									</div>
										</div>
								</div>
								<br/>	<br/>
								<div id="no-more-tables">
								<table class="table" id="table_liquidation">
									<thead>
									<tr>
										<th>Item</th>
										<th>Price</th>
										<th>Station</th>
										<th>Quantity</th>
										<th>Discount</th>
										<th>Total</th>
										<th>SR</th>
										<th>Date</th>
										<th></th>
									</tr>
									</thead>
									<tbody>

									</tbody>
								</table>
								</div>
								<p style='<?php echo $disnone; ?>'><span id='sdTotal'></span></p>
								<div class="row">
									<?php if($myReq->data()->is_approve_liq==1){
										?>
										<div class="col-md-8">

										</div>
										<div class="col-md-4">

											<input type="button" id='return' value='RETURN LIQUIDATION' class='btn btn-success' />
											<input type="button" id='save' value='VERIFY LIQUIDATION' class='btn btn-success' />
										</div>
										<?php
									} else {
										?>
										<div class="col-md-8">

										</div>
										<div class="col-md-4">

											<input type="button" id='btnForApproval' value='SUBMIT FOR APPROVAL' class='btn btn-success' />
										</div>
									<?php
									}?>

								</div>
							<?php

							} else {
								Redirect::to('manage_caravan.php');
								echo "Sorry, the system doesn't allowed you to view this data.";
							}


						} else {
							echo "<div class='alert alert-danger'>Sorry, the system doesn't allowed you to view this data.</div>";
						}
					?>


				</div>
			<div id="test"></div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<div class="modal fade" id="getpricemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
		<div class="modal-dialog" style='width:95%'>
			<div class="modal-content">
				<div class="modal-body">
					<div id='paymethods'>
						<input type="hidden" id="hid_sr_cur_payment" />
						<input type="hidden" id="hidcashpayment" />

						<input type="hidden" id="hidcreditpayment" />

						<input type="hidden" id="hidbanktransferpayment" />

						<input type="hidden" id="hidchequepayment" />

						<input type="hidden" id="hidconsumablepayment" />
						<input type="hidden" id="hidconsumablepaymentfreebies" />
						<input type="hidden" id="hidmembercredit" />
						<span id='totalOfAllPayment' style='padding-left:10px;'></span>
						<input type="hidden" id="hidTotalOfAllPayment" />
						<span  id='amountdue' style='padding-left:10px;'></span>
						<input type="hidden" id="hidamountdue" />
					</div>
					<hr>
					<ul class="nav nav-tabs">
						<li class="active"><a href="#tab_a" data-toggle="tab">Cash <span id='totalcashpayment' class='badge'></span></a></li>
						<li><a href="#tab_b" data-toggle="tab">Credit Card <span id='totalcreditpayment' class='badge'></span></a></li>
						<li><a href="#tab_c" data-toggle="tab">Bank Transfer <span id='totalbanktransferpayment' class='badge'></span></a></li>
						<li><a href="#tab_d" data-toggle="tab">Check 	<span id='totalchequepayment' class='badge'></span></a></li>
						<li><a href="#tab_e" data-toggle="tab">Consumable Amount <span id='totalconsumablepayment' class='badge'></span> </a></li>
						<li><a href="#tab_f" data-toggle="tab">Consumable Freebies <span id='totalconsumablepaymentfreebies' class='badge'></span> </a></li>
						<li><a href="#tab_g" data-toggle="tab">Credit <span id='totalmembercredit' class='badge'></span> </a></li>

					</ul>
					<div class="tab-content">
						<div class="tab-pane active" id="tab_a">
							<fieldset>
								<div class="form-group">
									<label class="col-md-3 control-label text-center" for="cashreceivetext">Amount</label>
									<div class="col-md-9">
										<input id="cashreceivetext" name="cashreceivetext" class="form-control input-md" type="text">
										<span class="help-block">Amount In Peso</span>
									</div>
								</div>


							</fieldset>
							<div class="form-group">
								<div class="row">
									<div class="col-md-9"></div>
									<div class="col-md-3">
										<button type="button" class="btn btn-primary cashreceiveok">OK </button>
										<button type="button"  class="btn btn-primary cashreceivecancel">CANCEL </button>
									</div>
								</div>
							</div>
						</div>

						<div class="tab-pane" id="tab_b">
							<fieldset>
								<legend>Billing Information</legend>
								<div class="row">
									<table class="table" id="credit_table"></table>
								</div>
								<div class="row">
									<div class="col-md-1"></div>
									<div class="col-md-3"><input id="billing_cardnumber" name="billing_cardnumber" placeholder="Card #" class="form-control input-md" type="text">
									</div>
									<div class="col-md-3"><input id="billing_amount" name="billing_amount" placeholder="Amount" class="form-control input-md" type="text"></div>
									<div class="col-md-3"><input id="billing_bankname" name="billing_bankname" placeholder="Bank" class="form-control input-md" type="text"></div>
									<div class="col-md-2"><input type="button" id='addcreditcard' class='btn btn-default' value='Add'/></div>
								</div>
								<hr />
								<div class="form-group">
									<label class="col-md-3 control-label text-center" for="billing_firstname">First Name <span class='text-danger'>*</span></label>
									<div class="col-md-7">
										<input id="billing_firstname" name="billing_firstname" class="form-control input-md" type="text">
										<span class="help-block"></span>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 control-label text-center" for="billing_middlename">Middle Name </label>
									<div class="col-md-7">
										<input id="billing_middlename" name="billing_middlename" class="form-control input-md" type="text">
										<span class="help-block"></span>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 control-label text-center" for="billing_lastname">Last Name <span class='text-danger'>*</span></label>
									<div class="col-md-7">
										<input id="billing_lastname" name="billing_lastname" class="form-control input-md" type="text">
										<span class="help-block"></span>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 control-label text-center" for="billing_company">Company </label>
									<div class="col-md-7">
										<input id="billing_company" name="billing_company" class="form-control input-md" type="text">
										<span class="help-block"></span>
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-3 control-label text-center" for="billing_address">Address </label>
									<div class="col-md-7">
										<input id="billing_address" name="billing_address" class="form-control input-md" type="text">
										<span class="help-block"></span>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 control-label text-center" for="billing_postal">Zip/Postal Code </label>
									<div class="col-md-7">
										<input id="billing_postal" name="billing_postal" class="form-control input-md" type="text">
										<span class="help-block"></span>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 control-label text-center" for="billing_phone">Cellphone/Tel Number <span class='text-danger'>*</span></label>
									<div class="col-md-7">
										<input id="billing_phone" name="billing_phone" class="form-control input-md" type="text">
										<span class="help-block"></span>
									</div>
								</div>


								<div class="form-group">
									<label class="col-md-3 control-label text-center" for="billing_email">Email </label>
									<div class="col-md-7">
										<input id="billing_email" name="billing_email" class="form-control input-md" type="text">
										<span class="help-block"></span>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 control-label text-center" for="billing_remarks">Special Notes</label>
									<div class="col-md-7">
										<input id="billing_remarks" name="billing_remarks" class="form-control input-md" type="text">
										<span class="help-block"></span>
									</div>
								</div>
							</fieldset>
							<div class="form-group">
								<div class="row">
									<div class="col-md-9"></div>
									<div class="col-md-3">
										<button type="button" class="btn btn-primary cashreceiveok">OK </button>
										<button type="button"  class="btn btn-primary cashreceivecancel">CANCEL </button>
									</div>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="tab_c">
							<fieldset>
								<legend>Billing Information</legend>
								<div class="row">
									<table class="table" id="bt_table"></table>
								</div>
								<div class="row">
									<div class="col-md-1"></div>
									<div class="col-md-3"><input id="bankfrom_account_number" name="bankfrom_account_number" placeholder="Account #" class="form-control input-md" type="text">
									</div>
									<div class="col-md-3"><input id="bt_amount" name="bt_amount" placeholder="Amount" class="form-control input-md" type="text"></div>
									<div class="col-md-3"><input id="bankfrom_name" name="bankfrom_name" placeholder="Bank" class="form-control input-md" type="text"></div>
									<div class="col-md-2"><input type="button" id='addbanktransfer' class='btn btn-default' value='Add'/></div>
								</div>
								<hr />
								<div class="form-group">
									<label class="col-md-3 control-label text-center" for="bt_bankto_name">Transfer to <span class='text-danger'>*</span></label>
									<div class="col-md-7">
										<input id="bt_bankto_name" name="bt_bankto_name" class="form-control input-md" type="text">
										<span class="help-block"></span>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 control-label text-center" for="bt_bankto_account_number">Bank Account Number <span class='text-danger'>*</span></label>
									<div class="col-md-7">
										<input id="bt_bankto_account_number" name="bt_bankto_account_number" class="form-control input-md" type="text">
										<span class="help-block"></span>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 control-label text-center" for="bt_firstname">First Name <span class='text-danger'>*</span></label>
									<div class="col-md-7">
										<input id="bt_firstname" name="bt_firstname" class="form-control input-md" type="text">
										<span class="help-block"></span>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 control-label text-center" for="bt_middlename">Middle Name </label>
									<div class="col-md-7">
										<input id="bt_middlename" name="bt_middlename" class="form-control input-md" type="text">
										<span class="help-block"></span>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 control-label text-center" for="bt_lastname">Last Name <span class='text-danger'>*</span></label>
									<div class="col-md-7">
										<input id="bt_lastname" name="bt_lastname" class="form-control input-md" type="text">
										<span class="help-block"></span>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 control-label text-center" for="bt_company">Company </label>
									<div class="col-md-7">
										<input id="bt_company" name="bt_company" class="form-control input-md" type="text">
										<span class="help-block"></span>
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-3 control-label text-center" for="bt_address">Address </label>
									<div class="col-md-7">
										<input id="bt_address" name="bt_address" class="form-control input-md" type="text">
										<span class="help-block"></span>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 control-label text-center" for="bt_postal">Zip/Postal Code </label>
									<div class="col-md-7">
										<input id="bt_postal" name="bt_postal" class="form-control input-md" type="text">
										<span class="help-block"></span>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 control-label text-center" for="bt_phone">Cellphone/Tel Number <span class='text-danger'>*</span></label>
									<div class="col-md-7">
										<input id="bt_phone" name="bt_phone" class="form-control input-md" type="text">
										<span class="help-block"></span>
									</div>
								</div>

							</fieldset>
							<div class="form-group">
								<div class="row">
									<div class="col-md-9"></div>
									<div class="col-md-3">
										<button type="button" class="btn btn-primary cashreceiveok">OK </button>
										<button type="button"  class="btn btn-primary cashreceivecancel">CANCEL </button>
									</div>
								</div>
							</div>

						</div>
						<div class="tab-pane" id="tab_d">
							<fieldset>
								<legend>Billing Information</legend>
								<div class="row">
									<table class="table" id="ch_table"></table>
								</div>
								<div class="row">
									<div class="row">
										<div class="col-md-6"><input id="ch_date" name="ch_date" placeholder="Maturity Date" class="form-control input-md" type="text"></div>
										<div class="col-md-6"><input id="ch_number" name="ch_number" placeholder="Cheque #" class="form-control input-md" type="text">
										</div>
										<hr />
									</div>
									<div class="row">
										<div class="col-md-6"><input id="ch_amount" name="ch_amount" placeholder="Amount" class="form-control input-md" type="text"></div>
										<div class="col-md-6"><input id="ch_bankname" name="ch_bankname" placeholder="Bank" class="form-control input-md" type="text"></div>
									</div>
									<hr />
									<div class="row">
										<div class="col-md-2"><input type="button" id='addcheque' class='btn btn-default' value='Add'/></div>
									</div>
								</div>
								<hr />

								<div class="form-group">
									<label class="col-md-3 control-label text-center" for="ch_firstname">First Name <span class='text-danger'>*</span></label>
									<div class="col-md-7">
										<input id="ch_firstname" name="ch_firstname" class="form-control input-md" type="text">
										<span class="help-block"></span>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 control-label text-center" for="ch_middlename">Middle Name </label>
									<div class="col-md-7">
										<input id="ch_middlename" name="ch_middlename" class="form-control input-md" type="text">
										<span class="help-block"></span>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 control-label text-center" for="ch_lastname">Last Name <span class='text-danger'>*</span></label>
									<div class="col-md-7">
										<input id="ch_lastname" name="ch_lastname" class="form-control input-md" type="text">
										<span class="help-block"></span>
									</div>
								</div>


								<div class="form-group">
									<label class="col-md-3 control-label text-center" for="ch_phone">Cellphone/Tel Number <span class='text-danger'>*</span></label>
									<div class="col-md-7">
										<input id="ch_phone" name="ch_phone" class="form-control input-md" type="text">
										<span class="help-block"></span>
									</div>
								</div>

							</fieldset>
							<div class="form-group">
								<div class="row">
									<div class="col-md-9"></div>
									<div class="col-md-3">
										<button type="button" class="btn btn-primary cashreceiveok">OK </button>
										<button type="button"  class="btn btn-primary cashreceivecancel">CANCEL </button>
									</div>
								</div>
							</div>

						</div>
						<div class="tab-pane" id="tab_e">

							<fieldset>
								<div class="form-group">
									<label class="col-md-3 control-label text-center" for="con_member">Member</label>
									<div class="col-md-9">
										<select name="con_member" id="con_member" class='form-control'>
										</select>
										<span class="help-block">Choose member name</span>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 control-label text-center" for="con_amount">Amount</label>
									<div class="col-md-9">
										<input id="con_amount" name="con_amount" class="form-control input-md" type="text">
										<span class="help-block">Amount In Peso</span>
									</div>
								</div>


							</fieldset>
							<div class="form-group">
								<div class="row">
									<div class="col-md-9"></div>
									<div class="col-md-3">
										<button type="button" class="btn btn-primary cashreceiveok">OK </button>
										<button type="button"  class="btn btn-primary cashreceivecancel">CANCEL </button>
									</div>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="tab_f">

							<fieldset>
								<div class="form-group">
									<label class="col-md-3 control-label text-center" for="con_member_freebies">Member</label>
									<div class="col-md-9">
										<select name="con_member_freebies" id="con_member_freebies" class='form-control'>
										</select>
										<span class="help-block">Choose member name</span>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 control-label text-center" for="con_amount_freebies">Amount</label>
									<div class="col-md-9">
										<input id="con_amount_freebies" name="con_amount_freebies" class="form-control input-md" type="text">
										<span class="help-block">Amount In Peso</span>
									</div>
								</div>
								<div class="form-group">
									<div id='lastholdcon' style='display:none;' class='col-md-12'>
										<h3>Last sold freebies</h3>
										<table id="lastsoldfree" class="table">
											<thead><tr><th>Barcode</th><th>Item Code</th><th>Description</th><th>Qty</th><th>Price</th><th>Discount</th><th>Total</th><th>Sold Date</th></tr></thead>
											<tbody></tbody>
										</table>
										<p id='lastsoldfreetotal'></p>
									</div>
								</div>


							</fieldset>
							<div class="form-group">
								<div class="row">
									<div class="col-md-9"></div>
									<div class="col-md-3">
										<button type="button" class="btn btn-primary cashreceiveok">OK </button>
										<button type="button"  class="btn btn-primary cashreceivecancel">CANCEL </button>
									</div>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="tab_g">

							<fieldset>
								<div class="form-group">
									<label class="col-md-3 control-label text-center" for="member_credit">Member</label>
									<div class="col-md-9">
										<input name="member_credit" id="member_credit" class='form-control'>

										<span class="help-block">Choose member name</span>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 control-label text-center" for="member_credit_amount">Amount</label>
									<div class="col-md-9">
										<input id="member_credit_amount" name="member_credit_amount" class="form-control input-md" type="text">
										<span class="help-block">Amount In Peso</span>
									</div>
								</div>


							</fieldset>
							<div class="form-group">
								<div class="row">
									<div class="col-md-9"></div>
									<div class="col-md-3">
										<button type="button" class="btn btn-primary cashreceiveok">OK </button>
										<button type="button"  class="btn btn-primary cashreceivecancel">CANCEL </button>
									</div>
								</div>
							</div>
						</div>
					</div><!-- tab content -->
				</div>

			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<script>
		$(document).ready(function() {
			var is_approve_liq = $('#is_approve_liq').val();
			$('#member_credit').select2({
				placeholder: 'Search member' , allowClear: true, minimumInputLength: 2,
				ajax: {
					url: '../ajax/ajax_json.php', dataType: 'json', type: "POST", quietMillis: 50, data: function(term) {
						return {
							q: term, functionName: 'members'
						};
					}, results: function(data) {
						return {
							results: $.map(data, function(item) {

								return {
									text: item.lastname ,
									slug: item.lastname,
									id: item.id
								}
							})
						};
					}
				}
			});
			var ajaxOnProgress = false;
			function formatItem(o) {
				if (!o.id)
					return o.text; // optgroup
				else {
					var r = o.text.split(':');
					return "<span> "+r[0]+"</span> <span style='margin-left:10px'>" + r[1] + "</span><span style='display:block;margin-top:5px;'  class='text-danger'><small class='testspanclass'>"+r[2]+"</small></span>";
				}
			}
			$("#select_item").select2({
					placeholder: 'Select an Item',
					formatResult: formatItem,
					formatSelection: formatItem,
					escapeMarkup: function(m) {
						return m;
					}
			});
			$("#select_member").select2({
				placeholder: 'Select Station'
			});
			$("#select_item").change(function(){

			});

			// cart function
			noItemInCart();
			clearPaymentLocal();
			function noItemInCart() {
				if(!$("#table_liquidation tbody").children().length) {
					$("#table_liquidation tbody").append("<td data-title='Remarks' colspan='3' id='noitem' style='padding-top:10px;' ><span class='text-danger'>NO ITEMS IN CART</span></td>");
				}
			}
			$('body').on('click', '.removeItem', function() {
				var row = $(this).parents('tr');
				var item_id = row.attr('data-item_id');
				var qty = row.children().eq(3).text();
				var tr = $("#main_table > tbody").find('tr#'+item_id);
				var prev_qty = tr.children().eq(3).text();
				var new_qty = parseInt(prev_qty) + parseInt(qty);
				tr.children().eq(3).text(new_qty);
				$(this).parents('tr').remove();
				noItemInCart();
				updateTotal();
				clearPaymentLocal();
				localStorage.removeItem('payment_srs');
			});

			$('#void').click(function() {
				if(confirm("Are you sure you want to clear the cart? ")){
					location.reload();
				}

			});

			$('#sold_date').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#sold_date').datepicker('hide');
			});

			$('#addtolist').click(function(){
				var item_id = $("#select_item").val();
				var item_code = $('#select_item :selected').text();
				var splitted = item_code.split(':');
				var member_id = $("#select_member").val();
				var member_name = $('#select_member :selected').text();
				var price_id = $('#select_item :selected').attr('data-price_id');
				var price = $('#select_item :selected').attr('data-price');
				var qty = $('#item_qty').val();
				var discount = $('#item_discount').val();
				var remarks = $('#remarks').val();
				var sr = $('#sr').val();
				var sold_date = $('#sold_date').val();
				if(!item_id){
					toastr.error("Invalid Item Name");
					return;
				}
				if(remarks){
					member_name = member_name + "<br><small>Remarks: <span class='text-danger'>"+remarks+"</span></small>";
				}
				if(!member_id){
					toastr.error("Invalid Station Name");
					return;
				}
				if(!sold_date){
					toastr.error("Please enter date sold");
					return;
				}
				if(!qty || isNaN(qty) ||  parseInt(qty) < 1){
					toastr.error("Quantity should be a number and must be greater than Zero.");
					return;
				}
				if(discount.indexOf("%") > 0){
					discount = parseFloat(discount)/100;
					discount = (parseFloat(price) * parseFloat(discount)) * parseFloat(qty);
				}
				if(!discount) discount = 0;
				if(isNaN(discount) ||  parseInt(discount) < 0 || (parseFloat(price) * parseFloat(qty)) < parseFloat(discount)){
					toastr.error("Discount should be a number and not greater than total amount.");
					return;
				}
				if(!sr || isNaN(sr) ||  parseInt(sr) < 1){
					toastr.error("SR should be a number");
					return;
				}
				if(itemAndMemberExists(item_id,member_id)){
					toastr.error("Item and Station already on the list...");
					return;
				}
				removeNoItemLabel();
				var tr = $("#main_table > tbody").find('tr#'+item_id);
				var prev_qty = tr.children().eq(3).text();
				var new_qty = parseInt(prev_qty) - parseInt(qty);
				if(new_qty < 0){
					toastr.error("Not enough item to liquidate");
					return;
				}
				
					tr.children().eq(3).text(new_qty);
			
				var total = (parseFloat(price) * parseFloat(qty)) - parseFloat(discount);
				$('#table_liquidation > tbody').append("<tr data-remarks='"+remarks+"' data-discount='"+discount+"' data-price_id="+price_id+" data-item_id="+item_id+" data-member_id="+member_id+" data-sr="+sr+" data-sold_date="+sold_date+" > <td data-title='Item'>" + splitted[2] + "</td><td data-title='Price'>" + number_format(price,2) + "</td><td data-title='Member'>" + member_name + "</td><td data-title='Qty'>"+qty+"</td><td data-title='Discount'>"+discount+"</td><td data-title='Total'>"+number_format(total,2)+"</td><td data-title='Sr'>"+sr+"</td><td data-title='Date'>"+sold_date+"</td><td data-title='Action'><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
				updateTotal();
				$("#select_item").select2("val", "");
			
				$('#item_qty').val('');
				$('#item_discount').val('');
				saveLocal();
			});

			function removeNoItemLabel() {
				$("#noitem").remove();
			}
			toastr.options = {
				"closeButton": false,
				"debug": false,
				"progressBar": false,
				"positionClass": "toast-bottom-right",
				"onclick": null,
				"showDuration": "300",
				"hideDuration": "1000",
				"timeOut": "5000",
				"extendedTimeOut": "1000",
				"showEasing": "swing",
				"hideEasing": "linear",
				"showMethod": "fadeIn",
				"hideMethod": "fadeOut"
			}

			function itemAndMemberExists(item,mem){
				var exists = false;
				$("#table_liquidation > tbody > tr").each(function(){
						var row = $(this);
						var item_id = row.attr('data-item_id');
						var mem_id = row.attr('data-member_id');
						if(item_id == item && mem_id == mem){
							exists =true;
						}
				});
				return exists;
			}
			$('body').on('click','#return',function(){
				if(confirm("Are you sure you want to RETURN this liquidation?")){
					var request_id =$("#request_id").val();
					$.ajax({
					    url:'../ajax/ajax_caravan.php',
					    type:'POST',
					    data: {functionName:'returnLiquidation',id:request_id},
					    success: function(data){
						    clearPaymentLocal();
						    location.href='manage_caravan.php';
					    },
					    error:function(){

					    }
					});
				}
			});
			$('#save').click(function(){
				var btncontext = $(this);
				var oldbtnval = btncontext.val();
				var request_id =$("#request_id").val();
				btncontext.attr('disabled',true);
				btncontext.val('Loading...');


				if(true) {
					if(confirm("Are you sure you want to submit this liquidation?")){
						$('.loading').show();
						var arrayPayment = ['payment_cash','payment_con','payment_con_freebies','payment_member_credit','payment_cheque','payment_bt','payment_credit'];
						var toliq = [];
						var soli = [];
						if(!localStorage['payment_srs'] && $("#table_liquidation > tbody > tr").length){
							alertify.alert('Invalid request');
							return;
						}
						if($("#table_liquidation > tbody > tr").length){
							var srs = JSON.parse(localStorage['payment_srs']);
							var payind = [];

							for(var i in srs){
								var hasPaymentSr = false;

								for(var j=0; j<arrayPayment.length; j++){
									var curkey = arrayPayment[j];
									var finalkey = curkey + "_"+srs[i];

									if(localStorage[finalkey]){
										hasPaymentSr = true;
										payind.push({
											sr : srs[i],
											payment: curkey,
											value: localStorage[finalkey]
										})
									}
								}
								if(!hasPaymentSr){
									alertify.alert("No payment on SR # " + srs[i]);
									btncontext.attr('disabled',false);
									btncontext.val(oldbtnval);
									$('.loading').hide();
									return;
								}

							}
							console.log(payind);



							$("#table_liquidation > tbody > tr").each(function(index){
								var row = $(this);
								var item_id = row.attr('data-item_id');
								var discount = row.attr('data-discount');
								var remarks = row.attr('data-remarks');
								toliq[index] = {
									item_id : item_id,
									price_id : row.attr('data-price_id'),
									member_id: row.attr('data-member_id'),
									sr: row.attr('data-sr'),
									sold_date: row.attr('data-sold_date'),
									qty: row.children().eq(3).text(),
									discount:discount,
									remarks:remarks
								};
							});
						}

						$("#main_table > tbody > tr").each(function(index){
							var row = $(this);
							var item_id = row.attr('id');
							var price_id = row.attr('data-price_id');
							var leftqty = row.children().eq(3).text();
							if(parseInt(leftqty) > 0) {
								soli[index] = {
									item_id: item_id,
									price_id: price_id,
									member_id: 0,
									qty: leftqty
								};
							}
						});
						soli = JSON.stringify(soli);
						toliq = JSON.stringify(toliq);
						if(ajaxOnProgress){
							return;
						} 
						ajaxOnProgress = true;


						$.ajax({
							url:'../ajax/ajax_liquidation.php',
							type:'POST',
							data: {request_id:request_id,toliq:toliq,soli:soli,finalPayment:JSON.stringify(payind)},
							success: function(data){
									$('#test').html(data);
									ajaxOnProgress = false;
									clearPaymentLocal();
									location.href='manage_caravan.php';
									btncontext.attr('disabled',false);
									btncontext.val(oldbtnval);
							},
							error: function(){
									alert('Something went wrong');
									ajaxOnProgress = false;
									btncontext.attr('disabled',false);
									btncontext.val(oldbtnval);
							}
						});
					}else {
						btncontext.attr('disabled',false);
						btncontext.val(oldbtnval);
					}
				} else {
					toastr.error("No items to liquidate");
					btncontext.attr('disabled',false);
					btncontext.val(oldbtnval);
				}
				saveLocal();
			});
			function updateTotal(){
				var totalarr = [] ;
				var srarr = [];
				$("#table_liquidation > tbody > tr").each(function(index){
						var row = $(this);
						var sr = row.children().eq(6).text();
						var t = parseFloat(replaceAll(row.children().eq(5).text(),',',''));
						t = (t) ? t :0;
						if(totalarr[sr]){
							totalarr[sr] = parseFloat(totalarr[sr]) + t;
						} else {
							totalarr[sr] = t;
							srarr.push(sr);
						}
					localStorage['payment_srs'] = JSON.stringify(srarr);
				});
				var retdis = '';
				for(var i in totalarr){
					var paymentButton = "<button id='btnPayment"+i+"' data-sr='"+i+"' class='btn btn-default getPayment'>Payment</button>";
					retdis += "<div style='margin-top:4px;'>SR " +i + ": <span class='text-danger'>" + number_format(totalarr[i],2) + "</span> "+paymentButton+"</div>";
				}
				$('#sdTotal').html(retdis);
			}
			$('body').on('click','.getPayment',function(){
				var prevval = replaceAll($(this).prev().html(),',','');
				var sr = $(this).attr('data-sr');

				$("#con_amount_freebies").val('');
				$("#con_amount").val('');
				$("#member_credit_amount").val('');
				 $("#hidcashpayment").val('');
				$("#hidcreditpayment").val('');
				 $("#hidbanktransferpayment").val('');
				 $("#hidchequepayment").val('');
				 $("#hidconsumablepayment").val('');
				$("#hidconsumablepaymentfreebies").val('');
				 $("#hidmembercredit").val('');
				updateCreditPayment();
				updateCashPayment();
				updateBankTransferPayment();
				updateChequePayment();
				updateConPayment();
				updateConPaymentFreebies();
				updateMemberCredit();
				$('#hid_sr_cur_payment').val(sr);
				$("#amountdue").html("<span style='font-size:1.2em;' class='text-info'><strong> Amount Due: " + prevval + "</strong></span>");
				$("#hidamountdue").val( replaceAll(prevval,',',''));
				$("#getpricemodal").modal("show");
				setTimeout(function() { $('#cashreceivetext').focus() }, 500);
			});
			$('body').on('click','#btnForApproval',function(){
				var request_id =$("#request_id").val();
				var cache_liquidation = localStorage['cache_liquidation'];
				var cache_payment = localStorage['cache_payment'];
				$.ajax({
				    url:'../ajax/ajax_caravan.php',
				    type:'POST',
				    data: {functionName:'caravanForApproval',id:request_id,cache_liquidation:cache_liquidation,cache_payment:cache_payment},
				    success: function(data){
					    clearPaymentLocal();
					    location.href='manage_caravan.php';
				    },
				    error:function(){

				    }
				});
			});
			function saveLocal(){
				var pendingMainTable = $('#main_table tbody').html();
				var liq_body = $('#table_liquidation tbody').html();
				var total_list = $('#sdTotal').html();
				var req_id = $('#request_id').val();
				var savedLocal = [];
					savedLocal.push({
						req_id:req_id,
						pendingMainTable: pendingMainTable,
						liq_body:liq_body,
						total_list:total_list
						}
					);
					savedLocal = JSON.stringify(savedLocal);
					localStorage['cache_liquidation'] = savedLocal;

			}
			checkPendingTransaction();
			function checkPendingTransaction(){
				var req_id = $('#request_id').val();
				if(is_approve_liq == 1 || is_approve_liq == -1){
					localStorage['cache_liquidation'] = $('#cache_liquidation').val();
					localStorage['cache_payment'] = $('#cache_payment').val();

				}

				if(localStorage['cache_liquidation']){
					if(req_id){
						var pendingRequest = JSON.parse(localStorage['cache_liquidation']);
						var pendingMainTable ='';
						var liq_body ='';
						var total_list ='';
						var hasPending = false;
						for(var i in pendingRequest){
							if(pendingRequest[i].req_id == req_id){
								pendingMainTable = pendingRequest[i].pendingMainTable;
								liq_body = pendingRequest[i].liq_body;
								total_list = pendingRequest[i].total_list;
								hasPending = true;
							}
						}
						if(hasPending){
							if(is_approve_liq == 1 || is_approve_liq == -1){
								$('#main_table tbody').html(pendingMainTable);
								$('#table_liquidation tbody').html(liq_body);
								$('#sdTotal').html(total_list);
								var arr_payment = [];
								try{
									if(localStorage['cache_payment']){
										arr_payment = JSON.parse(localStorage['cache_payment']);
										var srarr=[];


										for(var i in arr_payment){
											localStorage['payment_cash_'+arr_payment[i].sr] = arr_payment[i].payment_cash;
											localStorage['payment_con_'+arr_payment[i].sr] = arr_payment[i].payment_con;
											localStorage['payment_con_freebies_'+arr_payment[i].sr] = arr_payment[i].payment_con_freebies;
											localStorage['payment_member_credit_'+arr_payment[i].sr] = arr_payment[i].payment_member_credit;
											srarr.push(arr_payment[i].sr);
											$('#btnPayment'+arr_payment[i].sr).removeClass('btn-default');
											$('#btnPayment'+arr_payment[i].sr).addClass('btn-success');
										}
										localStorage['payment_srs'] = JSON.stringify(srarr);
									}
								} catch(e){

								}
							} else {
								alertify.confirm("You have unsaved request. Do you want to load it?", function (asc) {
									if (asc) {
										$('#main_table tbody').html(pendingMainTable);
										$('#table_liquidation tbody').html(liq_body);
										$('#sdTotal').html(total_list);
										var arr_payment = [];
										try{
											if(localStorage['cache_payment']){
												arr_payment = JSON.parse(localStorage['cache_payment']);
												for(var i in arr_payment){
													localStorage['payment_cash_'+arr_payment[i].sr] = arr_payment[i].payment_cash;
													localStorage['payment_con_'+arr_payment[i].sr] = arr_payment[i].payment_con;
													localStorage['payment_con_freebies_'+arr_payment[i].sr] = arr_payment[i].payment_con_freebies;
													localStorage['payment_member_credit_'+arr_payment[i].sr] = arr_payment[i].payment_member_credit;

													$('#btnPayment'+arr_payment[i].sr).removeClass('btn-default');
													$('#btnPayment'+arr_payment[i].sr).addClass('btn-success');
												}
											}
										} catch(e){

										}


									} else {

									}
								}, "");
							}

						}

					}
				}
			}
			$('#member_credit_amount').keyup(function (e) {

				if(!($('#member_credit').val())){
					showToast('error','<p>Please Choose '+MEMBER_LABEL+' First</p>','<h3>WARNING!</h3>','toast-bottom-right');
					$(this).val('');
					return;
				}

				if(isNaN($(this).val())){
					showToast('error','<p>Please Enter Valid Amount</p>','<h3>WARNING!</h3>','toast-bottom-right');
					$(this).val('');
					$(this).focus();
				}
				$("#hidmembercredit").val($(this).val());
				if(isValidAmount($(this).val(),false)){
					showToast('error','<p>Your payment exceeds to amount due.</p>','<h3>WARNING!</h3>','toast-bottom-right');
					$(this).val('');
				}
				$("#hidmembercredit").val($(this).val());
				updateMemberCredit();
			});
			function updateTotalPayment(){
				var cash = $("#cashreceivetext").val();
				if(!cash){
					cash=0;
				}
				var con_amount = $("#con_amount").val();
				if(!con_amount){
					con_amount=0;
				}
				var con_amount_freebies = $("#con_amount_freebies").val();
				if(!con_amount_freebies){
					con_amount_freebies=0;
				}
				var member_credit_amount = $("#member_credit_amount").val();
				if(!member_credit_amount){
					member_credit_amount=0;
				}
				var credit_amount = $("#hidcreditpayment").val();
				if(!credit_amount){
					credit_amount=0;
				}
				var bt_amount = $("#hidbanktransferpayment").val();
				if(!bt_amount){
					bt_amount=0;
				}
				var ck_amount = $("#hidchequepayment").val();
				if(!ck_amount){
					ck_amount=0;
				}
				var gtotal = parseFloat(cash) + parseFloat(con_amount) + parseFloat(con_amount_freebies) + parseFloat(member_credit_amount) + parseFloat(credit_amount) + parseFloat(bt_amount) + parseFloat(ck_amount);
				$("#totalOfAllPayment").html("<strong><span style='font-size:1.2em;' class='text-info' >Total Payment: " +gtotal + "</span></strong>");

			}
			function updateCashPayment(){
				var cash = $("#cashreceivetext").val();
				if(!cash){
					cash=0;
				}
				$("#totalcashpayment").html(cash);
				updateTotalPayment();
			}
			function updateConPayment(){
				var con_amount = $("#con_amount").val();
				if(!con_amount){
					con_amount=0;
				}
				$("#totalconsumablepayment").html(con_amount);
				updateTotalPayment();
			}
			function updateMemberCredit(){
				var member_credit_amount = $("#member_credit_amount").val();
				if(!member_credit_amount){
					member_credit_amount=0;
				}
				$("#totalmembercredit").html(member_credit_amount);
				updateTotalPayment();
			}
			function updateConPaymentFreebies(){
				var con_amount_freebies = $("#con_amount_freebies").val();
				if(!con_amount_freebies){
					con_amount_freebies=0;
				}

				$("#totalconsumablepaymentfreebies").html(con_amount_freebies);
				updateTotalPayment();
			}
			function updateCreditPayment(){
				var total = 0;
				if($("#credit_table tr").children().length ){
					$("#credit_table tr").each(function(index){
						var row = $(this);
						var amount = row.children().eq(1).text();
						total = parseFloat(total) + parseFloat(amount);
					});
				}
				$("#totalcreditpayment").html(total);
				$("#hidcreditpayment").val(total);
				updateTotalPayment();
			}
			function updateBankTransferPayment(){
				var total = 0;
				if($("#bt_table tr").children().length ){
					$("#bt_table tr").each(function(index){
						var row = $(this);
						var amount = row.children().eq(1).text();
						total = parseFloat(total) + parseFloat(amount);
					});
				}
				$("#totalbanktransferpayment").html(total);
				$("#hidbanktransferpayment").val(total);
				updateTotalPayment();
			}
			function updateChequePayment(){
				var total = 0;
				if($("#ch_table tr").children().length ){
					$("#ch_table tr").each(function(index){
						var row = $(this);
						var amount = row.children().eq(2).text();
						total = parseFloat(total) + parseFloat(amount);
					});
				}
				$("#totalchequepayment").html(total);
				$("#hidchequepayment").val(total);
				updateTotalPayment();
			}

			function hasItemCreditValidation(elem){
				if(!$("#credit_table tr").children().length ){
					showToast('error','<p>Please Add Credit Card First. </p>','<h3>WARNING!</h3>','toast-bottom-right');
					elem.val('');
				}
			}
			$("#billing_firstname, #billing_middlename, #billing_lastname, #billing_company, #billing_address, #billing_postal,#billing_phone,#billing_email,#billing_remarks").keyup(function(){
				hasItemCreditValidation($(this));
			});
			function hasItemBTValidation(elem){
				if(!$("#bt_table tr").children().length ){
					showToast('error','<p>Please Add Bank Transfer Data First. </p>','<h3>WARNING!</h3>','toast-bottom-right');
					elem.val('');
				}
			}
			$("#bt_bankto_name, #bt_bankto_account_number, #bt_firstname, #bt_middlename, #bt_lastname, #bt_company,#bt_address,#bt_postal,#bt_phone").keyup(function(){
				hasItemBTValidation($(this));
			});
			function hasItemChequeValidation(elem){
				if(!$("#ch_table tr").children().length ){
					showToast('error','<p>Please Add Cheque Data First. </p>','<h3>WARNING!</h3>','toast-bottom-right');
					elem.val('');
				}
			}

			$("#ch_firstname, #ch_middlename, #ch_lastname, #ch_phone").keyup(function(){
				hasItemChequeValidation($(this));
			});

			function isValidAmount(a,addme){
				var cash = $("#hidcashpayment").val();
				if(!cash) cash = 0;
				var credit = $("#hidcreditpayment").val();
				if(!credit) credit = 0;
				var banktransfer = $("#hidbanktransferpayment").val();
				if(!banktransfer) banktransfer = 0;
				var cheque = $("#hidchequepayment").val();
				if(!cheque) cheque = 0;
				var con_amount = $("#hidconsumablepayment").val();
				if(!con_amount) con_amount = 0;
				var con_amount_freebies = $("#hidconsumablepaymentfreebies").val();
				if(!con_amount_freebies) con_amount_freebies = 0;
				var member_credit_amount = $("#hidmembercredit").val();
				if(!member_credit_amount) member_credit_amount = 0;

				var grandtotal = parseFloat($("#hidamountdue").val());

				var currentNotCash =   parseFloat(credit) + parseFloat(banktransfer) + parseFloat(cheque) + parseFloat(con_amount) + parseFloat(con_amount_freebies)  + parseFloat(member_credit_amount);
				if(addme){
					currentNotCash = parseFloat(currentNotCash) + parseFloat(a);
				}
				if(parseFloat(currentNotCash) > parseFloat(grandtotal)){
					return true;
				} else {
					return false;
				}
			}
			function receivePayment(sr){
				var cash = $("#hidcashpayment").val();
				if(!cash) cash = 0;
				var credit = $("#hidcreditpayment").val();
				if(!credit) credit = 0;
				var banktransfer = $("#hidbanktransferpayment").val();
				if(!banktransfer) banktransfer = 0;
				var cheque = $("#hidchequepayment").val();
				if(!cheque) cheque = 0;
				var con_amount = $("#hidconsumablepayment").val();
				if(!con_amount) con_amount = 0;
				var con_amount_freebies = $("#hidconsumablepaymentfreebies").val();
				if(!con_amount_freebies) con_amount_freebies = 0;
				var member_credit_amount = $("#hidmembercredit").val();
				if(!member_credit_amount) member_credit_amount = 0;

				var totalpayment = parseFloat(cash) + parseFloat(credit) + parseFloat(banktransfer) + parseFloat(cheque) + parseFloat(con_amount) + parseFloat(con_amount_freebies) + parseFloat(member_credit_amount);
				var grandtotal = parseFloat($("#hidamountdue").val());



				if(parseFloat(totalpayment) < 0 || parseFloat(totalpayment) < parseFloat(grandtotal)) {

					showToast('error','<p>Invalid payment</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;
				} else {
					if(!isValidFormCheque(sr) || !isValidFormCredit(sr) || !isValidFormBankTransfer(sr) ){
						return;
					}
					var arr_payment = [];
					if(localStorage['cache_payment']){
						try{
							arr_payment = JSON.parse(localStorage['cache_payment']);
						} catch(e){
							console.log("cache payment error");
						}

					}
					var change = parseFloat(totalpayment) - parseFloat(grandtotal);
					cash = parseFloat(cash) - parseFloat(change);
					localStorage['payment_cash_'+sr] = cash;
					localStorage['payment_con_'+sr] = con_amount;
					localStorage['payment_con_freebies_'+sr] = con_amount_freebies;
					localStorage['payment_member_credit_'+sr] = member_credit_amount;

					arr_payment.push({
						sr:sr,payment_cash: cash, payment_con: con_amount, payment_con_freebies: con_amount_freebies,payment_member_credit: member_credit_amount
					});
					localStorage['cache_payment'] = JSON.stringify(arr_payment);

					if(con_amount){
						$("#opt_member").select2('val',$("#con_member").val());
					}
					if(con_amount_freebies){
						$("#opt_member").select2('val',$("#con_member_freebies").val());
					}
					if(member_credit_amount){
						$("#opt_member").select2('val',$("#member_credit").val());
					}


					$("#credit_table").find("tr").remove();
					$("#bt_table").find("tr").remove();
					$("#ch_table").find("tr").remove();
					$("#tab_d :input[type='text']").val('');
					$("#tab_c :input[type='text']").val('');
					$("#tab_b :input[type='text']").val('');
					$("#tab_a :input[type='text']").val('');
					$('#getpricemodal').modal("hide");
					$('#btnPayment'+sr).removeClass('btn-default');
					$('#btnPayment'+sr).addClass('btn-success');
				}
			}
			$('body').on('click','.cashreceiveok',function(){
				var sr = $('#hid_sr_cur_payment').val();
				receivePayment(sr);
			});
			function isValidFormCheque(sr){
				if($("#ch_table tr").children().length ){
					var chequeArray = new Array();
					var fn = $("#ch_firstname").val();
					var mn = $("#ch_middlename").val();
					var ln = $("#ch_lastname").val();
					var phone = $("#ch_phone").val();
					if(false){
						showToast('error','<p>Please Complete Cheque billing form. </p>','<h3>WARNING!</h3>','toast-bottom-right');
						return false;
					} else {
						if(fn && !isAlphaNumeric(fn)){
							showToast('error','<p>First name should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(mn && !isAlphaNumeric(mn)){
							showToast('error','<p>Middle name should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(ln && !isAlphaNumeric(ln)){
							showToast('error','<p>Last name should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(phone && !isAlphaNumeric(phone)){
							showToast('error','<p>Phone should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						$("#ch_table tr").each(function(index){
							var row = $(this);
							chequeArray[index] = {
								date : row.children().eq(0).text(),
								cheque_number : row.children().eq(1).text(),
								amount:  row.children().eq(2).text(),
								bank_name:  row.children().eq(3).text(),
								firstname : fn,
								lastname: ln,
								middlename : mn,
								phone: phone
							}
						});
						localStorage['payment_cheque_'+sr] = JSON.stringify(chequeArray);
						return true;
					}
				}
				return true;
			}

			function isValidFormCredit(sr){
				if($("#credit_table tr").children().length ){
					var creditArray = new Array();
					var fn = $("#billing_firstname").val();
					var mn = $("#billing_middlename").val();
					var ln = $("#billing_lastname").val();
					var comp = $("#billing_company").val();
					var  add = $("#billing_address").val();
					var  postal = $("#billing_postal").val();
					var  phone = $("#billing_phone").val();
					var  email = $("#billing_email").val();
					var  rem = $("#billing_remarks").val();
					if(false ){
						showToast('error','<p>Please Complete Credit Card billing form. </p>','<h3>WARNING!</h3>','toast-bottom-right');
						return false;
					} else {
						if(ln && !isAlphaNumeric(ln)){
							showToast('error','<p>Last name should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(fn && !isAlphaNumeric(fn)){
							showToast('error','<p>First name should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(mn && !isAlphaNumeric(mn)){
							showToast('error','<p>Middle name should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(comp && !isAlphaNumeric(comp)){
							showToast('error','<p>Company should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(add && !isAlphaNumeric(add)){
							showToast('error','<p>Address should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(postal && !isNumeric(postal)){
							showToast('error','<p>Postal should be numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(phone && !isAlphaNumeric(phone)){
							showToast('error','<p>Address should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(email && !isEmail(email)){
							showToast('error','<p>Email should be valid email address</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(rem && !isAlphaNumeric(rem)){
							showToast('error','<p>Remarks should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						$("#credit_table tr").each(function(index){
							var row = $(this);
							creditArray[index] = {
								credit_number : row.children().eq(0).text(),
								amount:  row.children().eq(1).text(),
								bank_name:  row.children().eq(2).text(),
								firstname : fn,
								lastname: ln,
								middlename : mn,
								phone: phone,
								comp: comp,
								add: add,
								postal:postal,
								email:email,
								remarks:rem
							}
						});
						localStorage['payment_credit_'+sr] = JSON.stringify(creditArray);
						return true;
					}

				}
				return true;
			}
			function isValidFormBankTransfer(sr){
				if($("#bt_table tr").children().length ){
					var bankTransferArray = new Array();
					var bt_bankto_name = $("#bt_bankto_name").val();
					var bt_bankto_account_number = $("#bt_bankto_account_number").val();
					var fn = $("#bt_firstname").val();
					var mn = $("#bt_middlename").val();
					var ln = $("#bt_lastname").val();
					var comp = $("#bt_company").val();
					var  add = $("#bt_address").val();
					var  postal = $("#bt_postal").val();
					var  phone = $("#bt_phone").val();

					if(false){
						showToast('error','<p>Please Bank Transfer  billing form. </p>','<h3>WARNING!</h3>','toast-bottom-right');
						return false;
					} else {
						if(bt_bankto_name && !isAlphaNumeric(bt_bankto_name)){
							showToast('error','<p>Bank name should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(bt_bankto_account_number && !isAlphaNumeric(bt_bankto_account_number)){
							showToast('error','<p>Bank account number should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(fn && !isAlphaNumeric(fn)){
							showToast('error','<p>First name should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(mn && !isAlphaNumeric(mn)){
							showToast('error','<p>Middle name should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(ln &&!isAlphaNumeric(ln)){
							showToast('error','<p>Last name should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(comp && !isAlphaNumeric(comp)){
							showToast('error','<p>Company should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(add && !isAlphaNumeric(add)){
							showToast('error','<p>Address should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(postal && !isNumeric(postal)){
							showToast('error','<p>Phone should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(phone && !isAlphaNumeric(phone)){
							showToast('error','<p>Phone should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						$("#bt_table tr").each(function(index){
							var row = $(this);
							bankTransferArray[index] = {
								credit_number : row.children().eq(0).text(),
								amount:  row.children().eq(1).text(),
								bank_name:  row.children().eq(2).text(),
								bt_bankto_name:bt_bankto_name,
								bt_bankto_account_number:bt_bankto_account_number,
								firstname : fn,
								lastname: ln,
								middlename : mn,
								phone: phone,
								comp: comp,
								add: add,
								postal:postal
							}
						});
						localStorage['payment_bt_'+sr] = JSON.stringify(bankTransferArray);
						return true;
					}
				}
				return true;
			}

			function clearPaymentLocal(){

				for(var key in localStorage){
					var localKey =key;
					if(localKey.indexOf('payment_') > -1){
						localStorage.removeItem(localKey);
					}
				}
				localStorage.removeItem('cache_payment');
			}
			function isAlphaNumeric(str){
				var rexp = /^[\w\-\s\.,Ò—]+$/
				if(rexp.test(str)){
					return true;
				} else {
					return false;
				}
			}
			function validateDate(testdate) {
				var date_regex = /^(0[1-9]|1[0-2])\/(0[1-9]|1\d|2\d|3[01])\/(19|20)\d{2}$/
				return date_regex.test(testdate);
			}
			function isNumeric(str){
				var rexp = /^[0-9]+$/
				if(rexp.test(str)){
					return true;
				} else {
					return false;
				}
			}
			function isEmail(str){
				var rexp = /^[\w\.-_\+]+@[\w-]+(\.\w{2,3})+$/
				if(rexp.test(str)){
					return true;
				} else {
					return false;
				}
			}
			$('#cashreceivetext').keyup(function (e) {
				if(isNaN($(this).val())){
					showToast('error','<p>Please Enter Valid Amount</p>','<h3>WARNING!</h3>','toast-bottom-right');
					$(this).val('');
					$(this).focus();
				}
				$("#hidcashpayment").val($(this).val());
				updateCashPayment();
			});
			$('#addcreditcard').click(function(){
				var bl_cardnumber = $('#billing_cardnumber').val();
				var bl_bank = $('#billing_bankname').val();
				var bl_amount = $('#billing_amount').val();
				if(!bl_cardnumber){
					showToast('error','<p>Please indicate card number</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;
				}
				if(!bl_amount){
					showToast('error','<p>Please indicate amount</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;
				}
				if(isNaN(bl_amount)){
					showToast('error','<p>Please indicate a valid amount</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;
				}
				if(!bl_bank){
					showToast('error','<p>Please indicate bank name</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;
				}
				if(isValidAmount(bl_amount,true)){
					showToast('error','<p>Your payment exceeds to amount due.</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return ;
				}
				$("#credit_table").append("<tr><td>"+bl_cardnumber+"</td><td>"+bl_amount+"</td><td>"+bl_bank+"</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
				$('#billing_cardnumber').val('');
				$('#billing_bankname').val('');
				$('#billing_amount').val('');
				updateCreditPayment();
			});
			$('#addbanktransfer').click(function(){
				var bt_cardnumber = $('#bankfrom_account_number').val();
				var bt_bank = $('#bankfrom_name').val();
				var bt_amount = $('#bt_amount').val();
				if(!bt_cardnumber){
					showToast('error','<p>Please indicate card number</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;
				}
				if(!bt_amount){
					showToast('error','<p>Please indicate amount</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;
				}
				if(isNaN(bt_amount)){
					showToast('error','<p>Please indicate a valid amount</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;
				}
				if(parseFloat(bt_amount) < 1){
					showToast('error','<p>Amount should be greater than Zero</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;
				}
				if(!bt_bank){
					showToast('error','<p>Please indicate bank name</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;
				}
				if(isValidAmount(bt_amount,true)){
					showToast('error','<p>Your payment exceeds to amount due.</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return ;
				}
				$("#bt_table").append("<tr><td>"+bt_cardnumber+"</td><td>"+bt_amount+"</td><td>"+bt_bank+"</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
				$('#bankfrom_account_number').val('');
				$('#bankfrom_name').val('');
				$('#bt_amount').val('');
				updateBankTransferPayment();
			});
			$('#addcheque').click(function(){
				var ch_date = $('#ch_date').val();
				var ch_number = $('#ch_number').val();
				var ch_amount = $('#ch_amount').val();
				var ch_bankname = $('#ch_bankname').val();
				if(!ch_date){
					showToast('error','<p>Please indicate date</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;
				}
				if(!ch_number){
					showToast('error','<p>Please indicate card number</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;
				}
				if(!ch_amount){
					showToast('error','<p>Please indicate amount</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;
				}
				if(!validateDate(ch_date)){
					showToast('error','<p>Invalid Date Format. It should be mm/dd/yyyy (Ex. 01/01/2014) </p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;
				}
				if(isNaN(ch_amount)){
					showToast('error','<p>Please indicate a valid amount</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;
				}
				if(parseFloat(ch_amount) < 1){
					showToast('error','<p>Amount should be greater than Zero</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;
				}
				if(!ch_bankname){
					showToast('error','<p>Please indicate bank name</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;
				}
				if(isValidAmount(ch_amount,true)){
					showToast('error','<p>Your payment exceeds to amount due.</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return ;
				}
				$("#ch_table").append("<tr><td>"+ch_date+"</td><td>"+ch_number+"</td><td>"+ch_amount+"</td><td>"+ch_bankname+"</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
				$('#ch_date').val('');
				$('#ch_number').val('');
				$('#ch_amount').val('');
				$('#ch_bankname').val('');
				updateChequePayment();
			});
		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>