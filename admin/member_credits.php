<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('credit_monitoring')) {
		// redirect to denied page
		Redirect::to(1);
	}

?>


	<input type="hidden" id='MEMBER_LABEL' value='<?php echo MEMBER_LABEL; ?>'>
	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> <?php echo MEMBER_LABEL; ?>'s Credit
			</h1>
		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}
		?>

		<div class="row">
			<div class="col-md-12">
				<div style='margin:5px;'>

					<a  class='btn btn-primary btn-sm' href="member_credits_2.php">Document Tracking</a>
					<?php
						if($user->hasPermission('other_income')){
							?>
							<a  class='btn btn-primary btn-sm' href="other_income.php">Other Income</a>
							<?php
						}
					?>
					<?php if(Configuration::thisCompany('avision')) { ?>
						<a  class='btn btn-primary btn-sm' href="upload_avision_collection.php">Import Collection</a>

					<?php } ?>
				</div>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">
						<div class="row">
							<div class="col-md-6">
								<?php echo MEMBER_LABEL; ?>'s Credit
							</div>
							<div class="col-md-6 text-right">
								<button id='btnDownload' class='btn btn-primary'><i class='fa fa-download'></i></button>
								<button id='btnAddCredit' class='btn btn-primary'><i class='fa fa-plus'></i></button>
							</div>
						</div>
					</div>
					<div class="panel-body">
						<div class="row">

							<div class="col-md-3">
								<div class="input-group">
									<span class="input-group-addon"><span class='glyphicon glyphicon-search'></span></span>
									<input type="text" id="searchSales" class='form-control' placeholder='Search..'/>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon"><span class='glyphicon glyphicon-calendar'></span></span>
										<input type="text" id="dt_from" class='form-control' placeholder='Date From'/>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon"><span class='glyphicon glyphicon-calendar'></span></span>
										<input type="text" id="dt_to" class='form-control' placeholder='Date To'/>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<select name="paid_type" id="paid_type" class="form-control">
									<option value="">Choose type</option>
									<option value="1">Fully paid</option>
									<option value="2">Not yet paid</option>
									<?php if(Configuration::getValue('credit_approval') == 1){ ?>
									<option value="3">For approval</option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<select id="sales_type" name="sales_type" class="form-control" multiple>
										<option value=""></option>
										<?php
											$salestype = new Sales_type();
											$salestypes = $salestype->get_active('salestypes',array('company_id','=',$user->data()->company_id));
											foreach ($salestypes as $st):
												?>
												<option value='<?php echo $st->id ?>'><?php echo $st->name ?> </option>
												<?php
											endforeach;
										?>
									</select>
								</div>
							</div>
							<?php if($user->hasPermission('credit_all')){
								?>
								<div class="col-md-3">
									<div class="form-group">
										<select id="branch_id" name="branch_id" class="form-control" multiple>
											<?php
												if(Configuration::isAquabest()){
													?>
													<option value="-1">Caravan</option>
													<?php
												}
												$branch = new Branch();
												$branches =  $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));
												foreach($branches as $b){
													$a = isset($id) ? $terminal->data()->branch_id : escape(Input::get('branch_id'));
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
										</select>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<div id="terminalholder">
											<p class='text-info'></p>
										</div>
									</div>
								</div>
							<?php
							}?>

						</div>
						<input type="hidden" id="hiddenpage" />
						<div id="holder"></div>
					</div>


				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<div class="modal fade" id="myModalCredit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id='ctitle'>Add Credit <?php echo MEMBER_LABEL; ?></h4>
					</div>
					<div class="modal-body" id='cbody'>
						<div class="form-group">
						<strong><?php echo MEMBER_LABEL; ?></strong>
						<input type="text" id='member_id' placeholder='Member' class='form-control'>
						</div>
						<div class="form-group">
							<strong>Amount</strong>
							<input type="text" id='transaction_amount' class='form-control' placeholder='Credit amount'>
						</div>
						<div class="form-group">
							<strong>Date Sold</strong>
							<input type="text" id='transaction_date' placeholder='Date' class='form-control'>
						</div>
						<div class="form-group">
							<strong><?php echo INVOICE_LABEL; ?></strong>
							<input type="text" id='transaction_invoice' placeholder='<?php echo INVOICE_LABEL; ?>' class='form-control'>
						</div>
						<div class="form-group">
							<strong><?php echo DR_LABEL; ?></strong>
							<input type="text" id='transaction_dr' placeholder='<?php echo DR_LABEL; ?>' class='form-control'>
						</div>
						<div class="form-group">
							<strong><?php echo PR_LABEL ?></strong>
							<input type="text" id='transaction_pr' placeholder='<?php echo PR_LABEL ?>' class='form-control'>
						</div>
						<div class="form-group">
							<strong>Branch</strong>
							<select id="credit_branch_id" name="credit_branch_id" class="form-control">
								<option value=""></option>
								<?php
									$branch = new Branch();
									$branches =  $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));
									foreach($branches as $b){
										?>
										<option value='<?php echo $b->id ?>' ><?php echo $b->name;?> </option>
										<?php
									}
								?>
							</select>
						</div>
						<div class="form-group">
							<strong>Type</strong>
							<select class='form-control' name="is_service" id="is_service">
								<option value="0">For Sales</option>
								<option value="1">For Service</option>
							</select>
						</div>
						<div class="form-group">
							<strong>CR #</strong>
							<input type="text" id='cr_number' placeholder='CR Number (optional)' class='form-control'>
						</div>
						<div class="form-group">
							<button id='btnSubmitCredit' class='btn btn-default'>Submit Credit</button>
						</div>
					</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!--5 /.modal -->
	<div class="modal fade" id="myModalTerms" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id='ttitle'></h4>
					</div>
					<div class="modal-body" id='tbody'>
					</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!--4 /.modal -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id='mtitle'></h4>
					</div>
					<div class="modal-body" id='mbody'>
						<input type="hidden" id='creditId'>
						<input type="hidden" id='pendingAmountHid'>
						<div class="form-group">
							<strong>Total amount</strong>
							<p class='text-danger' id='totalAmount'></p>
						</div>
						<div class="form-group">
							<strong>Total amount</strong>
							<p class='text-danger' id='totalAmount'></p>
						</div>
						<div class="form-group">
							<strong>Paid amount</strong>
							<p class='text-danger' id='paidAmount'></p>
						</div>
						<div class="form-group">
							<strong>Unpaid amount</strong>
							<p class='text-danger' id='pendingAmount'></p>
						</div>
						<div class="form-group">
							<strong>Amount to pay</strong>
							<input type="text" class='form-control' id='txtAmount'>
						</div>
						<div class="form-group">
							<strong>Remarks</strong>
							<input type="text" class='form-control' id=''>
						</div>

						<div class="form-group">
							<button id='btnSubmitPayment' class='btn btn-primary'>Submit Payment</button>
						</div>
					</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!--3 /.modal -->
	<div class="modal fade" id="myModalName" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id='ntitle'></h4>
					</div>
					<div class="modal-body" id='nbody'>
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" id='hid_member_credit_id'>
									<div class="form-group">
										<strong>Remarks:</strong>
										<input type="text" id='txtMemberName' class='form-control'>
									</div>
									<div class="form-group">

										<button class='btn btn-default' id='btnSaveName'>SAVE</button>
									</div>
								</div>
							</div>
					</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!--2 /.modal -->
	<div class="modal fade" id="getpricemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
		<div class="modal-dialog" style='width:95%'>
			<div class="modal-content">
				<div class="modal-body">
					<div id='paymethods'>
						<div class="row">
							<div class="col-md-6">
								<div id="over_payment_holder" ></div>
								<input type="hidden" id='payment_member_credit_id'>
								<input type="hidden" id="hid_sr_cur_payment" />
								<input type="hidden" id="hidcashpayment" />
								<input type="hidden" id="hidcreditpayment" />
								<input type="hidden" id="hidbanktransferpayment" />
								<input type="hidden" id="hidchequepayment" />
								<input type="hidden" id="hidconsumablepayment" />
								<input type="hidden" id="hidconsumablepaymentfreebies" />
								<input type="hidden" id="hidmembercredit" />
								<span id='totalOfAllPayment' style='padding-left:10px;'></span>
								<input type="hidden" id="hidTotalOfAllPayment" />
								<span  id='amountdue' style='padding-left:10px;'></span>
								<input type="hidden" id="hidamountdue" />
								<input type="hidden" id="hidmemberdeduction" />
								<div  class='text-right'>
									<button style='display:none;' id='use_user_overpayment' class='btn btn-default btn-sm'>Deposits</button>
								</div>
							</div>
							<div class="col-md-6">

								<input placeholder='Remarks' type="text" class='form-control' id='paymentRemarks'>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4">
								<input type="text" id='override_payment_date' v-model='override_payment_date' class='form-control' placeholder='Override date (optional)'>
								<span class='help-block'>Use only if you want to override the date of payment.</span>

							</div>
						</div>

					</div>
					<hr>
					<ul class="nav nav-tabs">
						<li class="active"><a href="#tab_a" data-toggle="tab">Cash <span id='totalcashpayment' class='badge'></span></a></li>
						<li class='notcash'><a href="#tab_b" data-toggle="tab">Credit Card <span id='totalcreditpayment' class='badge'></span></a></li>
						<li class='notcash'><a href="#tab_c" data-toggle="tab">Bank Transfer <span id='totalbanktransferpayment' class='badge'></span></a></li>
						<li class='notcash'><a href="#tab_d" data-toggle="tab">Check 	<span id='totalchequepayment' class='badge'></span></a></li>
						<li class='notcash'><a href="#tab_e" data-toggle="tab">Consumable Amount <span id='totalconsumablepayment' class='badge'></span> </a></li>
						<li class='notcash'><a href="#tab_f" data-toggle="tab">Consumable Freebies <span id='totalconsumablepaymentfreebies' class='badge'></span> </a></li>
						<li style='display:none;'><a href="#tab_g" data-toggle="tab">Credit <span id='totalmembercredit' class='badge'></span> </a></li>
						<li class='notcashlist'><a href="#tab_h" data-toggle="tab">Deduction <span id='totalmemberdeduction' class='badge'></span> </a></li>

					</ul>
					<div class="tab-content" style='margin-top:10px;'>
						<?php include 'includes/payment_module.php'; ?>
					</div><!-- tab content -->
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!--1 /.modal -->

	<script>
