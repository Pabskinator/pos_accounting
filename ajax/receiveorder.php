<?php
	include 'ajax_connection.php';
	$oid = Input::get('oid');
	$name= Input::get('name');
	$updateorder = new Reorder_item($oid);
	$user = new User();
	if($updateorder->data()->status == 2){

	if($updateorder->receiveorder($oid)){
			$updateorder->update(array(
				'status'=>3,
				'remarks'=>$name
			),$oid);
			$reorder_monitoring = new Reorder_monitoring();
			$reorder_monitoring->create(array(
				'reorder_id' => $oid,
				'status' => 2,
				'user_id' =>$user->data()->id,
				'date_processed' => strtotime(date('Y/m/d H:i:s')),
				'is_active' => 1,
				'company_id' => $user->data()->company_id,
				'remarks' => 'Transferred By'
			));
			echo "Order Received";
		} else {
			echo "Not enough stock to transfer.";
		}
	} else {
		echo "This order is already processed by someone else. Please check the log for more information.";
	}

?>