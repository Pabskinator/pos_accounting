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
	$wh_branch_id = 4;


	// request

	$type = Input::get('type');
	$batch_id = Input::get('batch_id');

?>


	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<div class="row">
				<div class="col-md-6">
					<h1>
						<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Import Sales
					</h1>
				</div>
				<div class="col-md-6 text-right">
					<a class='btn btn-default' href="wh-order.php">Back To Orders</a>
					<a class='btn btn-default' href="avision_order_list.php">Uploaded Orders</a>
				</div>
			</div>

		</div> <?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}

		?>


		<div class="row">
			<div class="col-md-12">

			</div>
			<div class="col-md-12">
				<?php
					$html = "";
					if(Input::exists()) {


						if(Token::check(Input::get('token'))) {
							// assign to html variable
							require_once('includes/avision/read_file.php');

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
									<input type="text" class='form-control' value='<?php echo date('mdY'); ?>' placeholder='Batch Name' name='batch_id' id='batch_id'>
									<span class='help-block'>Batch Name</span>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
								<select class='form-control' name="type" id="type" required>
									<option value="">Select Type</option>
									<option value="1">Lazada</option>
									<option value="2">Shopee</option>
								</select>
								<span class='help-block'>Sales Type</span>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select name="sales_type" id="sales_type" class='form-control' required>
										<option value="">Select Store Type</option>
										<?php foreach($types as $t){
											echo "<option value='$t->id'>$t->name</option>";
										} ?>
									</select>
									<span class='help-block'>Store Type</span>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select class='form-control' name="is_save" id="is_save" required>
										<option value="1">View Only</option>
										<option value="2">Import</option>
									</select>
									<span class='help-block'>View Or Import</span>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="file" class='btn btn-default' name='file' id='file' required>
								</div>
								<span class='help-block'>File to upload</span>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type='submit' class='btn btn-primary' name='btnUpload' value='UPLOAD'>
									<input type='hidden' name='token' value=<?php echo Token::generate(); ?>>
								</div>
							</div>
						</form>


					</div>
				</div>

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Uploads</div>
					<div class="panel-body">
						<?php
							if(Input::exists()){

							} else {
								echo "<div class='alert alert-info'>Upload file to import data.</div>";
							}

							$now = time();


							if($type == 1){
								if($arr_to_insert){
									if(Input::get('is_save') == 2){
										$wh = new Wh_order();
										$member = new Member();
										$prod = new Product();
										$sales_type_id = Input::get('sales_type');
										$batch_cls = new Wh_order_batch();
										// preparations
										$arr_final = [];

										foreach($arr_to_insert as $po_num => $items){
											foreach($items as $item_id => $data){
												foreach($data as $d){
													if(isset($arr_final[$po_num][$item_id])){
														$arr_final[$po_num][$item_id]['qty'] += 1;
													} else {
														$arr_final[$po_num][$item_id] = $data[0];
														$arr_final[$po_num][$item_id]['qty'] = 1;
													}
												}
											}
										}

										$current_type = new Sales_type($sales_type_id);
										$batch_cls->create(
											[
												'batch_name' => $batch_id,
												'status' => 1,
												'store_type' => $current_type->data()->name,
												'created' => time(),
											]
										);

										$lastInsertedBatchId= $batch_cls->getInsertedId();

										$po_number_success= [];
										foreach($arr_final as $po_num => $items){
											$main_info = $arr_to_insert_info[$po_num];


											$member_id = 0; // insert member
											$order_id_number = $po_num;
											$po_exists = $wh->isOrderExistByPO($po_num,1);
											$for_status = 3; // pending at warehouse status
											if(!$po_exists){
												// add payment
												if(!in_array($po_num,$po_number_success)){
													$po_number_success[] = $po_num;
												}
												$payment = new Payment();

												$dt_paid = time();

												$payment->create(array(
													'created' => $dt_paid,
													'company_id' => $user->data()->company_id,
													'is_active' => 1
												));

												$payment_lastid = $payment->getInsertedId();

												$client_name = $main_info['customer_name'];
												$client_address = $main_info['customer_address'];
												$client_contact_number = $main_info['customer_contact_number'];
												$client_name = strtolower(trim($client_name));

												$client_exists = $member->getByLastname($client_name);

												if(isset($client_exists->id) && $client_exists->id){
													$member_id = $client_exists->id;
												} else {

													$newmember = array(
														'lastname' => $client_name,
														'personal_address' => $client_address,
														'contact_number' =>$client_contact_number,
														'company_id' => $user->data()->company_id,
														'is_active' => 1,
														'salestype' => $sales_type_id,
														'created' => strtotime(date('Y/m/d H:i:s')),
														'modified' => strtotime(date('Y/m/d H:i:s'))
													);

													$member->create($newmember);
													$member_id = $member->getInsertedId();

												}

												// insert order
												$for_status = 3;
												$wh->create(array(
													'branch_id' => $wh_branch_id,
													'member_id' => $member_id,
													'to_branch_id' => $wh_branch_id,
													'remarks' => "",
													'client_po' => $order_id_number,
													'shipping_company_id' => "",
													'payment_id' => $payment_lastid,
													'created' => $now,
													'company_id' => $user->data()->company_id,
													'user_id' => $user->data()->id,
													'is_active' => 1,
													'status' => $for_status,
													'stock_out' => 0,
													'batch_id' => $lastInsertedBatchId,
												));
												$lastItOrder = $wh->getInsertedId();
												$amount_due = 0;
												foreach($items as $to_insert){

													$price_id = 0;
													$item_id = $to_insert['item_id'];
													$unit_price = str_replace(',',"",$to_insert['unit_price']);



													$price= $prod->getPrice($item_id);

													$price_id = $price->id;
													$orig_price = $price->price;
													$dif = $unit_price - $orig_price;
													$qty = $to_insert['qty'];
													$alladj = $dif * $qty;
													$amount_due += ($unit_price * $qty);

													$order_details = new Wh_order_details();

													$order_details->create(array(
														'wh_orders_id' => $lastItOrder,
														'item_id' => $item_id,
														'price_id' => $price_id,
														'qty' => $qty,
														'created' => $now,
														'modified' => $now,
														'company_id' => $user->data()->company_id,
														'is_active' => 1,
														'member_adjustment' => $alladj,
														'original_qty' => $qty,
													));


													// insert sales
													/*
														$terminal_id = 1;
														$newsales = new Sales();
														$newsales->create(array(
															'terminal_id' => $terminal_id,
															'invoice' => 0,
															'sv' => 0,
															'dr' => 0,
															'ir' => 0,
															'sr2' => 0,
															'ts' => '',
															'pref_inv' => '',
															'pref_dr' => '',
															'pref_ir' => '',
															'pref_sv' => '',
															'suf_inv' => '',
															'suf_dr' => '',
															'suf_ir' => '',
															'suf_sv' => '',
															'item_id' => $item_id,
															'price_id' => $price_id,
															'qtys' =>  $qty,
															'discount' => 0,
															'store_discount' => 0,
															'adjustment' => 0,
															'member_adjustment' => $alladj,
															'terms' =>0,
															'company_id' => $user->data()->company_id,
															'cashier_id' => $user->data()->id,
															'sold_date' => $now,
															'payment_id' =>$payment_lastid,
															'member_id' => $member_id,
															'warranty' => 24,
															'station_id' => 0,
															'sales_type' =>$sales_type_id,
														));
													*/

												} // end loop item

												$pcredit = new Member_credit();

												$pcredit->create(array(
													'amount' =>$amount_due,
													'is_active' => 1,
													'created' => $now,
													'modified' => $now,
													'payment_id' => $payment_lastid,
													'member_id' => $member_id,
													'is_cod' => 0
												));



											} // end if

										}

										if($wh_info_ar){
											$wh_info_cls = new Wh_po_info();

											foreach($wh_info_ar as $info){
												$client_po = $info['client_po'];

												if(in_array($client_po,$po_number_success)){
													$check_info  = $wh_info_cls->checkLazada($client_po,$info['laz_order_item_id']);
													if(isset($check_info->cnt) && $check_info->cnt){
														// existing
													} else {
														// insert
														try {
															$wh_info_cls->create($info);
														}catch (Exception $e){

														}

													}
												}
											}
										}


									} else {

										$wh = new Wh_order();
										$member = new Member();
										$prod = new Product();
										$sales_type_id = Input::get('sales_type');
										// preparations
										$arr_final = [];

										foreach($arr_to_insert as $po_num => $items){

											foreach($items as $item_id => $data){
												foreach($data as $d){
													if(isset($arr_final[$po_num][$item_id])){
														$arr_final[$po_num][$item_id]['qty'] += 1;
													} else {
														$arr_final[$po_num][$item_id] = $data[0];
														$arr_final[$po_num][$item_id]['qty'] = 1;
													}
												}

											}
										}

										echo "<div class='alert alert-warning'>View Only Mode</div>";
									}
								}
							} else if ($type == 2) {

								if(Input::get('is_save') == 2){ // shopeeeeeeee
									if($arr_to_insert){
										if(Input::get('is_save') == 2){
											$wh = new Wh_order();
											$batch_cls = new Wh_order_batch();
											$member = new Member();
											$prod = new Product();
											$sales_type_id = Input::get('sales_type');
											// preparations
											$arr_final = [];

											foreach($arr_to_insert as $po_num => $items){

												foreach($items as $item_id => $data){
													foreach($data as $d){
														if(isset($arr_final[$po_num][$item_id])){
															$arr_final[$po_num][$item_id]['qty'] += $d['qty'];
														} else {
															$arr_final[$po_num][$item_id] = $data[0];
														}
													}

												}
											}

											$po_number_success= [];
											$current_type = new Sales_type($sales_type_id);
											$batch_cls->create(
												[
													'batch_name' => $batch_id,
													'status' => 1,
													'store_type' => $current_type->data()->name,
													'created' => time(),
												]
											);

											$lastInsertedBatchId= $batch_cls->getInsertedId();

											foreach($arr_final as $po_num => $items){
												$main_info = $arr_to_insert_info[$po_num];


												$member_id = 0; // insert member
												$order_id_number = $po_num;
												$po_exists = $wh->isOrderExistByPO($po_num,1);
												$for_status = 3; // pending at warehouse status
												if(!$po_exists){
													// add payment
													if(!in_array($po_num,$po_number_success)){
														$po_number_success[] = $po_num;
													}
													$payment = new Payment();

													$dt_paid = time();

													$payment->create(array(
														'created' => $dt_paid,
														'company_id' => $user->data()->company_id,
														'is_active' => 1
													));

													$payment_lastid = $payment->getInsertedId();

													$client_name = $main_info['customer_name'];
													$rebate = $main_info['rebate'];
													$client_address = $main_info['customer_address'];
													$client_contact_number = $main_info['customer_contact_number'];
													$client_name = strtolower(trim($client_name));

													$client_exists = $member->getByLastname($client_name);

													$rebate = ($rebate) ? $rebate : 0;

													if(isset($client_exists->id) && $client_exists->id){
														$member_id = $client_exists->id;
													} else {

														$newmember = array(
															'lastname' => $client_name,
															'personal_address' => $client_address,
															'contact_number' =>$client_contact_number,
															'company_id' => $user->data()->company_id,
															'is_active' => 1,
															'salestype' => $sales_type_id,
															'created' => strtotime(date('Y/m/d H:i:s')),
															'modified' => strtotime(date('Y/m/d H:i:s'))
														);

														$member->create($newmember);
														$member_id = $member->getInsertedId();

													}

													// insert order
													$for_status = 3;
													$wh->create(array(
														'branch_id' => $wh_branch_id,
														'member_id' => $member_id,
														'to_branch_id' => $wh_branch_id,
														'remarks' => "",
														'client_po' => $order_id_number,
														'shipping_company_id' => "",
														'payment_id' => $payment_lastid,
														'created' => $now,
														'company_id' => $user->data()->company_id,
														'user_id' => $user->data()->id,
														'is_active' => 1,
														'status' => $for_status,
														'stock_out' => 0,
														'batch_id' => $lastInsertedBatchId,
														'rebate' =>$rebate ,
													));
													$lastItOrder = $wh->getInsertedId();
													$amount_due = 0;
													foreach($items as $to_insert){

														$price_id = 0;
														$item_id = $to_insert['item_id'];
														$unit_price = str_replace(',',"",$to_insert['unit_price']);



														$price= $prod->getPrice($item_id);

														$price_id = $price->id;
														$orig_price = $price->price;
														$dif = $unit_price - $orig_price;
														$qty = $to_insert['qty'];
														$alladj = $dif * $qty;
														$amount_due += ($unit_price * $qty);

														$order_details = new Wh_order_details();

														$order_details->create(array(
															'wh_orders_id' => $lastItOrder,
															'item_id' => $item_id,
															'price_id' => $price_id,
															'qty' => $qty,
															'created' => $now,
															'modified' => $now,
															'company_id' => $user->data()->company_id,
															'is_active' => 1,
															'member_adjustment' => $alladj,
															'original_qty' => $qty,
														));


														// insert sales
														/* $terminal_id = 1;
														$newsales = new Sales();
														$newsales->create(array(
															'terminal_id' => $terminal_id,
															'invoice' => 0,
															'sv' => 0,
															'dr' => 0,
															'ir' => 0,
															'sr2' => 0,
															'ts' => '',
															'pref_inv' => '',
															'pref_dr' => '',
															'pref_ir' => '',
															'pref_sv' => '',
															'suf_inv' => '',
															'suf_dr' => '',
															'suf_ir' => '',
															'suf_sv' => '',
															'item_id' => $item_id,
															'price_id' => $price_id,
															'qtys' =>  $qty,
															'discount' => 0,
															'store_discount' => 0,
															'adjustment' => 0,
															'member_adjustment' => $alladj,
															'terms' =>0,
															'company_id' => $user->data()->company_id,
															'cashier_id' => $user->data()->id,
															'sold_date' => $now,
															'payment_id' =>$payment_lastid,
															'member_id' => $member_id,
															'warranty' => 24,
															'station_id' => 0,
															'sales_type' =>$sales_type_id,
														)); */
													} // end loop item

													$pcredit = new Member_credit();

													$pcredit->create(array(
														'amount' =>$amount_due,
														'is_active' => 1,
														'created' => $now,
														'modified' => $now,
														'payment_id' => $payment_lastid,
														'member_id' => $member_id,
														'is_cod' => 0
													));



												} // end if

											}

											if($wh_info_ar){
												$wh_info_cls = new Wh_po_info();

												foreach($wh_info_ar as $info){
													$client_po = $info['client_po'];

													if(in_array($client_po,$po_number_success)){
														$check_info  = $wh_info_cls->checkShopee($client_po,$info['item_name']);
														if(isset($check_info->cnt) && $check_info->cnt){
															// existing
														} else {
															// insert
															$wh_info_cls->create($info);
														}
													}
												}
											}


										} else {

											$wh = new Wh_order();
											$member = new Member();
											$prod = new Product();
											$sales_type_id = Input::get('sales_type');
											// preparations
											$arr_final = [];

											foreach($arr_to_insert as $po_num => $items){

												foreach($items as $item_id => $data){
													foreach($data as $d){
														if(isset($arr_final[$po_num][$item_id])){
															$arr_final[$po_num][$item_id]['qty'] += 1;
														} else {
															$arr_final[$po_num][$item_id] = $data[0];
															$arr_final[$po_num][$item_id]['qty'] = 1;
														}
													}

												}
											}

											echo "<div class='alert alert-warning'>View Only Mode</div>";
										}
									}
								} else {

									$wh = new Wh_order();
									$member = new Member();
									$prod = new Product();
									$sales_type_id = Input::get('sales_type');
									// preparations
									$arr_final = [];

									foreach($arr_to_insert as $po_num => $items){

										foreach($items as $item_id => $data){
											foreach($data as $d){
												if(isset($arr_final[$po_num][$item_id])){
													$arr_final[$po_num][$item_id]['qty'] += $d['qty'];
												} else {
													$arr_final[$po_num][$item_id] = $data[0];
												}
											}

										}
									}



									echo "<div class='alert alert-warning'>View Only Mode</div>";
								}
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