<?php
// $user have all the properties and method of the current user
require_once '../includes/admin/page_head2.php';

	if(!$user->hasPermission('branch')){
		// redirect to denied page
		Redirect::to(1);
	}
	// get all branch base on company
	$branch = new Branch();
	$branches = $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));

 require_once 'views/branch/branch.view.php';

 require_once '../includes/admin/page_tail2.php';