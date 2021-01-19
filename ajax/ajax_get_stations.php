
<?php
	include 'ajax_connection.php';
	$company = Input::get("company_id");

	$stat = new Station();
	$stations = $stat->getAllStation($company);

	if($stations){
		echo json_encode($stations);
	} else {
		echo '0';
	}

