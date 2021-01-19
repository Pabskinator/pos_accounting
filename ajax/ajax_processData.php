<?php
	include 'ajax_connection.php';
	$method= Input::get('method');


		$curData = new Data();
		$user = new User();
		$mon_id = Input::get('mon_id');
		$process_id = Input::get('process');
		$remarks  = Input::get('remarks');
		$step_id  = Input::get('step_id');
		$is_final  = Input::get('is_final');

		$user_app = new User_approval();
		
		$now = time();
		// insert user approval
		$user_app->create(array(
			'status' => 1,
			'monitoring_id' => $mon_id,
			'user_id' => $user->data()->id,
			'created' => $now,
			'modified' => $now,
			'is_active' => 1,
			'company_id' => $user->data()->company_id,
			'step_id' => $step_id,
			'remarks' => $remarks
		));
		

	if($method == 1){ // approved
		$data_items = $curData->processData($mon_id,$is_final);
	} else if($method == 2){ // decline
	$data_items = $curData->declineData($mon_id);
	}

