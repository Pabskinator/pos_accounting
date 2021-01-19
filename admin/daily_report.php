<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';

	if(!$user->hasPermission('sales')){
		// redirect to denied page
		Redirect::to(1);
	}

	function getPaymentsOrder($payment_id){

		$id = $payment_id;
		$cash = new Cash();
		$credit = new Credit();
		$cheque = new Cheque();
		$bt = new Bank_transfer();
		$con = new Payment_consumable();
		$conFree = new Payment_consumable_freebies();
		$member_credit = new Member_credit();
		$deduction = new Deduction();

		$cash_list = $cash->get_active('cash',array('payment_id','=',$id));
		$credit_list = $credit->get_active('credit_card',array('payment_id','=',$id));
		$cheque_list = $cheque->get_active('cheque',array('payment_id','=',$id));
		$bt_list = $bt->get_active('bank_transfer',array('payment_id','=',$id));
		$con_list = $con->get_active('payment_consumable',array('payment_id','=',$id));
		$conFree_list = $conFree->get_active('payment_consumable_freebies',array('payment_id','=',$id));
		$member_credit_list = $member_credit->get_active('member_credit',array('payment_id','=',$id));
		$deductions = $deduction->get_active('deductions',array('payment_id','=',$id));

		$arr=[];

		if($deductions){
			foreach($deductions as $c){
				$arr['deduction']['id'] = $c->id;
				$arr['deduction']['amount'] = $c->amount;
			}
		}

		if($con_list){
			foreach($con_list as $c){
				$arr['consumable']['id'] = $c->id;
				$arr['consumable']['amount'] = $c->amount;
			}
		}

		if($cash_list){
			foreach($cash_list as $c){
				$arr['cash'][] = ['id' => $c->id,'amount' => $c->amount,'date' =>date('m/d/Y',$c->created)];

			}
		}

		if($credit_list){
			foreach($credit_list as $c){
				$arr['credit'][] = [
					'id' =>$c->id,
					'amount' =>$c->amount,
					'bank' =>$c->bank_name,
					'ref_number' =>$c->card_number,
					'date' =>date('m/d/Y',$c->date)
				];
			}
		}

		if($cheque_list){
			foreach($cheque_list as $c){
				$arr['cheque'][] = [
					'id' =>$c->id,
					'amount' =>$c->amount,
					'bank' =>$c->bank,
					'ref_number' =>$c->check_number,
					'date' =>date('m/d/Y',$c->payment_date)
				];

			}
		}

		if($bt_list){
			foreach($bt_list as $c){
				$arr['bt'][] = [
					'id' =>$c->id,
					'amount' =>$c->amount,
					'bank' =>$c->bankfrom_name,
					'ref_number' =>$c->bankfrom_account_number,
					'date' =>date('m/d/Y',$c->date)
				];
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
				$arr['mem_credit']['amount_paid'] = $c->amount_paid;
				$arr['mem_credit']['date'] =  $c->created;
				$arr['mem_credit']['modified'] =  $c->modified;
			}
		}


		return json_encode($arr);
	}
	$member_arr = [];
	$member_att = [];
	$member_credit = [];
	$coaches_commissions = [];

	$ci = new Commission_item();
	$list = $ci->get_active('commission_items',array('1' ,'=','1'));

	if($list){
		foreach($list as $ci_data){
			$commissions[$ci_data->item_id] = $ci_data->amount;
		}
	}
	/*
	$commissions = [
		'11'=>50,
		'32'=>300,
		'33'=>50,
		'43'=>50,
		'4651' => 100,
		'55' => 50,
		'43' => 100,
		'57' => 100,
		'58' => 100,
		'6' => 100,
		'64' => 100,
		'59' => 300,
		'8' => 100,
		'5' => 50,
		'69' => 100,
		'77' => 100,
		'52' => 100,
		'37' => 100
	]; */

	// sales
	if(isset($_POST['btnSubmit'])){
		if(Input::get('txtDateFrom') && Input::get('txtDateTo')){
			$dt = Input::get('txtDateFrom');
			$dt1 = strtotime($dt);
			$dt2 = strtotime(Input::get('txtDateTo') . "1 day -1 min");
		} else {
			$dt = date('m/d/Y');
			$dt1 = strtotime($dt);
			$dt2 = strtotime($dt . "1 day -1 min");
		}
	} else {
		$dt = date('m/d/Y');
		$dt1 = strtotime($dt);
		$dt2 = strtotime($dt . "1 day -1 min");
	}

	$sale = new Sales();
	$sales = $sale->getStoreSales($dt1,$dt2,$user->data()->company_id,$user->data()->branch_id,4,0,true);

	// service attendance

	$service_attendance = new Service_attendance();
	$start= 0;
	$limit = 500;
	$attendance = $service_attendance->get_record($start, $limit,$dt1,$dt2);

	// credit paid today

	$credit_cls = new Member_credit();
	$credit_list_paid = $credit_cls->getCreditForTheDay($dt1,$dt2);

	$all_member_list = [];

	$member_item_arr = [];

	$item_arr_sold = [];

	$con_arr = [];

