<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('sales_crud')) {
		// redirect to denied page
		Redirect::to(1);
	}
	if(isset($_GET['id'])) {
		$payment_id = $_GET['id'];
	} else {
		Redirect::to(1);
	}

	if (Input::exists()){
		$terminal = new Terminal();
		$terminal_mon = new Terminal_mon();
		$now = time();
		if(isset($_POST['btnCashRp'])){
			$pcash = new Cash();
			if(Input::get('cash_date')){
				$now = strtotime(Input::get('cash_date'));
			}
			$pcash->create(array(
				'amount' =>Input::get('cashrp'),
				'is_active' => 1,
				'created' => $now,
				'modified' => $now,
				'payment_id' => Input::get('cashpid')
			));

			//terminal money monitoring
			$total_amount = Input::get('cashrp');
			$pterminal = Input::get('payment_terminal');
			$pdr = Input::get('payment_dr');
			$pinv = Input::get('payment_inv');
			$pdr = ($pdr) ? 'Dr: '.$pdr:'';
			$pinv = ($pinv) ? 'Inv: '.$pinv:'';

			$prevamount = $terminal->getTAmount($pterminal,1);
			$prevamount = ($prevamount->t_amount) ? $prevamount->t_amount:0;
			$to_amount = $total_amount + $prevamount;

			$terminal->update(array(
				't_amount' => $to_amount
			),$pterminal);
			$terminal_mon->create( array(
				'terminal_id' => $pterminal,
				'user_id' => $user->data()->id,
				'from_amount' =>$prevamount,
				'amount' =>$total_amount,
				'to_amount'=>$to_amount,
				'status' => 1,
				'remarks' => "POS $pinv $pdr",
				'is_active' => 1,
				'company_id' => $user->data()->company_id,
				'p_type' => 1,
				'created' => time()
			));

			Log::addLog(
				$user->data()->id,
				$user->data()->company_id,
				"ADD CASH PAYMENT $pinv $pdr",
				'ajax_deletepermanent.php'
			);

			$pid = Encryption::encrypt_decrypt('encrypt',  Input::get('cashpid'));
			Session::flash('flash','Successfully added payment');
			Redirect::to('sales_crud.php?id='.$pid);
		}
		if(isset($_POST['btnMdRp'])){
			$payment_member_deduction =Input::get('mdrp');
			$member_deduction_remarks = Input::get('deduction_remarks');
			$pdeduct = new Deduction();
			$member_deduction_remarks = ($member_deduction_remarks) ? $member_deduction_remarks : '';
			if(Input::get('deduction_date')){
				$now = strtotime(Input::get('deduction_date'));
			}
			$pdeduct->create(array(
				'amount' =>$payment_member_deduction,
				'is_active' => 1,
				'created' => $now,
				'remarks' => $member_deduction_remarks,
				'payment_id' => Input::get('mdpid'),
				'member_id' => Input::get('member_deduction')
			));

			Log::addLog(
				$user->data()->id,
				$user->data()->company_id,
				"ADD DEDUCTION PAYMENT $pinv $pdr",
				'ajax_deletepermanent.php'
			);

			$pid = Encryption::encrypt_decrypt('encrypt',  Input::get('mdpid'));
			Session::flash('flash','Successfully added payment');
			Redirect::to('sales_crud.php?id='.$pid);
		}
		if(isset($_POST['btnMcRp'])){

			$pmem = new Member_credit();
			if(Input::get('credit_member_date')){
				$now = strtotime(Input::get('credit_member_date'));
			}
			$pmem->create(array(
				'amount' =>Input::get('mcrp'),
				'is_active' => 1,
				'created' => $now,
				'modified' => $now,
				'payment_id' => Input::get('mcpid'),
				'member_id' => Input::get('credit_member')
			));

			Log::addLog(
				$user->data()->id,
				$user->data()->company_id,
				"ADD MEMBER CREDIT PAYMENT ".Input::get('mcpid'),
				'ajax_deletepermanent.php'
			);

			$pid = Encryption::encrypt_decrypt('encrypt',  Input::get('mcpid'));
			Session::flash('flash','Successfully added payment');
			Redirect::to('sales_crud.php?id='.$pid);

		}

		if(isset($_POST['btnCreditCardRp'])){
			$credit = new Credit();
			$dt = strtotime(Input::get('billing_date'));
			if(Input::get('billing_override_date')){
				$now = strtotime(Input::get('billing_override_date'));
			}
			$credit->create(array(
				'card_number' =>Input::get('billing_cardnumber'),
				'amount' => Input::get('billing_amount'),
				'trace_number' => Input::get('billing_trace_number'),
				'approval_code' => Input::get('billing_approval_code'),
				'card_type' => Input::get('billing_card_type'),
				'date' => $dt,
				'bank_name'=> Input::get('billing_bankname'),
				'lastname'=> Input::get('billing_lastname'),
				'firstname'=>Input::get('billing_firstname'),
				'middlename'=>Input::get('billing_middlename'),
				'company'=>Input::get('billing_company'),
				'address'=>Input::get('billing_address'),
				'zip' => Input::get('billing_postal'),
				'contacts' => Input::get('billing_phone'),
				'email' => Input::get('billing_email'),
				'remarks' => Input::get('billing_remarks'),
				'is_active' => 1,
				'created' => $now,
				'modified' => $now,
				'payment_id' =>  Input::get('creditpid')
			));
			//terminal money monitoring
			$total_amount =  Input::get('billing_amount');
			$pterminal = Input::get('payment_terminal');
			$pdr = Input::get('payment_dr');
			$pinv = Input::get('payment_inv');
			$pdr = ($pdr) ? 'Dr: '.$pdr:'';
			$pinv = ($pinv) ? 'Inv: '.$pinv:'';

			$prevamount = $terminal->getTAmount($pterminal,2);
			$prevamount = ($prevamount->t_amount_cc) ? $prevamount->t_amount_cc:0;
			$to_amount = $total_amount + $prevamount;

			$terminal->update(array(
				't_amount_cc' => $to_amount
			),$pterminal);
			$terminal_mon->create( array(
				'terminal_id' => $pterminal,
				'user_id' => $user->data()->id,
				'from_amount' =>$prevamount,
				'amount' =>$total_amount,
				'to_amount'=>$to_amount,
				'status' => 1,
				'remarks' => "POS $pinv $pdr",
				'is_active' => 1,
				'company_id' => $user->data()->company_id,
				'p_type' => 2,
				'created' => time()
			));

			Log::addLog(
				$user->data()->id,
				$user->data()->company_id,
				"ADD CREDIT CARD PAYMENT $pinv $pdr",
				'ajax_deletepermanent.php'
			);

			$pid = Encryption::encrypt_decrypt('encrypt',  Input::get('creditpid'));
			Session::flash('flash','Successfully added payment');
			Redirect::to('sales_crud.php?id='.$pid);
		}
		if(isset($_POST['btnBtRp'])){
			$bank_transfer = new Bank_transfer();
			if(Input::get('bt_date')){
				$now = strtotime(Input::get('bt_date'));
			}
			$bank_transfer->create(array(
				'bankfrom_account_number' => Input::get('bankfrom_account_number'),
				'amount' => Input::get('bt_amount'),
				'bankfrom_name'=>Input::get('bankfrom_name'),
				'bankto_account_number' => Input::get('bt_bankto_account_number'),
				'bankto_name' => Input::get('bt_bankto_name'),
				'lastname'=>Input::get('bt_lastname'),
				'firstname'=>Input::get('bt_firstname'),
				'middlename'=>Input::get('bt_middlename'),
				'company'=>Input::get('bt_company'),
				'address'=>Input::get('bt_address'),
				'zip' => Input::get('bt_postal'),
				'contacts' => Input::get('bt_phone'),
				'is_active' => 1,
				'created' => $now,
				'modified' => $now,
				'payment_id' => Input::get('btpid')
			));
			//terminal money monitoring
			$total_amount = Input::get('bt_amount');
			$pterminal = Input::get('payment_terminal');
			$pdr = Input::get('payment_dr');
			$pinv = Input::get('payment_inv');
			$pdr = ($pdr) ? 'Dr: '.$pdr:'';
			$pinv = ($pinv) ? 'Inv: '.$pinv:'';

			$prevamount = $terminal->getTAmount($pterminal,4);
			$prevamount = ($prevamount->t_amount_bt) ? $prevamount->t_amount_bt:0;
			$to_amount = $total_amount + $prevamount;

			$terminal->update(array(
				't_amount_bt' => $to_amount
			),$pterminal);
			$terminal_mon->create( array(
				'terminal_id' => $pterminal,
				'user_id' => $user->data()->id,
				'from_amount' =>$prevamount,
				'amount' =>$total_amount,
				'to_amount'=>$to_amount,
				'status' => 1,
				'remarks' => "POS $pinv $pdr",
				'is_active' => 1,
				'company_id' => $user->data()->company_id,
				'p_type' => 4,
				'created' => time()
			));

			Log::addLog(
				$user->data()->id,
				$user->data()->company_id,
				"ADD BANK TRANSFER PAYMENT $pinv $pdr",
				'ajax_deletepermanent.php'
			);

			$pid = Encryption::encrypt_decrypt('encrypt',  Input::get('btpid'));
			Session::flash('flash','Successfully added payment');
			Redirect::to('sales_crud.php?id='.$pid);
		}
		if(isset($_POST['btnChequeRp'])){
			if(Input::get('ch_override_date')){
				$now = strtotime(Input::get('ch_override_date'));
			}
			$cheque = new Cheque();
			$cheque->create(array(
				'check_number' => Input::get('ch_number'),
				'amount' => Input::get('ch_amount'),
				'bank'=>Input::get('ch_bankname'),
				'payment_date' => strtotime(Input::get('ch_date')),
				'lastname'=>Input::get('ch_lastname'),
				'firstname'=>Input::get('ch_firstname'),
				'middlename'=>Input::get('ch_middlename'),
				'contacts' => Input::get('ch_phone'),
				'is_active' => 1,
				'created' => $now,
				'modified' => $now,
				'payment_id' => Input::get('chequepid')
			));

			//terminal money monitoring
			$total_amount = Input::get('ch_amount');
			$pterminal = Input::get('payment_terminal');
			$pdr = Input::get('payment_dr');
			$pinv = Input::get('payment_inv');
			$pdr = ($pdr) ? 'Dr: '.$pdr:'';
			$pinv = ($pinv) ? 'Inv: '.$pinv:'';

			$prevamount = $terminal->getTAmount($pterminal,3);
			$prevamount = ($prevamount->t_amount_ch) ? $prevamount->t_amount_ch:0;
			$to_amount = $total_amount + $prevamount;

			$terminal->update(array(
				't_amount_ch' => $to_amount
			),$pterminal);
			$terminal_mon->create( array(
				'terminal_id' => $pterminal,
				'user_id' => $user->data()->id,
				'from_amount' =>$prevamount,
				'amount' =>$total_amount,
				'to_amount'=>$to_amount,
				'status' => 1,
				'remarks' => "POS $pinv $pdr",
				'is_active' => 1,
				'company_id' => $user->data()->company_id,
				'p_type' => 3,
				'created' => time()
			));

			Log::addLog(
				$user->data()->id,
				$user->data()->company_id,
				"ADD CHEQUE PAYMENT $pinv $pdr",
				'ajax_deletepermanent.php'
			);


			$pid = Encryption::encrypt_decrypt('encrypt',  Input::get('chequepid'));
			Session::flash('flash','Successfully added payment');
			Redirect::to('sales_crud.php?id='.$pid);
		}

		if(isset($_POST['btnConAmountRp'])){

			$pcon = new Payment_consumable();
			$payment_con = Input::get('con_amount');
			if(Input::get('con_date')){
				$now = strtotime(Input::get('con_date'));
			}
			$pcon->create(array(
				'amount' =>Input::get('con_amount'),
				'is_active' => 1,
				'created' => $now,
				'modified' => $now,
				'payment_id' =>  Input::get('conamountpid'),
				'member_id' => Input::get('con_member')
			));


			$mem = new Member();
			$mycon = $mem->getMyConsumableAmount(Input::get('con_member'));
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

			Log::addLog(
				$user->data()->id,
				$user->data()->company_id,
				"ADD CONSUMABLE PAYMENT $pinv $pdr",
				'ajax_deletepermanent.php'
			);

			$pid = Encryption::encrypt_decrypt('encrypt',  Input::get('conamountpid'));
			Session::flash('flash','Successfully added payment');
			Redirect::to('sales_crud.php?id='.$pid);
		}
		if(isset($_POST['btnConFreebiesRp'])){

			$pcon = new Payment_consumable_freebies();
			$payment_con_freebies = Input::get('con_amount_freebies');
			if(Input::get('con_amount_date')){
				$now = strtotime(Input::get('con_amount_date'));
			}
			$pcon->create(array(
				'amount' =>Input::get('con_amount_freebies'),
				'is_active' => 1,
				'created' => $now,
				'modified' => $now,
				'payment_id' =>  Input::get('confreebiespid'),
				'member_id' => Input::get('con_member_freebies')
			));


			$mem = new Member();
			$mycon = $mem->getMyConsumableFreebies(Input::get('con_member_freebies'));
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

			Log::addLog(
				$user->data()->id,
				$user->data()->company_id,
				"ADD CONSUMABLE FREEBIE PAYMENT $pinv $pdr",
				'ajax_deletepermanent.php'
			);

			$pid = Encryption::encrypt_decrypt('encrypt',  Input::get('confreebiespid'));
			Session::flash('flash','Successfully added payment');
			Redirect::to('sales_crud.php?id='.$pid);
		}
		if (isset($_POST['btnAddSalesRp'])){


			if(Input::get('s_item_type') == -1){
				// deduct inventory
				$inventory = new Inventory();
				$rack = new Rack();
				$inv_mon = new Inventory_monitoring();
				if(Input::get('salesterminal')){
					$myterminal = new Terminal(Input::get('salesterminal'));
					$mybranch = $myterminal->data()->branch_id;
				} else {
					$myterminal =0;
					$mybranch = $user->data()->branch_id;
				}

				$rack_id = $rack->getRackForSelling($user->data()->branch_id);
				$curinventory = $inventory->getQty(Input::get('itemcode'),$mybranch,$rack_id->id);
				if(isset( $curinventory->qty) && $curinventory->qty >= Input::get('s_qty') ){
					$newqty = $curinventory->qty - Input::get('s_qty');
					$inventory->update(array(
						'qty' => $newqty
					), $curinventory->id);
					$monlabelinv ='';
					$monlabeldr ='';
					if(Input::get('salesinvoice')){
						$monlabelinv = "Invoice ".Input::get('salesinvoice');
					}
					if(Input::get('salesdr')){
						$monlabeldr = "Dr ".Input::get('salesdr');
					}

					$inv_mon->create(array(
						'item_id' => Input::get('itemcode'),
						'rack_id' => $rack_id->id,
						'branch_id' => $mybranch,
						'page' => 'sales_crud.php',
						'action' => 'Update',
						'prev_qty' => $curinventory->qty,
						'qty_di' => 2,
						'qty' => Input::get('s_qty'),
						'new_qty' => $newqty,
						'created' => time(),
						'user_id' => $user->data()->id,
						'remarks' => 'Deduct inventory upon editing on Sales Crud, Payment ID: ' . Input::get('salespid') . ' ' .$monlabelinv . " " . $monlabeldr,
						'is_active' => 1,
						'company_id' => $user->data()->company_id
					));
					$newitem = new Product();
					$myprice = $newitem->getPrice(Input::get('itemcode'));
					$newsales = new Sales();
					$newsales->create(array(
						'payment_id' => Input::get('salespid'),
						'invoice' => Input::get('salesinvoice'),
						'dr' => Input::get('salesdr'),
						'item_id' => Input::get('itemcode'),
						'terminal_id' => Input::get('salesterminal'),
						'member_id' => Input::get('salesmember'),
						'station_id' => Input::get('salesstation'),
						'cashier_id' => Input::get('salescashier'),
						'sold_date' => Input::get('salessolddate'),
						'qtys' => Input::get('s_qty'),
						'price_id' => $myprice->id,
						'discount' => Input::get('s_discount'),
						'company_id' => $user->data()->company_id,
						'is_active' => 1
					));

					$pid = Encryption::encrypt_decrypt('encrypt',  Input::get('salespid'));
					Session::flash('flash','Successfully added sales');
					Redirect::to('sales_crud.php?id='.$pid);
				} else {
					$pid = Encryption::encrypt_decrypt('encrypt',  Input::get('salespid'));
					Session::flash('flash','Failed to add an Item. Not enough stock.');
					Redirect::to('sales_crud.php?id='.$pid);
				}
			} else {
				$newitem = new Product();
				$myprice = $newitem->getPrice(Input::get('itemcode'));
				$newsales = new Sales();
				$newsales->create(array(
					'payment_id' => Input::get('salespid'),
					'invoice' => Input::get('salesinvoice'),
					'dr' => Input::get('salesdr'),
					'item_id' => Input::get('itemcode'),
					'terminal_id' => Input::get('salesterminal'),
					'member_id' => Input::get('salesmember'),
					'station_id' => Input::get('salesstation'),
					'cashier_id' => Input::get('salescashier'),
					'sold_date' => Input::get('salessolddate'),
					'qtys' => Input::get('s_qty'),
					'price_id' => $myprice->id,
					'discount' => Input::get('s_discount'),
					'company_id' => $user->data()->company_id,
					'is_active' => 1
				));
				$pid = Encryption::encrypt_decrypt('encrypt',  Input::get('salespid'));
				Session::flash('flash','Successfully added sales');
				Redirect::to('sales_crud.php?id='.$pid);
			}

		}
		if(isset($_POST['btnSaveMember'])){
			$sales_id = Input::get('sales_id');
			$sss = new Sales();
			$mem_id = Input::get('mem_id');
			$pid = Encryption::encrypt_decrypt('encrypt',  Input::get('memberpid'));
			$sss->update(array('member_id'=>$mem_id),$sales_id);
			Session::flash('flash','Successfully updated member');
			Redirect::to('sales_crud.php?id='.$pid);
		}
		if(isset($_POST['btnSaveStation'])){
			$sales_id = Input::get('sales_id');
			$sss = new Sales();
			$stat_id = Input::get('stat_id');
			$pid = Encryption::encrypt_decrypt('encrypt',  Input::get('stationpid'));
			$sss->update(array('station_id'=>$stat_id),$sales_id);
			Session::flash('flash','Successfully updated station');
			Redirect::to('sales_crud.php?id='.$pid);
		}
	}





