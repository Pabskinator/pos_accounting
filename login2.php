<?php
	$http_host = $_SERVER['HTTP_HOST'];
	if($http_host == 'kababayan.apollosystems.ph'){
		include_once('includes/logins/kababayan_login.php');
	} else {
		//include_once('includes/logins/kababayan_login.php');
		include_once('includes/logins/default_log.php');
	}

?>




