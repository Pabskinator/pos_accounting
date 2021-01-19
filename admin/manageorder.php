<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('createorder') && !$user->hasPermission('order') ) {
		// redirect to denied page
		Redirect::to(1);
	}

	$order = new Order();
	if(!$user->hasPermission('order')){
		$orders = $order->get_active('orders', array('user_id', '=', $user->data()->id));
	} else {
		$orders = $order->get_active('orders', array('company_id', '=', $user->data()->company_id));
	}



?>



	<!-- Page content -->
<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Manage Orders </h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('orderflash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('orderflash') . "</div>";
			}
		?>
		<div class="row">
			<div class="col-md-12">


				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Orders</div>
					<div class="panel-body">
						<div class="row">

							<div class="col-md-4">
								<div class="form-group">
								<div class="input-group">
									<span class="input-group-addon"><span class='glyphicon glyphicon-search'></span></span>
									<input type="text" id="searchSales" class='form-control' placeholder='Search..'/>
								</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
								<select id="branch_id" name="branch_id" class="form-control">
									<option value=''>--Select Branch--</option>
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
							<div class="col-md-4">
								<div class="form-group">
								<select id="status" name="status" class="form-control">

									<option value='1'>Pending</option>
									<option value='2'>Sold</option>
									<option value='-1'>Decline</option>
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
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style='width:95%'>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id='mtitle'></h4>
				</div>
				<div class="modal-body" id='mbody'>

				</div>

			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<script>

		$(document).ready(function() {
			$('body').on('click','.getorder',function(){
				var order_id = $(this).attr('id');
				var branch_id = localStorage['branch_id'];

				$('#myModal').modal('show');
				$('#mtitle').empty();
				$('#mtitle').append("Order ID # " + order_id);
				$.ajax({
					url: '../ajax/ajax_get_orderdetails.php',
					type:'POST',
					data:{functionName:"getOrderDetails",id:order_id,branch_id:branch_id},
					success:function(data){
						$('#mbody').empty();
						$('#mbody').append(data);
					}
				});
			});
			getPage(0,'',0,1);
			$('body').on('click','.paging',function(){
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				var search = $('#searchSales').val();
				var b = $('#branch_id').val();
				var status = $('#status').val();
				getPage(page,search,b,status);
			});
			$("#searchSales").keyup(function(){
				var search = $('#searchSales').val();
				var b = $('#branch_id').val();
				var status= $('#status').val();
				getPage(0,search,b,status);
			});
			function getPage(p,search,b,status){

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					data:{page:p,functionName:'reservationPaginate',cid: <?php echo $user->data()->company_id; ?>,search:search,b:b,status:status},
					success: function(data){
						$('#holder').empty();
						$('#holder').append(data);

					}
				});
			}
			$('body').on('change','#branch_id',function(){

				var search = $('#searchSales').val();
				var b = $('#branch_id').val();
				var status = $('#status').val();
				getPage(0,search,b,status);
			});
			$('body').on('change','#status',function(){
				var search = $('#searchSales').val();
				var b = $('#branch_id').val();
				var status = $('#status').val();
				getPage(0,search,b,status);
			});
		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>