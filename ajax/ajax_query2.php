<?php
	include 'ajax_connection.php';

	$functionName = Input::get("functionName");
	$functionName();

	function getTimeDiff($time){
	/*	$lbl = "ago";
		if($time < 60){
			//seconds
			$res= ($time>1) ?  "secs $lbl" : "sec $lbl";
			return floor($time) . " $res";
		} else if ($time < (60*60)) {
			//minutes
			$del = number_format($time/60,2);
			$res = (floor($del) > 1) ? "mins $lbl" : "min $lbl";
			return floor($del) . " $res";
		} else if ($time < (60*60) * 24){
			// hrs
			$del = number_format(($time/60)/60,2);
			$res = (floor($del) > 1) ? "hrs $lbl" : "hr $lbl";
			return  floor($del). " $res";
		} else{
			// if ($time < (((60 *60) * 24) * 30))
			$del = number_format((($time/60)/60)/24,2);
			$res = (floor($del) > 1) ? "days $lbl" : "day $lbl";
			return  floor($del). " $res";
		} */
		$lbl = "ago";
		if($time < 60){
			//seconds
			return 0;
		} else if ($time < (60*60)) {
			//minutes

			return 0;
		} else if ($time < (60*60) * 24){
			// hrs
			return 0;
		} else{
			// if ($time < (((60 *60) * 24) * 30))
			$del = number_format((($time/60)/60)/24,2);
			$res = (floor($del) > 1) ? "days $lbl" : "day $lbl";
			return  floor($del);
		}

	}

	function updateItemPricelist(){
		$item_id = Input::get('item_id');
		$branch_id = Input::get('branch_id');
		$price_group_id = Input::get('price_group_id');
		$id = Input::get('id');
		$adjustment = Input::get('adjustment');
		$user = new User();
		if(is_numeric($adjustment) && $item_id){
			$newAdjustment = new Item_price_adjustment();
			$newAdjustmentLog = new Item_price_adjustment_log();
			$now = time();
			if($id){
				// edit
				$preData = new Item_price_adjustment($id);
				$prev_adjustment = $preData->data()->adjustment;

				if($prev_adjustment != $adjustment){
					// update
					$newAdjustment->update(array(
						'adjustment' =>$adjustment,
						'modified' => $now
					),$id);

					$newAdjustmentLog->create(array(
						'branch_id' => $branch_id,
						'item_id' => $item_id,
						'from_price' => $prev_adjustment,
						'to_price' => $adjustment,
						'company_id' => $user->data()->company_id,
						'user_id' => $user->data()->id,
						'created' => $now,
						'is_active' => 1,
						'price_group_id' => $price_group_id
					));
					echo "Price adjusted successfully";
				} else {
					echo "No changes made";
				}
			} else {

				$newAdjustment->create(array(
					'price_group_id' => $price_group_id,
					'branch_id' => $branch_id,
					'item_id' => $item_id,
					'adjustment' =>$adjustment,
					'created' => $now,
					'modified' => $now,
					'company_id' => $user->data()->company_id,
					'is_active' => 1
				));
				$newAdjustmentLog->create(array(
					'price_group_id' => $price_group_id,
					'branch_id' => $branch_id,
					'item_id' => $item_id,
					'from_price' => 0,
					'to_price' => $adjustment,
					'company_id' => $user->data()->company_id,
					'user_id' => $user->data()->id,
					'created' => $now,
					'is_active' => 1
				));
				echo "Price adjusted successfully";
			}
		}
	}

	function __getChargeType($payment_id){
		$is_charge = 0;
		if(Configuration::getValue('charge_label') == 1){
			if(isset($payment_id) && $payment_id){
				$member_credit = new Member_credit();
				$cheque = new Cheque();
				$cheque_list = $cheque->getMemberChequeByPaymentID($payment_id);
				$list = $member_credit->getByPaymentId($payment_id);
				if($cheque_list && count($cheque_list) > 0){
					$is_charge = 3;
				} else if(isset($list->is_cod)){
					if($list->is_cod==1){
						$is_charge = 1;
					} else {
						$is_charge =2;
					}
				}
			}
		}
		return $is_charge;
	}

	function __countBundleItems($orders){
		$ctr_bundled_items= 0;
		foreach($orders as $order){
			if($order->is_bundle == 1){
				$bundle = new Bundle();
				$bundle_list = $bundle->getBundleItem($order->item_id);
				foreach($bundle_list as $bl){
					$ctr_bundled_items++;
				}
			}
		}

		return $ctr_bundled_items;

	}

	function __alreadyInSales($data){
		if($data->invoice != 0 || $data->dr != 0 || $data->pr != 0 || $data->sv != 0 || $data->sr != 0 || $data->ts != 0){
			return true;
		}
		return false;
	}


	function getItemForInvoicePrintingWh(){

	$order_id = Input::get('order_id');
	$myOrder  = new Wh_order($order_id);
	$order_details = $myOrder->getFullDetails($order_id);
	$finalarr = [];
	$itemlist = [];
	$bundle_cnt = 1;
	$whorder = new Wh_order_details();
	$orders = $whorder->getOrderDetails($order_id);

	$order_type = Input::get('order_type');
	$invoice = Input::get('invoice');
	$dr = Input::get('dr');
	$pr = Input::get('pr');
	$sv =  Input::get('sv');
	$sr =  Input::get('sr');
	$ts =  Input::get('ts');
	$terminal_id = Input::get('terminal_id');
	$pref_invoice = Input::get('pref_inv');
	$pref_dr = Input::get('pref_dr');
	$pref_ir = Input::get('pref_ir');
	$pref_sv = Input::get('pref_sv');
	$suf_inv = Input::get('suf_inv');
	$suf_dr = Input::get('suf_dr');
	$suf_ir = Input::get('suf_ir');
	$suf_sv = Input::get('suf_sv');
	$is_reprint = Input::get('rePrint');
	$custom_date = Input::get('custom_date');


	if(!$terminal_id) {
		die("Please set up terminal first.");
	}

	$terminal = new Terminal($terminal_id);

	$dr_limit = $terminal->data()->dr_limit;
	$invoice_limit = $terminal->data()->invoice_limit;
	$ir_limit = $terminal->data()->ir_limit;
	$sv_limit = $terminal->data()->sv_limit;

	$user = new User();

	$alreadyInsertedSales = __alreadyInSales($myOrder->data());


	// check type for charge label. cebuhiq only
	$is_charge = __getChargeType($myOrder->data()->payment_id);


	$ctr_bundled_items= __countBundleItems($orders);


	$count_all_items = $ctr_bundled_items + count($orders);

	//check limit
	$count_dr_use = ceil($count_all_items / $dr_limit);
	$count_invoice_use = ceil($count_all_items / $invoice_limit);
	$count_ir_use = ceil($count_all_items / $ir_limit);
	$count_sv_use = ceil($count_all_items / $sv_limit);


	if($terminal_id){

			if($is_reprint != '1'){

				if($order_type == 1){
					//increment invoice
					if($invoice){

						$invoice_range = $invoice .",";
						if($count_invoice_use > 1){
							for($i = 1; $i < $count_invoice_use; $i++){
								$invoice_range .= ($invoice+$i) . ",";
							}
						}
						$invoice_range = rtrim($invoice_range,',');
						$count_invoice_use = $count_invoice_use - 1;

						$invoice = $invoice + $count_invoice_use;
						$terminal->update(array(
							'modified' => strtotime(date('Y/m/d H:i:s')),
							'invoice' => $invoice
						), $terminal_id);
						$myOrder->update(array(
							'invoice' => $invoice,
							'invoice_range' => $invoice_range
						),$order_id);

						$dr = $myOrder->data()->dr;
						$pr = $myOrder->data()->pr;
						$sv =  $myOrder->data()->sv;
						$ts =   $myOrder->data()->ts;
						$sr =   $myOrder->data()->sr;

					}
				} else if($order_type == 2){
					// increment dr

					if($dr != 0){
						$dr_range = $dr .",";
						if($count_dr_use > 1){
							for($i = 1; $i < $count_dr_use; $i++){
								$dr_range .= ($dr+$i) . ",";
							}
						}
						$dr_range = rtrim($dr_range,',');
						$count_dr_use = $count_dr_use - 1;
						$dr = $dr + $count_dr_use;

						$terminal->update(array(
							'modified' => strtotime(date('Y/m/d H:i:s')),
							'dr' => $dr
						), $terminal_id);
						$myOrder->update(array(
							'dr' => $dr,
							'dr_range' => $dr_range
						),$order_id);
						$invoice = $myOrder->data()->invoice;
						$pr = $myOrder->data()->pr;
						$sv =  $myOrder->data()->sv;
						$ts =   $myOrder->data()->ts;
						$sr =   $myOrder->data()->sr;
					}
				} else if($order_type == 3){
					// increment pr


					if($pr != 0){
						$ir_range = $pr .",";
						if($count_ir_use > 1){
							for($i = 1; $i < $count_ir_use; $i++){
								$ir_range .= ($pr+$i) . ",";
							}
						}
						$ir_range = rtrim($ir_range,',');
						$count_ir_use = $count_ir_use - 1;
						$pr = $pr + $count_ir_use;
						$terminal->update(array(
							'modified' => strtotime(date('Y/m/d H:i:s')),
							'ir' => $pr
						), $terminal_id);

						$myOrder->update(array(
							'pr' => $pr,
							'pr_range' => $ir_range
						),$order_id);
						$invoice = $myOrder->data()->invoice;
						$dr = $myOrder->data()->dr;
						$sv =  $myOrder->data()->sv;
						$ts =   $myOrder->data()->ts;
						$sr =   $myOrder->data()->sr;
					}
				} else if($order_type == 4){
					// increment pr
						if($sv != 0){
						$sv_range = $sv .",";
						if($count_sv_use > 1){
							for($i = 1; $i < $count_sv_use; $i++){
								$sv_range .= ($sv+$i) . ",";
							}
						}
						$sv_range = rtrim($sv_range,',');
						$count_sv_use = $count_sv_use - 1;
						$sv = $sv + $count_sv_use;
						$terminal->update(array(
							'modified' => strtotime(date('Y/m/d H:i:s')),
							'sv' => $sv
						), $terminal_id);

						$myOrder->update(array(
							'sv' => $sv,
							'sv_range' => $sv_range
						),$order_id);
						$invoice = $myOrder->data()->invoice;
						$dr = $myOrder->data()->dr;
						$pr =  $myOrder->data()->pr;
					}


				} else if ($order_type == 5){
				// PRINT GROUP , PR, DR, INVOICE, SV, TS,SR
					$arrupOrder = [];
					$arrupTerminal = [];
					if($pr != 0 ){
					$ir_range = $pr .",";
						if($count_ir_use > 1){
							for($i = 1; $i < $count_ir_use; $i++){
								$ir_range .= ($pr+$i) . ",";
							}
						}
						$ir_range = rtrim($ir_range,',');
						$count_ir_use = $count_ir_use - 1;
						$pr = $pr + $count_ir_use;
						$arrupOrder['pr'] = $pr;
						$arrupOrder['pr_range'] = $ir_range;
						$arrupTerminal['ir'] = $pr;
					}
					if($dr != 0 ){
						$dr_range = $dr .",";
						if($count_dr_use > 1){
							for($i = 1; $i < $count_dr_use; $i++){
								$dr_range .= ($dr+$i) . ",";
							}
						}
						$dr_range = rtrim($dr_range,',');
						$count_dr_use = $count_dr_use - 1;
						$dr = $dr + $count_dr_use;

						$arrupOrder['dr'] = $dr;
						$arrupOrder['dr_range'] = $dr_range;

						$arrupTerminal['dr'] = $dr;
					}
					if($invoice != 0 ){
						$invoice_range = $invoice .",";
						if($count_invoice_use > 1){
							for($i = 1; $i < $count_invoice_use; $i++){
								$invoice_range .= ($invoice+$i) . ",";
							}
						}
						$invoice_range = rtrim($invoice_range,',');
						$count_invoice_use = $count_invoice_use - 1;

						$invoice = $invoice + $count_invoice_use;

						$arrupOrder['invoice'] = $invoice;
						$arrupOrder['invoice_range'] = $invoice_range;
						$arrupTerminal['invoice'] = $invoice;
					}
					if($sv != 0 ){
					$sv_range = $sv .",";
						if($count_sv_use > 1){
							for($i = 1; $i < $count_sv_use; $i++){
								$sv_range .= ($sv+$i) . ",";
							}
						}
						$sv_range = rtrim($sv_range,',');
						$count_sv_use = $count_sv_use - 1;

						$sv = $sv + $count_sv_use;

						$arrupOrder['sv'] = $sv;
						$arrupOrder['sv_range'] = $sv_range;
						$arrupTerminal['sv'] = $sv;
					}
					if($sr != 0 ){

						$arrupOrder['sr'] = $sr;
						$arrupTerminal['sr'] = $sr;
					}
					if($ts != 0 ){
						$arrupOrder['ts'] = $ts;
						$arrupTerminal['ts'] = $ts;
					}

					$arrupTerminal['modified'] =  strtotime(date('Y/m/d H:i:s'));
					$terminal->update($arrupTerminal, $terminal_id);
					$myOrder->update($arrupOrder,$order_id);
				} else if($order_type == 6){
					// increment sr
						if($sr != 0){

						$terminal->update(array(
							'modified' => strtotime(date('Y/m/d H:i:s')),
							'sr' => $sr
						), $terminal_id);

						$myOrder->update(array(
							'sr' => $sr,

						),$order_id);
						$invoice = $myOrder->data()->invoice;
						$dr = $myOrder->data()->dr;
						$pr =  $myOrder->data()->pr;
						$sv =  $myOrder->data()->sv;
						$ts =   $myOrder->data()->ts;
						}


				}else if($order_type == 7){
					// increment sr
						if($ts != 0){

						$terminal->update(array(
							'modified' => strtotime(date('Y/m/d H:i:s')),
							'ts' => $ts
						), $terminal_id);

						$myOrder->update(array(
							'ts' => $ts,

						),$order_id);

						$invoice = $myOrder->data()->invoice;
						$dr = $myOrder->data()->dr;
						$pr =  $myOrder->data()->pr;
						$sv =  $myOrder->data()->sv;
						$sr =  $myOrder->data()->sr;

					}
				}
			}

			Log::addLog(
				$user->data()->id,
				$user->data()->company_id,
				"PRINT Inv $invoice DR $dr PR $pr",
				'ajax_accounting.php'
			);


			$newsales = new Sales();
			$now = time();
			if($custom_date){
				$now = strtotime($custom_date);
			}
			$chksales = new Sales();
			$det = $chksales->getsinglesale($myOrder->data()->payment_id);
			if(!$det){
				if($dr){
					$dr = $dr-$count_dr_use;
				}
				if($invoice){
					$invoice = $invoice-$count_invoice_use;
				}
				if($pr){
					$pr = $pr-$count_ir_use;
				}
				if($sv){
					$sv = $sv-$count_sv_use;
				}
			}

			if($alreadyInsertedSales && $is_reprint != '1'){
				if($dr){
					if($order_type != 2) $dr = ($dr+1)-$count_dr_use;
					else $dr = ($dr)-$count_dr_use;
				}

				if($invoice){
					if($order_type != 1) $invoice = ($invoice+1)-$count_invoice_use;
					else $invoice = ($invoice)-$count_invoice_use;
				}

				if($pr){
					if($order_type != 3) $pr = ($pr+1)-$count_ir_use;
					else $pr = ($pr)-$count_ir_use;
				}

				if($sv){
					if($order_type != 4) $sv = ($sv+1)-$count_sv_use;
					else $sv = ($sv)-$count_sv_use;
				}

				if($sr){
					if($order_type != 6) $sr = ($sr+1);
				}

				if($ts){
					if($order_type != 7) $ts = ($ts+1);
				}

				$updateSales = new Sales();
				$listUpdate = $updateSales->salesTransactionBaseOnPaymentId($myOrder->data()->payment_id);

				$ctr_order_update = 1;
				foreach($listUpdate as $lupdate){

					if($ctr_order_update % ($dr_limit+1) == 0){
						if($dr){
							$dr +=1 ;
						}
					}
					if($ctr_order_update % ($invoice_limit+1) == 0){
						if($invoice){
							$invoice +=1 ;
						}
					}
					if($ctr_order_update % ($ir_limit+1) == 0){
						if($pr){
							$pr +=1 ;
						}
					}
					if($ctr_order_update % ($sv_limit+1) == 0){
						if($sv){
							$sv +=1 ;
						}
					}

					$ctr_order_update++;
					$updateSales->update(
						['invoice' => $invoice, 'dr' => $dr, 'ir' => $pr, 'sv' => $sv, 'sr2' => $sr, 'ts' => $ts]
						,$lupdate->id);
				}

			}

			if(Configuration::getValue('order_has_station') == 1){
				$sales_type_id =  $myOrder->data()->gen_sales_type;
				$station_id = $myOrder->data()->station_id;
			} else {
				$sales_type_id =   $order_details->salestype;
				$station_id = 0;
			}

			$showWarranty = Configuration::showWarranty();

			$ctr_order = 1;
			// select
			$hasdisc = 0;
			$sort_ctr = 1;

			foreach($orders as $order){
				// insert sales
				$total = ($order->qty * $order->adjusted_price) + $order->member_adjustment;
				$indDiscount = $order->member_adjustment / $order->qty;
				$adjustedPrice = $order->adjusted_price + $indDiscount;
				$racking = $order->racking;
				$rack_json = [];
				$priceAdjustment = $order->price_adjustment * $order->qty;
				$memberAdjustment = $order->member_adjustment;
				$discount_type = computeDiscountType($indDiscount,$order->adjusted_price,$order->hide_discount);
				$discount_type_sort = ($discount_type) ? 1 : 0;
				if($discount_type_sort){
					$hasdisc = 1;
				}

				if(!$det){

					if($is_reprint == '1'){
						$invoice = $myOrder->data()->invoice;
						$dr = $myOrder->data()->dr;
						$pr = $myOrder->data()->pr;
						$sv = $myOrder->data()->sv;
						$sr = $myOrder->data()->sr;
						$ts = $myOrder->data()->ts;
					}

					if(Configuration::getValue('order_has_station') == 1){

						if(isset($order->station_id) && $order->station_id){
							$station_id =  $order->station_id;
						}

						if(isset($order->spec_sales_type) && $order->spec_sales_type){
							$sales_type_id =  $order->spec_sales_type;
						}

					}

					if($ctr_order % ($dr_limit+1) == 0){
						if($dr){
							$dr +=1 ;
						}
					}

					if($ctr_order % ($invoice_limit+1) == 0){
						if($invoice){
							$invoice +=1 ;
						}
					}

					if($ctr_order % ($ir_limit+1) == 0){
						if($pr){
							$pr +=1 ;
						}
					}

					if($ctr_order % ($sv_limit+1) == 0){
						if($sv){
							$sv +=1 ;
						}
					}

					$ctr_order++;
					$newsales->create(array(
						'terminal_id' => $terminal_id,
						'invoice' => $invoice,
						'sv' => $sv,
						'dr' => $dr,
						'ir' => $pr,
						'sr2' => $sr,
						'ts' => $ts,
						'pref_inv' => $pref_invoice,
						'pref_dr' => $pref_dr,
						'pref_ir' => $pref_ir,
						'pref_sv' => $pref_sv,
						'suf_inv' => $suf_inv,
						'suf_dr' => $suf_dr,
						'suf_ir' => $suf_ir,
						'suf_sv' => $suf_sv,
						'item_id' => $order->item_id,
						'price_id' => $order->price_id,
						'qtys' =>  $order->qty,
						'discount' => 0,
						'store_discount' => 0,
						'adjustment' => $priceAdjustment,
						'member_adjustment' => $memberAdjustment,
						'terms' => $order->terms,
						'company_id' => $user->data()->company_id,
						'cashier_id' => $user->data()->id,
						'sold_date' => $now,
						'payment_id' => $myOrder->data()->payment_id,
						'member_id' => $myOrder->data()->member_id,
						'warranty' => $order->warranty,
						'station_id' => $station_id,
						'sales_type' => $sales_type_id,
						'adjustment_remarks' => $order->adjustment_remarks,
						'from_od' => 1
					));

					$prod = new Product($order->item_id);

					if ($prod->data()->item_type == 2 || $prod->data()->item_type == 3  || $prod->data()->item_type == 4 || $prod->data()->item_type == 5){
					for($startingservice = 0; $startingservice < $order->qty; $startingservice++){
						$con = new Consumable();
						$myCon = $con->getConsumableByItemId($order->item_id);

						$newServ = new Service();
						$start = time();
						$cday = $myCon->days;
						$endDate = strtotime(date('m/d/Y',$start) . $cday . " day");
						$newServ->create(array(
							'member_id' => $myOrder->data()->member_id,
							'item_id' => $order->item_id,
							'start_date' => $start,
							'end_date' =>$endDate,
							'consumable_qty' =>$myCon->qty,
							'company_id' => $user->data()->company_id,
							'payment_id' => $myOrder->data()->payment_id
						));
						$servlastid = $newServ->getInsertedId();

						if($prod->data()->item_type == 4){
							$con_amount = new Consumable_amount();
							$n = time();
							$pricecon = $prod->getPrice($order->item_id);
							$con_amount->create(array(
								'service_id' => $servlastid,
								'amount' => $pricecon->price,
								'item_id' => $order->item_id,
								'member_id' => $myOrder->data()->member_id,
								'is_active' => 1,
								'created' => $n,
								'modified' => $n,
								'payment_id' => $myOrder->data()->payment_id
							));
						}

						if($prod->data()->item_type == 5){
							$con_free = new Consumable_freebies();
							$con_free_amount = $con_free->getConsumableFreebiesAmount($order->item_id);

							$n = time();
							$con_free->create(array(
								'service_id' => $servlastid,
								'amount' => $con_free_amount->amount,
								'item_id' =>$order->item_id,
								'member_id' => $myOrder->data()->member_id,
								'is_active' => 1,
								'created' => $n,
								'modified' => $n,
								'payment_id' =>  $myOrder->data()->payment_id
							));
						}
					}
					}
				}

				if($racking){
					$racking = json_decode($order->racking);
					// get assigned person
					if(count($racking)){
						$rack_ret = "";
						$rack_cls = new Rack();
						if($racking){
							foreach($racking as $rack){
								if(isset($rack->rack_id) && $rack->rack_id ){
									$assignedPerson = $rack_cls->getAssignedPerson($rack->rack_id);
									if(isset($assignedPerson->lastname)){
										$assignedPerson = ucwords($assignedPerson->lastname . ", " . $assignedPerson->firstname  . " " . $assignedPerson->firstname);
									} else {
										$assignedPerson ='';
									}
									$rack_json[] = ['rack_name' => $rack->rack, 'qty' => $rack->qty , 'assigned' => $assignedPerson];
								}
							}

						}
					}
				}
				$rack_json = json_encode($rack_json);

				if($order->is_bundle == 1){
					$bundle = new Bundle();
					$bundle_list = $bundle->getBundleItem($order->item_id);

					$itemlist[] = ['hide_discount' => $order->hide_discount,'bundle_sort' => $bundle_cnt, 'price_group_id' => $order_details->price_group_id,'original_price' => $order->adjusted_price,'orig_unit'=>$order->unit_name,'unit_name'=>escape($order->unit_name),'item_code'=>escape($order->item_code),'description'=>strtolower(escape($order->description)), 'barcode'=>escape($order->barcode), 'orig_qty'=>escape(formatQuantity($order->qty)), 'qty'=>escape(formatQuantity($order->qty)), 'price'=>escape($adjustedPrice), 'discount'=>escape($order->member_adjustment), 'total'=>escape($total),'racking' => $rack_json,'discount_type' => $discount_type,'discount_type_sort' => $discount_type_sort,'sort_ctr'=>$sort_ctr,'price_label' => '', 'original_total'=>escape($order->adjusted_price * $order->qty)];
					$bundle_cnt++;
					$bundle_compare = substr($order->description,6,6);
					$bundle_compare = trim($bundle_compare);
					foreach($bundle_list as $bl){
						$bundle_comp_compare = trim(substr($bl->description,0,5));

						if(Configuration::thisCompany('cebuhiq')){
							$bqty = '';
							$bunit =  escape(formatQuantity($bl->child_qty * $order->qty)) . " ". escape($bl->unit_name );
						} else {
							$bqty = escape(formatQuantity($bl->child_qty * $order->qty));
							$bunit = escape($order->unit_name) ;
						}

						if($bundle_comp_compare != $bundle_compare)
							$itemlist[] = ['hide_discount' => $order->hide_discount, 'bundle_sort' => $bundle_cnt,'price_group_id' => $order_details->price_group_id,'original_price' => '','orig_unit'=>$bl->unit_name ,'unit_name'=>$bunit,'item_code'=>escape($bl->item_code),'description'=>strtolower(escape($bl->description)), 'barcode'=>escape($bl->barcode), 'orig_qty'=> ($bl->child_qty * $order->qty), 'qty'=>$bqty, 'price'=>escape(0), 'discount'=>escape(0), 'total'=>escape(0),'racking' => '[]','discount_type' => [],'discount_type_sort' => $discount_type_sort,'sort_ctr'=>$sort_ctr,'price_label' => '', 'original_total'=>0];

						$bundle_cnt++;
					}

				} else {

					if($showWarranty && $order->warranty != 0.00){
						$warranty = (int) $order->warranty;
						$desc = $order->description . "<small style='display:block;'>Warranty: $warranty month(s)</small>";
					} else {
						$desc = $order->description;
					}

					$unit_name = $order->unit_name;
					$unit_original_price = $order->adjusted_price;
					$unit_adjusted_price = $adjustedPrice;

					$unit_orig_qty = $order->qty;
					$unit_qty = $order->unit_qty;
					if(Configuration::thisCompany('cebuhiq')){
						if( $unit_orig_qty != $unit_qty && $unit_qty != 0.000 && $order->preferred_unit){

							$unit_name =  $order->preferred_unit;
							$unit_original_price = $unit_original_price * ($unit_orig_qty / $unit_qty);
							$unit_adjusted_price = $unit_adjusted_price * ($unit_orig_qty / $unit_qty);

							$unit_orig_qty = $unit_qty;
						}
					}



					$itemlist[] = ['hide_discount' => $order->hide_discount, 'bundle_sort'=>0,'price_group_id' => $order_details->price_group_id,'original_price' => $unit_original_price,'orig_unit'=>$unit_name,'unit_name'=>escape($unit_name),'item_code'=>escape($order->item_code),'description'=>strtolower($desc), 'barcode'=>escape($order->barcode), 'orig_qty'=>escape(formatQuantity($unit_orig_qty)), 'qty'=>escape(formatQuantity($unit_orig_qty)), 'price'=>escape($unit_adjusted_price), 'discount'=>escape($order->member_adjustment), 'total'=>escape($total),'racking' => $rack_json,'is_freebie' => $order->is_freebie,'discount_type' => $discount_type,'discount_type_sort' => $discount_type_sort,'sort_ctr'=>$sort_ctr,'price_label' => '', 'original_total'=>escape($order->adjusted_price * $order->qty)];
				}

				$sort_ctr++;

			}



			$consumable = new Consumable();
			$consumables = $consumable->get_active('payment_consumable',['payment_id','=',$myOrder->data()->payment_id]);
			$consumable_total = 0;
			if($consumables){
				foreach($consumables as $con){
					$consumable_total += $con->amount;
				}
			}

			$deduction = new Deduction();
			$special_discount = $deduction->getDiscount($myOrder->data()->payment_id);
			$special_discount_total = '';
			if(isset($special_discount->total_deduction) && $special_discount->total_deduction){
				$special_discount_total = $special_discount->total_deduction;
			}

			$membername = ucwords($order_details->mln);
			$cashiername = ucwords($order_details->uln . ", " . $order_details->ufn . " " . $order_details->umn);
			$remarks_append = "";
			if($order_details->shipping_company_name){
				$remarks_append .= "<br>" . $order_details->shipping_company_name;
			}
			if($order_details->branch_name){
				$remarks_append .= "<br>" . $order_details->branch_name;
			}

			if($order_details->client_po){
				$remarks_append .= "<br>PO#".$order_details->client_po;
			}
			$remarks = $order_details->remarks . $remarks_append;

			$terms = ($order_details->terms) ? $order_details->terms : '';

			$other_info_append = "";
			if($order_details->mfn || $order_details->mmn || $order_details->cel_number || $order_details->contact_number){
				$client_number = "";
				if($order_details->cel_number){
					$client_number .= $order_details->cel_number;
				}
				if($order_details->contact_number){
					if($client_number){
						$client_number .=",";
					}
					$client_number .= $order_details->contact_number;
				}

				$other_info_append = "<span style='display:block;font-size:12px;padding:0px;margin:0px;'>Contact Person: " .ucwords($order_details->mfn . " " . $order_details->mmn) . "</span><span style='display:block;font-size:12px;padding:0px;margin:0px;'>Contact Number: " .ucwords($client_number) . "</span>";
			}
			if(Configuration::thisCompany('aquabest') && Configuration::thisCompany('avision')){
				$other_info_append="";
			}

			$address = $order_details->personal_address . $other_info_append;
			$sales_type_name = ($order_details->sales_type_name) ? $order_details->sales_type_name : '';

			$finalarr['member_name'] = $membername;
			$finalarr['client_po'] = $order_details->client_po;
			$finalarr['tin_no'] = $order_details->tin_no;
			$finalarr['is_charge'] = $is_charge;
			$finalarr['dr'] = $order_details->dr;
			$finalarr['pr'] = $order_details->pr;
			$finalarr['sv'] = $order_details->sv;
			$finalarr['remarks'] = $remarks;
			$finalarr['special_discount_total'] = $special_discount_total;
			$finalarr['cashier_name'] = $cashiername;
			$finalarr['member_id'] = $order_details->member_id;
			$finalarr['station_name'] = $address;
			$finalarr['consumable_total'] = $consumable_total;
			$finalarr['station_id'] = '';
			$finalarr['station_address'] = '';
			$finalarr['terms'] = $terms;
			$finalarr['sales_type'] = $sales_type_name;
			$finalarr['date_sold'] = ($custom_date) ? $custom_date :  date('m/d/Y');
			$due = ($custom_date) ? $custom_date :  date('m/d/Y');
			if($terms){
				$finalarr['due_date'] = date('m/d/y',strtotime($due . $terms . " days"));
			} else {
				$finalarr['due_date'] = '';
			}
			if(Configuration::getValue('discount_label') == 1){
				if($hasdisc == 1){
					usort($itemlist, function($a, $b)
					{

						if ($a['sort_ctr'] == $b['sort_ctr']) {
						      return $a['bundle_sort'] - $b['bundle_sort'];
						}
						if ($a['discount_type_sort'] == $b['discount_type_sort']) {
						      return $a['sort_ctr'] - $b['sort_ctr'];
						}

						 return $a['discount_type_sort'] > $b['discount_type_sort'];
					});
				}


				$total_one  = 0;
				$has_one = false;
				$total_two  = 0;
				$has_two = false;
				$total_two_gross = 0;
				$arr_temp = [];

				$same_discount = true;
				$disc_temp = 0;
				foreach($itemlist as $il){

					if($il['discount_type_sort'] == 0){
						$total_one += $il['total'];
						$has_one = true;
					} else {
						$total_two += $il['total'];
						$total_two_gross += $il['original_total'];
						$has_two = true;
						$arr_discount_types = $il['discount_type'];
						if($arr_discount_types){
							foreach($arr_discount_types as $dsc){
								if($dsc != $disc_temp && $disc_temp !=0){
									$same_discount = false;
								}
								$disc_temp = $dsc;
							}
						}
					}

				}
				if(!$has_two){
					$same_discount = false;
				}
				if($has_one && $has_two){
				$done = false;
				foreach($itemlist as $il){
					if($il['discount_type_sort'] == 0){

					} else {
						if(!$done){
							$arr_temp[] = ['bundle_sort' => 0,'original_price' => '','orig_unit'=>'','unit_name'=>'','item_code'=>'','description'=>'', 'barcode'=>'', 'orig_qty'=>'', 'qty'=>'', 'price'=>'', 'discount'=>'', 'total'=>$total_one,'racking' => '','is_freebie' => '','discount_type' => '','discount_type_sort' => '','price_label' => 'Sub Total'];
							$done = true;
						}
					}

					$arr_temp[] = $il;

				}
					if($same_discount){
						$total_two_discount = abs($disc_temp);
						$arr_temp[] = ['bundle_sort' => 0, 'original_price' => '','orig_unit'=>'','unit_name'=>'','item_code'=>'','description'=>'', 'barcode'=>'', 'orig_qty'=>'', 'qty'=>'', 'price'=>'', 'discount'=>'', 'total'=>$total_two_gross,'racking' => '','is_freebie' => '','discount_type' => '','discount_type_sort' => '','price_label' => 'Gross'];
						$arr_temp[] = ['bundle_sort' => 0, 'original_price' => '','orig_unit'=>'','unit_name'=>'','item_code'=>'','description'=>'', 'barcode'=>'', 'orig_qty'=>'', 'qty'=>'', 'price'=>'', 'discount'=>'', 'total'=>$total_two_discount,'racking' => '','is_freebie' => '','discount_type' => '','discount_type_sort' => '','price_label' => 'Discount %'];
					}

					$arr_temp[] = ['bundle_sort' => 0 , 'original_price' => '','orig_unit'=>'','unit_name'=>'','item_code'=>'','description'=>'', 'barcode'=>'', 'orig_qty'=>'', 'qty'=>'', 'price'=>'', 'discount'=>'', 'total'=>$total_two,'racking' => '','is_freebie' => '','discount_type' => '','discount_type_sort' => '','price_label' => 'Sub Total'];
					$itemlist = $arr_temp;
				}

			}
			$unit_group_lbl = '';

			if(Configuration::thisCompany('cebuhiq')){


				$bdl_label = "Bundle";
				$Case_label = "Case";
				$bdl_count = 0;
				$other_count = 0;

				foreach($itemlist as $itlst){
					if($itlst['orig_unit'] == $bdl_label || $itlst['orig_unit'] == $Case_label){
						$bdl_count += str_replace(',','', $itlst['orig_qty']);
					} else {
						$other_count += str_replace(',','', $itlst['orig_qty']);
					}
				}
				$unit_group_lbl = "Bundle: " . $bdl_count. "<br>Pcs: " . $other_count;
			}


			$finalarr['item_list_sum'] = $unit_group_lbl;
			$finalarr['item_list'] = $itemlist;
			$finalarr['order_id'] = $order_id;
			$finalarr['same_discount'] = $same_discount ? 1 : 0;
			$finalarr['shipping_company_name'] = $order_details->shipping_company_name;
			echo json_encode($finalarr);
		}
	}



	function computeDiscountType($d,$p,$h){
		$arr_to_check[] = [10,1];
		$arr_to_check[] = [10,2];
		$arr_to_check[] = [10,3];
		$arr_to_check[] = [10,4];
		$arr_to_check[] = [10,5];
		$arr_to_check[] = [10,3,2];
		$arr_to_check[] = [20,5];
		$match = [];
		if(Configuration::getValue('discount_label') == 1 && !$h){
			if($d < 0 && $p){
				foreach($arr_to_check as $c){
					$total = 0;
					$cur_chk = 0;
					foreach($c as $i){
						$i = $i / 100;
						$tmp = ($i * ($p-$cur_chk));

						//echo   "$tmp = $i * $cur_chk <br>";
						$cur_chk += $tmp;
					//	echo   "$cur_chk = $tmp <br>";
						$total += $tmp;
					//	echo   "$total += $tmp<br>";

					}
					//	echo "$total == $d<br>";
 					if(number_format(abs($total),3) == number_format(abs($d),3)){
						$match = $c;
						break;
					}

				}
				if(!$match){
					$total = ($d/$p) * 100;
					$match = [number_format(abs($total),2,'.',',')];
				}
			}
		}
		return $match;
	}

	function submitWhOrder(){
		$items = json_decode(Input::get('items'));
		$request = json_decode(Input::get('request'));
		$is_service = Input::get('is_service');
		$is_service_notification = Input::get('is_service_notification');
		$user = new User();
		$now = time();
		$filename = "";

		 //backhere
		if($request->branch_id){
			$station_id = 0;
			if(count($items) > 0){
				if($request->branch_id_to){
					$branch_id_to = $request->branch_id_to;
				} else {
					$branch_id_to = $user->data()->branch_id;
				}
				if(isset($request->station_id) && $request->station_id){
				$station_id = $request->station_id;
				}
				$order = new Wh_order();
				if($request->member_id){
					$for_status = 1;
				} else {
					$for_status = 3;
				}
				if($is_service_notification){
					$for_status = -1;
				}
				if(Configuration::getValue('order_skip_reserve') == 1 && $request->is_reserve == 1){
					$dt_reserved = time();
				} else {
					$dt_reserved = 0;
				}

				if($request->is_reserve == 1 && Configuration::getValue('order_reservation_attachment') == 1){
					$target_path = "../uploads/";
					$ext = explode('.', basename($_FILES['file']['name']));
					$ref_table = "reservation";
					$filename = $ref_table ."-" .uniqid(). ".".$ext[count($ext) - 1];

					$path = $target_path .$filename ;
					$file = $_FILES['file'];
					if (move_uploaded_file($_FILES['file']['tmp_name'], $path)) {

					} else {
						echo "There's a problem in uploading your attachment.";
					}
				}
				$from_service = 0;
				if($request->is_reserve == 2){
					$is_reserve = 0;
					$dt_reserved = 0;
					$walkin_app = 1;
				}  else {
					$is_reserve = $request->is_reserve;
					$walkin_app = 0;
				}

				if($is_service){
					$from_service = $is_service;
				}
				$gen_sales_type = ($request->gen_sales_type) ? $request->gen_sales_type : 0;
				$nowValid = "";
				$isValid = true;
				foreach($items as $item){
					// check inventory again
					if($request->member_id){
						$data = getAdjustmentPrice($request->branch_id,$item->item_id,$request->member_id,$item->qty);
						$split = explode("||",$data);
						if(isset($split) && $split[2] == 0){
								$nowValid .= $item->item_code . " doesn't have inventory as of now. Other user just got it first. <br>";
							$isValid = false;
						}
					}
				}

				$price_group_id = $request->price_group_id ? $request->price_group_id : 0;

				if($isValid){

					$order->create(array(
					'branch_id' => $request->branch_id,
					'member_id' => $request->member_id,
					'to_branch_id' => $branch_id_to,
					'remarks' => $request->remarks,
					'client_po' => $request->client_po,
					'shipping_company_id' => $request->shipping_company_id,
					'created' => $now,
					'company_id' => $user->data()->company_id,
					'user_id' => $user->data()->id,
					'is_active' => 1,
					'status' => $for_status,
					'stock_out' => 0,
					'for_pickup' => $request->for_pickup,
					'is_reserve' => $is_reserve,
					'reserved_date' => $dt_reserved,
					'file_name' => $filename,
					'station_id' => $station_id,
					'for_approval_walkin' => $walkin_app,
					'gen_sales_type' => $gen_sales_type,
					'from_service' => $from_service,
					'for_notif_service' => $is_service_notification,
					'price_group_id' => $price_group_id,
				));

				$lastItOrder = $order->getInsertedId();
				$memberTerms = new Member_term();
				$member_name ="";
				if($request->member_id){
					$memberDetails = new Member($request->member_id);
					$member_name = $memberDetails->data()->lastname;
				}


				if($lastItOrder){
					$itemids = [];
					$total_price = 0;
					foreach($items as $item){

						$order_details = new Wh_order_details();
						$adjustmentcls = new Item_price_adjustment();
						$itemids[] = $item->item_id;
						$product = new Product($item->item_id);
						$price = $product->getPrice($item->item_id);
						$adjustment = $adjustmentcls->getAdjustment($request->branch_id,$item->item_id);

						if($request->price_group_id){
							$adjustment_price_group = $adjustmentcls->getAdjustmentPriceGroup($item->item_id,$request->price_group_id);
						}
						$terms= 0;
						if($request->member_id){
						$terms = $memberDetails->data()->terms;
						}


						$qty = $item->qty;
						$mem_ajdustment = __itemMemberAdjustment($request->member_id,$item->item_id,$qty);

						$alladj = $mem_ajdustment['adjustment'];
						$adjustment_remarks =  $mem_ajdustment['remarks'];;
						$ind_adjustment = 0 ;
						if($alladj){
							$ind_adjustment = $alladj / $qty ;
						}

						if(Configuration::getValue('addtl_disc')){

							$alladj =$item->adjustmentmem;

						}


						$branch_discount = new Branch_discount();
						$b_disc = $branch_discount->getDiscount($request->branch_id,$request->branch_id_to);
						$b_disc_amount = 0;
						if(isset($b_disc->discount) && !empty($b_disc->discount)){
						$b_disc_amount = $b_disc->discount / 100;
						$prod = new Product();
						$price = $prod->getPrice($item->item_id);
						$b_disc_amount = $price->price * $b_disc_amount;
						}
						if($b_disc_amount){
							$b_disc_amount  = ($b_disc_amount * $qty) * -1;
							$alladj += $b_disc_amount;
						}

						$adj_amount = 0;
						if(isset($adjustment->adjustment)){
							$adj_amount += $adjustment->adjustment;
						}
						if($request->price_group_id){
							if(isset($adjustment_price_group->adjustment)){
								$adj_amount += $adjustment_price_group->adjustment;
							}
						}
						if(Configuration::getValue('group_adjustment_optional') == 1){
							if($item->group_adjustment_selected){
								$adj_amount += ($item->group_adjustment_selected * $item->qty);
							}
						}


						$spec_station_id = 0;
						$spec_sales_type = 0;
						$freebie = 0;
						$is_surplus = 0;
						if(isset($item->spec_station_id) && $item->spec_station_id){
							$spec_station_id =$item->spec_station_id;
						}
						if(isset($item->spec_sales_type) && $item->spec_sales_type){
							$spec_sales_type =$item->spec_sales_type;
						}
						if(isset($item->freebies) && $item->freebies){
							$freebie =$item->freebies;
						}
						if(isset($item->is_surplus) && $item->is_surplus){
							$is_surplus =$item->is_surplus;
						}

						$unit_qty = $item->orig_qty;
						$unit_name = $item->preferred_unit;

						$unit_qty = ($unit_qty) ? $unit_qty : 0;
						$unit_name = ($unit_name) ? $unit_name : '';

						$current_total = $price->price + $adj_amount + $ind_adjustment;
						$total_price = $total_price +  ($current_total * $qty);
						$order_details->create(array(
							'wh_orders_id' => $lastItOrder,
							'station_id' => $spec_station_id,
							'spec_sales_type' => $spec_sales_type,
							'item_id' => $item->item_id,
							'price_id' => $price->id,
							'qty' => $qty,
							'created' => $now,
							'modified' => $now,
							'price_adjustment' => $adj_amount,
							'company_id' => $user->data()->company_id,
							'is_active' => 1,
							'is_freebie' => $freebie ,
							'is_surplus' => $is_surplus ,
							'terms' => $terms,
							'member_adjustment' => $alladj,
							'adjustment_remarks' => $adjustment_remarks,
							'original_qty' => $qty,
							'unit_qty' => $unit_qty,
							'preferred_unit' => $unit_name,
						));

							// update member terms if there is any
							$member_terms = new Member_term();
							$member_terms->updateSingleUseTerms($request->member_id,$item->item_id);

							// update pending

							$is_use = (isset($item->is_use) && !empty($item->is_use)) ? $item->is_use : 0;
							if($is_use){
								$wh_pending_order = new Wh_order_pending();
								$wh_pending_order->update(['status' => 2],$is_use);
							}
					}
					// check if there is assemble item on list
					$hasass = $order->hasAssembleItem($itemids);
					if($hasass->cnt){
						$order->update(
							array(
							'has_assemble_item' => 1
							),$lastItOrder);
					}

					// add custom avision
					__avisionOrder($lastItOrder,$member_name,$request->member_id,$total_price);


					echo json_encode(array('success' => true));
				} else {
					echo json_encode(array('failed' => true));
				}
					//isvalid end
				} else {
					echo json_encode(array('failed' => true,'message' => $nowValid));
				}

			} else {
				echo json_encode(array('failed' => true));
			}

		}
	}
	function __avisionOrder($order_id,$member_name,$member_id,$total_price){
		// assign batch
		if($member_id){
			$batch = new Wh_order_batch();
			$batch->create(
				[
					'batch_name' => date('m/d/Y') . " " . $member_name,
					'status' => 1,
					'store_type' => 'Offline',
				]
			);

			$batch_id = $batch->getInsertedId();



			// add pament na agad

			$payment = new Payment();

			$dt_paid = time();
			$user = new User();
			$payment->create(array(
				'created' => $dt_paid,
				'company_id' => $user->data()->company_id,
				'is_active' => 1
			));

			$payment_lastid = $payment->getInsertedId();

			$wh = new Wh_order();
			$wh->update(
				['status' => 3,'batch_id' => $batch_id,'payment_id' => $payment_lastid], $order_id
			);


			$pcredit = new Member_credit();
			$amount_due = $total_price;
			$pcredit->create(array(
				'amount' =>$amount_due,
				'is_active' => 1,
				'created' => $dt_paid,
				'modified' => $dt_paid,
				'payment_id' => $payment_lastid,
				'member_id' => $member_id,
				'is_cod' => 0
			));
		}



	}

	function sendPaymentWh(){

		$user = new User();

		$payment_cash = Input::get('payment_cash');
		$payment_con = Input::get('payment_con');
		$payment_con_freebies = Input::get('payment_con_freebies');
		$payment_member_credit = Input::get('payment_member_credit');
		$payment_member_deduction = Input::get('payment_member_deduction');
		$override_payment_date = Input::get('override_payment_date');

		$member_credit_cod = Input::get('member_credit_cod');
		$payment_credit = Input::get('payment_credit');
		$payment_bt = Input::get('payment_bt');
		$payment_cheque = Input::get('payment_cheque');
		$order_id = Input::get('order_id');
		$terminal_id = Input::get('terminal_id');
		$arr_op_ids = Input::get('arr_op_ids');
		$totalpayment = Input::get('totalpayment');
		$payment = new Payment();
		if(!$terminal_id) {
			die("Please set up terminal first.");
		}
		$scompany =$user->data()->company_id;
		if($override_payment_date){
			$dt_paid = strtotime($override_payment_date);
		} else {
			$dt_paid = time();
		}
		$payment->create(array(
			'created' => $dt_paid,
			'company_id' => $scompany,
			'is_active' => 1
		));
		$whOrder = new Wh_order($order_id);
		if(Configuration::getValue('wallet') == 1){
			if($whOrder->data()->member_id){
				$percentage_wallet = getInventoryPercentageWallet($whOrder->data()->member_id,$whOrder->data()->branch_id);
				$percentage_wallet = str_replace('%','',$percentage_wallet);
				$percentage_wallet = $percentage_wallet / 100;
				$total_e_wallet_credit = $percentage_wallet * $totalpayment;

				$memUser = new User();
				$memUserDetails = $memUser->getUserIdOfMember($whOrder->data()->member_id);
				if(isset($memUserDetails->id) && !empty($memUserDetails->id)){
					 // insert to member
					$wallet = new Wallet();
					$wallet->updateUserWallet($user,$memUserDetails->id,$total_e_wallet_credit,0,"Add wallet from Order Id # " .$whOrder->data()->id);
					// deduct to ho
					$walletFor = $wallet->getDeductOrders();
					if(isset($walletFor->id)){
							$wallet->updateCompanyWallet($user,$walletFor->id,$total_e_wallet_credit,"Deduct wallet from Order Id #".$whOrder->data()->id,1);
					}
				}


			}
		}


		$payment_lastid = $payment->getInsertedId();
		$member_id =$whOrder->data()->member_id;
		$cashier_id = $user->data()->id;
		$station_id=0;
		// update n din ung invoice or dr ?

		// start insert payment
		$sdr ='';$sinv ='';$sir =''; // change later
		$sdr = ($sdr) ? 'Dr: '.$sdr:'';
		$sinv = ($sinv) ? 'Inv: '.$sinv:'';
		$sir = ($sir) ? 'Ir: '.$sir:'';
		$sdate = time();

		$terminal = new Terminal();
		$terminal_mon = new Terminal_mon();

		if($payment_credit){
			// insert credit
			$payment_credit = json_decode($payment_credit,true);
			$credit = new Credit();
			$total_amount_cc = 0;

			foreach($payment_credit as $c){
				$total_amount_cc += $c['amount'];
				$credit->create(array(
					'card_number' => $c['credit_number'],
					'amount' => $c['amount'],
					'bank_name'=>$c['bank_name'],
					'lastname'=>$c['lastname'],
					'firstname'=>$c['firstname'],
					'middlename'=>$c['middlename'],
					'company'=>$c['comp'],
					'address'=>$c['add'],
					'zip' => $c['postal'],
					'contacts' => $c['phone'],
					'email' => $c['email'],
					'remarks' => $c['remarks'],
					'card_type' => $c['card_type'],
					'trace_number' => $c['trace_number'],
					'approval_code' => $c['approval_code'],
					'date' =>  strtotime($c['date']),
					'is_active' => 1,
					'created' => $dt_paid,
					'modified' => $dt_paid,
					'payment_id' => $payment_lastid
				));
			}

			$prevamount = $terminal->getTAmount($terminal_id,2);
			$prevamount = ($prevamount->t_amount_cc) ? $prevamount->t_amount_cc:0;
			$to_amount = $total_amount_cc + $prevamount;

			$terminal->update(array(
				't_amount_cc' => $to_amount
			),$terminal_id);
			$terminal_mon->create( array(
				'terminal_id' => $terminal_id,
				'user_id' => $cashier_id,
				'from_amount' =>$prevamount,
				'amount' =>$total_amount_cc,
				'to_amount'=>$to_amount,
				'status' => 1,
				'remarks' => "POS $sinv $sdr $sir",
				'is_active' => 1,
				'company_id' => $scompany,
				'p_type' => 2,
				'created' => $sdate
			));
		}
		if($payment_bt){
			// insert bank transfer
			$payment_bt = json_decode($payment_bt,true);
			$bank_transfer = new Bank_transfer();
			$total_amount_bt = 0;
			foreach($payment_bt as $c){
				$total_amount_bt += $c['amount'];
				$bank_transfer->create(array(
					'bankfrom_account_number' => $c['credit_number'],
					'amount' => $c['amount'],
					'bankfrom_name'=>$c['bank_name'],
					'bankto_account_number' => $c['bt_bankto_account_number'],
					'bankto_name' => $c['bt_bankto_name'],
					'lastname'=>$c['lastname'],
					'firstname'=>$c['firstname'],
					'middlename'=>$c['middlename'],
					'company'=>$c['comp'],
					'address'=>$c['add'],
					'zip' => $c['postal'],
					'contacts' => $c['phone'],
					'is_active' => 1,
					'created' => $dt_paid,
					'date' => strtotime($c['date']),
					'modified' => $dt_paid,
					'payment_id' => $payment_lastid
				));
			}
			$prevamount = $terminal->getTAmount($terminal_id,4);
			$prevamount = ($prevamount->t_amount_bt) ? $prevamount->t_amount_bt:0;
			$to_amount = $total_amount_bt + $prevamount;

			$terminal->update(array(
				't_amount_bt' => $to_amount
			),$terminal_id);
			$terminal_mon->create( array(
				'terminal_id' => $terminal_id,
				'user_id' => $cashier_id,
				'from_amount' =>$prevamount,
				'amount' =>$total_amount_bt,
				'to_amount'=>$to_amount,
				'status' => 1,
				'remarks' => "POS $sinv $sdr $sir",
				'is_active' => 1,
				'company_id' => $scompany,
				'p_type' => 4,
				'created' => $sdate
			));
		}
		if($payment_cheque){
			// insert cheque
			$payment_cheque = json_decode($payment_cheque,true);
			$cheque = new Cheque();
			$total_amount_ch = 0;
			foreach($payment_cheque as $c){
				$total_amount_ch += $c['amount'];
				$cheque->create(array(
					'check_number' => $c['cheque_number'],
					'amount' => $c['amount'],
					'bank'=>$c['bank_name'],
					'payment_date' => strtotime($c['date']),
					'lastname'=>$c['lastname'],
					'firstname'=>$c['firstname'],
					'middlename'=>$c['middlename'],
					'contacts' => $c['phone'],
					'is_active' => 1,
					'created' => $dt_paid,
					'modified' => $dt_paid,
					'payment_id' => $payment_lastid
				));
			}
			$prevamount = $terminal->getTAmount($terminal_id,3);
			$prevamount = ($prevamount->t_amount_ch) ? $prevamount->t_amount_ch:0;
			$to_amount = $total_amount_ch + $prevamount;

			$terminal->update(array(
				't_amount_ch' => $to_amount
			),$terminal_id);
			$terminal_mon->create( array(
				'terminal_id' => $terminal_id,
				'user_id' => $cashier_id,
				'from_amount' =>$prevamount,
				'amount' =>$total_amount_ch,
				'to_amount'=>$to_amount,
				'status' => 1,
				'remarks' => "POS $sinv $sdr $sir",
				'is_active' => 1,
				'company_id' => $scompany,
				'p_type' => 3,
				'created' => $sdate
			));
		}
		if($payment_cash){
			// insert cash
			$pcash = new Cash();
			$total_amount = $payment_cash;
			$pcash->create(array(
				'amount' =>$payment_cash,
				'is_active' => 1,
				'created' => $dt_paid,
				'modified' => $dt_paid,
				'payment_id' => $payment_lastid
			));
			$prevamount = $terminal->getTAmount($terminal_id,1);
			$prevamount = ($prevamount->t_amount) ? $prevamount->t_amount:0;
			$to_amount = $total_amount + $prevamount;

			$terminal->update(array(
				't_amount' => $to_amount
			),$terminal_id);
			$terminal_mon->create( array(
				'terminal_id' => $terminal_id,
				'user_id' => $cashier_id,
				'from_amount' =>$prevamount,
				'amount' =>$total_amount,
				'to_amount'=>$to_amount,
				'status' => 1,
				'remarks' => "POS $sinv $sdr $sir",
				'is_active' => 1,
				'company_id' => $scompany,
				'p_type' => 1,
				'created' => $sdate
			));
		}

		if($payment_member_deduction){
			// insert cash
			$payment_member_deduction = json_decode($payment_member_deduction);
			if(count($payment_member_deduction)){
				foreach($payment_member_deduction as $deduct_member){
					$pdeduct = new Deduction();
					$deduction_amount = ($deduct_member->amount) ? $deduct_member->amount : 0;
					$member_deduction_remarks = ($deduct_member->remarks) ? $deduct_member->remarks : '';
					$member_deduction_addtl_remarks = ($deduct_member->addtl_remarks) ? $deduct_member->addtl_remarks : '';
					$member_deduction_approved = ($deduct_member->is_approved) ? 1 : 0;
					$pdeduct->create(array(
						'amount' =>$deduction_amount,
						'is_active' => 1,
						'created' => $dt_paid,
						'remarks' => $member_deduction_remarks,
						'payment_id' => $payment_lastid,
						'addtl_remarks' => $member_deduction_addtl_remarks,
						'member_id' => $deduct_member->member_id,
						'status' => $member_deduction_approved,
					));
				}
			}
		}

		if($payment_member_credit){
			// insert cash

			$pcredit = new Member_credit();
			$pcredit->create(array(
				'amount' =>$payment_member_credit,
				'is_active' => 1,
				'created' => $dt_paid,
				'modified' => $dt_paid,
				'payment_id' => $payment_lastid,
				'member_id' => $member_id,
				'is_cod' => $member_credit_cod
			));
		}
		if($payment_con){
			// insert cash
			$pcon = new Payment_consumable();
			$pcon->create(array(
				'amount' =>$payment_con,
				'is_active' => 1,
				'created' => $dt_paid,
				'modified' => $dt_paid,
				'payment_id' => $payment_lastid,
				'member_id' => $member_id
			));

			//check exact amount

			$mem = new Member();
			$mycon = $mem->getMyConsumableAmount($member_id);
			$exact = $mem->getExactAmount($member_id,$payment_con);
			if(isset($exact->amount) && $exact->amount){
					$toupdate = new Consumable_amount($exact->id);
					$arr_info = [];
					if($toupdate->data()->payment_ids){
						$arr_info = json_decode($toupdate->data()->payment_ids,true);
					}
					$arr_info[] = ['amount' => $payment_con,'payment_id' => $payment_lastid];
					$toupdate->update(array('amount' => 0, 'modified' => time(),'is_exact'=>$payment_lastid, 'payment_ids' => json_encode($arr_info)), $exact->id);
			} else {
					if($mycon){
						$arr_info = [];
						foreach($mycon as $c){
							if($payment_con) {
								$notvalid = $mem->getNotYetValidCheque($c->payment_id);
								if($notvalid->cheque_amount) {
									$validamount = $c->amount - $notvalid->cheque_amount;
									$notv = $notvalid->cheque_amount;
								} else {
									$validamount = $c->amount;
									$notv = 0;
								}
								// jumphere
								$toupdate = new Consumable_amount($c->id);
									$arr_info = [];
									if($toupdate->data()->payment_ids){
										$arr_info = json_decode($toupdate->data()->payment_ids,true);
									}
								if($validamount >= $payment_con) {

									$leftamount = ($validamount - $payment_con) + $notv ;
									$arr_info[] = ['amount'=> $payment_con,'payment_id' => $payment_lastid];
									$payment_con =0;
									$toupdate->update(array('amount' => $leftamount, 'modified' => time(),'payment_ids' => json_encode($arr_info)), $c->id);
								} else {
									$leftamount = $notv;
									$allleft = $toupdate->data()->amount;
									if($allleft){
										$arr_info[] = ['amount'=> $allleft,'payment_id' => $payment_lastid];
									}
									$toupdate->update(array('amount' => $leftamount, 'modified' => time(),'payment_ids' => json_encode($arr_info)), $c->id);
									$payment_con = $payment_con - $validamount;
								}
							}
						}
				}
			}
		}



		if($payment_con_freebies){
			// insert cash
			$pcon = new Payment_consumable_freebies();
			$pcon->create(array(
				'amount' =>$payment_con_freebies,
				'is_active' => 1,
				'created' => $dt_paid,
				'modified' => $dt_paid,
				'payment_id' => $payment_lastid,
				'member_id' => $member_id
			));


			$mem = new Member();
			$mycon = $mem->getMyConsumableFreebies($member_id);
			if($mycon){

				foreach($mycon as $c){
					if($payment_con_freebies) {

						$validamount = $c->amount;

						$toupdate = new Consumable_freebies();
						if($validamount > $payment_con_freebies) {
							$leftamount = ($validamount - $payment_con_freebies);
							$payment_con_freebies =0;
							$toupdate->update(array('amount' => $leftamount, 'modified' => time()), $c->id);
						} else {
							$leftamount = 0;
							$toupdate->update(array('amount' => $leftamount, 'modified' => time()), $c->id);
							$payment_con_freebies = $payment_con_freebies - $validamount;
						}
					}
				}
			}
		}
		$user_credit = new User_credit();
		$arr_op_ids = json_decode($arr_op_ids,true);
		if($arr_op_ids){
			 foreach($arr_op_ids as $op_id){
			    if($op_id['id'] && is_numeric($op_id['id'])){
				      $cur_dep = new User_credit($op_id['id']);
				      $total_dep = 0;
				      if( $cur_dep->data()->status == 1){
				          $total_dep = $cur_dep->data()->json_data;
				      } else if( $cur_dep->data()->status == 2){
				          $decoded_dep = json_decode($cur_dep->data()->json_data);
				          foreach($decoded_dep as $dep){
				            $total_dep += $dep->amount;
				          }
				      } else if( $cur_dep->data()->status == 3){
				          $decoded_dep = json_decode($cur_dep->data()->json_data);
				          foreach($decoded_dep as $dep){
				            $total_dep += $dep->amount;
				          }
				      }else if( $cur_dep->data()->status == 4){
				          $decoded_dep = json_decode($cur_dep->data()->json_data);
				          foreach($decoded_dep as $dep){
				            $total_dep += $dep->amount;
				          }
				      }
					$used_total = $cur_dep->data()->used_total + $op_id['amount'];
					if($total_dep == $used_total){
						$is_used = 1;
					} else {
						$is_used = 0;
					}
					$payment_user_credit_used = $cur_dep->data()->payment_id;

					if($payment_user_credit_used){
						$payment_user_credit_used = json_decode($payment_user_credit_used, true);
						$payment_user_credit_used[]= ['payment_id' =>$payment_lastid, 'amount' => $op_id['amount']];
					} else {
						$payment_user_credit_used   = [];
						$payment_user_credit_used[] = ['payment_id' =>$payment_lastid, 'amount' => $op_id['amount']];
					}
				    $user_credit->update(['payment_id' => json_encode($payment_user_credit_used),'is_used'=>$is_used,'used_total'=>$used_total],$op_id['id']);

			    }
			 }
		}
		$toUpdateArr = ['payment_id'=>$payment_lastid];
		if(Configuration::thisCompany('avision')){
			if($_SERVER['HTTP_HOST'] != 'localhost:81')
				$toUpdateArr['status'] = 3;

		}
		$whOrder->update(
			$toUpdateArr,
				$order_id
		);

		echo "Payment received";
	}

	function sendPaymentMemberCredit(){

		$user = new User();

		if(!$user->data()->id) {
			die("Session expired. Please log in again.");
		}

		$payment_cash = Input::get('payment_cash');
		$payment_con = Input::get('payment_con');
		$payment_con_freebies = Input::get('payment_con_freebies');
		$payment_member_credit = Input::get('payment_member_credit');
		$payment_member_deduction = Input::get('payment_member_deduction');
		$payment_credit = Input::get('payment_credit');
		$payment_bt = Input::get('payment_bt');
		$payment_cheque = Input::get('payment_cheque');
		$member_credit_id = Input::get('member_credit_id');
		 $arr_op_ids = Input::get('arr_op_ids');
		 $override_payment_date = Input::get('override_payment_date');
		$memcls = new Member_credit();
		$memdata = $memcls->getMemberCreditDetials($member_credit_id);
			if($override_payment_date){
			$dt_paid = strtotime($override_payment_date);
		} else {
			$dt_paid = time();
		}
		$terminal_id = $memdata->terminal_id;
		if(!$terminal_id){
			$terminal_id =  Input::get('terminal_id');
		}
		if(!$terminal_id) {
			die("Please set up terminal first.");
		}
		$scompany =$user->data()->company_id;
		/* no need for payment_id */

		// get member credit details

		$payment_lastid = $memdata->payment_id;
		$member_id =$memdata->member_id;
		$cashier_id = $user->data()->id;
		$station_id=0;
		// update n din ung invoice or dr ?

		// start insert payment
		$sdr =$memdata->dr;$sinv =$memdata->invoice;$sir =$memdata->ir; // change later
		$sdr = ($sdr) ? 'Dr: '.$sdr:'';
		$sinv = ($sinv) ? 'Inv: '.$sinv:'';
		$sir = ($sir) ? 'Ir: '.$sir:'';
		$sdate = time();

		$terminal = new Terminal();
		$terminal_mon = new Terminal_mon();

		if($payment_credit){
			// insert credit
			$payment_credit = json_decode($payment_credit,true);
			$credit = new Credit();
			$total_amount_cc = 0;

			Log::addLog(
				$user->data()->id,
				$user->data()->company_id,
				"ADD PAYMENT CREDIT $payment_lastid",
				'ajax_accounting.php'
			);

			foreach($payment_credit as $c){
				$total_amount_cc += $c['amount'];
				$credit->create(array(
					'card_number' => $c['credit_number'],
					'amount' => $c['amount'],
					'bank_name'=>$c['bank_name'],
					'lastname'=>$c['lastname'],
					'firstname'=>$c['firstname'],
					'middlename'=>$c['middlename'],
					'company'=>$c['comp'],
					'address'=>$c['add'],
					'zip' => $c['postal'],
					'contacts' => $c['phone'],
					'email' => $c['email'],
					'remarks' => $c['remarks'],
					'card_type' => $c['card_type'],
					'trace_number' => $c['trace_number'],
					'approval_code' => $c['approval_code'],
					'date' =>  strtotime($c['date']),
					'is_active' => 1,
					'created' => $dt_paid,
					'modified' => $dt_paid,
					'payment_id' => $payment_lastid
				));
			}

			$prevamount = $terminal->getTAmount($terminal_id,2);
			$prevamount = ($prevamount->t_amount_cc) ? $prevamount->t_amount_cc:0;
			$to_amount = $total_amount_cc + $prevamount;

			$terminal->update(array(
				't_amount_cc' => $to_amount
			),$terminal_id);
			$terminal_mon->create( array(
				'terminal_id' => $terminal_id,
				'user_id' => $cashier_id,
				'from_amount' =>$prevamount,
				'amount' =>$total_amount_cc,
				'to_amount'=>$to_amount,
				'status' => 1,
				'remarks' => "POS $sinv $sdr $sir",
				'is_active' => 1,
				'company_id' => $scompany,
				'p_type' => 2,
				'created' => $sdate
			));
		}
		if($payment_bt){
			// insert bank transfer
			$payment_bt = json_decode($payment_bt,true);
			$bank_transfer = new Bank_transfer();
			$total_amount_bt = 0;
			Log::addLog(
				$user->data()->id,
				$user->data()->company_id,
				"ADD PAYMENT BT $payment_lastid",
				'ajax_accounting.php'
			);

			foreach($payment_bt as $c){
				$total_amount_bt += $c['amount'];
				$bank_transfer->create(array(
					'bankfrom_account_number' => $c['credit_number'],
					'amount' => $c['amount'],
					'bankfrom_name'=>$c['bank_name'],
					'bankto_account_number' => $c['bt_bankto_account_number'],
					'bankto_name' => $c['bt_bankto_name'],
					'lastname'=>$c['lastname'],
					'firstname'=>$c['firstname'],
					'middlename'=>$c['middlename'],
					'company'=>$c['comp'],
					'address'=>$c['add'],
					'zip' => $c['postal'],
					'contacts' => $c['phone'],
					'date' =>  strtotime($c['date']),
					'is_active' => 1,
					'created' => $dt_paid,
					'modified' => $dt_paid,
					'payment_id' => $payment_lastid
				));
			}
			$prevamount = $terminal->getTAmount($terminal_id,4);
			$prevamount = ($prevamount->t_amount_bt) ? $prevamount->t_amount_bt:0;
			$to_amount = $total_amount_bt + $prevamount;

			$terminal->update(array(
				't_amount_bt' => $to_amount
			),$terminal_id);
			$terminal_mon->create( array(
				'terminal_id' => $terminal_id,
				'user_id' => $cashier_id,
				'from_amount' =>$prevamount,
				'amount' =>$total_amount_bt,
				'to_amount'=>$to_amount,
				'status' => 1,
				'remarks' => "POS $sinv $sdr $sir",
				'is_active' => 1,
				'company_id' => $scompany,
				'p_type' => 4,
				'created' => $sdate
			));
		}
		if($payment_cheque){
			// insert cheque
			$payment_cheque = json_decode($payment_cheque,true);
			$cheque = new Cheque();
			$total_amount_ch = 0;
			Log::addLog(
				$user->data()->id,
				$user->data()->company_id,
				"ADD PAYMENT CHEQUE $payment_lastid",
				'ajax_accounting.php'
			);
			foreach($payment_cheque as $c){
				$total_amount_ch += $c['amount'];
				$cheque->create(array(
					'check_number' => $c['cheque_number'],
					'amount' => $c['amount'],
					'bank'=>$c['bank_name'],
					'payment_date' => strtotime($c['date']),
					'lastname'=>$c['lastname'],
					'firstname'=>$c['firstname'],
					'middlename'=>$c['middlename'],
					'contacts' => $c['phone'],
					'is_active' => 1,
					'created' => $dt_paid,
					'modified' => $dt_paid,
					'payment_id' => $payment_lastid,
					'from_credit' => $member_credit_id,
				));
			}
			$prevamount = $terminal->getTAmount($terminal_id,3);
			$prevamount = ($prevamount->t_amount_ch) ? $prevamount->t_amount_ch:0;
			$to_amount = $total_amount_ch + $prevamount;

			$terminal->update(array(
				't_amount_ch' => $to_amount
			),$terminal_id);
			$terminal_mon->create( array(
				'terminal_id' => $terminal_id,
				'user_id' => $cashier_id,
				'from_amount' =>$prevamount,
				'amount' =>$total_amount_ch,
				'to_amount'=>$to_amount,
				'status' => 1,
				'remarks' => "POS $sinv $sdr $sir",
				'is_active' => 1,
				'company_id' => $scompany,
				'p_type' => 3,
				'created' => $sdate
			));
		}
		if($payment_cash){
			// insert cash
			$pcash = new Cash();
			$total_amount = $payment_cash;
			Log::addLog(
				$user->data()->id,
				$user->data()->company_id,
				"ADD PAYMENT CASH $payment_lastid",
				'ajax_accounting.php'
			);
			$pcash->create(array(
				'amount' =>$payment_cash,
				'is_active' => 1,
				'created' => $dt_paid,
				'modified' => $dt_paid,
				'payment_id' => $payment_lastid,
				'from_credit' => $member_credit_id,
			));
			$prevamount = $terminal->getTAmount($terminal_id,1);
			$prevamount = ($prevamount->t_amount) ? $prevamount->t_amount:0;
			$to_amount = $total_amount + $prevamount;

			$terminal->update(array(
				't_amount' => $to_amount
			),$terminal_id);
			$terminal_mon->create( array(
				'terminal_id' => $terminal_id,
				'user_id' => $cashier_id,
				'from_amount' =>$prevamount,
				'amount' =>$total_amount,
				'to_amount'=>$to_amount,
				'status' => 1,
				'remarks' => "POS $sinv $sdr $sir",
				'is_active' => 1,
				'company_id' => $scompany,
				'p_type' => 1,
				'created' => $sdate
			));
		}
		if($payment_member_credit){
			// insert cash
			$pcredit = new Member_credit();
			Log::addLog(
				$user->data()->id,
				$user->data()->company_id,
				"ADD PAYMENT MEMBER CREDIT $payment_lastid",
				'ajax_accounting.php'
			);
			$pcredit->create(array(
				'amount' =>$payment_member_credit,
				'is_active' => 1,
				'created' => $dt_paid,
				'modified' => $dt_paid,
				'payment_id' => $payment_lastid,
				'member_id' => $member_id
			));
		}
		if($payment_con){
			// insert cash
			$pcon = new Payment_consumable();
				Log::addLog(
				$user->data()->id,
				$user->data()->company_id,
				"ADD PAYMENT CONSUMABLE $payment_lastid",
				'ajax_accounting.php'
			);
			$pcon->create(array(
				'amount' =>$payment_con,
				'is_active' => 1,
				'created' => $dt_paid,
				'modified' => $dt_paid,
				'payment_id' => $payment_lastid,
				'member_id' => $member_id
			));


			$mem = new Member();
			$mycon = $mem->getMyConsumableAmount($member_id);
			if($mycon){
				//jumphere
				foreach($mycon as $c){
					if($payment_con) {
						$notvalid = $mem->getNotYetValidCheque($c->payment_id);
						if($notvalid->cheque_amount) {
							$validamount = $c->amount - $notvalid->cheque_amount;
							$notv = $notvalid->cheque_amount;
						} else {
							$validamount = $c->amount;
							$notv = 0;
						}

						$toupdate = new Consumable_amount($c->id);
						$arr_info = [];
						if($toupdate->data()->payment_ids){
							$arr_info = json_decode($toupdate->data()->payment_ids,true);
						}
						if($validamount >= $payment_con) {
							$leftamount = ($validamount - $payment_con) + $notv ;
							$arr_info[] = ['amount'=> $payment_con,'payment_id' => $payment_lastid];
							$payment_con =0;
							$toupdate->update(array('amount' => $leftamount, 'modified' => time(),'payment_ids' => json_encode($arr_info)), $c->id);
						} else {
							$leftamount = $notv;
							$allleft = $toupdate->data()->amount;

							if($allleft){
								$arr_info[] = ['amount'=> $allleft,'payment_id' => $payment_lastid];
							}

							$toupdate->update(array('amount' => $leftamount, 'modified' => time(),'payment_ids' => json_encode($arr_info)), $c->id);
							$payment_con = $payment_con - $validamount;
						}
					}
				}
			}
		}
		if($payment_con_freebies){
			// insert cash
			$pcon = new Payment_consumable_freebies();
			Log::addLog(
				$user->data()->id,
				$user->data()->company_id,
				"ADD PAYMENT FREEBIE $payment_lastid",
				'ajax_accounting.php'
			);
			$pcon->create(array(
				'amount' =>$payment_con_freebies,
				'is_active' => 1,
				'created' => $dt_paid,
				'modified' => $dt_paid,
				'payment_id' => $payment_lastid,
				'member_id' => $member_id
			));


			$mem = new Member();
			$mycon = $mem->getMyConsumableFreebies($member_id);
			if($mycon){

				foreach($mycon as $c){
					if($payment_con_freebies) {

						$validamount = $c->amount;

						$toupdate = new Consumable_freebies();
						if($validamount > $payment_con_freebies) {
							$leftamount = ($validamount - $payment_con_freebies);
							$payment_con_freebies =0;
							$toupdate->update(array('amount' => $leftamount, 'modified' => time()), $c->id);
						} else {
							$leftamount = 0;
							$toupdate->update(array('amount' => $leftamount, 'modified' => time()), $c->id);
							$payment_con_freebies = $payment_con_freebies - $validamount;
						}
					}
				}
			}
		}

	if($payment_member_deduction){
			// insert cash
			Log::addLog(
				$user->data()->id,
				$user->data()->company_id,
				"ADD PAYMENT DEDUCTION $payment_lastid",
				'ajax_accounting.php'
			);
			$payment_member_deduction = json_decode($payment_member_deduction);
			if(count($payment_member_deduction)){
				foreach($payment_member_deduction as $deduct_member){
					$pdeduct = new Deduction();
					$deduction_amount = ($deduct_member->amount) ? $deduct_member->amount : 0;
					$member_deduction_remarks = ($deduct_member->remarks) ? $deduct_member->remarks : '';
					$member_deduction_addtl_remarks = ($deduct_member->addtl_remarks) ? $deduct_member->addtl_remarks : '';
					$member_deduction_approved = ($deduct_member->is_approved) ? 1 : 0;
					$pdeduct->create(array(
						'amount' =>$deduction_amount,
						'is_active' => 1,
						'created' => $dt_paid,
						'remarks' => $member_deduction_remarks,
						'addtl_remarks' => $member_deduction_addtl_remarks,
						'payment_id' => $payment_lastid,
						'member_id' => $deduct_member->member_id,
						'status' => $member_deduction_approved,
					));
				}
			}
		}

		// update memcreditpay
		$paymentRemarks = Input::get('paymentRemarks');
		$totalPayment = Input::get('totalpayment');

		$userfn = ucwords($user->data()->lastname . ", " . $user->data()->firstname);
		$amt = $totalPayment;
		$remarks = $paymentRemarks;
		$unpaid = $memdata->amount - $memdata->amount_paid;
		$id = Input::get('id');
		$remaining = $unpaid - $amt;
		$memcred = new Member_credit($member_credit_id);
		$paid = $memdata->amount_paid;
		$amt_paid = $paid + $amt;
		$arr = json_decode($memcred->data()->json_payment,true);
		$arr_receives = $memcred->data()->user_ids;

		if($arr_receives){
			$arr_receives = $arr_receives . "," . $user->data()->id;
		} else {
			$arr_receives = $user->data()->id;
		}
		$now = time();
		$arr[] = array('fn'=>$userfn,'amount'=>$amt,'date'=>$now,'remarks' => $remarks);
		$finalarr = json_encode($arr);
		$timenow =time();

		$user_credit = new User_credit();
		$arr_op_ids = json_decode($arr_op_ids,true);
		if($arr_op_ids){
			 foreach($arr_op_ids as $op_id){
			    if($op_id['id'] && is_numeric($op_id['id'])){
			      $cur_dep = new User_credit($op_id['id']);
			      $total_dep = 0;
			      if( $cur_dep->data()->status == 1){
			          $total_dep = $cur_dep->data()->json_data;
			      } else if( $cur_dep->data()->status == 2){
			          $decoded_dep = json_decode($cur_dep->data()->json_data);
			          foreach($decoded_dep as $dep){
			            $total_dep += $dep->amount;
			          }
			      } else if( $cur_dep->data()->status == 3){
			          $decoded_dep = json_decode($cur_dep->data()->json_data);
			          foreach($decoded_dep as $dep){
			            $total_dep += $dep->amount;
			          }
			      }else if( $cur_dep->data()->status == 4){
			          $decoded_dep = json_decode($cur_dep->data()->json_data);
			          foreach($decoded_dep as $dep){
			            $total_dep += $dep->amount;
			          }
			      }
				$used_total = $cur_dep->data()->used_total + $op_id['amount'];
				if($total_dep == $used_total){
					$is_used = 1;
				} else {
					$is_used = 0;
				}
				$payment_user_credit_used = $cur_dep->data()->payment_id;

				if($payment_user_credit_used){
					$payment_user_credit_used = json_decode($payment_user_credit_used, true);
					$payment_user_credit_used[]= ['payment_id' =>$payment_lastid, 'amount' => $op_id['amount']];
				} else {
					$payment_user_credit_used   = [];
					$payment_user_credit_used[] = ['payment_id' =>$payment_lastid, 'amount' => $op_id['amount']];
				}

			     $user_credit->update(['payment_id' => json_encode($payment_user_credit_used),'is_used'=>$is_used,'used_total'=>$used_total],$op_id['id']);
			    }


			 }
		}

		if($remaining == 0){
			// update status = 1
			// check if the user
			if($user->hasPermission("wh_agent")){
				$status = -1;
			} else {
				$status = 1;
			}

			$memcred->update(
				array(
					'amount_paid'=>$amt_paid,
					'status'=>$status,
					'json_payment'=>$finalarr,
					'modified' => $dt_paid,
					'user_id' => $user->data()->id,
					'user_ids' => $arr_receives,
					'ret_msg' => ''
				),$member_credit_id);

		} else {

			$memcred->update(
				array(
					'amount_paid'=>$amt_paid,
					'json_payment'=>$finalarr,
					'user_id' => $user->data()->id,
					'user_ids' => $arr_receives,
					'modified' => $dt_paid
				),$member_credit_id);

		}

		echo "Payment received";
	}
	function getWhOrders(){
		$user = new User();
		$whorder = new Wh_order();
		$status = Input::get('stat');
		$status = ($status) ? $status : 1;
		$user_id = 0;
		$member_id = 0;
		$approv_auth = true;

		$dt1 = Input::get('dt1');
		$dt2 = Input::get('dt2');
		$search = Input::get('search');
		$show_all = Input::get('show_all');

		//branch_id:vuecon.branch_id_filter,salestype:vuecon.salestype_filter,for_pickup:vuecon.for_pickup_filter,assemble:vuecon.assemble_filter

		$branch_id = Input::get('branch_id');
		$salestype = Input::get('salestype');
		$for_pickup = Input::get('for_pickup');
		$assemble = Input::get('assemble');

		if($status == 3){
			if($show_all){
				$dt1 = 0;
				$dt2 = 0;
			} else {
				if(!$dt1){

				} else {
					$dt1 = strtotime($dt1);
					$dt2 = strtotime($dt2 . "1 day -1 sec");
				}
			}

		} else {
			$dt1 = 0;
			$dt2 = 0;
		}

		if($user->hasPermission('wh_agent')){
			$user_id = $user->data()->id;
			$approv_auth = false;
		}

		if($user->hasPermission('wh_member')){
			$member_id = $user->data()->member_id;
			$approv_auth = false;
		}

		if($user->hasPermission('wh_order_all')){
			$user_id = 0;
			$approv_auth = true;
		}

		$my_auth = 0;
		$my_id = 0;
		if($approv_auth){

			$auth_approval = new Approval_auth();
			$my_auth = $auth_approval->getMyAuth($user->data()->id);

			if(isset($my_auth->ref_values) && !empty($my_auth->ref_values)){
				$my_auth = $my_auth->ref_values;
				$my_id =  $user->data()->id;
			} else {
				$my_auth = $user->data()->branch_id;
				$my_id =  $user->data()->id;
			}

		}

		// count
		$limit = 200;
		$countRecord = $whorder->countRecordOrder($user->data()->company_id,$user_id,$member_id,$my_auth,$my_id,$status,$dt1,$dt2,$branch_id,$salestype,$for_pickup,$assemble,$search);

		$total_pages = $countRecord->cnt;

		$stages = 3;
		$page = Input::get('page');
		$page = (int) $page;
		if($page) {
			$start = ($page - 1) * $limit;
		} else {
			$start = 0;
		}
		$nav = returnNav($page, $total_pages, $limit, $stages);

		$orders = $whorder->getOrders($user->data()->company_id,$user_id,$member_id,$my_auth,$my_id,$status,$dt1,$dt2,$start, $limit,$branch_id,$salestype,$for_pickup,$assemble,$search);

		$arr = [];
		if(!$orders) {
			echo json_encode($arr);
			exit();
		}
		$withinvarr = ['Without Invoice', 'With Invoice'];
		$rechedule_allowed = Configuration::getValue('reschedule_order');
		foreach($orders as $order){
			$time_diff = getTimeDiff(time() - $order->created);
			if($order->mln){
				$order->fullname = ucwords($order->mln . ", " . $order->mfn);
				if($order->b2_member_id){
					$order->fullname .= "<span class='span-block'> $order->branch_name_to</span>";
				}
			} else {
				$order->fullname = $order->branch_name_to;
			}
			$from_web_customer = "";
			if($order->walkin_info){
				$data_walkin = json_decode($order->walkin_info,true);
				if(isset($data_walkin['name'])){
				$from_web_customer = $data_walkin['name'] . " Contact: $data_walkin[phone]";
				$order->fullname = $from_web_customer;
				}
			}
			$order->time_diff = $time_diff;
			$order->fullnameUser = ($order->lastname) ? ucwords($order->lastname . ", " . $order->firstname) : 'None';
			$order->ordered_date = date('F d, Y H:i:s A',$order->created);

			if($order->approved_date){
				$order->approved_date = date('F d, Y H:i:s A',$order->approved_date);
			} else {
				$order->approved_date='N/A';
			}

			$order->total= number_format($order->total_price,2,'.','');
			$order->total_price = number_format($order->total_price,2);
			//if($order->invoice) $order->invoice = "<i class='fa fa-ban'></i>";
			//if($order->dr) $order->dr = "<i class='fa fa-ban'></i>";
			if(!$order->remarks) $order->remarks = "<i class='fa fa-ban'></i>";
			 $order->is_for_pickup = $order->for_pickup;
			if($order->for_pickup == 1){
			 $order->for_pickup = "For Pickup";
			} else if($order->for_pickup == 2){
			 $order->for_pickup = "Cashier Transaction";
			} else $order->for_pickup='';


			if($order->truck_name){
				$order->truck = $order->truck_name . "<br><span class='label label-primary'>".$order->truck_description."</span>";
			} else {
				$order->truck = "<i class='fa fa-ban'></i>";
			}
			if($order->shipping_name){
				$order->shipping_name = "VIA: ".$order->shipping_name;
			} else {
				$order->shipping_name = "";
			}
			if($order->client_po){
				$order->client_po = "PO#: ".$order->client_po;
			} else {
				$order->client_po = "";
			}
			$helperret = "<i class='fa fa-ban'></i>";
			if($order->helpers){
				$helperret='';
				if(strpos($order->helpers,'|') > 0){
					$exhel = explode('|',$order->helpers);
					foreach($exhel as $helpind){
					$helperret .=" <span class='label label-primary'>$helpind</span>";
					}
				} else {
					$helperret.=" <span class='label label-primary'>$order->helpers</span>";
				}
			}
			$order->helpers = $helperret;
			if($order->with_inv){
				$order->with_inv = $withinvarr[$order->with_inv];
			} else {
				$order->with_inv='';
			}

			if($order->driver){
				$order->driver = "<span class='label label-primary'>$order->driver</span>";
			} else {
			$order->driver = "<i class='fa fa-ban'></i>";
			}
			$now = time();
			if($order->is_scheduled)
			{
				$order->delivery_date = date('m/d/Y',$order->is_scheduled);
				$ordersched = $order->is_scheduled;
				$diff = $now - $ordersched;

				if($rechedule_allowed && is_numeric($rechedule_allowed)){
					$rechedule_allowed = 86400 * $rechedule_allowed;
				} else {
					$rechedule_allowed = 86400 * 3; // 3 days default
				}
				if($diff >= $rechedule_allowed ) //ten days
				{
					$order->canBeResched = 0;
				} else {
					$order->canBeResched = 1;
				}
				$order->is_scheduled = date('F d, Y',$order->is_scheduled);
				if($order->is_scheduled == date('F d, Y')){
				$order->is_current = 1;
				} else {
				$order->is_current = 0;
				}


			}
			/*

			S = success
			F = Failure
			P = Pending
			U = Unknown
			R = Refund
			K = Chargeback
			V = Void
			A = Authorized



		*/
			$dragonPayStatus['S'] = "Success";
			$dragonPayStatus['P'] = "Pending";
			$dragonPayStatus['F'] = "Failure";
			$dragonPayStatus['U'] = "Unknown";
			$dragonPayStatus['R'] = "Refund";
			$dragonPayStatus['K'] = "Chargeback";
			$dragonPayStatus['V'] = "Void";
			$dragonPayStatus['A'] = "Authorized";

			$pref_payment = '';
			if(isset($order->pref_payment) && $order->pref_payment == 1){
				$pref_payment = "COD";
			} else if (isset($order->pref_payment) && $order->pref_payment == 2){
				$pref_payment = "BT";
			} else if (isset($order->pref_payment) && $order->pref_payment == 3){
				$dp_stat="";
				if(isset($dragonPayStatus[$order->dragonpay_status])){
					$dp_stat = "<span class='span-block'>".$dragonPayStatus[$order->dragonpay_status]."</span>";
				}
				if($order->dragonpay_status == "NP"){
					$dp_stat = "<span class='span-block'>No payment</span>";
				}
				$pref_payment = "DragonPay" . $dp_stat ;

			}
			$order->pref_payment = $pref_payment;
			$file_name = "";
			if(isset($order->file_name) && $order->file_name){
				if(file_exists('../uploads/' . $order->file_name)){
					$file_name = '../uploads/' . $order->file_name;
				} else {
					$file_name = "http://bestphonedeals.ph/attachments/".$order->file_name;
				}
			}
			$order->file_name = $file_name;


			$arr[] = $order;
		}

		echo json_encode(['items' => $arr,'nav'=> $nav]);
	}
	function getOrderCount(){
		$user = new User();
		$whorder = new Wh_order();
		$user_id = 0;
		$member_id = 0;
		$approv_auth = true;
		if($user->hasPermission('wh_agent')){
			$user_id = $user->data()->id;
			$approv_auth = false;
		}
		if($user->hasPermission('wh_member')){
			$member_id = $user->data()->member_id;
			$approv_auth = false;
		}
		if($user->hasPermission('wh_order_all')){
			$user_id = 0;
			$approv_auth = true;
		}
		$my_auth = 0;
		$my_id = 0;
		if($approv_auth){
			$auth_approval = new Approval_auth();
			$my_auth = $auth_approval->getMyAuth($user->data()->id);
			if(isset($my_auth->ref_values) && !empty($my_auth->ref_values)){
				$my_auth = $my_auth->ref_values;
				$my_id =  $user->data()->id;
			} else {
				$my_auth = $user->data()->branch_id;
				$my_id =  $user->data()->id;
			}
		}

		$orders = $whorder->getOrderCount($user->data()->company_id,$user_id,$member_id,$my_auth,$my_id);
		echo json_encode($orders);
	}

	function scheduleOrderWh(){

		$order_id = Input::get('order_id');
		$schedule_date = Input::get('schedule_date');
		$truck_id = Input::get('truck_id');
		$driver_id = Input::get('driver_id');
		$driver = new Driver($driver_id);
		$helpers = json_decode(Input::get('helpers_id'),true);
		$order_details = json_decode(Input::get('order_details'));
		$truck_id= ($truck_id) ? $truck_id : 0;
		$user = new User();
		$myOrder = new Wh_order($order_id);
		$scheduleOrder = new Wh_order_date();

		$now = time();

		if($order_details){

			$to_insert_to_new_order_id = [];
			foreach($order_details as $od){
				if($od->to_exclude){
					$to_insert_to_new_order_id[] = $od->id;

				}
			}
			if(count($to_insert_to_new_order_id)){
				// insert wh order same,

				$copyOrder = $myOrder->copyOrder($order_id);
				$myOrder->update(
					['remarks' => "Order From ID $order_id"], $copyOrder
				);
				$wh_details = new Wh_order_details();
				foreach($to_insert_to_new_order_id as $update_id){
					$wh_details->update(
						['wh_orders_id' => $copyOrder] , $update_id
					);
				}
			}
		}

		if($schedule_date){
		$helperlist = "";
			if(count($helpers)){
			foreach($helpers as $h ){
				$helper = new Delivery_helper($h);
				$helperlist .= $helper->data()->name . "|";
			}
				$helperlist = rtrim($helperlist,"|");
			}
			$schedule_date = strtotime($schedule_date);

			$scheduleOrder->create(array(
				'company_id' => $user->data()->company_id,
				'user_id' => $user->data()->id,
				'created' =>$now,
				'modified' =>$now,
				'is_active' =>1,
				'schedule_date' =>$schedule_date,
				'wh_order_id' =>$order_id
			));

			$drivername='';
			if(isset($driver->data()->name) && !empty($driver->data()->name)){
			$drivername = $driver->data()->name;
			}
			// flowchange
			if($myOrder->data()->member_id == 0 && $myOrder->data()->to_branch_id != 0){ // @todo
				// Transfer mon
				$transferMon = new Transfer_inventory_mon();
				$transferMon->create(array(
					'branch_id' => $myOrder->data()->to_branch_id,
					'branch_from' => $myOrder->data()->branch_id,
					'company_id' => $myOrder->data()->company_id,
					'get_stock' => 1,
					'from_od' => $myOrder->data()->id,
					'del_schedule' => $schedule_date,
					'truck_id' => $truck_id,
					'helpers' => $helperlist,
					'driver' => $drivername,
					'from_where' => 'From Order',
					'status' => 1,
					'is_active' => 1,
					'created' => $now,
					'modified' => $now,
				));
				$orderlastid = $transferMon->getInsertedId();
				$whdet = new Wh_order_details();
				$details = $whdet->getOrderDetails($order_id);
				$transferdetails = new Transfer_inventory_details();
				foreach($details as $det){
					if($det->is_bundle == 0){
						$transferdetails->create(array(
							'transfer_inventory_id' => $orderlastid,
							'item_id' => $det->item_id,
							'rack_id_from' => 0,
							'rack_id_to' => 0,
							'qty' => $det->qty,
							'racking' => $det->racking,
							'is_active' => 1
						));
					} else {
						// select bundle, insert item
						$bundle = new Bundle();
						$bundles = $bundle->getBundleItem($det->item_id);
						if($bundles){
							foreach($bundles as $bun){
								$item_id = $bun->item_id_child;
								$qty = $bun->child_qty * $det->qty;
								$transferdetails->create(array(
									'transfer_inventory_id' => $orderlastid,
									'item_id' =>$item_id,
									'rack_id_from' => 0,
									'rack_id_to' => 0,
									'qty' => $qty,
									'racking' => $det->racking,
									'is_active' => 1
								));
							}
						}
					}

				}
			}
			// add sms module
			$myOrder->update(array('is_scheduled' => $schedule_date,'truck_id' => $truck_id,'status'=>4,'helpers' => $helperlist,'driver'=> $drivername), $order_id);
			Log::addLog($user->data()->id,$user->data()->company_id,"Schedule Order ID $order_id","ajax_query2.php");

			echo "Order scheduled successfully.";
		} else {
			echo "Please enter a valid date";
		}
	}
	function reScheduleOrderWh(){
		$order_id = Input::get('order_id');
		$schedule_date = Input::get('schedule_date');
		$re_truck_id = Input::get('re_truck_id');
		$re_driver_id = Input::get('re_driver_id');
		$re_helper_id = Input::get('re_helper_id');
		$re_for_pick_up = Input::get('re_for_pick_up');
		 $re_helper_id = trim($re_helper_id);
		if($re_helper_id){
			$helpers = json_decode($re_helper_id,true);
		}


		$helperlist = "";
		if(is_array($helpers)  && count($helpers)){

			foreach($helpers as $h ){
				$helperlist .= $h . "|";
			}
			$helperlist = rtrim($helperlist,"|");
		}

		if($re_for_pick_up == 1){
			$re_truck_id = 0;
			$re_driver_id = '';
			$helperlist = "";
		}
		$user = new User();
		$myOrder = new Wh_order();
		$scheduleOrder = new Wh_order_date();
		$now = time();
		if($schedule_date){
			$schedule_date = strtotime($schedule_date);
			$scheduleOrder->create(array(
				'company_id' => $user->data()->company_id,
				'user_id' => $user->data()->id,
				'created' =>$now,
				'modified' =>$now,
				'is_active' =>1,
				'schedule_date' =>$schedule_date,
				'wh_order_id' =>$order_id
			));
			$myOrder->update(array('for_pickup' => $re_for_pick_up,'is_scheduled' => $schedule_date,'truck_id'=>$re_truck_id,'driver'=>$re_driver_id,'helpers'=>$helperlist), $order_id);
			Log::addLog($user->data()->id,$user->data()->company_id,"Reschedule Order ID $order_id","ajax_query2.php");
			echo "Order rescheduled successfully.";
		} else {
			echo "Please enter a valid date";
		}
	}
	function getWhOrderDates(){
		$order_id = Input::get('order_id');
		$order_status = Input::get('order_status');
		$myOrder = new Wh_order($order_id);
		$user = new User();
		$odDates = new Wh_order_date();
		$dates = $odDates->getDates($order_id);
		if($dates){
			$arr = [];
			$isFirst = true;
			foreach($dates as $dt){
				$dt->fullname = ucwords($dt->lastname. ", " .$dt->firstname . " " .$dt->middlename);
				$dt->sched_date = date('m/d/y H:i:s A',$dt->schedule_date);

				$dt->isFirst = $isFirst;
				$isFirst = false;
				$arr[] = $dt;
			}
			echo json_encode($arr);
		}
	}

	function getWhOrderLog(){
		$user = new User();
		$whorder = new Wh_order();
		$user_id = 0;
		$member_id = 0;
		$from = Input::get('from');
		$to = Input::get('to');
		$search = Input::get('search');
		$truck_id = Input::get('truck_id');
		$order_type = Input::get('order_type');

		if($user->hasPermission('wh_agent')){
			$user_id = $user->data()->id;
		}
		if($user->hasPermission('wh_member')){
			$member_id = $user->data()->member_id;
		}

		$branch_id = 0;
		if(!$user->hasPermission('inventory_all')){
			$branch_id = $user->data()->branch_id;
		}

		//$orders_count = $whorder->countOrdersLog($user->data()->company_id,$user_id,$member_id,$from,$to,$order_type);

		$orders = $whorder->getOrdersLog($user->data()->company_id,$user_id,$member_id,$from,$to,$order_type,$search,$branch_id,$truck_id);


		$arr = [];
		if($orders){
			$now = time();
			foreach($orders as $order)
			{
				if($order->mln){
				$order->fullname = ucwords($order->mln . ", " . $order->mfn);
				} else {
				$order->fullname = $order->branch_name_to;
				}

				$order->fullnameUser = ucwords($order->lastname . ", " . $order->firstname);
				$order->ordered_date = date('F d, Y',$order->created);
				$order->total= number_format($order->total_price,2,'.','');
				$order->total_price = number_format($order->total_price,2);

				if($order->truck_name){
					$order->truck = $order->truck_name . "<small class='text-danger' style='display:block'>".$order->truck_description."</small>";
				} else {
					$order->truck = "<i class='fa fa-ban'></i>";
				}
				if($order->shipping_name){
					$order->shipping_name = "VIA: ".$order->shipping_name;
				} else {
					$order->shipping_name = "";
				}
				if($order->client_po){
					$order->client_po = "PO#: ".$order->client_po;
				} else {
					$order->client_po = "";
				}
				$helperret = "<i class='fa fa-ban'></i>";
					$helperval = "";
				if($order->helpers){
					$helperret='';

					if(strpos($order->helpers,'|') > 0){
						$exhel = explode('|',$order->helpers);
						foreach($exhel as $helpind){
						$helperret .=" <span class='label label-primary'>$helpind</span>";
						$helperval .= "$helpind,";
						}
						$helperval = rtrim($helperval,',');
					} else {
						$helperret.=" <span class='label label-primary'>$order->helpers</span>";
						$helperval =$order->helpers;
					}
				}
				$order->helpers = $helperret;
				$order->helperval = $helperval;

				if($order->driver){
					$order->driverval = $order->driver;
					$order->driver = "<span class='label label-primary'>$order->driver</span>";

				} else {
					$order->driver = "<i class='fa fa-ban'></i>";
					$order->driverval = "";
				}
				if($order->is_scheduled)
				{
					$ordersched = $order->is_scheduled;
					$diff = $now - $ordersched;
					$rechedule_allowed = Configuration::getValue('reschedule_order');
					if($rechedule_allowed && is_numeric($rechedule_allowed)){
						$rechedule_allowed = 86400 * $rechedule_allowed;
					} else {
						$rechedule_allowed = 86400 * 3; // 3 days default
					}
					if($diff >= $rechedule_allowed ) //ten days
					{
						$order->canBeResched = 0;
					} else {
						$order->canBeResched = 1;
					}
					$order->is_scheduled = date('F d, Y',$order->is_scheduled);
					if($order->is_scheduled == date('F d, Y')){
					$order->is_current = 1;
					} else {
					$order->is_current = 0;
					}
				}
				$order->pickup_status =$order->for_pickup;
				if($order->for_pickup){
					$order->for_pickup = 'For pickup';
				} else {
					$order->for_pickup = '';
				}

				$pref_payment = '';
				if(isset($order->pref_payment) && $order->pref_payment == 1){
					$pref_payment = "COD";
				} else if (isset($order->pref_payment) && $order->pref_payment == 2){
					$pref_payment = "BT";
				}

				$order->pref_payment = $pref_payment;

				if($order->approved_date){
					$order->approved_date = date('F d, Y H:i:s A',$order->approved_date);
				} else {
					$order->approved_date='N/A';
				}

				$arr[] = $order;

			}
		}

		echo json_encode($arr);
	}
	function getWhOrderPickup(){
		$user = new User();
		$whorder = new Wh_order();
		$user_id = 0;
		$member_id = 0;
		$from = Input::get('from');
		$to = Input::get('to');
		$search = Input::get('search');
		$pickup_filter_type = Input::get('pickup_filter_type');

		if($user->hasPermission('wh_agent')){
			$user_id = $user->data()->id;
		}
		if($user->hasPermission('wh_member')){
			$member_id = $user->data()->member_id;
		}
		$branch_id = 0;
		if(!$user->hasPermission('inventory_all')){
			$branch_id = $user->data()->branch_id;
		}
		$orders = $whorder->getOrdersPickup($user->data()->company_id,$user_id,$member_id,$from,$to,$pickup_filter_type,$search,$branch_id);

		$arr = [];
		if($orders){
			$now = time();
			foreach($orders as $order)
			{
				if($order->mln){
				$order->fullname = ucwords($order->mln . ", " . $order->mfn);
				} else {
				$order->fullname = $order->branch_name_to;
				}

				$order->fullnameUser = ucwords($order->lastname . ", " . $order->firstname);
				$order->ordered_date = date('F d, Y',$order->created);
				$order->total= number_format($order->total_price,2,'.','');
				$order->total_price = number_format($order->total_price,2);

				if($order->truck_name){
					$order->truck = $order->truck_name . "<small class='text-danger' style='display:block'>".$order->truck_description."</small>";
				} else {
					$order->truck = "<i class='fa fa-ban'></i>";
				}
				if($order->shipping_name){
					$order->shipping_name = "VIA: ".$order->shipping_name;
				} else {
					$order->shipping_name = "";
				}
				if($order->client_po){
					$order->client_po = "PO#: ".$order->client_po;
				} else {
					$order->client_po = "";
				}
				$helperret = "<i class='fa fa-ban'></i>";
					$helperval = "";
				if($order->helpers){
					$helperret='';

					if(strpos($order->helpers,'|') > 0){
						$exhel = explode('|',$order->helpers);
						foreach($exhel as $helpind){
						$helperret .=" <span class='label label-primary'>$helpind</span>";
						$helperval .= "$helpind,";
						}
						$helperval = rtrim($helperval,',');
					} else {
						$helperret.=" <span class='label label-primary'>$order->helpers</span>";
						$helperval =$order->helpers;
					}
				}
				$order->helpers = $helperret;
				$order->helperval = $helperval;

				if($order->driver){
					$order->driverval = $order->driver;
					$order->driver = "<span class='label label-primary'>$order->driver</span>";

				} else {
				$order->driver = "<i class='fa fa-ban'></i>";
					$order->driverval = "";
				}
				if($order->is_scheduled)
				{
					$ordersched = $order->is_scheduled;
					$diff = $now - $ordersched;
					$rechedule_allowed = Configuration::getValue('reschedule_order');
					if($rechedule_allowed && is_numeric($rechedule_allowed)){
						$rechedule_allowed = 86400 * $rechedule_allowed;
					} else {
						$rechedule_allowed = 86400 * 3; // 3 days default
					}
					if($diff >= $rechedule_allowed ) //ten days
					{
						$order->canBeResched = 0;
					} else {
						$order->canBeResched = 1;
					}
					$order->is_scheduled = date('F d, Y',$order->is_scheduled);
					if($order->is_scheduled == date('F d, Y')){
					$order->is_current = 1;
					} else {
					$order->is_current = 0;
					}
				}
				$order->pickup_status =$order->for_pickup;
				if($order->for_pickup){
					$order->for_pickup = 'For pickup';
				} else {
					$order->for_pickup = '';
				}

				if($order->approved_date){
					$order->approved_date = date('F d, Y H:i:s A',$order->approved_date);
				} else {
					$order->approved_date='N/A';
				}

				$arr[] = $order;

			}
		}

		echo json_encode($arr);
	}
	function getWhOrderService(){
		$user = new User();
		$whorder = new Wh_order();
		$user_id = 0;
		$member_id = 0;
		$from = Input::get('from');
		$to = Input::get('to');
		$search = Input::get('search');
		$pickup_filter_type = Input::get('pickup_filter_type');

		if($user->hasPermission('wh_agent')){
			$user_id = $user->data()->id;
		}
		if($user->hasPermission('wh_member')){
			$member_id = $user->data()->member_id;
		}
		$branch_id = 0;
		if(!$user->hasPermission('inventory_all')){
			$branch_id = $user->data()->branch_id;
		}
		$orders = $whorder->getOrdersService($user->data()->company_id,$user_id,$member_id,$from,$to,$pickup_filter_type,$search,$branch_id);

		$arr = [];
		if($orders){
			$now = time();
			foreach($orders as $order)
			{
				if($order->mln){
				$order->fullname = ucwords($order->mln . ", " . $order->mfn);
				} else {
				$order->fullname = $order->branch_name_to;
				}

				$order->fullnameUser = ucwords($order->lastname . ", " . $order->firstname);
				$order->ordered_date = date('F d, Y',$order->created);
				$order->total= number_format($order->total_price,2,'.','');
				$order->total_price = number_format($order->total_price,2);

				if($order->truck_name){
					$order->truck = $order->truck_name . "<small class='text-danger' style='display:block'>".$order->truck_description."</small>";
				} else {
					$order->truck = "<i class='fa fa-ban'></i>";
				}
				if($order->shipping_name){
					$order->shipping_name = "VIA: ".$order->shipping_name;
				} else {
					$order->shipping_name = "";
				}
				if($order->client_po){
					$order->client_po = "PO#: ".$order->client_po;
				} else {
					$order->client_po = "";
				}
				$helperret = "<i class='fa fa-ban'></i>";
					$helperval = "";
				if($order->helpers){
					$helperret='';

					if(strpos($order->helpers,'|') > 0){
						$exhel = explode('|',$order->helpers);
						foreach($exhel as $helpind){
						$helperret .=" <span class='label label-primary'>$helpind</span>";
						$helperval .= "$helpind,";
						}
						$helperval = rtrim($helperval,',');
					} else {
						$helperret.=" <span class='label label-primary'>$order->helpers</span>";
						$helperval =$order->helpers;
					}
				}
				$order->helpers = $helperret;
				$order->helperval = $helperval;

				if($order->driver){
					$order->driverval = $order->driver;
					$order->driver = "<span class='label label-primary'>$order->driver</span>";

				} else {
				$order->driver = "<i class='fa fa-ban'></i>";
					$order->driverval = "";
				}
				if($order->is_scheduled)
				{
					$ordersched = $order->is_scheduled;
					$diff = $now - $ordersched;
					$rechedule_allowed = Configuration::getValue('reschedule_order');
					if($rechedule_allowed && is_numeric($rechedule_allowed)){
						$rechedule_allowed = 86400 * $rechedule_allowed;
					} else {
						$rechedule_allowed = 86400 * 3; // 3 days default
					}
					if($diff >= $rechedule_allowed ) //ten days
					{
						$order->canBeResched = 0;
					} else {
						$order->canBeResched = 1;
					}
					$order->is_scheduled = date('F d, Y',$order->is_scheduled);
					if($order->is_scheduled == date('F d, Y')){
					$order->is_current = 1;
					} else {
					$order->is_current = 0;
					}
				}
				$order->pickup_status =$order->for_pickup;
				if($order->for_pickup){
					$order->for_pickup = 'For pickup';
				} else {
					$order->for_pickup = '';
				}

				if($order->approved_date){
					$order->approved_date = date('F d, Y H:i:s A',$order->approved_date);
				} else {
					$order->approved_date='N/A';
				}

				$arr[] = $order;

			}
		}

		echo json_encode($arr);
	}
	function processToShipping(){
		$order_id = Input::get('order_id');
		$myOrder = new Wh_order($order_id);
		if($myOrder->data()->for_pickup == 1 && $myOrder->data()->member_id != 0 ){
			$new_status = 2;

		} else if($myOrder->data()->for_pickup == 2){
			$new_status = 4;
		} else if ($myOrder->data()->truck_id){
			$new_status = 4;
		}else {
			$new_status = 2;
		}

		if($myOrder->data()->stock_out == 1){
			$myOrder->update(array('status' => $new_status),$order_id); // tochange
			$user = new User();

			Log::addLog($user->data()->id,$user->data()->company_id,"Process Order ID $order_id","ajax_query2.php");


			echo "Request processed successfully.";
		}
	}
	function getStockWarehouse(){
		$order_id = Input::get('order_id');
		$myOrder = new Wh_order($order_id);
		$user = new User();

		if($myOrder->data()->stock_out == 0 && $myOrder->data()->status == 3){
			$whorder = new Wh_order_details();
			$orders = $whorder->getOrderDetails($order_id);
			$arr = [];
			$has_ins = false;
			$bundleupdate = [];
			$rack_tags = new Rack_tag();
			$tags_ex = $rack_tags->get_tags_ex('wh_orders',$user->data()->company_id,$myOrder->data()->branch_id);

			if(isset($tags_ex->id) && !empty($tags_ex->id)){
				$excempt_tags = $tags_ex->tag_id;
			} else {
				$excempt_tags =0;
			}

			$specific_rack = Configuration::getSpecificRack();
			$specific_rack_id = 0;
			if(count($specific_rack) && isset($specific_rack[$myOrder->data()->branch_id])){
				$specific_rack_id = $specific_rack[$myOrder->data()->branch_id];
			}

			$rack = new Rack();
			$rack_defaults = $rack->getRackDefaults($myOrder->data()->branch_id);
			if(Configuration::getValue('surplus_rack') == 1){
				$surplus_rack_id = (isset($rack_defaults->surplus_rack)) ? $rack_defaults->surplus_rack : 0;
			} else {
				$surplus_rack_id = 0;
			}

			foreach($orders as $order){

				if($order->is_bundle == 0){
					if($order->item_type == -1){

						$item_for_order_cls = new Assemble_item_for_order();
						$item_for_order = $item_for_order_cls->getItem($order->item_id);
						$surplus_id = (Configuration::getValue('surplus_rack') == 1) ? $surplus_rack_id : 0;
						$spec_rack_id = $specific_rack_id;

						if($order->is_surplus){
							$surplus_id = 0;
							$spec_rack_id = $surplus_rack_id;
							$item_racking = inventory_racking_spareparts($order->qty,$order->item_id,$myOrder->data()->branch_id,4);
						} else {
							if(isset($item_for_order->item_id) && $item_for_order->item_id){
								$item_racking = inventory_racking($order_id,$order->qty,$order->item_id,$myOrder->data()->branch_id,false,0,$spec_rack_id,$surplus_id);
							} else {
								$item_racking = inventory_racking($order_id,$order->qty,$order->item_id,$myOrder->data()->branch_id,false,$excempt_tags,$spec_rack_id,$surplus_id);
							}
						}


						$order->racking = $item_racking['racking'];
						if(!$has_ins){
							$has_ins = $item_racking['insufficient'];
						}
						$total = $order->qty * $order->adjusted_price;
						$order->total = number_format($total,2);

					} else {
						$order->racking  = '[]';
					}
					$arr[] = $order;


				} else {

					$bundle = new Bundle();
					$bundle_list = $bundle->getBundleItem($order->item_id);
					$allracking = [];
					foreach($bundle_list as $bl){
					$item_for_order_cls = new Assemble_item_for_order();
						$item_for_order = $item_for_order_cls->getItem($bl->item_id_child);
						$surplus_id = (Configuration::getValue('surplus_rack') == 1) ? $surplus_rack_id : 0;
						 $spec_rack_id = $specific_rack_id;

						if($order->is_surplus){
							$surplus_id = 0;
							 $spec_rack_id = $surplus_rack_id;
						}

						if(isset($item_for_order->item_id) && $item_for_order->item_id){
							$item_racking = inventory_racking($order_id,$order->qty * $bl->child_qty,$bl->item_id_child,$myOrder->data()->branch_id,false,0,$spec_rack_id,$surplus_id);
						} else {
							$item_racking = inventory_racking($order_id,$order->qty * $bl->child_qty,$bl->item_id_child,$myOrder->data()->branch_id,false,$excempt_tags,$spec_rack_id,$surplus_id);
						}


						$bunqty =  $order->qty * $bl->child_qty;
						$neworder = (object) ["qty" =>$bunqty, "id" => $order->id, "item_id" => $bl->item_id_child,"racking" => $item_racking['racking'],"is_bundle" => 1];
						$allracking[$bl->item_id_child] = $item_racking["racking"];

						if(!$has_ins){
							$has_ins = $item_racking['insufficient'];
						}
						$arr[] = $neworder;
					}
					$bundleupdate[$order->id] = $allracking;
				}

			}
			// check if strict

			if(Configuration::getValue('inv_strict') == 1){
				$has_ins = false;
			}
			if(!$has_ins){
				$inv_mon = new Inventory_monitoring();
				$inventory = new Inventory();
				foreach($arr as $item){
					$racking = json_decode($item->racking);
					if($racking){
						if(!$item->is_surplus){
							foreach($racking as $rack){
								// check if item exists in rack
								if($inventory->checkIfItemExist($item->item_id,$myOrder->data()->branch_id,$user->data()->company_id,$rack->rack_id)){
									$curinventoryFrom = $inventory->getQty($item->item_id,$myOrder->data()->branch_id,$rack->rack_id);
									$currentqty = $curinventoryFrom->qty;
									$inventory->subtractInventory($item->item_id,$myOrder->data()->branch_id,$rack->qty,$rack->rack_id);
								} else {
									$currentqty = 0;
								}
								// monitoring
								$newqtyFrom = $currentqty - $rack->qty;
								$inv_mon->create(array(
									'item_id' => $item->item_id,
									'rack_id' => $rack->rack_id,
									'branch_id' => $myOrder->data()->branch_id,
									'page' => 'ajax/ajax_query2.php',
									'action' => 'Update',
									'prev_qty' => $currentqty,
									'qty_di' => 2,
									'qty' => $rack->qty,
									'new_qty' => $newqtyFrom,
									'created' => time(),
									'user_id' => $user->data()->id,
									'remarks' => 'Deduct inventory from rack (Order id #'.$order_id.')',
									'is_active' => 1,
									'company_id' => $user->data()->company_id
								));
							}
						} else {
							$inv_issues = new Inventory_issue();
							$inv_mon_issues = new Inventory_issues_monitoring();
							foreach($racking as $rack){

							if($inv_issues->checkIfItemExist($item->item_id,$myOrder->data()->branch_id,$user->data()->company_id,$rack->rack_id,4)){
								$curinventoryFrom = $inv_issues->getQty($item->item_id,$myOrder->data()->branch_id,$rack->rack_id,4);
								$currentqty = $curinventoryFrom->qty;
								$inv_issues->subtractInventory($item->item_id,$myOrder->data()->branch_id,$rack->qty,$rack->rack_id,4);
							} else {
								$currentqty = 0;
							}

							$new_issues = $currentqty - $rack->qty;
							$inv_mon_issues->create(array(
								'item_id' => $item->item_id,
								'rack_id' => $rack->rack_id,
								'branch_id' =>$myOrder->data()->branch_id,
								'page' => 'ajax/ajax_query.php',
								'action' => 'Update',
								'prev_qty' => $currentqty,
								'qty_di' => 2,
								'qty' => $rack->qty,
								'new_qty' => $new_issues,
								'created' => time(),
								'user_id' => $user->data()->id,
								'remarks' => 'Deduct inventory from rack (Order id #'.$order_id.')',
								'is_active' => 1,
								'company_id' => $user->data()->company_id,
								'type' => 4
							));
						}
						}

					}

					if($item->is_bundle == 1){
						$whorder->update(array('racking'=> json_encode($bundleupdate[$item->id])),$item->id);
					} else {
						$whorder->update(array('racking'=> $item->racking),$item->id);
					}

				}
				$myOrder->update(array('stock_out' => 1),$order_id);
				echo "Request processed successfully.";
			} else {
				echo "Some items dont have stock(s).";
			}
		} else {
			 echo "This request has been changed. Please refresh the page.";
		}
	}
	function getInventoryOfItem(){
		$id = Input::get('item_id');
		$invetories = new Inventory();
		$user = new User();
		$allinv = $invetories->getInventoryOfCompany($id,$user->data()->company_id);
		if($allinv){
			echo "<table class='table table-bordered'>";
			echo "<thead><tr><th>Branch</th><th>Rack</th><th>Qty</th></tr></thead>";
			echo "<tbody>";
			foreach($allinv as $in){
				echo "<tr><td>$in->bname</td><td>$in->rack</td><td> " . formatQuantity($in->qty) . "</td></tr>";
			}
			echo "</tbody>";
			echo "</table>";
		} else {
			echo "<p>No Inventory</p>";
		}


	}
	function updateSparePart(){
		$id =  Encryption::encrypt_decrypt('decrypt',Input::get('id'));
		$qty = Input::get('qty');
		if(is_numeric($id) && is_numeric($qty)){
			$edit = new Composite_item();
			$edit->update(array('qty' => $qty),$id);
			echo "Updated successfully";
		} else {
			echo "Invalid data";
		}
	}
	function getAssembleList(){
		$status = Input::get('status');
		$branch_id = Input::get('branch_id');
		$dt_from= Input::get('dt_from');
		$dt_to= Input::get('dt_to');
		$is_dl= Input::get('is_dl');
		$border = "";
		if($is_dl == 1){
			$filename = "assemble-list-" . date('m-d-Y-H-i-s-A') . ".xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");
			$border = "border=1";
		}
		$user = new User();
		$assemble_cls = new Assemble_request();
		$items = $assemble_cls->getAssembleRequest($user->data()->company_id,$status,$branch_id,$dt_from,$dt_to);

		  if($items){
			?>
			<div id="no-more-tables">
			<table class="table" <?php echo $border; ?>>
				<thead>
				<tr>
					<th>Id</th>
					<th>Branch</th>
					<th>Status</th>
					<th>User</th>
					<th>Date Created</th>
					<th>Remarks</th>
					<th>ORDER ID</th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php
					$arrType = ['',Configuration::getValue('a_step1'), Configuration::getValue('a_step2'),Configuration::getValue('a_step3'),'Cancelled'];
					foreach($items as $item){
						$rem = 'No remarks';
						if($item->remarks) $rem = $item->remarks;
						$orderid = "N/A";
						if($item->wh_id) $orderid = "ORDER ID# ".$item->wh_id;
						?>
						<tr>
							<td style='border-top:1px solid #ccc;' data-title='Id'><?php echo escape($item->id); ?></td>
							<td style='border-top:1px solid #ccc;' data-title='Branch'><?php echo escape($item->branch_name); ?></td>
							<td style='border-top:1px solid #ccc;' data-title='Status'><?php echo escape($arrType[$item->status]); ?></td>
							<td style='border-top:1px solid #ccc;' data-title='Name'><?php echo escape($item->uln . ", " . $item->ufn . " " . $item->umn); ?></td>
							<td style='border-top:1px solid #ccc;' data-title='Created'><?php echo date('F d, Y',$item->created); ?></td>
							<td style='border-top:1px solid #ccc;' data-title='Remarks'><?php echo escape($rem); ?></td>
							<td style='border-top:1px solid #ccc;' data-title='Order #'><?php echo escape($orderid); ?></td>
							<td style='border-top:1px solid #ccc;'><button data-id='<?php echo Encryption::encrypt_decrypt('encrypt',$item->id)?>' class='btn btn-primary btnDetails'>Details</button></td>
						</tr>
						<?php
					}
				?>
				</tbody>
			</table>
			</div>
			<?php
		} else {
			?>
			<div class="alert alert-info">No record yet.</div>
			<?php
		}
	}
	function getDisassembleList(){
		$status = Input::get('status');
		$user = new User();
		$disassemble_cls = new Disassemble_request();
		$items = $disassemble_cls->getDisassembleRequest($user->data()->company_id,$status);

		if($items){
			?>
			<div id="no-more-tables">
			<table class="table">
				<thead>
				<tr>
					<th>Id</th>
					<th>Branch</th>
					<th>Status</th>
					<th>User</th>
					<th>Date Created</th>
					<th>Remarks</th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php
					$arr_type = ['','Pending','Processed','Cancelled'];
					foreach($items as $item){
						$rem = 'No remarks';
						if($item->remarks) $rem = $item->remarks;
						?>
						<tr>
							<td data-title='Id'><?php echo escape($item->id); ?></td>
							<td data-title='Branch'><?php echo escape($item->branch_name); ?></td>
							<td data-title='Status'><?php echo escape($arr_type[$item->status]); ?></td>
							<td data-title='User'><?php echo escape($item->uln . ", " . $item->ufn . " " . $item->umn); ?></td>
							<td data-title='Created'><?php echo date('F d, Y',$item->created); ?></td>
							<td data-title='Remarks'><?php echo escape($rem); ?></td>
							<td>
								<button data-id='<?php echo Encryption::encrypt_decrypt('encrypt',$item->id)?>' class='btn btn-primary btnDetails'>Details</button>
							</td>
						</tr>
						<?php
					}
				?>
				</tbody>
			</table>
			</div>
			<?php
		} else {
			?>
			<div class="alert alert-info">No record yet.</div>
			<?php
		}
	}
	function getAssembleDetails(){

		$id = Encryption::encrypt_decrypt('decrypt',Input::get('id'));
		$b_name = Input::get('b_name');
		$user = new User();

		if(is_numeric($id)){
			$finalarr = [];
			//finalarr.push({stock_man:rackjson[rj].stock_man,rack:rackjson[rj].rack,qty:rackjson[rj].qty,item_code:bundledet[j].item_code,description:bundledet[j].description});

			$req = new Assemble_request($id);
			$payment_id = 0;
			if($req->data()->wh_id && is_numeric($req->data()->wh_id) ){
				$wh = new Wh_order($req->data()->wh_id);
				$payment_id = $wh->data()->payment_id;
			}
			if($req->data()->status == 3){
				$timeDiff = 0;
			} else {
				$timeDiff = time() -  $req->data()->modified ;
			}

			$cls= new Assemble_details();
			$details = $cls->getDetails($id);
			$rack = new Rack();
			//$print_data = $req->getDataPrint($id);
			$rack_list = $rack->get_active('racks',array('branch_id','=',$user->data()->branch_id));
			$optlist = "<select class='form-control'>";
			if($rack_list){
				foreach($rack_list as $rl){
					$optlist .= "<option value='$rl->id'>$rl->rack</option>";
				}
			}
			$optlist .= "</select>";
			$reqSTATUS = $req->data()->status;
			if($details){
				echo "<h4>REQUEST ID: #{$id}</h4>";
				echo "<input type='hidden' value='$timeDiff' id='timeDiff'>";
				echo "<div id='no-more-tables'>";
				echo "<p  class='text-danger' id='timeCtr'></p>";
				if(!$req->data()->wh_id){
					echo "<div class='row'>";
					echo "<div class='col-md-3'>";
					echo "<div class='form-group'><input type='text' class='form-control' id='wh_id_number' placeholder='Order Id Number'></div>";
					echo "</div>";
					echo "<div class='col-md-3'>";
					echo "<div class='form-group'><button data-id='".$req->data()->id."' class='btn btn-default' id='saveWhOrder'>Save Order Id</button></div>";
					echo "</div>";
					echo "</div>";
				}

				echo "<table id='tblDetails' class='table'>";
				echo "<thead>";
				echo "<tr><th>Item</th><th>Description</th><th>Quantity</th>";

				if($reqSTATUS == 2){
					echo "<th>Rack</th>";
					 echo "<th>Output</th>";
				}


				echo "<th>Item needed and racking</th></tr>";
				echo "</thead>";
				echo "<tbody>";
				$machine_list = "";
				foreach($details as $det){
					$racking = json_decode($det->racking);
					$racklbl = "";
					if($racking){
						$racklbl = "<table class='table'>";
						foreach($racking as $rack){
							$sparetype = new Spare_type();
							$lst = json_decode($rack->racking);
							$parts_needed = $rack->raw;
							$sptypeItem = $sparetype->getType($parts_needed->id);
							$sptypename ='';
							if(isset($sptypeItem->name) && $sptypeItem->name){
								$sptypename = $sptypeItem->name;
							}

							$spitem_code='';
							if(isset($parts_needed->item_code) && $parts_needed->item_code){
								$spitem_code = $parts_needed->item_code;
							}

							$rack_tbl = "<table class='table'>";
							foreach($lst as $l){
								$rack_tbl .= "<tr><td></td><td>$l->rack</td><td>" . formatQuantity($l->qty) . "</td></tr>";

								$finalarr[]= ['stock_man' =>$l->stock_man,'rack' =>$l->rack,'qty' =>formatQuantity($l->qty),'item_code' => $spitem_code,'description' => $parts_needed->desc ];
							}
								$rack_tbl .="</table>";

							$racklbl .="<tr><td style='border-bottom:1px solid #ccc;'><strong>$spitem_code</strong> <small class='span-block'>$parts_needed->desc - <strong class='text-danger'>".formatQuantity($parts_needed->need_total)."</strong></small><small class='text-danger span-block'>".$sptypename."</small></td><td style='border-bottom:1px solid #ccc;'>$rack_tbl</td></tr>";
						}
						$racklbl .= "</table>";

					}

					$btnAssemble = '';
					if($det->has_serial && $payment_id){
						$btnAssemble = "<button  data-item_id='$det->item_id_set' data-payment_id='$payment_id' data-qty='$det->qty' class='btn btn-default btnAssembleItem'> <i class='fa fa-list'></i></button>";
					}else if($det->has_serial && !$payment_id){
					$btnAssemble = "<button  data-item_id='$det->item_id_set' data-details_id='$det->id' data-qty='$det->qty' class='btn btn-default btnAssembleItemNoPaymentID'> <i class='fa fa-list'></i></button>";
					}
								$machine_list .= $det->description .", ";
					echo "<tr
							data-det_id='".Encryption::encrypt_decrypt('encrypt',$det->id) ."'
							data-item_id='".Encryption::encrypt_decrypt('encrypt',$det->item_id_set)."'
							data-qty='".Encryption::encrypt_decrypt('encrypt',$det->qty)."'
							>
							<td data-title='Item'>$btnAssemble $det->item_code</td>
							<td data-title='Description'>$det->description</td>
							<td data-title='Qty'>"  . formatQuantity($det->qty) ."</td>";
					if($reqSTATUS == 2){
						echo "<td>$optlist</td>";
						echo "<td><input type='text' class='form-control txt-qty' value='".formatQuantity($det->qty,true)."'></td>";
					}
					echo "<td data-title='Rack'>$racklbl</td>";
					echo "</tr>";
				}
				echo "</tbody>";
				echo "</table>";
				echo "</div>";
					$machine_list = rtrim($machine_list,", ");
				if($reqSTATUS == 2  && $req->data()->branch_id == $user->data()->branch_id){
					$wh_id = ($req->data()->wh_id) ? $req->data()->wh_id : '';
					echo "<div class='text-right'>";
					echo "<button id='btnConvert' data-id='".Encryption::encrypt_decrypt('encrypt',$id)."'class='btn btn-default'>Process</button>";
					echo "<button id='btnPrintRacks' data-wh_id='". $wh_id."'  data-machine='".$machine_list."' data-b_name='".$b_name."' data-print_id='".$id."' data-id='".Encryption::encrypt_decrypt('encrypt',$id)."'class='btn btn-default'>Print</button>";
					echo "<button id='btnCancel' data-id='".Encryption::encrypt_decrypt('encrypt',$id)."'class='btn btn-default'>Cancel</button>";
					echo "</div>";
				}
				if($reqSTATUS == 1 && $req->data()->branch_id == $user->data()->branch_id){
					echo "<div class='text-right'>";
					echo "<button id='btnPrepare' data-id='".Encryption::encrypt_decrypt('encrypt',$id)."'class='btn btn-default'>Prepare</button>";
					echo " <button id='btnCancel' data-id='".Encryption::encrypt_decrypt('encrypt',$id)."'class='btn btn-default'>Cancel</button>";
					echo "</div>";
				}
				if($reqSTATUS == 3  && $req->data()->branch_id == $user->data()->branch_id){
				echo "<button id='btnPrintRacks' data-machine='".$machine_list."' data-b_name='".$b_name."' data-print_id='".$id."' data-id='".Encryption::encrypt_decrypt('encrypt',$id)."'class='btn btn-default'>Print</button>";

				}


				echo "<input type='hidden'  id='hid_rack_location' value='".json_encode($finalarr)."'>";

			} else {
				echo "No record found.";
			}
		}
	}
	function disassembleListCancel(){
		$id = Input::get('id');
		$id = (int) Encryption::encrypt_decrypt('decrypt',$id);

		if($id){
			$user = new User();
			$disassemble_details = new Disassemble_details();
			$details = $disassemble_details->getDetails($id);
			if(count($details)){
				$inventory = new Inventory();
				$inv_mon = new Inventory_monitoring();
				foreach($details as $det){
					$item_id = $det->item_id_set;
					$racking  = json_decode($det->set_racking);
					foreach($racking as $rack){
						$rack_id = $rack->rack_id;
						$qty = $rack->qty;
						if($inventory->checkIfItemExist($item_id,$user->data()->branch_id,$user->data()->company_id,$rack_id)){
							$curinventory = $inventory->getQty($item_id,$user->data()->branch_id,$rack_id);
							$inventory->addInventory($item_id,$user->data()->branch_id,$qty,false,$rack_id);
							// monitoring

							$newqty = $curinventory->qty + $qty;
							$inv_mon->create(array(
								'item_id' => $item_id,
								'rack_id' => $rack_id,
								'branch_id' => $user->data()->branch_id,
								'page' => 'ajax/ajax_query2',
								'action' => 'Update',
								'prev_qty' => $curinventory->qty,
								'qty_di' => 1,
								'qty' => $qty,
								'new_qty' => $newqty,
								'created' => time(),
								'user_id' => $user->data()->id,
								'remarks' => 'Cancel disassemble item list. Request #' . $id,
								'is_active' => 1,
								'company_id' => $user->data()->company_id
							));
							}
					}
				}
			}
			$disassemble_request = new Disassemble_request();
			$disassemble_request->update(array('status' => 4),$id);
			echo "Request cancelled successfully";
		}
	}
	function assembleListCancel(){
		$id = Input::get('id');
		$id = (int) Encryption::encrypt_decrypt('decrypt',$id);

			if($id){
					$user = new User();
					$assemble_details = new Assemble_details();
					$details = $assemble_details->getDetails($id);
					if(count($details)){
						$inventory = new Inventory();
						$inv_mon = new Inventory_monitoring();
						foreach($details as $det){
							$racking = json_decode($det->racking);

							foreach($racking as $rack){
								$rk = json_decode($rack->racking);
								$item_id = $rack->raw->id;


								foreach($rk as $r){

									 $rack_id = $r->rack_id;
									 $qty = $r->qty;
									if($inventory->checkIfItemExist($item_id,$user->data()->branch_id,$user->data()->company_id,$rack_id)){
											$curinventory = $inventory->getQty($item_id,$user->data()->branch_id,$rack_id);
											$inventory->addInventory($item_id,$user->data()->branch_id,$qty,false,$rack_id);
											// monitoring

											$newqty = $curinventory->qty + $qty;
											$inv_mon->create(array(
												'item_id' => $item_id,
												'rack_id' => $rack_id,
												'branch_id' => $user->data()->branch_id,
												'page' => 'ajax/ajax_query2',
												'action' => 'Update',
												'prev_qty' => $curinventory->qty,
												'qty_di' => 1,
												'qty' => $qty,
												'new_qty' => $newqty,
												'created' => time(),
												'user_id' => $user->data()->id,
												'remarks' => 'Cancel assemble item list. Request #' . $id,
												'is_active' => 1,
												'company_id' => $user->data()->company_id
											));

								}
							}

						}
					}

			}
				$assembleList = new Assemble_request();
				$assembleList->update(array('status' => '4'),$id);
				echo "Processed successfully";
		}
	}
	function getDisassembleDetails(){
		$id = Encryption::encrypt_decrypt('decrypt',Input::get('id'));
		$user = new User();

		if(is_numeric($id)){
			$req = new Disassemble_request($id);
			$cls= new Disassemble_details();
			$details = $cls->getDetails($id);
			$rack = new Rack();
			$rack_list = $rack->get_active('racks',array('branch_id','=',$req->data()->branch_id));
			$optlist = "<select class='form-control rack_class'>";
			if($rack_list){
				foreach($rack_list as $rl){
					$optlist .= "<option value='$rl->id'>$rl->rack</option>";
				}
			}
			$optlist .= "</select>";
			if($details){
				echo "<h3>ID: $id</h3>";
				echo "<table id='tblDetails' class='table'>";
				echo "<thead>";
				echo "<tr><th>Item</th><th>Description</th><th>Quantity</th><th>Rack</th><th>Parts disassembled</th></tr>";
				echo "</thead>";
				echo "<tbody>";


				foreach($details as $det){
					$racking = json_decode($det->racking);
					$set_racking = $det->set_racking;
					$set_racking = json_decode($set_racking);
					$setlbl = "";
					$racklbl = "";
					if($set_racking){
						$setlbl .= "<table class='table'>";
						foreach($set_racking as $setrack){
							$setlbl .= "<tr><td>$setrack->rack</td><td>$setrack->qty</td></tr>";
						}
						$setlbl .= "</table>";
					}
					if($racking){

						$racklbl = "<table class='table disassembleParts' id='".uniqid()."'>";

						foreach($racking as $rack) {
							$racklbl .= "<tr data-item_id='".$rack->id."' data-qty='".$rack->need_total."'><td>$rack->desc</td><td>$rack->need_total</td><td>$optlist</td></tr>";
						}
						$racklbl .= "</table>";

					}
					echo "<tr data-item_id='".Encryption::encrypt_decrypt('encrypt',$det->item_id_set)."' data-qty='".Encryption::encrypt_decrypt('encrypt',$det->qty)."' ><td>$det->item_code</td><td>$det->description</td><td>$det->qty</td><td>$setlbl</td><td>$racklbl</td></tr>";
				}
				echo "</tbody>";
				echo "</table>";
				if($req->data()->status == 1){
					echo "<div class='text-right'>";
					echo "<button id='btnConvert' data-id='".Encryption::encrypt_decrypt('encrypt',$id)."'class='btn btn-default'>Convert</button>";
					echo " <button id='btnCancel' data-id='".Encryption::encrypt_decrypt('encrypt',$id)."'class='btn btn-default'>Cancel</button>";
					echo "</div>";
				}

			} else {
				echo "No record found.";
			}
		}
	}
	function disassembleSpareparts(){
		$lst = json_decode(Input::get('lst'));
		$id = Encryption::encrypt_decrypt('decrypt',Input::get('id'));
		$user = new User();
		if(is_numeric($id) && count($lst) > 0){
				$inventory = new Inventory();
				$disassemble_cls = new Disassemble_request($id);

				foreach($lst as $item){
					$item_id = $item->item_part;
					$qty =$item->qty;
					$rack_id = $item->rack_id;
					if(($item_id) && is_numeric($qty) && is_numeric($rack_id)){
						// add inventory
						if($inventory->checkIfItemExist($item_id,$disassemble_cls->data()->branch_id,$user->data()->company_id,$rack_id)){
							$curinventory = $inventory->getQty($item_id,$disassemble_cls->data()->branch_id,$rack_id);
							$inventory->addInventory($item_id,$disassemble_cls->data()->branch_id,$qty,false,$rack_id);
							// monitoring
							$inv_mon = new Inventory_monitoring();
							$newqty = $curinventory->qty + $qty;
							$inv_mon->create(array(
								'item_id' => $item_id,
								'rack_id' => $rack_id,
								'branch_id' => $disassemble_cls->data()->branch_id,
								'page' => 'ajax/ajax_query2',
								'action' => 'Update',
								'prev_qty' => $curinventory->qty,
								'qty_di' => 1,
								'qty' => $qty,
								'new_qty' => $newqty,
								'created' => time(),
								'user_id' => $user->data()->id,
								'remarks' => 'Disassemble item for spare inventory. Request #' . $id,
								'is_active' => 1,
								'company_id' => $user->data()->company_id
							));

						} else {
							$curinventory =0;
							$inventory->addInventory($item_id,$disassemble_cls->data()->branch_id,$qty,true,$rack_id);
							// monitoring
							$inv_mon = new Inventory_monitoring();
							$newqty = $curinventory + $qty;
							$inv_mon->create(array(
								'item_id' => $item_id,
								'rack_id' => $rack_id,
								'branch_id' => $disassemble_cls->data()->branch_id,
								'page' => 'ajax/ajax_query2',
								'action' => 'Insert',
								'prev_qty' => $curinventory,
								'qty_di' => 1,
								'qty' => $qty,
								'new_qty' => $newqty,
								'created' => time(),
								'user_id' => $user->data()->id,
								'remarks' => 'Disassemble item for spare inventory. Request #' . $id,
								'is_active' => 1,
								'company_id' => $user->data()->company_id
							));
						}
					}
				}

				$disassemble_cls->update(array('status'=> 2),$id);
				echo "Processed successfully.";

		}

	}

	function convertIssues(){
		$user = new User();
		$rack_id = Input::get('rack_id');
		$branch_id = Input::get('branch_id');
		$des_rack_id = Input::get('des_rack_id');
		$item_id = Input::get('item_id');
		$orig_qty= Input::get('orig_qty');
		$orig_type= Input::get('orig_type');
		$convert_qty= Input::get('convert_qty');
		$convert_type= Input::get('convert_type');
		$inv_issues = new Inventory_issue();
		$inv_mon = new Inventory_issues_monitoring();
		$inventory = new Inventory();
		$inventory_mon = new Inventory_monitoring();

		if($orig_qty >= $convert_qty && $rack_id && $item_id && $convert_type != $orig_type){
			// deduct to issues

			if($inv_issues->checkIfItemExist($item_id,$branch_id,$user->data()->company_id,$rack_id,$orig_type)){
				$curinventoryFrom = $inv_issues->getQty($item_id,$branch_id,$rack_id,$orig_type);
				$currentqty = $curinventoryFrom->qty;
				$inv_issues->subtractInventory($item_id,$branch_id,$convert_qty,$rack_id,$orig_type);
			} else {
				$currentqty = 0;
			}

			if($convert_type == 3){ // disposed
				$inventory_issues_dis = new Inventory_issue_dispose();
				$from_type = '';
				if($orig_type == 0) {
					$from_type = 'Good';
				} else if ($orig_type == 1){
					$from_type = 'Damage';
				} else if ($orig_type == 2){
					$from_type = 'Missing';
				} else if ($orig_type == 4){
					$from_type = 'Incomplete';
				}

				$inventory_issues_dis->create(
					[
					  'item_id' => $item_id,
					  'qty' => $convert_qty,
					  'from_type' => $from_type,
					  'created' => time(),
					]
				);
			}

			$new_issues = $currentqty - $convert_qty;
			$inv_mon->create(array(
				'item_id' => $item_id,
				'rack_id' => $rack_id,
				'branch_id' => $branch_id,
				'page' => 'admin/inventory_adjustments.php',
				'action' => 'Update',
				'prev_qty' => $currentqty,
				'qty_di' => 2,
				'qty' => $convert_qty,
				'new_qty' => $new_issues,
				'created' => time(),
				'user_id' => $user->data()->id,
				'remarks' => 'Convert item issues',
				'is_active' => 1,
				'company_id' => $user->data()->company_id,
				'type' => $orig_type
			));

			// add to converted type
			if($convert_type != 0){
				$curinvissues = $inv_issues->getQty($item_id,$branch_id,$des_rack_id,$convert_type);
				if(isset($curinvissues->qty)){
					$cur_issues = $curinvissues->qty;
				} else {
					$cur_issues = 0;
				}
				if($inv_issues->checkIfItemExist($item_id,$branch_id,$user->data()->company_id,$des_rack_id,$convert_type)){

					$inv_issues->addInventory($item_id,$branch_id,$convert_qty,false,$des_rack_id,$convert_type);
				} else {
					$inv_issues->addInventory($item_id,$branch_id,$convert_qty,true,$des_rack_id,$convert_type);
				}

				$new_issues = $cur_issues + $convert_qty;

				$inv_mon->create(array(
					'item_id' => $item_id,
					'rack_id' => $des_rack_id,
					'branch_id' => $branch_id,
					'page' => 'admin/inventory_adjustments.php',
					'action' => 'Update',
					'prev_qty' => $cur_issues,
					'qty_di' => 1,
					'qty' => $convert_qty,
					'new_qty' => $new_issues,
					'created' => time(),
					'user_id' => $user->data()->id,
					'remarks' => 'Convert item issue',
					'is_active' => 1,
					'company_id' => $user->data()->company_id,
					'type' => $convert_type
				));

			} else {

				if($inventory->checkIfItemExist($item_id,$branch_id,$user->data()->company_id,$des_rack_id)){
					$curinventory = $inventory->getQty($item_id,$branch_id,$des_rack_id);
					$inventory->addInventory($item_id,$branch_id,$convert_qty,false,$des_rack_id);

					// monitoring

					$newqty = $curinventory->qty + $convert_qty;
					$inventory_mon->create(array(
						'item_id' => $item_id,
						'rack_id' => $des_rack_id,
						'branch_id' => $branch_id,
						'page' => 'admin/addinventory',
						'action' => 'Update',
						'prev_qty' => $curinventory->qty,
						'qty_di' => 1,
						'qty' => $convert_qty,
						'new_qty' => $newqty,
						'created' => time(),
						'user_id' => $user->data()->id,
						'remarks' => 'Convert inventory from issues',
						'is_active' => 1,
						'company_id' => $user->data()->company_id
					));

				} else {
					$curinventory =0;
					$inventory->addInventory($item_id,$branch_id,$convert_qty,true,$des_rack_id);
					// monitoring
					$newqty = $curinventory + $convert_qty;
					$inventory_mon->create(array(
						'item_id' => $item_id,
						'rack_id' => $des_rack_id,
						'branch_id' => $branch_id,
						'page' => 'admin/addinventory',
						'action' => 'Insert',
						'prev_qty' => $curinventory,
						'qty_di' => 1,
						'qty' => $convert_qty,
						'new_qty' => $newqty,
						'created' => time(),
						'user_id' => $user->data()->id,
						'remarks' => 'Convert inventory from issues',
						'is_active' => 1,
						'company_id' => $user->data()->company_id
					));
				}
			}

		echo "Convert completed";
		} else {
		echo "Invalid data.";
		}
	}
	function prepareSpareparts(){
		$id= Encryption::encrypt_decrypt('decrypt',Input::get('id'));
		if(is_numeric($id)){
			$assemble_cls = new Assemble_request();
			$now = time();
			$assemble_cls->update(array('status'=> 2,'modified' => $now),$id);
			echo "Request process successfully.";
		} else {
			echo "Invalid request.";
		}
	}
	function convertSpareparts(){
		$lst = json_decode(Input::get('lst'));
		$user = new User();
		$id= Encryption::encrypt_decrypt('decrypt',Input::get('id'));
		if(is_numeric($id)){
			$inventory = new Inventory();
			$detassemble = new Assemble_details();
			foreach($lst as $item){
				$item_id = Encryption::encrypt_decrypt('decrypt',$item->item_id);
				$qty = Encryption::encrypt_decrypt('decrypt',$item->qty);
				$det_id = Encryption::encrypt_decrypt('decrypt',$item->det_id);
				$output_qty  = $item->output_qty;
				$rack_id = $item->rack_id;
				if(($item_id) && is_numeric($qty) && is_numeric($rack_id) && is_numeric($output_qty)){
					// add inventory
					if($inventory->checkIfItemExist($item_id,$user->data()->branch_id,$user->data()->company_id,$rack_id)){
						$curinventory = $inventory->getQty($item_id,$user->data()->branch_id,$rack_id);
						$inventory->addInventory($item_id,$user->data()->branch_id,$output_qty,false,$rack_id);

						// monitoring
						$inv_mon = new Inventory_monitoring();
						$newqty = $curinventory->qty + $output_qty;
						$inv_mon->create(array(
							'item_id' => $item_id,
							'rack_id' => $rack_id,
							'branch_id' => $user->data()->branch_id,
							'page' => 'ajax/ajax_query2',
							'action' => 'Update',
							'prev_qty' => $curinventory->qty,
							'qty_di' => 1,
							'qty' => $output_qty,
							'new_qty' => $newqty,
							'created' => time(),
							'user_id' => $user->data()->id,
							'remarks' => 'Assemble item inventory. Request #' . $id,
							'is_active' => 1,
							'company_id' => $user->data()->company_id
						));
					} else {
						$curinventory =0;
						$inventory->addInventory($item_id,$user->data()->branch_id,$output_qty,true,$rack_id);
						// monitoring

						$inv_mon = new Inventory_monitoring();
						$newqty = $curinventory + $output_qty;
						$inv_mon->create(array(
							'item_id' => $item_id,
							'rack_id' => $rack_id,
							'branch_id' => $user->data()->branch_id,
							'page' => 'ajax/ajax_query2',
							'action' => 'Insert',
							'prev_qty' => $curinventory,
							'qty_di' => 1,
							'qty' => $output_qty,
							'new_qty' => $newqty,
							'created' => time(),
							'user_id' => $user->data()->id,
							'remarks' => 'Assemble item inventory. Request #' . $id,
							'is_active' => 1,
							'company_id' => $user->data()->company_id
						));
					}
				$detassemble->update(array('output_qty' => $output_qty),$det_id);
				}
			}
			$assemble_cls = new Assemble_request();
			$assemble_cls->update(array('status'=> 3,'modified' => time()),$id);
			echo "Processed successfully.";
		} else {
			echo "Invalid data";
		}
	}

	function disassembleItem(){
		$item_list = Input::get('item_list');
		$item_list  = json_decode($item_list);
		$user = new User();
		$branch_id = Input::get('branch_id');
		if(!$branch_id){
			$branch_id = $user->data()->branch_id;
		}

		if(count($item_list)){
			$isValid = true;
			$checkOnly = [];

			foreach($item_list as $item){

					$checkOnly[$item->item_set] = $item->convertQty;

			}
			// check item availability
			$inv = new Inventory();
			foreach($checkOnly as $item_id => $qty_to_check){
				$cur_stock = $inv->getAllQuantity($item_id,$branch_id);
				if($cur_stock->totalQty < $qty_to_check){
					$isValid = false;
				}
			}


			if(!$isValid){
				echo "Not enough inventory";
			} else {
				// create assemble request
				$disassemble_cls = new Disassemble_request();
				$now = time();
				$disassemble_cls->create(array(
					'company_id' => $user->data()->company_id,
					'branch_id'=> $branch_id,
					'is_active' => 1,
					'created' => $now,
					'modified' => $now,
					'user_id'=> $user->data()->id,
					'status' => 1
				));
				$lastid = $disassemble_cls->getInsertedId();
				$inventory = new Inventory();
				$inv_mon = new Inventory_monitoring();
				foreach($item_list as $item){

					$qty_to_deduct = $item->convertQty;
					$item_set = $item->item_set;
					$rawlist = $item->splist;
					$rawlist = json_decode($rawlist);
					$allracks = [];
					$cur_inv = inventory_racking(0,$qty_to_deduct,$item_set,$branch_id,false);
					$racking = json_decode($cur_inv['racking']);
					$set_racking = $cur_inv['racking'];
					foreach($racking as $todec){
						$rack_id = $todec->rack_id;
						$rack_qty = $todec->qty;

						// check if item exists in rack
						if($inventory->checkIfItemExist($item_set,$branch_id,$user->data()->company_id,$rack_id)){
							$curinventoryFrom = $inventory->getQty($item_set,$branch_id,$rack_id);
							$currentqty = $curinventoryFrom->qty;
							$inventory->subtractInventory($item_set,$branch_id,$rack_qty,$rack_id);
						} else {
							$currentqty = 0;
						}


						// monitoring
						$newqtyFrom = $currentqty - $rack_qty;
						$inv_mon->create(array(
							'item_id' =>$item_set,
							'rack_id' => $rack_id,
							'branch_id' => $branch_id,
							'page' => 'ajax/ajax_query2.php',
							'action' => 'Update',
							'prev_qty' => $currentqty,
							'qty_di' => 2,
							'qty' => $rack_qty,
							'new_qty' => $newqtyFrom,
							'created' => time(),
							'user_id' => $user->data()->id,
							'remarks' => 'Disassemble item',
							'is_active' => 1,
							'company_id' => $user->data()->company_id
						));
					}

					//add details
					$disassemble_details_cls  = new Disassemble_details();
					$disassemble_details_cls->create(array(
						'disassemble_id' => $lastid,
						'item_id_set' => $item_set,
						'qty' => $qty_to_deduct,
						'set_racking' => $set_racking,
						'racking' => json_encode($rawlist),
						'status' => 1,
						'created' => $now,
						'is_active' => 1,
						'remarks' =>''
					));
				}
				echo "Item process successfully.";
			}
		}
	}
	function assembleItem(){
		$item_list = Input::get('item_list');
		$order_id = Input::get('order_id');
		$item_list  = json_decode($item_list);
		$user = new User();
		if(count($item_list)){
			$isValid = true;
			$checkOnly = [];

			foreach($item_list as $item){
				$rawlist = $item->splist;
				$rawlist = json_decode($rawlist);
				foreach($rawlist as $raw){
					if(isset($checkOnly[$raw->id])){
						$checkOnly[$raw->id] = $checkOnly[$raw->id] + $raw->need_total;
					} else {
						$checkOnly[$raw->id] = $raw->need_total;
					}
				}
			}
			// check spare parts availability
			// what to skip ?
			$rack_tags = new Rack_tag();
			$myTags = $rack_tags->get_my_tags($user->data()->id);
			if($myTags){
				$wh_tag = false;
				foreach($myTags as $mtag){
					if($mtag->id == 2){
						$wh_tag = true;
					}
				}
				if($wh_tag){
					// warehouse tag only
					$tags_ex = $rack_tags->get_tags_ex('wh_orders',$user->data()->company_id,$user->data()->branch_id);
					if(isset($tags_ex->id) && !empty($tags_ex->id)){
						$excempt_tags = $tags_ex->tag_id;
					} else {
						$excempt_tags =0;
					}
				} else {
					// assemble tag only
					$tags_ex = $rack_tags->get_tags_ex('assembly',$user->data()->company_id,$user->data()->branch_id);
					if(isset($tags_ex->id) && !empty($tags_ex->id)){
						$excempt_tags = $tags_ex->tag_id;
					} else {
						$excempt_tags =0;
					}
				}
			} else {
				// assemble tag only
				$tags_ex = $rack_tags->get_tags_ex('assembly',$user->data()->company_id,$user->data()->branch_id);
				if(isset($tags_ex->id) && !empty($tags_ex->id)){
					$excempt_tags = $tags_ex->tag_id;
				} else {
					$excempt_tags =0;
				}
			}

			$inv = new Inventory();
			foreach($checkOnly as $item_id => $qty_to_check){
				$cur_stock = $inv->getAllQuantity($item_id,$user->data()->branch_id,$excempt_tags);
				if($cur_stock->totalQty < $qty_to_check){
					$isValid = false;
				}
			}
			if(!$isValid){
				echo "Not enough inventory";
			} else {
				// create assemble request
				$assemble_cls = new Assemble_request();
				$now = time();
				$assemble_cls->create(array(
					'company_id' => $user->data()->company_id,
					'branch_id'=> $user->data()->branch_id,
					'is_active' => 1,
					'created' => $now,
					'modified' => $now,
					'user_id'=> $user->data()->id,
					'status' => 1,
					'wh_id' => $order_id
				));
				$lastid = $assemble_cls->getInsertedId();
				$inventory = new Inventory();
				$inv_mon = new Inventory_monitoring();
				foreach($item_list as $item){

					$qty_to_add = $item->convertQty;
					$item_set = $item->item_set;
					$rawlist = $item->splist;
					$rawlist = json_decode($rawlist);
					$allracks = [];
					// what to skip ?
					$rack_tags = new Rack_tag();
					$myTags = $rack_tags->get_my_tags($user->data()->id);
					if($myTags){
						$wh_tag = false;
						foreach($myTags as $mtag){
							if($mtag->id == 2){
								$wh_tag = true;
							}
						}
						if($wh_tag){
							// warehouse tag only
							$tags_ex = $rack_tags->get_tags_ex('wh_orders',$user->data()->company_id,$user->data()->branch_id);
							if(isset($tags_ex->id) && !empty($tags_ex->id)){
								$excempt_tags = $tags_ex->tag_id;
							} else {
								$excempt_tags =0;
							}
						} else {
							// assemble tag only
							$tags_ex = $rack_tags->get_tags_ex('assembly',$user->data()->company_id,$user->data()->branch_id);
							if(isset($tags_ex->id) && !empty($tags_ex->id)){
								$excempt_tags = $tags_ex->tag_id;
							} else {
								$excempt_tags =0;
							}
						}
					} else {
						// assemble tag only
						$tags_ex = $rack_tags->get_tags_ex('assembly',$user->data()->company_id,$user->data()->branch_id);
						if(isset($tags_ex->id) && !empty($tags_ex->id)){
							$excempt_tags = $tags_ex->tag_id;
						} else {
							$excempt_tags =0;
						}
					}
					foreach($rawlist as $raw){
						$cur_inv = inventory_racking(0,$raw->need_total,$raw->id,$user->data()->branch_id,false,$excempt_tags);
						$racking = json_decode($cur_inv['racking']);

						// deduct inv
						foreach($racking as $todec){
								$rack_id = $todec->rack_id;
								$rack_qty = $todec->qty;

							// check if item exists in rack
							if($inventory->checkIfItemExist($raw->id,$user->data()->branch_id,$user->data()->company_id,$rack_id)){
								$curinventoryFrom = $inventory->getQty($raw->id,$user->data()->branch_id,$rack_id);
								$currentqty = $curinventoryFrom->qty;
								$inventory->subtractInventory($raw->id,$user->data()->branch_id,$rack_qty,$rack_id);
							} else {
								$currentqty = 0;
							}


							// monitoring
							$newqtyFrom = $currentqty - $rack_qty;
							$inv_mon->create(array(
								'item_id' =>$raw->id,
								'rack_id' => $rack_id,
								'branch_id' => $user->data()->branch_id,
								'page' => 'ajax/ajax_query2.php',
								'action' => 'Update',
								'prev_qty' => $currentqty,
								'qty_di' => 2,
								'qty' => $rack_qty,
								'new_qty' => $newqtyFrom,
								'created' => time(),
								'user_id' => $user->data()->id,
								'remarks' => 'Deduct spare part for ' . $item->item_code,
								'is_active' => 1,
								'company_id' => $user->data()->company_id
							));
						}
						$indrack['racking'] = $cur_inv['racking'];
						$indrack['raw'] = $raw;
						$allracks[] = $indrack;
						unset($indrack['racking']);
						unset($indrack['raw']);
					}


					//add details
					$assemble_details_cls  = new Assemble_details();
					$assemble_details_cls->create(array(
						'assemble_id' => $lastid,
						'item_id_set' => $item_set,
						'qty' => $qty_to_add,
						'racking' => json_encode($allracks),
						'status' => 1,
						'created' => $now,
						'is_active' => 1,
						'remarks' =>''
					));
				}
				echo "Item process successfully.";
			}
		}
	}
	function getWhOrdersDetails(){

		$order_id = Input::get('order_id');

		$order_status = Input::get('order_status');

		$myOrder = new Wh_order($order_id);

		$user = new User();

		if($order_id){

			$whorder = new Wh_order_details();
			$orders = $whorder->getOrderDetails($order_id);
			$arr = [];
			$has_ins = false;
			$rack_tags = new Rack_tag();

			$tags_ex = $rack_tags->get_tags_ex('wh_orders',$user->data()->company_id,$myOrder->data()->branch_id);

			if(isset($tags_ex->id) && !empty($tags_ex->id)){
				$excempt_tags = $tags_ex->tag_id;
			} else {
				$excempt_tags =0;
			}

			$specific_rack = Configuration::getSpecificRack();
			$specific_rack_id = 0;

			if(count($specific_rack) && isset($specific_rack[$myOrder->data()->branch_id])){
				 $specific_rack_id = $specific_rack[$myOrder->data()->branch_id];
			}

			$rack = new Rack();
			$rack_defaults = $rack->getRackDefaults($myOrder->data()->branch_id);

			if(Configuration::getValue('surplus_rack') == 1){
				$surplus_rack_id = (isset($rack_defaults->surplus_rack)) ? $rack_defaults->surplus_rack : 0;
			} else {
				$surplus_rack_id = 0;
			}

			$sales = new Sales();

			foreach($orders as $order){

				if($myOrder->data()->status== 3 && $myOrder->data()->stock_out == 0){
					if($order->is_bundle == 0 && $order->item_type == -1){
						$item_for_order_cls = new Assemble_item_for_order();
						$item_for_order = $item_for_order_cls->getItem($order->item_id);
						$surplus_id = (Configuration::getValue('surplus_rack') == 1) ? $surplus_rack_id : 0;

						$spec_rack_id = $specific_rack_id;
						if($order->is_surplus){
							 $surplus_id = 0;
							 $spec_rack_id = $surplus_rack_id;
							 $item_racking = inventory_racking_spareparts($order->qty,$order->item_id,$myOrder->data()->branch_id,4);
						} else {
							if(isset($item_for_order->item_id) && $item_for_order->item_id){
								$item_racking = inventory_racking($order_id,$order->qty,$order->item_id,$myOrder->data()->branch_id,false,0,$spec_rack_id,$surplus_id);
							} else {
								$item_racking = inventory_racking($order_id,$order->qty,$order->item_id,$myOrder->data()->branch_id,false,$excempt_tags,$spec_rack_id,$surplus_id);
							}
						}



						$order->racking = $item_racking['racking'];
						if(!$has_ins){
							$has_ins = $item_racking['insufficient'];
						}
					} else {
						$order->racking = '[]';
					}
				}

				$order->adjustment_date = '';

				$order->last_sold_amount ='';
				$order->last_sold_date ='';
				$order->last_sold_info ='';
				$order->to_exclude = false;

				if($myOrder->data()->status== 1){

					$memadj = new Member_term();
					$member_adjustment_data = $memadj->getAdjustmentMember($myOrder->data()->member_id,$order->item_id);

					if($member_adjustment_data){
						$order->adjustment_date = date('m/d/y',$member_adjustment_data->created);
					} else {

						$sales_price = $sales->getLastPrice($order->item_id,$myOrder->data()->member_id);

						if(isset($sales_price->id) && $sales_price->id){
							$sales_ind_ajd = 0;
							if($sales_price->qtys && $sales_price->adjustment){
								$sales_ind_ajd = $sales_price->adjustment / $sales_price->qtys;
							}
							$sales_mem_ind_ajd = 0;
							if($sales_price->qtys && $sales_price->member_adjustment){
								$sales_mem_ind_ajd = $sales_price->member_adjustment / $sales_price->qtys;
							}

							$order->last_sold_date =date('m/d/y',$sales_price->sold_date);
							$order->last_sold_amount = number_format(($sales_price->price + $sales_ind_ajd + $sales_mem_ind_ajd),2);
							$order->last_sold_info = "Sold Date: " . $order->last_sold_date . " Price: ". number_format($order->last_sold_amount,2);

						} else {
							$order->last_sold_info ="No history";
						}
					}

				}

				if($order->is_bundle == 1){
					$bundle = new Bundle();
					$bundle_list = $bundle->getBundleItem($order->item_id);
						$bundleracking = json_decode($order->racking,true);
						$bundlef = [];
						if($bundle_list){
						foreach($bundle_list as $bl){
							if($myOrder->data()->status== 3 && $myOrder->data()->stock_out == 0){
								$item_racking = inventory_racking($order_id,$order->qty * $bl->child_qty,$bl->item_id_child,$myOrder->data()->branch_id,false,$excempt_tags,$specific_rack_id,$surplus_rack_id);
								$bl->racking = $item_racking['racking'];
								$retrack = "";
								if(!$has_ins){
									$has_ins = $item_racking['insufficient'];
								}
								$rackdecode = json_decode($bl->racking);
								if($rackdecode){
									foreach($rackdecode as $r){
									$retrack .= "<p class='text-danger'>$r->rack <i class='fa fa-long-arrow-right'></i> $r->qty</p>";
									}
								}
								$bl->rackhtml = $retrack;
							}  else if($myOrder->data()->stock_out == 1){
								$thisracking = json_decode($bundleracking[$bl->item_id_child]);
								$retrack = [];
								$retrackhtml  = "";
								foreach($thisracking as $racked){
									$retrackhtml .= "<p class='text-danger'>$racked->rack <i class='fa fa-long-arrow-right'></i> $racked->qty</p>";
									$retrack[]= ['rack' => $racked->rack, 'qty' =>  $racked->qty,'stock_man' => $racked->stock_man];

								}
								$bl->rackhtml = $retrackhtml;
								$bl->rackjson = json_encode($retrack);
							}
							$bundlef[] = $bl;
						}
						}

						 $order->bundles = json_encode($bundlef);
				} else {
					$order->bundles = "[]";
				}
				$adjusted_price = $order->adjusted_price;
				$total = $order->qty * $adjusted_price;
				$order->total = number_format($total,2,'.','');
				$order->machine = ($order->item_id_set)  ? 1 : 0;
				$order->adjusted_price = number_format($adjusted_price,2);
				$order->adjusted_total = number_format($total + $order->member_adjustment,2);
				$order->member_adjustment = number_format($order->member_adjustment,2,'.',"");
				if($order->member_adjustment){
					$ind_adj = $order->member_adjustment / floor($order->qty);
					$order->ind_discount = number_format($ind_adj,2,".","");
					$order->ind_price =  number_format(($adjusted_price + $ind_adj),2, ".","");
				} else {
					$order->ind_price = $adjusted_price;
				}
				$arr_div = getWholeAndDecimal($order->qty);
				$order->qty_whole = $arr_div['whole'];
				$order->orig_qty = $order->qty;
				$order->qty_decimal = (float) $arr_div['decimal'];
				$order->mem_adj = false;
				if(Configuration::getValue('mem_adj') == 1 && Configuration::getValue('mem_adj_round')){
					$to_round = Configuration::getValue('mem_adj_round');
					// 0.01 0.9
					//echo "$order->qty_decimal && $to_round >=$order->qty_decimal";
					if($order->qty_decimal && $to_round >=$order->qty_decimal){
					//echo "IN";
						$order->member_adjustment_round = -1 * ($adjusted_price * $order->qty_decimal);
						$order->member_adjustment_round = number_format($order->member_adjustment_round,2,".","");
						$order->mem_adj = true;
					}
					if($order->qty_decimal && $to_round < $order->qty_decimal){ // 0.9
					/*	$temp_qty_decimal = 1 - $order->qty_decimal;
						if($order->qty_decimal && $to_round >=$temp_qty_decimal){
						$order->member_adjustment_round = ($adjusted_price * $temp_qty_decimal);
							$order->mem_adj = true;
						} */
					}
				}

				$order->qty = formatQuantity($order->qty);
				if(Configuration::getValue('adjustment_default') == 2){
						$order->member_adjustment = $order->member_adjustment * -1;
				}

				$order->override_price = '';
				$arr[] = $order;

				if($order->has_serial == 1 && ($myOrder->data()->status == 3 || $myOrder->data()->status == 4)){
					$serials = new Serial();
					$countser = $serials->countSerials($myOrder->data()->payment_id,$order->item_id);
					$cur_ser_added = isset($countser->cnt) ? $countser->cnt : 0;
					if($cur_ser_added == $order->qty){
						$order->done_serial = 1;
					}
				}
			}
			if(Configuration::getValue('inv_strict') == 1){ // not strict
				$has_ins = false;
			}
			echo json_encode(array('order' => json_encode($arr), 'ins' => $has_ins));
		}
	}

	function inventory_racking($order_id=0,$qty=0,$item_id = 0,$branch_id=0,$deduct_prev = false,$tags=0,$specific_rack_id=0,$surplus_rack=0){
		$inv = new Inventory();
		$qty_racks = [];
		$insufficient = false;

		$inv_racks = $inv->get_racking($item_id,$branch_id,$tags,0,$specific_rack_id,$surplus_rack);
			if($inv_racks){
				$prev_order = 0;
				if($deduct_prev){
					// get prev order
					$wh_order = new Wh_order();
					$get_order_res = $wh_order->getPendingOrderQty($item_id,$branch_id,$order_id);
					if($get_order_res){
						$prev_order = $get_order_res->od_qty;
					}
				}

				if($inv_racks){
					foreach($inv_racks as $racking){
						if($prev_order > $racking->rack_qty){
							$prev_order = $prev_order - $racking->rack_qty;
						} else {
							$racking->rack_qty = $racking->rack_qty - $prev_order;
							$prev_order=0;
							$r_desc='';
							if($racking->rack_description){
							//		$r_desc = " (".$racking->rack_description.")";
							}
							if($racking->rack_qty > 0){
								if($qty > $racking->rack_qty){
									$qty = $qty - $racking->rack_qty;

									$qty_racks[] = array('rack' => $racking->rack . $r_desc,'rack_description' => $racking->rack_description,'stock_man' => $racking->stock_man,'qty' => $racking->rack_qty,'rack_id' => $racking->rack_id );
								} else {

									$qty_racks[] = array('rack' => $racking->rack . $r_desc,'rack_description' => $racking->rack_description,'stock_man' => $racking->stock_man,'qty' => $qty,'rack_id' => $racking->rack_id );
									$qty =0;
									break;
								}
							}
						}
					}
				}
			}
			if($qty > 0){
				$qty_racks[] = array('rack' => 'Insufficient stock','qty' => $qty,'rack_id' => 0 );
				$insufficient = true;
			}

		return array('racking' => json_encode($qty_racks),'insufficient' => $insufficient);
	}

	function shipOrder(){
		$order_id = Input::get('order_id');
		if($order_id){
			$whorder = new Wh_order($order_id);
			$user= new User();

			$success = true;
			$msg = '';

			if($success){
				$now = time();
				$scheduleOrder = new Wh_order_date();
				$scheduleOrder->create(array(
					'company_id' => $user->data()->company_id,
					'user_id' => $user->data()->id,
					'created' =>$now,
					'modified' =>$now,
					'is_active' =>1,
					'schedule_date' =>$whorder->data()->is_scheduled,
					'wh_order_id' =>$order_id
				));
				$whorder->update(array(
				'status' => 4,
				),$order_id);
				$msg = 'Shipped successfully.';


				$returnable = new Returnable();
				$returnables = $returnable->hasReturnables($order_id);


				if($returnables){

					$member_equipment_request = new Member_equipment_request();
					$member_equipment_request->create(
						array(
							'member_id' => $whorder->data()->member_id,
							'wh_order_id' => $order_id,
							'created' => $now,
							'status' => 0,
							'is_active' => 1,
							'company_id' => $user->data()->company_id,
							'remarks' => ''
						)
					);

					$return_last_id = $member_equipment_request->getInsertedId();

					foreach($returnables as $rn){
						$member_equipment_request_detail = new Member_equipment_request_detail();
						$member_equipment_request_detail->create(
							['request_id' => $return_last_id,'item_id' => $rn->item_id, 'qty' => $rn->qty]
						);
					}

				}
				// check if

			}

			echo json_encode(['success' => $success,'message' => $msg]);
		}
	}
	function approveWhOrder(){
		$order_id = Input::get('order_id');
		if($order_id){
			$whorder = new Wh_order($order_id);
			$now = time();
			$hasSales = false;
			$success = true;
			$msg = '';
			$user = new User();

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
				$whorder->update(array(
				'status' => $status, // flowchange
				'approved_date' => $now
				),$order_id);


				Log::addLog($user->data()->id,$user->data()->company_id,"Approve Order ID $order_id","ajax_query2.php");

			}

			echo json_encode(['success' => $success,'message' => $msg]);
		}
	}
	function getPaymentsOrder(){
		$order_id = Input::get('order_id');
		$myOrder = new Wh_order($order_id);
		$id = $myOrder->data()->payment_id;
		$cash = new Cash();
		$credit = new Credit();
		$cheque = new Cheque();
		$bt = new Bank_transfer();
		$con = new Payment_consumable();
		$conFree = new Payment_consumable_freebies();
		$member_credit = new Member_credit();


		$cash_list = $cash->get_active('cash',array('payment_id','=',$id));
		$credit_list = $credit->get_active('credit_card',array('payment_id','=',$id));
		$cheque_list = $cheque->get_active('cheque',array('payment_id','=',$id));
		$bt_list = $bt->get_active('bank_transfer',array('payment_id','=',$id));
		$con_list = $con->get_active('payment_consumable',array('payment_id','=',$id));
		$conFree_list = $conFree->get_active('payment_consumable_freebies',array('payment_id','=',$id));
		$member_credit_list = $member_credit->get_active('member_credit',array('payment_id','=',$id));

		$arr=[];
		if($cash_list){
			foreach($cash_list as $c){
				$arr['cash']['id'] = $c->id;
				$arr['cash']['amount'] = $c->amount;
				$arr['cash']['date'] = '';
			}
		}
		if($credit_list){
			foreach($credit_list as $c){
				$arr['credit']['id'] = $c->id;
				$arr['credit']['amount'] = $c->amount;
				$arr['credit']['date'] = date('m/d/Y',$c->date);
			}
		}
		if($cheque_list){
			foreach($cheque_list as $c){
				$arr['cheque']['id'] = $c->id;
				$arr['cheque']['amount'] = $c->amount;
				$arr['cheque']['date'] = date('m/d/Y',$c->payment_date);
			}
		}
		if($bt_list){
			foreach($bt_list as $c){
				$arr['bt']['id'] = $c->id;
				$arr['bt']['amount'] = $c->amount;
				$arr['bt']['date'] = date('m/d/Y',$c->date);
			}
		}
		if($con_list){
			foreach($con_list as $c){
				$arr['con']['id'] = $c->id;
				$arr['con']['amount'] = $c->amount;
				$arr['con']['date'] = '';
			}
		}
		if($conFree_list){
			foreach($conFree_list as $c){
				$arr['conf']['id'] = $c->id;
				$arr['conf']['amount'] = $c->amount;
				$arr['conf']['date'] = date('m/d/Y',$c->date);
			}
		}
		if($member_credit_list){
			foreach($member_credit_list as $c){
				$arr['mem_credit']['id'] = $c->id;
				$arr['mem_credit']['amount'] = $c->amount;
				$arr['mem_credit']['date'] = '';
			}
		}

		echo json_encode($arr);
	}

	//togglePriorityOrder
	function toggleReserveWhOrder(){
		$order_id = Input::get('order_id');
		if($order_id){
			$whorder = new Wh_order($order_id);
			$whorder->update(array(
				'is_reserve' => 0,
			),$order_id);
			echo json_encode(['success' => true]);
		}
	}

	function declineWhOrder(){

		$order_id = Input::get('order_id');
		$remarks  = Input::get('remarks');

		if($order_id){

			$whorder = new Wh_order($order_id);

			$remarks = ($remarks) ? $remarks  : '';

			$whorder->update(array(
				'status' => 5,
				'cancel_remarks' => $remarks
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

			echo json_encode(['success' => true]);

		}

	}
	function togglePriorityOrder(){
		$order_id = Input::get('order_id');
		$whorder = new Wh_order($order_id);
		$status = ($whorder->data()->is_priority == 1) ? 0 : 1;
		if($order_id){

			$whorder->update(array(
				'is_priority' => $status,
			),$order_id);
			echo json_encode(['success' => true]);
		}
	}

	function getAdjustmentPrice($branch_id=0,$item_id = 0, $member_id = 0,$qty=0){
		$adjustment_class = new Item_price_adjustment();
		$branch_id_to=0;

		if($branch_id && $item_id && $member_id && $qty){
			$is_ret = true;
		} else {
			$is_ret = false;
			$branch_id = Input::get('branch_id');
			$item_id = Input::get('item_id');
			$member_id = Input::get('member_id');
			$qty = Input::get('qty');
			$branch_id_to = Input::get('branch_id_to');
		}

		$price_group_id = Input::get('price_group_id');


		$nadj = 0;
		$alladj = 0;
		$ctr_item = 1;
		$_SESSION['cart_item_counter'] = $ctr_item;

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

			// kung okay lang mag negative, laging valid yung inventory
			if(Configuration::getValue('strict_order') == 2){
				$valid = 1;
			}

			// Member Individual Adjustment
			$mem_ajdustment = __itemMemberAdjustment($member_id,$item_id,$qty);
			$alladj = $mem_ajdustment['adjustment'];
			$remarks_for_adjustment =  $mem_ajdustment['remarks'];

			// Item Individual Adjustment by branch
			$adj = $adjustment_class->getAdjustment($branch_id,$item_id);
			if(isset($adj->adjustment)){
				$nadj += $adj->adjustment;
			} else {
				$nadj += 0;
			}

			// Price group Adjustment
			if($price_group_id){
				$adj_price_group = $adjustment_class->getAdjustmentPriceGroup($item_id,$price_group_id);
				if(isset($adj_price_group->adjustment)){
					$nadj += $adj_price_group->adjustment;
				}
			}


			// additional discount in Branch discount table
			$prod = new Product();
			$price = $prod->getPrice($item_id);

			$branch_discount = new Branch_discount();
			$b_disc = $branch_discount->getDiscount($branch_id,$branch_id_to);
			$b_disc_amount = 0;
			if(isset($b_disc->discount) && !empty($b_disc->discount)){
			$b_disc_amount = $b_disc->discount / 100;

			$b_disc_amount = $price->price * $b_disc_amount;
			}
			if($b_disc_amount){
				$b_disc_amount  = ($b_disc_amount * $qty) * -1;
				$alladj += $b_disc_amount;
			}


			// discount by category table
			if(Configuration::getValue('discount_by_category') == 1){
				$totaladd = $qty * discountByCategory($item_id,$member_id,$price->price);
				$alladj += $totaladd;

			}

		} else {
			$nadj += 0;
		}


		// get item freebie of item
		$item_freebie = new Item_freebie();
		$freebies = $item_freebie->getFreebies($item_id, $qty,$branch_id);

		$arr_freebies = [];
		if($freebies){
			foreach($freebies as $fr){
			$nadj_free =0;
			$adj_free = $adjustment_class->getAdjustment($branch_id,$fr->item_id);
			if(isset($adj_free->adjustment)){
				$nadj_free += $adj_free->adjustment;
			} else {
				$nadj_free += 0;
			}

			if($price_group_id){
				$adj_price_group_free = $adjustment_class->getAdjustmentPriceGroup($fr->item_id,$price_group_id);
				if(isset($adj_price_group_free->adjustment)){
					$nadj_free += $adj_price_group_free->adjustment;
				}
			}

			$fr->price += $nadj_free;
			$total = $fr->qty * $fr->price;

			$need_qty = $fr->need_qty;
			$discount = $fr->discount / 100;
			$total = $total * $discount;
			$total = number_format($total,2,".","");

			$multiplier =  $qty / $need_qty ;

			$multiplier = floor($multiplier);

			$fr->qty = $fr->qty *  $multiplier;

			$total = ($total * $multiplier);

				$arr_freebies[] = [
					'item_id' => $fr->item_id,
					'item_code' => $fr->item_code,
				    'description' => $fr->description,
				    'qty' => formatQuantity($fr->qty),
				    'price' => $fr->price,
				    'inv_qty' => formatQuantity($fr->inv_qty),
				    'price' => $fr->price,
				    'total' => $total
				 ];
			}
		} // end freebies


		$group_adjustment_optional = [];
		if(Configuration::getValue('group_adjustment_optional') == 1){
			$grp_adj_opt = new Item_group_adjustment();
			$grp_list = $grp_adj_opt->getAdjustment($item_id);
			if($grp_list){
				$group_adjustment_optional = [];
				foreach($grp_list as $grp_item){
					$group_adjustment_optional[] = $grp_item;
				}
			}
		}

		$_SESSION['cart_item_counter'] = ($_SESSION['cart_item_counter'])? $_SESSION['cart_item_counter'] : 1;
		if($is_ret){
			return  $nadj . "||" . $alladj. "||".$valid. "||" . $_SESSION['cart_item_counter'] . "||" .$remaining. "||" . $final_message;
		} else {
			 $output = $nadj . "||" . $alladj. "||".$valid. "||" . $_SESSION['cart_item_counter'] . "||" .$remaining. "||" . $final_message;
			echo json_encode(['data' => $output, 'freebies' => $arr_freebies, 'group_adjustment' => $group_adjustment_optional,'adjustment_remarks' => $remarks_for_adjustment]);
		}

	}


	function getSupplierItem() {

		$user = new User();
		$item_id = Input::get('item_id');
		$item_id = Encryption::encrypt_decrypt('decrypt', $item_id);
		$supplier = new Supplier_item();
		$items = $supplier->getItemSupBaseOnProducId($user->data()->company_id, $item_id);
		if($items){
			echo "<table class='table'>";
			echo "<tr><th>Supplier</th><th>Item Code</th><th>Description</th><th>Purchase Price</th></tr>";
			foreach($items as $item) {
				echo "<tr><td>$item->supname</td><td>$item->item_code</td><td>$item->description</td><td>$item->purchase_price</td></tr>";
			}
			echo "</table>";
		} else {
			echo "<span class='glyphicon glyphicon-info-sign'></span> <span>No date yet.</span>";
		}

	}

	function addSupplierItem() {
		$user = new User();
		$p_item_id = Input::get('p_item_id');
		$p_item_id = Encryption::encrypt_decrypt('decrypt', $p_item_id);
		$p_supplier_id = Input::get('p_supplier_id');
		$p_sitem_code = Input::get('p_sitem_code');
		$p_description = Input::get('p_description');
		$p_purchase_price = Input::get('p_purchase_price');
		$p_min_qty = Input::get('p_min_qty');
		$sup_item = new Supplier_item();
		$isExists = $sup_item->checkIfItemOnSupExists($user->data()->company_id, $p_supplier_id, $p_item_id);
		if($isExists->cnt > 0) {
			echo 'Supplier item already exists';
			exit();
		}
		$sup_item->create(array('item_id' => $p_item_id, 'supplier_id' => $p_supplier_id, 'item_code' => $p_sitem_code, 'description' => $p_description, 'purchase_price' => $p_purchase_price, 'min_qty' => $p_min_qty, 'created' => time(), 'modified' => time(), 'company_id' => $user->data()->company_id, 'is_active' => 1));
		echo "Supplier item added successfully";
	}

	function salesPastTenTransaction() {
		$mem_id = Input::get('mem_id');
		$sales = new Sales();
		$memsales = $sales->getSalesMember10($mem_id);
		$arr = [];
		if($memsales) {
			foreach($memsales as $s) {
				$obj['y'] = date('M d, Y', $s->sold_date);
				$obj['a'] = $s->saletotal;
				array_push($arr, $obj);
			}
			echo json_encode($arr);
		} else {
			echo json_encode(array('error' => true));
		}
	}

	function topMemberStation() {
		$dt1 = Input::get('dt1');
		$dt2 = Input::get('dt2');
		$member_id = Input::get('mem_id');
		$gsales = new Sales();
		$user = new User();
		// base on branch
		$stationsales = $gsales->topStationMember($member_id, $dt1, $dt2);

		$arr = [];

		if($stationsales) {
			foreach($stationsales as $bb) {
				if(!$bb->name) continue;
				$obj['label'] = $bb->name;
				$obj['value'] = $bb->saletotal;
				array_push($arr, $obj);
			}
		}
		if($arr) {
			echo json_encode($arr);
		} else {
			echo json_encode(array('error' => true));
		}
	}

	function topItemMember() {
		$member_id = Input::get('mem_id');
		$dt1 = Input::get('dt1');
		$dt2 = Input::get('dt2');
		$gsales = new Sales();
		$user = new User();
		$itemsales = $gsales->topItemMember($member_id, $dt1, $dt2);
		$arr = [];
		if($itemsales) {
			foreach($itemsales as $bb) {
				$obj['y'] = $bb->item_code;
				$obj['a'] = $bb->saletotal;
				array_push($arr, $obj);
			}
		}

		if($arr) {
			echo json_encode($arr);
		} else {
			echo json_encode(array('error' => true));
		}
	}

	function statsMemberPerTransaction() {

		$member_id = Input::get('mem_id');
		$type = Input::get('type');
		$gsales = new Sales();
		$user = new User();
		// base on branch
		$stationsales = $gsales->statsMemberPerTransaction($member_id);
		if($type == 1) {
			$arr = [];
			if($stationsales) {
				$obj['label'] = "Lowest sales Transaction";
				$obj['value'] = $stationsales->mintotal;
				array_push($arr, $obj);
				$obj['label'] = "Highest sales Transaction";
				$obj['value'] = $stationsales->maxtotal;
				array_push($arr, $obj);
				$obj['label'] = "Average sales Transaction";
				$obj['value'] = $stationsales->avgtotal;
				array_push($arr, $obj);
			}
			if($arr) {
				echo json_encode($arr);
			} else {
				echo json_encode(array('error' => true));
			}
		} else if($type == 2) {
			if($stationsales) {
				echo "<br>";
				echo "<table class='table'>";
				echo "<tr><td><strong><i class='fa fa-arrow-up'></i> Highest sales Transaction</strong></td><td class='text-danger'><i class='fa fa-rouble'></i> <strong>" . number_format($stationsales->maxtotal, 2) . "</strong></td></tr>";
				echo "<tr><td><strong><i class='fa fa-asterisk'></i> Average sales Transaction</strong></td><td class='text-danger'><i class='fa fa-rouble'></i> <strong>" . number_format($stationsales->avgtotal, 2) . "</strong></td></tr>";
				echo "<tr><td><strong><i class='fa fa-arrow-down'></i> Lowest sales Transaction</strong></td><td class='text-danger'><i class='fa fa-rouble'></i> <strong>" . number_format($stationsales->mintotal, 2) . "</strong></td></tr>";
				echo "</table>";
			} else {
				echo "No Data Found";
			}
		}
	}

	function printOrderInventory() {
		$transferId = Input::get('transfer_id');
		$backload = Input::get('is_backload');
		$user = new User();
		$cls = new Transfer_inventory_mon($transferId);
		$company_details = $user->getCompany($user->data()->company_id);
		$list = $cls->getOrderItems($user->data()->company_id, $transferId,$backload);
		$item1 = $list[0];
		$arrdata = [];

		$arrinfo['company_name'] = $company_details->name;
		$arrinfo['company_address'] = $company_details->address;
		$arrinfo['logo'] = "http://" . $_SERVER['HTTP_HOST'] . "/css/img/logo.jpg";
		$arrinfo['branch_from'] = ($item1->bname) ? $item1->bname  :'Internal transfer';
		$arrinfo['ref_number'] = ($item1->t_ref_number) ? $item1->t_ref_number :'';
		$arrinfo['branch_from_address'] =  ($item1->baddress) ? $item1->baddress :'';
		$arrinfo['branch_to_address'] = ($item1->b2address) ? $item1->b2address: '';
		if($backload == '1'){

			$arrinfo['branch_to'] = ($item1->b2name) ? $item1->b2name :$item1->member_name;
			$arrinfo['branch_to_address'] = ($item1->station_name) ? $item1->station_name : '';

		} else {
			$arrinfo['branch_to'] = ($item1->b2name) ? $item1->b2name :$item1->member_name;
		}



		$arrinfo['id'] = str_pad($transferId,6,'0',STR_PAD_LEFT);
		$arrinfo['date'] = date('m/d/Y');
		$arrinfo['wh_remarks'] = $item1->wh_remarks;
		$arrinfo['is_backload'] = $backload;
		if($cls->data()->remarks){
			$arrinfo['wh_remarks'] = $cls->data()->remarks;

		}
		if($cls->data()->from_where =='From transfer'){
			$arrinfo['branch_to'] = 'Internal transfer';
			$arrinfo['branch_from'] = '';
		}
		foreach($list as $l) {
			$arrdata[] =['qty' => formatQuantity($l->qty) , 'item_code' => $l->item_code,'description' =>  $l->description];
		}

		echo json_encode(['main' => $arrinfo, 'details' => $arrdata]);

	}

	function getNotificationRemarks() {
		$user = new User();
		$item_id = Input::get('item_id');
		$payment_id = Input::get('payment_id');

		$noticls = new Notification_remarks();
		$list = $noticls->getNotificationRemarks($user->data()->company_id, $item_id, $payment_id);
		if($list) {
			echo "<table class='table'>";
			echo "<thead><tr><th>User</th><th>Date Created</th><th>Remarks</th></tr></thead>";
			echo "<tbody>";
			foreach($list as $l) {
				echo "<tr><td>" . escape(ucwords($l->lastname . ", " . $l->firstname)) . "</td>";
				echo "<td>" . date('m/d/Y', $l->created) . "</td>";
				echo "<td class='text-danger'><strong>" . escape($l->remarks) . "</strong></td></tr>";
			}
			echo "</tbody>";
			echo "</table>";
		} else {
			echo "<div class='alert alert-danger'>No remarks</div>";
		}

	}

	function saveNotificationRemarks() {
		$user = new User();
		$item_id = Input::get('item_id');
		$payment_id = Input::get('payment_id');
		$remarks = Input::get('remarks');
		$noticls = new Notification_remarks();
		$now = time();
		$noticls->create(array('item_id' => $item_id, 'payment_id' => $payment_id, 'remarks' => $remarks, 'user_id' => $user->data()->id, 'company_id' => $user->data()->company_id, 'modified' => $now, 'created' => $now, 'is_active' => 1));
		echo "Remarks added successfully";
	}

	function memberSubscription() {
		$subs = new Service();
		$user = new User();
		$m = Input::get('mem_id');
		$services = $subs->getSubsciption($user->data()->company_id, $m);
		?>

		<?php
		if($services) {
			?>
			<div id="no-more-tables">
				<table class="table">
					<thead>
					<tr>
						<th>Subscription ID</th>
						<th>Name</th>
						<th>Start Date</th>
						<th>End Date</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<?php
						foreach($services as $s) {
							?>
							<tr>
								<td data-title='Ref Id'><?php echo "<span class='badge'>" . $s->id . "</span>"; ?></td>
								<td data-title='Start Date'><?php echo date('m/d/Y', $s->start_date); ?></td>
								<td data-title='End Date'><?php echo date('m/d/Y', $s->end_date); ?></td>
								<td data-title='Status' class='text-danger'><?php

										$dayremaining = getDays(date('m/d/Y', $s->end_date));
										//	$dayremaining += 1;
										if($dayremaining > 0) {
											if($dayremaining > 1) {
												$dlabel = "days";
											} else {
												$dlabel = "day";
											}
											echo $dayremaining . " $dlabel remaining";
										} else {
											echo "Subscription Expired";
										}
									?></td>
							</tr>
							<?php
						}
					?>
					</tbody>
				</table>
			</div>
			<?php
		} else {
			?>
			<div class="alert alert-info">There is no current item at the moment...</div>
			<?php
		}
		?>
		<?php
	}
	function memberConsumableQuantity() {
		$subs = new Service();
		$user = new User();
		$m = Input::get('mem_id');
		$services = $subs->getServices($user->data()->company_id, $m);
		?>

		<?php
		if($services) {
			?>
			<div id="no-more-tables">
				<table class="table">
					<thead>
					<tr>
						<th>Subscription ID</th>
						<th>Name</th>
						<th>Start Date</th>
						<th>End Date</th>
						<th>Consumables</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<?php
						foreach($services as $s) {
							?>
							<tr>
								<td data-title='Ref Id'><?php echo "<span class='badge'>" . $s->id . "</span>"; ?></td>
								<td data-title='Start Date'><?php echo date('m/d/Y', $s->start_date); ?></td>
								<td data-title='End Date'><?php echo date('m/d/Y', $s->end_date); ?></td>
								<td data-title='Consumables'><?php echo  $s->consumable_qty; ?></td>
								<td data-title='Status' class='text-danger'><?php

										$dayremaining = getDays(date('m/d/Y', $s->end_date));
										//	$dayremaining += 1;
										if($dayremaining > 0) {
											if($dayremaining > 1) {
												$dlabel = "days";
											} else {
												$dlabel = "day";
											}
											echo $dayremaining . " $dlabel remaining";
										} else {
											echo "Subscription Expired";
										}
									?></td>
							</tr>
							<?php
						}
					?>
					</tbody>
				</table>
			</div>
			<?php
		} else {
			?>
			<div class="alert alert-info">There is no current item at the moment...</div>
			<?php
		}
		?>
		<?php
	}

	function removeTheme(){
		$user  = new User();
		$cls = new Style();
		$id = Input::get('id');
		$cls->unsetTheme($user->data()->company_id);
		Log::addLog($user->data()->id,$user->data()->company_id,"Update themes","ajax_query2.php");

		echo "Theme set to default";
	}
	function setTheme(){
		$user  = new User();
		$id = Input::get('id');
		$cls = new Style($id);
		$cls->unsetTheme($user->data()->company_id);
		$cls->setTheme($id);
		Log::addLog($user->data()->id,$user->data()->company_id,"Update themes","ajax_query2.php");
		echo "Theme set to " . $cls->data()->name;
	}
	function updatePickup(){
		$id = Input::get('id');
		$pickupcls = new Pickup();
		$user = new User();
		if($pickupcls->processPickup($id,$user->data()->id)){
			echo 1;
		} else {
			echo 0;
		}
	}
	function cancelPickupRequest(){
		$id = Input::get('id');
		$pickupcls = new Pickup();
		$user = new User();
		if(is_numeric($id)){
			$pickupcls->update(array(
				'status' => 3
			),$id);
			echo "Cancelled successfully";
		}

	}
	function getAllStocks(){
		$item_id = Input::get('item_id');
		$user = new User();
		if(isset($user->data()->company_id)){
			$inv = new Inventory();
			$allstocks = $inv->allStockBaseOnItem($item_id,$user->data()->company_id);
		}

		if($allstocks){

			echo "<table class='table table-bordered'>";
			echo "<tr><th>Branch</th><th>Item</th><th>Stocks</th><th>Rack Location</th></tr>";
			foreach($allstocks as $stock){
				echo "<tr><td>".escape($stock->bname)."</td><td>".escape($stock->item_code)."<br><small>".escape($stock->description)."</small></td><td>".escape($stock->qty)."</td><td>".escape($stock->rack)."</td></tr>";
			}

		} else {

			echo "<div class='alert alert-warning'>No available stocks</div>";

		}

	}

	function processAutoPO(){
		$user = new User();
		$jsondata = json_decode(Input::get('datajson'));

		$supid = Input::get('supid');
		$tobr = Input::get('tobr');
		$bid = Input::get('bid');


		$rackDisplay = new Rack();


		if($supid){
			// insert to supplier_orders
			$sup_order = new Supplier_order();
			$od = new Supplier_order_details();
			$now = time();
			$sup_order->create(array(
				'created' => $now,
				'modified' => $now,
				'company_id' => $user->data()->company_id,
				'is_active' => 1,
				'status' => 1,
				'user_id' => $user->data()->id,
				'supplier_id' => $supid,
				'branch_to' => $bid
			));
			$lastorderid = $sup_order->getInsertedId();
			$supplier_item = new Supplier_item();
			foreach($jsondata as $json){
				// insert to supplier_order_details
				$supitemid = $supplier_item->getSupplierItemId($user->data()->company_id,$supid,$json->item_id);
				if(!$supitemid->id) continue;
				$od->create(array(
					'supplier_item_id' => $supitemid->id,
					'qty' => $json->order_qty,
					'created' => $now,
					'modified' => $now,
					'company_id' => $user->data()->company_id,
					'supplier_order_id' => $lastorderid,
					'is_active' => 1,
					'get_qty' => 0
				));
			}
		} else {
			if($tobr == -1){
					// add assemble request
				foreach($jsondata as $json){
					//todo
					// add assemble details
				}
			} else {
				$now = time();
				$order = new Wh_order();
					$order->create(array(
						'branch_id' => $tobr,
						'member_id' => 0,
						'to_branch_id' =>$bid,
						'remarks' => '',
						'client_po' => '',
						'shipping_company_id' => 0,
						'created' => $now,
						'company_id' => $user->data()->company_id,
						'user_id' => $user->data()->id,
						'is_active' => 1,
						'status' => 3, // warehouse agad
						'stock_out' => 0,
						'for_pickup' => 0,
					));
					$lastItOrder = $order->getInsertedId();
				foreach($jsondata as $json){

						$order_details = new Wh_order_details();
						$prod = new Product();
						$price = $prod->getPrice($json->item_id);
						$order_details->create(array(
								'wh_orders_id' => $lastItOrder,
								'item_id' => $json->item_id,
								'price_id' => $price->id,
								'qty' => $json->order_qty,
								'created' => $now,
								'modified' => $now,
								'price_adjustment' => 0,
								'original_qty' => $json->order_qty,
								'company_id' => $user->data()->company_id,
								'is_active' => 1,
								'terms' => 0,
								'member_adjustment' => 0
							));
				}
			}

		}
		echo "Order submitted successfully";
	}
	function getItemOnBranch(){
		$user = new User();
		$issup = Input::get('issup');
		$tid = Input::get('tid');
		$itemlist = '';
		if($issup == 1){
			// get item supplier
			$supcls = new Supplier_item();
			$supitems = $supcls->getitemssup($user->data()->company_id,$tid);
			foreach($supitems as $ie){
				$itemlist .= "<option value='".$ie->item_id."' >". escape($ie->barcode).":". escape($ie->ic).":".escape($ie->des). "</option>";
			}

		} else {
			// get item branch
			$item = new Product();
			$items = $item->get_active('items', array('company_id', '=', $user->data()->company_id));

			foreach($items as $i):
				if($i->item_type != -1) continue;
				$itemlist .= "<option value='".$i->id."' >". escape($i->barcode).":". escape($i->item_code).":".escape($i->description). "</option>";
			endforeach;
		}
		echo $itemlist;
	}
	function saveSupplierItem(){
		$user = new User();
		$jsondata = Input::get('jsondata');
		if($jsondata){
			$jsondata = json_decode($jsondata);
			$itemcls = new Supplier_item();
			$itemexist = [];
			$itemadded= false;
			foreach($jsondata as $d){
				$isExists = $itemcls->checkIfItemOnSupExists($user->data()->company_id,$d->supplier_id,$d->item_id);

				if($isExists->cnt > 0){
					$itemexist[] =$d->description;
				} else {
					try {
						$itemcls->create(array(
							'item_code' =>$d->item_code,
							'description' => $d->description,
							'min_qty' =>$d->min_qty,
							'purchase_price' => $d->purchase_price,
							'supplier_id' => $d->supplier_id,
							'is_active' => 1,
							'created' => time(),
							'modified' => time(),
							'company_id' => $user->data()->company_id,
							'item_id' => $d->item_id
						));
						$itemadded = true;
					} catch(Exception $e){
						die($e);
					}
				}
			}
			if($itemadded){
				echo "Item added successfully";
			}
			if(count($itemexist) > 0){
				echo "Some items already exists in the database";
			}
		}


	}

	function updateConsumableAmount(){

		$id = Input::get('id');
		$amount = Input::get('amount');

		$consumables = new Consumable_amount();
		$consumables->update(array(
			'amount' => $amount
		),$id);

		$user = new User();

		Log::addLog($user->data()->id,$user->data()->company_id,"Update Consumable ID $id","ajax_query2.php");
		
		echo "Updated Successfully";

	}

	function updateConsumableFreebiesAmount(){
		$id = Input::get('id');
		$amount = Input::get('amount');
		$consumables = new Consumable_freebies();
		$consumables->update(array(
			'amount' => $amount
		),$id);
		echo "Updated Successfully";
	}


	function reprintItem(){

		$payment_id = Encryption::encrypt_decrypt('decrypt', Input::get('payment_id'));
		$print_type  =  Input::get('type');
		$sales = new Sales();
		$saleslist = $sales->salesTransactionBaseOnPaymentId($payment_id,1);

		if($saleslist){

			$finalarr = [];
			$membername = "";
			$cashiername = "";
			$stationname = "";
			$stationid = "";
			$stationaddress= "";
			$datesold = "";
			$remarks = "";
			$ctrnum='';
			$sales_type='';
			$terms='';
			$tin_no='';
			$order_id='';
			$itemlist = [];

			foreach($saleslist as $s){

				$membername = ucwords($s->mln . ", " . $s->mfn . " " . $s->mmn);
				$cashiername = ucwords($s->uln . ", " . $s->ufn . " " . $s->umn);

				if( $print_type == 1 ){
					$ctrnum = $s->pref_dr.$s->dr . $s->suf_dr;
				} else if ( $print_type == 2 ){
					$ctrnum = $s->pref_ir . $s->ir . $s->suf_ir;
				}

				$order_id =$s->wh_id;
				$remarks = $s->wh_remarks;
				$terms = $s->terms;
				$tin_no = $s->tin_no;
				$stationname = $s->personal_address;
				$stationid = $s->station_id;
				$stationaddress = $s->station_address;
				$sales_type = $s->sales_type_name;
				$datesold = date('m/d/Y',$s->sold_date);
				$total = ($s->qtys * $s->price) + ($s->adjustment + $s->member_adjustment) - ($s->discount + $s->store_discount);

				$ind_member_adj = 0;
				$ind_price_adj = 0;

				if($s->member_adjustment){
					$ind_member_adj =  $s->member_adjustment / $s->qtys;
				}

				if($s->adjustment){
					$ind_price_adj =  $s->adjustment / $s->qtys;
				}

				$price = $s->price + ($ind_price_adj + $ind_member_adj);
				$s->qtys = formatQuantity($s->qtys);
				$itemlist[] = ['item_code'=>escape($s->item_code),'description'=>escape($s->description), 'barcode'=>escape($s->barcode), 'qty'=>escape($s->qtys), 'price'=>escape($price), 'discount'=>escape($s->discount), 'total'=>escape($total)];

			}
			$remarks = ($remarks) ? $remarks : '';
			$finalarr['member_name'] = $membername;
			$finalarr['remarks'] = $remarks;
			$finalarr['cashier_name'] = $cashiername;
			$finalarr['station_name'] = $stationname;
			$finalarr['station_id'] = $stationid;
			$finalarr['station_address'] = $stationaddress;
			$finalarr['date_sold'] = $datesold;
			$finalarr['item_list'] = $itemlist;
			$finalarr['sales_type_name'] = $sales_type;
			$finalarr['ctrnum'] = $ctrnum;
			$finalarr['terms'] = $terms;
			$finalarr['tin_no'] = $tin_no;
			$finalarr['order_id'] = $order_id;

			echo json_encode($finalarr);
		}
	}

	function memberCreditSave(){
		$user = new User();
		$userfn = ucwords($user->data()->lastname . ", " . $user->data()->firstname);
		$amt = Input::get('amt');
		$remarks = Input::get('remarks');
		$unpaid = Input::get('unpaid');
		$id = Input::get('id');
		$remaining = $unpaid - $amt;
		$memcred = new Member_credit($id);
		$paid = $memcred->data()->amount_paid;
		$amt_paid = $paid + $amt;
		$arr = json_decode($memcred->data()->json_payment,true);
		$now = time();
		$arr[] = array('fn'=>$userfn,'amount'=>$amt,'date'=>$now,'remarks' => $remarks);
		$finalarr = json_encode($arr);
		if($remaining == 0){
			// update status = 1
			$memcred->update(
				array(
					'amount_paid'=>$amt_paid,
					'status'=>1,
					'json_payment'=>$finalarr
				),$id);
		} else {
			$memcred->update(
				array(
					'amount_paid'=>$amt_paid,
					'json_payment'=>$finalarr
				),$id);
		}
		echo "Payment received successfully";

	}
	function updateDocList(){
		$invList = rtrim(Input::get('arrchk'),',');
		$paymentId = Input::get('payment_id');
		$payment = new Payment();
		$payment->update(array('docs'=>$invList),$paymentId);
	}
	function finalDocList(){
		$paymentId = Input::get('payment_id');
		$payment = new Payment();
		$payment->update(array('isFinal'=>1),$paymentId);
		echo "Updated successfully";
	}

	function addMemberConsumable($member_id = 0, $amount = 0){
		$has_ret = false;
		$override_date = 0;
		if(!$member_id || !$amount)
		{
			$has_ret = true;
			$member_id = Input::get('member_id');
			$amount = Input::get('amount');
			$override_date = Input::get('override_date');
		}
		if($member_id && $amount){
			$user = new User();
			$now = time();
			if($override_date){
				$now = strtotime($override_date);
			}
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
			$consumable_amount->create(array(
				'member_id' => $member_id,
				'item_id' => $item_id,
				'payment_id' => $paymentLastId,
				'amount' => $amount,
				'service_id' => $serviceLastId,
				'created' => $now,
				'modified' => $now,
				'is_active' => 1
			));
				Log::addLog(
				$user->data()->id,$user->data()->company_id,
				"Add client consumable PID $paymentLastId",
				"ajax_query2.php");


			if($has_ret){
				echo "Added Successfully";
			}
		}
	}
	function addMemberConsumableFreebies(){
		$member_id = Input::get('member_id');
		$amount = Input::get('amount');

		if($member_id && $amount){
			$user = new User();
			$now = time();
			$nextYear = strtotime(date('F Y') . " 1 year");
			$company_id = $user->data()->company_id;
			$item_id = 1223; // static item id
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
			$consumable_amount = new Consumable_freebies();
			$consumable_amount->create(array(
				'member_id' => $member_id,
				'item_id' => $item_id,
				'payment_id' => $paymentLastId,
				'amount' => $amount,
				'service_id' => $serviceLastId,
				'created' => $now,
				'modified' => $now,
				'is_active' => 1
			));
			echo "Added Successfully";
		}

	}
	function replenishPettyCash(){
		$amount = Input::get('amount');
		$branch_id =Encryption::encrypt_decrypt('decrypt',Input::get('branch_id'));
		$user = new User();
		if(is_numeric($branch_id) && is_numeric($amount)){
			// create a request
			$petty_request = new Pettycash_request();
			$petty_breakdown = new Pettycash_breakdown();
			$now = time();
			$petty_request->create(array(
				'company_id' => $user->data()->company_id,
				'branch_id' => $branch_id,
				'amount' => $amount,
				'created' => $now,
				'modified' => $now,
				'status' => 1,
				'is_active' => 1,
				'user_id' =>  $user->data()->id

			));
			$lastid = $petty_request->getInsertedId();
			if($lastid){
				$petty_breakdown->updatePettyBreakdown($branch_id,$lastid);
			}
			echo "Request sent successfully.";
		}
	}
	function deletePettycashExpense(){
		$user = new User();
		$id = Input::get('id');
		if(is_numeric($id)){
			$petty_breakdown = new Pettycash_breakdown($id);
			$petty_holder = new Pettycash_holder();
			$petty_holder->addPettycash($user->data()->company_id,$user->data()->branch_id,$petty_breakdown->data()->amount,$user->data()->id,$petty_breakdown->data()->description . "(Cancel)");
			$petty_breakdown->deletePetty($id);
		}
	}

	function cancelItemService(){
		$id = Encryption::encrypt_decrypt('decrypt',Input::get('id'));
		$cancelStatus = 9;
		if(is_numeric($id)){
			// get service details
			/*$service_details = new Item_service_details();
			$details = $service_details->getDetails($id);
			if(count($details)){
				$user = new User();
				$inventory= new Inventory();
				$inv_mon = new Inventory_monitoring();
				foreach($details as $det){
					if(trim($det->sp_needed) != ""){
						$sp_needed = json_decode($det->sp_needed);

						foreach($sp_needed as $need){

							$item_id = $need->item_id;
							$racking = json_decode($need->racking);

							foreach($racking as $rack){
								// return back to inventory
								$rack_id = $rack->rack_id;
								$qty = $rack->qty;
								if($inventory->checkIfItemExist($item_id,$user->data()->branch_id,$user->data()->company_id,$rack_id)){
									$curinventory = $inventory->getQty($item_id,$user->data()->branch_id,$rack_id);
									$inventory->addInventory($item_id,$user->data()->branch_id,$qty,false,$rack_id);
									// monitoring
									$newqty = $curinventory->qty + $qty;
									$inv_mon->create(array(
										'item_id' => $item_id,
										'rack_id' => $rack_id,
										'branch_id' => $user->data()->branch_id,
										'page' => 'ajax/ajax_query2',
										'action' => 'Update',
										'prev_qty' => $curinventory->qty,
										'qty_di' => 1,
										'qty' => $qty,
										'new_qty' => $newqty,
										'created' => time(),
										'user_id' => $user->data()->id,
										'remarks' => 'Cancel service Request #' . $id,
										'is_active' => 1,
										'company_id' => $user->data()->company_id
									));
								}
							}

						}
					}
				}
			} */

			// get item release
			$service_request = new Item_service_request($id);

			$service_item_use = new Service_request_item();
			$used_items = $service_item_use->getDetails($id,4);
			$user = new User();
			if($used_items){
				$tranfer_mon = new Transfer_inventory_mon();
				$now = time();
				$tranfer_mon->create(array(
					'status' => 1,
					'is_active' =>1,
					'branch_id' =>$service_request->data()->branch_id,
					'company_id' =>$user->data()->company_id,
					'created' => $now,
					'modified' =>$now,
					'from_where' => 'From Service'
				));
				$lastid = $tranfer_mon->getInsertedId();

				foreach($used_items as $used_item){
					$prod = new Product($used_item->item_id);
					if($prod->data()->is_bundle == 1){
						$bundle = new Bundle();
						$bundle_list = $bundle->getBundleItem($used_item->item_id);
						if($bundle_list){
							foreach($bundle_list as $bl){
								$tranfer_mon_details = new Transfer_inventory_details();
								$tranfer_mon_details->create(array(
									'transfer_inventory_id' => $lastid,
									'rack_id_from' => 0,
									'rack_id_to' =>  0,
									'item_id' => $bl->item_id_child,
									'qty' =>  $used_item->qty * $bl->child_qty,
									'is_active' => 1
								));
							}
						}
					} else {
						$tranfer_mon_details = new Transfer_inventory_details();
						$tranfer_mon_details->create(array(
							'transfer_inventory_id' => $lastid,
							'rack_id_from' => 0,
							'rack_id_to' =>  0,
							'item_id' => $used_item->item_id,
							'qty' =>  $used_item->qty,
							'is_active' => 1
						));
					}
				}
			}

			$service_request->update(array('status' => $cancelStatus),$id);
			if($id){
				$consumable = new Consumable_amount();
				$con = $consumable->selectConsumableByServiceId($id);
				if(isset($con->id) && $con->id){
					$consumable->update(
						['amount' => 0],$con->id
					);
				}
			}


			Log::addLog($user->data()->id,$user->data()->company_id,"Item Service: Cancel Request ID $id","ajax_service_item.php");


			echo "Request cancelled successfully.";
			// check if it has sp
		}

	}
	function itemServiceDetails(){
		$id = Input::get('id');
		$user = new User();
		$isLog = Input::get('isLog');
		$data_main =[];
		$data_details = [];
		$is_aquabest = Configuration::isAquabest();

		if($id){
			$id = Encryption::encrypt_decrypt('decrypt',$id);
			$terminal_id = Input::get('terminal_id');
			if(is_numeric($id)){
				$details = new Item_service_details();
				$myreq = new Item_service_request($id);
				$member_name = "";
				$member_address ="";
				$member_contact ="";
				$member_sales_type ="";

				if($myreq->data()->member_id){
					$memberinfo = new Member($myreq->data()->member_id);
					if($memberinfo->data()->salestype){
						$sales_type = new Sales_type($memberinfo->data()->salestype);
						if(isset($sales_type->data()->name) && $sales_type->data()->name){
							$member_sales_type = $sales_type->data()->name;
						}
					}



					if($memberinfo){
					$member_name = $memberinfo->data()->lastname;
					$member_address = $memberinfo->data()->personal_address;
					$member_contact = $memberinfo->data()->contact_number;
					$member_address = str_replace('"',"",$member_address);
					$member_address = str_replace("'","",$member_address);
					}
				}
				$service_type_name = "";
				if($myreq->data()->service_type_id){
					$service_type_cls = new Service_type($myreq->data()->service_type_id);
					$service_type_name = $service_type_cls->data()->name;
				}
				$member_name = str_replace("'","",$member_name);
				$member_name = str_replace('"',"",$member_name);
				$member_address = str_replace("'","",$member_address);
				$member_address = str_replace('"',"",$member_address);

				$data_main = [
					'service_id' => $myreq->data()->id,
					'contact_person' => $myreq->data()->contact_person,
					'contact_number' => $myreq->data()->contact_number,
					'contact_address' => $myreq->data()->contact_address,
					'member_name' => $member_name,
					'member_address' => $member_address,
					'member_contact' => $member_contact,
					'sales_type_name' => $member_sales_type,
					'backload_ref_number' => $myreq->data()->backload_ref_number,
					'date' => date('F d, Y'),'service_type_name' => $service_type_name
				 ];

				$list = $details->getDetails($id);

				$status_arr = [];

				/*

					'Good',// 2
					'Repaired with warranty',  // 3
					'Repaired without warranty', // 4
					'Replacement(Junk)', // 5
					'Replacement(Surplus)', // 6
					'Change Item(Junk)',// 7
					'Change Item(Surplus)', // 8
					'Cancelled' // 9

				*/

				$optionActions='';
				$btnProcessAction='';
				if($myreq->data()->status == 1){
					if($myreq->data()->request_type == 2){
					// 1 = scheduled 2 = rece  3= repairing
					$optionActions = '<select name="new_type" id="new_type" class="form-control typecls">';
					$optionActions .= '<option value="11">Received</option>';
					$optionActions .= '</select>';
					} else if ($myreq->data()->request_type == 3){
					$optionActions = '<select name="new_type" id="new_type" class="form-control typecls">';
					$optionActions .= '<option value="12">Repairing</option>';
					$optionActions .= '<option value="13">Installing</option>';
					$optionActions .= '</select>';
					}
					$btnProcessAction = '<button id="btnProccessService" class="btn btn-primary">Submit</button>';

				} else 	if($myreq->data()->status == 2){
					$optionActions = '<select name="new_type" id="new_type" class="form-control typecls">';
					if($is_aquabest){
						$optionActions .= '<option value="3">Repair with warranty</option>';
						$optionActions .= '<option value="4">Repair without warranty</option>';
					} else {
						$optionActions .= '<option value="2">Good</option>';
						$optionActions .= '<option value="3">Repair with warranty</option>';
						$optionActions .= '<option value="4">Repair without warranty</option>';
						if(Configuration::thisCompany('avision')){ // nakalimutan ko na kung anong mga status yung available
							$optionActions .= '<option value="101">For Claims</option>';
							$optionActions .= '<option value="102">Refund</option>';
						}
						if(!Configuration::thisCompany('cebuhiq')){
							$optionActions .= '<option value="5">Replacement(Junk)</option>';
							$optionActions .= '<option value="6">Replacement(Surplus)</option>';
							$optionActions .= '<option value="7">Change Item(Junk)</option>';
							$optionActions .= '<option value="8">Change Item(Surplus)</option>';
						}

					}

					$optionActions .= '</select>';

					$btnProcessAction = '<button id="btnProccessService" class="btn btn-primary">Submit</button>';
				}
				if($myreq->data()->request_type == 2 && !$myreq->data()->pullout_schedule){
					?>
					<div class="row">
					<div class="col-md-6"><input type="text" class='form-control' placeholder='Pullout schedule' id='update_pullout_schedule'></div>
					<div class="col-md-6"><button data-id='<?php echo $myreq->data()->id; ?>' class='btn btn-primary' id='btn_update_pullout_schedule'>Update Pullout Schedule</button></div>
					</div>
					<br>
					<?php
				}

				?>

				<div id="no-more-tables">
					<input type="hidden" id='hid_det_id' value="<?php echo Encryption::encrypt_decrypt('encrypt',$id) ?>">
				<table id='tblDet' class='table'>
							<thead>
							<tr>
							
								<th>Barcode</th>
								<th>Item</th>
								<th>Qty</th>
								<th>Remarks</th>
								<th></th>
								<th></th>
								<?php if($user->hasPermission('item_service_overwrite') && $myreq->data()->status == 3 && !$isLog){
								?>
									<th>Overwrite Price</th>
								<?php

								}
								?>

							</tr>
							</thead>
							<tbody>
							<?php
							$withspare = false;
								$prodcls = new Product();
								$withPayment = false;
								$withCredit = false;
								$totalCreditToClient = 0;
								$withoutWarranty = false;

								foreach($list as $item){

									$tblspare = "";

									$item_code = ($item->item_code) ?  $item->item_code : '';

									$description = ($item->description) ?  $item->description : '';
									$pitem = new Product();
									$pprice = $pitem->getPrice($item->item_id);
									$adjustment_class = new Item_price_adjustment();
									$adj = $adjustment_class->getAdjustment($myreq->data()->branch_id,$item->item_id);
									$price_branch_adj = 0;
									$all_adjustment = 0;
									if(isset($adj->adjustment)){
										$price_branch_adj = $adj->adjustment;
									}
									$total_per_pc=0;
									if($item->item_id){
										$mem_ajdustment = __itemMemberAdjustment($myreq->data()->member_id,$item->item_id,$item->qty);
										$all_adjustment = $mem_ajdustment['adjustment'];
										$total_price = ($pprice->price+$price_branch_adj) * $item->qty;
										$ind_adj = 0;
										if($all_adjustment){
											$ind_adj = $all_adjustment / $item->qty;
										}
										$total_per_pc = $pprice->price + $price_branch_adj + $ind_adj;
										/*if($item->item_id == 2077 && $id == 4232){
											$total_per_pc = 0;
										}*/
										if($item->adjustment_price != 0.00 ){
											if($item->adjustment_price == -0.01){
												$total_per_pc = 0;
											} else {
												$total_per_pc = $item->adjustment_price /  $item->qty;
											}

										}
									}
								$description = str_replace('"','',$description);
										$data_details[] = [
													 'status' => $item->is_done,
													 'item_code' => $item_code,
													 'description' => $description,
													 'qty' => $item->qty,
													 'item_id' => $item->item_id,
													 'remarks' => $item->remarks,
													 'price' => $total_per_pc,
													 'orig_price' => $item->orig_price,
												  ];


									if($item->sp_needed){

										$sp_needed = json_decode($item->sp_needed);
										if(count($sp_needed)){
											$encid = Encryption::encrypt_decrypt('encrypt',$item->item_id);
											$tblspare = "<table  id='tbl_{$encid}' class='table'>";
												$tblspare .= "<thead><tr><th></th><th>Item</th><th>Qty</th><th>Racking</th></tr></thead>";
												$tblspare .= "<tbody>";
											foreach($sp_needed as $need){
												$n_rack = json_decode($need->racking);
												$racking = "";
												foreach($n_rack as $r){
												$racking .= "<p>$r->rack $r->qty</p>";
												}
												$spareprice = $prodcls->getPrice($need->item_id);
												$tblspare .= "<tr data-price='$spareprice->price' data-item_id='$need->item_id'><td>";
												if($user->hasPermission('item_service_p') && $item->is_done == 4){ // with warranty
													$tblspare .= "<input class='chk_spare' type='checkbox'> Charge";
												}
												$tblspare .= "</td><td>$need->raw_item_code</td><td>$need->qty</td><td>$racking</td></tr>";
											}
												$tblspare .= "</tbody>";
												$tblspare .= "</table>";
										}
									} else {
										$spare = new Composite_item();
										$spare_parts = $spare->getSpareparts($item->item_id);
										if($spare_parts){
											$withspare = true;
											if(count($spare_parts) > 0){
												$encid = Encryption::encrypt_decrypt('encrypt',$item->item_id);
												$tblspare = "<table id='tbl_{$encid}' class='table'>";
												$tblspare .= "<tbody>";
												foreach($spare_parts as $e){
													$tblspare .= "<tr data-item_id='".Encryption::encrypt_decrypt('encrypt',$e->item_id_raw)."'><td>$e->item_code</td><td><input type='text' class='form-control' placeholder='Qty'></td></tr>";
												}
												$tblspare .= "</tbody>";
												$tblspare .= "</table>";
											}
										}
									}
									if(!$item->item_code){
										$item->item_code = 'No item requested';
										$item->qty ='';
									}
									$withspare = false;
									$tblspare = '';
									$doneLbl='';
									$isdonehis = [];
									$doneArr = [
												'', // 0
											'Repairing', // 1
											'Good',// 2
											'Repair with warranty',  // 3
											'Repair without warranty', // 4
											'Replacement(Junk)', // 5
											'Replacement(Surplus)', // 6
											'Change Item(Junk)',// 7
											'Change Item(Surplus)', // 8
											'Cancelled', // 9
											'Scheduled', // 10
											'Received', // 11
											'Repairing', // 12
											'Installing', // 13
											];
									if($myreq->data()->status == 1){
										$tblspare = '';
										if(strpos($item->status_history,',')>0){
											$isdonehis = explode(',',$item->status_history);
										} else {
											$isdonehis[] = $item->status_history;
										}

										$counthis = count($isdonehis);
										$ihis = 1;
										$doneLbl .= "<span class='text-danger span-block'>";
										foreach($isdonehis as $eachhis){
											$arrowright = "";
											if($counthis > $ihis){
												$arrowright = "<i class='fa fa-arrow-right'></i> ";
											}
											$ihis++;
											 $doneLbl .= $doneArr[$eachhis] . " " . $arrowright;
										}
										$doneLbl .= "</span>";

									} else if($myreq->data()->status == 2){
										$tblspare ='';
											if(strpos($item->status_history,',')>0){
											$isdonehis = explode(',',$item->status_history);
										} else {
											$isdonehis[] = $item->status_history;
										}

										$counthis = count($isdonehis);
										$ihis = 1;
										$doneLbl .= "<span class='text-danger span-block'>";
										foreach($isdonehis as $eachhis){
											$arrowright = "";
											if($counthis > $ihis){
												$arrowright = "<i class='fa fa-arrow-right'></i> ";
											}
											$ihis++;
											 $doneLbl .= $doneArr[$eachhis] . " " . $arrowright;
										}
										$doneLbl .= "</span>";
									}else if($myreq->data()->status == 3){
											if(strpos($item->status_history,',')>0){
											$isdonehis = explode(',',$item->status_history);
										} else {
											$isdonehis[] = $item->status_history;
										}

										$counthis = count($isdonehis);
										$ihis = 1;
										$doneLbl .= "<span class='text-danger span-block'>";
										foreach($isdonehis as $eachhis){
											$arrowright = "";
											if($counthis > $ihis){
												$arrowright = "<i class='fa fa-arrow-right'></i> ";
											}
											$ihis++;

											 $doneLbl .= $doneArr[$eachhis] . " " . $arrowright;
										}
										$doneLbl .= "</span>";
									} else if($myreq->data()->status == 4){
										if(strpos($item->status_history,',')>0){
											$isdonehis = explode(',',$item->status_history);
										} else {
											$isdonehis[] = $item->status_history;
										}

										$counthis = count($isdonehis);
										$ihis = 1;
										$doneLbl .= "<span class='text-danger span-block'>";
										foreach($isdonehis as $eachhis){
											$arrowright = "";
											if($counthis > $ihis){
												$arrowright = "<i class='fa fa-arrow-right'></i> ";
											}
											$ihis++;
											 $doneLbl .= $doneArr[$eachhis] . " " . $arrowright;
										}
										$doneLbl .= "</span>";

									}
									$detqty='';
									if($item->qty){
									 $detqty = formatQuantity($item->qty,true);
									}


									if($item->is_done == 4){
										$withPayment = true;

									}
									if($item->is_done == 3){
										$withCredit = true;
										$withoutWarranty = true;
									}
									if($item->is_done == 2 || $item->is_done == 5 || $item->is_done == 6 || $item->is_done == 7 || $item->is_done == 8){
										$withCredit = true;


										$total_price = $total_price + $all_adjustment;
										$orig_price = $total_price;
									/*	if($item->item_id == 2077 && $id == 4232){
											$total_price = 0; //remmoveit
										}*/

										if($item->adjustment_price != 0.00 ){
											if($item->adjustment_price == -0.01){
												$total_price = 0;
											} else {
												$total_price = $item->adjustment_price;
											}
										}
										$totalCreditToClient +=$total_price;

									}
									?>
									<tr data-orig_price="<?php echo $orig_price; ?>" data-det_status='<?php echo $item->is_done; ?>' data-item_id='<?php echo  Encryption::encrypt_decrypt('encrypt',$item->item_id) ?>'>
										<td style='border-top:1px solid #ccc;' data-title='Barcode'>
										<?php echo escape(($item->barcode) ?  $item->barcode : ''); echo $doneLbl; ?>
										<small class='text-danger' style='display:block;' ><?php echo escape((isset($total_price) && $total_price) ?  number_format($total_price,2) : ''); ?></small>
										</td>
										<td style='border-top:1px solid #ccc;' data-title='Item'>
												<?php echo escape(($item->item_code)); ?>
												<small class='text-danger' style='display:block;' ><?php echo escape(($item->description) ?  $item->description : ''); ?></small>
										</td>
										<td style='border-top:1px solid #ccc;'  data-title='Qty'><?php echo escape($detqty); ?></td>
										<td style='border-top:1px solid #ccc;' data-title='Remarks'><?php echo escape($item->remarks); ?></td>
										<td style='border-top:1px solid #ccc;' ><?php echo  (!$isLog) ? $tblspare : ''; ?></td>

										<td style='border-top:1px solid #ccc;' >


										<?php if($myreq->data()->payment_id ==0 && !$isLog &&  ($user->hasPermission('item_service_pr'))){ echo $optionActions;  }?></td>
										<?php if($user->hasPermission('item_service_overwrite') && $myreq->data()->status ==3 && !$isLog){ ?>
										<td  style='border-top:1px solid #ccc;' >

											<input class='txt-overwrite-price' data-id='<?php echo $item->id; ?>'  data-item_id='<?php echo $item->item_id; ?>' data-orig='<?php echo $total_price; ?>' placeholder='' type="text" value=''>
											<span class='help-block'>Enter Total To Overwrite Price</span>
										</td>
										<?php } ?>

									</tr>
									<?php
								}
							?>
							</tbody>
						</table>
						<hr>
					<div id='itemSpareContainer_test' style='display:none;'>
						<p><strong>Enter item used if there is any.</strong></p>
						<div class="row">
						<div class="col-md-4">
						<div class="form-group">
							<input type="text" class='form-control' id='spare_use'>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
							<input type="text" placeholder='Quantity' class='form-control' id='spare_qty'>
						</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
							<button class='btn btn-default' id='add_spare_use'>ADD</button>
						</div>
						</div>
						</div>
						<div id="no-more-tables">
						<table id='cart' class='table' style='font-size:1em'>
							<thead>
							<tr>
								<th>BARCODE</th>
								<th>ITEM CODE</th>
								<th>QTY</th>
								<th></th>
							</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
					</div>
					<?php
						$service_used_items = new Service_item_use();
						$used_items = $service_used_items->getUsedItems($id);
						$hasItemUsed = false;
						if($used_items){

								echo "<h4>Item used</h4>";
								echo "<table id='tbl-used-items' class='table'>";
								echo "<thead>";
								echo "<tr>";
								echo "<th>Barcode</th><th>Item Code</th><th>Quantity</th>";
								if(Configuration::thisCompany('cebuhiq')){
									echo "<th>Total</th>";

								}

								echo "</tr>";
								echo "</thead>";
								echo "<tbody>";
								$usedTotal= 0;
								foreach($used_items as $uitem){
									$prodItem = new Product($uitem->item_id);
									$uprice = $prodItem->getPrice($uitem->item_id);
									$usedTotal += $uprice->price * $uitem->qty;

									if($uitem->status == 0){
										$hasItemUsed = true;
									}

									echo "<tr data-id='".$uitem->id."' ><td style='border-top:1px solid #ccc;'>$uitem->item_code</td><td style='border-top:1px solid #ccc;'>$uitem->description</td><td style='border-top:1px solid #ccc;'>".formatQuantity($uitem->qty,true)."</td>";
									if(Configuration::thisCompany('cebuhiq')){
										$price_lbl = $uprice->price;
										 if($myreq->data()->status == 3){
											 $price_lbl = "<input type='text' class='service_price' value='".$uprice->price."'>";
										 } else if($myreq->data()->status == 4){
											 if($uitem->price_override != 0.00){
											    $price_lbl = $uitem->price_override;
											 }
										 }
										echo "<td style='border-top:1px solid #ccc;'>$price_lbl</td>";
									}
									echo "</tr>";
								}
								echo "</tbody>";
								echo "</table>";

								if($withoutWarranty) $totalCreditToClient += $usedTotal;
						}

					?>
					<input type="hidden" value='<?php echo json_encode($data_details); ?>' id='hid_details_data' >
					<?php if(false && $withPayment && $user->hasPermission('item_service_p') && !$isLog && $myreq->data()->status ==3 ){
					?>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<strong>Service Item:</strong>
								<input type="text" style='width:100%' class="selectitem" id='serviceItem' >
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<strong>Price:</strong>
								<input type="text" disabled id='servicePrice' class='form-control' value='0.00'>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<strong>Discount:</strong>
								<input type="text" class='form-control' id='serviceDiscount'>
							</div>
						</div>
					</div>
					<?php
					}
					?>
						<hr>
						<?php if(false &&  $withPayment && !$isLog && ($myreq->data()->status == 3 )) {
							?>
						<input type="hidden" id='hid_totalPayment' value='0'>
						<p>Total Payment: <strong id='totalPayment'>0.00</strong></p>
						<?php
						}?>

					<?php if($withCredit && !$isLog && ($myreq->data()->status == 3 )) {
							?>
						<p>Total Credited Amount:
							<strong style='display:none;' id='totalCreditedPayment'><?php echo number_format($totalCreditToClient,2); ?></strong>
							<input type="text" style='width:150px;display:inline-block;' class="form-control" disabled id='override_credited' value="<?php echo $totalCreditToClient; ?>">
						</p>
						<p>Enter refund amount (optional):
							<input type="text" style='width:150px;display:inline-block;' class="form-control" id='refund_amount' value="">
						</p>
						<?php
						}?>
						<?php if($withPayment &&  !$terminal_id && !$isLog && ($myreq->data()->status == 3)){
							?>

							<?php
						} ?>
						<div class="row">
						<div class="col-md-4">
						<?php

							$showPrint = true;
							if($is_aquabest && $myreq->data()->second_status != 2){
								$showPrint = false;
							}
							if($is_aquabest && $showPrint){
							?>
								<a target="_blank" href='../ajax/ajax_service_item.php?functionName=servicePrint&id=<?php echo $id; ?>' class='btn btn-default'>Print</a>

							<?php
							} else {
								if(!$user->hasPermission('p_credit_memo')){
								?>
								<button data-credit='0' class='btn btn-default' id='btnPrintCreditMemo'>Print</button>
								<?php
								}
							}
							if(Configuration::thisCompany('cebuhiq')){
								?>
								<?php if($user->hasPermission('print_scs')){
								?>
								<button class='btn btn-default' id='btnSCS'>SCS</button>
								<?php
								}?>
								<?php if($user->hasPermission('print_sar')){
								?>
								<button class='btn btn-default' id='btnSAR'>SAR</button>
								<?php
								}?>


								<?php

							}
						?>

						<?php

						if($user->hasPermission('p_credit_memo')){
							?>
								<button data-credit='1' class='btn btn-default' id='btnPrintCreditMemo'>Print Credit Memo</button>

							<?php
							}
						?>

						</div>

						<div class="col-md-8">
						<div class="text-right">
						<?php if($user->hasPermission('item_service_p') && !$isLog && ($myreq->data()->status == 3)){
								$btnwithp = 0;
								$btnwithc = 0;
								$btndis='';
								if($withPayment &&  $terminal_id){
									$btnwithp = 1;
								}
								if($withPayment &&  !$terminal_id){
									//$btndis = "display:none;";
								}
								if($withCredit){
									$btnwithc = 1;
								}
							?>
							<button style='<?php echo $btndis; ?>' data-withp='<?php echo $btnwithp; ?>' data-withc='<?php echo $btnwithc; ?>' data-credit_amount='<?php echo $totalCreditToClient; ?>' class='btn btn-primary' id='btnPayment'>Processed</button>
							<?php
						}
						?>
						</div>
						</div>
						</div>
						<?php
						 if($hasItemUsed && $user->hasPermission('item_service_p') && $isLog && ($myreq->data()->status == 4)){
						    ?>

						<div class="row">
						<div class="col-md-8"></div>
						<div class="col-md-4">
							<div class='panel panel-default'>
						    <div class='panel-body'>
								<strong>Additional Action</strong>
	<?php
							if(Configuration::getValue('c_trans') == 1)
						    {
						?>


							<div class="form-group">
								<input type="checkbox" id='chkWalkInApproval'>
									  <label for="chkWalkInApproval">
										For Walk In Approval
									</label>
							</div>



						<div class="form-group">
								<input type="checkbox" id='chkCashierTransaction'>
								  <label for="chkCashierTransaction">
									Cashier Transaction
								</label>
						</div>


						<div class="form-group">
								<select class='form-control' style='display:inline-block; width:150px; margin-right:5px;' name="is_service_item" id="is_service_item" >
									<option value="1">Service Item</option>
									<option value="0">Sales Item</option>
								</select>
						</div>


					<?php     } // end c_trans condition  ?>
					   <?php if(Configuration::thisCompany('cebuhiq'))
						    {
								?>


								<div class="form-group">
										<input type="checkbox" id='chkIssueServiceReceipt'>
										  <label for="chkIssueServiceReceipt">
											Issue SR
										</label>
								</div>
								<div class="form-group">
										<input type="checkbox" id='chkIssueTS'>
										  <label for="chkIssueTS">
											Issue TS
										</label>
								</div>


						<?php } // cebuhiq ?>
							<div class="form-group">
										 <button data-id='<?php echo $myreq->data()->id; ?>' class='btn btn-default' id='btnSubmitOrder'>Submit Order</button>
								</div>
							</div>
							</div>
						</div>
						</div>





							</div>

						 <?php  } // end user permission	?>






						<div class="form-group">
						<br>
						<?php

							if($myreq->data()->payment_id ==0 && !$isLog &&  ($user->hasPermission('item_service_pr'))){
								echo $btnProcessAction;

							}
							if($myreq->data()->payment_id == 0 && !$isLog && $myreq->data()->user_id == $user->data()->id){
							?>
							 <button id='cancelService' class='btn btn-danger'>Cancel</button>
							<?php

							}
						?>
						</div>
						</div>


					<input type="hidden" id='print_data' value='<?php echo json_encode(['main' => $data_main,'details'=>$data_details])?>'>

				<?php
			}
		}
	}

	function addChequeRemarks(){
		$id = Encryption::encrypt_decrypt('decrypt',Input::get('id'));
		$rem = Input::get('remarks');

		if(is_numeric($id)){
			$cheque = new Cheque($id);
			$remarks = $cheque->data()->remarks;
			if($remarks){
				$remarks = json_decode($remarks,true);
			} else {
				$remarks = [];
			}
			$remarks[] = ['date' => time(), 'remarks' => $rem];
			$cheque->update(array(
					'remarks' => json_encode($remarks)
			),$id);
		}

	}

	function getChequeRemarks(){
		$id = Encryption::encrypt_decrypt('decrypt',Input::get('id'));
		if(is_numeric($id)){
			$cheque = new Cheque($id);
			$remarks = $cheque->data()->remarks;
			?>
				<input id='cheque_id' type="hidden" value ='<?php echo  Encryption::encrypt_decrypt('encrypt',$id);?>'>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<input type="text" id='cheque_remarks' class='form-control' placeholder='Remarks'>
						</div>
					</div>
						<div class="col-md-6">
						<div class="form-group">
							<button class='btn btn-default' id='btnAddRemarks'>Add Remarks</button>
						</div>
					</div>
				</div>
			<?php
			if($remarks){
				$remarks = json_decode($remarks);
				?>
				<div id="no-more-tables">
				<table class='table'>
				<thead>
				<tr><th>Date</th><th>Remarks</th></tr>
				</thead>
				<tbody>
				<?php if(count($remarks)){
					foreach($remarks as $rem){
					?>
					<tr>
					<td data-title='Date'>
						<?php echo escape(date('m/d/Y',$rem->date));?>
					</td>
					<td data-title='Remarks'>
						<?php echo escape($rem->remarks);?>
					</td>
					</tr>
					<?php
					}
				}?>
				</tbody>
				</table>
				</div>
				<?php
			} else {
				echo "No remarks yet.";
			}

		}
	}
	function saveCreditName(){
		$id = Encryption::encrypt_decrypt('decrypt',Input::get('id'));
		$name = Input::get('name');
		if(is_numeric($id) && $name){
			$member_credit = new  Member_credit();
			$member_credit->update(array('name' => $name),$id);
			echo "Updated successfully";
		}
	}
	function saveConfigurations(){
		$form_config = json_decode(Input::get('form_config'),true);
		$configuration = new Configuration();
		$user = new User();
		$allb = [];
		$allb2 = [];
		$allb3 = [];
		Log::addLog(
				$user->data()->id,
				$user->data()->company_id,
				"Update configuration",
				'ajax_accounting.php'
		);

		foreach($form_config as $input){
			if($input['name'] == 'branch_show_price'){
				$allb[] = $input['value'];
				continue;
			}
			if($input['name'] == 'hide_price_inv'){
				$allb2[] = $input['value'];
				continue;
			}
			if($input['name'] == 'can_add_inventory'){
				$allb3[] = $input['value'];
				continue;
			}
			$check = $configuration->configExists($input['name'],$user->data()->company_id);
			if($check){
				// update
				$configuration->updateValue($input['name'],$input['value'],$user->data()->company_id);
			} else {
				// insert
				$configuration->create(array(
					'company_id' => $user->data()->company_id,
					'name' => $input['name'],
					'value' => $input['value'],
					'is_active' => 1,
				));
			}
		}
		if($allb){
			$check = $configuration->configExists('branch_show_price',$user->data()->company_id);
			$allb = implode(",",$allb);
			if($check){
				// update
				$configuration->updateValue('branch_show_price',$allb,$user->data()->company_id);
			} else {
				// insert
				$configuration->create(array(
					'company_id' => $user->data()->company_id,
					'name' => 'branch_show_price',
					'value' => $allb,
					'is_active' => 1,
				));
			}
		}else {
		$configuration->updateValue('branch_show_price','',$user->data()->company_id);
		}
		if($allb2){
			$check = $configuration->configExists('hide_price_inv',$user->data()->company_id);
			$allb2 = implode(",",$allb2);
			if($check){
				// update
				$configuration->updateValue('hide_price_inv',$allb2,$user->data()->company_id);
			} else {
				// insert
				$configuration->create(array(
					'company_id' => $user->data()->company_id,
					'name' => 'hide_price_inv',
					'value' => $allb2,
					'is_active' => 1,
				));
			}
		}else {
		$configuration->updateValue('hide_price_inv','',$user->data()->company_id);
		}
		if($allb3){
			$check = $configuration->configExists('can_add_inventory',$user->data()->company_id);
			$allb3 = implode(",",$allb3);
			if($check){
				// update
				$configuration->updateValue('can_add_inventory',$allb3,$user->data()->company_id);
			} else {
				// insert
				$configuration->create(array(
					'company_id' => $user->data()->company_id,
					'name' => 'can_add_inventory',
					'value' => $allb3,
					'is_active' => 1,
				));
			}
		} else {
		$configuration->updateValue('can_add_inventory','',$user->data()->company_id);
		}
		echo "Updated successfully.";

	}
	function sendPaymentService(){
		$user = new User();
		$payment_cash = Input::get('payment_cash');
		$payment_con = Input::get('payment_con');
		$payment_con_freebies = Input::get('payment_con_freebies');
		$payment_member_credit = Input::get('payment_member_credit');
		$payment_credit = Input::get('payment_credit');
		$payment_bt = Input::get('payment_bt');
		$payment_cheque = Input::get('payment_cheque');
		$terminal_id = Input::get('terminal_id');
		$service_id = Encryption::encrypt_decrypt('decrypt',Input::get('hid_det_id'));
		$service_item_list = Input::get('service_item_list');
		$serviceItem = Input::get('serviceItem');
		$serviceDiscount = Input::get('serviceDiscount');
		$invoice = Input::get('invoice');
		$member_id = Input::get('member_id');
		$credit_to_client = Input::get('credit_to_client');
		$service_details = Input::get('hid_details_data');
		$item_service = new Item_service_request();
		$payment = new Payment();
		if(!$terminal_id) {
			die("Please set up terminal first.");
		}
		if(!$invoice){
			die("Please set up terminal first.");
		}
		if(!$service_id){
			die("Invalid data on this request.");
		}
		// create payment
		$scompany =$user->data()->company_id;
		$payment->create(array(
			'created' => time(),
			'company_id' => $scompany,
			'is_active' => 1
		));

		$payment_lastid = $payment->getInsertedId();



		$cashier_id = $user->data()->id;
		$station_id=0;

		$sdr ='';
		$sinv = $invoice;
		$sir =''; // change later
		$sdr = ($sdr) ? 'Dr: '.$sdr:'';
		$sinv = ($sinv) ? 'Inv: '.$sinv:'';
		$sir = ($sir) ? 'Ir: '.$sir:'';
		$sdate = time();
		$sales = new Sales();
		$terminal = new Terminal();
		$terminal_mon = new Terminal_mon();
		if($invoice){
			$terminal->update(array(
				'modified' => strtotime(date('Y/m/d H:i:s')),
				'invoice' =>$invoice
			), $terminal_id);
		}
		if($service_id){
			$item_service->update(array(
				'status' => 4,
				'payment_id' => $payment_lastid
			),$service_id);
		}
		if($credit_to_client){ // with credit amount
			creditToMember($service_id,$member_id,$credit_to_client,$service_details);
		}
		$serviceItemCls = new Product($serviceItem);
		$serviceItemPrice = $serviceItemCls->getPrice($serviceItem);
		$now = time();
		$serviceDiscount = ($serviceDiscount) ? : 0;
			$sales->create(array(
				'terminal_id' => $terminal_id,
				'invoice' => $invoice,
				'dr' => 0,
				'ir' => 0,
				'item_id' => $serviceItem,
				'price_id' =>$serviceItemPrice->id,
				'qtys' => 1,
				'discount' => number_format($serviceDiscount,2, '.', ''),
				'store_discount' => 0,
				'adjustment' => 0,
				'company_id' => $user->data()->company_id,
				'cashier_id' => $cashier_id,
				'sold_date' => $now,
				'payment_id' => $payment_lastid,
				'member_id' => 0,
				'station_id' => 0,
				'sales_type' => 0,
				'warranty' => 0
			));
			$service_item_list = json_decode($service_item_list);
			if(count($service_item_list)){
				foreach($service_item_list as $perservice){
					$serviceItemCls = new Product($perservice->sp_id);
					$serviceItemPrice = $serviceItemCls->getPrice($perservice->sp_id);
					$sales->create(array(
							'terminal_id' => $terminal_id,
							'invoice' => $invoice,
							'dr' => 0,
							'ir' => 0,
							'item_id' => $perservice->sp_id,
							'price_id' =>$serviceItemPrice->id,
							'qtys' => $perservice->qty,
							'discount' => 0,
							'store_discount' => 0,
							'adjustment' => 0,
							'company_id' => $user->data()->company_id,
							'cashier_id' => $cashier_id,
							'sold_date' => $now,
							'payment_id' => $payment_lastid,
							'member_id' => 0,
							'station_id' => 0,
							'sales_type' => 0,
							'warranty' => 0
						));
				}
			}
		if($payment_credit){
			// insert credit
			$payment_credit = json_decode($payment_credit,true);
			$credit = new Credit();
			$total_amount_cc = 0;

			foreach($payment_credit as $c){
				$total_amount_cc += $c['amount'];
				$credit->create(array(
					'card_number' => $c['credit_number'],
					'amount' => $c['amount'],
					'bank_name'=>$c['bank_name'],
					'lastname'=>$c['lastname'],
					'firstname'=>$c['firstname'],
					'middlename'=>$c['middlename'],
					'company'=>$c['comp'],
					'address'=>$c['add'],
					'zip' => $c['postal'],
					'contacts' => $c['phone'],
					'email' => $c['email'],
					'remarks' => $c['remarks'],
					'card_type' => $c['card_type'],
					'trace_number' => $c['trace_number'],
					'approval_code' => $c['approval_code'],
					'date' =>  strtotime($c['date']),
					'is_active' => 1,
					'created' => $sdate,
					'modified' => $sdate,
					'payment_id' => $payment_lastid
				));
			}

			$prevamount = $terminal->getTAmount($terminal_id,2);
			$prevamount = ($prevamount->t_amount_cc) ? $prevamount->t_amount_cc:0;
			$to_amount = $total_amount_cc + $prevamount;

			$terminal->update(array(
				't_amount_cc' => $to_amount
			),$terminal_id);
			$terminal_mon->create( array(
				'terminal_id' => $terminal_id,
				'user_id' => $cashier_id,
				'from_amount' =>$prevamount,
				'amount' =>$total_amount_cc,
				'to_amount'=>$to_amount,
				'status' => 1,
				'remarks' => "POS $sinv $sdr $sir",
				'is_active' => 1,
				'company_id' => $scompany,
				'p_type' => 2,
				'created' => $sdate
			));
		}
		if($payment_bt){
			// insert bank transfer
			$payment_bt = json_decode($payment_bt,true);
			$bank_transfer = new Bank_transfer();
			$total_amount_bt = 0;
			foreach($payment_bt as $c){
				$total_amount_bt += $c['amount'];
				$bank_transfer->create(array(
					'bankfrom_account_number' => $c['credit_number'],
					'amount' => $c['amount'],
					'bankfrom_name'=>$c['bank_name'],
					'bankto_account_number' => $c['bt_bankto_account_number'],
					'bankto_name' => $c['bt_bankto_name'],
					'lastname'=>$c['lastname'],
					'firstname'=>$c['firstname'],
					'middlename'=>$c['middlename'],
					'company'=>$c['comp'],
					'address'=>$c['add'],
					'zip' => $c['postal'],
					'contacts' => $c['phone'],
					'date' =>  strtotime($c['date']),
					'is_active' => 1,
					'created' => $sdate,
					'modified' => $sdate,
					'payment_id' => $payment_lastid
				));
			}
			$prevamount = $terminal->getTAmount($terminal_id,4);
			$prevamount = ($prevamount->t_amount_bt) ? $prevamount->t_amount_bt:0;
			$to_amount = $total_amount_bt + $prevamount;

			$terminal->update(array(
				't_amount_bt' => $to_amount
			),$terminal_id);
			$terminal_mon->create( array(
				'terminal_id' => $terminal_id,
				'user_id' => $cashier_id,
				'from_amount' =>$prevamount,
				'amount' =>$total_amount_bt,
				'to_amount'=>$to_amount,
				'status' => 1,
				'remarks' => "POS $sinv $sdr $sir",
				'is_active' => 1,
				'company_id' => $scompany,
				'p_type' => 4,
				'created' => $sdate
			));
		}
		if($payment_cheque){
			// insert cheque
			$payment_cheque = json_decode($payment_cheque,true);
			$cheque = new Cheque();
			$total_amount_ch = 0;
			foreach($payment_cheque as $c){
				$total_amount_ch += $c['amount'];
				$cheque->create(array(
					'check_number' => $c['cheque_number'],
					'amount' => $c['amount'],
					'bank'=>$c['bank_name'],
					'payment_date' => strtotime($c['date']),
					'lastname'=>$c['lastname'],
					'firstname'=>$c['firstname'],
					'middlename'=>$c['middlename'],
					'contacts' => $c['phone'],
					'is_active' => 1,
					'created' => $sdate,
					'modified' => $sdate,
					'payment_id' => $payment_lastid
				));
			}
			$prevamount = $terminal->getTAmount($terminal_id,3);
			$prevamount = ($prevamount->t_amount_ch) ? $prevamount->t_amount_ch:0;
			$to_amount = $total_amount_ch + $prevamount;

			$terminal->update(array(
				't_amount_ch' => $to_amount
			),$terminal_id);
			$terminal_mon->create( array(
				'terminal_id' => $terminal_id,
				'user_id' => $cashier_id,
				'from_amount' =>$prevamount,
				'amount' =>$total_amount_ch,
				'to_amount'=>$to_amount,
				'status' => 1,
				'remarks' => "POS $sinv $sdr $sir",
				'is_active' => 1,
				'company_id' => $scompany,
				'p_type' => 3,
				'created' => $sdate
			));
		}
		if($payment_cash){
			// insert cash
			$pcash = new Cash();
			$total_amount = $payment_cash;
			$pcash->create(array(
				'amount' =>$payment_cash,
				'is_active' => 1,
				'created' => $sdate,
				'modified' => $sdate,
				'payment_id' => $payment_lastid
			));
			$prevamount = $terminal->getTAmount($terminal_id,1);
			$prevamount = ($prevamount->t_amount) ? $prevamount->t_amount:0;
			$to_amount = $total_amount + $prevamount;

			$terminal->update(array(
				't_amount' => $to_amount
			),$terminal_id);
			$terminal_mon->create( array(
				'terminal_id' => $terminal_id,
				'user_id' => $cashier_id,
				'from_amount' =>$prevamount,
				'amount' =>$total_amount,
				'to_amount'=>$to_amount,
				'status' => 1,
				'remarks' => "POS $sinv $sdr $sir",
				'is_active' => 1,
				'company_id' => $scompany,
				'p_type' => 1,
				'created' => $sdate
			));
		}
		if($payment_member_credit){
			// insert cash
			$pcredit = new Member_credit();
			$pcredit->create(array(
				'amount' =>$payment_member_credit,
				'is_active' => 1,
				'created' => $sdate,
				'modified' => $sdate,
				'payment_id' => $payment_lastid,
				'member_id' => $member_id
			));
		}
		if($payment_con){
			// insert cash
			$pcon = new Payment_consumable();
			$pcon->create(array(
				'amount' =>$payment_con,
				'is_active' => 1,
				'created' => $sdate,
				'modified' => $sdate,
				'payment_id' => $payment_lastid,
				'member_id' => $member_id
			));


			$mem = new Member();
			$mycon = $mem->getMyConsumableAmount($member_id);
			if($mycon){

				foreach($mycon as $c){
					if($payment_con) {
						$notvalid = $mem->getNotYetValidCheque($c->payment_id);
						if($notvalid->cheque_amount) {
							$validamount = $c->amount - $notvalid->cheque_amount;
							$notv = $notvalid->cheque_amount;
						} else {
							$validamount = $c->amount;
							$notv = 0;
						}
						$toupdate = new Consumable_amount();
						if($validamount > $payment_con) {
							$leftamount = ($validamount - $payment_con) + $notv ;
							$payment_con =0;
							$toupdate->update(array('amount' => $leftamount, 'modified' => time()), $c->id);
						} else {
							$leftamount = $notv;
							$toupdate->update(array('amount' => $leftamount, 'modified' => time()), $c->id);
							$payment_con = $payment_con - $validamount;
						}
					}
				}
			}
		}
		if($payment_con_freebies){
			// insert cash
			$pcon = new Payment_consumable_freebies();
			$pcon->create(array(
				'amount' =>$payment_con_freebies,
				'is_active' => 1,
				'created' => $sdate,
				'modified' => $sdate,
				'payment_id' => $payment_lastid,
				'member_id' => $member_id
			));


			$mem = new Member();
			$mycon = $mem->getMyConsumableFreebies($member_id);
			if($mycon){

				foreach($mycon as $c){
					if($payment_con_freebies) {

						$validamount = $c->amount;

						$toupdate = new Consumable_freebies();
						if($validamount > $payment_con_freebies) {
							$leftamount = ($validamount - $payment_con_freebies);
							$payment_con_freebies =0;
							$toupdate->update(array('amount' => $leftamount, 'modified' => time()), $c->id);
						} else {
							$leftamount = 0;
							$toupdate->update(array('amount' => $leftamount, 'modified' => time()), $c->id);
							$payment_con_freebies = $payment_con_freebies - $validamount;
						}
					}
				}
			}
		}
	}
	function saveSparepartsService(){
		$user = new User();
		 $id = Encryption::encrypt_decrypt('decrypt',Input::get('id'));
		$data = json_decode(Input::get('dt'));
		$need_sp = false;
		if(count($data)){
			$all_sp_to_deduct = [];
			$valid_sp_item = true;
			foreach($data as $item){
				$sp = json_decode($item->sp_arr);

				if(count($sp)){
					foreach($sp as $s){
						$raw_id = Encryption::encrypt_decrypt('decrypt',$s->raw_id);
						$raw_qty = $s->raw_qty;
						$raw_item_code = $s->raw_item_code;
						if($raw_id && $raw_qty){
							$need_sp= true;
							// get racking

							$racking =  inventory_racking(0,$raw_qty,$raw_id,$user->data()->branch_id,false);
							if($racking['insufficient']){
								$valid_sp_item = false;
							}
							$all_sp_to_deduct[$item->item_id][] = ['item_id' => $raw_id,'raw_item_code'=>$raw_item_code, 'qty' => $raw_qty, 'racking' =>$racking['racking'] ];

						}
					}
				}
			}
				if($valid_sp_item){
					$update_service_det = new Item_service_details();
					$inventory = new Inventory();
					$inv_mon = new Inventory_monitoring();
					if(count($all_sp_to_deduct)){
						foreach($all_sp_to_deduct as $item_id => $det){
							 $item_id = Encryption::encrypt_decrypt('decrypt',$item_id);

							foreach($det as $d){
									$d_raw_id = $d['item_id'];
									$d_raw_qty =$d['qty'];
									$d_racking = json_decode($d['racking']);
									foreach($d_racking as $rack){
										 // deduct inv
										    $rack_id = $rack->rack_id;
										    $rack_qty = $rack->qty;
											if($inventory->checkIfItemExist($d_raw_id,$user->data()->branch_id,$user->data()->company_id,$rack_id)){
											$curinventoryFrom = $inventory->getQty($d_raw_id,$user->data()->branch_id,$rack_id);
											$currentqty = $curinventoryFrom->qty;
											$inventory->subtractInventory($d_raw_id,$user->data()->branch_id,$rack_qty,$rack_id);
										} else {
											$currentqty = 0;
										}
											// monitoring
											$newqtyFrom = $currentqty - $rack_qty;
											$inv_mon->create(array(
												'item_id' =>$d_raw_id,
												'rack_id' => $rack_id,
												'branch_id' => $user->data()->branch_id,
												'page' => 'ajax/ajax_query2.php',
												'action' => 'Update',
												'prev_qty' => $currentqty,
												'qty_di' => 2,
												'qty' => $rack_qty,
												'new_qty' => $newqtyFrom,
												'created' => time(),
												'user_id' => $user->data()->id,
												'remarks' => 'Deduct spare part from service. Service# ' . $id,
												'is_active' => 1,
												'company_id' => $user->data()->company_id
											));
									}
							}
							// update service details
							$needed_spareparts = json_encode($det);
							$update_service_det->updateDetNeededSp($id,$item_id,$needed_spareparts);
						}
					} else {

					}

					echo "Request process successfully.";
				} else {
				echo "Invalid request. Please check item inventory.";
				}
		} else {
			echo "Invalid request.";
		}
	}

	function itemServiceRequest(){
		$invoice = Input::get('invoice');
		$dr = Input::get('dr');
		$ir = Input::get('ir');
		$invoice = ($invoice)? $invoice : 0;
		$dr = ($dr)? $dr : 0;
		$ir = ($ir)? $ir : 0;
		$branch_id = Input::get('branch_id');
		$service_type_id = Input::get('service_type_id');
		$service_remarks = Input::get('service_remarks');
		$contact_person = Input::get('contact_person');
		$contact_number = Input::get('contact_number');
		$contact_address = Input::get('contact_address');

		$technician_id = Input::get('technician_id');
		$member_id = Input::get('member_id');
		$station_id = Input::get('station_id');
		$serviceType = Input::get('serviceType');
		$backload_ref_id = Input::get('backload_ref_id');
		$client_po = Input::get('client_po');
		$member_id = ($member_id) ? $member_id : 0;
		$home_schedule = (Input::get('home_schedule')) ? strtotime(Input::get('home_schedule')) : 0;
		$pullout_schedule = (Input::get('pullout_schedule')) ? strtotime(Input::get('pullout_schedule')) : 0;

		$backload_ref_id = ($backload_ref_id) ? $backload_ref_id : '';
		$client_po = ($client_po) ? $client_po : '';

		if($serviceType == 2){
			$initstatus = 10;
			$reqstat = 1;
	 	} else if ($serviceType == 3){
	 	    if(Configuration::thisCompany('cebuhiq')){
	 	         $initstatus = 12;
	 	        $reqstat = 2;
	 	    } else {
	 	        $initstatus = 10;
	 	        $reqstat = 1;
	 	    }

	 	} else {
	    	$initstatus = 1;
	    	$reqstat = 2;
	 	}
		$req = json_decode(Input::get('req'));
		if(count($req) > 0){
			$user = new User();
			// create request
			$new_req = new Item_service_request();
			$now = time();
			$new_req->create(array(
				'branch_id' => $branch_id,
				'service_type_id' => $service_type_id,
				'company_id' => $user->data()->company_id,
				'created' => $now,
				'modified' => $now,
				'status' => $reqstat,
				'is_active' => 1,
				'pullout_schedule' => $pullout_schedule,
				'home_repair' => $home_schedule,
				'receive_date' => 0,
				'member_id' => $member_id,
				'station_id' => $station_id,
				'user_id' => $user->data()->id,
				'remarks' => '',
				'invoice' => $invoice,
				'dr' => $dr,
				'ir' => $ir,
				'request_type' => $serviceType,
				'technician_id' => $technician_id,
				'history_status' => $reqstat,
				'remarks' => $service_remarks,
				'contact_person' => $contact_person,
				'contact_number' => $contact_number,
				'contact_address' => $contact_address,
				'backload_ref_number' => $backload_ref_id,
				'client_po' => $client_po,
			));
		// last id
		$lastid = $new_req->getInsertedId();
		// create details
		$new_details = new Item_service_details();
			foreach($req as $item){
				$unit_qty = $item->unit_qty;
				$unit_name = $item->unit_name;
				$unit_name = ($unit_name) ? $unit_name : '';
				$unit_qty = ($unit_qty) ? $unit_qty : '';
				$new_details->create(array(
					'service_id' => $lastid,
					'item_id' => $item->item_id,
					'qty' => $item->qty,
					'remarks' => $item->remarks,
					'company_id' => $user->data()->company_id,
					'is_active' =>1,
					'created' =>$now,
					'modified' =>$now,
					'sp_needed' =>'',
					'is_done' =>$initstatus,
					'status_history' => $initstatus,
					'unit_qty' => $unit_qty,
					'unit_name' => $unit_name,
				));

			}
			$is_aquabest = Configuration::isAquabest();
			if($is_aquabest){
				$log = new Service_date_log();
					$log->create(
						[
							'dt' => time(),
							'service_id' => $lastid,
							'status' => 0,
							'remarks' => '',
							'user_id' => $user->data()->id,
						]
					);
			}
			echo "Request submitted successfully.";
		} else {
			echo "Error processing your request.";
		}

	}
	function getItemForService(){
		$invoice = Input::get('invoice');
		$dr = Input::get('dr');
		$ir = Input::get('ir');
		$sales = new Sales();
		$user = new User();
		$sales_list = $sales->getSalesInvDrIr($user->data()->company_id,$invoice,$dr,$ir);
		if($sales_list){
			$arr = [];
			foreach($sales_list as $item){
				$arr[]= $item;
			}
			echo json_encode($arr);
		} else {
			echo '[]';
		}

	}
	function getPettyExpense(){
		$user = new User();
		$branch_id = Input::get('branch_id');
		$request_id = Input::get('request_id');
		if(!$branch_id){
			$branch_id = $user->data()->branch_id;
		}
		if(!$request_id){
			$request_id = 0;
			$starting = false;
		} else {
			$request_id = Encryption::encrypt_decrypt('decrypt',$request_id);
			$myreq  = new Pettycash_request($request_id);
			if(isset($myreq->data()->is_starting) && $myreq->data()->is_starting == 1){
			$starting = true;
			} else{
			$starting = false;
			}
		}

		if(!$starting){

		$petty_breakdown = new Pettycash_breakdown();
		$pettylist = $petty_breakdown->getBreakdown($branch_id,$request_id);
			if($pettylist){
				$totalExpense = 0;
				?>
				<div id="no-more-tables">
					<table class="table table_border_top">
						<thead>
						<tr>
							<th>Date</th>
							<th>Account Title</th>
							<th>Description</th>
							<th>Amount</th>
							<th></th>
						</tr>
						</thead>
						<tbody>
							<?php
								foreach($pettylist as $petty){
									$totalExpense += $petty->amount;
									if($petty->account_title_id){
										$acc_cls = new Account_title($petty->account_title_id);
										$acc_name =$acc_cls->data()->name;
									} else {
										$acc_name ='';
									}


								?>
									<tr>
										<td  data-title='Date'><?php echo escape(date('F d, Y',$petty->dt)); ?></td>
										<td  data-title='Account title'><?php echo escape($acc_name); ?></td>
										<td  data-title='Description'><?php echo escape($petty->description); ?></td>
										<td  data-title='Amount'><?php echo escape(number_format($petty->amount,2)); ?></td>
										<td>
											<?php if($request_id == 0){
												?>
												<button data-id="<?php echo $petty->id; ?>" class='btn btn-sm btn-danger btnDelete'>Delete</button>
												<?php
											}?>

										</td>
									</tr>
								<?php
								}
							?>
						</tbody>
					</table>
				<hr>
				<p><strong>Total Expense: </strong> <?php echo number_format($totalExpense,2); ?></p>
				<?php
				}
			} else {
				?>
				<p class='text-danger'>Requesting starting petty cash.</p>
				<?php
			}
			if($request_id){
			// check auth
				?>
				<hr>
				<div class='text-right'>
				<?php
				if($myreq->data()->status == 1 && $user->hasPermission('pettycash_m')){
				?>

					<button data-id='<?php echo  Encryption::encrypt_decrypt('encrypt',$request_id);?>' class='btn btn-primary' id='btnApprovedPetty'>Approve</button>
					<button data-id='<?php echo  Encryption::encrypt_decrypt('encrypt',$request_id);?>' class='btn btn-danger' id='btnReturnPetty'>Return</button>
				<?php
				}
				?>

				</div>
				<?php

			}


	}
	function returnPettycash(){
		$user = new User();
		$id = Input::get('id');
		$id = Encryption::encrypt_decrypt('decrypt',$id);
		if(is_numeric($id)){
				// update breakdown request id to 0
				$petty_breakdown = new Pettycash_breakdown();
				$petty_breakdown->updatePettyBreakdown($user->data()->branch_id,0,$id);
				// delete request
				$petty_request = new Pettycash_request();
				$petty_request->update(array('is_active'=>0),$id);
				echo "Request has been returned successfully.";

		}
	}
	function approvedPettycash(){
		$user = new User();
		$id = Input::get('id');
		$id = Encryption::encrypt_decrypt('decrypt',$id);
		if(is_numeric($id)){
			 $petty_request = new Pettycash_request($id);
			 $now = time();
			 if($petty_request->data()->status == 1){
				 $petty_holder = new Pettycash_holder();
				 $getholder = $petty_holder->getHolder($user->data()->company_id, $petty_request->data()->branch_id);
				 if(!$getholder){
					 $petty_holder->create(array(
					    'company_id' => $user->data()->company_id,
					    'branch_id' => $petty_request->data()->branch_id,
					    'amount' => 0,
					    'created' => $now,
					    'modified' => $now,
						'is_active' => 1,
						'user_id' =>  $petty_request->data()->user_id
					 ));
				 }
				 $petty_holder->addPettycash(
					 $user->data()->company_id,
					 $petty_request->data()->branch_id,
					 $petty_request->data()->amount,$user->data()->id,
					 "Replenish petty cash. Request id # ".$id
					 );

				 $petty_request->update(array('status' => 2) , $id);
				echo "Petty cash request approved successfully.";
			 }
		}
	}
	function addPettyExpense(){
		$user = new User();
		$branch_id = $user->data()->branch_id;
		$desc = Input::get('desc');
		$amount = Input::get('amount');
		$dt = Input::get('dt');
		$account_title_id = Input::get('account_title_id');
		if(is_numeric($amount) && $desc && $dt){
			$petty_breakdown = new Pettycash_breakdown();
			$petty_holder = new Pettycash_holder();
			$petty_breakdown->create(array(
				'company_id' => $user->data()->company_id,
				'branch_id' =>$branch_id,
				'amount' => $amount,
				'description' => $desc,
				'dt' => strtotime($dt),
				'account_title_id' => $account_title_id,
				'is_active' => 1,
				'created' => time(),
				'modified' => time()
			));
			$petty_holder->deductPettycash($user->data()->company_id,$branch_id,$amount,$user->data()->id,$desc);
			// deduct to petty cash

			echo "Expense added successfully.";
		}
	}
	function whOrderLastTenDays(){
		$branch_id = Input::get('branch_id');
		$user = new User();
		$order = new Wh_order();
		$orderlist = $order->getOrderCountLastTenDays($user->data()->company_id,$branch_id);
		$arr = [];
		if($orderlist) {
			foreach($orderlist as $s) {
				$obj['y'] = date('M d, Y', strtotime($s->dt));
				$obj['a'] = $s->total_count;
				array_push($arr, $obj);
			}
			echo json_encode($arr);
		} else {
			echo json_encode(array('error' => true));
		}

	}
	function topAgentOrder() {

		$branch_id = Input::get('branch_id');
		$whorder = new Wh_order();
		$user = new User();
		// base on branch
		$list = $whorder->topAgentOrder($user->data()->company_id,$branch_id);

		$arr = [];

		if($list) {
			foreach($list as $bb) {
				$obj['label'] = ucwords($bb->lastname . ", " . $bb->firstname);
				$obj['value'] = $bb->total_count;
				array_push($arr, $obj);
			}
		}
		if($arr) {
			echo json_encode($arr);
		} else {
			echo json_encode(array('error' => true));
		}
	}
	function topMemberOrder() {

		$branch_id = Input::get('branch_id');
		$whorder = new Wh_order();
		$user = new User();
		// base on branch
		$list = $whorder->topMemberOrder($user->data()->company_id,$branch_id);

		$arr = [];

		if($list) {
			foreach($list as $bb) {
				if($bb->lastname){
					$obj['label'] = ucwords($bb->lastname . ", " . $bb->firstname);
					$obj['value'] = $bb->total_count;
					array_push($arr, $obj);
				}

			}
		}
		if($arr) {
			echo json_encode($arr);
		} else {
			echo json_encode(array('error' => true));
		}
	}
	function whPendingOrders(){
		$branch_id = Input::get('branch_id');
		$whorder = new Wh_order();
		$user = new User();
		// base on branch
		$list = $whorder->getPendingOrders($user->data()->company_id,$branch_id);

		$arr = [];

		if($list) {
		$arrType = ['','For Approval','Shipping','Warehouse','Deliveries','Declined'];
			foreach($list as $bb) {
				$obj['y'] = $arrType[$bb->status];
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
	function approveMemberTerms(){
		$id = Input::get('id');
		$id = Encryption::encrypt_decrypt('decrypt',$id);
		if(is_numeric($id)){
			$member_terms = new Member_term($id);
			$member_terms->updateSameTypAndQty($member_terms->data()->member_id,$member_terms->data()->item_id,$member_terms->data()->qty,$member_terms->data()->type);
			$user = new User();
			$member_terms->update(array('status' => 2,'approval_id'=>$user->data()->id),$id);
			// update prev terms with same type and qty

			// check current order for approval
			$adjustment = $member_terms->data()->adjustment;
			$whdet = new Wh_order();
			$details = $whdet->getMemberOrderWh($member_terms->data()->member_id,1,$member_terms->data()->item_id);
			if($details){
			$whorderdet = new Wh_order_details();
				foreach($details as $det){

					$qty = $det->qty;
					$memberTerms = new Member_term();
					$alladj = 0;
					$memadj =$memberTerms->getAdjustment($member_terms->data()->member_id,$member_terms->data()->item_id);
					if(count($memadj)){
						foreach($memadj as $m){
							$madj = $m->adjustment;

							if($m->type == 1){ // for every
								if($qty < 1 && $qty != 0){
							    if($m->qty == 1){
							      $x = $qty / $m->qty;
							    } else {
							          $x = 0;
							    }
							   } else {
							        $x = floor($qty / $m->qty);
							   }

								$madj = $madj * $x;
								$alladj += $madj;

							} else if ($m->type == 2){ // above qty
								if($qty >= $m->qty){
									if($m->discount_type == 0){
										$alladj += $madj;
									} else {
											$madj = $madj * $qty;
											$alladj += $madj;
									}

								}
							}
						}
					}


					$whorderdet->update(['member_adjustment' => $alladj],$det->id);

				}
				Log::addLog($user->data()->id,$user->data()->company_id,"Approve Member Terms ID $id","ajax_query2.php");
			}
			echo "Terms approved successfully.";
		}

	}
	function declineMemberTerms(){
		$id = Input::get('id');
		$id = Encryption::encrypt_decrypt('decrypt',$id);
		if(is_numeric($id)){

			$user = new User();
			$member_terms = new Member_term();
			$member_terms->update(array('status' => 3),$id);
			Log::addLog($user->data()->id,$user->data()->company_id,"Decline Member Terms ID $id","ajax_query2.php");
			echo "Terms declined successfully.";

		}
	}
	function requestMemberTerms(){
		 $member_id = Input::get('member_id');
		 $is_all = Input::get('is_all');
		 $item_id = Input::get('item_id');
		 $adjustment = Input::get('adjustment');
		 $terms = Input::get('terms');
		 $qty = Input::get('qty');
		 $type = Input::get('type');
		 $remarks = Input::get('remarks');
		 $discount_type = Input::get('discount_type');
		 $transaction_type = Input::get('transaction_type');

		if((is_numeric($member_id) || $is_all)&& is_numeric($item_id) && is_numeric($adjustment) && is_numeric($terms) && is_numeric($qty) && is_numeric($type)){
			$now = time();
			$member = new Member_term();
			 $user = new User();
			 if($is_all) $member_id = -1;
			$member->create(
				array(
					'member_id' => $member_id,
					'user_id' => $user->data()->id,
					'company_id' => $user->data()->company_id,
					'branch_id' => $user->data()->branch_id,
					'qty' => $qty,
					'item_id' => $item_id,
					'adjustment' => $adjustment,
					'type' => $type,
					'discount_type' => $discount_type,
					'transaction_type' => $transaction_type,
					'terms' => $terms,
					'is_active' => 1,
					'status' => 1,
					'remarks' => $remarks,
					'created' => $now,
					'modified' => $now
				)
			);
			echo "Request completed.";
		} else {
			echo "The data on your request is invalid.";
		}
	}
	function getTrucks(){
		$user = new User();
		$truck = new Truck();
		$trucks = $truck->get_active('trucks',array('company_id','=',$user->data()->company_id));
		$helpers= $truck->get_active('delivery_helpers',array('company_id','=',$user->data()->company_id));
		$drivers= $truck->get_active('drivers',array('company_id','=',$user->data()->company_id));
		$member_id = 0;
		$user_id = 0;
		$branch_id = 0;
		if($user->hasPermission('wh_agent')){
			$user_id = $user->data()->id;
		}
		if($user->hasPermission('wh_member')){
			$member_id = $user->data()->member_id;
		}
		if(!$user->hasPermission('inventory_all')){
			$branch_id = $user->data()->branch_id;
		}

		$whlog = new Wh_order();
		$fromService = 0;

		$count = $whlog->countRecord($user->data()->company_id,$search='',$b=0,$member_id,4,$user_id,$from=0,$to=0,0,$branch_id,0); // del
		$count_pickup = $whlog->countRecord($user->data()->company_id,$search='',$b=0,$member_id,4,$user_id,$from=0,$to=0,1,$branch_id,0); //  pickup
		$count_service = $whlog->countRecord($user->data()->company_id,$search='',$b=0,$member_id,4,$user_id,$from=0,$to=0,0,$branch_id,1); //  pickup

		$all = ['trucks' => $trucks,'helpers' => $helpers,'drivers' => $drivers,'count_del' => $count->cnt,'countpickup'=>$count_pickup->cnt,'countservice' => $count_service->cnt];
		$barcodeClass = new Barcode();
		$allDocFormat = $barcodeClass->getFormats($user->data()->company_id);

		$docFormatArr = [];

		foreach($allDocFormat as $doc_format){
			$doc_format->family = strtolower($doc_format->family);
			$all[$doc_format->family] = $doc_format->styling;
		}

		$byBranch = $barcodeClass->getFormatsByBranch($user->data()->branch_id);

		if($byBranch){

			foreach($byBranch as $newlayout){
				$newlayout->family = strtolower($newlayout->family);
				unset($all[$newlayout->family]);
				$all[$newlayout->family] = $newlayout->styling;
			}

		}

		$byUser = $barcodeClass->getFormatsByUser($user->data()->id);

		if($byUser){
			foreach($byUser as $u){
				$u->family = strtolower($u->family);
				unset($all[$u->family]);
				$all[$u->family] = $u->styling;

			}
		}

		echo json_encode($all);
	}
	function creditDetails(){
		$id = Input::get('id');
		$id = Encryption::encrypt_decrypt('decrypt',$id);
		$user = new User();
		$member_credit = new Member_credit();
		$credit_details = $member_credit->getMemberCreditDetials($id);
		?>
		<h4 class='text-danger'> Transaction </h4>
		<div class="row" style='margin-top:3px;'>
		<div class="col-md-3"><strong><?php echo MEMBER_LABEL; ?>: </strong><span class='text-danger'><?php echo ucwords($credit_details->lastname. " " . $credit_details->firstname." " . $credit_details->middlename);?></span></div>
		<div class="col-md-3"><strong>Invoice: </strong><span class='text-danger'><?php echo escape($credit_details->invoice)?></span></div>
		<div class="col-md-3"><strong>DR: </strong><span class='text-danger'><?php echo escape($credit_details->dr)?></span></div>
		<div class="col-md-3"><strong>PR: </strong><span class='text-danger'><?php echo escape($credit_details->ir)?></span></div>
		</div>
		<div class="row"  style='margin-top:3px;'>
		<div class="col-md-3"><strong>Terms: </strong><span class='text-danger'><?php echo escape($credit_details->def_terms)?></span></div>
		<div class="col-md-6"><strong>Date Sold: </strong><span class='text-danger'><?php echo escape(date('F d, Y h:i:s A',$credit_details->sold_date))?></span></div>
		</div>
		<hr>
		<h4 class='text-danger'> Item Details</h4>
		<?php
		// list ng items
		$sales = new Sales();
		$items = $sales->salesTransactionBaseOnPaymentId($credit_details->payment_id);
		$over1 = 0; $over2 = 0; $over3= 0; $over4 = 0;
		$all_total = 0;
		if(count($items) > 0){
		?>
		<table class="table table-bordered">
		<thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Adjustment</th><th>Total</th><th>End of terms</th></tr></thead>
		<tbody>
		<?php



			foreach($items as $item){
			$adjustment = $item->adjustment;
			$qtys = $item->qtys;
			$price = $item->price;
			if($adjustment){
				$per_item_adjustment = $adjustment / $qtys;
				$price = $price + $per_item_adjustment;
			}
			$total = ($qtys * $price) + $item->member_adjustment;
			$all_total += $total;
			$sold = $item->sold_date;
			$terms = $item->terms;
			if(!$terms){
				$terms = $credit_details->def_terms; // default terms of member
			}

			$end_of_terms = strtotime(date('F d, Y',$sold) . " $terms days");
			$now = strtotime(date('F d, Y'));
			$over_days = 0;
			if($now > $end_of_terms){
				 // over
				 // 86400 per day
			    $over = $now - $end_of_terms;
			    $over_days = $over / 86400;
			    $over_days = floor($over_days);
			    if($over_days >= 1 && $over_days <= 30){
			        $over1 += $total;
			    } else if($over_days >= 31 && $over_days <= 60){
			      $over2 += $total;
			    }else if($over_days >= 61 && $over_days <= 90){
			      $over3 += $total;
			    }else if($over_days >= 91){
			        $over4 += $total;
			    }
			}
			?>
			<tr>
				<td style='border-top:1px solid #ccc'><?php echo escape($item->item_code) . "<small class='text-danger' style='display:block;'>".$item->description."</small>"?></td>
				<td style='border-top:1px solid #ccc'><?php echo escape(number_format($item->qtys)); ?></td>
				<td style='border-top:1px solid #ccc'><?php echo escape(number_format($price,2)); ?></td>
				<td style='border-top:1px solid #ccc'><?php echo escape(number_format($item->member_adjustment,2)); ?></td>
				<td style='border-top:1px solid #ccc'><?php echo escape(number_format($total,2)); ?></td>
				<td style='border-top:1px solid #ccc'><?php echo date('F d, Y',$end_of_terms);?></td>
			</tr>
			<?php
			}
			?>
				<tr>
				<td style='border-top:1px solid #ccc'></td>
				<td style='border-top:1px solid #ccc' ></td>
				<td style='border-top:1px solid #ccc' ></td>
				<td style='border-top:1px solid #ccc' ></td>

				<td style='border-top:1px solid #ccc'><strong><?php echo number_format($all_total,2); ?></strong></td>
				<td style='border-top:1px solid #ccc'></td>
				</tr>
			</tbody>
		</table>
		<?php
		} else {
			?>
			<div class="alert alert-info">
				Invoice or DR is not yet printed.
			</div>
			<?php
		}


		// progress bar payment
		$total_due = $credit_details->amount;
		$total_paid = $credit_details->amount_paid;
		$percentage = ($total_paid / $total_due) * 100;
		$perc = number_format($percentage,2);

		$matchpayment = $sales->matchPaymentSales($user->data()->company_id,$credit_details->payment_id);
		$m_total = $matchpayment->ttotal;
		$m_cash = $matchpayment->cashamount;
		$m_cheque = $matchpayment->chequeamount;
		$m_bt = $matchpayment->btamount;
		$m_cc =  $matchpayment->ccamount;
	    $m_mc =  $matchpayment->mcamount;
	    $m_pc = $matchpayment->pcamount;
	    $m_pcf = $matchpayment->pcfamount;
		$other_payment  = $m_cash + $m_cheque + $m_bt + $m_cc + $m_pc + $m_pcf;
		?>
		<hr>
		<h4 class='text-danger'> Amount Due vs Amount Paid </h4>
		<div class="row"  style='margin-top:3px;'>
		<div class="col-md-3"><strong>Amount Due: </strong><span class='text-danger'><?php echo number_format($credit_details->amount,2)?></span></div>
		<div class="col-md-3"><strong>Amount Paid: </strong><span class='text-danger'><?php echo number_format($credit_details->amount_paid,2)?></span></div>
		<?php if($other_payment && $all_total != $credit_details->amount){
			?>
				<div class="col-md-3"><strong>Other payment: </strong><span class='text-danger'><?php echo number_format($other_payment-$credit_details->amount_paid,2)?></span></div>
			<?php
		}
		?>
		</div>
		<div style='margin-top:4px;' class="progress">
         <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $perc . "%"; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $perc . "%"; ?>;">
		  <?php echo $perc . "%"; ?>
		  </div>
		</div>
		<hr>
		<h4 class='text-danger'>Aging</h4>
		<?php

			$paid = $total_paid;
			if($all_total != $credit_details->amount){
				$paid += $other_payment;
			}

			if($over4){
				$over4 = $over4 - $paid;
				if($over4 < 0){
					$paid = abs($over4);
					$over4 = 0;
				} else {
					$paid = 0;
				}
			}
			if($over3){
				$over3 = $over3 - $paid;
				if($over3 < 0){
					$paid = abs($over3);
					$over3 = 0;
				} else {
					$paid = 0;
				}
			}
			if($over2){
				$over2 = $over2 - $paid;
				if($over2 < 0){
					$paid = abs($over2);
					$over2 = 0;
				} else {
					$paid = 0;
				}
			}
			if($over1){
				$over1 = $over1 - $paid;
				if($over1 < 0){
					$paid = abs($over1);
					$over1 = 0;
				} else {
					$paid = 0;
				}
			}

		?>
		<table class="table table-bordered">
			<thead>
				<tr><th>30 days</th><th>60 days</th><th>90 days</th><th>Over 90 days</th></tr>
			</thead>
			<tbody>
				<tr>
				<td><?php echo number_format($over1,2)?></td>
				<td><?php echo number_format($over2,2)?></td>
				<td><?php echo number_format($over3,2)?></td>
				<td><?php echo number_format($over4,2)?></td>
				</tr>
			</tbody>
		</table>
		<?php

	}
	function sendPOSSale(){
		$items = json_decode(Input::get('items'),true);
		$branch_id = Input::get('branch_id');
		$terminal_id = Input::get('terminal_id');
		$member_id = Input::get('member_id');
		$user = new User();

		$payment_con = Input::get('payment_con');
		$payment_con_freebies = Input::get('payment_con_freebies');
		$payment_member_credit = Input::get('payment_member_credit');
		$payment_member_deduction = Input::get('payment_member_deduction');

		$member_credit_cod = Input::get('member_credit_cod');
		$payment_cash = Input::get('payment_cash');
		$payment_credit = Input::get('payment_credit');
		$payment_bt = Input::get('payment_bt');
		$payment_cheque = Input::get('payment_cheque');
		$remarks  = Input::get('remarks');
		$sales_po_number  = Input::get('sales_po_number');
		$arr_points  = Input::get('arr_points');
		$pref_inv = Input::get('pref_inv');
		$pref_dr = Input::get('pref_dr');
		$pref_ir = Input::get('pref_ir');
		$custom_date_sold = Input::get('custom_date_sold');
		// over payment
		$op_payment_cash = Input::get('op_payment_cash');
		$op_payment_credit = Input::get('op_payment_credit');
		$op_payment_bt = Input::get('op_payment_bt');
		$op_payment_cheque = Input::get('op_payment_cheque');
		$arr_op_ids = Input::get('arr_op_ids');

		$payment = new Payment();

		if(!$terminal_id) {
			die("Please set up terminal first.");
		}
		$scompany =$user->data()->company_id;
		$payment->create(array(
			'created' => time(),
			'company_id' => $scompany,
			'is_active' => 1,
			'remarks' => $remarks,
			'po_number' => $sales_po_number
		));
		$payment_lastid = $payment->getInsertedId();
		$cashier_id = $user->data()->id;
		$station_id=0;

		$service_used_items = Input::get('service_used_items');
		$arr_exclude_release = [];
		if($service_used_items){
			$service_used_items = json_decode($service_used_items);
			$service_used_cls = new Service_item_use();
			foreach($service_used_items as $used){
				$arr_exclude_release[] = $used->item_id;
				$service_used_cls->update(array('status' => 1,'payment_id'=> $payment_lastid),$used->id);
			}
		}

		// start insert payment
		$sdr ='';$sinv ='';$sir =''; // change later


		$terminal = new Terminal();
		$terminal_mon = new Terminal_mon();
		$inventory = new Inventory();
		$rack = new Rack();
		$rack_default = $rack->getRackForSelling($branch_id);
		$inv_mon = new Inventory_monitoring();
		$total_all = 0;
		foreach($items as $sale){

		$newsales = new Sales();
		$forrelease = new Releasing();
		$date = (int) $sale['sold_date'];

		if (strpos($sale['discount'],'%')>0){
			$discount = (float)$sale['discount'];
			$discount = ($sale['price'] * $sale['qty']) * ($discount/100);
		} else {
			$discount = $sale['discount'];
		}

		$prod = new Product($sale['item_id']);
		$price = $prod->getPrice($sale['item_id']);
		$total_all += ($price->price * $sale['qty']) - $discount + $sale['member_adjustment'] + $sale['adjustment'];
		if($custom_date_sold){
			$date = strtotime($custom_date_sold);
		}
		$sale['sales_type'] = ($sale['sales_type']) ? $sale['sales_type']  : 0;

		$newsales->create(array(
			'terminal_id' => $terminal_id,
			'pref_inv' => $pref_inv,
			'pref_ir' => $pref_ir,
			'pref_dr' => $pref_dr,
			'invoice' => $sale['invoice'],
			'dr' => $sale['dr'],
			'ir' => $sale['ir'],
			'item_id' => $sale['item_id'],
			'price_id' =>$price->id,
			'qtys' => $sale['qty'],
			'discount' => number_format($discount,2, '.', ''),
			'store_discount' => number_format($sale['store_discount'],2, '.', ''),
			'adjustment' => number_format($sale['adjustment'],2, '.', ''),
			'member_adjustment' => number_format($sale['member_adjustment'],2, '.', ''),
			'company_id' => $sale['company_id'],
			'cashier_id' => $cashier_id,
			'sold_date' => $date,
			'payment_id' => $payment_lastid,
			'member_id' => $member_id,
			'station_id' => $station_id,
			'warranty' =>  $sale['warranty'],
			'sales_type' =>  $sale['sales_type'],
			'agent_id' =>  $sale['agent_id']
		));
		if(!in_array($sale['item_id'],$arr_exclude_release)){
			if($sale['is_bundle'] == 1){
				// get item bundle
				$bundle = new Bundle();
				$bundles = $bundle->getBundleItem($sale['item_id']);
				// check if Auto Deduct
				if($bundles){
					foreach($bundles as $bund){

						if(Configuration::getValue('auto_dededuct_inv') == 1){
							$curinventory = $inventory->getQty($sale['item_id'],$branch_id,$rack_default->id);
							if(isset($curinventory->qty)){
							$newqty = $curinventory->qty - $sale['qty']; // dito ibawas
							$inventory->update(array(
								'qty' => $newqty
							), $curinventory->id);
							// insert monitoring
							$monlabelinv ='';
							$monlabeldr ='';
							$monlabelir ='';
							if($sale['invoice']){
								$monlabelinv = "Invoice $sale[invoice]";
							}
							if($sale['dr']){
								$monlabeldr = "Dr $sale[dr]";
							}
							if($sale['ir']){
								$monlabelir = "Ir $sale[ir]";
							}
							$inv_mon->create(array(
								'item_id' => $sale['item_id'],
								'rack_id' => $rack_default->id,
								'branch_id' => $branch_id,
								'page' => 'ajax/ajax_sale.php',
								'action' => 'Update',
								'prev_qty' => $curinventory->qty,
								'qty_di' => 2,
								'qty' => $sale['qty'],
								'new_qty' => $newqty,
								'created' => time(),
								'user_id' => $cashier_id,
								'remarks' => 'Deduct inventory upon selling on POS, Payment ID: ' . $payment_lastid . ' ' .$monlabelinv . " " . $monlabeldr . " " .$monlabelir,
								'is_active' => 1,
								'company_id' => $sale['company_id']
							));
							} else {
								$inv_mon->create(array(
									'item_id' => $sale['item_id'],
									'rack_id' => $rack_default->id,
									'branch_id' => $branch_id,
									'page' => 'ajax/ajax_sale.php',
									'action' => 'Update',
									'prev_qty' => 0,
									'qty_di' => 2,
									'qty' => $sale['qty'],
									'new_qty' => 0,
									'created' => time(),
									'user_id' => $cashier_id,
									'remarks' => 'Deduct inventory upon selling on POS, Payment ID: ' . $payment_lastid . ' ' .$monlabelinv . " " . $monlabeldr . " " .$monlabelir,
									'is_active' => 1,
									'company_id' => $sale['company_id']
								));
							}

						} else {
								$forrelease->create(array(
									'item_id' =>$bund->item_id_child,
									'payment_id' => $payment_lastid,
									'qty' => $sale['qty'] * $bund->child_qty,
									'is_active' => 1,
									'status' => 1,
									'company_id' => $sale['company_id'],
									'user_id' => $cashier_id,
									'terminal_id' => $terminal_id,
									'created' => time()
							));
						}



					}
				}
			} else {
				if($prod->data()->item_type == -1){
					if(Configuration::getValue('auto_dededuct_inv') == 1){
						$curinventory = $inventory->getQty($sale['item_id'],$branch_id,$rack_default->id);
						if(isset($curinventory->qty)){
							$newqty = $curinventory->qty - $sale['qty']; // dito ibawas
								$inventory->update(array(
									'qty' => $newqty
								), $curinventory->id);
								// insert monitoring
								$monlabelinv ='';
								$monlabeldr ='';
								$monlabelir ='';
								if($sale['invoice']){
									$monlabelinv = "Invoice $sale[invoice]";
								}
								if($sale['dr']){
									$monlabeldr = "Dr $sale[dr]";
								}
								if($sale['ir']){
									$monlabelir = "Ir $sale[ir]";
								}
								$inv_mon->create(array(
									'item_id' => $sale['item_id'],
									'rack_id' => $rack_default->id,
									'branch_id' => $branch_id,
									'page' => 'ajax/ajax_sale.php',
									'action' => 'Update',
									'prev_qty' => $curinventory->qty,
									'qty_di' => 2,
									'qty' => $sale['qty'],
									'new_qty' => $newqty,
									'created' => time(),
									'user_id' => $cashier_id,
									'remarks' => 'Deduct inventory upon selling on POS, Payment ID: ' . $payment_lastid . ' ' .$monlabelinv . " " . $monlabeldr . " " .$monlabelir,
									'is_active' => 1,
									'company_id' => $sale['company_id']
								));
						}

					}  else {
					$forrelease->create(array(
						'item_id' => $sale['item_id'],
						'payment_id' => $payment_lastid,
						'qty' => $sale['qty'],
						'is_active' => 1,
						'status' => 1,
						'company_id' => $sale['company_id'],
						'user_id' => $cashier_id,
						'terminal_id' => $terminal_id,
						'created' => time()
					));
				}

				} else if ($prod->data()->item_type == 2 || $prod->data()->item_type == 3  || $prod->data()->item_type == 4 || $prod->data()->item_type == 5){
					for($startingservice = 0; $startingservice < $sale['qty']; $startingservice++){
						$con = new Consumable();
						$myCon = $con->getConsumableByItemId($sale['item_id']);
						$newServ = new Service();
						$start = $date;
						$cday = $sale['cdays'];
						$endDate = strtotime(date('m/d/Y',$start) . $cday . " day");
						$newServ->create(array(
							'member_id' => $member_id,
							'item_id' => $sale['item_id'],
							'start_date' => $start,
							'end_date' =>$endDate,
							'consumable_qty' => $sale['cqty'],
							'company_id' => $sale['company_id'],
							'payment_id' => $payment_lastid
						));
						$servlastid = $newServ->getInsertedId();
						if($prod->data()->item_type == 4){
							$con_amount = new Consumable_amount();
							$n = time();
							$con_amount->create(array(
								'service_id' => $servlastid,
								'amount' => $price->price,
								'item_id' => $sale['item_id'],
								'member_id' => $member_id,
								'is_active' => 1,
								'created' => $n,
								'modified' => $n,
								'payment_id' => $payment_lastid
							));
						}
						if($prod->data()->item_type == 5){
							$con_free = new Consumable_freebies();
							$con_free_amount = $con_free->getConsumableFreebiesAmount($sale['item_id']);

							$n = time();
							$con_free->create(array(
								'service_id' => $servlastid,
								'amount' => $con_free_amount->amount,
								'item_id' => $sale['item_id'],
								'member_id' => $member_id,
								'is_active' => 1,
								'created' => $n,
								'modified' => $n,
								'payment_id' => $payment_lastid
							));
						}
					}

				}

			}
		}

		// update member terms if there is any
			$member_terms = new Member_term();
			$member_terms->updateSingleUseTerms($member_id,$sale['item_id']);

			$sdr = $sale['dr'];
			$sir = $sale['ir'];
			$sinv =  $sale['invoice'];
			$sdate =$date;
		}   // end sale for

		if($sinv != 0){
			$terminal->update(array(
				'modified' => strtotime(date('Y/m/d H:i:s')),
				'invoice' =>$sinv
			), $terminal_id);
		}

		if($sdr != 0){
			$terminal->update(array(
				'modified' => strtotime(date('Y/m/d H:i:s')),
				'dr' =>$sdr
			), $terminal_id);
		}

		if($sir != 0){
			$terminal->update(array(
				'modified' => strtotime(date('Y/m/d H:i:s')),
				'ir' =>$sir
			), $terminal_id);
		}

		$sdr = ($sdr) ? 'Dr: '.$sdr:'';
		$sinv = ($sinv) ? 'Inv: '.$sinv:'';
		$sir = ($sir) ? 'Ir: '.$sir:'';

		if($arr_points && $member_id && $total_all && Configuration::getValue('points') == 1){
			$point_cls = new Point();
			$arr_points = json_decode($arr_points,true);
			foreach($arr_points as $pt_type){
				$point_cls->updateUserPoint($member_id,$user,$total_all,$payment_lastid,$pt_type);
			}
		}

		if($payment_credit){

		 // insert credit
		 $payment_credit = json_decode($payment_credit,true);
		 $credit = new Credit();
		 $total_amount_cc = 0;

		foreach($payment_credit as $c){
			$total_amount_cc += $c['amount'];
			$credit->create(array(
				'card_number' => $c['credit_number'],
				'amount' => $c['amount'],
				'bank_name'=>$c['bank_name'],
				'lastname'=>$c['lastname'],
				'firstname'=>$c['firstname'],
				'middlename'=>$c['middlename'],
				'company'=>$c['comp'],
				'address'=>$c['add'],
				'zip' => $c['postal'],
				'contacts' => $c['phone'],
				'email' => $c['email'],
				'remarks' => $c['remarks'],
				'card_type' => $c['card_type'],
				'trace_number' => $c['trace_number'],
				'approval_code' => $c['approval_code'],
				'date' =>  strtotime($c['date']),
				'is_active' => 1,
				'created' => $sdate,
				'modified' => $sdate,
				'payment_id' => $payment_lastid
			));
		}
		$prevamount = $terminal->getTAmount($terminal_id,2);
		$prevamount = ($prevamount->t_amount_cc) ? $prevamount->t_amount_cc:0;
		$to_amount = $total_amount_cc + $prevamount;

		$terminal->update(array(
			't_amount_cc' => $to_amount
		),$terminal_id);
		$terminal_mon->create( array(
			'terminal_id' => $terminal_id,
			'user_id' => $cashier_id,
			'from_amount' =>$prevamount,
			'amount' =>$total_amount_cc,
			'to_amount'=>$to_amount,
			'status' => 1,
			'remarks' => "POS $sinv $sdr $sir",
			'is_active' => 1,
			'company_id' => $scompany,
			'p_type' => 2,
			'created' => $sdate
		));
	}
	if($payment_bt){
		// insert bank transfer
		$payment_bt = json_decode($payment_bt,true);
		$bank_transfer = new Bank_transfer();
		$total_amount_bt = 0;
		foreach($payment_bt as $c){
			$total_amount_bt += $c['amount'];
			$bank_transfer->create(array(
				'bankfrom_account_number' => $c['credit_number'],
				'amount' => $c['amount'],
				'bankfrom_name'=>$c['bank_name'],
				'bankto_account_number' => $c['bt_bankto_account_number'],
				'bankto_name' => $c['bt_bankto_name'],
				'lastname'=>$c['lastname'],
				'firstname'=>$c['firstname'],
				'middlename'=>$c['middlename'],
				'company'=>$c['comp'],
				'address'=>$c['add'],
				'zip' => $c['postal'],
				'contacts' => $c['phone'],
				'date' => strtotime($c['date']),
				'is_active' => 1,
				'created' => $sdate,
				'modified' => $sdate,
				'payment_id' => $payment_lastid
			));
		}
		$prevamount = $terminal->getTAmount($terminal_id,4);
		$prevamount = ($prevamount->t_amount_bt) ? $prevamount->t_amount_bt:0;
		$to_amount = $total_amount_bt + $prevamount;

		$terminal->update(array(
			't_amount_bt' => $to_amount
		),$terminal_id);
		$terminal_mon->create( array(
			'terminal_id' => $terminal_id,
			'user_id' => $cashier_id,
			'from_amount' =>$prevamount,
			'amount' =>$total_amount_bt,
			'to_amount'=>$to_amount,
			'status' => 1,
			'remarks' => "POS $sinv $sdr $sir",
			'is_active' => 1,
			'company_id' => $scompany,
			'p_type' => 4,
			'created' => $sdate
		));
	}
	if($payment_cheque){
		// insert cheque
		$payment_cheque = json_decode($payment_cheque,true);
		$cheque = new Cheque();
		$total_amount_ch = 0;
		foreach($payment_cheque as $c){
			$total_amount_ch += $c['amount'];
			$cheque->create(array(
				'check_number' => $c['cheque_number'],
				'amount' => $c['amount'],
				'bank'=>$c['bank_name'],
				'payment_date' => strtotime($c['date']),
				'lastname'=>$c['lastname'],
				'firstname'=>$c['firstname'],
				'middlename'=>$c['middlename'],
				'contacts' => $c['phone'],
				'is_active' => 1,
				'created' => $sdate,
				'modified' => $sdate,
				'payment_id' => $payment_lastid
			));
		}
		$prevamount = $terminal->getTAmount($terminal_id,3);
		$prevamount = ($prevamount->t_amount_ch) ? $prevamount->t_amount_ch:0;
		$to_amount = $total_amount_ch + $prevamount;

		$terminal->update(array(
			't_amount_ch' => $to_amount
		),$terminal_id);
		$terminal_mon->create( array(
			'terminal_id' => $terminal_id,
			'user_id' => $cashier_id,
			'from_amount' =>$prevamount,
			'amount' =>$total_amount_ch,
			'to_amount'=>$to_amount,
			'status' => 1,
			'remarks' => "POS $sinv $sdr $sir",
			'is_active' => 1,
			'company_id' => $scompany,
			'p_type' => 3,
			'created' => $sdate
		));
	}
	if($payment_cash){
		// insert cash
		$pcash = new Cash();
		$total_amount = $payment_cash;
		$pcash->create(array(
			'amount' =>$payment_cash,
			'is_active' => 1,
			'created' => $sdate,
			'modified' => $sdate,
			'payment_id' => $payment_lastid
		));
		$prevamount = $terminal->getTAmount($terminal_id,1);
		$prevamount = ($prevamount->t_amount) ? $prevamount->t_amount:0;
		$to_amount = $total_amount + $prevamount;

		$terminal->update(array(
			't_amount' => $to_amount
		),$terminal_id);
		$terminal_mon->create( array(
			'terminal_id' => $terminal_id,
			'user_id' => $cashier_id,
			'from_amount' =>$prevamount,
			'amount' =>$total_amount,
			'to_amount'=>$to_amount,
			'status' => 1,
			'remarks' => "POS $sinv $sdr $sir",
			'is_active' => 1,
			'company_id' => $scompany,
			'p_type' => 1,
			'created' => $sdate
		));
	}
	if($payment_member_credit){
		// insert cash
		$pcredit = new Member_credit();
		$pcredit->create(array(
			'amount' =>$payment_member_credit,
			'is_active' => 1,
			'created' => $sdate,
			'modified' => $sdate,
			'payment_id' => $payment_lastid,
			'member_id' => $member_id
		));
	}
	if($payment_member_deduction){
		// insert cash
		$payment_member_deduction = json_decode($payment_member_deduction);
		if(count($payment_member_deduction)){
			foreach($payment_member_deduction as $deduct_member){
				$pdeduct = new Deduction();
				$deduction_amount = ($deduct_member->amount) ? $deduct_member->amount : 0;
				$member_deduction_remarks = ($deduct_member->remarks) ? $deduct_member->remarks : '';
				$pdeduct->create(array(
					'amount' =>$deduction_amount,
					'is_active' => 1,
					'created' => $sdate,
					'remarks' => $member_deduction_remarks,
					'payment_id' => $payment_lastid,
					'member_id' => $deduct_member->member_id
				));
			}
		}
	}
	if($payment_con){
		// insert cash
		$pcon = new Payment_consumable();
		$pcon->create(array(
			'amount' =>$payment_con,
			'is_active' => 1,
			'created' => $sdate,
			'modified' => $sdate,
			'payment_id' => $payment_lastid,
			'member_id' => $member_id
		));


		$mem = new Member();
		$mycon = $mem->getMyConsumableAmount($member_id);
		if($mycon){

			foreach($mycon as $c){
				if($payment_con) {
					$notvalid = $mem->getNotYetValidCheque($c->payment_id);
					if($notvalid->cheque_amount) {
						$validamount = $c->amount - $notvalid->cheque_amount;
						$notv = $notvalid->cheque_amount;
					} else {
						$validamount = $c->amount;
						$notv = 0;
					}
					$toupdate = new Consumable_amount();
					if($validamount > $payment_con) {
						$leftamount = ($validamount - $payment_con) + $notv ;
						$payment_con =0;
						$toupdate->update(array('amount' => $leftamount, 'modified' => time()), $c->id);
					} else {
						$leftamount = $notv;
						$toupdate->update(array('amount' => $leftamount, 'modified' => time()), $c->id);
						$payment_con = $payment_con - $validamount;
					}
				}
			}
		}
	}
	if($payment_con_freebies){
		// insert cash
		$pcon = new Payment_consumable_freebies();
		$pcon->create(array(
			'amount' =>$payment_con_freebies,
			'is_active' => 1,
			'created' => $sdate,
			'modified' => $sdate,
			'payment_id' => $payment_lastid,
			'member_id' => $member_id
		));


		$mem = new Member();
		$mycon = $mem->getMyConsumableFreebies($member_id);
		if($mycon){

			foreach($mycon as $c){
				if($payment_con_freebies) {

					$validamount = $c->amount;

					$toupdate = new Consumable_freebies();
					if($validamount > $payment_con_freebies) {
						$leftamount = ($validamount - $payment_con_freebies);
						$payment_con_freebies =0;
						$toupdate->update(array('amount' => $leftamount, 'modified' => time()), $c->id);
					} else {
						$leftamount = 0;
						$toupdate->update(array('amount' => $leftamount, 'modified' => time()), $c->id);
						$payment_con_freebies = $payment_con_freebies - $validamount;
					}
				}
			}
		}
	}
		// over payment
		$user_credit = new User_credit();
		if($op_payment_cash){ // status  = 1
			$user_credit->create(array(
				'status' => 1,
				'json_data' => $op_payment_cash,
				'company_id' => $scompany,
				'member_id' => $member_id,
				'user_id' => $cashier_id,
				'from_tbl' => "POS walk in",
				'is_active' => 1,
				'ref_id' => $payment_lastid,
				'is_used' => 0,
				'created' => time()
			));
		}
		if($op_payment_credit){ // status  = 2
			$user_credit->create(array(
				'status' => 2,
				'json_data' => $op_payment_credit,
				'company_id' => $scompany,
				'member_id' => $member_id,
				'user_id' => $cashier_id,
				'from_tbl' => "POS walk in",
				'is_active' => 1,
				'ref_id' => $payment_lastid,
				'is_used' => 0,
				'created' => time()
			));
		}
		if($op_payment_cheque){ // status  = 3
			$user_credit->create(array(
				'status' => 3,
				'json_data' => $op_payment_cheque,
				'company_id' => $scompany,
				'member_id' => $member_id,
				'user_id' => $cashier_id,
				'from_tbl' => "POS walk in",
				'is_active' => 1,
				'ref_id' => $payment_lastid,
				'is_used' => 0,
				'created' => time()
			));
		}
		if($op_payment_bt){ // status  = 4
			$user_credit->create(array(
				'status' => 4,
				'json_data' => $op_payment_bt,
				'company_id' => $scompany,
				'member_id' => $member_id,
				'user_id' => $cashier_id,
				'from_tbl' => "POS walk in",
				'is_active' => 1,
				'ref_id' => $payment_lastid,
				'is_used' => 0,
				'created' => time()
			));
		}
		$arr_op_ids = json_decode($arr_op_ids,true);
		if($arr_op_ids){
			 foreach($arr_op_ids as $op_id){
			    $user_credit->update(['is_used'=>1],$op_id);
			 }
		}
	}
	function forReleasing(){
		$now = date('m/d/Y H:i:s A');
		$forReleasing = new Releasing();
		$user = new User();
		$pending = $forReleasing->getPending($user->data()->branch_id);
		if(count($pending) > 0){
			echo "<table class='table table-bordered'>";
			echo "<thead><tr><th>Invoice/DR</th><th>Item</th><th>Qty</th><th></th></tr></thead>";
			echo "<tbody>";
			$prevpay = 0;
			foreach($pending as $item){
				$lbl = "";

				$bordertop ='';
				$btn = '';
				if($prevpay != $item->payment_id){
				$btn =  "<button class='btn btn-default getStocks' data-payment_id='".$item->payment_id."'>Get Stock</button>";
					if($item->invoice){
					$lbl .= "<span style='display:block;'>Invoice #".$item->invoice."</span>";
					}
					if($item->dr){
					$lbl .= "<span style='display:block;'>DR #".$item->dr."</span>";
					}
					if($item->ir){
					$lbl .= "<span style='display:block;'>PR #".$item->ir."</span>";
					}
					$bordertop = "border-top:1px solid #ccc;";
				}
				$prevpay = $item->payment_id;


				echo "<tr><td style='$bordertop'>".($lbl)."<span class='text-danger''>".date('m/d/Y H:i:s A',$item->sold_date)."</span></td><td  style='$bordertop'>$item->item_code<small style='display:block;'>".$item->description."</small></td><td  style='$bordertop'>$item->qty</td><td style='$bordertop'>$btn</td></tr>";
			}
			echo "</tbody>";
			echo "</table>";
			echo "<p>Time: ".date('F d, Y H:i:s A')."</p>";
		} else {
			echo "<p>No record at the moment.</p>";
		}
	}
	function getStockForReleasing2(){
		$payment_id = Input::get('payment_id');

		$user = new User();
		$forReleasing = new Releasing();
		$pending = $forReleasing->getByPayment($payment_id);
		if($user->hasPermission('up_releasing')){
			echo "<div class='row' >";
			echo "<div class='col-md-4'><div class='form-group'><input type='text' class='selectitem form-control' id='txtAddItem'></div></div>";
			echo "<div class='col-md-4'><div class='form-group'><input type='text' class='form-control' id='txtAddQty' placeholder='Quantity'></div></div>";
			echo "<div class='col-md-4'><div class='form-group'><button class='btn btn-default' id='btnAddItem'>Add Item</button></div></div>";
			echo "</div>";

		}
			echo "<table class='table table-bordered'>";
			echo "<thead><tr><th>Item</th><th>Qty</th><th>Racking</th><th></th></tr></thead>";
			echo "<tbody>";
			$inventory = new Inventory();

			foreach($pending as $item){
			$btnDelete='';
			if($user->hasPermission('up_releasing')){
				$btnDelete = "<button class='btn btn-danger btnDeleteItem'  data-id='$item->id' >";
			}
				if($item->is_bundle == 1) {

					$bundle = new Bundle();
					$bundles = $bundle->getBundleItem($item->item_id);
					if($bundles){
						foreach($bundles as $bun){

						}
					}
				} else {
					$bordertop = "border-top:1px solid #ccc;";
						$racks = $inventory->getRackInventory($item->item_id,$item->branch_id,0);
						$ret = "";
						if($racks){
							$ret = "<select class='form-control rackSelection'>";
							foreach($racks as $rack){
								$qty = formatQuantity($rack->qty);
								$ret .= "<option data-qty='$rack->qty' value='$rack->id'>$rack->rack (Stock - $qty)</option>";
							}
							$ret .= "</select>";
						} else {
							$ret = "No stocks available.";
						}
					echo "<tr class=''><td  style='$bordertop'>$item->item_code<small style='display:block;'>".$item->description."</small>  </td><td  style='$bordertop'>$item->qty</td><td style='$bordertop'>$ret</td><td>$btnDelete</td></tr>";
				}

			}
			echo "</tbody>";
			echo "</table>";
			echo "<hr>";
			echo "<button  class='btn btn-default' id='btnRelease'>Release</button>";
	}
	function getStockForReleasing(){
		$payment_id = Input::get('payment_id');

		$user = new User();
		$forReleasing = new Releasing();
		$pending = $forReleasing->getByPayment($payment_id);

		if($user->hasPermission('up_releasing')){
			echo "<input type='hidden' id='update_payment_id' value='$payment_id'>";
			echo "<div class='row' >";
			echo "<div class='col-md-4'><div class='form-group'><input type='text' class='form-control selectitem' id='txtAddItem'></div></div>";
			echo "<div class='col-md-4'><div class='form-group'><input type='text' class='form-control' id='txtAddQty' placeholder='Quantity'></div></div>";
			echo "<div class='col-md-4'><div class='form-group'><button class='btn btn-default' id='btnAddItem'>Add Item</button></div></div>";
			echo "</div>";
		}
			echo "<table class='table table-bordered'>";
			echo "<thead><tr><th>Item</th><th>Qty</th><th>Racking</th><th></th></tr></thead>";
			echo "<tbody>";
			$prevpay = 0;
			$toAssemble = [];
			foreach($pending as $item){
			$btnDelete='';
			if($user->hasPermission('up_releasing')){
				$btnDelete = "<button class='btn btn-danger btnDeleteItem'  data-id='$item->id' ><i class='fa fa-remove'></i></button>";
			}
				$bordertop = "border-top:1px solid #ccc;";
				$cur_inv = inventory_racking(0,$item->qty,$item->item_id,$user->data()->branch_id,false);

					$hasins = $cur_inv['insufficient'];
					$disabledbtn = '';
					$bgdanger = '';
					if($hasins){
						$disabledbtn = 'disabled';
						$bgdanger ='bg-danger';
					}
					$racking = json_decode($cur_inv['racking']);
					$dir = "";
					foreach($racking as $todec){
					$stock_man = (isset($todec->stock_man) && $todec->stock_man) ? $todec->stock_man : 'N/A';
					$dir .= "<span style='display:block;'>".$todec->rack." : ".$todec->qty."</span><span style='display:block;'>In charge: ".$stock_man."</span>";
					}
					if($item->item_id_set){
						$toAssemble[] = ['item_id' => $item->item_id_set, 'qty' => $item->qty] ;
					}
				echo "<tr class='$bgdanger'><td  style='$bordertop'>$item->item_code<small style='display:block;'>".$item->description."</small> </td><td  style='$bordertop'>$item->qty</td><td style='$bordertop'>$dir</td><td style='$bordertop' >$btnDelete</td> </tr>";
			}
			echo "</tbody>";
			echo "</table>";
			echo "<hr>";
			if(count($toAssemble) >0){
				echo "<button data-items='".json_encode($toAssemble)."' class='btn btn-default' id='btnAssemble'>Assemble</button>";
			}
			echo "<button $disabledbtn class='btn btn-default' id='btnRelease'>Release</button>";
	}
	function processedStockForReleasing(){
		$payment_id = Input::get('payment_id');
		$user = new User();
		$forReleasing = new Releasing();
		$pending = $forReleasing->getByPayment($payment_id);
			$inventory = new Inventory();
			$inv_mon = new Inventory_monitoring();
			$is_valid = true;
			foreach($pending as $item){
				if($item->status == 1){
					$cur_inv = inventory_racking(0,$item->qty,$item->item_id,$user->data()->branch_id,false);
					$item_set = $item->item_id;
					$racking = json_decode($cur_inv['racking']);
					$hasins = $cur_inv['insufficient'];
					if($hasins) $is_valid = false;

					}

			}

			if($is_valid){
			foreach($pending as $item){
					if($item->status == 1){
						$cur_inv = inventory_racking(0,$item->qty,$item->item_id,$user->data()->branch_id,false);
						$item_set = $item->item_id;
						$racking = json_decode($cur_inv['racking']);
						$hasins = $cur_inv['insufficient'];
						if($hasins) $is_valid = false;
						// check stock muna
						$lbl ='';
						if($item->invoice){
						$lbl .= " Invoice #".$item->invoice;
						}
						if($item->dr){
						$lbl .= " DR #".$item->dr;
						}
						if($item->ir){
						$lbl .= " PR #".$item->ir;
						}
						foreach($racking as $todec){
							$rack_id = $todec->rack_id;
							$rack_qty = $todec->qty;

							// check if item exists in rack
							if($inventory->checkIfItemExist($item_set,$user->data()->branch_id,$user->data()->company_id,$rack_id)){
								$curinventoryFrom = $inventory->getQty($item_set,$user->data()->branch_id,$rack_id);
								$currentqty = $curinventoryFrom->qty;
								$inventory->subtractInventory($item_set,$user->data()->branch_id,$rack_qty,$rack_id);
							} else {
								$currentqty = 0;
							}
							// monitoring
							$newqtyFrom = $currentqty - $rack_qty;
							$inv_mon->create(array(
								'item_id' =>$item_set,
								'rack_id' => $rack_id,
								'branch_id' => $user->data()->branch_id,
								'page' => 'ajax/ajax_query2.php',
								'action' => 'Update',
								'prev_qty' => $currentqty,
								'qty_di' => 2,
								'qty' => $rack_qty,
								'new_qty' => $newqtyFrom,
								'created' => time(),
								'user_id' => $user->data()->id,
								'remarks' => 'Deduct item from ' .$lbl,
								'is_active' => 1,
								'company_id' => $user->data()->company_id
							));
						}
						$forReleasing->update(array(
							'status' => 2,
							'racking' => $cur_inv['racking']
						),$item->id);
						// check order point
						//checkItemOrderPoint($item_set,$user->data()->branch_id,$user->data()->company_id);
					}
				}
				echo "Request processed successfully.";
			} else {
				echo "Invalid Inventories";
			}

	}
	function checkItemOrderPoint($item_set,$branch_id,$company_id){
				$checker = new Reorder_item();
				$invpoint  = new Inventory();
				$cnt = $checker->checkItemOrderPoint($item_set,$branch_id,$company_id);

				if($cnt->cnt == 0){
					$odpoint = new Reorder_point();
					// get current date base on

					$month = date('n');
					$pointqty = $odpoint->getOrderPoint($item_set,$branch_id,$company_id,$month);
					if($pointqty){
						$allqty = $invpoint->getAllQuantity($item_set,$branch_id);
						if ($allqty->totalQty < $pointqty->order_point ){
							// insert
							$insert = new Reorder_item();
							$insert->create(array(
								'item_id' =>$item_set,
								'qty' => $pointqty->order_qty,
								'orderby_branch_id' => $branch_id,
								'orderto_branch_id' => $pointqty->orderto_branch_id,
								'is_active' => 1,
								'company_id' => $company_id,
								'created' => strtotime(date('Y/m/d H:i:s')),
								'modified' => strtotime(date('Y/m/d H:i:s')),
								'status' => 1
							));
						}
					}
				}
	}
	function lastSoldItem(){
		$terminal_id = [Input::get('terminal_id')];
			$re_use_display = "style='display:none;'";
		if(Input::get('member_id')){
			$re_use_display ="";
				$member_id = [Input::get('member_id')];
				$memberData = new Member(Input::get('member_id'));
				$mycon = $memberData->getMyConsumableAmount(Input::get('member_id'));
				$totalcon = 0;
				if($mycon){
					foreach($mycon as $con){
					$totalcon += $con->amount;
					}
				}
				$withInv = ['Without Invoice','With Invoice'];
				$ishold = ['Not hold','Hold'];
				$memlimit = ($memberData->data()->credit_limit) ? $memberData->data()->credit_limit : "Not indicated";
				$tax_type = ($memberData->data()->tax_type) ? $memberData->data()->tax_type : "";

				$mem = [
				'total_consumable' => number_format($totalcon,2),
				'with_inv' => $withInv[$memberData->data()->with_inv],
				 'is_blacklisted' => $memberData->data()->is_blacklisted,
				 'credit_limit' => $memlimit,
				 'tax_type' => $tax_type,
				 'tin_no' => $memberData->data()->tin_no,
				 'terms' => $memberData->data()->terms,
				 'sales_type' => $memberData->data()->salestype
				 ];
				$user_credit = new User_credit();
				$user_credits = $user_credit->getCredit(Input::get('member_id'));
				echo "<input type='hidden' id='op_member_list' value='".json_encode($user_credits)."'>";
		} else {
			$mem = [];
			$member_id= 0;
		}
		$sales = new Sales();
		$user = new User();
		$limit = 100;
		$member_sales = $sales->getSalesR2($user->data()->company_id, 0, $limit, 0, 0, $terminal_id, 0, 0, $member_id, 0, 0, 0, 0, 0, 0, 0);
		$rethtml = "<div class='panel panel-default'><div class='panel-body'>No previous record.</div></div>";
		if(count($member_sales)){
			$rethtml = "";
			$prev = '';
			$phead = "<div class='panel panel-default'>";
			$phead .= "<div class='panel-body'>";
			$phead .= "<table class='table table-bordered'>";
			$phead .= "<thead><tr><th>Item</th><th>Price</th><th>Qty</th><th>Adjustment</th><th>Total</th></tr></thead>";
			$phead .= "<tbody>";


			$ptail = "</tbody>";
			$ptail .= "</table>";
			$ptail .= "</div>";
			$ptail .= "</div>";


			$first = true;
			$ctr = 1;
			foreach($member_sales as $item){
				$lbl_status = "";
				if($item->status == 1) {
				$lbl_status = "<span class='label label-danger'>Cancelled</span>";
				}
				if($prev != $item->payment_id){
				$spaninv = '';
				$spandr = '';
				$spanir = '';
					if($item->invoice){
						$spaninv = "<span class='label label-primary'>".INVOICE_LABEL. " ". $item->pref_inv." $item->invoice</span>";
					}
					if($item->dr){
						$spandr = "<span class='label label-primary'>".DR_LABEL. " ". $item->pref_dr." $item->dr</span>";
					}
					if($item->ir){
						$spanir = "<span class='label label-primary'>".PR_LABEL. " ".$item->pref_ir. " $item->ir</span>";
					}

					$alllbl = $spaninv . " " . $spandr . " " . $spanir . " " . $lbl_status;
					$member_name ="";
					$remarks="";
					if($item->member_name){
					$member_name = "<p><strong>" . $item->member_name . "</strong> <button $re_use_display data-id='".$item->payment_id."' class='btn btn-default btn-sm pull-right btnReuse'>Re-Use</button>  <span class='span-block text-success'>".date('F d, Y',$item->sold_date)."</span></p>";
					}
					if($item->p_remarks){
					$remarks = "<small class='text-danger span-block'>" . $item->p_remarks . "</small>";
					}
					if($first){
						$rethtml .= $alllbl . $phead . $member_name . $remarks;
						$first = false;
					} else {
						$rethtml .= $ptail;
						$rethtml .= $alllbl .  $phead . $member_name . $remarks;
					}
				}
				$total = (($item->qtys * $item->price) + $item->adjustment + $item->member_adjustment) - ($item->discount + $item->store_discount);

				$ind_adjustment =  0;
				if($item->adjustment){
				$ind_adjustment = $item->adjustment / $item->qtys;
				}
			$adjusted = $item->price + $ind_adjustment;
			if($total){
				$price_ind = $total / $item->qtys;
			}

			if(is_numeric($price_ind)){
				$price_ind = number_format($price_ind,2);
			}

			$rethtml .= "<tr><td>$item->item_code<small class='text-danger span-block'>$item->description</small></td><td> " . number_format($adjusted,2). "<small class='span-block text-danger'>$price_ind</small></td><td>$item->qtys</td><td>$item->member_adjustment</td><td>" . number_format($total,2) . "</td></tr>";
			$prev = $item->payment_id;
			$ctr ++;
			}
			$rethtml .= $ptail;
		}
		$service_used_item = new Service_item_use();

		$used_items = $service_used_item->getUsedItemsMember(Input::get('member_id'));
		if($used_items){
			$retused = [];
			foreach($used_items as $used){
				$usedprod = new Product();
				$usedprice = $usedprod->getPrice($used->item_id);
				$used->price = $usedprice->price;
				$used->total = $usedprice->price * $used->qty;
				$retused[] = $used;
			}
			if($retused){
				echo "<input type='hidden' value='".json_encode($retused)."' id='service_used_items'>";
			}
		}
		if(count($mem)){
			echo "<div class='panel panel-default'><div class='panel-body'><h4>Other details</h4>";
			echo "<p><i class='fa fa-info-circle'></i> Inv Type: <strong>$mem[with_inv]</strong></p>";
			echo "<p><i class='fa fa-ticket'></i> Credit Limit: <strong>$mem[credit_limit]</strong></p>";

			if($mem['total_consumable'] != 0) {
				echo "<p id='withConsumableAmount'><i class='fa fa-money'></i> Consumable amount: <strong>$mem[total_consumable]</strong></p>";
			}
			if($mem['is_blacklisted'] == 1){
			echo "<p class='text-danger'><i class='fa fa-exclamation-circle'></i> This account is on hold. You can only continue this transaction using cash payment only.</p>";
			}
			if(Input::get('member_id') && Configuration::getValue('points') == 1){
			$point_cls = new Point();
			$my_points = $point_cls->getActiveUserPoint(Input::get('member_id'));
			if($my_points){
				echo "<p><strong>Current point(s):</strong></p>";
				$arr_points = [];
				foreach($my_points as $user_point){
					$point_name = capitalize($user_point->point_name);
					$arr_points[]= ['point_name' => $point_name,'point_id' => $user_point->point_id];
					echo "<strong>" .capitalize($point_name) . "</strong> <span class='text-danger'>" . formatQuantity($user_point->points) ."</span> ";
				}
				echo "<input type='hidden' value='".json_encode($arr_points)."' id='mem_point_reg' >";
			}
		}
			echo "</div>";
			echo "</div>";
		}
		echo "<input type='hidden' value='".json_encode($mem)."' id='mem_data'>";
		if($member_id){
			echo "<input type='hidden' value='".$memberData->data()->salestype."' id='mem_salestype'>";
		}

		echo $rethtml;
	}
	function toggleCheckItem(){
		$det_id = Input::get('order_det_id');
		if($det_id){
		$detcls = new Wh_order_details($det_id);
		$new = ($detcls->data()->is_check) ? 0 : 1;
		$detcls->update(array(
			'is_check' =>$new
		),$det_id);
		}
	}
	function getBranchRack(){
		$rack = new Rack();
		$branch_id = Input::get('branch_id');
		$user = new User();
		if($branch_id){
			$racks = $rack->getBranchRacks($branch_id);
		} else {
			$racks = $rack->getAllRacks($user->data()->company_id);
		}
		$ret = '';
		$ret .= "<option></option>";
		foreach($racks as $r){
		$ret .= "<option value='$r->id'>$r->rack</option>";
		}
		echo $ret;

	}
	function updateTechnician(){
		$id = Input::get('id');
		$technician_id = Input::get('technician_id');
		$update_req = new Item_service_request();
		$update_req->update([
			'technician_id' => $technician_id
		],$id);
		echo "Updated successfully.";
	}
	function getBundleItem(){
		$item_id = Input::get("item_id");
		$item_code = Input::get("item_description");
		$bundle = new Bundle();
		$list = $bundle->getBundleItem($item_id);
		if($list){
		 echo "<h4>$item_code</h4>";
		 echo "<table class='table'>";
		 echo "<tr><th>Item</th><th>Qty</th></tr>";
		 foreach($list as $l){
		 echo "<tr><td>$l->description</td><td>$l->child_qty</td></tr>";
		 }
		 echo "</table>";
		}
	}

	function creditToMember($pid = '',$pmem='',$pamount=0,$pdet =''){
		if($pid){
			$id = $pid;
			$member_id = $pmem;
			$details = $pdet;
			$amount = $pamount;
		} else {
			$id = Encryption::encrypt_decrypt('decrypt',Input::get('id'));
			$member_id = Input::get('member_id');
			$details = Input::get('details_data');
			$amount = Input::get('amount');
		}

		 $refund_amount = Input::get('refund_amount');
		 $overwrite_arr = Input::get('overwrite_arr');
		 $used_items = Input::get('used_items');

		$details = json_decode($details);
		$service_request = new Item_service_request($id);
		$rackcls = new Rack();
		$default_racks = $rackcls->getRackDefaults($service_request->data()->branch_id);
		$user = new User();
		$consumable_remarks = "";
		if($member_id){

			if(true){ // $amount
				$arrgood = [];
				$arrissues= [];
				$arrsurplus=[];
				foreach($details as $item){
					$arrBackInGood = [2];
					$arrBackInIssues = [5,7];
					$arrBacksurplus = [6,8];
					if(in_array($item->status,$arrBackInGood)){
						$arrgood[$item->item_id] = $item->qty;
					} else if(in_array($item->status,$arrBackInIssues)){
						$arrissues[$item->item_id] = $item->qty;
					}else if(in_array($item->status,$arrBacksurplus)){
						$arrsurplus[$item->item_id] = $item->qty;
					}

				}
					if($refund_amount && is_numeric($refund_amount)){
						$refund = new Refund();
						$refund->create(array('service_id' => $id,'amount' => $refund_amount,'created' => time()));
					}





				if(count($arrgood) > 0){
					$inventory = new Inventory();
					$tranfer_mon = new Transfer_inventory_mon();
					$tranfer_mon->create(array(
						'status' => 1,
						'is_active' =>1,
						'branch_id' =>$service_request->data()->branch_id,
						'company_id' =>$user->data()->company_id,
						'created' => time(),
						'modified' => time(),
						'payment_id' => $service_request->data()->id,
						'from_where' => 'From service return item'
					));

					$lastidservice = $tranfer_mon->getInsertedId();
					$withDet = false;

					foreach($arrgood as $key => $val){
						$item_id =$key;
						$qty =$val;
						$rack_id = $default_racks->good_rack;

						if(($item_id) && is_numeric($qty)){
							$withDet = true;
							$tranfer_mon_details = new Transfer_inventory_details();
							$tranfer_mon_details->create(array(
									'transfer_inventory_id' => $lastidservice,
									'rack_id_from' => 0,
									'rack_id_to' => $rack_id,
									'item_id' =>$item_id,
									'qty' => $qty,
									'is_active' => 1
							));
						}
					}

					if(!$withDet){
						$tranfer_mon->update(['is_active' => 0],$lastidservice);
					}
				}

				if(count($arrissues) > 0){
					$inv_issues = new Inventory_issue();
					$inv_mon = new Inventory_issues_monitoring();
					foreach($arrissues as $key => $val){
					$item_id= $key;
					$branch_id = $user->data()->branch_id;
					$des_rack_id =$default_racks->issues_rack;
					$convert_type = 1;
					$convert_qty = $val;
					$curinvissues = $inv_issues->getQty($item_id,$branch_id,$des_rack_id,$convert_type);
					if(isset($curinvissues->qty)){
						$cur_issues = $curinvissues->qty;
					} else {
						$cur_issues = 0;
					}
					if($inv_issues->checkIfItemExist($item_id,$branch_id,$user->data()->company_id,$des_rack_id,$convert_type)){
						$inv_issues->addInventory($item_id,$branch_id,$convert_qty,false,$des_rack_id,$convert_type);
					} else {
						$inv_issues->addInventory($item_id,$branch_id,$convert_qty,true,$des_rack_id,$convert_type);
					}
					$new_issues = $cur_issues + $convert_qty;
					$inv_mon->create(array(
						'item_id' => $item_id,
						'rack_id' => $des_rack_id,
						'branch_id' => $branch_id,
						'page' => 'admin/inventory_adjustments.php',
						'action' => 'Update',
						'prev_qty' => $cur_issues,
						'qty_di' => 1,
						'qty' => $convert_qty,
						'new_qty' => $new_issues,
						'created' => time(),
						'user_id' => $user->data()->id,
						'remarks' => 'Back damage item from service. Id # ' . $id,
						'is_active' => 1,
						'company_id' => $user->data()->company_id,
						'type' => $convert_type
					));
						$prodcheck = new Product($item_id);
						$consumable_remarks .= $prodcheck->data()->item_code . "<br>" .  $prodcheck->data()->description . "<br>Qty: " .$convert_qty . "<br>";
					}
				}
				if(count($arrsurplus) > 0){
					$inv_issues = new Inventory_issue();
					$inv_mon = new Inventory_issues_monitoring();
					foreach($arrsurplus as $key => $val){
					$item_id= $key;
					$branch_id = $user->data()->branch_id;
					$des_rack_id =$default_racks->surplus_rack;
					$convert_type = 4;
					$convert_qty = $val;
					$curinvissues = $inv_issues->getQty($item_id,$branch_id,$des_rack_id,$convert_type);
					if(isset($curinvissues->qty)){
						$cur_issues = $curinvissues->qty;
					} else {
						$cur_issues = 0;
					}
					if($inv_issues->checkIfItemExist($item_id,$branch_id,$user->data()->company_id,$des_rack_id,$convert_type)){
						$inv_issues->addInventory($item_id,$branch_id,$convert_qty,false,$des_rack_id,$convert_type);
					} else {
						$inv_issues->addInventory($item_id,$branch_id,$convert_qty,true,$des_rack_id,$convert_type);
					}
					$new_issues = $cur_issues + $convert_qty;
					$inv_mon->create(array(
						'item_id' => $item_id,
						'rack_id' => $des_rack_id,
						'branch_id' => $branch_id,
						'page' => 'admin/inventory_adjustments.php',
						'action' => 'Update',
						'prev_qty' => $cur_issues,
						'qty_di' => 1,
						'qty' => $convert_qty,
						'new_qty' => $new_issues,
						'created' => time(),
						'user_id' => $user->data()->id,
						'remarks' => 'Back surplus item from service. Id # ' . $id,
						'is_active' => 1,
						'company_id' => $user->data()->company_id,
						'type' => $convert_type
					));
					$prodcheck = new Product($item_id);
						$consumable_remarks .= $prodcheck->data()->item_code . "<br>" .  $prodcheck->data()->description . "<br>Qty: " .$convert_qty . "<br>";
					}
				}

				$user = new User();
				$now = time();
				$nextYear = strtotime(date('F Y') . " 1 year");
				$company_id = $user->data()->company_id;
				$item_id = 0; // static item id
				// add payment
				if($amount){
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
					$consumable_remarks = "From Service:<br>" . $consumable_remarks;
					$consumable_amount->create(array(
						'member_id' => $member_id,
						'item_id' => $item_id,
						'payment_id' => $paymentLastId,
						'from_payment_id' => $id,
						'amount' => $amount,
						'service_id' => $serviceLastId,
						'created' => $now,
						'modified' => $now,
						'is_active' => 1,
						'remarks' => $consumable_remarks
					));
				}

			}

			if($overwrite_arr){
				$overwrite_arr = json_decode($overwrite_arr);
				if(count($overwrite_arr)){
					foreach($overwrite_arr as $or){
						if($or->id && $or->price){
							$item_service_details = new Item_service_details();
							$item_service_details->update(
							[
								'adjustment_price' => $or->price,
								'orig_price' => $or->orig_price,
							],$or->id);
						}
					}
				}
			}
			if($used_items){
				$used_items = json_decode($used_items);
				if(count($used_items)){
					foreach($used_items as $ui){
						if($ui->id && is_numeric($ui->id)){
							$price = $ui->price ? $ui->price : 0;
							$service_used_item = new Service_item_use();
							$service_used_item->update(
								['price_override' => $price] , $ui->id
							);
						}
					}
				}
			}

			$service_request->update(array('status' => 4), $id);

			Log::addLog($user->data()->id,$user->data()->company_id,"Item Service: Process Request ID $id","ajax_query2.php");

			echo "Successfully credited to client.";
		}
	}
	function returnStocks(){
		$order_id = Input::get('order_id');
		$myOrder = new Wh_order($order_id);
		$user = new User();

		if($myOrder->data()->stock_out == 1 && $myOrder->data()->status == 3){
			$whorder = new Wh_order_details();
			$orders = $whorder->getOrderDetails($order_id);
			$inventory = new Inventory();
			$inv_mon = new Inventory_monitoring();
			foreach($orders as $item){
				if($item->is_bundle == 0){
					if($item->item_type == -1){
						$rackings =  json_decode($item->racking);

						foreach($rackings as $rack){

								if($inventory->checkIfItemExist($item->item_id,$myOrder->data()->branch_id,$user->data()->company_id,$rack->rack_id)){
									$curinventoryFrom = $inventory->getQty($item->item_id,$myOrder->data()->branch_id,$rack->rack_id);
									$currentqty = $curinventoryFrom->qty;
									$inventory->addInventory($item->item_id,$myOrder->data()->branch_id,$rack->qty,false,$rack->rack_id);
								} else {
									$currentqty = 0;
								}
								// monitoring
								$newqtyFrom = $currentqty + $rack->qty;
								$inv_mon->create(array(
									'item_id' => $item->item_id,
									'rack_id' => $rack->rack_id,
									'branch_id' => $myOrder->data()->branch_id,
									'page' => 'ajax/ajax_query2.php',
									'action' => 'Update',
									'prev_qty' => $currentqty,
									'qty_di' => 1,
									'qty' => $rack->qty,
									'new_qty' => $newqtyFrom,
									'created' => time(),
									'user_id' => $user->data()->id,
									'remarks' => 'Add inventory from rack (Order id #'.$order_id.')',
									'is_active' => 1,
									'company_id' => $user->data()->company_id
								));
						}
					}

				} else if ($item->is_bundle == 1){

						$bundle = new Bundle();
						$bundle_list = $bundle->getBundleItem($item->item_id);
						$bundleracking = json_decode($item->racking,true);

						if($bundle_list){
						foreach($bundle_list as $bl){
							 if($myOrder->data()->status== 3 && $myOrder->data()->stock_out == 1){
								$thisracking = json_decode($bundleracking[$bl->item_id_child]);

								foreach($thisracking as $racked){

									//$retrack[]= ['rack' => $racked->rack, 'qty' =>  $racked->qty,'stock_man' => $racked->stock_man];
									if($inventory->checkIfItemExist($bl->item_id_child,$myOrder->data()->branch_id,$user->data()->company_id,$racked->rack_id)){
																$curinventoryFrom = $inventory->getQty($bl->item_id_child,$myOrder->data()->branch_id,$racked->rack_id);
																$currentqty = $curinventoryFrom->qty;
																$inventory->addInventory($bl->item_id_child,$myOrder->data()->branch_id,$racked->qty,false,$racked->rack_id);
															} else {
																$currentqty = 0;
															}
															// monitoring
															$newqtyFrom = $currentqty + $racked->qty;
															$inv_mon->create(array(
																'item_id' => $bl->item_id_child,
																'rack_id' => $racked->rack_id,
																'branch_id' => $myOrder->data()->branch_id,
																'page' => 'ajax/ajax_query2.php',
																'action' => 'Update',
																'prev_qty' => $currentqty,
																'qty_di' => 1,
																'qty' => $racked->qty,
																'new_qty' => $newqtyFrom,
																'created' => time(),
																'user_id' => $user->data()->id,
																'remarks' => 'Add inventory from rack (Order id #'.$order_id.')',
																'is_active' => 1,
																'company_id' => $user->data()->company_id
															));
								}

						}
					}
				}
			}
			}
			$myOrder->update(array('stock_out' => 0),$order_id);
			echo "Request processed successfully.";
		}
	}

	function submitOrderService(){

		$id = Input::get('id');

		$cashier_trans = Input::get('cashier_trans');
		$walkin = Input::get('walk_in');
		$is_service_item = Input::get('is_service_item');
		$issue_sr = Input::get('issue_sr');
		$issue_ts = Input::get('issue_ts');

		$cashier_trans = ($cashier_trans) ? $cashier_trans : 0;
		$walkin = ($walkin) ? $walkin : 0;

		if($id && is_numeric($id)){
			$item_service = new Item_service_request($id);
			if($item_service->data()->status==4){
				// select item used
				$service_used_items = new Service_item_use();
				$used_items = $service_used_items->getUsedItems($id);
				if($used_items){
					// create order
					// what status ?
					$arr_stats = explode(',',$item_service->data()->history_status);
					$stats = $arr_stats[count($arr_stats)-1];
					$det_stats = $item_service->getStatuses($id);
					foreach($det_stats as $ds){
						if($ds->status == 4){
							$stats = 4;
						}
					}

					$order = new Wh_order();
					$user = new User();
					$now = time();
					$for_pick_up = 0;

					if($cashier_trans) $for_pick_up = 2;

					// get service remarks
					$rem_list = new Remarks_list();
					$remarksall = $rem_list->getServices($id,'service',$user->data()->company_id);
					$rmall = "";
					if($remarksall){
						foreach($remarksall as $rem){
							$rmall .= $rem->remarks . " ";
						}
					}
					if($is_service_item == 1){
						$is_service = $id;
					} else {
						$is_service = 0;
					}


					$order->create(array(
						'branch_id' => $item_service->data()->branch_id,
						'member_id' => $item_service->data()->member_id,
						'to_branch_id' => 0,
						'remarks' => $rmall,
						'client_po' => '',
						'shipping_company_id' => 0,
						'created' => $now,
						'company_id' => $user->data()->company_id,
						'user_id' => $user->data()->id,
						'is_active' => 1,
						'status' => 1,
						'for_pickup' => $for_pick_up,
						'is_reserve' => 0,
						'stock_out' => 1,
						'for_approval_walkin' => $walkin,
						'from_service' => $is_service
				));
				$lastItOrder = $order->getInsertedId();
				$memberTerms = new Member_term();
				if($item_service->data()->member_id){
					$memberDetails = new Member($item_service->data()->member_id);
				}
					$itemlist = [];
					foreach($used_items as $used){
						// create details

						$order_details = new Wh_order_details();
						$adjustmentcls = new Item_price_adjustment();
						$product = new Product($used->item_id);
						$price = $product->getPrice($used->item_id);
						$adjustment = $adjustmentcls->getAdjustment($item_service->data()->branch_id,$used->item_id);
						$memadj =$memberTerms->getAdjustment($item_service->data()->member_id,$used->item_id);
						$qty = $used->qty;
						$terms= 0;
						if($item_service->data()->member_id){
						$terms = $memberDetails->data()->terms;
						}
						$alladj = 0;
						if(count($memadj)){
							//$same_type = [];
							foreach($memadj as $m){
								$madj = $m->adjustment;
								$terms = $m->terms;
								if($m->type == 1){ // for every
								if($qty < 1 && $qty != 0){
								    if($m->qty == 1){
								      $x = $qty / $m->qty;
								    } else {
								          $x = 0;
								    }
							   } else {
							     $x = floor($qty / $m->qty);
							   }


									$madj = $madj * $x;
									$alladj += $madj;
								} else if ($m->type == 2){ // above qty
									if($qty >= $m->qty){
										$alladj += $madj;
									}
								}
							}
						}

						if(isset($adjustment->adjustment)){
							$adj_amount = $adjustment->adjustment;
						} else {
							$adj_amount = 0;
						}

						if($stats == 3){
							$total_price = ($price->price + $adj_amount) * $qty;
							$alladj = -1 * $total_price;
						}

						if($used->price_override != 0.00){
							$price_dif = $used->price_override - $price->price; // 500
							$alladj = $price_dif * $qty;
							$adj_amount = 0 ;
						}



						$order_details->create(array(
							'wh_orders_id' => $lastItOrder,
							'item_id' => $used->item_id,
							'price_id' => $price->id,
							'qty' => $qty,
							'original_qty' => $qty,
							'created' => $now,
							'modified' => $now,
							'price_adjustment' => $adj_amount,
							'company_id' => $user->data()->company_id,
							'is_active' => 1,
							'terms' => $terms,
							'member_adjustment' => $alladj
						));

						$member_terms = new Member_term();
						$member_terms->updateSingleUseTerms($item_service->data()->member_id,$used->item_id);


					}

					$item_service->update(['payment_id' => $lastItOrder],$item_service->data()->id);
					// update service item
					$service_used_items->updateUsedItems($id);
						if(!Configuration::thisCompany('cebuhiq') || ($issue_sr == 0 && $issue_ts == 0)){
							echo "Order submitted successfully.";

						}
				}

		if(Configuration::thisCompany('cebuhiq') && ($issue_sr == 1 || $issue_ts == 1)){

			$whorder = new Wh_order_details();
			$orders = $whorder->getOrderDetails($lastItOrder);
			// add payment
			$terminal_id = Input::get('terminal_id');
			$payment = new Payment();
			if(!$terminal_id) {
				die("Please set up terminal first.");
			}
			$terminal = new  Terminal($terminal_id);
			$scompany =$user->data()->company_id;
			$dt_paid = time();

			$payment->create(array(
				'created' => $dt_paid,
				'company_id' => $scompany,
				'is_active' => 1
			));

			$payment_lastid = $payment->getInsertedId();
			$newsales = new Sales();
			$myOrder  = new Wh_order($lastItOrder);
			$order_details = $myOrder->getFullDetails($lastItOrder);
			$overall_total = 0;
			foreach($orders as $order){
						// insert sales
						$total = ($order->qty * $order->adjusted_price) + $order->member_adjustment;
						$overall_total += $total;
						$indDiscount = 0;
						if($order->member_adjustment){
							 $indDiscount = $order->member_adjustment / $order->qty;
						}


						$adjustedPrice = $order->adjusted_price + $indDiscount;
						$racking = $order->racking;
						$rack_json = [];
						$priceAdjustment = $order->price_adjustment * $order->qty;
						$memberAdjustment = $order->member_adjustment;
						$discount_type = computeDiscountType($indDiscount,$order->adjusted_price,$order->hide_discount);
						$desc = $order->description;

						$itemlist[] = [

								'original_price' => $order->adjusted_price,
								'orig_unit'=>$order->unit_name,
								'unit_name'=>escape($order->unit_name),
								'item_code'=>escape($order->item_code),
								'description'=>strtolower($desc),
								'barcode'=>escape($order->barcode),
								'qty'=>escape(formatQuantity($order->qty)),
								'price'=>escape($adjustedPrice),
								'discount'=>escape($order->member_adjustment),
								'total'=>escape($total),'racking' => $rack_json,
								'is_freebie' => $order->is_freebie,
								'discount_type' => $discount_type];

								// add sales
							$nextsr = '';
							$nextts = '';

							if($issue_sr == 1){
								$nextsr = ($terminal->data()->sr + 1);
							}
							if($issue_ts == 1){
								$nextts = ($terminal->data()->ts + 1);
							}

							$newsales->create(array(
							'terminal_id' => $terminal_id,
							'invoice' => 0,
							'sv' => 0,
							'dr' => 0,
							'ir' => 0,
							'sr2' => $nextsr,
							'ts' => $nextts,
							'pref_inv' => '',
							'pref_dr' => '',
							'pref_ir' => '',
							'pref_sv' => '',
							'suf_inv' => '',
							'suf_dr' => '',
							'suf_ir' => '',
							'suf_sv' => '',
							'item_id' => $order->item_id,
							'price_id' => $order->price_id,
							'qtys' =>  $order->qty,
							'discount' => 0,
							'store_discount' => 0,
							'adjustment' => $priceAdjustment,
							'member_adjustment' => $memberAdjustment,
							'terms' => $order->terms,
							'company_id' => $user->data()->company_id,
							'cashier_id' => $user->data()->id,
							'sold_date' => $dt_paid,
							'payment_id' =>$payment_lastid,
							'member_id' => $myOrder->data()->member_id,
							'warranty' => $order->warranty,
							'station_id' => 0,
							'sales_type' => $order_details->salestype,
							'adjustment_remarks' => $order->adjustment_remarks,
							'from_od' => 1
						));
					}

					if($overall_total){
						$pcredit = new Member_credit();
						$pcredit->create(array(
							'amount' =>$overall_total,
							'is_active' => 1,
							'created' => $dt_paid,
							'modified' => $dt_paid,
							'payment_id' => $payment_lastid,
							'member_id' => $myOrder->data()->member_id,
							'is_cod' => 0
						));
					}
					if($issue_sr == 1){
						$terminal->update(
						[
							'sr' => $nextsr
						] , $terminal->data()->id
						);

						$myOrder->update(
							[
							'status'=> 2,
							'sr' => $nextsr

							], $myOrder->data()->id
						);
					}
					if ($issue_ts == 1){
						$terminal->update(
							[
								'ts' => $nextts
							] , $terminal->data()->id
							);

							$myOrder->update(
								[
								'status'=> 2,
								'ts' => $nextts

								], $myOrder->data()->id
							);
					}


					$membername = ucwords($order_details->mln);
					$cashiername = ucwords($order_details->uln . ", " . $order_details->ufn . " " . $order_details->umn);
					$remarks_append = "";
					if($order_details->shipping_company_name){
						$remarks_append .= "<br>" . $order_details->shipping_company_name;
					}
					if($order_details->branch_name){
						$remarks_append .= "<br>" . $order_details->branch_name;
					}

					if($order_details->client_po){
						$remarks_append .= "<br>PO#".$order_details->client_po;
					}
					$remarks = $order_details->remarks . $remarks_append;

					$terms = ($order_details->terms) ? $order_details->terms : '';

					$other_info_append = "";
					if($order_details->mfn || $order_details->mmn || $order_details->cel_number || $order_details->contact_number){
						$client_number = "";
						if($order_details->cel_number){
							$client_number .= $order_details->cel_number;
						}
						if($order_details->contact_number){
							if($client_number){
								$client_number .=",";
							}
							$client_number .= $order_details->contact_number;
						}
						$other_info_append = "<span style='display:block;font-size:12px;padding:0px;margin:0px;'>Contact Person: " .ucwords($order_details->mfn . " " . $order_details->mmn) . "</span><span style='display:block;font-size:12px;padding:0px;margin:0px;'>Contact Number: " .ucwords($client_number) . "</span>";
					}

					$address = $order_details->personal_address . $other_info_append;
					$sales_type_name = ($order_details->sales_type_name) ? $order_details->sales_type_name : '';

					$finalarr = [];
					$finalarr['member_name'] = $membername;
					$finalarr['client_po'] = $order_details->client_po;
					$finalarr['tin_no'] = $order_details->tin_no;
					$finalarr['is_cod'] = 0;
					$finalarr['is_charge'] = 0;
					$finalarr['dr'] = $order_details->dr;
					$finalarr['pr'] = $order_details->pr;
					$finalarr['sv'] = $order_details->sv;
					$finalarr['remarks'] = $remarks;
					$finalarr['special_discount_total'] = '';
					$finalarr['cashier_name'] =$cashiername;
					$finalarr['member_id'] = $order_details->member_id;
					$finalarr['station_name'] = $address;
					$finalarr['consumable_total'] = '';
					$finalarr['station_id'] = '';
					$finalarr['station_address'] = '';
					$finalarr['terms'] = $terms;
					$finalarr['sales_type'] = $sales_type_name;
					$finalarr['date_sold'] =  date('m/d/Y');
					$due =  date('m/d/Y');
					if($terms){
						$finalarr['due_date'] = date('m/d/y',strtotime($due . $terms . " days"));
					} else {
						$finalarr['due_date'] = '';
					}

					$finalarr['item_list'] = $itemlist;
					$finalarr['order_id'] = $lastItOrder;
					$finalarr['shipping_company_name'] = $order_details->shipping_company_name;
					echo json_encode($finalarr);
				} // end cebuhiq
			}
		}

	}

	function getWholeAndDecimal($n=0){
		if($n && is_numeric($n)){
			$whole = floor($n);
			$fraction = number_format($n - $whole,3);
			return ['whole'=>$whole,'decimal'=>$fraction];
		}
	}

	function discountByCategory($item_id,$member_id,$price){

				$memdis = new Member_category_discount();
				$memcategdis= $memdis->hasDiscount($item_id,$member_id);
				$totaladd = 0;

				$computed_price = $price;
				if(isset($memcategdis->discount_1)  && $memcategdis->discount_1){
					$discount_1 = $memcategdis->discount_1 * 0.01;
					$toadd = $computed_price * $discount_1;
					$totaladd += $toadd;
					$computed_price = $computed_price - $toadd;
					if(isset($memcategdis->discount_2)  && $memcategdis->discount_2){
						$discount_2 = $memcategdis->discount_2 * 0.01;
						$toadd = $computed_price * $discount_2;
						$computed_price = $computed_price - $toadd;
						$totaladd += $toadd;
						if(isset($memcategdis->discount_3)  && $memcategdis->discount_3){
							$discount_3 = $memcategdis->discount_3 * 0.01;
							$toadd = $computed_price * $discount_3;
							$computed_price = $computed_price - $toadd;
							$totaladd += $toadd;
							if(isset($memcategdis->discount_4)  && $memcategdis->discount_4){
								$discount_4 = $memcategdis->discount_4 * 0.01;
								$toadd = $computed_price * $discount_4;
								$totaladd += $toadd;
							}
						}

					}
					$totaladd = $totaladd * -1;
					return $totaladd;
				}

			return 0;
	}
	function __itemMemberAdjustment($member_id,$item_id,$qty){

			$remarks_for_adjustment = "";
			$alladj=0;
			if($member_id){
				$memberTerms = new Member_term();
				$memadj =$memberTerms->getAdjustment($member_id,$item_id);
				$total_member_adjustment = 0;

				if(count($memadj)){
					$alladjInd = 0;
					$alladjAbove = 0;
					foreach($memadj as $m){
						$madj = $m->adjustment;
						if(!$madj) {
							continue;
						}
						if($m->remarks){
							$remarks_for_adjustment .= $m->remarks . "***";
						}

						if($m->type == 1){ // for every
						   if($qty < 1 && $qty != 0){
						    if($m->qty == 1){
						      $x = $qty / $m->qty;
						    } else {
						          $x = 0;
						    }

						   } else {
						     $x = floor($qty / $m->qty);
						   }

							$madj = $madj * $x;
							$total_member_adjustment += $madj;
							$alladjInd += $madj;
						} else if ($m->type == 2){ // above qty

							if($qty >= $m->qty){
								if($m->discount_type == 0){
									$alladjAbove += $madj;
									$total_member_adjustment += $madj;
								} else {
									$madj = $madj * $qty;
									$alladjAbove += $madj;
									$total_member_adjustment += $madj;
								}
							}


						}
					}
					$remarks_for_adjustment = rtrim($remarks_for_adjustment,'***');
					if($alladjAbove){
						$alladj = $alladjAbove;
					} else if($alladjInd){
						$alladj = $alladjInd;
					}
				}
			}

			return ['adjustment' => $alladj,'remarks'=> $remarks_for_adjustment];
	}

	function getInventoryPercentageWallet($member_id,$branch_id){

			if(!$member_id || ! $branch_id) return false;
			$memberDetails = new Member($member_id);
			$k_type_to = 0;
			$k_type_from = 0;
			if($memberDetails->data()->k_type){
				$k_type_to = $memberDetails->data()->k_type;
			}

			$branch = new Branch();
			$k_type_order_from = $branch->branchMember($branch_id);
			if($k_type_order_from){
				$k_type_from = $k_type_order_from->k_type;
			}

				$wallet_config = new Wallet_config();
				$wallet_configs = $wallet_config->get_active('wallet_configuration',['1','=','1']);
				$arr_config = [];
				if($wallet_configs){
					foreach($wallet_configs as $wc){
						$arr_config[$wc->key] = $wc->value;
					}
				}
				$_SESSION['wallet_config'] = $arr_config;



			if($k_type_to == 1 && !$k_type_from){ // distributor
				return $_SESSION['wallet_config']['inventory_depot_to_ho'];
			} else if($k_type_to == 2 && !$k_type_from){ // franchisee
				return $_SESSION['wallet_config']['inventory_franchisee_to_ho'];
			} else if($k_type_to == 2 && $k_type_from == 1){ // agent
				return $_SESSION['wallet_config']['inventory_franchisee_to_depot'];
			}
			return false;

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

				if($set['remaining'] && $set['remaining'] >= $qty  || $item_cls->data()->item_type != -1){
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
				if($item_cls->data()->item_type == 1){
					$valid = 1;
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

					$set = remainingSet($item_id,$branch_id);

					if($set['remaining'] && $set['remaining'] >= $qty ){
						$valid = 1;
						$rem = $_SESSION['machine_qty'];
					} else {

					}

				}

				if($item_cls->data()->item_type == 1 ){
					$valid = 1;
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
			$ids_raw = "";
			foreach($spare_parts as $spare){
				$ids_raw = $spare->item_id_raw .",";

				$pending_spare_qty = $whorder->pendingSpare($spare->item_id_raw,$branch_id); // get pending qty raw

				$assemble_spare_qty = 0; $whorder->spareWithAssemble($spare->item_id_raw,$branch_id);
				$assemble_qty_free = isset($assemble_spare_qty->assemble_qty) ? $assemble_spare_qty->assemble_qty : 0;
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
				$remaining_composite = ($st_composite+$assemble_qty_free) - ($pending_spare_qty->pending_qty + $service_qty);

				if($remaining_composite < 0) $remaining_composite = 0;
				$arr_inv[] = ['item_code' => $spare->item_code,'item_id_child' => $spare->item_id_raw,'remaining' => $remaining_composite,'current_stock' => $st_composite,'pending_order' => $pending_spare_qty->pending_qty,'pending_service' => $service_qty,'needed' => $spare->qty];
			}
			$ids_raw = rtrim($ids_raw,",");

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
				$current_pending_order = $whorder->getPendingOrder($bun->item_id_child,$branch_id);
				$od_add = 0;
				if($current_pending_order && isset( $current_pending_order->od_qty )){
					$od_add = $current_pending_order->od_qty;
				}
				if($pending_bundle_qty && isset( $pending_bundle_qty->pending_qty )){
					$pending_bundle_qty->pending_qty  += $od_add;
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

		$item_for_order_cls = new Assemble_item_for_order();
		$item_for_order = $item_for_order_cls->getItem($item_id);
		$ass_subtract_total = 0;
		$total_all = 0;
		if(isset($item_for_order->item_id) && !empty($item_for_order->item_id)){
			$for_order = $inv->getAllQuantity($item_id,$branch_id,0);
			$total_all = $for_order->totalQty;
			$ass_subtract_total = $item_for_order->min_qty;
		}


		$stock = $inv->getAllQuantity($item_id,$branch_id,$excempt_tags);

		if($total_all){
			$is_still_allowed = $total_all - $stock->totalQty;
			if($is_still_allowed >= $ass_subtract_total){
				$stock->totalQty = $total_all - $ass_subtract_total;
			}
		}

		$whorder = new Wh_order();
		$item_service = new Service_request_item();
		$current_pending_order = $whorder->getPendingOrder($item_id,$branch_id);
		$current_pending_service = $item_service->getPendingRequest($item_id,$branch_id);
		$current_pending_in_bundle = $whorder->pendingInBundle($item_id,$branch_id);

		$current_pending_in_assemble = $whorder->pendingInAssemble($item_id,$branch_id);


		$cur = 0;
		$st = 0;
		$service_qty = 0;
		$pending_in_bundle = 0;
		$pending_in_assemble = 0;

		if(isset($current_pending_in_bundle->pending_qty)){
			$pending_in_bundle =$current_pending_in_bundle->pending_qty ;
		}

		if(isset($current_pending_in_assemble->pending_qty)){
			$pending_in_assemble =$current_pending_in_assemble->pending_qty ;
		}

		if(isset($stock->totalQty)){
			$st =$stock->totalQty ;
		}

		if(isset($current_pending_order->od_qty)){
			$cur =$current_pending_order->od_qty ;
		}

		if(isset($current_pending_service->service_qty)){
			$service_qty =$current_pending_service->service_qty ;
		}

		$remaining = $st - ($cur + $service_qty + $pending_in_bundle + $pending_in_assemble);

		return ['remaining' => $remaining, 'current_stock' => $st,'pending_order'=>$cur,'pending_service'=>$service_qty];
	}

function returnNav($page, $total_pages, $limit, $stages) {
		if($page == 0) {
			$page = 1;
		}
		$prev = $page - 1;
		$next = $page + 1;
		$lastpage = ceil($total_pages / $limit);
		$LastPagem1 = $lastpage - 1;


		$paginate = '';
		if($lastpage > 1) {

			$paginate .= "<div style='padding:3px;' class='text-right'><ul class='pagination' >";

			if($page > 1) {
				$paginate .= "<li><a href='#'  class='paging' page='$prev' style='padding:5px'><span class='hidden-xs'>PREV</span><span class='visible-xs'><span class='glyphicon glyphicon-chevron-left'></span></span></a></li>";
			} else {
				$paginate .= "<li class='disabled'><span class='disabled' style='padding:5px'><span class='hidden-xs'>PREV</span><span class='visible-xs'><span class='glyphicon glyphicon-chevron-left'></span></span></span></span></li>";
			}


			if($lastpage < 7 + ($stages * 2)) {
				for($counter = 1; $counter <= $lastpage; $counter++) {
					if($counter == $page) {
						$paginate .= "<li class='active'><span class='current' style='padding:5px'>$counter</span></li>";
					} else {
						$paginate .= "<li><a href='#'  class='paging' page='$counter' style='padding:5px'>$counter</a></li>";
					}
				}
			} elseif($lastpage > 5 + ($stages * 2)) {

				if($page < 1 + ($stages * 2)) {
					for($counter = 1; $counter < 4 + ($stages * 2); $counter++) {
						if($counter == $page) {
							$paginate .= "<li class='active'><span class='current' style='padding:5px'>$counter</span></li>";
						} else {
							$paginate .= "<li><a href='#'  class='paging' page='$counter' style='padding:5px'>$counter</a></li>";
						}
					}
					$paginate .= "<li><span style='padding:5px'>...</span></li>";
					$paginate .= "<li><a href='#'   class='paging' page='$LastPagem1' style='padding:5px'>$LastPagem1</a></li>";
					$paginate .= "<li><a href='#' class='paging' page='$lastpage' style='padding:5px'>$lastpage</a></li>";
				} elseif($lastpage - ($stages * 2) > $page && $page > ($stages * 2)) {
					$paginate .= "<li><a href='#' class='paging' page='1'  style='padding:5px'>1</a></li>";
					$paginate .= "<li><a href='#' class='paging' page='2'  style='padding:5px'>2</a></li>";
					$paginate .= "<li><span style='padding:5px'>...</span></li>";
					for($counter = $page - $stages; $counter <= $page + $stages; $counter++) {
						if($counter == $page) {
							$paginate .= "<li class='active'><span class='current' style='padding:5px'>$counter</span></li>";
						} else {
							$paginate .= "<li><a href='#' class='paging' page='$counter'  style='padding:5px'>$counter</a></li>";
						}
					}
					$paginate .= "<li><span  style='padding:5px'>...</span></li>";
					$paginate .= "<li><a href='#' class='paging' page='$LastPagem1' style='padding:5px'>$LastPagem1</a></li>";
					$paginate .= "<li><a  href='#'  class='paging' page='$lastpage' style='padding:5px'>$lastpage</a></li>";
				} else {
					$paginate .= "<li><a href='#' class='paging' page='1' style='padding:5px'>1</a></li>";
					$paginate .= "<li><a href='#' class='paging' page='2' style='padding:5px'>2</a></li>";
					$paginate .= "<li><span style='padding:5px'>...</span></li>";
					for($counter = $lastpage - (2 + ($stages * 2)); $counter <= $lastpage; $counter++) {
						if($counter == $page) {
							$paginate .= "<li class='active'><span class='current' style='padding:5px'>$counter</span></li>";
						} else {
							$paginate .= "<li><a href='#' class='paging' page='$counter'  style='padding:5px'>$counter</a></li>";
						}
					}
				}
			}


			if($page < $counter - 1) {
				$paginate .= "<li><a href='#' class='paging' page='$next' style='padding:5px'><span class='hidden-xs'>NEXT</span><span class='visible-xs'><span class='glyphicon glyphicon-chevron-right'></span></span></a></li>";
			} else {
				$paginate .= "<li class='disabled'><span class='disabled' style='padding:5px'><span class='hidden-xs'>NEXT</span><span class='visible-xs'><span class='glyphicon glyphicon-chevron-right'></span></span></span></li>";
			}

			$paginate .= "</ul></div><div style='clear: both;'></div>";


		}
		// echo $total_pages.' Results';
		return $paginate;
	}

	function updateConsumableAmountBatch(){

		$ids = json_decode(Input::get('ids'), true);
		$amount = Input::get('amount');
		if($ids && is_numeric($amount)){
			foreach($ids as $id){
				$consumables = new Consumable_amount();
				$consumables->update(array(
					'amount' => $amount
				),$id);

				$user = new User();

				Log::addLog($user->data()->id,$user->data()->company_id,"Update Consumable ID $id","ajax_query2.php");
			}
		}


		echo "Updated Successfully";

	}