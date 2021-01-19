<?php
	$http_host = $_SERVER['HTTP_HOST'];

	$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

	$sublink = substr($actual_link,0,31); // two char

	$sublink5char = substr($actual_link,0,34); // five char

	$sublinknet = substr($actual_link,0,33);

	if($http_host == 'kababayan.apollosystems.ph'){
		include_once('includes/logins/kababayan_login.php');
	} else if ($http_host == 'vitalite.apollosystems.com.ph'
		|| $http_host == 'sh.apollosystems.com.ph'
	){
		$color_bg_background = "#fff";
		include_once('includes/logins/default_log.php');
	} else if ( $http_host == 'localhost:81' ||
		$http_host == 'demo.apollosystems.com.ph' ||
		($http_host == 'calayan.apollosystems.com.ph'  || ($http_host == 'apollosystems.com.ph' && $sublink == 'https://apollosystems.com.ph/mp')) ||
		($http_host == 'avision.apollosystems.net'  || ($http_host == 'apollosystems.net' && $sublinknet == 'https://apollosystems.net/avision')) ||
		$http_host == 'zenspa.apollosystems.com.ph'  ||
		$http_host == 'cebuhiq.apollosystems.com.ph'
		){
		$color_bg_background = "#ccc";
		include_once('includes/logins/with_captcha.php');
	} else if ($http_host == 'calayan.apollosystems.com.ph'  ||  ($http_host == 'apollosystems.com.ph' && $sublink == 'https://apollosystems.com.ph/mp')){
		$color_bg_background = "#ccc";
		include_once('includes/logins/with_captcha.php');
	}else {

		$color_bg_background = "#fff";
		include_once('includes/logins/default_log.php');

	}