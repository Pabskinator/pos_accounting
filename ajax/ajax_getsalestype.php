<?php
	include 'ajax_connection.php';
	$cid = Input::get('cid');
	// add rack id n escape
	$salestype = new Sales_type();

	$salestypes = $salestype->get_active('salestypes', array('company_id','=',$cid));
	echo json_encode($salestypes);

	
