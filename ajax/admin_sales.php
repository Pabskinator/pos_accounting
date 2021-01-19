<?php
	include 'ajax_connection.php';

	$sales = new Sales();
	$saleslist = $sales->get_active('sales', array('company_id', '=', $user->data()->company_id));
	if ($saleslist){
		foreach($saleslist as $s) {

			$cashier = new User($s->cashier_id);
			$pd = new Product($s->item_id);
			$price = $pd->getPriceByPriceId($s->price_id);
			?>
			<tr>
				<td><span class='badge'><?php echo escape($s->invoice) ?></span></td>
				<td ><?php echo escape($pd->data()->barcode) ?></td>
				<td><?php echo escape($pd->data()->item_code) ?></td>
				<td><?php echo escape($price->price); ?>
				</td>
				<td><?php echo escape($s->qtys) ?></td>
				<td><?php echo escape($s->discount)?></td>
				<td><?php echo escape(($s->qtys*$price->price)-$s->discount)?></td>
				<td><?php echo ucfirst(escape($cashier->data()->lastname . ", " . $cashier->data()->firstname)) ?></td>
			</tr>
		<?php

	}
	}
?>