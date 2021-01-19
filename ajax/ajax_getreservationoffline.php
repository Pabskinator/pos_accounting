
<?php
	include 'ajax_connection.php';


	$branch = Input::get("branch_id");
	$company = Input::get("company_id");

	$order = new Order();
	$orderlist = $order->getOrderForOffline($branch,$company);
	if($orderlist){
	$orderlistjson = "{";
	$pr = new Product();

	foreach($orderlist as $s) {
		$details = $order->getOrderDetails($s->id);
		$fullname = ucfirst($s->lastname . ", " .$s->firstname . " " . $s->middlename);
		$membername = ucfirst($s->mln . ", " .$s->mfn);
		$stationarr = array();
		if($s->station_id){
			if(!in_array($s->station_id,$stationarr)){
				$indstation = new Station($s->station_id);
				$indstation->data()->name = str_replace('"','',$indstation->data()->name);
				$indstation->data()->name = str_replace("'",'',$indstation->data()->name);
				$stationarr[] = $indstation->data()->name;
			}
		}
		$dateordered = date('m/d/Y H:i:s A',$s->created);
		$arrayitem = array();
		if($details){
		foreach($details as $dt){
			$itemcccode = str_replace('"','',$dt->item_code);
			$descccc = str_replace('"','',$dt->description);
			$itemcccode = str_replace("'",'',$dt->item_code);
			$descccc = str_replace("'",'',$dt->description);
			$obj['item_id'] = $dt->item_id;
			$obj['item_code'] = $itemcccode;
			$obj['barcode'] = $dt->barcode;
			$obj['discount'] = $dt->discount;
			$obj['description'] =$descccc;
			$obj['qty']= formatQuantity($dt->qty,true);
			$obj['price_adjustment']= $dt->price_adjustment;
			$price = $pr->getPrice($dt->item_id);
			$obj['price']=$price->price + $dt->price_adjustment;
			$obj['total']=($price->price + $dt->price_adjustment) * $dt->qty;
			$obj['ss_json']= $dt->ss_json;
			$obj['branch_json']= $dt->branch_json;
			$obj['item_type']= $dt->item_type;
			array_push($arrayitem,$obj);
			if(isset($dt->ss_json) && !empty($dt->ss_json)){
				$decodedjson= json_decode($dt->ss_json);
				foreach($decodedjson as $decoded){
					if($decoded->stationid){
						if(!in_array($decoded->stationid,$stationarr)){
							$indstation = new Station($decoded->stationid);
							$indstation->data()->name = str_replace('"','',$indstation->data()->name);
							$indstation->data()->name = str_replace("'",'',$indstation->data()->name);
							$stationarr[] = $indstation->data()->name;
						}
					}
				}
			}

		}
			$retstation ='';
			if($stationarr){
				foreach($stationarr as $eachstation){
					$retstation .= $eachstation . "::";
				}
				$retstation = rtrim($retstation,"::");
			}

		$decodearr = addslashes($retstation);
		$arrayitem = addslashes(json_encode($arrayitem));
		$s->payment_cheque = addslashes($s->payment_cheque);
		$s->payment_cash = addslashes($s->payment_cash);
		$s->payment_credit_card = addslashes($s->payment_credit_card);
		$s->payment_bt = addslashes($s->payment_bt);
		$s->payment_consumable = addslashes($s->payment_consumable);
		$s->payment_consumable_freebies = addslashes($s->payment_consumable_freebies);
		$s->payment_member_credit = addslashes($s->payment_member_credit);
		$s->remarks = str_replace('"','',$s->remarks);
		$s->remarks = str_replace("'",'',$s->remarks);
		$s->remarks = addslashes($s->remarks);
		$orderlistjson.= "\"_$s->id\":{\"order_id\":\"$s->id\",\"user_id\":\"$s->user_id\",\"payment_member_credit\":\"$s->payment_member_credit\",\"payment_consumable_freebies\":\"$s->payment_consumable_freebies\",\"payment_consumable\":\"$s->payment_consumable\",\"payment_credit_card\":\"$s->payment_credit_card\",\"payment_cheque\":\"$s->payment_cheque\",\"payment_bt\":\"$s->payment_bt\",\"payment_cash\":\"$s->payment_cash\",\"branch_id\":\"$s->branch_id\",\"src_branch\":\"$s->src_branch\",\"sales_type\":\"$s->sales_type\",\"stations\":\"$decodearr\",\"member_id\":\"$s->member_id\",\"member_name\":\"$membername\",\"station_id\":\"$s->station_id\",\"fullname\":\"$fullname\",\"date_ordered\":\"$dateordered\",\"remarks\":\"$s->remarks\",\"jsonitem\":\"$arrayitem\"},";
	}
	}
	$orderlistjson = rtrim($orderlistjson,",");
	$orderlistjson .= "}";

	echo $orderlistjson;
	} else {
		echo "0";
	}

