<?php
	include 'ajax_connection.php';
	$functionName = Input::get("functionName");
	$functionName();

	function branchSales(){
		$type = Input::get('type');
		$page = Input::get('page');
		$report = new TopManagement();
		if($type == 1){ // month
			$dt1 = date('m/01/Y');
			if($page){
				$dt1 = strtotime($dt1 . $page . " month");

			} else {
				$dt1 = strtotime($dt1);

			}
			$dt2 = strtotime(date('m/d/Y',$dt1)."1 month -1 min");
			$dt_name = date('M Y',$dt1);

		} else if ($type == 2){ // year
			$dt1 = date('01/01/Y');
			if($page){
				$dt1 = strtotime($dt1 . $page . " year");

			} else {
				$dt1 = strtotime($dt1);

			}
			$dt2 = strtotime(date('m/d/Y',$dt1)."1 year -1 min");
			$dt_name = date('Y',$dt1);
		} else {
			die("Error");
		}

		$branches = $report->getSalesByBranch($dt1,$dt2);

		$arr = [];
		$total_all = 0;
		if($branches){

			foreach($branches as $br){
				$total_all += $br->saletotal;
				$br->branch_name = ($br->branch_name) ? $br->branch_name : 'N/A';
				$arr[] = ['branch_name' => $br->branch_name,'total' => number_format($br->saletotal,2)];
			}

		}

		echo json_encode(['list' => $arr, 'date_name' => $dt_name,'total' => number_format($total_all,2)]);

	}

	function salesTypes(){
		$type = Input::get('type');
		$page = Input::get('page');

		if($type == 1){ // month
			$dt1 = date('m/01/Y');
			if($page){
				$dt1 = strtotime($dt1 . $page . " month");

			} else {
				$dt1 = strtotime($dt1);

			}
			$dt2 = strtotime(date('m/d/Y',$dt1)."1 month -1 min");
			$dt_name = date('M Y',$dt1);

		} else if ($type == 2){ // year
			$dt1 = date('01/01/Y');
			if($page){
				$dt1 = strtotime($dt1 . $page . " year");

			} else {
				$dt1 = strtotime($dt1);

			}
			$dt2 = strtotime(date('m/d/Y',$dt1)."1 year -1 min");
			$dt_name = date('Y',$dt1);
		} else {
			die("Error");
		}
		$report = new TopManagement();
		$salesTypes = $report->getSalesBySalesType($dt1,$dt2);
		$arr = [];
		$total_all = 0;
		if($salesTypes){
			foreach($salesTypes as $br){
				$total_all += $br->saletotal;
				$br->sales_type_name = ($br->sales_type_name) ? $br->sales_type_name : 'N/A';
				$arr[] = ['sales_type' => $br->sales_type_name,'total' => number_format($br->saletotal,2)];
			}
		}

		echo json_encode(['list' => $arr,'date_name'=> $dt_name,'total' => number_format($total_all,2)]);

	}

	function getSamePeriodPercentage(){
		$report = new TopManagement();
		$dt1 = date('01/01/Y');
		$dt2 = date('m/d/Y');

		$dt1_prev = strtotime($dt1 . "-1 year");
		$dt2_prev = strtotime($dt2 . "-1 year 1 day -1 min");

		$dt1 = strtotime($dt1);
		$dt2 = strtotime($dt2 ."1 day -1 min");

		$result1 = $report->getTotal($dt1,$dt2);
		$result2 = $report->getTotal($dt1_prev,$dt2_prev);

		$total1 = 0;
		$total2= 0;
		if(isset($result1->saletotal) && $result1->saletotal){
			$total1=$result1->saletotal;
		}
		if(isset($result2->saletotal) && $result2->saletotal){
			$total2=$result2->saletotal;
		}
		$lbl = "100";
		if($total1 && $total2){
			$i = ($total2 / $total1) * 100;
			$lbl = 100 - $i;
			$lbl = number_format($lbl,2);
			$lbl = (100 - $i > 0) ? "+".$lbl : $lbl;
		}

		echo json_encode([
			'lbl' => $lbl,
			'total_current' => number_format($total1,2),
			'period1' => date('M Y',$dt1) . " - " .  date('M Y',$dt2),
			'period2' => date('M Y',$dt1_prev) . " - " .  date('M Y',$dt2_prev),
			'total_prev' => number_format($total2,2)
		]);

	}

	function stockValue(){
		$tm = new TopManagement();
		$stocks = $tm->stockValue();

		$total = 0 ;
		$arr=  [];
		if($stocks){
			foreach($stocks as $st){
				$total +=  $st->total_amount;
				$arr[] = ['branch_name' => $st->branch_name,'total_amount' => number_format($st->total_amount,2)];
			}
		}
		echo json_encode(['list'=> $arr,'total'=> number_format($total,2)]);
	}

	function getPendingOrder(){

		$tm = new TopManagement();
		// base on branch
		$list = $tm->totalPendingRequest();

		$arr = [];
		$total = 0;
		if($list) {

			foreach($list as $bb) {
				$total += $bb->total;
				$arr[] = ['branch_name' => $bb->branch_name,'total' => $bb->total];
			}
		}
		echo json_encode(['list' => $arr,'total' => $total]);
	}

	function getReceivables(){

		$tm = new TopManagement();
		// base on branch
		$list = $tm->totalCredit();

		$arr = getCollection();

		$total_credit = 0;

		if(isset($list->total_amount) && $list->total_amount) {
			$total_credit = $list->total_amount;
		}

		echo json_encode(
				[
					'total_credit' => number_format($total_credit,2),
					'collections' => $arr['list'],
					'total_collected' => $arr['total'],
					'date_name' => $arr['date_name'],
				]
		);
	}


	function getCollection(){
		$tm = new TopManagement();


		$arr = [];
		$total = 0;

		$page = Input::get('page');
		$dt1 = Input::get('dt');

		if(!$dt1){
			$dt1 = date('m/d/Y');
		}

		if($page){
			$dt1 = strtotime($dt1 . $page . " day");
		} else {
			$dt1 = strtotime($dt1);
		}

		$dt2 = strtotime(date('m/d/Y',$dt1)."1 day -1 min");
		$dt_name = date('m/d/Y',$dt1);

		$list = $tm->getCollection($dt1,$dt2);

		if($list){
			foreach($list as $l){
				$total += $l->pamount;

				if( !$l->cr_number ) continue;

				$arr[] = [
					'cr_number' => $l->cr_number,
					'total' => $l->pamount,
					'date' => date('m/d/Y',$l->created)
				];
			}
		}
		return ['list' => $arr, 'total' => $total,'date_name' => $dt_name];
	}

	function collections(){

		$tm = new TopManagement();

		$dt_from = Input::get('dt_from');
		$dt_to = Input::get('dt_to');
		$num_from = Input::get('num_from');
		$num_to = Input::get('num_to');
		$agent_id = Input::get('agent_id');
		$sort_type = Input::get('sort_type');
		$branch_id = Input::get('branch_id');
		if($dt_from && $dt_to){

		} else {
			if($num_to && $num_from){
				$dt_from = 0;
				$dt_to = 0;
			} else {
				$dt_from = date('m/01/Y');
				$dt_to = date('m/d/Y');
			}

		}
		$lbl_dt1= $dt_from;
		$lbl_dt2= $dt_to;
		if(!(is_numeric($num_from) && is_numeric($num_to) && $num_from <= $num_from)){
			$num_from = 0;
			$num_to = 0;
		}

		$payments = $tm->getAllCr($dt_from,$dt_to,$num_from,$num_to,$agent_id,$sort_type,$branch_id);

		$total_receipt_amount =0;
		$total_deduction =0;
		$total_paid_amount =0;
		$arr = [];

		if($payments){

			$arr_display = [];
			foreach($payments as $p){
				$p->cr_number = trim($p->cr_number);
				$paid_amount = ($p->paid_amount) ? $p->paid_amount :0;
				$deduction = ($p->deduction) ? $p->deduction :0;
				$receipt_amount = ($p->receipt_amount) ? $p->receipt_amount :0;
				?>

				<?php

				$explode = [];

				if(strpos($p->cr_number,",") > 0){

					$explode = explode(",",$p->cr_number);

					foreach($explode as $ex){

						$collection_report = new Collection_report();
						$crdata = $collection_report->getData($ex);

						if(in_array($ex,$arr_display)){
							continue;
						}
						$arr_display[] = $ex;

						$dt_from = 0;
						$dt_to = 0;
						$paid_by = 0;
						$cashier_id = 0;
						$paid_method ="";
						$paid_by_name = "";
						$cashier_name="";
						$include_dr= "";
						$include_ir= "";
						$from_service= 0;

						if(isset($crdata->created) && $crdata->created){
							$dt_from = date('m/d/Y',$crdata->created);
						}
						if(isset($crdata->to_date) && $crdata->to_date){
							$dt_to = date('m/d/Y',$crdata->to_date);
						}

						if(isset($crdata->receive_by) && $crdata->receive_by){
							$paid_by = $crdata->receive_by;
							$paiduser = new User($crdata->receive_by);
							if(isset($paiduser->data()->firstname) && isset($paiduser->data()->lastname)){
								$paid_by_name = $paiduser->data()->firstname . " " . $paiduser->data()->lastname;
							}
						}
						if(isset($crdata->cashier_id) && $crdata->cashier_id){
							$cashier_id = $crdata->cashier_id;
							$cashieruser = new User($crdata->cashier_id);
							if(isset($cashieruser->data()->firstname) && isset($cashieruser->data()->lastname)){
								$cashier_name = $cashieruser->data()->firstname . " " . $cashieruser->data()->lastname;
							}
						}
						if(isset($crdata->remarks) && $crdata->remarks){
							$paid_method = $crdata->remarks;
						}

						if(isset($crdata->include_ov) && $crdata->include_ov){
							$include_dr = $crdata->include_ov;
						}
						if(isset($crdata->include_dv) && $crdata->include_dv){
							$include_ir = $crdata->include_dv;
						}
						if(isset($crdata->is_service) && $crdata->is_service){
							$from_service = $crdata->is_service;
						}

						if($dt_from && !$dt_to){
							$dt_to = $dt_from;
						}

						$cr_payment = new Payment();
						$current_log = $cr_payment->getCRSum($ex);

						if(isset($current_log->receipt_amount)){
							$receipt_amount = $current_log->receipt_amount;
							$deduction = $current_log->deduction;
							$paid_amount = $current_log->paid_amount;
							if($current_log->override_cr_date){
								$p->cr_date =  $current_log->override_cr_date;
							}


						}

					$arr[] = [
						'cr_number' => $ex,
						'date' => date('F d, Y',$p->cr_date),
						'receipt_amount' => number_format($receipt_amount,2),
						'deduction' => number_format($deduction,2),
						'paid_amount' => number_format($paid_amount,2),
					];
						$total_receipt_amount += $receipt_amount;
						$total_deduction += $deduction;
						$total_paid_amount += $paid_amount;

					} // end loop ex

				} else {

					if(in_array($p->cr_number,$arr_display)){
						continue;
					}
					$arr_display[] = $p->cr_number;
					$collection_report = new Collection_report();
					$crdata = $collection_report->getData($p->cr_number);
					$dt_from = 0;
					$dt_to = 0;
					$paid_by = 0;
					$cashier_id = 0;
					$paid_method ="";
					$cashier_name = "";
					$paid_by_name = "";

					$include_dr= "";
					$include_ir= "";
					$from_service= 0;

					if(isset($crdata->created) && $crdata->created){
						$dt_from = date('m/d/Y',$crdata->created);
					}
					if(isset($crdata->to_date) && $crdata->to_date){
						$dt_to = date('m/d/Y',$crdata->to_date);
					}
					if(isset($crdata->receive_by) && $crdata->receive_by){
						$paid_by = $crdata->receive_by;
						$paiduser = new User($crdata->receive_by);
						if(isset($paiduser->data()->firstname) && isset($paiduser->data()->lastname)){
							$paid_by_name = $paiduser->data()->firstname . " " . $paiduser->data()->lastname;
						}
					}
					if(isset($crdata->cashier_id) && $crdata->cashier_id){
						$cashier_id = $crdata->cashier_id;
						$cashieruser = new User($crdata->cashier_id);
						if(isset($cashieruser->data()->firstname) && isset($cashieruser->data()->lastname)){
							$cashier_name = $cashieruser->data()->firstname . " " . $cashieruser->data()->lastname;
						}
					}
					if(isset($crdata->remarks) && $crdata->remarks){
						$paid_method = $crdata->remarks;
					}
					if(isset($crdata->include_ov) && $crdata->include_ov){
						$include_dr = $crdata->include_ov;
					}
					if(isset($crdata->include_dv) && $crdata->include_dv){
						$include_ir = $crdata->include_dv;
					}
					if(isset($crdata->is_service) && $crdata->is_service){
						$from_service = $crdata->is_service;
					}

					if($dt_from && !$dt_to){
						$dt_to = $dt_from;
					}

					$arr[] = [
						'cr_number' => $p->cr_number,
						'date' => date('F d, Y',$p->cr_date),
						'receipt_amount' => number_format($receipt_amount,2),
						'deduction' => number_format($deduction,2),
						'paid_amount' => number_format($paid_amount,2),
					];
					$total_receipt_amount += $receipt_amount;
					$total_deduction += $deduction;
					$total_paid_amount += $paid_amount;
				}
			}
		}
		echo json_encode([
			'date_from' => $lbl_dt1,
			'date_to' => $lbl_dt2,
			'total_receipt_amount' => number_format($total_receipt_amount,2) ,
			'total_deduction' =>  number_format($total_deduction,2) ,
			'total_paid_amount' =>number_format( $total_paid_amount,2) ,
			'list' => $arr
		]);
	}


	function getDetails(){

		$cr_number = Input::get('cr_number');

		$tm = new TopManagement();

		$list = $tm->getCollectionReport($cr_number);

		$arr = [];
		$total_receipt = 0;
		$total_deduction = 0;
		$total_paidamount = 0;

		if($list){
			foreach($list as $l){
				$total_receipt += $l->receipt_amount;
				$total_deduction += $l->deduction;
				$total_paidamount += $l->paid_amount;
				$arr[] = $l;
			}
		}

		echo json_encode(
			[
				'list' => $arr,
				'total_receipt' => number_format($total_receipt,2),
				'total_deduction' => number_format($total_deduction,2),
				'total_paidamount' => number_format($total_paidamount,2),

			]

		);


	}

	function agingSummary(){


		$tm = new TopManagement();


		$total_30 = 0;
		$total_31_60 = 0;
		$total_61_90 = 0;
		$total_91_120 = 0;
		$total_121_above = 0;
		$cur = date('m/d/Y');

		$dt_1_30 = date('m/d/Y',strtotime($cur . "-30 days"));
		$dt_31_60 = date('m/d/Y',strtotime($cur . "-60 days"));
		$dt_61_90 = date('m/d/Y',strtotime($cur . "-90 days"));
		$dt_91_120 = date('m/d/Y',strtotime($cur . "-120 days"));


		$dt_121_above_from = strtotime($dt_91_120 . "-1 min");
		$dt_121_above_to = 0;

		$dt_91_120_from = strtotime($dt_61_90 . "-1 min");
		$dt_91_120_to = strtotime($dt_91_120);

		$dt_61_90_from = strtotime($dt_31_60 . "-1 min");
		$dt_61_90_to = strtotime($dt_61_90);

		$dt_31_60_from = strtotime($dt_1_30 . "-1 min");
		$dt_31_60_to = strtotime($dt_31_60);

		$dt_1_30_from = strtotime($cur);
		$dt_1_30_to = strtotime($dt_1_30);


		$result_1_30 = $tm->totalCreditByTime($dt_1_30_from,$dt_1_30_to);
		$result_31_60 = $tm->totalCreditByTime($dt_31_60_from,$dt_31_60_to);
		$result_61_90 = $tm->totalCreditByTime($dt_61_90_from,$dt_61_90_to);
		$result_91_120 = $tm->totalCreditByTime($dt_91_120_from,$dt_91_120_to);
		$result_121_above = $tm->totalCreditByTime($dt_121_above_from,$dt_121_above_to);


		if(isset($result_1_30->total_amount)){
			$total_30 =$result_1_30->total_amount;
		}

		if(isset($result_31_60->total_amount)){
			$total_31_60 =$result_31_60->total_amount;
		}

		if(isset($result_61_90->total_amount)){
			$total_61_90 =$result_61_90->total_amount;
		}

		if(isset($result_91_120->total_amount)){
			$total_91_120 =$result_91_120->total_amount;
		}

		if(isset($result_121_above->total_amount)){
			$total_121_above =$result_121_above->total_amount;
		}

		$total = $total_30 + $total_31_60 + $total_61_90+ $total_91_120+ $total_121_above;

		echo json_encode(
			[
				'below_30' => number_format(($total_30),2),
				'from_31_60' => number_format(($total_31_60),2),
				'from_61_90' => number_format(($total_61_90),2),
				'from_91_120' => number_format(($total_91_120),2),
				'above_121' => number_format(($total_121_above),2),
				'total' => number_format($total,2),
			]
		);



	}