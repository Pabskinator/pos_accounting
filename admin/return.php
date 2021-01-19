<?php
	// $user have all the properties and method of the current user
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('station')) {
		// redirect to denied page
		Redirect::to(1);
	}


?>


	<!-- Page content -->
<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>Return</h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}
		?>
			<hr />
			<div class="row">
				<div class="col-md-6">
					<input type="text" placeholder='Enter Invoice' class='form-control' id='txtInvoice' />
				</div>
				<div class="col-md-6">

				</div>
			</div>
			<br />
			<div class="panel panel-default">
				<div class="panel-body">
						<div class="row">
							<div class="col-md-12">
								<table id="tblItemList" class='table'>
									<thead>
									<tr>
										<td colspan='5'>Search Item..</td>
									</tr>
									</thead>
									<tbody></tbody>
								</table>
							</div>
						</div>
					<br />
					<div class="row">
						<div class="col-md-2">

						</div>
						<div class="col-md-10">
							<table class='table'>
								<tr>
									<td>Total Paid: <span id='totalpaidlabel' class='text-danger'>0.00</span></td>
									<td>Total Return: <span id='totalretlabel' class='text-danger'>0.00</span></td>
									<td>Available Amount: <span id='totalavaillabel' class='text-danger'>0.00</span></td>
									<td>Additional: <span id='totaladdlabel' class='text-danger'>0.00</span></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">

					<h3>Return</h3>
					<select name="addret" id="addret" class='form-control itemlist'>

					</select>

					<table id='tblItemListRet' class='table' style='font-size:1em'>
						<thead>
						<tr>
							<th>PRODUCT</th>
							<th>QTY</th>
							<th>PRICE</th>
							<th>DISCOUNT</th>
							<th>TOTAL</th>
							<th></th>
						</tr>
						</thead>
						<tbody>

						</tbody>
					</table>
				</div>
				<div class="col-md-6">
					<h3>Exchange</h3>
					 <input name="addexc" id="addexc" type="text" class='selectitem'>


					<table id='tblItemListExc' class='table' style='font-size:1em'>
						<thead>
						<tr>
							<th>PRODUCT</th>
							<th>QTY</th>

							<th>PRICE</th>
							<th>DISCOUNT</th>
							<th>TOTAL</th>
							<th></th>
						</tr>
						</thead>
						<tbody>

						</tbody>
					</table>
					<hr>
					<table id='cart-table' class="table table-condensed" style='outline:none;'>
						<tr>
							<td class='text-right' width="75%">SUB TOTAL:</td>
							<td class='text-right' id='subtotalholder'>0</td>
						</tr>
						<tr>
							<td class='text-right' width="75%">VAT:</td>
							<td class='text-right' id='vatholder'>0</td>
						</tr>
						<tr>
							<td class='text-right' width="75%">TOTAL</td>
							<td class='text-right' id='grandtotalholder'>0</td>
						</tr>
						<tr>
							<td class='text-right' width="75%">CASH RECEIVED</td>
							<td class='text-right' id='cashreceiveholder'>0</td>
						</tr>
						<tr>
							<td class='text-right' width="75%">CHANGE</td>
							<td class='text-right' id='changeholder'>0</td>
						</tr>
					</table>
					<hr>
					<div class="row">
						<div class="col-md-6 text-left">
							<button class='btn btn-danger' id='voidOrder'>VOID</button>
						</div>
						<div class="col-md-6 text-right">

							<button class='btn btn-success' id='gotopos'>SUBMIT</button>
						</div>
					</div>
				</div>
			</div>
		<div id="test"></div>
		</div>
	</div>

	<!-- end page content wrapper-->
	<script>

	$(function(){

			$('#gotopos').click(function(){
				var payment_id = $('#tblItemList > tbody').attr('data-payment_id');
				//tblItemListRet tblItemListExc
				if(!$("#noitemexc").length) {
					var origjson = [];
					var retjson = [];
					var excjson = [];

					$('#tblItemList > tbody > tr').each(function(index){
						var origrow = $(this);
						var oitem_id = origrow.attr('data-item_id');
						var oprice_id = origrow.attr('data-price_id');
						var oqty  = origrow.children().eq(1).text();
						var oprice = origrow.children().eq(2).text();
						var odiscount = origrow.children().eq(3).text();
						var ototal = origrow.children().eq(4).text();
						origjson[index] = {
							item_id : oitem_id,
							price_id : oprice_id,
							qty : oqty,
							price : oprice,
							discount : odiscount,
							total : ototal
						}
					});

					$('#tblItemListRet > tbody > tr').each(function(index){
						var rrigrow = $(this);
						var ritem_id = rrigrow.attr('data-item_id');
						var rprice_id = rrigrow.attr('data-price_id');
						var rqty  = rrigrow.children().eq(1).find('input').val();
						var rprice = rrigrow.children().eq(2).text();
						var rdiscount = rrigrow.children().eq(3).text();
						var rtotal = rrigrow.children().eq(4).text();
						retjson[index] = {
							item_id : ritem_id,
							price_id : rprice_id,
							qty : rqty,
							price : rprice,
							discount : rdiscount,
							total : rtotal
						}
					});

					$('#tblItemListExc > tbody > tr').each(function(index){
						var excrow = $(this);
						var ritem_id = excrow.attr('id');
						var rprice_id = excrow.attr('data-price_id');
						var rqty  = excrow.children().eq(1).find('input').val();
						var rprice = excrow.children().eq(2).text();
						var rdiscount = excrow.children().eq(3).find('input').val();
						var rtotal = excrow.children().eq(4).text();
						excjson[index] = {
							item_id : ritem_id,
							price_id : rprice_id,
							qty : rqty,
							price : rprice,
							discount : rdiscount,
							total : rtotal
						}
					});

					var origjson = JSON.stringify(origjson);
					var retjson = JSON.stringify(retjson);
					var excjson = JSON.stringify(excjson);
					$.ajax({
					    url:'../ajax/ajax_query.php',
					    type:'post',
					    data: {origjson:origjson,retjson:retjson,excjson:excjson,functionName:'retQuery'},
					    success: function(data){
							$('#test').html(data);
					    },
					    error:function(){

					    }
					})

				} else {
					alert('Please enter items to exchange.');
				}
			});
		/////// START

			function getItemBaseOnInvoice(inv,callback){
				$.ajax({
					url: "../ajax/ajax_query.php",
					type:"POST",
					data:{invoice:inv,functionName:'getItemBaseOnInvoice',terminal_id:localStorage['terminal_id']},
					success: function(data){
						$('#tblItemList').html(data);
						callback();
					}
				});
			}
			$('body').on('blur', '#txtInvoice', function() {
				var invoice = $(this).val();
				getItemBaseOnInvoice(invoice, function() {
				//	var items = JSON.parse(localStorage['items']);
					$("#addret").empty();
					$("#addret").append("<option value=''></option>");
					$("#tblItemList tbody tr").each(function() {
						var row = $(this);
						var item_id = row.attr('data-item_id');
						var itemcode_desc = row.attr('data-itemcode_desc');

						$("#addret").append("<option value='" + item_id + "'>" + itemcode_desc + "</option>");
					});
					$('#addret').select2({
						placeholder:'Select item to return'
					});
					$("#tblItemListExc tbody").empty();
					$("#tblItemListRet tbody").empty();

					noItemInCart();
					updateCompLabel();

				});
			});
			$('#txtInvoice').keypress(function(e) {
				var key = e.which;
				if(key == 13)  // the enter key code
				{
					$('#txtInvoice').blur();
				}
			});
		noItemInCart();
		function noItemInCart() {
			if(!$("#tblItemListExc tbody").children().length) {
				$("#tblItemListExc tbody").append("<td colspan='5' id='noitemexc' style='padding-top:10px;' ><span class='label label-info'>NO ITEMS IN CART</span></td>");
			}
			if(!$("#tblItemListRet tbody").children().length) {
				$("#tblItemListRet tbody").append("<td colspan='5' id='noitemret' style='padding-top:10px;' ><span class='label label-info'>NO ITEMS IN CART</span></td>");
			}
		}
		$("#addret").change(function() {
			if($("#tblItemList tbody").children().length) {
				var item_id = $(this).val();
				$("#tblItemList tbody tr").each(function() {
					var row = $(this);
					var this_itemid = row.attr('data-item_id');
					if(this_itemid == item_id) {
						var qty = row.children().eq(1).text();
						var price = row.children().eq(2).text();
						var price_id =row.attr('data-price_id');
						var discount = row.children().eq(3).text();
						var total = row.children().eq(4).text();
						var discPerQty = parseFloat(discount) / parseFloat(qty);
						var mydisc = parseFloat(discPerQty) * parseFloat(qty);
						if(!checkIfInRet(item_id)) {
							$("#tblItemListRet tbody").append("<tr data-price_id='"+price_id+"' data-item_id="+item_id+"><td>" +row.children().eq(0).html()+"</small></td><td><input type='text' value='" + qty.replace(",","") + "' class='form-control rqty'></td><td>" + price + "</td><td>" + mydisc + "</td><td>" + total + "</td><td><span class='glyphicon glyphicon-remove removeItem'></span></td></tr>");
							removeNoItemLabelRet();
							updateCompLabel();
							$('#addret').select2('val', null);
						} else {
							alert('Already in return list');
						}
					}
				});
			} else {
				alert('Please enter invoice first');
				$('#txtInvoice').focus();
			}
		});
		function checkIfInRet(i) {
			var e = false;
			$("#tblItemListRet tbody tr").each(function() {
				var row = $(this);
				var item_id = row.attr('data-item_id');

				if(i == item_id) {
					e = true;
					return false;
				}
			});
			return e;
		}
		function removeNoItemLabelRet() {
			$("#noitemret").remove();
		}
		function removeNoItemLabelExc() {
			$("#noitemexc").remove();
		}
		$('body').on('click','.removeItem',function(){
			$(this).parents('tr').remove();
			updatesubtotal();
			updateCompLabel();

		});
		function updatesubtotal(){
			var totals = [];
			$('#tblItemListExc > tbody  > tr ').each(function() {
				totals.push($(this).children().eq(4).text().replace(",",""));
			});

			var	grandtotal=0;
			var vat = 1.12;
			var subtotal = 0;
			for(i=0; i<totals.length;i++){
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
			$("#subtotalholder").append(number_format(subtotal,2));
			$("#vatholder").append(number_format(vat,2));
			$("#grandtotalholder").append(number_format(grandtotal,2));
		}
		function updateCompLabel() {
			if($("#tblItemList tbody").children().length) {
				var gtotal = 0;
				$("#tblItemList tbody tr").each(function() {
					var row = $(this);
					gtotal = parseFloat(gtotal) + parseFloat(row.children().eq(4).text().replace(",",""));
				});
				$("#totalpaidlabel").html(number_format(gtotal,2));
			}else
			{
				$("#totalpaidlabel").html(0.00);
			}
			if($("#tblItemListRet tbody").children().length) {
				var rgtotal = 0;
				$("#tblItemListRet tbody tr").each(function() {
					var rrow = $(this);
					rgtotal = parseFloat(rgtotal) + parseFloat(rrow.children().eq(4).text().replace(",",""));
				});
				$("#totalretlabel").html(number_format(rgtotal,2));

				if (rgtotal >  parseFloat($('#grandtotalholder').text().replace(",",""))){
					var avail = rgtotal - parseFloat($('#grandtotalholder').text().replace(",",""));
					$("#totalavaillabel").html(number_format(avail,2));
					$("#totaladdlabel").html(0.00);
				} else {
					var add =  parseFloat($('#grandtotalholder').text().replace(",","")) - rgtotal;
					$("#totalavaillabel").html(0.00);
					$("#totaladdlabel").html(number_format(add,2));
				}

			}else
			{
				$("#totalretlabel").html(0.00);
				$("#totaladdlabel").html(0.00);
				$("#totalavaillabel").html(0.00);
			}
		}
		$('body').on('keyup', '.rqty', function() {
			var row = $(this).parents('tr');
			var cqty = $(this).val();
			var item_id = row.attr('data-item_id');
			var cprice = row.children().eq(2).text().replace(",","");
			var tqty = 0;
			var tdics = 0;
			var price = 0;
			$("#tblItemList tbody tr").each(function() {
				var trow = $(this);
				if(trow.attr('data-item_id') == item_id) {
					tqty = trow.children().eq(1).text().replace(",","");
					tdics = trow.children().eq(3).text().replace(",","");
					return;
				}
			});
			var discPerQty = parseFloat(tdics) / parseFloat(tqty);

			var total = 0;

			if(parseFloat(cqty) > parseFloat(tqty)) {
				alert('Invalid Qty');
				$(this).val(1);

				total = (parseFloat($(this).val()) * parseFloat(cprice)) - (parseFloat($(this).val())*discPerQty);
				row.children().eq(3).text(number_format(discPerQty,2));
				row.children().eq(4).text(number_format(total,2));
				updateCompLabel();
				updatesubtotal();
			} else {
				total = (parseFloat($(this).val()) * parseFloat(cprice)) - (parseFloat($(this).val())*discPerQty);
				row.children().eq(3).text(parseFloat($(this).val())*discPerQty);
				row.children().eq(4).text(number_format(total,2));
				updateCompLabel();
				updatesubtotal();
			}
		});

		$("#addret").select2({
			placeholder: 'Search Item'
		});
		$("#addexc").change(function() {
			if($("#tblItemList tbody").children().length) {
				if(parseInt($("#totalretlabel").text()) > 0) {
					var opt = $(this);
					var item_id = opt.val();
					var price = $('#addexc option:selected').attr('data-price');
					var price_id = $('#addexc option:selected').attr('data-price_id');
					var qty = $('#addexc option:selected').attr('data-qty');
					var bc = $('#addexc option:selected').attr('data-bc');
					var desc = $('#addexc option:selected').html();
					if(item_id){
						$('#cashreceiveholder').empty();
						$('#changeholder').empty();
						$('#cashreceiveholder').append(0.00);
						$('#changeholder').append(0.00);

						$('#tblItemListExc > tbody').append("<tr data-bc='"+bc+"'data-qty='"+qty+"' data-price='"+price+"' data-price_id='"+price_id+"' id='"+item_id+"'><td>"+desc+"</td><td><input type='text' class='form-control circletextbox cartqty' value='1'></td><td>"+price+"</td><td><input type='text' class='form-control circletextbox cartdiscount' value='0'></td><td>"+price+"</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
						updatesubtotal();
						removeNoItemLabelExc();
						updateCompLabel();
						$('#addexc').select2('val', null);
					} else {
						alert('Not valid barcode or Not enough Stock in Display');
					}
				} else {
					alert('Please enter return item first');
					$('#addexc').select2('val', null);
				}
			} else {
				alert('Please enter invoice first');
				$('#txtInvoice').focus();
			}
		});

		$('body').on('keyup','.cartqty',function(){
			var newqty = $(this).val();
			if(isNaN(newqty) || parseInt(newqty) <  1 || !newqty){

				$(this).val(1);
				alert('Quantity should be a number and greater than 0');
				var parenttr= $(this).parents('tr');
				var price = parenttr.children().eq(2).text().replace(",","");
				var discount = parenttr.children().eq(3).find('input').val();
				if(discount.indexOf("%") > 0){
					discount = parseFloat(discount)/100;
					discount = (parseFloat(price) * parseFloat(discount)) * parseFloat(1);
				}
				var newtotal = (parseFloat(price) * parseFloat($(this).val())- parseFloat(discount));
				parenttr.children().eq(4).empty();
				parenttr.children().eq(4).append(number_format(newtotal,2));
				updatesubtotal();
				updateCompLabel();
			} else {
				parenttr= $(this).parents('tr');

				var tprice = parenttr.attr('data-price');
				var tprice_id = parenttr.attr('data-price_id');
				var tqty = parenttr.attr('data-qty');
				if(parseInt(newqty) > parseInt(tqty)){
					$(this).val(1);
					alert('Not enough stocks');
					 price = parenttr.children().eq(2).text().replace(",","");
					 discount = parenttr.children().eq(3).find('input').val();
					if(discount.indexOf("%") > 0){
						discount = parseFloat(discount)/100;
						discount = (parseFloat(price) * parseFloat(discount)) * parseFloat(1);
					}
					var newtotal = (parseFloat(price) * parseFloat($(this).val())- parseFloat(discount));
					parenttr.children().eq(4).empty();
					parenttr.children().eq(4).append(number_format(newtotal,2));
					updatesubtotal();
					updateCompLabel();
				} else {
					price = parenttr.children().eq(2).text().replace(",","");
					discount = parenttr.children().eq(3).find('input').val();
					if(discount.indexOf("%") > 0){
						discount = parseFloat(discount)/100;
						discount = (parseFloat(price) * parseFloat(discount)) * parseFloat(newqty);
					}
					var newtotal = (parseFloat(price) * parseFloat($(this).val())- parseFloat(discount));
					parenttr.children().eq(4).empty();
					parenttr.children().eq(4).append(number_format(newtotal,2));
					updatesubtotal();
					updateCompLabel();
				}

			}
		});
	});

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>