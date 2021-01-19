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
			<div class="col-md-4">
				Card Number
				<input id="billing_cardnumber" name="billing_cardnumber" placeholder="Card #" class="form-control input-md" type="text">
			</div>
			<div class="col-md-4">
				Amount
				<input id="billing_amount" name="billing_amount" placeholder="Amount" class="form-control input-md" type="text">
			</div>
			<div class="col-md-4">
				Bank
				<input id="billing_bankname" name="billing_bankname" placeholder="Bank" class="form-control input-md" type="text">
			</div>
		</div>

		<div class="row">
			<div class="col-md-4">
				Card Type (Visa/Mastercard) <span class='text-danger'>*</span>
				<input id="billing_card_type" name="billing_card_type" class="form-control input-md" type="text">
			</div>
			<div class="col-md-4">
				Trace Number <span class='text-danger'>*</span>
				<input id="billing_trace_number" name="billing_trace_number" class="form-control input-md" type="text">
			</div>
			<div class="col-md-4">
				Approval code <span class='text-danger'>*</span>
				<input id="billing_approval_code" name="billing_approval_code" class="form-control input-md" type="text">
			</div>
			<div class="col-md-4">
				Date <span class='text-danger'>*</span>
				<input id="billing_date" name="billing_date" class="form-control input-md" type="text">
			</div>
			<div class="col-md-4">
				<br>
				<input type="button" id='addcreditcard' class='btn btn-default' value='Add'/>
			</div>
		</div>

		<hr />
		<div class="form-group">
			<div class="col-md-4">
				First Name <span class='text-danger'></span>
				<input id="billing_firstname" name="billing_firstname" class="form-control input-md" type="text">
				<span class="help-block"></span>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-4">
				Middle Name
				<input id="billing_middlename" name="billing_middlename" class="form-control input-md" type="text">
				<span class="help-block"></span>
			</div>
		</div>
		<div class="form-group">

			<div class="col-md-4">
				Last Name <span class='text-danger'></span>
				<input id="billing_lastname" name="billing_lastname" class="form-control input-md" type="text">
				<span class="help-block"></span>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-4">
				Company
				<input id="billing_company" name="billing_company" class="form-control input-md" type="text">
				<span class="help-block"></span>
			</div>
		</div>

		<div class="form-group">
			<div class="col-md-4">
				Address
				<input id="billing_address" name="billing_address" class="form-control input-md" type="text">
				<span class="help-block"></span>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-4">
				Zip/Postal Code
				<input id="billing_postal" name="billing_postal" class="form-control input-md" type="text">
				<span class="help-block"></span>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-4">
				Cellphone/Tel Number
				<input id="billing_phone" name="billing_phone" class="form-control input-md" type="text">
				<span class="help-block"></span>
			</div>
		</div>


		<div class="form-group">
			<div class="col-md-4">
				Email
				<input id="billing_email" name="billing_email" class="form-control input-md" type="text">
				<span class="help-block"></span>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-4">
				Special Notes
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
			<div class="col-md-4">
				Date <span class='text-danger'>*</span>
				<input id="bt_date" name="bt_date" class="form-control input-md" type="text">
				<span class="help-block"></span>
			</div>
			<div class="col-md-4">
				Transfer to
				<input id="bt_bankto_name" name="bt_bankto_name" class="form-control input-md" type="text">
				<span class="help-block"></span>
			</div>
			<div class="col-md-4">
				Bank Account Number
				<input id="bt_bankto_account_number" name="bt_bankto_account_number" class="form-control input-md" type="text">
				<span class="help-block"></span>
			</div>
		</div>

		<div class="form-group">
			<div class="col-md-4">
				First Name
				<input id="bt_firstname" name="bt_firstname" class="form-control input-md" type="text">
				<span class="help-block"></span>
			</div>
			<div class="col-md-4">
				Middle Name
				<input id="bt_middlename" name="bt_middlename" class="form-control input-md" type="text">
				<span class="help-block"></span>
			</div>
			<div class="col-md-4">
				Last Name
				<input id="bt_lastname" name="bt_lastname" class="form-control input-md" type="text">
				<span class="help-block"></span>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-4">
				Company
				<input id="bt_company" name="bt_company" class="form-control input-md" type="text">
				<span class="help-block"></span>
			</div>
			<div class="col-md-4">
				Address
				<input id="bt_address" name="bt_address" class="form-control input-md" type="text">
				<span class="help-block"></span>
			</div>
			<div class="col-md-4">
				Zip/Postal Code
				<input id="bt_postal" name="bt_postal" class="form-control input-md" type="text">
				<span class="help-block"></span>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-4">
				Cellphone/Tel Number
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

			<div class="col-md-3">
				First Name
				<input id="ch_firstname" name="ch_firstname" class="form-control input-md" type="text">
				<span class="help-block"></span>
			</div>
			<div class="col-md-3">
				Middle Name
				<input id="ch_middlename" name="ch_middlename" class="form-control input-md" type="text">
				<span class="help-block"></span>
			</div>
			<div class="col-md-3">
				Last Name
				<input id="ch_lastname" name="ch_lastname" class="form-control input-md" type="text">
				<span class="help-block"></span>
			</div>
			<div class="col-md-3">
				Cellphone/Tel Number
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
			<label class="col-md-3 control-label text-center" for="con_member">Client</label>
			<div class="col-md-9">
				<select name="con_member" id="con_member" class='form-control'>
				</select>
				<span class="help-block">Choose client name</span>
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
	<p class='alert alert-info' id='consumable_remarks_holder'></p>
