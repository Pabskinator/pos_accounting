<?php
	require_once '../core/admininit.php';
	ob_start();
	$user = new User();
	if(!$user->isLoggedIn()){
		Redirect::to('/pos/login.php');
	}
	$config_style_cls = new Style();
	$config_styles = $config_style_cls->getActivatedStyle($user->data()->company_id);
	$decoded_themes = json_decode($config_styles->styles);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>TITLE</title>
	<link href="../css/all.css" rel="stylesheet">
	<link href="../css/font-awesome/css/font-awesome.css" rel="stylesheet">
	<link href="../css/admin.css" rel="stylesheet">
	<!--
	<script src="../js/jquery.js"></script>
		<script src="../js/bootstrap.min.js"></script>
		<script src="../js/bootstrap-datepicker.js"></script>
		<script src="../js/jquery.dataTables.js"></script>
		<script src="../js/dataTablesJs.js"></script>
	<script type="text/javascript" src="../js/jquery.cookie.js"></script>
	<script src="../js/offline.js"></script>
	<script src="../js/select2.js"></script> -->
	<script src="../js/all.js"></script>
	<script src="../js/tinymce.min.js"></script>
	<script src="../js/jquery.tinymce.min.js"></script>
	<script src="../js/jspdf.min.js"></script>
	<script src="../js/html2canvas.js"></script>
	<style type='text/css'>
		.navbar-custom {
			background-color:#222;
			color:#ffffff;
			border-radius:0;
			position:fixed;
			width:100%;
			z-index:1001;


		}

		.navbar-custom .navbar-nav > li > a {
			color:#fff;
			padding-left:12px;
			padding-right:12px;
		}
		.navbar-custom .navbar-nav > .active > a, .navbar-nav > .active > a:hover, .navbar-nav > .active > a:focus {
			color: #ffffff;
			background-color:transparent;
		}

		.navbar-custom .navbar-nav > li > a:hover, .nav > li > a:focus {
			text-decoration: none;
			background-color: #333;
		}
		.navbar-custom .navbar-nav > li.dropdown > ul.dropdown-menu > li > a:hover {
			text-decoration: none;
			background-color: #aaa;
		}
		.navbar-custom .navbar-nav > li.dropdown > ul.dropdown-menu >li{
			min-width:250px;
		}
		.navbar-custom .navbar-nav li.dropdown.open > .dropdown-toggle, .navbar .nav li.dropdown.active > .dropdown-toggle, .navbar .nav li.dropdown.open.active > .dropdown-toggle {
			background-color: #333;
		}
		.navbar-custom .navbar-brand {
			color:#eeeeee;
		}
		.navbar-custom .navbar-toggle {
			background-color:#eeeeee;
		}
		.navbar-custom .icon-bar {
			background-color:#333;
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
	</style>
	<script>
		$(function(){
			$('body').on('click','#user_account_link',function(e){
				e.preventDefault();
				localStorage['lastPage'] = $(this).attr('data-href');
				location.href ='../admin/main.php';
			});
		});
	</script>
</head>
<body>
<div class="navbar-custom" >
	<nav class="navbar navbar-custom" role="navigation">
		<div class="container-fluid">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="../index.php"><span class='glyphicon glyphicon-shopping-cart'></span> Monitoring</a>
			</div>

			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

				<ul class="nav navbar-nav navbar-right">

					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class='glyphicon glyphicon-user'></span>  Welcome <?php echo escape(ucwords($user->data()->lastname . ", " . $user->data()->firstname)); ?> <span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu">
							<li><a id='user_account_link' href='#' data-href="user_account.php">User Account</a></li>

							<li><a href="#" id='logout'>Log Out</a></li>

						</ul>
					</li>
				</ul>
			</div><!-- /.navbar-collapse -->
		</div><!-- /.container-fluid -->
	</nav>
</div>
<div id="wrapper" style='padding-top:50px;'>

