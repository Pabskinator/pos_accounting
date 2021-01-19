<?php

	include 'branches.php';




	$arr_items = [

		['sku' => '325147', 'item_code' => 'DOG SHA_ST ROCHE ORG HS 250ml ', 'price' => 169],
		['sku' => '326265', 'item_code' => 'DOG SHA_ST ROCHE ORG HP 250ML ', 'price' => 169],
		['sku' => '326266', 'item_code' => 'DOG SHA_ST ROCHE ORG MN 250ml ', 'price' => 169],
		['sku' => '326267', 'item_code' => 'DOG SHA_ST ROCHE ORG SE 250ml ', 'price' => 169],
		['sku' => '325148', 'item_code' => 'DOG SHA_ST ROCHE ORG HP1050ML ', 'price' => 469],
		['sku' => '326268', 'item_code' => 'DOG SHA_ST ROCHE ORG HS1050ML ', 'price' => 469],
		['sku' => '326269', 'item_code' => 'DOG SHA_ST ROCHE ORG MN1050ML ', 'price' => 469],
		['sku' => '326270', 'item_code' => 'DOG SHA_ST ROCHE ORG SE1050ML ', 'price' => 469],
		['sku' => '326271', 'item_code' => 'DOG SHA_ST ROCHE ORG HP 4L', 'price' => 1249],
		['sku' => '325149', 'item_code' => 'DOG SHA_ST ROCHE ORG HS 4L', 'price' => 1249],
		['sku' => '326272', 'item_code' => 'DOG SHA_ST ROCHE ORG MN 4L', 'price' => 1249],
		['sku' => '326273', 'item_code' => 'DOG SHA_ST ROCHE ORG SE 4L', 'price' => 1249],
		['sku' => '326274', 'item_code' => 'DOG SHA_ST ROCHE DRY HP 150ML ', 'price' => 249],
		['sku' => '325150', 'item_code' => 'DOG SHA_ST ROCHE DRY HS 150ML ', 'price' => 249],
		['sku' => '326275', 'item_code' => 'DOG SHA_ST ROCHE DRY MN 150ML ', 'price' => 249],
		['sku' => '326276', 'item_code' => 'DOG SHA_ST ROCHE DRY-SE 150ML', 'price' => 249],
		['sku' => '325151', 'item_code' => 'DOG SHA_ST ROCHE BA-CS 1000ML', 'price' => 329],
		['sku' => '326277', 'item_code' => 'DOG SHA_ST ROCHE BA-LL 1000ML ', 'price' => 329],
		['sku' => '326278', 'item_code' => 'DOG SHA_ST ROCHE BA-PB 1000ML', 'price' => 329],
		['sku' => '326279', 'item_code' => 'DOG SHA_ST ROCHE BA-SL 1000ML ', 'price' => 329],
		['sku' => '326280', 'item_code' => 'DOG ETD_ST ROCHE-HP 125ML ', 'price' => 219],
		['sku' => '325152', 'item_code' => 'DOG ETD_ST ROCHE-HS 125ML ', 'price' => 219],
		['sku' => '326281', 'item_code' => 'DOG ETD_ST ROCHE-MN 125ML ', 'price' => 219],
		['sku' => '326282', 'item_code' => 'DOG ETD_ST ROCHEE-SE 125ML ', 'price' => 219],
		['sku' => '325156', 'item_code' => 'DOG CNDR_ST ROCHE ORG-HP500ML ', 'price' => 379],
		['sku' => '326283', 'item_code' => 'DOG CNDR_ST ROCHE ORG-HS500ML ', 'price' => 379],
		['sku' => '326284', 'item_code' => 'DOG CNDR_ST ROCHE ORG-MN500ML ', 'price' => 379],
		['sku' => '326285', 'item_code' => 'DOG CNDR_ST ROCHE ORG-SE500ML ', 'price' => 379],
		['sku' => '326286', 'item_code' => ' DOG SOAP ST ROCHE ORG HP 135G', 'price' => 179],
		['sku' => '325157', 'item_code' => ' DOG SOAP ST ROCHE ORG-HS 135G', 'price' => 179],
		['sku' => '326287', 'item_code' => ' DOG SOAP ST ROCHE ORG-MN 135G', 'price' => 179],
		['sku' => '326288', 'item_code' => ' DOG SOAP ST ROCHE ORG-SE 135G', 'price' => 179],
		['sku' => '325174', 'item_code' => 'CATSHA_ST GERTIE ORG-HP 250ML ', 'price' => 149],
		['sku' => '326289', 'item_code' => 'CATSHA_ST GERTIE ORG-HS 250ML ', 'price' => 149],
		['sku' => '326290', 'item_code' => 'CATSHA_ST GERTIE ORG-MN 250ML ', 'price' => 149],
		['sku' => '326291', 'item_code' => 'CATSHA_ST GERTIE ORG-SE 250ML ', 'price' => 149],
		['sku' => '326292', 'item_code' => 'CATSHA_ST GERTIE ORG-HP 1050ML', 'price' => 399],
		['sku' => '325186', 'item_code' => 'CATSHA_ST GERTIE ORG-HS 1050ML', 'price' => 399],
		['sku' => '326293', 'item_code' => 'CATSHA_ST GERTIE ORG-MN 1050ML', 'price' => 399],
		['sku' => '326294', 'item_code' => 'CATSHA_ST GERTIE ORG-SE 1050ML', 'price' => 399],
		['sku' => '325189', 'item_code' => 'DOG SHA_FURMAGIC-BL 1000ML ', 'price' => 499],
		['sku' => '325192', 'item_code' => 'DOG SHA_FURMAGIC-PK 1000ML ', 'price' => 499],
		['sku' => '325193', 'item_code' => 'DOG SHA_FURMAGIC-VL 1000ML ', 'price' => 499],
		['sku' => '325194', 'item_code' => 'DOG SHA_FURMAGIC-PK 300ML ', 'price' => 219],
		['sku' => '326295', 'item_code' => 'DOG SHA_FURMAGIC-BL 300ML ', 'price' => 219],
		['sku' => '325195', 'item_code' => 'DOG SHA_FURMAGIC-VL 300ML ', 'price' => 219],
		['sku' => '325197', 'item_code' => 'DOG SHA_FURMAGIC-PK 600ML ', 'price' => 399],
		['sku' => '325196', 'item_code' => 'DOG SHA_FURMAGIC-BL 600ML ', 'price' => 399],
		['sku' => '325198', 'item_code' => 'DOG SHA_FURMAGIC-VL 600ML ', 'price' => 399],
		['sku' => '325199', 'item_code' => 'DOG SOAP_FURMAGIC-BL 135GM ', 'price' => 219],
		['sku' => '326297', 'item_code' => 'DOG SOAP_FURMAGIC-VL 135GM ', 'price' => 219],
		['sku' => '326306', 'item_code' => 'DOG SHA_ST ROCHE UTR AG1020ml ', 'price' => 719.75],
		['sku' => '326307', 'item_code' => 'DOG SHA_ST ROCHE UTR FI1020ml ', 'price' => 719.75],
		['sku' => '326308', 'item_code' => 'DOG SHA_ST ROCHE UTR PR1020ml ', 'price' => 719.75],
		['sku' => '326309', 'item_code' => 'DOG SHA_ST ROCHE UTR ST1020ml ', 'price' => 719.75],



	];
