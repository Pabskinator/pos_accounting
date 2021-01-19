<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('item_service_t')){
		// redirect to denied page
		Redirect::to(1);
	}

	// get all branch base on company
	$service_type = new Service_type();
	$service_types = $service_type->get_active('service_types',array('company_id' ,'=',$user->data()->company_id));

?>



	<!-- Page content -->
	<div id="page-content-wrapper">



	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">

		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Manage Service Types
			</h1>

		</div>
		<?php include 'includes/service_nav.php'; ?>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')){
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('flash')."</div>";
			}
		?>


		<div class="row">
			<div class="col-md-12">
				<?php if($user->hasPermission('sales_type_m')) { ?>

					<a href='add-service-type.php' class='btn btn-default' style='margin-bottom:5px;'>
						<span class='glyphicon glyphicon-plus'></span>
						Add Type
					</a>

				<?php } ?>
				<?php
					if ($service_types){
				?>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Types</div>
					<div class="panel-body">
						<table class='table' id='tblBranches'>
							<thead>
							<tr>
								<TH>Name</TH>
								<TH>Data Created</TH>
								<TH>Actions</TH>

							</tr>
							</thead>
							<tbody>
							<?php

								foreach($service_types as $s){
									?>
									<tr>
										<td><?php echo escape($s->name); ?></td>
										<td><?php echo escape(date('m/d/Y H:i:s A',$s->created)); ?></td>
										<td>
											<a  class='btn btn-primary'  href='add-service-type.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt',$s->id);?>' title='Edit Type'><span class='glyphicon glyphicon-pencil'></span></a>
											<a href='#' class='btn btn-primary deleteType' id="<?php echo Encryption::encrypt_decrypt('encrypt',$s->id);?>" title='Delete Type'><span class='glyphicon glyphicon-remove'></span></a>
										</td>

									</tr>
									<?php
								}
							?>
							</tbody>
						</table>
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
			$(".deleteType").click(function(){
				if(confirm("Are you sure you want to delete this record?")){
					var id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php',{id:id,table:'service_types'},function(data){
						if(data == "true"){
							location.reload();
						}
					});
				}
			});


		});


		$('#tblBranches').dataTable({
			iDisplayLength: 50
		});

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>