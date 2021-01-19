<?php
	include 'ajax_connection.php';
	$iamuser = new User();
	$toReq = json_decode(Input::get('toOrder'));
	$branch = Input::get('branch');
	$witness = Input::get('witness');
	$remarks = Input::get('remarks');
	$company_id = Input::get('company_id');
	$req = new Agent_request();

	$req->create(array(
		'user_id' =>$iamuser->data()->id,
		'branch_id' => $branch,
		'witness' => $witness,
		'remarks' => $remarks,
		'company_id' => $company_id,
		'status' => 1,
		'is_active' => 1,
		'created' => strtotime(date('Y/m/d H:i:s')),
		'modified' => strtotime(date('Y/m/d H:i:s')),
	));
	$lastid = $req->getInsertedId();
	$req_mon = new Request_monitoring();
	$req_mon->create(array(
			'agent_request_id' => $lastid,
			'status' => 1,
			'user_id' =>$iamuser->data()->id,
			'date_approved' => strtotime(date('Y/m/d H:i:s')),
			'is_active' => 1,
			'company_id' => $company_id,
			'remarks' => 'Requested By'
	));

	foreach($toReq as $o) {
		if($lastid) {
			$od = new Agent_request_details();
			$od->create(array(
				'item_id' =>$o->item_id,
				'qty' => $o->qty,
				'request_id' => $lastid,
				'is_active' => 1,
				'created' => strtotime(date('Y/m/d H:i:s')),
				'modified' => strtotime(date('Y/m/d H:i:s')),
			));
		}
	}
	echo "Request was successfully placed.";