<?php

	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require_once '../includes/monitoring/page_head.php';
	if(!$user->hasPermission('dashboard')){
		// redirect to denied page
		//	Redirect::to(1);
	}
	if(isset($_GET['process']) && isset($_GET['step']) ) {
		$process_id = $_GET['process'];
		$step_id = $_GET['step'];
	} else {
		$process_id = 0;
		$step_id = 0;
	}

?>


	<!-- Sidebar -->
<?php include_once '../includes/monitoring/sidebar.php';?>
	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<?php
			if($process_id && $step_id){
				$curProcess = new Process($process_id);
				$curStep = new Steps($step_id);
			}
		?>
		<div class="container-fluid">
		<h3><?php echo  $curStep->data()->name; ?></h3>
		<?php
			$curMonitoring = new Monitoring();
			$monitoringItems =$curMonitoring->getMonitoring ($process_id,$curStep->data()->step_number);
			if($monitoringItems){
					$checkstep = new Steps();
					$counsteps = $checkstep->countSteps($process_id);
				?>
				<table class="table">
					<tr>

						<th>Request Id</th>
						<th>Date Created</th>
						<th>Who Requested</th>
						<th>Attachment</th>
						<th>Report</th>
						<th>Details</th>

					</tr>

					<?php
					
						foreach ($monitoringItems as $value) {
							$request_user = new User($value->who_request);
							?>
							<tr>
								<td><span class='badge'><?php echo $value->id; ?></span></td>
								<td><?php echo date('m/d/Y h:i:s A',$value->created); ?></td>
								<td><?php echo ucwords($request_user->data()->lastname . ", " .$request_user->data()->firstname . " " . $request_user->data()->middlename); ?></td>
								<?php 
									
										
										
								?>
								<td>
								<?php 
								
									$process = true;
									$att = new Attachment();
									$req_attach = $att->getAttachments($value->id,$step_id);
									if($req_attach){
										foreach ($req_attach as $at) {
											?>
											<a style='margin:3px;' class='btn btn-default' href="attachments/<?php echo $at->filename; ?>" target='_blank'>
												<span class='glyphicon glyphicon-paperclip'></span> 
												<?php echo substr($at->filename,17); ?>
											</a> <br/>
											<?php
										}
									} else {
										if($curStep->data()->has_attachment == 1){
										$process = false;
										$msg = "(Required)";
										}
										if($curStep->data()->is_required == 0){
											$msg = "(Optional)";
										}
										?>
										<p class='text-danger'> Attachment <?php echo $msg; ?></p>
										<?php
									}




								?>
								</td>
								<td>
									<?php

										$remarks_list = new Remarks_list();
										$remarks = $remarks_list->getServices($value->id,'monitoring',$user->data()->company_id);
										if($remarks){
											echo "<table class='table'>";
											$ctrrep = 1;
											foreach($remarks as $rem){

												echo "<tr>";
												echo "<td>";
												echo "<button class='btn btn-default btn-sm btnShowReport' data-text='$rem->remarks'><i class='fa fa-paperclip'></i> Report $ctrrep</button> ";
												echo "</td>";
												echo "<td>";

												echo "</td></tr>";
												$ctrrep++;
											}
											echo "</table>";
										}else {
											$msg = "(Required)";
											if($curStep->data()->has_report == 1){
												$process = false;
											}
											if($curStep->data()->is_report_required == 0){
												$msg = "(Optional)";
											}
											?>
											<p class='text-danger'> Report <?php echo $msg; ?></p>
											<?php
										}

									?>
								</td>
								<?php
									if($curStep->data()->has_attachment == 1){
										if($curStep->data()->is_required == 0){
											$process = true;
											$msg = "(Optional)";
										}
									} else {
										$process = true;
									}
								?>
								<td>

									<button type='button' data-mon-id='<?php echo  $value->id; ?>'  class='btn btn-default showData'>
									<span class='glyphicon glyphicon-list-alt'></span> Show Details
									</button>
									<?php
									if ($process == true){
										 if ($counsteps->count_step != $curStep->data()->step_number) {
											$finalstep = "data-final-step='0'";
										}else {
											$finalstep = "data-final-step='1'";
										}
										?>
											<button type='button' style='margin:3px;' data-method='1' <?php echo $finalstep; ?> data-process_id='<?php echo $process_id; ?>' data-step-id='<?php echo $step_id; ?>' data-mon-id='<?php echo $value->id ?>'  class='btn btn-primary processData'>
											<span class='glyphicon glyphicon-ok-sign'></span> Process
											</button>
											<button type='button' style='margin:3px;' data-method='2' <?php echo $finalstep; ?> data-process_id='<?php echo $process_id; ?>' data-step-id='<?php echo $step_id; ?>' data-mon-id='<?php echo $value->id ?>'  class='btn btn-danger processData'>
											<span class='glyphicon glyphicon-exclamation-sign'></span> Decline
											</button>
											<?php

									} else {
										?>
									
										<?php
									}
									
									?>
								</td>
							</tr>
						<?php
						}

					?>

				</table>

			<?php
			} else { // no monitoring
				?>
				<div class="alert alert-info" style='width:95%;margin:0 auto;'>There is no pending Request</div>

			<?php
			}
		?>
		</div>
	</div>


	<!-- Modal bootstap -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style='width:70%;'>
			<div class="modal-content" >
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
	<div class="modal fade" id="processModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="ptitle"></h4>
				</div>
				<div class="modal-body" id="pbody">
				<input type='hidden' id='p_step_id'>
				<input type='hidden' id='p_mon_id'>
				<input type='hidden' id='p_process_id'>
				<input type='hidden' id='p_final'>
				<input type='hidden' id='p_method'>
				<div class="form-group">
						<label class="col-md-4 control-label" for="remarksProcess">Remarks</label>
						<div class="col-md-8">
						<input id="remarksProcess" name="remarksProcess" placeholder="Remarks" class="form-control input-md" type="text" >
						
						<span class="help-block">Remarks about the request (Optional)</span>
						</div>
						<br>
				</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<input type='button' id='p_submit' value='submit' class='btn btn-primary'>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="myModalReport" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel">Details</h4>
				</div>
				<div class="modal-body" id="rbody">

				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		$(function(){

			$('.showData').click(function(){
				// get data via ajax

				var mon_id = $(this).attr('data-mon-id');
				$('#myModalLabel').html("");
				$.ajax({
					url : '../ajax/ajax_showData.php',
					type: 'POST',
					beforeSend: function(){
						$("#mbody").html('Loading...');
					},
					data: {mon_id:mon_id},
					success: function(data){
						$("#mbody").html(data);
					}
				});
				$('#myModal').modal('show');
			});
			$('.processData').click(function(){
				// get data via ajax
				var method = $(this).attr('data-method');
				var mon_id = $(this).attr('data-mon-id');
				var process = $(this).attr('data-process_id');
				var step_id= $(this).attr('data-step-id');
				var is_final= $(this).attr('data-final-step');
				$('#p_step_id').val(step_id);
				$('#p_mon_id').val(mon_id);
				$('#p_process_id').val(process);
				$('#p_final').val(is_final);
				$('#p_method').val(method);
				if(method == 1){
					$('#ptitle').html("Process Request");
				} else if (method == 2){
					$('#ptitle').html("Decline Request");
				}
				$('#processModal').modal('show')
				// finalize process codes
				
			});
			$('#p_submit').click(function(){
				var mon_id =	$('#p_mon_id').val();
				var process =	$('#p_process_id').val();
				var step_id = $('#p_step_id').val();
				var remarks = $("#remarksProcess").val();
				var is_final =$('#p_final').val();
				var method = $('#p_method').val();
				if(confirm("Are you sure you want to process this request?")){
				$.ajax({
					url : '../ajax/ajax_processData.php',
					type: 'POST',
					data: {is_final:is_final,mon_id:mon_id,process:process,step_id:step_id,remarks:remarks,method:method},
					success: function(data){
						location.reload();
						}
					});
				}
			});
			$('body').on('click','.btnShowReport',function(){
				var rep = $(this).attr('data-text');
				$('#rbody').html(rep);
				$('#myModalReport').modal('show');
			});
		});
	</script>


<?php require_once '../includes/monitoring/page_tail.php'; ?>