
<?php
	include 'ajax_connection.php';


	$class = Input::get("class");
	$id = Input::get("id");
	$status = Input::get("status");
	$tochange = new $class();
	$now = time();
	$tochange->update(array(
		'modified' => $now,
		'status' => $status
	),$id);

	if($class == 'Agent_request' &&  $status == 6){
		$cur = new Agent_request($id);
		$iamuser = new User();
		$req_mon = new Request_monitoring();
		$req_mon->create(array(
			'agent_request_id' => $cur->data()->id,
			'status' => 6,
			'user_id' =>$iamuser->data()->id,
			'date_approved' => strtotime(date('Y/m/d H:i:s')),
			'is_active' => 1,
			'company_id' => $iamuser->data()->company_id,
			'remarks' => 'Approved By'
		));
	}

	echo  1;

