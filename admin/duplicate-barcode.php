<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';

	if(!$user->hasPermission('del_helper')){
		// redirect to denied page
		Redirect::to(1);
	}


	$width = '39mm';

	$height = '45mm';


	$deg = '90';

?>
	<style>

		.barcode{
			width:<?php echo $width; ?>;height: <?php echo $height; ?>;
			display: block;
		}
		.rotate {
			width:<?php echo $width; ?>;height: <?php echo $height; ?>;

			-ms-transform: rotate(<?php echo $deg; ?>deg);-webkit-transform: rotate(<?php echo $deg; ?>deg);  transform: rotate(<?php echo $deg; ?>deg);padding:0px !important;
		}
	</style>
	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Duplicate Barcode
			</h1>

		</div>

		<div class="container">
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<input type="text" value='2' class='form-control' id='item_per_row' placeholder='Item Per Row' >
						<span class="help-block">Item  Per Row</span>
					</div>
				</div>
				<div class="col-md-3" style='display:none;'>
					<div class="form-group">
						<input type="text" value='140' class='form-control' id='barcode_width' placeholder='Barcode Width' >
						<span class="help-block">Width</span>
					</div>
				</div>
				<div class="col-md-3" style='display:none;'>
					<div class="form-group">
						<input type="text" value='90' class='form-control' id='barcode_height' placeholder='Barcode Height' >
						<span class="help-block">Height</span>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<input type="text" value='' class='form-control' id='bc' placeholder='Serial/Barcode' >
						<span class='help-block'>Please Enter Serial or Barcode</span>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<button class='btn btn-default' id='add'>Add</button>
					</div>
				</div>
			</div>

			<?php

			//	dump( hash ( 'adler32' ,  '123-1asd-231asd-asd'));
			// hash then save
			?>

			<div id="con_barcode"></div>




			<button class='btn btn-default' id='btnPrint'>PRINT</button>
		</div>
	</div> <!-- end page content wrapper-->

	<script type="text/javascript" src="../js/barcode.js"></script>


	<script>

		$(document).ready(function(){

			document.addEventListener('keydown',function(event)
			{
				if(event.ctrlKey && event.keyCode==74)
				{
					event.preventDefault();
					console.log('Entered ctrl+j');
				}
			});
			window.focus();
			var ctr = 0;
			var dup_default = localStorage.getItem('barcode_default');

			if(dup_default){

				try{

					dup_default = JSON.parse(dup_default);

					if(dup_default.item_per_row){
						$('#item_per_row').val(dup_default.item_per_row);
					}

					if(dup_default.barcode_width){
						$('#barcode_width').val(dup_default.barcode_width);
					}

					if(dup_default.barcode_height){
						$('#barcode_height').val(dup_default.barcode_height);
					}

				} catch(e){

				}
			}

			$('body').on('keyup','#barcode_height,#barcode_width,#item_per_row',function(){
				var item_per_row = $('#item_per_row').val();
				var barcode_height = $('#barcode_height').val();
				var barcode_width = $('#barcode_width').val();
				var obj = {
					item_per_row:item_per_row,
					barcode_height:barcode_height,
					barcode_width:barcode_width
				}

				localStorage['barcode_default'] = JSON.stringify(obj);


			});
			$('body').on('click','#add',function(){

				var bc_value = $('#bc').val();

				addBarcode(bc_value);
				$('#bc').val('');

			});

			function addBarcode(v){

				var bc_svg = '<div class="rotate"><svg   class="barcode" jsbarcode-fontsize="40"   jsbarcode-height="85"  jsbarcode-fontsize="30" jsbarcode-value="'+v+'" jsbarcode-margin="0" jsbarcode-textmargin="0" ></svg></div>';

				ctr++;
				$('#con_barcode').append(bc_svg);
				var per_row = $('#item_per_row').val();
				if(ctr % per_row  == 0){
					$('#con_barcode').append("<div style='page-break-after: always'></div>");
				}

				JsBarcode(".barcode").init();

			}

			$('body').on('click','#btnPrint',function(){
				if($('#con_barcode').html() == ''){
					alert('Invalid request');
					return;
				}
				printBarcode($('#con_barcode').html());
			});

			function printBarcode(data){
				var mywindow = window.open('', 'new div', '');
				var width = $('#barcode_width').val();
				var height = $('#barcode_height').val();
				mywindow.document.write('<html><head><title></title><style>body{padding-right:1mm !important;padding-left:1mm !important; margin:0px !important;} .barcode{width:<?php echo $width; ?>; height:<?php echo $height; ?>;margin:0 !important; padding:0px !important;}.rotate {width:<?php echo $width; ?>;height: <?php echo $height; ?>; -ms-transform: rotate(<?php echo $deg; ?>deg);-webkit-transform: rotate(<?php echo $deg; ?>deg);  transform: rotate(<?php echo $deg; ?>deg);padding:0px !important; } </style>');
				mywindow.document.write('</head><body>');
				mywindow.document.write(data);
				mywindow.document.write('</body></html>');
				setTimeout(function() {
					mywindow.print();
					mywindow.close();

				}, 300);
				return true;
			}

			barcodeListener();

			function barcodeListener(){
				var millis = 300;
				var self = this;
				document.onkeypress = function(e) {
					e = e || window.event;

					if( e.key == 17)
					{
						console.log("??");
						e.preventDefault();

					}

					var charCode = (typeof e.which == "number") ? e.which : e.keyCode;

					if(localStorage.getItem("scan") && localStorage.getItem("scan") != 'null') {
						localStorage.setItem("scan", localStorage.getItem("scan") + String.fromCharCode(charCode));
					} else {
						localStorage.setItem("scan", String.fromCharCode(charCode));
						setTimeout(function() {
							localStorage.removeItem("scan");
						}, millis);
					}
					console.log(e.keyCode);
					if (e.keyCode === 13) {
						if(localStorage.getItem("scan").length >= 8) {
							console.log("Triggered");
							addBarcode(localStorage.getItem("scan"));
							localStorage.removeItem("scan");
						}
					 }

				}
			}

		});

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>