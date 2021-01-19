<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('item')){
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
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Manage Product Alert
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


				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Manage Product Alert</div>
					<div class="panel-body">
						<?php if(true) { ?>
						<table class='table'>
							<tr>

								<TH>Barcode</TH>
								<th>Item Code</th>
								<th>Description</th>
								<TH>Created</TH>
								<th>Alerts</th>
								<TH>Actions</TH>
							</tr>
							<tr>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
						</table>
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

		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>