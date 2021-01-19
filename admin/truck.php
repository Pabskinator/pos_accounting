<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('truck')){
		// redirect to denied page
		Redirect::to(1);
	}

	$truck = new Truck();
	$trucks = $truck->get_active('trucks',array('company_id' ,'=',$user->data()->company_id));
?>



	<!-- Page content -->
	<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Manage Trucks
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

				<?php if($user->hasPermission('truck')){ ?>
					<br>
					<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
						<a class='btn btn-default' href='addtruck.php' title='Add Truck'>
							<span class='glyphicon glyphicon-plus'></span>
						<span class='hidden-xs'>
						Add Truck
						</span>
						</a>
					</div>
				<?php } ?>

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Trucks</div>
					<div class="panel-body">
						<?php if($trucks) { ?>
						<div id="no-more-tables">
							<table class='table'>
								<thead>
								<tr>

									<TH>Truck Name</TH>
									<TH>Description</TH>
									<TH>CBM</TH>
									<TH>Created</TH>
									<?php if($user->hasPermission('truck')){ ?>
										<TH>Actions</TH>
									<?php } ?>
								</tr>
								</thead>
								<tbody>
								<?php

									foreach($trucks as $t) {
										?>
										<tr>

											<td data-title='Name'><?php echo escape($t->name) ?></td>
											<td data-title='Description'><?php echo ($t->description) ? escape($t->description) : "<i class='fa fa-ban'></i>"; ?></td>
											<td data-title='CBM'><?php echo ($t->cbm) ? escape($t->cbm) : "<i class='fa fa-ban'></i>"; ?></td>
											<td data-title='Created'><?php echo escape(date('m/d/Y H:i:s A', $t->created)) ?></td>
											<?php if($user->hasPermission('truck')){ ?>
												<td>

													<a class='btn btn-primary' href='addtruck.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt', $t->id); ?>' title='Edit Truck'><span class='glyphicon glyphicon-pencil' ></span></a>
													<a href='#' class='btn btn-primary deleteTruck' id="<?php echo Encryption::encrypt_decrypt('encrypt', $t->id); ?>" title='Delete Truck'><span class='glyphicon glyphicon-remove'></span></a>

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
					<?php   }  else { ?>
						<div class='alert alert-info'>There is no current item at the moment.</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function(){
			$(".deleteTruck").click(function(){
				if(confirm("Are you sure you want to delete this record?")){
					id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php',{id:id,table:'trucks'},function(data){
						if(data == "true"){
							location.reload();
						}
					});
				}
			});
		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>