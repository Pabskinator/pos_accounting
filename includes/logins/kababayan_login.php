<?php
	$css_main = "#ccc;";
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel='shortcut icon' href='css/img/logo.png' type='image/x-icon' />
	<link href="css/bootstrap.css" rel="stylesheet">
	<!--	<script src="js/jquery.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/md5.js"></script> -->
	<script src="js/all_offline.js"></script>
	<script src="js/main_pos.js"></script>
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

		.footermsg {
			color: white;
			text-align: center;
			padding-top: 20px;
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
			background-color: rgba(0, 0, 0, 0.3);
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

		body {
			background-image: url(css/img/LogIn.jpg);
			background-size: cover;
			background-repeat: no-repeat;
			background-position: center center;
		}
		@media only screen and (max-width: 800px) {
			body{
				background: #efefef;
			}
		}
		#newtitle {
			font-family: Impact, Haettenschweiler, 'Franklin Gothic Bold', Charcoal, 'Helvetica Inserat', 'Bitstream Vera Sans Bold', 'Arial Black', 'sans serif';
			letter-spacing: 4px;
		}

		.form-control::-moz-placeholder {
			color: <?php echo $css_main; ?>;
			opacity: 1;
		}

		.form-control:-ms-input-placeholder {
			color: <?php echo $css_main; ?>;
		}

		.form-control::-webkit-input-placeholder {
			color: <?php echo $css_main; ?>;
		}

		@media only screen and (max-width: 1000px) {
			.log_img {
				width: 100%;
			}
		}
		#form-holder{
			padding:10px;
		}
		.input-holder{
			-webkit-border-radius: 20px !important;
			-moz-border-radius: 20px;
			border-radius: 20px;
		}
		.graph_img{
			width:100%;
		}
	</style>
</head>
<body>
<div class="loading" style=''>Loading&#8230;</div>
<iframe id='manifest_iframe_hack' style='display: none;' src='temp.html'></iframe>
<div class="container">

	<div class="row" style='margin-top: 40px;'>
		<div class="col-sm-8"></div>
		<div class="col-sm-4">
			<div style='border-radius: 20px;background-color: rgba(135, 211, 124, 0.9);color:#000;border: 1px solid <?php echo $css_main; ?>;'>
				<div class="">
					<div class='text-center' style='<?php echo $css_main; ?>;'>
						<img class='log_img' src="css/img/KababayanKoPOS.png" alt="KababayanKoPOS">
					</div>
					<div id="form-holder">
						<div id='flashmsg'></div>

						<form action="" id='loginform'>
							<div class="form-group">
								<div  class="input-group input-group-lg input-holder">
									<span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
									<input  type="text" name='username' id='username' class='form-control' placeholder="Username" required />
								</div>
							</div>
							<div class="form-group">
								<div class="input-group input-group-lg input-holder">
									<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
									<input  type="password" name='password' id='password' class='form-control' placeholder="Password" required />
								</div>
							</div>
							<div class="form-group">
								<button style='width:100%;height:40px;' type='button' class='btn btn-primary' name='btn_button' id='btn_button'>LOG IN</button>
							</div>
						</form>
					</div>
					<div class='text-center' style='<?php echo $css_main; ?>;'>
						<div class='hidden-xs'>
						<img class='graph_img' src="css/img/login_graph.png" alt="Graph">
						</div>
					</div>
				</div>
			</div>
		</div>

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
	$(function() {
		$('.loading').hide();
		if(localStorage["company_name"]) {
			$('#posTitle').html("<span style='color:<?php echo $css_main; ?>;' class='glyphicon glyphicon-shopping-cart'></span> <span id='newtitle' style='color:<?php echo $css_main; ?>;'> " + localStorage["company_name"].toUpperCase() + " POS</span>");
		}
		updatemycache();
		if(localStorage["flashmsg"] != null) {
			$("#flashmsg").empty();
			$("#flashmsg").append(localStorage["flashmsg"]);
			localStorage.removeItem("flashmsg");
		} else {
			$("#flashmsg").empty();
		}
		// get users if there is internet connection
		if(con.hostReachable()) {

			if(localStorage["company_id"] == null) {
				// if company not specified get all users
				//getUsers(0);
			} else {
				// get by company
				//getUsers(localStorage["company_id"]);
				$.ajax({
					url: 'ajax/ajax_query.php',
					type: 'post',
					data: {cid: localStorage["company_id"], functionName: 'getAlternateLabels'},
					success: function(data) {
						if(data) {
							localStorage['labels'] = data;
						}
						//  var d = JSON.parse(data);
						//  alert(d['stations'].label_name);
					},
					error: function() {

					}
				});
			}
		}
		var users;
		if(localStorage["users"] != null) {
			users = JSON.parse(localStorage["users"]);
		}

		$('#password').keypress(function(e) {
			var key = e.which;
			if(key == 13)  // the enter key code
			{
				logMeIn();
			}
		});

		$('#btn_button').click(function() {
			logMeIn();
		});

		function logMeIn() {
			var username = $("#username").val().trim();
			var password = $("#password").val();
			loginAdmin(username, password);
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
							localStorage["flashmsg"] = "<div style='background-color: transparent;color:

			<?php echo $css_main; ?>;' class='alert alert-danger'>Wrong Username or Password</div>";
							location.reload();
						}
					} else {
						localStorage["flashmsg"] = "<div style='background-color: transparent;color:

			<?php echo $css_main; ?>;'  class='alert alert-danger'>Wrong Username or Password</div>";
						location.reload()
					}
				}
				else {
					localStorage["flashmsg"] = "<div style='background-color: transparent;color:

			<?php echo $css_main; ?>;'  class='alert alert-danger'>Username and password are required</div>";
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