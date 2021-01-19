<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('item_commission')){
		// redirect to denied page
		Redirect::to(1);
	}

	// get all branch base on company
	$ci = new Commission_item();
	$list = $ci->getCommissions();

?>



	<!-- Page content -->
	<div id="page-content-wrapper">




	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<div>
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Commission Item
			</h1>

			</div>
		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')){
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('flash')."</div><br>";
			}
		?>
		<div class="row">
			<div class="col-md-12">
				<?php 	if($user->hasPermission('item_commission')) { ?>
					<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
						<a class='btn btn-default' href='addcommissionitem.php' title='Add Commission'>
							<span class='glyphicon glyphicon-plus'></span> <span class='hidden-xs'>Add Record</span> </a>
						<a class='btn btn-default' href='commission_list.php' title='Commission list'>
							<span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>Commission List</span> </a>
					</div>
				<?php } ?>
				<?php
					if ($list){
				?>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Items with commission</div>
					<div class="panel-body">
						<div id="no-more-tables">
							<table class='table' id='tblbrands'>
								<thead>
								<tr>
									<TH>Item</TH>
									<TH>Amount/Perc</TH>
									<TH>Data Created</TH>
									<TH>Actions</TH>
								</tr>
								</thead>
								<tbody>
								<?php
									foreach($list as $b){
										$item_name = $b->item_code;
										$item_desc = $b->description;
										$name = ($b->lastname)? ucwords($b->firstname . " " . $b->lastname)  : "";


										?>
										<tr>
											<td data-title='Name'>
												<?php echo escape($item_name); ?>
												<small class='span-block'><?php echo $item_desc; ?></small>
												<small class='text-danger span-block'><?php echo $name; ?></small>
											</td>
											<td data-title='Amount'>

												<?php
													if($b->amount != 0.00 ){
														echo escape($b->amount);
													}else {
														echo escape($b->perc) . "%";
													}

												?>
											</td>
											<td data-title='Created'><?php echo escape(date('m/d/Y H:i:s A',$b->created)); ?></td>
											<td data-title='Action'>
												<a class='btn btn-primary' href='addcommissionitem.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt',$b->id);?>' title='Edit Record'><span class='glyphicon glyphicon-pencil'></span></a>
												<a href='#' class='btn btn-primary deleteItem' id="<?php echo Encryption::encrypt_decrypt('encrypt',$b->id);?>" title='Delete Record'><span class='glyphicon glyphicon-remove'></span></a>
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
			$(".deleteItem").click(function(){
				if(confirm("Are you sure you want to delete this record? \n ")){
					var id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php',{id:id,table:'commission_items'},function(data){
						if(data == "true"){
							location.reload();
						}
					});
				}
			});
		});


		$('#tblbrands').dataTable({
			iDisplayLength: 50
		});

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>