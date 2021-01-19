<?php
	include 'ajax_connection.php';

	$q = Input::get('q');
	function reArrayFiles(&$file_post) {
		$file_ary = array();
		$file_count = count($file_post['name']);
		$file_keys = array_keys($file_post);

		for ($i=0; $i<$file_count; $i++) {
			foreach ($file_keys as $key) {
				$file_ary[$i][$key] = $file_post[$key][$i];
			}
		}
		return $file_ary;
	}
	if($q == 1){
		$user = new User();
		$rack = Input::get('rack');
		$branch = Input::get('branch');
		$comp_id = $user->data()->company_id;
		$item_adj = new Item_adjustment();
		$output ='';
		if($rack){
			$normalview = true;
		} else {
			$rackcls = new Rack();
			$rackgroup = $rackcls->rackGroup($user->data()->company_id);

			if($rackgroup){
				foreach($rackgroup as $rg){
					$col ='';
					$col .= "<div class='col-md-4' style='text-align:center;'><div style='height:150px;' class='rackdiv1' onclick='getThisGroup(\"".$rg->rack ."-\");'>";
					$col .= "<h3 style='position: relative;top:15%;'>";
					$col .= "<span>".$rg->rack ."</span>";
					$col.="</h3>";

					//$col .= "<div class='progress' style='width:90%;margin:0 auto;margin-top:10px;'>";
					//	$col .= "<div class='progress-bar' role='progressbar' aria-valuenow='60' aria-valuemin='0' aria-valuemax='100' $percent>";
					//$col .= "$p";
					//$col .="</div>";
					//$col .="</div>";
					$col .= "</div></div>";
					$output .= $col;
				}
				echo $output ;
				$normalview = false;
			} else {
				$normalview = true;
			}
		}
		if($normalview){
			$racks = $item_adj->getSelectRack($rack,$comp_id,$branch);

			if($racks){
				foreach ($racks as $r) {
					$rack_audit = new Rack_audit_sp();
					$audit = $rack_audit->getRackAudit($r->id,$branch,$comp_id);

					if($audit){
						$lastaudit = "Last Audit: " . date('m/d/Y',$audit->created);
						$percent = "style='width:" . $audit->percent .";'";
						$p = $audit->percent;
						if($audit->status == 2) $label="Completed";
						else if ($audit->status == 1) $label="Auditing";
					} else {
						$percent = "";
						$p = "0%";
						$lastaudit='&nbsp;';
						$label="Not yet audited";
					}
					$col ='';
					$col .= "<div class='col-md-4' style='text-align:center;'><div class='rackdiv1' onclick='getThisRack(".$r->id.",$branch,$comp_id);'>";
					$col .="<span class='label label-default pull-right'>$label</span>";
					$col .= "<h4><span class='glyphicon glyphicon-list'></span> ";
					$col .= "<span>".$r->rack ."</span>";
					$col.="</h4>";
					$col.="<p>$lastaudit</p>";
					$col .= "<div class='progress' style='width:90%;margin:0 auto;margin-top:10px;'>";
					$col .= "<div class='progress-bar' role='progressbar' aria-valuenow='60' aria-valuemin='0' aria-valuemax='100' $percent>";
					$col .= "$p";
					$col .="</div>";
					$col .="</div>";
					$col .= "</div></div>";
					$output .= $col;

				}
				echo $output;
			} else {
				echo "No record found";
			}
		}


	} else if ($q == 2){
		$branch = Input::get('branch');
		$rack = Input::get('rack');
		$company = Input::get('company');
		if(!$company){
			$user = new User();
			$company = $user->data()->company_id;
		}
		// select inventory
		$inv = new Inventory();
		$rack_audit = new Rack_audit_sp();
		$ammend = new Inventory_ammend();
		$rackitems = $inv->getRackItemsAndInventory($rack,$branch);
		$audit = $rack_audit->getRackAudit($rack,$branch,$company);
		if($audit){
			$checked  = ($audit->status == 1) ? "checked" : "";
			$disabled  = ($audit->status == 1) ? "disabled" : "";
			$disabledbtn  = ($audit->status == 1) ? "" : "disabled";
			$auditid = $audit->id;
		} else {
			$checked ='';
			$disabled='';
			$disabledbtn ='disabled';
			$auditid=0;
		}
		echo "<div class='row'><div class='col-md-8'><button data-auditid='$auditid' data-rackid='$rack' data-branchid='$branch' class='btn btn-default' id='addfounditem'>Add Found Item</button></div>";
		echo "<div class='col-md-4'>";
		echo "<select id='auditHistory' class='form-control'>";
		echo "<option class=''>--Select Date--</option>";
		$audithis = $rack_audit->getAuditHis($rack,$branch,$company);
		if($audithis){
			foreach ($audithis as $his) {
				echo "<option value='".$his->id."'>".date('m/d/Y h:i:s A',$his->created)."</option>";
			}
		}
		echo "</select>";
		echo "</div></div><br>";

		// auditthis function

		if($rackitems){
			echo "<div class='row'>";
			echo "<div class='col-md-6'>";
			echo "<p><label for='auditcheck'>Start Audit</label> <input id='auditcheck' type='checkbox' onchange='auditThis($rack,$branch,$company)' $checked $disabled></p>";
			echo "</div>";
			echo "<div class='col-md-6 text-right'>";
			if(isset($audit->status) && $audit->status == 1){
				echo  "<button data-rack_id='$rack' data-audit_id='$auditid' class='btn btn-default btnStopAudit'>Stop Audit</button>";
			} else if(isset($audit->status) && $audit->status == 2) {
				echo  "<button data-rack_id='$rack' data-audit_id='$auditid' class='btn btn-default btnContinueAudit'>Continue Audit</button>";
			}

			echo "</div>";
			echo "</div>";
			echo "<div id='no-more-tables'>";
			echo "<table id='tblAmendInv' class='table'>";
			echo "<thead>";
			echo "<tr><th>Barcode</th><th>Item code</th><th>Quantity</th><th>Date Ammend</th><th>Witnesses</th><th>Remarks</th><th>Action</th><th style='display:none;'>Partition</th></tr>";
			echo "</thead>";
			echo "<tbody>";
			$itemctr = 0;
			$notammendctr = 0;
			$amend_uploadcls = new Amend_upload();
			foreach ($rackitems as $item) {
				if(!$item->qty) continue;
				$inv_ammend = $ammend->getInventoryAmmendByAuditId($rack,$branch,$company,$item->item_id,$auditid);
				$attachments = $amend_uploadcls->getAttach($auditid,$item->item_id);

				$retatt = "";
				if($attachments){
					foreach($attachments as $att){
						$retatt .= $att->path ."||";
					}
					$retatt = rtrim($retatt,"||");
				}

				if($inv_ammend){
					$disabledbtn1 = "disabled";

					$arem = $inv_ammend->remarks;
					$w1 = "<span class='glyphicon glyphicon-user'></span> " .$inv_ammend->witness1;
					$w2 = "<span class='glyphicon glyphicon-user'></span> " .$inv_ammend->witness2;
					$adate = date('m/d/Y',$inv_ammend->created);

				} else {
					$disabledbtn1 = '';
					$arem = '';
					$w1 = '';
					$w2 = '';
					$adate = '';
					$notammendctr += 1;
				}
				$itemctr +=1;
				$curitem = new Product($item->item_id);
				$zeroQtyClass ="";
				if($item->qty == 0){
					$zeroQtyClass = "qty-hide";
				}
				echo "<tr class='$zeroQtyClass'><td data-title='Barcode'>".$curitem->data()->barcode."</td><td  data-title='Item code'>".$curitem->data()->item_code."<br><small class='text-danger'>".$curitem->data()->description."</small></td><td class='text-right'  data-title='Qty'>".formatQuantity($item->qty)."</td>";
				//date
				echo "<td  data-title='Date'>$adate</td>";
				//witness
				echo "<td  data-title='Witness'> $w1 <br/> $w2</td>";
				//remarks
				$btnpath="";
				if($retatt){
					$btnpath = " <button class='btn btn-default btn-sm btnAttPath' data-paths='$retatt'><i class='fa fa-file'></i></button>";

				}
				echo "<td  data-title='Remarks'>$arem $btnpath</td>";
				$isAuditedBefore = $ammend->isAuditedBefore($rack,$branch,$company,$item->item_id);
				if($isAuditedBefore->cnum > 0){
					$auditbefore = "1";
				} else {
					$auditbefore = "0";
				}
				//action
				// ammendthis function confirmthis

				echo "<td><button $disabledbtn $disabledbtn1 class='btn btn-default' onclick='ammendThis($rack,$branch,".$item->item_id.",".$item->qty.",\"". $curitem->data()->item_code ."\",".$auditid.",$auditbefore)'>Amend</button> <button $disabledbtn $disabledbtn1  class='btn btn-default'  onclick='confirmThis($rack,$branch,".$item->item_id.",".$item->qty.",\"". $curitem->data()->item_code ."\",".$auditid.")'>Confirm</button></td>";
				//partition
				// partitionthis
				echo "<td  style='display:none;'><button $disabledbtn $disabledbtn1  class='btn btn-default' onclick='partitionThis($rack,$branch,".$item->item_id.",".$item->qty.",\"". $curitem->data()->item_code ."\",".$auditid.")'>Partition</button></td>";
				echo "</tr>";
			}
			echo "</tbody>";
			echo "</table>";
			echo "<input type='hidden' id='countnoammend' value='$notammendctr'>";
			if($notammendctr == 0){
				if($auditid){
					$cur_audit = new Rack_audit_sp();
					$cur_audit->update(
						array(
							'percent'=> '100%',
							'item_check' =>$itemctr,
							'status' => 2,
						),$auditid);
				}
			} else {
				$amendedctr = $itemctr-$notammendctr;
				$curpercentage = ($amendedctr/$itemctr) * 100;
				$curpercentage = number_format($curpercentage,2);
				$cur_audit = new Rack_audit_sp();
				if($auditid){
				 /*	$cur_audit->update(
						array(
							'percent'=> $curpercentage.'%',
							'item_check' =>$amendedctr,
							'items' => $itemctr,
							'status' => 1,
						),$auditid); */
				}
			}
			echo "</div>";

		} else {
			echo "<br><div class='alert alert-info'><span class='glyphicon glyphicon-exclamation-sign'></span> NO RECORD FOUND</div>";
		}
	} else if ($q == 3){
		$branch = Input::get('branch');
		$rack = Input::get('rack');
		$company = Input::get('company');
		$inv = new Inventory();
		$rack_audit = new Rack_audit_sp();
		$ammend = new Inventory_ammend();
		$audit = $rack_audit->getRackAudit($rack,$branch,$company);

		if(!$audit  || $audit->status == 2){
			$selectcount = $inv->getCountItemInInventory($rack,$branch);
			$rack_audit->create(array(
				'rack_id' => $rack,
				'created' => time(),
				'branch_id' => $branch,
				'percent' => '0%',
				'items' => $selectcount->cnt,
				'is_active' => 1,
				'status' => 1,
				'company_id' => $company
			));
			echo "1";
		} else {
			echo "Failde to audit";
		}
	} else if ($q == 4){
		$item_id = Input::get('item_id');
		$rack = Input::get('rack');
		$branch = Input::get('branch');
		$aid = Input::get('aid');
		$goodqty =Input::get('goodqty');
		$damageqty = Input::get('damageqty');
		$missingqty = Input::get('missingqty');
		$otherissueqty = Input::get('otherissueqty');

		$incqty = Input::get('incqty');
		$witness1 = Input::get('witness1');
		$witness2 = Input::get('witness2');
		$remarks = Input::get('remarks');
		$user = new User();
		$inv = new Inventory();
		$ammend = new Inventory_ammend();
		$inv_issues = new Inventory_issue();
		$inv_mon = new Inventory_monitoring();
		$inv_issues_mon = new Inventory_issues_monitoring();
		$incqty = ($incqty ) ? $incqty : 0;
		$missingqty = ($missingqty ) ? $missingqty : 0;
		$damageqty = ($damageqty ) ? $damageqty : 0;
		$otherissueqty = ($otherissueqty ) ? $otherissueqty : 0;

		$totalgood = $goodqty - ($missingqty + $damageqty + $incqty + $otherissueqty);
		$newQty = $totalgood;

		$curinventory = $inv->getQty($item_id,$branch,$rack);


		$inv->updateInventory($rack,$branch,$item_id,$totalgood);

		$ammend->create(array(
			'branch_id' =>$branch,
			'item_id' => $item_id,
			'rack_id' =>$rack,
			'qty' => $goodqty,
			'ammend_qty' => $totalgood,
			'created' => time(),
			'witness1' => $witness1,
			'witness2' => $witness2,
			'remarks' => $remarks,
			'audit_id' => $aid,
			'company_id' => $user->data()->company_id,
			'is_active' =>1
		));


		$deducted = $missingqty + $damageqty + $incqty + $otherissueqty;

		if($goodqty != $curinventory->qty){
			if(!$deducted){
				$deducted = $curinventory->qty -$goodqty;
				$deducted = abs($deducted);
			}
		}



		$inv_mon->create(array(
			'item_id' => $item_id,
			'rack_id' => $rack,
			'branch_id' => $branch,
			'page' => 'admin/inventory_adjustments.php',
			'action' => 'Update',
			'prev_qty' => $curinventory->qty,
			'qty_di' => 3,
			'qty' => $deducted,
			'new_qty' => $totalgood,
			'created' => time(),
			'user_id' => $user->data()->id,
			'remarks' => 'Ammend inventory',
			'is_active' => 1,
			'company_id' => $user->data()->company_id
		));

		if($damageqty > 0){
			// status 1 = damage
			$curinvissues = $inv_issues->getQty($item_id,$branch,$rack,1);
			if(isset($curinvissues->qty)){
				$cur_issues = $curinvissues->qty;
			} else {
				$cur_issues = 0;
			}
			if($inv_issues->checkIfItemExist($item_id,$branch,$user->data()->company_id,$rack,1)){

				$inv_issues->addInventory($item_id,$branch,$damageqty,false,$rack,1);
			} else {
				$inv_issues->addInventory($item_id,$branch,$damageqty,true,$rack,1);
			}
			$new_issues = $cur_issues + $damageqty;
			$inv_issues_mon->create(array(
				'item_id' => $item_id,
				'rack_id' => $rack,
				'branch_id' => $branch,
				'page' => 'admin/inventory_adjustments.php',
				'action' => 'Update',
				'prev_qty' => $cur_issues,
				'qty_di' => 1,
				'qty' => $damageqty,
				'new_qty' => $new_issues,
				'created' => time(),
				'user_id' => $user->data()->id,
				'remarks' => 'Add damage item',
				'is_active' => 1,
				'company_id' => $user->data()->company_id,
				'type' => 1
			));
		}
		if($otherissueqty > 0){
			// status 1 = damage
			$cur_other_issue = $inv_issues->getQty($item_id,$branch,$rack,5);
			if(isset($cur_other_issue->qty)){
				$cur_issues = $cur_other_issue->qty;
			} else {
				$cur_issues = 0;
			}
			if($inv_issues->checkIfItemExist($item_id,$branch,$user->data()->company_id,$rack,5)){
				$inv_issues->addInventory($item_id,$branch,$otherissueqty,false,$rack,5);
			} else {
				$inv_issues->addInventory($item_id,$branch,$otherissueqty,true,$rack,5);
			}

			$new_issues = $cur_issues + $otherissueqty;
			$inv_issues_mon->create(array(
				'item_id' => $item_id,
				'rack_id' => $rack,
				'branch_id' => $branch,
				'page' => 'admin/inventory_adjustments.php',
				'action' => 'Update',
				'prev_qty' => $cur_issues,
				'qty_di' => 1,
				'qty' => $otherissueqty,
				'new_qty' => $new_issues,
				'created' => time(),
				'user_id' => $user->data()->id,
				'remarks' => 'Add '.OTHER_ISSUE_LABEL.' item',
				'is_active' => 1,
				'company_id' => $user->data()->company_id,
				'type' => 5
			));
		}

		if($incqty > 0){
			// status 1 = damage
			$curinvissues = $inv_issues->getQty($item_id,$branch,$rack,4);
			if(isset($curinvissues->qty)){
				$cur_issues = $curinvissues->qty;
			} else {
				$cur_issues = 0;
			}
			if($inv_issues->checkIfItemExist($item_id,$branch,$user->data()->company_id,$rack,4)){

				$inv_issues->addInventory($item_id,$branch,$incqty,false,$rack,4);
			} else {
				$inv_issues->addInventory($item_id,$branch,$incqty,true,$rack,4);
			}
			$new_issues = $cur_issues + $incqty;
			$inv_issues_mon->create(array(
				'item_id' => $item_id,
				'rack_id' => $rack,
				'branch_id' => $branch,
				'page' => 'admin/inventory_adjustments.php',
				'action' => 'Update',
				'prev_qty' => $cur_issues,
				'qty_di' => 1,
				'qty' => $incqty,
				'new_qty' => $new_issues,
				'created' => time(),
				'user_id' => $user->data()->id,
				'remarks' => 'Add incomplete item',
				'is_active' => 1,
				'company_id' => $user->data()->company_id,
				'type' => 4
			));
		}
		if($missingqty > 0){
			// status 2 = missing
			$curinvissues = $inv_issues->getQty($item_id,$branch,$rack,2);
			if(isset($curinvissues->qty)){
				$cur_issues = $curinvissues->qty;
			} else {
				$cur_issues = 0;
			}
			if($inv_issues->checkIfItemExist($item_id,$branch,$user->data()->company_id,$rack,2)){
				$inv_issues->addInventory($item_id,$branch,$missingqty,false,$rack,2);
			} else {
				$inv_issues->addInventory($item_id,$branch,$missingqty,true,$rack,2);
			}
			$new_issues = $cur_issues + $missingqty;
			$inv_issues_mon->create(array(
				'item_id' => $item_id,
				'rack_id' => $rack,
				'branch_id' => $branch,
				'page' => 'admin/inventory_adjustments.php',
				'action' => 'Update',
				'prev_qty' => $cur_issues,
				'qty_di' => 1,
				'qty' => $missingqty,
				'new_qty' => $new_issues,
				'created' => time(),
				'user_id' => $user->data()->id,
				'remarks' => 'Add missing item',
				'is_active' => 1,
				'company_id' => $user->data()->company_id,
				'type' => 2
			));

		}
		if($aid){
			$cur_audit = new Rack_audit_sp($aid);

			$titem = $cur_audit->data()->items;
			$tcheck = $cur_audit->data()->item_check + 1;
			$percent = number_format(($tcheck/$titem) * 100,2);
			if($titem == $tcheck){
				$cur_audit->update(
					array(
						'percent'=> $percent . '%',
						'item_check' =>$tcheck,
						'status' => 2,
					),$aid);

			} else {
				$cur_audit->update(
					array(
						'percent'=> $percent . '%',
						'item_check' =>$tcheck
					),$aid);
			}
		}


		$files = $_FILES['file'];
		if($files){
			$amend_upload = new Amend_upload();
			$file_ary = reArrayFiles($files);
			$timenow = time();
			if(count($file_ary)){
				foreach($file_ary as $file){
					if($file['name']){
						// upload
						$prefup ="amend";
						$uniqid = uniqid();
						$fileext =  substr($file["name"], -3);
						$filename = $prefup . "-" . $uniqid . "-" . $item_id . "-" .$aid;
						if($file['type'] == 'image/jpeg' || $file['type'] == 'image/jpg' || $file['type'] == 'image/png' || $file['type'] == 'application/pdf'){
							$target_path = "../uploads/";
							$filepath = $target_path . $filename . "." . $fileext;
							if(move_uploaded_file($file["tmp_name"],$filepath)){
								$amend_upload->create(array(
									'item_id' => $item_id,
									'audit_id' => $aid,
									'company_id' => $user->data()->company_id,
									'is_active' => 1,
									'created' => $timenow,
									'path' => $filepath,

								));
							}

						}

					}
				}
			}

		}


		echo 1;
	} else if($q == 5){
		$aid = Input::get('aid');
		if(isset($aid) || !empty($aid)){
			$ammend = new Inventory_ammend();
			$selectthis = $ammend->get_active('inventory_ammend',array('audit_id','=',$aid));

			echo "<table class='table'>";
			echo "<tr><th>Barcode</th><th>Item code</th><th>Quantity</th><th>Ammend Quantity</th><th>Date ammended</th><th>Witness</th><th>Remarks</th></tr>";
			$amend_uploadcls = new Amend_upload();
			foreach ($selectthis as $value) {
				$item = new Product($value->item_id);
				$attachments = $amend_uploadcls->getAttach($aid,$value->item_id);
				$retatt = "";
				if($attachments){
					foreach($attachments as $att){
						$retatt .= $att->path ."||";
					}
					$retatt = rtrim($retatt,"||");
				}
				$btnpath="";
				if($retatt){
					$btnpath = " <button class='btn btn-default btn-sm btnAttPath' data-paths='$retatt'><i class='fa fa-file'></i></button>";

				}
				echo "<tr><td>".$item->data()->barcode ."</td><td>".$item->data()->item_code ."</td><td>".$value->qty."</td><td>".$value->ammend_qty."</td><td>".date('m/d/Y h:i:s A',$value->created)."</td><td>".$value->witness1."<br/>".$value->witness2."</td><td>".$value->remarks." $btnpath</td></tr>";
			}

			echo "</table>";
		}
	} else if ($q == 6){
		$item_id = Input::get('item_id');
		$rack = Input::get('rack');
		$branch = Input::get('branch');
		$aid = Input::get('aid');
		$goodqty = Input::get('goodqty');
		$witness1 =Input::get('witness1');
		$witness2 = Input::get('witness2');
		$remarks = Input::get('remarks');

		$totalgood = $goodqty;

		$newQty = $totalgood;

		$user = new User();
		$inv = new Inventory();
		$ammend = new Inventory_ammend();
		$inv_mon = new Inventory_monitoring();

		$inv->updateInventory($rack,$branch,$item_id,$totalgood);

		$ammend->create(array(
			'branch_id' =>$branch,
			'item_id' => $item_id,
			'rack_id' =>$rack,
			'qty' => $goodqty,
			'ammend_qty' => $totalgood,
			'created' => time(),
			'witness1' => $witness1,
			'witness2' => $witness2,
			'remarks' => $remarks,
			'audit_id' => $aid,
			'company_id' => $user->data()->company_id,
			'is_active' =>1
		));
		// monitoring
		$inv_mon->create(array(
			'item_id' => $item_id,
			'rack_id' => $rack,
			'branch_id' => $branch,
			'page' => 'admin/inventory_adjustments.php',
			'action' => 'Update',
			'prev_qty' => $goodqty,
			'qty_di' => 1,
			'qty' => 0,
			'new_qty' => $totalgood,
			'created' => time(),
			'user_id' => $user->data()->id,
			'remarks' => 'Confirm inventory',
			'is_active' => 1,
			'company_id' => $user->data()->company_id
		));
		if($aid){
			$cur_audit = new Rack_audit_sp($aid);

			$titem = $cur_audit->data()->items;
			$tcheck = $cur_audit->data()->item_check + 1;
			$percent = number_format(($tcheck/$titem) * 100,2);
			if($titem == $tcheck){
				$cur_audit->update(
					array(
						'percent'=> $percent . '%',
						'item_check' =>$tcheck,
						'status' => 2,
					),$aid);

			} else {
				$cur_audit->update(
					array(
						'percent'=> $percent . '%',
						'item_check' =>$tcheck
					),$aid);
			}
		}

		echo 1;
	} else if($q == 7){
		$rack = Input::get('rack');
		$branch = Input::get('branch');
		$rack_audit = new Rack_audit_sp();
		$check = $rack_audit->isAuditing($rack,$branch);

		if($check->cnt != 0) {
			echo "1";
		} else {
			echo "0";
		}
	} else if ($q == 8){
		$item_id = Input::get('item_id');
		$rack =  Input::get('rack');
		$branch =  Input::get('branch');
		$aid = Input::get('aid');
		$goodqty = Input::get('goodqty');
		$witness1 = Input::get('witness1');
		$witness2 =Input::get('witness2');
		$remarks = Input::get('remarks');
		$racking = json_decode(Input::get('racking'),true);
		$totalallocated = 0;

		$user = new User();
		$inv = new Inventory();
		$ammend = new Inventory_ammend();
		$rack_audit = new Rack_audit_sp();
		$inv_mon = new Inventory_monitoring();
		foreach($racking as $r){
			$audit = $rack_audit->getRackAudit($r['rack'],$branch,$user->data()->company_id);
			if($audit){
				if($audit->status==1){
					continue;
				}
			}
			if($inv->checkIfItemExist($item_id,$branch,$user->data()->company_id,$r['rack'])){
				$curinventory = $inv->getQty($item_id,$branch,$r['rack']);
				$inv->addInventory($item_id,$branch,$r['qty'],false,$r['rack']);
				$newqty = $curinventory->qty + $r['qty'];
				$inv_mon->create(array(
					'item_id' => $item_id,
					'rack_id' => $r['rack'],
					'branch_id' => $branch,
					'page' => 'admin/inventory_adjustments.php',
					'action' => 'Update',
					'prev_qty' => $curinventory->qty,
					'qty_di' => 1,
					'qty' => $r['qty'],
					'new_qty' => $newqty,
					'created' => time(),
					'user_id' => $user->data()->id,
					'remarks' => 'Allocate inventory to rack (partition)',
					'is_active' => 1,
					'company_id' => $user->data()->company_id
				));

			} else {
				$curinventory = 0;
				$inv->addInventory($item_id,$branch,$r['qty'],true,$r['rack']);
				$newqty = $curinventory + $r['qty'];
				$inv_mon->create(array(
					'item_id' => $item_id,
					'rack_id' => $r['rack'],
					'branch_id' => $branch,
					'page' => 'admin/inventory_adjustments.php',
					'action' => 'Insert',
					'prev_qty' => $curinventory,
					'qty_di' => 1,
					'qty' => $r['qty'],
					'new_qty' => $newqty,
					'created' => time(),
					'user_id' => $user->data()->id,
					'remarks' => 'Allocate inventory to rack (partition)',
					'is_active' => 1,
					'company_id' => $user->data()->company_id
				));

			}


			$totalallocated += $r['qty'];
		}

		$left = $goodqty - $totalallocated;

		$inv->updateInventory($rack,$branch,$item_id,$left);

		$ammend->create(array(
			'branch_id' =>$branch,
			'item_id' => $item_id,
			'rack_id' =>$rack,
			'qty' => $goodqty,
			'ammend_qty' => $left,
			'created' => time(),
			'witness1' => $witness1,
			'witness2' => $witness2,
			'remarks' => $remarks,
			'audit_id' => $aid,
			'company_id' => $user->data()->company_id,
			'is_active' =>1
		));
		// monitoring
		$inv_mon->create(array(
			'item_id' => $item_id,
			'rack_id' => $rack,
			'branch_id' => $branch,
			'page' => 'admin/inventory_adjustments.php',
			'action' => 'Update',
			'prev_qty' => $goodqty,
			'qty_di' => 2,
			'qty' => $totalallocated,
			'new_qty' => $left,
			'created' => time(),
			'user_id' => $user->data()->id,
			'remarks' => 'Allocate inventory to rack (partition)',
			'is_active' => 1,
			'company_id' => $user->data()->company_id
		));
		$cur_audit = new Rack_audit_sp($aid);

		$titem = $cur_audit->data()->items;
		$tcheck = $cur_audit->data()->item_check + 1;
		$percent = number_format(($tcheck/$titem) * 100,2);
		if($titem == $tcheck){
			$cur_audit->update(
				array(
					'percent'=> $percent . '%',
					'item_check' =>$tcheck,
					'status' => 2,
				),$aid);

		} else {
			$cur_audit->update(
				array(
					'percent'=> $percent . '%',
					'item_check' =>$tcheck
				),$aid);
		}
		echo 1;
	} else if($q == 9){
		$user = new User();
		$inventory = new Inventory();
		$ammend = new Inventory_ammend();

		$item_id = Input::get('item');
		$qty = Input::get('qty');
		$witness1 = Input::get('witness1');
		$witness2 = Input::get('witness2');
		$rackid = Input::get('rackid');
		$branchid = Input::get('branchid');
		$auditid = Input::get('auditid');
		$remarks = Input::get('remarks');

		// check if has audit
		$isAuditedBefore = $ammend->isAuditedBefore($rackid,$branchid,$user->data()->company_id,$item_id);
		if($isAuditedBefore->cnum > 0){
			$auditbefore= $remarks;
		} else {
			$auditbefore = $remarks;
			$ammend->create(array(
				'branch_id' =>$branchid,
				'item_id' => $item_id,
				'rack_id' =>$rackid,
				'qty' => $qty,
				'ammend_qty' => $qty,
				'created' => time(),
				'witness1' => $witness1,
				'witness2' => $witness2,
				'remarks' => $auditbefore,
				'audit_id' => $auditid,
				'company_id' => $user->data()->company_id,
				'is_active' =>1
			));
		}

		//check if inv exists
		if($inventory->checkIfItemExist($item_id,$branchid,$user->data()->company_id,$rackid)){
			$curinventory = $inventory->getQty($item_id,$branchid,$rackid);
			$inventory->addInventory($item_id,$branchid,$qty,false,$rackid); 	// add inventory
			// monitoring
			$inv_mon = new Inventory_monitoring();
			$newqty = $curinventory->qty + $qty;
			$inv_mon->create(array(
				'item_id' => $item_id,
				'rack_id' => $rackid,
				'branch_id' => $branchid,
				'page' => 'ajax/ajax_inventoryadjustment',
				'action' => 'Update',
				'prev_qty' => $curinventory->qty,
				'qty_di' => 1,
				'qty' => $qty,
				'new_qty' => $newqty,
				'created' => time(),
				'user_id' => $user->data()->id,
				'remarks' => $auditbefore,
				'is_active' => 1,
				'company_id' => $user->data()->company_id
			));

		} else {

			$curinventory = 0;

			$inventory->addInventory($item_id,$branchid,$qty,true,$rackid); 	// add inventory
			// monitoring
			$inv_mon = new Inventory_monitoring();
			$newqty = 0 + $qty;
			$inv_mon->create(array(
				'item_id' => $item_id,
				'rack_id' => $rackid,
				'branch_id' => $branchid,
				'page' => 'ajax/ajax_inventoryadjustment',
				'action' => 'Insert',
				'prev_qty' => 0,
				'qty_di' => 1,
				'qty' => $qty,
				'new_qty' => $newqty,
				'created' => time(),
				'user_id' => $user->data()->id,
				'remarks' => $auditbefore,
				'is_active' => 1,
				'company_id' => $user->data()->company_id
			));
		}
		echo "Inventory updated successfully";
	}else if($q == 10){
			$user = new User();
			$rackaudit = new Rack_audit_sp();

			$branch_id = Input::get('branch');
			$comp = $user->data()->company_id;
			$stillaudit = $rackaudit->getStillAuditing($branch_id);

			if($stillaudit){
				$retstring = '';
				$ctr = 0;
				foreach($stillaudit as $r){
					$rid = $r->rack_id;
					$ctr += 1;
					$retstring .= "<a class='text-success' href='#' onclick='getThisRack($rid,$branch_id,$comp);'>" . $r->rack ."</a>, ";
				}
				$retstring = rtrim($retstring, ", ");
				echo "Auditing $ctr rack(s): " . $retstring;
			} else {
				echo "";
			}

		}

