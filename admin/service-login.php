<?php
	include_once '../core/admininit.php';

	$str = "annual membership fee";

	?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Document</title>

	<link rel="stylesheet" href="../css/materialize.min.css">
	<link rel="stylesheet" href="../css/animate.css">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<style>
		.overlay {
			position: fixed;
			top: 0;
			left: 0;
			min-width: 100%;
			min-height: 100%;

			background-image:    url(../css/img/safehouse3.jpg);
			background-size:     cover;
			background-repeat:   no-repeat;
			background-position: center center;
			z-index: -3;
			-webkit-filter: blur(3px);
			-moz-filter: blur(3px);
			-o-filter: blur(3px);
			-ms-filter: blur(3px);
			filter: blur(3px);
		}
		.overlay-black-login {
			position: fixed;

			left: 30%;
			width: 40%;
			height: 100%;
			background: #000;
			opacity: 0.7;
			z-index: -2;

		}
		.overlay-black {
			position: absolute;
			top: 0;
			left: 0;
			min-width: 100%;
			min-height: 100%;
			background: #000;
			opacity: 0.5;
			z-index: -2;
		}
		.below-content-overlay a {
			color:#fff !important;
		}

		.main_nav a{
			color:#fff !important;
		}


		.login-container i {
			color:#fff;
		}
		.login-container{
			margin-top:20%;
		}

		#logo-con{
			text-align: center;
		}
		@media only screen and (max-width: 1050px) {
			.overlay-black-login {
				position: fixed;
				top: 0;
				left: 10%;
				width: 80%;
				height: 100%;
				background: #000;
				opacity: 0.7;
				z-index: -2;
			}
			.login-container{
				margin-top:10%;
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
				opacity: 0.7;
				z-index: -2;
			}
			.login-container{
				margin-top:5%;
			}
		}
		@media only screen and (max-width: 500px) {
			.overlay-black-login {
				position: fixed;
				top: 0;
				left: 1%;
				width: 98%;
				height: 100%;
				background: #000;
				opacity: 0.7;
				z-index: -2;
			}
			.login-container{
				margin-top:20px;
			}
		}
		#pdlog{
			padding:10px;
		}
		.medium-text{
			font-size: 20px;
		}
	</style>
</head>
<body>
<div class="overlay"></div>
<div class="overlay-black"></div>
<div class="overlay-black-login">
	<div class='login-container'>
		<div class="container" id='pdlog'>

			<div id='logo-con' class='animated bounceInLeft'>
				<i class="medium material-icons">lock</i>
			</div>
			<p id='lblSafehouse' class='white-text center-align animated bounceInRight' >Safehouse Fight Academy</p>

			<form id='formLogin' style='opacity:0;' autocomplete="off">
				<div class="switch center-align">
					<label>
						<span id='lblSubs' class='red-text'>Subscription</span>
						<input type="checkbox" id='chkPlan'>
						<span class="lever"></span>
						<span id='lblCons' >Consumables</span>
					</label>
				</div>
				<input type="hidden" value="<?php echo uniqid(); ?>">
			<br>
						<div class="input-field">
							<i class="material-icons prefix">lock</i>
							<input autocomplete="off" id="username" type="text" class="validate white-text">
							<label for="username">Username</label>
						</div>
			<br>
			<span class='white-text'>
			<?php

			?>
				</span>
			<br>

						<div class="center-align">
							<a id='btnLogIn' class="waves-effect waves-light btn grey darken-2">Log In</a>
						</div>
			</form>
		</div>
	</div>
</div>
<div class='main_nav'>
	<a href="#">&nbsp;</a>
	<a href="main.php"  class="button-collapse waves-effect waves-teal btn-flat left"><i class="medium material-icons left">home</i></a>
	<a href="list.php"  class="button-collapse waves-effect waves-teal btn-flat right"><i class="medium material-icons left">list</i></a>
</div>
<!-- Modal Structure -->
<div id="modal1" class="modal">
	<div class="modal-content">
		<h5>Choose Service</h5>
		<input type="hidden" id='member_id' value="0">
		<table id='tbl_services'>
			<thead>
			<tr>
				<th>Use</th>
				<th>
					Service Name
				</th>
				<th>
					Session remaining
				</th>
				<th>
					Valid until
				</th>
			</tr>
			</thead>
			<tbody>

			</tbody>
		</table>

	</div>
	<div class="modal-footer">

		<a href="#" id='btnSubmit' class="waves-effect waves-green btn">Submit</a> &nbsp;&nbsp;&nbsp;
		<a href="#" class=" modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>

	</div>
