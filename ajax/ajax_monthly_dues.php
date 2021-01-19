<?php
	include 'ajax_connection.php';

	$function = Input::get('functionName');

	$function();

	function insertDue(){

		$form = json_decode(Input::get('form'));

		if($form->member_id && $form->per_month && $form->dues ){
			$due = new Monthly_dues();
			$due->create([
				'member_id' => $form->member_id,
				'per_month' => $form->per_month,
				'dues' => $form->dues,
				'remarks' => $form->remarks,
				'created' => time(),
				'status' =>1
			]);

			echo "1";
		} else {
			echo "Please complete the form.";
		}

	}

	function addNewDetail(){
		$cur_due = json_decode(Input::get('cur_due'));
		$data = json_decode(Input::get('data'));

		if($data && $cur_due){
			 $amount = $data->amount;
			 $status = $data->status;
			 $due_date = strtotime($data->due_date);
			 $date_received = strtotime($data->date_received);
			$monthly_due = new Monthly_dues();
			$monthly_due->insertDetails($cur_due->id,$amount,$status,$due_date,$data->payment_type,$data->cheque_number,$data->date_matured,$data->remarks,$date_received,$data->cc_bank);
			echo "Info added successfully.";
		}
	}


	function showDetails(){
		$due = Input::get('due');
		$due = json_decode($due);
		$cls = new Monthly_dues();
		$details = $cls->showDetails($due->id);
		$arr = [];
		if($details){
			foreach($details as $det){
				$det->created = date('m/d/Y H:i:s A',$det->created);
				$det->dt_collected = date('m/d/Y',$det->dt_collected);
				$det->date_received = date('m/d/Y',$det->date_received);
				$arr[] = $det;
			}
		}
		echo json_encode($arr);

	}
	function receiveDue(){
		$due = Input::get('due');
		$cls = new Monthly_dues();
		$cls->receiveDue(json_decode($due));
		echo 1;
	}
	function deleteDue(){
		$due = json_decode(Input::get('due'));
		$cls = new Monthly_dues();
		$cls->deleteDue($due->id);
		echo "Deleted successfully.";

	}

	function deleteDetails(){
		$due = json_decode(Input::get('due'));
		$cls = new Monthly_dues();

		$cls->deleteDetails($due->id);
		echo "Deleted successfully.";
	}

	function getMonthlyDues(){
		$due = new Monthly_dues();
		$status = Input::get('status');
		$date_to = Input::get('date_to');
		$date_from = Input::get('date_from');
		$member_id = Input::get('member_id');
		$profit_center = Input::get('profit_center');

		$list = $due->getRecord($status,$member_id,$date_from,$date_to,$profit_center);
		$arr = [];
		if($list){

			foreach($list as $item){
				$item->created_at = date('m/d/Y',$item->created);

				if($item->covered_period){
					$item->covered_period = $item->covered_period;
				} else {
					$item->covered_period = 'NA';
				}
				
				if($item->period){
					$item->period = $item->period;
				} else {
					$item->period = 'NA';
				}

				$arr[] = $item;
			}

		}
		echo json_encode($arr);
	}

	function downMonthlyDues(){
		$due = new Monthly_dues();
		$status = Input::get('status');
		$date_to = Input::get('date_to');
		$date_from = Input::get('date_from');
		$member_id = Input::get('member_id');
		$profit_center = Input::get('profit_center');
		$list = $due->getRecord($status,$member_id,$date_from,$date_to,$profit_center);


		$filename = "monthlydues-" . date('m-d-Y-h-i-s') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");

		if($list){
			?>
			<table border='1' class='table' id='tblForApproval' v-show="dues.length">
				<thead>
				<tr>
					<th>ID</th>
					<th>Date</th>
					<th>Member</th>
					<th>Profit Center</th>
					<th>Monthly Due</th>
					<th>Total Paid</th>
					<th>Remaining</th>
				</tr>
				</thead>
				<tbody>
			<?php
			foreach($list as $item){
				$item->created_at = date('m/d/Y',$item->created);
				?>
				<tr>
					<td><?php echo $item->id; ?></td>
					<td><?php echo $item->created_at; ?></td>
					<td><?php echo $item->member_name; ?></td>
					<td><?php echo $item->profit_center; ?></td>
					<td><?php echo $item->dues; ?></td>
					<td><?php echo $item->total_paid; ?></td>
					<td><?php echo ($item->dues-$item->total_paid); ?></td>
				</tr>
				<?php
			}
			?>
			</tbody>
			</table>
			<?php

		} else {
			echo "No record found";
		}

	}


	function submitDueDetails(){

		$items = json_decode(Input::get('items'));

		$data = json_decode(Input::get('data'));

		$member_id = $data->member_id;

		$dues = $data->total_amount;

		$profit_center = $data->profit_center;

		$covered_period = $data->covered_period;

		$bank = $data->bank;

		$pr = $data->pr;

		$station_id = $data->station_id;

		$monthly_due = new Monthly_dues();

		$now = time();

		$user = new User();
		$station_id = ($station_id) ? $station_id :0;
		$monthly_due->create(
			[
				'member_id' => $member_id,
				'dues' => $dues,
				'profit_center' => $profit_center,
				'status' => 1,
				'created' => $now,
				'ctrl_num' => $pr,
				'covered_period' => $covered_period,
				'bank' => $bank,
				'station_id' => $station_id,
				'user_id' => $user->data()->id,
			]
		);

		$due_id = $monthly_due->getInsertedId();

		foreach($items as $item){

			$amount = $item->monthly_amount;

			$status = $item->status;

			$due_date = strtotime($item->due_date);

			$date_received = strtotime($item->date_received);

			$date_matured = $item->date_matured;

			$payment_type = $item->payment_type;

			$cheque_number = $item->cheque_number;

			$remarks = $item->remarks;

			$cc_bank = $item->cc_bank;

			$monthly_due->insertDetails($due_id,$amount,$status,$due_date,$payment_type,$cheque_number,$date_matured,$remarks,$date_received,$cc_bank);

		}

	}

	function updateDetails(){

		$items = json_decode(Input::get('items'));
		if(count($items)){

			$monthly_due = new Monthly_dues();

			foreach($items as $item){
				$dt_collected = strtotime($item->dt_collected);
				$monthly_due->updateDetail($item->id,$item->status,$item->remarks,$dt_collected);

			}

			echo "Update successfully.";

		}

	}

	function getMonthlyDuesDetails(){
		$due = new Monthly_dues();
		$date_to = Input::get('date_to');
		$date_from = Input::get('date_from');
		$member_id = Input::get('member_id');
		$profit_center = Input::get('profit_center');
		$payment_type = Input::get('payment_type');

		$list = $due->getRecordDetails($member_id,$date_from,$date_to,$profit_center,$payment_type);
		$arr = [];

		if($list){
			$arr_payment= ['','Cash','Cheque','Credit'];
			foreach($list as $item){
				if($item->dt_collected){
					$item->dt_collected = date('m/d/Y',$item->dt_collected);
				} else {
					$item->dt_collected = 'NA';
				}
				if($item->date_received){
					$item->date_received = date('m/d/Y',$item->date_received);
				} else {
					$item->date_received = 'NA';
				}
				$item->payment_type = $arr_payment[$item->payment_type];
				$arr[] = $item;
			}
		}

		echo json_encode($arr);
	}

	function downMonthlyDuesDetails(){
		$due = new Monthly_dues();
		$date_to = Input::get('date_to');
		$date_from = Input::get('date_from');
		$member_id = Input::get('member_id');
		$profit_center = Input::get('profit_center');
		$payment_type = Input::get('payment_type');
		$profit_center = ($profit_center) ?  $profit_center : '';
		$list = $due->getRecordDetails($member_id,$date_from,$date_to,$profit_center,$payment_type);

		$filename = "dues-details-" . date('m-d-Y-h-i-s') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");
		if($list){
			$arr_payment= ['','Cash','Cheque','Credit'];
			?>
			<table class='table table-bordered'>
				<thead>
					<tr>
						<th>Date</th>
						<th>Client</th>
						<th>Remarks</th>
						<th>Maturity</th>
						<th>Payment Method</th>
						<th>Check No</th>
						<th>Total</th>
						<th>Notes</th>
					</tr>
				</thead>
				<tbody>
			<?php
			foreach($list as $item){
				$item->dt_collected = date('m/d/Y',$item->dt_collected);
				$item->payment_type = $arr_payment[$item->payment_type];
				?>
				<tr>
					<td><?php echo $item->dt_collected; ?></td>
					<td><?php echo $item->member_name; ?></td>
					<td><?php echo $item->remarks; ?></td>
					<td><?php echo $item->date_matured; ?></td>
					<td><?php echo $item->payment_type; ?></td>
					<td><?php echo $item->check_number; ?></td>
					<td><?php echo $item->amount; ?></td>
					<td></td>
				</tr>
				<?php
			}
			?>
			</tbody>
			</table>
			<?php
		}

	}