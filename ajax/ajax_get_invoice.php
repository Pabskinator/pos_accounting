<?php
	include 'ajax_connection.php';

	$s = Input::get("sales");
	$ss = json_decode($s,true);
	 $invoice =  Input::get("invoice_number");
	 $invoice +=1;
	$terminal_id =Input::get("terminal_id");

	foreach($ss as $s){
		$toInvoice = new Sales($s['sales_id']);
		$updateinv = new Sales();
		$updateinv->updateInvoiceDr($toInvoice->data()->payment_id,$invoice,$toInvoice->data()->dr,$toInvoice->data()->ir);
		$updateinv->update(array(
			'invoice' => $invoice
		), $toInvoice->data()->payment_id);
	}

	$terminal = new Terminal();
	$terminal->update(array(
		'modified' => strtotime(date('Y/m/d H:i:s')),
		'invoice' => $invoice
	), $terminal_id);


