<?php
	// $user have all the properties and method of the current user
	require_once '../includes/admin/page_head2.php';
	include 'constants.php';

	if(!$user->hasPermission('ar')) {
		// redirect to denied page
		Redirect::to(1);
	}

	$barcodeClass = new Barcode();
	$barcode_format = $barcodeClass->get_cr_format($user->data()->company_id);
	$styles =  '';

	$by_branch = $barcodeClass->get_format_by_branch($user->data()->branch_id,"CR");

	if($by_branch){
		$barcode_format = $by_branch;
	}

	$by_user = $barcodeClass->get_format_by_user($user->data()->id,"CR");

	if($by_user){
		$barcode_format = $by_user;
	}

	if($barcode_format){
		$styles =  $barcode_format->styling;
	}

	$sales_type = new Sales_type();
	$sales_types = $sales_type->get_active('salestypes',array('company_id','=',$user->data()->company_id));


	$is_agent = 0;
	$agent_name ='';
	if($user->hasPermission('wh_agent')){
		$is_agent = 1;
		$agent_name = ucwords($user->data()->firstname . " ". $user->data()->lastname);
	}



// ...

usort($collection_report_view, function($a, $b) { // Make sure to give this a more meaningful name!
	return $a['order'] - $b['order'];
});


?>
	<!-- Page content -->
	<div id="page-content-wrapper">
		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<?php include 'includes/accounting_nav.php' ?>
			<!-----------------    AR   -------------------->
			<input type="hidden" id='is_agent' value='<?php echo $is_agent; ?>'>
			<input type="hidden" id='agent_name' value='<?php echo $agent_name; ?>'>

			<div id="con_ar" style='display:none;'>

				<h3>Account receivable</h3>

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Accounting</div>
					<div class="panel-body">
						<div class="row" >
							<div class="col-md-3">
								<?php
									$sales_type_ar_cls = new Sales_type();
									$types_ar = $sales_type_ar_cls->getMySalesType($user->data()->id);
								?>
								<div class="form-group">
								<select name="salestype_ar" id="salestype_ar" class='form-control'>
									<?php
										$is_ar_first = true;
										foreach($types_ar as $st){
											if($is_ar_first) {
												$selected = 'selected';
												$is_ar_first = false;
											} else {
												$selected = '';
											}
											echo  "<option value='$st->id' $selected>$st->name</option>";
										}
										if($http_host == 'cebuhiq.apollosystems.com.ph'){
											echo  "<option value='-1'>All</option>";
										}
									?>
								</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select name="is_service_ar" id="is_service_ar" class='form-control'>
										<option value="0">Sales</option>
										<option value="1">Service</option>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" autocomplete="off" class='form-control' placeholder='Date From' id='dtFromAr'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" autocomplete="off" class='form-control' placeholder='Date To' id='dtToAr'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control'  id='userIdAr'>
								</div>
							</div>
						</div>
						<div id='container1'></div>
					</div>
				</div>

			</div>

			<!----------------- END AR --------------------->

			<!-----------------    SOA --------------------->
			<div id="con_soa" style='display:none;'>
				<h3>Statement of Account</h3>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Accounting</div>
					<div class="panel-body" >
						<div class="row">
							<div class="col-md-4">
								<input type="text" class='form-control' id='member_id'>
							</div>
							<div class="col-md-4">
								<input type="checkbox" id='soa_cancel'> <label for="soa_cancel">Show Cancelled</label>
								<input type="checkbox" id='soa_fully_paid'> <label for="soa_fully_paid">Show Fully Paid</label>
							</div>
							<div class="col-md-4">
								<button class='btn btn-default' id='btnGetSOA'>Get SOA</button>
							</div>
							<div class="col-md-4"></div>
						</div>
						<br>
						<div id='container2'></div>
					</div>
				</div>

			</div>
			<!----------------- END SOA --------------------->

			<!-----------------   COLLECTION --------------------->
			<div id="con_collection" style='display:none;'>
				<h3>Collection report</h3>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Accounting</div>
					<div class="panel-body"  >
						<div class="row">
							<div class="col-md-6"></div>
							<div class="col-md-6 text-right">
								<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
										<a class='btn btn-default btn_nav_cr' data-con='1' title='Transaction List' href='#'> <span class='glyphicon glyphicon-list-alt'></span> <span class='hidden-xs'>Transaction List </span></a>
										<a class='btn btn-default btn_nav_cr' data-con='2' title='CR List' href='#'> <span class='glyphicon glyphicon-book'></span> <span class='hidden-xs'>CR List </span></a>
									<a class='btn btn-default'  title='Delete' href='cr_list_agent_report.php'> <span class='glyphicon glyphicon-user'></span> <span class='hidden-xs'>CR By Type </span></a>

									<a class='btn btn-default'  title='Layout' href='cr_layout.php'> <span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>CR Layout </span></a>
										<?php if($user->hasPermission('cr_delete')){
											?>
											<a class='btn btn-default'  title='Delete' href='update_cr_records.php'> <span class='glyphicon glyphicon-trash'></span> <span class='hidden-xs'>Delete from CR </span></a>

											<?php
										}?>

								</div>
							</div>
						</div>
						<div id='transaction_list_container'>
							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
										<select name="salestype" id="salestype" class='form-control' multiple>
											<option value=""></option>
											<?php
												foreach($sales_types as $st){
													$curid = (isset($id)) ?  $editMem->data()->salestype : 0;
													if($st->id == $curid) {
														$selected = 'selected';
													} else {
														$selected = '';
													}
													echo  "<option value='$st->id' $selected>$st->name</option>";
												}
											?>
										</select>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" autocomplete="off" class='form-control' id='cr_date1' placeholder='Date From'>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" autocomplete="off" class='form-control' id='cr_date2' placeholder='Date To'>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<select name="cr_terminal_id" id="cr_terminal_id" class='form-control'>
											<option value="">Select Terminal</option>
											<?php
												$terminal = new Terminal();
												$terminals = $terminal->get_active('terminals',['1','=',1]);
												if($terminals){
													foreach($terminals as $t){
														?>
														<option value="<?php echo $t->id; ?>"><?php echo $t->name; ?></option>
														<?php
													}
												}
											?>
										</select>

									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<input type='text' name="cr_agent_id" id="cr_agent_id" class='form-control'>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<input type='text' name="cr_user_id" id="cr_user_id" class='form-control'>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<input type='text' name="cr_paid_by" id="cr_paid_by" class='form-control'>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<select name="cr_type" id="cr_type" class='form-control' multiple>
											<option value="1">Cash</option>
											<option value="2">Credit Card</option>
											<option value="3">Check</option>
											<option value="4">Bank Transfer</option>
										</select>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<select name="from_service" id="from_service" class='form-control'>
											<option value="0">From Sales</option>
											<option value="1">From Service</option>
										</select>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<input placeholder='CR Number' type='text' name="cr_cr_num" id="cr_cr_num" class='form-control'>
										<input  type='hidden' name="cr_cr_date" id="cr_cr_date" >
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" name='cr_include_dr' id='cr_include_dr' class='form-control' placeholder='<?php echo "Include " . DR_LABEL; ?>'>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" name='cr_include_ir' id='cr_include_ir' class='form-control' placeholder='<?php echo "Include " . PR_LABEL; ?>'>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<select name="show_with_cr" id="show_with_cr" class='form-control'>
											<option value="0">Hide with CR number</option>
											<option value="1">Show with CR number</option>
										</select>
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group">
										<button class='btn btn-default' id='btnFilterReport'><i class='fa fa-search'></i> Filter</button>
									</div>
								</div>
							</div>
							<div id='container3'><div class="alert alert-info">Search Collection</div></div>
						</div>
						<div id='cr_list_container' style='display:none;'>
							<h3>Collection report list</h3>
							<div class="row">
								<div class="col-md-12 text-right">
									<div class="form-group">
										<button class='btn btn-default' id='btnCrListDownload'>Download</button>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" id='cr_num_from' class='form-control' placeholder='CR NUMBER FROM'>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" id='cr_num_to' class='form-control' placeholder='CR NUMBER TO'>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<select name="cr_sort_type" id="cr_sort_type" class='form-control'>
											<option value="1">By date desc</option>
											<option value="2">By date asc</option>
											<option value="3">By CR desc</option>
											<option value="4">By CR asc</option>
										</select>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" id='cr_branch_id' class='form-control'>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
										<input type="hidden" class='form-control' id='cr_list_agent_id'>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" class='form-control' id='cr_list_from' placeholder='Date From'>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" class='form-control' id='cr_list_to' placeholder='Date To'>
									</div>
								</div>
								<div class="col-md-3">

								</div>
								<div class="col-md-3">
									<div class="form-group">
										<button class='btn btn-default' id='btnCrListFilter'>Submit</button>

									</div>
								</div>

							</div>

							<div id="container3_cr"></div>
						</div>
					</div>
				</div>
			</div>
			<!----------------- END COLLECTION --------------------->
			<!-----------------    DSS --------------------->
			<div id="con_dss" style='display:none;'>
				<h3>Series Summary</h3>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Accounting</div>
					<div class="panel-body" >
						<div class="row">
							<div class="col-md-3">
								<input type="text" placeholder='Enter date from' class='form-control' id='txt_dss_date_1'>
							</div>
							<div class="col-md-3">
								<input type="text" placeholder='Enter date to' class='form-control' id='txt_dss_date_2'>
							</div>
							<div class="col-md-3">
								<button class='btn btn-default' id='btnGetDSS'>Get Daily Series</button>
							</div>
							<div class="col-md-3"></div>
						</div>
						<br>
						<div id='container4'></div>
					</div>
				</div>
			</div>
			<!----------------- END DSS --------------------->
			<!-----------------    DSS --------------------->
			<div id="con_sts" style='display:none;'>
				<h3>Sales Type Summary</h3>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Accounting</div>
					<div class="panel-body" >
						<div class="row">
							<div class="col-md-4">
								<input type="text" placeholder='Enter Year' class='form-control' id='txt_sts_year'>
							</div>
							<div class="col-md-4">
								<button class='btn btn-default' id='btnGetSalesTypeSummary'>Get Sales Type Summary</button>
							</div>
							<div class="col-md-4"></div>
						</div>
						<br>
						<div class="panel panel-default">
							<div class="panel-body">
								<h4 class='text-center'>Summary</h4>
								<div id='container5_graph' style='height:400px;'></div>
							</div>
						</div>

						<div id='container5'></div>

					</div>
				</div>
			</div>
			<!----------------- END DSS --------------------->
			<!-----------------Monthly Sales Summary ------------->
			<div id="con_monthly_sum" style='display:none;'>
				<h3>Yearly Summary</h3>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Summary</div>
					<div class="panel-body" >
						<div class="row">
							<div class="col-md-4">
								<select name="branch_id" id="branch_id" class='form-control'>
									<?php
										$branch = new Branch();
										$branches =  $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));
										foreach($branches as $b){
											$a = $user->data()->branch_id;
											if($a==$b->id){
												$selected='selected';
											} else {
												$selected='';
											}
											?>
											<option value='<?php echo $b->id ?>' <?php echo $selected ?>><?php echo $b->name;?> </option>
											<?php
										}
									?>
									<option value="-1">Service</option>

								</select>
							</div>
							<div class="col-md-4">
								<button class='btn btn-default' id='btnGetSalesSummary'>Get Sales Summary</button>
							</div>
							<div class="col-md-4"></div>
						</div>
						<br>


						<div id='container6'></div>

					</div>
				</div>
			</div>

			<!-----------------END Sales Summary ------------->

			<!-----------------Top Client ------------->
			<div id="con_monthly_client" style='display:none;'>
				<h3>Top Client</h3>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Summary</div>
					<div class="panel-body" >
						<div class="row">
							<div class="col-md-4">
								<input type="text" id='topClientMonth' class='form-control' placeholder='Choose Month'>
							</div>
							<div class="col-md-4">
								<button class='btn btn-default' id='btnGetTopClient'>Get Summary</button>
							</div>
							<div class="col-md-4"></div>
						</div>
						<br>


						<div id='container7'></div>

					</div>
				</div>
			</div>

			<!-----------------End Top Client------------->

			<!-----------------Area ------------->
			<div id="con_area" style='display:none;'>
				<h3>Area Summary</h3>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Summary</div>
					<div class="panel-body" >
						<h3>Area Monthly Summary</h3>
						<div class="row">
							<div class="col-md-4">
								<input type="text" id='area_year' class='form-control' placeholder='Choose Year'>
							</div>
							<div class="col-md-4">
								<button class='btn btn-default' id='btnGetArea'>Get Summary</button>
							</div>
							<div class="col-md-4"></div>
						</div>
						<br>


						<div id='container8'></div>

						<hr>
						<h3>Area Overall Summary</h3>
						<div class="row">
							<div class="col-md-4">
								<input type="text" id='area_dt_from' class='form-control' placeholder='Date From'>
							</div>
							<div class="col-md-4">
								<input type="text" id='area_dt_to' class='form-control' placeholder='Date To'>
							</div>
							<div class="col-md-4">
								<button class='btn btn-default' id='btnGetAreaOA'>Get Summary</button>
							</div>
							<div class="col-md-4"></div>
						</div>
						<br>


						<div id='container9'></div>



					</div>
				</div>
			</div>

			<!-----------------End Area ------------->

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

	<script>
		$(function() {
			var styles = '<?php echo $styles; ?>';
			var is_agent = '<?php if($user->hasPermission('wh_agent')) echo 1; else echo 0; ?>';
			var terminal_id = localStorage['terminal_id'];


			if(terminal_id){
				$('#cr_terminal_id').val(terminal_id);
			}
			$("#cr_agent_id").select2({
				placeholder: 'Search Agent',
				allowClear: true,
				minimumInputLength: 2,
				ajax: {
					url: '../ajax/ajax_json.php',
					dataType: 'json',
					type: "POST",
					quietMillis: 50,
					data: function (term) {
						return {
							q: term,
							functionName:'users'
						};
					},
					results: function (data) {
						return {
							results: $.map(data, function (item) {
								return {
									text: item.lastname + ", " + item.firstname + " " + item.middlename,
									slug: item.lastname + ", " + item.firstname + " " + item.middlename,
									id: item.id
								}
							})
						};
					}
				}
			});

			var agent_name = $('#agent_name').val();
			if(is_agent == 1){
				$('#cr_agent_id').select2('data', {id: is_agent, text: agent_name});
				$('#cr_agent_id').select2('enable',false);
			}
			$('#salestype').select2({
				placeholder: 'Search Sales Type',
				allowClear: true
			});

			$('#cr_type').select2({
				placeholder: 'Search Payment Type',
				allowClear: true
			});

			$("#cr_user_id").select2({
				placeholder: 'Search User',
				allowClear: true,
				multiple: true,
				minimumInputLength: 2,
				ajax: {
					url: '../ajax/ajax_json.php',
					dataType: 'json',
					type: "POST",
					quietMillis: 50,
					data: function (term) {
						return {
							q: term,
							functionName:'users'
						};
					},
					results: function (data) {
						return {
							results: $.map(data, function (item) {
								return {
									text: item.lastname + ", " + item.firstname + " " + item.middlename,
									slug: item.lastname + ", " + item.firstname + " " + item.middlename,
									id: item.id
								}
							})
						};
					}
				}
			});


			$('body').on('click','.btnCrDate',function(){
				var con = $(this);
				var cr_date = con.attr('data-cr_date');
				var cr_number = con.attr('data-cr_number');
				var override = con.attr('data-override');
				override = (override)? 1 : 0;
				var input_date_old ="<input type='hidden' value='"+cr_date+"' id='edit_date_old'>";
				var input_override ="<input type='hidden' value='"+override+"' id='edit_override'>";
				var input_cr_number_old ="<input type='hidden' value='"+cr_number+"' id='edit_cr_number_old'>";
				var input_date ="Date: <input type='text' value='"+cr_date+"' id='edit_date'  class='form-control'>";
				var input_submit = "<button class='btn btn-default' id='btnUpdateCr'>Save</button>";
				var html = input_date + "<br>";
				 html += input_override;
				 html += input_date_old;
				 html += input_cr_number_old;
				 html += input_submit + "<br>";
				$('#mbody').html(html);
				$('#myModal').modal('show');

			});

			$('body').on('click','#btnUpdateCr',function(){
				var date_old = $('#edit_date_old').val();
				var cr_number_old = $('#edit_cr_number_old').val();
				var date = $('#edit_date').val();
				var override = $('#edit_override').val();
				override = (override == 1) ? 1 : 0;

				$('#myModal').modal('hide');

				$.ajax({
				    url:'../ajax/ajax_accounting.php',
				    type:'POST',
				    data: {
					    functionName:'updateCrDate',
					    override:override,
					    date_old:date_old,
					    cr_number_old:cr_number_old,
					    date:date
				    },
				    success: function(data){
					    showCRList();
				    },
				    error:function(){

				    }
				});

			});

			$("#userIdAr").select2({
				placeholder: 'Search Agent',
				allowClear: true,
				multiple: true,
				minimumInputLength: 2,
				ajax: {
					url: '../ajax/ajax_json.php',
					dataType: 'json',
					type: "POST",
					quietMillis: 50,
					data: function (term) {
						return {
							q: term,
							functionName:'users'
						};
					},
					results: function (data) {
						return {
							results: $.map(data, function (item) {
								return {
									text: item.lastname + ", " + item.firstname + " " + item.middlename,
									slug: item.lastname + ", " + item.firstname + " " + item.middlename,
									id: item.id
								}
							})
						};
					}
				}
			});
			$('body').on('click','.btnApproveCredit',function(e){
				e.preventDefault();
				var id = $(this).attr('data-id');
				alertify.confirm("Are you sure want to APPROVE this request?",function(e){
					if(e){
						$.ajax({
							url:'../ajax/ajax_member_service.php',
							type:'POST',
							data: {functionName:'approveMemberCredit',id:id},
							success: function(data){
								tempToast('info',data,'Info');
								getCollectionReport();
							},
							error:function(){

							}
						});
					}
				});
			});
			$('body').on('click','.btnUpdateCredit',function(e){
				e.preventDefault();

				var con = $(this);
				var pid = con.attr('data-pid');
				var id = con.attr('data-id');
				var m = con.attr('data-method');
				var cr_log_id = con.attr('data-cr_log_id');
				cr_log_id = (cr_log_id) ? cr_log_id : '';

				$.ajax({
				    url:'../ajax/ajax_member_service.php',
				    type:'POST',
				    data: {functionName:'updatePayment',id:id,pid:pid,m:m ,cr_log_id:cr_log_id},
				    success: function(data){
					    $('#mbody').html(data);
					    $('#myModal').modal('show');

				    },
				    error:function(){

				    }
				});

			});

			$('body').on('click','.btnDeclineCredit',function(e){
				e.preventDefault();
				var id = $(this).attr('data-id');
				alertify.confirm("Are you sure want to DECLINE this request?",function(e){
					if(e){
						$.ajax({
							url:'../ajax/ajax_member_service.php',
							type:'POST',
							data: {functionName:'declineMemberCredit',id:id},
							success: function(data){
								tempToast('info',data,'Info');
								getCollectionReport();
							},
							error:function(){

							}
						});
					}
				});
			});


			$("#cr_list_agent_id").select2({
				placeholder: 'Search Agent',
				allowClear: true,
				minimumInputLength: 2,
				ajax: {
					url: '../ajax/ajax_json.php',
					dataType: 'json',
					type: "POST",
					quietMillis: 50,
					data: function (term) {
						return {
							q: term,
							functionName:'users'
						};
					},
					results: function (data) {
						return {
							results: $.map(data, function (item) {
								return {
									text: item.lastname + ", " + item.firstname + " " + item.middlename,
									slug: item.lastname + ", " + item.firstname + " " + item.middlename,
									id: item.id
								}
							})
						};
					}
				}
			});
			$("#cr_paid_by").select2({
				placeholder: 'Receive By',
				allowClear: true,
				multiple: true,
				minimumInputLength: 2,
				ajax: {
					url: '../ajax/ajax_json.php',
					dataType: 'json',
					type: "POST",
					quietMillis: 50,
					data: function (term) {
						return {
							q: term,
							functionName:'users'
						};
					},
					results: function (data) {
						return {
							results: $.map(data, function (item) {
								return {
									text: item.lastname + ", " + item.firstname + " " + item.middlename,
									slug: item.lastname + ", " + item.firstname + " " + item.middlename,
									id: item.id
								}
							})
						};
					}
				}
			});

			if(is_agent == 1){
				showContainer(true, false, false,false,false,false,false,false); // ar view
			} else {
				showContainer(false, false, true,false,false,false,false,false); // cr view
			}


			$('body').on('click', '.btn_nav', function(e) {
				e.preventDefault();
				var con = $(this).attr('data-con');
				if(con == 1) {
					showContainer(true, false, false,false,false,false,false,false);
				} else if(con == 2) {
					showContainer(false, true, false,false,false,false,false,false);
				} else if(con == 3) {
					showContainer(false, false, true,false,false,false,false,false);
				} else if(con == 4) {
					showContainer(false, false, false,true,false,false,false,false);
				} else if(con == 5) {
					showContainer(false, false, false,false,true,false,false,false);
				} else if(con == 6) {
					showContainer(false, false, false,false,false,true,false,false);
				}else if(con == 7) {
					showContainer(false, false, false,false,false,false,true,false);
				}else if(con == 8) {
					showContainer(false, false, false,false,false,false,false,true);
				}
			});
			function showContainer(c1, c2, c3,c4,c5,c6,c7,c8) {
				var con_ar = $('#con_ar');
				var con_soa = $('#con_soa');
				var con_collection = $('#con_collection');
				var con_dss = $('#con_dss');
				var con_sts = $('#con_sts');
				var con_yearly = $('#con_monthly_sum');
				var con_client = $('#con_monthly_client');
				var con_area = $('#con_area');
				con_ar.hide();
				con_soa.hide();
				con_collection.hide();
				con_sts.hide();
				con_dss.hide();
				con_yearly.hide();
				con_client.hide();
				con_area.hide();
				if(c1) {
					con_ar.fadeIn(300);
					getAR();
				} else if(c2) {
					con_soa.fadeIn(300);
					getSOA();
				} else if(c3) {
					con_collection.fadeIn(300);
				//	getCollectionReport();
				}else if(c4) {
					con_dss.fadeIn(300);
					getDailySeriesSummary();
				}else if(c5) {
					con_sts.fadeIn(300);
					getSalesTypeSummary();
					getSalesTypeMonthly();
				}else if(c6) {
					con_yearly.fadeIn(300);
					getSalesSummary();

				} else if(c7) {
					con_client.fadeIn(300);
					getTopClient();

				}else if(c8) {
					con_area.fadeIn(300);
					getAreaSummary();
					getAreaOASummary();

				}
			}
			$('body').on('click','#btnGetDSS',function(){
				getDailySeriesSummary();
			});
			$('body').on('click','#btnGetSalesSummary',function(){
				getSalesSummary();
			});
			$('body').on('click','#btnGetSalesTypeSummary',function(){
				getSalesTypeSummary();
				getSalesTypeMonthly();
			});
			$('body').on('click','#btnGetTopClient',function(){
				getTopClient();
			});
			$('#topClientMonth').datepicker({
				autoclose:true,
				format: "mm-yyyy",
				viewMode: "months",
				minViewMode: "months"
			}).on('changeDate', function(ev){
				$('#topClientMonth').datepicker('hide');
			});
			$('#cr_list_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#cr_list_from').datepicker('hide');
			});
			$('#cr_list_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#cr_list_to').datepicker('hide');
			});
			$('#area_dt_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#area_dt_from').datepicker('hide');
			});
			$('#area_dt_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#area_dt_to').datepicker('hide');
			});

			$('body').on('click','#btnCrListFilter',function(){
				showCRList(0);
			});
			$('body').on('click','#btnCrListDownload',function(){
				showCRListDownload();
			});
			$('body').on('click','#btnGetArea',function(){
				getAreaSummary();
			});
			$('body').on('click','#btnGetAreaOA',function(){
				getAreaOASummary();
			});
			function getAreaOASummary(){
				var dt1 = $('#area_dt_from').val();
				var dt2 = $('#area_dt_to').val();
				$.ajax({
				    url:'../ajax/ajax_accounting.php',
				    type:'POST',
				    data: {functionName:'regionOA',dt1:dt1,dt2:dt2},
				    success: function(data){
						$('#container9').html(data);
				    },
				    error:function(){

				    }
				})
			}
			function getAreaSummary(){
				var y = $('#area_year').val();
				$.ajax({
				    url:'../ajax/ajax_accounting.php',
				    type:'POST',
				    data: {functionName:'regionSummary',dt:y},
				    success: function(data){
						$('#container8').html(data);
				    },
				    error:function(){

				    }
				})
			}

			function getTopClient(){
				var dt = $('#topClientMonth').val();
				$.ajax({
					url:'../ajax/ajax_accounting.php',
					type:'POST',
					data: {functionName:'topClient',dt:dt},
					success: function(data){
						$('#container7').html(data);
					},
					error:function(){

					}
				});
			}
			function getSalesSummary(){

				var branch_id = $('#branch_id').val();
				$.ajax({
				    url:'../ajax/ajax_accounting.php',
				    type:'POST',
				    data: {functionName:'salesSummary',branch_id:branch_id},
				    success: function(data){
				        $('#container6').html(data);
				    },
				    error:function(){

				    }
				});
			}
			function getSalesTypeMonthly(){
				var dt = $('#txt_sts_year').val();
				$.ajax({
					url:'../ajax/ajax_accounting.php',
					type:'post',
					dataType:'json',
					data: {functionName:'graphSalesMonthly',dt:dt},
					success: function(data) {
						$('#container5_graph').html('');
						if (data.error){
							$('#container5_graph').html('No data found');
						} else {
							var a =0;
							Morris.Bar({
								element: 'container5_graph',
								data: data,
								xkey: 'y',
								ykeys: ['a'],
								labels: ['Sales'],
								xLabelAngle: 35,
								padding: 40,
								hideHover: 'auto',
								barOpacity: 0.9,
								barRadius: [10, 10, 5, 5],
								barColors: function(row, series, type) {
									a = a + 1;
									if(a % 2 == 0) return "#B21516"; else return "#1531B2";
								},
								hoverCallback: function(index, options, content) {
									var data = options.data[index];
									return("<p> "+data.y + "<br><span class='text-danger'> P. " + number_format(data.a,2) +"</span></p>");
								}
							});
						}
					},
					error:function(){

					}
				});
			}

			function getSalesTypeSummary(){
				var dt = $('#txt_sts_year').val();
				$.ajax({
					url:'../ajax/ajax_accounting.php',
					type:'POST',
					data: {functionName:'sts',dt:dt},
					success: function(data){
						$('#container5').html(data);
					},
					error:function(){

					}
				});
			}

			function getDailySeriesSummary(){

				var dt1 = $('#txt_dss_date_1').val();
				var dt2 = $('#txt_dss_date_2').val();
				$('#container4').html('Loading...');
				$.ajax({
					url:'../ajax/ajax_accounting.php',
					type:'POST',
					data: {functionName:'dss',dt1:dt1,dt2:dt2},
					success: function(data){
						$('#container4').html(data);
					},
					error:function(){

					}
				});

			}

			$('body').on('click','#btnSubmitAR',function(){
				getAR();
			});

			$('body').on('change','#salestype_ar,#is_service_ar',function(){
				getAR();
			});

			function getAR(){
				var dt1 = $('#dtFromAr').val();
				var dt2 = $('#dtToAr').val();
				var salestype_ar = $('#salestype_ar').val();
				var is_service_ar = $('#is_service_ar').val();
				if(dt1 && !dt2){
					return;
				}
				if(dt2 && !dt1){
					return;
				}
				$.ajax({
				    url:'../ajax/ajax_accounting.php',
				    type:'POST',
					beforeSend: function(){
						$('#container1').html('Loading...');
					},
				    data: {functionName:'ar2',dt1:dt1,dt2:dt2,salestype_ar:salestype_ar,is_service_ar:is_service_ar},
				    success: function(data){
				        $('#container1').html(data);
				    },
				    error:function(){

				    }
				});
			}

			$('body').on('click','#btnSummary',function(){
				var summary_details = $('#summary_details').val();
				try{
					 summary_details = JSON.parse(summary_details);
					 var html_ret = '<h3>Summary</h3>';
					 html_ret += '<ul class="list-group">';
					 var total = 0;
						for(var i in summary_details){
							total += parseFloat(summary_details[i]);
							html_ret += "<li class='list-group-item'><span class='badge'>"+number_format(summary_details[i],2)+"</span>"+i+"</li>"
						}
					 html_ret += "<li class='list-group-item'><span class='badge'>"+number_format(total,2)+"</span><strong>Total</strong></li>";
					 html_ret += '</ul>';
					 $('.right-panel-pane').fadeIn(100);
					 $('#right-pane-container').html(html_ret);

				} catch(e){
					$('.right-panel-pane').fadeIn(100);
					$('#right-pane-container').html("Error getting the summary");
				}
			});

			$("#member_id").select2({
				placeholder: 'Search Member',
				allowClear: true,
				minimumInputLength: 2,
				ajax: {
					url: '../ajax/ajax_json.php',
					dataType: 'json',
					type: "POST",
					quietMillis: 50,
					data: function (term) {
						return {
							q: term,
							functionName:'members'
						};
					},
					results: function (data) {
						return {
							results: $.map(data, function (item) {
								return {
									text: item.lastname + ", " + item.firstname + " " + item.middlename,
									slug: item.lastname + ", " + item.firstname + " " + item.middlename,
									id: item.id
								}
							})
						};
					}
				}
			});

			function getSOA(){

				var member_id = $('#member_id').val();
				var soa_cancel = $('#soa_cancel').is(':checked');
				var soa_fully_paid = $('#soa_fully_paid').is(':checked');
				soa_cancel= (soa_cancel) ? soa_cancel : 0;
				soa_fully_paid= (soa_fully_paid) ? soa_fully_paid : 0;
				$('#container2').html("Loading...");
				$.ajax({
					url:'../ajax/ajax_accounting.php',
					type:'POST',
					data: {functionName:'getSOA',member_id:member_id,soa_fully_paid:soa_fully_paid,soa_cancel:soa_cancel},
					success: function(data){
						$('#container2').html(data);
					},
					error:function(){

					}
				});


			}

			$('#dtFromAr').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dtFromAr').datepicker('hide');
				getAR();
			});

			$('#dtToAr').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dtToAr').datepicker('hide');
				getAR();
			});
			$('#ar_dt1').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#ar_dt1').datepicker('hide');
			});

			$('#ar_dt2').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#ar_dt2').datepicker('hide');
			});

			$('#txt_dss_date_1').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#txt_dss_date_1').datepicker('hide');
			});
			$('#txt_dss_date_2').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#txt_dss_date_2').datepicker('hide');
			});

			$('#cr_date1').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#cr_date1').datepicker('hide');
			});

			$('#cr_date2').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#cr_date2').datepicker('hide');
			});

			$('body').on('click','#btnHideUncheck',function(){

				$('#table-collection-report > table > tbody > tr').each(function(){
					var row = $(this);
					var chk = row.children().eq(15).find('.chkBatch').is(':checked');
					if(!chk){
						row.remove();
					}
				});
				calculateFooter();

			});

			$('body').on('change','.chkBatch',function(){
				var row = 	$(this).parents('tr');
				row.toggleClass("bg-info");
				var chker = hasCheckEntry();
				if(chker){
					$('#btnHideUncheck').show();
					console.log("with chk")
				} else {
					$('#btnHideUncheck').hide();
					console.log("without chk")
				}
			});

			function hasCheckEntry(){
				var chkr = false;
				$('#table-collection-report > table > tbody > tr').each(function(){
					var row = $(this);
					var chk = row.children().eq(15).find('.chkBatch').is(':checked');
					if(chk){
						chkr =  true;
					}
				});
				return chkr;
			}
			$('body').on('click','.btnRemoveTransaction',function(){

				$(this).parents('tr').remove();

				calculateFooter();

			});
			function calculateFooter(){
				var total_receipt_amount = 0;
				var total_deduction = 0;
				var total_paid = 0;
				var total_com = 0;

				$('#table-collection-report > table > tbody > tr').each(function(){

					var row = $(this);

					var receipt_amount = row.children().eq(4).text();

					var deduction = row.children().eq(5).text();

					var paid_amount = row.children().eq(6).text();
					var com_amount = row.children().eq(11).text();

					receipt_amount = replaceAll(receipt_amount,',','');
					deduction = replaceAll(deduction,',','');
					paid_amount = replaceAll(paid_amount,',','');
					com_amount = replaceAll(com_amount,',','');

					if(receipt_amount){
						total_receipt_amount = parseFloat(total_receipt_amount) + parseFloat(receipt_amount);
					}

					if(deduction){
						total_deduction = parseFloat(total_deduction) + parseFloat(deduction);
					}

					if(paid_amount){
						total_paid = parseFloat(total_paid) + parseFloat(paid_amount);
					}
					if(com_amount){
						total_com = parseFloat(total_com) + parseFloat(com_amount);
					}

				});

				$('#footer_receipt_amount').text(number_format(total_receipt_amount,2))
				$('#footer_deduction').text(number_format(total_deduction,2))
				$('#footer_collected_amount').text(number_format(total_paid,2))
				$('#footer_com').text(number_format(total_com,2))
			}

			$('body').on('click','#btnApproveAgentCR',function(){

				var con = $(this);
				var cr_number = con.attr('data-cr_number');
				alertify.confirm("Are you sure you want to process this request?", function(e){
					if(e){
						$.ajax({
							url:'../ajax/ajax_accounting.php',
							type:'POST',
							data: {functionName:'finalizeCRAgent',cr_number:cr_number},
							success: function(data){
								alertify.alert(data);
								getCollectionReport();
							},
							error:function(){

							}
						});
					}
				});


			});

			$('body').on('click','#btnSaveCRNumber',function(){
				var btncon = $(this);
				var has_pending_for_approval = $('#has_pending_for_approval').val();
				if(has_pending_for_approval == 1){
					alert("You need to approve/decline the pending request first.");
					return;
				}
				button_action.start_loading(btncon);



				var crNumber = $('#crNumber').val();
				var cr_payment_ids = $('#cr_payment_ids').val();
				var cr_log_ids = $('#cr_log_ids').val();
				var type = $('#cr_type').val();
				var dt = $('#cr_date1').val();
				var dt_to = $('#cr_date2').val();
				var user_id = $('#cr_user_id').val();
				var paid_by = $('#cr_paid_by').val();
				var cr_include_dr = $('#cr_include_dr').val();
				var cr_include_ir = $('#cr_include_ir').val();
				var from_service = $('#from_service').val();
				var cr_override = $('#overrided_item_per_page').val();
				var agent_id = $('#cr_agent_id').val();
				var crDate = $('#crDate').val();

				if(crNumber && cr_payment_ids){

					var det_arr = [];
					var rec_per_cr = $('#overrided_item_per_page').val();
					var count_row = 0;
					$('#table-collection-report > table > tbody > tr').each(function(){

						var row = $(this);
						var id = row.attr('data-id');
						var method = row.attr('data-method');
						var pid = row.attr('data-pid');
						var com =  row.children().eq(11).text();

						var ar_number =  row.children().eq(13).find('.txt_ar').val();
						var cr_slip =  row.children().eq(13).find('.txt_cr').val();

						com = (com) ? com : 0;
						ar_number = (ar_number) ? ar_number : '';

						det_arr.push({
							delivery_date: row.children().eq(0).text(),
							delivery_receipt: row.children().eq(1).text(),
							sales_invoice: row.children().eq(2).text(),
							client_name: row.children().eq(3).text(),
							receipt_amount: row.children().eq(4).text(),
							deduction: row.children().eq(5).text(),
							paid_amount: row.children().eq(6).text(),
							bank_name: row.children().eq(7).text(),
							check_no: row.children().eq(8).text(),
							check_date: row.children().eq(9).text(),
							terms: row.children().eq(10).text(),
							comm: com,
							ar_number: ar_number,
							cr_slip: cr_slip,
							id: id,
							method: method,
							pid: pid
						});
						count_row += 1;
					});

					var page_count = Math.ceil(count_row / rec_per_cr);
					var warning = "Total transactions: " + count_row;
					warning += "<br> CR Number(s): ";
					var crtosaved = crNumber;
					for(var i =0; i < page_count; i++){

						warning+= crtosaved + ",";
						crtosaved = parseFloat(crNumber) + parseFloat(1);
					}

					warning = warning.slice(0, -1);

					var arr_deposits = [];

					$('#tblDeposits > tbody > tr').each(function(){
						var row = $(this);
						var chk = row.children().eq(7).find('.chkDeposit');

						if(chk.is(':checked')){
							arr_deposits.push(chk.val());
						}
					});



					alertify.confirm(warning + "<br>Are you sure you want to continue?", function(e){
						if(e){
							$.ajax({
								url:'../ajax/ajax_accounting.php',
								type:'POST',
								data: {
									functionName:'updateCrNumber',
									crDate:crDate,
									agent_id:agent_id,
									cr_log_ids:cr_log_ids,
									cr_override:cr_override,
									det_arr: JSON.stringify(det_arr),
									arr_deposits: JSON.stringify(arr_deposits),
									from_service:from_service,
									cr_include_dr:cr_include_dr,
									cr_include_ir:cr_include_ir,
									dt_to:dt_to,user_id:user_id,
									paid_by:paid_by,type:type,
									dt:dt,
									payment_ids:cr_payment_ids,
									crNumber:crNumber
								},
								success: function(data){
									alertify.alert(data);
									getCollectionReport();
									button_action.end_loading(btncon);
								},
								error:function(){
									button_action.end_loading(btncon);
								}
							});
						} else {
							button_action.end_loading(btncon);
						}
					});
				} else {
					button_action.end_loading(btncon);
					alertify.alert("Please enter CR number.");
				}

			});
			$('body').on('change','#from_service',function(){

				if($('#from_service').val() == 1){
					$('#salestype option').each(function(){
						var opt = $(this).val();

						if(opt){
							$(this).prop('selected',true);
						}
					});
					$('#salestype').select2({
						placeholder: 'Search Sales Type',
						allowClear: true
					});
				} else {

				}

			});

			$('body').on('click','.btnShowDataCR',function(){

				$('#cr_date1').val('');
				$('#cr_date2').val('');

				$('#cr_include_dr').val('');
				$('#cr_include_ir').val('');

				$('#cr_type').select2('val',null);
				$('#cr_user_id').select2('val',null);
				$('#cr_paid_by').select2('val',null);

				var cr_number = $(this).attr('data-cr_number');
				var pm  = $(this).attr('data-pm');
				var dt  = $(this).attr('data-dt');
				var dt_to  = $(this).attr('data-dt_to');
				var paid_by  = $(this).attr('data-paid_by');
				var paid_by_name  = $(this).attr('data-paid_by_name');
				var cashier_id  = $(this).attr('data-cashier_id');
				var cashier_name  = $(this).attr('data-cashier_name');
				var include_dr  = $(this).attr('data-include_dr');
				var include_ir  = $(this).attr('data-include_ir');
				var from_service  = $(this).attr('data-from_service');
				var cr_cr_date  = $(this).attr('data-cr_date');
				$('#cr_cr_date').val(cr_cr_date);
				if(dt && dt != "0"){
					$('#cr_date1').val(dt);
					$('#cr_date2').val(dt_to);
				}
				if(include_dr){
					$('#cr_include_dr').val(include_dr);
				}

				if(include_ir){
					$('#cr_include_ir').val(include_ir);
				}

				$('#from_service').val(from_service);

				if(paid_by && paid_by != "0"){
					var paid_by_data = [
						{ id: paid_by, text: paid_by_name},
					];
					$('#cr_paid_by').select2('data', paid_by_data)
				}
				if(cashier_id && cashier_id != "0"){
					var cashier_data = [
						{ id: cashier_id, text: cashier_name},
					];
					$('#cr_user_id').select2('data', cashier_data)
				}
				try{
					pm = JSON.parse(pm);
					$('#cr_type').select2('val',pm);
				} catch(e){

				}

				$('#cr_cr_num').val(cr_number);
				getCollectionReport();
				$('#cr_list_container').hide();
				$('#transaction_list_container').fadeIn(300);
			});


			$('body').on('click','.btn_nav_cr',function(){
				var t = $(this).attr('data-con');

				$('#cr_list_container').hide();
				$('#transaction_list_container').hide();

				if(t == 1){
					$('#transaction_list_container').fadeIn(300);
				} else if (t == 2){
					$('#cr_list_container').fadeIn(300);
					showCRList(0);
				} else {
					$('#transaction_list_container').fadeIn(300);
				}

			});


			$('body').on('click','.btnRemoveCR',function(){
				var con = $(this);
				var cr_number = con.attr('data-cr_number');
				alertify.confirm("Are you sure you want to remove this record", function(e){
					if(e){
						$.ajax({
							url:  '../ajax/ajax_accounting.php',
							type: 'POST',
							data: { functionName:'removeCR', cr_number:cr_number},
							success: function(data){
								showToast('info',data,"Info");
								showCRList(0);
							},
							error:function(){

							}
						});
					}
				})
			});



			$('#cr_branch_id').select2({
				placeholder: 'Branch',
				allowClear: true,
				minimumInputLength: 2,
				ajax: {
					url: '../ajax/ajax_json.php',
					dataType: 'json',
					type: "POST",
					quietMillis: 50,
					data: function (term) {
						return {
							q: term,
							functionName:'branches'
						};
					},
					results: function (data) {
						return {
							results: $.map(data, function (item) {
								return {
									text: item.name ,
									slug: item.name ,
									id: item.id
								}
							})
						};
					}
				}
			});

			function showCRList(is_dl){
				$('#container3_cr').html('Loading...');
				var dt_from  = $('#cr_list_from').val();
				var dt_to  = $('#cr_list_to').val();
				var num_from  = $('#cr_num_from').val();
				var num_to  = $('#cr_num_to').val();
				var agent_id  = $('#cr_list_agent_id').val();
				var sort_type  = $('#cr_sort_type').val();
				var branch_id  = $('#cr_branch_id').val();

				$.ajax({
					url:'../ajax/ajax_accounting.php',
					type:'POST',
					data: {functionName:'collectionReportList',branch_id:branch_id,sort_type:sort_type,num_from:num_from,num_to:num_to,agent_id:agent_id,is_dl:is_dl,dt_from:dt_from,dt_to:dt_to},
					success: function(data){
						$('#container3_cr').html(data);
					},
					error:function(){

					}
				});

			}

			function showCRListDownload(){

				var dt_from  = $('#cr_list_from').val();
				var dt_to  = $('#cr_list_to').val();
				var agent_id  = $('#cr_list_agent_id').val();
				var branch_id  = $('#cr_branch_id').val();

				window.open(
					'../ajax/ajax_accounting.php?functionName=collectionReportList&dt_from='+dt_from+'&dt_to='+dt_to+'&is_dl=1'+'&agent_id='+agent_id+'&branch_id='+branch_id,
					'_blank' // <- This is what makes it open in a new window.
				);

			}

			function getCollectionReport(){

				var salestype = $('#salestype').val();
				var date_from = $('#cr_date1').val();
				var date_to = $('#cr_date2').val();
				var terminal_id = $('#cr_terminal_id').val();
				var user_id = $('#cr_user_id').val();
				var agent_id = $('#cr_agent_id').val();
				var paid_by = $('#cr_paid_by').val();
				var type = $('#cr_type').val();
				var cr_num = $('#cr_cr_num').val();
				var from_service = $('#from_service').val();
				var show_with_cr = $('#show_with_cr').val();
				var cr_include_dr = $('#cr_include_dr').val();
				var cr_include_ir = $('#cr_include_ir').val();

				$('.loading', window.parent.document).show();

				$.ajax({
					url:'../ajax/ajax_accounting.php',
					type:'POST',
					beforeSend:function(){
						$('#container3').html('Loading...');
					},
					data: {functionName:'collectionReport',agent_id:agent_id,cr_include_ir:cr_include_ir,cr_include_dr:cr_include_dr,show_with_cr:show_with_cr,from_service:from_service,cr_num:cr_num,paid_by:paid_by,user_id:user_id,terminal_id:terminal_id,type:type,dt1:date_from,dt2:date_to,salestype:salestype},
					success: function(data){
						$('#container3').html(data);
						$('#crDate').datepicker({
							autoclose:true
						}).on('changeDate', function(ev){
							$('#crDate').datepicker('hide');
						});
						$('.loading', window.parent.document).hide();
					},
					error:function(){
					}
				});

			}

			function getExcelCollectionReport(){

				var salestype = $('#salestype').val();
				var date_from = $('#cr_date1').val();
				var date_to = $('#cr_date2').val();
				var terminal_id = $('#cr_terminal_id').val();
				var user_id = $('#cr_user_id').val();
				var type = $('#cr_type').val();
				var cr_num = $('#cr_cr_num').val();
				var hideDeduction = $('#hideDeduction').is(':checked');

				if(hideDeduction){
					hideDeduction = 1;
				} else {
					hideDeduction = 0;
				}

				salestype = JSON.stringify(salestype);
				type = JSON.stringify(type);
				var table_cr = $('#table-collection-report').html();

				/*	window.open(
					'../ajax/ajax_accounting.php?functionName=excelCollectionReport&dt1='+date_from+'&dt2='+date_to+'&salestype='+salestype+'&terminal_id='+terminal_id+'&user_id='+user_id+'&type='+type+'&cr_num='+cr_num+'&hideDeduction='+hideDeduction,
					'_blank' // <- This is what makes it open in a new window.
				); */

				var form = document.createElement("form");
				form.setAttribute("method", "post");
				form.setAttribute("action", "../ajax/ajax_accounting.php");

				form.setAttribute("target", "view");

				var hiddenField = document.createElement("input");
				hiddenField.setAttribute("type", "hidden");
				hiddenField.setAttribute("name", "table");
				hiddenField.setAttribute("value", table_cr);
				form.appendChild(hiddenField);
				var hiddenField2 = document.createElement("input");
				hiddenField2.setAttribute("type", "hidden");
				hiddenField2.setAttribute("name", "functionName");
				hiddenField2.setAttribute("value", 'excelCR');
				form.appendChild(hiddenField2);
				document.body.appendChild(form);

				window.open('', 'view');

				form.submit();

			}
			function printSOA(member_id){
				var soa_cancel = $('#soa_cancel').is(':checked');
				var soa_fully_paid = $('#soa_fully_paid').is(':checked');
				soa_cancel= (soa_cancel) ? soa_cancel : 0;
				soa_fully_paid= (soa_fully_paid) ? soa_fully_paid : 0;

				window.open(
					'../ajax/ajax_accounting.php?functionName=samplePrint&member_id='+member_id+'&soa_cancel='+soa_cancel+'&soa_fully_paid='+soa_fully_paid,
					'_blank' // <- This is what makes it open in a new window.
				);
			}
			function printCR(){
				var salestype = $('#salestype').val();
				var date_from = $('#cr_date1').val();
				var date_to = $('#cr_date2').val();
				var terminal_id = $('#cr_terminal_id').val();
				var user_id = $('#cr_user_id').val();
				var type = $('#cr_type').val();
				var cr_num = $('#cr_cr_num').val();
				/*
				window.open(
					'../ajax/ajax_accounting.php?functionName=printCollectionReport&dt1='+date_from+'&dt2='+date_to+'&salestype='+salestype+'&terminal_id='+terminal_id+'&user_id='+user_id+'&type='+type+'&cr_num='+cr_num,
					'_blank' // <- This is what makes it open in a new window.
				); */
				printEmptyCR();

			}

			function printEmptyCR(){

				var det_arr = [];

				$('#table-collection-report > table > tbody > tr').each(function(){
					var row = $(this);
					var sales_invoice = replaceAll(row.children().eq(2).text(),'Gal.','');
					 sales_invoice = replaceAll(sales_invoice,'gal.','');
					sales_invoice = (sales_invoice) ? sales_invoice : '';
					sales_invoice = sales_invoice.trim();
					var ar_number =  row.children().eq(13).find('input').val();
					ar_number = (ar_number) ? ar_number : '';
					det_arr.push(
						{
							delivery_date: row.children().eq(0).text(),
							delivery_receipt: row.children().eq(1).text(),
							sales_invoice: sales_invoice,
							client_name: row.children().eq(3).text(),
							receipt_amount: row.children().eq(4).text(),
							deduction: row.children().eq(5).text(),
							paid_amount: row.children().eq(6).text(),
							bank_name: row.children().eq(7).text(),
							check_no: row.children().eq(8).text(),
							check_date: row.children().eq(9).text(),
							terms: row.children().eq(10).text(),
							ar_number:ar_number,
						}
					);

				});

				var receipt_amount = $('#footer_receipt_amount').text();
				var deduction = $('#footer_deduction').text();
				var collected_amount = $('#footer_collected_amount').text();

				$.ajax({
					url:'../ajax/ajax_accounting.php',
					type:'POST',
					dataType:'json',
					data: {functionName:'crEmpty', items: JSON.stringify(det_arr)},
					success: function(data){
						button_action.end_loading($('#btnPrintCollectionReport'));
						prepareCollectionReport(data.result,data.type,collected_amount,receipt_amount,deduction);
					},
					error:function(){
						button_action.end_loading($('#btnPrintCollectionReport'));
					}
				})
			}

			function prepareCollectionReport(data,type,total_collected,total_receipt,total_deduction){
				var cur_date = Date.now() /1000;
				var d = new Date(cur_date * 1000);
				var month = d.getMonth()+1;
				var day = d.getDate();
				var salestype = $('#salestype option:selected').text();
				var date_output = (month<10 ? '0' : '') + month + '/' +
					(day<10 ? '0' : '') + day + '/' + d.getFullYear();
				var crDate = $('#crDate').val();
				var crDate_save = $('#cr_cr_date').val();
				if(crDate){
					date_output = crDate;
				} else if (crDate_save){
					date_output = crDate_save;
				}
				var layout;
				try{
					layout = JSON.parse(styles);
				} catch(e){
					layout = false;
				}
				var itemtablestyle = "style='position:absolute;top:" + layout['itemtable'].top+"px;left:"+layout['itemtable'].left+"px;font-size:"+layout['itemtable'].fontSize+"px;'";
				var datestyle = "style='position:absolute;top:" + layout['date'].top+"px;left:"+layout['date'].left+"px;font-size:"+layout['date'].fontSize+"px;'";
				var salestypestyle = "style='position:absolute;top:" + layout['salestype'].top+"px;left:"+layout['salestype'].left+"px;font-size:"+layout['salestype'].fontSize+"px;'";
				var totalamountstyle = "style='position:absolute;top:" + layout['totalamount'].top+"px;left:"+layout['totalamount'].left+"px;font-size:"+layout['totalamount'].fontSize+"px;'";
				var totalreceiptstyle = "style='position:absolute;top:" + layout['totalreceipt'].top+"px;left:"+layout['totalreceipt'].left+"px;font-size:"+layout['totalreceipt'].fontSize+"px;'";
				var deductionstyle = "style='position:absolute;top:" + layout['deduction'].top+"px;left:"+layout['deduction'].left+"px;font-size:"+layout['deduction'].fontSize+"px;'";
				var printhtml = "";
				var fontFamily = "font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif;";
				printhtml = printhtml + "<div id='maindivforprinting' style='page-break-before: always;position:relative;"+fontFamily+"'>&nbsp;";
				printhtml= printhtml +  "<div "+datestyle+">"+  date_output+ " </div><div style='clear:both;'></div>";
				printhtml= printhtml +  "<div  "+salestypestyle+">"+  salestype+ " </div><div style='clear:both;'></div>";
				printhtml += "<table "+itemtablestyle+">";
				var page_arr = [];

				var num_per_page = parseInt($('#overrided_item_per_page').val()) + 1;
				num_per_page = (num_per_page) ? num_per_page : 23;
				if(isNaN(num_per_page)){
					alertify.alert("Invalid number of items per page.");
					return;
				}
				var page_head = printhtml;
				var page_tail = "";
				page_tail += "</div>";
				console.log("Ok");
				if(data.length){
					console.log("Ok1");
					var ctr = 1;
					var content = "";
					var cur_total_collected = 0;
					var cur_total_receipt = 0;
					var cur_total_deduction = 0;

					for(var i in data){
						var footer ='';
						if( ctr % num_per_page == 0){
							// footer = "<tr><td></td><td></td><td></td><td></td><td>"+number_format(cur_total_receipt,2)+"</td><td>"+number_format(cur_total_deduction,2)+"</td><td>"+number_format(cur_total_collected,2)+"</td><td></td><td></td><td></td><td></td></tr>";
							footer = "</table>";
							footer += "<div  "+totalreceiptstyle+">"+number_format(cur_total_receipt,2)+" </div><div style='clear:both;'></div>";
							footer += "<div  "+deductionstyle+">"+number_format(cur_total_deduction,2)+" </div><div style='clear:both;'></div>";
							footer += "<div  "+totalamountstyle+">"+number_format(cur_total_collected,2)+" </div><div style='clear:both;'></div>";
							footer += "</div>";
							cur_total_collected = 0;
							cur_total_receipt = 0;
							cur_total_deduction = 0;
							page_arr.push(page_head + content + footer + page_tail);
							content = "";
						}
						var row = "<tr style='line-height:20px;'>";

						for(var j in data[i]){

							var style_col = "col" + (parseInt(j) + 1);
							var v = data[i][j];
							var td_styles="";

							if(layout[style_col]){
								td_styles += "width:"+layout[style_col].width+"px;padding-left:"+layout[style_col].left+"px;";
							}
							if(type == 1){ // NORMAL VIEW
								if(j == 3){  // client
									if(v && v.length > 16){
										v = v.substr(0,16)
									}
								}

								if(j == 4){ // receipt amount
									if(v && !isNaN(v)){
										cur_total_receipt = parseFloat(cur_total_receipt) + parseFloat(v);

									} else {
										v = 0;
									}
									//console.log(cur_total_receipt  + " + " + parseFloat(v) + " = " + cur_total_receipt );
									v = number_format(v,2);
									td_styles += "text-align:right;";
								}

								if(j == 5){ // DEDUCTION
									if(v){
										var tempdeduction = v.split('|');
										v = tempdeduction[0];
										if(v && !isNaN(v)){
											cur_total_deduction = parseFloat(cur_total_deduction) + parseFloat(v);
										}

										if(v == '0.00'){
											v= '';
										}
										if(v == '0'){
											v= '';
										}
										if(tempdeduction[1]){
											v += tempdeduction[1];
										}

										td_styles += "text-align:center;";
									} else {
										v= '';
									}

								}

								if(j == 6){ // paid amount
									var temp = replaceAll(v,'Cash'," ");
									temp = replaceAll(temp,'Cheque'," ");

									var splitp = temp.split(":");
									var amount = 0;
									//console.log(splitp);
									splitp = splitp.map(function(value){
										return value.trim();
									});
									if(splitp[0]){
										amount = replaceAll(splitp[0],",","") + parseFloat(amount);
									}
									if(splitp[1]){
										amount = replaceAll(splitp[1],",","") + parseFloat(amount);
									}
									if(splitp[2]){
										amount = parseFloat(replaceAll(splitp[2],",","")) + parseFloat(amount);
									}
									if(splitp[3]){
										amount = parseFloat(replaceAll(splitp[3],",","")) + parseFloat(amount);
									}
									cur_total_collected = parseFloat(cur_total_collected) + parseFloat(amount);
									td_styles += "text-align:right;";

									if(!isNaN(v)){
										v = number_format(v,2);
									}
								}

							} else if (type == 2){

								if(j == 1){  // client
									if(v && v.length > 16){
										v = v.substr(0,16) + "..."
									}
								}

								if(j == 4){  // receipt amount
									if(v && !isNaN(v)){
										cur_total_receipt = parseFloat(cur_total_receipt) + parseFloat(v);

									} else {
										v = 0;
									}
									//console.log(cur_total_receipt  + " + " + parseFloat(v) + " = " + cur_total_receipt );
									v = number_format(v,2);
									td_styles += "text-align:right;";
								}

								if(j == 5){ // DEDUCTION
									if(v){
										var tempdeduction = v.split('|');
										v = tempdeduction[0];
										if(v && !isNaN(v)){
											cur_total_deduction = parseFloat(cur_total_deduction) + parseFloat(v);

										}

										if(v == '0.00'){
											v= '';
										}
										if(v == '0'){
											v= '';
										}
										if(tempdeduction[1]){
											v += tempdeduction[1];
										}

										td_styles += "text-align:center;";
									} else {
										v= '';
									}

								}

								if(j == 6){  // paid amount
									var temp = replaceAll(v,'Cash'," ");
									temp = replaceAll(temp,'Cheque'," ");

									var splitp = temp.split(":");
									var amount = 0;
									//console.log(splitp);
									splitp = splitp.map(function(value){
										return value.trim();
									});
									if(splitp[0]){
										amount = replaceAll(splitp[0],",","") + parseFloat(amount);
									}
									if(splitp[1]){
										amount = replaceAll(splitp[1],",","") + parseFloat(amount);
									}
									if(splitp[2]){
										amount = parseFloat(replaceAll(splitp[2],",","")) + parseFloat(amount);
									}
									if(splitp[3]){
										amount = parseFloat(replaceAll(splitp[3],",","")) + parseFloat(amount);
									}
									cur_total_collected = parseFloat(cur_total_collected) + parseFloat(amount);
									td_styles += "text-align:right;";

									if(!isNaN(v)){
										v = number_format(v,2);
									}

								}
							}


							var td = "<td style='"+td_styles+"'>";

							td +=v;
							td += "</td>";
							row += td;

						}
						row += "</tr>";
						//console.log(row);
						content += row;
						ctr++;
					}
					if(content != ""){
						while(ctr % num_per_page != 0){
							content += "<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
							ctr ++;
						}
						//var f  = "<tr><td></td><td></td><td></td><td></td><td>"+number_format(cur_total_receipt,2)+"</td><td>"+number_format(cur_total_deduction,2)+"</td><td>"+number_format(cur_total_collected,2)+"</td><td></td><td></td><td></td><td></td></tr>";
						var f = "</table>";
						f += "<div  "+totalreceiptstyle+">"+number_format(cur_total_receipt,2)+" </div><div style='clear:both;'></div>";
						f += "<div  "+deductionstyle+">"+number_format(cur_total_deduction,2)+" </div><div style='clear:both;'></div>";
						f += "<div  "+totalamountstyle+">"+number_format(cur_total_collected,2)+" </div><div style='clear:both;'></div>";
						f += "</div>";

						page_arr.push(page_head + content +  f +page_tail);
						content = "";
					}
				}

				var all_pages = "";
				for(var p in page_arr){
					all_pages += page_arr[p];
				}
				Popup(all_pages);
			}

			function Popup(data)
			{

				var mywindow = window.open('', 'new div', '');
				mywindow.document.write('<html><head><title></title><style></style>');
				mywindow.document.write('</head><body style="padding:0;margin:0;">');
				mywindow.document.write(data);
				mywindow.document.write('</body></html>');
				mywindow.print();
				mywindow.close();
				return true;

			}

			function excelSOA(member_id){
				var soa_cancel = $('#soa_cancel').is(':checked');
				var soa_fully_paid = $('#soa_fully_paid').is(':checked');
				soa_cancel= (soa_cancel) ? soa_cancel : 0;
				soa_fully_paid= (soa_fully_paid) ? soa_fully_paid : 0;
				window.open(
					'../ajax/ajax_accounting.php?functionName=excelSOA&member_id='+member_id+'&soa_cancel='+soa_cancel+'&soa_fully_paid='+soa_fully_paid,
					'_blank' // <- This is what makes it open in a new window.
				);
			}

			function arExcel(){
				var sales_type_ar =  $('#salestype_ar').val();
				var is_service_ar = $('#is_service_ar').val();
				var dt1 = $('#dtFromAr').val();
				var dt2 = $('#dtToAr').val();

				window.open(
					'../ajax/ajax_accounting.php?functionName=excelAr2&salestype_ar='+sales_type_ar+'&is_service_ar='+is_service_ar+'&dt1='+dt1+'&dt2='+dt2,
					'_blank' // <- This is what makes it open in a new window.
				);
			}

			$('body').on('click','#btnArExcel',function(){
				arExcel();
			});
			$('body').on('click','#btnGetSOA',function(){
				getSOA();
			});
			$('body').on('click','#btnDetailsSOA',function(){
				var html = $('#all_payment_details').html();
				$('.right-panel-pane').fadeIn(100);
				$('#right-pane-container').html(html);
			});
			$('body').on('click','#btnFilterReport',function(){
				getCollectionReport()
			});
			$('body').on('click','#btnExcelCollectionReport',function(){
				getExcelCollectionReport()
			});

			$('body').on('click','#btnPrintSOA',function(){
				var member_id = $(this).attr('data-member_id');
				printSOA(member_id);
			});
			$('body').on('click','#btnExcelSOA',function(){
				var member_id = $(this).attr('data-member_id');
				excelSOA(member_id);
			});
			$('body').on('click','#btnPrintCollectionReport',function(){
				button_action.start_loading($('#btnPrintCollectionReport'));
				printCR();
			});

			$('body').on('click','#btnCheckUpdate',function(){
				var con = $(this);
				var id = $('#ch_update_id').val();
				var check_number = $('#ch_update_check_number').val();
				var bank = $('#ch_update_bank').val();
				var maturity_date = $('#ch_update_maturity_date').val();
				var cr_log_id  =  $('#update_cr_log_id').val();
				$.ajax({
				    url:'../ajax/ajax_accounting.php',
				    type:'POST',
				    data: {functionName:'updatePaymentMethod',method:'cheque',cr_log_id:cr_log_id,id:id,check_number:check_number,bank:bank,maturity_date:maturity_date},
				    success: function(data){
					    tempToast('info',data,'Info');
					    getCollectionReport();
					    $('#myModal').modal('hide');
				    },
				    error:function(){

				    }
				});


			});
			$('body').on('click','#btnBTUpdate',function(){
				var con = $(this);
				var id = $('#bt_update_id').val();
				var bankfrom_name = $('#bt_update_bankfrom_name').val();
				var bankfrom_account_number = $('#bt_update_bankfrom_account_number').val();
				var date = $('#bt_update_date').val();
				var cr_log_id  =  $('#update_cr_log_id').val();
				$.ajax({
					url:'../ajax/ajax_accounting.php',
					type:'POST',
					data: {functionName:'updatePaymentMethod',method:'bt',cr_log_id:cr_log_id,id:id,bankfrom_name:bankfrom_name,bankfrom_account_number:bankfrom_account_number,date:date},
					success: function(data){
						tempToast('info',data,'Info');
						getCollectionReport();
						$('#myModal').modal('hide');
					},
					error:function(){

					}
				});


			});
			$('body').on('click','#btnCreditUpdate',function(){
				var con = $(this);
				var id =  $('#credit_update_id').val();
				var card_number =  $('#credit_update_card_number').val();
				var bank_name =  $('#credit_update_bank_name').val();
				var card_type =  $('#credit_update_card_type').val();
				var approval_code =  $('#credit_update_approval_code').val();
				var trace_number =  $('#credit_update_trace_number').val();
				var date =  $('#credit_update_date').val();
				var cr_log_id  =  $('#update_cr_log_id').val();

				$.ajax({
					url:'../ajax/ajax_accounting.php',
					type:'POST',
					data: {functionName:'updatePaymentMethod',cr_log_id:cr_log_id,method:'credit',id:id,card_number:card_number,bank_name:bank_name,card_type:card_type,approval_code:approval_code,trace_number:trace_number,date:date},
					success: function(data){
						tempToast('info',data,'Info');
						getCollectionReport();
						$('#myModal').modal('hide');
					},
					error:function(){

					}
				});

			});

		});
	</script><?php require_once '../includes/admin/page_tail2.php'; ?>