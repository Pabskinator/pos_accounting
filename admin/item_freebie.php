<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('freebie')){
		// redirect to denied page
		Redirect::to(1);
	}




?>
	<link rel="stylesheet" href="../css/dropzone2.css">
	<input type="hidden" value='<?php echo json_encode($arr_main_spec)?>'  id='main_specs'>
	<input type="hidden" value='<?php echo json_encode($arr_ind_spec)?>'  id='all_specs'>

	<!-- Page content -->
	<div id="page-content-wrapper">


		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset" id ='app'>
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					Freebies
				</h1>

			</div>

			<div class="row">
				<div class="col-md-12">
					<div class="btn-group hidden-xs" role="group" aria-label="..." style='margin-bottom:10px;'>
						<a class='btn btn-default btn_nav' @click="showContainer(1)" data-con='1' title='List' href='#'> <span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>List Item </span></a>
						<a class='btn btn-default btn_nav'  @click="showContainer(2)" data-con='2' title='Add New'  href='#'> <span class='glyphicon glyphicon-plust'></span> <span class='hidden-xs'>Add New</span></a>
						</div>
					<div class="panel panel-primary">
						<!-- Default panel contents -->
						<div class="panel-heading">Item Freebies</div>
						<div class="panel-body">
							<div v-show="con.list_view"> <!-- End List View -->
								<h4 class='text-center'>Item List</h4>
								<div class="row">
									<table class='table table-bordered'>
										<thead>
											<tr>
												<th>Item</th>
												<th>Qty to buy</th>
												<th>Freebies</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
											<tr v-for="item in items">
												<td style='border-top: 1px solid #ccc;'>
													{{item.item_code}}
													<small class='span-block text-danger'>{{item.description}}</small>
												</td>
												<td style='border-top: 1px solid #ccc;'>{{item.qty}}</td>
												<td style='border-top: 1px solid #ccc;'>{{{item.freebies}}}</td>
												<td style='border-top: 1px solid #ccc;'>
													<button class='btn btn-default btn-sm'><i class='fa fa-edit'></i></button>
													<button class='btn btn-danger btn-sm'><i class='fa fa-trash'></i></button>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div> <!-- End List View -->
							<div v-show="con.add_view"> <!-- End List View -->
								<h4 class='text-center'>Add Item</h4>

								<div class="row">
									<div class="col-md-3">
										<div class="form-group">
											<input type="text" class='form-control selectitem' id='item_id' v-model='form.item_id'>
											<span class='help-block'>Select Item with Freebies</span>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<input type="text" class='form-control' placeholder='Quantity' v-model='form.qty'>
											<span class='help-block'>Needed Quantity to get Freebies</span>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-3">
										<div class="form-group">
											<input type="text" class='form-control selectitem' id='item_id_freebie' v-model="freebie.item_id">
											<span class='help-block'>Freebie item</span>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<input type="text" class='form-control' placeholder='Quantity' v-model="freebie.qty">
											<span class='help-block'>Qty to get</span>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<div class="input-group">
												<input type="text" class="form-control" placeholder="Discount" v-model='freebie.discount' aria-describedby="basic-addon2">
												<span class="input-group-addon" id="basic-addon2">%</span>
											</div>
											<span class='help-block'>Discount, 1-100, 100 if it's free</span>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<button class='btn btn-primary' @click="addFreebie">Add Freebie</button>
										</div>
									</div>
								</div>

								<table class='table table-bordered' v-show="freebies.length">
									<thead>
										<tr>
											<th>Item</th>
											<th>Qty</th>
											<th>Discount</th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="f in freebies">
											<td style='border-top: 1px solid #ccc;' >{{f.item_id}}</td>
											<td style='border-top: 1px solid #ccc;' >{{f.qty}}</td>
											<td style='border-top: 1px solid #ccc;' >{{f.discount}}</td>
										</tr>
									</tbody>
								</table>
								<div class="alert alert-info" v-show="!freebies.length">Choose freebies</div>

								<hr>
								<button class='btn btn-default' @click="saveFreebie" v-show="freebies.length" >Save</button>

							</div> <!-- End List View -->

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
					con: {list_view:true,add_view:false},
					ajax_running:false,
					items: [],
					form: {item_id:'',qty:''},
					freebie: {discount:'100',qty:'',item_id:''},
					freebies:[]
				},
				watch: {
					fitem_id_freebie: function(){
						if(!this.fitem_id_freebie){
							$('#item_id_freebie').select2('val',null);
						}

					}
				},
				computed: {
					item_id_freebie: function(){
						return this.freebie.item_id;
					}
				},
				ready: function(){
					var vm  = this;
					vm.getRecord();
				},
				methods: {
					saveFreebie: function(){
						var self = this;
						if(!self.form.item_id){
							tempToast('error','Invalid item.','');
							return;
						}
						if(!self.form.qty){
							tempToast('error','Invalid item qty.','');
							return;
						}
						if(!self.freebies.length){
							tempToast('error','Enter freebies first','');
							return;
						}
						$.ajax({
						    url:'../ajax/ajax_freebies.php',
						    type:'POST',
							dataType:'json',
						    data: {functionName:'saveFreebie', main_item: JSON.stringify(self.form), freebies:JSON.stringify(self.freebies)},
						    success: function(data){
								if(data.success){

									tempToast('info','Inserted successfully.','');

									self.form= {item_id:'',qty:''};
									self.freebie= {discount:'100',qty:'',item_id:''};
									self.freebies=[];
									$('#item_id_freebie').select2('val',null);
									$('#item_id').select2('val',null);


								} else {
									tempToast('error','Invalid request.','');
								}
						    },
						    error:function(){

						    }
						});
					},
					addFreebie: function(){
						var self =this;
						self.freebies.push(self.freebie);
						self.freebie =  {discount:'100',qty:'',item_id:''};
					},
					hideContainer: function(){
						this.con = {list_view:false,add_view:false}
					},
					showContainer: function(c){
						var vm  = this;
						vm.hideContainer();
						if(c == 1){
							vm.con.list_view = true;

							vm.getRecord();

						} else if (c == 2){
							vm.con.add_view = true;

						}
					},
					submitRecord : function(){

					},
					getRecord: function(s){
						var self = this;

						
						if(self.ajax_running){
							return;
						}
						self.ajax_running = true;
						$.ajax({
						    url:'../ajax/ajax_freebies.php',
						    type:'POST',
						    data: {functionName:'getRecord'},
							dataType:'json',
						    success: function(data){
							    self.items = data;
						        self.ajax_running=  false;
						    },
						    error:function(){
							    self.ajax_running=  false;
						    }
						})
					}

				}
			});
			$(document).ready(function(){

			});




		</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>