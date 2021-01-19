<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('invoice_layout')){
		// redirect to denied page
		Redirect::to(1);
	}
	$barcodeClass = new Barcode();
	$barcode_format = $barcodeClass->get_cr_format($user->data()->company_id);

	$by_branch = $barcodeClass->get_format_by_branch($user->data()->branch_id,"CR");
	$has_own_layout = 0;
	if($by_branch){
		$has_own_layout = 1;
		$barcode_format = $by_branch;
	}

	$by_user = $barcodeClass->get_format_by_user($user->data()->id,"CR");
	$has_own_layout_user = 0;
	if($by_user){
		$has_own_layout_user = 1;
		$barcode_format = $by_user;

	}


	$styles =  json_decode($barcode_format->styling,true);



?>
	<!-- 	<link rel="stylesheet" href="../css/gridster.css" /> -->

	<style>
		.conpage{
			width: 1056px;
			height: 816px;
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
		.salestype {
			position:absolute;
			top:<?php echo $styles['salestype']['top'] . "px" ?>;
			left:<?php echo $styles['salestype']['left'] . "px" ?>;
			width: <?php echo $styles['salestype']['width'] . "px" ?>;
			height: <?php echo $styles['salestype']['height'] . "px" ?>;
			font-size: <?php echo $styles['salestype']['fontSize'] . "px" ?>;
			display: <?php echo  ($styles['salestype']['visible']) ? 'block' : 'none' ?>;
			font-weight: <?php echo  ($styles['salestype']['bold']) ? 'bold' : 'normal' ?>;
		}
		.deduction {
			position:absolute;
			top:<?php echo $styles['deduction']['top'] . "px" ?>;
			left:<?php echo $styles['deduction']['left'] . "px" ?>;
			width: <?php echo $styles['deduction']['width'] . "px" ?>;
			height: <?php echo $styles['deduction']['height'] . "px" ?>;
			font-size: <?php echo $styles['deduction']['fontSize'] . "px" ?>;
			display: <?php echo  ($styles['deduction']['visible']) ? 'block' : 'none' ?>;
			font-weight: <?php echo  ($styles['deduction']['bold']) ? 'bold' : 'normal' ?>;
		}
		.totalamount {
			position:absolute;
			top:<?php echo $styles['totalamount']['top'] . "px" ?>;
			left:<?php echo $styles['totalamount']['left'] . "px" ?>;
			width: <?php echo $styles['totalamount']['width'] . "px" ?>;
			height: <?php echo $styles['totalamount']['height'] . "px" ?>;
			font-size: <?php echo $styles['totalamount']['fontSize'] . "px" ?>;
			display: <?php echo  ($styles['totalamount']['visible']) ? 'block' : 'none' ?>;
			font-weight: <?php echo  ($styles['totalamount']['bold']) ? 'bold' : 'normal' ?>;
		}
		.totalreceipt {
			position:absolute;
			top:<?php echo $styles['totalreceipt']['top'] . "px" ?>;
			left:<?php echo $styles['totalreceipt']['left'] . "px" ?>;
			width: <?php echo $styles['totalreceipt']['width'] . "px" ?>;
			height: <?php echo $styles['totalreceipt']['height'] . "px" ?>;
			font-size: <?php echo $styles['totalreceipt']['fontSize'] . "px" ?>;
			display: <?php echo  ($styles['totalreceipt']['visible']) ? 'block' : 'none' ?>;
			font-weight: <?php echo  ($styles['totalreceipt']['bold']) ? 'bold' : 'normal' ?>;
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
		<?php for($i=1;$i<=11;$i++){
		$colname = 'col'.$i;
		?>
		.col<?php echo $i;?>{
			position:relative;
			width: <?php echo $styles[$colname]['width'] . "px" ?>;
			padding-left: <?php echo $styles[$colname]['left'] . "px" ?>;
			display: <?php echo  ($styles[$colname]['visible']) ? 'inline-block' : 'none' ?>;
			font-weight: <?php echo  ($styles[$colname]['bold']) ? 'bold' : 'normal' ?>;
		}
		<?php
		}?>

	</style>
	<!-- Page content -->
	<div id="page-content-wrapper">
		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<input type="hidden" id='has_own_layout' value="<?php echo $has_own_layout; ?>">
			<input type="hidden" id='has_own_layout_user' value="<?php echo $has_own_layout_user; ?>">
			<br>
			<div id="content" >
				<div id='conpage' data-targetid='conpageValue' class="conpage">
					<div data-targetid='date' class='date canbedrag'>Date</div>
					<div data-targetid='salestype' class='salestype canbedrag'>Sales Type</div>
					<div data-targetid='totalreceipt' class='totalreceipt canbedrag'>Total Receipt</div>
					<div data-targetid='deduction' class='deduction canbedrag'>Deduction</div>
					<div data-targetid='totalamount' class='totalamount canbedrag'>Total Amount</div>

					<div data-targetid='itemtable' class='itemtable canbedrag'>
						Drag table here
						<table class='tbl'>
							<tr class='indtr'>
								<?php for($i=1;$i<=11;$i++){ ?>
								<td class='col<?php echo $i; ?>' style=''>
									Column <?php echo $i; ?>
								</td>
								<?php } ?>
							</tr>
						</table>
					</div>
				</div>
			</div>
			<div id="editor" style='padding:10px;'>
				<div style='clear:both;'></div>
				<hr />
				<p class='text-muted'>*save first before printing.</p>
				<div class="row">
					<div class="col-md-3">
						<select name="user_id" id="user_id" class='form-control' <?php echo ($has_own_layout_user) ? 'disabled' : ''; ?>>
							<option value="1">Global</option>
							<option value="2"  <?php echo ($has_own_layout_user) ? 'selected' : ''; ?>>Only me</option>
						</select>

					</div>
					<div class="col-md-6">
						<input type="button" class='btn btn-primary' value='SAVE' id='save'/> <input type="button" class='btn btn-primary' value='PRINT' id='print'/>
					</div>
				</div>

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
						<td>Sales Type</td>
						<td><input type="checkbox" id='salestypeVisible' <?php echo ($styles['salestype']['visible']) ? 'checked' : ''; ?> /></td>
						<td><input type="checkbox" id='salestypeBold' <?php echo ($styles['salestype']['bold']) ? 'checked' : ''; ?> /></td>
						<td><input  class='form-control' type='text' id='salestypeTop' value='<?php echo $styles['salestype']['top']; ?>' /></td>
						<td><input class='form-control' type='text' id='salestypeLeft' value='<?php echo $styles['salestype']['left']; ?>'/></td>
						<td><input  class='form-control' disabled type='text' id='salestypeWidth' value='<?php echo $styles['salestype']['width']; ?>'/></td>
						<td><input class='form-control' disabled type='text' id='salestypeHeight' value="<?php echo $styles['salestype']['height'];  ?>"/></td>
						<td><input class='form-control'  type='text' id='salestypeFontSize' value="<?php echo $styles['salestype']['fontSize'];  ?>"/></td>

					</tr>
					<tr >
						<td>Total Receipt</td>
						<td><input type="checkbox" id='totalreceiptVisible' <?php echo ($styles['totalreceipt']['visible']) ? 'checked' : ''; ?> /></td>
						<td><input type="checkbox" id='totalreceiptBold' <?php echo ($styles['totalreceipt']['bold']) ? 'checked' : ''; ?> /></td>
						<td><input  class='form-control' type='text' id='totalreceiptTop' value='<?php echo $styles['totalreceipt']['top']; ?>' /></td>
						<td><input class='form-control' type='text' id='totalreceiptLeft' value='<?php echo $styles['totalreceipt']['left']; ?>'/></td>
						<td><input  class='form-control' disabled type='text' id='totalreceiptWidth' value='<?php echo $styles['totalreceipt']['width']; ?>'/></td>
						<td><input class='form-control' disabled type='text' id='totalreceiptHeight' value="<?php echo $styles['totalreceipt']['height'];  ?>"/></td>
						<td><input class='form-control'  type='text' id='totalreceiptFontSize' value="<?php echo $styles['totalreceipt']['fontSize'];  ?>"/></td>
					</tr>
					<tr >
						<td>Deduction</td>
						<td><input type="checkbox" id='deductionVisible' <?php echo ($styles['deduction']['visible']) ? 'checked' : ''; ?> /></td>
						<td><input type="checkbox" id='deductionBold' <?php echo ($styles['deduction']['bold']) ? 'checked' : ''; ?> /></td>
						<td><input  class='form-control' type='text' id='deductionTop' value='<?php echo $styles['deduction']['top']; ?>' /></td>
						<td><input class='form-control' type='text' id='deductionLeft' value='<?php echo $styles['deduction']['left']; ?>'/></td>
						<td><input  class='form-control' disabled type='text' id='deductionWidth' value='<?php echo $styles['deduction']['width']; ?>'/></td>
						<td><input class='form-control' disabled type='text' id='deductionHeight' value="<?php echo $styles['deduction']['height'];  ?>"/></td>
						<td><input class='form-control'  type='text' id='deductionFontSize' value="<?php echo $styles['deduction']['fontSize'];  ?>"/></td>
					</tr>
					<tr >
						<td>Total Amount</td>
						<td><input type="checkbox" id='totalamountVisible' <?php echo ($styles['totalamount']['visible']) ? 'checked' : ''; ?> /></td>
						<td><input type="checkbox" id='totalamountBold' <?php echo ($styles['totalamount']['bold']) ? 'checked' : ''; ?> /></td>
						<td><input  class='form-control' type='text' id='totalamountTop' value='<?php echo $styles['totalamount']['top']; ?>' /></td>
						<td><input class='form-control' type='text' id='totalamountLeft' value='<?php echo $styles['totalamount']['left']; ?>'/></td>
						<td><input  class='form-control' disabled type='text' id='totalamountWidth' value='<?php echo $styles['totalamount']['width']; ?>'/></td>
						<td><input class='form-control' disabled type='text' id='totalamountHeight' value="<?php echo $styles['totalamount']['height'];  ?>"/></td>
						<td><input class='form-control'  type='text' id='totalamountFontSize' value="<?php echo $styles['totalamount']['fontSize'];  ?>"/></td>
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
					<?php for($i =1; $i<=11;$i++){
							$colname = 'col'.$i;
						?>
						<tr >
							<td>Col <?php echo $i; ?></td>
							<td><input type="checkbox" id='col<?php echo $i; ?>Visible' <?php echo ($styles[$colname]['visible']) ? 'checked' : ''; ?> /></td>
							<td><input type="checkbox" id='col<?php echo $i; ?>Bold' <?php echo ($styles[$colname]['Bold']) ? 'checked' : ''; ?> /></td>
							<td><input type="text" class='form-control' disabled/></td>
							<td><input  class='form-control' type='text' id='col<?php echo $i; ?>Left' value='<?php echo $styles[$colname]['left']; ?>'/></td>
							<td><input type="text" class='form-control' disabled/></td>
							<td><input  class='form-control' type='text' id='col<?php echo $i; ?>Width' value='<?php echo $styles[$colname]['width']; ?>'/></td>
						</tr>
					<?php
					}?>



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
						} else if(targetid == 'salestype'){
							updateSalestype(dposition.top,dposition.left);
						}else if(targetid == 'totalreceipt'){
							updateTotalreceipt(dposition.top,dposition.left);
						}else if(targetid == 'deduction'){
							updateDeduction(dposition.top,dposition.left);
						}else if(targetid == 'totalamount'){
							updateTotalamount(dposition.top,dposition.left);
						}else if(targetid == 'itemtable'){
							updateItemtable(dposition.top,dposition.left);
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

			function updateSalestype(t,l){
				$('#salestypeTop').val(t);
				$('#salestypeLeft').val(l);
			}
			function updateTotalreceipt(t,l){
				$('#totalreceiptTop').val(t);
				$('#totalreceiptLeft').val(l);
			}
			function updateTotalamount(t,l){
				$('#totalamountTop').val(t);
				$('#totalamountLeft').val(l);
			}
			function updateDeduction(t,l){
				$('#deductionTop').val(t);
				$('#deductionLeft').val(l);
			}

			function updateItemtable(t,l){
				$('#itemtableTop').val(t);
				$('#itemtableLeft').val(l);
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
			$('#salestypeTop,#salestypeLeft,#salestypeHeight,#salestypeWidth,#salestypeFontSize').keyup(function(){
				var t = $('#salestypeTop').val();
				var l = $('#salestypeLeft').val();
				var h = $('#salestypeHeight').val();
				var w = $('#salestypeWidth').val();
				var fs = $('#salestypeFontSize').val();
				changeHW(h,w,'salestype');
				changePosition(t,l,'salestype');
				changeFontSize(fs,'salestype');
			});
			$('#totalreceiptTop,#totalreceiptLeft,#totalreceiptHeight,#totalreceiptWidth,#totalreceiptFontSize').keyup(function(){
				var t = $('#totalreceiptTop').val();
				var l = $('#totalreceiptLeft').val();
				var h = $('#totalreceiptHeight').val();
				var w = $('#totalreceiptWidth').val();
				var fs = $('#totalreceiptFontSize').val();
				changeHW(h,w,'totalreceipt');
				changePosition(t,l,'totalreceipt');
				changeFontSize(fs,'totalreceipt');
			});
			$('#totalamountTop,#totalamountLeft,#totalamountHeight,#totalamountWidth,#totalamountFontSize').keyup(function(){
				var t = $('#totalamountTop').val();
				var l = $('#totalamountLeft').val();
				var h = $('#totalamountHeight').val();
				var w = $('#totalamountWidth').val();
				var fs = $('#totalamountFontSize').val();
				changeHW(h,w,'totalamount');
				changePosition(t,l,'totalamount');
				changeFontSize(fs,'totalamount');
			});
			$('#deductionTop,#deductionLeft,#deductionHeight,#deductionWidth,#deductionFontSize').keyup(function(){
				var t = $('#deductionTop').val();
				var l = $('#deductionLeft').val();
				var h = $('#deductionHeight').val();
				var w = $('#deductionWidth').val();
				var fs = $('#deductionFontSize').val();
				changeHW(h,w,'deduction');
				changePosition(t,l,'deduction');
				changeFontSize(fs,'deduction');
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
			/*
			$('#tdbarcode').keyup(function(){
				var w = $('#tdbarcode').val();
				changeW(w,'tdbarcode');
			});
			*/
			<?php for($i = 1;$i<=11;$i++){
					?>
			$('#col<?php echo $i; ?>Width').keyup(function(){
				var w = $('#col<?php echo $i; ?>Width').val();
				changeW(w,'col<?php echo $i; ?>');
			});
			<?php } ?>

			function changeHW(h,w,title){
				$("."+title).css({"height": h+"px", "width": w+"px"});
			}
			function changeW(w,title){
				$("."+title).css({"width": w+"px"});
			}
			function changePW(w,title){
				$("."+title).css({"paddingLeft": w+"px"});
			}
			<?php for($i = 1;$i<=11;$i++){
							?>
			$('#col<?php echo $i; ?>Left').keyup(function(){
				var w = $('#col<?php echo $i; ?>Left').val();
				changePW(w,'col<?php echo $i; ?>');
			});
			<?php } ?>
			/*
			$('#tdbarcodeLeft').keyup(function(){
				var w = $('#tdbarcodeLeft').val();
				changePW(w,'tdbarcode');
			}); */

			$('#dateVisible').change(function(){
				changeVisibility($(this).is(":checked"),'date');
			});
			$('#dateBold').change(function(){
				changeBold($(this).is(":checked"),'date');
			});
			$('#salestypeVisible').change(function(){
				changeVisibility($(this).is(":checked"),'salestype');
			});
			$('#salestypeBold').change(function(){
				changeBold($(this).is(":checked"),'salestype');
			});
			$('#totalreceiptVisible').change(function(){
				changeVisibility($(this).is(":checked"),'totalreceipt');
			});
			$('#totalreceiptBold').change(function(){
				changeBold($(this).is(":checked"),'totalreceipt');
			});
			$('#deductionVisible').change(function(){
				changeVisibility($(this).is(":checked"),'deduction');
			});
			$('#deductionBold').change(function(){
				changeBold($(this).is(":checked"),'deduction');
			});
			$('#totalamountVisible').change(function(){
				changeVisibility($(this).is(":checked"),'totalamount');
			});
			$('#totalamountBold').change(function(){
				changeBold($(this).is(":checked"),'totalamount');
			});
			$('#itemtableVisible').change(function(){
				changeVisibility($(this).is(":checked"),'itemtable');
			});
			<?php for($i = 1;$i<=11;$i++){
										?>
			$('#col<?php echo $i; ?>Visible').change(function(){
				changeVisibility($(this).is(":checked"),'col<?php echo $i; ?>');
			});
			<?php } ?>
			function changePosition(t,l,title){
				$("."+title).css({"top": t+"px", "left": l+"px"});
			}
			function changeFontSize(s,title){
				$("."+title).css({"font-size": s+"px"});
			}
			function changeVisibility(v,title){
				console.log(v + " " + title);
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
				var has_own_layout = $('#has_own_layout').val();
				var has_own_layout_user = $('#has_own_layout_user').val();

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
					itemtable: {
						visible: $('#itemtableVisible').is(':checked'),
						bold: $('#itemtableBold').is(':checked'),
						top : $('#itemtableTop').val(),
						left : $('#itemtableLeft').val(),
						height : $('#itemtableHeight').val(),
						width:$('#itemtableWidth').val(),
						fontSize:$('#itemtableFontSize').val()
					},
					salestype: {
						visible: $('#salestypeVisible').is(':checked'),
						bold: $('#salestypeBold').is(':checked'),
						top : $('#salestypeTop').val(),
						left : $('#salestypeLeft').val(),
						height : $('#salestypeHeight').val(),
						width:$('#salestypeWidth').val(),
						fontSize:$('#salestypeFontSize').val()
					},
					totalamount: {
						visible: $('#totalamountVisible').is(':checked'),
						bold: $('#totalamountBold').is(':checked'),
						top : $('#totalamountTop').val(),
						left : $('#totalamountLeft').val(),
						height : $('#totalamountHeight').val(),
						width:$('#totalamountWidth').val(),
						fontSize:$('#totalamountFontSize').val()
					},
					totalreceipt: {
						visible: $('#totalreceiptVisible').is(':checked'),
						bold: $('#totalreceiptBold').is(':checked'),
						top : $('#totalreceiptTop').val(),
						left : $('#totalreceiptLeft').val(),
						height : $('#totalreceiptHeight').val(),
						width:$('#totalreceiptWidth').val(),
						fontSize:$('#totalreceiptFontSize').val()
					},
					deduction: {
						visible: $('#deductionVisible').is(':checked'),
						bold: $('#deductionBold').is(':checked'),
						top : $('#deductionTop').val(),
						left : $('#deductionLeft').val(),
						height : $('#deductionHeight').val(),
						width:$('#deductionWidth').val(),
						fontSize:$('#deductionFontSize').val()
					}
					<?php for($i = 1;$i<=11;$i++){
					?>
					,
					col<?php echo $i; ?>: {
						width : $('#col<?php echo $i; ?>Width').val(),
						left : $('#col<?php echo $i; ?>Left').val(),
						visible: $('#col<?php echo $i; ?>Visible').is(':checked'),
						bold: $('#col<?php echo $i; ?>Bold').is(':checked')
					}
					<?php
					}?>
				};
				var family = 'CR';
				var user_id = $('#user_id').val();
				$.ajax({
					url:'../ajax/ajax_query.php',
					type:'post',
					data: {fid:family,styles:JSON.stringify(allstyle),functionName:'saveBarcode',user_id:user_id,has_own_layout:has_own_layout,has_own_layout_user:has_own_layout_user},
					success: function(data){
						alertify.alert(data,function(){
							location.href ='cr_layout.php';
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
		.salestype {\
			position:absolute;\
			visible:<?php echo $styles['salestype']['visible'] . "px" ?>;\
			top:<?php echo $styles['salestype']['top'] . "px" ?>;\
			left:<?php echo $styles['salestype']['left'] . "px" ?>;\
			width: <?php echo $styles['salestype']['width'] . "px" ?>;\
			height: <?php echo $styles['salestype']['height'] . "px" ?>;\
		}\
		.memberaddress{\
			position:absolute;\
			visible:<?php echo $styles['memberaddress']['visible'] . "px" ?>;\
			top:<?php echo $styles['memberaddress']['top'] . "px" ?>;\
			left:<?php echo $styles['memberaddress']['left'] . "px" ?>;\
			width: <?php echo $styles['memberaddress']['width'] . "px" ?>;\
			height: <?php echo $styles['memberaddress']['height'] . "px" ?>;\
		}\
		.col1 {\
			position:relative;\
			width: <?php echo $styles['col1']['width'] . "px" ?>;\
			padding-left: <?php echo $styles['col1']['left'] . "px" ?>;\
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