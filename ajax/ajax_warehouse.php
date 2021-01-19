<?php
	include 'ajax_connection.php';
	$functionName = Input::get("functionName");
	$functionName();

	function getWHAmount(){
		$type  = 1;
		$user = new User();
		if($type == 1){
			$branch_id = $user->data()->branch_id;
			$cid = $user->data()->company_id;
			$inventory = new Inventory();
			$totalAmount = $inventory->getTotalWhAmount($branch_id,$cid);
			$arr = [];

			if($totalAmount){
				$tablestring = "<div class='list-group'>";
				$tablestring .= " <a href='#' class='list-group-item active'></a>";

				foreach($totalAmount as $bb){
					if (!$bb->branch_name) continue;
					$obj['label'] = $bb->branch_name;
					$obj['value']= $bb->totalAmount;
					$arr['donut'][] = $obj;
					$tablestring .="<a href='#' class='list-group-item'>".$bb->branch_name."<span class='pull-right text-danger'>".number_format($bb->totalAmount,2)."</span></a>";
				}
				$tablestring .="</div>";
				$arr['label'] = $tablestring;
			}else {
				$arr = [];
				$arr['error'] = true;

			}
			echo json_encode($arr);

		}
	}
	function getIssuesAmount(){
		$type  = 1;
		$user = new User();
		if($type == 1){
			$status = Input::get('s');
			$branch_id = $user->data()->branch_id;
			$cid = $user->data()->company_id;
			$inventory = new Inventory_issue();
			$totalAmount = $inventory->getTotalIssuesAmount($branch_id,$cid,$status);
			$arr = [];

			if($totalAmount){
				$tablestring = "<div class='list-group'>";
				$tablestring .= " <a href='#' class='list-group-item active'></a>";

				foreach($totalAmount as $bb){
					if (!$bb->branch_name) continue;
					$obj['label'] = $bb->branch_name;
					$obj['value']= $bb->totalAmount;
					$arr['donut'][] = $obj;
					$tablestring .="<a href='#' class='list-group-item'>".$bb->branch_name."<span class='pull-right text-danger'>".number_format($bb->totalAmount,2)."</span></a>";
				}
				$tablestring .="</div>";
				$arr['label'] = $tablestring;
			} else {
				$arr = [];
				$arr['error'] = true;

			}
			echo json_encode($arr);

		}
	}
	function  stopAuditRack(){
	$rack = Input::get('rack_id');
	$audit_id =Input::get('audit_id');
	$rackcls = new Rack_audit_sp($audit_id);
	$update_rack = new Rack_audit_sp();
	$update_rack->update(array(
		'status' => 2,
		'items' => $rackcls->data()->item_check,
		'percent' => '100.00%'
	),$audit_id);


	echo "Updated successfully";
}
	function continueAuditRack(){
		$rack =  (int) Input::get('rack_id');
		$audit_id =  (int)Input::get('audit_id');
		$countnoamend = (int) Input::get('countnoamend');
		$rackcls = new Rack_audit_sp($audit_id);

		$items= $rackcls->data()->items;
		$items = $items + $countnoamend;
		$item_check =  $rackcls->data()->item_check;
		$percent = ($item_check/$items) *  100;
		$percent = number_format($percent,2);
		if($percent == 100.00){
			echo "Unable to process. No item to ammend";
			exit();
		}
		$rack_update = new Rack_audit_sp();
		$rack_update->update(array(
			'status' => 1,
			'items' => $items,
			'percent' => $percent . '%'
		),$audit_id);
		echo "Updated successfully";

	}
	function printWarehousePending() {
		$filename = "warehouse-orders-" . date('m-d-Y-h-i-s') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");
		$user = new User();
		$wh_orders = new Wh_order();
		$branch_id = 0;
		$orders = $wh_orders->get_record($user->data()->company_id,0,1000,'',$branch_id,0,3,0,0,0);
		if($orders){
			echo "<table border=1>";
			echo "<tr><th>Order Id</th><th>From</th><th>To</th><th>Invoice</th><th>Dr</th><th>Pr</th></tr>";
			foreach($orders as $order){
				$to = "";
				if($order->mln){
					$to = $order->mln;
				} else {
					$to = $order->to_branch_name;
				}
				$from = $order->branch_name;
				$invoice = $order->invoice;
				$dr = $order->dr;
				$pr = $order->pr;
				$colspan = 6;
				echo "<tr><th>$order->id</th><th>$from</th><th>$to</th><th>$invoice</th><th>$dr</th><th>$pr</th></tr>";
				$wh_details = new Wh_order_details();
				$details = $wh_details->getOrderDetails($order->id);
				if($details){
					echo "<tr><th>Item Code</th><th>Description</th><th>Quantity</th><th></th><th></th><th></th></tr>";
					foreach($details as $det){

						echo "<tr><td>$det->item_code</td><td>$det->description</td><td>$det->qty</td><td></td><td></td><td></td></tr>";
					}
				}
				echo "<tr><th colspan='$colspan'>&nbsp;</th></tr>";
			}
			echo "</table>";
		}
	}