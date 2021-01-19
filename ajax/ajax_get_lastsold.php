
<?php
	include 'ajax_connection.php';


	$cid = Input::get("cid");
	$memid = Input::get("memid");
	$terminal_id = Input::get("terminal_id");
	$mem = new Member();
	$products  = $mem->getLastSold($cid,$memid);

	$productjson = '0';
	if($products){
		$productjson = "{";
		foreach($products as $p) {
			$pd = new Product($p->item_id);

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
			$price = $pd->getPrice($p->item_id);
			if($p->item_type == 2 || $p->item_type == 3 || $p->item_type == 4 ){
				$con = new Consumable();
				$pcon = $con->getConsumableByItemId($p->item_id);
				$days = $pcon->days;
				$cqty = $pcon->qty;
			} else {
				$days = -1;
				$cqty = -1;
			}

			$itemcccode = str_replace('"','',$p->item_code);
			$descccc = str_replace('"','',$p->description);
			$productjson.= "\"$p->barcode\":{\"member_id\":\"$memid\",\"id\":\"$p->item_id\",\"barcode\":\"$p->barcode\",\"item_code\":\"$itemcccode\",\"description\":\"$descccc\",\"item_type\":\"$p->item_type\",\"price\":\"$price->price\",\"price_id\":\"$price->id\",\"cqty\":\"$cqty\",\"cdays\":\"$days\"},";
		}
		$productjson = rtrim($productjson,",");
		$productjson .= "}";
	}

	echo $productjson;

