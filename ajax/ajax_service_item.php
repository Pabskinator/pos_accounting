<?php
	include 'ajax_connection.php';
	$functionName = Input::get("functionName");

	if(function_exists($functionName)){
		$functionName();
	}
	function submitMeasurement(){
		$id = Encryption::encrypt_decrypt('decrypt',Input::get('id'));
		$arr = json_decode(Input::get('arr'));
		$date_measured = Input::get('date_measured');
		$time_measured = Input::get('time_measured');
		$date_measured_to = Input::get('date_measured_to');
		$time_measured_to = Input::get('time_measured_to');
		$troubleshooting_details = Input::get('troubleshooting_details');
		$technical_remarks = Input::get('technical_remarks');

		if($id && $date_measured && $arr){
			$measure = new Service_measurement_test();
			$service = new Item_service_request($id);
			$date_measured = strtotime($date_measured . " " . $time_measured );
			$date_measured_to = strtotime($date_measured_to . " " . $time_measured_to);
			$now = time();

			foreach($arr as $a) {
				$measure->create(['service_id' => $id,'service_measurement_id'=>$a->id,'val' => $a->val,'date_measured' => $date_measured,'date_measured_to' => $date_measured_to,'created'=> $now]);
			}

			if($troubleshooting_details || $technical_remarks){
				$det = $service->data()->troubleshooting_details . " " .$troubleshooting_details;
				$tech = $service->data()->techinal_remarks .  " " . $technical_remarks;

				$service->update(
					['troubleshooting_details' => $det,'technical_remarks' => $tech], $id
				);
			}

			echo "Record updated successfully.";

		}

	}
	function addMeasurement(){
		$id = Encryption::encrypt_decrypt('decrypt',Input::get('id'));
		if(is_numeric($id)){
			// get current measurement

			// include form
				// select lahat ng measurement
			$user = new User();
			$service_measurement = new Service_measurement();
			$service_measurements = $service_measurement->get_active('service_measurements',array('company_id' ,'=',$user->data()->company_id));
			if($service_measurements){
				echo "<div class='row'>";
				echo "<div class='col-md-3'><div class='form-group'>Date From: <input type='text' class='form-control' id='date_measured' placeholder='Enter Date'><span class='help-block'>Ex. (01/01/2018)</span></div></div>";
				echo "<div class='col-md-3'><div class='form-group'>Time From: <input type='text' class='form-control' id='time_measured' placeholder='Enter Time'><span class='help-block'>Ex. (18:30) 24 Hour format</span></div></div>";
				echo "<div class='col-md-3'><div class='form-group'>Date To: <input type='text' class='form-control' id='date_measured_to' placeholder='Enter Date'><span class='help-block'>Ex. (01/01/2018)</span></div></div>";
				echo "<div class='col-md-3'><div class='form-group'>Time To: <input type='text' class='form-control' id='time_measured_to' placeholder='Enter Time'><span class='help-block'>Ex. (18:30) 24 Hour format</span></div></div>";
				echo "</div>";
				echo "<div class='row'>";
				foreach($service_measurements as $m){
					echo "<div class='col-md-3'><div class='form-group'>$m->name: <input type='text' data-id='$m->id' placeholder='$m->name' class='form-control formMeasurement'></div></div>";
				}
				echo "<div class='col-md-12'><div class='form-group'><textarea class='form-control' name='technical_remarks' id='technical_remarks' cols='30' rows='2' placeholder='Technical Remarks'></textarea></div></div>";

				echo "<div class='col-md-12'><div class='form-group'><textarea class='form-control' name='troubleshooting_details' id='troubleshooting_details' cols='30' rows='2' placeholder='Troubleshooting Details'></textarea></div></div>";
				echo "</div>";

				echo "<br><button class='btn btn-default' data-id='". Encryption::encrypt_decrypt('encrypt',$id)."' id='btnSubmitMeasurement'>Submit</button>";

				echo "<hr>";

				$service_measurement_test = new Service_measurement_test();
				$data = $service_measurement_test->getMeasurement($id);

				$arr= [];
				if($data){
					$service_item_request = new Item_service_request($id);
					foreach($data as $d){
						$arr[$d->date_measured."|".$d->date_measured_to][] = $d;
					}
					echo "<h5>Measurements</h5>";
					echo "<div class='row'>";
					foreach($arr as $d => $mes){
						echo "<div class='col-md-6'>";
						echo "<div class='panel panel-default'>";
							$dt_ex = explode("|",$d);
							echo "<div class='panel-body'>";
							echo "<ul class='list-group'>";
							echo "<li class='list-group-item active'><strong>Date From: ". date('m/d/Y H:i:s A',$dt_ex[0]). "</strong></li>";
							echo "<li class='list-group-item active'><strong>Date To: ". date('m/d/Y H:i:s A',$dt_ex[1]). "</strong></li>";
							foreach($mes as $me){
								echo "<li class='list-group-item'>$me->name : <span class='text-danger'>$me->val</span></li>";
							}
							echo "<li class='list-group-item text-danger'>Troubleshooting: ".$service_item_request->data()->troubleshooting_details."</li>";
							echo "<li class='list-group-item text-danger'>Technical Remarks: ".$service_item_request->data()->technical_remarks."</li>";
							echo "</ul>";
							echo "</div>";
						echo "</div>";
						echo "</div>";
					}
					echo "</div>";

				} else {
					echo "<div class='alert alert-info'>No measurement yet.</div>";
				}

			} else {
				echo "Update measurement first.";
			}
			// display as textbox

		}
	}
	function checkAvailabilityOfItem(){

		$item_id = Input::get('item_id');
		$branch_id = Input::get('branch_id');
		$qty = Input::get('qty');
		$item = new Product($item_id);
		if($item->data()->item_type == -1){
			$availability = getReservedStocks($item_id,$branch_id,$qty);
			if($availability && $availability['message']){
				if(!$availability['success']){
					echo $final_message =  $availability['message'];
				} else {
					echo 1;
				}
				//echo $remaining = $availability['remaining'];
			}
		} else {
			echo 1;
		}


	}

	function inventory_racking($order_id=0,$qty=0,$item_id = 0,$branch_id=0,$deduct_prev = false,$tags=0,$item_ex = false){
		$inv = new Inventory();
		$qty_racks = [];
		$insufficient = false;
		$inv_racks = $inv->get_racking($item_id,$branch_id,$tags,$item_ex);

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
	function saveServiceItem(){
		$data = Input::get('arr');
		$id = Encryption::encrypt_decrypt('decrypt',Input::get('id'));
		if(is_numeric($id)){
			$data = json_decode($data);
			$service_cls = new Service_request_item();
			$user = new User();
			$now = time();

			foreach($data as $d){
				$service_cls->create(array(
					'item_id' => $d->item_id,
					'qty' => $d->qty,
					'is_active' => 1,
					'company_id' => $user->data()->company_id,
					'user_id' => $user->data()->id,
					'status' => 1,
					'service_id' => $id,
					'created' => $now
				));
			}

			Log::addLog(
				$user->data()->id,$user->data()->company_id,
				"Item Service: Request Needed Items Service ID $id",
				"ajax_service_item.php"
			);

			echo "Item requested successfully.";
		}
	}


	function detailedInfo(){
		$id = Encryption::encrypt_decrypt('decrypt',Input::get('id'));
		$item_service = new Item_service_request();
		$details = $item_service->getFullDetails($id);

		$data_request = [];
		$created= date('m/d/Y',$details->created);
		$name = ucwords($details->firstname . " " . $details->lastname);
		$branch_name = $details->branch_name;
		$remarks = $details->remarks;
		$troubleshooting_details = $details->troubleshooting_details;
		$technical_remarks = $details->technical_remarks;
		$technician_lbl = "None";
		$tech_print = "";
		if($details->technician_id){
			$techs = explode(',',$details->technician_id);
			$technician_lbl = '';
			foreach($techs as $tech){
				$technician = new Technician($tech);
				$technician_lbl .=  "<strong class='label label-primary'>".$technician->data()->name."</strong> ";
			}
		}
		// get request info
		$data_request=[
			'id' => $details->id,
			'member_name' => $details->member_name,
			'personal_address' => $details->personal_address,
			'service_type_name' => $details->service_type_name,
			'created' =>$created,
			'name' =>$name,
			'branch_name' =>$branch_name,
			'tech' =>$technician_lbl,
			'troubleshooting_details' =>$troubleshooting_details,
			'technical_remarks' =>$technical_remarks,
			'remarks' =>$remarks,
		];


		// get remarks


		$ref_table= 'service';
		$ret_remarks = "None";
		if(is_numeric($id)){
			$rem_list = new Remarks_list();
			$user = new User();
			$remarks = $rem_list->getServices($id,$ref_table,$user->data()->company_id);
			if($remarks){
				$ret_remarks = "<ul class='timeline'>";
						$ctr = 1;
						foreach($remarks as $rem){
							$cls ="";
						if($ctr % 2 != 0){
						$cls = "timeline-inverted";
						}
						$ctr++;
						$ret_remarks .= "<li class='$cls'>";
						$ret_remarks .= " <div class='timeline-badge primary'><i class='glyphicon glyphicon-list'></i></div>";
						$ret_remarks .= "<div class='timeline-panel'> <div class='timeline-heading'> <p><i class='fa fa-user'></i><span class='text-primary'>".ucwords($rem->firstname . " " . $rem->lastname)."</span></p><small class='span-block'>".date('m/d/Y H:i:s A',$rem->created)."</small> </div> <div class='timeline-body text-danger'>". $rem->remarks . " </div> </div>";
						$ret_remarks .= "</li>";


						}
				$ret_remarks .= "</ul>";
			}
		}

		// get time log

		$secondary = [
			'Service Report Validation Schedule',
			'SO Creation And Dispatching',
			'For Reporting',
			'CCD Verification',
			'Close',
			'Hold',
			'Cancelled'
		];

			$cls_service_date_log = new Service_date_log();
			$service_date_log = $cls_service_date_log->getList($id);
			$timelog="None";

			if($service_date_log){
				$timelog = "";
				foreach($service_date_log as $dtlog){
					$timelog .= "<p>".$secondary[$dtlog->status]."<br><i class='fa fa-user'></i> <small class='text-danger'>".ucwords($dtlog->firstname . " ". $dtlog->lastname)."</small><br><i class='fa fa-calendar'></i> <small class='text-danger'>".date('m/d/Y h:i:s A',$dtlog->dt)."</small></p>";
				}
			}


		// item used

		$service_used_items = new Service_item_use();
		$used_items = $service_used_items->getUsedItems($id);
		$lbl_item = "None";
		if($used_items){
			$lbl_item = "";
			$lbl_item .= "<table class='table'>";
			$lbl_item .= "<tr><th>Item code</th><th>Description</th><th>Quantity</th></tr>";

			foreach($used_items as $uitem){
				$lbl_item .= "<tr><td style='border-top:1px solid #ccc;'>$uitem->item_code</td><td style='border-top:1px solid #ccc;'>$uitem->description</td><td style='border-top:1px solid #ccc;'>$uitem->qty</td></tr>";
			}
			$lbl_item .= "</table>";
		}

		// measurement

		$service_measurement_test = new Service_measurement_test();
		$data = $service_measurement_test->getMeasurement($id);
		$lbl_measurement = "None";
		$arr= [];
		if($data) {
			foreach($data as $d) {
				$arr[$d->date_measured . "|" . $d->date_measured_to][] = $d;
			}
			$lbl_measurement = "";
			$lbl_measurement .= "<div class='row'>";
			foreach($arr as $d => $mes) {
				$lbl_measurement .= "<div class='col-md-12'>";
				$lbl_measurement .= "<div class='panel panel-default'>";
				$dt_ex = explode("|", $d);
				$lbl_measurement .= "<div class='panel-body'>";
				$lbl_measurement .= "<ul class='list-group'>";
				$lbl_measurement .= "<li class='list-group-item active'><strong>Date From: " . date('m/d/Y H:i:s A', $dt_ex[0]) . "</strong></li>";
				$lbl_measurement .= "<li class='list-group-item active'><strong>Date To: " . date('m/d/Y H:i:s A', $dt_ex[1]) . "</strong></li>";
				foreach($mes as $me) {
					$lbl_measurement .= "<li class='list-group-item'>$me->name : <span class='text-danger'>$me->val</span></li>";
				}
				$lbl_measurement .= "</ul>";
				$lbl_measurement .= "</div>";
				$lbl_measurement .= "</div>";
				$lbl_measurement .= "</div>";
			}
			$lbl_measurement .= "</div>";
		}
		echo json_encode(['request' => $data_request,'remarks'=>$ret_remarks,'timelog'=>$timelog,'used_item' => $lbl_item,'measurement'=>$lbl_measurement]);
	}
	function getServiceDetails(){
		$id = Encryption::encrypt_decrypt('decrypt',Input::get('id'));
		if(is_numeric($id)){
			$service_cls = new Service_request_item();
			$my_request = new Item_service_request($id);
			$member_cls = new Member($my_request->data()->member_id);
			$details = $service_cls->getDetails($id);
			$get_stats = $service_cls->stillPending($id);
			$branch_name = Input::get('branch_name');
			$technician_lbl = "None";
			$tech_print = "";
			if($my_request->data()->technician_id){
				$techs = explode(',',$my_request->data()->technician_id);
				$technician_lbl = '';
				foreach($techs as $tech){
					$technician = new Technician($tech);
					$technician_lbl .=  "<strong class='span-block'>".$technician->data()->name."</strong>";
					$tech_print .= $technician->data()->name . ", ";
				}
			}
			$tech_print = rtrim($tech_print,", ");


			if($details){
				$status = ['','Pending','Release','Liquidated'];
				$rf_id_con = "";
				if(Configuration::thisCompany('cebuhiq')){
					$rf_id_con = "<input class='form-control' id='rf_id' placeholder='Ref ID'>";
				}
				echo "<h3>Requested Item</h3>";
				//echo "<p>Status: </p>";
				$cur_stat = 0;
				$req_by = "";
				echo "<div  class='row' style='margin-bottom:5px;'>
						<div class='col-md-3'></div>
						<div class='col-md-4'></div>
						<div class='col-md-3'>$rf_id_con</div>
						<div class='col-md-2'>
							<button  data-contact_address='".$my_request->data()->contact_address."'  data-contact_number='".$my_request->data()->contact_number."' data-contact_person='".$my_request->data()->contact_person."' data-address='".$member_cls->data()->personal_address."' data-client='".$member_cls->data()->lastname."'  data-id='$id'  data-tech='$tech_print' data-branch_name='$branch_name' id='printRequestedItem' class='btn btn-default'><i class='fa fa-print'></i> Print</button>
						</div>
						</div>";
				echo "<table id='tbl_service_request' class='table table-bordered'>";
				echo "<thead>";
				echo "<tr><th>Item code</th><th>Description</th><th>Quantity</th><th></th></tr>";
				echo "</thead>";
				echo "<tbody>";
				$prod = new Product();

				$mem_price_group = new Member_price_group();
				$mem_price = $mem_price_group->getPriceGroup($my_request->data()->member_id);
				$price_group_id = 0;
				if(isset($mem_price->price_group_id)){
					$price_group_id =$mem_price->price_group_id;
				}
				$adjcls = new Item_price_adjustment();
				$prod = new Product();
				foreach($details as $det){
					$is_bundle = $det->is_bundle;

					if($is_bundle == 1){

						$bundle = new Bundle();
						$bundles = $bundle->getBundleItem($det->item_id);
						if($bundles){
							$cur_stat = $det->status;

							if($cur_stat == 1){
								$label_stats ='Pending';
							} else {
								$label_stats ='Processed';
							}
							if($get_stats->cnt == 0){
								$consume_input = "<input type='text' class='form-control' placeholder='Qty Used'>";
							} else {
								$consume_input = $label_stats;
							}
							foreach($bundles as $bund){
								$price = $prod->getPrice($bund->item_id_child);
								$item_price = $price->price;
								echo "<tr data-price='$item_price' data-item_id='$bund->item_id_child' ><td style='border-top:1px solid #ccc;'>$bund->item_code</td><td style='border-top:1px solid #ccc;'>$bund->description</td><td style='border-top:1px solid #ccc;'>" . formatQuantity($det->qty * $bund->child_qty)."</td><td style='border-top:1px solid #ccc;'>$consume_input</td></tr>";
							}

						}
					} else {
						$cur_stat = $det->status;
						$req_by = ucwords($det->firstname .  " " . $det->lastname);
						$consume_input = "";
						$nadj = 0;
						$adj = $adjcls->getAdjustment($my_request->data()->branch_id,$det->item_id);
						$label_stats= "";
						if($cur_stat == 1){
							$label_stats ='Pending';
						} else {
							$label_stats ='Processed';
						}
						if(isset($adj->adjustment)){
							$nadj += $adj->adjustment;
						} else {
							$nadj += 0;
						}
						if($price_group_id){
							$adj_price_group = $adjcls->getAdjustmentPriceGroup($det->item_id,$price_group_id);
							if(isset($adj_price_group->adjustment)){
								$nadj += $adj_price_group->adjustment;
							}
						}
						$price = $prod->getPrice($det->item_id);
						$item_price = $price->price + $nadj;
						if($get_stats->cnt == 0){
							$consume_input = "<input type='text' class='form-control' placeholder='Qty Used'>";
						} else {
							$consume_input = $label_stats;
						}

						echo "<tr data-price='$item_price' data-item_id='$det->item_id' ><td style='border-top:1px solid #ccc;'>$det->item_code</td><td style='border-top:1px solid #ccc;'>$det->description</td><td style='border-top:1px solid #ccc;'>" . formatQuantity($det->qty)."</td><td style='border-top:1px solid #ccc;'>$consume_input</td></tr>";
					}

				}
				echo "</tbody>";
				echo "</table>";
				echo "<p>Status: <strong>".$status[$cur_stat]."</strong></p>";
				echo "<p>Requested by: <strong id='service_request_by'>".$req_by."</strong></p>";
				echo "<p>".MEMBER_LABEL.": <strong>".$member_cls->data()->lastname."</strong></p>";
				echo "<p>Technician(s): </p>";
				echo $technician_lbl;

				if($get_stats->cnt == 0) {
					echo "<hr>";
					echo "<div class='text-right'><button data-id='".$id."' id='btnLiquidate' class='btn btn-default'>Liquidate</button></div>";
				} else {
					echo "";
				}
			} else {
				echo "<h3>Requested Item</h3>";
				echo "<div class='alert alert-info'>No request yet.</div>";
			}

		}
	}
	function serviceRelease(){
		$id = Input::get('id');
		$id = Encryption::encrypt_decrypt('decrypt',$id);

		if(is_numeric($id)){

			$branch_name = Input::get('branch_name');
			$service_cls= new Item_service_request($id);
			$service_req = new Service_request_item();
			$member_cls = new Member($service_cls->data()->member_id);
			$details  = $service_req->getDetails($id);
			$technician_lbl = "None";
			$tech_print = "";

			if($service_cls->data()->technician_id){

				$techs = explode(',',$service_cls->data()->technician_id);
				$technician_lbl = '';

				foreach($techs as $tech){

					$technician = new Technician($tech);
					$technician_lbl .= "<strong class='span-block'>".$technician->data()->name."</strong>";
					$tech_print .= $technician->data()->name . ", ";

				}

			}

			$tech_print = rtrim($tech_print,", ");
			if($details){
				$status = ['','Pending','Release','Liquidated'];
				echo "<h3>Requested Item</h3>";
				//echo "<p>Status: </p>";
				$cur_stat = 0;
				$req_by = "";
				echo "<table class='table table-bordered'>";
				echo "<tr><th>Item code</th><th>Description</th><th>Quantity</th><th></th></tr>";
				$item_racks = [];
				$is_valid = true;
				$item_print_arr = [];
				$item_ex = true;
				$rack_tags = new Rack_tag();
				$user = new User();
				$tags_ex = $rack_tags->get_tags_ex('wh_orders',$user->data()->company_id,$service_cls->data()->branch_id);
				if(isset($tags_ex->id) && !empty($tags_ex->id)){
					$excempt_tags = $tags_ex->tag_id;
				} else {
					$excempt_tags =0;
				}
				foreach($details as $det){

					$cur_stat = $det->status;
					if($cur_stat != 1) continue;
					$req_by = ucwords($det->firstname .  " " . $det->lastname);


					if($det->is_bundle == 1){

						$bundle = new Bundle();
						$bundles = $bundle->getBundleItem($det->item_id);
						if($bundles){
							foreach($bundles as $bund){
								$cur_inv = inventory_racking(0,($det->qty * $bund->child_qty),$bund->item_id_child,$service_cls->data()->branch_id,false,$excempt_tags,$item_ex);
								$racking = json_decode($cur_inv['racking']);

								if($cur_inv['insufficient']){
									$is_valid = false;
								}

								$racklbl = "";
								// finalarr.push({stock_man:rackjson[rj].stock_man,rack:rackjson[rj].rack,qty:rackjson[rj].qty,item_code:bundledet[j].item_code,description:bundledet[j].description});

								foreach($racking as $rack){
									$racklbl .= "<span class='text-danger span-block'>$rack->rack <i class='fa fa-long-arrow-right'></i> $rack->qty</span>";
									$item_racks[$bund->item_id_child][] = ['rack_id' => $rack->rack_id, 'qty' => $rack->qty];
									$stock_man="";
									if(isset($rack->stock_man)){
										$stock_man = $rack->stock_man;
									}
									$item_print_arr[]= ['stock_man' =>$stock_man,'rack' =>$rack->rack,'qty' =>formatQuantity($rack->qty),'item_code' => $bund->item_code,'description' => $bund->description ];

								}
								echo "<tr><td style='border-top:1px solid #ccc;'>$bund->item_code</td><td style='border-top:1px solid #ccc;'>$bund->description</td><td style='border-top:1px solid #ccc;'>" . formatQuantity($det->qty * $bund->child_qty)."</td><td style='border-top:1px solid #ccc;'>$racklbl</td></tr>";
							}

						}

					} else {

						$item_cls = new Product($det->item_id);
						$racklbl="";
						if($item_cls->data()->item_type == -1){
							$cur_inv = inventory_racking(0,$det->qty,$det->item_id,$service_cls->data()->branch_id,false,$excempt_tags,$item_ex);
							$racking = json_decode($cur_inv['racking']);

							if($cur_inv['insufficient']){
								$is_valid = false;
							}


							//finalarr.push({stock_man:rackjson[rj].stock_man,rack:rackjson[rj].rack,qty:rackjson[rj].qty,item_code:bundledet[j].item_code,description:bundledet[j].description});

							foreach($racking as $rack){
								$racklbl .= "<span class='text-danger span-block'>$rack->rack <i class='fa fa-long-arrow-right'></i> $rack->qty</span>";
								$item_racks[$det->item_id][] = ['rack_id' => $rack->rack_id, 'qty' => $rack->qty];
								$stock_man="";
								if(isset($rack->stock_man)){
									$stock_man = $rack->stock_man;
								}
								$item_print_arr[]= ['stock_man' =>$stock_man,'rack' =>$rack->rack,'qty' =>formatQuantity($rack->qty),'item_code' => $det->item_code,'description' => $det->description ];

							}
						}


						echo "<tr><td style='border-top:1px solid #ccc;'>$det->item_code</td><td style='border-top:1px solid #ccc;'>$det->description</td><td style='border-top:1px solid #ccc;'>" . formatQuantity($det->qty)."</td><td style='border-top:1px solid #ccc;'>$racklbl</td></tr>";

					}

				}

				echo "</table>";
				echo "<p>Status: <strong>".$status[$cur_stat]."</strong></p>";
				echo "<p>Requested by: <strong>".$req_by."</strong></p>";
				echo "<p>".MEMBER_LABEL.": <strong>".$member_cls->data()->lastname."</strong></p>";
				echo "<p>Technician(s): </p>";
				echo $technician_lbl;
				echo "<hr>";
				if($is_valid){
					echo "<div class='text-right'><button data-address='".$member_cls->data()->personal_address."' data-client='".$member_cls->data()->lastname."' data-tech='$tech_print' data-branch_name='$branch_name' data-date='".date('m/d/Y')."' data-id='".$id."' data-racks='".json_encode($item_print_arr)."' id='btnReleasePrint' class='btn btn-default'>Print</button> <button data-id='".$id."' data-racks='".json_encode($item_racks)."' id='btnReleaseItem' class='btn btn-default'>Release Item</button></div>";
				} else {
					echo "<p class='text-right text-muted'>Insufficient stocks</p>";
				}

			} else {
				echo "<h3>Requested Item</h3>";
				echo "<div class='alert alert-info'>No request yet.</div>";
			}
		}
	}

	function releaseItem(){

		$id = Input::get('id');
		$item_racks = Input::get('racks');

		if(is_numeric($id)){

			$item_racks = json_decode($item_racks);
			$service_cls = new Item_service_request($id);

			if($item_racks){

				$inventory = new Inventory();
				$inv_mon = new Inventory_monitoring();
				$user = new User();

				foreach($item_racks as $item_id => $item){
					foreach($item as $i){
						$rack_id = $i->rack_id;
						$rack_qty = $i->qty;

						// check if item exists in rack
						if($inventory->checkIfItemExist($item_id,$service_cls->data()->branch_id,$user->data()->company_id,$rack_id)){
							$curinventoryFrom = $inventory->getQty($item_id,$service_cls->data()->branch_id,$rack_id);
							$currentqty = $curinventoryFrom->qty;
							$inventory->subtractInventory($item_id,$service_cls->data()->branch_id,$rack_qty,$rack_id);
						} else {
							$currentqty = 0;
						}

						// monitoring
						$newqtyFrom = $currentqty - $rack_qty;

						$inv_mon->create(array(
							'item_id' =>$item_id,
							'rack_id' => $rack_id,
							'branch_id' => $service_cls->data()->branch_id,
							'page' => 'ajax/ajax_query2.php',
							'action' => 'Update',
							'prev_qty' => $currentqty,
							'qty_di' => 2,
							'qty' => $rack_qty,
							'new_qty' => $newqtyFrom,
							'created' => time(),
							'user_id' => $user->data()->id,
							'remarks' => 'Deduct inventory for service item id #' . $id,
							'is_active' => 1,
							'company_id' => $user->data()->company_id
						));
					}

				}

				$service_request_item = new Service_request_item();
				$service_request_item->updateStatus($id,2);

				Log::addLog($user->data()->id,$user->data()->company_id,"Item Service: Release Item ID $id","ajax_service_item.php");

				echo "Item release successfully.";

			} else {
				$user = new User();
				$service_request_item = new Service_request_item();
				$service_request_item->updateStatus($id,2);

				Log::addLog($user->data()->id,$user->data()->company_id,"Item Service: Release Item ID $id No Rack","ajax_service_item.php");
				echo "Item release successfully.";

			}
		}

	}
	function getService(){

		$status = Input::get('status');
		$date_from = Input::get('dt1');
		$date_to = Input::get('dt2');
		$branch_id = Input::get('branch_id');
		$service_type_id = Input::get('service_type_id');
		$is_dl = Input::get('is_dl');
		$border = "";
		if($is_dl == 1){
			$border= "border='1'";
			$filename = "service-type-" . date('m-d-Y-h-i-s') . ".xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");
		}

		$item_service = new Item_service_request();
		$user = new User();
		$list = $item_service->getRequest($user->data()->company_id,0,$status,$date_from,$date_to,$branch_id,$service_type_id);

		$is_aquabest = Configuration::isAquabest();
		$status_arr = ['', // 0
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



		$secondary = [
			'Service Report Validation Schedule',
			'SO Creation And Dispatching',
			'For Reporting',
			'CCD Verification',
			'Close',
			'Hold',
			'Cancelled'
		];

		if($list){
			?>
			<h3>Details</h3>
			<div id="no-more-tables">
			<table class='table' <?php echo $border; ?> id='tblForApproval'>
			<thead>
			<tr>
				<th>Details</th>
				<th>Member</th>
				<?php if($is_aquabest){ ?>
					<th>Date Log</th>
				<?php } ?>
				<th>Date Created</th>
				<th>Technician</th>
				<th></th>
			</tr>
			</thead>
			<tbody>

			<?php
			$techcls = new Technician();
			foreach($list as $item) {
			if($item->member_id) {
			$mem = escape($item->mln . ", " . $item->mfn . " " . $item->mmn);
			} else {
			$mem = 'Not available';
			}
			$techids = $item->technician_id;
			$alltechnician = "<p class='text-danger'>No technician assign.</p>";
             if($techids) {
             $listech = $techcls->getTech($techids);
             if($listech) {
             $alltechnician = "";
             foreach($listech as $l) {
             $alltechnician .= "<p class='text-danger'><i class='fa fa-user'></i> $l->name</p>";
                  }
                  }
                  }
                  ?>
			<tr>
				<td data-title="Id">
					<strong>ID:</strong> <?php echo escape($item->id); ?><br>
					<strong>User: </strong><?php echo escape($item->lastname . ", " . $item->firstname . " " . $item->middlename); ?><br>
					<strong>Branch: </strong>

					<?php
						if($item->branch_name){
							echo escape($item->branch_name);
						} else {
							echo "No branch assigned.";
						}

					?>


				</td>

				<td data-title="Member">
					<?php echo $mem; ?>
					<small class='text-danger span-block'>
						<?php
							$allstatus = $item_service->getStatuses($item->id);
							$lblstats = "";

							if(count($allstatus)) {
								foreach($allstatus as $ind_stat) {
									$lblstats .= $status_arr[$ind_stat->status] . ", ";
								}
								echo $lblstats = rtrim($lblstats, ", ");
							}

						?>

					</small>
					<small class='span-block'>
						<?php echo ($item->service_type_name) ? $item->service_type_name : ''; ?>
					</small>

					<?php
						if($is_aquabest){
							?>
							<strong class='text-success span-block'>
								<?php
									echo strtoupper($secondary[$item->second_status]);
									if($item->second_status == 6){
										echo "<br>Remarks: ". $item->cancel_remarks;
									} else if($item->second_status == 5){
										echo "<br>Remarks: ". $item->hold_remarks;
									}

								?>

								<br>


							</strong>
							<?php
						}
					?>
				</td>
				<?php if($is_aquabest){ ?>
					<td>
						<?php
							$cls_service_date_log = new Service_date_log();
							$service_date_log = $cls_service_date_log->getList($item->id);
							if($service_date_log){
								foreach($service_date_log as $dtlog){
									echo "<p>".$secondary[$dtlog->status]."<br><i class='fa fa-user'></i> <small class='text-danger'>".ucwords($dtlog->firstname . " ". $dtlog->lastname)."</small><br><i class='fa fa-calendar'></i> <small class='text-danger'>".date('m/d/Y h:i:s A',$dtlog->dt)."</small></p>";
								}
							} else {
								echo "<p>N/A</p>";
							}
						?>
					</td>
				<?php } ?>
				<td data-title="Created"><?php echo date('m/d/Y', $item->created); ?></td>
				<td><?php echo $alltechnician; ?></td>
				<td>



				</td>
			</tr>
			<?php
		}
		?>
<?php
		} else {
			echo "<div class='alert alert-info'>No record found</div>";
		}
	}

function liquidateItem(){
	$id = Input::get('id');
	$arr = Input::get('arr');
	if(is_numeric($id)){
		$arr = json_decode($arr);
		$my_req = new Item_service_request($id);

		$user = new User();
		$arr_rec = [];
		$arr_used = [];
		foreach($arr as $a){
			$qty = $a->qty;
			$con_qty = $a->con_qty;
			$item_id = $a->item_id;
			$return = $qty - $con_qty;
			if($return > 0){
				$arr_rec[$item_id] = $return;
			}
			if($con_qty > 0){
				$arr_used[$item_id] = $con_qty;
			}
		}
		$now = time();
		if(count($arr_rec)){
			// receiving
			$transferMon = new Transfer_inventory_mon();

			$transferMon->create(array(
				'branch_id' => $my_req->data()->branch_id,
				'company_id' => $user->data()->company_id,
				'from_where' => 'From Service Liquidation',
				'status' => 1,
				'is_active' => 1,
				'payment_id' => $id,
				'created' => $now,
				'modified' => $now,
			));
			$orderlastid = $transferMon->getInsertedId();
			$transferdetails = new Transfer_inventory_details();

			foreach($arr_rec as $k => $v){
				$transferdetails->create(array(
					'transfer_inventory_id' => $orderlastid,
					'item_id' => $k,
					'rack_id_from' => 0,
					'rack_id_to' => 0,
					'qty' => $v,
					'is_active' => 1
				));
			}

		}

		if(count($arr_used)){
			//item used
			$serviceItem = new Service_item_use();
			foreach($arr_used as $k => $v){
				$serviceItem->create(array(
					'service_id' => $id,
					'item_id' => $k ,
					'qty' => $v,
					'member_id' => $my_req->data()->member_id,
					'is_active' => 1,
					'company_id' => $user->data()->company_id,
					'created' => $now
				));

			}
		}
		$service_request_item = new Service_request_item();
		$service_request_item->updateStatus($id,3);
		Log::addLog($user->data()->id,$user->data()->company_id,"Item Service: Liquidate Item ID $id","ajax_service_item.php");

		echo "Liquidated successfully.";
	}
}

	function updatePulloutSchedule(){
		 $id = (int) Input::get('id');
		 $dt = Input::get('dt');

		if($dt){
			$item_service = new Item_service_request($id);
			$item_service->update(array('pullout_schedule'=> strtotime($dt)),$id);
			echo "Updated successfully.";
		} else {
			echo "Error updating information. Please try again.";
		}

	}

	/***************************************/


	function getReservedStocks($item_id = 0, $branch_id = 0,$qty=0){
		$msg = "";
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
			foreach($bundles as $bun){
				$pending_bundle_qty = $whorder->pendingBundles($bun->item_id_child,$branch_id);
				if($pending_bundle_qty && isset( $pending_bundle_qty->pending_qty )){
					$stock_bundle = $inv->getAllQuantity($bun->item_id_child,$branch_id);
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


	function addUnitTime(){
		$item_id = Input::get('item_id');
		$unit_name = Input::get('unit_name');
		$unit_qty = Input::get('unit_qty');
		$user = new User();
		if($item_id && $unit_name && is_numeric($unit_qty)){
			$item_unit = new Item_unit();
			$now = time();

			$item_unit->create(
				[
					'item_id' => $item_id,
					'qty' => $unit_qty,
					'name' => $unit_name,
					'is_active' => 1,
					'created' => $now,
					'company_id' => $user->data()->id
				]
			);
			echo "1";
		} else {
			echo "0";
		}
	}
	function addMemberDiscountCategory(){
		$member_id= Input::get('member_id');
		$category_id = Input::get('category_id');
		$discount_1 = Input::get('discount_1');
		$discount_2 = Input::get('discount_2');
		$discount_3 = Input::get('discount_3');
		$discount_4 = Input::get('discount_4');
		$request_id = Input::get('request_id');


		if($member_id && $category_id){
			$member = new Member_category_discount();
			if($request_id){
				// update

				$member->update(
					[
						'member_id' => $member_id,
						'category_id' => $category_id,
						'discount_1' => $discount_1,
						'discount_2' => $discount_2,
						'discount_3' => $discount_3,
						'discount_4' => $discount_4
					] , $request_id
				);

			} else {

				$now = time();


				$member->create(
					[
						'member_id' => $member_id,
						'category_id' => $category_id,
						'discount_1' => $discount_1,
						'discount_2' => $discount_2,
						'discount_3' => $discount_3,
						'discount_4' => $discount_4,
						'created' => $now,
						'is_active' => 1

					]
				);

			}


			echo "1";
		} else {
			echo "0";
		}
	}

	function GenerateWord()
	{
		//Get a random word
		$nb=rand(3,10);
		$w='';
		for($i=1;$i<=$nb;$i++)
			$w.=chr(rand(ord('a'),ord('z')));
		return $w;
	}

	function GenerateSentence()
	{
		//Get a random sentence

		$nb = rand(1,10);
		$s = '';
		for($i=1;$i<=$nb;$i++)
			$s.=GenerateWord().' ';

		return substr($s,0,-1);

	}

	function servicePrint(){
		require('../libs/fpdf17/fpdf.php');

		$user = new User();
		$id = Input::get('id');
		$company_data = $user->getCompany($user->data()->company_id);

		$company_name = $company_data->name;

		$company_address = $company_data->address;

		$service_cls = new Item_service_request();

		$service_data = $service_cls->getFullDetails($id);

		$contact_number = $company_data->contact_number;

		$website = $company_data->web_address;

		$company_email = $company_data->email;

		$troubleshooting_details = $service_data->troubleshooting_details;

		$technical_remarks = $service_data->technical_remarks;

		$request_date = date('m/d/Y',$service_data->created);
		$service_date_from = "";
		$service_date_to = "";
		$owner= $service_data->member_name;
		$service_time_from = "";
		$service_time_to = "";
		$member_address = $service_data->personal_address;

		$service_type = $service_data->service_type_name;

		$tech = new Technician();

		$tech_list = $tech->getTech($service_data->technician_id);
		$tech_arr =[];
		if($tech_list){
			foreach($tech_list as $t){
				$tech_arr[]= $t->name;
			}
		}
		$tech1= isset($tech_arr[0]) ? $tech_arr[0] : '';
		$tech2=isset($tech_arr[1]) ? $tech_arr[1] : '';
		$tech3=isset($tech_arr[2]) ? $tech_arr[2] : '';

		$ccd_remarks= $service_data->remarks;

		$measurement =  new Service_measurement_test();

		$measurement_list = $measurement->getMeasurement($id);
		$empty = $measurement->getBlank();

		if($measurement_list){
			foreach($measurement_list as $mem){
				$arr[$mem->grp][] = [$mem->name => $mem->val];
			}
		} else {
			foreach($empty as $e){
				$arr[$e->grp][] = [$e->name =>''];
			}
		}


		$keys_measurement = [];
		$length_measurement = 0;
		foreach($arr as $key => $data){

			$keys_measurement[] = $key;
			$cnt = count($data);
			if($cnt > $length_measurement){
				$length_measurement = $cnt;
			}
		}

		$pdf=new Pdf_service();
		$pdf->AddPage();
		$pdf->SetFont('Arial','',9);
		//Table with 20 rows and 4 columns
		$pdf->SetWidths(array(130,60));
		$pdf->Row(array("$company_name\n$company_address\n$contact_number\n$website\n$company_email","Service Request Acknowledgement\n$id"));

		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(190,5,"Requestor details",0,1,'L');

		$pdf->SetFont('Arial','',9);
		$pdf->SetWidths(array(100,45,45));
		$pdf->Row(array("Request Date: $request_date","Service Date From: $service_date_from","Service Date To: $service_date_to"));
		$pdf->Row(array("Owner: $owner","Service Time From: $service_time_from","Service Time To: $service_time_to"));

		$pdf->SetWidths(array(190));
		$pdf->Row(array("Address: $member_address"));

		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(190,5,"Service Information",0,1,'L');

		$pdf->SetFont('Arial','',9);
		$pdf->SetWidths(array(55,45,45,45));
		$pdf->Row(array("Service Type: $service_type","Tech1: $tech1","Tech2: $tech2","Tech3: $tech3"));

		$pdf->SetWidths(array(63.33,63.33,63.34));
		$pdf->Row(array("Payment","Amount","Arrangement"));

		$pdf->SetWidths(array(190));
		$pdf->Row(array("**Nothing follows**"));

		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(190,5,"Machine Issues",0,1,'L');

		$pdf->SetFont('Arial','',9);
		$pdf->SetWidths(array(63.33,63.33,63.34));
		$pdf->Row(array("Issue","Related Product","Description"));
		$pdf->SetWidths(array(190));
		$pdf->Row(array("**Nothing follows**"));

		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(190,5,"Machine Issues",0,1,'L');
		$pdf->SetFont('Arial','',9);
		$pdf->SetWidths(array(190));
		$pdf->Row(array("CCD Remarks: $ccd_remarks"));
		$pdf->ln(10);
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(127,10,"",0,0,'L');
		$pdf->Cell(52,10,"__________________________________",0,1,'L');

		$pdf->Cell(125,1,"",0,0,'L');
		$pdf->Cell(55,1,"Signature over Printed Name and Position",0,1,'L');
		$pdf->ln(10);
		$pdf->Cell(0,4,"----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------",0,1,'L');

		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(190,5,"Service Summary",0,1,'L');
		$pdf->SetFont('Arial','',9);

		$all_page = 190;
		$per_col_key = $all_page / count($keys_measurement);
		$arr_width_measure_key = [];
		for($i=1;$i<=count($keys_measurement);$i++){
			$arr_width_measure_key[] = $per_col_key;
		}

		$pdf->SetWidths($arr_width_measure_key);
		$pdf->Row($keys_measurement);

		$per_col_key_val = $all_page / (count($keys_measurement) * 2);
		$arr_width_measure_key_val = [];
		for($i=1;$i<=(count($keys_measurement) * 2);$i++){
			$arr_width_measure_key_val[] = $per_col_key_val;
		}

		$pdf->SetWidths($arr_width_measure_key_val);
		for($i=0;$i<$length_measurement;$i++){
			$arr_for_row = [];
			foreach($keys_measurement as $key){
				$arr_cur = $arr[$key][$i];
				$arr_for_row[] = key($arr_cur);
				$arr_for_row[] = $arr_cur[key($arr_cur)];
			}
			$pdf->Row($arr_for_row);
		}

		/*
		$pdf->SetWidths(array(47.5,47.5,47.5,47.5));
		$pdf->Row(array("TDS","Flow Rate","Pressure","Meter Reading")); // base sa key
		$pdf->SetWidths(array(23.75,23.75,23.75,23.75,23.75,23.75,23.75,23.75));
		$pdf->Row(array("Source:","","Permeate:","","Inlet","","Before",""));
		$pdf->Row(array("Product:","","Concentrate:","","Outlet","","After",""));
		$pdf->Row(array("Reject:","","","","","","",""));
		*/


		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(190,5,"Technical Remarks",0,1,'L');

		$pdf->SetWidths(array(190));
		if(!$technical_remarks){
			$technical_remarks = " \n\n\n";
		}
		$pdf->Row(array($technical_remarks,));

		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(190,5,"Troubleshooting Details",0,1,'L');
		$pdf->SetWidths(array(190));
		if(!$troubleshooting_details){
			$troubleshooting_details = " \n\n\n";
		}
		$pdf->Row(array($troubleshooting_details));

		$pdf->SetFont('Arial','',9);
		$pdf->Cell(127,10,"",0,0,'L');
		$pdf->Cell(52,10,"__________________________________",0,1,'L');

		$pdf->Cell(125,1,"",0,0,'L');
		$pdf->Cell(55,1,"Signature over Printed Name and Position",0,1,'L');
		$pdf->ln(10);

		$pdf->Output();
	}


	function saveWhOrderIdNumber(){
		$order_id = Input::get('order_id');
		$assembly_id = Input::get('assembly_id');

		$wh_order = new Wh_order($order_id);
		if(!(isset($wh_order->data()->id) && $wh_order->data()->id)){
			die("Invalid order id number.");
		}
		if($wh_order->data()->payment_id){
			$payment_id = $wh_order->data()->payment_id;
			$assembly = new Assemble_request();
			$assembly->update(
				[
				'wh_id' => $order_id
				],$assembly_id
			);
			$assembly_details = new Assemble_details();

			$list = $assembly_details->getDetails($assembly_id);

			if($list){
				$serial = new Serial();
				$user = new User();
				$now = time();
				foreach($list as $l){
					$serial_numbers  = $l->serial_numbers;
					if($serial_numbers){
						$serial_numbers = json_decode($serial_numbers);
						foreach($serial_numbers as $s){
							$serial->create(array(
								'serial_no' => 	$s->serial_no,
								'item_id' => $s->item_id,
								'payment_id' => $payment_id,
								'company_id' => $user->data()->company_id,
								'user_id' => $user->data()->id,
								'created' => $now,
								'is_active' => 1
							));
						}
					}
				}
			}
			echo "Updated successfully.";
		} else {
			echo "Order doesn't have an invoice/dr yet.";
		}
	}

	function updateRFID(){
		 $id = Input::get('id');
		 $rfid = Input::get('rf_id');
		if(is_numeric($id) && $rfid){
			$cls = new Item_service_request();
			$cls->update(['rf_id' => $rfid],$id);

		}
	}