<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('sales')){
		// redirect to denied page
		Redirect::to(1);
	}

	// get all branch base on company
	$del = new Deduction_list();

	$deductions = $del->get_active('deduction_list',array('company_id' ,'=',$user->data()->company_id));

?>



	<!-- Page content -->
	<div id="page-content-wrapper">




	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Manage Deduction
			</h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')){
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('flash')."</div>";
			}
		?>
		<div class="row">
			<div class="col-md-12">

				<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
					<a class='btn btn-default' href='add_deduction_list.php' title='Add Deduction'>
						<span class='glyphicon glyphicon-plus'></span>
						<span class='hidden-xs'>Add Deduction</span>
					</a>
				</div>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Deduction</div>
					<div class="panel-body">
						<?php
							if ($deductions){
								?>

								<div id="no-more-tables">
									<table class='table' id='tblBranches'>
										<thead>
										<tr>
											<TH>Name</TH>
											<TH>Data Created</TH>
											<TH>Actions</TH>
										</tr>
										</thead>
										<tbody>
										<?php
											foreach($deductions as $b){
												?>
												<tr>
													<td data-title='Helper'><?php echo escape($b->name); ?></td>
													<td data-title='Created'><?php echo escape(date('m/d/Y H:i:s A',$b->created)); ?></td>
													<td data-title='Action'>
														<a class='btn btn-primary' href='add_deduction_list.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt',$b->id);?>' title='Edit Deduction'><span class='glyphicon glyphicon-pencil'></span></a>
														<a href='#' class='btn btn-primary deleteDeduction' id="<?php echo Encryption::encrypt_decrypt('encrypt',$b->id);?>" title='Delete Deduction'><span class='glyphicon glyphicon-remove'></span></a>
													</td>

												</tr>
												<?php
											}
										?>
										</tbody>
									</table>
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
		</div>
	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function(){
			$(".deleteDeduction").click(function(){
				if(confirm("Are you sure you want to delete this record? \n ")){
					id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php',{id:id,table:'deduction_list'},function(data){
						if(data == "true"){
							location.reload();
						}
					});
				}
			});
		});
		$('#tblBranches').dataTable({
			iDisplayLength: 50
		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>