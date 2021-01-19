<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('sales_type')){
		// redirect to denied page
		Redirect::to(1);
	}

	// get all branch base on company
	$salestype = new Sales_type();
	$salestypes = $salestype->get_active('salestypes',array('company_id' ,'=',$user->data()->company_id));

?>

	<!-- Page content -->
<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<?php include 'includes/sales_nav.php'; ?>
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Manage Sales type
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
				<?php if($user->hasPermission('sales_type_m')) { ?>

						<a href='add-sales-type.php' class='btn btn-default' style='margin-bottom:5px;'>
							<span class='glyphicon glyphicon-plus'></span>
							Add Type
						</a>

				<?php } ?>
				<?php
					if ($salestypes){
				?>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Types</div>
					<div class="panel-body">
						<table class='table' id='tblBranches'>
							<thead>
							<tr>
								<TH>ID</TH>
								<TH>Name</TH>
								<TH>Description</TH>
								<TH>Data Created</TH>

								<?php if($user->hasPermission('sales_type_m')){
									?>
									<th>User</th>
									<th>Default</th>
									<TH>Actions</TH>
								<?php
								}?>

							</tr>
							</thead>
							<tbody>
							<?php
								$ardef = array('','Yes');
								foreach($salestypes as $s){

									$user_id = $s->user_id;
									$user_list = "";
									if($user_id){
										$explodes = explode(',',$user_id);
										foreach($explodes as $ex){
											if($ex){
												$us = new User($ex);
												if(isset($us->data()->id)){
													$user_list .= "<p class='text-danger'>".$us->data()->firstname." ".$us->data()->lastname."</p>";
												}
											}

										}
									}
									?>
									<tr>
										<td style='border-top: 1px solid #ccc;'><?php echo escape($s->id); ?></td>
										<td style='border-top: 1px solid #ccc;'><?php echo escape($s->name); ?></td>
										<td style='border-top: 1px solid #ccc;'><?php echo escape($s->description); ?></td>
										<td style='border-top: 1px solid #ccc;'><?php echo escape(date('m/d/Y H:i:s A',$s->created)); ?></td>

										<?php if($user->hasPermission('sales_type_m')){
										?>
										<td style='border-top: 1px solid #ccc;'>
											<?php echo $user_list; ?>
										</td>
											<td style='border-top: 1px solid #ccc;'><?php echo $ardef[$s->is_default]; ?></td>
										<td style='border-top: 1px solid #ccc;'>
											
											<a  class='btn btn-primary'  href='add-sales-type.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt',$s->id);?>' title='Edit Type'><span class='glyphicon glyphicon-pencil'></span></a>
											<a href='#' class='btn btn-primary deleteType' id="<?php echo Encryption::encrypt_decrypt('encrypt',$s->id);?>" title='Delete Type'><span class='glyphicon glyphicon-remove'></span></a>
										</td>
										<?php
										}?>
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
					$.post('../ajax/ajax_delete.php',{id:id,table:'salestypes'},function(data){
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