?>

	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					Sales Utilities
				</h1>
			</div>
			<?php

				if(Session::exists('flash')){
					echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('flash')."</div> <br>";
				}
			?>
			<div class="row">
				<div class="col-md-12">
						<?php 
							$payment_id = Encryption::encrypt_decrypt('decrypt', $payment_id);
							$edit_sales = new Sales();
							if(is_numeric($payment_id)){
								$edit_sales = $edit_sales->salesTransactionBaseOnPaymentId($payment_id,1);
								$indsales = $edit_sales[0];
							}



							?>
							<?php
								$salesinc = new Sales();
								/*$inconsistent = $salesinc->getInconsistentData($user->data()->company_id,$user->data()->branch_id);
								if($inconsistent){
									echo "<div class='well'>";
									echo "<p class='text-danger'><strong>You have unmatched sales total and payment total.</strong></p>";
									echo "<hr>";
									foreach($inconsistent as $incon){
										$invlabel='';
										$drlabel ='';
										if($incon->invoice){
											$invlabel="Invoice#".$incon->invoice;
										}
										if($incon->dr){
											$drlabel="Dr#".$incon->dr;
										}
										$alltotal = $incon->cashamount + $incon->chequeamount + $incon->btamount + $incon->ccamount + $incon->mcamount + $incon->pcamount+ $incon->pcfamount;
										$alltotal = number_format($alltotal,2);
										echo "<p>$invlabel $drlabel Sales Total=".$incon->ttotal.", Payment Total=$alltotal </p>";
										echo "<p><small>(Cash=<span class='text-danger'>$incon->cashamount</span>, Cheque=<span class='text-danger'>$incon->chequeamount</span>, Credit Card=<span class='text-danger'>$incon->ccamount</span>, Bank Transfer=<span class='text-danger'>$incon->btamount</span>, Member Credit=<span class='text-danger'>$incon->mcamount</span>, Consumable=<span class='text-danger'>$incon->pcamount</span>, Consumable freebies=<span class='text-danger'>$incon->pcfamount</span>)</small></p>";
										echo "<hr>";
									}
									echo "</div>";
								} */
							?>
							<fieldset>
							<div class="form-group">
								<label class="col-md-4 control-label" for="invoice"><?php echo INVOICE_LABEL; ?></label>
								<div class="col-md-8">
									<input id="invoice" name="invoice" placeholder="" class="form-control input-md" type="text" value='<?php echo $indsales->invoice; ?>' >
									<span class="help-block"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="dr"><?php echo DR_LABEL; ?></label>
								<div class="col-md-8">
									<input id="dr" name="dr" placeholder="r" class="form-control input-md" type="text" value='<?php echo $indsales->dr; ?>'>
									<span class="help-block"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="ir"><?php echo PR_LABEL; ?></label>
								<div class="col-md-8">
									<input id="ir" name="ir" placeholder="" class="form-control input-md" type="text" value='<?php echo $indsales->ir; ?>'>
									<span class="help-block"></span>
								</div>
							</div>
								<?php if(Configuration::getValue('has_sv') == 1){
									?>
									<div class="form-group">
										<label class="col-md-4 control-label" for="sv">SV</label>
										<div class="col-md-8">
											<input id="sv" name="sv" placeholder="" class="form-control input-md" type="text" value='<?php echo $indsales->sv; ?>'>
											<span class="help-block"></span>
										</div>
									</div>
									<?php
								}?>


								<div class="form-group">
								<label class="col-md-4 control-label" for="dt">Date</label>
								<div class="col-md-8">
									<input id="dt" name="dt" placeholder="Date" class="form-control input-md" type="text" value='<?php echo date('m/d/Y',$indsales->sold_date) ?>'>
									<span class="help-block">Date sold</span>
								</div>
							</div>

							<div class="form-group">
								<label class="col-md-4 control-label" for="button1id">Class</label>
								<div class="col-md-8">
									<select name="from_service" id="from_service" class='form-control'>
										<option value="0" <?php echo ($indsales->from_service == 0 && $indsales->is_service==0) ? 'selected' : ''; ?> >Main Sales</option>
										<option value="1" <?php echo ($indsales->from_service != 0 || $indsales->is_service !=0) ? 'selected' : ''; ?> >Service Sales</option>
									</select>
									<br>
								</div>

							</div>
								<div class="form-group">
									<label class="col-md-4 control-label" for="button1id">Sales type</label>
									<div class="col-md-8">
										<select name="main_sales_type" id="main_sales_type" class='form-control'>
											<?php
												$salesType_cls = new Sales_type();
												$allsts = $salesType_cls->get_active('salestypes',array('1','=','1'));
												foreach($allsts as $sts){
													$selected='';
													if($indsales->sales_type == $sts->id){
														$selected = 'selected';
													}
													echo "<option value='$sts->id' $selected>{$sts->name}</option>";
												}

											?>
										</select>
										<br>
									</div>

								</div>
								<div class="form-group">
									<label class="col-md-4 control-label" for="button1id">Addtl Remarks</label>
									<div class="col-md-8">
										<input id="addtl_remarks" name="addtl_remarks" placeholder="" class="form-control input-md" type="text" value='<?php echo $indsales->addtl_remarks; ?>'>
										<span class="help-block"></span>
									</div>

								</div>


							<div class="form-group">
								<label class="col-md-4 control-label" for="button1id"></label>
								<div class="col-md-8">
									<input type='button' data-payment-id='<?php echo  Encryption::encrypt_decrypt('encrypt', $payment_id); ?>' class='btn btn-success' name='btnSave' id='btnSave' value='SAVE'/>
								</div>
							</div>
						</fieldset>
					<hr />
							<table class="table" style='display:none;' >
								<thead>
									<tr>
										<th>Invoice</th>
										<th>Dr</th>
										<th>Item Code</th>
										<th>Qty</th>
										<th>Price</th>
										<th>Discount</th>
										<th>Total</th>
										<th>Sold To</th>
										<th>Station</th>
										<th>SalesType</th>
										<th></th>
									</tr>
								</thead>
								<tbody>

									<?php
										$drarr = [];
										$invarr = [];

									foreach ($edit_sales as $s) {
										$price = $s->price;
										$optitems = "";
										if($s->dr && !in_array($s->dr,$drarr)){
											$drarr[]=$s->dr;
										}
										if($s->invoice && !in_array($s->invoice,$invarr)){
											$invarr[]=$s->invoice;
										}
										$drlabel = ($s->dr) ? $s->dr : 'No Dr';
										$invoicelabel = ($s->invoice) ?  $s->invoice : 'No Invoice';
										?>
										<tr>
									<td style='border-top:1px solid #ccc;'>
										<?php echo $invoicelabel; ?>
									</td>
									<td  style='border-top:1px solid #ccc;'>
											<?php echo $drlabel; ?>
									</td>
										<td  style='border-top:1px solid #ccc;'>
											<?php

												echo $s->item_code;
											?>
											<small class='span-block text-danger'>
												<?php echo $s->description; ?>
											</small>
									</td  style='border-top:1px solid #ccc;'>
									<td  style='border-top:1px solid #ccc;'>
									<?php echo $s->qtys; ?>
									</td>
									<td  style='border-top:1px solid #ccc;'>
										<?php echo number_format($price,2); ?>
									</td>
									<td  style='border-top:1px solid #ccc;'>
									<?php echo number_format($s->discount,2); ?>
									</td>
									<td  style='border-top:1px solid #ccc;'>
									<?php
										$stotal = ($s->qtys*$price) - $s->discount;
										echo number_format($stotal,2);
									?>
									</td>
									<td  style='border-top:1px solid #ccc;'>
										<form action="" method='POST'>
											<input type="hidden" value='<?php echo $s->id; ?>' name='sales_id'>
											<input type="hidden" name='memberpid' value='<?php echo $payment_id; ?>' />
										<select name="mem_id" class='form-control select2_member'>

										<?php
											$memcls = new Member($s->member_id);
											echo "<option value='$s->member_id' $selected>{$memcls->data()->lastname}, {$memcls->data()->firstname}</option>";

										?>
										</select>
										<input type="submit" style='margin-top:3px' name='btnSaveMember' value='Save' class='btn btn-default'>
										</form>
									</td>
									<td  style='border-top:1px solid #ccc;'>
										<form action="" method='POST'>
											<input type="hidden" value='<?php echo $s->id; ?>' name='sales_id'>
											<input type="hidden" name='stationpid' value='<?php echo $payment_id; ?>' />
													<select name="stat_id" class='form-control select2_station'>
														<option value=""></option>

														<?php
															$stationcls = new Station();
															$allstations = $memcls->get_active('stations',array('member_id','=',$s->member_id));
															foreach($allstations as $stat){
																$selected='';
																if($s->station_id == $stat->id){
																	$selected = 'selected';
																}
																echo "<option value='$stat->id' $selected>{$stat->name}</option>";
															}
														?>
													</select>

													<input type="submit" style='margin-top:3px' name='btnSaveStation' value='Save' class='btn btn-default'>

										</form>
									</td>
											<td  style='border-top:1px solid #ccc;'>
												<form action="" method='POST'>
													<input type="hidden" value='<?php echo $s->id; ?>' name='sales_id'>
													<input type="hidden" name='stationpid' value='<?php echo $payment_id; ?>' />
													<select name="salesType_id" class='form-control select2_station'>
														<option value=""></option>

														<?php
														$stationcls = new Station();
														$allstations = $memcls->get_active('stations',array('member_id','=',$s->member_id));
														foreach($allstations as $stat){
															$selected='';
															if($s->station_id == $stat->id){
																$selected = 'selected';
															}
															echo "<option value='$stat->id' $selected>{$stat->name}</option>";
														}
														?>
													</select>

													<input type="submit" style='margin-top:3px' name='btnSaveStation' value='Save' class='btn btn-default'>

												</form>
											</td>
									<td  style='border-top:1px solid #ccc;'>

										<?php echo "<button class='btn btn-danger btnDelSales' data-id='".Encryption::encrypt_decrypt('encrypt',$s->id)."'><i class='fa fa-close'></i></button>"; ?>
									</td>
									</tr>
									<?php	
									}
									?>
								</tbody>
							</table>

					<?php
						$drcount = count($drarr);
						$drlist = "";
						if($drcount > 1){
							foreach($drarr as $drind){
								$drlist .= $drind .",";
							}
							$drlist = rtrim($drlist,',');
						}
						$invcount = count($invarr);
						$invlist = "";
						if($invcount > 1){

							foreach($invarr as $invind){
								$invlist .= $invind .",";
							}
							$invlist = rtrim($invlist,',');
						}
					?>
					<br>
					<div class='text-right' style='display:none;'>
						<button id='btnAddSales' class='btn btn-default'><i class='fa fa-plus'></i> Sales</button>
					</div>
				</div>

			</div>
			<hr />
			<h1>Payments</h1>
			<?php
				$cash = new Cash();
				$cashp = $cash->get_active("cash",array('payment_id','=',$payment_id));

				if ($cashp){
					?>
					<div class="panel panel-default">
						<div class="panel-heading"></div>
						<div class="panel-body">
					<?php
					echo "<h3>Cash</h3>";
					echo "<table class='table'>";
					echo "<tr><th>Date</th><th>Amount</th><th></th></tr>";
					foreach($cashp as $c){
						echo "<tr><td>".escape(date('m/d/Y',$c->created))."</td><td>".escape($c->amount)."</td><td><button data-amount='".$c->amount."'data-paymentdr='". $indsales->dr."' data-paymentinv='". $indsales->invoice."' data-terminal_id='". $indsales->terminal_id."' class='btn btn-default btnDelCash' data-id='".Encryption::encrypt_decrypt('encrypt',$c->id)."'><i class='fa fa-close'></i></button></td></tr>";
					}
					echo "</table>";
						?>
						</div>
					</div>
					<?php
				}
				$cheque = new Cheque();
				//$chequep = $cheque->getChequeBaseOnPayment($payment_id);
				$chequep = $cheque->get_active("cheque",array('payment_id','=',$payment_id));

				if ($chequep){
					?>
					<div class="panel panel-default">
					<div class="panel-heading"></div>
					<div class="panel-body">
					<?php
					echo "<h3>Cheque</h3>";
					echo "<table class='table'>";
					echo "<tr><th>Payment Date</th><th>Check number</th><th>Bank</th><th>Name</th><th>Amount</th><th>Status</th><th></th></tr>";

					foreach($chequep as $c){
						$checkStatus = $c->status;
						$checkDate= strtotime(date('m/d/Y',$c->payment_date));
						$datenow = strtotime(date('m/d/Y'));

						if ($checkStatus == 1){
							if($checkDate > $datenow){
								$lablecheck="<span class='text-danger'>For collection <br> ".date('m/d/Y',$checkDate)."</span>";
							} else {
								$lablecheck="<span class='text-danger'>Collected</span>";
							}
						} else if ($checkStatus == 3){
							$lablecheck="<span class='text-danger'>Bounce</span>";
						}
						echo "<tr><td>".escape(date('m/d/Y',$c->payment_date))."</td><td>".escape($c->check_number)."</td><td>".escape($c->bank)."</td><td>".escape($c->lastname . ", " . $c->firstname . " " . $c->middlename)."</td><td>".escape($c->amount)."</td><td>$lablecheck</td><td><button data-amount='".$c->amount."'data-paymentdr='". $indsales->dr."' data-paymentinv='". $indsales->invoice."' data-terminal_id='". $indsales->terminal_id."'  class='btn btn-default btnDelCheque' data-id='".Encryption::encrypt_decrypt('encrypt',$c->id)."'><i class='fa fa-close'></i></button></td></tr>";
					}
					echo "</table>";
					?>
					</div>
					</div>
				<?php
				}
				$bt = new Bank_transfer();
				$btp = $bt->get_active("bank_transfer",array('payment_id','=',$payment_id));

				if ($btp){
					?>
					<div class="panel panel-default">
					<div class="panel-heading"></div>
					<div class="panel-body">
					<?php
					echo "<h3>Bank Transfer</h3>";
					echo "<table class='table'>";
					echo "<tr><th>Date</th><th>Account Number</th><th>Bank</th><th>Name</th><th>Amount</th><th></th></tr>";
					foreach($btp as $c){
						echo "<tr><td>".escape(date('m/d/Y',$c->created))."</td><td>".escape($c->bankfrom_account_number)."</td><td>".escape($c->bankfrom_name)."</td><td>".escape($c->lastname . ", " . $c->firstname . " " . $c->middlename)."</td><td>".escape($c->amount)."</td><td><button data-amount='".$c->amount."'data-paymentdr='". $indsales->dr."' data-paymentinv='". $indsales->invoice."' data-terminal_id='". $indsales->terminal_id."'  class='btn btn-default btnDelBt' data-id='".Encryption::encrypt_decrypt('encrypt',$c->id)."'><i class='fa fa-close'></i></button></td></tr>";
					}
					echo "</table>";
					?>
					</div>
					</div>
				<?php
				}
				$cc = new Credit();
				$ccp = $cc->get_active("credit_card",array('payment_id','=',$payment_id));
				if ($ccp){
					?>
					<div class="panel panel-default">
					<div class="panel-heading"></div>
					<div class="panel-body">
					<?php
					echo "<h3>Credit Card</h3>";
					echo "<table class='table'>";
					echo "<tr><th>Date</th><th>Card Number</th><th>Bank</th><th>Name</th><th>Amount</th><th></th></tr>";
					foreach($ccp as $c){
						echo "<tr><td>".escape(date('m/d/Y',$c->created))."</td><td>".escape($c->card_number)."</td><td>".escape($c->bank_name)."</td><td>".escape($c->lastname . ", " . $c->firstname . " " . $c->middlename)."</td><td>".escape($c->amount)."</td><td><button data-amount='".$c->amount."'data-paymentdr='". $indsales->dr."' data-paymentinv='". $indsales->invoice."' data-terminal_id='". $indsales->terminal_id."'  class='btn btn-default btnDelCredit' data-id='".Encryption::encrypt_decrypt('encrypt',$c->id)."'><i class='fa fa-close'></i></button></td></tr>";
					}
					echo "</table>";
					?>

					</div>
					</div>
				<?php
				}
				$pc = new Payment_consumable();
				$pcp = $pc->get_active("payment_consumable",array('payment_id','=',$payment_id));
				if ($pcp){
					echo "<h3>Consumable Amount</h3>";
					echo "<table class='table'>";
					echo "<tr><th>Date</th><th>Amount</th></tr>";
					foreach($pcp as $c){
						echo "<tr><td>".escape(date('m/d/Y',$c->created))."</td><td>".escape($c->amount)."</td><td><button class='btn btn-default btnDelConAmount' data-id='".Encryption::encrypt_decrypt('encrypt',$c->id)."'><i class='fa fa-close'></i></button></td></tr>";
					}
					echo "</table>";
				}
				$pcf = new Payment_consumable_freebies();
				$pcfp = $pcf->get_active("payment_consumable_freebies",array('payment_id','=',$payment_id));
				if ($pcfp){
					echo "<h3>Consumable Amount for Freebies</h3>";
					echo "<table class='table'>";
					echo "<tr><th>Date</th><th>Amount</th></tr>";
					foreach($pcfp as $c){
						echo "<tr><td>".escape(date('m/d/Y',$c->created))."</td><td>".escape($c->amount)."</td><td><button class='btn btn-default btnDelConFreebies' data-id='".Encryption::encrypt_decrypt('encrypt',$c->id)."'><i class='fa fa-close'></i></button></td></tr>";
					}
					echo "</table>";
				}
				$pmemcredit = new Member_credit();
				$pmemcreditp = $pmemcredit->get_active("member_credit",array('payment_id','=',$payment_id));
				if ($pmemcreditp){
					echo "<h3>Member credit</h3>";
					echo "<table class='table'>";
					echo "<tr><th>Date</th><th>Amount</th></tr>";
					foreach($pmemcreditp as $c){
						echo "<tr><td>".escape(date('m/d/Y',$c->created))."</td><td>".escape($c->amount)."</td><td><button class='btn btn-default btnDelMemCredit' data-id='".Encryption::encrypt_decrypt('encrypt',$c->id)."'><i class='fa fa-close'></i></button></td></tr>";
					}
					echo "</table>";
				}
				$pmemdeduct = new Deduction();
				$pmemdeducts = $pmemdeduct->get_active("deductions",array('payment_id','=',$payment_id));
				if ($pmemdeducts){
					echo "<h3>Deduction</h3>";
					echo "<table class='table'>";
					echo "<tr><th>Date</th><th>Remarks</th><th>Amount</th><th></th></tr>";
					foreach($pmemdeducts as $c){
						echo "<tr><td>".escape(date('m/d/Y',$c->created))."</td><td>".escape($c->remarks)."</td><td>".escape($c->amount)."</td><td><button class='btn btn-default btnDelDeduction' data-id='".Encryption::encrypt_decrypt('encrypt',$c->id)."'><i class='fa fa-close'></i></button></td></tr>";
					}
					echo "</table>";
				}
			?>
			<hr />
			<div class='text-right'>
				<button id='btnCash' class='btn btn-default'><i class='fa fa-plus'></i> Cash</button>
				<button id='btnCreditCard' class='btn btn-default'><i class='fa fa-plus'></i> Credit Card</button>
				<button id='btnBankTransfer' class='btn btn-default'><i class='fa fa-plus'></i> Bank Transfer</button>
				<button id='btnCheque' class='btn btn-default'><i class='fa fa-plus'></i> Cheque</button>
				<button id='btnConAmount' class='btn btn-default'><i class='fa fa-plus'></i> Consumable Amount</button>
				<button id='btnConFreebies' class='btn btn-default'><i class='fa fa-plus'></i> Consumable Freebies</button>
				<button id='btnMemberCredit' class='btn btn-default'><i class='fa fa-plus'></i> Member Credit</button>
				<button id='btnMemberDeduction' class='btn btn-default'><i class='fa fa-plus'></i> Member Deduction</button>

			</div>

			<!-- Modal Cash -->

			<div class="modal fade" id="modalPayment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog" style='width:70%;' >
					<div class="modal-content"  >
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title">Payment</h4>
						</div>
						<div class="modal-body" >
							<div id="forCash" style='display:none;'>
								<fieldset>
									<form action="" method="post">
										<input type="hidden" name='cashpid'value='<?php echo $payment_id; ?>' />

										<div class="form-group">
										<label class="col-md-3 control-label text-center" for="cashrp">Amount</label>
										<div class="col-md-9">
											<input id="cashrp" name="cashrp" required class="form-control input-md" type="text">
											<span class="help-block">Amount In Peso</span>
										</div>
										<div class="form-group">
											<label class="col-md-3 control-label text-center" for="cash_date">Override Date</label>
											<div class="col-md-9">
												<input id="cash_date" name="cash_date" required class="form-control input-md" type="text">
												<span class="help-block">mm/dd/yyyy (optional)</span>
											</div>
										</div>
									</div>
									<div class="form-group">

										<div class="col-md-12 text-right">
											<input type="hidden" name='payment_inv' value='<?php echo $indsales->invoice; ?>'>
											<input type="hidden" name='payment_dr' value='<?php echo $indsales->dr; ?>'>
											<input type="hidden" name='payment_terminal' value='<?php echo $indsales->terminal_id; ?>'>
											<input id="btnCashRp" name="btnCashRp" class="btn btn-default" type="submit">

										</div>
									</div>
									</form>
								</fieldset>
							</div>
							<div id="forMemberDeduction" style='display:none;'>
								<fieldset>
									<form action="" method="post">
										<input type="hidden" name='mdpid' value='<?php echo $payment_id; ?>' />
										<div class="form-group">
											<label class="col-md-3 control-label text-center" for="mdrp">Amount</label>
											<div class="col-md-9">
												<input id="mdrp" name="mdrp" required class="form-control input-md" type="text">
												<span class="help-block">Amount In Peso</span>
											</div>
										</div>
										<input class='form-control' type="hidden" value='<?php echo $indsales->member_id; ?>'  id='member_deduction'>
										<div class="form-group" style='display:none;'>
											<label class="col-md-3 control-label text-center" for="member_deduction">Member</label>
											<div class="col-md-9">

												<span class="help-block">Choose member name</span>
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-3 control-label text-center" for="deduction_remarks">Remarks</label>
											<div class="col-md-9">
												<input class='form-control' type="text" name='deduction_remarks' id='deduction_remarks'>
												<span class="help-block">Remarks</span>
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-3 control-label text-center" for="deduction_date">Override Date</label>
											<div class="col-md-9">
												<input id="deduction_date" name="deduction_date" required class="form-control input-md" type="text">
												<span class="help-block">mm/dd/yyyy (optional)</span>
											</div>
										</div>
										<div class="form-group">

											<div class="col-md-12 text-right">

												<input id="btnMdRp" name="btnMdRp" class="btn btn-default" type="submit">

											</div>
										</div>
									</form>
								</fieldset>
							</div>
							<div id="forMemberCredit" style='display:none;'>
								<fieldset>
									<form action="" method="post">
										<input type="hidden" name='mcpid'value='<?php echo $payment_id; ?>' />
										<div class="form-group">
											<label class="col-md-3 control-label text-center" for="mcrp">Amount</label>
											<div class="col-md-9">
												<input id="mcrp" name="mcrp" required class="form-control input-md" type="text">
												<span class="help-block">Amount In Peso</span>
											</div>
										</div>
										<input class='form-control' type="hidden" value='<?php echo $indsales->member_id; ?>'  name='credit_member' id='credit_member'>
										<div class="form-group" style='display:none;'>
											<label class="col-md-3 control-label text-center" for="credit_member">Member</label>
											<div class="col-md-9">

												<span class="help-block">Choose member name</span>
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-3 control-label text-center" for="credit_member_date">Override Date</label>
											<div class="col-md-9">
												<input id="credit_member_date" name="credit_member_date" required class="form-control input-md" type="text">
												<span class="help-block">mm/dd/yyyy (optional)</span>
											</div>
										</div>
										<div class="form-group">

											<div class="col-md-12 text-right">

												<input id="btnMcRp" name="btnMcRp" class="btn btn-default" type="submit">

											</div>
										</div>
									</form>
								</fieldset>
							</div>
							<div id="forCheck" style='display:none;'>
								<fieldset>
									<form action="" method="post">
										<input type="hidden" name='chequepid' value='<?php echo $payment_id; ?>' />
									<div class="form-group">
										<label class="col-md-3 control-label text-center" for="ch_date">Payment Date <span class='text-danger'>*</span></label>
										<div class="col-md-7">
											<input id="ch_date" name="ch_date" required class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label text-center" for="ch_number">Cheque Number <span class='text-danger'>*</span></label>
										<div class="col-md-7">
											<input id="ch_number" name="ch_number" required class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label text-center" for="ch_amount">Amount <span class='text-danger'>*</span></label>
										<div class="col-md-7">
											<input id="ch_amount" name="ch_amount" required class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label text-center" for="ch_bankname">Bank <span class='text-danger'>*</span></label>
										<div class="col-md-7">
											<input id="ch_bankname" name="ch_bankname" required class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label text-center" for="ch_firstname">First Name <span class='text-danger'>*</span></label>
										<div class="col-md-7">
											<input id="ch_firstname" name="ch_firstname" required class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label text-center" for="ch_middlename">Middle Name </label>
										<div class="col-md-7">
											<input id="ch_middlename" name="ch_middlename"  class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label text-center" for="ch_lastname">Last Name <span class='text-danger'>*</span></label>
										<div class="col-md-7">
											<input id="ch_lastname" name="ch_lastname" required class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
									</div>


									<div class="form-group">
										<label class="col-md-3 control-label text-center" for="ch_phone">Cellphone/Tel Number <span class='text-danger'>*</span></label>
										<div class="col-md-7">
											<input id="ch_phone" name="ch_phone" required class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
									</div>
										<div class="form-group">
											<label class="col-md-3 control-label text-center" for="ch_override_date">Override Date</label>
											<div class="col-md-7">
												<input id="ch_override_date" name="ch_override_date" required class="form-control input-md" type="text">
												<span class="help-block">mm/dd/yyyy (optional)</span>
											</div>
										</div>
										<div class="form-group">
											<div class="col-md-12 text-right">
												<input type="hidden" name='payment_inv' value='<?php echo $indsales->invoice; ?>'>
												<input type="hidden" name='payment_dr' value='<?php echo $indsales->dr; ?>'>
												<input type="hidden" name='payment_terminal' value='<?php echo $indsales->terminal_id; ?>'>

												<input id="btnChequeRp" name="btnChequeRp" class="btn btn-default" type="submit">

											</div>
										</div>
									</form>
								</fieldset>
							</div>
							<div id="forCredit" style='display:none;'>
								<fieldset>
									<form action="" method="post">
									<input type="hidden" name='creditpid' value='<?php echo $payment_id; ?>' />
									<div class="form-group">
										<label class="col-md-3 control-label text-center" for="billing_amount">Amount <span class='text-danger'>*</span></label>
										<div class="col-md-7">
											<input id="billing_amount" name="billing_amount" required class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label text-center" for="billing_card_type">Card Type <span class='text-danger'></span></label>
										<div class="col-md-7">
											<input id="billing_card_type" name="billing_card_type"  class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label text-center" for="billing_trace_number">Trace Number <span class='text-danger'></span></label>
										<div class="col-md-7">
											<input id="billing_trace_number" name="billing_trace_number"  class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label text-center" for="billing_approval_code">Approval Code <span class='text-danger'></span></label>
										<div class="col-md-7">
											<input id="billing_approval_code" name="billing_approval_code"  class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label text-center" for="billing_date">Date <span class='text-danger'></span></label>
										<div class="col-md-7">
											<input id="billing_date" name="billing_date"  class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label text-center" for="billing_bankname">Bank name <span class='text-danger'></span></label>
										<div class="col-md-7">
											<input id="billing_bankname" name="billing_bankname"  class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label text-center" for="billing_cardnumber">Card Number <span class='text-danger'></span></label>
										<div class="col-md-7">
											<input id="billing_cardnumber" name="billing_cardnumber"  class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label text-center" for="billing_firstname">First Name <span class='text-danger'></span></label>
										<div class="col-md-7">
											<input id="billing_firstname" name="billing_firstname"  class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label text-center" for="billing_middlename">Middle Name </label>
										<div class="col-md-7">
											<input id="billing_middlename" name="billing_middlename"  class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label text-center" for="billing_lastname">Last Name <span class='text-danger'></span></label>
										<div class="col-md-7">
											<input id="billing_lastname" name="billing_lastname"  class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label text-center" for="billing_company">Company </label>
										<div class="col-md-7">
											<input id="billing_company" name="billing_company" class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
									</div>

									<div class="form-group">
										<label class="col-md-3 control-label text-center" for="billing_address">Address </label>
										<div class="col-md-7">
											<input id="billing_address" name="billing_address" class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label text-center" for="billing_postal">Zip/Postal Code </label>
										<div class="col-md-7">
											<input id="billing_postal" name="billing_postal" class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label text-center" for="billing_phone">Cellphone/Tel Number <span class='text-danger'></span></label>
										<div class="col-md-7">
											<input id="billing_phone" name="billing_phone"  class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
									</div>



									<div class="form-group">
										<label class="col-md-3 control-label text-center" for="billing_email">Email </label>
										<div class="col-md-7">
											<input id="billing_email" name="billing_email" class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label text-center" for="billing_remarks">Special Notes</label>
										<div class="col-md-7">
											<input id="billing_remarks" name="billing_remarks" class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
									</div>
										<div class="form-group">
											<label class="col-md-3 control-label text-center" for="billing_override_date">Override Date</label>
											<div class="col-md-7">
												<input id="billing_override_date" name="billing_override_date" required class="form-control input-md" type="text">
												<span class="help-block">mm/dd/yyyy (optional)</span>
											</div>
										</div>
									<div class="form-group">
										<div class="col-md-12 text-right">
											<input type="hidden" name='payment_inv' value='<?php echo $indsales->invoice; ?>'>
											<input type="hidden" name='payment_dr' value='<?php echo $indsales->dr; ?>'>
											<input type="hidden" name='payment_terminal' value='<?php echo $indsales->terminal_id; ?>'>

											<input id="btnCreditCardRp" name="btnCreditCardRp" class="btn btn-default" type="submit">
											<span class="help-block"></span>
										</div>
									</div>
									</form>
								</fieldset>
							</div>
							<div id="forBt" style='display:none;'>
								<fieldset>

									<form action="" method="post">
										<input type="hidden" name='btpid' value='<?php echo $payment_id; ?>' />
									<div class="form-group">
										<div class="row">
										<label class="col-md-3 control-label text-center" for="bankfrom_account_number">Bank Acccount number(From) <span class='text-danger'>*</span></label>
										<div class="col-md-7">
											<input id="bankfrom_account_number" required name="bankfrom_account_number" class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
										</div>
									</div>
									<div class="form-group">
										<div class="row">
										<label class="col-md-3 control-label text-center" for="bt_amount">Amount <span class='text-danger'>*</span></label>
										<div class="col-md-7">
											<input id="bt_amount" name="bt_amount" required class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
											</div>
									</div>
									<div class="form-group">
										<div class="row">
										<label class="col-md-3 control-label text-center" for="bankfrom_name">Bank Name(From) <span class='text-danger'>*</span></label>
										<div class="col-md-7">
											<input id="bankfrom_name" name="bankfrom_name" required class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
										</div>
									</div>
									<div class="form-group">
										<div class="row">
										<label class="col-md-3 control-label text-center" for="bt_bankto_name">Bank name(to) <span class='text-danger'>*</span></label>
										<div class="col-md-7">
											<input id="bt_bankto_name" name="bt_bankto_name" required class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
											</div>
									</div>
									<div class="form-group">
										<div class="row">
										<label class="col-md-3 control-label text-center" for="bt_bankto_account_number">Bank Account Number(to) <span class='text-danger'>*</span></label>
										<div class="col-md-7">
											<input id="bt_bankto_account_number" name="bt_bankto_account_number" required class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
											</div>
									</div>
									<div class="form-group">
										<div class="row">
											<label class="col-md-3 control-label text-center" for="bt_firstname">First Name <span class='text-danger'>*</span></label>
											<div class="col-md-7">
												<input id="bt_firstname" name="bt_firstname" required class="form-control input-md" type="text">
												<span class="help-block"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="row">
										<label class="col-md-3 control-label text-center" for="bt_middlename">Middle Name </label>
										<div class="col-md-7">
											<input id="bt_middlename" name="bt_middlename"  class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
											</div>
									</div>
									<div class="form-group">
										<div class="row">
										<label class="col-md-3 control-label text-center" for="bt_lastname">Last Name <span class='text-danger'>*</span></label>
										<div class="col-md-7">
											<input id="bt_lastname" name="bt_lastname" required class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
										</div>
									</div>
									<div class="form-group">
										<div class="row">
										<label class="col-md-3 control-label text-center" for="bt_company">Company </label>
										<div class="col-md-7">
											<input id="bt_company" name="bt_company" class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
											</div>
									</div>

									<div class="form-group">
										<div class="row">
										<label class="col-md-3 control-label text-center" for="bt_address">Address </label>
										<div class="col-md-7">
											<input id="bt_address" name="bt_address" class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
											</div>
									</div>
									<div class="form-group">
										<div class="row">
										<label class="col-md-3 control-label text-center" for="bt_postal">Zip/Postal Code </label>
										<div class="col-md-7">
											<input id="bt_postal" name="bt_postal" class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
											</div>
									</div>
									<div class="form-group">
										<div class="row">
										<label class="col-md-3 control-label text-center" for="bt_phone">Cellphone/Tel Number <span class='text-danger'>*</span> </label>
										<div class="col-md-7">
											<input id="bt_phone" name="bt_phone" required class="form-control input-md" type="text">
											<span class="help-block"></span>
										</div>
										</div>
									</div>
										<div class="form-group">
											<label class="col-md-3 control-label text-center" for="bt_date">Override Date</label>
											<div class="col-md-7">
												<input id="bt_date" name="bt_date" required class="form-control input-md" type="text">
												<span class="help-block">mm/dd/yyyy (optional)</span>
											</div>
										</div>
										<div class="form-group">
											<div class="row">
											<div class="col-md-12 text-right">
												<input type="hidden" name='payment_inv' value='<?php echo $indsales->invoice; ?>'>
												<input type="hidden" name='payment_dr' value='<?php echo $indsales->dr; ?>'>
												<input type="hidden" name='payment_terminal' value='<?php echo $indsales->terminal_id; ?>'>

												<input id="btnBtRp" name="btnBtRp" class="btn btn-default" type="submit">
											</div>
										</div>
									</form>
								</fieldset>
							</div>
						</div>
						<div id="forConAmount" style='display:none'>


								<fieldset>
									<form action="" method='post'>
										<input type="hidden" name='conamountpid' value='<?php echo $payment_id; ?>' />
										<input class='form-control' type="hidden" value='<?php echo $indsales->member_id; ?>' name='con_member' id='con_member'>
										<div class="form-group" style='display:none;'>
										<label class="col-md-3 control-label text-center" for="con_member">Member</label>
										<div class="col-md-9">

											<span class="help-block">Choose member name</span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label text-center" for="con_amount">Amount</label>
										<div class="col-md-9">
											<input id="con_amount" name="con_amount" class="form-control input-md" type="text">
											<span class="help-block">Amount In Peso</span>
										</div>
									</div>
										<div class="form-group">
											<label class="col-md-3 control-label text-center" for="con_date">Override Date</label>
											<div class="col-md-9">
												<input id="con_date" name="con_date" required class="form-control input-md" type="text">
												<span class="help-block">mm/dd/yyyy (optional)</span>
											</div>
										</div>
										<div class="form-group">
											<div class="row">
												<div class="col-md-11 text-right">
												<input id="btnConAmountRp" name="btnConAmountRp" class="btn btn-default" type="submit">
												</div>
											</div>
										</div>
									</form>
								</fieldset>
						</div>
						<div id="forConFreebies" style='display:none'>
							<fieldset>
								<form action="" method='post'>
									<input type="hidden" name='confreebiespid' value='<?php echo $payment_id; ?>' />
									<input class='form-control' type="hidden" value='<?php echo $indsales->member_id; ?>'  name='con_member_freebies' id='con_member_freebies'>
									<div class="form-group" style='display:none;'>
									<label class="col-md-3 control-label text-center" for="con_member_freebies">Member</label>
									<div class="col-md-9">


										<span class="help-block">Choose member name</span>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 control-label text-center" for="con_amount_freebies">Amount</label>
									<div class="col-md-9">
										<input id="con_amount_freebies" name="con_amount_freebies" class="form-control input-md" type="text">
										<span class="help-block">Amount In Peso</span>
									</div>
								</div>
									<div class="form-group">
										<label class="col-md-3 control-label text-center" for="con_amount_date">Override Date</label>
										<div class="col-md-9">
											<input id="con_amount_date" name="con_amount_date" required class="form-control input-md" type="text">
											<span class="help-block">mm/dd/yyyy (optional)</span>
										</div>
									</div>
									<div class="form-group">
											<div class="col-md-12">
											<input id="btnConFreebiesRp" name="btnConFreebiesRp" class="btn btn-default" type="submit">

										</div>
									</div>
								</form>
							</fieldset>
						</div>
						<div id="forSales" style='display:none;'>
							<div class="container-fluid">
							<fieldset>
								<form action="" method="post">
									<input type="hidden" name='salespid' value='<?php echo $payment_id; ?>' />
									<input type="hidden" name='salesinvoice' value='<?php echo $indsales->invoice; ?>'/>
									<input type="hidden" name='salesdr' value='<?php echo $indsales->dr; ?>'/>
									<input type="hidden" name='salesterminal' value='<?php echo $indsales->terminal_id; ?>'/>
									<input type="hidden" name='salesterminal' value='<?php echo $indsales->terminal_id; ?>'/>
									<input type="hidden" name='salesmember' value='<?php echo $indsales->member_id; ?>'/>
									<input type="hidden" name='salesstation' value='<?php echo $indsales->station_id; ?>'/>
									<input type="hidden" name='salescashier' value='<?php echo $indsales->cashier_id; ?>'/>
									<input type="hidden" name='salessolddate' value='<?php echo $indsales->sold_date; ?>'/>
									<div class="form-group">
										<div class="row">
										<label class="col-md-3 control-label text-center" for="itemcode">Item Code</label>
										<div class="col-md-7">
											<select name="itemcode" class='form-control' id="itemcode">
												<option value=""></option>
												<?php
													$item = new Product();
													$items = $item->get_active('items', array('company_id', '=', $user->data()->company_id));
													$ilist = '';
													foreach($items as $i):
														if($i->item_type == 2) continue; //aqua
														$prc = $item->getPrice($i->id);
														?>
														<?php
														echo "<option data-item_type='$i->item_type' data-price='".$prc->price."' data-bc='".$i->barcode."' value='".$i->id."'>". escape($i->barcode).":". escape($i->item_code).":". escape($i->description)."</option>";
														?>
												<?php endforeach; ?>

											</select> <input type="hidden" name="s_item_type" id='s_item_type'>
										</div>
											<div class="col-md-1">
												<div id="imagecon" style='position:absolute;left:0px;top:0px;'>
													<img src="" alt="Image" />
												</div>
											</div>
										</div>
									</div>

									<div class="form-group">
										<div class="row">
										<label class="col-md-3 control-label text-center" for="s_qty">Qty</label>
										<div class="col-md-7">
											<input id="s_qty" name="s_qty"  class="form-control input-md" type="text">
										</div>
											<div class="col-md-1"></div>
										</div>
									</div>
									<div class="form-group">
										<div class="row">
											<label class="col-md-3 control-label text-center" for="s_price">Price</label>
											<div class="col-md-7">
												<span class='badge' id='s_price'>0.00</span>
											</div>
											<div class="col-md-1"></div>
										</div>
									</div>
									<div class="form-group">
										<div class="row">
										<label class="col-md-3 control-label text-center" for="s_discount">Discount</label>
										<div class="col-md-7">
											<input id="s_discount" value='0' name="s_discount" class="form-control input-md" type="text">
										</div>
											<div class="col-md-1"></div>
											</div>
									</div>
									<div class="form-group">
										<div class="row">
											<label class="col-md-3 control-label text-center" for="s_total">Total</label>
											<div class="col-md-7">
												<span class='badge' id='s_total'>0.00</span>
											</div>
											<div class="col-md-1"></div>
										</div>
									</div>

									<div class="form-group">
										<div class="row">
										<div class="col-md-12 text-right">
											<input id="btnAddSalesRp" name="btnAddSalesRp" class="btn btn-default" type="submit" value='Add'>
										</div>
											</div>
									</div>
								</form>
							</fieldset>
							</div>
						</div>

					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->

		</div>
	</div> <!-- end page content wrapper-->



