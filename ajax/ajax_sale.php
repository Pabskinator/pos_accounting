<?php
	include 'ajax_connection.php';

	$terminal_id = Input::get('id');
	$branch_id = Input::get('bid');
	$terminal = new Terminal();
	$terminal_mon = new Terminal_mon();


	$cashier_id = Input::get('cashier_id');
	$comp = Input::get('comp');

	$sales1 = Input::get('sales');

	$sales1 = json_decode($sales1,true);
	$forpickup = [];

	foreach($sales1 as $s){
	$sales = json_decode($s,true);
	$payment = new Payment();
	$payment->create(array(
		'created' => time(),
		'company_id' => $comp,
		'is_active' => 1
	));

	$payment_lastid = $payment->getInsertedId();
	$member_id =0;
	$station_id=0;



	$sales_remarks = '';
	$rack = new Rack();
	$rack_id = $rack->getRackForSelling($branch_id);
	if(isset($rack_id->rack) && !empty($rack_id->rack)){

	} else {
		$rack_id = $rack->getRackDisplayId($comp);
	}
	foreach($sales as $sale){
		// subtract to inventories
		$item = new Product();
		$order_id = $sale['order_id'];
		$warranty = $sale['warranty'];
		$warranty = ($warranty) ? $warranty : 0;
		$price = $item->getPriceByPriceId($sale['price_id']);
		$type = $item->getType($sale['item_id']);
		if($sale['mem_id']) $memid = $sale['mem_id'];
		else $memid=0;

		if($sale['stationid']) $station = $sale['stationid'];
		else $station=0;

		if($type->item_type == -1){
			$inventory = new Inventory();
			$inv_mon = new Inventory_monitoring();


			if(!$sale['branch_json']){

				$curinventory = $inventory->getQty($sale['item_id'],$branch_id,$rack_id->id);
				$newqty = $curinventory->qty - $sale['qty']; // dito ibawas
				$inventory->update(array(
					'qty' => $newqty
				), $curinventory->id);
				// insert monitoring
				$monlabelinv ='';
				$monlabeldr ='';
				$monlabelir ='';
				if($sale['invoice']){
					$monlabelinv = "Invoice $sale[invoice]";
				}
				if($sale['dr']){
					$monlabeldr = "Dr $sale[dr]";
				}
				if($sale['ir']){
					$monlabelir = "Ir $sale[ir]";
				}
				$inv_mon->create(array(
					'item_id' => $sale['item_id'],
					'rack_id' => $rack_id->id,
					'branch_id' => $branch_id,
					'page' => 'ajax/ajax_sale.php',
					'action' => 'Update',
					'prev_qty' => $curinventory->qty,
					'qty_di' => 2,
					'qty' => $sale['qty'],
					'new_qty' => $newqty,
					'created' => time(),
					'user_id' => $cashier_id,
					'remarks' => 'Deduct inventory upon selling on POS, Payment ID: ' . $payment_lastid . ' ' .$monlabelinv . " " . $monlabeldr . " " . $monlabelir,
					'is_active' => 1,
					'company_id' => $sale['company_id']
				));
				// check order point
				$checker = new Reorder_item();
				$invpoint  = new Inventory();
				$cnt = $checker->checkItemOrderPoint($sale['item_id'],$branch_id,$sale['company_id']);

				if($cnt->cnt == 0){
					$odpoint = new Reorder_point();
					// get current date base on

					$month = date('n');
					$pointqty = $odpoint->getOrderPoint($sale['item_id'],$branch_id,$sale['company_id'],$month);
					if($pointqty){
						$allqty = $invpoint->getAllQuantity($sale['item_id'],$branch_id);
						if ($allqty->totalQty < $pointqty->order_point ){
							// insert
							$insert = new Reorder_item();
							$insert->create(array(
								'item_id' => $sale['item_id'],
								'qty' => $pointqty->order_qty,
								'orderby_branch_id' => $branch_id,
								'orderto_branch_id' => $pointqty->orderto_branch_id,
								'is_active' => 1,
								'company_id' => $sale['company_id'],
								'created' => strtotime(date('Y/m/d H:i:s')),
								'modified' => strtotime(date('Y/m/d H:i:s')),
								'status' => 1
							));
						}
					}
				}
			} else {
				if($sale['branch_json'] && $sale['todeductqty'] == 1){
					// parse json, deduct qty
					$jsonperbranch = json_decode($sale['branch_json']);
					foreach($jsonperbranch as $jpb){
						if($jpb->branch_id == $branch_id && $jpb->qty > 0){
							// deduct inventory
							$curinventory = $inventory->getQty($sale['item_id'],$branch_id,$rack_id->id);
							$newqty = $curinventory->qty - $jpb->qty; // dito ibawas
							$inventory->update(array(
								'qty' => $newqty
							), $curinventory->id);
							// insert monitoring
							$monlabelinv ='';
							$monlabeldr ='';
							$monlabelir ='';
							if($sale['invoice']){
								$monlabelinv = "Invoice $sale[invoice]";
							}
							if($sale['dr']){
								$monlabeldr = "Dr $sale[dr]";
							}
							if($sale['ir']){
								$monlabelir = "Ir $sale[ir]";
							}
							$inv_mon->create(array(
								'item_id' => $sale['item_id'],
								'rack_id' => $rack_id->id,
								'branch_id' => $branch_id,
								'page' => 'ajax/ajax_sale.php',
								'action' => 'Update',
								'prev_qty' => $curinventory->qty,
								'qty_di' => 2,
								'qty' => $jpb->qty,
								'new_qty' => $newqty,
								'created' => time(),
								'user_id' => $cashier_id,
								'remarks' => 'Deduct inventory upon selling on POS, Payment ID: ' . $payment_lastid . ' ' .$monlabelinv . " " . $monlabeldr . " " .$monlabelir,
								'is_active' => 1,
								'company_id' => $sale['company_id']
							));
							// reorder point check
							$checker = new Reorder_item();
							$invpoint  = new Inventory();
							$cnt = $checker->checkItemOrderPoint($sale['item_id'],$branch_id,$sale['company_id']);

							if($cnt->cnt == 0){
								$odpoint = new Reorder_point();
								// get current date base on

								$month = date('n');
								$pointqty = $odpoint->getOrderPoint($sale['item_id'],$branch_id,$sale['company_id'],$month);
								if($pointqty){
									$allqty = $invpoint->getAllQuantity($sale['item_id'],$branch_id);
									if ($allqty->totalQty < $pointqty->order_point ){
										// insert
										$insert = new Reorder_item();
										$insert->create(array(
											'item_id' => $sale['item_id'],
											'qty' => $pointqty->order_qty,
											'orderby_branch_id' => $branch_id,
											'orderto_branch_id' => $pointqty->orderto_branch_id,
											'is_active' => 1,
											'company_id' => $sale['company_id'],
											'created' => strtotime(date('Y/m/d H:i:s')),
											'modified' => strtotime(date('Y/m/d H:i:s')),
											'status' => 1
										));
									}
								}
							}

						} else if ($jpb->branch_id != $branch_id && $jpb->qty > 0){
							// insert to for pickups nila
							$pnow = time();
							$pickupcls = new Pickup();
							$pickupcls->create(array(
								'branch_id' => $jpb->branch_id,
								'src_branch' => $branch_id,
								'company_id' => $sale['company_id'],
								'status' => 1,
								'created' =>$pnow,
								'modified' => $pnow,
								'cashier_id' => $cashier_id,
								'is_active' => 1,
								'payment_id' => $payment_lastid,
								'member_id' => $memid,
								'item_id'=>$sale['item_id'],
								'qty' => $jpb->qty
							));

						}
					}
				}
			}
			//end check order point
		} else if ($type->item_type == 2 || $type->item_type == 3  || $type->item_type == 4 || $type->item_type == 5){
			for($startingservice = 0; $startingservice < $sale['qty']; $startingservice++){
				$con = new Consumable();
				$myCon = $con->getConsumableByItemId($sale['item_id']);
				$newServ = new Service();
				$start = $sale['sold_date'];
				$cday = $sale['cdays'];
				$endDate = strtotime(date('m/d/Y',$start) . $cday . " day");
				$newServ->create(array(
					'member_id' => $memid,
					'item_id' => $sale['item_id'],
					'start_date' => $start,
					'end_date' =>$endDate,
					'consumable_qty' => $sale['cqty'],
					'company_id' => $sale['company_id'],
					'payment_id' => $payment_lastid
				));
				$servlastid = $newServ->getInsertedId();
				if($type->item_type == 4){
					$con_amount = new Consumable_amount();
					$n = time();
					$con_amount->create(array(
						'service_id' => $servlastid,
						'amount' => $price->price,
						'item_id' => $sale['item_id'],
						'member_id' => $memid,
						'is_active' => 1,
						'created' => $n,
						'modified' => $n,
						'payment_id' => $payment_lastid
					));
				}
				if($type->item_type == 5){
					$con_free = new Consumable_freebies();
					$con_free_amount = $con_free->getConsumableFreebiesAmount($sale['item_id']);

					$n = time();
					$con_free->create(array(
						'service_id' => $servlastid,
						'amount' => $con_free_amount->amount,
						'item_id' => $sale['item_id'],
						'member_id' => $memid,
						'is_active' => 1,
						'created' => $n,
						'modified' => $n,
						'payment_id' => $payment_lastid
					));
				}
			}

		}
		if($sale['invoice'] != 0){
			$terminal->update(array(
				'modified' => strtotime(date('Y/m/d H:i:s')),
				'invoice' => $sale['invoice']
			), $terminal_id);
		}
		if($order_id){
			$update_order = new Order();
			$update_order->update(array(
				'modified' => strtotime(date('Y/m/d H:i:s')),
				'status' => 2,
				'payment_id' =>$payment_lastid
			), $order_id);
		}

		if($sale['dr'] != 0){
			$terminal->update(array(
				'modified' => strtotime(date('Y/m/d H:i:s')),
				'dr' => $sale['dr']
			), $terminal_id);
		}
		if($sale['ir'] != 0){
			$terminal->update(array(
				'modified' => strtotime(date('Y/m/d H:i:s')),
				'ir' => $sale['ir']
			), $terminal_id);
		}

		$newsales = new Sales();

		$date = (int) $sale['sold_date'];
		if (strpos($sale['discount'],'%')>0){
			$discount = (float)$sale['discount'];
			$discount = ($sale[price] * $sale['qty']) * ($discount/100);
		} else {
			$discount = $sale['discount'];
		}
		$sales_adj = 0;
		if($discount < 0){
			$sales_adj = ($discount * -1);
			$discount = 0;
		}
		if(!$sale['sales_type']) $sale['sales_type'] = 0;
		$newsales->create(array(
			'terminal_id' => $terminal_id,
			'invoice' => $sale['invoice'],
			'dr' => $sale['dr'],
			'ir' => $sale['ir'],
			'item_id' => $sale['item_id'],
			'price_id' => $sale['price_id'],
			'qtys' => $sale['qty'],
			'discount' => number_format($discount,2, '.', ''),
			'store_discount' => number_format($sale['store_discount'],2, '.', ''),
			'adjustment' => number_format($sale['adjustment'],2, '.', ''),
			'member_adjustment' =>$sales_adj,
			'company_id' => $sale['company_id'],
			'cashier_id' => $cashier_id,
			'sold_date' => $date,
			'payment_id' => $payment_lastid,
			'member_id' => $memid,
			'station_id' => $station,
			'sales_type' => $sale['sales_type'],
			'warranty' => $warranty
		));
		$payment_cash = $sale['payment_cash'];
		$payment_con = $sale['payment_con'];
		$payment_con_freebies = $sale['payment_con_freebies'];
		$payment_member_credit = $sale['payment_member_credit'];
		$payment_member_deduction = $sale['payment_member_deduction'];
		$sdate =$date;
		$scompany= $sale['company_id'];
		$payment_credit = $sale['payment_credit'];
		$payment_bt = $sale['payment_bt'];
		$payment_cheque = $sale['payment_cheque'];
		$member_id = $memid;
		$sdr = $sale['dr'];
		$sir = $sale['ir'];
		$sinv =  $sale['invoice'];
		$sales_remarks = $sale['sales_remarks'];
	}


	// start insert payment
	$sdr = ($sdr) ? 'Dr: '.$sdr:'';
	$sinv = ($sinv) ? 'Inv: '.$sinv:'';
	$sir = ($sir) ? 'Ir: '.$sir:'';
	if($payment_credit){
		// insert credit
		$payment_credit = json_decode($payment_credit,true);
		$credit = new Credit();
		$total_amount_cc = 0;

		foreach($payment_credit as $c){
			$total_amount_cc += $c['amount'];
			$credit->create(array(
				'card_number' => $c['credit_number'],
				'amount' => $c['amount'],
				'bank_name'=>$c['bank_name'],
				'lastname'=>$c['lastname'],
				'firstname'=>$c['firstname'],
				'middlename'=>$c['middlename'],
				'company'=>$c['comp'],
				'address'=>$c['add'],
				'zip' => $c['postal'],
				'contacts' => $c['phone'],
				'email' => $c['email'],
				'remarks' => $c['remarks'],
				'is_active' => 1,
				'created' => $sdate,
				'modified' => $sdate,
				'payment_id' => $payment_lastid
			));
		}
		$prevamount = $terminal->getTAmount($terminal_id,2);
		$prevamount = ($prevamount->t_amount_cc) ? $prevamount->t_amount_cc:0;
		$to_amount = $total_amount_cc + $prevamount;

		$terminal->update(array(
			't_amount_cc' => $to_amount
		),$terminal_id);
		$terminal_mon->create( array(
			'terminal_id' => $terminal_id,
			'user_id' => $cashier_id,
			'from_amount' =>$prevamount,
			'amount' =>$total_amount_cc,
			'to_amount'=>$to_amount,
			'status' => 1,
			'remarks' => "POS $sinv $sdr $sir",
			'is_active' => 1,
			'company_id' => $scompany,
			'p_type' => 2,
			'created' => $sdate
		));
	}
	if($payment_bt){
		// insert bank transfer
		$payment_bt = json_decode($payment_bt,true);
		$bank_transfer = new Bank_transfer();
		$total_amount_bt = 0;
		foreach($payment_bt as $c){
			$total_amount_bt += $c['amount'];
			$bank_transfer->create(array(
				'bankfrom_account_number' => $c['credit_number'],
				'amount' => $c['amount'],
				'bankfrom_name'=>$c['bank_name'],
				'bankto_account_number' => $c['bt_bankto_account_number'],
				'bankto_name' => $c['bt_bankto_name'],
				'lastname'=>$c['lastname'],
				'firstname'=>$c['firstname'],
				'middlename'=>$c['middlename'],
				'company'=>$c['comp'],
				'address'=>$c['add'],
				'zip' => $c['postal'],
				'contacts' => $c['phone'],
				'is_active' => 1,
				'created' => $sdate,
				'modified' => $sdate,
				'payment_id' => $payment_lastid
			));
		}
		$prevamount = $terminal->getTAmount($terminal_id,4);
		$prevamount = ($prevamount->t_amount_bt) ? $prevamount->t_amount_bt:0;
		$to_amount = $total_amount_bt + $prevamount;

		$terminal->update(array(
			't_amount_bt' => $to_amount
		),$terminal_id);
		$terminal_mon->create( array(
			'terminal_id' => $terminal_id,
			'user_id' => $cashier_id,
			'from_amount' =>$prevamount,
			'amount' =>$total_amount_bt,
			'to_amount'=>$to_amount,
			'status' => 1,
			'remarks' => "POS $sinv $sdr $sir",
			'is_active' => 1,
			'company_id' => $scompany,
			'p_type' => 4,
			'created' => $sdate
		));
	}
	if($payment_cheque){
		// insert cheque
		$payment_cheque = json_decode($payment_cheque,true);
		$cheque = new Cheque();
		$total_amount_ch = 0;
		foreach($payment_cheque as $c){
			$total_amount_ch += $c['amount'];
			$cheque->create(array(
				'check_number' => $c['cheque_number'],
				'amount' => $c['amount'],
				'bank'=>$c['bank_name'],
				'payment_date' => strtotime($c['date']),
				'lastname'=>$c['lastname'],
				'firstname'=>$c['firstname'],
				'middlename'=>$c['middlename'],
				'contacts' => $c['phone'],
				'is_active' => 1,
				'created' => $sdate,
				'modified' => $sdate,
				'payment_id' => $payment_lastid
			));
		}
		$prevamount = $terminal->getTAmount($terminal_id,3);
		$prevamount = ($prevamount->t_amount_ch) ? $prevamount->t_amount_ch:0;
		$to_amount = $total_amount_ch + $prevamount;

		$terminal->update(array(
			't_amount_ch' => $to_amount
		),$terminal_id);
		$terminal_mon->create( array(
			'terminal_id' => $terminal_id,
			'user_id' => $cashier_id,
			'from_amount' =>$prevamount,
			'amount' =>$total_amount_ch,
			'to_amount'=>$to_amount,
			'status' => 1,
			'remarks' => "POS $sinv $sdr $sir",
			'is_active' => 1,
			'company_id' => $scompany,
			'p_type' => 3,
			'created' => $sdate
		));
	}
	if($payment_cash){
		// insert cash
		$pcash = new Cash();
		$total_amount = $payment_cash;
		$pcash->create(array(
			'amount' =>$payment_cash,
			'is_active' => 1,
			'created' => $sdate,
			'modified' => $sdate,
			'payment_id' => $payment_lastid
		));
		$prevamount = $terminal->getTAmount($terminal_id,1);
		$prevamount = ($prevamount->t_amount) ? $prevamount->t_amount:0;
		$to_amount = $total_amount + $prevamount;

		$terminal->update(array(
			't_amount' => $to_amount
		),$terminal_id);
		$terminal_mon->create( array(
			'terminal_id' => $terminal_id,
			'user_id' => $cashier_id,
			'from_amount' =>$prevamount,
			'amount' =>$total_amount,
			'to_amount'=>$to_amount,
			'status' => 1,
			'remarks' => "POS $sinv $sdr $sir",
			'is_active' => 1,
			'company_id' => $scompany,
			'p_type' => 1,
			'created' => $sdate
		));
	}
	if($payment_member_credit){
		// insert cash
		$pcredit = new Member_credit();
		$pcredit->create(array(
			'amount' =>$payment_member_credit,
			'is_active' => 1,
			'created' => $sdate,
			'modified' => $sdate,
			'payment_id' => $payment_lastid,
			'member_id' => $member_id
		));
	}
	if($payment_member_deduction){
		// insert cash
		$pdeduct = new Deduction();
		$pdeduct->create(array(
			'amount' =>$payment_member_deduction,
			'is_active' => 1,
			'created' => $sdate,
			'payment_id' => $payment_lastid,
			'member_id' => $member_id
		));
	}
	if($payment_con){
		// insert cash
		$pcon = new Payment_consumable();
		$pcon->create(array(
			'amount' =>$payment_con,
			'is_active' => 1,
			'created' => $sdate,
			'modified' => $sdate,
			'payment_id' => $payment_lastid,
			'member_id' => $member_id
		));


		$mem = new Member();
		$mycon = $mem->getMyConsumableAmount($memid);
		if($mycon){

			foreach($mycon as $c){
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
						$leftamount = ($validamount - $payment_con) + $notv ;
						$payment_con =0;
						$toupdate->update(array('amount' => $leftamount, 'modified' => time()), $c->id);
					} else {
						$leftamount = $notv;
						$toupdate->update(array('amount' => $leftamount, 'modified' => time()), $c->id);
						$payment_con = $payment_con - $validamount;
					}
				}
			}
		}
	}
	if($payment_con_freebies){
		// insert cash
		$pcon = new Payment_consumable_freebies();
		$pcon->create(array(
			'amount' =>$payment_con_freebies,
			'is_active' => 1,
			'created' => $sdate,
			'modified' => $sdate,
			'payment_id' => $payment_lastid,
			'member_id' => $member_id
		));


		$mem = new Member();
		$mycon = $mem->getMyConsumableFreebies($memid);
		if($mycon){

			foreach($mycon as $c){
				if($payment_con_freebies) {
				
					$validamount = $c->amount;
					
					$toupdate = new Consumable_freebies();
					if($validamount > $payment_con_freebies) {
						$leftamount = ($validamount - $payment_con_freebies);
						$payment_con_freebies =0;
						$toupdate->update(array('amount' => $leftamount, 'modified' => time()), $c->id);
					} else {
						$leftamount = 0;
						$toupdate->update(array('amount' => $leftamount, 'modified' => time()), $c->id);
						$payment_con_freebies = $payment_con_freebies - $validamount;
					}
				}
			}
		}
	}
		if($sales_remarks){
				$payment->update(array(
					'remarks' => $sales_remarks
				),$payment_lastid);
		}

	}
	echo 1;
?>