<?php

	session_start();

	unset($_SESSION["acc_log"]);

	function getRealIpAddr()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
		{
			$ip=$_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
		{
			$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
			$ip=$_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
	$user_ip_addr = getRealIpAddr();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel='shortcut icon' href='css/img/logo.jpg?v=3' />
	<link href="css/bootstrap.css" rel="stylesheet">
	<script src="js/jquery.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/md5.js"></script>
	<!-- <script src="js/all_offline.js"></script> -->
	<script src="js/main_pos.js?12"></script>

	<style type="text/css">
		html {
			position: relative;
			min-height: 100%;
		}
		body {
			/* Margin bottom by footer height */
			margin-bottom: 60px;
		}
		.footer {
			position: absolute;
			bottom: 0;
			width: 100%;
			/* Set the fixed height of the footer here */
			height: 60px;
			background-color: #000;
		}
		.footermsg{
			color:white;
			text-align: center;
			padding-top:20px;
		}
		/* Absolute Center CSS Spinner */
		.loading {
			position: fixed;
			z-index: 999;
			height: 2em;
			width: 2em;
			overflow: show;
			margin: auto;
			top: 0;
			left: 0;
			bottom: 0;
			right: 0;
		}
		/* Transparent Overlay */
		.loading:before {
			content: '';
			display: block;
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background-color: rgba(0,0,0,0.3);
		}

		/* :not(:required) hides these rules from IE9 and below */
		.loading:not(:required) {
			/* hide "loading..." text */
			font: 0/0 a;
			color: transparent;
			text-shadow: none;
			background-color: transparent;
			border: 0;
		}
		.loading:not(:required):after {
			content: '';
			display: block;
			font-size: 10px;
			width: 1em;
			height: 1em;
			margin-top: -0.5em;
			-webkit-animation: spinner 1500ms infinite linear;
			-moz-animation: spinner 1500ms infinite linear;
			-ms-animation: spinner 1500ms infinite linear;
			-o-animation: spinner 1500ms infinite linear;
			animation: spinner 1500ms infinite linear;
			border-radius: 0.5em;
			-webkit-box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.5) -1.5em 0 0 0, rgba(0, 0, 0, 0.5) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
			box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) -1.5em 0 0 0, rgba(0, 0, 0, 0.75) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
		}
		/* Animation */
		@-webkit-keyframes spinner {
			0% {
				-webkit-transform: rotate(0deg);
				-moz-transform: rotate(0deg);
				-ms-transform: rotate(0deg);
				-o-transform: rotate(0deg);
				transform: rotate(0deg);
			}
			100% {
				-webkit-transform: rotate(360deg);
				-moz-transform: rotate(360deg);
				-ms-transform: rotate(360deg);
				-o-transform: rotate(360deg);
				transform: rotate(360deg);
			}
		}
		@-moz-keyframes spinner {
			0% {
				-webkit-transform: rotate(0deg);
				-moz-transform: rotate(0deg);
				-ms-transform: rotate(0deg);
				-o-transform: rotate(0deg);
				transform: rotate(0deg);
			}
			100% {
				-webkit-transform: rotate(360deg);
				-moz-transform: rotate(360deg);
				-ms-transform: rotate(360deg);
				-o-transform: rotate(360deg);
				transform: rotate(360deg);
			}
		}
		@-o-keyframes spinner {
			0% {
				-webkit-transform: rotate(0deg);
				-moz-transform: rotate(0deg);
				-ms-transform: rotate(0deg);
				-o-transform: rotate(0deg);
				transform: rotate(0deg);
			}
			100% {
				-webkit-transform: rotate(360deg);
				-moz-transform: rotate(360deg);
				-ms-transform: rotate(360deg);
				-o-transform: rotate(360deg);
				transform: rotate(360deg);
			}
		}
		@keyframes spinner {
			0% {
				-webkit-transform: rotate(0deg);
				-moz-transform: rotate(0deg);
				-ms-transform: rotate(0deg);
				-o-transform: rotate(0deg);
				transform: rotate(0deg);
			}
			100% {
				-webkit-transform: rotate(360deg);
				-moz-transform: rotate(360deg);
				-ms-transform: rotate(360deg);
				-o-transform: rotate(360deg);
				transform: rotate(360deg);
			}
		}
		body{
			background: #424242;
		}
		body {
			background-image:    url("css/img/login_bg.jpg");
			background-size:     cover;
			background-repeat:   no-repeat;
			background-position: center center;

		}
		#newtitle{
			font-family: Impact, Haettenschweiler, 'Franklin Gothic Bold', Charcoal, 'Helvetica Inserat', 'Bitstream Vera Sans Bold', 'Arial Black', 'sans serif';
			letter-spacing: 4px;
		}

		.form-control::-moz-placeholder {
			color: <?php echo $color_bg_background; ?>;
			opacity: 1;
		}
		.form-control:-ms-input-placeholder {
			color: <?php echo $color_bg_background; ?>;
		}
		.form-control::-webkit-input-placeholder {
			color: <?php echo $color_bg_background; ?>;
		}
		.overlay-black-login {
			position: fixed;

			left: 25%;
			width: 50%;
			height: 100%;
			background: #000;
			opacity: 0.5;
			z-index: -2;

		}
		@media only screen and (max-width: 1050px) {
			.overlay-black-login {
				position: fixed;
				top: 0;
				left: 10%;
				width: 80%;
				height: 100%;
				background: #000;
				opacity: 0.5;
				z-index: -2;
			}

		}
		@media only screen and (max-width: 800px) {
			.overlay-black-login {
				position: fixed;
				top: 0;
				left: 3%;
				width: 94%;
				height: 100%;
				background: #000;
				opacity: 0.5;
				z-index: -2;
			}

		}
	</style>
