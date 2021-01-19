<?php
	require_once 'core/init.php';
	$user = new User();
	$user->logout();
//	session_destroy();
    unset($_SESSION["acc_log"]);
	Redirect::to('login.php');
?>