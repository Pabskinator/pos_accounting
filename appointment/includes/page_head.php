<?php
	include 'util.php';
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title></title>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" type="text/css" rel="stylesheet">
	<link rel="stylesheet" href="../css/select2.css">
	<link rel="stylesheet" href="../css/select2_bootstrap.css">
	<link rel="stylesheet" href="../css/bootstrap-timepicker.min.css">
	<script src='../js/jquery.js'></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	<script src='../js/select2.js'></script>
	<script src='../js/bootstrap-timepicker.min.js'></script>
	<style>




		.btn-circle {
			width: 60px;
			height: 60px;

			border-radius: 50%;
			box-shadow: 0 2px 2px 0 #666;
			transition: all 0.1s ease-in-out;
			font-size: 30px;
			color: white;
			text-align: center;
			line-height: 60px;
			position: fixed;
			right: 30px;
			bottom: 30px;
		}

		.btn-circle:hover {
			box-shadow: 0 6px 14px 0 #666;
			transform: scale(1.05);
		}
		.btn-remove{
			margin-bottom: 10px;
			cursor: pointer;
		}

		ul.timeline {
			list-style-type: none;
			position: relative;
		}
		ul.timeline:before {
			content: ' ';
			background: #d4d9df;
			display: inline-block;
			position: absolute;
			left: 29px;
			width: 2px;
			height: 100%;
			z-index: 400;
		}
		ul.timeline > li {
			margin: 20px 0;
			padding-left: 20px;
		}
		ul.timeline > li:before {
			content: ' ';
			background: white;
			display: inline-block;
			position: absolute;
			border-radius: 50%;
			border: 3px solid #22c0e8;
			left: 20px;
			width: 20px;
			height: 20px;
			z-index: 400;
		}

		.style-2::-webkit-scrollbar-track
		{
			-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
			background-color: #F5F5F5;
		}

		.style-2::-webkit-scrollbar
		{
			width: 6px;
			background-color: #F5F5F5;
		}

		.style-2::-webkit-scrollbar-thumb
		{
			background-color: #222;
		}

	</style>
</head>
<body>

<nav class="navbar navbar-expand-lg  navbar-dark bg-dark">
	<a class="navbar-brand" href="#">Appointment System</a>
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>

	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav ml-auto">


			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Hi, Temp
				</a>
				<div class="dropdown-menu" aria-labelledby="navbarDropdown">

					<a class="dropdown-item" href="#">User Account</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="#">Log out</a>
				</div>
			</li>


		</ul>

	</div>
</nav>