<?php
	include 'ajax_connection.php';

	$c = Input::get('cid');
	$b=Input::get('bid');
	$r = Input::get('rack');
	$rack = new Rack();
	$myb = $rack->isRackExists($r,$c,$b);
	if($myb){
		echo "true";
	} else {
		echo "false";
	}
?>