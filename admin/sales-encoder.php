<?php
	include '../core/admininit.php';
	require_once '../includes/admin/page_head2.php';
?>
	<br><br>
	<div class="container">
	<?php

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
					/*$arr['deduction']
					$arr['deduction']['amount'] = $c->amount;
					$arr['deduction']['created'] = $c->created;*/
					$arr['deduction'][] = ['id' => $c->id,'amount'=> $c->amount,'created'=> $c->created];
				}
			}
			if($con_list){

				foreach($con_list as $c){

					/*
						$arr['consumable']['id'] = $c->id;
						$arr['consumable']['amount'] = $c->amount;
						$arr['consumable']['created'] = $c->created;
					*/

					$arr['consumable'][] = ['id' => $c->id, 'amount' => $c->amount, 'created' => $c->created];
				}

			}

			if($cash_list){
				foreach($cash_list as $c){
					$arr['cash'][] = ['created'=> $c->created,'id' => $c->id,'amount' => $c->amount,'date' =>date('m/d/Y',$c->created)];

				}
			}

			if($credit_list){
				foreach($credit_list as $c){
					$arr['credit'][] = [
						'id' =>$c->id,
						'created' => $c->created,
						'amount' =>$c->amount,
						'bank' =>$c->bank_name,
						'ref_number' =>$c->card_number,
						'approval_code' =>$c->approval_code,
						'trace_number' =>$c->trace_number,
						'ref_number' =>$c->card_number,
						'date' =>date('m/d',$c->date)
					];
				}
			}
			if($cheque_list){
				foreach($cheque_list as $c){
					$arr['cheque'][] = [
						'id' =>$c->id,
						'created' => $c->created,
						'amount' =>$c->amount,
						'bank' =>$c->bank,
						'ref_number' =>$c->check_number,
						'date' =>date('n/j/y',$c->payment_date)
					];
				}
			}

			if($bt_list){
				foreach($bt_list as $c){
					$arr['bt'][] = [
						'id' =>$c->id,
						'amount' =>$c->amount,
						'created' => $c->created,
						'bank' =>$c->bankfrom_name,
						'ref_number' =>$c->bankfrom_account_number,
						'date' =>date('n/j/y',$c->date)
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
					$arr['conf']['date'] = date('m/d/Y',$c->created);
				}
			}

			if($member_credit_list){
				foreach($member_credit_list as $c){
					$arr['mem_credit']['id'] = $c->id;
					$arr['mem_credit']['amount'] = $c->amount;
					$arr['mem_credit']['amount_paid'] = $c->amount_paid;
					$arr['mem_credit']['date'] =  $c->created;
				}
			}

			return json_encode($arr);

		}

		if(Input::exists()){

			$wh_detail = new Wh_order_details();

			$id= Input::get('order_id');
			$invoice= Input::get('invoice');
			$dr= Input::get('dr');
			$ir= Input::get('ir');

			if(is_numeric($id) && $id){
				$wh = new Wh_order($id);
				if(isset($wh->data()->payment_id) && $wh->data()->payment_id ){
					$details = $wh_detail->get_active('wh_order_details',['wh_orders_id','=',$id]);

					$sales = new Sales();
					$terminal = new Terminal();
					if($wh->data()->branch_id  == 27){
						$wh->data()->branch_id = 28;
					}
					$terminals = $terminal->getAllTerminal($wh->data()->branch_id);
					$terminal_id = $terminals[0]->id;
					$prevsales = $terminal->getCashier($terminal_id);
					$exists = $sales->getsinglesale($wh->data()->payment_id);

					if(!isset($exists->id)){
						$member = new Member($wh->data()->member_id);

						$prod = new Product();
						$total_trans = 0;
						foreach($details as $det){

							$sold_date = 0;
							if($det->approved_date){
								$sold_date = $det->approved_date;
							} else if ($det->is_scheduled){
								$sold_date = $det->is_scheduled;
							} else {
								$sold_date = $det->created;
							}

							$price = $prod->getPriceByPriceId($det->price_id);
							$adjusted_price = $det->price_adjustment + $price->price;
							$total_price = ($adjusted_price * $det->qty) + $det->member_adjustment;

							$ind_adj = ($det->price_adjustment * $det->qty);

							$sales->create(array(
								'terminal_id' => $terminal_id,
								'pref_inv' => '',
								'pref_ir' => '',
								'pref_dr' => '',
								'invoice' => $wh->data()->invoice,
								'dr' => $wh->data()->dr,
								'ir' => $wh->data()->pr,
								'item_id' => $det->item_id,
								'price_id' => $det->price_id,
								'qtys' => $det->qty,
								'discount' => 0,
								'store_discount' => 0,
								'adjustment' => $ind_adj,
								'member_adjustment' => $det->member_adjustment,
								'company_id' => $wh->data()->company_id,
								'cashier_id' => $prevsales->cashier_id,
								'sold_date' => $sold_date,
								'payment_id' => $wh->data()->payment_id,
								'member_id' => $wh->data()->member_id,
								'station_id' => 0,
								'warranty' =>  0,
								'sales_type' => $member->data()->salestype,
								'agent_id' =>  0
							));
							$sdate = $sold_date;
							$total_trans += $total_price;

						}

						$withPayment = json_decode(getPaymentsOrder($wh->data()->payment_id),true);

						if(count($withPayment)){
							echo "<p>With Payment</p>";
						} else {
							echo "<p>No Payment. Added to member credit</p>";
							$pcredit = new Member_credit();
							$pcredit->create(array(
								'amount' =>$total_trans,
								'is_active' => 1,
								'created' =>$sdate ,
								'modified' => $sdate,
								'payment_id' => $wh->data()->payment_id,
								'member_id' =>  $wh->data()->member_id
						));
						}

						echo "<p>Success.</p>";
					} else {
						echo "<p>Already in sales</p>";
					}
				} else {

					echo "<p>No Payment</p>";

					$details = $wh_detail->get_active('wh_order_details',['wh_orders_id','=',$id]);

					$sales = new Sales();
					$terminal = new Terminal();
					if($wh->data()->branch_id  == 27){
						$wh->data()->branch_id = 28;
					}
					$terminals = $terminal->getAllTerminal($wh->data()->branch_id);
					$terminal_id = $terminals[0]->id;
					$prevsales = $terminal->getCashier($terminal_id);


						$member = new Member($wh->data()->member_id);
						$prod = new Product();
						$total_trans = 0;
						$payment = new Payment();
						$payment->create(array(
							'created' => time(),
							'company_id' => $wh->data()->company_id,
							'is_active' => 1
						));

						$payment_lastid = $payment->getInsertedId();

						foreach($details as $det){

							$sold_date = 0;
							if($det->approved_date){
								$sold_date = $det->approved_date;
							} else if ($det->is_scheduled){
								$sold_date = $det->is_scheduled;
							} else {
								$sold_date = $det->created;
							}

							$price = $prod->getPriceByPriceId($det->price_id);
							$adjusted_price = $det->price_adjustment + $price->price;
							$total_price = ($adjusted_price * $det->qty) + $det->member_adjustment;

							$ind_adj = ($det->price_adjustment * $det->qty);
							$newsales = new Sales();
							$newsales->create(array(
								'terminal_id' => $terminal_id,
								'pref_inv' => '',
								'pref_ir' => '',
								'pref_dr' => '',
								'invoice' => $invoice,
								'dr' => $dr,
								'ir' =>$ir,
								'item_id' => $det->item_id,
								'price_id' => $det->price_id,
								'qtys' => $det->qty,
								'discount' => 0,
								'store_discount' => 0,
								'adjustment' => $ind_adj,
								'member_adjustment' => $det->member_adjustment,
								'company_id' => $wh->data()->company_id,
								'cashier_id' => $prevsales->cashier_id,
								'sold_date' => $sold_date,
								'payment_id' => $payment_lastid,
								'member_id' => $wh->data()->member_id,
								'station_id' => 0,
								'warranty' =>  0,
								'sales_type' => $member->data()->salestype,
								'agent_id' =>  0
							));
							$sdate = $sold_date;

							$total_trans += $total_price;

						}




							echo "<p>No Payment. Added to member credit</p>";


							$pcredit = new Member_credit();
							$pcredit->create(array(
								'amount' =>$total_trans,
								'is_active' => 1,
								'created' =>$sdate ,
								'modified' => $sdate,
								'payment_id' => $payment_lastid,
								'member_id' =>  $wh->data()->member_id
							));

						echo "<p>Success.</p>";

						$wh->update(['invoice' => $invoice, 'dr' => $dr,'pr' => $ir],$id);





					/*
					$memcred->update(
						array(
							'amount_paid'=>$amt_paid,
							'status'=>$status,
							'json_payment'=>$finalarr,
							'modified' => $timenow,
							'user_id' => $user->data()->id,
							'ret_msg' => ''
						),$member_credit_id); */

				}

			} else {
				echo "<p>Invalid Order ID</p>";
			}

		}

	?>
		<form action="" method="POST">
		<div class="row">

			<div class="col-md-3">
				<input type="text" name='order_id' placeholder="ORDER ID">
			</div>
			<div class="col-md-3">
				<input type="text" name='invoice' placeholder="INVOICE">
			</div>
			<div class="col-md-3">
				<input type="text" name='dr' placeholder="DR">
			</div>
			<div class="col-md-3">
				<input type="text" name='ir' placeholder="IR">
			</div>

			<div class="col-md-3">
				<input type="submit" value='Submit' name='btnSubmit'>
			</div>
		</div>
		</form>
	</div>
	<script>

	</script>
<?php
	require_once '../includes/admin/page_tail2.php';
?>