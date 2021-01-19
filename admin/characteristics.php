<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('characteristics')) {
		// redirect to denied page
		Redirect::to(1);
	}

	$char = new Characteristics();
	$chars = $char->get_active('characteristics', array('company_id', '=', $user->data()->company_id));


?>

	<!-- Page content -->
	<div id="page-content-wrapper">
		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Manage Characteristics </h1>

			</div>
			<?php
				// get flash message if add or edited successfully
				if(Session::exists('characteristicsflash')) {
					echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('characteristicsflash') . "</div>";
				}
			?>
			<div class="row">
				<div class="col-md-12">
				<?php
					if($user->hasPermission('characteristics_m')) {
						?>

						<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
							<a href='addcharacteristics.php' class='btn btn-default' title='Add Characteristics'>
								<span class='glyphicon glyphicon-plus'></span>
								<span class='hidden-xs'>Add Characteristics</span>
							</a></div>
						<?php } ?>
					<?php
						if ($chars){
					?>
					<div class="panel panel-primary">
						<!-- Default panel contents -->
						<div class="panel-heading">Characteristics</div>
						<div class="panel-body">
							<div id="no-more-tables">
					<table class='table'>
						<thead>
						<tr>
							<TH>Name</TH>
							<TH>Created</TH>
							<?php
								if($user->hasPermission('characteristics_m')) {
							?>
							<TH>Actions</TH>
							<?php  } ?>
						</tr>
						</thead>
						<tbody>
						<?php

							foreach($chars as $c) {
								?>
								<tr>
									<td data-title='Name'><strong><?php echo escape($c->name) ?></strong></td>
									<td data-title='Created'><?php echo escape(date('m/d/Y H:i:s A', $c->created)) ?></td>
									<?php
										if($user->hasPermission('characteristics_m')) {
									?>
									<td  data-title='Action'>
										<a class='btn btn-primary' href='addcharacteristics.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt', $c->id); ?>' title='Edit Characteristics'><span class='glyphicon glyphicon-pencil'></span></a>
										<a href='#' class='btn btn-primary deleteCharacteristics' id="<?php echo Encryption::encrypt_decrypt('encrypt', $c->id); ?>" title='Delete Characteristics'><span class='glyphicon glyphicon-remove'></span></a>
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
			$(".deleteCharacteristics").click(function() {
				if(confirm("Are you sure you want to delete this record?")) {
					id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php', {id: id, table: 'characteristics'}, function(data) {
						if(data == "true") {
							location.reload();
						}
					});
				}
			});
		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>