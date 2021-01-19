<?php
	// $user have all the properties and method of the current user
	require_once '../includes/admin/page_head2.php';

	$service_request_item = new Service_request_item();

	$branch_id = 0;
	$date_from = 0;
	$date_to = 0;

	if(Input::exists()){
		$branch_id = Input::get('branch_id');
		$date_from = Input::get('date_from');
		$date_to = Input::get('date_to');
	}

	$list =  $service_request_item->listItem(2,$date_from,$date_to,$branch_id);




?>

	<!-- Page content -->
	<div id="page-content-wrapper">
		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>Items not liquidated yet</h1>
			</div>

			<?php include 'includes/service_nav.php'; ?>
	<div class="panel panel-primary">
		<!-- Default panel contents -->
		<div class="panel-heading">List</div>
		<div class="panel-body">
			<div id=''>
				<form action="" method="POST">
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<input type="text" class='form-control' id='date_from' name='date_from' placeholder="Date From">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<input type="text" class='form-control' id='date_to' name='date_to' placeholder="Date To">
							</div>
						</div>
						<div class="col-md-3">
							<input type="text" class='form-control' id='branch_id' name='branch_id'>
						</div>
						<div class="col-md-3">
							<input type="submit" value='Submit' name='btnSubmit' class='btn btn-default'>
						</div>
					</div>
				</form>

				<?php
					if($list){
						?>
						<table class="table table-bordered" >
							<thead>
							<tr><th>Service Id</th><th>Item</th><th>Quantity</th><th>Price</th><th>Total</th><th>Requested at</th><th></th></tr>
							</thead>
							<tbody>
								<?php
									$prev = "";
									$total_all = 0;
									foreach($list as $l){
										$id = "";
										$branch_name = "";
										$name = "";
										$member_name = "";
										$prod = new Product();
										$price = $prod->getPrice($l->item_id);
										if($prev != $l->service_id){
											$border = "border-top:1px solid #ccc;";
											$id = $l->service_id;
											$branch_name = $l->branch_name;
											$member_name = $l->member_name;
											$name = ucwords($l->firstname . " " . $l->lastname);
										} else {
											$border = "";
										}
										$prev = $l->service_id;
										$total = $price->price * $l->qty;
										$total_all += $total;
										?>
										<tr>
											<td style='<?php echo $border; ?>'>
												<?php echo $id; ?>
												<small class='span-block '><?php echo $member_name; ?></small>
												<small class='span-block'><?php echo $branch_name; ?></small>
												<small class='span-block text-danger'><?php echo $name; ?></small>

											</td>

											<td style='<?php echo $border; ?>'>
												<?php echo $l->item_code; ?>
												<small class='text-danger span-block'>
													<?php echo $l->description; ?>
												</small>
											</td>
											<td style='<?php echo $border; ?>'>
												<?php echo formatQuantity($l->qty); ?>
											</td>
											<td style='<?php echo $border; ?>' class='text-right'>
												<?php echo number_format($price->price,2); ?>
											</td>
											<td style='<?php echo $border; ?>' class='text-right'>
												<?php echo number_format($total,2); ?>
											</td>
											<td style='<?php echo $border; ?>'>
												<?php

													$c =  $l->created;

													if(!$c){
														$c = $l->created_at;
													}
													$dt =  date('m/d/Y H:i:s A',$c);
													echo $dt;
													$dayspending = getDays(date('m/d/Y',$c));
													$dayspending = abs($dayspending);
													$lbl="day";

													if($dayspending > 1){
														$lbl.="s";
													}

												?>
											</td>
											<?php
												$bgdg ="";
												if($dayspending >= 7){
													$bgdg="bg-danger text-danger";
												}
											?>
											<td class='<?php echo $bgdg; ?>' style='<?php echo $border; ?>'>
												<?php
													echo $dayspending. " $lbl pending";
												?>
											</td>
										</tr>
										<?php
									}
								?>
							</tbody>
						</table>
						<p>Total: <strong><?php echo number_format($total_all,2); ?></strong></p>
					<?php
					} else {
						?>
						<div class="alert alert-info">No record found.</div>
				<?php
					}
				?>
			</div>
			</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->

	<script>
		$(function() {
			$('#date_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#date_from').datepicker('hide');
			});

			$('#date_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#date_to').datepicker('hide');
			});
			$('#branch_id').select2({
				placeholder: 'Branch',
				allowClear: true,
				minimumInputLength: 2,
				ajax: {
					url: '../ajax/ajax_json.php',
					dataType: 'json',
					type: "POST",
					quietMillis: 50,
					data: function (term) {
						return {
							q: term,
							functionName:'branches'
						};
					},
					results: function (data) {
						return {
							results: $.map(data, function (item) {
								return {
									text: item.name ,
									slug: item.name ,
									id: item.id
								}
							})
						};
					}
				}
			});

		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>