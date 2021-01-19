
<?php
	include 'ajax_connection.php';


	$cid = Input::get("cid");

	$service = new Service();
	$services = $service->getServices($cid);
	if($services){
		echo json_encode($services);
	} else {
		echo '0';
	}


