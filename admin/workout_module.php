<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('wo_mod')){
		// redirect to denied page
		Redirect::to(1);
	}

	// get all branch base on company
	$mod = new Workout_module();
	$mods = $mod->get_active('workout_module',array('1' ,'=','1'));

?>


	<!-- Page content -->
	<div id="page-content-wrapper">




	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Workout Module
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
				<div class='text-right'>
					<a href='assigned_workout.php' class='btn btn-default'>Assign Module</a>
					<a href='add_workout_module.php' class='btn btn-default'>Add New Level</a>
				</div>
				<br>
				<?php
					if ($mods){
				?>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Workout Module</div>
					<div class="panel-body">
						<div id="no-more-tables">
							<table class='table' id='tblbrands'>
								<thead>
								<tr>
									<TH>Name</TH>
									<TH>Module</TH>
									<TH>Actions</TH>
								</tr>
								</thead>
								<tbody>
								<?php
									foreach($mods as $b){
										?>
										<tr>
											<td data-title='Name'><?php echo escape($b->name); ?></td>
											<td data-title='Points Needed'><?php echo ($b->description); ?></td>
											<td data-title='Action'>
												<a class='btn btn-primary' href='add_workout_module.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt',$b->id);?>' title='Edit'><span class='glyphicon glyphicon-pencil'></span></a>
												<a href='#' class='btn btn-primary deleteModule' id="<?php echo Encryption::encrypt_decrypt('encrypt',$b->id);?>" title='Delete'><span class='glyphicon glyphicon-remove'></span></a>
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
						?>						<br><div class='alert alert-info'>There is no current item at the moment.</div>
						<?php
					}
					?>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function(){
			$(".deleteModule").click(function(){
				if(confirm("Are you sure you want to delete this record? \n ")){
					var id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php',{id:id,table:'workout_module'},function(data){
						if(data == "true"){
							location.reload();
						}
					});
				}
			});
		});




	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>