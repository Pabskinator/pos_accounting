<?php
	include 'ajax_connection.php';

	$functionName = Input::get("functionName");
	$functionName();

	function sendMail($fromEmail='',$fromName='',$email = [], $subject='', $body = '', $altbody ='',$replyTo=''){
		$mail = new PHPMailer;
		//$mail->SMTPDebug = 3;                               // Enable verbose debug output

		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = 'mail.apollosystems.com.ph';  // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = '_mainaccount@apollosystems.com.ph';                 // SMTP username
		$mail->Password = '409186963@StephenWang';                           // SMTP password
		$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 25;                                    // TCP port to connect to
		$mail->setFrom('_mainaccount@apollosystems.com.ph', $fromName);
		if(count($email) > 0){
			foreach($email as $e){
				$mail->addAddress($e, 'test ');
			}
		}

		// Add a recipient              // Name is optional
		$mail->addReplyTo($replyTo, '');

		// Optional name
		$mail->isHTML(true);                                  // Set email format to HTML

		$mail->Subject = $subject;
		$mail->Body    = $body;
		$mail->AltBody = $altbody;

		if(!$mail->send()) {
			return false;
			//echo 'Mailer Error: ' . $mail->ErrorInfo;
		} else {
			return true;
		}
	}

	function getAddInvLog(){
		$id= Input::get('id');
		$user = new User();
		$parent = new Add_batch_inv($id);
		$cls =  new Add_batch_inv_detail();
		$details = $cls->getDetails($id,$user->data()->company_id);

		if($details){
			echo "<table class='table'>";
			echo "<thead><tr><th>Rack</th><th>Item</th><th>Qty</th><th>Total</th><th>Remarks</th></tr></thead>";
			echo "<tbody>";
			foreach($details as $det){
				$total = $det->price * $det->qty;
				$rem = ($det->remarks) ? $det->remarks : "<i class='fa fa-ban'></i>";
				echo "<tr><td>$det->rack</td><td>$det->item_code <small class='text-danger span-block'>$det->description</small></td><td>". formatQuantity($det->qty) . "</td><td>".number_format($total,2)."</td><td>$rem</td></tr>";
			}
			echo "</tbody>";
			echo "</table>";
			if($parent->data()->is_pending == 1){
			echo "<p class='text-muted'> * Inventory will be added once approved.</p>"; }
		} else {
			echo "<p>No item found.</p>";
		}
		if($parent->data()->is_pending == 1){
			if($user->hasPermission('inventory_app')){
				echo "<div class='text-right'>";
				echo "<button class='btn btn-danger btnDecline' data-id='$id'>Decline</button> ";
				echo "<button class='btn btn-success btnApprove' data-id='$id'>Approve</button> ";
				echo "</div>";
			}

		}
	}

	function batchInvDecline(){

		$id = Input::get('id');

		if($id){
			$parent = new Add_batch_inv();
			$parent->update(['is_pending' => 2],$id);
			echo "Declined successfully.";

		}


	}

	function batchInvAdd(){

		$id = Input::get('id');

		if($id){

			$parent = new Add_batch_inv($id);
			if($parent->data()->is_pending == 1){
				$cls =  new Add_batch_inv_detail();
				$details = $cls->getDetails($id);
				if($details){
					$branch_id = $parent->data()->to_branch_id;
					$inventory = new Inventory();
					$user = new User();
					foreach($details as $det){
						$item_id = $det->item_id;
						$rack_id = $det->rack_id;
						$qty = $det->qty;
						if($inventory->checkIfItemExist($item_id,$branch_id,$user->data()->company_id,$rack_id)){
							$curinventory = $inventory->getQty($item_id,$branch_id,$rack_id);
							$inventory->addInventory($item_id,$branch_id,$qty,false,$rack_id);
							// monitoring
							$inv_mon = new Inventory_monitoring();
							$newqty = $curinventory->qty + $qty;
							$inv_mon->create(array(
								'item_id' => $item_id,
								'rack_id' => $rack_id,
								'branch_id' => $branch_id,
								'page' => 'admin/addinventory',
								'action' => 'Update',
								'prev_qty' => $curinventory->qty,
								'qty_di' => 1,
								'qty' => $qty,
								'new_qty' => $newqty,
								'created' => time(),
								'user_id' => $user->data()->id,
								'remarks' => 'Add inventory',
								'is_active' => 1,
								'company_id' => $user->data()->company_id
							));

						} else {
							$curinventory =0;
							$inventory->addInventory($item_id,$branch_id,$qty,true,$rack_id);
							// monitoring

							$inv_mon = new Inventory_monitoring();
							$newqty = $curinventory + $qty;
							$inv_mon->create(array(
								'item_id' => $item_id,
								'rack_id' => $rack_id,
								'branch_id' => $branch_id,
								'page' => 'admin/addinventory',
								'action' => 'Insert',
								'prev_qty' => $curinventory,
								'qty_di' => 1,
								'qty' => $qty,
								'new_qty' => $newqty,
								'created' => time(),
								'user_id' => $user->data()->id,
								'remarks' => 'Add inventory',
								'is_active' => 1,
								'company_id' => $user->data()->company_id
							));
						}

					}
				}
				$parent->update(['is_pending' => 0],$id);
				echo "Request updated successfully.";
			}


		}


	}

	function salesBreakDown(){
		$user = new User();
		$sales = new Sales();
		$dt1 = Input::get('dt1');
		$dt2 = Input::get('dt2');
		$sales_type = Input::get('sales_type');
		$branch_id = Input::get('branch_id');
		if(!$dt1 || ! $dt2){
			$dt1 = strtotime(date('m/d/Y'));
			$dt2 = strtotime(date('m/d/Y') . "1 day -1 sec");
		} else {
			$dt1 = strtotime($dt1);
			$dt2 = strtotime($dt2 . "1 day -1 sec");
		}
		if(!$branch_id){
			$branch_id = $user->data()->branch_id;
		}
		// group by inv
		// group by dr
		// order by inv/dr desc
		$from = date('m/d/Y',$dt1);
		$to = date('m/d/Y',$dt2);
		echo "<div id='printablediv'>";
		if($from == $to ){
			echo "<p class='text-center'>Store sales $from</p>";
		} else {
			echo "<p class='text-center'>Store sales $from - $to</p>";
		}
		$resultinv = $sales->getStoreSales($dt1,$dt2,$user->data()->company_id,$branch_id,1,$sales_type,true);
		$resultdr = $sales->getStoreSales($dt1,$dt2,$user->data()->company_id,$branch_id,2,$sales_type,true);
		$resultsr = $sales->getStoreSales($dt1,$dt2,$user->data()->company_id,$branch_id,3,$sales_type,true);
		$resultpr = $sales->getStoreSales($dt1,$dt2,$user->data()->company_id,$branch_id,5,$sales_type,true);

		$totalinv = 0;
		$totaldr = 0;
		$totalfinalcon = 0;
		$totalfinalconf=0;
		$totalsr = 0;
		$totalpr = 0;


		$payment_method = Input::get('payment_method');
		echo "<table style='font-size:0.8em' class='table table-bordered'>";
		echo "<thead><tr><th>Date</th><th>Store</th><th>Partner</th><th>Salesman</th><th>Description</th><th>Total</th><th>Deduction</th><th>Cash</th><th>Check</th><th>DD</th><th>CC</th><th>AR</th><th>C</th><th>F</th><th>Remarks</th></tr></thead>";
		echo "<tbody>";
		if($resultinv){

			$crud = new Crud();
			$totaltotal = 0;
			$totalcash = 0;
			$totalcheque = 0;
			$totalbanktransfer=0;
			$totalcreditcard= 0;
			$totalmember_credit=0;
			$totalcon = 0;
			$totalconf = 0;
			$totaldeduction=0;

			foreach($resultinv as $inv){
				$paymentdet = new Payment($inv->payment_id);
				$cash = $crud->get_active('cash',array('payment_id','=',$inv->payment_id));
				$cheque = $crud->get_active('cheque',array('payment_id','=',$inv->payment_id));
				$credit_card = $crud->get_active('credit_card',array('payment_id','=',$inv->payment_id));
				$bank_transfer = $crud->get_active('bank_transfer',array('payment_id','=',$inv->payment_id));
				$con = $crud->get_active('payment_consumable',array('payment_id','=',$inv->payment_id));
				$conf = $crud->get_active('payment_consumable_freebies',array('payment_id','=',$inv->payment_id));
				$member_credit = $crud->get_active('member_credit',array('payment_id','=',$inv->payment_id));
				$deductions = $crud->get_active('deductions',array('payment_id','=',$inv->payment_id));

				$skip = skipTransactionBaseOnPayment($payment_method,$cash,$cheque,$credit_card,$bank_transfer,$con,$conf,$member_credit);
				if(!$skip) continue;

				$toremarks='';
				$totaltotal += $inv->stotal;
				if($inv->station_name){
					$sn = $inv->station_name;
				}else {
					$sn = "Walk In";
				}
				if($inv->mln){
					$mn = ucwords($inv->mln . ", " .$inv->mfn . " " . $inv->mmn);
				}else {
					$mn = "Walk In Customer";
				}
				if($inv->reserved_by){
					$ruser = new User($inv->reserved_by);
					$soldby = ucwords($ruser->data()->lastname . ", " .$ruser->data()->firstname . " " . $ruser->data()->middlename);
				} else {
					$soldby = ucwords($inv->lastname . ", " .$inv->firstname . " " . $inv->middlename);
				}
				if($inv->whlastname){
					$soldby = ucwords($inv->whlastname . ", " .$inv->whfirstname);
				}

				$desc = "";
				if($inv->invoice){
					$desc .= "<span style='display:block;'>Inv# ".$inv->invoice . "</span>";
				}
				if($inv->dr){
					$desc .= "<span style='display:block;'>Dr# ".$inv->dr . "</span>";
				}
				if($inv->ir){
					$desc .= "<span style='display:block;'>PR# ".$inv->ir . "</span>";
				}
				if($inv->sr){
					$desc .= "<span style='display:block;'>SR# ".$inv->sr . "</span>";
				}
				echo "<tr>";
				echo "<td style='border-bottom:1px solid #ccc;'>" . date('m/d/y',$inv->sold_date) . "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>" . $sn . "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>" . $mn . "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>" .$soldby . "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>" .$desc . "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>" . number_format($inv->stotal,2) . "</td>";
				echo "<td style='border-bottom:1px solid #ccc ;'>";
				if($deductions){
					foreach($deductions as $d){
						$totalinv +=  $d->amount; //todeductba
						$totaldeduction += $d->amount;
						echo "<span style='display:block;'>".number_format($d->amount,2)."</span>";
					}
				}else {
					echo "0.00";
				}

				echo "</td>";
				echo "<td style='border-bottom:1px solid #ccc ;'>";
				if($cash){
					foreach($cash as $c){
						$totalinv +=  $c->amount;
						$totalcash += $c->amount;
						echo "<span style='display:block;'>".number_format($c->amount,2)."</span>";
					}
				}else {
					echo "0.00";
				}

				echo "</td>";

				echo "<td style='border-bottom:1px solid #ccc;'>";
				if($cheque){
					foreach($cheque as $c){
						$totalinv +=  $c->amount;
						$totalcheque += $c->amount;
						echo "<span style='display:block;'>".number_format($c->amount,2)."</span>";
						$toremarks .= "<span style='display:block;'>Cheque " . $c->bank . " " .date('m/d/Y',$c->payment_date)."</span>";
					}
				}else {
					echo "0.00";
				}

				echo "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>";
				if($bank_transfer){
					foreach($bank_transfer as $c){
						$totalinv +=  $c->amount;
						$totalbanktransfer += $c->amount;
						echo "<span style='display:block;'>".number_format($c->amount,2)."</span>";
						$toremarks .= "<span style='display:block;'>" . $c->bankfrom_name ."</span>";
					}
				}else {
					echo "0.00";
				}
				echo "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>";
				if($credit_card){
					foreach($credit_card as $c){
						$totalinv +=  $c->amount;
						$totalcreditcard += $c->amount;
						echo "<span style='display:block;'>".number_format($c->amount,2)."</span>";
						$toremarks .= "<span style='display:block;'>" . $c->bank_name ."</span>";
					}
				} else {
					echo "0.00";
				}
				echo "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>";
				if($member_credit){
					foreach($member_credit as $c){
						$totalinv +=  ($c->amount - $c->amount_paid);
						$totalmember_credit += ($c->amount - $c->amount_paid);
						echo "<span style='display:block;'>".number_format(($c->amount - $c->amount_paid),2)."</span>";
						echo "<span style='display:block;'>Paid: ".number_format(($c->amount_paid),2)."</span>";
					}
				} else {
					echo "0.00";
				}
				echo "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>";
				if($con){
					foreach($con as $c){
						$totalfinalcon +=  $c->amount;
						$totalcon += $c->amount;
						echo "<span style='display:block;'>".number_format($c->amount,2)."</span>";
					}
				}else {
					echo "0.00";
				}
				echo "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>";
				if($conf){
					foreach($conf as $c){
						$totalfinalconf +=  $c->amount;
						$totalconf += $c->amount;
						echo "<span style='display:block;'>".number_format($c->amount,2)."</span>";
					}
				}else {
					echo "0.00";
				}
				echo "</td>";
				if($paymentdet->data()->remarks){
					$toremarks .= "<span style='display:block;'>" . $paymentdet->data()->remarks ."</span>";
				}
				if($inv->wh_remarks){
					$toremarks .= "<span style='display:block;'>" . $inv->wh_remarks ."</span>";
				}

				echo "<td style='border-bottom:1px solid #ccc;width: 150px;'>";
				echo $toremarks;
				?>
				<button data-payment_id='<?php echo $inv->payment_id; ?>' class='btn btn-default btn-sm getPTDetails'>
					<i class='fa fa-list'></i>
				</button>
				<?php
				echo "</td>";
				echo "</tr>";
			}
			echo "<tr  class='bg-info'><td></td><td></td><td></td><td></td><td></td>";
			echo "<td><strong>".number_format($totaltotal,2)."</strong></td><td><strong>".number_format($totaldeduction,2)."</strong></td><td><strong>".number_format($totalcash,2)."</strong></td><td><strong>".number_format($totalcheque,2)."</strong></td><td><strong>".number_format($totalbanktransfer,2)."</strong></td><td><strong>".number_format($totalcreditcard,2)."</strong></td><td><strong>".number_format($totalmember_credit,2)."</strong></td><td><strong>".number_format($totalcon,2)."</strong></td><td><strong>".number_format($totalconf,2)."</strong></td><td></td></tr>";

		}

		if($resultdr){

			$crud = new Crud();
			$totaltotal = 0;
			$totalcash = 0;
			$totalcheque = 0;
			$totalbanktransfer=0;
			$totalcreditcard= 0;
			$totalmember_credit=0;
			$totalcon = 0;
			$totalconf = 0;
			$totaldeduction=0;
			foreach($resultdr as $inv){
				$paymentdet = new Payment($inv->payment_id);
				$cash = $crud->get_active('cash',array('payment_id','=',$inv->payment_id));
				$cheque = $crud->get_active('cheque',array('payment_id','=',$inv->payment_id));
				$credit_card = $crud->get_active('credit_card',array('payment_id','=',$inv->payment_id));
				$bank_transfer = $crud->get_active('bank_transfer',array('payment_id','=',$inv->payment_id));
				$con = $crud->get_active('payment_consumable',array('payment_id','=',$inv->payment_id));
				$conf = $crud->get_active('payment_consumable_freebies',array('payment_id','=',$inv->payment_id));
				$member_credit = $crud->get_active('member_credit',array('payment_id','=',$inv->payment_id));
				$deductions = $crud->get_active('deductions',array('payment_id','=',$inv->payment_id));
				$skip = skipTransactionBaseOnPayment($payment_method,$cash,$cheque,$credit_card,$bank_transfer,$con,$conf,$member_credit);
				if(!$skip) continue;
				$toremarks='';
				$totaltotal += $inv->stotal;
				if($inv->station_name){
					$sn = $inv->station_name;
				} else {
					$sn = "Walk In";
				}
				if($inv->mln){
					$mn = ucwords($inv->mln . ", " .$inv->mfn . " " . $inv->mmn);
				}else {
					$mn = "Walk In Customer";
				}
				$desc = "";
				if($inv->reserved_by){
					$ruser = new User($inv->reserved_by);
					$soldby = ucwords($ruser->data()->lastname . ", " .$ruser->data()->firstname . " " . $ruser->data()->middlename);
				} else {
					$soldby = ucwords($inv->lastname . ", " .$inv->firstname . " " . $inv->middlename);
				}
				if($inv->whlastname){
					$soldby = ucwords($inv->whlastname . ", " .$inv->whfirstname);
				}
				if($inv->invoice){
					$desc .= "<span style='display:block;'>Inv# ".$inv->invoice . "</span>";
				}
				if($inv->dr){
					$desc .= "<span style='display:block;'>DR# ".$inv->dr . "</span>";
				}
				if($inv->ir){
					$desc .= "<span style='display:block;'>PR# ".$inv->ir . "</span>";
				}
				if($inv->sr){
					$desc .= "<span style='display:block;'>SR# ".$inv->sr . "</span>";
				}
				echo "<tr>";
				echo "<td style='border-bottom:1px solid #ccc;'>" .  date('m/d/y',$inv->sold_date) . "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>" . $sn . "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>" .$mn. "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>" .$soldby . "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>" . $desc . "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>" . number_format($inv->stotal,2) . "</td>";
				echo "<td style='border-bottom:1px solid #ccc ;'>";
				if($deductions){
					foreach($deductions as $d){
						$totaldr +=  $d->amount;
						$totaldeduction += $d->amount;
						echo "<span style='display:block;'>".number_format($d->amount,2)."</span>";
					}
				}else {
					echo "0.00";
				}

				echo "</td>";
				echo "<td style='border-bottom:1px solid #ccc ;'>";
				if($cash){
					foreach($cash as $c){
						$totaldr +=  $c->amount;
						$totalcash += $c->amount;
						echo "<span style='display:block;'>".number_format($c->amount,2)."</span>";
					}
				}else {
					echo "0.00";
				}

				echo "</td>";

				echo "<td style='border-bottom:1px solid #ccc;'>";
				if($cheque){
					foreach($cheque as $c){
						$totaldr +=  $c->amount;
						$totalcheque += $c->amount;
						echo "<span style='display:block;'>".number_format($c->amount,2)."</span>";
						$toremarks .= "<span style='display:block;'>Cheque " . $c->bank . " " .date('m/d/Y',$c->payment_date)."</span>";
					}
				}else {
					echo "0.00";
				}

				echo "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>";
				if($bank_transfer){
					foreach($bank_transfer as $c){
						$totaldr +=  $c->amount;
						$totalbanktransfer += $c->amount;
						echo "<span style='display:block;'>".number_format($c->amount,2)."</span>";
						$toremarks .= "<span style='display:block;'>" . $c->bankfrom_name ."</span>";
					}
				}else {
					echo "0.00";
				}
				echo "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>";
				if($credit_card){
					foreach($credit_card as $c){
						$totaldr +=  $c->amount;
						$totalcreditcard += $c->amount;
						echo "<span style='display:block;'>".number_format($c->amount,2)."</span>";
						$toremarks .= "<span style='display:block;'>" . $c->bank_name ."</span>";
					}
				}else {
					echo "0.00";
				}
				echo "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>";
				if($member_credit){
					foreach($member_credit as $c){
						$totaldr +=  $c->amount-$c->amount_paid;
						$totalmember_credit += $c->amount-$c->amount_paid;
						echo "<span style='display:block;'>".number_format($c->amount-$c->amount_paid,2)."</span>";
						echo "<span style='display:block;'>Paid: ".number_format(($c->amount_paid),2)."</span>";
					}
				}else {
					echo "0.00";
				}
				echo "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>";
				if($con){
					foreach($con as $c){
						$totalfinalcon += $c->amount;
						$totalcon += $c->amount;
						echo "<span style='display:block;'>".number_format($c->amount,2)."</span>";
					}
				}else {
					echo "0.00";
				}
				echo "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>";
				if($conf){
					foreach($conf as $c){
						$totalfinalconf += $c->amount;
						$totalconf += $c->amount;
						echo "<span style='display:block;'>".number_format($c->amount,2)."</span>";
					}
				}else {
					echo "0.00";
				}
				echo "</td>";
				if($paymentdet->data()->remarks){
					$toremarks .= "<span style='display:block;'>" . $paymentdet->data()->remarks ."</span>";
				}

				if($inv->wh_remarks){
					$toremarks .= "<span style='display:block;'>" . $inv->wh_remarks ."</span>";
				}


				echo "<td style='border-bottom:1px solid #ccc;width: 150px;'>";
				echo $toremarks;
				?>
				<button data-payment_id='<?php echo $inv->payment_id; ?>' class='btn btn-default btn-sm getPTDetails'>
					<i class='fa fa-list'></i>
				</button>
				<?php
				echo "</td>";
				echo "</tr>";
			}
			echo "<tr class='bg-info'><td></td><td></td><td></td><td></td><td></td>";
			echo "<td><strong>".number_format($totaltotal,2)."</strong></td><td><strong>".number_format($totaldeduction,2)."</strong></td><td><strong>".number_format($totalcash,2)."</strong></td><td><strong>".number_format($totalcheque,2)."</strong></td><td><strong>".number_format($totalbanktransfer,2)."</strong></td><td><strong>".number_format($totalcreditcard,2)."</strong></td><td><strong>".number_format($totalmember_credit,2)."</strong></td><td><strong>".number_format($totalcon,2)."</strong></td><td><strong>".number_format($totalconf,2)."</strong></td><td></td></tr>";

		}
		if($resultpr){

			$crud = new Crud();
			$totaltotal = 0;
			$totalcash = 0;
			$totalcheque = 0;
			$totalbanktransfer=0;
			$totalcreditcard= 0;
			$totalmember_credit=0;
			$totalcon = 0;
			$totalconf = 0;
			$totaldeduction = 0;
			foreach($resultpr as $inv){
				$cash = $crud->get_active('cash',array('payment_id','=',$inv->payment_id));
				$cheque = $crud->get_active('cheque',array('payment_id','=',$inv->payment_id));
				$credit_card = $crud->get_active('credit_card',array('payment_id','=',$inv->payment_id));
				$bank_transfer = $crud->get_active('bank_transfer',array('payment_id','=',$inv->payment_id));
				$con = $crud->get_active('payment_consumable',array('payment_id','=',$inv->payment_id));
				$conf = $crud->get_active('payment_consumable_freebies',array('payment_id','=',$inv->payment_id));
				$member_credit = $crud->get_active('member_credit',array('payment_id','=',$inv->payment_id));
				$deductions = $crud->get_active('deductions',array('payment_id','=',$inv->payment_id));
				$skip = skipTransactionBaseOnPayment($payment_method,$cash,$cheque,$credit_card,$bank_transfer,$con,$conf,$member_credit);
				if(!$skip) continue;
				$toremarks='';
				$totaltotal += $inv->stotal;
				echo "<tr>";
				if($inv->station_name){
					$sn = $inv->station_name;
				}else {
					$sn = "Walk In";
				}
				if($inv->mln){
					$mn = ucwords($inv->mln . ", " .$inv->mfn . " " . $inv->mmn);
				}else {
					$mn = "Walk In Customer";
				}
				if($inv->reserved_by){
					$ruser = new User($inv->reserved_by);
					$soldby = ucwords($ruser->data()->lastname . ", " .$ruser->data()->firstname . " " . $ruser->data()->middlename);
				} else {
					$soldby = ucwords($inv->lastname . ", " .$inv->firstname . " " . $inv->middlename);
				}
				if($inv->whlastname){
					$soldby = ucwords($inv->whlastname . ", " .$inv->whfirstname);
				}
				$desc = "";
				if($inv->invoice){
					$desc .= "<span style='display:block;'>Inv# ".$inv->invoice . "</span>";
				}
				if($inv->dr){
					$desc .= "<span style='display:block;'>Dr# ".$inv->dr . "</span>";
				}
				if($inv->ir){
					$desc .= "<span style='display:block;'>PR# ".$inv->ir . "</span>";
				}
				if($inv->sr){
					$desc .= "<span style='display:block;'>SR# ".$inv->sr . "</span>";
				}
				echo "<td style='border-bottom:1px solid #ccc;'>" . date('m/d/y',$inv->sold_date) . "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>" . $sn . "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>" .$mn. "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>" .$soldby. "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>$desc </td>";
				echo "<td style='border-bottom:1px solid #ccc;'>" . number_format($inv->stotal,2) . "</td>";
				echo "<td style='border-bottom:1px solid #ccc ;'>";
				if($deductions){
					foreach($deductions as $d){
						$totalpr +=  $d->amount;
						$totaldeduction += $d->amount;
						echo "<span style='display:block;'>".number_format($d->amount,2)."</span>";
					}
				}else {
					echo "0.00";
				}

				echo "</td>";
				echo "<td style='border-bottom:1px solid #ccc ;'>";
				if($cash){
					foreach($cash as $c){
						$totalpr += $c->amount;
						$totalcash += $c->amount;
						echo "<span style='display:block;'>".number_format($c->amount,2)."</span>";
					}
				}else {
					echo "0.00";
				}

				echo "</td>";

				echo "<td style='border-bottom:1px solid #ccc;'>";
				if($cheque){
					foreach($cheque as $c){
						$totalsr += $c->amount;
						$totalcheque += $c->amount;
						echo "<span style='display:block;'>".number_format($c->amount,2)."</span>";
						$toremarks .= "<span style='display:block;'>Cheque " . $c->bank . " " .date('m/d/Y',$c->payment_date)."</span>";
					}
				}else {
					echo "0.00";
				}

				echo "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>";
				if($bank_transfer){
					foreach($bank_transfer as $c){
						$totalsr += $c->amount;
						$totalbanktransfer += $c->amount;
						echo "<span style='display:block;'>".number_format($c->amount,2)."</span>";
						$toremarks .= "<span style='display:block;'>" . $c->bankfrom_name ."</span>";
					}
				}else {
					echo "0.00";
				}
				echo "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>";
				if($credit_card){
					foreach($credit_card as $c){
						$totalsr += $c->amount;
						$totalcreditcard += $c->amount;
						echo "<span style='display:block;'>".number_format($c->amount,2)."</span>";
						$toremarks .= "<span style='display:block;'>" . $c->bank_name ."</span>";
					}
				}else {
					echo "0.00";
				}
				echo "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>";
				if($member_credit){
					foreach($member_credit as $c){
						$totalsr += $c->amount-$c->amount_paid;
						$totalmember_credit += $c->amount-$c->amount_paid;
						echo "<span style='display:block;'>".number_format($c->amount-$c->amount_paid,2)."</span>";
						echo "<span style='display:block;'>Paid: ".number_format(($c->amount_paid),2)."</span>";
					}
				}else {
					echo "0.00";
				}
				echo "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>";
				if($con){
					foreach($con as $c){
						$totalsr += $c->amount;
						$totalcon += $c->amount;
						echo "<span style='display:block;'>".number_format($c->amount,2)."</span>";
					}
				}else {
					echo "0.00";
				}
				echo "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>";
				if($conf){
					foreach($conf as $c){
						$totalsr += $c->amount;
						$totalconf += $c->amount;
						echo "<span style='display:block;'>".number_format($c->amount,2)."</span>";
					}
				}else {
					echo "0.00";
				}
				echo "</td>";
				if($inv->wh_remarks){
					$toremarks .= "<span style='display:block;'>" . $inv->wh_remarks ."</span>";
				}

				echo "<td style='border-bottom:1px solid #ccc;width: 150px;'>";
				echo $toremarks;
				?>
				<button data-payment_id='<?php echo $inv->payment_id; ?>' class='btn btn-default btn-sm getPTDetails'>
					<i class='fa fa-list'></i>
				</button>
				<?php
				echo "</td>";
				echo "</tr>";
			}
			echo "<tr  class='bg-info'><td></td><td></td><td></td><td></td><td></td>";
			echo "<td><strong>".number_format($totaltotal,2)."</strong></td><td><strong>".number_format($totaldeduction,2)."</strong></td><td><strong>".number_format($totalcash,2)."</strong></td><td><strong>".number_format($totalcheque,2)."</strong></td><td><strong>".number_format($totalbanktransfer,2)."</strong></td><td><strong>".number_format($totalcreditcard,2)."</strong></td><td><strong>".number_format($totalmember_credit,2)."</strong></td><td><strong>".number_format($totalcon,2)."</strong></td><td><strong>".number_format($totalconf,2)."</strong></td><td></td></tr>";

		}
		if($resultsr){

			$crud = new Crud();
			$totaltotal = 0;
			$totalcash = 0;
			$totalcheque = 0;
			$totalbanktransfer=0;
			$totalcreditcard= 0;
			$totalmember_credit=0;
			$totalcon = 0;
			$totalconf = 0;
			$totaldeduction = 0;
			foreach($resultsr as $inv){
				$cash = $crud->get_active('cash',array('payment_id','=',$inv->payment_id));
				$cheque = $crud->get_active('cheque',array('payment_id','=',$inv->payment_id));
				$credit_card = $crud->get_active('credit_card',array('payment_id','=',$inv->payment_id));
				$bank_transfer = $crud->get_active('bank_transfer',array('payment_id','=',$inv->payment_id));
				$con = $crud->get_active('payment_consumable',array('payment_id','=',$inv->payment_id));
				$conf = $crud->get_active('payment_consumable_freebies',array('payment_id','=',$inv->payment_id));
				$member_credit = $crud->get_active('member_credit',array('payment_id','=',$inv->payment_id));
				$deductions = $crud->get_active('deductions',array('payment_id','=',$inv->payment_id));

				$skip = skipTransactionBaseOnPayment($payment_method,$cash,$cheque,$credit_card,$bank_transfer,$con,$conf,$member_credit);
				if(!$skip) continue;
				$toremarks='';
				$totaltotal += $inv->stotal;
				echo "<tr>";
				if($inv->station_name){
					$sn = $inv->station_name;
				}else {
					$sn = "Walk In";
				}
				if($inv->mln){
					$mn = ucwords($inv->mln . ", " .$inv->mfn . " " . $inv->mmn);
				}else {
					$mn = "Walk In Customer";
				}
				if($inv->reserved_by){
					$ruser = new User($inv->reserved_by);
					$soldby = ucwords($ruser->data()->lastname . ", " .$ruser->data()->firstname . " " . $ruser->data()->middlename);
				} else {
					$soldby = ucwords($inv->lastname . ", " .$inv->firstname . " " . $inv->middlename);
				}
				if($inv->whlastname){
					$soldby = ucwords($inv->whlastname . ", " .$inv->whfirstname);
				}
				$desc = "";
				if($inv->invoice){
					$desc .= "<span style='display:block;'>Inv# ".$inv->invoice . "</span>";
				}
				if($inv->dr){
					$desc .= "<span style='display:block;'>Dr# ".$inv->dr . "</span>";
				}
				if($inv->ir){
					$desc .= "<span style='display:block;'>PR# ".$inv->ir . "</span>";
				}
				if($inv->sr){
					$desc .= "<span style='display:block;'>SR# $desc</span>";
				}
				echo "<td style='border-bottom:1px solid #ccc;'>" . date('m/d/y',$inv->sold_date) . "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>" . $sn . "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>" .$mn. "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>" .$soldby. "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>Sr# " .$inv->sr." </td>";
				echo "<td style='border-bottom:1px solid #ccc;'>" . number_format($inv->stotal,2) . "</td>";
				echo "<td style='border-bottom:1px solid #ccc ;'>";
				if($deductions){
					foreach($deductions as $d){
						$totalsr +=  $d->amount;
						$totaldeduction += $d->amount;
						echo "<span style='display:block;'>".number_format($d->amount,2)."</span>";
					}
				}else {
					echo "0.00";
				}

				echo "</td>";
				echo "<td style='border-bottom:1px solid #ccc ;'>";
				if($cash){
					foreach($cash as $c){
						$totalsr += $c->amount;
						$totalcash += $c->amount;
						echo "<span style='display:block;'>".number_format($c->amount,2)."</span>";
					}
				}else {
					echo "0.00";
				}

				echo "</td>";

				echo "<td style='border-bottom:1px solid #ccc;'>";
				if($cheque){
					foreach($cheque as $c){
						$totalsr += $c->amount;
						$totalcheque += $c->amount;
						echo "<span style='display:block;'>".number_format($c->amount,2)."</span>";
						$toremarks .= "<span style='display:block;'>Cheque " . $c->bank . " " .date('m/d/Y',$c->payment_date)."</span>";
					}
				}else {
					echo "0.00";
				}

				echo "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>";
				if($bank_transfer){
					foreach($bank_transfer as $c){
						$totalsr += $c->amount;
						$totalbanktransfer += $c->amount;
						echo "<span style='display:block;'>".number_format($c->amount,2)."</span>";
						$toremarks .= "<span style='display:block;'>" . $c->bankfrom_name ."</span>";
					}
				}else {
					echo "0.00";
				}
				echo "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>";
				if($credit_card){
					foreach($credit_card as $c){
						$totalsr += $c->amount;
						$totalcreditcard += $c->amount;
						echo "<span style='display:block;'>".number_format($c->amount,2)."</span>";
						$toremarks .= "<span style='display:block;'>" . $c->bank_name ."</span>";
					}
				}else {
					echo "0.00";
				}
				echo "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>";
				if($member_credit){
					foreach($member_credit as $c){
						$totalsr += $c->amount-$c->amount_paid;
						$totalmember_credit += $c->amount-$c->amount_paid;
						echo "<span style='display:block;'>".number_format($c->amount-$c->amount_paid,2)."</span>";
						echo "<span style='display:block;'>Paid: ".number_format(($c->amount_paid),2)."</span>";
					}
				}else {
					echo "0.00";
				}
				echo "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>";
				if($con){
					foreach($con as $c){
						$totalsr += $c->amount;
						$totalcon += $c->amount;
						echo "<span style='display:block;'>".number_format($c->amount,2)."</span>";
					}
				}else {
					echo "0.00";
				}
				echo "</td>";
				echo "<td style='border-bottom:1px solid #ccc;'>";
				if($conf){
					foreach($conf as $c){
						$totalsr += $c->amount;
						$totalconf += $c->amount;
						echo "<span style='display:block;'>".number_format($c->amount,2)."</span>";
					}
				}else {
					echo "0.00";
				}
				echo "</td>";
				echo "<td style='border-bottom:1px solid #ccc;width: 150px;'>";
				echo $toremarks;
				?>
				<button data-payment_id='<?php echo $inv->payment_id; ?>' class='btn btn-default btn-sm getPTDetails'>
					<i class='fa fa-list'></i>
				</button>
				<?php
				echo "</td>";
				echo "</tr>";
			}
			echo "<tr  class='bg-info'><td></td><td></td><td></td><td></td><td></td>";
			echo "<td><strong>".number_format($totaltotal,2)."</strong></td><td><strong>".number_format($totaldeduction,2)."</strong></td><td><strong>".number_format($totalcash,2)."</strong></td><td><strong>".number_format($totalcheque,2)."</strong></td><td><strong>".number_format($totalbanktransfer,2)."</strong></td><td><strong>".number_format($totalcreditcard,2)."</strong></td><td><strong>".number_format($totalmember_credit,2)."</strong></td><td><strong>".number_format($totalcon,2)."</strong></td><td><strong>".number_format($totalconf,2)."</strong></td><td></td></tr>";

		}
		echo "</tbody>";
		echo "</table>";
		if($totalinv){
			echo "<ul class='list-group' style='width:30%;'>";
			echo "<li class='list-group-item'>";
			echo "<span class='text-danger pull-right'>".number_format($totalinv,2)."</span>";
			echo "SI";
			echo "</li>";
		}
		if($totaldr){
			echo "<li class='list-group-item'>";
			echo "<span class='text-danger pull-right'>".number_format($totaldr,2)."</span>";
			echo "DR";
			echo "</li>";
		}
		if($totalsr){
			echo "<li class='list-group-item'>";
			echo "<span class='text-danger pull-right'>".number_format($totalsr,2)."</span>";
			echo "SR";
			echo "</li>";
		}
		if($totalpr){
			echo "<li class='list-group-item'>";
			echo "<span class='text-danger pull-right'>".number_format($totalpr,2)."</span>";
			echo "SR";
			echo "</li>";
		}
		if($totalfinalcon){
			echo "<li class='list-group-item'>";
			echo "<span class='text-danger pull-right'>".number_format($totalfinalcon,2)."</span>";
			echo "Consumables";
			echo "</li>";
		}

		if($totalfinalconf){
			echo "<li class='list-group-item'>";
			echo "<span class='text-danger pull-right'>".number_format($totalfinalconf,2)."</span>";
			echo "Freebies";
			echo "</li>";
		}

		echo "</ul>";

		echo "</div>";
		echo "<div style='clear:both;'>";
		echo "<div class='text-right'>";
		echo "<input type='button' id='btnPrintDiv' class='btn btn-default' value='PRINT'>";
		echo "</div>";
	}
	function skipTransactionBaseOnPayment($payment_method,$cashrp,$chequepm,$creditpm,$bankpm,$conamountpm,$confreepm,$member_credit){
		$ret = false;
		if($payment_method) {

				if($cashrp) {
					//1
					if(in_array('1', $payment_method)) {
						$ret = true;;
					}
				}
				if($chequepm) {
					//2
					if(in_array('2', $payment_method)) {
						$ret = true;
					}
				}
				if($creditpm) {
					// 3
					if(in_array('3', $payment_method)) {
						$ret = true;
					}
				}
				if($bankpm) {
					// 4
					if(in_array('4', $payment_method)) {
						$ret = true;
					}
				}
				if($conamountpm) {
					// 6
					if(in_array('6', $payment_method)) {
						$ret = true;
					}
				}
				if($confreepm) {
					//7
					if(in_array('7', $payment_method)) {
						$ret = true;
					}
				}
				if($member_credit) {
					//7
					if(in_array('8', $payment_method)) {
						$ret = true;
					}
				}
			} else {
			$ret = true;
		}
		return $ret;
	}

	function chequeChangeStatus(){

		$id = Input::get('id');
		$val= Input::get('val');
		$b_reason= Input::get('b_reason');
		$b_others= Input::get('b_others');

		$cheque = new Cheque($id);


		if($val == 2 || $val == 3 || $val == 4){

			$payment_id  = $cheque->data()->payment_id;
			$sale = new Sales();
			$data = $sale->getsinglesale($payment_id);

				$pcredit = new Member_credit();

				$checker = $pcredit->checkerBounce($payment_id,$cheque->data()->id);

				if(isset($checker->cnt) && $checker->cnt > 0){

					$message = "Member Credit already exists";

				} else {
					$pcredit->create(array(
						'amount' =>$cheque->data()->amount,
						'is_active' => 1,
						'created' => $data->sold_date,
						'modified' => $data->sold_date,
						'payment_id' => $payment_id,
						'member_id' => $data->member_id,
						'ref_check_number' =>  $cheque->data()->id,
						'is_cod' => 0
					));
					$message = "Invalid cheque was converted to member credit.";
				}
			 $cheque->update([
				 'status' => $val,
				 'bounce_reason' => $b_reason,
				 'other_reason' => $b_others,
			 ],$id);
		} else {
			$message = "Credit already added to user. Please encode another check";
		}
			echo $message;
	}

	function getTerminals(){
		$branchid = Input::get('branch_id');

		$retstring = "<select class='form-control' id='terminal_id' multiple>";
		$retstring .= "<option value=''></option>";
		//get terminals
		if ($branchid){
		foreach ($branchid as $value) {
			$branch = new Branch($value);
			$terminal = new Terminal();
			$terminals = $terminal->getAllTerminal($value);

			if($terminals){
				foreach($terminals as $t){
				$retstring .= "<option value='$t->id'>$t->name (".$branch->data()->name.")</option>";
				}
			}
		}
		$retstring .= "</select>";
		$retstring .= "<script>$('#terminal_id').select2({placeholder: 'Choose terminal', allowClear: true })</script>";
		echo $retstring;
	} else {
		echo "No terminal found";
	}
}

	function getStations(){
		$memid = Input::get('member_id');
		$user = new User();
		$cf = new Custom_field();
		$getstationdet = $cf->getcustomform('stations',$user->data()->company_id);
		$custom_station_name = isset($getstationdet->label_name)? strtoupper($getstationdet->label_name):'STATION';
		$custom_station_name = ucfirst(strtolower($custom_station_name));

		$retstring = "<select class='form-control' id='station_id' multiple>";
		$retstring .= "<option value=''></option>";
		//get terminals
		if ($memid){
			foreach ($memid as $value) {

				$station = new Station();
				$stations = $station->getStationByMember($value);

				if($stations){
					foreach($stations as $s){
						$retstring .= "<option value='$s->id'>$s->name</option>";
					}
				}
			}
			$retstring .= "</select>";
			$retstring .= "<script>$('#station_id').select2({placeholder: 'Choose $custom_station_name', allowClear: true })</script>";
			echo $retstring;
		} else {
			echo "No $custom_station_name found";
		}
	}

