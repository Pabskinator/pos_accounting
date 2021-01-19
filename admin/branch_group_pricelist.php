<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('branch')){
		// redirect to denied page
		Redirect::to(1);
	}


	$branch_group = new Branch_group();
	$groups = $branch_group->get_active('branch_groups',['1','=','1']);


?>



	<!-- Page content -->
	<div id="page-content-wrapper">


	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
	<div class="content-header">
		<h1>
			<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
			Branch Group Price list
		</h1>
	</div>

	<div class="row">
		<div class="col-md-12 text-right">
			<a href="branch_group.php" class='btn btn-default'>Group List</a>
			<a href="add_branch_group.php" class='btn btn-default'>Add Item to Group</a>
		</div>
	</div>
	<br>

	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
				<!-- Default panel contents -->
				<div class="panel-heading">List</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-3">
							<select name="group_id" id="group_id" class='form-control'>

								<?php
									if($groups){
										foreach($groups as $gr){
											echo "<option value='$gr->id'>$gr->name</option>";
										}
									}
								?>
							</select>
						</div>
						<div class="col-md-3">
							<input type="text" class='form-control' id='search' placeholder='Search Record'>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<button class='btn btn-default' id='btnSubmit'>Submit</button>
							</div>
						</div>
						<div class="col-md-3 text-right">
							<div class="form-group">
								<button class='btn btn-primary' id='btnDownload'>Download</button>
							</div>
						</div>
					</div>
					<br>

					<input type="hidden" id="hiddenpage" />
					<div id="holder"></div>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function(){
			getPage(0);
			$('body').on('click','#btnSubmit',function(){
				getPage(0);

			});

			$('body').on('click','#btnDownload',function(){
				var group_id = $('#group_id').val();
				var search = $('#search').val();

				window.open(
					'excel_downloader_2.php?downloadName=branchGroups&search='+search+'&branch_group_id='+group_id,
					'_blank' //
				);
			});

			function getPage(p) {
				var group_id = $('#group_id').val();
				var search = $('#search').val();

				$.ajax({
					url: '../ajax/ajax_paging_2.php', type: 'post',
					beforeSend: function() {
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					}, data: {
						page: p, functionName: 'branchGroups', branch_group_id: group_id,search:search,
					}, success: function(data) {
						$('#holder').html(data);
					}
				});
			}


		});




	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>