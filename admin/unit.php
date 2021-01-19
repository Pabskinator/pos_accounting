<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('unit')) {
		// redirect to denied page
		Redirect::to(1);
	}
	$unit = new Unit();
	$units = $unit->get_active('units', array('company_id', '=', $user->data()->company_id));


?>



	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Manage Units </h1>

			</div>
			<?php
				// get flash message if add or edited successfully
				if(Session::exists('unitflash')) {
					echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('unitflash') . "</div>";
				}
			?>
			<div class="row">

				<div class="col-md-12">
					<?php 	if($user->hasPermission('unit_m')) { ?>

						<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
							<a href='addunit.php' class='btn btn-default' title='Add Unit'>
								<span class='glyphicon glyphicon-plus'></span>
								<span class='hidden-xs'>Add Unit</span>
							</a>
							<a href='item_unit.php' class='btn btn-default' title='Item Unit'>
								<span class='glyphicon glyphicon-barcode'></span>
								<span class='hidden-xs'>item Unit</span>
							</a>
						</div>
				<?php } ?>
					<?php
						if ($units){
							?>
					<div class="panel panel-primary">
						<!-- Default panel contents -->
						<div class="panel-heading">Units</div>
						<div class="panel-body">
							<table class='table'>
								<tr>
									<TH>Name</TH>
									<TH>Created</TH>
									<?php 	if($user->hasPermission('unit_m')) { ?>
									<TH>Actions</TH>
									<?php } ?>
								</tr>
								<?php

									foreach($units as $u) {
										?>
										<tr>
											<td><strong><?php echo escape($u->name) ?></strong></td>
											<td><?php echo escape(date('m/d/Y H:i:s A', $u->created)) ?></td>
											<?php 	if($user->hasPermission('unit_m')) { ?>
											<td>
												<a class='btn btn-primary' href='addunit.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt', $u->id); ?>' title='Edit Unit'><span class='glyphicon glyphicon-pencil' ></span></a>
												<a href='#' class='btn btn-primary deleteUnits' id="<?php echo Encryption::encrypt_decrypt('encrypt', $u->id); ?>" title='Delete Unit'><span class='glyphicon glyphicon-remove'></span></a>
											</td>
											<?php } ?>
										</tr>
									<?php
									}
								?>
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

		$(document).ready(function() {
			$(".deleteUnits").click(function() {
				if(confirm("Are you sure you want to delete this record?")) {
					id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php', {id: id, table: 'units'}, function(data) {
						if(data == "true") {
							location.reload();
						}
					});
				}
			});
		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>