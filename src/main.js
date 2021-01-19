
import index from "./index.js"

var vm = index.vm;

// watching branch and member select2
vm.$watch('request.for_pickup', function(val) {
	if($('#IS_CEBUHIQ').val() == 1){
		if(val == 1){

			if(vm['request'].price_group_id !=  8){
				vm['request'].price_group_id = '0';
				tempToast('error','Chosen price group is Invalid for order type "For Pick up".','Price Group was RESET');
			}

		} else {

		}
	}
});

$('#branch_id').on('change', function() {
	vm['request'].branch_id = $(this).val();
	vm.checkPendingOrder();
});

vm.$watch('request.branch_id', function(val) {
	$('#branch_id').select2({
		'placeholder': 'From', allowClear: true
	});
});
// watching branch and member select2
$('#branch_id_to').on('change', function() {
	var v = $(this).val()
	vm['request'].branch_id_to = v;
	$.ajax({
		url:'../ajax/ajax_wh_order.php',
		type:'POST',
		dataType:'json',
		data: {functionName:'branchHasMember',branch_id:v},
		success: function(data){

			if(data.member.id){
				vm['request'].member_id = data.member.id;
				$('#member_id').select2('data',{id:data.member.id,text:data.member.name});
			} else {
				//	vm['request'].member_id = 0;
				//	$('#member_id').select2('val',null);
			}
			if(data.branches.length > 0){
				vm['request'].branch_id = 0;
				$('#branch_id').select2('val','null');
				var ret = "<option value=''></option>";
				for(var i in data.branches){
					ret += "<option value='"+data.branches[i].branch_id+"'>"+data.branches[i].branch_name+"</option>";
				}
				$('#branch_id').html(ret);
			}
		},
		error:function(){

		}
	});
});

vm.$watch('request.branch_id_to', function(val) {
	$('#branch_id_to').select2({
		'placeholder': 'To', allowClear: true
	});
});
$('#shipping_company_id').on('change', function() {
	vm['request'].shipping_company_id = $(this).val();
});

vm.$watch('request.shipping_company_id', function(val) {
	$('#shipping_company_id').select2({
		'placeholder': 'Shipping Company', allowClear: true
	});
});
$('#update_shipping_company').on('change', function() {
	vm['order_info'].shipping_company_id = $(this).val();
});

vm.$watch('order_info.shipping_company_id', function(val) {
	$('#update_shipping_company').select2({
		'placeholder': 'Shipping Company', allowClear: true
	});
});
vm.$watch('helper_id', function(val) {
	$('#helper_id').select2({
		'placeholder': 'Select Helper', allowClear: true
	});
});
$('#helper_id').on('change', function() {
	vm.helper_id = $(this).val();
});
$('#member_id').on('change', function() {
	var v = $(this).val();
	vm['request'].member_id = v;
	// ajax call if member has branch
	$.ajax({
		url:'../ajax/ajax_wh_order.php',
		type:'POST',
		dataType:'json',
		data: {functionName:'getOwnedBranch',member_id:v},
		success: function(data){
			var my_branch = data.branches;
			var my_station = data.stations;
			var my_credits = data.credits;

			vm.current_credit_list = my_credits;
			vm.request.price_group_id =data.price_group_id;
			vm.is_hold = data.is_hold;
			vm.member_info.contact_number = data.contact_number;
			vm.member_info.personal_address = data.personal_address;
			vm.member_info.region = data.region;
			vm.member_info.terms = data.terms;
			vm.member_info.credit_limit = data.credit_limit;


			if(my_branch.length > 0){
				var ret = "<option value=''></option>";
				for(var i in my_branch){
					ret += "<option value='"+my_branch[i].id+"'>"+my_branch[i].name+"</option>";
				}
				$('#branch_id_to').html(ret);
			}
			if(my_station.length > 0){
				var ret = "<option value='0'>Choose Station</option>";
				for(var i in my_station){
					ret += "<option value='"+my_station[i].id+"'>"+my_station[i].name+"</option>";
				}
				$('#station_id').html(ret);
				$('#spec_station_id').html(ret);
			} else {

				$('#station_id').html("<option value='0'>No Station</option>");
				$('#spec_station_id').html("<option value='0'>No Station</option>");
			}

		},
		error:function(){

		}
	});

});

vm.$watch('request.member_id', function(val) {
	/*$('#member_id').select2({
		'placeholder' :'Select Member',
		allowClear: true
	});*/
	/*$("#member_id").select2({
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
							text: item.lastname + ", " + item.firstname + " " + item.middlename,
							slug: item.lastname + ", " + item.firstname + " " + item.middlename,
							id: item.id
						}
					})
				};
			}
		}
	});*/
});

vm.$watch('schedule_date', function(val) {
	$('#schedule_date').datepicker({
		autoclose: true
	}).on('changeDate', function(ev) {
		$('#schedule_date').datepicker('hide');
	});
});

$('#schedule_date').datepicker({
	autoclose: true
}).on('changeDate', function(ev) {
	$('#schedule_date').datepicker('hide');
	vm['schedule_date'] = $('#schedule_date').val();
});

$('#custom_date').datepicker({
	autoclose: true
}).on('changeDate', function(ev) {
	$('#custom_date').datepicker('hide');
});
vm.$watch('log_from', function(val) {
	$('#log_from').datepicker({
		autoclose: true
	}).on('changeDate', function(ev) {
		$('#log_from').datepicker('hide');
	});
});
$('#log_from').datepicker({
	autoclose: true
}).on('changeDate', function(ev) {
	$('#log_from').datepicker('hide');
	vm['log_from'] = $('#log_from').val();
});
vm.$watch('log_to', function(val) {
	$('#log_to').datepicker({
		autoclose: true
	}).on('changeDate', function(ev) {
		$('#log_to').datepicker('hide');
	});
});
$('#log_to').datepicker({
	autoclose: true
}).on('changeDate', function(ev) {
	$('#log_to').datepicker('hide');
	vm['log_to'] = $('#log_to').val();
});
vm.$watch('re_schedule_date', function(val) {
	$('#re_schedule_date').datepicker({
		autoclose: true
	}).on('changeDate', function(ev) {
		$('#re_schedule_date').datepicker('hide');
	});
});
$('#re_schedule_date').datepicker({
	autoclose: true
}).on('changeDate', function(ev) {
	$('#re_schedule_date').datepicker('hide');
	vm['re_schedule_date'] = $('#re_schedule_date').val();
});

vm.$watch('warehouse_dt1', function(val) {
	$('#warehouse_dt1').datepicker({
		autoclose: true
	}).on('changeDate', function(ev) {
		$('#warehouse_dt1').datepicker('hide');
	});
});
$('#warehouse_dt1').datepicker({
	autoclose: true
}).on('changeDate', function(ev) {
	$('#warehouse_dt1').datepicker('hide');
	vm['warehouse_dt1'] = $('#warehouse_dt1').val();
});
vm.$watch('warehouse_dt2', function(val) {
	$('#warehouse_dt2').datepicker({
		autoclose: true
	}).on('changeDate', function(ev) {
		$('#warehouse_dt2').datepicker('hide');
	});
});
$('#warehouse_dt2').datepicker({
	autoclose: true
}).on('changeDate', function(ev) {
	$('#warehouse_dt2').datepicker('hide');
	vm['warehouse_dt2'] = $('#warehouse_dt2').val();
});

