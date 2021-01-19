<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';

	if(!$user->hasPermission('terminal')) {
		// redirect to denied page
		Redirect::to(1);
	}

	if(isset($_GET['edit'])) {
		$editid = $_GET['edit'];
	} else {
		$editid = 0;
	}

?>
<?php require_once '../includes/admin/page_head.php'; ?>
	<!-- Sidebar -->
<?php include_once '../includes/admin/sidebar.php'; ?>
	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">

	<h3>Page Settings</h3>

		<div class="row">
			<div class="col-xs-6 col-md-3">
				<a href="#" class="thumbnail">
					<div style='height:200px;'>
						<h3>Station</h3>
					</div>
				</a>
			</div>
			<div class="col-xs-6 col-md-3">
				<a href="#" class="thumbnail">
					<div style='height:200px;'></div>
				</a>
			</div>
			<div class="col-xs-6 col-md-3">
				<a href="#" class="thumbnail">
					<div style='height:200px;'></div>
				</a>
			</div>
			<div class="col-xs-6 col-md-3">
				<a href="#" class="thumbnail">
					<div style='height:200px;'></div>
				</a>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
</div>

<?php require_once '../includes/admin/page_tail2.php'; ?>