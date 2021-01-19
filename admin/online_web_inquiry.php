<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('branch')){
		// redirect to denied page
		Redirect::to(1);
	}

	// get all branch base on company
	$ol = new Online_web_inquiry();
	$ols = $ol->get_active('online_web_inquiry',array('1' ,'=','1'));

?>



	<!-- Page content -->
	<div id="page-content-wrapper">




	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				List of Booking
			</h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')){
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('flash')."</div><br>";
			}
		?>
		<div class="row">
			<div class="col-md-12">

				<?php
					if ($ols){
				?>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Bookig</div>
					<div class="panel-body">
						<div id="no-more-tables">
							<table class='table' id='tblbrands'>
								<thead>
								<tr>
									<TH>Name</TH>
									<TH>Class</TH>
									<TH>Contact</TH>
									<TH>Email</TH>
									<TH>Age</TH>
									<TH>Data Created</TH>
									<TH>Actions</TH>
								</tr>
								</thead>
								<tbody>
								<?php
									foreach($ols as $b){
										?>
										<tr>
											<td data-title='Name'><?php echo escape($b->name); ?></td>
											<td data-title='Name'><?php echo escape($b->class_name); ?></td>
											<td data-title='Phone'><?php echo escape($b->phone); ?></td>
											<td data-title='Email'><?php echo escape($b->email); ?></td>
											<td data-title='Age'><?php echo escape($b->age); ?></td>
											<td data-title='Created'><?php echo date('m/d/y H:i:s A',escape($b->created)); ?></td>
											<td></td>

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

		$(document).ready(function(){

		});


		$('#tblbrands').dataTable({
			iDisplayLength: 50
		});

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>