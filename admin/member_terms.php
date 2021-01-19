<?php
	// $user have all the properties and method of the current user
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('m_terms_request')) {
		// redirect to denied page
		Redirect::to(1);
	}

	$user_permbranch = $user->hasPermission('inventory_all');



?>


	<!-- Page content -->
	<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1><span id="menu-toggle" class='glyphicon glyphicon-list'></span> <?php echo MEMBER_LABEL; ?> Terms </h1>
		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}
		?>
		<div class="form-group">
			<a class='btn btn-default navPage' href="member_term_request.php"><i class='fa fa-plus'></i> Request Terms</a>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">
						<div class="row">
							<div class="col-md-6"><?php echo MEMBER_LABEL; ?> Terms	</div>
							<div class="col-md-6 text-right">
								<?php if($user->hasPermission('dl_inv')){ ?>
									<button id='btnDownloadExcel' title='Download Excel' class='btn btn-default btn-sm'><i class='fa fa-download'></i></button>
								<?php } ?>
							</div>

						</div>

					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' placeholder='Search client or item' id='txtSearch'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input style='width:100%;' type='hidden' name="member_id" id="member_id">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select  style='width:100%;' name="status" id="status">
										<option value=""></option>
										<option value="1" selected>Pending</option>
										<option value="2">Approved</option>
										<option value="3">Declined</option>
										<option value="4">Used Adjustment</option>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select  <?php echo (!$user_permbranch) ? 'disabled' : ''; ?> id="branch_id" name="branch_id" class="form-control">
										<option value=''></option>
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
									</select>
								</div>
							</div>
						</div>
						<div class="row">

							<div class="col-md-3">
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
							<div class="col-md-3">
								<div class="form-group">
									<input  class='form-control' type='hidden' name="user_id" id="user_id">
								</div>
							</div>
							<div class="col-md-3"></div>
							<div class="col-md-3 text-right"></div>
						</div>
						<input type="hidden" id="hiddenpage" />
						<button id='btnBatchApprove' class='btn btn-success'>Batch Approve</button>
						<button id='btnBatchDecline' class='btn btn-danger'>Batch Decline</button>
						<div id="holder"></div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- end page content wrapper-->
	<script>

		$(document).ready(function() {
			$("#user_id").select2({
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

			$("#user_id").change(function(){
				getPage(0);
			});

			getPage(0);

			$('body').on('click','.paging',function(){
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});
			$("#sales_type").select2({
				placeholder: 'Choose Sales Type',
				allowClear: true
			});
			function getPage(p){
				var search = $('#txtSearch').val();
				var member_id = $('#member_id').val();
				var status = $('#status').val();
				var sales_type = $('#sales_type').val();
				var branch_id = $('#branch_id').val();
				var user_id = $('#user_id').val();

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html('Loading...');
					},
					data:{page:p,sales_type:sales_type,user_id:user_id,branch_id:branch_id,search:search,member_id:member_id,status:status,functionName:'memberTerms',cid: <?php echo $user->data()->company_id; ?>},
					success: function(data){
						$('#holder').html(data);

					},
					error: function(){

						location.href='member_terms.php';
						$('.loading').hide();
					}
				});
			}


			$('body').on('click','#btnDownloadExcel',function(){
				var search = $('#txtSearch').val();
				var member_id = $('#member_id').val();
				var status = $('#status').val();
				var sales_type = $('#sales_type').val();
				var branch_id = $('#branch_id').val();
				var user_id = $('#user_id').val();
				sales_type = JSON.stringify(sales_type);
				window.open(
					'excel_downloader.php?downloadName=member_terms&search='+search+'&branch_id='+branch_id+'&status='+status+'&member_id='+member_id+'&sales_type='+sales_type+'&user_id='+user_id,
					'_blank' //
				);
			});

			$('body').on('change','#member_id,#status,#sales_type,#branch_id',function(){
				getPage(0);
			});
			$('body').on('keyup','#txtSearch',function(){
				getPage(0);
			});
			$("#status").select2({
				placeholder: 'Status',
				allowClear: true
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

			$('body').on('click','.btnApprove',function(){
				var id = $(this).attr('data-id');
				alertify.confirm("Are you sure you want to approve this request?", function(e){
					if(e){
						$('.loading').show();
						$.ajax({
							url:'../ajax/ajax_query2.php',
							type:'POST',
							data: {functionName:'approveMemberTerms',id:id},
							success: function(data){
								alertify.alert(data);
								$('.loading').hide();
								getPage(0);
							},
							error:function(){
								$('.loading').hide();
							}
						});
					}
				});
			});

			$('body').on('click','.btnDecline',function(){
				var id = $(this).attr('data-id');
				alertify.confirm("Are you sure you want to decline this request?", function(e){
					if(e){
						$('.loading').show();
						$.ajax({
							url:'../ajax/ajax_query2.php',
							type:'POST',
							data: {functionName:'declineMemberTerms',id:id},
							success: function(data){
								alertify.alert(data);
								$('.loading').hide();
								getPage(0);
							},
							error:function(){

								$('.loading').hide();
							}
						});
					}
				});
			});

			$('body').on('click','#btnBatchApprove',function(){
				var arr = [];
				var con=$(this);

				$('.chkApprove').each(function(){
					var chk = $(this);
					if(chk.is(':checked')){
						arr.push(chk.val());
					}
				});
				if(!arr.length){
					tempToast('error','Please select record to approve','Error');
					return;
				}
				button_action.start_loading(con);
				alertify.confirm("Are you sure you want to APPROVE this record(s)?",function(e){
					if(e){

						$.ajax({
						    url:'../ajax/ajax_member_service.php',
						    type:'POST',
						    data: {functionName:'batchApproveMember',ids:JSON.stringify(arr)},
						    success: function(data){
						        tempToast('info',data,'Info');
							    getPage(0);
							    button_action.end_loading(con);
						    },
						    error:function(){
							    tempToast('error','Error sending request. Please try again.','Error');
							    button_action.end_loading(con);
							    getPage(0);
						    }
						});


					} else {
						button_action.end_loading(con);
					}
				});

			});

			$('body').on('click','#btnBatchDecline',function(){
				var arr = [];
				var con=$(this);

				$('.chkApprove').each(function(){
					var chk = $(this);
					if(chk.is(':checked')){
						arr.push(chk.val());
					}
				});
				if(!arr.length){
					tempToast('error','Please select record to decline','Error');
					return;
				}
				button_action.start_loading(con);
				alertify.confirm("Are you sure you want to DECLINE this record(s)?",function(e){
					if(e){

						$.ajax({
							url:'../ajax/ajax_member_service.php',
							type:'POST',
							data: {functionName:'batchDeclineMember',ids:JSON.stringify(arr)},
							success: function(data){
								tempToast('info',data,'Info');
								getPage(0);
								button_action.end_loading(con);
							},
							error:function(){
								tempToast('error','Error sending request. Please try again.','Error');
								button_action.end_loading(con);
								getPage(0);
							}
						});


					} else {
						button_action.end_loading(con);
					}
				});

			});

		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>