<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('item_service_p')) {
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
			<span id="menu-toggle" class='glyphicon glyphicon-list'></span>  </h1>
	</div>
	<?php
		// get flash message if add or edited successfully
		if(Session::exists('flash')) {
			echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
		}
	?>

			<div id="test"></div>
			<div class="row">
				<div class="col-md-12">
				</div>
			</div>
		</div>
	</div>
	<script>
		$(function(){

		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>