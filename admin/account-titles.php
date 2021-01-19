<?php
	// $user have all the properties and method of the current user
	// test test
// test 3 456
// test 3 updated

	require_once '../includes/admin/page_head2.php';

	if(!$user->hasPermission('acc_v')){
		// redirect to denied page
		Redirect::to(1); //sdf
	}

	$account_title = new Account_title();
	$account_titles = $account_title->get_active('account_titles',array('company_id' ,'=',$user->data()->company_id));

	require_once 'views/account_title/account-titles.view.php';
    require_once '../includes/admin/page_tail2.php';


