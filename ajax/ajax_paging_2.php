<?php
	include 'ajax_connection.php';
	/*
	 * codes in ajax_paging is very large
	 */

	$functionName = Input::get('functionName');
	$params = Input::get('page');
	$cid = Input::get('cid');
	$functionName($params, $cid);

	function consumablePaginate($args, $cid) {
		$page = new Pagination(new Payment_consumable());
		$page->setCompanyId($cid);
		$page->setPageNum($args);
		$page->paginate();
	}

    function bundleList($args, $cid) {
		$search = Input::get('txtSearch');
		$bundle = new Bundle();
		$user = new User();
		?>
		<div id="no-more-tables">
			<table class='table' >
				<thead>
				<tr>

					<TH>Parent</TH>
					<TH>Child</TH>
					<TH>Qty</TH>
					<th></th>

				</tr>
				</thead>
				<tbody>
				<?php
					//$targetpage = "paging.php";

					$limit = 20;
					$countRecord = $bundle->countRecord($cid,$search);

					$total_pages = $countRecord->cnt;

					$stages = 3;
					$page = ($args);
					$page = (int)$page;
					if($page) {
						$start = ($page - 1) * $limit;
					} else {
						$start = 0;
					}

					$company_op = $bundle->get_record($cid, $start, $limit,$search);
					getpagenavigation($page, $total_pages, $limit, $stages);

					if($company_op) {
						$prev = "";
						$border_top = "";
						foreach($company_op as $o) {
								$parent_name = '';
								$child_name = $o->item_code_child . "<small class='span-block text-danger'>".$o->description_child."</small>";
								if($prev != $o->item_code){
									$parent_name = $o->item_code . "<small class='span-block text-danger'>".$o->description."</small>";
									$border_top = "border-top:1px solid #ccc;";
								} else {
									$border_top = "";
								}
								$prev = $o->item_code;
							?>
							<tr data-item_name="<?php echo $o->item_code_child; ?>" data-id='<?php echo $o->id; ?>'>
								<td style='<?php echo $border_top; ?>' data-title='Parent'><?php echo $parent_name ?></td>
								<td style='<?php echo $border_top; ?>' data-title='Child'><?php echo $child_name ?></td>
								<td style='<?php echo $border_top; ?>' data-title='Qty'><?php echo formatQuantity(escape($o->child_qty),true); ?></td>
								<td style='<?php echo $border_top; ?>' data-title='Action'>
									<?php if($user->hasPermission('bundles_m')){
									?>
										<button class='btn btn-default btn-sm btnEdit'><i class='fa fa-pencil'></i></button>
										<button class='btn btn-default btn-sm btnDelete'><i class='fa fa-trash'></i></button>
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

	function notReceived($args, $cid) {
		$search = Input::get('txtSearch');
		$notReceived = new Not_receive_item();
		?>
		<div id="no-more-tables">
			<table class='table' id='tblSales'>
				<thead>
				<tr>

					<TH>Supplier Order ID</TH>
					<TH>Item</TH>
					<TH>Qty</TH>
					<th></th>

				</tr>
				</thead>
				<tbody>
				<?php
					//$targetpage = "paging.php";

					$limit = 500;
					$countRecord = $notReceived->countRecord($cid,$search);

					$total_pages = $countRecord->cnt;

					$stages = 3;
					$page = ($args);
					$page = (int)$page;
					if($page) {
						$start = ($page - 1) * $limit;
					} else {
						$start = 0;
					}

					$company_op = $notReceived->get_record($cid, $start, $limit,$search);
					getpagenavigation($page, $total_pages, $limit, $stages);

					if($company_op) {
						$prev = "";
						foreach($company_op as $o) {
								$parent_name = '';
								if($prev != $o->supplier_order_id){
									$parent_name = $o->supplier_order_id;
									$border_top = "border-top:1px solid #ccc;";
								} else {
									$border_top = "";
								}
								$prev = $o->supplier_order_id;
							?>
							<tr data-id=''>
								<td style='<?php echo $border_top; ?>' data-title='Qty'><?php echo $parent_name; ?></td>
								<td style='<?php echo $border_top; ?>' data-title='Parent'><?php echo  $o->item_code; ?></td>
								<td style='<?php echo $border_top; ?>' data-title='Child'><?php echo $o->qty; ?></td>
								<td style='<?php echo $border_top; ?>' data-title='Action'>

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


	function getpagenavigation($page, $total_pages, $limit, $stages) {
		if($page == 0) {
			$page = 1;
		}
		$prev = $page - 1;
		$next = $page + 1;
		$lastpage = ceil($total_pages / $limit);
		$LastPagem1 = $lastpage - 1;


		$paginate = '';
		if($lastpage > 1) {

			$paginate .= "<div style='padding:3px;' class='text-right'><ul class='pagination' >";

			if($page > 1) {
				$paginate .= "<li><a href='#'  class='paging' page='$prev' style='padding:5px'><span class='hidden-xs'>PREV</span><span class='visible-xs'><span class='glyphicon glyphicon-chevron-left'></span></span></a></li>";
			} else {
				$paginate .= "<li class='disabled'><span class='disabled' style='padding:5px'><span class='hidden-xs'>PREV</span><span class='visible-xs'><span class='glyphicon glyphicon-chevron-left'></span></span></span></span></li>";
			}


			if($lastpage < 7 + ($stages * 2)) {
				for($counter = 1; $counter <= $lastpage; $counter++) {
					if($counter == $page) {
						$paginate .= "<li class='active'><span class='current' style='padding:5px'>$counter</span></li>";
					} else {
						$paginate .= "<li><a href='#'  class='paging' page='$counter' style='padding:5px'>$counter</a></li>";
					}
				}
			} elseif($lastpage > 5 + ($stages * 2)) {

				if($page < 1 + ($stages * 2)) {
					for($counter = 1; $counter < 4 + ($stages * 2); $counter++) {
						if($counter == $page) {
							$paginate .= "<li class='active'><span class='current' style='padding:5px'>$counter</span></li>";
						} else {
							$paginate .= "<li><a href='#'  class='paging' page='$counter' style='padding:5px'>$counter</a></li>";
						}
					}
					$paginate .= "<li><span style='padding:5px'>...</span></li>";
					$paginate .= "<li><a href='#'   class='paging' page='$LastPagem1' style='padding:5px'>$LastPagem1</a></li>";
					$paginate .= "<li><a href='#' class='paging' page='$lastpage' style='padding:5px'>$lastpage</a></li>";
				} elseif($lastpage - ($stages * 2) > $page && $page > ($stages * 2)) {
					$paginate .= "<li><a href='#' class='paging' page='1'  style='padding:5px'>1</a></li>";
					$paginate .= "<li><a href='#' class='paging' page='2'  style='padding:5px'>2</a></li>";
					$paginate .= "<li><span style='padding:5px'>...</span></li>";
					for($counter = $page - $stages; $counter <= $page + $stages; $counter++) {
						if($counter == $page) {
							$paginate .= "<li class='active'><span class='current' style='padding:5px'>$counter</span></li>";
						} else {
							$paginate .= "<li><a href='#' class='paging' page='$counter'  style='padding:5px'>$counter</a></li>";
						}
					}
					$paginate .= "<li><span  style='padding:5px'>...</span></li>";
					$paginate .= "<li><a href='#' class='paging' page='$LastPagem1' style='padding:5px'>$LastPagem1</a></li>";
					$paginate .= "<li><a  href='#'  class='paging' page='$lastpage' style='padding:5px'>$lastpage</a></li>";
				} else {
					$paginate .= "<li><a href='#' class='paging' page='1' style='padding:5px'>1</a></li>";
					$paginate .= "<li><a href='#' class='paging' page='2' style='padding:5px'>2</a></li>";
					$paginate .= "<li><span style='padding:5px'>...</span></li>";
					for($counter = $lastpage - (2 + ($stages * 2)); $counter <= $lastpage; $counter++) {
						if($counter == $page) {
							$paginate .= "<li class='active'><span class='current' style='padding:5px'>$counter</span></li>";
						} else {
							$paginate .= "<li><a href='#' class='paging' page='$counter'  style='padding:5px'>$counter</a></li>";
						}
					}
				}
			}


			if($page < $counter - 1) {
				$paginate .= "<li><a href='#' class='paging' page='$next' style='padding:5px'><span class='hidden-xs'>NEXT</span><span class='visible-xs'><span class='glyphicon glyphicon-chevron-right'></span></span></a></li>";
			} else {
				$paginate .= "<li class='disabled'><span class='disabled' style='padding:5px'><span class='hidden-xs'>NEXT</span><span class='visible-xs'><span class='glyphicon glyphicon-chevron-right'></span></span></span></li>";
			}

			$paginate .= "</ul></div><div style='clear: both;'></div>";


		}
		// echo $total_pages.' Results';
		echo $paginate;
	}


	function groupAdjustment($args,$cid){


		$log = new Item_group_adjustment();

		$search_item = Input::get('search_item');
		$group_id = Input::get('group_id');


		?>
		<div id="no-more-tables">


		<table class='table' id='tblSales'>
			<thead>
			<tr>
				<th>Group</th>
				<th>Item</th>
				<th>Price</th>
				<th>Adjustment</th>
				<th>Adjusted Price</th>
				<th>Created by</th>
				<th>Date</th>
			</tr>
			</thead>
			<tbody>
			<?php
				//$targetpage = "paging.php";
				$limit = 30;

				$countRecord = $log->countRecord($cid,$search_item,$group_id);


				$total_pages = $countRecord->cnt;

				$stages = 3;
				$page = ($args);
				$page = (int)$page;
				if($page) {
					$start = ($page - 1) * $limit;
				} else {
					$start = 0;
				}

				$company_sales = $log->get_record($cid, $start, $limit,$search_item,$group_id);

				getpagenavigation($page, $total_pages, $limit, $stages);

				if($company_sales) {
					$prodcls = new Product();
					foreach($company_sales as $s) {
						$price = $prodcls->getPrice($s->id);



						?>
						<tr>
							<td><?php echo $s->name; ?></td>
							<td data-title='Item'>
								<?php echo $s->item_code . "<small style='display:block;' class='text-danger'>".$s->description."</small>"?>
							</td>
							<td>
								<?php echo escape(number_format($price->price,2)); ?><br>
							</td>
							<td data-title='Adjustment'>
								<?php echo escape(number_format($s->adjustment,2)); ?><br>
							</td>
							<td>
								<?php echo escape(number_format($price->price+$s->adjustment ,2)); ?><br>
							</td>
							<td data-title='Modified by'>
								<?php echo escape(ucwords($s->lastname . ", " . $s->firstname)); ?><br>
							</td>
							<td data-title='Created'>
								<?php
									echo escape(date('F d, Y H:i:s A',$s->created));
								?>
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
	}


	function itemRequestedPaginate($args, $cid) {
		// pages,
		$user = new User();
		$used_cls = new Service_item_use();

		$s = Input::get('s');
		$date_from = Input::get('dt_to');
		$date_to = Input::get('dt_from');
		$type = Input::get('type');


		//$targetpage = "paging.php";
		$limit = 30;
		$countRecord = $used_cls->countRecordRequested($cid,$s,$date_from,$date_to,$type);
		$total_pages = isset($countRecord->cnt) ? $countRecord->cnt : 0;
		$stages = 3;
		$page = ($args);
		$page = (int)$page;

		if($page) {
			$start = ($page - 1) * $limit;
		} else {
			$start = 0;
		}

		$company_inv = $used_cls->get_record_requested($cid, $start, $limit,$s,$date_from,$date_to,$type);
		getpagenavigation($page, $total_pages, $limit, $stages);
		?>
		<div id="no-more-tables">
			<div class="table-responsive">
				<table class='table' id='tblSales'>
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
									$border_top ='border-top:1px solid #ccc;';
									$technician_names = $tech_names;
								}
								$prev_val = $det->service_id;

								?>
								<tr>
							    	<td style='<?php echo $border_top; ?>' data-title='Type'><?php echo escape($service_id);  ?></td>
									<td style='<?php echo $border_top; ?>' data-title='<?php echo MEMBER_LABEL; ?>'><?php echo escape(capitalize($member_name));  ?></td>
									<td style='<?php echo $border_top; ?>' data-title='Technician'><?php echo $technician_names ?></td>
									<td style='<?php echo $border_top; ?>' data-title='Points'><?php echo escape($det->item_code) . "<small class='text-danger span-block'>". escape($det->description)."</small>";  ?></td>
									<td style='<?php echo $border_top; ?>' ><?php echo escape(formatQuantity($det->qty));  ?></td>
									<td style='<?php echo $border_top; ?>' ></td>
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
	}

	   function branchGroups($args, $cid) {

		$branch_group_id = Input::get('branch_group_id');
		$search = Input::get('search');

		$branch_group = new Branch_group_pricelist();

			?>

				<?php
						//$targetpage = "paging.php";

						$limit = 100;
						$countRecord = $branch_group->countRecord($branch_group_id,$search);

						$total_pages = $countRecord->cnt;

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
							<div id="no-more-tables">
									<table class='table' id='tblSales'>
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
							</div>
		<?php } else {
		?>
			<div class="alert alert-info">
			No record found.
			</div>
		<?php
		} ?>

	<?php
	}


	function quotationPaginate($args, $cid){

		$page = new Pagination(new Quotation());
		$user = new User();
		$cid = $user->data()->company_id;
		$page->setCompanyId($cid);
		$page->setPageNum($args);
		$page->paginate();

	}

	function cbmList($args, $cid){

			$user = new User();
			$search = Input::get('search');

			$limit_by = Input::get('limit_by');

			$product = new Product();

			?>
			<div id="no-more-tables">
				<div class="table-responsive">

					<table class='table table_border_top' id='tblSales'>
						<thead>
						<tr>
							<TH class='page_sortby'>Barcode</TH>
							<TH class='page_sortby'>Item Code</TH>
							<th>Length</th>
							<th>Width</th>
							<th>Height</th>
							<th>CBM</th>
							<th>Weight</th>
							<TH>Actions</TH>

						</tr>
						</thead>
						<tbody>
						<?php
							//$targetpage = "paging.php";
							if($limit_by){
								$limit = $limit_by;
							} else {
								$limit = 20;
							}

							$countRecord = $product->countCbmRecord($cid, $search);

							$total_pages = $countRecord->cnt;

							$stages = 4;
							$page = ($args);
							$page = (int)$page;

							if($page) {
								$start = ($page - 1) * $limit;
							} else {
								$start = 0;
							}

							$company_items = $product->get_cbm_record($cid, $start, $limit, $search);
							$product->getPageNavigation($page, $total_pages, $limit, $stages);
							if($company_items) {
								foreach($company_items as $s) {
									$cbm_l = $s->cbm_l ? $s->cbm_l : 0;
									$cbm_w = $s->cbm_w ? $s->cbm_w : 0;
									$cbm_h = $s->cbm_h ? $s->cbm_h : 0;
									$item_weight = $s->item_weight ? $s->item_weight : 0;
									$cmb = $cbm_l * $cbm_w * $cbm_h;
									$cmb = number_format($cmb,6);
									?>

									<tr>
										<td><?php echo $s->barcode; ?></td>
										<td>
											<?php echo $s->item_code; ?>
											<small class='span-block text-danger'><?php echo $s->description; ?></small>
										</td>
										<td><?php echo $cbm_l; ?></td>
										<td><?php echo $cbm_w; ?></td>

										<td><?php echo $cbm_h; ?></td>
										<td><?php echo $cmb; ?></td>


										<td><?php echo $item_weight; ?></td>
										<td>
										<button
										data-id='<?php echo $s->id; ?>'
										data-cbm_l='<?php echo $cbm_l; ?>'
										data-cbm_w='<?php echo $cbm_w; ?>'
										data-cbm_h='<?php echo $cbm_h; ?>'
										data-item_weight='<?php echo $item_weight; ?>'
										class='btn btn-default btnUpdate'><i class='fa fa-pencil'></i></button>
										</td>
									</tr>
									<?php
								}
							}
							?>

						</tbody>
					</table>
				</div>
			</div>
			<?php
	}

	function memberCreditList($args, $cid) {
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

		if(!$user->hasPermission('credit_all')){
			$branch_id = [$user->data()->branch_id];
		}

		if($branch_id && is_numeric($branch_id)){
			$branch_id = [$branch_id];
		} else if(!is_array($branch_id)) {
			$branch_id = json_decode($branch_id,true);
		}
		if(is_array($branch_id) && $branch_id[0] == -1){
			$terminal_id = [0];
			$branch_id = [];
		}
		if($terminal_id && is_numeric($terminal_id)){
			$terminal_id = [$terminal_id];
		} else if(!is_array($terminal_id)) {
			$terminal_id = json_decode($terminal_id,true);
		}

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

		?>
		<div id="no-more-tables">
			<div class="table-responsive">
				<table class='table' id='tblSales'>
					<thead>
					<tr>
						<TH>Name</TH>
						<th><?php echo INVOICE_LABEL; ?>/<?php echo DR_LABEL; ?></th>
						<th>Date</th>
						<TH>Payment</TH>
						<TH>Details</TH>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						if($type == 2){
							$limit = 30;
						} else {
							$limit = 30;
						}

						$countRecord = $mem->countRecord($cid, $search, $type,$dt_from,$dt_to,$branch_id,$terminal_id,$sales_type,$user_id);

						$total_pages = isset($countRecord->cnt) ? $countRecord->cnt : 0;
						$total_pages;
						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}



						$company_inv = $mem->get_member_record($cid, $start, $limit, $search, $type,$dt_from,$dt_to,$branch_id,$terminal_id,$sales_type,$user_id);

						getpagenavigation($page, $total_pages, $limit, $stages);

						if($company_inv) {
							$prev_mem = '';

							foreach($company_inv as $s) {

									$amt = $s->amount;
									$paid =  $s->amount_paid;
									$pendingamount = $amt - $paid;

									$bordertop = 'border-top:1px solid #ccc;';

									$prev_mem = $s->member_id;
									if($pendingamount == 0 && $s->status == 1){
										$labelstat = "<div class='alert alert-info'><span class='glyphicon glyphicon-info-sign'></span> Fully Paid</div>";
									} else if($pendingamount == 0 && $s->status == -1){
										$labelstat = "<div class='alert alert-info'><span class='glyphicon glyphicon-info-sign'></span> For approval</div>";
									} else{
										$labelstat = "<div class='alert alert-danger'><span class='glyphicon glyphicon-info-sign'></span> Unpaid</div>";
									}



									$ctrnum = '';
									$doc_receive='';

									if($s->invoice){
										$ctrnum .= "<span  class='span-block'><strong>".INVOICE_LABEL."#</strong> ". escape($s->invoice) . "</span>";
									}

									if($s->dr){
										$ctrnum .=  "<span  class='span-block'><strong>".DR_LABEL."#</strong>  ". escape($s->dr) . "</span>";
									}

									if($s->ir){
										$ctrnum .=  "<span  class='span-block'><strong>".PR_LABEL."# </strong> ". escape($s->ir) . "</span>";
									}

									if($s->sr){
										$ctrnum .=  "<span  class='span-block'><strong>SR#</strong>  ". escape($s->sr) . "</span>";
									}

									$bgwarn = "";

									$to_branch_name='';

									if($s->to_branch_name){
										$to_branch_name = $s->to_branch_name;
									}
									
									$detpay = "<p><i class='fa fa-ban'></i></p>";

									if($s->holders){

										$detpay = '';
										$holders = json_decode($s->holders,true);
										if(count($holders) > 0){

											$detpay .= "<table class='table table-bordered'>";
											$detpay .= "<tr><th style='width:150px;'>Name</th><th style='width:60px;'>Date</th></tr>";

											foreach($holders as $pp){
												$detpay .= "<tr><td>$pp[name] <small class='text-danger span-block'>$pp[remarks]</small></td><td>$pp[date]</td></tr>";
											}
	
										} else {
											$detpay = "<p><i class='fa fa-ban'></i></p>";
										}
										$detpay .= " </table>";

									}

								?>
								<tr class='<?php echo $bgwarn; ?>' data-total='<?php echo $s->amount - $s->amount_paid; ?>'>
									<td style='<?php echo $bordertop; ?>width:250px;' data-title="Name" class='text-danger'>
									<?php echo ucwords(escape($s->lastname . ", " . $s->firstname . " " . $s->middlename)) . " "; ?>
									<small class='span-block'><?php echo ucwords($s->station_name); ?></small>
									<small class='span-block'><?php echo ucwords($to_branch_name) . " "; ?></small>
									<small class='span-block'><?php echo ($s->wh_remarks) ? "<span class='text-muted'>Remarks: " . $s->wh_remarks ."</span>": ""; ?></small>
									<small class='text-muted' style='display:block;'><?php echo ucwords(escape($s->ufn . " " . $s->uln)); ?></small>
									<?php if($s->cr_number){ ?>
										<small class='span-block'>CR Number: <span class='text-muted'><?php echo $s->cr_number; ?></span></small>
										<small class='span-block'>CR Date: <span class='text-muted'><?php echo date('m/d/Y',$s->cr_date); ?></span></small>
									<?php } ?>

									</td>
									<td style='<?php echo $bordertop; ?>'>
									 <?php echo $ctrnum; ?>
									 <?php echo $doc_receive; ?>
									</td>
									<td style='<?php echo $bordertop; ?>' data-title="Date"><?php echo date('m/d/Y',escape($s->solddate)); ?></td>
									<td style='<?php echo $bordertop; ?>'>
									<span class='span-block'><strong style='width:100px;display: inline-block;'>Total Credit:</strong> <?php echo number_format(escape($s->amount),2); ?></span>
									<span class='span-block'><strong style='width:100px;display: inline-block;'>Paid:</strong> <?php echo number_format(escape($s->amount_paid),2); ?></span>
									<span class='span-block'><strong style='width:100px;display: inline-block;'>Remaining:</strong> <?php echo number_format(escape($s->amount - $s->amount_paid),2); ?></span>
									</td>
									<td style='<?php echo $bordertop; ?>'>
										<?php echo $detpay; ?>
									</td>
									<td style='<?php echo $bordertop; ?>'>
									<?php echo $labelstat; ?>
									<button  class='btn btn-default btnAddDetails' data-id='<?php echo $s->id; ?>'><i class='fa fa-plus'></i> Add Details</button>
									</td>
								</tr>
							<?php
							}
						} else {
							?>
							<tr>
								<td colspan='5'><h3><span class='label label-info'>No Record Found...</span></h3></td>
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
