<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('deduction_summary')){
		// redirect to denied page
		Redirect::to(1);
	}

	$branch = new Branch();
	$branches = $branch->branchJSON($user->data()->company_id);


?>



	<!-- Page content -->
	<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<div class="row">
				<div class="col-md-12">
					<h1>
						<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
						Deduction Summary
					</h1>
				</div>

			</div>
		</div>

		<div class="panel panel-primary">
			<div class="panel-heading">
			</div>
			<div class="panel-body">

				<div id="con1">
					<div class="row">

						<div class="col-md-3">
							<select name="branch_id" class='form-control' id="branch_id">
								<option value=""></option>
								<?php
									foreach($branches as $b){
										echo "<option value='$b->id'>$b->name</option>";
									}
								?>
							</select>
						</div>
						<div class="col-md-3">
							<input type="button" id='btnSubmit' class='btn btn-primary' value='Submit'>
						</div>

						<div class="col-md-3">

						</div>
					</div>
					<br>
					<div id="holder"></div>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->


	<script>

		$(document).ready(function(){

			function getSummary(){
				var branch_id = $('#branch_id').val();
				$.ajax({
				    url:'../ajax/ajax_sales_query.php',
				    type:'POST',
				    data: {functionName:'aging',branch_id:branch_id},
				    success: function(data){
					    $('#holder').html(data);
				    },
				    error:function(){

				    }
				});
			}

			$('body').on('click','#btnSubmit',function(){
				getSummary();
			});

		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>