</head>
<body>
<div class="loading" style=''>Loading&#8230;</div>


<div class="container">

	<h1 id='posTitle' class='text-center' style='margin-bottom: 40px;margin-top: 70px;<?php echo $color_bg_background; ?>;'><span style='color:<?php echo $color_bg_background; ?>;'>Apollo System</span></h1>
	<div class="row">

		<div class="col-sm-3"></div>
		<div class="col-sm-6">

			<div  style='background-color: rgba(0, 0, 0, 0.1);color:#000;border: 1px solid <?php echo $color_bg_background; ?>;'>
				<div class="" style='background-color: transparent;color:<?php echo $color_bg_background; ?>;height:60px;padding:20px;'>
					<noscript style='color:#ff0000;'>Javascript ERROR: Please turn on Javascript on your browser</noscript>
					<h3 class="panel-title"><strong>Log In </strong></h3>
					<p>Your IP: <?php echo $user_ip_addr; ?></p>
					<?php
						if($http_host == 'apollosystems.net' && $sublinknet == 'https://apollosystems.net/avision'){
							?>
							<p class='text-danger'>Please check that you are visiting</p>
							<img src="secure/avisionlogin.png" alt="avisionlogin">
							<?php
						}

					?>
				</div>
				<div class="">
					<div id='flashmsg'></div>

					<form action="" id='loginform'>
						<table class='table'>
							<tr>
								<td>
									<div style='border: 1px solid <?php echo $color_bg_background; ?>;' class="input-group input-group-lg">
										<span style='background-color: transparent;color:<?php echo $color_bg_background; ?>;' class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
										<input style='background-color: transparent;color:<?php echo $color_bg_background; ?>;' type="text" name='username' id='username' class='form-control' placeholder="Username" required />
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div style='border: 1px solid <?php echo $color_bg_background; ?>;' class="input-group input-group-lg">
										<span style='background-color: transparent;color:<?php echo $color_bg_background; ?>;' class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
										<input style='background-color: transparent;color:<?php echo $color_bg_background; ?>;'  type="password" name='password' id='password' class='form-control' placeholder="Password" required />
									</div>
								</td>
							</tr>
							<tr>
								<td class='text-right'>
									<button style='background-color: transparent;color:<?php echo $color_bg_background; ?>;' type='button' class='btn btn-default' name='btn_button' id='btn_button'>LOG IN</button>
								</td>
							</tr>
						</table>
					</form>

				</div>
			</div>
		</div>
		<div class="col-sm-3"></div>
	</div>

</div>
<!--
<div class="footer">
	<div class="container">
		<p class="footermsg"> &copy; Mutual Success Lightings Fixtures Inc. (2016)</p>
	</div>
