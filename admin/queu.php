<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('queue')){
		// redirect to denied page
		Redirect::to(1);
	}


	$branch = new Branch();
	$branches = $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));

	$hasqueue= false;

?>



	<!-- Page content -->
<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Manage Queues
			</h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('queueflash')){
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('queueflash')."</div>";
			}
		?>
		<div class="row">
			<div class="col-md-12">
				<?php if($user->hasPermission('queue_m')){ ?>

					<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
						<a href='addqueu.php' class='btn btn-default'>
							<span class='glyphicon glyphicon-plus'></span>
							<span class='hidden-xs'>Add Queue</span>
						</a></div>
				<?php } ?>
				<?php if($branches){ ?>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Queues</div>
					<div class="panel-body">
						<div id="no-more-tables">
						<table class='table'>
							<thead>
							<tr>
								<TH>Queue</TH>
								<TH>Created</TH>
								<TH>Branch</TH>
								<?php if($user->hasPermission('queue_m')){ ?>
									<TH>Actions</TH>
								<?php } ?>
							</tr>
							</thead>
							<tbody>
							<?php
								// get all branch base on company

								// get all terminals of each branch
								$hasqueue= false;
								foreach($branches as $b){
									$queue = new Queu();
									$queues = $queue->get_active('queus',array('branch_id' ,'=',$b->id));

									if(!$queues){
										continue;
									}
									foreach($queues as $t){
										$hasqueue = true;
										?>
										<tr>
											<td data-title='Name'><strong><?php echo escape($t->name) ?></strong></td>
											<td data-title='Created'><?php echo escape(date('m/d/Y H:i:s A',$t->created)) ?></td>
											<td data-title='Branch'><?php echo $b->name ?></td>
											<?php if($user->hasPermission('queue_m')){ ?>
												<td data-title='Action'>
													<a class='btn btn-primary' href='addqueu.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt',$t->id);?>' title='Edit Queue'><span class='glyphicon glyphicon-pencil' ></span></a>
													<a href='#' class='btn btn-primary deleteQueue' id="<?php echo Encryption::encrypt_decrypt('encrypt',$t->id);?>" title='Delete Queue'><span class='glyphicon glyphicon-remove'></span></a>
												</td>
											<?php } ?>
										</tr>
									<?php
									}
								}

							?>
							</tbody>
						</table>
						</div>
						<?php
							if(!$hasqueue){
								?>
								<div class="alert alert-info">No item at the moment..</div>
								<?php
							}
						?>
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
			$(".deleteQueue").click(function(){
				if(confirm("Are you sure you want to delete this record?")){
					id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php',{id:id,table:'queus'},function(data){
						if(data == "true"){
							location.reload();
						}
					});
				}
			});
		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>