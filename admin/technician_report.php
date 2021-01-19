<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('item_service_r')){
		// redirect to denied page
		Redirect::to(1);
	}

	// get all branch base on company
	$tech = new Technician();
	$technicians = $tech->get_active('technicians',array('company_id' ,'=',$user->data()->company_id));

?>



	<!-- Page content -->
	<div id="page-content-wrapper">




	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Manage Technician
			</h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')){
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('flash')."</div>";
			}
		?>
		<?php include 'includes/service_nav.php'; ?>
		<div class="row">
			<div class="col-md-12">

			
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">
						<div class="row">
							<div class="col-md-6">
								Technicians log
							</div>
							<div class="col-md-6 text-right">
								<button id='btnDownload' class='btn btn-default'><i class='fa fa-download'></i></button>
							</div>
						</div>

					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<?php
										if ($technicians){
											?>
											<select name="technician_id" id="technician_id" class='form-control'>

												<?php
													foreach($technicians as $b){
														?>
														<option value="<?php echo $b->id; ?>"><?php echo $b->name; ?></option>
														<?php
													}
												?>
											</select>
											<?php
										}
									?>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select name="status" id="status" class='form-control'>
										<option value="">Select Status</option>
										<option value="1">Pending</option>
										<option value="2">For evaluation</option>
										<option value="3">For Payment/Credit</option>
										<option value="4">Processed</option>
									</select>
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

	<div class="modal fade" id="myModalTimelog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id='ttitle'>Time log</h4>
				</div>
				<div class="modal-body" id='tbody'>
					<input type="hidden" id='request_id'>
					<input type="hidden" id='request_fullname'>
					<div>
						<strong>Time In</strong>
					</div>
					<div class="row">
						<div class="col-md-6">
							<input type="text" class='form-control' id='time_in_date' placeholder='mm/dd/yyyy'>
							<span class='help-block'>Enter date</span>
						</div>
						<div class="col-md-6">
							<input type="time" class='form-control' id='time_in_hour' placeholder='00:00 '>
							<span class='help-block'>Enter time</span>
						</div>
					</div>
					<div>
						<strong>Time Out</strong>
					</div>
					<div class="row">
						<div class="col-md-6">
							<input type="text" class='form-control' id='time_out_date' placeholder='mm/dd/yyyy'>
							<span class='help-block'>Enter date</span>
						</div>
						<div class="col-md-6">
							<input type="time" class='form-control' id='time_out_hour' placeholder='00:00 '>
							<span class='help-block'>Enter time</span>
						</div>
					</div>
					<div>
						<strong>Remark</strong>
					</div>
					<div class="row">
						<div class="col-md-12">
							<input type="text" class='form-control' id='remarks' placeholder='Remarks'>
						</div>
					</div>
					<br>
					<div>

						<button class='btn btn-default' id='btnAddTimelog'>ADD</button>
					</div>
					<br>
					<div class="panel panel-default">
						<div class="panel-body">
							<div id='timelog_container'></div>
						</div>
					</div>

				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->


	<script>

		$(document).ready(function(){
			$('body').on('click','.btnDetails',function(){
				var id = $(this).attr('data-id');
				if(id){
					$('#myModal').modal('show');
					getDetails(id);
				}
			});

			$('#time_in_date').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#time_in_date').datepicker('hide');
				$('#time_out_date').val($('#time_in_date').val());
			});

			$('#time_out_date').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#time_out_date').datepicker('hide');
			});

			$('body').on('change','#technician_id,#status',function(){
				getPage(0);
			});
			function getDetails(id){
				$('#myModal').modal('show');
				var terminal_id = localStorage['terminal_id'];
				$.ajax({
					url:'../ajax/ajax_query2.php',
					type:'POST',
					beforeSend:function(){
						$('#mbody').html('Loading...');
					},
					data: {functionName:'itemServiceDetails',id:id,terminal_id:terminal_id,isLog:1},
					success: function(data){
						$('#mbody').html(data);
					},
					error:function(){

						$('.myModal').modal('hide');
					}
				})
			}
			$('body').on('click','.paging',function(e){
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});

			$('body').on('click','#btnDownload',function(){

				var technician_id = $('#technician_id').val();
				var status = $('#status').val();

				window.open(
					'excel_downloader.php?downloadName=techLog&technician_id='+technician_id+'&status='+status,
					'_blank' //
				);
			});

			getPage(0);

			function getPage(p){

				var technician_id = $('#technician_id').val();
				var status = $('#status').val();

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,functionName:'technicianLog',status:status, technician_id:technician_id},
					success: function(data){
						$('#holder').html(data);
					}
				});
			}

			$('body').on('click','.btnTimelogShow',function(){
				var id = $(this).attr('data-id');
				$('#request_id').val(id);
				$('#request_fullname').val($('#technician_id option:selected').text());
				getTimelog();
				$('#myModalTimelog').modal('show');

			});

			$('body').on('click','#btnAddTimelog',function(){
				var time_in_date =  $('#time_in_date').val();
				var time_in_hour =  $('#time_in_hour').val();

				var time_out_date =  $('#time_out_date').val();
				var time_out_hour =  $('#time_out_hour').val();

				var remarks = $('#remarks').val();

				var id = $('#request_id').val();
				var fullname = $('#request_fullname').val();

				if(time_in_date && time_in_hour && time_out_date && time_out_hour) {


					$.ajax({
						url: '../ajax/ajax_timelog.php',
						type: 'POST',
						data: {
							functionName: 'addTimelog',
							fullname: fullname,
							id: id,
							remarks: remarks,
							time_in_date: time_in_date,
							time_in_hour: time_in_hour,
							time_out_date: time_out_date,
							time_out_hour: time_out_hour
						},
						success: function(data) {
							tempToast('info', data, 'Info');
							$('#time_in_date').val('');
							$('#time_in_hour').val('');
							$('#time_out_date').val('');
							$('#time_out_hour').val('');
							$('#remarks').val('');

							getTimelog();
						},
						error: function() {

						}
					});
				} else {
					tempToast('error','Please complete the form','Error');
				}
			});

			function getTimelog(){
				var id = $('#request_id').val();
				$.ajax({
				    url:'../ajax/ajax_timelog.php',
				    type:'POST',
				    data: {functionName:'getTimelog',id:id},
				    success: function(data){
					    $('#timelog_container').html(data);
				    },
				    error:function(){

				    }
				});

			}

		});
	
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>