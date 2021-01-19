<?php include_once 'includes/page_head.php';  ?>	<br>

	<div id='app' class="container">
		<?php makeBreadCrumb('Request Appointment') ?>

		<h3>Request Appointment</h3>

		<div v-show="view.list">
			<div class='btn-primary btn-circle' @click="showContainer(2)" ><i class='fa fa-plus'></i></div>

		</div>

		<div v-show="view.add">
			<br> <br>

			<div class="row">

				<div class="col-md-6">

					<div class="form-group">
						<input type="text" v-model='form.branch_id' class='form-control' id='branch_id'>
					</div>
					<div class="form-group">
						<select name="doctor_id" class='form-control' id="doctor_id" v-model='form.doctor_id'>
							<option value=""></option>
							<option v-for="doc in doctors" v-bind:value="doc.id">{{doc.name}}</option>
						</select>
						<span class='help-block'></span>
					</div>
					<div class="form-group">
						<input type="text" id='member_id' class='form-control' >
					</div>
					<div class="form-group">
						<select name="type_id" class='form-control' id="type_id" v-model='form.type_id'>
							<option value=""></option>
							<option v-for="t in types" v-bind:value="t.id">{{t.name}}</option>
						</select>
						<span class='help-block'></span>
					</div>
					<div class="form-group">
						<input type="date" class='form-control' autocomplete="off" placeholder='Date' id="dt" v-model='form.dt'>
					</div>
					<div class="form-group">
						<div class="row">
							<div class="col">
								<div class="input-group bootstrap-timepicker timepicker">
									<input autocomplete="off" id="desired_time" placeholder="Desired Time"  v-model='form.desired_time' type="text" class="form-control input-small">
									<div class="input-group-append">
										<span class="input-group-text" ><i class="fa fa-clock-o"></i></span>
									</div>
								</div>
								<small class="form-text text-muted">Ex. 12:30 PM</small>
							</div>

						</div>
					</div>


					<div class="form-group">
						<button class='btn btn-primary' @click="addRecord">Submit</button>
						<p class='text-muted'><small>* Appointments are subject for approval and confirmation.</small></p>
					</div>
				</div>
				<div class="col-md-6">
					<div v-show="!form.doctor_id">
						<div class="card">
							<div class="card-body text-center">
								<p><i class='fa fa-info-circle fa-3x'></i></p>
								Choose Doctor To Show Schedules
							</div>
						</div>
					</div>
					<div v-show="form.doctor_id">
						<div class="row">
							<div class="col"><i  @click="prevSched()" style='cursor: pointer;' class=' fa fa-arrow-left'></i></div>
							<div class="col text-center">{{doctor_name}}</div>
							<div class="col text-right"><i  @click="nextSched()" style='cursor: pointer;'  v-show="page < 0" class=' fa fa-arrow-right'></i></div>
						</div>
						<ul class="timeline" v-show="doctor_scheds.length">
							<li v-for="ds in doctor_scheds">
								<a href='#'>{{ ds.desired_date }} {{ ds.desired_time }} - {{ds.day_of_the_week}}</a>
								<a href="#" class="float-right">{{ ds.type_name }}</a>
								<p>Patient Name: {{ds.member_name}}  <br>   Remarks: {{ ds.remarks }}</p>
							</li>
						</ul>
						<div v-show="!doctor_scheds.length" class="alert alert-danger mt-2">
							No record found.
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script src='../js/vue3.js'></script>
	<script>
		vm = new Vue({
			el:'#app',
			data: {
				appointments:[],
				doctors:[],
				doctor_name:'',
				page:0,
				doctor_scheds:[],
				form:{type_id:'',doctor_id:'',id:0,branch_id:'',dt:'',desired_time:'',member_id:''},
				ajax_running: false,
				types:[],
				view: {list:false,add:true}
			},
			mounted: function(){

				var self = this;

				self.getRecord();
				self.getDoctors();
				self.getTypes();

				$('#member_id').select2({
					placeholder: 'Search Client', allowClear: true, minimumInputLength: 2,

					ajax: {
						url: '../ajax/ajax_json.php', dataType: 'json', type: "POST", quietMillis: 50, data: function(term) {
							return {
								q: term, functionName: 'members', my_client: 0
							};
						}, results: function(data) {
							return {
								results: $.map(data, function(item) {
									return {
										text: item.lastname ,
										slug: item.lastname ,
										id: item.id
									}
								})
							};
						}
					}
				});

				$('#member_id').change(function(){
					self.form.member_id = $(this).val();
				});

				$('#desired_time').timepicker();

				$('#desired_time').timepicker().on('changeTime.timepicker', function(e) {
					self.form.desired_time = e.time.value
				});

				$('#doctor_id').select2({placeholder: 'Select Doctor' ,allowClear: true});

				$('#type_id').select2({placeholder: 'Select Type' ,allowClear: true});

				$('#doctor_id').change(function(){
					self.form.doctor_id = $(this).val();
					self.doctor_name = $('#doctor_id').select2('data').text;
					self.page = 0;
					self.getDoctorSched();
				});

				$('#doctor_id_filter').change(function(){
					self.filter.doctor_id = $(this).val();
				});

				$('#type_id').change(function(){
					self.form.type_id = $(this).val();
				});

				$('#branch_id,#branch_id_filter').select2({
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

				$('#branch_id').change(function(){
					self.form.branch_id = $(this).val();
				});

				$('#branch_id_filter').change(function(){
					self.filter.branch_id = $(this).val();
				});



			},
			methods: {
				resetData: function(){
					this.page = 0;
					this.form = {type_id:'',doctor_id:'',id:0,branch_id:'',dt:'',desired_time:'',member_id:''};
					this.doctor_scheds= [];
					$('#branch_id').select2('val',null);
					$('#member_id').select2('val',null);
					$('#doctor_id').select2('val',null);
					$('#type_id').select2('val',null);
				},
				nextSched: function(){
					this.page += 1;
					this.getDoctorSched();
				},
				prevSched: function(){
					this.page -= 1;
					this.getDoctorSched();
				},
				getTypes: function(){
					var self = this;
					$.ajax({
						url:'ajax_service.php',
						type:'POST',
						data: {functionName:'getTypes'},
						dataType:'json',
						success: function(data){
							self.types = data;
						},
						error:function(){

						}
					});
				},
				getDoctorSched: function(){
					var self = this;
					$.ajax({
					    url:'ajax_service.php',
					    type:'POST',
					    data: {functionName:'getLatestSched',page:self.page, doctor_id:self.form.doctor_id},
						dataType:'json',
					    success: function(data){
							self.doctor_scheds = data;
					    },
					    error:function(){

					    }
					});
				},
				showContainer: function(c){
					var self = this;
					self.view = {list:false,add:false};
					if(c == 1){
						self.view.list = true;
						$('#doctor_id').select2('val',null);
						$('#branch_id').select2('val',null);
						self.form = {doctor_id:'',id:0,branch_id:'',days:'1',time_in:'',time_out:''};

					} else if (c == 2){

						self.view.add = true;
					}
				},
				getRecord: function(){
					var self = this;


				},
				addRecord: function(){
					var self = this;
					$.ajax({
						url:'ajax_service.php',
						type:'POST',
						data: {functionName:'addAppointment', form: JSON.stringify(self.form)},
						success: function(data){
							alert(data);
							self.resetData();
						},
						error:function(){

						}
					});

				},editRecord: function(t){
					var self = this;

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
				}
			}
		});
	</script>
<?php include_once 'includes/page_tail.php';  ?>