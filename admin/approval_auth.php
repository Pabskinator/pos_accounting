<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head
	require_once '../includes/admin/page_head2.php';
	if(false) {
		// redirect to denied page
		Redirect::to(1);
	}

	$approval_auth = new Approval_auth();

	$auths = $approval_auth->get_active('approval_auths',array('company_id','=',$user->data()->company_id))
?>


	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Approval Permission</h1>

		</div> <?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}
		?>
		<div class="text-right">
			<a class='btn btn-default btn-sm' href='add_approval_auth.php'><i class='fa fa-plus'></i> Add</a>
		</div>
		<br>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Auth</div>
					<div class="panel-body">
						<div id="no-more-tables">

							<?php
								if($auths){
									?>
									<table class="table">
										<thead>
										<tr><th>User</th><th>Auth</th><th></th></tr>
										</thead>
										<tbody>
									<?php

										foreach($auths as $auth){
											$auth_user = new User($auth->user_id);
											$manage_branch = new Branch();
											$m_branch = $manage_branch->branchIn( $auth->ref_values);
											$handled = "";
											if($m_branch){
												foreach($m_branch as $b){
													$handled .= "<span class='label label-default'>$b->name</span> ";
												}
											}
										?>
												<tr>
													<td data-title='User'><?php echo capitalize($auth_user->data()->lastname . ", " . $auth_user->data()->firstname); ?></td>
													<td data-title='Values'><?php echo $handled; ?></td>
													<td data-title='Action'>
														<a class='btn btn-primary' href='add_approval_auth.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt',$auth->id);?>' title='Edit Auth'><span class='glyphicon glyphicon-pencil'></span></a>

														<a href='#' class='btn btn-primary deleteAuth' id="<?php echo Encryption::encrypt_decrypt('encrypt',$auth->id);?>" title='Delete Auth'><span class='glyphicon glyphicon-remove'></span></a>
													</td>
												</tr>
										<?php
										}
										?>
										</tbody>
									</table>
									<?php
								} else{
									echo "<div class='alert alert-info'>No record found</div>";
								}
							?>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
	<!-- end page content wrapper-->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id='mtitle'></h4>
				</div>
				<div class="modal-body" id='mbody'></div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<!-- /.modal -->
	<script>

		$(document).ready(function() {
			$(".deleteAuth").click(function(){
				if(confirm("Are you sure you want to delete this record? \n ")){
					id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php',{id:id,table:'approval_auths'},function(data){
						if(data == "true"){
							location.reload();
						}
					});
				}
			});

		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>