<?php
	include 'ajax_connection.php';

	$functionName = Input::get("functionName");
	if(function_exists($functionName)) $functionName();

	function getMedHistoryList(){

	}