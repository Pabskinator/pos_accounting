<?php
	include 'ajax_connection.php';
	$functionName = Input::get("functionName");
	$functionName();



	function getTimelog(){
		$id = Encryption::encrypt_decrypt('decrypt', Input::get('id'));

		$timelog = new Timelog();
		if($id && is_numeric($id)){
			$list = $timelog->getTimelogTechnician($id);
			if($list){
				echo "<table class='table table-condensed table-bordered'>";
				echo "<tr><th>Time In</th><th>Time Out</th><th>Remarks</th></tr>";
				foreach($list as $l){
					echo "<tr><td style='border-top:1px solid #ccc;'>" . date('m/d/Y H:i:s A',$l->time_in) . "</td><td style='border-top:1px solid #ccc;'>" . date('m/d/Y H:i:s A',$l->time_out) . "</td><td style='border-top:1px solid #ccc;'>$l->remarks</td></tr>";
				}
				echo "</table>";
			} else {
				echo "<div class='alert alert-info'>No record</div>";
			}
		} else {
			echo "<div class='alert alert-danger'>Invalid request</div>";
		}

	}

	function addTimelog(){
		$id = Encryption::encrypt_decrypt('decrypt', Input::get('id'));
		$remarks = trim(Input::get('remarks'));
		$fullname = trim(Input::get('fullname'));
		$time_in_date = trim(Input::get('time_in_date'));
		$time_in_hour = trim(Input::get('time_in_hour'));
		$time_out_date = trim(Input::get('time_out_date'));
		$time_out_hour = trim(Input::get('time_out_hour'));

		$time_in = $time_in_date . " " . $time_in_hour;
		$time_out = $time_out_date . " " . $time_out_hour;

		$timelog = new Timelog();
		$user = new User();
		$timelog->create(
			[
				'time_in' => strtotime($time_in),
				'time_out' => strtotime($time_out),
				'remarks' => $remarks,
				'user_id' => $user->data()->id,
				'ref_id' => $id,
				'created' => time(),
				'fullname' => $fullname
			]
		);
		echo "Added successfully.";

	}