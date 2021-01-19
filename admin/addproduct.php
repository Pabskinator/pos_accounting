<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('item')) {
		// redirect to denied page
		Redirect::to(1);
	}
	// check if it is edit or new insert
	if(isset($_GET['edit'])) {
		$editid = $_GET['edit'];

	} else {
		$editid = 0;
	}

	if(Input::get('page')){
		$page = Input::get('page');
	} else {
		$page = 0;
	}

	$prev_search = "";
	$prev_categ = "";

	if(Input::get('search')){
		$prev_search = Input::get('search');
	}

	if(Input::get('categ')){
		$prev_categ = Input::get('categ');
	}

	$http_host = $_SERVER['HTTP_HOST'];
	$displaynone= "display:none;";
	$displaycerf = "display:none;";
	$displayProductCost = "display:none;";

	/*if($http_host == 'pw.apollosystems.com.ph' || $http_host == 'aquabest.apollosystems.com.ph' ){
		$displaynone= "display:block;";
	} else if($http_host == 'localhost:81'){
		$displaynone= "display:block;";
		$displaycerf= "display:block;";
		$displayProductCost= "display:block;";

	} else if($http_host == 'safehouse.apollosystems.ph'){
		$displaycerf= "display:block;";

	} else if($http_host == 'cebuhiq.apollosystems.com.ph'){
		$displayProductCost= "display:block;";
	} */
	if(Configuration::thisCompany('pw') || Configuration::thisCompany('aquabest') ){
		$displaynone= "display:block;";
	} else if($http_host == 'localhost:81'){
		$displaynone= "display:block;";
		$displaycerf= "display:block;";
		$displayProductCost= "display:block;";

	} else if($http_host == 'safehouse.apollosystems.ph'){
		$displaycerf= "display:block;";

	} else if(Configuration::thisCompany('cebuhiq')){
			$displayProductCost= "display:block;";
	}


	require_once 'views/product/add-product.view.php';

	require_once '../includes/admin/page_tail2.php';


