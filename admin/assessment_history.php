<?php

	// $user have all the properties and method of the current user
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('wo_mod')) {
		// redirect to denied page
		Redirect::to(1);
	}
	$member_id = 0;
	if(Input::exists()){
		$member_id = Input::get('member_id');
	}
	$assessment = new Assessment_list();
	$list = $assessment->getHistory($member_id);


?>
	<div id="page-content-wrapper">


		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					Assessment Record history
				</h1>

			</div>
			<?php include_once "includes/assessment_nav.php" ?>
			<div class="panel panel-primary">
				<div class="panel-heading">
					History
				</div>

				<div class="panel-body">
					<form action="" method="POST">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" id='member_id' name='member_id' class='form-control'>
								</div>
							</div>

							<div class="col-md-3">
								<div class="form-group">
									<input type='submit' class='btn btn-default' value='Submit' id='btnSubmit' name='btnSubmit'>
								</div>
							</div>
						</div>
					</form>
					<?php
						if($list){
							echo "<table class='table'>";
							echo "<thead><tr><th>Created At</th><th>Member</th><th>Discipline</th><th>Coach</th><th>Session Number</th><th>Goal</th><th>Details</th></tr></thead>";
							echo "<tbody>";

							foreach($list as $l){
								echo "<tr>";
								echo "<td>" . date('m/d/Y H:i:s A',$l->created) . "</td>";
								echo "<td>$l->member_name</td>";
								echo "<td>$l->disc_name</td>";
								echo "<td>$l->coach_name</td>";
								echo "<td>$l->session_number</td>";
								echo "<td>$l->goals</td>";
								echo "<td><button class='btn btn-default btn-sm btnDetails' data-id='$l->id', data-disc_id='$l->disc_id'>Details</button></td>";
								echo "</tr>";
							}

							echo "</tbody>";
							echo "</table>";

							if($member_id){
								echo "<h4>Total number of assessment: <span class='text-danger'>".count($list)."</span></h4>";
							}
						} else {
							echo "<div class='alert alert-danger'>No record found.</div>";
						}


					?>

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
	<script>
		$(function(){

			var mem_select2 = $('#member_id');




			mem_select2.select2({
				placeholder: 'Search client',
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
			$('body').on('click','.btnDetails',function(){
				var con = $(this);
				var id = con.attr('data-id');

				$('#myModal').modal('show');
				$('#mbody').html('Loading...');
				$.ajax({
				    url:'../ajax/ajax_member_service.php',
				    type:'POST',
				    data: {functionName:'getDetails',id:id},
				    success: function(data){
						$('#mbody').html(data);

				    },
				    error:function(){

				    }
				});
			});



		});
	</script>
<?php	require_once '../includes/admin/page_tail2.php';