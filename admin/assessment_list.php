<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('branch')){
		// redirect to denied page
		Redirect::to(1);
	}

	// get all branch base on company
	$assessment = new Assessment_list();
	$assessments = $assessment->getAssessment();

?>



	<!-- Page content -->
	<div id="page-content-wrapper">




	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Assessment Information
			</h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')){
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('flash')."</div><br>";
			}
		?>
		<div class="row">
			<div class="col-md-12">
				<?php include_once "includes/assessment_nav.php" ?>
				<?php
					if ($assessments){
				?>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">List</div>
					<div class="panel-body">
						<div id="no-more-tables">
							<table class='table' id='tblbrands'>
								<thead>
								<tr>
									<TH>Discipline</TH>
									<TH>Name</TH>
									<TH>Group</TH>
									<TH>Data Created</TH>
									<TH>Actions</TH>
								</tr>
								</thead>
								<tbody>
								<?php
									foreach($assessments as $b){
										?>
										<tr>
											<td data-title='Name'><?php echo escape($b->disc_name); ?></td>
											<td data-title='Description'><?php echo escape($b->name); ?></td>
											<td data-title='Group'><?php echo escape($b->grp); ?></td>
											<td data-title='Created'><?php echo escape(date('m/d/Y H:i:s A',$b->created)); ?></td>
											<td data-title='Action'>
												<a class='btn btn-primary' href='add_assessment.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt',$b->id);?>' title='Edit'><span class='glyphicon glyphicon-pencil'></span></a>
												<a href='#' class='btn btn-primary deleteAssessment' id="<?php echo Encryption::encrypt_decrypt('encrypt',$b->id);?>" title='Delete'><span class='glyphicon glyphicon-remove'></span></a>
											</td>

										</tr>
										<?php
									}
								?>
								</tbody>
							</table>
						</div>
					</div>
					<?php
						} else {
						?>
						<div class='alert alert-info'>There is no current item at the moment.</div>
						<?php
					}
					?>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function(){
			$(".deleteAssessment").click(function(){
				if(confirm("Are you sure you want to delete this record? \n ")){
					var id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php',{id:id,table:'assessment_list'},function(data){
						if(data == "true"){
							location.reload();
						}
					});
				}
			});
		});


		$('#tblbrands').dataTable({
			iDisplayLength: 50
		});

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>