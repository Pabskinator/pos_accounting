<?php
	// $user have all the properties and method of the current user
	require_once '../includes/admin/page_head2.php';
	if(false){
		Redirect::to(1);
	}

?>

	<style>
		.timeline {
			list-style: none;
			padding: 20px 0 20px;
			position: relative;
		}

		.timeline:before {
			top: 0;
			bottom: 0;
			position: absolute;
			content: " ";
			width: 3px;
			background-color: #eeeeee;
			left: 50%;
			margin-left: -1.5px;
		}

		.timeline > li {
			margin-bottom: 20px;
			position: relative;
		}

		.timeline > li:before,
		.timeline > li:after {
			content: " ";
			display: table;
		}

		.timeline > li:after {
			clear: both;
		}

		.timeline > li:before,
		.timeline > li:after {
			content: " ";
			display: table;
		}

		.timeline > li:after {
			clear: both;
		}

		.timeline > li > .timeline-panel {
			width: 46%;
			float: left;
			border: 1px solid #d4d4d4;
			border-radius: 2px;
			padding: 20px;
			position: relative;
			-webkit-box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175);
			box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175);
		}

		.timeline > li > .timeline-panel:before {
			position: absolute;
			top: 26px;
			right: -15px;
			display: inline-block;
			border-top: 15px solid transparent;
			border-left: 15px solid #ccc;
			border-right: 0 solid #ccc;
			border-bottom: 15px solid transparent;
			content: " ";
		}

		.timeline > li > .timeline-panel:after {
			position: absolute;
			top: 27px;
			right: -14px;
			display: inline-block;
			border-top: 14px solid transparent;
			border-left: 14px solid #fff;
			border-right: 0 solid #fff;
			border-bottom: 14px solid transparent;
			content: " ";
		}

		.timeline > li > .timeline-badge {
			color: #fff;
			width: 50px;
			height: 50px;
			line-height: 50px;
			font-size: 1.4em;
			text-align: center;
			position: absolute;
			top: 16px;
			left: 50%;
			margin-left: -25px;
			background-color: #999999;
			z-index: 100;
			border-top-right-radius: 50%;
			border-top-left-radius: 50%;
			border-bottom-right-radius: 50%;
			border-bottom-left-radius: 50%;
		}

		.timeline > li.timeline-inverted > .timeline-panel {
			float: right;
		}

		.timeline > li.timeline-inverted > .timeline-panel:before {
			border-left-width: 0;
			border-right-width: 15px;
			left: -15px;
			right: auto;
		}

		.timeline > li.timeline-inverted > .timeline-panel:after {
			border-left-width: 0;
			border-right-width: 14px;
			left: -14px;
			right: auto;
		}

		.timeline-badge.primary {
			background-color: #2e6da4 !important;
		}

		.timeline-badge.success {
			background-color: #3f903f !important;
		}

		.timeline-badge.warning {
			background-color: #f0ad4e !important;
		}

		.timeline-badge.danger {
			background-color: #d9534f !important;
		}
		.timeline-badge.info {
			background-color: #5bc0de !important;
		}
		.timeline-title {
			margin-top: 0;
			color: inherit;
		}
		.timeline-body > p,
		.timeline-body > ul {
			margin-bottom: 0;
		}

		.timeline-body > p + p {
			margin-top: 5px;
		}
	</style>

	<!-- Page content -->
	<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				 E-Wallet
			</h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')){
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('flash')."</div>";
			}
		?>
		<div id='vue_app'>
			<?php include 'includes/user_wallet_nav.php' ?>

			<div class="row">

				<div class="col-md-12">
					<div class="panel panel-primary">
						<!-- Default panel contents -->
						<div class="panel-heading">E-wallet</div>
						<div class="panel-body">
							<div class="text-center" v-show="ajax_loading">
								<p><i class='fa fa-circle-o-notch fa-spin fa-2x'></i></p>
							</div>
							<div v-else>
								<!-- COMPANY WALLET -->
								<div v-show="con.container1">
									<div id='container1'>
												<div class="row">
													<div class="col-md-3"></div>
													<div class="col-md-6">
													<div class="panel panel-default">
														<div class="panel-body">
																<h4>Request E-Wallet Load</h4>
																<strong class='text-success'>Current E-Wallet Load: {{ user.wallet }}</strong>
															<br>
															<div class="form-group">
																	<span class='text-danger'>Payment method:</span>
																	<select v-model='request.payment_method' class='form-control' name="select_payment_method" id="select_payment_method">
																		<option value="1">Cash</option>
																		<option value="2">Credit Card</option>
																		<option value="3">Cheque</option>
																		<option value="4">Bank Deposit</option>
																		<option value="5">Pay Pal</option>
																	</select>
																</div>
																<div class="form-group">
																	<span class='text-danger'>Amount:</span>
																	<input type="text" class='form-control' v-model="request.amount">
																</div>
																<div class="form-group">
																	<span class='text-danger'>Reference Number:</span>
																	<input type="text" class='form-control' v-model="request.ref_no">
																</div>
																<div class="form-group">
																	<span class='text-danger'>Remarks:</span>
																	<input type="text" class='form-control' v-model="request.remarks">
																</div>
																<div class="form-group">
																	<span class='text-danger'>Attachment:</span>
																	<input type="file" name='requestAttachment' class='form-control' v-model="request.attachment">
																</div>
																<div class="form-group">
																	<button id='btnSubmitRequest' class='btn btn-default' @click="submitRequest" >Submit</button>
																	<button class='btn btn-danger' @click="resetRequest" >Reset</button>
																</div>
															</div>
														</div>
													</div>
													<div class="col-md-3"></div>
											</div>
									</div>
								</div>
								<!-- END COMPANY WALLET -->
								<!-- PENDING REQUEST -->
								<div  v-show="con.container2">
									<div id='container2' v-show="item_request.length">
										<table class='table'>
											<thead>
												<tr>
													<th><i class='fa fa-lock'></i> ID</th>
													<th><i class='fa fa-user'></i> User</th>
													<th><i class='fa fa-money'></i> Requested Amount</th>
													<th><i class='fa fa-file'></i> Attachment</th>
													<th><i class='fa fa-pencil'></i> Details</th>
													<th><i class='fa fa-check-square'></i> Status</th>
													<th></th>
												</tr>
											</thead>
											<tbody>
												<tr v-for="item in item_request">
													<td style='border-top:1px solid #ccc;'>
														<strong class='text-danger'>
														{{item.id}}
														</strong>
													</td>
													<td style='border-top:1px solid #ccc;'>{{item.fullName}}</td>
													<td  style='border-top:1px solid #ccc;'>{{item.amount_formatted}}</td>
													<td  style='border-top:1px solid #ccc;'>

														<a v-show="item.file_name" target="_blank" href="../uploads/{{item.file_name}}"><i class='fa fa-file'></i></a>
														<span v-else><i class='fa fa-ban'></i></span>
													</td>
													<td style='border-top:1px solid #ccc;'>
														<p><span class='text-danger'>Ref No: </span>{{item.ref_no}}</p>
														<p><span class='text-danger'>Remarks: </span>{{item.remarks}}</p>
														<p><span class='text-danger'>Payment Method: </span>{{item.payment_method_label}}</p>
													</td>
													<td style='border-top:1px solid #ccc;'>
														<span class='text-danger'>
														{{item.current_status}}
														</span>
													</td>
													<td style='border-top:1px solid #ccc;'>
														<span v-show="item.action == 1">
															<button @click="processRequest(item)" class='btn btn-default btn-sm'><i class='fa fa-ok'></i> Process</button>
															<button @click="cancelRequest(item)" class='btn btn-danger btn-sm'><i class='fa fa-remove'></i> Cancel</button>

														</span>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
									<div v-else>
										<div class="alert alert-info">No record found.</div>
									</div>
								</div>
								<!-- END PENDING REQUEST -->
								<!-- HISTORY REQUEST -->
								<div v-show="con.container3">
									<div v-show="histories.length">
										<div>
											<div class="row">
												<div class="col-md-4"></div>
												<div class="col-md-4"></div>
												<div class="col-md-4">
													<div class="form-group">
													<input class='form-control' @keyup="getHistory | debounce 1000" placeholder='Search...' type="text" v-model='searchHistory'>
													</div>
												</div>
											</div>
										</div>
										<table class='table'>
											<thead>
											<tr>
												<th>User</th>
												<th>From</th>
												<th>To</th>
												<th>Date</th>
												<th>Remarks</th>
											</tr>
											</thead>
											<tbody>
											<tr v-for="his in histories">
												<td><strong>{{his.fullName}}</strong></td>
												<td>{{his.from_amount}}</td>
												<td>{{his.to_amount}}</td>
												<td>{{his.date_created}}</td>
												<td class='text-danger'>{{his.remarks}}</td>
											</tr>
											</tbody>
										</table>
									</div>
								</div>
								<!-- END HISTORY REQUEST -->
							</div>
						</div>
					</div>
				</div>
			</div>
		</div> <!-- end page content wrapper-->
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id='mtitle'></h4>
					</div>
					<div class="modal-body" id='mbody'>
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
	</div><!-- END Vue APP -->


	<script src='../js/vue.js'></script>
	<script>
		function validString(v){
			var regex = /^[a-zA-Z0-9_\-\s]*$/;
			return regex.test(v);
		}
		function validNumber(v){
			var regex = /(?:^\d{1,3}(?:\.?\d{3})*(?:,\d{2})?$)|(?:^\d{1,3}(?:,?\d{3})*(?:\.\d{2})?$)/;
			return regex.test(v);
		}
		function validateDate(testdate) {
			var date_regex = /^(0[1-9]|1[0-2])\/(0[1-9]|1\d|2\d|3[01])\/(19|20)\d{2}$/ ;
			return date_regex.test(testdate);
		}
		var vm = new Vue({
			el:'#vue_app',
			data:{
				ajax_loading:false,
				searchHistory:'',
				request: {remarks:'',amount:'',attachment:'',payment_method:'1',ref_no:''},
				item_request:[],
				histories:[],
				con: {container1:false,container2:false,container3:false},
				user: {wallet: 0}
			},
			ready:function(){
				this.showContainer(1);
			},
			methods:{
				getCurrentUserWallet: function(){
					var vm = this;
					$.ajax({
					    url:'../ajax/ajax_wallet.php',
					    type:'POST',
						dataType:'json',
					    data: {functionName:'getCurrentWalletUser'},
					    success: function(data){
							if(data.wallet && data.id){
								vm.user.wallet = data.wallet;
							}
					    },
					    error:function(){

					    }
					});
				},
				processRequest: function(item){
					var vm = this;
					if(vm.ajax_loading) return;
					vm.ajax_loading = true;
					alertify.confirm("Are you sure you want to process this action?",function(e){
						if(e){
							$.ajax({
							    url:'../ajax/ajax_wallet.php',
							    type:'POST',
							    data: {functionName:'processRequest',request_id: item.id},
							    success: function(data){
								    tempToast('info',data,'Info');
								    vm.ajax_loading = false;
								    vm.getRequest();
							    },
							    error:function(){
								    vm.ajax_loading = false;
							    }
							});

						} else {
							vm.ajax_loading = false;
						}
					});
				},
				cancelRequest: function(item){
					var vm = this;
					if(vm.ajax_loading) return;
					vm.ajax_loading = true;
					alertify.confirm("Are you sure you want to cancel this request?",function(e){
						if(e){
							$.ajax({
								url:'../ajax/ajax_wallet.php',
								type:'POST',
								data: {functionName:'cancelRequest',request_id: item.id},
								success: function(data){
									tempToast('info',data,'Info');
									vm.ajax_loading = false;
									vm.getRequest();
								},
								error:function(){
									vm.ajax_loading = false;
								}
							});

						} else {
							vm.ajax_loading = false;
						}
					});
				},
				getRequest:function(){
					var vm = this;
					vm.ajax_loading = true;
					$.ajax({
						url:'../ajax/ajax_wallet.php',
						type:'POST',
						dataType:'json',
						data: {functionName:'getRequest'},
						success: function(data){
							vm.item_request = data;
							vm.ajax_loading = false;
						},
						error:function(){
							vm.ajax_loading = false;
						}
					});
				},
				getHistory:function(){
					var vm = this;
					vm.ajax_loading = true;
					var search = vm.searchHistory;
					$.ajax({
						url:'../ajax/ajax_wallet.php',
						type:'POST',
						dataType:'json',
						data: {functionName:'getHistory',search:search},
						success: function(data){
							vm.histories = data;
							vm.ajax_loading = false;
						},
						error:function(){
							vm.ajax_loading = false;
						}
					});
				},
				submitRequest:function(){
					var vm = this;
					var request = vm.request;
					var fd = new FormData();
					var con = $('#btnSubmitRequest');
					button_action.start_loading(con);
					var file_data = $('input[name=requestAttachment]')[0].files[0];
					fd.append('file',file_data);
					fd.append('amount',request.amount);
					fd.append('remarks',request.remarks);
					fd.append('payment_method',request.payment_method);
					fd.append('ref_no',request.ref_no);

					if(validNumber(request.amount) && validString(request.remarks) ){
						alertify.confirm("Are you sure you want to continue this action?",function(e){
							if(e){
								$.ajax({
									url: '../ajax/ajax_wallet_attachment.php',
									data: fd,
									contentType: false,
									processData: false,
									type: 'POST',
									dataType: 'json',
									success: function(data){
										if(data.success){
											tempToast('info',data.message,'Info');
											vm.request = {remarks:'',amount:'',attachment: '',ref_no:'',payment_method:'1'};
											button_action.end_loading(con);
										} else {
											tempToast('error',data.message,'Error');
											button_action.end_loading(con);
										}
									},
									error:function(){
										button_action.end_loading(con);
									}
								});
							}
						});
					} else {
						tempToast('error','Invalid Data','Info');
						button_action.end_loading(con);
					}

				},
				resetRequest:function(){
					this.request = {remarks:'',amount:''};
				},
				showContainer: function(i){
					this.hideContainer();
					if(i == 1){
						this.con.container1 = true;
						this.getCurrentUserWallet();
					} else if(i == 2){
						this.con.container2 = true;
						this.getRequest();
					}else if(i == 3){
						this.con.container3 = true;
						this.getHistory();
					}
				},
				hideContainer: function(){
					this.con.container1 = false;
					this.con.container2 = false;
					this.con.container3 = false;
				}
			}
		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>