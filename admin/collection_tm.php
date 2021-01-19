<?php

	// fix and check
	require_once '../includes/admin/page_head2.php';

	if(!$user->hasPermission('dashboard')){
		// redirect to denied page
		Redirect::to(1);
	}

?>

	<br>
	<div class="container-fluid" id='top_management'>
		<link rel="stylesheet" href="../css/custom.css">
		<div class="row">
			<div class="col-md-12">
				<div class="x_panel tile" v-show="!details.list.length">
					<div class="x_title">
						<div class="row">
							<div class="col-md-3">
								<h3>Collection Report</h3>
							</div>
							<div class="col-md-3">

							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' autocomplete="off" id='dt_from' placeholder='Date From'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' autocomplete="off" id='dt_to' placeholder='Date To'>
								</div>
							</div>

						</div>

					</div>
					<div class="x_content">
						<div class="row">
							<div class="animated flipInY col-md-4 col-sm-12">
								<div class="tile-stats">
									<div class="count">{{collections.total_receipt_amount}}</div>
									<h3>Receipt Amount Total</h3>
									<p></p>
								</div>
							</div>
							<div class="animated flipInY col-md-4 col-sm-12">
								<div class="tile-stats">
									<div class="count">{{collections.total_deduction}}</div>
									<h3>Deduction Total</h3>
									<p></p>
								</div>
							</div>
							<div class="animated flipInY col-md-4 col-sm-12">
								<div class="tile-stats">
									<div class="count">{{collections.total_paid_amount}}</div>
									<h3>Paid Total</h3>
									<p></p>
								</div>
							</div>
						</div>
						<h5 v-show="collections.date_from && collections.date_to" >From: <strong class='text-danger'>{{collections.date_from }}</strong> To: <strong class='text-danger'>{{collections.date_to }}</strong></h5>
						<table class='table table-bordered' id='tblForApproval'>

							<thead>
							<tr>
								<th>CR Number</th>
								<th>Created</th>
								<th class='text-right'>Receipt Amount</th>
								<th class='text-right'>Deduction </th>
								<th class='text-right'>Total </th>
								<th class='text-center'>Details </th>
							</tr>
							</thead>

							<tbody>

							<tr v-for="col in collections.list">

								<td>{{col.cr_number}}</td>
								<td>{{col.date}}</td>
								<td class='text-right'>{{col.receipt_amount}}</td>
								<td class='text-right'>{{col.deduction}}</td>
								<td class='text-right'>{{col.paid_amount}}</td>
								<td class='text-center'><button class='btn btn-default' @click="getDetails(col)"><i class='fa fa-list'></i></button></td>

							</tr>


							</tbody>

							<tfoot>
							<tr>
								<th></th>
								<th></th>
								<th class='text-right'>{{collections.total_receipt_amount}}</th>
								<th class='text-right'>{{collections.total_deduction}}</th>
								<th class='text-right'>{{collections.total_paid_amount}}</th>
								<th></th>
							</tr>
							</tfoot>

						</table>

					</div>
					<!-- Aging View -->

						<div class="x_content">
							<div class="row">
								<div class="col-md-3">
									<div class="tile-stats">
										<div class="count">{{aging.total}}</div>
										<h3>Total Receivables</h3>
										<p></p>
									</div>
								</div>
								<div class="col-md-9">
									<table class='table table-bordered' id='tblWithBorder'>
										<tr>
											<th><strong class='text-danger'>Aging</strong></th>
											<th>0-30</th>
											<th>31-60</th>
											<th>61-90</th>
											<th>91-120</th>
											<th>121-Above</th>
										</tr>
										<tr>
											<td></td>
											<td>{{aging.below_30}}</td>
											<td>{{aging.from_31_60}}</td>
											<td>{{aging.from_61_90}}</td>
											<td>{{aging.from_91_120}}</td>
											<td>{{aging.above_121}}</td>
										</tr>

									</table>
								</div>
							</div>


						</div>

					<!-- end aging -->
				</div>
				<!-- Details View -->
				<div class="x_panel tile" v-show="details.list.length">
					<div class="x_title">
						<button class='btn btn-default' @click="back()"> <i class='fa fa-arrow-left'></i> Back </button>
					</div>
					<div class="x_content">
						<div class="row">
							<div class="animated flipInY col-md-4 col-sm-12">
								<div class="tile-stats">
									<div class="count">{{details.total_receipt}}</div>
									<h3>Receipt Amount Total</h3>
									<p></p>
								</div>
							</div>
							<div class="animated flipInY col-md-4 col-sm-12">
								<div class="tile-stats">
									<div class="count">{{details.total_deduction}}</div>
									<h3>Deduction Total</h3>
									<p></p>
								</div>
							</div>
							<div class="animated flipInY col-md-4 col-sm-12">
								<div class="tile-stats">
									<div class="count">{{details.total_paidamount}}</div>
									<h3>Paid Total</h3>
									<p></p>
								</div>
							</div>
						</div>
						<table class='table table-bordered' id='tblWithBorder'>
							<thead>
								<tr>
									<th>Ctrl #</th>
									<th>Client</th>
									<th>Invoice</th>
									<th>DR</th>
									<th class='text-right'>Receipt Amount</th>
									<th class='text-right'>Deduction</th>
									<th class='text-right'>Paid</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="det in details.list">
									<td>{{det.cr_number}}</td>
									<td>{{det.client_name}}</td>
									<td>{{det.sales_invoice}}</td>
									<td>{{det.delivery_receipt}}</td>
									<td class='text-right'>{{formatAmount(det.receipt_amount)}}</td>
									<td class='text-right'>{{formatAmount(det.deduction)}}</td>
									<td class='text-right'>{{formatAmount(det.paid_amount)}}</td>
								</tr>
							</tbody>

						</table>
					</div>
				</div>
				<!-- end details -->

			</div>
		</div>



	</div>
	<script src='../js/vue3.js'></script>
	<script>

		var vm = new Vue({
			el: "#top_management",
			data:{
				aging: {
					below_30 : '',
					from_31_60 : '',
					from_61_90 : '',
					from_91_120 : '',
					above_121 : '',
				},
				collections: {
					total_receipt_amount: 0,
					total_deduction: 0,
					total_paid_amount: 0,
					date_from: '',
					date_to: '',
					list: []
				},
				fltr: {
					date_from : '',
					date_to : ''
				},
				details:{
					list: [],
					total_receipt: 0,
					total_deduction: 0,
					total_paidamount: 0,
				}

			},
			mounted: function(){

				var self = this;
				self.getCollections();

				$('#dt_from').datepicker({
					autoclose:true
				}).on('changeDate', function(ev){
					$('#dt_from').datepicker('hide');
					self.fltr.date_from = $('#dt_from').val();
					self.checkDate();
				});

				$('#dt_to').datepicker({
					autoclose:true
				}).on('changeDate', function(ev){
					$('#dt_to').datepicker('hide');
					self.fltr.date_to = $('#dt_to').val();
					self.checkDate();
				});
				self.getAging();

			},
			methods: {
				formatAmount: function(a){
					return number_format(a,2);
				},
				checkDate: function(){
					var self = this;
					if(self.fltr.date_from && self.fltr.date_to){
						self.getCollections();
					}
				},
				getCollections: function(){

					var self = this;
					$.ajax({
						url:'../ajax/ajax_tm.php',
						type:'POST',
						dataType:'json',
						data: { functionName:'collections',dt_from: self.fltr.date_from ,dt_to: self.fltr.date_to },
						success: function(data){

							self.collections.date_from = data.date_from;
							self.collections.date_to = data.date_to;
							self.collections.list = data.list;
							self.collections.total_deduction = data.total_deduction;
							self.collections.total_receipt_amount = data.total_receipt_amount;
							self.collections.total_paid_amount = data.total_paid_amount;

						},
						error:function(){

						}
					});

				},

				getDetails: function(col){
					var self = this;

					$.ajax({
					    url:'../ajax/ajax_tm.php',
					    type:'POST',
					    dataType:'json',
					    data: {functionName:'getDetails',cr_number:col.cr_number },
					    success: function(data){

							self.details.list = data.list;
							self.details.total_receipt = data.total_receipt;
							self.details.total_deduction = data.total_deduction;
							self.details.total_paidamount = data.total_paidamount;
					    },
					    error:function(){

					    }
					})
				},
				getAging: function(col){
					var self = this;

					$.ajax({
					    url:'../ajax/ajax_tm.php',
					    type:'POST',
					    dataType:'json',
					    data: {functionName:'agingSummary'},
					    success: function(data){

							self.aging = data;

					    },
					    error:function(){

					    }
					})
				},
				back: function(){
					this.details.list = [];
				}
			}
		});



	</script>

<?php require_once '../includes/admin/page_tail2.php'; ?>