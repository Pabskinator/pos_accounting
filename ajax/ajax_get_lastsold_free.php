
<?php
	include 'ajax_connection.php';


	$cid = Input::get("cid");
	$memid = Input::get("memid");
	$terminal_id = Input::get("terminal_id");
	$mem = new Member();
	$products  = $mem->getLastSoldFree($cid,$memid);

	$productjson = '0';
	if($products){
		$productjson = "{";
		foreach($products as $p) {
			$pd = new Product($p->item_id);
			$price = $pd->getPrice($p->item_id);
			$datesold = date('m/d/Y',$p->sold_date);

			$itemcccode = str_replace('"','',$p->item_code);
			$descccc = str_replace('"','',$p->description);
			$productjson.= "\"$p->barcode\":{\"member_id\":\"$memid\",\"id\":\"$p->item_id\",\"barcode\":\"$p->barcode\",\"qtys\":\"$p->qtys\",\"discount\":\"$p->discount\",\"item_code\":\"$itemcccode\",\"description\":\"$descccc\",\"price\":\"$price->price\",\"date_sold\":\"$datesold\"},";
		}
		$productjson = rtrim($productjson,",");
		$productjson .= "}";
	}
	echo $productjson;

