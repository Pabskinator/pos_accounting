
<?php
	include 'ajax_connection.php';


	$functionName = Input::get("functionName");
	$functionName();

	function returnOrder(){
		$id = Input::get('id');
		$req = new Agent_request();
		$req->update(['status' => 1], $id);
		$req_mon = new Request_monitoring();
		$user = new User();
		$req_mon->create(array(
			'agent_request_id' => $id,
			'status' => 1,
			'user_id' =>$user->data()->id,
			'date_approved' => strtotime(date('Y/m/d H:i:s')),
			'is_active' => 1,
			'company_id' => $user->data()->company_id,
			'remarks' => 'Returned By'
		));
		echo "Request returned successfully.";
	}

	function deleteCaravanItem(){

		$id = Input::get('id');
		$agent_details = new Agent_request_details();
		$agent_details->deleteItem($id);

	}

	function caravanForApproval(){
		$id = Input::get('id');
		$cache_liquidation = Input::get('cache_liquidation');
		$cache_payment= Input::get('cache_payment');
		$agent = new Agent_request();
		if(is_numeric($id)){
			$agent->update(['cache_liquidation' =>$cache_liquidation,'cache_payment'=> $cache_payment,'is_approve_liq'=>1 ],$id);
		}
	}
	function returnLiquidation(){
		$id = Input::get('id');

		$agent = new Agent_request();
		if( is_numeric($id)){
			$agent->update(['is_approve_liq'=>-1 ],$id);
		}
	}

	function getCurrentQty(){
		$item_id = Input::get("item_id");
		$branch_id = Input::get('branch_id');
		$invcheck = new Inventory();
		$allqty = $invcheck->getAllQuantity($item_id,$branch_id);
		 if($allqty->totalQty){
			 echo $allqty->totalQty;
		} else {
			 echo 0;
		 }
	}

	function addNewItem(){
		$item_id = Input::get('item_id');
		$qty = Input::get('qty');
		$id = Input::get('id');

		$det = new Agent_request_details();
		$now = time();

		if($item_id && is_numeric($item_id) && $id && is_numeric($id)  && $qty && is_numeric($qty) && $qty > 0){

			 $check = $det->isExists($id,$item_id);

			if(isset($check->cnt) && $check->cnt > 0){
				echo "Item already exists.";
			} else {
				$det->create(
					[
						'item_id' => $item_id,
						'qty' => $qty,
						'request_id' => $id,
						'is_active' => 1,
						'modified' => $now,
						'created' => $now
					]
				);
				echo "Item added successfully.";
			}




		} else {
			echo "Invalid request.";
		}


	}

	function reUse(){
		$id = Input::get('id');
		if($id && is_numeric($id)) {


			$agent = new Agent_request($id);
			if(isset($agent->data()->id) && $agent->data()->id){
				$main = ['branch_id' => $agent->data()->branch_id, 'remarks' => $agent->data()->remarks, 'witness' => $agent->data()->witness];


				$agent_details = new Agent_request_details();

				$list = $agent_details->getItems($id);


				$arr = [];
				$prod = new Product();

				foreach($list as $a) {
					$p = $prod->getPrice($a->item_id);
					$arr[] = ['price' => $p->price,'barcode'=> $a->barcode,'id' => $a->item_id,'item_code' => $a->item_code, 'description' => $a->description, 'qty' => formatQuantity($a->qty)];
				}

				echo  json_encode(['main' => $main, 'items' => $arr]);
			}

		}

	}


