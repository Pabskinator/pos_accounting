<?php
	session_start();
	date_default_timezone_set('Asia/Manila');
	$http_host = $_SERVER['HTTP_HOST'];
	if($http_host == 'sh.apollosystems.com.ph'){
		error_reporting(0);
		$mysql_username = "apollosy_sh";
		$mysql_password = "409186963@StephenWang";
		$mysql_database = "apollosy_sh";
	}  else if($http_host == 'safehouseacademy.com'){
		error_reporting(0);
		$mysql_username = "apollo29_safeh";
		$mysql_password = "409186963@StephenWang";
		$mysql_database = "apollo29_safehouse";
	} else if($http_host == 'dev.apollo.ph:81'){
		$mysql_username = "root";
		$mysql_password = "";
		$mysql_database = "vit_new";
	} else if($http_host == 'localhost'){
		$mysql_username = "root";
		$mysql_password = "";
		$mysql_database = "vit_new";
	} else {
		$mysql_username = "root";
		$mysql_password = "";
		$mysql_database = "vit_new";
	}

	$mysqli = new mysqli("localhost", $mysql_username, $mysql_password,$mysql_database);
	$mysqli->query("SET timezone = '+8:00'");

	$date_one = date('m/01/Y');
	$date_two = date('m/d/Y', strtotime($date_one . " 1 month -1 min"));
	$arr_calender = [];
	$q = "Select s.* ,os.name from class_schedules s left join offered_services os on os.id = s.class_id where 1 = 1 and s.is_active = 1";
	$result = $mysqli->query($q);
	$arr_calender = [];
	$arr_class_name= [];

	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		// select class here
		$arr_calender[$row['day_of_the_week']][] =['name' => $row['name'],'time_of_the_day' =>$row['time_of_the_day'], 'id' => $row['id'], 'is_pt' => $row['is_pt'], 'class_type' => $row['class_type']];
		if(!in_array($row['name'], $arr_class_name)){
			$arr_class_name[$row['class_id']] = $row['name'];
		}
	}

//	$my_request = "Select m.* , os.name, ch.name as coach_name from member_service_request m left join offered_services os on os.id = m.class_id  left join coaches ch on ch.id = m.coach_id where m.member_id = $_SESSION[member_id]";
//	$result_request = $mysqli->query($my_request);
//	$num_rows_req = $result_request->num_rows;
//	$arr_req = [];
//
//	if($num_rows_req > 0){
//		while($row = $result_request->fetch_array(MYSQLI_ASSOC)){
//
//			$ex = explode('-',$row['time_of_the_day']);
//			$hr_from = date('h:i A',strtotime($ex[0]));
//			$hr_to = date('h:i A',strtotime($ex[1]));
//
//			$arr_status = ['','Pending','Processed'];
//			$arr_req[] = [
//				'title' => $row['name'],
//				'start' => date('Y-m-d',$row['schedule_date']),
//				'reserved' => $hr_from . "-" . $hr_to,
//
//				'coach_name' => $row['coach_name']
//			];
//		}
//	}

	function date_compare($a, $b)
	{

	}

	function getClassesSchedule($mysqli){

		$q  = "Select s.* ,os.name, ch.name as coach_name from class_schedules s left join offered_services os on os.id = s.class_id left join coaches ch on ch.id = s.coach_id where 1 = 1 and s.is_active = 1";
		$result = $mysqli->query($q);
		$arr_os = [];
		$arr_key = [];
		while($row = $result->fetch_array(MYSQLI_ASSOC)){
			// select class here
			$arr_os[$row['name']][$row['day_of_the_week']][] =['time_of_the_day' =>$row['time_of_the_day'] , 'id' => $row['id'], 'is_pt' => $row['is_pt'], 'class_type' => $row['class_type'], 'coach_name' => $row['coach_name']];
			$arr_key[$row['name']] = $row['day_of_the_week'];
		}
		foreach($arr_key as $key => $val){
			// select class here
			usort($arr_os[$key][$val],function($a,$b){
				$a_ex = explode("-",$a['time_of_the_day']);
				$b_ex = explode("-",$b['time_of_the_day']);

				$t1 = strtotime($a_ex[0]);
				$t2 = strtotime($b_ex[0]);
				return $t1 - $t2;
			});
		}




		$arr_days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];



		echo "<div id='no-more-tables'>";
		echo "<table class=''>";
		echo "<thead>";
		echo "<tr>";
		echo "<th>Class</th>";
		foreach($arr_days as $day){
			echo "<th>$day</th>";
		}
		echo "</tr>";
		echo "</thead>";
		echo "<tbody>";
		$classtype = ['','Turf Area','Matted Area'];
		$is_pt = ['','Class','PT'];
		foreach($arr_os as $class_name => $aos){
			echo "<tr>";
			echo "<td data-title='Class' style='border:1px solid #ccc;' class='red-text'>$class_name</td>";
			foreach($arr_days as $d){
				$cur = isset($aos[$d]) ? $aos[$d] : [];
				echo "<td data-title='".$d."' style='border:1px solid #ccc;' >";
				if(count($cur)){
					foreach($cur as $a){
						$ex = explode('-',$a['time_of_the_day']);
						$hr_from = date('h:i A',strtotime($ex[0]));
						$hr_to = date('h:i A',strtotime($ex[1]));
						echo "<small style='display:block;'>
										$hr_from - $hr_to
										<small style='display:block;' class='red-text'>".$classtype[$a['class_type']]."- ".$is_pt[$a['is_pt']]."</small>
										<small style='display:block;' class='red-text'>Coach: $a[coach_name]</small>
										</small> ";
					}
				}
				echo "</td>";
			}
			echo "</tr>";
		}
		echo "</tbody>";
		echo "</table>";
		echo "</div>";
	}



