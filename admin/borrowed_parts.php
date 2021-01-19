<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('borrow_part')){
		// redirect to denied page
		Redirect::to(1);
	}

?>



	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					Borrowed Parts
				</h1>
			</div>
			<div class="btn-group hidden-xs" role="group" aria-label="..." style='margin-bottom:10px;'>
				<a class='btn btn-default btnCon'  @click="showContainer(1)" title='Borrow Item'>
					Borrow Item
				</a>
				<a class='btn btn-default btnCon'  @click="showContainer(2)" title='Borrowed List'>
					Borrowed List
				</a>
			</div>
			<div class="container" v-show="view.request">
				<h5>Request Item</h5>
				<div class="row">

					<div class="col-md-6">
						<div class="panel panel-default">
							<div class="panel-body">
								<div style='position:relative;padding:5px;'>
									<div v-show="parts.length" style='background-color: rgba(0, 0, 0, 0.4);position: absolute;top:0;left;0;width:100%;height: 100%;z-index:99'>
										<div class='text-center' style='color:#fff;position:relative;height:100%;top:50%;'>Read Only</div>
									</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<input type="text" class='form-control' id='branch_id' v-model='request.branch_id'>
											<span class='help-block'>Branch</span>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<input type="text" class='form-control' placeholder='Remarks' id='remarks' v-model='request.remarks'>
											<span class='help-block'>Addition Remarks</span>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<input type="text" id='item_id' class='form-control selectitem'  v-model='request.item_id'>
											<span class='help-block'>Enter Set Item</span>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<input type="text"  placeholder='Qty' class='form-control' v-model='request.qty'>
											<span class='help-block'>Qty Borrowed</span>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<select name="rack_id_from" class='form-control' id="rack_id_from" v-model='request.rack_id'>
												<option value="">Select Rack</option>
												<option v-for="rack in rack_from" v-bind:value="rack.id">{{ rack.rack_name }} ( {{rack.qty}} )</option>
											</select>
											<span class='help-block'>Deduct From</span>
										</div>
									</div>
								</div>
							</div>
							<p class='text-danger'><small>* This item will be temporarily deducted from the inventory</small></p>
						</div>
						</div>


					</div>

					<div class="col-md-6">
						<div class="panel panel-default">
							<div class="panel-body">
								<div style='position:relative;'>
									<div v-show="!complete_form" style='background-color: rgba(0, 0, 0, 0.4);position: absolute;top:0;left;0;width:100%;height: 100%;z-index:99'>
										<div class='text-center' style='color:#fff;position:relative;height:100%;top:50%;'>Please Complete The Form First</div>
									</div>


								<div class="row">

									<div class="col-md-6">
										<div class="form-group">
											<input type="text" id='item_id_part' class='form-control selectitem' >
											<span class='help-block'>Borrowing Part</span>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<input type="text" class='form-control'  v-model='borrow_item.qty' placeholder='Enter Quantity' >
											<span class='help-block'>Qty</span>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<select name="rack_to" id="rack_to" class='form-control'>
												<option value=""></option>
											</select>
											<span class='help-block'>Insert To Rack</span>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<button class='btn btn-default' @click="addItem">Add Item</button>
										</div>
									</div>
								</div>
							</div>
							</div>
						</div>
						<div class="panel panel-default" v-show="complete_form">
							<div class="panel-body">
								<div class="alert alert-info" v-show="!parts.length">Enter Parts First</div>
								<div v-show="parts.length">
									<table class='table' id='tblWithBorder'>
										<thead>
											<tr>
												<th>Item Code</th>
												<th>Rack</th>
												<th>Qty</th>
												<th></th>

											</tr>
										</thead>
										<tbody>
											<tr v-for="(part,index) in parts">
												<td style='width:250px;'>
													<span>{{part.item_code}}</span>
													<small class='text-danger span-block'>{{part.description}}</small>
												</td>
												<td>
													{{ part.rack }}
												</td>
												<td>
													{{ part.qty }}
												</td>
												<td>
													<button class='btn btn-danger btn-sm' @click="removePart(index)">
														<i class='fa fa-trash'></i>
													</button>
												</td>
											</tr>
										</tbody>
									</table>
									<hr>
									<div class='text-right'>
										<button class='btn btn-primary' @click='submitRequest()'>Submit</button>
									</div>
									<p class='text-danger'><small>* This item will be added to the inventory</small></p>
									<p class='text-danger'><small>* You must return this part to reassemble the set item</small></p>
								</div>

							</div>
						</div>
					</div>

				</div>
			</div>
			<div v-show="view.list">
				<div class="row">
					<div class="col-md-9"></div>
					<div class="col-md-3">
						<div class="form-group">
							<select name="status" v-model='status' @change="getBorrowedParts(status)" class='form-control' id="status">
								<option value="1">Borrowed</option>
								<option value="2">Returned</option>
							</select>
							<span class='help-block'>Filter Status</span>
						</div>
					</div>
				</div>
				<div v-show="records.length">

					<table class="table" >
						<thead>
						<tr>
							<th>ID</th>
							<th>Users</th>
							<th>Item</th>
							<th>From Rack</th>
							<th>Borrowed Qty</th>
							<th>Parts</th>
							<th></th>
						</tr>
						</thead>
						<tbody>
						<tr v-for="record in records">
							<td>{{ record.id }}</td>
							<td>
								<span class='span-block'>Borrowed By:
									<br><strong class='text-danger'> {{ record.firstname }} {{ record.lastname }}</strong>
									</strong>
								</span>
								<span v-show="record.status == 2" class='span-block'>
									Returned By:<br>
									<strong class='text-danger'> {{ record.firstname2 }} {{ record.lastname2 }}</strong>
								</span>
							</td>
							<td style='border-top:1px solid #ccc;'>
								{{record.item_code}}
								<span class='text-danger span-block'>{{record.description}}</span>
							</td>
							<td style='border-top:1px solid #ccc;'>{{record.rack}}</td>
							<td style='border-top:1px solid #ccc;'>{{record.qty}}</td>
							<td style='border-top:1px solid #ccc;'>
								<table class='table table-bordered' style='width:350px;'>
									<tr><th>Item</th><th>Qty</th></tr>
									<tr v-for="r in JSON.parse(record.borrowed_part)">
										<td style='border-top:1px solid #ccc;'>
											{{r.item_code}}
											<span class='text-danger span-block'>{{r.description}}</span>
										</td>
										<td style='border-top:1px solid #ccc;'>{{r.qty}}</td>
									</tr>
								</table>
							</td>
							<td style='border-top:1px solid #ccc;'>
								<button v-show="record.status == 1" class='btn btn-default' @click="returnPart(record)">Return Borrowed Parts</button>
								<span v-show="record.status == 2" class="label label-primary">Returned</span>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
				<div v-show="!records.length">
					<div class="alert alert-info">
						No record found.
					</div>
				</div>
			</div>
		</div>
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id='mtitle'>Return Stocks</h4>
						</div>
						<div class="modal-body" id='mbody'>
							{{record_details}}
							<table class='table'>
								<tr v-for="r in record_details">
									<td>
										{{ r.item_code }}
										<small class='text-danger span-block'>{{r.description}}</small>
									</td>
									<td>{{ r.qty }}</td>
									<td>
										<div v-show="r.rack_inventory.length">
										<select v-model="r.chosen_rack" name="rack_inv_id" id="rack_inv_id" class='form-control'>
										<option  v-for="ind in r.rack_inventory" v-bind:value='ind.id'>
												{{ind.rack_name }} ({{ind.qty}})
										</option>
										</select>
										</div>
										<div v-show="!r.rack_inventory.length">Not enough stock</div>
									</td>
								</tr>
							</table>
							<div class='text-right'>
								<button class='btn btn-default' @click="updateBorrowed">Submit</button>
							</div>
						</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
	</div> <!-- end page content wrapper-->
	<script src='../js/vue3.js'></script>
	<script>
		var vm = new Vue({
			el: '#page-content-wrapper',
			data:{
				request:{item_id:'',branch_id:'',qty:'1', remarks:'',rack_id:''},
				parts:[],
				complete_form:false,
				rack_from:[],
				borrow_item:{item_id:'',qty:'',rack_id:''},
				view:{request: true, list:false},
				records:[],
				record_details:[],
				temp_data:{},
				status:'1',
				ajax_running:false,
			},
			computed: {
				complete_form: function(){
					var self = this;
					if(self.request.item_id && self.request.branch_id  && self.request.qty  && self.request.rack_id){
						return true;
					} else {
						return false;
					}
				}

			},
			mounted: function(){
				var self = this;

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
				$('body').on('change','#branch_id',function(){
					self.request.branch_id = $(this).val();
					if(self.request.branch_id){
						self.getInventoryRacks();
						self.request.item_id = '';
						$('#item_id').select2('val',null);
						self.request.rack_id= '';
					}
				});

				$('body').on('change','#item_id',function(){
					self.request.item_id = $(this).val();
					if(self.request.item_id){
						if(!self.request.branch_id){
							alert('Enter branch first');
							self.request.item_id = '';
							$('#item_id').select2('val',null);
						} else{
							self.getRackInventory();
						}
					}
				});

				$('body').on('change','#item_id_part',function(){
					self.borrow_item.item_id = $(this).val();
				});
				$('#rack_to').select2({placeholder: 'Select Rack' ,allowClear: true});
				$('body').on('change','#rack_to',function(){
					self.borrow_item.rack_id = $(this).val();
				});

			},
			methods: {

				updateBorrowed: function(){
					var self = this;
					var valid = true;
					for(var i in self.record_details){
						if(!self.record_details[i].chosen_rack){
							valid = false;
						}
					}
					if(self.ajax_running) {
						return;
					}
					if(valid){
						alertify.confirm("Are you sure you want to submit this request",function(e){
							if(e){

								self.ajax_running = true;
								$.ajax({
									url:'../ajax/ajax_inventory.php',
									type:'POST',
									data: {
										functionName:'processBorrowedItem',
										rack_details: JSON.stringify(self.record_details),
										data: JSON.stringify(self.temp_data)
									},
									success: function(data){
										self.ajax_running = false;
										self.getBorrowedParts(1);
										$('#myModal').modal('hide');

										tempToast('info',data,'Info')
									},
									error:function(){
										self.ajax_running = false;
									}
								});

							}
						});

					} else {
						tempToast('error',"Invalid Rack",'Warning')
					}
				},
				returnPart: function(r){
					var self = this;
					self.temp_data = r;
					$.ajax({
					    url:'../ajax/ajax_inventory.php',
					    type:'POST',
					    data: {functionName:'showPartModule', id: r.id},
						dataType:'json',
						success: function(data){
							self.record_details = data;
							$('#myModal').modal('show');
					    },
					    error:function(){

					    }
					});

					$('#myModal').modal('show');

				},
				showContainer: function(c){

						var self = this;
						self.view = {request: false, list:false};

						if(c == 1){
							self.view.request =  true;
							self.resetRecord();

						} else if (c == 2){
							self.view.list =  true;
							self.getBorrowedParts(1);
						}

				},
				getBorrowedParts: function(s){

					var self = this;
					$.ajax({
					    url:'../ajax/ajax_inventory.php',
					    type:'POST',
					    data: {functionName:'getBorrowedParts',status:s},
						dataType:'json',
					    success: function(data){

					        self.records = data;

					    },
					    error:function(){

					    }
					});

				},
				removePart: function(i){

					this.parts.splice(i,1);

				},
				getRackInventory: function(){
					var self = this;
					if(self.request.item_id && self.request.branch_id){
						$.ajax({
							url:'../ajax/ajax_inventory.php',
							type:'POST',
							data: {functionName:'getRackInventory', item_id:self.request.item_id,branch_id:self.request.branch_id},
							dataType:'json',
							success: function(data){
								self.rack_from =data;
							},
							error:function(){

							}
						})
					}
				},
				getInventoryRacks: function(){
					var self = this;
					$.ajax({
						url:'../ajax/ajax_query2.php',
						type:'POST',
						data: {functionName:'getBranchRack',branch_id:self.request.branch_id},
						success: function(data){
							$('#rack_to').html(data);
						},
						error:function(){

						}
					});
				},
				addItem: function(){
					var self = this;
					if(self.borrow_item.item_id && self.borrow_item.rack_id && self.borrow_item.qty){
						var item_text = $('#item_id_part').select2('data').text;
						var rack_text = $('#rack_to').select2('data').text;

						var splt = item_text.split(':');

						self.parts.push(
							{
								item_id: self.borrow_item.item_id,
								item_code: splt[1],
								description: splt[2],
								rack: rack_text,
								rack_id: self.borrow_item.rack_id,
								qty: self.borrow_item.qty
							}
						);

						self.borrow_item = {item_id:'',qty:'',rack_id:''};

						$('#item_id_part').select2('val',null);
						$('#rack_to').select2('val',null);


					} else {
						alert("Please complete the form.");
					}
				},
				resetRecord: function(){
					var self = this;

					self.request = {item_id:'',branch_id:'',qty:'1', remarks:'',rack_id:''};
					self.parts = [];
					self.rack_from = [];
					self.borrow_item = {item_id:'',qty:'',rack_id:''};

					$('#item_id_part').select2('val',null);
					$('#rack_to').select2('val',null);
					$('#branch_id').select2('val',null);
					$('#item_id').select2('val',null);

				},
				submitRequest: function(){
					var self = this;
					if(self.ajax_running) {
						return;
					}
					if(self.parts.length &&  self.request.item_id && self.request.branch_id  && self.request.qty  && self.request.rack_id){
						alertify.confirm("Are you sure you want to submit this request", function(e){
							if(e){
								self.ajax_running = true;
								$.ajax({
									url:'../ajax/ajax_inventory.php',
									type:'POST',
									data: {functionName:'submitBorrowedItems', parts: JSON.stringify(self.parts),form: JSON.stringify(self.request) },
									success: function(data){
										self.ajax_running = false;
										self.resetRecord();
										tempToast('info',data,'Info');

									},
									error:function(){
										self.ajax_running = false;
									}
								});
							}
						});

					} else {
						tempToast('error',"Invalid Request",'Warning');
					}
				},
			}
		});

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>