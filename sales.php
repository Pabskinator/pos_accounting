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
<div class="container-fluid" id='mainCon' style='display:none;'>
	<div id="pendingsales">
		<h3>Pending Sales</h3>
		<table class="table" id="saleshistorypending">
			<tr><th>Date</th><th>Invoice</th><th>Dr</th><th>Barcode</th><th>Price</th><th>Quantity</th><th>Discount</th><th>Total</th></tr>
		</table>
	</div>
	<h3>Sales History</h3>
	<p class='text-muted'>Sales for the past three months. For complete list, click <a href='admin/sales.php'>here</a>.</p>
	<!--<div id="no-more-tables">
	<table class="table" id="saleshistory">
	<thead>
	<tr><th>Date</th><th>Terminal</th><th>Invoice</th><th>DR</th><th>PR</th><th>Item Code</th><th>Price</th><th>Quantity</th><th>Discount</th><th>Total</th><th>Cashier</th> <th>Sold To</th><th id='thstationheader'>Station</th></tr>
	</thead>
	</table>
	</div> -->

	<div id="saleshistory2"></div>
	<hr>
	<div class='text-right'>
		<input type="button" style='position:fixed; top:90%;right:5px;opacity:0.8;border-radius:20px;' name='btnGetInvoice' id='btnGetInvoice' class='btn btn-success' value="GET INVOICE"/>
	</div>
