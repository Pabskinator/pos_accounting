<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('branch')){
		// redirect to denied page
		Redirect::to(1);
	}

	// get all branch base on company
	$sms = new Sms_gateway();
	$mobile_numbers = $sms->get_active('sms_gateway',array('company_id' ,'=',$user->data()->company_id));

?>



	<!-- Page content -->
	<div id="page-content-wrapper">




	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Manage Mobile Numbers
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
				<?php if($user->hasPermission('branch')) { ?>
					<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
						<a class='btn btn-default' href='addmobile.php' title='Add Mobile'>
							<span class='glyphicon glyphicon-plus'></span>
							<span class='hidden-xs'>Add Mobile</span>
						</a>
					</div>
				<?php } ?>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Mobile Numbers</div>
					<div class="panel-body">
						<?php
							if ($mobile_numbers){
								?>

								<div id="no-more-tables">
									<table class='table' id='tblBranches'>
										<thead>
										<tr>
											<TH>Name</TH>
											<TH>Number</TH>
											<TH>Branch</TH>
											<th>Created</th>
											<TH>Actions</TH>
										</tr>
										</thead>
										<tbody>
										<?php
											foreach($mobile_numbers as $b){
												$branches = "";
												if(strpos($b->terminal_id,",") > 0){
													$exploded_terminal = explode(",",$b->terminal_id);
													foreach($exploded_terminal as $t){
														$terminal = new Terminal($t);
														$branch = new Branch($terminal->data()->branch_id);
														$branches .= $branch->data()->name . ", ";
													}
													$branches = trim($branches,", ");
												} else {
													$terminal = new Terminal($b->terminal_id);
													$branch = new Branch($terminal->data()->branch_id);
													$branches = $branch->data()->name;
												}


												?>
												<tr>
													<td data-title='Name'><?php echo escape($b->name); ?></td>
													<td data-title='Mobile'><?php echo escape($b->mobile_number); ?></td>
													<td data-title='Branch'><?php echo escape($branches); ?></td>
													<td data-title='Created'><?php echo escape(date('m/d/Y H:i:s A',$b->created)); ?></td>

													<td data-title='Action'>
														<a class='btn btn-primary' href='addmobile.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt',$b->id);?>' title='Edit Mobile'><span class='glyphicon glyphicon-pencil'></span></a>
														<a href='#' class='btn btn-primary deleteMobile' id="<?php echo Encryption::encrypt_decrypt('encrypt',$b->id);?>" title='Delete Mobile'><span class='glyphicon glyphicon-remove'></span></a>
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

			$('body').on('click','.deleteMobile',function(e){
				e.preventDefault();
				if(confirm("Are you sure you want to delete this record? \n ")){
					var id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php',{id:id,table:'sms_gateway'},function(data){
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