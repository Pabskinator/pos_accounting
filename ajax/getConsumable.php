<?php
	include 'ajax_connection.php';
	$type = Input::get('type');
	if($type == 1){
		// check if there is consumable items
		$company_id = Input::get('c');
		$p = new Product();
		if($p->checkConsumable($company_id) == true){
			echo 1;
		} else {
			echo 2;
		}
	} else if ($type==2){
		// get all members
		$company_id = Input::get('c');
		$m = new Member();
		$mem =  $m->getMembers($company_id);
		$memberjson = "{";
		foreach($mem as $em){
			$memberjson.= "\"$em->id\":{\"lastname\":\"$em->lastname\",\"firstname\":\"$em->firstname\",\"middlename\":\"$em->middlename\",\"company_id\":\"$em->company_id\"},";
		}
		$memberjson = rtrim($memberjson,",");
		$memberjson .="}";
		echo $memberjson;
	}

?>