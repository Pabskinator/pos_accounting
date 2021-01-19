<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!true){
		// redirect to denied page
		Redirect::to(1);
	}

	// get all branch base on company
	$rack_tag = new Rack_tag();
	$rack_tags = $rack_tag->get_active('rack_tags',array('company_id' ,'=',$user->data()->company_id));

?>



	<!-- Page content -->
	<div id="page-content-wrapper">




	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Manage Tagging
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
				<?php if(true) { ?>
					<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
						<a class='btn btn-default' href='add_rack_tagging.php' title='Add Tags'>
							<span class='glyphicon glyphicon-plus'></span>
							<span class='hidden-xs'>Add Tags</span>
						</a>
					</div>
				<?php } ?>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Rack Tags</div>
					<div class="panel-body">
						<?php
							if ($rack_tags){
								?>

								<div id="no-more-tables">
									<table class='table' id='tblBranches'>
										<thead>
										<tr>
											<TH>Tag Name</TH>
											<TH>Data Created</TH>
											<TH>Users</TH>
											<th></th>
										</tr>
										</thead>
										<tbody>
										<?php
											foreach($rack_tags as $b){
												$rack_user = new User();
												$user_arr = [];
												$lbls = "";
												if(strpos($b->assign_to,",") > 0){
													$user_arr = explode(',',$b->assign_to);
												} else {
													if($b->assign_to){
														$user_arr[] = $b->assign_to;
													}

												}
												foreach($user_arr as $arr){
													if($arr){
														$rack_user = new User($arr);
														$lbls .= "<span class='text-danger span-block'>".ucwords($rack_user->data()->firstname . " " .$rack_user->data()->lastname) ."</span>";
													}

												}
												?>
												<tr>
													<td data-title='Tag name'><?php echo escape($b->tag_name); ?></td>
													<td data-title='Created'><?php echo escape(date('m/d/Y H:i:s A',$b->created)); ?></td>
													<td><?php echo ($lbls) ? $lbls : "N/A"; ?></td>

													<td data-title='Action'>
														<a class='btn btn-primary' href='add_rack_tagging.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt',$b->id);?>' title='Edit Tag'><span class='glyphicon glyphicon-pencil'></span></a>
														<a href='#' class='btn btn-primary deleteTag' id="<?php echo Encryption::encrypt_decrypt('encrypt',$b->id);?>" title='Delete Tag'><span class='glyphicon glyphicon-remove'></span></a>
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
		</div>
	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function(){
			$(".deleteTag").click(function(){
				if(confirm(" Are you sure you want to delete this record? ")){
					id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php',{id:id,table:'rack_tags'},function(data){
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