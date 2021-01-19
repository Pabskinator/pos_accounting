<?php
	// $user have all the properties and method of the current user
	require_once '../includes/admin/page_head2.php';

	$service_type = new Service_type();
	$service_types = $service_type->get_active('service_types',['1','=','1']);
	if($service_types){
		$secondary[0]="No service type";
		foreach($service_types as $st2){
			$secondary[$st2->id]=$st2->name;
		}
	}

	$branch_id = 0;
	$date_from = 0;
	$date_to = 0;

	if(Input::exists()){
		$branch_id = Input::get('branch_id');
		$date_from = Input::get('date_from');
		$date_to = Input::get('date_to');
	}
	$service_request_item = new Service_request_item();
	$list = $service_request_item->getSummaryService($date_from,$date_to,$branch_id);

	$branch = new Branch();
	$branches = $branch->get_active('branches',['1','=','1']);



?>

	<!-- Page content -->
	<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="page-content inset">
			<div class="content-header">
				<h1>Summary</h1>
			</div>

			<?php include 'includes/service_nav.php'; ?>
			<div class="panel panel-primary">
				<!-- Default panel contents -->
				<div class="panel-heading">List</div>
				<div class="panel-body">

					<div id=''>
						<form action="" method="POST">
							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" value='<?php echo (Input::get('date_from')) ? Input::get('date_from') : ''; ?>' class='form-control' id='date_from' name='date_from' placeholder="Date From">
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" value='<?php echo (Input::get('date_to')) ? Input::get('date_to') : ''; ?>' class='form-control' id='date_to' name='date_to' placeholder="Date To">
									</div>
								</div>
								<div class="col-md-3">
									<select class='form-control' name="branch_id" id="branch_id">
										<option value=""></option>
										<?php foreach($branches as $b){
											$selected = "";
											if(Input::get('branch_id') && Input::get('branch_id') == $b->id){
												$selected = "selected";
											}
											?>
											<option <?php echo $selected; ?> value="<?php echo $b->id; ?>"><?php echo $b->name; ?></option>
											<?php

										}
										?>
									</select>
								</div>
								<div class="col-md-3">
									<input type="submit" value='Submit' name='btnSubmit' class='btn btn-default'>
								</div>
							</div>
						</form>


						<div class="row">
							<?php
								if($list){
									foreach($list as $l){
										?>
										<div class="col-md-4">
											<div class="panel panel-default">
												<div class="panel-heading">
													<?php echo $secondary[$l->service_type_id]; ?> <i data-service_type_id='<?php echo $l->service_type_id; ?>' class='fa fa-download downloadDetails'></i>
												</div>
												<div class="panel-body">
													<h2 class='showDetails' style='cursor: pointer;' data-service_type_id='<?php echo $l->service_type_id; ?>' ><?php echo $l->cnt; ?></h2>
												</div>
											</div>
										</div>
										<?php
									}
								} else {
									?>
									<div class=" container alert alert-info">No record</div>
									<?php
								}

							?>
						</div>
						<br>
						<div id="con">
						</div>


					</div>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->

	<script>
		$(function() {
			$('#date_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#date_from').datepicker('hide');
			});

			$('#date_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#date_to').datepicker('hide');
			});

			$('#branch_id').select2({placeholder: 'Select branch' ,allowClear: true});

			$('body').on('click','.showDetails',function(){
				var con = $(this);
				var dt1 = $('#date_from').val();
				var dt2 = $('#date_to').val();
				var branch_id = $('#branch_id').val();

				$('html, body').animate({
					scrollTop: $("#con").offset().top
				}, 1000);
				var service_type_id = con.attr('data-service_type_id');

				$('#con').html('Loading...');
				$.ajax({
					url:'../ajax/ajax_service_item.php',
					type:'POST',
					data: {functionName:'getService',status:0,service_type_id:service_type_id,service_type_id,dt1:dt1,dt2:dt2,branch_id:branch_id},
					success: function(data){
						$('#con').html(data);
					},
					error:function(){

					}
				});

			});
			$('body').on('click','.downloadDetails',function(){
				var con = $(this);
				var dt1 = $('#date_from').val();
				var dt2 = $('#date_to').val();
				var branch_id = $('#branch_id').val();
				var service_type_id = con.attr('data-service_type_id');

				window.open(
					'../ajax/ajax_service_item.php?functionName=getService&is_dl=1&dt1='+dt1+'&dt2='+dt2+'&branch_id='+branch_id+'&service_type_id='+service_type_id,
					'_blank' // <- This is what makes it open in a new window.
				);
			});


		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>