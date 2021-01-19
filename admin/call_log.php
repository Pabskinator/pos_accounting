<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';


	$user_list = $user->get_active('users',['1','=','1']);


?>
	<link rel="stylesheet" href="../css/dropzone2.css">

	<style>
		.responsive-img{
			height: 40vh !important;
			cursor: pointer;
		}
		#nav-container{
			margin-top: 5%;
		}
		.thumbnail:hover{
			border: 1px solid #ff0000;
		}
		.dropzone{
			min-height: 100px !important;
		}

	</style>

	<!-- Page content -->
	<div id="page-content-wrapper">


		<input type="hidden" value='<?php echo $user->data()->id; ?>' id='current_user_id'>
		<input type="hidden" value='<?php echo ucwords($user->data()->firstname . " " . $user->data()->lastname); ?>' id='current_user_name'>

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset" id='call-log-app'>
		<div class="content-header"  v-show="!container.main" >
			<h1 class='text-center'>
				<button class='btn btn-default'@click="showContainer(1)"><i class='fa fa-long-arrow-left fa-2x'></i></button>

			</h1>
		</div>
		<h1 class='text-center' v-show="container.main">Phone Call Monitoring</h1>
		<div id='nav-container' v-show="container.main">
			<div class="row">
				<div class="col-xs-12 col-sm-4">
					<div class='thumbnail' @click="showContainer(2)">
						<img src="../css/img/incoming.png" class='responsive-img' alt="">
						<h5 class='text-center'>Incoming</h5>
					</div>
				</div>
				<div class="col-xs-12 col-sm-4">
					<div class='thumbnail' @click="showContainer(3)">
					<img src="../css/img/log.png"  class='responsive-img'  alt="">
						<h5 class='text-center'>Log</h5>
						</div>
				</div>
				<div class="col-xs-12 col-sm-4">
					<div class='thumbnail' @click="showContainer(4)">
					<img src="../css/img/outgoing.png"  class='responsive-img'  alt="">
						<h5 class='text-center'>Outgoing</h5>
						</div>
				</div>
			</div>
		</div>
		<div v-show="container.incoming">
			<div class="row">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-body">
							<div>


								<h5>INCOMING</h5>
								<div class="form-group">
									<strong>Number: </strong> <input v-model="form_incoming.number" type="text" class='form-control'>
								</div>
								<div class="form-group">
									<strong>Person Calling: </strong> <input v-model="form_incoming.person_calling" type="text" class='form-control'>
								</div>

								<div class='form-group'>
									<strong>Answered by: </strong>
									<input id='answered_by' class='form-control' v-model="form_incoming.answered_by">
								</div>

								<div class="form-group">
									<strong>Technician:</strong>
									<input v-model="form_incoming.technician"  id='technician' class='form-control' />
								</div>

								<div class="form-group">
									<strong>Remarks:</strong>
									<textarea cols="30" rows="4" v-model="form_incoming.remarks" class='form-control'></textarea>
								</div>

								<div class='form-group'>
									<form action="/target" class="dropzone" id="my-dropzone">
										<div class="dz-message" data-dz-message><span>Drop Files Here or Click to Upload</span></div>
									</form>
								</div>
							</div>
							<div class="form-group">
								<button class='btn btn-primary' @click="submitIncoming">Submit</button>
							</div>
						</div>
					</div>


				</div>
				<div class="col-md-3"></div>
			</div>
		</div>
		<div v-show="container.log">

			<div class="container">
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
					<input type="text" class='form-control' v-model.lazy="search" placeholder="Search....">
						<span class='help-block'>Search Record</span>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<select class='form-control'  v-model="type"  @change="getLog">
							<option value="1">Incoming</option>
							<option value="2">Outgoing</option>
						</select>
						<span class='help-block'>Choose Type</span>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<input type="text" class='form-control' id='dt_from' placeholder="From" autocomplete="off" v-model="dt_from" >
						<span class='help-block'>Date from</span>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<input type="text" class='form-control'  id='dt_to'  placeholder="To" autocomplete="off"  v-model="dt_to" >
						<span class='help-block'>Date To</span>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel panel-body">
					<div id="con"></div>
				</div>
			</div>
			</div>
		</div>
		<div v-show="container.outgoing">
			<div class="row">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-body">
							<div>

								<h5>OUTGOING</h5>

								<div class="form-group">
									<strong>Number: </strong> <input v-model="form_outgoing.number" type="text" class='form-control'>
								</div>
								<div class="form-group">
									<strong>Contact: </strong> <input v-model="form_outgoing.person_calling" type="text" class='form-control'>
								</div>
								<div class='form-group'>
									<strong>Caller: </strong>
									<input id='answered_by2' class='form-control' v-model="form_outgoing.answered_by">

								</div>
								<div class='form-group'>
									<strong>Technician: </strong>
									<input class='form-control' v-model="form_outgoing.technician">

								</div>
								<div class="form-group">
									<strong>Remarks:</strong>
									<textarea cols="30" rows="4" v-model="form_outgoing.remarks" class='form-control'></textarea>
								</div>
								<div class='form-group'>
									<form action="/target" class="dropzone" id="my-dropzone2">
										<div class="dz-message" data-dz-message><span>Drop Files Here or Click to Upload</span></div>
									</form>
								</div>
							</div>
							<div class="form-group">
								<button class='btn btn-primary' @click="submitOutgoing">Submit</button>
							</div>
						</div>
					</div>


				</div>
				<div class="col-md-3"></div>
			</div>
			<!-- end out going -->
		</div>

		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id='mtitle'>Enter Date</h4>
						</div>
						<div class="modal-body" id='mbody'>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<input type="hidden" id='close_time_id'>
										<input type="text" class='form-control' placeholder='Date' id='update_close_time'>
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group">
										<button class='btn btn-default btnDateSubmit'>Submit</button>
									</div>
								</div>
							</div>
						</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->

		<div class="modal fade" id="myModalRemarks" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id='rtitle'></h4>
						</div>
						<div class="modal-body" id='rbody'>
							<div>
								<div class="form-group">
									<input type="text" class='form-control' v-model='remarks' placeholder='Enter Progress Remarks'>
								</div>
								<div class="form-group">
									<button class='btn btn-primary' @click="addRemarks">Save</button>
								</div>
							</div>
							<hr>
							<div v-show="remarks_list.length">
								<table class="table">
									<thead>
										<tr>
											<th>Remarks</th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="rem in remarks_list">
											<td>
												<span class='span-block'>
													<strong>{{ rem.fullname }}</strong>
												</span>
												{{ rem.remarks }}
												<small class='help-block'>
													{{ rem.date }}
												</small>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							<div v-show="!remarks_list.length">
								<div class='alert alert-info'>No remarks.</div>
							</div>
						</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->


	</div> <!-- end page content wrapper-->
		<script src='../js/vue3.js'></script>
		<script src='../js/dropzone2.js'></script>
	<script>

		var vm = new Vue({
			el:'#call-log-app',
			data: {
				current_user_id: '',
				current_user_name:'',
				container: { main: true, incoming: false, log:false, outgoing: false },
				myDropzone: {},
				myDropzone2: {},
				search:'',
				dt_from:'',
				dt_to:'',
				type:'1',
				form_incoming:{number:'',person_calling:'',remarks:'',answered_by:'',technician:''},
				form_outgoing:{number:'',person_calling:'',remarks:'',answered_by:'',technician:''},
				current_page:1,
				timer: null,
				current_id:'',
				remarks:'',
				remarks_list:[],
				ajax_running: false,
			},
			mounted: function(){
				var self = this;

				Dropzone.autoDiscover = false;

				self.myDropzone = new Dropzone("#my-dropzone", {
					url: "../ajax/ajax_call_log_upload.php",
					autoProcessQueue: false,
					parallelUploads: 10,
				});
				self.myDropzone2 = new Dropzone("#my-dropzone2", {
					url: "../ajax/ajax_call_log_upload.php",
					autoProcessQueue: false,
					parallelUploads: 10,
				});

				self.current_user_id = $('#current_user_id').val();
				self.current_user_name = $('#current_user_name').val();






	/*
				answered_by.select2('val',self.current_user_id);
				self.form_incoming.answered_by = self.current_user_id;

				answered_by2.select2('val',self.current_user_id);
				self.form_outgoing.answered_by = self.current_user_id;


				 */

				$('body').on('click','.paging',function(){
					var page = $(this).attr('page');
					self.current_page = page;
					self.getLog();

				});
				$('body').on('click','.btnShowImage',function(){
					var con = $(this);

				});

				$('#dt_from').datepicker({
					autoclose:true
				}).on('changeDate', function(ev){
					$('#dt_from').datepicker('hide');
					self.dt_from = $(this).val();
					self.checkDate();
				});

				$('#dt_to').datepicker({
					autoclose:true
				}).on('changeDate', function(ev){
					$('#dt_to').datepicker('hide');
					self.dt_to = $(this).val();
					self.checkDate();
				});

				$('body').on('click','.btnDate',function(){
					$('#myModal').modal('show');
					$('#update_close_time').val('');
					$('#close_time_id').val($(this).attr('data-id'));
				});
				$('body').on('click','.btnDateSubmit',function(){
					var update_close_time = $('#update_close_time').val();
					var id = $('#close_time_id').val();
					self.updateCloseTime(id,update_close_time)
					$('#myModal').modal('hide');
				});
				$('#update_close_time').datepicker({
					autoclose:true
				}).on('changeDate', function(ev){
					$('#update_close_time').datepicker('hide');
				});
				
				$('body').on('click','.btnRemarks',function(){
					var id = $(this).attr('data-id');
					self.current_id = id;
					self.getRemarks();
				});

			},
			methods: {
				addRemarks: function(){
					var self = this;
					$.ajax({
					    url:'../ajax/ajax_call_log.php',
					    type:'POST',
					    data: {functionName:'addRemarks',id:self.current_id,remarks:self.remarks},
					    success: function(data){
						    self.getRemarks();
						    self.remarks = '';
					    },
					    error:function(){
					        
					    }
					})
				},
				getRemarks: function(){

					    var self = this;
						$('#myModalRemarks').modal('show');

						$.ajax({
						    url:'../ajax/ajax_call_log.php',
						    type:'POST',
							dataType:'json',
						    data: {functionName:'getRemarks',id:self.current_id},
						    success: function(data){
							    self.remarks_list = data;

						    },
						    error:function(){

						    }
						});

				},
				updateCloseTime: function(id,update_close_time){
					var self = this;
					$.ajax({
					    url:'../ajax/ajax_call_log.php',
					    type:'POST',
					    data: {functionName:'updateCloseTime',id:id,update_close_time:update_close_time},
					    success: function(data){
							self.getLog();
						    tempToast('info',data,'Info');
					    },
					    error:function(){

					    }
					});

				},
				checkDate: function(){

					var self = this;
					if(self.dt_from && self.dt_to ){
						self.getLog();
					}

				},
				showContainer: function(c){
					var self = this;
					self.container = { main: false, incoming: false, log:false, outgoing: false };
					if(c == 1){
						self.container.main = true;
					} else if ( c == 2){
						self.container.incoming = true;
					} else if ( c == 3){
						self.container.log = true;
						self.getLog();
					} else if ( c == 4){
						self.container.outgoing = true;
					}
				},
				submitOutgoing: function(){
					var self = this
					if(self.form_outgoing.number && self.form_outgoing.person_calling && self.form_outgoing.remarks && self.form_outgoing.answered_by){

					} else {
						tempToast('error','Please complete the form.','Warning');
						return;
					}
					$.ajax({
						url: '../ajax/ajax_call_log.php',
						type: 'POST',
						data: {functionName:'addLog', data: JSON.stringify(self.form_outgoing),type:'2'},
						success: function(data) {
							console.log(data);
							if(data && self.myDropzone2.files.length){
								self.myDropzone2.on("sending", function(file, xhr, formData) {
									formData.append("id", data); // id
									formData.append("functionName", 'uploadPic'); // id
								});
								self.myDropzone2.processQueue();
								self.myDropzone2.on("queuecomplete", function (file) {
									self.resetIncoming();
									tempToast('info','Log inserted successfully.','Info');
								});

							} else {
								//success
								tempToast('info','Log inserted successfully.','Info');
								self.resetIncoming();
							}

						},
						error: function(data) {

						}
					});
				},
				submitIncoming: function(){
					var self = this;
					//{number:'',person_calling:'',remarks:'',answered_by:''},
					var form_data =  null;
					if(self.ajax_running){
						return;
					}
					self.ajax_running = true;
					console.log("triggered");
					if(self.form_incoming.number && self.form_incoming.person_calling && self.form_incoming.remarks && self.form_incoming.answered_by){

						form_data = self.form_incoming;
						self.form_incoming = { number:'', person_calling:'', remarks:'', answered_by:'', technician:''};

					} else {
						tempToast('error','Please complete the form.','Warning');
						return;
					}
					$.ajax({
						url: '../ajax/ajax_call_log.php',
						type: 'POST',
						data: {functionName:'addLog', data: JSON.stringify(form_data),type:'1'},
						success: function(data) {

							if(data && self.myDropzone.files.length){

								self.myDropzone.on("sending", function(file, xhr, formData) {
									formData.append("id", data); // id
									formData.append("functionName", 'uploadPic'); // id
								});

								self.myDropzone.processQueue();
								self.myDropzone.on("queuecomplete", function (file) {
									self.resetIncoming();
									tempToast('info','Log inserted successfully.','Info');
									self.ajax_running = false;
								});

							} else {
								//success
								tempToast('info','Log inserted successfully.','Info');
								self.ajax_running = false;
							}

						},
						error: function(data) {

						}
					});
				},
				resetIncoming: function(){
					var self = this;

					self.form_outgoing = {number:'',person_calling:'',remarks:'',answered_by:'',technician:''};

					self.myDropzone.removeAllFiles(true);
					self.myDropzone2.removeAllFiles(true);



					$(".dz-message").removeClass("hidden");
				},
				getLog: function(){
					var self = this;

					$.ajax({
					    url:'../ajax/ajax_call_log.php',
					    type:'POST',
					    data: {functionName:'getLog',current_page:self.current_page, search: self.search,type:self.type,dt_from: self.dt_from,dt_to: self.dt_to},
					    success: function(data){
							$('#con').html(data)
					    },
					    error:function(){

					    }
					});
				}
			}
		});





	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>