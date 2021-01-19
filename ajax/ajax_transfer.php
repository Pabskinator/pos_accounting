<?php
	include 'ajax_connection.php';
	$functionName = Input::get("functionName");
	$functionName();

	function updateRemarks(){
		$ref = Input::get('ref');
		$id = Input::get('id');
		$trans = new Transfer_inventory_mon();
		if($id && is_numeric($id)){
			$trans->update(['remarks' => $ref],$id);
			echo "Updated successfully.";
		} else {
			echo "Invalid data supplied";
		}
	}