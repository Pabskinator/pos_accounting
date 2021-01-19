<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('branch')){
		// redirect to denied page
		Redirect::to(1);
	}

	$rack = new Rack();
	$default_racks = $rack->getRackDefaults($user->data()->branch_id);
	$arr['surplus'] = ['rack_name' => $default_racks->rack_surplus,'rack_id' => $default_racks->surplus_rack];
	$arr['bo'] = ['rack_name' => $default_racks->rack_bo,'rack_id' => $default_racks->bo_section];

?>



	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset" id='vapp'>
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				BO Section/ Surplus Rack
			</h1>
		</div>
		<div class="btn-group hidden-xs" role="group" aria-label="..." style='margin-bottom:10px;'>

			<a class='btn btn-default' @click.prevent="getContainer(1)"  title='Surplus'>
				<span class='glyphicon glyphicon-barcode'></span> <span class='hidden-xs'>Surplus</span></span>
			</a>

			<a class='btn btn-default'   @click.prevent="getContainer(2)" title='BO Section'>
				<span class='glyphicon glyphicon-barcode'></span> <span class='hidden-xs'>BO Section</span></span>
			</a>

		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				Title
			</div>
			<div class="panel-body">
				<input type="hidden" id='rack_defaults' value='<?php echo json_encode($arr); ?>'>
				<div v-show="from_surplus.length">
					<h3>For transfer rack</h3>
					<table class='table table-bordered' id='tblBordered'>
						<tr>
							<th>Item</th>
							<th>Type</th>
							<th>Rack</th>
							<th>Qty</th>
							<th></th>
						</tr>
						<tr v-for="item,$index in from_surplus">
							<td>{{item.item_code}}</td>
							<td>{{item.type}}</td>
							<td>{{item.rack_name}}</td>
							<td>{{item.qty}}</td>
							<td><a class='btn btn-danger' @click.prevent="removeItem($index)"><i class='fa fa-trash'></i></a></td>
						</tr>

					</table>
					<br>
					<button @click="submitConversion">Submit</button>

				</div>
				<div v-show="container.surplus">
					<div class="row">
						<div class="col-md-12">
							<h3>Surplus Items</h3>
							<table v-show='surplus' id='tblBordered' class='table table-bordered'>
								<tr>
									<th>Item</th>
									<th>Qty</th>
									<th></th>
								</tr>
								<tr v-for="item in surplus">
									<td>{{ item.item_code }}</td>
									<td>{{ item.qty }}</td>
									<td><a href="#" class='btn btn-default' @click="convert(item,1)"><i class='fa fa-refresh'></i></a></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div v-show="container.bo">
					<div class="row">
						<div class="col-md-12">
							<h3>BO Section</h3>
							<table v-show='bo' id='tblBordered'  class='table table-bordered'>
								<tr>
									<th>Item</th>
									<th>Qty</th>
									<th></th>
								</tr>
								<tr v-for="item in bo">
									<td>{{ item.item_code }}</td>
									<td>{{ item.qty }}</td>
									<td><a href="#" class='btn btn-default' @click="convert(item,2)"><i class='fa fa-refresh'></i></a></td>
								</tr>
							</table>
							<br>

						</div>

					</div>
				</div>

			</div>
		</div>
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id='mtitle'>Convert</h4>
						</div>
						<div class="modal-body" id='mbody'>
							<h4>{{cur_item.item_code}}</h4>
							<p>Qty: <span class='text-danger'>{{cur_item.qty}}</span></p>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										Convert To:
										<select class='form-control' @change="getRackSpec" id='type_id' v-model='con.type_id'>
											<option v-for="type in types" v-bind:value="type.id">{{type.text}}</option>

										</select>
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group">
										Rack:
										<input type="hidden" class='form-control' id='rack_id' v-model='con.rack_id'>
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group">
										Qty:
										<input type="text" class='form-control' placeholder='Enter Qty' id='qty' v-model='con.qty'>
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group">
										<button class='btn btn-default' @click="addConversion">Submit</button>
									</div>
								</div>
							</div>
						</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->

	</div> <!-- end page content wrapper-->
		<script src='../js/vue3.js'></script>
	<script>
		$(document).ready(function(){

		});

		var vm = new Vue({
			el:'#vapp',
			data:{
				container:{bo:false,surplus:true},
				con: {type_id:'1',rack_id:'',qty:'',rack_name:''},
				surplus: [],
				bo:[],
				cur_item:{},
				from_surplus:[],
				types:[],
				rack_defaults:[],
				bo_selection:[{id:'1',text:'Good'},{id:'2',text:'Damage'},{id:'3',text:'Incomplete'},{id:'5',text:'Surplus'},{id:'6',text:'Dispose'}],
				surplus_selection:[{id:'1',text:'Good'},{id:'2',text:'Damage'},{id:'3',text:'Incomplete'},{id:'4',text:'BO Section'},{id:'6',text:'Dispose'}],
			},
			mounted:function(){
				var self = this;
				self.getRecord();

				$('#rack_id').select2({
					placeholder: 'Search rack',
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
								functionName:'racks'
							};
						},
						results: function (data) {
							return {
								results: $.map(data, function (item) {
									return {
										text: item.rack,
										slug: item.rack,
										id: item.id
									}
								})
							};
						}
					}
				});
				$('#rack_id').change(function(){
					self.con.rack_id = $(this).val();
					self.con.rack_name = $('#rack_id').select2('data').text;

				});

				try{
					self.rack_defaults =JSON.parse($('#rack_defaults').val());

				}catch(e){

				}


			},
			methods:{
				getRackSpec:function(){
					var self = this;
					if(self.con.type_id == 4 || self.con.type_id == 5){
						if(self.con.type_id == 4){
							$('#rack_id').select2('data', {id: self.rack_defaults['bo'].rack_id, text: self.rack_defaults['bo'].rack_name});
							self.rack_id = self.rack_defaults['bo'].rack_id;
						}
						if(self.con.type_id == 5){
							$('#rack_id').select2('data', {id: self.rack_defaults['surplus'].rack_id, text: self.rack_defaults['surplus'].rack_name});
							self.rack_id = self.rack_defaults['surplus'].rack_id;
						}
						$('#rack_id').select2('enable',false);

					} else {
						$('#rack_id').select2('enable',true);
					}
				},
				removeItem: function(i){
					this.from_surplus.splice(i,1);
				},
				submitConversion: function(){
					var self = this;
					$.ajax({
					    url:'../ajax/ajax_bo.php',
					    type:'POST',
					    data: {functionName:'convert',items:JSON.stringify(self.from_surplus)},
					    success: function(data){
							alertify.alert(data,function(){
								location.reload();
							});
					    },
					    error:function(){

					    }
					});
				},
				addConversion: function(){
					var self = this;
					if(parseFloat(self.con.qty) > parseFloat(self.cur_item.qty) ){
						alert("Invalid qty");
						self.con.qty=0;
						return;
					}
					self.from_surplus.push(
							{
								rack_id_from:self.cur_item.rack_id,
								type: $('#type_id option:selected').text(),
								type_id:self.con.type_id,
								item_id:self.cur_item.item_id,
								item_code:self.cur_item.item_code,
								rack_name: self.con.rack_name,
								qty:self.con.qty,
								rack_id_to:self.con.rack_id
							}
						);
					$('#myModal').modal('hide');

					self.con= {rack_id:'',qty:'',rack_name:''};
					self.cur_item ={};
					$('#rack_id').select2('val',null);



				},
				convert: function(i,n){
					var self = this;
					self.cur_item = i;
					if(n == 1){
						self.types = self.surplus_selection;
					} else if (n == 2){
						self.types = self.bo_selection;
					}
					self.con = {type_id:'1',rack_id:'',qty:'',rack_name:''};
					$('#rack_id').select2('val',null);
					$('#myModal').modal('show');
				},
				getRecord: function(){
					var self = this;

					$.ajax({
					    url:'../ajax/ajax_bo.php',
					    type:'POST',
						dataType:'json',
					    data: {functionName:'getBO'},
					    success: function(data){
							self.bo = data.bo;
							self.surplus = data.surplus;
					    },
					    error:function(){

					    }
					});
				},
				getContainer:function(v){
					this.hideContainer();

					if(v == 1){
						this.container.surplus = true;
					} else if (v == 2){
						this.container.bo = true;
					}
				},
				hideContainer: function(){
					this.container = {bo:false,surplus:false};
					this.from_surplus = [];
				}
			}
		});

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>