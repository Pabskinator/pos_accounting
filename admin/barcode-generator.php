<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';

	if(!$user->hasPermission('item')){
		Redirect::to(1);
	}
	$barcodeClass = new Barcode();
	$barcode_format = $barcodeClass->getBarcodeFormat($user->data()->company_id);

	$styles =  json_decode($barcode_format[0]->styling,true);


	require_once 'views/barcode/barcode.view.php';

	require_once '../includes/admin/page_tail2.php';