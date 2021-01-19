<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('user')){
		// redirect to denied page
		Redirect::to(1);
	}

	// get all users base on company
	$listPosition = new Position();
	$data = $listPosition->get_active('positions',array('company_id' ,'=',$user->data()->company_id));

?>

	<!-- Page content -->
<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Manage Positions
			</h1>
		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('positionflash')){
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('positionflash')."</div>";
			}
		?>
		<div class="row">
			<div class="col-md-12">

				<div class="btn-group hidden-xs" role="group" aria-label="..." style='margin-bottom:10px;'>
					<?php 	if($user->hasPermission('position_m')){ ?>
					<a class='btn btn-default' href='addposition.php' title='Add Position'>
						<span class='glyphicon glyphicon-plus'></span>
						<span class='hidden-xs'>Add Position</span>
					</a>
					<a class='btn btn-default' href='approval_auth.php' title='Approval Auth'>
						<span class='glyphicon glyphicon-plus'></span>
						<span class='hidden-xs'>Approval Auth</span>
					</a>
					<?php } ?>
				</div>
				<div class='visible-xs'>
					<button id='btnShowNavigationContainer' class='btn btn-default'><i class='fa fa-bars'></i></button>
					<div class='card-nav card-nav-2' id='secondNavigationContainer' style='display:none;'>
						<button class='btn btn-default btn-sm' id='btnRemoveSecondNavigationContainer'><i class='fa fa-remove'></i></button>
						<?php 	if($user->hasPermission('position_m')){ ?>
							<a class='btn btn-default btn-second-nav' href='addposition.php' title='Add Position'>
								<span class='glyphicon glyphicon-plus'></span>
								<span class='title'>Add Position</span>
							</a>
							<a class='btn btn-default btn-second-nav' href='approval_auth.php' title='Approval Auth'>
								<span class='glyphicon glyphicon-plus'></span>
								<span class='title'>Approval Auth</span>
							</a>
						<?php } ?>
					</div>
				</div>

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Positions</div>
					<div class="panel-body">
						<div id="no-more-tables">
						<table class='table table-hover' id='tblWithBorder'>
							<thead>
							<tr>
								<TH>Position Name</TH>
								<TH>Data Created</TH>
						<?php if($user->hasPermission('position_m')){ ?>
								<TH>Actions</TH>
						<?php } ?>
							</tr>
							</thead>
							<tbody>
							<?php
								foreach($data as $p) {
									?>
									<tr>
										<td data-title='Postion'><?php echo  $p->position; ?></td>
										<td data-title='Privilege'><?php echo date('m/d/Y', $p->created); ?></td>
										<?php 	if($user->hasPermission('position_m')){ ?>
										<td data-title='Action'><a class='btn btn-primary' href='addposition.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt',$p->id);?>' title='Edit Position'><span class='glyphicon glyphicon-pencil'></span></a>
										</td>
										<?php } ?>
									</tr>
									<?php
								}

							?>
							</tbody>
					</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<script>
		$(document).ready(function(){
			$('#tblWithBorder').dataTable({
				iDisplayLength: 50
			});
		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>