<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>File Manager</title>
	<link rel="stylesheet" href="../css/bootstrap.css">
	<link rel="stylesheet" href="../css/dropzone2.css">
	<style>
		.navbar-default {
			background-color: #20bf6b;
			border-color: #26de81;
		}
		.navbar-default .navbar-brand {
			color: #ffffff;
		}
		.navbar-default .navbar-brand:hover,
		.navbar-default .navbar-brand:focus {
			color: #ffffff;
		}
		.navbar-default .navbar-text {
			color: #ffffff;
		}
		.navbar-default .navbar-nav > li > a {
			color: #ffffff;
		}
		.navbar-default .navbar-nav > li > a:hover,
		.navbar-default .navbar-nav > li > a:focus {
			color: #ffffff;
		}
		.navbar-default .navbar-nav > li > .dropdown-menu {
			background-color: #20bf6b;
		}
		.navbar-default .navbar-nav > li > .dropdown-menu > li > a {
			color: #ffffff;
		}
		.navbar-default .navbar-nav > li > .dropdown-menu > li > a:hover,
		.navbar-default .navbar-nav > li > .dropdown-menu > li > a:focus {
			color: #ffffff;
			background-color: #26de81;
		}
		.navbar-default .navbar-nav > li > .dropdown-menu > li.divider {
			background-color: #26de81;
		}
		.navbar-default .navbar-nav .open .dropdown-menu > .active > a,
		.navbar-default .navbar-nav .open .dropdown-menu > .active > a:hover,
		.navbar-default .navbar-nav .open .dropdown-menu > .active > a:focus {
			color: #ffffff;
			background-color: #26de81;
		}
		.navbar-default .navbar-nav > .active > a,
		.navbar-default .navbar-nav > .active > a:hover,
		.navbar-default .navbar-nav > .active > a:focus {
			color: #ffffff;
			background-color: #26de81;
		}
		.navbar-default .navbar-nav > .open > a,
		.navbar-default .navbar-nav > .open > a:hover,
		.navbar-default .navbar-nav > .open > a:focus {
			color: #ffffff;
			background-color: #26de81;
		}
		.navbar-default .navbar-toggle {
			border-color: #26de81;
		}
		.navbar-default .navbar-toggle:hover,
		.navbar-default .navbar-toggle:focus {
			background-color: #26de81;
		}
		.navbar-default .navbar-toggle .icon-bar {
			background-color: #ffffff;
		}
		.navbar-default .navbar-collapse,
		.navbar-default .navbar-form {
			border-color: #ffffff;
		}
		.navbar-default .navbar-link {
			color: #ffffff;
		}
		.navbar-default .navbar-link:hover {
			color: #ffffff;
		}

		@media (max-width: 767px) {
			.navbar-default .navbar-nav .open .dropdown-menu > li > a {
				color: #ffffff;
			}
			.navbar-default .navbar-nav .open .dropdown-menu > li > a:hover,
			.navbar-default .navbar-nav .open .dropdown-menu > li > a:focus {
				color: #ffffff;
			}
			.navbar-default .navbar-nav .open .dropdown-menu > .active > a,
			.navbar-default .navbar-nav .open .dropdown-menu > .active > a:hover,
			.navbar-default .navbar-nav .open .dropdown-menu > .active > a:focus {
				color: #ffffff;
				background-color: #26de81;
			}
		}

		/*!
 * Start Bootstrap - Simple Sidebar (https://startbootstrap.com/template-overviews/simple-sidebar)
 * Copyright 2013-2017 Start Bootstrap
 * Licensed under MIT (https://github.com/BlackrockDigital/startbootstrap-simple-sidebar/blob/master/LICENSE)
 */


		#wrapper {
			margin-left: 0;
			-webkit-transition: all 0.5s ease;
			-moz-transition: all 0.5s ease;
			-o-transition: all 0.5s ease;
			transition: all 0.5s ease;
		}

		#wrapper.toggled {
			margin-left: 250px;
		}

		#sidebar-wrapper {
			z-index: 1000;
			position: fixed;
			left: 0px;
			width: 0;
			height: 100%;
			margin-left: 0px;
			overflow-y: auto;
			background: #2980b9;
			-webkit-transition: all 0.5s ease;
			-moz-transition: all 0.5s ease;
			-o-transition: all 0.5s ease;
			transition: all 0.5s ease;
		}

		#wrapper.toggled #sidebar-wrapper {
			width: 250px;
		}

		#page-content-wrapper {
			width: 100%;
			position: absolute;
			padding: 15px;
		}

		#wrapper.toggled #page-content-wrapper {
			position: absolute;
			margin-right: 0px;
		}


		/* Sidebar Styles */

		.sidebar-nav {
			position: absolute;
			top: 0;
			width: 250px;
			margin: 0;
			padding: 0;
			list-style: none;
		}

		.sidebar-nav li {
			text-indent: 20px;
			line-height: 40px;
		}

		.sidebar-nav li a {
			display: block;
			text-decoration: none;

			color:#fff;
		}

		.sidebar-nav li a:hover {
			text-decoration: none;
			color: #fff;
			background: rgba(255, 255, 255, 0.2);
		}

		.sidebar-nav li a:active, .sidebar-nav li a:focus {
			text-decoration: none;
		}

		.sidebar-nav>.sidebar-brand {
			height: 65px;
			font-size: 18px;
			line-height: 60px;
		}

		.sidebar-nav>.sidebar-brand a {

			color:#fff;
		}

		.sidebar-nav>.sidebar-brand a:hover {
			color: #fff;
			background: none;

		}

		@media(min-width:768px) {
			#wrapper {
				margin-left: 0px;
			}
			#wrapper.toggled {
				margin-left: 250px;
			}
			#sidebar-wrapper {
				width: 0;
			}
			#wrapper.toggled #sidebar-wrapper {
				width: 250px;
			}
			#page-content-wrapper {
				padding: 20px;
				position: relative;
			}
			#wrapper.toggled #page-content-wrapper {
				position: relative;
				margin-right: 0;
			}
		}

		/* steps */
		/* --------------------------------
Primary style
-------------------------------- */

		section {
			/* used just to separate different styles */
			border-bottom: 1px solid #e6e6e6;
			padding: 4em 0;
		}
		section h2 {
			width: 90%;
			margin: 0 auto 2em;
			color: #2c3f4c;
			font-size: 2rem;
			font-weight: 700;
			text-align: center;
		}
		@media only screen and (min-width: 1170px) {
			section {
				padding: 6em 0;
			}
			section h2 {
				margin: 0 auto 3em;
			}
		}

		/* --------------------------------
		Basic Style
		-------------------------------- */
		.cd-breadcrumb, .cd-multi-steps {
			width: 90%;
			max-width: 768px;
			padding: 0.5em 1em;
			margin: 1em auto;
			background-color: #edeff0;
			border-radius: .25em;
		}
		.cd-breadcrumb:after, .cd-multi-steps:after {
			content: "";
			display: table;
			clear: both;
		}
		.cd-breadcrumb li, .cd-multi-steps li {
			display: inline-block;
			float: left;
			margin: 0.5em 0;
		}
		.cd-breadcrumb li::after, .cd-multi-steps li::after {
			/* this is the separator between items */
			display: inline-block;
			content: '\00bb';
			margin: 0 .6em;
			color: #959fa5;
		}
		.cd-breadcrumb li:last-of-type::after, .cd-multi-steps li:last-of-type::after {
			/* hide separator after the last item */
			display: none;
		}
		.cd-breadcrumb li > *, .cd-multi-steps li > * {
			/* single step */
			display: inline-block;
			font-size: 1.4rem;
			color: #2c3f4c;
		}
		.cd-breadcrumb li.current > *, .cd-multi-steps li.current > * {
			/* selected step */
			color: #96c03d;
		}
		.no-touch .cd-breadcrumb a:hover, .no-touch .cd-multi-steps a:hover {
			/* steps already visited */
			color: #96c03d;
		}

		.cd-breadcrumb.custom-icons li:not(.current):nth-of-type(2) > *::before, .cd-multi-steps.custom-icons li:not(.current):nth-of-type(2) > *::before {
			/* change custom icon using image sprites */
			background-position: -20px 0;
		}
		.cd-breadcrumb.custom-icons li:not(.current):nth-of-type(3) > *::before, .cd-multi-steps.custom-icons li:not(.current):nth-of-type(3) > *::before {
			background-position: -40px 0;
		}
		.cd-breadcrumb.custom-icons li:not(.current):nth-of-type(4) > *::before, .cd-multi-steps.custom-icons li:not(.current):nth-of-type(4) > *::before {
			background-position: -60px 0;
		}
		.cd-breadcrumb.custom-icons li.current:first-of-type > *::before, .cd-multi-steps.custom-icons li.current:first-of-type > *::before {
			/* change custom icon for the current item */
			background-position: 0 -20px;
		}
		.cd-breadcrumb.custom-icons li.current:nth-of-type(2) > *::before, .cd-multi-steps.custom-icons li.current:nth-of-type(2) > *::before {
			background-position: -20px -20px;
		}
		.cd-breadcrumb.custom-icons li.current:nth-of-type(3) > *::before, .cd-multi-steps.custom-icons li.current:nth-of-type(3) > *::before {
			background-position: -40px -20px;
		}
		.cd-breadcrumb.custom-icons li.current:nth-of-type(4) > *::before, .cd-multi-steps.custom-icons li.current:nth-of-type(4) > *::before {
			background-position: -60px -20px;
		}
		@media only screen and (min-width: 768px) {
			.cd-breadcrumb, .cd-multi-steps {
				padding: 0 1.2em;
			}
			.cd-breadcrumb li, .cd-multi-steps li {
				margin: 1.2em 0;
			}
			.cd-breadcrumb li::after, .cd-multi-steps li::after {
				margin: 0 1em;
			}
			.cd-breadcrumb li > *, .cd-multi-steps li > * {
				font-size: 1.6rem;
			}
		}


		/* --------------------------------
		Custom icons hover effects - breadcrumb and multi-steps
		-------------------------------- */
		@media only screen and (min-width: 768px) {
			.no-touch .cd-breadcrumb.triangle.custom-icons li:first-of-type a:hover::before, .cd-breadcrumb.triangle.custom-icons li.current:first-of-type em::before, .no-touch .cd-multi-steps.text-center.custom-icons li:first-of-type a:hover::before, .cd-multi-steps.text-center.custom-icons li.current:first-of-type em::before {
				/* change custom icon using image sprites - hover effect or current item */
				background-position: 0 -40px;
			}
			.no-touch .cd-breadcrumb.triangle.custom-icons li:nth-of-type(2) a:hover::before, .cd-breadcrumb.triangle.custom-icons li.current:nth-of-type(2) em::before, .no-touch .cd-multi-steps.text-center.custom-icons li:nth-of-type(2) a:hover::before, .cd-multi-steps.text-center.custom-icons li.current:nth-of-type(2) em::before {
				background-position: -20px -40px;
			}
			.no-touch .cd-breadcrumb.triangle.custom-icons li:nth-of-type(3) a:hover::before, .cd-breadcrumb.triangle.custom-icons li.current:nth-of-type(3) em::before, .no-touch .cd-multi-steps.text-center.custom-icons li:nth-of-type(3) a:hover::before, .cd-multi-steps.text-center.custom-icons li.current:nth-of-type(3) em::before {
				background-position: -40px -40px;
			}
			.no-touch .cd-breadcrumb.triangle.custom-icons li:nth-of-type(4) a:hover::before, .cd-breadcrumb.triangle.custom-icons li.current:nth-of-type(4) em::before, .no-touch .cd-multi-steps.text-center.custom-icons li:nth-of-type(4) a:hover::before, .cd-multi-steps.text-center.custom-icons li.current:nth-of-type(4) em::before {
				background-position: -60px -40px;
			}
		}
		/* --------------------------------
		Multi steps indicator
		-------------------------------- */
		@media only screen and (min-width: 768px) {
			.cd-multi-steps {
				/* reset style */
				background-color: transparent;
				padding: 0;
				text-align: center;
			}

			.cd-multi-steps li {
				position: relative;
				float: none;
				margin: 0.4em 40px 0.4em 0;
			}
			.cd-multi-steps li:last-of-type {
				margin-right: 0;
			}
			.cd-multi-steps li::after {
				/* this is the line connecting 2 adjacent items */
				position: absolute;
				content: '';
				height: 4px;
				background: #edeff0;
				/* reset style */
				margin: 0;
			}
			.cd-multi-steps li.visited::after {
				background-color: #96c03d;
			}
			.cd-multi-steps li > *, .cd-multi-steps li.current > * {
				position: relative;
				color: #2c3f4c;
			}

			.cd-multi-steps.custom-separator li::after {
				/* reset style */
				height: 4px;
				background: #edeff0;
			}

			.cd-multi-steps.text-center li::after {
				width: 100%;
				top: 50%;
				left: 100%;
				-webkit-transform: translateY(-50%) translateX(-1px);
				-moz-transform: translateY(-50%) translateX(-1px);
				-ms-transform: translateY(-50%) translateX(-1px);
				-o-transform: translateY(-50%) translateX(-1px);
				transform: translateY(-50%) translateX(-1px);
			}
			.cd-multi-steps.text-center li > * {
				z-index: 1;
				padding: .6em 1em;
				border-radius: .25em;
				background-color: #edeff0;
			}
			.no-touch .cd-multi-steps.text-center a:hover {
				background-color: #2c3f4c;
			}
			.cd-multi-steps.text-center li.current > *, .cd-multi-steps.text-center li.visited > * {
				color: #ffffff;
				background-color: #96c03d;
			}
			.cd-multi-steps.text-center.custom-icons li.visited a::before {
				/* change the custom icon for the visited item - check icon */
				background-position: 0 -60px;
			}

			.cd-multi-steps.text-top li, .cd-multi-steps.text-bottom li {
				width: 80px;
				text-align: center;
			}
			.cd-multi-steps.text-top li::after, .cd-multi-steps.text-bottom li::after {
				/* this is the line connecting 2 adjacent items */
				position: absolute;
				left: 50%;
				/* 40px is the <li> right margin value */
				width: calc(100% + 40px);
			}
			.cd-multi-steps.text-top li > *::before, .cd-multi-steps.text-bottom li > *::before {
				/* this is the spot indicator */
				content: '';
				position: absolute;
				z-index: 1;
				left: 50%;
				right: auto;
				-webkit-transform: translateX(-50%);
				-moz-transform: translateX(-50%);
				-ms-transform: translateX(-50%);
				-o-transform: translateX(-50%);
				transform: translateX(-50%);
				height: 12px;
				width: 12px;
				border-radius: 50%;
				background-color: #edeff0;
			}
			.cd-multi-steps.text-top li.visited > *::before,
			.cd-multi-steps.text-top li.current > *::before, .cd-multi-steps.text-bottom li.visited > *::before,
			.cd-multi-steps.text-bottom li.current > *::before {
				background-color: #96c03d;
			}

			.no-touch .cd-multi-steps.text-top a:hover, .no-touch .cd-multi-steps.text-bottom a:hover {
				color: #96c03d;
			}

			.no-touch .cd-multi-steps.text-top a:hover::before, .no-touch .cd-multi-steps.text-bottom a:hover::before {
				box-shadow: 0 0 0 3px rgba(150, 192, 61, 0.3);
			}

			.cd-multi-steps.text-top li::after {
				/* this is the line connecting 2 adjacent items */
				bottom: 4px;
			}

			.cd-multi-steps.text-top li > * {
				padding-bottom: 20px;
			}

			.cd-multi-steps.text-top li > *::before {
				/* this is the spot indicator */
				bottom: 0;
			}

			.cd-multi-steps.text-bottom li::after {
				/* this is the line connecting 2 adjacent items */
				top: 3px;
			}
			.cd-multi-steps.text-bottom li > * {
				padding-top: 20px;
			}
			.cd-multi-steps.text-bottom li > *::before {
				/* this is the spot indicator */
				top: 0;
			}
		}

		/* --------------------------------
		Add a counter to the multi-steps indicator
		-------------------------------- */
		.cd-multi-steps.count li {
			counter-increment: steps;
		}

		.cd-multi-steps.count li > *::before {
			content: counter(steps) " - ";
		}

		@media only screen and (min-width: 768px) {
			.cd-multi-steps.text-top.count li > *::before,
			.cd-multi-steps.text-bottom.count li > *::before {
				/* this is the spot indicator */
				content: counter(steps);
				height: 26px;
				width: 26px;
				line-height: 26px;
				font-size: 1.4rem;
				color: #ffffff;
			}

			.cd-multi-steps.text-top.count li:not(.current) em::before,
			.cd-multi-steps.text-bottom.count li:not(.current) em::before {
				/* steps not visited yet - counter color */
				color: #2c3f4c;
			}

			.cd-multi-steps.text-top.count li::after {
				bottom: 11px;
			}

			.cd-multi-steps.text-top.count li > * {
				padding-bottom: 34px;
			}

			.cd-multi-steps.text-bottom.count li::after {
				top: 11px;
			}

			.cd-multi-steps.text-bottom.count li > * {
				padding-top: 34px;
			}
		}
		/* end steps*/

	</style>