?>





<!DOCTYPE html>
<html lang="en">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="UTF-8">
	<title>Document</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<link href="../css/select2.css" rel="stylesheet">
	<link href="../css/select2_bootstrap.css" rel="stylesheet">
	<script
		src="https://code.jquery.com/jquery-3.3.1.min.js"
		integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
		crossorigin="anonymous"></script><script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	<script src="../js/select2.js"></script>
	<style>
		#imgInp{
			width:200px;
		}
		#blah,#pic1,#pic2{
			height:220px;
			width:auto;
			margin:5px;
		}
		#pic1,#pic2{
			margin: 0 auto;
		}
		body{

		}
		.img-con{
			overflow-x:hidden;
			text-align: center;
		}
		#btn-action{
			display:none;
		}
		.linePreloader{
			width:200px;
			height:4px;
			background:linear-gradient(to right,green,green);
			background-color:#ccc;
			display:none;
			margin:auto;
			border-radius:4px;
			background-size:20%;
			background-repeat:repeat-y;
			background-position:-25% 0;
			animation:scroll 1.2s ease-in-out infinite;
			margin-top:5px;
			margin-bottom:5px;
		}

		@keyframes scroll{
			50%{background-size:80%}
			100%{background-position:125% 0;}
		}
		@media only screen and (max-width: 800px) {

			/* Force table to not be like tables anymore */
			#no-more-tables table,
			#no-more-tables thead,
			#no-more-tables tbody,
			#no-more-tables th,
			#no-more-tables td,
			#no-more-tables tr {
				display: block;
			}

			/* Hide table headers (but not display: none;, for accessibility) */
			#no-more-tables thead tr {
				position: absolute;
				top: -9999px;
				left: -9999px;
			}

			#no-more-tables tr {
				border: 1px solid #ccc;
			}

			#no-more-tables td {
				/* Behave  like a "row" */
				border: none;
				border-bottom: 1px solid #eee;
				position: relative;
				padding-left: 50%;
				white-space: normal;
				text-align: left;
			}

			#no-more-tables td:before {
				/* Now like a table header */
				position: absolute;
				/* Top/left values mimic padding */
				top: 6px;
				left: 6px;
				width: 45%;
				padding-right: 10px;
				white-space: nowrap;
				text-align: left;
				font-weight: bold;
			}

			/*
			Label the data
			*/
			#no-more-tables td:before {
				content: attr(data-title);
			}
		}
		.navbar{
			margin-bottom: 10px;
		}
		/* The snackbar - position it at the bottom and in the middle of the screen */
		#snackbar {
			visibility: hidden; /* Hidden by default. Visible on click */
			min-width: 250px; /* Set a default minimum width */
			margin-left: -125px; /* Divide value of min-width by 2 */
			background-color: #333; /* Black background color */
			color: #fff; /* White text color */
			text-align: center; /* Centered text */
			border-radius: 2px; /* Rounded borders */
			padding: 16px; /* Padding */
			position: fixed; /* Sit on top of the screen */
			z-index: 1; /* Add a z-index if needed */
			left: 50%; /* Center the snackbar */
			bottom: 30px; /* 30px from the bottom */
		}

		/* Show the snackbar when clicking on a button (class added with JavaScript) */
		#snackbar.show {
			visibility: visible; /* Show the snackbar */

			/* Add animation: Take 0.5 seconds to fade in and out the snackbar.
			However, delay the fade out process for 2.5 seconds */
			-webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
			animation: fadein 0.5s, fadeout 0.5s 2.5s;
		}

		/* Animations to fade the snackbar in and out */
		@-webkit-keyframes fadein {
			from {bottom: 0; opacity: 0;}
			to {bottom: 30px; opacity: 1;}
		}

		@keyframes fadein {
			from {bottom: 0; opacity: 0;}
			to {bottom: 30px; opacity: 1;}
		}

		@-webkit-keyframes fadeout {
			from {bottom: 30px; opacity: 1;}
			to {bottom: 0; opacity: 0;}
		}

		@keyframes fadeout {
			from {bottom: 30px; opacity: 1;}
			to {bottom: 0; opacity: 0;}
		}
	</style>
	<script>
		function showStackbar(msg) {
			// Get the snackbar DIV
			var x = document.getElementById("snackbar")

			// Add the "show" class to DIV
			x.className = "show";
			$('#snackbar').html(msg);
			// After 3 seconds, remove the show class from DIV
			setTimeout(function(){ x.className = x.className.replace("show", ""); }, 1000);
		}
		function number_format(number, decimals, dec_point, thousands_sep) {

			number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
			var n = !isFinite(+number) ? 0 : +number, prec = !isFinite(+decimals) ? 0 : Math.abs(decimals), sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep, dec = (typeof dec_point === 'undefined') ? '.' : dec_point, s = '', toFixedFix = function(n, prec) {
				var k = Math.pow(10, prec);
				return '' + (Math.round(n * k) / k).toFixed(prec);
			};

			s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
			if(s[0].length > 3) {
				s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
			}
			if((s[1] || '').length < prec) {
				s[1] = s[1] || '';
				s[1] += new Array(prec - s[1].length + 1).join('0');
			}
			return s.join(dec);
		}
	</script>
