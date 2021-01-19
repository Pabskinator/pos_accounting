<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('sales')) {
		// redirect to denied page
		Redirect::to(1);
	}
	$branch = new Branch();
	$branches = $branch->branchJSON($user->data()->company_id);

?>


	<!-- Page content -->
	<div id="page-content-wrapper">
		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Order </h1>

			</div>
			<?php
				// get flash message if add or edited successfully
				if(Session::exists('salesflash')) {
					echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('salesflash') . "</div>";
				}
			?>
			<?php require_once 'includes/report_nav.php'; ?>
			<div class="panel panel-primary">
				<!-- Default panel contents -->
				<div class="panel-heading">Order</div>
				<div class="panel-body">
					<div class="btn-group hidden-xs" role="group" aria-label="..." style='margin-bottom:10px;'>
						<a class='btn btn-default' @click.prevent="showContainer(1)" title='Summary'>
							<span class='glyphicon glyphicon-list'></span>
							<span class='hidden-xs'>Summary</span>
						</a>
						<a class='btn btn-default' @click.prevent="showContainer(2)" title='Raw Material'>
							<span class='glyphicon glyphicon-list-alt'></span>
							<span class='hidden-xs'>Raw Mats</span>
						</a>
						<a class='btn btn-default' @click.prevent="showContainer(3)" title='Yearly'>
							<span class='glyphicon glyphicon-calendar'></span>
							<span class='hidden-xs'>Raw Mats Yearly</span>
						</a>
					</div>
					<br>

					<div v-show="container.summary">

						<div class='row'>

							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' id='date_from' v-model='form.date_from' placeholder="Date From">
								</div>
							</div>

							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' id='date_to' v-model='form.date_to' placeholder="Date To">
								</div>
							</div>

						</div>
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<select name="branch_id" class='form-control' v-model='form.branch_id' id="branch_id">
										<option value=""></option>
										<?php
											foreach($branches as $b){
												echo "<option value='$b->id'>$b->name</option>";
											}
										?>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select name="branch_id_except" class='form-control' v-model='form.except' id="branch_id_except">
										<option value=""></option>
										<?php
											foreach($branches as $b){
												echo "<option value='$b->id'>$b->name</option>";
											}
										?>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<button class='btn btn-default' @click="getSummary()">Submit</button>
								</div>
							</div>
						</div>
						<br>
						<table id='order_summary' v-show="orders.length"  class='table table-bordered table-condensed'>
							<thead>
								<tr>
									<th>Item Code</th>
									<th @click="sortOrder(1)" >Delivered Qty <span v-show="order_asc" class='fa fa-sort-asc'></span><span v-show="!order_asc" class='fa fa-sort-desc'></span></th>
									<th @click="sortOrder(2)">Sales Qty  <span v-show="sales_asc" class='fa fa-sort-asc'></span><span v-show="!sales_asc" class='fa fa-sort-desc'></span></th>
									<th >Current Stock</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="item in orders">
									<td style='border-top:1px solid #ccc;' >{{item.item_code}}</td>
									<td style='border-top:1px solid #ccc;' >{{item.total_order}}</td>
									<td style='border-top:1px solid #ccc;' >{{item.total_sales}}</td>
									<td style='border-top:1px solid #ccc;' >{{item.total_inv}}</td>
								</tr>
							</tbody>
						</table>
						<div  class='alert alert-info' v-show="!orders.length">No record found.</div>
					</div>

					<div v-show='container.raw'>
						<div class='row'>

							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' id='date_from_raw' v-model='form_raw.date_from' placeholder="Date From">
								</div>
							</div>

							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' id='date_to_raw' v-model='form_raw.date_to' placeholder="Date To">
								</div>
							</div>

							<div class="col-md-3">
								<div class="form-group">
									<button class='btn btn-default' id='btn_raw' @click="getRaw()">Submit</button>
								</div>
							</div>

						</div>
						<hr>
						<div v-show="raws.length">

							<table class='table table-bordered' id='tbl'>
								<thead>
								<tr>
									<th >Item</th>
									<th class='text-right'>Raw Qty</th>
									<th class='text-right'>Order Qty</th>
									<th class='text-right'>Assembled Qty</th>
								</tr>
								</thead>
								<tbody>
									<tr v-for="r in raws">
										<td style='border-top:1px solid #ccc;'>{{ r.item_code }}</td>
										<td  class='text-right' style='border-top:1px solid #ccc;'>{{ r.qty }}</td>
										<td  class='text-right' style='border-top:1px solid #ccc;'>{{ r.out_qty }}</td>
										<td  class='text-right' style='border-top:1px solid #ccc;'>{{ r.raw_qty }}</td>
									</tr>
								</tbody>

							</table>
						</div>
						<div v-show="!raws.length"><div class="alert alert-info">No record.</div></div>

					</div>
					<div v-show="container.yearly">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<input name="year" class='form-control' placeholder='Enter Year' v-model='year' id="year">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<button class='btn btn-default' @click="getYearlySummary">Submit</button>
								</div>
							</div>
						</div>
						<div id="yearly-holder">

							<hr>
							<table class='table table-bordered' id='tblForApproval'>
								<thead>
								<tr>
									<th>Item</th><th  class='text-right'>Jan</th><th  class='text-right'>Feb</th><th  class='text-right'>March</th><th  class='text-right'>April</th><th  class='text-right'>May</th><th  class='text-right'>June</th><th  class='text-right'>July</th><th  class='text-right'>August</th><th  class='text-right'>Sept</th><th  class='text-right'>Oct</th><th  class='text-right'>Nov</th><th  class='text-right'>Dec</th><th></th>
								</tr>
								</thead>
								<tbody>
								<tr v-for="y,index in yearlyData">
									<td style='width:250px'>{{index}}</td>
									<td v-for="x in 12" class='text-right'>
										{{ formatAmount(y[x]) }}
									</td>
									<td class='text-right'><strong>{{formatAmount(yearlyItem[index])}}</strong></td>
								</tr>
								</tbody>
								<tfoot>
								<tr>
									<th></th>
									<th v-for="ey in yearlyTotal" class='text-right'>
										{{ formatAmount(ey.a)}}
									</th>

								</tr>
								</tfoot>
							</table>
						</div>

					</div>


				</div>
			</div>
		</div>

	</div> <!-- end page content wrapper-->

	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style='width:70%;' >
			<div class="modal-content"  >
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id='mtitle'></h4>
				</div>
				<div class="modal-body" id='mbody'>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<script src='../js/vue3.js'></script>
	<script>

		var vm = new Vue({
			el:'#page-content-wrapper',
			data:{
				container : {summary:true,raw:false,yearly:false},
				form: { date_from:'',date_to:'',limit_by:'10' ,branch_id:'',except:''},
				form_raw: { date_from:'',date_to:''},
				orders:[],
				raws:[],
				yearlyData:[],
				yearlyTotal:[],
				yearlyItem:[],
				year:'',
				current_year: 0,
				prev_year: 0,
				order_asc:true,
				sales_asc:true
			},
			mounted: function(){

				var self = this;
				var date_from = $('#date_from');
				var date_to = $('#date_to');
				var date_from_raw = $('#date_from_raw');
				var date_to_raw = $('#date_to_raw');
				var branch_select2 = $('#branch_id');
				var branch_id_except = $('#branch_id_except');

				date_from.datepicker({
					autoclose:true
				}).on('changeDate', function(ev){
					date_from.datepicker('hide');
					self.form.date_from = date_from.val();
				});
				date_to.datepicker({
					autoclose:true
				}).on('changeDate', function(ev){
					date_to.datepicker('hide');
					self.form.date_to = date_to.val();
				});

				date_from_raw.datepicker({
					autoclose:true
				}).on('changeDate', function(ev){
					date_from_raw.datepicker('hide');
					self.form_raw.date_from = date_from_raw.val();
				});
				date_to_raw.datepicker({
					autoclose:true
				}).on('changeDate', function(ev){
					date_to_raw.datepicker('hide');
					self.form_raw.date_to = date_to_raw.val();
				});

				branch_select2.select2({placeholder: 'Search Branch' ,allowClear: true});


				branch_select2.on('change',function(){
					self.form.branch_id = branch_select2.val();
				});

				branch_id_except.select2({placeholder: 'Search Except' ,allowClear: true});


				branch_id_except.on('change',function(){
					self.form.except = branch_id_except.val();
				});


				self.getSummary();

			},
			methods: {
				formatAmount: function(x){
					return number_format(x,3);
				},
				showContainer: function(x){
					var self = this;
					self.hideContainer();
					if(x == 1){
						self.container.summary = true;
					} else if (x == 2){
						self.container.raw = true;
					} else if (x == 3){
						self.getYearlySummary();
						self.container.yearly = true;
					}
				},
				hideContainer: function(){
					var self = this;
					self.container =  {summary:false,raw:false,yearly:false};
				},
				getYearlySummary: function(){
					var self = this;

					$.ajax({
						url:'../ajax/ajax_reports.php',
						type:'POST',
						dataType:'json',
						data: {functionName:'rawSummaryYear', dt:self.year},
						success: function(data){
							self.yearlyData = data.results;
							self.yearlyTotal = data.total;
							self.yearlyItem = data.item_total;


						},
						error:function(){

						}
					});
				},
				getRaw: function(){
					var self = this;
					var con = $('#btn_raw');
					button_action.start_loading(con);
					$.ajax({
						url:'../ajax/ajax_reports.php',
						type:'POST',
						dataType:'json',
						data: {functionName:'rawSummary', form:JSON.stringify(self.form_raw)},
						success: function(data){

							self.raws = data.results;

							button_action.end_loading(con);


						},
						error:function(){
							button_action.end_loading(con);
						}
					});
				},
				getSummary: function(){
					var self = this;
					$.ajax({
						url:'../ajax/ajax_reports.php',
						type:'POST',
						dataType:'json',
						data: {functionName:'orderSummary', form:JSON.stringify(self.form)},
						success: function(data){

							self.orders = data.results;
							self.sortOrder(1)



						},
						error:function(){

						}
					});
				},
				sortOrder: function(n){
					var self = this;
					if(n == 1){
						self.order_asc = !self.order_asc;

						self.orders.sort(function(a, b) {
							if(self.order_asc)
								return parseFloat(a.total_order) - parseFloat(b.total_order);
							else if(!self.order_asc)
								return parseFloat(b.total_order) - parseFloat(a.total_order);

						});
					} else if (n == 2){
						self.sales_asc = !self.sales_asc;

						self.orders.sort(function(a, b) {
							if(self.sales_asc)
								return parseFloat(a.total_sales) - parseFloat(b.total_sales);
							else if(!self.sales_asc)
								return parseFloat(b.total_sales) - parseFloat(a.total_sales);

						});
					}

				}
			}
		});

	</script>

	<script type="text/javascript">

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>