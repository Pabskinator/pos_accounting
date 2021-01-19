<?php
	require_once('secured_connection.php');
	$id = Input::get('affiliate');
	$id = Encryption::encrypt_decrypt('decrypt',$id);
	$cur_url = "http://" . $_SERVER['HTTP_HOST'] . "/secure/affiliate.php?affiliate=" . Encryption::encrypt_decrypt('encrypt',$id);
	$store_points = 0;
	$is_negative = 0;
	if(is_numeric($id)){
		$affiliate = new Affiliate($id);
		if(!$affiliate){
			die("Invalid affiliate.");
		}
		$store_points = $affiliate->data()->current_wallet;
		$is_negative = $affiliate->data()->is_negative;
		if($is_negative && date('m/d/Y',$is_negative) != date('m/d/Y')){
			die("The store doesn't have enough points. Please reload to continue.");
		}
	} else {

		die("You are not allowed to use this page.");
	}
	$current_ip = $_SERVER['REMOTE_ADDR'];
	$color = "#fff";
	?>
<!doctype html>
<html lang="en">
<head>
	<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">

	<title>Secure connection</title>
	<link rel="stylesheet" href="../css/bootstrap.css">

	<style>
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
		
		body {
			/*background-color: #019875 ; */
			background-image: url("../css/img/aff.jpg");
			background-size:     cover;
			background-repeat:   no-repeat;
			background-position: center center;
		}
		#newtitle{
			font-family: Impact, Haettenschweiler, 'Franklin Gothic Bold', Charcoal, 'Helvetica Inserat', 'Bitstream Vera Sans Bold', 'Arial Black', 'sans serif';
			letter-spacing: 4px;
		}

		.form-control::-moz-placeholder {
			color: <?php echo $color; ?>;
			opacity: 1;
		}
		.form-control:-ms-input-placeholder {
			color: <?php echo $color; ?>;
		}
		.form-control::-webkit-input-placeholder {
			color: <?php echo $color; ?>;
		}
		#aff_logo{
			height: 30px;
			-webkit-border-radius: 10px;
			-moz-border-radius: 10px;
			border-radius: 10px;: ;
		}

		/* Loading indicator */
		body {
			background: #888;    /* container background */
			padding: 12px;
		}
		/* Loading indicator */
		.loading {
			position: fixed;
			width: 72px;    /* diameter */
			height: 72px;    /* diameter */
			top:40%;
			left:43%;
		}
		.outer, .inner, .loading:after {
			position: absolute;
			left: 0;
			top: 0;
			right: 0;
			bottom: 0;
		}
		/* Mask */
		.loading:after {
			content:" ";
			margin: 10%;    /* stroke width */
			border-radius: 100%;
			background: #888;    /* container background */
		}
		/* Spinning gradients */
		.outer, .inner {
			animation-duration: 5s;    /* speed */
			-webkit-animation-duration: 5s;    /* speed */
			animation-iteration-count: infinite;
			-webkit-animation-iteration-count: infinite;
			animation-timing-function: linear;
			-webkit-animation-timing-function: linear;
		}
		.outer {
			animation-name: rotate-outer;
			-webkit-animation-name: rotate-outer;
		}
		.inner {
			animation-name: rotate-inner;
			-webkit-animation-name: rotate-inner;
		}
		/* Halfs */
		.outer:before, .inner:before, .outer:after, .inner:after {
			position: absolute;
			top: 0;
			bottom: 0;
			content:" ";
		}
		/* Left half */
		.outer:before, .inner:before {
			left: 0;
			right: 50%;
			border-radius: 72px 0 0 72px;    /* diameter */
		}
		/* Right half */
		.outer:after, .inner:after {
			left: 50%;
			right: 0;
			border-radius: 0 72px 72px 0;    /* diameter */
		}
		/* Half gradients */
		.outer:before {
			background-image: -webkit-linear-gradient(top, hsla(0, 0%, 100%, 0.0), hsla(0, 0%, 100%, 0.5));
			background-image: -moz-linear-gradient(top, hsla(0, 0%, 100%, 0.0), hsla(0, 0%, 100%, 0.5));
			background-image: linear-gradient(to bottom, hsla(0, 0%, 100%, 0.0), hsla(0, 0%, 100%, 0.5));
		}
		.outer:after {
			background-image: -webkit-linear-gradient(top, hsla(0, 0%, 100%, 1.0), hsla(0, 0%, 100%, 0.5));
			background-image: -moz-linear-gradient(top, hsla(0, 0%, 100%, 1.0), hsla(0, 0%, 100%, 0.5));
			background-image: linear-gradient(to bottom, hsla(0, 0%, 100%, 1.0), hsla(0, 0%, 100%, 0.5));
		}
		.inner:before {
			background-image: -webkit-linear-gradient(top, hsla(0, 0%, 100%, 0.5), hsla(0, 0%, 75%, 0.5));
			background-image: -moz-linear-gradient(top, hsla(0, 0%, 100%, 0.5), hsla(0, 0%, 75%, 0.5));
			background-image: linear-gradient(to bottom, hsla(0, 0%, 100%, 0.5), hsla(0, 0%, 75%, 0.5));
		}
		.inner:after {
			background-image: -webkit-linear-gradient(top, hsla(0, 0%, 50%, 0.5), hsla(0, 0%, 75%, 0.5));
			background-image: -moz-linear-gradient(top, hsla(0, 0%, 50%, 0.5), hsla(0, 0%, 75%, 0.5));
			background-image: linear-gradient(to bottom, hsla(0, 0%, 50%, 0.5), hsla(0, 0%, 75%, 0.5));
		}
		/* Spinning animations */
		@keyframes rotate-outer {
			0% {
				transform: rotate(0deg);
				-moz-transform: rotate(0deg);
				-webkit-transform: rotate(0deg);
			}
			100% {
				transform: rotate(1080deg);
				-moz-transform: rotate(1080deg);
				-webkit-transform: rotate(1080deg);
			}
		}
		@-webkit-keyframes rotate-outer {
			0% {
				-webkit-transform: rotate(0deg);
			}
			100% {
				-webkit-transform: rotate(1080deg);
			}
		}
		@keyframes rotate-inner {
			0% {
				transform: rotate(720deg);
				-moz-transform: rotate(720deg);
				-webkit-transform: rotate(720deg);
			}
			100% {
				transform: rotate(0deg);
				-moz-transform: rotate(0deg);
				-webkit-transform: rotate(0deg);
			}
		}
		@-webkit-keyframes rotate-inner {
			0% {
				-webkit-transform: rotate(720deg);
			}
			100% {
				-webkit-transform: rotate(0deg);
			}
		}
	</style>
