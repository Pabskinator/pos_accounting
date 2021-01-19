<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>File Manager</title>
	<link rel="stylesheet" href="../css/bootstrap.css">
	<link rel="stylesheet" href="../css/dropzone2.css">
	<link rel="stylesheet" href="../css/select2.css">
	<link rel="stylesheet" href="../css/select2_bootstrap.css">
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

		#dropzone_form {
			border:none;
			min-height: 35vh;
		}
		.dz-message {
			font-size: 30px;
		}

		#dzone{

			width:70%;
			margin: 0 auto;
			padding: 0px;
			-webkit-box-shadow: 0px 0px 5px 1px rgba(119,119,119,1);
			-moz-box-shadow: 0px 0px 5px 1px rgba(119,119,119,1);
			box-shadow: 0px 0px 5px 1px rgba(119,119,119,1);
			margin-top:12vh;
			min-height: 35vh;
		}

		.dzone-header{
			background-color: #20bf6b;
			height: 100%;
			color:#fff;
			font-size: 35px;
			padding: 10px;
			text-align: center;

		}
		#nav-head{
			position: fixed;
			height: 15vh;
			width:100%;
			background: #26de81;
			top:0px;
			left:0px;
			z-index: -1;

		}
		#nav-head-main{
			float:left;
			font-size: 24px;
			color:#fff;
			margin-left: 20px;
			margin-top: 20px;
			z-index: 999;
		}

		#nav-head-list{
			float:right;
			font-size: 24px;
			color:#fff;
			margin-top: 20px;
			margin-right: 20px;
			z-index: 999;
		}

		#nav-head-main  a {
			color:#fff;

		}

		#nav-head-list  a {
			color:#fff;
		}
		a:hover{
			text-decoration: none;
		}
		#back{
			position: fixed;
			bottom: 10px;
			left: 10px;
			color: #222;
		}
		.truncate {
			width: 100%;
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
		}

	</style>
