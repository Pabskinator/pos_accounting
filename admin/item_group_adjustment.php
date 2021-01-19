<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('item_adj')){
		// redirect to denied page
		Redirect::to(1);
	}

	$group_adjustment = new Group_adjustment_optional();
	$groups = $group_adjustment->getRecord(1);

?>



	<!-- Page content -->
	<div id="page-content-wrapper">




	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Group Price Adjustment
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
				<div class="btn-group hidden-xs" role="group" aria-label="..." style='margin-bottom:10px;'>
					<a class='btn btn-default' href='item_group_adjustment.php' title='List'> <span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>Group Adjustment</span> </a>
					<a class='btn btn-default' href='add_item_group_adjustment.php' title='Add adjustment'> <span class='glyphicon glyphicon-plus'></span> <span class='hidden-xs'>Add  Group Adjustment</span> </a>
				</div>

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Group Adjustment Log</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<select name="group_id" id="group_id" class='form-control'>
										<?php
											if($groups){
												foreach($groups as $gr){
													?>
													<option value="<?php echo $gr->id; ?>"><?php echo $gr->name; ?></option>
													<?php
												}
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

			$('body').on('change','#group_id',function(){

				getPage(0);

			});


			$('body').on('keyup','#search_item',function(){

				getPage(0);

			});

			function getPage(p) {
				var group_id = $('#group_id').val();
				var search_item = $('#search_item').val();
				$.ajax({
					url: '../ajax/ajax_paging_2.php',
					type: 'post',
					beforeSend: function() {
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data: {
						page: p,
						group_id:group_id,
						search_item:search_item,
						functionName: 'groupAdjustment',
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