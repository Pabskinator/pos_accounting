<?php
	require_once 'core/init.php';
	Redirect::to("login.php");
	die();
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	$user = new User();
	if($user->isLoggedIn()){
		if(!$user->hasPermission('mainpos')){
			Redirect::to('admin/index.php');
		}
		$branch = new Branch();
		$branches = $branch->get_active('branches',array('company_id','=',$user->data()->company_id));
		$terminal = new Terminal();
		$terminals = $terminal->get_active('terminals',array('company_id','=',$user->data()->company_id));

		if(!$branches || !$terminals){
			Session::flash('homeflash','PLEASE ADD BRANCH AND TERMINAL FIRST');
			Redirect::to('admin/index.php');
		}

	} else {
		Redirect::to("login.php");
	}

?>
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
						<li id="liqueue" style='display:none;' ><a href="#" id='showqueuelist'>Queue(<span class='text-danger' id='pendingqueues'></span>)</a></li>
						<li id='shoutnav' style='display:none;' ><a href="shoutbox/index.html">Message(<span id='ctrshout'>0</span>)</a></li>
					</ul>

					<ul class="nav navbar-nav navbar-right" >
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" style='color:#fff;' ><span class='glyphicon glyphicon-user'></span> HI, <span id='currentuserfullname'></span> <span id='isonline'></span><span class="caret"></span></a>
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
	<div class="container-fluid" id='mainContainer' style='display:none;'>

		<div class="row">
			<div class="col-md-6 two-column">
			<!--	<div class="input-group">
					<span class="input-group-addon navbar-inverse" style='color:white;'><span class='glyphicon glyphicon-search'></span></span>
					<input type="text" list='item_list' autocomplete=off id='addproductincart' class="form-control" placeholder="Search ITEM or Scan Barcode">

				</div> -->
					<div class="form-group">
					<select data-toggle="tooltip" data-placement="top" title='Space' class='form-control' name="addproductincart" id="addproductincart" >

					</select>
					</div>
				<div class="hidden-xs">
				<div id="imagecon">
					<span style='cursor:pointer; position:absolute;right:2px;top:2px;font-size:1.1em;' class='glyphicon glyphicon-remove-sign removeImage'></span>
					<img src="" alt="Image" /> <br>
				</div>

				<table id='productDisplay' class='table noselect posview1' style='font-size:1.2em;display:none;'>
					<thead>
					<tr>
						<th>BARCODE</th>
						<th>ITEM CODE</th>
						<th>PRICE</th>
						<th>QTY</th>
						<th></th>

					</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
				<div id="recentSoldItem"></div>

				<div class="posview2" style='display: none;'>
					<div class="row">

					</div>
				</div>
			</div>
			</div>
			<div class="col-md-6 two-column" id='rightCon' >


				<!-- <div class="input-group" id='membersLog' style='display:none;'>
					<span class="input-group-addon navbar-inverse" style='color:white;'><span class='glyphicon glyphicon-user'></span></span>
					<input class="form-control" type="text" list='member_list' id='membersLogName' placeholder='Enter Member Name' />
				</div> -->
				<div id="membersLog" style='display:none;'>
					<div class="form-group">
					<!--<select class='form-control' name="membersLogName" id="membersLogName">

					</select>-->
						<input class='form-control' name="membersLogName" id="membersLogName">
					</div>
				</div>

			<div id="no-more-tables">
				<table id='cart' class='table noselect' style='margin-top:4px;' >
					<thead>
					<tr>
						<th>QTY</th>
						<th>PRODUCT</th>
						<th>PRICE</th>
						<th>DISCOUNT</th>
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
					<div class="col-md-12">
						<span id='membersIdHelper'></span><span id='memberId' class='badge'></span><br>
						<span id='membersnameHelper'></span>
						<span id='membersname' class='text-danger'></span>
						<div id='serviceInfo'></div>
					</div>
				</div>
				<div class='row'>
					<div class="col-md-12 text-right">
						<button id='showDiscountQtyAll' class='btn btn-default'>
							<span class='glyphicon glyphicon-cog'></span>
						</button>
					</div>
				</div>

				<div class='row' id='conDiscountQtyAll' style='margin-top:5px;display:none;'>

					<div class="col-md-6">
						<div class="form-group">
							<input type="text" class='form-control' id='txtQtyAll' placeholder='Change All Quantity'>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<input type="text" class='form-control' id='txtDiscountAll' placeholder='Change All Discount'>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<p class='text-danger' style='font-weight:bold;' id='nextInvoicenumber'></p>
					</div>
					<div class="col-md-6">
						<p class='text-danger' style='font-weight:bold;' id='nextDrnumber'></p>
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
					</div>
				</div>

				<hr />

				<div class="row">
					<div class="col-md-12">
						<input type="text" name="opt_member" id="opt_member" class='form-control'>
						<!--<select name="opt_member" id="opt_member" class='form-control'>
							<option></option>
						</select> -->
						<span class="help-block">Not on the list? Add member <a href='admin/addmember.php'>here</a></span>
					</div>
					<div class="col-md-12">
						<select name="opt_station" id="opt_station" class='form-control'>
							<option></option>
						</select>
					</div>
				</div>
				<hr>

				<div class="row">
					<div class="form-group">
						<div class="col-md-3 text-right">Receipt Type</div>
						<div class="col-md-3">
							<label class="radio-inline" for="checkInvoice">
								<input name="checkType" id="checkInvoice" value="1" checked="checked" type="checkbox">
								Invoice
							</label>
						</div>
						<div class="col-md-3">
							<label class="radio-inline" for="checkDR">
								<input name="checkType" id="checkDR" value="2" type="checkbox">
								DR
							</label>
						</div>
						<div class="col-md-3">
							<label class="radio-inline" for="checkIR">
								<input name="checkType" id="checkIR" value="3" type="checkbox">
								PR
							</label>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="form-group">
						<div class="col-md-3 text-right">Sales type</div>
						<div class="col-md-9">
							<select name="selectSalesType" id="selectSalesType" class='form-control' style='margin:3px;'>

							</select>
						</div>
						<!--
						<div class="col-md-3">
							<label class="radio-inline" for="radioTypePos">
								<input name="radioSalesType" id="radioTypePos" value="1" checked="checked" type="radio">
								Sales POS
							</label>
						</div>
						<div class="col-md-3">
							<label class="radio-inline" for="radioTypeDr1">
								<input name="radioSalesType" id="radioTypeDr1" value="2" type="radio">
								Package Delivery
							</label>
						</div>
						<div class="col-md-3">
							<label class="radio-inline" for="radioTypeDr2">
								<input name="radioSalesType" id="radioTypeDr2" value="3" type="radio">
								Delivery Express
							</label>
						</div>
						-->
						</div>
					</div>
				<div class="row">
					<div class="form-group">
						<div class="col-md-3 text-right">Remarks</div>
						<div class="col-md-9">
							<input type="text" name='sales_remarks' placeholder="Optional" id='sales_remarks' class='form-control' style='margin:3px;'/>
						</div>
					</div>
				</div>
				<div class="row" id='sales_startdatecon' style='display:none;'>
					<div class="form-group">
						<div class="col-md-3 text-right">Start Date</div>
						<div class="col-md-9">
							<input type="text" name='sales_startdate' placeholder="Start Date" id='sales_startdate' class='form-control' style='margin:3px;'/>
						</div>
					</div>
				</div>
				<br>
					<div class="row">
						<div class="col-md-3 text-left">
							<button class='btn btn-danger' id='voidOrder'>VOID</button>
						</div>
						<div class="col-md-9 text-right">
							<button class='btn btn-success' id='btnSyncAll'>SYNC</button>
							<button class='btn btn-success' style='display:none;' id='getMember'>MEMBER</button>
							<button class='btn btn-success' style='display:none;' id='queue'>QUEUE</button>
							<button class='btn btn-success' id='checkout'>CHECK OUT</button>
							<button class='btn btn-success' id='print'>PRINT</button>
						</div>
					</div>
				</div>
				<hr>
		</div>

	</div>
	<div class="footer">
		<div class="container">
			<p class="footermsg"> &copy; Apollo Systems </p>
		</div>
	</div>

	<!-- set up branch and terminal if not yet configure -->
	<div class="modal fade" id="btSetup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title">Branch and Terminal</h3>
					<p>You need to set up first your branch and terminal</p>
				</div>
				<div class="modal-body">
					<form class="form-horizontal">
						<fieldset>

							<div class="form-group">
								<label class="col-md-4 control-label" for="branches">Select Branch</label>
								<div class="col-md-4" id='branchitemholder'>

								</div>
							</div>

							<!-- Select Basic -->
							<div class="form-group">
								<label class="col-md-4 control-label" for="terminals">Select Terminal</label>
								<div class="col-md-4"  id='terminalitemholder'>
									<span class="label label-danger">Choose branch first..</span>
								</div>
							</div>

						</fieldset>
					</form>

				</div>
				<div class="modal-footer">
					<button type="button" id='submitbt' class="btn btn-primary">Save </button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
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
						<li class='notcashlist'><a href="#tab_h" data-toggle="tab">Deduction <span id='totalmemberdeduction' class='badge'></span> </a></li>

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
						<div class="tab-pane" id="tab_h">

							<fieldset>
								<div class="form-group">
									<label class="col-md-3 control-label text-center" for="member_id_deduction">Client</label>
									<div class="col-md-9">
										<select name="member_deduction" id="member_deduction" class='form-control'>
										</select>
										<span class="help-block">Choose client name</span>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 control-label text-center" for="member_deduction_amount">Amount</label>
									<div class="col-md-9">
										<input id="member_deduction_amount" name="member_deduction_amount" class="form-control input-md" type="text">
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
	<div class="modal fade" id="queuemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title">QUEUES</h3>
				</div>
				<div class="modal-body">

					<fieldset>

						<div class="form-group">
							<label class="col-md-4 control-label" for="queueselect">Queue Name</label>
							<div class="col-md-4" id='queueholder'>
								<select id='queueselect' class='form-control'>
								</select>
							</div>
						</div>

					</fieldset>


				</div>
				<div class="modal-footer">
					<button type="button" id='queueok' class="btn btn-primary">OK </button>
					<button type="button" id='queuecancel' class="btn btn-primary">CANCEL </button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<div class="modal fade" id="queuelistmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
		<div class="modal-dialog" style="width:98%;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title">QUEUE LIST</h3>
				</div>
				<div class="modal-body">
					<table class='table' id='tableQueueList'>
						<thead><tr>
							<th>Queue</th><th>Order Details</th><th>Action</th>
						</tr>
						</thead>

						<tbody></tbody>
					</table>
				</div>

			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<div class="modal fade" id="membersModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title">Members</h3>
				</div>
				<div class="modal-body">

					<fieldset>

						<div class="form-group">
							<label class="col-md-4 control-label" for="membersText"><span class='glyphicon glyphicon-user'> Member's Name</label>
							<div class="col-md-8">
								<input type="text" id='membersText'  list='member_list' class='form-control' autocomplete="off"/>
							</div>
						</div>

					</fieldset>
					<hr>
					<div class='col-md-12'>Not yet registered? Register <a href='admin/addmember.php' class='text-danger'>HERE</a></div>

				</div>
				<div class="modal-footer">
					<button type="button" id='memberOk' class="btn btn-primary">OK </button>
					<button type="button" id='memberCancel' class="btn btn-primary">CANCEL </button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<div class="modal fade" id="serviceListModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
		<div class="modal-dialog" style="width:98%;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title">Service</h3>
				</div>
				<div class="modal-body">
					<h4 id='modalServiceName'></h4>
					<table class='table' id='tableServiceList'>
						<thead>
						<tr>
							<th>Service</th><th>Start Date</th><th>End Date</th><th>Consumable Qty</th><th></th>
						</tr>
						</thead>

						<tbody></tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" id='serviceOk' class="btn btn-primary">OK </button>
					<button type="button" id='serviceCancel' class="btn btn-primary">CANCEL </button>
				</div>

			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->


	<div class="modal fade" id="multiplessModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
		<div class="modal-dialog" style='width:95%;' >
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

					<div class="row">
					<div class="col-md-3">
						<div class="form-group">
						<input type="text" placeholder='Quantity' class='form-control' id='multiple_ss_qty'/>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
						<select name="ind_station_select2" id="ind_station_select2" class='form-control' style=''>


						</select>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
						<select name="selectSalesType2" id="selectSalesType2" class='form-control' style=''>

						</select>
						</div>
					</div>
						<div class="col-md-3">
							<div class="form-group">
							<button  class='btn btn-default' id='ind_multiple_ss_addmore'>
								<span class='glyphicon glyphicon-plus'></span> Add
							</button>
							</div>
						</div>
					</div>
					<div class="row">
						<br>
						<div class="container-fluid">
							<div id="no-more-tables">
							<table class='table' id='ind_multiple_ss_tbl' style='display:none;'>
								<thead>
								<tr><th>Quantity</th><th>Station Name</th><th>Sales Type</th><th></th></tr>
								</thead>
								<tbody>

								</tbody>
							</table>
							</div>
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
	<datalist id="item_list">
	</datalist>
	<datalist id="member_list">
	</datalist>
	</div>
	<script src='js/index.js'></script>

<?php require_once 'includes/page_tail.php'; ?>