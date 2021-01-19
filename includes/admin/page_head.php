<?php
	require_once '../core/admininit.php';

	ob_start();
	$user = new User();
	if(!$user->isLoggedIn()) {
		Redirect::to('../login.php');
	}

	$thiscompany = $user->getCompany($user->data()->company_id);
	$sb_alert_class = new Alert_item();
	$sb_alert_count = $sb_alert_class->getAlert($user->data()->position_id, $user->data()->company_id, $user->data()->id);
	$config_style_cls = new Style();
	$config_styles = $config_style_cls->getActivatedStyle($user->data()->company_id);
	$decoded_themes = json_decode($config_styles->styles);
	$configurations_cls = new Configuration();
	$configurations_cls->getConfig($user->data()->company_id);

	include_once 'includes/labels.php';

?>
<!DOCTYPE html>
<html lang="en" >
<head>
	<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#2c3e50">
	<title>Apollo Systems</title>
	<!--
	<link href="../css/bootstrap.css" rel="stylesheet">
	<link href="../css/select2.css" rel="stylesheet">
	<link href="../css/select2_bootstrap.css" rel="stylesheet">
	<link href="../css/bs_datepicker.css" rel="stylesheet">

	<link rel="stylesheet" href="../css/dataTablesCss.css" />
	<link href="../css/alertify.core.css" rel="stylesheet">
	<link href="../css/alertify.bootstrap.css" rel="stylesheet">
	<link href="../css/toastr.min.css" rel="stylesheet">
	<link rel="stylesheet" href="../css/morris.css" />
	<link rel="stylesheet" href="../css/bootflat.css" />
	-->
	<link rel='shortcut icon' href='../css/img/logo.jpg?v=4'/>
	<link href="../css/all.css" rel="stylesheet">
	<link href="../css/font-awesome/css/font-awesome.css?ss" rel="stylesheet">
	<link href="../css/admin.css" rel="stylesheet">


	<script src="../js/jquery.js"></script>
	<script src="../js/bootstrap.min.js"></script>
	<script src="../js/select2.js"></script>
	<script src="../js/bootstrap-datepicker.js"></script>
	<script src="../js/jquery.dataTables.js"></script>
	<script src="../js/dataTablesJs.js"></script>
	<script type="text/javascript" src="../js/jquery.cookie.js"></script>
	<script src="../js/toastr.min.js"></script>
	<script src='../js/morris.js'></script>
	<script src='../js/raphael.js'></script>
	<script src="../js/alertify.min.js"></script>
	<script src="../js/feedback.js"></script>
	<!-- <script src="../js/all.js"></script> -->
	<script src="../js/tinymce.min.js"></script>
	<script src="../js/jquery.tinymce.min.js"></script>


	<style type='text/css'>
		#wrapper {

		}
		.navPage{
			cursor: pointer;
		}
		.navbar-custom {
			background-color: <?php echo (isset($decoded_themes->header_background_color)) ? $decoded_themes->header_background_color :'#222'; ?>;
			color: <?php echo (isset($decoded_themes->header_link_color)) ? $decoded_themes->header_link_color :'#fff'; ?>;
			border-radius: 0;
			position: fixed;
			width: 100%;
			z-index: 1001;

		}

		.navbar-custom .navbar-nav > li > a {
			color: <?php echo (isset($decoded_themes->header_link_color)) ? $decoded_themes->header_link_color :'#fff'; ?>;
			padding-left: 12px;
			padding-right: 12px;
		}

		.navbar-custom .navbar-nav > .active > a, .navbar-nav > .active > a:hover, .navbar-nav > .active > a:focus {
			color: <?php echo (isset($decoded_themes->header_hover_color)) ? $decoded_themes->header_hover_color :'#fff'; ?>;
			background-color: transparent;
		}

		#mainposlink {
			color: <?php echo (isset($decoded_themes->header_link_color)) ? $decoded_themes->header_link_color :'#fff'; ?>;
		}

		.navbar-custom .navbar-nav > li > a:hover, .nav > li > a:focus {
			text-decoration: none;
			background-color: <?php echo (isset($decoded_themes->header_hover_color)) ? $decoded_themes->header_hover_color :'#333'; ?>;
		}

		.navbar-custom .navbar-nav > li.dropdown > ul.dropdown-menu > li > a:hover {
			text-decoration: none;
			background-color: #aaa;
		}

		.navbar-custom .navbar-nav > li.dropdown > ul.dropdown-menu > li {
			min-width: 250px;
		}

		.navbar-custom .navbar-nav li.dropdown.open > .dropdown-toggle, .navbar .nav li.dropdown.active > .dropdown-toggle, .navbar .nav li.dropdown.open.active > .dropdown-toggle {
			background-color: <?php echo (isset($decoded_themes->header_background_color)) ? $decoded_themes->header_background_color :'#333'; ?>;
		}

		.navbar-custom .navbar-brand {
			color: #eeeeee;
		}

		.navbar-custom .navbar-toggle {
			background-color: #eeeeee;
		}

		.navbar-custom .icon-bar {
			background-color: <?php echo (isset($decoded_themes->header_background_color)) ? $decoded_themes->header_background_color :'#333'; ?>;
		}

		.table > tbody > tr > th, .table > tbody > tr > td {
			border-top: none;
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

		.page_sortby {
			cursor: pointer;
		}

		#alertify {
			z-index: 99999999999999;
		}

		/* can be edited*/
		#navhider {
			position: fixed;
			top: 50%;
			margin-left: -5px;
			padding: 8px 10px;
			opacity: 0.8;
			z-index: 999999999;
			background: #000;
			color: #fff;
			-webkit-border-top-right-radius: 30px;
			-webkit-border-bottom-right-radius: 30px;
			-moz-border-radius-topright: 30px;
			-moz-border-radius-bottomright: 30px;
			border-top-right-radius: 30px;
			border-bottom-right-radius: 30px;
			font-size: 1.1em;
			cursor: pointer;

		}

		#accordion .panel-default {
			border: none;
		}

		#accordion .panel-heading {
			background-color: <?php echo (isset($decoded_themes->sidebar_background_color)) ? $decoded_themes->sidebar_background_color :'#222'; ?>;
			color: <?php echo (isset($decoded_themes->sidebar_text_color)) ? $decoded_themes->sidebar_text_color :'#fff'; ?>;;
		}

		#accordion .panel-collapse {
			background-color: <?php echo (isset($decoded_themes->sidebar_background_color)) ? $decoded_themes->sidebar_background_color :'#222'; ?>;
		}

		#accordion .panel-collapse a {
			color: <?php echo (isset($decoded_themes->sidebar_link_color)) ? $decoded_themes->sidebar_link_color :'#428bca'; ?>;
		}

		#sidebar-wrapper {
			margin-left: -250px;
			padding-left: 30px;
			left: 0px;
			width: 250px;
			background: <?php echo (isset($decoded_themes->sidebar_background_color)) ? $decoded_themes->sidebar_background_color :'#222'; ?>;
			position: fixed;
			height: 100%;
			overflow-y: auto;
			z-index: 1000;
			transition: all 0.4s ease 0s;
		}
		body{
			overflow-y: hidden ;
		}

		.panel-primary > .panel-heading {
			color: #fff;
			background-color: <?php echo (isset($decoded_themes->panel_head_color)) ? $decoded_themes->panel_head_color :'#428bca'; ?>;
			border-color: <?php echo (isset($decoded_themes->panel_border_color)) ? $decoded_themes->panel_border_color :'#428bca'; ?>;
		}

		.panel-primary {
			border-color: <?php echo (isset($decoded_themes->panel_border_color)) ? $decoded_themes->panel_border_color :'#428bca'; ?>;
		}

		.panel-primary > .panel-body {
			border: 1px solid <?php echo (isset($decoded_themes->panel_border_color)) ? $decoded_themes->panel_border_color :'#428bca'; ?>;
		}

		<?php
			if(isset($decoded_themes->btnp_background_color)){
			?>
		.btn-primary {
			background-color: <?php echo (isset($decoded_themes->btnp_background_color)) ? $decoded_themes->btnp_background_color :'#428bca'; ?>;
			border-color: <?php echo (isset($decoded_themes->btnp_background_color)) ? $decoded_themes->btnp_background_color :'#428bca'; ?>;

		}

		.btn-primary:hover {
			background-color: <?php echo (isset($decoded_themes->btnp_hover_color)) ? $decoded_themes->btnp_hover_color :'#428bca'; ?>;
			border-color: <?php echo (isset($decoded_themes->btnp_background_color)) ? $decoded_themes->btnp_background_color :'#428bca'; ?>;

		}

		<?php
		}
	?>

		.topBordered > tbody > tr > td{
			border-top:1px solid #ccc;
		}

		.lds-spinner {
			color: official;
			display: inline-block;
			position: relative;
			width: 64px;
			height: 64px;
		}
		.lds-spinner div {
			transform-origin: 32px 32px;
			animation: lds-spinner 1.2s linear infinite;
		}
		.lds-spinner div:after {
			content: " ";
			display: block;
			position: absolute;
			top: 3px;
			left: 29px;
			width: 5px;
			height: 14px;
			border-radius: 20%;
			background: #fff;
		}
		.lds-spinner div:nth-child(1) {
			transform: rotate(0deg);
			animation-delay: -1.1s;
		}
		.lds-spinner div:nth-child(2) {
			transform: rotate(30deg);
			animation-delay: -1s;
		}
		.lds-spinner div:nth-child(3) {
			transform: rotate(60deg);
			animation-delay: -0.9s;
		}
		.lds-spinner div:nth-child(4) {
			transform: rotate(90deg);
			animation-delay: -0.8s;
		}
		.lds-spinner div:nth-child(5) {
			transform: rotate(120deg);
			animation-delay: -0.7s;
		}
		.lds-spinner div:nth-child(6) {
			transform: rotate(150deg);
			animation-delay: -0.6s;
		}
		.lds-spinner div:nth-child(7) {
			transform: rotate(180deg);
			animation-delay: -0.5s;
		}
		.lds-spinner div:nth-child(8) {
			transform: rotate(210deg);
			animation-delay: -0.4s;
		}
		.lds-spinner div:nth-child(9) {
			transform: rotate(240deg);
			animation-delay: -0.3s;
		}
		.lds-spinner div:nth-child(10) {
			transform: rotate(270deg);
			animation-delay: -0.2s;
		}
		.lds-spinner div:nth-child(11) {
			transform: rotate(300deg);
			animation-delay: -0.1s;
		}
		.lds-spinner div:nth-child(12) {
			transform: rotate(330deg);
			animation-delay: 0s;
		}
		@keyframes lds-spinner {
			0% {
				opacity: 1;
			}
			100% {
				opacity: 0;
			}
		}
		.imgonnastick{

			position: fixed;
			z-index: 10;
			bottom: -30px;
			right:20px;
		}

	</style>

	<script>
		//"use strict";
		function number_format(number, decimals, dec_point, thousands_sep) {


			number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
			var n = !isFinite(+number) ? 0 : +number, prec = !isFinite(+decimals) ? 0 : Math.abs(decimals), sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep, dec = (typeof dec_point === 'undefined') ? '.' : dec_point, s = '', toFixedFix = function(n, prec) {
					var k = Math.pow(10, prec);
					return '' + (Math.round(n * k) / k).toFixed(prec);
				};
			// Fix for IE parseFloat(0.55).toFixed(0) = 0;
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

		function showToast(label, msg, title, position) {
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
			toastr[label](msg, title);
		}


		var tempToast = function(label, msg, title, options) {
			// This is the easiest way to have default options.
			var settings = $.extend({
				// These are the defaults.
				"closeButton": false,
				"debug": false,
				"positionClass": "toast-bottom-right",
				"onclick": null,
				"showDuration": "300",
				"hideDuration": "1000",
				"timeOut": "3000",
				"extendedTimeOut": "1000",
				"showEasing": "swing",
				"hideEasing": "linear",
				"showMethod": "fadeIn",
				"hideMethod": "fadeOut"
			}, options);

			toastr.options = settings;
			toastr[label](msg, title);
		};

		function escapeRegExp(string) {
			return string.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
		}
		function replaceAll(string, find, replace) {
			return string.replace(new RegExp(escapeRegExp(find), 'g'), replace);
		}
		function addCommas(x) {
			x = replaceAll(x, ',', '');
			var parts = x.toString().split(".");
			parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
			return parts.join(".");
		}

		$(function() {
			//"use strict";
			function formatItem(o) {

				if(!o.id)
					return o.text; // optgroup
				else {
					var r = o.text.split(':');
					return "<span> " + r[0] + "</span> <span style='margin-left:10px'>" + r[1] + "</span><span style='display:block;margin-top:5px;'  class='text-danger'><small class='testspanclass'>" + r[2] + "</small></span>";
				}
			}

			$('body').on('keyup', '.addcomma', function() {
				var cont = $(this);
				cont.val(addCommas(cont.val()));
			});
			$('body').on('click', '.removeImage', function() {
				$('#imagecon').hide();
			});

			$(".selectitem").select2({
				placeholder: 'Item code',
				allowClear: true,
				minimumInputLength: 2,
				formatResult: formatItem,
				formatSelection: formatItem,
				escapeMarkup: function(m) {
					return m;
				},
				ajax: {
					url: '../ajax/ajax_query.php',
					dataType: 'json',
					type: "POST",
					quietMillis: 50,
					data: function(term) {
						return {
							search: term, functionName: 'searchItemJSON'
						};
					},
					results: function(data) {
						return {
							results: $.map(data, function(item) {
								return {
									text: item.barcode + ":" + replaceAll(item.item_code,':','') + ":" + replaceAll(item.description,':','') + ":" + item.price,
									slug: item.description,
									is_bundle: item.is_bundle,
									unit_name: item.unit_name,
									id: item.id
								}
							})
						};
					}

				}
			}).on("select2-close", function(e) {
				// fired to the original element when the dropdown closes

				setTimeout(function() {
					$('#imagecon').fadeOut();
				}, 300);
			}).on("select2-highlight", function(e) {
				console.log("highlighted val=" + e.val + " choice=" + e.choice.text);
				var itemid = e.choice.id;
				var itemjpg = itemid + ".jpg";
				var opt = $(this);
				$.ajax({
					url: '../item_images/' + itemjpg, type: 'HEAD', error: function() {
						$('#imagecon').fadeOut();
					}, success: function() {
						$('#imagecon  img').attr('src', '../item_images/' + itemjpg);
						$('#imagecon').fadeIn();
					}
				});
			});


		});


	</script>
</head>
<body>
<noscript style='color:#ff0000;'>Javascript ERROR: Please turn on Javascript on your browser</noscript>

<div id="allcontent" style='display:none;'>
	<div class="navbar-custom">
		<nav class="navbar navbar-custom" role="navigation">

			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" id='btnHeaderNavbarMain' data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span>
					<span class="icon-bar"></span> <span class="icon-bar"></span>
				</button> <?php if($user->hasPermission('mainpos')){ ?>
				<a class="navbar-brand navPage" id='mainposlink' data-loc="index.php">
					<?php } else {
					?> <a class="navbar-brand navPage" id='mainposlink' data-loc="index.php">
						<?php
							} ?> <span class='glyphicon glyphicon-shopping-cart'></span>
						<span class='hidden-xs'><?php echo $thiscompany->name; ?></span></a>

			</div>

			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav navbar-left">

					<?php if($user->hasPermission('notification')) { ?>
						<li  id='nav_notification'>
							<a class='navPage'  data-loc="notification.php"><span class='glyphicon glyphicon-bell'></span>
								<span class='badge'><?php echo $sb_alert_count->cnt; ?></span></a>
						</li>					<?php } ?>

				</ul>
				<ul class="nav navbar-nav navbar-right">
          <li ><a href="../accounting/public/#/dashboard">Accounting</a></li>
					<?php if($user->hasPermission('call_log')){
						?>
					<li ><a href="call_log.php">Phone Call Log</a></li>
						<?php
					} ?>
					<?php if($user->data()->company_id != 1) { ?>
						<li id='monitoringnavs'><a href="../monitoring/">Forms</a>
						</li>					<?php } ?>

					<?php if (Configuration::isSalon()){
						?>
					<li><a href="../pos.php">POS</a></li>
					<?php } ?>
					<?php if (Configuration::getValue('simple_timelog') == 1){
						?>
						<li><a href="../tl/index.php">Timelog</a></li>
					<?php } ?>
					<?php if (Configuration::isGym()){
						?>
						<li><a href="../pos.php">POS</a></li>
						<li><a href="../booking.php">Book</a></li>
						<li><a href="service-login.php">Client Login</a></li>
					<?php
					}?>

					<?php if($user->hasPermission('mainpos') && !Configuration::isGym()) { ?>
						<li id='mainposnav'><a href="pos.php">Home</a></li>					<?php } ?>
					<?php if(false) { ?>
						<li id=''><a href="for-releasing.php">Releasing</a>
						</li>					<?php } ?>	<?php if($user->hasPermission('mainpos_sr')) { ?>
						<li id='saleshistorynav'><a href="sales.php">Sales History</a>
						</li>					<?php } ?>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class='glyphicon glyphicon-user'></span> Welcome <?php echo escape(ucwords($user->data()->lastname . ", " . $user->data()->firstname)); ?>
							<span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu">
							<li><a href="#" data-loc='user_account.php' class='navPage'>User Account</a></li>
							<li><a href="#" id='logout'>Log Out</a></li>
						</ul>
					</li>
				</ul>
			</div>
			<!-- /.navbar-collapse -->
		</nav>
	</div>
	<!--<div class="loading loading-n">
		<div class="outer-n"></div>
		<div class="inner-n"></div>
	</div>-->
	 <div class="loading">
			 <div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
	 </div>

	<div style='position:absolute;padding-top:50px;'>
		<?php include_once '../includes/admin/sidebar.php';?>
	</div>
	<div id="wrapper" >