</head>
<body>
<div id='app'>
	<div id="wrapper" class='toggled'>

		<!-- Sidebar -->
		<div id="sidebar-wrapper">
			<ul class="sidebar-nav">
				<li class="sidebar-brand">
					<a href="#">
						 <span class='glyphicon glyphicon-wrench'></span> SERVICE
					</a>
				</li>
				<li> </li>
				<li>
					<a href="#">Service Request</a>
				</li>
				<li>
					<a href="#">For Servicing</a>
				</li>
				<li>
					<a href="#">For My Approval</a>
				</li>
				<li>
					<a href="#">For Releasing</a>
				</li>
			</ul>
		</div>
		<!-- /#sidebar-wrapper -->

		<!-- Page Content -->
		<div id="page-content-wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-2"></div>
					<div class="col-md-8">
						<ul class="nav nav-tabs nav-justified">
							<li role="presentation" class="active"><a href="#">Service Inside</a></li>
							<li role="presentation"><a href="#">Service Outside</a></li>
						</ul>
						<div v-show="request.container.service_in">
							<nav>
								<ol class="cd-multi-steps text-center">
									<li v-bind:class="{current: request.service_in_form.nav.basic, visited: request.service_in_form.visited.basic }"  @click="goForm(1)"><a href="#">Basic Info</a></li>
									<li  v-bind:class="{current: request.service_in_form.nav.service, visited: request.service_in_form.visited.service  }" @click="goForm(2)" ><a href="#">Service Info</a></li>
									<li  v-bind:class="{current: request.service_in_form.nav.item_list, visited: request.service_in_form.visited.item_list  }" @click="goForm(3)"><a href="#">Item List</a></li>
								</ol>
							</nav>
							<div v-show="request.service_in_form.container.basic">
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<input type="text" class='form-control' placeholder='Client Name'>
										</div>
									</div>
									<div class="col-md-12">
										<div class="form-group">
											<input type="text" class='form-control' placeholder='Branch Name'>
										</div>
									</div>
									<div class="col-md-12">
										<div class="form-group">
											<input type="text" class='form-control' placeholder='Invoice'>
										</div>
									</div>
									<div class="col-md-12">
										<div class="form-group">
											<input type="text" class='form-control' placeholder='DR'>
										</div>
									</div>
									<div class="col-md-12">
										<div class="form-group text-center">
											<button class='btn btn-primary' @click="goForm(2)">Next</button>
										</div>
									</div>
								</div>
							</div> <!-- end container basic -->

							<div v-show="request.service_in_form.container.service">
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<input type="text" class='form-control' placeholder='Technician'>
										</div>
									</div>
									<div class="col-md-12">
										<div class="form-group">
											<input type="text" class='form-control' placeholder='Service Type'>
										</div>
									</div>
									<div class="col-md-12">
										<div class="form-group">
											<input type="text" class='form-control' placeholder='Service Remarks'>
										</div>
									</div>

									<div class="col-md-12">
										<div class="form-group text-center">
											<button class='btn btn-primary' @click="goForm(3)">Next</button>
										</div>
									</div>
								</div>
							</div><!-- end container service -->

							<div v-show="request.service_in_form.container.item_list">

								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<input type="text" class='form-control' placeholder='Item'>
										</div>
									</div>
									<div class="col-md-12">
										<div class="form-group">
											<input type="text" class='form-control' placeholder='Qty'>
										</div>
									</div>
									<div class="col-md-12">
										<div class="form-group">
											<input type="text" class='form-control' placeholder='Remarks'>
										</div>
									</div>

									<div class="col-md-12">
										<div class="form-group text-center">
											<button class='btn btn-primary' @click="goForm(3)">Next</button>
										</div>
									</div>
								</div>
							</div><!-- end container item list -->

						</div> <!-- END SERVICE IN -->

					</div>
					<div class="col-md-2"></div>
				</div>
			</div>
		</div>
		<!-- /#page-content-wrapper -->

	</div>
