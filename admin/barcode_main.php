<?php
	// $user have all the properties and method of the current user
	require_once '../core/admininit.php';
	ob_start();
	$user = new User();

	if(!$user->isLoggedIn()){
		Redirect::to('pos/login.php');
	}
	$thiscompany = $user->getCompany($user->data()->company_id);
	if(!$user->hasPermission('branch')) {
		// redirect to denied page
		Redirect::to(1);
	}

	$barcodeClass = new Barcode();
	$barcode_format = $barcodeClass->get_active('barcode_generator',array('company_id','=','14'));
	$id = Input::get('id');
	$styles =  json_decode($barcode_format[0]->styling,true);
	if($id){
		$prod = new Product($id);
		$price = $prod->getPrice($id);
		$categ = new Category();
		$prodcateg = $categ->getProductCategory($user->data()->company_id,$prod->data()->category_id);

	}else {
		Redirect::to('product.php');
	}


?>
	<!doctype html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Barcode</title>

		<link href="../css/bootstrap.css" rel="stylesheet">

		<style>
				/* RESET */
			html,body,div,span,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,abbr,address,cite,code,del,dfn,em,img,ins,kbd,q,samp,small,strong,sub,sup,var,b,i,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td,article,aside,canvas,details,figcaption,figure,footer,header,hgroup,menu,nav,section,summary,time,mark,audio,video{margin:0;padding:0;border:0;outline:0;font-size:100%;vertical-align:baseline;background:transparent}
			body{line-height:1}
			article,aside,details,figcaption,figure,footer,header,hgroup,menu,nav,section{display:block}
			nav ul{list-style:none}
			blockquote,q{quotes:none}
			blockquote:before,blockquote:after,q:before,q:after{content:none}
			a{margin:0;padding:0;font-size:100%;vertical-align:baseline;background:transparent}
			ins{background-color:#ff9;color:#000;text-decoration:none}
			mark{background-color:#ff9;color:#000;font-style:italic;font-weight:bold}
			del{text-decoration:line-through}
			abbr[title],dfn[title]{border-bottom:1px dotted;cursor:help}
			table{border-collapse:collapse;border-spacing:0}
			hr{display:block;height:1px;border:0;border-top:1px solid #ccc;margin:1em 0;padding:0}
			input,select{vertical-align:middle}
			/* RESET */

			#content{
				position: relative;
				top:10px;
				left:10px;
			}
			.bcholder{
				width: <?php echo $styles['container']['width'] . "px" ?>;
				height: <?php echo $styles['container']['height'] . "px" ?>;
				float:left;
				position:relative;
				top: <?php echo $styles['container']['top'] . "px" ?>;
				left: <?php echo $styles['container']['left'] . "px" ?>;

			}
			.titleHeader{
				font-size: <?php echo $styles['title']['fontSize'] . "px" ?>;

				letter-spacing: <?php echo $styles['title']['letterSpacing'] . "px" ?>;
				font-family: Optima, Segoe, "Segoe UI", Candara, Calibri, Arial, sans-serif;
				font-weight: bold;
				position:absolute;
				top: <?php echo $styles['title']['top'] . "px" ?>;
				left:<?php echo $styles['title']['left'] . "px" ?>;

			}
			.bcbar{
				position:absolute;
				top:<?php echo $styles['bar']['top'] . "px" ?>;
				left:<?php echo $styles['bar']['left'] . "px" ?>;
				overflow: hidden;
				-ms-transform: scale(1.1, 1.1); /* IE 9 */
				-webkit-transform: scale(1.1, 1.1); /* Chrome, Safari, Opera */
				transform: scale(1.1, 1.1); /* Standard syntax */
			}
			.bclabel{
				position:absolute;
				letter-spacing: <?php echo $styles['barLabel']['letterSpacing'] . "px" ?>;
				font-size: <?php echo $styles['barLabel']['fontSize'] . "px" ?>;
				top:<?php echo $styles['barLabel']['top'] . "px" ?>;
				left:<?php echo $styles['barLabel']['left'] . "px" ?>;
				background-color:<?php echo $styles['barLabel']['backgroundColor'] . ' !important'; ?>;
				display: <?php echo  ($styles['barLabel']['display']) ? 'block' : 'none' ?>;
				font-weight: <?php echo  ($styles['barLabel']['fontWeight']) ? 'bold' : 'normal' ?>;
			}
			.extradesc{
				position:absolute;
				font-size: <?php echo $styles['extraDesc']['fontSize'] . "px" ?>;
				top:<?php echo $styles['extraDesc']['top'] . "px" ?>;
				left:<?php echo $styles['extraDesc']['left'] . "px" ?>;
				display: <?php echo  ($styles['extraDesc']['display']) ? 'block' : 'none' ?>;
				font-weight: <?php echo  ($styles['extraDesc']['fontWeight']) ? 'bold' : 'normal' ?>;

			}
			.itemcode{
				position:absolute;
				font-size: <?php echo $styles['itemcode']['fontSize'] . "px" ?>;
				top:<?php echo $styles['itemcode']['top'] . "px" ?>;
				left:<?php echo $styles['itemcode']['left'] . "px" ?>;
				display: <?php echo  ($styles['itemcode']['display']) ? 'block' : 'none' ?>;
				font-weight: <?php echo  ($styles['itemcode']['fontWeight']) ? 'bold' : 'normal' ?>;

			}
			.category{
				position:absolute;
				font-size: <?php echo $styles['category']['fontSize'] . "px" ?>;
				top:<?php echo $styles['category']['top'] . "px" ?>;
				left:<?php echo $styles['category']['left'] . "px" ?>;
				display: <?php echo  ($styles['category']['display']) ? 'block' : 'none' ?>;
				font-weight: <?php echo  ($styles['category']['fontWeight']) ? 'bold' : 'normal' ?>;
				padding:0;
				margin:0;

			}
			.price {
				position:absolute;
				padding:0;
				margin:0;
				font-size: <?php echo $styles['price']['fontSize'] . "px" ?>;
				top:<?php echo $styles['price']['top'] . "px" ?>;
				left:<?php echo $styles['price']['left'] . "px" ?>;
				display: <?php echo  ($styles['price']['display']) ? 'block' : 'none' ?>;
				font-weight: <?php echo  ($styles['price']['fontWeight']) ? 'bold' : 'normal' ?>;
			}
			.supcateg {
				position:absolute;
				padding:0;
				margin:0;
				font-size: <?php echo $styles['supcateg']['fontSize'] . "px" ?>;
				top:<?php echo $styles['supcateg']['top'] . "px" ?>;
				left:<?php echo $styles['supcateg']['left'] . "px" ?>;
				display: <?php echo  ($styles['supcateg']['display']) ? 'block' : 'none' ?>;
				font-weight: <?php echo  ($styles['supcateg']['fontWeight']) ? 'bold' : 'normal' ?>;
			}
			.storecode {
				position:absolute;
				padding:0;
				margin:0;
				font-size: <?php echo $styles['storecode']['fontSize'] . "px" ?>;
				top:<?php echo $styles['storecode']['top'] . "px" ?>;
				left:<?php echo $styles['storecode']['left'] . "px" ?>;
				display: <?php echo  ($styles['storecode']['display']) ? 'block' : 'none' ?>;
				font-weight: <?php echo  ($styles['storecode']['fontWeight']) ? 'bold' : 'normal' ?>;
				-ms-transform: <?php echo  ($styles['storecode']['rotate']) ? 'rotate('.$styles['storecode']['rotate'].'deg)' : 'rotate(0deg)'; ?>; /* IE 9 */
				-webkit-transform:<?php echo  ($styles['storecode']['rotate']) ? 'rotate('.$styles['storecode']['rotate'].'deg)' : 'rotate(0deg)'; ?>; /* Chrome, Safari, Opera */
				-moz-transform:<?php echo  ($styles['storecode']['rotate']) ? 'rotate('.$styles['storecode']['rotate'].'deg)' : 'rotate(0deg)'; ?>;
				transform: <?php echo   ($styles['storecode']['rotate']) ? 'rotate('.$styles['storecode']['rotate'].'deg)' : 'rotate(0deg)'; ?>;
			}
			.date {
				position:absolute;
				padding:0;
				margin:0;
				font-size: <?php echo $styles['date']['fontSize'] . "px" ?>;
				top:<?php echo $styles['date']['top'] . "px" ?>;
				left:<?php echo $styles['date']['left'] . "px" ?>;
				display: <?php echo  ($styles['date']['display']) ? 'block' : 'none' ?>;
				font-weight: <?php echo  ($styles['date']['fontWeight']) ? 'bold' : 'normal' ?>;
			}
			.draggable {
				background-color:#999;
				cursor:move;
			}
		</style>

	</head>
	<body>
	<div id="content" >
	<div id='bcholder' data-targetid='bcholderValue' class="bcholder canbedrag">
		<div data-targetid='titleValue' class='titleHeader canbedrag'><?php echo strtoupper($thiscompany->name); ?></div>
		<div data-targetid='bcbarValue' class='bcbar canbedrag'></div>
		<div  data-targetid='bclabelValue' class="bclabel canbedrag"><?php echo $prod->data()->barcode; ?></div>
		<div data-targetid='extradescValue' class="extradesc canbedrag"><?php  echo $styles['extraDesc']['value']; ?></div>
		<div data-targetid='itemcodeValue' class="itemcode canbedrag"><?php echo $prod->data()->item_code; ?></div>
		<div data-targetid='categoryValue' class="category canbedrag"><?php echo $prodcateg->name; ?></div>
		<div data-targetid='priceValue' class="price canbedrag"><?php echo number_format($price->price,2); ?></div>
		<div data-targetid='storecodeValue' class="storecode canbedrag"><?php  echo $styles['storecode']['value']; ?></div>
		<div data-targetid='dateValue' class="date canbedrag"></div>
		<div data-targetid='supcategValue' class="supcateg canbedrag"></div>
	</div>
	<div id="itemholder"></div>
	</div>


	<script type="text/javascript" src="../js/jquery.js"></script>
	<script type="text/javascript" src="../js/gridster.js"></script>
	<script type="text/javascript" src="../js/jquery-barcode.js"></script>
	<script>
		$(function(){
			$(".bcbar").barcode('<?php echo $prod->data()->barcode; ?>', '<?php echo ($styles['settings']['type']) ? $styles['settings']['type'] : 'code128' ?>', {
				barWidth: 1, barHeight: 25, showHRI: false, moduleSize: 5, output:'css'
			});
			howMany('<?php echo $styles['settings']['howmany']; ?>');
			function howMany(n){
				$('#itemholder').html('');
				for(var i=1;i<n;i++){
					$('#itemholder').append($('#bcholder').clone());
				}
			}
			window.print();
			location.href="product.php";
		});

	</script>
	</body>
	</html>