?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta charset="UTF-8">

	<meta name="description" content="">
	<meta name="author" content="Jayson Temporas">
	<meta http-equiv="content-type" content="text/html;charset=UTF-8">
	<META NAME="ROBOTS" CONTENT="INDEX, FOLLOW">
	<meta name="HandheldFriendly" content="True" />
	<meta name="theme-color" content="#212121">
	<link rel='shortcut icon' href='css/img/logo.jpg?v=3'/>

	<link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>

	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css">

	<link rel="stylesheet" href="css/numpad.css" />
	<link rel="stylesheet" href="css/select2.css" />

	<style>
		/*
		 * Select2 v3.4.6 styles customization for Flat UI
		 */
		/*----------------------------------------------- Main select element ------------------------------------------------*/
		.select2-container .select2-choice {
			height: 41px; /* Jobsy form controls have 37px total height */
			border: 2px solid #bdc3c7;
			border-radius: 6px;
			outline: none;
			font: 15px/38px "Lato", Liberation Sans, Arial, sans-serif;
			color: #34495e;

			/* important - to keep height always as constant */
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
			box-sizing: border-box;

			/* reset gradient */
			background-image: none;
			filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);

			-webkit-transition: border-color .25s;
			-moz-transition: border-color .25s;
			-ms-transition: border-color .25s;
			-o-transition: border-color .25s;
			transition: border-color .25s;
		}

		/* active state */
		.select2-container-active .select2-choice,
		.select2-container-active .select2-choices {
			border-color: #1abc9c;

			/* reset shadow */
			-webkit-box-shadow: none;
			box-shadow: none;
		}

		/* container state, when dropdown open */
		.select2-dropdown-open .select2-choice {
			border-bottom: none;
			border-radius: 6px 6px 0 0;
			padding-bottom: 2px;
			background-color: #fff;

			/* reset shadow */
			-webkit-box-shadow: none;
			box-shadow: none;

			/* reset gradient */
			background-image: none;
			filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
		}

		/* container state, when dropup open */
		.select2-dropdown-open.select2-drop-above .select2-choice,
		.select2-dropdown-open.select2-drop-above .select2-choices {
			border: 2px solid #1abc9c;
			border-radius: 0 0 6px 6px;
			padding-bottom: 0;
			border-top: none;
			padding-top: 2px;
			background-image: none;
			filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
		}

		/* disabled state */
		.select2-container.select2-container-disabled .select2-choice {
			background-color: #f4f6f6;
			border: 2px solid #d5dbdb;
			color: #d5dbdb;
			cursor: default;
		}

		/*-------------------------------------- X-icon (which clears selected result) ---------------------------------------*/
		.select2-container .select2-choice abbr {
			top: 12px;
		}

		.select2-container-active.select2-drop-above .select2-choice abbr {
			top: 14px;
		}

		/*---------------------------------------------------- Down-arrow ----------------------------------------------------*/
		.select2-container .select2-choice .select2-arrow {
			width: 22px;
			height: 27px;
			top: 5px;
			border: none;
			background: #fff;

			/* reset gradient */
			background-image: none;
			filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
		}

		.select2-dropdown-open.select2-drop-above .select2-choice .select2-arrow {
			top: 7px;
		}

		/*----------------------------------------------------- Dropdown -----------------------------------------------------*/
		/* dropdown with options */
		.select2-drop {
			-webkit-transition: border-color .25s;
			-moz-transition: border-color .25s;
			-ms-transition: border-color .25s;
			-o-transition: border-color .25s;
			transition: border-color .25s;

			/* reset shadow */
			-webkit-box-shadow: none;
			box-shadow: none;
		}

		/* dropup (case, when there is not enough space below the field) */
		.select2-drop.select2-drop-above {
			/* reset shadow */
			-webkit-box-shadow: none;
			box-shadow: none;
		}

		/* active dropdown */
		.select2-drop-active {
			border: 2px solid #1abc9c;
			border-top: none;
			border-radius: 0 0 6px 6px;
		}

		/* active dropup */
		.select2-drop.select2-drop-above.select2-drop-active {
			border-top: 2px solid #1abc9c;
			border-radius: 6px 6px 0 0;
		}

		.select2-results .select2-result {
			font: 15px/38px "Lato", Liberation Sans, Arial, sans-serif;
			color: #34495e;
		}

		.select2-results .select2-highlighted {
			background-color: #1abc9c;
			color: #fff;
		}

		/* help-text line */
		.select2-results .select2-no-results,
		.select2-results .select2-searching,
		.select2-results .select2-selection-limit {
			background: #fff;
			font: 15px/38px "Lato", Liberation Sans, Arial, sans-serif;
			color: #34495e;
		}

		/* "loading more results" message */
		.select2-more-results.select2-active {
			background: #f4f4f4 url('select2-spinner.gif') no-repeat 100%;
			padding: 4px 7px;
		}

		/* input in dropdown */
		.select2-search input {
			background: transparent;
			font: 15px/38px "Lato", Liberation Sans, Arial, sans-serif;
			color: #34495e;
		}

		/*----------------------------------------------- Retina displays fix ------------------------------------------------*/
		@media only screen and (-webkit-min-device-pixel-ratio: 1.5), only screen and (min-resolution: 144dpi) {
			.select2-container .select2-choice abbr {
				margin-top: 1px;
			}

			.select2-container .select2-choice .select2-arrow {
				margin-top: 1px;
			}
		}
		/* end select 2 */
		.mpointer{
			cursor: pointer;
		}
		#main-cart .collection{
			border: 0px !important;
			padding: 0px !important;
		}
		#main-cart .collection-item{
			border-bottom: 1px solid #333;
			width: 98%;

		}
		.btn-con .btn{
			width:100%;

		}

		#main-cart h4{
			position: relative;
		}
		#main-cart h4 span{

			position: absolute;
			top: 15px;
			line-height: 30px;
		}
		#main-cart h1{
			margin-left: 20px;
		}
		#main-cart p{
			margin: 0px;
			line-height: 16px;

		}
		#cart-con{

		}
		.con-box{
			width:50%;
			float: left;
			margin-left: 0px;
			height: 250px;
			text-align: center;
			overflow:  hidden !important ;
		}
		.con-box img{
			width:auto;
			height: 180px;

		}
		@media only screen and (max-width:  992px) {
			.con-box{
				width: 100%;
			}
			#cart-con-col-1{
				width:33%;
				float: left;
			}
			#cart-con-col-2{
				width: 33%;
				float: left;
			}
			#cart-con-col-3{
				width:33%;
				float: left;
			}

		}
		@media only screen and (max-width: 600px) {
			.con-box{
				width: 100%;
			}
			#cart-con-col-1{
				width:100%;

			}
			#cart-con-col-2{
				width: 100%;

			}
			#cart-con-col-3{
				width: 100%;
			}
		}
		.cart-image img{
			width: auto !important;
			height: 140px;
			margin:  0 auto !important;
			cursor: pointer;

		}
		.card-image img{
			overflow-x: hidden !important;


		}
		.item-name{
			font-size:9px;
		}

		.secondary-content{
			top: 40px !important;
		}
		#cart-con-list{

			overflow-y: auto;
			margin-top: -20px;
		}
		#cart-content,#cart-con-list{
			height: 75vh;;
			overflow-y: auto;
		}
		.nav-wrapper{
			padding-left: 15px;
			padding-right: 15px;
		}
		.swing li {
			background-color: #fff !important;
			opacity: 0;
			transform: rotateX(-90deg);
			transition: all 0.5s cubic-bezier(.36,-0.64,.34,1.76);

		}

		.swing li.show {
			opacity: 1;
			transform: none;
			transition: all 0.5s cubic-bezier(.36,-0.64,.34,1.76);
		}
		.swing {
			perspective: 100px;
		}
		.card-content {
			margin: 0px !important;
			padding: 0px !important;
			padding-left: 8px !important;
		}
		.btn-fix {
			width: 120px;
		}
		.txtQty{
			width: 70px !important;
			margin: 0px !important;
			padding: 0px !important;
		}
		.categ-title{
			display: block;
			position: relative;
			padding-top: 20px !important;
			padding-left: 20px;
		}
		.categ-url{
			margin-top: 10px;
			position: relative;

		}
		#categ-list li{

			padding-bottom: 0px !important;
			margin: 0px !important;

			height: 30px !important;
			font-size: 16px;
			font-weight: bold;
			cursor: pointer;
		}
		img.square{
			width: 50px;
			height: 50px;
			float: left;
			margin-left: -30px;
			margin-right: 20px;
			border-radius: 10px 10px 10px 10px;
			-moz-border-radius: 10px 10px 10px 10px;
			-webkit-border-radius: 10px 10px 10px 10px;
			border: 0px solid #000000;

		}
		.nmpd-display{
			text-align: center !important;
			font-size: 26px !important;
			font-weight: bold !important;
		}
		.nav-wrapper .brand-logo img {
			height: 64px !important;
		}


		@media (max-width: 600px) {
			.nav-wrapper .brand-logo img {
				height: 56px !important;
			}
		}
		#c-title{
			margin-left: 80px;
			font-size: 30px;
		}



		#total-item-in-cart{
			position: absolute;
			top: 2px;
			left: 45%;
		}
		.fixed-action-btn{
			bottom: 10px !important;
			left: 10px !important;

		}
		.fixed-action-btn ul{
			left: -240px !important;
		}
		.bottom-sheet{
			padding: 0px !important;
			margin: 0px;
		}
		#modalUpdate > .modal-content{
			padding: 10px;
			min-height: 150px;
		}
		#payment-container{
			position: fixed;
			height: 90vh;
			width: 90%;
			margin-left: 5%;
			margin-top: 5vh;
			overflow-y: auto;
			z-index: 99;
		}
		#payment-container > .row{
			margin-top: 10px;
		}
		#payment-container > #total-holder > div{
			margin-bottom: 3px;
		}
		#cash-container{
			margin-top:5%;
			min-height: 200px;
			padding-top: 30px;
		}
		#member-credit-container{
			margin-top:5%;
			min-height: 200px;
			padding-top: 30px;
		}
		#cash-total-holder{

		}
		#grand-total-holder{
			position: fixed;
			bottom: 0px;
			right: 100px;
			z-index: 100;
			padding: 10px;
		}
		#grand-total-holder-details{
			position: fixed;
			bottom: 90px;
			right: 30px;
			z-index: 100;
			height: 60px;
			padding: 10px;
		}
		.btn-width{
			width: 200px;
		}
		#change-holder{
			position: fixed;
			bottom: 0px;
			left: 100px;
			z-index: 100;
			padding: 10px;

		}
		#change-holder .btn{
			width: 300px;
		}
		#mem-con-bg-overlay{

		}
		#mem-con-bg{
			position: fixed;
			top: 0;
			left: 0;
			z-index: 99;
			background: #ccc;
			width: 100%;
			height: 100%;
			background-repeat:no-repeat;
			background-position: center center;
			background-image:url(css/img/reg_background.jpg);
		}

		#member-container-new{
			width:330px;
			height: 500px;
			z-index: 102;
			position: absolute;
			top: 10%;
			overflow-y: auto;

		}
		.full-height {
			position: absolute;
			top: 0 !important;
			height: 100% !important;
			overflow-y: auto;
		}

		#no-item-con{
			text-align: center;
			margin-top:20%;
		}
		.payment-container-close{
			position: absolute;
			top: 0px;
			right: 0px;
		}
		#menubtn{
			position: fixed;
			top:0px;
			left: -12px;
		}
		.fc-title{
			cursor: pointer;
		}
		.fc-center h2{
			font-size: 25px !important;
		}
		#btnNav1,#btnNav2{
			width: 250px;

		}
		@media only screen and (max-width: 800px) {
			#btnNav1,#btnNav2{
				width: 100%;
				display: block;
				margin-bottom: 5px;
			}
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

	</style>
