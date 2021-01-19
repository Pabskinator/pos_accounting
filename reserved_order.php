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
					<li id='shoutnav' style='display:none;'  ><a href="shoutbox/index.html">Message(<span id='ctrshout'>0</span>)</a></li>
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
	<h3>Reserved Orders</h3>
	<hr />
	<div id="orderholder"></div>

	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
		<div class="modal-dialog" style='width:90%;'>
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title" id='mtitle'></h3>
				</div>
				<div class="modal-body" id='mbody'>

				</div>

			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
</div>
</div>
<script>
$(function(){

	$('#allcontent').fadeIn();
	if(localStorage["company_name"]){
		$('#postitle').html(localStorage["company_name"].toUpperCase());
	}
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
	getAllStations(localStorage['company_id']);
	getBranches(localStorage['company_id']);
	getSalesTypeAx(localStorage['company_id']);
	getOrderOffline(localStorage['company_id'],localStorage['branch_id'],displayReserveOrderList);
	getProducts(localStorage["company_id"],localStorage["branch_id"],localStorage["terminal_id"],false,function(){
		$('.loading').hide();
	});
	var ajaxOnProgress = false;
	function displayReserveOrderList(){
	if(localStorage['reserved_order'] != null || localStorage['reserved_order'] !=undefined || localStorage['reserved_order'] !=''){

		var order = JSON.parse(localStorage['reserved_order']);
		if(order){
			var htmlstring = '<table class="table">';
			htmlstring += "<thead><tr><th>Order Id</th><th>Created</th><th>Reserved by</th><th>Member</th><th>Details</th><th></th></tr></thead>";
			htmlstring += "<tbody>";
			for(var c in order){
				if(isMember() && order[c].user_id != localStorage['current_id']) continue;
				var retstat = '';
				if(order[c].stations){
					var statall = order[c].stations;
					if(statall.indexOf('::') > 0){
						var explodestat= statall.split('::');
						for(var exploded in explodestat){
							retstat = retstat + " <span style='font-size:0.8em' class='label label-primary'>"+explodestat[exploded]+"</span> ";
						}
					} else {
						retstat = "<span style='font-size:0.8em' class='label label-primary'>"+statall+"</span>";
					}
				}
				var payment_cash =order[c].payment_cash;
				var payment_con =order[c].payment_consumable;
				var payment_bt =order[c].payment_bt;
				var payment_cheque =order[c].payment_cheque;
				var payment_con_freebies = order[c].payment_consumable_freebies;
				var payment_member_credit= order[c].payment_member_credit;
				var payment_credit =order[c].payment_credit_card;

				var withpayment = false;
				if(payment_cash || payment_con || payment_con_freebies || payment_bt || payment_cheque || payment_credit || payment_member_credit){
					withpayment = true;

				}
				var labelwithpayment = "";
				if(withpayment){
					labelwithpayment = "With payment";
				}
				htmlstring += "<tr><td><span class='badge'>"+order[c].order_id+"</span></td><td>"+order[c].date_ordered+"</td><td class='text-danger'>"+order[c].fullname+"<br><small>"+order[c].remarks+"</small></td><td>"+order[c].member_name+"<br>"+retstat+"</td>";
				var details = JSON.parse(order[c].jsonitem);
				var detstring ='';
				detstring += "<table data-reserved_by='"+order[c].fullname+"' class='table' >";
				detstring += "<tr><th>#</th><th>Item</th><th>Description</th><th class='text-right'>Qty</th><th class='text-right'>Price</th><th class='text-right'>Discount</th><th class='text-right'>Total</th><th></th></tr>";
				var totalreservation = 0;
				var detailsctr = 1;
				for(var i in details){
					var ss = '';
					var jsonss_ret ='';
					var jsonbranch_ret ='';
					if(details[i].ss_json){
						ss = JSON.parse(details[i].ss_json);
						jsonss_ret = "<table class='table' style='font-size:0.8em;'>";
						jsonss_ret += "<tr><td colspan='4' class='text-danger' style='font-weight:bolder;'>Station Qty Allocation for "+details[i].description+"</td></tr>";
						for(var h in ss){
							var stationname = getStationName(ss[h].stationid);
							var salestypename = getSalesTypeName(ss[h].memberid);
							jsonss_ret = jsonss_ret + "<tr><td style='border-top:none;'>Station: <strong class='text-danger'>"+stationname+"</strong></td> <td style='border-top:none;'>Sales Type:<strong class='text-danger'>"+salestypename+"</strong></td> <td style='border-top:none;'>Qty:<strong class='text-danger'> "+ss[h].qty+"</strong></td></tr>"
						}
						jsonss_ret = jsonss_ret+  "</table>";
					}
					if(details[i].branch_json){
						ssbranch = JSON.parse(details[i].branch_json);
						jsonbranch_ret = "<table class='table' style='font-size:0.8em;'>";
						jsonbranch_ret += "<tr><td colspan='2' class='text-danger' style='font-weight:bolder;'>Branch Qty Allocation for "+details[i].description+"</td></tr>";
						for(var g in ssbranch){
							var bname = getBranchName(ssbranch[g].branch_id);
							jsonbranch_ret = jsonbranch_ret + "<tr><td style='border-top:none;'>Branch: <strong class='text-danger'>"+bname+"</strong></td> <td style='border-top:none;'>Qty: <strong class='text-danger'>"+ssbranch[g].qty+"</strong></td> </tr>"
						}
						jsonbranch_ret = jsonbranch_ret+  "</table>";
					}
					totalreservation = parseFloat(totalreservation) + (parseFloat(details[i].total) - parseFloat(details[i].discount));
					detstring += "<tr><td><span class='badge'>"+detailsctr+"</span></td><td >"+details[i].item_code+"</td><td class='text-muted'>"+details[i].description+"</td><td class='text-right'>"+details[i].qty+"</td><td class='text-right text-danger' >"+number_format(details[i].price,2)+"</td><td class='text-right' >"+number_format(details[i].discount,2)+"</td><td class='text-right' style='font-weight: bolder;'>"+number_format(details[i].total-details[i].discount,2)+"</td><td></td></tr>";
					detailsctr = parseInt(detailsctr) + 1;
					if(jsonss_ret){
						detstring += "<tr><td colspan='8'>"+jsonss_ret+"</td></tr>";
					}
					if(jsonbranch_ret){
						detstring += "<tr><td colspan='8'>"+jsonbranch_ret+"</td></tr>";
					}
				}
				detstring +="</table>";
				detstring += "<hr>";
				detstring += "<p class='text-right' style='margin-right:35px;'>Total Sales: <span class='text-danger'><strong>"+number_format(totalreservation,2)+"</strong></span></p>";
				detstring += "<hr>";

				if(!isMember()) detstring += "<div class='text-right'><button  class='btn btn-default processorder' data-order_id='"+order[c].order_id+"' ><span class='glyphicon glyphicon-cog'></span> Process</button></div>";

				htmlstring += "<td><div style='display:none;'>"+detstring+"</div><button class='btn btn-default getDetailsOrder'><span class='glyphicon glyphicon-list'></span> Details</button>";
				htmlstring += "</td>";
				htmlstring += "<td>"+labelwithpayment+"</td>";
				htmlstring += "</tr>";
			}
			htmlstring += "</tbody>";
			htmlstring += "</table>";
			$('#orderholder').html(htmlstring);
		}
	}
}
	$('body').on('click','.getDetailsOrder',function(){
		var prevdiv = $(this).prev();
		$('#mbody').html(prevdiv.html());
		$('#myModal').modal('show');
	});
	$('body').on('click','.processorder',function(){
		$('.loading').show();
		var order_id =$(this).attr('data-order_id');

		var reserved = JSON.parse(localStorage['reserved_order']);
		var items = JSON.parse(localStorage['items']);
		var order = reserved["_"+order_id];

		var member_id = order.member_id;
		var station_id = order.station_id;
		var salestype = order.sales_type;
		var remarks = order.remarks;
		var jsonitem = JSON.parse(order.jsonitem);

		var payment_cash =order.payment_cash;
		var payment_con =order.payment_consumable;
		var payment_bt =order.payment_bt;
		var payment_cheque =order.payment_cheque;
		var payment_con_freebies = order.payment_consumable_freebies;
		var payment_member_credit= order.payment_member_credit;
		var payment_credit =order.payment_credit_card;
		var withpayment = 0;
		if(payment_cash || payment_con || payment_con_freebies || payment_bt || payment_cheque || payment_credit || payment_member_credit){
			withpayment = 1;
			if(payment_cash)  localStorage['payment_cash'] = payment_cash;
			if(payment_con) localStorage['payment_con'] = payment_con;
			if(payment_bt) localStorage['payment_bt'] = payment_bt;
			if(payment_cheque) localStorage['payment_cheque'] = payment_cheque;
			if(payment_con_freebies) localStorage['payment_con_freebies']=payment_con_freebies;
			if(payment_member_credit) localStorage['payment_member_credit']= payment_member_credit;
			if(payment_member_credit) localStorage['payment_credit'] = payment_member_credit;
		}

		var rettable = '';
		var valid = true;
		console.log(jsonitem);
		for(var i in jsonitem){
			var barcode = jsonitem[i].barcode;
			var item_id = jsonitem[i].item_id;
			var item_type = jsonitem[i].item_type;
			var qty= jsonitem[i].qty;
			var discount= jsonitem[i].discount;
			var inditem = items[barcode];
			console.log(inditem);
			if(!inditem){
				valid = false;
				break;
			}
			var ssjson='';
			var export_ss = '';
			var e_branch_json='';
			var tocheckqty = 0;
			if(jsonitem[i].ss_json){
				var ssjson =jsonitem[i].ss_json;
				 export_ss=	"<input type='hidden' value='"+ssjson+"' id='hid_multiple_ss"+item_id+"'><span  style='margin-right:8px;' id='spanmultipless"+item_id+"' class='glyphicon glyphicon-folder-open text-success'></span>";
			}
			if(jsonitem[i].branch_json) {
				var branch_json = jsonitem[i].branch_json;
				e_branch_json = "<input type='hidden' value='" + branch_json + "' id='hid_allocatebranch" + item_id + "'><span  style='margin-right:8px;' id='span_allocatebranch" + item_id + "' class='glyphicon glyphicon-map-marker text-success'></span>";
				var toloopbranchjson = JSON.parse(branch_json);
				for(var is in toloopbranchjson){
					if(parseInt(toloopbranchjson[is].qty) > 0 && toloopbranchjson.branch_id == localStorage['branch_id']){
						tocheckqty = parseInt(tocheckqty) + parseInt(toloopbranchjson[is].qty);
					}
				}
			}
			if(!inditem){
				valid = false;

			}

			if(item_type == -1){
				if(!inditem.qty){
					valid = false;

				}
				if(parseInt(qty) > parseInt(inditem.qty)){
					if(!tocheckqty && !e_branch_json){
						valid = false;
					} else if(parseInt(tocheckqty) > parseInt(inditem.qty)){
						valid = false;
					}

				}
			}

			if(!discount){
				discount = 0;
			}
			var qtyprice = (parseFloat(qty) * parseFloat(inditem.price)) - discount;
			rettable += "<tr data-store_discount='0' data-reserved_by='"+order.fullname+"' data-itemcode='"+inditem.item_code+"' data-desc='"+inditem.description+"' data-order_id='"+order_id+"' id='"+item_id+"' c-qty='"+inditem.cqty+"' c-days='"+inditem.cdays+"' data-barcode='"+barcode+"' > <td><input readonly type='text' class='form-control circletextbox cartqty' value='"+qty+"'></td>	<td>"+inditem.item_code+"<br><small class='text-danger'>"+inditem.description+"</small></td><td id='"+inditem.price_id+"'>"+inditem.price+"</td><td><input type='text' class='form-control circletextbox cartdiscount' disabled value='"+discount+"'></td><td>"+ number_format(qtyprice,2) +"</td><td>"+export_ss+" "+e_branch_json+"</td></tr>";
		}
		if(valid){
			localStorage['outReservation'] = rettable;
			localStorage['outReservationMember'] = member_id;
			localStorage['outReservationSalestype'] = salestype;
			localStorage['outReservationStation'] = station_id;
			localStorage['outReservationRemarks'] = remarks;
			localStorage['outWithPayment'] = withpayment;
			location.href = "index.php";
		} else {
			showToast('error','<p>Unable to process due to lack of stocks.</p>','<h3>WARNING!</h3>','toast-bottom-right');
			$('.loading').hide();
		}

	});
	savePendingOrder();

	function savePendingOrder(){
		if(localStorage["reservation_pending"] != null){
			if(con.hostReachable()){
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

	getCountShouts();
	function getStationName(id){
		if(id){
			var stations = JSON.parse(localStorage['stations']);
			for(var i in stations){
				if(stations[i].id == id ){
					return stations[i].name;
				}
			}
			return 'None';
		} else {
			return 'None';
		}

	}
	function getMemberName(id){
		if(id){
			var members = JSON.parse(localStorage['members']);
			for(var i in members){
				if(members[i].id == id ){
					return members[i].lastname+ ", "+ members[i].firstname;
				}
			}
			return 'None';
		} else {
			return 'None';
		}
	}
	function getBranchName(id){
		if(id){
			var branches = JSON.parse(localStorage['branch_list']);
			for(var i in branches){
				if(branches[i].id == id ){
					return branches[i].name;
				}
			}
			return 'None';
		} else {
			return 'None';
		}
	}
	function getSalesTypeName(id){
		if(id){
			var salestype = JSON.parse(localStorage['sales_type_json']);
			for(var i in salestype){
				if(salestype[i].id == id ){
					return salestype[i].name;
				}
			}
			return 'None';
		} else {
			return 'None';
		}
	}
});
</script>
<?php require_once 'includes/page_tail.php'; ?>
