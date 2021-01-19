<?php include_once 'includes/page_head.php';  ?>	<br>

	<div id='app' class="container">
		<?php makeBreadCrumb('Doctor Schedule') ?>

		<h3>Doctor Schedule</h3>

		<div v-show="view.list">
			<div class='btn-primary btn-circle' @click="showContainer(2)" ><i class='fa fa-plus'></i></div>
			<div v-show="doctor_scheds.length">
				<div class="card">
					<div class="card-body">
						<div class="row">
							<div class="col-md-2"><strong>Filter:</strong></div>
							<div class="col-md-8">
								<div class="row">
									<div class="col">
										<select name="doctor_id" class='form-control' id="doctor_id_filter" v-model='filter.doctor_id'>
											<option value=""></option>
											<option v-for="doc in doctors" v-bind:value="doc.id">{{doc.name}}</option>
										</select>
									</div>
									<div class="col"><input  v-model='filter.branch_id' type="text" id='branch_id_filter' class='form-control'></div>
								</div>
							</div>
							<div class="col-md-2"></div>
						</div>

					</div>
				</div>
				<br>

				<table class='table table-bordered'>
					<tr>
						<th>Doctor</th>
						<th v-for="d in days">{{d}}</th>

					</tr>
					<tr v-for="md in doctor_scheds">
						<td>{{md.doctor_name}}</td>
						<td>{{md[1]}}</td>
						<td>{{md[2]}}</td>
						<td>{{md[3]}}</td>
						<td>{{md[4]}}</td>
						<td>{{md[5]}}</td>
						<td>{{md[6]}}</td>
						<td>{{md[7]}}</td>

					</tr>
				</table>
			</div>
			<div v-show="!doctor_scheds.length && ajax_running == false">
				<div class="alert alert-info">No Record</div>
			</div>
		</div>

		<div v-show="view.add">
			<br> <br>

			<div class="row">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<div class="text-right">
						<i class='btn-remove fa fa-remove fa-2x' @click="showContainer(1)"></i>
					</div>
					{{form}}
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
						<select name="days" class='form-control' id="days" v-model='form.days'>
							<option value="1">Monday</option>
							<option value="2">Tuesday</option>
							<option value="3">Wednesday</option>
							<option value="4">Thursday</option>
							<option value="5">Friday</option>
							<option value="6">Saturday</option>
							<option value="7">Sunday</option>

						</select>
						<span class='help-block'></span>
					</div>
					<div class="form-group">
						<div class="row">
							<div class="col">

								<div class="input-group bootstrap-timepicker timepicker">
									<input id="time_in" placeholder="Time In"  v-model='form.time_in' type="text" class="form-control input-small">
									<div class="input-group-append">
										<span class="input-group-text" ><i class="fa fa-clock-o"></i></span>
									</div>
								</div>
								<small class="form-text text-muted">Ex. 12:30 PM</small>

							</div>
							<div class="col">
								<div class="input-group bootstrap-timepicker timepicker">
									<input id="time_out" placeholder="Time Out"  v-model='form.time_out' type="text" class="form-control input-small">
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
					</div>
				</div>
				<div class="col-md-3"></div>
			</div>
		</div>
	</div>
	<script src='../js/vue3.js'></script>
	<script>
		vm = new Vue({
			el:'#app',
			data: {
				doctor_scheds:[],
				doctors:[],
				filter:{branch_id:'',branch_id:''},
				days:['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'],
				form:{doctor_id:'',id:0,branch_id:'',days:'1',time_in:'',time_out:''},
				ajax_running: false,
				view: {list:true,add:false}
			},
			mounted: function(){
				var self = this;
				self.getRecord();
				self.getDoctors();
				$('#time_in').timepicker();
				$('#time_out').timepicker();
				$('#time_out').timepicker().on('changeTime.timepicker', function(e) {
					self.form.time_out = e.time.value

				});
				$('#time_in').timepicker().on('changeTime.timepicker', function(e) {
					self.form.time_in = e.time.value

				});

				$('#doctor_id').select2({placeholder: 'Select Doctor' ,allowClear: true});
				$('#doctor_id').change(function(){
					self.form.doctor_id = $(this).val();
				});



				$('#branch_id').select2({
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




			},
			methods: {
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
					$.ajax({
						url:'ajax_service.php',
						type:'POST',
						data: {functionName:'getDoctorSchedules'},
						dataType: 'json',
						success: function(data){
							self.doctor_scheds = data;

						},
						error:function(){

						}
					});
				},
				addRecord: function(){
					var self = this;
					$.ajax({
					    url:'ajax_service.php',
					    type:'POST',
					    data: {functionName:'addDoctorSchedule', form: JSON.stringify(self.form)},
					    success: function(data){
						    self.showContainer(1);

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