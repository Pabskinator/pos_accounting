<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('unit')) {
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
			Unmatched
			</h1>

		</div>

		<div class="row">
			<div class="row">
				<form action="" method="POST">
				<div class="col-md-3">
					<input type="text" id='branch_id' name='branch_id' class='form-control'>
				</div>
				<div class="col-md-3">
					<input type="text" autocomplete="off" id='dt_from' name='dt_from' class='form-control' placeholder='Date From'>
				</div>
				<div class="col-md-3">
					<input type="text" autocomplete="off" id='dt_to' name='dt_to' class='form-control' placeholder='Date To'>
				</div>
				<div class="col-md-3">
					<input type="submit" class='btn btn-default' name='btnSubmit'>
				</div>
				</form>
			</div>
			<?php
				$sales = new Sales();
				if(Input::exists()){
					$branch_id = Input::get('branch_id');
					$dt_from = strtotime(Input::get('dt_from'));
					$dt_to = strtotime(Input::get('dt_to') . " 1 day -1 sec");
					$inconsistent = $sales->getInconsistentData($user->data()->company_id,$branch_id,$dt_from,$dt_to);
					if($inconsistent){

						echo "<div class='well'>";
						echo "<p class='text-danger'><strong>You have unmatched sales total and payment total.</strong></p>";
						echo "<hr>";
						foreach($inconsistent as $incon){
							$invlabel='';
							$drlabel ='';
							$irlabel ='';
							$srlabel ='';
							if($incon->invoice){
								$invlabel="Invoice#".$incon->invoice;
							}
							if($incon->dr){
								$drlabel="Dr#".$incon->dr;
							}
							if($incon->ir){
								$irlabel="Ir#".$incon->ir;
							}
							if($incon->sr){
								$srlabel="SR#".$incon->sr;
							}
							$sdunmatched = date('m/d/Y',$incon->sold_date);
							$alltotal = $incon->cashamount + $incon->chequeamount + $incon->btamount + $incon->ccamount + $incon->mcamount + $incon->pcamount+ $incon->pcfamount+ $incon->deduction;

							if($alltotal == 0.00){
								echo "<h5>Add Credit $incon->member_id</h5>";
								//$pmem = new Member_credit();

								$now = $incon->sold_date;

								$arr_update = array(
									'amount' => $incon->ttotal,
									'is_active' => 1,
									'created' => $now,
									'modified' => $now,
									'payment_id' => $incon->payment_id,
									'member_id' => $incon->member_id
								);

								dump($arr_update);

								//$pmem->create($arr_update);

							}

							$alltotal = number_format($alltotal,2);
							echo "<p>$incon->payment_id $sdunmatched $invlabel $drlabel $irlabel$srlabel  Sales Total=".$incon->ttotal.", Payment Total=$alltotal <a class='btn btn-default pull-right' href='sales_crud.php?id=".Encryption::encrypt_decrypt('encrypt',$incon->payment_id)."'><span class='glyphicon glyphicon-pencil'> Edit</a></p>";

							echo "<p><small>(Cash=<span class='text-danger'>$incon->cashamount</span>, Cheque=<span class='text-danger'>$incon->chequeamount</span>, Credit Card=<span class='text-danger'>$incon->ccamount</span>, Bank Transfer=<span class='text-danger'>$incon->btamount</span>, Member Credit=<span class='text-danger'>$incon->mcamount</span>, Consumable=<span class='text-danger'>$incon->pcamount</span>, Consumable freebies=<span class='text-danger'>$incon->pcfamount</span>, Deduction=<span class='text-danger'>$incon->deduction</span>)</small></p>";
							echo "<hr>";
						}
						echo "</div>";
					}
				}
			?>

		</div>
	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function() {
			$('#dt_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('dt_from').datepicker('hide');
			});
			$('#dt_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_to').datepicker('hide');
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