<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';



	// select member credit paid != amount
	$mem_credit = new Member_credit();
	$mem_credit_list = $mem_credit->getPendingCredit(0);


	$cheque = new Cheque();
	$cheque_list = $cheque->getPostDated($user->data()->company_id);


?>


	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">

		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Unpaid Accounts/Post Dated Check
			</h1>
		</div>

		<div class="row">
			<div class="col-md-12">
				<?php

					echo "<h3>Upaid Credit</h3>";
					if($mem_credit_list){

						echo "<table class='table table-bordered' id='tblWithBorder'>";
						echo "<tr><th>Branch</th><th>Client Name</th><th>".  INVOICE_LABEL ."</th><th>".  DR_LABEL ."</th><th>".  PR_LABEL ."</th><th>Sold Date</th><th>Total Amount</th><th>Paid</th><th>Balance</th></tr>";

						foreach($mem_credit_list as $mc){
							$pending = $mc->amount - $mc->amount_paid;
							$inv = ($mc->invoice) ? $mc->invoice : 'N/A';
							$dr = ($mc->dr) ? $mc->dr : 'N/A';
							$ir = ($mc->ir) ? $mc->ir : 'N/A';
							echo "<tr>";
							echo "<td>".$mc->branch_name."</td>";
							echo "<td>".$mc->lastname."</td>";
							echo "<td>".$inv."</td>";
							echo "<td>".$dr."</td>";
							echo "<td>".$ir."</td>";
							echo "<td>".date('m/d/Y',$mc->sold_date)."</td>";
							echo "<td>".number_format($mc->amount,2)."</td>";
							echo "<td>".number_format($mc->amount_paid,2)."</td>";
							echo "<td>".number_format($pending,2)."</td>";
							echo "</tr>";


						}
						echo "</table>";
					}
					echo "<h3>Not yet matured Cheque</h3>";
					if($cheque_list){
						echo "<table class='table table-bordered' id='tblWithBorder2'>";
						echo "<tr><th>Branch</th><th>Client Name</th><th>".  INVOICE_LABEL ."</th><th>".  DR_LABEL ."</th><th>".  PR_LABEL ."</th><th>Bank Name</th><th>Check Number</th><th>Maturity Date</th><th>Amount</th></tr>";
						foreach($cheque_list as $ch) {
							$member_name = ($ch->mln) ? $ch->mln : 'N/A';
							$inv = ($mc->invoice) ? $mc->invoice : 'N/A';
							$dr = ($mc->dr) ? $mc->dr : 'N/A';
							$ir = ($mc->ir) ? $mc->ir : 'N/A';
							echo "<tr>";
							echo "<td>" . $ch->branch_name . "</td>";
							echo "<td>" . $member_name . "</td>";
							echo "<td>$inv</td>";
							echo "<td>$dr</td>";
							echo "<td>$ir</td>";
							echo "<td>" . $ch->bank . "</td>";
							echo "<td>" . $ch->check_number . "</td>";
							echo "<td>".date('m/d/Y',$ch->payment_date)."</td>";
							echo "<td>" .number_format($ch->amount,2) . "</td>";

							echo "</tr>";
						}
						echo "</table>";
					}
				?>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<script>
		$(document).ready(function(){

		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>