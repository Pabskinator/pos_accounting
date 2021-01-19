<?php
	// $user have all the properties and method of the current user
	require_once '../includes/admin/page_head2.php';

	$crud_cls = new Crud();
	$salestypelist = $crud_cls->get_active('salestypes',['company_id','=',$user->data()->company_id]);
	$userlist = $crud_cls->get_active('users',['company_id','=',$user->data()->company_id]);

	/* test */

	$layout = new Barcode();
	$cur = $layout->get_print_layout($user->data()->id,'invoice');
	$format = json_decode($cur->layout);
	$final = [];

	foreach($format as $f){
		$final[$f->order] = $f;
	}

	ksort($final);

?>

	<input type="hidden" id='txtLayout' value='<?php  echo json_encode($final); ?>'>
	<input type="hidden" id='DR_LABEL' value='<?php echo DR_LABEL; ?>'>
	<input type="hidden" id='INVOICE_LABEL' value='<?php echo INVOICE_LABEL; ?>'>
	<input type="hidden" id='PR_LABEL' value='<?php echo PR_LABEL; ?>'>
	<input type="hidden" value="<?php echo $thiscompany->name; ?>" id='co_name'>
	<input type="hidden" value="<?php echo $thiscompany->description; ?>" id='co_desc'>
	<input type="hidden" value="<?php echo  $thiscompany->address; ?>" id='co_address'>
	<!-- Page content -->
	<div id="page-content-wrapper">
		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div id="all_content" style='display:none;'>
				<div id="test_con"></div>

				<div class="row">
					<div class="col-md-6">
						<div class="panel panel-default">
							<div class="panel-body">
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<input id='member_id' type="text" style='width:100%;'>
										</div>
									</div>
									<div class="col-md-12">
										<div class="form-group">
											<input id='item_adjustment' type="text" class='form-control' placeholder='Enter adjustment'>
											<span class='help-block'>Negative value for discount.</span>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<input id='qty' type="text" placeholder='Quantity' class='form-control'>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<input id='item_id' type="text" class='selectitem'>
										</div>
									</div>


								</div>

								<div id="lastSold">

								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="panel panel-default">

							<div class="panel-body">
								<div class="row">
									<div class="col-md-4">
										<input type="text" class='form-control' id='custom_invoice' placeholder='<?php echo INVOICE_LABEL; ?>'>
										<span class='help-block'>Use to override next <?php echo INVOICE_LABEL; ?></span>
									</div>
									<div class="col-md-4">
										<input type="text" class='form-control' id='custom_dr' placeholder='<?php echo DR_LABEL; ?>'>
										<span class='help-block'>Use to override next <?php echo DR_LABEL; ?></span>
									</div>
									<div class="col-md-4">
										<input type="text" class='form-control' id='custom_ir' placeholder='<?php echo PR_LABEL; ?>'>
										<span class='help-block'>Use to override next <?php echo PR_LABEL; ?></span>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<input type="text" class='form-control' id='custom_date_sold' placeholder='Date Sold'>
										<span class='help-block'>Date Sold (Optional)</span>
									</div>

								</div>
								<div id="no-more-tables">
									<table id='cart' class='table noselect' style='margin-top:4px;' >
										<thead>
										<tr>
											<th>PRODUCT</th>
											<th>QTY</th>
											<th>PRICE</th>
											<th>ADJUSTMENT</th>
											<th>TOTAL</th>
											<th></th>
										</tr>
										</thead>
										<tbody>

										</tbody>
									</table>
								</div>
								<hr>
								<div class="row">
									<div class="col-md-4">
										<p class='text-danger' style='font-weight:bold;' id='nextInvoicenumber'></p>
									</div>
									<div class="col-md-4">
										<p class='text-danger' style='font-weight:bold;' id='nextDrnumber'></p>
									</div>
									<div class="col-md-4">
										<p class='text-danger' style='font-weight:bold;' id='nextIrnumber'></p>
									</div>
								</div>

								<div class="row">

									<div class="col-md-12">
										<div class="row">
											<div class="col-md-3 text-left" ><strong>SUB TOTAL:</strong></div>
											<div class="col-md-3 text-danger"  id='subtotalholder'>0</div>
											<div class="col-md-3 "></div>
											<div class="col-md-3"></div>

										</div>
										<div class="row">
											<div class="col-md-3 text-left"><strong>VAT:</strong></div>
											<div class="col-md-3 text-danger"   id='vatholder'>0</div>
											<div class="col-md-3 text-left"><strong>RECEIVED</strong></div>
											<div class="col-md-3 text-danger"   id='cashreceiveholder'>0</div>
										</div>
										<div class="row">
											<div class="col-md-3 text-left"><strong>TOTAL:</strong></div>
											<div class="col-md-3 text-danger"  style='font-weight: bold;'   id='grandtotalholder'>0</div>
											<div class="col-md-3 text-left"><strong>CHANGE</strong></div>
											<div class="col-md-3 text-danger"   style='font-weight: bold;' id='changeholder'>0</div>
										</div>
										<div class="row" style='display:none;' id='op_label_holder'>
											<div class="col-md-3 text-left"><strong>OVER PAYMENT:</strong></div>
											<div class="col-md-3 text-danger"  style='font-weight: bold;'   id='op_grandtotalholder'>0</div>
										</div>
									</div>
								</div>
								<hr />
								<div class="row">
									<div class="form-group">
										<div class="col-md-3 text-right">Receipt Type</div>
										<div class="col-md-3">
											<label class="radio-inline" for="checkInvoice">
												<input name="checkType" id="checkInvoice" value="1"  type="checkbox" >
												<?php echo INVOICE_LABEL; ?>
											</label>
										</div>
										<div class="col-md-3">
											<label class="radio-inline" for="checkDR">
												<input name="checkType" id="checkDR" value="2" type="checkbox" >
												<?php echo DR_LABEL; ?>
											</label>
										</div>
										<div class="col-md-3">
											<label class="radio-inline" for="checkIR">
												<input name="checkType" id="checkIR" value="3" type="checkbox" checked="checked">
												<?php echo PR_LABEL; ?>
											</label>
										</div>
									</div>
								</div>
								<!-- end of row 2-->		<!--  start of button row-->						<br>
								<div class="form-group">
									<select name="sales_type" id="sales_type" class='form-control'>
										<option value=""></option>
										<?php if($salestypelist){
											foreach($salestypelist as $sl){
												?>
												<option <?php echo ($sl->is_default == 1) ? 'selected' : ''; ?> value="<?php echo $sl->id?>"><?php echo $sl->name; ?></option>
												<?php
											}
										}?>
									</select>
								</div>
								<div class="form-group">
									<select name="agent_id" id="agent_id" class='form-control'>
										<option value=""></option>
										<?php if($userlist){
											foreach($userlist as $ul){
												?>
												<option  value="<?php echo $ul->id?>"><?php echo ucwords($ul->lastname . ", " . $ul->firstname . " " . $ul->middlename); ?></option>
												<?php
											}
										}?>
									</select>
								</div>
								<div class="form-group">
									<input type="text" class='form-control' id='remarks' placeholder='Remarks'>
								</div>
								<div class="form-group">
									<input type="text" class='form-control' id='sales_po_number' placeholder='PO Number'>
								</div>
								<input type="hidden" class="form-control" id='print_copy' value='<?php echo Configuration::getValue('rec_num_copy'); ?>'>
								<input type="hidden" class="form-control" id='news_print' value=''>

								<div class="form-group">
									<div class="row">
										<div class="col-md-8">
											<button id='btnSync' class='btn btn-default'>Sync</button>
											<button id='btnOverPayment' class='btn btn-default'>OP</button>
											<div id='point_holder'></div>
										</div>
										<div class="col-md-4">
											<input type="button" disabled id='checkout' value='CHECK OUT' class='btn btn-danger' />
											<input type="button" disabled id='print' value='PRINT' class='btn btn-success' />
										</div>
									</div>
								</div>
								<div class="form-group">
									<input type="button"  id='test_print' value='TEST PRINT' class='btn btn-warning' />
								</div>
							</div>
						</div>

					</div>
				</div>

			</div>
			<div id="error_content" style='display:none;'>
				<div class="alert alert-info"> Please assign this computer as terminal</div>
			</div>
		</div> <!-- end page content wrapper-->
		<div class="modal fade" id="getpricemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
			<div class="modal-dialog" style='width:95%'>
				<div class="modal-content">
					<div class="modal-body">

						<div id='paymethods'>
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
							<div  class='text-right'>
							<button style='' id='use_user_overpayment' class='btn btn-default btn-sm'>Credit</button>
							</div>
						</div>
						<hr>
						<ul class="nav nav-tabs">
							<li class="active"><a href="#tab_a" data-toggle="tab">Cash <span id='totalcashpayment' class='badge'></span></a></li>
							<li class='notcashlist'><a href="#tab_b" data-toggle="tab">Credit Card <span id='totalcreditpayment' class='badge'></span></a></li>
							<li class='notcashlist'><a href="#tab_c" data-toggle="tab">Bank Transfer <span id='totalbanktransferpayment' class='badge'></span></a></li>
							<li class='notcashlist'><a href="#tab_d" data-toggle="tab">Check 	<span id='totalchequepayment' class='badge'></span></a></li>
							<li class='notcashlist'><a href="#tab_e" data-toggle="tab">Consumable Amount <span id='totalconsumablepayment' class='badge'></span> </a></li>
							<li class='notcashlist'><a href="#tab_f" data-toggle="tab">Consumable Freebies <span id='totalconsumablepaymentfreebies' class='badge'></span> </a></li>
							<li class='notcashlist'><a href="#tab_g" data-toggle="tab">Credit <span id='totalmembercredit' class='badge'></span> </a></li>
							<li class='notcashlist'><a href="#tab_h" data-toggle="tab">Deduction <span id='totalmemberdeduction' class='badge'></span> </a></li>

						</ul>
						<div class="tab-content">
							<br><br>
							<?php include 'includes/payment_module.php'; ?>
						</div><!-- tab content -->
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
							<?php include "includes/modal_op.php"; ?>
						</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
	</div>
	<script src='../js/pos3.js?v=123'></script>
<?php require_once '../includes/admin/page_tail2.php'; ?>