</head>
<body>
<div id="snackbar"></div>
<nav class="navbar navbar-expand-lg navbar-light bg-dark"    >
	<a class="navbar-brand text-white" href="../admin/index.php">Order Item</a>
	<button class="navbar-toggler text-white" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon text-white" ></span>
	</button>
	<div class="collapse navbar-collapse" id="navbarNavAltMarkup">
		<div class="navbar-nav">


		</div>
	</div>
</nav>
<input type="hidden" id='dt' value='<?php echo date('m/d/Y'); ?>'>
<div class="container">
	<br>
	<div class="row">
		<div class="col-md-4">
			<div class="card">
				<div class="card-body">
					<div class="form-group">
						<strong>Item Name</strong>

						<select name="item_name" id="item_name" class='form-control'>
							<option value=""></option>
							<?php  foreach($arr_items as $i){
								echo "<option value='$i[sku]'>$i[item_code]</option>";
							} ?>
						</select>
					</div>
					<div class="form-group">
						<strong>Qty</strong> <input autocomplete="off" type="number" class='form-control' id='qty' placeholder='Enter Quantity'>
					</div>
					<div class="form-group">
						<strong>Price</strong> <input readonly autocomplete="off" type="number" class='form-control' id='price' placeholder='Enter Price'>
					</div>
					<div class="form-group">
					<button id='addItem' class='btn btn-primary'>Add Item</button>
					</div>
				</div>
			</div>

		</div>
		<div class="col-md-8">
			<div class="card">
				<div class="card-body">
					<div id='no_record'><div class='alert alert-info'>No item yet.</div></div>
					<div id="cart_body">
						<div class="alert alert-info">
							Notes:
							<ul>
								<li>Maximum of 10 items per DR</li>
								<li>Print to legal-size paper</li>
							</ul>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<select class='form-control' name="branch_id" id="branch_id">
										<option value=""></option>
										<?php
											foreach($branches as $b){
												echo "<option value='$b[address]'>$b[nome] (".$b['storecode'].")</option>";
											}
										?>
									</select>
								</div>
							</div>
						</div>
						<table id='cart' class='table table-bordered'>
							<thead>
							<tr>
								<th>Qty</th>
								<th>Unit</th>
								<th>Item</th>
								<th>Price</th>
								<th>Total</th>
								<th></th>
							</tr>
							</thead>
							<tbody>

							</tbody>
						</table>
						<p class='text-danger' id='total_label'></p>
						<div class='row'><div class="col-md-12 text-right">
								<button id='btnSubmit' class='btn btn-primary'>Submit</button>
							</div></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="con"></div>
	<br><br>
