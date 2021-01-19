<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';


?>


	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">

			<div class="content-header">
				<h1><span id="menu-toggle" class='glyphicon glyphicon-list'></span> Claims and Refund</h1>
			</div>

			<div class="panel panel-primary">
				<div class="panel-heading">

				</div>
				<div class="panel-body">
					<div id='con1'>
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<?php
										$service_type = new Service_type();
										$service_types = $service_type->get_active('service_types',['1','=','1']);
										if($service_types){
											?>

												<select name="service_type_id" id="service_type_id" class='form-control'>
													<option value="">Select Type</option>
													<?php foreach($service_types as $st2){
														?>
														<option value="<?php echo $st2->id; ?>"><?php echo $st2->name; ?></option>
														<?php
													} ?>
												</select>

											<?php
										}
									?>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select name="is_done" id="is_done" class='form-control'>
										<option value="">Select Status</option>
										<option value="101">For Claims</option>
										<option value="102">For Refund</option>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" autocomplete="off" class='form-control' id='dt_from' placeholder='Date From'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" autocomplete="off" class='form-control' id='dt_to' placeholder='Date To'>
								</div>
							</div>
						</div>

						<div id="holder"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end page content wrapper-->

	<script>

		$(document).ready(function() {




			$('#dt_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_from').datepicker('hide');
				changeDate();
			});

			$('#dt_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_to').datepicker('hide');
				changeDate();
			});

			getList();



			var timer;

			$("#search").keyup(function(){
				var searchtxt = $("#search");
				clearTimeout(timer);
				timer = setTimeout(function() {
					if(searchtxt.val()){
						searchtxt.val(searchtxt.val().trim());
					}
					getList();
				}, 1000);

			});

			$('body').on('change','#service_type_id,#is_done',function(){
				getList();
			});

			function changeDate(){

				if($('#dt_from').val() && $('#dt_to').val()){
					getList();
				}

			}

			function getList(){
				var dt_from = $('#dt_from').val();
				var dt_to = $('#dt_to').val();
				var service_type_id = $('#service_type_id').val();
				var is_done = $('#is_done').val();

				$.ajax({
				    url:'../ajax/ajax_avision.php',
				    type:'POST',
				    data: {functionName:'getServiceMonitoring',is_done:is_done,dt_from:dt_from,dt_to:dt_to,service_type_id:service_type_id},
				    success: function(data){
					    $('#holder').html(data);
				    },
				    error:function(){

				    }
				});

			}

		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>