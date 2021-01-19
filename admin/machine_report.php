<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('sales')){
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
				MACHINES
			</h1>

		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-primary">
					<!-- Default panel contents -->

					<div class="panel-heading">
						<div class="text-right">

						</div>
					</div>

					<div class="panel-body">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' placeholder='From' id='dt_from'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' placeholder='To' id='dt_to'>
								</div>
							</div>
							<div class='col-md-3'>
								<div class="form-group">
									<select name="sales_type" id="sales_type" class='form-control'>
										<option value="">Select sales type</option>
										<?php
											$sales_type = new Sales_type();
											$sales_types = $sales_type->get_active('salestypes',array('company_id','=',$user->data()->company_id));
											foreach($sales_types as $st){

												echo  "<option value='$st->id'>$st->name</option>";
											}
											echo  "<option value='-1'>No Agent</option>";
										?>
									</select>

								</div>
							</div>

						</div>
						<div id="con"></div>
					</div>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function(){

			getRecord(0);

			function getRecord(t){
				var dt_from = $('#dt_from').val();
				var dt_to = $('#dt_to').val();
				var sales_type = $('#sales_type').val();
				$.ajax({
					url:'../ajax/ajax_sales_query.php',
					type:'POST',
					data: {functionName:'machineReport',t:t,dt_from:dt_from,dt_to:dt_to,sales_type:sales_type},
					success: function(data){
						$('#con').html(data);
					},
					error:function(){

					}
				});
			}

			$('body').on('change','#sales_type',function(){
				getRecord(0);
			});

			$('#dt_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_from').datepicker('hide');
				getRecord(0);
			});

			$('#dt_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_to').datepicker('hide');
				getRecord(0);
			});

			$('body').on('click','#btnDl',function(){
				downloadRecord();
			});

			function downloadRecord(){

				var dt_from = $('#dt_from').val();
				var dt_to = $('#dt_to').val();
				var sales_type = $('#sales_type').val();
				window.open(
					'../ajax/ajax_sales_query.php?functionName=getCustomRecord&dt_from='+dt_from+'&dt_to='+dt_to+'&sales_type='+sales_type+'&t=1',
					'_blank' // <- This is what makes it open in a new window.
				);

			}


		});



	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>