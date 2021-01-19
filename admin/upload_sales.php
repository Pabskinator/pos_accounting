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
									$arr = [];
									foreach($sheetNames as $index => $name) {
										$objPHPExcel->setActiveSheetIndex($index);
										$lastRow = $objPHPExcel->setActiveSheetIndex($index)->getHighestRow();
										$lastColumn = $objPHPExcel->setActiveSheetIndex($index)->getHighestColumn();
										$startRow = 2;
										$branch_id = $objPHPExcel->getActiveSheet()->getCell("A1")->getValue();
										$branch_name = $objPHPExcel->getActiveSheet()->getCell("B1")->getValue();
										$html .= "<h1>$branch_id - $branch_name</h1>";
										$html .= "<table class='table table-border'>";
										$html .= "<tr><th>Date </th><th>Invoice</th><th>Item</th><th>Qty</th><th>Price</th><th>Total</th></tr>";
										for($row = $startRow; $row <= $lastRow; $row++) {

												$colDate = "A";
												$colInvoice = "B";
												$colItemcode = "C";
												$colQty = "D";
												$colPrice = "E";
												$colTotal = "F";

												$date = $objPHPExcel->getActiveSheet()->getCell($colDate . $row)->getValue();
												$invoice = $objPHPExcel->getActiveSheet()->getCell($colInvoice . $row)->getValue();
												$itemcode = $objPHPExcel->getActiveSheet()->getCell($colItemcode . $row)->getValue();
												$qty = $objPHPExcel->getActiveSheet()->getCell($colQty . $row)->getValue();
												$price = $objPHPExcel->getActiveSheet()->getCell($colPrice . $row)->getValue();
												$total = $objPHPExcel->getActiveSheet()->getCell($colTotal . $row)->getValue();

											$arr[$branch_id ."-".$invoice][] = [
												'date' => toUnixTimeStamp2($date),
												'invoice' => $invoice,
												'item_code' => $itemcode,
												'qty' => $qty,
												'price' => $price,
												'total' => $total,
											];

											$html .= "<tr><td>$date</td><td>$invoice</td><td>$itemcode</td><td>$qty</td><td>$price</td><td>$total</td></tr>";

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
								<input type="file" class='btn btn-default' name='file' id='file' required>
							</div>
							<div class="col-md-3">
								<input type='submit' class='btn btn-primary' name='btnUpload' value='UPLOAD'>
								<input type='hidden' name='token' value=<?php echo Token::generate(); ?>>
							</div>
						</form>
							<div class="col-md-3"></div>
							<div class="col-md-3 text-right">
								<button class='btn btn-default' id='btnFormat'>Format</button>
							</div>

					</div>
				</div>

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Uploads</div>
					<div class="panel-body">

						<?php if($html){
							//echo $html;

							$terminal_id = 0;
							//$payment_id = addPayment($user->data()->company_id);
							foreach($arr as $invoice => $items ){
								// add payment

								$explode = explode('-',$invoice);

								$payment_id = addPayment($user->data()->company_id);
								$is_exists = false;
								$product = new Product();
								$total = 0;
								$now = 0;
								$newsales = new Sales();
								$checker = $newsales->invoiceBranchSalesExists($explode[1],$explode[0]);


								if(isset($checker->cnt) && $checker->cnt){
									echo "<div class='alert alert-info'><h4>Invoice " . ($explode[1]) . " already exists</h4></div>";
								} else {
								echo "<h3>Inserting Payment ID # " . $payment_id . "</h3>";
								echo "<table class='table table-bordered'>";
								echo "<tr><th style='width:100px;'>Control</th><th style='width:40%;'>Item</th><th  style='width:120px;'>Qty</th><th  style='width:150px;'>Price</th><th></th></tr>";
								foreach($items as $item){
									// add sales

									$cur  = $product->isProductExist($item['item_code'],$user->data()->company_id,true);
									$now = $item['date'];
									$lbl = "";


									if(isset($cur->id)){
										$price = $product->getPrice($cur->id);
										$total += ($price->price * $item['qty']);

										$newsales->create(array(
											'terminal_id' => $terminal_id,
											'invoice' => $item['invoice'],
											'item_id' => $cur->id,
											'price_id' =>$price->id,
											'qtys' => $item['qty'],
											'company_id' => $user->data()->company_id,
											'cashier_id' => 0,
											'sold_date' => $item['date'],
											'payment_id' => $payment_id,
											'branch_id' => $branch_id,
											'member_id' => 0,
											'station_id' => 0,
										));
										$lbl = "<span class='text-success'>Inserted</span>";

									} else {
										$lbl = "<span class='text-danger'>Unknown Product</span>";
									}
									echo "<tr><td style='border-top:1px solid #ccc;'>$item[invoice]</td><td style='border-top:1px solid #ccc'>$item[item_code]</td><td style='border-top:1px solid #ccc;'>$item[qty]</td><td style='border-top:1px solid #ccc;'>$price->price</td><td style='border-top:1px solid #ccc;' >$lbl</td></tr>";
								}
								echo "</table>";
								if($is_exists){
									//delete payment

								}
								$cash = new Cash();


								$cash->create(array(
									'amount' =>$total,
									'is_active' => 1,
									'created' => $now,
									'modified' => $now,
									'payment_id' => $payment_id
								));

								}
							}

						} else {
							echo "Upload file first.";
						} ?>

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