<?php
	// $user have all the properties and method of the current user
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require_once '../includes/monitoring/page_head.php';
	if(!$user->hasPermission('terminal')){
		// redirect to denied page
		Redirect::to(1);
	}


	$process = new Process();
	$processes = $process->get_active('processes',array('company_id' ,'=',$user->data()->company_id));



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
					Manage Steps
				</h1>

			</div>
			<?php
				// get flash message if add or edited successfully
				if(Session::exists('stepsflash')){
					echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('stepsflash')."</div>";
				}
			?>
			<div class="row">
				<div class="col-md-12">
					<h3>
						<a href='addsteps.php'>
							<span class='glyphicon glyphicon-plus'></span>
							Add Steps
						</a>
					</h3>
					<?php if($processes){ ?>
					<div class="panel panel-primary">
						<!-- Default panel contents -->
						<div class="panel-heading">Steps</div>
						<div class="panel-body">
					<table class='table'>
						<tr>
							<TH>Process</TH>
							<TH>Step #</TH>
							<TH>Step Name</TH>
							<TH>Has Attachment</TH>
							<TH>Has Report</TH>
							<TH>Who's Responsible</TH>
							<th>Date Created</th>
							<TH>Actions</TH>
						</tr>
						<?php
							// get all branch base on company

							// get all terminals of each branch
							$arr_att = ['No Attachment','With Attachment'];
							$arr_rep = ['No Report','With Report'];

							foreach($processes as $p){
								$step = new Steps();
								$steps = $step->get_active('steps',array('process_id' ,'=',$p->id));

								if(!$steps){
									continue;
								}
						
								foreach($steps as $s){
									$pos = new Position();
									$position_list = $pos->getPositions($s->whos_responsible);
									$lblpos = "";
									if($position_list){
										foreach($position_list as $pp){

											$lblpos .= "<span class='label label-default'>$pp->position</span> ";
										}
									}
							?>
							<tr>
								<td><?php echo escape($p->name) ?></td>
								<td><?php echo escape($s->step_number) ?></td>
								<td><?php echo escape($s->name) ?></td>
								<td><?php echo escape($arr_att[$s->has_attachment]) ?></td>
								<td><?php echo escape($arr_rep[$s->has_report]) ?></td>
								<td style='width:300px;'><?php echo ($lblpos) ?></td>
								<td><?php echo escape(date('m/d/Y H:i:s A',$s->created)) ?></td>
						
								<td>
									<a href='addsteps.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt',$s->id);?>' title='Edit Steps'><span class='glyphicon glyphicon-pencil' style='margin-right:5px;'></span></a>
									<a href='#' class='deleteSteps' id="<?php echo Encryption::encrypt_decrypt('encrypt',$s->id);?>" title='Delete Steps'><span class='glyphicon glyphicon-remove'></span></a>
								</td>
							</tr>
							<?php
									$p->name ='';
								}
						}
						?>
					</table>
							</div>
				<?php } else { ?>
						<div class='alert alert-info'>There is no current item at the moment.</div>
				<?php } ?>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function(){
			$(".deleteSteps").click(function(){
				if(confirm("Are you sure you want to delete this record?")){
					id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php',{id:id,table:'steps'},function(data){
						if(data == "true"){
							location.reload();
						}
					});
				}
			});
		});


	</script>
<?php require_once '../includes/monitoring/page_tail.php'; ?>