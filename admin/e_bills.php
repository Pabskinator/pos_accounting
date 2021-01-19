<?php
	// $user have all the properties and method of the current user
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('ez_bills')) {
		// redirect to denied page
		Redirect::to(1);
	}
	$category_biller = new Biller_category();
	$billers = $category_biller->get_active('biller_categories', [1, '=', 1]);

	$page_biller_name = new Biller_name();
	$page_biller_names = $page_biller_name->get_active('biller_names', [1, '=', 1]);

	$wallet = new Wallet();
	$my_wallet = $wallet->myWallet(2,$user->data()->id);
	$wallet_amount = 0;
	$usd_pv= 0;
	$binary_pv= 0;
	$uni_level_pv= 0;
	if($my_wallet){
		$wallet_amount = $my_wallet->amount;
		$usd_pv= $my_wallet->usd_pv;
		$binary_pv= $my_wallet->binary_pv;
		$uni_level_pv= $my_wallet->uni_level_pv;
	}

	$wallet_config = new Wallet_config();
	$wallet_configs = $wallet_config->get_active('wallet_configuration',['1','=','1']);
	$arr_config = [];
	if($wallet_configs){
		foreach($wallet_configs as $wc){
			$arr_config[$wc->key] = $wc->value;
		}
	}
	$user = new User();
	$member_k_type = 0;
	if($user->data()->member_id){
		$member = new Member($user->data()->member_id);
		$member_k_type = $member->data()->k_type;
	}
	$_SESSION['wallet_config'] = $arr_config;
	$_SESSION['k_type'] = $member_k_type;
