<?php
	include 'ajax_connection.php';
	$target_path = "../uploads/";
	$ext = explode('.', basename($_FILES['file']['name']));
	$file_extension = end($ext);
	$ref_table = "wallet";
	$filename = $ref_table ."-" .uniqid(). ".".$ext[count($ext) - 1];
	$path = $target_path .$filename ;
	$file = $_FILES['file'];
	$amount = Input::get('amount');
	$remarks = Input::get('remarks');
	$ref_no = Input::get('ref_no');
	$payment_method = Input::get('payment_method');

	if (move_uploaded_file($_FILES['file']['tmp_name'], $path)) {

			if($amount && is_numeric($amount)){
				$wallet = new Wallet_request();
				$user = new User();
				$wallet->create(
					array(
						'user_id' => $user->data()->id,
						'remarks' => $remarks,
						'amount' => $amount,
						'company_id' => $user->data()->company_id,
						'is_active' => 1,
						'status' => 1,
						'ref_no' => $ref_no,
						'payment_method' => $payment_method,
						'file_name' => $filename,
						'created' => time()
					)
				);
				$arr = ['success' => true,'message'=>"Request was sent successfully."];
			} else {
				$arr = ['success' => false,'message'=>"Invalid amount"];
			}
		echo json_encode($arr);
	} else {
		echo "Failed";
	}

