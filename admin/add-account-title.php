<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('acc_m')) {
	// redirect to denied page
	Redirect::to(1);
	}

	if(isset($_GET['edit'])) {
		$editid = $_GET['edit'];
	} else {
		$editid = 0;
	}

	require_once 'views/account_title/add-account-title.view.php';

	require_once '../includes/admin/page_tail2.php';