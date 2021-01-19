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

			<?php
				$myCurRequest = new Monitoring();
				$mreq = $myCurRequest->allRequest();
				if($mreq){

					?>
					<h3>Request monitoring</h3>
					<?php
					$arrTabNav = [];
					$tabContent = [];
					foreach ($mreq as $req) {
						//$mprocess = new Process($req->process_id);
						//$req_by = new User($req->who_request);
						//if(!$mprocess) continue;
						if(!in_array($req->process_name,$arrTabNav)){
							$arrTabNav[$req->process_name] = $req->process_name;
						}
						$step = new Steps();
						$approve_auth = $step->hasAuth($req->process_id,$user->data()->position_id);

						if(!(isset($approve_auth->cnt) && !empty($approve_auth->cnt))){
							continue;
						}

						?>
						<?php
						if($req->current_step != -1 && $req->current_step != -2){
							$step = new Steps();
							$stepname = $step->getStepName($req->process_id,$req->current_step);
							//	$steplbl = $stepname->name;
							$steplbl = "Pending (<span class='text-danger'>{$stepname->name}</span>)";
							$class='cls-pending';
						} else {
							if($req->current_step != -1){
								$steplbl = "Approved";
								$class='cls-approved';
							} else {
								if($req->from_cancel == 0){
									$steplbl = "Declined";
									$class='cls-declined';
								} else {
									$steplbl = "Cancelled";
									$class='cls-cancelled';
								}

							}

						}
						?>
						<?php
						$btn = "<button style='margin:3px;' type='button' data-step='{$req->id}'  class='btn btn-success showData'><span class='glyphicon glyphicon-list-alt'></span> Show Details</button>";
						$tabContent[$req->process_name] .= "<tr class='$class'>";
						//$tabContent[$req->process_name] .= "<td>".strtoupper($req->process_name)."</td>";
						$tabContent[$req->process_name] .= "<td>$req->id</td>";
						$tabContent[$req->process_name] .= "<td>".ucwords($req->lastname . ", " . $req->firstname)."</td>";
						$tabContent[$req->process_name] .= "<td>".date('m/d/Y H:i:s A',$req->created)."</td>";
						$tabContent[$req->process_name] .= "	<td>$steplbl</td>";
						$tabContent[$req->process_name] .= "<td>$btn</td>";
						$tabContent[$req->process_name] .= "</tr>";
					}

					?>

					<?php
					$nav = '<ul class="nav nav-tabs" role="tablist">';
					$con = '<div class="tab-content">';
					$isFirst = true;
					foreach($arrTabNav as $tab){
						$key = str_replace(' ','',$tab);
						$liactive = '';
						if($isFirst){
							$liactive ='active';
							$isFirst = false;
						}
						$nav .= "<li role='presentation' class='$liactive'><a href='#p_$key' aria-controls='p_$key' role='tab' data-toggle='tab'>$tab</a></li>";


						if(isset($tabContent[$tab]) && !empty($tabContent[$tab])){
							$con .= "<div role='tabpanel' class='tab-pane $liactive' id='p_{$key}'>";
							$opt = "<select class='form-control selectstatus' ><option value=''>All</option><option value='1'>Pending</option><option value='2'>Approved</option><option value='3'>Declined</option><option value='4'>Cancelled</option></select>";
							$con .= "<br><div class='row'><div class='col-md-8'></div><div class='col-md-4'>$opt</div></div>";
							$con .= "<br><table class='table'>";
							$con .= "<tr><th>REQUEST ID</th><th>REQUESTED BY</th><th>DATE REQUESTED</th><th>STATUS</th><th></th></tr>";
							$con .= $tabContent[$tab];
							$con .= "</table>";
							$con .= "</div>";
						} else {
							$con .= "<div role='tabpanel' class='tab-pane $liactive' id='p_{$key}'>";
							$con .= "<br><div class='alert alert-info'>No request found.</div>";
							$con .= "</div>";
						}


					}
					$nav .= '</ul>';
					$con .= '</div>';
					echo $nav;
					echo $con;

				} else {
					?>
					<div class="container-fluid">
						<div class="alert alert-info">
							No Request Yet.
						</div>
					</div>
					<?php
				}
			?>
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
			$('body').on('change','.selectstatus',function(){
				var v = $(this).val();
				$('.cls-pending').hide();
				$('.cls-approved').hide();
				$('.cls-declined').hide();
				$('.cls-cancelled').hide();
				if(v == 1){
					$('.cls-pending').show();
				} else if(v == 2) {
					$('.cls-approved').show();
				}else if(v == 3) {
					$('.cls-declined').show();
				} else if(v == 4) {
					$('.cls-cancelled').show();
				}else {
					$('.cls-pending').show();
					$('.cls-approved').show();
					$('.cls-declined').show();
					$('.cls-cancelled').show();
				}
			});
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

			$('.cancelRequest').click(function(){
				// get data via ajax

				var step = $(this).attr('data-step');
				$.ajax({
					url : 'query.php',
					type: 'POST',
					data: {mon_id:step,functionName:'cancelRequest'},
					success: function(data){
						alert(data);
						location.href='my_request.php';
					}
				});
				$('#myModal').modal('show');
			});


			$('body').on('click','#addmoreimg',function(){
				var mon_id = $(this).attr('mon_id');
				var from = $(this).attr('f');

				$("#file_container"+mon_id+from).append("<input style='margin-top:3px;' name='mon_img[]' class='btn btn-default' type='file' style='display:block' required /> <input type='button' style='margin-top:3px;' class='btn btn-danger removeUpload' value='remove'>");
			});
			$('body').on('click','.removeUpload',function(){
				$(this).prev().remove();
				$(this).remove();
			});

		});

	</script>


<?php require_once '../includes/monitoring/page_tail.php'; ?>