(function(vm) {

	//getMembers(localStorage['company_id']);
	//getMemberOptList();

	var ajaxOnProgress = false;

	$('body').on('click', '.opennewtabimage', function() {

		var src = $(this).attr('src');
		$.swipebox([{href: src, title: ''}]);

	});

	function getMembersInd(company_id, member_id) {
		$("#activaTab").empty();
		$("#con_member").append("<option></option>");
		$("#con_member_freebies").empty();
		$("#con_member_freebies").append("<option></option>");
		$("#member_credit").empty();
		$("#member_credit").append("<option></option>");
		$("#member_deduction").empty();
		$("#member_deduction").append("<option></option>");
		$.ajax({
			url: "../ajax/ajax_get_members.php",
			type: "POST",
			data: {company_id: company_id, member_id: member_id, type: 1},
			success: function(data) {
				if(data != 0) {
					var mems = JSON.parse(data);
					for(var i in mems) {
						var amt = 0;
						var amt_freebies = 0;
						if(mems[i].amt) {
							var check_not_validyet = 0;
							amt = mems[i].amt;
							if(mems[i].camt) check_not_validyet = mems[i].camt;
							amt = amt - check_not_validyet;
							$("#con_member").append("<option data-con='" + amt + "' value='" + mems[i].id + "'>" + mems[i].lastname + ", " + mems[i].firstname + " " + mems[i].middlename + " (" + amt + ")</option>");
						}
						if(mems[i].freebiesamount) {
							amt_freebies = mems[i].freebiesamount;
						}
						$("#con_member_freebies").append("<option data-con_freebies='" + amt_freebies + "' value='" + mems[i].id + "'>" + mems[i].lastname + ", " + mems[i].firstname + " " + mems[i].middlename + " (" + amt_freebies + ")</option>");
						$("#member_credit").append("<option value='" + mems[i].id + "'>" + mems[i].lastname + ", " + mems[i].firstname + " " + mems[i].middlename + "</option>");
						$("#member_deduction").append("<option value='" + mems[i].id + "'>" + mems[i].lastname + ", " + mems[i].firstname + " " + mems[i].middlename + "</option>");
						$('#consumable_remarks_holder').html( mems[i].consumable_remarks
						)
					}
					$("#con_member_freebies").select2('val', member_id);
					$("#member_credit").select2('val', member_id);
					$("#con_member").select2('val', member_id);
					$("#member_deduction").select2('val', member_id);
					$("#con_member_freebies").attr('disabled', true);
					$("#member_credit").attr('disabled', true);
					$("#con_member").attr('disabled', true);
					$("#member_deduction").attr('disabled', true);
				}
			}
		});
		$.ajax({
			url: '../ajax/ajax_wh_order.php',
			type: 'POST',
			data: {functionName: 'getOverPayment', member_id: member_id},
			success: function(data) {
				$('#over_payment_holder').html(data);
				var over_payment_list = JSON.parse($('#op_member_list').val());
				if(over_payment_list.length > 0) {
					$('#use_user_overpayment').show();
				}
			},
			error: function() {

			}
		});
	}

	function activaTabOpen(tab){
		$('.nav-tabs a[href="#' + tab + '"]').tab('show');
	}

	function getMembers(company_id) {
		$.ajax({
			url: "../ajax/ajax_get_members.php",
			type: "POST",
			data: {company_id: company_id, type: 1},
			success: function(data) {
				if(data != 0) {
					localStorage['members'] = data;
				} else {
					localStorage.removeItem('members');
				}
			}
		});
	}

	function getMemberOptList() {
		if(localStorage['members']) {
			var mems = JSON.parse(localStorage['members']);
			$("#con_member").empty();
			$("#con_member").append("<option></option>");
			$("#con_member_freebies").empty();
			$("#con_member_freebies").append("<option></option>");
			$("#member_credit").empty();
			$("#member_credit").append("<option></option>");
			for(var i in mems) {
				var amt = 0;
				var amt_freebies = 0;

				if(mems[i].amt) {
					var check_not_validyet = 0;
					amt = mems[i].amt;
					if(mems[i].camt) check_not_validyet = mems[i].camt;
					amt = amt - check_not_validyet;
					$("#con_member").append("<option data-con='" + amt + "' value='" + mems[i].id + "'>" + mems[i].lastname + ", " + mems[i].firstname + " " + mems[i].middlename + " (" + amt + ")</option>");
				}
				if(mems[i].freebiesamount) {
					amt_freebies = mems[i].freebiesamount;
				}
				$("#con_member_freebies").append("<option data-con_freebies='" + amt_freebies + "' value='" + mems[i].id + "'>" + mems[i].lastname + ", " + mems[i].firstname + " " + mems[i].middlename + " (" + amt_freebies + ")</option>");
				$("#member_credit").append("<option value='" + mems[i].id + "'>" + mems[i].lastname + ", " + mems[i].firstname + " " + mems[i].middlename + "</option>");

			}
		}
	}

	$("#con_member").select2({
		placeholder: 'Choose member name...', allowClear: true
	}).on('select2-open', function() {

	}).on("select2-close", function(e) {
		// fired to the original element when the dropdown closes
		setTimeout(function() {
			$('.select2-container-active').removeClass('select2-container-active');
			$(':focus').blur();
		}, 100);
	});

	$("#con_member_freebies").select2({
		placeholder: 'Choose member name...', allowClear: true
	}).on('select2-open', function() {

	}).on("select2-close", function(e) {
		// fired to the original element when the dropdown closes
		setTimeout(function() {
			$('.select2-container-active').removeClass('select2-container-active');
			$(':focus').blur();
		}, 100);
	});


	$("#member_credit").select2({
		placeholder: 'Choose member name...', allowClear: true
	});
	$("#member_deduction").select2({
		placeholder: 'Choose member name...', allowClear: true
	});


	function receiveCash() {
		var cashreceivebtn = $('.cashreceiveok');
		var cashreceiveoldval = cashreceivebtn.html();
		cashreceivebtn.attr('disabled', true);
		cashreceivebtn.html('Loading...');

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
		var member_deduction_amount = $("#hidmemberdeduction").val();
		if(!member_deduction_amount) member_deduction_amount = 0;
		if(!member_credit_amount) member_credit_amount = 0;
		var member_credit_cod = $('#member_credit_cod').is(':checked');
		member_credit_cod = (member_credit_cod) ? 1 : 0;
		var totalpayment = parseFloat(cash) + parseFloat(credit) + parseFloat(banktransfer) + parseFloat(cheque) + parseFloat(con_amount) + parseFloat(con_amount_freebies) + parseFloat(member_credit_amount) + parseFloat(member_deduction_amount);
		var grandtotal = parseFloat($("#hidamountdue").val());


		totalpayment= number_format(totalpayment,2,'.','');
		grandtotal= number_format(grandtotal,2,'.','');

		if(parseFloat(totalpayment) < 0 || parseFloat(totalpayment) < parseFloat(grandtotal)) {
			cashHolderComputation(0, 0);
			showToast('error', '<p>Invalid payment s</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			cashreceivebtn.attr('disabled', false);
			cashreceivebtn.html(cashreceiveoldval);
			return;
		} else {
			if(!isValidFormCheque() || !isValidFormCredit() || !isValidFormBankTransfer() || !isValidFormDeduction()) {
				cashreceivebtn.attr('disabled', false);
				cashreceivebtn.html(cashreceiveoldval);
				return;
			}

			var change = parseFloat(totalpayment) - parseFloat(grandtotal);
			cash = parseFloat(cash) - parseFloat(change);
			localStorage['payment_cash'] = cash;
			localStorage['payment_con'] = con_amount;
			localStorage['payment_con_freebies'] = con_amount_freebies;
			localStorage['payment_member_credit'] = member_credit_amount;

			alertify.confirm('Are you sure you want to submit this payment?', function(e) {
				if(e) {
					// ajax request payment
					var payment_credit;
					var payment_bt;
					var payment_cheque;
					var payment_cash;
					var payment_con_freebies;
					var payment_con;
					var payment_member_credit;
					var payment_member_deduction;
					var member_deduction_remarks;
					var order_id = $('#payment_order_id').val();
					if(localStorage['payment_cash']) {
						payment_cash = localStorage['payment_cash'];
					}
					if(localStorage['payment_con']) {
						payment_con = localStorage['payment_con'];
					}
					if(localStorage['payment_con_freebies']) {
						payment_con_freebies = localStorage['payment_con_freebies'];
					}
					if(localStorage['payment_member_credit']) {
						payment_member_credit = localStorage['payment_member_credit'];
					}
					if(localStorage['payment_credit']) {
						payment_credit = localStorage['payment_credit'];
					}
					if(localStorage['payment_bt']) {
						payment_bt = localStorage['payment_bt'];
					}
					if(localStorage['payment_cheque']) {
						payment_cheque = localStorage['payment_cheque'];
					}
					if(localStorage['payment_member_deduction']) {
						payment_member_deduction = localStorage['payment_member_deduction'];

					}
					if(ajaxOnProgress) {
						return;
					}
					ajaxOnProgress = true;
					var arr_op_ids = [];
					$('input:checkbox.chk_overpayment').each(function() {
						var op_chk = $(this);
						if(op_chk.is(":checked")) {
							var amount_used = op_chk.prev().val();
							arr_op_ids.push({id: op_chk.val(),amount:amount_used});
						}


					});

					$.ajax({
						url: '../ajax/ajax_query2.php', type: 'POST', data: {
							functionName: 'sendPaymentWh',
							payment_credit: payment_credit,
							payment_bt: payment_bt,
							payment_cheque: payment_cheque,
							payment_cash: payment_cash,
							payment_con: payment_con,
							payment_con_freebies: payment_con_freebies,
							payment_member_credit: payment_member_credit,
							member_credit_cod: member_credit_cod,
							payment_member_deduction: payment_member_deduction,
							order_id: order_id,
							override_payment_date:vm.override_payment_date,
							arr_op_ids: JSON.stringify(arr_op_ids),
							totalpayment: totalpayment,
							terminal_id: localStorage['terminal_id']
						}, success: function(data) {
							ajaxOnProgress = false;
							tempToast('info', "<p>" + data + "</p>", "<h4>Information!</h4>");
							cashreceivebtn.attr('disabled', false);
							cashreceivebtn.html(cashreceiveoldval);
							vm.printGroup(order_id);
							vm.fetchedOrder(1);
							localStorage.removeItem('payment_cheque');
							localStorage.removeItem('payment_credit');
							localStorage.removeItem('payment_bt');
							localStorage.removeItem('payment_cash');
							localStorage.removeItem('payment_con');
							localStorage.removeItem('payment_con_freebies');
							localStorage.removeItem('payment_member_credit');
							localStorage.removeItem('payment_member_deduction');
							$('#hidmembercredit').val(0);
							$('#hidmemberdeduction').val(0);
							$('#hidcashpayment').val(0);
							$('#hidcreditpayment').val(0);
							$('#hidbanktransferpayment').val(0);
							$('#hidchequepayment').val(0);
							$('#hidconsumablepayment').val(0);
							$('#hidconsumablepaymentfreebies').val(0);
							vm.override_payment_date = '';
							$('#chkInvoice').prop('checked',false);
							$('#chkDr').prop('checked',false);
							$('#chkPr').prop('checked',false);
							$('#chkSv').prop('checked',false);
						}, error: function() {
							ajaxOnProgress = false;
							tempToast('error', "<p>Error Occur. Please try again.</p>", "<h4>Error!</h4>");
							cashreceivebtn.attr('disabled', false);
							cashreceivebtn.html(cashreceiveoldval);
							vm.fetchedOrder(1);
							localStorage.removeItem('payment_cheque');
							localStorage.removeItem('payment_credit');
							localStorage.removeItem('payment_bt');
							localStorage.removeItem('payment_cash');
							localStorage.removeItem('payment_con');
							localStorage.removeItem('payment_con_freebies');
							localStorage.removeItem('payment_member_credit');
							localStorage.removeItem('payment_member_deduction');
							$('#hidmembercredit').val(0);
							$('#hidmemberdeduction').val(0);
							$('#hidcashpayment').val(0);
							$('#hidcreditpayment').val(0);
							$('#hidbanktransferpayment').val(0);
							$('#hidchequepayment').val(0);
							$('#hidconsumablepayment').val(0);
							$('#hidconsumablepaymentfreebies').val(0);
						}
					});
					$("#credit_table").find("tr").remove();
					$("#bt_table").find("tr").remove();
					$("#ch_table").find("tr").remove();
					$("#tab_d :input[type='text']").val('');
					$("#tab_c :input[type='text']").val('');
					$("#tab_b :input[type='text']").val('');
					$("#tab_a :input[type='text']").val('');
					$('#getpricemodal').modal("hide");
				} else {
					cashreceivebtn.attr('disabled', false);
					cashreceivebtn.html(cashreceiveoldval);
				}
			});
		}
	}

	$('.cashreceivecancel').click(function() {
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
	});

	$('body').on('click', '.cashreceiveok', function() {
		receiveCash();
	});

	$('#ch_date').datepicker({
		autoclose: true
	}).on('changeDate', function(ev) {
		$('#ch_date').datepicker('hide');
	});

	$('#billing_date').datepicker({
		autoclose: true
	}).on('changeDate', function(ev) {
		$('#billing_date').datepicker('hide');
	});
	$('#bt_date').datepicker({
		autoclose: true
	}).on('changeDate', function(ev) {
		$('#bt_date').datepicker('hide');
	});
	function cashHolderComputation(cash, change) {
		$('#cashreceiveholder').empty();
		$('#changeholder').empty();
		$('#cashreceiveholder').append(number_format(cash, 2));
		$('#changeholder').append(number_format(change, 2));
	}

	function updateTotalPayment() {
		var cash = $("#cashreceivetext").val();
		if(!cash) {
			cash = 0;
		}
		var con_amount = $("#con_amount").val();
		if(!con_amount) {
			con_amount = 0;
		}
		var con_amount_freebies = $("#con_amount_freebies").val();
		if(!con_amount_freebies) {
			con_amount_freebies = 0;
		}
		var member_credit_amount = $("#member_credit_amount").val();
		if(!member_credit_amount) {
			member_credit_amount = 0;
		}
		var member_deduction_amount = $("#hidmemberdeduction").val();
		if(!member_deduction_amount) {
			member_deduction_amount = 0;
		}
		var credit_amount = $("#hidcreditpayment").val();
		if(!credit_amount) {
			credit_amount = 0;
		}
		var bt_amount = $("#hidbanktransferpayment").val();
		if(!bt_amount) {
			bt_amount = 0;
		}
		var ck_amount = $("#hidchequepayment").val();
		if(!ck_amount) {
			ck_amount = 0;
		}
		var gtotal = parseFloat(cash) + parseFloat(con_amount) + parseFloat(con_amount_freebies) + parseFloat(member_credit_amount) + parseFloat(credit_amount) + parseFloat(bt_amount) + parseFloat(ck_amount) + parseFloat(member_deduction_amount);
		$("#totalOfAllPayment").html("<strong><span style='font-size:1.2em;' class='text-info' >Total Payment: " + gtotal.toFixed(2) + "</span></strong>");

	}

	function updateCashPayment() {
		var cash = $("#cashreceivetext").val();
		if(!cash) {
			cash = 0;
		}
		$("#totalcashpayment").html(cash);
		updateTotalPayment();
	}

	function updateConPayment() {
		var con_amount = $("#con_amount").val();
		if(!con_amount) {
			con_amount = 0;
		}
		$("#totalconsumablepayment").html(con_amount);
		updateTotalPayment();
	}

	function updateMemberCredit() {
		var member_credit_amount = $("#member_credit_amount").val();
		if(!member_credit_amount) {
			member_credit_amount = 0;
		}
		$("#totalmembercredit").html(member_credit_amount);
		updateTotalPayment();
	}

	function updateMemberDeduction() {
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

	function updateConPaymentFreebies() {
		var con_amount_freebies = $("#con_amount_freebies").val();
		if(!con_amount_freebies) {
			con_amount_freebies = 0;
		}

		$("#totalconsumablepaymentfreebies").html(con_amount_freebies);
		updateTotalPayment();
	}

	function updateCreditPayment() {
		var total = 0;
		if($("#credit_table tr").children().length) {
			$("#credit_table tr").each(function(index) {
				var row = $(this);
				var amount = row.children().eq(1).text();
				total = parseFloat(total) + parseFloat(amount);
			});
		}
		$("#totalcreditpayment").html(total);
		$("#hidcreditpayment").val(total);
		updateTotalPayment();
	}

	function updateBankTransferPayment() {
		var total = 0;
		if($("#bt_table tr").children().length) {
			$("#bt_table tr").each(function(index) {
				var row = $(this);
				var amount = row.children().eq(1).text();
				total = parseFloat(total) + parseFloat(amount);
			});
		}
		$("#totalbanktransferpayment").html(total);
		$("#hidbanktransferpayment").val(total);
		updateTotalPayment();
	}

	function updateChequePayment() {
		var total = 0;
		if($("#ch_table tr").children().length) {
			$("#ch_table tr").each(function(index) {
				var row = $(this);
				var amount = row.children().eq(2).text();
				total = parseFloat(total) + parseFloat(amount);
			});
		}
		$("#totalchequepayment").html(total);
		$("#hidchequepayment").val(total);
		updateTotalPayment();
	}

	function hasItemCreditValidation(elem) {
		if(!$("#credit_table tr").children().length) {
			showToast('error', '<p>Please Add Credit Card First. </p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			elem.val('');
		}
	}

	$("#billing_firstname, #billing_middlename, #billing_lastname, #billing_company, #billing_address, #billing_postal,#billing_phone,#billing_email,#billing_remarks").keyup(function() {
		hasItemCreditValidation($(this));
	});
	function hasItemBTValidation(elem) {
		if(!$("#bt_table tr").children().length) {
			showToast('error', '<p>Please Add Bank Transfer Data First. </p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			elem.val('');
		}
	}

	$("#bt_bankto_name, #bt_bankto_account_number, #bt_firstname, #bt_middlename, #bt_lastname, #bt_company,#bt_address,#bt_postal,#bt_phone").keyup(function() {
		hasItemBTValidation($(this));
	});
	function hasItemChequeValidation(elem) {
		if(!$("#ch_table tr").children().length) {
			showToast('error', '<p>Please Add Cheque Data First. </p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			elem.val('');
		}
	}

	$("#ch_firstname, #ch_middlename, #ch_lastname, #ch_phone").keyup(function() {
		hasItemChequeValidation($(this));
	});

	function isValidAmount(a, addme) {
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

		var currentNotCash = parseFloat(credit) + parseFloat(banktransfer) + parseFloat(cheque) + parseFloat(con_amount) + parseFloat(con_amount_freebies) + parseFloat(member_credit_amount) + parseFloat(member_deduction_amount);
		if(addme) {
			currentNotCash = parseFloat(currentNotCash) + parseFloat(a);
		}
		if(parseFloat(currentNotCash).toFixed(2) > parseFloat(grandtotal)) {
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
	function isValidFormCheque() {
		if($("#ch_table tr").children().length) {
			var chequeArray = new Array();
			var fn = $("#ch_firstname").val();
			var mn = $("#ch_middlename").val();
			var ln = $("#ch_lastname").val();
			var phone = $("#ch_phone").val();

			if(fn && !isAlphaNumeric(fn)) {
				showToast('error', '<p>First name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
				return false;
			}
			if(mn && !isAlphaNumeric(mn)) {
				showToast('error', '<p>Middle name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
				return false;
			}
			if(ln && !isAlphaNumeric(ln)) {
				showToast('error', '<p>Last name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
				return false;
			}
			if(phone && !isAlphaNumeric(phone)) {
				showToast('error', '<p>Phone should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
				return false;
			}
			$("#ch_table tr").each(function(index) {
				var row = $(this);
				chequeArray[index] = {
					date: row.children().eq(0).text(),
					cheque_number: row.children().eq(1).text(),
					amount: row.children().eq(2).text(),
					bank_name: row.children().eq(3).text(),
					firstname: fn,
					lastname: ln,
					middlename: mn,
					phone: phone
				}
			});
			localStorage['payment_cheque'] = JSON.stringify(chequeArray);
			return true;
		}

		return true;
	}

	function isValidFormCredit() {
		if($("#credit_table tr").children().length) {
			var creditArray = new Array();
			var fn = $("#billing_firstname").val();
			var mn = $("#billing_middlename").val();
			var ln = $("#billing_lastname").val();
			var comp = $("#billing_company").val();
			var add = $("#billing_address").val();
			var postal = $("#billing_postal").val();
			var phone = $("#billing_phone").val();
			var email = $("#billing_email").val();
			var rem = $("#billing_remarks").val();
			// required
			/*var card_type = $("#billing_card_type").val();
			var trace_number = $("#billing_trace_number").val();
			var approval_code = $("#billing_approval_code").val();
			var date = $("#billing_date").val();*/

			if(false) {
				showToast('error', '<p>Please Complete Credit Card billing form. </p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
				return false;
			} else {
				if(ln && !isAlphaNumeric(ln)) {
					showToast('error', '<p>Last name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(fn && !isAlphaNumeric(fn)) {
					showToast('error', '<p>First name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(mn && !isAlphaNumeric(mn)) {
					showToast('error', '<p>Middle name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(comp && !isAlphaNumeric(comp)) {
					showToast('error', '<p>Company should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(add && !isAlphaNumeric(add)) {
					showToast('error', '<p>Address should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(postal && !isNumeric(postal)) {
					showToast('error', '<p>Postal should be numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(phone && !isAlphaNumeric(phone)) {
					showToast('error', '<p>Phone should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(email && !isEmail(email)) {
					showToast('error', '<p>Email should be valid email address</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(rem && !isAlphaNumeric(rem)) {
					showToast('error', '<p>Remarks should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
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

	function isValidFormBankTransfer() {
		if($("#bt_table tr").children().length) {
			var bankTransferArray = new Array();
			var bt_bankto_name = $("#bt_bankto_name").val();
			var bt_bankto_account_number = $("#bt_bankto_account_number").val();
			var fn = $("#bt_firstname").val();
			var mn = $("#bt_middlename").val();
			var ln = $("#bt_lastname").val();
			var comp = $("#bt_company").val();
			var add = $("#bt_address").val();
			var postal = $("#bt_postal").val();
			var phone = $("#bt_phone").val();
			var date = $("#bt_date").val();

			if(!date) {
				showToast('error', '<p>Please Complete Bank Transfer  billing form. </p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
				return false;
			} else {
				if(bt_bankto_name && !isAlphaNumeric(bt_bankto_name)) {
					showToast('error', '<p>Bank name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(bt_bankto_account_number && !isAlphaNumeric(bt_bankto_account_number)) {
					showToast('error', '<p>Bank account number should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(fn && !isAlphaNumeric(fn)) {
					showToast('error', '<p>First name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(mn & !isAlphaNumeric(mn)) {
					showToast('error', '<p>Middle name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(ln && !isAlphaNumeric(ln)) {
					showToast('error', '<p>Last name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(comp && !isAlphaNumeric(comp)) {
					showToast('error', '<p>Company should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(add && !isAlphaNumeric(add)) {
					showToast('error', '<p>Address should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(postal && !isNumeric(postal)) {
					showToast('error', '<p>Phone should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(phone && !isAlphaNumeric(phone)) {
					showToast('error', '<p>Phone should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				$("#bt_table tr").each(function(index) {
					var row = $(this);
					bankTransferArray[index] = {
						credit_number: row.children().eq(0).text(),
						amount: row.children().eq(1).text(),
						bank_name: row.children().eq(2).text(),
						bt_bankto_name: bt_bankto_name,
						bt_bankto_account_number: bt_bankto_account_number,
						firstname: fn,
						lastname: ln,
						middlename: mn,
						phone: phone,
						comp: comp,
						add: add,
						postal: postal,
						date: date
					}
				});
				localStorage['payment_bt'] = JSON.stringify(bankTransferArray);
				return true;
			}
		}
		return true;
	}

	function isAlphaNumeric(str) {
		var rexp = /^[\w\-\s\.,??]+$/
		if(rexp.test(str)) {
			return true;
		} else {
			return false;
		}
	}

	function validateDate(testdate) {
		var date_regex = /^(0[1-9]|1[0-2])\/(0[1-9]|1\d|2\d|3[01])\/(19|20)\d{2}$/
		return date_regex.test(testdate);
	}

	function isNumeric(str) {
		var rexp = /^[0-9]+$/
		if(rexp.test(str)) {
			return true;
		} else {
			return false;
		}
	}

	function isEmail(str) {
		var rexp = /^[\w\.-_\+]+@[\w-]+(\.\w{2,3})+$/
		if(rexp.test(str)) {
			return true;
		} else {
			return false;
		}
	}

	function showpricemodal(totalforfreebies, grandtotal) {

		if(!totalforfreebies) {
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
		localStorage.removeItem('payment_member_deduction');

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

		$('#hidcashpayment').val(0);
		$('#hidcreditpayment').val(0);
		$('#hidbanktransferpayment').val(0);
		$('#hidchequepayment').val(0);
		$('#hidconsumablepayment').val(0);
		$('#hidconsumablepaymentfreebies').val(0);
		$('#hidmembercredit').val(0);
		$('#hidmemberdeduction').val(0);
		$('#hidTotalOfAllPayment').val(0);
		$('#cashreceivetext').val('');

		updateCreditPayment();
		updateCashPayment();
		updateBankTransferPayment();
		updateChequePayment();
		updateConPayment();
		updateConPaymentFreebies();
		updateMemberCredit();
		updateMemberDeduction();
		$("#amountdue").html("<span style='font-size:1.2em;' class='text-info'><strong> Amount Due: " + grandtotal + "</strong></span>");
		$("#hidamountdue").val(replaceAll(grandtotal, ',', ''));
		$("#getpricemodal").modal("show");

		setTimeout(function() {
			$('#cashreceivetext').focus()
		}, 500);
	}

	$('#cashreceivetext').keypress(function(e) {
		var key = e.which;
		if(key == 13)  // the enter key code
		{
			receiveCash();
			$('#cashreceivetext').val('');
			$('#getpricemodal').modal("hide");
		}

	});

	$('#cashreceivetext').keyup(function(e) {
		if(isNaN($(this).val())) {
			showToast('error', '<p>Please Enter Valid Amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			$(this).val('');
			$(this).focus();
		}
		$("#hidcashpayment").val($(this).val());
		updateCashPayment();
	});
	$('#addcreditcard').click(function() {
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
		if(!bl_amount) {
			showToast('error', '<p>Please indicate amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(isNaN(bl_amount)) {
			showToast('error', '<p>Please indicate a valid amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(!bl_bank) {
			showToast('error', '<p>Please indicate bank name</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(isValidAmount(bl_amount, true)) {
			showToast('error', '<p>Your payment exceeds to amount due.</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
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

	$('body').on('click', '.removeItem', function() {
		var trid = $(this).parents('tr').remove();
		updateCreditPayment();
		updateCashPayment();
		updateConPayment();
		updateBankTransferPayment();
		updateChequePayment();
	});
	$('#addbanktransfer').click(function() {
		var bt_cardnumber = $('#bankfrom_account_number').val();
		var bt_bank = $('#bankfrom_name').val();
		var bt_amount = $('#bt_amount').val();
		if(!bt_cardnumber) {
			showToast('error', '<p>Please indicate card number</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(!bt_amount) {
			showToast('error', '<p>Please indicate amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(isNaN(bt_amount)) {
			showToast('error', '<p>Please indicate a valid amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(parseFloat(bt_amount) < 1) {
			showToast('error', '<p>Amount should be greater than Zero</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(!bt_bank) {
			showToast('error', '<p>Please indicate bank name</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(isValidAmount(bt_amount, true)) {
			showToast('error', '<p>Your payment exceeds to amount due.</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		$("#bt_table").append("<tr><td>" + bt_cardnumber + "</td><td>" + bt_amount + "</td><td>" + bt_bank + "</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
		$('#bankfrom_account_number').val('');
		$('#bankfrom_name').val('');
		$('#bt_amount').val('');
		updateBankTransferPayment();
	});
	$('#addcheque').click(function() {
		var ch_date = $('#ch_date').val();
		var ch_number = $('#ch_number').val();
		var ch_amount = $('#ch_amount').val();
		var ch_bankname = $('#ch_bankname').val();
		if(!ch_date) {
			showToast('error', '<p>Please indicate date</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(!ch_number) {
			showToast('error', '<p>Please indicate card number</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(!ch_amount) {
			showToast('error', '<p>Please indicate amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(!validateDate(ch_date)) {
			showToast('error', '<p>Invalid Date Format. It should be mm/dd/yyyy (Ex. 01/01/2014) </p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(isNaN(ch_amount)) {
			showToast('error', '<p>Please indicate a valid amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(parseFloat(ch_amount) < 1) {
			showToast('error', '<p>Amount should be greater than Zero</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(!ch_bankname) {
			showToast('error', '<p>Please indicate bank name</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(isValidAmount(ch_amount, true)) {
			showToast('error', '<p>Your payment exceeds to amount due.</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		$("#ch_table").append("<tr><td>" + ch_date + "</td><td>" + ch_number + "</td><td>" + ch_amount + "</td><td>" + ch_bankname + "</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
		$('#ch_date').val('');
		$('#ch_number').val('');
		$('#ch_amount').val('');
		$('#ch_bankname').val('');
		updateChequePayment();
	});
	$('#con_amount_freebies').keyup(function(e) {

		if(!($('#con_member_freebies').val())) {
			showToast('error', '<p>Please Choose member first</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		var validamt = $('#con_member_freebies option:selected').attr('data-con_freebies');
		var cartfreebies = parseFloat(localStorage['totalforfreebies']);
		cartfreebies = 10000000;
		if(parseFloat($(this).val()) > cartfreebies) {
			showToast('error', '<p>Invalid freebies amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			$(this).focus();
			$(this).val('');
		}

		if(parseFloat(validamt) < parseFloat($(this).val())) {
			showToast('error', '<p>Invalid freebies amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			$(this).focus();
			$(this).val('');
		}
		if(isNaN($(this).val())) {
			showToast('error', '<p>Please Enter Valid Amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			$(this).val('');
			$(this).focus();
		}
		$("#hidconsumablepaymentfreebies").val($(this).val());
		if(isValidAmount($(this).val(), false)) {
			showToast('error', '<p>Your payment exceeds to amount due.</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			$(this).val('');
		}
		$("#hidconsumablepaymentfreebies").val($(this).val());
		updateConPaymentFreebies();
	});
	$('#member_credit_amount').keyup(function(e) {

		if(!($('#member_credit').val())) {
			showToast('error', '<p>Please Choose member first</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			$(this).val('');
			return;
		}


		if(isNaN($(this).val())) {
			showToast('error', '<p>Please Enter Valid Amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			$(this).val('');
			$(this).focus();
		}
		$("#hidmembercredit").val($(this).val());
		if(isValidAmount($(this).val(), false)) {
			showToast('error', '<p>Your payment exceeds to amount due.</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			$(this).val('');
		}
		$("#hidmembercredit").val($(this).val());
		updateMemberCredit();
	});
	$('#member_deduction_amount').keyup(function(e) {

		if(!($('#member_deduction').val())) {
			showToast('error', '<p>Please Choose member first</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			$(this).val('');
			return;
		}

		if(isNaN($(this).val())) {
			showToast('error', '<p>Please Enter Valid Amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			$(this).val('');
			$(this).focus();
		}

		updateMemberDeduction();
	});
	$('#con_amount').keyup(function(e) {

		if(!($('#con_member').val())) {
			showToast('error', '<p>Please Choose member first</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(localStorage['hasType2'] == 1) {
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
		if(parseFloat(validamt) < parseFloat($(this).val())) {
			showToast('error', '<p>Invalid consumable amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			$(this).focus();
			$(this).val('');
		}
		if(isNaN($(this).val())) {
			showToast('error', '<p>Please Enter Valid Amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			$(this).val('');
			$(this).focus();
		}
		$("#hidconsumablepayment").val($(this).val());
		if(isValidAmount($(this).val(), false)) {
			showToast('error', '<p>Your payment exceeds to amount due.</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			$(this).val('');
		}
		$("#hidconsumablepayment").val($(this).val());
		updateConPayment();
	});
	$('body').on('click', '.btnPayment', function(e) {
		e.preventDefault();
		var row = $(this).parents('tr');
		var total = row.attr('data-total');
		var order_id = row.attr('data-id');
		var member_id = $(this).attr('data-member_id');
		var for_pickup = $(this).attr('data-for_pick_up');

		getMembersInd(localStorage['company_id'], member_id);

		$('#payment_order_id').val(order_id);
		if($('#AUTO_MEMBER_CREDIT').val() == 1){
			activaTabOpen('tab_g');
		}
		if($('#IS_CEBUHIQ').val() == 1){
			if(for_pickup == 0){

				$('.tab_nav_a ').hide(1);
				$('.tab_nav_b ').hide(2);
				$('.tab_nav_c ').hide(3);
				$('.tab_nav_e ').hide(4);
				$('.tab_nav_f ').hide(5);

			} else if (for_pickup == 1){

				$('.tab_nav_b ').hide(1);
				$('.tab_nav_c ').hide(2);
				$('.tab_nav_e ').hide(3);
				$('.tab_nav_f ').hide(4);

			}

		}

		showpricemodal('0', total.toString());
	});

	/************* OVER PAYMENT ******************/
	$('body').on('click', '#btnOverPayment', function() {
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
	function op_updateTotalPayment() {
		var cash = $("#op_cashreceivetext").val();
		if(!cash) {
			cash = 0;
		}
		var credit_amount = $("#op_hidcreditpayment").val();
		if(!credit_amount) {
			credit_amount = 0;
		}
		var bt_amount = $("#op_hidbanktransferpayment").val();
		if(!bt_amount) {
			bt_amount = 0;
		}
		var ck_amount = $("#op_hidchequepayment").val();
		if(!ck_amount) {
			ck_amount = 0;
		}
		var gtotal = parseFloat(cash) + parseFloat(credit_amount) + parseFloat(bt_amount) + parseFloat(ck_amount);
		$("#op_totalOfAllPayment").html("<strong><span style='font-size:1.2em;' class='text-info' >Total Over Payment: " + gtotal.toFixed(2) + "</span></strong>");

	}

	function op_updateCashPayment() {
		var cash = $("#op_cashreceivetext").val();
		if(!cash) {
			cash = 0;
		}
		$("#op_totalcashpayment").html(cash);
		op_updateTotalPayment();
	}

	function op_updateCreditPayment() {
		var total = 0;
		if($("#op_credit_table tr").children().length) {
			$("#op_credit_table tr").each(function(index) {
				var row = $(this);
				var amount = row.children().eq(1).text();
				total = parseFloat(total) + parseFloat(amount);
			});
		}
		$("#op_totalcreditpayment").html(total);
		$("#op_hidcreditpayment").val(total);
		op_updateTotalPayment();
	}

	function op_updateBankTransferPayment() {
		var total = 0;
		if($("#op_bt_table tr").children().length) {
			$("#op_bt_table tr").each(function(index) {
				var row = $(this);
				var amount = row.children().eq(1).text();
				total = parseFloat(total) + parseFloat(amount);
			});
		}
		$("#op_totalbanktransferpayment").html(total);
		$("#op_hidbanktransferpayment").val(total);
		op_updateTotalPayment();
	}

	function op_updateChequePayment() {
		var total = 0;
		if($("#op_ch_table tr").children().length) {
			$("#op_ch_table tr").each(function(index) {
				var row = $(this);
				var amount = row.children().eq(2).text();
				total = parseFloat(total) + parseFloat(amount);
			});
		}
		$("#op_totalchequepayment").html(total);
		$("#op_hidchequepayment").val(total);
		op_updateTotalPayment();
	}

	$('#op_cashreceivetext').keyup(function(e) {
		if(isNaN($(this).val())) {
			showToast('error', '<p>Please Enter Valid Amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			$(this).val('');
			$(this).focus();
		}
		$("#op_hidcashpayment").val($(this).val());
		op_updateCashPayment();
	});
	$('#op_addcreditcard').click(function() {
		var bl_cardnumber = $('#op_billing_cardnumber').val();
		var bl_bank = $('#op_billing_bankname').val();
		var bl_amount = $('#op_billing_amount').val();
		if(!bl_cardnumber) {
			bl_cardnumber = 'N/A';
		}
		if(!bl_amount) {
			showToast('error', '<p>Please indicate amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(isNaN(bl_amount)) {
			showToast('error', '<p>Please indicate a valid amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(!bl_bank) {
			showToast('error', '<p>Please indicate bank name</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(isValidAmount(bl_amount, true)) {
			showToast('error', '<p>Your payment exceeds to amount due.</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		$("#op_credit_table").append("<tr><td>" + bl_cardnumber + "</td><td>" + bl_amount + "</td><td>" + bl_bank + "</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
		$('#op_billing_cardnumber').val('');
		$('#op_billing_bankname').val('');
		$('#op_billing_amount').val('');
		op_updateCreditPayment();
	});
	$('#op_addbanktransfer').click(function() {
		var bt_cardnumber = $('#op_bankfrom_account_number').val();
		var bt_bank = $('#op_bankfrom_name').val();
		var bt_amount = $('#op_bt_amount').val();
		if(!bt_cardnumber) {
			showToast('error', '<p>Please indicate card number</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(!bt_amount) {
			showToast('error', '<p>Please indicate amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(isNaN(bt_amount)) {
			showToast('error', '<p>Please indicate a valid amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(parseFloat(bt_amount) < 1) {
			showToast('error', '<p>Amount should be greater than Zero</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(!bt_bank) {
			showToast('error', '<p>Please indicate bank name</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(isValidAmount(bt_amount, true)) {
			showToast('error', '<p>Your payment exceeds to amount due.</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		$("#op_bt_table").append("<tr><td>" + bt_cardnumber + "</td><td>" + bt_amount + "</td><td>" + bt_bank + "</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
		$('#op_bankfrom_account_number').val('');
		$('#op_bankfrom_name').val('');
		$('#op_bt_amount').val('');
		op_updateBankTransferPayment();
	});
	$('#op_addcheque').click(function() {
		var ch_date = $('#op_ch_date').val();
		var ch_number = $('#op_ch_number').val();
		var ch_amount = $('#op_ch_amount').val();
		var ch_bankname = $('#op_ch_bankname').val();
		if(!ch_date) {
			showToast('error', '<p>Please indicate date</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(!ch_number) {
			showToast('error', '<p>Please indicate card number</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(!ch_amount) {
			showToast('error', '<p>Please indicate amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(!validateDate(ch_date)) {
			showToast('error', '<p>Invalid Date Format. It should be mm/dd/yyyy (Ex. 01/01/2014) </p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(isNaN(ch_amount)) {
			showToast('error', '<p>Please indicate a valid amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(parseFloat(ch_amount) < 1) {
			showToast('error', '<p>Amount should be greater than Zero</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(!ch_bankname) {
			showToast('error', '<p>Please indicate bank name</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(isValidAmount(ch_amount, true)) {
			showToast('error', '<p>Your payment exceeds to amount due.</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		$("#op_ch_table").append("<tr><td>" + ch_date + "</td><td>" + ch_number + "</td><td>" + ch_amount + "</td><td>" + ch_bankname + "</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
		$('#op_ch_date').val('');
		$('#op_ch_number').val('');
		$('#op_ch_amount').val('');
		$('#op_ch_bankname').val('');
		op_updateChequePayment();
	});
	$('.op_cashreceivecancel').click(function() {
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
	$('body').on('click', '.op_cashreceiveok', function() {
		var member_id = $('#op_member_id').val();
		var remarks = $('#op_remarks').val();
		if(member_id) {
			op_receiveCash();
		} else {
			tempToast('error', "<p>Please enter a client</p>", "<h4>Information!</h4>");
		}

	});
	function op_isValidFormCheque() {
		if($("#op_ch_table tr").children().length) {
			var chequeArray = new Array();
			var fn = $("#op_ch_firstname").val();
			var mn = $("#op_ch_middlename").val();
			var ln = $("#op_ch_lastname").val();
			var phone = $("#op_ch_phone").val();

			if(fn && !isAlphaNumeric(fn)) {
				showToast('error', '<p>First name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
				return false;
			}
			if(mn && !isAlphaNumeric(mn)) {
				showToast('error', '<p>Middle name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
				return false;
			}
			if(ln && !isAlphaNumeric(ln)) {
				showToast('error', '<p>Last name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
				return false;
			}
			if(phone && !isAlphaNumeric(phone)) {
				showToast('error', '<p>Phone should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
				return false;
			}
			$("#op_ch_table tr").each(function(index) {
				var row = $(this);
				chequeArray[index] = {
					date: row.children().eq(0).text(),
					cheque_number: row.children().eq(1).text(),
					amount: row.children().eq(2).text(),
					bank_name: row.children().eq(3).text(),
					firstname: fn,
					lastname: ln,
					middlename: mn,
					phone: phone
				}
			});
			localStorage['op_payment_cheque'] = JSON.stringify(chequeArray);
			return true;
		}

		return true;
	}

	function op_isValidFormCredit() {
		if($("#op_credit_table tr").children().length) {
			var creditArray = new Array();
			var fn = $("#op_billing_firstname").val();
			var mn = $("#op_billing_middlename").val();
			var ln = $("#op_billing_lastname").val();
			var comp = $("#op_billing_company").val();
			var add = $("#op_billing_address").val();
			var postal = $("#op_billing_postal").val();
			var phone = $("#op_billing_phone").val();
			var email = $("#op_billing_email").val();
			var rem = $("#op_billing_remarks").val();
			// required
			var card_type = $("#op_billing_card_type").val();
			var trace_number = $("#op_billing_trace_number").val();
			var approval_code = $("#op_billing_approval_code").val();
			var date = $("#op_billing_date").val();

			if(!card_type || !trace_number || !approval_code || !date) {
				showToast('error', '<p>Please Complete Credit Card billing form. </p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
				return false;
			} else {
				if(ln && !isAlphaNumeric(ln)) {
					showToast('error', '<p>Last name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(fn && !isAlphaNumeric(fn)) {
					showToast('error', '<p>First name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(mn && !isAlphaNumeric(mn)) {
					showToast('error', '<p>Middle name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(comp && !isAlphaNumeric(comp)) {
					showToast('error', '<p>Company should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(add && !isAlphaNumeric(add)) {
					showToast('error', '<p>Address should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(postal && !isNumeric(postal)) {
					showToast('error', '<p>Postal should be numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(phone && !isAlphaNumeric(phone)) {
					showToast('error', '<p>Phone should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(email && !isEmail(email)) {
					showToast('error', '<p>Email should be valid email address</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(rem && !isAlphaNumeric(rem)) {
					showToast('error', '<p>Remarks should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				$("#op_credit_table tr").each(function(index) {
					var row = $(this);
					creditArray[index] = {
						credit_number: row.children().eq(0).text(),
						amount: row.children().eq(1).text(),
						bank_name: row.children().eq(2).text(),
						firstname: fn,
						lastname: ln,
						middlename: mn,
						phone: phone,
						comp: comp,
						add: add,
						postal: postal,
						email: email,
						remarks: rem,
						card_type: card_type,
						trace_number: trace_number,
						approval_code: approval_code,
						date: date
					}
				});
				localStorage['op_payment_credit'] = JSON.stringify(creditArray);
				return true;
			}

		}
		return true;
	}

	function op_isValidFormBankTransfer() {
		if($("#op_bt_table tr").children().length) {
			var bankTransferArray = new Array();
			var bt_bankto_name = $("#op_bt_bankto_name").val();
			var bt_bankto_account_number = $("#op_bt_bankto_account_number").val();
			var fn = $("#op_bt_firstname").val();
			var mn = $("#op_bt_middlename").val();
			var ln = $("#op_bt_lastname").val();
			var comp = $("#op_bt_company").val();
			var add = $("#op_bt_address").val();
			var postal = $("#op_bt_postal").val();
			var phone = $("#op_bt_phone").val();
			var date = $("#op_bt_date").val();

			if(!date) {
				showToast('error', '<p>Please Bank Transfer  billing form. </p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
				return false;
			} else {
				if(bt_bankto_name && !isAlphaNumeric(bt_bankto_name)) {
					showToast('error', '<p>Bank name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(bt_bankto_account_number && !isAlphaNumeric(bt_bankto_account_number)) {
					showToast('error', '<p>Bank account number should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(fn && !isAlphaNumeric(fn)) {
					showToast('error', '<p>First name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(mn & !isAlphaNumeric(mn)) {
					showToast('error', '<p>Middle name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(ln && !isAlphaNumeric(ln)) {
					showToast('error', '<p>Last name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(comp && !isAlphaNumeric(comp)) {
					showToast('error', '<p>Company should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(add && !isAlphaNumeric(add)) {
					showToast('error', '<p>Address should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(postal && !isNumeric(postal)) {
					showToast('error', '<p>Phone should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(phone && !isAlphaNumeric(phone)) {
					showToast('error', '<p>Phone should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				$("#op_bt_table tr").each(function(index) {
					var row = $(this);
					bankTransferArray[index] = {
						credit_number: row.children().eq(0).text(),
						amount: row.children().eq(1).text(),
						bank_name: row.children().eq(2).text(),
						bt_bankto_name: bt_bankto_name,
						bt_bankto_account_number: bt_bankto_account_number,
						firstname: fn,
						lastname: ln,
						middlename: mn,
						phone: phone,
						comp: comp,
						add: add,
						postal: postal,
						date: date
					}
				});
				localStorage['op_payment_bt'] = JSON.stringify(bankTransferArray);
				return true;
			}
		}
		return true;
	}

	function op_receiveCash() {
		var cash = $("#op_hidcashpayment").val();
		if(!cash) cash = 0;
		var credit = $("#op_hidcreditpayment").val();
		if(!credit) credit = 0;
		var banktransfer = $("#op_hidbanktransferpayment").val();
		if(!banktransfer) banktransfer = 0;
		var cheque = $("#op_hidchequepayment").val();
		if(!cheque) cheque = 0;
		var totalpayment = parseFloat(cash) + parseFloat(credit) + parseFloat(banktransfer) + parseFloat(cheque);
		if(totalpayment) {
			if(!op_isValidFormCheque() || !op_isValidFormCredit() || !op_isValidFormBankTransfer()) {
				return;
			}

			var change = 0;
			cash = parseFloat(cash) - parseFloat(change);
			localStorage['op_payment_cash'] = cash;


			var payment_credit;
			var payment_bt;
			var payment_cheque;
			var payment_cash;


			if(localStorage['op_payment_cash']) {
				payment_cash = localStorage['op_payment_cash'];
			}
			if(localStorage['op_payment_credit']) {
				payment_credit = localStorage['op_payment_credit'];
			}
			if(localStorage['op_payment_bt']) {
				payment_bt = localStorage['op_payment_bt'];
			}
			if(localStorage['op_payment_cheque']) {
				payment_cheque = localStorage['op_payment_cheque'];
			}
			if(payment_cash || payment_credit || payment_bt || payment_cheque) {
				$("#op_credit_table").find("tr").remove();
				$("#op_bt_table").find("tr").remove();
				$("#op_ch_table").find("tr").remove();
				$("#over_payment_b :input[type='text']").val('');
				$("#over_payment_c :input[type='text']").val('');
				$("#over_payment_d :input[type='text']").val('');
				$("#over_payment_a :input[type='text']").val('');
				$('#op_label_holder').show();
				$('#op_grandtotalholder').html(number_format(totalpayment, 2));
				$('#modalOverPayment').modal("hide");
				// ajax call
				$.ajax({
					url: '../ajax/ajax_wh_order.php', type: 'POST', data: {
						functionName: 'saveOverPaymentOrder',
						op_payment_credit: payment_credit,
						op_payment_bt: payment_bt,
						op_payment_cheque: payment_cheque,
						op_payment_cash: payment_cash,
						credit: credit,
						banktransfer: banktransfer,
						cheque: cheque,
						terminal_id: localStorage['terminal_id'],
						member_id: $('#op_member_id').val(),
						remarks: $('#op_remarks').val(),
					}, success: function(data) {

						alertify.alert("Payment received", function() {
							printDepositSlip(data,totalpayment,$('#op_remarks').val());

							location.href = 'wh-order.php';
						});
					}, error: function() {

					}
				});
				// refresh
			} else {
				tempToast('error', "<p>No payment receive.</p>", "<h4>Error!</h4>");
			}
		}
	}

	// American Numbering System
	var th = ['','thousand','million','billion','trillion'];
	// uncomment this line for English Number System
	// var th = ['','thousand','million','milliard','billion'];

	var dg = ['zero','one','two','three','four','five','six','seven','eight','nine']; var tn = ['ten','eleven','twelve','thirteen','fourteen','fifteen','sixteen','seventeen','eighteen','nineteen']; var tw = ['twenty','thirty','forty','fifty','sixty','seventy','eighty','ninety'];
	function toWords(s){s = s.replace(/[\, ]/g,''); if (s != parseFloat(s)) return 'not a number'; var x = s.indexOf('.'); if (x == -1) x = s.length; if (x > 15) return 'too big'; var n = s.split(''); var str = ''; var sk = 0; for (var i=0; i < x; i++) {if ((x-i)%3==2) {if (n[i] == '1') {str += tn[Number(n[i+1])] + ' '; i++; sk=1;} else if (n[i]!=0) {str += tw[n[i]-2] + ' ';sk=1;}} else if (n[i]!=0) {str += dg[n[i]] +' '; if ((x-i)%3==0) str += 'hundred ';sk=1;} if ((x-i)%3==1) {if (sk) str += th[(x-i-1)/3] + ' ';sk=0;}} if (x != s.length) {var y = s.length; str += 'point '; for (var i=x+1; i<y; i++) str += dg[n[i]] +' ';} return str.replace(/\s+/g,' ');}

	function printDepositSlip(d,total,rem){
		var data = JSON.parse(d);

		var company_name =data.company_name;
		var company_address = data.company_address;
		var member_name = data.member_name;
		var id = data.id;
		//var member_address = "";
		var amount = total;
		var amount_word = toWords(number_format(amount,0,'.',''));

		var remarks = rem;
		var rethtml = "<div style='border:1px solid #ccc;padding-left:20px;padding-right:20px;'>";
		var img = "";
		var contact = data.company_contact_number + " " + data.company_email;

		rethtml += "<h1 style='text-align: center;'>"+ img + " " +(company_name)+"</h1>";
		rethtml += "<p style='position:relative;top:-20px;text-align: center;'>"+company_address+"</p>";
		rethtml += "<p style='position:relative;top:-30px;text-align: center;'>"+contact+"</p>";
		rethtml += "<h4 style='position:relative;top:-10px;text-align: center;'><strong>ACKNOWLEDGEMENT RECEIPT</strong></h4>";
		rethtml += "<p style='position:relative;top:-10px;text-align: center;'>ID #"+id+"</p>";
		rethtml += "<p> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;This is to acknowledge the receipt of payment from <strong>"+(member_name)+"</strong> the amount of <strong>"+amount_word+" Pesos Only</strong> (Php. "+amount+") as payment of the following: </p>";
		rethtml += "<p style='height:10px;width:200px;border-bottom: 1px solid #000;'>&nbsp;</p>";
		rethtml += "<p style='height:10px;width:200px;border-bottom: 1px solid #000;'><span style='position:relative;top:-10px;'>"+remarks+"</span></p>";
		rethtml += "<p style='height:10px;width:200px;border-bottom: 1px solid #000;'>&nbsp;</p>";
		rethtml += "<p style='height:10px;text-align: right;'>Receive By: <span style='display:inline-block;width:200px;border-bottom: 1px solid #000;'></span></p>";
		rethtml += "<p style='height:10px;text-align: right;'>Date: <span style='display:inline-block;width:200px;border-bottom: 1px solid #000;'></span></p>";

		rethtml += "</div>";
		vm.popUpPrint(rethtml);


	}

	$('body').on('click', '#use_user_overpayment', function() {
		var over_payment_list = JSON.parse($('#op_member_list').val());
		if(over_payment_list.length > 0) {
			$('#use_user_overpayment').show();
		}
		var ret_html = "";
		for(var op in over_payment_list) {

			var remarks = (over_payment_list[op].remarks) ? over_payment_list[op].remarks : 'None';
			if(over_payment_list[op].status == 1) { // cash
				var total_cash = over_payment_list[op].json_data;
				var total_used_cash = over_payment_list[op].used_total;
				ret_html += "<div class='panel panel-default'>";
				ret_html += "<div class='panel-body'>";
				ret_html += "<p>Remarks: " + remarks + "</p>";
				ret_html += "<p>Type: Cash</p>";
				ret_html += "<p>Used: "+(total_used_cash)+"</p>";
				ret_html += "<p>Total: " + (total_cash -total_used_cash) + "</p>";
				ret_html += "<p><input type='text' class='form-control' value='"+ (total_cash -total_used_cash) +"'><input data-status='1' data-id='" + over_payment_list[op].id + "' value='" + over_payment_list[op].id + "' data-total='" + over_payment_list[op].json_data + "' type='checkbox' class='chk_overpayment' > Use Payment</p>";
				ret_html += "</div>";
				ret_html += "</div>";
			} else if(over_payment_list[op].status == 2) { // credit
				ret_html += "<div class='panel panel-default'>";
				ret_html += "<div class='panel-body'>";
				ret_html += "<p>Remarks: " + remarks + "</p>";
				ret_html += "<p>Type: Credit Card</p>";
				var credit_data = JSON.parse(over_payment_list[op].json_data);
				var total_used_credit = over_payment_list[op].used_total;
				var total_credit = 0;
				for(var cd in credit_data) {
					ret_html += "<p>Card: " + credit_data[cd].card_type + "</p>";
					ret_html += "<p>Trance Number: " + credit_data[cd].trace_number + "</p>";
					ret_html += "<p>Date: " + credit_data[cd].date + "</p>";
					ret_html += "<p>Amount: " + credit_data[cd].amount + "</p>";
					ret_html += "<hr>";
					total_credit += parseFloat(total_credit) + parseFloat(credit_data[cd].amount);
				}
				ret_html += "<p>Used: " + total_used_credit + "</p>";
				ret_html += "<p>Total: " + (total_credit - total_used_credit) + "</p>";
				ret_html += "<p><input class='form-control' type='text' value='"+ (total_credit -total_used_credit) +"'> <input data-json='" + JSON.stringify(over_payment_list[op]) + "' value='" + over_payment_list[op].id + "' data-status='2' data-id='" + over_payment_list[op].id + "' data-total='" + total_credit + "' type='checkbox' class='chk_overpayment' > Use Payment</p>";
				ret_html += "</div>";
				ret_html += "</div>";
			} else if(over_payment_list[op].status == 3) { // cheque
				ret_html += "<div class='panel panel-default'>";
				ret_html += "<div class='panel-body'>";
				ret_html += "<p>Remarks: " + remarks + "</p>";
				ret_html += "<p>Type: Check</p>";
				var cheque_data = JSON.parse(over_payment_list[op].json_data);
				var total_used_checked = over_payment_list[op].used_total;
				var total_cheque = 0;

				for(var cd in cheque_data) {
					ret_html += "<p>Ctrl#: " + cheque_data[cd].cheque_number + "</p>";
					ret_html += "<p>Date: " + cheque_data[cd].date + "</p>";
					ret_html += "<p>Amount: " + cheque_data[cd].amount + "</p>";
					ret_html += "<hr>";
					total_cheque = parseFloat(total_cheque) + parseFloat(cheque_data[cd].amount);
				}
				ret_html += "<p>Used: " + total_used_checked + "</p>";
				ret_html += "<p>Total: " + total_cheque + "</p>";
				ret_html += "<p><input class='form-control' type='text' value='"+ (total_cheque - total_used_checked) +"'> <input data-json='" + JSON.stringify(over_payment_list[op]) + "' value='" + over_payment_list[op].id + "'  data-status='3' data-id='" + over_payment_list[op].id + "' data-total='" + total_cheque + "' type='checkbox' class='chk_overpayment' > Use Payment</p>";
				ret_html += "</div>";
				ret_html += "</div>";
			} else if(over_payment_list[op].status == 4) { // bt
				ret_html += "<div class='panel panel-default' >";
				ret_html += "<div class='panel-body'>";
				ret_html += "<p>Remarks: " + remarks + "</p>";
				ret_html += "<p>Type: Bank Transfer</p>";
				var bt_data = JSON.parse(over_payment_list[op].json_data);
				var total_bt_used = over_payment_list[op].used_total;
				var total_bt = 0;
				for(var cd in bt_data) {
					ret_html += "<p>Date: " + bt_data[cd].date + "</p>";
					ret_html += "<p>Amount: " + bt_data[cd].amount + "</p>";

					total_bt = parseFloat(total_bt) + parseFloat( bt_data[cd].amount);
				}

				ret_html += "<p>Used: "+total_bt_used+"</p>";
				ret_html += "<p>Total: "+(total_bt -total_bt_used) +"</p>";

				ret_html += "<p> <input class='form-control' type='text' value='"+ (total_bt -total_bt_used) +"'> <input data-json='" + JSON.stringify(over_payment_list[op]) + "'  data-status='4' data-id='" + over_payment_list[op].id + "' value='" + over_payment_list[op].id + "' data-total='" + total_bt + "' type='checkbox' class='chk_overpayment' > Use Payment</p>";
				ret_html += "</div>";
				ret_html += "</div>";
			}
		}

		$('#right-pane-container').html(ret_html);
		$('.right-panel-pane').fadeIn(100);
	});
	$('body').on('click', '.chk_overpayment', function() {

		var con = $(this);
		var status = con.attr('data-status');
		var total = con.attr('data-total');

		var v = con.is(':checked');
		if(status == 1) { // cash
			var txtcon = $('#cashreceivetext');
			var cur_cash = txtcon.val();
			cur_cash = (cur_cash) ? cur_cash : 0;
			var total_use = con.prev().val();
			var cash_id = con.attr('data-id');
			if(v) {
				txtcon.val(parseFloat(cur_cash) + parseFloat(total_use));
				$("#hidcashpayment").val(parseFloat(cur_cash) + parseFloat(total_use));
			} else {
				txtcon.val(parseFloat(cur_cash) - parseFloat(total_use));
				$("#hidcashpayment").val(parseFloat(cur_cash) - parseFloat(total_use));
			}
			updateCashPayment();

		} else if(status == 2) { // credit
			var json = JSON.parse(con.attr('data-json'));
			var credit_data = JSON.parse(json.json_data);
			var billing_card_type = $('#billing_card_type');
			var billing_trace_number = $('#billing_trace_number');
			var billing_approval_code = $('#billing_approval_code');
			var billing_date = $('#billing_date');
			var chk_id = con.attr('data-id');
			var total_use = con.prev().val();
			var tax = parseFloat(total_use) * 0.035;
			var tax = number_format(tax,2,'.','');
			var rem = total_use - tax;
			rem = number_format(rem,2,'.','');
			$('#member_deduction_amount').val(tax);
			for(var i in credit_data) {
				if(v) {

					var others = "<p>Card Type: "+credit_data[i].card_type+"</p>";
					others += "<p>Trace Number: "+credit_data[i].trace_number+"</p>";
					others += "<p>Approval Code: "+credit_data[i].approval_code+"</p>";
					others += "<p>Date: "+credit_data[i].date+"</p>";

					$("#credit_table").append("<tr id='from_user_credit_credit" +chk_id + "' data-date='"+credit_data[i].date+"' data-card_type='"+credit_data[i].card_type+"' data-trace_number='"+credit_data[i].trace_number+"' data-approval_code='"+credit_data[i].trace_number+"'><td>"+credit_data[i].credit_number +"</td><td>"+rem+"</td><td>"+credit_data[i].bank_name+"</td><td>"+others+"</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");


				} else {
					$('#from_user_credit_credit' + chk_id).remove();
				}
			}
			updateCreditPayment();
		} else if(status == 3) { // check
			var json = JSON.parse(con.attr('data-json'));
			var check_data = JSON.parse(json.json_data);
			var ch_firstname = $('#ch_firstname');
			var ch_middlename = $('#ch_middlename');
			var ch_lastname = $('#ch_lastname');
			var ch_phone = $('#ch_phone');
			var total_use = con.prev().val();
			var chk_id = con.attr('data-id');
			for(var i in check_data) {
				if(v) {
					$('#ch_table').append("<tr id='from_user_credit_check" + chk_id + "' ><td>" + check_data[i].date + "</td><td>" + check_data[i].cheque_number + "</td><td>" + total_use + "</td><td>" + check_data[i].bank_name + "</td><td></td></tr>");
					ch_firstname.val(check_data[i].firstname);
					ch_middlename.val(check_data[i].middlename);
					ch_lastname.val(check_data[i].lastname);
					ch_phone.val(check_data[i].phone);
				} else {
					$('#from_user_credit_check' +chk_id).remove();
				}
			}
			updateChequePayment();
		} else if(status == 4) { // check
			var json = JSON.parse(con.attr('data-json'));
			var bt_id = con.attr('data-id');
			var check_data = JSON.parse(json.json_data);
			var ch_firstname = $('#bt_firstname');
			var ch_middlename = $('#bt_middlename');
			var ch_lastname = $('#bt_lastname');
			var ch_date= $('#bt_date');
			var total_use = con.prev().val();

			for(var i in check_data) {
				if(v) {

					ch_firstname.val(check_data[i].firstname);
					ch_middlename.val(check_data[i].middlename);
					ch_lastname.val(check_data[i].lastname);
					ch_date.val(check_data[i].date);

					$('#bt_table').append("<tr id='from_user_credit_check" + bt_id + "' ><td>" + check_data[i].credit_number + "</td><td>" + total_use + "</td><td>" + check_data[i].bank_name + "</td><td></td></tr>");


				} else {
					$('#from_user_credit_check' + bt_id).remove();
				}
			}
			updateBankTransferPayment();
		}

	});
	/************* END OVER PAYMENT ******************/

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
})(vm);
