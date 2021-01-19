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
				Manage Prices
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
					<div class="panel-heading">
						<div class="row">
							<div class="col-md-8">Item Price Adjustment</div>
							<div class="col-md-4 text-right">
								<?php if($user->hasPermission('dl_price')){ ?>
								<button class='btn btn-default btn-sm' id='btnDownload'><span class='fa fa-download'></span></button>
								<?php } ?>
							</div>
						</div>

					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-3">
								<?php
									$branch_disable = "disabled";
									if($user->hasPermission('inventory_all')){
										$branch_disable = "";
									}
								?>
								<div class="form-group">

									<select <?php echo $branch_disable; ?> id="branch_id" name="branch_id" class="form-control">
										<option value=''>--Select Branch--</option>
										<?php
											$branch = new Branch();
											$branches =  $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));
											foreach($branches as $b){
												$selected="";
												if($b->id == $user->data()->branch_id){
													$selected="selected";
												}
												?>
												<option value='<?php echo $b->id ?>' <?php echo $selected; ?>><?php echo $b->name;?> </option>
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
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" id='dt_from' class='form-control' placeholder='Date From'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" id='dt_to' class='form-control' placeholder='Date To'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select class='form-control' name="limit_by" id="limit_by">
										<option value="">Select Limit</option>
										<option value="50">50</option>
										<option value="100">100</option>
										<option value="500">500</option>
										<option value="1000">1000</option>
									</select>
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
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id='mtitle'>Edit price adjustment</h4>
					</div>
					<div class="modal-body" id='mbody'>
						<div class="form-group">
							<input type="hidden" id='edit_id'>
							<input type="hidden" id='edit_orig_adjustment'>
							<input type="hidden" id='edit_item_id'>
							<input type="hidden" id='edit_branch_id'>
							<strong>Original Price:</strong>
							<input type="text" id='edit_orig_adjustment_lbl' disabled class='form-control' >
							<strong>New Price:</strong>
							<input type="text" id='edit_adjustment' class='form-control' >
						</div>
						<div class="form-group">
							<button disabled class='btn btn-primary' id='btnSave'>Save</button>
						</div>
					</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<script>

		$(document).ready(function() {
			getPage(0);
			$('body').on('click', '.paging', function(e) {
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});
			$('body').on('change','#branch_id,#limit_by',function(){
				getPage(0);
			});
			$('body').on('keyup','#search_item',function(){
				getPage(0);
			});
			$('body').on('click','#btnDownload',function(){
				var branch_id = $('#branch_id').val();
				var search_item = $('#search_item').val();
				var limit_by = $('#limit_by').val();
				var dt_from = $('#dt_from').val();
				var dt_to = $('#dt_to').val();
				window.open(
					'excel_downloader.php?downloadName=priceList&search_item='+search_item+'&branch_id='+branch_id+'&dt_from='+dt_from+'&dt_to='+dt_to,
					'_blank' // <- This is what makes it open in a new window.
				);
			});
			function getPage(p) {
				var branch_id = $('#branch_id').val();
				var search_item = $('#search_item').val();
				var limit_by = $('#limit_by').val();
				var dt_from = $('#dt_from').val();
				var dt_to = $('#dt_to').val();
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type: 'post',
					beforeSend: function() {
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data: {
						page: p,
						branch_id:branch_id,
						dt_from:dt_from,
						dt_to:dt_to,
						search_item:search_item,
						functionName: 'itemPriceAdjustmentPaginate',
						limit_by:limit_by,
						cid: <?php echo $user->data()->company_id; ?>
					},
					success: function(data) {
						$('#holder').html(data);
					}
				});
			}
			$('body').on('click','.btnEdit',function(){

				var row = $(this).parents('tr');
				var adjustment = row.attr('data-adjustment');
				var id = row.attr('data-id');
				var item_id = row.attr('data-item_id');
				var orig_price = row.attr('data-orig-price');
				var adjusted_price = row.attr('data-adjusted-price');
				var branch_id = row.attr('data-branch_id');
				$('#edit_id').val(id);
				$('#edit_adjustment').val(adjusted_price);
				$('#edit_orig_adjustment').val(adjustment);
				$('#edit_orig_adjustment_lbl').val(orig_price);
				$('#edit_item_id').val(item_id);
				$('#edit_branch_id').val(branch_id);
				$('#myModal').modal('show');

			});
			$('body').on('click','#btnSave',function(){
				var id = $('#edit_id').val();
				var adjustment =  $('#edit_adjustment').val() - $('#edit_orig_adjustment_lbl').val();
				if(adjustment){
					adjustment = number_format(adjustment,2,'.','');
				}

				var item_id = $('#edit_item_id').val();
				var branch_id = $('#edit_branch_id').val();
				var btncon = $(this);
				var btnoldval = btncon.html();
				btncon.html('Loading...');
				btncon.attr('disabled',true);
				$.ajax({
					url: '../ajax/ajax_query2.php',
					type: 'post',
				    data: {functionName:'updateItemPricelist', item_id:item_id,branch_id:branch_id,id:id,adjustment:adjustment},
				    success: function(data){
					    alertify.alert(data);
					    $('#myModal').modal('hide');
					    getPage($('#hiddenpage').val());
					    btncon.html(btnoldval);
					    btncon.attr('disabled',true);
				    },
				    error:function(){

					    btncon.html(btnoldval);
					    btncon.attr('disabled',false);
				    }
				})

			});
			$('body').on('blur','#edit_adjustment',function(){
				var orig = $('#edit_orig_adjustment').val();
				var cur =  $('#edit_adjustment').val() - $('#edit_orig_adjustment_lbl').val();

				if(isNaN(cur)){
					alertify.alert('Invalid amount');
					$('#btnSave').attr('disabled',true);
					return;
				}

				cur = number_format(cur,2,".","");
				orig = number_format(orig,2,".","");
				if(cur == orig){
					$('#btnSave').attr('disabled',true);
				} else {
					$('#btnSave').attr('disabled',false);
				}
			});

			$('#dt_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_from').datepicker('hide');
				getPage($('#hiddenpage').val());
			});
			$('#dt_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_to').datepicker('hide');
				getPage($('#hiddenpage').val());
			});

		})

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>