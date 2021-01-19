<?php
	include 'ajax_connection.php';

	$functionName = Input::get('functionName');
	$functionName();

	function getDetails() {
		$user = new User();
		$req_id = Input::get('id');
		$unliq = new Unliquidated();
		$list = $unliq->get_active('unliquidated',array('request_id','=',$req_id));
		$recnaba = false;
		if($list){
			echo "<table id='tbl_issues' class='table'>";
			echo "<thead><th>Barcode</th><th>Item code</th><th>Price</th><th>Quantiy</th><th>Total</th><th></th></thead>";
			echo "<tbody>";
			$totalissues =0;
			foreach($list as $l){
				$req_item = new Product($l->item_id);
				if($l->status == 2){
					$recnaba = true;
				}
				$price = $req_item->getPrice($req_item->data()->id);
				$total =  $price->price * $l->qty;
				$total = number_format($total,2);

				$totalissues += $total;
				echo "<tr data-item_id='".$l->item_id."' data-product_cost='".number_format($req_item->data()->product_cost,2)."' data-orig_cost=". number_format($price->price,2).">";
				echo "<td>". $req_item->data()->barcode . "</td>";
				echo "<td>". $req_item->data()->item_code ."</td>";
				echo "<td>". $price->price . "</td>";
				echo "<td>".$l->qty."</td>";
				echo "<td>$total</td>";
				echo "<td>";
				if($l->issues_type == 1){
					echo "<input class='allRD' type='radio' name='{$l->id}rdIssues' value='1' checked> Original Price <input class='allRD' type='radio' name='{$l->id}rdIssues' value='2'> Product cost";
				}
				echo "</td>";
				echo "</tr>";
			}
			echo "</tbody>";
			echo "</table>";
			echo "<p>Total: <span id='totalissues'>$totalissues</span></p>";
			if (!$recnaba){
				echo "<hr>";
				echo "<div class='text-right'> <input type='button' value='Receive Payment' data-req_id='$req_id' class='btn btn-default receivePayment'/></div>";
				}
			}
	}

	function receivePayment() {
		$req_id = Input::get('id');
		$unliq = new Unliquidated();
		$unliq->receivePayment($req_id);
		echo "Payment Received";
	}
?>