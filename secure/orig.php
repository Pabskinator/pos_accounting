<?php
	require_once('secured_connection.php');
	$id = Input::get('affiliate');
	$id = Encryption::encrypt_decrypt('decrypt',$id);

	if(is_numeric($id)){
		$affiliate = new Affiliate($id);
		if(!$affiliate){
			die("Invalid affiliate.");
		}
	} else {
		die("You are not allowed to use this page.");
	}
	$current_ip = $_SERVER['REMOTE_ADDR'];
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Secured connection</title>
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
			background-color: #019875 ;
			background-size:     cover;
			background-repeat:   no-repeat;
			background-position: center center;
		}
		#newtitle{
			font-family: Impact, Haettenschweiler, 'Franklin Gothic Bold', Charcoal, 'Helvetica Inserat', 'Bitstream Vera Sans Bold', 'Arial Black', 'sans serif';
			letter-spacing: 4px;
		}

		.form-control::-moz-placeholder {
			color: #fff;
			opacity: 1;
		}
		.form-control:-ms-input-placeholder {
			color: #fff;
		}
		.form-control::-webkit-input-placeholder {
			color: #fff;
		}
		#aff_logo{
			height: 30px;
			-webkit-border-radius: 10px;
			-moz-border-radius: 10px;
			border-radius: 10px;: ;
		}

	</style>
</head>
<body oncontextmenu="return false">
<div class="container">

	<h1 id='posTitle' class='text-center' style='margin-bottom: 10px;margin-top: 15px;#fff;'><span style='color:#fff;'><img id='aff_logo' src="../css/img/logo.jpg" alt="Logo"/></h1>
	<div class="row">
		<div class="col-sm-3"></div>
		<div class="col-sm-6">
			<div  style='background-color: rgba(255, 255, 255, 0.1);color:#000;border: 1px solid #fff;'>
				<div class="" style='background-color: transparent;color:#fff;height:60px;padding:20px;'>
					<h5 class="panel-title"><strong>Verification for <?php echo $affiliate->data()->name; ?></strong></h5>
					<small style='display:block;color:#fff' >Your IP: <?php echo $current_ip; ?></small>
				</div>
				<div class="">
					<div id='flashmsg'></div>
					<form action="" id='loginform'>
						<input id='aff_id' type="hidden" value='<?php echo Input::get('affiliate'); ?>'>
						<table class='table'>
							<tr>
								<td>
									<div style='border: 1px solid #fff;' class="input-group input-group-lg">
										<span style='background-color: transparent;color:#fff;' class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
										<input style='background-color: transparent;color:#fff;' type="text" name='username' id='username' class='form-control' placeholder="Username" required />
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div style='border: 1px solid #fff;' class="input-group input-group-lg">
										<span style='background-color: transparent;color:#fff;' class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
										<input style='background-color: transparent;color:#fff;'  type="password" name='password' id='password'  autocomplete="off" class='form-control' placeholder="Password" required />
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div style='border: 1px solid #fff;' class="input-group input-group-lg">
										<span style='background-color: transparent;color:#fff;' class="input-group-addon"><span class="glyphicon glyphicon-list-alt"></span></span>
										<input style='background-color: transparent;color:#fff;'  autocomplete="off"  type="text" name='total' id='total' class='form-control' placeholder="Total amount" required />
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div style='border: 1px solid #fff;' class="input-group input-group-lg">
										<span style='background-color: transparent;color:#fff;' class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
										<input style='background-color: transparent;color:#fff;' autocomplete="off" type="password" name='security_code' id='security_code' class='form-control' placeholder="Security Code" required />
									</div>
								</td>
							</tr>
							<tr>
								<td class='text-right'>
									<button style='background-color: transparent;color:#fff;' type='button' class='btn btn-default' name='btn_process' id='btn_process'>PROCESS</button>
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
<script src='../js/jquery.js'></script>
<script>
	$(function(){
		$('body').on('click','#btn_process',function(){

			var username = $('#username').val();
			var password = $('#password').val();
			var total = $('#total').val();
			var security_code = $('#security_code').val();
			var aff_id = $('#aff_id').val();
			$.ajax({
			    url:'process.php',
			    type:'POST',
			    data: {aff_id:aff_id,username:username,password:password,total:total,security_code:security_code},
			    success: function(data){
			        console.log(data);
			    },
			    error:function(){
			        
			    }
			})
		});
	});
</script>
</body>
</html>