</div>
<div class="tab-pane" id="tab_f">

	<fieldset>
		<div class="form-group">
			<label class="col-md-3 control-label text-center" for="con_member_freebies">Client</label>
			<div class="col-md-9">
				<select name="con_member_freebies" id="con_member_freebies" class='form-control'>
				</select>
				<span class="help-block">Choose client name</span>
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
			<label class="col-md-3 control-label text-center" for="member_credit">Client</label>
			<div class="col-md-9">
				<select name="member_credit" id="member_credit" class='form-control'>
				</select>
				<span class="help-block">Choose client name</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label text-center" for="member_credit_amount">Amount</label>
			<div class="col-md-9">
				<input id="member_credit_amount" name="member_credit_amount" class="form-control input-md" type="text">
				<span class="help-block">Amount In Peso</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label text-center" for="member_credit_cod">COD</label>
			<div class="col-md-9">
				<input id="member_credit_cod" name="member_credit_cod" class="" type="checkbox">
				<span class="">Cash on delivery</span>
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
		<div>
			<table id="member_deduction_table" class='table'>
				<thead><tr><th>Client</th><th>Amount</th><th>Type</th><th>Remarks</th><th></th><th></th></tr></thead>
				<tbody></tbody>
			</table>
		</div>
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
		<div class="form-group">
			<label class="col-md-3 control-label text-center" for="member_deduction_remarks">Type</label>
			<div class="col-md-9">
				<?php
					$deduction_list_cls = new Deduction_list();
					$deduction_list_results = $deduction_list_cls->get_active('deduction_list',array('company_id' ,'=',$user->data()->company_id));
				?>
				<select class='form-control'name="member_deduction_remarks" id="member_deduction_remarks">
					<option value="">Select remarks</option>
					<?php if($deduction_list_results){
						foreach($deduction_list_results as $dlr){
							echo "<option value='$dlr->name'>$dlr->name</option>";
						}
					}
					?>
				</select>
				<span class="help-block">(Optional)</span>
			</div>
		</div>

		<?php if(Configuration::thisCompany('cebuhiq')){
			?>
			<div class="form-group">
				<label class="col-md-3 control-label text-center" for="member_deduction_addtl_remarks">Remarks</label>
				<div class="col-md-9">

					<input class='form-control' placeholder='Addtl Remarks' name="member_deduction_addtl_remarks" id="member_deduction_addtl_remarks">

					<span class="help-block">(Optional)</span>
				</div>
			</div>
			<div class="form-group"><label class="col-md-3 control-label text-center">&nbsp;</label><input type="checkbox" id='member_deduction_is_approved'> <label for="member_deduction_is_approved">For approval</label></div>
			<?php
		}?>
		<div class="form-group">
			<div class="col-md-3 control-label text-center">&nbsp;</div>
			<div class="col-md-9">
			<button class='btn btn-default' id='btnAddMoreMemberDeduction'>Add</button>
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