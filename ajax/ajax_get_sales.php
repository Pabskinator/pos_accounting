
<?php
	include 'ajax_connection.php';


	$branch = Input::get("branch_id");
	$company = Input::get("company_id");
	$terminal = Input::get("terminal_id");
	$sales = new Sales();
	$saleslist = $sales->getSales($branch,$company,$terminal);
	if($saleslist){
	$salesjson = "{";

	foreach($saleslist as $s) {
		$datesold = date('M d, Y',$s->sold_date);
		$itemcccode = str_replace('"','',$s->item_code);
		$descccc = str_replace('"','',$s->description);
		$saleqty = formatQuantity($s->qtys,true);
		$salesjson.= "\"_$s->sales_id\":{\"sales_id\":\"$s->sales_id\",\"sold_date\":\"$datesold\",\"payment_id\":\"$s->payment_id\",\"company_name\":\"$s->company_name\",\"terminal_name\":\"$s->terminal_name\",\"branch_name\":\"$s->branch_name\",\"item_code\":\"$itemcccode\",\"description\":\"$descccc\",\"barcode\":\"$s->barcode\",\"price\":\"$s->price\",\"lastname\":\"$s->lastname\",\"firstname\":\"$s->firstname\",\"middlename\":\"$s->middlename\",\"mln\":\"$s->mln\",\"mfn\":\"$s->mfn\",\"mmn\":\"$s->mmn\",\"sales_id\":\"$s->sales_id\",\"invoice\":\"$s->invoice\",\"dr\":\"$s->dr\",\"ir\":\"$s->ir\",\"qtys\":\"$saleqty\",\"discount\":\"$s->discount\",\"store_discount\":\"$s->store_discount\",\"adjustment\":\"$s->adjustment\",\"station\":\"$s->station_name\",\"status\":\"$s->status\"},";
	}
	$salesjson = rtrim($salesjson,",");
	$salesjson .= "}";
	echo $salesjson;
	}

