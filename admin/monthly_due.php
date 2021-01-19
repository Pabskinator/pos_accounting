<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('member')) {
		// redirect to denied page
		Redirect::to(1);
	}

	$profit_center = [
		'Monthly dues',
		'Ads',
		'Consumable',
		'Machine',
		'Interest',
		'Penalty',
		'Other'
	];

?>
	<div id='monthly_due_app'>

	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Monthly Dues
			</h1>

		</div>
		<div class="btn-group hidden-xs" role="group" aria-label="..." style='margin-bottom:10px;'>
			<a class='btn btn-default btn_nav' @click="container = {list: true,details: false, new_request:false}" title='List' href='#'>
				<span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>List View</span>
			</a>
			<a class='btn btn-default btn_nav' @click="showDetailsCon()" title='List' href='#'>
				<span class='glyphicon glyphicon-list-alt'></span> <span class='hidden-xs'>Details View</span>
			</a>
			<a class='btn btn-default btn_nav' @click="container = {list: false,details: false, new_request:true}" title='Request' href='#'>
				<span class='glyphicon glyphicon-pencil'></span> <span class='hidden-xs'>Request </span>
			</a>
		</div>
		<div class="panel panel-primary">
			<div class="panel-heading">
				<div class="row">
					<div class="col-md-6">
						List
					</div>
					<div class="col-md-6 text-right">
						<button v-if="false" v-on:click="showAddModal" class='btn btn-default'><i class='fa fa-plus'></i></button>
					</div>
				</div>
			</div>
			<div class="panel-body">
				<div class="container-fluid">
				<div class="row" v-show="container.list">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<input type="text" class='form-control'  id='s_member_id' v-model="s_member_id" >
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<input type="text" class='form-control' placeholder='Date From' id='s_date_from' v-model="s_date_from">
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<input type="text" class='form-control' placeholder='Date To' id='s_date_to' v-model="s_date_to">
							</div>
						</div>
						</div>

						<div class="row">

							<div class="col-md-3">
								<div class="form-group">
									<select v-model="s_profit_center" class="form-control">
										<?php
											foreach($profit_center as $p)
												echo "<option value='$p'>$p</option>";
										?>
									</select>
									<span class='help-block'>Profit Center</span>
								</div>
							</div>


							<div class="col-md-3">
								<button class='btn btn-default' @click="getRecord(1)"> Filter </button>
								<button class='btn btn-default' @click="dlMonthlyDues()"> Download </button>
							</div>
						</div>
					<table class='table' id='tblForApproval' v-show="dues.length">
						<thead>
						<tr>
							<th>ID</th>
							<th>Created</th>
							<th>Member</th>
							<th>Profit Center</th>
							<th>PR</th>
							<th>Bank</th>
							<th>Monthly Due</th>
							<th>Total Paid</th>
							<th>Remaining</th>
							<th>Action</th>
						</tr>
						</thead>
						<tbody>
							<tr v-for="due in dues">
								<td>
									{{due.id}}
								</td>
								<td>
									{{due.created_at}}
								</td>
								<td>
									{{due.member_name}}
									<div class='text-danger'>
										{{ due.station_name }}
									</div>
									<div class='text-danger'>
										{{ due.covered_period }}
									</div>
								</td>
								<td>
									{{due.profit_center}}
								</td>	<td>
									{{due.ctrl_num}}
								</td>
								<td>
									{{ due.bank }}
								</td>
								<td>
									{{due.dues}}
								</td>
								<td>
									{{due.total_paid}}
								</td>
								<td>
									{{due.dues - due.total_paid}}
								</td>
								<td>
									<button class='btn btn-default btn-sm' @click="showDetails(due)">
										<i class='fa fa-list'></i>
									</button>
									<?php if($user->hasPermission('u_dues')){
										?>
										<button @click="deleteDue(due)" class='btn btn-default btn-sm'>
											<i class='fa fa-trash'></i>
										</button>
										<?php
									}?>

								</td>
							</tr>
						</tbody>
					</table>
					<div class="alert alert-info" v-show="!dues.length">
						No record found.
					</div>
				</div> <!-- END LIST VIEW -->
					<div v-show="container.details">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control'  id='d_member_id' v-model="d_member_id" >
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' placeholder='Date From' id='d_date_from' v-model="d_date_from">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' placeholder='Date To' id='d_date_to' v-model="d_date_to">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select name="d_payment_method" id="d_payment_method" class='form-control'  v-model="d_payment_method">
										<option value="0">Select Payment Type</option>
										<option value="1">Cash</option>
										<option value="2">Cheque</option>
										<option value="3">Credit Card</option>
									</select>


								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<select v-model="d_profit_center" class="form-control">
										<?php
											foreach($profit_center as $p)
												echo "<option value='$p'>$p</option>";
										?>
									</select>
									<span class='help-block'>Profit Center</span>
								</div>
							</div>
							<div class="col-md-3">
								<button class='btn btn-default' @click="getRecordDetails()"> Filter </button>
								<button class='btn btn-default' @click="dlMonthlyDuesDetails()"> Download </button>
							</div>
						</div>
						<br>
						<table class='table table-bordered'>
							<thead>
							<tr>
								<th>Date Collected</th>
								<th>Date Received</th>
								<th>Client</th>
								<th>PR</th>
								<th>Remarks</th>
								<th>Maturity</th>
								<th>Payment Method</th>
								<th>Check No</th>
								<th>Total</th>
								<th>Notes</th>
							</tr>
							</thead>
							<tbody>
								<tr v-for="dd in due_details">
									<td style='border-top:1px solid #ccc;'>{{dd.dt_collected}}</td>
									<td style='border-top:1px solid #ccc;'>{{dd.date_received}}</td>
									<td style='border-top:1px solid #ccc;'>{{dd.member_name}}</td>
									<td style='border-top:1px solid #ccc;'>{{dd.ctrl_num}}</td>
									<td style='border-top:1px solid #ccc;'>{{dd.remarks}}</td>
									<td style='border-top:1px solid #ccc;'>{{dd.date_matured}}</td>
									<td style='border-top:1px solid #ccc;'>{{dd.payment_type}}</td>
									<td style='border-top:1px solid #ccc;'>{{dd.check_number}}</td>
									<td style='border-top:1px solid #ccc;'>{{dd.amount}}</td>
									<td style='border-top:1px solid #ccc;'>{{dd.profit_center}}</td>
								</tr>
							</tbody>
						</table>
					</div><!-- END DETAILS VIEW-->
					<div v-show="container.new_request">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' id='member_id_req' v-model="new_req.member_id" >
								</div>
							</div>


							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control'  v-model="new_req.total_amount" placeholder="Total Amount">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select v-model="new_req.profit_center" class="form-control">
										<?php
											foreach($profit_center as $p)
												echo "<option value='$p'>$p</option>";
										?>
									</select>
								</div>
							</div>
						</div>
						<div>
						</div>
						<div class="row">

							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' autocomplete="off" id='nr_covered_period'  v-model="new_req.covered_period" placeholder="Covered Period">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' autocomplete="off" id='nr_bank' v-model="new_req.bank" placeholder="Bank">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control'  v-model="new_req.pr" placeholder="PR">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select style='display:none;'  v-model="new_req.station_id" name="station_id" id="station_id" class='form-control'>

									</select>
								</div>
							</div>
						</div>
						<hr>
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' placeholder="Monthly Amount" v-model='new_req.monthly_amount'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' id='new_req_date' placeholder='Due Date' v-model='new_req.due_date'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select v-model="new_req.status" class="form-control">
										<option value="Pending">Pending</option>
										<option value="Processed">Processed</option>
										<option value="Hold">Hold</option>
									</select>
								</div>
							</div>

						</div>
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
								<select v-model="new_req.payment_type" class="form-control">
									<option value="1">Cash</option>
									<option value="2">Cheque</option>
									<option value="3">Credit</option>
								</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
								<input type="text" v-model="new_req.date_received" id='nr_date_received' class='form-control' placeholder='Date Received'>
							</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
								<input type="text" v-model="new_req.remarks" class='form-control' placeholder='Remarks'>
							</div>
							</div>
							<div v-show="new_req.payment_type == '2'">
								<div class="col-md-3">
									<div class="form-group">
									<input type="text" class='form-control' id='new_req_date_matured' placeholder='Maturity' v-model='new_req.date_matured'>
								</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
									<input type="text" class='form-control'  placeholder='Cheque Number' v-model='new_req.cheque_number'>
								</div>
								</div>
							</div>
							<div v-show="new_req.payment_type == '3'">
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" class='form-control'  placeholder='Bank Name' v-model='new_req.cc_bank'>
									</div>
								</div>

							</div>
							<div class="col-md-3">
								<div class="form-group">
									<button class='btn btn-primary' @click="addRecord()">Add Record</button>
								</div>
							</div>
						</div>

						<br>
						<table class='table table-bordered table-condensed topBordered' v-show="new_req_items.length">
							<thead>
							<tr><th>Due</th><th>Received</th><th>Amount</th><th>Status</th><th>Type</th><th></th></tr>
							</thead>
							<tbody>
							<tr v-for="item in new_req_items">
								<td>{{item.due_date}}</td>
								<td>{{item.date_received}}</td>
								<td>{{item.monthly_amount}}</td>
								<td>{{item.status}} <span class='span-block text-danger'>{{item.remarks}}</span></td>
								<td>

									<div v-show="item.payment_type == '1'">Cash</div>
									<div v-show="item.payment_type == '2'">
										Cheque
										<small class='span-block'>{{ item.cheque_number }}</small>
										<small class='span-block'>{{ item.date_matured }}</small>
									</div>
									<div v-show="item.payment_type == '3'">
										Credit Card
										<small class='span-block'>{{ item.cc_bank }}</small>
									</div>

								</td>
								<td><button @click="removeItem(item)"><i class='fa fa-trash'></i></button></td>
							</tr>
							</tbody>
						</table>
						<br>
						<div class="text-right">
							<button class='btn btn-default' v-show="new_req_items.length" @click="submitDueDetails">Submit</button>
						</div>

						<div class="alert alert-info" v-show="!new_req_items.length">
							No item yet.
						</div>
					</div>
				</div>
			</div>
		</div>
		</div>
	</div> <!-- end page content wrapper-->
		<div class="modal fade" id="myModalList" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title">Breakdown for request id # {{cur_due.id}}</h4>
						</div>
						<div class="modal-body">

							<p>Member: <strong>{{cur_due.member_name}}</strong></p>
							<?php if($user->hasPermission('a_dues')|| $user->hasPermission('u_dues')){ ?>
							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
									<input type="text" class='form-control' id='dt_due_date'  v-model="update_item.due_date" placeholder='Due Date'>
										</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
									<input type="text" class='form-control' id='dt_date_received' v-model="update_item.date_received" placeholder='Received Date'>
										</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" class='form-control' v-model='update_item.amount' placeholder='Amount'>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">

										<select class='form-control' v-model="update_item.status">
										<option value="Pending">Pending</option>
										<option value="Processed">Processed</option>
										<option value="Hold">Hold</option>
										</select>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
									<select v-model="update_item.payment_type" class="form-control">
										<option value="1">Cash</option>
										<option value="2">Cheque</option>
										<option value="3">Credit</option>
									</select>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" class='form-control' v-model='update_item.remarks' placeholder='Remarks'>
									</div>
								</div>
								<div v-show="update_item.payment_type == '2'">
									<div class="col-md-3">
										<div class="form-group">
										<input type="text" class='form-control'  placeholder='Maturity' v-model='update_item.date_matured'>
									</div>	</div>
									<div class="col-md-3">
										<div class="form-group">
										<input type="text" class='form-control'  placeholder='Cheque Number' v-model='update_item.cheque_number'>
										</div>
									</div>
								</div>
								<div v-show="update_item.payment_type == '3'">
									<div class="col-md-3">
										<div class="form-group">
										<input type="text" class='form-control'  placeholder='Bank Name' v-model='update_item.cc_bank'>
									</div>	</div>

								</div>
								<div class="col-md-3">
									<div class="form-group">
									<button  class='btn btn-default' @click="addNewDetail">Add New</button>
								</div>	</div>
							</div>
							<?php } ?>

							<table class='table' v-show="details.length">
								<thead>
								<tr>

									<th>Due Date</th>
									<th>Received Date</th>
									<th>Amount</th>
									<th>Type</th>
									<th>Status</th>
									<th>Remarks</th>
									<th></th>
								</tr>
								</thead>
								<tbody>
									<tr v-for="det in details">

										<td>


											<input class='form-control' type="text" v-model="det.dt_collected">
										</td>
										<td>
											{{ det.date_received }}
										</td>
										<td>
											{{ det.amount }}
										</td>
										<td>

											<div v-show="det.payment_type == '1'">
												Cash
											</div>
											<div v-show="det.payment_type == '2'">
												Cheque
												<small class='span-block'>{{det.check_number}}</small>
												<small class='span-block'>{{det.date_matured}}</small>
											</div>
											<div v-show="det.payment_type == '3'">
												Credit
												<small class='span-block'>{{det.cc_bank}}</small>
											</div>
										</td>
										<td>
											<?php if($user->hasPermission('u_dues')){
												?>
												<select class='form-control' v-model="det.status">
													<option value="Pending">Pending</option>
													<option value="Processed">Processed</option>
													<option value="Hold">Hold</option>
												</select>
												<?php
											} else {
												?>
												{{ det.status }}
											<?php
											}?>

										</td>
										<td>
											<input class='form-control' type="text" v-model="det.remarks">

										</td>
										<td>
											<?php if($user->hasPermission('u_dues')){
											?>
											<button @click="deleteDetails(det)" class='btn btn-default btn-sm'><i class='fa fa-trash'></i></button>
												<?php
											}?>
										</td>
									</tr>
								</tbody>
							</table>
							<div class="text-right" v-show="details.length">
								<?php if($user->hasPermission('u_dues')){
								?>
								<button class='btn btn-default' @click="saveDetails">Save</button>
								<?php } ?>
							</div>
							<div class="alert alert-info" v-show="!details.length">
								No record found.
							</div>
						</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id='mtitle'>Details</h4>
					</div>
					<div class="modal-body" id='mbody'>

						<div class="form-group">
							<strong>Member Name</strong>
							<input type="text" class='form-control' id='member_id' v-model="form.member_id">
						</div>

						<div class="form-group">
							<strong>Month Due</strong>
							<input type="text" class='form-control'  v-model="form.dues">
						</div>

						<div class="form-group">
							<strong>Per month deduction</strong>
							<input type="text" class='form-control'  v-model="form.per_month">
						</div>

						<div class="form-group">
							<strong>Profit Center</strong>
							<select v-model="form.profit_center" class="form-control">
								<?php
									foreach($profit_center as $p)
										echo "<option value='$p'>$p</option>";
								?>
							</select>
						</div>

						<div class="form-group">
							<strong>Remarks</strong>
							<input type="text" class='form-control'  v-model="form.remarks">
						</div>
						<div class="form-group">
								<button class='btn btn-default' @click="submitDue">Submit</button>
						</div>
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
	</div>

	<script src='../js/vue3.js?v=2'></script>
	<script>

	var vue_model = new Vue({
		el:'#monthly_due_app',
		data: {
			container:{list:true,new_request:false},
			dues: [],
			cur_due:{},
			details:[],
			form: {member_id:'',per_month:'',dues:'',remarks:''},
			ajax_running:false,
			s_member_id:'',
			s_date_from:'',
			s_date_to:'',
			s_profit_center:'',
			d_member_id:'',
			d_date_from:'',
			d_date_to:'',
			d_payment_method:'0',
			d_profit_center:'',
			due_details:[],
			new_req: { date_received:'', covered_period:'',bank:'', cc_bank:'', station_id:'',pr:'',remarks:"",payment_type:'1', cheque_number:'', date_matured:'',member_id:'' , total_amount:'',profit_center:'', monthly_amount:'',status:'Pending',due_date:''},
			new_req_items:[],
			update_item: {date_received:'',cc_bank:'',mremarks:"",payment_type:'1', cheque_number:'', date_matured:'',cc_bank:'',amount:'',status:'Pending',due_date:''}
		},
		mounted(){

			var vm = this;
			vm.getRecord(1);

			var mem_select2 = $('#member_id');

			var search_member = $('#s_member_id');

			var s_date_from = $('#s_date_from');

			var s_date_to = $('#s_date_to');

			mem_select2.select2({
			placeholder: 'Search client', allowClear: true, minimumInputLength: 2,
				ajax: {
					url: '../ajax/ajax_json.php', dataType: 'json', type: "POST", quietMillis: 50, data: function(term) {
						return {
							q: term, functionName: 'members'
						};
					}, results: function(data) {
						return {
							results: $.map(data, function(item) {

								return {
									text: item.lastname,
									slug: item.lastname ,
									id: item.id
								}
							})
						};
					}
				}
		});

		$('#d_member_id').select2({
			placeholder: 'Search client', allowClear: true, minimumInputLength: 2,
			ajax: {
				url: '../ajax/ajax_json.php', dataType: 'json', type: "POST", quietMillis: 50, data: function(term) {
					return {
						q: term, functionName: 'members'
					};
				}, results: function(data) {
					return {
						results: $.map(data, function(item) {

							return {
								text: item.lastname,
								slug: item.lastname ,
								id: item.id
							}
						})
					};
				}
			}
		});

		search_member.select2({
				placeholder: 'Search client', allowClear: true, minimumInputLength: 2,
				ajax: {
					url: '../ajax/ajax_json.php', dataType: 'json', type: "POST", quietMillis: 50, data: function(term) {
						return {
							q: term, functionName: 'members'
						};
					}, results: function(data) {
						return {
							results: $.map(data, function(item) {

								return {
									text: item.lastname,
									slug: item.lastname ,
									id: item.id
								}
							})
						};
					}
				}
			});

		$('#member_id_req').select2({
				placeholder: 'Search client', allowClear: true, minimumInputLength: 2,
				ajax: {
					url: '../ajax/ajax_json.php', dataType: 'json', type: "POST", quietMillis: 50, data: function(term) {
						return {
							q: term, functionName: 'members'
						};
					}, results: function(data) {
						return {
							results: $.map(data, function(item) {

								return {
									text: item.lastname,
									slug: item.lastname ,
									id: item.id
								}
							})
						};
					}
				}
			});

		$('body').on('change','#member_id_req',function(){
			var v = $(this).val();
			$.ajax({
				url:'../ajax/ajax_wh_order.php',
				type:'POST',
				dataType:'json',
				data: {functionName:'getOwnedBranch',member_id:v},
				success: function(data){

					var my_station = data.stations;



					if(my_station.length > 0){
						var ret = "<option value='0'>Choose Station</option>";
						for(var i in my_station){
							ret += "<option value='"+my_station[i].id+"'>"+my_station[i].name+"</option>";
						}
						$('#station_id').show();
						$('#station_id').html(ret);
						//$('#spec_station_id').html(ret);
					} else {
						$('#station_id').hide();
						$('#station_id').html("<option value='0'>No Station</option>");
						//$('#spec_station_id').html("<option value='0'>No Station</option>");
					}

				},
				error:function(){

				}
			});
		});

			$('body').on('change','#member_id_req',function(){
				vm.new_req.member_id = $(this).val();
			});

			$('body').on('change','#member_id',function(){
				vm.form.member_id = $(this).val();
			});

			$('body').on('change','#s_member_id',function(){
				vm.s_member_id = $(this).val();
			});

			$('#new_req_date').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#new_req_date').datepicker('hide');
				vm.new_req.due_date= $('#new_req_date').val();
			});

			$('#new_req_date_matured').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#new_req_date').datepicker('hide');
				vm.new_req.date_matured = $('#new_req_date_matured').val();
			});

			$('#s_date_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#s_date_from').datepicker('hide');
				vm.s_date_from = $('#s_date_from').val();
			});
			$('#s_date_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#s_date_to').datepicker('hide');
				vm.s_date_to = $('#s_date_to').val();
			});

		$('body').on('change','#d_member_id',function(){
			vm.d_member_id = $(this).val();
		});
		$('#d_date_from').datepicker({
			autoclose:true
		}).on('changeDate', function(ev){
			$('#d_date_from').datepicker('hide');
			vm.d_date_from = $('#d_date_from').val();
		});
		$('#d_date_to').datepicker({
			autoclose:true
		}).on('changeDate', function(ev){
			$('#d_date_to').datepicker('hide');
			vm.d_date_to = $('#d_date_to').val();
		});


		$('#nr_date_received').datepicker({
			autoclose:true
		}).on('changeDate', function(ev){
			$('#nr_date_received').datepicker('hide');
			vm.new_req.date_received = $('#nr_date_received').val();
		});

		$('#dt_due_date').datepicker({
			autoclose:true
		}).on('changeDate', function(ev){
			$('#dt_due_date').datepicker('hide');
			vm.update_item.due_date = $('#dt_due_date').val();
		});
		$('#dt_date_received').datepicker({
			autoclose:true
		}).on('changeDate', function(ev){
			$('#dt_date_received').datepicker('hide');
			vm.update_item.date_received = $('#dt_date_received').val();
		});


		vm.loadUnsaved();
	},
		methods: {
			loadUnsaved: function(){
				var vm = this;
				if(localStorage['monthly_dues_details_bak']){
					alertify.confirm('You have unsaved request. Do you want to load it?', function(e) {
						if(e) {

								try{
									vm.new_req_items = JSON.parse(localStorage['monthly_dues_details_bak']);
									vm.container = {list: false,details: false, new_request:true};
								} catch(e){
									console.log("Error parsing");
								}

						}
					});
				}

			},
			addNewDetail:function (){
				var vm = this;
				$.ajax({
				    url:'../ajax/ajax_monthly_dues.php',
				    type:'POST',
				    data: {functionName:'addNewDetail',data: JSON.stringify(vm.update_item), cur_due: JSON.stringify(vm.cur_due) },
				    success: function(data){
					    tempToast('info',data,'Info');
					    vm.getRecord(1);
					    vm.showDetails(vm.cur_due);
					    vm.update_item.amount = "";
					    vm.update_item.due_date = "";
					    vm.update_item.date_received = "";
					    vm.update_item.remarks = "";
					    vm.update_item.date_matured = "";
					    vm.update_item.cheque_number = "";
					    vm.update_item.remarks = "";
					    vm.update_item.payment_type = "1";
					    vm.update_item.status="Pending";
				    },
				    error:function(){
				        
				    }
				});
			},
			saveDetails: function(){
				var vm = this;

				var details = vm.details;
				$.ajax({
				    url:'../ajax/ajax_monthly_dues.php',
				    type:'POST',
				    data: {functionName:'updateDetails', items:JSON.stringify(details)},
				    success: function(data){
					    tempToast('info',data,'Info');
					    vm.getRecord(1);
				    },
				    error:function(){

				    }
				});
			},
			submitDueDetails: function(){
				var vm = this;
				if(vm.new_req.member_id && vm.new_req.total_amount && vm.new_req.profit_center && vm.new_req.profit_center && vm.new_req.covered_period ){
					$.ajax({
						url:'../ajax/ajax_monthly_dues.php',
						type:'POST',
						data: {functionName:'submitDueDetails',items:JSON.stringify(vm.new_req_items),data:JSON.stringify(vm.new_req)},
						success: function(data){
							localStorage.removeItem('monthly_dues_details_bak');
							tempToast('info',data,'Info');
							vm.new_req =  { date_received:'', covered_period:'', bank:'', cc_bank:'',station_id:'', pr:'', remarks:'',payment_type:'1', cheque_number:'', date_matured:'', member_id:'' , total_amount:'',profit_center:'', monthly_amount:'',status:'Pending',due_date:''};
							vm.new_req_items = [];
							vm.getRecord(1);

						},
						error:function(){

						}
					});
				} else {
					tempToast('error','Please complete the form (Client name, total amount, profit center, etc..)','Warning');
				}


			},
			removeItem: function(item){
				var newitems = this.new_req_items.filter(function(e){
					return e.due_date !== item.due_date;
				});
				this.new_req_items = newitems;
				localStorage['monthly_dues_details_bak'] = JSON.stringify(this.new_req_items);
			},
			addRecord: function(){

				var vm = this;
				vm.new_req_items.push({ date_received: vm.new_req.date_received, remarks:vm.new_req.remarks,payment_type:vm.new_req.payment_type,cheque_number:vm.new_req.cheque_number,date_matured:vm.new_req.date_matured, monthly_amount:vm.new_req.monthly_amount,status:vm.new_req.status,due_date:vm.new_req.due_date,cc_bank:vm.new_req.cc_bank});
				localStorage['monthly_dues_details_bak'] = JSON.stringify(vm.new_req_items);
				vm.new_req.monthly_amount = "";
				vm.new_req.status = "Pending";
				vm.new_req.due_date = "";
				vm.new_req.date_received = "";
				vm.new_req.cheque_number = "";
				vm.new_req.date_matured = "";
				vm.new_req.payment_type = "1";
				vm.new_req.remarks = "";

			},

			deleteDetails: function(det){
				var vm = this;
				alertify.confirm("Are you sure you want to delete this record?", function(e){
					if(e){
						$.ajax({
							url:'../ajax/ajax_monthly_dues.php',
							type:'POST',
							data: {functionName:'deleteDetails',due: JSON.stringify(det)},
							success: function(data){
								tempToast('info',data,'Info');
								vm.showDetails(vm.cur_due);
								vm.getRecord(1);
							},
							error:function(){

							}
						});
					}
				});
			},
			deleteDue: function(due){
				var vm = this;
				alertify.confirm("Are you sure you want to delete this record?", function(e){
					if(e){
						$.ajax({
							url:'../ajax/ajax_monthly_dues.php',
							type:'POST',
							data: {functionName:'deleteDue',due: JSON.stringify(due)},
							success: function(data){
								tempToast('info',data,'Info');
								vm.getRecord(1);
							},
							error:function(){

							}
						});
					}
				});

			},
			showDetails: function (due){

				$('#myModalList').modal('show');
				var vm = this;
				vm.cur_due = due;
				console.log(JSON.stringify(due));
				$.ajax({
				    url:'../ajax/ajax_monthly_dues.php',
				    type:'POST',
					dataType:'json',
				    data: {functionName:'showDetails',due: JSON.stringify(due)},
				    success: function(data){
					    vm.details = data;
				    },
				    error:function(){

				    }
				});
			},
			receiveDues: function(due){
				var vm = this;
				alertify.confirm("Are you sure you want to continue this action?", function(e){
					if(e){
						if(!vm.ajax_running) {
							vm.ajax_running = true;

							$.ajax({
								url: '../ajax/ajax_monthly_dues.php',
								type: 'POST',
								data: {functionName: 'receiveDue', due: JSON.stringify(due)},
								success: function(data) {
									if(data == 1){
										tempToast('info', 'Inserted successfully', 'Info');
										vm.getRecord(1);
									} else {
										tempToast('error', data, 'Error');
									}
									vm.ajax_running = false;

								},
								error: function() {
									vm.ajax_running = false;
								}
							});
						}
					}
				});
			},
			submitDue: function(){

				var vm = this;

				if(!vm.ajax_running) {

					vm.ajax_running = true;
					$.ajax({
						url: '../ajax/ajax_monthly_dues.php',
						type: 'POST',
						data: {functionName: 'insertDue', form: JSON.stringify(vm.form)},
						success: function(data) {
							if(data == 1){
								tempToast('info', 'Inserted successfully', 'Info');
								$('#myModal').modal('hide');
								vm.form = {member_id:'',per_month:'',dues:'',remarks:''};
								$('#member_id').select2('val',null);
								vm.getRecord(1);
							} else {
								tempToast('error', data, 'Error');
							}
							vm.ajax_running = false;
						},
						error: function() {
							vm.ajax_running = false;
						}
					});
				}
			},
			getRecord: function(status){

				var vm = this;

				$.ajax({
				    url:'../ajax/ajax_monthly_dues.php',
				    type:'POST',
					dataType:'json',
				    data: {
					    functionName:'getMonthlyDues',
					    status:status,
					    member_id:vm.s_member_id,
					    date_from:vm.s_date_from,
					    date_to:vm.s_date_to,
					   profit_center:vm.s_profit_center,
				    },
				    success: function(data){
				       vm.dues = data;
				    },
				    error:function(){

				    }
				});

			},getRecordDetails: function(){

				var vm = this;
				$.ajax({
				    url:'../ajax/ajax_monthly_dues.php',
				    type:'POST',
					dataType:'json',
				    data: {
					    functionName:'getMonthlyDuesDetails',
					    member_id:vm.d_member_id,
					    date_from:vm.d_date_from,
					    payment_type:vm.d_payment_method,
					    date_to:vm.d_date_to,
					    profit_center:vm.d_profit_center,
				    },
				    success: function(data){
				       vm.due_details = data;
				    },
				    error:function(){

				    }
				});

			},
			showAddModal: function(){
				$('#myModal').modal('show');
			},
			showDetailsCon: function(){

				this.container = {list: false,details: true, new_request:false}

				var vm = this;
				vm.getRecordDetails();

			},
			dlMonthlyDues: function(){
				var self = this;
				window.open(
					'../ajax/ajax_monthly_dues.php?functionName=downMonthlyDues&member_id='+self.s_member_id+'&date_from='+self.s_date_from+'&date_to='+self.s_date_to+'&status=1'+'&profit_center='+self.s_profit_center,
					'_blank' //
				);
			},dlMonthlyDuesDetails: function(){
				var self = this;
				self.d_profit_center = self.d_profit_center?  self.d_profit_center :'';
				window.open(
					'../ajax/ajax_monthly_dues.php?functionName=downMonthlyDuesDetails&member_id='+self.d_member_id+'&date_from='+self.d_date_from+'&date_to='+self.d_date_to+'&profit_center='+self.d_profit_center+'&payment_type='+self.d_payment_method,
					'_blank' //
				);
			},

		}

	});

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>