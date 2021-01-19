<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('orderpoint')) {
		// redirect to denied page
		Redirect::to(1);
	}

	if($user->hasPermission('inventory_all')){
		$dis = '';
	} else {
		$dis = 'disabled';
	}
?>



	<!-- Page content -->
<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
	<div class="content-header">
		<h1>
			<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Order Point </h1>

	</div>
	<?php
		// get flash message if add or edited successfully
		if(Session::exists('flash')) {
			echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
		}
	?>
	<div class="row">
		<div class="col-md-12">
				<?php 	if($user->hasPermission('orderpoint_m')) { ?>
			<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
					<a class='btn btn-default' href="addorderpoint.php">Add Order Point</a>
				</div>
				<?php } ?>
			<div class="panel panel-primary">
				<!-- Default panel contents -->

				<div class="panel-heading">Order Point</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
							<div class="input-group">
							<span class="input-group-addon"><span class='glyphicon glyphicon-search'></span></span>
							<input type="text" id="searchItemCode" class='form-control' placeholder='Search Item..'/>
							</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
							<select <?php echo $dis; ?> class="form-control" name="searchBranch" id="searchBranch">
								<option value="">--Select Branch--</option>
								<?php
									$branch = new Branch();
									$branches =  $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));
									foreach($branches as $b){
										$a = $user->data()->branch_id;
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

					</div>


					<input type="hidden" id="hiddenpage" />
					<div id="holder"></div>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->


	<script>

		$(function(){
			getPage(0,'','');
			$('body').on('click','.paging',function(){
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				var search = $('#searchItemCode').val();
				var branch = $('#searchBranch').val();
				getPage(page,search,branch);
			});
			$("#searchItemCode").keyup(function(){
				var search = $('#searchItemCode').val();
				var branch = $('#searchBranch').val();
				getPage(0,search,branch);

			});
			$('#searchBranch').change(function(){
				var search = $('#searchItemCode').val();
				var branch = $('#searchBranch').val();
				getPage(0,search,branch);
			});
			function getPage(p,search,branch){

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					data:{page:p,functionName:'orderPointPaginate',cid: <?php echo $user->data()->company_id; ?>,search:search,searchBranch :branch},
					success: function(data){
						$('#holder').empty();
						$('#holder').append(data);

					}
				});
			}

				$("body").on('click','.deleteOrderPoint',function(){
					if(confirm("Are you sure you want to delete this record? \n ")){
						id = $(this).prop('id');
						$.post('../ajax/ajax_delete.php',{id:id,table:'reorder_points'},function(data){
							if(data == "true"){
								location.reload();
							}
						});
					}
				});

		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>