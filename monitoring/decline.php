<?php


	require_once '../includes/monitoring/page_head.php';
	if(!$user->hasPermission('dashboard')){
		// redirect to denied page
		//	Redirect::to(1);
	}
	if(isset($_GET['process'])) {
		$process_id = $_GET['process'];
	} else {
		$process_id = 0;

	}


?>


	<!-- Sidebar -->
<?php include_once '../includes/monitoring/sidebar.php';?>
	<!-- Page content -->
	<div id="page-content-wrapper" style='padding-top:20px;'>

		<!-- Keep all page content within the page-content inset div! -->
		<div class="container-fluid">
			<h3>Decline Request</h3>
	<div class="panel panel-primary">
		<div class="panel-heading">Request Monitoring</div>
		<div class="panel-body">
			<?php
				$myCurRequest = new Monitoring();
				$mreq = $myCurRequest->getDeclineRequest($user->data()->id);
				if($mreq){
					?>
					<table class="table table-bordered">
						<tr>
							<th>PROCESS</th>
							<th>REQUEST ID</th>
							<th>DATE REQUESTED</th>
							<th>ATTACHMENT</th>
							<th>ACTION</th>
						</tr>
						<?php
							foreach ($mreq as $req) {
							$mprocess = new Process($req->process_id);

						?>
						<tr>
							<td><?php echo  strtoupper($mprocess->data()->name); ?></td>

							<td><span class='badge'><?php echo $req->id; ?></span></td>
							<td><?php echo date('m/d/Y H:i:s A',$req->created); ?> </td>
						
							<td>
								<?php 
									$att = new Attachment();
									$req_attach = $att->getAttachments($req->id);
									if($req_attach){
										foreach ($req_attach as $value) {
											?>
											<a style='margin:3px;' class='btn btn-default' href="attachments/<?php echo $value->filename; ?>" target='_blank'>
												<span class='glyphicon glyphicon-paperclip'></span>  
												<?php echo substr($value->filename,17); ?>
											</a> <br>
											<?php
										}
									} else {
										?>
										No Attachment
										<?php
									}
								?>
							</td>
							<td>
								<button style='margin:3px;' type='button' data-step='<?php echo $req->id; ?>'  class='btn btn-success showData'>
									<span class='glyphicon glyphicon-list-alt'></span> Show Details
								</button>
							</td>
							<?php
								}
							?>
					</table>
				<?php
				} else {
						?>
				<div class="container-fluid">
					<div class="alert alert-info">
						No Decline Request Yet.
					</div> 
				</div>
				<?php
				}
			?>
			</div>
		</div>
		</div>
	</div>
	<!-- Modal bootstap -->
	<!-- Modal bootstap -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">Modal title</h4>
				</div>
				<div class="modal-body" id="mbody">
					...
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

				</div>
			</div>
		</div>
	</div>

	<!-- Modal end -->
	<script type="text/javascript">

		$(function(){

			$('.showData').click(function(){
				// get data via ajax

				var step = $(this).attr('data-step');
				$('#myModalLabel').html("Details");
				$.ajax({
					url : '../ajax/ajax_showData.php',
					type: 'POST',
					beforeSend: function(){
						$("#mbody").html('Loading...');
					},
					data: {mon_id:step},
					success: function(data){
						$("#mbody").html(data);
					}
				});
				$('#myModal').modal('show');
			});
			$('body').on('click','#btnReSubmit',function(e){
				e.preventDefault();
				var data = $('#formReturn').serializeArray();
				console.log(JSON.stringify(data));
				$.ajax({
				    url:'query.php',
				    type:'POST',
				    data: {functionName:'reSubmitRequest', datajson:JSON.stringify(data)},
				    success: function(data){
				        alertify.alert(data,function(){
					        location.href='decline.php';
				        });
				    },
				    error:function(){

				    }
				});

			});
		});

	</script>


<?php require_once '../includes/monitoring/page_tail.php'; ?>