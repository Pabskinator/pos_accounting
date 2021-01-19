<?php
	include 'ajax_connection.php';
	$functionName = Input::get("functionName");

	if(function_exists($functionName)){
		$functionName();
	}


	function getList(){

		$items = json_decode(Input::get('items'));
		$arr_errors = [];
		if($items){
			$branch_group = new Branch_group_pricelist();

			foreach($items as $item){

				$item_id = $item->item_id;
				$branch_group_id = $item->branch_group_id;
				$price = $item->price;
				$barcode = $item->barcode;

				if($item_id && $branch_group_id){

					$checker = $branch_group->checkerEx($branch_group_id,$item_id);

					if(isset($checker->cnt) && $checker->cnt > 0){
						$arr_errors[] = $item->item_code ." already exists";
					} else {
						$branch_group->create(
							[
								'item_id' => $item_id,
								'branch_group_id' => $branch_group_id,
								'barcode' => $barcode,
								'price' => $price,
							]
						);
					}

				}

			}
		} else {
			$arr_errors[] = "Invalid Items";
		}
		echo json_encode($arr_errors);


	}