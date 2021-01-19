<?php
	include 'ajax_connection.php';
	$functionName = Input::get("functionName");
	$functionName();


	function smBottles(){

		$item_id = Input::get('item_id');
		$qty = Input::get('qty');
		$received_date = Input::get('received_date');
		$branch_id = Input::get('branch_id');
		$status = 4;

		$order = new Wh_order();
		$now = time();
		$user = new User();
		$order->create(array(
			'branch_id' =>Configuration::getMainBranch(),
			'member_id' => 0,
			'to_branch_id' => $branch_id,
			'remarks' => '',
			'client_po' => '',
			'shipping_company_id' => 0,
			'created' => $now,
			'company_id' => $user->data()->company_id,
			'user_id' => $user->data()->id,
			'is_active' => 1,
			'status' => $status,
			'for_pickup' => 0,
			'is_reserve' => 0,
			'stock_out' => 1,
			'is_received' => 1,
			'received_date' => strtotime($received_date)
		));
		$lastItOrder = $order->getInsertedId();
		$order_details = new Wh_order_details();
		$prod = new Product();
		$price = $prod->getPrice($item_id);
		$order_details->create(array(
			'wh_orders_id' => $lastItOrder,
			'item_id' => $item_id,
			'price_id' => $price->id,
			'qty' => $qty,
			'created' => $now,
			'modified' => $now,
			'price_adjustment' => 0,
			'company_id' => $user->data()->company_id,
			'is_active' => 1,
			'terms' => 0,
			'original_qty' => $qty,
			'member_adjustment' => 0
		));

		echo "Update complete";
	}


	function insertToSendMessage(){
		$num = Input::get('num');
		$msg = Input::get('msg');

		if($num && $msg){
			$sms = new Sms_to_send();
			$sms->create([
				'msg' => $msg,
				'number' => $num,
				'status' => 0,
				'created' => time()
			]);
			echo "Added successfully.";
		} else {
			echo "Request failed.";
		}
	}

	function deleteDicerDeposits(){
		$id = Input::get('id');
		if($id && is_numeric($id)){
			$dicer = new Dicer_deposit();
			$dicer->deleteP($id);
			echo "Deleted successully";
		} else {
			echo "Request failed";
		}
	}



	function messageDetails(){
		$number = Input::get('number');
		$msg = Input::get('msg');
		$id = Input::get('id');
		$date_received = Input::get('date_received');
		$sms = new Sms_receive($id);
		$sms_gateway = new Sms_gateway();
		if($sms->data()->terminal_id){
			$terminal = new Terminal();
			$results = $terminal->getTerminalData($sms->data()->terminal_id);
			$branch_id = $results->branch_id;
			$branch_name = $results->branch_name;
		} else {
			$results = $sms_gateway->getBranchByNumber($number);
			$branch_id = $results->branch_id;
			$branch_name = $results->branch_name;
		}

		$data = saveEndingInventory($msg,$branch_id,$date_received,$sms->data()->payment_id);
		$expense = new Branch_expense();

		$expenses = $expense->getPending($branch_id,str_replace('/','-',date('Y-m-d',strtotime($date_received))));
		$arr_ex = [];
		if($expenses){
			foreach($expenses as $ex){
				$arr_ex[] = $ex;
			}
		}
		// get sales deposit
		$dd = new Dicer_deposit();

		$dd_result = $dd->getDeposit($branch_id,str_replace('/','-',date('Y-m-d',strtotime($date_received))));
		$sales_deposit = 0;
		if(isset($dd_result) && isset($dd_result->amount) && $dd_result->amount){
			$sales_deposit= $dd_result->amount;
		}

		$badorder = new Bad_order();
		$dt1 = strtotime($date_received);
		$dt2 = strtotime($date_received . " 1 day -1min");


		$results = $badorder->badorderDicer($dt1,$dt2,$branch_id);
		$arr_badorder = [];

		if($results){
			foreach($results as $res){
				$arr_badorder[$res->item_id] = $res->qty;
			}
		}

		echo json_encode(['expenses' => $arr_ex,'data' => $data, 'branch_id'=> $branch_id,'branch_name' => $branch_name,'sales_deposit' => $sales_deposit,'badorder'=>$arr_badorder ]);

	}
	function declineData(){
		$id = Input::get('id');
		$sms = new Sms_receive();
		$sms->update(['status' => 6],$id);
		echo "Declined successfully.";
	}
	function smsSummary(){
		$branch_id = Input::get('branch_id');
		$dt_from = Input::get('dt_from');
		$dt_to = Input::get('dt_to');

		if($dt_to && $dt_from){
			$dt_from = strtotime($dt_from);
			$dt_to = strtotime($dt_to . "1 day 1 sec");
		} else {
			$dt_from = strtotime(date('m/d/Y') . "-11 days");
			$dt_to = strtotime(date('m/d/Y') . "1 day -1 min");
		}

		echo "<h5>From: ".date('m/d/Y',$dt_from)." To: ".date('m/d/Y',$dt_to)."</h5>";
		if($branch_id && $dt_from && $dt_to){
			$sms = new Sms_receive();
			$list = $sms->getSummary($branch_id,$dt_from,$dt_to);
			if($list){
				// get all item
				$item = new Product();
				$items = $item->get_active('items',[1,'=', 1]);
				$arr_items = [];

				$all_used_item = [];
				$daily_arr = [];
				$all_used_dates = [];
				$arr_payment_id = [];

				$arr_total_bo = [];
				foreach($items as $i){
					$arr_items[$i->id] = $i->item_code;
				}
				foreach($list as $l){
					$msg = trim($l->message);
					$msg = explode(' ', $msg);
					$i = 1;
					$ctr = 1;
					$arr = [];
					$newBo = new Bad_order();
					$getbo = $newBo->getBOTotal($l->id);
					$cur_total_bo = 0;
					if(isset($getbo->total_bo)){
						$cur_total_bo = $getbo->total_bo;
					}
					$arr_total_bo[$l->date_received] =$cur_total_bo ;
					$arr_payment_id[$l->date_received] =$l->payment_id ;
					foreach($msg as $d){
						if(isset($arr[$i])) $arr[$i] .= " " .$d; else $arr[$i] = $d;
						if($ctr % 2 == 0) $i++;
						$ctr++;
					}

					foreach($arr as $a){

						$pair = explode(" ",$a);
						if(!(isset($pair[0]) && isset($pair[1]))){
							continue;
						}
						if(is_numeric($pair[0]) && is_numeric($pair[1])){
								$item_id = $pair[0];
								$qty = $pair[1];



 								$daily_arr[$arr_items[$item_id]][$l->date_received] = ['item_code' => $arr_items[$item_id],'qty' => $qty,'item_id' => $item_id];
								if(!in_array($l->date_received,$all_used_dates)){
									$all_used_dates[] = $l->date_received;
								}
								if(!in_array($arr_items[$item_id],$all_used_item)){
									$all_used_item[] =$arr_items[$item_id] ;
								}
						}
					}

				}
				echo "<div class='table-responsive'>";
				echo "<table class='table table-bordered table-condensed'>";
				echo "<tr>";
				echo "<th >Item</th>";
				function date_sort($a, $b) {
					return strtotime($a) - strtotime($b);
				}
				usort($all_used_dates, "date_sort");

				foreach($all_used_dates as $dt){
					echo "<th>$dt</th>";
				}
				echo "</tr>";
				// to update
				$tocheckbranch = 0;
				$date_to_start = "";
				$arr_to_insert = [];

				foreach($all_used_item as $ui){
					$cur  = $daily_arr[$ui];
				//	dump($cur);
					echo "<tr>";
					echo "<td style='border-top: 1px solid #ccc;'><span class='text-danger'>$ui</span></td>";
					foreach($all_used_dates as $dt){
						if($cur[$dt]){
							//dump($cur[$dt]);
							$f = $cur[$dt];
							if($dt == $date_to_start && $branch_id == $tocheckbranch){
								$arr_to_insert[$f['item_id']] = $f['qty'];
							}
							echo "<td style='border-top: 1px solid #ccc;'>$f[qty]</td>";
						} else {
							echo "<td style='border-top: 1px solid #ccc;'>0</td>";
						}



					}
					echo "</tr>";
				}
				echo "<tr>";
				echo "<th style='border-top: 1px solid #ccc;'>Sales Deposit</th>";
				foreach($all_used_dates as $dt){
					$dd = new Dicer_deposit();

					$dd_result = $dd->getDeposit($branch_id,str_replace('/','-',$dt));

					$sales_deposit = 0;
					if(isset($dd_result) && isset($dd_result->amount) && $dd_result->amount){
						$sales_deposit= $dd_result->amount;
					}
					echo "<th style='border-top: 1px solid #ccc;'>" . number_format($sales_deposit,2). "</th>";
				}
				echo "</tr>";

				echo "<tr>";
				echo "<th style='border-top: 1px solid #ccc;'>Expense</th>";
				foreach($all_used_dates as $dt){
					$exp = new Branch_expense();
					$pid = isset($arr_payment_id[$dt]) ? $arr_payment_id[$dt] : 0;
					if($pid){
						$exp_res = $exp->getTotalExpense($pid);
						$exp_amount = $exp_res->amt;

					} else {
						$exp_amount = 0;
					}
					echo "<th style='border-top: 1px solid #ccc;'>" . number_format($exp_amount,2). "</th>";
				}
				echo "</tr>";

				echo "<tr>";
				echo "<th style='border-top: 1px solid #ccc;'>Bad Order</th>";
				foreach($all_used_dates as $dt){
					$bototal = isset($arr_total_bo[$dt]) ? $arr_total_bo[$dt] : 0;
					echo "<th style='border-top: 1px solid #ccc;'>" . number_format($bototal,2). "</th>";
				}
				echo "</tr>";

				echo "<tr>";
				echo "<th style='border-top: 1px solid #ccc;'>Received Order</th>";
				$wh_order = new Wh_order();
				foreach($all_used_dates as $dt){
					$received_date = strtotime($dt);
					$det= $wh_order->getByReceivedDate($branch_id,$received_date);
					$det_list = "N/A";
					if($det){
						$det_list = "";
						foreach($det as $d){
							$det_list .= "<a href='#' class='btnGetOrderDetails span-block'>$d->id</a>";
						}
					}

					echo "<th style='border-top: 1px solid #ccc;'>$det_list</th>";
				}
				echo "</tr>";

				echo "</table>";
				echo "</div>";

				if(count($arr_to_insert)){
					$newrack = new Rack();
					$rack_cur = $newrack->getRackForSelling($tocheckbranch);
					$rack_id = $rack_cur->id;
					foreach($arr_to_insert as $item_id => $qty){

						$inv = new Inventory();
						//$inv->updateInventory($rack_id,$tocheckbranch,$item_id,$qty);
						//echo "Rack ID: $rack_id Item: $item_id Qty: $qty Branch: $tocheckbranch <br>";
					}

				}
			} else {
				echo "<div class='alert alert-info'>No record found</div>";
			}
		} else {
			echo "<div class='alert alert-info'>Choose branch first.</div>";
		}
	}

	function  saveEndingInventory($data,$branch_id=0,$date_received=0,$payment_id= 0){

		$data = explode(" " , $data);
		$arr = [];
		$arr_codes = ['CAS'=>1,
			'CAS2'=>2,
			'CSAS'=>3,
			'CSAS2'=>4,
			'CRS'=>5,
			'CRS2'=>6,
			'CCN'=>7,
			'CSCN'=>8,
			'CSG'=>9,
			'CMN'=>10,
			'CPB'=>11,
			'CCN2'=>12,
			'CC'=>13,
			'CAB'=>14,
			'CSS'=>15,
			'CCFN'=>16,
			'CCGP'=>17,
			'PB'=>18,
			'CCCP'=>19,
			'CRA'=>20,
			'PB2'=>21,
			'CHCPS'=>22,
			'CHCPM'=>23,
			'CS'=>24,
			'CI'=>25,
			'CB'=>26,
			'CB2'=>27,
			'CB3'=>28,
			'CLM'=>29,
			'CLM2'=>30,
			'FSC'=>31,
			'SFSC'=>32,
			'MC'=>33,
			'PFSV'=>34,
			'SFSV'=>35,
			'CT'=>36,
			'FRT'=>72,
			'CN'=>73,
			'GP'=>74,
			'SKIN'=>75,
			'GL'=>76,
			'SL'=>77,
			'PL'=>78,
			'SALT'=>79,
			'SP'=>80,
			'SPC'=>81,
			'CP'=>82,
			'LID'=>83,
			'ZL'=>84,
			'ZLS'=>85,
			'ZLM'=>86,
			'RPB'=>87,
			'RCN'=>88,
			'RC'=>89,
			'RS'=>90,
			'RCGP'=>91,
			'RCN2'=>92,
			'RSCN'=>93,
			'RCC'=>94,
			'AS'=>95,
			'AS2'=>96,
			'SG'=>97,
			'SAS'=>98,
			'SAS2'=>99,
			'MN'=>100,
			'PB3'=>101,
			'CN2'=>102,
			'C2'=>103,
			'RS2'=>104,
			'RS3'=>105,
			'CN3'=>106,
			'SC3'=>107,
			'SS'=>108,
			'AB'=>109,
			'CFP'=>110,
			'CGP'=>111,
			'PB4'=>112,
			'CCP'=>113,
			'CA'=>114,
			'CCPS'=>115,
			'CCPM'=>116,
			'RA'=>117,
			'CML'=>118,
			'CC2'=>120,
			'SBW'=>121,
			'SEPC'=>122,
			'APRON'=>123,
			'PWPS1'=>124,
			'PWPS2'=>125,
			'PWPS3'=>126,
			'PV' => 37,
			'SV' => 38,
			'CWS' => 39,
			'CWM' => 40,
			'CWL' => 41,
			'BW14' => 42,
			'BW12' => 43,
			'BW34' => 44,
			'BW2' => 45,
			'BW4' => 46,
			'BW10' => 47,
			'PP712' => 48,
			'PP814' => 49,
			'PE712' => 50,
			'PE814' => 51,
			'PBM' => 52,
			'PBT' => 53,
			'PBM' => 54,
			'PBL' => 55,
			'P58' => 56,
			'P1620' => 57,
			'CP12' => 58,
			'CP1' => 59,
			'VC' => 60,
			'VP' => 61,
			'ALC' => 62,
			'RP' => 63,
			'TIS' => 64,
			'GLV' => 65,
			'RUG' => 66,
			'EC' => 67,
			'SAB' => 68,
			'CLS' => 69,
			'GB' => 70,
			'SB' => 71,
			'COKE' => 132,
			'CTM' => 133,
			'CTL' => 134,
			'BL' => 145,
			'CRL' => 148,
			'CUL' => 141,
			'FL' => 154,
			'GIL' => 142,
			'GAL' => 147,
			'GTL' => 152,
			'JTL' => 153,
			'KL' => 144,
			'ML' => 150,
			'OL' => 143,
			'PFL' => 149,
			'STL' => 146,
			'YL' => 151,
			'SYR' => 181,
			'RL' => 196,
			'CLID' => 157,
			'CUP' => 155,
		];
		if(count($data) > 0){
			$i = 1;
			$ctr = 1;
			foreach($data as $d){
				if(isset($arr[$i])) $arr[$i] .= " " .$d; else $arr[$i] = $d;
				if($ctr % 2 == 0) $i++;
				$ctr++;
			}
			$inv = new Inventory();
			$getAllInv = $inv->getAllInventories(1,$branch_id);
			$arr_inv = [];
			if($getAllInv){
				foreach($getAllInv as $binv){
					$arr_inv[$binv->item_id] = $binv->qty;
				}
			}
			$wh_order = new Wh_order();
			$details = $wh_order->getBranchOrderScheduleToday($branch_id,$date_received);
			$arr_order = [];
			if($details){

				foreach($details as $det){
					if(isset($arr_order[$det->item_id])){
						$arr_order[$det->item_id] += $det->qty;
					} else {
						$arr_order[$det->item_id] = $det->qty;
					}


				}
			}
			$to_add_in_sales = [];
			$data_arr =[];

			foreach($arr as $a){

				$pair = explode(" ",$a);

				if(!is_numeric($pair[0])){
					$pair[0] = strtoupper($pair[0]);
					if(isset($arr_codes[$pair[0]])){
						$pair[0] = $arr_codes[$pair[0]];
					}
				}

				if(!(isset($pair[0]) && isset($pair[1]))){
					continue;
				}
				if(is_numeric($pair[0]) && is_numeric($pair[1])){
					// get inventory
					$cur_inv = isset($arr_inv[$pair[0]]) ? $arr_inv[$pair[0]] : 0;

					// get order
					$cur_order = isset($arr_order[$pair[0]]) ? $arr_order[$pair[0]] : 0;

					// get total inv
					$total_inventory = $cur_inv +  $cur_order;

					//get difference to reported inv

					$diff = $total_inventory - $pair[1];

					$cur_inv = formatQuantity($cur_inv,true);
					$r = (float) $pair[1];
					$c = (float) $cur_inv;

					if($r != 0 || $cur_order != 0 || $c != 0) {
					if($diff > 0){
						// add sales
						$to_add_in_sales[] = ['item_id' => $pair[0], 'qty' => $diff];
					} else {
						//$diff = 0;
					}

					// insert snapshot

					// update inventory
					$prod = new Product($pair[0]);
					$price = $prod->getPrice($pair[0]);
					$adjutment = new Item_price_adjustment();
					$p_adjustment = $adjutment->getAdjustment($branch_id,$pair[0]);
					$price_final = $price->price + $p_adjustment->adjustment;
					$data_arr[] = ['item_code' => $prod->data()->item_code, 'price' => $price_final,'reported' => $pair[1],'item_id' => $pair[0],'cur_inv' => $cur_inv,'cur_order' => $cur_order, 'total_inv' => $total_inventory, 'diff' => $diff];

					}
				}
			}
			if($payment_id){
				$sold = new Sales();
				$sold_list = $sold->salesTransactionBaseOnPaymentId($payment_id);
				$to_add_in_sales = [];
				foreach($sold_list as $sl){
					$to_add_in_sales[] =  ['item_id' => $sl->item_id, 'qty' =>  $sl->qtys];

				}
			}
			return ['details' => $data_arr,'sold' => $to_add_in_sales];
		}
	}


	function editInventory(){
		$branch_id = 57;
		$item_id = 1;
		$qty = 3;
		$dateInv = 1;
	}

	function processedData($id = 0){


		$type = Input::get('type');

		if($type == 1){

			processedNJL($id);

		} else {

			processedNormal($id);

		}


	}
	function processedNJL($id){

		$fresh_lemon_id = 196;
		if(!$id){
			$id = Input::get('id');
		}

		if(!is_numeric($id)){
			die("Invalid data");
		}
		$sms = new Sms_receive($id);
		$number = $sms->data()->number;
		$msg = $sms->data()->message;
		$sms_gateway = new Sms_gateway();

		$results = $sms_gateway->getBranchByNumber($number);
		if($sms->data()->terminal_id){
			$terminal_id = $sms->data()->terminal_id;
			$tclass = new Terminal($terminal_id);
		} else {
			$terminal_id = $results->terminal_id;
			$tclass = new Terminal($terminal_id);
		}

		$branch_id = $tclass->data()->branch_id;
		$branchcls = new Branch($branch_id);
		$member_id = $branchcls->data()->member_id;

		$data = saveEndingInventory($msg,$branch_id,$sms->data()->date_received);

		$sold = $data['sold'];
		$inv = $data['details'];
		$total_badorder = Input::get('total_badorder');
		$bad_order_decoded = json_decode(Input::get('total_badorder_items'));
		$bad_order_arr = [];
		if($bad_order_decoded){
			removeBackloadCurrent($branch_id,$sms->data()->date_received);
			$newRequest = new Bad_order();
			$now = time();
			$total_badorder = str_replace(',','',$total_badorder);

			$newRequest->create(array(
				'branch_id' => $branch_id,
				'supplier_id' => 0,
				'remarks' => '',
				'supplier_order_id' => $id,
				'company_id' => 1,
				'is_active' => 1,
				'status' => 1,
				'created' => $now,
				'from_dicer' => 1,
				'total_bo' => $total_badorder
			));

			$bo_last_id = $newRequest->getInsertedId();
			foreach($bad_order_decoded as $bad_order){
				$bad_order_arr[$bad_order->item_id] = $bad_order->qty;
				$details = new Bad_order_detail();
				$details->create(array(
					'bad_order_id' => $bo_last_id,
					'item_id' => $bad_order->item_id,
					'qty' => $bad_order->qty,
					'rack_id' => 0,
					'remarks' => '',
					'created' => $now,
					'company_id' => 1
				));
			}

		}



		$expense = new Branch_expense();
		$expenses = $expense->getPending($branch_id,str_replace('/','-',date('Y-m-d',strtotime($sms->data()->date_received))));
		$total_expense = 0;
		if($expenses){
			foreach($expenses as $ex){
				$total_expense += $ex->amount;
			}
		}

		$user = new User();
		$scompany =$user->data()->company_id;

		if($sold){
			$payment = new Payment();
			$payment->create(array(
				'created' => time(),
				'company_id' => $scompany,
				'is_active' => 1,
				'remarks' => '',
				'po_number' => ''
			));

			$payment_lastid = $payment->getInsertedId();
			$date  = strtotime( $sms->data()->date_received );
			$total = 0;

			foreach($sold as $s){
				if($s['item_id'] == $fresh_lemon_id){
					continue;
				}
				// insert Sales
				$newsales = new Sales();
				$prod = new Product();
				$price = $prod->getPrice($s['item_id']);
				$adjutment = new Item_price_adjustment();
				$p_adjustment = $adjutment->getAdjustment($branch_id,$s['item_id']);
				$branch_adj = ($p_adjustment->adjustment) ? $p_adjustment->adjustment : 0;
				if(isset($bad_order_arr[$s['item_id']])){
					$s['qty'] = $s['qty'] - $bad_order_arr[$s['item_id']];
				}
				if($s['qty']){
					$final_adjustment = $s['qty'] * $branch_adj;
					$newsales->create(array(
						'terminal_id' => $terminal_id,
						'pref_inv' => '',
						'pref_ir' => '',
						'pref_dr' => '',
						'invoice' => '',
						'dr' => '',
						'ir' => '',
						'item_id' => $s['item_id'],
						'price_id' =>$price->id,
						'qtys' => $s['qty'],
						'discount' => 0,
						'store_discount' => 0,
						'adjustment' =>$final_adjustment,
						'member_adjustment' => 0,
						'company_id' => $scompany,
						'cashier_id' => 0,
						'sold_date' => $date,
						'payment_id' => $payment_lastid,
						'member_id' => $member_id,
						'station_id' => 0,
						'warranty' =>  0,
						'sales_type' =>  0,
						'agent_id' =>  0
					));
					$final_prices = ($price->price + $branch_adj)* $s['qty'];
					$total += $final_prices;
				}
			}
			// insert cash

			$now = time();
			$total_amount = $total - $total_expense;
			if($total_expense){

				$pdeduct = new Deduction();
				$deduction_amount = $total_expense;
				$member_deduction_remarks = "Branch expense";
				$pdeduct->create(array(
					'amount' =>$deduction_amount,
					'is_active' => 1,
					'created' => $now,
					'remarks' => $member_deduction_remarks,
					'payment_id' => $payment_lastid,
					'member_id' =>$member_id
				));

				$branch_ex = new Branch_expense();
				$branch_ex->updatePending($branch_id,$payment_lastid,str_replace('/','-',date('Y-m-d',strtotime($sms->data()->date_received))));

			}

			$pcash = new Cash();
			$pcash->create(array(
				'amount' =>$total_amount,
				'is_active' => 1,
				'created' => $now,
				'modified' => $now,
				'payment_id' => $payment_lastid
			));


		}

		if($inv){
			$rack = new Rack();
			$cur_rack = $rack->getRackForSelling($branch_id);
			$rack_id = $cur_rack->id;
			$addbatchcls = new Add_batch_inv();
			$now = time();
			$addbatchcls->create(array(
				'to_branch_id' => $branch_id,
				'supplier_id' => 0,
				'date_receive' =>$now,
				'packing_list_num' => '',
				'ref_num' => '',
				'company_id' => $user->data()->company_id,
				'user_id' => $user->data()->id,
				'created' => time()
			));

			$lastidbatch = $addbatchcls->getInsertedId();

			$inventory = new Inventory();


			foreach($inv as $i){

				$iid =  $i['item_id'];
				$bid = $branch_id;
				$rid = $rack_id;
				$qty = $i['reported'];

				if($inventory->checkIfItemExist($iid,$bid,$user->data()->company_id,$rid)){
					$curinventory = $inventory->getQty($iid,$bid,$rid);
					$inventory->updateInventory($rid,$bid,$iid,$qty);
					// monitoring
					$inv_mon = new Inventory_monitoring();
					$newqty =  $qty;
					$inv_mon->create(array(
						'item_id' => $iid,
						'rack_id' => $rid,
						'branch_id' => $bid,
						'page' => 'admin/addinventory',
						'action' => 'Update',
						'prev_qty' => $curinventory->qty,
						'qty_di' => 3,
						'qty' => $qty,
						'new_qty' => $newqty,
						'created' => time(),
						'user_id' => $user->data()->id,
						'remarks' => 'Reported inventory',
						'is_active' => 1,
						'company_id' => $user->data()->company_id
					));
				} else {
					if($qty == 0){
						continue;
					}
					$curinventory =0;
					$inventory->addInventory($iid,$bid,$qty,true,$rid);
					// monitoring
					$inv_mon = new Inventory_monitoring();
					$newqty = $curinventory + $qty;
					$inv_mon->create(array(
						'item_id' => $iid,
						'rack_id' => $rid,
						'branch_id' => $bid,
						'page' => 'admin/addinventory',
						'action' => 'Insert',
						'prev_qty' => $curinventory,
						'qty_di' => 3,
						'qty' => $qty,
						'new_qty' => $newqty,
						'created' => time(),
						'user_id' => $user->data()->id,
						'remarks' => 'Reported inventory',
						'is_active' => 1,
						'company_id' => $user->data()->company_id
					));
				}
				$addbatchdetails = new Add_batch_inv_detail();
				$addbatchdetails->create(array(
					'batch_id' => $lastidbatch,
					'item_id' => $i['item_id'],
					'qty' => $i['reported'],
					'is_active' => 1,
					'company_id' => $scompany,
					'rack_id' => $rack_id
				));
			}
		}
		$sms->update(['status' => 1,'payment_id' => $payment_lastid],$id);
		echo "Processed successfully.";

	}

	function processedNormal($id){
		if(!$id){
			$id = Input::get('id');
		}

		if(!is_numeric($id)){
			die("Invalid data");
		}
		$sms = new Sms_receive($id);
		$number = $sms->data()->number;
		$msg = $sms->data()->message;
		$sms_gateway = new Sms_gateway();

		$results = $sms_gateway->getBranchByNumber($number);
		if($sms->data()->terminal_id){
			$terminal_id = $sms->data()->terminal_id;
			$tclass = new Terminal($terminal_id);
		} else {
			$terminal_id = $results->terminal_id;
			$tclass = new Terminal($terminal_id);
		}

		$branch_id = $tclass->data()->branch_id;
		$branchcls = new Branch($branch_id);
		$member_id = $branchcls->data()->member_id;

		$data = saveEndingInventory($msg,$branch_id,$sms->data()->date_received);

		$sold = $data['sold'];
		$inv = $data['details'];
		$total_badorder = Input::get('total_badorder');
		$bad_order_decoded = json_decode(Input::get('total_badorder_items'));
		$bad_order_arr = [];
		if($bad_order_decoded){
			removeBackloadCurrent($branch_id,$sms->data()->date_received);
			$newRequest = new Bad_order();
			$now = time();
			$total_badorder = str_replace(',','',$total_badorder);

			$newRequest->create(array(
				'branch_id' => $branch_id,
				'supplier_id' => 0,
				'remarks' => '',
				'supplier_order_id' => $id,
				'company_id' => 1,
				'is_active' => 1,
				'status' => 1,
				'created' => $now,
				'from_dicer' => 1,
				'total_bo' => $total_badorder
			));

			$bo_last_id = $newRequest->getInsertedId();
			foreach($bad_order_decoded as $bad_order){
				$bad_order_arr[$bad_order->item_id] = $bad_order->qty;
				$details = new Bad_order_detail();
				$details->create(array(
					'bad_order_id' => $bo_last_id,
					'item_id' => $bad_order->item_id,
					'qty' => $bad_order->qty,
					'rack_id' => 0,
					'remarks' => '',
					'created' => $now,
					'company_id' => 1
				));
			}

		}



		$expense = new Branch_expense();
		$expenses = $expense->getPending($branch_id,str_replace('/','-',date('Y-m-d',strtotime($sms->data()->date_received))));
		$total_expense = 0;
		if($expenses){
			foreach($expenses as $ex){
				$total_expense += $ex->amount;
			}
		}

		$user = new User();
		$scompany =$user->data()->company_id;

		if($sold){
			$payment = new Payment();
			$payment->create(array(
				'created' => time(),
				'company_id' => $scompany,
				'is_active' => 1,
				'remarks' => '',
				'po_number' => ''
			));

			$payment_lastid = $payment->getInsertedId();
			$date  = strtotime( $sms->data()->date_received );
			$total = 0;

			foreach($sold as $s){
				// insert Sales
				$newsales = new Sales();
				$prod = new Product();
				$price = $prod->getPrice($s['item_id']);
				$adjutment = new Item_price_adjustment();
				$p_adjustment = $adjutment->getAdjustment($branch_id,$s['item_id']);
				$branch_adj = ($p_adjustment->adjustment) ? $p_adjustment->adjustment : 0;
				if(isset($bad_order_arr[$s['item_id']])){
					$s['qty'] = $s['qty'] - $bad_order_arr[$s['item_id']];
				}
				if($s['qty']){
					$final_adjustment = $s['qty'] * $branch_adj;
					$newsales->create(array(
						'terminal_id' => $terminal_id,
						'pref_inv' => '',
						'pref_ir' => '',
						'pref_dr' => '',
						'invoice' => '',
						'dr' => '',
						'ir' => '',
						'item_id' => $s['item_id'],
						'price_id' =>$price->id,
						'qtys' => $s['qty'],
						'discount' => 0,
						'store_discount' => 0,
						'adjustment' =>$final_adjustment,
						'member_adjustment' => 0,
						'company_id' => $scompany,
						'cashier_id' => 0,
						'sold_date' => $date,
						'payment_id' => $payment_lastid,
						'member_id' => $member_id,
						'station_id' => 0,
						'warranty' =>  0,
						'sales_type' =>  0,
						'agent_id' =>  0
					));
					$final_prices = ($price->price + $branch_adj)* $s['qty'];
					$total += $final_prices;
				}
			}
			// insert cash

			$now = time();
			$total_amount = $total - $total_expense;
			if($total_expense){

				$pdeduct = new Deduction();
				$deduction_amount = $total_expense;
				$member_deduction_remarks = "Branch expense";
				$pdeduct->create(array(
					'amount' =>$deduction_amount,
					'is_active' => 1,
					'created' => $now,
					'remarks' => $member_deduction_remarks,
					'payment_id' => $payment_lastid,
					'member_id' =>$member_id
				));

				$branch_ex = new Branch_expense();
				$branch_ex->updatePending($branch_id,$payment_lastid,str_replace('/','-',date('Y-m-d',strtotime($sms->data()->date_received))));

			}

			$pcash = new Cash();
			$pcash->create(array(
				'amount' =>$total_amount,
				'is_active' => 1,
				'created' => $now,
				'modified' => $now,
				'payment_id' => $payment_lastid
			));


		}

		if($inv){
			$rack = new Rack();
			$cur_rack = $rack->getRackForSelling($branch_id);
			$rack_id = $cur_rack->id;
			$addbatchcls = new Add_batch_inv();
			$now = time();
			$addbatchcls->create(array(
				'to_branch_id' => $branch_id,
				'supplier_id' => 0,
				'date_receive' =>$now,
				'packing_list_num' => '',
				'ref_num' => '',
				'company_id' => $user->data()->company_id,
				'user_id' => $user->data()->id,
				'created' => time()
			));

			$lastidbatch = $addbatchcls->getInsertedId();

			$inventory = new Inventory();


			foreach($inv as $i){

				$iid =  $i['item_id'];
				$bid = $branch_id;
				$rid = $rack_id;
				$qty = $i['reported'];

				if($inventory->checkIfItemExist($iid,$bid,$user->data()->company_id,$rid)){
					$curinventory = $inventory->getQty($iid,$bid,$rid);
					$inventory->updateInventory($rid,$bid,$iid,$qty);
					// monitoring
					$inv_mon = new Inventory_monitoring();
					$newqty =  $qty;
					$inv_mon->create(array(
						'item_id' => $iid,
						'rack_id' => $rid,
						'branch_id' => $bid,
						'page' => 'admin/addinventory',
						'action' => 'Update',
						'prev_qty' => $curinventory->qty,
						'qty_di' => 3,
						'qty' => $qty,
						'new_qty' => $newqty,
						'created' => time(),
						'user_id' => $user->data()->id,
						'remarks' => 'Reported inventory',
						'is_active' => 1,
						'company_id' => $user->data()->company_id
					));
				} else {
					if($qty == 0){
						continue;
					}
					$curinventory =0;
					$inventory->addInventory($iid,$bid,$qty,true,$rid);
					// monitoring
					$inv_mon = new Inventory_monitoring();
					$newqty = $curinventory + $qty;
					$inv_mon->create(array(
						'item_id' => $iid,
						'rack_id' => $rid,
						'branch_id' => $bid,
						'page' => 'admin/addinventory',
						'action' => 'Insert',
						'prev_qty' => $curinventory,
						'qty_di' => 3,
						'qty' => $qty,
						'new_qty' => $newqty,
						'created' => time(),
						'user_id' => $user->data()->id,
						'remarks' => 'Reported inventory',
						'is_active' => 1,
						'company_id' => $user->data()->company_id
					));
				}
				$addbatchdetails = new Add_batch_inv_detail();
				$addbatchdetails->create(array(
					'batch_id' => $lastidbatch,
					'item_id' => $i['item_id'],
					'qty' => $i['reported'],
					'is_active' => 1,
					'company_id' => $scompany,
					'rack_id' => $rack_id
				));
			}
		}
		$sms->update(['status' => 1,'payment_id' => $payment_lastid],$id);
		echo "Processed successfully.";
	}

	function removeBackloadCurrent($branch_id, $received){

		$newRequest = new Bad_order();

		$dt1 = strtotime($received);

		$dt2 = strtotime( $received. "1 day -1 min");

		$newRequest->deleteBadorder($dt1,$dt2,$branch_id);


	}

	function getToSend(){
		$sms = new Sms_to_send();
		$list = $sms->getPending();
		$ids = "";
		$arr=[];
		foreach($list as $l){
			$ids .= $l->id . ",";
			$arr[] = ['number' => $l->number,'msg' => $l->msg];
		}

		$ids = rtrim($ids,",");

		echo json_encode(['list' => $arr,'ids' => $ids]);

	}

	function updateSMSToSend(){
		$ids = Input::get('ids');
		$explode = explode(',',$ids);

		if(count($explode)){
			$toUpdate = "";
			foreach($explode as $ex){
				if(is_numeric($ex)){
					$toUpdate .= $ex .",";
				}
			}
			$toUpdate = rtrim($toUpdate,",");
			$sms = new Sms_to_send();
			$sms->updateReceived($toUpdate);
		}
	}



	function updateReportedInv(){
		$id = Input::get('id');
		$msg = Input::get('msg');
		$old_msg = Input::get('old_msg');
		$msg =  trim($msg);
		if($id && $msg && $old_msg){
			$sms = new Sms_receive();
			$sms->update([
				'message' => $msg,
				'old_msg' => $old_msg
			],$id);
		}
		echo "1";
	}

	function deleteExpense(){
		$expid = Input::get('expid');
		if($expid && is_numeric($expid)){
			$db = DB::getInstance();
			if($db->delete('branch_expenses',array('id' ,'=' ,$expid))){
				echo '1';
			}
		}
	}


	function addNewExpense(){
		$branch_id = Input::get('branch_id');
		$amount = Input::get('amount');
		$description = Input::get('description');
		$dt = strtotime(Input::get('dt'));
		$branch_expense = new Branch_expense();

		$branch_expense->create(
			[
				'description' => $description,
				'amount' => $amount,
				'branch_id' => $branch_id,
				'member_id' => 0,
				'dicer_name' => '',
				'created' => $dt,
				'company_id' => 1,
				'is_active' => 1
			]
		);
		echo 1;

	}


	function updateSalesDeposit(){

		$branch_id = Input::get('branch_id');
		$amount = Input::get('amount');
		$date_received  = Input::get('date_received');

		// check if sd exists

		$dd = new Dicer_deposit();
		$dd_result = $dd->getDeposit($branch_id,str_replace('/','-',date('Y-m-d',strtotime($date_received))));

		if(isset($dd_result) && isset($dd_result->amount) && $dd_result->amount){
			// update
			$dd->update(['amount' => $amount],$dd_result->id);
		} else {
			// insert
			$dt = strtotime($date_received . "1 day");
			$dd->create(
				[
					'company_id' => 1,
					'is_active' => 1,
					'created' =>$dt,
					'branch_id' => $branch_id,
					'ref_id' => time(),
					'amount' => $amount,
					'deposit_by' => ''
				]
			);
		}
		echo 1;

	}

	function updateSmsDate(){
		$id = Input::get('id');
		$dt = Input::get('dt');
		$sms = new Sms_receive();

		$dt = strtotime($dt);
		$dt = date('Y-m-d',$dt);
		$sms->update(['date_received' => $dt],$id);
		echo "Date updated successfully";
	}

	function updateCurrentInventory(){

		$branch_id = Input::get('branch_id');
		$item_id = Input::get('item_id');
		$qty = Input::get('qty');

		$rack = new Rack();
		$cur_rack = $rack->getRackForSelling($branch_id);
		$user = new User();
		$rack_id = $cur_rack->id;
		$inventory = new Inventory();
		if($inventory->checkIfItemExist($item_id,$branch_id,$user->data()->company_id,$rack_id)){
			$curinventory = $inventory->getQty($item_id,$branch_id,$rack_id);
			$inventory->updateInventory($rack_id,$branch_id,$item_id,$qty);
			// monitoring
			$inv_mon = new Inventory_monitoring();
			$newqty =  $qty;
			$inv_mon->create(array(
				'item_id' => $item_id,
				'rack_id' => $rack_id,
				'branch_id' => $branch_id,
				'page' => 'admin/addinventory',
				'action' => 'Update',
				'prev_qty' => $curinventory->qty,
				'qty_di' => 3,
				'qty' => $qty,
				'new_qty' => $newqty,
				'created' => time(),
				'user_id' => $user->data()->id,
				'remarks' => 'Update current inventory',
				'is_active' => 1,
				'company_id' => $user->data()->company_id
			));
		} else {

			$curinventory =0;
			$inventory->addInventory($item_id,$branch_id,$qty,true,$rack_id);
			// monitoring
			$inv_mon = new Inventory_monitoring();
			$newqty = $curinventory + $qty;

			$inv_mon->create(array(
				'item_id' => $item_id,
				'rack_id' => $rack_id,
				'branch_id' => $branch_id,
				'page' => 'admin/addinventory',
				'action' => 'Insert',
				'prev_qty' => $curinventory,
				'qty_di' => 3,
				'qty' => $qty,
				'new_qty' => $newqty,
				'created' => time(),
				'user_id' => $user->data()->id,
				'remarks' => 'Update current inventory',
				'is_active' => 1,
				'company_id' => $user->data()->company_id
			));

		}

		echo "Updated successfully.";

	}