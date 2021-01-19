<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('branch')){
		// redirect to denied page
		Redirect::to(1);
	}


	$branch_group = new Branch_group();
	$groups = $branch_group->get_active('branch_groups',['1','=','1']);


?>



	<!-- Page content -->
	<div id="page-content-wrapper">


	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Branch Group
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
				<div class="row">
					<div class="col-md-6">
						<a href="branch_group_pricelist.php" class='btn btn-default'>Branch Group Pricelist</a>
					</div>
					<div class="col-md-6 text-right">
						<a href="add-branch-group.php" class='btn btn-default'>Add Record</a>
					</div>
				</div>
				<br>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">List</div>
					<div class="panel-body">
						<?php
							if($groups){
							?>
							<table class='table table-bordered'>
								<thead>
									<tr>
										<th>Name</th>
										<th>Created</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
								<?php
									foreach($groups as $group){
								?>
									<tr>
										<td style='border-top:1px solid #ccc;'><?php echo $group->name; ?></td>
										<td style='border-top:1px solid #ccc;'><?php echo $group->created; ?></td>
										<td style='border-top:1px solid #ccc;'>
											<a class='btn btn-primary' href='add-branch-group.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt',$group->id);?>' title='Edit Branch'><span class='glyphicon glyphicon-pencil'></span></a>
											<a href='#' class='btn btn-primary deleteBranchGroup' id="<?php echo Encryption::encrypt_decrypt('encrypt',$group->id);?>" title='Delete Branch'><span class='glyphicon glyphicon-remove'></span></a>
										</td>
									</tr>
								<?php } ?>
								</tbody>
							</table>
							<?php

						}
					?>
					</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function(){

			$('body').on('click','.deleteBranchGroup',function(){
				if(confirm("Are you sure you want to delete this record? \n ")){
					var id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php',{id:id,table:'branch_groups'},function(data){
						if(data == "true"){
							location.reload();
						}
					});
				}
			});

		});




	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>