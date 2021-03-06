<!DOCTYPE html>
<html  manifest='manifest/pos.appcache'>
<head>
	<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Apollo System</title>
	<link rel='shortcut icon' href='css/img/logo.png' type='image/x-icon'/>
	<link href="css/bootstrap.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">
	<link href="css/toastr.min.css" rel="stylesheet">
	<link href="css/select2.css" rel="stylesheet">
	<link href="css/select2_bootstrap.css" rel="stylesheet">
	<link href="css/bs_datepicker.css" rel="stylesheet">
	<script src="js/jquery.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/main_pos.js"></script>
	<script src="js/toastr.min.js"></script>
	<script src="js/select2.js"></script>
	<script src="js/bootstrap-datepicker.js"></script>


	<script>
		if(localStorage["current_id"] == null){
			location.href="login.php";
		}

		permissions = JSON.parse(localStorage["current_permissions"]);

	</script>
	<style>

		<!--
		.shout_box {
			background: #627BAE;
			width: 260px;
			overflow: hidden;
			position: fixed;
			bottom: 0;
			right: 20%;
			z-index:9;
		}
		.shout_box .header .close_btn {
			background: url(images/close_btn.png) no-repeat 0px 0px;
			float: right;
			width: 15px;
			height: 15px;
		}
		.shout_box .header .close_btn:hover {
			background: url(images/close_btn.png) no-repeat 0px -16px;
		}

		.shout_box .header .open_btn {
			background: url(images/close_btn.png) no-repeat 0px -32px;
			float: right;
			width: 15px;
			height: 15px;
		}
		.shout_box .header .open_btn:hover {
			background: url(images/close_btn.png) no-repeat 0px -48px;
		}
		.shout_box .header{
			padding: 5px 3px 5px 5px;
			font: 11px 'lucida grande', tahoma, verdana, arial, sans-serif;
			font-weight: bold;
			color:#fff;
			border: 1px solid rgba(0, 39, 121, .76);
			border-bottom:none;
			cursor: pointer;
		}
		.shout_box .header:hover{
			background-color: #627BAE;
		}
		.shout_box .message_box {
			background: #FFFFFF;
			height: 200px;
			overflow:auto;
			border: 1px solid #CCC;
		}
		.shout_msg{
			margin-bottom: 10px;
			display: block;
			border-bottom: 1px solid #F3F3F3;
			padding: 0px 5px 5px 5px;
			font: 11px 'lucida grande', tahoma, verdana, arial, sans-serif;
			color:#7C7C7C;
		}
		.message_box:last-child {
			border-bottom:none;
		}
		time{
			font: 11px 'lucida grande', tahoma, verdana, arial, sans-serif;
			font-weight: normal;
			float:right;
			color: #D5D5D5;
		}
		.shout_msg .username{
			margin-bottom: 10px;
			margin-top: 10px;
		}
		.user_info input {
			width: 98%;
			height: 25px;
			border: 1px solid #CCC;
			border-top: none;
			padding: 3px 0px 0px 3px;
			font: 11px 'lucida grande', tahoma, verdana, arial, sans-serif;
		}
		.shout_msg .username{
			font-weight: bold;
			display: block;
		}
		-->

	</style>
</head>
<body>