</div>
<script src='../js/jquery.js'></script>
<script src='../js/bootstrap.min.js'></script>
<script src='../js/dropzone2.js'></script>
<script src='../js/vue3.js'></script>
<script>
	Dropzone.autoDiscover = false;
	var vm = new Vue({
		el: '#app',
		data:{
			request:{
				container: {service_in: true,service_out:false},
				service_in_form: {
					nav: {basic:true, service:false,item_list:false},
					visited: {basic:false, service:false,item_list:false},
					container: {basic: true, service:false,item_list:false}
				}
			}
		},
		computed: {

		},
		mounted: function(){
			var self = this;
			$("#menu-toggle").click(function(e) {
				e.preventDefault();
				$("#wrapper").toggleClass("toggled");
			});
		},
		methods:{
			goForm: function(c){
				var self = this;
				if(c == 1){
					self.request.service_in_form.container = {basic: true,service:false,item_list:false};
					self.request.service_in_form.nav  = {basic:true,service:false,item_list:false};
					self.request.service_in_form.visited  = {basic:false,service:false,item_list:false};
				} else if (c == 2){
					self.request.service_in_form.container = {basic: false,service:true,item_list:false};
					self.request.service_in_form.nav  = {basic:true,service:true,item_list:false};
					self.request.service_in_form.visited  = {basic:true,service:false,item_list:false};
				} else if (c == 3){
					self.request.service_in_form.container = {basic: false,service:false,item_list:true};
					self.request.service_in_form.nav  = {basic:true,service:true,item_list:true};
					self.request.service_in_form.visited  = {basic:true,service:true,item_list:false};
				}
			}
		}
	});


</script>
</body>
</html>