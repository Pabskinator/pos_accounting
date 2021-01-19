<?php
	include 'ajax_connection.php';
	$functionName = Input::get("functionName");

	if(function_exists($functionName)){
		$functionName();
	}


	function getNotif(){

		$wh = new Wh_order();

		$user = new User();
		$status = Input::get('status');
		$service_notif = 0;
		if($status == 1){
			$status = 0;
			$service_notif = 1;
		} else if ($status == 2){
			$status = 0;
			$service_notif = 2;
		}

		$user_id = 0;
		if(!$user->hasPermission('item_service_pr')){
			$user_id = $user->data()->id;
		}

		if($service_notif == 2){
			$whdate = new Wh_service_date();
			$list = $whdate->getNotification();
			//dump($list);
			if($list){
				echo "<table id='tblForApproval' class='table table-bordered'>";
				echo "<tr><th>Item Code</th><th>Description</th><th>Starting Date</th><th>Duration</th><th>Deadline</th></tr>";
				foreach($list as $l){
					echo "<tr><td>".$l->item_code."</td><td>".$l->description."</td><td> ".date('m/d/Y',$l->start_date)." </td><td>".$l->duration ."</td><td>".$l->deadline ."</td></tr>";
				}
				echo "</table>";

			}
		} else {

			$list = $wh->getServiceNotif($user->data()->company_id,$user_id,0,0,0,$status,0,0,0,4000,0,0,0,0,0,$service_notif);

			if($list){

				echo "<table id='tblBordered' class='table'>";

				echo "<tr><th>ID</th><th>Client</th><th>Address</th><th>Created At</th>";
				echo "<th></th></tr>";

				foreach($list as $l){

					echo "<tr>";
					echo "<td >$l->id</td>";
					echo "<td>$l->mln</td>";
					echo "<td>$l->personal_address</td>";
					echo "<td>" . date('m/d/Y',$l->created). "</td>";
					echo "<td><button data-id='$l->id' class='btn btn-default btnDetails'>Details</button></td>";
					echo "</tr>";


				}
				echo "</table>";

			} else {
				echo "<div class='alert alert-info'>No record</div>";
			}
		}


	}
	function updateOrderNotif(){
		$id = Input::get('id');
		$durations = Input::get('durations');

		if($id && is_numeric($id)) {

			$durations = json_decode($durations);

			foreach($durations as $dur) {
				$wsd = new Wh_service_date();
				$wsd->create(['wh_order_id' => $id, 'duration' => $dur->duration, 'item_id' => $dur->item_id]);
			}

			$wh = new Wh_order();

			$wh->update(['status' => 1, 'for_notif_service' => 1], $id);

		}

	}

	function updateOrderDate(){
		$id = Input::get('id');
		if($id && is_numeric($id)){
			$wh = new Wh_order();
			$wds = new Wh_service_date();
			$data = $wds->getInfo($id);

			if($data->id && is_numeric($data->id)){

				$start_dates= json_decode(Input::get('start_dates'));

				foreach($start_dates as $sd){
					$wds->updateDate($sd->item_id,strtotime($sd->start_date),$id);
				}

				$wh->update(
					['for_notif_service' => 2], $id
				);
			}

		}
	}


	function orderDetails(){
		$id = Input::get('id');
		$wh = new Wh_order($id);
		$whorder = new Wh_order_details();
		$orders = $whorder->getOrderForNotif($id);
		$user = new User();
		$user_id = 0;
		if(!$user->hasPermission('item_service_pr')){
			$user_id = $user->data()->id;
		}

		echo "<table class='table' id='tblDetails'>";
		echo "<thead><tr><th>Item</th><th>Description</th><th>Qty</th>";

		echo "<th>";
		if($wh->data()->status != -1) {
			echo "Duration";
		}
		echo "</th>";

		echo "<th>";
		if($wh->data()->status != -1 ) {
			echo "Start";
		}
		echo "</th>";
		echo "</tr></thead>";
		echo "<tbody>";
		foreach($orders as $o){
			echo "<tr data-item_id='$o->item_id'>";
			echo "<td>$o->item_code</td>";
			echo "<td>$o->description</td>";
			echo "<td>" . formatQuantity($o->qty). "</td>";
			echo "<td>";
			if($wh->data()->status == -1 && !$user_id) {
				echo "<input type='text' placeholder='Duration'>";
			}
			if($wh->data()->status != -1 && $o->duration) {
				echo $o->duration;
			}
			echo "</td>";

			echo "<td>";
			if($wh->data()->status != -1 && !$o->start_date && !$user_id) {
				echo "<input type='text'  class='' placeholder='Start Date'>";
			}
			if($wh->data()->status != -1 && $o->start_date) {
				echo date('m/d/Y',$o->start_date);
			} else if($wh->data()->status != -1 && !$o->start_date && $user_id){
				echo "Pending";
			}

			echo "</td>";
			echo "</tr>";
		}
		echo "</tbody>";
		echo "</table>";

		if($wh->data()->status == -1 && !$user_id){
			echo "<button class='btn btn-primary' data-id='$id' id='saveDuration'>Save Duration</button>";
		} else if ($wh->data()->for_notif_service == 1 && !$user_id){
			echo "<button class='btn btn-primary'  data-id='$id' id='saveDate'>Save</button>";
		}

	}