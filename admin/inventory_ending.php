<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('inventory')){
		// redirect to denied page
		Redirect::to(1);
	}



?>


	<!-- Page content -->
	<div id="page-content-wrapper">


		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset" id ='app'>
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					Ending Inventory
				</h1>

			</div>

			<div class="row">
				<div class="col-md-12">
					<div class="btn-group hidden-xs" role="group" aria-label="..." style='margin-bottom:10px;'>
						<a class='btn btn-default btn_nav' @click="showContainer(1)" data-con='1' title='Add Ending' href='#'> <span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>Add Ending </span></a>
						<a class='btn btn-default btn_nav'  @click="showContainer(2)" data-con='2' title='Ending Inventory Report'  href='#'> <span class='glyphicon glyphicon-ok'></span> <span class='hidden-xs'>Ending Inventory Report</span></a>

					</div>
					<div class="panel panel-primary">
						<!-- Default panel contents -->
						<div class="panel-heading">Add Ending Inventory</div>
						<div class="panel-body">
							<div v-show="con.post_view">
								<h4 class='text-center'></h4>
								<div class="row">

									<div class="col-md-4">
										<div class="panel panel-default">
											<div class="panel-body">
												<h4>Item Information</h4>
												<div class="form-group">
													<label for=""></label>
													<input v-model="branch_id" id="branch_id"  type="text" class='form-control'>

												</div>
												<div class="form-group">
													<label for="">Date of report</label>
													<input v-model="report_date" id="report_date" placeholder='Enter Date' type="text" class='form-control'>

												</div>
												<hr>
												<div class="form-group">
													<label for="">Item Name</label>
													<input v-model="post.item_id" id='item_id' type="text" class='form-control selectitem'>
												</div>

												<div class="form-group">
													<label for="">Quantity</label>
													<input v-model="post.qty" placeholder='Quantity' type="text" class='form-control'>
												</div>


												<div class='text-right'>
													<button class='btn btn-default' @click="submitRecord">Add Item</button>
												</div>

											</div>
										</div>

									</div>
									<div class="col-md-8">

										<div v-show="cart_items.length">
											<table class='table table-bordered table-condensed'>
												<thead>
												<tr>
													<th>Item</th>

													<th>Qty</th>
													<th></th>
												</tr>
												</thead>
												<tbody>
												<tr v-for="item in cart_items">
													<td style='border-top:1px solid #ccc;'>
														{{ item.item_code }}
														<small class='text-danger span-block'>
															{{ item.description}}
														</small>
													</td>

													<td style='border-top:1px solid #ccc;'>{{ item.qty }}</td>
													<td style='border-top:1px solid #ccc;'>
														<button class='btn btn-default btn-sm' @click="removeItem(item)"><i class='fa fa-trash'></i></button>
													</td>
												</tr>
												</tbody>
											</table>
											<div class='text-right'>
												<button id='btnSaveRecord' class='btn btn-primary' @click="saveRecord">Submit</button>
											</div>
										</div>

										<div v-show="!cart_items.length">
											<div class="alert alert-info">
												<i class='fa fa-warning'></i>
												Add item first.
											</div>
										</div>


									</div>
								</div>
								<hr>

							</div> <!-- End Post View -->

							<div v-show="con.item_list_view">



								<div v-show="report_list.length && !details.length">
									<h4>Report List</h4>
									<table class='table table-bordered table-condensed'>
										<thead>
										<tr>
											<th>Warehouse</th>
											<th>Report Date</th>
											<th></th>
										</tr>
										</thead>
										<tbody>
										<tr v-for="list in report_list">
											<td style='border-top:1px solid #ccc;'>{{ list.branch_name }}</td>
											<td style='border-top:1px solid #ccc;'>{{ list.formatted_date }}</td>
											<td style='border-top:1px solid #ccc;'>
												<button class='btn btn-default' @click="getDetails(list)">Details</button>
											</td>
										</tr>
										</tbody>
									</table>
								</div>

								<div v-show="details.length">
									<div class="row">
										<div class="col-md-6">

										</div>
										<div class="col-md-6 text-right">

										</div>
									</div>


										<div class="row">
											<div class="col-md-3"></div>
											<div class="col-md-6">
											<div class="text-center">
												<button class='btn btn-default' @click="details = []">BACK</button>
											</div>
												<br>
												<table class='table table-bordered table-condensed'>
													<thead>
														<tr>
															<th>Item</th>
															<th>Qty</th>
														</tr>
													</thead>
													<tbody>
														<tr v-for="det in details">
															<td style='border-top: 1px solid #ccc;' >
																{{ det.item_code }}
																<small class='span-block text-danger'>
																	{{ det.description }}
																</small>
															</td>
															<td style='border-top: 1px solid #ccc;' >
																{{ det.qty }}
															</td>
														</tr>
													</tbody>
												</table>

											</div>
											<div class="col-md-3"></div>
										</div>

								</div>

								<div v-show="!report_list.length">
									<div class="alert alert-info">
										No Record Found
									</div>
								</div>
							</div> <!-- End Item List View -->





						</div>
					</div>
				</div>
			</div>


		</div> <!-- end page content wrapper-->
		<script src='../js/vue.js'></script>

		<script>

			var vm = new Vue({
				el :"#app",
				data: {
					title:'New App',
					con: {post_view:true,item_list_view: false},
					post: {item_id: '',item_code:'',description:'',qty:''},
					report_date:'',
					report_list: [],
					cart_items:[],
					details: [],
				},
				computed: {

				},
				ready: function(){
					var self  = this;
					$('body').on('change','#item_id',function(){

						var con = $(this);
						var splitted =con.select2('data').text;
						splitted =	splitted.split(':');
						self.post.item_id = con.val();
						self.post.item_code = splitted[1];
						self.post.description = splitted[2];


					});

					$('#report_date').datepicker({
						autoclose:true
					}).on('changeDate', function(ev){
						$('#report_date').datepicker('hide');
						self.report_date  = $('#report_date').val();
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

					$('body').on('change','#branch_id',function(){
						self.branch_id = $('#branch_id').val();
					});
					if(localStorage['pending_ending_inventory']){
						alertify.confirm("You have unsaved work. Do you want to load it?",function(d){
							if(d){
								try{
									self.cart_items = JSON.parse(localStorage['pending_ending_inventory']);
								}catch(e){
									alert("Your work does not have a correct format. You need to add it again.");
								}
							}

						});
					}


				},
				methods: {
					getDetails: function(list){
						var self = this;
						$.ajax({
						    url:'../ajax/ajax_inventory.php',
						    type:'POST',
							dataType:'json',
						    data: {functionName: 'getEndingDetails',branch_id:list.branch_id, report_date:list.report_date},
						    success: function(data){
								self.details = data;
						    },
						    error:function(){

						    }
						});

					},
					removeItem: function(item){
						this.cart_items.$remove(item);
					},
					submitRecord: function(){
						var self = this;
						if(self.post.item_id && self.post.qty){
							self.cart_items.push(self.post);
							self.post = {item_id: '',item_code:'',description:'',qty:''};
							$('#item_id').select2('val',null);
							localStorage['pending_ending_inventory'] = JSON.stringify(self.cart_items);
						} else {
							tempToast('error','Please complete the form','Error')
						}
					},
					saveRecord: function(){

						var self = this;
						if(self.report_date && self.branch_id && self.cart_items){
							var con = $('#btnSaveRecord');
							button_action.start_loading(con);
							$.ajax({
								url:'../ajax/ajax_inventory.php',
								type:'POST',
								data: {
									functionName:'saveEndingInventory',
									items: JSON.stringify(self.cart_items),
									report_date: self.report_date,
									branch_id:self.branch_id
								},
								success: function(data){

									self.cart_items = [];
									self.report_date = '';
									self.branch_id = '';
									localStorage.removeItem('pending_ending_inventory');
									$('#branch_id').select2('val',null);
									button_action.end_loading(con);
									alert(data);


								},
								error:function(){
									alert("Failed to connect. You have slow internet.");
									button_action.end_loading(con);
								}
							});
						} else {
							tempToast('error','Please add branch and date first.','Error');
						}


					},

					hideContainer: function(){
						this.con = {post_view:false,item_list_view:false}
					},

					showContainer: function(c){
						this.hideContainer();
						if(c == 1){
							this.con.post_view = true;
						} else if (c == 2){
							this.con.item_list_view = true;
							this.details = [];
							this.getRecord();
						}
					},

					getRecord: function(s){

						var self = this;
						$.ajax({
						    url:'../ajax/ajax_inventory.php',
						    type:'POST',
							dataType:'json',
						    data: {functionName:'getEndingInventory'},
						    success: function(data){
						        self.report_list = data;
						    },
						    error:function(){

						    }
						});

					}
				}

			});

		</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>