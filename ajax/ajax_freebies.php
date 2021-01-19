<?php
	include 'ajax_connection.php';

	$functionName = Input::get("functionName");


	if(function_exists($functionName)){
		$functionName();
	}

	function getRecord(){

		$user = new User();
		$if = new Item_freebie();

		$list = $if->getRecord($user->data()->company_id);
		$arr_main = [];
		$arr = [];
		if($list){
			foreach($list as $l){
				if(!in_array($l->item_id,$arr_main)){
					$arr_main[$l->item_id] = ['item_code' => $l->item_code,'description' => $l->description,'qty' => formatQuantity($l->qty)];
				}
				$arr[$l->item_id][] = $l;
			}
			$ret = [];
			foreach($arr_main as $item_id => $details){
				$freebies = "";
				foreach($arr[$item_id] as $c){
					if($c->discount == 100){
						$label = "<span class='badge badge-danger'>Free</span>";
					} else {
						$label = "<span class='badge badge-danger'>{$c->discount}% discount</span>";
					}
					$freebies .="<p>$c->item_code_freebie - $label </p>";
				}
				$details['freebies'] = $freebies;
				$ret[] = $details;
			}

			echo json_encode($ret);

		}

	}


	function saveFreebie(){
		$item = json_decode(Input::get('main_item'));
		$freebies = json_decode(Input::get('freebies'));


		$item_freebie = new Item_freebie();
		$now = time();
		$user = new User();
		$item_freebie->create(
			[
				'item_id' => $item->item_id,
				'qty' => $item->qty,
				'created'=> $now,
				'user_id' => $user->data()->id,
				'company_id' => $user->data()->company_id,
				'is_active' => 1

			]
		);
		$last_id = $item_freebie->getInsertedId();

		if($last_id){
			if($freebies){
				$freebie_details = new Item_freebie_detail();
				foreach($freebies as $freebie){
					$freebie_details->create(
						[
							'item_id' => $freebie->item_id,
							'if_id' => $last_id,
							'qty' => $freebie->qty,
							'discount' => $freebie->discount
						]
					);
				}
			}
		}
		echo json_encode(['success' => true]);


	}