<?php

	error_reporting(0);

	ini_set('session.gc_maxlifetime', 7200);

	// each client should remember their session id for EXACTLY 1 hour
	session_set_cookie_params(7200);

	session_start(); // ready to go!

	include '../core/connection.php';

	spl_autoload_register(function($class){
		require_once '../classes/' . $class . '.php';
	});

	require_once '../functions/sanitize.php';

	include_once '../admin/includes/labels.php';

