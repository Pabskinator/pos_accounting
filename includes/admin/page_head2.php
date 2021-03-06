<?php

	require_once '../core/admininit.php';

	ob_start();

	$user = new User();
	if(!$user->isLoggedIn()) {
		Redirect::to('../login.php');
	}

	$thiscompany = $user->getCompany($user->data()->company_id);
	define("POINTS",$thiscompany->points);
	define("POINT_AMOUNT",$thiscompany->amount);

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
<html lang="en">
<head>
	<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">

	<title>TITLE2</title>
	<!--
	<link href="../css/bootstrap.css" rel="stylesheet">
	<link href="../css/select2.css" rel="stylesheet">
	<link href="../css/select2_bootstrap.css" rel="stylesheet">
	<link href="../css/bs_datepicker.css" rel="stylesheet">
	<link href="../css/font-awesome/css/font-awesome.css" rel="stylesheet">
	<link rel="stylesheet" href="../css/dataTablesCss.css" />
	<link href="../css/alertify.core.css" rel="stylesheet">
	<link href="../css/alertify.bootstrap.css" rel="stylesheet">
	<link href="../css/toastr.min.css" rel="stylesheet">
	<link rel="stylesheet" href="../css/morris.css" />
	<link rel="stylesheet" href="../css/bootflat.css" />
	-->
	<link href="../css/all.css" rel="stylesheet">
	<link href="../css/font-awesome/css/font-awesome.css" rel="stylesheet">
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
		.alertify-text{
			z-index: 99999999;
		}
		#wrapper {

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
		.table_border_top > tbody > tr > th, .table_border_top > tbody > tr > td {
			border-top: 1px solid #ddd;
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
		.withTopBorder {
			border-top: 1px solid #ccc;
		}
	</style>
	<script>
	//	"use strict";

		localStorage['lastPage'] = window.location.pathname;
		if (!window.frameElement) {
			location.href='main.php';
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

			var settings = $.extend({

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
		//	"use strict";
			function formatItem(o) {
				if(!o.id)
					return o.text;
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
									cdays: item.cdays,
									cqty: item.cqty,
									unit_name: item.unit_name,
									id: item.id
								}
							})
						};
					}

				}
			}).on("select2-close", function(e) {


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
			$('body').on('click','#btnCloseRightPanel',function(){
				$('.right-panel-pane').fadeOut(100);
			});
		});

		var button_action = (function(){
			var loading_label = "<i class='fa fa-spinner fa-spin'></i> Loading...";
			var old_val = "";
			var pub = {};
			function get_loading_label(){
				return loading_label;
			};
			pub.start_loading = function(btn){
				btn.attr('disabled',true);
				if(btn.is(":button")){
					old_val  = btn.html();
					btn.html(get_loading_label);
				} else if(btn.is(":input")){
					old_val  = btn.val();
					btn.html(get_loading_label);
				}
			};
			pub.end_loading = function(btn){
				btn.attr('disabled',false);
				if(btn.is(":button")){
					btn.html(old_val);
				} else if(btn.is(":input")){
					btn.val(old_val);
				}
			};
			return pub;
		})();




	</script>
</head>
<body>
<div class="right-panel-pane" style='display:none;'>
	<div id="right-pane-container">
	</div>
	<div class='btn-close'><button  id="btnCloseRightPanel" class='btn btn-default btn-sm'>Close</button></div>
</div>
<?php
//echo "<div class='container'> <h1 class='text-center'><i class='fa fa-wrench fa-4x'></i></h1><h3> We are currently upgrading the system. Please come back later. Thank you.</h3></div>";
//exit();
?>
<input type="hidden" id='iframeChecker' value='jayson'>
<div id="allcontent" >
	<div id="wrapper" >