</div>
</div>
<script>
$(function(){

	$('#allcontent').show();
	$('.loading').hide();
	$('#thstationheader').html(config_station_label_name);
	if(localStorage["company_name"]){
		$('#postitle').html(localStorage["company_name"].toUpperCase());
	}
	getSalesDisplay(localStorage['branch_id'],localStorage['company_id'],localStorage['terminal_id'],function(){
		if(localStorage['sales']){
			var sales = JSON.parse(localStorage['sales']);
			/*if($("#saleshistorypending").length){
				$("#saleshistory").find("tr:gt(0)").remove();
			}

			for(var c in sales){
				var myinvoice = 0;
				var mydr = 0;
				var myir = 0;
				if(sales[c].invoice != 0){
					myinvoice = sales[c].invoice;
				} else {
					myinvoice = "No invoice <input type='checkbox' class='checkboxInvoice' title='Get Invoice' data-sales_id='"+sales[c].sales_id+"'>";
				}
				if(sales[c].dr != 0){
					mydr = sales[c].dr;
				} else {
					mydr = "No Dr";
				}
				if(sales[c].ir != 0){
					myir = sales[c].ir;
				} else {
					myir = "No Ir";
				}
				sales[c].adjustment = (sales[c].adjustment) ? sales[c].adjustment : 0;
				var discount = parseFloat(sales[c].discount) + parseFloat(sales[c].store_discount);
				var ind_adj = 0;
				if(sales[c].adjustment){
					ind_adj = sales[c].adjustment / sales[c].qtys;
				}
				var total = ((parseFloat(sales[c].qtys) * parseFloat(sales[c].price)) + parseFloat(sales[c].adjustment) ) - parseFloat(discount);
				var price = parseFloat(sales[c].price) + parseFloat(ind_adj);

				var mname ='<span class="glyphicon glyphicon-ban-circle"></span> Not Indicated';
				var station_name = '<span class="glyphicon glyphicon-ban-circle"></span> None';
				var status ='';
				if( sales[c].mln &&  sales[c].mfn){
					 mname = '<span class="glyphicon glyphicon-user"></span> ' + sales[c].mln.toUpperCase() +", " +sales[c].mfn.toUpperCase();
				}
				if(sales[c].station){
					station_name = sales[c].station.toUpperCase();
				}
				if(sales[c].status == 1){
					status = '<br>(Cancelled)';
				}
				$('#saleshistory').append('<tr><td data-title="Date" class="text-danger">'+ sales[c].sold_date +'<span class="text-muted">'+status+'</span></td><td data-title="Terminal"><strong>'+ sales[c].terminal_name +'</strong></td><td data-title="Invoice"><span class="badge">'+ myinvoice +'</span></td><td data-title="Dr"><span class="badge">'+ mydr +'</span></td><td data-title="PR"><span class="badge">'+ myir +'</span></td><td data-title="Item code">'+ sales[c].item_code +'<br><span class="text-muted">'+sales[c].description+'</span></td><td data-title="Price">'+ price.toFixed(2) +'</td><td data-title="Qty">'+ sales[c].qtys +'</td><td data-title="Discount">'+ discount.toFixed(2) +'</td><td data-title="Total">'+ total.toFixed(2) +'</td><td data-title="Cashier"> <span class="glyphicon glyphicon-user"></span> '+ sales[c].lastname.toUpperCase() +", " +sales[c].firstname.toUpperCase() +'</td><td data-title="Member">'+ mname +'</td><td data-title="Station">'+ station_name +'</td></tr>');

			} */ // old sales


			var previnv = 0;
			var prevdr = 0;
			var previr  = 0;
			var retsaleshistory ="";
			var thead = "<tr><th>Item</th><th>Price</th><th>Quantity</th><th>Discount</th><th>Total</th><th>Action</th></tr>";

			for(var c in sales){
				// multiple invoice
				if(previnv != sales[c].payment_id){
					var myinvoice = 0;
					var mydr = 0;
					var myir = 0;
					if(sales[c].invoice != 0){
						myinvoice = sales[c].invoice;
					} else {
						myinvoice = "No invoice <input type='checkbox' class='checkboxInvoice' title='Get Invoice' data-sales_id='"+sales[c].sales_id+"'>";
					}
					if(sales[c].dr != 0){
						mydr = sales[c].dr;
					} else {
						mydr = "No Dr";
					}
					if(sales[c].ir != 0){
						myir = sales[c].ir;
					} else {
						myir = "No Ir";
					}
					sales[c].adjustment = (sales[c].adjustment) ? sales[c].adjustment : 0;
					var discount = parseFloat(sales[c].discount) + parseFloat(sales[c].store_discount);
					var ind_adj = 0;
					if(sales[c].adjustment){
						ind_adj = sales[c].adjustment / sales[c].qtys;
					}
					var total = ((parseFloat(sales[c].qtys) * parseFloat(sales[c].price)) + parseFloat(sales[c].adjustment) ) - parseFloat(discount);
					var price = parseFloat(sales[c].price) + parseFloat(ind_adj);

					var mname ='Not Indicated';
					var station_name = 'None';
					var status ='Sold';
					if( sales[c].mln &&  sales[c].mfn){
						mname = '' + sales[c].mln.toUpperCase() +", " +sales[c].mfn.toUpperCase();
					}
					if(sales[c].station){
						station_name = sales[c].station.toUpperCase();
					}
					if(sales[c].status == 1){
						status = '(Cancelled)';
					}

					previnv = sales[c].payment_id;

					var border ='style="border-top:1px solid #000;"';

					var listli = "<ul class='list-group'>";
					listli += "<li class='list-group-item'><strong>Invoice: </strong> "+myinvoice+"</li>";
					listli += "<li class='list-group-item'><strong>DR: </strong> "+mydr+"</li>";
					listli += "<li class='list-group-item'><strong>PR: </strong> "+myir+"</li>";
					listli += "<li class='list-group-item'><strong>Member:</strong> " + mname+"</li>";
					listli += "<li class='list-group-item'><strong>Station:</strong> " + station_name+"</li>";
					listli += "<li class='list-group-item'>"+status+"</li>";
					listli += "<li class='list-group-item'><button class='btn btn-sm btn-default btnCancel'>Cancel</button> <button class='btn  btn-sm  btn-default btnReturn'>Return</button></li>";

					listli += "</li>";
					if(previnv == 0){
						retsaleshistory += "<div class='panel panel-default'><div class='panel-body'><div class='row'><div class='col-md-4'>"+listli+"</div><div class='col-md-8'><table class='table table-bordered'>"+thead;
					} else {
						retsaleshistory += "</table></div></div></div></div><div class='panel panel-default'><div class='panel-body'><div class='row'><div class='col-md-4'>"+listli+"</div><div class='col-md-8'><table class='table table-bordered'>"+thead;
					}
				} else {
					allinv = '';
					var border='style="border-top:0px solid #000;"';
				}


				retsaleshistory += "<tr><td>"+ sales[c].item_code +"<br><span class='text-muted'>"+sales[c].description+"</span></td><td>"+ price.toFixed(2) +"</td><td>"+ sales[c].qtys +"</td><td>"+ discount.toFixed(2) +"</td><td>"+ total.toFixed(2) +"</td><td></td></tr>";

			}
			$('#saleshistory2').append(retsaleshistory+'</table></div></div></div></div>');

		}
	});
	function getSalesDisplay(branch_id,company_id,terminal_id,callback){
		if(conReachable){
			$.ajax({
				url: "ajax/ajax_get_sales.php",
				type:"POST",
				data:{branch_id:branch_id,company_id:company_id,terminal_id:terminal_id},
				success: function(data){
					if(data){
						localStorage['sales']=data;
						callback();
						showGetInvoice();
					}
				}
			});
		} else {
			callback();
			showGetInvoice();
		}

	}
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

	if(localStorage["sales_pending"]){
		$("#pendingsales").show();
		var pending = JSON.parse(localStorage['sales_pending']);
		if($("#saleshistorypending").length){
			$("#saleshistorypending").find("tr:gt(0)").remove();
		}

		for(var c in pending){
			var eachpending = JSON.parse(pending[c]);
			for(var a in eachpending){
				console.log(eachpending[a]);
				var t = new Date(eachpending[a].sold_date * 1000);
				var month = t.getMonth()+1;
				var day = t.getDate();
				var output = (month<10 ? '0' : '') + month + '/' +
					(day<10 ? '0' : '') + day + '/' + t.getFullYear();
				var myinvoice = 0;
				var mydr = 0;
				if(pending[c][a].invoice != 0 ){
					myinvoice =eachpending[a].invoice;
				} else {
					myinvoice = "No invoice";
				}
				if(pending[c][a].dr  != 0 ){
					mydr = eachpending[a].dr;
				} else {
					mydr = "No Dr";
				}
				var price =  eachpending[a].price;
				var total = eachpending[a].total;
				var discount =  eachpending[a].discount;

				$('#saleshistorypending').append('<tr><td>'+  output +'</td><td>'+ myinvoice +'</td><td>'+ mydr +'</td><td>'+eachpending[a].barcode +'</td><td>'+ price +'</td><td>'+ eachpending[a].qty +'</td><td>'+ discount +'</td><td>'+ total  +'</td></tr>');
			}
		}
	} else {
		$("#pendingsales").hide();
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
	function showGetInvoice(){
		if($('.checkboxInvoice').length > 0){
			$("#btnGetInvoice").show();
		} else {
			$("#btnGetInvoice").hide();
		}
	}


	$('#mainCon').fadeIn();
	$("#btnGetInvoice").click(function(){
		var hasChecked = false;
		var toInvoice = new Array();
		var toPrint = new Array();
		var sales = JSON.parse(localStorage['sales']);
		$(".checkboxInvoice").each(function(){
				if($(this).is(":checked")){
					hasChecked = true;
				}
		});
		if(hasChecked){
			if(confirm("Are you sure you want to print invoice slip with this items?")){
				$(".checkboxInvoice").each(function(index){
					if($(this).is(":checked")){
						var sid = $(this).attr('data-sales_id');
						toInvoice[index] = {
							sales_id : sid
						}
						var ky = "_"+sid;
						toPrint[index] = sales[ky];
					}
				});
				toInvoice = JSON.stringify(toInvoice);
				var curinv = localStorage['invoice'];
				PrintElem(toPrint);
				$.ajax({
					url: "ajax/ajax_get_invoice.php",
					type: "POST",
					data: {terminal_id: localStorage['terminal_id'],sales:toInvoice,invoice_number:curinv },
					success: function(data){

						localStorage['invoice'] = parseInt(localStorage['invoice']) + 1;
						location.reload();
					},
					error: function(){
						alert("Error");
						location.reload();
					}
				});
			}
		} else {
			showToast('error','<p>No items selected</p>','<h3>WARNING!</h3>','toast-bottom-left');
		}
	});

	function PrintElem(toPrint)
	{

		var d = new Date();
		var month = d.getMonth()+1;
		var day = d.getDate();
		var output = (month<10 ? '0' : '') + month + '/' +
			(day<10 ? '0' : '') + day + '/' + d.getFullYear();

		var printhtml="";
		var toprintindexzero = toPrint[0];
		var description = toprintindexzero.description;
		var membername = toprintindexzero.mln.toUpperCase() + ', ' + toprintindexzero.mfn.toUpperCase();
		var stationname =  toprintindexzero.station;

		printhtml= printhtml +  "<div style='position:absolute;top:100px; right:70px;'><b> <br/>"+  output+ " </b></div><div style='clear:both;'></div>";
		printhtml= printhtml +  "<div style='position:absolute;top:205px; left:100px;'><b>"+membername+"</b></div>";
		printhtml= printhtml +  "<div style='position:absolute;top:270px; left:100px;'><b>"+stationname+"</b></div>";
		printhtml= printhtml +  "<div style='position:absolute;top:205px; left:470px;'><b></b></div>";
		printhtml= printhtml + "<table id='itemscon' style='position:absolute;top:425px;left:20px;'> ";

		var grandtotal = 0;
		var vat = 1.12;
		var subtotal = 0;
		for(item in toPrint){
			var total = (parseFloat(toPrint[item].qtys) * parseFloat(toPrint[item].price)) - toPrint[item].discount;
			if(parseFloat(toPrint[item].discount) > 0){

				var perunitdisc =  parseFloat(toPrint[item].discount) / parseFloat(toPrint[item].qtys);
				perunitdisc = number_format(perunitdisc,2);
				var labeldisc = "<br/>(Disc. " + perunitdisc + ")";
				var totaldiscount = number_format(toPrint[item].discount,2);
				var labeldisc2 = "<br/>("+totaldiscount+")";
			} else {
				var labeldisc ='';
				var labeldisc2 ='';
			}
				printhtml = printhtml + "<tr><td style='width:100px;'>"+toPrint[item].item_code+"</td><td style='width:90px;'>"+toPrint[item].qtys+"</td><td style='width:330px;'>"+description+"  <span style='padding-left:20px;'>"+labeldisc+"</span></td><td style='width:100px;'>"+number_format(toPrint[item].price,2)+"</td><td style='width:150px;'>"+number_format(total,2)+" "+labeldisc2+"</td></tr>";
			grandtotal += total;
		}
		subtotal = (grandtotal / vat);
		vat = parseFloat(grandtotal) - parseFloat(subtotal);
		subtotal = subtotal.toFixed(2);
		vat = vat.toFixed(2);
		grandtotal = grandtotal.toFixed(2);
		printhtml = printhtml + "</table>";
		printhtml = printhtml + "<ul style='position:absolute; list-style-type: none; right:130px;top:830px;'><li><b>"+subtotal+"</b></li><li><b>"+vat+"</b></li><li>&nbsp;</li><li><b>"+grandtotal+"</b></li></ul>";
		printhtml = printhtml + "<div style='position:absolute;left:40px;top:960px;'>"+localStorage['current_lastname'] + ", "  + localStorage['current_firstname'] +"</div>";
		Popup(printhtml);
	}
	function Popup(data)
		{
			var mywindow = window.open('', 'new div', '');
			mywindow.document.write('<html><head><title></title><style></style>');
			/*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
			mywindow.document.write('</head><body style="padding:0;margin:0;" >');
			mywindow.document.write(data);
			mywindow.document.write('</body></html>');
			mywindow.print();
			mywindow.close();
			return true;
		}
	getCountShouts();
})
</script>
<?php require_once 'includes/page_tail.php'; ?>
