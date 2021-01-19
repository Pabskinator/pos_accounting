<?php
	include 'ajax_connection.php';

	$c = Input::get('cid');
	$b = Input::get('branchname');
	$branch = new Branch();
	$myb = $branch->isBranchExist($b,$c);
	if($myb){
		echo "true";
	} else {
		echo "false";
	}
?>