<?php
	include 'ajax_connection.php';
	$isblock = Input::get('isblack');
	$memid = Input::get('memid');
	$mem = new Member($memid);

	$user = new User();
	$lbl = ($isblock) ? 'Block' : 'Unblock';
	Log::addLog(
		$user->data()->id,
		$user->data()->company_id,
		"$lbl Client ". $mem->data()->lastname,
		'ajax_deletepermanent.php'
	);
	$mem->changeBlacklistStatus($isblock,$memid);