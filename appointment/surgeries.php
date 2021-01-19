<?php include_once 'includes/page_head.php';  ?>	<br>

	<div id='app' class="container">
		<?php makeBreadCrumb('Appointment Types') ?>

		<h3>Appointment Types</h3>

		<div v-show="view.list">
			<div class='btn-primary btn-circle' @click="showContainer(2)" ><i class='fa fa-plus'></i></div>
			<div v-show="types.length">
				<table class='table table-bordered'>
					<tr>
						<th>Name</th>
						<th>Created At</th>
						<th></th>
					</tr>
					<tr v-for="type in types">
						<td>{{ type.name }}</td>
						<td>{{ type.created_at }}</td>
						<td>
							<button type="button" class="btn btn-primary btn-sm" @click="editType(type)"><i class='fa fa-pencil'></i></button>
							<button type="button" class="btn btn-danger btn-sm"><i class='fa fa-trash'></i></button>
						</td>
					</tr>
				</table>
			</div>
			<div v-show="!types.length && ajax_running == false">
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

				<div class="form-group">
					<input type="text" class='form-control' v-model='form.name' placeholder='Appointment Name'>
					<span class='help-block'></span>
				</div>

				<div class="form-group">
					<span class='help-block'><strong>Time Required</strong></span>
					<div class="row">
						<div class="col-md-4 mb-2"  v-for="time in times">
							<div class="card" @click="changeTime(time)" v-bind:class="time.active == 1 ? 'bg-primary text-light' : ''">
								<div class="card-body truncate" v-bind:title="time">
									{{time.label}}
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="form-group">
					<button class='btn btn-primary' @click="addType">Submit</button>
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
				types:[],
				form:{name:'',id:0,time_required:''},
				times:[
					{time: 0.5 , label: "30 Minutes",active:0},
					{time: 1 , label: "1 Hour",active:1},
					{time: 1.5 , label: "1.5 Hours",active:0},
					{time: 2 , label: "2 Hours",active:0},
					{time: 2.5 , label: "2.5 Hours",active:0},
					{time: 3 , label: "3 Hours",active:0},
					{time: 3.5 , label: "3.5 Hours",active:0},
					{time: 4 , label: "4 Hours",active:0},
				],
				ajax_running: false,
				view: {list:true,add:false}
			},
			mounted: function(){
				this.getTypes();
			},
			methods: {
				changeTime: function(t){
					var self = this;
					for(var i in self.times){
						self.times[i].active = 0;
					}
					t.active = 1;
					self.form.time_required = t.time;
				},
				showContainer: function(c){
					var self = this;
					self.view = {list:false,add:false};
					if(c == 1){
						self.view.list = true;
						self.form = {name:'',id:0};
						self.getTypes();
					} else if (c == 2){

						self.view.add = true;
					}
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
				addType: function(){
					var self = this;
					$.ajax({
						url:'ajax_service.php',
						type:'POST',
						data: {functionName:'addType', form: JSON.stringify(self.form)},

						success: function(data){
						self.showContainer(1);
						self.form = {name:'',id:0};
						},
						error:function(){

						}
					});
				},editType: function(t){
					var self = this;
					self.showContainer(2);
					self.form = {name: t.name,id: t.id};
				}
			}
		});
	</script>
<?php include_once 'includes/page_tail.php';  ?>