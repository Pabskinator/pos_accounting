<?php
	include 'ajax_connection.php';

	if(Input::get('type') == 1) {
		$toOrder = json_decode(Input::get('toOrder'));
		$branch = Input::get('branch');
		$member_id = Input::get('member_id');
		$r_remarks = Input::get('r_remarks');
		$station_id = Input::get('stationid');
		$salestype = Input::get('salestype');
		$company_id = Input::get('company_id');
		$src_branch  = Input::get('src_branch');
		$payment_cash  = Input::get('payment_cash');
		$payment_bt  = Input::get('payment_bt');
		$payment_cheque  = Input::get('payment_cheque');
		$payment_con_freebies  = Input::get('payment_con_freebies');
		$payment_member_credit  = Input::get('payment_member_credit');
		$payment_con  = Input::get('payment_con');
		$payment_credit  = Input::get('payment_credit');

		$order = new Order();
		$orderuser = new User();
		$order->create(
				array(
					'user_id' => $orderuser->data()->id,
					'member_id' => $member_id, 'branch_id' => $branch,
					'company_id' => $company_id,
					'status' => 1, 'is_active' => 1,
					'created' => strtotime(date('Y/m/d H:i:s')),
					'src_branch' => $src_branch,
					'sales_type' => $salestype,
					'station_id' => $station_id,
					'modified' => strtotime(date('Y/m/d H:i:s')),
					'payment_cash' => $payment_cash,
					'payment_bt' => $payment_bt,
					'payment_cheque' => $payment_cheque,
					'payment_consumable_freebies' => $payment_con_freebies,
					'payment_consumable' => $payment_con,
					'payment_credit_card' => $payment_credit,
					'payment_member_credit' => $payment_member_credit,
					'remarks' => $r_remarks
					));
		$lastid = $order->getInsertedId();

		foreach($toOrder as $o) {
			if($lastid) {
				$od = new OrderDetails();
				$od->create(array('item_id' => $o->item_id,'ss_json' => $o->multipless,'branch_json' => $o->allocatedqty,  'qty' => $o->qty, 'price_adjustment' => $o->price_adjustment, 'discount' => $o->discount, 'order_id' => $lastid, 'status' => 1, 'is_active' => 1, 'created' => strtotime(date('Y/m/d H:i:s')), 'modified' => strtotime(date('Y/m/d H:i:s')),));
			}

		}
		echo "Order was successfully placed.";
	} else if(Input::get('type') == 2){
		// pending order
		$toOrder = json_decode(Input::get('pending'));

		$company_id = Input::get('company_id');
		$src_branch  = Input::get('src_branch');

		foreach($toOrder as $indorder){
			$jsono = json_decode($indorder);

				$bid = $jsono[0]->bid ;
				$mid =  $jsono[0]->mid ;
				$station_id = $jsono[0]->stationid;
				$salestype =$jsono[0]->salestype;

			$order = new Order();
			$orderuser = new User();
			$order->create(array('user_id' => $orderuser->data()->id, 'member_id' => $mid, 'branch_id' => $bid,'sales_type' => $salestype,'station_id' => $station_id, 'company_id' => $orderuser->data()->company_id, 'status' => 1, 'is_active' => 1, 'created' => strtotime(date('Y/m/d H:i:s')),'src_branch' => $src_branch, 'modified' => strtotime(date('Y/m/d H:i:s')),));
			$lastid = $order->getInsertedId();

			foreach($jsono as $s){
				$itemid = $s->item_id;
				$qty = $s->qty;
				$discount = $s->discount;
				$od = new OrderDetails();
				if($lastid){
				$od->create(array('item_id' => $itemid, 'qty' => $qty,'discount' => $discount, 'order_id' => $lastid, 'status' => 1, 'is_active' => 1, 'created' => strtotime(date('Y/m/d H:i:s')), 'modified' => strtotime(date('Y/m/d H:i:s')),));
				}
			}
		}

	}