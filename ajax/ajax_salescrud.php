<?php
	include 'ajax_connection.php';
	$functionName = Input::get('functionName');

	$functionName();

	function saveSales(){

		$payment_id = (int) Encryption::encrypt_decrypt('decrypt', Input::get('payment_id'));
		$invoice = (int) Input::get('invoice');
		$dr = (int) Input::get('dr');
		$dt = Input::get('dt');
		$ir = Input::get('ir');
		$sv = Input::get('sv');
		$main_sales_type= Input::get('main_sales_type');
		$from_service = Input::get('from_service');
		$addtl_remarks = Input::get('addtl_remarks');

		$sv = ($sv) ? $sv : '';
		$ed = new Sales();
		$wh = new Wh_order();

		if($payment_id){

			$ed->saveEditedSales($payment_id,$invoice,$dr,$dt,$ir,$sv,$from_service,$main_sales_type);

			$wh->updateTransactionDetails($payment_id,$invoice,$dr,$dt,$ir,$sv,$from_service);

			$payment = new Payment();
			$payment->update(
				['addtl_remarks' => $addtl_remarks],$payment_id
			);

		}

		echo "Edited Successfully";
	}

?>