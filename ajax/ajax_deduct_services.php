
<?php
	include 'ajax_connection.php';


	$s = Input::get("s");
	$ss = json_decode($s,true);
	foreach($ss as $s){
		$service = new Service();
		$sqty = $s['consumable_qty'] - 1;
		$service->update(array(
			'consumable_qty' => $sqty
			), $s["service_id"]);
	}

	echo "1";