</head>
<body>
<div id='app'>
	<div id='back' v-show="container.upload"><a href="main.php">Back to POS</a></div>
	<div v-show="container.upload && !view_only">
		<div id='nav-head-main'>
			File Manager
		</div>
		<div id='nav-head-list'>
			<a href="#" @click="showContainer(2)">Uploaded Files</a>
		</div>
		<div id='nav-head'>

		</div>
		<div class='container'>
			<div id='dzone'>
				<div class="dzone-header">
					<span class='glyphicon glyphicon-cloud'></span> UPLOAD
				</div>
				<div class="dzone-body">
					<p style='margin-left: 5px;'>Total Files Size: <strong>{{total_file_size}} MB</strong> Total Upload Limit: <strong>{{ total_limit }} MB</strong> Remaining: <strong>{{ total_limit - total_file_size}} MB</strong></p>
					<p style='margin-left: 5px;'>Supported File Types: <span class='label label-primary'>.jpg</span> <span class='label label-primary'>.jpeg</span> <span class='label label-primary'>.png</span> <span class='label label-primary'>.bmp</span>
					<span class='label label-primary'>.pdf</span> <span class='label label-primary'>.xls</span> <span class='label label-primary'>.xlsx</span>  <span class='label label-primary'>.doc</span>  <span class='label label-primary'>.docx</span></p>

					<div class="row"  style='margin: 5px;'>
						<div class="col-md-6">
							<div class="form-group">
								<input type="text"  v-model='description' class='form-control' placeholder='Description'>
								<span class='help-block'>Add Title/Description before uploading files.</span>
							</div>
						</div>
						<div class="col-md-6">

								<select  v-model='position_id' name="position_id" id="position_id" class='form-control'  multiple="multiple" >
									<option value=""></option>
									<option v-for="position in positions" v-bind:value='position.id'> {{position.position}}</option>
								</select>

							<span class='help-block'>Select Position</span>
						</div>
						<div class="col-md-12">
							<div class="input-group">
								<select  v-model='user_ids' name="user_id" id="user_id" class='form-control'  multiple="multiple" >
									<option value=""></option>
									<option v-for="user in users" v-bind:value='user.id'> {{user.name}}</option>
								</select>
							      <span class="input-group-btn">
							        <button class="btn btn-default" type="button" @click="clearForm()">Clear</button>
							      </span>
							</div><!-- /input-group -->
							<span class='help-block'>Leave it blank if for all users</span>
						</div>
					</div>


					<form v-show="total_file_size < total_limit" action="/file-upload"  id='dropzone_form' class="dropzone">
						<div class="fallback">
							<input name="file" type="file" multiple />
						</div>
						<div class="dz-message">
								<span class='glyphicon glyphicon-upload'></span>
							Click Here or Drop Files to Upload
						</div>
					</form>

					<div v-show="total_file_size > total_limit" class='alert alert-danger'>
						You have reached your limit. Please <a href="#" @click.prevent="showContainer(2)">delete</a> old files to allocate space.
					</div>
				</div>
			</div>
		</div>
	</div> <!-- UPLOAD CONTAINER -->

	<div v-show="container.list">
		<nav class="navbar navbar-default">
			<div class="container-fluid">
				<!-- Brand and toggle get grouped for better mobile display -->
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="#">File Manager</a>
				</div>

				<!-- Collect the nav links, forms, and other content for toggling -->
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav navbar-right">
						<li><a href="#" @click="showContainer(3)" v-show="!container.upload && !view_only">Log</a></li>
						<li><a href="#" @click="showContainer(2)" v-show="!container.upload && !view_only">Uploaded List</a></li>
						<li><a href="#" @click="showContainer(1)" v-show="!container.upload">Upload New File</a></li>
						<li><a href="main.php">Back to POS</a></li>
					</ul>
				</div><!-- /.navbar-collapse -->
			</div><!-- /.container-fluid -->
		</nav>

		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<input type="text" v-model='search' class='form-control' placeholder='Search...'>

				</div>
			</div>
			<br>
			<paginate
				name="files"
				:list="computedFiles"
				:per="18"


				>
			<div class="row" v-show="computedFiles.length">


				<div class="col-sm-4 col-md-2" v-for="file in  paginated('files')">
					<div class="thumbnail">

							<div class="caption truncate">
								<a @click.prevent="showThisFile(file)">
								<strong v-bind:title="file.title"> 	<img v-bind:src="file.src_type" style='height:20px;' alt="..."> {{ file.title }}</strong>
								<small class='help-block truncate' v-bind:title="file.description">{{file.description}}</small>
								<small class='help-block'>{{file.created}} - {{file.size }}</small>
								</a>
								<div class="row">
									<div class="col-md-6">
										<i class='glyphicon glyphicon-user' v-bind:title="file.users"></i>
									</div>
									<div class="col-md-6">
										<div class='text-right'><a href="#" @click.prevent="deleteFile(file)" class='text-danger'><i class='glyphicon glyphicon-remove'></i></a></div>
									</div>
								</div>
							</div>

					</div>
				</div>

			</div>
			</paginate>
			<div class="text-center">
			<paginate-links for="files"
			                :classes="{
	                                        'ul': 'pagination',}
	                                        "
			                :limit="18"
			                :show-step-links="true"
				></paginate-links>
			</div>
			<div class="alert alert-info" v-show="!computedFiles.length">No Files Uploaded</div>
		</div>
	</div> <!-- END LIST VIEW -->

	<div v-show="container.log">
		<nav class="navbar navbar-default">
			<div class="container-fluid">
				<!-- Brand and toggle get grouped for better mobile display -->
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="#">File Manager</a>
				</div>

				<!-- Collect the nav links, forms, and other content for toggling -->
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav navbar-right">
						<li><a href="#" @click="showContainer(3)" v-show="!container.upload && !view_only">Log</a></li>
						<li><a href="#" @click="showContainer(2)" v-show="!container.upload && !view_only">Uploaded List</a></li>
						<li><a href="#" @click="showContainer(1)" v-show="!container.upload">Upload New File</a></li>
						<li><a href="main.php">Back to POS</a></li>
					</ul>
				</div><!-- /.navbar-collapse -->
			</div><!-- /.container-fluid -->
		</nav>

		<div class="container">
			<input type="hidden" id="hiddenpage" />
			<div id="holder"></div>
		</div>
	</div> <!-- END LIST VIEW -->
