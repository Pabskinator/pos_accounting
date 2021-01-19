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
			<h3>Reports</h3>
			<div class="row">
				<div class="col-md-4">
					<select id='process_id' class='form-control'>
						<option></option>
					
					<?php  
						if($myProcesses){
							dump($myProcesses);
							foreach($myProcesses as $p){
								?>
								<option value='<?php echo $p->id?>'><?php echo $p->name?></option>
								<?php
							}
						}
					?>
					</select>
				</div>
				<div class="col-md-4">
					<div id="stepholder">

					</div>
				</div>
				<div class="col-md-4"></div>
			</div>

			<hr>

			<input type="hidden" id="hiddenpage" />
			<div id="holder"></div>
		</div>
	</div>

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

	<script type="text/javascript">
	$(function(){
		getPage(0)
		function getPage(p,process_id,step_id){
			$.ajax({
				url: '../ajax/ajax_paging_mon.php',
				type:'post',
				data:{page:p,functionName:'monitoringPaginate',cid: <?php echo $user->data()->company_id; ?>,process_id:process_id,step_id:step_id},
				success: function(data){
					$('#holder').empty();
					$('#holder').append(data);
				}
			});
		}
		$('body').on('click','.paging',function(e){
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				// branch , Terminal,
				var process_id = $("#process_id").val();
				var step_id = $("#step_id").val();	
				getPage(page,process_id,step_id);
		});

		$("body").on('change','#step_id',function(){			
				var process_id = $("#process_id").val();
				var step_id = $("#step_id").val();	
				getPage(0,process_id,step_id);
		});

		$("#process_id").select2({
				placeholder: 'Choose Process Request',
				allowClear: true
		});
		$("#process_id").change(function(){
			var pid = $(this).val();
			if(pid){
				$.ajax({
					url: '../ajax/ajax_monitoring_reports.php',
					type: 'POST',
					data: {process_id:pid,functionName:'getSteps'},
					success: function(data){

						$('#stepholder').html(data);
						$("#step_id").select2({
								placeholder: 'Choose Step',
								allowClear: true
						});
					}
				});
			} else {
					$('#stepholder').html('');
			}

				var process_id = $("#process_id").val();
				var step_id = $("#step_id").val();	
				getPage(0,process_id,step_id);

		});

			$('body').on('click','.showData',function(){
				// get data via ajax

				var mon_id = $(this).attr('data-mon-id');
				$('#myModalLabel').html("");
				$.ajax({
					url : '../ajax/ajax_showData.php',
					type: 'POST',
					data: {mon_id:mon_id},
					success: function(data){
						$("#mbody").html(data);
					}
				});
				$('#myModal').modal('show');
			});
	});
		


	</script>


<?php require_once '../includes/monitoring/page_tail.php'; ?>