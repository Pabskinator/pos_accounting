<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('branch')){
		// redirect to denied page
		Redirect::to(1);
	}

	// get all branch base on company
	$wh_order = new Wh_order();
	$details = $wh_order->getReceived();
?>



	<!-- Page content -->
	<div id="page-content-wrapper">




	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Received Orders
			</h1>

		</div>

		<div class="row">
			<div class="col-md-12">

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading"></div>
					<div class="panel-body">
						<?php
							if($details){
							?>
							<table class='table' id='tbl-ro'>
								<thead>
								<tr>
									<th>Order ID</th>
									<th>Order by</th>
									<th>Branch</th>
									<th>Date Deliver</th>
									<th>Date Received</th>
									<th></th>
								</tr>
								</thead>
								<tbody>
								<?php
									foreach($details as $det){
									?>
									<tr>
										<td style='border-top: 1px solid #ccc;' >
											<strong><?php echo $det->id; ?></strong>
										</td>
										<td style='border-top: 1px solid #ccc;' >
											<span class='text-muted'>
												<?php echo ($det->user_id) ? ucwords($det->firstname . " " . $det->lastname) : $det->remarks; ?>
											</span>
										</td>
										<td style='border-top: 1px solid #ccc;' >
											<?php echo $det->branch_name; ?>
										</td>
										<td style='border-top: 1px solid #ccc;' class='text-danger'>
											<?php
												if($det->is_scheduled){
													echo date('m/d/Y',$det->is_scheduled);
												} else {
													echo "Not yet set.";
												}

											?>
										</td>
										<td style='border-top: 1px solid #ccc;'  class='text-danger'>
											<?php
												if($det->received_date){
													echo date('m/d/Y',$det->received_date);
												} else {
													echo "Not yet set.";
												}

											?>

										</td>
										<td style='border-top: 1px solid #ccc;' >

										</td>
									</tr>
									<?php
								}
								?>
								</tbody>
							</table>
							<?php
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function(){
			$('#tbl-ro').dataTable({
				iDisplayLength: 50,
				"aaSorting": []
			});
		});




	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>