<?php
	// $user have all the properties and method of the current user
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('wallet_manage')){
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
			<?php include 'includes/company_walllet_nav.php' ?>

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
									<div class="col-md-12 text-right">
										<button class='btn btn-default' @click="addWallet">Add Wallet</button>
									</div>
									<div v-show="company_wallets.length">
										<h4>Current E-wallet</h4>
										<p class='text-muted'> * In order to view the current e-wallet, you may need to reload your page.</p>
										<div class="row">

										</div>
										<table class='table'>
											<thead><tr><th>Name</th><th>Amount</th><th>Add Affiliate</th><th>Add Pay bills</th><th>Deduct Load</th><th>Deduct Order</th><th></th></tr></thead>
											<tbody>
											<tr v-for="cw in company_wallets">
												<td>{{cw.label}}</td>
												<td>{{cw.amount}}</td>
												<td>{{ (cw.add_affiliate == 1) ? 'Yes' : 'No' }}</td>
												<td>{{ (cw.add_paybills == 1) ? 'Yes' : 'No' }}</td>
												<td>{{ (cw.deduct_load == 1) ? 'Yes' : 'No' }}</td>
												<td>{{ (cw.deduct_orders == 1) ? 'Yes' : 'No' }}</td>
												<td>
													<button @click="updateWallet(cw)" class='btn btn-default btn-sm'><i class='fa fa-pencil'></i> Update</button>
													<button @click="deleteWallet(cw)" class='btn btn-danger btn-sm'><i class='fa fa-trash'></i> Delete</button>

												</td>
											</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<!-- END COMPANY WALLET -->
							<!-- USER WALLET -->
							<div  v-show="con.container2">
								<div id='container2'>
									<div class="col-md-12 text-right">
										<button class='btn btn-default' @click="addUserWallet">Add Wallet</button>
									</div>
									<div v-show="user_wallets.length">
										<h4>Current E-wallet</h4>
										<p class='text-muted'> * In order to view the current e-wallet, you may need to reload your page.</p>

										<div class="row">

										</div>
										<table class='table'>
											<thead><tr><th>Name</th><th>E-wallet</th><th>USD PV</th><th>Binary PV</th><th>Uni Level PV</th><th></th></tr></thead>
											<tbody>
											<tr v-for="user in user_wallets">
												<td> {{user.fullName}} </td>
												<td> {{user.amount}} </td>
												<td> {{user.usd_pv}} </td>
												<td> {{user.binary_pv}} </td>
												<td> {{user.uni_level_pv}} </td>
												<td>
													<button @click="updateUserWallet(user)" class='btn btn-default btn-sm'><i class='fa fa-pencil'></i> Update</button>
													<button @click="deleteUserWallet(user)" class='btn btn-danger btn-sm'><i class='fa fa-trash'></i> Delete</button>
												</td>
											</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<!-- END USER WALLET -->
							<!-- WALLET CONFIGURATION-->
								<div  v-show="con.container3">
									<div id='container3'>
										<div v-show="configs.length">
											<h4>Configurations</h4>


											<table class='table'>
												<thead><tr><th>Key</th><th>Value</th><th></th></tr></thead>
												<tbody>
												<tr v-for="config in configs">
													<td>{{config.label}}</td>
													<td>
														<input v-show='config.is_edit == 1' type="text" v-model="config.value" value="{{config.value}}">
														<span v-else>{{config.value}}</span>

													</td>
													<td>
														<button v-show='config.is_edit == 0' @click="updateConfig(config)" class='btn btn-default btn-sm'><i class='fa fa-pencil'></i> Update</button>
														<button v-show='config.is_edit == 1' @click="saveConfig(config)" class='btn btn-default btn-sm'><i class='fa fa-save'></i> Save</button>
														<button v-show='config.is_edit == 1' @click="cancelConfig(config)" class='btn btn-default btn-sm'><i class='fa fa-remove'></i> Cancel</button>
													</td>
												</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							<!-- END USER WALLET -->
							<!-- ADD  COMPANY WALLET -->
							<div v-show="form_wallet_container">
								<div class="col-md-3"></div>
								<div class="col-md-6">

									<h4>E-Wallet</h4>
									<input type="hidden" v-model="form_wallet.id">
									<div class="form-group">
										E-Wallet Name:
										<input type="text" class='form-control' v-model="form_wallet.label" >
									</div>
									<div class="form-group">
										Amount
										<input type="text" class='form-control' v-model="form_wallet.amount" >
									</div>
									<div class="form-group">
										<input type="checkbox" id='checkbox_affiliate'  v-model="form_wallet.add_affiliate" >
										<label for="checkbox_affiliate">Add Affiliate</label>
									</div>
									<div class="form-group">
										<input type="checkbox" id='checkbox_paybills'  v-model="form_wallet.add_paybills" >
										<label for="checkbox_paybills">Add Pay bills</label>
									</div>
									<div class="form-group">
										<input type="checkbox" id='checkbox_load' v-model="form_wallet.deduct_load" >
										<label for="checkbox_load">Deduct Load</label>
									</div>
									<div class="form-group">
										<input type="checkbox"  id='checkbox_order' v-model="form_wallet.deduct_orders" >
										<label for="checkbox_order">Deduct Order</label>
									</div>
									<div class="form-group">
										<button id='btnSaveWallet' class='btn btn-default' @click="saveWallet">SAVE</button>
										<button class='btn btn-danger' @click="cancelWallet">CANCEL</button>
									</div>
								</div>
								<div class="col-md-3"></div>
							</div>
							<!-- END COMPANY WALLET -->
							<!-- ADD  COMPANY WALLET -->
								<div v-show="form_user_wallet_container">
									<div class="col-md-3"></div>
									<div class="col-md-6">

										<h4>E-Wallet</h4>
										<input type="hidden" v-model="form_user_wallet.id">
										<div class="form-group" v-show="!form_user_wallet.id">
											Name:
											<input id='user_id' type="text" class='form-control' v-model="form_user_wallet.user_id" >
										</div>
										<div v-show="form_user_wallet.id">
											<p>Name: <input disabled type="text" class='form-control' value="{{ form_user_wallet.fullName }}">

										</div>
										<div class="form-group">
											Current E-wallet
											<input type="text" class='form-control'  value='{{form_user_wallet.cur_amount}}' disabled>
										</div>
										<div class="form-group">
											<i class='fa fa-plus'></i> E-wallet
											<input type="text" class='form-control' v-model="form_user_wallet.amount" >
										</div>
										<div class="form-group">
											Current USD PV
											<input type="text" class='form-control'  value='{{form_user_wallet.cur_usd_pv}}' disabled>
										</div>
										<div class="form-group">
											<i class='fa fa-plus'></i> USD PV
											<input type="text" class='form-control' v-model="form_user_wallet.usd_pv" >
										</div>
										<div class="form-group">
											Current Binary PV
											<input type="text" class='form-control'  value='{{form_user_wallet.cur_binary_pv}}' disabled>
										</div>
										<div class="form-group">
											<i class='fa fa-plus'></i> Binary PV
											<input type="text" class='form-control' v-model="form_user_wallet.binary_pv" >
										</div>
										<div class="form-group">
											Current Uni Level PV
											<input type="text" class='form-control'  value='{{form_user_wallet.cur_uni_level_pv}}' disabled>
										</div>
										<div class="form-group">
											<i class='fa fa-plus'></i> Uni Level PV
											<input type="text" class='form-control' v-model="form_user_wallet.uni_level_pv" >
										</div>
										<div class="form-group">
											<button id='btnSaveWalletUser' class='btn btn-default' @click="saveUserWallet">SAVE</button>
											<button class='btn btn-danger' @click="cancelUserWallet">CANCEL</button>
										</div>
									</div>
									<div class="col-md-3"></div>
								</div>
							<!-- END COMPANY WALLET -->
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

		var vm = new Vue({
			el:'#vue_app',
			data:{
				ajax_loading: false,
				company_wallets:[],
				is_update_config: false,
				configs:[],
				user_wallets:[],
				form_wallet_container: false,
				form_user_wallet_container: false,
				form_wallet:{ id:0, label:'', amount:'',add_affiliate:0,deduct_orders:0,deduct_load:0,add_paybills:0},
				form_user_wallet:{ id:0, user_id:'', amount:'',usd_pv:'',binary_pv:'',uni_level_pv:'',fullName:'',cur_amount:0,cur_usd_pv:0,cur_binary_pv:0,cur_uni_level_pv:0},
				all_request:[],
				con: {container1:false,container2:false,container3:false}
			},
			ready:function(){
				this.showContainer(1);
				this.getCompanyWallet();
				var user_select2 = $('#user_id');
				user_select2.select2({
					placeholder: 'Search ',allowClear: true, minimumInputLength: 2,

					ajax: {
						url: '../ajax/ajax_json.php', dataType: 'json', type: "POST", quietMillis: 50, data: function(term) {
							return {
								q: term, functionName: 'users'
							};
						}, results: function(data) {
							return {
								results: $.map(data, function(item) {

									return {
										text: item.lastname + ", " + item.firstname,
										slug: item.lastname + ", " + item.firstname + " " + item.middlename,
										id: item.id
									}
								})
							};
						}
					}
				});
			},
			methods:{
				updateConfig: function (c){
					c.is_edit = 1;
				},
				cancelConfig: function (c){
					c.is_edit = 0;
					c.value = c.value_old;
				},
				saveConfig: function (c){
					$.ajax({
					    url:'../ajax/ajax_wallet.php',
					    type:'POST',
						dataType:'json',
					    data: {functionName:'saveConfig',request:JSON.stringify(c)},
					    success: function(data){
						    if(data.success){
							    tempToast('info',data.message,"Info");
							    vm.showContainer(3);
							    vm.ajax_loading = false;
						    } else {
							    tempToast('error',data.message,"Error");
							    vm.ajax_loading = false;
						    }
					    },
					    error:function(){

					    }
					})
				},
				deleteWallet: function(cw){
					var vm = this;
					alertify.confirm("Are you sure you want to delete this record",function(e){
						if(e){
							vm.ajax_loading = true;
							$.ajax({
							    url:'../ajax/ajax_wallet.php',
							    type:'POST',
								dataType:'json',
							    data: {functionName:'deleteWallet',id:cw.id},
							    success: function(data){
								    if(data.success){
									    tempToast('info',data.message,"Info");
									    vm.getCompanyWallet();
									    vm.showContainer(1);
									    vm.ajax_loading = false;
								    } else {
									    tempToast('error',data.message,"Error");
									    vm.ajax_loading = false;
								    }
							    },
							    error:function(){

							    }
							});
						}
					});
				},
				updateUserWallet: function(cw){
					this.hideContainer();
					this.form_user_wallet.id = cw.id;
					this.form_user_wallet.user_id = cw.user_id;
					this.form_user_wallet.label = cw.label;
					this.form_user_wallet.cur_amount = cw.amount;
					this.form_user_wallet.amount = 0;
					this.form_user_wallet.cur_usd_pv = cw.usd_pv;
					this.form_user_wallet.usd_pv = 0;
					this.form_user_wallet.cur_binary_pv = cw.binary_pv;
					this.form_user_wallet.binary_pv = 0;
					this.form_user_wallet.cur_uni_level_pv = cw.uni_level_pv;
					this.form_user_wallet.uni_level_pv = 0;
					this.form_user_wallet.fullName = cw.fullName;
					this.form_user_wallet_container = true;
				},
				cancelUserWallet:function(){
					this.form_user_wallet_container = false;
					this.showContainer(2);
				},
				deleteUserWallet: function(cw){
					var vm = this;
					alertify.confirm("Are you sure you want to delete this record",function(e){
						if(e){
							vm.ajax_loading = true;
							$.ajax({
								url:'../ajax/ajax_wallet.php',
								type:'POST',
								dataType:'json',
								data: {functionName:'deleteUserWallet',id:cw.id},
								success: function(data){
									if(data.success){
										tempToast('info',data.message,"Info");
										vm.getUserWallet();
										vm.showContainer(2);
										vm.ajax_loading = false;
									} else {
										tempToast('error',data.message,"Error");
										vm.ajax_loading = false;
									}
								},
								error:function(){

								}
							});
						}
					});
				},
				addUserWallet:function(){
					this.hideContainer();
					this.form_user_wallet.id=0;
					this.form_user_wallet.label='';
					$('#user_id').select2('val',null);
					this.form_user_wallet.user_id='';
					this.form_user_wallet.amount='';
					this.form_user_wallet.cur_amount='0';
					this.form_user_wallet.fullName='';
					this.form_user_wallet_container = true;
				},
				updateWallet: function(cw){
					this.hideContainer();
					this.form_wallet.id = cw.id;
					this.form_wallet.label = cw.label;
					this.form_wallet.amount = cw.amount;
					this.form_wallet.add_affiliate = (cw.add_affiliate == 1) ? true: false;
					this.form_wallet.deduct_orders = (cw.deduct_orders == 1) ? true: false;
					this.form_wallet.deduct_load = (cw.deduct_load == 1) ? true: false;
					this.form_wallet.add_paybills = (cw.add_paybills == 1) ? true: false;
					this.form_wallet_container = true;
				},
				cancelWallet:function(){
					this.form_wallet_container = false;
					this.showContainer(1);
				},
				addWallet:function(){
					this.hideContainer();
					this.form_wallet.id=0;
					this.form_wallet.label='';
					this.form_wallet.amount='';
					this.form_wallet_container = true;
				},
				saveUserWallet:function(){
					var vm = this;
					var con = $('#btnSaveWalletUser');
					button_action.start_loading(con);
					vm.ajax_loading = true;
					alertify.confirm("Are you sure you want to continue this action?",function(e){
						if(e){
							$.ajax({
								url:'../ajax/ajax_wallet.php',
								type:'POST',
								dataType:'json',
								data: {functionName:'saveWalletUser', data:JSON.stringify(vm.form_user_wallet)},
								success: function(data){

									if(data.success){
										tempToast('info',data.message,"Info");
										vm.ajax_loading = false;
										button_action.end_loading(con);
										vm.getUserWallet();
										vm.showContainer(2);
									} else {
										tempToast('error',data.message,"Error");
										button_action.end_loading(con);
										vm.ajax_loading = false;
									}
								},
								error:function(){
									vm.ajax_loading = false;
								}
							});
						} else {
							button_action.end_loading(con);
							vm.ajax_loading = false;
						}
					});

				},
				saveWallet:function(){
					var vm = this;
					var con = $('#btnSaveWallet');
					button_action.start_loading(con);
					vm.ajax_loading = true;
					alertify.confirm("Are you sure you want to continue this action?",function(e){
						if(e){
							$.ajax({
								url:'../ajax/ajax_wallet.php',
								type:'POST',
								dataType:'json',
								data: {functionName:'saveWallet', data:JSON.stringify(vm.form_wallet)},
								success: function(data){
									if(data.success){
										tempToast('info',data.message,"Info");
										vm.ajax_loading = false;
										button_action.end_loading(con);
										vm.getCompanyWallet();
										vm.showContainer(1);
									} else {
										tempToast('error',data.message,"Error");
										button_action.end_loading(con);
										vm.ajax_loading = false;
									}
								},
								error:function(){
									vm.ajax_loading = false;
								}
							});
						} else {
							button_action.end_loading(con);
							vm.ajax_loading = false;
						}
					});

				},
				getCompanyWallet: function(){
					var vm = this;
					vm.ajax_loading = true;
					$.ajax({
					    url:'../ajax/ajax_wallet.php',
					    type:'POST',
						dataType:'json',
					    data: {functionName:'getCompanyWallet'},
					    success: function(data){
					        vm.company_wallets = data;
						    vm.ajax_loading = false;
					    },
					    error:function(){

					    }
					});
				},
				getUserWallet: function(){
					var vm = this;
					vm.ajax_loading = true;
					$.ajax({
					    url:'../ajax/ajax_wallet.php',
					    type:'POST',
						dataType:'json',
					    data: {functionName:'getUserWallet'},
					    success: function(data){
					        vm.user_wallets = data;
						    vm.ajax_loading = false;
					    },
					    error:function(){

					    }
					});
				},
				getConfigurations: function(){
					var vm = this;
					vm.ajax_loading = true;
					$.ajax({
						url:'../ajax/ajax_wallet.php',
						type:'POST',
						dataType:'json',
						data: {functionName:'getWalletConfigurations'},
						success: function(data){
							vm.configs = data;
							vm.ajax_loading = false;
						},
						error:function(){

						}
					});
				},
				showContainer: function(i){
					this.hideContainer();
					this.hideForms();
					if(i == 1){
						this.con.container1 = true;
						this.getCompanyWallet();
					} else if(i == 2){
						this.con.container2 = true;
						this.getUserWallet();
					}else if(i == 3){
						this.con.container3 = true;
						this.getConfigurations();
					}
				},
				hideContainer: function(){
					this.con.container1 = false;
					this.con.container2 = false;
					this.con.container3 = false;
				},
				hideForms: function(){
					this.form_wallet_container= false;
					this.form_user_wallet_container= false;
				}
			}
		});






	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>