</head>
<body>


	<div>

		<div class="navbar-fixed">
			<nav class='grey darken-4'>
				<div class="nav-wrapper">
					<a href="admin/index.php" class="brand-logo">
						<img src="css/img/demo-pic.png">

					</a>
					<a id='menubtn' href="#" data-activates="mobile-demo" class="button-collapse"><i class="material-icons">menu</i></a>

					<span id='c-title'></span>

					<ul   id="nav-mobile" class="right hide-on-med-and-down">

						<ul class="site-navigation nav navbar-nav">
							<li>
								<a href="http://www.safehouseacademy.com/index.html">HOME</a>
							</li>
							<li>
								<a href="http://www.safehouseacademy.com/about.html">ABOUT</a>
							</li>
							<li>
								<a href="http://www.safehouseacademy.com/trainings.html">TRAININGS</a>
							</li>
							<li>
								<a href="http://www.safehouseacademy.com/instructors.html">INSTRUCTORS</a>
							</li>
							<li>
								<a href="http://www.safehouseacademy.com/classes.html">CLASSES</a>
							</li>
							<li>
								<a href="http://www.safehouseacademy.com/contact.html">CONTACT</a>
							</li>
						</ul>
					</ul>

				</div>
			</nav>
		</div>
		<div >



			<ul class="side-nav" id="mobile-demo">
				<li >
					<div class="userView">
						<div style='height:100px;' class="background grey darken-4">
							<img style='width:90%;' src="css/img/demo-pic.png">
						</div>
					</div>
					<br><br>
				</li>
				<li>
					<a href="http://www.safehouseacademy.com/index.html">HOME</a>
				</li>
				<li>
					<a href="http://www.safehouseacademy.com/about.html">ABOUT</a>
				</li>
				<li>
					<a href="http://www.safehouseacademy.com/trainings.html">TRAININGS</a>
				</li>
				<li>
					<a href="http://www.safehouseacademy.com/instructors.html">INSTRUCTORS</a>
				</li>
				<li>
					<a href="http://www.safehouseacademy.com/classes.html">CLASSES</a>
				</li>
				<li>
					<a href="http://www.safehouseacademy.com/contact.html">CONTACT</a>
				</li>
			</ul>

			<input name="booking_type" id="booking_type"  type='hidden' value='1'>
			<div class="row">
				<div class="col s12">
			<div class="card-panel">
				<h3 class='center-align'>Train With Us</h3>
			<form  >

				<div class="row">

				<div class="input-field col s12 m4 l3">
						<i class="material-icons prefix">person</i>
						<input type='text' name="booking_name" id="booking_name"  required>

						<label for="booking_name">Client name</label>
				</div>
				<div class="input-field col s12 m4 l3">
					<i class="material-icons prefix">&#xE0CD;</i>
					<input type='text' name="booking_contact" id="booking_contact"  required>

					<label for="booking_contact">Contact number</label>
				</div>
				<div class="input-field col s12 m4 l3">
					<i class="material-icons prefix">&#xE0BE;</i>
					<input type='text' name="booking_email" id="booking_email"  required>

					<label for="booking_email">Email</label>
				</div>
				</div>
				<div class="row">
				<div class="input-field col s12 m4 l3">
					<i class="material-icons prefix">book</i>
					<select name="booking_class" id="booking_class">
						<?php foreach($arr_class_name as $sid => $sname){
							?>
							<option value="<?php echo $sid; ?>"><?php echo $sname; ?></option>
							<?php

						}?>
					</select>
					<label for="booking_class">Class</label>
				</div>

				<div class="input-field col s12 m4 l3">
					<i class="material-icons prefix">&#xE916;</i>
					<input id="booking_date" type="text" required>
					<label for="booking_date">Date</label>
				</div>

				<div class="input-field col s12 m4 l3">
					<i class="material-icons prefix">&#xE855;</i>
					<select name="booking_time" id="booking_time"  required>
						<option value="">Choose date first</option>
					</select>
					<label for="booking_time">Time</label>
				</div>
					<div class="input-field col s12 m4 l3 center-align">
						<button class='btn grey' id='btnSubmit'>Submit</button>
					</div>
				</div>
			</form>
			</div>
				</div>
			</div>
			<div class="row">
				<div class="col s12 center-align">
						<button class='btn grey darken-4' id='btnNav1'>Monthly Calendar</button>
						<button class='btn grey darken-4' id='btnNav2'>Weekly Schedule</button>
				</div>


				<div id="nav1" class="col s12" >
					<br>
					<div class="container">
						<h5>Showing schedule for <select  style='display:inline-block !important; width:230px;' name="booking_class_2" id="booking_class_2"  >
								<?php foreach($arr_class_name as $sid => $sname){
									?>
									<option value="<?php echo $sid; ?>"><?php echo $sname; ?></option>
									<?php

								}?>
							</select>
						</h5>
						<div class="progress" id='loading' style='display: none;'>
							<div class="indeterminate"></div>
						</div>

						<div id="calendar"></div>
					</div>
				</div>
				<div id="nav2" class="col s12" style='display:none;'>
					<br>
					<div class="row">
						<div class="col s12">
							<?php
								getClassesSchedule($mysqli);
							?>
						</div>
					</div>
				</div>

			</div>


		</div>

	</div>

