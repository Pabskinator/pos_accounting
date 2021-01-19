<?php
	// $user have all the properties and method of the current user
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('body_measure')) {
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
				Body Measurements
			</h1>

		</div>
		<form action="" id='formMeasurement'>
		<div class="row">
			<div class="col-md-7">
				<div class="panel panel-default">
					<div class="panel-body">
						<img style='width:90%;margin-top:20px;' src="../css/img/body-tape-measurements.jpg" alt="body measurements">

					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-body">
						<h4>Date</h4>
						<input type="text" class='form-control' id='dt_date' name='dt_date' placeholder='Enter Date' value="<?php echo date('m/d/Y'); ?>"> <br>
						<h4>Name</h4>
						<input type="text" class='form-control' id='member_id' name='member_id' placeholder='Search Member'> <br>
						<h4>Height and Weight</h4>
						<div class="form-group">
							<label for="">1. Height</label>
							<div class="row">
								<div class="col-md-6">
									<strong>Feet:</strong>
									<input type="text" class='form-control' id='height_feet' name='height_feet'>
								</div>
								<div class="col-md-6">
									<strong>Inches:</strong>
									<input type="text" class='form-control'  id='height_inches' name='height_inches'>
								</div>
							</div>

						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-md-6">
									<label for="">2. Weight (lbs)</label>
									<input type="text" class='form-control' id='weight'  name='weight'>
								</div>
							</div>

						</div>
						</div>
					</div>
			</div>
			<div class="col-md-5">
				<div class="panel panel-default">
					<div class="panel-body">
						<h4>Measurements are in Inches</h4>
						<div class="form-group">
							<label for="">1. Chest</label>
							<input type="text" class='form-control' id='txt_chest' name='txt_chest' >
						</div>
						<div class="form-group">
							<label for="">2. Left Arm</label>
							<input type="text" class='form-control' id='txt_l_upperarm' name='txt_l_upperarm' >
						</div>
						<div class="form-group">
							<label for="">3. Right Arm</label>
							<input type="text" class='form-control' id='txt_r_upperarm'  name='txt_r_upperarm'>
						</div>
						<div class="form-group">
							<label for="">4. Waist</label>
							<input type="text" class='form-control' id='txt_waist'  name='txt_waist' >
						</div>
						<div class="form-group">
							<label for="">5. Abdomen</label>
							<input type="text" class='form-control' id='txt_abdomen'  name='txt_abdomen'>
						</div>
						<div class="form-group">
							<label for="">6. Hips</label>
							<input type="text" class='form-control' id='txt_hips'  name='txt_hips' >
						</div>
						<div class="form-group">
							<label for="">7. Left Thigh</label>
							<input type="text" class='form-control' id='txt_l_mid_thigh'   name='txt_l_mid_thigh'>
						</div>
						<div class="form-group">
							<label for="">8. Right Thigh</label>
							<input type="text" class='form-control' id='txt_r_mid_thigh'  name='txt_r_mid_thigh'>
						</div>
						<div class="form-group">
							<label for="">9. Left Calf</label>
							<input type="text" class='form-control' id='txt_l_calf'  name='txt_l_calf'>
						</div>
						<div class="form-group">
							<label for="">10. Right Calf</label>
							<input type="text" class='form-control' id='txt_r_calf'  name='txt_r_calf'>
						</div>
						<div class="form-group">


						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-body">
				<br>
				<p class='text-danger text-center'>*Click submit when you're done.</p>
				<div class="row">
					<div class="col-md-12">

						<div class="form-group text-center">
							<button  style='position:fixed;top:90%;right:5px;opacity:0.8;border-radius:20px;' class='btn btn-success' id='btnSubmit'>Submit</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		</form>
	</div>
	<!-- end page content wrapper-->
	<script>

		$(document).ready(function() {
			$('#dt_date').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_date').datepicker('hide');
			});
			$('body').on('click','#btnSubmit',function(e){
				e.preventDefault();
				var form = $('#formMeasurement').serializeArray();
				var con = $(this);
				button_action.start_loading(con);
				$.ajax({
				    url:'../ajax/ajax_member_service.php',
				    type:'POST',
				    data: {functionName:'saveMeasurements',form:JSON.stringify(form)},
				    success: function(data){
				        if(data == 1){
					        tempToast('error','Enter member name and date','Warning');
					        button_action.end_loading(con);
				        } else  {
					        alertify.alert(data,function(){
						        button_action.end_loading(con);
						        location.reload();
					        });
				        }
				    },
				    error:function(){

				    }
				});
			});

			$("#member_id").select2({
				placeholder: 'Search member' ,
				allowClear: true,

				minimumInputLength: 2,
				ajax: {
					url: '../ajax/ajax_json.php',
					dataType: 'json',
					type: "POST",
					quietMillis: 50,
					data: function (term) {
						return {
							q: term,
							functionName:'members'
						};
					},
					results: function (data) {
						return {
							results: $.map(data, function (item) {
								return {
									text: item.lastname + ", " + item.firstname + " " + item.middlename,
									slug: item.lastname + ", " + item.firstname + " " + item.middlename,
									id: item.id
								}
							})
						};
					}
				}
			});

		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>