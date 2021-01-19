<?php
	include 'ajax_connection.php';
	require '../classes/class.phpmailer.php';
	require '../classes/class.smtp.php';

	$functionName = Input::get("functionName");

	if(function_exists($functionName)){
		$functionName();
	}


	function machineReport(){
		$t = Input::get('t');
		if($t == 1){
			$filename = "machinesales-" . date('m-d-Y-H-i-s-A') . ".xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");
		}
		$dt_from = Input::get('dt_from');
		$dt_to = Input::get('dt_to');
		$sales_type = Input::get('sales_type');

		if(!$dt_from && !$dt_to){
			$dt_from = date('F Y');
			$dt_to = date('m/d/Y',strtotime(date('F Y') . "1 month -1 day"));
		}

		$sales = new Sales();
		$user = new User();
		if($sales_type){
			if($sales_type == -1){
				$st = [0];
			} else {
				$st = [$sales_type];
			}
		} else{
			$st = [];
		}

		$query_string = "";
		$list = $sales->getSalesForDownload($user->data()->company_id,0,0,0,0,0,0,0,$dt_from,$dt_to,0,0,$st,0,0,$query_string);

		if($list){
			$total_all = 0;
			echo "<h5>Record from ". date('m/d/Y',strtotime($dt_from))." to ". date('m/d/Y',strtotime($dt_to))."</h5>";
			echo "<table class='table'>";
			echo "<thead><tr><th>Date Sold</th><th>Sales type</th><th>Member</th><th>Invoice</th><th>Dr</th><th>Pr</th><th>Item</th><th>Qty</th><th>Price</th><th>Adjustment</th><th>Total</th></tr></thead>";
			foreach($list as $l){
				$total = ($l->qtys * ($l->price + $l->adjustment)) + $l->member_adjustment;
				$total_all += $total;
				echo "<tr>";
				echo "<td>" .date('m/d/Y',$l->sold_date)."</td>";
				echo "<td>$l->sales_type_name</td>";
				echo "<td>$l->member_name</td>";
				echo "<td>$l->invoice</td>";
				echo "<td>$l->dr</td>";
				echo "<td>$l->ir</td>";
				echo "<td>$l->description</td>";
				echo "<td>$l->qtys</td>";
				echo "<td> " . ($l->price+ $l->adjustment). " </td>";
				echo "<td>$l->member_adjustment</td>";
				echo "<td>". number_format($total,2) . "</td>";
				echo "</tr>";
			}
			echo "</tbody>";
			echo "</table>";
			echo "<h5>Total: ".number_format($total_all,2)."</h5>";
		} else {
			echo "No record found.";
		}
	}
	function getCustomRecordSummary(){
		$t = Input::get('t');
		$query_string = Input::get('query_string');
		$branch_id = json_decode(Input::get('branch_id'),true);
		$withBorder = "";
		if($t == 1){
			$withBorder = "border=1";
			$filename = $query_string. "-SALES-" . date('m-d-Y-H-i-s-A') . ".xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");
		}
		$dt_from = Input::get('dt_from');
		$dt_to = Input::get('dt_to');
		$sales_type = Input::get('sales_type');

		if($dt_from && $dt_to){

		} else {
			$dt_from = date('F Y');
			$dt_to = date('m/d/Y',strtotime(date('F Y') . "1 month -1 day"));
		}

		$sales = new Sales();
		$user = new User();
		if($sales_type){
			if($sales_type == -1){
				$st = [0];
			} else {
				$st = [$sales_type];
			}
		} else{
			$st = [];
		}
		$from_service = 0;
		if($query_string == 'SERVICE'){
			$query_string = '';
			$from_service = 3;
		}
		$list = $sales->getSalesForDownload2($user->data()->company_id,0,$branch_id,0,0,0,0,0,$dt_from,$dt_to,0,0,$st,0,0,$from_service,0,0,$query_string);

		if($list){
			$total_all = 0;
			echo "<h5>Record from ". date('m/d/Y',strtotime($dt_from))." to ". date('m/d/Y',strtotime($dt_to))."</h5>";
			echo "<table class='table' $withBorder id='tblBordered'>";
			echo "<thead><tr><th>Date Sold</th><th>Sales type</th><th>Member</th><th>Invoice</th><th>Dr</th><th>Pr</th><th>Total</th></tr></thead>";
			foreach($list as $l){
				$total_all += $l->totalamount;
				echo "<tr>";
				echo "<td>" .date('m/d/Y',$l->sold_date)."</td>";
				echo "<td>$l->sales_type_name</td>";
				echo "<td>$l->mln</td>";
				echo "<td>$l->invoice</td>";
				echo "<td>$l->dr</td>";
				echo "<td>$l->ir</td>";
				echo "<td>". number_format($l->totalamount,2) . "</td>";
				echo "</tr>";
			}
			echo "</tbody>";
			echo "</table>";
			echo "<h5>Total: ".number_format($total_all,2)."</h5>";
		} else {
			echo "No record found.";
		}

	}
	function getCustomRecord(){
		$t = Input::get('t');
		$query_string = Input::get('query_string');
		$branch_id = json_decode(Input::get('branch_id'),true);
		$withBorder = "";
		if($t == 1){
			$withBorder = "border=1";
			$filename = $query_string. "-SALES-" . date('m-d-Y-H-i-s-A') . ".xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");
		}
		$dt_from = Input::get('dt_from');
		$dt_to = Input::get('dt_to');
		$sales_type = Input::get('sales_type');

		if($dt_from && $dt_to){

		} else {
			$dt_from = date('F Y');
			$dt_to = date('m/d/Y',strtotime(date('F Y') . "1 month -1 day"));
		}

		$sales = new Sales();
		$user = new User();
		if($sales_type){
			if($sales_type == -1){
				$st = [0];
			} else {
				$st = [$sales_type];
			}
		} else{
			$st = [];
		}
		$from_service = 0;
		if($query_string == 'SERVICE'){
			$query_string = '';
			$from_service = 3;
		}
		$list = $sales->getSalesForDownload($user->data()->company_id,0,$branch_id,0,0,0,0,0,$dt_from,$dt_to,0,0,$st,0,0,$query_string,$from_service);

		if($list){
			$total_all = 0;
			echo "<h5>Record from ". date('m/d/Y',strtotime($dt_from))." to ". date('m/d/Y',strtotime($dt_to))."</h5>";
			echo "<table class='table' $withBorder id='tblBordered'>";
			echo "<thead><tr><th>Date Sold</th><th>Sales type</th><th>Member</th><th>Invoice</th><th>Dr</th><th>Pr</th><th>Item</th><th>Qty</th><th>Price</th><th>Adjustment</th><th>Total</th></tr></thead>";
			foreach($list as $l){
				$total = ($l->qtys * $l->price) + $l->adjustment + $l->member_adjustment;

				$total_all += $total;
				echo "<tr>";
				echo "<td>" .date('m/d/Y',$l->sold_date)."</td>";
				echo "<td>$l->sales_type_name</td>";
				echo "<td>$l->member_name</td>";
				echo "<td>$l->invoice</td>";
				echo "<td>$l->dr</td>";
				echo "<td>$l->ir</td>";
				echo "<td>$l->description</td>";
				echo "<td>$l->qtys</td>";
				echo "<td> " . number_format($l->price,2). " </td>";
				echo "<td>" . number_format($l->member_adjustment +  $l->adjustment,2). "</td>";
				echo "<td>". number_format($total,2) . "</td>";
				echo "</tr>";
			}
			echo "</tbody>";
			echo "</table>";
			echo "<h5>Total: ".number_format($total_all,2)."</h5>";
		} else {
			echo "No record found.";
		}
	}
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


	function freightPaidByPaymentId(){
		$id = Input::get('payment_id');
		$freight = new Freight();
		$freight->paidFreight($id);
		echo "Updated successfully.";
	}
	function freightPaid(){
		$id = Input::get('id');
		$freight = new Freight($id);
		$paid_amount = $freight->data()->charge + $freight->data()->freight_adjustment;
			$freight->update(
			[
				'status' => 1,
				'paid_amount' => $paid_amount,

			],$id
		);
		echo "Updated successfully.";
	}
	function saveCredit(){
		$cr_number = Input::get('cr_number');
		$member_id = Input::get('member_id');
		$branch_id = Input::get('branch_id');
		$amount = Input::get('amount');
		$dt = Input::get('dt');
		$dr = Input::get('dr');
		$pr = Input::get('pr');
		$invoice = Input::get('invoice');
		$terminal_id = Input::get('terminal_id');
		$is_service = Input::get('is_service');
		$user = new User();
		$dr = ($dr) ? $dr : '';
		$pr = ($pr) ? $pr : '';
		$invoice = ($invoice) ? $invoice : '';
		$cr_number = ($cr_number) ? $cr_number : '';

		if(!$amount || !is_numeric($amount) || !$member_id || !is_numeric($member_id) || !$dt){
			echo "Invalid data";
		} else {
			$member_data = new Member($member_id);
			// add payment
			$payment = new Payment();
			if(!$terminal_id) {
				die("Please set up terminal first.");
			}
			$scompany =$user->data()->company_id;
			$payment->create(array(
				'created' => time(),
				'company_id' => $scompany,
				'is_active' => 1,
				'cr_number' => $cr_number
			));
			$payment_lastid = $payment->getInsertedId();

			// add sales


			if(Configuration::thisCompany('pw')){
				$item_id = 119; //tochange
			}else if(Configuration::thisCompany('vitalite')) {
				$item_id = 589; //tochange
			} else {
				//die("You are not allowed to used this.");
				$item_id = 4646; //tochange
			}

			$qty = 1;
			$newsales = new Sales();

			$date = strtotime($dt);

			$prod = new Product();
			$price = $prod->getPrice($item_id);



			$salestype = (isset($member_data->data()->salestype) && $member_data->data()->salestype) ? $member_data->data()->salestype : 0;
			$newsales->create(array(
				'terminal_id' => $terminal_id,
				'item_id' => $item_id,
				'price_id' =>$price->id,
				'qtys' => $qty,
				'discount' => 0,
				'store_discount' => 0,
				'adjustment' => 0,
				'member_adjustment' => $amount,
				'company_id' => $scompany,
				'cashier_id' => $user->data()->id,
				'sold_date' => $date,
				'payment_id' => $payment_lastid,
				'member_id' => $member_id,
				'sales_type' =>$salestype,
				'dr' => $dr,
				'invoice' => $invoice,
				'ir' => $pr,
				'is_service' => $is_service
			));


			// add member credit

			$now = time();
			$pcredit = new Member_credit();

			$pcredit->create(array(
				'amount' =>$amount,
				'is_active' => 1,
				'created' => $now,
				'modified' => $now,
				'payment_id' => $payment_lastid,
				'member_id' => $member_id,
				'branch_id' => $branch_id
			));

			Log::addLog($user->data()->id,$user->data()->company_id,"Add Member Credit PID: $payment_lastid MID: $member_id","ajax_sales_query.php");

			echo "Credit added successfully";

		}
	}

	function sendBillingEmail(){
		$member_id = Input::get('member_id');
		$payment_id = Input::get('payment_id');
		$content = Input::get('content');
		$email = Input::get('email');
		$subject = Input::get('subject');
		$remarks = Input::get('remarks');
		$remarks = str_replace('\n',"<br>",$remarks);
		$div = "";
		//$div = "<div style='min-width:650px;width:100%;background-color:#f0f0f0;padding:10px;'>";
		$content = "<html><body><div>" .$remarks ."</div> <div style='clear:both;'></div>"  . "<div>" . $content . "</div></body></html>";
		$div .= $content;
		//$div .= "</div>";
		$div = wordwrap($div);

		/*
			$res = mail($email,
			$subject,
			$div,
			"From: pw@peanutworld.com" . "\r\n" . 'MIME-Version: 1.0' . "\r\n". "Content-Type: text/html; charset=utf-8");
		*/
		$email_arr = [];
		if(strpos($email,",")){
			$email_arr = explode(',',$email);
		} else {
			$email_arr[] = $email;
		}
		$res_mail  = sendMail(
			"pw@peanutworld.com",
			"Peanut World",
			$email_arr,
			$subject,
			$div,
			"",
			"jm.peanutworld@gmail.com"
		);

		if($res_mail){
			$wh_order = new Wh_order();
			$wh_not_use = $wh_order->backloadNotUse($member_id);
			if($wh_not_use){

				foreach($wh_not_use as $used){
					$wh_order_details = new Wh_order_details();
					$wh_order_details->update(['is_use' => $payment_id],$used->id);
				}


			}
			echo "Email sent successfully";
		} else {
			echo "There is a problem in sending your message. Please try again.";
		}
	}
	function getBillingData(){

		$member_id = Input::get('member_id');

		$payment_id = Input::get('payment_id');

		$ret_html = Input::get('ret_html');

		$member_email = "";

		if($member_id){

			$member = new Member($member_id);

			$member_email = $member->data()->email;

		}


		echo "<input type='hidden' value='$member_id' id='billing_member_id'>";
		echo "<input type='hidden' value='$payment_id' id='billing_payment_id'>";
		echo "<div class='row'>";
		echo "<div class='col-md-12'>";
		echo "<div class='form-group'>";
		echo "<strong>Email</strong>";
		echo "<input type='text' class='form-control' id='txtBillingEmail' placeholder='Email' value='".$member_email."'>";
		echo "</div>";
		echo "</div>";
		echo "<div class='col-md-12'>";
		echo "<div class='form-group'>";
		echo "<strong>Subject</strong>";
		echo "<input type='text' class='form-control' id='txtBillingSubject'  placeholder='Subject' value='Billing Statement'>";
		echo "</div>";
		echo "</div>";
		echo "<div class='col-md-12'>";
		echo "<div class='form-group'>";
		echo "<strong>Additional Remarks</strong>";
		echo "<textarea class='form-control' placeholder='Remarks' id='txtBillingRemarks'></textarea>";
		echo "</div>";
		echo "</div>";
		echo "<div class='col-md-12'>";
		echo "<div class='form-group'>";
		echo "<h4>Preview</h4>";
		echo "<div id='bill_to_email'>" . $ret_html . "</div>";
		echo "</div>";
		echo "</div>";
		echo "<div class='col-md-12'>";
		echo "<div class='form-group'>";
		echo "<button class='btn btn-default' id='btnSubmitEmail'>Submit</button>";
		echo "</div>";
		echo "</div>";
		echo "</div>";

	}

	function floordec($zahl,$decimals=2){
		return floor($zahl*pow(10,$decimals))/pow(10,$decimals);
	}

	function billingStatementData(){
		$user = new User();
		$payment_id = Input::get('payment_id');
		$member_id = Input::get('member_id');
		$print_type  =  Input::get('type');
		$sales = new Sales();
		$wh_order = new Wh_order();
		$saleslist = $sales->salesTransactionBaseOnPaymentId($payment_id,1,1);
		$wh_order_data = $wh_order->getFullDetailsByPayment($payment_id);
		$company = new Company($user->data()->company_id);
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
			$itemlist_m = [];
			$itemlist_nm = [];
			$con = new Consumable();
			$con_list = $con->get_active('payment_consumable',array('payment_id','=',$payment_id)); // bad order
			$ret_tbl_bad_order ='';
			$total_bad_order = 0;
		//	if(count($con_list) > 0){
				// get bad order

				$wh_order_details= new Wh_order_details();
				$wh_use_in_this_tranc = $wh_order->backloadByUsed($payment_id);
				/*$total_consumable = 0;
				foreach($con_list as $clist){
					$total_consumable += $clist->amount;
				}*/
				if($wh_use_in_this_tranc){

					$ret_tbl_bad_order = "<table class='table table-bordered table-condensed' style='font-size:10px;width:100%;'>";
					$ret_tbl_bad_order .= "<thead><tr ><th colspan='5' class='text-left'>Less</th></tr></thead>";
					$ret_tbl_bad_order .= "<thead><tr><th>Bad Order</th><th>Quantity</th><th>Date</th><th>Charge</th><th>Amount</th></tr></thead>";
					$ret_tbl_bad_order .=  "<tbody>";
					foreach($wh_use_in_this_tranc as $used){

						$adjusted_price = $used->adjusted_price;
						$total = $used->backload_qty * $adjusted_price;
						$ind_adj = $used->member_adjustment / $used->backload_qty;
						$adjusted_price = $adjusted_price + $ind_adj;
						$adjusted_total = $total + $used->member_adjustment;

						/*$total_consumable -= $adjusted_total;
						if($total_consumable < 0){
							continue;
						}*/
						$total_bad_order += $adjusted_total;

						$wh_order_details->update(['is_use' => $payment_id],$used->id);
						$ret_tbl_bad_order .=  "<tr><td>".$used->item_code."</td><td>".formatQuantity($used->backload_qty)."</td><td>".date('m/d/Y',$used->backload_date)."</td><td>" .number_format($adjusted_price,2)."</td><td>" .number_format($adjusted_total,2)."</td></tr>";
					}
					$ret_tbl_bad_order .=  "</tbody>";
					$ret_tbl_bad_order .= "<tfoot><tr><th colspan='4'>Total:</th><th>".number_format($total_bad_order,2)."</th></tr></tfoot>";
					$ret_tbl_bad_order .=  "</table>";
				}
			//}

			$wh_not_use = $wh_order->backloadNotUse($member_id);
			if($wh_not_use){
				$ret_tbl_bad_order = "<table class='table table-bordered table-condensed' style='font-size:10px;width:100%;'>";
				$ret_tbl_bad_order .= "<thead><tr ><th colspan='5' class='text-left'>Less</th></tr></thead>";
				$ret_tbl_bad_order .= "<thead><tr><th>Bad order</th><th>Quantity</th><th>Date</th><th>Charge</th><th>Amount</th></tr></thead>";
				$ret_tbl_bad_order .=  "<tbody>";
				foreach($wh_not_use as $used){
					$adjusted_price = $used->adjusted_price;
					$total = $used->backload_qty * $adjusted_price;
					//$member_adjustement = $used->member_adjustment
					//$ind_adj = $used->member_adjustment / $used->backload_qty;
					$ind_adj = 0;
					$adjusted_price = $adjusted_price + $ind_adj;
					$adjusted_total = $total;

					$total_bad_order += $adjusted_total;

					//$wh_order_details->update(['is_use' => $payment_id],$used->id);
					$ret_tbl_bad_order .=  "<tr><td>".$used->item_code."</td><td>".formatQuantity($used->backload_qty)."</td><td>".date('m/d/Y',$used->backload_date)."</td><td>" .number_format($adjusted_price,2)."</td><td>" .number_format($adjusted_total,2)."</td></tr>";
				}
				$ret_tbl_bad_order .=  "</tbody>";
				$ret_tbl_bad_order .= "<tfoot><tr><th colspan='4'>Total:</th><th>".number_format($total_bad_order,2)."</th></tr></tfoot>";
				$ret_tbl_bad_order .=  "</table>";
			}


			if($wh_order_data->dr == 1096){

				$additional_deduction = "<table class='table table-bordered table-condensed' style='font-size:10px;width:100%;'>";
				$additional_deduction .= "<thead><tr ><th colspan='2' class='text-left'>Special Discount</th></tr></thead>";
				$additional_deduction .= "<thead><tr><th>Item</th><th>Discount</th></tr></thead>";
				$additional_deduction .=  "<tbody>";
				$additional_deduction .= "<tr><td>Spicy Adobo Skin</td><td>34</td></tr>";
				$additional_deduction .= "<tr><td>Spicy Adobo Skinless</td><td>44</td></tr>";
				$additional_deduction .= "<tr><td>Pop Beans</td><td>31</td></tr>";
				$additional_deduction .=  "</tbody>";

				$additional_deduction .= "<tfoot><tr><th colspan='2'>Total:</th><th>".number_format(34+44+31,2)."</th></tr></tfoot>";
				$additional_deduction .=  "</table>";
				$total_bad_order += (34+44+31);
				$ret_tbl_bad_order .= $additional_deduction;
			}

			$branch_destination = 0;
			$payment_id_cur = 0;
			foreach($saleslist as $s){
				$payment_id_cur = $s->payment_id;
				if(isset($s->wh_branch_destionation_id) && $s->wh_branch_destionation_id){
					$branch_destination = $s->wh_branch_destionation_id;
				}
				$membername = ucwords($s->mln . ", " . $s->mfn . " " . $s->mmn);
				$cashiername = ucwords($s->uln . ", " . $s->ufn . " " . $s->umn);
				if($print_type == 1){
					$ctrnum = $s->pref_dr.$s->dr . $s->suf_dr;
				}
				$remarks = $s->premarks;
				$terms = $s->terms;
				$tin_no = $s->tin_no;
				if($s->whbranch){
					$stationname = $s->whbranch;
				} else {
					$stationname = $s->whbranch;
					//$stationname = $s->personal_address;
				}



				$stationid = $s->station_id;
				$stationaddress = $s->station_address;
				$sales_type = $s->sales_type_name;
				$datesold = date('m/d/Y',$s->sold_date);
				$total = ($s->qtys * $s->price) + ($s->adjustment + $s->member_adjustment) - ($s->discount + $s->store_discount);
				$adjusted_price_ind = ($s->price);
				$ind_adj =0;
				$ind_member_adj =0;
				if($s->adjustment){
					$ind_adj = $s->adjustment / $s->qtys;
				}
				if($s->member_adjustment){
					$ind_member_adj = $s->member_adjustment / $s->qtys;
				}

				$n = $s->qtys;
				$whole = floor($n);
				$fraction = $n - $whole;
				$adjustment_to_round = Configuration::getValue('mem_adj_round');
				if($fraction < $adjustment_to_round){
					$adjusted_price_ind = $total / $whole;
				}else{
					$adjusted_price_ind += ($ind_adj + $ind_member_adj);
				}

				$s->qtys = formatQuantity($s->qtys);

				if($s->for_selling == 1){
					$itemlist_nm[] = ['unit_name' => $s->unit_name,'item_code'=>escape($s->item_code),'description'=>escape($s->description), 'barcode'=>escape($s->barcode), 'qty'=>escape($s->qtys), 'price'=>escape($adjusted_price_ind), 'discount'=>escape($s->discount), 'total'=>escape($total), 'for_selling'=>escape($s->for_selling),'category_name' => $s->category_name];
				} else {
					$itemlist_m[] = ['unit_name' => $s->unit_name,'item_code'=>escape($s->item_code),'description'=>escape($s->description), 'barcode'=>escape($s->barcode), 'qty'=>escape($s->qtys), 'price'=>escape($adjusted_price_ind), 'discount'=>escape($s->discount), 'total'=>escape($total), 'for_selling'=>escape($s->for_selling),'category_name' => $s->category_name];
				}


			}
			// member credit

			// total credit , total paid, current charges, amount due

			$member_credit = new Member_credit();
			$credits = $member_credit->getMemberCreditPayment($member_id);
			$arr_credit = [];
			$total_amount = 0;
			$total_amount_paid = 0;
			if($credits){
				$deduction = new Deduction();
				foreach($credits as $credit){
					if($credit->amount ==  $credit->amount_paid ) continue;
					//if($credit->payment_id ==  $payment_id_cur ) continue;
					if((isset($credit->to_branch_id) && $credit->to_branch_id == $branch_destination) || $credit->branch_id == $branch_destination){

						$backload_deduction = $deduction->getDeductedBackload($credit->payment_id);
						if(isset($backload_deduction->total_deduction_backload)){
							$credit->amount = $credit->amount - $backload_deduction->total_deduction_backload;
							$credit->amount_paid = $credit->amount_paid - $backload_deduction->total_deduction_backload;
						}
						$total_amount += $credit->amount;
						$total_amount_paid += $credit->amount_paid;

						// deduct sa begining at payment



					}

				}
			}


			// freight

			$total_freight = 0;
			$total_freight_beg = 0;
			$ret_tbl = "";
			if(is_numeric($payment_id)){
				$freight = new Freight();
				$freights = $freight->getPendingFreight($member_id,$branch_destination);

				if($freights){
					$ret_tbl = "<table class='table table-bordered table-condensed' style='font-size:10px;width:100%;'>";
					$ret_tbl .= "<thead><tr ><th colspan='4' class='text-left'>Additional</th></tr></thead>";
					$ret_tbl .= "<thead><tr><th>Description</th><th>Date</th><th>Charge</th><th>Amount</th></tr></thead>";
					$ret_tbl .=  "<tbody>";
					$total_freight = 0;
					$total_freight_beg = 0;
					$haspaid = false;
					foreach($freights as $f){
						$ispaid="";
						if($f->payment_id != $payment_id){
							if($f->status == 0){
								$total_freight_beg+= ($f->charge + $f->freight_adjustment);

							}

						} else {
							if($f->status == 0){
								$total_freight += ($f->charge + $f->freight_adjustment);
							} else {

								$haspaid = true;
								$ispaid = "<br>(Paid)";
							}

							$ret_tbl .=  "<tr><td>".$f->remarks."</td><td>".date('m/d/Y',$f->created)."</td><td>$f->charge <br> ($f->freight_adjustment)</td><td>".($f->charge + $f->freight_adjustment)." $ispaid </td></tr>";
						}
					}
					$ret_tbl .=  "</tbody>";
					$ret_tbl .= "<tfoot><tr><th colspan='3'>Total:</th><th>".number_format($total_freight,2)."</th></tr></tfoot>";
					$ret_tbl .=  "</table>";
					if($total_freight == 0 && !$haspaid){
						$ret_tbl = "";
					}
				}

			}



			$arr_credit['credit_amount'] = $total_amount + $total_freight_beg;
			$arr_credit['credit_amount_paid'] = $total_amount_paid;


			$remarks = ($remarks) ? $remarks : '';

			$description_to = strtolower($wh_order_data->description_to) ;
			$logo2 = 1;
			if (strpos($description_to, 'crave') !== false) {
				$logo2 = 2;
			} else if (strpos($description_to, 'lemon') !== false) {
				$logo2 = 3;
			}
			$finalarr['member_credit'] = $arr_credit;
			$finalarr['member_name'] = $membername;
			$finalarr['remarks'] = $remarks;
			$finalarr['cashier_name'] = $cashiername;
			$finalarr['station_name'] = $stationname;
			$finalarr['station_id'] = $stationid;
			$finalarr['station_address'] = $stationaddress;
			$finalarr['date_sold'] = $datesold;
			$finalarr['item_list_m'] = $itemlist_m;
			$finalarr['item_list_nm'] = $itemlist_nm;
			$finalarr['sales_type_name'] = $sales_type;
			$finalarr['ctrnum'] = $ctrnum;
			$finalarr['terms'] = $terms;
			$finalarr['tin_no'] = $tin_no;
			$finalarr['logo2'] = $logo2;
			$finalarr['company_address'] = $company->data()->address;
			$finalarr['company_name'] = $company->data()->name;
			$finalarr['company_contact'] = "Telefax # (632) 743-8853, 410-7516";
			$finalarr['company_email'] = "jm.peanutworld@gmail.com";
			$finalarr['company_website'] = "www.peanutworldweb.com";
			$finalarr['statement_date'] = date('F d, Y');

			$finalarr['order_date'] = (isset($wh_order_data->created) && !empty($wh_order_data->created)) ?  date('F d, Y',$wh_order_data->created) : '';
			$finalarr['delivery_date'] = (isset($wh_order_data->is_scheduled) && !empty($wh_order_data->is_scheduled)) ?  date('F d, Y',$wh_order_data->is_scheduled) : '';
			$finalarr['order_id'] = (isset($wh_order_data->id) && !empty($wh_order_data->id)) ? $wh_order_data->id : '';

			$finalarr['total_freight'] = $total_freight;
			$finalarr['total_bad_order'] = $total_bad_order;
			$finalarr['freight_tbl'] = $ret_tbl;
			$finalarr['bad_order_tbl'] = $ret_tbl_bad_order;

			//consumables

			echo json_encode($finalarr);
		}


	}

	function payFreight(){
		$id = Input::get('id');
		$amount = Input::get('amount');
		if($id && is_numeric($id) && $amount && is_numeric($amount)){
			$freight = new Freight($id);
			if($freight->data()->status == 1){
				echo "Invalid request";
			} else {
				$paid_amount = $freight->data()->paid_amount;
				$charge = $freight->data()->charge + $freight->data()->freight_adjustment;

				$dif = $charge - $paid_amount;
				if($dif < $amount){
					echo "Invalid Amount";
				} else {
					$paid_amount = $paid_amount + $amount;
					if($paid_amount == $charge){
						$stat = 1;
					} else {
						$stat = 0;
					}
					$freight->update(['status' => $stat, 'paid_amount' => $paid_amount],$id);
					echo $freight->data()->payment_id;
				}
			}
		} else {
			echo "Invalid request";
		}
	}


	function addFreight() {
		$payment_id = Input::get('payment_id');
		if(is_numeric($payment_id)){
			$freight = new Freight();
			$freights = $freight->get_active('freight_charges',['payment_id','=',$payment_id]);
			if($freights){
				echo "<h4>Freight Charge</h4>";
				foreach($freights as $f){
					$paid_amount =  $f->paid_amount;
					$lbl = "";
					if($f->status == 1){
						$paid_amount = 	$f->charge + $f->freight_adjustment;
						$lbl = "<span class='label label-danger'>Paid</span>";
					}

					echo "
					<div style='border: 1px solid #ccc; padding:5px;margin-top:5px;'>
                    <div class='row'>
                     <div class='col-md-8'><span class='text-danger'>" . escape($f->remarks) . "</span></div>
                     <div class='col-md-4 text-right'>";
					if($f->status != 1){
						echo "<button class='btn btn-default btn-freight-payment' data-id='$f->id' ><i class='fa fa-money'></i></button>";
					}

                    echo "</div>
					</div>


                    <strong class='span-block'>Charge: ".number_format($f->charge+$f->freight_adjustment,2)." $lbl</strong>
                    <strong class='span-block'>Paid Amount: ". number_format($paid_amount,2)."</strong>
                    <strong class='span-block'>Remaining:  ".number_format($f->charge+$f->freight_adjustment-$paid_amount,2)."</strong>
                    </div>";

				}

			} else {
				echo "<div class='alert alert-info'>No record yet.</div>";
			}
		} else {
			echo "<div class='alert alert-danger'>Invalid request.</div>";
		}

	}
	function saveFreight(){
		$payment_id = Input::get('payment_id');
		$amount = Input::get('amount');
		$remarks = Input::get('remarks');
		$adjustment = Input::get('adjustment');
		$freight = new Freight();
		$user = new User();
		$now = time();
		if(is_numeric($payment_id) && is_numeric($amount) && $remarks){
			$adjustment = ($adjustment) ? $adjustment :0;
			$freight->create([
				'company_id' => $user->data()->company_id,
				'is_active' =>1,
				'remarks' =>$remarks,
				'charge' =>$amount,
				'freight_adjustment' =>$adjustment,
				'payment_id' =>$payment_id,
				'created' =>$now
			]);
			echo "Freight Added successfully.";
		} else {
			echo "Invalid data.";
		}
	}

	function getPrevListItem(){
		$payment_id = Input::get('id');
		$sale = new Sales();
		$sales = $sale->salesTransactionBaseOnPaymentId($payment_id);
		if($sales){
			$ret = "";
			foreach($sales as $s){
				$price_adjustment = $s->adjustment / $s->qtys;
				$total = (($price_adjustment + $s->price) * $s->qtys) + $s->member_adjustment;
				$qty = formatQuantity($s->qtys);
				$ret .= "<tr data-is_bundle='".$s->is_bundle."' data-unit_name='".$s->unit_name."' data-barcode='".$s->barcode."' data-itemcode='".$s->item_code."' data-desc='".$s->description."' data-item_id='".$s->item_id."' data-price_adjustment='".$price_adjustment."' data-member_adjustment='".$s->member_adjustment."'><td>".$s->item_code."</td><td>".$qty."</td><td>". ($s->price + $price_adjustment)."</td><td>".$s->member_adjustment."</td><td>".$total."</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>";
			}
			echo $ret;
		}
	}


	function getByCategory(){

		$user = new User();
		$sales = new Sales();
		$branch_name = "";
		$dl = Input::get('dl');
		$border="";
		if($dl == 1){
			$filename = "reports-" . date('m-d-Y-H-i-s-A') . ".xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");
			$border = "border=1";
		}
		if(Input::get('dt') &&  Input::get('branch_id')){
			$dt = Input::get('dt');
			$ex = explode("-",$dt);
			$branch_id = Input::get('branch_id');
			$b_cls = new Branch($branch_id);
			$month = (int) $ex[0];
			$year = (int) $ex[1];
			$branch_name = $b_cls->data()->name;

		} else {
			$month = (int) (date('m'));
			$year = date('Y');
			$branch_id = 0;

		}

		$list = $sales->getCategorySummary($user->data()->company_id,$month,$year,$branch_id);

		$dt = $month . "/01/". $year;
		$dtlast = strtotime($dt . "1 month -1 sec");
		$last_day = date('j',$dtlast);


		$branch_expense = new Branch_expense();
		$branch_expenses = $branch_expense->getExpenseSummary($user->data()->company_id,$month,$year,$branch_id);

		$data_expense=[];
		if($branch_expenses){
			foreach($branch_expenses as $ex){
				$data_expense[$ex->d] = $ex->totalamount;
			}
		}

		$dt_dep_from = $month . "/02/" . $year;
		if($month == 12){
			$dt_dep_to =  "01/01/" . ($year+1);
		} else {
			$dt_dep_to = ($month+1) . "/01/" . $year;
		}


		$deposit = new Dicer_deposit();
		$deposits = $deposit->getDicerDepositSummary($user->data()->company_id,$dt_dep_from,$dt_dep_to,$branch_id);

		$data_deposits=[];
		if($deposits){
			foreach($deposits as $dep){
				$data_deposits[$dep->d] = $dep->totalamount;
			}
		}

		$bad_order_cls = new Bad_order();
		$badOrders = $bad_order_cls->getBadOrderSummary($user->data()->company_id,$dt_dep_from,$dt_dep_to,$branch_id);

		$data_bad_order=[];
		if($badOrders){
			foreach($badOrders as $dep){
				$data_bad_order[$dep->d] = $dep->totalamount;
			}
		}

		$sms_receive = new Sms_receive();

		$sms_summary = $sms_receive->getSummary($branch_id,strtotime($dt_dep_from),strtotime($dt_dep_to));
		$arr_in_charge = [];
		if($sms_summary){
			foreach($sms_summary as $smsSum){
				$d = date('j', strtotime($smsSum->date_received));
				$arr_in_charge[$d] = $smsSum->name;
			}
		}


		$sm_bottled_water_id = 121;
		$sm_bottled_amount = 20;
		$sm_bottled_amount_cost = 8.91;

		$wh_order = new Wh_order();
		$sm_bottled_order = $wh_order->getItemOrder($month,$year,$sm_bottled_water_id,$branch_id);

		$arr_bottled = [];
		if($sm_bottled_order){
			foreach($sm_bottled_order as $indsm){
				$arr_bottled[$indsm->d] = ($indsm->totalquantity * $sm_bottled_amount_cost);
			}
		}

		?>
		<p>
			<?php
				echo "Date: <span class='text-danger'>" . $month . "-" . $year . "</span>";
				echo " ";
				echo ($branch_name) ? "Branch:  <span class='text-danger'>" . $branch_name."</span>" : "";
			?>
		</p>
		<?php

		$arr_categ = [];
		$arr_purchase = [];
		$data=[];

		foreach($list as $l){
			if(!in_array($l->category_name,$arr_categ)){
				$arr_categ[] = $l->category_name;
			}
			$data[$l->d][$l->category_name] = $l->totalamount;
			$arr_purchase[$l->d][$l->category_name] = $l->purchase_price;
		}

		echo "<div class='table-responsive'>";
		echo "<table $border class='table table-bordered table-condensed' id='tblForApproval' style='font-size:9px;'>";
		echo "<thead>";
		$tr_head = "";
		$tr_head.= "<tr>";
		$tr_head.= "<th>Day</th>";

		$total_gross = 0;
		$total_net = 0;
		$total_expense = 0;
		$total_deposit = 0;
		$total_purchases = 0;
		$total_badorder = 0;
		$total_short_over = 0;

		$arr_total = [];
		$arr_total_supplier = [];
		$categ_sales_num = 0;
		foreach($arr_categ as $categ){
			$categ_sales_num++;
			$tr_head.= "<th class='text-right'>$categ</th>";
		}

		$tr_head.= "<th class='text-right'>Gross Sales</th>";
		$tr_head.= "<th class='text-right'>Site Expense</th>";
		$tr_head.= "<th class='text-right'>Net Sales</th>";
		$tr_head.= "<th class='text-right'>Deposit</th>";

		$tr_head.= "<th class='text-right'>Purchases</th>";
		$tr_head.= "<th class='text-right'>Borrowed Items</th>";
		$categ_capital_num=0;
		foreach($arr_categ as $categ){
			if($categ != 'Drinks') {
				$categ_capital_num++;
				$tr_head.= "<th class='text-right'>$categ</th>";
			}
		}
		$tr_head.= "<th class='text-right'>Bad Order</th>";
		$tr_head.= "<th class='text-right'>Short/Over</th>";
		$tr_head.= "<th class='text-right'>In-charge</th>";
		$tr_head.= "</tr>";
		echo "<tr><th></th><th colspan='$categ_sales_num'>Sales</th><th></th><th></th><th></th><th></th><th></th><th></th><th colspan='$categ_capital_num'>Capital</th><th></th><th></th><th></th></tr>";
		echo $tr_head;
		echo "</thead>";
		echo "<tbody>";
		for($i=1;$i<=$last_day;$i++){
			echo "<tr>";
			echo "<td>$i</td>";
			$total = 0;
			$non_mdse= 0;
			$purchases = isset($arr_bottled[$i]) ? $arr_bottled[$i] : 0;
			$borrowed_items=0;
			$bad_order = 0;
			$short_over= 0;
			$in_charge ='';
			foreach($arr_categ as $categ){
				$v = isset($data[$i][$categ]) ? $data[$i][$categ] : 0;

				if(isset($arr_total[$categ])){
					$arr_total[$categ] += $v;
				} else {
					$arr_total[$categ] = $v;
				}
				echo "<td class='text-right'>". number_format($v,2)."</td>";
				$total += $v;
			}
			$expense = isset($data_expense[$i]) ? $data_expense[$i] : 0;
			if($i == $last_day){
				$dicer_deposit = isset($data_deposits[1]) ? $data_deposits[1] : 0;
			} else {
				$dicer_deposit = isset($data_deposits[$i+1]) ? $data_deposits[$i+1] : 0;
			}

			$bad_order_day = isset($data_bad_order[$i]) ? $data_bad_order[$i] : 0;
			$in_charge = isset($arr_in_charge[$i]) ? $arr_in_charge[$i] : '';


			$net = $total - $expense;
			$short_over = $net - $dicer_deposit;

			$total_badorder+= $bad_order_day;
			$total_gross += $total;
			$total_expense += $expense;
			$total_net += $net;
			$total_deposit += $dicer_deposit;
			$total_purchases += $purchases;
			$total_short_over +=$short_over;

			echo "<td class='text-right'>" . number_format($total,2) . "</td>";
			echo "<td class='text-right'>" . number_format($expense,2) . "</td>";
			echo "<td class='text-right'>" . number_format($net,2) . "</td>";
			echo "<td class='text-right'>" . number_format($dicer_deposit,2) . "</td>";

			echo "<td class='text-right'>" . number_format($purchases,2) . "</td>";
			echo "<td class='text-right'>" . number_format($borrowed_items,2) . "</td>";

			foreach($arr_categ as $categ){

				$pp = isset($arr_purchase[$i][$categ]) ? $arr_purchase[$i][$categ] : 0;
				if($categ != 'Drinks') {
					if(isset($arr_total_supplier[$categ])){
						$arr_total_supplier[$categ] += $pp;
					} else {
						$arr_total_supplier[$categ] = $pp;
					}
					echo "<td class='text-right'>". number_format($pp,2)."</td>";
				}
			}

			echo "<td class='text-right'>" . number_format($bad_order_day,2) . "</td>";
			echo "<td class='text-right'>" . number_format($short_over,2) . "</td>";
			echo "<td class='text-right'>" . $in_charge . "</td>";
			echo "</tr>";
		}
		echo "</tbody>";
		echo "<tfoot>";
		echo "<tr class='text-danger'>";
		echo "<th></th>";
		foreach($arr_categ as $categ){
			$pp = isset($arr_total[$categ]) ? $arr_total[$categ] : 0;
			echo "<th class='text-right'>".number_format($pp,2)."</th>";
		}
		echo "<th class='text-right'>" . number_format($total_gross,2) . "</th>";
		echo "<th class='text-right'>" . number_format($total_expense,2) . "</th>";
		echo "<th class='text-right'>" . number_format($total_net,2) . "</th>";
		echo "<th class='text-right'>" . number_format($total_deposit,2) . "</th>";
		echo "<th class='text-right'>" . number_format($total_purchases,2) . "</th>";
		echo "<th class='text-right'>" . number_format(0,2) . "</th>";

		foreach($arr_categ as $categ){

			$pp = isset($arr_total_supplier[$categ]) ? $arr_total_supplier[$categ] : 0;
			if($categ != 'Drinks') {
				echo "<th class='text-right'>". number_format($pp,2)."</th>";
			}

		}

		echo "<th class='text-right'>" . number_format($total_badorder,2) . "</th>";
		echo "<th class='text-right'>" . number_format($total_short_over,2) . "</th>";
		echo "<th class='text-right'></th>";
		echo "</tr>";
		echo "</tfoot>";
		echo "</table>";
		echo "</div>";

		?>
		<?php
	}

	function getDaily(){

		$user = new User();
		$sales = new Sales();
		$branch_name = "";
		$dl = Input::get('dl');
		$border="";
		if($dl == 1){
			$filename = "reports-" . date('m-d-Y-H-i-s-A') . ".xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");
			$border = "border=1";
		}
		if(Input::get('dt') &&  Input::get('branch_id')){
			$dt = Input::get('dt');
			$ex = explode("-",$dt);
			$branch_id = Input::get('branch_id');
			$b_cls = new Branch($branch_id);
			$month = (int) $ex[0];
			$year = (int) $ex[1];
			$branch_name = $b_cls->data()->name;

		} else {
			$month = (int) (date('m'));
			$year = date('Y');
			$branch_id = 0;

		}

		$list = $sales->getDailySummary($user->data()->company_id,$month,$year,$branch_id);

		$dt = $month . "/01/". $year;
		$dtlast = strtotime($dt . "1 month -1 sec");
		$last_day = date('j',$dtlast);


		?>
		<div id='print_me'>
		<p class='text-center'>
			<?php
				echo "Date: <span class='text-danger'>" . $month . "-" . $year . "</span>";
				echo " ";
				echo ($branch_name) ? "Branch:  <span class='text-danger'>" . $branch_name."</span>" : "";
			?>
		</p>
		<?php

		//jump1
		$data=[];
		$data_cash= [];
		$data_cheque= [];
		$data_credit= [];
		$data_bt= [];
		$data_deduction= [];
		$data_member_credit= [];

		foreach($list as $l){
			if(isset($data[$l->d])){
				$data[$l->d] += $l->totalamount;
			} else {
				$data[$l->d] = $l->totalamount;
			}
			if(isset($data_cash[$l->d])){
				$data_cash[$l->d] += $l->cash_amount;
			} else {
				$data_cash[$l->d] = $l->cash_amount;
			}
			if(isset($data_cheque[$l->d])){
				$data_cheque[$l->d] += $l->cheque_amount;
			} else {
				$data_cheque[$l->d] = $l->cheque_amount;
			}
			if(isset($data_credit[$l->d])){
				$data_credit[$l->d] += $l->credit_card_amount;
			} else {
				$data_credit[$l->d] = $l->credit_card_amount;
			}
			if(isset($data_bt[$l->d])){
				$data_bt[$l->d] += $l->bt_amount;
			} else {
				$data_bt[$l->d] = $l->bt_amount;
			}
			if(isset($data_deduction[$l->d])){
				$data_deduction[$l->d] += $l->deduction_amount;
			} else {
				$data_deduction[$l->d] = $l->deduction_amount;
			}
			if(isset($data_member_credit[$l->d])){
				$data_member_credit[$l->d] += $l->member_amount;
			} else {
				$data_member_credit[$l->d] = $l->member_amount;
			}



		}
		echo "<div class='row'>";
		echo "<div class='col-md-2'>";
		echo "</div>";
		echo "<div class='col-md-8'>";
		echo "<div class='table-responsive'>";
		echo "<table $border class='table table-bordered table-condensed' id='tblForApproval' style='font-size:9px;'>";
		echo "<thead>";
		$tr_head = "";
		$tr_head.= "<tr>";
		$tr_head.= "<th>Day</th>";

			$total_gross = 0;
			$total_cash = 0;
			$total_credit = 0;
			$total_cheque = 0;
			$total_bt = 0;
			$total_member = 0;
			$total_deduction = 0;

		$tr_head.= "<th class='text-right'>Cash</th>";
		$tr_head.= "<th class='text-right'>Cheque</th>";
		$tr_head.= "<th class='text-right'>Credit Card</th>";
		$tr_head.= "<th class='text-right'>Bank Transfer</th>";
		$tr_head.= "<th class='text-right'>Member Credit</th>";
		$tr_head.= "<th class='text-right'>Deduction</th>";
		$tr_head.= "<th class='text-right'>Gross Sales</th>";

		echo $tr_head.= "</tr>";
		echo "</thead>";
		echo "<tbody>";

		for($i=1;$i<=$last_day;$i++){

			echo "<tr>";
			echo "<td>$i</td>";
			$total = isset($data[$i]) ? $data[$i] :0;
			$cash = isset($data_cash[$i]) ? $data_cash[$i] :0;
			$credit = isset($data_credit[$i]) ? $data_credit[$i] :0;
			$cheque = isset($data_cheque[$i]) ? $data_cheque[$i] :0;
			$bt = isset($data_bt[$i]) ? $data_bt[$i] :0;
			$member_credit = isset($data_member_credit[$i]) ? $data_member_credit[$i] :0;
			$deduction = isset($data_deduction[$i]) ? $data_deduction[$i] :0;



			$total_gross += $total;

			$total_cash += $cash;
			$total_cheque += $cheque;
			$total_credit += $credit;
			$total_bt += $bt;
			$total_member += $member_credit;
			$total_deduction += $deduction;

			echo "<td class='text-right'>" . number_format($cash,2) . "</td>";
			echo "<td class='text-right'>" . number_format($cheque,2) . "</td>";
			echo "<td class='text-right'>" . number_format($credit,2) . "</td>";
			echo "<td class='text-right'>" . number_format($bt,2) . "</td>";
			echo "<td class='text-right'>" . number_format($member_credit,2) . "</td>";
			echo "<td class='text-right'>" . number_format($deduction,2) . "</td>";
			echo "<td class='text-right text-danger'><strong>" . number_format($total,2) . "</strong></td>";

			echo "</tr>";
		}
		echo "</tbody>";
		echo "<tfoot><tr><th >Total</th><th class='text-right'>".number_format($total_cash,2)."</th><th class='text-right'>".number_format($total_cheque,2)."</th><th class='text-right' >".number_format($total_credit,2)."</th><th class='text-right'>".number_format($total_bt,2)."</th><th class='text-right'>".number_format($total_member,2)."</th><th class='text-right'>".number_format($total_deduction,2)."</th><th class='text-danger text-right'>".number_format($total_gross,2)."</th></tr></tfoot>";

		echo "</table>";
		echo "</div>";
		echo "</div>";
		echo "<div class='col-md-2'>";
		echo "</div>";

			echo "</div>";
		?>
		</div>
		<?php
	}


	function getSalesTypeSummary(){

		$user = new User();
		$sales = new Sales();
		$branch_name = "";
		$dl = Input::get('dl');
		$date_type = Input::get('date_type');
		$border="";
		if($dl == 1){
			$filename = "reports-" . date('m-d-Y-H-i-s-A') . ".xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");
			$border = "border=1";
		}
		if(Input::get('dt')){
			$dt = Input::get('dt');
			$ex = explode("-",$dt);
			$branch_id = Input::get('branch_id');
			$b_cls = new Branch($branch_id);
			$month = (int) $ex[0];
			$year = (int) $ex[1];
			$branch_name = $b_cls->data()->name;

		} else {
			$month = (int)(date('m'));
			$year = date('Y');
		}
		if(Input::get('branch_id')){
			$branch_id = Input::get('branch_id');
			$b_cls = new Branch($branch_id);
			$branch_name = $b_cls->data()->name;
		} else {
			$branch_id = 0;
		}

		$list = $sales->getSTSummary($user->data()->company_id,$month,$year,$branch_id,$date_type);

		?>
		<div id='print_me'>
			<p class='text-center'>
				<?php
					echo "Date: <span class='text-danger'>" . $month . "-" . $year . "</span>";
					echo " ";
					echo ($branch_name) ? "Branch:  <span class='text-danger'>" . $branch_name."</span>" : "";
				?>
			</p>
			<?php


				$data=[];
				$data_cash= [];
				$data_cheque= [];
				$data_credit= [];
				$data_bt= [];
				$data_deduction= [];
				$data_member_credit= [];
				$arr_type = [];

				foreach($list as $l){
					$l->sales_type_name = ($l->sales_type_name) ? $l->sales_type_name : 'No type';
					if(!in_array($l->sales_type_name,$arr_type)) $arr_type[] = $l->sales_type_name;
					$l->d = $l->sales_type_name; // tamad baguhin variable
					if(isset($data[$l->d])){
						$data[$l->d] += $l->totalamount;
					} else {
						$data[$l->d] = $l->totalamount;
					}
					if(isset($data_cash[$l->d])){
						$data_cash[$l->d] += $l->cash_amount;
					} else {
						$data_cash[$l->d] = $l->cash_amount;
					}
					if(isset($data_cheque[$l->d])){
						$data_cheque[$l->d] += $l->cheque_amount;
					} else {
						$data_cheque[$l->d] = $l->cheque_amount;
					}

					if(isset($data_credit[$l->d])){
						$data_credit[$l->d] += $l->credit_card_amount;
					} else {
						$data_credit[$l->d] = $l->credit_card_amount;
					}

					if(isset($data_bt[$l->d])){
						$data_bt[$l->d] += $l->bt_amount;
					} else {
						$data_bt[$l->d] = $l->bt_amount;
					}
					if(isset($data_deduction[$l->d])){
						$data_deduction[$l->d] += $l->deduction_amount;
					} else {
						$data_deduction[$l->d] = $l->deduction_amount;
					}
					if(isset($data_member_credit[$l->d])){
						$data_member_credit[$l->d] += $l->member_amount;
					} else {
						$data_member_credit[$l->d] = $l->member_amount;
					}


				}
				echo "<div class='row'>";
				echo "<div class='col-md-2'>";
				echo "</div>";
				echo "<div class='col-md-8'>";
				echo "<div class='table-responsive'>";
				echo "<table $border class='table table-bordered table-condensed' id='tblForApproval' style='font-size:9px;'>";
				echo "<thead>";
				$tr_head = "";
				$tr_head.= "<tr>";
				$tr_head.= "<th>Day</th>";

				$total_gross = 0;
				$total_cash = 0;
				$total_credit = 0;
				$total_cheque = 0;
				$total_bt = 0;
				$total_member = 0;
				$total_deduction = 0;

				$tr_head.= "<th class='text-right'>Cash</th>";
				$tr_head.= "<th class='text-right'>Cheque</th>";
				$tr_head.= "<th class='text-right'>Credit Card</th>";
				$tr_head.= "<th class='text-right'>Bank Transfer</th>";
				$tr_head.= "<th class='text-right'>Member Credit</th>";
				$tr_head.= "<th class='text-right'>Deduction</th>";
				$tr_head.= "<th class='text-right'>Gross Sales</th>";

				echo $tr_head.= "</tr>";
				echo "</thead>";
				echo "<tbody>";
				foreach($arr_type as $type){
					echo "<tr>";
					echo "<td>$type</td>";
					$i = $type;


					$total = isset($data[$i]) ? $data[$i] :0;
					$cash = isset($data_cash[$i]) ? $data_cash[$i] :0;
					$credit = isset($data_credit[$i]) ? $data_credit[$i] :0;
					$cheque = isset($data_cheque[$i]) ? $data_cheque[$i] :0;
					$bt = isset($data_bt[$i]) ? $data_bt[$i] :0;
					$member_credit = isset($data_member_credit[$i]) ? $data_member_credit[$i] :0;
					$deduction = isset($data_deduction[$i]) ? $data_deduction[$i] :0;


					$total_gross += $total;
					$total_cash += $cash;
					$total_cheque += $cheque;
					$total_credit += $credit;
					$total_bt += $bt;
					$total_member += $member_credit;
					$total_deduction += $deduction;

					echo "<td class='text-right'>" . number_format($cash,2) . "</td>";
					echo "<td class='text-right'>" . number_format($cheque,2) . "</td>";
					echo "<td class='text-right'>" . number_format($credit,2) . "</td>";
					echo "<td class='text-right'>" . number_format($bt,2) . "</td>";
					echo "<td class='text-right'>" . number_format($member_credit,2) . "</td>";
					echo "<td class='text-right'>" . number_format($deduction,2) . "</td>";
					echo "<td class='text-right'>" . number_format($total,2) . "</td>";

					echo "</tr>";
				}
				echo "</tbody>";
				echo "<tfoot><tr><th>Total</th><th>".number_format($total_cash,2)."</th><th>".number_format($total_cheque,2)."</th><th>".number_format($total_credit,2)."</th><th>".number_format($total_bt,2)."</th><th>".number_format($total_member,2)."</th><th>".number_format($total_deduction,2)."</th><th class='text-danger text-right'>".number_format($total_gross,2)."</th></tr></tfoot>";

				echo "</table>";
				echo "</div>";
				echo "</div>";
				echo "<div class='col-md-2'>";
				echo "</div>";

				echo "</div>";
			?>
		</div>
		<?php
	}
	function freebieSummary(){

		$user = new User();
		$sales = new Sales();
		$branch_name = "";
		$dl = Input::get('dl');
		$border="";
		if($dl == 1){
			$filename = "freebie-" . date('m-d-Y-H-i-s-A') . ".xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");
			$border = "border=1";
		}
		if(Input::get('branch_id')){
			$branch_id = Input::get('branch_id');
			$b_cls = new Branch($branch_id);
			$branch_name = $b_cls->data()->name;
		} else {
			$branch_id = 0;
		}
		if(Input::get('dt')){
			$dt = Input::get('dt');
			$ex = explode("-",$dt);
			$month = (int) $ex[0];
			$year = (int) $ex[1];

		} else {
			$month = (int) (date('m'));
			$year = date('Y');

		}

		$list = $sales->getFreebieSummary($month,$year,$branch_id);

		?>
		<div id='print_me'>
			<p class='text-center'>
				<?php
					echo "Date: <span class='text-danger'>" . $month . "-" . $year . "</span>";
					echo " ";
					echo ($branch_name) ? "Branch:  <span class='text-danger'>" . $branch_name."</span>" : "";
				?>
			</p>
			<?php if($list){
				?>

			<table class="table table-bordered" <?php echo $border; ?>>
				<thead>
					<tr><th>Ctrl #</th><th>Client</th><th>Item</th><th>Qty</th><th>Created</th></tr>
				</thead>
				<tbody>
				<?php
					foreach($list as $l) {
						$ctrl = "";
						$invoice = "N/A";
						$dr =  "N/A";
						$pr = "N/A";
						if($l->invoice){
							$invoice = $l->invoice;
						}
						if($l->dr){
							$dr = $l->dr;
						}
						if($l->pr){
							$pr = $l->pr;
						}
						?>
						<tr>
							<td style='border-top: 1px solid #ccc;'>
								<span class='span-block'><strong>Invoice: </strong> <span><?php echo $invoice; ?></span></span>
								<span class='span-block'><strong>DR: </strong> <span><?php echo $dr; ?></span></span>
								<strong>PR: </strong> <span><?php echo $pr; ?></span></span>
							</td>
							<td style='border-top: 1px solid #ccc;'>
								<span class='text-danger'><?php echo $l->member_name; ?></span>
							</td>
							<td style='border-top: 1px solid #ccc;'>
								<span><?php echo $l->item_code; ?></span>
								<small class='span-block text-muted'><?php echo $l->description; ?></small>
							</td>
							<td style='border-top: 1px solid #ccc;'>
								<span><?php echo formatQuantity($l->qty); ?></span>
							</td>
							<td style='border-top: 1px solid #ccc;'>
								<span><?php echo date('m/d/Y',$l->created); ?></span>
							</td>
						</tr>
						<?php
					}
				?>
				</tbody>
			</table>
				<?php
			} else {
				?>
				<div class="alert alert-info">No record found.</div>
			<?php
			}?>

		</div>
		<?php
	}
	function freebieSummaryYear(){

		$user = new User();
		$sales = new Sales();
		$branch_name = "";
		$dl = Input::get('dl');
		$border="";
		if($dl == 1){
			$filename = "freebie-" . date('m-d-Y-H-i-s-A') . ".xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");
			$border = "border=1";
		}
		if(Input::get('branch_id')){
			$branch_id = Input::get('branch_id');
			$b_cls = new Branch($branch_id);
			$branch_name = $b_cls->data()->name;
		} else {
			$branch_id = 0;
		}
		if(Input::get('dt')){
			$year = Input::get('dt');


		} else {

			$year = date('Y');

		}

		$list = $sales->getFreebieSummaryYear($year,$branch_id);

		?>
		<div id='print_me'>
			<p class='text-center'>
				<?php
					echo "Date: <span class='text-danger'>" . $year . "</span>";
					echo " ";
					echo ($branch_name) ? "Branch:  <span class='text-danger'>" . $branch_name."</span>" : "";
				?>
			</p>
			<?php if($list){
				?>

			<table class="table table-bordered" <?php echo $border; ?>>
				<thead>
					<tr>
						<th>Item</th>
						<?php
							for($i=1;$i<=12;$i++){
								$dateObj   = DateTime::createFromFormat('!m', $i);
								$monthName = $dateObj->format('F');
								echo "<th>$monthName</th>";
							}
						?>
						<th></th>
					</tr>
				</thead>
				<tbody>
				<?php
					$arr_data = [];
					$arr_items = [];
					foreach($list as $l) {
						if(!isset($arr_items[$l->item_code])){
							$arr_items[$l->item_code] = $l->description;
						}
						$arr_data[$l->item_code][$l->m] = $l->tqty;
					}
					foreach($arr_items as $item_code => $description){

						echo "<tr>";
						echo "<td style='border-top:1px solid #ccc;'>$item_code</td>";
						$item_total = 0;
						for($i=1;$i<=12;$i++){
							$qtyv = isset($arr_data[$item_code][$i]) ? $arr_data[$item_code][$i] : 0;
							$item_total += $qtyv;
							echo "<td style='border-top:1px solid #ccc;'>" . formatQuantity($qtyv). "</td>";
						}
						echo "<td style='border-top:1px solid #ccc;'>" . formatQuantity($item_total). "</td>";
						echo "</tr>";
					}
				?>
				</tbody>
			</table>
				<?php
			} else {
				?>
				<div class="alert alert-info">No record found.</div>
			<?php
			}?>

		</div>
		<?php
	}
	function getDeductionDetailed(){
		$branch_id = Input::get('branch_id');
		$type = Input::get('type');
		$date_type = Input::get('date_type');
		$dt = Input::get('dt');
		$dl = Input::get('dl');
		$ex = explode('-',$dt);
		$dt1 = $ex[0]. "/01/".$ex[1];
		$dt2 = strtotime($dt1. "1 month -1 min");
		$dt2= date('m/d/Y',$dt2);
		$limit = 1000;
		$page = 0;
		$user = new User();
		$cid = $user->data()->company_id;
		$cls = new Deduction();
		if($dl == 1){
			$filename = "deduction-detailed-" . date('m-d-Y-H-i-s-A') . ".xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");
			$border = "border=1";
		}

		$list = $cls->get_active_record($cid,0,$limit,'',$dt1,$dt2,$type,$branch_id,0,$date_type);

		if($list){
			echo "<br><p $border class='text-center'>". strtoupper($type)."</p>";
			echo "<table $border class='table table-bordered' id='tblForApproval'>";
			echo "<thead>";
			echo "<tr><th>Member</th><th>CR #</th><th>CR Date</th><th>DR/SI</th><th>DR/SI Date</th><th>Amount</th></tr>";
			echo "</thead>";
			echo "<tbody>";
			$last = "";
			$total_sub = 0;
			foreach($list as $l){
				$ctrl = "";
				$l->sales_type_name = ($l->sales_type_name) ? $l->sales_type_name :'No type';
				if($l->invoice){
					$ctrl .= $l->invoice.",";
				}
				if($l->dr){
					$ctrl .= $l->dr.",";
				}
				if($l->ir){
					$ctrl .= $l->ir.",";
				}
				$ctrl = rtrim($ctrl,",");
				$cr_number='';
				$cr_date= '';
				if($l->cr_number){
					$cr_number = $l->cr_number;
				}
				if($l->cr_date){
					$cr_date =date('m/d/Y',$l->cr_date);
				}

				if($last != $l->sales_type_name){
					if($total_sub){
						echo "<tr><td colspan='6' class='text-right'>".number_format($total_sub,2)."</td></tr>";
						$total_sub = 0;
					}
					echo "<tr><td colspan='6' class='text-danger'>$l->sales_type_name</td></tr>";
				}
				$total_sub += $l->amount;
				$last = $l->sales_type_name;
				echo "<tr>";
				echo "<td>".$l->member_name."</td>";
				echo "<td>".$cr_number."</td>";
				echo "<td>".$cr_date."</td>";
				echo "<td>".$ctrl."</td>";
				echo "<td>".date('m/d/Y',$l->sold_date)."</td>";
				echo "<td class='text-right'>".number_format($l->amount,2)."</td>";
				echo "</tr>";
			}
			if($total_sub){
				echo "<tr><td colspan='6' class='text-right'>".number_format($total_sub,2)."</td></tr>";
				$total_sub = 0;
			}
			echo "</tbody>";
			echo "</table>";
		} else {
			echo "<br><div class='alert alert-danger'>No record found.</div>";
		}


	}
