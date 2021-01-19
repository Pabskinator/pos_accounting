// check if there is connection
var con = (function() {
	var pub = {};
	pub.url = 'test2.php';
	pub.hostReachable = function() {
		var xmlhttp;
		if(window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp = new XMLHttpRequest();
		} else {// code for IE6, IE5
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.open("HEAD", "test2.php", false);
		try {
			xmlhttp.send();
			return ( xmlhttp.status >= 200 && xmlhttp.status < 300 || xmlhttp.status === 304 );
		} catch(error) {
			return false;
		}
	};
	function test() {
		alert('private');
	}
	return pub;
}());

var conReachable = con.hostReachable();
var speedopt =false;
if(localStorage['speed_opt']){
	speedopt = ((localStorage['speed_opt']).trim() == '1');
}


// get user list
function isMember(){
	return (localStorage['current_position']).trim().toLowerCase() == 'member';
}
function getUsers(cid){
	if(conReachable){
		$.ajax({
			url: "ajax/ajax_getoffline_resources.php",
			type:"POST",
			data:{cid:cid},
			success: function(data){
					localStorage["users"] = data;
			}
		});
	}
}
function getSalesTypeAx(cid){
	if(conReachable){
		$.ajax({
			url: "ajax/ajax_getsalestype.php",
			type:"POST",
			data:{cid:cid},
			success: function(data){
				localStorage["sales_type_json"] = data;
			}
		});
	}
}
function getDocumentLayout(cid){
	if(conReachable){
		$.ajax({
			url: "ajax/ajax_query.php",
			type:"POST",
			data:{cid:cid,functionName:'getDocumentLayout'},
			success: function(data){
				var formatStyle = JSON.parse(data);
				localStorage["invoice_format"] = formatStyle.invoice;
				localStorage["dr_format"] = formatStyle.dr;
				localStorage["ir_format"] = formatStyle.ir;
			}
		});
	}
}
function getDrFormat(cid){
	if(conReachable){
		$.ajax({
			url: "ajax/ajax_query.php",
			type:"POST",
			data:{cid:cid,functionName:'getDrFormat'},
			success: function(data){
				localStorage["dr_format"] = data;
			}
		});
	}
}
function getIrFormat(cid){
	if(conReachable){
		$.ajax({
			url: "ajax/ajax_query.php",
			type:"POST",
			data:{cid:cid,functionName:'getIrFormat'},
			success: function(data){
				localStorage["ir_format"] = data;
			}
		});
	}
}
function getCountShouts(){
	if(conReachable){
		$.ajax({
			url: "ajax/ajax_count_shouts.php",
			type:"POST",
			data:{},
			success: function(data){
				localStorage["count_shouts"] = data;
				var last_shout = localStorage['count_shouts_last'];
				var pending = parseInt(data) - parseInt(last_shout);
				if(!pending || parseInt(pending) < 1){
					pending = 0;
				}

				$('#ctrshout').html(pending);
			}
		});
	}
}

function getServerTime(){
	if(conReachable){
			if (!Date.now) {
				Date.now = function() { return new Date().getTime(); };
			}
			var cur_date = Date.now() /1000;

		$.ajax({
			url: "ajax/ajax_getservertime.php",
			type:"POST",
			data:{},
			success: function(data){
					localStorage["servertime"] = data;
					localStorage["localtime"] = Math.floor(cur_date);
			}
		});
	}
}
// get product of branch
function getProducts(cid,branch,terminal,isGetItem,callback){
	//speedopt
	if(speedopt && !isGetItem){
		callback();
	} else {
		//endspeedopt
		if(conReachable){
			$('.loading').show();
			$.ajax({
				url: "ajax/ajax_get_item.php",
				type:"POST",
				data:{cid:cid,branch:branch,terminal:terminal},
				success: function(data){
					localStorage["items"] = data;
					callback();
					$('.loading').hide();
				}
			});
		} else {
			$('.loading').hide();
		}
	}
}

function getReservationItem(cid,callback){
	if(conReachable){
		$('.loading').show();
		$.ajax({
			url: "ajax/ajax_get_reservation.php",
			type:"POST",
			data:{cid:cid},
			success: function(data){
				localStorage["items_reserve"] = data;
				$('.loading').hide();
				callback();
			}
		});
	}
}
function getOrderOffline(cid,branch, callback){
	if(conReachable){
		$.ajax({
			url: "ajax/ajax_getreservationoffline.php",
			type:"POST",
			data:{company_id:cid,branch_id:branch},
			success: function(data){
				if(data != 0){
					localStorage["reserved_order"] = data;
					callback();
				} else {
					localStorage.removeItem('reserved_order');
				}
			}
		});
	}
}
function number_format(number, decimals, dec_point, thousands_sep) {
	number = (number + '')
		.replace(/[^0-9+\-Ee.]/g, '');
	var n = !isFinite(+number) ? 0 : +number,
		prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
		sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
		dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
		s = '',
		toFixedFix = function(n, prec) {
			var k = Math.pow(10, prec);
			return '' + (Math.round(n * k) / k)
				.toFixed(prec);
		};
	// Fix for IE parseFloat(0.55).toFixed(0) = 0;
	s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
		.split('.');
	if (s[0].length > 3) {
		s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
	}
	if ((s[1] || '')
			.length < prec) {
		s[1] = s[1] || '';
		s[1] += new Array(prec - s[1].length + 1)
			.join('0');
	}
	return s.join(dec);
}
function getLastSoldItem(cid,memid,terminal_id,callback){
	if(conReachable){
		$.ajax({
			url: "ajax/ajax_get_lastsold.php",
			type:"POST",
			data:{cid:cid,memid:memid,terminal_id:terminal_id},
			success: function(data){
				localStorage["last_sold"] = data;
				callback();
			}
		});
	}
}
function getLastSoldFree(cid,memid,terminal_id,callback){
	if(conReachable){
		$.ajax({
			url: "ajax/ajax_get_lastsold_free.php",
			type:"POST",
			data:{cid:cid,memid:memid,terminal_id:terminal_id},
			success: function(data){
				localStorage["last_sold_free"] = data;
				callback();
			}
		});
	}
}
// append items in table
function loadItems(){
	if(localStorage["items"] != null){
		var items = JSON.parse(localStorage["items"]);

		for(var i in items){
			var item = items[i];
			var qty = (item.qty) ? item.qty : 0 ;
			$('#productDisplay > tbody:last').append('<tr id="'+item.id+'"><td>'+i+'</td><td>'+item.item_code+'</td><td id="'+item.price_id+'">'+item.price+'</td><td>'+qty+'</td><td><span class="glyphicon glyphicon-plus addcart"></span></td></tr>');
		}
	}
}
// assign terminal on first load only
function assignedTerminal(t){
	if(conReachable)
	$.ajax({
		url: "ajax/ajax_assigned_terminal.php",
		type:"POST",
		data:{t:t},
		success: function(data){
			location.href = 'index.php';
		},
		error:function(){
			showToast('error','<p>Error in getting the data. Try reloading the page. </p>','<h3>WARNING!</h3>','toast-bottom-right');
		}
	});
}
// add items in cart base on barcode
function addCart(barcode){
	var items = JSON.parse(localStorage['items']);
	var item = items[barcode];
	var discountList = JSON.parse(item.discountJSON);

	var store_discount = 0;
	var warranty = item.warranty;
	var discount_ids = [];

	if(discountList.length > 0){
		for(var d in discountList){
			var discount_qty = discountList[d].for_qty;
			if(discount_qty == 1){
				discount_ids.push(discountList[d].id);
				store_discount = parseFloat(discountList[d].amount) + parseFloat(store_discount);
			}
		}
	}
	if(item){
		var rdReceiptType =$("input[name='radioType']:checked").val();

		$('#cashreceiveholder').empty();
		$('#changeholder').empty();
		$('#cashreceiveholder').append(0);
		$('#changeholder').append(0);
		var isdisableqty= '';
		if (item.item_type == -1){
			if(item.qty == '0' || item.qty == ''){
				showToast('error','<p>Not enough stock</p>','<h3>WARNING!</h3>','toast-bottom-right');
				return;
			}
		}
		if(item.item_type == 2 || item.item_type == 3 || item.item_type == 4 || item.item_type == 5){

		localStorage['hasType2'] = 1;
	//	$("#membersModal").modal("show");
	//	localStorage["temp_item_holder"] = JSON.stringify(item);

		}
		if(parseInt(localStorage['end_invoice'].trim()) == parseInt(localStorage['invoice'].trim())){
			showToast('error','<p>You don\'t have invoice slip. Please update start and end invoice of your Terminal</p>','<h3>WARNING!</h3>','toast-bottom-right');
		} else {
		var price = parseFloat(item.price);
		var total = price - store_discount;
		var trrandom = Math.floor((Math.random() * 999999) + 1);
		var lbldisc = '';
		if(parseFloat(store_discount) > 0){
			lbldisc = "*Addtl: " + store_discount;
		}
		$('#cart > tbody').append("<tr data-item_type='"+item.item_type+"' data-warranty='"+warranty+"' data-adjustment='"+item.price_adjustment+"' data-discountJSON='"+item.discountJSON+"' data-discount_ids='"+JSON.stringify(discount_ids)+"' data-store_discount='"+store_discount+"' data-is_decimal='"+item.is_decimal+"' data-random='"+trrandom+"' data-desc='"+item.description+"' data-itemcode='"+item.item_code+"'id='"+item.id+"' c-qty='"+item.cqty+"' c-days='"+item.cdays+"' data-barcode='"+barcode+"'> <td data-title='Qty'><input type='text' class='form-control circletextbox cartqty' value='1' "+isdisableqty+"></td>	<td data-title='Item'>"+item.item_code+"<br><small class='text-danger'>"+item.description+"</small></td><td data-title='Price' id='"+item.price_id+"'>"+price.toFixed(2)+"</td><td data-title='Discount'><input  type='text' class='form-control circletextbox cartdiscount' value='0'><small class='text-danger store_discount'>"+lbldisc+"</small></td><td data-title='Total'>"+total.toFixed(2)+"</td><td><input type='hidden' id='hid_unsaved_ss"+item.id+"'><input type='hidden' id='hid_multiple_ss"+item.id+"'><span  style='margin-right:8px;' id='spanmultipless"+item.id+"' class='glyphicon glyphicon-folder-open ind_multiple_ss'></span><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
			setTimeout(function(){
				var rowappended = $('#cart #'+item.id);
				rowappended.children().eq(0).find('input').focus();
				rowappended.children().eq(0).find('input').select();
			},100);
		}
	} else {
		showToast('error','<p>Not valid barcode</p>','<h3>WARNING!</h3>','toast-bottom-right');
	}
}
// check if item exist in cart if  true, append, if not, add new
function itemExistInCart(barcode){
	var e = false;
	$('#cart > tbody > tr').each(function(index){
		var row = $(this);
		var b = row.attr('data-barcode');

		if(b==barcode){
			$('#cashreceiveholder').empty();
			$('#changeholder').empty();
			$('#cashreceiveholder').append(0);
			$('#changeholder').append(0);
			e=true;
			if (row.attr('c-qty') == '-1' && row.attr('c-days') == '-1'){
			//	e=false;
			//	return;
				var qty = row.children().eq(0).find('input').val();
				 qty =parseInt(qty) + 1;
				row.children().eq(0).find('input').val(qty);
				 var price = row.children().eq(2).text();
				 var discount = row.children().eq(3).find('input').val();
				 var newtotal = (parseFloat(price) * parseFloat(qty)- parseFloat(discount));
				row.children().eq(4).empty();
				row.children().eq(4).append(number_format(newtotal,2));
			} else {
				var qty = 1;
				row.children().eq(0).find('input').val(qty);
				var price = row.children().eq(2).text();
				var discount = row.children().eq(3).find('input').val();
				var newtotal = (parseFloat(price) * parseFloat(qty)- parseFloat(discount));
				row.children().eq(4).empty();
				row.children().eq(4).append(newtotal);
				showToast('error','<p>Item already in cart.</p>','<h3>WARNING!</h3>','toast-bottom-right');
			}
			return false;
		}
	});

	return e;
}
function checkCartQty(barcode,leftqty){
	var e = false;
	$('#cart > tbody > tr').each(function(index){
		var row = $(this);
		var b = row.attr('data-barcode');
		if(b==barcode){

			if(leftqty == row.children().eq(0).find('input').val()){

				showToast('error','<p>Not Enough Stock</p>','<h3>WARNING!</h3>','toast-bottom-right');
				e=true;
			}
		}
	});
	return e;
}
function checkQty(barcode,qty){
	var items = JSON.parse(localStorage['items']);
	item = items[barcode];
	if(parseInt(item.qty) >= parseInt(qty)){
		return false;
	} else {
		return item.qty;
	}
}
// save sales item on db or locally
function saveTransaction(callback){

	var hasType2 = localStorage['hasType2'];
	var memid = 0;
	var stationid=0;

	var chkReceiptType = [];
	$("input[name='checkType']:checked").each(function(){
		chkReceiptType.push($(this).val());
	});
	if(!chkReceiptType.length){
		showToast('error','<p>Please select receipt type.</p>','<h3>WARNING!</h3>','toast-bottom-right');
	}
	var radioSalesType =$("#selectSalesType").val();
	var sales_remarks = $('#sales_remarks').val();

	var invoicelimit = localStorage['invoice_limit'];
	var drlimit = localStorage['dr_limit'];
	var irlimit = localStorage['ir_limit'];
	var cartlength = $("#cart tbody tr").length;


	var whatisorderidbullshit = 0;
	if($("#opt_member").val()){
		memid = $("#opt_member").val();
	}
	if($("#opt_station").val()){
		stationid = $("#opt_station").val();
	}
	if(hasType2 == 1){
		memid = $("#memberId").text();

	}

	if($("#cart > tbody > #noitem").children().length){
		showToast('error','<p>no items in cart</p>','<h3>WARNING!</h3>','toast-bottom-right');
	} else {
		$('.loading').show();
		var items = new Array();
		if (!Date.now) {
			Date.now = function() { return new Date().getTime(); };
		}

		var cur_date = Date.now() /1000;

		//time computation
		var timedifference = parseInt(localStorage['servertime']) - parseInt(localStorage['localtime']);
		cur_date = parseInt(cur_date) + parseInt(timedifference);

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
	$('#cart >tbody > tr').each(function(index){
		var row = $(this);
		var item_id = row.prop('id');
		var warranty = row.attr('data-warranty');
		var order_id = row.attr('data-order_id');
		whatisorderidbullshit = order_id;
		var payment_credit = '';
		var payment_bt = '';
		var payment_cheque = '';
		var payment_cash = '';
		var payment_con ='';
		var payment_con_freebies ='';
		var payment_member_credit='';
		var payment_member_deduction='';
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
		if(!order_id){
			order_id=0;
		}
		var store_discount = row.attr('data-store_discount');
		var price_adjustment = row.attr('data-adjustment');
		store_discount = (store_discount) ? store_discount : 0;
		price_adjustment = (price_adjustment) ? price_adjustment : 0;
		var cqty = row.attr('c-qty');
		var cdays = row.attr('c-days');

		var multipless = $('#hid_multiple_ss'+item_id).val();
		var branch_json = $('#hid_allocatebranch'+item_id).val();
		if(!branch_json) branch_json = '';
		var isOtherBranch  = false;
		var todeductqty = true;

		if(branch_json) isOtherBranch = true;

		if(multipless){
			multipless = JSON.parse(multipless);
			var m_discount = row.children().eq(3).find('input').val();

			var discount_multiple=0;
			var store_discount_multiple = 0;
			var price_adjustment_multiple = 0;
			if(m_discount){
				if(m_discount.indexOf("%") > 0){
					m_discount = parseFloat(m_discount)/100;
					m_discount = (parseFloat(row.children().eq(2).text()) * parseFloat(m_discount)) * parseFloat(row.children().eq(0).find('input').val());
				}
				discount_multiple = parseFloat(m_discount)/parseFloat(multipless.length);
			}
			store_discount_multiple = parseFloat(store_discount)/parseFloat(multipless.length);

			var multiplessIsFirst = true;
			for(var ms in multipless){
				var indqty = multipless[ms].qty;
				price_adjustment_multiple = parseFloat(price_adjustment) * parseFloat(indqty);
				if(multiplessIsFirst && isOtherBranch){
					multiplessIsFirst = false;
					todeductqty = true;
				}else if(!multiplessIsFirst && isOtherBranch){
					todeductqty = false;
				}
				if(!multipless[ms].stationid) multipless[ms].stationid = 0;
				if(!multipless[ms].salestypeid) multipless[ms].salestypeid = 0;
				items.push({
					item_id: item_id,
					qty: indqty,
					barcode: row.attr('data-barcode'),
					price:  row.children().eq(2).text(),
					price_id: row.children().eq(2).prop('id'),
					discount: discount_multiple,
					store_discount:store_discount_multiple,
					adjustment: price_adjustment_multiple,
					total: row.children().eq(4).text(),
					company_id:localStorage['company_id'],
					sold_date:cur_date,
					invoice: inv,
					dr:dr,
					ir:ir,
					cqty: cqty,
					cdays:cdays,
					order_id:order_id,
					payment_credit:payment_credit,
					payment_bt:payment_bt,
					payment_cheque:payment_cheque,
					payment_cash:payment_cash,
					payment_con:payment_con,
					payment_con_freebies:payment_con_freebies,
					payment_member_credit:payment_member_credit,
					payment_member_deduction:payment_member_deduction,
					mem_id:memid,
					stationid:multipless[ms].stationid,
					sales_type: multipless[ms].salestypeid,
					todeductqty:(todeductqty) ? 1:0,
					branch_json:branch_json,
					warranty:warranty
				});
				// kung todeductqty , tapos may branchjson, disable ung normal deduction ng qty
			}
			return;
		}
		if(!radioSalesType) radioSalesType = 0;

		price_adjustment =  parseFloat(price_adjustment) * parseFloat(row.children().eq(0).find('input').val());

		items.push({
		item_id: item_id,
		qty: row.children().eq(0).find('input').val(),
		barcode: row.attr('data-barcode'),
		price:  row.children().eq(2).text(),
		price_id: row.children().eq(2).prop('id'),
		discount: row.children().eq(3).find('input').val(),
		store_discount: store_discount,
		adjustment: price_adjustment,
		total: row.children().eq(4).text(),
		company_id:localStorage['company_id'],
		sold_date:cur_date,
		invoice: inv,
		dr:dr,
		ir:ir,
		cqty: cqty,
		cdays:cdays,
		order_id:order_id,
		payment_credit:payment_credit,
		payment_bt:payment_bt,
		payment_cheque:payment_cheque,
		payment_cash:payment_cash,
		payment_con:payment_con,
		payment_con_freebies:payment_con_freebies,
		payment_member_credit:payment_member_credit,
		payment_member_deduction:payment_member_deduction,
		mem_id:memid,
		stationid:stationid,
		sales_type:radioSalesType,
		sales_remarks:sales_remarks,
		todeductqty:(todeductqty) ? 1:0,
		branch_json:branch_json,
		warranty:warranty
	});
		// kung may branch json, disable ung normal na deduction ng qty

	});


	if(con.hostReachable()){
		savePendingTransaction();
		var terminal = localStorage['terminal_id'];
		var branch = localStorage['branch_id'];
		var cid = localStorage['current_id'];
		var comp =localStorage['company_id'];
		var jsonitem = JSON.stringify(items);
		var singlesales = new Array();
		//speedopt
		if(speedopt){
			var itemlocal = JSON.parse(localStorage["items"]);
			for(var p in items){
				if(itemlocal[items[p].barcode].item_type == -1){
					var itemQty = parseFloat(itemlocal[items[p].barcode].qty)-parseFloat(items[p].qty);
					itemlocal[items[p].barcode].qty = itemQty;
					$('#productDisplay #'+items[p].item_id).find('td:eq(3)').text(itemQty);
				}
			}
			localStorage["items"] = JSON.stringify(itemlocal);


		}
		//endspeedopt

		singlesales.push(jsonitem);
		singlesales = JSON.stringify(singlesales);
		$.ajax({
			url: "ajax/ajax_sale.php",
			type:"POST",
			async:false,
			data:{sales:singlesales,id:terminal,bid:branch,cashier_id:cid,comp:comp},
			success: function(data){
				//speedopt
				if(speedopt){
					$('.loading').hide();
					for(var indir in chkReceiptType){
						if(chkReceiptType[indir] == 1){
							localStorage["invoice"]  = parseInt(localStorage['invoice']) + 1;
						}
						if(chkReceiptType[indir] == 2){
							localStorage["dr"]  = parseInt(localStorage['dr']) + 1;
						}
						if(chkReceiptType[indir] == 3){
							localStorage["ir"] = parseInt(localStorage['ir']) + 1;
						}
					}
					callback();
					getSales(localStorage['branch_id'],localStorage['company_id'],localStorage['terminal_id'],recentSoldItem);
					if(whatisorderidbullshit){
						location.href='index.php';
					}

				} else {
					location.href='index.php';
				}
			},
			error:function(){
				// save in local storage
				//alert('Saving transaction error');
				$('.loading').hide();
			}
		});
	} else {
		var pending = new Array();
		var perinvoice = new Array();

		if(whatisorderidbullshit){
			var reservedorder = JSON.parse(localStorage["reserved_order"]);
			delete reservedorder["_"+whatisorderidbullshit];
			localStorage['reserved_order'] = JSON.stringify(reservedorder);
		}


		if(localStorage["sales_pending"] != null){
			pending = JSON.parse(localStorage["sales_pending"]);
		}
		var itemlocal = JSON.parse(localStorage["items"]);
		for(var p in items){
			perinvoice.push(items[p]);
			var itemQty = parseFloat(itemlocal[items[p].barcode].qty)-parseFloat(items[p].qty);
			itemlocal[items[p].barcode].qty = itemQty;
		}
		localStorage["items"] = JSON.stringify(itemlocal);
		pending.push(JSON.stringify(perinvoice));
		localStorage["sales_pending"] = JSON.stringify(pending);
		var curinvoice = localStorage["invoice"];
		var curdr = localStorage["dr"];
		for(var indir in chkReceiptType){
			if(chkReceiptType[indir] == 1){
				localStorage["invoice"]  = parseInt(localStorage['invoice']) + 1;
			}
			if(chkReceiptType[indir] == 2){
				localStorage["dr"]  = parseInt(localStorage['dr']) + 1;
			}
			if(chkReceiptType[indir] == 3){
				localStorage["ir"] = parseInt(localStorage['ir']) + 1;
			}
		}
		location.href = 'index.php';
	}
	}
}
function savePendingTransaction(){
	if(localStorage["sales_pending"] != null){
		var psales = localStorage["sales_pending"];


				var terminal = localStorage['terminal_id'];
				var branch = localStorage['branch_id'];
				var cid = localStorage['current_id'];
				var comp =localStorage['company_id'];
				$.ajax({
					url: "ajax/ajax_sale.php",
					type:"POST",
					async:false,
					data:{sales:psales,id:terminal,bid:branch,cashier_id:cid,comp:comp},
					success: function(data){

						localStorage.removeItem("sales_pending");
					},
					error:function(){
						// save in local storage
						//alert('wtf happen');
					}
				});


	}
}
function recentSoldItem(){

		var sales = localStorage['sales'];
		try{
			sales = JSON.parse(localStorage['sales']);
			$('#recentSoldItem').html('');
			var previnv = 0;
			var prevdr = 0;
			var previr  = 0;
			var retsaleshistory ="";
			var thead = "<tr><th>Item</th><th>Price</th><th>Quantity</th><th>Discount</th><th>Total</th></tr>";
			var ctr = 1;
			for(var c in sales){
				// multiple invoice
				if(previnv != sales[c].payment_id){
					if(ctr == 4){
						break;
					}
					ctr += 1;
					var myinvoice = 0;
					var mydr = 0;
					var myir = 0;
					if(sales[c].invoice != 0){
						myinvoice = "<span class='label label-primary'>Invoice # "+sales[c].invoice+" </span>";
					} else {
						myinvoice = "";
					}
					if(sales[c].dr != 0){
						mydr = "<span class='label label-danger'>DR # "+sales[c].dr+" </span>";
					} else {
						mydr = "";
					}
					if(sales[c].ir != 0){
						myir = "<span class='label label-warning'>PR # "+sales[c].ir+" </span>";
					} else {
						myir = "";
					}

					var mname ='Not Indicated';
					var station_name = 'None';
					var status ='Sold';
					if( sales[c].mln){
						mname = '' + sales[c].mln.toUpperCase();
					}
					if(sales[c].station){
						station_name = sales[c].station.toUpperCase();
					}
					if(sales[c].status == 1){
						status = '(Cancelled)';
					}

					previnv = sales[c].payment_id;

					var border ='style="border-top:1px solid #000;"';

					/*	var listli = "<ul class='list-group'>";
						listli += "<li class='list-group-item'><strong>Invoice: </strong> "+myinvoice+"</li>";
						listli += "<li class='list-group-item'><strong>DR: </strong> "+mydr+"</li>";
						listli += "<li class='list-group-item'><strong>PR: </strong> "+myir+"</li>";
						listli += "<li class='list-group-item'><strong>Member:</strong> " + mname+"</li>";
						listli += "<li class='list-group-item'><strong>Station:</strong> " + station_name+"</li>";
						listli += "<li class='list-group-item'>"+status+"</li>";
						listli += "<li class='list-group-item'><button class='btn btn-sm btn-default btnCancel'>Cancel</button> <button class='btn  btn-sm  btn-default btnReturn'>Return</button></li>";
						listli += "</li>"; */
					var listli ='';
					if(previnv == 0){
						retsaleshistory += "<div class='panel panel-default'><div class='panel-body'>"+myinvoice+mydr+myir+"<div class='row'><div class='col-md-12'><table class='table table-bordered'>"+"<tr><th colspan='5'>Member: <span class='text-danger'>"+mname+"</span> Station: <span class='text-danger'>"+station_name+"</span></th></tr>"+thead;
					} else {
						retsaleshistory += "</table></div></div></div></div><div class='panel panel-default'><div class='panel-body'>"+myinvoice+mydr+myir+"<div class='row'><div class='col-md-12'><table class='table table-bordered'>"+"<tr><th colspan='5'>Member: <span class='text-danger'>"+mname+"</span> Station: <span class='text-danger'>"+station_name+"</span></th></tr>"+thead;
					}
				} else {

					var border='style="border-top:0px solid #000;"';
				}

				sales[c].adjustment = (sales[c].adjustment) ? sales[c].adjustment : 0;
				var discount = parseFloat(sales[c].discount) + parseFloat(sales[c].store_discount);
				var ind_adj = 0;
				if(sales[c].adjustment){
					ind_adj = sales[c].adjustment / sales[c].qtys;
				}
				var total = ((parseFloat(sales[c].qtys) * parseFloat(sales[c].price)) + parseFloat(sales[c].adjustment) ) - parseFloat(discount);
				var price = parseFloat(sales[c].price) + parseFloat(ind_adj);

				retsaleshistory += "<tr><td>"+ sales[c].item_code +"<br><span class='text-muted'>"+sales[c].description+"</span></td><td>"+ price.toFixed(2) +"</td><td>"+ sales[c].qtys +"</td><td>"+ discount.toFixed(2) +"</td><td>"+ total.toFixed(2) +"</td></tr>";

			}
			$('#recentSoldItem').append(retsaleshistory+'</table></div></div></div></div>');
			$('#recentSoldItem').append("<div class='alert alert-info text-center'><a href='sales.php'>Show more sales</a></div>");
		}catch(e){
			$('#recentSoldItem').html('No sales record found.');
		}

}
function saveQueue(){
	var qId= localStorage['qId'];
	var startQueue = localStorage['queueStart'];
	var endQueue = Date.now() / 1000;
	var queues = {};
	var arr = [];
	queues['qId'] = qId;
	queues['startQueue'] = startQueue;
	queues['endQueue'] = endQueue;
	arr.push(queues);
	if(conReachable){
			var branch = localStorage['branch_id'];
			var company_id = localStorage['company_id'];
			var qjson = JSON.stringify(arr);
			$.ajax({
				url: "ajax/ajax_save_queues.php",
				type:"POST",
				async: false,
				data:{queues:qjson,bid:branch,company_id:company_id},
				success: function(data){

				},
				error:function(){
					// save in local storage
					showToast('error','<p>Error in getting the data. Try reloading the page. </p>','<h3>WARNING!</h3>','toast-bottom-right');
				}
			});
		} else {
		var pending = new Array();
		if(localStorage["queuelist_pending"] != null){
			pending = JSON.parse(localStorage["queuelist_pending"]);
		}
		pending.push(arr);
		localStorage["queuelist_pending"] = JSON.stringify(pending);
	}
}

function deductService(){
	var services = new Array();
	var isChecked = false;
	$('#tableServiceList > tbody > tr').each(function(index){
		var row = $(this);
		var checks = $(this).find('.checkServices');
		if(checks.is(':checked')){
			var service_id = $(this).attr('id');
			var member_id = $(this).attr('member-id');
			var serveach = {};
			serveach = {
				item_id:row.children().eq(0).text(),
				service_id: service_id,
				member_id: member_id,
				consumable_qty: row.children().eq(3).text()
			}
			services.push(serveach);
			isChecked = true;
		}


	});
	if(!isChecked){

		showToast('error','<p>Please Choose Services</p>','<h3>WARNING!</h3>','toast-bottom-right');
	} else {
		$('.loading').show();
		//save here sayon
		var s = JSON.stringify(services);

		$.ajax({
			url: "ajax/ajax_deduct_services.php",
			type:"POST",
			data:{s:s},
			success: function(data){
			location.href='index.php';

			},
			error: function(){
				showToast('error','<p>Error in getting the data. Try reloading the page. </p>','<h3>WARNING!</h3>','toast-bottom-right');
				$('.loading').hide();
			}
		});
	}
}
function loginAdmin(un,pw){

	$.ajax({
		url: "ajax/ajax_login_admin.php",
		type:"POST",
		data:{username:un,password:pw},
		success: function(data){


			if(data!=0){
				var user = JSON.parse(data);
				localStorage["current_id"] = user.id;
				localStorage["current_lastname"] = user.lastname;
				localStorage["current_middlename"] = user.middlename;
				localStorage["current_firstname"] = user.firstname;
				localStorage["current_username"] = user;
				localStorage["current_position"] = user.position;
				localStorage["current_position_id"] = user.position_id;
				localStorage["current_permissions"] = user.permisions;
				localStorage["company_id"] = user.company_id;
				localStorage["branch_id"] = user.branch_id;
				if(!localStorage['terminal_id']){
					localStorage["terminal_id"] = 0;
				}
				localStorage["company_name"] = user.company_name;
				location.href='admin/main.php';
			} else {

				localStorage["flashmsg"] = "<div style=''  class='alert alert-danger'>Wrong Username or Password</div>";
				location.reload()

			}
		},
		error: function(){
			showToast('error','<p>Error in getting the data. Try reloading the page. </p>','<h3>WARNING!</h3>','toast-bottom-right');
		}
	});
}
function branchTerminal(cid,type){
	$.ajax({
		url: "ajax/ajax_get_branchAndTerminal.php",
		type:"POST",
		data:{cid:cid,type:type},
		success: function(data){

				if(type == 1) {

					$("#branchitemholder").empty();
					$("#branchitemholder").append(data);

				} else {

					$("#terminalitemholder").empty();
					$("#terminalitemholder").append(data);

				}

		},
		error: function(){
			showToast('error','<p>Error in getting the data. Try reloading the page. </p>','<h3>WARNING!</h3>','toast-bottom-right');
		}
	});
}
function getCurrentInvoice(tid){
	if(localStorage['terminal_id'] != null){
		if(conReachable){
			$.ajax({
				url: "ajax/ajax_get_branchAndTerminal.php",
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
				}
			});
		}
	}
}
function getBranches(cid){

		if(conReachable){

			$.ajax({
				url: "ajax/ajax_get_branchAndTerminal.php",
				type:"POST",
				data:{cid:cid,type:5},
				success: function(data){
					localStorage["branch_list"] = data;

				},
				error: function(){
					showToast('error','<p>Error in getting the data. Try reloading the page. </p>','<h3>WARNING!</h3>','toast-bottom-right');
				}
			});
		}
}

function getSales(branch_id,company_id,terminal_id,callback){
	if(conReachable){
		$.ajax({
			url: "ajax/ajax_get_sales.php",
			type:"POST",
			data:{branch_id:branch_id,company_id:company_id,terminal_id:terminal_id},
			success: function(data){
				localStorage['sales']=data;
				if(callback){
						callback();
				}

			}
		});
	}
}

function getQueues(branch){
	if(conReachable){
		$.ajax({
			url: "ajax/ajax_get_branchAndTerminal.php",
			type:"POST",
			data:{cid:branch,type:4},
			success: function(data){
				if(data != 0){

				localStorage["queueList"] = data;
				var queuelist = JSON.parse(localStorage["queueList"]);
				$("#queueselect").empty();
				$("#queueselect").append("<option value=''>--Select Item--</option>");
				for(var q in queuelist){
					if(localStorage["onqueue"] !=null){

						var onqueue =JSON.parse(localStorage["onqueue"]);
						var checkMatch= false;
						for(var quququ in onqueue){


								if(onqueue[quququ].qId == queuelist[q].id){
									checkMatch=true;
								}

						}
					}

					if(checkMatch== true){
						continue;
					}
						$("#queueselect").append("<option value='"+queuelist[q].id+"'>"+queuelist[q].name+"</option>");
				}


				} else {
					localStorage.removeItem('queueList');
				}
			}
		});
	} else {
		if(localStorage["queueList"] != null){

			var queuelist = JSON.parse(localStorage["queueList"]);
			$("#queueselect").empty();
			$("#queueselect").append("<option value=''>--Select Item--</option>");
			for(var q in queuelist){
				if(localStorage["onqueue"] !=null){

				var onqueue =JSON.parse(localStorage["onqueue"]);
					var checkMatch= false;
					for(var quququ in onqueue){


						if(onqueue[quququ].qId == queuelist[q].id){
							checkMatch=true;
						}

					}
				}
				if(checkMatch== true){
					continue;
				}
				$("#queueselect").append("<option value='"+queuelist[q].id+"'>"+queuelist[q].name+"</option>");
			}
		}
	}
}
function getMembers(company_id){
	if(conReachable){
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
}
function getAllStations(company_id){
	if(conReachable){
		$.ajax({
			url: "ajax/ajax_get_stations.php",
			type:"POST",
			data:{company_id:company_id},
			success: function(data){
					localStorage['stations']=data;
			}
		});
	}

}
function getServices(company_id){
	if(conReachable){
		$.ajax({
			url: "ajax/ajax_get_services.php",
			type:"POST",
			data:{cid:company_id},
			success: function(data){
				if(data != 0){
					localStorage['services']= data;
				} else {
					localStorage.removeItem('services');
				}

			}
		});
	}

}
function updatemycache(){
	if ('applicationCache' in window){
		var appCache = window.applicationCache;
		switch (appCache.status) {
			case appCache.UNCACHED: // UNCACHED == 0
				console.log('UNCACHED');
				break;
			case appCache.IDLE: // IDLE == 1
				console.log('IDLE');
				break;
			case appCache.CHECKING: // CHECKING == 2
				console.log( 'CHECKING');
				break;
			case appCache.DOWNLOADING: // DOWNLOADING == 3
				console.log('DOWNLOADING');
				break;
			case appCache.UPDATEREADY:  // UPDATEREADY == 4
				console.log('UPDATEREADY');
				appCache.swapCache();
				break;
			case appCache.OBSOLETE: // OBSOLETE == 5
				console.log('OBSOLETE');
				break;
			default:
				console.log('UKNOWN CACHE STATUS');
				break;
		};
	} else {
		console.log("AppCache: No Support");
	}
}
// utility
function toTitleCase(str)
{
	return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
}
function removeMemberDetails(){
	$("#membersnameHelper").empty();
	$("#membersname").empty();
	$("#memberId").empty();
	$("#membersIdHelper").empty();
	$("#serviceInfo").empty();
}
function timeConverter(UNIX_timestamp){
	var a = new Date(UNIX_timestamp*1000);
	var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
	var year = a.getFullYear();
	var month = months[a.getMonth()];
	var date = a.getDate();
	var time = month + ' ' + date + ', ' + year;
	return time;
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
var config_station_label_name = 'Station';
if(localStorage['labels']){
	var json_obj_labels = JSON.parse(localStorage['labels']);
	if(json_obj_labels['stations']){
		config_station_label_name = json_obj_labels['stations'].label_name;
	}
}
function escapeRegExp(string) {
	return string.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
}
function replaceAll(string, find, replace) {
	return string.replace(new RegExp(escapeRegExp(find), 'g'), replace);
}