<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js" ></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>
	<script src="member/js/moment.js"></script>
<script src="member/js/fullCalendar.js"></script>


<script>
	$(function(){

		$(".button-collapse").sideNav();
		window.mobilecheck = function() {
			var check = false;
			(function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
			return check;
		};
		$('body').on('click','#btnNav1',function(){
			$('#nav1').hide();
			$('#nav2').hide();
			$('#nav1').fadeIn(300);

		});
		$('body').on('click','#btnNav2',function(){
			$('#nav1').hide();
			$('#nav2').hide();
			$('#nav2').fadeIn(300);

		});
		$('#main-cart').show();
		$('#booking_date').pickadate({
			selectMonths: true,
			selectYears: 15,
			format: 'mm/dd/yyyy',
			closeOnSelect: true,
			//onOpen:getThisFunc,
			onSet: function (ele) {
				if(ele.select){
					this.close();
					getAvailableTime();
				}
			}
		});
		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next',
				center: 'title',
				right: 'month,basicWeek'
			},
			viewRender: function (view, element) {
				var b = $('#calendar').fullCalendar('getDate');
				getThisFunc(b.format('L'));
			},
			eventClick: function(calEvent, jsEvent, view) {

				if(calEvent.title != 'Fully booked'){

					resetDatetime();

					$('#booking_date').pickadate('picker').set('select', calEvent.dt, { format: 'mm/dd/yyyy' }).trigger("change");
					setTimeout(function(){
						$('#booking_time').html("<option value='"+calEvent.time_id+"'>"+calEvent.title+"</option>");
						$('#booking_time').material_select();
					},2000);

					$("html, body").animate({ scrollTop: $("#booking_email").offset().top }, "fast");

				}


				// change the border color just for fun
				//	$(this).css('border-color', 'red');

			},
			defaultView: window.mobilecheck() ? "basicDay" : "month",
			defaultDate: moment().format('YYYY-MM-DD'),
			eventLimit: true, // allow "more" link when too many events
			navLinks: true, // can click day/week names to navigate views
			events: []
		});
		$('body').on('change','#booking_class',function(){
			getThisFunc(moment().format('YYYY-MM-DD'));

			$('#booking_class_2').val($('#booking_class').val());
			resetDatetime();

		});
		$('body').on('change','#booking_class_2',function(){

			$('#booking_class').val($('#booking_class_2').val());
			$('#booking_class').material_select();
			getThisFunc(moment().format('YYYY-MM-DD'));
			resetDatetime();

		});
		function resetDatetime(){
			$('#booking_date').val('')
			$('#booking_time').html('');
			$('#booking_time').material_select();
		}
		getThisFunc(moment().format('YYYY-MM-DD'));
		function getThisFunc(dt){

			var class_id = $('#booking_class').val();
			var type = $('#booking_type').val();
			if(class_id){
				$('#loading').show();
				$.ajax({
					url:'member/service/service.php',
					type:'POST',
					dataType:'json',
					data: {functionName:'getFull',dt:dt,class_id:class_id,type:type},
					success: function(data){
						var cal = $('#calendar');
						cal.fullCalendar( 'removeEvents');
						cal.fullCalendar( 'addEventSource', data);
						cal.fullCalendar( 'rerenderEvents' );
						setTimeout(function(){
							$('#loading').hide();
						},1000);


					},
					error:function(){
						setTimeout(function(){
							$('#loading').hide();
						},1000);
					}
				});
			}
		}

		$('#booking_time').material_select();
		$('#bookingclasslable').html($('#booking_class option:selected').text());
		function getAvailableTime(){
			var dt = $('#booking_date').val();
			var class_id = $('#booking_class').val();
			var type = $('#booking_type').val();
			if(dt && class_id){
				$.ajax({
					url:'member/service/service.php',
					type:'POST',
					data: {functionName:'getAvailableTime',dt:dt,class_id:class_id,type:type},
					success: function(data){
						if(data){

							$('#booking_time').html(data);
							$('#booking_time').material_select();

						} else {
							Materialize.toast("No schedule for that date.",2000,"red lighten-2");
							$('#booking_date').val('')
							$('#booking_time').html('');
							$('#booking_time').material_select();
						}
					},
					error:function(){

					}
				});
			}
		}
		$('#booking_class').material_select();


		$('body').on('click','#btnSubmit',function(e){
			e.preventDefault();
			var con = $(this);
			var btnval = con.html();
			con.html('Loading...');
			con.attr('disabled',true);
			var name = $('#booking_name').val();
			var contact = $('#booking_contact').val();
			var email = $('#booking_email').val();
			var cls = $('#booking_class').val();
			var dt = $('#booking_date').val();
			var tm = $('#booking_time').val();
			var type = $('#booking_type').val();
			if(name && contact && email && cls && dt && tm){

				$.ajax({
					url:'member/service/service.php',
					type:'POST',
					data: {functionName:'submitSchedule',dt:dt,class_id:cls,type:type,tm:tm,name:name,email:email,contact:contact},
					success: function(data){
						if(data){
							Materialize.toast("Request submitted successfully.",3000,'green',function(){
								con.html(btnval);
								con.attr('disabled',false);
								location.reload();
							});


						} else {
							Materialize.toast("No schedule for that date.",2000,"red lighten-2");
							con.html(btnval);
							con.attr('disabled',false);
							$('#booking_date').val('')
							$('#booking_time').html('');
							$('#booking_time').material_select();
						}
					},
					error:function(){

					}
				});
			} else {
				con.html(btnval);
				con.attr('disabled',false);
				Materialize.toast("Please complete the form.",3000,"red lighten-2");
			}
		});
	});




</script>
</body>
</html>