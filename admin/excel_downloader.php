<?php

	include_once '../core/admininit.php';
	include_once 'includes/labels.php';
	$fn = Input::get('downloadName');
	$fn();

	function member_terms(){
		$time_start = microtime(true);

		$filename = "member-terms-" . date('m-d-Y-H-i-s-A') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");
		$user = new User();
		$mem = new Member_term();
		$search = Input::get('search');
		$member_id = Input::get('member_id');
		$status = Input::get('status');
		$sales_type = Input::get('sales_type');
		$branch_id= Input::get('branch_id');
		$user_id= Input::get('user_id');

		if($sales_type){
			$sales_type = json_decode($sales_type,true);
		}
		?>
		<div id="no-more-tables">
			<div class="table-responsive">

				<?php
					//$targetpage = "paging.php";
					$limit = 40000;
					$countRecord = $mem->countRecord($user->data()->company_id, $search,$member_id,$status,$sales_type,$branch_id,$user_id);

					$total_pages = $countRecord->cnt;

					$stages = 3;
					$page = 0;
					$page = (int)$page;
					if($page) {
						$start = ($page - 1) * $limit;
					} else {
						$start = 0;
					}

					$company_inv = $mem->get_record($user->data()->company_id, $start, $limit, $search,$member_id,$status,$sales_type,$branch_id,$user_id);
					getpagenavigation($page, $total_pages, $limit, $stages);
					if($company_inv) {
				?>
				<table class='table' id='tblTerms' border=1>
					<thead>
					<tr>
						<TH><?php echo MEMBER_LABEL; ?></TH>
						<th>Requested by</th>
						<th>Item</th>
						<th>Qty</th>
						<th>Type</th>
						<TH>Price</TH>
						<TH>Adjustment</TH>
						<th>Adjusted Price</th>
						<th>Terms</th>
						<th>Created</th>
						<th>Status</th>
						<th>Transaction</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<?php
						$prod = new Product();
						$status = ['','Pending','Approved','Declined','Used transaction'];
						$ttype = ['All transaction','Single Transaction'];
						foreach($company_inv as $s) {
							$price = $prod->getPrice($s->item_id);
							$branch_adjustment = ($s->branch_adjustment) ? $s->branch_adjustment : 0;
							$adjusted_price = $s->adjustment +  ($price->price + $branch_adjustment);
							$adjusted_price = number_format($adjusted_price,2);

							?>
							<tr class='border_top'>
								<td  data-title="<?php echo MEMBER_LABEL; ?>">
									<?php
										if($s->lastname){
											echo ucwords(escape($s->lastname . ", " . $s->firstname . " " . $s->middlename));
										} else {
											echo "For all " .MEMBER_LABEL . "s";
										}

									?>
									<span class='span-block text-danger'>
										<?php if($s->remarks){
											echo " * " . $s->remarks;
										}?>
									</span>
								</td>
								<td data-title="Requested by"><?php echo ucwords(escape($s->uln . ", " . $s->ufn . " " . $s->umn)) ?></td>
								<td data-title="Item"><?php echo escape( $s->item_code) . "<small class='text-danger' style='display:block;'>". escape($s->description)."</small>"; ?></td>
								<td data-title="Qty" ><?php echo escape( number_format($s->qty,2)); ?></td>
								<td data-title="Type"><?php echo escape($s->type); ?></td>
								<td data-title="Price" class='text-danger'><?php echo escape( number_format($price->price+$branch_adjustment,2)); ?></td>
								<td data-title="Adjustment"><?php echo escape( number_format($s->adjustment,2)); ?></td>
								<td data-title="Adjusted price"><?php echo escape($adjusted_price); ?></td>
								<td data-title="Terms"><?php echo escape($s->terms); ?></td>
								<td data-title="Created"><?php echo date('F d, Y',$s->created);?></td>
								<td data-title="Status"><strong class='text-danger'><?php echo $status[$s->status];?></strong></td>
								<td data-title="Transaction"><strong class='text-danger'><?php echo $ttype[$s->transaction_type];?></strong></td>
								<td data-title="">
									<?php
										if($s->status == 1 && $user->hasPermission('m_terms')){
											?>
											<?php
										}
									?>
									<?php
										if($s->status == 2 && $user->hasPermission('m_terms')){
											?>
											<?php
										}
									?>
								</td>
							</tr>
							<?php
						}
						} else {
						?>
						<tr>
							<td colspan='8'><h3><span class='label label-info'>No Record Found...</span></h3></td>
						</tr>
						<?php
					}
					?>
					</tbody>
				</table>
			</div>
		</div>
		<?php
		$time_end = microtime(true);
		$execution_time = number_format($time_end - $time_start,4);
		Log::addLog(
			$user->data()->id,
			$user->data()->company_id,
			"DOWNLOAD MEMBER TERMS - ".$execution_time,
			'excel_downloader.php'
		);
	}
	
	function creditMonitoring(){
		$time_start = microtime(true);


		$filename = "member-credit-" . date('m-d-Y-H-i-s-A') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");
		// pages,
		$user = new User();
		$mem = new Member_credit();
		$search = addslashes(Input::get('search'));
		$type= addslashes(Input::get('paid_type'));
		$dt_from= addslashes(Input::get('dt_from'));
		$dt_to= addslashes(Input::get('dt_to'));
		$branch_id = Input::get('branch_id');
		$terminal_id = Input::get('terminal_id');
		$sales_type = Input::get('sales_type');
		$cid = $user->data()->company_id;


		if(!$user->hasPermission('credit_all')){
			$branch_id = [$user->data()->branch_id];
		}
		if($branch_id && is_numeric($branch_id)){
			$branch_id = [$branch_id];
		} else if(!is_array($branch_id)) {
			$branch_id = json_decode($branch_id,true);
		}
		if($terminal_id && is_numeric($terminal_id)){
			$terminal_id = [$terminal_id];
		} else if(!is_array($terminal_id)) {
			$terminal_id = json_decode($terminal_id,true);
		}
		//print_r($terminal_id);
		// add filter if agent
		$user_id = 0;
		if($user->hasPermission('wh_agent')){
			// get all his clients
			$user_id = $user->data()->id;
		}
		$hasDoc =false;
		$docs = [];
		if(Configuration::getValue('doc_util') == 1){
			$hasDoc = true;
			$doc_color = new Doc_color();
			$doc_colors = $doc_color->get_active('doc_colors',['1','=','1']);

			if($doc_colors){
				foreach($doc_colors as $dc){
					$docs[$dc->id] = $dc->name;
				}
			}
		}
		$isAquaBest = Configuration::isAquabest();
		?>
		<div id="no-more-tables">
			<div class="table-responsive">
				<table class='table' id='tblSales' border=1>
					<thead>
					<tr>
						<TH>Name</TH>
						<TH>Branch</TH>
						<th>Agent</th>
						<?php if($isAquaBest){
							?>
							<th>Station</th>
							<th>Remarks</th>
							<?php
						}?>
						<th>Invoice</th>
						<th>DR</th>
						<th>PR</th>
						<TH>Credit</TH>
						<th>Date</th>
						<TH>Paid</TH>
						<TH>Remaining</TH>
						<th>Payment Details</th>
					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						if($type == 2){
							$limit = 10000;
						} else {
							$limit = 10000;
						}

						$countRecord = $mem->countRecord($cid, $search, $type,$dt_from,$dt_to,$branch_id,$terminal_id,$sales_type,$user_id);

						$total_pages = isset($countRecord->cnt) ? $countRecord->cnt : 0;
						$total_pages;
						$stages = 3;
						$page = (0);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}
						$over1 = 0;
						$over2 = 0;
						$over3 = 0;
						$over4 = 0;
						$company_inv = $mem->get_member_record($cid, $start, $limit, $search, $type,$dt_from,$dt_to,$branch_id,$terminal_id,$sales_type,$user_id);
						getpagenavigation($page, $total_pages, $limit, $stages);

						if($company_inv) {
							$prev_mem = '';

							foreach($company_inv as $s) {
								$amt = $s->amount;
								$paid =  $s->amount_paid;
								$pendingamount = $amt - $paid;
								if($prev_mem != $s->member_id){
									$bordertop = 'border-top:1px solid #ccc;';
								} else {
									$bordertop = "";
								}
								$prev_mem = $s->member_id;
								if($pendingamount == 0 && $s->status == 1){
									$labelstat = "<div class='alert alert-info'><span class='glyphicon glyphicon-info-sign'></span> Fully Paid</div>";
								} else if($pendingamount == 0 && $s->status == -1){
									$labelstat = "<div class='alert alert-info'><span class='glyphicon glyphicon-info-sign'></span> For approval</div>";
								} else{
									$labelstat= "<button style='margin:2px;width:130px;' data-member_id='$s->member_id' data-is_cod='$s->is_cod' data-id='$s->id' data-paid='$paid' data-pending='$pendingamount' data-amt='$amt' class='btn btn-default btn-sm btnPayment' ><i class='fa fa-money'></i> Payment</button>";
								}
								$paymentjson = json_decode($s->json_payment,true);
								$detpay = "<div class='panel panel-default'>";
								$detpay .= "<div class='panel-body'>";

								if(count($paymentjson) > 0){
									$detpay = "<div class='panel panel-default'>";
									$detpay .= "<div class='panel-body'>";
									foreach($paymentjson as $pp){
										if(!is_numeric($pp['amount'])){
											$pp['amount'] = 0;
										}
										if(isset($pp['remarks']) && !empty($pp['remarks'])){
											$payRem = "<small style='display:block'>Remarks: <span class='text-danger'>" . $pp['remarks'] . "</span></small>";
										} else {
											$payRem = '';
										}
										$detpay .="<p>$pp[fn] received <span class='text-danger'>". number_format($pp['amount'],2)."</span> on ".date('M d, Y',$pp['date'])." $payRem</p>";
									}

								} else {
									$detpay .="<p class='text-danger'><span class='glyphicon glyphicon-exclamation-sign'></span> No payment yet.</p>";
								}
								$detpay .= "</div>";
								$detpay .= "</div>";
								if($s->name){
									$n = "N: " .$s->name;
								} else {
									$n ='';
								}
								$ctrnum = '';
								$doc_receive='';
								if($s->invoice){
									$ctrnum .= "<span  class='span-block'><strong>Inv#</strong> ". escape($s->invoice) . "</span>";
								}
								if($s->dr){
									$ctrnum .=  "<span  class='span-block'>DR# ". escape($s->dr) . "</span>";
								}
								if($s->ir){
									$ctrnum .=  "<span  class='span-block'>PR# ". escape($s->ir) . "</span>";
								}
								if($s->sr){
									$ctrnum .=  "<span  class='span-block'>SR# ". escape($s->sr) . "</span>";
								}
								if($hasDoc && $s->docs){

									$doc_ex = explode(',',$s->docs);
									$doc_receive ='<br><strong>Received:</strong> <br>';
									foreach($doc_ex as $dex){
										$doc_type= isset($docs[$dex]) ? $docs[$dex] : '';
										$doc_receive .= "<span class='span-block text-danger'>$doc_type</span>";
									}

								}


								// calculate aging

								$bgwarn = "";

								$to_branch_name='';
								if($s->to_branch_name){
									$to_branch_name = $s->to_branch_name;
								}

								?>
								<tr class='<?php echo $bgwarn; ?>' data-total='<?php echo $s->amount - $s->amount_paid; ?>'>
									<td data-title="Name" class='text-danger'>
										<?php echo ucwords(escape($s->lastname . ", " . $s->firstname . " " . $s->middlename)) . " "; ?>
									</td>
									<td><?php echo ucwords($to_branch_name) . " "; ?></td>
									<td><?php echo ucwords(escape($s->ufn . " " . $s->uln)); ?></td>
									<td><?php echo ucwords($s->station_name) . " "; ?></td>
									<td><?php echo ucwords($s->wh_remarks) . " "; ?></td>
									<td><?php echo $s->invoice; ?></td>
									<td><?php echo $s->dr; ?></td>
									<td><?php echo $s->ir; ?></td>
									<td  ><?php echo number_format(escape($s->amount),2); ?></td>
									<td ><?php echo date('m/d/Y',escape($s->solddate)); ?></td>
									<td><?php echo number_format(escape($s->amount_paid),2); ?> </td>
									<td>
										<?php echo number_format(escape($s->amount - $s->amount_paid),2); ?>
										<?php if($s->charges != 0.00){
											?>
											<br>
												Unpaid Freight: <?php echo  number_format($s->charges,2); ?>


											<?php
										}?>
									</td>
									<td style='<?php echo $bordertop; ?>' >

										<?php echo $detpay; ?> <br><?php echo escape($n); ?>

									</td>

								</tr>
								<?php
							}

						} else {
							?>
							<tr>
								<td colspan='8'><h3><span class='label label-info'>No Record Found...</span></h3></td>
							</tr>
							<?php
						}
					?>
					</tbody>
				</table>

			</div>
		</div>
		<?php
		$time_end = microtime(true);
		$execution_time = number_format($time_end - $time_start,4);
		Log::addLog(
			$user->data()->id,
			$user->data()->company_id,
			"DOWNLOAD CREDIT MONITORING - ".$execution_time,
			'excel_downloader.php'
		);
	}
	
	function products(){
		$time_start = microtime(true);

		$filename = "products-" . date('m-d-Y-H-i-s-A') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");
		$user = new User();
		$items = new Product();
		$category_id = Input::get('category_id');
		$date_from = Input::get('dt_from');
		$date_to = Input::get('dt_to');
		$search = Input::get('search');
		$sortby = Input::get('sortby');
		$cid = $user->data()->company_id;
		$branch_id = $user->data()->branch_id;




		?>

				<table class='table' border='1' id='tblSales'>
					<thead>
					<tr>
						<TH>Barcode</TH>
						<TH>Item Code</TH>
						<TH>Description</TH>
						<TH>Price</TH>
						<TH>Main Category</TH>
						<TH>Sub Category</TH>
						<TH>Created</TH>
						<TH></TH>
					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";

						$limit = 20000;
						$start = 0;
						$company_items = $items->get_product_record($cid, $start, $limit, $search, $sortby,$category_id,$branch_id,false,$date_from,$date_to);

						if($company_items) {
							foreach($company_items as $s) {
								$pd = new Product($s->id);
								$price = $pd->getPrice($s->id);
							//	$itemchar = $pd->getItemChar($s->id);
							//  $priceHistory = $pd->getPriceHistory($s->id);
							//	if($itemchar) {
							//		$itemcharjson = json_encode($itemchar);
							//	} else {
							//		$itemcharjson = '';
							//	}
								$main_category ="";
								$sub_category = "";
								if($s->parent_parent_name){
									$main_category = $s->parent_parent_name;
									$sub_category = $s->name;
								} else if($s->parent_name){
									$main_category = $s->parent_name;
									$sub_category = $s->name;
								}else {
									$main_category = $s->name;

								}
								?>
								<tr>
									<td><?php echo escape($s->barcode) ?></td>
									<td><?php echo escape($s->item_code); ?></td>
									<td style='width:450px;'><?php echo escape($s->description); ?></td>
									<td><?php echo escape(number_format($price->price, 2)); ?></td>
									<td><?php echo escape($main_category); ?></td>
									<td><?php echo escape($sub_category); ?></td>
									<td><?php echo escape(date('m/d/Y H:i:s A', $s->created)); ?></td>
									<td>
									</td>
								</tr>
								<?php
							}
						} else {
							?>
							<tr>
								<td colspan='8'><h3><span class='label label-info'>No Record Found...</span></h3></td>
							</tr>
							<?php
						}
					?>
					</tbody>
				</table>

		<?php
		$time_end = microtime(true);
		$execution_time = number_format($time_end - $time_start,4);
		Log::addLog(
			$user->data()->id,
			$user->data()->company_id,
			"DOWNLOAD PRODUCT LIST - ".$execution_time,
			'excel_downloader.php'
		);
	}
	function testImg(){
		$filename = "products-" . date('m-d-Y-H-i-s-A') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");

		?>

		<table class='table' border='1' id='tblSales'>
			<thead>
			<tr>
				
				<TH>Item Code</TH>
				<th></th>
				
			</tr>
			</thead>
			<tbody>
				<tr height="100">
					<td>
						imte 1
					</td>
					
					<td width="120" align="center">
						<img src="http://dev.apollo.ph:81/item_images/1.jpg" height="100" width="100" alt="">
					</td>
				</tr>
			</tbody>
		</table>

		<?php
	}

	function serviceLog(){
		$time_start = microtime(true);

		$filename = "service-log-" . date('m-d-Y-H-i-s-A') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");

		$service_req = new Item_service_request();
		$user  = new User();

		$branch_id = Input::get('b');
		$user_id = Input::get('user_id');
		$member_id = Input::get('member_id');
		$service_type = Input::get('service_type');
		$service_type_2 = Input::get('service_type_2');
		$date_from = Input::get('date_from');
		$date_to = Input::get('date_to');
		$technician_id = Input::get('technician_id');
		$list = $service_req->get_record($user->data()->company_id,0,1000,$branch_id,$user_id,$member_id,$service_type,$date_from,$date_to,$technician_id,$service_type_2);
		$colspan = 4;
		$date_lbl = "No filtered date";
		if($date_to && $date_from){
			$date_lbl = $date_from . " - " . $date_to;
		}


		?>
		<table>
			<tr><th colspan="<?php echo $colspan; ?>"><?php echo "Date: $date_lbl"; ?></th></tr>
			<tr><th colspan="<?php echo $colspan; ?>"><?php echo "Generated by: " . $user->data()->firstname . " " . $user->data()->lastname ; ?></th></tr>
		</table>
		<table border="1" class="table">
			<thead><tr><th>Client</th><th>Status</th><th>Remarks</th><th>Technician</th></tr></thead>
			<tbody>
			<?php if($list){
				$primaryStatus = ['','Pending','For Evaluation','For Payment/Credit','Processed'];
				$techcls = new Technician();
				foreach($list as $item){
					if(isset($primaryStatus[$item->status])){
						$lblPrimaryStatus =  $primaryStatus[$item->status];
					} else {
						$lblPrimaryStatus = "Cancelled";
					}
					$rem_list = new Remarks_list();
					$remarksall = $rem_list->getServices($item->id,'service',$user->data()->company_id);
					$rmall = "";
					if($remarksall){
						foreach($remarksall as $rem){
							$rmall .= $rem->remarks . ", ";
						}
						$rmall = rtrim($rmall,', ');
					}
					$techids = $item->technician_id;
					$alltechnician = "No technician assign";
					if($techids) {
						$listech = $techcls->getTech($techids);
						if($listech) {
							$alltechnician = "";
							foreach($listech as $l) {
								$alltechnician .= "$l->name, ";
							}
							$alltechnician = rtrim($alltechnician,', ');
						}
					}
					?>
					<tr>
						<td><?php echo $item->mln; ?></td>
						<td><?php echo $lblPrimaryStatus; ?></td>
						<td><?php echo $rmall; ?></td>
						<td><?php echo $alltechnician; ?></td>
					</tr>
					<?php	
				}
				
			}?>
			</tbody>
		</table>
		<?php
		$time_end = microtime(true);
		$execution_time = number_format($time_end - $time_start,4);
		Log::addLog(
			$user->data()->id,
			$user->data()->company_id,
			"DOWNLOAD SERVICE LOG - ".$execution_time,
			'excel_downloader.php'
		);
	}
	function inventoryAudit(){
		$time_start = microtime(true);

		$filename = "inventory-audit-" . date('m-d-Y-H-i-s-A') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");

		// pages,
		$user = new User();
		$inv = new Inventory();
		$search = Input::get('search');
		$b = Input::get('b');
		$args = 0;


		?>

		<div id="no-more-tables">
			<div class="table-responsive">
				<table border=1 class='table' id='tblAuditAll'>
					<thead>
					<tr>
						<TH>Branch</TH>
						<TH>Rack</TH>
						<TH>Barcode</TH>
						<TH>Item Code</TH>
						<th>Description</th>
						<TH class='text-right'>Qty</TH>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 10000;
						$countRecord = $inv->countRecordAudit($user->data()->company_id, $search, $b);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_inv = $inv->get_audit_record($user->data()->company_id, $start, $limit, $search, $b);
						getpagenavigation($page, $total_pages, $limit, $stages);
						if($company_inv) {


							foreach($company_inv as $s) {



								?>
								<tr>
									<td data-title="Branch"><?php echo escape($s->name) ?></td>
									<td data-title="Rack" class='text-danger'><?php echo "<strong>" . escape($s->rack)."</strong>"; ?></td>
									<td data-title="Barcode"><?php echo escape($s->barcode) ?></td>
									<td data-title="Item code"><?php echo escape($s->item_code) ?><small class='text-danger span-block'><?php echo escape($s->category_name) ?></small></td>
									<td data-title="Description" class='text-muted'><?php echo escape($s->description) ?></td>

									<td data-title="Quantity" class='text-right' style='padding-right:20px;'>
										<strong>
											<?php
												echo formatQuantity($s->qty);
											?>
										</strong>
									</td>
									<td>
										<?php echo strtolower($s->unit_name); ?>
									</td>

								</tr>
								<?php
							}
						} else {
							?>
							<tr>
								<td colspan='8'><h3><span class='label label-info'>No Record Found...</span></h3></td>
							</tr>
							<?php
						}
					?>
					</tbody>
				</table>
			</div>
		</div>
		<?php
		$time_end = microtime(true);
		$execution_time = number_format($time_end - $time_start,4);
		Log::addLog(
			$user->data()->id,
			$user->data()->company_id,
			"DOWNLOAD INVENTORY AUDIT LOG - " .$execution_time,
			'excel_downloader.php'
		);
	}
	function backload(){
		$time_start = microtime(true);


		$filename = "backload-" . date('m-d-Y-H-i-s-A') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");

		// pages,
		$user = new User();
		$order = new Wh_order();
		$search = Input::get('search');
		$b = Input::get('branch_id');
		$m = Input::get('member_id');
		$status = Input::get('status');
		$user_id = Input::get('user_id');
		$from = Input::get('txtFrom');
		$to = Input::get('txtTo');
		$search = trim($search);
		$cid = $user->data()->company_id;
		$args = Input::get('page');



		?>

		<div id="no-more-tables">
			<div class="table-responsive">
				<table border=1 class='table' id='tblSales'>
					<thead>
					<tr>
						<TH>ID</TH>
						<th><?php echo INVOICE_LABEL; ?></th>
						<th><?php echo DR_LABEL; ?></th>
						<th><?php echo PR_LABEL; ?></th>
						<TH>Branch</TH>
						<th>Request by</th>
						<TH>Member</TH>
						<TH>Created At</TH>
						<TH>Item Code</TH>
						<TH>Description</TH>
						<TH>QTY</TH>
					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 5000;
						$countRecord = $order->countRecordBackload($cid, $search, $b,$m,$status,$user_id,$from,$to);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_inv = $order->get_record_backload($cid, $start, $limit, $search, $b,$m,$status,$user_id,$from,$to);
						getpagenavigation($page, $total_pages, $limit, $stages);
						if($company_inv) {
							foreach($company_inv as $s) {

								?>
								<tr>
									<td style='border-top:1px solid #ccc;' data-title="ID"><strong><?php echo $s->wh_orders_id?></strong></td>
									<td style='border-top:1px solid #ccc;' data-title="<?php echo INVOICE_LABEL; ?>"><?php echo escape($s->invoice)?></td>
									<td style='border-top:1px solid #ccc;' data-title="<?php echo DR_LABEL; ?>"><?php echo escape($s->dr)?></td>
									<td style='border-top:1px solid #ccc;' data-title="<?php echo PR_LABEL; ?>"><?php echo escape($s->pr)?></td>
									<td style='border-top:1px solid #ccc;' data-title="Branch"><?php echo escape($s->branch_name)?></td>
									<td style='border-top:1px solid #ccc;'  data-title="Request by" class='text-muted'><?php echo escape(ucwords($s->lastname . ", " . $s->firstname . " " . $s->middlename))?></td>
									<td style='border-top:1px solid #ccc;' data-title="Member"><?php echo escape(ucwords($s->mln . ", " . $s->mfn . " " . $s->mmn))?></td>
									<td style='border-top:1px solid #ccc;'  data-title="Created at"><?php echo date('m/d/Y',$s->created); ?></td>
									<td style='border-top:1px solid #ccc;'  data-title="Item Code"><?php echo $s->item_code; ?></td>
									<td style='border-top:1px solid #ccc;'  data-title="Created at"><?php echo $s->description; ?></td>
									<td style='border-top:1px solid #ccc;'  data-title="Qty"><strong class='text-danger'><?php echo formatQuantity($s->backload_qty); ?></strong></td>
								</tr>
								<?php
							}
						} else {
							?>
							<tr>
								<td colspan='8'><h3><span class='label label-info'>No Record Found...</span></h3></td>
							</tr>
							<?php
						}
					?>
					</tbody>
				</table>
			</div>
		</div>
		<?php
		$time_end = microtime(true);
		$execution_time = number_format($time_end - $time_start,4);
		Log::addLog(
			$user->data()->id,
			$user->data()->company_id,
			"DOWNLOAD BACKLOAD - " .$execution_time,
			'excel_downloader.php'
		);
	}

	function checkMon(){
		$time_start = microtime(true);

		$filename = "check-" . date('m-d-Y-H-i-s-A') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");
		// pages,
		$user = new User();
		$cheque = new Cheque();
		$search = Input::get('search');
		$check_type = Input::get('check_type');
		$mem_id = Input::get('member_id');
		$dt1 = Input::get('dt1');
		$dt2 = Input::get('dt2');
		$mem_id = Encryption::encrypt_decrypt('decrypt', $mem_id);
		$branch_id = Input::get('branch_id');
		$terminal_id = Input::get('terminal_id');
		$sales_type = Input::get('sales_type');
		$with_terms = Input::get('with_terms');
		$agent_id = Input::get('agent_id');
		$cid = $user->data()->company_id;
		$args = 0;



		if($branch_id){
			$branch_id = json_decode($branch_id,true);
		}

		?>

		<?php
		//$targetpage = "paging.php";
		$limit = 5000;

		$countRecord = $cheque->countRecord($cid, $search, $check_type, $mem_id, $dt1,$dt2,$branch_id,$terminal_id,$sales_type,$with_terms);

		$total_pages = $countRecord->cnt;

		$stages = 3;
		$page = ($args);
		$page = (int)$page;
		if($page) {
			$start = ($page - 1) * $limit;
		} else {
			$start = 0;
		}

		$company_inv = $cheque->get_cheque_record($cid, $start, $limit, $search, $check_type, $mem_id, $dt1,$dt2,$branch_id,$terminal_id,$sales_type,$with_terms);
		getpagenavigation($page, $total_pages, $limit, $stages);

			if($company_inv) {
			?>
			<div id="no-more-tables">
				<div class="table-responsive">
					<table class='table' id='tblSales' border='1'>
						<thead>
						<tr>


							<TH>Invoice</TH>
							<TH>DR</TH>
							<TH>PR</TH>
				<?php if(Configuration::isAquabest()){
					?>
							<TH>SR</TH>
					<?php } ?>
							<TH>Sold date</TH>
							<TH>Terms</TH>
							<TH>Sales Type</TH>
							<TH>Client</TH>
							<TH>Station</TH>
							<TH>Agent/Cashier</TH>
							<TH>Name</TH>
							<TH>Check #</TH>
							<TH>Bank</TH>
							<th>Maturity</th>
							<TH>Amount</TH>
							<th></th>

						</tr>
						</thead>
						<tbody>
						<?php
							$sales = new Sales();

							$total_check = 0;
							foreach($company_inv as $s) {

								$total_check += $s->amount;
								$station_name = '';
								if($s->station_name){
									$station_name = "<span class='span-block'>".$s->station_name."</span>";
								}
								$s->salestype_name = ($s->salestype_name) ? $s->salestype_name : 'N/A';
								$user_name = "N/A";
								if($s->ufn && $s->uln){
									$user_name = ucwords($s->ufn ." ". $s->uln);
								}

								?>
								<tr>

									<td data-title='Invoice' style=''>
										<span class='span-block'> <?php echo (isset($s->invoice) && $s->invoice != 0) ? "<strong class='text-danger'>" . escape($s->invoice) . "</strong>" : '<i class="fa fa-ban"></i>'; ?></span >
										</td>
									<td>
										<span class='span-block'><?php echo (isset($s->dr) && $s->dr != 0) ? "<strong class='text-danger'>" . escape($s->dr) . "</strong>" : '<i class="fa fa-ban"></i>'; ?></span >
									</td>
									<td>
										<span class='span-block'> <?php echo (isset($s->ir) && $s->ir != 0) ? "<strong class='text-danger'>" . escape($s->ir) . "</strong>" : '<i class="fa fa-ban"></i>'; ?></span >
									</td>
								<?php if(Configuration::isAquabest()){
									?>
									<td>
										<span class='span-block'> <?php echo (isset($s->sr) && $s->sr != 0) ? "<strong class='text-danger'>" . escape($s->sr) . "</strong>" : '<i class="fa fa-ban"></i>'; ?></span >
									</td>
									<?php } ?>
									<td data-title='Sold' style='<?php echo $borderTop?>'>
										<?php echo escape(date('m/d/Y', $s->sold_date)) ?>
									</td>
									<td>
										<small class='span-block text-danger'><?php  echo  ($s->terms) ? $s->terms: 'N/A'; ?></small>
									</td>

									<td data-title='Client' style='<?php echo $borderTop?>' >

										<?php echo "<span class='span-block'><strong></strong> " . ucwords(escape ($s->salestype_name)) . "</span>"; ?>
									</td>
									<td>
										<?php
											echo "<span class='span-block'><strong></strong>" . ucwords(escape ($s->mln)) . "</span>";
											?>
									</td>
									<td>
										<?php
											echo $station_name;
										?>
										</td>
									<td>
										<?php echo "<span class='span-block'><strong></strong>" .$user_name . "</span>"; ?>

										<?php if($s->wh_remarks){
											?>
											<span class='span-block text-muted'><?php echo $s->wh_remarks; ?></span>
											<?php
										}?>
									</td>
									<?php
										if($s->lastname && $s->firstname){
											$chname = ucwords(escape($s->lastname . ", " . $s->firstname . " " . $s->middlename));
										} else {
											$chname = "<i class='fa fa-ban'></i>";
										}
									?>
									<td data-title='Name' style='' >

										<span class='span-block'><strong></strong> <?php echo $chname ?></span>
									</td>
									<td>
										<span class='span-block'><strong></strong> <?php echo escape($s->check_number) ?></span>
									</td>
									<td>
										<span class='span-block'><strong></strong> <?php echo escape($s->bank) ?></span>
									</td>
									<td>
										<span class='span-block'><strong></strong><?php echo escape(date('m/d/Y', $s->payment_date)) ?></span>
									</td>
									<td>
										<span class='span-block'><strong></strong> <?php echo number_format(escape($s->amount), 2); ?></span>
									</td>
									<td data-title='Status' style=''>
										<?php
											$now = time();
											$payment_date = $s->payment_date;
											$dtmaturity = getTimeCheque($payment_date - $now);
											if($dtmaturity && $s->status == 1) {
												echo $dtmaturity;
											} else {
												$chequestatus = array('', 'Collected', 'DAIF', 'Bounce','Others');
												echo $chequestatus[$s->status];
											}
										?>
									</td>


								</tr>
								<?php
							}
						?>
						</tbody>
					</table>
				</div>
			</div>

			<?php
		} else {
			?>
			<h3><span class='label label-info'>No Record Found...</span></h3>

			<?php
		}

		$time_end = microtime(true);
		$execution_time = number_format($time_end - $time_start,4);
		Log::addLog(
			$user->data()->id,
			$user->data()->company_id,
			"DOWNLOAD CHECK MONITORING - " . $execution_time,
			'excel_downloader.php'
		);
	}

	function creditCardMon(){
		$time_start = microtime(true);

		$filename = "credit-" . date('m-d-Y-H-i-s-A') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");
		$user = new User();
		$credit_card = new Credit();

		$search = Input::get('search');
		$dt1 = Input::get('dt1');
		$dt2 = Input::get('dt2');


		$cid = $user->data()->company_id;


		$limit = 5000;

		$total_pages = 0;
		$stages = 3;
		$page = (0);
		$page = (int)$page;
		if($page) {
			$start = ($page - 1) * $limit;
		} else {
			$start = 0;
		}

		$company_op = $credit_card->get_active_record($cid, $start, $limit, $search,$dt1,$dt2);

		if($company_op) {
			?>
			<div id="no-more-tables">
				<table class='table table-bordered table-condensed' border="1" id='tblSummaryOP'>
					<thead>
					<tr>
						<TH>ID</TH>
						<TH><?php echo MEMBER_LABEL; ?></TH>
						<TH><?php echo INVOICE_LABEL; ?></TH>
						<TH><?php echo DR_LABEL; ?></TH>
						<TH><?php echo PR_LABEL; ?></TH>
						<TH>Bank</TH>
						<TH>Card type</TH>
						<TH>Approval Code</TH>
						<TH>Trace Number</TH>
						<TH>Date</TH>
						<TH>Amount</TH>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<?php

						foreach($company_op as $o) {


							?>
							<tr>
								<td><strong><?php echo escape($o->id); ?></strong></td>
								<td>
									<?php echo $o->member_name; ?>
								</td><td>
									<?php echo $o->invoice; ?>
								</td>
								<td><?php echo $o->dr; ?></td>
								<td><?php echo $o->ir; ?></td>
								<td><?php echo $o->bank_name; ?></td>
								<td><?php echo $o->card_type; ?></td>
								<td><?php echo $o->approval_code; ?></td>
								<td><?php echo $o->trace_number; ?></td>
								<td><?php echo date('F d, Y H:i:s A',$o->date); ?></td>
								<td><?php echo number_format($o->amount,2); ?></td>

								<td></td>
							</tr>
							<?php
						}
					?>
					</tbody>
				</table>
			</div>
			<?php
		} else {
			?>
			<div class='alert alert-info'>No record found</div>
			<?php
		}
		$time_end = microtime(true);
		$execution_time = number_format($time_end - $time_start,4);
		Log::addLog(
			$user->data()->id,
			$user->data()->company_id,
			"DOWNLOAD CREDIT CARD MONITORING - " . $execution_time,
			'excel_downloader.php'
		);

	}
	function bankTransferMon(){
		$time_start = microtime(true);


		$filename = "bank-transfer-" . date('m-d-Y-H-i-s-A') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");
		$bt = new Bank_transfer();
		$user = new User();
		$search = Input::get('search');
		$dt1 = Input::get('dt1');
		$dt2 = Input::get('dt2');
		$limit = 5000;
		$cid = $user->data()->company_id;
		$page = (0);
		$page = (int)$page;
		if($page) {
			$start = ($page - 1) * $limit;
		} else {
			$start = 0;
		}
		$company_op = $bt->get_active_record($cid, $start, $limit, $search,$dt1,$dt2);

		if($company_op) {
			?>
			<div id="no-more-tables">
				<table class='table table-bordered table-condensed' border="1" id='tblSummaryOP'>
					<thead>
					<tr>
						<TH>ID</TH>
						<TH><?php echo MEMBER_LABEL; ?></TH>
						<TH><?php echo INVOICE_LABEL; ?></TH>
						<TH><?php echo DR_LABEL; ?></TH>
						<TH><?php echo PR_LABEL; ?></TH>
						<TH>Bank To</TH>
						<TH>Bank From</TH>
						<TH>Bank Account #</TH>
						<TH>Amount</TH>
						<TH>Created</TH>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<?php
						foreach($company_op as $o) {
							?>
							<tr>
								<td><strong><?php echo escape($o->id); ?></strong></td>
								<td>
									<?php echo $o->member_name; ?>
								</td>
								<td>
									<?php echo $o->invoice; ?>
								</td>
								<td><?php echo $o->dr; ?></td>
								<td><?php echo $o->ir; ?></td>
								<td><?php echo $o->bankto_name; ?></td>
								<td><?php echo $o->bankfrom_name; ?></td>
								<td><?php echo $o->bankfrom_account_number; ?></td>
								<td><?php echo number_format($o->amount,2); ?></td>
								<td><?php echo date('F d, Y H:i:s A',$o->date); ?></td>
								<td></td>
							</tr>
							<?php
						}
					?>
					</tbody>
				</table>
			</div>
			<?php
		} else {
			?>
			<div class='alert alert-info'>No record found</div>
			<?php
		}
		$time_end = microtime(true);
		$execution_time = number_format($time_end - $time_start,4);
		Log::addLog(
			$user->data()->id,
			$user->data()->company_id,
			"DOWNLOAD BANK TRANSFER - " . $execution_time,
			'excel_downloader.php'
		);

	}

	function cashMon(){
		$time_start = microtime(true);

		$user = new User();
		$filename = "cash-payment-" . date('m-d-Y-H-i-s-A') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");

		$search = Input::get('search');
		$dt1 = Input::get('dt1');
		$dt2 = Input::get('dt2');
		$limit = 5000;
		$cash = new Cash();
		$countRecord = $cash->countRecord($user->data()->company_id, $search,$dt1,$dt2);
		$total_pages = $countRecord->cnt;
		$stages = 3;
		$page = (0);
		$page = (int)$page;
		if($page) {
			$start = ($page - 1) * $limit;
		} else {
			$start = 0;
		}
		$company_op = $cash->get_active_record($user->data()->company_id, $start, $limit, $search,$dt1,$dt2);

		if($company_op) {
			?>

				<table class='table table-bordered table-condensed' border='1' id='tblSummaryOP'>
					<thead>
					<tr>
						<TH>ID</TH>
						<th><?php echo MEMBER_LABEL; ?></th>
						<TH><?php echo INVOICE_LABEL; ?></TH>
						<TH><?php echo DR_LABEL; ?></TH>
						<TH><?php echo PR_LABEL; ?></TH>
						<th>Sold date</th>
						<TH>Amount</TH>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<?php

						foreach($company_op as $o) {


							?>
							<tr>
								<td><strong><?php echo escape($o->id); ?></strong></td>
								<td>
									<?php echo $o->member_name; ?>
								</td>
								<td>
									<?php echo $o->invoice; ?>
								</td>
								<td><?php echo $o->dr; ?></td>
								<td><?php echo $o->ir; ?></td>
								<td><?php echo date('F d, Y H:i:s A',$o->sold_date); ?></td>
								<td><?php echo number_format($o->amount,2); ?></td>

								<td></td>
							</tr>
							<?php
						}
					?>
					</tbody>
				</table>

			<?php
		} else {
			?>
			<div class='alert alert-info'>No record found</div>
			<?php
		}

		$time_end = microtime(true);
		$execution_time = number_format($time_end - $time_start,4);
		Log::addLog(
			$user->data()->id,
			$user->data()->company_id,
			"DOWNLOAD CASH PAYMENT - " . $execution_time,
			'excel_downloader.php'
		);

	}

	function inventoryReport(){

		$time_start = microtime(true);

		$filename = "inventory-report-" . date('m-d-Y-H-i-s-A') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");
		$user = new User();
		$inv = new Inventory();
		$search = Input::get('search');
		$b = Input::get('b');
		$r = Input::get('r');
		$start_date =  Input::get('from');
		$end_date =Input::get('to');
		$group_by =Input::get('group_by');
		$display_type =Input::get('display_type');



		if(!$start_date){
			$start_date = strtotime(date('F Y'));
		} else {
			$start_date = strtotime($start_date);
		}

		if(!$end_date){
			$end_date = time();
		} else {
			$end_date = strtotime($end_date . "1 day -1 min");
		}

		$cid = $user->data()->company_id;

		$hide_rack='';

		if($group_by == 1){
			$hide_rack = "display:none;";
		}


		$is_cebu_hiq = (Configuration::thisCompany('cebuhiq')) ? true : false ;
		$lblOnHand = "On Hand";
		if($is_cebu_hiq){
			$lblOnHand = "System Qty";
		}
		if($is_cebu_hiq){
				$limit = 10000;

				$countRecord = $inv->countRecordReport($cid, $search, $start_date, $end_date, $b, $r, $group_by, $is_cebu_hiq);
				$total_pages = $countRecord->cnt;

				$stages = 3;
				$page = 0;
				$page = (int)$page;
				if($page) {
					$start = ($page - 1) * $limit;
				} else {
					$start = 0;
				}

				$company_inv = $inv->get_report_record($cid, $start, $limit, $search, $start_date, $end_date, $b, $r, $group_by, $is_cebu_hiq);
				getpagenavigation($page, $total_pages, $limit, $stages);
				if($company_inv) {

					$arr = [];
					foreach($company_inv as $s) {
						$beg_qty = $s->qty - $s->in_qty2 + $s->out_qty2; // ibawas/dagdag lahat hanggang current date


						$onhand = $beg_qty + $s->in_qty - $s->out_qty;


						$ending_inventory  = (int) $s->ending_qty;
						$variants = $s->ending_qty - $onhand;
						$total_cost = $s->ending_qty * $s->product_cost;

						$name = $s->item_code . "<br><small class='span-block text-danger'>".$s->description."</small>";


						$arr[] = [
							'name' => $name,
							'category_name' => $s->category_name,
							'parent_name' => $s->parent_name,
							'system_qty' =>  $onhand,
							'ending_qty' =>  $ending_inventory,
							'variants' =>  $variants,
							'total_cost' =>  $total_cost,
						];
					}
					 echo "<p><strong>Actual Inventory Report</strong></p>";
					$bname = "None";
					if($b){
						$branch = new Branch($b);
						$bname = $branch->data()->name;
					}

					 echo "<p>Branch: <strong>" .$bname . "</strong> <br> Date From: <strong>" . date('F d, Y', $start_date) . "</strong>  Date To: <strong>" . date('F d, Y', $end_date) . "</strong></p>";


					echo "<table border=1>";
					echo "<tr><th>Category</th><th>Name</th><th>System Qty</th><th>Actual Qty</th><th>Variants</th><th>Total Cost</th></tr>";
					$last_categ = "";
					$first = true;

					$cur_system_qty = 0;
					$cur_actual_qty = 0;
					$cur_variants = 0;
					$cur_total_cost = 0;
					$cur_system_qty_parent = 0;
					$cur_actual_qty_parent = 0;
					$cur_variants_parent = 0;
					$cur_total_cost_parent = 0;
					$total_system_qty = 0;
					$total_actual_qty = 0;
					$total_variants = 0;
					$total_total_cost = 0;

					$last_parent = '';

					foreach($arr as $a){
						$categ = '';
						$parent = '';


						if($last_categ != $a['category_name']){
							$last_categ = $a['category_name'];
							$categ = $a['category_name'];
							if($a['parent_name']){
								$categ = $a['parent_name'] . "-" . $categ;
							}

							if(!$first){
								echo "<tr><th></th><th></th><th>$cur_system_qty</th><th>$cur_actual_qty</th><th>$cur_variants</th><th>$cur_total_cost</th></tr>";

								$cur_system_qty = 0;
								$cur_actual_qty = 0;
								$cur_variants = 0;
								$cur_total_cost = 0;

							}

						}

						if($last_parent !=  $a['parent_name']){

							if(!$first){
								echo "<tr><th>$last_parent</th><th></th><th>$cur_system_qty_parent</th><th>$cur_actual_qty_parent</th><th>$cur_variants_parent</th><th>$cur_total_cost_parent</th></tr>";

								$cur_system_qty_parent = 0;
								$cur_actual_qty_parent = 0;
								$cur_variants_parent = 0;
								$cur_total_cost_parent = 0;

							}

							$last_parent = $a['parent_name'];
							$parent = $a['parent_name'];

						}



						$cur_system_qty += $a['system_qty'];
						$cur_actual_qty += $a['ending_qty'];
						$cur_variants += $a['variants'];
						$cur_total_cost += $a['total_cost'];

						$cur_system_qty_parent += $a['system_qty'];
						$cur_actual_qty_parent += $a['ending_qty'];
						$cur_variants_parent += $a['variants'];
						$cur_total_cost_parent += $a['total_cost'];

						$total_system_qty += $a['system_qty'];
						$total_actual_qty += $a['ending_qty'];
						$total_variants += $a['variants'];
						$total_total_cost += $a['total_cost'];


						echo "<tr>";
						echo "<td>";
						echo $categ;
						echo "</td>";
						echo "<td>$a[name]</td>";
						echo "<td>$a[system_qty]</td>";
						echo "<td>$a[ending_qty]</td>";
						echo "<td>$a[variants]</td>";
						echo "<td>$a[total_cost]</td>";
						echo "</tr>";

						if($first){
							$first = false;
						}

					}

					echo "<tr><th></th><th></th><th>$cur_system_qty</th><th>$cur_actual_qty</th><th>$cur_variants</th><th>$cur_total_cost</th></tr>";
					echo "<tr><th>$last_parent</th><th></th><th>$cur_system_qty_parent</th><th>$cur_actual_qty_parent</th><th>$cur_variants_parent</th><th>$cur_total_cost_parent</th></tr>";
					echo "<tr><th></th><th></th><th>".($total_system_qty)."</th><th>".($total_actual_qty)."</th><th>".($total_variants)."</th><th>".($total_total_cost)."</th></tr>";
					echo "</table>";


				}
		} else {
			$bname = "None";
			if($b){
				$branch = new Branch($b);
				$bname = $branch->data()->name;
			}

			?>

			<div id="no-more-tables">
				<div class="table-responsive">

					<?php echo "<p>Branch: <strong>" .$bname . "</strong> <br> Date From: <strong>" . date('F d, Y', $start_date) . "</strong>  Date To: <strong>" . date('F d, Y', $end_date) . "</strong></p>"; ?>
					<table class='table' border='1' id='tblSales'>
						<thead>
						<tr>
							<TH>Branch</TH> <?php if(!$group_by) {
								?>
								<TH>Rack</TH>							<?php
							} ?>

							<TH>Item Code</TH>
							<th>Description</th>
							<?php if($display_type == 1) { ?>
							<TH class='text-right'>Beginning Qty<br><strong>(<?php echo date('F d, Y', $start_date); ?>)</strong>
							</TH>
							<th class='text-right'>Stock In</th>
							<th class='text-right'>Stock Out</th>
							<th class='text-right'>Amend Qty</th>
							<th class='text-right'><?php echo $lblOnHand; ?>
								<br><strong>(<?php echo date('F d, Y', $end_date); ?>)</strong></th>

							<?php
								if($is_cebu_hiq) {
									?>
								<th>Actual Qty</th>
								<th class='text-right'>Variants</th>
								<th class='text-right'>Product Cost</th>
						<?php } ?>
						<?php } // End display type ?>

							<?php if(date('F d, Y', $end_date) != date('F d, Y') || $display_type == 2) {
								?>
								<th class='text-right'>On Hand<br><strong>(<?php echo date('F d, Y'); ?>)</strong>
								</th>							<?php
							} ?>


						</tr>
						</thead>
						<tbody>

						<?php

							//$targetpage = "paging.php";
							$limit = 10000;

							$countRecord = $inv->countRecordReport($cid, $search, $start_date, $end_date, $b, $r, $group_by, $is_cebu_hiq);
							$total_pages = $countRecord->cnt;

							$stages = 3;
							$page = 0;
							$page = (int)$page;
							if($page) {
								$start = ($page - 1) * $limit;
							} else {
								$start = 0;
							}

							$company_inv = $inv->get_report_record($cid, $start, $limit, $search, $start_date, $end_date, $b, $r, $group_by, $is_cebu_hiq);
							getpagenavigation($page, $total_pages, $limit, $stages);
							if($company_inv) {
								$invProduct = new Product();
								foreach($company_inv as $s) {
									$beg_qty = $s->qty - $s->in_qty2 + $s->out_qty2 + $s->amend_qty2; // ibawas/dagdag lahat hanggang current date


									$onhand = $beg_qty + $s->in_qty - $s->out_qty - $s->amend_qty;



									$amend_qty = 0;
									if($beg_qty < 0){
										if(!$s->in_qty  && !$s->out_qty){
											$amend_qty =0;
											$onhand = 0;
										} else {
											$amend_qty = abs($beg_qty);
										}

										$beg_qty = 0;

										if($s->in_qty){
											$beg_qty = $beg_qty - $s->in_qty;
										}
										if($s->out_qty){
											$beg_qty = $beg_qty + $s->out_qty;
										}
										if($onhand < 0){
											$onhand = 0;
											$amend_qty = 0;
										}

									} else {
										$amend_qty = $s->amend_qty;
									}

									?>
									<tr>
										<td  data-title="Branch"><?php echo escape($s->name) ?></td>
										<td  data-title="Rack" class='text-danger'><?php echo "<strong>" . escape($s->rack)."</strong>"; ?></td>
										<td  data-title="Item code"><?php echo escape($s->item_code) ?></td>
										<td   data-title="Description" class='text-muted'><?php echo escape($s->description) ?></td>
										<?php if($display_type == 1){ ?>
											<td  class='text-right' data-title="Beginning"><?php echo escape(formatQuantity($beg_qty)) ?></td>
											<td    class='text-right' data-title="Stock In"><?php echo escape(formatQuantity($s->in_qty)) ?></td>
											<td    class='text-right' data-title="Stock Out"><?php echo escape(formatQuantity($s->out_qty)) ?></td>
											<td   class='text-right' data-title="Stock Out"><?php echo escape(formatQuantity($amend_qty)) ?></td>
											<td  class='text-right'  data-title="On hand"><?php echo escape(formatQuantity($onhand)) ?></td>

											<?php if($is_cebu_hiq){ ?>
												<td   ><?php echo escape(formatQuantity($s->ending_qty)) ?></td>
												<td ><?php echo escape(formatQuantity($s->ending_qty - $onhand)); ?></td>
												<td   ><?php echo escape(number_format(($s->ending_qty * $s->product_cost),2)); ?></td>
											<?php } ?>
										<?php } // end display two ?>
										<?php
											if(date('F d, Y',$end_date) != date('F d, Y') || $display_type == 2){
												?>
												<td  class='text-right'  data-title="On hand"><?php echo escape(formatQuantity($s->qty)) ?></td>
											<?php } ?>

									</tr>							<?php
								}
							} else {
								?>
								<tr>
									<td colspan='8'><h3><span class='label label-info'>No Record Found...</span></h3>
									</td>
								</tr>							<?php
							}
						?>
						</tbody>
					</table>
				</div>
			</div>		<?php
		}

		$time_end = microtime(true);
		$execution_time = number_format($time_end - $time_start,4);

		Log::addLog(
			$user->data()->id,
			$user->data()->company_id,
			"DOWNLOAD INVENTORY REPORT - " . $execution_time,
			'excel_downloader.php'
		);

	}

	function inventories(){

		$time_start = microtime(true);


		$filename = "inventories-" . date('m-d-Y-H-i-s-A') . ".xls";

		header("Content-Disposition: attachment; filename=\"$filename\"");

		header("Content-Type: application/vnd.ms-excel");


		$user = new User();
		$inv = new Inventory();
		$search = Input::get('search');
		$b = Input::get('b');
		$r = Input::get('r');
		$si = Input::get('s');
		$txtRack = Input::get('txtRack');
		$category_id = Input::get('category_id');
		$cid = $user->data()->company_id;
		$allcategcat = [];


		if($category_id){
			$catecls = new Category();
			$allchild = $catecls->getAllChild($category_id);
			$allcategcat[] = $category_id;
			if($allchild){
				foreach($allchild as $child){
					if($child->lev2 && !in_array($child->lev2,$allcategcat)){
						$allcategcat[] = $child->lev2;
					}
					if($child->lev3 && !in_array($child->lev3,$allcategcat)){
						$allcategcat[] = $child->lev3;
					}
					if($child->lev4 && !in_array($child->lev4,$allcategcat)){
						$allcategcat[] = $child->lev4;
					}
				}
			}
		}
		$imploded = implode($allcategcat,",");

		$branch_list = "";
		if($b){
			$b = json_decode($b,true);
			foreach($b as $curb){
				$branch_list .= $curb . ",";
			}
			$branch_list = rtrim($branch_list,',');
			$b = $branch_list;
		}

		$is_cebuhiq = Configuration::thisCompany('cebuhiq') ? true : false;
		$limit = 10000;

		$company_inv = $inv->get_sales_record($cid, 0, $limit, $search, $b, $r, $si, $txtRack, $imploded);
		if($is_cebuhiq){  // cebuhiq specific format
			if($company_inv){
				$total_current = 0;

				$qty_current = 0;

				$total_prev = 0;

				$qty_prev = 0;

				$total_cost = 0;



					foreach($company_inv as $i){



						$name = $i->item_code . "<br><small class='span-block text-danger'>".$i->description."</small>";




						$arr[] = [
							'name' => $name,
							'category_name' => $i->category_name,
							'parent_name' => $i->parent_name,
							'rack' => $i->rack,
							'cost' =>  ($i->product_cost * $i->qty),
							'qty' =>  $i->qty,
						];
					}



				echo "<table border=1>";
				echo "<tr><th>Category</th><th>Name</th><th>Qty</th><th>Total Cost</th></tr>";
				$last_categ = "";
				$first = true;

				$cur_qty = 0;
				$cur_cost = 0;
				$cur_qty_parent = 0;
				$cur_cost_parent = 0;
				$total_qty = 0;
				$total_cost = 0;
				$last_parent = '';

				foreach($arr as $a){
					$categ = '';
					$parent = '';


					if($last_categ != $a['category_name']){
						$last_categ = $a['category_name'];
						$categ = $a['category_name'];
						if($a['parent_name']){
							$categ = $a['parent_name'] . "-" . $categ;
						}

						if(!$first){
							echo "<tr><th></th><th></th><th>$cur_qty</th><th>$cur_cost</th></tr>";

							$cur_qty = 0;
							$cur_cost = 0;

						}

					}

					if($last_parent !=  $a['parent_name']){

						if(!$first){
							echo "<tr><th>$last_parent</th><th></th><th>$cur_qty_parent</th><th>$cur_cost_parent</th></tr>";

							$cur_qty_parent = 0;
							$cur_cost_parent = 0;
						}
						$last_parent = $a['parent_name'];
						$parent = $a['parent_name'];
					}


					$cur_cost += str_replace(',','',$a['cost']);
					$cur_qty += str_replace(',','',$a['qty']);
					$cur_cost_parent += str_replace(',','',$a['cost']);
					$total_cost += str_replace(',','',$a['cost']);
					$cur_qty_parent += str_replace(',','',$a['qty']);
					$total_qty += str_replace(',','',$a['qty']);

					echo "<tr>";
					echo "<td>";
					echo $categ;
					echo "</td>";
					echo "<td>$a[name]</td>";
					echo "<td>$a[qty]</td>";
					echo "<td>$a[cost]</td>";
					echo "</tr>";
					if($first){
						$first = false;
					}
				}

				echo "<tr><th></th><th></th><th>$cur_qty</th><th>$cur_cost</th></tr>";

				echo "<tr><th>$last_parent</th><th></th><th>$cur_qty_parent</th><th>$cur_cost_parent</th></tr>";
				echo "<tr><th></th><th></th><th>".number_format($total_qty)."</th><th>".number_format($total_cost)."</th></tr>";

				echo "</table>";
			}

		} else { // other format
			?>
			<div id="no-more-tables">
				<div class="table-responsive">
					<table class='table' border='1' id='tblSales'>
						<thead>
						<tr>
							<TH>Branch</TH>
							<TH>Rack</TH>
							<TH>Barcode</TH>
							<TH>Item Code</TH>
							<th>Description</th> <?php if((Configuration::getValue('hide_price_inv') == 1)) { ?>
								<th class='text-right'>Price</th>						<?php } ?>
							<?php if($user->hasPermission('dl_inv_pr')) {
								?>
								<th>Cost</th>							<?php
							} ?>

							<TH class='text-right'>Qty</TH>
							<th>Unit</th>
							<th>Critical Order</th>
						</tr>
						</thead>
						<tbody>
						<?php
							//$targetpage = "paging.php";



							if($company_inv) {

								$invProduct = new Product();

								foreach($company_inv as $s) {
									$price = $invProduct->getPrice($s->item_id);
									$alldis = '';
									if($s->rack == 'Display') {
										if($s->display_location) {
											if(strpos($s->display_location, ',') > 0) {
												$explodeddis = explode(',', $s->display_location);
												foreach($explodeddis as $ed) {
													$displayLocation = new Display_location($ed);
													$alldis .= " <span style='font-size:0.8em;' class='label label-primary'>" . $displayLocation->data()->name . "</span> ";
												}

											} else {
												$displayLocation = new Display_location($s->display_location);
												$alldis = $displayLocation->data()->name;
											}
											$alldis = "<br>" . $alldis;
										}
									}
									?>

									<tr>
										<td data-title="Branch"><?php echo escape($s->name) ?></td>
										<td data-title="Rack" class='text-danger'><?php echo "<strong>" . escape($s->rack) . "</strong>" . $alldis; ?></td>
										<td data-title="Barcode"><?php echo escape($s->barcode) ?></td>
										<td data-title="Item code"><?php echo escape($s->item_code) ?></td>
										<td style='width:450px;' data-title="Description" class='text-muted'><?php echo escape($s->description) ?></td> <?php if((Configuration::getValue('hide_price_inv') == 1)) { ?>
											<td data-title="Price" class='text-right'><?php echo escape(number_format($price->price, 2)) ?></td>									<?php } ?>
										<?php if($user->hasPermission('dl_inv_pr')) {
											?>
											<td><?php echo $s->product_cost; ?></td>										<?php
										} ?>
										<td data-title="Quantity" class='text-right' style='padding-right:20px;'>
											<strong>
												<?php
													echo (formatQuantity($s->qty));
												?>
											</strong>
										</td>
										<td data-title="Unit" >
												<?php
													echo $s->unit_name;
												?>
										</td>
										<td>
											<?php echo  (isset($s->order_point)) ? $s->order_point : 0; ?>
										</td>
									</tr>
							<?php
								}
							} else {
								?>
								<tr>
									<td colspan='8'><h3><span class='label label-info'>No Record Found...</span></h3>
									</td>
								</tr>
								<?php
							}
						?>
						</tbody>
					</table>
				</div>
			</div>		<?php
		}

		$time_end = microtime(true);
		$execution_time = number_format($time_end - $time_start,4);
		Log::addLog(
			$user->data()->id,
			$user->data()->company_id,
			"DOWNLOAD INVENTORIES - " . $execution_time,
			'excel_downloader.php'
		);

	}

	function sales(){
		$time_start = microtime(true);


		$filename = "sales-" . date('m-d-Y-H-i-s-A') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");
		$user = new User();
		$sales = new Sales();
		$search = Input::get('search');
		$tran_type = Input::get('tran_type');
		$b = Input::get('b');
		$t = Input::get('t');
		$type = Input::get('type');
		$m = Input::get('mem_id');
		$sort_by = Input::get('sortby');
		$item_id = Input::get('item_id');
		$date_from = Input::get('date_from');
		$date_to = Input::get('date_to');

		if($user->hasPermission('is_franchisee')){
			$b = $user->data()->branch_id;
		}


		$sales_type_ar_cls = new Sales_type();
		$types_ar = $sales_type_ar_cls->getMySalesType($user->data()->id);
		if(!$tran_type){
			$arr_sales_type = [];
			if($types_ar){ // agent has sales type
				$user_id = 0;
				foreach($types_ar as $current_type){
					$arr_sales_type[] = $current_type->id;
				}
				$tran_type = $arr_sales_type;
			}
		} else {
			if($types_ar) $user_id = 0; // reset user id if agent has sales type
		}



	?>
		<div id="no-more-tables">
			<div class="table-responsive">
				<p>Displaying the last 2000 records only</p>
				<table border=1 class='table' id='tblSales'>
					<thead>
					<tr>
						<TH title='Sort by invoice' data-sort=' order by IF (IFNULL(s.invoice,0) = 0, 1, 0), s.invoice * 1 ' class='page_sortby'><?php echo MEMBER_LABEL; ?></TH>
						<TH title='Sort by invoice' data-sort=' order by IF (IFNULL(s.invoice,0) = 0, 1, 0), s.invoice * 1 ' class='page_sortby'><?php echo INVOICE_LABEL; ?></TH>
						<TH title='Sort by dr' data-sort=' order by IF (IFNULL(s.dr,0) = 0, 1, 0), s.dr * 1 ' class='page_sortby'><?php echo DR_LABEL; ?></TH>
						<TH title='Sort by sr' data-sort=' order by IF (IFNULL(s.sr,0) = 0, 1, 0), s.ir * 1 ' class='page_sortby'><?php echo PR_LABEL; ?></TH>
						<TH title='Sort by item' data-sort='order by i.item_code ' class='page_sortby'>Item Code</TH>
						<TH title='Sort by price' data-sort='order by pr.price ' class='page_sortby'>Price</TH>
						<TH title='Sort by quantity' data-sort='order by s.qtys ' class='page_sortby'>Qty</TH>

						<TH >Adjustment</TH>
						<TH >Adjusted</TH>
						<TH title='Sort by total' data-sort='order by ((s.qtys * price)-s.discount) ' class='page_sortby'>Total</TH>
						<TH title='Sort by quantity' data-sort='order by s.sold_date ' class='page_sortby'>Date sold</TH>
						<th>Sales type</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";




						$company_sales = $sales->getDownloadRecord($user->data()->company_id, $search, $b, $t, $m, $type, $sort_by,$tran_type,$item_id,$date_from,$date_to);


						if($company_sales) {

							foreach($company_sales as $s) {

								$total_current = (($s->qtys * $s->price) + $s->adjustment + $s->member_adjustment) - ($s->discount + $s->store_discount);

								if($s->qtys == 0) continue;

								/*

								$adjusted_date = '';

								if($s->member_adjustment){

									$memadj = new Member_term();
									$member_adjustment_data = $memadj->getAdjustmentMember($s->member_id,$s->item_id);
									if($member_adjustment_data){
										$adjusted_date = date('m/d/y',$member_adjustment_data->created);
									}

								}

								*/


								?>
								<tr  >
									<td data-title="Member">
										<strong><?php echo ($s->member_name) ? escape($s->member_name) : ""; ?></strong>
									</td>
									<td data-title="Invoice">
										<strong><?php echo ($s->invoice) ? escape($s->pref_inv.padLeft($s->invoice).$s->suf_inv) : ""; ?></strong>
									</td>
									<td data-title="Dr">
										<strong><?php echo ($s->dr) ? escape($s->pref_dr.padLeft($s->dr).$s->suf_dr): "" ?></strong></td>

									<td data-title="PR">
										<strong><?php echo ($s->ir) ? escape($s->pref_ir.padLeft($s->ir).$s->suf_ir) : "" ?></strong></td>
									<td data-title="Item"><?php echo escape($s->item_code) . "<br><small class='text-danger'>" . escape($s->description) . "</small>"; ?></td>
									<td data-title="Price"><?php echo escape(number_format($s->price, 2)); ?>
									</td>
									<td data-title="Quantity"><?php echo escape($s->qtys) ?></td>

									<td data-title="Adjustment"><?php echo escape(number_format($s->member_adjustment, 2)) ?></td>
									<td data-title="Adjusted">
										<?php echo escape(number_format($total_current/$s->qtys, 2)) ?>
											<?php
											/*	if($adjusted_date){
													echo " <br>(" .$adjusted_date . ")";
												} */
											?>


									</td>
									<td data-title="Total">
										<strong><?php echo escape(number_format($total_current, 2))  ?></strong>
									</td>
									<td data-title="Date"><?php echo escape(date('m/d/Y ', $s->sold_date)); ?></td>
									<td data-title="Sales type"><?php echo  $s->sales_type_name; ?></td>

									<td>

									</td>
								</tr>
								<?php
							}
						} else {
							?>
							<tr>
								<td colspan='8'><h3><span class='label label-info'>No Record Found...</span></h3></td>
							</tr>
							<?php
						}
					?>
					</tbody>
				</table>
			</div>
		</div>
	<?php
		$time_end = microtime(true);
		$execution_time = number_format($time_end - $time_start,4);
		Log::addLog(
			$user->data()->id,
			$user->data()->company_id,
			"DOWNLOAD SALES REPORT - " . $execution_time,
			'excel_downloader.php'
		);

	} // end sales

	function padLeft($input){
		return str_pad($input, 5, "0", STR_PAD_LEFT);
	}

	function reports(){
		$time_start = microtime(true);

			$filename = "reports-" . date('m-d-Y-H-i-s-A') . ".xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");

			$payment_method = json_decode(Input::get('payment_method'),true);
			$branch = json_decode(Input::get('branch'),true);
			$terminal = json_decode(Input::get('terminal'),true);
			$item_type = json_decode(Input::get('item_type'),true);
			$category = json_decode(Input::get('category'),true);
			$memid = json_decode(Input::get('member'),true);
			$stationid = json_decode(Input::get('station'),true);
			$dateStart = Input::get('dateStart');
			$dateEnd = Input::get('dateEnd');
			$cashier = json_decode(Input::get('cashier'),true);
			$rdstrict = Input::get('rdStrict');
			$char = json_decode(Input::get('char'),true);
			$item_id = json_decode(Input::get('item_id'),true);
			$sales_type = json_decode(Input::get('sales_type'),true);

			$region = json_decode(Input::get('region'),true);
			$rdRegion = Input::get('rdRegion');
			$report_type = Input::get('report_type');
			$sort_by = Input::get('sort_by');
			$from_od = Input::get('from_od');
			$from_service = Input::get('from_service');
			$doc_type = Input::get('doc_type');
			$custom_string_query = Input::get('custom_string_query');
			$release_branch_id = Input::get('release_branch_id');
			$date_type = Input::get('date_type');
			$include_cancel = Input::get('include_cancel');

			$filterinfo = "";


			if($report_type == 1) {

				$sales = new Sales();
				$user = new User();
				// cash cheque
				$crud = new Crud();

				?>

				<div id="no-more-tables">
					<div class="table-responsive">

						<table border='1' class='table' id='tblSales'>
							<thead>
							<tr>
								<th >Branch</th>
								<th >Client</th>
								<TH title='Sort by invoice' data-sort=' order by IF (IFNULL(s.invoice,0) = 0, 1, 0), s.invoice * 1 ' class='page_sortby'><?php echo INVOICE_LABEL ?></TH>
								<TH title='Sort by dr' data-sort=' order by IF (IFNULL(s.dr,0) = 0, 1, 0), s.dr * 1 ' class='page_sortby'><?php echo DR_LABEL ?></TH>
								<TH title='Sort by ir' data-sort=' order by IF (IFNULL(s.ir,0) = 0, 1, 0), s.ir * 1 ' class='page_sortby'><?php echo PR_LABEL ?></TH>
								<TH title='Sort by sv' data-sort=' order by IF (IFNULL(s.sv,0) = 0, 1, 0), s.sv * 1 ' class='page_sortby'><?php echo "SV" ?></TH>
								<TH title='Sort by item' data-sort='order by it.item_code ' class='page_sortby'>Item Code</TH>
								<TH title='Sort by item' data-sort='order by it.description ' class='page_sortby'>Description</TH>
								<TH title='Sort by price' data-sort='order by pr.price ' class='page_sortby'>Price</TH>
								<TH title='Sort by quantity' data-sort='order by s.qtys ' class='page_sortby'>Qty</TH>
								<TH title='Sort by discount' data-sort='order by s.discount ' class='page_sortby'>Discount</TH>
								<TH>Adjustment</TH>
								<TH title='Sort by total' data-sort='order by ((s.qtys*pr.price)-s.discount) ' class='page_sortby'>Total</TH>
								<TH title='Sort by date' data-sort='order by s.sold_date ' class='page_sortby'>Date sold</TH>
								<TH title='Sort by date' >Delivery Date</TH>
								<th></th>
								<th></th>
							</tr>
							</thead>
							<tbody>
							<?php
								//$targetpage = "paging.php";

								if($item_id){
									$item_id=explode(',',$item_id);
								}

								$company_sales = $sales->getSalesForDownload($user->data()->company_id, $payment_method, $branch, $terminal, $item_type, $category, $memid, $stationid, $dateStart, $dateEnd, $cashier, $item_id, $sales_type, $sort_by,$from_od,"",$from_service,$doc_type,$custom_string_query,$release_branch_id,$date_type);

								if($company_sales) {

									$prevpid = 0;

									$totalsales = 0;

									foreach($company_sales as $s) {


										if($payment_method) {
											if($rdstrict == 1 && !in_array('5', $payment_method)) {
												$cashrp = $crud->get_active('cash', array('payment_id', '=', $s->payment_id));
												$chequepm = $crud->get_active('cheque', array('payment_id', '=', $s->payment_id));
												$creditpm = $crud->get_active('credit_card', array('payment_id', '=', $s->payment_id));
												$bankpm = $crud->get_active('bank_transfer', array('payment_id', '=', $s->payment_id));
												$conamountpm = $crud->get_active('payment_consumable', array('payment_id', '=', $s->payment_id));
												$confreepm = $crud->get_active('payment_consumable_freebies', array('payment_id', '=', $s->payment_id));
												$membercredits = $crud->get_active('member_credit', array('payment_id', '=', $s->payment_id));
												$deductions = $crud->get_active('deductions', array('payment_id', '=', $s->payment_id));
												if($deductions) {
													//1
													if(!in_array('9', $payment_method)) {
														continue;
													}
												}
												if($membercredits) {
													//1
													if(!in_array('8', $payment_method)) {
														continue;
													}
												}
												if($cashrp) {
													//1
													if(!in_array('1', $payment_method)) {
														continue;
													}
												}
												if($chequepm) {
													//2
													if(!in_array('2', $payment_method)) {
														continue;
													}
												}
												if($creditpm) {
													// 3
													if(!in_array('3', $payment_method)) {
														continue;
													}
												}
												if($bankpm) {
													// 4
													if(!in_array('4', $payment_method)) {
														continue;
													}
												}
												if($conamountpm) {
													// 6
													if(!in_array('6', $payment_method)) {
														continue;
													}
												}
												if($confreepm) {
													//7
													if(!in_array('7', $payment_method)) {
														continue;
													}
												}
											}
										}
										$cashier = new User($s->cashier_id);


										$pd = new Product($s->item_id);
										if($char) {
											$chars = $pd->getItemChar($s->item_id);
											$findchar = false;
											if($chars) {
												foreach($chars as $c) {
													if(in_array($c->characteristics_id, $char)) {
														$findchar = true;
														break;
													}
												}
											}

											if(!$findchar) {
												continue;
											}
										}


										$price = $pd->getPriceByPriceId($s->price_id);
										$sss = new Sales();


										$totalsales += ($s->qtys * $price->price) - $s->discount;

										$bundle_tr ="";
										if($s->is_bundle){
											$bundle = new Bundle();
											$bundle_list = $bundle->getBundleItem($s->item_id);
											if($bundle_list){
												foreach($bundle_list as $bundle_item){
													$bundle_tr .= "<tr>";
													$bundle_tr .= "<td></td><td></td><td></td><td></td><td></td><td></td>";
													$bundle_desc =   escape($bundle_item->description) ;
													$bundle_tr .= "<td>". $bundle_item->item_code." </td>";
													$bundle_tr .= "<td>". $bundle_desc ." </td>";
													$bundle_tr .= "<td></td>";
													$bundle_tr .= "<td class='text-right'>". ($bundle_item->child_qty * $s->qtys)."</td>";
													$bundle_tr .= "<td></td>";
													$bundle_tr .= "<td></td>";
													$bundle_tr .= "<td></td>";
													$bundle_tr .= "<td></td>";
													$bundle_tr .= "<td></td>";
													$bundle_tr .= "</tr>";
												}
											}
										}
										?>
										<tr>
											<td data-title='Terminal'>
												<?php
													if(!$s->tname) {
														$btname = "Caravan";
													} else {
														$btname = $s->bname;
													}
													echo "<strong>" . escape($btname) . "</strong>";
												?>
											</td>
											<td data-title='Client'>
												<?php echo ($s->member_name) ? escape($s->member_name) : "No client"; ?>
											</td>
											<td data-title='Invoice'>
												<?php echo ($s->invoice) ? escape($s->invoice) : "No invoice"; ?>
											</td>

											<td data-title='Dr'>
												<?php echo ($s->dr) ? escape($s->dr) : "No Dr" ?>
											</td>
											<td data-title='Ir'>
												<?php echo ($s->ir) ? escape($s->ir) : "No pr"; ?>
											</td>
											<td data-title='Ir'>
												<?php echo ($s->sv) ? escape($s->sv) : "No SV"; ?>
											</td>

											<td data-title='Item'><?php echo escape($pd->data()->item_code) ?></td>
											<td data-title='Description'><?php echo escape($pd->data()->description) ?></td>
											<td data-title='Price'><?php echo escape(number_format($price->price, 2)); ?>
											</td>
											<td data-title='Quantity'><?php echo escape($s->qtys) ?></td>
											<td data-title='Discount'><?php echo escape(number_format($s->discount, 2)) ?></td>
											<td data-title='Adjusment'><?php echo escape(number_format($s->adjustment, 2)) ?></td>
											<td data-title='Total' class='text-danger'>
												<strong><?php echo escape(number_format((($s->qtys * $s->price) + $s->adjustment + $s->member_adjustment) - ($s->discount + $s->store_discount), 2)) ?></strong>
											</td>
											<td data-title='Date sold'><?php echo escape(date('m/d/Y ', $s->sold_date)); ?></td>
											<td ><?php echo escape(date('m/d/Y ', $s->is_scheduled)); ?></td>
											<td>
												<?php
													if($s->status == 1) {
														echo "<span class='text-danger'>Cancelled</span>";
													}
												?>
											</td>
											<td >
											</td>

										</tr>
										<?php
										echo $bundle_tr;
									}
								} else {
									?>
									<tr>
										<td colspan='8'><h3><span class='label label-info'>No Record Found...</span></h3></td>
									</tr>
									<?php
								}
							?>
							</tbody>
						</table>
					</div>
				</div>
				<?php
				$time_end = microtime(true);
				$execution_time = number_format($time_end - $time_start,4);
				Log::addLog(
					$user->data()->id,
					$user->data()->company_id,
					"DOWNLOAD SALES REPORT (report page) - ".$execution_time,
					'excel_downloader.php'
				);
			} else 	if($report_type == 2) {

				$sales = new Sales();
				$user = new User();
				$company = new Company($user->data()->company_id);
				$colspan = 19;
				$dt_timeframe = "";
				if($dateStart && $dateEnd){
					$dt_timeframe .= date('F d, Y',$dateStart) . " " . date('F d, Y',$dateEnd);
				} else {

				}

				?>
				<table border=1>
					<thead>
					<tr>
						<th align='left' colspan="<?php echo $colspan; ?>"><?php echo $company->data()->name; ?></th>
					</tr>
					<tr>
						<th  align='left' colspan="<?php echo $colspan; ?>"><?php echo $company->data()->description; ?></th>
					</tr>
					<tr>
						<th align='left' colspan="<?php echo $colspan; ?>"><?php echo $company->data()->address; ?></th>
					</tr>
					<tr>
						<th align='left' colspan="<?php echo $colspan; ?>"></th>
					</tr>
					</thead>
				</table>
				<div id="no-more-tables">


					<table border='1' class='table' id='tblSales'>
						<thead>
						<tr>
							<TH width="200px" data-sort='order by IF (IFNULL(s.dr,0) = 0, 1, 0), s.dr * 1 ' class='page_sortby'><?php echo DR_LABEL; ?></TH>
							<TH width="200px" data-sort='order by IF (IFNULL(s.invoice,0) = 0, 1, 0), s.invoice * 1 ' class='page_sortby'><?php echo INVOICE_LABEL; ?></TH>
							<TH width="200px" data-sort='order by IF (IFNULL(s.ir,0) = 0, 1, 0), s.ir * 1 ' class='page_sortby'><?php echo PR_LABEL; ?></TH>
							<TH width="200px" data-sort='order by IF (IFNULL(s.sv,0) = 0, 1, 0), s.sv * 1 ' class='page_sortby'><?php echo "SV"; ?></TH>
							<TH width="200px" data-sort=' order by s.sold_date ' class='page_sortby'>Date sold</TH>
							<TH width="200px" >Delivery Date</TH>
							<th width="200px">Agent</th>
							<th width="200px">Client</th>
							<th width="200px">Client Address</th>
							<th width="200px">TIN</th>
							<th width="200px">Station</th>
							<th width="200px">Sales type</th>
							<th width="200px">Remarks</th>
							<TH width="200px" data-sort=' order by totalamount ' class='page_sortby' >Deduction</TH>
							<TH width="200px" data-sort=' order by totalamount ' class='page_sortby' >Total Sales</TH>
							<th>Unpaid Amount</th>
							<th>(Total/1.12) * 12%</th>
							<th></th>
							<th></th>
						</tr>
						</thead>
						<tbody>
						<?php
							//$targetpage = "paging.php";


							$company_sales = $sales->getSalesForDownload2($user->data()->company_id, $payment_method, $branch, $terminal, $item_type, $category, $memid, $stationid, $dateStart, $dateEnd, $cashier, $item_id, $sales_type,$sort_by,$from_od,$from_service,$release_branch_id,$doc_type,'',$date_type,$include_cancel);



							if($company_sales) {

								$prevpid = 0;
								$totalsales = 0;
								$crud = new Crud();
								foreach($company_sales as $s) {
									$inv = "";
									$dr =  "";
									$ir =  "";
									$sv =  "";
									$cancel_status = "";
									if($s->status == 1){
										$cancel_status = 'Cancelled';
									}
									$membercredits = $crud->get_active('member_credit', array('payment_id', '=', $s->payment_id));
									$deductions = $crud->get_active('deductions', array('payment_id', '=', $s->payment_id));
									$conamountpm = $crud->get_active('payment_consumable', array('payment_id', '=', $s->payment_id));
									if($payment_method) {
										if($rdstrict == 1 && !in_array('5', $payment_method)) {

											$cashrp = $crud->get_active('cash', array('payment_id', '=', $s->payment_id));
											$chequepm = $crud->get_active('cheque', array('payment_id', '=', $s->payment_id));
											$creditpm = $crud->get_active('credit_card', array('payment_id', '=', $s->payment_id));
											$bankpm = $crud->get_active('bank_transfer', array('payment_id', '=', $s->payment_id));
											$conamountpm = $crud->get_active('payment_consumable', array('payment_id', '=', $s->payment_id));
											$confreepm = $crud->get_active('payment_consumable_freebies', array('payment_id', '=', $s->payment_id));



											if($deductions) {
												//1
												if(!in_array('9', $payment_method)) {
													continue;
												}
											}
											if($membercredits) {
												//1
												if(!in_array('8', $payment_method)) {
													continue;
												}
											}
											if($cashrp) {
												//1
												if(!in_array('1', $payment_method)) {
													continue;
												}
											}
											if($chequepm) {
												//2
												if(!in_array('2', $payment_method)) {
													continue;
												}
											}
											if($creditpm) {
												// 3
												if(!in_array('3', $payment_method)) {
													continue;
												}
											}
											if($bankpm) {
												// 4
												if(!in_array('4', $payment_method)) {
													continue;
												}
											}
											if($conamountpm) {
												// 6
												if(!in_array('6', $payment_method)) {
													continue;
												}
											}
											if($confreepm) {
												//7
												if(!in_array('7', $payment_method)) {
													continue;
												}
											}
										}
									}
									if($s->invoice) {
										$inv = $s->pref_inv.padLeft($s->invoice);
									}
									if($s->dr) {
										$dr  = $s->pref_dr.padLeft($s->dr);
									}
									if($s->ir) {
										$ir  = $s->pref_ir.padLeft($s->ir);
									}if($s->sv) {
										$sv  = $s->pref_sv.padLeft($s->sv);
									}
									$member_name = 'None';
									if($s->mln) {
										$member_name = $s->mln;
									}
									$reservedby = "";
									$station = "";
									if($s->reserved_by_lastname){
										$reservedby = ucwords($s->reserved_by_lastname . ", " . $s->reserved_by_firstname);
									}
									if($s->station_name){
										$station = ucwords($s->station_name);
									}
									$unpaid = 0;
									if($membercredits){
										foreach($membercredits as $mcred){
											$unpaid += $mcred->amount - $mcred->amount_paid;
										}
									}
									$total_deductions = 0;
									if($deductions){
										foreach($deductions as $deduction){
											$total_deductions += $deduction->amount;
										}
									}
									if($conamountpm){
										foreach($conamountpm as $pm){
											$total_deductions += $pm->amount;
										}
									}
									$tax = (($s->totalamount - $total_deductions)/ 1.12 ) * 0.12;
									$after_tax = ($s->totalamount - $total_deductions) - $tax;

									if($s->status == 1){
										$tax = 0;
										$after_tax = 0;
										$total_deductions = 0;
										$unpaid = 0;
										$s->totalamount = 0;
									}

									
									?>
									<tr>
										<td width="200px"  data-title='Dr' style='border-bottom:1px solid #ccc;'><?php echo ($dr) ?></td>
										<td width="200px"  data-title='Invoice' style='border-bottom:1px solid #ccc;'><?php echo ($inv) ?></td>
										<td width="200px"  data-title='IR' style='border-bottom:1px solid #ccc;'><?php echo ($ir) ?></td>
										<td width="200px"  data-title='SV' style='border-bottom:1px solid #ccc;'><?php echo ($sv) ?></td>
										<td width="200px"  data-title='Date sold' style='border-bottom:1px solid #ccc;'><?php echo escape(date('m/d/Y', $s->sold_date)); ?></td>
										<td width="200px" ><?php echo escape(date('m/d/Y', $s->is_scheduled)); ?></td>
										<td width="200px"  data-title='Reserved by' style='border-bottom:1px solid #ccc;' class='text-success'><?php echo escape($reservedby) ?></td>
										<td width="200px"  data-title='Sold To' style='border-bottom:1px solid #ccc;' class='text-success'><?php echo escape(ucwords($member_name)) ?></td>
										<td width="200px"  data-title='Sold To' style='border-bottom:1px solid #ccc;' class='text-success'><?php echo escape(ucwords($s->personal_address)) ?></td>
										<td width="200px"  data-title='Sold To' style='border-bottom:1px solid #ccc;' class='text-success'><?php echo escape(ucwords($s->tin_no)) ?></td>
										<td width="200px"  data-title='Station' style='border-bottom:1px solid #ccc;' class='text-success'><?php echo escape($station) ?></td>
										<td width="200px"  data-title='Sales type' style='border-bottom:1px solid #ccc;' class=''>
											<?php
												echo escape(ucwords($s->sales_type_name));
											?>
										</td>
										<td width="200px"  style='border-bottom:1px solid #ccc;'>

											<?php echo $s->wh_remarks; ?>

										</td>
										<td><?php echo number_format($total_deductions,2); ?></td>
										<td width="200px"  data-title='Total' style='border-bottom:1px solid #ccc;' class='text-danger'>
											<strong><?php echo escape(number_format($s->totalamount - $total_deductions, 2)) ?></strong>
										</td>
										<td><?php echo number_format($unpaid,2) ?></td>
										<td><?php echo number_format($tax,2) ?></td>
										<td><?php echo number_format($after_tax,2) ?></td>
										<td><?php echo $cancel_status; ?></td>

									</tr>
									<?php
								}
							} else {
								?>
								<tr>
									<td colspan='8'><h3><span class='label label-info'>No Record Found...</span></h3>
									</td>
								</tr>
								<?php
							}
						?>
						</tbody>
					</table>
				</div>

				<?php
				$time_end = microtime(true);
				$execution_time = number_format($time_end - $time_start,4);
				Log::addLog(
					$user->data()->id,
					$user->data()->company_id,
					"DOWNLOAD SALES REPORT (report page) - ".$execution_time,
					'excel_downloader.php'
				);
			}

		}

	function inventoryMonitoring() {
		$time_start = microtime(true);

		$filename = "inventory-monitoring-" . date('m-d-Y-H-i-s-A') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");
		$user = new User();
		$inv = new Inventory_monitoring();
		$search = Input::get('search');
		$b = Input::get('b');
		$r = Input::get('r');
		$date_from = Input::get('from');
		$date_to = Input::get('to');
		$branch_id2 = Input::get('branch_id2');
		$cid = $user->data()->company_id;


		?>
		<div id="no-more-tables">
			<div class="table-responsive">
				<table class='table' border='1' id='tblSales'>
					<thead>
					<tr>
						<TH>Branch</TH>
						<th>Date</th>
						<TH>Rack</TH>
						<TH>Item Code</TH>
						<TH>Description</TH>
						<th>Prev</th>
						<th>Action</th>
						<th>Qty</th>
						<th>New Qty</th>
						<th>Remarks</th>
					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 10000;

						$company_inv = $inv->get_sales_record($cid, 0, $limit, $search, $b, $r,$date_from,$date_to,$branch_id2);

						if($company_inv) {
							$invProduct = new Product();
							foreach($company_inv as $s) {

								if($s->qty_di == 1) {
									$actionicon = "<span class='glyphicon glyphicon-plus'></span>";
								} else if($s->qty_di == 2) {
									$actionicon = "<span class='glyphicon glyphicon-minus'></span>";
								} else if($s->qty_di == 3) {
									$actionicon = "<span class='glyphicon glyphicon-arrow-right'></span>";
								}
								?>
								<tr>
									<td data-title="Branch"><?php echo escape($s->name) ?></td>
									<td data-title="Created"><?php echo escape(date('m/d/Y H:i:s A', $s->created)); ?></td>
									<td data-title="Rack" class='text-danger'><?php echo escape(($s->rack) ? $s->rack : 'No rack' ) ?></td>
									<td data-title="Item Code"><?php echo escape($s->item_code); ?></td>
									<td data-title="Description "><?php echo escape($s->description); ?></td>
									<td data-title="Prev Quantity">
										<?php echo formatQuantity($s->prev_qty) ?>
									</td>
									<td data-title="Action">
										<?php echo $actionicon; ?>
									</td>
									<td data-title="Qty">
										<?php echo formatQuantity($s->qty) ?>
									</td>
									<td data-title="New Qty">
										<?php echo formatQuantity($s->new_qty) ?>
									</td>
									<td><small><span class='text-danger'><?php echo ($s->remarks) ?></span></small></td>
								</tr>
								<?php
							}
						} else {
							?>
							<tr>
								<td colspan='8'><h3><span class='label label-info'>No Record Found...</span></h3></td>
							</tr>
							<?php
						}
					?>
					</tbody>
				</table>
			</div>
		</div>
		<?php
		$time_end = microtime(true);
		$execution_time = number_format($time_end - $time_start,4);
		Log::addLog(
			$user->data()->id,
			$user->data()->company_id,
			"DOWNLOAD INVENTORY MONITORING - ".$execution_time,
			'excel_downloader.php'
		);
	}

	function members(){
		$time_start = microtime(true);

		$search = addslashes(Input::get('search'));
		$salestype = addslashes(Input::get('salestype'));
		$char = addslashes(Input::get('char'));
		$agent_id = addslashes(Input::get('agent_id'));
		$region = addslashes(Input::get('region'));
		$date_from = (Input::get('date_from'));
		$date_to = (Input::get('date_to'));
		$salestype= ($salestype) ? $salestype : 0;
		$char= ($char) ? $char : 0;


		$filename = "members-" . date('m-d-Y-H-i-s-A') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");
		// pages,

		$mem = new Member();
		$user = new User();


		?>
		<div id="no-more-tables">
			<div class="table-responsive">
				<table class='table' id='tblSales' border="1">
					<thead>
					<tr>
						<TH>Name</TH>
						<TH>Address</TH>
						<TH><?php echo MEMBER_LABEL; ?> Since</TH>
						<TH>Created At</TH>
						<th>Address</th>
						<th>Contact Number</th>
						<th>Email</th>
						<th>Tin</th>
						<th>With/Without Inv</th>
						<th>Is Hold</th>
						<th>Payment Type</th>
						<th>Sales Type</th>

					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 10000;


						$cid =1;
						$start = 0;


						$company_inv = $mem->get_member_record($cid, $start, $limit, $search,$salestype,$char,0,$agent_id,$region,$date_from,$date_to);

						if($company_inv) {
							foreach($company_inv as $s) {
								$blacklist = '';
								if($s->is_blacklisted == 1) {
									$blacklist = 'Hold';
								}
								$inv = 'Without Invoice';
								if($s->with_inv == 1) {
									$inv = 'With Invoice';
								}
								if($s->member_since){
									$member_since = date('m/d/Y', $s->member_since);
								} else {
									$member_since = "N/A";
								}


								$agent_name = "";



								?>
								<tr>
									<td data-title="Name" width="350">
										<?php echo ucwords(escape($s->lastname)) ?>

									</td>
									<td><?php echo $s->personal_address; ?></td>
									<td data-title="<?php echo MEMBER_LABEL; ?> Since"><?php echo escape($member_since) ?></td>
									<td data-title=""><?php echo date('m/d/Y',$s->created) ?></td>
									<td> <?php echo $s->personal_address; ?></td>
									<td> <?php echo $s->contact_number; ?></td>
									<td> <?php echo $s->email; ?></td>
									<td> <?php echo $s->tin_no; ?></td>
									<td> <?php echo $inv; ?></td>
									<td> <?php echo $blacklist; ?></td>
									<td> <?php echo $s->payment_type; ?></td>
									<td> <?php echo $s->st_name; ?></td>


									
								</tr>
								<?php
							}
						} else {
							?>
							<tr>
								<td colspan='8'><h3><span class='label label-info'>No Record Found...</span></h3></td>
							</tr>
							<?php
						}
					?>
					</tbody>
				</table>
			</div>
		</div>
		<?php
		$time_end = microtime(true);
		$execution_time = number_format($time_end - $time_start,4);
		Log::addLog(
			$user->data()->id,
			$user->data()->company_id,
			"DOWNLOAD MEMBERS - " .$execution_time,
			'excel_downloader.php'
		);
	}

	function spareparts(){
		$time_start = microtime(true);

	$filename = "spareparts-" . date('m-d-Y-H-i-s-A') . ".xls";
	header("Content-Disposition: attachment; filename=\"$filename\"");
	header("Content-Type: application/vnd.ms-excel");
	// pages,

		$user = new User();
		$ci = new Composite_item();
		$search = Input::get('search');



?>
	<div id="no-more-tables">
		<div class="table-responsive">
			<table class='table' id='tblSales' border="1">
				<thead>
				<tr>
					<TH>Set</TH>
					<th>Raw</th>
					<TH>Qty</TH>
					<TH></TH>
				</tr>
				</thead>
				<tbody>
			<?php
				//$targetpage = "paging.php";
				$limit = 10000;
				$start = 0;

				$company_inv = $ci->get_sales_record($user->data()->company_id, $start, $limit,$search);

				if($company_inv) {
					$prevset = "";
					$hasPerm = $user->hasPermission('spare_part_add');
					$sparetype = new Spare_type();
					$sparetypes = $sparetype->get_active('spare_type',array('company_id' ,'=',$user->data()->company_id));
					$sparetypearr = [];
					if($sparetypes){
						foreach($sparetypes as $indtype){
							$sparetypearr[$indtype->id] = $indtype->name;
						}
					}
					$inv = new Inventory();
					foreach($company_inv as $s) {
						$borderTop ='';
						$setItemCode = '';

						if($prevset != $s->item_id_set){
							$setItemCode = $s->set_item_code . "<small class='text-danger' style='display:block;  '>".$s->set_description."</small>";
							$borderTop = "border-top:1px solid #ddd;";

						}
						$prevset =  $s->item_id_set;

						?>
						<tr data-id='<?php echo Encryption::encrypt_decrypt('encrypt',$s->id); ?>'>
							<td style='<?php echo $borderTop; ?>' data-title="Set Item"><?php echo ($setItemCode)  ?></td>
							<td style='<?php echo $borderTop; ?>'  data-title="Raw Item"><?php echo escape($s->item_code). "<small class='text-danger' style='display:block;  '>".$s->description."</small>" ?></td>
							<td style='<?php echo $borderTop; ?>'  data-title="Qty"><?php echo formatQuantity(escape($s->qty)) ?></td>
							<td style='<?php echo $borderTop; ?>'  data-title="Action">
							</td>
						</tr>
						<?php
					}
				} else {
					?>
					<tr>
						<td colspan='4'><h3><span class='label label-info'>No Record Found...</span></h3></td>
					</tr>
					<?php
				}
			?>
			</tbody>
		</table>
	</div>
</div>
<?php
		$time_end = microtime(true);
		$execution_time = number_format($time_end - $time_start,4);
		Log::addLog(
			$user->data()->id,
			$user->data()->company_id,
			"DOWNLOAD SPAREPARTS - " . $execution_time,
			'excel_downloader.php'
		);
	}



	function priceList(){
	$time_start = microtime(true);

	$filename = "pricelist-" . date('m-d-Y-H-i-s-A') . ".xls";
	header("Content-Disposition: attachment; filename=\"$filename\"");
	header("Content-Type: application/vnd.ms-excel");

	$item_price_adjustment = new Item_price_adjustment();

	$branch_id = (Input::get('branch_id')) ? Input::get('branch_id') : 0;
	$price_group_id = (Input::get('price_group_id')) ? Input::get('price_group_id') : 0;

	$search_item = Input::get('search_item');
	$limit_by = Input::get('limit_by');

	$dt_from = Input::get('dt_from');
	$dt_to = Input::get('dt_to');

	$user = new User();




?>
<div id="no-more-tables">

	<table class='table' id='tblSales' border=1>
		<thead>
		<tr>
			<th>Branch</th>
			<th>Remarks</th>
			<th>Batch</th>
			<th>Date Modified</th>
			<th>Item</th>
			<TH>Original Price</TH>
			<th>Adjustment</th>
			<th>Adjusted Price</th>
			<th></th>
		</tr>
		</thead>
		<tbody>
		<?php
			//$targetpage = "paging.php";
			if($limit_by){
				$limit = $limit_by;
			} else {
				$limit = 5000;
			}
			$user = new User();

			$start = 0;

			$company_sales = $item_price_adjustment->forDownload($user->data()->company_id, $start, $limit,$search_item,$branch_id,$dt_from,$dt_to);


			if($company_sales) {
				$prodcls = new Product();

				$branchCls = new Branch($branch_id);
				$branch_name = $branchCls->data()->name;

				foreach($company_sales as $s) {
					$price = $s->price;
					$adjustment = 0;
					$edit_id = 0;
					$remarks = "<i class='fa fa-ban'></i>";
					$batch_dt = "<i class='fa fa-ban'></i>";
					if(!$branch_id && !$price_group_id){
						$branch_name = "Masterlist";
					} else if($branch_id){

						$adjustment = ($s->adjustment) ? $s->adjustment : 0;
						$edit_id =  ($s->spid) ? $s->spid : 0;

					}else if($price_group_id){
						$price_group_cls = new Price_group($price_group_id);
						$branch_name = $price_group_cls->data()->name;
						$ind_adjcls = new Item_price_adjustment();
						$get_adjustment = $ind_adjcls->getAdjustment(0,$s->id,$price_group_id);

						if(isset($get_adjustment->adjustment)){
							$adjustment = $get_adjustment->adjustment;
							$edit_id = $get_adjustment->id;
						}
					}


					?>
					<tr data-branch_id='<?php echo $branch_id; ?>' data-price_group_id='<?php echo $price_group_id; ?>'  data-item_id='<?php echo $s->id; ?>' data-id='<?php echo $edit_id; ?>' data-adjustment='<?php echo $adjustment; ?>'>
						<td data-title='Branch' style='border-top:1px solid #ccc;'>
							<?php echo escape($branch_name); ?>
						</td>
						<td data-title='Remarks'  style='border-top:1px solid #ccc;'>
							<?php echo ($remarks); ?>


						</td>
						<td data-title='Batch'  style='border-top:1px solid #ccc;'>
							<?php echo ($batch_dt); ?>
						</td>
						<td data-title='Item'  style='border-top:1px solid #ccc;'>
							<?php if($s->modified) echo  date("m/d/Y H:s:i A",$s->modified); ?>
						</td>
						<td data-title='Item'  style='border-top:1px solid #ccc;'>
							<?php echo $s->item_code . "<small style='display:block;' class='text-danger'>".$s->description."</small>"?>
						</td>
						<td data-title='Original Price'  style='border-top:1px solid #ccc;'>
							<?php echo escape(number_format($price,2)); ?><br>
						</td>
						<td data-title='Adjustment'  style='border-top:1px solid #ccc;'>
							<?php echo escape(number_format($adjustment,2)); ?><br>
						</td>
						<td data-title='Adjusted Price'  style='border-top:1px solid #ccc;'>
							<?php echo escape(number_format($adjustment + $price,2)); ?><br>
						</td>
						<td  style='border-top:1px solid #ccc;'>

						</td>
					</tr>
					<?php
				}
			} else {
				?>
				<tr>
					<td colspan='6'><h3><span class='label label-info'>No Record Found...</span></h3>
					</td>
				</tr>
				<?php
			}
		?>
		</tbody>
	</table>
	<?php
		$time_end = microtime(true);
		$execution_time = number_format($time_end - $time_start,4);
		Log::addLog(
			$user->data()->id,
			$user->data()->company_id,
			"DOWNLOAD PRICELIST - " . $execution_time,
			'excel_downloader.php'
		);
	}

		function pendingItems(){
			$time_start = microtime(true);


			$filename = "pendingitems-" . date('m-d-Y-H-i-s-A') . ".xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");
			$s = Input::get('s');
			$branch_id = Input::get('branch_id');
			$wh_order = new Wh_order();
			$user = new User();

			?>
		<table class='table' border='1' id='tblForApprovalReserved'>
				<thead>
				<tr>

					<TH>Order ID</TH>
					<TH>Invoice</TH>
					<TH>DR</TH>
					<TH>PR</TH>
					<TH>Created</TH>
					<TH>Item Code</TH>
					<TH>Pending Qty</TH>

				</tr>
				</thead>
				<tbody>
				<?php
					//$targetpage = "paging.php";

					$limit = 2000;
					$countRecord = $wh_order->countRecordPending($user->data()->company_id,$s,$branch_id);

					$total_pages = $countRecord->cnt;

					$stages = 3;
					$page = 0;
					$page = (int)$page;
					if($page) {
						$start = ($page - 1) * $limit;
					} else {
						$start = 0;
					}

					$company_op = $wh_order->get_record_pending($user->data()->company_id, $start, $limit,$s,$branch_id);


					if($company_op) {

						foreach($company_op as $o) {
							if(!$o->wh_orders_id) continue;
							?>
							<tr>
								<td><?php echo (escape($o->wh_orders_id)) ?> </td>
								<td><?php echo (escape($o->invoice)) ?> </td>
								<td><?php echo (escape($o->dr)) ?> </td>
								<td><?php echo (escape($o->pr)) ?> </td>
								<td><?php echo (date('m/d/Y',$o->created)) ?> </td>
								<td data-title='Item Code'>
									<?php echo capitalize(escape($o->item_code)) ?>
									<span class='text-danger span-block'><?php echo $o->description; ?></span>
								</td>
								<td data-title='Qty'><?php echo formatQuantity((escape($o->qty))) ?> </td>


							</tr>
							<?php
						}
					} else {
						?>
						<tr>
							<td colspan='3' class='text-left'><h3>
									<span class='label label-info'>No Record Found...</span></h3></td>
						</tr>
						<?php
					}
				?>
	</tbody>
	</table>
	<?php
			$time_end = microtime(true);
			$execution_time = number_format($time_end - $time_start,4);
			Log::addLog(
				$user->data()->id,
				$user->data()->company_id,
				"DOWNLOAD PENDING ITEMS - " . $execution_time,
				'excel_downloader.php'
			);
		}

		function city(){
			$filename = "cities-" . date('m-d-Y-H-i-s-A') . ".xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");

			$city = new City_mun();


			$city = new City_mun();
			?>
			<div id="no-more-tables">
				<table class='table' border=1 id='tblSales'>
					<thead>
					<tr>

						<TH>Region</TH>
						<TH>Name</TH>
						<TH>Delivery Charge Cash</TH>
						<TH>Delivery Charge BT</TH>
						<th></th>

					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";

						$limit = 20000;
						$countRecord = $city->countRecord('','');

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = (0);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_op = $city->get_record($start, $limit, '','');
						getpagenavigation($page, $total_pages, $limit, $stages);

						if($company_op) {

							foreach($company_op as $o) {

								?>
								<tr id='row<?php echo $o->id?>'>
									<td style='border-top:1px solid #ccc;' data-title='Region'><?php echo capitalize(escape($o->provDesc)) ?></td>
									<td style='border-top:1px solid #ccc;' data-title='Name'><?php echo capitalize(escape($o->citymunDesc)) ?></td>
									<td style='border-top:1px solid #ccc;' data-title='Charge Cash '><?php echo number_format($o->del_charge_cash,2) ?></td>
									<td style='border-top:1px solid #ccc;' data-title='Charge BT'><?php echo number_format($o->del_charge_bt,2) ?></td>
									<td style='border-top:1px solid #ccc;' data-title='Action'>


									</td>

								</tr>
								<?php
							}
						} else {
							?>
							<tr>
								<td colspan='4' class='text-left'><h3>
										<span class='label label-info'>No Record Found...</span></h3></td>
							</tr>
							<?php
						}
					?>
					</tbody>
				</table>
			</div>
			<?php


		}
		function getTimeCheque($time) {
			if($time > (60 * 60) * 24) {
				// if ($time < (((60 *60) * 24) * 30))
				$del = number_format((($time / 60) / 60) / 24, 2);
				$res = (floor($del) > 1) ? "days left" : "day left";

				return floor($del) . " $res";
			}

			return false;
		}
		function terminalMonitoring() {
			$time_start = microtime(true);

			// pages, //aqua
			$filename = "terminal-" . date('m-d-Y-H-i-s-A') . ".xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");
			$user = new User();
			$p_type = Input::get('type');
			$terminal_mon = new Terminal_mon();
			$terminal_id = Input::get('terminal_id');
			$dt1 = Input::get('dt1');
			$dt2 = Input::get('dt2');
			$args = 0;
			?>
			<div id="no-more-tables">
				<div class="table-responsive">
					<table class='table' id='tblSales' border=1>
						<thead>
						<tr>
							<TH>Terminal</TH>
							<TH>User</TH>
							<TH>Created</TH>
							<TH class='text-right'>From</TH>
							<th></th>
							<TH class='text-right'>Amount</TH>
							<th></th>
							<TH class='text-right'>To</TH>
							<TH>Type</TH>
							<TH>Remarks</TH>
						</tr>
						</thead>
						<tbody>
						<?php
							//$targetpage = "paging.php";
							$limit = 10000;
							$countRecord = $terminal_mon->countRecord($user->data()->company_id, $p_type, $terminal_id,$dt1,$dt2);

							$total_pages = $countRecord->cnt;

							$stages = 3;
							$page = ($args);
							$page = (int)$page;
							if($page) {
								$start = ($page - 1) * $limit;
							} else {
								$start = 0;
							}

							$company_req = $terminal_mon->get_record($user->data()->company_id, $start, $limit, $p_type, $terminal_id,$dt1,$dt2);
							getpagenavigation($page, $total_pages, $limit, $stages);

							if($company_req) {
								$p_type = array('', 'Cash', 'Credit Card', 'Cheque', 'Bank transfer');
								foreach($company_req as $s) {
									$p_typelabel = $p_type[$s->p_type];
									$stats = $s->status;
									?>
									<tr>
										<td data-title="Terminal"><?php echo escape(ucwords($s->tname)) ?></td>
										<td data-title="User"><?php echo escape(ucwords($s->lastname . ", " . $s->firstname . " " . $s->middlename)); ?></td>
										<td data-title="Created"><?php echo escape(date('m/d/Y H:i:s A', $s->created)); ?></td>
										<td data-title="From" class='text-right'><?php echo escape(number_format($s->from_amount, 2)); ?></td>
										<td class='text-right'>
											<?php
												if($stats == 1) {
													?>
													<span class='glyphicon glyphicon-plus'></span>
													<?php
												} else if($stats == 2) {
													?>
													<span class='glyphicon glyphicon-minus'></span>
													<?php
												}
											?>
										</td>
										<td data-title="Amount" class='text-right'><?php echo escape(number_format($s->amount, 2)); ?></td>
										<td class='text-right'><span class='glyphicon glyphicon-arrow-right'></span></td>
										<td data-title="To" class='text-right'>
											<strong> <?php echo escape(number_format($s->to_amount, 2)); ?></strong></td>
										<td data-title="Type"><?php echo escape($p_typelabel); ?></td>
										<td data-title="Remarks">
											<?php
												if(strpos($s->remarks, 'Inv:') > 0 || strpos($s->remarks, 'Dr:') > 0) {
													$s->remarks = "<span class='text-danger' style='font-size:1.5em;'>*</span> " . $s->remarks;
												}
												echo($s->remarks);
											?>
										</td>
									</tr>
									<?php
								}
							} else {
								?>
								<tr>
									<td colspan='7'><h3><span class='label label-info'>No Record Found...</span></h3></td>
								</tr>
								<?php
							}
						?>
						</tbody>
					</table>
				</div>
			</div>
			<?php
		}


		function r3Pagination() {
			$filename = "sales-" . date('m-d-Y-H-i-s-A') . ".xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");

			$payment_method = Input::get('payment_method');
			$branch = Input::get('branch');
			$terminal = Input::get('terminal');
			$item_type = Input::get('item_type');
			$category = Input::get('category');
			$memid = Input::get('member');
			$stationid = Input::get('station');
			$dateStart = Input::get('dateStart');
			$dateEnd = Input::get('dateEnd');
			$cashier = Input::get('cashier');
			$rdstrict = Input::get('rdStrict');
			$char = Input::get('char');
			$item_id = Input::get('item_id');
			$sales_type = Input::get('sales_type');
			$from_od = Input::get('from_od');
			$with_serial = Input::get('with_serial');
			$from_service = Input::get('from_service');

			$region = Input::get('region');
			$rdRegion = Input::get('rdRegion');
			$report_type = Input::get('report_type');
			$sort_by = Input::get('sort_by');


			$filterinfo = "";
			if(!($dateStart && $dateEnd)){
				echo "<div class='alert alert-danger'>Please choose dates first.</div>";
				exit();
			}
			if($report_type == 1) {
				$sales = new Sales();
				if(!is_array($sales_type)){
					$sales_type = [$sales_type];
				}
				$user = new User();
				$cid = $user->data()->company_id;
				$list = $sales->getSalesR3v3($cid,$dateStart,$dateEnd,$sales_type);
				if($list){
//<th class='text-right'>Cash</th><th class='text-right'>Cheque</th><th class='text-right'>Credit</th><th  class='text-right'>Bank Transfer</th><th class='text-right'>Deduction</th><th class='text-right'>Unpaid</th>
					echo "<table class='table' id='tblForApproval' border=1>";
					echo "<thead>";
					echo "<tr><th>Member</th><th>Date</th><th>".INVOICE_LABEL."</th><th>".DR_LABEL."</th><th>".PR_LABEL."</th><th class='text-right' >Total</th></tr>";
					echo "</thead>";
					echo "<tbody>";
					$total_cash = 0;
					$total_cheque = 0;
					$total_credit = 0;
					$total_bt = 0;
					$total_deduction = 0;
					$total_member = 0;
					$total_sales = 0;
					foreach($list as $l){
						$inv = ($l->invoice) ? $l->invoice : 'N/A';
						$dr = ($l->dr) ? $l->dr : 'N/A';
						$ir = ($l->ir) ? $l->ir : 'N/A';

						$cash = ($l->cash_amount) ? $l->cash_amount : 0;
						$credit = ($l->credit_card_amount) ? $l->credit_card_amount : 0;
						$bt = ($l->bt_amount) ? $l->bt_amount : 0;
						$cheque = ($l->cheque_amount) ? $l->cheque_amount : 0;
						$deduction = ($l->deduction_amount) ? $l->deduction_amount : 0;
						$member_credit = ($l->member_amount) ? $l->member_amount : 0;

						$total_cash += $cash;
						$total_cheque  += $cheque;
						$total_credit  += $credit;
						$total_bt  += $bt;
						$total_deduction  += $deduction;
						$total_member  += $member_credit;
						$total_sales += $l->totalamount;
						$total_amount = $cash + $credit + $bt + $cheque + $deduction + $member_credit;
						$cls ='';
						$lbl = '';
						if(number_format($total_amount,2) == number_format($l->totalamount,2)){

						} else {
							$cls ='bg-danger showdanger';
							$lbl ='<br>unmatched';
						}

						echo "<tr class='$cls'><td>$l->mln<br>$l->sales_type_name $lbl</td><td>".date('m/d/Y',$l->sold_date)."</td><td>$inv</td><td>$dr</td><td>$ir</td><td class='text-right'> " . number_format($l->totalamount,2)."</td>";
						//echo "<td class='text-right'>".number_format($cash,2)."</td>";
						//echo "<td class='text-right'>".number_format($cheque,2)."</td>";
						//echo "<td class='text-right'>".number_format($credit,2)."</td>";
						////echo "<td class='text-right'>".number_format($bt,2)."</td>";
					//	echo "<td class='text-right'>".number_format($deduction,2)."</td>";
						//echo "<td class='text-right'>".number_format($member_credit,2)."</td>";
						echo "</tr>";
					}

					echo "</tbody>";
					echo "<tr><th style='border-top: 1px solid #ccc;'></th><th style='border-top: 1px solid #ccc;'></th><th style='border-top: 1px solid #ccc;'></th><th style='border-top: 1px solid #ccc;'></th><th style='border-top: 1px solid #ccc;'></th>";
					//echo "<th class='text-right' style='border-top: 1px solid #ccc;'>".number_format($total_sales,2)."</th>";
					//echo "<th class='text-right'  style='border-top: 1px solid #ccc;'>".number_format($total_cash,2)."</th>";
					//echo "<th class='text-right'  style='border-top: 1px solid #ccc;'>".number_format($total_cheque,2)."</th>";
					//echo "<th class='text-right'   style='border-top: 1px solid #ccc;'>".number_format($total_credit,2)."</th>";
					//echo "<th class='text-right'  style='border-top: 1px solid #ccc;'>".number_format($total_bt,2)."</th>";
				//	echo "<th class='text-right'  style='border-top: 1px solid #ccc;'>".number_format($total_deduction,2)."</th>";
				//	echo "<th class='text-right'  style='border-top: 1px solid #ccc;'>".number_format($total_member,2)."</th>";
					echo "</tr>";
					/*
					 echo "<tr><th style='border-top: 1px solid #ccc;'></th><th style='border-top: 1px solid #ccc;'></th><th style='border-top: 1px solid #ccc;'></th><th style='border-top: 1px solid #ccc;'></th><th style='border-top: 1px solid #ccc;'></th>";
					echo "<th class='text-right' style='border-top: 1px solid #ccc;display:none;'>".number_format($total_cash + $total_cheque + $total_credit + $total_bt + $total_deduction + $total_member ,2)."</th>";
					echo "<th class='text-right'  style='border-top: 1px solid #ccc;'></th>";
					echo "<th class='text-right'  style='border-top: 1px solid #ccc;'></th>";
					echo "<th class='text-right'   style='border-top: 1px solid #ccc;'></th>";
					echo "<th class='text-right'  style='border-top: 1px solid #ccc;'></th>";
					echo "<th class='text-right'  style='border-top: 1px solid #ccc;'></th>";
					echo "<th class='text-right'  style='border-top: 1px solid #ccc;'></th>";
					echo "<th class='text-right'  style='border-top: 1px solid #ccc;'></th>";
					echo "</tr>*/
					echo "</table>";
				} else {
					echo "No record";
				}
			} else if($report_type == 2) {

			}
		}


	function audit_log(){
		// pages,
		$filename = "audit-log-" . date('m-d-Y-H-i-s-A') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");

		$inv = new Inventory_ammend();
		$search = Input::get('search');
		$b = Input::get('b');
		$r = Input::get('r');
		$date_from = Input::get('date_from');
		$date_to = Input::get('date_to');
		$user = new User();
		$cid = $user->data()->company_id;

		Log::addLog(
			$user->data()->id,
			$user->data()->company_id,
			"DOWNLOAD AUDIT LOG",
			'excel_downloader.php'
		);
	?>


	<div id="no-more-tables">
		<div class="table-responsive">
			<table class='table' id='tblForApproval' border="1">
				<thead>
				<tr>
					<TH>Branch</TH>
					<TH>Rack</TH>
					<TH>Item</TH>
					<TH>Description</TH>
					<TH>Category</TH>
					<TH>Qty</TH>
					<TH>Amend Qty</TH>
					<TH>Created At</TH>
					<th>User</th>
					<th>Remarks</th>
				</tr>
				</thead>
				<tbody>
				<?php
					//$targetpage = "paging.php";
					$limit = 5000;
					$countRecord = $inv->countRecord($cid, $search, $b, $r,$date_from,$date_to);

					$total_pages = $countRecord->cnt;

					$stages = 3;
					$page = (0);
					$page = (int)$page;
					if($page) {
						$start = ($page - 1) * $limit;
					} else {
						$start = 0;
					}

					$company_inv = $inv->get_record($cid, $start, $limit, $search, $b, $r,$date_from,$date_to);
					getpagenavigation($page, $total_pages, $limit, $stages);
					if($company_inv) {
						$invProduct = new Product();


						foreach($company_inv as $s) {
							$price = $invProduct->getPrice($s->item_id);

							$inv_mon = new Inventory_monitoring();
							$dtto = $s->created;
							$dtfrom = strtotime(date('m/d/Y', $dtto) . "-7 days");
							$isFoundItem = $inv_mon->isFoundItem($s->item_id,$s->rack_id,$s->branch_id,$dtfrom,$dtto,$s->qty);

							$foundItem = false;
							if(isset($isFoundItem->cnt) && $isFoundItem->cnt){
								$foundItem = true;
							}

							$alldis ='';
							if($s->rack == 'Display'){
								if($s->display_location){
									if(strpos($s->display_location,',') > 0){
										$explodeddis = explode(',',$s->display_location);
										foreach($explodeddis as $ed){
											$displayLocation = new Display_location($ed);
											$alldis .= " <span style='font-size:0.8em;' class='label label-primary'>".$displayLocation->data()->name . "</span> ";
										}

									} else {
										$displayLocation = new Display_location($s->display_location);
										$alldis = $displayLocation->data()->name;
									}
									$alldis = "<br>".$alldis;
								}

							}
							?>
							<tr>
								<td data-title="Branch"><?php echo escape($s->name) ?></td>
								<td data-title="Rack" class='text-danger'><?php echo "<strong>" . escape($s->rack)."</strong>".$alldis; ?></td>

								<td data-title="Item code"><?php echo escape($s->item_code) ?></td>
								<td><?php echo escape($s->description) ?></td>
								<td><?php echo escape($s->category_name) ?></td>



								<td data-title="Qty" class='text-right' style='padding-right:20px;'>
									<strong>
										<?php
											echo formatQuantity($s->qty);
										?>
									</strong>
								</td>
								<td data-title="Amend Qty" class='text-right' style='padding-right:20px;'>
									<strong>
										<?php
											echo formatQuantity($s->ammend_qty);
										?>
									</strong>
								</td>
								<td>
									<?php echo date('m/d/Y H:i:s A',$s->created); ?>
								</td>
								<td>
									<?php echo ucwords($s->firstname . " " . $s->lastname); ?>



								</td>
								<td>

									<?php if($foundItem ){
										?>
										<small class='text-danger span-block'><?php echo ($foundItem) ? "Add found item" : ""; ?></small>
										<?php
									} else {
										?>
										<small class='text-danger span-block'><?php echo $s->remarks; ?></small>
										<?php
									}?>
								</td>
							</tr>
							<?php
						}
					} else {
						?>
						<tr>
							<td colspan='8'><h3><span class='label label-info'>No Record Found...</span></h3></td>
						</tr>
						<?php
					}
				?>
				</tbody>
			</table>
		</div>
	</div>
	<?php
		}


		function issues_log(){
			$time_start = microtime(true);

			$filename = "audit-log-" . date('m-d-Y-H-i-s-A') . ".xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");

			$user = new User();
			$inv = new Inventory_issues_monitoring();
			$search = Input::get('search');
			$b = Input::get('b');
			$r = Input::get('r');
			$type = Input::get('type');
			$arrType = ['',DAMAGE_LABEL,MISSING_LABEL,'Disposed',INCOMPLETE_LABEL];

			$cid = $user->data()->company_id;


			?>
			<div id="no-more-tables">
				<div class="table-responsive" border="1">
					<table class='table' border="1" id='tblSales'>
						<thead>
						<tr>
							<TH>Branch</TH>
							<th>User</th>
							<th>Date</th>
							<TH>Rack</TH>
							<TH>Item</TH>
							<th>Type</th>
							<th>Price</th>
							<th>Prev</th>
							<th>Action</th>
							<th>Qty</th>
							<th>New Qty</th>
							<th>Remarks</th>

						</tr>
						</thead>
						<tbody>
						<?php
							//$targetpage = "paging.php";
							$limit = 10000;
							$countRecord = $inv->countRecord($cid, $search, $b, $r,$type);

							$total_pages = $countRecord->cnt;

							$stages = 3;
							$page = 0;
							$page = (int)$page;
							if($page) {
								$start = ($page - 1) * $limit;
							} else {
								$start = 0;
							}

							$company_inv = $inv->get_sales_record($cid, $start, $limit, $search, $b, $r,$type);
							getpagenavigation($page, $total_pages, $limit, $stages);
							if($company_inv) {
								$invProduct = new Product();
								foreach($company_inv as $s) {

									$price = $invProduct->getPrice($s->item_id);
									if($s->qty_di == 1) {
										$actionicon = "<span class='glyphicon glyphicon-plus'></span>";
									} else if($s->qty_di == 2) {
										$actionicon = "<span class='glyphicon glyphicon-minus'></span>";
									} else if($s->qty_di == 3) {
										$actionicon = "<span class='glyphicon glyphicon-left'></span>";
									}


									?>
									<tr>
										<td data-title="Branch"><?php echo escape($s->name) ?></td>
										<td data-title="User"><?php echo escape(ucwords($s->firstname . " " . $s->lastname)); ?></td>
										<td data-title="Created"><?php echo escape(date('m/d/Y H:i:s A', $s->created)); ?></td>
										<td data-title="Rack" class='text-danger'><?php echo escape($s->rack) ?></td>
										<td data-title="Item "><?php echo escape($s->item_code) . "<br>" . "<small>" . escape($s->description) . "</small>" ?></td>
										<td data-title="Type"><?php echo escape($arrType[$s->type]); ?></td>
										<td data-title="Price"><?php echo escape(number_format($price->price, 2)) ?></td>
										<td data-title="Prev Quantity">
											<?php echo formatQuantity($s->prev_qty) ?>
										</td>
										<td data-title="Action">
											<?php echo $actionicon; ?>
										</td>
										<td data-title="Qty">
											<?php echo formatQuantity($s->qty) ?>
										</td>
										<td data-title="New Qty">
											<?php echo formatQuantity($s->new_qty) ?>
										</td>
										<td><small><span class='text-danger'><?php echo escape(($s->remarks)) ?></span></small></td>
									</tr>
									<?php
								}
							} else {
								?>
								<tr>
									<td colspan='9'><h3><span class='label label-info'>No Record Found...</span></h3></td>
								</tr>
								<?php
							}
						?>
						</tbody>
					</table>
				</div>
			</div>
			<?php
			$time_end = microtime(true);
			$execution_time = number_format($time_end - $time_start,4);
			Log::addLog(
				$user->data()->id,
				$user->data()->company_id,
				"DOWNLOAD ISSUES LOG - " . $execution_time,
				'excel_downloader.php'
			);
		}

		function issues(){

		}

		function techLog(){
			$time_start = microtime(true);

			$filename = "techinician-log-" . date('m-d-Y-H-i-s-A') . ".xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");

			$user = new User();
			$service_req = new Item_service_request();
			$technician_id = Input::get('technician_id');
			$status = Input::get('status');
			$cid = $user->data()->company_id;

			$limit = 10000;


			$args = 0;
			$stages = 3;
			$page = ($args);
			$page = (int)$page;
			if($page) {
				$start = ($page - 1) * $limit;
			} else {
				$start = 0;
			}

			$company_sales = $service_req->get_technician_record($cid, $start, $limit,$technician_id,$status);
			$item_use = new Service_item_use();
			$item_request = new Item_service_request();

			$item_used = $item_use->getUsedItemsAll($status,$technician_id);
			$item_requested = $item_request->getRequestWithDetails($status,$technician_id);

			$item_used_arr = [];
			$item_request_arr = [];
			foreach($item_used as $item){
				$item_used_arr[$item->service_id][] = $item;
			}

			foreach($item_requested as $item2){
				$item_request_arr[$item2->service_id][] = $item2;
			}

			echo "<table border=1>";
			?>
			<thead>
			<tr>
				<th>Id</th>
				<th>Technician</th>
				<th>Branch</th>
				<th>User</th>
				<th>Member</th>
				<th>Date Created</th>
				<th>Status</th>

			</tr>
			</thead>
			<?php
			$primaryStatus = ['','Pending','For Evaluation','For Payment/Credit','Processed'];
			foreach($company_sales as $item){

				$lblstats = "";
				$lblPrimaryStatus="";
				if(isset($primaryStatus[$item->status])){
					$lblPrimaryStatus =  $primaryStatus[$item->status] ;
				} else {
					$lblPrimaryStatus =  Cancelled;
				}

				if($item->member_id){
					$mem =   escape($item->mln);
				} else {
					$mem = 'N/A';
				}
				if($item->pullout_schedule){
					$pullout = date('m/d/Y',$item->pullout_schedule);
				} else {
					$pullout = "<i class='fa fa-ban'></i>";
				}
				if($item->home_repair){
					$home_repair = date('m/d/Y',$item->home_repair);
				} else {
					$home_repair = "<i class='fa fa-ban'></i>";
				}
				?>
				<tr>
					<td data-title="Id">
						<?php echo escape($item->id); ?>
					</td>
					<td data-title="Technician">
						<?php echo escape($item->technician_name); ?>
					</td>
					<td data-title="Branch">
						<?php echo escape($item->branch_name); ?>
					</td>
					<td data-title="User">
						<?php echo escape($item->firstname . " " . $item->lastname); ?>
					</td>
					<td data-title="Member">
						<?php echo $mem; ?>
					</td>
					<td data-title="Created"><?php echo date('m/d/Y',$item->created); ?></td>
					<td data-title="Status" class='text-danger'><?php echo $lblPrimaryStatus; ?></td>
				</tr>
				<?php
				$rem_list = new Remarks_list();
				$ref_table= 'service';
				$remarks_list  = $rem_list->getServices($item->id,$ref_table,$user->data()->company_id);
				if($remarks_list){
					foreach($remarks_list as $rl){
						echo "<tr>";
						echo "<td></td>";
						echo "<td></td>";
						echo "<td></td>";
						echo "<td style='color:red;'>Remarks</td>";
						echo "<td>".ucwords($rl->firstname . " " . $rl->lastname)."</td>";
						echo "<td>".date('m/d/Y',$rl->created)."</td>";
						echo "<td>".$rl->remarks."</td>";
						echo "</tr>";
					}
				}
				if($item_request_arr[$item->id]){
					$toIterate = $item_request_arr[$item->id];
					foreach($toIterate as $used){
						if($used->item_id){
							echo "<tr>";
							echo "<td></td>";
							echo "<td></td>";
							echo "<td></td>";

							echo "<td style='color:red;'>Item Requested</td>";
							echo "<td></td>";
							echo "<td>$used->description</td>";
							echo "<td>$used->qty</td>";

							echo "</tr>";
						}

					}
				}


				if($item_used_arr[$item->id]){
					$toIterate = $item_used_arr[$item->id];
					foreach($toIterate as $used){
						echo "<tr>";
						echo "<td></td>";
						echo "<td></td>";
						echo "<td></td>";

						echo "<td style='color:red;'>Item Used</td>";
						echo "<td></td>";
						echo "<td>$used->description</td>";
						echo "<td>$used->qty</td>";

						echo "</tr>";
					}

				}
			}
			echo "</table>";

			$time_end = microtime(true);
			$execution_time = number_format($time_end - $time_start,4);
			Log::addLog(
				$user->data()->id,
				$user->data()->company_id,
				"DOWNLOAD TECHNICIAN LOG - ".$execution_time,
				'excel_downloader.php'
			);

		}

?>