</head>
<body oncontextmenu="return false">
<div class="container" style='display:none;'>

	<h1 id='posTitle' class='text-center' style='margin-bottom: 10px;margin-top: 15px;#fff;'><span style='color:#fff;'><img id='aff_logo' src="../css/img/logo.jpg" alt="Logo"/></h1>
	<div class="row">
		<div class="col-sm-3"></div>
		<div class="col-sm-6">
			<div  style='background-color: rgba(255, 255, 255, 0.1);color:#000;border: 1px solid #fff;'>
				<div class="" style='background-color: transparent;color:<?php echo $color; ?>;height:60px;padding:5px;'>
					<h5 class="panel-title"><strong>Verification for <?php echo $affiliate->data()->name; ?></strong></h5>
					<div class="row">
						<div class="col-md-6">
							<small style='display:block;color:<?php echo $color; ?>' >Your IP: <?php echo $current_ip; ?></small>
						</div>
						<div class="col-md-6">
							<small style='display:block;color:<?php echo $color; ?>' >Store Wallet: <?php echo $store_points; ?></small>
						</div>
					</div>



				</div>
				<div class="">
					<div id='flashmsg'></div>
					<form action="" id='loginform'>
						<input id='aff_id' type="hidden" value='<?php echo Input::get('affiliate'); ?>'>
						<table class='table'>
							<tr>
								<td>
									<div style='border: 1px solid <?php echo $color; ?>;' class="input-group input-group-lg">
										<span style='background-color: transparent;color:<?php echo $color; ?>;' class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
										<input style='background-color: transparent;color:<?php echo $color; ?>;' type="text" name='username' autocomplete="off" id='username' class='form-control' placeholder="Username" required />
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div style='border: 1px solid <?php echo $color; ?>;' class="input-group input-group-lg">
										<span style='background-color: transparent;color:<?php echo $color; ?>;' class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
										<input style='background-color: transparent;color:<?php echo $color; ?>;'  type="password" name='password' id='password'  autocomplete="off" class='form-control' placeholder="Password" required />
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div style='border: 1px solid <?php echo $color; ?>;' class="input-group input-group-lg">
										<span style='background-color: transparent;color:<?php echo $color; ?>;' class="input-group-addon"><span class="glyphicon glyphicon-list-alt"></span></span>
										<input style='background-color: transparent;color:<?php echo $color; ?>;'  autocomplete="off"  type="text" name='total' id='total' class='form-control' placeholder="Total amount" required />
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div style='border: 1px solid <?php echo $color; ?>;' class="input-group input-group-lg">
										<span style='background-color: transparent;color:<?php echo $color; ?>;' class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
										<input style='background-color: transparent;color:<?php echo $color; ?>;' autocomplete="off" type="password" name='security_code' id='security_code' class='form-control' placeholder="Security Code" required />
									</div>
								</td>
							</tr>
							<tr>
								<td class='text-right'>
									<button style='background-color: transparent;color:<?php echo $color; ?>;' type='button' class='btn btn-default' name='btn_process' id='btn_process'>PROCESS</button>
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
<div class="loading">
	<div class="outer"></div>
	<div class="inner"></div>
</div>
<script src='../js/jquery.js'></script>
<script>
	$(function(){
		$('.container').show();
		$('.loading').hide();
		/*
			background-size:     cover;
			background-repeat:   no-repeat;
			background-position: center center;
		 */
		$('body').css(
			{'background-image':'url(../css/img/aff.jpg)','background-size':'cover','background-repeat':'no-repeat','background-position': 'center center'}
		);
		$('body').on('click','#btn_process',function(){

			var username = $('#username').val();
			var password = $('#password').val();
			var total = $('#total').val();
			var security_code = $('#security_code').val();
			var aff_id = $('#aff_id').val();
			var con = $(this);
			var old_val = con.html();
			if(username && password && total && security_code && !isNaN(total)){
				con.attr('disabled',true);
				con.html('Loading...');
				$.ajax({
					url:'process.php',
					type:'POST',
					data: {username:username,password:password,total:total,security_code:security_code,aff_id:aff_id},
					success: function(data){
						alert(data);
						location.href = '<?php echo $cur_url; ?>';
						con.attr('disabled',false);
						con.html(old_val);
					},
					error:function(){
						con.attr('disabled',false);
						con.html(old_val);
					}
				});
			} else {
				alert("Please complete the form.");
			}
		});
	});
</script>
</body>
</html>

