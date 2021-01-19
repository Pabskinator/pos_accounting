<?php
// $user have all the properties and method of the current user
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require_once '../includes/monitoring/page_head.php';
	if(!$user->hasPermission('branch')){
		// redirect to denied page
		//Redirect::to(1);
	}


	$process = new Process();
	$processes = $process->get_active('processes',array('company_id' ,'=', $user->data()->company_id));
	
?>

	<!-- Sidebar -->
<?php include_once '../includes/monitoring/sidebar.php';?>

	<!-- Page content -->
	<div id="page-content-wrapper">




		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					Manage Process
				</h1>

			</div>
			<?php
				// get flash message if add or edited successfully
				if(Session::exists('processflash')){
					echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('processflash')."</div>";
				}
			?>
			<div class="row">
				<div class="col-md-12">
					<h3>
						<a href='addprocess.php'>
							<span class='glyphicon glyphicon-plus'></span>
							Add Process
						</a>
					</h3>
					<?php

						if ($processes){
							?>
					<div class="panel panel-primary">
						<!-- Default panel contents -->
						<div class="panel-heading">Process</div>
						<div class="panel-body">
					<table class='table' id='tblBranches'>
						<thead>
						<tr>
							<TH>Process Name</TH>
							<TH>Description</TH>
							<TH>Number of Steps</TH>
							<TH>Data Created</TH>
							<TH>Actions</TH>
						</tr>
						</thead>
						<tbody>
						<?php
					
						foreach($processes as $p){
							?>
							<tr>
								<td><?php echo escape($p->name); ?></td>
								<td><?php echo escape($p->description); ?></td>
								<td><?php echo escape($p->steps); ?></td>
								<td><?php echo escape(date('m/d/Y H:i:s A',$p->created)); ?></td>
								<td>
									<a href='addprocess.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt',$p->id);?>' title='Edit Process'><span class='glyphicon glyphicon-pencil' style='margin-right:5px;'></span></a>
									<a href='#' class='deleteProcess' id="<?php echo Encryption::encrypt_decrypt('encrypt',$p->id);?>" title='Delete Process'><span class='glyphicon glyphicon-remove'></span></a>
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
	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function(){
		$(".deleteProcess").click(function(){
				if(confirm("Are you sure you want to delete this record? \n ")){
					id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php',{id:id,table:'processes'},function(data){
						if(data == "true"){
						location.reload();
						}
					});
				}
		});
		});


		//$('#tblBranches').dataTable({
		//	iDisplayLength: 50
		//});

	</script>
<?php require_once '../includes/monitoring/page_tail.php'; ?>