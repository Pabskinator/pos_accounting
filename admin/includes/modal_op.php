<input type="hidden" id="op_hidcashpayment" />
<input type="hidden" id="op_hidcreditpayment" />
<input type="hidden" id="op_hidbanktransferpayment" />
<input type="hidden" id="op_hidchequepayment" />
<span id='op_totalOfAllPayment' style='padding-left:10px;'></span>
<input type="hidden" id="op_hidTotalOfAllPayment" />
<ul class="nav nav-tabs">
	<li class="active"><a href="#over_payment_a" data-toggle="tab">Cash <span id='op_totalcashpayment' class='badge'></span></a></li>
	<li class='notcashlist'><a href="#over_payment_b" data-toggle="tab">Credit Card <span id='op_totalcreditpayment' class='badge'></span></a></li>
	<li class='notcashlist'><a href="#over_payment_c" data-toggle="tab">Bank Transfer <span id='op_totalbanktransferpayment' class='badge'></span></a></li>
	<li class='notcashlist'><a href="#over_payment_d" data-toggle="tab">Check 	<span id='op_totalchequepayment' class='badge'></span></a></li>
</ul>
<div class="tab-content">
	<br><br>
	<div class="tab-pane active" id="over_payment_a">
		<fieldset>
			<div class="form-group">
				<label class="col-md-3 control-label text-center" for="op_cashreceivetext">Amount</label>
				<div class="col-md-9">
					<input id="op_cashreceivetext" name="op_cashreceivetext" class="form-control input-md" type="text">
					<span class="help-block">Amount In Peso</span>
				</div>
			</div>
		</fieldset>
		<div class="form-group">
			<div class="row">
				<div class="col-md-9"></div>
				<div class="col-md-3">
					<button type="button" class="btn btn-primary op_cashreceiveok">OK </button>
					<button type="button"  class="btn btn-primary op_cashreceivecancel">CANCEL </button>
				</div>
			</div>
		</div>
	</div>

	<div class="tab-pane" id="over_payment_b">
		<fieldset>
			<legend>Billing Information</legend>
			<div class="row">
				<table class="table" id="op_credit_table"></table>
			</div>
			<div class="row">
				<div class="col-md-1"></div>
				<div class="col-md-3"><input id="op_billing_cardnumber" name="op_billing_cardnumber" placeholder="Card #" class="form-control input-md" type="text">
				</div>
				<div class="col-md-3"><input id="op_billing_amount" name="op_billing_amount" placeholder="Amount" class="form-control input-md" type="text"></div>
				<div class="col-md-3"><input id="op_billing_bankname" name="op_billing_bankname" placeholder="Bank" class="form-control input-md" type="text"></div>
				<div class="col-md-2"><input type="button" id='op_addcreditcard' class='btn btn-default' value='Add'/></div>
			</div>
			<hr>
			<div class="row">
				<div class="col-md-4">
					Card Type (Visa/Mastercard) <span class='text-danger'>*</span>
					<input id="op_billing_card_type" name="op_billing_card_type" class="form-control input-md" type="text">
					<span class="help-block"></span>
				</div>
				<div class="col-md-4">
					Trace Number <span class='text-danger'>*</span>
					<input id="op_billing_trace_number" name="op_billing_trace_number" class="form-control input-md" type="text">
					<span class="help-block"></span>
				</div>
				<div class="col-md-4">
					Approval code <span class='text-danger'>*</span>
					<input id="op_billing_approval_code" name="op_billing_approval_code" class="form-control input-md" type="text">
					<span class="help-block"></span>
				</div>
				<div class="col-md-4">
					Date <span class='text-danger'>*</span>
					<input id="op_billing_date" name="op_billing_date" class="form-control input-md" type="text">
					<span class="help-block"></span>
				</div>
			</div>
			<hr />
			<div class="form-group">
				<div class="col-md-4">
					First Name <span class='text-danger'></span>
					<input id="op_billing_firstname" name="op_billing_firstname" class="form-control input-md" type="text">
					<span class="help-block"></span>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-4">
					Middle Name
					<input id="op_billing_middlename" name="op_billing_middlename" class="form-control input-md" type="text">
					<span class="help-block"></span>
				</div>
			</div>
			<div class="form-group">

				<div class="col-md-4">
					Last Name <span class='text-danger'></span>
					<input id="op_billing_lastname" name="op_billing_lastname" class="form-control input-md" type="text">
					<span class="help-block"></span>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-4">
					Company
					<input id="op_billing_company" name="op_billing_company" class="form-control input-md" type="text">
					<span class="help-block"></span>
				</div>
			</div>

			<div class="form-group">
				<div class="col-md-4">
					Address
					<input id="op_billing_address" name="op_billing_address" class="form-control input-md" type="text">
					<span class="help-block"></span>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-4">
					Zip/Postal Code
					<input id="op_billing_postal" name="op_billing_postal" class="form-control input-md" type="text">
					<span class="help-block"></span>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-4">
					Cellphone/Tel Number
					<input id="op_billing_phone" name="op_billing_phone" class="form-control input-md" type="text">
					<span class="help-block"></span>
				</div>
			</div>


			<div class="form-group">
				<div class="col-md-4">
					Email
					<input id="op_billing_email" name="op_billing_email" class="form-control input-md" type="text">
					<span class="help-block"></span>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-4">
					Special Notes
					<input id="op_billing_remarks" name="op_billing_remarks" class="form-control input-md" type="text">
					<span class="help-block"></span>
				</div>
			</div>
		</fieldset>
		<div class="form-group">
			<div class="row">
				<div class="col-md-9"></div>
				<div class="col-md-3">
					<button type="button" class="btn btn-primary op_cashreceiveok">OK </button>
					<button type="button"  class="btn btn-primary op_cashreceivecancel">CANCEL </button>
				</div>
			</div>
		</div>
	</div>
	<div class="tab-pane" id="over_payment_c">
		<fieldset>
			<legend>Billing Information</legend>
			<div class="row">
				<table class="table" id="op_bt_table"></table>
			</div>
			<div class="row">
				<div class="col-md-1"></div>
				<div class="col-md-3"><input id="op_bankfrom_account_number" name="op_bankfrom_account_number" placeholder="Account #" class="form-control input-md" type="text">
				</div>
				<div class="col-md-3"><input id="op_bt_amount" name="op_bt_amount" placeholder="Amount" class="form-control input-md" type="text"></div>
				<div class="col-md-3"><input id="op_bankfrom_name" name="op_bankfrom_name" placeholder="Bank" class="form-control input-md" type="text"></div>
				<div class="col-md-2"><input type="button" id='op_addbanktransfer' class='btn btn-default' value='Add'/></div>
			</div>
			<hr />
			<div class="form-group">
				<div class="col-md-4">
					Date <span class='text-danger'>*</span>
					<input id="op_bt_date" name="op_bt_date" class="form-control input-md" type="text">
					<span class="help-block"></span>
				</div>
				<div class="col-md-4">
					Transfer to
					<input id="op_bt_bankto_name" name="op_bt_bankto_name" class="form-control input-md" type="text">
					<span class="help-block"></span>
				</div>
				<div class="col-md-4">
					Bank Account Number
					<input id="op_bt_bankto_account_number" name="op_bt_bankto_account_number" class="form-control input-md" type="text">
					<span class="help-block"></span>
				</div>
			</div>

			<div class="form-group">
				<div class="col-md-4">
					First Name
					<input id="op_bt_firstname" name="op_bt_firstname" class="form-control input-md" type="text">
					<span class="help-block"></span>
				</div>
				<div class="col-md-4">
					Middle Name
					<input id="op_bt_middlename" name="op_bt_middlename" class="form-control input-md" type="text">
					<span class="help-block"></span>
				</div>
				<div class="col-md-4">
					Last Name
					<input id="op_bt_lastname" name="op_bt_lastname" class="form-control input-md" type="text">
					<span class="help-block"></span>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-4">
					Company
					<input id="op_bt_company" name="op_bt_company" class="form-control input-md" type="text">
					<span class="help-block"></span>
				</div>
				<div class="col-md-4">
					Address
					<input id="op_bt_address" name="op_bt_address" class="form-control input-md" type="text">
					<span class="help-block"></span>
				</div>
				<div class="col-md-4">
					Zip/Postal Code
					<input id="op_bt_postal" name="op_bt_postal" class="form-control input-md" type="text">
					<span class="help-block"></span>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-4">
					Cellphone/Tel Number
					<input id="op_bt_phone" name="op_bt_phone" class="form-control input-md" type="text">
					<span class="help-block"></span>
				</div>
			</div>

		</fieldset>
		<div class="form-group">
			<div class="row">
				<div class="col-md-9"></div>
				<div class="col-md-3">
					<button type="button" class="btn btn-primary op_cashreceiveok">OK </button>
					<button type="button"  class="btn btn-primary op_cashreceivecancel">CANCEL </button>
				</div>
			</div>
		</div>

	</div>
	<div class="tab-pane" id="over_payment_d">
		<fieldset>
			<legend>Billing Information</legend>
			<div class="row">
				<table class="table" id="op_ch_table"></table>
			</div>
			<div class="row">
				<div class="row">
					<div class="col-md-6"><input id="op_ch_date" name="op_ch_date" placeholder="Maturity Date" class="form-control input-md" type="text"></div>
					<div class="col-md-6"><input id="op_ch_number" name="op_ch_number" placeholder="Cheque #" class="form-control input-md" type="text">
					</div>
					<hr />
				</div>
				<div class="row">
					<div class="col-md-6"><input id="op_ch_amount" name="op_ch_amount" placeholder="Amount" class="form-control input-md" type="text"></div>
					<div class="col-md-6"><input id="op_ch_bankname" name="op_ch_bankname" placeholder="Bank" class="form-control input-md" type="text"></div>
				</div>
				<hr />
				<div class="row">
					<div class="col-md-2"><input type="button" id='op_addcheque' class='btn btn-default' value='Add'/></div>
				</div>
			</div>
			<hr />

			<div class="form-group">

				<div class="col-md-3">
					First Name
					<input id="op_ch_firstname" name="op_ch_firstname" class="form-control input-md" type="text">
					<span class="help-block"></span>
				</div>
				<div class="col-md-3">
					Middle Name
					<input id="op_ch_middlename" name="op_ch_middlename" class="form-control input-md" type="text">
					<span class="help-block"></span>
				</div>
				<div class="col-md-3">
					Last Name
					<input id="op_ch_lastname" name="op_ch_lastname" class="form-control input-md" type="text">
					<span class="help-block"></span>
				</div>
				<div class="col-md-3">
					Cellphone/Tel Number
					<input id="op_ch_phone" name="op_ch_phone" class="form-control input-md" type="text">
					<span class="help-block"></span>
				</div>
			</div>

		</fieldset>
		<div class="form-group">
			<div class="row">
				<div class="col-md-9"></div>
				<div class="col-md-3">
					<button type="button" class="btn btn-primary op_cashreceiveok">OK </button>
					<button type="button"  class="btn btn-primary op_cashreceivecancel">CANCEL </button>
				</div>
			</div>
		</div>

	</div>
</div>
