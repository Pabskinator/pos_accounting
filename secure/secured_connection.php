<?php
	session_start();
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	include '../core/connection.php';
	spl_autoload_register(function($class){
		require_once '../classes/' . $class . '.php';
	});
	require_once '../functions/sanitize.php';
	include_once '../admin/includes/labels.php';
