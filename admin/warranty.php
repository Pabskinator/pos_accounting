<?php
	// $user have all the properties and method of the current user

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
	<div class="row">
		<div class="col-md-12">
			<?php include 'includes/sales_nav.php'; ?>
		</div>
	</div>


	<div class="content-header">

		<h1>
			<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Warranty </h1>


	</div>
	<?php
		// get flash message if add or edited successfully
		if(Session::exists('flash')) {
			echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
		}
	?>
	<div id="test"></div>
	<div class="row">
		<div class="col-md-12">


			<div class="panel panel-primary">
				<!-- Default panel contents -->
				<div class="panel-heading">Warranty</div>
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
									<option value='-1'>Caravan</option>
								</select>
							</div>
						</div>
						<div class="col-md-3"><div id='terminalitemholder'></div></div>


					</div>

					<input type="hidden" id="hiddenpage" />
					<div id="holder"></div>

				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->

	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style='width:95%;'>
			<div class="modal-content" >
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Payment Details</h4>
				</div>
				<div class="modal-body" id='mbody'>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<script>
		//$('#tblSales').dataTable({
		//	iDisplayLength: 50
		//});
		$(function(){
			getPage(0);

			$('body').on('click','.paging',function(e){
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(0);
			});

			$('body').on('keyup','#searchSales',function(){
				getPage(0);
			});
			$('body').on('change','#branch_id',function(){
				getPage(0);
			});
			function getPage(p){
				$('.loading').show();
				var s = $('#searchSales').val();
				var branch_id = $('#branch_id').val();

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					data:{page:p,functionName:'warrantyPaginate',cid: <?php echo $user->data()->company_id; ?>,s:s,branch_id:branch_id},
					success: function(data){
						$('#holder').empty();
						$('#holder').append(data);
						$('.loading').hide();
					},
					error:function(){
						alert('Something went wrong. The page will be refresh.');
						location.href='sales.php';
						$('.loading').hide();
					}
				});
			}

			$("body").on('click','.paymentDetails',function(){
				var payment_id = $(this).attr('data-payment_id');
				$.ajax({
					url: '../ajax/ajax_paymentDetails.php',
					type: 'POST',
					data: {id:payment_id},
					success: function(data){
						$("#mbody").html(data);
						$("#myModal").modal('show');
					}
				});
			});



		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>