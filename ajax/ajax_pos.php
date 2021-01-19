<?php
	include 'ajax_connection.php';

	$functionName = Input::get("functionName");
	$functionName();
	function getUsers(){
		$u = new User();
		$list = $u->get_active('users',[1,'=',1]);
		$arr = [];
		if($list){
			foreach($list as $l){
				$arr[] = ['active'=>0,'id' => $l->id,'name' => ucwords($l->firstname. " ". $l->lastname)];
			}
		}
		return $arr;
	}
	function getCategories(){
		$user = new User();
		$categ = new Category();
		$list = $categ->getCategory($user->data()->company_id,true);
		$arr_categ = [];
		$arr_main= [];
		if($list){

			foreach($list as $a){

				if(file_exists('../uploads/categories/' . $a->id . ".jpg")){
					$a->url = 'uploads/categories/' . $a->id . ".jpg";
				} else {
					$a->url = 'css/img/no-thumb.jpg';
				}
				if($a->parent == 0){
					$arr_main[] = $a;
				} else {
					$arr_categ[] = $a;
				}

			}
		}

		$prod = new Product();
		$user = new User();
		$rack = new Rack();
		$forselling = $rack->getRackForSelling($user->data()->branch_id);
		$rack_id = 0;
		if(isset($forselling->id)){
			$rack_id = $forselling->id;
		}

		$list = $prod->getItemsAndInventoriesPOS($user->data()->branch_id,$user->data()->company_id,$rack_id);

		$arr = [];
		$keys = [];
		if($list){
			$bundle = new Bundle();

			foreach($list as $a){
				$a->show = false;
				if(file_exists('../item_images/' . $a->item_id . ".jpg")){
					$a->url = 'item_images/' . $a->item_id . ".jpg";
				} else {
					$a->url = 'css/img/no-thumb.jpg';
				}
				$bundle_arr=  [];
				if($a->is_bundle == 1){
					$bundles = $bundle->getBundleItem($a->item_id);
					if($bundles){
						foreach($bundles as $bund){
							$bund->child_qty = formatQuantity($bund->child_qty,true);
							$bund->total_qty = $bund->child_qty;
							$bund->used_qty = $bund->child_qty;
							$bundle_arr[] = $bund;
						}
					}
				}
				$a->inv_qty = $a->qty;
				$a->qty = 0;
				$a->edit_qty = 0;
				$a->id = $a->item_id;
				$a->discount = 0;
				$a->agent_id = 0;
				$a->agent_name = 0;
				$a->agent_qty = 0;
				$a->agent_list = [];
				$a->bundle_arr = json_encode($bundle_arr);
				$arr[] = $a;
				$keys[$a->description] = $a->url;
			}
		}

		$arr_queues = [];
		$queue = new Queu();

		$queues = $queue->getQueues($user->data()->branch_id);
		$current_queues = getCurrentQueues();
		if($queues){
			foreach($queues as $q){

				$arr_queues[] = $q;

			}
		}

		$user_list = getUsers();
		if(count($user_list)>8){
			$user_list=[];
		}
		$reservations = getCurrentReservations();
		echo json_encode(['reservations' => $reservations, 'users' => $user_list, 'queue_list' => $current_queues,'queues' => $arr_queues,'main' => $arr_main, 'child'=> $arr_categ,'items' =>$arr,'keys' => $keys]);

	}
	function cancelReservation(){

		$id = Input::get('id');

		$pr = new Pos_reservation();

		$pr->update(
			[
				'status' => 2
			] , $id
		);

	}
	function getStoreSales(){
		$dt1 = date('m/d/Y');
		$dt2  = date('m/d/Y');

		$sales = new Sales();
		$user = new User();

		$list = $sales->get_sales_record($user->data()->company_id,0,10000,'',[$user->data()->branch_id],0,0,0,0,0,0,'',$dt1,$dt2);

		$arr = [];
		if($list){
			$prev = '';
			foreach($list as $l){

				$inv = "";
				$dr ="";
				$ir = "";
				$pid = "0";
				$ctr = "";

				if($prev != $l->payment_id){
					$inv = $l->invoice;
					$dr = $l->dr;
					$ir = $l->ir;
					$pid = $l->payment_id;
					if($inv){
						$ctr = $inv;
					} else if ($dr){
						$ctr = $dr;
					} else if ($ir){
						$ctr = $ir;
					}
				}
				$l->total = number_format(($l->price * $l->qtys) + $l->member_adjustment,2);
				$l->sold_date = date('H:i A',$l->sold_date);
				$l->price = number_format($l->price,2);
				$l->discount = number_format(abs($l->member_adjustment),2);

				$l->invoice = $inv;
				$l->dr = $dr;
				$l->ir = $ir;
				$l->payment_id = $pid;
				$l->qtys= formatQuantity($l->qtys);
				$l->ctr= $ctr;
				$prev = $l->payment_id;
				$arr[] = $l;
			}
		}
		echo json_encode($arr);
	}
	function getReservation(){
		echo json_encode(getCurrentReservations());
	}

	function getCurrentReservations(){
		$pos_reservation = new Pos_reservation();
		$user = new User();
		$list = $pos_reservation->getReservation($user->data()->branch_id);
		$arr =[];
		if($list){
			$q = new Queu();
			foreach($list as $a){
				$qname = "";
				if(strpos($a->queue_list_id,",") >0 ){
					$qlist = $q->byQueueId($a->queue_list_id);
					foreach($qlist as $i) {
						$qname .= $i->name . ",";
					}
					$qname =  rtrim($qname,",");
					$a->queue_name = $qname;
				} else if(!$a->queue_name){
					$a->queue_name = 'None';
				}
				$a->fullname = ucwords($a->firstname . " " . $a->lastname);
				$arr[] = $a;
			}
		}
		return $arr;
	}
	function insertMember(){

	}

	function getQueues(){
		$user = new User();
		$arr_queues = [];
		$queue = new Queu();

		$queues = $queue->getQueues($user->data()->branch_id);
		$current_queues = getCurrentQueues();
		if($queues){
			foreach($queues as $q){
				$pending = false;
				foreach($current_queues as $ql){
					if($ql->queu_id == $q->id){
						$pending = true;
						break;
					}
				}
				if($pending) continue;
				$arr_queues[] = $q;

			}
		}

		echo json_encode(['queue_list' => $current_queues,'queues' => $arr_queues]);

	}

	function getOpenBundleList(){
		$id = Input::get('id');
		$prod = new Product();
		$user = new User();
		$rack = new Rack();
		$forselling = $rack->getRackForSelling($user->data()->branch_id);
		$rack_id = 0;
		if(isset($forselling->id)){
			$rack_id = $forselling->id;
		}

		$list = $prod->getItemsAndInventories($user->data()->branch_id,$user->data()->company_id,$rack_id,$id);


		$arr = [];
		if($list){

				foreach($list as $a){

					$a->is_chosen = false;
					$a->qty = 1;
					$a->show = false;
					if(file_exists('../item_images/' . $a->item_id . ".jpg")){
						$a->url = 'item_images/' . $a->item_id . ".jpg";
					} else {
						$a->url = 'css/img/no-thumb.jpg';
					}
					$bundle_arr=  [];
					$a->id = $a->item_id;
					$a->discount = 0;
					$a->agent_id = 0;
					$a->bundle_arr = json_encode($bundle_arr);
					$arr[] = $a;

				}


		}
		echo json_encode($arr);

	}

	function getProduct(){
		$prod = new Product();
		$user = new User();
		$rack = new Rack();
		$forselling = $rack->getRackForSelling($user->data()->branch_id);
		$rack_id = 0;
		if(isset($forselling->id)){
			$rack_id = $forselling->id;
		}

		$list = $prod->getItemsAndInventories($user->data()->branch_id,$user->data()->company_id,$rack_id);

		$arr = [];
		$keys = [];
		if($list){
			$bundle = new Bundle();

			foreach($list as $a){
				$a->show = false;
				if(file_exists('../item_images/' . $a->item_id . ".jpg")){
					$a->url = 'item_images/' . $a->item_id . ".jpg";
				} else {
					$a->url = 'css/img/no-thumb.jpg';
				}
				$bundle_arr=  [];
				if($a->is_bundle == 1){
					$bundles = $bundle->getBundleItem($a->item_id);
					if($bundles){
						foreach($bundles as $bund){
							$bund->child_qty = formatQuantity($bund->child_qty,true);
							$bund->total_qty = $bund->child_qty;
							$bund->used_qty = $bund->child_qty;
							$bundle_arr[] = $bund;
						}
					}
				}
				$a->qty = 0;
				$a->id = $a->item_id;
				$a->discount = 0;
				$a->agent_id = 0;
				$a->agent_name = 0;
				$a->agent_qty = 0;
				$a->agent_list = [];
				$a->bundle_arr = json_encode($bundle_arr);
				$arr[] = $a;
				$keys[$a->description] = $a->url;
			}
		}
		echo json_encode(['items' =>$arr,'keys' => $keys]);

	}

	function createClient(){
		$member_data = Input::get('member_data');
		$member_data = json_decode($member_data);
		if($member_data->name && $member_data->contact ){
			$newmem = new Member();
			$user = new User();
			$newmemarr = array(
				'lastname' => $member_data->name ,
				'personal_address' => $member_data->address ,
				'gender' => $member_data->gender ,
				'birthdate' => strtotime($member_data->bday) ,
				'email' =>$member_data->email,
				'contact_number' =>  $member_data->contact,
				'company_id' => $user->data()->company_id,
				'is_active' => 1,
				'created' => strtotime(date('Y/m/d H:i:s')),
				'modified' => strtotime(date('Y/m/d H:i:s'))
			);

			$newmem->create($newmemarr);
			$lastid = $newmem->getInsertedId();


			if($member_data->username && $member_data->password){
				$explode = explode(" ", $member_data->name);
				$lastname = $explode[count($explode) - 1];
				$firstname = "";
				foreach($explode as $ex){
					if($ex != $explode[count($explode) - 1]){
						$firstname .= $ex . " " ;
					}
				}
				$firstname = rtrim($firstname," ");
				$position_id = 2;
				$newUser = new User();
				$newUser->create(array(
					'lastname' => $lastname,
					'firstname' => $firstname,
					'username' => $member_data->username,
					'password' => Hash::make($member_data->password),
					'is_active' => 1,
					'position_id' => $position_id,
					'branch_id' => $user->data()->branch_id,
					'is_member' => 1,
					'company_id' => $user->data()->company_id,
					'created' => strtotime(date('Y/m/d H:i:s')),
					'modified' => strtotime(date('Y/m/d H:i:s')),
					'member_id' => $lastid,
				));
			}

			echo json_encode(['id'=> $lastid, 'name' => $member_data->name]);
		}

	}
	function insertReferrals($r,$member_id){
		if($r && $member_id){
			$r = json_decode($r);
			if($r->id && $r->member_id){
				$ref = new Referral();
				$now = time();
				$ref->create(
					[
						'member_id' =>  $member_id,
						'referred_by' =>  $r->member_id,
						'service_id' =>  $r->id,
						'created_at' =>  $now,
						'old_expiration' =>  strtotime($r->end_date),
						'new_expiration' =>  strtotime($r->extends_to)
					]
				);
				$service = new Service();
				$service->update(['end_date' => strtotime($r->extends_to)],$r->id);
			}
		}
	}


	function queueComplete(){
		 $q = Input::get('queue');

		$q = json_decode($q);
		if($q->id){
			$checkout = $q->checkout;
			$queue = new Queu();
			$queue->markAsComplete($q->id,$checkout);
			echo "Processed successfully.";

		}

	}
	function getCurrentQueues(){
		$q = new Queu();
		$user = new User();
		$list = $q->getQueueList($user->data()->branch_id);
		$arr =[];
		if($list){
			foreach($list as $a){
				$a->time_in = date('m/d/Y H:i',$a->checkin);
				$a->time_out = date('m/d/Y H:i',$a->checkout);
				$a->sync = 1;
				$arr[] = $a;
			}
		}
		return $arr;
	}
	function insertQueuesBatch($queues){
		if($queues){
			$queues = json_decode($queues);
			if(count($queues) > 0){
				$q = new Queu();
				$user = new User();
				$sync_ids = [];
				foreach($queues as $ql){
					if(!$ql->id){
						$ql->agent_id = ($ql->agent_id) ? $ql->agent_id : 0;
						$last_id = $q->insertQeueList(
							$ql->queue_id,
							$ql->checkin,
							$ql->checkout,
							$user->data()->company_id,
							$user->data()->branch_id,
							$ql->agent_id
						);
						$sync_ids[] = ['id' => $last_id,'queue_id' => $ql->queue_id,'checkin' => $ql->checkin];
					} else {
						$q->markAsComplete($ql->id,$ql->checkout);
						$sync_ids[] = ['id' => $ql->id,'queue_id' => $ql->queue_id,'checkin' => $ql->checkin];
					}
				}
				echo json_encode($sync_ids);
			}
		}
	}
	function insertQueues($queues,$queues_list){
		$queue_inserted_id = "";
		if($queues && $queues_list){
			$queues = json_decode($queues);
			$queues_list = json_decode($queues_list);
			if(count($queues_list) > 0){
				foreach($queues_list as $ql){
					if($ql->queue_id && $ql->start && $ql->hrs){

						$q = new Queu();
						$time_in = date('m/d/Y') . " " . $ql->start;
						$time_out = strtotime($time_in . "+".$ql->hrs . " hour");

						$time_in = strtotime($time_in);

						$agent_id = ($ql->agent_id) ? $ql->agent_id : '0';

						$user = new User();

						$lastid = $q->insertQeueList(
							$ql->queue_id,
							$time_in,
							$time_out,
							$user->data()->company_id,
							$user->data()->branch_id,
							$agent_id
						);

						 $queue_inserted_id .= $lastid . ",";


					}
				}
			} else {
				if($queues->queue_id && $queues->start && $queues->hrs){

					$q = new Queu();
					$time_in = date('m/d/Y') . " " . $queues->start;
					$time_out = strtotime($time_in . "+".$queues->hrs . " hour");

					$time_in = strtotime($time_in);
					$agent_id = ($queues->agent_id) ? $queues->agent_id : '0';
					$user = new User();

					$lastid = $q->insertQeueList(
						$queues->queue_id,
						$time_in,
						$time_out,
						$user->data()->company_id,
						$user->data()->branch_id,
						$agent_id
					);
					$queue_inserted_id .= $lastid . ",";

				}
			}
			$queue_inserted_id = rtrim($queue_inserted_id,",");
		}
		return $queue_inserted_id;
	}
	function insertReservation(){
		$cart = Input::get('cart');
		$user = new User();
		 $arr_queue_ids = insertQueues(Input::get('queues'),Input::get('queues_list'));

		$pos_reservation = new Pos_reservation();

		$pos_reservation->create(
			[
				'branch_id' => $user->data()->branch_id,
				'status' => 0,
				'cart_items' => $cart,
				'created' => time(),
				'queue_list_id' => $arr_queue_ids,
			]
		);

		echo json_encode(['success' => true]);

	}
	function syncQueues(){
		$queues = Input::get('queues');
		if($queues){
			insertQueuesBatch($queues);
		}
	}
	function syncSales(){
		$sales = Input::get('sales');
		if($sales){
			$sales = json_decode($sales);
			if($sales){
				$failed = [];
				foreach($sales as $s){
					$failed[] = insertSales($s->cart,$s->member_id,$s->cash,$s->member_credit,$s->cheque,$s->credit_card,$s->bank_transfer,$s->deductions,$s->terminal_id,$s->reservation_id,$s->doc_type,$s->ctrl_number,$s->referals);
				}
				echo json_encode($failed);
			}
		}
	}

	function insertSales($cart='',$member_id='',$cash='',$member_credit='',$cheque='',$credit_card='',$bank_transfer='',$deductions='',$terminal_id='',$reservation_id='',$order_type='',$ctrl_number='',$referrals=''){

		$withoutput = false;
		if($cart == '' && $terminal_id == ''){

			$cart = Input::get('cart');
			$member_id = Input::get('member_id');
			$cash = Input::get('cash');
			$member_credit = Input::get('member_credit');
			$cheque = Input::get('cheque');
			$credit_card = Input::get('credit_card');
			$bank_transfer = Input::get('bank_transfer');
			$deductions = Input::get('deductions');
			$terminal_id = Input::get('terminal_id');
			$reservation_id = Input::get('reservation_id');
			$order_type = Input::get('doc_type');
			$ctrl_number = Input::get('ctrl_number');
			$referrals = Input::get('referrals');
			$arr_queue_ids = insertQueues(Input::get('queues'),Input::get('queues_list'));
			$member_id = ($member_id) ? $member_id : 0;
			$withoutput = true;

		}


		insertReferrals($referrals,$member_id);
		$user = new User();
		$payment_cash =$cash;

		$payment_member_credit = $member_credit;
		$payment_member_deduction = $deductions;

		$payment_credit = $credit_card;
		$payment_bt = $bank_transfer;
		$payment_cheque =$cheque;

		$payment = new Payment();

		if(!$terminal_id) {
			die("Please set up terminal first.");
		}
		$scompany =$user->data()->company_id;
		$payment->create(array(
			'created' => time(),
			'company_id' => $scompany,
			'is_active' => 1
		));



		$payment_lastid = $payment->getInsertedId();

		if($reservation_id){
			$pos_res = new Pos_reservation();
			$pos_res->update(
			 	[
				    'status' => 1,
				    'payment_id' => $payment_lastid,
			    ], $reservation_id
			);
		}

		$invoice = "";
		$dr = "";
		$pr= "";



		$cashier_id = $user->data()->id;
		$station_id=0;

		$sdr ='';$sinv ='';$sir =''; // change later




			if($terminal_id) {
				$terminl = new Terminal($terminal_id);
				if($order_type == 1){
					$invoice = $ctrl_number;
					$terminl->update(
						['invoice' => $invoice] , $terminal_id
					);
					$dr = 0;
					$pr = 0;
				} else if ($order_type == 2) {
					$invoice = 0;
					$dr =  $ctrl_number;
					$terminl->update(
						['dr' => $dr] , $terminal_id
					);
					$pr = 0;
				}else if ($order_type == 3) {
					$invoice = 0;
					$dr =  0;
					$pr =  $ctrl_number;
					$terminl->update(
						['ir' => $pr] , $terminal_id
					);
				}
			}




		$sdr = ($sdr) ? 'Dr: '.$sdr:'';
		$sinv = ($sinv) ? 'Inv: '.$sinv:'';
		$sir = ($sir) ? 'Ir: '.$sir:'';
		$sdate = time();

		$terminal = new Terminal();
		$terminal_mon = new Terminal_mon();

		if($payment_credit && $payment_cheque != "[]"){

				$payment_credit = json_decode($payment_credit);


			$credit = new Credit();
			$total_amount_cc = 0;

			foreach($payment_credit as $c){
				$total_amount_cc += $c->amount;
				$credit->create(array(
					'amount' =>  $c->amount,
					'bank_name'=> $c->bank,
					'card_type' => $c->type,
					'date' =>  $c->date,
					'is_active' => 1,
					'created' => $sdate,
					'modified' => $sdate,
					'payment_id' => $payment_lastid
				));
			}

		}
		if($payment_bt && $payment_cheque != "[]"){


			$payment_bt = json_decode($payment_bt);


			$bank_transfer = new Bank_transfer();
			$total_amount_bt = 0;
			foreach($payment_bt as $c){
				$total_amount_bt += $c->amount;
				$bank_transfer->create(array(
					'amount' => $c->amount,
					'bankto_name' => $c->bank,
					'is_active' => 1,
					'created' => $sdate,
					'date' => strtotime($c->date),
					'modified' => $sdate,
					'payment_id' => $payment_lastid
				));
			}

		}
		if($payment_cheque && $payment_cheque != "[]"){
			// insert cheque

			$payment_cheque = json_decode($payment_cheque);


			$cheque = new Cheque();
			$total_amount_ch = 0;
			foreach($payment_cheque as $c){
				$total_amount_ch += $c->amount;
				$cheque->create(array(
					'check_number' => $c->number,
					'amount' => $c->amount,
					'bank'=>$c->bank,
					'payment_date' => strtotime($c->ate),
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
		if($payment_member_deduction && $payment_cheque != "[]"){
			// insert cash

			$payment_member_deduction = json_decode($payment_member_deduction);


			if(count($payment_member_deduction)){
				foreach($payment_member_deduction as $deduct_member){
					$pdeduct = new Deduction();
					$deduction_amount = ($deduct_member->amount) ? $deduct_member->amount : 0;
					$member_deduction_remarks = ($deduct_member->remarks) ? $deduct_member->remarks : '';
					$pdeduct->create(array(
						'amount' =>$deduction_amount,
						'is_active' => 1,
						'created' => $sdate,
						'remarks' => $member_deduction_remarks,
						'payment_id' => $payment_lastid,
						'member_id' => $deduct_member->member_id
					));
				}
			}
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
				'member_id' => $member_id,
				'is_cod' => 0
			));
		}


		$cart_item = json_decode($cart);

		$newsales = new Sales();

		$sv = "";
		$now= time();
		$inventory = new Inventory();
		$rack = new Rack();
		$rack_default = $rack->getRackForSelling($user->data()->branch_id);
		$inv_mon = new Inventory_monitoring();
		foreach($cart_item as $item){

			$orig_qty = $item->qty;
			$withDifAgent = false;
			if(count($item->agent_list) > 0){
				$withDifAgent = true;
				foreach($item->agent_list as $ag){
					if($ag->qty <= $orig_qty){
						$orig_qty -= $ag->qty;

						$item->discount = -1 * $item->discount;

						$newsales->create(array(
							'terminal_id' => $terminal_id,
							'invoice' => $invoice,
							'sv' => $sv,
							'dr' => $dr,
							'ir' => $pr,
							'item_id' => $item->item_id,
							'price_id' => $item->price_id,
							'qtys' =>  $ag->qty,
							'discount' => 0,
							'store_discount' => 0,
							'adjustment' => 0,
							'member_adjustment' => $item->discount,
							'terms' => 0,
							'company_id' => $user->data()->company_id,
							'cashier_id' => $user->data()->id,
							'sold_date' => $now,
							'payment_id' =>$payment_lastid,
							'member_id' => $member_id,
							'station_id' => 0,
							'sales_type' => 1,
							'agent_id' => $ag->agent_id,
							'from_od' => 0

						));

						// check commission

						if($ag->agent_id) {
							$com = new Commission_item();
							$agent_com = $com->hasComission($item->item_id,$ag->agent_id);
							$commission_amount = 0;
							if($agent_com && ($agent_com->amount || $agent_com->perc)){
								if($agent_com->perc){
									$perc = $agent_com->perc / 100;
									$commission_amount = $item->price * $perc;
								} else {
									$commission_amount = $agent_com->amount;
								}


							} else {
								$generic_com = $com->hasComission($item->item_id); // generic
								if($generic_com && ($generic_com->amount || $generic_com->perc)){
									if($generic_com->perc){
										$perc = $generic_com->perc / 100;
										$commission_amount = $item->price * $perc;
									} else {
										$commission_amount = $generic_com->amount;
									}

								}
							}
							if($commission_amount){
								$com_list = new Commission_list();
								$com_list->create([
										'agent_id' => $ag->agent_id,
										'item_id' => $item->item_id,
										'amount' => $commission_amount * $ag->qty,
										'created' => $now,
										'company_id' => $user->data()->company_id,
										'status' =>0
									]
								);
							}
						}
					}
				}

			}
			if($orig_qty){
				$item->discount = -1 * $item->discount;
				if($withDifAgent){
					$item->agent_id = 0;
				}

				$newsales->create(array(
					'terminal_id' => $terminal_id,
					'invoice' => $invoice,
					'sv' => $sv,
					'dr' => $dr,
					'ir' => $pr,
					'item_id' => $item->item_id,
					'price_id' => $item->price_id,
					'qtys' =>  $orig_qty,
					'discount' => 0,
					'store_discount' => 0,
					'adjustment' => 0,
					'member_adjustment' => $item->discount,
					'terms' => 0,
					'company_id' => $user->data()->company_id,
					'cashier_id' => $user->data()->id,
					'sold_date' => $now,
					'payment_id' =>$payment_lastid,
					'member_id' => $member_id,
					'station_id' => 0,
					'sales_type' => 1,
					'agent_id' => $item->agent_id,
					'from_od' => 0

				));

				// check commission

				if($item->agent_id) {
					$com = new Commission_item();
					$agent_com = $com->hasComission($item->item_id,$item->agent_id);
					$commission_amount = 0;
					if($agent_com && ($agent_com->amount || $agent_com->perc)){
						if($agent_com->perc){
							$perc = $agent_com->perc / 100;
							$commission_amount = $item->price * $perc;

						} else {
							$commission_amount = $agent_com->amount;
						}

					} else {
						$generic_com = $com->hasComission($item->item_id); // generic
						if($generic_com && ($generic_com->amount || $generic_com->perc)){
							if($generic_com->perc){
								$perc = $generic_com->perc / 100;
								$commission_amount = $item->price * $perc;
							} else {
								$commission_amount = $generic_com->amount;
							}

						}
					}
					if($commission_amount){
						$com_list = new Commission_list();
						$com_list->create([
								'agent_id' => $item->agent_id,
								'item_id' => $item->item_id,
								'amount' => $commission_amount * $orig_qty,
								'created' => $now,
								'company_id' => $user->data()->company_id,
								'status' =>0
							]
						);
					}
				}
			}



			if($item->is_bundle == 0){
				$prod = new Product($item->item_id);

				if ($prod->data()->item_type == 2 || $prod->data()->item_type == 3  || $prod->data()->item_type == 4 || $prod->data()->item_type == 5){
					for($startingservice = 0; $startingservice < $item->qty; $startingservice++){
						$con = new Consumable();
						$myCon = $con->getConsumableByItemId($item->item_id);

						$newServ = new Service();
						$start = time();
						$cday = $myCon->days;
						$endDate = strtotime(date('m/d/Y',$start) . $cday . " day");
						$newServ->create(array(
							'member_id' => $member_id,
							'item_id' => $item->item_id,
							'start_date' => $start,
							'end_date' =>$endDate,
							'consumable_qty' =>$myCon->qty,
							'company_id' => $user->data()->company_id,
							'payment_id' => $payment_lastid
						));
						$servlastid = $newServ->getInsertedId();
						if($prod->data()->item_type == 4){
							$con_amount = new Consumable_amount();
							$n = time();
							$pricecon = $prod->getPrice($item->item_id);
							$con_amount->create(array(
								'service_id' => $servlastid,
								'amount' => $pricecon->price,
								'item_id' => $item->item_id,
								'member_id' => $member_id,
								'is_active' => 1,
								'created' => $n,
								'modified' => $n,
								'payment_id' => $payment_lastid
							));
						}
						if($prod->data()->item_type == 5){
							$con_free = new Consumable_freebies();
							$con_free_amount = $con_free->getConsumableFreebiesAmount($item->item_id);

							$n = time();
							$con_free->create(array(
								'service_id' => $servlastid,
								'amount' => $con_free_amount->amount,
								'item_id' =>$item->item_id,
								'member_id' => $member_id,
								'is_active' => 1,
								'created' => $n,
								'modified' => $n,
								'payment_id' =>  $payment_lastid
							));
						}
					}

				} else {
					if($prod->data()->item_type == -1){
						// deduct inventory
						$curinventory = $inventory->getQty($item->item_id,$user->data()->branch_id,$rack_default->id);
						$newqty = $curinventory->qty - $item->qty;
						$inventory->update(array(
							'qty' => $newqty
						), $curinventory->id);
						// insert monitoring


						$inv_mon->create(array(
							'item_id' =>$item->item_id,
							'rack_id' => $rack_default->id,
							'branch_id' => $user->data()->branch_id,
							'page' => 'ajax/ajax_pos.php',
							'action' => 'Update',
							'prev_qty' => $curinventory->qty,
							'qty_di' => 2,
							'qty' => $item->qty,
							'new_qty' => $newqty,
							'created' => time(),
							'user_id' => $user->data()->id,
							'remarks' => 'Deduct inventory upon selling on POS, Payment ID: ' . $payment_lastid,
							'is_active' => 1,
							'company_id' => $user->data()->company_id
						));
					}

				}
			} else {
				$bund_arr = json_decode($item->bundle_arr);
				if($bund_arr){

					foreach($bund_arr as $b){
						// deduct inventory
						$curinventory = $inventory->getQty($b->item_id_child,$user->data()->branch_id,$rack_default->id);
						$newqty = $curinventory->qty - $b->used_qty;
						$inventory->update(array(
							'qty' => $newqty
						), $curinventory->id);
						// insert monitoring


						$inv_mon->create(array(
							'item_id' =>$b->item_id_child,
							'rack_id' => $rack_default->id,
							'branch_id' => $user->data()->branch_id,
							'page' => 'ajax/ajax_pos.php',
							'action' => 'Update',
							'prev_qty' => $curinventory->qty,
							'qty_di' => 2,
							'qty' => $b->used_qty,
							'new_qty' => $newqty,
							'created' => time(),
							'user_id' => $user->data()->id,
							'remarks' => 'Deduct inventory upon selling on POS, Payment ID: ' . $payment_lastid,
							'is_active' => 1,
							'company_id' => $user->data()->company_id
						));
					}

				}


			}


		}
		if($withoutput){
			echo json_encode(['success' => true]);
		} else {
			return true;
		}


	}

	function checkSubs(){
		$member_id = Input::get('member_id');
		$service = new Service();
		$user = new User();
		$subs = $service->getSubsciption($user->data()->company_id,$member_id);


		if($subs){
			foreach($subs as $s){
				$s->end_date = date('m/d/Y',$s->end_date);
				$s->extends_to = strtotime($s->end_date . "30 days");
				$s->extends_to = date('m/d/Y',$s->extends_to);

				echo json_encode($s);
				break;
			}
		} else {
			echo "{}";
		}


	}




