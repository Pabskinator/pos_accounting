<?php
	include_once '../core/admininit.php';
	include_once 'includes/labels.php';

	$fn = Input::get('downloadName');
	$fn();

	function consumableMon(){
		$time_start = microtime(true);

		$user = new User();
		$filename = "consumable-payment-" . date('m-d-Y-H-i-s-A') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");

		$search = Input::get('search');
		$dt1 = Input::get('dt1');
		$dt2 = Input::get('dt2');
		$limit = 5000;
		$cash = new Payment_consumable();
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

						if(!$o->sold_date) continue;
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
			"DOWNLOAD CONSUMABLE PAYMENT - " . $execution_time,
			'excel_downloader.php'
		);

	}
	function serials(){

		$user = new User();
		$filename = "serials-" . date('m-d-Y-H-i-s-A') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");

		// pages,

		$serial = new Serial();
		$search = Input::get('search');
		$branch_id = Input::get('branch_id');
		$dateEnd = Input::get('dateEnd');
		$dateStart = Input::get('dateStart');
		$member_id = Input::get('member_id');
		$item_id = Input::get('item_id');

		$assembly_only = ($user->hasPermission('serial_assembly')) ? 1 : 0;

		$cid = $user->data()->company_id;
		?>


				<table border='1' class='table' id='tblForApproval'>
					<thead>
					<tr>
						<TH>Client</TH>
						<TH><?php echo INVOICE_LABEL; ?></TH>
						<TH><?php echo DR_LABEL; ?></TH>
						<TH><?php echo PR_LABEL; ?></TH>
						<TH>Item</TH>
						<TH>Description</TH>
						<TH>Serial No</TH>
						<th>Sold Date</th>

					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 5000;

						$page = (0);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$serials = $serial->get_record($cid, $start, $limit, $search,$branch_id,$dateStart,$dateEnd,$member_id,$item_id,$assembly_only);


						if($serials) {

							foreach($serials as $s) {
								$lblInvoice = "";
								$lblDr = "";
								$lblIr= "";
								if($s->invoice){
									$lblInvoice = "<span class='label label-danger'>".INVOICE_LABEL." $s->invoice</span>";
								}
								if($s->dr){
									$lblDr = "<span class='label label-warning'>".DR_LABEL." $s->dr</span>";
								}
								if($s->ir){
									$lblIr = "<span class='label label-primary'>".PR_LABEL." $s->ir</span>";
								}
								?>
								<tr>
									<td data-title='Member' >
											<?php echo ($s->member_name) ? $s->member_name : 'None'; ?>
									</td>
									<td  ><?php echo $s->invoice; ?> </td>
									<td  ><?php echo $s->dr; ?> </td>
									<td  ><?php echo $s->ir; ?> </td>

									<td data-title='Item'><?php echo $s->item_code; ?></td>
									<td data-title='Item'><?php echo $s->description; ?></td>
									<td data-title='Serial #'><?php echo $s->serial_no; ?></td>
									<td  ><?php echo date('m/d/Y h:s:i A',$s->sold_date); ?></td>

								</tr>
								<?php
							}
						} else {
							?>
							<tr>
								<td colspan='2'><h3><span class='label label-info'>No Record Found...</span></h3></td>
							</tr>
							<?php
						}
					?>
					</tbody>
				</table>
		<?php
	}

	function branchGroups(){

		$filename = "branch-groups-" . date('m-d-Y-H-i-s-A') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");

		$branch_group_id = Input::get('branch_group_id');
		$search = Input::get('search');

		$branch_group = new Branch_group_pricelist();

		?>

		<?php
		//$targetpage = "paging.php";

		$limit = 5000;
		$countRecord = $branch_group->countRecord($branch_group_id,$search);

		$total_pages = $countRecord->cnt;
		$args = 0;
		$stages = 3;
		$page = ($args);
		$page = (int)$page;
		if($page) {
			$start = ($page - 1) * $limit;
		} else {
			$start = 0;
		}

		$company_op = $branch_group->get_record($branch_group_id, $start, $limit,$search);
		getpagenavigation($page, $total_pages, $limit, $stages);

		if($company_op) { ?>
				<p>Note: Maximum of 5000 records only. Use filter to divide your data.</p>
				<table border=1 class='table' id='tblSales'>
					<thead>
					<tr>
						<TH>Group</TH>
						<TH>Item</TH>
						<TH>Barcode</TH>
						<th>Price</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<?php
						foreach($company_op as $o) {
							?>
							<tr>
								<td style='border-top:1px solid #ccc;'><?php echo $o->group_name; ?></td>
								<td style='border-top:1px solid #ccc;'><?php echo $o->item_code; ?></td>
								<td style='border-top:1px solid #ccc;'><?php echo $o->barcode; ?></td>
								<td style='border-top:1px solid #ccc;'><?php echo $o->price; ?></td>
								<td style='border-top:1px solid #ccc;'></td>
							</tr>
							<?php
						}

					?>
					</tbody>
				</table>

		<?php } else {
			?>

				No record found.

			<?php
		}

	}


	function byCategoryDiscount(){
		$time_start = microtime(true);


		$filename = "category-discount-" . date('m-d-Y-H-i-s-A') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");

		$search = Input::get('search');


		?>


		<?php


			$limit = 10000;

			$member_category = new Member_category_discount();

			$countRecord = $member_category->countRecord($search);

			$total_pages = $countRecord->cnt;

			$stages = 4;
			$args = 0;
			$page = ($args);
			$page = (int)$page;
			if($page) {
				$start = ($page - 1) * $limit;
			} else {
				$start = 0;
			}

			$company_items = $member_category->get_record($start, $limit, $search);
			$member_category->getPageNavigation($page, $total_pages, $limit, $stages);

			if($company_items) {
				?>
				<table border='1'>
					<thead>
					<tr>
						<TH>Member</TH>
						<TH>Category</TH>
						<TH>Discount 1</TH>
						<TH>Discount 2</TH>
						<TH>Discount 3</TH>
						<TH>Discount 4</TH>
					</tr>
					</thead>
					<tbody>
					<?php

						foreach($company_items as $s) {
							?>
							<tr >
								<td><?php echo $s->member_name; ?></td>
								<td><?php echo $s->category_name; ?></td>
								<td><?php echo formatQuantity($s->discount_1); ?></td>
								<td><?php echo formatQuantity($s->discount_2); ?></td>
								<td><?php echo formatQuantity($s->discount_3); ?></td>
								<td><?php echo formatQuantity($s->discount_4); ?></td>
							</tr>
							<?php
						}
					?>
					</tbody>
				</table>
				<?php
			} else {
				?>
				<div class="alert alert-info">No record found</div>
				<?php
			}

		$time_end = microtime(true);
		$execution_time = number_format($time_end - $time_start,4);
		$user = new User();
		Log::addLog(
			$user->data()->id,
			$user->data()->company_id,
			"DOWNLOAD CATEGORY DISCOUNT - ".$execution_time,
			'excel_downloader.php'
		);
	}

	function criticalOrderCustom(){
		$time_start = microtime(true);

			$filename = "category-discount-" . date('m-d-Y-H-i-s-A') . ".xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");


			$inv = new Inventory_monitoring();
			$user = new User();

			$dt1 = strtotime(Input::get('dt1'));
			$dt2 = strtotime(Input::get('dt2') . "1 day -1 min");

			$branch_id = Input::get('branch_id');


			$result = $inv->criticalOrder($branch_id,$dt1,$dt2);
			$items = [];
			$item_info = [];
			foreach($result as $res){
				$item_info[$res->item_code] = $res->description;
				$items[$res->item_code][$res->m] = $res->total_qty;
			}

		echo "<table class='table table-bordered' border=1>";
		echo "<thead><tr><th>Item</th><th>Total Sold</th><th>Months Sold</th><th>Monthly Avg</th><th>Forecast<br><small>For the next 6 months</small></th></tr></thead>";
		foreach($items as $item_id => $item){

			$total = array_sum($item);
			$cnt = count($item);
			$avg = 0;
			if($total){
				$avg= floor($total / $cnt);
			}
			echo "
				<tr>
					<td style='border-top:1px solid #ccc;'>
					$item_info[$item_id]
						<span style='display: none;'></span>

					</td>
					<td  style='border-top:1px solid #ccc;'>$total</td>
					<td  style='border-top:1px solid #ccc;'>$cnt</td>
					<td  style='border-top:1px solid #ccc;'>$avg</td>
					<td  style='border-top:1px solid #ccc;'>" .($avg * $cnt). "</td>
				</tr>";
		}
		echo "</table>";
		$time_end = microtime(true);
		$execution_time = number_format($time_end - $time_start,4);
		Log::addLog(
			$user->data()->id,
			$user->data()->company_id,
			"DOWNLOAD CRITICAL ORDER CUSTOM - ".$execution_time,
			'excel_downloader.php'
		);
	}
	function serviceUsed(){
		$time_start = microtime(true);

		$filename = "service-requested-" . date('m-d-Y-H-i-s-A') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");

		$user = new User();



		// pages,
		$user = new User();
		$used_cls = new Service_item_use();
		$cid = $user->data()->company_id;
		$s = Input::get('s');

		//$targetpage = "paging.php";

		$limit = 5000;
		$countRecord = $used_cls->countRecord($cid,$s);
		$total_pages = isset($countRecord->cnt) ? $countRecord->cnt : 0;
		$stages = 3;
		$page = (0);
		$page = (int)$page;
		if($page) {
			$start = ($page - 1) * $limit;
		} else {
			$start = 0;
		}
		$company_inv = $used_cls->get_record($cid, $start, $limit,$s);
		getpagenavigation($page, $total_pages, $limit, $stages);
		?>
		<div id="no-more-tables">
			<div class="table-responsive">
				<table class='table' border=1 id='tblSales'>
					<thead>
					<tr>
						<th>Service Id</th>
						<th><?php echo MEMBER_LABEL; ?></th>
						<th>Technician</th>
						<th>Item</th>
						<th>Qty</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<?php
						if($company_inv) {
							$tech = new Technician();
							$tech_list = $tech->get_active('technicians',[1,'=',1]);
							$arr_tech = [];

							if($tech_list){
								foreach($tech_list as $te){
									$arr_tech[$te->id] = $te->name;
								}
							}

							$prev_val  ="";

							foreach($company_inv as $det) {
								$service_id = "";
								$member_name= "";
								$border_top = "";
								$technician_names= "";


								$tech_ids = $det->technician_id;
								$tech_names = "None";
								if($tech_ids){
									$tech_ids = explode(',',$tech_ids);
									$tech_names ='';
									foreach($tech_ids as $tids){
										if(isset($arr_tech[$tids])){
											$tech_names .= "<p>".$arr_tech[$tids]."</p>";
										}
									}
								}

								if($prev_val != $det->service_id){

									$service_id = $det->service_id;
									$member_name = $det->member_name;
									$technician_names = $tech_names;

								}

								$prev_val = $det->service_id;

								?>
								<tr>
									<td><?php echo escape($service_id);  ?></td>
									<td><?php echo escape(capitalize($member_name));  ?></td>
									<td><?php echo $technician_names;  ?></td>
									<td><?php echo escape($det->item_code) . "<small class='text-danger span-block'>". escape($det->description)."</small>";  ?></td>
									<td><?php echo escape(formatQuantity($det->qty));  ?></td>
									<td></td>
								</tr>
								<?php
							}
						} else {
							?>
							<tr>
								<td colspan='3'><h3><span class='label label-info'>No Record Found...</span></h3></td>
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
			"DOWNLOAD SERVICE ITEM REQUESTED - ".$execution_time,
			'excel_downloader.php'
		);

	}
	function serviceRequested(){
		$time_start = microtime(true);

			$filename = "service-requested-" . date('m-d-Y-H-i-s-A') . ".xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");

		// pages,
		$user = new User();
		$used_cls = new Service_item_use();

		$s = Input::get('s');
		$date_from = Input::get('dt_to');
		$date_to = Input::get('dt_from');
		$type = Input::get('type');


		//$targetpage = "paging.php";
		$limit = 10000;
	//	$countRecord = $used_cls->countRecordRequested($user->data()->company_id,$s,$date_from,$date_to,$type);
		$total_pages = 0;
		$stages = 3;
		$page = (0);
		$page = (int)$page;

		if($page) {
			$start = ($page - 1) * $limit;
		} else {
			$start = 0;
		}

		$company_inv = $used_cls->get_record_requested($user->data()->company_id, $start, $limit,$s,$date_from,$date_to,$type);
		getpagenavigation($page, $total_pages, $limit, $stages);
		?>
		<div id="no-more-tables">
			<div class="table-responsive">
				<table class='table' id='tblSales' border="1">
					<thead>
					<tr>
						<th>Service Id</th>
						<th>Invoice</th>
						<th>DR</th>
						<th><?php echo MEMBER_LABEL; ?></th>
						<th>Technician</th>
						<th>Item</th>
						<th>Description</th>
						<th>Qty</th>
						<th>Remarks</th>

					</tr>
					</thead>
					<tbody>
					<?php
						if($company_inv) {

							$tech = new Technician();
							$tech_list = $tech->get_active('technicians',[1,'=',1]);
							$arr_tech = [];

							if($tech_list){
								foreach($tech_list as $te){
									$arr_tech[$te->id] = $te->name;
								}
							}

							$prev_val  ="";
							foreach($company_inv as $det) {
								$service_id = "";
								$member_name= "";
								$border_top = "";
								$technician_names= "";
								$invoice= "";
								$dr= "";




								$tech_ids = $det->technician_id;

								$tech_names = "None";

								if($tech_ids){
									$tech_ids = explode(',',$tech_ids);
									$tech_names ='';
									foreach($tech_ids as $tids){
										if(isset($arr_tech[$tids])){
											$tech_names .= "<p>".$arr_tech[$tids]."</p>";
										}
									}
								}

								if($prev_val != $det->service_id){
									$service_id = $det->service_id;
									$member_name = $det->member_name;
									$border_top ='border-top:1px solid #ccc;';
									$technician_names = $tech_names;
									$invoice= $det->invoice;
									$dr= $det->dr;
								}

								$prev_val = $det->service_id;

								?>
								<tr>
									<td data-title='Type'><?php echo escape($service_id);  ?></td>
									<td data-title='Type'><?php echo escape($invoice);  ?></td>
									<td data-title='Type'><?php echo escape($dr);  ?></td>
									<td  data-title='<?php echo MEMBER_LABEL; ?>'><?php echo escape(capitalize($member_name));  ?></td>
									<td ><?php echo  $technician_names; ?></td>
									<td ><?php echo escape($det->item_code);  ?></td>
									<td ><?php echo  escape($det->description); ?></td>
									<td ><?php echo escape(formatQuantity($det->qty));  ?></td>
									<td ><?php echo escape($det->remarks);  ?></td>

								</tr>
								<?php
							}
						} else {
							?>
							<tr>
								<td colspan='3'><h3><span class='label label-info'>No Record Found...</span></h3></td>
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
			"DOWNLOAD SERVICE ITEM REQUESTED - ".$execution_time,
			'excel_downloader.php'
		);

	}

	function sparepartsDetail(){


		$filename = "sp-details-" . date('m-d-Y-H-i-s-A') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");


		$spare = new Composite_item();


		$id = Input::get('id');

		$list = $spare->getSpDetails($id);



		if($list){

			$item_set = "";
			$ret = "<table border='1'>";
			$ret .= "<tr><th>Item Code</th><th>Description</th><th>Qty</th><th>Rack</th><th>Out</th><th>Remarks</th></tr>";
			foreach($list as $l){
				$item_set = $l->set_description;
				$ret .= "<tr><td>$l->item_code</td><td>$l->description</td><td>$l->qty</td><td></td><td></td><td></td></tr>";
			}
			$ret .= "</table>";
			echo "<table border='1'>";
			echo "<tr><td colspan='1'>Date</td><td colspan='5'>".date('m/d/Y')."</td></tr>";
			echo "<tr><td colspan='1'>Client</td><td colspan='5'></td></tr>";
			echo "<tr><td colspan='1'>Item</td><td colspan='5'>$item_set</td></tr>";
			echo "</table>";

			echo $ret;

		} else {
			echo "No record";
		}


	}

	function invlog(){
		// pages

		$filename = "add-inventory-log-" . date('m-d-Y-H-i-s-A') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");

		$user = new User();
		$add_batch_inv = new Add_batch_inv();
		$branch_id = Input::get('branch_id');
		$dt_from = Input::get('dt_from');
		$dt_to = Input::get('dt_to');
		$item_id = Input::get('item_id');
		$category_id = Input::get('category_id');

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
		$cid = $user->data()->company_id;
		?>


				<table class='table' id='tblSales' border='1'>
					<thead>
					<tr>
						<th>Item</th>
						<th>Qty</th>
						<TH>Branch</TH>
						<th>User</th>
						<TH>Supplier</TH>
						<th>Date Created</th>
						<TH>Date Received</TH>
						<th>Packing List</th>
						<th>Ref #</th>

					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 5000;
						$start = 0;


						$company_inv = $add_batch_inv->get_record_details($cid, $start, $limit,$branch_id,$dt_from,$dt_to,$item_id,$imploded);

						if($company_inv) {

							$added_arr = ['Added','Pending','Declined'];
							foreach($company_inv as $s) {
								$sup = ($s->supplier_id) ? $s->supplier_name : "<i class='fa fa-ban'></i>";
								$date_receive = ($s->date_receive) ? date('m/d/Y',$s->date_receive) : "<i class='fa fa-ban'></i>";
								$packing = ($s->packing_list_num) ? $s->packing_list_num : "<i class='fa fa-ban'></i>";
								$ref_num = ($s->ref_num) ? $s->ref_num : "<i class='fa fa-ban'></i>";
								?>
								<tr>
									<td data-title="Item Code"><?php echo escape($s->item_code) ?><small class='span-block'><?php echo escape($s->description) ?></small></td>
									<td data-title="Qty"><?php echo escape(formatQuantity($s->qty)) ?></td>
									<td data-title="Branch" class='text-danger'><?php echo $s->branch_name; ?></td>
									<td data-title="User" ><?php echo ucwords($s->firstname . " " . $s->lastname); ?></td>

									<td data-title="Branch" ><?php echo $sup; ?></td>
									<td data-title="Created"><?php echo escape(date('m/d/Y',$s->created)) ?></td>
									<td data-title="Received"><?php echo ($date_receive) ?></td>
									<td data-title="Packing" class='text-muted'><?php echo $packing ?></td>
									<td data-title="Ref" class='text-muted'><?php echo $ref_num ?></td>


								</tr>
								<?php
							}
						} else {
							?>
							<tr>
								<td colspan='6'><h3><span class='label label-info'>No Record Found...</span></h3></td>
							</tr>
							<?php
						}
					?>
					</tbody>
				</table>

		<?php
	}

	function inventoriesissues(){
		// pages

		$filename = "inventory-issues-" . date('m-d-Y-H-i-s-A') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");

		// pages,
		$user = new User();
		$inv = new Inventory_issue();
		$search = Input::get('search');
		$b = Input::get('b');
		$r = Input::get('r');
		$t = Input::get('t');


		$arrType = ['',DAMAGE_LABEL,MISSING_LABEL,'Disposed',INCOMPLETE_LABEL,OTHER_ISSUE_LABEL];

		?>

		<div id="no-more-tables">
			<div class="table-responsive">
				<table class='table' id='tblSales' border="1">
					<thead>
					<tr>
						<TH>Branch</TH>
						<th>Type</th>
						<TH>Rack</TH>
						<TH>Barcode</TH>
						<TH>Item Code</TH>
						<th>Description</th>
						<th class='text-right'>Price</th>
						<TH class='text-right'>Qty</TH>
						<th></th>

					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 10000;
						$countRecord = $inv->countRecord($user->data()->company_id, $search, $b, $r,$t);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = (0);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_inv = $inv->get_sales_record($user->data()->company_id, $start, $limit, $search, $b, $r,$t);
						getpagenavigation($page, $total_pages, $limit, $stages);
						$total = 0;
						if($company_inv) {
							$invProduct = new Product();

							$amend_upload = new Amend_upload();

							foreach($company_inv as $s) {
								$price = $invProduct->getPrice($s->item_id);
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
								$results = $amend_upload->getAttachAllRack($s->item_id,$s->rack_id);
								$withAttac =false;
								if($results){
									$withAttac = true;
								}
								$total += $price->price * $s->qty;
								?>
								<tr>
									<td data-title="Branch"><?php echo escape($s->name) ?></td>
									<td data-title='Type' class='text-danger''><?php echo $arrType[$s->status] ?></td>
									<td data-title="Rack" class='text-danger'><?php echo "<strong>" . escape($s->rack)."</strong>".$alldis; ?></td>
									<td data-title="Barcode"><?php echo escape($s->barcode) ?></td>
									<td data-title="Item code"><?php echo escape($s->item_code) ?></td>
									<td data-title="Description" class='text-muted'><?php echo escape($s->description) ?></td>
									<td data-title="Price"  class='text-right'><?php echo escape(number_format($price->price, 2)) ?></td>
									<td data-title="Quantity" class='text-right' style='padding-right:20px;'>
										<strong>
											<?php
												echo formatQuantity($s->qty);
											?>
										</strong>
									</td>
									<td></td>
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
				<?php //echo number_format($total,2); ?>
			</div>
		</div>
		<?php

	}