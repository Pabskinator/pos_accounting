<?php

	// fix and check
	require_once '../includes/admin/page_head2.php';

	if(!$user->hasPermission('dashboard_tm')){
		// redirect to denied page
		Redirect::to(1);
	}

	// by branch

	// by sales type

	// avg daily sales

	// jan to dec sales


?>


<div class="container-fluid" id='top_management'>

<link rel="stylesheet" href="../css/custom.css">
<br>
 <!-- X PANEL BRANCHES -->
 <!-- Cur Vs Prev -->
	<div class="row">
		<div class="col-md-12">
			<div class="x_panel tile  overflow_hidden">
				<div class="x_content">
					<h3 class='text-center' ><a href="reports2.php" class='text-success'>{{vsCurLast.lbl}}%</a></h3>
					<div class="row">
						<div class="col-md-6">
							{{vsCurLast.total_prev}}<br> ({{vsCurLast.period2}})
						</div>
						<div class="col-md-6 text-right">

							{{vsCurLast.total_current}} <br> ({{vsCurLast.period1}})
						</div>
					</div>

					<p class='text-muted text-center'>Total Sales Last Year vs Current Year Same Period</p>
				</div>
			</div>
		</div>
	</div>
 <!--End  Cur Vs Prev -->
	<div class="x_panel">
		<div class="x_title">
		<div class="row">
		<div class="col-md-4">
		<h5>Branches</h5>
		</div>
		<div class="col-md-4 text-center">

		<h5><i class="fa fa-arrow-left cpointer" @click="prevBranch()" ></i> <span style='margin-left: 20px;margin-right:20px;'>{{branch_sales.date_name}}</span> <i class="fa fa-arrow-right cpointer" v-show="branch_sales.page < 0" @click="nextBranch()" ></i></h5>
		</div>
		<div class="col-md-4 text-right">
			<select v-model="branch_sales.type" @change="changeBranch()">
				<option value="1">Month</option>
				<option value="2">Year</option>
			</select>
		</div>
		</div>
		<div class="clearfix"></div>
		</div>
		<div class="x_content">
				<div class="row tile_count" v-show="branch_sales.list.length">
					<div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count" v-for="bs in branch_sales.list">
						<span class="count_top"><i class="fa fa-map-marker"></i> {{bs.branch_name}}</span>
						<div class="count">{{bs.total}}</div>
					</div>
					<div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count text-success" >
						<span class="count_top"><i class="fa fa-map-marker"></i> Total</span>
						<div class="count">{{branch_sales.total}}</div>
					</div>
				</div>
				<div v-show="!branch_sales.list.length">
					<p>No record yet.</p>
				</div>
		</div>
	</div>
<!-- X PANEL BRANCHES END -->
<div class="x_panel">  <!-- X PANEL SALESTYPE -->
	<div class="x_title">
		<div class="row">
		<div class="col-md-4">
		<h5>Sales Type</h5>
		</div>
		<div class="col-md-4 text-center">

		<h5><i class="fa fa-arrow-left cpointer" @click="prevSalesType()"></i> <span style='margin-left: 20px;margin-right:20px;'>{{salestype_sales.date_name}}</span> <i v-show="salestype_sales.page < 0" class="fa fa-arrow-right cpointer" @click="nextSalesType()"></i></h5>
		</div>
		<div class="col-md-4 text-right">
			<select v-model="salestype_sales.type" @change="changeSalesType()">
				<option value="1">Month</option>
				<option value="2">Year</option>
			</select>
		</div>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="x_content">
		<div class="row tile_count" v-show="salestype_sales.list.length">
			<div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count" v-for="bs in salestype_sales.list">
				<span class="count_top"><i class="fa fa-money"></i> {{bs.sales_type}}</span>
				<div class="count">{{bs.total}}</div>
			</div>
			<div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count text-success" >
				<span class="count_top"><i class="fa fa-map-marker"></i> Total</span>
				<div class="count">{{salestype_sales.total}}</div>
			</div>
		</div>
		<div v-show="!salestype_sales.list.length">
			<p>No record yet.</p>
		</div>
	</div>
