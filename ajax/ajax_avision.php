<?php
	include 'ajax_connection.php';
	$functionName = Input::get("functionName");

	if(function_exists($functionName)){
		$functionName();
	}

	function getRebateList(){
		$wh = new Wh_order();

		$dt_from = Input::get('dt_from');
		$dt_to = Input::get('dt_to');
		$sales_type_id= Input::get('sales_type_id');

		$data = $wh->rebateDetails($sales_type_id,$dt_from,$dt_to);
		$arr = [];

		if($data){
			foreach($data as $a){
				$a->created_at = date('m/d/Y',$a->created);
				$arr[]= $a;
			}
		}
		echo json_encode($arr);


	}

	function getRebateSummary(){
		$wh = new Wh_order();

		$year = Input::get('year');

		$year = ($year) ? trim($year) : date('Y');

		$data = $wh->rebateSummary($year);

		$arr = [];
		$types = [];
		$arr_formatted = [];
		if($data){

			foreach($data as $a){

				if(!in_array($a->sales_type_name,$types)) $types[]= $a->sales_type_name;

				$arr[$a->sales_type_name][$a->m]= $a->total_rebate;

			}

			foreach($types as $type_name ){
				for($i=1;$i<=12;$i++){
					$val = isset($arr[$type_name][$i]) ? $arr[$type_name][$i] : 0;
					$arr_formatted[$type_name][$i] = $val;
				}
			}
		}
		echo json_encode(['types' => $types,'items' => $arr_formatted]);


	}

	function getServiceMonitoring(){

		$dt_from = Input::get('dt_from');

		$dt_to = Input::get('dt_to');

		$service_type_id = Input::get('service_type_id');

		$is_done = Input::get('is_done');


			$service = new Item_service_details();

			$list = $service->getService($service_type_id,$dt_from,$dt_to,$is_done);

			if($list){
				echo "<table class='table table-bordered' id='tblForApproval'>";
				echo "<tr><th>Client</th><th>Po Number</th><TH>Item</TH><th>Type</th><th>Status</th><th>Collected Amount</th></tr>";
				$arr_types = [];
				$arr_types['101'] = 'For Claims';
				$arr_types['102'] = 'Refund';
				$total_amount = 0;
				foreach($list as $l){
					$amount_paid = "";
					if($l->amount_paid){
						$amount_paid = $l->amount_paid;
						$total_amount += $l->amount_paid;
					} else {
						$amount_paid = "Not collected";
					}
					echo "<tr>";
					echo "<td  class='text-success'>";
					echo $l->member_name;
					echo "</td>";
					echo "<td>";
					echo $l->client_po;
					echo "</td>";
					echo "<td>";
					echo $l->description;
					echo "</td>";
					echo "<td>";
					echo $l->service_type_name;
					echo "</td>";

					echo "<td>";
					echo $arr_types[$l->is_done];
					echo "</td>";
					echo "<td class='text-danger'>";
					echo "<strong>".$amount_paid."</strong>";
					echo "</td>";
					echo "</tr>";
				}
				echo "</table>";
				echo "<br><div><strong>Total: </strong>".number_format($total_amount,2)."</div>";
			} else {
				echo "<div class='alert alert-info'>No record</div>";
			}

	}

	function paymentList(){
			$user = new User();
			$page = new Pagination(new Wh_order_payment());
			$pagenum = Input::get('page');
			$pagenum = ($pagenum) ? $pagenum : 0;
			$page->setCompanyId($user->data()->company_id);
			$page->setPageNum($pagenum);
			$page->paginate();

	}
	function orderList(){
			$user = new User();
			$page = new Pagination(new Wh_po_info());
			$pagenum = Input::get('page');
			$pagenum = ($pagenum) ? $pagenum : 0;
			$page->setCompanyId($user->data()->company_id);
			$page->setPageNum($pagenum);
			$page->paginate();

	}

	function getSOA(){
		$dt_from = Input::get('dt_from');
		$dt_to = Input::get('dt_to');
		if($dt_from && $dt_to){
			$dt_from = strtotime($dt_from);
			$dt_to = strtotime($dt_to . " 1 day -1 min");

		} else {
			$day = date('w');
			 $dt_from = date('m/d/Y', strtotime('-'.$day.' days'));
			 $dt_to = date('m/d/Y', strtotime('+'.(6-$day).' days'));
			$dt_from = strtotime($dt_from);
			$dt_to = strtotime($dt_to . "1 day -1 min");
		}
		$wh = new Wh_order_payment();
		$list  = $wh->SOASummary($dt_from,$dt_to);

		$arr_group = [];
		$uniq_cols = [];
		foreach($list as $l){
			$arr_group[$l->name][] = $l;
			if(!in_array($l->name,$uniq_cols)){
				$uniq_cols[] = $l->name;
			}
		}

		$arr = [];
		$g_name_cols = "";
		$sales_revenue_cols = "";
		$revenue_from_claims_cols = "";
		$shipping_fee_cols = "";
		$shipping_fee_cus_cols = "";
		$empty_cols = "";
		$payment_fee_on_claims_cols = "";
		$fulfillment_fee_on_sales_revenue_cols = "";
		$payment_fee_on_sales_revenue_cols = "";
		$other_revenue_credited_cols = "";
		$sales_revenue_debited_returns_cols = "";

		foreach($uniq_cols as $col_name){
			$g_name_cols .= "<th>$col_name</th>";
			$current_details = $arr_group[$col_name];

			foreach($current_details as $l){
				$arr[trim($l->soa_ref)] = $l->amt;
			}

			$sales_revenue = ($arr['Sales Revenue']) ? $arr['Sales Revenue'] : 0;
			$revenue_from_claims = ($arr['Revenue from claims']) ? $arr['Revenue from claims'] : 0;
			$payment_fee_on_claims = ($arr['Payment fee on claims']) ? $arr['Payment fee on claims'] : 0;
			$fulfillment_fee_on_sales_revenue = ($arr['Fulfillment fee on sales revenue']) ? $arr['Fulfillment fee on sales revenue'] : 0;
			$payment_fee_on_sales_revenue = ($arr['Payment fee on sales revenue']) ? $arr['Payment fee on sales revenue'] : 0;
			$other_revenue_credited = ($arr['Other Revenue credited']) ? $arr['Other Revenue credited'] : 0;
			$sales_revenue_debited_returns = ($arr['Sales Revenue debited returns']) ? $arr['Sales Revenue debited returns'] : 0;

			$sales_revenue_cols .= "<td>$sales_revenue</td>";
			$revenue_from_claims_cols .= "<td>$revenue_from_claims</td>";
			$payment_fee_on_claims_cols .= "<td>$payment_fee_on_claims</td>";
			$fulfillment_fee_on_sales_revenue_cols .= "<td>$fulfillment_fee_on_sales_revenue</td>";
			$payment_fee_on_sales_revenue_cols .= "<td>$payment_fee_on_sales_revenue</td>";
			$other_revenue_credited_cols .= "<td>$other_revenue_credited</td>";
			$sales_revenue_debited_returns_cols .= "<td>$sales_revenue_debited_returns</td>";

			$empty_cols .= "<td>0</td>";
		}



		echo "<h5>Period  ". date('m/d/Y',$dt_from)."  - ".date('m/d/Y',$dt_to)."</h5>";
		echo "<table id='tblForApproval' class='table table-bordered'>";
		echo "<thead><tr><th></th><th></th>$g_name_cols</tr></thead>";
		echo "<tbody>";
		echo "<tr><td><strong>Net Revenue</strong></td><td>Sales Revenue</td>$sales_revenue_cols</tr>";
		echo "<tr><td></td><td>Revenue from claims</td>$revenue_from_claims_cols</tr>";
		echo "<tr><td></td><td>Other Revenue credited</td>$other_revenue_credited_cols</tr>";
		echo "<tr><td></td><td>Other Revenue credited(Bulky Fee & ODZ)</td>$empty_cols</tr>";
		echo "<tr><td></td><td>Sales Revenue debited returns</td>$sales_revenue_debited_returns_cols</tr>";
		echo "<tr><td><strong>Total Revenue</strong></td><td></td>$empty_cols</tr>";

		echo "<tr><td><strong>Net Commission Fee</strong></td><td>Commission on sales revenue</td>$empty_cols</tr>";
		echo "<tr><td></td><td>Commission on Claims</td>$empty_cols</tr>";
		echo "<tr><td></td><td>Commission on Returns</td>$empty_cols</tr>";
		echo "<tr><td></td><td>Commission Adjustments</td>$empty_cols</tr>";
		echo "<tr><td></td><td>Net commission (Inclusive of VAT)</td>$empty_cols</tr>";
		echo "<tr><td></td><td>Net commssion (Exclusive of VAT)</td>$empty_cols</tr>";
		echo "<tr><td></td><td>12% VAT</td>$empty_cols</tr>";


		echo "<tr><td><strong>Net Fulfillment Fee</strong></td><td>Fulfillment fee on sales revenue</td>$fulfillment_fee_on_sales_revenue_cols</tr>";
		echo "<tr><td></td><td>Fulfillment fee adjustments</td>$empty_cols</tr>";
		echo "<tr><td></td><td>Net fulfillment fee (Inclusive of VAT)</td>$empty_cols</tr>";
		echo "<tr><td></td><td>Total Fulfillment fee (Exclusive of VAT)</td>$empty_cols</tr>";
		echo "<tr><td></td><td>12% VAT</td>$empty_cols</tr>";

		echo "<tr><td><strong>Net Payment Fee</strong></td><td>Payment fee on sales revenue</td>$payment_fee_on_sales_revenue_cols</tr>";
		echo "<tr><td></td><td>Adjustment Payment Fee</td>$empty_cols</tr>";
		echo "<tr><td></td><td>Payment fee on claims</td>$payment_fee_on_claims_cols</tr>";
		echo "<tr><td></td><td>Net payment fee (Inclusive of VAT)</td>$empty_cols</tr>";
		echo "<tr><td></td><td>Total payment fee (Exclusive of VAT)</td>$empty_cols</tr>";
		echo "<tr><td></td><td>12% VAT</td>$empty_cols</tr>";

		echo "<tr><td><strong>Other Seller fee</strong></td><td>Other fee</td>$empty_cols</tr>";
		echo "<tr><td></td><td>Other fee adjustment</td>$empty_cols</tr>";
		echo "<tr><td></td><td>Net Other fee (inclusive of VAT)</td>$empty_cols</tr>";
		echo "<tr><td></td><td>Other fee (Exclusive of VAT)</td>$empty_cols</tr>";
		echo "<tr><td></td><td>12% VAT</td>$empty_cols</tr>";



		echo "<tr><td><strong>Total Payment</strong></td><td> EWT 516(15%)</td>$empty_cols</tr>";
		echo "<tr><td></td><td>EWT 120 (2%)</td>$empty_cols</tr>";


		echo "</tbody>";
		echo "</table>";

	}

	function getPendingBatch(){
		$batch = new Wh_order_batch();
		$dt_from = Input::get('dt_from');
		$dt_to = Input::get('dt_to');
		$sales_type_name = Input::get('sales_type_name');
		$status = Input::get('status');

		$pending = $batch->getPending(0,$dt_from,$dt_to,$sales_type_name,$status);

		$arr = [];
		if($pending){
			foreach($pending as $item){
				$item->store_type_name = $item->store_type;
				$arr[] = $item;
			}
		}

		echo json_encode($arr);

	}
	function getLogBatch(){
		$batch = new Wh_order_batch();
		$dt_from = Input::get('dt_from');
		$dt_to = Input::get('dt_to');
		$sales_type_name = Input::get('sales_type_name');
		$pending = $batch->getPending(6,$dt_from,$dt_to,$sales_type_name);

		$arr = [];
		if($pending){
			foreach($pending as $item){
				$item->store_type_name = $item->store_type;
				$arr[] = $item;
			}
		}

		echo json_encode($arr);

	}
	function displayItems(){
		$batch_id = Input::get('batch_id');

		$data = [];
		if($batch_id && is_numeric($batch_id)){
			$batch = new Wh_order_batch($batch_id);


				$items = $batch->getItems($batch_id);


				foreach($items as $r){

					$type = $r->item_type == -1 ? 1 :0;
					if(isset($data[$r->order_id])){
						$data[$r->order_id]['items'][] = [
							'item_id' => $r->item_id,
							'qty' => $r->qty,
							'item_code' => $r->item_code,
							'description' => $r->description,
							'with_inventory' => $type,
							'racks' =>[]
						];
					} else {
						$data[$r->order_id] = [
							'order_id' => $r->order_id,
							'client_po' => $r->client_po,
							'client_name' => $r->member_name,
							'items' => [
								[
									'item_id' => $r->item_id,
									'qty' => $r->qty,
									'item_code' => $r->item_code,
									'description' => $r->description,
									'with_inventory' => $type,
									'racks' =>[]
								]
							]
						];
					}

				}
			generateTable($data,$batch_id);
		}

	}

	function generateTable($data,$batch_id){
		foreach($data  as $item){
			$client_po = $item['client_po'] ? $item['client_po']  :'N/A' ;

			echo "<h5>PO Number: <strong>$client_po</strong> Order ID: $item[order_id]</h5>";
			echo "<div class='row'>";
			echo "<div class='col-md-6'><p>Client: <strong>" .ucwords($item['client_name']) . "</strong></p></div>";
			echo "<div class='col-md-6 text-right'><button data-batch_id='$batch_id' data-id='$item[order_id]'  class='btn btn-danger btn-sm btnDecline'> Cancel</button></div>";
			echo "</div>";
			$items = $item['items'];
			echo "<table class='table table-bordered'>";
			foreach($items as $i){
				echo "<tr><td>$i[item_code]</td><td>$i[description]</td><td>$i[qty]</td></tr>";
			}
			echo "</table>";
		}
	}

	function getStocks(){
		$batch_id = Input::get('batch_id');
		$rack_to_deduct = 3;
		$data = [];
		if($batch_id && is_numeric($batch_id)){
			$batch = new Wh_order_batch($batch_id);

			if(isset($batch->data()->id) && $batch->data()->status == 1) {
				$racking = $batch->getItems($batch_id);

				$rack = new Rack($rack_to_deduct);
				foreach($racking as $r){

					$type = $r->item_type == -1 ? 1 :0;
					if(isset($data[$r->order_id])){
						$data[$r->order_id]['items'][] = [
							'item_id' => $r->item_id,
							'qty' => formatQuantity($r->qty,true),
							'item_code' => $r->item_code,
							'description' => $r->description,
							'with_inventory' => $type,
							'racks' =>[['rack' => $rack->data()->rack,'qty' => formatQuantity($r->qty,true)]]
						];
					} else {
						$data[$r->order_id] = [
							'order_id' => $r->order_id,
							'client_name' => $r->member_name,
							'client_po' => $r->client_po,
							'items' => [
								[
									'item_id' => $r->item_id,
									'qty' => formatQuantity($r->qty,true),
									'item_code' => $r->item_code,
									'description' => $r->description,
									'with_inventory' => $type,
									'racks' =>[['rack' => $rack->data()->rack,'qty' =>formatQuantity($r->qty,true)]]
								]
							]
						];
					}

				}

				$formatted = [];
				foreach($data as $k => $items){
					$formatted[] = $items;
				}
				$data = $formatted;

			}
		}
		echo json_encode($data);
	}

	function getSerials(){
		$batch_id = Input::get('batch_id');

		$data = [];
		if($batch_id && is_numeric($batch_id)){
			$batch = new Wh_order_batch($batch_id);

			if(isset($batch->data()->id) && $batch->data()->status == 2) {

				$items = $batch->getItems($batch_id);

				foreach($items as $r){


					if(isset($data[$r->order_id])){
						$data[$r->order_id]['items'][] = [
							'item_id' => $r->item_id,
							'qty' => formatQuantity($r->qty,true),
							'item_code' => $r->item_code,
							'description' => $r->description,
							'with_serial' => $r->has_serial,
							'serials' =>[]
						];
					} else {
						$data[$r->order_id] = [
							'order_id' => $r->order_id,
							'client_po' => $r->client_po,
							'payment_id' => $r->payment_id,
							'client_name' => $r->member_name,
							'items' => [
								[
									'item_id' => $r->item_id,
									'qty' => formatQuantity($r->qty,true),
									'item_code' => $r->item_code,
									'description' => $r->description,
									'with_serial' => $r->has_serial,
									'serials' =>[]
								]
							]
						];
					}

				}

				$formatted = [];
				foreach($data as $k => $items){
					$formatted[] = $items;
				}
				$data = $formatted;
			}
		}
		echo json_encode($data);
	}

	function deductStocks(){
		$batch_id = Input::get('batch_id');
		$rack_to_deduct = 3;
		$status = 2;
		if($batch_id && is_numeric($batch_id)) {
			$batch = new Wh_order_batch($batch_id);

			if(isset($batch->data()->id) && $batch->data()->status == 1) {
				$racking = $batch->getItems($batch_id);
				$inventory = new Inventory();
				$inv_mon = new Inventory_monitoring();
				$user = new User();
				$rackcls = new Rack($rack_to_deduct);
				$whOrderDetails = new Wh_order_details();
				$order_ids = [];
				$with_serial = false;
				foreach($racking as $item){
					if($item->has_serial==1){
						$with_serial = true;
					}
					if(!in_array($item->order_id,$order_ids)){
						$order_ids[] = $item->order_id;
					}
					$rackjson = [];
					if($item->item_type == -1){

						$rackjson = [['rack' => $rackcls->data()->rack,'rack_description' => '', 'rack_id' =>$rackcls->data()->id,'stock_man' => '', 'qty'=>$item->qty]];

						// check if item exists in rack
						if($inventory->checkIfItemExist($item->item_id,$item->branch_id,$user->data()->company_id,$rack_to_deduct)){
							$curinventoryFrom = $inventory->getQty($item->item_id,$item->branch_id,$rack_to_deduct);
							$currentqty = $curinventoryFrom->qty;
							$inventory->subtractInventory($item->item_id,$item->branch_id,$item->qty,$rack_to_deduct);
						} else {
							$currentqty = 0;
						}

						// monitoring
						$newqtyFrom = $currentqty - $item->qty;
						$arr_mon = array(
							'item_id' => $item->item_id,
							'rack_id' => $rack_to_deduct,
							'branch_id' => $item->branch_id,
							'page' => 'ajax/ajax_query2.php',
							'action' => 'Update',
							'prev_qty' => $currentqty,
							'qty_di' => 2,
							'qty' => $item->qty,
							'new_qty' => $newqtyFrom,
							'created' => time(),
							'user_id' => $user->data()->id,
							'remarks' => 'Deduct inventory from rack (Order id #'.$item->order_id.')',
							'is_active' => 1,
							'company_id' => $user->data()->company_id
						);
						$inv_mon->create($arr_mon);

					}
					if($item->details_id && is_numeric($item->details_id)){

						$whOrderDetails->update(array('racking'=> json_encode($rackjson)),$item->details_id);
					}

				}

				// orders
				$myOrder = new Wh_order();
				foreach($order_ids as $order_id){
					if($order_id && is_numeric($order_id)){

						$myOrder->update(array('stock_out' => 1),$order_id);
					}
				}

				if(!$with_serial){ // if no item with serial on request, derecho print dr
					$status = 3;
				}
			    $batch->update(['status'=>$status],$batch_id);
			}
		}
		echo $status;
	}


	function saveSerials(){
		$orders = Input::get('items');
		$batch_id = Input::get('batch_id');
		$user = new User();

		if($orders){
			$orders = json_decode($orders,true);
			$valid = true;
			foreach($orders as $order){
				$items = $order['items'];
				foreach($items as $item){
					$serials = $item['serials'];
					$with_serial = $item['with_serial'];
					if((int) $item['qty'] != count($serials) && $with_serial == '1'){
						$valid = false;
					}
				}
			}
			if($valid){
				$serial_cls= new Serial();
				$now = time();
				foreach($orders as $order){
					$items = $order['items'];
					foreach($items as $item){
						$serials = $item['serials'];
						foreach($serials as $serial){
							$checker = $serial_cls->checkIfExists($order['payment_id'],$item['item_id'],$serial);
							if(isset($checker->cnt) && $checker->cnt > 0){
								// exists
							} else {
								$serial_cls->create(
									[
										'payment_id' => $order['payment_id'],
										'item_id' => $item['item_id'],
										'serial_no' => $serial,
										'created' => $now,
										'modified' => $now,
										'user_id' => $user->data()->id,
										'company_id' => $user->data()->company_id,
										'is_active' => 1,
									]
								);
							}
						}
					}
				}
				$b = new Wh_order_batch();
				$b->update(
					['status' => 3] , $batch_id
				);

			}

		}
		echo 1;
	}


	function prepareDR(){

		$batch_id = Input::get('batch_id');
		$terminal_id = Input::get('terminal_id');
		$dr = Input::get('dr');
		$data = [];
		$print = Input::get('print');

		if($batch_id && is_numeric($batch_id)) {
			$batch = new Wh_order_batch($batch_id);
			$orders = [];
			$payment_ids = [];
			$order_ids = [];
			if(isset($batch->data()->id) && $batch->data()->status == 3) {
				$items = $batch->getItems($batch_id);
				foreach($items as $item){
					if(!in_array($item->payment_id,$payment_ids)){
						$payment_ids[] = $item->payment_id;
					}
					if(!in_array($item->order_id,$order_ids)){
						$order_ids[] = $item->order_id;
					}
					$orders[$item->order_id][] = $item;
				}

			}


			$payment_id_split = implode(',',$payment_ids);
			$serial = new Serial;
			$serial_list = $serial->getSerialIn($payment_id_split);
			$serial_data = [];
			if($serial_list){
				foreach($serial_list as $sl){
					$serial_data[$sl->payment_id][$sl->item_id][] = $sl->serial_no;
				}
			}





			$main_info=[];
			$item_info=[];
			foreach($orders as $order_id => $records){
				foreach($records as $record){
					if($batch->data()->has_dr){
						$dr = $record->dr;
					} else {
						$dr++;
					}
					if(!isset($main_info[$order_id])){

						$main_info[$order_id] = [
							'order_id' => $order_id,
							'member_name' => $record->member_name,
							'client_po' => $record->client_po,
							'personal_address' => $record->personal_address,
							'rebate' => $record->rebate,
							'sales_type_name' => $batch->data()->store_type,
							'dr' => $dr
						];

					}
					$price = $record->price;
					if($record->member_adjustment){
						$ind_adj = $record->member_adjustment / $record->qty;
						$price += $ind_adj;
					}
					$serials = [];
					if(isset($serial_data[$record->payment_id][$record->item_id])){
						$serials = $serial_data[$record->payment_id][$record->item_id];
					}
					$item_info[$order_id][] = [
						'item_id' => $record->item_id,
						'item_code' => $record->item_code,
						'price_id' => $record->price_id,
						'sales_type_id' => $record->salestype,
						'member_adjustment' =>  $record->member_adjustment,
						'description' => $record->description,
						'qty' => formatQuantity($record->qty,true),
						'price' => $price,
						'total' => $price * $record->qty,
						'serials' => $serials,
					];
				}
			}
			$final = [];
			foreach($order_ids as $id){

				$main_info[$id]['item_list'] = $item_info[$id];
				$final[] = $main_info[$id];
			}

			if($print == 1){
				if($final && !$batch->data()->has_dr) {

					insertSales($final, $terminal_id);

					$batch->update(
						['has_dr' => 1] , $batch->data()->id
					);

				}
			}

			echo json_encode($final);

		}
	}

	function insertSales($orders,$terminal_id){

			$user = new User();
			$lastDr= 0;
			foreach($orders as $o){

				$order_id = $o['order_id'];
				$items = $o['item_list'];
				$dr = $o['dr'];

				if($dr > $lastDr){
					$lastDr = $dr;
				}
				$wh = new Wh_order($order_id);
				$wh->update(
					['dr' => $dr] , $order_id
				);
				$payment_id = $wh->data()->payment_id;

				$now = time();

				foreach($items as $item){
					$item_id = $item['item_id'];
					$price_id = $item['price_id'];
					$member_adjustmemt = $item['member_adjustment'];
					$qty = $item['qty'];
					$sales_type_id = $item['sales_type_id'];
					$sales_type_id = $sales_type_id ? $sales_type_id : 0;

						$newsales = new Sales();
						$newsales->create(array(
							'terminal_id' => $terminal_id,
							'invoice' => 0,
							'sv' => 0,
							'dr' => $dr,
							'ir' => 0,
							'sr2' => 0,
							'ts' => '',
							'pref_inv' => '',
							'pref_dr' => '',
							'pref_ir' => '',
							'pref_sv' => '',
							'suf_inv' => '',
							'suf_dr' => '',
							'suf_ir' => '',
							'suf_sv' => '',
							'item_id' => $item_id,
							'price_id' => $price_id,
							'qtys' =>  $qty,
							'discount' => 0,
							'store_discount' => 0,
							'adjustment' => 0,
							'member_adjustment' => $member_adjustmemt,
							'terms' =>0,
							'company_id' => $user->data()->company_id,
							'cashier_id' => $user->data()->id,
							'sold_date' => $now,
							'payment_id' =>$payment_id,
							'member_id' => $wh->data()->member_id,
							'warranty' => 24,
							'station_id' => 0,
							'sales_type' =>$sales_type_id,
						));


				}
		}
		$terminal = new Terminal();
		$terminal->update(['dr' => $lastDr],$terminal_id);
	}

	function changeStatus(){

		$batch_id = Input::get('batch_id');
		$from_status = Input::get('from_status');
		$to_status = Input::get('to_status');

		if($batch_id && is_numeric($batch_id)) {

			$batch = new Wh_order_batch($batch_id);

			if(isset($batch->data()->id) && $batch->data()->status == $from_status) {
				$batch->update(
					['status' => $to_status] , $batch->data()->id
				);
			}
		}

	}

	function printManifest(){
		$batch_id = Input::get('batch_id');
		$arr = [];
		if($batch_id && is_numeric($batch_id)) {

			$batch = new Wh_order_batch($batch_id);
			$type_name = $batch->data()->store_type;

			if(isset($batch->data()->id) && $batch->data()->status == 4) {
				$ids = $batch->getOrders($batch_id);
				foreach($ids as $id){
					$arr[] = $id->client_po;
				}
			}

		}

		echo json_encode(['ids' =>$arr,'type_name'=>$type_name]);
	}

	function shipOut(){
		$batch_id = Input::get('batch_id');
		$dt = Input::get('dt');

		if($batch_id && is_numeric($batch_id)) {

			$batch = new Wh_order_batch($batch_id);
			$user = new User();

			if(isset($batch->data()->id) && $batch->data()->status == 5) {

				$ids = $batch->getOrders($batch_id);

				$scheduleOrder = new Wh_order_date();

				$schedule_date = strtotime($dt);

				$now = time();

				$myOrder = new Wh_order();

				foreach($ids as $order){

					$order_id = $order->id;

					$scheduleOrder->create(array(
						'company_id' => $user->data()->company_id,
						'user_id' => $user->data()->id,
						'created' =>$now,
						'modified' =>$now,
						'is_active' =>1,
						'schedule_date' =>$schedule_date,
						'wh_order_id' =>$order_id
					));

					$myOrder->update(array('is_scheduled' => $schedule_date,'status'=>4), $order_id);

					Log::addLog($user->data()->id, $user->data()->company_id,"Schedule Order ID $order_id","ajax_avision.php");


				}

				$batch->update(
					['status' => 6] , $batch_id
				);
			echo 1;
			}

		}


	}

	function cancelOrder(){
		$order_id = Input::get('order_id');


		if($order_id){

			$whorder = new Wh_order($order_id);



			$whorder->update(array(
				'status' => 5
			),$order_id);

			if($whorder->data()->payment_id){
				$sales = new Sales();
				$sales->cancelPayment($whorder->data()->payment_id);
			}

			$user = new User();

			Log::addLog(
				$user->data()->id,
				$user->data()->company_id,
				"Decline/Cancel Order Request Order ID=".$order_id." PID=".$whorder->data()->payment_id,
				'ajax_accounting.php'
			);

			// return inventory

			$wh = new Wh_order_details();

			$items= $wh->getOrderDetails($order_id);

			if($items){
				$inventory = new Inventory();
				$inv_mon = new Inventory_monitoring();
				$rack_to_deduct = 3;
				$branch_id = $whorder->data()->branch_id;


				foreach($items as $item){
					$item_id = $item->item_id;
					$qty = $item->qty;

					// check if item exists in rack
					if($inventory->checkIfItemExist($item_id,$branch_id,$user->data()->company_id,$rack_to_deduct)){
						$curinventoryFrom = $inventory->getQty($item_id,$branch_id,$rack_to_deduct);
						$currentqty = $curinventoryFrom->qty;
						$inventory->addInventory($item_id,$branch_id,$qty,false,$rack_to_deduct);
					} else {
						$currentqty = 0;
					}

					// monitoring
					$newqtyFrom = $currentqty + $qty;
					$arr_mon = array(
						'item_id' => $item_id,
						'rack_id' => $rack_to_deduct,
						'branch_id' => $branch_id,
						'page' => 'ajax/ajax_query2.php',
						'action' => 'Update',
						'prev_qty' => $currentqty,
						'qty_di' => 1,
						'qty' => $qty,
						'new_qty' => $newqtyFrom,
						'created' => time(),
						'user_id' => $user->data()->id,
						'remarks' => 'Add inventory to rack (Order id #'.$order_id.')',
						'is_active' => 1,
						'company_id' => $user->data()->company_id
					);
					$inv_mon->create($arr_mon);

				}
			}

		}
	}