?>

	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<?php include 'includes/e_bills_nav.php' ?>

			<!-----------------    REQUEST --------------------->
			<div id="con_req" style='display:none;'>
				<h3>Request</h3>

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Request bills payment</div>
					<div class="panel-body" id='container1'>
						<?php
							if($billers) {
								?>

										<div class="row">
											<div class="col-md-3"></div>
											<div class="col-md-6">
											<div class="panel panel-default">
												<div class="panel-body">
													<p class='hidden-xs'>E-Wallet: <span class='h3' style='color:#a94442;'>PHP <?php echo number_format($wallet_amount,2); ?></span></p>
													<p class='visible-xs'>E-Wallet: <span style='color:#a94442;'>PHP <?php echo number_format($wallet_amount,2); ?></span></p>
													<p>USD PV: <span style='color:#a94442;'><?php echo number_format($usd_pv,2); ?></p></span>
													<p>Binary PV: <span style='color:#a94442;'><?php echo number_format($binary_pv,2); ?></p></span>
													<p>Uni Level PV: <span style='color:#a94442;'><?php echo number_format($uni_level_pv,2); ?></p></span>
												</div>
											</div>
											<div class="panel panel-default">
												<div class="panel-body">
															<div class="form-group">
																<label for="req_category_id">Bill Type</label>
																<select name="req_category_id" id="req_category_id" class='form-control'>
																	<option value="">Select category</option> <?php foreach($billers as $bill) {
																		?>
																		<option value="<?php echo $bill->id; ?>"><?php echo $bill->name; ?></option>                                                <?php
																	} ?>
																</select>
															</div>
															<div class="form-group">
																<label for="req_bill_id">Company Name</label>
																<select name="req_bill_id" id="req_bill_id" class='form-control'>
																	<option value="">Choose Category First</option>
																</select>
															</div>
													<hr>
													<div id='form_container'>
														<div class="alert alert-warning">Select bills to pay.</div>
													</div>

													</div>
												</div>
											</div>
											<div class="col-md-3"></div>
								</div>								<?php
							} else {
								?>
								<div class="alert alert-info">Add biller first.</div>                                <?php
							}
						?>

						<div id='form_container'></div>
						<div id="test_con"></div>
					</div>
				</div>

			</div>
			<!----------------- END REQUEST --------------------->

			<!-----------------    MY REQUEST --------------------->
			<div id="con_pending" style='display:none;'>
				<h3>Process Request</h3>

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Pending Request</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-3"></div>
							<div class="col-md-3"></div>
							<div class="col-md-3"></div>
							<div class="col-md-3">
								<div class="form-group">
									<select name="request_status" id="request_status" class='form-control'>
										<option value="1">Pending</option>
										<option value="2">Processed</option>
										<option value="3">Declined</option>
									</select>
								</div>
							</div>
						</div>
						<div id='container2'></div>
					</div>
				</div>
			</div>
			<!----------------- END MY REQUEST --------------------->            <!-----------------   CATEGORY --------------------->
			<div id="con_category" style='display:none;'>
				<h3>Biller Category</h3>

				<div class="text-right">
					<button class='btn btn-default btn-sm' id='btnAddCategory'><i class='fa fa-plus'></i> Add Category
					</button>
				</div>
				<br>

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Category</div>
					<div class="panel-body">
						<div id='container3'></div>

					</div>
				</div>
			</div>
			<!----------------- END CATEGORY --------------------->
			<!-----------------   Name --------------------->
			<div id="con_name" style='display:none;'>
				<h3>Biller Name</h3>

				<div class="text-right">
					<button class='btn btn-default btn-sm' id='btnAddBillerName'>
						<i class='fa fa-plus'></i> Add Biller Name
					</button>
				</div>
				<br>

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Name</div>
					<div class="panel-body">
						<div id='container4'></div>
					</div>
				</div>
			</div>
			<!----------------- END name --------------------->
			<!-----------------   Name --------------------->
			<div id="con_my_request" style='display:none;'>
				<h3>Requested Bills</h3>

				<br>

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading"></div>
					<div class="panel-body">
						<div id='container5'></div>
					</div>
				</div>
			</div>
			<!----------------- END name --------------------->

		</div>
	</div> <!-- end page content wrapper-->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id='mtitle'></h4>
				</div>
				<div class="modal-body" id='mbody'></div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<div id="con_modal_category" style='display:none;'>
		<div class="form-group">
			<strong>Name:</strong> <input type="text" class='form-control' id='category_name'>
			<input type="hidden" class='form-control' id='category_id'>
		</div>
		<div class="form-group">
			<button class='btn btn-default' id='btnCategorySave'>SAVE</button>
		</div>
	</div>
	<div id="con_modal_name" style='display:none;'>
		<div class="form-group">
			<strong>Name:</strong> <input type="text" class='form-control' id='biller_name'>
			<input type="hidden" class='form-control' id='biller_id'>
		</div>
		<div class="form-group">
			<strong>Category:</strong> <select class='form-control' id='biller_category_id'> </select>

		</div>
		<div class="form-group">
			<button class='btn btn-default' id='btnBillerNameSave'>SAVE</button>
		</div>
	</div>
	<div id="con_modal_custom_fields" style='display:none;'>
		<?php include_once('includes/biller_name_custom_fields.php'); ?>
	</div>
	<script>
		$(function() {
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
			showContainer(true, false, false, false);
			function getPending() {
				var request_status = $('#request_status').val();
				$.ajax({
				    url:'../ajax/ajax_e_bills.php',
				    type:'POST',
				    data: {functionName:'pendingRequest',status:request_status},
				    success: function(data){
				        $('#container2').html(data)
				    },
				    error:function(){
				        
				    }
				})
			}
			function getMyPending() {
				var request_status = $('#request_status').val();
				$.ajax({
					url:'../ajax/ajax_e_bills.php',
					type:'POST',
					data: {functionName:'pendingRequest',status:request_status,user_type:1},
					success: function(data){
						$('#container5').html(data)
					},
					error:function(){

					}
				})
			}

			function getName() {
				$('#container4').html('Fetching records...');
				$.ajax({
					url: '../ajax/ajax_e_bills.php',
					type: 'POST',
					data: {functionName: 'getBillerName'},
					success: function(data) {
						$('#container4').html(data);
						var categ_list = $('#biller_categ_json').val();
						try {
							categ_list = JSON.parse(categ_list);
							if(categ_list) {
								var ret = "<option value=''>Select Category</option>";
								for(var i in categ_list) {
									ret += "<option value='" + categ_list[i].id + "'>" + categ_list[i].name + "</option>"
								}

								$('#biller_category_id').html(ret);
							}
						} catch(e) {

						}
					},
					error: function() {

					}
				});
			}

			function getCategory() {
				$('#container3').html('Fetching records...');
				$.ajax({
					url: '../ajax/ajax_e_bills.php',
					type: 'POST',
					data: {functionName: 'getCategory'},
					success: function(data) {
						$('#container3').html(data);
					},
					error: function() {

					}
				});
			}
			/*************** pending *******************/
				$('body').on('click','#request_status',function(){
					getPending();
				});
				$('body').on('click','.btnProcessRequest',function(){
					var con = $(this);
					var id = con.attr('data-id');
					alertify.confirm("Are you sure you want to process this request",function(e){
						if(e){
							$.ajax({
								url:'../ajax/ajax_e_bills.php',
								type:'POST',
								data: {functionName:'processRequest',id:id},
								success: function(data){
									tempToast('info',data,'Info!');
									getPending();
								},
								error:function(){

								}
							});
						}
					});
				});
				$('body').on('click','.btnRemoveRequest',function(){
					var con = $(this);
					var id = con.attr('data-id');
					alertify.confirm("Are you sure you want to process this request",function(e){
						if(e){
							$.ajax({
								url:'../ajax/ajax_e_bills.php',
								type:'POST',
								data: {functionName:'removeRequest',id:id},
								success: function(data){
									tempToast('info',data,'Info!');
									getPending();
								},
								error:function(){

								}
							});
						}
					});

				});
			/*************** end pending *******************/
			/*************Bill Request ****************/
			$('body').on('click','#btnSubmitRequest',function(e){

				e.preventDefault();
				//main_form_bills
				var arr = [];
				var biller_id = $('#req_bill_id').val();
				var has_error = [];
				$('.form_bill_inputs').each(function(i){
					var input = $(this);
					var input_type = input.attr('type');
					var form_id = input.attr('id');
					var form_value = input.val();
					var form_label = input.attr('data-label');
					var rule_id = "rule_"+form_id;
					var rules  = $('#'+rule_id).val();

					try {
						rules = JSON.parse(rules);
					} catch(e){
						rules = [];
					}
					console.log(rules.data_type);
					if(rules.is_required == '1' && !form_value){
							has_error.push({id:form_id,msg:"This field is required"});
					}
					arr.push({
						id:form_id,
						value:form_value,
						label:form_label
					});
				});
				if(has_error.length){
					for(var i in has_error){
						$('#group_'+has_error[i].id).addClass('has-error');
						$('#msg_error_'+has_error[i].id).html(has_error[i].msg);
					}
					return;
				}

				var con =$(this);
				button_action.start_loading(con);
				if(arr.length){
					$.ajax({
					    url:'../ajax/ajax_e_bills.php',
					    type:'POST',
					    data: {functionName:'saveRequestBills',data:JSON.stringify(arr),biller_id:biller_id},
					    success: function(data){
						   alertify.alert(data,function(){
							  location.href = 'e_bills.php';
						   });
					    },
					    error:function(){
						    alertify.alert("Request Failed. Please try again.");
						    button_action.end_loading(con);
					    }
					})
				}
			});

			$('body').on('keyup','.form_bill_inputs',function(){
				var con = $(this);
				var form_id = con.attr('id');
				var form_value = con.val();
				var rule_id = "rule_"+form_id;
				var rules  = $('#'+rule_id).val();
				try {
					rules = JSON.parse(rules);
				} catch(e){
					rules = [];
				}
				var has_error=[];
				if(rules.data_type == 'string'){
					if(!validString(form_value))
						has_error.push({id:form_id,msg:"This field should contain alphanumeric characters only"});
				} else if (rules.data_type == 'int'){
					if(!validNumber(form_value))
						has_error.push({id:form_id,msg:"This field should contain numbers only"});
				} else if (rules.data_type == 'date'){
					if(!validateDate(form_value))
						has_error.push({id:form_id,msg:"This field should be a valid date(mm/dd/yyyy)"});
				}
				if(has_error.length){
					for(var i in has_error){
						$('#group_'+has_error[i].id).addClass('has-error');
						$('#msg_error_'+has_error[i].id).html(has_error[i].msg);
					}
				} else {
					$('#group_'+form_id).removeClass('has-error');
					$('#msg_error_'+form_id).html('');
				}

			});
			$('body').on('change','#req_bill_id',function(){
				var id = $(this).val();
				if(id){
					$('#form_container').html("<p class='text-center'><i class='fa fa-circle-o-notch fa-spin fa-3x'></i></p>")
					$.ajax({
					    url:'../ajax/ajax_e_bills.php',
					    type:'POST',
					    data: {functionName:'showFormCompany',id:id},
					    success: function(data){
						    $('#form_container').html(data)
					    },
					    error:function(){
					        
					    }
					});
				}
			});
			$('body').on('change','#req_category_id',function(){
				var id = $(this).val();
				if(id){
					$.ajax({
					    url:'../ajax/ajax_e_bills.php',
					    type:'POST',
					    data: {functionName:'getCompanyByCategory',id:id},
					    success: function(data){
						    if(data == 1){
							    alertify.alert("Invalid data.");
						    } else if(data == 2){
							    alertify.alert("No company found.");
						    } else {
							    $('#req_bill_id').html(data);
						    }

					    },
					    error:function(){

					    }
					})
				}
			});
			/*************End Bill Request ****************/
			/************ BILLER NAME *****************/
			$('body').on('click','#btnSameAsBiller',function(){
				var id = $('#same_as_biller_id').val();
				var cur_id = $('#biller_id_field').val();
				if(id == cur_id){
					alertify.alert("Selected company is invalid.");
					$('#same_as_biller_id').val('');
					return;
				}
				if(id){
					$.ajax({
						url:'../ajax/ajax_e_bills.php',
						type:'POST',
						data: {functionName:'sameForms', id:id,cur_id:cur_id},
						success: function(data){
							alertify.alert(data,function(){
								$('#myModal').modal('hide');
								getName();
							});
						},
						error:function(){

						}
					});
				} else {
					alertify.alert("Choose company name");
				}

			});
			$('body').on('click', '#btnAddMore', function() {
				$("#clonethis").clone().appendTo("#appendclone");
			});
			$('body').on('click', '#btnSubmitFields', function(e) {
				e.preventDefault();
				var json = $('#custom_fields_form').serializeArray();
				var biller_id = $('#biller_id_field').val();
				var ctr = 0;
				var finalList = [];
				var item = {element_name: 0, choices: 0, data_type: 0, label: '', is_required: ''};
				var item_ctr = 0;
				for(var i in json) {
					if(ctr > 0) {
						if(item_ctr == 0) {
							item.element_name = json[i].value;
							item_ctr++;
						} else if(item_ctr == 1) {
							item.choices = json[i].value;
							item_ctr++;
						} else if(item_ctr == 2) {
							item.data_type = json[i].value;
							item_ctr++;
						} else if(item_ctr == 3) {
							item.label = json[i].value;
							item_ctr++;
						} else if(item_ctr == 4) {
							item.is_required = json[i].value;
							finalList.push(item);
							item = {element_name: 0, choices: 0, data_type: 0, label: '', is_required: ''};
							item_ctr = 0;
						}
					}
					ctr++;
				}
				if(finalList.length) {
					if(!biller_id) {
						alertify.alert("Please enter biller name.");
						return;
					}
					$.ajax({
						url: '../ajax/ajax_e_bills.php',
						type: 'POST',
						data: {functionName: 'saveFields', data: JSON.stringify(finalList), biller_id: biller_id},
						success: function(data) {
							alertify.alert(data,function(){
								$('#myModal').modal('hide');
								getName();
							});
						},
						error: function() {

						}
					})
				}
			});
			$("#myModal").on("hidden.bs.modal", function() {
				$('#myModal > .modal-dialog').removeClass('modal-lg');
			});
			$('body').on('click', '.btnFields', function() {
				$('#mbody').html($('#con_modal_custom_fields').html());
				$('#myModal > .modal-dialog').addClass('modal-lg');
				var con = $(this);
				var biller_id = con.attr('data-id');
				var biller_name = con.attr('data-name');
				$('#biller_name_field').html(biller_name);
				$('#biller_id_field').val(biller_id);
				$('#myModal').modal('show');
			});
			$('body').on('click', '#btnAddBillerName', function() {
				$('#mbody').html($('#con_modal_name').html());
				$('#biller_name').val('');
				$('#biller_id').val('');
				$('#biller_category_id').val('');
				$('#myModal').modal('show');
			});
			$('body').on('click', '#btnBillerNameSave', function() {
				var name = $('#biller_name').val();
				var id = $('#biller_id').val();
				var category_id = $('#biller_category_id').val();
				var con = $(this);
				button_action.start_loading(con);
				$.ajax({
					url: '../ajax/ajax_e_bills.php',
					type: 'POST',
					data: {functionName: 'addBillerName', name: name, id: id, category_id: category_id},
					success: function(data) {
						tempToast('info', data, 'Info');
						$('#myModal').modal('hide');
						getName();
						button_action.end_loading(con);
					},
					error: function() {
						button_action.end_loading(con);
					}
				});
			});
			$('body').on('click', '.btnUpdateBillerName', function() {
				var con = $(this);
				var id = con.attr('data-id');
				var name = con.attr('data-name');
				var category_id = con.attr('data-category_id');
				$('#mbody').html($('#con_modal_name').html());
				$('#biller_category_id').val(category_id);
				$('#biller_id').val(id);
				$('#biller_name').val(name);
				$('#myModal').modal('show');
			});
			$('body').on('click', '.btnDeleteBillerName', function() {
				var con = $(this);
				var id = con.attr('data-id');
				alertify.confirm('Are you sure you want to delete this company?', function(e) {
					if(e) {
						$.ajax({
							url: '../ajax/ajax_e_bills.php',
							type: 'POST',
							data: {functionName: 'deleteBillername', id: id},
							success: function(data) {
								tempToast('info', data, 'Info');
								getName();
							},
							error: function() {

							}
						})
					}
				});

			});

			/************ END BILLER NAME *****************/
			/************ CATEGORY *****************/
			$('body').on('click', '#btnAddCategory', function() {
				$('#mbody').html($('#con_modal_category').html());
				$('#category_id').val('');
				$('#category_name').val('');
				$('#myModal').modal('show');
			});

			$('body').on('click', '.btnUpdateCategory', function() {
				var con = $(this);
				var id = con.attr('data-id');
				var name = con.attr('data-name');
				$('#mbody').html($('#con_modal_category').html());
				$('#category_id').val(id);
				$('#category_name').val(name);
				$('#myModal').modal('show');

			});
			$('body').on('click', '.btnDeleteCategory', function() {
				var con = $(this);
				var id = con.attr('data-id');
				alertify.confirm('Are you sure you want to delete this category?', function(e) {
					if(e) {
						$.ajax({
							url: '../ajax/ajax_e_bills.php',
							type: 'POST',
							data: {functionName: 'deleteCategory', id: id},
							success: function(data) {
								tempToast('info', data, 'Info');
								getCategory();
							},
							error: function() {

							}
						})
					}
				});

			});

			$('body').on('click', '#btnCategorySave', function() {
				var name = $('#category_name').val();
				var category_id = $('#category_id').val();
				var con = $(this);
				button_action.start_loading(con);
				$.ajax({
					url: '../ajax/ajax_e_bills.php',
					type: 'POST',
					data: {functionName: 'addCategory', name: name, id: category_id},
					success: function(data) {
						tempToast('info', data, 'Info');
						$('#myModal').modal('hide');
						getCategory();
						button_action.end_loading(con);
					},
					error: function() {
						button_action.end_loading(con);
					}
				});

			});
			/************ END CATEGORY *************/

			$('body').on('click', '.btn_nav', function(e) {
				e.preventDefault();
				var con = $(this).attr('data-con');
				if(con == 1) {
					showContainer(true, false, false, false,false);
				} else if(con == 2) {
					showContainer(false, true, false, false,false);
				} else if(con == 3) {
					showContainer(false, false, true, false,false);
				} else if(con == 4) {
					showContainer(false, false, false, true,false);
				} else if(con == 5) {
					showContainer(false, false, false,false, true);
				}
				$('#secondNavigationContainer').hide();
			});


			function showContainer(c1, c2, c3, c4,c5) {
				var con_req = $('#con_req');
				var con_pending = $('#con_pending');
				var con_category = $('#con_category');
				var con_name = $('#con_name');
				var con_my_request = $('#con_my_request');
				con_req.hide();
				con_pending.hide();
				con_category.hide();
				con_name.hide();
				con_my_request.hide();
				if(c1) {
					con_req.fadeIn(300);
				} else if(c2) {
					con_pending.fadeIn(300);
					getPending();
				} else if(c3) {
					con_category.fadeIn(300);
					getCategory();
				} else if(c4) {
					con_name.fadeIn(300);
					getName();
				}else if(c5) {
					con_my_request.fadeIn(300);
					getMyPending();

				}
			}
			$('body').on('keyup','.txtAmount',function(){
				var con = $(this);
				var v = con.val();

				if(isNaN(v) || !v){
					v = 0;
				}

				var convenience_fee  = $('#form_convenience_fee').val();
				var total = parseFloat(convenience_fee) + parseFloat(v);

				$('#form_grand_total').val(total.toFixed(2));
			});
		});
	</script><?php require_once '../includes/admin/page_tail2.php'; ?>