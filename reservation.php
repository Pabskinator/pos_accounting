<?php require_once 'includes/page_head.php'; ?>
<div class="loading" style=''>Loading&#8230;</div>
<div id="allcontent" style='display:none;'>
<div class="navbar-inverse" >
	<nav class="navbar navbar-inverse" role="navigation" style='z-index:101;'>

		<div class="container-fluid">

			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="admin/main.php" style='font-size:1.2em; word-spacing: -12px; color:#fff' id='showMenu'><span id='postitle' class='glyphicon glyphicon-shopping-cart online'> POS SYSTEM</span></a>
			</div>
			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

				<ul class="nav navbar-nav">
					<li id='mainposnav' style='display:none;' ><a href="index.php">Home</a></li>
					<li  id='saleshistorynav' style='display:none;' ><a href="sales.php">Sales History</a></li>
					<li id='reservationnav' style='display:none;' ><a href="reservation.php">Reservation</a></li>
					<li id='reservedordernav' style='display:none;' ><a href="reserved_order.php">Reserved Order</a></li>
					<li id='shoutnav' style='display:none;' ><a href="shoutbox/index.html">Message(<span id='ctrshout'>0</span>)</a></li>
				</ul>
				<ul class="nav navbar-nav navbar-right" >
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" style='color:#fff;' ><span class='glyphicon glyphicon-user'></span> HI, <span id='currentuserfullname'></span>  <span id='isonline'></span> <span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu">
							<li><a href="#">User Settings</a></li>
							<li class="divider"></li>
							<li><a href="#" id="logout">Log Out</a></li>
						</ul>
					</li>
				</ul>
			</div><!-- /.navbar-collapse -->

		</div><!-- /.container-fluid -->

	</nav>
</div>
<div class="container" id='mainCon' style='display:none;'>
	<div class="row">
		<div class="col-md-12"><h3>Reservation</h3></div>




	<div class="row">

		<div class="col-md-3">
			<div class="form-group">
			<select name="bid" id="bid" class='form-control'>
				<option value=""></option>
			</select>
			</div>

		</div>
		<div class="col-md-3">
			<div class="form-group">
			<select class='form-control' id='salestype' >
				<option></option>
			</select>
				</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
			<select class='form-control' id='searchMember' >
				<option></option>

			</select>
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
			<select class='form-control' id='memberStation' >
				<option></option>
			</select>
				</div>
		</div>
		</div>

	<div class="row">
		<div class="col-md-3">
			<div class="form-group">
				<input type="text" class='form-control' id='r_remarks' placeholder='Remarks (Optional)'>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
			<select class='form-control' id='searchOrder'>
				<option></option>
			</select>
			</div>
		</div>
		<div class="col-md-3">
			<div class="hidden-xs">
			<div id="imagecon" style='left:0px;top:0px;'>
				<span style='cursor:pointer; position:absolute;right:2px;top:2px' class='glyphicon glyphicon-remove removeImage'></span>
				<img src="" alt="Image" />
				<br>
			</div>
			</div>
			<div class="form-group">
			<button class='btn btn-default' id='addorder'><span class='glyphicon glyphicon-plus'></span> ADD ITEM</button>
			</div>
			</div>

	</div>
	<!-- End Row 1-->		<!-- Start Row 2 -->
	<div class="row">
		<div class="col-md-12">
			<br />
			<div id="no-more-tables">
			<table id='cart' class='table'>
				<thead>
				<tr>
					<th>Barcode</th>
					<th>Item</th>
					<th>Qty</th>
					<th>Price</th>
					<th>Discount</th>
					<th>Total</th>
					<TH></TH>
					<th></th>
					<th></th>
				</tr>
				</thead>

				<tbody>

				</tbody>

			</table>
			</div>
			<hr>
			<div class="row">
				<div class="col-md-6">
					<div class="row">
						<div class="col-md-6">
							Grand Total
						</div>
						<div class="col-md-6">
							<p class='text-danger' style='font-weight: bold;' id='grandtotalholder'>0</p>
						</div>
						<div class="col-md-6">
							Receive
						</div>
						<div class="col-md-6">
							<p class='text-danger' style='font-weight: bold;' id='cashreceiveholder'>0</p>
						</div>
						<div class="col-md-6">
							Change
						</div>
						<div class="col-md-6">
							<p class='text-danger' style='font-weight: bold;' id='changeholder'>0</p>
						</div>
					</div>
				</div>
				<div class="col-md-6">

				</div>
			</div>



		</div>
	</div>
	<div class="row">
		<div class="row">

		</div>

	</div>
	<!-- end of row 2-->		<!--  start of button row-->
	<div class="row">
		<div class="col-md-8">
		</div>
		<div class="col-md-4 text-right">
			<p><span id='withpayment' style='margin-top:10px;' class='label label-primary'>WITHOUT PAYMENT</span></p>
			<input type="button" id='void' value='VOID' class='btn btn-danger' />
			<button class='btn btn-warning' id='checkout'>PAYMENT</button>
			<input type="button" id='save' value='SAVE' class='btn btn-success' />
		</div>
	</div>

	<div id="test"></div>

	</div>
	<div class="modal fade" id="modalStocks" tabindex="-1" role="dialog" aria-labelledby="modalStocksLabel" aria-hidden="true" >
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title">Stocks</h3>
				</div>
				<div class="modal-body" id='sbody'>

				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title">Branch</h3>

				</div>
				<div class="modal-body">
					<form class="form-horizontal">
						<fieldset>
							<div class="form-group">
								<label class="col-md-4 control-label" for="a_all_qty">Ordered Quantity</label>
								<div class="col-md-8" >
									<input type="hidden" id='a_all_qty' />
									<input type="hidden" id='a_item_id' />
									<span id='a_all_qty_span'></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="a_branch_id">Select Branch</label>
								<div class="col-md-8" id=''>
									<select name="a_branch_id" id="a_branch_id" class='form-control'>
										<option value=""></option>
									</select>
								</div>
							</div>

							<!-- Select Basic -->
							<div class="form-group">
								<label class="col-md-4 control-label" for="a_qty">Quantity</label>
								<div class="col-md-8" >
									<input type="text" class='form-control' id='a_qty' placeholder='Quantity'/>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="a_qty"></label>
								<div class="col-md-8" >
									<p id='a_remarks' class='text-danger'></p>
								</div>
							</div>
						</fieldset>
					</form>

				</div>
				<div class="modal-footer">
					<button type="button" id='saveAllocatedBranch' class="btn btn-primary">Save </button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
