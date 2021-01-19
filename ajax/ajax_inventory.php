<?php
	include 'ajax_connection.php';

	$functionName = Input::get("functionName");

	$user = new User();
	$company_id = $user->data()->company_id;
	if(function_exists($functionName) && $company_id) {
		$functionName($company_id);
	}

	function addReleasing() {
		$release = new Releasing();

		$item_id = Input::get('item_id');
		$qty = Input::get('qty');
		$payment_id = Input::get('payment_id');
		$data = $release->getSingle($payment_id);
		$user = new User();
		if($item_id && $qty && is_numeric($item_id) && is_numeric($qty) && $data) {

			$forrelease = new Releasing();
			Log::addLog($user->data()->id, $user->data()->company_id, "Add releasing item " . $payment_id, 'ajax_inventory.php');
			$forrelease->create(array('item_id' => $item_id, 'payment_id' => $payment_id, 'qty' => $qty, 'is_active' => 1, 'status' => 1, 'company_id' => $user->data()->company_id, 'user_id' => $user->data()->id, 'terminal_id' => $data->terminal_id, 'created' => time()));
			echo "Item Added successfully.";
		} else {
			echo "Invalid request.";
		}

	}

	function deleteReleasing() {
		$id = Input::get('id');
		if($id && is_numeric($id)) {
			$release = new Releasing($id);
			$user = new User();
			$payment_id = $release->data()->payment_id;
			Log::addLog($user->data()->id, $user->data()->company_id, "Delete releasing item " . $payment_id, 'ajax_inventory.php');
			$release->deleteReleasing($id);
			echo "Deleted successfully.";
		} else {
			echo "Invalid request.";
		}

	}

	function showItemRackData() {
		$branch_id = Input::get('branch_id');
		$item_id = Input::get('item_id');
		$inv = new Inventory();
		$user = new User();
		$prod = new Product($item_id);
		$stocks = $inv->allStockBaseOnItem($item_id, $user->data()->company_id, $branch_id);
		if($stocks) {
			echo "<h4>" . $prod->data()->description . "</h4>";
			echo "<input type='hidden' id='hidden_row_id'>";
			echo "<table class='table table-bordered'>";
			echo "<thead><tr><th>Rack</th><th>Qty</th><th></th></tr></thead>";
			$total_qty = 0;
			foreach($stocks as $st) {
				$rack = $st->rack_id;
				$branch = $branch_id;
				$qty = $st->qty;
				$item_code = str_replace('"', '', $st->item_code);
				$item_code = str_replace("'", "", $item_code);
				$auditid = 0;
				echo "<tr><td>$st->rack</td><td>" . number_format($st->qty, 2) . "</td>";
				echo "<td>";
				echo "<button   class='btn btn-default' onclick='ammendThis($rack,$branch,$item_id,$qty,\"$item_code\",$auditid,\"\")'>Amend</button> <button    class='btn btn-default'  onclick='confirmThis($rack,$branch," . $item_id . "," . $qty . ",\"$item_code\",$auditid)'>Confirm</button>";
				echo "</td>";
				echo "</tr>";
				$total_qty += $qty;
			}
			echo "</table>";
			$total_qty = formatQuantity($total_qty);
			echo "<input type='hidden' id='hidden_row_new_qty' value='$total_qty'>";


		} else {
			echo "<p>No Stocks</p>";
		}
	}

	function addInventory($company_id) {
		$user = new User();
		$data = Input::get('p');
		$data = json_decode($data);
		$request = $data->request_data;
		$addbatchcls = new Add_batch_inv();
		$addbatchcls->create(array('to_branch_id' => $request->branch_id, 'supplier_id' => $request->supplier_id, 'date_receive' => strtotime($request->date_receive), 'packing_list_num' => $request->packing_list, 'company_id' => $user->data()->company_id, 'user_id' => $user->data()->id, 'created' => time()));
		$lastidbatch = $addbatchcls->getInsertedId();
		$inventory = new Inventory();
		$bid = $request->branch_id;
		foreach($data->item_list as $item) {
			$iid = $item->item_id;
			$rid = $item->rack_id;
			$qty = $item->qty;
			$qty = removeComma($qty);
			if($iid && $rid && $qty) {


				if($iid && $rid) {
					$addbatchdetails = new Add_batch_inv_detail();
					$addbatchdetails->create(array('batch_id' => $lastidbatch, 'item_id' => $iid, 'qty' => $qty, 'is_active' => 1, 'company_id' => $user->data()->company_id, 'rack_id' => $rid));
					if($inventory->checkIfItemExist($iid, $bid, $user->data()->company_id, $rid)) {
						$curinventory = $inventory->getQty($iid, $bid, $rid);
						$inventory->addInventory($iid, $bid, $qty, false, $rid);
						// monitoring
						$inv_mon = new Inventory_monitoring();
						$newqty = $curinventory->qty + $qty;
						$inv_mon->create(array('item_id' => $iid, 'rack_id' => $rid, 'branch_id' => $bid, 'page' => 'admin/addinventory', 'action' => 'Update', 'prev_qty' => $curinventory->qty, 'qty_di' => 1, 'qty' => $qty, 'new_qty' => $newqty, 'created' => time(), 'user_id' => $user->data()->id, 'remarks' => 'Add inventory', 'is_active' => 1, 'company_id' => $user->data()->company_id));

					} else {
						$curinventory = 0;
						$inventory->addInventory($iid, $bid, $qty, true, $rid);
						// monitoring

						$inv_mon = new Inventory_monitoring();
						$newqty = $curinventory + $qty;
						$inv_mon->create(array('item_id' => $iid, 'rack_id' => $rid, 'branch_id' => $bid, 'page' => 'admin/addinventory', 'action' => 'Insert', 'prev_qty' => $curinventory, 'qty_di' => 1, 'qty' => $qty, 'new_qty' => $newqty, 'created' => time(), 'user_id' => $user->data()->id, 'remarks' => 'Add inventory', 'is_active' => 1, 'company_id' => $user->data()->company_id));
					}
				}
			}
		}
		echo "Inventory updated successfully.";
	}

	function addInvBatch() {
		$config_checker = Configuration::getValue('inv_check');
		$to_check_first = false;
		if($config_checker && $config_checker == 1) {
			$to_check_first = true;
		}
		$items = json_decode(Input::get('items'));
		$request = json_decode(Input::get('request'));
		$user = new User();
		$inventory = new Inventory();


		$bid = $request->branch_id;
		$supplier_id = ($request->supplier_id) ? $request->supplier_id : 0;
		$date_receive = ($request->date_receive) ? $request->date_receive : 0;
		$packing_list = $request->packing_list;
		$ref_num = $request->ref_num;
		$remarks = $request->remarks;

		// add batch
		$status = 0;
		$msg = "You have successfully updated the inventory";
		if($to_check_first) {
			$status = 1;
			$msg = "Inventory request has been successfully created.";
		}

		$addbatchcls = new Add_batch_inv();
		$addbatchcls->create(array('to_branch_id' => $bid,'remarks' => $remarks, 'supplier_id' => $supplier_id, 'date_receive' => strtotime($date_receive), 'packing_list_num' => $packing_list, 'ref_num' => $ref_num, 'company_id' => $user->data()->company_id, 'user_id' => $user->data()->id, 'created' => time(), 'is_pending' => $status));

		$lastidbatch = $addbatchcls->getInsertedId();


		foreach($items as $item) {
			$name = $item->item_id;
			$rname = $item->rack_id;
			$item_remarks = $item->remarks;
			$qty = removeComma($item->qty);

			if($name && $rname && $qty && $bid) {

				$iid = $name;
				$rid = $rname;


				if($iid && $rid) {

					$addbatchdetails = new Add_batch_inv_detail();
					$addbatchdetails->create(array('batch_id' => $lastidbatch, 'item_id' => $iid, 'remarks' => $item_remarks, 'qty' => $qty, 'is_active' => 1, 'company_id' => $user->data()->company_id, 'rack_id' => $rid

					));
					if(!$to_check_first) {
						if($inventory->checkIfItemExist($iid, $bid, $user->data()->company_id, $rid)) {
							$curinventory = $inventory->getQty($iid, $bid, $rid);
							$inventory->addInventory($iid, $bid, $qty, false, $rid);
							// monitoring
							$inv_mon = new Inventory_monitoring();
							$newqty = $curinventory->qty + $qty;
							$inv_mon->create(array('item_id' => $iid, 'rack_id' => $rid, 'branch_id' => $bid, 'page' => 'admin/addinventory', 'action' => 'Update', 'prev_qty' => $curinventory->qty, 'qty_di' => 1, 'qty' => $qty, 'new_qty' => $newqty, 'created' => time(), 'user_id' => $user->data()->id, 'remarks' => 'Add inventory', 'is_active' => 1, 'company_id' => $user->data()->company_id));

						} else {
							$curinventory = 0;
							$inventory->addInventory($iid, $bid, $qty, true, $rid);
							// monitoring

							$inv_mon = new Inventory_monitoring();
							$newqty = $curinventory + $qty;
							$inv_mon->create(array('item_id' => $iid, 'rack_id' => $rid, 'branch_id' => $bid, 'page' => 'admin/addinventory', 'action' => 'Insert', 'prev_qty' => $curinventory, 'qty_di' => 1, 'qty' => $qty, 'new_qty' => $newqty, 'created' => time(), 'user_id' => $user->data()->id, 'remarks' => 'Add inventory', 'is_active' => 1, 'company_id' => $user->data()->company_id));
						}
					}

					$itemadded = true;
				} else {

				}

			}
		}
		if(!$itemadded) {
			echo "Invalid request.";
		} else {
			echo $msg;
		}

	}

	function addSpare() {
		$item_set = Input::get('item_set');
		$data = Input::get('data');
		$data = json_decode($data, true);
		if($data) {
			$user = new User();
			$composite = new Composite_item();
			$now = time();
			$has_ext = false;
			$has_added = false;
			foreach($data as $d) {
				if($d['item_id'] && $d['qty']) {
					$val = $d['item_id'];
					$exists = $composite->checkIfExists($user->data()->company_id, $item_set, $val);
					if($exists->cnt > 0) {
						$has_ext = true;
					} else {
						$composite->create(array('item_id_raw' => $val, 'item_id_set' => $item_set, 'qty' => $d['qty'], 'created' => $now, 'modified' => $now, 'company_id' => $user->data()->company_id, 'is_active' => 1));
						$has_added = true;

					}
				}
			}
			$returnString = "";
			if($has_added) {
				$returnString .= "<p>Item added successfully</p>";
			} else {
				$returnString .= "<p>Request failed.</p>";
			}
			if($has_ext) {
				$returnString .= "<p>Some item(s) are already exists</p>";
			}
			echo $returnString;
		} else {
			echo "Invalid request.";
		}
	}


	function inOut() {

		$from = Input::get('from');
		$to = Input::get('to');
		$user = new User();
		$branch_id = Input::get('branch_id');
		$item_id = Input::get('item_id');
		$is_dl = Input::get('is_dl');
		$sales_date_type = Input::get('sales_date_type');

		$border = "";
		$to_check_id = $item_id;

		if($is_dl == 1) {
			$filename = "in-out-" . date('m-d-Y-h-i-s') . ".xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");
			$border = "border=1";
		}

		if($from && $to) {
			$from = strtotime($from);
			$to = strtotime($to . "1 day -1 min");
		} else {
			$from = strtotime(date('F Y'));
			$to = strtotime(date('F Y') . "1 month -1 min");
		}
		echo "<p>From: " . date('m/d/Y H:i:s A', $from) . " To: " . date('m/d/Y H:i:s A', $to) . "</p>";
		if(!$branch_id) {
			$branch_id = $user->data()->branch_id;
		}

		// sold by branch ref table - wh_orders with member

		// transfer qty by branch ref table - wh_orders member = 0

		//  transfer out qty by rack = transfer mon

		// INCOMING

		//  add inventory  - add batch item

		// return item - transfer mon - from backload - from service liqui-  from service return - from cancel -


		$wh = new Wh_order();

		$transfer = new Transfer_inventory_mon();

		$branch = new Branch($branch_id);

		$sold = $wh->getSoldFromBranch($branch_id, $from, $to,0,$sales_date_type);

		$transfer_to_branch = $wh->getTransferToBranch($branch_id, $from, $to);

		$transfer_in_branch = $wh->getTransferIn($branch_id, $from, $to);

		$transfer_rack = $transfer->getTransfer($from, $to, $branch_id);

		$service_item = $transfer->getServiceReturn($from, $to, $branch_id);

		$batch = new Add_batch_inv();

		$add_inventory = $batch->get_record_details($user->data()->company_id, 0, 100000, $branch_id, date('m/d/Y', $from), date('m/d/Y', $to));

		$arr_sold = [];
		$arr_transfer_to_branch = [];

		$arr_items = [];
		$arr_service = [];
		$arr_transfer_out = [];
		$arr_transfer_in = [];
		$arr_add = [];
		$arr_ctr_in = [];
		$arr_transfer_internal = [];
		if($add_inventory) {
			foreach($add_inventory as $add) {
				$arr_items[$add->item_id][$add->rack_id] = ['rack_id' => $add->rack_id, 'rack' => $add->rack, 'item_code' => $add->item_code, 'description' => $add->description];
				if(isset($arr_add[$add->item_id][$add->rack_id])) {
					$arr_add[$add->item_id][$add->rack_id] += $add->qty;
				} else {
					$arr_add[$add->item_id][$add->rack_id] = $add->qty;
				}
			}
		}

		if($service_item) {

			foreach($service_item as $tr) {

				$arr_items[$tr->item_id][$tr->rack_id] = ['rack_id' => $tr->rack_id_to, 'rack' => $tr->rack_to, 'item_code' => $tr->item_code, 'description' => $tr->description];

				if(isset($arr_service[$tr->item_id][$tr->rack_id_to])) {
					$arr_service[$tr->item_id][$tr->rack_id_to] += $tr->qty;
				} else {
					$arr_service[$tr->item_id][$tr->rack_id_to] = $tr->qty;
				}

			}
		}

		if($transfer_rack) {

			foreach($transfer_rack as $tr) {
				$arr_items[$tr->item_id][$tr->rack_id_from] = ['rack_id' => $tr->rack_id_from, 'rack' => $tr->rack_from, 'item_code' => $tr->item_code, 'description' => $tr->description];
				$arr_items[$tr->item_id][$tr->rack_id_to] = ['rack_id' => $tr->rack_id_to, 'rack' => $tr->rack_to, 'item_code' => $tr->item_code, 'description' => $tr->description];

				if(isset($arr_transfer_out[$tr->item_id][$tr->rack_id_from])) {
					$arr_transfer_out[$tr->item_id][$tr->rack_id_from] += $tr->qty;
				} else {
					$arr_transfer_out[$tr->item_id][$tr->rack_id_from] = $tr->qty;
				}

				if(isset($arr_transfer_internal[$tr->item_id][$tr->rack_id_to])) {
					$arr_transfer_internal[$tr->item_id][$tr->rack_id_to] += $tr->qty;
				} else {
					$arr_transfer_internal[$tr->item_id][$tr->rack_id_to] = $tr->qty;
				}


			}
		}

		if($sold) {

			foreach($sold as $s) {


				$json = json_decode($s->racking);
				if(!$s->is_bundle && $json) {
					foreach($json as $j) {
						$arr_items[$s->item_id][$j->rack_id] = ['rack_id' => $j->rack_id, 'rack' => $j->rack, 'item_code' => $s->item_code, 'description' => $s->description];
						if(isset($arr_sold[$s->item_id][$j->rack_id])) {
							$arr_sold[$s->item_id][$j->rack_id] += $j->qty;
						} else {
							$arr_sold[$s->item_id][$j->rack_id] = $j->qty;
						}
					}
				} else {
					// bundle logic
					if($s->racking) {
						$json = json_decode($s->racking, true);


						foreach($json as $cur_item_id => $array) {
							$item_code = '';
							$description = '';

							if($cur_item_id) {
								$prod = new Product($cur_item_id);
								$item_code = $prod->data()->item_code;
								$description = $prod->data()->description;
							}
							$array = json_decode($array, true);
							foreach($array as $data) {
								$rack_id = $data['rack_id'];
								$rack = $data['rack'];
								$qty = $data['qty'];
								$arr_items[$cur_item_id][$rack_id] = ['rack_id' => $rack_id, 'rack' => $rack, 'item_code' => $item_code, 'description' => $description];
								if(isset($arr_sold[$cur_item_id][$rack_id])) {
									$arr_sold[$cur_item_id][$rack_id] += $qty;
								} else {
									$arr_sold[$cur_item_id][$rack_id] = $qty;
								}
							}

						}
					}
				}


			}
		}


		if($transfer_to_branch) {

			foreach($transfer_to_branch as $t) {


				$json = json_decode($t->racking);
				if(!$t->is_bundle && $json) {
					foreach($json as $j) {
						$arr_items[$t->item_id][$j->rack_id] = ['rack_id' => $j->rack_id, 'rack' => $j->rack, 'item_code' => $t->item_code, 'description' => $t->description];

						$ctrl = "";

						if($t->wh_orders_id) {
							$ctrl .= "Branch: " . $t->branch_name . " Qty: " . formatQuantity($j->qty);
						}


						if(isset($arr_inv[$t->item_id][$j->rack_id])) {
							$arr_inv[$t->item_id][$j->rack_id] .= "<br>" . $ctrl;
						} else {
							$arr_inv[$t->item_id][$j->rack_id] = $ctrl;
						}


						if(isset($arr_transfer_to_branch[$t->item_id][$j->rack_id])) {
							$arr_transfer_to_branch[$t->item_id][$j->rack_id] += $j->qty;
						} else {
							$arr_transfer_to_branch[$t->item_id][$j->rack_id] = $j->qty;
						}

					}
				} else {
					if($t->racking) {
						$json = json_decode($t->racking, true);


						foreach($json as $cur_item_id => $array) {
							$item_code = '';
							$description = '';

							if($cur_item_id) {
								$prod = new Product($cur_item_id);
								$item_code = $prod->data()->item_code;
								$description = $prod->data()->description;
							}
							$array = json_decode($array, true);
							foreach($array as $data) {
								$rack_id = $data['rack_id'];
								$rack = $data['rack'];
								$qty = $data['qty'];

							}

						}
					}

				}
			}

		}


		if($transfer_in_branch) {

			foreach($transfer_in_branch as $t) {


				$json = json_decode($t->racking);
				if(!$s->is_bundle && $json) {
					foreach($json as $j) {
						$arr_items[$t->item_id][$j->rack_id] = ['rack_id' => $j->rack_id, 'rack' => $j->rack, 'item_code' => $t->item_code, 'description' => $t->description];

						$ctrl = "";

						if($t->wh_orders_id) {
							$ctrl .= "Branch: " . $t->branch_name . " Qty: " . formatQuantity($j->qty);
						}

						if(isset($arr_ctr_in[$t->item_id][$j->rack_id])) {
							$arr_ctr_in[$t->item_id][$j->rack_id] .= "<br>" . $ctrl;
						} else {
							$arr_ctr_in[$t->item_id][$j->rack_id] = $ctrl;
						}

						if(isset($arr_transfer_in[$t->item_id][$j->rack_id])) {
							$arr_transfer_in[$t->item_id][$j->rack_id] += $j->qty;
						} else {
							$arr_transfer_in[$t->item_id][$j->rack_id] = $j->qty;
						}

					}
				} else {

				}
			}

		}

		if($arr_items) {

			echo "<table $border class='table table-bordered'>";
			echo "<tr><th>Item Code</th><th>Description</th><th>Branch</th><th>Rack</th><th>Sold</th><th>Transfer To Other Branch</th><th>Rack Transfer Out<br>(Internal)</th><th>Rack Transfer In<br>(Internal)</th><th>Add Inventory</th><th>Order from other branch</th><th>Service</th></tr>";

			foreach($arr_items as $item_id => $racks) {
				if($to_check_id && $to_check_id != $item_id) {
					continue;
				}
				foreach($racks as $r) {

					$c_sold = isset($arr_sold[$item_id][$r['rack_id']]) ? $arr_sold[$item_id][$r['rack_id']] : 0;
					$c_out_branch = isset($arr_transfer_to_branch[$item_id][$r['rack_id']]) ? $arr_transfer_to_branch[$item_id][$r['rack_id']] : 0;
					$c_transfer_out = isset($arr_transfer_out[$item_id][$r['rack_id']]) ? $arr_transfer_out[$item_id][$r['rack_id']] : 0;
					$c_transfer_in = isset($arr_transfer_internal[$item_id][$r['rack_id']]) ? $arr_transfer_internal[$item_id][$r['rack_id']] : 0;
					$c_add = isset($arr_add[$item_id][$r['rack_id']]) ? $arr_add[$item_id][$r['rack_id']] : 0;
					$c_in_branch = isset($arr_transfer_in[$item_id][$r['rack_id']]) ? $arr_transfer_in[$item_id][$r['rack_id']] : 0;

					$c_service = isset($arr_service[$item_id][$r['rack_id']]) ? $arr_service[$item_id][$r['rack_id']] : 0;
					$ctrl_to_branch = isset($arr_inv[$item_id][$r['rack_id']]) ? $arr_inv[$item_id][$r['rack_id']] : '';
					$ctrl_from_branch = isset($arr_ctr_in[$item_id][$r['rack_id']]) ? $arr_ctr_in[$item_id][$r['rack_id']] : '';

					$c_in_branch = ($c_in_branch) ? $c_in_branch : 0;
					$c_add = ($c_add) ? $c_add : 0;
					$c_transfer_in = ($c_transfer_in) ? $c_transfer_in : 0;
					$c_transfer_out = ($c_transfer_out) ? $c_transfer_out : 0;
					$c_sold = ($c_sold) ? $c_sold : 0;
					$c_out_branch = ($c_out_branch) ? $c_out_branch : 0;

					$branch_name = $branch->data()->name;

					echo "<tr>";

					echo "<td style='border-top:1px solid #ccc;'>" . $r['item_code'] . "</td>";
					echo "<td style='border-top:1px solid #ccc;'>" . $r['description'] . "</td>";
					echo "<td style='border-top:1px solid #ccc;'>" . $branch_name . "</td>";
					echo "<td style='border-top:1px solid #ccc;width:80px;'>" . $r['rack'] . "</td>";
					echo "<td style='border-top:1px solid #ccc;'>" . formatQuantity($c_sold) . "</td>";
					echo "<td style='border-top:1px solid #ccc;'>" . formatQuantity($c_out_branch) . "<br> $ctrl_to_branch</td>";
					echo "<td style='border-top:1px solid #ccc;'>" . formatQuantity($c_transfer_out) . "</td>";
					echo "<td style='border-top:1px solid #ccc;'>" . formatQuantity($c_transfer_in) . "</td>";
					echo "<td style='border-top:1px solid #ccc;'>" . formatQuantity($c_add) . "</td>";
					echo "<td style='border-top:1px solid #ccc;'>" . formatQuantity($c_in_branch) . "<br>$ctrl_from_branch</td>";
					echo "<td style='border-top:1px solid #ccc;'>" . formatQuantity($c_service) . "</td>";


					echo "</tr>";
				}


			}
			echo "</table>";
		}

	}

	function getAssemblyInOut() {

		$from = Input::get('from');
		$to = Input::get('to');
		$user = new User();
		$branch_id = Input::get('branch_id');
		$is_dl = Input::get('is_dl');

		$border = "";
		if($is_dl == 1) {
			$filename = "in-out-" . date('m-d-Y-h-i-s') . ".xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");
			$border = "border=1";
		}

		if($from && $to) {
			$from = strtotime($from);
			$to = strtotime($to . "1 day -1 min");
		} else {
			$from = strtotime(date('F Y'));
			$to = strtotime(date('F Y') . "1 month -1 min");
		}

		if(!$branch_id) {
			$branch_id = $user->data()->branch_id;
		}

		$transfer = new Transfer_inventory_mon();
		$transfer_rack = $transfer->getTransferAssembly($from, $to, $branch_id);
		if($transfer_rack) {

			echo "<p>From: <strong class='text-danger'>" . date('m/d/Y', $from) . "</strong> To: <strong class='text-danger'>" . date('m/d/Y', $to) . "</strong></p>";

			echo "<table $border class='table table-bordered'>";
			echo "<tr><th>Item</th><th>Rack From</th><th>Qty</th><th>Rack To</th></tr>";
			foreach($transfer_rack as $t) {
				echo "<tr>
						<td style='border-top:1px solid #ccc;'>" . $t->description . "</td>
						<td style='border-top:1px solid #ccc;'>" . $t->rack_from . "<br>" . $t->tag_name_from . "</td>
						<td style='border-top:1px solid #ccc;'>" . $t->qty . "</td>
						<td style='border-top:1px solid #ccc;'>" . $t->rack_to . "<br>" . $t->tag_name_to . "</td>
					  </tr>";
			}
			echo "</table>";
		}
	}

	function inOut2() {

		$from = Input::get('from');

		$to = Input::get('to');

		$user = new User();
		$sales_date_type = Input::get('sales_date_type');

		$branch_id = Input::get('branch_id');
		$is_dl = Input::get('is_dl');
		$border = "";
		if($is_dl == 1) {
			$filename = "in-out-" . date('m-d-Y-h-i-s') . ".xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");
			$border = "border=1";
		}

		if($from && $to) {
			$from = strtotime($from);
			$to = strtotime($to . "1 day -1 min");
		} else {
			$from = strtotime(date('F Y'));
			$to = strtotime(date('F Y') . "1 month -1 min");
		}

		if(!$branch_id) {
			$branch_id = $user->data()->branch_id;
		}

		// sold by branch ref table - wh_orders with member

		// transfer qty by branch ref table - wh_orders member = 0

		//  transfer out qty by rack = transfer mon

		// INCOMING

		//  add inventory  - add batch item

		// return item - transfer mon - from backload - from service liqui-  from service return - from cancel

		$wh = new Wh_order();

		$transfer = new Transfer_inventory_mon();

		$sold = $wh->getSoldFromBranch($branch_id, $from, $to,$sales_date_type);

		$transfer_to_branch = $wh->getTransferToBranch($branch_id, $from, $to);

		$transfer_in_branch = $wh->getTransferIn($branch_id, $from, $to);

		$transfer_rack = $transfer->getTransfer($from, $to, $branch_id);

		$service_item = $transfer->getServiceReturn($from, $to, $branch_id);

		$batch = new Add_batch_inv();


		$add_inventory = $batch->get_record_details($user->data()->company_id, 0, 100000, $branch_id, date('m/d/Y', $from), date('m/d/Y', $to));

		$arr_sold = [];
		$arr_transfer_to_branch = [];
		$arr_transfer_in = [];
		$arr_items = [];
		$arr_transfer_out = [];
		$arr_service = [];
		$arr_add = [];
		$arr_inv = [];
		$arr_ctr_in = [];


		if($add_inventory) {
			foreach($add_inventory as $add) {
				$arr_items[$add->item_id][$add->rack_id] = ['rack_id' => $add->rack_id, 'rack' => $add->rack, 'item_code' => $add->item_code, 'description' => $add->description];


				if(isset($arr_add[$add->item_id])) {
					$arr_add[$add->item_id] += $add->qty;
				} else {
					$arr_add[$add->item_id] = $add->qty;
				}
			}
		}

		if($transfer_rack) {

			foreach($transfer_rack as $tr) {
				$arr_items[$tr->item_id] = ['item_code' => $tr->item_code, 'description' => $tr->description];


				if(isset($arr_transfer_out[$tr->item_id])) {
					$arr_transfer_out[$tr->item_id] += $tr->qty;
				} else {
					$arr_transfer_out[$tr->item_id] = $tr->qty;
				}

				if(isset($arr_transfer_in[$tr->item_id])) {
					$arr_transfer_in[$tr->item_id] += $tr->qty;
				} else {
					$arr_transfer_in[$tr->item_id] = $tr->qty;
				}

			}
		}
		if($service_item) {

			foreach($service_item as $tr) {
				$arr_items[$tr->item_id] = ['item_code' => $tr->item_code, 'description' => $tr->description];


				if(isset($arr_service[$tr->item_id])) {
					$arr_service[$tr->item_id] += $tr->qty;
				} else {
					$arr_service[$tr->item_id] = $tr->qty;
				}

			}
		}

		if($sold) {

			foreach($sold as $s) {


				$json = json_decode($s->racking);
				if(!$s->is_bundle && $json) {
					foreach($json as $j) {
						$arr_items[$s->item_id] = ['item_code' => $s->item_code, 'description' => $s->description];
						if(isset($arr_sold[$s->item_id])) {
							$arr_sold[$s->item_id] += $j->qty;
						} else {
							$arr_sold[$s->item_id] = $j->qty;
						}
					}
				} else {
					// bundle logic
				}


			}
		}


		if($transfer_to_branch) {

			foreach($transfer_to_branch as $t) {

				$ctrl = "";

				if($t->wh_orders_id) {
					$ctrl .= "Branch: " . $t->branch_name . " Qty: " . formatQuantity($t->qty);
				}


				if(isset($arr_inv[$t->item_id])) {
					$arr_inv[$t->item_id] .= "<br>" . $ctrl;
				} else {
					$arr_inv[$t->item_id] = $ctrl;
				}

				$json = json_decode($t->racking);
				if(!$t->is_bundle && $json) {
					foreach($json as $j) {
						$arr_items[$t->item_id] = ['item_code' => $t->item_code, 'description' => $t->description];
						if(isset($arr_transfer_to_branch[$t->item_id])) {
							$arr_transfer_to_branch[$t->item_id] += $j->qty;
						} else {
							$arr_transfer_to_branch[$t->item_id] = $j->qty;
						}

					}
				} else {

				}
			}

		}


		if($transfer_in_branch) {

			foreach($transfer_in_branch as $t) {


				$ctrl = "";

				if($t->wh_orders_id) {
					$ctrl .= "Branch: " . $t->branch_name . " Qty: " . formatQuantity($t->qty);
				}

				if(isset($arr_ctr_in[$t->item_id])) {
					$arr_ctr_in[$t->item_id] .= "<br>" . $ctrl;
				} else {
					$arr_ctr_in[$t->item_id] = $ctrl;
				}

				$json = json_decode($t->racking);
				if(!$s->is_bundle && $json) {
					foreach($json as $j) {
						$arr_items[$t->item_id] = ['item_code' => $t->item_code, 'description' => $t->description];
						if(isset($arr_transfer_in[$t->item_id])) {
							$arr_transfer_in[$t->item_id] += $j->qty;
						} else {
							$arr_transfer_in[$t->item_id] = $j->qty;
						}

					}
				} else {

				}
			}

		}

		if($arr_items) {

			echo "<p>From: " . date('m/d/Y', $from) . " To: " . date('m/d/Y', $to) . "</p>";
			echo "<table $border class='table table-bordered'>";
			echo "<tr><th>Item Code</th><th>Description</th><th>Sold</th><th>Transfer To Other Branch</th><th>Add Inventory</th><th>Order from other branch</th><th>Service</th></tr>";

			foreach($arr_items as $item_id => $item) {
				$c_sold = isset($arr_sold[$item_id]) ? $arr_sold[$item_id] : 0;
				$c_out_branch = isset($arr_transfer_to_branch[$item_id]) ? $arr_transfer_to_branch[$item_id] : 0;
				$c_transfer_out = isset($arr_transfer_out[$item_id]) ? $arr_transfer_out[$item_id] : 0;
				$c_transfer_in = isset($arr_transfer_in[$item_id]) ? $arr_transfer_in[$item_id] : 0;
				$c_add = isset($arr_add[$item_id]) ? $arr_add[$item_id] : 0;
				$c_in_branch = isset($arr_transfer_in[$item_id]) ? $arr_transfer_in[$item_id] : 0;
				$c_service = isset($arr_service[$item_id]) ? $arr_service[$item_id] : 0;

				$ctrl_to_branch = isset($arr_inv[$item_id]) ? $arr_inv[$item_id] : '';
				$ctrl_from_branch = isset($arr_ctr_in[$item_id]) ? $arr_ctr_in[$item_id] : '';
				$c_in_branch = ($c_in_branch) ? $c_in_branch : 0;
				$c_add = ($c_add) ? $c_add : 0;
				$c_transfer_in = ($c_transfer_in) ? $c_transfer_in : 0;
				$c_transfer_out = ($c_transfer_out) ? $c_transfer_out : 0;
				$c_sold = ($c_sold) ? $c_sold : 0;
				$c_out_branch = ($c_out_branch) ? $c_out_branch : 0;

				echo "<tr>";
				echo "<td style='border-top:1px solid #ccc;'>" . $item['item_code'] . "</td>";
				echo "<td style='border-top:1px solid #ccc;'>" . $item['description'] . "</td>";
				echo "<td style='border-top:1px solid #ccc;'>" . formatQuantity($c_sold) . "</td>";
				echo "<td style='border-top:1px solid #ccc;width:250px;'>" . formatQuantity($c_out_branch) . "<br> $ctrl_to_branch</td>";
				echo "<td style='border-top:1px solid #ccc;'>" . formatQuantity($c_add) . "</td>";
				echo "<td style='border-top:1px solid #ccc;width:250px;'>" . formatQuantity($c_in_branch) . "<br> $ctrl_from_branch</td>";
				echo "<td style='border-top:1px solid #ccc;'>" . formatQuantity($c_service) . "</td>";
				echo "</tr>";
			}
			echo "</table>";
		}

	}


	function saveEndingInventory() {

		$branch_id = Input::get('branch_id');
		$report_date = Input::get('report_date');
		$items = Input::get('items');
		$items = json_decode($items, true);

		if($items) {

			$end_inventory = new Inventory_ending();

			foreach($items as $item) {

				$end_inventory->create(array('item_id' => $item['item_id'], 'branch_id' => $branch_id, 'report_date' => strtotime($report_date), 'qty' => $item['qty']));

			}


			echo "Record updated successfully.";

		}

	}

	function getEndingInventory() {

		$ending_inventory = new Inventory_ending();

		$results = $ending_inventory->getSummary();

		$arr = [];

		foreach($results as $res) {

			$res->formatted_date = date('m/d/Y', $res->report_date);
			$arr[] = $res;

		}
		echo json_encode($arr);


	}

	function getEndingDetails() {
		$report_date = Input::get('report_date');
		$branch_id = Input::get('branch_id');
		$ending_inventory = new Inventory_ending();
		$results = $ending_inventory->getDetails($branch_id, $report_date);
		$arr = [];
		if($results) {
			foreach($results as $res) {
				$res->qty = formatQuantity($res->qty);
				$arr[] = $res;

			}
			echo json_encode($arr);
		}
	}


	function avgRawConsumption() {
		$assemble = new Assemble_request();

		$dt1 = Input::get('dt1');
		$dt2 = Input::get('dt2');

		if($dt1 && $dt2) {
			$dt1 = strtotime($dt1);
			$dt2 = strtotime($dt2 . "1 day -1 min");
		} else {
			$current = date('F Y');
			$dt1 = strtotime($current . "-1 month");
			$dt2 = strtotime($current . "-1 min");
		}


		echo "<p>From: <strong>" . date('m/d/Y', $dt1) . "</strong> To: <strong>" . date('m/d/Y', $dt2) . "</strong></p>";
		$results = $assemble->getDataWeekly($dt1, $dt2);

		$arr = [];
		$arr_item = [];
		foreach($results as $res) {


			$decode = json_decode($res->racking, true);

			foreach($decode as $dec) {
				$arr[$dec['raw']['item_code']][$res->week_number] = $dec['raw']['need_total'];
				$arr_item[$dec['raw']['item_code']] = $dec['raw']['id'];
			}


		}
		echo "<table id='tblItem' class='table table-bordered'>";
		echo "<thead>";
		echo "<tr><th>Item</th><th>Avg Qty</th><th></th></tr>";
		echo "</thead>";
		echo "<tbody>";
		foreach($arr as $item_code => $a) {

			$count = (count($a));
			$sum = (array_sum($a));
			$avg_per_week = $sum / $count;
			$avg_per_week = formatQuantity($avg_per_week, true);
			$item_id = $arr_item[$item_code];
			echo "<tr data-item_id='$item_id'>";
			echo "<td style='border-top:1px solid #ccc;'>" . $item_code . "</td>";
			echo "<td style='border-top:1px solid #ccc;'><input type='text' value='" . $avg_per_week . "' placeholder='Qty'></td>";
			echo "<td style='border-top:1px solid #ccc;' ><button class='btn btn-default btn-sm btnRemove'><i class='fa fa-close'></i></button></td>";
			echo "</tr>";

		}

		echo "</tbody>";
		echo "</table>";

		$branch = new Branch();
		$branches = $branch->get_active('branches', [1, '=', 1]);

		$supplier = new Supplier();
		$suppliers = $supplier->get_active('suppliers', [1, '=', 1]);

		echo "<div>";
		echo "<div class='row'>";
		echo "<div class='col-md-6'>";
		echo "<button class='btn btn-default' id='btnAddMore'>Add More Item</button>";
		echo "</div>";
		echo "</div>";
		echo "<br>";
		echo "<hr>";
		echo "<div class='form-group'>";
		echo "<div class='row'>";
		echo "<div class='col-md-3'>";
		echo "<select id='branch_id' class='form-control'>";
		echo "<option value=''></option>";
		foreach($branches as $b) {
			echo "<option value='$b->id'>$b->name</option>";
		}
		echo "</select>";
		echo "</div>";
		echo "<div class='col-md-3'>";
		echo "<select id='supplier_id' class='form-control'>";
		echo "<option value=''></option>";
		foreach($suppliers as $s) {
			echo "<option value='$s->id'>$s->name</option>";
		}
		echo "</select>";
		echo "</div>";
		echo "<div class='col-md-3'>";
		echo "<button class='btn btn-primary' id='btnFinal'>Submit Order to Supplier</button>";
		echo "</div>";
		echo "</div>";
		echo "</div>";

	}

	function submitSupplierItem(){
		$user = new User();
		$sup_order = new Supplier_order();
		$od = new Supplier_order_details();
		$company_id = $user->data()->company_id;
		$now = time();
		$timelog = [];
		$msg = "Requested by "  . ucwords($user->data()->firstname .  " " . $user->data()->lastname);
		$timelog[] = ['time' => $now,'message' => $msg];
		$supplier_id = Input::get('supplier_id');
		$branch_id = Input::get('branch_id');
		$items = json_decode(Input::get('items'));

		if(count($items) && $branch_id && $supplier_id){

			$supplier_item = new Supplier_item();

			$sup_order->create(array(
				'created' => $now,
				'modified' => $now,
				'company_id' => $company_id,
				'is_active' => 1,
				'status' => 0,
				'is_rush' => 0,
				'user_id' => $user->data()->id,
				'supplier_id' => $supplier_id,
				'branch_to' => $branch_id
			));

			$lastorderid = $sup_order->getInsertedId();

			foreach($items as $o){
				$itm = $supplier_item->getSupplierItemId($company_id,$supplier_id,$o->item_id);
				$supplier_item_id = (isset($itm->id) && $itm->id ) ?  $itm->id  : 0;
				if($supplier_item_id){
					$od->create(array(
						'supplier_item_id' => $supplier_item_id,
					'qty' => $o->qty,
					'created' => $now,
					'modified' => $now,
					'company_id' => $company_id,
					'supplier_order_id' => $lastorderid,
					'is_active' => 1,
					'get_qty' => 0
				));
				}



			}
			echo "Order was successfully placed. Order id # $lastorderid";
		} else {
			echo "Invalid Data!";
		}


	}


	function criticalOrderCustom(){
		$inv = new Inventory_monitoring();
		$user = new User();

		$dt1 = strtotime(Input::get('dt1'));
		$dt2 = strtotime(Input::get('dt2') . "1 day -1 min");

		$branch_id = Input::get('branch_id');


		$result = $inv->criticalOrder($branch_id,$dt1,$dt2);
		$items = [];
		$item_info = [];
		foreach($result as $res){
			$item_info[$res->item_code] = $res->description;
			$items[$res->item_code][$res->m] = $res->total_qty;
		}

		echo "<table class='table table-bordered'>";
		echo "<thead><tr><th>Item</th><th>Total Sold</th><th>Months Sold</th><th>Monthly Avg</th><th>Forecast<br><small>For the next 6 months</small></th></tr></thead>";
		foreach($items as $item_id => $item){

			$total = array_sum($item);
			$cnt = count($item);
			$avg = 0;
			if($total){
				$avg= floor($total / $cnt);
			}
			echo "
				<tr>
					<td style='border-top:1px solid #ccc;'>
					$item_info[$item_id]
						<span style='display: none;'></span>

					</td>
					<td  style='border-top:1px solid #ccc;'>$total</td>
					<td  style='border-top:1px solid #ccc;'>$cnt</td>
					<td  style='border-top:1px solid #ccc;'>$avg</td>
					<td  style='border-top:1px solid #ccc;'>" .($avg * $cnt). "</td>
				</tr>";
		}
		echo "</table>";
	}

	function byItemSummary(){

		$ctr = Input::get('ctr');
		$type = Input::get('type');

		$wh_order = new Wh_order();

		$ctr = ($ctr) ? $ctr : 0;

		$user = new User();
		$branch_id = $user->data()->branch_id;
		$dt1 =  date("m/d/Y", strtotime('monday this week'));
		$dt2 =  date("m/d/Y", strtotime('sunday this week'));

		if($ctr){
			$ctr1 = $ctr * 7;

			$dt1 = strtotime($dt1 . "$ctr1 day");
			$dt2 = strtotime(date('m/d/Y',$dt1) . "7 days -1 min");

		} else {
			$dt1 = strtotime($dt1);
			$dt2 = strtotime($dt2 . "1 day -1 min");
		}
		$dpnone ="display:none;";
		if($ctr<0){
			$dpnone ="display:block;";
		}
		$list = $wh_order->getSummaryOfOutItems($dt1,$dt2,$branch_id,$type);
		$list2 = $wh_order->getSummaryOfOutItems2($dt1,$dt2,$branch_id,$type);

		$start = date('Y-m-d',$dt1);
		$end = date('Y-m-d',$dt2);


		$begin = new DateTime($start);
		$end = new DateTime($end);
		$end = $end->modify( '+1 day' );
		$interval = DateInterval::createFromDateString('1 day');
		$period = new DatePeriod($begin, $interval, $end);


		echo "<div class='row'>";
		echo "<div class='col-md-3'><button class='btn btn-default' id='btnPrev'><i class=' fa fa-arrow-left'></i> </button></div>";
		echo "<div class='col-md-6'>";
		echo "<h3 class='text-center'>".date('m/d/Y',$dt1)." - ".date('m/d/Y',$dt2)."</h3>";
		echo "</div>";
		echo "<div class='col-md-3 text-right' style='$dpnone'><button class='btn btn-default' id='btnNext'><i class=' fa fa-arrow-right'></i> </button></div>";
		echo "</div>";
		$arr_items = [];
		$arr_data = [];
		$arr_data_orig = [];
			if($list){
				echo "<div style='overflow-x: auto;border: 2px solid #ccc;'>";
			echo "<table class='table table-bordered' id='tblWithBorder' style='font-size:10px;'>";
			echo "<tr>";
			echo "<th style='width:130px;'>Item</th>";


			foreach ($period as $dt) {
				echo "<th colspan='3'>".$dt->format("m") . "-" . $dt->format("j")."</th>";
			}
			echo "<th colspan='3'>Total</th>";

			echo "</tr>";
				echo "<tr>";
				echo "<th style='width:150px;'></th>";
				echo "<th>Ordered</th>";
				echo "<th>Served</th>";
				echo "<th>Dif</th>";
				echo "<th>Ordered</th>";
				echo "<th>Served</th>";
				echo "<th>Dif</th>";
				echo "<th>Ordered</th>";
				echo "<th>Served</th>";
				echo "<th>Dif</th>";
				echo "<th>Ordered</th>";
				echo "<th>Served</th>";
				echo "<th>Dif</th>";
				echo "<th>Ordered</th>";
				echo "<th>Served</th>";
				echo "<th>Dif</th>";
				echo "<th>Ordered</th>";
				echo "<th>Served</th>";
				echo "<th>Dif</th>";
				echo "<th>Ordered</th>";
				echo "<th>Served</th>";
				echo "<th>Dif</th>";
				echo "<th>Ordered</th>";
				echo "<th>Served</th>";
				echo "<th>Dif</th>";
				echo "</tr>";

				foreach($list as $l){
					$arr_items[$l->item_id] = $l->item_code;
					$arr_data_orig[$l->item_id][$l->d] = $l->original_qty;
				}

				foreach($list2 as $l){
					$arr_items[$l->item_id] = $l->item_code;
					$arr_data[$l->item_id][$l->d] = $l->qty;
				}

				$total_per_d = [];
				$total_per_d_orig = [];

				foreach($arr_items as $item_id => $item_code){
					echo "<tr>";
					echo "<td>$item_code</td>";
					$total_qty = 0;
					$total_qty_dif= 0;
					foreach ($period as $dt) {
						$n = $dt->format("j");

						$qty = isset($arr_data[$item_id][$n]) ? $arr_data[$item_id][$n] :0;
						$original_qty = isset($arr_data_orig[$item_id][$n]) ? $arr_data_orig[$item_id][$n] :0;



						if(isset($total_per_d[$n])){
							$total_per_d[$n] += $qty;
						} else {
							$total_per_d[$n] = $qty;
						}
						if(isset($total_per_d_orig[$n])){
							$total_per_d_orig[$n] += $original_qty;
						} else {
							$total_per_d_orig[$n] = $original_qty;
						}

						$dif = $original_qty - $qty;
						echo "<td>$original_qty</td>";
						echo "<td >$qty</td>";
						echo "<td class='text-danger'>" . number_format($dif,3,".","") . "</td>";
						$total_qty += $qty;
						$total_qty_dif += $original_qty;
					}
					$total_diff = $total_qty_dif - $total_qty;
					echo "<td>$total_qty_dif</td>";
					echo "<td>$total_qty</td>";
					echo "<td>$total_diff</td>";
					echo "</tr>";
				}

				echo "<tr>";
				echo "<th style='border-top:1px solid #ccc;'>Total</th>";

				$total_all = 0;
				$total_all_orig = 0;
				foreach ($period as $dt) {
					$td = isset($total_per_d[$dt->format("j")]) ? $total_per_d[$dt->format("j")] : 0;
					$td_orig = isset($total_per_d_orig[$dt->format("j")]) ? $total_per_d_orig[$dt->format("j")] : 0;
					$total_all += $td;
					$total_all_orig += $td_orig;
					$td_diff = $td_orig - $td;
					echo "<th style='border-top:1px solid #ccc;'>". $td_orig ."</th>";
					echo "<th style='border-top:1px solid #ccc;'>".$td."</th>";
					echo "<th style='border-top:1px solid #ccc;'> " . number_format($td_diff,3,".","") . "</th>";
				}
				$total_all_dif = $total_all_orig - $total_all;
				echo "<th style='border-top:1px solid #ccc;'>$total_all_orig</th>";
				echo "<th style='border-top:1px solid #ccc;'>$total_all</th>";
				echo "<th style='border-top:1px solid #ccc;'>$total_all_dif</th>";
				echo "</tr>";



				echo "</table>";
				echo "</div>";
		} else {
			echo "<div class='alert alert-info'>No record found.</div>";
		}

	}

	function getRackByGroup(){
		$branch_id = Input::get('branch_id');
		$grp = Input::get('grp');
		$rack = new Rack();

		$list = $rack->rackGroupInformation($grp,$branch_id);
		if($list){
			echo "<div class='row'>";
			echo "<div class='col-md-4'>";
			echo "<input type='text' placeholder='Stockman Name' class='form-control' id='txtStockMan'>";
			echo "</div>";
			echo "<div class='col-md-4'>";
			echo "<button class='btn btn-default' id='btnUpdate'>Update</button>";
			echo "</div>";
			echo "</div> <br>";

			echo "<table class='table table-bordered' id='tblRacks'>";
			echo "<thead><tr><th>Rack</th><th>Custodian</th><th></th></tr></thead>";
			echo "<tbody>";
			foreach($list as $l){
				echo "<tr data-id='$l->id'>";
				echo "<td style='border-top:1px solid #ccc;'>$l->rack</td>";
				echo "<td style='border-top:1px solid #ccc;'>$l->stock_man</td>";
				echo "<td style='border-top:1px solid #ccc;'></td>";
				echo "</tr>";
			}
			echo "</tbody>";
			echo "</table>";
		} else {
			echo "<div class='alert alert-info'>No record.</div>";
		}

	}

	function updateRackGroup(){
		$ids = Input::get('ids');
		$stock_man =Input::get('stock_man');
		$user = new User();

		if($stock_man && $ids){
			$ids = json_decode($ids, true);
			if(count($ids)){
				$rack = new Rack();
				foreach($ids as $id){
					$rack->update(['stock_man' => $stock_man],$id);
					Log::addLog($user->data()->id,$user->data()->company_id,"Update Stock Man Rack ID $id Stock Man $stock_man","ajax_inventory.php");

				}

			}
			echo  "Updated successfully";
		} else {
			echo  "Failed to update the records";
		}
	}


	function amendWarning(){

		$item_id = Input::get('item_id');
		$qty = Input::get('qty');
		$branch_id = Input::get('branch_id');

		if($branch_id && $item_id && $qty){
			$user = new User();
			$wh_order = new Wh_order();
			$rack_tags = new Rack_tag();
			$tags_ex = $rack_tags->get_tags_ex('wh_orders',$user->data()->company_id,$branch_id);

			if(isset($tags_ex->id) && !empty($tags_ex->id)){
				$excempt_tags = $tags_ex->tag_id;
			} else {
				$excempt_tags =0;
			}
			$pending = $wh_order->getPendingOrder($item_id,$branch_id);
			$inv = new Inventory();
			$stock = $inv->getAllQuantity($item_id,$branch_id,$excempt_tags);
			$stock_qty = 0;
			$pending_order_qty = 0;

			if(isset($stock->totalQty) && $stock->totalQty){
				$stock_qty = $stock->totalQty;
			}

			if(isset($pending->od_qty) && $pending->od_qty){
				$pending_order_qty = $pending->od_qty;
			}

			$diff = $stock_qty - $pending_order_qty;

			if($diff <= $qty){
				echo "Pending Order: <strong>$pending_order_qty</strong> Total Branch Inventory: <strong>$stock_qty</strong>";
			}

		}

	}

	function getRackInventory($item_id = 0,$branch_id = 0){
		$ret = false;
		if($item_id && $branch_id){
			$ret = true;
		} else {
			$item_id = Input::get('item_id');
			$branch_id = Input::get('branch_id ');
		}

		$user = new User();
		$inv = new Inventory();

		$list = $inv->allStockBaseOnItem($item_id,$user->data()->company_id,$branch_id);

		$arr = [];
		if($list){
			foreach($list as $a){
				if(!$a->rack) continue;
				$arr[] = ['rack_name' => $a->rack,'qty' => formatQuantity($a->qty), 'id' => $a->rack_id];
			}
		}
		if($ret){
			return $arr;
		} else {
			echo json_encode($arr);
		}

	}

	function submitBorrowedItems(){

		$form = Input::get('form');

		$parts = Input::get('parts');

		$form = json_decode($form);

		$parts = json_decode($parts);



		if(count($parts) > 0 && $form->branch_id && $form->item_id && $form->qty && $form->rack_id ){

			// deduction inventory of main
			$inventory = new Inventory();
			$inv_mon = new Inventory_monitoring();
			$user = new User();

			if($inventory->checkIfItemExist($form->item_id,$form->branch_id,$user->data()->company_id,$form->rack_id)){

				$curinventoryFrom = $inventory->getQty($form->item_id,$form->branch_id,$form->rack_id);

				$currentqty = $curinventoryFrom->qty;
				$inventory->subtractInventory($form->item_id,$form->branch_id,$form->qty,$form->rack_id);
			} else {

				$currentqty = 0;
			}


			// monitoring
			$newqtyFrom = $currentqty - $form->qty;
			$inv_mon->create(array(
				'item_id' =>$form->item_id,
				'rack_id' => $form->rack_id,
				'branch_id' => $form->branch_id,
				'page' => 'ajax/ajax_inventory.php',
				'action' => 'Update',
				'prev_qty' => $currentqty,
				'qty_di' => 2,
				'qty' => $form->qty,
				'new_qty' => $newqtyFrom,
				'created' => time(),
				'user_id' => $user->data()->id,
				'remarks' => 'Borrowed parts',
				'is_active' => 1,
				'company_id' => $user->data()->company_id
			));

			// insert borrowed tables

			$borrowed = new Borrowed_part();

			$borrowed->create(
				[
					'qty' => $form->qty,
					'remarks' => $form->remarks,
					'from_rack_id' => $form->rack_id,
					'branch_id' => $form->branch_id,
					'item_id' => $form->item_id,
					'status' => 1,
					'user_id' => $user->data()->id,
					'borrowed_part' => json_encode($parts),
				]
			);

			$lastid = $borrowed->getInsertedId();

			foreach($parts as $part){

				if($inventory->checkIfItemExist($part->item_id,$form->branch_id,$user->data()->company_id,$part->rack_id)){
					$curinventoryFrom = $inventory->getQty($part->item_id,$form->branch_id,$part->rack_id);
					$currentqty = $curinventoryFrom->qty;
					$inventory->addInventory($part->item_id,$form->branch_id,$part->qty,false,$part->rack_id);
				} else {
					$currentqty = 0;
					$inventory->addInventory($part->item_id,$form->branch_id,$part->qty,true,$part->rack_id);
				}
				// monitoring
				$newqtyFrom = $currentqty + $part->qty;
				$inv_mon->create(array(
					'item_id' => $part->item_id,
					'rack_id' => $part->rack_id,
					'branch_id' => $form->branch_id,
					'page' => 'ajax/ajax_query2.php',
					'action' => 'Update',
					'prev_qty' => $currentqty,
					'qty_di' => 1,
					'qty' => $part->qty,
					'new_qty' => $newqtyFrom,
					'created' => time(),
					'user_id' => $user->data()->id,
					'remarks' => 'Add borrow part (id #'.$lastid.')',
					'is_active' => 1,
					'company_id' => $user->data()->company_id
				));
			}
			echo "Processed successfully.";
		} else {
			echo "Invalid Request";
		}
	}


	function getBorrowedParts(){
		$borrowed = new Borrowed_part();
		$status = Input::get('status');

		$list = $borrowed->getRecords($status);
		$arr = [];

		foreach($list as $l){
			$l->qty = formatQuantity($l->qty);
			$arr[] = $l;
		}

		echo json_encode($arr);

	}

	function showPartModule(){
		$id = Input::get('id');
		$borrowed = new Borrowed_part($id);

		if($borrowed->data()->status == 1){
			$json = json_decode($borrowed->data()->borrowed_part);
			$branch_id = $borrowed->data()->branch_id;
			if($json){

				$arr = [];

				foreach($json as $j){

					$rack_inv = getRackInventory($j->item_id,$branch_id);

					$arr_finale = [];

					foreach($rack_inv as $rr){
						if($rr['qty'] >= $j->qty){
							$arr_finale[] = $rr;
						}
					}

					$j->rack_inventory = $arr_finale;
					$j->chosen_rack = 0;
					$arr[]= $j;

				}

			}
			echo json_encode($arr);

		}

	}

	function processBorrowedItem(){
		$rack_details = json_decode(Input::get('rack_details'));
		$data = json_decode(Input::get('data'));

		$branch_id = $data->branch_id;
		$id = $data->id;
		$item_id = $data->item_id;
		$qty = $data->qty;
		$rack_id = $data->from_rack_id;



		$user = new User();

		if($rack_details){
			$inventory = new Inventory();
			$inv_mon = new Inventory_monitoring();
			foreach($rack_details as $rd) {
				if($rd->item_id && $rd->qty >= $qty) {
					if($inventory->checkIfItemExist($rd->item_id, $branch_id, $user->data()->company_id, $rd->chosen_rack)) {
						$curinventoryFrom = $inventory->getQty($rd->item_id, $branch_id, $rd->chosen_rack);
						$currentqty = $curinventoryFrom->qty;
						$inventory->subtractInventory($rd->item_id, $branch_id, $rd->qty, $rd->chosen_rack);
					} else {
						$currentqty = 0;
					}

					$newqtyFrom = $currentqty - $rd->qty;
					$inv_mon->create(array('item_id' => $rd->item_id, 'rack_id' => $rd->chosen_rack, 'branch_id' => $branch_id, 'page' => 'ajax/ajax_query2.php', 'action' => 'Update', 'prev_qty' => $currentqty, 'qty_di' => 2, 'qty' => $rd->qty, 'new_qty' => $newqtyFrom, 'created' => time(), 'user_id' => $user->data()->id, 'remarks' => 'Return Borrowed Part', 'is_active' => 1, 'company_id' => $user->data()->company_id));
				}
			}

			if($inventory->checkIfItemExist($item_id, $branch_id, $user->data()->company_id, $rack_id)) {
				$curinventory = $inventory->getQty($item_id, $branch_id, $rack_id);
				$inventory->addInventory($item_id, $branch_id, $qty, false, $rack_id);
				// monitoring
				$inv_mon = new Inventory_monitoring();
				$newqty = $curinventory->qty + $qty;
				$inv_mon->create(array('item_id' => $item_id, 'rack_id' => $rack_id, 'branch_id' => $branch_id, 'page' => 'admin/addinventory', 'action' => 'Update', 'prev_qty' => $curinventory->qty, 'qty_di' => 1, 'qty' => $qty, 'new_qty' => $newqty, 'created' => time(), 'user_id' => $user->data()->id, 'remarks' => 'Add Inventory From Borrowed Set Item', 'is_active' => 1, 'company_id' => $user->data()->company_id));

			} else {
				$curinventory = 0;
				$inventory->addInventory($item_id, $branch_id, $qty, true, $rack_id);
				// monitoring

				$inv_mon = new Inventory_monitoring();
				$newqty = $curinventory + $qty;
				$inv_mon->create(array('item_id' => $item_id, 'rack_id' => $rack_id, 'branch_id' => $branch_id, 'page' => 'admin/addinventory', 'action' => 'Insert', 'prev_qty' => $curinventory, 'qty_di' => 1, 'qty' => $qty, 'new_qty' => $newqty, 'created' => time(), 'user_id' => $user->data()->id, 'remarks' => 'Add Inventory From Borrowed Set Item', 'is_active' => 1, 'company_id' => $user->data()->company_id));
			}

			$borrowed = new Borrowed_part();
			$borrowed->update([
				'status' => 2,
				'returned_by' => $user->data()->id
			],$id);
			echo "Update successfully.";
		}

	}
