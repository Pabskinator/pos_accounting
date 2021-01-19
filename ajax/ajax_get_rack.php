<?php
	include 'ajax_connection.php';
	 $item_id = Input::get('item_id');
	 $branch_id = Input::get('branch_id');
	 $rack_id = Input::get('rack_id');
	$inventory = new Inventory();
	// add rack id n escape
	$racks = $inventory->getRackInventory($item_id,$branch_id,$rack_id);

	$dis ='';
	if($racks){
		foreach ($racks as $r){
			$qty = formatQuantity($r->qty,true);
			$rack_desc = ($r->description) ? "(".$r->description.")" : "";
			$dis .= "<option value='$r->id,$qty'>$r->rack"." " . $rack_desc ."</option>";
		}
	}
	
	echo $dis ;
	
	