<div class="modal fade" id="multiplessModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
	<div class="modal-dialog" style='width:90%;' >
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 class="modal-title"></h3>
			</div>
			<div class="modal-body">
				<div class="panel panel-default">
					<div class="panel-body">
						<input type="hidden" id='tridforss' />
						<input type="hidden" id='ind_multiple_ss_qty'/>
						<p id='multiple_ss_qty_span'></p>
						<p id='multiple_ss_toallocateqty_span'></p>
						<div class="row">
							<div class="col-md-3">
								<input type="text" placeholder='Quantity' class='form-control' id='multiple_ss_qty'/>
							</div>
							<div class="col-md-3">
								<select name="ind_station_select2" id="ind_station_select2" class='form-control' style=''>


								</select>
							</div>
							<div class="col-md-3">
								<select name="selectSalesType2" id="selectSalesType2" class='form-control' style=''>

								</select>
							</div>
							<div class="col-md-3">
								<button  class='btn btn-default' id='ind_multiple_ss_addmore'>
									<span class='glyphicon glyphicon-plus'></span> Add
								</button>
							</div>
						</div>
						<div class="row">
							<br>
							<div class="container-fluid">
								<table class='table' id='ind_multiple_ss_tbl' style='display:none;'>
									<thead>
									<tr><th>Quantity</th><th>Station Name</th><th>Sales Type</th><th></th></tr>
									</thead>
									<tbody>

									</tbody>
								</table>
							</div>

						</div>
						<hr />
						<div class="text-right">	<button type="button" id='ind_multiple_ss_ok' class="btn btn-primary"><span class='glyphicon glyphicon-floppy-save'></span> SAVE</button></div>
					</div>
				</div>
			</div>




		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

	<!-- PAYMENT START-->
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
										<select name="member_credit" id="member_credit" class='form-control'>
										</select>
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
	<!-- PAYMENT END -->
