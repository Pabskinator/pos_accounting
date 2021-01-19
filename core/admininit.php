<?php
	// server should keep session data for AT LEAST 1 hour
	ini_set('session.gc_maxlifetime', 86400);

	// each client should remember their session id for EXACTLY 1 hour
	session_set_cookie_params(86400);

	session_start(); // ready to go!

	include 'connection.php';

	spl_autoload_register(function($class){
		  require_once '../classes/' . $class . '.php';
	});

	require_once '../functions/sanitize.php';

	error_reporting(0);

