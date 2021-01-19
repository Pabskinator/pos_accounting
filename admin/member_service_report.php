<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('member')) {
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
			<span id="menu-toggle" class='glyphicon glyphicon-list'></span>Class/Discipline List</h1>

		</div>


		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">

					</div>
					<div class="panel-body">
						<div class="row">
							<div class='col-md-3'>
								<input type="text" class='form-control' id='txtSearch' placeholder='Search...'>
							</div>
							<div class='col-md-3'>
								<select name="service_id" id="service_id" class='form-control'>
									<option value="">Select Class/Discipline</option>
									<?php
										$os = new Offered_service();
										$oses = $os->get_active('offered_services',[1,'=', 1]);
										foreach($oses as $j){
											echo "<option value='$j->id'>$j->name</option>";
										}
									?>
								</select>
							</div>
							<div class='col-md-3'>
								<input style='display:none;' type="text" id='item_id' class='selectitem' >
							</div>
							<div class='col-md-3'></div>
						</div>
						<br>
						<div id="con"></div>
					</div>


				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function() {
			getReport();
			$('body').on('change','#service_id,#item_id',function(){
				getReport();
			});
			$('body').on('keyup','#txtSearch',function(){
				getReport();
			});
			function getReport(){

				var service_id = $('#service_id').val();
				var search = $('#txtSearch').val();
				var item_id = $('#item_id').val();

				$.ajax({
					url:'../ajax/ajax_member_service.php',
					type:'POST',
					data: {functionName:'serviceMemberReport',service_id:service_id,item_id:item_id,search:search},
					success: function(data){
						$('#con').html(data);

					},
					error:function(){

					}
				});
			}

		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>