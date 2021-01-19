<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('inventory_issues')) {
		// redirect to denied page
		Redirect::to(1);
	}


?>



	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>Disposed</h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}
		?>
		<?php include 'includes/issues_nav.php'; ?>
		<div class="row">
			<div class="col-md-12">

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Disposed</div>
					<div class="panel-body">
						<div class="row">

							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon"><span class='glyphicon glyphicon-search'></span></span>
										<input type="text" id="searchSales" class='form-control' placeholder='Search..'/>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select id="branch_id" name="branch_id" class="form-control">
										<option value=''></option>
										<?php
											$branch = new Branch();
											$branches =  $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));
											foreach($branches as $b){
												$a = isset($id) ? $terminal->data()->branch_id : escape(Input::get('branch_id'));

												if($a==$b->id){
													$selected='selected';
												} else {
													$selected='';
												}
												?>
												<option value='<?php echo $b->id ?>' <?php echo $selected ?>><?php echo $b->name;?> </option>
												<?php
											}
										?>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select id="rack_id" name="rack_id" class="form-control">
										<option value=''></option>
										<option value='-1'>Warehouse racks</option>
										<?php
											$rack = new Rack();
											$racks =  $branch->get_active('racks',array('company_id' ,'=',$user->data()->company_id));
											foreach($racks as $b){
												?>
												<option value='<?php echo $b->id ?>'><?php echo $b->rack;?> </option>
												<?php
											}
										?>
									</select>
								</div>
							</div>

						</div>

						<input type="hidden" id="hiddenpage" />
						<div id="holder"></div>
					</div>


				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->

	<script>

		$(document).ready(function() {

		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>