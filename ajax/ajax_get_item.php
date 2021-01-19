
	<?php
	include 'ajax_connection.php';
	error_reporting(0);
	$cid = Input::get("cid");
	$branch = Input::get("branch");
	$terminal_id = Input::get("terminal");
	$product = new Product();
	$products = $product->getItemsAndInventories($branch,$cid);
	$rack = new Rack();
	$rackDefault = $rack->getRackForSelling($branch);
	$productjson = "{";
	if($products){
	$discount = new Discount();
		$arr_list = [];

	foreach($products as $p) {
		if(in_array($p->item_id,$arr_list))
		{
			continue;
		}
		$arr_list[] = $p->item_id;
		//$pd = new Product($p->item_id);
		$pricefinal = $p->price;
		$price_id = $p->price_id;
		if($p->product_terminals){
			if(strpos($p->product_terminals,",")){
				$product_terminals = explode(',',$p->product_terminals);
				if(!in_array($terminal_id,$product_terminals)){
					continue;
				}
			} else {

				if($p->product_terminals != $terminal_id){
					continue;
				}
			}
		}

		$indDiscount = $discount->getDiscount($p->item_id,$branch);
		$discountJSON = [];
		if($indDiscount){
			foreach($indDiscount as $dd){
				$disind['id'] = $dd->id;
				$disind['amount'] = $dd->amount;
				$disind['for_qty'] = $dd->for_every;
				$disind['type'] = $dd->type;
				$discountJSON[] = $disind;
			}
		}

		$discountJSON = addslashes(json_encode($discountJSON));

		if($p->item_type == 2 || $p->item_type == 3 || $p->item_type == 4 || $p->item_type == 5 ){
			$con = new Consumable();
			$pcon = $con->getConsumableByItemId($p->item_id);
			$days = $pcon->days;
			$cqty = $pcon->qty;
		} else {
			$days = -1;
			$cqty = -1;
			if($p->item_type != 1) {
				if(isset($rackDefault->rack) && !empty($rackDefault->rack)){
					$defrack = $rackDefault->rack;
				} else {
					$defrack = 'Display';
				}
				if($p->rack != $defrack) continue;
			}
		}
		$itemcccode = str_replace('"','',$p->item_code);
		$descccc = str_replace('"','',$p->description);
		$itemcccode = str_replace("'",'',$itemcccode);
		$descccc = str_replace("'",'',$descccc);
		$descccc = trim($descccc);
		$itemcccode = trim($itemcccode);
		$finalInv = formatQuantity($p->qty,true);
		$price_adjustment = ($p->adjustment) ? $p->adjustment : 0;
		$pricefinal = $pricefinal + $price_adjustment;
		$productjson.= "\"$p->barcode\":{\"id\":\"$p->item_id\",\"is_decimal\":\"$p->is_decimal\",\"warranty\":\"$p->warranty\",\"price_adjustment\":\"$price_adjustment\",\"discountJSON\":\"$discountJSON\",\"unit_name\":\"$p->unit_name\",\"item_code\":\"$itemcccode\",\"description\":\"$descccc\",\"item_type\":\"$p->item_type\",\"price\":\"$pricefinal\",\"price_id\":\"$price_id\",\"inventory_id\":\"$p->inventory_id\",\"qty\":\"$finalInv\",\"branch_id\":\"$p->branch_id\",\"cqty\":\"$cqty\",\"cdays\":\"$days\",\"for_freebies\":\"$p->for_freebies\"},";
		}
	}
	$productjson = rtrim($productjson,",");
	$productjson .= "}";
	echo $productjson;

