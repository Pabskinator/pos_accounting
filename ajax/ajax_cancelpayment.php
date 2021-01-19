
<?php
	include 'ajax_connection.php';


	$id = Input::get("id");
	$sales = new Sales();
	$list = $sales->get_active('sales',array('payment_id','=',$id));
	$user = new User();
	$rackDisplay = new Rack();
	$b = 0;
	$toadd = array();

	$pc = new Payment_consumable();
	$pcp = $pc->get_active("payment_consumable",array('payment_id','=',$id));
	if($pcp){
		foreach($pcp as $con){
			$paymentConsumable = new Payment_consumable($con->id);
			$payment_amount = $paymentConsumable->data()->amount;
			$member_id = $paymentConsumable->data()->member_id;
			$concls = new Consumable();
			$res = $concls->updateConsumable($payment_amount,$member_id);
		}
	}
	$pcf = new Payment_consumable_freebies();
	$pcfp = $pcf->get_active("payment_consumable_freebies",array('payment_id','=',$id));
	if ($pcfp){
		foreach($pcfp as $c){
			$paymentConsumable = new Payment_consumable_freebies($c->id);
			$payment_amount = $paymentConsumable->data()->amount;
			$member_id = $paymentConsumable->data()->member_id;
			$concls = new Consumable_freebies();
			$res = $concls->updateConsumable($payment_amount,$member_id);
		}
	}
	$totalcash =  $sales->getSalesTotalBaseOnPayment($id,1);
	$totalcash = ($totalcash->st) ? ($totalcash->st) : 0;
	$totalcredit =  $sales->getSalesTotalBaseOnPayment($id,2);
	$totalcredit = ($totalcredit->st) ? ($totalcredit->st) : 0;
	$totalcheque =  $sales->getSalesTotalBaseOnPayment($id,3);
	$totalcheque = ($totalcheque->st) ? ($totalcheque->st) : 0;
	$totalbt =  $sales->getSalesTotalBaseOnPayment($id,4);
	$totalbt = ($totalbt->st) ? ($totalbt->st) : 0;

	$terminal_id = 0;
	$invoice = 0;

	foreach($list as $l){
		$myterminal = new Terminal($l->terminal_id);

		if(!$terminal_id) $terminal_id =$l->terminal_id;
		if(!$invoice) $invoice =$l->invoice;
		$bb = new Branch($myterminal->data()->branch_id);
		$b = $bb->data()->id;
		$myitem = new Product($l->item_id);
		if($myitem->data()->item_type == -1){
			$toadd[$l->item_id] = $l->qtys;
		}
	}
	$terminal = new Terminal($terminal_id);
	if($totalcash){
		$col = 't_amount';
		$prev_amount = $terminal->data()->t_amount;
		$prev_amount = ($prev_amount) ? $prev_amount : 0;
		$to_amount =   $prev_amount-$totalcash;
		$now = time();
		$terminal->update(array(
			$col => $to_amount
		),$terminal_id);
		$terminal_mon = new Terminal_mon();
		$terminal_mon->create( array(
			'terminal_id' => $terminal_id,
			'user_id' => $user->data()->id,
			'from_amount' =>$prev_amount,
			'amount' =>$totalcash,
			'to_amount'=>$to_amount,
			'status' => 2,
			'remarks' => 'Cancel Sales. Invoice #' . $invoice,
			'is_active' => 1,
			'company_id' => $user->data()->company_id,
			'p_type'=>1,
			'created' => $now
		));
	}
	if($totalcredit){
		$col = 't_amount_cc';
		$prev_amount = $terminal->data()->t_amount_cc;
		$prev_amount = ($prev_amount) ? $prev_amount : 0;
		$to_amount =   $prev_amount-$totalcredit;
		$now = time();
		$terminal->update(array(
			$col => $to_amount
		),$terminal_id);
		$terminal_mon = new Terminal_mon();
		$terminal_mon->create( array(
			'terminal_id' => $terminal_id,
			'user_id' => $user->data()->id,
			'from_amount' =>$prev_amount,
			'amount' =>$totalcredit,
			'to_amount'=>$to_amount,
			'status' => 2,
			'remarks' => 'Cancel Sales. Invoice #' . $invoice,
			'is_active' => 1,
			'company_id' => $user->data()->company_id,
			'p_type'=>2,
			'created' => $now
		));
	}
	if($totalcheque){
		$col = 't_amount_ch';
		$prev_amount = $terminal->data()->t_amount_ch;
		$prev_amount = ($prev_amount) ? $prev_amount : 0;
		$to_amount =   $prev_amount-$totalcheque;
		$now = time();
		$terminal->update(array(
			$col => $to_amount
		),$terminal_id);
		$terminal_mon = new Terminal_mon();
		$terminal_mon->create( array(
			'terminal_id' => $terminal_id,
			'user_id' => $user->data()->id,
			'from_amount' =>$prev_amount,
			'amount' =>$totalcheque,
			'to_amount'=>$to_amount,
			'status' => 2,
			'remarks' => 'Cancel Sales. Invoice #' . $invoice,
			'is_active' => 1,
			'company_id' => $user->data()->company_id,
			'p_type'=>3,
			'created' => $now
		));
	}
	if($totalbt){
		$col = 't_amount_bt';
		$prev_amount = $terminal->data()->t_amount_bt;
		$prev_amount = ($prev_amount) ? $prev_amount : 0;
		$to_amount =   $prev_amount-$totalbt;
		$now = time();
		$terminal->update(array(
			$col => $to_amount
		),$terminal_id);
		$terminal_mon = new Terminal_mon();
		$terminal_mon->create( array(
			'terminal_id' => $terminal_id,
			'user_id' => $user->data()->id,
			'from_amount' =>$prev_amount,
			'amount' =>$totalbt,
			'to_amount'=>$to_amount,
			'status' => 2,
			'remarks' => 'Cancel Sales. Invoice #' . $invoice,
			'is_active' => 1,
			'company_id' => $user->data()->company_id,
			'p_type'=>4,
			'created' => $now
		));
	}




	if(count($toadd) > 0){
		$releasing = new Releasing();
		$released = $releasing->getForCancel($id);
		if($released) {
			foreach($released as $rel){
				if(isset($toadd[$rel->item_id]) && $rel->status == 1){
					unset($toadd[$rel->item_id]);
				}
			}
			$cancel_release = new Releasing();
			$cancel_release->cancelByPaymentId($id);
		}

		if(count($toadd) > 0) {
			$tranfer_mon = new Transfer_inventory_mon();
			$tranfer_mon->create(array('status' => 1, 'is_active' => 1, 'branch_id' => $b, 'company_id' => $user->data()->company_id, 'created' => time(), 'modified' => time(), 'from_where' => 'From cancelled', 'payment_id' => $id));
			$lastid = $tranfer_mon->getInsertedId();
			foreach($toadd as $i => $v) {
				$tranfer_mon_details = new Transfer_inventory_details();
				$tranfer_mon_details->create(array('transfer_inventory_id' => $lastid, 'rack_id_from' => 0, 'rack_id_to' => 0, 'item_id' => $i, 'qty' => $v, 'is_active' => 1));
			}
		}
		$sales->cancelPayment($id);

		Log::addLog(
			$user->data()->id,
			$user->data()->company_id,
			"Cancel sales PID: " . $id,
			'ajax_cancelpayment.php'
		);
	}

	echo "1";


