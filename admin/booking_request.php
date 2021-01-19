<?php
	// $user have all the properties and method of the current user
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('m_ref')) {
		// redirect to denied page
		Redirect::to(1);
	}


?>



	<!-- Page content -->
	<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
			</h1>
		</div>

		<div>
			<div class="panel panel-default">
				<div class="panel-body">
					<h3>Booking request</h3>
					<div class="row">
						<div class="col-md-8"></div>
						<div class="col-md-4">
							<select name="status" id="status" class='form-control'>
								<option value="1">Pending</option>
								<option value="2">Confirmed</option>
								<option value="3">Reject</option>
							</select>
						</div>
					</div>
					<div id="con"></div>
				</div>
			</div>
		</div>
	</div>
	<!-- end page content wrapper-->
	<script>

		$(document).ready(function() {
			getRequest();
			function getRequest(){
				var status = $('#status').val();
				$.ajax({
				    url:'../ajax/ajax_member_service.php',
				    type:'POST',
				    data: {functionName: 'getMemberBookingRequest',status:status},
				    success: function(data){
				        $('#con').html(data);
				    },
				    error:function(){
				        
				    }
				});
			}
			$('body').on('click','.btnOk',function(){
				var con = $(this);
				var id = con.attr('data-id');
				alertify.confirm("Are you sure you want to process this request?",function(e){
					if(e){
						$.ajax({
						    url:'../ajax/ajax_member_service.php',
						    type:'POST',
						    data: {functionName:'serviceRequestChangeStatus',id:id,status:2},
						    success: function(data){
						        tempToast('info',data,'Info');
							    getRequest();
						    },
						    error:function(){

						    }
						});
					}
				});
			});
			$('body').on('click','.btnReject',function(){
				var con = $(this);
				var id = con.attr('data-id');
				alertify.confirm("Are you sure you want to reject this request?",function(e){
					if(e){
						$.ajax({
							url:'../ajax/ajax_member_service.php',
							type:'POST',
							data: {functionName:'serviceRequestChangeStatus',id:id,status:3},
							success: function(data){
								tempToast('info',data,'Info');
								getRequest();
							},
							error:function(){

							}
						});
					}
				});
			});
			$('body').on('change','#status',function(){
				getRequest();
			});
		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>