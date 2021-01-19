<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('item_service_r')){
		// redirect to denied page
		Redirect::to(1);
	}
	$barcodeClass = new Barcode();
	$barcode_format = $barcodeClass->getFormat($user->data()->company_id,"SERVICE");


	$styles =  json_decode($barcode_format->styling,true);

?>
	<!-- 	<link rel="stylesheet" href="../css/gridster.css" /> -->

	<style>
		.conpage{
			width: 816px;
			height: 1056px;
			float:left;
			position:relative;
			border: 1px solid #000;
		}
		.date {
			position:absolute;
			top:<?php echo $styles['date']['top'] . "px" ?>;
			left:<?php echo $styles['date']['left'] . "px" ?>;
			width: <?php echo $styles['date']['width'] . "px" ?>;
			height: <?php echo $styles['date']['height'] . "px" ?>;
			font-size: <?php echo $styles['date']['fontSize'] . "px" ?>;
			display: <?php echo  ($styles['date']['visible']) ? 'block' : 'none' ?>;
			font-weight: <?php echo  ($styles['date']['bold']) ? 'bold' : 'normal' ?>;
		}
		.membername {
			position:absolute;
			top:<?php echo $styles['membername']['top'] . "px" ?>;
			left:<?php echo $styles['membername']['left'] . "px" ?>;
			width: <?php echo $styles['membername']['width'] . "px" ?>;
			height: <?php echo $styles['membername']['height'] . "px" ?>;
			font-size: <?php echo $styles['membername']['fontSize'] . "px" ?>;
			display: <?php echo  ($styles['membername']['visible']) ? 'block' : 'none' ?>;
			font-weight: <?php echo  ($styles['membername']['bold']) ? 'bold' : 'normal' ?>;
		}
		.memberaddress{
			position:absolute;
			top:<?php echo $styles['memberaddress']['top'] . "px" ?>;
			left:<?php echo $styles['memberaddress']['left'] . "px" ?>;
			width: <?php echo $styles['memberaddress']['width'] . "px" ?>;
			height: <?php echo $styles['memberaddress']['height'] . "px" ?>;
			font-size: <?php echo $styles['memberaddress']['fontSize'] . "px" ?>;
			display: <?php echo  ($styles['memberaddress']['visible']) ? 'block' : 'none' ?>;
			font-weight: <?php echo  ($styles['memberaddress']['bold']) ? 'bold' : 'normal' ?>;
		}
		.stationname{
			position:absolute;
			top:<?php echo $styles['stationname']['top'] . "px" ?>;
			left:<?php echo $styles['stationname']['left'] . "px" ?>;
			width: <?php echo $styles['stationname']['width'] . "px" ?>;
			height: <?php echo $styles['stationname']['height'] . "px" ?>;
			font-size: <?php echo $styles['stationname']['fontSize'] . "px" ?>;
			display: <?php echo  ($styles['stationname']['visible']) ? 'block' : 'none' ?>;
			font-weight: <?php echo  ($styles['stationname']['bold']) ? 'bold' : 'normal' ?>;
		}
		.servicetype{
			position:absolute;
			top:<?php echo $styles['servicetype']['top'] . "px" ?>;
			left:<?php echo $styles['servicetype']['left'] . "px" ?>;
			width: <?php echo $styles['servicetype']['width'] . "px" ?>;
			height: <?php echo $styles['servicetype']['height'] . "px" ?>;
			font-size: <?php echo $styles['servicetype']['fontSize'] . "px" ?>;
			display: <?php echo  ($styles['servicetype']['visible']) ? 'block' : 'none' ?>;
			font-weight: <?php echo  ($styles['servicetype']['bold']) ? 'bold' : 'normal' ?>;
		}
		.stationaddress{
			position:absolute;
			width: <?php echo $styles['stationaddress']['width'] . "px" ?>;
			height: <?php echo $styles['stationaddress']['height'] . "px" ?>;
			top:<?php echo $styles['stationaddress']['top'] . "px" ?>;
			left:<?php echo $styles['stationaddress']['left'] . "px" ?>;
			font-size: <?php echo $styles['stationaddress']['fontSize'] . "px" ?>;
			display: <?php echo  ($styles['stationaddress']['visible']) ? 'block' : 'none' ?>;
			font-weight: <?php echo  ($styles['stationaddress']['bold']) ? 'bold' : 'normal' ?>;
		}

		.itemtable{
			position:absolute;
			top:<?php echo $styles['itemtable']['top'] . "px" ?>;
			left:<?php echo $styles['itemtable']['left'] . "px" ?>;
			width: <?php echo $styles['itemtable']['width'] . "px" ?>;
			height: <?php echo $styles['itemtable']['height'] . "px" ?>;
			font-size: <?php echo $styles['itemtable']['fontSize'] . "px" ?>;
			display: <?php echo  ($styles['itemtable']['visible']) ? 'block' : 'none' ?>;
			font-weight: <?php echo  ($styles['itemtable']['bold']) ? 'bold' : 'normal' ?>;
		}

		.payments {
			position:absolute;
			top:<?php echo $styles['payments']['top'] . "px" ?>;
			left:<?php echo $styles['payments']['left'] . "px" ?>;
			width: <?php echo $styles['payments']['width'] . "px" ?>;
			height: <?php echo $styles['payments']['height'] . "px" ?>;
			font-size: <?php echo $styles['payments']['fontSize'] . "px" ?>;
			display: <?php echo  ($styles['payments']['visible']) ? 'block' : 'none' ?>;
			font-weight: <?php echo  ($styles['payments']['bold']) ? 'bold' : 'normal' ?>;
		}
		.payments2 {
			position:absolute;
			top:<?php echo $styles['payments2']['top'] . "px" ?>;
			left:<?php echo $styles['payments2']['left'] . "px" ?>;
			width: <?php echo $styles['payments2']['width'] . "px" ?>;
			height: <?php echo $styles['payments2']['height'] . "px" ?>;
			font-size: <?php echo $styles['payments2']['fontSize'] . "px" ?>;
			display: <?php echo  ($styles['payments2']['visible']) ? 'block' : 'none' ?>;
			font-weight: <?php echo  ($styles['payments2']['bold']) ? 'bold' : 'normal' ?>;
		}
		.payments3 {
			position:absolute;
			top:<?php echo $styles['payments3']['top'] . "px" ?>;
			left:<?php echo $styles['payments3']['left'] . "px" ?>;
			width: <?php echo $styles['payments3']['width'] . "px" ?>;
			height: <?php echo $styles['payments3']['height'] . "px" ?>;
			font-size: <?php echo $styles['payments3']['fontSize'] . "px" ?>;
			display: <?php echo  ($styles['payments3']['visible']) ? 'block' : 'none' ?>;
			font-weight: <?php echo  ($styles['payments3']['bold']) ? 'bold' : 'normal' ?>;
		}
		.cashier {
			position:absolute;
			top:<?php echo $styles['cashier']['top'] . "px" ?>;
			left:<?php echo $styles['cashier']['left'] . "px" ?>;
			width: <?php echo $styles['cashier']['width'] . "px" ?>;
			height: <?php echo $styles['cashier']['height'] . "px" ?>;
			font-size: <?php echo $styles['cashier']['fontSize'] . "px" ?>;
			display: <?php echo  ($styles['cashier']['visible']) ? 'block' : 'none' ?>;
			font-weight: <?php echo  ($styles['cashier']['bold']) ? 'bold' : 'normal' ?>;
		}
		.remarks {
			position:absolute;
			top:<?php echo $styles['remarks']['top'] . "px" ?>;
			left:<?php echo $styles['remarks']['left'] . "px" ?>;
			width: <?php echo $styles['remarks']['width'] . "px" ?>;
			height: <?php echo $styles['remarks']['height'] . "px" ?>;
			font-size: <?php echo $styles['remarks']['fontSize'] . "px" ?>;
			display: <?php echo  ($styles['remarks']['visible']) ? 'block' : 'none' ?>;
			font-weight: <?php echo  ($styles['remarks']['bold']) ? 'bold' : 'normal' ?>;
		}
		.reserved {
			position:absolute;
			top:<?php echo $styles['reserved']['top'] . "px" ?>;
			left:<?php echo $styles['reserved']['left'] . "px" ?>;
			width: <?php echo $styles['reserved']['width'] . "px" ?>;
			height: <?php echo $styles['reserved']['height'] . "px" ?>;
			font-size: <?php echo $styles['reserved']['fontSize'] . "px" ?>;
			display: <?php echo  ($styles['reserved']['visible']) ? 'block' : 'none' ?>;
			font-weight: <?php echo  ($styles['reserved']['bold']) ? 'bold' : 'normal' ?>;
		}
		.drnum {
			position:absolute;
			top:<?php echo $styles['drnum']['top'] . "px" ?>;
			left:<?php echo $styles['drnum']['left'] . "px" ?>;
			width: <?php echo $styles['drnum']['width'] . "px" ?>;
			height: <?php echo $styles['drnum']['height'] . "px" ?>;
			font-size: <?php echo $styles['drnum']['fontSize'] . "px" ?>;
			display: <?php echo  ($styles['drnum']['visible']) ? 'block' : 'none' ?>;
			font-weight: <?php echo  ($styles['drnum']['bold']) ? 'bold' : 'normal' ?>;
		}
		.terms {
			position:absolute;
			top:<?php echo $styles['terms']['top'] . "px" ?>;
			left:<?php echo $styles['terms']['left'] . "px" ?>;
			width: <?php echo $styles['terms']['width'] . "px" ?>;
			height: <?php echo $styles['terms']['height'] . "px" ?>;
			font-size: <?php echo $styles['terms']['fontSize'] . "px" ?>;
			display: <?php echo  ($styles['terms']['visible']) ? 'block' : 'none' ?>;
			font-weight: <?php echo  ($styles['terms']['bold']) ? 'bold' : 'normal' ?>;
		}
		.ponum {
			position:absolute;
			top:<?php echo $styles['ponum']['top'] . "px" ?>;
			left:<?php echo $styles['ponum']['left'] . "px" ?>;
			width: <?php echo $styles['ponum']['width'] . "px" ?>;
			height: <?php echo $styles['ponum']['height'] . "px" ?>;
			font-size: <?php echo $styles['ponum']['fontSize'] . "px" ?>;
			display: <?php echo  ($styles['ponum']['visible']) ? 'block' : 'none' ?>;
			font-weight: <?php echo  ($styles['ponum']['bold']) ? 'bold' : 'normal' ?>;
		}
		.tin {
			position:absolute;
			top:<?php echo $styles['tin']['top'] . "px" ?>;
			left:<?php echo $styles['tin']['left'] . "px" ?>;
			width: <?php echo $styles['tin']['width'] . "px" ?>;
			height: <?php echo $styles['tin']['height'] . "px" ?>;
			font-size: <?php echo $styles['tin']['fontSize'] . "px" ?>;
			display: <?php echo  ($styles['tin']['visible']) ? 'block' : 'none' ?>;
			font-weight: <?php echo  ($styles['tin']['bold']) ? 'bold' : 'normal' ?>;
		}
		.draggable {
			background-color:#999;
			cursor:move;
		}
		.tdbarcode {
			position:relative;
			width: <?php echo $styles['tdbarcode']['width'] . "px" ?>;
			padding-left: <?php echo $styles['tdbarcode']['left'] . "px" ?>;
			display: <?php echo  ($styles['tdbarcode']['visible']) ? 'inline-block' : 'none' ?>;
			font-weight: <?php echo  ($styles['tdbarcode']['bold']) ? 'bold' : 'normal' ?>;
		}
		.tdqty {
			position:relative;
			width: <?php echo $styles['tdqty']['width'] . "px" ?>;
			padding-left: <?php echo $styles['tdqty']['left'] . "px" ?>;
			display: <?php echo  ($styles['tdqty']['visible']) ? 'inline-block' : 'none' ?>;
			font-weight: <?php echo  ($styles['tdqty']['bold']) ? 'bold' : 'normal' ?>;
		}
		.tddescription {
			position:relative;
			width: <?php echo $styles['tddescription']['width'] . "px" ?>;
			padding-left: <?php echo $styles['tddescription']['left'] . "px" ?>;
			display: <?php echo  ($styles['tddescription']['visible']) ? 'inline-block' : 'none' ?>;
			font-weight: <?php echo  ($styles['tddescription']['bold']) ? 'bold' : 'normal' ?>;

		}
		.tdprice {
			position:relative;
			width: <?php echo $styles['tdprice']['width'] . "px" ?>;
			padding-left: <?php echo $styles['tdprice']['left'] . "px" ?>;
			display: <?php echo  ($styles['tdprice']['visible']) ? 'inline-block' : 'none' ?>;
			font-weight: <?php echo  ($styles['tdprice']['bold']) ? 'bold' : 'normal' ?>;
		}
		.tdtotal {
			position:relative;
			width: <?php echo $styles['tdtotal']['width'] . "px" ?>;
			padding-left: <?php echo $styles['tdtotal']['left'] . "px" ?>;
			display: <?php echo  ($styles['tdtotal']['visible']) ? 'inline-block' : 'none' ?>;
			font-weight: <?php echo  ($styles['tdtotal']['bold']) ? 'bold' : 'normal' ?>;

		}
	</style>
	<!-- Page content -->
	<div id="page-content-wrapper">
		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<?php include 'includes/service_nav.php'; ?>
			<br>
			<div id="content" >
				<div id='conpage' data-targetid='conpageValue' class="conpage">
					<div data-targetid='date' class='date canbedrag'>Date</div>
					<div data-targetid='membername' class='membername canbedrag'>Customer Name</div> <!-- Member -->
					<div data-targetid='memberaddress' class='memberaddress canbedrag'>Customer Address</div><!-- Member address-->
					<div data-targetid='stationname' class='stationname canbedrag'>Customer Contanct #</div><!-- station name -->
					<div data-targetid='servicetype' class='servicetype canbedrag'>Service Type</div><!-- station name -->
					<div data-targetid='itemtable' class='itemtable canbedrag'>
						Drag table here
						<table class='tbl'>
							<tr class='indtr'>
								<td class='tdbarcode' style=''>
									BOT-001
								</td>
								<td class='tdqty' >
									Qty
								</td>
								<td class='tddescription' >
									Item Name and Description
								</td>
								<td class='tdprice' >
									Remarks
								</td>
								<td class='tdtotal'>
									100.00
								</td>
							</tr>
							<tr class='indtr'>
								<td class='tdbarcode'>
									BOT-001
								</td>
								<td class='tdqty' >
									Qty
								</td>
								<td class='tddescription' >
									Item Name and Description
								</td>
								<td class='tdprice' >
									Remarks
								</td>
								<td class='tdtotal'>
									100.00
								</td>
							</tr>
						</table>
					</div>

				</div>
			</div>
			<div id="editor" style='padding:10px;'>
				<div style='clear:both;'></div>
				<hr />
				<p class='text-muted'>*save first before printing.</p>
				<input type="button" class='btn btn-primary' value='SAVE' id='save'/> <input type="button" class='btn btn-primary' value='PRINT' id='print'/>
				<hr />


				<table class="table">
					<thead>
					<tr><th>Parts</th><th>Visibility</th><th>Bold</th><th>Top</th><th>Left</th><th>Height</th><th>Width</th><th>Font Size</th></tr>
					</thead>
					<tbody>

					<tr >
						<td>Date</td>
						<td><input type="checkbox" id='dateVisible' <?php echo ($styles['date']['visible']) ? 'checked' : ''; ?> /></td>
						<td><input type="checkbox" id='dateBold' <?php echo ($styles['date']['bold']) ? 'checked' : ''; ?> /></td>
						<td><input  class='form-control' type='text' id='dateTop' value='<?php echo $styles['date']['top']; ?>' /></td>
						<td><input class='form-control' type='text' id='dateLeft' value='<?php echo $styles['date']['left']; ?>'/></td>
						<td><input  class='form-control' disabled type='text' id='dateWidth' value='<?php echo $styles['date']['width']; ?>'/></td>
						<td><input class='form-control' disabled type='text' id='dateHeight' value="<?php echo $styles['date']['height'];  ?>"/></td>
						<td><input class='form-control'  type='text' id='dateFontSize' value="<?php echo $styles['date']['fontSize'];  ?>"/></td>

					</tr>
					<tr >
						<td>Customer Name</td>
						<td><input type="checkbox" id='membernameVisible' <?php echo ($styles['membername']['visible']) ? 'checked' : ''; ?> /></td>
						<td><input type="checkbox" id='membernameBold' <?php echo ($styles['membername']['bold']) ? 'checked' : ''; ?> /></td>
						<td><input  class='form-control' type='text' id='membernameTop' value='<?php echo $styles['membername']['top']; ?>' /></td>
						<td><input class='form-control' type='text' id='membernameLeft' value='<?php echo $styles['membername']['left']; ?>'/></td>
						<td><input  class='form-control' disabled type='text' id='membernameWidth' value='<?php echo $styles['membername']['width']; ?>'/></td>
						<td><input class='form-control' disabled type='text' id='membernameHeight' value="<?php echo $styles['membername']['height'];  ?>"/></td>
						<td><input class='form-control'  type='text' id='membernameFontSize' value="<?php echo $styles['membername']['fontSize'];  ?>"/></td>

					</tr>
					<tr >
						<td>Customer address</td>
						<td><input type="checkbox" id='memberaddressVisible' <?php echo ($styles['memberaddress']['visible']) ? 'checked' : ''; ?> /></td>
						<td><input type="checkbox" id='memberaddressBold' <?php echo ($styles['memberaddress']['bold']) ? 'checked' : ''; ?> /></td>
						<td><input  class='form-control' type='text' id='memberaddressTop' value='<?php echo $styles['memberaddress']['top']; ?>' /></td>
						<td><input class='form-control' type='text' id='memberaddressLeft' value='<?php echo $styles['memberaddress']['left']; ?>'/></td>
						<td><input  class='form-control' disabled type='text' id='memberaddressHeigh' value='<?php echo $styles['memberaddress']['height']; ?>'/></td>
						<td><input class='form-control' type='text' id='memberaddressWidth' value="<?php echo $styles['memberaddress']['width'];  ?>"/></td>
						<td><input class='form-control'  type='text' id='memberaddressFontSize' value="<?php echo $styles['memberaddress']['fontSize'];  ?>"/></td>

					</tr>
					<tr >
						<td>Customer Contact</td>
						<td><input type="checkbox" id='stationnameVisible' <?php echo ($styles['stationname']['visible']) ? 'checked' : ''; ?> /></td>
						<td><input type="checkbox" id='stationnameBold' <?php echo ($styles['stationname']['bold']) ? 'checked' : ''; ?> /></td>
						<td><input  class='form-control' type='text' id='stationnameTop' value='<?php echo $styles['stationname']['top']; ?>' /></td>
						<td><input class='form-control' type='text' id='stationnameLeft' value='<?php echo $styles['stationname']['left']; ?>'/></td>
						<td><input  class='form-control' disabled  type='text' id='stationnameHeight' value='<?php echo $styles['stationname']['height']; ?>'/></td>
						<td><input class='form-control' disabled type='text' id='stationnameWidth' value="<?php echo $styles['stationname']['width'];  ?>"/></td>
						<td><input class='form-control'  type='text' id='stationnameFontSize' value="<?php echo $styles['stationname']['fontSize'];  ?>"/></td>

					</tr>
					<tr >
						<td>Service Type</td>
						<td><input type="checkbox" id='servicetypeVisible' <?php echo ($styles['servicetype']['visible']) ? 'checked' : ''; ?> /></td>
						<td><input type="checkbox" id='servicetypeBold' <?php echo ($styles['servicetype']['bold']) ? 'checked' : ''; ?> /></td>
						<td><input  class='form-control' type='text' id='servicetypeTop' value='<?php echo $styles['servicetype']['top']; ?>' /></td>
						<td><input class='form-control' type='text' id='servicetypeLeft' value='<?php echo $styles['servicetype']['left']; ?>'/></td>
						<td><input  class='form-control' disabled  type='text' id='servicetypeHeight' value='<?php echo $styles['servicetype']['height']; ?>'/></td>
						<td><input class='form-control' disabled type='text' id='servicetypeWidth' value="<?php echo $styles['servicetype']['width'];  ?>"/></td>
						<td><input class='form-control'  type='text' id='servicetypeFontSize' value="<?php echo $styles['servicetype']['fontSize'];  ?>"/></td>

					</tr>

					<tr >
						<td>Item Table</td>
						<td><input type="checkbox" id='itemtableVisible' <?php echo ($styles['itemtable']['visible']) ? 'checked' : ''; ?> /></td>
						<td><input type="checkbox" id='itemtableBold' <?php echo ($styles['itemtable']['bold']) ? 'checked' : ''; ?> /></td>
						<td><input  class='form-control' type='text' id='itemtableTop' value='<?php echo $styles['itemtable']['top']; ?>' /></td>
						<td><input class='form-control' type='text' id='itemtableLeft' value='<?php echo $styles['itemtable']['left']; ?>'/></td>
						<td><input  class='form-control' disabled type='text' id='itemtableWidth' value='<?php echo $styles['itemtable']['width']; ?>'/></td>
						<td><input class='form-control' disabled type='text' id='itemtableHeight' value="<?php echo $styles['itemtable']['height'];  ?>"/></td>
						<td><input class='form-control'  type='text' id='itemtableFontSize' value="<?php echo $styles['itemtable']['fontSize'];  ?>"/></td>

					</tr>



					<tr >
						<td>Qty</td>
						<td><input type="checkbox" id='tdqtyVisible' <?php echo ($styles['tdqty']['visible']) ? 'checked' : ''; ?> /></td>
						<td><input type="checkbox" id='tdqtyBold' <?php echo ($styles['tdqty']['Bold']) ? 'checked' : ''; ?> /></td>
						<td><input type="text" class='form-control' disabled/></td>
						<td><input  class='form-control' type='text' id='tdqtyLeft' value='<?php echo $styles['tdqty']['left']; ?>'/></td>
						<td><input type="text" class='form-control' disabled/></td>
						<td><input  class='form-control' type='text' id='tdqty' value='<?php echo $styles['tdqty']['width']; ?>'/></td>

					</tr>
					<tr >
						<td>Description</td>
						<td><input type="checkbox" id='tddescriptionVisible' <?php echo ($styles['tddescription']['visible']) ? 'checked' : ''; ?> /></td>
						<td><input type="checkbox" id='tddescriptionBold' <?php echo ($styles['tddescription']['Bold']) ? 'checked' : ''; ?> /></td>
						<td><input type="text" class='form-control' disabled/></td>
						<td><input  class='form-control' type='text' id='tddescriptionLeft' value='<?php echo $styles['tddescription']['left']; ?>'/></td>
						<td><input type="text" class='form-control' disabled/></td>
						<td><input  class='form-control' type='text' id='tddescription' value='<?php echo $styles['tddescription']['width']; ?>'/></td>

					</tr>
					<tr >
						<td>Remarks</td>
						<td><input type="checkbox" id='tdpriceVisible' <?php echo ($styles['tdprice']['visible']) ? 'checked' : ''; ?> /></td>
						<td><input type="checkbox" id='tdpriceBold' <?php echo ($styles['tdprice']['Bold']) ? 'checked' : ''; ?> /></td>
						<td><input type="text" class='form-control' disabled/></td>
						<td><input  class='form-control' type='text' id='tdpriceLeft' value='<?php echo $styles['tdprice']['left']; ?>'/></td>
						<td><input type="text" class='form-control' disabled/></td>
						<td><input  class='form-control' type='text' id='tdprice' value='<?php echo $styles['tdprice']['width']; ?>'/></td>

					</tr>


					</tbody>
				</table>

				<!-- TEST-->


			</div>

		</div>
	</div> <!-- end page content wrapper-->

	<script type="text/javascript" src="../js/jquery.js"></script>
	<script>
		$(function(){
			$('.loading').hide();



			$('body').on('mousedown', '.canbedrag', function() {
				$(this).addClass('draggable').parents().on('mousemove', function(e) {
					$('.draggable').offset({
						top: e.pageY - $('.draggable').outerHeight() / 2,
						left: e.pageX - $('.draggable').outerWidth() / 2
					}).on('mouseup', function() {
						$(this).removeClass('draggable');
						var myclass = $(this).attr('class').split(' ')[0];
						var targetid = $(this).attr('data-targetid');
						var dposition = $(this).position();

						$("."+myclass).css({top: dposition.top, left: dposition.left});
						if(targetid == 'date'){
							updateDate(dposition.top,dposition.left);
						} else if(targetid == 'membername'){
							updateMembername(dposition.top,dposition.left);
						}else if(targetid == 'memberaddress'){
							updateMemberaddress(dposition.top,dposition.left);
						}else if(targetid == 'stationname'){
							updateStationname(dposition.top,dposition.left);
						}else if(targetid == 'servicetype'){
							updateServicetype(dposition.top,dposition.left);
						}else if(targetid == 'stationaddress'){
							updateStationaddress(dposition.top,dposition.left);
						}else if(targetid == 'itemtable'){
							updateItemtable(dposition.top,dposition.left);
						}else if(targetid == 'payments'){
							updatePayments(dposition.top,dposition.left);
						}else if(targetid == 'payments2'){
							updatePayments2(dposition.top,dposition.left);
						}else if(targetid == 'payments3'){
							updatePayments3(dposition.top,dposition.left);
						}else if(targetid == 'cashier'){
							updateCashier(dposition.top,dposition.left);
						}else if(targetid == 'remarks'){
							updateRemarks(dposition.top,dposition.left);
						}
						else if(targetid == 'reserved'){
							updateReserved(dposition.top,dposition.left);
						}
						else if(targetid == 'drnum'){
							updateDrnum(dposition.top,dposition.left);
						}
						else if(targetid == 'terms'){
							updateTerms(dposition.top,dposition.left);
						}
						else if(targetid == 'ponum'){
							updatePonum(dposition.top,dposition.left);
						}
						else if(targetid == 'tin'){
							updateTin(dposition.top,dposition.left);
						}
						else if(targetid == 'drnum'){
							updateDrnum(dposition.top,dposition.left);
						}
						else if(targetid == 'barcode'){
							updateBarcode(dposition.top,dposition.left);
						}
						else if(targetid == 'qty'){
							updateQty(dposition.top,dposition.left);
						}
						else if(targetid == 'description'){
							updateDescription(dposition.top,dposition.left);
						}
						else if(targetid == 'price'){
							updatePrice(dposition.top,dposition.left);
						}
						else if(targetid == 'total'){
							updateTotal(dposition.top,dposition.left);
						}
					});

				});
				e.preventDefault();
			}).on('mouseup', function() {
				$('.draggable').removeClass('draggable');
			});


			function updateDate(t,l){
				$('#dateTop').val(t);
				$('#dateLeft').val(l);
			}
			function updateBarcode(t,l){
				$('#barcodeTop').val(t);
				$('#barcodeLeft').val(l);
			}
			function updateQty(t,l){
				$('#qtyTop').val(t);
				$('#qtyLeft').val(l);
			}
			function updateDescription(t,l){
				$('#descriptionTop').val(t);
				$('#descriptionLeft').val(l);
			}
			function updateTotal(t,l){
				$('#totalTop').val(t);
				$('#totalLeft').val(l);
			}
			function updatePrice(t,l){
				$('#priceTop').val(t);
				$('#priceLeft').val(l);
			}

			function updateMembername(t,l){
				$('#membernameTop').val(t);
				$('#membernameLeft').val(l);
			}
			function updateMemberaddress(t,l){
				$('#memberaddressTop').val(t);
				$('#memberaddressLeft').val(l);
			}
			function updateStationname(t,l){
				$('#stationnameTop').val(t);
				$('#stationnameLeft').val(l);
			}
			function updateServicetype(t,l){
				$('#servicetypeTop').val(t);
				$('#servicetypeLeft').val(l);
			}
			function updateStationaddress(t,l){
				$('#stationaddressTop').val(t);
				$('#stationaddressLeft').val(l);
			}
			function updateItemtable(t,l){
				$('#itemtableTop').val(t);
				$('#itemtableLeft').val(l);
			}
			function updatePayments(t,l){
				$('#paymentsTop').val(t);
				$('#paymentsLeft').val(l);
			}
			function updatePayments2(t,l){
				$('#payments2Top').val(t);
				$('#payments2Left').val(l);
			}
			function updatePayments3(t,l){
				$('#payments3Top').val(t);
				$('#payments3Left').val(l);
			}
			function updateCashier(t,l){
				$('#cashierTop').val(t);
				$('#cashierLeft').val(l);
			}
			function updateRemarks(t,l){
				$('#remarksTop').val(t);
				$('#remarksLeft').val(l);
			}
			function updateReserved(t,l){
				$('#reservedTop').val(t);
				$('#reservedLeft').val(l);
			}
			function updateDrnum(t,l){
				$('#drnumTop').val(t);
				$('#drnumLeft').val(l);
			}
			function updateTerms(t,l){
				$('#termsTop').val(t);
				$('#termsLeft').val(l);
			}
			function updatePonum(t,l){
				$('#ponumTop').val(t);
				$('#ponumLeft').val(l);
			}
			function updateTin(t,l){
				$('#tinTop').val(t);
				$('#tinLeft').val(l);
			}
			$('#dateTop,#dateLeft,#dateHeight,#dateWidth,#dateFontSize').keyup(function(){
				var t = $('#dateTop').val();
				var l = $('#dateLeft').val();
				var h = $('#dateHeight').val();
				var w = $('#dateWidth').val();
				var fs = $('#dateFontSize').val();
				changeHW(h,w,'date');
				changePosition(t,l,'date');
				changeFontSize(fs,'date');
			});

			$('#barcodeTop,#barcodeLeft').keyup(function(){
				var t = $('#barcodeTop').val();
				var l = $('#barcodeLeft').val();
				changePosition(t,l,'barcode');
			});
			$('#qtyTop,#qtyLeft').keyup(function(){
				var t = $('#qtyTop').val();
				var l = $('#qtyLeft').val();
				changePosition(t,l,'qty');
			});
			$('#descriptionTop,#descriptionLeft').keyup(function(){
				var t = $('#descriptionTop').val();
				var l = $('#descriptionLeft').val();
				changePosition(t,l,'description');
			});
			$('#priceTop,#priceLeft').keyup(function(){
				var t = $('#priceTop').val();
				var l = $('#priceLeft').val();
				changePosition(t,l,'price');
			});
			$('#totalTop,#totalLeft').keyup(function(){
				var t = $('#totalTop').val();
				var l = $('#totalLeft').val();
				changePosition(t,l,'total');
			});
			$('#tdbarcode').keyup(function(){
				var w = $('#tdbarcode').val();
				changeW(w,'tdbarcode');
			});
			$('#tdqty').keyup(function(){
				var w = $('#tdqty').val();
				changeW(w,'tdqty');
			});
			$('#tddescription').keyup(function(){
				var w = $('#tddescription').val();
				changeW(w,'tddescription');
			});

			$('#tdprice').keyup(function(){
				var w = $('#tdprice').val();
				changeW(w,'tdprice');
			});

			$('#tdtotal').keyup(function(){
				var w = $('#tdtotal').val();
				changeW(w,'tdtotal');
			});

			$('#membernameTop,#membernameLeft,#membernameHeight,#membernameWidth, #membernameFontSize').keyup(function(){
				var t = $('#membernameTop').val();
				var l = $('#membernameLeft').val();
				var h = $('#membernameHeight').val();
				var w = $('#membernameWidth').val();
				var fs = $('#membernameFontSize').val();
				changeHW(h,w,'membername');
				changePosition(t,l,'membername');
				changeFontSize(fs,'membername');
			});

			$('#memberaddressTop,#memberaddressLeft,#memberaddressHeight,#memberaddressWidth , #memberaddressFontSize').keyup(function(){
				var t = $('#memberaddressTop').val();
				var l = $('#memberaddressLeft').val();
				var h = $('#memberaddressHeight').val();
				var w = $('#memberaddressWidth').val();
				var fs = $('#memberaddressFontSize').val();
				changeHW(h,w,'memberaddress');
				changePosition(t,l,'memberaddress');
				changeFontSize(fs,'memberaddress');
			});
			$('#stationnameTop,#stationnameLeft,#stationnameHeight,#stationnameWidth,#stationnameFontSize').keyup(function(){
				var t = $('#stationnameTop').val();
				var l = $('#stationnameLeft').val();
				var h = $('#stationnameHeight').val();
				var w = $('#stationnameWidth').val();
				var fs = $('#stationnameFontSize').val();
				changeHW(h,w,'stationname');
				changePosition(t,l,'stationname');
				changeFontSize(fs,'stationname');
			});

			$('#servicetypeTop,#servicetypeLeft,#servicetypeHeight,#servicetypeWidth,#servicetypeFontSize').keyup(function(){
				var t = $('#servicetypeTop').val();
				var l = $('#servicetypeLeft').val();
				var h = $('#servicetypeHeight').val();
				var w = $('#servicetypeWidth').val();
				var fs = $('#servicetypeFontSize').val();
				changeHW(h,w,'servicetype');
				changePosition(t,l,'servicetype');
				changeFontSize(fs,'servicetype');
			});

			$('#stationaddressTop,#stationaddressLeft,#stationaddressHeight,#stationaddressWidth,#stationaddressFontSize').keyup(function(){
				var t = $('#stationaddressTop').val();
				var l = $('#stationaddressLeft').val();
				var h = $('#stationaddressHeight').val();
				var w = $('#stationaddressWidth').val();
				var fs = $('#stationaddressFontSize').val();
				changeHW(h,w,'stationaddress');
				changePosition(t,l,'stationaddress');
				changeFontSize(fs,'stationaddress');
			});
			$('#itemtableTop,#itemtableLeft,#itemtableHeight,#itemtableWidth,#itemtableFontSize').keyup(function(){
				var t = $('#itemtableTop').val();
				var l = $('#itemtableLeft').val();
				var h = $('#itemtableHeight').val();
				var w = $('#itemtableWidth').val();
				var fs = $('#itemtableFontSize').val();
				changeHW(h,w,'itemtable');
				changePosition(t,l,'itemtable');
				changeFontSize(fs,'itemtable');
			});
			$('#paymentsTop,#paymentsLeft,#paymentsHeight,#paymentsWidth,#paymentsFontSize').keyup(function(){
				var t = $('#paymentsTop').val();
				var l = $('#paymentsLeft').val();
				var h = $('#paymentsHeight').val();
				var w = $('#paymentsWidth').val();
				var fs = $('#paymentsFontSize').val();
				changeHW(h,w,'payments');
				changePosition(t,l,'payments');
				changeFontSize(fs,'payments');

			});
			$('#payments2Top,#payments2Left,#payments2Height,#payments2Width,#payments2FontSize').keyup(function(){
				var t = $('#payments2Top').val();
				var l = $('#payments2Left').val();
				var h = $('#payments2Height').val();
				var w = $('#payments2Width').val();
				var fs = $('#payments2FontSize').val();
				changeHW(h,w,'payments2');
				changePosition(t,l,'payments2');
				changeFontSize(fs,'payments2');
			});
			$('#payments3Top,#payments3Left,#payments3Height,#payments3Width,#payments3FontSize').keyup(function(){
				var t = $('#payments3Top').val();
				var l = $('#payments3Left').val();
				var h = $('#payments3Height').val();
				var w = $('#payments3Width').val();
				var fs = $('#payments3FontSize').val();
				changeHW(h,w,'payments3');
				changePosition(t,l,'payments3');
				changeFontSize(fs,'payments3');
			});
			$('#cashierTop,#cashierLeft,#cashierHeight,#cashierWidth,#cashierFontSize').keyup(function(){
				var t = $('#cashierTop').val();
				var l = $('#cashierLeft').val();
				var h = $('#cashierHeight').val();
				var w = $('#cashierWidth').val();
				var fs = $('#cashierFontSize').val();
				changeHW(h,w,'cashier');
				changePosition(t,l,'cashier');
				changeFontSize(fs,'cashier');
			});
			$('#remarksTop,#remarksLeft,#remarksHeight,#remarksWidth,#remarksFontSize').keyup(function(){
				var t = $('#remarksTop').val();
				var l = $('#remarksLeft').val();
				var h = $('#remarksHeight').val();
				var w = $('#remarksWidth').val();
				var fs = $('#remarksFontSize').val();
				changeHW(h,w,'remarks');
				changePosition(t,l,'remarks');
				changeFontSize(fs,'remarks');
			});
			$('#reservedTop,#reservedLeft,#reservedHeight,#reservedWidth,#reservedFontSize').keyup(function(){
				var t = $('#reservedTop').val();
				var l = $('#reservedLeft').val();
				var h = $('#reservedHeight').val();
				var w = $('#reservedWidth').val();
				var fs = $('#reservedFontSize').val();
				changeHW(h,w,'reserved');
				changePosition(t,l,'reserved');
				changeFontSize(fs,'reserved');
			});
			$('#drnumTop,#drnumLeft,#drnumHeight,#drnumWidth,#drnumFontSize').keyup(function(){
				var t = $('#drnumTop').val();
				var l = $('#drnumLeft').val();
				var h = $('#drnumHeight').val();
				var w = $('#drnumWidth').val();
				var fs = $('#drnumFontSize').val();
				changeHW(h,w,'drnum');
				changePosition(t,l,'drnum');
				changeFontSize(fs,'drnum');
			});
			$('#termsTop,#termsLeft,#termsHeight,#termsWidth,#termsFontSize').keyup(function(){
				var t = $('#termsTop').val();
				var l = $('#termsLeft').val();
				var h = $('#termsHeight').val();
				var w = $('#termsWidth').val();
				var fs = $('#termsFontSize').val();
				changeHW(h,w,'terms');
				changePosition(t,l,'terms');
				changeFontSize(fs,'terms');
			});
			$('#ponumTop,#ponumLeft,#ponumHeight,#ponumWidth,#ponumFontSize').keyup(function(){
				var t = $('#ponumTop').val();
				var l = $('#ponumLeft').val();
				var h = $('#ponumHeight').val();
				var w = $('#ponumWidth').val();
				var fs = $('#ponumFontSize').val();
				changeHW(h,w,'ponum');
				changePosition(t,l,'ponum');
				changeFontSize(fs,'ponum');
			});
			$('#tinTop,#tinLeft,#tinHeight,#tinWidth,#tinFontSize').keyup(function(){
				var t = $('#tinTop').val();
				var l = $('#tinLeft').val();
				var h = $('#tinHeight').val();
				var w = $('#tinWidth').val();
				var fs = $('#tinFontSize').val();
				changeHW(h,w,'tin');
				changePosition(t,l,'tin');
				changeFontSize(fs,'tin');
			});
			function changeHW(h,w,title){
				$("."+title).css({"height": h+"px", "width": w+"px"});
			}
			function changeW(w,title){
				$("."+title).css({"width": w+"px"});
			}
			function changePW(w,title){
				$("."+title).css({"paddingLeft": w+"px"});
			}

			$('#tdbarcodeLeft').keyup(function(){
				var w = $('#tdbarcodeLeft').val();
				changePW(w,'tdbarcode');
			});
			$('#tdqtyLeft').keyup(function(){
				var w = $('#tdqtyLeft').val();
				changePW(w,'tdqty');
			});
			$('#tddescriptionLeft').keyup(function(){
				var w = $('#tddescriptionLeft').val();
				changePW(w,'tddescription');
			});
			$('#tdpriceLeft').keyup(function(){
				var w = $('#tdpriceLeft').val();
				changePW(w,'tdprice');
			});

			$('#tdtotalLeft').keyup(function(){
				var w = $('#tdtotalLeft').val();
				changePW(w,'tdtotal');
			});
			$('#dateVisible').change(function(){
				changeVisibility($(this).is(":checked"),'date');
			});
			$('#dateBold').change(function(){
				changeBold($(this).is(":checked"),'date');
			});
			$('#membernameVisible').change(function(){
				changeVisibility($(this).is(":checked"),'membername');
			});
			$('#membernameBold').change(function(){
				changeBold($(this).is(":checked"),'membername');
			});
			$('#memberaddressVisible').change(function(){
				changeVisibility($(this).is(":checked"),'memberaddress');
			});
			$('#memberaddressBold').change(function(){
				changeBold($(this).is(":checked"),'memberaddress');
			});
			$('#stationnameVisible').change(function(){
				changeVisibility($(this).is(":checked"),'stationname');
			});
			$('#servicetypeVisible').change(function(){
				changeVisibility($(this).is(":checked"),'servicetype');
			});
			$('#stationnameBold').change(function(){
				changeBold($(this).is(":checked"),'stationname');
			});
			$('#servicetypeeBold').change(function(){
				changeBold($(this).is(":checked"),'servicetype');
			});
			$('#stationaddressVisible').change(function(){
				changeVisibility($(this).is(":checked"),'stationaddress');
			});
			$('#stationaddressBold').change(function(){
				changeBold($(this).is(":checked"),'stationaddress');
			});
			$('#itemtableVisible').change(function(){
				changeVisibility($(this).is(":checked"),'itemtable');
			});
			$('#itemtableBold').change(function(){
				changeBold($(this).is(":checked"),'itemtable');
			});
			$('#paymentsVisible').change(function(){
				changeVisibility($(this).is(":checked"),'payments');
			});
			$('#paymentsBold').change(function(){
				changeBold($(this).is(":checked"),'payments');
			});
			$('#payments2Visible').change(function(){
				changeVisibility($(this).is(":checked"),'payments2');
			});
			$('#payments2Bold').change(function(){
				changeBold($(this).is(":checked"),'payments2');
			});
			$('#payments3Visible').change(function(){
				changeVisibility($(this).is(":checked"),'payments3');
			});
			$('#payments3Bold').change(function(){
				changeBold($(this).is(":checked"),'payments3');
			});
			$('#cashierVisible').change(function(){
				changeVisibility($(this).is(":checked"),'cashier');
			});
			$('#cashierBold').change(function(){
				changeBold($(this).is(":checked"),'cashier');
			});
			$('#remarksVisible').change(function(){
				changeVisibility($(this).is(":checked"),'remarks');
			});
			$('#remarksBold').change(function(){
				changeBold($(this).is(":checked"),'remarks');
			});
			$('#reservedVisible').change(function(){
				changeVisibility($(this).is(":checked"),'reserved');
			});
			$('#reservedBold').change(function(){
				changeBold($(this).is(":checked"),'reserved');
			});
			$('#drnumVisible').change(function(){
				changeVisibility($(this).is(":checked"),'drnum');
			});
			$('#drnumBold').change(function(){
				changeBold($(this).is(":checked"),'drnum');
			});
			$('#termsVisible').change(function(){
				changeVisibility($(this).is(":checked"),'terms');
			});
			$('#termsBold').change(function(){
				changeBold($(this).is(":checked"),'terms');
			});
			$('#ponumVisible').change(function(){
				changeVisibility($(this).is(":checked"),'ponum');
			});
			$('#ponumBold').change(function(){
				changeBold($(this).is(":checked"),'ponum');
			});
			$('#tinVisible').change(function(){
				changeVisibility($(this).is(":checked"),'tin');
			});
			$('#tinBold').change(function(){
				changeBold($(this).is(":checked"),'tin');
			});
			$('#tdbarcodeVisible').change(function(){
				changeVisibility($(this).is(":checked"),'tdbarcode');
			});
			$('#tdbarcodeBold').change(function(){
				changeBold($(this).is(":checked"),'tdbarcode');
			});
			$('#tdqtyVisible').change(function(){
				changeVisibility($(this).is(":checked"),'tdqty');
			});
			$('#tdqtyBold').change(function(){
				changeBold($(this).is(":checked"),'tdqty');
			});
			$('#tddescriptionVisible').change(function(){
				changeVisibility($(this).is(":checked"),'tddescription');
			});
			$('#tddescriptionBold').change(function(){
				changeBold($(this).is(":checked"),'tddescription');
			});
			$('#tdpriceVisible').change(function(){
				changeVisibility($(this).is(":checked"),'tdprice');
			});
			$('#tdpriceBold').change(function(){
				changeBold($(this).is(":checked"),'tdprice');
			});
			$('#tdtotalVisible').change(function(){
				changeVisibility($(this).is(":checked"),'tdtotal');
			});
			$('#tdtotalBold').change(function(){
				changeBold($(this).is(":checked"),'tdtotal');
			});
			function changePosition(t,l,title){
				$("."+title).css({"top": t+"px", "left": l+"px"});
			}
			function changeFontSize(s,title){
				$("."+title).css({"font-size": s+"px"});
			}
			function changeVisibility(v,title){
				if(v){
					$("."+title).show();
				} else {
					$("."+title).hide();
				}
			}
			function changeBold(v,title){
				if(v){
					$("."+title).css({"font-weight": "bold"});
				} else {
					$("."+title).css({"font-weight": "normal"});
				}
			}


			$('#save').click(function(){
				$('.loading').show();
				var allstyle = {
					date: {
						visible: $('#dateVisible').is(':checked'),
						bold: $('#dateBold').is(':checked'),
						top : $('#dateTop').val(),
						left : $('#dateLeft').val(),
						height : $('#dateHeight').val(),
						width:$('#dateWidth').val(),
						fontSize:$('#dateFontSize').val()
					},
					membername: {
						visible: $('#membernameVisible').is(':checked'),
						bold: $('#membernameBold').is(':checked'),
						top : $('#membernameTop').val(),
						left : $('#membernameLeft').val(),
						height : $('#membernameHeight').val(),
						width:$('#membernameWidth').val(),
						fontSize:$('#membernameFontSize').val()
					},
					memberaddress: {
						visible: $('#memberaddressVisible').is(':checked'),
						bold: $('#memberaddressBold').is(':checked'),
						top : $('#memberaddressTop').val(),
						left : $('#memberaddressLeft').val(),
						height : $('#memberaddressHeight').val(),
						width: $('#memberaddressWidth').val(),
						fontSize:$('#memberaddressFontSize').val()

					},
					stationname: {
						visible: $('#stationnameVisible').is(':checked'),
						bold: $('#stationnameBold').is(':checked'),
						top : $('#stationnameTop').val(),
						left : $('#stationnameLeft').val(),
						height : $('#stationnameHeight').val(),
						width:$('#stationnameWidth').val(),
						fontSize:$('#stationnameFontSize').val()
					},
					servicetype: {
						visible: $('#servicetypeVisible').is(':checked'),
						bold: $('#servicetypeBold').is(':checked'),
						top : $('#servicetypeTop').val(),
						left : $('#servicetypeLeft').val(),
						height : $('#servicetypeHeight').val(),
						width:$('#servicetypeWidth').val(),
						fontSize:$('#servicetypeFontSize').val()
					},
					stationaddress: {
						visible: $('#stationaddressVisible').is(':checked'),
						bold: $('#stationaddressBold').is(':checked'),
						top : $('#stationaddressTop').val(),
						left : $('#stationaddressLeft').val(),
						height : $('#stationaddressHeight').val(),
						width:$('#stationaddressWidth').val(),
						fontSize:$('#stationaddressFontSize').val()
					},
					itemtable: {
						visible: $('#itemtableVisible').is(':checked'),
						bold: $('#itemtableBold').is(':checked'),
						top : $('#itemtableTop').val(),
						left : $('#itemtableLeft').val(),
						height : $('#itemtableHeight').val(),
						width:$('#itemtableWidth').val(),
						fontSize:$('#itemtableFontSize').val()
					},
					payments: {
						visible: $('#paymentsVisible').is(':checked'),
						bold: $('#paymentsBold').is(':checked'),
						top : $('#paymentsTop').val(),
						left : $('#paymentsLeft').val(),
						height : $('#paymentsHeight').val(),
						width:$('#paymentsWidth').val(),
						fontSize:$('#paymentsFontSize').val()
					},
					payments2: {
						visible: $('#payments2Visible').is(':checked'),
						bold: $('#payments2Bold').is(':checked'),
						top : $('#payments2Top').val(),
						left : $('#payments2Left').val(),
						height : $('#payments2Height').val(),
						width:$('#payments2Width').val(),
						fontSize:$('#payments2FontSize').val()
					},
					payments3: {
						visible: $('#payments3Visible').is(':checked'),
						bold: $('#payments3Bold').is(':checked'),
						top : $('#payments3Top').val(),
						left : $('#payments3Left').val(),
						height : $('#payments3Height').val(),
						width:$('#payments3Width').val(),
						fontSize:$('#payments3FontSize').val()
					},
					cashier: {
						visible: $('#cashierVisible').is(':checked'),
						bold: $('#cashierBold').is(':checked'),
						top : $('#cashierTop').val(),
						left : $('#cashierLeft').val(),
						height : $('#cashierHeight').val(),
						width:$('#cashierWidth').val(),
						fontSize:$('#cashierFontSize').val()
					},
					remarks: {
						visible: $('#remarksVisible').is(':checked'),
						bold: $('#remarksBold').is(':checked'),
						top : $('#remarksTop').val(),
						left : $('#remarksLeft').val(),
						height : $('#remarksHeight').val(),
						width:$('#remarksWidth').val(),
						fontSize:$('#remarksFontSize').val()
					},
					reserved: {
						visible: $('#reservedVisible').is(':checked'),
						bold: $('#reservedBold').is(':checked'),
						top : $('#reservedTop').val(),
						left : $('#reservedLeft').val(),
						height : $('#reservedHeight').val(),
						width:$('#reservedWidth').val(),
						fontSize:$('#reservedFontSize').val()
					},
					drnum: {
						visible: $('#drnumVisible').is(':checked'),
						bold: $('#drnumBold').is(':checked'),
						top : $('#drnumTop').val(),
						left : $('#drnumLeft').val(),
						height : $('#drnumHeight').val(),
						width:$('#drnumWidth').val(),
						fontSize:$('#drnumFontSize').val()
					},
					terms: {
						visible: $('#termsVisible').is(':checked'),
						bold: $('#termsBold').is(':checked'),
						top : $('#termsTop').val(),
						left : $('#termsLeft').val(),
						height : $('#termsHeight').val(),
						width:$('#termsWidth').val(),
						fontSize:$('#termsFontSize').val()
					},
					ponum: {
						visible: $('#ponumVisible').is(':checked'),
						bold: $('#ponumBold').is(':checked'),
						top : $('#ponumTop').val(),
						left : $('#ponumLeft').val(),
						height : $('#ponumHeight').val(),
						width:$('#ponumWidth').val(),
						fontSize:$('#ponumFontSize').val()
					},
					tin: {
						visible: $('#tinVisible').is(':checked'),
						bold: $('#tinBold').is(':checked'),
						top : $('#tinTop').val(),
						left : $('#tinLeft').val(),
						height : $('#tinHeight').val(),
						width:$('#tinWidth').val(),
						fontSize:$('#tinFontSize').val()
					},
					tdbarcode: {
						width : $('#tdbarcode').val(),
						left : $('#tdbarcodeLeft').val(),
						visible: $('#tdbarcodeVisible').is(':checked'),
						bold: $('#tdbarcodeBold').is(':checked')
					},
					tdqty: {
						width : $('#tdqty').val(),
						left : $('#tdqtyLeft').val(),
						visible: $('#tdqtyVisible').is(':checked'),
						bold: $('#tdqtyBold').is(':checked')
					},
					tddescription: {
						width : $('#tddescription').val(),
						left : $('#tddescriptionLeft').val(),
						visible: $('#tddescriptionVisible').is(':checked'),
						bold: $('#tddescriptionBold').is(':checked')
					},
					tdtotal: {
						width : $('#tdtotal').val(),
						left : $('#tdtotalLeft').val(),
						visible: $('#tdtotalVisible').is(':checked'),
						bold: $('#tdtotalBold').is(':checked')
					},
					tdprice: {
						width : $('#tdprice').val(),
						left : $('#tdpriceLeft').val(),
						visible: $('#tdpriceVisible').is(':checked'),
						bold: $('#tdpriceBold').is(':checked')
					}
				};
				var family = 'SERVICE';

				$.ajax({
					url:'../ajax/ajax_query.php',
					type:'post',
					data: {fid:family,styles:JSON.stringify(allstyle),functionName:'saveBarcode'},
					success: function(data){
						alertify.alert(data,function(){
							location.href ='service_form_generator.php';
						});
					},
					error:function(){

						$('.loading').hide();
					}
				});
			});

			$('#print').click(function(){
				Popup($('#conpage').html());
			});
			function Popup(data)
			{
				var mywindow = window.open('', 'new div', '');
				mywindow.document.write('<html><head><title></title><style>\
				.conpage{\
			width: 816px;\
			height: 1056px;\
			float:left;\
			position:relative;\
			border: 1px solid #000;\
		}\
		.date {\
			position:absolute;\
			visible:<?php echo $styles['date']['visible'] . "px" ?>;\
			top:<?php echo $styles['date']['top'] . "px" ?>;\
			left:<?php echo $styles['date']['left'] . "px" ?>;\
			width: <?php echo $styles['date']['width'] . "px" ?>;\
			height: <?php echo $styles['date']['height'] . "px" ?>;\
		}\
		.membername {\
			position:absolute;\
			visible:<?php echo $styles['membername']['visible'] . "px" ?>;\
			top:<?php echo $styles['membername']['top'] . "px" ?>;\
			left:<?php echo $styles['membername']['left'] . "px" ?>;\
			width: <?php echo $styles['membername']['width'] . "px" ?>;\
			height: <?php echo $styles['membername']['height'] . "px" ?>;\
		}\
		.memberaddress{\
			position:absolute;\
			visible:<?php echo $styles['memberaddress']['visible'] . "px" ?>;\
			top:<?php echo $styles['memberaddress']['top'] . "px" ?>;\
			left:<?php echo $styles['memberaddress']['left'] . "px" ?>;\
			width: <?php echo $styles['memberaddress']['width'] . "px" ?>;\
			height: <?php echo $styles['memberaddress']['height'] . "px" ?>;\
		}\
		.stationname{\
			position:absolute;\
			visible:<?php echo $styles['stationname']['visible'] . "px" ?>;\
			top:<?php echo $styles['stationname']['top'] . "px" ?>;\
			left:<?php echo $styles['stationname']['left'] . "px" ?>;\
			width: <?php echo $styles['stationname']['width'] . "px" ?>;\
			height: <?php echo $styles['stationname']['height'] . "px" ?>;\
		}\
		.servicetype{\
			position:absolute;\
			visible:<?php echo $styles['servicetype']['visible'] . "px" ?>;\
			top:<?php echo $styles['servicetype']['top'] . "px" ?>;\
			left:<?php echo $styles['servicetype']['left'] . "px" ?>;\
			width: <?php echo $styles['servicetype']['width'] . "px" ?>;\
			height: <?php echo $styles['servicetype']['height'] . "px" ?>;\
		}\
		.stationaddress{\
			position:absolute;\
			visible:<?php echo $styles['stationaddress']['visible'] . "px" ?>;\
			width: <?php echo $styles['stationaddress']['width'] . "px" ?>;\
			height: <?php echo $styles['stationaddress']['height'] . "px" ?>;\
			top:<?php echo $styles['stationaddress']['top'] . "px" ?>;\
			left:<?php echo $styles['stationaddress']['left'] . "px" ?>;\
		}\
		.itemtable{\
			position:absolute;\
			visible:<?php echo $styles['itemtable']['visible'] . "px" ?>;\
			top:<?php echo $styles['itemtable']['top'] . "px" ?>;\
			left:<?php echo $styles['itemtable']['left'] . "px" ?>;\
			width: <?php echo $styles['itemtable']['width'] . "px" ?>;\
			height: <?php echo $styles['itemtable']['height'] . "px" ?>;\
		}\
		.payments {\
			position:absolute;\
			visible:<?php echo $styles['payments']['visible'] . "px" ?>;\
			top:<?php echo $styles['payments']['top'] . "px" ?>;\
			left:<?php echo $styles['payments']['left'] . "px" ?>;\
			width: <?php echo $styles['payments']['width'] . "px" ?>;\
			height: <?php echo $styles['payments']['height'] . "px" ?>;\
		}\
		.payments2 {\
			position:absolute;\
			visible:<?php echo $styles['payments2']['visible'] . "px" ?>;\
			top:<?php echo $styles['payments2']['top'] . "px" ?>;\
			left:<?php echo $styles['payments2']['left'] . "px" ?>;\
			width: <?php echo $styles['payments2']['width'] . "px" ?>;\
			height: <?php echo $styles['payments2']['height'] . "px" ?>;\
		}\
		.payments3 {\
			position:absolute;\
			visible:<?php echo $styles['payments3']['visible'] . "px" ?>;\
			top:<?php echo $styles['payments3']['top'] . "px" ?>;\
			left:<?php echo $styles['payments3']['left'] . "px" ?>;\
			width: <?php echo $styles['payments3']['width'] . "px" ?>;\
			height: <?php echo $styles['payments3']['height'] . "px" ?>;\
		}\
		.cashier {\
			position:absolute;\
			visible:<?php echo $styles['cashier']['visible'] . "px" ?>;\
			top:<?php echo $styles['cashier']['top'] . "px" ?>;\
			left:<?php echo $styles['cashier']['left'] . "px" ?>;\
			width: <?php echo $styles['cashier']['width'] . "px" ?>;\
			height: <?php echo $styles['cashier']['height'] . "px" ?>;\
		}\
		.remarks {\
			position:absolute;\
			visible:<?php echo $styles['visible']['top'] . "px" ?>;\
			top:<?php echo $styles['remarks']['top'] . "px" ?>;\
			left:<?php echo $styles['remarks']['left'] . "px" ?>;\
			width: <?php echo $styles['remarks']['width'] . "px" ?>;\
			height: <?php echo $styles['remarks']['height'] . "px" ?>;\
		}\
		.draggable {\
			background-color:#999;\
			cursor:move;\
		}\
		.tdbarcode {\
			position:relative;\
			width: <?php echo $styles['tdbarcode']['width'] . "px" ?>;\
			padding-left: <?php echo $styles['tdbarcode']['left'] . "px" ?>;\
		}\
		.tdqty {\
			position:relative;\
			width: <?php echo $styles['tdqty']['width'] . "px" ?>;\
			padding-left: <?php echo $styles['tdbarcode']['left'] . "px" ?>;\
		}\
		.tddescription {\
			position:relative;\
			width: <?php echo $styles['tddescription']['width'] . "px" ?>;\
			padding-left: <?php echo $styles['tdbarcode']['left'] . "px" ?>;\
		}\
		.tdprice {\
			position:relative;\
			width: <?php echo $styles['tdprice']['width'] . "px" ?>;\
			padding-left: <?php echo $styles['tdbarcode']['left'] . "px" ?>;\
		}\
		.tdtotal {\
			position:relative;\
			width: <?php echo $styles['tdtotal']['width'] . "px" ?>;\
			padding-left: <?php echo $styles['tdbarcode']['left'] . "px" ?>;\
		}\
		</style>');
				/*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
				mywindow.document.write('</head><body style="padding:0;margin:0;">');
				mywindow.document.write(data);
				mywindow.document.write('</body></html>');
				mywindow.print();
				mywindow.close();
				return true;
			}
		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>