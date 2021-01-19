<?php
	include 'ajax_connection.php';
	 $functionName = Input::get("functionName");

	if(function_exists($functionName)){
		$functionName();
	}
	function processesFeedback(){
		$id = Input::get('id');
		if($id && is_numeric($id)){
			$feedback = new Feedback($id);
			$status = $feedback->data()->status;
			$processed_date = 0;
			$completed_date = 0;
			if($status == 0){
				$processed_date = time();
			} else if($status == 1){
				$completed_date = time();
			}

			$newStat =  $status + 1;
			$feedback->update(
				array(
					'status' => $newStat,
					'processed_date' => $processed_date,
					'completed_date' => $completed_date
				) , $id
			);
		}
	}

	function  getMobileTerminal(){
		$smscls = new Sms_gateway();
		$company_id = 1;
		$mobilelist = $smscls->get_active('sms_gateway',['company_id','=',$company_id]);
		$numbers = [];
		foreach($mobilelist as $m){
			$numbers[] = ['number' => $m->mobile_number, 'name' => $m->name, 'terminal_ids' => $m->terminal_id,'url' =>'pw.apollosystems.com.ph'];
		}
		$arr['numbers'] = $numbers;
		echo json_encode($arr);
	}

	function  getItems(){
		$smscls = new Sms_gateway();
		$company_id = 1;
		$arr=[];
		$number = Input::get('number');

		$sms_gateway = new Sms_gateway();
		$first_digit = substr($number,0,2);
		if($first_digit == '63'){
			$number = "0" . substr($number,2);
		} else {

		}

		//$number = '09995013851';

		$data_number = $sms_gateway->getDataByNumber($number);
		/*
		 if(!$data_number){
			$number = '09330796655';
		}
		$data_number = $sms_gateway->getDataByNumber($number);
		*/
		$terminal_id = $data_number->terminal_id;
		$arr_terminal = [];
		$member_id = 0;
		if(strpos($terminal_id,",") > 0){
			$ex_terminal = explode(",",$terminal_id);

			foreach($ex_terminal as $ex){
				if(is_numeric($ex)){
					$terminal_data = new Terminal($ex);
					$arr_terminal[] = ['terminal_id' => $terminal_data->data()->id, 'terminal_name' => $terminal_data->data()->name];
					$branch_id = $terminal_data->data()->branch_id;
				}

			}
			if($branch_id){
				$branch_data = new Branch($branch_id);
				$member_id = $branch_data->data()->member_id;
				$is_multiple = '1';
			}

		} else {
			$results = $sms_gateway->getBranchByNumber($number);
			$member_id = $results->member_id;
			$is_multiple = '0';
		}


		if($member_id){
			$whereF= "1,2";
		} else {
			$whereF = "0,2";
		}
		$itemlist = $smscls->getItems($company_id,$whereF);
		$items = [];
		$configs = [];
		$bb = new Branch();
		$bb_list = $bb->branchJSON($company_id,'');
		$bb_arr = [];
		if($bb_list){
			foreach($bb_list as $b){
				$bb_arr[] = $b;
			}
		}

		if($itemlist){
			foreach($itemlist as $m){
				$items[] = ["for_selling" => $m->for_selling, "item_id"=>$m->id,"item_name" =>$m->item_code, "description" => $m->description];
			}
		}
		$configs[] = ['member_id' => $member_id,'is_multiple' => $is_multiple,'terminal_json' => ['terminals' => $arr_terminal], 'app_version' => '1', 'app_download_link' => 'http://pw.apollosystems.com.ph/service/apollo-sms.apk','app_number_receiver' => '09323800536'];
		$arr['items'] = $items;
		$arr['branches'] = $bb_arr;
		$arr['configs'] = $configs;
		echo json_encode($arr);
	}

	function  saveEndingInventory($data,$branch_id=0){

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
			$details = $wh_order->getBranchOrderScheduleToday($branch_id);
			$arr_order = [];
			if($details){
				foreach($details as $det){
					$arr_order[$det->item_id]= $arr_order->qty;
				}
			}
			$to_add_in_sales = [];
			foreach($arr as $a){
				$pair = explode(" ",$a);
				if(!is_numeric($pair[0])){
					$pair[0] = strtoupper($pair[0]);
					if(isset($arr_codes[$pair[0]])){
						$pair[0] = $arr_codes[$pair[0]];
					}
				}

				if(is_numeric($pair[0]) && is_numeric($pair[1])){
					// get inventory
					$cur_inv = isset($arr_inv[$pair[0]]) ? $pair[0] : 0;

					// get order
					$cur_order = isset($arr_order[$pair[0]]) ? $arr_order[0] : 0;

					// get total inv
					$total_inventory = $cur_inv +  $cur_order;

					//get difference to reported inv

					$diff = $total_inventory - $pair[1];
					if($diff > 0){
						// add sales
						$to_add_in_sales[] = ['item_id' => $pair[0], 'qty' => $diff];
					}

					// insert snapshot

					// update inventory


				}
			}
		}
	}

	function saveToServer(){
		$number = Input::get('number');
		$message = trim(Input::get('message'));
		$send_date = Input::get('send_date');
		$unique_id = Input::get('unique_id');
		if(!$unique_id) {
			$unique_id = uniqid();
		}
		$company_id = 1;
		$sms = new Sms_receive();
		$tocheck = substr($message,0,5);

		$is_order = 0;
		$is_expense = 0;
		$is_transfer= 0;
		$transfer_to = 0;
		$is_deposit = 0;
		$is_senior = 0;
		$is_received = 0;
		$dont_insert = 0;
		$is_badorder = 0;
		if(strtoupper($tocheck) == "ORDER"){
			$message = trim(substr($message,5));
			$is_order = 1;
		} else if(strtoupper($tocheck) == "EXPEN"){
			$message = trim(substr($message,7));
			$is_expense = 1;
		} else if(strtoupper($tocheck) == "TRANS"){
			$message = trim(substr($message,8));
			$is_transfer = 1;
			$dont_insert = 1;
		}else if(strtoupper($tocheck) == "RECEI"){
			$message = trim(substr($message,7));
			$is_received = 1;
			$dont_insert = 1;
		}else if(strtoupper($tocheck) == "DEPOS"){
			$message = trim(substr($message,7));
			$is_deposit = 1;
			$dont_insert = 1;
		} else if (strtoupper($tocheck) == "SENIO"){
			 $message = trim(substr($message,6));
			$is_senior = 1;
			$dont_insert = 1;

		} else if (strtoupper($tocheck) == "BADOR"){
			 $message = trim(substr($message,8));
			$is_badorder = 1;
			$dont_insert = 1;

		}
		$multi = substr($message,0,1);
		$terminal_id=0;
		if($multi == "("){
			$last_multi = strpos($message,")");
			$terminal_id = substr($message,1,$last_multi-1);
			 $message = substr($message,$last_multi+1);

			if($is_transfer){
				if(strpos($terminal_id,'-') > 0){
					$explode_inner = explode('-',$terminal_id);
					$terminal_id = $explode_inner[0];
					$transfer_to = $explode_inner[1];
				} else {
					$terminal_id = 0;
					$transfer_to = $terminal_id;
				}
			}

		}


		$checker = $sms->isMesasageExists($company_id,$number,$message,$send_date,$is_order,$is_expense);
		if(isset($checker->num) && $checker->num > 0){
			echo $unique_id;

		} else {
			if(!$terminal_id){
				$smschecker = new Sms_gateway();
				$resultCheck = $smschecker->getBranchByNumber($number);
				if(isset($resultCheck->terminal_id)){
					$terminal_id = $resultCheck->terminal_id;
				}
			}

			$res = $sms->insertSMSData($company_id,$number,$message,$send_date,$unique_id,time(),$is_order,$terminal_id,$is_expense,$dont_insert);
			if($res){
				$sms_gateway = new Sms_gateway();
				if(!$terminal_id){

					$results = $sms_gateway->getBranchByNumber($number);
					$branch_id = $results->branch_id;
					$member_id = $results->member_id;
					$name_dicer = $results->name;

				} else {

					$terminal_cls = new Terminal();
					$results = $terminal_cls->getTerminalData($terminal_id);
					$branch_id = $results->branch_id;
					$member_id = $results->member_id;
					$sms_service = new Sms_gateway();
					$sms_data = $sms_service->getDataByNumber($number);
					$name_dicer = $sms_data->name;

				}

				if($is_order == 1){

					saveOrderForDicer($message,$branch_id,$member_id,$name_dicer);
				} else if($is_expense == 1){

					saveExpense($message,$branch_id,$member_id,$name_dicer,$unique_id);

				} else if($is_transfer == 1){

					saveTransfer($message,$branch_id,$transfer_to);

				} else if($is_deposit == 1){

					saveDeposit($message,$branch_id,$name_dicer,$unique_id);

				} else if($is_senior == 1){

					saveSenior($message,$branch_id);

				} else if($is_received == 1){

					saveReceive($message);

				}else if($is_badorder == 1){

					saveBadOrder($message,$branch_id);

				} else {

					saveEndingInventory($message,$branch_id);

				}

				echo $unique_id;
			} else {
				echo "0";
			}
		}
	}

	function saveBadOrder($message,$branch_id){
		$newRequest = new Bad_order();
		$now = time();
		$total_badorder = 0;
		$newRequest->create(array(
			'branch_id' => $branch_id,
			'supplier_id' => 0,
			'remarks' => '',
			'supplier_order_id' => 0,
			'company_id' => 1,
			'is_active' => 1,
			'status' => 0,
			'created' => $now,
			'from_dicer' => 1,
			'total_bo' => $total_badorder
		));

		$bo_last_id = $newRequest->getInsertedId();
		$i = 1;
		$ctr = 1;
		$data = explode(" ", $message);
		$arr= [];
		foreach($data as $d){
			if(isset($arr[$i])) $arr[$i] .= " " .$d; else $arr[$i] = $d;
			if($ctr % 2 == 0) $i++;
			$ctr++;
		}
		foreach($arr as $a){
			$pair = explode(" ",$a);
			if(!is_numeric($pair[0])){
				$pair[0] = strtoupper($pair[0]);
				if(isset($arr_codes[$pair[0]])){
					$pair[0] = $arr_codes[$pair[0]];
				}
			}

			if(is_numeric($pair[0]) && is_numeric($pair[1])){
				$details = new Bad_order_detail();
				$details->create(array(
					'bad_order_id' => $bo_last_id,
					'item_id' => $pair[0],
					'qty' => $pair[1],
					'rack_id' => 0,
					'remarks' => '',
					'created' => $now,
					'company_id' => 1
				));
			}
		}

	}
	function saveReceive($message){
		$id_order =  (int)$message;
		$whorder = new Wh_order();
		if($id_order){
			$whorder->update(['is_received' => 1,'received_date' => time() ],$id_order);
		}
	}
	function saveSenior($message,$branch_id){
		if($message){

			$senior = new Senior_discount();
			$explode = explode('|',$message);
			$item_list = $explode[0];
			$senior_name = $explode[1];
			$senior_id = $explode[2];
			$total = $explode[3];
			$discount = $explode[4];
			$invoice_number = $explode[5];

			$senior->create(
				array(
					'senior_name' => $senior_name,
					'senior_id' =>$senior_id,
					'senior_total' =>$total,
					'senior_discount' =>$discount,
					'item_list' =>$item_list,
					'created' =>time(),
					'branch_id' =>$branch_id,
					'invoice_number' =>$invoice_number
				)
			);

		}
	}

	function saveDeposit($message,$branch_id,$name_dicer,$unique_id){
		// $message
		// explode with ||
		// [0] = num , [1] = amount
		if($message) {

			$explode = explode("||", $message);
			$amount = (isset($explode[0])) ? $explode[0] : 0;
			$num = (isset($explode[1])) ? $explode[1] : '';
			$dd = new Dicer_deposit();
			$chk_dicer = $dd->checkDeposit($branch_id, date('Y-m-d'), $amount);

			if(isset($chk_dicer->cnt) && $chk_dicer->cnt) {

			} else {
				$dd->create([
					'company_id' => 1,
					'is_active' => 1,
					'created' => time(),
					'branch_id' => $branch_id,
					'ref_id' => $unique_id,
					'amount' => $amount,
					'deposit_slit_number' => $num,
					'deposit_by' => $name_dicer
				]);
			}
		}
	}

	function saveTransfer($message,$branch_id,$transfer_to){
		$data = trim($message);
		$data = explode(' ',$data);
		if(count($data) > 0) {
			$i = 1;
			$ctr = 1;
			$arr = [];
			foreach($data as $d) {
				if(isset($arr[$i])) $arr[$i] .= " " . $d; else $arr[$i] = $d;
				if($ctr % 2 == 0) $i++;
				$ctr++;
			}





			// insert to order
			// insert to transfer
			// insert wh order
			$member_id =  0;
			$order = new Wh_order();
			$now = time();



			$main_branch = $branch_id;
			if(count($arr)){
			//	echo $main_branch . " -- " . $transfer_to;
				$order->create(array(
					'branch_id' => $main_branch,
					'member_id' =>$member_id,
					'to_branch_id' => $transfer_to,
					'remarks' => "Transfer Inventory (FROM SMS)",
					'created' => $now,
					'company_id' =>1,
					'user_id' => 0,
					'is_active' => 1,
					'status' => 4,
					'stock_out' => 1
				));
				$lastItOrder = $order->getInsertedId();

				$transfer = new Transfer_inventory_mon();
				$now = time();
				$transfer->create(array(
					'status' => 1,
					'is_active' =>1,
					'branch_id' =>$transfer_to,
					'company_id' =>1,
					'created' => $now,
					'modified' => $now,
					'from_where' => 'From Order',
					'payment_id' => $lastItOrder
				));
				$lastidTransfer = $transfer->getInsertedId();

			}



			foreach($arr as $a) {
				$pair = explode(" ", $a);
				if(!is_numeric($pair[0])) {
					$pair[0] = strtoupper($pair[0]);
					if(isset($arr_codes[$pair[0]])) {
						$pair[0] = $arr_codes[$pair[0]];
					}
				}

				if(is_numeric($pair[0]) && is_numeric($pair[1])) {
					$item_id = $pair[0];
					$qty = $pair[1];
					$order_details = new Wh_order_details();
					$adjustmentcls = new Item_price_adjustment();
					$itemids[] =$item_id;
					$product = new Product($item_id);
					$price = $product->getPrice($item_id);
					$adjustment = $adjustmentcls->getAdjustment($main_branch,$item_id);
					$alladj =0;

					$branch_discount = new Branch_discount();
					$b_disc = $branch_discount->getDiscount($main_branch,$branch_id);
					$b_disc_amount = 0;
					if(isset($b_disc->discount) && !empty($b_disc->discount)){
						$b_disc_amount = $b_disc->discount / 100;
						$prod = new Product();
						$price = $prod->getPrice($item_id);
						$b_disc_amount = $price->price * $b_disc_amount;
					}
					if($b_disc_amount){
						$b_disc_amount  = ($b_disc_amount * $qty) * -1;
						$alladj += $b_disc_amount;
					}

					if(isset($adjustment->adjustment)){
						$adj_amount = $adjustment->adjustment;
					} else {
						$adj_amount = 0;
					}
					$order_last_id = $lastItOrder;

					$order_details->create(array(
						'wh_orders_id' => $order_last_id,
						'item_id' => $item_id,
						'price_id' => $price->id,
						'qty' => $qty,
						'created' => $now,
						'modified' => $now,
						'price_adjustment' => $adj_amount,
						'company_id' => 1,
						'is_active' => 1,
						'terms' => 0,
						'member_adjustment' => $alladj,
						'original_qty' => $qty
					));

					$transfer_details = new Transfer_inventory_details();
					$transfer_details->create(array(
						'transfer_inventory_id' => $lastidTransfer,
						'rack_id_from' => 0,
						'rack_id_to' => 0,
						'item_id' =>$item_id,
						'qty' =>$qty,
						'is_active' => 1
					));

				}
			}
		}
	}
	function saveExpense($message,$branch_id,$member_id,$name_dicer,$unique_id=''){
		$message = rtrim($message,"::");
		$data = trim($message);
		if(strpos($message,"::") > 0){
			$data = explode("::" , $message);
			foreach($data as $d){
				$ind = explode("|",$d);
				$invoice = (isset($ind[2]) && $ind[2]) ? $ind[2] : '';
				$tin = (isset($ind[3]) && $ind[3]) ? $ind[3] : '';
				if(isset($ind[0]) && $ind[0] && isset($ind[1]) && $ind[1] )
				$arr[] = ['description' => $ind[0], 'amount' => $ind[1],'invoice_number' => $invoice,'tin_number' => $tin];
			}
		} else {
			$ind = explode("|",$data);
			$invoice = (isset($ind[2]) && $ind[2]) ? $ind[2] : '';
			$tin = (isset($ind[3]) && $ind[3]) ? $ind[3] : '';
			if(isset($ind[0]) && $ind[0] && isset($ind[1]) && $ind[1] )
				$arr[] = ['description' => $ind[0], 'amount' => $ind[1],'invoice_number' => $invoice,'tin_number' => $tin];
		}
		$branch_expense = new Branch_expense();
		$now = time();
		$member_id = ($member_id) ? $member_id : 0;
		$name_dicer = ($name_dicer) ? $name_dicer : '';

		if($arr){
			foreach($arr as $a){
				if(trim(strtolower($a['description'])) == 'ro'){
					$id_order =  (int) $a['amount'];
					$whorder = new Wh_order();
					if(is_numeric($id_order)){
						$whorder->update(['is_received' => 1,'received_date' => time() ],$id_order);
					}
				} else if(trim(strtolower($a['description'])) == 'sd'){

						$dd = new Dicer_deposit();
						$chk_dicer = $dd->checkDeposit($branch_id,date('Y-m-d'),$a['amount']);
						if(isset($chk_dicer->cnt) && $chk_dicer->cnt){

						} else {

							$dd->create(
								[
									'company_id' => 1,
									'is_active' => 1,
									'created' => time(),
									'branch_id' => $branch_id,
									'ref_id' => $unique_id,
									'amount' => $a['amount'],
									'deposit_by' => $name_dicer
								]
							);

						}

				} else {

						$branch_expense->create(
							[
								'description' => $a['description'],
								'amount' => $a['amount'],
								'branch_id' => $branch_id,
								'member_id' => $member_id,
								'dicer_name' => $name_dicer,
								'created' => $now,
								'company_id' => 1,
								'is_active' => 1,
								'invoice_number' => 1,
								'tin_number' => 1,
							]
						);

				}

			}
		}
	}

	function saveOrderForDicer($data,$branch_id,$member_id,$name_dicer){
		$data = trim($data);
		$data = explode(" " , $data);
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
		$arr = [];
		if(count($data) > 0){
			$i = 1;
			$ctr = 1;
			$arr_special = Configuration::getSpecialItem();
			$has_special = 0;
			foreach($data as $d){
				if(isset($arr[$i])){
					$arr[$i] .= " " .$d;
				}  else{
					if(in_array($d,$arr_special)) $has_special = 1;
					$arr[$i] = $d;
				}
				if($ctr % 2 == 0) $i++;
				$ctr++;
			}

			// insert wh order
			$member_id =  ($member_id) ? $member_id : 0;
			$order = new Wh_order();
			$now = time();

			if($member_id){
				$for_status = 1;
			} else {
				$for_status = 3;
			}

			$main_branch = Configuration::getMainBranch();
			if((count($arr) > 1 && $has_special == 1) || (count($arr) > 0 && $has_special == 0)){
				$order->create(array(
					'branch_id' => $main_branch,
					'member_id' =>$member_id,
					'to_branch_id' => $branch_id,
					'remarks' => "$name_dicer (FROM SMS)",
					'created' => $now,
					'company_id' =>1,
					'user_id' => 0,
					'is_active' => 1,
					'status' => $for_status,
					'stock_out' => 0
				));
				$lastItOrder = $order->getInsertedId();
			}


			if($has_special == 1){
				$newOrderSpecial = new Wh_order();
				$newOrderSpecial->create(array(
					'branch_id' => $main_branch,
					'member_id' =>$member_id,
					'to_branch_id' => $branch_id,
					'remarks' => "$name_dicer (FROM SMS)",
					'created' => $now,
					'company_id' =>1,
					'user_id' => 0,
					'is_active' => 1,
					'is_scheduled' => strtotime(date('m/d/Y')),
					'received_date' => strtotime(date('m/d/Y')),
					'is_received' => 1,
					'status' => 4,
					'stock_out' => 1
				));
				$lastItOrderSpecial = $newOrderSpecial->getInsertedId();
			}
			$memberTerms = new Member_term();
			if($member_id){
				$memberDetails = new Member($member_id);
			}
			foreach($arr as $a){
				$pair = explode(" ",$a);

				if(!is_numeric($pair[0])){
					$pair[0] = strtoupper($pair[0]);
					if(isset($arr_codes[$pair[0]])){
						$pair[0] = $arr_codes[$pair[0]];
					}
				}

				if(is_numeric($pair[0]) && is_numeric($pair[1])){
					$item_id = $pair[0];
					$qty = $pair[1];
					$order_details = new Wh_order_details();
					$adjustmentcls = new Item_price_adjustment();
					$itemids[] =$item_id;
					$product = new Product($item_id);
					$price = $product->getPrice($item_id);
					$adjustment = $adjustmentcls->getAdjustment($main_branch,$item_id);
					$memadj =$memberTerms->getAdjustment($member_id,$item_id);

					$terms= 0;
					if($member_id){
						$terms = $memberDetails->data()->terms;
					}
					$alladj = 0;
					if(count($memadj)){
						//$same_type = [];
						foreach($memadj as $m){
							$madj = $m->adjustment;
							$terms = $m->terms;
							if($m->type == 1){ // for every
								$x = floor($qty / $m->qty);
								$madj = $madj * $x;
								$alladj += $madj;
							} else if ($m->type == 2){ // above qty
								if($qty >= $m->qty){
									$alladj += $madj;
								}
							}
						}
					}

					$branch_discount = new Branch_discount();
					$b_disc = $branch_discount->getDiscount($main_branch,$branch_id);
					$b_disc_amount = 0;
					if(isset($b_disc->discount) && !empty($b_disc->discount)){
						$b_disc_amount = $b_disc->discount / 100;
						$prod = new Product();
						$price = $prod->getPrice($item_id);
						$b_disc_amount = $price->price * $b_disc_amount;
					}
					if($b_disc_amount){
						$b_disc_amount  = ($b_disc_amount * $qty) * -1;
						$alladj += $b_disc_amount;
					}

					if(isset($adjustment->adjustment)){
						$adj_amount = $adjustment->adjustment;
					} else {
						$adj_amount = 0;
					}
					$order_last_id = $lastItOrder;
					if($has_special == 1 && in_array($item_id,$arr_special)){
						$order_last_id = $lastItOrderSpecial;
					}
					$order_details->create(array(
						'wh_orders_id' => $order_last_id,
						'item_id' => $item_id,
						'price_id' => $price->id,
						'qty' => $qty,
						'created' => $now,
						'modified' => $now,
						'price_adjustment' => $adj_amount,
						'company_id' => 1,
						'is_active' => 1,
						'terms' => $terms,
						'member_adjustment' => $alladj,
						'original_qty' => $qty
					));
				}
			}
			$hasass = $order->hasAssembleItem($itemids);
			if($hasass->cnt){
				$order->update(
					array(
						'has_assemble_item' => 1
					),$lastItOrder);
			}
		}
	}

	function deleteImageProduct(){
		 $id = Input::get('id');
		$id = Encryption::encrypt_decrypt('decrypt',$id);
		if(is_numeric($id)){
			unlink("../item_images/{$id}.jpg");
			echo "Image deleted successfully.";
		}
	}

