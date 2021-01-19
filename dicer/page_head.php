<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<link rel="stylesheet" href="../css/bootstrap.css">
	<link href="../css/bs_datepicker.css" rel="stylesheet">
	<link href="../css/select2.css" rel="stylesheet">
	<link href="../css/select2_bootstrap.css" rel="stylesheet">
	<link href="../css/toastr.min.css" rel="stylesheet">
	<link rel="stylesheet" href="dicer.css">
	<script>
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
	</script>
</head>
<body>
<div id="wrapper">

	<!-- Sidebar -->
	<div id="sidebar-wrapper">

		<h3 id='main-lbl'></h3>
		<ul class="sidebar-nav">
			<li>
				<a href='#' @click.prevent="goTo('home')"><span class='glyphicon glyphicon-home'></span> Home</a>
			</li>
			<li>
				<a href='#' @click.prevent="goTo('history')"><span class='glyphicon glyphicon-list'></span> History</a>
			</li>
		</ul>
	</div>
	<!-- /#sidebar-wrapper -->

	<!-- Page Content -->
	<div id="page-content-wrapper">
		<div class="container-fluid">


