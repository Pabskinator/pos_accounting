<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('mc_liquidate_sales') || !$user->hasPermission('caravan_manage')) {
		// redirect to denied page
		Redirect::to(1);
	}

	if(Input::exists()){
		$ctype = Input::get('ctype');
		$unliq = new Unliquidated();
		$list = $unliq->getUnliquidated($user->data()->company_id,$ctype);
	} else {
		$unliq = new Unliquidated();
		$list = $unliq->getUnliquidated($user->data()->company_id,1);
	}


?>



	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Caravan Issues </h1>

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
						<div class="panel-heading">Request</div>
						<div class="panel-body">
							<form action="" method ='POST'>
								<div class="row">
									<div class="col-md-4">
										<div class="form-group">
										<select required class="form-control" name="ctype" id="ctype">
											<option value="">--Select Option--</option>
											<option value="1">Pending</option>
											<option value="2">Paid</option>
										</select>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<input type="submit" class='btn btn-default' value='Submit' name='btnSubmit'/>
										</div>
									</div>
									<div class="col-md-4"></div>
								</div>
							</form>
							<hr />
							<?php

								if($list){
									?>
									<div id="no-more-tables">
									<table class='table' id='tblIssues'>
										<thead>
										<tr>
										<th>Request Id</th>
										<th>Request by</th>
										<th>Branch</th>
										<th>Created</th>
										<th>Show Details</th>
										<th></th>
										</tr>
										</thead>
										<tbody>
									<?php
									foreach($list as $l){
										$req_user = new User($l->user_id);
										$req_branch = new Branch($l->branch_id);
									?>
										<tr>
											<td data-title='Id'><?php echo escape($l->id); ?></td>
											<td data-title='Name'><?php echo escape($req_user->data()->lastname . ", " . $req_user->data()->firstname . " " . $req_user->data()->middlename); ?></td>
											<td data-title='Branch'><?php echo escape($req_branch->data()->name); ?></td>
											<td data-title='Created'><?php echo escape(date('m/d/Y H:i:s A',$l->created)); ?></td>
											<td  data-title='Action'><input type="button" class='btn btn-default getDetails' value='Details' data-req_id='<?php echo $l->id; ?>'/></td>
											<td></td>
										</tr>
									<?php
									}
									?>
										</tbody>
									</table>
									</div>
									<?php
								} else {
									?>
									<div class="alert alert-info">No data yet.</div>
									<?php
								}
							?>
						<input type="hidden" id="hiddenpage" />
						<div id="holder"></div>
						</div>
					</div>

				</div>
			</div>
			<div id="test"></div>
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

		$(document).ready(function() {
			$('body').on('click','.getDetails',function(){
				var req_id = $(this).attr('data-req_id');
				$('#mtitle').empty();
				$('#mtitle').append("Request ID # " + req_id);
				$.ajax({
					url: '../ajax/ajax_request_issues.php',
					type:'POST',
					data:{functionName:"getDetails",id:req_id},
					success:function(data){
						$('#mbody').empty();
						$('#mbody').append(data);
						$('#myModal').modal('show');
					}
				});
			});
			$('body').on('click','.receivePayment',function(){
				var req_id = $(this).attr('data-req_id');
				$.ajax({
					url: '../ajax/ajax_request_issues.php',
					type:'POST',
					data:{functionName:"receivePayment",id:req_id},
					success:function(data){
						alert(data);
						location.reload();
					}
				});
			});
			$('body').on('change','.allRD',function(){
				var rd = $(this).val();
				var row = $(this).parents('tr');
				var orig_cost = row.attr('data-orig_cost');
				var product_cost= row.attr('data-product_cost');
				var qty = row.children().eq(3).text();
				var total = row.children().eq(4).text();
				if(rd == 1){
					row.children().eq(2).text(orig_cost);
					 total = parseFloat(orig_cost) * parseFloat(qty);
					row.children().eq(4).text(total.toFixed(2));
				} else {
					 row.children().eq(2).text(product_cost);
					 total = parseFloat(product_cost) * parseFloat(qty);

					row.children().eq(4).text(total.toFixed(2));
				}
				computeTotal();
			});
			function computeTotal(){

				var gt=0;
				$('#tbl_issues tbody tr').each(function(){
					var row = $(this);
					var total = row.children().eq(4).text();
					if(!total) total = 0;

					gt = gt + parseFloat(total);
					console.log(total);
					console.log(gt);
				});
				$('#totalissues').html(gt.toFixed(2));


			}
		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>