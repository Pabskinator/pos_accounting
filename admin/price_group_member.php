<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('price_group')){
		// redirect to denied page
		Redirect::to(1);
	}

	// get all branch base on company
	$pid = 0;
	if(Input::exists()){
		$pid = Input::get('price_group_id');
		$pid = ($pid) ? $pid : 0;
	}
	$mem = new Member_price_group();
	$price_groups = $mem->getMemberPriceGroups($pid);

	$price_group = new Price_group();
	$pg = $price_group->getPG();


?>


	<!-- Page content -->
	<div id="page-content-wrapper">


	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Member's Price List
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
					<a href='add_member_in_price_group.php' class='btn btn-default'>Add New</a>
				</div>
				<?php
					if ($price_groups){
				?>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">List</div>
					<div class="panel-body">
						<form action="" method="POST">
							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
									<select name="price_group_id" id="price_group_id" class='form-control'>
										<option value="0">All</option>
										<?php
											foreach($pg as $p){
												$selected = "";
												if($pid == $p->id){
													$selected = 'selected';
												}
												echo "<option $selected value='$p->id'>$p->name</option>";
											}
										?>
									</select>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<input type="submit" class='btn btn-default' value='Submit'>
									</div>
								</div>
							</div>
						</form>

						<div id="no-more-tables">
							<table class='table' id='tblbrands'>
								<thead>
								<tr>
									<TH>Member</TH>
									<TH>Price List</TH>
									<TH>Actions</TH>
								</tr>
								</thead>
								<tbody>
								<?php
									foreach($price_groups as $b){
										?>
										<tr>
											<td data-title='Name'><?php echo escape($b->member_name); ?></td>
											<td data-title='Module'><?php echo escape($b->group_name); ?></td>
											<td data-title='Action'>
												<a class='btn btn-primary' href='add_member_in_price_group.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt',$b->id);?>' title='Edit'><span class='glyphicon glyphicon-pencil'></span></a>
												<a href='#' class='btn btn-primary deleteMemberPriceGroup' id="<?php echo Encryption::encrypt_decrypt('encrypt',$b->id);?>" title='Delete'><span class='glyphicon glyphicon-remove'></span></a>
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
			$(".deleteMemberPriceGroup").click(function(){
				if(confirm("Are you sure you want to delete this record? \n ")){
					var id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php',{id:id,table:'member_price_groups'},function(data){
						if(data == "true"){
							location.reload();
						}
					});
				}
			});
			$('#tblbrands').dataTable({
				iDisplayLength: 50
			});
		});




	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>