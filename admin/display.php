<?php
	// $user have all the properties and method of the current user
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('display_location_m')){
		// redirect to denied page
		Redirect::to(1);
	}
	$display = new Display_location();
	$display = $display->get_active('display_location',array('company_id' ,'=',$user->data()->company_id));

 require_once 'views/display_location/display-location.view.php';

 require_once '../includes/admin/page_tail2.php';
