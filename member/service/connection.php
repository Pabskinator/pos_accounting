<?php 


	 $http_host = $_SERVER['HTTP_HOST'];
	$globals_order_limit = 0;
	if($http_host == 'pw.apollosystems.ph'){
		error_reporting(0);
		$mysql_username = "apollo29_peanut";
		$mysql_password = "409186963@StephenWang";
		$mysql_database = "apollo29_peanutworld";

	} else if($http_host == 'kababayan.apollosystems.ph'){
		error_reporting(0);
		$mysql_username = "apollo29_kbb";
		$mysql_password = "409186963@StephenWang";
		$mysql_database = "apollo29_kbb";
	} else if($http_host == 'vitalite.apollosystems.ph'){
		error_reporting(0);
		$mysql_username = "apollo29_vit";
		$mysql_password = "409186963@StephenWang";
		$mysql_database = "apollo29_vit";

	} else if($http_host == 'calayan.apollosystems.ph'){
		error_reporting(0);
		$mysql_username = "apollo29_mp";
		$mysql_password = "409186963@StephenWang";
		$mysql_database = "apollo29_mp";

	}  else if($http_host == 'sh.apollosystems.com.ph'){
		error_reporting(0);
		$mysql_username = "apollosy_sh";
		$mysql_password = "409186963@StephenWang";
		$mysql_database = "apollosy_sh";

	}  else if($http_host == 'safehouseacademy.com'){
		error_reporting(0);
		$mysql_username = "apollo29_safeh";
		$mysql_password = "409186963@StephenWang";
		$mysql_database = "apollo29_safehouse";

	} else if($http_host == 'dev.apollo.ph:81'){
		$mysql_username = "root";
		$mysql_password = "";
		$mysql_database = "vit_new";

	} else if($http_host == 'localhost'){
		$mysql_username = "root";
		$mysql_password = "";
		$mysql_database = "vit_new";

	} else {
		$mysql_username = "root";
		$mysql_password = "";
		$mysql_database = "vit_new";

	}

	date_default_timezone_set('Asia/Manila');
	$mysqli = new mysqli("localhost", $mysql_username, $mysql_password,$mysql_database);
	$mysqli->query("SET timezone = '+8:00'");
?>