//SELECT * FROM  `sales` WHERE  `item_id` =589 AND sold_date <1475251200

		$(document).ready(function() {
			var MEMBER_LABEL = $('#MEMBER_LABEL').val();
			$('#credit_branch_id').select2({
				allowClear: true,
				placeholder:'Select Branch'
			});

			$('#override_payment_date').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#override_payment_date').datepicker('hide');
			});

			$('#getpricemodal').on('shown.bs.modal', function() {
				$(document).off('focusin.modal');
			});

			$('body').on('click','.btnApproveCredit',function(){
				var id = $(this).attr('data-id');
				alertify.confirm("Are you sure want to approve this request?",function(e){
					if(e){
						$.ajax({
							url:'../ajax/ajax_member_service.php',
							type:'POST',
							data: {functionName:'approveMemberCredit',id:id},
							success: function(data){
								tempToast('info',data,'Info');
								getPage( $('#hiddenpage').val());
							},
							error:function(){

							}
						});
					}
				});
			});

			$('body').on('click','.btnDeclineCredit',function(){
				var id = $(this).attr('data-id');
				alertify.confirm("Are you sure want to decline this request?",function(e){
					if(e){
						$.ajax({
							url:'../ajax/ajax_member_service.php',
							type:'POST',
							data: {functionName:'declineMemberCredit',id:id},
							success: function(data){
								tempToast('info',data,'Info');
								getPage( $('#hiddenpage').val());
							},
							error:function(){

							}
						});
					}
				});
			});

			getPage(0);

			$('#member_id').select2({
				placeholder: 'Search ' + MEMBER_LABEL, allowClear: true, minimumInputLength: 2,

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

			$('body').on('click','#btnDownload',function(){

				alertify.confirm("Are you sure you want to continue?",function(e){

					var search = $('#searchSales').val();
					var paid_type =  $('#paid_type').val();
					var dt_from = $('#dt_from').val();
					var dt_to = $('#dt_to').val();
					var branch_id = $('#branch_id').val();
					var terminal_id = $('#terminal_id').val();
					var sales_type = $('#sales_type').val();

					if(branch_id){
						branch_id = JSON.stringify(branch_id);
					} else {
						branch_id = 0;
					}

					if(terminal_id){
						terminal_id = JSON.stringify(terminal_id);
					} else {
						terminal_id = 0;
					}

					if(sales_type){
						sales_type = JSON.stringify(sales_type);
					} else {
						sales_type = 0;
					}

					window.open(
						'excel_downloader.php?downloadName=creditMonitoring&search='+search+'&paid_type='+paid_type+'&dt_from='+dt_from+'&dt_to='+dt_to+'&branch_id='+branch_id+'&terminal_id='+terminal_id+'&sales_type='+sales_type,
						'_blank' //
					);
				});
			});


			$('body').on('click','#btnAddCredit',function(){
				$('#myModalCredit').modal('show');
				$('#member_id').select('val',null);
				 $('#credit_branch_id').val('');
				 $('#transaction_amount').val('');
				$('#transaction_date').val('');
				 $('#transaction_dr').val('');
				 $('#transaction_invoice').val('');
				 $('#transaction_pr').val('');
				  $('#is_service').val('0');
				 $('#cr_number').val('');
			});

			$('body').on('click','#btnSubmitCredit',function(){
				var member_id = $('#member_id').val();
				var branch_id = $('#credit_branch_id').val();
				var amount = $('#transaction_amount').val();
				var dt = $('#transaction_date').val();
				var dr = $('#transaction_dr').val();
				var invoice = $('#transaction_invoice').val();
				var pr = $('#transaction_pr').val();
				var is_service = $('#is_service').val();
				var terminal_id = localStorage['terminal_id'];
				var cr_number = $('#cr_number').val();
				if(member_id && amount && dt){
						$.ajax({
						    url:'../ajax/ajax_sales_query.php',
						    type:'POST',
						    data: {functionName:'saveCredit',cr_number:cr_number,is_service:is_service,dr:dr,invoice:invoice,pr:pr,terminal_id:terminal_id,member_id:member_id,amount:amount,dt:dt,branch_id:branch_id},
						    success: function(data){
						        alertify.alert(data,function(){
							        location.reload();
						        });
						    },
						    error:function(){

						    }
						})
				} else {
					tempToast('error','Please complete the form.','Error');
				}
			});

			$('body').on('click','.paging',function(){
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});


			var timer_ajax;
			$("#searchSales").keyup(function(){

				clearTimeout(timer_ajax);
				timer_ajax = setTimeout(function() {
					getPage(0);
				}, 1000);
			});

			$("#paid_type").change(function(){
				getPage(0);
			});
			$('body').on('click','.btnAddName',function(e){
				e.preventDefault();
				var id = $(this).attr('data-id');
				$('#hid_member_credit_id').val(id);
				$('#txtMemberName').val('');
				$('#myModalName').modal('show');
			});
			$('body').on('click','.btnTerms',function(e){
				e.preventDefault();
				var id = $(this).attr('data-id');
				$('#myModalTerms').modal('show');
				$('#tbody').html('Loading...');
				$.ajax({
				    url:'../ajax/ajax_query2.php',
				    type:'POST',
				    data: {functionName:'creditDetails',id:id},
				    success: function(data){
					    $('#tbody').html(data);
				    },
				    error:function(){

				    }
				});
			});
			//btnTerms
			$('body').on('click','#btnSaveName',function(){
				var id = $('#hid_member_credit_id').val();
				var name = $('#txtMemberName').val();
				if(name && name.trim() != ''){
					$.ajax({
					    url:'../ajax/ajax_query2.php',
					    type:'POST',
					    data: {functionName:'saveCreditName', id:id,name:name},
					    success: function(data){
						    $('#myModalName').modal('hide');
					        alertify.alert(data);
						    var page = $('#hiddenpage').val();
						    getPage(page);
					    },
					    error:function(){

					    }
					})
				} else {
					alertify.alert('Invalid name.');
				}
			});

			function getPage(p){
				var search = $('#searchSales').val();
				var paid_type =  $('#paid_type').val();
				var dt_from = $('#dt_from').val();
				var dt_to = $('#dt_to').val();
				var branch_id = $('#branch_id').val();
				var terminal_id = $('#terminal_id').val();
				var sales_type = $('#sales_type').val();

				$('.loading').show();

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html('Loading...');
					},
					data:{page:p,functionName:'memberCreditList',sales_type:sales_type,branch_id:branch_id,terminal_id:terminal_id,dt_from:dt_from,dt_to:dt_to,cid: <?php echo $user->data()->company_id; ?>,search:search,paid_type:paid_type},
					success: function(data){

						$('#holder').html(data);
						$('.loading').hide();
					},
					error: function(){
						alert('Something went wrong. The page will be refresh.');
						location.href='member_credits.php';
						$('.loading').hide();
					}
				});
			}

		/*	$('body').on('click','.btnPayment',function(){
				var total = $(this).attr('data-amt');
				var paid = $(this).attr('data-paid');
				var pending = $(this).attr('data-pending');
				var memcredit = $(this).attr('data-id');
				$('#creditId').val(memcredit);
				$('#totalAmount').html(number_format(total,2));
				$('#paidAmount').html(number_format(paid,2));
				$('#pendingAmount').html(number_format(pending,2));
				$('#pendingAmountHid').val(pending);
				$('#myModal').modal('show');

			}); */
			$('body').on('click','#btnSubmitPayment',function(){
				var id = $('#creditId').val();
				var unpaid = $('#pendingAmountHid').val();
				var amt = $('#txtAmount').val();
				var remarks = $('#paymentRemarks').val();
				var btn = $(this);
				var btnoldval = btn.html();
				btn.attr('disabled',true);
				btn.html('Loading..');
				if(!amt || parseFloat(amt) > parseFloat(unpaid) || isNaN(amt)){
					alertify.alert('Invalid Amount');
					$('#txtAmount').val('');
					btn.attr('disabled',false);
					btn.html(btnoldval);
					return;
				}

				$.ajax({
				    url:'../ajax/ajax_query2.php',
				    type:'POST',
				    data: {functionName:'memberCreditSave',id:id,amt:amt,unpaid:unpaid,remarks:remarks},
				    success: function(data){
				        alertify.alert(data,function(){
					        location.href='member_credits.php';
				        });

				    },
				    error:function(){

					    btn.attr('disabled',false);
					    btn.html(btnoldval);
				    }
				});
			});
			$('#transaction_date').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#transaction_date').datepicker('hide');
			});
			$('#dt_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_from').datepicker('hide');
				getPage(0);
			});
			$('#dt_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_to').datepicker('hide');
				getPage(0);
			});
			$("#branch_id").select2({
				placeholder: 'Choose Branch',
				allowClear: true
			});
			$("#sales_type").select2({
				placeholder: 'Choose Sales Type',
				allowClear: true
			});

			$("#branch_id").change(function(){
				var bid = $(this).val();
				getPage(0);
				$.ajax({
					url:'../ajax/ajax_query.php',
					type:'post',
					data:{functionName:'getTerminals',branch_id:bid},
					success: function(data){
						$('#terminalholder').html(data);
					},
					error: function(){

					}
				});
			});
			$('body').on('change','#terminal_id,#sales_type',function(){
				getPage(0);
			});


			//////***************** payment logic *************************///////////////
			function getMembersInd(company_id,member_id){
				$("#con_member").empty();
				$("#con_member").append("<option></option>");
				$("#con_member_freebies").empty();
				$("#con_member_freebies").append("<option></option>");
				$("#member_credit").empty();
				$("#member_credit").append("<option></option>");
				$("#member_deduction").empty();
				$("#member_deduction").append("<option></option>");
				if(member_id){
					$.ajax({
						url: "../ajax/ajax_get_members.php",
						type:"POST",
						data:{company_id:company_id,member_id:member_id,type:1},
						success: function(data){
							if(data != 0)
							{
								var mems = JSON.parse(data);
								for(var i in mems){
									var amt =0;
									var amt_freebies = 0;
									if(mems[i].amt){
										var check_not_validyet =0;
										amt = mems[i].amt;
										if(mems[i].camt) check_not_validyet = mems[i].camt;
										amt = amt - check_not_validyet;
										$("#con_member").append("<option data-con='"+amt+"' value='"+mems[i].id+"'>"+mems[i].lastname+", " +mems[i].firstname + " " + mems[i].middlename +" ("+amt+")</option>");
									}
									if(mems[i].freebiesamount){
										amt_freebies = mems[i].freebiesamount;
									}
									$("#con_member_freebies").append("<option data-con_freebies='"+amt_freebies+"' value='"+mems[i].id+"'>"+mems[i].lastname+", " +mems[i].firstname + " " + mems[i].middlename +" ("+amt_freebies+")</option>");
									$("#member_credit").append("<option value='"+mems[i].id+"'>"+mems[i].lastname+", " +mems[i].firstname + " " + mems[i].middlename +"</option>");
									$("#member_deduction").append("<option value='"+mems[i].id+"'>"+mems[i].lastname+", " +mems[i].firstname + " " + mems[i].middlename +"</option>");
								}
								$("#con_member_freebies").select2('val',member_id);
								$("#member_credit").select2('val',member_id);
								$("#con_member").select2('val',member_id);
								$("#member_deduction").select2('val',member_id);
								$("#con_member_freebies").attr('disabled',true);
								$("#member_credit").attr('disabled',true);
								$("#member_deduction").attr('disabled',true);
								$("#con_member").attr('disabled',true);
							}
						}
					});
					$.ajax({
						url: '../ajax/ajax_wh_order.php',
						type: 'POST',
						data: {functionName: 'getOverPayment', member_id: member_id},
						success: function(data) {
							$('#over_payment_holder').html(data);
							var over_payment_list = JSON.parse($('#op_member_list').val());
							if(over_payment_list.length > 0) {
								$('#use_user_overpayment').show();
							} else {
								$('#use_user_overpayment').hide();
							}
						},
						error: function() {

						}
					});
				}

			}
			$('body').on('click', '#use_user_overpayment', function() {
				var over_payment_list = JSON.parse($('#op_member_list').val());
				if(over_payment_list.length > 0) {
					$('#use_user_overpayment').show();
				}
				var ret_html = "";
				for(var op in over_payment_list) {
					console.log(over_payment_list[op]);
					var remarks = (over_payment_list[op].remarks) ? over_payment_list[op].remarks : 'None';
					if(over_payment_list[op].status == 1) { // cash
						var total_cash = over_payment_list[op].json_data;
						var total_used_cash = over_payment_list[op].used_total;
						ret_html += "<div class='panel panel-default'>";
						ret_html += "<div class='panel-body'>";
						ret_html += "<p>Remarks: " + remarks + "</p>";
						ret_html += "<p>Type: Cash</p>";
						ret_html += "<p>Used: "+(total_used_cash)+"</p>";
						ret_html += "<p>Total: " + (total_cash -total_used_cash) + "</p>";
						ret_html += "<p><input type='text' class='form-control' value='"+ (total_cash -total_used_cash) +"'><input data-status='1' data-id='" + over_payment_list[op].id + "' value='" + over_payment_list[op].id + "' data-total='" + over_payment_list[op].json_data + "' type='checkbox' class='chk_overpayment' > Use Payment</p>";
						ret_html += "</div>";
						ret_html += "</div>";
					} else if(over_payment_list[op].status == 2) { // credit
						ret_html += "<div class='panel panel-default'>";
						ret_html += "<div class='panel-body'>";
						ret_html += "<p>Remarks: " + remarks + "</p>";
						ret_html += "<p>Type: Credit Card</p>";
						var credit_data = JSON.parse(over_payment_list[op].json_data);
						var total_used_credit = over_payment_list[op].used_total;
						var total_credit = 0;
						for(var cd in credit_data) {
							ret_html += "<p>Card: " + credit_data[cd].card_type + "</p>";
							ret_html += "<p>Trance Number: " + credit_data[cd].trace_number + "</p>";
							ret_html += "<p>Date: " + credit_data[cd].date + "</p>";
							ret_html += "<p>Amount: " + credit_data[cd].amount + "</p>";
							ret_html += "<hr>";
							total_credit += parseFloat(total_credit) + parseFloat(credit_data[cd].amount);
						}
						ret_html += "<p>Used: " + total_used_credit + "</p>";
						ret_html += "<p>Total: " + (total_credit - total_used_credit) + "</p>";
						ret_html += "<p><input class='form-control' type='text' value='"+ (total_credit -total_used_credit) +"'> <input data-json='" + JSON.stringify(over_payment_list[op]) + "' value='" + over_payment_list[op].id + "' data-status='2' data-id='" + over_payment_list[op].id + "' data-total='" + total_credit + "' type='checkbox' class='chk_overpayment' > Use Payment</p>";
						ret_html += "</div>";
						ret_html += "</div>";
					} else if(over_payment_list[op].status == 3) { // cheque
						ret_html += "<div class='panel panel-default'>";
						ret_html += "<div class='panel-body'>";
						ret_html += "<p>Remarks: " + remarks + "</p>";
						ret_html += "<p>Type: Check</p>";
						var cheque_data = JSON.parse(over_payment_list[op].json_data);
						var total_used_checked = over_payment_list[op].used_total;
						var total_cheque = 0;

						for(var cd in cheque_data) {
							ret_html += "<p>Ctrl#: " + cheque_data[cd].cheque_number + "</p>";
							ret_html += "<p>Date: " + cheque_data[cd].date + "</p>";
							ret_html += "<p>Amount: " + cheque_data[cd].amount + "</p>";
							ret_html += "<hr>";
							total_cheque = parseFloat(total_cheque) + parseFloat(cheque_data[cd].amount);
						}
						ret_html += "<p>Used: " + total_used_checked + "</p>";
						ret_html += "<p>Total: " + total_cheque + "</p>";
						ret_html += "<p><input class='form-control' type='text' value='"+ (total_cheque - total_used_checked) +"'> <input data-json='" + JSON.stringify(over_payment_list[op]) + "' value='" + over_payment_list[op].id + "'  data-status='3' data-id='" + over_payment_list[op].id + "' data-total='" + total_cheque + "' type='checkbox' class='chk_overpayment' > Use Payment</p>";
						ret_html += "</div>";
						ret_html += "</div>";
					} else if(over_payment_list[op].status == 4) { // bt
						ret_html += "<div class='panel panel-default' >";
						ret_html += "<div class='panel-body'>";
						ret_html += "<p>Remarks: " + remarks + "</p>";
						ret_html += "<p>Type: Bank Transfer</p>";
						var bt_data = JSON.parse(over_payment_list[op].json_data);
						var total_bt_used = over_payment_list[op].used_total;
						var total_bt = 0;
						for(var cd in bt_data) {
							ret_html += "<p>Date: " + bt_data[cd].date + "</p>";
							ret_html += "<p>Amount: " + bt_data[cd].amount + "</p>";

							total_bt = parseFloat(total_bt) + parseFloat( bt_data[cd].amount);
						}

						ret_html += "<p>Used: "+total_bt_used+"</p>";
						ret_html += "<p>Total: "+(total_bt -total_bt_used) +"</p>";

						ret_html += "<p> <input class='form-control' type='text' value='"+ (total_bt -total_bt_used) +"'> <input data-json='" + JSON.stringify(over_payment_list[op]) + "'  data-status='4' data-id='" + over_payment_list[op].id + "' value='" + over_payment_list[op].id + "' data-total='" + total_bt + "' type='checkbox' class='chk_overpayment' > Use Payment</p>";
						ret_html += "</div>";
						ret_html += "</div>";
					}
				}

				$('#right-pane-container').html(ret_html);
				$('.right-panel-pane').fadeIn(100);
			});
			$('body').on('click', '.chk_overpayment', function() {
				var con = $(this);
				var status = con.attr('data-status');
				var total = con.attr('data-total');

				var v = con.is(':checked');
				if(status == 1) { // cash
					var txtcon = $('#cashreceivetext');
					var cur_cash = txtcon.val();
					cur_cash = (cur_cash) ? cur_cash : 0;
					var total_use = con.prev().val();
					var cash_id = con.attr('data-id');
					if(v) {
						txtcon.val(parseFloat(cur_cash) + parseFloat(total_use));
						$("#hidcashpayment").val(parseFloat(cur_cash) + parseFloat(total_use));
					} else {
						txtcon.val(parseFloat(cur_cash) - parseFloat(total_use));
						$("#hidcashpayment").val(parseFloat(cur_cash) - parseFloat(total_use));
					}
					updateCashPayment();

				} else if(status == 2) { // credit
					var json = JSON.parse(con.attr('data-json'));
					var credit_data = JSON.parse(json.json_data);
					var billing_card_type = $('#billing_card_type');
					var billing_trace_number = $('#billing_trace_number');
					var billing_approval_code = $('#billing_approval_code');
					var billing_date = $('#billing_date');
					var total_use = con.prev().val();
					var tax = parseFloat(total_use) * 0.035;
					var tax = number_format(tax,2,'.','');
					var rem = total_use - tax;
					rem = number_format(rem,2,'.','');
					$('#member_deduction_amount').val(tax);

					for(var i in credit_data) {
						if(v) {

							var others = "<p>Card Type: "+credit_data[i].card_type+"</p>";
							others += "<p>Trace Number: "+credit_data[i].trace_number+"</p>";
							others += "<p>Approval Code: "+credit_data[i].approval_code+"</p>";
							others += "<p>Date: "+credit_data[i].date+"</p>";

							$("#credit_table").append("<tr id='from_user_credit_credit" +chk_id + "' data-date='"+credit_data[i].date+"' data-card_type='"+credit_data[i].card_type+"' data-trace_number='"+credit_data[i].trace_number+"' data-approval_code='"+credit_data[i].trace_number+"'><td>"+credit_data[i].credit_number +"</td><td>"+rem+"</td><td>"+credit_data[i].bank_name+"</td><td>"+others+"</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");


						} else {
							$('#from_user_credit_credit' + chk_id).remove();
						}
					}
					updateCreditPayment();
				} else if(status == 3) { // check
					var json = JSON.parse(con.attr('data-json'));
					var check_data = JSON.parse(json.json_data);
					var ch_firstname = $('#ch_firstname');
					var ch_middlename = $('#ch_middlename');
					var ch_lastname = $('#ch_lastname');
					var ch_phone = $('#ch_phone');
					var total_use = con.prev().val();
					var chk_id = con.attr('data-id');
					for(var i in check_data) {
						if(v) {
							$('#ch_table').append("<tr id='from_user_credit_check" + chk_id + "' ><td>" + check_data[i].date + "</td><td>" + check_data[i].cheque_number + "</td><td>" + total_use + "</td><td>" + check_data[i].bank_name + "</td><td></td></tr>");
							ch_firstname.val(check_data[i].firstname);
							ch_middlename.val(check_data[i].middlename);
							ch_lastname.val(check_data[i].lastname);
							ch_phone.val(check_data[i].phone);
						} else {
							$('#from_user_credit_check' +chk_id).remove();
						}
					}


					updateChequePayment();

				} else if(status == 4) { // check
					var json = JSON.parse(con.attr('data-json'));
					var bt_id = con.attr('data-id');
					var check_data = JSON.parse(json.json_data);
					var ch_firstname = $('#bt_firstname');
					var ch_middlename = $('#bt_middlename');
					var ch_lastname = $('#bt_lastname');
					var ch_date= $('#bt_date');
					var total_use = con.prev().val();

					for(var i in check_data) {
						if(v) {

							ch_firstname.val(check_data[i].firstname);
							ch_middlename.val(check_data[i].middlename);
							ch_lastname.val(check_data[i].lastname);
							ch_date.val(check_data[i].date);

							$('#bt_table').append("<tr id='from_user_credit_check" + bt_id + "' ><td>" + check_data[i].credit_number + "</td><td>" + total_use + "</td><td>" + check_data[i].amount + "</td><td>" + check_data[i].bank_name + "</td><td></td></tr>");


						} else {
							$('#from_user_credit_check' + bt_id).remove();
						}
					}
					updateBankTransferPayment();
				}

			});
			//getMembers(localStorage['company_id']);
			//getMemberOptList();
			function getMembers(company_id){
				$.ajax({
					url: "ajax/ajax_get_members.php",
					type:"POST",
					data:{company_id:company_id,type:1},
					success: function(data){
						if(data != 0)
						{
							localStorage['members']=data;
						} else {
							localStorage.removeItem('members');
						}
					}
				});
			}
			function getMemberOptList(){
				if(localStorage['members']){
					var mems = JSON.parse(localStorage['members']);
					$("#con_member").empty();
					$("#con_member").append("<option></option>");
					$("#con_member_freebies").empty();
					$("#con_member_freebies").append("<option></option>");
					$("#member_credit").empty();
					$("#member_credit").append("<option></option>");
					for(var i in mems){
						var amt =0;
						var amt_freebies = 0;

						if(mems[i].amt){
							var check_not_validyet =0;
							amt = mems[i].amt;
							if(mems[i].camt) check_not_validyet = mems[i].camt;
							amt = amt - check_not_validyet;
							$("#con_member").append("<option data-con='"+amt+"' value='"+mems[i].id+"'>"+mems[i].lastname+", " +mems[i].firstname + " " + mems[i].middlename +" ("+amt+")</option>");
						}
						if(mems[i].freebiesamount){
							amt_freebies = mems[i].freebiesamount;
						}
						$("#con_member_freebies").append("<option data-con_freebies='"+amt_freebies+"' value='"+mems[i].id+"'>"+mems[i].lastname+", " +mems[i].firstname + " " + mems[i].middlename +" ("+amt_freebies+")</option>");
						$("#member_credit").append("<option value='"+mems[i].id+"'>"+mems[i].lastname+", " +mems[i].firstname + " " + mems[i].middlename +"</option>");

					}
				}
			}
			$("#member_deduction").select2({
				placeholder: 'Choose '+MEMBER_LABEL+' Name...',
				allowClear: true
			});
			$("#con_member").select2({
				placeholder: 'Choose '+MEMBER_LABEL+' Name...',
				allowClear: true
			}).on('select2-open',function(){

			}).on("select2-close", function(e) {
				// fired to the original element when the dropdown closes
				setTimeout(function() {
					$('.select2-container-active').removeClass('select2-container-active');
					$(':focus').blur();
				}, 100);
			});

			$("#con_member_freebies").select2({
				placeholder: 'Choose '+MEMBER_LABEL+' Name',
				allowClear: true
			}).on('select2-open',function(){

			}).on("select2-close", function(e) {
				// fired to the original element when the dropdown closes
				setTimeout(function() {
					$('.select2-container-active').removeClass('select2-container-active');
					$(':focus').blur();
				}, 100);
			});



			$("#member_credit").select2({
				placeholder: 'Choose '+MEMBER_LABEL+' Name',
				allowClear: true
			});

			$('.cashreceivecancel').click(function(){
				$('#getpricemodal').modal("hide");
				$("#credit_table").find("tr").remove();
				$("#bt_table").find("tr").remove();
				$("#ch_table").find("tr").remove();
				$("#tab_d :input[type='text']").val('');
				$("#tab_c :input[type='text']").val('');
				$("#tab_b :input[type='text']").val('');
				$("#tab_a :input[type='text']").val('');
				localStorage.removeItem('payment_cheque');
				localStorage.removeItem('payment_credit');
				localStorage.removeItem('payment_bt');
				localStorage.removeItem('payment_cash');
				localStorage.removeItem('payment_con');
				localStorage.removeItem('payment_con_freebies');
				localStorage.removeItem('payment_member_credit');
				localStorage.removeItem('payment_member_deduction');
			});

			$('body').on('click','.cashreceiveok',function(){
				var con = $(this);

				receiveCash(con);
			});

			$('#ch_date').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#ch_date').datepicker('hide');
			});

			$('#billing_date').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#billing_date').datepicker('hide');
			});
			$('#bt_date').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#bt_date').datepicker('hide');
			});
			function receiveCash(btn){
				var cash = $("#hidcashpayment").val();
				if(!cash) cash = 0;
				var credit = $("#hidcreditpayment").val();
				if(!credit) credit = 0;
				var banktransfer = $("#hidbanktransferpayment").val();
				if(!banktransfer) banktransfer = 0;
				var cheque = $("#hidchequepayment").val();
				if(!cheque) cheque = 0;
				var con_amount = $("#hidconsumablepayment").val();
				if(!con_amount) con_amount = 0;
				var con_amount_freebies = $("#hidconsumablepaymentfreebies").val();
				if(!con_amount_freebies) con_amount_freebies = 0;
				var member_credit_amount = $("#hidmembercredit").val();
				if(!member_credit_amount) member_credit_amount = 0;
				var member_deduction_amount = $("#hidmemberdeduction").val();
				if(!member_deduction_amount) member_deduction_amount = 0;
				var totalpayment = parseFloat(cash) + parseFloat(credit) + parseFloat(banktransfer) + parseFloat(cheque) + parseFloat(con_amount) + parseFloat(con_amount_freebies) + parseFloat(member_credit_amount)+ parseFloat(member_deduction_amount);
				var grandtotal = parseFloat($("#hidamountdue").val());

				var paymentRemarks = $('#paymentRemarks').val();

				if(!totalpayment || parseFloat(totalpayment) < 0 || parseFloat(totalpayment) > parseFloat(grandtotal)) {
					cashHolderComputation(0,0);
					showToast('error','<p>Invalid payment</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;
				} else {

					if(!isValidFormCheque() || !isValidFormCredit() || !isValidFormBankTransfer() || !isValidFormDeduction() ){
						return;
					}

					localStorage['payment_cash'] = cash;
					localStorage['payment_con'] = con_amount;
					localStorage['payment_con_freebies'] = con_amount_freebies;
					localStorage['payment_member_credit'] = member_credit_amount;
					button_action.start_loading(btn);
					alertify.confirm('Are you sure you want to submit this payment?',function(e){

						if(e){
							// ajax request payment
							var payment_credit;
							var payment_bt;
							var payment_cheque;
							var payment_cash;
							var payment_con_freebies;
							var payment_con;
							var payment_member_credit;
							var member_credit_id = $('#payment_member_credit_id').val();
							var payment_member_deduction;
							if(localStorage['payment_member_deduction']){
								payment_member_deduction = localStorage['payment_member_deduction'];
							}
							if(localStorage['payment_cash']){
								payment_cash = localStorage['payment_cash'];
							}
							if(localStorage['payment_con']){
								payment_con = localStorage['payment_con'];
							}
							if(localStorage['payment_con_freebies']){
								payment_con_freebies = localStorage['payment_con_freebies'];
							}
							if(localStorage['payment_member_credit']){
								payment_member_credit = localStorage['payment_member_credit'];
							}
							if(localStorage['payment_credit']){
								payment_credit = localStorage['payment_credit'];
							}
							if(localStorage['payment_bt']){
								payment_bt = localStorage['payment_bt'];
							}
							if(localStorage['payment_cheque']){
								payment_cheque = localStorage['payment_cheque'];
							}

							var arr_op_ids = [];
							$('input:checkbox.chk_overpayment').each(function() {
								var op_chk = $(this);
								if(op_chk.is(":checked")) {
									var amount_used = op_chk.prev().val();
									arr_op_ids.push({id: op_chk.val(),amount:amount_used});
								}

							});

							var override_payment_date = $('#override_payment_date').val();

							$.ajax({
								url:'../ajax/ajax_query2.php',
								type:'POST',
								data: {
									functionName:'sendPaymentMemberCredit',
									payment_member_deduction:payment_member_deduction,
									payment_credit:payment_credit,
									payment_bt:payment_bt,
									payment_cheque:payment_cheque,
									payment_cash:payment_cash,
									payment_con:payment_con,
									payment_con_freebies:payment_con_freebies,
									payment_member_credit:payment_member_credit,
									member_credit_id:member_credit_id,
									paymentRemarks:paymentRemarks,
									totalpayment:totalpayment,
									override_payment_date:override_payment_date,
									arr_op_ids: JSON.stringify(arr_op_ids),
									terminal_id:localStorage['terminal_id']
								},
								success: function(data){

									alertify.alert(data,function(){
										location.href='member_credits.php';
									});
									button_action.end_loading(btn);



								},
								error:function(){
									tempToast('error',"<p>Error Occur. Please try again.</p>","<h4>Error!</h4>");
									button_action.end_loading(btn);
								}
							});
							$("#credit_table").find("tr").remove();
							$("#bt_table").find("tr").remove();
							$("#ch_table").find("tr").remove();
							$("#tab_d :input[type='text']").val('');
							$("#tab_c :input[type='text']").val('');
							$("#tab_b :input[type='text']").val('');
							$("#tab_a :input[type='text']").val('');
							$('#getpricemodal').modal("hide");
						} else {
							button_action.end_loading(btn);
						}
					});
				}
			}

			function cashHolderComputation(cash,change){
				$('#cashreceiveholder').empty();
				$('#changeholder').empty();
				$('#cashreceiveholder').append(number_format(cash,2));
				$('#changeholder').append(number_format(change,2));
			}

			function updateTotalPayment(){
				var cash = $("#cashreceivetext").val();
				if(!cash){
					cash=0;
				}
				var con_amount = $("#con_amount").val();
				if(!con_amount){
					con_amount=0;
				}
				var con_amount_freebies = $("#con_amount_freebies").val();
				if(!con_amount_freebies){
					con_amount_freebies=0;
				}
				var member_credit_amount = $("#member_credit_amount").val();
				if(!member_credit_amount){
					member_credit_amount=0;
				}
				var member_deduction_amount = $("#hidmemberdeduction").val();
				if(!member_deduction_amount) {
					member_deduction_amount = 0;
				}
				var credit_amount = $("#hidcreditpayment").val();
				if(!credit_amount){
					credit_amount=0;
				}
				var bt_amount = $("#hidbanktransferpayment").val();
				if(!bt_amount){
					bt_amount=0;
				}
				var ck_amount = $("#hidchequepayment").val();
				if(!ck_amount){
					ck_amount=0;
				}
				var gtotal = parseFloat(cash) + parseFloat(con_amount) + parseFloat(con_amount_freebies) + parseFloat(member_credit_amount) + parseFloat(credit_amount) + parseFloat(bt_amount) + parseFloat(ck_amount)+ parseFloat(member_deduction_amount);
				$("#totalOfAllPayment").html("<strong><span style='font-size:1.2em;' class='text-info' >Total Payment: " +gtotal.toFixed(2) + "</span></strong>");

			}
			function updateCashPayment(){
				var cash = $("#cashreceivetext").val();
				if(!cash){
					cash=0;
				}
				$("#totalcashpayment").html(cash);
				updateTotalPayment();
			}
			function updateConPayment(){
				var con_amount = $("#con_amount").val();
				if(!con_amount){
					con_amount=0;
				}
				$("#totalconsumablepayment").html(con_amount);
				updateTotalPayment();
			}
			function updateMemberCredit(){
				var member_credit_amount = $("#member_credit_amount").val();
				if(!member_credit_amount){
					member_credit_amount=0;
				}
				$("#totalmembercredit").html(member_credit_amount);
				updateTotalPayment();
			}
			function updateMemberDeduction(){
				var member_deduction_amount=0;
				$('#member_deduction_table > tbody > tr').each(function(i){
					var row = $(this);
					var amount = row.attr('data-amount');
					amount = parseFloat(amount);
					member_deduction_amount = parseFloat(member_deduction_amount) + parseFloat(amount);

				});
				$("#totalmemberdeduction").html(member_deduction_amount);
				$("#hidmemberdeduction").val(member_deduction_amount);
				updateTotalPayment();
			}
			function updateConPaymentFreebies(){
				var con_amount_freebies = $("#con_amount_freebies").val();
				if(!con_amount_freebies){
					con_amount_freebies=0;
				}

				$("#totalconsumablepaymentfreebies").html(con_amount_freebies);
				updateTotalPayment();
			}
			function updateCreditPayment(){
				var total = 0;
				if($("#credit_table tr").children().length ){
					$("#credit_table tr").each(function(index){
						var row = $(this);
						var amount = row.children().eq(1).text();
						total = parseFloat(total) + parseFloat(amount);
					});
				}
				$("#totalcreditpayment").html(total);
				$("#hidcreditpayment").val(total);
				updateTotalPayment();
			}
			function updateBankTransferPayment(){
				var total = 0;
				if($("#bt_table tr").children().length ){
					$("#bt_table tr").each(function(index){
						var row = $(this);
						var amount = row.children().eq(1).text();
						total = parseFloat(total) + parseFloat(amount);
					});
				}
				$("#totalbanktransferpayment").html(total);
				$("#hidbanktransferpayment").val(total);
				updateTotalPayment();
			}
			function updateChequePayment(){
				var total = 0;
				if($("#ch_table tr").children().length ){
					$("#ch_table tr").each(function(index){
						var row = $(this);
						var amount = row.children().eq(2).text();
						total = parseFloat(total) + parseFloat(amount);
					});
				}
				$("#totalchequepayment").html(total);
				$("#hidchequepayment").val(total);
				updateTotalPayment();
			}

			function hasItemCreditValidation(elem){
				if(!$("#credit_table tr").children().length ){
					showToast('error','<p>Please Add Credit Card First. </p>','<h3>WARNING!</h3>','toast-bottom-right');
					elem.val('');
				}
			}
			$("#billing_firstname, #billing_middlename, #billing_lastname, #billing_company, #billing_address, #billing_postal,#billing_phone,#billing_email,#billing_remarks").keyup(function(){
				hasItemCreditValidation($(this));
			});
			function hasItemBTValidation(elem){
				if(!$("#bt_table tr").children().length ){
					showToast('error','<p>Please Add Bank Transfer Data First. </p>','<h3>WARNING!</h3>','toast-bottom-right');
					elem.val('');
				}
			}
			$("#bt_bankto_name, #bt_bankto_account_number, #bt_firstname, #bt_middlename, #bt_lastname, #bt_company,#bt_address,#bt_postal,#bt_phone").keyup(function(){
				hasItemBTValidation($(this));
			});
			function hasItemChequeValidation(elem){
				if(!$("#ch_table tr").children().length ){
					showToast('error','<p>Please Add Cheque Data First. </p>','<h3>WARNING!</h3>','toast-bottom-right');
					elem.val('');
				}
			}

			$("#ch_firstname, #ch_middlename, #ch_lastname, #ch_phone").keyup(function(){
				hasItemChequeValidation($(this));
			});

			function isValidAmount(a,addme){
				var cash = $("#hidcashpayment").val();
				if(!cash) cash = 0;
				var credit = $("#hidcreditpayment").val();
				if(!credit) credit = 0;
				var banktransfer = $("#hidbanktransferpayment").val();
				if(!banktransfer) banktransfer = 0;
				var cheque = $("#hidchequepayment").val();
				if(!cheque) cheque = 0;
				var con_amount = $("#hidconsumablepayment").val();
				if(!con_amount) con_amount = 0;
				var con_amount_freebies = $("#hidconsumablepaymentfreebies").val();
				if(!con_amount_freebies) con_amount_freebies = 0;
				var member_credit_amount = $("#hidmembercredit").val();
				if(!member_credit_amount) member_credit_amount = 0;
				var member_deduction_amount = $("#hidmemberdeduction").val();
				if(!member_deduction_amount) member_deduction_amount = 0;
				var grandtotal = parseFloat($("#hidamountdue").val());

				var currentNotCash =   parseFloat(credit) + parseFloat(banktransfer) + parseFloat(cheque) + parseFloat(con_amount) + parseFloat(con_amount_freebies)  + parseFloat(member_credit_amount)+ parseFloat(member_deduction_amount);
				if(addme){
					currentNotCash = parseFloat(currentNotCash) + parseFloat(a);
				}
				if(parseFloat(currentNotCash).toFixed(2) > parseFloat(grandtotal)){
					return true;
				} else {
					return false;
				}
			}

			function isValidFormDeduction(){
				if($("#member_deduction_table > tbody > tr").children().length ){
					var deductionArray = new Array();
					var member_id = $('#member_deduction').val();

					$("#member_deduction_table > tbody > tr").each(function(index){
						var row = $(this);
						var amount = row.attr('data-amount');
						var remarks = row.children().eq(2).text();
						var addtl_remarks = row.children().eq(3).text();
						deductionArray[index] = {
							member_id : member_id,
							amount : amount,
							remarks : remarks,
							addtl_remarks : addtl_remarks,
						}
					});
					localStorage['payment_member_deduction'] = JSON.stringify(deductionArray);
					return true;
				}

				return true;
			}

			function isValidFormCheque(){
				if($("#ch_table tr").children().length ){
					var chequeArray = new Array();
					var fn = $("#ch_firstname").val();
					var mn = $("#ch_middlename").val();
					var ln = $("#ch_lastname").val();
					var phone = $("#ch_phone").val();

					if(fn && !isAlphaNumeric(fn)){
						showToast('error','<p>First name should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
						return false;
					}
					if(mn && !isAlphaNumeric(mn)){
						showToast('error','<p>Middle name should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
						return false;
					}
					if(ln && !isAlphaNumeric(ln)){
						showToast('error','<p>Last name should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
						return false;
					}
					if(phone && !isAlphaNumeric(phone)){
						showToast('error','<p>Phone should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
						return false;
					}
					$("#ch_table tr").each(function(index){
						var row = $(this);
						chequeArray[index] = {
							date : row.children().eq(0).text(),
							cheque_number : row.children().eq(1).text(),
							amount:  row.children().eq(2).text(),
							bank_name:  row.children().eq(3).text(),
							firstname : fn,
							lastname: ln,
							middlename : mn,
							phone: phone
						}
					});
					localStorage['payment_cheque'] = JSON.stringify(chequeArray);
					return true;
				}

				return true;
			}

			function isValidFormCredit(){
				if($("#credit_table tr").children().length ){
					var creditArray = new Array();
					var  fn = $("#billing_firstname").val();
					var  mn = $("#billing_middlename").val();
					var  ln = $("#billing_lastname").val();
					var  comp = $("#billing_company").val();
					var  add = $("#billing_address").val();
					var  postal = $("#billing_postal").val();
					var  phone = $("#billing_phone").val();
					var  email = $("#billing_email").val();
					var  rem = $("#billing_remarks").val();
					// required
					var card_type = $("#billing_card_type").val();
					var trace_number = $("#billing_trace_number").val();
					var approval_code = $("#billing_approval_code").val();
					var date = $("#billing_date").val();

					if(false){
						showToast('error','<p>Please Complete Credit Card billing form. </p>','<h3>WARNING!</h3>','toast-bottom-right');
						return false;
					} else {
						if(ln && !isAlphaNumeric(ln)){
							showToast('error','<p>Last name should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(fn && !isAlphaNumeric(fn)){
							showToast('error','<p>First name should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(mn && !isAlphaNumeric(mn)){
							showToast('error','<p>Middle name should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(comp && !isAlphaNumeric(comp)){
							showToast('error','<p>Company should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(add && !isAlphaNumeric(add)){
							showToast('error','<p>Address should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(postal && !isNumeric(postal)){
							showToast('error','<p>Postal should be numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(phone && !isAlphaNumeric(phone)){
							showToast('error','<p>Phone should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(email && !isEmail(email)){
							showToast('error','<p>Email should be valid email address</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(rem && !isAlphaNumeric(rem)){
							showToast('error','<p>Remarks should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						$("#credit_table tr").each(function(index){
							var row = $(this);
							creditArray[index] = {
								credit_number : row.children().eq(0).text(),
								amount:  row.children().eq(1).text(),
								bank_name:  row.children().eq(2).text(),
								firstname : fn,
								lastname: ln,
								middlename : mn,
								phone: phone,
								comp: comp,
								add: add,
								postal:postal,
								email:email,
								remarks:rem,
								card_type:card_type,
								trace_number:trace_number,
								approval_code:approval_code,
								date:date
							}
						});
						localStorage['payment_credit'] = JSON.stringify(creditArray);
						return true;
					}

				}
				return true;
			}
			function isValidFormBankTransfer(){
				if($("#bt_table tr").children().length ){
					var bankTransferArray = new Array();
					var bt_bankto_name = $("#bt_bankto_name").val();
					var bt_bankto_account_number = $("#bt_bankto_account_number").val();
					var fn = $("#bt_firstname").val();
					var mn = $("#bt_middlename").val();
					var ln = $("#bt_lastname").val();
					var comp = $("#bt_company").val();
					var  add = $("#bt_address").val();
					var  postal = $("#bt_postal").val();
					var  phone = $("#bt_phone").val();
					var  date = $("#bt_date").val();

					if(!date){
						showToast('error','<p>Please Bank Transfer  billing form. </p>','<h3>WARNING!</h3>','toast-bottom-right');
						return false;
					} else {
						if(bt_bankto_name && !isAlphaNumeric(bt_bankto_name)){
							showToast('error','<p>Bank name should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(bt_bankto_account_number && !isAlphaNumeric(bt_bankto_account_number)){
							showToast('error','<p>Bank account number should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(fn && !isAlphaNumeric(fn)){
							showToast('error','<p>First name should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(mn & !isAlphaNumeric(mn)){
							showToast('error','<p>Middle name should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(ln && !isAlphaNumeric(ln)){
							showToast('error','<p>Last name should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(comp && !isAlphaNumeric(comp)){
							showToast('error','<p>Company should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(add && !isAlphaNumeric(add)){
							showToast('error','<p>Address should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(postal && !isNumeric(postal)){
							showToast('error','<p>Phone should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						if(phone && !isAlphaNumeric(phone)){
							showToast('error','<p>Phone should have letters and numbers only</p>','<h3>WARNING!</h3>','toast-bottom-right');
							return false;
						}
						$("#bt_table tr").each(function(index){
							var row = $(this);
							bankTransferArray[index] = {
								credit_number : row.children().eq(0).text(),
								amount:  row.children().eq(1).text(),
								bank_name:  row.children().eq(2).text(),
								bt_bankto_name:bt_bankto_name,
								bt_bankto_account_number:bt_bankto_account_number,
								firstname : fn,
								lastname: ln,
								middlename : mn,
								phone: phone,
								comp: comp,
								add: add,
								postal:postal,
								date:date
							}
						});
						localStorage['payment_bt'] = JSON.stringify(bankTransferArray);
						return true;
					}
				}
				return true;
			}

			function isAlphaNumeric(str){
				var rexp = /^[\w\-\s\.,��]+$/
				if(rexp.test(str)){
					return true;
				} else {
					return false;
				}
			}
			function validateDate(testdate) {
				var date_regex = /^(0[1-9]|1[0-2])\/(0[1-9]|1\d|2\d|3[01])\/(19|20)\d{2}$/
				return date_regex.test(testdate);
			}
			function isNumeric(str){
				var rexp = /^[0-9]+$/
				if(rexp.test(str)){
					return true;
				} else {
					return false;
				}
			}
			function isEmail(str){
				var rexp = /^[\w\.-_\+]+@[\w-]+(\.\w{2,3})+$/
				if(rexp.test(str)){
					return true;
				} else {
					return false;
				}
			}
			function showpricemodal(totalforfreebies,grandtotal){

				if (!totalforfreebies){
					localStorage['totalforfreebies'] = 0;
				} else {
					localStorage['totalforfreebies'] =totalforfreebies;
				}
				localStorage.removeItem('payment_cheque');
				localStorage.removeItem('payment_credit');
				localStorage.removeItem('payment_bt');
				localStorage.removeItem('payment_cash');
				localStorage.removeItem('payment_con');
				localStorage.removeItem('payment_con_freebies');
				localStorage.removeItem('payment_member_credit');
				localStorage.removeItem('payment_member_deduction');
				$("#cashreceiveholder").text(0);
				$("#changeholder").text(0);
				$("#con_amount_freebies").val('');
				$("#con_amount").val('');
				$("#member_credit_amount").val('');

				$('#hidcashpayment').val(0);
				$('#hidcreditpayment').val(0);
				$('#hidbanktransferpayment').val(0);
				$('#hidchequepayment').val(0);
				$('#hidconsumablepayment').val(0);
				$('#hidconsumablepaymentfreebies').val(0);
				$('#hidmembercredit').val(0);
				$('#hidmemberdeduction').val(0);
				$('#hidTotalOfAllPayment').val(0);


				updateCreditPayment();
				updateCashPayment();
				updateBankTransferPayment();
				updateChequePayment();
				updateConPayment();
				updateConPaymentFreebies();
				updateMemberCredit();
				updateMemberDeduction();
				$("#amountdue").html("<span style='font-size:1.2em;' class='text-info'><strong> Amount Due: " + grandtotal + "</strong></span>");
				$("#hidamountdue").val( replaceAll(grandtotal,',',''));
				$("#getpricemodal").modal("show");
				setTimeout(function() { $('#cashreceivetext').focus() }, 500);
			}
			$('#cashreceivetext').keypress(function (e) {
				var key = e.which;
				if(key == 13)  // the enter key code
				{
					receiveCash();
					$('#cashreceivetext').val('');
					$('#getpricemodal').modal("hide");
				}

			});

			$('#cashreceivetext').keyup(function (e) {
				if(isNaN($(this).val())){
					showToast('error','<p>Please Enter Valid Amount</p>','<h3>WARNING!</h3>','toast-bottom-right');
					$(this).val('');
					$(this).focus();
				}
				$("#hidcashpayment").val($(this).val());
				updateCashPayment();
			});
			$('#addcreditcard').click(function(){
				var bl_cardnumber = $('#billing_cardnumber').val();
				var bl_bank = $('#billing_bankname').val();
				var bl_amount = $('#billing_amount').val();
				if(!bl_cardnumber){
				//	showToast('error','<p>Please indicate card number</p>','<h3>WARNING!</h3>','toast-bottom-right');
				//	return;
					bl_cardnumber ='NA';
				}
				if(!bl_amount){
					showToast('error','<p>Please indicate amount</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;
				}
				if(isNaN(bl_amount)){
					showToast('error','<p>Please indicate a valid amount</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;
				}
				if(!bl_bank){
					showToast('error','<p>Please indicate bank name</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;
				}
				if(isValidAmount(bl_amount,true)){
					showToast('error','<p>Your payment exceeds to amount due.</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return ;
				}
				$("#credit_table").append("<tr><td>"+bl_cardnumber+"</td><td>"+bl_amount+"</td><td>"+bl_bank+"</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
				$('#billing_cardnumber').val('');
				$('#billing_bankname').val('');
				$('#billing_amount').val('');
				updateCreditPayment();
			});
			$('#addbanktransfer').click(function(){

				var bt_cardnumber = $('#bankfrom_account_number').val();

				var bt_bank = $('#bankfrom_name').val();

				var bt_amount = $('#bt_amount').val();

				if(!bt_cardnumber){

				   //showToast('error','<p>Please indicate card number</p>','<h3>WARNING!</h3>','toast-bottom-right');
				   //return;
					bt_cardnumber  = 'NA';

				}

				if(!bt_amount){

					showToast('error','<p>Please indicate amount</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;

				}

				if(isNaN(bt_amount)){
					showToast('error','<p>Please indicate a valid amount</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;
				}
				if(parseFloat(bt_amount) <= 0){
					showToast('error','<p>Amount should be greater than Zero</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;
				}
				if(!bt_bank){
					showToast('error','<p>Please indicate bank name</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;
				}
				if(isValidAmount(bt_amount,true)){
					showToast('error','<p>Your payment exceeds to amount due.</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return ;
				}
				$("#bt_table").append("<tr><td>"+bt_cardnumber+"</td><td>"+bt_amount+"</td><td>"+bt_bank+"</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
				$('#bankfrom_account_number').val('');
				$('#bankfrom_name').val('');
				$('#bt_amount').val('');
				updateBankTransferPayment();
			});
			$('#addcheque').click(function(){
				var ch_date = $('#ch_date').val();
				var ch_number = $('#ch_number').val();
				var ch_amount = $('#ch_amount').val();
				var ch_bankname = $('#ch_bankname').val();
				if(!ch_date){
					showToast('error','<p>Please indicate date</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;
				}
				if(!ch_number){
					showToast('error','<p>Please indicate card number</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;
				}
				if(!ch_amount){
					showToast('error','<p>Please indicate amount</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;
				}
				if(!validateDate(ch_date)){
					showToast('error','<p>Invalid Date Format. It should be mm/dd/yyyy (Ex. 01/01/2014) </p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;
				}
				if(isNaN(ch_amount)){
					showToast('error','<p>Please indicate a valid amount</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;
				}
				if(parseFloat(ch_amount) <= 0){
					showToast('error','<p>Amount should be greater than Zero</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;
				}
				if(!ch_bankname){
					showToast('error','<p>Please indicate bank name</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;
				}
				if(isValidAmount(ch_amount,true)){
					showToast('error','<p>Your payment exceeds to amount due.</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return ;
				}
				$("#ch_table").append("<tr><td>"+ch_date+"</td><td>"+ch_number+"</td><td>"+ch_amount+"</td><td>"+ch_bankname+"</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
				$('#ch_date').val('');
				$('#ch_number').val('');
				$('#ch_amount').val('');
				$('#ch_bankname').val('');
				updateChequePayment();
			});
			$('#con_amount_freebies').keyup(function (e) {

				if(!($('#con_member_freebies').val())){
					showToast('error','<p>Please Choose '+MEMBER_LABEL+' First</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;
				}
				var validamt = $('#con_member_freebies option:selected').attr('data-con_freebies');
				//var cartfreebies = parseFloat(localStorage['totalforfreebies']);
				var cartfreebies = 1000000;

				if (parseFloat($(this).val()) > cartfreebies){
					showToast('error','<p>Invalid freebies amount</p>','<h3>WARNING!</h3>','toast-bottom-right');
					$(this).focus();
					$(this).val('');
				}

				if(parseFloat(validamt) < parseFloat($(this).val())){
					showToast('error','<p>Invalid freebies amount</p>','<h3>WARNING!</h3>','toast-bottom-right');
					$(this).focus();
					$(this).val('');
				}
				if(isNaN($(this).val())){
					showToast('error','<p>Please Enter Valid Amount</p>','<h3>WARNING!</h3>','toast-bottom-right');
					$(this).val('');
					$(this).focus();
				}
				$("#hidconsumablepaymentfreebies").val($(this).val());
				if(isValidAmount($(this).val(),false)){
					showToast('error','<p>Your payment exceeds to amount due.</p>','<h3>WARNING!</h3>','toast-bottom-right');
					$(this).val('');

				}
				$("#hidconsumablepaymentfreebies").val($(this).val());
				updateConPaymentFreebies();
			});
			$('#member_credit_amount').keyup(function (e) {

				if(!($('#member_credit').val())){
					showToast('error','<p>Please Choose '+MEMBER_LABEL+' First</p>','<h3>WARNING!</h3>','toast-bottom-right');
					$(this).val('');
					return;
				}


				if(isNaN($(this).val())){
					showToast('error','<p>Please Enter Valid Amount</p>','<h3>WARNING!</h3>','toast-bottom-right');
					$(this).val('');
					$(this).focus();
				}
				$("#hidmembercredit").val($(this).val());
				if(isValidAmount($(this).val(),false)){
					showToast('error','<p>Your payment exceeds to amount due.</p>','<h3>WARNING!</h3>','toast-bottom-right');
					$(this).val('');
				}
				$("#hidmembercredit").val($(this).val());
				updateMemberCredit();
			});

			$('#member_deduction_amount').keyup(function (e) {

				if(!($('#member_deduction').val())){
					showToast('error','<p>Please Choose member first</p>','<h3>WARNING!</h3>','toast-bottom-right');
					$(this).val('');
					return;
				}


				if(isNaN($(this).val())){
					showToast('error','<p>Please Enter Valid Amount</p>','<h3>WARNING!</h3>','toast-bottom-right');
					$(this).val('');
					$(this).focus();
				}
				$("#hidmemberdeduction").val($(this).val());
				if(isValidAmount($(this).val(),false)){
					showToast('error','<p>Your payment exceeds to amount due.</p>','<h3>WARNING!</h3>','toast-bottom-right');
					$(this).val('');
				}
				$("#hidmemberdeduction").val($(this).val());
				updateMemberDeduction();
			});

			$('#con_amount').keyup(function (e) {

				if(!($('#con_member').val())){
					showToast('error','<p>Please Choose '+MEMBER_LABEL+' First</p>','<h3>WARNING!</h3>','toast-bottom-right');
					return;
				}
				if (localStorage['hasType2'] == 1){
					//current
					var name = $("#con_member option:selected").text();
					var memId = $("#con_member").val();
					removeMemberDetails();
					$("#membersIdHelper").append('Member Id: ');
					$("#memberId").append(memId);
					$("#membersnameHelper").append('Name: ');
					$("#membersname").append(name);
					localStorage.removeItem("temp_item_holder");
				}
				var validamt = $('#con_member option:selected').attr('data-con');
				if(parseFloat(validamt) < parseFloat($(this).val())){
					showToast('error','<p>Invalid consumable amount</p>','<h3>WARNING!</h3>','toast-bottom-right');
					$(this).focus();
					$(this).val('');
				}
				if(isNaN($(this).val())){
					showToast('error','<p>Please Enter Valid Amount</p>','<h3>WARNING!</h3>','toast-bottom-right');
					$(this).val('');
					$(this).focus();
				}
				$("#hidconsumablepayment").val($(this).val());
				if(isValidAmount($(this).val(),false)){
					showToast('error','<p>Your payment exceeds to amount due.</p>','<h3>WARNING!</h3>','toast-bottom-right');
					$(this).val('');

				}
				$("#hidconsumablepayment").val($(this).val());
				updateConPayment();
			});
			$('body').on('click','.btnPayment',function(){
				var row = $(this).parents('tr');
				var total = row.attr('data-total');
				var credit_id = $(this).attr('data-id');
				var is_cod = $(this).attr('data-is_cod');
				var member_id = $(this).attr('data-member_id');
				getMembersInd(localStorage['company_id'],member_id);

				$('.nav-tabs li').removeClass('active');
				$('.tab-content .tab-pane').removeClass('active');
				$('.nav-tabs li').first().addClass('active');
				$('.tab-content .tab-pane').first().addClass('active');
				if(is_cod == 1){
					// hide other tab
					$('.notcash').show();
				} else {
					// show all tab
					$('.notcash').show();

				}
				$('#payment_member_credit_id').val(credit_id);
				showpricemodal('0',total);
			});
			$('#getpricemodal').on('hidden.bs.modal', function () {
				$("#credit_table").find("tr").remove();
				$("#bt_table").find("tr").remove();
				$("#ch_table").find("tr").remove();
				$("#tab_g :input[type='text']").val('');
				$("#tab_f :input[type='text']").val('');
				$("#tab_d :input[type='text']").val('');
				$("#tab_c :input[type='text']").val('');
				$("#tab_b :input[type='text']").val('');
				$("#tab_a :input[type='text']").val('');
				localStorage.removeItem('payment_cheque');
				localStorage.removeItem('payment_credit');
				localStorage.removeItem('payment_bt');
				localStorage.removeItem('payment_cash');
				localStorage.removeItem('payment_con');
				localStorage.removeItem('payment_con_freebies');
				localStorage.removeItem('payment_member_credit');


			});
			$('body').on('click','.btnFreightPayment',function(){
				var payment_id = $(this).attr('data-payment_id');
				alertify.confirm("Are you sure you want to mark this as paid?",function(e){
					if(e){
						$.ajax({
						    url:'../ajax/ajax_sales_query.php',
						    type:'POST',
						    data: {functionName:'freightPaidByPaymentId',payment_id:payment_id},
						    success: function(data){
						        alertify.alert(data);
							    getPage(0);
						    },
						    error:function(){

						    }
						});
					}
				});
			});

			$('body').on('click','#btnAddMoreMemberDeduction',function(){
				var member = $('#member_deduction');
				var member_id = member.val();
				var member_name = member.select2('data').text;
				var member_deduction_amount = $('#member_deduction_amount').val();
				var member_deduction_remarks = $('#member_deduction_remarks').val();
				var member_deduction_addtl_remarks = $('#member_deduction_addtl_remarks').val();
				$('#member_deduction_amount').val('');
				$('#member_deduction_remarks').val('');
				var ret = "<tr data-amount='"+member_deduction_amount+"' ><td>"+member_name+"</td><td>"+member_deduction_amount+"</td><td>"+member_deduction_remarks+"</td><td>"+member_deduction_addtl_remarks+"</td><td><span class='glyphicon glyphicon-remove removeItem'></span></td></tr>";
				if(!isValidAmount(member_deduction_amount,true)){
					$('#member_deduction_table > tbody').append(ret);
					updateMemberDeduction();
				} else {
					showToast('error','<p>Invalid payment</p>','<h3>WARNING!</h3>','toast-bottom-right');
				}

			});

			$("body").on('click','.paymentDetails',function(e){
				e.preventDefault();
				var payment_id = $(this).attr('data-payment_id');
				$.ajax({
					url: '../ajax/ajax_paymentDetails.php',
					type: 'POST',
					beforeSend: function(){
						$('#right-pane-container').html('Fetching record. Please wait.');
					},
					data: {id:payment_id},
					success: function(data){
						$('#right-pane-container').html(data);
						$('.right-panel-pane').fadeIn(100);
					}
				});

			});



		});




	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>