function topBranch(){
	$dt1 = Input::get('dt1');
	$dt2 = Input::get('dt2');
	$type = Input::get('type');
	$gsales = new Sales();
	$user = new User();
	// base on branch
	$branchsales = $gsales->getTotalSalesPerBranch($user->data()->company_id,$dt1,$dt2);

	$arr = [];

	if($branchsales){
		$tablestring = "<div class='list-group'>";
		$tablestring .= " <a href='#' class='list-group-item active'></a>";

		foreach($branchsales as $bb){
			if (!$bb->name) continue;
			$obj['label'] = $bb->name;
			$obj['value']= $bb->saletotal;
			array_push($arr,$obj);
			$tablestring .="<a href='#' class='list-group-item'>".$bb->name."<span class='pull-right text-danger'>".number_format($bb->saletotal,2)."</span></a>";
		}
		$tablestring .="</div>";
	}
	if ($type == 1){
		if($arr){
			echo json_encode($arr);
		} else {
			echo json_encode(array('error' => true));
		}

	} else if ($type==2){
		if(isset($tablestring)){
			echo $tablestring;
		}

	}
}
	function salesTypeTotal(){
		$dt1 = Input::get('dt1');
		$dt2 = Input::get('dt2');
		$type = Input::get('type');
		$gsales = new Sales();
		$user = new User();

		$classSalestype = new Sales_type();
		$salestypelist = $classSalestype->get_active('salestypes',array('company_id','=',$user->data()->company_id));
		$tablestring2 = "<ul class='list-group'>";
		$totalstl = 0;
		$arr = [];
		foreach($salestypelist as $stl){
			$stlres = $gsales->getTotalSalesPerSalesType($user->data()->company_id,$dt1,$dt2,$stl->id);
			$stlamount = (isset($stlres->saletotal)) ? $stlres->saletotal : 0;
			$totalstl+= $stlamount;
			$obj['label'] = $stl->name;
			$obj['value']=$stlamount;
			array_push($arr,$obj);
			$tablestring2 .= "<li class='list-group-item'> <span class='pull-right text-danger'> ".number_format($stlamount,2)."</span><strong> $stl->name</strong></li>";
		}
		$stlrescaravan = $gsales->getTotalSalesPerSalesType($user->data()->company_id,$dt1,$dt2,-1);
		$stlamountcaravan = (isset($stlrescaravan->saletotal)) ? $stlrescaravan->saletotal : 0;
		$totalstl += $stlamountcaravan;
		$obj['label'] = 'Caravan';
		$obj['value']=$stlamountcaravan;
		array_push($arr,$obj);
		$stlresnotype = $gsales->getTotalSalesPerSalesType($user->data()->company_id,$dt1,$dt2,0);
		$stlamountnotype = (isset($stlresnotype->saletotal)) ? $stlresnotype->saletotal : 0;
		$totalstl += $stlamountnotype;
		$obj['label'] = 'No type';
		$obj['value']=$stlamountnotype;
		array_push($arr,$obj);
		$tablestring2 .= "<li class='list-group-item'> <span class='pull-right text-danger'> ".number_format($stlamountcaravan,2)."</span><strong>Caravan</strong></li>";
		$tablestring2 .= "<li class='list-group-item'> <span class='pull-right text-danger'> ".number_format($stlamountnotype,2)."</span><strong>No type</strong></li>";
		$tablestring2 .= "<li class='list-group-item'> <strong><span class='pull-right'> ".number_format($totalstl,2)."</span></strong><strong>TOTAL</strong></li>";
		$tablestring2 .="</ul>";




		if ($type == 1){
			if($arr){
				echo json_encode($arr);
			} else {
				echo json_encode(array('error' => true));
			}

		} else if ($type==2){
			if(isset($tablestring2)){
				echo $tablestring2;
			}

		}
	}
	function transferMonitoring(){
		$id = Input::get('id');
		$fromwhat = Input::get('fromwhat');
		$transfer_mon = new Transfer_inventory_mon($id);
		$user = new User();
		$json = json_decode(Input::get('jsondet'),true);
		$bid = $transfer_mon->data()->branch_id;
		$branch_from = $transfer_mon->data()->branch_from;
		$payment_id = $transfer_mon->data()->payment_id;
		$inventory = new Inventory();
		$rackDisplay = new Rack();
		$from_remarks = $transfer_mon->data()->from_where;
		$dis = $rackDisplay->getRackForSelling($user->data()->branch_id);
		$isvalid = true;
		$hasdisplay = false;
		$hasother = false;
		$invaliditems = [];
		foreach($json as $i){
			$itemid = $i['item_id'];
			$torack = $i['rack_to'];
			$transferqty = $i['qty'];
			$rackid = $i['rack_from'];
			if(isset($dis->id) && !empty($dis->id) && $torack == $dis->id ){
				$hasdisplay = true;
			} else {
				$hasother = true;
			}
			if($rackid){
				$curinventoryrackFrom = $inventory->getQty($itemid,$bid,$rackid);
				if(isset($curinventoryrackFrom->qty)){
					if($curinventoryrackFrom->qty < $transferqty){
						$isvalid = false;
						$inItem = new Product($itemid);
						$invaliditems[] = $inItem->data()->description;
					}
				} else {
					$isvalid = false;
					$inItem = new Product($itemid);
					$invaliditems[] = $inItem->data()->description;
				}
			}


		}
		if(!$isvalid){
			echo "<div class='container'><p class='text-danger'>Not enough stocks for this item(s)</p>";
			foreach($invaliditems as $indi){
				echo "<p>$indi</p>";
			}
			echo "</div>";
			exit();
		}

		if($hasdisplay && !$user->hasPermission('rack_display')){
			echo "Unable to process. You don't have permission to receive on this rack";
			exit();
		}
		if($hasother && !$user->hasPermission('rack_other')){
			echo "Unable to process. You don't have permission to receive on this rack";
			exit();
		}
		if(!$isvalid){
			echo "Unable to process. Not enough stocks";
			exit();
		}
		$rackcls = new Rack();
		$addtllabel = ",$fromwhat";
		if($payment_id && $from_remarks != 'From service return item' && $from_remarks != 'From Service Liquidation'){
			$salescls = new Sales();
			$dt_sales = $salescls->getsinglesale($payment_id);
			$invlabel = $dt_sales->invoice;
			$drlabel = $dt_sales->dr;

			if($invlabel){
				$addtllabel .= " Inv#".$invlabel;
			}
			if($drlabel){
				$addtllabel .= " Dr#".$drlabel;
			}

		}
		foreach($json as $i){
			$itemid = $i['item_id'];
			$torack = $i['rack_to'];
			$transferqty = $i['qty'];
			$rackid = $i['rack_from'];
			$torackname = $rackcls->getRackName($torack);
			$torackname = $torackname->rack;
			if($rackid == 0){
				$fromrackname = '';
				$fromrackname2='';
			} else {
				$fromracknamec = $rackcls->getRackName($rackid);
				$fromrackname = 'from ' . $fromracknamec->rack;
				$fromrackname2 ='to ' . $fromracknamec->rack;
			}

			if($inventory->checkIfItemExist($itemid,$bid,$user->data()->company_id,$torack)){
				$inv_mon = new Inventory_monitoring();
				//	echo "UPDATE";
				$curinventoryDis = $inventory->getQty($itemid,$bid,$torack);
				$inventory->addInventory($itemid,$bid,$transferqty,false,$torack);
				// monitoring

				$newqtyDis = $curinventoryDis->qty + $transferqty;
				$labelrem = "";
				$inv_mon->create(array(
					'item_id' => $itemid,
					'rack_id' => $torack,
					'branch_id' => $bid,
					'page' => 'admin/transfer.php',
					'action' => 'Update',
					'prev_qty' => $curinventoryDis->qty,
					'qty_di' => 1,
					'qty' => $transferqty,
					'new_qty' => $newqtyDis,
					'created' => time(),
					'user_id' => $user->data()->id,
					'remarks' => 'Add inventory to '.$torackname.' '.$fromrackname.' '.$addtllabel.'(Transfer id #'.$id.')',
					'is_active' => 1,
					'company_id' => $user->data()->company_id
				));
				if($rackid != 0){
					$curinventoryFrom = $inventory->getQty($itemid,$bid,$rackid);
					$inventory->subtractInventory($itemid,$bid,$transferqty,$rackid);

					// monitoring
					$newqtyFrom = $curinventoryFrom->qty - $transferqty;
					$inv_mon->create(array(
						'item_id' => $itemid,
						'rack_id' => $rackid,
						'branch_id' => $bid,
						'page' => 'admin/transfer.php',
						'action' => 'Update',
						'prev_qty' => $curinventoryFrom->qty,
						'qty_di' => 2,
						'qty' => $transferqty,
						'new_qty' => $newqtyFrom,
						'created' => time(),
						'user_id' => $user->data()->id,
						'remarks' => 'Deduct inventory '.$fromrackname2.' transfer to '.$torackname.' (Transfer id #'.$id.')',
						'is_active' => 1,
						'company_id' => $user->data()->company_id
					));
				}

			} else {
				$inv_mon = new Inventory_monitoring();
				//	echo "INSERT";
				$curinventoryDis = 0;
				$inventory->addInventory($itemid,$bid,$transferqty,true,$torack);
				//monitoring
				$newqtyDis = $curinventoryDis + $transferqty;
				$inv_mon->create(array(
					'item_id' => $itemid,
					'rack_id' => $torack,
					'branch_id' => $bid,
					'page' => 'admin/transfer.php',
					'action' => 'Insert',
					'prev_qty' => $curinventoryDis,
					'qty_di' => 1,
					'qty' => $transferqty,
					'new_qty' => $newqtyDis,
					'created' => time(),
					'user_id' => $user->data()->id,
					'remarks' => 'Add inventory to '.$torackname.' '.$fromrackname.' (Transfer id #'.$id.')',
					'is_active' => 1,
					'company_id' => $user->data()->company_id
				));

				if($rackid != 0){
					$curinventoryFrom = $inventory->getQty($itemid,$bid,$rackid);
					$inventory->subtractInventory($itemid,$bid,$transferqty,$rackid);
					// monitoring
					$newqtyFrom = $curinventoryFrom->qty - $transferqty;
					$inv_mon->create(array(
						'item_id' => $itemid,
						'rack_id' => $rackid,
						'branch_id' => $bid,
						'page' => 'admin/transfer.php',
						'action' => 'Insert',
						'prev_qty' => $curinventoryFrom->qty,
						'qty_di' => 2,
						'qty' => $transferqty,
						'new_qty' => $newqtyFrom,
						'created' => time(),
						'user_id' => $user->data()->id,
						'remarks' => 'Deduct inventory '.$fromrackname2.' transfer to '.$torackname.' (Transfer id #'.$id.')',
						'is_active' => 1,
						'company_id' => $user->data()->company_id
					));
				}
			}
		}

		$transfer_mon->update(array(
			'status' => 2
		),$id);

		echo '1';
	}

	function getTransferDetails(){
		$id = Input::get('id');
		$from= Input::get('from');
		$rack = new Rack();
		$user = new User();
		$tcls = new Transfer_inventory_mon($id);
		// tags
		$racktags = new Rack_tag();
		$my_tags = $racktags->get_my_tags($user->data()->id);
		$tagcat = "";
		$tags_arr = [];
		$tagname = "";

		if($user->hasPermission('inventory_all_rack')) $my_tags= [];

		if($my_tags){

			foreach($my_tags as $m){
				$tagname .= $m->tag_name . ", ";
				$tags_arr[] =  $m->id;
				$tagcat .= $m->id . ",";
			}

			$tagcat = rtrim($tagcat,",");
			$tagname= rtrim($tagname,", ");

		}
		$racks = $rack->rackJSON($user->data()->company_id,$tcls->data()->branch_id,'',$tagcat);

		$inv_mon = new Transfer_inventory_details();
		$details = $inv_mon->getDetails($id);
		$hasdisplay = false;
		$hasotherrack = false;
		$needracking = ($tcls->data()->branch_from == $user->data()->branch_id && $tcls->data()->get_stock == 0) ? true : false;
		 if($tcls->data()->branch_id == $user->data()->branch_id || $user->hasPermission('inventory_all')){
			 $viewtype = 1;
		 } else if ($tcls->data()->branch_from == $user->data()->branch_id){
			 $viewtype = 2;
		 } else {
			 $viewtype = 1;
		 }
		$in_tags = false;
		if($details){
				$branch_details = new Branch($tcls->data()->branch_id);
				if($viewtype ==1){  // sayo dadalhin
					if($user->hasPermission('inventory_ref_number')){
						echo "<div class='row'><div class='col-md-4'><input type='text' placeholder='Entry Ref Number' class='form-control' id='ref_number' value='".$tcls->data()->remarks."'></div><div class='col-md-4'><button data-id='$id' class='btn btn-default' id='btnSaveRef'>Save Ref #</button></div></div> <br>";
					}
					echo "<div id='no-more-tables'>";
					echo "<table class='table' id='tblTransfer' data-tid='$id' >";
					echo "<thead>";
					echo "<tr><th>Item Name</th><th>Rack From</th><th>Rack To</th><th>Qty</th></tr>";
					echo "</thead>";
					echo "<tbody>";
					foreach($details as $d){
						$stock_man_to='';
						$stock_man_from='';
						if($d->rack_id_from){
							$rackFrom = new Rack($d->rack_id_from);
							$stock_man_from = $rackFrom->data()->stock_man;
							$stock_man_from = str_replace(["'",'"'],"",$stock_man_from);
						}

						if($d->rack_id_to){
							$rackTo = new Rack($d->rack_id_to);
							$stock_man_to = $rackTo->data()->stock_man;
							$stock_man_to = str_replace(["'",'"'],"",$stock_man_to);
						}


						echo "<tr  data-stock_man_from='".$stock_man_from."' data-stock_man_to='".$stock_man_to."' data-item_id='$d->itemid' data-to='$d->rack_id_to' data-from='$d->rack_id_from' data-qty='$d->qty'><td data-title='Item code'>".$d->item_code."<br><small class='text-danger'>".$d->description."</small></td><td  data-title='Rack'>";
						//$rack_desc = ($d->description) ? " (".$d->description.")" : "";
						echo ($d->rack_id_from) ? $rackFrom->data()->rack  : "No rack";
						echo "</td><td  data-title='Rack To'>";
						$rackname = '';

						if($tcls->data()->status == 1){

							echo "<select class='form-control torack'>";
							echo "<option value=''></option>";
							if($racks){
								foreach($racks as $r){
									if($tags_arr && $d->rack_tag){
										if(in_array($d->rack_tag,$tags_arr)){
											$in_tags = true;
										}
									} else {
										$in_tags = true;
									}

									if($r->id == $d->rack_id_from) continue;
									if($r->id == $d->rack_id_to){
										$selected='selected';
										if($r->rack == 'Display') $hasdisplay = true;
										if($r->rack != 'Display') $hasotherrack = true;
									} else {
										$selected ='';
									}
									echo "<option value='$r->id' $selected>$r->rack</option>";
								}
							} else {
								$in_tags = true;
							}

							echo "</select>";
						} else if($tcls->data()->status == 2){
							if($d->rack_id_to){
								$nrack = new Rack($d->rack_id_to);
								echo $nrack->data()->rack;
							} else {
								echo "No Rack";
							}
						}


						echo "</td><td  data-title='Quantity'>".formatQuantity($d->qty)."</td></tr>";
					}
					echo "</tbody>";
					echo "</table>";
					echo "</div>";
					echo "<hr>";
					$lblwarning = "";
					if(($user->data()->branch_id == $tcls->data()->branch_id && $tcls->data()->status == 1) || $user->hasPermission('inventory_all')){
						$view = true;
						if(!$in_tags){
							$view = false;
						}
						if($hasdisplay && !$user->hasPermission('rack_display')){
							$view = false;
						}
						if($hasotherrack && !$user->hasPermission('rack_other')){
							$view = false;
						}
						if($tcls->data()->from_where =='From Order' && ($tcls->data()->get_stock != 1 || $tcls->data()->del_schedule == 0)){
							$lblwarning = "<p class='text-muted'>Order still pending in logistics</p>";
							$view = false;
						}
						$now = time();
						if($tcls->data()->from_where =='From Order' && $tcls->data()->del_schedule && $tcls->data()->del_schedule > $now ){
							//$lblwarning = "<p class='text-muted'>Schedule date is on ".date('m/d/Y', $tcls->data()->del_schedule)."</p>";
							//$view = false;
						}

						if($view){

							$print_html='';
							if($tcls->data()->from_where =='From transfer' || $tcls->data()->from_where =='From Service Liquidation'){
								$print_html = "<button style='margin-left:3px'  data-branch_name='".$branch_details->data()->name."' data-remarks='".$tcls->data()->remarks . "' data-transfer_id='".$tcls->data()->id . "' class='btn btn-default print_rack_transfer' title='Print'><span class='glyphicon glyphicon-print'></span> <span class='hidden-xs'>Print</span></button>";
							}
							echo "<div class='text-right'><input type='button' data-from='$from' class='btn btn-default' value='Cancel' id='btnCancelTransfer'/> $print_html <input type='button' data-from='$from' class='btn btn-default' value='Transfer' id='btnTransfer'/></div>";

						} else {
							echo "<p class='text-muted'>You will not be able to receive it. You are only allowed to receive items to <span class='text-danger'>$tagname</span> tag.</p>";
							echo $lblwarning;
						}
					}

				} else if ($viewtype == 2){ // sayo kukunin

					if($user->hasPermission('inventory_ref_number')){
						echo "<div class='row'><div class='col-md-4'><input type='text' placeholder='Entry Ref Number' class='form-control' id='ref_number' value='".$tcls->data()->remarks."'></div><div class='col-md-4'><button data-id='$id' class='btn btn-default' id='btnSaveRef'>Save Ref #</button></div></div> <br>";
					}

					if($needracking || $tcls->data()->del_schedule == 0){ //getstock = 0
						echo "<table class='table table-bordered'>";
						echo "<thead>";
						echo "<tr><th>Item</th><th>Description</th><th>Qty</th><th>Racking</th></tr>";
						echo "</thead>";
						echo "<tbody>";
						$hasins = false;
						foreach($details as $d){
							$racking = inventory_racking(0,$d->qty,$d->itemid,$user->data()->branch_id,false);
							$racks = json_decode($racking['racking'],true);
							$is_false = $racking['insufficient'];
							if($is_false){
								$hasins = true;
							}
							$retracking = "<table class='table'>";
							$retracking .= "<tr><th>Rack</th><th>Stock Man</th><th>Qty</th></tr>";
							foreach($racks as $r){
								$stock_man = (isset($r['stock_man']) && !empty($r['stock_man'])) ? $r['stock_man'] : 'None';
								$rdes = (isset($r['rack_description']) && !empty($r['rack_description'])) ?$r['rack_description'] : '';
								$retracking .= "<tr><td>$r[rack]<small class='span-block'>$rdes</small></td><td>$stock_man</td><td>" . formatQuantity($r['qty']) . "</td></tr>";
							}
							$retracking .= "</table>";


						echo "<tr><td>$d->item_code</td><td>$d->description</td><td>" . formatQuantity($d->qty) . "</td><td>$retracking </td></tr>";
						}
						echo "</tbody>";
						echo "</table>";
						echo "<hr>";
						echo "<div class='text-right'>";
						if(!$hasins && $needracking){
							echo "<button data-id='".Encryption::encrypt_decrypt('encrypt',$id)."' class='btn btn-default' id='btnGetStocks'>Get Stocks</button>";
						}
						if($tcls->data()->del_schedule == 0 && !$needracking){
							$drivercls = new Driver();
							$helperscls = new Delivery_helper();
							$truckcls = new Truck();
							$drivers = $drivercls->get_active('drivers',['company_id','=',$user->data()->company_id]);
							$helpers = $helperscls->get_active('delivery_helpers',['company_id','=',$user->data()->company_id]);
							$trucks = $truckcls->get_active('trucks',['company_id','=',$user->data()->company_id]);

							?>
							<input type="hidden" id='init_sched_el' value='1'>
							<div class="row text-left">
								<div class="col-md-3">
									<div class="form-group">
										<input id='dt_sched' type="text" class='form-control' placeholder='Date'>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<select name="truck_id" id="truck_id" class='form-control'>
											<option value=""></option>
											<?php foreach($trucks as $truck):
												?>
												<option value="<?php echo $truck->id; ?>"><?php echo $truck->name; ?></option>
												<?php
											endforeach
											?>
										</select>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<select name="s_driver" id="s_driver" class='form-control'>
											<option value=""></option>
											<?php foreach($drivers as $driver):
												    ?>
												<option value="<?php echo $driver->name; ?>"><?php echo $driver->name; ?></option>
													<?php
												  endforeach
											?>
										</select>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<select name="s_helper" id="s_helper" class='form-control' multiple>
											<option value=""></option>
											<?php foreach($helpers as $helper):
												?>
												<option value="<?php echo $helper->name; ?>"><?php echo $helper->name; ?></option>
												<?php
											endforeach
											?>
										</select>
									</div>
								</div>

							</div>
							<?php
								  echo "<button data-id='".Encryption::encrypt_decrypt('encrypt',$id)."' class='btn btn-default' id='btnAddSchedule'>Add Schedule</button>";
						}


						echo "</div>";
					} else { // already deduct stock
						if($user->hasPermission('inventory_ref_number')){
							echo "<div class='row'><div class='col-md-4'><input type='text' placeholder='Entry Ref Number' class='form-control' id='ref_number' value='".$tcls->data()->remarks."'></div><div class='col-md-4'><button data-id='$id' class='btn btn-default' id='btnSaveRef'>Save Ref #</button></div></div> <br>";
						}
						echo "<table class='table table-bordered'>";
						echo "<thead>";
						echo "<tr><th>Item</th><th>Description</th><th>Qty</th><th>Racking</th></tr>";
						echo "</thead>";
						echo "<tbody>";

						foreach($details as $d){

							$racks = json_decode($d->racking,true);
							$retracking = "<table class='table'>";
							$retracking .= "<tr><th>Rack</th><th>Stock Man</th><th>Qty</th></tr>";
							foreach($racks as $r){

								$stock_man = ($r['stock_man']) ? $r['stock_man'] : 'None';
								$retracking .= "<tr><td>$r[rack]<small class='span-block'>$r[rack_description]</small></td><td>$stock_man</td><td>" . formatQuantity($r['qty']) . "</td></tr>";
							}
							$retracking .= "</table>";
							echo "<tr><td>$d->item_code</td><td>$d->description</td><td>" . formatQuantity($d->qty) . "</td><td>$retracking </td></tr>";
						}
						echo "</tbody>";
						echo "</table>";
						echo "<p class='text-muted'>Waiting for the destination branch to receive the item</p>";
					}
				}
			}

	}
	function updateDelSchedTransfer(){
		$id = Encryption::encrypt_decrypt('decrypt', Input::get('id'));
		if(is_numeric($id)){
			$dt = strtotime(Input::get('dt_sched'));
			$driver = Input::get('driver');
			$helper = json_decode(Input::get('helper'));
			$truck_id = Input::get('truck_id');
			$tcls = new Transfer_inventory_mon();
			$helper = implode('|',$helper);
			if($dt){
				$tcls->update(array(
					'driver' => $driver,
					'truck_id' => $truck_id,
					'helpers' => $helper,
					'del_schedule' => $dt
				),$id);
				echo "Order updated successfully";
			}

		}
	}
	function deductStockFromTransfer(){
		$id = Encryption::encrypt_decrypt('decrypt', Input::get('id'));
		if(is_numeric($id)){
			$user = new User();
			$tcls = new Transfer_inventory_mon($id);
			$inv_mon = new Transfer_inventory_details();
			$details = $inv_mon->getDetails($id);
			$hasins = false;
			foreach($details as $d) {
				$racking = inventory_racking(0, $d->qty, $d->itemid, $user->data()->branch_id, false);
				$is_false = $racking['insufficient'];
				if($is_false) {
					$hasins = true;
				}
			}
			if(!$hasins){
				$inventory = new Inventory();
				$inv_mon = new Inventory_monitoring();

				foreach($details as $d) {
					$racking = inventory_racking(0, $d->qty, $d->itemid, $user->data()->branch_id, false);
					$racks = json_decode($racking['racking'],true);
					$branch_id = $user->data()->branch_id;
					$item_id =  $d->itemid;
					$detcls = new Transfer_inventory_details();
					$detcls->update(['racking' => $racking['racking']],$d->id);

					foreach($racks as $r){
						$qty = $r['qty'];
						$rack_id = $r['rack_id'];
						// check if item exists in rack
						if($inventory->checkIfItemExist($item_id,$branch_id,$user->data()->company_id,$rack_id)){
							$curinventoryFrom = $inventory->getQty($item_id,$branch_id,$rack_id);
							$currentqty = $curinventoryFrom->qty;
							$inventory->subtractInventory($item_id,$branch_id,$qty,$rack_id);
						} else {
							$currentqty = 0;
						}
						// monitoring
						$newqtyFrom = $currentqty - $qty;
						$inv_mon->create(array(
							'item_id' => $item_id,
							'rack_id' => $rack_id,
							'branch_id' => $branch_id,
							'page' => 'ajax/ajax_query2.php',
							'action' => 'Update',
							'prev_qty' => $currentqty,
							'qty_di' => 2,
							'qty' => $qty,
							'new_qty' => $newqtyFrom,
							'created' => time(),
							'user_id' => $user->data()->id,
							'remarks' => 'Deduct inventory from rack (Transfer id #'.$id.')',
							'is_active' => 1,
							'company_id' => $user->data()->company_id
						));
					}
				}
				$tcls->update(['get_stock'=> 1],$id);
				echo "Processed successfully.";
			}
		}
	}
	function inventory_racking($order_id=0,$qty=0,$item_id = 0,$branch_id=0,$deduct_prev = false){
		$inv = new Inventory();
		$inv_racks = $inv->get_racking($item_id,$branch_id);
		$qty_racks = [];
		$insufficient = false;
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
	function cancelTransferMon(){
		$id = Input::get('id');
		$tcls = new Transfer_inventory_mon();
		$tcls->update(array('status'=>3),$id);
		echo 1;
	}
	function topCashier(){
		$dt1 = Input::get('dt1');
		$dt2 = Input::get('dt2');
		$type = Input::get('type');
		$gsales = new Sales();
		$user = new User();
		// base on branch
		$branchsales = $gsales->getTotalSalesPerCashier($user->data()->company_id,$dt1,$dt2);

		$arr = [];

		if($branchsales){
			$tablestring = "<div class='list-group'>";
			$tablestring .= " <a href='#' class='list-group-item active'></a>";

			foreach($branchsales as $bb){
				$obj['label'] = ucwords($bb->lastname . ', ' . $bb->firstname . ' ' . $bb->middlename) ;
				$obj['value']= number_format($bb->saletotal,2,'.','');
				array_push($arr,$obj);
				$tablestring .="<a href='#' class='list-group-item'>".ucwords($bb->lastname . ', ' . $bb->firstname . ' ' . $bb->middlename)."<span class='pull-right text-danger'>".number_format($bb->saletotal,2)."</span></a>";
			}
			$tablestring .="</div>";
		}
		if ($type == 1){
			if($arr){
				echo json_encode($arr);
			} else {
				echo json_encode(array('error' => true));
			}
		} else if ($type==2){

			if(isset($tablestring)){
				echo $tablestring;
			}
		}
	}
	function topItemsSales(){
		// base on item
		$dt1 = Input::get('dt1');
		$dt2 = Input::get('dt2');
		$gsales = new Sales();
		$user = new User();
		$type = Input::get('type');

		$itemsales = $gsales->getTotalSalesBaseOnItem($user->data()->company_id,$dt1,$dt2);
		$arr = [];
		if($itemsales){
			$tablestring = "<div class='list-group'>";
			$tablestring .= " <a href='#' class='list-group-item active'></a>";

			foreach($itemsales as $bb){
			$obj['y'] =  $bb->item_code;
			$obj['a'] = $bb->saletotal;
			array_push($arr,$obj);
				$tablestring .="<a href='#' class='list-group-item'>".$bb->item_code."<br><small>".$bb->description."</small><span style='' class='text-danger pull-right'>".number_format($bb->saletotal,2)."</span></a>";

			}
			$tablestring .= "</div>";
		}
		if ($type == 1){
			if($arr){
				echo json_encode($arr);
			} else {
				echo json_encode(array('error' => true));
			}
		} else if ($type==2){
			if(isset($tablestring)){
				echo $tablestring;
			}
		}
	}
	function topItemsQty(){
		$dt1 = Input::get('dt1');
		$dt2 = Input::get('dt2');
		$type = Input::get('type');
		$gsales = new Sales();
		$user = new User();
		$itemsalesqty = $gsales->getTotalSalesBaseOnItemQty($user->data()->company_id,$dt1,$dt2);
		$arr = [];
		if($itemsalesqty){
			$tablestring = "<div class='list-group'>";
			$tablestring .= " <a href='#' class='list-group-item active'></a>";
			foreach($itemsalesqty as $bb){

				$obj['y'] =  $bb->item_code;
				$obj['a']= $bb->qtytotal;
				array_push($arr,$obj);
				$tablestring .="<a href='#' class='list-group-item'>".$bb->item_code."<br><small>".$bb->description."</small><span class='pull-right text-danger'>".$bb->qtytotal."</span></a>";
			}
		}

		if ($type == 1){
			if($arr){
				echo json_encode($arr);
			} else {
				echo json_encode(array('error' => true));
			}
		} else if ($type==2){
			if(isset($tablestring)){
				echo $tablestring;
			}
		}
	}

	function getPast10(){
		$branch = Input::get('branch');
		$sales = new Sales();
		for($i=0;$i>-10;$i--){
			$monthStart = strtotime(date('m/d/Y') . "$i day" );
			$temp = $i + 1;
			$monthEnd = strtotime(date('m/d/Y').  "$temp day -1 sec");
			$msale = $sales->getSalesBranch($branch,$monthStart,$monthEnd);
			$msale = ($msale->saletotal) ? $msale->saletotal : 0;
			$arrMon[] = date('M d Y',$monthStart);
			$arrTotal[] =$msale;
		}
		$arr = [];
		for($i = 0; $i<10; $i++){
			$obj['y'] = $arrMon[$i];
			$obj['a'] = (int) $arrTotal[$i];
			array_push($arr,$obj);
		}
		echo json_encode($arr);
	}
	function updateCaravanOrderQty(){
		$id = Input::get('id');
		$qty = Input::get('qty');
		$reqid = Input::get('reqid');
		$agentreq = new Agent_request($reqid);
		// counter check
		if($agentreq->data()->status == 1 ||  $agentreq->data()->status == 6){ // for approval and for releasing
			$req = new Agent_request_details();
			$req->update(array(
				'qty' => $qty
			),$id);
		}

	}

	function searchItem(){
		$string = Input::get('search');
		$company = Input::get('company');
		$prod = new Product();
		$res = $prod->searchItem($company,$string);

		if($res){
			foreach($res as $s){
				//Return each page title seperated by a newline.
				echo $s->id . "|" .$s->description . "\n";
			}
		}
	}
	function searchItemJSON(){
		$string = Input::get('search');
		$user = new User();
		$company =$user->data()->company_id;
		$prod = new Product();
		$is_franchisee = false;
		if($user->hasPermission('is_franchisee')){
			$is_franchisee = true;
		}
		$res = $prod->searchItem($company,$string,$is_franchisee);

		if($res){
			$arr= [];
			foreach($res as $s){
				$priceObj = new Product();
				$price = $priceObj->getPrice($s->id);
				$s->description = str_replace(':','',$s->description);
				$s->price = (isset($price->price)) ? $price->price : 0;
				$s->unit_name = ($s->unit_name) ? $s->unit_name :'';

				if($s->item_type == 2 || $s->item_type == 3 || $s->item_type == 4 || $s->item_type == 5 ){
					$con = new Consumable();
					$pcon = $con->getConsumableByItemId($s->id);
					$days = $pcon->days;
					$cqty = $pcon->qty;
				} else {
					$days = -1;
					$cqty = -1;
				}
				$s->cdays = $days;
				$s->cqty = $cqty;
				$arr[] = $s;
			}
			echo json_encode($arr);
		}
	}
	function searchSpareJSON(){
		$string = Input::get('search');
		$user = new User();
		$company =$user->data()->company_id;
		$prod = new Product();
		$res = $prod->searchSpare($company,$string);

		if($res){
			$arr= [];
			foreach($res as $s){
				$priceObj = new Product();
				$price = $priceObj->getPrice($s->id);
				$s->price = $price->price;
				$arr[] = $s;
			}
			echo json_encode($arr);
		}
	}
	function orderInventory(){
		$user = new User();
		$rackDisplay = new Rack();
		$dis = $rackDisplay->getRackForSelling($user->data()->branch_id);

		$toorder = json_decode(Input::get('toOrder'));
		$branch_to = Input::get('branch_to');
		$branch_from = Input::get('branch_from');
		$is_sup = Input::get('is_sup');
		$tranfer_mon = new Transfer_inventory_mon();
		if($is_sup == 1){
			$sup = $branch_from;
			$branch_from = 0;
		} else {
			$sup = 0;
		}
		$tranfer_mon->create(array(
			'status' => 1,
			'is_active' =>1,
			'branch_id' =>$branch_to,
			'company_id' =>$user->data()->company_id,
			'created' => time(),
			'modified' => time(),
			'from_where' => 'From Order',
			'branch_from' => $branch_from,
			'supplier_id' => $sup
		));
		$lastid = $tranfer_mon->getInsertedId();
		$err = "";
		$inserted = false;
		foreach($toorder as $t){
			$tranfer_mon_details = new Transfer_inventory_details();
			$newProduct = new Product($t->item_id);
			$unit = new Unit($newProduct->data()->unit_id);
			if(is_decimal($t->qty) &&  $unit->data()->is_decimal==0){
				$err .= "<p>".$newProduct->data()->item_code.", quantity should be a number.</p>";
				continue;
			}
			$inserted = true;
			$tranfer_mon_details->create(array(
				'transfer_inventory_id' => $lastid,
				'rack_id_from' => 0,
				'rack_id_to' => $dis->id,
				'item_id' =>$t->item_id,
				'qty' => $t->qty,
				'is_active' => 1
			));
		}
		if($inserted == false){
			$tranfer_mon->update(array('is_active'=>0),$lastid);
			echo "Failed to order item.". $err;
		} else {
			if($err){
				$err = "except:" . $err;
			}
			echo "All item(s) were ordered successfully". $err;
		}


	}

	function getItemBaseOnInvoice() {
		$user = new User();
		 $inv = Input::get('invoice');
		 $terminal_id = Input::get('terminal_id');
		$s = new Sales();
		$results = $s->getItemBaseOnInvoice($inv, $user->data()->company_id, $terminal_id);
		$payment = $s->getPaymentId($inv);

		$return ='';
		if($results) {

				$return .= "<thead><tr><th>Barcode</th><th>Qty</th><th>Price</th><th>Discount</th><th>Total</th></tr></thead>";
				$return .= "<tbody data-payment_id=".$payment->payment_id.">";
				$grandtotal = 0;
				foreach($results as $r){
					$total = ($r->qtys * $r->price) - $r->discount;
					$return .= "<tr data-item_id='$r->item_id' data-price_id='$r->price_id' data-itemcode_desc='$r->item_code $r->description'><td>$r->item_code<br><small>$r->description</small></td><td>" . number_format($r->qtys)."</td><td>".number_format($r->price,2)."</td><td>".number_format($r->discount,2)."</td><td>".number_format($total,2)."</td></tr>";
					$grandtotal += $total;
				}
				$return .= "</tbody>";
			echo $return;
			echo "<input type='hidden' value='$grandtotal' id='totalPrevSale'/>";
	}
	}
	function retQuery(){

		$user= new User();

		$origjson = json_decode(Input::get('origjson'));
		$retjson = json_decode(Input::get('retjson'));
		$excjson = json_decode(Input::get('excjson'));

		$finaljson = [];
		$finalret = [];

		foreach($origjson as $o){
			$item = [];

			$item_id = $o->item_id;
			$price_id = $o->price_id;
			 $qty = str_replace(',','',$o->qty) ;
			$price = str_replace(',','',$o->price) ;
			$discount = str_replace(',','',$o->discount) ;
			$total =  str_replace(',','',$o->total) ;

			foreach($retjson as $r){
				if($item_id == $r->item_id){
					$r->qty =  str_replace(',','',$o->qty) ;
					 $qty = $qty - $r->qty;
					$r->discount =  str_replace(',','',$o->discount) ;
					 $discount = $discount - $r->discount;
					$r->total =  str_replace(',','',$o->total) ;
					$total = $total - $r->total;
				}
			}
			//
			foreach($excjson as $ex){
				if($item_id == $ex->item_id){
					$ex->qty =  str_replace(',','',$ex->qty) ;

					$qty =  $qty +  $ex->qty;
					$ex->discount =  str_replace(',','',$ex->discount) ;
					$discount = $discount + $ex->discount;
					$ex->total =  str_replace(',','',$ex->total) ;
					$total = $total + $ex->total;
				}
			}
			if($qty){
				$item['item_id'] = $item_id;
				$item['price_id'] = $price_id;
				$item['qty'] = $qty;
				$item['price'] = $price;
				$item['discount'] = $discount;
				$item['total'] =$total;
				array_push($finaljson,$item);
			}
		}
		$finalret = [];
		foreach($retjson as $r){
			$itemret = [];
			$qty=  str_replace(',','',$r->qty) ;
			$discount =  str_replace(',','',$r->discount) ;
			$total =  str_replace(',','',$r->total) ;
			foreach($excjson as $ex){
				if($ex->item_id == $r->item_id){

					$ex->qty =  str_replace(',','',$ex->qty) ;
					$qty  =$qty -$ex->qty;

					$ex->discount =  str_replace(',','',$ex->discount) ;
					$discount = $discount -$ex->discount;

					$ex->total =  str_replace(',','',$ex->total) ;
					$total= $total - $ex->discount;
				}
			}
			if($qty > 0){
				$itemret['item_id'] = $r->item_id;
				$itemret['qty'] = $qty;
				$itemret['price_id'] = $r->price_id;
				$itemret['price'] = $r->price;
				$itemret['total'] = $total;
				$itemret['discount'] =$discount;
				array_push($finalret,$itemret);
			}
		}

		$finalexc = [];
		foreach($excjson as $ex){
			$itemex = [];

			$qty = str_replace(',','',$ex->qty) ;
			$discount =  str_replace(',','',$ex->discount) ;
			$total =  str_replace(',','',$ex->total) ;
			foreach($retjson as $r){
				if($ex->item_id == $r->item_id){
					$r->qty =  str_replace(',','',$r->qty) ;

					$qty = $qty - $r->qty;
					$r->discount =  str_replace(',','',$r->discount) ;
					$discount = $discount - $r->discount;
					$r->total =  str_replace(',','',$r->total) ;
					$total =$total -  $r->total;
				}
			}
			if($qty > 0){
				$itemex['item_id'] = $ex->item_id;
				$itemex['qty'] = $qty;
				$itemex['price_id'] = $ex->price_id;
				$itemex['price'] = $ex->price;
				$itemex['total'] = $total;
				$itemex['discount'] = $discount;
				array_push($finalexc,$itemex);
				$find = false;
				foreach($finaljson as $f){
					if($f['item_id'] == $ex->item_id){
						$find = true;
						break;
					}
				}
				if(!$find){
					array_push($finaljson,$itemex);
				}
			}
		}

		dump($finaljson);
		dump($finalret);
		dump($finalexc);

		// check if the is stock
		if($finalexc){
			foreach($finalexc as $fe){

			}
		}

		// update old sale to returned
		// add new sale
		// add return stock
		// deduct exchange stock
		// alert complete


		//dump($origjson);
		//dump($retjson);
		//dump($excjson);

	}

	function saveBarcode(){
		$user = new User();
		$f = Input::get('fid');
		$has_own_layout = Input::get('has_own_layout');
		$has_own_layout_user = Input::get('has_own_layout_user');
		$jsonstring = Input::get('styles');
		$user_id = Input::get('user_id');
		$barcodegen = new Barcode();

		if($has_own_layout_user && $user_id == 2){
			Log::addLog($user->data()->id,$user->data()->company_id,"Update form layout $f ".$user->data()->branch_id,'admin/ajax_query.php?f=saveBarcode');
			$barcodegen->updateStyle($f,$jsonstring,$user->data()->company_id,0,$user->data()->id);
		} else if (!$has_own_layout_user && $user_id == 2){
			Log::addLog($user->data()->id,$user->data()->company_id,"Update form layout $f Create for user ",'admin/ajax_query.php?f=saveBarcode');
			$barcodegen->create(
				['family' => $f,'styling'=> $jsonstring, 'company_id' => $user->data()->company_id,'branch_id' => 0,'user_id' => $user->data()->id]
			);

		}else if($has_own_layout){
			Log::addLog($user->data()->id,$user->data()->company_id,"Update barcode layout $f ".$user->data()->branch_id,'admin/ajax_query.php?f=saveBarcode');
			$barcodegen->updateStyle($f,$jsonstring,$user->data()->company_id,$user->data()->branch_id,0);
		} else {
			Log::addLog($user->data()->id,$user->data()->company_id,"Update barcode layout $f main",'admin/ajax_query.php?f=saveBarcode');
			$barcodegen->updateStyle($f,$jsonstring,$user->data()->company_id,0,0);

		}

		echo "Updated successfully";
	}
	function getInvoiceFormat(){
		$c = Input::get('cid');
		$barcodeClass = new Barcode();
		$barcode_format = $barcodeClass->get_invoice_format($c);
		echo $barcode_format->styling;
	}
	function getDocumentLayout(){
		$c = Input::get('cid');
		$barcodeClass = new Barcode();
		$user = new User();
		$allDocFormat = $barcodeClass->getFormats($c);
		$docFormatArr = [];
		foreach($allDocFormat as $doc_format){
			$doc_format->family = strtolower($doc_format->family);
			$docFormatArr[$doc_format->family] = $doc_format->styling;
		}

		$byBranch = $barcodeClass->getFormatsByBranch($user->data()->branch_id);
		if($byBranch){

			foreach($byBranch as $newlayout){
				$newlayout->family = strtolower($newlayout->family);
				unset($docFormatArr[$newlayout->family]);
				$docFormatArr[$newlayout->family] = $newlayout->styling;
			}
		}
	/*	$barcode_format = $barcodeClass->get_invoice_format($c);
		$invoiceStyle = $barcode_format->styling;

		$dr_format = $barcodeClass->get_dr_format($c);
		$drStyle =  $dr_format->styling;

		$ir_format = $barcodeClass->get_ir_format($c);
		$irStyle =  $ir_format->styling;

		$invoiceStyle =($invoiceStyle) ? $invoiceStyle : '';
		$drStyle =($drStyle) ? $drStyle : '';
		$irStyle =($irStyle) ? $irStyle : '';
*/
	//	$format = array('invoice' => $invoiceStyle, 'dr'=>$drStyle, 'ir'=>$irStyle);
		echo json_encode($docFormatArr);
	}
	function getSuppliersItem(){
		$user = new User();
		$c = $user->data()->company_id;
		$sup_id = Input::get('sup_id');
		$sup_item = new Supplier_item();
		$sup_items = $sup_item->getitemssup($c,$sup_id);
		if($sup_items){
				foreach($sup_items as $si){
				?>
					<option data-is_decimal=<?php echo escape($si->is_decimal); ?> data-item_code="<?php echo escape($si->item_code); ?>" data-min_qty="<?php echo escape($si->min_qty); ?>" data-item_id="<?php echo escape($si->item_id); ?>" data-purchase_price="<?php echo escape($si->purchase_price); ?>" value="<?php echo $si->id; ?>"><?php echo $si->description . ":{$si->ic}:{$si->des}"; ?></option>
				<?php
				}
			?>
			<option value="-1"><?php echo "New Item Supplier" ?></option>
			<?php
		} else {
			?>
			<option value="-1"><?php echo "New Item Supplier" ?></option>
			<?php
		}
	}
	function getDrFormat(){
		$c = Input::get('cid');
		$barcodeClass = new Barcode();
		$barcode_format = $barcodeClass->get_dr_format($c);
		echo $barcode_format->styling;
	}
	function getIrFormat(){
		$c = Input::get('cid');
		$barcodeClass = new Barcode();
		$barcode_format = $barcodeClass->get_ir_format($c);
		echo $barcode_format->styling;
	}


	function orderToSupplier(){

		$supplier_id = Input::get('supplier_id');
		$branch_to = Input::get('branch_to');
		$is_rush = Input::get('is_rush');
		$ship_to = Input::get('ship_to');
		$terms = Input::get('terms');
		$delivery_date = Input::get('delivery_date');
		$remarks = Input::get('remarks');

		$toOrder = Input::get('toOrder');
		$toOrder = json_decode($toOrder,true);
		$user = new User();
		$sup_order = new Supplier_order();
		$od = new Supplier_order_details();
		$si = new Supplier_item();
		$company_id = $user->data()->company_id;
		$now = time();
		$timelog = [];

		$msg = "Requested by "  . ucwords($user->data()->firstname .  " " . $user->data()->lastname);

		$timelog[] = ['time' => $now,'message' => $msg];

		$is_rush = ($is_rush) ? $is_rush : 0;

		$sup_order->create(array(
			'created' => $now,
			'modified' => $now,
			'company_id' => $company_id,
			'is_active' => 1,
			'status' => 0,
			'is_rush' => $is_rush,
			'user_id' => $user->data()->id,
			'supplier_id' => $supplier_id,
			'branch_to' => $branch_to,
			'ship_to' => $ship_to,
			'terms' => $terms,
			'expected_delivery_date' => $delivery_date,
			'remarks' => $remarks,
		));

		$lastorderid = $sup_order->getInsertedId();
		$html = "<h4>Rush Order Notification</h4>";
		$html .= "<p>Order Number: $lastorderid</p>";
		$html .= "<p>$msg</p>";
		$html .= "<p>Date: ".date('m/d/Y')."</p>";

		$total_item_cost = 0;

		foreach($toOrder as $o){
			if ($o['is_new'] == 1){
				$si->create(array(
					'supplier_id' =>$supplier_id,
					'item_code' => $o['item_code'],
					'description' => $o['description'],
					'min_qty' => $o['min_qty'],
					'item_id' => 0,
					'created' => $now,
					'modified' => $now,
					'company_id' => $company_id,
					'purchase_price' => $o['purchase_price']
				));
				$lastidsupitem = $si->getInsertedId();
			} else {
				$lastidsupitem = $o['supplier_item_id'];
			}


			$total_item_cost += ($o['purchase_price'] * $o['qty']);
			$od->create(array(
				'supplier_item_id' => $lastidsupitem,
				'qty' => $o['qty'],
				'cost_price' => $o['purchase_price'],
				'created' => $now,
				'modified' => $now,
				'company_id' => $company_id,
				'supplier_order_id' => $lastorderid,
				'is_active' => 1,
				'get_qty' => 0
			));

			if($is_rush == 1){
				$prod = new Product($o['supplier_item_id']);
				if(isset($prod->data()->item_code)){
					$html .= "<p>".$prod->data()->item_code." : ".$o['qty']."</p>";
				}
			}

			$sup_order->update(['purchase_price_manual' => $total_item_cost],$lastorderid);

		}

		if(Configuration::allowedPermission('email_rush')){

			$res_mail  = sendMail(
				"zol_cebuhiq@yahoo.com",
				"Rush Order",
				[Configuration::allowedPermission('email_rush')],
				"This request needs your attention",
				$html,
				"",
				""
			);

		}

		echo "Order was places successfully";

	}


	function getSupplierOrdersDetails(){
		$supplier_order_id = Input::get('supplier_order_id');
		$branchname = Input::get('branch_name');
		$branchaddress = Input::get('branch_address');
		$status = Input::get('status');

		$od = new Supplier_order($supplier_order_id);
		$details = $od->getOrderDetails($supplier_order_id,$od->data()->branch_to);
		$user = new User();

		$cf = new Custom_field();
		$cfd = new Custom_field_details();
		$getsupplierdet = $cf->getcustomform('suppliers',$user->data()->company_id);

		$otherfield = isset($getsupplierdet->other_field)?$getsupplierdet->other_field:'';
		if($otherfield){
			$otherfield = json_decode($otherfield,true);
		}
		$supplier = new Supplier($od->data()->supplier_id);

		if($details){
			$prod =  new Product();
			$nosupitem = $prod->getItemWithoutSupplier($od->data()->supplier_id,$user->data()->company_id);
			$selectitem = "<select class='form-control no_item_sup'>";
			$selectitem .= "<option value=''></option>";
			if($nosupitem){
				foreach($nosupitem as $ni){
					$selectitem .= "<option data-description='' value='".$ni->id."' >" .$ni->barcode. ":" . $ni->item_code. ":" . $ni->description."</option>";
				}
			}
			$selectitem .= "<option value='-1'>New Item</option>";
			$selectitem .= "</select>";
			echo "<div id='no-more-tables'>";
			echo "<div id='printtblorder'>";

			/*
				if($otherfield){
					foreach($otherfield as $cfield){
						if($cfield['field-visibility'] == 1){
								$jsonind = json_decode($s->jsonfield,true);
							?>
							<span style='display:block;'><?php echo  "<span style='color:#888;' class=''>" . $cfield['field-label'] . ":</span> <span class='text-danger'>" . $jsonind[$cfield['field-id']] . "</span>"; ?></span>
							<?php
						}
					}
				}

			*/


			$html_tbl = "";
			$html_tbl .= "<table data-supplier_description='".$supplier->data()->address."' data-supplier_name='".$supplier->data()->name."' data-order_id='$supplier_order_id' id='tblReceive' class='table' data-branch='$branchname' data-branch_address='$branchaddress'>";
			$html_tbl .= "<thead>";
			$html_tbl .= "<tr >";
			$html_tbl .= "<th>Supplier Item</th><th>Item</th><th>Current Stock</th><th>Order Qty</th><th>CBM</th><th>Total CBM</th>";
			$html_tbl .= "<th>Cost Price</th>";
			if($status == 1 && $user->hasPermission('supplier_order_rec')){
				$html_tbl .= "<th>Received Qty</th><th>Pending Qty</th><th>Qty to receive</th><th>Mark as Done</th>";
			}

			$html_tbl .= "</tr>";
			$html_tbl .= "</thead>";
			$html_tbl .= "<tbody>";

			$cost_price = 0;

			foreach( $details as $dt ) {

				$cost_price += ($dt->cost_price * $dt->qty);

				if($dt->item_code && $dt->description){
					$ouritem = "<br>".$dt->item_code."<br><small class='text-danger'>".$dt->description."</small>";
					$noitem = '';
				} else {
					$ouritem = "$selectitem <br><span class='text-danger'>Item not assigned yet.</span>";
					$noitem = '1';
				}
				$stock=$dt->stock ? formatQuantity($dt->stock): 0;
				$img = "../css/img/no-thumb.jpg";
				if(file_exists('../item_images/'.$dt->s_item_id.".jpg")){
					$img = '../item_images/'.$dt->s_item_id.".jpg";
				}
				$cbm = $dt->cbm_l * $dt->cbm_w * $dt->cbm_h;
				$total_cbm = $cbm * $dt->qty;
				$supitem = "<div class='row'><div class='col-md-4'><img height=100 width=100 src='$img'></div><div class='col-md-8' style='display:none;'><br>".$dt->s_item_code."<br><small class='text-danger'>".$dt->s_description."</small></div></div>";
				$html_tbl .= "<tr  data-cbm='$cbm' data-total_cbm='$total_cbm' data-item_id='".$dt->s_item_id."' data-details_id='".$dt->id."' id='s_".$dt->supid."' data-no_item='$noitem' data-sup_id='".$dt->supid."' data-s_item_code='".$dt->s_item_code."' data-s_description='".$dt->s_description."' data-s_purchase_price='".$dt->spp."'>";
				$html_tbl .= "<td data-title='Supplier Item' style='border-top:1px solid #ccc;'>$supitem</td>";
				$availableqty = $dt->qty - $dt->get_qty;
				$disabledbtn ='';
				if(!$availableqty || $dt->is_done) $disabledbtn ='disabled';


				$html_tbl .= "<td  data-title='Item' style='border-top:1px solid #ccc;' >" . $ouritem . "</td><td  data-title='Stock' style='border-top:1px solid #ccc;'>$stock</td><td data-title='Qty' style='border-top:1px solid #ccc;'>" . formatQuantity($dt->qty,true) . "</td>";
				$html_tbl .= "<td style='border-top:1px solid #ccc;'>".number_format($cbm,3)."</td>";
				$html_tbl .= "<td style='border-top:1px solid #ccc;'>".number_format($total_cbm,3)."</td>";
				$html_tbl .= "<td style='border-top:1px solid #ccc;'>".number_format(($dt->cost_price * $dt->qty),2)."</td>";

				if($status == 1 && $user->hasPermission('supplier_order_rec')) {
					$html_tbl .= "<td data-title='Received Qty' style='border-top:1px solid #ccc;'>" . $dt->get_qty . "</td><td data-title='Available Qty' style='border-top:1px solid #ccc;'>" . $availableqty . "</td><td data-title='To Receive' style='border-top:1px solid #ccc;'><input $disabledbtn type='text' style='max-width:100px;' class='form-control recqty' ></td>";
					$html_tbl .= "<td style='border-top:1px solid #ccc;'><input type='checkbox' class='chkDone'></td>";
				}

				$html_tbl .= "</tr>";


			}

			$html_tbl .= "</tbody>";
			$html_tbl .= "</table>";

			if($status == 2){ // update details
				echo "<div class='panel panel-default'>";
				echo "<div class='panel-body'>";
				echo "<p><strong>Details</strong></p>";
				echo "<div class='row'>";
				echo "<div class='col-md-3'>";
				echo "<strong>Po Number</strong>";
				echo "<input type='text' id='u_po_number' class='form-control' placeholder='Po Number' value='".$od->data()->po_number."'>";
				echo "</div>";
				echo "<div class='col-md-3'>";
				echo "<strong>Invoice</strong>";
				echo "<input type='text' id='u_invoice' class='form-control' placeholder='Invoice' value='".(($od->data()->invoice) ? $od->data()->invoice : '')."'>";
				echo "</div>";
				echo "<div class='col-md-3'>";
				echo "<strong>DR</strong>";
				echo "<input type='text' id='u_dr' class='form-control' placeholder='DR' value='".(($od->data()->dr) ? $od->data()->dr : '')."'>";
				echo "</div>";
				echo "<div class='col-md-3'>";
				echo "<strong>CR</strong>";
				echo "<input type='text' id='u_cr' class='form-control' placeholder='CR' value='".(($od->data()->cr) ? $od->data()->cr : '')."'>";
				echo "</div>";
				echo "<div class='col-md-3'>";
				echo "<strong>Cost</strong>";
				echo "<input type='text' id='u_cost' class='form-control' placeholder='Cost' value='".(($od->data()->purchase_price_manual != 0.00) ? $od->data()->purchase_price_manual : $cost_price)."'>";
				echo "</div>";
				echo "<div class='col-md-3'>";
				echo "<strong>Terms</strong>";
				echo "<input type='text' id='u_terms' class='form-control' placeholder='Terms' value='".$od->data()->terms."'>";
				echo "</div>";
				echo "<div class='col-md-3'>";
				echo "<strong>Due Date</strong>";
				echo "<input type='text' id='u_due_date' class='form-control' placeholder='Due Date' value='".(($od->data()->due_date) ? date('m/d/Y',$od->data()->due_date) : '')."'>";
				echo "</div>";
				echo "<div class='col-md-3'>";
				echo "<strong>Expected Delivery</strong>";
				echo "<input type='text' id='u_expected_delivery' class='form-control' placeholder='Expected Delivery' value='".(($od->data()->expected_delivery_date) ? date('m/d/Y',$od->data()->expected_delivery_date) : '')."'>";
				echo "</div>";

				echo "<div class='col-md-3'>";
				echo "<br>";
				echo "<button class='btn btn-default' data-id='$supplier_order_id' id='btnUpdateInfo'>Update Details</button>";
				echo "</div>";
				echo "</div>";
				echo "</div>";
				echo "</div>";
			}

			if($status == 1){
				echo "<div class='panel panel-primary'>";
				echo "<div class='panel-heading'>Details</div>";
				echo "<div class='panel-body'>";
				echo "<div class='row'>";
				echo "<div class='col-md-3'>";
				echo "<strong>Date Delivered</strong>";
				echo "<input type='text' id='r_date_delivered' class='form-control' placeholder='Date Delivered' value=''>";
				echo "</div>";
				echo "<div class='col-md-3'>";
				echo "<strong>Remarks</strong>";
				echo "<input type='text' id='r_remarks' class='form-control' placeholder='Delivery Remarks' value=''>";
				echo "</div>";
				echo "<div class='col-md-3'>";
				echo "<strong>Received by: </strong>";
				echo "<input type='text' id='r_received_by' class='form-control' placeholder='Receive By' value=''>";
				echo "</div>";

				echo "<div class='col-md-3'>";
				/*
					echo "<br>";
					echo "<button class='btn btn-default' data-id='$supplier_order_id' id='btnReceiveUpdate'>Update Details</button>";
				*/
				echo "</div>";
				echo "</div>";
				echo "</div>";
				echo "</div>";

				echo "<div class='row'><div class='col-md-6'>";
				echo "<div class='panel panel-primary'>";
				echo "<div class='panel-heading'>Item Status</div>";
				echo "<div class='panel-body'>";
				echo "<select id='receive_status' class='form-control'>";
				echo "<option value='1'>Good</option>";
				echo "<option value='2'>Damage</option>";
				echo "</select>";
				echo "</div>";
				echo "</div>";
				echo "</div></div>";



			}


			echo $html_tbl;

			echo "</div>";
			echo "</div>";

			echo "<div class='row'>";
			echo "<div class='col-md-6'>";
			echo "<input data_list='".json_encode($od->data())."' type='button' value='Print' class='btn btn-default' id='btnPrint'> ";
			if(Configuration::thisCompany('aquabest')){
				echo "<input data_list='".json_encode($od->data())."' type='button' value='Print MRIR' class='btn btn-default' id='btnPrintMRIR'> ";
				echo "<input data_list='".json_encode($od->getSupInfo($user->data()->company_id,$supplier_order_id))."' type='button' value='Print PO Form' class='btn btn-default' id='btnPrintPOForm'> ";

			}
			echo "<input type='button' value='Email' class='btn btn-default' id='btnEmail'>";
			echo "</div>";
			echo "<div class='col-md-6 text-right'>";
			if($status == 1){
				if($user->hasPermission('supplier_order_rec')){
					echo "<input type='button' value='Receive' class='btn btn-primary' id='btnReceive'>";
				}

			} else if ($status == 0 ){
				if($user->hasPermission('supplier_order_app')){
					echo "<input type='button' data-id='$supplier_order_id' value='Approve' class='btn btn-primary' id='btnApproved'>";
					echo " <input type='button' data-id='$supplier_order_id' value='Return' class='btn btn-primary' id='btnReturn'>";
					echo " <input type='button' data-id='$supplier_order_id' value='Decline' class='btn btn-primary' id='btnDecline'>";
				} else {
					echo "<span class='text-danger'> * Pending for approval</span>";
				}

			} else if ($status == 2 ){
				if($user->hasPermission('supplier_order_app')){
					echo "<input type='button' data-id='$supplier_order_id' value='Process' class='btn btn-primary' id='btnProcessed'>";
				}
			}else if ($status == -1){
				echo "<input type='button' data-id='$supplier_order_id' value='Resend' class='btn btn-primary' id='btnResend'>";
			}

			echo "</div>";
			echo "</div>";


		} else {
			echo "Cannot fetched the data.";
		}
	}
	function receiveOrderFromSupplier(){
		$order_id = Input::get('order_id');
		$receive_status = Input::get('receive_status');
		$dt_delivered = Input::get('dt_delivered');
		$rec_remarks = Input::get('rec_remarks');
		$received_by = Input::get('received_by');

		$jsonorder = json_decode(Input::get('torec'),true);
		$prod = new Product();
		$user = new User();
		$prodPrice = new Price();
		$sup_item = new Supplier_item();
		$sup_order = new Supplier_order($order_id);
		$tranfer_mon = new Transfer_inventory_mon();

		$tranfer_mon->create(array(
			'status' => 1,
			'is_active' =>1,
			'branch_id' =>$sup_order->data()->branch_to,
			'company_id' =>$user->data()->company_id,
			'created' => time(),
			'modified' => time(),
			'from_where' => 'From Supplier'
		));
		$lastidsup = $tranfer_mon->getInsertedId();
		$rackDisplay = new Rack();
		//$dis = $rackDisplay->getRackDisplayId($user->data()->company_id);


		foreach($jsonorder as $o){
			if(!$o) continue;
			$noitem = $o['noitem'];
			$item_id = $o['item_id'];
			$details_id = $o['details_id'];
			$recqty = $o['recqty'];
			$product_details = $o['product_details'];
			$sup_item_id = $o['sup_item_id'];
			if($noitem == 1){
				if($item_id == -1){
					$product_details = json_decode($product_details,true);
					// insert product
					$prod->create(array(
						'barcode' => $product_details['barcode'],
						'item_code' => 	$product_details['item_code'],
						'description' => $product_details['description'],
						'category_id' => $product_details['category_id'],
						'company_id' => $user->data()->company_id,
						'is_active' => 1,
						'created' => strtotime(date('Y/m/d H:i:s')),
						'modified' => strtotime(date('Y/m/d H:i:s')),
						'item_type' => -1,
						'product_cost' => $product_details['product_cost'],
						'for_freebies' => 0
					));
					$lastid = $prod->getInsertedId();
					$prodPrice->create(array(
						'price' => $product_details['price'],
						'item_id' => $lastid,
						'unit_id' => Input::get('unit_id'),
						'effectivity' => time(),
						'created' => time()
					));
					$sup_item->update(array(
						'item_id' => $lastid
					),$sup_item_id);
					$item_id = $lastid;
					// get last id
				} else {
					$sup_item->update(array(
						'item_id' => $item_id
					),$sup_item_id);
				}
			}
			$sup_order_details = new Supplier_order_details($details_id);
			$allqtyrec =  $sup_order_details->data()->get_qty + $recqty;

			$arr_update = array(
				'get_qty' => $allqtyrec
			);
			if($o['is_done']){
				$arr_update['is_done'] = $o['is_done'];
			}
			$sup_order_details->update($arr_update,$details_id);

			if($recqty > 0){

				$tranfer_mon_details = new Transfer_inventory_details();
				$tranfer_mon_details->create(array(
					'transfer_inventory_id' => $lastidsup,
					'rack_id_from' => 0,
					'rack_id_to' => 0,
					'item_id' =>$item_id,
					'qty' => $recqty,
					'is_active' => 1
				));

				$reccls = new Supplier_order_receive();
				$statrec = ($receive_status) ? $receive_status : 1;
				$dt = strtotime(date('m/d/Y'));
				$reccls->create(
					[
						'item_id' => $item_id,
						'qty' => $recqty,
						'status' => $statrec,
						'remarks' => $rec_remarks,
						'dt' => strtotime($dt_delivered),
						'supplier_order_id' => $order_id,
						'receive_by' => $received_by,
					]
				);
			}



		}
		$allrec = $sup_order->getItems($order_id);
		$done = true;
		if($allrec){
			$now = time();
			foreach($allrec as $rec){

				if($rec->is_done || $rec->get_qty >=  $rec->qty){

					$item = new Supplier_order_details();
					if(!$rec->is_done){
						$item->update(['is_done' => 1],$rec->id);
					}



					$not_receive_item = new Not_receive_item();
					$checker = $not_receive_item->checkItem($order_id,$rec->supplier_item_id);

					$qty_left =  $rec->qty - $rec->get_qty;

					if(!(isset($checker->id) && $checker->id)){
						if($qty_left > 0){
							$not_receive_item->create(
								[
									'supplier_order_id' => $order_id,
									'supplier_item_id' => $rec->supplier_item_id,
									'qty' => $qty_left,
									'created' => $now,
								]
							);
						}

					}

				}  else {
					$done = false;
				}

			}
		} else {
			$done = false;
		}
		if($done){
			$sup_order->update(array(
				'status' => 4
			),$order_id);
		}
		echo "Item received successfully";
	}

	function getAlternateLabels(){
		$cid = Input::get('cid');
		$cf = new Custom_field();
		$cfd = new Custom_field_details();
		if($cid){
			$getstationdet = $cf->getcustomform('stations',$cid);
			if($getstationdet){
				$stations = isset($getstationdet->label_name) ?$getstationdet->label_name:'Station';
				$labels = '{
						"stations":{"label_name":"'.$stations.'"}
					}';
				echo $labels;
			}
		}
	}

	function updateTerminalAmountOnHand(){
		$type = Input::get('type');
		$payment_type = Input::get('payment_type');
		$amount = Input::get('amount');
		$remarks = Input::get('remarks');
		$terminal_id = Input::get('terminal_id');
		$terminal_mon = new Terminal_mon();
		$user = new User();
		$terminal = new Terminal($terminal_id);
		if($payment_type == 1){
			$col = 't_amount';
			$prev_amount = $terminal->data()->t_amount;
			$prev_amount = ($prev_amount) ? $prev_amount : 0;
		} else if($payment_type == 2){
			$col = 't_amount_cc';
			$prev_amount = $terminal->data()->t_amount_cc;
			$prev_amount = ($prev_amount) ? $prev_amount : 0;
		}else if($payment_type == 3){
			$col = 't_amount_ch';
			$prev_amount = $terminal->data()->t_amount_ch;
			$prev_amount = ($prev_amount) ? $prev_amount : 0;
		}else if($payment_type == 4){
			$col = 't_amount_bt';
			$prev_amount = $terminal->data()->t_amount_bt;
			$prev_amount = ($prev_amount) ? $prev_amount : 0;
		}


		$prev_amount = ($prev_amount) ? $prev_amount : 0;
		if($type == 1){
			$to_amount = $amount + $prev_amount;
		} else if ($type == 2){
			$to_amount = $prev_amount-$amount;
		}
		$now = time();
		$terminal->update(array(
			$col => $to_amount
		),$terminal_id);
		$terminal_mon->create( array(
			'terminal_id' => $terminal_id,
			'user_id' => $user->data()->id,
			'from_amount' =>$prev_amount,
			'amount' =>$amount,
			'to_amount'=>$to_amount,
			'status' => $type,
			'remarks' => $remarks,
			'is_active' => 1,
			'company_id' => $user->data()->company_id,
			'p_type'=>$payment_type,
			'created' => $now
		));
		echo "Updated Successfully";
	}

	function getPTDetails(){
		$id = Input::get('id');
		$sales = new Sales();
		$user = new User();
		$saleslist = $sales->salesTransactionBaseOnPaymentId($id);
		$cf = new Custom_field();
		$getstationdet = $cf->getcustomform('stations',$user->data()->company_id);
		$custom_station_name = isset($getstationdet->label_name)? strtoupper($getstationdet->label_name):'STATION';
		$custom_station_name = ucfirst(strtolower($custom_station_name));
		if($saleslist){
			echo "<h3>Transaction Details</h3>";
			foreach($saleslist as $sl){
				$isCancelled = '';
				if($sl->status == 1){
					$isCancelled = "<span class='text-danger'>Cancelled</span>";
				}
				$stationname = ($sl->station_name) ? escape($sl->station_name) :'None';
			?>
				<div class="panel panel-default">
					<div class="panel-body">
						<ul class="list-group">
							<li class="list-group-item">
								<?php echo escape($sl->item_code); ?>
							</li>
							<li class="list-group-item text-danger">
								<?php echo escape($sl->description); ?>
							</li>
							<li class="list-group-item text-muted">
								<strong>Price</strong>  <?php echo escape(number_format($sl->price,2)); ?>
							</li>
							<li class="list-group-item text-muted">
								<strong>Quantity</strong> <?php echo escape(number_format($sl->qtys)); ?>
							</li>
							<li class="list-group-item text-muted">
								<strong>Dsicount</strong> <?php echo escape(number_format($sl->discount + $sl->store_discount,2)); ?>
							</li>
							<li class="list-group-item text-muted">
								<strong>Adjustment</strong> <?php echo escape(number_format($sl->member_adjustment,2)); ?>
							</li>
							<li class="list-group-item text-muted">
								<strong>Total</strong> <?php echo escape(number_format((($sl->qtys * $sl->price) + $sl->member_adjustment - ($sl->discount + $sl->store_discount)),2)); ?>
							</li>

						</ul>
					</div>
				</div>
				<?php
			}
		}
	}
	function restoreFromRecycleBin(){
		$id = Input::get('id');
		$tbl = Input::get('tbl');
		$rec =new Recycle_bin();
		$rec->restoreItem($id,$tbl);
		echo "Restored Successfully";
	}
	function getUnreadOrder(){
		$user = new User();
		if(isset($user->data()->id) && !empty($user->data()->id)){
			$transfer_mon = new Transfer_inventory_mon();
			$getUnread =$transfer_mon->getUnread($user->data()->company_id,$user->data()->branch_id);
			if(isset($getUnread->cnt)  && $getUnread->cnt >  0){
				$transfer_mon->updateNotif($user->data()->company_id,$user->data()->branch_id,$user->data()->id);
			}
			echo isset($getUnread->cnt) ? $getUnread->cnt : 0;
		} else {
			echo 0;
		}
	}
	function requestSupplies(){
		$user = new User();
		$consumable_supply = new Consumable_supply();


		$toorder = json_decode(Input::get('toOrder'));
		$member_id = Input::get('member_id');
		$user_id = Input::get('user_id');
		$branch_id = Input::get('branch_id');
		$remarks = Input::get('remarks');
		$ref_id = Input::get('ref_id');
		$user_id = ($user_id) ? $user_id : 0;
		$member_id = ($member_id) ? $member_id : 0;

		$consumable_supply->create(array(
			'status' => 1,
			'is_active' =>1,
			'branch_id' =>$branch_id,
			'company_id' =>$user->data()->company_id,
			'created' => time(),
			'user_id' => $user->data()->id,
			'for_user_id' => $user_id,
			'member_id' => $member_id,
			'remarks' => $remarks,
			'ref_id' => $ref_id,
		));
		$lastid = $consumable_supply->getInsertedId();
		$err = "";
		$inserted = false;
		foreach($toorder as $t){
			/*$tranfer_mon_details = new Transfer_inventory_details();
			$newProduct = new Product($t->item_id);
			$unit = new Unit($newProduct->data()->unit_id);
			if(is_decimal($t->qty) &&  $unit->data()->is_decimal==0){
				$err .= "<p>".$newProduct->data()->item_code.", quantity should be a number.</p>";
				continue;
			}*/
			$inserted = true;
			$arr = "";
			if($t->rack_id){
				$arrcon = [];
				$arrcon[] = ['rack_id' => $t->rack_id,'rack_name' => $t->rack_name,'qty' => $t->qty];
				$arr = json_encode($arrcon);
			}
			$consumable_supply_details = new Consumable_supply_details();
			$consumable_supply_details->create(array(
				'consumable_supply_id' => $lastid,
				'item_id' =>$t->item_id,
				'qty' => $t->qty,
				'racking' => $arr
			));
		}
		if($inserted == false){
			$consumable_supply->update(array('is_active'=>0),$lastid);
			echo "Failed to request item.". $err;
		} else {
			if($err){
				$err = " Except:" . $err;
			}
			echo "All item(s) were ordered successfully". $err;
		}
	}
	function getForApprovalSupplies(){
		$consumable_supplies = new Consumable_supply();
		$user = new User();
		$items = $consumable_supplies->getForApproval($user->data()->company_id,1);
		if($items){
			?>
			<div id="no-more-tables">
				<table class="table table-bordered">
					<thead>
					<tr>
						<th>Id</th>
						<th>Branch</th>
						<th>For</th>
						<th>Request by</th>
						<th>Date Created</th>
						<th>Remarks</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<?php
						foreach($items as $item){
							$remarks =  ($item->remarks) ? escape($item->remarks) : "<i class='fa fa-ban'></i>"
							?>
							<tr>
								<td data-title='Id'><?php echo escape($item->id);?></td>
								<td data-title='Branch'>
									<?php echo escape($item->bname);?>
									<?php if($item->ref_id){ ?>
										<small class='span-block text-danger'>Ref Id: <?php echo $item->ref_id; ?></small>
									<?php 
									}
									?>
									
								</td>
								<td data-title='For'>
									<?php
										if($item->member_id){
											echo escape(ucwords($item->mln . ", " . $item->mfn . " " . $item->mmn));
										} else if($item->for_user_id) {
											echo escape(ucwords($item->for_lastname . ", " . $item->for_firstname . " " . $item->for_middlename));
											echo "<small class='span-block text-danger'>$item->user_branch</small>";
										}


									?>
								</td>
								<td data-title='Request by'><?php echo escape(ucwords($item->lastname . ", " . $item->firstname . " " . $item->middlename));?></td>
								<td data-title='Created'><?php echo escape(date('m/d/Y',$item->created));?></td>
								<td data-title='Remarks'><?php echo ($remarks);?></td>
								<td data-title=''><button data-id='<?php echo $item->id; ?>' class='btn btn-default btn-sm btnDetails'>Details</button></td>
							</tr>
							<?php
						}
					?>
					</tbody>
				</table>
			</div>
			<?php
		} else {
			echo "<p>No request at the moment.</p>";
		}
	}
	function getForLiquidationSupplies(){
		$consumable_supplies = new Consumable_supply();
		$user = new User();
		$items = $consumable_supplies->getForApproval($user->data()->company_id,2);
		if($items){
			?>
			<div id="no-more-tables">
				<table id='tblLiquidation' class="table table-bordered">
					<thead>
					<tr>
						<th>Id</th>
						<th>Branch</th>
						<th>For</th>
						<th>Request by</th>
						<th>Date Created</th>
						<th>Remarks</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<?php
						foreach($items as $item){
							$remarks =  ($item->remarks) ? escape($item->remarks) : "<i class='fa fa-ban'></i>"
							?>
							<tr>
								<td data-title='Id'><?php echo escape($item->id);?></td>
								<td data-title='Branch'><?php echo escape($item->bname);?></td>
								<td data-title='For'>
									<?php
										if($item->member_id){
											echo escape(ucwords($item->mln . ", " . $item->mfn . " " . $item->mmn));
										} else if($item->for_user_id) {
											echo escape(ucwords($item->for_lastname . ", " . $item->for_firstname . " " . $item->for_middlename));
										}

									?>
								</td>
								<td data-title='Request by'><?php echo escape(ucwords($item->lastname . ", " . $item->firstname . " " . $item->middlename));?></td>
								<td data-title='Created'><?php echo escape(date('m/d/Y',$item->created));?></td>
								<td data-title='Remarks'><?php echo ($remarks);?></td>
								<td data-title=''><button data-id='<?php echo $item->id; ?>' class='btn btn-default btn-sm btnDetails'>Details</button></td>
							</tr>
							<?php
						}
					?>
					</tbody>
				</table>
			</div>
			<?php
		} else {
			echo "<p>No request at the moment.</p>";
		}
	}
	function getSupplyRequestDetails(){
		$id = Input::get('id');
		$user = new User();
		if($id && is_numeric($id)){
			$con_details = new Consumable_supply_details();
			$con_supply = new Consumable_supply($id);
			$con_info = $con_supply->getInfo($id);
			$details = $con_details->getDetails($id);
			$name="";
			if($con_supply->data()->for_user_id){
				$u = new User($con_supply->data()->for_user_id);
				$name = $u->data()->firstname . " " . $u->data()->lastname;
			} else if ($con_supply->data()->member_id){
				$m = new Member($con_supply->data()->member_id);
				$name = $m->data()->lastname;
			}
			if($details){
				?>
				<div id="no-more-tables">
					<table id='tblSupplyForApproval' class="table table-bordered">
						<thead>
						<tr>
							<th>Item</th>
							<th>Qty</th>
							<th>Racking</th>
							<?php 	if($con_supply->data()->status ==2){ ?>
							<th>Consumption</th>
							<?php }?>
							<?php 	if($con_supply->data()->status ==3){ ?>
								<th>Consumed</th>
							<?php }?>
						</tr>
						</thead>
						<tbody>
						<?php
							$hasins= false;
							$todeduct = [];
							$arr_item=[];
							foreach($details as $item){
								$retracking = '';
								$arr_item[] = ['qty' =>  formatQuantity($item->qty),'item_code'=>$item->item_code,'description' => $item->description];

								if($con_supply->data()->status == 1 ){
									if(!$item->racking){
										$racking = inventory_racking(0, $item->qty, $item->item_id, $con_supply->data()->branch_id, false);
										$racks = json_decode($racking['racking'],true);
										$is_false = $racking['insufficient'];
										if($is_false){
											$hasins = true;
										}
										$retracking = "<table class='table'>";
										$retracking .= "<tr><th>Rack</th><th>Stock Man</th><th>Qty</th></tr>";
										foreach($racks as $r){
											$stock_man = (isset($r['stock_man']) && !empty($r['stock_man'])) ? $r['stock_man'] : 'None';
											$rdes = (isset($r['rack_description']) && !empty($r['rack_description'])) ?$r['rack_description'] : '';
											$retracking .= "<tr><td>$r[rack]<small class='span-block'>$rdes</small></td><td>$stock_man</td><td>" . formatQuantity($r['qty']) . "</td></tr>";
											$todeduct[$item->item_id][] =['rack_id' => $r['rack_id'],'rack_name' => $r['rack'],'qty' => $r['qty'] ];
										}
										$retracking .= "</table>";
									} else {
										$racks = json_decode($item->racking,true);
										$inventory = new Inventory();
										$retracking = "<table class='table'>";
										$retracking .= "<tr><th>Rack</th><th>Stock Man</th><th>Qty</th></tr>";
										foreach($racks as $r){
											$cur_qty = $inventory->getQty($item->item_id,$con_supply->data()->branch_id,$r['rack_id']);
											$inslbl = "";
											if(isset($cur_qty->qty)){
												if($cur_qty->qty < $item->qty){
													$hasins = true;
													$inslbl = "Insufficient";
												} else {
													$todeduct[$item->item_id][] =['rack_id' => $r['rack_id'],'rack_name' => $r['rack_name'],'qty' => $r['qty'] ];
												}
											} else {
												$hasins = true;
												$inslbl = "Insufficient";
											}
											$retracking .= "<tr><td>$r[rack_name] <span class='text-danger span-block'>$inslbl</span></td><td></td><td>" . formatQuantity($r['qty']) . "</td></tr>";
										}
										$retracking .= "</table>";
									}



								} else if ($con_supply->data()->status == 2 || $con_supply->data()->status == 3){
									$racked = json_decode($item->racking);
									if($racked){
										$retracking = "<table class='table'>";
										$retracking .= "<tr><th>Rack</th><th>Qty</th></tr>";
										foreach($racked as $r){
											$retracking .= "<tr><td>".$r->rack_name."</td><td>" . formatQuantity($r->qty) . "</td></tr>";

										}
										$retracking .= "</table>";
									}
								}

								?>
								<tr data-item_id='<?php echo $item->item_id; ?>' data-qty='<?php echo $item->qty; ?>'>
									<td data-title='Item'><?php echo escape($item->item_code);?><small class='span-block'><?php echo escape($item->description);?></small></td>
									<td data-title='Qty'><?php echo escape($item->qty);?></td>
									<td data-title='Racking'>
										<?php echo $retracking;?>
									</td>
									<?php if($con_supply->data()->status ==2){ ?>
										<td><input type="text" value='<?php echo $item->qty; ?>' class='form-control txt-qty-lg txtConQty' placeholder='Qty'></td>
									<?php }?>
									<?php if($con_supply->data()->status ==3){ ?>
										<td><?php echo $item->consume_qty; ?></td>
									<?php }?>
								</tr>
								<?php
							}
						?>
						</tbody>
					</table>
				</div>				<br>
				<div class="text-right">
					<div class="row">
						<div class="col-md-6 text-left">
							<button data-remarks='<?php echo $con_info->remarks; ?>' data-branch='<?php echo $con_info->bname; ?>' data-user_branch='<?php echo $con_info->user_branch; ?>' data-ref_id='<?php echo $con_supply->data()->ref_id; ?>' data-name='<?php echo $name; ?>' data-list='<?php echo json_encode($arr_item); ?>' data-id='<?php echo $id; ?>'   id='printSupplies' class='btn btn-default'>Print</button>
						</div>
						<div class="col-md-6">
							<?php
								if($con_supply->data()->status == 1 && $user->hasPermission('app_sup') && !$hasins) {
									?>
									<button data-id='<?php echo $id; ?>'  data-racks='<?php echo Encryption::encrypt_decrypt('encrypt', json_encode($todeduct)); ?>' id='btnApproveRequest' class='btn btn-default'>Approve</button>

									<?php
								}
								if($con_supply->data()->status == 1){
									?>
									<button data-id='<?php echo $id; ?>' id='btnDeclineRequest' class='btn btn-default'>Decline</button>
									<?php
								}

								if($con_supply->data()->status == 2 && $user->hasPermission('liq_sup')) {
									?>
									<button data-id='<?php echo $id; ?>'  id='btnLiquidateRequest' class='btn btn-default'>Liquidate</button>
									<?php
								}
							?>
						</div>
					</div>



				</div>
				<?php
			} else {
				echo "<p>Invalid data</p>";
			}
		} else {
			echo "<p>Invalid data</p>";
		}
	}
	function approveSupplyRequest(){
		$id = Input::get('id');

		$racks = Encryption::encrypt_decrypt('decrypt',Input::get('racks'));

		$racks = json_decode($racks);
		if(is_numeric($id) && $racks){
			$con_supply = new Consumable_supply($id);
			$valid  = true;
			$inventory = new Inventory();
			$inv_mon = new Inventory_monitoring();
			$user = new User();
			foreach($racks as $item_id => $racking){
				foreach($racking as $rack){
					$curinventory = $inventory->getQty($item_id, $con_supply->data()->branch_id, $rack->rack_id);
					if(!(isset($curinventory->qty) && $curinventory->qty >= $rack->qty)){
						$valid = false;
					}
				}
			}
			if($valid){
				$con_details = new Consumable_supply_details();
				Log::addLog($user->data()->id,$user->data()->company_id,"Approve Supplies ID $id","ajax_query.php");

				foreach($racks as $item_id => $racking){

					$lblracking =  json_encode($racking);

					foreach($racking as $rack){

						$curinventoryFrom = $inventory->getQty($item_id,$con_supply->data()->branch_id, $rack->rack_id);
						$inventory->subtractInventory($item_id,$con_supply->data()->branch_id,$rack->qty,$rack->rack_id);
						// monitoring
						$newqtyFrom = $curinventoryFrom->qty - $rack->qty;
						$inv_mon->create(array(
							'item_id' => $item_id,
							'rack_id' => $rack->rack_id,
							'branch_id' => $con_supply->data()->branch_id,
							'page' => 'ajax_query.php',
							'action' => 'Update',
							'prev_qty' => $curinventoryFrom->qty,
							'qty_di' => 2,
							'qty' => $rack->qty,
							'new_qty' => $newqtyFrom,
							'created' => time(),
							'user_id' => $user->data()->id,
							'remarks' => 'Deduct inventory ( '.SUPPLY_LABEL.' id #'.$id.')',
							'is_active' => 1,
							'company_id' => $user->data()->company_id
						));

					}
				$con_details->updateRacks($lblracking,$item_id,$id);
				}
				$con_supply->update(array('status' => 2),$id);
				echo "Request approved successfully";
			}
		}


	}
	function declineSupplyRequest(){
		$id = Input::get('id');
		$consumable_sup  = new Consumable_supply();
		$user = new User();
		$consumable_sup->update(array('status' => 6),$id);
		Log::addLog($user->data()->id,$user->data()->company_id,"Decline Supplies ID $id","ajax_query.php");

		echo "Request declined successfully";

	}
	function liquidateSupply(){
		$id = Input::get('id');
		if(is_numeric($id)){
			$consumable_sup  = new Consumable_supply($id);
			if($consumable_sup->data()->status == 2){
				$arr = json_decode(Input::get('arr'));
				$user = new User();
				if($arr){

					$recarr = [];
					$con_details = new Consumable_supply_details();
					foreach($arr as $a){
						$item_id = $a->item_id;
						$qty = $a->qty;
						$consume_qty = $a->consume_qty;
						$remaining_qty = $qty - $consume_qty;
						if($remaining_qty > 0){
							$recarr[$item_id] =  $remaining_qty;
						}
						$con_details->updateConsumption($consume_qty,$item_id,$id);
					}

					Log::addLog($user->data()->id,$user->data()->company_id,"Liquidate Supplies ID $id","ajax_query.php");


					if(count($recarr) > 0){
						$tranfer_mon = new Transfer_inventory_mon();
						$tranfer_mon->create(array(
							'status' => 1,
							'is_active' =>1,
							'branch_id' =>$consumable_sup->data()->branch_id,
							'company_id' =>$user->data()->company_id,
							'created' => time(),
							'modified' => time(),
							'from_where' => 'From ' . SUPPLY_LABEL
						));

						$lastid = $tranfer_mon->getInsertedId();
						$rackDisplay = new Rack();
						$dis = 0;

						foreach($recarr as $item_id => $remaining_qty){
							$tranfer_mon_details = new Transfer_inventory_details();
							$tranfer_mon_details->create(array(
								'transfer_inventory_id' => $lastid,
								'rack_id_from' => 0,
								'rack_id_to' => $dis,
								'item_id' =>$item_id,
								'qty' => $remaining_qty,
								'is_active' => 1
							));
						}
					}
					$consumable_sup  = new Consumable_supply();
					$consumable_sup->update(array('status' => 3),$id);
					echo "Liquidated successfully.";
				}
			}

		}



		/*
		 $tranfer_mon = new Transfer_inventory_mon();
		$tranfer_mon->create(array(
			'status' => 1,
			'is_active' =>1,
			'branch_id' =>$b,
			'company_id' =>$user->data()->company_id,
			'created' => time(),
			'modified' => time(),
			'from_where' => 'From ' . SUPPLY_LABEL,
			'payment_id' => $id
		));
		$lastid = $tranfer_mon->getInsertedId();
		foreach($toadd as $i => $v){
			$tranfer_mon_details = new Transfer_inventory_details();
			$tranfer_mon_details->create(array(
				'transfer_inventory_id' => $lastid,
				'rack_id_from' => 0,
				'rack_id_to' => $dis->id,
				'item_id' =>$i,
				'qty' => $v,
				'is_active' => 1
			));
		} */
	}
	function getSuppliesLog(){
		$consumable_supplies = new Consumable_supply();
		$user = new User();
		$items = $consumable_supplies->getForApproval($user->data()->company_id,3);
		if($items){
			?>
			<div id="no-more-tables">
				<table class="table table-bordered">
					<thead>
					<tr>
						<th>Id</th>
						<th>Branch</th>
						<th>For</th>
						<th>Request by</th>
						<th>Date Created</th>
						<th>Remarks</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<?php
						foreach($items as $item){
							$remarks =  ($item->remarks) ? escape($item->remarks) : "<i class='fa fa-ban'></i>"
							?>
							<tr>
								<td data-title='Id'><?php echo escape($item->id);?></td>
								<td data-title='Branch'><?php echo escape($item->bname);?></td>
								<td data-title='For'>
									<?php
										if($item->member_id){
											echo escape(ucwords($item->mln . ", " . $item->mfn . " " . $item->mmn));
										} else if($item->for_user_id) {
											echo escape(ucwords($item->for_lastname . ", " . $item->for_firstname . " " . $item->for_middlename));
										}
									?>
								</td>
								<td data-title='Request by'><?php echo escape(ucwords($item->lastname . ", " . $item->firstname . " " . $item->middlename));?></td>
								<td data-title='Created'><?php echo escape(date('m/d/Y',$item->created));?></td>
								<td data-title='Remarks'><?php echo ($remarks);?></td>
								<td data-title=''><button data-id='<?php echo $item->id; ?>' class='btn btn-default btn-sm btnDetails'>Details</button></td>
							</tr>
							<?php
						}
					?>
					</tbody>
				</table>
			</div>
			<?php
		} else {
			echo "<p>No request at the moment.</p>";
		}
	}
	function saveDiagnosis(){
		$member_id = Input::get('member_id');
		$doctor_id = Input::get('doctor_id');
		$nurse_id = Input::get('nurse_id');
		$remarks = Input::get('remarks');
		$now = time();
		$user = new User();
		$med_diag = new Med_diagnosis();
		if($member_id && ($doctor_id || $nurse_id) && $remarks){
			$med_diag->create(array(
				'member_id' => $member_id,
				'doctor_id' => $doctor_id,
				'nurse_id' => $nurse_id,
				'remarks' => $remarks,
				'created' => $now,
				'is_active' => 1,
				'company_id' => $user->data()->company_id,
				'user_id' => $user->data()->id
			));
			echo "Remarks added successfully.";
		}
	}
	function processServiceRequestAvision($items,$id){
		$items = json_decode($items);
		$service = new Item_service_request($id);
		$user = new User();
		if(is_numeric($id) && count($items)){
			foreach($items as $item){
				if($item->status == 13){

				}
				$service->changeStatusItem(
					$id,
					Encryption::encrypt_decrypt('decrypt',$item->item_id),
					$item->status
				);

			}
			$service->update(['status' => 4, 'history_status' => $service->data()->history_status. ",". 4],$id);
			echo '1';
		}
	}
	function changeStatusService(){

		$id = Encryption::encrypt_decrypt('decrypt',Input::get('id'));
		$items = Input::get('items');

		if(Configuration::thisCompany('avision')){
			processServiceRequestAvision($items,$id);
		} else {
			if(is_numeric($id) ){
				$user = new User();
				$service = new Item_service_request($id);
				if($service->data()->status == 1){
					$newStat = 2;
				} else if($service->data()->status == 2){
					$newStat = 3;
				}


				$items = json_decode($items);
				$usedItem = json_decode(Input::get('usedItem'));

				if($usedItem){
					$serviceItem = new Service_item_use();
					$now = time();
					foreach($usedItem as $used){
						$serviceItem->create(array(
							'service_id' => $id,
							'item_id' => $used->item_id,
							'qty' => $used->qty,
							'member_id' => $service->data()->member_id,
							'is_active' => 1,
							'company_id' => $user->data()->company_id,
							'created' => $now
						));
					}
				}
				if(count($items)){
					$stats  = 3;
					foreach($items as $item){
						if($item->status == 13){
							$newStat =3;
						}
						$service->changeStatusItem(
							$id,
							Encryption::encrypt_decrypt('decrypt',$item->item_id),
							$item->status
						);

					}

					$service->update(['status' => $newStat, 'history_status' => $service->data()->history_status. ",". $newStat],$id);

					Log::addLog($user->data()->id,$user->data()->company_id,"Item Service: Submit Request ID $id","ajax_query.php");

					echo "Action completed successfully.";
				}
			}
		}



	}
	function getAttachmentIssues(){
		$item_id  = Input::get('item_id');
		$rack_id  = Input::get('rack_id');
		$amend_upload = new Amend_upload();
		$results = $amend_upload->getAttachAllRack($item_id,$rack_id);
		echo json_encode($results);
	}
	function saveRackDefaults(){
		$arr = json_decode(Input::get('arr'),true);
		$user = new User();
		if(count($arr)){
			$rackcls =new Rack();
			foreach($arr as $a){

				$cur = $rackcls->isRackDefaultExists($a['branch_id']);
				if(isset($cur->cnt) && !empty($cur->cnt)){
					// update
					if($a['good_rack'] && $a['issues_rack']) {
						Log::addLog($user->data()->id, $user->data()->company_id, "Update Rack Defaults", "ajax_query.php");
					}
					$rackcls->updateDefault($a['branch_id'],$a['good_rack'],$a['issues_rack'],$a['surplus_rack'],$a['bo_rack']);
				} else {
					// insert
					if($a['good_rack'] && $a['issues_rack']){
						Log::addLog($user->data()->id,$user->data()->company_id,"Insert Rack Defaults","ajax_query.php");
					}

					$rackcls->insertDefault($a['branch_id'],$a['good_rack'],$a['issues_rack'],$user->data()->company_id,$a['surplus_rack'],$a['bo_rack']);
				}

			}
			echo "Updated successfully";
		}
	}
	function deleteDiagnosis(){
		$id = Input::get('id');
		if($id){
			$med = new Med_diagnosis();
			$med->update(['is_active' => 0],$id);
			echo "Record deleted successfully";
		}
	}
	function deleteItemOrders(){
		$cur_order = Input::get('cur_order');
		$cur_order = json_decode($cur_order);
		$user = new User();
		if($cur_order->member_id == 0 || $cur_order->payment_id == 0){

			$id = Input::get('id');
			$to_pending = Input::get('to_pending');
			$whdet = new Wh_order_details($id);

			if($to_pending == 1 && $cur_order->member_id != 0){
				$member_id = $cur_order->member_id;
				$branch_id_to = isset($cur_order->branch_id_to) ? $cur_order->branch_id_to : 0;
				$branch_id = $cur_order->branch_id;
				$item_id= $whdet->data()->item_id;
				$qty= $whdet->data()->qty;
				$company_id = $user->data()->company_id;
				$wh_pending = new Wh_order_pending();
				$wh_pending->create(array(
					'item_id' => $item_id,
					'member_id' => $member_id,
					'branch_id' => $branch_id,
					'branch_id_to' => $branch_id_to,
					'qty' => $qty,
					'company_id' => $company_id,
					'is_active' => 1,
					'status' => 1,
					'created' => time()
				));

			}

			$whdet->deleteItem($id);


			echo "Item Deleted successfully";
		}
	}
	function itemItemOrders(){
		$cur_order = Input::get('cur_order');
		$item_id = Input::get('item_id');
		$qty = Input::get('qty');
		$unit_qty = Input::get('unit_qty');
		$preferred_unit = Input::get('preferred_unit');
		$cur_order = json_decode($cur_order);
		$user = new User();
		if($cur_order->member_id == 0 || $cur_order->payment_id == 0){
			$item_cls = new Product($item_id);
			$valid = 0;
			$availability = getReservedStocks($item_id,$cur_order->branch_id,$qty);
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
			if($item_cls->data()->item_type == 1){
				$valid = 1;
			}
			if($valid){
				$alladj = 0;
				$orderdet = new Wh_order_details();
				$product = new Product();
				$price = $product->getPrice($item_id);
				$now = time();
				$user = new User();
				$member_id = $cur_order->member_id;
				if($member_id){
					$memberTerms = new Member_term();
					$memadj =$memberTerms->getAdjustment($member_id,$item_id);
					$total_member_adjustment = 0;
					if(count($memadj)){
						$alladjInd = 0;
						$alladjAbove = 0;
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
						if($alladjAbove){
							$alladj = $alladjAbove;
						} else if($alladjInd){
							$alladj = $alladjInd;
						}
					}
				}
				$adjustment_class = new Item_price_adjustment();
				$adj = $adjustment_class->getAdjustment($cur_order->branch_id,$item_id);
				$nadj = 0;
				if(isset($adj->adjustment)){
					$nadj += $adj->adjustment;
				} else {
					$nadj += 0;
				}

				$orderdet->create(array(
					'wh_orders_id' => $cur_order->id,
					'item_id' => $item_id,
					'price_id' => $price->id,
					'qty' => $qty,
					'created' => $now,
					'modified' => $now,
					'price_adjustment' => $nadj,
					'company_id' => $user->data()->company_id,
					'is_active' => 1,
					'terms' => 0,
					'original_qty' => $qty,
					'unit_qty' => $unit_qty,
					'preferred_unit' => $preferred_unit,
					'member_adjustment' => $alladj
				));

				echo "Updated successfully";
			} else {
				echo "Not enough stocks";
			}

		}
	}

	function updateItemOrders(){
		$cur_order = Input::get('cur_order');
		$cur_order = json_decode($cur_order);
		if($cur_order->member_id == 0 || $cur_order->payment_id == 0){
			$cur_order_details = Input::get('od');
			$cur_order_details = json_decode($cur_order_details);
			if($cur_order_details){
				$whDetails = new Wh_order_details();
				$inv = new Inventory();
				$whorder = new Wh_order($cur_order->id);
				if($whorder->data()->payment_id) {
					die("Update failed! Invoice/DR has already issued.");
				}
				$user = new User();
				$has_mem_adj = 0;
				if(Configuration::getValue('mem_adju')){
					$has_mem_adj = 1;
				}
				$has_over = 0;
				$rack_tags = new Rack_tag();
				$tags_ex = $rack_tags->get_tags_ex('wh_orders',$user->data()->company_id,$cur_order->branch_id);
				if(isset($tags_ex->id) && !empty($tags_ex->id)){
					$excempt_tags = $tags_ex->tag_id;
				} else {
					$excempt_tags =0;
				}

				foreach($cur_order_details as $det){
					$valid = 0;



					if($det->orig_qty > $det->qty){
						$valid = 1;
					} else {
						$tocheckqty = $det->qty - $det->orig_qty;
						if($tocheckqty){

							$availability = getReservedStocks($det->item_id,$cur_order->branch_id,$tocheckqty);

							if($availability && $availability['message']){
								if(!$availability['success']){
									$final_message =  $availability['message'];
								} else {
									$valid = 1;
								}
								$remaining = $availability['remaining'];
							}
						} else {
							$valid = 1;
						}

					}

					if(Configuration::getValue('strict_order') == 2){
						$valid = 1;
					}

					if(!$valid){
						$prod = new Product($det->item_id);
						if($prod->data()->item_type != -1){
							$valid = 1;
						}
					}

					if($valid){
						$det->qty = str_replace(',','',$det->qty);
						if (strpos($det->member_adjustment,'%')>0){
							$adj = (float) $det->member_adjustment;
							$adj = ($det->adjusted_price * $det->qty) * ($adj/100);
						} else {
							$adj = $det->member_adjustment;
						}
						if(Configuration::getValue('adjustment_default') == 2){
							$adj = $adj * -1;
						}
						$whDetails->updateWhDetails($det->id,$det->qty,$adj);
					} else {
						$has_over = 1;
					}

				}
				$msg = "";
				if($has_over) $msg = " <br>But some items are out of stock.";
				echo "Updated successfully." .$msg;
			}
		}
	}

	function convertIncomplete(){
		$toConvert = json_decode(Input::get('toConvert'),true);

		$itemUsed = json_decode(Input::get('itemUsed'),true);

		$inc_item_id = Input::get('inc_item_id');
		$inc_qty = Input::get('inc_qty');
		$rack_id = Input::get('inc_rack_id');
		$branch_id = Input::get('inc_branch_id');
		$inc_type = 4;

		if(count($toConvert) && is_numeric($inc_item_id) && is_numeric($inc_qty)){
			// deduct incomplete
			$user = new User();
			$itemcls = new Product($inc_item_id);
			$inv_issues = new Inventory_issue();
			$inv_mon = new Inventory_issues_monitoring();


			if($inv_issues->checkIfItemExist($inc_item_id,$branch_id,$user->data()->company_id,$rack_id,$inc_type)){
				$curinventoryFrom = $inv_issues->getQty($inc_item_id,$branch_id,$rack_id,$inc_type);
				$currentqty = $curinventoryFrom->qty;
				$inv_issues->subtractInventory($inc_item_id,$branch_id,$inc_qty,$rack_id,$inc_type);
			} else {
				$currentqty = 0;
			}

			$new_issues = $currentqty - $inc_qty;
			$inv_mon->create(array(
				'item_id' => $inc_item_id,
				'rack_id' => $rack_id,
				'branch_id' =>$branch_id,
				'page' => 'ajax/ajax_query.php',
				'action' => 'Update',
				'prev_qty' => $currentqty,
				'qty_di' => 2,
				'qty' => $inc_qty,
				'new_qty' => $new_issues,
				'created' => time(),
				'user_id' => $user->data()->id,
				'remarks' => 'Convert item parts to good',
				'is_active' => 1,
				'company_id' => $user->data()->company_id,
				'type' => $inc_type
			));



			$inventory = new Inventory();
			$inventory_mon = new Inventory_monitoring();
			foreach($toConvert as $con){
				if($con['item_id'] && $con['qty']){
					$item_id = $con['item_id'] ;

					$des_rack_id =  $con['rack_id'];
					$convert_qty = $con['qty'] ;
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
							'remarks' => 'Convert item parts to good',
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
							'remarks' => 'Convert item parts to good',
							'is_active' => 1,
							'company_id' => $user->data()->company_id
						));
					}
				}
			}


			// deduct qty used in converting item to set
			if(count($itemUsed)){
				foreach($itemUsed as $iused){
					$u_item_id = $iused['item_id'];
					$u_qty = $iused['qty'];
					$u_rack_id = $iused['rack_id'];

					$curinventory = $inventory->getQty($u_item_id,$branch_id,$u_rack_id);
					$newqty = $curinventory->qty - $u_qty;
					$inventory->subtractInventory($u_item_id,$branch_id,$u_qty,$u_rack_id);

					$inventory_mon->create(array(
						'item_id' => $u_item_id,
						'rack_id' => $u_rack_id,
						'branch_id' => $branch_id,
						'page' => 'admin/addinventory',
						'action' => 'Insert',
						'prev_qty' => $curinventory->qty,
						'qty_di' => 2,
						'qty' => $u_qty,
						'new_qty' => $newqty,
						'created' => time(),
						'user_id' => $user->data()->id,
						'remarks' => 'Used for fixing damage product',
						'is_active' => 1,
						'company_id' => $user->data()->company_id
					));

				}
			}

			echo "Converted successfully";
		}
	}
