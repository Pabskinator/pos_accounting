<?php


	error_reporting(0);

	ini_set('session.gc_maxlifetime', 7200);
	// each client should remember their session id for EXACTLY 1 hour
	session_set_cookie_params(7200);

	session_start(); // ready to go!

	include '../core/connection.php';

	spl_autoload_register(function($class){
		require_once '../classes/' . $class . '.php';
	});

	require_once '../functions/sanitize.php';

	include_once '../admin/includes/labels.php';


	$func = Input::get('functionName');
	if(function_exists($func)){
		$func();
	}


	function addSales () {
		$sales = new Sales();
		$user = new User();
		$items = Input::get('items');
		if($user->data()->id){

			$items = json_decode($items);
			// get branch
			foreach($items as $item){
				// enter sales

			}

		}
	}

	function getSales(){

		$sales = new Sales();
		$user = new User();

		$dt_from = Input::get('date_from');
		$dt_to = Input::get('date_to');

		if($dt_from && $dt_to){
			$dt_from = strtotime($dt_from);
			$dt_to = strtotime($dt_to . "1 day -1min");

		} else {
			$dt_from = strtotime(date('F Y') . "-3 months");
			$dt_to = strtotime(date('F Y') . "1 month -1min");

		}
		$data = $sales->getSalesByDicer($user->data()->company_id,$dt_from,$dt_to);
		$arr = [];
		if($data){

			foreach($data as $item){
				$arr[] = [
					'invoice' => $item->invoice,
					'dr' => $item->dr,
					'payment_id' => $item->payment_id,
					'ir' => $item->ir,
					'sold_date' => date('m/d/Y',$item->sold_date),
					'total' => number_format($item->totalamount,2),
				];
			}
		}
		echo json_encode($arr);

	}

	function getDetails(){

		$id = Input::get('payment_id');
		$sales = new Sales();
		$arr = [];

		if($id){
			$list = $sales->salesTransactionBaseOnPaymentId($id);
			if($list){

				foreach($list as $l){
					$arr[] = ['qty' => $l->qtys,'item_code' => $l->item_code];
				}
			}
		}

		echo json_encode($arr);
	}
