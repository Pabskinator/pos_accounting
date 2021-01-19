<?php
	include 'ajax_connection.php';
	$id = Input::get('id');
	$cash = new Cash();
	$member_credit = new Member_credit();
	$credit = new Credit();
	$cheque = new Cheque();
	$bt = new Bank_transfer();
	$con = new Payment_consumable();
	$deduct = new Deduction();
	$conFree = new Payment_consumable_freebies();
	$sales = new Sales();
	$ordercls = new Order();
	$paymentcls = new Payment($id);
	$whordercls = new Wh_order();
	$whorderdetails = $whordercls->getFullDetailsByPayment($id);
	$cashlist = $cash->get_active('cash',array('payment_id','=',$id));

	$sold_item = $sales->getsinglesale($id);
	$orderref = $ordercls->get_active('orders',array('payment_id','=',$id));
	$dataorder = $orderref[0];

	$member ='';
	$station = "";
	$reservedby = "";
	if(isset($dataorder->user_id)){
		$ouser = new User($dataorder->user_id);
		$reservedby = ucfirst($ouser->data()->lastname .", " . $ouser->data()->firstname);
	}
	$memarr=[];
	$stat=[];
	if($sold_item) {

			if($sold_item->member_id) {
				$m = new Member($sold_item->member_id);
				if(!in_array($sold_item->member_id, $memarr)) {
					$memarr[] = $sold_item->member_id;
					$member .= $m->data()->lastname . ", " . $m->data()->firstname . " " . $m->data()->middlename . "<br>";
				}
				if($sold_item->station_id) {
					$st = new Station($sold_item->station_id);
					if(!in_array($sold_item->station_id, $stat)) {
						$stat[] = $sold_item->station_id;
						$station .= $st->data()->name . "<br>";
					}
				}
			}

	}

	$cashier = "None";
	if(isset($sold_item->cashier_id)){
		$u = new User($sold_item->cashier_id);
		$cashier = $u->data()->lastname . ", " .  $u->data()->firstname . " " . $u->data()->middlename;
	}
	if(!$member){
		$member ='None';
	}
	if(!$station){
		$station ='None';
	}
	?>
	<p>Cashier: <br><span class='text-danger'><?php echo ucwords($cashier)?> </span></p>
	<?php
		if($reservedby){
			?>
			<p>Reserved by:<br> <span class='text-danger'><?php echo ucwords($reservedby)?> </span></p>
			<?php
		}
		$final_remarks = "";

	if($paymentcls->data()->remarks){
		$final_remarks .= $paymentcls->data()->remarks;
	}

	if(isset($dataorder->remarks)){
		$final_remarks .= $dataorder->remarks;
	}

	if(isset($whorderdetails->remarks)){
		$final_remarks .= "<span class='span-block'>".$whorderdetails->remarks."</span>";
	}
	$sales_type_name = ($sold_item->sales_type_name) ? $sold_item->sales_type_name : 'None';
	?>
	<p>Remarks:<br> <span class='text-danger'><?php echo ($final_remarks) ?  $final_remarks : 'No remarks'?></span></p>
	<div class='row'>
		<div class="col-md-12"><?php echo MEMBER_LABEL; ?>: <br><span class='text-danger'><?php echo ucfirst($member) ?></span> </div>
		<div class="col-md-12">Station: <br><span class='text-danger'><?php echo ucfirst($station) ?></span></div>
		<div class="col-md-12">Sales type: <br><span class='text-danger'><?php echo ucfirst($sales_type_name) ?></span> </div>

	</div>

	<?php

	if($cashlist){
		?>
		<h3 class='text-danger'>Cash Payment</h3>
		<div class="row">
		<div class="col-md-12" >
		<ul class="list-group" >
		<a class="list-group-item active">Details</a>
			<?php
				foreach($cashlist as $c){
					?>
					<li class="list-group-item"><p><strong>Date:</strong><span style='color:#999'> <?php echo date('m/d/Y H:i:s A', $c->created) ?></span></p></li>
					<li class="list-group-item"><p><strong>Amount: </strong><span style='color:#999'><?php echo number_format($c->amount,2); ?></span></p></li>


					<?php
				}
			?>
		</ul></div>
		</div>
		<?php
	}
	$mem_creditlist = $member_credit->get_active('member_credit',array('payment_id','=',$id));
	if($mem_creditlist){
		?>
		<h3 class='text-danger'>Credit Payment</h3>
		<div class="row">
			<div class="col-md-12" >
				<ul class="list-group" >
					<a class="list-group-item active">Details</a>
					<?php
						foreach($mem_creditlist as $c){
							?>
							<li class="list-group-item"><p><strong>Created Date:</strong><span style='color:#999'> <?php echo date('m/d/Y H:i:s A', $c->created) ?></span></p></li>
							<li class="list-group-item"><p><strong>Updated at:</strong><span style='color:#999'> <?php echo date('m/d/Y H:i:s A', $c->modified) ?></span></p></li>
							<li class="list-group-item"><p><strong>Amount: </strong><span style='color:#999'><?php echo number_format($c->amount,2); ?></span></p></li>
							<li class="list-group-item"><p><strong>Amount Paid: </strong><span style='color:#999'><?php echo number_format($c->amount_paid,2); ?></span></p></li>
							<li class="list-group-item"><p><strong>Remaining: </strong><span style='color:#999'><?php echo number_format($c->amount - $c->amount_paid,2); ?></span></p></li>
						<?php
						}
					?>
				</ul></div>
		</div>
	<?php
	}
	$deduction = $deduct->get_active('deductions',array('payment_id','=',$id));

	if($deduction){
		?>
		<h3 class='text-danger'>Deduction</h3>
		<div class='row'>
			<div class="col-md-12" >
				<ul class="list-group">
					<a class="list-group-item active">Details</a>
					<?php
						foreach($deduction as $c){
							?>
							<li class="list-group-item"><p><strong>Date:</strong><span style='color:#999'> <?php echo date('m/d/Y H:i:s A', $c->created) ?></span></p></li>
							<li class="list-group-item"><p><strong>Amount: </strong><span style='color:#999'><?php echo number_format($c->amount,2) ?></span></p></li>
							<li class="list-group-item"><p><strong>Remarks: </strong><span style='color:#999'><?php echo $c->remarks ?></span></p></li>
							<?php
						}
					?>
				</ul></div>
		</div>
		<?php
	}
	$conlist = $con->get_active('payment_consumable',array('payment_id','=',$id));

	if($conlist){
		?>
		<h3 class='text-danger'>Consumable Amount Payment</h3>
		<div class='row'>
		<div class="col-md-12" >
		<ul class="list-group">
		<a class="list-group-item active">Details</a>

		<?php

				foreach($conlist as $c){
					?>
					<li class="list-group-item"><p><strong>Date:</strong><span style='color:#999'> <?php echo date('m/d/Y H:i:s A', $c->created) ?></span></p></li>
					<li class="list-group-item"><p><strong>Amount: </strong><span style='color:#999'><?php echo number_format($c->amount,2) ?></span></p></li>

				<?php
				}
			?>
		</ul></div>
		</div>
	<?php
	}
	$confreelist = $conFree->get_active('payment_consumable_freebies',array('payment_id','=',$id));

	if($confreelist){
		?>
		<h3 class='text-danger'>Consumable Amount for Freebies</h3>
		<div class='row'>
			<div class="col-md-12" >
				<ul class="list-group">
					<a class="list-group-item active">Details</a>

					<?php

						foreach($confreelist as $c){
							?>
							<li class="list-group-item"><p><strong>Date:</strong><span style='color:#999'> <?php echo date('m/d/Y H:i:s A', $c->created) ?></span></p></li>
							<li class="list-group-item"><p><strong>Amount: </strong><span style='color:#999'><?php echo number_format($c->amount,2); ?></span></p></li>

						<?php
						}
					?>
				</ul></div>
		</div>
	<?php
	}
	$creditlist = $credit->get_active('credit_card',array('payment_id','=',$id));
	if($creditlist){
		?>
		<h3 class='text-danger'>Credit Card Payment</h3>
		<div class="row">
			<?php
				 $count = 1;
				foreach($creditlist as $c){
					?>
					<?php if($count == 1) {
						?>
						<div class="col-md-12">
						<?php
					} else if ($count == 2){
						?>
						<div class="col-md-6">
						<?php
					}else {
						?>
						<div class="col-md-4">
						<?php
					}?>

						<ul class="list-group">
							<a class="list-group-item active">Details</a>
							<li class="list-group-item"><p><strong>Card Holder: </strong><span style='color:#999'><?php echo  ucwords($c->lastname .", ". $c->firstname. " ".  $c->middlename); ?> </span></p></li>
							<li class="list-group-item"><p><strong>Card Number: </strong><span style='color:#999'><?php echo $c->card_number ?></span></p></li>
							<li class="list-group-item">	<p><strong>Bank: </strong><span style='color:#999'><?php echo $c->bank_name ?></span></p></li>
							<li class="list-group-item"><p><strong>Address: </strong><span style='color:#999'><?php echo $c->address ?></span></p></li>
							<li class="list-group-item"><p><strong>Zip/Postal: </strong><span style='color:#999'><?php echo $c->trace_number ?></span></p></li>
							<li class="list-group-item"><p><strong>Company: </strong><span style='color:#999'><?php echo $c->company ?></span></p></li>
							<li class="list-group-item"><p><strong>Contact Number: </strong><span style='color:#999'><?php echo $c->contacts ?></span></p></li>
							<li class="list-group-item"><p><strong>Email: </strong><span style='color:#999'><?php echo $c->email ?></span></p></li>
							<li class="list-group-item"><p><strong>Date: </strong><span style='color:#999'><?php echo date('m/d/Y H:i:s A', $c->created) ?></span></p>
							</li>
							<li class="list-group-item"><p><strong>Amount: </strong><span style='color:#999'><?php echo number_format($c->amount,2); ?></span></p></li>
						</ul>
					</div>











				<?php
				}
			?>
		</div>

	<?php
	}

	$chequelist = $cheque->get_active('cheque',array('payment_id','=',$id));
	if($chequelist){
		?>
		<h3 class='text-danger'>Cheque Payment</h3>

		<?php
			$count = count($chequelist);
			$arr_cheque_status = ['','Good','DAIF','Bounce','Others'];
		foreach($chequelist as $c){
				if($c->status == 1){
					$bgcheque = '';
				} else {
					$bgcheque = '';
				}
			?>

				<ul class="list-group">
					<a class="list-group-item active">Details <strong class='text-danger'><?php echo isset($arr_cheque_status[$c->status]) ? $arr_cheque_status[$c->status] : ''; ?></strong></a>

					<li class="list-group-item"><p><strong>Name: </strong><span style='color:#999'><?php echo  ucwords($c->lastname .", ". $c->firstname. " ".  $c->middlename); ?> </span></p></li>
					<li class="list-group-item">	<p><strong>Cheque Number: </strong><span style='color:#999'><?php echo $c->check_number ?></span></p></li>
					<li class="list-group-item"><p><strong>Bank: </strong><span style='color:#999'><?php echo $c->bank ?></span></p></li>
					<li class="list-group-item"><p><strong>Contact Number: </strong><span style='color:#999'><?php echo $c->contacts ?></span></p></li>
					<li class="list-group-item"><p><strong>Payment Date: </strong><span style='color:#999'><?php echo date('m/d/Y H:i:s A', $c->payment_date) ?></span></p></li>
					<li class="list-group-item"><p><strong>Amount: </strong><span style='color:#999'><?php echo number_format($c->amount,2); ?></span></p></li>
				</ul>


		<?php
		}
		?>

	<?php
	}

	$btlist = $bt->get_active('bank_transfer',array('payment_id','=',$id));
	if($btlist){
		?>
		<h3 class='text-danger'>Bank Transfer Payment</h3>
		<div class="row">
		<?php
			$count = count($btlist);
		foreach($btlist as $c){
			?>
		<?php if($count == 1) {
		?>
		<div class="col-md-12">
		<?php
			} else if ($count == 2){
		?>
		<div class="col-md-6">
		<?php
			}else {
		?>
		<div class="col-md-4">
			<?php
				}
			?>
				<ul class="list-group">
					<a class="list-group-item active">Details</a>
					<li class="list-group-item"><p><strong>Name: </strong><span style='color:#999'><?php echo  ucwords($c->lastname .", ". $c->firstname. " ".  $c->middlename); ?> </span></p></li>
					<li class="list-group-item"><p><strong>Account Number: </strong><span style='color:#999'><?php echo $c->bankfrom_account_number ?></span></p></li>
					<li class="list-group-item"><p><strong>Bank: </strong><span style='color:#999'><?php echo $c->bankfrom_name ?></span></p></li>
					<li class="list-group-item"><p><strong>Transfer to Account Number: </strong><span style='color:#999'><?php echo $c->bankto_account_number ?></span></p></li>
					<li class="list-group-item"><p><strong>Bank: </strong><span style='color:#999'><?php echo $c->bankto_name ?></span></p></li>
					<li class="list-group-item"><p><strong>Address: </strong><span style='color:#999'><?php echo $c->address ?></span></p></li>
					<li class="list-group-item"><p><strong>Contact Number: </strong><span style='color:#999'><?php echo $c->contacts ?></span></p></li>
					<li class="list-group-item"><p><strong>Created: </strong><span style='color:#999'><?php echo date('m/d/Y H:i:s A', $c->created) ?></span></p></li>
					<li class="list-group-item"><p><strong>Date: </strong><span style='color:#999'><?php echo date('m/d/Y H:i:s A', $c->date) ?></span></p></li>
					<li class="list-group-item"><p><strong>Amount: </strong><span style='color:#999'><?php echo number_format($c->amount,2) ?></span></p></li>

			</ul>
			</div>
		<?php
		}
		?>
		</div>
	<?php
	}