function checkItemAvailability(){
	$item_id = Input::get('item_id');
	$rack_id = Input::get('rack_id');
	$qty = Input::get('qty');
	$branch_id = Input::get('branch_id');
	if(is_numeric($item_id) && is_numeric($rack_id) && is_numeric($qty) && is_numeric($branch_id)){
		$inventory = new Inventory();
		$itemqty = $inventory->getQty($item_id,$branch_id,$rack_id);
		if(isset($itemqty->qty) && !empty($itemqty->qty) && $itemqty->qty >= $qty){
			echo 1;
		} else {
			echo 0;
		}
	}  else {
		echo 0;
	}
}
function requestBadOrder(){
	$toOrder = Input::get('toOrder');
	$supplier_id = Input::get('supplier_id');
	$branch_id = Input::get('branch_id');
	$supplier_order_id = Input::get('supplier_order_id');
	$remarks = Input::get('remarks');
	$toOrder = json_decode($toOrder);
	if($supplier_id && $branch_id && $toOrder){
		$user = new User();
		$newRequest = new Bad_order();
		$now = time();
		$newRequest->create(array(
			'branch_id' => $branch_id,
			'supplier_id' => $supplier_id,
			'remarks' => $remarks,
			'supplier_order_id' => $supplier_order_id,
			'company_id' => $user->data()->company_id,
			'is_active' => 1,
			'status' => 1,
			'created' => $now
		));
		$last_id = $newRequest->getInsertedId();
		$inventory = new Inventory();
		$inv_mon = new Inventory_monitoring();
		foreach($toOrder as $order){
			// deduct inventory
			if($inventory->checkIfItemExist($order->item_id,$branch_id,$user->data()->company_id,$order->rack_id)){
				$curinventoryFrom = $inventory->getQty($order->item_id,$branch_id,$order->rack_id);
				$currentqty = $curinventoryFrom->qty;
				$inventory->subtractInventory($order->item_id,$branch_id,$order->qty,$order->rack_id);
			} else {
				$currentqty = 0;
			}
			// monitoring
			$newqtyFrom = $currentqty - $order->qty;
			$inv_mon->create(array(
				'item_id' => $order->item_id,
				'rack_id' => $order->rack_id,
				'branch_id' => $branch_id,
				'page' => 'ajax/ajax_query.php',
				'action' => 'Update',
				'prev_qty' => $currentqty,
				'qty_di' => 2,
				'qty' => $order->qty,
				'new_qty' => $newqtyFrom,
				'created' => time(),
				'user_id' => $user->data()->id,
				'remarks' => 'Deduct inventory from rack (Bad Order request #'.$last_id.')',
				'is_active' => 1,
				'company_id' => $user->data()->company_id
			));

			$details = new Bad_order_detail();
			$details->create(array(
				'bad_order_id' => $last_id,
				'item_id' => $order->item_id,
				'qty' => $order->qty,
				'rack_id' => $order->rack_id,
				'remarks' => '',
				'created' => $now,
				'company_id' => $user->data()->company_id
			));
		}
		echo "Requested successfully.";
	}
}
	function getBadorder(){
		$bad_orders = new Bad_order();
		$user = new User();
		$status =Input::get('status');
		$items = $bad_orders->getForApproval($user->data()->company_id,$status);
		if($items){
			?>
			<div id="no-more-tables">
				<table class="table table-bordered">
					<thead>
					<tr>
						<th>Id</th>
						<th>Branch</th>
						<th>Supplier</th>
						<th>Order Id</th>
						<th>Date Created</th>
						<th>Remarks</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<?php
						foreach($items as $item){
							$remarks =  ($item->remarks) ? escape($item->remarks) : "<i class='fa fa-ban'></i>"
							?>
							<tr>
								<td data-title='Id'><?php echo escape($item->id);?></td>
								<td data-title='Branch'><?php echo escape($item->bname);?></td>
								<td data-title='Supplier'>
									<?php echo escape($item->supplier_id);?>
								</td>
								<td data-title='Order ID'>
									<?php echo escape($item->supplier_order_id);?>
								</td>
								<td data-title='Created'><?php echo escape(date('m/d/Y',$item->created));?></td>
								<td data-title='Remarks'><?php echo ($remarks);?></td>
								<td data-title=''><button data-id='<?php echo $item->id; ?>' class='btn btn-default btn-sm btnDetails'>Details</button></td>
							</tr>
							<?php
						}
					?>
					</tbody>
				</table>
			</div>
			<?php
		} else {
			echo "<p>No request at the moment.</p>";
		}
	}

	function getBadOrderDetails(){
		$id = Input::get('id');

		$user = new User();
		if($id && is_numeric($id)){
			$con_details = new Bad_order_detail();
			$con_supply = new Bad_order($id);
			$details = $con_details->getDetails($id);
			if($details){
				?>
				<div id="no-more-tables">
					<table id='tblSupplyForApproval' class="table table-bordered">
						<thead>
						<tr>
							<th>Item</th>
							<th>Qty</th>
							<th>Rack</th>
						</tr>
						</thead>
						<tbody>
						<?php

							foreach($details as $item){

								if($con_supply->data()->status == 1){

								}

								?>
								<tr data-item_id='<?php echo $item->item_id; ?>' data-qty='<?php echo $item->qty; ?>'>
									<td data-title='Item'><?php echo escape($item->item_code);?><small class='span-block'><?php echo escape($item->description);?></small></td>
									<td data-title='Qty'><?php echo escape($item->qty);?></td>
									<td data-title='Rack'><?php echo escape($item->rack);?></td>

								</tr>
								<?php
							}
						?>
						</tbody>
					</table>
				</div>				<br>
				<div class="text-right">
					<?php
						if($con_supply->data()->status == 1) {
							?>
							<button data-id='<?php echo $id; ?>'   id='btnApproveRequest' class='btn btn-default'>Approve</button>
							<button style='display:none;' data-id='<?php echo $id; ?>' id='btnDeclineRequest' class='btn btn-default'>Decline</button>
							<?php
						}
					?>
				</div>
				<?php
			} else {
				echo "<p>Invalid data</p>";
			}
		} else {
			echo "<p>Invalid data</p>";
		}
	}

	function approveRequestBadOrder(){
		$id = Input::get('id');
		$bad_order = new Bad_order();
		if(is_numeric($id)){
			$bad_order->update(array('status'=>2),$id);
			echo "Order approved successfully.";
		} else {
			echo "Invalid details.";
		}

	}
	function getReOrderDetails(){
		$item_id = Input::get('item_id');
		$branch_id = Input::get('branch_id');
		$searchBranch = Input::get('searchBranch');
		$user = new User();
		$item = new Product($item_id);
		$op = new Reorder_point();
		$limit = 20;
		$start=0;
		$countRecord = $op->countRecord($user->data()->company_id, $item->data()->item_code, $branch_id);

		$total_pages = $countRecord->cnt;



		$company_op = $op->get_active_record($user->data()->company_id, $start, $limit, $item->data()->item_code, $branch_id);

			?>
			<div id="no-more-tables">
			<table class='table' id='tblSales'>
			<thead>
			<tr>

				<TH>Barcode</TH>
				<TH>Item Code</TH>
				<TH>Reorder point</TH>
				<TH>Order Quantity</TH>
				<TH>Branch</TH>
				<TH>Order Branch</TH>
				<TH>Month</TH>
				<TH>Created</TH>

			</tr>
			</thead>
			<tbody>
				<?php
					if($company_op) {
						$mm = array('Unknown', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December', 'ALL');
						foreach($company_op as $o) {
							 $by = new Branch($o->orderby_branch_id);
							if($o->orderto_branch_id && $o->orderto_branch_id != -2){
								$to = new  Branch($o->orderto_branch_id);
								$toname = $to->data()->name;

							} else if ($o->orderto_supplier_id) {
								$to = new  Supplier($o->orderto_supplier_id);
								$toname = $to->data()->name;
							} else {
								$toname = "Other branch";
							}

							?>
							<tr>
								<td data-title='Barcode'><?php echo escape($o->barcode) ?></td>
								<td data-title='Item Code'><?php echo escape($o->item_code) ?></td>
								<td data-title='Order Point'><?php echo escape($o->order_point) ?></td>
								<td data-title='Qty'><?php echo escape($o->order_qty) ?></td>
								<td data-title='Order By'><?php echo escape($by->data()->name) ?></td>
								<td data-title='Order From'><?php echo escape($toname); ?></td>
								<td data-title='Month'><?php echo $mm[$o->month]; ?></td>
								<td data-title='Created'><?php echo date('m/d/Y', escape($o->created)); ?></td>

							</tr>
							<?php
						}
					} else {
						echo "<tr><tr><td colspan='8'><h3><span class='label label-info'>No Record Found...</span></h3></td></tr></tr>";
					}
				?>
			</tbody>
			</table>
			</div>
				<?php

	}
	function addServiceRemarks(){
		$id = Encryption::encrypt_decrypt('decrypt',Input::get('id'));
		$remarks = Input::get('remarks');
		$ref_table= 'service';
		if(is_numeric($id) && $remarks){
			$rem_list = new Remarks_list();
			$user = new User();
			$rem_list->create(
				array(
					'ref_table' => $ref_table,
					'ref_id' => $id,
					'remarks' => $remarks,
					'company_id' => $user->data()->company_id,
					'is_active' =>1,
					'user_id' =>$user->data()->id,
					'created' =>time()
				)
			);
			echo "Remarks added successfully.";
		}
	}
	function getServiceRemarks(){
		$id = Encryption::encrypt_decrypt('decrypt',Input::get('id'));
			$ref_table= 'service';
			if(is_numeric($id)){
				$rem_list = new Remarks_list();
				$user = new User();
				$remarks = $rem_list->getServices($id,$ref_table,$user->data()->company_id);
				if($remarks){
					?>
					<table class='table table-bordered'>
						<thead>
						<tr>
							<th>Date</th>
							<th>Created By</th>
							<th>Remarks</th>
						</tr>
						</thead>
						<tbody>
						<?php
							foreach($remarks as $rem){
								?>
								<tr>
									<td style='border-top:1px solid #ccc;'><?php echo date('F d, Y H:i:s A',$rem->created); ?></td>
									<td style='border-top:1px solid #ccc;'><?php echo ucwords($rem->firstname . " " . $rem->lastname); ?></td>
									<td style='border-top:1px solid #ccc;'><?php echo $rem->remarks; ?></td>
								</tr>
								<?php
							}
						?>
						</tbody>
					</table>
					<?php
				}
		}
	}
	function addNewPoint(){
		$amount = Input::get('a');
		$point = Input::get('p');
		$name = Input::get('n');
		$unit = Input::get('u');
		if($name && $amount && $point && $unit){
			$now = time();
			$point_cls = new Point();
			$user = new User();
			$cur_point = $point_cls->getPoints($user->data()->company_id,$name);
			if($cur_point){
				echo "Invalid name.";
			} else {
				$point_cls->create(array(
					'company_id' => $user->data()->company_id,
					'is_active' => 1,
					'user_id' => $user->data()->id,
					'created' =>$now,
					'amount' =>$amount,
					'points' =>$point,
					'unit_name' =>$unit,
					'name' =>$name
				));
				echo "1";
			}


		} else {
			echo "Invalid data.";
		}

	}
	function getPoints(){
		$point_cls = new Point();
		$user = new User();
		$cur_point = $point_cls->getMembers($user->data()->company_id);
		echo "<div class='text-right' style='margin:10px;'>";
		echo " <button id='btnAddMemGroup' class='btn btn-default btn-sm'><i class='fa fa-plus'></i> Add Membership</button>";
		echo " <button id='btnAddPoint' class='btn btn-default btn-sm'><i class='fa fa-plus'></i> Add Point</button>";
		echo " <button class='btn btn-default btn-sm' id='registerUser' ><i class='fa fa-list'></i> Register</button>";
		echo "</div>";
		if($cur_point){
			?>

			<?php
				$arr= [];
				$prev = "";
				$ctrcol = 0;
				$arr_group = [];
				$added = [];
			foreach($cur_point as $pp){
					$arr_group[$pp->pg_id][] = $pp->point_id;
					$point = $pp->points;
					$amount = $pp->amount;
					$name = $pp->name;
					$group_name = "";
					if($prev != $pp->pg_id){
						$group_name = $pp->group_name;
						if($prev){
							echo "</tbody></table></div></div></div>";
						}
					}

					$prev = $pp->pg_id;
					if(!in_array($pp->point_id,$added)){
						$arr[] = ['name'=>ucwords($name),'id'=>$pp->point_id];
						$added[] = $pp->point_id;
					}

					if($group_name){
						if($ctrcol % 3 == 0){
							echo "<div class='clearfix'></div>";
						}
						$ctrcol++;
						?>

						<div class='col-md-4'>
						<div class='panel panel-default'>
						<div class="panel-heading"><a href='#' class='updateGroup' data-name='<?php echo $group_name; ?>'  data-sup_count='<?php echo $pp->supplementary; ?>' data-id='<?php echo $pp->pg_id; ?>'><?php echo $group_name; ?></a></div>
						<div class="panel-body">
						<table class='table'>
						<thead>
						<tr>
							<th>Name</th>
							<th>Amount</th>
							<th>Points</th>
							<th></th>
						</tr>
						</thead>
						<tbody>
						<?php
					}
			?>

				<tr>
					<td ><i class='fa fa-money'></i> <?php echo escape(ucwords($name)); ?></td>
					<td ><?php echo number_format($amount,2); ?></td>
					<td><?php echo number_format($point,3); ?></td>
					<td>
						<button data-id='<?php echo $pp->point_id ?>' data-amount='<?php echo $amount; ?>'  data-point='<?php echo $point; ?>' class='btn btn-default btn-sm updatePoints' ><i class='fa fa-pencil'></i> Update</button>
					</td>
				</tr>
				<?php
			}
			?>

			<?php
			echo "</tbody></table></div></div></div>";
			foreach($arr_group as $key => $gr){
				echo "<input type='hidden' id='hid_group_".$key."' value='".implode(',',$gr)."'>";
			}
			echo "<input type='hidden' id='point_list' value='".json_encode($arr)."'>";

		}
		?>



		<?php
	}
	function updatePoints(){
		$amount = Input::get('amount');
		$point = Input::get('point');
		$id = Input::get('id');
		// update points table
		$amount  = ($amount) ? $amount : 0;
		$point  = ($point) ? $point : 0;

		$point_cls = new Point();
		$point_history_cls = new Point_history();
		$user = new User();
		$cur = $point_cls->getPoints($user->data()->company_id,$id);
		$now= time();
		if($cur){
			$prev_amount = $cur->amount;
			$prev_point = $cur->points;
			$point_cls->update(array(
				'amount' =>$amount,
				'points' =>$point
			),$cur->id);
		} else {
			/*$prev_amount = 0;
			$prev_point = 0;
			$point_cls->create(array(
				'company_id' => $user->data()->company_id,
				'is_active' => 1,
				'user_id' => $user->data()->id,
				'created' =>$now,
				'amount' =>$amount,
				'points' =>$point
			));*/
		}

		// add history
		$point_history_cls->create(array(
			'company_id' => $user->data()->company_id,
			'is_active' => 1,
			'user_id' => $user->data()->id,
			'point_id' => $cur->id,
			'from_amount' => $prev_amount,
			'from_points' => $prev_point,
			'to_amount' => $amount,
			'to_points' => $point,
			'created' => $now
		));
		echo "Updated successfully.";

	}
	function registerUserPoint(){
		$member_id = Input::get('member_id');
		$point_group =Input::get('point_group');

		if(is_numeric($member_id) && is_numeric($point_group)){
			// select group in rel
			$mem = new Member();
			$mem->update(['membership_id' => $point_group],$member_id);
			$pg_rel = new Point_group_rel();
			$results = $pg_rel->getRel($point_group);
			if($results){
				foreach($results as $res){
					$point_cls = new Point();
					$user = new User();
					$point_cls->updateUserPoint($member_id,$user,0,0,$res->point_id,0);
				}
			}

			echo 1;
		} else {
			echo "Invalid Form";
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
				$pending_spare_qty = $whorder->pendingSpare($spare->item_id_raw,$branch_id); // get pending qty raw
				$assemble_spare_qty = 0; //$whorder->spareWithAssemble($spare->item_id_raw,$branch_id);
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
		}
		return $arr_inv;
	}