function getDeductionSummary(){

		$user = new User();
		$deduction = new Deduction();
		$branch_name = "";
		$dl = Input::get('dl');
		$date_type = Input::get('date_type');
		$border="";
		if($dl == 1){
			$filename = "deduction-summary-" . date('m-d-Y-H-i-s-A') . ".xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");
			$border = "border=1";
		}
		if(Input::get('dt') &&  Input::get('branch_id')){

			$dt = Input::get('dt');
			$ex = explode("-",$dt);
			$branch_id = Input::get('branch_id');
			$b_cls = new Branch($branch_id);
			$month = (int) $ex[0];
			$year = (int) $ex[1];
			$branch_name = $b_cls->data()->name;

		} else {

			$month = (int) (date('m'));
			$year = date('Y');
			$branch_id = 0;

		}

		$list = $deduction->getSummaryBySalestype($month,$year,$branch_id,$date_type);

		?>
		<div id='print_me'>
			<p class='text-center'>
				<?php
					echo "Date: <span class='text-danger'>" . $month . "-" . $year . "</span>";
					echo " ";
					echo ($branch_name) ? "Branch:  <span class='text-danger'>" . $branch_name."</span>" : "";
				?>
			</p>
			<?php

				if($list){


				$data=[];
				$all_remarks = [];
				$all_types = [];


				foreach($list as $l){


					$l->sales_type_name = $l->sales_type_name ? $l->sales_type_name : 'No Type';
					$l->remarks = $l->remarks ? $l->remarks : 'No Remarks';

					if(!in_array($l->remarks,$all_remarks)) $all_remarks[] = $l->remarks;
					if(!in_array($l->sales_type_name,$all_types)) $all_types[] = $l->sales_type_name;

					$data[$l->sales_type_name][$l->remarks] = $l->total_deduction;


				}

				echo "<div class='row'>";

				echo "<div class='col-md-12'>";
				echo "<div class='table-responsive'>";
				echo "<table $border class='table table-bordered table-condensed' id='tblForApproval' style='font-size:9px;'>";
				echo "<thead>";
				$tr_head = "";
				$tr_head.= "<tr>";
				$tr_head.= "<th>Type Name</th>";
				$tr_head.= "<th>Total Amount</th>";
				foreach($all_remarks as $rem){
					$tr_head.= "<th class='text-right'>$rem</th>";
				}

				$tr_head.= "</tr>";
				echo $tr_head;

				echo "</thead>";
				echo "<tbody>";
				$sum_type = [];
				$all_total = 0;
				foreach($all_types as $type){
					$tds = "";
					$total_row = 0;
					foreach($all_remarks as $rem){
						$a =isset($data[$type][$rem]) ? $data[$type][$rem] : 0;
						if(isset($sum_type[$rem])){
							$sum_type[$rem] += number_format($a,2,".","");
						} else {
							$sum_type[$rem] = number_format($a,2,".","");
						}
						$total_row += $a;
						$all_total += $a;
						$tds .=  "<td class='text-right'>".number_format($a,2)."</td>";
					}

					$tr_row =  "<tr>";
					$tr_row .= "<td>$type</td>";
					$tr_row .= "<td>". number_format($total_row,2). "</td>";
					$tr_row .= $tds;
					$tr_row .=  "</tr>";
					echo $tr_row;

				}

				echo "</tbody>";

				$tr_foot = "<tr>";
				$tr_foot.= "<th>Type Name</th>";
				$tr_foot.= "<th>".number_format($all_total,2)."</th>";
				foreach($all_remarks as $rem){
					$tr_foot.= "<th class='text-right'>". number_format($sum_type[$rem],2)."</th>";
				}

				 $tr_foot.= "</tr>";
				echo $tr_foot;
				echo "</table>";
				echo "</div>";
				echo "</div>";
				echo "</div>";

				} else {
					echo "No record found.";
				}
			?>
		</div>
		<?php
	}

	function getRequestNew(){
		$wh_order = new Wh_order();
		$user = new User();
		$cid = $user->data()->company_id;
		$wh_results = $wh_order->get_record($cid,0,10,'',0,0,0,0,0,0,0);

		$arr_wh = [];
		$arr_service = [];
		$arr_terms = [];

		if($wh_results){
			foreach($wh_results as $wh){
				$wh->created = date('m/d/Y H:i:s A',$wh->created);
				$arr_wh[] = $wh;
			}
		}
		$service = new Item_service_request();
		$service_results = $service->get_record($cid,0,10,0,0,0,0,0,0,0);

		if($service_results){
			foreach($service_results as $serv){
				$serv->created = date('m/d/Y H:i:s A',$serv->created);
				$arr_service[] = $serv;
			}
		}

		$term = new Member_term();
		$term_results = $term->get_record($cid,0,10,'',0,0,0,0);

		if($term_results){
			foreach($term_results as $serv){
				$serv->created = date('m/d/Y H:i:s A',$serv->created);
				$arr_terms[] = $serv;
			}
		}

		$data = ['orders' => $arr_wh,'services' => $arr_service,'terms' => $arr_terms];
		echo json_encode($data);

	}

	function aging(){
		$is_dl = Input::get('dl');
		$user = new User();
		$company = $user->getCompany($user->data()->company_id);
		$border="";
		if($is_dl == 1){
			$filename = "aging-" . date('m-d-Y-H-i-s-A') . ".xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");
			$colspan = 7;
			$border = "border=1";
			$header = "<table $border ><tr><td colspan='$colspan' >" . $company->name . "</td></table>";
			$header .= "<table $border ><tr><td colspan='$colspan' >Summary of Account Receivables</td></table>";
			$header .= "<table $border ><tr><td colspan='$colspan' >As of ".date('m/d/Y')."</td></table>";
			echo $header;
		}
		$branch_id = Input::get('branch_id');
		$dt_from = Input::get('dt_from');
		$dt_to = Input::get('dt_to');
		$date_type = Input::get('date_type');

		$mem = new Member_credit();
		$user = new User();

		$data = $mem->get_credit($dt_from,$dt_to,$branch_id,$date_type);
		$arr_30 = [];
		$arr_31_60=[];
		$arr_61_90=[];
		$arr_91_120=[];
		$arr_121_above=[];
		$arr_types = [];
		$arr_mem = [];
		if($data){
			foreach($data as $d){
				if(!in_array($d->member_id,$arr_types[$d->sales_type_name])){

					$arr_types[$d->sales_type_name][] = $d->member_id;
				}


				$arr_mem[$d->member_id] = $d->lastname;
				$sold_date = $d->sold_date;

				$diff = getDays(date('m/d/Y',$sold_date));
				$diff = abs($diff);
				$remaining =  $d->amount - $d->amount_paid;

				if($remaining){
					if($diff<=30){
						if(isset($arr_30[$d->member_id])){
							$arr_30[$d->member_id] += $remaining;
						} else {
							$arr_30[$d->member_id] = $remaining;
						}


					} else if($diff<=60 ){

						if(isset($arr_31_60[$d->member_id])){
							$arr_31_60[$d->member_id] += $remaining;
						} else {
							$arr_31_60[$d->member_id] = $remaining;
						}
					}else if($diff<=90 ){

						if(isset($arr_61_90[$d->member_id])){
							$arr_61_90[$d->member_id] += $remaining;
						} else {
							$arr_61_90[$d->member_id] = $remaining;
						}
					}else if($diff<=120 ){

						if(isset($arr_91_120[$d->member_id])){
							$arr_91_120[$d->member_id] += $remaining;
						} else {
							$arr_91_120[$d->member_id] = $remaining;
						}
					} else {

						if(isset($arr_121_above[$d->member_id])){
							$arr_121_above[$d->member_id] += $remaining;
						} else {
							$arr_121_above[$d->member_id] = $remaining;
						}
					}
				}
			}

			echo "<table $border class='table table-bordered' id='tblForApproval'>";
			echo "<tr class='text-right'><th class='text-left'>Client</th><th class='text-right'>0-30</th><th class='text-right'>31-60</th><th class='text-right'>61-90</th><th class='text-right'>91-120</th><th class='text-right'>121 above</th><th class='text-right'>Total</th></tr>";
			foreach($arr_types as $st => $mem_arr){
				$st = ($st)?  $st : 'No type';
				echo "<tr>";
				echo "<td colspan='7' class='text-danger text-left'><strong>$st</strong></td>";
				echo "</tr>";
				$subtotal_30 = 0;
				$subtotal_60 = 0;
				$subtotal_90 = 0;
				$subtotal_120 = 0;
				$subtotal_121 = 0;
				$subtotal = 0;
				foreach($mem_arr as $mid){
					$member_name = $arr_mem[$mid];
					echo "<tr>";
					echo "<td class='text-left'>$member_name</td>";
					$below_30 = isset($arr_30[$mid]) ? $arr_30[$mid] : 0;
					$from_31_60 = isset($arr_31_60[$mid]) ? $arr_31_60[$mid] : 0;
					$from_61_90 = isset($arr_61_90[$mid]) ? $arr_61_90[$mid] : 0;
					$from_91_120 = isset($arr_91_120[$mid]) ? $arr_91_120[$mid] : 0;
					$above_121 = isset($arr_121_above[$mid]) ? $arr_121_above[$mid] : 0;
					$total = $below_30 + $from_31_60 + $from_61_90 + $from_91_120 + $above_121;
					$subtotal_30 += $below_30;
					$subtotal_60 += $from_31_60;
					$subtotal_90 += $from_61_90;
					$subtotal_120 += $from_91_120;
					$subtotal_121 += $subtotal_121;
					$subtotal += $total;

					echo "<td class='text-right'>".number_format($below_30,2)."</td>";
					echo "<td class='text-right'>".number_format($from_31_60,2)."</td>";
					echo "<td class='text-right'>".number_format($from_61_90,2)."</td>";
					echo "<td class='text-right'>".number_format($from_91_120,2)."</td>";
					echo "<td class='text-right'>".number_format($above_121,2)."</td>";
					echo "<td class='text-right'>".number_format($total,2)."</td>";

					echo "</tr>";
				}
				echo "<tr>";
				echo "<th class='text-right'>Sub Total</th>";
				echo "<th class='text-right'>".number_format($subtotal_30,2)."</th>";
				echo "<th class='text-right'>".number_format($subtotal_60,2)."</th>";
				echo "<th class='text-right'>".number_format($subtotal_90,2)."</th>";
				echo "<th class='text-right'>".number_format($subtotal_120,2)."</th>";
				echo "<th class='text-right'>".number_format($subtotal_121,2)."</th>";
				echo "<th class='text-right'>".number_format($subtotal,2)."</th>";
				echo "</tr>";
			}
			echo "<tr>";
			echo "<th class='text-right'>Overall</th>";
			$sum_30 = array_sum($arr_30);
			$sum_60 = array_sum($arr_31_60);
			$sum_90 = array_sum($arr_61_90);
			$sum_120 = array_sum($arr_91_120);
			$sum_121 = array_sum($arr_121_above);
			$grand_total = $sum_30 + $sum_60 + $sum_90 + $sum_120 + $sum_121;
			echo "<th class='text-right'>".number_format($sum_30,2)."</th>";
			echo "<th class='text-right'>".number_format($sum_60,2)."</th>";
			echo "<th class='text-right'>".number_format($sum_90,2)."</th>";
			echo "<th class='text-right'>".number_format($sum_120,2)."</th>";
			echo "<th class='text-right'>".number_format($sum_121,2)."</th>";
			echo "<th class='text-right'>".number_format($grand_total,2)."</th>";

			echo "</tr>";
			echo "</table>";

		}

	}
	function creditSummaryByType(){
		$member_credit = new Member_credit();

		$dt_from = Input::get('dt_from');
		$dt_to = Input::get('dt_to');
		$date_type = Input::get('date_type');
		$branch_id = Input::get('branch_id');

		$is_dl = Input::get('dl');
		$border ="";
		if($is_dl == 1){
			$filename = "st-summary-" . date('m-d-Y-H-i-s-A') . ".xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");
			$border = "border='1'";

		}

		$list = $member_credit->typeSummary($dt_from,$dt_to,$date_type,$branch_id);

		if($list){

			echo "<table $border class='table table-bordered'>";
			echo "<tr><th>Type</th><th>Total</th></tr>";
			$total = 0;
			foreach($list as $l){
				$name = $l->sales_type_name ? $l->sales_type_name : 'NA';
				$total += $l->total_credit;
				echo "<tr><td style='border-top:1px solid #ccc;'>$name</td><td style='border-top:1px solid #ccc;'>". number_format($l->total_credit,2)."</td></tr>";
			}
			echo "<tr><td  style='border-top:1px solid #ccc;'></td><td  style='border-top:1px solid #ccc;'>" . number_format($total,2). "</td></tr>";
			echo "</table>";

		}
	}
	function emailReport(){

		$email = Input::get('email');
		$subject = Input::get('subject');
		$addtl_message = Input::get('addtl_message');
		$body = Input::get('body');
		$dt = Input::get('dt');
		$branch_name = Input::get('branch_name');

		$h4 = "<h4>Branch: $branch_name  <br> Month: $dt</h4>";
		$addtl_message = "<p>$addtl_message</p>";
		$div = "";
		//$div = "<div style='min-width:650px;width:100%;background-color:#f0f0f0;padding:10px;'>";
		$content = "<html><head><style> table { border-collapse: collapse; } td,th { border: 1px solid #000 }</style></head><body><div> " .$addtl_message ."</div> <div style='clear:both;'></div>"  . "<div>" . $body . "</div></body></html>";
		$div .= $content;
		//$div .= "</div>";
		$div = wordwrap($div);

		$email_arr = [];

		if(strpos($email,",")){
			$email_arr = explode(',',$email);
		} else {
			$email_arr[] = $email;
		}

		$res_mail  = sendMail(
			"",
			"AutoGenerated Email",
			$email_arr,
			$subject,
			$div,
			"",
			""
		);

		echo "Email Sent.";

	}
	function getForecast(){

		$sales = new Sales();
		$year = Input::get('year');
		$year = $year ? $year : date('Y');
		$list = $sales->getForecast($year);


		$arr = [];
		$branches = [];
		foreach($list as $a){
			$a->branch_name = ($a->branch_name) ? $a->branch_name :'No branch';
			if(!in_array($a->branch_name,$branches)) $branches[] = $a->branch_name;
			$quota = ($a->quota) ? $a->quota : 0;
			$arr[$a->branch_name][$a->m] = ['amount' => $a->totalamount,'quota' =>$quota];
		}

		echo "<table class='table table-bordered table-condensed' id='tblForApproval' style='font-size:9px;'>";


	?>
		<thead>
			<tr>
			<th>Name</th><th  class='text-right'>Jan</th><th  class='text-right'>Feb</th><th  class='text-right'>March</th><th  class='text-right'>April</th><th  class='text-right'>May</th><th  class='text-right'>June</th><th  class='text-right'>July</th><th  class='text-right'>August</th><th  class='text-right'>Sept</th><th  class='text-right'>Oct</th><th  class='text-right'>Nov</th><th  class='text-right'>Dec</th><th>Total</th>
			</tr>
		</thead>
<?php

		foreach($branches as $b){
			$grand_total = 0;
			$grand_quota = 0;

			echo "<tr>";
			echo "<td><strong class='text-danger'>$b</strong></td>";
			for($i=1;$i<=12;$i++){
				$total = isset($arr[$b][$i]['amount']) ? $arr[$b][$i]['amount'] : 0;
				$q = isset($arr[$b][$i]['quota']) ? $arr[$b][$i]['quota'] : 0;
				$grand_total += $total;
				$grand_quota += $q;
				$percent = 0.00;
				if($total && $q){
					$percent = ($total/$q)*100;
					$percent = number_format($percent,2);
				}


				echo "<td class='text-right'><strong>". number_format($total,2). "</strong><span class='span-block text-success'>". number_format($q,2)."</span>"."<span class='span-block text-danger'>".$percent."%</span></td>";
			}
			if($grand_total && $grand_quota){
				$percent = ($grand_total/$grand_quota)*100;
				$percent = number_format($percent,2);
			}
			echo "<td class='text-right'>". number_format($grand_total,2). "<span class='span-block text-success'>". number_format($grand_quota,2)."</span>"."<span class='span-block text-danger'>".$percent."%</span></td>";
			echo "</tr>";
		}

		echo "</table>";

	}

