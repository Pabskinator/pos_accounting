<!-- Page content -->
<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Manage Branches
			</h1>
		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('branchflash')){
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('branchflash')."</div>";
			}
		?>
		<div class="row">
			<div class="col-md-12">
				<?php if($user->hasPermission('branch_m')) {
					include "includes/branch_nav.php";
				 } ?>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Branches</div>
					<div class="panel-body">
						<?php
							if ($branches){
								?>

								<div id="no-more-tables">
									<table class='table' id='tblBranches'>
										<thead>
										<tr>
											<TH>ID</TH>
											<TH>Branch</TH>
											<TH>Description</TH>
											<TH>Data Created</TH>
											<?php if($user->hasPermission('branch_m')) { ?>
												<TH>Actions</TH>
											<?php } ?>
										</tr>
										</thead>
										<tbody>
										<?php
											foreach($branches as $b){
												?>
												<tr>
													<td data-title='Branch'><?php echo escape($b->id); ?></td>
													<td data-title='Branch'><?php echo escape($b->name); ?></td>
													<td data-title='Description'><?php echo escape($b->description); ?></td>
													<td data-title='Created'><?php echo escape(date('m/d/Y H:i:s A',$b->created)); ?></td>
													<?php if($user->hasPermission('branch_m')) { ?>
														<td data-title='Action'>
															<a class='btn btn-primary' href='addbranch.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt',$b->id);?>' title='Edit Branch'><span class='glyphicon glyphicon-pencil'></span></a>
															<a href='#' class='btn btn-primary deleteBranch' id="<?php echo Encryption::encrypt_decrypt('encrypt',$b->id);?>" title='Delete Branch'><span class='glyphicon glyphicon-remove'></span></a>
														</td>
													<?php } ?>
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
			$(".deleteBranch").click(function(){
				if(confirm("All terminals under this branch will be deleted too. \n Are you sure you want to delete this record? \n ")){
					id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php',{id:id,table:'branches'},function(data){
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