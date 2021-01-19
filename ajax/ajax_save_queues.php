<?php
	include 'ajax_connection.php';
	$company_id = Input::get('company_id');
	$branch_id = Input::get('bid');
	$jsonq = Input::get('queues');
	$toSave = json_decode($jsonq,true);

	foreach($toSave as $t){
		$newQueue = new Queu();
		$new =$newQueue->insertQeueList($t['qId'],$t['startQueue'],$t['endQueue'],$company_id,$branch_id);
	}
	echo 1;
?>