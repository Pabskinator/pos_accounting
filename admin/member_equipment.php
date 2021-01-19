<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('mem_equipment')) {
		// redirect to denied page
		Redirect::to(1);
	}

	$member_equipment = new Member_equipment();
	$list = $member_equipment->getMemberEquipments();





?>



	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Member Borrowed Items
			</h1>
		</div>
		<?php include 'includes/member_equipment_nav.php' ?>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">
						<div class='row'>
							<div class='col-md-6'>List</div>
							<div class='col-md-6 text-right'>
							</div>
						</div>
					</div>
					<div class="panel-body">
						<?php
							if($list){
								?>
								<table class="table table-bordered">
									<thead>
									<tr>
										<th>Id</th>
										<th>Client</th>
										<th>Item</th>
										<th>Borrowed Qty</th>

									</tr>
									</thead>
									<tbody>
									<?php
										foreach($list as $item){
											?>
											<tr>
												<td style='border-top: 1px solid #ccc;'><?php echo $item->id; ?></td>
												<td style='border-top: 1px solid #ccc;'><?php echo $item->member_name; ?></td>
												<td style='border-top: 1px solid #ccc;'>
													<?php echo $item->item_code; ?>
													<span class='span-block text-danger'>
														<?php echo $item->description; ?>
													</span>
												</td>
												<td style='border-top: 1px solid #ccc;'> <?php echo $item->borrowed_qty; ?></td>

											</tr>
											<?php
										}
									?>
									</tbody>
								</table>
								<?php
							} else {
								?>
								<div class="alert alert-info">
									No record
								</div>
								<?php
							}
						?>

					</div>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id='mtitle'></h4>
				</div>
				<div class="modal-body" id='mbody'>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<script>

		$(document).ready(function() {


		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>