
<?php
	include 'ajax_connection.php';


	$memid = 17;
	$payment_con = 4000;
	if($payment_con){
		// insert cash

		$mem = new Member();
		// loop all valid con
		$mycon = $mem->getMyConsumableAmount($memid);
		if($mycon){
				dump($mycon);
			foreach($mycon as $c){
				$notvalid = $mem->getNotYetValidCheque($c->payment_id);
				if($notvalid->cheque_amount){
					 $validamount = $c->amount - $notvalid->cheque_amount;
				}
			}
		}

	}