<script type="text/javascript">
	$(function(){
		var invcount = '<?php echo $invcount; ?>';
		var drcount = '<?php echo $drcount; ?>';
		if(parseInt(invcount) > 1){
			$('#invoice').val('<?php echo $invlist; ?>');
			$('#btnSave').attr('disabled',true);
		}
		if(parseInt(drcount) > 1){
			$('#dr').val('<?php echo $drlist; ?>');
			$('#btnSave').attr('disabled',true);
		}
		function formatItem(o) {
			if (!o.id)
				return o.text; // optgroup
			else {
				var r = o.text.split(':');
				return "<span> "+r[0]+"</span> <span style='margin-left:10px'>" + r[1] + "</span><span style='display:block;margin-top:5px;'  class='text-danger'><small class='testspanclass'>"+r[2]+"</small></span>";
			}
		}
		$("#itemcode").select2({
			placeholder: 'Select Item',
			allowClear: true,
			formatResult: formatItem,
			formatSelection: formatItem,
			escapeMarkup: function(m) {
				return m;
			}
		}).on("select2-close", function(e) {
			// fired to the original element when the dropdown closes

			setTimeout(function() {
				$('#imagecon').fadeOut();
			}, 300);
		}).on("select2-highlight", function(e) {
			console.log("highlighted val=" + e.val + " choice=" + e.choice.text);
			var itemid =  e.choice.id;
			var itemjpg = itemid +".jpg";
			var opt = $(this);
			$.ajax({
				url:'../item_images/'+itemjpg,
				type:'HEAD',
				error: function()
				{
					$('#imagecon').fadeOut();
				},
				success: function()
				{
					$('#imagecon  img').attr('src','../item_images/'+itemjpg);
					$('#imagecon').fadeIn();

				}
			});
		});
		$('#itemcode').change(function(){
			var item = $('#itemcode :selected');
			var price = item.attr('data-price');
			if(price){
				price = parseFloat(price);
				price = price.toFixed(2);
				$('#s_price').html(price);
			} else {
				$('#s_price').html('0.00');
			}
			$('#s_item_type').val(item.attr('data-item_type'));
		});
		$('#s_qty,#s_discount').keyup(function(){
			computetotal();
		});

		function computetotal(){
			var qty = parseFloat($('#s_qty').val());
			var discount = parseFloat($('#s_discount').val());
			var price = parseFloat($('#s_price').text());
			var item = $('#itemcode').val();
			if(!item){
				alertify.alert('Please choose item first');
				return;
			}
			if (isNaN(qty) || !qty){
				alertify.alert('Please Enter valid qty');
				$('#s_qty').val('1');
				return;
			}
			if (qty && isNaN(discount)){
				alertify.alert('Please Enter valid discount');
				$('#s_discount').val('0');
				return;
			}
			var total = (qty * price) - discount;
			$('#s_total').html(total.toFixed(2));

		}

	    $('#dt').datepicker({
			autoclose:true
		}).on('changeDate', function(ev){
			$('#dt').datepicker('hide');
		});

		$('#billing_date').datepicker({
			autoclose:true
		}).on('changeDate', function(ev){
			$('#billing_date').datepicker('hide');
		});

		$('#btnCash').click(function(){
			$('#modalPayment').modal('show');
			$('#forCash').fadeIn();
			$('#forCheck').hide();
			$('#forCredit').hide();
			$('#forBt').hide();
			$('#forConAmount').hide();
			$('#forConFreebies').hide();
			$('#forSales').hide();
			$('#forMemberCredit').hide();
			$('#forMemberDeduction').hide();
		});

		$('#btnMemberCredit').click(function(){
			$('#modalPayment').modal('show');
			$('#forCash').hide();
			$('#forCheck').hide();
			$('#forCredit').hide();
			$('#forBt').hide();
			$('#forConAmount').hide();
			$('#forConFreebies').hide();
			$('#forSales').hide();
			$('#forMemberCredit').fadeIn();
			$('#forMemberDeduction').hide();
		});

		$('#btnMemberDeduction').click(function(){
			$('#modalPayment').modal('show');
			$('#forCash').hide();
			$('#forCheck').hide();
			$('#forCredit').hide();
			$('#forBt').hide();
			$('#forConAmount').hide();
			$('#forConFreebies').hide();
			$('#forSales').hide();
			$('#forMemberCredit').hide();
			$('#forMemberDeduction').fadeIn();
		});

		$('#btnCheque').click(function(){
			$('#modalPayment').modal('show');
			$('#forCash').hide();
			$('#forCheck').fadeIn();
			$('#forCredit').hide();
			$('#forBt').hide();
			$('#forConAmount').hide();
			$('#forConFreebies').hide();
			$('#forSales').hide();
			$('#forMemberDeduction').hide();

		});

		$('#btnCreditCard').click(function(){
			$('#modalPayment').modal('show');
			$('#forCash').hide();
			$('#forCheck').hide();
			$('#forCredit').fadeIn();
			$('#forBt').hide();
			$('#forConAmount').hide();
			$('#forConFreebies').hide();
			$('#forSales').hide();
			$('#forMemberCredit').hide();
			$('#forMemberDeduction').hide();
		});

		$('#btnBankTransfer').click(function(){
			$('#modalPayment').modal('show');
			$('#forCash').hide();
			$('#forCheck').hide();
			$('#forCredit').hide();
			$('#forBt').fadeIn();
			$('#forConAmount').hide();
			$('#forConFreebies').hide();
			$('#forSales').hide();
			$('#forMemberCredit').hide();
			$('#forMemberDeduction').hide();
		});

		$('#btnConAmount').click(function(){
			$('#modalPayment').modal('show');
			$('#forCash').hide();
			$('#forCheck').hide();
			$('#forCredit').hide();
			$('#forBt').hide();
			$('#forConAmount').fadeIn();
			$('#forConFreebies').hide();
			$('#forSales').hide();
			$('#forMemberCredit').hide();
			$('#forMemberDeduction').hide();

		});

		$('#btnConFreebies').click(function(){
			$('#modalPayment').modal('show');
			$('#forCash').hide();
			$('#forCheck').hide();
			$('#forCredit').hide();
			$('#forBt').hide();
			$('#forConAmount').hide();
			$('#forConFreebies').fadeIn();
			$('#forSales').hide();
			$('#forMemberCredit').hide();
			$('#forMemberDeduction').hide();
		});

		$('#btnAddSales').click(function(){
			$('#modalPayment').modal('show');
			$('#forCash').hide();
			$('#forCheck').hide();
			$('#forCredit').hide();
			$('#forBt').hide();
			$('#forConAmount').hide();
			$('#forConFreebies').hide();
			$('#forSales').fadeIn();
			$('#forMemberCredit').hide();
			$('#forMemberDeduction').hide();
		});

		$('body').on('click','.btnDelCash',function(){
			if(confirm("Are you sure you want to delete this record? \n ")){
				var btn = $(this);
				var id = btn.attr('data-id');
				var payment_dr = btn.attr('data-paymentdr');
				var payment_inv = btn.attr('data-paymentinv');
				var amount = btn.attr('data-amount');
				var terminal_id = btn.attr('data-terminal_id');

				$.post('../ajax/ajax_deletepermanent.php',{id:id,table:'cash',payment_dr:payment_dr,payment_inv:payment_inv,amount:amount,terminal_id:terminal_id},function(data){
					if(data == "true"){
						location.reload();
					}
				});
			}
		});

		$('body').on('click','.btnDelMemCredit',function(){
			if(confirm("Are you sure you want to delete this record? \n ")){
				var btn = $(this);
				var id = btn.attr('data-id');
				$.post('../ajax/ajax_deletepermanent.php',{id:id,table:'member_credit'},function(data){
					if(data == "true"){
						location.reload();
					}
				});
			}
		});
		$('body').on('click','.btnDelDeduction',function(){
			if(confirm("Are you sure you want to delete this record? \n ")){
				var btn = $(this);
				var id = btn.attr('data-id');
				$.post('../ajax/ajax_deletepermanent.php',{id:id,table:'deductions'},function(data){
					if(data == "true"){
						location.reload();
					}
				});
			}
		});

		$('body').on('click','.btnDelCheque',function(){
			if(confirm("Are you sure you want to delete this record? \n ")){
				var btn = $(this);
				var id = btn.attr('data-id');
				var payment_dr = btn.attr('data-paymentdr');
				var payment_inv = btn.attr('data-paymentinv');
				var amount = btn.attr('data-amount');
				var terminal_id = btn.attr('data-terminal_id');

				$.post('../ajax/ajax_deletepermanent.php',{id:id,table:'cheque',payment_dr:payment_dr,payment_inv:payment_inv,amount:amount,terminal_id:terminal_id},function(data){
					if(data == "true"){
						location.reload();
					}
				});
			}
		});

		$('body').on('click','.btnDelSales',function(){
			if(confirm("Are you sure you want to delete this record? \n ")){
				var id = $(this).attr('data-id');
				$.post('../ajax/ajax_deletepermanent.php',{id:id,table:'sales'},function(data){
					if(data == "true"){
						location.reload();
					}
				});

			}
		});

		$('body').on('click','.btnDelCredit',function(){
			if(confirm("Are you sure you want to delete this record? \n ")){
				var btn = $(this);
				var id = btn.attr('data-id');
				var payment_dr = btn.attr('data-paymentdr');
				var payment_inv = btn.attr('data-paymentinv');
				var amount = btn.attr('data-amount');
				var terminal_id = btn.attr('data-terminal_id');

				$.post('../ajax/ajax_deletepermanent.php',{id:id,table:'credit_card',payment_dr:payment_dr,payment_inv:payment_inv,amount:amount,terminal_id:terminal_id},function(data){
					if(data == "true"){
						location.reload();
					}
				});
			}
		});

		$('body').on('click','.btnDelBt',function(){
			if(confirm("Are you sure you want to delete this record? \n ")){
				var btn = $(this);
				var id = btn.attr('data-id');
				var payment_dr = btn.attr('data-paymentdr');
				var payment_inv = btn.attr('data-paymentinv');
				var amount = btn.attr('data-amount');
				var terminal_id = btn.attr('data-terminal_id');
				$.post('../ajax/ajax_deletepermanent.php',{id:id,table:'bank_transfer',payment_dr:payment_dr,payment_inv:payment_inv,amount:amount,terminal_id:terminal_id},function(data){
					if(data == "true"){
						location.reload();
					}
				});
			}
		});

		$('body').on('click','.btnDelConAmount',function(){
			if(confirm("Are you sure you want to delete this record? \n ")){
				var id = $(this).attr('data-id');
				$.post('../ajax/ajax_deletepermanent.php',{id:id,table:'payment_consumable'},function(data){
					if(data == "true"){
						location.reload();
					}
				});
			}
		});

		$('body').on('click','.btnDelConFreebies',function(){

			if(confirm("Are you sure you want to delete this record? \n ")){
				var id = $(this).attr('data-id');
				$.post('../ajax/ajax_deletepermanent.php',{id:id,table:'payment_consumable_freebies'},function(data){
					if(data == "true"){
						location.reload();
					}
				});
			}

		});

		$("#btnSave").click(function(){

			var payment_id= $(this).attr('data-payment-id');
			var invoice = $("#invoice").val();
			var sv = $("#sv").val();
			var dr = $("#dr").val();
			var dt = $("#dt").val();
			var ir = $("#ir").val();
			var from_service = $("#from_service").val();
			var main_sales_type = $("#main_sales_type").val();
			var addtl_remarks = $("#addtl_remarks").val();

			var rgx = /^\d+$/
			var rgx2 = /^\d{2}\/\d{2}\/\d{4}$/

			if(!rgx.test(invoice)){
				alert('Invoice should be a number');
				return;
			}

			if(!rgx.test(dr)){
				alert('Dr should be a number');
				return;
			}

			if(!rgx2.test(dt)){
				alert('Date should be a valid date (mm/dd/yyyy)');
				return;
			}

			$.ajax({
				url:'../ajax/ajax_salescrud.php',
				type:'post',
				data:{functionName:'saveSales',addtl_remarks:addtl_remarks,main_sales_type:main_sales_type,sv:sv,dr:dr,dt:dt,invoice:invoice,ir:ir,from_service:from_service,payment_id:payment_id},
				success: function(data){
					alert(data);
					location.href='sales_crud.php?id='+payment_id;
				}
			})

		});

		/*

		$("#con_member,#con_member_freebies,#credit_member,#member_deduction").select2({
			placeholder: 'Search Member',
			allowClear: true,
			minimumInputLength: 2,
			ajax: {
				url: '../ajax/ajax_json.php',
				dataType: 'json',
				type: "POST",
				quietMillis: 50,
				data: function (term) {
					return {
						q: term,
						functionName:'members'
					};
				},
				results: function (data) {
					return {
						results: $.map(data, function (item) {
							return {
								text: item.lastname + ", " + item.sales_type_name,
								slug: item.lastname + ", " + item.firstname + " " + item.middlename,
								id: item.id
							}
						})
					};
				}
			}
		});

		*/

		$("#select2_member").select2({
			placeholder: 'Choose member name...',
			allowClear: true
		});
		$("#select2_station").select2({
			placeholder: 'Choose station name...',
			allowClear: true
		});

		//getMembers(localStorage['company_id']);

		function getMembers(company_id){
			console.log(company_id);

				$.ajax({
					url: "../ajax/ajax_get_members.php",
					type:"POST",
					data:{company_id:company_id,type:1},
					success: function(data){

						if(data != 0)
						{
							localStorage['members']=data;
							getMemberOptList();


						} else {

						}

					}
				});

		}
		function getMembersInd(company_id,member_id){
			$("#con_member").empty();
			$("#con_member").append("<option></option>");
			$("#con_member_freebies").empty();
			$("#con_member_freebies").append("<option></option>");
			$("#credit_member").empty();
			$("#credit_member").append("<option></option>");
			$("#member_deduction").empty();
			$("#member_deduction").append("<option></option>");
			if(member_id){
				$.ajax({
					url: "../ajax/ajax_get_members.php",
					type:"POST",
					data:{company_id:company_id,member_id:member_id,type:1},
					success: function(data){
						if(data != 0)
						{
							var mems = JSON.parse(data);
							for(var i in mems){
								var amt =0;
								var amt_freebies = 0;
								if(mems[i].amt){
									var check_not_validyet =0;
									amt = mems[i].amt;
									if(mems[i].camt) check_not_validyet = mems[i].camt;
									amt = amt - check_not_validyet;
									$("#con_member").append("<option data-con='"+amt+"' value='"+mems[i].id+"'>"+mems[i].lastname+", " +mems[i].firstname + " " + mems[i].middlename +" ("+amt+")</option>");
								}
								if(mems[i].freebiesamount){
									amt_freebies = mems[i].freebiesamount;
								}
								$("#con_member_freebies").append("<option data-con_freebies='"+amt_freebies+"' value='"+mems[i].id+"'>"+mems[i].lastname+", " +mems[i].firstname + " " + mems[i].middlename +" ("+amt_freebies+")</option>");
								$("#credit_member").append("<option value='"+mems[i].id+"'>"+mems[i].lastname+", " +mems[i].firstname + " " + mems[i].middlename +"</option>");
								$("#member_deduction").append("<option value='"+mems[i].id+"'>"+mems[i].lastname+", " +mems[i].firstname + " " + mems[i].middlename +"</option>");
							}
							$("#con_member_freebies").select2('val',member_id);
							$("#credit_member").select2('val',member_id);
							$("#con_member").select2('val',member_id);
							$("#member_deduction").select2('val',member_id);
							$("#con_member_freebies").attr('disabled',true);
							$("#credit_member").attr('disabled',true);
							$("#member_deduction").attr('disabled',true);
							$("#con_member").attr('disabled',true);
						}
					}
				});
			}

		}
		function getMemberOptList(){
			if(localStorage['members'] != null){
				var mems = JSON.parse(localStorage['members']);

				$("#con_member").empty();
				$("#con_member").append("<option></option>");
				$("#con_member_freebies").empty();
				$("#con_member_freebies").append("<option></option>");
				$("#credit_member").empty();
				$("#credit_member").append("<option></option>");
				for(var i in mems){
					var amt =0;
					var amt_freebies = 0;
					if(mems[i].amt){
						var check_not_validyet =0;
						amt = mems[i].amt;
						if(mems[i].camt) check_not_validyet = mems[i].camt;
						amt = amt - check_not_validyet;
						$("#con_member").append("<option data-con='"+amt+"' value='"+mems[i].id+"'>"+mems[i].lastname+", " +mems[i].firstname + " " + mems[i].middlename +" ("+amt+")</option>");
					}
					if(mems[i].freebiesamount){
						amt_freebies = mems[i].freebiesamount;
					}
					$("#con_member_freebies").append("<option data-con_freebies='"+amt_freebies+"' value='"+mems[i].id+"'>"+mems[i].lastname+", " +mems[i].firstname + " " + mems[i].middlename +" ("+amt_freebies+")</option>");
					$("#credit_member").append("<option  value='"+mems[i].id+"'>"+mems[i].lastname+", " +mems[i].firstname + " " + mems[i].middlename + "</option>");
				}
			}
		}
	});
	
</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>