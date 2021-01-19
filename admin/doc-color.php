<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('sales')) {
		// redirect to denied page
		Redirect::to(1);
	}


?>



	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">

		<div class="content-header">
			<?php include 'includes/sales_nav.php'; ?>
		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}
		?>

		<div class="row">
			<div class="col-md-12">
				<?php 	if($user->hasPermission('sales')) { ?>
					<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
						<a class='btn btn-default' href='adddoccolor.php' title='Add Member'>
							<span class='glyphicon glyphicon-plus'></span> <span class='hidden-xs'>Add</span> </a>
					</div>
				<?php } ?>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Docs</div>
					<div class="panel-body">
						<div class="row">

							<div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon"><span class='glyphicon glyphicon-search'></span></span>
									<input type="text" id="search" class='form-control' placeholder='Search..'/>
								</div>
							</div>
							<div class="col-md-4">

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

			getPage(0);
			$('body').on('click','.paging',function(){
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				var search = $('#search').val();


				getPage(page,search);
			});
			$("#search").keyup(function(){
				var search = $('#search').val();


				getPage(0,search);
			});
			function getPage(p,search){
				$('.loading').show();
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					data:{page:p,functionName:'docColorList',cid: <?php echo $user->data()->company_id; ?>,search:search},
					success: function(data){
						$('#holder').empty();
						$('#holder').append(data);
						$('.loading').hide();
					},
					error: function(){
						alert('Something went wrong. The page will be refresh.');
						location.href='doc-color.php';
						$('.loading').hide();
					}
				});
			}
			$('body').on('click','.deleteDoc',function(){
				if(confirm("Are you sure you want to delete this record?")) {
					id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php', {id: id, table: 'doc_colors'}, function(data) {
						if(data == "true") {
							location.reload();
						}
					});
				}
			});
		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>