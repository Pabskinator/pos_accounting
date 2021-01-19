<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('branch')){
		// redirect to denied page
		Redirect::to(1);
	}

	// get all branch base on company
	$dicer_dep = new Dicer_deposit();
	$details = $dicer_dep->getReceived();
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
								<table class='table'>
									<thead>
									<tr>
										<th>Order ID</th>
										<th>Dicer</th>
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
												<td>
													<?php echo $det->id; ?>
												</td>
												<td>
													<?php echo $det->remarks; ?>
												</td>
												<td>
													<?php echo $det->branch_name; ?>
												</td>
												<td>
													<?php echo date('m/d/Y',$det->is_scheduled); ?>
												</td>
												<td>
													<?php echo date('m/d/Y',$det->date_received); ?>
												</td>
												<td>

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

		});



	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>