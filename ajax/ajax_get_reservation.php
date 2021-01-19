
<?php
	include 'ajax_connection.php';
	$cid = Input::get("cid");
	$product = new Product();
	$products = $product->getItemsAndInventoriesReserve($cid);
	$productjson = "{";
	if($products){
		foreach($products as $p) {
			$pd = new Product($p->item_id);
			$price = $pd->getPrice($p->item_id);
			if($p->item_type == 2 || $p->item_type == 3 || $p->item_type == 4 || $p->item_type == 5 ){
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
			$itemcccode = str_replace("'",'',$itemcccode);
			$descccc = str_replace("'",'',$descccc);

			$desccc = trim($descccc);
			$itemcccode = trim($itemcccode);
			$productjson.= "\"$p->barcode\":{\"id\":\"$p->item_id\",\"item_code\":\"$itemcccode\",\"description\":\"$descccc\",\"item_type\":\"$p->item_type\",\"price\":\"$price->price\",\"price_id\":\"$price->id\",\"cqty\":\"$cqty\",\"cdays\":\"$days\",\"for_freebies\":\"$p->for_freebies\"},";
		}
	}
	$productjson = rtrim($productjson,",");
	$productjson .= "}";
	echo $productjson;

