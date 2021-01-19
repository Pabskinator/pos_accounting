<?php
	include 'ajax_connection.php';
	$functionName = Input::get("functionName");

	if(function_exists($functionName)){
		$functionName();
	}

	function getBO(){
		$inv = new Inventory();
		$user = new User();
		$rack = new Rack();

		$default_racks = $rack->getRackDefaults($user->data()->branch_id);

		$surplus = $inv->getRackItemsAndInventory($default_racks->surplus_rack,$user->data()->branch_id);

		$bo =  $inv->getRackItemsAndInventory($default_racks->bo_section,$user->data()->branch_id);

		echo json_encode(['surplus' => $surplus, 'bo' => $bo,'test' => 123]);


	}

	function convert(){

		$inv = new Inventory();
		$user = new User();
		$rack = new Rack();


		$items =  json_decode(Input::get('items'));


		$now = time();



		$arr_good = [];
		$arr_bad = [];

		/*
		 *
		{id:'1',text:'Good'},
		{id:'4',text:'BO'},
		{id:'5',text:'Surplus'},

		{id:'2',text:'Damage'},
		{id:'3',text:'Incomplete'},
		{id:'6',text:'Dispose'}

		*/

		if($items){



			foreach($items as $item){
				if($item->type_id == 1 || $item->type_id == 4 || $item->type_id == 5){
					$arr_good[] = $item;
				} else {
					$arr_bad[] = $item;
				}

			}

			if($arr_good){

				/*
				$tranfer_mon = new Transfer_inventory_mon();
				$tranfer_mon->create(array(
					'status' => 1,
					'is_active' =>1,
					'branch_id' =>$user->data()->branch_id,
					'company_id' =>$user->data()->company_id,
					'created' => $now,
					'modified' => $now,
					'from_where' => 'From BO/Surplus'
				));

				$lastid = $tranfer_mon->getInsertedId();

				*/

				foreach($arr_good  as $item){
					/*

					$tranfer_mon_details = new Transfer_inventory_details();
					$tranfer_mon_details->create(array(
						'transfer_inventory_id' => $lastid,
						'rack_id_from' => $item->rack_id_from,
						'rack_id_to' => $item->rack_id_to,
						'item_id' =>$item->item_id,
						'qty' => $item->qty,
						'is_active' => 1
					));

					*/

				}
			}

			if($arr_bad){
				$inventory = new Inventory();
				$inv_mon = new Inventory_monitoring();
				$inv_issues = new Inventory_issue();
				$inv_mon_issues = new Inventory_issues_monitoring();
				foreach($arr_bad as $item){

					// substract inventory

					$curinventoryFrom = $inventory->getQty($item->item_id,$user->data()->branch_id,$item->rack_id_from);

					$inventory->subtractInventory($item->item_id,$user->data()->branch_id,$item->qty,$item->rack_id_from);

					// monitoring

					$newqtyFrom = $curinventoryFrom->qty - $item->qty;

					$inv_mon->create(array(
						'item_id' => $item->item_id,
						'rack_id' => $item->rack_id_from,
						'branch_id' => $user->data()->branch_id,
						'page' => 'admin/transfer.php',
						'action' => 'Update',
						'prev_qty' => $curinventoryFrom->qty,
						'qty_di' => 2,
						'qty' => $item->qty,
						'new_qty' => $newqtyFrom,
						'created' => time(),
						'user_id' => $user->data()->id,
						'remarks' => 'Convert inventory',
						'is_active' => 1,
						'company_id' => $user->data()->company_id
					));

					// 1=damage 2=incomplete 4=missing  3=disposed

					$lbl = "";

					if($item->type_id == 2){
						$convert_type = 1;
						$lbl = "damage";
					} else if($item->type_id == 3){
						$convert_type = 2;
						$lbl = "incomplete";
					} else if($item->type_id == 6){
						$convert_type = 3;
						$lbl = " disposed";
					}

					$curinvissues = $inv_issues->getQty($item->item_id,$user->data()->branch_id,$item->rack_id_to,$convert_type);
					if(isset($curinvissues->qty)){
						$cur_issues = $curinvissues->qty;
					} else {
						$cur_issues = 0;
					}

					if($inv_issues->checkIfItemExist($item->item_id,$user->data()->branch_id,$user->data()->company_id,$item->rack_id_to,$convert_type)){
						$inv_issues->addInventory($item->item_id,$user->data()->branch_id,$item->qty,false,$item->rack_id_to,$convert_type);
					} else {
						$inv_issues->addInventory($item->item_id,$user->data()->branch_id,$item->qty,true,$item->rack_id_to,$convert_type);
					}

					$new_issues = $cur_issues + $item->qty;
					$inv_mon_issues->create(array(
						'item_id' => $item->item_id,
						'rack_id' => $item->rack_id_to,
						'branch_id' => $user->data()->branch_id,
						'page' => 'admin/inventory_adjustments.php',
						'action' => 'Update',
						'prev_qty' => $cur_issues,
						'qty_di' => 1,
						'qty' =>  $item->qty,
						'new_qty' => $new_issues,
						'created' => time(),
						'user_id' => $user->data()->id,
						'remarks' => 'Convert surplus to '.$lbl,
						'is_active' => 1,
						'company_id' => $user->data()->company_id,
						'type' => $convert_type
					));

				}
			}

			echo "Request submitted successfully.";
		}

	}