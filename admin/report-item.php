<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('r_item')) {
		// redirect to denied page
		Redirect::to(1);
	}

	$branch = new Branch();
	$branches = $branch->branchJSON($user->data()->company_id);

	$service_only = $user->hasPermission('r_service_only') ? 1 : 0;
	
	$no_price = $user->hasPermission('r_item_no');
	$no_price = ($no_price) ? 1 : 0;

?>


	<!-- Page content -->
	<div id="page-content-wrapper">
		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Item </h1>

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
				<div class="panel-heading">
					<div class="row">
						<div class="col-md-6">Item report</div>
						<div class="col-md-6 text-right">

						</div>
					</div>

				</div>
				<div class="panel-body">
					<input type="hidden" id='NO_PRICE' value='<?php echo $no_price; ?>'>
					<div class="btn-group hidden-xs" role="group" aria-label="..." style='margin-bottom:10px;'>
						<a class='btn btn-default' @click.prevent="showCon(1)" title='General Summary'>
							<span class='glyphicon glyphicon-list'></span>
							<span class='hidden-xs'>General Summary</span>
						</a>
						<?php if(!$service_only){

							?>
							<a class='btn btn-default' @click.prevent="showCon(2)"   title='Yearly Summary'>
								<span class='fa fa-calendar'></span> <span class='hidden-xs'>Yearly Summary</span>
							</a>
							<?php

						}?>


					</div>
					<div v-show="con.summary">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<select name="type" id="type" v-model='form.type' class='form-control'>
										<option value="1">By Item</option>
										<option value="2">By Category</option>
									</select>
								</div>
							</div>
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
									<input name="member_id" class='form-control' v-model='form.member_id' id="member_id">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select name="limit_by" id="limit_by" v-model='form.limit_by' class='form-control'>
										<option value="5">Limit by 5 records</option>
										<option value="10">Limit by 10 records</option>
										<option value="20">Limit by 20 records</option>
										<option value="50">Limit by 50 records</option>
										<option value="100">Limit by 100 records</option>
										<option value="100000">All</option>
									</select>
								</div>
							</div>
						</div>
						<div class='row'>
							<div class="col-md-3">
								<div class="form-group">
									<?php if($service_only){
										?>										<input type="hidden" value='1' id='hid_type'>
									<?php } ?>
									<select class='form-control' name="item_type" id="item_type" v-model='form.item_type'>
										<?php if(!$service_only){
										?>
										<option value="0"> ALL</option>
										<option value="-1"> Main sales</option>
										<?php } ?>
										<option value="1"> Service sales</option>
									</select>
								</div>
							</div>
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
							<div class="col-md-3">
								<div class="form-group">
									<select name="sort_type" id="sort_type" class='form-control' v-model='form.sort_type'>
										<option value="1">Sort by Total Amount</option>
										<option value="2">Sort by  Total Qty</option>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
								<select name="date_type" id="date_type" v-model='form.date_type' class='form-control'>
									<option value="0">All Status</option>
									<option value="1">Delivered and Picked Up Only</option>
								</select>
								<span class='help-block'>Date type</span>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<button class='btn btn-default' id='btnSubmit' @click="getItem()">Submit</button>
								</div>
							</div>
							<div class="col-md-6">

							</div>
							<div class="col-md-3 text-right">
								<button  class='btn btn-default' id='btnDownload' @click="getDownload()"><i class='fa fa-download'></i></button>
							</div>

						</div>
						<br>
						<div v-show="ajax.pending">Loading...</div>

						<table class='table table-bordered' v-show="items.length">
							<thead>

							<tr>
								<th>{{ ( form.type == 1) ? 'Item' : 'Category'}}</th>

								<th>Quantity</th>
								<th v-show="form.type == 1 && no_price == '0'">Cost</th>
								<th v-show="no_price == '0'">Total</th>
							</tr>
							</thead>
							<tbody>
							<tr v-for="item in items">
								<td style='border-top:1px solid #ccc;' v-html="item.name"></td>
								<td  style='border-top:1px solid #ccc;' class='text-danger'  v-text="item.qty"></td>
								<td  v-show="form.type == 1 && no_price == '0'" style='border-top:1px solid #ccc;' class='text-danger'  v-text="item.cost"></td>
								<td   v-show="no_price == '0'" style='border-top:1px solid #ccc;'  class='text-danger' v-text="item.total"></td>
							</tr>
							</tbody>
							<tfoot>
							<tr>
								<th></th>


								<th>{{ totals.qty_current }}</th>
								<th  v-show="form.type == 1 && no_price == '0'">{{ totals.cost_current }}</th>

								<th   v-show="no_price == '0'">{{ totals.total_current }}</th>
							</tr>
							</tfoot>
						</table>
						<div v-show="!items.length" class='alert alert-info'>No record found.</div>

					</div> <!-- end summary-->
					<div v-show="con.yearly">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<select name="sum_branch_id" class='form-control' v-model='sum_branch_id' id="sum_branch_id">
										<option value=""></option>
										<?php
											foreach($branches as $b){
												echo "<option value='$b->id'>$b->name</option>";
											}
										?>
									</select>
									<span class='help-block'>Search Branch</span>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input name="year" class='form-control' placeholder='Enter Year' v-model='year' id="year">
									<span class='help-block'>Enter Year</span>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select class='form-control' @change="getYearlySummary" name="by_what" id="by_what" v-model='by_what'>
										<option value="1"> Total Amount</option>
										<option value="2"> Total Quantity</option>
										<span class='help-block'>Data to show</span>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select class='form-control' @change="getYearlySummary" name="order_by" id="order_by" v-model='order_by'>
										<option value="1">By Total</option>
										<option value="2">By Category</option>
									</select>
									<span class='help-block'>Sorting</span>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<button class='btn btn-default' @click="getYearlySummary">Submit</button>
									<button class='btn btn-default' @click="downloadYearlySummary">Download</button>
								</div>
							</div>
						</div>
						<div id="yearly-holder">
							<hr>

							<div v-show="ajax.pending">Loading...</div>
							<div class="table-responsive">
							<table v-show="!ajax.pending" class='table table-bordered table-condensed' id='tblForApproval' style='padding:0px;font-size:9px !important; '>
								<thead>
								<tr>
									<th>Item</th><th  class='text-right'>Jan</th><th  class='text-right'>Feb</th><th  class='text-right'>March</th><th  class='text-right'>April</th><th  class='text-right'>May</th><th  class='text-right'>June</th><th  class='text-right'>July</th><th  class='text-right'>August</th><th  class='text-right'>Sept</th><th  class='text-right'>Oct</th><th  class='text-right'>Nov</th><th  class='text-right'>Dec</th><th>Total</th>
								</tr>
								</thead>
								<tbody>
								<tr v-for="y,index in yearlyData">
									<td style='width:250px'>
										{{index}}
										<span class='text-danger span-block'>{{categories[index]}}</span>
									</td>
									<td v-for="x in 12" class='text-right'>
										{{ formatAmount(y[x]) }}
									</td>
									<td class='text-right'>
										{{getTotal(y)}}
									</td>
								</tr>
								</tbody>
								<tfoot>
								<tr>
									<th></th>
									<th v-for="ey in yearlyTotal" class='text-right'>
										{{ formatAmount(ey.a)}}
									</th>
									<th class='text-right'>{{ totalYearly()}}</th>
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
				form: {date_type:'0',sort_type:'1',item_type:'0', type:'1', branch_id:'', member_id:'', date_from:'',date_to:'',limit_by:'10' },
				items: [],
				con: {summary:true, yearly: false},
				yearlyData:[],
				yearlyTotal:[],
				year:'',
				sum_branch_id:'',
				categories:[],
				by_what:'1',
				order_by:'1',
				current_year: 0,
				no_price: '0',
				prev_year: 0,
				sum_branch_id: 0,
				totals: {},
				type:'1',
				ajax: { pending:false }
			},
			mounted: function(){

				var self = this;
				if($('#hid_type').val() == '1'){
					self.form.item_type = '1';
				}
				self.getItem();
				var mem_select2 = $('#member_id');
				var branch_select2 = $('#branch_id');
				var sum_branch_id = $('#sum_branch_id');

				self.no_price = $('#NO_PRICE').val();
				if(self.no_price == '1'){
					self.by_what = '2';
					self.form.sort_type = '2';
					$('#by_what').attr('disabled',true);
					$('#sort_type').attr('disabled',true);
				}

				mem_select2.select2({
					placeholder: 'Search Client' , allowClear: true, minimumInputLength: 2,
					ajax: {
						url: '../ajax/ajax_json.php', dataType: 'json', type: "POST", quietMillis: 50, data: function(term) {
							return {
								q: term, functionName: 'members'
							};
						}, results: function(data) {
							return {
								results: $.map(data, function(item) {

									return {
										text: item.lastname + ", " + item.sales_type_name,
										slug: item.lastname + ", " + item.firstname + " " + item.middlename,
										id: item.id
									}
								})
							};
						}
					}
				});

				branch_select2.select2({placeholder: 'Search Branch' ,allowClear: true});
				sum_branch_id.select2({placeholder: 'Search Branch' ,allowClear: true});


				branch_select2.on('change',function(){
					self.form.branch_id = branch_select2.val();
				});

				sum_branch_id.on('change',function(){
					self.sum_branch_id = sum_branch_id.val();
				});

				mem_select2.on('change',function(){
					self.form.member_id = mem_select2.val();
				});


				var date_from = $('#date_from');
				var date_to = $('#date_to');

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

			},
			methods: {
				totalYearly: function(){
					var total = 0;
					var yy =  this.yearlyTotal;
					for(var i in yy){
						if(yy[i].a){
							total = parseFloat(yy[i].a) + parseFloat(total);

						}

					}
					return this.formatAmount(total);
				},
				getTotal: function(y){
					var total = 0;
					for(var i =1;i<=12;i++){
						if(y[i]){
							total = parseFloat(y[i]) + parseFloat(total);

						}

					}
					return this.formatAmount(total);
				},
				formatAmount: function(x){
					if(this.by_what == 1){
						return number_format(x,2);
					} else {
						return number_format(x);
					}

				},
				showCon: function(x){
					var self = this;
					self.con = { summary: false, yearly: false};
					if(x == 1){
						self.con.summary = true;
					} else if(x == 2){
						self.con.yearly = true;
						self.getYearlySummary();
					}
				},
				getYearlySummary: function(){
					var self = this;
					self.yearlyData = [];
					self.yearlyTotal = [];

					if(self.ajax.pending == true) return;

					self.ajax.pending = true;

					$.ajax({
						url:'../ajax/ajax_reports.php',
						type:'POST',
						dataType:'json',
						data: {functionName:'itemSummary', dt:self.year,by_what:self.by_what,branch_id:self.sum_branch_id,order_by:self.order_by},
						success: function(data){
							self.yearlyData = data.results;
							self.yearlyTotal = data.total;
							self.categories = data.categories;


							self.ajax.pending = false;
						},
						error:function(){
							self.ajax.pending = false;
						}
					});
				},
				downloadYearlySummary: function(){
					var self = this;

					window.open(
						'../ajax/ajax_reports.php?functionName=downloadItemSummary&dt='+self.year+"&by_what="+self.by_what+"&branch_id="+self.sum_branch_id+"&order_by="+self.order_by,
						'_blank' // <- This is what makes it open in a new window.
					);

				},
				getItem: function(){
					var self = this;

					if(self.ajax.pending){
						return;
					}

					self.ajax.pending = true;

					$.ajax({
						url:'../ajax/ajax_reports.php',
						type:'POST',
						dataType:'json',
						data: {functionName:'item', form:JSON.stringify(self.form)},
						success: function(data){
							self.items = data.results;
							self.current_year = data.current_year;
							self.prev_year = data.prev_year;
							self.totals = data.totals;
							self.ajax.pending = false;
						},
						error:function(){
							self.ajax.pending = false;
						}
					});
				},
				getDownload: function(){
					var self = this;

					window.open(
						'../ajax/ajax_reports.php?functionName=itemDownload&form='+JSON.stringify(self.form),
						'_blank' // <- This is what makes it open in a new window.
					);
				}
			}
		});

	</script>

	<script type="text/javascript">

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>