<?php
	include 'ajax_connection.php';

	$payment_id =  Input::get('payment_id');
	 $payment_method = Input::get('payment_method');
	switch($payment_method){
		case 1:
			echo "<p class='text-danger'>Cash Payment</p>"
			;break;
		case 2:
			echo "<p class='text-danger'>Credit Card Payment</p>"
			;break;
		case 3:
			echo "<p class='text-danger'>Bank Transfer Payment</p>"
			;break;
		case 4:
			echo "<p class='text-danger'>Cheque Payment</p>"
			;break;
		case 5:
			echo "<p class='text-danger'>Consumable Amount Payment</p>"
			;break;
	}
	if($payment_id){
		// get sales base on payment ID
	$ss = new Sales();
		$t = $ss->salesTransactionBaseOnPaymentId($payment_id);
		if($t){
			?>
			<table class="table">
				<tr>
					<TH>Invoice</TH>
					<TH>Dr</TH>
					<TH>Barcode</TH>
					<TH>Item Code</TH>
					<TH>Price</TH>
					<TH>Qty</TH>
					<TH>Discount</TH>
					<TH>Total</TH>
					<TH>Date sold</TH>
					<TH>Cashier</TH>
					<TH>Sold To</TH>
				</tr>


			<?php
			foreach($t as $s){
				$cashier = new User($s->cashier_id);
				$soldto = new Member($s->member_id);
				$pd = new Product($s->item_id);
				$price = $pd->getPriceByPriceId($s->price_id);
				$sss = new Sales();
				?>
				<tr >
					<td><span class='badge'>
									<?php echo ($s->invoice) ? escape($s->invoice) : "No invoice"; ?>
								</span>
					</td>
					<td><?php echo ($s->dr) ? escape($s->dr): "No Dr" ?></td>
					<td><?php echo escape($pd->data()->barcode) ?></td>
					<td><?php echo escape($pd->data()->item_code) ?></td>
					<td><?php echo escape($price->price); ?>
					</td>
					<td><?php echo escape($s->qtys) ?></td>
					<td><?php echo escape($s->discount) ?></td>
					<td><?php echo escape(($s->qtys * $price->price) - $s->discount) ?></td>
					<td><?php echo escape(date('m/d/Y ',$s->sold_date)); ?></td>
					<td><?php echo ucfirst(escape($cashier->data()->lastname . ", " . $cashier->data()->firstname)) ?></td>
					<td><?php echo ucfirst(escape($soldto->data()->lastname . ", " . $soldto->data()->firstname)) ?></td>
				</tr>
			<?php
			}
			?>
			</table>
				<?php
		}
	}

	$id = $payment_id;
	$cash = new Cash();
	$credit = new Credit();
	$cheque = new Cheque();
	$bt = new Bank_transfer();
	$con = new Payment_consumable();
	$cashlist = $cash->get_active('cash',array('payment_id','=',$id));
	if($payment_method != 1) {
		if($cashlist) {
			?>
			<p class='text-danger'>Cash Payment</p>
			<div class="row">
				<div class="col-md-12">
					<ul class="list-group">
						<a class="list-group-item active">Details</a>
						<?php
							foreach($cashlist as $c) {
								?>
								<li class="list-group-item"><p>
										<strong>Date:</strong><span style='color:#999'> <?php echo date('m/d/Y H:i:s A', $c->created) ?></span>
									</p></li>
								<li class="list-group-item"><p>
										<strong>Amount: </strong><span style='color:#999'><?php echo $c->amount ?></span>
									</p></li>


							<?php
							}
						?>
					</ul>
				</div>
			</div>
		<?php
		}
	}
	if($payment_method != 5) {


		$conlist = $con->get_active('payment_consumable', array('payment_id', '=', $id));

		if($conlist) {
			?>
			<p class='text-danger'>Consumable Amount Payment</p>
			<div class='row'>
				<div class="col-md-12">
					<ul class="list-group">
						<a class="list-group-item active">Details</a>

						<?php

							foreach($conlist as $c) {
								?>
								<li class="list-group-item"><p>
										<strong>Date:</strong><span style='color:#999'> <?php echo date('m/d/Y H:i:s A', $c->created) ?></span>
									</p></li>
								<li class="list-group-item"><p>
										<strong>Amount: </strong><span style='color:#999'><?php echo $c->amount ?></span>
									</p></li>

							<?php
							}
						?>
					</ul>
				</div>
			</div>
		<?php
		}
	}
	if($payment_method != 2) {
		$creditlist = $credit->get_active('credit_card', array('payment_id', '=', $id));
		if($creditlist) {
			?>
			<p class='text-danger'>Credit Card Payment</p>
			<div class="row">
			<?php
			$count = count($creditlist);
			foreach($creditlist as $c) {
				?>
				<?php if($count == 1) {
					?>
					<div class="col-md-12">
				<?php
				} else if($count == 2) {
					?>
					<div class="col-md-6">
				<?php
				} else {
					?>
					<div class="col-md-4">
				<?php
				}?>

				<ul class="list-group">
					<a class="list-group-item active">Details</a>
					<li class="list-group-item"><p>
							<strong>Card Holder: </strong><span style='color:#999'><?php echo ucwords($c->lastname . ", " . $c->firstname . " " . $c->middlename); ?> </span>
						</p></li>
					<li class="list-group-item"><p>
							<strong>Card Number: </strong><span style='color:#999'><?php echo $c->card_number ?></span>
						</p></li>
					<li class="list-group-item"><p>
							<strong>Bank: </strong><span style='color:#999'><?php echo $c->bank_name ?></span></p></li>
					<li class="list-group-item"><p>
							<strong>Address: </strong><span style='color:#999'><?php echo $c->address ?></span></p></li>
					<li class="list-group-item"><p>
							<strong>Zip/Postal: </strong><span style='color:#999'><?php echo $c->zip ?></span></p></li>
					<li class="list-group-item"><p>
							<strong>Company: </strong><span style='color:#999'><?php echo $c->company ?></span></p></li>
					<li class="list-group-item"><p>
							<strong>Contact Number: </strong><span style='color:#999'><?php echo $c->contacts ?></span>
						</p></li>
					<li class="list-group-item"><p>
							<strong>Email: </strong><span style='color:#999'><?php echo $c->email ?></span></p></li>
					<li class="list-group-item"><p>
							<strong>Date: </strong><span style='color:#999'><?php echo date('m/d/Y H:i:s A', $c->created) ?></span>
						</p>
					</li>
					<li class="list-group-item"><p>
							<strong>Amount: </strong><span style='color:#999'><?php echo $c->amount ?></span></p></li>
				</ul>
				</div>
			<?php
			}
			?>
			</div>

		<?php
		}
	}
	if($payment_method != 4) {
	$chequelist = $cheque->get_active('cheque', array('payment_id', '=', $id));
if($chequelist){
	?>
	<p class='text-danger'>Cheque Payment</p>
	<div class="row">
	<?php
		$count = count($chequelist);
		foreach ($chequelist as $c){

	?>
	<?php if ($count == 1) {
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
				<li class="list-group-item"><p>
						<strong>Name: </strong><span style='color:#999'><?php echo ucwords($c->lastname . ", " . $c->firstname . " " . $c->middlename); ?> </span>
					</p></li>
				<li class="list-group-item"><p>
						<strong>Cheque Number: </strong><span style='color:#999'><?php echo $c->check_number ?></span>
					</p></li>
				<li class="list-group-item"><p>
						<strong>Bank: </strong><span style='color:#999'><?php echo $c->bank ?></span></p></li>
				<li class="list-group-item"><p>
						<strong>Contact Number: </strong><span style='color:#999'><?php echo $c->contacts ?></span></p>
				</li>
				<li class="list-group-item"><p>
						<strong>Payment Date: </strong><span style='color:#999'><?php echo date('m/d/Y H:i:s A', $c->payment_date) ?></span>
					</p></li>
				<li class="list-group-item"><p>
						<strong>Amount: </strong><span style='color:#999'><?php echo $c->amount ?></span></p></li>
			</ul>

		</div>
		<?php
			}
		?>
	</div>
<?php
}
}
	if($payment_method != 3) {
		$btlist = $bt->get_active('bank_transfer', array('payment_id', '=', $id));
		if($btlist) {
			?>
			<p class='text-danger'>Bank Transfer Payment</p>
			<div class="row">
			<?php
			$count = count($btlist);
		foreach($btlist as $c){
			?>
			<?php if ($count == 1) {
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
					<li class="list-group-item"><p>
							<strong>Name: </strong><span style='color:#999'><?php echo ucwords($c->lastname . ", " . $c->firstname . " " . $c->middlename); ?> </span>
						</p></li>
					<li class="list-group-item"><p>
							<strong>Account Number: </strong><span style='color:#999'><?php echo $c->bankfrom_account_number ?></span>
						</p></li>
					<li class="list-group-item"><p>
							<strong>Bank: </strong><span style='color:#999'><?php echo $c->bankfrom_name ?></span></p>
					</li>
					<li class="list-group-item"><p>
							<strong>Transfer to Account Number: </strong><span style='color:#999'><?php echo $c->bankto_account_number ?></span>
						</p></li>
					<li class="list-group-item"><p>
							<strong>Bank: </strong><span style='color:#999'><?php echo $c->bankto_name ?></span></p>
					</li>
					<li class="list-group-item"><p>
							<strong>Address: </strong><span style='color:#999'><?php echo $c->address ?></span></p></li>
					<li class="list-group-item"><p>
							<strong>Contact Number: </strong><span style='color:#999'><?php echo $c->contacts ?></span>
						</p></li>
					<li class="list-group-item"><p>
							<strong>Date: </strong><span style='color:#999'><?php echo date('m/d/Y H:i:s A', $c->created) ?></span>
						</p></li>
					<li class="list-group-item"><p>
							<strong>Amount: </strong><span style='color:#999'><?php echo $c->amount ?></span></p></li>

				</ul>
			</div>
		<?php
		}
			?>
			</div>
		<?php
		}
	}
?>