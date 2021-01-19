<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('sales')){
		// redirect to denied page
		Redirect::to(1);
	}

	// get all branch base on company
	$assembly = new Assemble_item_for_order();
	$items = $assembly->get_active('assemble_item_for_orders',array('1' ,'=','1'));

?>


	<!-- Page content -->
	<div id="page-content-wrapper">




	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Assemble Item for Ordering
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
					<a href='add_assemble_item_for_ordering.php' class='btn btn-default'>Add New Item</a>
				</div>
				<br>
				<?php
					if ($items){
				?>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Items</div>
					<div class="panel-body">
						<div id="no-more-tables">
							<table class='table' id='tblbrands'>
								<thead>
								<tr>
									<TH>Item</TH>
									<TH>Minimum Level</TH>
									<TH>Actions</TH>
								</tr>
								</thead>
								<tbody>
								<?php
									foreach($items as $b){
										$prod = new Product($b->item_id)
										?>
										<tr>
											<td data-title='Name'>
												<?php echo escape($prod->data()->item_code); ?>
												<small class='span-block text-danger'>
													<?php echo escape($prod->data()->description); ?>
												</small>
											</td>
											<td data-title='Points Needed'><?php echo ($b->min_qty); ?></td>
											<td data-title='Action'>
												<a class='btn btn-primary' href='add_assemble_item_for_ordering.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt',$b->id);?>' title='Edit'><span class='glyphicon glyphicon-pencil'></span></a>
												<a href='#' class='btn btn-primary deleteItem' id="<?php echo Encryption::encrypt_decrypt('encrypt',$b->id);?>" title='Delete'><span class='glyphicon glyphicon-remove'></span></a>
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
			$(".deleteItem").click(function(){
				if(confirm("Are you sure you want to delete this record? \n ")){
					var id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php',{id:id,table:'assemble_item_for_orders'},function(data){
						if(data == "true"){
							location.reload();
						}
					});
				}
			});
		});




	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>