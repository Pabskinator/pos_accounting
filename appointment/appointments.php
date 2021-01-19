<?php include_once 'includes/page_head.php';  ?>	<br>

	<div id='app'>
		<div class="container">
		<?php makeBreadCrumb('Appointments') ?>

		<h3>Appointments</h3>
		</div>
		<div class="container">
			<div class="btn-group mb-2" role="group" aria-label="Basic example">
				<button type="button" @click="showContainer(2)" class="btn btn-secondary">Weekly View</button>
				<button type="button" @click="showContainer(1)" class="btn btn-secondary">List View</button>
				<button type="button" @click="showContainer(3)" class="btn btn-secondary">Upcoming</button>
			</div>
		<div v-show="view.list">
			<div class="row">

				<div class="col-md-3">
					<div class="form-group">
						<input name="branch_id" class='form-control'  id="branch_id" v-model='filter.branch_id'>

					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<select name="doctor_id" class='form-control'  id="doctor_id" v-model='filter.doctor_id'>
							<option value=""></option>
							<option v-for="doc in doctors" v-bind:value="doc.id">{{doc.name}}</option>
						</select>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<select name="status" id="status"  @change="getAppointments()" class='form-control' v-model='filter.status'>
							<option value="0">All</option>
							<option value="1">Pending</option>
							<option value="2">Confirm</option>
							<option value="3">Reconfirm</option>
							<option value="4">Done</option>
							<option value="5">Cancelled</option>
						</select>
					</div>
				</div>
			</div>
			<table class='table table-bordered table-striped'>
				<thead>
				<tr>
					<th>ID</th>
					<th>Branch</th>
					<th>Doctor</th>
					<th>Client</th>
					<th>Type</th>
					<th>Date</th>
					<th>Status</th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<tr v-for="ap in appointments">
					<td>{{ ap.id }}</td>
					<td>{{ ap.branch_name }}</td>
					<td>{{ ap.doctor_name }}</td>
					<td>{{ ap.member_name }}</td>
					<td>{{ ap.type_name }}</td>
					<td>
						{{ ap.desired_date }}
						<br>
						{{ ap.desired_time }}
					</td>
					<td>{{ statuses[ap.status] }}</td>
					<td>
						<button v-show="ap.status==1" class='btn btn-primary btn-sm' @click="changeStatus(ap,2)">Confirm</button>
						<button v-show="ap.status==2" class='btn btn-primary btn-sm' @click="changeStatus(ap,3)">Reconfirm</button>
						<button v-show="ap.status==3" class='btn btn-primary btn-sm' @click="changeStatus(ap,4)">Finish</button>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
		<div v-show="view.weekly">
				<div class=''>
					<div class="row">
						<div class="col-md-9"></div>
						<div class="col-md-3">
							<div class="form-group">
								<input name="branch_id" class='form-control'  id="branch_id2" v-model='filter.branch_id'>

							</div>
						</div>
					</div>
					<div style='overflow-x: auto;height:70vh;overflow-y: auto' class='style-2'>
				<table class='table table-bordered'>
					<thead class="thead-dark">
						<tr>
							<th>Doctor</th>
							<th v-for="day in days">{{day}}</th>
						</tr>
					</thead>
					<tbody>
					  <tr v-for="a in appointments" >
							<td>{{a.doctor_name}}</td>
						    <td v-for="i in [1,2,3,4,5,6,7]">
							    <span v-html="a[i]"></span>
						    </td>
					  </tr>
					</tbody>
				</table>
					</div>
					<br>
					</div>

		</div>
			<div v-show="view.upcoming">
				<div class="row">
					<div class="col-md-9"></div>
					<div class="col-md-3">
						<div class="form-group">
							<input name="branch_id_up" class='form-control'  id="branch_id_up" v-model='branch_id_up'>
						</div>
					</div>
				</div>
				<p >Branch: <strong>{{branch_name}}</strong></p>
				<table class='table table-bordered table-striped'>
					<thead>
					<tr>
						<th>ID</th>

						<th>Doctor</th>
						<th>Client</th>
						<th>Type</th>
						<th>Date</th>
						<th>Status</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<tr v-for="ap in upcoming_list">
						<td>{{ ap.id }}</td>

						<td>{{ ap.doctor_name }}</td>
						<td>{{ ap.member_name }}</td>
						<td>{{ ap.type_name }}</td>
						<td>{{ ap.desired_date }}<br> {{ap.desired_time}}</td>
						<td>{{ statuses[ap.status] }}</td>
						<td></td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<script src='../js/vue3.js'></script>
	<script>
		vm = new Vue({
			el:'#app',
			data: {
				filter:{doctor_id:'',status:'0',branch_id:''},
				filter2:{branch_id:''},
				doctors:[],
				types:[],
				upcoming_list:[],
				appointments:[],
				branch_id_up:'',
				branch_name:'',
				statuses:['','Pending','Confirmed','Reconfirmed','Done','Cancelled'],
				view:{list:false,weekly: true, upcoming:false},
				days:['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'],
			},
			mounted: function(){
				var self = this;

				self.getDoctors();
				self.showContainer(2);

				$('#doctor_id').select2({placeholder: 'Select Doctor' ,allowClear: true});


				$('#doctor_id').change(function(){
					self.filter.doctor_id = $(this).val();
					self.getAppointments();
				});

				$('#branch_id,#branch_id2,#branch_id_up').select2({
					placeholder: 'Branch',
					allowClear: true,
					minimumInputLength: 2,
					ajax: {
						url: '../ajax/ajax_json.php',
						dataType: 'json',
						type: "POST",
						quietMillis: 50,
						data: function (term) {
							return {
								q: term,
								functionName:'branches'
							};
						},
						results: function (data) {
							return {
								results: $.map(data, function (item) {
									return {
										text: item.name ,
										slug: item.name ,
										id: item.id
									}
								})
							};
						}
					}
				});
				$('body').on('change','#branch_id',function(){
					self.filter.branch_id = $(this).val();

				});
				$('body').on('change','#branch_id_up',function(){
					self.branch_id_up = $(this).val();
					self.getUpcoming();

				});
				$('body').on('change','#branch_id2',function(){
					self.filter2.branch_id = $(this).val();
					self.getAppointmentsWeekly();
				});

			},
			methods:{
				showContainer: function(c){
					var self = this;
					self.view = {list:false,weekly: false, upcoming:false};
					if(c == 1){
						self.view.list =true;
						self.getAppointments();
					} else if (c == 2){
						self.view.weekly =true;
						self.getAppointmentsWeekly();
					}else if (c == 3){
						self.view.upcoming =true;
						self.getUpcoming();
					}
				},
				getUpcoming: function(){
					var self= this;
					$.ajax({
						url:'ajax_service.php',
						type:'POST',
						data: {functionName:'getUpcoming',branch_id: self.branch_id_up},
						dataType: 'json',
						success: function(data){
							self.upcoming_list = data.list;
							self.branch_name = data.branch_name;
						},
						error:function(){

						}
					});
				},
				getDoctors: function(){
					var self = this;
					$.ajax({
						url:'ajax_service.php',
						type:'POST',
						data: {functionName:'getDoctors'},
						dataType: 'json',
						success: function(data){
							self.doctors = data;
						},
						error:function(){

						}
					});
				},
				changeStatus: function(ap, status){
					if(confirm("Are you sure you want to process this request?")){
						var orig_status = ap.status;
						ap.status = status;
						$.ajax({
							url:'ajax_service.php',
							type:'POST',
							data: {functionName:'changeStatus',id:ap.id,status:status},
							success: function(data){
								if(data == 1){

								} else {
									alert("Error. Please try again.");
									ap.status = orig_status;
								}
							},
							error:function(){

							}
						});
					}


				},
				getAppointments: function(){
					var self = this;

					$.ajax({
					    url:'ajax_service.php',
					    type:'POST',
					    data: {functionName:'getAppointments',type:1, filter: JSON.stringify(self.filter)},
						dataType:'json',
					    success: function(data){
					        self.appointments = data;
					    },
					    error:function(){

					    }
					});
				},getAppointmentsWeekly: function(){
					var self = this;

					$.ajax({
						url:'ajax_service.php',
						type:'POST',
						data: {functionName:'getAppointments',type:2, filter: JSON.stringify(self.filter2)},
						dataType:'json',
						success: function(data){
							self.appointments = data;
						},
						error:function(){

						}
					});
				},

			},
		});

	</script>
<?php include_once 'includes/page_tail.php';  ?>