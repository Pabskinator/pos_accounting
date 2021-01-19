<?php
	// $user have all the properties and method of the current user
	require_once '../includes/admin/page_head2.php';



?>

	<!-- Page content -->
	<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="page-content inset">
			<div class="content-header">
				<h1>Service Notification</h1>
			</div>


			<div class="panel panel-primary">
				<!-- Default panel contents -->
				<div class="panel-heading">List</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-3">
							<select  class='form-control' name="status" id="status">
								<option value="-1">Pending</option>
								<option value="1">With Duration</option>
								<option value="2">Notification</option>
							</select>
						</div>
					</div>
					<br>
					<div id="con"></div>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
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
			$('body').on('click','#saveDuration',function(){

				var con = $(this);
				var id = con.attr('data-id');
				var arr = [];

				$('#tblDetails tbody tr').each(function(){
					var row = $(this);
					var item_id = row.attr('data-item_id');
					var duration = row.children().eq(3).find('input').val();
					arr.push({
						item_id:item_id,
						duration:duration,
					})
				});

				$.ajax({
				    url:'../ajax/ajax_service_notif.php',
				    type:'POST',
				    data: {functionName:'updateOrderNotif',id:id,durations:JSON.stringify(arr)},
				    success: function(data){
					    $('#myModal').modal('hide');
					    getNotification();

				    },
				    error:function(){

				    }
				});
			});

			$('body').on('click','#saveDate',function(){

				var con = $(this);
				var id = con.attr('data-id');
				var arr = [];

				$('#tblDetails tbody tr').each(function(){
					var row = $(this);
					var item_id = row.attr('data-item_id');
					var date = row.children().eq(4).find('input').val();
					arr.push({
						item_id:item_id,
						start_date:date,
					});
				});

				$.ajax({
					url:'../ajax/ajax_service_notif.php',
					type:'POST',
					data: {functionName:'updateOrderDate',id:id,start_dates:JSON.stringify(arr)},
					success: function(data){
						$('#myModal').modal('hide');
						getNotification();
					},
					error:function(){

					}
				});

			});
			$('body').on('change','#status',function(){
				getNotification();
			});
			getNotification();
			function getNotification(){
				var status = $('#status').val();
				$.ajax({
				    url:'../ajax/ajax_service_notif.php',
				    type:'POST',
				    data: {functionName:'getNotif' ,status:status},
				    success: function(data){
					    $('#con').html(data);

				    },
				    error:function(){

				    }
				});
			}
			$('body').on('click','.btnDetails',function(){
				var con = $(this);
				var id = con.attr('data-id');
				$('#myModal').modal('show');
				$.ajax({
				    url:'../ajax/ajax_service_notif.php',
				    type:'POST',
				    data: {functionName:'orderDetails',id:id},
				    success: function(data){
					    $('#mbody').html(data);

					    $('.dt_date').datepicker({
						    autoclose:true
					    }).on('changeDate', function(ev){
						    $('.dt_date').datepicker('hide');
					    });
				    },
				    error:function(){

				    }
				});
			});

		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>