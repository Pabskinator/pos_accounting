<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('package')){
		// redirect to denied page
		Redirect::to(1);
	}

	// get all branch base on company
	$packagecls = new Package();
	$packages = $packagecls->get_active('packages',array('company_id' ,'=',$user->data()->company_id));

	$cf = new Custom_field();
	$cfd = new Custom_field_details();
	$getstationdet = $cf->getcustomform('stations',$user->data()->company_id);
	$label_name = isset($getstationdet->label_name)? strtoupper($getstationdet->label_name):'STATION';
?>



	<!-- Page content -->
	<div id="page-content-wrapper">




	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Manage Packages
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
				<?php include 'includes/station_nav.php'; ?>
				<br>
				<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
					<a class='btn btn-default' href='addpackage.php' title='Add Package'>
						<span class='glyphicon glyphicon-plus'></span>
						<span class='hidden-xs'>Add Package</span>
					</a>
				</div>

				<?php
					if ($packages){
				?>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Packages</div>
					<div class="panel-body">
						<div id="no-more-tables">
							<table class='table' id='tblpackages'>
								<thead>
								<tr>
									<TH>Packages</TH>
									<TH>Description</TH>
									<TH>Data Created</TH>
									<TH>Actions</TH>
								</tr>
								</thead>
								<tbody>
								<?php
									foreach($packages as $b){
										?>
										<tr>
											<td data-title='Package'><?php echo escape($b->name); ?></td>
											<td data-title='Description'><?php echo escape($b->description); ?></td>
											<td data-title='Created'><?php echo escape(date('m/d/Y H:i:s A',$b->created)); ?></td>
											<td data-title='Action'>
												<a class='btn btn-primary' href='addpackage.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt',$b->id);?>' title='Edit Package'><span class='glyphicon glyphicon-pencil'></span></a>
												<a href='#' class='btn btn-primary deletePackage' id="<?php echo Encryption::encrypt_decrypt('encrypt',$b->id);?>" title='Delete Package'><span class='glyphicon glyphicon-remove'></span></a>
											</td>
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
			$(".deletePackage").click(function(){
				if(confirm("Are you sure you want to delete this record? \n ")){
					var id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php',{id:id,table:'packages'},function(data){
						if(data == "true"){
							location.reload();
						}
					});
				}
			});
		});


		$('#tblpackages').dataTable({
			iDisplayLength: 50
		});

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>