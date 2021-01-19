<?php
	include 'ajax_connection.php';
	$functionName = Input::get("functionName");

	if(function_exists($functionName)){
		$functionName();
	}

	function backToWarehouse(){
		$order_id = Input::get('order_id');
		$wh_order = new Wh_order();
		if(is_numeric($order_id)){
			$wh_order->update(['status' => 3,'is_scheduled' => 0],$order_id);
			echo "Update successfully.";
		}
	}

	function getUnits(){
		$arr = [];
		$item_id = Input::get('item_id');
		$item_unit = new Item_unit();
		$units = $item_unit->getUnits($item_id);
		if($units){
			$hasOne = false;
			foreach($units as $u){
				if($u->qty == 1){
					$hasOne = true;
				}
				$arr[] = $u;
			}
			if(!$hasOne){
				$new_unit = null;
				$new_unit->qty = 1;
				$new_unit->unit_name = 'Pcs';

				array_unshift($arr,$new_unit);
			}
		}
		echo json_encode($arr);
	}
	function getItemInfo(){
		$user = new User();
		$arr = [];
		$ret = [];
		$item_id = Input::get('item_id');
		$branch_id = Input::get('branch_id');
		if($item_id){
			$item_unit = new Item_unit();
			$units = $item_unit->getUnits($item_id);
			if($units){
				$hasOne = false;
				foreach($units as $u){
					if($u->qty == 1){
						$hasOne = true;
					}
					$arr[] = $u;
				}
				if(!$hasOne){
					$new_unit = null;
					$new_unit->qty = 1;
					$new_unit->unit_name = 'Pcs';

					array_unshift($arr,$new_unit);
				}
			}

			/* BO RACK OR SUPLUS RACK */

			$suplus_rack = 0;


			if(Configuration::getValue('surplus_rack') == 1){
				$suplus_rack = 1;
			}



			if($suplus_rack == 1 && $branch_id){
			/*

				$rack = new Rack();
				$inv = new Inventory();

				$default_racks = $rack->getRackDefaults($branch_id);
				$surplus = $inv->getSurplusAvailable($default_racks->surplus_rack,$branch_id,$item_id);

				$total_pending = ($surplus->pending_qty) ?$surplus->pending_qty : 0;
				$total_qty = ($surplus->totalqty) ?$surplus->totalqty : 0;

				$allowed = $total_qty - $total_pending;
				$ret = ['total_pending' => $total_pending,'total_qty' => $total_qty,'allowed' => $allowed];

			*/

				$inv = new Inventory_issue();
				$totalqty = $inv->getAllQuantity($item_id,$branch_id,0,4);
				$totalpending = $inv->getPendingOrderQty($item_id,$branch_id);
				$total_qty = ($totalqty->totalQty) ?$totalqty->totalQty : 0;
				$total_pending = ($totalpending->od_qty) ?$totalpending->od_qty : 0;
				$allowed = $total_qty - $total_pending;
				$ret = ['total_pending' => $total_pending,'total_qty' => $total_qty,'allowed' => $allowed];

			}
		}


		echo json_encode(['units' => $arr,'surplus' => $ret]);

	}
	function updateRemarks(){
		$id = Input::get('id');
		$remarks = Input::get('remarks');
		$received_date = Input::get('received_date');
		$sales_type = Input::get('sales_type');
		$is_received = 0;
		if($received_date){
			$is_received = 1;
			$received_date = strtotime($received_date);
		}else {
			$received_date = 0;
		}

		if($id && is_numeric($id)){
			$wh_order = new Wh_order($id);
			$wh_order->update(
				[
					'remarks' => $remarks,
					'is_received' => $is_received,
					'received_date' => $received_date,
					'gen_sales_type' => $sales_type
				],$id);

			$sales = new Sales();
			if($wh_order->data()->payment_id){
				$sales->updateSalestype($wh_order->data()->payment_id, $sales_type);
			}


			echo "Updated successfully.";

		}
	}
	function getPrevConsumable(){
		$member_id = Input::get('member_id');
		if($member_id){
			$wh_order = new Wh_order();
			$user = new User();
			$list = $wh_order->getOrders($user->data()->company_id,0,$member_id,0,0,4);
			if($list){
				echo "<table class='table'>";
				echo "<thead>";
				echo "<tr><th>Id</th><th>Invoice</th><th>Dr</th><th>PR</th><th>Total</th><th>Remarks</th></tr>";
				echo "</thead>";
				echo "<tbody>";
				foreach($list as $l){
					$rem = ($l->remarks)  ? $l->remarks : "<i class=' fa fa-ban'></i>";
					echo "<tr>";
					echo "<td>$l->id</td>";
					echo "<td>$l->invoice</td>";
					echo "<td>$l->dr</td>";
					echo "<td>$l->pr</td>";
					echo "<td>$l->total_price</td>";
					echo "<td>$rem</td>";

					echo "</tr>";
				}
				echo "</tbody>";
				echo "</table>";
			} else {
				echo "No record.";
			}
		} else {
			echo "No record.";
		}
	}
	function topItemBranch(){
		$branch_id = Input::get('branch_id');
		$month = Input::get('month');
		$sort = Input::get('sort');
		$byCateg = Input::get('by_categ');
		$limit = Input::get('limit');
		$whorder = new Wh_order();

		$sort_by ="desc";
		$group_by =" od.item_id ";

		if($sort == 1){
			$sort_by="asc";
		}

		if($byCateg == 1){
			$group_by = " i.category_id ";
		}

		$dt1 =0;
		$dt2 = 0;
		if($month){
			$explode = explode('-',$month);
			$monthNum  = $explode[0];
			$dateObj   = DateTime::createFromFormat('!m', $monthNum);
			$monthName = $dateObj->format('F');
			$dt = $monthName . " 1, " . $explode[1];
			$dt1 = strtotime($dt);
			$dt2 = strtotime($dt . " 1 month -1 sec");
		} else {
			$dt = date('F Y');
			$dt1 = strtotime($dt);
			$dt2 = strtotime($dt . " 1 month -1 sec");
		}
		$list = $whorder->topItemBranch($branch_id,$dt1,$dt2,$sort_by,$group_by,$limit);

		$arr = [];

		if($list) {

			foreach($list as $bb) {
				if($byCateg == 1){
					$lbl = $bb->category_name;
					$desc="";
				} else {
					$lbl = $bb->item_code;
					$desc= "<span class='text-danger span-block'>".  $bb->description."</span>";
				}
				$obj['y'] = $lbl;
				$obj['description'] = $desc;
				$obj['a'] = formatQuantity($bb->total_qty,true);
				array_push($arr, $obj);
			}
		}
		if($arr) {

			echo json_encode($arr);
		} else {
			echo json_encode(array('error' => true));
		}
	}
	function monthlyDelivered(){
		$branch_id = Input::get('branch_id');
		$whorder = new Wh_order();
		$user = new User();
		// base on branch
		if(!$branch_id) $branch_id = $user->data()->branch_id;
		$list = $whorder->monthlyDelivered($user->data()->company_id,$branch_id);

		$arr = [];

		if($list) {

			foreach($list as $bb) {
				$monthNum  = $bb->m;
				$dateObj   = DateTime::createFromFormat('!m', $monthNum);
				$monthName = $dateObj->format('F') . " " . $bb->y;
				$obj['y'] = $monthName;
				$obj['a'] = $bb->total_count;
				array_push($arr, $obj);
			}
		}

		if($arr) {
			echo json_encode($arr);
		} else {
			echo json_encode(array('error' => true));
		}

	}

	function checkStockPending(){
		$pending = Input::get('pending');
		$branch_id = Input::get('branch_id');
		$member_id = Input::get('member_id');
		$pending = json_decode($pending);
		if($pending) {
			$valid_items = [];
			foreach($pending as $p){
				if(isset($p->is_use) && $p->is_use){
					$ret = getAdjustmentPrice($branch_id,$p->item_id, $member_id,$p->qty);
					$split = explode("||",$ret);
					if($split[2] == 1){
						$prod = new Product($p->item_id);
						$pricecls = $prod->getPrice($p->item_id);
						$price = $pricecls->price;
						$price = $price + $split[0];
						$total = ($price * $p->qty);
						$adjusted_total = $total + $split[1];
						$is_bundle = $prod->data()->is_bundle;
						$item_code ="<p>" .$prod->data()->item_code. "<small style='display:block' class='text-danger'>" .$prod->data()->description. "</small></p>";

						$valid_items[] = ['item_id' => $p->item_id,
								'qty'=> $p->qty,
								'item_code'=> $item_code,
								'price'=> number_format($price,2,".",""),
								'total' => number_format($adjusted_total,2,".",""),
								'adjustmentmem'=> number_format( $split[1],2,".",""),
								'is_bundle' => $is_bundle,
								'item_count'=> $split[3],
								'remaining' => $split[4],
								'is_use' => $p->id
						];
					}

				}
			}
			echo json_encode($valid_items);
		}
	}
	function getMemberPendingOrder(){
		$request = Input::get('request');
		$request  = json_decode($request);
		if($request->member_id && $request->branch_id){
			$wh = new Wh_order_pending();
			$details = $wh->getPending($request->member_id,$request->branch_id);
			if($details){
				echo json_encode(['success' => true, 'details' => $details]);
			} else {
				echo json_encode(['success' => false]);
			}
		}
	}
	function usePrevOrder(){

		$id = Input::get('id');
		if($id && is_numeric($id)){
			$wh_order = new Wh_order($id);
			$user = new User();
			if(true){ // $user->data()->id == $wh_order->data()->user_id
				$whorder = new Wh_order_details();
				$branch_id = $wh_order->data()->branch_id;
				$member_id = $wh_order->data()->member_id;
				$member_name = "";
				if($member_id){
					$memcls = new Member($member_id);
					$member_name = $memcls->data()->lastname;
				}

				$main_data = [
					'member_id' => $member_id,
					'member_name' => $member_name,
					'branch_id' => $branch_id,
					'price_group_id' => $wh_order->data()->price_group_id,
				];
				$orders = $whorder->getOrderDetails($id);
				$arr_ret = [];
				$arr_msg = "";
				if($orders){
					foreach($orders as $order){
						$valid = isInventoryValid($branch_id,$order->item_id,$order->qty);
						if($valid){
							$adjusted_price = $order->adjusted_price;
							$total = $order->qty * $adjusted_price;
							$order->total = number_format($total,2,'.','');
							$order->adjusted_total = number_format($total + $order->member_adjustment,2);
							$items  = "<p>" .$order->item_code.  "<small style='display:block' class='text-danger'>" .$order->description.  "</small></p>";
							$order->item_code = $items;
							$order->remaining = $valid;
							$arr_ret[]= $order;
						} else {
							$arr_msg .= $order->item_code . ", ";
						}
					}
					if($arr_msg){
						$arr_msg = rtrim($arr_msg,", ");
						$arr_msg = "Not Valid Item(s): <br>" . $arr_msg;
					}

				}
				$data=  ['main_data' => $main_data, 'details' => $arr_ret,'msg'=>$arr_msg];
				echo json_encode($data);
			}

		}
	}
	function getAdjustmentPrice($branch_id=0,$item_id = 0, $member_id = 0,$qty=0){
		$adjustment_class = new Item_price_adjustment();
		if($branch_id && $item_id && $member_id && $qty){
			$is_ret = true;
		} else {
			$is_ret = false;
			$branch_id = Input::get('branch_id');
			$item_id = Input::get('item_id');
			$member_id = Input::get('member_id');
			$qty = Input::get('qty');
		}




		$nadj = 0;
		$alladj = 0;
		$ctr_item = 1;
		$_SESSION['cart_item_counter'] = $ctr_item;
		// get current order not get stock
		// get stock
		// compare qty
		$valid = 0;
		$final_message = "";
		$remaining = 0;
		if($branch_id && $item_id && $qty){

			$availability = getReservedStocks($item_id,$branch_id,$qty);
			if($availability && $availability['message']){
				if(!$availability['success']){
					$final_message =  $availability['message'];
				} else {
					$valid = 1;
				}
				$remaining = $availability['remaining'];
			}
			if(Configuration::getValue('strict_order') == 2){
				$valid = 1;
			}
			if($member_id){
				$memberTerms = new Member_term();
				$memadj =$memberTerms->getAdjustment($member_id,$item_id);

				if(count($memadj)){
					$alladjInd = 0;
					$alladjAbove = 0;
					foreach($memadj as $m){
						$madj = $m->adjustment;

						if($m->type == 1){ // for every
							$x = floor($qty / $m->qty);
							$madj = $madj * $x;
							$alladjInd += $madj;
						} else if ($m->type == 2){ // above qty

							if($qty >= $m->qty){
								if($m->discount_type == 0){
									$alladjAbove += $madj;
								} else {
									$madj = $madj * $qty;
									$alladjAbove += $madj;
								}
							}


						}
					}
					if($alladjAbove){
						$alladj = $alladjAbove;
					} else if($alladjInd){
						$alladj = $alladjInd;
					}
				}
			}
			$adj = $adjustment_class->getAdjustment($branch_id,$item_id);
			if(isset($adj->adjustment)){
				$nadj += $adj->adjustment;
			} else {
				$nadj += 0;
			}
		} else {
			$nadj += 0;
		}
		$_SESSION['cart_item_counter'] = ($_SESSION['cart_item_counter'])? $_SESSION['cart_item_counter'] : 1;
		if($is_ret){
			return  $nadj . "||" . $alladj. "||".$valid. "||" . $_SESSION['cart_item_counter'] . "||" .$remaining. "||" . $final_message;
		} else {
			echo $nadj . "||" . $alladj. "||".$valid. "||" . $_SESSION['cart_item_counter'] . "||" .$remaining. "||" . $final_message;
		}

	}

	function branchHasMember(){
		$branch_id = Input::get('branch_id');
		$arr = [];
		$mem = [];
		$branches = [];
		if($branch_id){

			$branch = new Branch($branch_id);
			$my_branch = $branch->branchMember($branch_id);

			if(isset($my_branch->member_id) && !empty($my_branch->member_id) && $my_branch->member_id != 0){
				$mem['id'] = $my_branch->member_id;
				$mem['name'] = $my_branch->lastname;
			}

			$arr['member'] = $mem;

			if($branch->data()->branch_tag_order){
				$exploded = explode(',',$branch->data()->branch_tag_order);
				if($exploded){
					$in_id = "";
					foreach($exploded as $ex){
						$id = (int) $ex;
						if($id){
							$in_id .= $ex . ",";
						}
					}
					$in_id = rtrim($in_id,',');
					$list = $branch->canOrderTo($in_id);
					if($list){
						foreach($list as $b){
							$branches[] = ['branch_id' => $b->id, 'branch_name' => $b->name];
						}
						$arr['branches'] = $branches;
					}
				}
			} else {
				$allbranch = $branch->get_active('branches',['1','=','1']);

				if($allbranch){
					foreach($allbranch as $mbranch){
						if($mbranch->id == $branch_id) continue;
						$branches[] = ['branch_id' => $mbranch->id, 'branch_name' => $mbranch->name];
					}
					$arr['branches'] = $branches;
				}

			}
		}
		echo json_encode($arr);
	}
	function approveReserveOrder(){
		$order_id = Input::get('order_id');
		$now =  time();
		if($order_id && is_numeric($order_id)){
			if(Configuration::getValue('order_skip_reserve') == 1){
				$update_arr =array('reserved_date' => $now,'is_reserve' => 0);
			} else {
				$update_arr = array('reserved_date' => $now);
			}
			$wh = new Wh_order();
			$wh->update($update_arr,$order_id);
			echo json_encode(['success' => true]);
		} else {
			echo json_encode(['success' => false]);
		}
	}
	function approveWalkInOrder(){
		$order_id = Input::get('order_id');

		if($order_id && is_numeric($order_id)){
			$update_arr = array('for_approval_walkin' => 0);
			$wh = new Wh_order();
			$wh->update($update_arr,$order_id);
			echo json_encode(['success' => true]);
		} else {
			echo json_encode(['success' => false]);
		}
	}
	function getOwnedBranch(){
		$member_id = Input::get('member_id');
		$arr = [];
		$arr_stat = [];
		$arr_credit = [];
		if($member_id){
			$user = new User();
			$memdetails = new Member($member_id);
			$branch = new Branch();
			$my_branch = $branch->getMemberBranch($member_id);

			$station = new Station();
			$my_stations = $station->getStationByMember($member_id);

			$member_credit = new Member_credit();
			$credit_list = $member_credit->getPendingCredit($member_id);
			if($credit_list){
				foreach($credit_list as $c){
					$c->remaining = $c->amount - $c->amount_paid;
					$arr_credit[] = $c;
				}
			}
			if($my_branch){
				foreach($my_branch as $b){
					$arr[] = ['id'=>$b->id,'name' => $b->name];
				}
			}
			if($my_stations){
				foreach($my_stations as $b){
					$arr_stat[] = ['id'=>$b->id,'name' => $b->name];
				}
			}
			$price_group_id = 0;
			if(Configuration::getValue('price_group') == 1 && $user->hasPermission('price_group_flex')){
				$pg = new Member_price_group();
				$res =$pg->getPriceGroup($member_id);
				if(isset($res->price_group_id)){
					$price_group_id = $res->price_group_id;
				}
			}

			$pernal_address='N/A';
			$region ='N/A';
			$terms ='N/A';
			$contact_number ='N/A';
			$credit_limit ='N/A';

			if(isset($memdetails->data()->personal_address) && $memdetails->data()->personal_address){
				$pernal_address = $memdetails->data()->personal_address;
			}
			if(isset($memdetails->data()->firstname) && $memdetails->data()->firstname){
				$contact_person = ucwords($memdetails->data()->firstname . " " . $memdetails->data()->middlename);
			}


			if(isset($memdetails->data()->region) && $memdetails->data()->region){
				$region = $memdetails->data()->region;
			}

			if(isset($memdetails->data()->terms) && $memdetails->data()->terms){
				$terms = $memdetails->data()->terms;
			}

			if(isset($memdetails->data()->contact_number) && $memdetails->data()->contact_number){
				$contact_number = $memdetails->data()->contact_number;
			}

			if(isset($memdetails->data()->credit_limit) && $memdetails->data()->credit_limit){
				$credit_limit = $memdetails->data()->credit_limit;
			}



		}

		echo json_encode([
							'branches' =>$arr,
							'stations' => $arr_stat,
							'credits' => $arr_credit,
							'price_group_id' => $price_group_id,
							'is_hold' => $memdetails->data()->is_blacklisted,
							'personal_address' => $pernal_address,
							'region' => $region,
							'terms' => $terms,
							'contact_number' => $contact_number,
							'contact_person' => $contact_person,
							'credit_limit' => $credit_limit
						]
			);



	}
	function saveBackload(){
		$id = Input::get('order_id');
		$orders = Input::get('orders');
		$backload_child = Input::get('backload_child');
		$consumable_remarks = "";
		if(is_numeric($id) && $id){
			$orders = json_decode($orders);
			$credit_amount = 0;
			$wh_order = new Wh_order($id);
			$user = new User();
			$arr_insert = [];
			foreach($orders as $order){

				$member_adjustment = $order->member_adjustment;
				$ind_mem_adj = 0;
				$racks =  $order->racking;
				$backload_qty = $order->back_qty;

				if($member_adjustment){
					$ind_mem_adj = $member_adjustment / $order->qty;
				}
				$ind_price = str_replace(',','',$order->adjusted_price) + $ind_mem_adj;
				$credit_amount += str_replace(',','',$ind_price * $backload_qty);

				if($racks){
					if($order->is_bundle == 1){
						$bundle = new Bundle();
						$list = $bundle->getBundleItem($order->item_id);
						if($list){
							$racks = json_decode($racks,true);
							foreach($list as $bun){

								$child_id = $bun->item_id_child;
								$child_qty = $bun->child_qty;
								$bundle_racks = json_decode($racks[$child_id]);
								$to_back_qty = $child_qty * $backload_qty;
								if($bundle_racks){
									foreach($bundle_racks as $to_rack){
										if($to_back_qty){
											if($to_rack->qty  >= $to_back_qty){
												$arr_insert[] = ['rack_id' => $to_rack->rack_id,'qty'=>$to_back_qty,'item_id'=>$child_id];
												$to_back_qty = 0;
											} else {
												$arr_insert[] = ['rack_id' => $to_rack->rack_id,'qty'=>$to_rack->qty,'item_id'=>$child_id];
												$to_back_qty -= $to_rack->qty;
											}
										}
									}
								}
							}
						}
					} else {
						$racks = json_decode($racks);
						if(count($racks)){
							$temp_backqty = $backload_qty;
							foreach($racks as $rack){
								if($temp_backqty){
									if($rack->qty  >= $temp_backqty){
										$arr_insert[] = ['rack_id' => $rack->rack_id,'qty'=>$temp_backqty,'item_id'=>$order->item_id];
										$temp_backqty = 0;
									} else {
										$arr_insert[] = ['rack_id' => $rack->rack_id,'qty'=>$rack->qty,'item_id'=>$order->item_id];
										$temp_backqty -= $rack->qty;
									}
								}
							}
						} else {
							$arr_insert[] = ['rack_id' => 0,'qty'=> $backload_qty,'item_id'=>$order->item_id];
						}
					}
				}
				$backload_child = json_decode($backload_child);
				if($backload_child && count($backload_child)){
					$back_arr = [];
					$to_ret = [];
					foreach($backload_child as $ind_backload){
						$rem = $ind_backload->orig_qty - $ind_backload->qty;
						// receive inventory
						if($ind_backload->qty > 0){
							$arr_insert[] = ['rack_id' => $ind_backload->rack_id,'qty'=>$ind_backload->qty,'item_id'=>$ind_backload->item_id_child];
						}

						$back_arr[$ind_backload->id][$ind_backload->item_id_child][] = [
							'rack_id' => $ind_backload->rack_id,
							'qty' => $rem,
							'rack' => $ind_backload->rack,
							'stock_man' => '',
							'rack_description' => ''
						];

					}

					if(count($back_arr)){
						foreach($back_arr as $i => $arr){

							$id = $i;
							$allracking = [];
							foreach($arr as $c_id => $c_rack){
								$allracking[$c_id] = json_encode($c_rack);
							}

							$racks_new = json_encode($allracking);

							$details_cls = new Wh_order_details();

							$details_cls->update(['racking' => $racks_new],$id);

						}
					}
				}
				$remarks = "";
				if($order->backload_remarks){
					$remarks = $order->backload_remarks;
				}
				// mark it as back load
				$wh_det = new Wh_order_details();
				$now = time();
				$wh_det->update(array('backload_qty'=>$backload_qty,'backload_date'=>$now,'backload_remarks' => $remarks ),$order->id);
			}

			// insert to receiving
			if($arr_insert){
				// create transfer
				$transfer = new Transfer_inventory_mon();
				$now = time();
				$transfer->create(array(
					'status' => 1,
					'is_active' =>1,
					'branch_id' =>$wh_order->data()->branch_id,
					'company_id' =>$user->data()->company_id,
					'created' => $now,
					'modified' => $now,
					'from_where' => 'From backload',
					'payment_id' => $wh_order->data()->payment_id
				));
				$lastid = $transfer->getInsertedId();
				foreach($arr_insert as $insert){
					$transfer_details = new Transfer_inventory_details();
					$transfer_details->create(array(
						'transfer_inventory_id' => $lastid,
						'rack_id_from' => 0,
						'rack_id_to' => $insert['rack_id'],
						'item_id' =>$insert['item_id'],
						'qty' =>$insert['qty'],
						'is_active' => 1
					));

					$prodcheck = new Product($insert['item_id']);
					$consumable_remarks .= $prodcheck->data()->item_code . "<br>" .  $prodcheck->data()->description . "<br>Qty: " .$insert['qty'] . "<br>";


				}
			}
			if($wh_order->data()->member_id){
				$payment_id = $wh_order->data()->payment_id;
				// check if it has credit balance
				$member_credit  = new Member_credit();
				$credit_list = $member_credit->getMemberCreditByPaymentID($payment_id);
				$add_con = true;
				if($credit_list){
					if(isset($credit_list->amount) && isset($credit_list->amount_paid)){
						if($credit_list->amount != $credit_list->amount_paid){
							$diff = $credit_list->amount - $credit_list->amount_paid;
							if($diff >= $credit_amount){
								// okay to deduct to credit
								$prev_credit_amount =  $credit_list->amount;
								$prev_amount_paid =  $credit_list->amount_paid;
								$add_to_deduction = $credit_amount;
								if($prev_amount_paid == 0){
									// update credit amount only
									$member_credit->update(array(
										'amount' => ($prev_credit_amount - $credit_amount)
									),$credit_list->id);
								} else {
									// update credit amount and amount paid
									// 104000, 103600, 4400, 4600,15000
									if($prev_amount_paid < $credit_amount){
										//$diff_credit = $credit_amount - $prev_amount_paid;
										//$add_to_deduction += $diff_credit;
										//$prev_amount_paid = 0;
										$member_credit->update(array(
											'amount' => ($prev_credit_amount - $credit_amount),
											'amount_paid' => $prev_amount_paid
										),$credit_list->id);
									} else {
										$member_credit->update(array(
											'amount' => ($prev_credit_amount - $credit_amount),
											'amount_paid' => ($prev_amount_paid)
										),$credit_list->id);
									}
								}
								$pdeduct = new Deduction();
								$deduction_amount = $add_to_deduction;
								$member_deduction_remarks = "Backload item";
								$pdeduct->create(array(
									'amount' =>$deduction_amount,
									'is_active' => 1,
									'created' => time(),
									'remarks' => $member_deduction_remarks,
									'payment_id' => $payment_id,
									'member_id' =>$wh_order->data()->member_id
								));
								$add_con = false;
							}
						}
					}
				}
				if($add_con)
					addMemberConsumable($wh_order->data()->member_id,$credit_amount,$consumable_remarks);



			}
			echo "Back load complete. Please check the item(s) in receive inventory.";
		}
	}
	function addMemberConsumable($member_id = 0, $amount = 0,$consumable_remarks=''){
		$has_ret = false;
		if(!$member_id || !$amount)
		{
			$has_ret = true;
			$member_id = Input::get('member_id');
			$amount = Input::get('amount');
		}
		if($member_id && $amount){
			$user = new User();
			$now = time();
			$nextYear = strtotime(date('F Y') . " 1 year");
			$company_id = $user->data()->company_id;
			$item_id = 0; // static item id
			// add payment
			$payment = new Payment();
			$payment->create(array(
				'created' => $now,
				'company_id' => $company_id,
				'is_active' => 1
			));
			$paymentLastId= $payment->getInsertedId();
			// add service
			$service = new Service();
			$service->create(array(
				'start_date' => $now,
				'end_date' => $nextYear,
				'company_id' => $company_id,
				'member_id' => $member_id,
				'item_id' => $item_id,
				'consumable_qty' => 10000,
				'payment_id' => $paymentLastId
			));
			$serviceLastId = $service->getInsertedId();
			// add consumable amount
			$consumable_amount = new Consumable_amount();
			$consumable_remarks = "From backload:<br>" . $consumable_remarks;
			$consumable_amount->create(array(
				'member_id' => $member_id,
				'item_id' => $item_id,
				'payment_id' => $paymentLastId,
				'amount' => $amount,
				'service_id' => $serviceLastId,
				'created' => $now,
				'modified' => $now,
				'is_active' => 1,
				'remarks' => $consumable_remarks
			));
			if($has_ret){
				echo "Added Successfully";
			}
		}
	}
	function showBackloadWh(){
		$id = Input::get('order_id');
		if(is_numeric($id) && $id){
			$whorder = new Wh_order_details();
			$orders = $whorder->getOrderDetails($id);
			$arr = [];
			foreach($orders as $order){
				$order->back_qty = 0;
				$order->qty = formatQuantity($order->qty,true);
				$order->backload_qty = formatQuantity($order->backload_qty,true);
				$total = $order->qty * $order->adjusted_price;
				$order->total = number_format($total,2);
				$order->machine = ($order->item_id_set)  ? 1 : 0;
				$order->adjusted_price = number_format($order->adjusted_price,2);
				$order->adjusted_total = number_format(($total + $order->member_adjustment),2);
				$racking = json_decode($order->racking);
				$rackhtml ='';
				if($order->is_bundle == 0){
					foreach($racking as $r){
						$rackhtml .= "<p class='text-danger'>$r->rack <i class='fa fa-long-arrow-right'></i> " .formatQuantity($r->qty) ."</p>";
					}
				} else {
					$bundle = new Bundle();
					$bundle_list = $bundle->getBundleItem($order->item_id);
					$bundleracking = json_decode($order->racking,true);
					$retrackhtml='';
					if($bundle_list){
						foreach($bundle_list as $bl){
							$thisracking = json_decode($bundleracking[$bl->item_id_child]);
							$retrackhtml  .= "<p>$bl->description</p>";
							foreach($thisracking as $racked){
								$retrackhtml .= "<p class='text-danger'>$racked->rack <i class='fa fa-long-arrow-right'></i> Qty: $racked->qty Backload Qty: <input style='width: 50px;' type='text' class='tobackload_bundle' data-rack='$racked->rack' data-id='$order->id' data-item_id_parent='$order->item_id' data-qty='$racked->qty' data-item_id_child='$bl->item_id_child' data-rack_id='$racked->rack_id'  value='0'></p>";

							}
						}
					}
					$rackhtml = $retrackhtml;
				}
				$order->rackhtml = $rackhtml;
				$arr[] = $order;
			}
			echo json_encode($arr);
		}



	}
	function getBackloadList(){
		// pages,
		$user = new User();
		$order = new Wh_order();
		$search = Input::get('search');
		$b = Input::get('branch_id');
		$m = Input::get('member_id');
		$status = Input::get('status');
		$user_id = Input::get('user_id');
		$from = Input::get('txtFrom');
		$to = Input::get('txtTo');
		$search = trim($search);
		$cid = $user->data()->company_id;
		$args = Input::get('page');
		?>

		<div id="no-more-tables">
			<div class="table-responsive">
				<table class='table' id='tblSales'>
					<thead>
					<tr>
						<TH>ID</TH>
						<th><?php echo INVOICE_LABEL; ?></th>
						<th><?php echo DR_LABEL; ?></th>
						<th><?php echo PR_LABEL; ?></th>
						<TH>Branch</TH>
						<th>Request by</th>
						<TH>Member</TH>
						<TH>Created At</TH>
						<TH>Item Code</TH>
						<TH>Description</TH>
						<TH>QTY</TH>
					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 50;
						$countRecord = $order->countRecordBackload($cid, $search, $b,$m,$status,$user_id,$from,$to);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_inv = $order->get_record_backload($cid, $start, $limit, $search, $b,$m,$status,$user_id,$from,$to);
						getpagenavigation($page, $total_pages, $limit, $stages);
						if($company_inv) {
							foreach($company_inv as $s) {

								?>
								<tr>
									<td style='border-top:1px solid #ccc;' data-title="ID"><strong><?php echo $s->wh_orders_id?></strong></td>
									<td style='border-top:1px solid #ccc;' data-title="<?php echo INVOICE_LABEL; ?>"><?php echo escape($s->invoice)?></td>
									<td style='border-top:1px solid #ccc;' data-title="<?php echo DR_LABEL; ?>"><?php echo escape($s->dr)?></td>
									<td style='border-top:1px solid #ccc;' data-title="<?php echo PR_LABEL; ?>"><?php echo escape($s->pr)?></td>
									<td style='border-top:1px solid #ccc;' data-title="Branch"><?php echo escape($s->branch_name)?></td>
									<td style='border-top:1px solid #ccc;'  data-title="Request by" class='text-muted'><?php echo escape(ucwords($s->lastname . ", " . $s->firstname . " " . $s->middlename))?></td>
									<td style='border-top:1px solid #ccc;' data-title="Member"><?php echo escape(ucwords($s->mln . ", " . $s->mfn . " " . $s->mmn))?></td>
									<td style='border-top:1px solid #ccc;'  data-title="Created at"><?php echo date('m/d/Y',$s->created); ?></td>
									<td style='border-top:1px solid #ccc;'  data-title="Item Code"><?php echo $s->item_code; ?></td>
									<td style='border-top:1px solid #ccc;'  data-title="Created at"><?php echo $s->description; ?></td>
									<td style='border-top:1px solid #ccc;'  data-title="Qty"><strong class='text-danger'><?php echo formatQuantity($s->backload_qty); ?></strong></td>
								</tr>
								<?php
							}
						} else {
							?>
							<tr>
								<td colspan='8'><h3><span class='label label-info'>No Record Found...</span></h3></td>
							</tr>
							<?php
						}
					?>
					</tbody>
				</table>
			</div>
		</div>
		<?php
	}
	function getPendingMemberOrder(){
		// pages,
		$user = new User();
		$order = new Wh_order_pending();
		$search = Input::get('search');
		$search = trim($search);
		$cid = $user->data()->company_id;
		$args = Input::get('page');
		?>

		<div id="no-more-tables">
			<div class="table-responsive">
				<table class='table' id='tblSales'>
					<thead>
					<tr>
						<TH>ID</TH>
						<TH>Member</TH>
						<TH>Created At</TH>
						<TH>Item Code</TH>
						<TH>Description</TH>
						<TH>Qty</TH>
						<th>Status</th>
					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 50;
						$countRecord = $order->countRecord($cid, $search);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_inv = $order->get_record($cid, $start, $limit, $search);
						getpagenavigation($page, $total_pages, $limit, $stages);
						if($company_inv) {
							$status_arr = ['','Pending','Ordered'];
							foreach($company_inv as $s) {

								?>
								<tr>
									<td style='border-top:1px solid #ccc;' data-title="ID"><strong><?php echo $s->id?></strong></td>
									<td style='border-top:1px solid #ccc;' data-title="Member"><?php echo escape(ucwords($s->mln))?></td>
									<td style='border-top:1px solid #ccc;'  data-title="Created at"><?php echo date('m/d/Y',$s->created); ?></td>
									<td style='border-top:1px solid #ccc;'  data-title="Item Code"><?php echo $s->item_code; ?></td>
									<td style='border-top:1px solid #ccc;'  data-title="Description"><?php echo $s->description; ?></td>
									<td style='border-top:1px solid #ccc;'  data-title="Qty"><strong class='text-danger'><?php echo formatQuantity($s->qty); ?></strong></td>
									<td  style='border-top:1px solid #ccc;' data-title="Status"><?php echo $status_arr[$s->status]; ?></td>
								</tr>
								<?php
							}
						} else {
							?>
							<tr>
								<td colspan='6'><h3><span class='label label-info'>No Record Found...</span></h3></td>
							</tr>
							<?php
						}
					?>
					</tbody>
				</table>
			</div>
		</div>
		<?php
	}
	function saveOverPaymentOrder(){
		// over payment
		$cheque = Input::get('cheque');
		$credit = Input::get('credit');
		$banktransfer = Input::get('banktransfer');

		$op_payment_cash = Input::get('op_payment_cash');
		$op_payment_credit = Input::get('op_payment_credit');
		$op_payment_bt = Input::get('op_payment_bt');
		$op_payment_cheque = Input::get('op_payment_cheque');
		$terminal_id = Input::get('terminal_id');
		$member_id = Input::get('member_id');
		$remarks = Input::get('remarks');
		$user = new User();
		$scompany = $user->data()->company_id;
		$cashier_id = $user->data()->id;
		$payment_lastid = 0;
		$msg = "From Order";
		// over payment
		$user_credit = new User_credit();
		$total = 0;
		$lastid_credit ="";
		if($op_payment_cash){ // status  = 1

			$user_credit->create(array(
				'status' => 1,
				'json_data' => $op_payment_cash,
				'total' => $op_payment_cash,
				'company_id' => $scompany,
				'member_id' => $member_id,
				'remarks' => $remarks,
				'user_id' => $cashier_id,
				'from_tbl' => $msg,
				'is_active' => 1,
				'ref_id' => $payment_lastid,
				'is_used' => 0,
				'created' => time()
			));
			$lastid_credit .= $user_credit->getInsertedId() . ",";
		}
		if($op_payment_credit){ // status  = 2
			$user_credit->create(array(
				'status' => 2,
				'json_data' => $op_payment_credit,
				'total' => $credit,
				'company_id' => $scompany,
				'member_id' => $member_id,
				'remarks' => $remarks,
				'user_id' => $cashier_id,
				'from_tbl' => $msg,
				'is_active' => 1,
				'ref_id' => $payment_lastid,
				'is_used' => 0,
				'created' => time()
			));
			$lastid_credit .= $user_credit->getInsertedId() . ",";
		}
		if($op_payment_cheque){ // status  = 3
			$user_credit->create(array(
				'status' => 3,
				'json_data' => $op_payment_cheque,
				'total' => $cheque,
				'company_id' => $scompany,
				'member_id' => $member_id,
				'remarks' => $remarks,
				'user_id' => $cashier_id,
				'from_tbl' => $msg,
				'is_active' => 1,
				'ref_id' => $payment_lastid,
				'is_used' => 0,
				'created' => time()
			));
			$lastid_credit .= $user_credit->getInsertedId() . ",";
		}
		if($op_payment_bt){ // status  = 4
			$user_credit->create(array(
				'status' => 4,
				'json_data' => $op_payment_bt,
				'total' => $banktransfer,
				'company_id' => $scompany,
				'member_id' => $member_id,
				'remarks' => $remarks,
				'user_id' => $cashier_id,
				'from_tbl' => $msg,
				'is_active' => 1,
				'ref_id' => $payment_lastid,
				'is_used' => 0,
				'created' => time()
			));
			$lastid_credit .= $user_credit->getInsertedId() . ",";
		}
		$lastid_credit = rtrim($lastid_credit,",");

		$arr = [];
		if(is_numeric($member_id)){
			$mem = new Member($member_id);
			$thiscompany = $user->getCompany($user->data()->company_id);
			$member_name=$mem->data()->lastname;
			$mem_address = $mem->data()->personal_address;
			$company_name = $thiscompany->name;
			$company_address =$thiscompany->address;
			$company_contact_number =$thiscompany->contact_number;
			$company_email =$thiscompany->email;

			if(isset($mem->data()->id)){
				$arr= [
					'company_name' => $company_name,
					'company_address' => $company_address,
					'company_contact_number' => $company_contact_number,
					'company_email' => $company_email,
					'member_name' => $member_name,
					'mem_address' => $mem_address,
					'id' => $lastid_credit
				] ;
				echo json_encode($arr);
			}
		}

	}
	function getOverPayment(){
		$user_credit = new User_credit();
		$user_credits = $user_credit->getCredit(Input::get('member_id'));
		if(!$user_credits) $user_credits = [];
		echo "<input type='hidden' id='op_member_list' value='".json_encode($user_credits)."'>";
	}

	function saveTrans(){
		$pending = Input::get('pending');
		$pending = json_decode($pending);
		if($pending){
			// transfer mon
			$user = new User();
			$tranfer_mon = new Transfer_inventory_mon();
			$now = time();
			$tranfer_mon->create(array(
				'status' => 1,
				'is_active' =>1,
				'branch_id' =>$user->data()->branch_id,
				'company_id' =>$user->data()->company_id,
				'created' => $now,
				'modified' =>$now,
				'from_where' => 'From transfer'
			));
			$lastid = $tranfer_mon->getInsertedId();

			foreach($pending as $p){
				$rack_from = $p->rack_id_from;
				$rack_from  = explode(',',$rack_from);
				if($rack_from[0]){
					$tranfer_mon_details = new Transfer_inventory_details();
					$tranfer_mon_details->create(array(
						'transfer_inventory_id' => $lastid,
						'rack_id_from' => $rack_from[0],
						'rack_id_to' =>  $p->rack_id_to,
						'item_id' => $p->item_id,
						'qty' =>  $p->qty,
						'is_active' => 1
					));
				}
			}
			echo "Transfer request was sent successfully.";
		}
	}
	function updateOrderInfoSave(){
		$data = Input::get('data');
		$data = json_decode($data);
		$user = new User();
		if($data){
			$wh_order = new Wh_order();
			if($data->id && is_numeric($data->id)){
				$is_scheduled = $data->delivery_date;
				if($is_scheduled){
					$is_scheduled = strtotime($is_scheduled);
				} else {
					$is_scheduled = 0;
				}
				$wh_order->update(array(
					'is_scheduled' => $is_scheduled,
					'remarks' => $data->remarks,
					'client_po' => $data->client_po,
					'for_pickup' => $data->is_for_pickup,
					'rebate' => $data->rebate,
					'warranty_card_number' => $data->warranty_card_number,
					'shipping_company_id' => $data->shipping_company_id
				),$data->id);
				echo "Data updated successfully.";
			}
		}
	}

	function getReservedStocks($item_id = 0, $branch_id = 0,$qty=0){
		$msg = "";
		$count_item = 1;
		if($branch_id && $item_id && $qty){
			$item_cls = new Product($item_id);
			$composite = new Composite_item();
			$is_composite = $composite->hasSpare($item_id);
			if($item_cls->data()->is_bundle != 1 && !(isset($is_composite->cnt) && !empty($is_composite->cnt))){

				$set = remainingSet($item_id,$branch_id);

				if($set['remaining'] && $set['remaining'] >= $qty){
					return ['remaining' => $set['remaining'],'success' => true, 'message' => 'Stocks available'];
				} else {
					$msg = " Current Stock: "
						. formatQuantity($set['current_stock'])
						. " Pending Order: "
						.  formatQuantity($set['pending_order'])
						. " Pending in Service: "
						. formatQuantity($set['pending_service'])
						. " Available Stocks: "
						. formatQuantity($set['remaining']);

					$withDesign = "<ul class='list-group'>";
					$withDesign .= "<li class='list-group-item'>Current Quantity <strong>".formatQuantity($set['current_stock'])."</strong></li>";
					$withDesign .= "<li class='list-group-item'>Pending Order <strong>".formatQuantity($set['pending_order'])."</strong></li>";
					$withDesign .= "<li class='list-group-item'>Pending in Service <strong>".formatQuantity($set['pending_service'])."</strong></li>";
					$withDesign .= "<li class='list-group-item'>Available Stocks <strong>".formatQuantity($set['remaining'])."</strong></li>";
					$withDesign .= "</ul>";

					return ['remaining' => $set['remaining'],'success' => false, 'message' => $withDesign];
				}
			} else if ($item_cls->data()->is_bundle == 1){
				$bundle  = remainingBundle($item_id,$branch_id);
				$valid = 1;
				$arr_bundle = [];
				$rem = 0;
				foreach($bundle as $b){

					$needed_qty = $b['needed'] * $qty;
					if($b['remaining'] && $b['remaining'] >= $needed_qty){
						$arr_bundle[] = ['success' => true, 'message' => $b['item_code'] . ' *available'];

					} else {
						$msg = $b['item_code'] . " Needed: " . formatQuantity($needed_qty) . " Available: " . formatQuantity($b['remaining']). " Pending order: " . formatQuantity($b['pending_order']) . " Pending service: " . formatQuantity($b['pending_service']);
						$arr_bundle[] = ['success' => false, 'message' => $msg];

						$valid = 0;
					}
					$all = floor($b['remaining'] / $qty);
					if(!$rem || $rem > $all){
						$rem = $all;
					}
				}
				if($valid){
					return ['remaining' => $rem,'success' => true, 'message' => 'Stocks available'];
				} else {
					$finalmsg = "<ul class='list-group'>";
					foreach($arr_bundle as $arr){
						if($arr['success']){
							$finalmsg .= "<li class='list-group-item text-success'>". $arr['message'] ."</li>";
						} else {
							$finalmsg .= "<li class='list-group-item text-danger'>". $arr['message'] ."</li>";
						}
					}
					$finalmsg .= "</ul>";
					return ['remaining' => $rem,'success' => false, 'message' => $finalmsg];
				}
			} else if (isset($is_composite->cnt) && !empty($is_composite->cnt)){
				$_SESSION['machine_qty'] = 0;
				$com = remainingComposite($item_id,$branch_id);

				$valid = 1;
				$arr_bundle = [];
				$rem = 0;
				foreach($com as $b){
					$needed_qty = $b['needed'] * $qty;
					if($b['remaining'] && $b['remaining'] >= $needed_qty){
						$arr_bundle[] = ['success' => true, 'message' => $b['item_code'] . ' *available'];

					} else {
						$msg = $b['item_code'] . " Needed: " . formatQuantity($needed_qty) . " Available: " . formatQuantity($b['remaining']). " Pending order: " . formatQuantity($b['pending_order']) . " Pending service: " . formatQuantity($b['pending_service']);
						$arr_bundle[] = ['success' => false, 'message' => $msg];

						$valid = 0;
					}
					$all = floor($b['remaining'] / $qty);
					if(!$rem || $rem > $all){
						$rem = $all;
					}
				}
				if($_SESSION['machine_qty'] >= $qty ){
					$valid = 1;
					$rem = $_SESSION['machine_qty'];
				}
				if($valid){
					return ['remaining' => $rem,'success' => true, 'message' => 'Stocks available'];
				} else {
					$finalmsg = "<ul class='list-group'>";
					foreach($arr_bundle as $arr){
						if($arr['success']){
							$finalmsg .= "<li class='list-group-item text-success'>". $arr['message'] ."</li>";
						} else {
							$finalmsg .= "<li class='list-group-item' text-danger>". $arr['message'] ."</li>";
						}
					}
					$finalmsg .= "</ul>";
					return ['remaining' => $rem,'success' => false, 'message' => $finalmsg];
				}
			}
		}
		return false;
	}

	function remainingComposite($item_id = 0, $branch_id = 0){
		$composite = new Composite_item();
		$inv = new Inventory();
		$whorder = new Wh_order();
		$item_service = new Service_request_item();

		$spare_parts = $composite->getSpareparts($item_id);
		$arr_inv = [];
		if($spare_parts){
			//	$_SESSION['cart_item_counter'] = count($spare_parts);

			$assembled_qty = $inv->getAllQuantity($item_id,$branch_id);
			$ass_qty = 0;
			if(isset($assembled_qty->totalQty)){
				$ass_qty = $assembled_qty->totalQty ;
			}
			$_SESSION['machine_qty'] = $ass_qty;

			foreach($spare_parts as $spare){
				$pending_spare_qty = $whorder->pendingSpare($spare->item_id_raw,$branch_id);
				$stock_composite = $inv->getAllQuantity($spare->item_id_raw,$branch_id);
				$st_composite = 0;
				$current_pending_service = $item_service->getPendingRequest($spare->item_id_raw,$branch_id);
				$service_qty = 0;
				if(isset($stock_composite->totalQty)){
					$st_composite = $stock_composite->totalQty ;
				}
				if(isset($current_pending_service->service_qty)){
					$service_qty =$current_pending_service->service_qty ;
				}
				$remaining_composite = $st_composite - ($pending_spare_qty->pending_qty + $service_qty);
				if($remaining_composite < 0) $remaining_composite = 0;
				$arr_inv[] = ['item_code' => $spare->item_code,'item_id_child' => $spare->item_id_raw,'remaining' => $remaining_composite,'current_stock' => $st_composite,'pending_order' => $pending_spare_qty->pending_qty,'pending_service' => $service_qty,'needed' => $spare->qty];
			}
		}
		return $arr_inv;
	}
	function remainingBundle($item_id = 0, $branch_id = 0){
		$bundle = new Bundle();
		$bundles = $bundle->getBundleItem($item_id);
		$inv = new Inventory();
		$whorder = new Wh_order();
		$item_service = new Service_request_item();
		$arr_inv = [];
		if($bundles){
			$_SESSION['cart_item_counter'] = count($bundles);
			$user = new User();
			$rack_tags = new Rack_tag();
			$tags_ex = $rack_tags->get_tags_ex('wh_orders',$user->data()->company_id,$branch_id);
			if(isset($tags_ex->id) && !empty($tags_ex->id)){
				$excempt_tags = $tags_ex->tag_id;
			} else {
				$excempt_tags =0;
			}
			foreach($bundles as $bun){
				$pending_bundle_qty = $whorder->pendingBundles($bun->item_id_child,$branch_id,$excempt_tags);
				if($pending_bundle_qty && isset( $pending_bundle_qty->pending_qty )){
					$stock_bundle = $inv->getAllQuantity($bun->item_id_child,$branch_id,$excempt_tags);
					$current_pending_service = $item_service->getPendingRequest($bun->item_id_child,$branch_id);
					$st_bundle = 0;
					$service_qty = 0;
					if(isset($stock_bundle->totalQty)){
						$st_bundle = $stock_bundle->totalQty ;
					}
					if(isset($current_pending_service->service_qty)){
						$service_qty =$current_pending_service->service_qty ;
					}
					$remaining_bundle = $st_bundle - ($pending_bundle_qty->pending_qty + $service_qty);
					if($remaining_bundle < 0) $remaining_bundle = 0;

					$arr_inv[] = ['item_code'=> $bun->item_code,'item_id_child' => $bun->item_id_child,'remaining' => $remaining_bundle,'current_stock' => $st_bundle,'pending_order' => $pending_bundle_qty->pending_qty,'pending_service' => $service_qty,'needed' => $bun->child_qty];
				}
			}
		}
		return $arr_inv;
	}
	function remainingSet($item_id = 0, $branch_id = 0){
		$inv = new Inventory();
		$rack_tags = new Rack_tag();
		$user = new User();
		$tags_ex = $rack_tags->get_tags_ex('wh_orders',$user->data()->company_id,$branch_id);
		if(isset($tags_ex->id) && !empty($tags_ex->id)){
			$excempt_tags = $tags_ex->tag_id;
		} else {
			$excempt_tags =0;
		}
		$stock = $inv->getAllQuantity($item_id,$branch_id,$excempt_tags);
		$whorder = new Wh_order();
		$item_service = new Service_request_item();
		$current_pending_order = $whorder->getPendingOrder($item_id,$branch_id);
		$current_pending_service = $item_service->getPendingRequest($item_id,$branch_id);
		$cur = 0;
		$st = 0;
		$service_qty = 0;
		if(isset($stock->totalQty)){
			$st =$stock->totalQty ;
		}
		if(isset($current_pending_order->od_qty)){
			$cur =$current_pending_order->od_qty ;
		}
		if(isset($current_pending_service->service_qty)){
			$service_qty =$current_pending_service->service_qty ;
		}
		$remaining = $st - ($cur + $service_qty);
		return ['remaining' => $remaining, 'current_stock' => $st,'pending_order'=>$cur,'pending_service'=>$service_qty];
	}

	function truckSummary(){

		$date_from = Input::get('date_from');
		$date_to = Input::get('date_to');
		$dl = Input::get('dl');
		$border = "";
		if($dl == 1){
			$filename = "truck-" . date('m-d-Y-h-i-s') . ".xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");
			$border ="border=1";
		}
		$wh_order = new Wh_order();
		$list = $wh_order->getTruckSummary($date_from,$date_to);

		if($list){
			$total = 0;
			$total_count = 0;
			echo "<table $border class='table table-bordered' >";
			echo "<thead><tr><th>Name</th><th>Total Amount</th><th>PO count</th></tr></thead>";
			echo "<tbody>";
			foreach($list as $l){
				$total += $l->total_price;
				$total_count += $l->po_count;
				echo "<tr><td style='border-top:1px solid #ccc;'>$l->truck_name</td><td  style='border-top:1px solid #ccc;'>" . number_format($l->total_price,2). "</td><td  style='border-top:1px solid #ccc;'>$l->po_count</td></tr>";
			}
			echo "<tr><th style='border-top:1px solid #ccc;'></th><th style='border-top:1px solid #ccc;'>".number_format($total,2)."</th><th style='border-top:1px solid #ccc;'>$total_count</th></tr>";
			echo "</tbody>";

			echo "</table>";
		} else {
			echo "<p>No record.</p>";
		}

	}

	function batchApprove(){
		$arr = Input::get('arr');
		$truck_id = Input::get('truck_id');
		$driver = Input::get('driver_id');
		$dt = Input::get('sched');

		$arr = json_decode($arr,true);
		$user = new User();
		if(!$truck_id){
			//&& $truck_id && $driver && $dt
			$truck_id = 0;
		}
		if(!$driver){
			$driver = '';
		}
		if(!$dt){
			$dt =0;
		}
		if($arr){
			foreach($arr as $a){
				$order_id = $a;
				if($order_id){
					$whorder = new Wh_order($order_id);
					$now = time();
					$hasSales = false;
					$success = true;
					$msg = '';
					if($whorder->data()->member_id){
						$sales = new Sales();
						$det = $sales->getsinglesale($whorder->data()->payment_id);
						if($det){
							$hasSales = true;
						}
					} else {
						$hasSales = true;
					}

					if(!$hasSales){
						$success = false;
						$msg = 'This order is not included in sales. Please try to re-print invoice, dr or pr.';
					}

					if($success){
						if($whorder->data()->stock_out == 1){
							$status = 4;
						} else {
							$status = 3;
						}

						$scheduleOrder = new Wh_order_date();
						$schedule_date = strtotime($dt);
						$whorder->update(array(
							'status' => $status, // flowchange
							'approved_date' => $now,
							'truck_id' => $truck_id,
							'driver' => $driver,
							'is_scheduled' => $schedule_date,
						),$order_id);


						$scheduleOrder->create(array(
							'company_id' => $user->data()->company_id,
							'user_id' => $user->data()->id,
							'created' =>$now,
							'modified' =>$now,
							'is_active' =>1,
							'schedule_date' =>$schedule_date,
							'wh_order_id' =>$order_id
						));

					}


				}
			}
			echo "Batch process complete";
		} else {
			echo "Invalid information.";
		}

	}
	function batchDecline(){
		$arr = Input::get('arr');
		$arr = json_decode($arr,true);


		if($arr){
			foreach($arr as $a){
				$order_id = $a;
				if($order_id){

					$remarks  = '';
					if($order_id){
						$whorder = new Wh_order($order_id);
						$remarks = ($remarks) ? $remarks  : '';
						$whorder->update(array(
							'status' => 5,
							'cancel_remarks' => $remarks
						),$order_id);
						if($whorder->data()->payment_id){
							// cancel sales
							$sales = new Sales();
							$sales->cancelPayment($whorder->data()->payment_id);
						}

					}
				}
			}
			echo "Batch process complete";
		} else {
			echo "Invalid information.";
		}

	}

	function showCarrierManifest(){

		$type_name = Input::get('type_name');
		$lazada_arr = ['Ideas Philippines','Nextbook','Lazada Avision Philippines'];
		if(in_array($type_name,$lazada_arr)){
			lazadaManifest();
		} else {
			shopeeManifest();
		}


	}
	function lazadaManifest(){
		$arr = Input::get('arr');
		$arr = json_decode($arr,true);

		$wh =  new Wh_po_info();
		$ls = "";

		foreach($arr as $a){
			$client_po = str_replace('PO#: ','' ,$a);
			$ls .= "'".$client_po."',";
		}

		 $ls = rtrim($ls,",");

		$manifest = $wh->getManifest($ls);

		$html = "";

		if($manifest){
			$dt = date('F d Y');
			$html .= "<div style='page-break-before: always;'>";
			$total_items = 0;
			$arr_tracking = [];
			$arr_sum_items = [];
			$arr_items = [];
			$shipping_arr = [];
			$shipping_sum_arr = [];
			$total_amount = 0;
			$tracking_sum_arr =[];
			$parcel_summary_arr = [];
			$list_by_shipping = [];
			$tblStyle = "font-size:10px !important;";
			foreach($manifest as $m){

				$list_by_shipping[$m->shipping_company][] =  $m;

				$total_items  +=$m->qty;
				if(isset($shipping_sum_arr[$m->shipping_company])){
					$shipping_sum_arr[$m->shipping_company] += 1;
				} else {
					$shipping_sum_arr[$m->shipping_company] = 1;
				}

				if(isset($arr_sum_items[$m->seller_sku])){
					$arr_sum_items[$m->seller_sku] += 1;
				} else {
					$arr_sum_items[$m->seller_sku] = 1;
				}
				if(!isset($arr_items[$m->seller_sku])){
					$arr_items[$m->seller_sku] = $m->item_name;
				}


				$total_amount += ($m->unit_price * $m->qty);
				if(!in_array($m->tracking_number,$arr_tracking)){
					$arr_tracking[] = $m->tracking_number;
					if(isset($tracking_sum_arr[$m->shipping_company])){
						$tracking_sum_arr[$m->shipping_company] += 1;
					} else {
						$tracking_sum_arr[$m->shipping_company] = 1;
					}

				}

				if(!in_array($m->shipping_company,$shipping_arr)){
					$shipping_arr[] = $m->shipping_company;
				}

				if(isset($parcel_summary_arr[$m->tracking_number])){
					$parcel_summary_arr[$m->tracking_number]['count'] += $m->qty;
				} else {
					$parcel_summary_arr[$m->tracking_number] = ['count' => $m->qty,'shipping_company'=> $m->shipping_company,'client_po' => $m->client_po];
				}


			}



			$carrier_summary_html = "";
			$type_name = Input::get('type_name');
			$type_name = str_replace('Lazada','',$type_name);
			$type_name = str_replace('Shopee','',$type_name);
			$carrier_summary_html .= "<style>.table-bordered tbody > tr > th, .table-bordered tbody > tr > td, .table-bordered > thead > tr > th { border: 1px solid #000  !important;}</style>";
			$carrier_summary_html .= "<div style='width:48%;float:left;font-size:35px;'><strong>$type_name</strong></div>";
			$carrier_summary_html .= "<div style='width:48%;float:left;padding-top:20px;font-size:15px' class='text-right'>$dt</div>";
			$carrier_summary_html .= "<div style='clear:both;'></div><br>";

			$carrier_summary_html .= "<h3 class='text-center'>SUMMARY OF CARRIER MANIFEST</h3>";
			$carrier_summary_html .= "<table class='table table-bordered' style='width:350px;margin:0 auto;margin-top:100px;'>";
			$carrier_summary_html .= "<tbody>";
			$carrier_summary_html .= "<tr><th>Courier</th><th>Parcels</th></tr>";
			$total_carrier_parcels = 0;
			foreach($shipping_arr as $sh){
					$parcel_count = isset($tracking_sum_arr[$sh]) ? $tracking_sum_arr[$sh] : 0;
					$total_carrier_parcels += $parcel_count;
					$carrier_summary_html .= "<tr><th style=' border: 2px solid #000'>$sh</th><th style='width:120px;'>$parcel_count</th></tr>";
			}


			$carrier_summary_html .= "</tbody>";
			$carrier_summary_html .= "</table>";
			$carrier_summary_html .= "<br><br><br>";
			$carrier_summary_html .= "<p class='text-center'>Total of <span style='font-size:25px;'>$total_carrier_parcels</span> parcels only</p>";
			$carrier_summary_html .= "<p class='text-center'>(".convertNumberToWord($total_carrier_parcels)." parcels only )</p>";
			$carrier_summary_html .= "<br><br><br>";
			$carrier_summary_html .= "<div style='width:170px;float:left'><span class='text-right' style='display:inline-block;width:90px;'>Merchant:</span><span style='display:inline-block;border-bottom:1px solid #000;width:75px;'>&nbsp;</div>";
			$carrier_summary_html .= "<div style='width:170px;float:left'><span style='display:inline-block;width:90px;'>&nbsp;</span><span style='display:inline-block;width:75px;'>&nbsp;</div>";
			$carrier_summary_html .= "<div style='width:170px;float:left'><span class='text-right' style='display:inline-block;width:90px;'>Trucker:</span><span style='display:inline-block;border-bottom:1px solid #000;width:75px;'>&nbsp;</div>";
			$carrier_summary_html .= "<div style='width:170px;float:left'><span style='display:inline-block;width:90px;'>&nbsp;</span><span style='display:inline-block;width:75px;'>&nbsp;</div>";
			$carrier_summary_html .= "<div style='clear:both'></div>";

			$carrier_summary_html .= "<div style='width:170px;float:left'><span class='text-right' style='display:inline-block;width:90px;'>Signature:</span><span style='display:inline-block;border-bottom:1px solid #000;width:75px;'>&nbsp;</div>";
			$carrier_summary_html .= "<div style='width:170px;float:left'><span class='text-right' style='display:inline-block;width:90px;'>Date:</span><span style='display:inline-block;border-bottom:1px solid #000;width:75px;'>&nbsp;</div>";
			$carrier_summary_html .= "<div style='width:170px;float:left'><span class='text-right' style='display:inline-block;width:90px;'>Signature:</span><span style='display:inline-block;border-bottom:1px solid #000;width:75px;'>&nbsp;</div>";
			$carrier_summary_html .= "<div style='width:170px;float:left'><span class='text-right' style='display:inline-block;width:90px;'>Date:</span><span style='display:inline-block;border-bottom:1px solid #000;width:75px;'>&nbsp;</div>";
			$carrier_summary_html .= "<div style='clear:both'></div>";

			$carrier_summary_html .= "<div style='width:170px;float:left'><span class='text-right' style='display:inline-block;width:90px;'>Name:</span><span style='display:inline-block;border-bottom:1px solid #000;width:75px;'>&nbsp;</div>";
			$carrier_summary_html .= "<div style='width:170px;float:left'><span class='text-right' style='display:inline-block;width:90px;'>Time:</span><span style='display:inline-block;border-bottom:1px solid #000;width:75px;'>&nbsp;</div>";
			$carrier_summary_html .= "<div style='width:170px;float:left'><span class='text-right' style='display:inline-block;width:90px;'>Name:</span><span style='display:inline-block;border-bottom:1px solid #000;width:75px;'>&nbsp;</div>";
			$carrier_summary_html .= "<div style='width:170px;float:left'><span class='text-right' style='display:inline-block;width:90px;'>Time In:</span><span style='display:inline-block;border-bottom:1px solid #000;width:75px;'>&nbsp;</div>";
			$carrier_summary_html .= "<div style='clear:both'></div>";

			$carrier_summary_html .= "<div style='width:170px;float:left'><span class='text-right' style='display:inline-block;width:90px;'></span><span style='display:inline-block;width:75px;'>&nbsp;</div>";
			$carrier_summary_html .= "<div style='width:170px;float:left'><span class='text-right' style='display:inline-block;width:90px;'></span><span style='display:inline-block;width:75px;'>&nbsp;</div>";
			$carrier_summary_html .= "<div style='width:170px;float:left'><span class='text-right' style='display:inline-block;width:90px;'>Plate #:</span><span style='display:inline-block;border-bottom:1px solid #000;width:75px;'>&nbsp;</div>";
			$carrier_summary_html .= "<div style='width:170px;float:left'><span class='text-right' style='display:inline-block;width:90px;'>Time Out:</span><span style='display:inline-block;border-bottom:1px solid #000;width:75px;'>&nbsp;</div>";
			$carrier_summary_html .= "<div style='clear:both'></div>";


			$parcel_summary_html = "";
			$full_date = date('l F d, Y H:i:s A');

			foreach($shipping_arr as $sh){
				$parcel_summary_html .="<div style='page-break-before: always;'>";
				$parcel_summary_html .= "<div style='width:100%;border-top:20px solid #ccc;'>&nbsp;</div>";
				$parcel_summary_html .= "<h1><STRONG>SELLER</STRONG> CENTER</h1>";
				$parcel_summary_html .= "<p><strong>Merchant Name:</strong> $type_name</p>";
				$parcel_summary_html .= "<p>Carrier manifest printed on: ".$full_date."</p>";
				$parcel_summary_html .= "<br>";
				$parcel_summary_html .= "<table style='$tblStyle' class='table table-bordered table-condensed'>";
				$parcel_summary_html .= "<tbody>";
				$parcel_summary_html .= "<tr><th>Order Number</th><th>Parcel Tracking Number</th><th>Number of Pieces In Parcel</th></tr>";
				$cur_par_count = 0;
				foreach($parcel_summary_arr as $tnum => $tarr){
					if($tarr['shipping_company'] == $sh){
						$cur_par_count += 1;
						$parcel_summary_html .= "<tr><td>".$tarr['client_po']."</td><td>".$tnum."</td><td>".$tarr['count']."</td></tr>";
					}
				}
				$parcel_summary_html .="</tbody>";
				$parcel_summary_html .="</table>";
				$parcel_summary_html .="<br><br><br>";
				$parcel_summary_html .="<div style='width:400px;border:1px solid #000;'><strong>Total Number of Parcels: $cur_par_count</strong></div>";
				$parcel_summary_html .="<br>";
				$parcel_summary_html .="<table style='width:400px;' class='table table-bordered'>";
				$parcel_summary_html .="<tr><td style='font-size:10px;'>Date: $full_date</td></tr>";
				$parcel_summary_html .="<tr><td style='height:40px;display:block;font-size:10px;'>Signature of Driver - Trucker</td></tr>";
				$parcel_summary_html .="<tr><td style='height:40px;display:block;font-size:10px;'>Signature of Authorized Seller Personnel</td></tr>";

				$parcel_summary_html .="</table>";

				$parcel_summary_html .="<div style='margin-bottom:0px;' class='text-right'>$sh</div>";
				$parcel_summary_html .="</div>";
			}




			$item_summary_html = "<div style='page-break-before: always;'>";
			$item_summary_html .= "<table style='$tblStyle' class='table table-bordered table-condensed' >";
			$item_summary_html .= "<tbody>";
			$item_summary_html .= "<tr><th>Seller SKU</th><th>Item Name</th><th style='width:120px;'>Count Of Lazada SKU</th></tr>";

			foreach($arr_sum_items as $sku => $cnt){
				$item_name = $arr_items[$sku];
				$item_summary_html .= "<tr>";
				$item_summary_html .= "<td>$sku</td>";
				$item_summary_html .= "<td>".$item_name."</td>";
				$item_summary_html .= "<td style='width:80px;'>$cnt</td>";
				$item_summary_html .= "</tr>";
			}
			$item_summary_html .= "<tr><td colspan='2'>Total Items</td><td style='width:120px;'>$total_items</td></tr>";
			$item_summary_html .= "<tr><td colspan='2'>Total Parcels</td><td style='width:120px;'>".count($arr_tracking)."</td></tr>";
			$item_summary_html .= "</tbody>";
			$item_summary_html .= "</html>";
			$item_summary_html .= "<br>";
			$item_summary_html .= "<br>";
			$item_summary_html .= "<table style='width:100%;' >";
			$item_summary_html .= "<tbody>";
			$dt_name = date('l, F d, Y');
			$item_summary_html .= "<tr><th>PHP ".number_format($total_amount,2)."</th><th class='text-right'>$dt_name</th></tr>";
			$item_summary_html .= "</tbody>";
			$item_summary_html .= "</table>";

			$item_summary_html .= "</div>";

			$item_summary_html_2 = "<div style='page-break-before: always;'>";
			$item_summary_html_2 .= "<h4><strong>Merchant Name:</strong> $type_name</h4>";
			$item_summary_html_2 .= "<table style='$tblStyle' class='table table-bordered' >";
			$item_summary_html_2 .= "<tbody>";

			$item_summary_html_2 .= "<tr><th rowspan='2'>Seller SKU</th><th rowspan='2'>Item Name</th><th rowspan='2' style='width:70px;'>QTY</th><th colspan='2'>Signature</th><th rowspan='2'>Item Served and Pick up</th></tr>";
			$item_summary_html_2 .= "<tr><th>Merchant</th><th>Trucker</th></tr>";

			foreach($arr_sum_items as $sku => $cnt){
				$item_name = $arr_items[$sku];
				$item_summary_html_2 .= "<tr>";
				$item_summary_html_2 .= "<td>$sku</td>";
				$item_summary_html_2 .= "<td>".$item_name."</td>";
				$item_summary_html_2 .= "<td style='width:70px;'>$cnt</td>";
				$item_summary_html_2 .= "<td style='width:70px;'></td>";
				$item_summary_html_2 .= "<td style='width:70px;'></td>";
				$item_summary_html_2 .= "<td style='width:70px;'></td>";
				$item_summary_html_2 .= "</tr>";
			}
			$item_summary_html_2 .= "<tr><td colspan='2'>Total Items</td><td style='width:120px;'>$total_items</td><td></td><td></td><td></td></tr>";
			$item_summary_html_2 .= "<tr><td colspan='2'>Total Parcels</td><td style='width:120px;'>".count($arr_tracking)."</td><td></td><td></td><td></td></tr>";
			$item_summary_html_2 .= "</tbody>";
			$item_summary_html_2 .= "</html>";
			$item_summary_html_2 .= "<br>";
			$item_summary_html_2 .= "<br>";
			$item_summary_html_2 .= "<table style='width:100%;' >";
			$item_summary_html_2 .= "<tbody>";
			$dt_name = date('l, F d, Y');
			$item_summary_html_2 .= "<tr><th></th><th class='text-right'>$dt_name</th></tr>";
			$item_summary_html_2 .= "</tbody>";
			$item_summary_html_2 .= "</table>";

			$item_summary_html_2 .= "</div>";


			$shipping_list_html = "<div style='page-break-before: always;'>";
			$shipping_list_html .= "<h4><strong>Merchant Name:</strong> $type_name</h4>";
			$shipping_list_html .= "<table style='$tblStyle' class='table table-bordered table-condensed' style='font-size:11px !important;padding:2px;' >";
			$shipping_list_html .= "<tbody>";

			$shipping_list_html .= "<tr  style='font-size:12px;'><th rowspan='2'>Order #</th><th rowspan='2'>Tracking #</th><th rowspan='2'  style='width:30px;'>Seller SKU</th><th rowspan='2'>Item Name</th><th rowspan='2' style='width:30px;'>QTY</th><th colspan='2'>Signature</th><th rowspan='2'>Item Served and Pick up</th></tr>";
			$shipping_list_html .= "<tr  style='font-size:12px;'><th>Merchant</th><th>Trucker</th></tr>";
			$breakworkd = "overflow-wrap: break-word; word-wrap: break-word; -ms-word-break: break-all; word-break: break-all; word-break: break-word; -ms-hyphens: auto; -moz-hyphens: auto; -webkit-hyphens: auto; hyphens: auto;";
			$total_qty_list= 0;
			$total_parcel_list= 0;
			foreach($shipping_arr as $sh) {

				if(isset($list_by_shipping[$sh])&& count($list_by_shipping[$sh])){

					$temphtml = "";
					$tempqty= 0;
					$arr_temp = [];
					foreach($list_by_shipping[$sh] as $m){
						$tempqty += $m->qty;
						if(!in_array($m->tracking_number,$arr_temp)){
							$arr_temp[] = $m->tracking_number;
						}

						$temphtml .= "<tr ><td>$m->client_po</td><td style='width:100px;".$breakworkd."'>$m->tracking_number</td><td style='width:100px;".$breakworkd."'>$m->seller_sku</td><td >$m->item_name</td><td >$m->qty</td><td ></td><td ></td><td></td></tr>";
					}
					$tempparcelcount = count($arr_temp);
					$total_parcel_list += $tempparcelcount;
					$total_qty_list += $tempqty;
					$shipping_list_html .= "<tr><th colspan='4'>$sh</th><th>$tempqty</th><th colspan='3' class='text-center'>$tempparcelcount parcel".( $tempparcelcount > 1? "s":"" )."</th></tr>";
					$shipping_list_html .= $temphtml;

				}
			}

			$shipping_list_html .= "<tr><th colspan='4'>Grand Total</th><th>$total_qty_list</th><th colspan='3' class='text-center'>$total_parcel_list parcel".( $total_parcel_list > 1? "s":"" )."</th></tr>";
			$shipping_list_html .= "</tbody>";
			$shipping_list_html .= "</table>";
			$shipping_list_html .= "</div>";


			$html .= $carrier_summary_html;
			$html .= $parcel_summary_html;

			$html .= $item_summary_html_2;
			$html .= $shipping_list_html;
			$html .= $item_summary_html;

			echo $html ;

		}


	}
	function shopeeManifest(){

		 $arr = Input::get('arr');

		$arr = json_decode($arr,true);

		$wh =  new Wh_po_info();
		$ls = "";
		foreach($arr as $a){
			$client_po = str_replace('PO#: ','' ,$a);
			$ls .= "'".$client_po."',";
		}

		 $ls = rtrim($ls,",");

		$manifest = $wh->getManifest($ls);
		$html = "";

		if($manifest){

			$dt = date('F d Y');

			$html .= "<style>.table-bordered tbody > tr > th, .table-bordered tbody > tr > td, .table-bordered > thead > tr > th { border: 1px solid #000  !important;}</style>";
			$html .= "<div style='page-break-before: always;'><h3 class='text-center'>AVISION - PICK UP DATE: $dt</h3>";
			$html .= "<table class='table table-bordered table-condensed' style='font-size:10px !important;' >";
			$html .= "<tbody>";
			$html .= "<tr><th>DR</th><th>Order ID</th><th>Tracking Number</th><th>Receiver Name</th><th>Seller SKU</th><th>QTY</th><th>Unit Price</th></tr>";

			$total_items = 0;
			$arr_tracking = [];
			$arr_sum_items = [];
			$arr_items = [];
			$shipping_arr = [];
			$shipping_sum_arr = [];
			$total_amount = 0;

			foreach($manifest as $m){
				$total_items  +=$m->qty;
				if(isset($shipping_sum_arr[$m->shipping_company])){
					$shipping_sum_arr[$m->shipping_company] += 1;
				} else {
					$shipping_sum_arr[$m->shipping_company] = 1;
				}
				if(isset($arr_sum_items[$m->seller_sku])){
					$arr_sum_items[$m->seller_sku] += 1;
				} else {
					$arr_sum_items[$m->seller_sku] = 1;
				}
				if(!isset($arr_items[$m->seller_sku])){
					$arr_items[$m->seller_sku] = $m->item_name;
				}

				$html .= "<tr>";
				$html .= "<td>$m->dr</td>";
				$html .= "<td>$m->client_po</td>";
				$html .= "<td>$m->tracking_number</td>";
				$html .= "<td>$m->member_name</td>";
				$html .= "<td>$m->seller_sku</td>";
				$html .= "<td>$m->qty</td>";
				$html .= "<td>$m->unit_price</td>";
				$html .= "</tr>";
				$total_amount += ($m->unit_price * $m->qty);
				if(!in_array($m->tracking_number,$arr_tracking)){
					$arr_tracking[] = $m->tracking_number;
				}
				if(!in_array($m->shipping_company,$shipping_arr)){
					$shipping_arr[] = $m->shipping_company;
				}

			}
			$html .= "<tr><td colspan='5'>Total Items</td><td>$total_items</td><td></td></tr>";
			$html .= "<tr><td colspan='5'>Total Parcels</td><td>".count($arr_tracking)."</td><td></td></tr>";
			$html .= "</tbody>";
			$html .= "</table>";
			$html .= "</div>";


			// summary

			$html .= "<div style='page-break-before: always;'><h3 class='text-center'>AVISION - SUMMARY: $dt</h3>";
			$html .= "<table class='table table-bordered table-condensed' style='font-size:10px !important;' >";
			$html .= "<tbody>";
			$html .= "<tr><th>Seller SKU</th><th>Item Name</th><th style='width:120px;'>Count Of Shopee SKU</th></tr>";

			foreach($arr_sum_items as $sku => $cnt){
				$item_name = $arr_items[$sku];
				$html .= "<tr>";
				$html .= "<td>$sku</td>";
				$html .= "<td>".$item_name."</td>";
				$html .= "<td style='width:80px;'>$cnt</td>";
				$html .= "</tr>";
			}
			$html .= "<tr><td colspan='2'>Total Items</td><td style='width:120px;'>$total_items</td></tr>";
			$html .= "<tr><td colspan='2'>Total Parcels</td><td style='width:120px;'>".count($arr_tracking)."</td></tr>";
			$html .= "</tbody>";
			$html .= "</thtml>";
			$html .= "<br>";

			$html .= "<table class='table table-bordered table-condensed' style='font-size:10px !important;' >";
			$html .= "<tbody>";
			$html .= "<tr><th rowspan='".count($shipping_arr)."'>Shipping Provider</th>";
			$tr_count = 1;
			foreach($shipping_arr as $sh){
				$ship_count = $shipping_sum_arr[$sh];

				if($tr_count == 1){
					$html .= "<th>$sh</th><th style='width:120px;'>$ship_count</th></tr>";
				} else {
					$html .= "<tr><th>$sh</th><th style='width:120px;'>$ship_count</th></tr>";
				}

				$tr_count++;

			}


			$html .= "</tbody>";
			$html .= "</table>";
			$ctr_div = 1;
			foreach($shipping_arr as $sh){
				$html .= "<div style='width:340px; float:left'>";
				$html .= "<p><span style='display:inline-block;width:120px;'>Trucker:</span><span style='display:inline-block;width:190px;border-bottom:1px solid #000; font-size:12px;'>$sh</span></p>";
				$html .= "<p><span style='display:inline-block;width:120px;'>Signature:</span> <span style='display:inline-block;width:190px;border-bottom:1px solid #000;'></span></p>";
				$html .= "<p><span style='display:inline-block;width:120px;'>Printed Name:</span> <span style='display:inline-block;width:190px;border-bottom:1px solid #000;'></span></p>";
				$html .= "</div>";
				if($ctr_div % 2 == 0){
					$html .= "<div style='clear:both'></div><br>";
				}

			}

			$html .= "</div>";


			// with price
			// summary
			$html .= "<div style='page-break-before: always;'><h3 class='text-center'>AVISION - SUMMARY: $dt</h3>";
			$html .= "<table class='table table-bordered table-condensed' style='font-size:10px !important;' >";
			$html .= "<tbody>";
			$html .= "<tr><th>Seller SKU</th><th>Item Name</th><th style='width:120px;'>Count Of Shopee SKU</th></tr>";

			foreach($arr_sum_items as $sku => $cnt){
				$item_name = $arr_items[$sku];
				$html .= "<tr>";
				$html .= "<td>$sku</td>";
				$html .= "<td>".$item_name."</td>";
				$html .= "<td style='width:80px;'>$cnt</td>";
				$html .= "</tr>";
			}

			$html .= "<tr><td colspan='2'></td><td style='width:120px;'>".count($arr_tracking)."</td></tr>";
			$html .= "</tbody>";
			$html .= "</html>";
			$html .= "<br>";
			$html .= "<br>";
			$html .= "<table style='width:100%;' >";
			$html .= "<tbody>";
			$dt_name = date('l, F d, Y');
			$html .= "<tr><th>PHP ".number_format($total_amount,2)."</th><th class='text-right'>$dt_name</th></tr>";




			$html .= "</div>";

			echo $html;

		}

	}

	function toggleDiscount(){
		$det_id = Input::get('order_det_id');
		if($det_id){
			$detcls = new Wh_order_details($det_id);
			$new = ($detcls->data()->hide_discount) ? 0 : 1;
			$detcls->update(array(
				'hide_discount' =>$new
			),$det_id);
		}
	}

