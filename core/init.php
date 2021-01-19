<?php

ini_set('session.gc_maxlifetime', 86400);
session_set_cookie_params(86400);

session_start();
include 'connection.php';
spl_autoload_register(function($class){
	require_once 'classes/' . $class . '.php';

});
require_once 'functions/sanitize.php';

