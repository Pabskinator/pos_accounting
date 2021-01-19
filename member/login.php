<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<style>
		#mem-con-bg-overlay{
			position: fixed;
			top: 0;
			left: 0;
			z-index: 100;
			width: 100%;
			height: 100%;
			background: rgba(0,0,0,.5);
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
			background-image:url(../css/img/reg_background.jpg);
		}

		#member-container-new{
			width:350px;
			height: 350px;
			z-index: 102;
			position: absolute;
			top: 15%;
			overflow-y: auto;
		}
		@media only screen and (max-width: 800px) {
			#member-container-new{
				width:95%;

				z-index: 102;
				position: fixed;
				margin:0 auto;
				top: 15%;

			}
		}
		#member-container-new img {
			width: 40px;
		}

	</style>
</head>
<body>
<div id='app'>
	<div   id='mem-con-bg-overlay'></div>
	<div   id='mem-con-bg'></div>
	<div  >
		<div class="row">
			<div class="col s12 m8 red-text" style=''>
				<br><br>
				Safehouse Fight Academy
			</div>
			<div class="col s12 m4">
				<div id="member-container-new" class='grey lighten-3' >
					<br>
					<h5 class='center-align'>Enter Your Credentials</h5>

					<div class="row">
						<form class="col s12">
							<div class="row">
								<div class="input-field col s12">
									<i class="material-icons prefix">account_circle</i>
									<input id="icon_prefix" type="text" class="validate" v-model='username'>
									<label for="icon_prefix">First Name</label>
								</div>
								<div class="input-field col s12">
									<i class="material-icons prefix">lock</i>
									<input id="icon_telephone" type="password" @keyup="logme($event)" class="validate" v-model='password'>
									<label for="icon_telephone">Password</label>
								</div>
								<div class="input-field col s12 center-align">
									<a class="waves-effect waves-light btn red" id='btnLogin' @click="login">Login</a>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>


	</div>
</div>
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js" ></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.4.4/vue.min.js"></script>
<script>
	var vm = new Vue({
		el: '#app',
		data: {
			username:'',
			password:''
		},
		mounted: function(){

		},
		methods: {
			login: function(){
				if(this.username && this.password){
					var  con = $('#btnLogin');
					con.html('Loading...');
					con.attr('disabled',true);
					$.ajax({
						url:'service/service.php',
						type:'POST',
						data: {functionName:'login',username:this.username, password:this.password},
						success: function(data){
							if(data == '1'){
								location.href='members.php';

							} else {
								Materialize.toast("Invalid Credentials",2000,"red lighten-2");
								con.html('Login');
								con.attr('disabled',false);
							}
						},
						error:function(){

						}
					});
				} else {
					Materialize.toast("Please complete the form.",2000,"red lighten-2");
				}
			},
			logme: function(e){
				if (e.keyCode === 13) {
					this.login();
				}
			}
		}
	});
</script>
</body>
</html>