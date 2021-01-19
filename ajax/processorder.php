<?php
	include 'ajax_connection.php';
	$oid = Input::get('oid');
	$name= Input::get('name');
	$user = new User();
	$updateorder = new Reorder_item($oid);
	if($updateorder->data()->status == 1){
		$updateorder->update(array(
			'status'=>2,
			'remarks'=>$name
		),$oid);
		$reorder_monitoring = new Reorder_monitoring();
		$reorder_monitoring->create(array(
			'reorder_id' => $oid,
			'status' => 1,
			'user_id' =>$user->data()->id,
			'date_processed' => strtotime(date('Y/m/d H:i:s')),
			'is_active' => 1,
			'company_id' => $user->data()->company_id,
			'remarks' => 'Processed By'
		));
		echo "Order's Status Updated";
	} else {
		echo "This order is already processed by someone else. Please check the log for more information.";
	}

?>