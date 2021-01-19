<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('orderpoint')) {
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
			<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Orders </h1>

	</div>
	<?php
		// get flash message if add or edited successfully
		if(Session::exists('flash')) {
			echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
		}
	?>
	<div class="row">
		<div class="col-md-12">


			<div class="panel panel-primary">
				<!-- Default panel contents -->

				<div class="panel-heading">Order List</div>
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
							<select class="form-control" name="searchBranch" id="searchBranch">
								<option value="">--Select Branch--</option>
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
							<select class="form-control" name="searchStatus" id="searchStatus">
								<option value="">--Select Status--</option>
								<option value="1">Pending</option>
								<option value="2">Processed</option>
								<option value="3">Received</option>
							</select>
								</div>
						</div>
					</div>


					<input type="hidden" id="hiddenpage" />
					<div id="holder"></div>
				</div>
			</div>
			<?php



			?>
		</div>
	</div> <!-- end page content wrapper-->

	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style='width:90%'>
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

		$(function(){
			getPage(0,'','');
			$('body').on('click','.paging',function(){
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				var search = $('#searchItemCode').val();
				var branch = $('#searchBranch').val();
				var status = $('#searchStatus').val();
				getPage(page,search,branch,status);
			});
			$("#searchItemCode").keyup(function(){
				var search = $('#searchItemCode').val();
				var branch = $('#searchBranch').val();
				var status = $('#searchStatus').val();
				getPage(0,search,branch,status);

			});
			$('#searchBranch').change(function(){
				var search = $('#searchItemCode').val();
				var branch = $('#searchBranch').val();
				var status = $('#searchStatus').val();
				getPage(0,search,branch,status);
			});
			$('#searchStatus').change(function(){
				var search = $('#searchItemCode').val();
				var branch = $('#searchBranch').val();
				var status = $('#searchStatus').val();
				getPage(0,search,branch,status);
			});
			function getPage(p,search,branch,status){

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					data:{page:p,functionName:'ordersItemPaginate',cid: <?php echo $user->data()->company_id; ?>,search:search,searchBranch :branch,status:status},
					success: function(data){
						$('#holder').empty();
						$('#holder').append(data);

					}
				});
			}

		});
		$('body').on('click','.processOrder',function(){
			var oid = $(this).attr('data-oid');
			var name = localStorage['current_lastname'].toUpperCase() + ", " + localStorage['current_firstname'].toUpperCase();

			if(confirm(" Are you sure you want to process this order?")){
				var branch = localStorage['branch_id'];
				var company = localStorage['company_id'];
				$.ajax({
					url: '../ajax/processorder.php',
					type:'post',
					data: {oid:oid,branch:branch,company:company,name:name},
					success: function(data){
						alert(data);
						location.reload();
					}
				})
			}

		});
		$('body').on('click','.timelog',function(){
			var oid = $(this).attr('data-oid');
			$.ajax({
					url: '../ajax/order_timelog.php',
					type:'post',
					data: {oid:oid},
					success: function(data){
						$('#mbody').empty();
						$('#mbody').append(data);
						$('#myModal').modal('show');
					}
			})
		});
		$('body').on('click','.transferOrder',function(){
			var oid = $(this).attr('data-oid');
			var name = localStorage['current_lastname'].toUpperCase() + ", " + localStorage['current_firstname'].toUpperCase();
			if(confirm("Are you sure you want to transfer this order?")){
				var branch = localStorage['branch_id'];
				var company = localStorage['company_id'];
				$.ajax({
					url: '../ajax/receiveorder.php',
					type:'post',
					data: {oid:oid,branch:branch,company:company,name:name},
					success: function(data){
						alert(data);
						location.reload();
					}
				})
			}
		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>