<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('item_adj')){
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
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Price adjustment log
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
				<?php include 'includes/pricelist_nav.php'; ?>

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Price Adjustment Log</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<select id="branch_id" name="branch_id" class="form-control">
										<option value=''>--Select Branch--</option>
										<?php
											$branch = new Branch();
											$branches =  $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));
											foreach($branches as $b){
												?>
												<option value='<?php echo $b->id ?>'><?php echo $b->name;?> </option>
												<?php
											}
										?>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" id='search_item' class='form-control' placeholder='Search Item'>
								</div>
							</div>
						</div>
						<input type="hidden" id="hiddenpage" value=0/>
						<div id="holder"></div>
					</div>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->

	<script>

		$(document).ready(function() {
			getPage(0);
			$('body').on('click', '.paging', function(e) {
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});
			$('body').on('change','#branch_id',function(){
				getPage(0);
			});
			$('body').on('keyup','#search_item',function(){
				getPage(0);
			});
			function getPage(p) {
				var branch_id = $('#branch_id').val();
				var search_item = $('#search_item').val();
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type: 'post',
					beforeSend: function() {
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data: {
						page: p,
						branch_id:branch_id,
						search_item:search_item,
						functionName: 'itemPriceAdjustmentLogPaginate',
						cid: <?php echo $user->data()->company_id; ?>
					},
					success: function(data) {
						$('#holder').html(data);
					}
				});
			}

		})

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>