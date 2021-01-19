<?php
	include 'ajax_connection.php';
	// decrypt the id and get the post data
	$id = Encryption::encrypt_decrypt('decrypt',Input::get('id'));
	$table = Input::get('table');
	// get instance of databases
	$db = DB::getInstance();

	if($table == 'cash'){


		$cash = new Cash($id);
		$payment_dr = Input::get('payment_dr');
		$payment_inv = Input::get('payment_inv');
		$amount = Input::get('amount');
		$terminal_id = Input::get('terminal_id');
		$user = new User();
		$terminal = new Terminal();
		$terminal_mon = new Terminal_mon();

		$total_amount = $amount;
		$prevamount = $terminal->getTAmount($terminal_id,1);
		$prevamount = ($prevamount->t_amount) ? $prevamount->t_amount:0;
		$to_amount =  $prevamount-$total_amount;

		$pdr = ($payment_dr) ? 'Dr: '.$payment_dr:'';
		$pinv = ($payment_inv) ? 'Inv: '.$payment_inv:'';

		$terminal->update(array(
			't_amount' => $to_amount
		),$terminal_id);
		$terminal_mon->create( array(
			'terminal_id' => $terminal_id,
			'user_id' => $user->data()->id,
			'from_amount' =>$prevamount,
			'amount' =>$total_amount,
			'to_amount'=>$to_amount,
			'status' => 2,
			'remarks' => "POS $pinv $pdr",
			'is_active' => 1,
			'company_id' => $user->data()->company_id,
			'p_type' => 1,
			'created' => time()
		));
		Log::addLog(
			$user->data()->id,
			$user->data()->company_id,
			"DELETE CASH PAYMENT $pinv $pdr",
			'ajax_deletepermanent.php'
		);

	}
	if($table == 'cheque'){
		$cheque = new Cheque($id);
		$payment_dr = Input::get('payment_dr');
		$payment_inv = Input::get('payment_inv');
		$amount = Input::get('amount');
		$terminal_id = Input::get('terminal_id');
		$user = new User();
		$terminal = new Terminal();
		$terminal_mon = new Terminal_mon();
		$total_amount = $amount;
		$prevamount = $terminal->getTAmount($terminal_id,3);
		$prevamount = ($prevamount->t_amount_ch) ? $prevamount->t_amount_ch:0;
		$to_amount =  $prevamount-$total_amount;
		$pdr = ($payment_dr) ? 'Dr: '.$payment_dr:'';
		$pinv = ($payment_inv) ? 'Inv: '.$payment_inv:'';

		$terminal->update(array(
			't_amount_ch' => $to_amount
		),$terminal_id);
		$terminal_mon->create( array(
			'terminal_id' => $terminal_id,
			'user_id' => $user->data()->id,
			'from_amount' =>$prevamount,
			'amount' =>$total_amount,
			'to_amount'=>$to_amount,
			'status' => 2,
			'remarks' => "POS $pinv $pdr",
			'is_active' => 1,
			'company_id' => $user->data()->company_id,
			'p_type' => 3,
			'created' => time()
		));

		Log::addLog(
			$user->data()->id,
			$user->data()->company_id,
			"DELETE CHEQUE PAYMENT $pinv $pdr",
			'ajax_deletepermanent.php'
		);
	}
	if($table == 'bank_transfer'){
		$bankTransfer = new Bank_transfer($id);

		$payment_dr = Input::get('payment_dr');
		$payment_inv = Input::get('payment_inv');
		$amount = Input::get('amount');
		$terminal_id = Input::get('terminal_id');
		$user = new User();
		$terminal = new Terminal();
		$terminal_mon = new Terminal_mon();
		$total_amount = $amount;
		$prevamount = $terminal->getTAmount($terminal_id,4);
		$prevamount = ($prevamount->t_amount_bt) ? $prevamount->t_amount_bt:0;
		$to_amount =  $prevamount-$total_amount;
		$pdr = ($payment_dr) ? 'Dr: '.$payment_dr:'';
		$pinv = ($payment_inv) ? 'Inv: '.$payment_inv:'';
		$terminal->update(array(
			't_amount_bt' => $to_amount
		),$terminal_id);
		$terminal_mon->create( array(
			'terminal_id' => $terminal_id,
			'user_id' => $user->data()->id,
			'from_amount' =>$prevamount,
			'amount' =>$total_amount,
			'to_amount'=>$to_amount,
			'status' => 2,
			'remarks' => "POS $pinv $pdr",
			'is_active' => 1,
			'company_id' => $user->data()->company_id,
			'p_type' => 4,
			'created' => time()
		));

		Log::addLog(
			$user->data()->id,
			$user->data()->company_id,
			"DELETE BANK TRANSFER PAYMENT $pinv $pdr",
			'ajax_deletepermanent.php'
		);

	}
	if($table == 'credit_card'){

		$creditCard = new Credit($id);
		$payment_dr = Input::get('payment_dr');
		$payment_inv = Input::get('payment_inv');
		$amount = Input::get('amount');
		$terminal_id = Input::get('terminal_id');
		$user = new User();
		$terminal = new Terminal();
		$terminal_mon = new Terminal_mon();

		$total_amount = $amount;
		$prevamount = $terminal->getTAmount($terminal_id,2);
		$prevamount = ($prevamount->t_amount_cc) ? $prevamount->t_amount_cc:0;
		$to_amount =  $prevamount-$total_amount;
		$pdr = ($payment_dr) ? 'Dr: '.$payment_dr:'';
		$pinv = ($payment_inv) ? 'Inv: '.$payment_inv:'';

		$terminal->update(array(
			't_amount_cc' => $to_amount
		),$terminal_id);
		$terminal_mon->create( array(
			'terminal_id' => $terminal_id,
			'user_id' => $user->data()->id,
			'from_amount' =>$prevamount,
			'amount' =>$total_amount,
			'to_amount'=>$to_amount,
			'status' => 2,
			'remarks' => "POS $pinv $pdr",
			'is_active' => 1,
			'company_id' => $user->data()->company_id,
			'p_type' => 2,
			'created' => time()
		));

		Log::addLog(
			$user->data()->id,
			$user->data()->company_id,
			"DELETE CREDIT CARD TRANSFER PAYMENT $pinv $pdr",
			'ajax_deletepermanent.php'
		);

	}

	if($table == 'payment_consumable'){
		$paymentConsumable = new Payment_consumable($id);
		$payment_amount = $paymentConsumable->data()->amount;
		$member_id = $paymentConsumable->data()->member_id;
		$concls = new Consumable();
		$res = $concls->updateConsumable($payment_amount,$member_id);
	}

	if($table == 'payment_consumable_freebies') {
		$paymentConsumable = new Payment_consumable_freebies($id);
		$payment_amount = $paymentConsumable->data()->amount;
		$member_id = $paymentConsumable->data()->member_id;
		$concls = new Consumable_freebies();
		$res = $concls->updateConsumable($payment_amount,$member_id);
	}

	if($table == 'sales'){
		// transfer mon
		$user = new User();
		$sales = new Sales($id);
		 $sales->update(
			array('status' => 1 )
			,$id);
		$terminal = new Terminal($sales->data()->terminal_id);

		$product = new Product($sales->data()->item_id);


		if($product->data()->item_type == -1) {

			$tranfer_mon = new Transfer_inventory_mon();
			$tranfer_mon->create(array('status' => 1, 'is_active' => 1, 'branch_id' => $terminal->data()->branch_id, 'company_id' => $user->data()->company_id, 'created' => time(), 'modified' => time(), 'from_where' => 'From cancelled', 'payment_id' => $sales->data()->payment_id));
			$lastid = $tranfer_mon->getInsertedId();
			$tranfer_mon_details = new Transfer_inventory_details();
			$tranfer_mon_details->create(array('transfer_inventory_id' => $lastid, 'rack_id_from' => 0, 'rack_id_to' => 0, 'item_id' => $sales->data()->item_id, 'qty' => $sales->data()->qtys, 'is_active' => 1));

		}

		echo 'true';
	}
	if($table != 'sales'){

		$user = new User();

		Log::addLog(
			$user->data()->id,
			$user->data()->company_id,
			"DELETE on $table where id is $id",
			'ajax_deletepermanent.php'
		);

		if($db->delete($table,array('id' ,'=' ,$id))){
			echo 'true';
		}

	}//s


