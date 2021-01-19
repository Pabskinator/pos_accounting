<?php
// die("Detecting slow server response. Please allow us some time to look into this. Thank you for your patience in advance.");

	$http_host = $_SERVER['HTTP_HOST'];

	$globals_order_limit = 0;

	$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

	$sublink = substr($actual_link,0,31);

	$sublinknet = substr($actual_link,0,33);


	if($http_host == 'kababayan.apollosystems.ph') {
		error_reporting(0);
		$mysql_username = "apollo29_kbb";
		$mysql_password = "409186963@StephenWang";
		$mysql_database = "apollo29_kbb";
	} else if($http_host == 'safehouse.apollosystems.com.ph') {
		error_reporting(0);
		$mysql_username = "apollo29_safeh";
		$mysql_password = "409186963@StephenWang";
		$mysql_database = "apollo29_safehouse";
	}  else if($http_host == 'erp.bestphonedeals.ph') {
		 error_reporting(0);
		 $mysql_username = "apollosy_bgcon";
		 $mysql_password = "409186963@StephenWang";
		 $mysql_database = "apollosy_bgcon";
	 } else if($http_host == 'www.erp.bestphonedeals.ph') {
		 error_reporting(0);
		 $mysql_username = "apollosy_bgcon";
		 $mysql_password = "409186963@StephenWang";
		 $mysql_database = "apollosy_bgcon";
	 } else if($http_host == 'bgcon.apollosystems.com.ph') {
		 error_reporting(0);
		 $mysql_username = "apollosy_bgcon";
		 $mysql_password = "409186963@StephenWang";
		 $mysql_database = "apollosy_bgcon";
	 }  else if($http_host == 'www.bgcon.apollosystems.com.ph') {
		 error_reporting(0);
		 $mysql_username = "apollosy_bgcon";
		 $mysql_password = "409186963@StephenWang";
		 $mysql_database = "apollosy_bgcon";
	 } else if($http_host == 'pw.apollosystems.com.ph') {
		error_reporting(0);
		$mysql_username = "apollosy_peanut";
		$mysql_password = "409186963@StephenWang";
		$mysql_database = "apollosy_peanutworld";
	} else if($http_host == 'kababayan.apollosystems.com.ph') {
		error_reporting(0);
		$mysql_username = "apollosy_kbb";
		$mysql_password = "409186963@StephenWang";
		$mysql_database = "apollosy_kbb";
	} else if($http_host == 'vitalite.apollosystems.com.ph') {
		error_reporting(0);
		$mysql_username = "apollosy_vit";
		$mysql_password = "409186963@StephenWang";
		//$mysql_database = "apollosy_vitalite";
		$mysql_database = "apollosy_vit";
	} else if($http_host == 'aquabest.apollosystems.com.ph') {
		error_reporting(0);
		$mysql_username = "apollosy_aqbest";
		$mysql_password = "409186963@StephenWang";
		//$mysql_database = "apollosy_vitalite";
		$mysql_database = "apollosy_aquabest";
	} else if($http_host == 'calayan.apollosystems.com.ph'  ||  ($http_host == 'apollosystems.com.ph' && $sublink == 'https://apollosystems.com.ph/mp')) {

		error_reporting(E_ALL);
		ini_set('display_errors', 1);

		$mysql_username = "apollosy_mp";
		$mysql_password = "409186963@StephenWang";
		$mysql_database = "apollosy_mp";


	} else if($http_host == 'demo.apollosystems.com.ph' ||  ($http_host == 'apollosystems.com.ph' && $sublink == 'https://apollosystems.com.ph/de')) {
		// error_reporting(E_ALL);
		// ini_set('display_errors', 1);
		 error_reporting(0);
		$mysql_username = "apollosy_demo";
		$mysql_password = "409186963@StephenWang";
		$mysql_database = "apollosy_demo2";


	} else if($http_host == 'zenspa.apollosystems.com.ph') {
		// error_reporting(E_ALL);
		// ini_set('display_errors', 1);
		 error_reporting(0);
		$mysql_username = "apollosy_zenspa";
		$mysql_password = "409186963@StephenWang";
		$mysql_database = "apollosy_zenspa";


	} else if($http_host == 'cebuhiq.apollosystems.com.ph') {
		 // error_reporting(E_ALL);
		 // ini_set('display_errors', 1);
		 error_reporting(0);
		 $mysql_username = "apollosy_cebuhiq";
		 $mysql_password = "409186963@StephenWang";
		 $mysql_database = "apollosy_cebuhiq";

	 } else if($http_host == 'cn.apollosystems.com.ph') {
		 error_reporting(0);
		 $mysql_username = "apollosy_candy";
		 $mysql_password = "409186963@StephenWang";
		 $mysql_database = "apollosy_candy";

	 } else if ($http_host == 'sh.apollosystems.com.ph') {
		 error_reporting(0);
		 $mysql_username = "apollosy_sh";
		 $mysql_password = "409186963@StephenWang";
		 $mysql_database = "apollosy_sh";

	 } else if($http_host == 'avision.apollosystems.net' ||  ($http_host == 'apollosystems.net' && $sublinknet == 'https://apollosystems.net/avision')) {
		 // error_reporting(E_ALL);
		 // ini_set('display_errors', 1);
		 error_reporting(0);
		 $mysql_username = "apollos1_avision";
		 $mysql_password = "409186963@StephenWangAvision";
		 $mysql_database = "apollos1_avision";


	 }  else if($http_host == 'zamaryan.apollosystems.net' ||  ($http_host == 'apollosystems.net' && $sublinknet == 'https://apollosystems.net/zamarya')) {
		 // error_reporting(E_ALL);
		 // ini_set('display_errors', 1);
		  error_reporting(0);
		  $mysql_username = "apollos1_sr";
		  $mysql_password = "409186963@StephenWang";
		  $mysql_database = "apollos1_saintroche";


	 }   else if ($http_host == 'dev.apollo.ph:81') {

		 error_reporting(0);
		 ini_set('display_errors', 1);
		 $mysql_username = "root";
		 $mysql_password = "";
		 $mysql_database = "vit_new";

	} else if ($http_host == 'localhost:81') {

		//	error_reporting(E_ALL);
		//ini_set('display_errors', 1);

		 error_reporting(0);

		 $mysql_username = "root";

		 $mysql_password = "";

		 $mysql_database = "avi";


	} else if($http_host == 'localhost') {

		// error_reporting(E_ALL);
		// ini_set('display_errors', 1);
		 error_reporting(0);
		 $mysql_username = "root";
		 $mysql_password = "";
		 $mysql_database = "dunsk";

	} else {

		 $mysql_username = "";
		 $mysql_password = "";
		 $mysql_database = "";

	 }





	date_default_timezone_set('Asia/Manila');
	$GLOBALS['config'] = array('mysql' => array('host' => 'localhost', 'username' => $mysql_username, 'password' => $mysql_password, 'db' => $mysql_database), 'remember' => array('cookie_name' => 'hash', 'cookie_expiry' => 604800), 'session' => array('session_name' => 'user', 'token_name' => 'token'));