</div>
-->
<script>
	$(function(){

		$("#username").focus();

		$('.loading').hide();

		if(localStorage["company_name"]){
			$('#posTitle').html("<span style='color:<?php echo $color_bg_background; ?>;' class='glyphicon glyphicon-shopping-cart'></span> <span id='newtitle' style='color:<?php echo $color_bg_background; ?>;'> " + localStorage["company_name"].toUpperCase() +"</span>");
		}

		updatemycache();
		if(localStorage["flashmsg"] != null){
			$("#flashmsg").empty();
			$("#flashmsg").append(localStorage["flashmsg"]);
			localStorage.removeItem("flashmsg");
		} else {
			$("#flashmsg").empty();
		}
		// get users if there is internet connection
		if( con.hostReachable()){

			if(localStorage["company_id"] == null){
				// if company not specified get all users
				//getUsers(0);
			} else {
				// get by company
				//getUsers(localStorage["company_id"]);
				$.ajax({
					url:'ajax/ajax_query.php',
					type:'post',
					data: {cid:localStorage["company_id"],functionName:'getAlternateLabels'},
					success: function(data){
						if(data){
							localStorage['labels'] = data;
						}
						//  var d = JSON.parse(data);
						//  alert(d['stations'].label_name);
					},
					error:function(){

					}
				});
			}
		}
		var users;
		if(localStorage["users"] != null){
			users = JSON.parse(localStorage["users"]);
		}

		$('#password').keypress(function (e) {
			var key = e.which;
			if(key == 13)  // the enter key code
			{
				logMeIn();
			}
		});

		$('#btn_button').click(function(){
			logMeIn();
		});

		logoutAccounting();

		function logoutAccounting()
		{
				$.ajax({
						url: "/dunsk/accounting/public/api/logout",
						type: "POST",
						data: {

						},
						success: function(data){

						},
						error: function(){

						}
				});
		}

		function logMeIn(){
			var username = $("#username").val().trim();
			var password = $("#password").val();

			//temporarily save the credentials in local storage
			localStorage.setItem('username', username);
			localStorage.setItem('password', password);

			loginAdmin(username,password);
			/*
			$('.loading').show();
			if(localStorage["users"] != null){

				var username = $("#username").val().trim();
				var password = $("#password").val();
				users = JSON.parse(localStorage["users"]);
				user = users[username];
				if(username !='' && password != ''){
					if(user){
						if(user.password.trim() == md5(password.trim())){
							localStorage["current_id"] = user.id;
							localStorage["current_lastname"] = user.lastname;
							localStorage["current_middlename"] = user.middlename;
							localStorage["current_firstname"] = user.firstname;
							localStorage["current_username"] = user;
							localStorage["current_position"] = user.position;
							localStorage["current_position_id"] = user.position_id;
							localStorage["current_permissions"] = user.permissions;
							localStorage["company_id"] = user.company_id;
							localStorage["branch_id"] = user.branch_id;
							if(!localStorage['terminal_id']){
								localStorage["terminal_id"] = 0;
							}
							localStorage["company_name"] = user.company_name;
							localStorage["current_permissions"];
							permissions = JSON.parse(localStorage["current_permissions"]);

							if(permissions.mainpos){
								localStorage['has_mainpos'] = 1;
							} else {
								localStorage.removeItem('has_mainpos');
							}
							if(permissions.branch){
								// process log in of admin
								if(con.hostReachable()){
									loginAdmin(username,password);
								} else {
									location.href = "index.php";
								}
							} else {
								if(con.hostReachable()){
									loginAdmin(username,password);
								} else {
									location.href = "index.php";
								}
							}

						} else {
							localStorage["flashmsg"] = "<div style='background-color: transparent;color:#fff;' class='alert alert-danger'>Wrong Username or Password</div>";
							location.reload();
						}
					} else {
						localStorage["flashmsg"] = "<div style='background-color: transparent;color:#fff;'  class='alert alert-danger'>Wrong Username or Password</div>";
						location.reload()
					}
				}
				else {
					localStorage["flashmsg"] = "<div style='background-color: transparent;color:#fff;'  class='alert alert-danger'>Username and password are required</div>";
					location.reload();
				}
			} else {
				alert('Please Connect on the Internet first');
				$('.loading').hide();
			}*/
		}


	});
</script>
</body>
</html>