</div>
<script>

	$(function(){
		var items = [];
		try {
			items = JSON.parse('<?php echo json_encode($arr_items); ?>');
			console.log(items);
		} catch(e){

		}
		$('#item_name').select2(
			{placeholder: 'Select Item'}
		);

		$('body').on('change','#item_name',function(){
			var sku = $(this).val();
			for(var i in items){
				if(items[i].sku == sku){
					$('#price').val(items[i].price);
					break;
				}
			}
		});

		$('#branch_id').select2({placeholder: 'Select Branch'});

		$('body').on('click','#btnSubmit',function(){
			var branch_name = $('#branch_id > option:selected').text();
			var branch_address = $('#branch_id').val();
			if(branch_address){
				if(confirm("Are you sure you want to submit this request?")){

					printDiv(branch_name,branch_address);
				}

			} else {
				alert("Please choose branch.");
			}

		});

		function printDiv(branch_name,branch_address){

			var arr_items = [];
				var dt =$('#dt').val();
			$('#cart > tbody > tr').each(function(){
				var row = $(this);
				var qty = row.children().eq(0).text();
				var unit = row.children().eq(1).text();
				var item_name = row.children().eq(2).text();
				var price = row.children().eq(3).text();
				var total = row.children().eq(4).text();
				arr_items.push(
					{qty:qty,unit:unit,item_name:item_name,price:price,total:total}
				);
			});


			var $bor = "style='border-top-style:solid;border-bottom-style:solid;border-left-style:solid;border-right-style:solid;border:1px solid black;'";
			var $tbor = "style='border-top : 1px solid black;border-bottom : 1px solid black;border-collapse:collapse;'";
			var $tborin = "style='border-top : 1px solid black;border-bottom : 1px solid black;border-right : 1px solid black;'";
			var $totdr = '';
			var $id = '1';


			$.ajax({
				url:'process.php',
				type:'POST',
				data: {functionName:'saveOrder',branch_name:branch_name ,items:JSON.stringify(arr_items)},
				success: function(data){
					var $drf = "<table style='border-collapse:collapse;' cellpadding=2><tr><td width='80'></td><td STYLE='font-family:\"arial\";font-size:14pt' colspan='3' align='center'><strong>MUTUAL SUCCESS LIGHTING FIXTURE INC.</strong></td><td width='150'></td></tr>";
					$drf += "<tr><td></td><td width='80'></td><td STYLE='font-family:\"arial\";font-size:9pt' width='400' align='center'>"+$totdr+" 6096 BLK. 7, Lot1, Kowloon Ind'l. Subd.</td><td width='120'></td><td></td></tr>";
					$drf += "<tr><td></td><td></td><td STYLE='font-family:\"arial\";font-size:9pt' align='center'>Bo. Ugong, Valenzuela City</td><td></td><td></td></tr>";
					$drf += "<tr><td></td><td></td><td></td><td colspan='2' STYLE='font-family:\"Bookman Old Style\";font-size:14pt' align='right'><strong>DR</strong> No. <font size='5'>" + data + "</font></td></tr>";
					$drf += "<tr><td></td><td></td><td STYLE='font-family:\"Bookman Old Style\";font-size:12pt' align='center'>DELIVERY RECEIPT</td><td></td><td></td></tr>";
					$drf += "<br />";
					$drf += "<tr><td STYLE='font-family:\"Bookman Old Style\";font-size:9pt'>Delivered to</td><td STYLE='font-family:\"arial\"; font-size:11pt;border-bottom: 1px solid #000;' valign='middle' colspan='2'><strong>"+branch_name+"</strong> ";  $drf+="</td><td STYLE='font-family:\"Bookman Old Style\";font-size:9pt' colspan='2'> Date <span STYLE='font-family:\"arial\"; font-size:11pt;border-bottom: 1px solid #000;display:inline-block;width:210px;margin-left: 20px;'><strong>"+dt+"</strong></span> </td></tr>";
					$drf += "<tr><td STYLE='font-family:\"Bookman Old Style\";font-size:9pt'>Address</td><td STYLE='font-family:\"arial\"; font-size:11pt;border-bottom:1px solid #000;' colspan='2'><strong>"+branch_address+"</strong></td><td STYLE='font-family:\"Bookman Old Style\";font-size:9pt' colspan='2'></td></tr></table>";
					$drf += "<table style='border-collapse:collapse;' cellpadding=2><tr align='center' STYLE='font-family:\"Bookman Old Style\";font-size:9pt'><td "+$tborin+" width='80'>QUANTITY</td><td "+$tborin+" width='80'>UNIT</td><td width='400' "+$tborin+">ARTICLES</td><td "+$tborin+" width='120'>UNIT PRICE</td><td width='150' "+$tbor+">AMOUNT</td></tr>";
					var grand_total = 0;
					var total_qty = 0;
					for(var i=0;i<11;i++){
						var cur = arr_items[i];
						var cur_qty = "";
						var cur_unit = "";
						var cur_item = "";
						var cur_price = "";
						var cur_total = "";
						if(cur){
							cur_qty = cur.qty;
							cur_unit = cur.unit;
							cur_item = cur.item_name;
							grand_total = parseFloat(grand_total) +  parseFloat(cur.total);
							cur_price = number_format(cur.price,2);
							cur_total = number_format(cur.total,2);
							total_qty = parseFloat(total_qty) +  parseFloat(cur_qty);
						}
						$drf += "<tr><td "+$tborin+" width='80'>"+cur_qty+"</td><span style='visibility: hidden'>.</span><td "+$tborin+" width='80'>"+cur_unit+"<span style='visibility: hidden'>.</span></td><td "+$tborin+" width='400'>"+cur_item+"</td><td "+$tborin+" width='120'>"+cur_price+"</td><td "+$tborin+" width='150'>"+cur_total+"</td></tr>";
					}
					$drf += "<tr><td colspan=2><strong>Total of "+total_qty+" sets</strong></td><td align=center><em><font size=2></font></em></td><td colspan=2 align=right><strong>Total: "+number_format(grand_total,2)+"</strong></td></tr></table>";

					var $drfend = "<font STYLE='font-family:\"Bookman Old Style\";font-size:9pt'> Check and Certified by:</font><font style='visibility:hidden'>................................................................................</font><font STYLE='font-family:\"Bookman Old Style\";font-size:9pt'>Received the above merchandise in good order and condition.</font>";
					$drfend += "<table style='border-collapse:collapse;' cellpadding=0><td height='40' width='80' "+$tbor+"><font style='visibility:hidden'>.</font></td><td width='80' "+$bor+"><font style='visibility:hidden'>.</font></td><td  "+$bor+" width='80'><font style='visibility:hidden'>.</font></td><td "+$tbor+" width='80'><font style='visibility:hidden'>.</font></td><td width='120'></td><td style='border-bottom : 1px solid black;' width='410'></td></table>";


					var $drforig = $drf +  $drfend ;
					var $html =  "<div id='printable'>" + $drforig +"</div>";
					 $html +="<br><br><br><br>"+ $html;
					popUpPrint("<br><br>" + $html);
				},
				error:function(){

				}
			})
		}

		function popUpPrint(data){
			var mywindow = window.open('', 'new div', '');
			mywindow.document.write('<html><head><title></title><style></style>');
			mywindow.document.write('</head><body style="padding:0;margin:0;;font-family: Arial, Helvetica, sans-serif;">');
			mywindow.document.write(data);
			mywindow.document.write('</body></html>');
			setTimeout(function() {
				mywindow.print();
				mywindow.close();

			}, 300);
			return true;
		}



		$('body').on('click','#addItem',function(){
			addItem();
		});
		hideNoRecord();
		computeTotal();
		function hideNoRecord(){
			computeTotal();
			if(hasRecord()){
				$('#no_record').hide();
				$('#cart_body').show();
			} else {
				$('#cart_body').hide();
				$('#no_record').show();
			}
		}
		function computeTotal(){
			if($('#cart tbody tr').length > 0){
				var grand_total = 0;

				$('#cart > tbody > tr').each(function(){
					var row = $(this);
					var total = row.children().eq(4).text();
					grand_total = parseFloat(total) + parseFloat(grand_total);
				});
				$('#total_label').html("Total: " + number_format(grand_total,2));
			} else {
				$('#total_label').html("");
			}
		}
		function hasRecord(){
			if($('#cart tbody tr').length > 0){
				return true;
			} else {
				return false
			}
		}


		$('body').on('click','.btnRemove',function(){
			$(this).parents('tr').remove();
			hideNoRecord();
		});

		function itemExists(item_name){
			var e = false;
			$('#cart tbody tr').each(function(){
				var row = $(this);
				var i = row.children().eq(2).text();
				if(i == item_name){
					e= true;
				}
			})
			return e;
		}

		function addItem(){
			var item_name = $('#item_name option:selected').text();
			var qty = $('#qty').val();
			var price = $('#price').val();
			var total = parseFloat(qty) * parseFloat(price);

			if($('#cart tbody tr').length >= 10){
				alert("10 items per dr only.");
				return;

			}
			if(itemExists(item_name)){
				alert("Item already exists");
				return;
			}
			if(item_name && qty){
				$('#cart tbody').append("<tr><td>"+qty+"</td><td>Sets</td><td>"+item_name+"</td><td>"+price+"</td><td>"+total+"</td><td><button class='btn btn-danger btn-sm btn-flat btnRemove'>Remove</button></td></tr>");
				$('#item_name').select2('val',null);
				$('#qty').val('');
				$('#price').val('');
				hideNoRecord();
			} else {
				alert("Please complete the form");
			}

		}
	});
</script>
</body>
</html>