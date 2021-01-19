<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('item_commission')){
		// redirect to denied page
		Redirect::to(1);
	}



?>



	<!-- Page content -->
	<div id="page-content-wrapper">




	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<div>
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					Commission
				</h1>

			</div>
		</div>


				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Items with commission</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<select class='form-control' name="status" id="status">
										<option value="0">Pending</option>
										<option value="1">Processed</option>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' id='dt1' placeholder='Date From'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' id='dt2' placeholder='Date To'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
								<button class='btn btn-default' id='btnSubmit'>Submit</button>
								</div>
							</div>
						</div>
						<div id="con"></div>
						<div class="row">
							<div class="col-md-12 text-right" >
								<button class='btn btn-default' id='btnBatch' style='display:none;'>Batch Process</button>
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

	<script>

		$(document).ready(function(){
			$('body').on('click','#btnBatch',function(){
				
				alertify.confirm("Are you sure you want to continue this action?",function(e){
					if(e){
						var arr= [];
						$('#tblForApproval > tbody > tr').each(function(){
							var row = $(this);
							var agent_id = row.attr('data-id');
							var chk = row.children().eq(0).find('input');
							if(chk.is(':checked')){
								arr.push(agent_id);
							}

						});
						if(arr.length){
							var dt1  = $('#dt1').val();
							var dt2  = $('#dt2').val();
							$.ajax({
							    url:'../ajax/ajax_member_service.php',
							    type:'POST',
							    data: {functionName:'batchProcessCommission',dt1:dt1,dt2:dt2, agent_ids: JSON.stringify(arr)},
							    success: function(data){
								    getCommission();
								    alert(data);
							    },
							    error:function(){
							        
							    }
							});
						}
					}
				});
			});
			$('body').on('change','#chkAll',function(){
				if($('#chkAll').is(":checked")){
					checkAll(1);
				} else {
					checkAll(0);
				}
			});
			$('body').on('change','.chbk',function(){
				toggleBatch();
			});
			function checkAll(t){
				if(t){
					$('.chbk').prop("checked",true);
				} else {
					$('.chbk').prop("checked",false);
				}
				toggleBatch();

			}
			function hasCheck(){
				var ret = false;
				$('.chbk').each(function(){
					var chk = $(this).is(":checked");
					if(chk){
						ret = true;
					}
				});
				return ret;
			}
			function toggleBatch(){
				var i = hasCheck();
				if(i){
					$('#btnBatch').show();
				} else {
					$('#btnBatch').hide();
				}
			}
			$('body').on('click','#btnSubmit',function(){
				getCommission();
			});
			getCommission();
			$('body').on('click','.btnDetails',function(){
				var con = $(this);
				var id = con.attr('data-id');
				var pay_date =con.attr('data-pay_date');
				var status =con.attr('data-status');
				var dt1  = $('#dt1').val();
				var dt2  = $('#dt2').val();

				$('#myModal').modal('show');
				$('#mbody').html('Loading...');
				$.ajax({
				    url:'../ajax/ajax_member_service.php',
				    type:'POST',
				    data: {functionName:'commissionDetails',id:id,pay_date:pay_date,status:status,dt1:dt1,dt2:dt2},
				    success: function(data){
				        $('#mbody').html(data);
				    },
				    error:function(){

				    }
				})


			});
			$('#dt1').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt1').datepicker('hide');
			});
			$('#dt2').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt2').datepicker('hide');
			});
			function getCommission(){
				var status = $('#status').val();
				var dt1 = $('#dt1').val();
				var dt2 = $('#dt2').val();
				$.ajax({
					url:'../ajax/ajax_member_service.php',
					type:'POST',
					data: { functionName:'getCommission',status:status,dt1:dt1, dt2:dt2},
					success: function(data){
						$('#con').html(data);
					},
					error:function(){

					}
				}) ;
			}
			$('body').on('click','.btnPay',function(){
				var con = $(this);
				var agent_id = con.attr('data-id');
				var dt1  = $('#dt1').val();
				var dt2  = $('#dt2').val();
				alertify.confirm("Are you sure you want to process this request?",function(e){
					if(e){
						$.ajax({
							url:'../ajax/ajax_member_service.php',
							type:'POST',
							data: {id:agent_id, functionName:'payCommission',dt1:dt1,dt2:dt2},
							success: function(data){
								getCommission();
							},
							error:function(){

							}
						}) ;
					}
				});

			});

		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>