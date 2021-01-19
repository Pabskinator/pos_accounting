<?php
	include 'ajax_connection.php';

	$c = Input::get('cid');
	$i = Input::get('itemname');
	$item = new Product();
	$myi = $item->isProductExist($i,$c);
	if($myi){
		echo "true";
	} else {
		echo "false";
	}
?>