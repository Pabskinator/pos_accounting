<?php
	// $user have all the properties and method of the current user
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('settings')) {
		// redirect to denied page
		Redirect::to(1);
	}

	$styleClass = new Style();
	$myStyles = $styleClass->get_active('styles',array('company_id','=',$user->data()->company_id));

?>



	<!-- Page content -->
	<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Manage Styles </h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}
		?>
		<div class="row">
			<div class="col-md-12">
				<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
					<a class='btn btn-default' href='addstyles.php'> <span class='glyphicon glyphicon-plus'></span> Add Themes </a>
				</div>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Themes</div>
					<div class="panel-body">
						<?php
							if($myStyles){
								?>
						<div id="no-more-tables">
							<div class="table-responsive">
								<table class='table'>
									<thead>
									<tr>
										<TH>Name</TH>
										<TH>Created</TH>
										<th></th>
									</tr>
									</thead>
									<tbody>
								<?php
								foreach($myStyles as $st){
									?>
									<tr>
										<td data-title='Theme name'><?php echo escape($st->name); ?></td>
										<td data-title='Date created'><?php echo escape(date('m/d/Y',$st->created)); ?></td>
										<td >
											<a  class='btn btn-primary' href='addstyles.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt', $st->id); ?>' title='Edit Theme'><span class='glyphicon glyphicon-pencil'></span></a>
											<?php if($st->is_set == 1){
												?>
												<a  class='btn btn-primary removeTheme' data-id='<?php echo $st->id ;?>' href='#' title='Remove theme' ><span class='glyphicon glyphicon-remove'></span></a>
												<?php
											} else {
												?>
												<a  class='btn btn-primary setTheme' data-id='<?php echo $st->id ;?>' href='#' title='Remove theme' ><span class='glyphicon glyphicon-ok'></span></a>
												<?php
											}
											?>
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
								echo "<div class='alert alert-info'>No record found.</div>";
							}
						?>
					</div>
				</div>

			</div>
		</div>
	</div>
	<!-- end page content wrapper-->
	<script>

		$(document).ready(function() {
			$('body').on('click','.removeTheme',function(){
				var id = $(this).attr('data-id');
				$.ajax({
				    url:'../ajax/ajax_query2.php',
				    type:'post',
				    data: {functionName:'removeTheme',id:id},
				    success: function(data){
					    location.href='style_config.php';
				    },
				    error:function(){

				    }
				});
			});

			$('body').on('click','.setTheme',function(){
				var id = $(this).attr('data-id');
				$.ajax({
					url:'../ajax/ajax_query2.php',
					type:'post',
					data: {functionName:'setTheme',id:id},
					success: function(data){
						location.href='style_config.php';
					},
					error:function(){

					}
				});
			});
		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>