function getDailyQuotas(){
	$cur = Input::get('cur_week');
	$now = time();
	$nameOfDay = date('l',$now);
	$last_day = strtotime("next sunday" . "1 day -1 sec");
	if(strtolower($nameOfDay) == 'sunday'){
		$last_day = strtotime(date('m/d/Y') . " 1 day -1 sec");
	}
	$arr_days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

	if($cur){
		$cur = 7 * $cur;

		$last_day = strtotime(date('m/d/Y',$last_day) . "$cur days");
		$last_day = strtotime(date('m/d/Y',$last_day) . " 1 day -1 sec");
	}
	$first_day = strtotime(date('m/d/Y',$last_day) . "-6 days");
	echo "<p class='text-center'> <strong>" . date('m/d/Y h:i:s A',$first_day ) . " " . date('m/d/Y h:i:s A',$last_day ) . "</strong></p>";
	$n = $first_day;
	$arr_date = [];
	$ch = 1;
	while(date('Y-m-d',$n) != date('Y-m-d',strtotime(date('Y-m-d',$last_day ). "1 day"))){
		$arr_date[] = date('Y-m-d',$n);
		$n = strtotime(date('Y-m-d',$n) . "1 day");
		$ch++;
		if($ch == 100){
			break;
		}
	}
	$sales = new Sales();
	$list = $sales->dailyForecast($first_day,$last_day);
	$branches = [];
	$arr= [];
	if($list){
		foreach($list as $a){
			$a->branch_name = ($a->branch_name) ? $a->branch_name :'No branch';
			if(!in_array($a->branch_name,$branches)) $branches[] = $a->branch_name;
			$quota = ($a->daily_quota) ? $a->daily_quota : 0;
			$arr[$a->branch_name][$a->d] = ['amount' => $a->totalamount,'quota' =>$quota];
		}

		echo "<table class='table table-bordered table-condensed'>";
		echo "<thead>";
		echo "<tr>";
		echo "<th>Branch</th>";
		foreach($arr_days as $d){
			echo "<th>$d</th>";
		}
		echo "<th></th>";
		echo "</tr>";
		echo "</thead>";
		echo "<tbody>";
		foreach($branches as $b){
			$grand_total = 0;
			$grand_quota = 0;

			echo "<tr>";
			echo "<td><strong class='text-danger'>$b</strong></td>";
			foreach($arr_date as $i){
				$total = isset($arr[$b][$i]['amount']) ? $arr[$b][$i]['amount'] : 0;
				$q = isset($arr[$b][$i]['quota']) ? $arr[$b][$i]['quota'] : 0;
				$grand_total += $total;
				$grand_quota += $q;
				$percent = 0.00;
				if($total && $q){
					$percent = ($total/$q)*100;
					$percent = number_format($percent,2);
				}


				echo "<td class='text-right'><strong>". number_format($total,2). "</strong><span class='span-block text-success'>". number_format($q,2)."</span>"."<span class='span-block text-danger'>".$percent."%</span></td>";
			}
			if($grand_total && $grand_quota){
				$percent = ($grand_total/$grand_quota)*100;
				$percent = number_format($percent,2);
			}
			echo "<td class='text-right'>". number_format($grand_total,2). "<span class='span-block text-success'>". number_format($grand_quota,2)."</span>"."<span class='span-block text-danger'>".$percent."%</span></td>";
			echo "</tr>";
		}
		echo "</tbody>";
		echo "</table>";
	} else {
		echo "<p>No sales for this week.</p>";
	}

}