</div>
<script src="../js/jquery.js"></script>
<script src="../js/materialize.min.js"></script>
<script>
	$(function(){
		$('#modal1').modal();

		setTimeout(function(){
			$('#formLogin').addClass("animated fadeIn");

		},1000);

		$('body').on('change','#chkPlan',function(){
			if($(this).is(":checked")){
				$('#lblSubs').removeClass("red-text");
				$('#lblCons').addClass("red-text");

			} else {
				$('#lblSubs').addClass("red-text");
				$('#lblCons').removeClass("red-text");
			}
		});
		function timeConverter(UNIX_timestamp){
			var a = new Date(UNIX_timestamp * 1000);
			var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
			var year = a.getFullYear();
			var month = months[a.getMonth()];
			var date = a.getDate();

			var time = date + ' ' + month + ' ' + year;
			return time;
		}
		$('#username').keypress(function (e) {
			var key = e.which;
			if(key == 13)  // the enter key code
			{
				$('#btnLogIn').click();
			}
		});
		$('body').on('click','#btnLogIn',function(e){
			e.preventDefault();
			var username = $('#username').val();

			var con = $(this);
			var oldval = con.text();
			var is_sub = $('#chkPlan').is(":checked");
			is_sub = (is_sub) ? 0 : 1;
			con.attr('disabled',true);
			con.text("Loading...");
			if(username){
				$.ajax({
					url:'../ajax/ajax_member_service.php',
					type:'POST',
					data: {functionName:'loginMemberService',is_sub:is_sub,username:username},
					success: function(data){
						if(data == 1){
							Materialize.toast("Invalid credentials",2000,"red lighten-2");

							con.attr('disabled',false);
							con.text(oldval);
						} else if (data == 2){
							Materialize.toast("You are not a member",2000,"red lighten-2");
							con.attr('disabled',false);
							con.text(oldval);
						}  else if (data == 3){
							Materialize.toast("You are not enrolled to any service",2000,"red lighten-2");
							con.attr('disabled',false);
							con.text(oldval);
						} else if (data == 4){
							Materialize.toast("Your subscription was expired.",2000,"red lighten-2");
							con.attr('disabled',false);
							con.text(oldval);
						} else if (data == 5){
							Materialize.toast("You have successfully signed in.",2000,"green lighten-2",function(){
								location.href = "service-login.php";
							});

						} else if (data == 6){
							Materialize.toast("You are already signed in.",2000,"red lighten-2");
							con.attr('disabled',false);
							con.text(oldval);
						} else {
							data = JSON.parse(data);
							var d = data.data;
							var member_id = data.member_id;
							try{

								var ret = "";
								for(var i in d){
									ret += "<tr><td><input id='chk_"+d[i].id+"' value='"+d[i].id+"' type='checkbox' class='chkServices'><label for='chk_"+d[i].id+"'></label></td><td>"+d[i].item_code+"</td><td>"+d[i].consumable_qty+"</td><td>"+timeConverter(d[i].end_date)+"</td></tr>"
								}
								$('#tbl_services tbody').html(ret);
								$('#member_id').val(member_id);
								$('#modal1').modal("open");
								con.attr('disabled',false);
								con.text(oldval);
							}catch(e){
								console.log(e);
							}

						}
						$('#username').val('');
						$('#password').val('');
					},
					error:function(){
						Materialize.toast("Failed request. Please try again.",2000,"red lighten-2",function(){
							location.href = "service-login.php";
						});
					}
				});
			} else {
				Materialize.toast("Please complete the form.",2000,"red lighten-2");
				con.attr('disabled',false);
				con.text(oldval);
			}


		});

		$('body').on('click','#btnSubmit',function(){
			var to_deduct = [];
			var member_id = $('#member_id').val();
			var con = $(this);
			var oldval = con.html();
			con.attr('disabled',true);
			con.html('Loading...');
			$('.chkServices').each(function(){
				var con = $(this);
				if(con.is(":checked")){
					to_deduct.push(con.val());
				}
			});
			if(!to_deduct){
				Materialize.toast("Please choose a service",2000,"red lighten-2");
				con.attr('disabled',false);
				con.html(oldval);
			} else {
				$.ajax({
				    url:'../ajax/ajax_member_service.php',
				    type:'POST',
				    data: {functionName:'deductConsumables',to_deduct: JSON.stringify(to_deduct),member_id:member_id},
				    success: function(data){
					    if(data == 1){
						    Materialize.toast("Invalid Data.",2000,"red lighten-2");
						    con.attr('disabled',false);
						    con.html(oldval);
					    } else if (data == 2){
						    Materialize.toast("You have successfully signed in.",2000,"green lighten-2",function(){
							    location.href = "service-login.php";
						    });
					    }
				    },
				    error:function(){

				    }
				})
			}

		});
	});

</script>
</body>
</html>
