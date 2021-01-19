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
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Client </h1>

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
				<div class="panel-heading">Client</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-9">
							<div class="btn-group hidden-xs" role="group" aria-label="..." style='margin-bottom:10px;'>
								<a class='btn btn-default' @click.prevent="showCon(1)" title='General Summary'>
									<span class='glyphicon glyphicon-list'></span>
									<span class='hidden-xs'>General Summary</span>
								</a>

								<a class='btn btn-default' @click.prevent="showCon(2)"   title='Statement Of Account'>
									<span class='fa fa-list-alt'></span> <span class='hidden-xs'>Statement Of Account</span>
								</a>
								<a class='btn btn-default' @click.prevent="showCon(3)"   title='Yearly Summary'>
									<span class='fa fa-calendar'></span> <span class='hidden-xs'>Yearly Summary</span>
								</a>
							</div>
						</div>
						<div class="col-md-3 text-right">
							<a href="report-member-summary.php" class='btn btn-default'>Client Summary</a>
						</div>
					</div>

					<div v-show="con.summary">
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
						<div class="col-md-3">
							<div class="form-group">
								<select name="branch_id" id="branch_id" class='form-control' v-model='form.branch_id'>
									<option value=""></option>
									<?php
										if($branches){
											foreach($branches as $b){
												echo "<option value='$b->id'>$b->name</option>";
											}
										}
									?>
								</select>
							</div>
						</div>
						<div class="col-md-3">
							<select name="sales_type_id" id="sales_type_id" class='form-control'>
								<option value=""></option>
								<?php
									$sales_type = new Sales_type();
									$sales_types = $sales_type->get_active('salestypes',array('company_id','=',$user->data()->company_id));


									foreach($sales_types as $st){
										echo  "<option value='$st->id'>$st->name</option>";
									}


								?>
							</select>
						</div>

					</div>

					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<select name="type" id="type" v-model='form.type' class='form-control'>
									<option value="1">Sales</option>
									<option value="2">Credits</option>

								</select>
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
									<option value="500">Limit by 500 records</option>
									<option value="1000">Limit by 1000 records</option>
									<option value="100000">Show all</option>
								</select>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<select v-model='form.date_type'  name="date_type" id="date_type" class='form-control'>
									<option value="0">All Status</option>
									<option value="1">Delivered and Picked Up Only</option>
								</select>
								<span class='help-block'>Date type</span>
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<button class='btn btn-default' @click="getMember()">Submit</button>
								<button class='btn btn-default' @click="downloadMember()">Download</button>
							</div>
						</div>
					</div>
					<br>
					<div v-show="ajax.pending">Loading...</div>
					<table class='table table-bordered' v-show="members.length">
						<thead>

						<tr>
							<th>Client</th>
							<th>Type</th>
							<th class='text-right'>Total</th>
						</tr>
						</thead>
						<tbody>
						<tr v-for="member in members">
							<td style='border-top:1px solid #ccc;' v-html="member.member_name"></td>
							<td style='border-top:1px solid #ccc;' v-html="member.sales_type_name"></td>
							<td class='text-right' style='border-top:1px solid #ccc;'  class='text-danger' v-text="member.total"></td>
						</tr>
						</tbody>
						<tfoot>
						<tr>
							<th></th>
							<th></th>
							<th class='text-right'>{{member_total}}</th>
						</tr>
						</tfoot>
					</table>
					<div v-show="!members.length" class='alert alert-info'>No record found.</div>

					</div>

					<div v-show="con.soa">

						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<input name="member_id" class='form-control' v-model='member_id' id="member_id">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<button class='btn btn-default' @click="getSOA">Get SOA</button>
								</div>
							</div>
						</div>
						<div id="soa-holder"></div>

					</div>

					<div v-show="con.yearly">

						<div class="row">
							<div class="col-md-2">
								<div class="form-group">
									<input name="year" class='form-control' @keyup="removeFromTo" placeholder='Enter Year' v-model='year' id="year">
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group text-center text-danger">
									<strong>OR</strong>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group text-center text-danger">
									<input name="sum_from" class='form-control' placeholder='Enter Date From' v-model='sum_from' id="sum_from">
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group text-center text-danger">
									<input name="sum_to" class='form-control' placeholder='Enter Date To' v-model='sum_to' id="sum_to">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<button class='btn btn-default' @click="getYearlySummary">Submit</button>
									<button id="btnExport" class='btn btn-primary pull-right' @click="fnExcelReport"> EXPORT </button>
								</div>
							</div>
						</div>
						<div id="yearly-holder" style='font-size: 5px;'>
							<div v-show="ajax.pending">Loading...</div>
							<hr>
							<div class='table-responsive'>


							<table class='table table-bordered' id='exportTbl' style='font-size: 5px;'>
								<thead>
									<tr>
										<th>Client</th>
										<th>Sales type</th>
										<th v-for="mm in yearlyKeys">{{mm}}</th>

										<th></th>
									</tr>
								</thead>
								<tbody>
								<tr v-for="y,index in yearlyData">
									<td style='width:250px' v-html="index"></td>
									<td style='width:250px'>{{st[index]}}</td>
									<td v-for="x in yearlyKeys" class='text-right' >
										<span style='font-size:1em !important;'>
										{{ formatAmount(y[x]) }}
											</span>
									</td>
									<td  class='text-right' >{{ formatAmount(member_total[index]) }}</td>
								</tr>
								</tbody>
								<tfoot>
								<tr>
									<th></th>
									<th></th>
									<th v-for="ey in yearlyTotal" class='text-right'>
										{{ formatAmount(ey.a)}}
									</th>
									<th></th>
								</tr>
								</tfoot>
							</table>
							</div>
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

	<script src='https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.3/FileSaver.min.js'></script>
	<script src='https://cdnjs.cloudflare.com/ajax/libs/TableExport/4.0.11/js/tableexport.min.js'></script>
	<script src='../js/vue3.js'></script>
	<script>

		var vm = new Vue({
			el:'#page-content-wrapper',
			data:{
				form: {date_type:'0', type:'1', date_from:'',date_to:'',limit_by:'10' ,branch_id:'',sales_type_id:''},
				con: {summary:true, soa: false, yearly: false},
				members: [],
				member_id:'',
				year:'',
				yearlyData:[],
				yearlyTotal:[],
				yearlyKeys:[],
				st:[],
				member_total: [],
				sum_from:'',
				sum_to:'',
				member_total:'',
				ajax: { pending:false }
			},
			mounted: function(){

				var self = this;
				self.getMember();

				var date_from = $('#date_from');
				var date_to = $('#date_to');
				var sum_from = $('#sum_from');
				var sum_to = $('#sum_to');

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
				sum_from.datepicker({
					autoclose:true
				}).on('changeDate', function(ev){
					sum_from.datepicker('hide');
					self.sum_from = sum_from.val();
					self.year = '';
				});

				sum_to.datepicker({
					autoclose:true
				}).on('changeDate', function(ev){
					sum_to.datepicker('hide');
					self.sum_to = sum_to.val();
					self.year = '';
				});

				var mem_select2 = $('#member_id');


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

				mem_select2.on('change',function(){
					self.member_id = mem_select2.val();
				});


				$('body').on('click','#btnPrintSOA',function(){
					var member_id = $(this).attr('data-member_id');
					self.printSOA(member_id);
				});
				$('body').on('click','#btnExcelSOA',function(){
					var member_id = $(this).attr('data-member_id');
					self.excelSOA(member_id);
				});

				$('#branch_id').select2({placeholder: 'Search Branch' ,allowClear: true});
				$('body').on('change','#branch_id',function(){
					self.form.branch_id = $(this).val();
				});
				$('#sales_type_id').select2({placeholder: 'Search Type' ,allowClear: true});
				$('body').on('change','#sales_type_id',function(){
					self.form.sales_type_id = $(this).val();
				});
			},
			computed: {

			},
			methods: {
				totalByClient: function(y){

				},
				removeFromTo: function(){
					var self = this;
					self.sum_from = '';
					self.sum_to = '';
					$('#sum_from').val('');
					$('#sum_to').val('');
				},
				formatAmount: function(x){
					return number_format(x,2);
				},
				downloadMember: function(){

					var self = this;

					window.open(
						'../ajax/ajax_reports.php?functionName=memberDownload&form='+JSON.stringify(self.form),
						'_blank' // <- This is what makes it open in a new window.
					);

				},
				getMember: function(){

					var self = this;
					self.ajax.pending = true;

					$.ajax({
						url:'../ajax/ajax_reports.php',
						type:'POST',
						dataType:'json',
						data: {functionName:'member', form:JSON.stringify(self.form)},
						success: function(data){
							self.members = data.results;
							self.member_total = data.total;
							self.ajax.pending = false;
						},
						error:function(){
							self.ajax.pending = false;
						}
					});

				},
				showCon: function(x){
					var self = this;
					self.con = { summary: false, soa: false, yearly: false};
					if(x == 1){
						self.con.summary = true;
					} else if(x == 2){
						self.con.soa = true;

					} else if(x == 3){
						self.con.yearly = true;
						self.getYearlySummary();
					}
				},
				getSOA: function(){
					var self = this;
					var member_id = self.member_id;
					$('#soa-holder').html("Loading...");
					$.ajax({
						url:'../ajax/ajax_accounting.php',
						type:'POST',
						data: {functionName:'getSOA',member_id:member_id},
						success: function(data){
							$('#soa-holder').html(data);
						},
						error:function(){

						}
					})
				},
				getYearlySummary:function(){
					var self = this;
					self.ajax.pending = true;
					$.ajax({
						url:'../ajax/ajax_reports.php',
						type:'POST',
						dataType:'json',
						data: {functionName:'memberSummary', dt:self.year, dt_from: self.sum_from,dt_to:self.sum_to},
						success: function(data){
							self.yearlyData = data.results;
							self.yearlyTotal = data.total;
							self.yearlyKeys = data.keys;
							self.st = data.st;
							self.member_total = data.member_total;


							self.ajax.pending = false;
						},
						error:function(){
							self.ajax.pending = false;
						}
					});
				},
				printSOA: function(member_id){
					window.open(
						'../ajax/ajax_accounting.php?functionName=samplePrint&member_id='+member_id,
						'_blank' // <- This is what makes it open in a new window.
					);
				},
				excelSOA: function(member_id){
					window.open(
						'../ajax/ajax_accounting.php?functionName=excelSOA&member_id='+member_id,
						'_blank' // <- This is what makes it open in a new window.
					);
				},
				fnExcelReport: function()
				{

					$("#exportTbl").tableExport(
						{
							bootstrap: true,
							formats: [ 'csv', 'txt'] ,
							position: 'bottom'
						});
				}
			}

		});

	</script>

	<script type="text/javascript">

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>