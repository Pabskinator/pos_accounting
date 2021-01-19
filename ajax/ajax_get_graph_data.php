<?php
	include 'ajax_connection.php';
	$type = Input::get('type');
	$user = new User();
	if($type == 1){
		$gsales = new Sales();
		// base on branch
		$branchsales = $gsales->getTotalSalesPerBranch($user->data()->company_id);
		$saleslist = '';
		$bbi = 0;
		foreach($branchsales as $bb){
			if($bbi % 2 == 0) $style = "red"; else $style = "blue";

			$saleslist .= "['" . $bb->name ."',".number_format( $bb->saletotal, 2, '.', '').",'$style'],";

			$bbi +=1;
		}
		$saleslist = rtrim($saleslist,",");
		echo $saleslist;

	}