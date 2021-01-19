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


	$sales_type = new Sales_type();
	$types = $sales_type->get_active('salestypes',[1,'=',1]);

?>


	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<div class="row">
				<div class="col-md-6">
					<h1>
						<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Import Collection </h1>
				</div>
				<div class="col-md-6 text-right">
					<a class='btn btn-default' href="wh-order.php">Back To Orders</a>
					<a class='btn btn-default' href="avision_collection_list.php">Uploaded Collection</a>
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

			</div>
			<div class="col-md-12">
				<?php
					$html = "";
					if(Input::exists()) {


						if(Token::check(Input::get('token'))) {
							$html .= "<h3>" . $_FILES["file"]["name"] . "</h3>";
							$allowedExts = array("xls", "xlsx");
							$temp = explode(".", $_FILES["file"]["name"]);

							$extension = end($temp);

							if($_FILES["file"]["type"] == "application/vnd.ms-excel" || $_FILES["file"]["type"] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" && in_array($extension, $allowedExts)) {
								$uploads_dir = "../service/";
								$filename = $_FILES["file"]["name"];

								$isUploaded = move_uploaded_file($_FILES["file"]["tmp_name"], $uploads_dir . $_FILES["file"]["name"]);
								if($isUploaded) {

									if(!file_exists($uploads_dir . $_FILES['file']['name'])) {
										exit("FILE NOT FOUND!." . PHP_EOL);
									}
									$objPHPExcel = PHPExcel_IOFactory::load($uploads_dir . $_FILES['file']['name']);

									$sheetNames = $objPHPExcel->getSheetNames();

									$type = Input::get('type');

									$wh = new Wh_order();

									$arr_insert = [];
									$paid_list = [];
									foreach($sheetNames as $index => $name) {

										$objPHPExcel->setActiveSheetIndex($index);
										$lastRow = $objPHPExcel->setActiveSheetIndex($index)->getHighestRow();
										$lastColumn = $objPHPExcel->setActiveSheetIndex($index)->getHighestColumn();
										$startRow = 2;
										$branch_id = 1;

											$html .= "<table class='table' id='tblForApproval'>";
											$html .= "<tr><th>Shop</th><th>SOA Ref</th><th>Transaction Date</th><th>Transaction Type</th><th>Order Number</th><th>Order Item ID</th><th>Item</th><th>Amount</th><th></th></tr>";

										if($type == 1){ // lazada

											for($row = $startRow; $row <= $lastRow; $row++) {

												$sales_type_name = $objPHPExcel->getActiveSheet()->getCell("A" . $row)->getValue();
												$transaction_date = $objPHPExcel->getActiveSheet()->getCell("B" . $row)->getValue();
												$transaction_type = $objPHPExcel->getActiveSheet()->getCell("C" . $row)->getValue();
												$soa_ref = $objPHPExcel->getActiveSheet()->getCell("D" . $row)->getValue();
												$fee_name = $objPHPExcel->getActiveSheet()->getCell("E" . $row)->getValue();
												$item_name = $objPHPExcel->getActiveSheet()->getCell("G" . $row)->getValue();
												$order_number = $objPHPExcel->getActiveSheet()->getCell("P" . $row)->getValue();
												$order_item_id = $objPHPExcel->getActiveSheet()->getCell("Q" . $row)->getValue();
												$amount = $objPHPExcel->getActiveSheet()->getCell("J" . $row)->getValue();
												$danger = "";
												$order_number= number_format($order_number,0,"","");
												$order_item_id= number_format($order_item_id,0,"","");
												$checker = $wh->isOrderExistByPO($order_number, true);
												$amount_due= 0;
												$amount_paid= 0;
												$payment_id= 0;
												$transaction_date = toUnixTimeStamp2($transaction_date);
												if(isset($checker->id) && $checker->id){

													$amount_due  = $checker->amount;
													$amount_paid = $checker->amount_paid;
													$payment_id = $checker->payment_id;

													if($amount_paid >= $amount_due){
														// paid na
														$paid_list[] = [
															'order_number' => $order_number,
															'order_item_id' => $order_item_id,
															'transaction_type' => $transaction_type,
															'amount' => $amount,
															'item_name' => $item_name,
														];

													} else {
														// checker
														$wh_order_payments = new Wh_order_payment();
														$ch = $wh_order_payments->checkerLazada(trim($order_number),trim($order_item_id));
														if(isset($ch->id) && $ch->id){

														} else {
															$arr_insert[] = [
																'sales_type_name' =>  $sales_type_name,
																'soa_ref' =>  $soa_ref,
																'order_number' =>  $order_number,
																'transaction_type' => $transaction_type,
																'transaction_date' => $transaction_date,
																'order_item_id' => $order_item_id,
																'amount_due' => $amount_due,
																'item_name' => $item_name,
																'amount_paid' => $amount_paid,
																'member_credit_id' => $checker->member_credit_id,
																'payment_id' => $checker->payment_id,
																'amount' => $amount,
																'fee_name' => $fee_name,
															];
														}
													}
												} else {
													$danger = "bg-danger";
												}
												$html .= "<tr class='$danger'><td>$sales_type_name</td><td>$soa_ref</td><td>". date('m/d/Y',$transaction_date)."</td><td>$transaction_type</td><td>$order_number</td><td>$item_name</td><td>$order_item_id</td><td>$amount</td><td></td></tr>";
											}
											$html .= "</table>";
										} else if($type == 2){ // shopee
											for($row = $startRow; $row <= $lastRow; $row++) {
												$order_number = $objPHPExcel->getActiveSheet()->getCell("A" . $row)->getValue();
											}

										}
										$html .= "</table>";

									}
								} else {
									$html .= "Not uploaded";
								}
							} else {
								$html .= "Wrong file type.";
							}
						} else {
							$html .= "Invalid token";
						}
					}
				?>

				<div class="form-group">
					<div class="row">
						<form action="" method="POST" enctype="multipart/form-data">
							<div class="col-md-3">
								<div class="form-group">
									<select class='form-control' name="type" id="type" required>
										<option value="">Select Type</option>
										<option value="1">Lazada</option>
										<option value="2">Shopee</option>
									</select>
								</div>
							</div>

							<div class="col-md-3">
								<div class="form-group">
									<select class='form-control' name="is_save" id="is_save" required>
										<option value="1">View Only</option>
										<option value="2">Import</option>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="file" class='btn btn-default' name='file' id='file' required>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type='submit' class='btn btn-primary' name='btnUpload' value='UPLOAD'>
									<input type='hidden' name='token' value=<?php echo Token::generate(); ?>>
								</div>
							</div>
						</form>
						<div class="col-md-3"></div>


					</div>
				</div>

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Uploads</div>
					<div class="panel-body">
						<?php
							if(Input::exists()){
								if($has_error){

								} else {

								}

								$arr_type = [
									        'Ideas Ph' => 1,
											'Avision Ph' => 5,
									        'Nextbook'  => 4
										];

								if($paid_list){

								/*
									echo "<h3>Paid List</h3>";
									echo "<table class='table table-bordered'>";
									echo "<tr><th>Order Number</th><th>Order Item ID</th><th>Amount</th><th>Transaction Type</th></tr>";
									foreach($paid_list as $p){
										if($p['transaction_type'] == 'Orders-Item Charges'){
											echo "<tr>";
											echo "<tr><td style='border-top:1px solid #ccc;'>$p[order_number]</td><td style='border-top:1px solid #ccc;'>$p[order_item_id]</td><td style='border-top:1px solid #ccc;'>$p[amount]</td><td style='border-top:1px solid #ccc;'>$p[transaction_type]</td></tr>";
											echo "</tr>";
										}
									}
									echo "</table>";
								*/

								}
								$type = Input::get('type');
								$sales_type = Input::get('sales_type');
								if($type == 1){
									if(Input::get('is_save') == 2){
										if(count($arr_insert)){

											$member_credit = new Member_credit();
											$cash = new Cash();
											$wh_order_payment_cls = new Wh_order_payment();

											foreach($arr_insert as $i){
												if($i['member_credit_id']){
													$amount =(float)  $i['amount'];
													if($i['transaction_type'] == 'Orders-Item Charges'){

														$member_credit_cur =  new Member_credit( $i['member_credit_id']);
														$amount_paid = $member_credit_cur->data()->amount_paid;
														$to_amount = $amount + $amount_paid;
														$amount_due = (float) $i['amount_due'];
														$status = 0;

														if($to_amount >= $amount_due){
															$status = 1;
														}

														try{
															$member_credit_cur->update(
																[
																	'amount_paid' => $to_amount,
																	'status' => $status,
																] , $i['member_credit_id']
															);
														} catch(Exception $e){
															dump($e);
														}

														try{
															$i['payment_id'] = (int)$i['payment_id'];

															$cash->create([
																'amount' => $amount,
																'created' => time(),
																'payment_id' => $i['payment_id'],
																'is_active' => 1,
															]);
														} catch(Exception $e){
															dump($e);
														}

													//	echo "<p>Payment Received for  " . $i['item_name'] . " PO Number: ". $i['order_number']."</p>";
													}
													$sales_type_id = isset($arr_type[trim($i['sales_type_name'])]) ? $arr_type[trim($i['sales_type_name'])] : 0;
													try{
														$wh_order_payment_cls->create(
															[
																'soa_ref' => $i['soa_ref'],
																'client_po' => $i['order_number'],
																'item_name' => $i['item_name'],
																'amount' => $amount,
																'order_item_id' => $i['order_item_id'],
																'fee_name' => $i['fee_name'],
																'transaction_type' =>  $i['transaction_type'],
																'transaction_date' => $i['transaction_date'],
																'sales_type_id' => $sales_type_id,
															]
														);
													} catch(Exception $e){
														dump($e);
													}
												}
											}
										}
									} else {
										dump($arr_insert);
										echo "<div class='alert alert-warning'>View Mode Only</div>";
									}
								} else {
									echo "<div class='alert alert-info'>Under construction</div>";
								}
							} else {
								echo "<div class='alert alert-info'>Upload file to import data.</div>";
							}
						?>
						<?php echo $html; ?>
					</div>
				</div>
			</div>
		</div>
		<!-- /.modal -->
	</div>
	<!-- end page content wrapper-->

	<script>

		$(document).ready(function() {


		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>