?>



	<!-- Page content -->
	<div id="page-content-wrapper">




	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Daily Reports
			</h1>

		</div>
		<div class='text-right'>
			<a class='btn btn-primary btn-sm' style='margin-bottom: 5px;' href="commission_item.php">Commission Item</a>
		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')){
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('flash')."</div>";
			}
		?>

		<div class="row">
			<div class="col-md-12">

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Daily Reports</div>
					<div class="panel-body">
						<form action="" method="POST">
							<div class="form-group">
							<div class="row">
								<div class="col-md-3"><input type="text" placeholder='Date From' class='form-control' id='txtDateFrom' name='txtDateFrom'></div>
								<div class="col-md-3"><input type="text" placeholder='Date To' class='form-control' id='txtDateTo' name='txtDateTo'></div>
								<div class="col-md-3"><input name='btnSubmit' type="submit" class='btn btn-default' value='Submit'></div>
								<div class="col-md-3"></div>
							</div>
							</div>
						</form>

						<?php
							echo "<p>From: " . date('m/d/Y H:i:s A',$dt1) . " To: " . date('m/d/Y H:i:s A',$dt2). "</p>";
							$sales_cls = new Sales();
							foreach($sales as $s){

								$payment = getPaymentsOrder($s->payment_id);

								$arr_payments = json_decode($payment,true);

								if(!in_array($s->member_id,$all_member_list)) $all_member_list[] = $s->member_id;



								$member_arr[$s->member_id]['member_name'] = $s->mln;


								$items_sold = $sales_cls->salesTransactionBaseOnPaymentId($s->payment_id,1);

								foreach($items_sold as $is){
									if(!in_array($is->item_code,$item_arr_sold)) $item_arr_sold[] = $is->item_code;

									if(isset($member_item_arr[$s->member_id][$is->item_code])){
										$member_item_arr[$s->member_id][$is->item_code] += $is->qtys;
									} else {
										$member_item_arr[$s->member_id][$is->item_code] = $is->qtys;
									}

									$con_qty = $is->con_qty;
									if($con_qty && $con_qty != 10000){
										$com_amt = isset($commissions[$is->item_id]) ? $commissions[$is->item_id] : 0;

										if(isset($con_arr[$s->member_id])){
											$con_arr[$s->member_id] += ($com_amt * $con_qty);
										} else {
											$con_arr[$s->member_id] = ($com_amt * $con_qty);
										}

									}
								}




								$total_cc = 0;
								$cash = 0;
								$bt_total = 0;
								$cheque_total = 0;
								$total_mem_credit = 0;
								$deduction = 0;

								if(isset($arr_payments['cash'])){

									foreach($arr_payments['cash'] as $arr_cash){
										$cash += $arr_cash['amount'];
									}

									if(isset($member_arr[$s->member_id]['cash'])){
										$member_arr[$s->member_id]['cash'] += $cash;
									} else {
										$member_arr[$s->member_id]['cash'] = $cash;
									}

								}

								if(isset($arr_payments['credit'])){
									foreach($arr_payments['credit'] as $arr_ccc){
										$total_cc += $arr_ccc['amount'];
									}
									if(isset($member_arr[$s->member_id]['cc'])){
										$member_arr[$s->member_id]['cc'] += $total_cc;
									}  else {
										$member_arr[$s->member_id]['cc'] = $total_cc;
									}

								}
								if(isset($arr_payments['bt'])){
									foreach($arr_payments['bt'] as $arr_bt){
										$bt_total += $arr_bt['amount'];
									}
									if(isset($member_arr[$s->member_id]['bt'])){
										$member_arr[$s->member_id]['bt'] += $bt_total;
									}  else {
										$member_arr[$s->member_id]['bt'] = $bt_total;
									}
								}
								if(isset($arr_payments['cheque'])){
									foreach($arr_payments['cheque'] as $arr_cheque){

										$cheque_total += $arr_cheque['amount'];

									}
									if(isset($member_arr[$s->member_id]['cheque'])){
										$member_arr[$s->member_id]['cheque'] += $cheque_total;
									}  else {
										$member_arr[$s->member_id]['cheque'] = $cheque_total;
									}

								}

								if(isset($arr_payments['deduction'])){
									$deduct = $arr_payments['deduction']['amount'];
									$deduction += $deduct;

								/*	if(isset($member_arr[$s->member_id]['cheque'])){
										$member_arr[$s->member_id]['cheque'] += $cheque_total;
									}  else {
										$member_arr[$s->member_id]['cheque'] = $cheque_total;
									}
									*/
								}

								if(isset($arr_payments['mem_credit'])){
									//$total_mem_credit = $arr_payments['mem_credit']['amount']  - $arr_payments['mem_credit']['amount_paid'] ;
									$total_mem_credit = $arr_payments['mem_credit']['amount'];
									if($arr_payments['mem_credit']['amount_paid']){
										$member_arr[$s->member_id]['cash'] -= $arr_payments['mem_credit']['amount_paid'];
									}
									if(isset($member_arr[$s->member_id]['mem_credit'])){
										$member_arr[$s->member_id]['mem_credit'] += $total_mem_credit;
									}  else {
										$member_arr[$s->member_id]['mem_credit'] = $total_mem_credit;
									}
								}
							}



							/*************Attendance******************/
							if($attendance){
							//dump($attendance);
								foreach($attendance as $att){

									if(!in_array($att->member_id,$all_member_list)) $all_member_list[] = $att->member_id;

									$member_att[$att->member_id][] = [
											'member_id' => $att->member_id,
											'item_id' => $att->item_id,
											'item_code' => $att->item_code,
											'member_name' => $att->member_name,
											'coach_name' => $att->coach_name
											];

									if(isset($commissions[$att->item_id]) && $commissions[$att->item_id]){
										if(isset($coaches_commissions[$att->coach_name]) && $coaches_commissions[$att->coach_name]){
											$coaches_commissions[$att->coach_name] += $commissions[$att->item_id];

										} else {
											if($att->coach_name)
											$coaches_commissions[$att->coach_name] = $commissions[$att->item_id];

										}
									}

								}

							}

							if($credit_list_paid){

								foreach($credit_list_paid as $clp){
									$amount_paid = $clp->amount_paid;
									$member_paid = $clp->member_id;

									if(!in_array($member_paid,$all_member_list)) $all_member_list[] = $member_paid;

									$total_credit_paid = 0;
									if($amount_paid){
										$json = $clp->json_payment;
										$data = json_decode($json);
										foreach($data as $ind_data){
											if(isset($ind_data->amount) && $ind_data->amount){
												if($ind_data->date > $dt1 && $ind_data->date < $dt2){
													$total_credit_paid += $ind_data->amount;
												}

											}
										}


									}
									if(isset($member_credit[$member_paid]) && $total_credit_paid){

										$member_credit[$member_paid] += $total_credit_paid;
									} else {
										if($total_credit_paid)
										$member_credit[$member_paid] = $total_credit_paid;
									}
								}



							}



							if($all_member_list){
								echo "<div class='table-responsive'>";
								echo "<table style='font-size:0.8em;' id='tblForApproval' class='table table-bordered'>";
								echo "<thead><tr>";
								echo "<th>Member</th><th>Service</th><th>Coach</th><th>Com</th>";
								foreach($item_arr_sold as $item_sold){
									echo "<th>$item_sold</th>";
								}
								echo "<th class='text-right'>Cash</th><th>CC</th><th>Cheque</th><th class='text-right'>Credit</th><th class='text-right'>Credit Paid</th><th>Commission</th>";
								echo "</tr></thead>";
								echo "<tbody>";
								$grand_total_cash = 0;
								$grand_total_cc = 0;
								$grand_total_cheque = 0;
								$grand_total_credit = 0;
								$grand_total_credit_paid = 0;
								$total_com_from_items = 0;
								foreach($all_member_list as $mem){
									$member_cls = new Member($mem);
									$total_com_amount = isset($con_arr[$mem]) ?$con_arr[$mem] : 0;
									$cash = isset($member_arr[$mem]['cash']) ? $member_arr[$mem]['cash'] : 0;
									$cheque = isset($member_arr[$mem]['cheque']) ? $member_arr[$mem]['cheque'] : 0;
									$credit_card = isset($member_arr[$mem]['cc']) ? $member_arr[$mem]['cc'] : 0;
									$credit = isset($member_arr[$mem]['mem_credit']) ? $member_arr[$mem]['mem_credit'] : 0;
									$credit_paid = isset($member_credit[$mem]) ? $member_credit[$mem] : 0;
									$mem_att =[];
									$member_coach_lbl = "";
									$member_service_lbl = "";
									$member_commission_lbl = "";
									if(isset($member_att[$mem])){

										$mem_att = $member_att[$mem];
										foreach($mem_att as $ind_att ){
											if(isset($commissions[$ind_att['item_id']])){
												$member_commission_lbl .= "<span class='span-block'>" .$commissions[$ind_att['item_id']]. "</span>";
											} else {
												$member_commission_lbl .= "<span class='span-block'>&nbsp;</span>";
											}
											$member_coach_lbl .= "<span class='span-block'>$ind_att[coach_name]</span>";
											$member_service_lbl .= "<span class='span-block'>$ind_att[item_code]</span>";
										}

									}


									echo "<tr>";
									echo "<td>".$member_cls->data()->lastname."</td>";
									echo "<td>$member_service_lbl</td>";
									echo "<td>$member_coach_lbl</td>";
									echo "<td>$member_commission_lbl</td>";
									for($i=0;$i< count($item_arr_sold); $i ++){
										$item_sold = $item_arr_sold[$i];
										$member_sold_per_item = isset($member_item_arr[$mem][$item_sold]) ? $member_item_arr[$mem][$item_sold] : 0;
										echo "<td> " . number_format($member_sold_per_item) . "</td>";
									}
									$grand_total_cash += $cash;
									$grand_total_cc += $credit_card;
									$grand_total_cheque += $cheque;
									$grand_total_credit += $credit;
									$grand_total_credit_paid += $credit_paid;
									$total_com_from_items += $total_com_amount;
									echo "<td class='text-right'>". number_format($cash,2)."</td>";
									echo "<td class='text-right'>". number_format($credit_card,2)."</td>";
									echo "<td class='text-right'>". number_format($cheque,2)."</td>";
									echo "<td class='text-right'>". number_format($credit,2)."</td>";
									echo "<td class='text-right'>". number_format($credit_paid,2)."</td>";
									echo "<td class='text-right'>". number_format($total_com_amount,2)."</td>";

									echo "</tr>";
								}
								echo "</tbody>";
								echo "</table>";

								echo "</div>";
							echo "<div class='row'>";
							echo "<div class='col-md-6'>";
							echo "<div class='list-group'>";
							echo "	<a class='list-group-item '><strong>Commission List</strong></a>";
							$total_commission = 0;
							if($coaches_commissions){
								foreach($coaches_commissions as $n => $c){
									$total_commission +=$c;
									echo "	<a class='list-group-item'><span class='badge'>".number_format($c,2). "</span> $n</a>";
								}
							} else {
								echo "	<a class='list-group-item'>No record</a>";
							}

							echo "</div>";
							echo "</div>";
							echo "</div>";
							echo "<p><strong>Total Cash: <span class='text-danger'>" .number_format($grand_total_cash,2) . "</span></strong></p>";
							echo "<p><strong>Total Credit Card: <span class='text-danger'>" .number_format($grand_total_cc,2) . "</span></strong></p>";
							echo "<p><strong>Total Cheque: <span class='text-danger'>" .number_format($grand_total_cheque,2) . "</span></strong></p>";
							echo "<p><strong>Total Credit: <span class='text-danger'>" .number_format($grand_total_credit,2) . "</span></strong></p>";
							echo "<p><strong>Total Credit Paid: <span class='text-danger'>" .number_format($grand_total_credit_paid,2) . "</span></strong></p>";
							echo "<p><strong>Total Commission of Coaches: <span class='text-danger'>" .number_format($total_commission,2) . "</span></strong></p>";
							echo "<p><strong>Total Commission from Items: <span class='text-danger'>" .number_format($total_com_from_items,2) . "</span></strong></p>";

							}

						?>

					</div>



				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function(){
			$('#txtDateFrom').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#txtDateFrom').datepicker('hide');
			});

			$('#txtDateTo').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#txtDateTo').datepicker('hide');

			});
		});

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>