</div>
<script src='../js/jquery.js'></script>
<script src='../js/select2.js'></script>
<script src='../js/dropzone2.js'></script>
<script src='../js/vue3.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/vue-paginate/3.6.0/vue-paginate.min.js'></script>
<script>
	Dropzone.autoDiscover = false;
	var vm = new Vue({
		el: '#app',
		data:{
			dp: {},
			view_only: true,
			description: '',
			user_ids:[],
			container: { upload:false, list:false, log:false },
			paginate: ['files'],
			files: [
				{ id:0,title: 'Title 1', src:'../css/img/reg_background.jpg', src_type:'../css/img/icon-image.png', size:1},
				{ id:0,title: 'Title 2', src:'../css/img/reg_background.jpg', src_type:'../css/img/icon-pdf.png', size:1},
				{ id:0,title: 'Title 3', src:'../css/img/reg_background.jpg', src_type:'../css/img/icon-image.png', size:1},
				{id:0,title: 'Title 4', src:'../css/img/reg_background.jpg', src_type:'../css/img/icon-image.png', size:1},
				{ id:0,title: 'Title 1', src:'../css/img/reg_background.jpg', src_type:'../css/img/icon-pdf.png' , size:1},
				{ id:0,title: 'Title 2', src:'../css/img/reg_background.jpg', src_type:'../css/img/icon-excel.png', size:1},
				{ id:0,title: 'Title 3', src:'../css/img/reg_background.jpg', src_type:'../css/img/icon-excel.png', size:1},
				{ id:0,title: 'Title 4', src:'../css/img/reg_background.jpg', src_type:'../css/img/icon-image.png', size:1}

			],
			search:'',
			total_file_size:0,
			total_limit: 100,
			users: [],
			positions:[],
			position_id:[],
		},
		computed: {
			computedFiles: function(){
				var self = this;
				return self.files.filter(function(f){
					if(self.search){
						var title = f.title.toLowerCase();

						if(title.indexOf(self.search.toLowerCase()) > -1){
							return true;
						} else {
							return false;
						}
					}
					return true;

				});

			},
		},
		mounted: function(){
			var self = this;


			self.dp = new Dropzone("#dropzone_form", {
				url: "../ajax/ajax_product.php?functionName=uploadFiles",
				acceptedFiles: ".jpg,.jpeg,.png,.bmp,.pdf,.xls,.xlsx,.doc,.docx",
				maxFilesize: 30, // MB
			});
			self.dp.on('sending', function(file, xhr, formData){
				formData.append('description', self.description);
				formData.append('user_ids', JSON.stringify(self.user_ids));

			});

			self.dp.on("queuecomplete", function (file) {
				self.getFiles();
			});

			self.getPermission();

			if(self.view_only){
				self.showContainer(2);
			} else {
				self.showContainer(1);
			}
		

			$('#user_id').select2({placeholder: 'Target User' ,allowClear: true});

			$('#position_id').select2({placeholder: 'Target Positions' ,allowClear: true});

			$("#user_id").change(function(){
				self.user_ids = $("#user_id").val();
			});
			$("#position_id").change(function(){
				self.position_id = $("#position_id").val();
				self.appendUsers();
			});

			$('body').on('click','.paging',function(){
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				self.getPage(page);
			});

		},
		methods:{
			clearForm: function(){
				var self = this;
				$('#user_id').select2('val',[]);
				self.user_ids = [];
				$('#position_id').select2('val',[]);
				self.position_id = [];
				self.description = '';
			},
			appendUsers: function(){
				var self = this;
				if(self.position_id){
					for(var i in self.position_id){
						var user_in_position = self.getUserByPosition(self.position_id[i]);
						for(var j in user_in_position){
							var checker = self.isUserExists(user_in_position[j].id);
							if(!checker){
								self.user_ids.push(user_in_position[j].id);
							}
						}
					}
					$('#user_id').select2('val',self.user_ids);
				}

			},
			isUserExists: function(u){
					var self = this;
				var r = false;
				for(var i in self.user_ids){
					if(self.user_ids[i] == u){
						r= true;
					}
				}
				return r;
			},
			getUserByPosition: function(po_id){
				var users = [];
				var self = this;
				if(po_id){
					for(var i in self.users){
						if(self.users[i].position_id == po_id){
							users.push(self.users[i]);
						}
					}
					return users
				}

			},
			getPage: function(p){

				var search = 'View File';
				var user_id = '';
				$('#holder').html('Loading...')
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					data:{page:p,functionName:'logList',user_id:user_id,search:search,cid: 1},
					success: function(data){

						$('#holder').html(data);

					},
					error: function(){
						alert('Something went wrong. The page will be refresh.');
						location.href='logs.php';

					}
				});

			},
			showThisFile: function(f){
				window.open(f.src, '_blank');
				$.ajax({
				    url:'../ajax/ajax_product.php',
				    type:'POST',
				    data: {functionName:'logUser',file_info:JSON.stringify(f)},
				    success: function(data){

				    },
				    error:function(){

				    }
				});

			},
			deleteFile: function(file){
				var self = this;
				if(confirm("Are you sure you want to delete this file?")){
					$.ajax({
						url:'../ajax/ajax_product.php',
						type:'POST',
						data: {functionName:'deleteFileManager',id:file.id},
						success: function(data){
							self.getFiles();
							alert(data);

						},
						error:function(){

						}
					});
				}
			},
			getPermission: function(){
				var self = this;
				$.ajax({
				    url:'../ajax/ajax_product.php',
				    type:'POST',
				    data: {functionName:'fileManagerPermission'},
				    success: function(data){
					    if(data == 0){
						    self.view_only = true;
					    } else if (data == 1){
						    self.view_only = false;
					    }

				    },
				    error:function(){

				    }
				})
			},
			showContainer: function(c){
				var self = this;
				self.container = { upload:false, list:false };
				if( c == 1 ){
					self.container = { upload : true, list: false, log:false };
					self.dp.removeAllFiles();
				} else if ( c == 2 ){
					self.container = { upload : false, list: true, log:false  };
					self.getFiles();
				}else if ( c == 3 ){
					self.container = { upload : false, list: false , log:true };
					self.getPage(0);
				}
			},
			getFiles: function(){
				var self = this;
				$.ajax({
				    url:'../ajax/ajax_product.php',
				    type:'POST',
					dataType:'json',
				    data: {functionName:'getFiles'},
				    success: function(data){
					    self.files = data.files;
					    self.users = data.users;
					    self.positions = data.positions;
					    self.total_file_size = data.total_file_size;
				    },
				    error:function(){

				    }
				});
			},
		}
	});


</script>
</body>
</html>