<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../libs/phpexcel/Classes/PHPExcel.php';
	require_once '../includes/admin/page_head2.php';

	function toUnixTimeStamp2($date) {
		return ($date - 32662) * 24 * 60 * 60 + 612806400;
	}

	function removeExcessSpace($s) {

		$s = trim($s);
		$s = str_replace('    ', ' ', $s);
		$s = str_replace('   ', ' ', $s);
		$s = str_replace('  ', ' ', $s);

		return $s;
	}

	function removeUnwatedChar($s) {
		$s = trim($s);
		$s = str_replace('mr.', '', strtolower($s));
		$s = str_replace('mr', '', strtolower($s));

		return strtolower($s);
	}

	function addPayment($cid){
		$payment = new Payment();

		$payment->create(array(
			'created' => time(),
			'company_id' => $cid,
			'is_active' => 1
		));
		$payment_lastid = $payment->getInsertedId();
		return $payment_lastid;
	}

?>

	<!-- Page content -->
	<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<div class="row">
				<div class="col-md-6">
					<h1>
						<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Upload Sales </h1>
				</div>
				<div class="col-md-6">

				</div>
			</div>

		</div> <?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}
			//	dump($_SESSION['test']);
		?>

		<div class="row">
			<div class="col-md-12">
				<?php
					$html = "";


					$uploads_dir = "ar4.xlsx";

					if(!file_exists($uploads_dir)) {
						exit("FILE NOT FOUND!." . PHP_EOL);
					}

					$objPHPExcel = PHPExcel_IOFactory::load($uploads_dir);
					$sheetNames = $objPHPExcel->getSheetNames();
					$arr = [];
					$payment = new Payment();
					$member_credit = new Member_credit();
					$cash = new Cash();

					foreach($sheetNames as $index => $name) {
						$objPHPExcel->setActiveSheetIndex($index);
						$lastRow = $objPHPExcel->setActiveSheetIndex($index)->getHighestRow();
						$lastColumn = $objPHPExcel->setActiveSheetIndex($index)->getHighestColumn();
						$startRow = 3;


						$html .= "<table id='tblForApproval' class='table table-border'>";
						$html .= "<tr><th>Client</th><th>Date </th><th>Invoice</th><th>Receipt Amount</th><th>Collection Amount</th><th>Deduct</th><th>Balance</th><th>Type</th></tr>";
						for($row = $startRow; $row <= $lastRow; $row++) {

							$colMemId = "H";
							$colDate = "C";
							$colInv = "B";
							$colReceipt = "D";
							$colCollection= "E";
							$colDeduct= "F";
							$colBalance= "G";
							$colType= "I";

							$member_id= $objPHPExcel->getActiveSheet()->getCell($colMemId . $row)->getValue();
							$invoice = $objPHPExcel->getActiveSheet()->getCell($colInv . $row)->getValue();
							$rec_amount  = $objPHPExcel->getActiveSheet()->getCell($colReceipt . $row)->getValue();
							$col_amount = $objPHPExcel->getActiveSheet()->getCell($colCollection . $row)->getValue();
							$deduct_amount = $objPHPExcel->getActiveSheet()->getCell($colDeduct . $row)->getValue();
							$balance = $objPHPExcel->getActiveSheet()->getCell($colBalance . $row)->getValue();
							$date = $objPHPExcel->getActiveSheet()->getCell($colDate . $row)->getValue();
							$sale_type_id = $objPHPExcel->getActiveSheet()->getCell($colType . $row)->getValue();

							if($date){
								$date = toUnixTimeStamp2($date);
								$date = date('m/d/Y' , $date);
							}

							$explode_inv = explode("-",$invoice);
							$cntinv = count($explode_inv);
							$lblinv = "";

							if($cntinv > 2){
								$lblinv = $explode_inv[0] . $explode_inv[1];
							} else {
								$lblinv = $explode_inv[0];
							}
							$lblinv = trim($lblinv);
							$lblinv = strtoupper($lblinv);

							$item_id = 1577;
							$terminal_id = 19;
							$dr= 0;
							$inv = 0;
							$sr = 0;
							$ts = 0;
							$ctrlnum = "";
							if($lblinv == 'NSI' ||$lblinv == 'N' || $lblinv == 'L' || $lblinv == 'ISI' || $lblinv == 'C' || $lblinv == 'LISI' || $lblinv == 'SI' ||$lblinv == 'LSI' || $lblinv == 'LCSI'){
								$type = 1;
								$inv = $explode_inv[$cntinv-1];
								$ctrlnum = $inv;
							} else if ($lblinv == 'DRSLLB' || $lblinv == 'DRBL' || $lblinv == 'DRLA' || $lblinv == 'DRS' || $lblinv == 'DRDL' || $lblinv == 'DRL' || $lblinv == 'DRSLB' || $lblinv == 'DRA' || $lblinv == 'DRD' || $lblinv == 'DRB'|| $lblinv == 'DRSLD' || $lblinv == 'DRSLLD'){
								$type = 2;
								$dr = $explode_inv[$cntinv-1];
								$ctrlnum = $dr;
							} else if ($lblinv == 'SR'){
								$type = 4;
								$sr = $explode_inv[$cntinv-1];
								$ctrlnum = $sr;
							} else if ($lblinv == 'TS'){
								$type = 5;
								$ts = $explode_inv[$cntinv-1];
								$ctrlnum = $ts;
							} else {
								$type = 0;
							}
							$cls = "";
							if($member_id && $date && $explode_inv[$cntinv-1] && $invoice && $rec_amount) {
								$checker = $payment->getSalesByDoc($member_id, $type, $ctrlnum);
								if(isset($checker->cnt) && $checker->cnt > 0) {
									$cls = "bg-danger";
									$html .= "<tr class='$cls'>
										<td>$member_id type == $type</td>
										<td>$date</td>
										<td>$lblinv == " . $explode_inv[$cntinv - 1] . "</td>
										<td>$rec_amount</td>
										<td>$col_amount -- $addcash</td>
										<td>$deduct_amount -- $adddeduct</td>
										<td>$balance</td>
										<td>$sale_type_id</td>
									  </tr>";

								} else {


									$rec_amount = number_format($rec_amount, 2, ".", "");
									$col_amount = number_format($col_amount, 2, ".", "");
									$deduct_amount = number_format($deduct_amount, 2, ".", "");
									$balance = number_format($balance, 2, ".", "");
									$addcash = "";
									$adddeduct = "";

									if($col_amount != 0.00) {
										$addcash = "addcash";
									}

									if($deduct_amount != 0.00) {
										$adddeduct = "addcash";
									}

									$html .= "<tr class='$cls'>
										<td>$member_id type == $type</td>
										<td>$date</td>
										<td>$lblinv == " . $explode_inv[$cntinv - 1] . "</td>
										<td>$rec_amount</td>
										<td>$col_amount -- $addcash</td>
										<td>$deduct_amount -- $adddeduct</td>
										<td>$balance</td>
										<td>$sale_type_id</td>
									  </tr>";

/*
									// add payment
									$date = strtotime($date);

																		$payment->create(array(
																			'created' => time(),
																			'company_id' => $date,
																			'is_active' => 1,
																			'remarks' => 'From Upload 5'
																		));

																		$payment_lastid = $payment->getInsertedId();

																		$sales = new Sales();

																		// add sale
																		$sales->create(array(
																			'terminal_id' => $terminal_id,
																			'invoice' => $inv,
																			'dr' => $dr,
																			'ir' => 0,
																			'sr' => $sr,
																			'ts' => $ts,
																			'item_id' => $item_id,
																			'price_id' =>1760,
																			'qtys' => 1,
																			'discount' => 0,
																			'store_discount' => 0,
																			'adjustment' => 0,
																			'company_id' => $user->data()->company_id,
																			'cashier_id' => $user->data()->id,
																			'sold_date' => $date,
																			'payment_id' => $payment_lastid,
																			'member_id' => $member_id,
																			'member_adjustment' => $rec_amount,
																			'station_id' => 0,
																			'sales_type' => $sale_type_id,
																			'warranty' => 0,
																			'adjustment_remarks' => 'from upload 4'
																		));

																		// add member_credit
																		$pcredit = new Member_credit();

																		$deduct_amount = ($deduct_amount) ? $deduct_amount : 0;

																		$col_amount = ($col_amount) ? $col_amount : 0;

																		$pcredit->create(array(
																			'amount' =>$rec_amount,
																			'amount_paid' => ($col_amount + $deduct_amount),
																			'is_active' => 1,
																			'created' => $date,
																			'modified' => $date,
																			'payment_id' => $payment_lastid,
																			'member_id' => $member_id
																		));

																		if($col_amount != 0.00){
																			$pcash = new Cash();

																			$pcash->create(array(
																				'amount' =>$col_amount,
																				'is_active' => 1,
																				'created' => $date,
																				'modified' => $date,
																				'payment_id' => $payment_lastid
																			));
																		}

																		if($deduct_amount != 0.00){
																			$pdeduct = new Deduction();

																			$pdeduct->create(array(
																				'amount' =>$deduct_amount,
																				'is_active' => 1,
																				'created' => $date,
																				'remarks' => "",
																				'payment_id' => $payment_lastid,
																				'member_id' => $member_id
																			));
																		}


*/
								}
							}
						}
						$html .= "</table>";

					}



				?>

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Uploads</div>
					<div class="panel-body">
						<?php	echo $html; ?>
					</div>
				</div>
			</div>

		</div>
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog" style='width:70%;'>
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body" id='mbody'>
						<div class="row text-center">
							<img src="../css/img/upload_format.png" alt="">
						</div>
					</div>
				</div>
				<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->
	</div>
	<!-- end page content wrapper-->

	<script>

		$(document).ready(function() {
			$('body').on('click','#btnFormat',function(e){
				e.preventDefault();
				$('#myModal').modal('show');
			});

		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>