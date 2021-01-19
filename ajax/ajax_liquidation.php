<?php
	include 'ajax_connection.php';
	$req_id = Input::get('request_id');
	$toliq = json_decode(Input::get('toliq'));
	$soli = json_decode(Input::get('soli'));
	$finalPayment = json_decode(Input::get('finalPayment'),true);


	$caravan = new Caravan_liquidation();
	$arrayPayment = ['payment_cash','payment_con','payment_con_freebies','payment_member_credit','payment_cheque','payment_bt','payment_credit'];

	$now = time();
	$myreq = new Agent_request();
	$akoto = new User();
	if($toliq) {
		$sr = [];
		$rempersr = [];
		//rearrange
		$srPayment = [];

		foreach($finalPayment as $indPayment) {
			$srPayment[$indPayment['sr']][$indPayment['payment']] = $indPayment['value'];
		}

		foreach($toliq as $liq) {
			if($liq) {
				// start
				if($liq->remarks) {
					if(isset($rempersr[$liq->sr])) {
						if(!in_array($liq->remarks, $rempersr[$liq->sr])) {
							$rempersr[$liq->sr][] = $liq->remarks;
						}
					} else {
						$rempersr[$liq->sr][] = $liq->remarks;
					}

				}

				$sr[$liq->sr][] = array('agent_request_id' => $req_id, 'item_id' => $liq->item_id, 'qty' => $liq->qty, 'member_id' => $liq->member_id, 'price_id' => $liq->price_id, 'created' => $now, 'modified' => $now, 'agent_id' => $akoto->data()->id, 'is_active' => 1, 'discount' => $liq->discount, 'sr' => $liq->sr, 'sold_date' => strtotime($liq->sold_date), 'remarks' => $liq->remarks,);
				//end test
			}
		}

		//dump($sr);
		foreach($sr as $srnum => $s) {
			//payment

			$first = $s[0];
			$stats = new Station();
			$smemid = $stats->getMemberId($first['member_id']);
			$remfinal = "";

			if(count($rempersr[$srnum]) > 0) {

				foreach($rempersr[$srnum] as $rem) {
					$remfinal .= $rem . ",";
				}

			}
			$remfinal = rtrim($remfinal, ',');

			$payment = new Payment();
			$payment->create(array('created' => time(), 'company_id' => $akoto->data()->company_id, 'is_active' => 1, 'remarks' => $remfinal));
			$payment_lastid = $payment->getInsertedId();
			$totaltopayincash = 0;
			foreach($s as $i) {
				$caravan->create(array('agent_request_id' => $req_id, 'item_id' => $i['item_id'], 'qty' => $i['qty'], 'member_id' => $i['member_id'], 'price_id' => $i['price_id'], 'created' => $now, 'modified' => $now, 'agent_id' => $akoto->data()->id, 'is_active' => 1, 'payment_id' => $payment_lastid, 'sr' => $i['sr'], 'discount' => $i['discount'], 'remarks' => $i['remarks'], 'sold_date' => $i['sold_date']));
				$itemp = new Product($i['item_id']);
				$itemp = $itemp->getPrice($i['item_id']);
				$itemptotalamount = ($i['qty'] * $itemp->price) - $i['discount'];
				$totaltopayincash += $itemptotalamount;

				$newsales = new Sales();
				$newsales->create(array('item_id' => $i['item_id'], 'price_id' => $i['price_id'], 'qtys' => $i['qty'], 'discount' => $i['discount'], 'company_id' => $akoto->data()->company_id, 'cashier_id' => $akoto->data()->id, 'sold_date' => $i['sold_date'], 'payment_id' => $payment_lastid, 'member_id' => $smemid->member_id, 'station_id' => $i['member_id'], 'sales_type' => -1, 'sr' => $i['sr']));
			}

			$paymentList = $srPayment[$srnum];
			foreach($paymentList as $ptype => $pval) {
				if($pval) {
					if($ptype == 'payment_cash') {
						$pcash = new Cash();
						$pcash->create(array('amount' => $pval, 'is_active' => 1, 'created' => time(), 'modified' => time(), 'payment_id' => $payment_lastid));
					} else if($ptype == 'payment_cheque') {
						$payment_cheque = json_decode($pval, true);
						$cheque = new Cheque();
						foreach($payment_cheque as $c) {
							$cheque->create(array('check_number' => $c['cheque_number'], 'amount' => $c['amount'], 'bank' => $c['bank_name'], 'payment_date' => strtotime($c['date']), 'lastname' => $c['lastname'], 'firstname' => $c['firstname'], 'middlename' => $c['middlename'], 'contacts' => $c['phone'], 'is_active' => 1, 'created' => $now, 'modified' => $now, 'payment_id' => $payment_lastid));
						}
					} else if($ptype == 'payment_con') {
						$pcon = new Payment_consumable();
						$payment_con = $pval;
						$pcon->create(array('amount' => $payment_con, 'is_active' => 1, 'created' => $now, 'modified' => $now, 'payment_id' => $payment_lastid, 'member_id' => $smemid->member_id));

						$mem = new Member();
						$mycon = $mem->getMyConsumableAmount($smemid->member_id);
						if($mycon) {

							foreach($mycon as $c) {
								if($payment_con) {
									$notvalid = $mem->getNotYetValidCheque($c->payment_id);
									if($notvalid->cheque_amount) {
										$validamount = $c->amount - $notvalid->cheque_amount;
										$notv = $notvalid->cheque_amount;
									} else {
										$validamount = $c->amount;
										$notv = 0;
									}
									$toupdate = new Consumable_amount();
									if($validamount > $payment_con) {
										$leftamount = ($validamount - $payment_con) + $notv;
										$payment_con = 0;
										$toupdate->update(array('amount' => $leftamount, 'modified' => time()), $c->id);
									} else {
										$leftamount = $notv;
										$toupdate->update(array('amount' => $leftamount, 'modified' => time()), $c->id);
										$payment_con = $payment_con - $validamount;
									}
								}
							}
						}
					} else if($ptype == 'payment_con_freebies') {
						// insert cash
						$pcon = new Payment_consumable_freebies();
						$payment_con_freebies = $pval;
						$pcon->create(array('amount' => $payment_con_freebies, 'is_active' => 1, 'created' => $now, 'modified' => $now, 'payment_id' => $payment_lastid, 'member_id' => $smemid->member_id));


						$mem = new Member();
						$mycon = $mem->getMyConsumableFreebies($smemid->member_id);
						if($mycon) {

							foreach($mycon as $c) {
								if($payment_con_freebies) {

									$validamount = $c->amount;

									$toupdate = new Consumable_freebies();
									if($validamount > $payment_con_freebies) {
										$leftamount = ($validamount - $payment_con_freebies);
										$payment_con_freebies = 0;
										$toupdate->update(array('amount' => $leftamount, 'modified' => time()), $c->id);
									} else {
										$leftamount = 0;
										$toupdate->update(array('amount' => $leftamount, 'modified' => time()), $c->id);
										$payment_con_freebies = $payment_con_freebies - $validamount;
									}
								}
							}
						}
					} else if($ptype == 'payment_member_credit') {
						$pcredit = new Member_credit();

						$pcredit->create(array('amount' => $pval, 'is_active' => 1, 'created' => $now, 'modified' => $now, 'payment_id' => $payment_lastid, 'member_id' => $smemid->member_id));
					} else if($ptype == 'payment_bt') {
						$payment_bt = json_decode($pval, true);
						$bank_transfer = new Bank_transfer();
						foreach($payment_bt as $c) {
							$bank_transfer->create(array('bankfrom_account_number' => $c['credit_number'], 'amount' => $c['amount'], 'bankfrom_name' => $c['bank_name'], 'bankto_account_number' => $c['bt_bankto_account_number'], 'bankto_name' => $c['bt_bankto_name'], 'lastname' => $c['lastname'], 'firstname' => $c['firstname'], 'middlename' => $c['middlename'], 'company' => $c['comp'], 'address' => $c['add'], 'zip' => $c['postal'], 'contacts' => $c['phone'], 'is_active' => 1, 'created' => $now, 'modified' => $now, 'payment_id' => $payment_lastid));
						}
					} else if($ptype == 'payment_credit') {
						$payment_credit = json_decode($pval, true);
						$credit = new Credit();

						foreach($payment_credit as $c) {

							$credit->create(array('card_number' => $c['credit_number'], 'amount' => $c['amount'], 'bank_name' => $c['bank_name'], 'lastname' => $c['lastname'], 'firstname' => $c['firstname'], 'middlename' => $c['middlename'], 'company' => $c['comp'], 'address' => $c['add'], 'zip' => $c['postal'], 'contacts' => $c['phone'], 'email' => $c['email'], 'remarks' => $c['remarks'], 'is_active' => 1, 'created' => $now, 'modified' => $now, 'payment_id' => $payment_lastid));
						}
					}
				}
			}

		}
	}

	if($soli){

		foreach($soli as $s){
			if($s){
				$caravan->create(array(
					'agent_request_id' => $req_id,
					'item_id' => $s->item_id,
					'qty' => $s->qty,
					'member_id' =>0,
					'price_id' => $s->price_id,
					'created' => $now,
					'modified' => $now,
					'agent_id' => $akoto->data()->id,
					'is_active' => 1
				));
			}

		}
	}
	$myreq->update(array(
		'status' => 5,
		'modified'=>$now
	),$req_id);
		$req_mon = new Request_monitoring();
		$req_mon->create(array(
			'agent_request_id' =>$req_id,
			'status' => 5,
			'user_id' =>$akoto->data()->id,
			'date_approved' => strtotime(date('Y/m/d H:i:s')),
			'is_active' => 1,
			'company_id' => $akoto->data()->company_id,
			'remarks' => 'Liquidated By'
		));
	echo "Liquidated successfully";