</div>
<script>
$(function(){
	$('.loading').hide();
	$('#allcontent').fadeIn();
	if(localStorage["company_name"]){
		$('#postitle').html(localStorage["company_name"].toUpperCase());
	}
	getReservationItem(localStorage["company_id"],function(){
		getReserveOpt();
	});
	$('#mainCon').fadeIn();

	if(localStorage["current_id"] != null){
		// set a welcome page if id is set
		$("#currentuserfullname").empty();
		$("#currentuserfullname").append(localStorage["current_lastname"].toUpperCase() +", "+ localStorage["current_firstname"].toUpperCase() + "-" + localStorage["terminal_name"] + "");
		if(permissions.mainpos){
			$('#mainposnav').show();
		}
		if(permissions.mainpos_sr){
			$('#saleshistorynav').show();
		}
		if(permissions.mainpos_ar){
			$('#reservationnav').show();
		}
		if(permissions.mainpos_mr){
			$('#reservedordernav').show();
		}
		if(localStorage['company_id'] == 14){
			$('#shoutnav').show();
		}
	} else {
		// redirect to login if not set
		location.href="login.php";
	}

	getMembers(localStorage['company_id']);
	getSalesTypeAx(localStorage['company_id']);
	getAllStations(localStorage['company_id']);
	getBranches(localStorage['company_id']);

	function getReserveOpt(){
		if(localStorage["items_reserve"] != null){
			var items = JSON.parse(localStorage["items_reserve"].replace(/(\r\n|\n|\r)/gm," "));
			var itemlist = '';
			itemlist = '<option></option>';
			for(var i in items){
				var item = items[i];
				itemlist +="<option data-bc='" +i+ "' value='"+item.id+"'> " + i + ":" + item.item_code + ":" +  item.description +"</option>";
			}
			$('#searchOrder').html(itemlist);
		}
	}
	if(conReachable){
		$(".online").css({'color':'lime'});
		$("#isonline").empty();
		$("#isonline").append('(Online)').css({'color':'lime'});
	} else {
		$(".online").css({'color':'red'});
		$("#isonline").empty();
		$("#isonline").append('(Offline)').css({'color':'red'});
	}
	// logout
	$("#logout").click(function(){
		// remove the current_id
		localStorage.removeItem("current_id");
		location.href='login.php';

	});
	$('body').on('click','.removeImage',function(){
		$('#imagecon').hide();

	});
	var branches = JSON.parse(localStorage['branch_list']);
	var blist = '';
	blist+='<option></option>';
	var mybranch = '';
	for(var b in branches){
		blist+= "<option value='"+ branches[b].id +"'>"+branches[b].name+"</option>";
	}
	$('#bid').html(blist);


	if(localStorage['members']){
		var members = JSON.parse(localStorage['members']);
		var mlist ='';
		mlist = '<option></option>';
		var ConMemberContext = $("#con_member");
		var ConMemberFreebiesContext = $("#con_member_freebies");
		var MemberCreditContext = $("#member_credit");
		var MemberContext = $("#searchMember");
		ConMemberContext.empty();
		ConMemberContext.append("<option></option>");
		ConMemberFreebiesContext.empty();
		ConMemberFreebiesContext.append("<option></option>");
		MemberCreditContext.empty();
		MemberCreditContext.append("<option></option>");

		var user_id = (localStorage['current_id']).trim();
		var disabledm = false;
		for(var m in members){
			var selectedm = '';
			if(isMember() && members[m].user_id == user_id){
				selectedm = 'selected';
				disabledm = true;
			}
			mlist+= "<option "+selectedm+" value='"+ members[m].id +"'>"+members[m].lastname + " " +members[m].firstname + " " +members[m].middlename + "</option>"
			var amt =0;
			var amt_freebies = 0;

			if( members[m].amt){
				var check_not_validyet =0;
				amt =  members[m].amt;
				if( members[m].camt) check_not_validyet = members[m].camt;
				amt = amt - check_not_validyet;
				ConMemberContext.append("<option  "+selectedm+" data-con='"+amt+"' value='"+ members[m].id+"'>"+ members[m].lastname+", " + members[m].firstname + " " +  members[m].middlename +" ("+amt+")</option>");
			}
			if( members[m].freebiesamount){
				amt_freebies =  members[m].freebiesamount;
			}
			ConMemberFreebiesContext.append("<option  "+selectedm+" data-con_freebies='"+amt_freebies+"' value='"+members[m].id+"'>"+members[m].lastname+", " +members[m].firstname + " " + members[m].middlename +" ("+amt_freebies+")</option>");
			MemberCreditContext.append("<option  "+selectedm+" value='"+members[m].id+"'>"+members[m].lastname+", " +members[m].firstname + " " + members[m].middlename +"</option>");
		}
		MemberContext.html(mlist);
		if(disabledm){
			MemberContext.attr('disabled',true);
			ConMemberFreebiesContext.attr('disabled',true);
			ConMemberContext.attr('disabled',true);
			MemberCreditContext.attr('disabled',true);
		}

	}

	if(localStorage['sales_type_json']){
		var salestypelist = JSON.parse(localStorage['sales_type_json']);
		var slist ='';
		slist = '<option></option>';
		var slist2 ='';
		slist2 = '<option></option>';
		for(var m in salestypelist){
			slist+= "<option value='"+ salestypelist[m].id +"'>"+salestypelist[m].name+"</option>"
			slist2+= "<option value='"+ salestypelist[m].id +"'>"+salestypelist[m].name+"</option>"
		}
		$('#salestype').html(slist);
		$('#selectSalesType2').html(slist2);
		$('#selectSalesType2').select2({
			placeholder: 'Please Choose Sales Type',
			allowClear: true
		});
	}




	var ajaxOnProgress = false;
	noItemInCart();
	function noItemInCart() {
		if(!$("#cart tbody").children().length) {
			$("#cart tbody").append("<td colspan='3' id='noitem' style='padding-top:10px;' ><span class='text-danger'>NO ITEMS IN CART</span></td>");
		}
	}

	$('body').on('click', '.removeItem', function() {
		$(this).parents('tr').remove();
		noItemInCart();
		updateTotalReservation();
		checkInd();
		checkAllocateBranch();

		cashHolderComputation(0,0);
		updateCreditPayment();
		updateCashPayment();
		updateConPayment();
		updateBankTransferPayment();
		updateChequePayment();
	});

	$('body').on('keyup','#a_qty',function(){
		var qty = $(this).val();
		var allqty = $('#a_all_qty').val();
		var leftqty=0;
		mybranch = $('#bid > option:selected').text();
		if(isNaN(qty) || parseInt(qty) < 1){
			showToast('error','<p>Invalid Quantity</p>','<h3>WARNING!</h3>','toast-bottom-right');
			$(this).val(1);
		 leftqty = parseInt(allqty) - parseInt(1);
		$('#a_remarks').html("*Stocks to be deducted from "+mybranch+ ": " +leftqty );
			return;
		}
		if(parseInt(qty) > parseInt(allqty)){
			showToast('error','<p>Invalid Quantity</p>','<h3>WARNING!</h3>','toast-bottom-right');
			 leftqty = parseInt(allqty) - parseInt(1);
			$('#a_remarks').html("*Stocks to be deducted from "+mybranch+ ": " +leftqty );
			$(this).val(1);
			return;
		}
		leftqty = parseInt(allqty) - parseInt(qty);
		$('#a_remarks').html("*Stocks to be deducted from "+mybranch+ ": " +leftqty );
	});
	$('#saveAllocatedBranch').click(function(){
		var allqty = $('#a_all_qty').val();
		var qty = $('#a_qty').val();
		var leftqty = parseInt(allqty) - parseInt(qty);
		var item_id = $('#a_item_id').val();
		var allocation = [];
			allocation.push({
				branch_id: $('#a_branch_id').val(),
				qty:qty
			});
			allocation.push({
			branch_id: localStorage['branch_id'],
			qty:leftqty
			});
		var all = JSON.stringify(allocation);
		$('#hid_allocatedbranch'+item_id).val(all);
		$('#span_allocatedbranch'+item_id).css("color","green");
		$('#a_branch_id').select2('val',null);
		$('#a_all_qty').val('');
		$('#a_qty').val('');
		$('#bid').select2('enable',false);
		$('#myModal').modal('hide');
	});
	$('body').on('click', '.allocateBranch', function() {
		var btn = $(this);
		var row = btn.parents('tr');
		var curqty = row.children().eq(2).find('input').val();
		var item_id = btn.attr('data-id');
		mybranch = $('#bid > option:selected').text();

		var blist2 = '';
		blist2 += '<option></option>';
		for(var b in branches){
			if(branches[b].id != $('#bid').val()){
				blist2+= "<option value='"+ branches[b].id +"'>"+branches[b].name+"</option>";
			}
		}
		$('#a_branch_id').html(blist2);
		$('#a_item_id').val(item_id);
		$('#a_all_qty').val(curqty);
		$('#a_branch_id').select2('val',null);
		$('#a_remarks').html("*Stocks to be deducted from "+mybranch+ ": " +curqty );
		$('#a_all_qty_span').html("<span class='badge'>" + curqty + "</span>");

		$('#myModal').modal('show');
	});
	$('#void').click(function() {
		$("#cart").find("tr:gt(0)").remove();
		noItemInCart();
		updateTotalReservation();
		checkInd();
		checkAllocateBranch();
	});
	$("#salestype").select2({
		placeholder: 'Please Choose Sales Type',
		allowClear: true
	});
	$("#memberStation").select2({
		placeholder: config_station_label_name+' (optional)',
		allowClear: true
	});
	$('#save').click(function() {
		var btncontext = $(this);
		btncontext.attr('disabled',true);
		btncontext.val('Loading...');
		if($("#cart tbody tr").children().length) {
			var ismt = checkIfStationSalesTypeMatch();
			if(!ismt){
				showToast('error','<p>Quantity did not match on allocated quantity on ' +config_station_label_name+'</p>','<h3>WARNING!</h3>','toast-bottom-right');
				btncontext.attr('disabled',false);
				btncontext.val('SAVE');
				return;
			}


			var branch = $("#bid").val();
			var member_id = $("#searchMember").val();
			var salestype = $('#salestype').val();
			var stationid = $('#memberStation').val();
			var r_remarks = $('#r_remarks').val();
			var foundNoqty = 0;
			if(branch && member_id ) {
				var toOrder = new Array();
				$('#cart >tbody > tr').each(function(index) {
					var row = $(this);
					var item_id = $(this).prop('id');
					var qty = row.children().eq(2).find('input').val();
					var price = parseFloat(row.children().eq(3).text());
					var discount = row.children().eq(4).find('input').val();
					var price_adjustment = row.attr('data-adjustment');
					price_adjustment = (price_adjustment) ? price_adjustment :0;

					var multipless = $('#hid_multiple_ss'+item_id).val();
					var allocatedqty = $('#hid_allocatedbranch'+item_id).val();
					if(discount){
						if(discount.indexOf("%") > 0){
							discount = parseFloat(discount)/100;
							discount = (discount * price) * qty;
						} else {
							discount = parseFloat(discount);
						}
					} else {
						discount = 0;
					}
					if(!qty || qty == 0 || isNaN(qty) || qty == undefined) {
						foundNoqty = parseInt(foundNoqty) + 1;
					}


					toOrder[index] = {
						item_id: item_id,multipless:multipless,allocatedqty:allocatedqty, qty: qty,discount:discount,mid:member_id,bid:branch,salestype:salestype,stationid: stationid,price_adjustment:price_adjustment
					}
				});
				if(foundNoqty > 0) {
					alert("Please Indicate the Quantity of the items");
					btncontext.attr('disabled',false);
					btncontext.val('SAVE');
				} else {
					$('.loading').show();
					console.log(toOrder);
					toOrder = JSON.stringify(toOrder);
					console.log(toOrder);
					if(conReachable){
						if(ajaxOnProgress) {
							return;
						}
						ajaxOnProgress = true;
						var receivemoney = $('#cashreceiveholder').html();
						var payment_cash = '';
						var payment_con = '';
						var payment_bt ='';
						var payment_cheque ='';
						var payment_con_freebies ='';
						var payment_member_credit='';
						var payment_credit =''
						if(parseFloat(receivemoney) > 0){
							if(localStorage['payment_cash']) payment_cash = localStorage['payment_cash'];
							if(localStorage['payment_con']) payment_con = localStorage['payment_con'];
							if(localStorage['payment_bt']) payment_bt = localStorage['payment_bt'];
							if(localStorage['payment_cheque']) payment_cheque = localStorage['payment_cheque'];
							if(localStorage['payment_con_freebies']) payment_con_freebies = localStorage['payment_con_freebies'];
							if(localStorage['payment_member_credit']) payment_member_credit = localStorage['payment_member_credit'];
							if(localStorage['payment_credit']) payment_member_credit = localStorage['payment_credit'];
						}
						$.ajax({
							url: "ajax/ajax_order.php",
							type: "POST",
							async: false,
							data: {
								toOrder: toOrder,
								branch: branch,
								r_remarks:r_remarks,
								member_id: member_id,
								src_branch : localStorage['branch_id'],
								company_id:localStorage['company_id'],
								type:1,
								salestype:salestype,
								stationid: stationid,
								payment_cash:payment_cash,
								payment_con:payment_con,
								payment_bt:payment_bt,
								payment_cheque:payment_cheque,
								payment_con_freebies:payment_con_freebies,
								payment_member_credit:payment_member_credit,
								payment_credit:payment_credit

							},
							beforeSend: function(){

							},
							success: function(data) {
								alert(data);
								ajaxOnProgress = false;
								location.reload();
							},
							error: function() {
								// save in local storage
								alert('Saving transaction error');
								ajaxOnProgress = false;
								location.reload();
							}
						});
					} else {
						var pending = new Array();
						if(localStorage["reservation_pending"] != null){
							pending = JSON.parse(localStorage["reservation_pending"]);
						}
						pending.push(toOrder);
						localStorage["reservation_pending"] = JSON.stringify(pending);
						alert('Order was placed successfully');
						location.reload();
					}
				}
			} else {
				alert('Please choose branch and member first');
				btncontext.attr('disabled',false);
				btncontext.val('SAVE');
			}
		} else {
			alert('No items in cart');
		}
	});

	function checkIfStationSalesTypeMatch(){
		var ret = true;
		$('#cart > tbody > tr').each(function(){
			var row = $(this);
			var id = row.attr('id');
			var qty = row.children(2).find('input').val();
			var hid = $('#hid_multiple_ss'+id).val();
			if(hid){
				var toloopqty = JSON.parse(hid);
				var allqty = 0;
				for(var i in toloopqty){
					allqty = parseInt(allqty) + parseInt(toloopqty[i].qty);
				}
				if(qty !=  allqty){
					ret = false;
					$('#spanmultipless'+id).css('color','red');
				}
			}
		});
		return ret;
	}

	function updateTotalReservation(){
		$("#grandtotalholder").empty();
		$("#changeholder").empty();
		$("#cashreceiveholder").empty();
		$("#changeholder").append(0);
		$("#cashreceiveholder").append(0);
		$('#withpayment').html("WITHOUT PAYMENT");
		 if($('#cart > tbody > tr').length > 0 ){
			 var supertotal = 0;
			 $('#cart > tbody > tr').each(function(){
				 var row = $(this);
				 var t = replaceAll(row.children().eq(5).text(),",","");
				 var total= parseFloat(t);
				 supertotal = parseFloat(supertotal) + parseFloat(total);

			 });
			 $('#grandtotalholder').html(number_format(supertotal,2));

		 } else {
			 $('#grandtotalholder').html(number_format(0,2));

		 }
	}
	savePendingOrder();
	function savePendingOrder(){
		if(localStorage["reservation_pending"] != null){
			if(conReachable){
				var pendingsales = localStorage["reservation_pending"];
				if(ajaxOnProgress){
					return;
				}
				ajaxOnProgress = true;
				$.ajax({
					url: "ajax/ajax_order.php",
					type: "POST",
					async: false,
					data: {
						pending:pendingsales,
						company_id:localStorage['company_id'],
						type:2,
						src_branch : localStorage['branch_id']
					},
					success: function(data) {

						ajaxOnProgress = false;
						localStorage.removeItem("reservation_pending");
						location.reload();
					},
					error: function() {
						// save in local storage
						alert('Saving transaction error');
						ajaxOnProgress = false;
					}
				});
			}
		}
	}
	$('body').on('keyup', '.qty,.discount',function(){


		var row = $(this).parents('tr');
		var qty = row.children().eq(2).find('input').val();
		if(isNaN(qty)){
			$(this).val(1);
			alert('Invalid quantity');
			return;
		}

		var price = parseFloat(replaceAll(row.children().eq(3).text(),",",""));
		var discount = row.children().eq(4).find('input').val();

		if(discount){
			if(discount.indexOf("%") > 0){
				 discount = parseFloat(discount)/100;
				discount = (discount * price) * qty;
			} else {
				discount = parseFloat(discount);
			}
		} else {
			discount = 0;
		}

		var total = (qty * price) - discount;

		row.children().eq(5).text(number_format(total,2));

		updateTotalReservation();
	});
	$('body').on('keyup', '.discount',function(){

		var discount = $(this).val();
		var row = $(this).parents('tr');


		var price = parseFloat(replace(row.children().eq(3).text(),",",""));

		var qty  = row.children().eq(2).find('input').val();
		if(discount){
			if(discount.indexOf("%") > 0){
				discount = parseFloat(discount)/100;
				discount = (discount * price) * qty;
			} else {
				discount = parseFloat(discount);
			}
		} else {
			discount = 0;
		}
		if(isNaN(discount)){
			$(this).val(0);
			alert('Invalid discount');
			return;
		}
		if(discount > total){
			alert('Invalid discount');
			return;
		}
		var total = (qty * price) - discount;

		row.children().eq(5).text(number_format(total,2));

		updateTotalReservation();
	});
	$("#addorder").click(function() {
		var val = $('#searchOrder');
		var id = val.val();
		var branch = $("#bid").val();
		var mem_id = $("#searchMember").val();
		var isoncart = false;
		$('#cart >tbody > tr').each(function(){
			var row_id = $(this).attr('id');
			if(row_id == id){
				isoncart = true;
				$("#searchOrder").select2('val',null);
				return;
			}
		});
		if(isoncart){
			alert('Item is already in cart');
			return;
		}
		if(!branch || !id || !mem_id) {
			alert('Please complete the form.');
			$("#searchOrder").select2('val',null);
		} else {

			var x = $("#searchOrder option:selected").text();
			var splittedx = x.split(':');
			removeNoItemLabel();
			var item_id = id;
			var item_bc = $("#searchOrder option:selected").attr('data-bc');
			var items  = JSON.parse(localStorage['items_reserve'].replace(/(\r\n|\n|\r)/gm," "));
			var items_orig  = JSON.parse(localStorage['items'].replace(/(\r\n|\n|\r)/gm," "));
			var thisitem = items[item_bc];
			var thisitem_orig = items_orig[item_bc];
			var price_adjustment = 0;
			if(thisitem_orig){
				 price_adjustment = thisitem_orig.price_adjustment;
			}

			price_adjustment = (price_adjustment) ? price_adjustment : 0;
			var adjusted_price = parseFloat(thisitem.price) +  parseFloat(price_adjustment);
			console.log(price_adjustment);
			console.log(thisitem);
			//var item_qty = $("#searchOrder option:selected").attr('data-qty');
			var btnstocks ='';
			if(conReachable){ //
				btnstocks = "<button class='btn btn-default getallstocks' data-item_id='"+item_id+"'><span class='glyphicon glyphicon-list'></span> Stocks</button>";
			}
			$('#cart > tbody').append("<tr data-adjustment='"+price_adjustment+"' data-barcode='"+item_bc+"' id='" + item_id + "'><td data-title='Barcode'>" + item_bc + "</td><td data-title='Item'>" + splittedx[2] + "<br><small class='text-danger'>" +thisitem.description+"</small></td><td data-title='Qty'><input type='text' class='form-control  qty' value='1' style='width:80px;'></td><td data-title='Price'>"+number_format(adjusted_price,2)+"</td><td data-title='Discount'><input type='text' class='form-control  discount' value='0' style='width:80px;'></td><td data-title='Total'>"+number_format(adjusted_price,2)+"</td><td data-title='Stocks'></td><td>"+btnstocks+"</td><td><input type='hidden' id='hid_unsaved_ss"+item_id+"'><input type='hidden' id='hid_multiple_ss"+item_id+"'><span  id='spanmultipless"+item_id+"' class='glyphicon glyphicon-folder-open ind_multiple_ss'></span> <input type='hidden' id='hid_allocatedbranch"+item_id+"'><span  id='span_allocatedbranch"+item_id+"' data-id='"+item_id+"' class='glyphicon glyphicon-map-marker allocateBranch'></span>   <span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
			$("#searchOrder").select2('val',null);
			$("#"+item_id).children().eq(2).find('input').focus().select();
			updateTotalReservation();
		}
	});

	$('body').on('click','.getallstocks',function(){
		var item_id = $(this).attr('data-item_id');
		$.ajax({
		    url:'ajax/ajax_query2.php',
		    type:'post',
			beforeSend:function(){
				$('#sbody').html('<p class="text-center">Loading...</p>');
			},
		    data: {functionName: 'getAllStocks',item_id:item_id},
		    success: function(data){
				$('#sbody').html(data);
			    $('#modalStocks').modal('show');
		    },
		    error:function(){
		        alert('Error Occur');
		    }
		})
	});
	function removeNoItemLabel() {
		$("#noitem").remove();
	}
	function showToast(label,msg,title,position){
		toastr.options = {
			"closeButton": false,
			"debug": false,
			"positionClass": position,
			"onclick": null,
			"showDuration": "300",
			"hideDuration": "1000",
			"timeOut": "3000",
			"extendedTimeOut": "1000",
			"showEasing": "swing",
			"hideEasing": "linear",
			"showMethod": "fadeIn",
			"hideMethod": "fadeOut"
		}
		toastr[label](msg,title);
	}
	$('#searchMember').change(function(){

		var memid = $('#searchMember').select2("val");
		if (!memid) {
			$('#memberStation').select2("val",null);
			getStationOptList('');
			removeMemberDetails();
			return;
		}
		var isblock = $("#searchMember").select2().find(":selected").data("isblock");
		if(isblock == '1'){
			alert('This member is on the blacklist.')
		}

		getStationOptList(memid);


		$('#searchMember').select2({
			allowClear: true,
			placeholder: "Sold to (optional)"
		});
	});
	$('#memberStation').change(function(){
		var memid = $("#memberStation").select2().find(":selected").data("member_id");
		$('#searchMember').select2("val",memid);

		$('#opt_station').select2({
			allowClear: true,
			placeholder: "Station (optional)"
		});
	});
	$("#searchMember").select2({
		placeholder: 'Select Member',
		allowClear: true
	});
	$("#bid").select2({
		placeholder: 'Select Branch',
		allowClear: true
	});
	$("#a_branch_id").select2({
		placeholder: 'Select Branch',
		allowClear: true
	});
	function formatItem(o) {
		if (!o.id)
			return o.text; // optgroup
		else {
			var r = o.text.split(':');
			return "<span> "+r[0]+"</span><span style='margin-left:30px;'>" + r[1] + "</span><br><span style='margin-left:100px;display: inline-block;margin-top:5px;' class='text-danger'><small class='testspanclass'>"+r[2]+"</small></span>";
		}

	}
	$("#searchOrder").select2({
		placeholder: 'Select Item',
		allowClear: true,
		formatResult: formatItem,
		formatSelection: formatItem,
		escapeMarkup: function(m) {
			return m;
		}
	}).on("select2-close", function(e) {
		// fired to the original element when the dropdown closes

		setTimeout(function() {
			$('#imagecon').hide();
		}, 300);
	}).on("select2-highlight", function(e) {
		console.log("highlighted val=" + e.val + " choice=" + e.choice.text);
		var itemid =  e.choice.id;
		var itemjpg = itemid +".jpg";
		var opt = $(this);
		$.ajax({
			url:window.location.origin+'/pos/item_images/'+itemjpg,
			type:'HEAD',
			error: function()
			{
				$('#imagecon').hide();
			},
			success: function()
			{
				$('#imagecon  img').attr('src',window.location.origin+'/pos/item_images/'+itemjpg);
				$('#imagecon').fadeIn();

			}
		});
	});
	getOrderOffline(localStorage['company_id'],localStorage['branch_id']);

	function getStationOptList(mem_id){

		if(localStorage['stations'].trim() != '0'){
			var stats = JSON.parse(localStorage['stations']);
			$("#memberStation").empty();
			$("#memberStation").append("<option value=''></option>");
			$("#ind_station_select2").empty();
			$("#ind_station_select2").append("<option value=''></option>");
			var isSelected='';
			for(var i in stats){
				if(mem_id){

					if (stats[i].member_id == mem_id){
						$("#memberStation").append("<option data-member_id='"+stats[i].member_id+"'value='"+stats[i].id+"' "+isSelected+">"+stats[i].name+"</option>");
						$("#ind_station_select2").append("<option data-member_id='"+stats[i].member_id+"'value='"+stats[i].id+"' "+isSelected+">"+stats[i].name+"</option>");
					}
				} else {
					$("#memberStation").append("<option data-member_id='"+stats[i].member_id+"'value='"+stats[i].id+"' "+isSelected+">"+stats[i].name+"</option>");
					$("#ind_station_select2").append("<option data-member_id='"+stats[i].member_id+"'value='"+stats[i].id+"' "+isSelected+">"+stats[i].name+"</option>");
				}

			}
			$("#memberStation").select2({
				placeholder: config_station_label_name+' (optional)',
				allowClear: true
			});
			$("#ind_station_select2").select2({
				placeholder: config_station_label_name+' (optional)',
				allowClear: true
			});
		}
	}
	$('body').on('click','.ind_multiple_ss',function(){
		var row = $(this).parents('tr');
		var qty = row.children().eq(2).find('input').val();

		$('#tridforss').val(row.attr('id'));
		$('#ind_multiple_ss_qty').val(qty);

		$('#multiplessModal').modal('show');
		$("#ind_multiple_ss_tbl").find("tr:gt(0)").remove();
		$("#ind_multiple_ss_tbl").hide();
		$('#ind_station_select2').select2('val',null);
		$('#multiple_ss_qty').val('');
		$('#multiple_ss_qty_span').html("<strong>Total Quantity: <span class='badge'> " + qty + "</span></strong>");
		if(parseInt(qty) == 1){
			$('#multiple_ss_qty').val(qty);
		}
		var unsaved= "hid_unsaved_ss"+row.attr('id');
		console.log(unsaved);
		if($('#'+unsaved).val()){
			$('#ind_multiple_ss_tbl').show();
			$('#ind_multiple_ss_tbl > tbody').html($('#'+unsaved).val());

		}
	});
	$('body').on('click','#ind_multiple_ss_addmore',function(){
		var allqty = $('#ind_multiple_ss_qty').val();
		var qty = $('#multiple_ss_qty').val();
		var ind_station = $('#ind_station_select2').val();
		var ind_salestype = $('#selectSalesType2').val();
		var typename = $('#selectSalesType2 option:selected').text();
		var stationname = $('#ind_station_select2 option:selected').text();
		var member_id=$('#ind_station_select2 option:selected').attr('data-member_id');
		var toallocateqty = allqty;
		if(qty && (ind_station || ind_salestype)){
			$('#ind_multiple_ss_tbl').show();
			var totalqty = qty;
			$('#ind_multiple_ss_tbl > tbody > tr').each(function(){
				var iqty = $(this).children().eq(0).text();
				console.log(iqty);
				totalqty = parseInt(totalqty) + parseInt(iqty)
			});
			if(parseInt(totalqty) > parseInt(allqty)){
				showToast('error','<p>Invalid Quantity</p>','<h3>WARNING!</h3>','toast-bottom-right');
				return false;
			}
			if(!ind_station) stationname = 'None';
			else{
				if(member_id){
					getStationOptList(member_id)
				}
			}
			if(!ind_salestype) typename = 'None';
			toallocateqty = parseInt(toallocateqty) - totalqty;
			$('#multiple_ss_toallocateqty_span').html("<strong>Quantity To Allocate: <span class='badge'> " + toallocateqty + "</span></strong>");

			$('#ind_multiple_ss_tbl > tbody').append("<tr data-member_id='"+member_id+"' data-station='"+ind_station+"' data-salestype='"+ind_salestype+"'><td>"+qty+"</td><td>"+stationname+"</td><td>"+typename+"</td><td><span class='glyphicon glyphicon-remove-sign removeItem'></td></tr>")
		}
		$('#ind_station_select2').select2('val',null);
		$('#selectSalesType2').select2('val',null);
		$('#multiple_ss_qty').val('');

		//validation sa qty
		// validation sa station

	});
	$('body').on('click','#ind_multiple_ss_ok',function(){
		if($("#ind_multiple_ss_tbl tbody tr").children().length == 0) {
			showToast('error','<p>Add record first.</p>','<h3>WARNING!</h3>','toast-bottom-right');
		} else {
			var allqty = $('#ind_multiple_ss_qty').val();
			var formModified = false;
			var tridss = $('#tridforss').val();

			if($('#multiple_ss_qty').val() || $('#ind_station_select2').val() || $('#selectSalesType2').val()){
				formModified = true;
			}
			if(formModified){
				var gosubmit = confirm("Form has been modified. Are you sure you want to continue?");
			}else {
				var gosubmit = true;
			}
			if(gosubmit){
				var totalqty = 0;

				var jsonss = new Array();
				var memberall = 0;
				var hasstation = false;
				var hassalestype = false;
				$("#ind_multiple_ss_tbl > tbody > tr").each(function(){
					var row = $(this);
					var stationid =row.attr('data-station');
					var salestypeid = row.attr('data-salestype');
					var memberid = row.attr('data-member_id');

					if(!memberall){
						memberall = memberid;
					}
					if(stationid){
						hasstation =true
					}
					if(salestypeid){
						hassalestype =true
					}
					var qty = row.children().eq(0).text();
					if(!qty) qty = 0;
					totalqty = parseInt(totalqty) + parseInt(qty);
					if(!stationid) stationid = 0;
					if(!salestypeid) salestypeid = 0;
					jsonss.push({
						stationid : stationid,
						salestypeid:salestypeid,
						qty:qty,
						memberid:memberid
					});
				});

				if(parseInt(allqty) > parseInt(totalqty)){
					gosubmit = confirm("Not all quantity are allocated. Do you want to continue?");
				} else {
					gosubmit = true;
				}
				var unallocated = parseInt(allqty) - parseInt(totalqty);
				if(unallocated){
					jsonss.push({
						stationid : 0,
						salestypeid:0,
						qty:unallocated,
						memberid:memberall
					});
				}
				if(gosubmit){
					console.log(jsonss);
					$('#hid_multiple_ss'+tridss).val(JSON.stringify(jsonss));
					$('#hid_unsaved_ss'+tridss).val($('#ind_multiple_ss_tbl > tbody').html());
					if(hasstation){
						$('#searchMember').select2("val",memberall);
						$('#searchMember').select2("enable",false);
						$('#memberStation').select2("val",null);
						$('#memberStation').select2("enable",false);
					}
					if(hassalestype){
						$('#salestype').select2("val",null);
						$('#salestype').select2("enable",false);
					}

					$('#spanmultipless'+tridss).css("color","green");
					$('#multiplessModal').modal('hide');
				}

			}
		}
	});
	function checkInd(){
		var hasind = false;
		var hasind2 = false;
		$('#cart > tbody  > tr ').each(function() {
			var trid  = $(this).attr('id');
			if($('#hid_multiple_ss'+trid).val()){
				var jsondet = JSON.parse($('#hid_multiple_ss'+trid).val());
				for(var e in jsondet){
					if(jsondet[e].stationid){
						hasind = true;
					}
					if(jsondet[e].salestypeid){
						hasind2 = true;
					}
				}
			}
		});
		if(!hasind && !isMember()){
			$('#searchMember').select2("enable",true);
			$('#memberStation').select2("enable",true);
		}
		if(!hasind2){
			$('#salestype').select2("enable",true);
		}
	}
	function checkAllocateBranch(){
		var hasallocatebranch = false;

		$('#cart > tbody  > tr ').each(function() {
			var trid  = $(this).attr('id');
			if($('#hid_allocatedbranch'+trid).val()){
				hasallocatebranch = true;
			}
		});
		if(!hasallocatebranch){
			$('#bid').select2("enable",true);
		}
	}
	getCountShouts();

	/* PAYMENT LOGIC START  */
	$('#checkout').click(function(){
		showpricemodal()
	});
	function showpricemodal(){
		if($("#cart tbody tr").children().length){
			var items = JSON.parse(localStorage['items_reserve'].replace(/(\r\n|\n|\r)/gm," "));
			var totalforfreebies = 0;
			$('#cart > tbody > tr').each(function(index){
				var row = $(this);
				var b = row.attr('data-barcode');
				var totalamount = row.children().eq(4).text();
				if(items[b].for_freebies != 0){

					totalforfreebies = parseFloat(totalforfreebies) + parseFloat(totalamount);
				}
			});

			if (!totalforfreebies){
				localStorage['totalforfreebies'] = 0;
			} else {
				localStorage['totalforfreebies'] = totalforfreebies;
			}
			localStorage.removeItem('payment_cheque');
			localStorage.removeItem('payment_credit');
			localStorage.removeItem('payment_bt');
			localStorage.removeItem('payment_cash');
			localStorage.removeItem('payment_con');
			localStorage.removeItem('payment_con_freebies');
			localStorage.removeItem('payment_member_credit');
			$("#cashreceiveholder").text(0);
			$("#changeholder").text(0);
			$("#con_amount_freebies").val('');
			$("#con_amount").val('');
			$("#member_credit_amount").val('');
			updateCreditPayment();
			updateCashPayment();
			updateBankTransferPayment();
			updateChequePayment();
			updateConPayment();
			updateConPaymentFreebies();
			updateMemberCredit();
			$("#amountdue").html("<span style='font-size:1.2em;' class='text-info'><strong> Amount Due: " + $("#grandtotalholder").text() + "</strong></span>");
			$("#hidamountdue").val( replaceAll($("#grandtotalholder").text(),',',''));
			$("#getpricemodal").modal("show");
			setTimeout(function() { $('#cashreceivetext').focus() }, 500);
		} else {
			showToast('error','<p>No items in cart yet.</p>','<h3>WARNING!</h3>','toast-bottom-right');
		}
	}
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

	function isValidFormCheque(){
		if($("#ch_table tr").children().length ){
			var chequeArray = new Array();
			var fn = $("#ch_firstname").val();
			var mn = $("#ch_middlename").val();
			var ln = $("#ch_lastname").val();
			var phone = $("#ch_phone").val();
			if(!ln || !fn || !phone){
				showToast('error','<p>Please Complete Cheque billing form. </p>','<h3>WARNING!</h3>','toast-bottom-right');
				return false;
			} else {
				if(!isAlphaNumeric(fn)){
					showToast('error','<p>First name should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return false;
				}
				if(mn && !isAlphaNumeric(mn)){
					showToast('error','<p>Middle name should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return false;
				}
				if(!isAlphaNumeric(ln)){
					showToast('error','<p>Last name should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return false;
				}
				if(!isAlphaNumeric(phone)){
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
				localStorage['payment_cheque'] = JSON.stringify(chequeArray);
				return true;
			}
		}
		return true;
	}

	function isValidFormCredit(){
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
			if(!ln || !fn  || !phone  ){
				showToast('error','<p>Please Complete Credit Card billing form. </p>','<h3>WARNING!</h3>','toast-bottom-right');
				return false;
			} else {
				if(!isAlphaNumeric(ln)){
					showToast('error','<p>Last name should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return false;
				}
				if(!isAlphaNumeric(fn)){
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
				if(!isAlphaNumeric(phone)){
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
				localStorage['payment_credit'] = JSON.stringify(creditArray);
				return true;
			}

		}
		return true;
	}
	function isValidFormBankTransfer(){
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

			if(!ln || !fn || !phone || !bt_bankto_name || !bt_bankto_account_number){
				showToast('error','<p>Please Bank Transfer  billing form. </p>','<h3>WARNING!</h3>','toast-bottom-right');
				return false;
			} else {
				if(!isAlphaNumeric(bt_bankto_name)){
					showToast('error','<p>Bank name should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return false;
				}
				if(!isAlphaNumeric(bt_bankto_account_number)){
					showToast('error','<p>Bank account number should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return false;
				}
				if(!isAlphaNumeric(fn)){
					showToast('error','<p>First name should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return false;
				}
				if(mn & !isAlphaNumeric(mn)){
					showToast('error','<p>Middle name should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return false;
				}
				if(!isAlphaNumeric(ln)){
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
				localStorage['payment_bt'] = JSON.stringify(bankTransferArray);
				return true;
			}
		}
		return true;
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
	function receiveCash(){
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
			cashHolderComputation(0,0);
			showToast('error','<p>Invalid payment</p>','<h3>WARNING!</h3>','toast-bottom-right');
			return;
		} else {
			if(!isValidFormCheque() || !isValidFormCredit() || !isValidFormBankTransfer() ){
				return;
			}

			var change = parseFloat(totalpayment) - parseFloat(grandtotal);
			cash = parseFloat(cash) - parseFloat(change);
			localStorage['payment_cash'] = cash;
			localStorage['payment_con'] = con_amount;
			localStorage['payment_con_freebies'] = con_amount_freebies;
			localStorage['payment_member_credit'] = member_credit_amount;
			if(con_amount){
				$("#opt_member").select2('val',$("#con_member").val());
			}
			if(con_amount_freebies){
				$("#opt_member").select2('val',$("#con_member_freebies").val());
			}
			if(member_credit_amount){
				$("#opt_member").select2('val',$("#member_credit").val());
			}
			cashHolderComputation(totalpayment,change);

			$("#credit_table").find("tr").remove();
			$("#bt_table").find("tr").remove();
			$("#ch_table").find("tr").remove();
			$("#tab_d :input[type='text']").val('');
			$("#tab_c :input[type='text']").val('');
			$("#tab_b :input[type='text']").val('');
			$("#tab_a :input[type='text']").val('');
			$('#getpricemodal').modal("hide");
		}
	}
	$('body').on('click','.cashreceiveok',function(){
		receiveCash();

	});
	function cashHolderComputation(cash,change){
		$('#cashreceiveholder').empty();
		$('#changeholder').empty();
		$('#cashreceiveholder').append(number_format(cash,2));
		$('#changeholder').append(number_format(change,2));
		if(parseFloat(cash) == 0){
			$('#withpayment').html("WITHOUT PAYMENT");
		} else {
			$('#withpayment').html("WITH PAYMENT");
		}
	}
	$('.cashreceivecancel').click(function(){
		$('#getpricemodal').modal("hide");
		$("#credit_table").find("tr").remove();
		$("#bt_table").find("tr").remove();
		$("#ch_table").find("tr").remove();
		$("#tab_d :input[type='text']").val('');
		$("#tab_c :input[type='text']").val('');
		$("#tab_b :input[type='text']").val('');
		$("#tab_a :input[type='text']").val('');
		localStorage.removeItem('payment_cheque');
		localStorage.removeItem('payment_credit');
		localStorage.removeItem('payment_bt');
		localStorage.removeItem('payment_cash');
		localStorage.removeItem('payment_con');
	});
	$('#cashreceivetext').keypress(function (e) {
		var key = e.which;
		if(key == 13)  // the enter key code
		{
			receiveCash();
			$('#cashreceivetext').val('');
			$('#getpricemodal').modal("hide");
		}

	});
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
	$("#con_member").on('select2-focus',function(){
		$(document).unbind('keyup');
	});
	$("#con_member_freebies").on('select2-focus',function(){
		$(document).unbind('keyup');
	});
	$("#member_credit").on('select2-focus',function(){
		$(document).unbind('keyup');
	});
	$('#con_member_freebies').select2({
		allowClear: true,
		placeholder: "Search member"
	});
	$('#member_credit').select2({
		allowClear: true,
		placeholder: "Search member"
	});
	$('#con_member').select2({
		allowClear: true,
		placeholder: "Search member"
	});
	$('#cashreceivetext').keyup(function (e) {
		if(isNaN($(this).val())){
			showToast('error','<p>Please Enter Valid Amount</p>','<h3>WARNING!</h3>','toast-bottom-right');
			$(this).val('');
			$(this).focus();
		}
		$("#hidcashpayment").val($(this).val());
		updateCashPayment();
	});
	$('#con_amount').keyup(function (e) {

		if(!($('#con_member').val())){
			showToast('error','<p>Please Choose member first</p>','<h3>WARNING!</h3>','toast-bottom-right');
			return;
		}
		if (localStorage['hasType2'] == 1){
			//current
			var name = $("#con_member option:selected").text();
			var memId = $("#con_member").val();
			removeMemberDetails();
			$("#membersIdHelper").append('Member Id: ');
			$("#memberId").append(memId);
			$("#membersnameHelper").append('Name: ');
			$("#membersname").append(name);
			localStorage.removeItem("temp_item_holder");
		}
		var validamt = $('#con_member option:selected').attr('data-con');
		if(parseFloat(validamt) < parseFloat($(this).val())){
			showToast('error','<p>Invalid consumable amount</p>','<h3>WARNING!</h3>','toast-bottom-right');
			$(this).focus();
			$(this).val('');
		}
		if(isNaN($(this).val())){
			showToast('error','<p>Please Enter Valid Amount</p>','<h3>WARNING!</h3>','toast-bottom-right');
			$(this).val('');
			$(this).focus();
		}
		$("#hidconsumablepayment").val($(this).val());
		if(isValidAmount($(this).val(),false)){
			showToast('error','<p>Your payment exceeds to amount due.</p>','<h3>WARNING!</h3>','toast-bottom-right');
			$(this).val('');

		}
		$("#hidconsumablepayment").val($(this).val());
		updateConPayment();
	});
	$('#con_amount_freebies').keyup(function (e) {

		if(!($('#con_member_freebies').val())){
			showToast('error','<p>Please Choose member first</p>','<h3>WARNING!</h3>','toast-bottom-right');
			return;
		}
		var validamt = $('#con_member_freebies option:selected').attr('data-con_freebies');
		var cartfreebies = parseFloat(localStorage['totalforfreebies']);

		if (parseFloat($(this).val()) > cartfreebies){
			showToast('error','<p>Invalid freebies amount</p>','<h3>WARNING!</h3>','toast-bottom-right');
			$(this).focus();
			$(this).val('');
		}

		if(parseFloat(validamt) < parseFloat($(this).val())){
			showToast('error','<p>Invalid freebies amount</p>','<h3>WARNING!</h3>','toast-bottom-right');
			$(this).focus();
			$(this).val('');
		}
		if(isNaN($(this).val())){
			showToast('error','<p>Please Enter Valid Amount</p>','<h3>WARNING!</h3>','toast-bottom-right');
			$(this).val('');
			$(this).focus();
		}
		$("#hidconsumablepaymentfreebies").val($(this).val());
		if(isValidAmount($(this).val(),false)){
			showToast('error','<p>Your payment exceeds to amount due.</p>','<h3>WARNING!</h3>','toast-bottom-right');
			$(this).val('');

		}
		$("#hidconsumablepaymentfreebies").val($(this).val());
		updateConPaymentFreebies();
	});
	$('#member_credit_amount').keyup(function (e) {

		if(!($('#member_credit').val())){
			showToast('error','<p>Please Choose member first</p>','<h3>WARNING!</h3>','toast-bottom-right');
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
	localStorage.removeItem('payment_cheque');
	localStorage.removeItem('payment_credit');
	localStorage.removeItem('payment_bt');
	localStorage.removeItem('payment_cash');
	localStorage.removeItem('payment_con');
	localStorage.removeItem('payment_con_freebies');
	localStorage.removeItem('payment_member_credit');
	/* PAYMENT LOGIC END */


});
</script>
<?php require_once 'includes/page_tail.php'; ?>