</div> <!-- X PANEL SALESTYPE END -->

			<!-- graphs -->
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<div class="x_panel tile fixed_height_320">
						<div class="x_title">
							<h2>Sales</h2>

							<div class="clearfix"></div>
						</div>
						<div class="x_content">
							<div id="line-example"  style='height: 220px;'></div>
						</div>
					</div>
				</div>

				<!-- Donut order request -->
				<div class="col-md-12 col-sm-12 col-xs-12" >
					<div class="x_panel tile fixed_height_320 overflow_hidden">

						<div class="x_title">
							<h2>Pending Client Order</h2>
							<div class="clearfix"></div>
						</div>

						<div class="x_content">
							<div class="row">
								<div class="animated flipInY col-md-4 col-sm-12">
									<div class="tile-stats">
										<div class="count">{{pending_order.total}}</div>
										<h3>Total Pending Request</h3>
										<p></p>
									</div>
								</div>
								<div class="col-md-8 col-sm-12">
									<table class="table table-bordered dataTable" id='tblBordered'>
										<thead>
											<tr>
												<th>Branch</th>
												<th>Request</th>
											</tr>
										</thead>
										<tbody>
											<tr v-for="p in pending_order.list">
												<td>{{p.branch_name}}</td>
												<td>{{p.total}}</td>
											</tr>
										</tbody>
									</table>
									<br>
									<div class='text-right'>
										<a class='btn btn-success' href="wh_reports.php">View All Request</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- end donut-->
			</div> <!-- end graph -->

			       <!-- Product, User stats-->
			<div class="row top_titles">
				<div class="col-md-12">
					<div class="x_panel tile">
						<div class="x_title">
							<h2>Inventory</h2>
							<div class="clearfix"></div>
						</div>
						<div class="x_content">
							<div class="row">
								<div class="animated flipInY col-md-4 col-sm-12">
									<div class="tile-stats">
										<div class="count">{{inventory.total}}</div>
										<h3>Stock Value</h3>
										<p>All stocks in our system</p>
									</div>
								</div>
								<div class="col-md-8 col-sm-12" >
									<div class='fixed_height_320' style='overflow-y: auto;'>
										<table class="table table-bordered dataTable"  id='tblBordered'>
											<thead>
												<tr>
													<th>Branch</th>
													<th>Stock Value</th>
												</tr>
											</thead>
											<tbody>
												<tr v-for="inv in inventory.list">
													<td>{{inv.branch_name}}</td>
													<td>{{inv.total_amount}}</td>
												</tr>
											</tbody>
										</table>
										<div class='text-right'>
											<a class='btn btn-success' href="inventory.php">View All Stocks</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
	 <!-- Total Credit -->
		<div class="row">
			<div class="col-md-12">
				<div class="x_panel tile  overflow_hidden">
					<div class="x_content">
						<h3 class='text-center'><a class='text-success' href="member_credits.php">{{receivables.total_credit}}</a></h3>
						<p class='text-muted text-center'>Total receivables</p>
					</div>
				</div>
			</div>
		</div>
	 <!-- End Total Credit -->
	<div v-show="receivables.collections.length">

		<div class="x_panel tile ">
			<div class="x_title">
				<h2>Last 10 Collections</h2>

				<div class="clearfix"></div>
			</div>
			<div class="x_content">

				<div class="animated flipInY col-md-4 col-sm-12">
					<div class="tile-stats">
						<div class="count">{{receivables.total_collected}}</div>
						<h3>Total Collection</h3>

					</div>
				</div>

				<div class="col-md-8">
					<table class="table table-bordered dataTable" id='tblBordered'>
						<thead>
						<tr>
							<th>Cr Number</th>
							<th>Total</th>
							<th>Date</th>
						</tr>

						</thead>
						<tbody>
						<tr v-for="i in receivables.collections">
							<td>{{i.cr_number}}</td>
							<td>{{i.total}}</td>
							<td>{{i.date}}</td>
						</tr>
						</tbody>
					</table>
				</div>

			</div>
		</div>
	</div>

	</div>
	<script src='../js/vue3.js'></script>
	<script>

		var vm = new Vue({
			el: "#top_management",
			data:{
				branch_sales: {
					list: [
						{ branch_name:'Office', total:1000.00 },
						{ branch_name:'Warehouse', total:1000.00 },
						{ branch_name:'Watervent', total:1000.00 },
						{ branch_name:'Table Top', total:1000.00 },
						{ branch_name:'WH', total:1000.00 },
					],
					page: 0,
					type: '1',
					date_name: '',
					total:0,
				},
				salestype_sales: {
					list : [
						{ sales_type:'POS', total:2000.00 },
						{ sales_type:'CS', total:3000.00 },
						{ sales_type:'Office Cash', total:2000.00 },
						{ sales_type:'JG', total:1000.00 },
						{ sales_type:'POS', total:2000.00 },
						{ sales_type:'CS', total:3000.00 },
						{ sales_type:'Office Cash', total:2000.00 },
						{ sales_type:'JG', total:1000.00 },
					],
					page:0,
					type:'1',
					date_name: '',
					total:0,
				},
				inventory: {
					total:10000.00,
					list:[
						{ branch_name:'Office', total_amount: 1000.00},
						{ branch_name:'Warehouse', total_amount: 1000.00},
						{ branch_name:'Watervent', total_amount: 1000.00},
					],
				},
				pending_order: {
					total:10000.00,
					list:[
						{ branch_name:'Office', total:20},
						{ branch_name:'Warehouse', total: 23},
						{ branch_name:'Watervent', total: 11},
					],
				},
				vsCurLast: {lbl:'',total_current:0,total_prev:0, period1:'',period2:''},
				client_request:  [
					{label: "For Approval", value: 12},
					{label: "Warehouse", value: 23},
					{label: "Shipping", value: 22}
				],
				supplier_request:  [
					{label: "For Approval", value: 12},
					{label: "Warehouse", value: 30},
					{label: "Shipping", value: 20}
				],
				receivables: {
					total_credit: 0,
					collections: [],
					total_collected: 0,
				}
			},
			mounted: function(){

				var self = this;


				setTimeout(function(){
					self.getPast10();
				},1000);

				self.getVsCurLast();
				self.getBranchSales();
				self.getSalesType();



				setTimeout(function(){

					self.getStockValue();
					self.getPendingRequest();
					self.getReceivables();
				},2000); // after two seconds

				setTimeout(function(){

				},4000); // after four seconds

			},
			methods: {
				getPast10: function(){
					$.ajax({
						url:'../ajax/ajax_query.php',
						type:'post',
						dataType:'json',
						data: {branch:localStorage['branch_id'],functionName:'getPast10'},
						success: function(data){
							$('#salesPastTenDays').html('');
							Morris.Line({
								element: 'line-example',
								data: data,
								xkey: 'y',
								ykeys: ['a'],
								labels: ['Sales'],
								xLabelAngle: 35,
								padding: 40,
								parseTime: false
							});
						},
						error:function(){

						}
					});
				},
				changeBranch: function(){
					this.branch_sales.page=0;
					this.getBranchSales();
				},
				prevBranch: function(){
					this.branch_sales.page-=1;
					this.getBranchSales();
				},
				nextBranch: function(){
					this.branch_sales.page+=1;
					this.getBranchSales();
				},
				getBranchSales: function(){
					var self = this;

					$.ajax({
					    url:'../ajax/ajax_tm.php',
					    type:'POST',
						dataType:'json',
					    data: { functionName:'branchSales', type:self.branch_sales.type, page:self.branch_sales.page},
					    success: function(data){
						    self.branch_sales.list = data.list;
						    self.branch_sales.date_name = data.date_name;
						    self.branch_sales.total = data.total;
					    },
					    error:function(){

					    }
					});

				},
				changeSalesType: function(){
					this.salestype_sales.page=0;
					this.getSalesType();
				},
				prevSalesType: function(){
					this.salestype_sales.page-=1;
					this.getSalesType();
				},
				nextSalesType: function(){
					this.salestype_sales.page+=1;
					this.getSalesType();
				},
				getSalesType: function(){
					var self = this;
					$.ajax({
					    url:'../ajax/ajax_tm.php',
					    type:'POST',
						dataType:'json',
					    data: { functionName:'salesTypes', type:self.salestype_sales.type, page:self.salestype_sales.page},
					    success: function(data){
						    self.salestype_sales.list = data.list;
						    self.salestype_sales.date_name = data.date_name;
						    self.salestype_sales.total = data.total;
					    },
					    error:function(){

					    }
					});

				},
				getVsCurLast: function(){
					var self = this;
					$.ajax({
						url:'../ajax/ajax_tm.php',
					    type:'POST',
						dataType:'json',
					    data: {functionName:'getSamePeriodPercentage'},
					    success: function(data){
					        self.vsCurLast = data;
					    },
					    error:function(){

					    }
					});
				},
				getStockValue: function(){
					var self = this;
					$.ajax({
						url:'../ajax/ajax_tm.php',
					    type:'POST',
						dataType:'json',
					    data: {functionName:'stockValue'},
					    success: function(data){
					        self.inventory = data;
					    },
					    error:function(){

					    }
					});
				},
				getPendingRequest: function(){
					var self = this;
					$.ajax({
						url:'../ajax/ajax_tm.php',
					    type:'POST',
						dataType:'json',
					    data: {functionName:'getPendingOrder'},
					    success: function(data){
					        self.pending_order = data;
					    },
					    error:function(){

					    }
					});
				},
				getReceivables: function(){
					var self = this;
					$.ajax({
						url:'../ajax/ajax_tm.php',
						type:'POST',
						dataType:'json',
						data: {functionName:'getReceivables'},
						success: function(data){
							self.receivables = data;
						},
						error:function(){

						}
					});
				}
			}
		});



	</script>

<?php require_once '../includes/admin/page_tail2.php'; ?>