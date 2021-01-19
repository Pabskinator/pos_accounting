$(function(){

	getMembers(localStorage['company_id']);
	getMemberOptList();
	function getMembers(company_id){
			$.ajax({
				url: "ajax/ajax_get_members.php",
				type:"POST",
				data:{company_id:company_id,type:1},
				success: function(data){
					if(data != 0)
					{
						localStorage['members']=data;
					} else {
						localStorage.removeItem('members');
					}
				}
			});
	}
	function getMemberOptList(){
		if(localStorage['members']){
			var mems = JSON.parse(localStorage['members']);
			$("#con_member").empty();
			$("#con_member").append("<option></option>");
			$("#con_member_freebies").empty();
			$("#con_member_freebies").append("<option></option>");
			$("#member_credit").empty();
			$("#member_credit").append("<option></option>");
			for(var i in mems){
				var amt =0;
				var amt_freebies = 0;

				if(mems[i].amt){
					var check_not_validyet =0;
					amt = mems[i].amt;
					if(mems[i].camt) check_not_validyet = mems[i].camt;
					amt = amt - check_not_validyet;
					$("#con_member").append("<option data-con='"+amt+"' value='"+mems[i].id+"'>"+mems[i].lastname+", " +mems[i].firstname + " " + mems[i].middlename +" ("+amt+")</option>");
				}
				if(mems[i].freebiesamount){
					amt_freebies = mems[i].freebiesamount;
				}
				$("#con_member_freebies").append("<option data-con_freebies='"+amt_freebies+"' value='"+mems[i].id+"'>"+mems[i].lastname+", " +mems[i].firstname + " " + mems[i].middlename +" ("+amt_freebies+")</option>");
				$("#member_credit").append("<option value='"+mems[i].id+"'>"+mems[i].lastname+", " +mems[i].firstname + " " + mems[i].middlename +"</option>");

			}
		}
	}

	$("#con_member").select2({
		placeholder: 'Choose member name...',
		allowClear: true
	}).on('select2-open',function(){

	}).on("select2-close", function(e) {
		// fired to the original element when the dropdown closes
		setTimeout(function() {
			$('.select2-container-active').removeClass('select2-container-active');
			$(':focus').blur();
		}, 100);
	});

	$("#con_member_freebies").select2({
		placeholder: 'Choose member name...',
		allowClear: true
	}).on('select2-open',function(){

	}).on("select2-close", function(e) {
		// fired to the original element when the dropdown closes
		setTimeout(function() {
			$('.select2-container-active').removeClass('select2-container-active');
			$(':focus').blur();
		}, 100);
	});



	$("#member_credit").select2({
		placeholder: 'Choose member name...',
		allowClear: true
	});

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
		localStorage.removeItem('payment_con_freebies');
		localStorage.removeItem('payment_member_credit');
	});

	$('body').on('click','.cashreceiveok',function(){
		receiveCash();
	});
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
	function cashHolderComputation(cash,change){
		$('#cashreceiveholder').empty();
		$('#changeholder').empty();
		$('#cashreceiveholder').append(number_format(cash,2));
		$('#changeholder').append(number_format(change,2));
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
		$("#totalOfAllPayment").html("<strong><span style='font-size:1.2em;' class='text-info' >Total Payment: " +gtotal.toFixed(2) + "</span></strong>");

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
		if(parseFloat(currentNotCash).toFixed(2) > parseFloat(grandtotal)){
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
	function showpricemodal(totalforfreebies,grandtotal){

		if (!totalforfreebies){
			localStorage['totalforfreebies'] = 0;
		} else {
			localStorage['totalforfreebies'] =totalforfreebies;
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
		$("#amountdue").html("<span style='font-size:1.2em;' class='text-info'><strong> Amount Due: " + grandtotal + "</strong></span>");
		$("#hidamountdue").val( replaceAll(grandtotal,',',''));
		$("#getpricemodal").modal("show");
		setTimeout(function() { $('#cashreceivetext').focus() }, 500);
	}
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
	$('body').on('click','#btnGetPayment',function(){
		showpricemodal('0','300');
	});
});