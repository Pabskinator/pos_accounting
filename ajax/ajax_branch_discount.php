<?php
	include 'ajax_connection.php';
	$functionName = Input::get("functionName");

	if(function_exists($functionName)){
		$functionName();
	}

	function getList(){
		$branch_discount = new Branch_discount();
		$list = $branch_discount->getAll();
		if(!$list) $list = [];
		echo json_encode($list);
	}
	function insertDiscount(){
		$user = new User();
		$branch_disocunt = new Branch_discount();
		$request = json_decode(Input::get('request'));
		if($request->branch_id_req && $request->branch_id_src && $request->discount){
			$request->discount = str_replace('%','',$request->discount);
			$branch_disocunt->create(array(
				'branch_id_req' => $request->branch_id_req,
				'branch_id_src' => $request->branch_id_src,
				'discount' => $request->discount,
				'is_active' => 1,
				'company_id' => $user->data()->company_id,
				'status' => 1,
				'created' => time(),
			));
			echo json_encode(['success' => true]);
		} else {
			echo json_encode(['success' => false]);
		}

	}