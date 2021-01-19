<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('sales')) {
		// redirect to denied page
			Redirect::to(1);
	}

?>



	<!-- Page content -->
	<div id="page-content-wrapper">
		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
			
					<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Manage Sales </h1>

				
			</div>
			<?php
				// get flash message if add or edited successfully
				if(Session::exists('salesflash')) {
					echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('salesflash') . "</div>";
				}
			?>
			<div class="row">
				<div class="col-md-12">


					<div class="panel panel-primary">
						<!-- Default panel contents -->
						<div class="panel-heading">Sales</div>
						<div class="panel-body">
							<div id='no-more-tables'>
						<table class='table-bordered table-striped table-condensed cf'>
	<thead class='cf'>
		<tr>
			<th>Code</th>
			<th>Company</th>
			<th class="numeric">Price</th>
			<th class="numeric">Change</th>
			<th class="numeric">Change %</th>
			<th class="numeric">Open</th>
			<th class="numeric">High</th>
			<th class="numeric">Low</th>
			<th class="numeric">Volume</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td data-title="Code">AAC</td>
			<td data-title="Company">AUSTRALIAN AGRICULTURAL COMPANY LIMITED.</td>
			<td data-title="Price" class="numeric">$1.38</td>
			<td data-title="Change" class="numeric">-0.01</td>
			<td data-title="Change %" class="numeric">-0.36%</td>
			<td data-title="Open" class="numeric">$1.39</td>
			<td data-title="High" class="numeric">$1.39</td>
			<td data-title="Low" class="numeric">$1.38</td>
			<td data-title="Volume" class="numeric">9,395</td>
		</tr>
	</tbody>
</table>
</div>
						</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->

	<script>

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>