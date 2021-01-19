<?php
	include 'ajax_connection.php';

	$functionName = Input::get("functionName");


	if(function_exists($functionName)){
		$functionName();
	}

	function saveCR(){
		$data = Input::get('data');
		$cr_number = Input::get('cr_number');
		if($data){
			$data = json_decode($data);
			if($data){
				foreach($data as $d){
					$user_dep = new User_credit($d->id);
					$total = $user_dep->data()->total;
					$mem = new Member($user_dep->data()->member_id);
					$member_name = $mem->data()->lastname;
					$user_dep->addCrLog($member_name,$total,$d->id,$cr_number);
					$user_dep->update(['cr_number' => $cr_number],$d->id);
				}
			}
			echo "CR updated successfully.";
		}
	}

	function crList(){
		$dep = new User_credit();
		$dt1 = Input::get('dt1');
		$dt2 = Input::get('dt2');
		if($dt1 && $dt2){
			$dt1 = strtotime($dt1);
			$dt2 = strtotime($dt2 . " 1 day -1 sec");
		} else {
			$dt1 = date("F Y");
			$dt2 = strtotime($dt1 . "1 month -1 sec");
			$dt1 = strtotime($dt1);
		}

		$list = $dep->getCRList($dt1,$dt2);

		if($list){
			echo "<table class='table'>";
			echo "<tr><th>CR</th><th>Total</th><th>Created at</th><th></th></tr>";
			foreach($list as $l){

				echo "<tr>";
				echo "<td class='withTopBorder'>".$l->cr_number."</td>";
				echo "<td class='withTopBorder'>".$l->cr_total."</td>";
				echo "<td class='withTopBorder'>".date('m/d/Y',$l->dt_created)."</td>";
				echo "<td class='withTopBorder'><button class='btn btn-default btnDetails' cr_number='".$l->cr_number."'>Details</button></td>";

				echo "</tr>";
			}
			echo "</table>";
		} else {
			echo "<div class='alert aler-info'>No saved CR</div>";
		}

	}

	function crDeposit(){

		$dt1 = Input::get('dt1');
		$dt2 = Input::get('dt2');
		$member_id = Input::get('member_id');

		// select all dep

		$dep = new User_credit();

		if($dt1 && $dt2){
			$dt1 = strtotime($dt1);
			$dt2 = strtotime($dt2 . " 1 day -1 sec");
		} else {
			$dt1 = date("F Y");
			$dt2 = strtotime($dt1 . "1 month -1 sec");
			$dt1 = strtotime($dt1);
		}

		$list = $dep->getCRDeposits($dt1,$dt2,$member_id);

		if($list){

			echo "<div class='row'>";
			echo "<div class='col-md-3'>";
			echo "<input class='form-control' id='cr_number' placeholder='Enter CR Number'>";
			echo "</div>";
			echo "<div class='col-md-3'>";
			echo "<button class='btn btn-default' id='btnSaveCr'>SAVE CR</button>";
			echo "</div>";
			echo "</div><br>";
			echo "<table id='tblDep' class='table table-bordered'>";
			echo "<thead>";
			echo "<tr><th>Client</th><th>Created</th><th class='text-right'>Amount</th></tr>";
			echo "</thead>";
			$total = 0;

			echo "<tbody>";
			foreach($list as $l){

				echo "<tr data-id='".$l->id."' >";

				echo "<td style='border-top:1px solid #ccc;'>$l->member_name</td>";
				echo "<td style='border-top:1px solid #ccc;'>" . date('m/d/Y',$l->created). "</td>";
				echo "<td style='border-top:1px solid #ccc;' class='text-right' >$l->total</td>";

				echo "</tr>";

				$total += $l->total;

			}

			echo "</tbody>";

			echo "<tfoot>";
			echo "<tr><th></th><th></th><th class='text-right'>". number_format($total,2). "</th></tr>";
			echo "</tfoot>";
			echo "</table>";



		} else {
			echo "<div class='alert alert-info'>No record found.</div>";
		}
	}

	function crDepositDetails(){

		$cr_number = Input::get('cr_number');
		$dep = new User_credit();
		$list = $dep->getCRDepositDetails($cr_number);

		if($list){

		/*	echo "<div class='row'>";
			echo "<div class='col-md-3'>";
			echo "<input class='form-control' id='cr_number' placeholder='Enter CR Number'>";
			echo "</div>";
			echo "<div class='col-md-3'>";
			echo "<button class='btn btn-default' id='btnSaveCr'>SAVE CR</button>";
			echo "</div>";
			echo "</div><br>";*/
			echo "<table id='tblDep' class='table table-bordered'>";
			echo "<thead>";
			echo "<tr><th>Client</th><th>Created</th><th class='text-right'>Amount</th></tr>";
			echo "</thead>";
			$total = 0;
			echo "<tbody>";

			foreach($list as $l){
				echo "<tr data-id='".$l->id."' >";
				echo "<td style='border-top:1px solid #ccc;'>$l->member_name</td>";
				echo "<td style='border-top:1px solid #ccc;'>".date('m/d/Y',$l->dep_created_at)."</td>";
				echo "<td style='border-top:1px solid #ccc;' class='text-right' >$l->amount</td>";
				echo "</tr>";
				$total += $l->amount;
			}

			echo "</tbody>";
			echo "<tfoot>";
			echo "<tr><th></th><th></th><th class='text-right'>". number_format($total,2). "</th></tr>";
			echo "</tfoot>";
			echo "</table>";



		} else {
			echo "<div class='alert alert-info'>No record found.</div>";
		}
	}

	function getOtherIncome(){

		$other_income = new Other_income();

		$date_from = Input::get('date_from');

		$date_to = Input::get('date_to');

		$member_id = Input::get('member_id');


		if(!$date_from || !$date_to){

			$date_from = date('m/01/Y');
			$date_to = date('m/d/Y', strtotime($date_from . "1 month -1 min"));

		}

		$list = $other_income->getRecord($date_from,$date_to,$member_id);

		echo "<p>Date From: <strong>".$date_from."</strong> Date To: <strong>".$date_to."</strong></p>";

		if($list){

			echo "<table id='tblForApproval' class='table table-bordered'>";
			echo "<thead>";
			echo "<tr><th>Client/Source</th><th>Created At</th><th>Amount</th><th>Remarks</th><th>CR Number</th></tr>";
			echo "</thead>";
			echo "<tbody>";
			foreach($list as $l){

				$income  = ($l->member_name) ? $l->member_name : $l->other_source;
				$item_list= "";
				if($l->item_list && $l->item_list != '[]'){
					$items = json_decode($l->item_list);
					if($items){

						$item_list = "<table style='width:450px;margin-top:5px;' class='table table-bordered'>";
						$item_list .= "<thead>";
						$item_list .= "<tr><th>Description</th><th>Qty</th><th>Price</th><th>Total</th></tr>";
						$item_list .= "</thead>";
						$item_list .= "<tbody>";

						foreach($items as $i){
							$item_list .= "<tr><td style='width:250px;' >".$i->item_description."</td><td style='width:100px;'>".$i->item_qty."</td><td  style='width:100px;'>". number_format($i->item_price,2) ."</td><td>". number_format(($i->item_price*$i->item_qty),2) ."</td></tr>";
						}

						$item_list .= "</tbody>";
						$item_list .= "</table>";
						$l->created_at = date('m/d/Y',$l->created);
						$item_list .= "<button data-list='". json_encode($l) ."' class='btn btn-default btnPrint'>Print</button>";
					}
				}

				echo "<tr><td><strong>$income</strong> $item_list</td><td>".date('m/d/Y',$l->created)."</td><td>".number_format($l->amount,2)."</td><td>".$l->remarks."</td><td>".$l->cr_number."</td></tr>";

			}
			echo "</tbody>";
			echo "</table>";

		} else {
			echo "<div class='alert alert-info'>No record</div>";
		}

	}



	function addIncome(){

		$amount = Input::get('amount');

		$member_id = Input::get('member_id');

		$remarks = Input::get('remarks');

		$cr_number = Input::get('cr_number');

		$other_source = Input::get('other_source');

		$items = Input::get('items');

		$other_income = new Other_income();

		$other_income->create(
			[
				'amount' => $amount,
				'cr_number' => $cr_number,
				'remarks' => $remarks,
				'created' => time(),
				'member_id' => $member_id,
				'other_source' => $other_source,
				'item_list' => $items
			]
		);

		echo "Income Added";

	}

