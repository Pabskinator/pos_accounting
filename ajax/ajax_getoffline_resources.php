
	<?php
		include 'ajax_connection.php';
	$cid = Input::get("cid");
	$u = new User();
	$users =  $u->getUsers($cid);


	$userjson = "{";
	foreach($users as $us){
		$userjson.= "\"$us->username\":{\"id\":\"$us->id\",\"password\":\"$us->password\",\"lastname\":\"$us->lastname\",\"firstname\":\"$us->firstname\",\"middlename\":\"$us->middlename\",\"company_name\":\"$us->company_name\",\"company_id\":\"$us->company_id\",\"branch_id\":\"$us->branch_id\",\"position\":\"$us->position\",\"position_id\":\"$us->position_id\",\"middlename\":\"$us->middlename\",\"permissions\":\"" .addslashes($us->permisions) . "\"},";
	}
		$userjson = rtrim($userjson,",");
		 $userjson .="}";
	 echo $userjson;
?>
