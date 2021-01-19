$(document).ready(function(){

	localStorage.removeItem('op_payment_cheque');
	localStorage.removeItem('op_payment_credit');
	localStorage.removeItem('op_payment_bt');
	localStorage.removeItem('op_payment_cash');
	var invoice_label = 'Invoice';
	var dr_label = 'DR';
	var pr_label = 'PR';
	function str_pad(pad, str, padLeft) {
		if (typeof str === 'undefined')
			return pad;
		if (padLeft) {
			return (pad + str).slice(-pad.length);
		} else {
			return (str + pad).substring(0, pad.length);
		}
	}
	function activaTab(tab){
		$('.nav-tabs a[href="#' + tab + '"]').tab('show');
	}
	noItemInCart();
	function noItemInCart() {
		if(!$("#cart tbody").children().length) {
			$("#cart tbody").append("<td data-title='Remarks' colspan='3' id='noitem' style='padding-top:10px;' ><span class='text-danger'>NO ITEMS IN CART</span></td>");
		}
	}
	$('body').on('click', '.removeItem', function() {
		$(this).parents('tr').remove();
		noItemInCart();
		updatesubtotal();
	});

	$('#void').click(function() {
		$("#cart").find("tr:gt(0)").remove();
		noItemInCart();
		updatesubtotal();
	});
	function removeNoItemLabel() {
		$("#noitem").remove();
	}

	function updatesubtotal(){
		var totals = [];
		$('#cart > tbody  > tr ').each(function() {
			totals.push(replaceAll($(this).children().eq(4).text(),',',''));
		});
		var	grandtotal=0;
		var vat = 1.12;
		var subtotal = 0;
		for(var i=0; i<totals.length;i++){
			grandtotal += parseFloat(totals[i]);
		}
		subtotal = (grandtotal / vat);
		vat = parseFloat(grandtotal) - parseFloat(subtotal);
		subtotal = subtotal.toFixed(2);
		vat = vat.toFixed(2);
		grandtotal = grandtotal.toFixed(2);
		$("#subtotalholder").empty();
		$("#vatholder").empty();
		$("#grandtotalholder").empty();
		$("#changeholder").empty();
		$("#cashreceiveholder").empty();
		$("#changeholder").append(0);
		$("#cashreceiveholder").append(0);
		$("#subtotalholder").append(number_format(subtotal,2));
		$("#vatholder").append(number_format(vat,2));
		$("#grandtotalholder").append(number_format(grandtotal,2));
		toggleMemberInput();
	}

	$('body').on('change','#item_id',function(){
		var itemCon = $('#item_id');
		var item_id = 	itemCon.val();
		var item_adjustment = 	$('#item_adjustment').val();
		item_adjustment = (item_adjustment ) ? item_adjustment : 0;

		if(isNaN(item_adjustment)){
			item_adjustment = 0;
		}

		var item_code = (itemCon.select2('data').text).split(':');
		var price = item_code[3];
		var barcode = item_code[0];
		var desc = item_code[2];
		var itemc = item_code[1];
		var is_bundle = itemCon.select2('data').is_bundle;
		var cdays = itemCon.select2('data').cdays;
		var cqty = itemCon.select2('data').cqty;

		var qty = $('#qty').val();
		var member_id = $('#member_id').val();
		if(item_id  && qty){
			if(isNaN(qty) || parseFloat(qty) < 0){
				alertify.alert('Invalid Quantity');
				itemCon.select2('val',null);
				$('#qty').val('');
				return;
			}
			$('.loading').show();
			$.ajax({
				url:'../ajax/ajax_query2.php',
				type:'POST',
				data: {functionName:'getAdjustmentPrice',branch_id:localStorage['branch_id'],item_id:item_id,member_id:member_id,qty:qty},
				success: function(data){
					var dt = JSON.parse(data);
					var data = dt.data;
					var splitted = data.split('||');
					price = parseFloat(price) + parseFloat(splitted[0]);
					var adjustmentmem = splitted[1];
					item_code = item_code[2]+"<small style='display:block' class='text-danger'>"+item_code[1]+"</small>";
					var total = parseFloat(price) * parseFloat(qty) ;

					adjustmentmem = number_format(adjustmentmem,2,".","");
					adjustmentmem = parseFloat(adjustmentmem) + parseFloat(item_adjustment);
					$('#item_adjustment').val('');
					price = number_format(price,2,".","");
					total = parseFloat(total) + parseFloat(adjustmentmem);
					total = number_format(total,2);
					//$('#cart tbody').append("<tr data-unit_name='"+itemCon.select2('data').unit_name+"' data-barcode='"+barcode+"' data-itemcode='"+itemc+"' data-desc='"+desc+"' data-item_id='"+item_id+"' data-price_adjustment='"+splitted[0]+"' data-member_adjustment='"+adjustmentmem+"'><td>"+item_code+"</td><td>"+qty+"</td><td>"+price+"</td><td>"+adjustmentmem+"</td><td>"+total+"</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
					$("<tr data-cdays='"+cdays+"' data-cqty='"+cqty+"' data-is_bundle='"+is_bundle+"' data-unit_name='"+itemCon.select2('data').unit_name+"' data-barcode='"+barcode+"' data-itemcode='"+itemc+"' data-desc='"+desc+"' data-item_id='"+item_id+"' data-price_adjustment='"+splitted[0]+"' data-member_adjustment='"+adjustmentmem+"'><td>"+item_code+"</td><td>"+qty+"</td><td>"+price+"</td><td>"+adjustmentmem+"</td><td>"+total+"</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>").prependTo("#cart tbody");
					itemCon.select2('val',null);
					$('#qty').val('');
					removeNoItemLabel();
					updatesubtotal();
					$('.loading').hide();
				},
				error:function(){
					$('.loading').hide();
					alert('It seems like you have a very slow internet connection.');
				}
			});
		} else {
			itemCon.select2('val',null);
			alertify.alert('Please complete the form');
		}
	});
	$('#sales_type').select2({
		placeholder: 'Select Sales Type',
		allowClear: true
	});

	$('#agent_id').select2({
		placeholder: 'Select Agent',
		allowClear: true
	});

	$("#member_id").select2({
		placeholder: 'Search Member',
		allowClear: true,
		minimumInputLength: 2,
		ajax: {
			url: '../ajax/ajax_json.php',
			dataType: 'json',
			type: "POST",
			quietMillis: 50,
			data: function (term) {
				return {
					q: term,
					functionName:'members'
				};
			},
			results: function (data) {
				return {
					results: $.map(data, function (item) {
						return {
							text: item.lastname + ", " + item.sales_type_name,
							slug: item.lastname + ", " + item.firstname + " " + item.middlename,
							address: item.personal_address,
							id: item.id
						}
					})
				};
			}
		}
	});

	$('body').on('change','#member_id',function(){
		getMembersInd(localStorage['company_id'],$(this).val());
		getLastSoldItem();
	});
	$('#custom_date_sold').datepicker({
		autoclose:true
	}).on('changeDate', function(ev){
		$('#custom_date_sold').datepicker('hide');
	});

	//getMembers(localStorage['company_id']);
	//getMemberOptList();
	getCurrentInvoice(localStorage['terminal_id']);
	getLayout();
	checkTerminal();
	getServerTime();
	function checkInvDrPr(){
		if(localStorage['terminal_id']){
			$.ajax({
				url: "../ajax/ajax_get_branchAndTerminal.php",
				type:"POST",
				dataType:"json",
				data:{terminal_id:localStorage['terminal_id'],type:7,invoice:localStorage['invoice'],dr:localStorage['dr'],ir:localStorage['ir']},
				success: function(data){
					if(data.msg){
						alertify.alert(data.msg);
						if(data.next.invoice){
							localStorage['invoice'] = data.next.invoice;
							displayNextInvoice();
						}
						if(data.next.dr){
							localStorage['dr'] = data.next.dr;
							displayNextDr();
						}
						if(data.next.ir){
							localStorage['ir'] = data.next.ir;
							displayNextIr();
						}
					}
				}
			});
		}
	}
	function getLayout(){
		if(localStorage['company_id']){
			$.ajax({
				url: "../ajax/ajax_query.php",
				type:"POST",
				data:{cid:localStorage['company_id'],functionName:'getDocumentLayout'},
				success: function(data){
					var formatStyle = JSON.parse(data);
					localStorage["invoice_format"] = formatStyle.invoice;
					localStorage["dr_format"] = formatStyle.dr;
					localStorage["ir_format"] = formatStyle.ir;
					localStorage["news_format"] = formatStyle.extra;
				}
			});
		}
	}
	function getCurrentInvoice(tid){
		if(localStorage['terminal_id'] != null){
			$.ajax({
				url: "../ajax/ajax_get_branchAndTerminal.php",
				type:"POST",
				data:{cid:tid,type:3},
				success: function(data){
					var invarr = data.split(":");

					localStorage["invoice"] = invarr[0];
					localStorage["end_invoice"] = invarr[1];
					localStorage["dr"] = invarr[2];
					localStorage["end_dr"] = invarr[3];
					localStorage["invoice_limit"] = invarr[4];
					localStorage["dr_limit"] = invarr[5];
					localStorage["ir"] = invarr[6];
					localStorage["end_ir"] = invarr[7];
					localStorage["ir_limit"] = invarr[8];
					localStorage["speed_opt"] = invarr[9];
					localStorage["use_printer"] = invarr[10];
					localStorage["data_sync"] = invarr[11];
					localStorage["news_print"] = invarr[12];
					localStorage["print_inv"] = invarr[13];
					localStorage["print_dr"] = invarr[14];
					localStorage["print_ir"] = invarr[15];
					localStorage["pref_inv"] = invarr[16];
					localStorage["pref_dr"] = invarr[17];
					localStorage["pref_ir"] = invarr[18];

					displayNextInvoice();
					displayNextDr();
					displayNextIr();
					checkInvDrPr();
				}
			});

		}
	}
	function getMembersInd(company_id,member_id){

		$("#con_member").empty();
		$("#con_member").append("<option></option>");
		$("#con_member_freebies").empty();
		$("#con_member_freebies").append("<option></option>");
		$("#member_credit").empty();
		$("#member_credit").append("<option></option>");
		$("#member_deduction").empty();
		$("#member_deduction").append("<option></option>");

		if(member_id){
			$.ajax({
				url: "../ajax/ajax_get_members.php",
				type:"POST",
				data:{company_id:company_id,member_id:member_id,type:1},
				success: function(data){
					if(data != 0)
					{
						var mems = JSON.parse(data);
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
							$("#member_deduction").append("<option value='"+mems[i].id+"'>"+mems[i].lastname+", " +mems[i].firstname + " " + mems[i].middlename +"</option>");
						}
						$("#con_member_freebies").select2('val',member_id);
						$("#member_credit").select2('val',member_id);
						$("#con_member").select2('val',member_id);
						$("#member_deduction").select2('val',member_id);
						$("#con_member_freebies").attr('disabled',true);
						$("#member_credit").attr('disabled',true);
						$("#member_deduction").attr('disabled',true);
						$("#con_member").attr('disabled',true);
					}
				}
			});
		}

	}
	function getMembers(company_id){
		$.ajax({
			url: "../ajax/ajax_get_members.php",
			type:"POST",
			data:{company_id:company_id,type:1},
			success: function(data){
				if(data != 0)
				{
					localStorage['members']= data;
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

		setTimeout(function() {
			$('.select2-container-active').removeClass('select2-container-active');
			$(':focus').blur();
		}, 100);
	});



	$("#member_credit").select2({
		placeholder: 'Choose member name...',
		allowClear: true
	});
	$("#member_deduction").select2({
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
		localStorage.removeItem('payment_member_deduction');
		localStorage.removeItem('op_payment_cheque');
		localStorage.removeItem('op_payment_credit');
		localStorage.removeItem('op_payment_bt');
		localStorage.removeItem('op_payment_cash');
	});

	$('body').on('click','.cashreceiveok',function(){
		receiveCash();
	});
	$('#ch_date').datepicker({
		autoclose:true
	}).on('changeDate', function(ev){
		$('#ch_date').datepicker('hide');
	});
	$('#billing_date').datepicker({
		autoclose:true
	}).on('changeDate', function(ev){
		$('#billing_date').datepicker('hide');
	});
	$('#bt_date').datepicker({
		autoclose:true
	}).on('changeDate', function(ev){
		$('#bt_date').datepicker('hide');
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
		var member_deduction_amount = $("#hidmemberdeduction").val();
		if(!member_deduction_amount) member_deduction_amount = 0;
		var member_credit_cod = $('#member_credit_cod').is(':checked');
		member_credit_cod = (member_credit_cod) ? 1 : 0;
		var totalpayment = parseFloat(cash) + parseFloat(credit) + parseFloat(banktransfer) + parseFloat(cheque) + parseFloat(con_amount) + parseFloat(con_amount_freebies) + parseFloat(member_credit_amount) + parseFloat(member_deduction_amount);
		var grandtotal = parseFloat($("#hidamountdue").val());


		totalpayment= number_format(totalpayment,2,'.','');
		grandtotal= number_format(grandtotal,2,'.','');

		if(parseFloat(totalpayment) < 0 || parseFloat(totalpayment) < parseFloat(grandtotal)) {
			cashHolderComputation(0,0);
			showToast('error','<p>Invalid payment</p>','<h3>WARNING!</h3>','toast-bottom-right');
			return;
		} else {
			if(!isValidFormCheque() || !isValidFormCredit() || !isValidFormBankTransfer() || !isValidFormDeduction()){
				return;
			}

			var change = parseFloat(totalpayment) - parseFloat(grandtotal);
			cash = parseFloat(cash) - parseFloat(change);
			localStorage['payment_cash'] = cash;
			localStorage['payment_con'] = con_amount;
			localStorage['payment_con_freebies'] = con_amount_freebies;
			localStorage['payment_member_credit'] = member_credit_amount;


			var payment_credit;
			var payment_bt;
			var payment_cheque;
			var payment_cash;
			var payment_con_freebies;
			var payment_con;
			var payment_member_credit;
			var payment_member_deduction;
			var order_id = $('#payment_order_id').val();
			if(localStorage['payment_cash']){
				payment_cash = localStorage['payment_cash'];
			}
			if(localStorage['payment_con']){
				payment_con = localStorage['payment_con'];
			}
			if(localStorage['payment_con_freebies']){
				payment_con_freebies = localStorage['payment_con_freebies'];
			}
			if(localStorage['payment_member_credit']){
				payment_member_credit = localStorage['payment_member_credit'];
			}
			if(localStorage['payment_member_deduction']){
				payment_member_deduction = localStorage['payment_member_deduction'];
			}
			if(localStorage['payment_credit']){
				payment_credit = localStorage['payment_credit'];
			}
			if(localStorage['payment_bt']){
				payment_bt = localStorage['payment_bt'];
			}
			if(localStorage['payment_cheque']){
				payment_cheque = localStorage['payment_cheque'];
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
		var member_deduction_amount = $("#hidmemberdeduction").val();
		if(!member_deduction_amount){
			member_deduction_amount=0;
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
		var gtotal = parseFloat(cash) + parseFloat(con_amount) + parseFloat(con_amount_freebies) + parseFloat(member_credit_amount) + parseFloat(credit_amount) + parseFloat(bt_amount) + parseFloat(ck_amount)+ parseFloat(member_deduction_amount);
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
	function updateMemberDeduction(){
		var member_deduction_amount=0;
		$('#member_deduction_table > tbody > tr').each(function(i){
				var row = $(this);
				var amount = row.attr('data-amount');
				amount = parseFloat(amount);
				member_deduction_amount = parseFloat(member_deduction_amount) + parseFloat(amount);

		});
		$("#totalmemberdeduction").html(member_deduction_amount);
		$("#hidmemberdeduction").val(member_deduction_amount);
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
		var member_deduction_amount = $("#hidmemberdeduction").val();
		if(!member_deduction_amount) member_deduction_amount = 0;
		var grandtotal = parseFloat($("#hidamountdue").val());

		var currentNotCash =   parseFloat(credit) + parseFloat(banktransfer) + parseFloat(cheque) + parseFloat(con_amount) + parseFloat(con_amount_freebies)  + parseFloat(member_credit_amount)+ parseFloat(member_deduction_amount);
		if(addme){
			currentNotCash = parseFloat(currentNotCash) + parseFloat(a);
		}
		if(parseFloat(currentNotCash).toFixed(2) > parseFloat(grandtotal)){
			return true;
		} else {
			return false;
		}
	}

	function isValidFormDeduction(){
		if($("#member_deduction_table > tbody > tr").children().length ){
			var deductionArray = new Array();
			var member_id = $('#member_deduction').val();

			$("#member_deduction_table > tbody > tr").each(function(index){
				var row = $(this);
				var amount = row.attr('data-amount');
				var remarks = row.children().eq(2).text();
				deductionArray[index] = {
					member_id : member_id,
					amount : amount,
					remarks : remarks
				}
			});
			localStorage['payment_member_deduction'] = JSON.stringify(deductionArray);
			return true;
		}

		return true;
	}
	function isValidFormCheque(){
		if($("#ch_table tr").children().length ){
			var chequeArray = new Array();
			var fn = $("#ch_firstname").val();
			var mn = $("#ch_middlename").val();
			var ln = $("#ch_lastname").val();
			var phone = $("#ch_phone").val();

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
			localStorage['payment_cheque'] = JSON.stringify(chequeArray);
			return true;
		}

		return true;
	}

	function isValidFormCredit(){
		if($("#credit_table tr").children().length ){
			var creditArray = new Array();
			var  fn = $("#billing_firstname").val();
			var  mn = $("#billing_middlename").val();
			var  ln = $("#billing_lastname").val();
			var  comp = $("#billing_company").val();
			var  add = $("#billing_address").val();
			var  postal = $("#billing_postal").val();
			var  phone = $("#billing_phone").val();
			var  email = $("#billing_email").val();
			var  rem = $("#billing_remarks").val();
			// required
			/*var card_type = $("#billing_card_type").val();
			var trace_number = $("#billing_trace_number").val();
			var approval_code = $("#billing_approval_code").val();
			var date = $("#billing_date").val(); */

			if(false){
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
					showToast('error','<p>Phone should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
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
						remarks:rem,
						card_type:row.attr('data-card_type'),
						trace_number:row.attr('data-trace_number'),
						approval_code:row.attr('data-approval_code'),
						date:row.attr('data-date')
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
			var  date = $("#bt_date").val();

			if(!date){
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
				if(mn & !isAlphaNumeric(mn)){
					showToast('error','<p>Middle name should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return false;
				}
				if(ln && !isAlphaNumeric(ln)){
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
						postal:postal,
						date:date
					}
				});
				localStorage['payment_bt'] = JSON.stringify(bankTransferArray);
				return true;
			}
		}
		return true;
	}

	function isAlphaNumeric(str){
		var rexp = /^[\w\-\s\.,??]+$/
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
		var over_payment_list;
		try{
			over_payment_list = JSON.parse($('#op_member_list').val());
			if(over_payment_list.length > 0){
				$('#use_user_overpayment').show();
			}
		}catch(e){
			console.log("No over payment");
		}
		if (!totalforfreebies){
			localStorage['totalforfreebies'] = 0;
		} else {
			localStorage['totalforfreebies'] =totalforfreebies;
		}

		localStorage.removeItem('payment_cheque');
		localStorage.removeItem('payment_member_deduction');
		localStorage.removeItem('payment_credit');
		localStorage.removeItem('payment_bt');
		localStorage.removeItem('payment_cash');
		localStorage.removeItem('payment_con');
		localStorage.removeItem('payment_con_freebies');
		localStorage.removeItem('payment_member_credit');
		$("#credit_table tbody").html('');
		$("#member_deduction_table tbody").html('');
		$("#credit_table tbody").html('');
		$("#bt_table tbody").html('');
		$("#ch_table tbody").html('');
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
		updateMemberDeduction();
		$("#amountdue").html("<span style='font-size:1.2em;' class='text-info'><strong> Amount Due: " + grandtotal + "</strong></span>");
		$("#hidamountdue").val( replaceAll(grandtotal,',',''));
		try{
			var memdata = JSON.parse($('#mem_data').val());
			if(memdata.is_blacklisted == 1){
				$('.notcashlist').hide();
			} else {
				$('.notcashlist').show();
			}
		} catch(e){

		}

		$("#getpricemodal").modal("show");
		setTimeout(function() {

			if($('#withConsumableAmount').length){
				activaTab('tab_e');
				$('#con_amount').focus();
			} else {
				$('#cashreceivetext').focus();
			}
		}, 500);
	}
	$('#cashreceivetext').keypress(function (e) {
		var key = e.which;
		if(key == 13)
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
		var bl_card_type = $('#billing_card_type').val();
		var bl_trace_number = $('#billing_trace_number').val();
		var bl_approval_code = $('#billing_approval_code').val();
		var bl_date = $('#billing_date').val();
		var others = "<p>Card Type: "+bl_card_type+"</p>";
		others += "<p>Trace Number: "+bl_trace_number+"</p>";
		others += "<p>Approval Code: "+bl_approval_code+"</p>";
		others += "<p>Date: "+bl_date+"</p>";

		if(!bl_cardnumber){
			bl_cardnumber ='N/A';
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
		$("#credit_table").append("<tr data-date='"+bl_date+"' data-card_type='"+bl_card_type+"' data-trace_number='"+bl_trace_number+"' data-approval_code='"+bl_approval_code+"'><td>"+bl_cardnumber+"</td><td>"+bl_amount+"</td><td>"+bl_bank+"</td><td>"+others+"</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
		$('#billing_cardnumber').val('');
		$('#billing_bankname').val('');
		$('#billing_amount').val('');
		$('#billing_card_type').val('');
		$('#billing_trace_number').val('');
		 $('#billing_approval_code').val('');
		 $('#billing_date').val('');
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
	$('#member_deduction_amount').keyup(function (e) {

		if(!($('#member_deduction').val())){
			showToast('error','<p>Please Choose member first</p>','<h3>WARNING!</h3>','toast-bottom-right');
			$(this).val('');
			return;
		}


		if(isNaN($(this).val())){
			showToast('error','<p>Please Enter Valid Amount</p>','<h3>WARNING!</h3>','toast-bottom-right');
			$(this).val('');
			$(this).focus();
		}

		updateMemberDeduction();
	});
	$('#con_amount').keyup(function (e) {

		if(!($('#con_member').val())){
			showToast('error','<p>Please Choose member first</p>','<h3>WARNING!</h3>','toast-bottom-right');
			return;
		}
		if (localStorage['hasType2'] == 1){

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
	function cashHolderComputation(cash,change){
		$('#cashreceiveholder').empty();
		$('#changeholder').empty();
		$('#cashreceiveholder').append(number_format(cash,2));
		$('#changeholder').append(number_format(change,2));
	}
	$('body').on('click','#checkout',function(){

		var total = $('#grandtotalholder').text();
		var member_id = $('#member_id').val();
		$("#con_member_freebies").select2('val',member_id);
		$("#member_credit").select2('val',member_id);
		$("#con_member").select2('val',member_id);
		$("#member_deduction").select2('val',member_id);
		$("#con_member_freebies").attr('disabled',true);
		$("#member_credit").attr('disabled',true);
		$("#con_member").attr('disabled',true);
		$("#member_deduction").attr('disabled',true);

		showpricemodal('0',total.toString());
	});


	function displayNextInvoice(){
		var inv = parseInt(localStorage['invoice']) +1;
		var invdis = "<span style='color:#000;'>Next " + invoice_label + ": </span> " + inv;
		$("#nextInvoicenumber").html(invdis);
	}
	function displayNextDr(){
		var inv = parseInt(localStorage['dr']) +1;
		var invdis = "<span style='color:#000;'>Next " + dr_label + ": </span> " + inv;
		$("#nextDrnumber").html(invdis);
	}
	function displayNextIr(){
		var inv = parseInt(localStorage['ir']) +1;
		var invdis = "<span style='color:#000;'>Next " + pr_label + ": </span> " + inv;
		$("#nextIrnumber").html(invdis);
	}

	$('body').on('click','#print',function(){

		if($('#cashreceiveholder').text() != '0'){
			var btncon = $(this);
			var btnoldval = btncon.html();
			btncon.html('Loading...');
			btncon.attr('disabled',true);
			var cartlength = $("#cart tbody tr").length;
			var invoicelimit = localStorage['invoice_limit'];
			var drlimit = localStorage['dr_limit'];
			var irlimit = localStorage['ir_limit'];
			var chkReceiptType = [];
			var pagedr = 1;
			var pageinvoice =1;
			var pageir = 1;

			$("input[name='checkType']:checked").each(function(){
				if($(this).val() == 1){
					pageinvoice = parseFloat(cartlength)/parseFloat(invoicelimit);
					pageinvoice = Math.ceil(pageinvoice);
				}
				if($(this).val() == 2){
					pagedr = parseFloat(cartlength)/parseFloat(drlimit);
					pagedr = Math.ceil(pagedr);
				}
				if($(this).val() == 3){
					pageir = parseFloat(cartlength)/parseFloat(irlimit);
					pageir = Math.ceil(pageir);
				}
				chkReceiptType.push($(this).val());
			});
			

			if(!invoicelimit) {
				alert('Please add limit items per ' + invoice_label + ' in manage terminal page first.');
				btncon.html(btnoldval);
				btncon.attr('disabled',false);
				return;
			}
			if(!drlimit) {
				alert('Please add limit items per ' + dr_label + ' in manage terminal page first.');
				btncon.html(btnoldval);
				btncon.attr('disabled',false);
				return;
			}
			if(!irlimit) {
				alert('Please add limit items per ' + pr_label + ' in manage terminal page first.');
				btncon.html(btnoldval);
				btncon.attr('disabled',false);
				return;
			}

			if(pagedr > 1){
				if(!confirm('This transaction will have ' + pagedr +' ' + dr_label + '\'s')){
					btncon.html(btnoldval);
					btncon.attr('disabled',false);
					return;
				}
			}
			if(pageinvoice > 1){
				if(!confirm('This transaction will have ' + pageinvoice + ' ' + invoice_label + 's')){
					btncon.html(btnoldval);
					btncon.attr('disabled',false);
					return;
				}
			}
			if(pageir > 1){
				if(!confirm('This transaction will have ' + pageir +' ' + pr_label + '\'s')){
					btncon.html(btnoldval);
					btncon.attr('disabled',false);
					return;
				}
			}
			if(cartlength) {
				printInvoiceOrDr(chkReceiptType, invoicelimit, drlimit, irlimit);
				if(localStorage['news_print'] && localStorage['news_print'] == 1){
					PrintElemNewsPrint(drlimit);
				}
			} else {
				showToast('error','<p>No items in cart</p>','<h3>WARNING!</h3>','toast-bottom-right');
				btncon.html(btnoldval);
				btncon.attr('disabled',false);
			}
			submitTransaction();
		} else {
			alertify.alert('Please receive payment yet.');
		}
	});
	$('body').on('click','#test_print',function(){
		var btncon = $(this);
		var btnoldval = btncon.html();
		btncon.html('Loading...');
		btncon.attr('disabled',true);
		var cartlength = $("#cart tbody tr").length;
		var invoicelimit = localStorage['invoice_limit'];
		var drlimit = localStorage['dr_limit'];
		var irlimit = localStorage['ir_limit'];
		var chkReceiptType = [];
		var pagedr = 1;
		var pageinvoice =1;
		var pageir = 1;

		$("input[name='checkType']:checked").each(function(){
			if($(this).val() == 1){
				pageinvoice = parseFloat(cartlength)/parseFloat(invoicelimit);
				pageinvoice = Math.ceil(pageinvoice);
			}
			if($(this).val() == 2){
				pagedr = parseFloat(cartlength)/parseFloat(drlimit);
				pagedr = Math.ceil(pagedr);
			}
			if($(this).val() == 3){
				pageir = parseFloat(cartlength)/parseFloat(irlimit);
				pageir = Math.ceil(pageir);
			}
			chkReceiptType.push($(this).val());
		});


		if(!invoicelimit) {
			alert('Please add limit items per ' + invoice_label + ' in manage terminal page first.');
			btncon.html(btnoldval);
			btncon.attr('disabled',false);
			return;
		}
		if(!drlimit) {
			alert('Please add limit items per ' + dr_label + ' in manage terminal page first.');
			btncon.html(btnoldval);
			btncon.attr('disabled',false);
			return;
		}
		if(!irlimit) {
			alert('Please add limit items per ' + pr_label + ' in manage terminal page first.');
			btncon.html(btnoldval);
			btncon.attr('disabled',false);
			return;
		}

		if(pagedr > 1){
			if(!confirm('This transaction will have ' + pagedr +' ' + dr_label + '\'s')){
				btncon.html(btnoldval);
				btncon.attr('disabled',false);
				return;
			}
		}
		if(pageinvoice > 1){
			if(!confirm('This transaction will have ' + pageinvoice + ' ' + invoice_label + 's')){
				btncon.html(btnoldval);
				btncon.attr('disabled',false);
				return;
			}
		}
		if(pageir > 1){
			if(!confirm('This transaction will have ' + pageir +' ' + pr_label + '\'s')){
				btncon.html(btnoldval);
				btncon.attr('disabled',false);
				return;
			}
		}
		if(cartlength) {
			printInvoiceOrDr(chkReceiptType, invoicelimit, drlimit, irlimit);
			if(localStorage['news_print'] && localStorage['news_print'] == 1){
				PrintElemNewsPrint(drlimit);
			}
		} else {
			showToast('error','<p>No items in cart</p>','<h3>WARNING!</h3>','toast-bottom-right');
			btncon.html(btnoldval);
			btncon.attr('disabled',false);
		}
		location.href='pos.php';
	});
	function submitTransaction(){

		var member_id = $('#member_id').val();
		var agent_id = $('#agent_id').val();
		var sales_type = $('#sales_type').val();
		var sales_remarks = $('#remarks').val();
		var sales_po_number = $('#sales_po_number').val();
		var custom_date_sold = $('#custom_date_sold').val();
		var chkReceiptType = [];
		var arr_points = getPointCredited();

		$("input[name='checkType']:checked").each(function(){
			chkReceiptType.push($(this).val());
		});
		if(!chkReceiptType.length){
			showToast('error','<p>Please select receipt type.</p>','<h3>WARNING!</h3>','toast-bottom-right');
		}
		var invoicelimit = localStorage['invoice_limit'];
		var drlimit = localStorage['dr_limit'];
		var irlimit = localStorage['ir_limit'];
		var cartlength = $("#cart tbody tr").length;
		var custom_invoice = $('#custom_invoice').val();
		var custom_dr = $('#custom_dr').val();
		var custom_ir = $('#custom_ir').val();

		if($("#cart > tbody > #noitem").children().length){
			showToast('error','<p>no items in cart</p>','<h3>WARNING!</h3>','toast-bottom-right');
		} else {
			var inv = 0;
			var dr =0;
			var ir = 0;
			for(var indir in chkReceiptType){
				if(chkReceiptType[indir] == 1){
					inv = parseInt(localStorage['invoice']) + 1;
					invoicelimit = parseInt(invoicelimit) + 1;
				}
				if(chkReceiptType[indir] == 2){
					dr = parseInt(localStorage['dr']) + 1;
					drlimit = parseInt(drlimit) + 1;
				}
				if(chkReceiptType[indir] == 3){
					ir = parseInt(localStorage['ir']) + 1;
					irlimit = parseInt(irlimit) + 1;
				}
			}
			var rowctr = 1;
			var items =[];
			var payment_credit = '';
			var payment_bt = '';
			var payment_cheque = '';
			var payment_cash = '';
			var payment_con ='';
			var payment_con_freebies ='';
			var payment_member_credit='';
			var payment_member_deduction='';


			if(localStorage['payment_cash']){
				payment_cash = localStorage['payment_cash'];
			}
			if(localStorage['payment_con']){
				payment_con = localStorage['payment_con'];
			}
			if(localStorage['payment_con_freebies']){
				payment_con_freebies = localStorage['payment_con_freebies'];
			}
			if(localStorage['payment_member_credit']){
				payment_member_credit = localStorage['payment_member_credit'];
			}
			if(localStorage['payment_member_deduction']){
				payment_member_deduction = localStorage['payment_member_deduction'];
			}
			if(localStorage['payment_credit']){
				payment_credit = localStorage['payment_credit'];
			}
			if(localStorage['payment_bt']){
				payment_bt = localStorage['payment_bt'];
			}
			if(localStorage['payment_cheque']){
				payment_cheque = localStorage['payment_cheque'];
			}
			/***** over payment *****/
			var op_payment_credit = '';
			var op_payment_bt = '';
			var op_payment_cheque = '';
			var op_payment_cash = '';
			if(localStorage['op_payment_credit']){
				op_payment_credit = localStorage['op_payment_credit'];
			}
			if(localStorage['op_payment_bt']){
				op_payment_bt = localStorage['op_payment_bt'];
			}
			if(localStorage['op_payment_cheque']){
				op_payment_cheque = localStorage['op_payment_cheque'];
			}
			if(localStorage['op_payment_cash']){
				op_payment_cash = localStorage['op_payment_cash'];
			}
			var arr_op_ids = [];
			$('input:checkbox.chk_overpayment').each(function () {
				var op_chk =$(this);
				if(op_chk.is(":checked")){
					arr_op_ids.push(op_chk.val());
				}
			});

			/**********end***********/
			$('#cart >tbody > tr').each(function(index){
				var row = $(this);
				var item_id = row.attr('data-item_id');
				var warranty = 0;

				if(chkReceiptType.indexOf('1') != -1){
					if(rowctr % invoicelimit == 0){
						inv = parseInt(inv) + 1;
					}
				}
				if(chkReceiptType.indexOf('2') != -1){
					if(rowctr % drlimit == 0){
						dr = parseInt(dr) + 1;
					}
				}
				if(chkReceiptType.indexOf('3') != -1){
					if(rowctr % irlimit == 0){
						ir = parseInt(ir) + 1;
					}
				}
				rowctr = parseInt(rowctr) + 1;


				var store_discount = 0;
				var price_adjustment = row.attr('data-price_adjustment');
				var member_adjustment = row.attr('data-member_adjustment');
				store_discount = (store_discount) ? store_discount : 0;
				price_adjustment = (price_adjustment) ? price_adjustment : 0;
				member_adjustment = (member_adjustment) ? member_adjustment : 0;

				price_adjustment =  parseFloat(price_adjustment) * parseFloat(row.children().eq(1).text());
				var cur_date = Date.now() /1000;
				inv = (custom_invoice) ?  custom_invoice : inv;
				dr = (custom_dr) ?  custom_dr : dr;
				ir = (custom_ir) ?  custom_ir : ir;


				items.push({
					item_id: item_id,
					qty: row.children().eq(1).text(),
					barcode: row.attr('data-barcode'),
					is_bundle: row.attr('data-is_bundle'),
					cdays: row.attr('data-cdays'),
					cqty: row.attr('data-cqty'),
					price:  row.children().eq(2).text(),
					price_id: row.children().eq(2).prop('id'),
					discount: 0,
					store_discount: store_discount,
					adjustment: price_adjustment,
					member_adjustment:member_adjustment,
					total: row.children().eq(4).text(),
					company_id:localStorage['company_id'],
					sold_date:cur_date,
					invoice: inv,
					dr:dr,
					ir:ir,
					mem_id:member_id,
					remarks:sales_remarks,
					warranty:warranty,
					agent_id:agent_id,
					sales_type:sales_type
				});
			});
			if(items.length > 0){
				var service_used_items = '';
				if( $('#service_used_items').length > 0){
					service_used_items = $('#service_used_items').val();
				}
				var pref_inv = (localStorage['pref_inv']) ? localStorage['pref_inv'] : '';
				var pref_dr =  (localStorage['pref_dr']) ? localStorage['pref_dr'] : '';
				var pref_ir =  (localStorage['pref_ir']) ? localStorage['pref_ir'] : '';

				$.ajax({
					url:'../ajax/ajax_query2.php',
					type:'POST',
					data: {
						functionName:'sendPOSSale',
						items: JSON.stringify(items),
						terminal_id: localStorage['terminal_id'],
						branch_id: localStorage['branch_id'],
						payment_credit:payment_credit,
						payment_bt:payment_bt,
						payment_cheque:payment_cheque,
						payment_cash:payment_cash,
						payment_con:payment_con,
						payment_con_freebies:payment_con_freebies,
						payment_member_credit:payment_member_credit,
						payment_member_deduction:payment_member_deduction,
						member_id:member_id,
						remarks:sales_remarks,
						sales_po_number:sales_po_number,
						service_used_items:service_used_items,
						arr_points:arr_points,
						pref_inv:pref_inv,
						pref_dr:pref_dr,
						pref_ir:pref_ir,
						custom_date_sold:custom_date_sold,
						op_payment_credit:op_payment_credit,
						op_payment_bt:op_payment_bt,
						op_payment_cheque:op_payment_cheque,
						op_payment_cash:op_payment_cash,
						arr_op_ids:JSON.stringify(arr_op_ids)

					},
					success: function(data){
						location.reload();
					},
					error:function(){
						console.log('Error Occur');
					}
				});
			}

		}
	}


	function printInvoiceOrDr(type,invoice_limit,dr_limit,ir_limit){
		for(var i in type){
			if(type[i] == 1){
				PrintElem(invoice_limit);
			}
			if(type[i] == 2){
				PrintElemDr(dr_limit);
				//print_con_dr();
			}
			if(type[i] == 3){
				PrintElemIr(ir_limit);
			}
		}
	}
	function PrintElem(invoice_limit)
	{
		if(localStorage['print_inv'] == 0){
			return true; // dont print invoice
		}

		var withoutformlayout = 1;
		var w_border = '', w_date = '<br/><br/>', w_member = '', w_member_address = '', w_table_head = '', w_header = '', wh_tablecss = '';
		var w_vat = '', w_vatable = '', w_total = '';
		if(withoutformlayout == 1) {
			w_border = "border:1px solid #ccc;";
			wh_tablecss = "margin: 0 auto;border-collapse:collapse;";
			w_date = "<span>Date: </span>";
			w_member = "<span>Client: </span>";
			w_member_address = "<span>Address: </span>";
			w_vat = "<span style='display:inline-block;width:70px;'>Vat: </span>";
			w_vatable = "<span style='display:inline-block;width:70px;'>Vatable: </span>";
			w_total = "<span style='display:inline-block;width:70px;'>Total: </span>";
			w_header = "<h1 style='font-weight:normal;text-align:center;margin-top:0px;'>" + $('#co_name').val();
			w_header += "<span style='font-weight:normal;display:block;font-size:14px;text-align:center;'>" + $('#co_desc').val() + "</span>";
			w_header += "<span style='font-weight:normal;display:block;font-size:14px;text-align:center;'>" + $('#co_address').val() + "</span>";
			w_header += "</h3>";
		}

		var mem = $("#member_id");
		var member_name = '';
		var styling = JSON.parse(localStorage['invoice_format']);
		var mem_name_split;
		if(mem.val()){
			member_name = $("#member_id").select2('data').text;
			mem_name_split = member_name.split(',');
			member_name = mem_name_split[0];

		}
		var memlisttest = '';
		if(localStorage['members']){
			memlisttest = JSON.parse(localStorage['members']);
		}

		var remarks = $('#remarks').val();

		var station_name ='';
		var station_address='';
		var station = $("#opt_station");
		var station_id ='';
		if(memlisttest){
			for(var i in memlisttest){
				var cur = memlisttest[i];
				if(cur.id == mem.val()){
					station_name = cur.personal_address + "<br>Contact Person: " + cur.firstname + " " + cur.lastname + "<br>Contact #: " + cur.contact_number;
				}
			}
		}


		var cur_date = Date.now() /1000;



		var d = new Date(cur_date * 1000);
		var month = d.getMonth()+1;
		var day = d.getDate();
		var output = (month<10 ? '0' : '') + month + '/' +
			(day<10 ? '0' : '') + day + '/' + d.getFullYear();
		var custom_date_sold = $('#custom_date_sold').val();
		if(custom_date_sold){
			output = custom_date_sold;
		}

		var printhtml="";
		var datevisible = (styling['date']['visible']) ? 'display:block;' : 'display:none;';
		var membernamevisible = (styling['membername']['visible']) ? 'display:block;' : 'display:none;';
		var memberaddressvisible = (styling['memberaddress']['visible']) ? 'display:block;' : 'display:none;';
		var stationnamevisible = (styling['stationname']['visible']) ? 'display:block;' : 'display:none;';
		var stationaddressvisible = (styling['stationaddress']['visible']) ? 'display:block;' : 'display:none;';
		var itemtablevisible = (styling['itemtable']['visible']) ? 'display:block;' : 'display:none;';
		var paymentsvisible = (styling['payments']['visible']) ? 'display:block;' : 'display:none;';
		var payments2visible = (styling['payments2']['visible']) ? 'display:block;' : 'display:none;';
		var payments3visible = (styling['payments3']['visible']) ? 'display:block;' : 'display:none;';
		var cashiervisible = (styling['cashier']['visible']) ? 'display:block;' : 'display:none;';
		var remarksvisible = (styling['remarks']['visible']) ? 'display:block;' : 'display:none;';
		var reservedvisible = (styling['reserved']['visible']) ? 'display:block;' : 'display:none;';
		var drnumvisible = (styling['drnum']['visible']) ? 'display:block;' : 'display:none;';
		var tdbarcodevisible = (styling['tdbarcode']['visible']) ? 'display:inline-block;' : 'display:none;';
		var tdqtyvisible = (styling['tdqty']['visible']) ? 'display:inline-block;' : 'display:none;';
		var tddescriptionvisible = (styling['tddescription']['visible']) ? 'display:inline-block;' : 'display:none;';
		var tdpricevisible = (styling['tdprice']['visible']) ? 'display:inline-block;' : 'display:none;';
		var tdtotalvisible = (styling['tdtotal']['visible']) ? 'display:inline-block;' : 'display:none;';

		var dateBold = (styling['date']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var membernameBold = (styling['membername']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var memberaddressBold = (styling['memberaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var stationnameBold = (styling['stationname']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var stationaddressBold = (styling['stationaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var itemtableBold = (styling['itemtable']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var paymentsBold = (styling['payments']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var payments2Bold = (styling['payments2']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var payments3Bold = (styling['payments3']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var cashierBold = (styling['cashier']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var remarksBold = (styling['remarks']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var reservedBold = (styling['reserved']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var drnumBold = (styling['drnum']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var tdbarcodeBold = (styling['tdbarcode']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var tdqtyBold = (styling['tdqty']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var tddescriptionBold = (styling['tddescription']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var tdpriceBold = (styling['tdprice']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var tdtotalBold = (styling['tdtotal']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';

		printhtml = printhtml + "<div id='maindivforprinting' style='page-break-before: always;position:relative;height:1056px;'>" + w_header;
		printhtml= printhtml +  "<div style='"+datevisible+dateBold+"position:absolute;top:"+styling['date']['top']+"px; left:"+styling['date']['left']+"px;font-size:"+styling['date']['fontSize']+"px;'> <br/><br/>"+ w_date+  output+ " </div><div style='clear:both;'></div>";
		printhtml= printhtml +  "<div style='"+membernamevisible+membernameBold+"position:absolute;top:"+styling['membername']['top']+"px; left:"+styling['membername']['left']+"px;font-size:"+styling['membername']['fontSize']+"px;'>"+ w_member +  member_name+"</div>";
		printhtml= printhtml +  "<div style='"+memberaddressvisible+memberaddressBold+"position:absolute;top:"+styling['memberaddress']['top']+"px; left:"+styling['memberaddress']['left']+"px;width:"+styling['memberaddress']['width']+"px;font-size:"+styling['memberaddress']['fontSize']+"px;'>"+ w_member_address + station_name+"</div>";
		printhtml= printhtml +  "<div style='"+stationnamevisible+stationnameBold+"position:absolute;top:"+styling['stationname']['top']+"px; left:"+styling['stationname']['left']+"px;font-size:"+styling['stationname']['fontSize']+"px;'>"+station_id+"</div>";
		printhtml= printhtml +  "<div style='"+stationaddressvisible+stationaddressBold+"position:absolute;top:"+styling['stationaddress']['top']+"px; left:"+styling['stationaddress']['left']+"px;width:"+styling['stationaddress']['width']+"px;font-size:"+styling['stationaddress']['fontSize']+"px;'>"+station_address+"</div>";
		printhtml= printhtml + "<table id='itemscon' style='"+wh_tablecss+ itemtablevisible+itemtableBold+"position:absolute;top:"+styling['itemtable']['top']+"px;left:"+styling['itemtable']['left']+"px;font-size:"+styling['itemtable']['fontSize']+"px;'> &nbps;";
		if(withoutformlayout == 1) {
			w_table_head = "<tr ><th style='" + w_border + tdqtyvisible + tdqtyBold + "position:relative;width:" + styling['tdqty']['width'] + "px;padding-left:" + styling['tdqty']['left'] + "px;'>Qty</th><th style='" + w_border + tdbarcodevisible + tdbarcodeBold + "position:relative;width:" + styling['tdbarcode']['width'] + "px;padding-left:" + styling['tdbarcode']['left'] + "px;'>Item</th><th style='" + w_border + tddescriptionvisible + tddescriptionBold + "position:relative;width:" + styling['tddescription']['width'] + "px;padding-left:" + styling['tddescription']['left'] + "px;'> Description </th><th style='" + w_border + tdpricevisible + tdpriceBold + "position:relative;width:" + styling['tdprice']['width'] + "px;padding-left:" + styling['tdprice']['left'] + "px;'>Price</th><th style='" + w_border + tdtotalvisible + tdtotalBold + "position:relative;width:" + styling['tdtotal']['width'] + "px;padding-left:" + styling['tdtotal']['left'] + "px;'>Total</th></tr>";
		}
		printhtml = printhtml + w_table_head;
		var countallitem = 	$('#cart > tbody > tr').length;
		var invoicelimit = localStorage['invoice_limit'];
		var drlimit = localStorage['dr_limit'];
		var lamankadainvoice =[];
		var pagectr = 1;
		var rowctr = 1;
		var pagesubtotal = 0;
		var pagetax=0;
		var pagegrandtotal = 0;
		var vat = 1.12;
		invoicelimit = parseInt(invoicelimit) + 1;
		var reservedbyname = "";
		$('#cart > tbody > tr').each(function(index){
			var row = $(this);
			var itemcode = row.attr("data-itemcode");
			var description = row.attr("data-desc");
			var b = row.attr('data-barcode');
			var unit_name = row.attr('data-unit_name');
			unit_name = (unit_name) ? unit_name : '';
			if(withoutformlayout == 1) {
				qty = row.children().eq(1).text()+ "<span>"+unit_name+"</span>";
			} else {
				qty = row.children().eq(1).text()+ "<td style='width:45px;'>"+unit_name+"</td>";
			}



			var price = row.children().eq(2).text();
			var discount = row.children().eq(3).text();
			var total = replaceAll(row.children().eq(4).text(),',','');

			var origtotal = parseFloat(qty) * parseFloat(price);
			var adjustment = row.children().eq(3).text();

			var additionalDiscount = 0;

			if(parseFloat(adjustment) != 0 ){

				var labeldisc = "";
				var labeldisc2 = "<br/>("+number_format(adjustment,2)+")";
			} else {
				var labeldisc ='';
				var labeldisc2 ='';
			}
			if(rowctr % invoicelimit == 0){
				var subtotal = (pagesubtotal / vat);
				var vatable = parseFloat(pagesubtotal) - parseFloat(subtotal);
				subtotal = subtotal.toFixed(2);
				vatable = vatable.toFixed(2);
				pagesubtotal = pagesubtotal.toFixed(2);
				lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "</table>";
				lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "<div style='"+paymentsvisible+paymentsBold+"position:absolute; list-style-type: none; left:"+styling['payments']['left']+"px;top:"+styling['payments']['top']+"px;font-size:"+styling['payments']['fontSize']+"px;'>"+w_vatable + subtotal+"</div>";
				lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "<div style='"+payments2visible+payments2Bold+"position:absolute; list-style-type: none; left:"+styling['payments2']['left']+"px;top:"+styling['payments2']['top']+"px;font-size:"+styling['payments2']['fontSize']+"px;'>"+w_vat+ vatable+"</div>";
				lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "<div style='"+payments3visible+payments3Bold+"position:absolute; list-style-type: none; left:"+styling['payments3']['left']+"px;top:"+styling['payments3']['top']+"px;font-size:"+styling['payments3']['fontSize']+"px;'>"+w_total+pagesubtotal+"</div>";
				pagectr = parseInt(pagectr) + 1;
				pagesubtotal=0;
			}
			pagesubtotal = parseFloat(pagesubtotal) + parseFloat(total);
			if(!lamankadainvoice[pagectr]) lamankadainvoice[pagectr] = '';
			lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "<tr ><td style='"+ w_border + tdbarcodevisible+tdbarcodeBold+"position:relative;width:"+styling['tdbarcode']['width']+"px;padding-left:"+styling['tdbarcode']['left']+"px;'>"+itemcode+"</td><td style='"+ w_border + tdqtyvisible+tdqtyBold+"position:relative;width:"+styling['tdqty']['width']+"px;padding-left:"+styling['tdqty']['left']+"px;'>"+qty+"</td><td style='"+ w_border + tddescriptionvisible+tddescriptionBold+"position:relative;width:"+styling['tddescription']['width']+"px;padding-left:"+styling['tddescription']['left']+"px;'> "+ description +" <span style='padding-left:20px;'>"+labeldisc+"</span> </td><td style='"+ w_border + tdpricevisible+tdpriceBold+"position:relative;width:"+styling['tdprice']['width']+"px;padding-left:"+styling['tdprice']['left']+"px;'>"+number_format(price,2)+"</td><td style='"+ w_border + tdtotalvisible+tdtotalBold+"position:relative;width:"+styling['tdtotal']['width']+"px;padding-left:"+styling['tdtotal']['left']+"px;'>"+number_format(origtotal,2)+" "+labeldisc2+"</td></tr>";
			rowctr = parseInt(rowctr) +1;
		});

		if(pagesubtotal > 0){
			var consumable_payment = $('#hidconsumablepayment').val();
			if(consumable_payment > 0){
				pagesubtotal = pagesubtotal - consumable_payment;
			}
			var subtotal = (pagesubtotal / vat);
			var vatable = parseFloat(pagesubtotal) - parseFloat(subtotal);
			subtotal = subtotal.toFixed(2);
			vatable = vatable.toFixed(2);
			pagesubtotal = pagesubtotal.toFixed(2);

			if(withoutformlayout == 1) {
				for(var padrow = rowctr; padrow <= invoicelimit; padrow++) {
					lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "<tr ><td style='" + w_border + tdqtyvisible + tdqtyBold + "position:relative;width:" + styling['tdqty']['width'] + "px;padding-left:" + styling['tdqty']['left'] + "px;'>&nbsp;</td><td style='" + w_border + tdbarcodevisible + tdbarcodeBold + "position:relative;width:" + styling['tdbarcode']['width'] + "px;padding-left:" + styling['tdbarcode']['left'] + "px;'>&nbsp;</td><td style='" + w_border + tddescriptionvisible + tddescriptionBold + "position:relative;width:" + styling['tddescription']['width'] + "px;padding-left:" + styling['tddescription']['left'] + "px;'>&nbsp;<span style='padding-left:20px;'></span> </td><td style='" + w_border + tdpricevisible + tdpriceBold + "position:relative;width:" + styling['tdprice']['width'] + "px;padding-left:" + styling['tdprice']['left'] + "px;'>&nbsp;</td><td style='" + w_border + tdtotalvisible + tdtotalBold + "position:relative;width:" + styling['tdtotal']['width'] + "px;padding-left:" + styling['tdtotal']['left'] + "px;'>&nbsp;</td></tr>";
				}
			}

			if(!lamankadainvoice[pagectr]) lamankadainvoice[pagectr] = '';
			lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "</table>";
			if(consumable_payment > 0) lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "<div style='"+paymentsvisible+paymentsBold+"position:absolute; list-style-type: none; left:"+styling['payments']['left']+"px;top:"+((styling['payments']['top']) - 12) +"px;font-size:"+styling['payments']['fontSize']+"px;'>("+consumable_payment+")</div>";

			lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "<div style='"+paymentsvisible+paymentsBold+"position:absolute; list-style-type: none; left:"+styling['payments']['left']+"px;top:"+styling['payments']['top']+"px;font-size:"+styling['payments']['fontSize']+"px;'>"+w_vatable+subtotal+"</div>";
			lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "<div style='"+payments2visible+payments2Bold+"position:absolute; list-style-type: none; left:"+styling['payments2']['left']+"px;top:"+styling['payments2']['top']+"px;font-size:"+styling['payments2']['fontSize']+"px;'>"+w_vat+vatable+"</div>";
			lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "<div style='"+payments3visible+payments3Bold+"position:absolute; list-style-type: none; left:"+styling['payments3']['left']+"px;top:"+styling['payments3']['top']+"px;font-size:"+styling['payments3']['fontSize']+"px;'>"+w_total+pagesubtotal+"</div>";

		}
		var printhtmlend = "";
		if(!reservedbyname) reservedbyname = "";
		var agent_con = $('#agent_id');
		var agent_name ="";
		if(agent_con.val()){
			agent_name = agent_con.select2('data').text;
		}
		reservedbyname = agent_name;
		var cinvoice = $('#custom_invoice').val();
		var ninvoice = parseInt(localStorage['invoice']) + 1;
		var custom_invoice = (cinvoice) ? cinvoice : ninvoice;
		var drnumctr =  custom_invoice;

		if(!remarks) remarks = "";
		var pref_inv = (localStorage['pref_inv']) ? localStorage['pref_inv'] : '';
		drnumctr = str_pad('000000',drnumctr,true);
		drnumctr = pref_inv + drnumctr;
		printhtmlend = printhtmlend + "<div style='"+cashiervisible+cashierBold+"position:absolute;left:"+styling['cashier']['left']+"px;top:"+styling['cashier']['top']+"px;font-size:"+styling['cashier']['fontSize']+"px;'>"+localStorage['current_lastname'] + ", "  + localStorage['current_firstname'] +"</div>";
		printhtmlend = printhtmlend + "<div style='"+remarksvisible+remarksBold+"position:absolute;left:"+styling['remarks']['left']+"px;top:"+styling['remarks']['top']+"px;font-size:"+styling['remarks']['fontSize']+"px;'>"+remarks+"</div>";
		printhtmlend = printhtmlend + "<div style='"+reservedvisible+reservedBold+"position:absolute;left:"+styling['reserved']['left']+"px;top:"+styling['reserved']['top']+"px;font-size:"+styling['reserved']['fontSize']+"px;'>"+reservedbyname+"</div>";
		printhtmlend = printhtmlend + "<div style='"+drnumvisible+drnumBold+"position:absolute;left:"+styling['drnum']['left']+"px;top:"+styling['drnum']['top']+"px;font-size:"+styling['drnum']['fontSize']+"px;'>"+drnumctr+"</div>";


		var termstxt ='';
		var ponumtxt = '';
		var tintxt ='';
		// add here
		var termsvisible = (styling['terms']['visible']) ? 'display:inline-block;' : 'display:none;';
		var ponumvisible = (styling['ponum']['visible']) ? 'display:inline-block;' : 'display:none;';
		var tinvisible = (styling['tin']['visible']) ? 'display:inline-block;' : 'display:none;';
		var termsbold = (styling['terms']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var ponumbold = (styling['ponum']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var tinbold = (styling['tin']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';

		printhtmlend = printhtmlend + "<div style='"+termsvisible+termsbold+"position:absolute;left:"+styling['terms']['left']+"px;top:"+styling['terms']['top']+"px;font-size:"+styling['terms']['fontSize']+"px;'>"+termstxt+"</div>";
		printhtmlend = printhtmlend + "<div style='"+ponumvisible+ponumbold+"position:absolute;left:"+styling['ponum']['left']+"px;top:"+styling['ponum']['top']+"px;font-size:"+styling['ponum']['fontSize']+"px;'>"+ponumtxt+"</div>";
		printhtmlend = printhtmlend + "<div style='"+tinvisible+tinbold+"position:absolute;left:"+styling['tin']['left']+"px;top:"+styling['tin']['top']+"px;font-size:"+styling['tin']['fontSize']+"px;'>"+tintxt+"</div>";




		printhtmlend = printhtmlend + "</div>";

		var finalprint = "";
		for(var i in lamankadainvoice ){
			finalprint = finalprint + printhtml + lamankadainvoice[i] + printhtmlend;
		}
		finalprint = replaceAll(finalprint,'undefined','');
		Popup(finalprint);
	}
	function popUpPrintWithStyle(data){
		var mywindow = window.open('', 'new div', '');
		mywindow.document.write('<html><head><title></title><style></style>');
		mywindow.document.write('<link rel="stylesheet" href="../css/bootstrap.css" type="text/css" />');
		mywindow.document.write('</head><body style="padding:0;margin:0;">');
		mywindow.document.write(data);
		mywindow.document.write('</body></html>');
		setTimeout(function(){
			mywindow.print();
			mywindow.close();
		},300);
		return true;
	}
	function PrintElemDr(dr_limit)
	{
		if(localStorage['print_dr'] == 0){
			return true; // dont print invoice
		}
		var DR_LABEL = $('#DR_LABEL').val();
		var memdata = '';
		try{
			memdata = JSON.parse($('#mem_data').val());
			if(memdata.is_blacklisted == 1){
				$('.notcashlist').hide();
			} else {
				$('.notcashlist').show();
			}
		} catch(e){

		}
		//var fontFamily = "font-family: 'Arial Black', Gadget, sans-serif;letter-spacing:2px;";
		var fontFamily = "font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif;";

		var mem = $("#member_id");
		var member_name = '';
		var styling = JSON.parse(localStorage['dr_format']);
		var mem_name_split;
		var salestype = '';
		if(mem.val()){
			member_name = $("#member_id").select2('data').text;
			mem_name_split = member_name.split(',');
			member_name = mem_name_split[0];
			salestype = mem_name_split[1];
		}
		var memlisttest = '';
		if(localStorage['members']){
			memlisttest = JSON.parse(localStorage['members']);
		}


		var remarks = $('#remarks').val();
		var station_name ='';
		var station_address='';
		var station = $("#opt_station");
		var station_id ='';


		/* form border */

		var withoutformlayout = 1;
		var w_border = '', w_date = '<br/><br/>', w_member = '', w_member_address = '', w_table_head = '', w_header = '', wh_tablecss = '';
		var w_vat = '', w_vatable = '', w_total = '';
		if(withoutformlayout == 1) {
			w_border = "border:1px solid #ccc;";
			wh_tablecss = "margin: 0 auto;border-collapse:collapse;";
			w_date = "<span>Date: </span>";
			w_member = "<span>Client: </span>";
			w_member_address = "<span>Address: </span>";
			w_vat = "<span style='display:inline-block;width:70px;'>Vat: </span>";
			w_vatable = "<span style='display:inline-block;width:70px;'>Vatable: </span>";
			w_total = "<span style='display:inline-block;width:70px;'>Total: </span>";
			w_header = "<h1 style='font-weight:normal;text-align:center;margin-top:0px;'>" + $('#co_name').val();
			w_header += "<span style='font-weight:normal;display:block;font-size:14px;text-align:center;'>" + $('#co_desc').val() + "</span>";
			w_header += "<span style='font-weight:normal;display:block;font-size:14px;text-align:center;'>" + $('#co_address').val() + "</span>";
			w_header += "</h3>";
		}

		if(memlisttest){
			for(var i in memlisttest){
				var cur = memlisttest[i];
				if(cur.id == mem.val()){
					station_name = cur.personal_address + "<br>Contact Person: " + cur.firstname + " " + cur.lastname + "<br>Contact #: " + cur.contact_number;
				}
			}
		}
		if(mem.val()){
			station_name = $("#member_id").select2('data').address;
		}
		if(station.val()){

			station_address = $("#"+station.attr('id')+ " :selected").attr('data-address');
			station_id = $("#"+station.attr('id')+ " :selected").text()
		}
		var cur_date = Date.now() /1000;

		var timedifference = parseInt(localStorage['servertime']) - parseInt(localStorage['localtime']);
		cur_date = parseInt(cur_date) + parseInt(timedifference);

		var d = new Date(cur_date * 1000);
		var month = d.getMonth()+1;
		var day = d.getDate();
		var output = (month<10 ? '0' : '') + month + '/' +
			(day<10 ? '0' : '') + day + '/' + d.getFullYear();
		var custom_date_sold = $('#custom_date_sold').val();
		if(custom_date_sold){
			output = custom_date_sold;
		}
		var datevisible = (styling['date']['visible']) ? 'display:block;' : 'display:none;';
		var membernamevisible = (styling['membername']['visible']) ? 'display:block;' : 'display:none;';
		var memberaddressvisible = (styling['memberaddress']['visible']) ? 'display:block;' : 'display:none;';
		var stationnamevisible = (styling['stationname']['visible']) ? 'display:block;' : 'display:none;';
		var stationaddressvisible = (styling['stationaddress']['visible']) ? 'display:block;' : 'display:none;';
		var itemtablevisible = (styling['itemtable']['visible']) ? 'display:block;' : 'display:none;';
		var paymentsvisible = (styling['payments']['visible']) ? 'display:block;' : 'display:none;';
		var payments2visible = (styling['payments2']['visible']) ? 'display:block;' : 'display:none;';
		var payments3visible = (styling['payments3']['visible']) ? 'display:block;' : 'display:none;';
		var cashiervisible = (styling['cashier']['visible']) ? 'display:block;' : 'display:none;';
		var remarksvisible = (styling['remarks']['visible']) ? 'display:block;' : 'display:none;';
		var reservedvisible = (styling['reserved']['visible']) ? 'display:block;' : 'display:none;';
		var drnumvisible = (styling['drnum']['visible']) ? 'display:block;' : 'display:none;';
		var tdbarcodevisible = (styling['tdbarcode']['visible']) ? 'display:inline-block;' : 'display:none;';
		var tdqtyvisible = (styling['tdqty']['visible']) ? 'display:inline-block;' : 'display:none;';
		var tddescriptionvisible = (styling['tddescription']['visible']) ? 'display:inline-block;' : 'display:none;';
		var tdpricevisible = (styling['tdprice']['visible']) ? 'display:inline-block;' : 'display:none;';
		var tdtotalvisible = (styling['tdtotal']['visible']) ? 'display:inline-block;' : 'display:none;';

		var dateBold = (styling['date']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var membernameBold = (styling['membername']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var memberaddressBold = (styling['memberaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var stationnameBold = (styling['stationname']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var stationaddressBold = (styling['stationaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var itemtableBold = (styling['itemtable']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var paymentsBold = (styling['payments']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var payments2Bold = (styling['payments2']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var payments3Bold = (styling['payments3']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var cashierBold = (styling['cashier']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var remarksBold = (styling['remarks']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var reservedBold = (styling['reserved']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var drnumBold = (styling['drnum']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var tdbarcodeBold = (styling['tdbarcode']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var tdqtyBold = (styling['tdqty']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var tddescriptionBold = (styling['tddescription']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var tdpriceBold = (styling['tdprice']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var tdtotalBold = (styling['tdtotal']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var howMany = $('#print_copy').val();
		howMany = (howMany) ? parseInt(howMany) : 1;
		var combinePage = "";
		for(var countPage = 1; countPage <= howMany; countPage++){ // all page
			if(countPage == 1){ // hide price and total
				tdpricevisible = 'display:none;';
				tdtotalvisible = 'display:none;';
			} else {
				tdpricevisible = (styling['tdprice']['visible']) ? 'display:inline-block;' : 'display:none;';
				tdtotalvisible = (styling['tdtotal']['visible']) ? 'display:inline-block;' : 'display:none;';
			}

			var printhtml="";
			printhtml = printhtml + "<div id='maindivforprinting' style='page-break-before: always;position:relative;"+fontFamily+"'>&nbsp;"+ w_header;
			printhtml= printhtml +  "<div style='"+datevisible+dateBold+"position:absolute;top:"+styling['date']['top']+"px; left:"+styling['date']['left']+"px;font-size:"+styling['date']['fontSize']+"px;'><br/><br/>"+w_date+  output+ " </div><div style='clear:both;'></div>";
			printhtml= printhtml +  "<div style='"+membernamevisible+membernameBold+"position:absolute;top:"+styling['membername']['top']+"px; left:"+styling['membername']['left']+"px;font-size:"+styling['membername']['fontSize']+"px;'>"+w_member+ member_name+"</div>";
			printhtml= printhtml +  "<div style='"+memberaddressvisible+memberaddressBold+"position:absolute;top:"+styling['memberaddress']['top']+"px; left:"+styling['memberaddress']['left']+"px;width:"+styling['memberaddress']['width']+"px;font-size:"+styling['memberaddress']['fontSize']+"px;'>"+w_member_address+ station_name+"</div>";
			printhtml= printhtml +  "<div style='"+stationnamevisible+stationnameBold+"position:absolute;top:"+styling['stationname']['top']+"px; left:"+styling['stationname']['left']+"px;font-size:"+styling['stationname']['fontSize']+"px;'>"+station_id+"</div>";
			printhtml= printhtml +  "<div style='"+stationaddressvisible+stationaddressBold+"position:absolute;top:"+styling['stationaddress']['top']+"px; left:"+styling['stationaddress']['left']+"px;width:"+styling['stationaddress']['width']+"px;font-size:"+styling['stationaddress']['fontSize']+"px;'>"+ station_address+"</div>";
			printhtml= printhtml + "<table id='itemscon' style='"+wh_tablecss+"position:absolute;top:"+styling['itemtable']['top']+"px;left:"+styling['itemtable']['left']+"px;font-size:"+styling['itemtable']['fontSize']+"px;'> ";

			if(withoutformlayout == 1) {
				w_table_head = "<tr ><th style='" + w_border + tdbarcodevisible + tdbarcodeBold + "position:relative;width:" + styling['tdbarcode']['width'] + "px;padding-left:" + styling['tdbarcode']['left'] + "px;'>Item</th><th style='" + w_border + tdqtyvisible + tdqtyBold + "position:relative;width:" + styling['tdqty']['width'] + "px;padding-left:" + styling['tdqty']['left'] + "px;'>Qty</th><th style='" + w_border + tddescriptionvisible + tddescriptionBold + "position:relative;width:" + styling['tddescription']['width'] + "px;padding-left:" + styling['tddescription']['left'] + "px;'> Description </th><th style='" + w_border + tdpricevisible + tdpriceBold + "position:relative;width:" + styling['tdprice']['width'] + "px;padding-left:" + styling['tdprice']['left'] + "px;'>Price</th><th style='" + w_border + tdtotalvisible + tdtotalBold + "position:relative;width:" + styling['tdtotal']['width'] + "px;padding-left:" + styling['tdtotal']['left'] + "px;'>Total</th></tr>";
			}
			printhtml = printhtml + w_table_head;

			var countallitem = 	$('#cart > tbody > tr').length;
			var drlimit = localStorage['dr_limit'];
			var lamankadadr =[];
			var pagectr = 1;
			var rowctr = 1;
			var pagesubtotal = 0;
			var pagetax=0;
			var pagegrandtotal = 0;
			var vat = 1.12;
			drlimit = parseInt(drlimit) + 1;
			var reservedbyname = "";
			$('#cart > tbody > tr').each(function(index){
				var row = $(this);
				var itemcode = row.attr("data-itemcode");
				var description = row.attr("data-desc");
				var b = row.attr('data-barcode');
				var unit_name = row.attr('data-unit_name');
				unit_name = (unit_name) ? unit_name : '';
				var qty = 0;
				if(withoutformlayout == 1) {
					qty = row.children().eq(1).text()+ "<span>"+unit_name+"</span>";
				} else {
					qty = row.children().eq(1).text()+ "<td style='width:45px;'>"+unit_name+"</td>";
				}



				var price = row.children().eq(2).text();
				var discount = row.children().eq(3).find('input').val();
				var total = replaceAll(row.children().eq(4).text(),',','');
				var origtotal = parseFloat(qty) * parseFloat(price);
				var adjustment = row.children().eq(3).text();

				var additionalDiscount = parseFloat(row.attr("data-store_discount"));
				discount = parseFloat(discount) + additionalDiscount;
				reservedbyname = row.attr('data-reserved_by');
				if(parseFloat(adjustment) != 0){
					var perunitdisc = parseFloat(discount) / parseFloat(qty);
					var labeldisc = "";
					var labeldisc2 = "<br/>("+number_format(adjustment,2)+")";
				} else {
					var labeldisc ='';
					var labeldisc2 ='';
				}
				if(rowctr % drlimit == 0){
					var subtotal = (pagesubtotal / vat);
					var vatable = parseFloat(pagesubtotal) - parseFloat(subtotal);
					subtotal = subtotal.toFixed(2);
					vatable = vatable.toFixed(2);
					pagesubtotal = pagesubtotal.toFixed(2);
					if(!lamankadadr[pagectr]) lamankadadr[pagectr] = '';
					lamankadadr[pagectr] = lamankadadr[pagectr] + "</table>";
					if(memdata && memdata.tax_type == "") { // dont print tax compu
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + paymentsvisible + paymentsBold + "position:absolute; list-style-type: none; left:" + styling['payments']['left'] + "px;top:" + styling['payments']['top'] + "px;font-size:" + styling['payments']['fontSize'] + "px;'>" +w_vatable+ subtotal + "</div>";
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + payments2visible + payments2Bold + "position:absolute; list-style-type: none; left:" + styling['payments2']['left'] + "px;top:" + styling['payments2']['top'] + "px;font-size:" + styling['payments2']['fontSize'] + "px;'>" + w_vat+ vatable + "</div>";
					}
					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments3visible+payments3Bold+"position:absolute; list-style-type: none; left:"+styling['payments3']['left']+"px;top:"+styling['payments3']['top']+"px;font-size:"+styling['payments3']['fontSize']+"px;'>"+ w_total+ pagesubtotal+"</div>";
					pagectr = parseInt(pagectr) + 1;
					pagesubtotal=0;
				}
				pagesubtotal = parseFloat(pagesubtotal) + parseFloat(total);
				lamankadadr[pagectr] = lamankadadr[pagectr] + "<tr ><td style='"+w_border+tdbarcodevisible+tdbarcodeBold+"position:relative;width:"+styling['tdbarcode']['width']+"px;padding-left:"+styling['tdbarcode']['left']+"px;'>"+itemcode+"</td><td style='"+w_border+tdqtyvisible+tdqtyBold+"position:relative;width:"+styling['tdqty']['width']+"px;padding-left:"+styling['tdqty']['left']+"px;'>"+qty+"</td><td style='"+w_border+tddescriptionvisible+tddescriptionBold+"position:relative;width:"+styling['tddescription']['width']+"px;padding-left:"+styling['tddescription']['left']+"px;'> "+ description +" <span style='padding-left:20px;'>"+labeldisc+"</span> </td><td style='"+w_border+tdpricevisible+tdpriceBold+"position:relative;width:"+styling['tdprice']['width']+"px;padding-left:"+styling['tdprice']['left']+"px;'>"+number_format(price,2)+"</td><td style='"+w_border+tdtotalvisible+tdtotalBold+"position:relative;width:"+styling['tdtotal']['width']+"px;padding-left:"+styling['tdtotal']['left']+"px;'>"+number_format(origtotal,2)+" "+labeldisc2+"</td></tr>";
				rowctr = parseInt(rowctr) +1;
			});
			if(pagesubtotal > 0){
				var consumable_payment = $('#hidconsumablepayment').val();
				if(consumable_payment > 0){
					pagesubtotal = pagesubtotal - consumable_payment;
				}
				var subtotal = (pagesubtotal / vat);
				var vatable = parseFloat(pagesubtotal) - parseFloat(subtotal);
				subtotal = subtotal.toFixed(2);
				vatable = vatable.toFixed(2);
				pagesubtotal = pagesubtotal.toFixed(2);
				if(!lamankadadr[pagectr]) lamankadadr[pagectr] = '';
				if(withoutformlayout == 1) {
					for(var padrow = rowctr; padrow <= drlimit; padrow++) {
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<tr ><td style='" + w_border + tdbarcodevisible + tdbarcodeBold + "position:relative;width:" + styling['tdbarcode']['width'] + "px;padding-left:" + styling['tdbarcode']['left'] + "px;'>&nbsp;</td><td style='" + w_border + tdqtyvisible + tdqtyBold + "position:relative;width:" + styling['tdqty']['width'] + "px;padding-left:" + styling['tdqty']['left'] + "px;'>&nbsp;</td><td style='" + w_border + tddescriptionvisible + tddescriptionBold + "position:relative;width:" + styling['tddescription']['width'] + "px;padding-left:" + styling['tddescription']['left'] + "px;'>&nbsp;<span style='padding-left:20px;'></span> </td><td style='" + w_border + tdpricevisible + tdpriceBold + "position:relative;width:" + styling['tdprice']['width'] + "px;padding-left:" + styling['tdprice']['left'] + "px;'>&nbsp;</td><td style='" + w_border + tdtotalvisible + tdtotalBold + "position:relative;width:" + styling['tdtotal']['width'] + "px;padding-left:" + styling['tdtotal']['left'] + "px;'>&nbsp;</td></tr>";
					}
				}
				lamankadadr[pagectr] = lamankadadr[pagectr] + "</table>";
				if(consumable_payment > 0) lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+paymentsvisible+paymentsBold+"position:absolute; list-style-type: none; left:"+styling['payments']['left']+"px;top:"+((styling['payments']['top']) - 12) +"px;font-size:"+styling['payments']['fontSize']+"px;'>("+consumable_payment+")</div>";

				if(memdata && memdata.tax_type == ""){ // dont print tax compu
					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+paymentsvisible+paymentsBold+"position:absolute; list-style-type: none; left:"+styling['payments']['left']+"px;top:"+styling['payments']['top']+"px;font-size:"+styling['payments']['fontSize']+"px;'>"+w_vatable+ subtotal+"</div>";
					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments2visible+payments2Bold+"position:absolute; list-style-type: none; left:"+styling['payments2']['left']+"px;top:"+styling['payments2']['top']+"px;font-size:"+styling['payments2']['fontSize']+"px;'>"+w_vat +  vatable+"</div>";
				}
				lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments3visible+payments3Bold+"position:absolute; list-style-type: none; left:"+styling['payments3']['left']+"px;top:"+styling['payments3']['top']+"px;font-size:"+styling['payments3']['fontSize']+"px;'>"+ w_total+ pagesubtotal+"</div>";
			}
			var printhtmlend = "";
			if(!reservedbyname) reservedbyname = "";
			var agent_con = $('#agent_id');
			var agent_name ="";
			if(agent_con.val()){
				agent_name = agent_con.select2('data').text;
			}
			reservedbyname = agent_name;
			reservedbyname = salestype + " " + reservedbyname;
			var cdr = $('#custom_dr').val();
			var ndr = parseInt(localStorage['dr']) + 1;
			var custom_dr = (cdr) ? cdr : ndr;
			var drnumctr =  custom_dr;
			var pref_dr = (localStorage['pref_dr']) ? localStorage['pref_dr'] : '';
			drnumctr = str_pad('000000',drnumctr,true);
			drnumctr = pref_dr + drnumctr;
			drnumctr = DR_LABEL + " " + drnumctr;
			if(!remarks) remarks = "";
			printhtmlend = printhtmlend + "<div style='"+cashiervisible+cashierBold+"position:absolute;left:"+styling['cashier']['left']+"px;top:"+styling['cashier']['top']+"px;font-size:"+styling['cashier']['fontSize']+"px;'>"+localStorage['current_lastname'] + ", "  + localStorage['current_firstname'] +"</div>";
			printhtmlend = printhtmlend + "<div style='"+remarksvisible+remarksBold+"position:absolute;left:"+styling['remarks']['left']+"px;top:"+styling['remarks']['top']+"px;font-size:"+styling['remarks']['fontSize']+"px;'>"+remarks+"</div>";
			printhtmlend = printhtmlend + "<div style='"+reservedvisible+reservedBold+"position:absolute;left:"+styling['reserved']['left']+"px;top:"+styling['reserved']['top']+"px;font-size:"+styling['reserved']['fontSize']+"px;'>"+reservedbyname+"</div>";
			printhtmlend = printhtmlend + "<div style='"+drnumvisible+drnumBold+"position:absolute;left:"+styling['drnum']['left']+"px;top:"+styling['drnum']['top']+"px;font-size:"+styling['drnum']['fontSize']+"px;'>"+drnumctr+"</div>";


			var termstxt ='';
			var ponumtxt =$('#sales_po_number').val();
			var tintxt ='';
			if(memdata && memdata.terms){
				termstxt =  memdata.terms;
			}
			if(memdata && memdata.tin_no){
				tintxt =  memdata.tin_no;
			}


			var termsvisible = (styling['terms']['visible']) ? 'display:inline-block;' : 'display:none;';
			var ponumvisible = (styling['ponum']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tinvisible = (styling['tin']['visible']) ? 'display:inline-block;' : 'display:none;';
			var termsbold = (styling['terms']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var ponumbold = (styling['ponum']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tinbold = (styling['tin']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';

			printhtmlend = printhtmlend + "<div style='"+termsvisible+termsbold+"position:absolute;left:"+styling['terms']['left']+"px;top:"+styling['terms']['top']+"px;font-size:"+styling['terms']['fontSize']+"px;'>"+termstxt+"</div>";
			printhtmlend = printhtmlend + "<div style='"+ponumvisible+ponumbold+"position:absolute;left:"+styling['ponum']['left']+"px;top:"+styling['ponum']['top']+"px;font-size:"+styling['ponum']['fontSize']+"px;'>"+ponumtxt+"</div>";
			printhtmlend = printhtmlend + "<div style='"+tinvisible+tinbold+"position:absolute;left:"+styling['tin']['left']+"px;top:"+styling['tin']['top']+"px;font-size:"+styling['tin']['fontSize']+"px;'>"+tintxt+"</div>";


			printhtmlend = printhtmlend + "</div>";
			var finalprint = "";
			for(var i in lamankadadr ){
				finalprint = finalprint + printhtml + lamankadadr[i] + printhtmlend;
			}
			finalprint = replaceAll(finalprint,'undefined','');

			combinePage +="<div>" + finalprint + "</div>";

		}


		Popup(combinePage);
	}

	function PrintElemIr(ir_limit)
	{
		if(localStorage['print_ir'] == 0){
			return true; // dont print ir
		}
		var memdata = '';
		var PR_LABEL = $('#PR_LABEL').val();
		try{
			memdata = JSON.parse($('#mem_data').val());
			if(memdata.is_blacklisted == 1){
				$('.notcashlist').hide();
			} else {
				$('.notcashlist').show();
			}
		} catch(e){

		}

		var fontFamily = "font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif;letter-spacing:1px;";
		var mem = $("#member_id");
		var member_name = '';
		var styling = JSON.parse(localStorage['ir_format']);
		var mem_name_split;
		if(mem.val()){
			member_name = $("#member_id").select2('data').text;
			mem_name_split = member_name.split(',');
			member_name = mem_name_split[0];
		}

		var memlisttest = '';
		if(localStorage['members']){
			memlisttest = JSON.parse(localStorage['members']);
		}

		var remarks = $('#remarks').val();
		var station_name ='';
		var station_address='';
		var station = $("#opt_station");
		var station_id ='';
		if(memlisttest){
			for(var i in memlisttest){
				var cur = memlisttest[i];
				if(cur.id == mem.val()){
					station_name = cur.personal_address + "<br>Contact Person: " + cur.firstname + " " + cur.lastname + "<br>Contact #: " + cur.contact_number;
				}
			}
		}
		if(mem.val()){
			station_name = $("#member_id").select2('data').address;
		}


		if(station.val()){

			station_address = $("#"+station.attr('id')+ " :selected").attr('data-address');
			station_id = $("#"+station.attr('id')+ " :selected").text()
		}
		var cur_date = Date.now() /1000;

		var timedifference = parseInt(localStorage['servertime']) - parseInt(localStorage['localtime']);
		cur_date = parseInt(cur_date) + parseInt(timedifference);

		var d = new Date(cur_date * 1000);
		var month = d.getMonth()+1;
		var day = d.getDate();
		var output = (month<10 ? '0' : '') + month + '/' +
			(day<10 ? '0' : '') + day + '/' + d.getFullYear();
		var custom_date_sold = $('#custom_date_sold').val();
		if(custom_date_sold){
			output = custom_date_sold;
		}
		var datevisible = (styling['date']['visible']) ? 'display:block;' : 'display:none;';
		var membernamevisible = (styling['membername']['visible']) ? 'display:block;' : 'display:none;';
		var memberaddressvisible = (styling['memberaddress']['visible']) ? 'display:block;' : 'display:none;';
		var stationnamevisible = (styling['stationname']['visible']) ? 'display:block;' : 'display:none;';
		var stationaddressvisible = (styling['stationaddress']['visible']) ? 'display:block;' : 'display:none;';
		var itemtablevisible = (styling['itemtable']['visible']) ? 'display:block;' : 'display:none;';
		var paymentsvisible = (styling['payments']['visible']) ? 'display:block;' : 'display:none;';
		var payments2visible = (styling['payments2']['visible']) ? 'display:block;' : 'display:none;';
		var payments3visible = (styling['payments3']['visible']) ? 'display:block;' : 'display:none;';
		var cashiervisible = (styling['cashier']['visible']) ? 'display:block;' : 'display:none;';
		var remarksvisible = (styling['remarks']['visible']) ? 'display:block;' : 'display:none;';
		var reservedvisible = (styling['reserved']['visible']) ? 'display:block;' : 'display:none;';
		var drnumvisible = (styling['drnum']['visible']) ? 'display:block;' : 'display:none;';
		var tdbarcodevisible = (styling['tdbarcode']['visible']) ? 'display:inline-block;' : 'display:none;';
		var tdqtyvisible = (styling['tdqty']['visible']) ? 'display:inline-block;' : 'display:none;';
		var tddescriptionvisible = (styling['tddescription']['visible']) ? 'display:inline-block;' : 'display:none;';
		var tdpricevisible = (styling['tdprice']['visible']) ? 'display:inline-block;' : 'display:none;';
		var tdtotalvisible = (styling['tdtotal']['visible']) ? 'display:inline-block;' : 'display:none;';


		var dateBold = (styling['date']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var membernameBold = (styling['membername']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var memberaddressBold = (styling['memberaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var stationnameBold = (styling['stationname']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var stationaddressBold = (styling['stationaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var itemtableBold = (styling['itemtable']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var paymentsBold = (styling['payments']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var payments2Bold = (styling['payments2']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var payments3Bold = (styling['payments3']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var cashierBold = (styling['cashier']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var remarksBold = (styling['remarks']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var reservedBold = (styling['reserved']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var drnumBold = (styling['drnum']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';

		var tdbarcodeBold = (styling['tdbarcode']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var tdqtyBold = (styling['tdqty']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var tddescriptionBold = (styling['tddescription']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var tdpriceBold = (styling['tdprice']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var tdtotalBold = (styling['tdtotal']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';

		var howMany = $('#print_copy').val();
		howMany = (howMany) ? parseInt(howMany) : 1;
		var combinePage = "";
		for(var countPage = 1; countPage <= howMany; countPage++) { // all page
			if(countPage == 1) { // hide price and total
				tdpricevisible = 'display:none;';
				tdtotalvisible = 'display:none;';
			} else {
				tdpricevisible = (styling['tdprice']['visible']) ? 'display:inline-block;' : 'display:none;';
				tdtotalvisible = (styling['tdtotal']['visible']) ? 'display:inline-block;' : 'display:none;';
			}
			var printhtml = "";
			printhtml = printhtml + "<div id='maindivforprinting' style='page-break-before: always;position:relative;"+fontFamily+"'>&nbsp;";
			printhtml = printhtml + "<div style='" + datevisible + dateBold + "position:absolute;top:" + styling['date']['top'] + "px; left:" + styling['date']['left'] + "px;font-size:" + styling['date']['fontSize'] + "px;'><br/><br/>" + output + " </div><div style='clear:both;'></div>";
			printhtml = printhtml + "<div style='" + membernamevisible + membernameBold + "position:absolute;top:" + styling['membername']['top'] + "px; left:" + styling['membername']['left'] + "px;font-size:" + styling['membername']['fontSize'] + "px;'>" + member_name + "</div>";
			printhtml = printhtml + "<div style='" + memberaddressvisible + memberaddressBold + "position:absolute;top:" + styling['memberaddress']['top'] + "px; left:" + styling['memberaddress']['left'] + "px;width:" + styling['memberaddress']['width'] + "px;font-size:" + styling['memberaddress']['fontSize'] + "px;'>" + station_name + "</div>";
			printhtml = printhtml + "<div style='" + stationnamevisible + stationnameBold + "position:absolute;top:" + styling['stationname']['top'] + "px; left:" + styling['stationname']['left'] + "px;font-size:" + styling['stationname']['fontSize'] + "px;'>" + station_id + "</div>";
			printhtml = printhtml + "<div style='" + stationaddressvisible + stationaddressBold + "position:absolute;top:" + styling['stationaddress']['top'] + "px; left:" + styling['stationaddress']['left'] + "px;width:" + styling['stationaddress']['width'] + "px;font-size:" + styling['stationaddress']['fontSize'] + "px;'>" + station_address + "</div>";
			printhtml = printhtml + "<table id='itemscon' style='position:absolute;top:" + styling['itemtable']['top'] + "px;left:" + styling['itemtable']['left'] + "px;font-size:" + styling['itemtable']['fontSize'] + "px;'> ";
			var countallitem = $('#cart > tbody > tr').length;
			var irlimit = localStorage['ir_limit'];
			var lamankadadr = [];
			var pagectr = 1;
			var rowctr = 1;
			var pagesubtotal = 0;
			var pagetax = 0;
			var pagegrandtotal = 0;
			var vat = 1.12;
			irlimit = parseInt(irlimit) + 1;
			var reservedbyname = "";
			$('#cart > tbody > tr').each(function(index) {
				var row = $(this);
				var itemcode = row.attr("data-itemcode");
				var description = row.attr("data-desc");
				var b = row.attr('data-barcode');
				var unit_name = row.attr('data-unit_name');
				unit_name = (unit_name) ? unit_name : '';
				var qty = row.children().eq(1).text()+ "<td style='width:45px;'>"+unit_name+"</td>";
				var price = row.children().eq(2).text();
				var discount = row.children().eq(3).find('input').val();
				var total = replaceAll(row.children().eq(4).text(), ',', '');
				var origtotal = parseFloat(qty) * parseFloat(price);
				var adjustment = row.children().eq(3).text();

				var additionalDiscount = parseFloat(row.attr("data-store_discount"));
				discount = parseFloat(discount) + additionalDiscount;
				reservedbyname = row.attr('data-reserved_by');
				if(parseFloat(adjustment) != 0) {
					var perunitdisc = parseFloat(discount) / parseFloat(qty);
					var labeldisc = "";
					var labeldisc2 = "<br/>(" + number_format(adjustment, 2) + ")";
				} else {
					var labeldisc = '';
					var labeldisc2 = '';
				}
				if(rowctr % irlimit == 0) {
					var subtotal = (pagesubtotal / vat);
					var vatable = parseFloat(pagesubtotal) - parseFloat(subtotal);
					subtotal = subtotal.toFixed(2);
					vatable = vatable.toFixed(2);
					pagesubtotal = pagesubtotal.toFixed(2);
					if(!lamankadadr[pagectr]) lamankadadr[pagectr] = '';
					lamankadadr[pagectr] = lamankadadr[pagectr] + "</table>";
					if(memdata && memdata.tax_type == "") { // dont print tax compu
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + paymentsvisible + paymentsBold + "position:absolute; list-style-type: none; left:" + styling['payments']['left'] + "px;top:" + styling['payments']['top'] + "px;font-size:" + styling['payments']['fontSize'] + "px;'>" + subtotal + "</div>";
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + payments2visible + payments2Bold + "position:absolute; list-style-type: none; left:" + styling['payments2']['left'] + "px;top:" + styling['payments2']['top'] + "px;font-size:" + styling['payments2']['fontSize'] + "px;'>" + vatable + "</div>";
					}
					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + payments3visible + payments3Bold + "position:absolute; list-style-type: none; left:" + styling['payments3']['left'] + "px;top:" + styling['payments3']['top'] + "px;font-size:" + styling['payments3']['fontSize'] + "px;'>" + pagesubtotal + "</div>";
					pagectr = parseInt(pagectr) + 1;
					pagesubtotal = 0;
				}
				pagesubtotal = parseFloat(pagesubtotal) + parseFloat(total);
				lamankadadr[pagectr] = lamankadadr[pagectr] + "<tr ><td style='" + tdbarcodevisible + tdbarcodeBold + "position:relative;width:" + styling['tdbarcode']['width'] + "px;padding-left:" + styling['tdbarcode']['left'] + "px;'>" + itemcode + "</td><td style='" + tdqtyvisible + tdqtyBold + "position:relative;width:" + styling['tdqty']['width'] + "px;padding-left:" + styling['tdqty']['left'] + "px;'>" + qty + "</td><td style='" + tddescriptionvisible + tddescriptionBold + "position:relative;width:" + styling['tddescription']['width'] + "px;padding-left:" + styling['tddescription']['left'] + "px;'> " + description + " <span style='padding-left:20px;'>" + labeldisc + "</span> </td><td style='" + tdpricevisible + tdpriceBold + "position:relative;width:" + styling['tdprice']['width'] + "px;padding-left:" + styling['tdprice']['left'] + "px;'>" + number_format(price, 2) + "</td><td style='" + tdtotalvisible + tdtotalBold + "position:relative;width:" + styling['tdtotal']['width'] + "px;padding-left:" + styling['tdtotal']['left'] + "px;'>" + number_format(origtotal, 2) + " " + labeldisc2 + "</td></tr>";
				rowctr = parseInt(rowctr) + 1;
			});
			if(pagesubtotal > 0) {
				var consumable_payment = $('#hidconsumablepayment').val();
				if(consumable_payment > 0){
					pagesubtotal = pagesubtotal - consumable_payment;
				}
				var subtotal = (pagesubtotal / vat);
				var vatable = parseFloat(pagesubtotal) - parseFloat(subtotal);
				subtotal = subtotal.toFixed(2);
				vatable = vatable.toFixed(2);
				pagesubtotal = pagesubtotal.toFixed(2);
				if(!lamankadadr[pagectr]) lamankadadr[pagectr] = '';
				lamankadadr[pagectr] = lamankadadr[pagectr] + "</table>";
				if(consumable_payment > 0) lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+paymentsvisible+paymentsBold+"position:absolute; list-style-type: none; left:"+styling['payments']['left']+"px;top:"+((styling['payments']['top']) - 12) +"px;font-size:"+styling['payments']['fontSize']+"px;'>("+consumable_payment+")</div>";

				if(memdata && memdata.tax_type == "") { // dont print tax compu
					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + paymentsvisible + paymentsBold + "position:absolute; list-style-type: none; left:" + styling['payments']['left'] + "px;top:" + styling['payments']['top'] + "px;font-size:" + styling['payments']['fontSize'] + "px;'>" + subtotal + "</div>";
					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + payments2visible + payments2Bold + "position:absolute; list-style-type: none; left:" + styling['payments2']['left'] + "px;top:" + styling['payments2']['top'] + "px;font-size:" + styling['payments2']['fontSize'] + "px;'>" + vatable + "</div>";
				}
				lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + payments3visible + payments3Bold + "position:absolute; list-style-type: none; left:" + styling['payments3']['left'] + "px;top:" + styling['payments3']['top'] + "px;font-size:" + styling['payments3']['fontSize'] + "px;'>" + pagesubtotal + "</div>";
			}
			var printhtmlend = "";
			if(!reservedbyname) reservedbyname = "";
			var agent_con = $('#agent_id');
			var agent_name = "";
			if(agent_con.val()) {
				agent_name = agent_con.select2('data').text;
			}
			reservedbyname = agent_name;
			var cir = $('#custom_ir').val();
			var nir = parseInt(localStorage['ir']) + 1;
			var custom_ir = (cir) ? cir : nir;
			var irctrnum = custom_ir;
			var pref_ir = (localStorage['pref_ir']) ? localStorage['pref_ir'] : '';
			irctrnum = str_pad('000000',irctrnum,true);
			irctrnum = pref_ir + irctrnum;
			irctrnum = PR_LABEL + " " + irctrnum;
			if(!remarks) remarks = "";
			printhtmlend = printhtmlend + "<div style='" + cashiervisible + cashierBold + "position:absolute;left:" + styling['cashier']['left'] + "px;top:" + styling['cashier']['top'] + "px;font-size:" + styling['cashier']['fontSize'] + "px;'>" + localStorage['current_lastname'] + ", " + localStorage['current_firstname'] + "</div>";
			printhtmlend = printhtmlend + "<div style='" + remarksvisible + remarksBold + "position:absolute;left:" + styling['remarks']['left'] + "px;top:" + styling['remarks']['top'] + "px;font-size:" + styling['remarks']['fontSize'] + "px;'>" + remarks + "</div>";
			printhtmlend = printhtmlend + "<div style='" + reservedvisible + reservedBold + "position:absolute;left:" + styling['reserved']['left'] + "px;top:" + styling['reserved']['top'] + "px;font-size:" + styling['reserved']['fontSize'] + "px;'>" + reservedbyname + "</div>";
			printhtmlend = printhtmlend + "<div style='" + drnumvisible + drnumBold + "position:absolute;left:" + styling['drnum']['left'] + "px;top:" + styling['drnum']['top'] + "px;font-size:" + styling['drnum']['fontSize'] + "px;'>" + irctrnum + "</div>";


			var termstxt = '';
			var ponumtxt = '';
			var tintxt = '';
			// add here
			var termsvisible = (styling['terms']['visible']) ? 'display:inline-block;' : 'display:none;';
			var ponumvisible = (styling['ponum']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tinvisible = (styling['tin']['visible']) ? 'display:inline-block;' : 'display:none;';
			var termsbold = (styling['terms']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var ponumbold = (styling['ponum']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tinbold = (styling['tin']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';

			printhtmlend = printhtmlend + "<div style='" + termsvisible + termsbold + "position:absolute;left:" + styling['terms']['left'] + "px;top:" + styling['terms']['top'] + "px;font-size:" + styling['terms']['fontSize'] + "px;'>" + termstxt + "</div>";
			printhtmlend = printhtmlend + "<div style='" + ponumvisible + ponumbold + "position:absolute;left:" + styling['ponum']['left'] + "px;top:" + styling['ponum']['top'] + "px;font-size:" + styling['ponum']['fontSize'] + "px;'>" + ponumtxt + "</div>";
			printhtmlend = printhtmlend + "<div style='" + tinvisible + tinbold + "position:absolute;left:" + styling['tin']['left'] + "px;top:" + styling['tin']['top'] + "px;font-size:" + styling['tin']['fontSize'] + "px;'>" + tintxt + "</div>";

			printhtmlend = printhtmlend + "</div>";
			var finalprint = "";
			for(var i in lamankadadr) {
				finalprint = finalprint + printhtml + lamankadadr[i] + printhtmlend;
			}
			finalprint = replaceAll(finalprint, 'undefined', '');
			combinePage +="<div>" + finalprint + "</div>";
		}
		console.log(combinePage);
		Popup(combinePage);
	}
	function PrintElemNewsPrint(dr_limit)
	{
		var memdata = '';
		var PR_LABEL = $('#PR_LABEL').val();
		var DR_LABEL = $('#DR_LABEL').val();

		try{
			memdata = JSON.parse($('#mem_data').val());
			if(memdata.is_blacklisted == 1){
				$('.notcashlist').hide();
			} else {
				$('.notcashlist').show();
			}
		} catch(e){

		}
		var checkDR = $('#checkDR').is(':checked');
		var checkIR = $('#checkIR').is(':checked');

		//var fontFamily = "font-family: Calibri;letter-spacing:2px;";
		var fontFamily = "font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif;letter-spacing:1px;";
		var displayPriceType = 1;
		var mem = $("#member_id");
		var member_name = '';
		var styling = JSON.parse(localStorage['news_format']);
		var mem_name_split;
		var salestype = '';
		if(mem.val()){
			member_name = $("#member_id").select2('data').text;
			mem_name_split = member_name.split(',');
			member_name = mem_name_split[0];
			salestype = mem_name_split[1];
		}


		var memlisttest = '';
		if(localStorage['members']){
			memlisttest = JSON.parse(localStorage['members']);
		}


		var remarks = $('#sales_remarks').val();
		var station_name ='';
		var station_address='';
		var station = $("#opt_station");
		var station_id ='';

		if(memlisttest){
			for(var i in memlisttest){
				var cur = memlisttest[i];
				if(cur.id == mem.val()){
					station_name = cur.personal_address + "<br>Contact Person: " + cur.firstname + " " + cur.lastname + "<br>Contact #: " + cur.contact_number;
				}
			}
		}
		if(mem.val()){
			station_name = $("#member_id").select2('data').address;
		}
		if(station.val()){

			station_address = $("#"+station.attr('id')+ " :selected").attr('data-address');
			station_id = $("#"+station.attr('id')+ " :selected").text()
		}
		var cur_date = Date.now() /1000;

		var timedifference = parseInt(localStorage['servertime']) - parseInt(localStorage['localtime']);
		cur_date = parseInt(cur_date) + parseInt(timedifference);

		var d = new Date(cur_date * 1000);
		var month = d.getMonth()+1;
		var day = d.getDate();
		var output = (month<10 ? '0' : '') + month + '/' +
			(day<10 ? '0' : '') + day + '/' + d.getFullYear();
		var custom_date_sold = $('#custom_date_sold').val();
		if(custom_date_sold){
			output = custom_date_sold;
		}
		var datevisible = (styling['date']['visible']) ? 'display:block;' : 'display:none;';
		var membernamevisible = (styling['membername']['visible']) ? 'display:block;' : 'display:none;';
		var memberaddressvisible = (styling['memberaddress']['visible']) ? 'display:block;' : 'display:none;';
		var stationnamevisible = (styling['stationname']['visible']) ? 'display:block;' : 'display:none;';
		var stationaddressvisible = (styling['stationaddress']['visible']) ? 'display:block;' : 'display:none;';
		var itemtablevisible = (styling['itemtable']['visible']) ? 'display:block;' : 'display:none;';
		var paymentsvisible = (styling['payments']['visible']) ? 'display:block;' : 'display:none;';
		var payments2visible = (styling['payments2']['visible']) ? 'display:block;' : 'display:none;';
		var payments3visible = (styling['payments3']['visible']) ? 'display:block;' : 'display:none;';
		var cashiervisible = (styling['cashier']['visible']) ? 'display:block;' : 'display:none;';
		var remarksvisible = (styling['remarks']['visible']) ? 'display:block;' : 'display:none;';
		var reservedvisible = (styling['reserved']['visible']) ? 'display:block;' : 'display:none;';
		var drnumvisible = (styling['drnum']['visible']) ? 'display:block;' : 'display:none;';
		var tdbarcodevisible = (styling['tdbarcode']['visible']) ? 'display:inline-block;' : 'display:none;';
		var tdqtyvisible = (styling['tdqty']['visible']) ? 'display:inline-block;' : 'display:none;';
		var tddescriptionvisible = (styling['tddescription']['visible']) ? 'display:inline-block;' : 'display:none;';
		var tdpricevisible = (styling['tdprice']['visible']) ? 'display:inline-block;' : 'display:none;';
		var tdtotalvisible = (styling['tdtotal']['visible']) ? 'display:inline-block;' : 'display:none;';

		var dateBold = (styling['date']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var membernameBold = (styling['membername']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var memberaddressBold = (styling['memberaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var stationnameBold = (styling['stationname']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var stationaddressBold = (styling['stationaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var itemtableBold = (styling['itemtable']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var paymentsBold = (styling['payments']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var payments2Bold = (styling['payments2']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var payments3Bold = (styling['payments3']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var cashierBold = (styling['cashier']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var remarksBold = (styling['remarks']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var reservedBold = (styling['reserved']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var drnumBold = (styling['drnum']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var tdbarcodeBold = (styling['tdbarcode']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var tdqtyBold = (styling['tdqty']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var tddescriptionBold = (styling['tddescription']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var tdpriceBold = (styling['tdprice']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var tdtotalBold = (styling['tdtotal']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var howMany = $('#print_copy').val();
		howMany = (howMany) ? parseInt(howMany) : 1;
		var combinePage = "";
		for(var countPage = 1; countPage <= howMany; countPage++){ // all page
			var printhtml="";
			printhtml = printhtml + "<div id='maindivforprinting' style='page-break-before: always;position:relative;"+fontFamily+"'>&nbsp;";
			printhtml= printhtml +  "<div style='"+datevisible+dateBold+"position:absolute;top:"+styling['date']['top']+"px; left:"+styling['date']['left']+"px;font-size:"+styling['date']['fontSize']+"px;'><br/><br/>"+  output+ " </div><div style='clear:both;'></div>";
			printhtml= printhtml +  "<div style='"+membernamevisible+membernameBold+"position:absolute;top:"+styling['membername']['top']+"px; left:"+styling['membername']['left']+"px;font-size:"+styling['membername']['fontSize']+"px;'>"+member_name+"</div>";
			printhtml= printhtml +  "<div style='"+memberaddressvisible+memberaddressBold+"position:absolute;top:"+styling['memberaddress']['top']+"px; left:"+styling['memberaddress']['left']+"px;width:"+styling['memberaddress']['width']+"px;font-size:"+styling['memberaddress']['fontSize']+"px;'>"+station_name+"</div>";
			printhtml= printhtml +  "<div style='"+stationnamevisible+stationnameBold+"position:absolute;top:"+styling['stationname']['top']+"px; left:"+styling['stationname']['left']+"px;font-size:"+styling['stationname']['fontSize']+"px;'>"+station_id+"</div>";
			printhtml= printhtml +  "<div style='"+stationaddressvisible+stationaddressBold+"position:absolute;top:"+styling['stationaddress']['top']+"px; left:"+styling['stationaddress']['left']+"px;width:"+styling['stationaddress']['width']+"px;font-size:"+styling['stationaddress']['fontSize']+"px;'>"+station_address+"</div>";
			printhtml= printhtml + "<table id='itemscon' style='position:absolute;top:"+styling['itemtable']['top']+"px;left:"+styling['itemtable']['left']+"px;font-size:"+styling['itemtable']['fontSize']+"px;'> ";
			var countallitem = 	$('#cart > tbody > tr').length;
			var drlimit = localStorage['dr_limit'];
			var lamankadadr =[];
			var pagectr = 1;
			var rowctr = 1;
			var pagesubtotal = 0;
			var pagetax=0;
			var pagegrandtotal = 0;
			var vat = 1.12;
			drlimit = parseInt(drlimit) + 1;
			var reservedbyname = "";
			$('#cart > tbody > tr').each(function(index){
				var row = $(this);
				var itemcode = row.attr("data-itemcode");
				var description = row.attr("data-desc");
				var b = row.attr('data-barcode');
				var unit_name = row.attr('data-unit_name');
				unit_name = (unit_name) ? unit_name : '';
				var qty = row.children().eq(1).text()+ "<td style='width:45px;'>"+unit_name+"</td>";
				var price = row.children().eq(2).text();
				var discount = 0;
				var total = replaceAll(row.children().eq(4).text(),',','');
				var origtotal = parseFloat(qty) * parseFloat(price);
				var adjustment = row.children().eq(3).text();

				var additionalDiscount = parseFloat(row.attr("data-member_adjustment"));
				discount = parseFloat(discount) + additionalDiscount;
				reservedbyname = row.attr('data-reserved_by');
				var labeldisc ='';
				var labeldisc2 ='';
				if(parseFloat(adjustment) != 0){
					var perunitdisc = parseFloat(discount) / parseFloat(row.children().eq(1).text());
					if(displayPriceType == 1){
						price = parseFloat(price) + parseFloat(perunitdisc);
						origtotal = total;
					} else {
						labeldisc = "";
						labeldisc2 = "<br/>("+number_format(adjustment,2)+")";
					}
				}
				if(rowctr % drlimit == 0){
					var subtotal = (pagesubtotal / vat);
					var vatable = parseFloat(pagesubtotal) - parseFloat(subtotal);
					subtotal = subtotal.toFixed(2);
					vatable = vatable.toFixed(2);
					pagesubtotal = pagesubtotal.toFixed(2);
					if(!lamankadadr[pagectr]) lamankadadr[pagectr] = '';
					lamankadadr[pagectr] = lamankadadr[pagectr] + "</table>";
					if(memdata && memdata.tax_type == "") { // dont print tax compu
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + paymentsvisible + paymentsBold + "position:absolute; list-style-type: none; left:" + styling['payments']['left'] + "px;top:" + styling['payments']['top'] + "px;font-size:" + styling['payments']['fontSize'] + "px;'>" + subtotal + "</div>";
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + payments2visible + payments2Bold + "position:absolute; list-style-type: none; left:" + styling['payments2']['left'] + "px;top:" + styling['payments2']['top'] + "px;font-size:" + styling['payments2']['fontSize'] + "px;'>" + vatable + "</div>";
					}
					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments3visible+payments3Bold+"position:absolute; list-style-type: none; left:"+styling['payments3']['left']+"px;top:"+styling['payments3']['top']+"px;font-size:"+styling['payments3']['fontSize']+"px;'>"+pagesubtotal+"</div>";
					pagectr = parseInt(pagectr) + 1;
					pagesubtotal=0;
				}
				pagesubtotal = parseFloat(pagesubtotal) + parseFloat(total);
				lamankadadr[pagectr] = lamankadadr[pagectr] + "<tr ><td style='"+tdbarcodevisible+tdbarcodeBold+"position:relative;width:"+styling['tdbarcode']['width']+"px;padding-left:"+styling['tdbarcode']['left']+"px;'>"+itemcode+"</td><td style='"+tdqtyvisible+tdqtyBold+"position:relative;width:"+styling['tdqty']['width']+"px;padding-left:"+styling['tdqty']['left']+"px;'>"+qty+"</td><td style='"+tddescriptionvisible+tddescriptionBold+"position:relative;width:"+styling['tddescription']['width']+"px;padding-left:"+styling['tddescription']['left']+"px;'> "+ description +" <span style='padding-left:20px;'>"+labeldisc+"</span> </td><td style='"+tdpricevisible+tdpriceBold+"position:relative;width:"+styling['tdprice']['width']+"px;padding-left:"+styling['tdprice']['left']+"px;text-align:right;'>"+number_format(price,2)+"</td><td style='"+tdtotalvisible+tdtotalBold+"position:relative;width:"+styling['tdtotal']['width']+"px;padding-left:"+styling['tdtotal']['left']+"px;text-align:right;'>"+number_format(origtotal,2)+" "+labeldisc2+"</td></tr>";
				rowctr = parseInt(rowctr) +1;
			});
			if(pagesubtotal > 0){
				var subtotal = (pagesubtotal / vat);
				var vatable = parseFloat(pagesubtotal) - parseFloat(subtotal);
				subtotal = subtotal.toFixed(2);
				vatable = vatable.toFixed(2);
				pagesubtotal = pagesubtotal.toFixed(2);
				if(!lamankadadr[pagectr]) lamankadadr[pagectr] = '';
				lamankadadr[pagectr] = lamankadadr[pagectr] + "</table>";
				if(memdata && memdata.tax_type == ""){ // dont print tax compu
					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+paymentsvisible+paymentsBold+"position:absolute; list-style-type: none; left:"+styling['payments']['left']+"px;top:"+styling['payments']['top']+"px;font-size:"+styling['payments']['fontSize']+"px;'>"+subtotal+"</div>";
					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments2visible+payments2Bold+"position:absolute; list-style-type: none; left:"+styling['payments2']['left']+"px;top:"+styling['payments2']['top']+"px;font-size:"+styling['payments2']['fontSize']+"px;'>"+vatable+"</div>";
				}
				lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments3visible+payments3Bold+"position:absolute; list-style-type: none; left:"+styling['payments3']['left']+"px;top:"+styling['payments3']['top']+"px;font-size:"+styling['payments3']['fontSize']+"px;'>&nbsp;&nbsp; Grand Total: "+ number_format(pagesubtotal,2)+"</div>";
				lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments3visible+payments3Bold+"position:absolute; list-style-type: none; left:"+styling['payments3']['left']+"px;top:"+ (parseInt(styling['payments3']['top']) + parseInt(12)) +"px;font-size:"+styling['payments3']['fontSize']+"px;width:255px;border-bottom:1px solid #000;'>&nbsp;</div>";
				lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments3visible+payments3Bold+"position:absolute; list-style-type: none; left:"+styling['payments3']['left']+"px;top:"+(parseInt(styling['payments3']['top']) + parseInt(15))+"px;font-size:"+styling['payments3']['fontSize']+"px;width:255px;border-bottom:1px solid #000;'>&nbsp;</div>";
			}
			var printhtmlend = "";
			if(!reservedbyname) reservedbyname = "";
			var agent_con = $('#agent_id');
			var agent_name ="";
			if(agent_con.val()){
				agent_name = agent_con.select2('data').text;
			}
			reservedbyname = agent_name;
			reservedbyname = salestype + " " + reservedbyname;

			var ctr_number = '';
			if(checkDR){
				var cdr = $('#custom_dr').val();
				var ndr = parseInt(localStorage['dr']) + 1;
				var custom_dr = (cdr) ? cdr : ndr;
				var drnumctr =  custom_dr;
				var pref_dr = (localStorage['pref_dr']) ? localStorage['pref_dr'] : '';
				drnumctr = str_pad('000000',drnumctr,true);
				drnumctr = pref_dr + drnumctr;
				ctr_number = DR_LABEL + " " + drnumctr;
			} else if(checkIR){
				var cir = $('#custom_ir').val();
				var nir = parseInt(localStorage['ir']) + 1;
				var custom_ir = (cir) ? cir : nir;
				var irctrnum = custom_ir;
				var pref_ir = (localStorage['pref_ir']) ? localStorage['pref_ir'] : '';
				irctrnum = str_pad('000000',irctrnum,true);
				irctrnum = pref_ir + irctrnum;
				ctr_number = PR_LABEL + " " +irctrnum;
			}


			if(!remarks) remarks = "";
			printhtmlend = printhtmlend + "<div style='"+cashiervisible+cashierBold+"position:absolute;left:"+styling['cashier']['left']+"px;top:"+styling['cashier']['top']+"px;font-size:"+styling['cashier']['fontSize']+"px;'>"+localStorage['current_lastname'] + ", "  + localStorage['current_firstname'] +"</div>";
			printhtmlend = printhtmlend + "<div style='"+remarksvisible+remarksBold+"position:absolute;left:"+styling['remarks']['left']+"px;top:"+styling['remarks']['top']+"px;font-size:"+styling['remarks']['fontSize']+"px;'>"+remarks+"</div>";
			printhtmlend = printhtmlend + "<div style='"+reservedvisible+reservedBold+"position:absolute;left:"+styling['reserved']['left']+"px;top:"+styling['reserved']['top']+"px;font-size:"+styling['reserved']['fontSize']+"px;'>"+reservedbyname+"</div>";
			printhtmlend = printhtmlend + "<div style='"+drnumvisible+drnumBold+"position:absolute;left:"+styling['drnum']['left']+"px;top:"+styling['drnum']['top']+"px;font-size:"+styling['drnum']['fontSize']+"px;'>"+ctr_number+"</div>";


			var termstxt ='';
			var ponumtxt ='';
			var tintxt ='';

			var termsvisible = (styling['terms']['visible']) ? 'display:inline-block;' : 'display:none;';
			var ponumvisible = (styling['ponum']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tinvisible = (styling['tin']['visible']) ? 'display:inline-block;' : 'display:none;';
			var termsbold = (styling['terms']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var ponumbold = (styling['ponum']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tinbold = (styling['tin']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';

			printhtmlend = printhtmlend + "<div style='"+termsvisible+termsbold+"position:absolute;left:"+styling['terms']['left']+"px;top:"+styling['terms']['top']+"px;font-size:"+styling['terms']['fontSize']+"px;'>"+termstxt+"</div>";
			printhtmlend = printhtmlend + "<div style='"+ponumvisible+ponumbold+"position:absolute;left:"+styling['ponum']['left']+"px;top:"+styling['ponum']['top']+"px;font-size:"+styling['ponum']['fontSize']+"px;'>"+ponumtxt+"</div>";
			printhtmlend = printhtmlend + "<div style='"+tinvisible+tinbold+"position:absolute;left:"+styling['tin']['left']+"px;top:"+styling['tin']['top']+"px;font-size:"+styling['tin']['fontSize']+"px;'>"+tintxt+"</div>";


			printhtmlend = printhtmlend + "</div>";
			var finalprint = "";
			for(var i in lamankadadr ){
				finalprint = finalprint + printhtml + lamankadadr[i] + printhtmlend;
			}
			finalprint = replaceAll(finalprint,'undefined','');

			combinePage +="<div>" + finalprint + "</div>";

		}

		Popup(combinePage);
	}
	function Popup(data)
	{
		var mywindow = window.open('', 'new div', '');
		mywindow.document.write('<!DOCTYPE html><html><head><title></title><style></style>');
		mywindow.document.write('</head><body style="padding:0;margin:0;">');
		mywindow.document.write(data);
		mywindow.document.write('</body></html>');

		mywindow.print();
		mywindow.close();
		return true;


	}
	getLastSoldItem();
	function getLastSoldItem(){
		var member_id = $('#member_id').val();
		var terminal_id = localStorage['terminal_id'];
		$.ajax({
			url:'../ajax/ajax_query2.php',
			type:'POST',
			beforeSend:function(){
				$('#lastSold').html('Fetching record. Please wait...');
			},
			data: {functionName:'lastSoldItem',member_id:member_id,terminal_id:terminal_id},
			success: function(data){
				$('#lastSold').html(data);
				if($('#service_used_items').length){
					try{
						removeNoItemLabel();
						var useditems = JSON.parse($('#service_used_items').val());
						for(var i in useditems){
							var item_code = useditems[i].item_code+"<small style='display:block' class='text-danger'>"+useditems[i].description+"</small>";

							$('#cart tbody').append("<tr data-barcode='"+useditems[i].barcode+"' data-unit_name='"+useditems[i].unit_name+"' data-itemcode='"+useditems[i].item_code+"' data-desc='"+useditems[i].description+"' data-item_id='"+useditems[i].item_id+"' data-price_adjustment='"+0+"' data-member_adjustment='"+0+"'><td>"+item_code+"</td><td>"+useditems[i].qty+"</td><td>"+useditems[i].price+"</td><td>"+0+"</td><td>"+useditems[i].total+"</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");

						}
						updatesubtotal();
					} catch(e){

					}
				}
				if($('#mem_salestype').val()){
					$('#sales_type').select2('val',$('#mem_salestype').val());
					$('#sales_type').select2('enable',false);
				} else {
					$('#sales_type').select2('enable',true);
				}
				if($('#mem_point_reg').length){
					try{

						var point_reg = JSON.parse($('#mem_point_reg').val());
						var ret_html='';
						for(var j in point_reg){
							ret_html += " <input checked type='checkbox' class='member_point_enrolled'  value='"+point_reg[j].point_id+"'> " + point_reg[j].point_name;
						}
						$('#point_holder').html(ret_html);

					} catch(e){

					}
				}
			},
			error:function(){
				console.log('Error Occur');
			}
		});
	}
	function getPointCredited(){
		var arr =[];
		if($('#mem_point_reg').length){
			$('.member_point_enrolled:checkbox:checked').each(function(){
				arr.push($(this).val());
			});
		}
		return JSON.stringify(arr);
	}
	function checkTerminal(){
		$('#all_content').hide();
		$('#error_content').hide();
		if(localStorage['terminal_id'] != '0'){
			$('#all_content').fadeIn(300);
		} else {
			$('#error_content').fadeIn(300);
		}
	}
	function toggleMemberInput(){
		var l = $('#cart tbody tr').length;
		if(parseFloat(l) > 0){
			$('#member_id').select2('disable',true);
			$('#checkout').attr('disabled',false);
			$('#print').attr('disabled',false);
		}else {
			$('#member_id').select2('enable',true);
			$('#checkout').attr('disabled',true);
			$('#print').attr('disabled',true);
		}
	}
	function getServerTime(){

		if (!Date.now) {
			Date.now = function() { return new Date().getTime(); };
		}
		var cur_date = Date.now() /1000;

		$.ajax({
			url: "../ajax/ajax_getservertime.php",
			type:"POST",
			data:{},
			success: function(data){
				localStorage["servertime"] = data;
				localStorage["localtime"] = Math.floor(cur_date);
			}
		});
	}

	// indexed db
	var db = null;
	var DBNAME = "apollo_db";
	var DBVER = 1;
	openDB();
	// open a database
	function openDB() {
		var request = indexedDB.open(DBNAME, DBVER);

		request.onupgradeneeded = function (e) {

			var thisDB = e.target.result;
			var store = null;
			if (!thisDB.objectStoreNames.contains("members")) {
				// create objectStore as keyPath="email"
				store = thisDB.createObjectStore("members", {
					keyPath: "id"
				});
				//thisDB.createObjectStore("people", { autoIncrement: true });

				// create index to 'name' for conditional search
				store.createIndex('lastname', 'lastname', {
					unique: false
				});
				//store.deleteIndex('name');
			}
			var store2 = null;
			if (!thisDB.objectStoreNames.contains("items")) {
				// create objectStore as keyPath="email"
				store2 = thisDB.createObjectStore("items", {
					keyPath: "barcode"
				});
				//thisDB.createObjectStore("people", { autoIncrement: true });

				// create index to 'name' for conditional search
				store2.createIndex('barcode', 'items', {
					unique: true
				});
				//store.deleteIndex('name');
			}
		};

		request.onsuccess = function (e) {

			db = e.target.result;

		};

		request.onerror = function (e) {

		};
	}

	function addItem(o) {
		var tx = db.transaction(["items"], "readwrite");
		var store = tx.objectStore("items");



		// add to store
		var request = store.add(o);

		request.onsuccess = function (e) {

		};

		request.onerror = function (e) {

		};
	}

	function findByKey(key) {
		var tx = db.transaction(["items"], "readonly");
		var store = tx.objectStore("items");
		var request = store.get(key);
		var ret = null;
		request.onsuccess = function (e) {
			processProduct(e.target.result);
		};
		request.onerror = function(){
			showToast('error','<p>Invalid barcode</p>','<h3>WARNING!</h3>','toast-bottom-right');
		};
	}
	function processProduct(p){
		if(p){
			var barcode = p.barcode;
			var item_code = p.item_code;
			var description = p.description;
			var price = p.price;
			var is_bundle = p.is_bundle;
			var qty= 1;
			var total = parseFloat(price) * qty;

			$('#cart tbody').append("<tr data-is_bundle='"+is_bundle+"' data-unit_name='"+p.unit_name+"' data-barcode='"+barcode+"' data-itemcode='"+item_code+"' data-desc='"+description+"' data-item_id='"+p.item_id+"' data-price_adjustment='0' data-member_adjustment='0'><td>"+item_code+"</td><td>"+qty+"</td><td>"+price+"</td><td>"+0+"</td><td>"+total+"</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
			removeNoItemLabel();
			updatesubtotal();
		} else {
			showToast('error','<p>Invalid barcode</p>','<h3>WARNING!</h3>','toast-bottom-right');
		}

	}

	$('body').on('click','#btnSync',function(){
		var con = $(this);
		button_action.start_loading(con);
		$.ajax({
			url:'../ajax/ajax_json.php',
			type:'POST',
			dataType:'json',
			data: {functionName:'allItems'},
			success: function(data){
				if(data.length){
					for(var i in data){
						addItem(data[i]);
					}
					button_action.end_loading(con);
				}
			},
			error:function(){

			}
		});
	});
	localStorage.removeItem("scan");
	barcodeListener();
	function barcodeListener(){
		var millis = 300;
		document.onkeypress = function(e) {
			e = e || window.event;
			var charCode = (typeof e.which == "number") ? e.which : e.keyCode;

			if(localStorage.getItem("scan") && localStorage.getItem("scan") != 'null') {
				localStorage.setItem("scan", localStorage.getItem("scan") + String.fromCharCode(charCode));
			} else {
				localStorage.setItem("scan", String.fromCharCode(charCode));
				setTimeout(function() {
					localStorage.removeItem("scan");
				}, millis);
			}
			if(localStorage.getItem("scan").length >= 8) {
				findByKey(localStorage.getItem("scan"));
			}
		}
	}
	var allArrItem=[];
	function findAll() {
		var tx = db.transaction(["items"], "readonly");
		var objectStore = tx.objectStore("items");
		var cursor = objectStore.openCursor();

		cursor.onsuccess = function (e) {
			var res = e.target.result;
			if (res) {
				allArrItem.push({id: res.value.item_id,text: res.value.item_code});
				res.continue();
			}
		};
	}

	function print_con_dr(){
		var memdata = '';
		try{
			memdata = JSON.parse($('#mem_data').val());
			if(memdata.is_blacklisted == 1){
				$('.notcashlist').hide();
			} else {
				$('.notcashlist').show();
			}
		} catch(e){

		}
		var mem = $("#member_id");
		var member_name = '';

		var mem_name_split;
		if(mem.val()){
			member_name = $("#member_id").select2('data').text;
			mem_name_split = member_name.split(',');
			member_name = mem_name_split[0];

		}
		var memlisttest = '';
		if(localStorage['members']){
			memlisttest = JSON.parse(localStorage['members']);
		}


		var remarks = $('#sales_remarks').val();
		var station_name ='';
		var station_address='';
		var station = $("#opt_station");
		var station_id ='';
		if(memlisttest){
			for(var i in memlisttest){
				var cur = memlisttest[i];
				if(cur.id == mem.val()){
					station_name = cur.personal_address + "<br>Contact Person: " + cur.firstname + " " + cur.lastname + "<br>Contact #: " + cur.contact_number;
				}
			}
		}
		if(station.val()){
			station_address = $("#"+station.attr('id')+ " :selected").attr('data-address');
			station_id = $("#"+station.attr('id')+ " :selected").text()
		}
		var cur_date = Date.now() /1000;

		var timedifference = parseInt(localStorage['servertime']) - parseInt(localStorage['localtime']);
		cur_date = parseInt(cur_date) + parseInt(timedifference);
		var d = new Date(cur_date * 1000);
		var month = d.getMonth()+1;
		var day = d.getDate();
		var output = (month<10 ? '0' : '') + month + '/' +
			(day<10 ? '0' : '') + day + '/' + d.getFullYear();
		var custom_date_sold = $('#custom_date_sold').val();
		if(custom_date_sold){
			output = custom_date_sold;
		}

		var pagesubtotal = 0;
		var pagetax=0;
		var pagegrandtotal = 0;
		var vat = 1.12;
		var reservedbyname = "";
		var item_list = [];
		$('#cart > tbody > tr').each(function(index){
			var row = $(this);
			var itemcode = row.attr("data-itemcode");
			var description = row.attr("data-desc");
			var b = row.attr('data-barcode');
			var unit_name = row.attr('data-unit_name');
			unit_name = (unit_name) ? unit_name : '';
			var qty = row.children().eq(1).text();
			var price = row.children().eq(2).text();
			var discount = row.children().eq(3).find('input').val();
			var total = replaceAll(row.children().eq(4).text(),',','');
			var origtotal = parseFloat(qty) * parseFloat(price);
			var adjustment = row.children().eq(3).text();
			var additionalDiscount = parseFloat(row.attr("data-store_discount"));
			discount = parseFloat(discount) + additionalDiscount;
			reservedbyname = row.attr('data-reserved_by');
			pagesubtotal = parseFloat(pagesubtotal) + parseFloat(total);
			price = number_format(price,2);
			item_list.push({qty:qty,item_code:itemcode,price:price,total:total,unit_name:unit_name,discount:discount});

		});
		if(pagesubtotal > 0){
			var consumable_payment = $('#hidconsumablepayment').val();
			if(consumable_payment > 0){
				pagesubtotal = pagesubtotal - consumable_payment;
			}
			var subtotal = (pagesubtotal / vat);
			var vatable = parseFloat(pagesubtotal) - parseFloat(subtotal);
			subtotal = subtotal.toFixed(2);
			vatable = vatable.toFixed(2);
			pagesubtotal = pagesubtotal.toFixed(2);

		}

		var agent_con = $('#agent_id');
		var agent_name ="";
		if(agent_con.val()){
			agent_name = agent_con.select2('data').text;
		}
		reservedbyname = agent_name;
		var cdr = $('#custom_dr').val();
		var ndr = parseInt(localStorage['dr']) + 1;
		var custom_dr = (cdr) ? cdr : ndr;
		var drnumctr =  custom_dr;
		var pref_dr = (localStorage['pref_dr']) ? localStorage['pref_dr'] : '';
		drnumctr = str_pad('000000',drnumctr,true);
		drnumctr = pref_dr + drnumctr;
		if(!remarks) remarks = "";

		// START TEST
		var data = {
			company: localStorage["company_name"],
			date: output,
			tin: '123213123213123213123123',
			contact: '012391230',
			ctr_no: drnumctr,
			items: item_list,
			sub_total: subtotal,
			vat: vatable,
			total: pagesubtotal,
			remarks: remarks,
			test: '',
			address: '',
			member: member_name
		};

		print_con_data(data);
		//END TEST

	}
	console.log($('#txtLayout').val());
	function print_con_data(data){
		var form = $('#txtLayout').val();
		try{
			form = JSON.parse(form);
			var ret_html = "";
			var prev = false;
			for(var i in form){
				var label = "";
				if(data[form[i].key]){
					if(form[i].type == 'table'){


						if(form[i].style){
							var ob;
							try{
								ob = form[i].style;
								for(var o in ob){
									styles += o +":"+ ob[o] +";";
								}

							}catch(e){

							}
						}
						ret_html += "<table style='margin:0 auto;width:300px;'>";
						var items = data.items;
						for(var arr in items){
							var divs = (form[i].div).split("|");
							for(var j in divs){
								var props = (divs[j]).split(',');
								ret_html += "<tr>";
								for(var p in props){
									ret_html += "<td style='"+styles+"padding:3px;'>"+items[arr][props[p]]+"</td>";
								}
								ret_html += "</tr>";
							}
						}

						ret_html += "</table>";
					} else {
						var styles = "";
						var has_float = false;

						if(form[i].style){
							var ob;
							try{
								ob = form[i].style;
								for(var o in ob){
									var extra = "";
									if(o == "float"){
										has_float = true;
										prev = true;
									}
									styles += o +":"+ ob[o] +";";
								}

							}catch(e){

							}
						}
						if(!has_float && prev){
							ret_html += "<div style='clear:both;'>&nbsp;</div>";
							prev = false;
						}
						if(form[i].label){
							label = form[i].label + " ";
						}
						ret_html += "<div style='"+styles+"'>"+label+data[form[i].key]+"</div>";
					}
				}
			}
			Popup("<div style='width:300px;' >"+ret_html+"</div>");
		}catch(e){

		}
	}
	$('body').on('click','#btnOverPayment',function(){
		localStorage.removeItem('op_payment_cheque');
		localStorage.removeItem('op_payment_credit');
		localStorage.removeItem('op_payment_bt');
		localStorage.removeItem('op_payment_cash');

		op_updateCreditPayment();
		op_updateCashPayment();
		op_updateBankTransferPayment();
		op_updateChequePayment();
		$('#op_label_holder').hide();
		$('#modalOverPayment').modal('show');
	});

	/************************************ over price **********************************/
	function op_updateTotalPayment(){
		var cash = $("#op_cashreceivetext").val();
		if(!cash){
			cash=0;
		}
		var credit_amount = $("#op_hidcreditpayment").val();
		if(!credit_amount){
			credit_amount=0;
		}
		var bt_amount = $("#op_hidbanktransferpayment").val();
		if(!bt_amount){
			bt_amount=0;
		}
		var ck_amount = $("#op_hidchequepayment").val();
		if(!ck_amount){
			ck_amount=0;
		}
		var gtotal = parseFloat(cash) + parseFloat(credit_amount) + parseFloat(bt_amount) + parseFloat(ck_amount);
		$("#op_totalOfAllPayment").html("<strong><span style='font-size:1.2em;' class='text-info' >Total Over Payment: " +gtotal.toFixed(2) + "</span></strong>");

	}
	function op_updateCashPayment(){
		var cash = $("#op_cashreceivetext").val();
		if(!cash){
			cash=0;
		}
		$("#op_totalcashpayment").html(cash);
		op_updateTotalPayment();
	}
	function op_updateCreditPayment(){
		var total = 0;
		if($("#op_credit_table tr").children().length ){
			$("#op_credit_table tr").each(function(index){
				var row = $(this);
				var amount = row.children().eq(1).text();
				total = parseFloat(total) + parseFloat(amount);
			});
		}
		$("#op_totalcreditpayment").html(total);
		$("#op_hidcreditpayment").val(total);
		op_updateTotalPayment();
	}
	function op_updateBankTransferPayment(){
		var total = 0;
		if($("#op_bt_table tr").children().length ){
			$("#op_bt_table tr").each(function(index){
				var row = $(this);
				var amount = row.children().eq(1).text();
				total = parseFloat(total) + parseFloat(amount);
			});
		}
		$("#op_totalbanktransferpayment").html(total);
		$("#op_hidbanktransferpayment").val(total);
		op_updateTotalPayment();
	}
	function op_updateChequePayment(){
		var total = 0;
		if($("#op_ch_table tr").children().length ){
			$("#op_ch_table tr").each(function(index){
				var row = $(this);
				var amount = row.children().eq(2).text();
				total = parseFloat(total) + parseFloat(amount);
			});
		}
		$("#op_totalchequepayment").html(total);
		$("#op_hidchequepayment").val(total);
		op_updateTotalPayment();
	}
	$('#op_cashreceivetext').keyup(function (e) {
		if(isNaN($(this).val())){
			showToast('error','<p>Please Enter Valid Amount</p>','<h3>WARNING!</h3>','toast-bottom-right');
			$(this).val('');
			$(this).focus();
		}
		$("#op_hidcashpayment").val($(this).val());
		op_updateCashPayment();
	});
	$('#op_addcreditcard').click(function(){
		var bl_cardnumber = $('#op_billing_cardnumber').val();
		var bl_bank = $('#op_billing_bankname').val();
		var bl_amount = $('#op_billing_amount').val();
		if(!bl_cardnumber){
			bl_cardnumber ='N/A';
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
		$("#op_credit_table").append("<tr><td>"+bl_cardnumber+"</td><td>"+bl_amount+"</td><td>"+bl_bank+"</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
		$('#op_billing_cardnumber').val('');
		$('#op_billing_bankname').val('');
		$('#op_billing_amount').val('');
		op_updateCreditPayment();
	});
	$('#op_addbanktransfer').click(function(){
		var bt_cardnumber = $('#op_bankfrom_account_number').val();
		var bt_bank = $('#op_bankfrom_name').val();
		var bt_amount = $('#op_bt_amount').val();
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
		$("#op_bt_table").append("<tr><td>"+bt_cardnumber+"</td><td>"+bt_amount+"</td><td>"+bt_bank+"</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
		$('#op_bankfrom_account_number').val('');
		$('#op_bankfrom_name').val('');
		$('#op_bt_amount').val('');
		op_updateBankTransferPayment();
	});
	$('#op_addcheque').click(function(){
		var ch_date = $('#op_ch_date').val();
		var ch_number = $('#op_ch_number').val();
		var ch_amount = $('#op_ch_amount').val();
		var ch_bankname = $('#op_ch_bankname').val();
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
		$("#op_ch_table").append("<tr><td>"+ch_date+"</td><td>"+ch_number+"</td><td>"+ch_amount+"</td><td>"+ch_bankname+"</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
		$('#op_ch_date').val('');
		$('#op_ch_number').val('');
		$('#op_ch_amount').val('');
		$('#op_ch_bankname').val('');
		op_updateChequePayment();
	});
	$('.op_cashreceivecancel').click(function(){
		$('#modalOverPayment').modal("hide");
		$("#op_credit_table").find("tr").remove();
		$("#op_bt_table").find("tr").remove();
		$("#op_ch_table").find("tr").remove();
		$("#over_payment_a :input[type='text']").val('');
		$("#over_payment_b :input[type='text']").val('');
		$("#over_payment_c :input[type='text']").val('');
		$("#over_payment_d :input[type='text']").val('');
		localStorage.removeItem('payment_cheque');
		localStorage.removeItem('payment_credit');
		localStorage.removeItem('payment_bt');
		localStorage.removeItem('payment_cash');
		localStorage.removeItem('payment_con');
		localStorage.removeItem('payment_con_freebies');
		localStorage.removeItem('payment_member_credit');
		localStorage.removeItem('payment_member_deduction');
		localStorage.removeItem('op_payment_cheque');
		localStorage.removeItem('op_payment_credit');
		localStorage.removeItem('op_payment_bt');
		localStorage.removeItem('op_payment_cash');
	});
	$('body').on('click','.op_cashreceiveok',function(){
		op_receiveCash();
	});
	function op_isValidFormCheque(){
		if($("#op_ch_table tr").children().length ){
			var chequeArray = new Array();
			var fn = $("#op_ch_firstname").val();
			var mn = $("#op_ch_middlename").val();
			var ln = $("#op_ch_lastname").val();
			var phone = $("#op_ch_phone").val();

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
			$("#op_ch_table tr").each(function(index){
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
			localStorage['op_payment_cheque'] = JSON.stringify(chequeArray);
			return true;
		}

		return true;
	}

	function op_isValidFormCredit(){
		if($("#op_credit_table tr").children().length ){
			var creditArray = new Array();
			var  fn = $("#op_billing_firstname").val();
			var  mn = $("#op_billing_middlename").val();
			var  ln = $("#op_billing_lastname").val();
			var  comp = $("#op_billing_company").val();
			var  add = $("#op_billing_address").val();
			var  postal = $("#op_billing_postal").val();
			var  phone = $("#op_billing_phone").val();
			var  email = $("#op_billing_email").val();
			var  rem = $("#op_billing_remarks").val();
			// required
			var card_type = $("#op_billing_card_type").val();
			var trace_number = $("#op_billing_trace_number").val();
			var approval_code = $("#op_billing_approval_code").val();
			var date = $("#op_billing_date").val();

			if(!card_type || !trace_number  || !approval_code || !date ){
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
					showToast('error','<p>Phone should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
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
				$("#op_credit_table tr").each(function(index){
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
						remarks:rem,
						card_type:card_type,
						trace_number:trace_number,
						approval_code:approval_code,
						date:date
					}
				});
				localStorage['op_payment_credit'] = JSON.stringify(creditArray);
				return true;
			}

		}
		return true;
	}
	function op_isValidFormBankTransfer(){
		if($("#op_bt_table tr").children().length ){
			var bankTransferArray = new Array();
			var bt_bankto_name = $("#op_bt_bankto_name").val();
			var bt_bankto_account_number = $("#op_bt_bankto_account_number").val();
			var fn = $("#op_bt_firstname").val();
			var mn = $("#op_bt_middlename").val();
			var ln = $("#op_bt_lastname").val();
			var comp = $("#op_bt_company").val();
			var  add = $("#op_bt_address").val();
			var  postal = $("#op_bt_postal").val();
			var  phone = $("#op_bt_phone").val();
			var  date = $("#op_bt_date").val();

			if(!date){
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
				if(mn & !isAlphaNumeric(mn)){
					showToast('error','<p>Middle name should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return false;
				}
				if(ln && !isAlphaNumeric(ln)){
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
				$("#op_bt_table tr").each(function(index){
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
						postal:postal,
						date:date
					}
				});
				localStorage['op_payment_bt'] = JSON.stringify(bankTransferArray);
				return true;
			}
		}
		return true;
	}
	function op_receiveCash(){
		var cash = $("#op_hidcashpayment").val();
		if(!cash) cash = 0;
		var credit = $("#op_hidcreditpayment").val();
		if(!credit) credit = 0;
		var banktransfer = $("#op_hidbanktransferpayment").val();
		if(!banktransfer) banktransfer = 0;
		var cheque = $("#op_hidchequepayment").val();
		if(!cheque) cheque = 0;

		var totalpayment = parseFloat(cash) + parseFloat(credit) + parseFloat(banktransfer) + parseFloat(cheque);



		if(totalpayment){
			if(!op_isValidFormCheque() || !op_isValidFormCredit() || !op_isValidFormBankTransfer() ){
				return;
			}

			var change = 0;
			cash = parseFloat(cash) - parseFloat(change);
			localStorage['op_payment_cash'] = cash;


			var payment_credit;
			var payment_bt;
			var payment_cheque;
			var payment_cash;
			var payment_con_freebies;
			var payment_con;
			var payment_member_credit;
			var payment_member_deduction;

			if(localStorage['op_payment_cash']){
				payment_cash = localStorage['op_payment_cash'];
			}
			if(localStorage['op_payment_credit']){
				payment_credit = localStorage['op_payment_credit'];
			}
			if(localStorage['op_payment_bt']){
				payment_bt = localStorage['op_payment_bt'];
			}
			if(localStorage['op_payment_cheque']){
				payment_cheque = localStorage['op_payment_cheque'];
			}
			$("#op_credit_table").find("tr").remove();
			$("#op_bt_table").find("tr").remove();
			$("#op_ch_table").find("tr").remove();
			$("#over_payment_b :input[type='text']").val('');
			$("#over_payment_c :input[type='text']").val('');
			$("#over_payment_d :input[type='text']").val('');
			$("#over_payment_a :input[type='text']").val('');
			$('#op_label_holder').show();
			$('#op_grandtotalholder').html(number_format(totalpayment,2));
			$('#modalOverPayment').modal("hide");
		}
	}
	$('body').on('click','#use_user_overpayment',function(){
		var over_payment_list = JSON.parse($('#op_member_list').val());
		if(over_payment_list.length > 0){
			$('#use_user_overpayment').show();
		}
		var ret_html = "";

		for(var op in over_payment_list){
			console.log(over_payment_list[op]);
			if(over_payment_list[op].status == 1){ // cash
				ret_html += "<div class='panel panel-default'>";
				ret_html += "<div class='panel-body'>";
				ret_html += "<p>Type: Cash</p>";
				ret_html += "<p>Total: "+ over_payment_list[op].json_data + "</p>";
				ret_html += "<p><input data-status='1' data-id='"+over_payment_list[op].id+"' value='"+over_payment_list[op].id+"' data-total='"+over_payment_list[op].json_data+"' type='checkbox' class='chk_overpayment' > Use Payment</p>";
				ret_html += "</div>";
				ret_html += "</div>";
			} else if(over_payment_list[op].status == 2){ // credit
				ret_html += "<div class='panel panel-default'>";
				ret_html += "<div class='panel-body'>";
				ret_html += "<p>Type: Credit Card</p>";
				var credit_data = JSON.parse(over_payment_list[op].json_data);
				var total_credit = 0;
				for(var cd in credit_data){
					ret_html += "<p>Card: "+credit_data[cd].card_type+"</p>";
					ret_html += "<p>Trance Number: "+credit_data[cd].trace_number+"</p>";
					ret_html += "<p>Date: "+credit_data[cd].date+"</p>";
					ret_html += "<p>Amount: "+credit_data[cd].amount+"</p>";
					ret_html += "<hr>";
					total_credit += parseFloat(total_credit) + parseFloat(credit_data[cd].amount);
				}
				ret_html += "<p>Total: "+total_credit + "</p>";
				ret_html += "<p><input data-json='"+JSON.stringify(over_payment_list[op])+"' value='"+over_payment_list[op].id+"' data-status='2' data-id='"+over_payment_list[op].id+"' data-total='"+total_credit+"' type='checkbox' class='chk_overpayment' > Use Payment</p>";
				ret_html += "</div>";
				ret_html += "</div>";
			}else if(over_payment_list[op].status == 3){ // cheque
				ret_html += "<div class='panel panel-default'>";
				ret_html += "<div class='panel-body'>";
				ret_html += "<p>Type: Check</p>";
				var cheque_data = JSON.parse(over_payment_list[op].json_data);
				var total_cheque = 0;

				for(var cd in cheque_data){
					ret_html += "<p>Ctrl#: "+cheque_data[cd].cheque_number+"</p>";
					ret_html += "<p>Date: "+cheque_data[cd].date+"</p>";
					ret_html += "<p>Amount: "+cheque_data[cd].amount+"</p>";
					ret_html += "<hr>";
					total_cheque = parseFloat(total_cheque) + parseFloat(cheque_data[cd].amount);
				}
				ret_html += "<p>Total: "+total_cheque + "</p>";
				ret_html += "<p><input data-json='"+JSON.stringify(over_payment_list[op])+"' value='"+over_payment_list[op].id+"'  data-status='3' data-id='"+over_payment_list[op].id+"' data-total='"+total_cheque+"' type='checkbox' class='chk_overpayment' > Use Payment</p>";
				ret_html += "</div>";
				ret_html += "</div>";
			}else if(over_payment_list[op].status == 4){ // bt
				ret_html += "<div class='panel panel-default'>";
				ret_html += "<div class='panel-body'>";
				ret_html += "<p>Type: Bank Transfer</p>";
				var bt_data = JSON.parse(over_payment_list[op].json_data);
				var total_bt = 0;
				for(var cd in bt_data){
					ret_html += "<p>Date: "+bt_data[cd].date+"</p>";
					ret_html += "<p>Amount: "+bt_data[cd].amount+"</p>";
				}
				ret_html += "<p>Total: "+total_bt + "</p>";
				ret_html += "<p><input data-status='4' data-id='"+over_payment_list[op].id+"' value='"+over_payment_list[op].id+"' data-total='"+total_bt+"' type='checkbox' class='chk_overpayment' > Use Payment</p>";
				ret_html += "</div>";
				ret_html += "</div>";
			}
		}

		$('#right-pane-container').html(ret_html);
		$('.right-panel-pane').fadeIn(100);
	});
	$('body').on('click','.chk_overpayment',function(){
		var con = $(this);
		var status = con.attr('data-status');
		var total = con.attr('data-total');
		var v = con.is(':checked');
		if(status == 1){ // cash
			var txtcon = $('#cashreceivetext');
			var cur_cash = txtcon.val();
			cur_cash = (cur_cash) ? cur_cash : 0;
			if(v){
				txtcon.val(parseFloat(cur_cash) + parseFloat(total));
				$("#hidcashpayment").val(parseFloat(cur_cash) + parseFloat(total));
			}else{
				txtcon.val(parseFloat(cur_cash) - parseFloat(total));
				$("#hidcashpayment").val(parseFloat(cur_cash) - parseFloat(total));
			}
			updateCashPayment();

		} else if(status == 2){ // credit
			var json = JSON.parse(con.attr('data-json'));
			var credit_data = JSON.parse(json.json_data);
			var billing_card_type = $('#billing_card_type');
			var billing_trace_number = $('#billing_trace_number');
			var billing_approval_code = $('#billing_approval_code');
			var billing_date = $('#billing_date');
			for(var i in credit_data){
				if(v){
					$('#credit_table').append("<tr id='from_user_credit_credit"+credit_data[i].id+"' ><td>"+credit_data[i].credit_number+"</td><td>"+credit_data[i].amount+"</td><td>"+credit_data[i].bank_name+"</td><td></td></tr>");
					billing_card_type.val(credit_data[i].card_type);
					billing_trace_number.val(credit_data[i].trace_number);
					billing_approval_code.val(credit_data[i].approval_code);
					billing_date.val(credit_data[i].date);
				} else {
					$('#from_user_credit_credit'+credit_data[i].id).remove();
				}
			}
			updateCreditPayment();
		} else if(status == 3){ // check
			var json = JSON.parse(con.attr('data-json'));
			var check_data = JSON.parse(json.json_data);
			var ch_firstname = $('#ch_firstname');
			var ch_middlename = $('#ch_middlename');
			var ch_lastname = $('#ch_lastname');
			var ch_phone = $('#ch_phone');
			for(var i in check_data){
				if(v){
					$('#ch_table').append("<tr id='from_user_credit_check"+check_data[i].id+"' ><td>"+check_data[i].date+"</td><td>"+check_data[i].cheque_number+"</td><td>"+check_data[i].amount+"</td><td>"+check_data[i].bank_name+"</td><td></td></tr>");
					ch_firstname.val(check_data[i].firstname);
					ch_middlename.val(check_data[i].middlename);
					ch_lastname.val(check_data[i].lastname);
					ch_phone.val(check_data[i].phone);
				} else {
					$('#from_user_credit_check'+check_data[i].id).remove();
				}
			}
			updateChequePayment();
		}
	});
	$('body').on('click','.btnReuse',function(){
		var id = $(this).attr('data-id');
		var member_id = $('#member_id').val();
		$.ajax({
			url:'../ajax/ajax_sales_query.php',
			type:'POST',
			data: {functionName:'getPrevListItem',id:id},
			success: function(data){
				$(data).prependTo("#cart tbody");
				removeNoItemLabel();
				updatesubtotal();
			},
			error:function(){

			}
		})
	});
	// changechange

	$('body').on('click','#btnAddMoreMemberDeduction',function(){
		var member = $('#member_deduction');
		var member_id = member.val();
		var member_name = member.select2('data').text;
		var member_deduction_amount = $('#member_deduction_amount').val();
		var member_deduction_remarks = $('#member_deduction_remarks').val();
		$('#member_deduction_amount').val('');
		$('#member_deduction_remarks').val('');
		var ret = "<tr data-amount='"+member_deduction_amount+"' ><td>"+member_name+"</td><td>"+member_deduction_amount+"</td><td>"+member_deduction_remarks+"</td><td><span class='glyphicon glyphicon-remove removeItem'></span></td></tr>";
		if(!isValidAmount(member_deduction_amount,true)){
			$('#member_deduction_table > tbody').append(ret);
			updateMemberDeduction();
		} else {
			showToast('error','<p>Invalid payment</p>','<h3>WARNING!</h3>','toast-bottom-right');
		}
	});
});