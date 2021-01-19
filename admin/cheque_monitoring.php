<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('cheque_monitoring')) {
		// redirect to denied page
		Redirect::to(1);
	}

?>


	<!-- Page content -->
<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1><span id="menu-toggle" class='glyphicon glyphicon-list'></span> Cheque Monitoring </h1>
		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}

			if(Input::get('mem')){
				?>
				<input type="hidden" value='<?php echo Input::get('mem'); ?>' id='mem_id' />
				<?php
			}
		?>

		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="col-md-12 text-right">
						<a href='bounce_check.php' class='btn btn-default btn-sm'>Bounce Cheque</a>
					</div>
				</div>
				<br>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">
						<div class="row">
							<div class="col-md-6">List</div>
							<div class="col-md-6 text-right">
								<button id='btnDownloadExcel' title='Download Excel' class='btn btn-default btn-sm'><i class='fa fa-download'></i></button>
							</div>
						</div>
					</div>
					<div class="panel-body">
						<div class="row">

							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon"><span class='glyphicon glyphicon-search'></span></span>
										<input type="text" id="searchCheque" class='form-control' placeholder='Search..'/>
									</div>
								</div>
							</div>

							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon"><span class='glyphicon glyphicon-calendar'></span></span>
										<input type="text" id="dt1" class='form-control' placeholder='Date From'/>
									</div>
								</div>
							</div>

							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon"><span class='glyphicon glyphicon-calendar'></span></span>
										<input type="text" id="dt2" class='form-control' placeholder='Date To'/>
									</div>
								</div>
							</div>

							<div class="col-md-3">
								<div class="form-group">
									<select name="check_type" id="check_type" class='form-control'>
										<option value="">--Select Check Type--</option>
										<option value="1">Good</option>
										<option value="2">DAIF</option>
										<option value="3">Bounce</option>
										<option value="4">Others</option>
									</select>
								</div>
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
								<div class="col-md-3">
									<div class="form-group">
									<select id="branch_id" name="branch_id" class="form-control" multiple>
										<?php if(Configuration::isAquabest()){
											?>
											<option value="-1">Caravan</option>
											<?php
										}?>
										<?php
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
							<div class="col-md-3">
								<div class="form-group">
									<select name="with_terms" id="with_terms" class='form-control'>
										<option value="-1">All</option>
										<option value="1">With Terms Only</option>
										<option value="0">Without Terms Only</option>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" id='agent_id' class='form-control'>
								</div>
							</div>
								<div class="col-md-6">
									<div class="form-group">
									<div id="terminalholder">
										<p class='text-info'></p>
									</div>
									</div>
								</div>
						</div>
						<input type="hidden" id="hiddenpage" />
						<div id="holder"></div>
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
	<div class="modal fade" id="myModalBounce" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id=''>Please Complete the Form</h4>
					</div>
					<div class="modal-body" id=''>
						<input type="hidden" id='hid_id' value=''>
						<input type="hidden" id='hid_val' value=''>
						<div class="form-group">
							<strong>Reason</strong>
							<select name="b_reason" id="b_reason" class='form-control'>
								<option value="">Choose Reason</option>
								<option value="DAIF">DAIF</option>
								<option value="DAUD">DAUD</option>
								<option value="With Alteration">With Alteration</option>
								<option value="No Signature">No Signature</option>
							</select>
						</div>
						<div class="form-group">
							<strong>Others: </strong> <input id='b_others' class='form-control' type="text" placeholder="">
						</div>
						<div class="form-group">
							<button class='btn btn-default' id='btnSave'>Save</button>
						</div>
					</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<script>

		$(document).ready(function() {

			$('#dt1').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt1').datepicker('hide');
				getPage(0);
			});
			$('#dt2').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt2').datepicker('hide');
				getPage(0);
			});
			$("#sales_type").select2({
				placeholder: 'Choose Sales Type',
				allowClear: true
			});

			$('body').on('click','#btnSave',function(){

				var id = $('#hid_id').val();
				var val = $('#hid_val').val();
				var b_reason = $('#b_reason').val();
				var b_others = $('#b_others').val();
				$('#myModalBounce').modal('hide');

				$.ajax({
						url: '../ajax/ajax_query.php',
						type:'post',
						data:{functionName:'chequeChangeStatus',b_reason:b_reason,b_others:b_others,id:id,val:val},
						success: function(data){
							var page = $('#hiddenpage').val();
							var search = $('#searchCheque').val();
							var check_type = $('#check_type').val();
							if(data){
								alertify.alert(data);
							}
							getPage(page,search,check_type);
						}
				});

			});

			$("#agent_id").select2({
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

			getPage(0);
			$('body').on('click','.paging',function(){

				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);

			});

			var timer_ajax;
			$("#searchCheque").keyup(function(){

				clearTimeout(timer_ajax);
				timer_ajax = setTimeout(function() {
					getPage(0);
				}, 1000);

			});

			$("#check_type,#with_terms,#agent_id").change(function(){

				getPage(0);

			});

			function getPage(p){
				var search = $('#searchCheque').val();
				var check_type = $('#check_type').val();
				var dt1 = $('#dt1').val();
				var dt2 = $('#dt2').val();
				var member_id = 0;
				var branch_id = $('#branch_id').val();
				var terminal_id = $('#terminal_id').val();
				var sales_type = $('#sales_type').val();
				var with_terms = $('#with_terms').val();
				if($('#mem_id').length > 0){
					member_id = $('#mem_id').val();
				}

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend: function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,sales_type:sales_type,with_terms:with_terms,dt1:dt1,dt2:dt2,branch_id:branch_id,terminal_id:terminal_id,functionName:'chequeList',cid: <?php echo $user->data()->company_id; ?>,search:search,check_type:check_type,member_id:member_id},
					success: function(data){

						$('#holder').html(data);

					}
				});
			}

			$('body').on('click','#btnDownloadExcel',function(){

				var search = $('#searchCheque').val();
				var check_type = $('#check_type').val();
				var dt1 = $('#dt1').val();
				var dt2 = $('#dt2').val();
				var member_id = 0;
				var branch_id = $('#branch_id').val();
				var terminal_id = $('#terminal_id').val();
				var sales_type = $('#sales_type').val();
				var with_terms = $('#with_terms').val();

				if($('#mem_id').length > 0){
					member_id = $('#mem_id').val();
				}

				if(member_id){
					member_id = JSON.stringify(member_id);
				}

				if(branch_id){
					branch_id = JSON.stringify(branch_id);
				} else {
					branch_id = 0;
				}

				if(!terminal_id){
					terminal_id =0;
				}

				if(sales_type){
					sales_type = JSON.stringify(sales_type);
				} else {
					sales_type = 0;
				}

				if(dt1 && dt2){
					window.open(
						'excel_downloader.php?downloadName=checkMon&search='+search+'&check_type='+check_type+'&dt1='+dt1+'&dt2='+dt2+'&member_id='+member_id+'&branch_id='+branch_id+'&terminal_id='+terminal_id+'&sales_type='+sales_type+'&with_terms='+with_terms,
						'_blank' //
					);
				} else {
					alert("Dates are required when downloading data. Please enter dates first.");
				}

			});
			$('body').on('change','.rdChequeType',function(){
				var id = $(this).attr('data-id');
				var value = $(this).val();
				var td = $(this).parents('td');
				var def_val = td.attr('data-default_value');
				if(value == 2 || value == 3 || value == 4){
					alertify.confirm("This cheque will be converted to member credit. You cannot undo this action. Continue?",function(e){

						if(e){
							$('#hid_id').val(id);
							$('#hid_val').val(value);
							$('#myModalBounce').modal('show');

						} else {
							$("input[name=rdChequeType"+id+"][value=" + def_val + "]").prop('checked', true);
						}

					});
				} else {
					$.ajax({
						url: '../ajax/ajax_query.php',
						type:'post',
						data:{functionName:'chequeChangeStatus',id:id,val:value},
						success: function(data){
							var page = $('#hiddenpage').val();
							var search = $('#searchCheque').val();
							var check_type = $('#check_type').val();
							if(data){
								alertify.alert(data);
							}
							getPage(page,search,check_type);

						}
					});
				}



			});
			$('body').on('click','.btnAddRemarks',function(){
				var id = $(this).attr('data-id');
				$('#myModal').modal('show');
				$('#mtitle').html('Add Remarks');
				$('#mbody').html('Loading...');
				$.ajax({
				    url:'../ajax/ajax_query2.php',
				    type:'POST',
				    data: {functionName:'getChequeRemarks',id:id},
				    success: function(data){
					    $('#mbody').html(data);
				    },
				    error:function(){

				    }
				});
			});
			$('body').on('click','#btnAddRemarks',function(){
				var id =$('#cheque_id').val();
				var remarks = $('#cheque_remarks').val();
				var btn = $(this);
				var btnoldval = btn.html();
				btn.attr('disabled',true);
				btn.html('Loading...');
				if(remarks && remarks.trim() != ''){
					$.ajax({
					    url:'../ajax/ajax_query2.php',
					    type:'POST',
					    data: {functionName:'addChequeRemarks',id:id,remarks:remarks},
					    success: function(data){
						    $.ajax({
							    url:'../ajax/ajax_query2.php',
							    type:'POST',
							    data: {functionName:'getChequeRemarks',id:id},
							    success: function(data){
								    $('#mbody').html(data);
							    },
							    error:function(){

							    }
						    });
					    },
					    error:function(){

					    }
					})
				} else {
					alertify.alert('Invalid Remarks');
					btn.attr('disabled',false);
					btn.html(btnoldval);
				}
			});
			$('body').on('click','.getPTDetails',function(){
				var payment_id = $(this).attr('data-payment_id');
				$.ajax({
					url: '../ajax/ajax_query.php',
					type: 'POST',
					data: {id:payment_id,functionName:'getPTDetails'},
					success: function(data){
						$("#mbody").html(data);
						$("#mtitle").html('Transaction Details');
						$("#myModal").modal('show');
					}
				});
			});

			$("#branch_id").select2({
				placeholder: 'Choose Branch',
				allowClear: true
			});

			$("#branch_id").change(function(){

				var bid = $(this).val();

				getPage(0);

				$.ajax({
					url:'../ajax/ajax_query.php',
					type:'post',
					data:{ functionName:'getTerminals',branch_id:bid },
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

		});



	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>