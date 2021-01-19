$(document).ready(function(){
	$('.loading').hide();
	$('#allcontent').fadeIn();

/*
*
*  alwaysSync  = false will get only items
*  alwaysSync = true will get items, stations, branches, sales type, inv dr pr layout, and queues
*
*  speedopt = true optimize request by not refreshing the page
*  usePrinter = true will show document printing page
*
*/

	var speedopt = false; // you need to edit speedopt variable in main_pos.js too.
	var usePrinter = true;
	var alwaysSync = true;

	if(localStorage['data_sync']){
		alwaysSync = ((localStorage['data_sync']).trim() == '1');
	}
	if(localStorage['speed_opt']){
		speedopt = ((localStorage['speed_opt']).trim() == '1');
	}
	if(localStorage['use_printer']){
		usePrinter = ((localStorage['use_printer']).trim() == '1');
	}
	console.log("Speed opt:" + speedopt);
	console.log("User printer:" + usePrinter);
	console.log("Always sync:" + alwaysSync);
	var ajaxOnProgress = false;
	// initialization/cleaning previous data
	redirectUser();
	localStorage.removeItem('hasType2');
	localStorage.removeItem('qtocheckout');
	localStorage.removeItem('qId');
	localStorage.removeItem('queueStart');

	getSales(localStorage['branch_id'],localStorage['company_id'],localStorage['terminal_id'],recentSoldItem);

	//getOrderOffline(localStorage['company_id'],localStorage['branch_id']);
	//getMemberList();
	checkOnlineIndicator();
	bindKeyupShorcut();
	checkPendingQueues();
	showQueueListOnNav();
	//getMembers(localStorage['company_id']);
	savePendingTransaction();
	getServices(localStorage['company_id']);
	getCurrentInvoice(localStorage['terminal_id']);
	emptyCart();
	getCountShouts();
	getServerTime();
	if(alwaysSync){
		getSalesTypeAx(localStorage['company_id']);
		getAllStations(localStorage['company_id']);
		getBranches(localStorage['company_id']);
		getDocumentLayout(localStorage['company_id']);
		getQueues(localStorage["branch_id"]);
		getItems(true);

	} else {
		getItems();

	}

	$("#mainContainer").fadeIn();
	if(localStorage["company_name"]){
		$('#postitle').html(localStorage["company_name"].toUpperCase());
	}
	/************ Plugins init ***************/
	/*$("#opt_member").select2({
		placeholder: 'Sold to (optional)',
		allowClear: true
	}).on('select2-open',function(){
		unBindShortcut();
	}).on("select2-close", function(e) {
		// fired to the original element when the dropdown closes
		setTimeout(function() {
			$('.select2-container-active').removeClass('select2-container-active');
			$(':focus').blur();
		}, 100);
	});*/
	$("#opt_member").select2({
		placeholder:  'Sold to (optional)',
		allowClear: true,
		minimumInputLength: 2,
		ajax: {
			url: 'ajax/ajax_json.php',
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
	}).on('select2-open',function(){
		unBindShortcut();
	}).on("select2-close", function(e) {
		// fired to the original element when the dropdown closes
		setTimeout(function() {
			$('.select2-container-active').removeClass('select2-container-active');
			$(':focus').blur();
		}, 100);
	});

	$("#opt_station").select2({
		placeholder: config_station_label_name+' (optional)',
		allowClear: true
	}).on('select2-open',function(){
		unBindShortcut();
	}).on("select2-close", function(e) {
		// fired to the original element when the dropdown closes
		setTimeout(function() {
			$('.select2-container-active').removeClass('select2-container-active');
			$(':focus').blur();
		}, 100);
	});
	$('#addproductincart').select2({
		placeholder: 'Enter Item',
		allowClear: true
	});
	/*$('#membersLogName').select2({
		placeholder: "For services: Enter member's name",
		allowClear: true
	}).on('select2-open',function(){
		unBindShortcut();
	}).on("select2-close", function(e) {
		// fired to the original element when the dropdown closes
		setTimeout(function() {
			$('.select2-container-active').removeClass('select2-container-active');
			$(':focus').blur();
		}, 100);
	});*/
	$("#membersLogName").select2({
		placeholder: " For services: Enter member's name",
		allowClear: true,
		minimumInputLength: 2,
		ajax: {
			url: 'ajax/ajax_json.php',
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
	}).on('select2-open',function(){
		unBindShortcut();
	}).on("select2-close", function(e) {
		// fired to the original element when the dropdown closes
		setTimeout(function() {
			$('.select2-container-active').removeClass('select2-container-active');
			$(':focus').blur();
		}, 100);
	});


	$("#con_member").select2({
		placeholder: 'Choose member name...',
		allowClear: true
	}).on('select2-open',function(){
		unBindShortcut();
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
		unBindShortcut();
	}).on("select2-close", function(e) {
		// fired to the original element when the dropdown closes
		setTimeout(function() {
			$('.select2-container-active').removeClass('select2-container-active');
			$(':focus').blur();
		}, 100);
	});


	$('#con_member_freebies').change(function(){
		getLastSoldFree(localStorage['company_id'], $(this).val(), localStorage['terminal_id'], function() {
			if(localStorage["last_sold_free"] != '0'){
				var ls = JSON.parse(localStorage['last_sold_free']);
				$('#lastsoldfree > tbody').html('');
				var total = 0;
				for(var c in ls){
					$('#lastsoldfree > tbody').append("<tr><td>" + ls[c].barcode + "</td><td>" + ls[c].item_code + "</td><td>" +ls[c].description+ "</td><td>" +ls[c].qtys+ "</td><td>" +ls[c].price+ "</td><td>" +ls[c].discount+ "</td><td>" +((parseFloat(ls[c].qtys) * parseFloat(ls[c].price))-parseFloat(ls[c].discount))+ "</td><td>" +ls[c].date_sold+ "</td></tr>");
					total = total + parseFloat(ls[c].price);
				}
				$('#lastsoldfreetotal').html("Total: " +  total.toFixed(2));
				if(total == 0){
					$('#lastholdcon').hide();
				} else {
					$('#lastholdcon').fadeIn();
				}
			} else {
				$('#lastholdcon').hide();
			}

			if (localStorage['hasType2'] == 1){
				//current
				var name = $("#con_member_freebies option:selected").text();
				var memId = $("#con_member_freebies").val();
				removeMemberDetails();
				$("#membersIdHelper").append('Member Id: ');
				$("#memberId").append(memId);
				$("#membersnameHelper").append('Name: ');
				$("#membersname").append(name);
				localStorage.removeItem("temp_item_holder");
			}
		});
	});
	$("#member_credit").select2({
		placeholder: 'Choose member name...',
		allowClear: true
	});

	$("#member_deduction").select2({
		placeholder: 'Choose member name...',
		allowClear: true
	});

	$('#ch_date').datepicker({
		autoclose:true
	}).on('changeDate', function(ev){
		$('#ch_date').datepicker('hide');
	});

	/************ START OF 'CLICK' EVENT ***********/
	$('body').on('click','.removeImage',function(){
		$('#imagecon').hide();

	});
	$('body').on('click','#btnSyncAll',function(){
		$('.loading').show();

		getSalesTypeAx(localStorage['company_id']);
		getAllStations(localStorage['company_id']);
		getBranches(localStorage['company_id']);
		getDocumentLayout(localStorage['company_id']);
		getQueues(localStorage["branch_id"]);
		getItems(true);
		getMembers(localStorage['company_id']);
		setTimeout(function(){
			//location.href='index.php';
			$('.loading').hide();
		},4000);
	});

	$('body').on('click','.ind_multiple_ss',function(){
		var row = $(this).parents('tr');
		var qty = row.children().eq(0).find('input').val();

		$('#tridforss').val(row.attr('id'));
		$('#ind_multiple_ss_qty').val(qty);

		$('#multiplessModal').modal('show');
		$("#ind_multiple_ss_tbl").find("tr:gt(0)").remove();
		$("#ind_multiple_ss_tbl").hide();
		$('#ind_station_select2').select2('val',null);
		$('#multiple_ss_qty').val('');
		$('#multiple_ss_qty_span').html("<strong>Total: <span class='badge'> " + qty + "</span></strong>");
		if(parseInt(qty) == 1){
			$('#multiple_ss_qty').val(qty);
		}
		var unsaved= "hid_unsaved_ss"+row.attr('id');

		if($('#'+unsaved).val()){
			$('#ind_multiple_ss_tbl').show();
			$('#ind_multiple_ss_tbl > tbody').html($('#'+unsaved).val());

		}
	});
	$('body').on('click','#ind_station_add_btn',function(){
		var qty = $('#ind_station_select2_qty').val();
		var ind_val = $('#ind_station_select2').val();
		if(qty && ind_val){
			$('#ind_station_tbl').show();
			$('#ind_station_tbl > tbody').append("<tr><td>"+qty+"</td><td>"+ind_val+"</td><td><span class='glyphicon glyphicon-remove-sign removeItem'></td></tr>")
		}
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

					$('#hid_multiple_ss'+tridss).val(JSON.stringify(jsonss));
					$('#hid_unsaved_ss'+tridss).val($('#ind_multiple_ss_tbl > tbody').html());
					if(hasstation){
						$('#opt_member').select2("val",memberall);
						$('#opt_member').select2("enable",false);
						$('#opt_station').select2("val",null);
						$('#opt_station').select2("enable",false);
					}
					if(hassalestype){
						$('#selectSalesType').select2("val",null);
						$('#selectSalesType').select2("enable",false);
					}

					$('#spanmultipless'+tridss).css("color","green");
					$('#multiplessModal').modal('hide');
				}

			}
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

		if(qty && (ind_station || ind_salestype)){
			$('#ind_multiple_ss_tbl').show();
			var totalqty = qty;
			$('#ind_multiple_ss_tbl > tbody > tr').each(function(){
				var iqty = $(this).children().eq(0).text();

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


			$('#ind_multiple_ss_tbl > tbody').append("<tr data-member_id='"+member_id+"' data-station='"+ind_station+"' data-salestype='"+ind_salestype+"'><td data-title='Qty'>"+qty+"</td><td data-title='Station'>"+stationname+"</td><td data-title='Type'>"+typename+"</td><td><span class='glyphicon glyphicon-remove-sign removeItem'></td></tr>")
		}
		$('#ind_station_select2').select2('val',null);
		$('#selectSalesType2').select2('val',null);
		$('#multiple_ss_qty').val('');

		//validation sa qty
		// validation sa station

	});
	$('#ind_station_ok').click(function(){
		var trid = 	$('#tridforselect2').val();
		var row = $('#'+trid);
		$('#hid_s'+trid).val('1');
		$('#hid_st'+trid).val($('#ind_station_select2').val());
		$('#hid_m'+trid).val($('#ind_station_select2 option:selected').attr('data-member_id'));
		$('#stModal').modal('hide');
		$('#tridforselect2').val('');
		$('#ind_station_select2').select2('val',null);
		$('#opt_member').select2("enable",false);
		$('#opt_station').select2("enable",false);
		$('#spanstation'+trid).css("color","green");
	});

	$("#serviceCancel").click(function(){
		$("#serviceListModal").modal("hide");
	});

	$("#serviceOk").click(function(){
		deductService();
	});



	$("#memberCancel").click(function(){
		$("#membersModal").modal("hide");
		emptyCart();
	});
	$('#opt_member').change(function(){
		var memid = $('#opt_member').select2("val");

		if (!memid) {
			$('#opt_station').select2("val",null);
			getStationOptList('');
			removeMemberDetails();
			$("#cashreceiveholder").text(0);
			$("#changeholder").text(0);
			return;
		}
		//var isblock = $("#opt_member").select2().find(":selected").data("isblock");
		//if(isblock == '1'){
		//	showToast('error','<p>This member is on the blacklist.</p>','<h3>WARNING!</h3>','toast-bottom-right');
		//}
		if($("#con_member").val() != $("#opt_member").val()){
			$("#con_member").select2('val',$("#opt_member").val());
			if( localStorage['payment_con']){
				localStorage.removeItem('payment_con');
				$("#cashreceiveholder").text(0);
				$("#changeholder").text(0);
				updateConPayment();
				$('#con_amount').val('');
			}

		}
		if($("#con_member_freebies").val() != $("#opt_member").val()){
			$("#con_member_freebies").select2('val',$("#opt_member").val());
			if( localStorage['payment_con_freebies']){
				localStorage.removeItem('payment_con_freebies');
				$("#cashreceiveholder").text(0);
				$("#changeholder").text(0);
				updateConPaymentFreebies();
				$('#con_amount_freebies').val('');
			}
		}

		if (localStorage['hasType2'] == 1){
			//current
			var name = $("#opt_member").select2('data').text;
			var memId = $("#opt_member").val();
			removeMemberDetails();
			$("#membersIdHelper").append('Member Id: ');
			$("#memberId").append(memId);
			$("#membersnameHelper").append('Name: ');
			$("#membersname").append(name);
			localStorage.removeItem("temp_item_holder");
		}
		getMembersInd(localStorage['company_id'],$('#opt_member').val());



		getStationOptList(memid);
		if($("#cart tbody tr").children().length == 0) {
			getLastSoldItem(localStorage['company_id'], $(this).val(), localStorage['terminal_id'], function() {
				if(localStorage["last_sold"] != '0'){
					var ls = JSON.parse(localStorage['last_sold']);
					var items = JSON.parse(localStorage['items']);
					for(var c in ls){
						if(items[ls[c].barcode]){
							additemincart(ls[c].barcode);
						}

					}
				}
			});
		}

		$('#opt_member').select2({
			allowClear: true,
			placeholder: "Sold to (optional)"
		});

	});
	$('#opt_station').change(function(){
		var memid = $("#opt_station").select2().find(":selected").data("member_id");
		$('#opt_member').select2("val",memid);
		if($(this).val()){
			if (localStorage['hasType2'] == 1){
				//current
				var name = $("#opt_member option:selected").text();
				var memId = $("#opt_member").val();
				removeMemberDetails();
				$("#membersIdHelper").append('Member Id: ');
				$("#memberId").append(memId);
				$("#membersnameHelper").append('Name: ');
				$("#membersname").append(name);
				localStorage.removeItem("temp_item_holder");
			}
		}


		$('#opt_station').select2({
			allowClear: true,
			placeholder: config_station_label_name+" (optional)"
		});
	});
	$("#memberOk").click(function(){
		// verify members name
		if($("#membersText").val()==''){
			showToast('error','<p>Please Enter a Valid member\'s Name</p>','<h3>WARNING!</h3>','toast-bottom-right');
		} else {
			// include to cart
			var name = toTitleCase($("#membersText").val());
			var val = $("#member_list").find('option[value="' + $("#membersText").val() + '"]');
			var memId = val.attr('id');
			removeMemberDetails();
			$("#membersIdHelper").append('Member Id: ');
			$("#memberId").append(memId);
			$("#membersnameHelper").append('Name: ');
			$("#membersname").append(name);
			$("#membersText").val("");
			$("#membersModal").modal("hide");
			localStorage.removeItem("temp_item_holder");
			$("#opt_member").select2('val',memId);
		}
	});

	$("#logout").click(function(){
		localStorage.removeItem("current_id");
		if(conReachable){
			// if there is an internet connection, kill the session too
			location.href="logout.php";
		} else {
			// if not, redirect to login
			location.href='login.php';
		}
	});

	$('#submitbt').click(function(){
		// if no item selected
		if($("#branches").val() == "" || $("#terminals").val()=="" ){
			showToast('error','<p>Please Choose Branch and Terminal first</p>','<h3>WARNING!</h3>','toast-bottom-right');

		} else {
			var terminalarr = $('#terminals').val().split(",");
			// assign terminal and branch to the computer
			localStorage["branch_id"] = $("#branches").val();
			localStorage["branch_name"] = $("#branches option:selected").text();
			localStorage["terminal_name"]=$("#terminals option:selected").text();
			localStorage["terminal_id"] = terminalarr[0];
			localStorage["invoice"] = terminalarr[1];
			$("#btSetup").modal("hide");
			// get the product of the branch
			localStorage.removeItem("items");
			getProducts(localStorage["company_id"],localStorage["branch_id"],function(){
				if(localStorage["items"] != null){
					var viewType = 1;
					$('.posview1').hide();
					$('.posview2').hide();
					if(viewType == 1){
						var items = JSON.parse(localStorage["items"]);
						for(var i in items){
							var item = items[i];
							var qty = (item.qty) ? item.qty : 0 ;
							$('#productDisplay > tbody:last').append('<tr id="'+item.id+'"><td>'+i+'</td><td>'+item.item_code+'</td><td id="'+item.price_id+'">'+item.price+'</td><td>'+qty+'</td><td><span class="glyphicon glyphicon-plus addcart"></span></td></tr>');
						}
						$('.posview1').fadeIn();
					} else if (viewType == 2){

					}

				}
			});
			assignedTerminal(terminalarr[0]);
		}
	});

	$('body').on('click','.removeItem',function(){
		var trid = $(this).parents('tr').remove();
		refreshCartData();
		cashHolderComputation(0,0);

		localStorage.removeItem('hasType2');
		updateCreditPayment();
		updateCashPayment();
		updateConPayment();
		updateBankTransferPayment();
		updateChequePayment();
	});

	$('body').on('click','.addcart',function(){
		var producttr= $(this).parents('tr');
		if(checkCartQty(producttr.children().eq(0).text(),producttr.children().eq(3).text())){
		} else {
			var exist = itemExistInCart(producttr.children().eq(0).text());
			if(!exist){
				addCart(producttr.children().eq(0).text());
				updatesubtotal();
			} else {
				computeCartLine(producttr);
			}
			showVoid();
			removeNoItemLabel();
		}
	});

	$('body').on('click','.thumbItem',function(){
		var productBc = $(this).attr('data-barcode');
		var productQty = $(this).attr('data-barcode');
		if(checkCartQty(productBc,productQty)){
		} else {
			var exist = itemExistInCart(productBc);
			if(!exist){
				addCart(productBc);
			}
			showVoid();
			updatesubtotal();
			removeNoItemLabel();
		}
	});
	$("#queue").click(function(){
		if(localStorage['hasType2'] == 1){
			showToast('error','<p>CANNOT QUEUE ITEM SERVICES OR SUBSCRIPTIONS</p>','<h3>WARNING!</h3>','toast-bottom-right');
		} else {
			showqueuemodal();
		}
	});

	$("#showqueuelist").click(function(){
		if(localStorage["onqueue"] != null && localStorage['pendingqueue_count'] !=0 ){
			var queueitems = JSON.parse(localStorage["onqueue"]);
			$("#tableQueueList > tbody").empty();
			for(var onq in queueitems){
				$("#tableQueueList > tbody").append("<tr id='"+queueitems[onq].qLabel+"'><td>"+queueitems[onq].qLabel+"</td><td><table class='table'>"+queueitems[onq].qTableDisp+"</table></td><td><button class='btn btn-default checkout'><span class='glyphicon glyphicon-check'></span> Check out</button> <button class='btn btn-default voidQueue'><span class='glyphicon glyphicon-trash'></span> Void</button></td></tr>");
			}
			$("#queuelistmodal").modal('show');
		} else {
			showToast('error','<p>NO ITEM ON QUEUE</p>','<h3>WARNING!</h3>','toast-bottom-right');
		}
	});

	$('body').on('click','.checkout',function(){
		var tr= $(this).parents('tr');
		var queueCheckout = tr.prop("id");
		var curqueue = JSON.parse(localStorage["onqueue"]);
		for (var i in curqueue){
			if(curqueue[i].qLabel == queueCheckout){
				$('#cart > tbody').empty();
				$('#cart > tbody').append(curqueue[i].qBody);
				localStorage['queueStart'] = curqueue[i].startQueue;
				localStorage['qId'] = curqueue[i].qId;
				localStorage['qtocheckout'] = i;
				$("#queuelistmodal").modal('hide');
				updatesubtotal();
				return;
			}
		}
	});
	$('body').on('click','.voidQueue',function(){

		var tr= $(this).parents('tr');
		var queueCheckout = tr.prop("id");
		var curqueue = JSON.parse(localStorage["onqueue"]);
		for (var i in curqueue){
			if(curqueue[i].qLabel == queueCheckout){
				var newquee = JSON.parse(localStorage["onqueue"]);
				newquee = jQuery.grep(newquee, function(value) {
					return value != newquee[i];
				});
				localStorage["onqueue"] = JSON.stringify(newquee);
				localStorage["pendingqueue_count"] = parseInt(localStorage["pendingqueue_count"])-1;
				checkPendingQueues();
				refreshQueueList();
				$("#queuelistmodal").modal('hide');
				return;
			}
		}
	});

	$('#queuecancel').click(function(){
		$('#queuemodal').modal("hide");
	});

	$('body').on('click','#queueok',function(){
		if($("#cart > tbody > #noitem").children().length){
			showToast('error','<p>No items in cart</p>','<h3>WARNING!</h3>','toast-bottom-right');
		} else {
			var toBeQueued = '';
			var ordersQueue ='';
			ordersQueue = ordersQueue + "<tr><th>Item</th><th>Quantity</th><th>Price</th><th>Discount</th><th>Total</th></tr>";
			$("#cart > tbody > tr").each(function(){
				var row = $(this);
				var rowId = row.prop('id');
				var itemdesc = row.attr('data-desc');
				var itemcode = row.attr('data-itemcode');
				var cqty = row.attr('c-qty');
				var cdays = row.attr('c-days');
				var b = row.attr('data-barcode');
				var qty = row.children().eq(0).find('input').val();
				var price = row.children().eq(2).text();
				var priceId = row.children().eq(2).prop('id');
				var discount = row.children().eq(3).find('input').val();
				var total = row.children().eq(4).text();
				ordersQueue = ordersQueue + "<tr><td>"+b+"</td><td>"+qty+"</td><td>"+price+"</td><td>"+discount+"</td><td>"+total+"</td></tr>";
				toBeQueued = toBeQueued + "<tr data-desc='"+itemdesc+"' data-itemcode='"+itemcode+"' c-qty='"+cqty+"' c-days='"+cdays+"' id='"+rowId+"' data-barcode='"+b+"'><td><input type='text' class='form-control circletextbox cartqty' value='"+qty+"'></td>	<td>"+b+"</td><td id='"+priceId+"'>"+price+"</td><td><input type='text' class='form-control circletextbox cartdiscount' value='"+discount+"'></td><td>"+total+"</td><td><input type='hidden' id='hid_unsaved_ss"+rowId+"'><input type='hidden' id='hid_multiple_ss"+rowId+"'><span  style='margin-right:8px;' id='spanmultipless"+rowId+"' class='glyphicon glyphicon-folder-open ind_multiple_ss'></span><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>"
			});
			var queue = $("#queueselect").val();
			if(queue ==''){
				showToast('error','<p>No Queue..</p>','<h3>WARNING!</h3>','toast-bottom-right');

			} else {
				var queuetext = $("#queueselect option:selected").text();
				var queuedate = Date.now() /1000;
				if(localStorage["onqueue"] == null){

					var arrT = [];
					var arrQ = {"qBody":toBeQueued,"qId":queue,"qLabel":queuetext,qTableDisp:ordersQueue,startQueue:queuedate};
					arrT.push(arrQ);
					localStorage["onqueue"] = JSON.stringify(arrT);
				} else {
					var arrT = [];
					var arrQ = {"qBody":toBeQueued,"qId":queue,"qLabel":queuetext,qTableDisp:ordersQueue,startQueue:queuedate};
					arrT = JSON.parse(localStorage["onqueue"]);
					arrT.push(arrQ);
					localStorage["onqueue"] = JSON.stringify(arrT);
				}
				if(localStorage['pendingqueue_count']== null ||localStorage['pendingqueue_count'] ==0){
					localStorage['pendingqueue_count']=1;
				} else {
					localStorage['pendingqueue_count'] = parseInt(localStorage['pendingqueue_count'])+1;
				}
			}
		}
		checkPendingQueues();
		emptyCart();
		refreshQueueList();
		$('#queuemodal').modal("hide");

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
	});

	$('body').on('click','.cashreceiveok',function(){
		receiveCash();
	});

	$('#voidOrder').click(function(){
		$('#rightCon').slideUp(function(){
			emptyCart();
			$('#rightCon').slideDown();
			showVoid();
		});
	});

	$('#print').click(function(){
		var btncon = $(this);
		var btnoldval = btncon.html();
		if(!usePrinter){
			btncon.html('Processing...');
			btncon.attr('disabled',true);
		}

		var cartlength = $("#cart tbody tr").length;
		var rdReceiptType =$("input[name='radioType']:checked").val();
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
			alert('Please add limit items per invoice in manage terminal page first.');
			btncon.html(btnoldval);
			btncon.attr('disabled',false);
			return;
		}
		if(!drlimit) {
			alert('Please add limit items per DR in manage terminal page first.');
			btncon.html(btnoldval);
			btncon.attr('disabled',false);
			return;
		}
		if(!irlimit) {
			alert('Please add limit items per IR in manage terminal page first.');
			btncon.html(btnoldval);
			btncon.attr('disabled',false);
			return;
		}



		if(pagedr > 1){
			if(!confirm('This transaction will have ' + pagedr +' dr\'s')){
				btncon.html(btnoldval);
				btncon.attr('disabled',false);
				return;
			}
		}
		if(pageinvoice > 1){
			if(!confirm('This transaction will have ' + pageinvoice +' invoices')){
				btncon.html(btnoldval);
				btncon.attr('disabled',false);
				return;
			}
		}
		if(pageir > 1){
			if(!confirm('This transaction will have ' + pageir +' ir\'s')){
				btncon.html(btnoldval);
				btncon.attr('disabled',false);
				return;
			}
		}

		if(cartlength){
			var ismt = checkIfStationSalesTypeMatch();
			if(!ismt){
				showToast('error','<p>Quantity did not match on allocated quantity on ' +config_station_label_name+'</p>','<h3>WARNING!</h3>','toast-bottom-right');
				btncon.html(btnoldval);
				btncon.attr('disabled',false);
				return;
			}
			var t2 = localStorage['hasType2'];
			if(t2 == 1){
				var memid = $("#memberId").text();
				if(memid == ''){
					showToast('error','<p>Please Choose a member first</p>','<h3>WARNING!</h3>','toast-bottom-right');
					btncon.html(btnoldval);
					btncon.attr('disabled',false);
				} else{
					if(parseFloat($("#cashreceiveholder").text()) == 0){
						showToast('error','<p>Receive payment first</p>','<h3>WARNING!</h3>','toast-bottom-right');
						btncon.html(btnoldval);
						btncon.attr('disabled',false);
					} else {

						if(usePrinter){
							printInvoiceOrDr(chkReceiptType,invoicelimit,drlimit,irlimit);
						}

						saveTransaction(function(){
							//speedopt
							if(speedopt){
								getServices(localStorage['company_id']);
								emptyCart();
								displayNextInvoice();
								displayNextDr();

								showToast('info','<p>Transaction complete</p>','<h3>Info!</h3>','toast-top-right');
								btncon.html(btnoldval);
								btncon.attr('disabled',false);
							}
						});

						localStorage.removeItem('hasType2');
					}
				}
			} else {
				if(parseFloat($("#cashreceiveholder").text()) == 0){
					showToast('error','<p>Receive payment first</p>','<h3>WARNING!</h3>','toast-bottom-right');
					btncon.html(btnoldval);
					btncon.attr('disabled',false);
				} else {
					if(localStorage['qtocheckout'] != null){
						saveQueue();
						var qindex =parseInt(localStorage['qtocheckout']);
						var curqueue = JSON.parse(localStorage["onqueue"]);
						curqueue = jQuery.grep(curqueue, function(value) {
							return value != curqueue[qindex];
						});
						localStorage["pendingqueue_count"] = parseInt(localStorage["pendingqueue_count"])-1;
						localStorage["onqueue"] = JSON.stringify(curqueue);
						localStorage.removeItem('qtocheckout');
						localStorage.removeItem('qId');
						localStorage.removeItem('queueStart');
					}
					if(usePrinter){
						printInvoiceOrDr(chkReceiptType,invoicelimit,drlimit,irlimit);
					}
					saveTransaction(function(){
						//speedopt
						if(speedopt){
							getServices(localStorage['company_id']);
							emptyCart();
							displayNextInvoice();
							displayNextDr();
							showToast('info','<p>Transaction complete</p>','<h3>Info!</h3>','toast-top-right');
							btncon.html(btnoldval);
							btncon.attr('disabled',false);
						}

					});

				}
			}
		} else {
			showToast('error','<p>No items in cart</p>','<h3>WARNING!</h3>','toast-bottom-right');
			btncon.html(btnoldval);
			btncon.attr('disabled',false);
		}
	});
	$('#checkout').click(function(){
		showpricemodal()
	});
	function checkIfStationSalesTypeMatch(){
		var ret = true;
		$('#cart > tbody > tr').each(function(){
			var row = $(this);
			var id = row.attr('id');
			var qty = row.children(0).find('input').val();
			var hid = $('#hid_multiple_ss'+id).val();
			if(hid){
				var toloopqty = JSON.parse(hid);
				var allqty = 0;
				for(var i in toloopqty){
					allqty = parseFloat(allqty) + parseFloat(toloopqty[i].qty);
				}
				if(qty !=  allqty){
					ret = false;
					$('#spanmultipless'+id).css('color','red');
				}
			}
		});
		return ret;
	}
	$('#addcreditcard').click(function(){
		var bl_cardnumber = $('#billing_cardnumber').val();
		var bl_bank = $('#billing_bankname').val();
		var bl_amount = $('#billing_amount').val();
		if(!bl_cardnumber){
			bl_cardnumber = 'N/A';
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
	$('body').on('click','#showDiscountQtyAll',function(){

		showDiscountQtyAll();
	});
	/*********** END OF 'CLICK' EVENT ************/


	/*********** START OF 'CHANGE' EVENT ************/

	$('body').on('change','#branches',function(){
		branchTerminal($('#branches').val(),2);
	});

	/*********** END OF 'CHANGE' EVENT ************/


	/*********** START OF 'BLUR' EVENT ************/

	$('body').on('change','#addproductincart',function(e){

		var itembc = $('#addproductincart option:selected').attr('data-barcode');
		var prod = $('#addproductincart');
		if(itembc){
			// check if item exists
			var exist = itemExistInCart(itembc);
			if(!exist){
				additemincart(itembc);
				prod.select2('val',null);
			}
		}
		prod.select2('val',null);

		updatesubtotal();
		removeNoItemLabel();
	});


	$('#addproductincart').keypress(function (e) {
		var key = e.which;
		if(key == 13)  // the enter key code
		{
		//	$('#addproductincart').change();
		}
	});
	//bind of not on input
	$("body").on("blur",":input", function(){ bindKeyupShorcut(); });

	/*********** END OF 'BLUR' EVENT ************/


	/*********** START OF 'FOCUS' EVENT ************/

		// unbind  key shortcut when on input

	$("body").on("focus",":input", function(){
		unBindShortcut();
	});


	// select all item in textbox when focus
	$('body').on('click','input[type=text]',function(){
		var save_this = $(this);
		setTimeout (function(){
			save_this.select();
		},10);
	});

	/*********** END OF 'FOCUS' EVENT ************/


	/*********** START OF 'KEYUP/KEYPRESS' EVENT ************/
	$('body').on('change','#txtQtyAll',function(){
		var thisqty = $(this).val();
		if(!thisqty) thisqty = 1;
		$('.cartqty').val(thisqty);
		updateDiscountAndQtyAll();
	});
	$('body').on('change','#txtDiscountAll',function(){
		var thisdiscount = $(this).val();
		if(!thisdiscount) thisdiscount = 0;
		$('.cartdiscount').val(thisdiscount);
		updateDiscountAndQtyAll();
	});
	$('body').on('blur','.cartqty',function(){
		var newqty = $(this).val();
		if(newqty<1){
			$(this).val(1);
			showToast('error','<p>Quantity should be greater than 0</p>','<h3>WARNING!</h3>','toast-bottom-right');
			var parenttr= $(this).parents('tr');
			var price = parenttr.children().eq(2).text();
			var discount = parenttr.children().eq(3).find('input').val();
			var store_discount = getStoreDiscount(parenttr);
			if(discount.indexOf("%") > 0){
				discount = parseFloat(discount)/100;
				discount = (parseFloat(price) * parseFloat(discount)) * parseFloat(newqty);
			}
			discount = parseFloat(store_discount) + parseFloat(discount);
			var newtotal = (parseFloat(price) * parseFloat($(this).val()) - parseFloat(discount));
			parenttr.children().eq(4).empty();
			parenttr.children().eq(4).append(number_format(newtotal,2));
			updatesubtotal();
		}
	});
	$('body').on('keypress','.cartqty',function(e){
		console.log(e.which);
		if(!(parseInt(e.which) >= 46 && parseInt(e.which) <= 57)){
			e.preventDefault();
		}
		if(e.which==99) { // c
			showpricemodal()
		}
		else if(e.which==80) {

		}
		else if(e.which==32) { // space
			$('#addproductincart').select2('open');
			unBindShortcut();
		}
		else if(e.which==114) { // r
			removeLastItem();
		}
		else if(e.which==118) { // v
			emptyCart();
		}
		else if(e.which==113) { // q
			showqueuemodal();
		}
		else if(e.which==112) { // p
			$('#print').click();
		}
		else if(e.which==111) { // o

		}
		else if(e.which==109) { // m
			$('#opt_member').select2('open');
			unBindShortcut();
		}
		else if(e.which==115) { // s
			$('#opt_station').select2('open');
			unBindShortcut();
		}
	});
	$('body').on('keypress','.cartdiscount',function(e){
		if(e.which == 32){ // space
			e.preventDefault();
			$('#addproductincart').select2('open');
			unBindShortcut();
		}

	});
	$('body').on('keyup','.cartqty',function(){
		var newqty = $(this).val();
		var parenttr= $(this).parents('tr');
		var isdecimal = parenttr.attr('data-is_decimal');
		var item_type = parenttr.attr('item_type');
		// check if decimal
		console.log(isdecimal);
		if(isdecimal == 0){
			if(is_decimal(newqty)){
				$(this).val(1);
				showToast('error','<p>Quantity should be a whole number</p>','<h3>WARNING!</h3>','toast-bottom-right');
				computeCartLine($(this));
				return;
			}
		}
		if(isNaN(newqty)){
			$(this).val(1);
			showToast('error','<p>Quantity should be a number</p>','<h3>WARNING!</h3>','toast-bottom-right');
			computeCartLine($(this));
		}
		else if(newqty<=0){
			if(newqty == '') return;
			$(this).val(1);
			showToast('error','<p>Quantity should be greater than 0</p>','<h3>WARNING!</h3>','toast-bottom-right');
			computeCartLine($(this));
		} else {
			if (parenttr.attr('c-qty') == '-1' && parenttr.attr('c-days') == '-1' && item_type == -1){
				if(checkQty(parenttr.attr('data-barcode'),parenttr.children().eq(0).find('input').val())){
					var left = checkQty(parenttr.attr('data-barcode'),parenttr.children().eq(0).find('input').val());
					showToast('error','<p>Not enough qty only ' + left + ' item(s) left</p>','<h3>WARNING!</h3>','toast-bottom-right');
					var price = parenttr.children().eq(2).text();
					var discount = parenttr.children().eq(3).find('input').val();
					var store_discount = getStoreDiscount(parenttr);
					if(discount.indexOf("%") > 0){
						discount = parseFloat(discount)/100;
						discount = (parseFloat(price) * parseFloat(discount)) * parseFloat(newqty);
					}
					discount = parseFloat(store_discount) + parseFloat(discount);
					var newtotal = (parseFloat(price) * parseFloat(left)- parseFloat(discount));
					parenttr.children().eq(4).empty();
					parenttr.children().eq(4).append(number_format(newtotal,2));
					$(this).val(left);
					updatesubtotal();
				} else {
					computeCartLine($(this));
				}
			} else {
				//if(item_type == -1)
				//$(this).val(1);
				//showToast('error','<p>This item is for ONE quantity per Transaction.</p>','<h3>WARNING!</h3>','toast-bottom-right');
				computeCartLine($(this));
			}
		}
	});
	function computeCartLine(con){
		console.log('computeCartLine');
		var newqty = con.val();
		var parenttr= con.parents('tr');
		var price = parenttr.children().eq(2).text();
		var discount = parenttr.children().eq(3).find('input').val();
		var store_discount = getStoreDiscount(parenttr);
		if(discount.indexOf("%") > 0){
			discount = parseFloat(discount)/100;
			discount = (parseFloat(price) * parseFloat(discount)) * parseFloat(newqty);
		}
		discount = parseFloat(discount) +  parseFloat(store_discount);
		var newtotal = (parseFloat(price) * parseFloat(con.val())- parseFloat(discount));
		parenttr.children().eq(4).empty();
		parenttr.children().eq(4).append(number_format(newtotal,2));
		updatesubtotal();
	}

	$('body').on('blur','.cartdiscount',function(){
		var newdiscount = $(this).val();
		var ispercent = false;
		if(newdiscount.indexOf("%") > 0){
			newdiscount = parseFloat(newdiscount)/100;
			ispercent = true;
		}else if(newdiscount.indexOf("!") > -1){
			newdiscount = newdiscount.substring(1);
			console.log(newdiscount);
			newdiscount = -1 * newdiscount;
			console.log(newdiscount);
		}

		var parenttr= $(this).parents('tr');
		var qty =  parenttr.children().eq(0).find('input').val();
		var price = parenttr.children().eq(2).text();
		var newtotal = 0;
		var additionalDiscount = parseFloat(parenttr.attr('data-store_discount'));
		if(isNaN(newdiscount) || newdiscount==''){
			$(this).val(0);
			newtotal = (parseFloat(price) * parseFloat(qty)  -parseFloat($(this).val()));
			newtotal = parseFloat(newtotal) - additionalDiscount;
			parenttr.children().eq(4).empty();
			parenttr.children().eq(4).append(number_format(newtotal,2));
			updatesubtotal();
		}  else {
			newtotal = (parseFloat(price) * parseFloat(qty) -parseFloat(0));
			if(parseFloat(newdiscount) > parseFloat(newtotal)){
				showToast('error','<p>Discount should not be greater than the total</p>','<h3>WARNING!</h3>','toast-bottom-right');
				$(this).val(0);
				newtotal = (parseFloat(price) * parseFloat(qty) -parseFloat(0));
				newtotal = parseFloat(newtotal) - additionalDiscount;
				parenttr.children().eq(4).empty();
				parenttr.children().eq(4).append(number_format(newtotal,2));
			} else {
				if(ispercent==true){
					newdiscount = (newdiscount * price) * qty;
				}
				newtotal = (parseFloat(price) * parseFloat(qty) -parseFloat(newdiscount));
				newtotal = parseFloat(newtotal) - additionalDiscount;
				parenttr.children().eq(4).empty();
				parenttr.children().eq(4).append(number_format(newtotal,2));
				updatesubtotal();
			}
		}

		if(newdiscount==''){
			$(this).val(0);
			updatesubtotal();
			showToast('error','<p>Discount should be a number.</p>','<h3>WARNING!</h3>','toast-bottom-right');
			parenttr= $(this).parents('tr');
			qty =  parenttr.children().eq(0).find('input').val();
			price = parenttr.children().eq(2).text();
			newtotal = (parseFloat(price) * parseFloat(qty) -parseFloat($(this).val()));
			parenttr.children().eq(4).empty();
			parenttr.children().eq(4).append(number_format(newtotal,2));
		}
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
		$("#hidmemberdeduction").val($(this).val());
		if(isValidAmount($(this).val(),false)){
			showToast('error','<p>Your payment exceeds to amount due.</p>','<h3>WARNING!</h3>','toast-bottom-right');
			$(this).val('');
		}
		$("#hidmemberdeduction").val($(this).val());
		updateMemberDeduction();
	});
	$('#membersLogName').change(function () {

		var val = $("#membersLogName option:selected").text();
		var memId =  $(this).val();

		var hasServices = false;
		if(memId){
			if(localStorage['services'] != null  || !localStorage['services'] ){
				var services = JSON.parse(localStorage['services']);

				$('#tableServiceList > tbody').empty();
				$('#modalServiceName').empty();
				$('#modalServiceName').append("<span class='glyphicon glyphicon-user'></span> HELLO, <span class='text-danger'><strong>" + val.toUpperCase() + "</strong></span>");
				$('#tableServiceList > tbody').empty();
				for(var s in services){
					if(services[s].member_id == memId){
						$('#tableServiceList > tbody').append("<tr id='"+services[s].id+"' member-id='"+services[s].member_id+"'><td>"+services[s].item_id+"</td><td>"+timeConverter(services[s].start_date)+"</td><td>"+timeConverter(services[s].end_date)+"</td><td>"+services[s].consumable_qty+"</td><td><input type='checkbox' class='checkServices'></td></tr>");
						hasServices = true;
					}
				}
			}
			if(hasServices == false){
				$('#tableServiceList > tbody').empty();
				$('#tableServiceList > tbody').append("<tr><td colspan=5 class='text-danger'>NO ENROLLED SERVICES..</td></tr>");
			}
			$("#serviceListModal").modal('show');
		}
		$("#membersLogName").select2('val',null);

	});

	/************ END OF 'KEYUP' EVENT ***********/


	/************   START FUNCTIONS   ***********/

	function showMemberTextBox(){
		if(localStorage['members'] != null){
			$('#membersLog').show();
		} else {
			$('#membersLog').hide();
		}
	}

	function getMemberList(){
		if(localStorage['members']){
			try{
				var mems = JSON.parse(localStorage['members']);
				$("#membersLogName").empty();
				$("#membersLogName").append("<option></option>");
				for(var i in mems){
					$("#membersLogName").append("<option data-name='"+mems[i].lastname+ ", " + mems[i].firstname + " "+mems[i].middlename +"' value='"+mems[i].id+"'>"+mems[i].lastname+", " +mems[i].firstname + " " + mems[i].middlename +"</option>");
				}
				$('#membersLogName').select2({
					placeholder: "For services: Enter member's name",
					allowClear: true
				});
			}catch(e){

			}

		}
	}
	function getSalesTypeOpt(){
		if(localStorage['sales_type_json'] != null){
			var salestype = JSON.parse(localStorage['sales_type_json']);
			$("#selectSalesType").empty();
			$("#selectSalesType").append("<option></option>");
			$("#selectSalesType2").empty();
			$("#selectSalesType2").append("<option></option>");
			for(var i in salestype){
				var selected='';
				if(salestype[i].is_default == 1){
					selected='selected';
				}
				$("#selectSalesType").append("<option "+selected+"  value='"+salestype[i].id+"'>"+salestype[i].name+"</option>");
				$("#selectSalesType2").append("<option  "+selected+" value='"+salestype[i].id+"'>"+salestype[i].name+"</option>");
			}

			$("#selectSalesType").select2({
				placeholder: 'Please Choose Sales Type',
				allowClear: true
			}).on('select2-open',function(){
				unBindShortcut();
			}).on("select2-close", function(e) {
				// fired to the original element when the dropdown closes
				setTimeout(function() {
					$('.select2-container-active').removeClass('select2-container-active');
					$(':focus').blur();
				}, 100);
			});

			$("#selectSalesType2").select2({
				placeholder: 'Please Choose Sales Type',
				allowClear: true
			}).on('select2-open',function(){
				unBindShortcut();
			}).on("select2-close", function(e) {
				// fired to the original element when the dropdown closes
				setTimeout(function() {
					$('.select2-container-active').removeClass('select2-container-active');
					$(':focus').blur();
				}, 100);
			});
		}
	}
	function getMemberOptList(){
		if(localStorage['members']){
			var mems = JSON.parse(localStorage['members']);
			$("#opt_member").empty();
			$("#opt_member").append("<option></option>");
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
				$("#opt_member").append("<option data-isblock='"+mems[i].is_blacklisted+"' value='"+mems[i].id+"'>"+mems[i].lastname+", " +mems[i].firstname + " " + mems[i].middlename +"</option>");
				if(mems[i].freebiesamount){
					amt_freebies = mems[i].freebiesamount;
				}
				$("#con_member_freebies").append("<option data-con_freebies='"+amt_freebies+"' value='"+mems[i].id+"'>"+mems[i].lastname+", " +mems[i].firstname + " " + mems[i].middlename +" ("+amt_freebies+")</option>");
				$("#member_credit").append("<option value='"+mems[i].id+"'>"+mems[i].lastname+", " +mems[i].firstname + " " + mems[i].middlename +"</option>");

			}
		}
	}
	function getStationOptList(mem_id){
		if(localStorage['stations']){
			if(localStorage['stations'].trim() != '0'){

				var stats = JSON.parse(localStorage['stations']);
				//var memberslist  = JSON.parse(localStorage['members']);
				$("#opt_station").empty();
				$("#opt_station").append("<option value=''></option>");
				$("#ind_station_select2").empty();
				$("#ind_station_select2").append("<option value=''></option>");
				var isSelected='';
				var firststation =  0;
				for(var i in stats){
					var stat_add = stats[i].address;
					stat_add = replaceAll(stat_add,'"','');
					stat_add = replaceAll(stat_add,"'","");
					if(mem_id){
						if (stats[i].member_id == mem_id){
							if(firststation == 0){
								firststation = stats[i].id;
							}
							$("#opt_station").append("<option data-address='"+stat_add+"' data-member_id='"+stats[i].member_id+"'value='"+stats[i].id+"' "+isSelected+">"+stats[i].name+"</option>");
							$('#ind_station_select2').append("<option data-address='"+stat_add+"' data-member_id='"+stats[i].member_id+"'value='"+stats[i].id+"' "+isSelected+"> "+stats[i].mln+", "+stats[i].mfn+": "+stats[i].name+"</option>");
						}
					} else {
						$('#ind_station_select2').append("<option data-address='"+stat_add+"' data-member_id='"+stats[i].member_id+"'value='"+stats[i].id+"' "+isSelected+"> "+stats[i].mln+", "+stats[i].mfn+": "+stats[i].name+"</option>");
						$("#opt_station").append("<option data-address='"+stat_add+"' data-member_id='"+stats[i].member_id+"'value='"+stats[i].id+"' "+isSelected+">"+stats[i].name+"</option>");
					}

				}
				$('#opt_station').select2({
					allowClear: true,
					placeholder: config_station_label_name+" (optional)"
				});

				function formatStation(o) {
					if (!o.id)
						return o.text; // optgroup
					else {
						var r = o.text.split(':');
						return  r[1] + "<span style='' class='text-danger'><br><small style='display:inline-block;margin-top:5px;'>"+r[0]+"</small></span>";
					}
				}
				$('#ind_station_select2').select2({
					allowClear: true,
					placeholder: config_station_label_name+" (optional)",
					formatResult: formatStation,
					formatSelection: formatStation,
					escapeMarkup: function(m) {
						return m;
					}
				});
				if(mem_id){
					$('#opt_station').select2('val',firststation);
					$('#ind_station_select2').select2('val',firststation);
				}
			}
		}

	}

	function redirectUser(){
		if(localStorage["current_id"] != null){
			// set a welcome page if id is set
			$("#currentuserfullname").empty();
			$("#currentuserfullname").append(localStorage["current_lastname"].toUpperCase() +", "+ localStorage["current_firstname"].toUpperCase() + "-" + localStorage["terminal_name"] + "");
			$('#shout_username').val(localStorage["current_name"]);
		} else {
			// redirect to login if not set
			location.href="login.php";
		}
		if(localStorage["terminal_id"] == 0){
			location.href="admin/main.php";
		}

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
	}

	function checkOnlineIndicator(){
		if(conReachable){
			$(".online").css({'color':'lime'});
			$("#isonline").empty();
			$("#isonline").append('(Online)').css({'color':'lime'});
		} else {
			$(".online").css({'color':'red'});
			$("#isonline").empty();
			$("#isonline").append('(Offline)').css({'color':'red'});
		}
	}



	function getItems(isGetItem){
		if(localStorage["company_id"] != null && localStorage["branch_id"] != null && localStorage["terminal_id"] != null){
			// load items
			getProducts(localStorage["company_id"],localStorage["branch_id"],localStorage["terminal_id"],isGetItem,function(){

				getItemListSuggestion();
				//getMemberOptList();
				getStationOptList();
				showMemberTextBox();
				displayNextInvoice();
				displayNextDr();
				getSalesTypeOpt();

				displayItem();

			});
			if(!conReachable){
				getItemListSuggestion();
				//getMemberOptList();
				getStationOptList();
				showMemberTextBox();
				displayNextInvoice();
				displayNextDr();
				getSalesTypeOpt();

				displayItem();

			}

		}
	}

	function getItemListSuggestion(){
		if(localStorage["items"] != null){
			var items = JSON.parse(localStorage['items']);
			$("#addproductincart").empty();
			$('#addproductincart').append("<option></option>");
			for(var item in items){
				var qty = (items[item].qty) ? items[item].qty :0;
				if(items[item].item_type==1 || items[item].item_type ==2 || items[item].item_type==3 || items[item].item_type==4 || items[item].item_type==5){
					qty = "Available";
				}
				if(parseFloat(qty) > 0 || qty == "Available"){
					$('#addproductincart').append("<option data-barcode='"+item+"'data-id='"+items[item].id+"'value='"+items[item].id+"'>"+item+": &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+items[item].item_code+":"+items[item].description+":"+qty+"</option>");
				}
			}

			function formatItem(o) {

				if (!o.id)
					return o.text; // optgroup
				else {
					var r = o.text.split(':');
					return "<span> "+r[0]+"</span>" + r[1] + "<br><span style='margin-left:100px;' class='text-danger'><small class='testspanclass'>"+r[2]+"</small></span><span class='pull-right text-info'>"+r[3]+"</span>";
				}

			}
			setTimeout(function(){
				$('#addproductincart').select2({
					placeholder: 'Search Item',
					allowClear: true,
					formatResult: formatItem,
					formatSelection: formatItem,
					escapeMarkup: function(m) {
						return m;
					}
				}).on('select2-open',function(){
					unBindShortcut();
				}).on("select2-close", function(e) {
					// fired to the original element when the dropdown closes
					if(!speedopt){
						setTimeout(function() {
							$('.select2-container-active').removeClass('select2-container-active');
							$(':focus').blur();
							$('#imagecon').hide();
						}, 100);
					}


				}).on("select2-highlight", function(e) {
					if(!speedopt){
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
								$('#imagecon').fadeIn(50);

							}
						});
					}
				});
			},300);

		}
	}

	function displayItem() {
		if(false){
		if(localStorage["items"] != null) {
			var items = JSON.parse(localStorage["items"]);
			var viewType = 1;
			$('.posview1').hide();
			$('.posview2').hide();
			$('#productDisplay tbody').html('');
			if(viewType == 1) {
				for(var i in items) {
					var item = items[i];
					var gift = '';
					if(item.for_freebies == 1) {
						gift = "<span class='glyphicon glyphicon-gift'></span> <span class='glyphicon glyphicon-gift'></span> <span class='glyphicon glyphicon-gift'></span> ";
					}
					var qty = (item.qty) ? item.qty : 0;
					if(item.item_type == 1 || item.item_type == 2 || item.item_type == 3 || item.item_type == 4 || item.item_type == 5) {
						qty = "<span class='text-success'>Available";
					}
					if(qty > 0 || qty == "<span class='text-success'>Available") {
						if(!isNaN(qty)) {
							qty = formatInventoryQty(qty);
						}
						$('#productDisplay > tbody:last').append('<tr id="' + item.id + '"><td>' + i + '</td><td>' + item.item_code + " " + gift + ' <br> <small class="text-danger">' + item.description + '</small></td><td id="' + item.price_id + '">' + number_format(item.price, 2) + '</td><td>' + qty + '</td><td><span class="glyphicon glyphicon-plus addcart"></span></td></tr>');
					}
				}
				$('.posview1').fadeIn();
			} else if(viewType == 2) {
				//$('#imagecon  img').attr('src',window.location.origin+'/pos/item_images/'+itemjpg);
				$('.posview2').fadeIn();
				for(var i in items) {
					var item = items[i];
					if(item.for_freebies == 1) {
						gift = "<span class='glyphicon glyphicon-gift'></span> <span class='glyphicon glyphicon-gift'></span> <span class='glyphicon glyphicon-gift'></span> ";
					}
					var qty = (item.qty) ? item.qty : 0;
					if(item.item_type == 1 || item.item_type == 2 || item.item_type == 3 || item.item_type == 4 || item.item_type == 5) {
						qty = "<span class='text-success'>Available";
					}
					if(qty > 0 || qty == "<span class='text-success'>Available") {
						if(!isNaN(qty)) {
							qty = number_format(qty);
						}
						$('.posview2').append('	<div data-qty="' + qty + '" data-barcode="' + i + '" class="col-sm-6 col-md-4 thumbItem">' + '<div class="thumbnail">' + '<img class="item_image" onError="this.onerror=null;this.src=\'item_images/no_image.png\';" src="item_images/' + item.id + '.jpg" alt="' + item.item_code + '"> ' + '<div class="caption"> ' + '<p>' + item.item_code + '</p>' + '<small  class="item_details">' + item.description + '</small>' + '<small class="item_details">' + number_format(item.price, 2) + '</small>' + '</div>' + '</div>' + '</div>');
					}
				}
			}
		}
		function imgError(image) {
			image.onerror = "";
			image.src = "item_images/no_image.png";
			return true;
		}
	}
		if(localStorage['outReservation'] != null){
			console.log('test');
			$('#cart > tbody').append(localStorage['outReservation']);
			setTimeout(function(){
				if(conReachable){
					$("#opt_member").select2('val',localStorage['outReservationMember']);
					$("#con_member").select2('val',localStorage['outReservationMember']);
					$("#con_member_freebies").select2('val',localStorage['outReservationMember']);
					$('#opt_member').select2("enable",false);
					$('#con_member').select2("enable",false);
					$('#con_member_freebies').select2("enable",false);
					if(localStorage['outReservationStation']){
						$("#opt_station").select2('val',localStorage['outReservationStation']);
						$('#opt_station').select2("enable",false);
					}
					$("#selectSalesType").select2('val',localStorage['outReservationSalestype']);
					$('#selectSalesType').select2("enable",false);
					$('#sales_remarks').val(localStorage['outReservationRemarks']);
					$('#sales_remarks').attr("disabled",true);
				} else {
					$("#opt_member").val(localStorage['outReservationMember']);
					$("#con_member").val(localStorage['outReservationMember']);
					$('#opt_member').attr("disabled",true);
					$('#con_member').attr("disabled",true);
					$('#con_member_freebies').attr("disabled",true);
					if(localStorage['outReservationStation']){
						$("#opt_station").val(localStorage['outReservationStation']);
						$('#opt_station').attr("disabled",true);
					}
					$("#selectSalesType").val(localStorage['outReservationSalestype']);
					$('#selectSalesType').attr("disabled",true);
					$('#sales_remarks').val(localStorage['outReservationRemarks']);
					$('#sales_remarks').attr("disabled",true);
				}
				noItemInCart();
				updatesubtotal();
				removeNoItemLabel();
				$('#queue').hide();
				$(".addcart").hide();
				if(localStorage['outWithPayment'] == 1){
					$('#cashreceiveholder').html($('#grandtotalholder').html());
					$('#checkout').hide();
				}
				$('#addproductincart').select2("enable",false);
				localStorage.removeItem('outReservation');
				localStorage.removeItem('outReservationMember');
				localStorage.removeItem('outReservationSalestype');
				localStorage.removeItem('outReservationStation');
				localStorage.removeItem('outReservationRemarks');
				localStorage.removeItem('outWithPayment');
			},300);






		}
	}

	// if branch and terminal is not set, it will show in first use only
	function checkBranchTerminalSetup(){
		if(!localStorage["branch_id"] || !localStorage["terminal_id"]){
			// get all the branch and terminal of a company
			branchTerminal(localStorage["company_id"],1);
			// prevent the modal to be close
			$('#btSetup').modal({
				backdrop: 'static',
				keyboard: false
			});
			$("#btSetup").modal("show");
		}
	}

	function bindKeyupShorcut(){
		$(document).bind('keypress', function(e){
		console.log(e.which);
		e.preventDefault();
		if(e.which==122) { // c
			var chkInvoice = $('#checkInvoice');
			chkInvoice.prop('checked',!chkInvoice.prop("checked"))
		}
		else if(e.which==120) { // z
			var chkDr = $('#checkDR');
			chkDr.prop('checked',!chkDr.prop("checked"))
		}
		else if(e.which==99) { // x
			var chkIr = $('#checkIR');
			chkIr.prop('checked',!chkIr.prop("checked"))
		} else if(e.which==98) { // b
			showpricemodal();
		}
		else if(e.which==32) { // space
			$('#addproductincart').select2('open');
			unBindShortcut();
		}
		else if(e.which==114) { // r
			removeLastItem();
		}
		else if(e.which==118) { // v
			emptyCart();
		}
		else if(e.which==113) { // q
			showqueuemodal();
		}
		else if(e.which==112) { // p
			$('#print').click();
		}
		else if(e.which==111) { // o

		}
		else if(e.which==109) { // m
			$('#opt_member').select2('open');
			unBindShortcut();
		}
		else if(e.which==115) { // s
			$('#opt_station').select2('open');
			unBindShortcut();
		}
	});
	}
	function printInvoiceOrDr(type,invoice_limit,dr_limit,ir_limit){
		for(var i in type){
			if(type[i] == 1){
				PrintElem(invoice_limit);
			}
			if(type[i] == 2){
				PrintElemDr(dr_limit);
			}
			if(type[i] == 3){
			PrintElemIr(ir_limit);
			}
		}
	}
	function removeLastItem(){
		$("#cart tbody > tr:last-child").remove();
		refreshCartData();
		cashHolderComputation(0,0);

		localStorage.removeItem('hasType2');
	}

	function additemincart(barcode){
		addCart(barcode);
		showVoid();
		noItemInCart();
		updatesubtotal();
		removeNoItemLabel();
	}
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
		if(!hasind){
			$('#opt_member').select2("enable",true);
			$('#opt_station').select2("enable",true);
		}
		if(!hasind2){
			$('#selectSalesType').select2("enable",true);
		}
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
	}

	function showQueueListOnNav(){
		if(localStorage['queueList'] != null){
			$('#liqueue').show();
			$('#queue').show();
		} else {
			$('#liqueue').hide();
			$('#queue').hide();
		}
	}

	function checkPendingQueues(){
		var pendingqueues = localStorage['pendingqueue_count'];
		if(pendingqueues){
			$("#pendingqueues").empty();
			$("#pendingqueues").append(pendingqueues);
		} else {
			$("#pendingqueues").empty();
			$("#pendingqueues").append(0);
		}
	}

	function showqueuemodal(){
		if($("#cart tbody tr").children().length){
			refreshQueueList();
			$("#queuemodal").modal("show");

		} else {
			showToast('error','<p>No items in cart yet.</p>','<h3>WARNING!</h3>','toast-bottom-right');
		}
	}

	function showpricemodal(){
		if($("#cart tbody tr").children().length){
			var items = JSON.parse(localStorage['items']);
			var totalforfreebies = 0;
			$('#cart > tbody > tr').each(function(index){
				var row = $(this);
				var b = row.attr('data-barcode');
				var totalamount = replaceAll(row.children().eq(4).text(),',','');
				if(items[b].for_freebies != 0){
					totalforfreebies = parseFloat(totalforfreebies) + parseFloat(totalamount);
				}
			});

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
			updateMemberDeduction();
			$("#amountdue").html("<span style='font-size:1.2em;' class='text-info'><strong> Amount Due: " + $("#grandtotalholder").text() + "</strong></span>");
			$("#hidamountdue").val( replaceAll($("#grandtotalholder").text(),',',''));
			$("#getpricemodal").modal("show");
			setTimeout(function() { $('#cashreceivetext').focus() }, 500);
		} else {
			showToast('error','<p>No items in cart yet.</p>','<h3>WARNING!</h3>','toast-bottom-right');
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
		var member_deduction_amount = $("#hidmemberdeduction").val();
		if(!member_deduction_amount) member_deduction_amount = 0;

		var totalpayment = parseFloat(cash) + parseFloat(credit) + parseFloat(banktransfer) + parseFloat(cheque) + parseFloat(con_amount) + parseFloat(con_amount_freebies) + parseFloat(member_credit_amount)+ parseFloat(member_deduction_amount);
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
			localStorage['payment_member_deduction'] = member_deduction_amount;
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

	function showVoid(){
		if($("#cart tbody tr").children().length){
			$('#voidOrder').show();
		} else {
			$('#voidOrder').hide();
		}
	}

	function refreshCartData(){
		removeMemberDetails();
		showVoid();
		updatesubtotal();
		noItemInCart();
		checkInd();
	}

	function emptyCart(){
		$("#cart").find("tr:gt(0)").remove();
		$("#subtotalholder").empty();
		$("#vatholder").empty();
		$("#grandtotalholder").empty();
		refreshCartData();
		localStorage.removeItem('hasType2');
	}

	function removeNoItemLabel(){
		$("#noitem").remove();
	}

	function noItemInCart(){
		if(!$("#cart tbody").children().length){
			$("#cart tbody").append("<td colspan='5' id='noitem' style='padding-top:10px;' ><span class='label label-info'>NO ITEMS IN CART</span></td>");
			$('#txtDiscountAll').val('');
			$('#txtQtyAll').val('');
			$('#conDiscountQtyAll').slideUp();
		}
	}



	function PrintElem(invoice_limit)
	{
		var mem = $("#opt_member");
		var member_name = '';
		var styling = JSON.parse(localStorage['invoice_format']);

		if(mem.val()){
			member_name = $("#"+mem.attr('id')+ " :selected").text();
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
					station_name = cur.personal_address;

				}
			}
		}

		if(station.val()){
			//station_name = $("#"+station.attr('id')+ " :selected").text();
			station_address = $("#"+station.attr('id')+ " :selected").attr('data-address');
			station_id = $("#"+station.attr('id')+ " :selected").text()
		}
		var cur_date = Date.now() /1000;
		//time computation
		var timedifference = parseInt(localStorage['servertime']) - parseInt(localStorage['localtime']);
		cur_date = parseInt(cur_date) + parseInt(timedifference);

		var d = new Date(cur_date * 1000);
		var month = d.getMonth()+1;
		var day = d.getDate();
		var output = (month<10 ? '0' : '') + month + '/' +
			(day<10 ? '0' : '') + day + '/' + d.getFullYear();


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
		var tdbarcodeBold = (styling['tdbarcode']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var tdqtyBold = (styling['tdqty']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var tddescriptionBold = (styling['tddescription']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var tdpriceBold = (styling['tdprice']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var tdtotalBold = (styling['tdtotal']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';

		printhtml = printhtml + "<div id='maindivforprinting' style='page-break-before: always;position:relative;'>";
		printhtml= printhtml +  "<div style='"+datevisible+dateBold+"position:absolute;top:"+styling['date']['top']+"px; left:"+styling['date']['left']+"px;font-size:"+styling['date']['fontSize']+"px;'> <br/><br/>"+  output+ " </div><div style='clear:both;'></div>";
		printhtml= printhtml +  "<div style='"+membernamevisible+membernameBold+"position:absolute;top:"+styling['membername']['top']+"px; left:"+styling['membername']['left']+"px;font-size:"+styling['membername']['fontSize']+"px;'>"+member_name+"</div>";
		printhtml= printhtml +  "<div style='"+memberaddressvisible+memberaddressBold+"position:absolute;top:"+styling['memberaddress']['top']+"px; left:"+styling['memberaddress']['left']+"px;width:"+styling['memberaddress']['width']+"px;font-size:"+styling['memberaddress']['fontSize']+"px;'>"+station_name+"</div>";
		printhtml= printhtml +  "<div style='"+stationnamevisible+stationnameBold+"position:absolute;top:"+styling['stationname']['top']+"px; left:"+styling['stationname']['left']+"px;font-size:"+styling['stationname']['fontSize']+"px;'>"+station_id+"</div>";
		printhtml= printhtml +  "<div style='"+stationaddressvisible+stationaddressBold+"position:absolute;top:"+styling['stationaddress']['top']+"px; left:"+styling['stationaddress']['left']+"px;width:"+styling['stationaddress']['width']+"px;font-size:"+styling['stationaddress']['fontSize']+"px;'>"+station_address+"</div>";
		printhtml= printhtml + "<table id='itemscon' style='"+itemtablevisible+itemtableBold+"position:absolute;top:"+styling['itemtable']['top']+"px;left:"+styling['itemtable']['left']+"px;font-size:"+styling['itemtable']['fontSize']+"px;'> &nbps;";

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
			reservedbyname = row.attr('data-reserved_by');
			var qty = row.children().eq(0).find('input').val();
			var price = row.children().eq(2).text();
			var discount = row.children().eq(3).find('input').val();
			var total = replaceAll(row.children().eq(4).text(),',','');

			var origtotal = parseFloat(qty) * parseFloat(price);
			var additionalDiscount = parseFloat(row.attr("data-store_discount"));
			discount = parseFloat(discount) + additionalDiscount;
			console.log(discount);
			var labeldisc ='';
			var labeldisc2 ='';
			if(parseFloat(discount) > 0){
				var perunitdisc = parseFloat(discount) / parseFloat(qty);
				 labeldisc = "<br/>(Disc. " + number_format(perunitdisc,2) + ")";
				 labeldisc2 = "<br/>("+number_format(discount,2)+")";
			} else if(parseFloat(discount) < 0){
				price = parseFloat(price) - (parseFloat(discount) / parseFloat(qty));
				origtotal = parseFloat(qty) * parseFloat(price);
			}
			if(rowctr % invoicelimit == 0){
				var subtotal = (pagesubtotal / vat);
				var vatable = parseFloat(pagesubtotal) - parseFloat(subtotal);
				subtotal = subtotal.toFixed(2);
				vatable = vatable.toFixed(2);
				pagesubtotal = pagesubtotal.toFixed(2);
				lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "</table>";
				lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "<div style='"+paymentsvisible+paymentsBold+"position:absolute; list-style-type: none; left:"+styling['payments']['left']+"px;top:"+styling['payments']['top']+"px;font-size:"+styling['payments']['fontSize']+"px;'>"+subtotal+"</div>";
				lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "<div style='"+payments2visible+payments2Bold+"position:absolute; list-style-type: none; left:"+styling['payments2']['left']+"px;top:"+styling['payments2']['top']+"px;font-size:"+styling['payments2']['fontSize']+"px;'>"+vatable+"</div>";
				lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "<div style='"+payments3visible+payments3Bold+"position:absolute; list-style-type: none; left:"+styling['payments3']['left']+"px;top:"+styling['payments3']['top']+"px;font-size:"+styling['payments3']['fontSize']+"px;'>"+pagesubtotal+"</div>";
				pagectr = parseInt(pagectr) + 1;
				pagesubtotal=0;
			}
			pagesubtotal = parseFloat(pagesubtotal) + parseFloat(total);
			if(!lamankadainvoice[pagectr]) lamankadainvoice[pagectr] = '';
			lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "<tr ><td style='"+tdbarcodevisible+tdbarcodeBold+"position:relative;width:"+styling['tdbarcode']['width']+"px;padding-left:"+styling['tdbarcode']['left']+"px;'>"+itemcode+"</td><td style='"+tdqtyvisible+tdqtyBold+"position:relative;width:"+styling['tdqty']['width']+"px;padding-left:"+styling['tdqty']['left']+"px;'>"+qty+"</td><td style='"+tddescriptionvisible+tddescriptionBold+"position:relative;width:"+styling['tddescription']['width']+"px;padding-left:"+styling['tddescription']['left']+"px;'> "+ description +" <span style='padding-left:20px;'>"+labeldisc+"</span> </td><td style='"+tdpricevisible+tdpriceBold+"position:relative;width:"+styling['tdprice']['width']+"px;padding-left:"+styling['tdprice']['left']+"px;'>"+number_format(price,2)+"</td><td style='"+tdtotalvisible+tdtotalBold+"position:relative;width:"+styling['tdtotal']['width']+"px;padding-left:"+styling['tdtotal']['left']+"px;'>"+number_format(origtotal,2)+" "+labeldisc2+"</td></tr>";
			rowctr = parseInt(rowctr) +1;
		});
		if(pagesubtotal > 0){
			var subtotal = (pagesubtotal / vat);
			var vatable = parseFloat(pagesubtotal) - parseFloat(subtotal);
			subtotal = subtotal.toFixed(2);
			vatable = vatable.toFixed(2);
			pagesubtotal = pagesubtotal.toFixed(2);
			if(!lamankadainvoice[pagectr]) lamankadainvoice[pagectr] = '';
			lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "</table>";
			lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "<div style='"+paymentsvisible+paymentsBold+"position:absolute; list-style-type: none; left:"+styling['payments']['left']+"px;top:"+styling['payments']['top']+"px;font-size:"+styling['payments']['fontSize']+"px;'>"+subtotal+"</div>";
			lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "<div style='"+payments2visible+payments2Bold+"position:absolute; list-style-type: none; left:"+styling['payments2']['left']+"px;top:"+styling['payments2']['top']+"px;font-size:"+styling['payments2']['fontSize']+"px;'>"+vatable+"</div>";
			lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "<div style='"+payments3visible+payments3Bold+"position:absolute; list-style-type: none; left:"+styling['payments3']['left']+"px;top:"+styling['payments3']['top']+"px;font-size:"+styling['payments3']['fontSize']+"px;'>"+pagesubtotal+"</div>";

		}
		var printhtmlend = "";
		if(!reservedbyname) reservedbyname = "";
		if(!remarks) remarks = "";

		printhtmlend = printhtmlend + "<div style='"+cashiervisible+cashierBold+"position:absolute;left:"+styling['cashier']['left']+"px;top:"+styling['cashier']['top']+"px;font-size:"+styling['cashier']['fontSize']+"px;'>"+localStorage['current_lastname'] + ", "  + localStorage['current_firstname'] +"</div>";
		printhtmlend = printhtmlend + "<div style='"+remarksvisible+remarksBold+"position:absolute;left:"+styling['remarks']['left']+"px;top:"+styling['remarks']['top']+"px;font-size:"+styling['remarks']['fontSize']+"px;'>"+remarks+"</div>";
		printhtmlend = printhtmlend + "<div style='"+reservedvisible+reservedBold+"position:absolute;left:"+styling['reserved']['left']+"px;top:"+styling['reserved']['top']+"px;font-size:"+styling['reserved']['fontSize']+"px;'>"+reservedbyname+"</div>";
		printhtmlend = printhtmlend + "</div>";

		var finalprint = "";
		for(var i in lamankadainvoice ){
			finalprint = finalprint + printhtml + lamankadainvoice[i] + printhtmlend;
		}
		console.log(finalprint);
		Popup(finalprint);
	}
	function PrintElemIr(ir_limit)
	{
		var mem = $("#opt_member");
		var member_name = '';
		var styling = JSON.parse(localStorage['ir_format']);

		if(mem.val()){
			member_name = $("#"+mem.attr('id')+ " :selected").text();
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
					station_name = cur.personal_address;

				}
			}
		}

		if(station.val()){
			//station_name = $("#"+station.attr('id')+ " :selected").text();
			station_address = $("#"+station.attr('id')+ " :selected").attr('data-address');
			station_id = $("#"+station.attr('id')+ " :selected").text()
		}
		var cur_date = Date.now() /1000;
		//time computation
		var timedifference = parseInt(localStorage['servertime']) - parseInt(localStorage['localtime']);
		cur_date = parseInt(cur_date) + parseInt(timedifference);

		var d = new Date(cur_date * 1000);
		var month = d.getMonth()+1;
		var day = d.getDate();
		var output = (month<10 ? '0' : '') + month + '/' +
			(day<10 ? '0' : '') + day + '/' + d.getFullYear();

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
		var tdbarcodeBold = (styling['tdbarcode']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var tdqtyBold = (styling['tdqty']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var tddescriptionBold = (styling['tddescription']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var tdpriceBold = (styling['tdprice']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var tdtotalBold = (styling['tdtotal']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';

		var printhtml="";
		printhtml = printhtml + "<div id='maindivforprinting' style='page-break-before: always;position:relative;'>";
		printhtml= printhtml +  "<div style='"+datevisible+dateBold+"position:absolute;top:"+styling['date']['top']+"px; left:"+styling['date']['left']+"px;font-size:"+styling['date']['fontSize']+"px;'><br/><br/>"+  output+ " </div><div style='clear:both;'></div>";
		printhtml= printhtml +  "<div style='"+membernamevisible+membernameBold+"position:absolute;top:"+styling['membername']['top']+"px; left:"+styling['membername']['left']+"px;font-size:"+styling['membername']['fontSize']+"px;'>"+member_name+"</div>";
		printhtml= printhtml +  "<div style='"+memberaddressvisible+memberaddressBold+"position:absolute;top:"+styling['memberaddress']['top']+"px; left:"+styling['memberaddress']['left']+"px;width:"+styling['memberaddress']['width']+"px;font-size:"+styling['memberaddress']['fontSize']+"px;'>"+station_name+"</div>";
		printhtml= printhtml +  "<div style='"+stationnamevisible+stationnameBold+"position:absolute;top:"+styling['stationname']['top']+"px; left:"+styling['stationname']['left']+"px;font-size:"+styling['stationname']['fontSize']+"px;'>"+station_id+"</div>";
		printhtml= printhtml +  "<div style='"+stationaddressvisible+stationaddressBold+"position:absolute;top:"+styling['stationaddress']['top']+"px; left:"+styling['stationaddress']['left']+"px;width:"+styling['stationaddress']['width']+"px;font-size:"+styling['stationaddress']['fontSize']+"px;'>"+station_address+"</div>";
		printhtml= printhtml + "<table id='itemscon' style='position:absolute;top:"+styling['itemtable']['top']+"px;left:"+styling['itemtable']['left']+"px;font-size:"+styling['itemtable']['fontSize']+"px;'> ";
		var countallitem = 	$('#cart > tbody > tr').length;
		var irlimit = localStorage['ir_limit'];
		var lamankadadr =[];
		var pagectr = 1;
		var rowctr = 1;
		var pagesubtotal = 0;
		var pagetax=0;
		var pagegrandtotal = 0;
		var vat = 1.12;
		irlimit = parseInt(irlimit) + 1;
		var reservedbyname = "";
		$('#cart > tbody > tr').each(function(index){
			var row = $(this);
			var itemcode = row.attr("data-itemcode");
			var description = row.attr("data-desc");
			var b = row.attr('data-barcode');
			var qty = row.children().eq(0).find('input').val();
			var price = row.children().eq(2).text();
			var discount = row.children().eq(3).find('input').val();
			var total = replaceAll(row.children().eq(4).text(),',','');
			var origtotal = parseFloat(qty) * parseFloat(price);
			var additionalDiscount = parseFloat(row.attr("data-store_discount"));
			discount = parseFloat(discount) + additionalDiscount;
			reservedbyname = row.attr('data-reserved_by');
			var labeldisc ='';
			var labeldisc2 ='';
			if(parseFloat(discount) > 0){
				var perunitdisc = parseFloat(discount) / parseFloat(qty);
				labeldisc = "<br/>(Disc. " + number_format(perunitdisc,2) + ")";
				labeldisc2 = "<br/>("+number_format(discount,2)+")";
			} else if(parseFloat(discount) < 0){
				price = parseFloat(price) - (parseFloat(discount) / parseFloat(qty));
				origtotal = parseFloat(qty) * parseFloat(price);
			}
			if(rowctr % irlimit == 0){
				var subtotal = (pagesubtotal / vat);
				var vatable = parseFloat(pagesubtotal) - parseFloat(subtotal);
				subtotal = subtotal.toFixed(2);
				vatable = vatable.toFixed(2);
				pagesubtotal = pagesubtotal.toFixed(2);
				if(!lamankadadr[pagectr]) lamankadadr[pagectr] = '';
				lamankadadr[pagectr] = lamankadadr[pagectr] + "</table>";
				lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+paymentsvisible+paymentsBold+"position:absolute; list-style-type: none; left:"+styling['payments']['left']+"px;top:"+styling['payments']['top']+"px;font-size:"+styling['payments']['fontSize']+"px;'>"+subtotal+"</div>";
				lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments2visible+payments2Bold+"position:absolute; list-style-type: none; left:"+styling['payments2']['left']+"px;top:"+styling['payments2']['top']+"px;font-size:"+styling['payments2']['fontSize']+"px;'>"+vatable+"</div>";
				lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments3visible+payments3Bold+"position:absolute; list-style-type: none; left:"+styling['payments3']['left']+"px;top:"+styling['payments3']['top']+"px;font-size:"+styling['payments3']['fontSize']+"px;'>"+pagesubtotal+"</div>";
				pagectr = parseInt(pagectr) + 1;
				pagesubtotal=0;
			}
			pagesubtotal = parseFloat(pagesubtotal) + parseFloat(total);
			lamankadadr[pagectr] = lamankadadr[pagectr] + "<tr ><td style='"+tdbarcodevisible+tdbarcodeBold+"position:relative;width:"+styling['tdbarcode']['width']+"px;padding-left:"+styling['tdbarcode']['left']+"px;'>"+itemcode+"</td><td style='"+tdqtyvisible+tdqtyBold+"position:relative;width:"+styling['tdqty']['width']+"px;padding-left:"+styling['tdqty']['left']+"px;'>"+qty+"</td><td style='"+tddescriptionvisible+tddescriptionBold+"position:relative;width:"+styling['tddescription']['width']+"px;padding-left:"+styling['tddescription']['left']+"px;'> "+ description +" <span style='padding-left:20px;'>"+labeldisc+"</span> </td><td style='"+tdpricevisible+tdpriceBold+"position:relative;width:"+styling['tdprice']['width']+"px;padding-left:"+styling['tdprice']['left']+"px;'>"+number_format(price,2)+"</td><td style='"+tdtotalvisible+tdtotalBold+"position:relative;width:"+styling['tdtotal']['width']+"px;padding-left:"+styling['tdtotal']['left']+"px;'>"+number_format(origtotal,2)+" "+labeldisc2+"</td></tr>";
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
			lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+paymentsvisible+paymentsBold+"position:absolute; list-style-type: none; left:"+styling['payments']['left']+"px;top:"+styling['payments']['top']+"px;font-size:"+styling['payments']['fontSize']+"px;'>"+subtotal+"</div>";
			lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments2visible+payments2Bold+"position:absolute; list-style-type: none; left:"+styling['payments2']['left']+"px;top:"+styling['payments2']['top']+"px;font-size:"+styling['payments2']['fontSize']+"px;'>"+vatable+"</div>";
			lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments3visible+payments3Bold+"position:absolute; list-style-type: none; left:"+styling['payments3']['left']+"px;top:"+styling['payments3']['top']+"px;font-size:"+styling['payments3']['fontSize']+"px;'>"+pagesubtotal+"</div>";
		}
		var printhtmlend = "";
		if(!reservedbyname) reservedbyname = "";
		printhtmlend = printhtmlend + "<div style='"+cashiervisible+cashierBold+"position:absolute;left:"+styling['cashier']['left']+"px;top:"+styling['cashier']['top']+"px;font-size:"+styling['cashier']['fontSize']+"px;'>"+localStorage['current_lastname'] + ", "  + localStorage['current_firstname'] +"</div>";
		printhtmlend = printhtmlend + "<div style='"+remarksvisible+remarksBold+"position:absolute;left:"+styling['remarks']['left']+"px;top:"+styling['remarks']['top']+"px;font-size:"+styling['remarks']['fontSize']+"px;'>"+remarks+"</div>";
		printhtmlend = printhtmlend + "<div style='"+reservedvisible+reservedBold+"position:absolute;left:"+styling['reserved']['left']+"px;top:"+styling['reserved']['top']+"px;font-size:"+styling['reserved']['fontSize']+"px;'>"+reservedbyname+"</div>";
		printhtmlend = printhtmlend + "</div>";
		var finalprint = "";
		for(var i in lamankadadr ){
			finalprint = finalprint + printhtml + lamankadadr[i] + printhtmlend;
		}
		console.log(finalprint);
		Popup(finalprint);
	}
	function PrintElemDr(dr_limit)
	{
		var mem = $("#opt_member");
		var member_name = '';
		var styling = JSON.parse(localStorage['dr_format']);

		if(mem.val()){
			member_name = $("#"+mem.attr('id')+ " :selected").text();
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
					station_name = cur.personal_address;

				}
			}
		}

		if(station.val()){
			//station_name = $("#"+station.attr('id')+ " :selected").text();
			station_address = $("#"+station.attr('id')+ " :selected").attr('data-address');
			station_id = $("#"+station.attr('id')+ " :selected").text()
		}
		var cur_date = Date.now() /1000;
		//time computation
		var timedifference = parseInt(localStorage['servertime']) - parseInt(localStorage['localtime']);
		cur_date = parseInt(cur_date) + parseInt(timedifference);

		var d = new Date(cur_date * 1000);
		var month = d.getMonth()+1;
		var day = d.getDate();
		var output = (month<10 ? '0' : '') + month + '/' +
			(day<10 ? '0' : '') + day + '/' + d.getFullYear();

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
		var tdbarcodeBold = (styling['tdbarcode']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var tdqtyBold = (styling['tdqty']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var tddescriptionBold = (styling['tddescription']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var tdpriceBold = (styling['tdprice']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
		var tdtotalBold = (styling['tdtotal']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';

		var printhtml="";
		printhtml = printhtml + "<div id='maindivforprinting' style='page-break-before: always;position:relative;'>";
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
			var qty = row.children().eq(0).find('input').val();
			var price = row.children().eq(2).text();
			var discount = row.children().eq(3).find('input').val();
			var total = replaceAll(row.children().eq(4).text(),',','');
			var origtotal = parseFloat(qty) * parseFloat(price);
			var additionalDiscount = parseFloat(row.attr("data-store_discount"));
			discount = parseFloat(discount) + additionalDiscount;
			reservedbyname = row.attr('data-reserved_by');
			var labeldisc ='';
			var labeldisc2 ='';
			if(parseFloat(discount) > 0){
				var perunitdisc = parseFloat(discount) / parseFloat(qty);
				labeldisc = "<br/>(Disc. " + number_format(perunitdisc,2) + ")";
				labeldisc2 = "<br/>("+number_format(discount,2)+")";
			} else if(parseFloat(discount) < 0){
				price = parseFloat(price) - (parseFloat(discount) / parseFloat(qty));
				origtotal = parseFloat(qty) * parseFloat(price);
			}
			if(rowctr % drlimit == 0){
				var subtotal = (pagesubtotal / vat);
				var vatable = parseFloat(pagesubtotal) - parseFloat(subtotal);
				subtotal = subtotal.toFixed(2);
				vatable = vatable.toFixed(2);
				pagesubtotal = pagesubtotal.toFixed(2);
				if(!lamankadadr[pagectr]) lamankadadr[pagectr] = '';
				lamankadadr[pagectr] = lamankadadr[pagectr] + "</table>";
				lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+paymentsvisible+paymentsBold+"position:absolute; list-style-type: none; left:"+styling['payments']['left']+"px;top:"+styling['payments']['top']+"px;font-size:"+styling['payments']['fontSize']+"px;'>"+subtotal+"</div>";
				lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments2visible+payments2Bold+"position:absolute; list-style-type: none; left:"+styling['payments2']['left']+"px;top:"+styling['payments2']['top']+"px;font-size:"+styling['payments2']['fontSize']+"px;'>"+vatable+"</div>";
				lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments3visible+payments3Bold+"position:absolute; list-style-type: none; left:"+styling['payments3']['left']+"px;top:"+styling['payments3']['top']+"px;font-size:"+styling['payments3']['fontSize']+"px;'>"+pagesubtotal+"</div>";
				pagectr = parseInt(pagectr) + 1;
				pagesubtotal=0;
			}
			pagesubtotal = parseFloat(pagesubtotal) + parseFloat(total);
			lamankadadr[pagectr] = lamankadadr[pagectr] + "<tr ><td style='"+tdbarcodevisible+tdbarcodeBold+"position:relative;width:"+styling['tdbarcode']['width']+"px;padding-left:"+styling['tdbarcode']['left']+"px;'>"+itemcode+"</td><td style='"+tdqtyvisible+tdqtyBold+"position:relative;width:"+styling['tdqty']['width']+"px;padding-left:"+styling['tdqty']['left']+"px;'>"+qty+"</td><td style='"+tddescriptionvisible+tddescriptionBold+"position:relative;width:"+styling['tddescription']['width']+"px;padding-left:"+styling['tddescription']['left']+"px;'> "+ description +" <span style='padding-left:20px;'>"+labeldisc+"</span> </td><td style='"+tdpricevisible+tdpriceBold+"position:relative;width:"+styling['tdprice']['width']+"px;padding-left:"+styling['tdprice']['left']+"px;'>"+number_format(price,2)+"</td><td style='"+tdtotalvisible+tdtotalBold+"position:relative;width:"+styling['tdtotal']['width']+"px;padding-left:"+styling['tdtotal']['left']+"px;'>"+number_format(origtotal,2)+" "+labeldisc2+"</td></tr>";
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
			lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+paymentsvisible+paymentsBold+"position:absolute; list-style-type: none; left:"+styling['payments']['left']+"px;top:"+styling['payments']['top']+"px;font-size:"+styling['payments']['fontSize']+"px;'>"+subtotal+"</div>";
			lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments2visible+payments2Bold+"position:absolute; list-style-type: none; left:"+styling['payments2']['left']+"px;top:"+styling['payments2']['top']+"px;font-size:"+styling['payments2']['fontSize']+"px;'>"+vatable+"</div>";
			lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments3visible+payments3Bold+"position:absolute; list-style-type: none; left:"+styling['payments3']['left']+"px;top:"+styling['payments3']['top']+"px;font-size:"+styling['payments3']['fontSize']+"px;'>"+pagesubtotal+"</div>";
		}
		var printhtmlend = "";
		if(!reservedbyname) reservedbyname = "";
		printhtmlend = printhtmlend + "<div style='"+cashiervisible+cashierBold+"position:absolute;left:"+styling['cashier']['left']+"px;top:"+styling['cashier']['top']+"px;font-size:"+styling['cashier']['fontSize']+"px;'>"+localStorage['current_lastname'] + ", "  + localStorage['current_firstname'] +"</div>";
		printhtmlend = printhtmlend + "<div style='"+remarksvisible+remarksBold+"position:absolute;left:"+styling['remarks']['left']+"px;top:"+styling['remarks']['top']+"px;font-size:"+styling['remarks']['fontSize']+"px;'>"+remarks+"</div>";
		printhtmlend = printhtmlend + "<div style='"+reservedvisible+reservedBold+"position:absolute;left:"+styling['reserved']['left']+"px;top:"+styling['reserved']['top']+"px;font-size:"+styling['reserved']['fontSize']+"px;'>"+reservedbyname+"</div>";
		printhtmlend = printhtmlend + "</div>";
		var finalprint = "";
		for(var i in lamankadadr ){
			finalprint = finalprint + printhtml + lamankadadr[i] + printhtmlend;
		}

		Popup(finalprint);
	}

	function Popup(data)
	{

		var mywindow = window.open('', 'new div', '');
		mywindow.document.write('<html><head><title></title><style></style>');
		/*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
		mywindow.document.write('</head><body style="padding:0;margin:0;">');
		mywindow.document.write(data);
		mywindow.document.write('</body></html>');
		mywindow.print();
		mywindow.close();
		return true;
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
		var member_deduction_amount = $("#member_deduction_amount").val();
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
		var member_deduction_amount = $("#member_deduction_amount").val();
		if(!member_deduction_amount){
			member_deduction_amount=0;
		}
		$("#totalmemberdeduction").html(member_deduction_amount);
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
		var rexp = /^[\w\-\s\.,ñÑ]+$/
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
	function displayNextInvoice(){
		var inv = parseInt(localStorage['invoice']) +1;
		var invdis = "<span style='color:#000;'>Next Invoice: </span> " + inv;
		$("#nextInvoicenumber").html(invdis);
	}
	function displayNextDr(){
		var inv = parseInt(localStorage['dr']) +1;
		var invdis = "<span style='color:#000;'>Next Dr: </span> " + inv;
		$("#nextDrnumber").html(invdis);
	}
	function refreshQueueList(){
		var queuelist = JSON.parse(localStorage["queueList"]);
		$("#queueselect").empty();
		$("#queueselect").append("<option value=''>--Select Item--</option>");
		for(var q in queuelist){
			var checkMatch= false;
			if(localStorage["onqueue"] !=null){

				var onqueue =JSON.parse(localStorage["onqueue"]);

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

	function updateDiscountAndQtyAll(){
		if($('#cart > tbody  > tr ').length > 0){
			$('#cart > tbody  > tr ').each(function() {
				var row = $(this);
				row.children().eq(0).find('.cartqty').blur();
				row.children().eq(3).find('.cartdiscount').blur();
			});
			updatesubtotal();
		}
	}
	function unBindShortcut(){
		$(document).unbind('keypress');
	}
	function showDiscountQtyAll(){
		if($('#cart tbody tr').length > 0){
			$('#conDiscountQtyAll').slideToggle(300);
		} else {
			showToast('error','<p>No item in cart.</p>','<h3>WARNING!</h3>','toast-bottom-right');
		}
	}
	function is_decimal(v){
		if(v % 1 != 0){
			return true;
		} else {
			return false;
		}
	}
	function formatInventoryQty(v){
		if(is_decimal(v)){
			return number_format(v,3);
		} else {
			return number_format(v);
		}
	}

	function getStoreDiscount(con){
		var store_discount = con.attr('data-store_discount');
		var qty = con.children().eq(0).find('input').val();

		var discountJSON = JSON.parse(con.attr('data-discountJSON'));
		var totalDiscount = 0;
		console.log(qty);
		if(discountJSON.length > 0){
			for(var i in discountJSON){
				var discount_qty = discountJSON[i].for_qty;
				var discount_amount = discountJSON[i].amount;
				var discount_type = discountJSON[i].type;
				if(discount_type==1){
					if(parseFloat(qty) >= parseFloat(discount_qty)){
						var t = Math.floor(parseFloat(qty) / parseFloat(discount_qty));
						totalDiscount = parseFloat(totalDiscount) + (parseFloat(t) * parseFloat(discount_amount));
						console.log(totalDiscount);
					}
				}
				else if(discount_type == 2){
					if(parseFloat(qty) >= parseFloat(discount_qty)){
						totalDiscount = parseFloat(totalDiscount) + parseFloat(discount_amount);
						console.log(totalDiscount);
					}
				}
			}
		}
		var lbldisc = '';
		if(parseFloat(totalDiscount) > 0){
			lbldisc = "*Addtl: " + totalDiscount;
		}

		con.attr('data-store_discount',totalDiscount);
		con.children().eq(3).find('.store_discount').text(lbldisc);

		return (totalDiscount) ? totalDiscount : 0;
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
		$.ajax({
			url: "ajax/ajax_get_members.php",
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
					$("#con_member").attr('disabled',true);
					$("#member_deduction").attr('disabled',true);
				}
			}
		});
	}
	/********* END FUNCTIONS **********/

});