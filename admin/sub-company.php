<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('subcom')){
		// redirect to denied page
		Redirect::to(1);
	}

	// get all branch base on company
	$subCompany = new Sub_company();
	$subCompanies = $subCompany->get_active('sub_companies',array('company_id' ,'=',$user->data()->company_id));

?>

	<!-- Page content -->
	<div id="page-content-wrapper">




	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Manage <?php echo Configuration::getValue('sub_company'); ?>
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
				<?php if($user->hasPermission('subcom_m')) { ?>
					<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
						<a class='btn btn-default' href='add-sub-company.php' title='Add Branch'>
							<span class='glyphicon glyphicon-plus'></span>
							<span class='hidden-xs'>Add <?php echo Configuration::getValue('sub_company'); ?></span>
						</a>
					</div>
				<?php } ?>
				<?php
					if ($subCompanies){
				?>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading"><?php echo Configuration::getValue('sub_company'); ?></div>
					<div class="panel-body">
						<div id="no-more-tables">
							<table class='table' id='tbl_comp'>
								<thead>
								<tr>
									<TH>Name</TH>
									<TH>Description</TH>
									<TH>Data Created</TH>
									<?php if($user->hasPermission('subcom_m')) { ?>
										<TH>Actions</TH>
									<?php } ?>
								</tr>
								</thead>
								<tbody>
								<?php
									foreach($subCompanies as $b){
										?>
										<tr>
											<td data-title='Name'><?php echo escape($b->name); ?></td>
											<td data-title='Description'><?php echo escape($b->description); ?></td>
											<td data-title='Created'><?php echo escape(date('m/d/Y H:i:s A',$b->created)); ?></td>
											<?php if($user->hasPermission('subcom_m')) { ?>
												<td data-title='Action'>
													<a class='btn btn-primary' href='add-sub-company.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt',$b->id);?>' title='Edit Company'><span class='glyphicon glyphicon-pencil'></span></a>
													<a href='#' class='btn btn-primary deleteSubCompany' id="<?php echo Encryption::encrypt_decrypt('encrypt',$b->id);?>" title='Delete Company'><span class='glyphicon glyphicon-remove'></span></a>
												</td>
											<?php } ?>
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
						<div class='alert alert-info'>There is no current record at the moment.</div>
						<?php
					}
					?>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function(){
			$(".deleteSubCompany").click(function(){
				if(confirm("Are you sure you want to delete this record? \n ")){
					id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php',{id:id,table:'sub_companies'},function(data){
						if(data == "true"){
							location.reload();
						}
					});
				}
			});
		});


		$('#tbl_comp').dataTable({
			iDisplayLength: 50
		});

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>