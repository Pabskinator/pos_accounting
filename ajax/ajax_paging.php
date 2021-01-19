<?php
	include 'ajax_connection.php';
	/*
	 * get the function name and run it
	 */
	$functionName = Input::get('functionName');
	$params = Input::get('page');
	$cid = Input::get('cid');

	$functionName($params, $cid);


function shippingCompany($args, $cid) {
	$page = new Pagination(new Shipping_company());
	$page->setCompanyId($cid);
	$page->setPageNum($args);
	$page->paginate();

}
function cityMun($args, $cid) {
	$page = new Pagination(new City_mun());
		$page->setCompanyId($cid);
		$page->setPageNum($args);
		$page->paginate();

}

	function dicerDeposits($args, $cid) {
	$search = Input::get('search');

	$dd = new Dicer_deposit();


				//$targetpage = "paging.php";

				$limit = 50;
				$countRecord = $dd->countRecord($cid,$search);

				$total_pages = $countRecord->cnt;

				$stages = 3;
				$page = ($args);
				$page = (int)$page;
				if($page) {
					$start = ($page - 1) * $limit;
				} else {
					$start = 0;
				}

				$data_list = $dd->get_record($cid,$start, $limit, $search);
				getpagenavigation($page, $total_pages, $limit, $stages);

				if($data_list) {
					?>
					<div id="no-more-tables">
						<table class='table' id='tblSales'>
							<thead>
							<tr>

								<TH>Branch</TH>
								<TH>Name</TH>
								<TH>Amount</TH>
								<TH>Created</TH>
								<th></th>

							</tr>
							</thead>
							<tbody>
								<?php
						foreach($data_list as $o) {

							?>
							<tr id='row<?php echo $o->id?>'>
								<td style='border-top:1px solid #ccc;' data-title='Name'><?php echo capitalize(escape($o->branch_name)) ?></td>
								<td style='border-top:1px solid #ccc;' data-title='Name'><?php echo capitalize(escape($o->deposit_by)) ?></td>
								<td style='border-top:1px solid #ccc;' data-title='Amount'><?php echo number_format($o->amount,2) ?></td>
								<td style='border-top:1px solid #ccc;' data-title='Created'><?php echo date('m/d/Y H:i:s A',$o->created) ?></td>
								<td style='border-top:1px solid #ccc;' data-title='Action'>
									<button class='btn btn-danger btnDelete' data-id='<?php echo $o->id; ?>' >Delete Record</button>
								</td>

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
				<div class="alert alert-info">No record found.</div>
				<?php
				}

	}
function dicerBO($args, $cid) {

	$search = Input::get('search');

	$dd = new Bad_order_detail();


				//$targetpage = "paging.php";

				$limit = 50;
				$countRecord = $dd->countRecord($cid,$search);

				$total_pages = $countRecord->cnt;

				$stages = 3;
				$page = ($args);
				$page = (int)$page;
				if($page) {
					$start = ($page - 1) * $limit;
				} else {
					$start = 0;
				}

				$data_list = $dd->get_record($cid,$start, $limit, $search);
				getpagenavigation($page, $total_pages, $limit, $stages);

				if($data_list) {
					?>
					<div id="no-more-tables">
						<table class='table' id='tblSales'>
							<thead>
							<tr>

								<TH>Branch</TH>
								<TH>Item </TH>
								<TH>Qty</TH>
								<TH>Created</TH>
								<th></th>

							</tr>
							</thead>
							<tbody>
								<?php

						foreach($data_list as $o) {
							?>
							<tr id='row<?php echo $o->id?>'>
								<td style='border-top:1px solid #ccc;' data-title='Branch'><?php echo capitalize(escape($o->branch_name)) ?></td>
								<td style='border-top:1px solid #ccc;' data-title='Item'><?php echo capitalize(escape($o->item_code)) ?></td>
								<td style='border-top:1px solid #ccc;' data-title='Qty'><?php echo formatQuantity($o->qty); ?></td>
								<td  style='border-top:1px solid #ccc;' data-title='Created'><?php echo date('m/d/Y',$o->created); ?></td>
								<td  style='border-top:1px solid #ccc;'></td>
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
				<div class="alert alert-info">No record found.</div>
				<?php
				}

	}
function contactFormMessages($args, $cid) {


		$contact = new Contact_form_message();
		$user = new User();
		?>


				<?php
					//$targetpage = "paging.php";

					$limit = 50;
					$countRecord = $contact->countRecord($user->data()->company_id);

					$total_pages = $countRecord->cnt;

					$stages = 3;
					$page = ($args);
					$page = (int)$page;
					if($page) {
						$start = ($page - 1) * $limit;
					} else {
						$start = 0;
					}

					$company_op = $contact->get_record($user->data()->company_id,$start, $limit);
					getpagenavigation($page, $total_pages, $limit, $stages);

					if($company_op) {

						foreach($company_op as $o) {

							?>
							<div class="col-md-3">
								<div class="thumbnail" >

							      <div class="caption">
							        <p><strong><?php echo capitalize(escape($o->fullname)) ?></strong></p>
							        <p><i class='fa fa-envelope'></i> <?php echo $o->email ?>
							        <span class='span-block'><i class='fa fa-phone'></i> <?php echo $o->contact_number ?></span>

							        </p>
							        <p class='text-muted' style='height:90px;overflow-x:auto;'>
							        <i class='fa fa-envelope-o'></i> <?php echo nl2br($o->msg) ?>
							        </p>
							         <small class='span-block text-success'><i class='fa fa-clock-o'></i> <?php echo date('F d, Y H:i:s A',$o->created); ?></small>
							      </div>
							    </div>
							</div>


						<?php
						}
					} else {
						?>
						<div class="alert alert-info">No record found.</div>
					<?php
					}
				?>

	<?php
	}
		function smsLogPaginate($args, $cid) {

			$search = Input::get('search');
			$b  = Input::get('b');
			$type  = Input::get('type');
			$smsrec = new Sms_receive();

		?>
		<div id="no-more-tables">
			<table class='table' id='tblForApproval'>
				<thead>
				<tr>

					<TH>Name</TH>
					<TH>Number</TH>

					<TH>Date Reported</TH>
					<TH>Status</TH>
					<th></th>

				</tr>
				</thead>
				<tbody>
				<?php
					//$targetpage = "paging.php";

					$limit = 20;
					$countRecord = $smsrec->countRecord($cid, $search,$b,$type);

					$total_pages = $countRecord->cnt;

					$stages = 3;
					$page = ($args);
					$page = (int)$page;

					if($page) {
						$start = ($page - 1) * $limit;
					} else {
						$start = 0;
					}

					$company_op = $smsrec->get_record($cid, $start, $limit, $search, $b,$type);

					getpagenavigation($page, $total_pages, $limit, $stages);

					if($company_op) {

						$arr_status  = ['Pending','Processed'];
						foreach($company_op as $o) {
								?>
								<tr data-id='<?php echo $o->id; ?>'>
									<td data-title='Name'>
										<?php echo capitalize(escape($o->name)) ?>
										<span class='span-block text-danger'>
										<?php
										$bdesc = "";
										 if($o->terminal_name) {
											echo $o->terminal_name;

											$branch = new Branch($o->branch_id1);
											$bdesc = $branch->data()->description;
										}   else {
											echo $o->terminal_name2;
											$branch = new Branch($o->branch_id2);
											$bdesc = $branch->data()->description;
										}
										?>
										</span>
									</td>
									<td data-title='Number'>
										<?php echo escape($o->number) ?>
									</td>
									<td data-title='Reported Date'>
										<?php echo date('m/d/Y',strtotime(escape($o->date_received))); ?>
										<button data-id='<?php echo $o->id; ?>' data-dt='<?php echo date('m/d/Y',strtotime(escape($o->date_received))); ?>' class='btn btn-default btn-sm btnUpdateDate' >
										<i class='fa fa-pencil'></i></button>
									</td>
									<td class='text-danger''><?php echo $arr_status[$o->status]; ?></td>
									<td data-title='Action'>
									<button data-branch_description='<?php echo $bdesc; ?>' data-name='<?php echo capitalize(escape($o->name)) ?>' data-received='<?php echo date('m/d/Y',strtotime(escape($o->date_received))); ?>' data-status='<?php echo escape($o->status) ?>' data-id='<?php echo escape($o->id) ?>' data-number='<?php echo escape($o->number) ?>' class='btn btn-default btnShowData' data-message='<?php echo escape($o->message); ?>' >
										Details
									</button>
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
	function freight_charges($args, $cid){

		$freight = new Freight();
		$status = Input::get('status');
		$search = Input::get('search');

		?>
		<div id="no-more-tables">
			<table class='table' id='tblForApproval'>
				<thead>
				<tr>

					<TH>Order ID</TH>
					<TH>Invoice</TH>
					<TH>DR</TH>
					<TH>PR</TH>
					<TH>Created</TH>
					<TH>Member Name</TH>
					<TH>Freight</TH>
					<TH>Payment</TH>
					<TH>Remarks</TH>
					<TH>Status</TH>
					<TH></TH>



				</tr>
				</thead>
				<tbody>
				<?php
					//$targetpage = "paging.php";

					if($search){
						$limit = 500;
					} else {
						$limit = 20;
					}



					$countRecord = $freight->countRecord($cid,$status,$search);

					$total_pages = $countRecord->cnt;

					$stages = 3;
					$page = ($args);
					$page = (int)$page;
					if($page) {
						$start = ($page - 1) * $limit;
					} else {
						$start = 0;
					}

					$company_op = $freight->get_record($cid, $start, $limit,$status,$search);
					getpagenavigation($page, $total_pages, $limit, $stages);

					if($company_op) {
						$status = ['Pending','Paid'];
						foreach($company_op as $o) {
							if($o->status == 1){
								$paid_amount = $o->charge + $o->freight_adjustment;
								$lbl = "<span class='label label-danger'>Paid</span>";
							} else {
								$paid_amount = $o->paid_amount;
							}
							?>
							<tr>
								<td data-title='ID'>
								<strong class='text-danger' >
									<?php echo (escape($o->wh_id)) ?>
								</strong>

								</td>
								<td data-title='Invoice'><?php echo (escape($o->invoice)) ?> </td>
								<td data-title='DR'><?php echo (escape($o->dr)) ?> </td>
								<td data-title='PR'><?php echo (escape($o->pr)) ?> </td>
								<td data-title='Created'><?php echo (date('m/d/Y',$o->created)) ?> </td>
								<td data-title='Item Code'>
								<?php echo capitalize(escape($o->member_name)) ?>

								</td>
								<td data-title='Charge' class='text-danger'><?php echo ((number_format($o->charge + $o->freight_adjustment,2))) ?> </td>
								<td data-title='Payment' class='text-danger'><?php echo ((number_format($paid_amount,2))) ?> </td>
								<td data-title='Remarks' class='text-muted'>
								<?php echo ((escape($o->remarks))) ?>

								</td>
								<td data-title=''>
								<span class='label label-default'>
								<?php echo $status[$o->status]; ?>
</span>

								</td>
								<td>
								<?php if($o->status == 0){
								?>
								<button class='btn btn-default btn-sm btnPay' data-id='<?php echo $o->id; ?>'> Mark as Paid</button>
								<?php

								} ?>

</td>

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
		</div>
		<?php
	}

	function itemReservedPaginate($args, $cid) {
		$s = Input::get('s');
		$branch_id = Input::get('branch_id');
		$wh_order = new Wh_order();

		?>
		<div id="no-more-tables">
			<table class='table' id='tblForApprovalReserved'>
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

					$limit = 100;
					$countRecord = $wh_order->countRecordPending($cid,$s,$branch_id);

					$total_pages = $countRecord->cnt;

					$stages = 3;
					$page = ($args);
					$page = (int)$page;
					if($page) {
						$start = ($page - 1) * $limit;
					} else {
						$start = 0;
					}

					$company_op = $wh_order->get_record_pending($cid, $start, $limit,$s,$branch_id);
					getpagenavigation($page, $total_pages, $limit, $stages);

					if($company_op) {

						foreach($company_op as $o) {
									if(!$o->wh_orders_id) continue;

							$is_bundle = $o->is_bundle;

							if($is_bundle){
 									$bundle = new Bundle();
									$bundle_list = $bundle->getBundleItem($o->item_id);
									if($bundle_list){
										foreach($bundle_list as $bund){
											?>
											<tr>
									<td><?php echo (escape($o->wh_orders_id)) ?> </td>
									<td><?php echo (escape($o->invoice)) ?> </td>
									<td><?php echo (escape($o->dr)) ?> </td>
									<td><?php echo (escape($o->pr)) ?> </td>
									<td><?php echo (date('m/d/Y H:i:s A',$o->created)) ?> </td>
									<td data-title='Item Code'>
									<?php echo capitalize(escape($bund->item_code)) ?>
									<span class='text-danger span-block'><?php echo $bund->description; ?></span>
									<small class='span-block text-success'>Parent: <?php echo $o->item_code; ?></small>
									<small class='span-block text-success'> <?php echo $o->description; ?></small>
									</td>
									<td data-title='Qty'><?php echo formatQuantity((escape($o->qty * $bund->child_qty))) ?> </td>

								</tr>
											<?php

										}
									}
							}else if ($o->item_id_set){
								$composite = new Composite_item();
								$assembly_list = $composite->getSpareparts($o->item_id_set);
								if($assembly_list){
									foreach($assembly_list as $ass){
									?>
											<tr>
									<td><?php echo (escape($o->wh_orders_id)) ?> </td>
									<td><?php echo (escape($o->invoice)) ?> </td>
									<td><?php echo (escape($o->dr)) ?> </td>
									<td><?php echo (escape($o->pr)) ?> </td>
									<td><?php echo (date('m/d/Y H:i:s A',$o->created)) ?> </td>
									<td data-title='Item Code'>
									<?php echo capitalize(escape($ass->item_code)) ?>
									<span class='text-danger span-block'><?php echo $ass->description; ?></span>
									<small class='span-block text-success'>Parent: <?php echo $o->item_code; ?></small>
									<small class='span-block text-success'> <?php echo $o->description; ?></small>
									</td>
									<td data-title='Qty'><?php echo formatQuantity((escape($o->qty * $ass->qty))) ?> </td>

								</tr>
											<?php
									}
								}
							}
							else {
								?>
								<tr>
									<td><?php echo (escape($o->wh_orders_id)) ?> </td>
									<td><?php echo (escape($o->invoice)) ?> </td>
									<td><?php echo (escape($o->dr)) ?> </td>
									<td><?php echo (escape($o->pr)) ?> </td>
									<td><?php echo (date('m/d/Y H:i:s A',$o->created)) ?> </td>
									<td data-title='Item Code'>
									<?php echo capitalize(escape($o->item_code)) ?>
									<span class='text-danger span-block'><?php echo $o->description; ?></span>
									</td>
									<td data-title='Qty'><?php echo formatQuantity((escape($o->qty))) ?> </td>


								</tr>
							<?php

							}

							/* else if ($o->item_id_set){
								$composite = new Composite_item();
								$assembly_list = $composite->getSpareparts($o->item_id_set);
								if($assembly_list){
									foreach($assembly_list as $ass){
									?>
											<tr>
									<td><?php echo (escape($o->wh_orders_id)) ?> </td>
									<td><?php echo (escape($o->invoice)) ?> </td>
									<td><?php echo (escape($o->dr)) ?> </td>
									<td><?php echo (escape($o->pr)) ?> </td>
									<td><?php echo (date('m/d/Y H:i:s A',$o->created)) ?> </td>
									<td data-title='Item Code'>
									<?php echo capitalize(escape($ass->item_code)) ?>
									<span class='text-danger span-block'><?php echo $ass->description; ?></span>
									<small class='span-block text-success'>Parent: <?php echo $o->item_code; ?></small>
									<small class='span-block text-success'> <?php echo $o->description; ?></small>
									</td>
									<td data-title='Qty'><?php echo formatQuantity((escape($o->qty * $bund->child_qty))) ?> </td>

								</tr>
											<?php
									}
								}
							}*/

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
		</div>
	<?php
	}
	function ordersItemPaginate($args, $cid) {
		$user = new User();
		$item = new Product();
		$op = new Reorder_item();
		$branch = new Branch();
		$search = Input::get('search');

		?>
		<div id="no-more-tables">
			<table class='table' id='tblSales'>
				<thead>
				<tr>
					<TH>Barcode</TH>
					<TH>Item Code</TH>
					<TH>Order Quantity</TH>
					<TH>Branch</TH>
					<TH>Order Branch</TH>
					<TH>Created</TH>
					<th></th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php

					//$targetpage = "paging.php";
					$searchBranch = Input::get('searchBranch');
					$status = Input::get('status');
					$limit = 20;
					$countRecord = $op->countRecord($cid, $search, $searchBranch, $status);

					$total_pages = $countRecord->cnt;

					$stages = 3;
					$page = ($args);
					$page = (int)$page;
					if($page) {
						$start = ($page - 1) * $limit;
					} else {
						$start = 0;
					}

					$company_op = $op->get_active_record($cid, $start, $limit, $search, $searchBranch, $status);
					getpagenavigation($page, $total_pages, $limit, $stages);

					if($company_op) {
						foreach($company_op as $o) {
							$by = new Branch($o->orderby_branch_id);
							$to = new  Branch($o->orderto_branch_id);
							?>
							<tr>
								<td data-title='Barcode'><?php echo escape($o->barcode) ?></td>
								<td data-title='Item Code'><?php echo escape($o->item_code) ?></td>
								<td data-title='Quantity'><?php echo escape($o->qty) ?></td>
								<td data-title='Order by'><?php echo escape($by->data()->name) ?></td>
								<td data-title='Order from'><?php if($o->orderto_branch_id == 0) echo "Supplier"; else echo escape($to->data()->name); ?></td>
								<td data-title='Created'><?php echo date('m/d/Y', escape($o->created)); ?></td>
								<td data-title='Action'>
									<?php
										if($o->status == 1) {
									?>
										<input type="button" value='Process Order' class='btn btn-default processOrder' data-oid='<?php echo $o->id ?>' />
										<?php
										} else if($o->status == 2) {
											?>
											<input type="button" value='Transfer Order' class='btn btn-primary transferOrder' data-oid='<?php echo $o->id ?>' />
										<?php
										} else if($o->status == 3) {
											?>
											<span class='label label-default' style='font-size:1em'>Order received</span>
										<?php
										}
									?>
								</td>
								<td>
									<?php if($o->status == 2 || $o->status == 3) {
										?>
										<input type="button" class='btn btn-success timelog' data-oid='<?php echo $o->id ?>' value='Timelog' />
									<?php
									} ?>
								</td>
							</tr>
						<?php
						}
					} else {
						?>
						<tr>
							<td colspan='8' class='text-left'><h3>
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

function consumablesListPaginate($args, $cid){
		$page = new Pagination(new Consumable());
		$page->setCompanyId($cid);
		$page->setPageNum($args);
		$page->paginate();
}

function consumablesListPaginate2($cid,$args){

	$con = new Consumable();
	$con->paginate2($args,$cid);


}
function chequePaginate($cid,$args){

	$con = new Cheque();
	$con->paginate2($args,$cid);


}

function userPaginate($args, $cid){

		$page = new Pagination(new User());

		$page->setCompanyId($cid);

		$page->setPageNum($args);

		$page->paginate();

}

function departmentPaginate($args, $cid){

		$page = new Pagination(new Department());

		$page->setCompanyId($cid);

		$page->setPageNum($args);

		$page->paginate();

}

function deductions($args, $cid){

		$page = new Pagination(new Deduction());

		$page->setCompanyId($cid);

		$page->setPageNum($args);

		$page->paginate();

}

function consumablesFreebiesListPaginate($args, $cid){

		$user = new User();

		$consumables = new Consumable_freebies();

		$search = Input::get('search');

		?>
		<div id="no-more-tables">
			<table class='table' id='tblSales'>
				<thead>
				<tr>

					<TH>Member</TH>
					<TH>Consumable Freebies</TH>
					<TH class='text-right'>Not yet matured</TH>
					<th class='text-right'>Bounce</th>
					<TH class='text-right'>Valid</TH>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php
					//$targetpage = "paging.php";
					$limit = 20;
					$countRecord = $consumables->countRecord($cid, $search);

					$total_pages = $countRecord->cnt;

					$stages = 3;
					$page = ($args);
					$page = (int)$page;
					if($page) {
						$start = ($page - 1) * $limit;
					} else {
						$start = 0;
					}

					$company_op = $consumables->get_active_record($cid, $start, $limit, $search);
					getpagenavigation($page, $total_pages, $limit, $stages);

					if($company_op) {
						$cheque = new Cheque();
						$now = time();
						foreach($company_op as $o) {
								// get not yet valid cheque
								// get bounche cheque
								$mycheques = $cheque->getMemberCheque($o->payment_id);
								$notyetmatured = 0;
								$bounce = 0;
								if($mycheques){
									foreach($mycheques as $indc){
											if($now < $indc->payment_date){
												if($indc->status == 1){
												$notyetmatured +=  $indc->amount;
												}
											}
											if($indc->status == 3){
											$bounce +=  $indc->amount;
											}
									}
								}
								$valid =  $o->amount - ($notyetmatured + $bounce)
							?>
							<tr>
								<td><?php echo $o->lastname . ", " . $o->firstname; ?></td>
								<td><input class='form-control' type="text" value="<?php echo $o->amount?>"></td>
								<td class='text-right'><?php echo number_format($notyetmatured,2); ?></td>
								<td class='text-right'><?php echo  number_format($bounce,2); ?></td>
							<td class='text-right'><?php echo  number_format($valid,2); ?></td>
								<td><button class='btn btn-default btnUpdate' data-id="<?php echo $o->id; ?>">Update</button></td>
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
		<?php
}
	function orderPointPaginate($args, $cid) {
		$user = new User();
		$item = new Product();
		$op = new Reorder_point();
		$branch = new Branch();
		$search = Input::get('search');
		?>
		<div id="no-more-tables">
			<table class='table' id='tblSales'>
				<thead>
				<tr>

					<TH>Barcode</TH>
					<TH>Item Code</TH>
					<TH>Reorder point</TH>
					<TH>Order Quantity</TH>
					<TH>Branch</TH>
					<TH>Order Branch</TH>
					<TH>Month</TH>
					<TH>Created</TH>
					<?php if($user->hasPermission('orderpoint_m')) { ?>
						<th></th>
					<?php } ?>
				</tr>
				</thead>
				<tbody>
				<?php
					//$targetpage = "paging.php";
					$searchBranch = Input::get('searchBranch');
					$limit = 20;
					$countRecord = $op->countRecord($cid, $search, $searchBranch);

					$total_pages = $countRecord->cnt;

					$stages = 3;
					$page = ($args);
					$page = (int)$page;
					if($page) {
						$start = ($page - 1) * $limit;
					} else {
						$start = 0;
					}

					$company_op = $op->get_active_record($cid, $start, $limit, $search, $searchBranch);
					getpagenavigation($page, $total_pages, $limit, $stages);

					if($company_op) {
						$mm = array('Unknown', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December', 'ALL');
						foreach($company_op as $o) {
							$by = new Branch($o->orderby_branch_id);
							if($o->orderto_branch_id && $o->orderto_branch_id != -2 && $o->orderto_branch_id != -1){ // -2 = other branch
								$to = new  Branch($o->orderto_branch_id);
								$od_form = $to->data()->name;
							} else if($o->orderto_supplier_id){
								$od_form = "Supplier";
							} else {
								if($o->orderto_branch_id == -2){
								$od_form = "Other branch";
								} else {
								$od_form = Configuration::getValue('assemble');
								}
							}

							?>
							<tr>
								<td data-title='Barcode'><?php echo escape($o->barcode) ?></td>
								<td data-title='Item Code'><?php echo escape($o->item_code) ?></td>
								<td data-title='Order Point'><?php echo escape($o->order_point) ?></td>
								<td data-title='Qty'><?php echo escape($o->order_qty) ?></td>
								<td data-title='Order By'><?php echo escape($by->data()->name) ?></td>
								<td data-title='Order From'><?php echo escape($od_form); ?></td>
								<td data-title='Month'><?php echo $mm[$o->month]; ?></td>
								<td data-title='Created'><?php echo date('m/d/Y', escape($o->created)); ?></td>
								<?php if($user->hasPermission('orderpoint_m')) { ?>
									<td data-title='Action'>
										<a class='btn btn-primary' href='addorderpoint.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt', $o->id); ?>' title='Edit Order Point'><span class='glyphicon glyphicon-pencil'></span></a>
										<a href='#' class='btn btn-primary deleteOrderPoint' id="<?php echo Encryption::encrypt_decrypt('encrypt', $o->id); ?>" title='Delete Order Point'><span class='glyphicon glyphicon-remove'></span></a>

									</td>
								<?php } ?>
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
	<?php
	}
	function padLeft($input){
	 return str_pad($input, 5, "0", STR_PAD_LEFT);
	}

	function salesPaginate($args, $cid) {
		// pages,
		$page = new Pagination(new Sales());
		$page->setCompanyId($cid);
		$page->setPageNum($args);
		$page->paginate();
	}

	function serviceItemLog($args, $cid){
	// pages,
		$user = new User();
		$service_req = new Item_service_request();

		$branch_id = Input::get('b');
		$user_id = Input::get('user_id');
		$member_id = Input::get('member_id');
		$service_type = Input::get('service_type');
		$service_type_2 = Input::get('service_type_2');
		$date_from = Input::get('date_from');
		$date_to = Input::get('date_to');
		$technician_id = Input::get('technician_id');

		$for_printing = Input::get('for_printing');
		if($for_printing){
			include_once "../admin/includes/print_header.php";
		?>
			<h3 class='text-center text-danger'>For Service Print Out</h3>
		<?php
			}
		?>
				<div id="no-more-tables">
							<table class='table'>
								<thead>
								<tr>
									<th>Id</th>
									<th>Branch</th>
									<th>User</th>
									<th>Member</th>
									<th>Date Created</th>
									<th></th>
									<th></th>
								</tr>
								</thead>
								<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 20;
						$hide_for_printing = "";
						if($for_printing){
							$limit = 1000;
							$hide_for_printing = "display:none;";

						}

						$countRecord = $service_req->countRecord($cid,$branch_id,$user_id,$member_id,$service_type,$date_from,$date_to,$technician_id,$service_type_2);
						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_sales = $service_req->get_record($cid, $start, $limit,$branch_id,$user_id,$member_id,$service_type,$date_from,$date_to,$technician_id,$service_type_2);
						getpagenavigation($page, $total_pages, $limit, $stages);

						if($company_sales) {
								$arr_status = [
								'', // 0
								'Repairing', // 1
								'Good',// 2
								'Repair with warranty',  // 3
								'Repair without warranty', // 4
								'Replacement(Junk)', // 5
								'Replacement(Surplus)', // 6
								'Change Item(Junk)',// 7
								'Change Item(Surplus)', // 8
								'Cancelled', // 9
								'Scheduled', // 10
								'Received', // 11
								'Repairing', // 12
								'Installing', // 13
							];

							$primaryStatus = ['','Pending','For Evaluation','For Payment/Credit','Processed'];
							foreach($company_sales as $item) {
								$allstatus = $service_req->getStatuses($item->id);
								$lblstats = "";
								$lblPrimaryStatus="";
								if(isset($primaryStatus[$item->status])){
								$lblPrimaryStatus = "<small class='text-danger span-block'> " . $primaryStatus[$item->status] . "</small>";
								} else {
								$lblPrimaryStatus = "<small class='text-danger span-block'> Cancelled</small>";
								}
								if(count($allstatus)){
									foreach($allstatus as $ind_stat){
									$lblstats .= $arr_status[$ind_stat->status]. ", ";
									}
									$lblstats = rtrim($lblstats,", ");
								}
								if($item->member_id){
									$mem =   escape($item->mln . ", " . $item->mfn . " " . $item->mmn);
								} else {
									$mem = 'Not available';
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
									<td style='border-top:1px solid #ccc;' data-title="Id"><?php echo escape($item->id) . $lblPrimaryStatus; ?></td>
									<td style='border-top:1px solid #ccc;' data-title="Branch"><?php echo escape($item->branch_name); ?></td>
									<td style='border-top:1px solid #ccc;' data-title="User"><?php echo escape($item->lastname . ", " . $item->firstname . " " . $item->middlename); ?></td>
									<td style='border-top:1px solid #ccc;' data-title="Member">
									<?php echo $mem; ?>
									<strong class='span-block text-danger'><?php echo $item->service_type_name; ?></strong>
									</td>
									<td style='border-top:1px solid #ccc;' data-title="Created"><?php echo date('m/d/Y',$item->created); ?></td>

									<td style='border-top:1px solid #ccc;' data-title="Status" class='text-danger'><?php //echo escape($lblstats); ?></td>
									<td style='border-top:1px solid #ccc;'>
										<button style='<?php echo $hide_for_printing; ?>' data-id='<?php echo  escape(Encryption::encrypt_decrypt('encrypt',$item->id)); ?>' class='btn btn-sm btn-default btnDetails'>Details</button>
											<?php if($user->hasPermission('item_service_rem')){
																?>
													<button data-member_id='<?php echo escape($item->member_id); ?>' data-id='<?php echo escape(Encryption::encrypt_decrypt('encrypt', $item->id)); ?>' class='btn btn-sm btn-default btnAddRemarks'>
														<i class='fa fa-plus'></i> Add Remarks
													</button>
																<?php
											} ?>
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
	<?php
	}

	function technicianLog($args, $cid){
	// pages,

		$user = new User();
		$service_req = new Item_service_request();
		$technician_id = Input::get('technician_id');
		$status = Input::get('status');
		$cid = $user->data()->company_id;

		?>
			<div id="no-more-tables">
				<table class='table'>
					<thead>
					<tr>
						<th>Id</th>
						<th>Technician</th>
						<th>Branch</th>
						<th>User</th>
						<th>Member</th>
						<th>Date Created</th>
						<th></th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 20;


						$countRecord = $service_req->countTechRecord($cid,$technician_id,$status);
						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_sales = $service_req->get_technician_record($cid, $start, $limit,$technician_id,$status);
						getpagenavigation($page, $total_pages, $limit, $stages);

						if($company_sales) {
								$arr_status = [
								'', // 0
								'Repairing', // 1
								'Good',// 2
								'Repair with warranty',  // 3
								'Repair without warranty', // 4
								'Replacement(Junk)', // 5
								'Replacement(Surplus)', // 6
								'Change Item(Junk)',// 7
								'Change Item(Surplus)', // 8
								'Cancelled', // 9
								'Scheduled', // 10
								'Received', // 11
								'Repairing', // 12
								'Installing', // 13
							];
							$primaryStatus = ['','Pending','For Evaluation','For Payment/Credit','Processed'];

							foreach($company_sales as $item) {

								$allstatus = $service_req->getStatuses($item->id);
								$lblstats = "";
								$lblPrimaryStatus="";
								if(isset($primaryStatus[$item->status])){
								$lblPrimaryStatus = "<small class='text-danger span-block'> " . $primaryStatus[$item->status] . "</small>";
								} else {
								$lblPrimaryStatus = "<small class='text-danger span-block'> Cancelled</small>";
								}
								if(count($allstatus)){
									foreach($allstatus as $ind_stat){
									$lblstats .= $arr_status[$ind_stat->status]. ", ";
									}
									$lblstats = rtrim($lblstats,", ");
								}
								if($item->member_id){
									$mem =   escape($item->mln);
								} else {
									$mem = 'Not available';
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
									<td style='border-top:1px solid #ccc;' data-title="Id">
									<?php echo escape($item->id) . $lblPrimaryStatus; ?>
									</td>
									<td style='border-top:1px solid #ccc;' data-title="Technician">
									<?php echo escape($item->technician_name); ?>
									</td>
									<td style='border-top:1px solid #ccc;' data-title="Branch">
									<?php echo escape($item->branch_name); ?>
									</td>
									<td style='border-top:1px solid #ccc;' data-title="User">
										<?php echo escape($item->lastname . ", " . $item->firstname . " " . $item->middlename); ?>
										</td>
									<td style='border-top:1px solid #ccc;' data-title="Member">
									<?php echo $mem; ?>
									</td>
									<td style='border-top:1px solid #ccc;' data-title="Created"><?php echo date('m/d/Y',$item->created); ?></td>
									<td style='border-top:1px solid #ccc;' data-title="Status" class='text-danger'><?php //echo escape($lblstats); ?></td>
									<td style='border-top:1px solid #ccc;'>

										<button  data-id='<?php echo  escape(Encryption::encrypt_decrypt('encrypt',$item->id)); ?>' class='btn btn-sm btn-default btnDetails'>Details</button>
										<button  data-id='<?php echo  escape(Encryption::encrypt_decrypt('encrypt',$item->id)); ?>' class='btn btn-sm btn-default btnTimelogShow'>Timelog</button>

									</td>

								</tr>
								<tr>
									<td colspan="8">
									<?php

									$ref_table= 'service';

									$rem_list = new Remarks_list();
									$user = new User();
									$remarks_list  = $rem_list->getServices($item->id,$ref_table,$user->data()->company_id);
									if($remarks_list){
										?>
										<ul class="list-group">
											<li class='list-group-item active text-danger'><strong>Remarks</strong></li>
											<?php foreach($remarks_list as $rl){
												?>
												<li class="list-group-item">
												<strong><?php echo ucwords($rl->firstname . " " . $rl->lastname); ?></strong>
												<span class='span-block text-danger'>
													<?php echo $rl->remarks; ?>
													</span>
												<small class='text-success'>
													<?php echo date('F d, Y H:i:s A',$rl->created); ?>
												</small>
												</li>
												<?php
											}
											?>
										</ul>
										<?php

									} else {
										?>
											<p class='text-danger'><strong>No remarks</strong></p>
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
	<?php
	}
	function warrantyPaginate($args, $cid) {
		// pages,

		$user = new User();
		$sales = new Sales();
		$search = Input::get('s');
		$b = Input::get('branch_id');

		?>

		<div id="no-more-tables">
			<div class="table-responsive">
				<table class='table' id='tblSales'>
					<thead>
					<tr>
						<TH >Invoice</TH>
						<TH >Dr</TH>
						<TH>Sr</TH>
						<TH>Item Code</TH>
						<TH class='text-right'>Price</TH>
						<TH class='text-right'>Qty</TH>
						<TH class='text-right'>Discount</TH>
						<TH class='text-right'>Total</TH>
						<TH>Warranty Start</TH>
						<th>Warranty Expiration</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 20;
						$countRecord = $sales->countRecordWarranty($cid, $search, $b);
						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int) $page;

						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_sales = $sales->get_warranty_record($cid, $start, $limit, $search, $b);
						getpagenavigation($page, $total_pages, $limit, $stages);

						if($company_sales) {
							$prevpid = 0;
							foreach($company_sales as $s) {
								$cashier = new User($s->cashier_id);

								$sss = new Sales();
								$p_length = $sss->countPaymentLength($s->payment_id, $start, $limit);

								if($prevpid != $s->payment_id) {
									$bordertop = "style='border-top:1px solid #ccc;'";
								} else {
									$bordertop = '';
								}

								$ind_adjustment = 0;

								if($s->adjustment){
								    $ind_adjustment = $s->adjustment / $s->qtys;
								}

								$warranty_start = date('m/d/Y',$s->sold_date);
								$warranty_end =  date('m/d/Y',strtotime($s->dueWarranty));
								if(Configuration::thisCompany('vitalite')){

									if($warranty_start){
										$warranty_start =  date('m/d/Y',$s->is_scheduled);
										$warranty_end =  date('m/d/Y',strtotime($s->dueWarrantyWH));
									} else {
										$warranty_start = "Not delivered yet";
										$warranty_end = "N/A";
									}
								}


								?>
								<tr <?php echo $bordertop; ?> >
									<td data-title="Invoice">
										<strong><?php echo ($s->invoice) ? escape($s->invoice) : "No invoice"; ?></strong>
									</td>
									<td data-title="Dr">
										<strong><?php echo ($s->dr) ? escape($s->dr) : "No Dr" ?></strong></td>
										<td data-title="Sr">
										<strong><?php echo ($s->sr) ? escape($s->sr) : "No Sr" ?></strong></td>
									<td data-title="Item"><?php echo escape($s->item_code) . "<br><small class='text-danger'>" . escape($s->description) . "</small>"; ?></td>
									<td data-title="Price" class='text-right'><?php echo escape(number_format(($s->price+$ind_adjustment), 2)); ?>
									</td>
									<td data-title="Quantity" class='text-right'><?php echo formatQuantity($s->qtys) ?></td>
									<td data-title="Discount" class='text-right'><?php echo escape(number_format($s->discount+ $s->store_discount, 2)) ?></td>
									<td data-title="Total" class='text-right'>
										<strong><?php echo escape(number_format((($s->qtys * $s->price) + $s->adjustment +  $s->member_adjustment) - ($s->discount + $s->store_discount), 2)) ?></strong>
									</td>
									<td data-title="Date"><?php echo escape($warranty_start); ?><br></td>
									<td data-title="Warranty Expiration">
									<?php echo escape($warranty_end); ?>
									</td>
									<td

										>
										<?php
											if($prevpid != $s->payment_id) {
												?>
												<button title='Details' data-payment_id='<?php echo $s->payment_id ?>' class='btn btn-default paymentDetails'>
													<i class='fa fa-list'></i></button>
												<?php
												$prevpid = $s->payment_id;
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
	}

	function membersTransactionPaginate($args, $cid) {
		// pages,
		$user = new User();
		$sales = new Sales();
		$search = addslashes(Input::get('search'));
		$b = Input::get('b');
		$t = Input::get('t');
		$type = Input::get('type');
		$m = Input::get('mem_id');

		?>
		<div id='no-more-tables'>
		<table class='table' id='tblSales'>
						<thead>
						<tr>
							<TH title='Sort by Member' data-sort=' order by m.lastname ' class='page_sortby'><?php echo MEMBER_LABEL; ?></TH>
							<TH title='Sort by invoice' data-sort=' order by IF (IFNULL(s.invoice,0) = 0, 1, 0), s.invoice * 1 ' class='page_sortby'><?php echo INVOICE_LABEL ; ?></TH>
							<TH title='Sort by dr' data-sort=' order by IF (IFNULL(s.dr,0) = 0, 1, 0), s.dr * 1 ' class='page_sortby'><?php echo DR_LABEL ; ?></TH>
							<TH title='Sort by sr' data-sort=' order by IF (IFNULL(s.sr,0) = 0, 1, 0), s.sr * 1 ' class='page_sortby'><?php echo "SR" ; ?></TH>
							<TH title='Sort by sr' data-sort=' order by IF (IFNULL(s.ir,0) = 0, 1, 0), s.ir * 1 ' class='page_sortby'><?php echo PR_LABEL ; ?></TH>
							<?php if(Configuration::getValue('has_sv') == 1){
								?>
								<TH title='Sort by sv' data-sort=' order by IF (IFNULL(s.sv,0) = 0, 1, 0), s.sv * 1 ' class='page_sortby'><?php echo "SV"; ?></TH>
								<?php
							}?>


							<TH title='Sort by item' data-sort='order by i.item_code ' class='page_sortby'>Item Code</TH>
							<TH title='Sort by price' data-sort='order by pr.price ' class='page_sortby text-right'>Price</TH>
							<TH title='Sort by quantity' data-sort='order by s.qtys ' class='page_sortby text-right'>Qty</TH>
							<TH class='text-right'>Adjustment</TH>
							<TH class='text-right'>Adjusted</TH>
							<TH title='Sort by total' data-sort='order by ((s.qtys * price)-s.discount) ' class='page_sortby text-right'>Total</TH>
							<TH title='Sort by quantity' data-sort='order by s.sold_date ' class='page_sortby'>Date sold</TH>
							<th></th>
						</tr>
						</thead>
						<tbody>
		<?php
			//$targetpage = "paging.php";
			$limit = 20;

			$countRecord = $sales->countRecord($cid, $search, $b, $t, $m, $type);


			$total_pages = $countRecord->cnt;

			$stages = 3;
			$page = ($args);
			$page = (int)$page;
			if($page) {
				$start = ($page - 1) * $limit;
			} else {
				$start = 0;
			}

			$company_sales = $sales->get_sales_record($cid, $start, $limit, $search, $b, $t, $m, $type);
			getpagenavigation($page, $total_pages, $limit, $stages);

			if($company_sales) {
				$prevpid = 0;
								$wh_pickup_arr = ['For deliver','For Pick up','Cashier Transaction'];
								$is_service = false;
								foreach($company_sales as $s) {

									if($s->qtys == 0) continue;

									$cashier = new User($s->cashier_id);

									//$sss = new Sales();
									//$p_length = $sss->countPaymentLength($s->payment_id, $start, $limit);
									$wh_label = "";
									$member_label = "";
									$pickup_label = "";
									$branch_label = "";
									$is_service_label = "";
									$remarks_row = "";

									if($prevpid != $s->payment_id) {
										$is_service_label = "<span class='span-block label label-primary'>Sales</span>";
										$is_service = false;
										if($s->is_service == 1 || $s->from_service != 0){
											$is_service_label = "<span class='span-block label label-danger'>Service</span>";
											$is_service = true;
										}
										$bordertop = "style='border-top:1px solid #ccc;'";
										if($s->wh_id){
											$wh_label = "<span class='span-block text-danger'>Order # $s->wh_id</span>";
											$pickup_label = "<span class='span-block text-danger'>" .$wh_pickup_arr[$s->for_pickup]."</span>";
										}

										$member_label = $s->member_name;
										$branch_label = $s->branch_name;
										if(isset($s->remarks) && $s->remarks){
											$remarks_row = $s->remarks;
										}
										if($s->wh_remarks){
											$remarks_row .= " " . $s->wh_remarks;
										}
										if($remarks_row){
											$remarks_row = "Order # ".$s->wh_id . " Remarks: " .  $remarks_row;
										}

										if($s->cr_number){
											$cr_number = "<br>CR: " . $s->cr_number;
										} else {
											$cr_number = "<br>CR: N/A";
										}

									} else {
										$cr_number='';
										$bordertop = '';
										$wh_label="";
										$member_label ="";
										$pickup_label = "";
									}
									$ind_adjustment = 0;
									if($s->adjustment){
										$ind_adjustment = $s->adjustment / $s->qtys;
									}

									// add sales


									if(Configuration::thisCompany('pw')){
										//$item_id = 119; //tochange

									}else if(Configuration::thisCompany('vitalite')) {
										$item_id = 589; //tochange
									} else {
										//die("You are not allowed to used this.");
										$item_id = 589; //tochange
									}

									//if($s->item_id == $item_id) continue;

									if($remarks_row){
										echo "<tr class='bg-warning'><td colspan='14' style='border-top:1px solid #ccc;'>$remarks_row</td></tr>";
									}

									$adjusted_date = '';
								/*	if($s->member_adjustment){
										$memadj = new Member_term();
										$member_adjustment_data = $memadj->getAdjustmentMember($s->member_id,$s->item_id);

										if($member_adjustment_data){
											$adjusted_date = date('m/d/y',$member_adjustment_data->created);
										}
									} */


									?>
									<tr <?php echo $bordertop; ?> >
										<td data-title="<?php echo MEMBER_LABEL; ?>">
											<?php echo capitalize($member_label); ?>
											<?php echo $wh_label . $pickup_label; ?>
											<?php echo $cr_number;?>
											<small class='text-danger span-block'><?php echo $branch_label; ?></small>
											<?php echo $is_service_label; ?>
										</td>
										<td data-title="Invoice">
											<strong>
												<?php echo ($s->invoice) ? escape($s->pref_inv.padLeft($s->invoice).$s->suf_inv) : "<i class='fa fa-ban'></i>"; ?>
											</strong>
										</td>
										<td data-title="Dr">
											<strong><?php echo ($s->dr) ? escape($s->pref_dr.padLeft($s->dr).$s->suf_dr) : "<i class='fa fa-ban'></i>" ?></strong></td>

										<td data-title="Sr">
											<strong><?php echo ($s->sr) ? escape(padLeft($s->sr)) : "<i class='fa fa-ban'></i>" ?></strong>
										</td>

										<td data-title="PR">
											<strong><?php echo ($s->ir) ? escape($s->pref_ir.padLeft($s->ir).$s->suf_ir) : "<i class='fa fa-ban'></i>" ?></strong>
										</td>
										<?php if(Configuration::getValue('has_sv') == 1){
											?>
										<td data-title="SV">
											<strong><?php echo ($s->sv) ? escape(padLeft($s->sv)) : "<i class='fa fa-ban'></i>" ?></strong>
										</td>
										<?php } ?>
										<td data-title="Item">
											<?php
												if($s->item_id == $item_id && $is_service){
												?>
													Service Sales
												<?php
												} else if($s->item_id == $item_id && !$is_service){
													?>
													Main Sales
													<?php
												} else {
													?>
													<?php echo escape($s->item_code) . "<br><small class='text-danger'>" . escape($s->description) . "</small>"; ?>
													<?php
												}
												$total_current = (($s->qtys * $s->price) + $s->adjustment + $s->member_adjustment) - ($s->discount + $s->store_discount);
											?>
										</td>
										<td data-title="Price" class='text-right'><?php echo escape(number_format(($s->price+$ind_adjustment), 2)); ?>
										</td>
										<td data-title="Quantity" class='text-right'><?php echo formatQuantity($s->qtys) ?></td>
										<td data-title="Adjustment" class='text-right'><?php echo escape(number_format($s->member_adjustment, 2)) ?></td>
										<td data-title="Adjusted" class='text-right'>
											<?php echo escape(number_format($total_current/$s->qtys, 2)) ?>
											<small class='span-blcok text-danger'>
												<?php
													if($adjusted_date){
														echo "(" .$adjusted_date . ")";
													}
												?>
											</small>
										</td>
										<td data-title="Total" class='text-right'>
											<strong><?php echo escape(number_format($total_current, 2)) ?></strong>
										</td>
										<td data-title="Date"><?php echo escape(date('m/d/Y ', $s->sold_date)); ?></td>
										<td class='text-left'>
											<?php
												if($prevpid != $s->payment_id) {
													?>
														<button title='Details'
															data-payment_id='<?php echo $s->payment_id ?>'
															class='btn btn-default btn-sm btn-margin btn-fixed-width paymentDetails'>
															<i class='fa fa-list'></i> Payment
													    </button>
													<?php
													$prevpid = $s->payment_id;
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
	<?php
	}

	function transferPaginate($args, $cid) {
		$user = new User();
		$tcls = new Transfer_inventory_mon();
		$status = Input::get('status');
		$all_branch = $user->hasPermission('inventory_all');
		$branch_id = $user->data()->branch_id;
		if($all_branch){
		$branch_id = 0;
		}
		?>
		<div id="no-more-tables">
		<table class='table' id='tblBranches'>
			<thead>
			<tr>
				<TH>Id</TH>
				<TH>Branch</TH>
				<TH>Data Created</TH>
				<th>Remarks</th>
				<th>Details</th>
			</tr>
			</thead>
			<tbody>
			<?php
				$limit = 30;
				$countRecord = $tcls->countRecord($cid,$branch_id,$status);

				$total_pages = $countRecord->cnt;

				$stages = 3;
				$page = ($args);
				$page = (int)$page;
				if($page) {
					$start = ($page - 1) * $limit;
				} else {
					$start = 0;
				}

				$company_inv = $tcls->get_sales_record($cid, $start, $limit,$branch_id,$status);
				getpagenavigation($page, $total_pages, $limit, $stages);

				if($company_inv) {

					foreach($company_inv as $pt) {
						?>
						<tr>
							<td data-title='Id'><?php echo escape($pt->id); ?></td>
							<td data-title='Branch'><?php echo escape($pt->name); ?></td>
							<td data-title='Create'>
							<?php echo escape(date('m/d/Y H:i:s A', $pt->created)); ?>

							</td>
							<td data-title='Remarks'>
								<?php
									echo escape($pt->from_where);
									if($pt->from_where == "From Order") {
										if($pt->branch_from) {
											echo "<br><small class='text-danger'>Branch: " . escape($pt->name2) . "</small>";
										}
										if($pt->supplier_id) {
											echo "<br><small class='text-danger'>Supplier: " . escape($pt->supname) . "</small>";
										}
									}
								?>

								<?php
									if($pt->remarks){
										echo "<strong class='span span-block'>".$pt->remarks."</strong>";
									}
								?>
								</td>
							<td>
								<button data-transfer_id='<?php echo escape($pt->id); ?>' class='btn btn-default btnDetails'><span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>Details</span></button>
								<?php if ($pt->from_where == "From Order" || $pt->from_where == "From transfer"): ?>
								<button style='margin-left:3px'  data-transfer_id='<?php echo escape($pt->id); ?>' class='btn btn-default btnPrint' ><span class='glyphicon glyphicon-print'></span> <span class='hidden-xs'>Print</span></button>
								<?php endif; ?>


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
	<?php
	}
	//whOrdersPaginate
	function whOrdersPaginate($args, $cid) {
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

		?>

		<div id="no-more-tables">
			<div class="table-responsive">
				<table class='table' id='tblSales' style='font-size:11px;'>
					<thead>
					<tr>
						<TH>ID</TH>
						<TH>Branch</TH>
						<th>Request by</th>
						<TH>Member</TH>
						<TH>Created At</TH>
						<TH>Approved Date</TH>
						<th>Delivery Schedule</th>
						<th>Remarks</th>
						<TH>Status</TH>
						<th></th>

					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 30;
						$is_pickup=0;
						if($status == -4){
							$status = 4;
							$is_pickup = 1;
						}
						$countRecord = $order->countRecord($cid, $search, $b,$m,$status,$user_id,$from,$to,$is_pickup);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_inv = $order->get_record($cid, $start, $limit, $search, $b,$m,$status,$user_id,$from,$to,$is_pickup);
						getpagenavigation($page, $total_pages, $limit, $stages);
						if($company_inv) {
							$invProduct = new Product();
							$arrType = ['','For approval','Shipping','Warehouse','Deliveries','Declined'];
							foreach($company_inv as $s) {
								if($s->is_scheduled){
									$sched = date('m/d/Y',$s->is_scheduled);
								} else {
									$sched = "<i class='fa fa-ban'></i>";
								}
								$lbl_transaction = "";
								$lbl_cancel = "";
								if($s->cancel_remarks){
									$lbl_cancel = "Cancellation remarks: " . $s->cancel_remarks;
								}
								if($s->invoice){
									$lbl_transaction .= "<span class='span-block'>".INVOICE_LABEL.": <span class='text-danger'>$s->invoice</span></span>";
								}
								if($s->dr){
									$lbl_transaction .= "<span class='span-block'>".DR_LABEL.": <span class='text-danger'>$s->dr</span></span>";
								}
								if($s->pr){
									$lbl_transaction .= "<span class='span-block'>".DR_LABEL.": <span class='text-danger'>$s->pr</span></span>";
								}
									if($is_pickup == 1){
									$lblType = "Pick up";
									} else {
									$lblType = $arrType[$s->status];
									}

									if($s->mln){
									$des = $s->mln;
									} else {
									$des = $s->to_branch_name;
									}
									if($s->approved_date){
										$dt_approve = 	date('m/d/Y',$s->approved_date);
									} else {
										$dt_approve = "<i class='fa fa-ban'></i>";
									}

								?>
								<tr>
									<td style='border-top:1px solid #ccc;' data-title="ID"><strong><?php echo $s->id?></strong></td>
									<td style='border-top:1px solid #ccc;' data-title="Branch">
									<?php echo escape($s->branch_name)?>
									<span class='span-block'><?php echo $s->sales_type_name; ?></span>
									</td>
									<td style='border-top:1px solid #ccc;'  data-title="Request by" class='text-muted'><?php echo escape(ucwords($s->lastname . ", " . $s->firstname . " " . $s->middlename))?></td>
									<td style='border-top:1px solid #ccc;' data-title="Member">
									<?php echo escape(ucwords($des))?>
									<?php
										if(Configuration::thisCompany('avision')){
											if($s->rebate != '0.00'){
												echo "<div class='text-info'>REBATE: $s->rebate</div>";
											}
										}
									?>
									</td>
									<td style='border-top:1px solid #ccc;'  data-title="Created at"><?php echo date('m/d/Y',$s->created); ?></td>
									<td style='border-top:1px solid #ccc;'  data-title="Created at"><?php echo $dt_approve; ?></td>
									<td style='border-top:1px solid #ccc;'  data-title="Schedule" class='text-danger'><?php echo $sched; ?></td>
									<td style='border-top:1px solid #ccc;'  data-title="Remarks">
									<?php echo ($s->remarks) ? escape($s->remarks) : "No remarks"; ?>
										<small class='span-block'><?php echo $lbl_cancel; ?></small>
										
										<span class='span-block'>
										<a href="#" data-id='<?php echo $s->id; ?>' data-sales_type='<?php echo $s->gen_sales_type; ?>' data-remarks='<?php echo $s->remarks; ?>' data-received_date='<?php echo ($s->received_date) ? date('m/d/Y',$s->received_date) : ''; ?>' class='btn btn-default btn-sm showUpdateRemarks'><i class='fa fa-pencil'></i></a>
</span>
									</td>
									<td style='border-top:1px solid #ccc;'  data-title="Status"><?php echo escape($lblType)?></td>

									<td style='border-top:1px solid #ccc;'  data-title="Details"><?php echo $lbl_transaction ?></td>
									<td style='border-top:1px solid #ccc;'  data-title="">
									<button title='Details' data-payment_id='<?php echo $s->payment_id ?>' class='btn btn-default btn-sm btn-margin btn-fixed-width paymentDetails'>
														<i class='fa fa-list'></i> Payment </button>
									<button data-id='<?php echo $s->id?>' id='btnDetails' class='btn btn-default btn-sm btn-margin btn-fixed-width'><i class='fa fa-list'></i> Details</button>
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

function inventoryIssuesPaginate($args, $cid) {
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
				<table class='table' id='tblSales'>
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
						$limit = 100;
						$countRecord = $inv->countRecord($cid, $search, $b, $r,$t);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_inv = $inv->get_sales_record($cid, $start, $limit, $search, $b, $r,$t);
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
											echo formatQuantity($s->qty) . " <sub class='text-danger'>".strtolower($s->unit_name)."</sub>";
										 ?>
									</strong>
									</td>
									<td>
										<?php if($user->hasPermission('manage_issues')){
										?>
										<button
											data-item_id="<?php echo $s->item_id?>"
											data-item_code="<?php echo $s->item_code; ?>"
											data-description="<?php echo $s->description; ?>"
											data-rack_id="<?php echo $s->rack_id; ?>"
											data-qty="<?php echo $s->qty; ?>"
											data-status="<?php echo $s->status; ?>"
											data-branch_id="<?php echo $s->branch_id; ?>"
											class='btn btn-default btnConvert'>
											Convert
										</button>
										<?php
										} ?>

										<?php if($withAttac) {
										?>
										<button
											data-item_id="<?php echo $s->item_id?>"
											data-rack_id="<?php echo $s->rack_id; ?>"

											class='btn btn-default btnAtt'>
											Attachment
											</button>
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
				<?php //echo number_format($total,2); ?>
			</div>
		</div>
	<?php
	}
	function forReleasingPaging($args, $cid) {
		// pages,
		$user = new User();
		$forReleasing = new Releasing();
		$search = Input::get('search');

		?>

		<div id="no-more-tables">
			<div class="table-responsive">
				<table class='table table-bordered' id='tblSales'>
					<thead>
					<tr>
						<TH>Invoice/Dr</TH>
						<th>Item</th>
						<TH>Qty</TH>
						<TH>Racking</TH>

					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 100;
						$countRecord = $forReleasing->countRecord($cid,$search);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_inv = $forReleasing->get_record($cid, $start, $limit,$search);

						getpagenavigation($page, $total_pages, $limit, $stages);
						if($company_inv) {
							$prevpay = 0;
							foreach($company_inv as $s) {

								$racking = json_decode($s->racking);
								$racklbl = "";
								if(count($racking) > 0){
								foreach($racking as $rack){
								$racklbl .= "<p>".$rack->rack." : ".$rack->qty."</p>";
								}
								}
								$lbl = "";
								$bordertop = "";
								if($prevpay != $s->payment_id){
									if($s->invoice){
									$lbl .= "<span style='display:block;'>Invoice #".$s->invoice."</span>";
									}
									if($s->dr){
									$lbl .= "<span style='display:block;'>DR #".$s->dr."</span>";
									}
									if($s->ir){
									$lbl .= "<span style='display:block;'>PR #".$s->ir."</span>";
									}
									$bordertop = "border-top:1px solid #ccc;";
								}
								$prevpay = $s->payment_id;

								?>
								<tr>
									<td style='<?php echo $bordertop; ?>' data-title="Ctrl #"><?php echo $lbl; ?></td>
									<td style='<?php echo $bordertop; ?>' data-title='Item' class='text-danger''><?php echo $s->description; ?></td>
									<td style='<?php echo $bordertop; ?>' data-title="Qty" class='text-danger'><?php echo "<strong>" . escape($s->qty)."</strong>"; ?></td>
									<td style='<?php echo $bordertop; ?>' data-title="Racks">
									<?php echo $racklbl ?>
									<br>
									<?php if($s->has_serial == 1){
										?>
										<button  data-item_id="<?php echo $s->item_id; ?>" data-payment_id="<?php echo $s->payment_id; ?>"  data-qty="<?php echo $s->qty; ?>" class='btn btn-sm btnAddSerial'><i class='fa fa-list'></i></button>
										<?php

									}?>
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
	}

	function serviceAttendancePaginate($args, $cid){

		$service_attendance = new Service_attendance();
		$dt = Input::get("dt");

		?>


					<?php
						//$targetpage = "paging.php";
						$limit = 200;
						$countRecord = $service_attendance->countRecord($dt);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_inv = $service_attendance->get_record($start, $limit,$dt);
						getpagenavigation($page, $total_pages, $limit, $stages);
						if($company_inv) {
							?>
							<div id="no-more-tables">
							<div class="table-responsive">
								<table class='bordered highlight' id='tblSales'> <!-- Materialize css -->
									<thead>
									<tr>
										<th>Name</th>
										<th>Time In</th>
										<th>Time Out</th>
										<th>Service consumed</th>
										<th>Length of Stay</th>
									</tr>
									</thead>
									<tbody>
							<?php
							foreach($company_inv as $s) {
								$out = ($s->time_out) ? date('m/d/Y H:i:s A',$s->time_out) : "<i class='material-icons'>not_interested</i>";
								$diff = "N/A";
								$service_con ="";
								if($s->time_out){
									$diff = getTimeDiff($s->time_out - $s->time_in);
									$o_his = new Offered_service_history();
									$list = $o_his->getServiceConsumed($s->id);
									if($list){
										foreach($list as $l){
										$service_con .= "<div class='chip grey  white-text'>".$l->service_name."</div>";
										}
									}
								}
								?>
								<tr>
									<td data-title="Member"><?php echo $s->member_name;?> </td>
									<td data-title="Time In"><?php echo date('m/d/Y H:i:s A',$s->time_in);?> </td>
									<td data-title="Time Out"><?php echo $out; ?> </td>
									<td data-title="Service"><?php echo $service_con; ?></td>
									<td class="red-text"><?php echo $diff; ?></td>
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
							<div style="padding:10px; " class="grey lighten-5 z-depth-2">
								<h3><span class='label label-info'>No Record Found...</span></h3>
							</div>
						<?php
						}
					?>

	<?php
	}
	function inventoryReportPaginate($args, $cid) {
		// pages,
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

		$hide_rack='';
		if($group_by == 1){
			$hide_rack = "display:none;";
		}

		$is_cebu_hiq = Configuration::thisCompany('cebuhiq') ? true : false ;
         // $is_cebu_hiq = true;

      	    $lblOnHand = "On Hand";
			if($is_cebu_hiq){
			$lblOnHand = "System Qty";
			}

		?>

		<div id="no-more-tables">
			<div class="table-responsive">
				<?php echo "<p>Date From: <strong>" . date('F d, Y',$start_date) ."</strong>  Date To: <strong>" . date('F d, Y',$end_date) ."</strong></p>"; ?>
				<table class='table' id='tblSales'>
					<thead>
					<tr>
						<TH>Branch</TH>
						<TH style='<?php echo $hide_rack; ?>'>Rack</TH>
						<TH>Item Code</TH>
						<th>Description</th>
						<?php if($display_type == 1){
						?>
						<TH class='text-right'>Beginning Qty<br><strong>(<?php echo date('F d, Y',$start_date); ?>)</strong></TH>
						<th class='text-right'>Stock In</th>
						<th class='text-right'>Stock Out</th>
						<th class='text-right'>Amend Qty</th>
						<th class='text-right'><?php echo $lblOnHand; ?><br><strong>(<?php echo date('F d, Y',$end_date); ?>)</strong></th>

						<?php

							if($is_cebu_hiq){

						?>
							<th>Actual Qty</th>
						  	<th class='text-right'>Variants</th>
						  	<th>Actual Cost</th>
						  <?php }  // #end cebuhiq ?>
						<?php
						}  // #end display type condition
						?>
						<?php if(date('F d, Y',$end_date) != date('F d, Y') || $display_type == 2){
							?>
							<th class='text-right'>On Hand<br><strong>(<?php echo date('F d, Y'); ?>)</strong></th>
						<?php } ?>

					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 100;
						$countRecord = $inv->countRecordReport($cid, $search,$start_date,$end_date,$b,$r,$group_by,$is_cebu_hiq);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_inv = $inv->get_report_record($cid, $start, $limit, $search,$start_date,$end_date,$b,$r ,$group_by,$is_cebu_hiq);
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
									<td style='border-top:1px solid #ccc;' data-title="Branch"><?php echo escape($s->name) ?></td>
									<td style='<?php echo $hide_rack; ?> border-top:1px solid #ccc;' data-title="Rack" class='text-danger'><?php echo "<strong>" . escape($s->rack)."</strong>"; ?></td>
									<td  style='border-top:1px solid #ccc;'  data-title="Item code"><?php echo escape($s->item_code) ?></td>
									<td  style='border-top:1px solid #ccc;'  data-title="Description" class='text-muted'><?php echo escape($s->description) ?></td>
									<?php if($display_type == 1){ ?>
									<td  style='border-top:1px solid #ccc;'  class='text-right' data-title="Beginning"><?php echo escape(formatQuantity($beg_qty)) ?></td>
									<td  style='border-top:1px solid #ccc;'  class='text-right' data-title="Stock In"><?php echo escape(formatQuantity($s->in_qty)) ?></td>
									<td  style='border-top:1px solid #ccc;'  class='text-right' data-title="Stock Out"><?php echo escape(formatQuantity($s->out_qty)) ?></td>
									<td  style='border-top:1px solid #ccc;'  class='text-right' data-title="Stock Out"><?php echo escape(formatQuantity($amend_qty)) ?></td>
									<td  style='border-top:1px solid #ccc;'  class='text-right'  data-title="On hand"><?php echo escape(formatQuantity($onhand)) ?></td>

									<?php if($is_cebu_hiq){ ?>
										<td  style='border-top:1px solid #ccc;' ><?php echo escape(formatQuantity($s->ending_qty)) ?></td>
										<td  style='border-top:1px solid #ccc;' ><?php echo escape(formatQuantity($s->ending_qty - $onhand)); ?></td>
										<td  style='border-top:1px solid #ccc;' ><?php echo escape(number_format(($s->ending_qty * $s->product_cost),2)); ?></td>
									<?php } ?>
									<?php } // end display two ?>
									<?php
										if(date('F d, Y',$end_date) != date('F d, Y') || $display_type == 2){
									?>
									<td  style='border-top:1px solid #ccc;'  class='text-right'  data-title="On hand"><?php echo escape(formatQuantity($s->qty)) ?></td>
									<?php } ?>

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
	function serialsPaginate($args, $cid) {
		// pages,
		$user = new User();
		$serial = new Serial();
		$search = Input::get('search');
		$branch_id = Input::get('branch_id');
		$dateEnd = Input::get('dateEnd');
		$dateStart = Input::get('dateStart');
		$member_id = Input::get('member_id');
		$item_id = Input::get('item_id');

		$assembly_only = ($user->hasPermission('serial_assembly')) ? 1 : 0;

		?>

		<div id="no-more-tables">
			<div class="table-responsive">
				<table class='table' id='tblForApproval'>
					<thead>
					<tr>
						<TH>Client</TH>
						<TH>Payment ID</TH>
						<TH>Item</TH>
						<TH>Serial No</TH>
						<th>Sold Date</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 50;
						$countRecord = $serial->countRecord($cid, $search,$branch_id,$dateStart,$dateEnd,$member_id,$item_id,$assembly_only);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$serials = $serial->get_record($cid, $start, $limit, $search,$branch_id,$dateStart,$dateEnd,$member_id,$item_id,$assembly_only);

						getpagenavigation($page, $total_pages, $limit, $stages);
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
									<strong>
										<?php echo ($s->member_name) ? $s->member_name : 'None'; ?>
									</strong>
								</td>
								<td data-title='Ref #' ><?php echo $lblInvoice . " " . $lblDr . " " . $lblIr; ?> </td>
								<td data-title='Item'><?php echo $s->item_code; ?><small class='span-block text-danger'><?php echo $s->description; ?></small></td>
								<td data-title='Serial #'><?php echo $s->serial_no; ?></td>
								<td class='text-success' data-title='Sold Date' ><?php echo date('m/d/Y h:s:i A',$s->sold_date); ?></td>
								<td>
									<button style='display:none;' title='Payment Details' data-payment_id='<?php echo $s->payment_id ?>' class='btn btn-default btn-sm paymentDetails '>
										<i class='fa fa-list'></i>
									</button>
								</td>
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
			</div>
		</div>
	<?php
	}
	function expiPaginate($args, $cid) {
		// pages,
		$user = new User();
		$expi = new Addtl_experience();
		$search = Input::get('search');


		?>

		<div id="no-more-tables">
			<div class="table-responsive">
				<table class='table' id='tblSales'>
					<thead>
					<tr>
						<TH><i class='fa fa-user'></i> Member</TH>
						<TH><i class='fa fa-list-alt'></i> Experience</TH>
						<th><i class='fa fa-calendar'></i> Created At</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 50;
						$countRecord = $expi->countRecord($cid, $search);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$serials = $expi->get_record($cid, $start, $limit, $search);

						getpagenavigation($page, $total_pages, $limit, $stages);
						if($serials) {

							foreach($serials as $s) {

								?>
								<tr>
								<td data-title='Member' ><span class='text-danger'><?php echo escape($s->lastname); ?></span> </td>
								<td data-title='Experience' ><strong><?php echo escape($s->exp); ?> </strong></td>
								<td data-title='Created At'><?php echo date('m/d/Y',$s->created); ?></td>
								<td></td>
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
	}
	function inventoryPaginate($args, $cid) {
		// pages,
		$user = new User();
		$inv = new Inventory();
		$search = Input::get('search');
		$b = Input::get('b');
		$r = Input::get('r');
		$si = Input::get('s');
		$txtRack = Input::get('txtRack');
		$category_id = Input::get('category_id');
		$rack_tag_id = Input::get('rack_tag_id');
		$branch_list = "";

		if($b){
			$b = json_decode($b,true);
			foreach($b as $curb){

				$branch_list .= $curb . ",";
			}
			 $branch_list = rtrim($branch_list,',');
			$b = $branch_list;
		}

		$negativeinv = $inv->getNegativeQuantity($cid);
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

		if($negativeinv){
			?>
			<h3>Warning!!! You have negative quantity in your inventory</h3>
			<div id="no-more-tables">
			<div class="table-responsive">
				<table class="table">
					<thead>
					<tr>
						<TH>Branch</TH>
						<TH>Rack</TH>
						<TH>Barcode</TH>
						<TH>Item Code</TH>
						<th>Description</th>
						<th class='text-right'></th>
						<TH class='text-right'>Qty</TH>
					</tr>
					</thead>
					<tbody>
					<?php
						foreach($negativeinv as $neginv){
							?>
							<tr>
								<td><?php echo $neginv->bname ?></td>
								<td><?php echo $neginv->rack ?></td>
								<td><?php echo $neginv->barcode ?></td>
								<td><?php echo $neginv->item_code ?></td>
								<td><?php echo $neginv->description ?></td>
								<td></td>
								<td class='text-right'><?php echo $neginv->qty ?></td>
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
		?>

		<div id="no-more-tables">
			<div class="table-responsive">
				<table class='table' id='tblSales'>
					<thead>
					<tr>
						<TH>Branch</TH>
						<TH>Rack</TH>
						<TH>Barcode</TH>
						<TH>Item Code</TH>
						<th>Description</th>
						<?php if((Configuration::getValue('hide_price_inv') == 1)){ ?>
						<th class='text-right'>Price</th>
						<?php }?>

						<TH class='text-right'>Qty</TH>
						<TH class='text-right'>Critical Order</TH>
					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 100;
						$countRecord = $inv->countRecord($cid, $search, $b, $r, $si,$txtRack,$imploded,$rack_tag_id);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_inv = $inv->get_sales_record($cid, $start, $limit, $search, $b, $r, $si,$txtRack,$imploded,$rack_tag_id);
						getpagenavigation($page, $total_pages, $limit, $stages);
						if($company_inv) {
							$invProduct = new Product();


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
								?>
								<tr>
									<td data-title="Branch"><?php echo escape($s->name) ?></td>
									<td data-title="Rack" class='text-danger'><?php echo "<strong>" . escape($s->rack)."</strong>".$alldis; ?></td>
									<td data-title="Barcode"><?php echo escape($s->barcode) ?></td>
									<td data-title="Item code"><?php echo escape($s->item_code) ?><small class='text-danger span-block'><?php echo escape($s->category_name) ?></small></td>
									<td data-title="Description" class='text-muted'><?php echo escape($s->description) ?></td>
									<?php if((Configuration::getValue('hide_price_inv') == 1)){ ?>
									<td data-title="Price"  class='text-right'><?php echo escape(number_format($price->price, 2)) ?></td>
									<?php }?>
									<td data-title="Quantity" class='text-right' style='padding-right:20px;'>
										<strong>
										<?php
											echo formatQuantity($s->qty) . " <sub class='text-danger'>".strtolower($s->unit_name)."</sub>";
										 ?>
									</strong>
									</td>
									<td data-title="Critical" class='text-right' style='padding-right:20px;'>
										<strong>
										<?php
											$od_point = (isset($s->order_point)) ? $s->order_point : 0;
											echo  "<a data-item_id='{$s->item_id}' data-branch_id='$s->branch_id' href='#' class='btnReorderDetails'>" . formatQuantity($od_point). " <sub class='text-danger'>".strtolower($s->unit_name)."</sub>" . "</a>";
										 ?>
									</strong>
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
	function amendPaginate($args, $cid) {
		// pages,

		$inv = new Inventory_ammend();
		$search = Input::get('search');
		$b = Input::get('b');
		$r = Input::get('r');
		$date_from = Input::get('date_from');
		$date_to = Input::get('date_to');
		?>


		<div id="no-more-tables">
			<div class="table-responsive">
				<table class='table' id='tblForApproval'>
					<thead>
					<tr>
						<TH>Branch</TH>
						<TH>Rack</TH>
						<TH>Item </TH>
						<TH class='text-right'>Qty</TH>
						<TH class='text-right'>Amend Qty</TH>
						<TH>Created At</TH>
						<th>User</th>

					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 100;
						$countRecord = $inv->countRecord($cid, $search, $b, $r,$date_from,$date_to);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
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

							$inv_mon = new Inventory_monitoring();


							foreach($company_inv as $s) {
								// check if there is add found item
								// item_id, rack_id, branch_id, date from ,

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

									<td data-title="Item code"><?php echo escape($s->item_code) ?>

									<small class='text-muted span-block'><?php echo escape($s->description) ?></small>
										<small class='text-danger span-block'><?php echo escape($s->category_name) ?></small>
									</td>


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

	function feedbackPaginate($args, $cid) {

			$feedback = new Feedback();
			$status = Input::get('status');

		?>


					<?php
						//$targetpage = "paging.php";
						$limit = 100;
						$countRecord = $feedback->countRecord($cid,$status);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$feedbacks = $feedback->get_record($cid, $start, $limit,$status);
						getpagenavigation($page, $total_pages, $limit, $stages);
						if($feedbacks) {
							?>
							<div id="no-more-tables">
							<div class="table-responsive">
								<table class='table' id='tblSales'>
									<thead>
									<tr>
										<TH>Id</TH>
										<TH>User</TH>
										<TH>Feedback</TH>
										<TH>Created</TH>
										<TH>Status</TH>
										<TH></TH>

									</tr>
									</thead>
									<tbody>
							<?php
							$arr = ['Queued','Processing','Processed'];
							$user = new User();
							foreach($feedbacks as $s) {

							$link = "<a class='btn btn-default btn-sm' target='_blank' href='".$s->filename."'>Screen shot</a>";
			
								?>
								<tr>
									<td><strong><?php echo $s->id; ?></strong></td>
									<td><?php echo capitalize($s->firstname . " " . $s->lastname); ?></td>
									<td><?php echo $s->feedback; ?></td>
									<td><?php echo date('m/d/Y H:s:i A',$s->created); ?></td>
									<td class='text-danger'><?php  echo $arr[$s->status]; ?></td>
									<td>
									<?php
										echo $link;
										$super_id = Configuration::getSuperAdminId();
										if($super_id && $super_id == $user->data()->id && $s->status != 2){
										?>
											<button data-id='<?php echo $s->id; ?>' class='btn btn-default btn-sm btnProcess'>Process</button>
										<?php
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
							<div class="alert alert-info">No record found.</div>
						<?php
						}
					?>

	<?php
	}
	function inventoryAuditAll($args, $cid) {
		// pages,
		$user = new User();
		$inv = new Inventory();
		$search = Input::get('search');
		$b = Input::get('b');

		?>

		<div id="no-more-tables">
			<div class="table-responsive">
				<table class='table' id='tblAuditAll' style='border: 1px solid #ccc;'>
					<thead>
					<tr>
						<TH>Branch</TH>
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
						$limit = 500;
						$countRecord = $inv->countRecordAudit($cid, $search, $b);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_inv = $inv->get_audit_record($cid, $start, $limit, $search, $b);
						getpagenavigation($page, $total_pages, $limit, $stages);
						if($company_inv) {
							$invProduct = new Product();

							$prev_item = "";
							foreach($company_inv as $s) {
								$item_code = "";
								$barcode='';
								$description ='';
								$category ='';
								$branch = '';
								$border_top = "";
								if($prev_item != $s->barcode){
									$item_code = $s->item_code;
									$barcode = $s->barcode;
									$branch =$s->name;
									$description = $s->description;
									$category = $s->category_name;
									$border_top = "border-top:1px solid #ccc;";
								}
								$prev_item  = $s->barcode;
								$uniqid = uniqid() . $s->item_code;
								$uniqid = md5($uniqid);
								?>
								<tr data-id='<?php echo $uniqid; ?>' data-qty='<?php echo $s->qty; ?>'>
									<td style='<?php echo $border_top; ?>' data-title="Branch"><?php echo escape($branch) ?></td>
									<td style='<?php echo $border_top; ?>' data-title="Barcode"><?php echo escape($barcode) ?></td>
									<td style='<?php echo $border_top; ?>' data-title="Item code"><?php echo escape($item_code) ?><small class='text-danger span-block'><?php echo escape($category) ?></small></td>
									<td style='<?php echo $border_top; ?>' data-title="Description" class='text-muted'><?php echo escape($description) ?></td>
									<td style='<?php echo $border_top; ?>' data-title="Quantity" class='text-right' style='padding-right:20px;'>
										<strong>
											<?php
												echo "<span id='$uniqid'>" . formatQuantity($s->qty) . "</span>" . " <sub class='text-danger'>".strtolower($s->unit_name)."</sub>";
											 ?>
										</strong>
									</td>
									<td style='<?php echo $border_top; ?>' >
									<?php
									$branch = $s->branch_id;
									$item_id = $s->item_id;
									/*$rack = $s->rack_id;


									$qty = $s->qty;
									$item_code = str_replace('"','',$s->item_code);
									$item_code = str_replace("'",'',$item_code);
									$auditid = 0;
									echo "<button   class='btn btn-default' onclick='ammendThis($rack,$branch,$item_id,$qty,\"$item_code\",$auditid,\"\")'>Amend</button> <button    class='btn btn-default'  onclick='confirmThis($rack,$branch,".$item_id.",".$qty.",\"$item_code\",$auditid)'>Confirm</button>";
									*/
									echo "<button data-branch_id='$branch' data-item_id='$item_id' class='btn btn-default btnDetails'>Details</button>";
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
	}
function inventoryLogPaginate($args, $cid) {
		// pages,
		$user = new User();
		$add_batch_inv = new Add_batch_inv();
		$branch_id = Input::get('branch_id');
		$dt_from = Input::get('dt_from');
		$dt_to = Input::get('dt_to');


	?>

		<div id="no-more-tables">
			<div class="table-responsive">
				<table class='table' id='tblSales'>
					<thead>
					<tr>
						<TH>ID</TH>
						<TH>Branch</TH>
						<th>User</th>
						<TH>Supplier</TH>
						<th>Date Created</th>
						<TH>Date Received</TH>
						<th>Other Details</th>
						<th>Status</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 100;
						$countRecord = $add_batch_inv->countRecord($cid,$branch_id,$dt_from,$dt_to);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_inv = $add_batch_inv->get_record($cid, $start, $limit,$branch_id,$dt_from,$dt_to);
						getpagenavigation($page, $total_pages, $limit, $stages);
						if($company_inv) {

							$added_arr = ['Added','Pending','Declined'];
							foreach($company_inv as $s) {
								$sup = ($s->supplier_id) ? $s->supplier_name : "<i class='fa fa-ban'></i>";
								$date_receive = ($s->date_receive) ? date('m/d/Y',$s->date_receive) : "<i class='fa fa-ban'></i>";
								$packing = ($s->packing_list_num) ? $s->packing_list_num : "<i class='fa fa-ban'></i>";
								$ref_num = ($s->ref_num) ? $s->ref_num : "<i class='fa fa-ban'></i>";
								$remarks = ($s->remarks) ? $s->remarks : "<i class='fa fa-ban'></i>";
								?>
								<tr>
									<td data-title="ID"><?php echo escape($s->id) ?></td>
									<td data-title="Branch" class='text-danger'><?php echo $s->branch_name; ?></td>
									<td data-title="User" ><?php echo ucwords($s->firstname . " " . $s->lastname); ?></td>

									<td data-title="Branch" ><?php echo $sup; ?></td>
									<td data-title="Created"><?php echo escape(date('m/d/Y',$s->created)) ?></td>
									<td data-title="Received"><?php echo ($date_receive) ?></td>
									<td data-title="Packing" class='text-muted'>
									<span class='span-block'><strong>Packing: </strong><?php echo $packing ?></span>
									<span class='span-block'><strong>Ref Num: </strong><?php echo $ref_num ?></span>
									<span class='span-block'><strong>Remarks: </strong><?php echo $remarks ?></span>
									</td>

									<td data-title="Status" class='text-muted'>
										<?php echo $added_arr[$s->is_pending]; ?>
									</td>
									<td>
									<button data-id='<?php echo escape($s->id) ?>' class='btn btn-default btnDetails'>Details</button>
									</td>
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
			</div>
		</div>
	<?php
	}

	function inventoryLogDetailsPaginate($args, $cid) {
		// pages,
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
	?>

		<div id="no-more-tables">
			<div class="table-responsive">
				<table class='table' id='tblSales'>
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
						$limit = 100;
						$countRecord = $add_batch_inv->countRecordDetails($cid,$branch_id,$dt_from,$dt_to,$item_id,$imploded);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_inv = $add_batch_inv->get_record_details($cid, $start, $limit,$branch_id,$dt_from,$dt_to,$item_id,$imploded);
						getpagenavigation($page, $total_pages, $limit, $stages);
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
			</div>
		</div>
	<?php
	}

	function unreadNotification($args, $cid) {
		// pages,
		$user = new User();
		$alertcls = new Notification();
		$search = Input::get('search');
		$m = Input::get('member_id');
		$m = ($m) ? $m : 0;

		//$targetpage = "paging.php";
		$limit = 30;
		$countRecord = $alertcls->countRecord($cid, $user->data()->position_id, $user->data()->id, $search, $m);

		$total_pages = isset($countRecord->cnt) ? $countRecord->cnt : 0;
		$stages = 3;
		$page = ($args);
		$page = (int)$page;
		if($page) {
			$start = ($page - 1) * $limit;
		} else {
			$start = 0;
		}
		$company_inv = $alertcls->get_sales_record($cid, $start, $limit, $user->data()->position_id, $user->data()->id, $search, $m);
		getpagenavigation($page, $total_pages, $limit, $stages);
		?>
		<div id="no-more-tables">
			<div class="table-responsive">
				<table class='table' id='tblSales'>
					<thead>
					<tr>
						<th>Item</th>
						<th>Ref #</th>
						<th>Sold Date</th>
						<?php
							if(!$m){
								?>
								<th>Member</th>
								<?php
							}
						?>

						<th>Alert Message</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<?php
						if($company_inv) {

							foreach($company_inv as $det) {
								$dr = '';
								$inv = '';
								$ir = '';
								$sr = '';
								if($det->invoice) {
									$inv = "<span style='display:block'>Invoice# " . $det->invoice . "</span>";
								}
								if($det->dr) {
										$dr = "<span style='display:block'>Dr#" . $det->dr . "</span>";
								}
								if($det->ir) {
										$ir = "<span style='display:block'>Pr#" . $det->ir . "</span>";
								}
								if($det->sr) {
										$sr = "<span style='display:block'>Sr#" . $det->sr . "</span>";
								}
								?>
								<tr>
									<td data-title='Item code'><?php echo escape($det->item_code) . "<br> <small class='text-danger'>" . escape($det->description) . "</small>" ?></td>
									<td data-title='Invoice/Dr'><?php echo $inv . $dr. $sr. $ir; ?></td>
									<td data-title='Sold Date'><?php echo escape(date('m/d/Y', $det->sold_date)) ?></td>
									<?php
										if(!$m){
											?>
											<td data-title='Member'><?php echo escape(ucwords($det->mln . ", " . $det->mfn)); ?></td>
										<?php
										}
									?>

									<td  data-title='Message'><?php echo escape($det->alert_msg); ?></td>
									<td>
										<input type="button" data-item_id='<?php echo $det->item_id; ?>' data-payment_id='<?php echo $det->payment_id; ?>' value='Remarks' class='btn btn-default addrm' />
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
function diagnosisList($args, $cid) {
		// pages,
		$user = new User();
		$med_diag = new Med_diagnosis();
		$search = Input::get('search');
		$type = Input::get('type');
		$m = Input::get('member_id');
		$m = ($m) ? $m : 0;
		$type = ($type) ? $type : 1;
		//$targetpage = "paging.php";
		$limit = 30;
		$countRecord = $med_diag->countRecord($cid, $search,$m,$type);
		$total_pages = isset($countRecord->cnt) ? $countRecord->cnt : 0;
		$stages = 3;
		$page = ($args);
		$page = (int)$page;
		if($page) {
			$start = ($page - 1) * $limit;
		} else {
			$start = 0;
		}
		$company_inv = $med_diag->get_record($cid, $start, $limit, $search,$m,$type);
		getpagenavigation($page, $total_pages, $limit, $stages);
		?>
		<div id="no-more-tables">
			<div class="table-responsive">
				<table class='table' id='tblSales'>
					<thead>
					<tr>
						<th><?php echo ($type == 1) ? 'Doctor' : 'Nurse' ; ?></th>
						<th>Date</th>
						<th>Remarks</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<?php
						if($company_inv) {

							foreach($company_inv as $det) {
								$name = ($type == 1) ? $det->doctor_name : $det->nurse_name;
								?>
								<tr>
									<td style='border-top:1px solid #ccc;' data-title='<?php echo ($type == 1) ? 'Doctor' : 'Nurse' ; ?>'><?php echo escape($name);  ?></td>
									<td style='border-top:1px solid #ccc;' data-title='Created'><?php echo escape(date('m/d/Y',$det->created));  ?></td>
									<td style='border: 1px dotted #ccc;border-top:1px solid #ccc;width:65%;background:#efefef;' data-title='Remarks'><?php echo ($det->remarks);  ?></td>
									<td style='border-top:1px solid #ccc;'><button class='btn btn-danger btn-sm btnDeleteDiagnosis' data-id='<?php echo $det->id; ?>'>Delete</button></td>
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
}

	function itemUsedPaginate($args, $cid) {
		// pages,
		$user = new User();
		$used_cls = new Service_item_use();

		$s = Input::get('s');


		//$targetpage = "paging.php";
		$limit = 30;
		$countRecord = $used_cls->countRecord($cid,$s);
		$total_pages = isset($countRecord->cnt) ? $countRecord->cnt : 0;
		$stages = 3;
		$page = ($args);
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
									<td style='<?php echo $border_top; ?>' data-title='Technician'><?php echo $technician_names;  ?></td>
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
	function userPoints($args, $cid) {
		// pages,
		$user = new User();
		$point_cls = new Point();

		$m = Input::get('member_id');
		$m = ($m) ? $m : 0;
		$s = Input::get('s');
		$s = ($s) ? $s : '';

		//$targetpage = "paging.php";
		$limit = 30;
		$countRecord = $point_cls->countRecord($cid,$m,$s);
		$total_pages = isset($countRecord->cnt) ? $countRecord->cnt : 0;
		$stages = 3;
		$page = ($args);
		$page = (int)$page;
		if($page) {
			$start = ($page - 1) * $limit;
		} else {
			$start = 0;
		}
		$company_inv = $point_cls->get_record($cid, $start, $limit,$m,$s);
		getpagenavigation($page, $total_pages, $limit, $stages);
		?>

					<?php
						if($company_inv) {
							$prev = "";
							$ctrcol = 0;
							foreach($company_inv as $det) {
									$member_name = "";
									if($prev != $det->member_id){
									    $member_name = escape(capitalize($det->lastname));
									    if($prev){
											echo "</tbody></table></div></div></div>";
										}
									}
									if($member_name){
										if($ctrcol % 3 == 0){
											echo "<div class='clearfix'></div>";
										}
										$ctrcol++;

										?>
											<div class='col-md-4'>
											<div class='panel panel-default'>
											<div class="panel-heading">
												<div class="row">
												<div class="col-md-6"><?php echo $member_name; ?> </div>
												<div class="col-md-6 text-right">
													<button data-id="<?php echo $det->member_id; ?>" class='btn btn-default btn-sm btnAddSupplementary'><i class='fa fa-plus'></i></button>
												</div>
												</div>
											</div>
											<div class="panel-body">
											<p class='text-danger'><strong><?php echo $det->pg_name; ?></strong></p>
											<table class='table' id='tblSales'>
											<thead>
											<tr>

												<th>Type</th>
												<th>Points</th>
											</tr>
											</thead>
											<tbody>
										<?php
									}
									$prev = $det->member_id;
								?>
								<tr>
									<td style='border-top:1px solid #ccc;' data-title='Type'><?php echo escape(capitalize($det->point_name));  ?></td>
									<td style='border-top:1px solid #ccc;' data-title='Points'><?php echo escape(number_format($det->points,3));  ?></td>
								</tr>
							<?php
							}
							echo "</tbody></table></div></div></div>";
						} else {
							echo "<div class='alert alert-info'>No record yet.</div>";
						}
	}
	function userPointsLog($args, $cid) {
		// pages,
		$user = new User();
		$point_cls = new Point();

		$filter_point = Input::get('filter_point');
		$m = Input::get('member_id');
		$s = Input::get('s');
		$user_view = Input::get('user_view');
		$m = ($m) ? $m : 0;
		$user_view = ($user_view) ? $user_view : 0;
		if($user_view == 1){
			$m = $user->data()->member_id;
			if(!$m) $m = -1;// dont display anything
		}
		//$targetpage = "paging.php";
		$limit = 50;
		$countRecord = $point_cls->countRecordUserLog($cid,$m,$s,$filter_point);
		$total_pages = isset($countRecord->cnt) ? $countRecord->cnt : 0;
		$stages = 3;
		$page = ($args);
		$page = (int)$page;
		if($page) {
			$start = ($page - 1) * $limit;
		} else {
			$start = 0;
		}
		$company_inv = $point_cls->get_record_user_log($cid, $start, $limit,$m,$s,$filter_point);
		getpagenavigation($page, $total_pages, $limit, $stages);
		?>
		<?php
		if($company_inv) {
		?>
		  <div class="page-header">
  <h4 id="timeline">Log</h4>
  </div>
  <ul class="timeline">
		<?php
			$ctr = 0;
			foreach($company_inv as $det) {
				$from_points = $det->from_points;
				$to_points = $det->to_points;
				$lbl = "primary";
				$icon = "check";
				if($from_points > $to_points) {
					$lbl = "danger";
					$icon = "remove";
				}
				$cls ="";
				if($ctr % 2 != 0){
				$cls = "timeline-inverted";
				}
				$ctr++;
				?>
				<li class='<?php echo  $cls; ?>'>
      <div class="timeline-badge <?php echo $lbl; ?>"><i class="glyphicon glyphicon-<?php echo $icon; ?>"></i></div>
      <div class='timeline-panel'>
        <div class='timeline-heading'>
            <p><i class='fa fa-user'></i><span class='text-primary'> <?php echo escape(capitalize($det->lastname));  ?></span></p>
            <small class='span-block'><span class='h5'><?php echo number_format($from_points,3); ?></span> <span class='h5'><i class='fa fa-long-arrow-right'></i></span> <span class='h5'><?php echo number_format($to_points,3); ?></span></small>
            <small class='span-block'><?php echo escape(capitalize($det->point_name));  ?></small>
            <small class='span-block'><?php echo date('m/d/Y H:i:s A',$det->created); ?></small>
        </div>
        <div class="timeline-body text-danger">
          <?php echo escape(capitalize($det->remarks));  ?>
        </div>
      </div>
    </li>
				<?php
			}
			?>
			</ul>
			<?php
		} else {
		echo "<p>No result found.</p>";
		}
		?>
	<?php
	}
	function pointsLog($args, $cid) {
		// pages,
		$user = new User();
		$point_cls = new Point();
		$s = Input::get('s');
		//$targetpage = "paging.php";
		$limit = 30;
		$countRecord = $point_cls->countRecordPointLog($cid,$s);
		$total_pages = isset($countRecord->cnt) ? $countRecord->cnt : 0;
		$stages = 3;
		$page = ($args);
		$page = (int)$page;
		if($page) {
			$start = ($page - 1) * $limit;
		} else {
			$start = 0;
		}
		$company_inv = $point_cls->get_record_point_log($cid, $start, $limit,$s);
		getpagenavigation($page, $total_pages, $limit, $stages);
		?>
		<div id="no-more-tables">
			<div class="table-responsive">
				<table class='table' id='tblSales'>
					<thead>
					<tr>
						<th>Updated By</th>
						<th>Name</th>
						<th>From Amount</th>
						<th>To Amount</th>
						<th>From Points</th>
						<th>To Points</th>
						<th>Date</th>
					</tr>
					</thead>
					<tbody>
					<?php
						if($company_inv) {

							foreach($company_inv as $det) {

								?>
								<tr>
									<td style='border-top:1px solid #ccc;' data-title='Updated By'><?php echo escape(capitalize($det->lastname . ", " . $det->firstname . " " .$det->middlename));  ?></td>
									<td style='border-top:1px solid #ccc;' data-title='Name'><?php echo escape(capitalize($det->point_name));  ?></td>
									<td style='border-top:1px solid #ccc;' data-title='From Amount'><?php echo escape(number_format($det->from_amount,2));  ?></td>
									<td style='border-top:1px solid #ccc;' data-title='To Amount'><?php echo escape(number_format($det->to_amount,2));  ?></td>
									<td style='border-top:1px solid #ccc;' data-title='From Points'><?php echo escape(number_format($det->from_points,3));  ?></td>
									<td style='border-top:1px solid #ccc;' data-title='To Points'><?php echo escape(number_format($det->to_points,3));  ?></td>
									<td style='border-top:1px solid #ccc;' data-title='Created'><?php echo date('m/d/Y H:s:i A',$det->created);  ?></td>

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
function issuesLogPaginate($args, $cid) {
		// pages,
		$user = new User();
		$inv = new Inventory_issues_monitoring();
		$search = Input::get('search');
		$b = Input::get('b');
		$r = Input::get('r');
		$type = Input::get('type');
		$arrType = ['',DAMAGE_LABEL,MISSING_LABEL,'Disposed',INCOMPLETE_LABEL,OTHER_ISSUE_LABEL];

		?>
		<div id="no-more-tables">
			<div class="table-responsive">
				<table class='table' id='tblSales'>
					<thead>
					<tr>
						<TH>Branch</TH>
						<TH>User</TH>
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
						$limit = 100;
						$countRecord = $inv->countRecord($cid, $search, $b, $r,$type);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
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
	}
	function memberTerms($args, $cid) {
		// pages,
		$user = new User();
		$mem = new Member_term();
		$search = Input::get('search');
		$member_id = Input::get('member_id');
		$status = Input::get('status');
		$sales_type = Input::get('sales_type');
		$branch_id= Input::get('branch_id');
		$user_id= Input::get('user_id');
		?>
		<div id="no-more-tables">
			<div class="table-responsive">

					<?php
						//$targetpage = "paging.php";
						$limit = 100;
						$countRecord = $mem->countRecord($cid, $search,$member_id,$status,$sales_type,$branch_id,$user_id);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_inv = $mem->get_record($cid, $start, $limit, $search,$member_id,$status,$sales_type,$branch_id,$user_id);
						getpagenavigation($page, $total_pages, $limit, $stages);
						if($company_inv) {
							?>
							<table class='table' id='tblTerms'>
							<thead>
							<tr>
							<th></th>
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
								<td>
								<input type="checkbox" value='<?php echo $s->id; ?>'  class='chkApprove'>
</td>
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
									<td data-title="Created"><?php echo date('F d, Y H:i:s A',$s->created);?></td>
									<td data-title="Status"><strong class='text-danger'><?php echo $status[$s->status];?></strong></td>
									<td data-title="Transaction"><strong class='text-danger'><?php echo $ttype[$s->transaction_type];?></strong></td>
									<td data-title="">
										<?php
											if($s->status == 1 && $user->hasPermission('m_terms')){
											?>
											<button data-id="<?php echo Encryption::encrypt_decrypt('encrypt',$s->id)?>" class='btn btn-default btnApprove'><i class='fa fa-check'></i></button>
											<button data-id="<?php echo Encryption::encrypt_decrypt('encrypt',$s->id)?>"   class='btn btn-default btnDecline'><i class='fa fa-remove'></i></button>
											<?php
											}
										?>
										<?php
											if($s->status == 2 && $user->hasPermission('m_terms')){
											?>

											<button data-id="<?php echo Encryption::encrypt_decrypt('encrypt',$s->id)?>"   class='btn btn-default btnDecline'><i class='fa fa-remove'></i></button>
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
	}
	function inventoryMonitoringPaginate($args, $cid) {
		// pages,
		$user = new User();
		$inv = new Inventory_monitoring();
		$search = Input::get('search');
		$b = Input::get('b');
		$r = Input::get('r');
		$date_from = Input::get('from');
		$date_to = Input::get('to');
		$branch_id2 = Input::get('branch_id2');

		?>
		<div id="no-more-tables">
			<div class="table-responsive">
				<table class='table' id='tblSales'>
					<thead>
					<tr>
						<TH>Branch</TH>
						<th>Date</th>
						<TH>Rack</TH>
						<TH>Item</TH>
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
						$limit = 100;
						$countRecord = $inv->countRecord($cid, $search, $b, $r,$date_from,$date_to,$branch_id2);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_inv = $inv->get_sales_record($cid, $start, $limit, $search, $b, $r,$date_from,$date_to,$branch_id2);
						getpagenavigation($page, $total_pages, $limit, $stages);
						if($company_inv) {

							foreach($company_inv as $s) {

								if($s->qty_di == 1 && $s->remarks != 'Confirm inventory') {
									$actionicon = "<span class='glyphicon glyphicon-plus'></span>";
								} else if($s->qty_di == 1 && $s->remarks == 'Confirm inventory') {
									$actionicon = "<span class='glyphicon glyphicon-arrow-right'></span>";
									$s->qty = $s->new_qty;
								} else if($s->qty_di == 2) {
									$actionicon = "<span class='glyphicon glyphicon-minus'></span>";
								} else if($s->qty_di == 3) {
									$actionicon = "<span class='glyphicon glyphicon-arrow-right'></span>";
								}

								$wh_id = getOrderIdOnRemarks($s->remarks);
								$other_lbl = '';
								/*if($wh_id && is_numeric($wh_id)){
									$wh_order = new Wh_order();
									$wh = $wh_order->getFullDetails($wh_id);
									if(isset($wh->id)){

										if($wh->mln){
											$other_lbl = "<span class='span-block'>".INVOICE_LABEL.": ".$wh->invoice."</span>";
											$other_lbl .= "<span class='span-block'>".DR_LABEL.": ".$wh->dr."</span>";
											$other_lbl .= "<span class='span-block'>".PR_LABEL.": ".$wh->pr."</span>";
											$other_lbl .= "<span class='span-block'>".MEMBER_LABEL.": ".$wh->mln."</span>";
										} else {
											$other_lbl .= "<span class='span-block'>Transfer To: ".$wh->branch_name2."</span>";
										}


									}
								} */
								if($s->wh_id && is_numeric($s->wh_id)){

										if($s->member_name){
											$other_lbl = "<span class='span-block'>".INVOICE_LABEL.": ".$s->invoice."</span>";
											$other_lbl .= "<span class='span-block'>".DR_LABEL.": ".$s->dr."</span>";
											$other_lbl .= "<span class='span-block'>".PR_LABEL.": ".$s->pr."</span>";
											$other_lbl .= "<span class='span-block'>".MEMBER_LABEL.": ".$s->member_name."</span>";
										} else {
											$other_lbl .= "<span class='span-block'>Transfer To: ".$s->branch_name2."</span>";
										}

								}

								?>
								<tr>
									<td data-title="Branch">
									<?php echo escape($s->name) ?>
										<small class='text-danger span-block'><?php echo ucwords(escape($s->firstname . " " . $s->lastname)) ?></small>
									</td>

									<td data-title="Created"><?php echo escape(date('m/d/Y H:i:s A', $s->created)); ?></td>
									<td data-title="Rack" class='text-danger'><?php echo escape(($s->rack) ? $s->rack : 'No rack' ) ?></td>
									<td data-title="Item "><?php echo escape($s->item_code) . "<br>" . "<small>" . escape($s->description) . "</small>" ?></td>
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
									<td>
									<small><span class='text-danger'><?php echo ($s->remarks) ?></span></small>
									<small class='span-block'><span class='text-danger'><?php echo ($s->a_remarks) ?></span></small>
									<small><?php echo $other_lbl; ?></small>
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
				<?php //getpagenavigation($page, $total_pages, $limit, $stages); ?>
			</div>
		</div>
	<?php
	}
	function itemWithSpareList($args,$cid){
		$user = new User();
		$ci = new Composite_item();
		$search = Input::get('search');
		$orderItemPre = Input::get('toAssemble');
		$orderarrItem = [];
		$orderarrQty = [];
		$item_ids = "";
		$orderlist=[];
		if($orderItemPre){
			$orderlist = json_decode(Input::get('toAssemble'));
			foreach($orderlist as $indorder){
				$item_ids .= $indorder->item_id . ",";
				$orderarrQty[$indorder->item_id] = $indorder->qty;
			}
			$item_ids = rtrim($item_ids,",");
		}
		$curOnList = json_decode(Input::get('curOnList'));
		$arrOnList = [];
		if(count($curOnList)){
			foreach($curOnList as $cur_list){
				$partsConsume = json_decode($cur_list->splist);
				if(count($partsConsume)){
					foreach($partsConsume as $part){
						$arrOnList[$part->id] = (isset($arrOnList[$part->id])) ? $arrOnList[$part->id] : 0;
						$arrOnList[$part->id] += $part->need_total;
					}
				}
			}
			//dump($arrOnList);
		}
						$inventory = new Inventory();
						$limit = 30;
						if($orderlist){
							$limit = 1000;
						}
						// my categ
						$categ = new Category();
						$uid = $user->data()->id;

						$list_categ = $categ->get_active('categories',['user_id','=',$uid]);
						$categ_str = '';
						if($list_categ){
							foreach($list_categ as $ind_categ){
								$categ_str .= $ind_categ->id . ",";
							}
							$categ_str = rtrim($categ_str,",");
						}
						if($user->data()->id == 100 && Configuration::thisCompany('vitalite')) $categ_str ='';
						$countRecord = $ci->countItemWithParts($cid,$search,$item_ids,$categ_str);
						$total_pages = $countRecord->cnt;
						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$withSpareParts = $ci->getItemWithParts($cid, $start, $limit,$search,$item_ids,$categ_str);
						getpagenavigation($page, $total_pages, $limit, $stages);
						?>
								<?php
									if($withSpareParts){
										// get orders in logistics and WH
										$whorders = new Wh_order();

										$pendingForAssemble = $whorders->getPendingToAssemble($user->data()->company_id,$user->data()->branch_id);
										if($pendingForAssemble){
											echo "<h3>Order Pending to ".Configuration::getValue('assemble')."</h3>";
											echo "<p><span style='width:12px;height:12px;border:1px solid #ccc;background:#fff;display:inline-block;'></span> Without inventory <span style='width:12px;height:12px;border:1px solid #ccc;background:#8cc152;display:inline-block;'></span> With inventory</p>";
											$inv = new Inventory();
											$listedQty = [];
											foreach($pendingForAssemble as $forAssemble){
													$itemsToAssemble = $whorders->getItemToAssemble($forAssemble->id);
													$arrItem = [];
													$btncls='btn btn-default';
													if($itemsToAssemble){
														foreach($itemsToAssemble as $item){
															$allqty = $inv->getAllQuantity($item->item_id,$user->data()->branch_id);
															if(isset($listedQty[$item->item_id])){
																   $allqty->totalQty = $allqty->totalQty - $listedQty[$item->item_id];
															}
															if($allqty->totalQty >= $item->qty ){
																if(isset($listedQty[$item->item_id])){
																    $listedQty[$item->item_id]+=$item->qty;
																} else {
																	 $listedQty[$item->item_id]=$item->qty;
																}
																$btncls = "btn btn-success";
															}
															$arrItem[] = ['item_id' => $item->item_id, 'qty' => formatQuantity($item->qty,true)];
														}

													} else {
														continue;
													}
												echo " <button data-items='".json_encode($arrItem)."' id='forAssemble{$forAssemble->id}' class='$btncls btnAssembleOrder' data-id='$forAssemble->id'>Order # $forAssemble->id</button>";
											}
										}

										?>

										<div id="no-more-tables">
										<table class="table">
											<thead>
											<tr>
												<th>Item </th>
												<th><?php echo Configuration::getValue('spare_part')?></th>
												<th>Allowed Qty</th>
												<th>Qty</th>
											</tr>
										</thead>
										<tbody>
										<?php
										$sparetype = new Spare_type();
										$sparetypes = $sparetype->get_active('spare_type',array('company_id' ,'=',$user->data()->company_id));
										$sparetypearr = [];
										if($sparetypes){
											foreach($sparetypes as $indtype){
												$sparetypearr[$indtype->id] = $indtype->name;
											}
										}
											foreach($withSpareParts as $item) {
													$spareparts = $ci->getSpareparts($item->item_id);
													$qtytoass='';
													if(isset($orderarrQty[$item->item_id])){
														$qtytoass = $orderarrQty[$item->item_id];
													}
													?>
													<tr data-item_set="<?php echo $item->item_id; ?>" id='item<?php echo $item->item_id; ?>'>
														<td style='border-top:1px solid #ccc;' data-title='Item'><strong><?php echo escape($item->item_code) ?></strong><small style='display:block'><?php echo escape($item->description) ?></small></td>

														<td style='border-top:1px solid #ccc;' data-title='Spare part'>
															<?php

																if($spareparts){
															?>


															<table class='table tblSpareNeeded ' id="tbl_<?php echo $item->item_id; ?>">
																<thead>
																<tr>
																	<td class='text-danger'>Spare</td>
																	<td class='text-danger'>Needed</td>
																	<td class='text-danger'>Stock</td>
																	<td class='text-danger'>
																	<?php
																			if(Configuration::getValue('assemble_strict') == 2){
																				echo "Consume";
																			}
																		?>
																	</td>

																</tr>
																</thead>
																<tbody>
																<?php
																	$min = [];
																	// what to skip ?
																// what to skip ?
													$rack_tags = new Rack_tag();
													$myTags = $rack_tags->get_my_tags($user->data()->id);
													if($myTags){
														$wh_tag = false;
														foreach($myTags as $mtag){
															if($mtag->id == 2){
																$wh_tag = true;
															}
														}

														if($wh_tag){
															// warehouse tag only
															$tags_ex = $rack_tags->get_tags_ex('wh_orders',$user->data()->company_id,$user->data()->branch_id);
															if(isset($tags_ex->id) && !empty($tags_ex->id)){
																$excempt_tags = $tags_ex->tag_id;
															} else {
																$excempt_tags =0;
															}
														} else {
															// assemble tag only
															$tags_ex = $rack_tags->get_tags_ex('assembly',$user->data()->company_id,$user->data()->branch_id);
															if(isset($tags_ex->id) && !empty($tags_ex->id)){
																$excempt_tags = $tags_ex->tag_id;
															} else {
																$excempt_tags =0;
															}
														}
													} else {
														// assemble tag only
														$tags_ex = $rack_tags->get_tags_ex('assembly',$user->data()->company_id,$user->data()->branch_id);
														if(isset($tags_ex->id) && !empty($tags_ex->id)){
															$excempt_tags = $tags_ex->tag_id;
														} else {
															$excempt_tags =0;
														}

													}

																	foreach($spareparts as $sp) {
																		$sp_qty = 0;
																		$spinv = $inventory->getAllQuantity($sp->item_id_raw, $user->data()->branch_id,$excempt_tags); // inv of user
																		if($spinv) {
																			$sp_qty = $spinv->totalQty;
																		}
																			$getCurQty = isset($arrOnList[$sp->item_id_raw]) ? $arrOnList[$sp->item_id_raw] : 0;
																			$sp_qty -= $getCurQty;
																			$typename = (isset($sparetypearr[$sp->sptype])) ? $sparetypearr[$sp->sptype] : '';
																		?>
																		<tr data-sp_qty='<?php echo $sp->$sp_qty; ?>' data-unit='<?php echo $sp->unit_name; ?>' data-typename='<?php echo $typename; ?>' data-id="<?php echo $sp->item_id_raw; ?>" data-desc="<?php echo escape($sp->description); ?>" data-item_code="<?php echo escape($sp->item_code); ?>" data-need="<?php echo escape($sp->qty); ?>" data-stock="<?php echo $sp_qty; ?>">
																			<td data-title='Raw' >
																			<strong><?php echo escape($sp->item_code); ?></strong>
																			<small class='span-block'><?php echo escape($sp->description); ?></small>
																			<small class='text-danger span-block'><?php echo (isset($sparetypearr[$sp->sptype])) ? $sparetypearr[$sp->sptype] : ''; ?></small>
																			<br>

																			</td>
																			<td data-title='Needed'>
																			<span class='text-danger' style='font-weight:bold;'>
																			<?php echo formatQuantity(escape($sp->qty),true); ?>
																			</span>
																			<small class='span-block'><?php echo $sp->unit_name; ?></small>
																			</td>
																			<td data-title='Stock'>
																				<span class='text-danger' style='font-weight:bold;'>
																				<?php

																					echo escape(formatQuantity($sp_qty),true);
																					$min[] = $sp_qty / $sp->qty;
																				?>
																				</span>
																				<small class='span-block'><?php echo $sp->unit_name; ?></small>
																			</td>
																			<td data-title='Consume'>
																		<?php
																			if(Configuration::getValue('assemble_strict') == 2){
																				echo "<input type='text' placeholder='Qty' class='form-control custom_need'>";
																			}
																		?>
																			</td>
																		</tr>
																		<?php
																	}
																	}
																?>
																</tbody>
															</table>
															</div>
														</td>
														<td style='border-top:1px solid #ccc;' data-title='Allowed'>
															<?php
																echo floor(min($min));
																// get same item
															?>
														</td>
														<td data-min="<?php echo min($min); ?>" style='border-top:1px solid #ccc;' data-title='Processed'>
															<input style='margin-bottom:10px;width:70px;'  value='<?php echo $qtytoass ?>' type="text" class='form-control' >

															<button class='btn btn-primary btnAssemble'>
																<i class='fa fa-wrench'></i>
															</button>
														</td>
													</tr>                                            <?php
												}
										?>
										</tbody>
										</table>
										</div>

										<?php
									} else {
										echo "<div class='alert alert-info'>No record found.</div>";
									}
	}
	function itemWithSpareListDis($args,$cid){

		$user = new User();
		$ci = new Composite_item();

		$search = Input::get('search');
		$branch_id = Input::get('branch_id');

		$inventory = new Inventory();

		$limit = 30;
		$countRecord = $ci->countItemWithParts($cid,$search);

		$total_pages = $countRecord->cnt;

		$stages = 3;
		$page = ($args);
		$page = (int)$page;

		if($page) {
			$start = ($page - 1) * $limit;
		} else {
			$start = 0;
		}

		$withSpareParts = $ci->getItemWithParts($cid, $start, $limit,$search);

		getpagenavigation($page, $total_pages, $limit, $stages);

		?>
		<?php
				if($withSpareParts){
		?>
										<div id="no-more-tables">
										<table class="table">
											<thead>
											<tr>
												<th>Item</th>
												<th>Spare part</th>
												<th>Current Inventory</th>
												<th>Qty</th>
											</tr>
										</thead>
										<tbody>
										<?php
											foreach($withSpareParts as $item) {
													if(!$branch_id){
														$branch_id = $user->data()->branch_id;
													}
													$spareparts = $ci->getSpareparts($item->item_id);
													$inv_qty = $inventory->getAllQuantity($item->item_id, $branch_id); // inv of user
													$totalQty = 0;
													if($inv_qty){
													$totalQty = $inv_qty->totalQty;
													}
													?>
													<tr data-qty="<?php echo $totalQty; ?>" data-item_set="<?php echo $item->item_id; ?>">
														<td style='border-top:1px solid #ccc;' data-title='Item'><?php echo escape($item->item_code) ?><small style='display:block;'><?php echo escape($item->description) ?></small></td>
														<td style='border-top:1px solid #ccc;' data-title='Spare part'>
															<?php
																if($spareparts){
															?>
															<table  class='table tblSpareNeeded' id="tbl_<?php echo $item->item_id; ?>">
																<thead>
																<tr>
																	<td class='text-danger'>Spare</td>
																	<td class='text-danger'>Qty</td>
																	<td></td>
																</tr>
																</thead>
																<tbody>
																<?php

																	foreach($spareparts as $sp) {
																		?>
																		<tr data-id="<?php echo $sp->item_id_raw; ?>" data-item_code='<?php echo escape($sp->item_code); ?>' data-desc="<?php echo escape($sp->description); ?>" data-need="<?php echo escape($sp->qty); ?>">
																			<td ><?php echo $sp->item_code ."<small class='text-danger span-block'>" . escape($sp->description) . "</small>"; ?></td>
																			<td><?php echo escape($sp->qty); ?></td>

																			<td>

																			</td>
																		</tr>
																		<?php
																	}
																	}
																?>
																</tbody>
															</table>
														</td>
														<td style='border-top:1px solid #ccc;'>
															<?php echo formatQuantity($totalQty); ?>
														</td>
														<td style='border-top:1px solid #ccc;'>
															<input style='margin-bottom:10px;width:75px;' type="text" class='form-control' placeholder="">
															<button class='btn btn-default btnDis'><i class='fa fa-chain-broken'></i></button>
														</td>
													</tr>              <?php
												}
										?>
										</tbody>
										</table>
										</div>
										<?php
									} else {
										echo "<div class='alert alert-info'>No record found.</div>";
									}
	}
	function sparepartsList($args, $cid) {
		// pages,

		$user = new User();
		$ci = new Composite_item();
		$search = Input::get('search');
		$branch_id = Input::get('branch_id');

		?>
		<div id="no-more-tables">
			<div class="table-responsive">
				<table class='table' id='tblSales'>
					<thead>
					<tr>
						<TH>Set</TH>
						<th>Raw</th>
						<TH>Qty</TH>
						<TH>Stocks</TH>
						<TH></TH>
						<th></th>

					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 100;
						$countRecord = $ci->countRecord($cid,$search);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_inv = $ci->get_sales_record($cid, $start, $limit,$search);
						getpagenavigation($page, $total_pages, $limit, $stages);
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
								$repack = '';
								if($prevset != $s->item_id_set){
									$setItemCode = $s->set_item_code . "<small class='text-danger' style='display:block;  '>".$s->set_description."</small>";
									$setItemCode .= "<div><button class='btn btn-default btn-sm btnDownloadDetail' data-id='$s->item_id_set'><i class='fa fa-download'></i></button></div>";
									$borderTop = "border-top:1px solid #ddd;";
									$repack = $s->repack ;
								}
								$prevset =  $s->item_id_set;
								if(!$branch_id){
									$branch_id = $user->data()->branch_id;
								}
								$stock_composite = $inv->getAllQuantity($s->item_id_raw,$branch_id);
								$st_composite = 0;
								if(isset($stock_composite->totalQty)){
									$st_composite = $stock_composite->totalQty ;
								}
								?>
								<tr data-id='<?php echo Encryption::encrypt_decrypt('encrypt',$s->id); ?>'>
									<td style='<?php echo $borderTop; ?>' data-title="Set Item">
									<?php echo ($setItemCode)  ?>
									</td>
									<td style='<?php echo $borderTop; ?>'  data-title="Raw Item"><?php echo escape($s->item_code). "<small class='text-danger' style='display:block;  '>".$s->description."</small>" ?></td>
									<td style='<?php echo $borderTop; ?>'  data-title="Qty"><?php echo formatQuantity(escape($s->qty)) ?></td>
									<td style='<?php echo $borderTop; ?>'  data-title="Qty"><?php echo formatQuantity(escape($st_composite)) ?></td>
									<td style='<?php echo $borderTop; ?>' class='text-danger' data-title="Type"><?php echo (isset($sparetypearr[$s->sptype])) ? $sparetypearr[$s->sptype] : ''; ?></td>
									<td style='<?php echo $borderTop; ?>'  data-title="Action">
										<?php if($hasPerm) {
										?>
										<button class='btn btn-primary btnEdit'>
											<span class='glyphicon glyphicon-pencil'></span>
										</button>
										<button class='btn btn-primary btnDelete'>
											<span class='glyphicon glyphicon-trash'></span>
										</button>
										<?php
										}?>
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
	}
	function supplierItemPaginate($args, $cid) {
		// pages,
		$user = new User();
		$sup = new Supplier_item();
		$search = Input::get('search');
		$item_id = Input::get('item_id');
		$supplier_id = Input::get('supplier_id');

		?>
		<div id="no-more-tables">
			<div class="table-responsive">

				<table class='table' id='tblSales'>
					<thead>
					<tr>
						<TH>Supplier</TH>
						<TH>Supplier Item</TH>
						<TH>Company Item</TH>
						<?php if($user->hasPermission('supplier_item_price_show')) { ?>
						<TH>Purchase Price</TH>
						<?php } ?>
						<th>Stock</th>
						<th>Min Quantity</th>
						<?php if($user->hasPermission('supplier_sim')) { ?>
							<TH></TH>
						<?php } ?>
					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 100;
						$countRecord = $sup->countRecord($cid, $search, $user->data()->branch_id,$item_id,$supplier_id);

						$total_pages = (isset($countRecord->cnt)) ? $countRecord->cnt : 0;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_inv = $sup->get_sales_record($cid, $start, $limit, $search, $user->data()->branch_id,$item_id,$supplier_id);
						getpagenavigation($page, $total_pages, $limit, $stages);

						if($company_inv) {
							foreach($company_inv as $s) {
								if($s->ic || $s->des) {
									$comitem = $s->ic . "<br><small class='text-danger'>" . $s->des . "</small>";
								} else {
									$comitem = 'No item yet';
								}
								?>
								<tr>
									<td data-title="Supplier"><?php echo escape($s->supname) ?></td>
									<td data-title="Supplier Item"><?php echo($s->item_code . "<br><small class='text-danger'>" . $s->description . "</small>"); ?></td>
									<td data-title="Company Item"><?php echo($comitem); ?></td>
									<?php if($user->hasPermission('supplier_item_price_show')) { ?>
									<td data-title="Purchase price"><?php echo escape(number_format($s->purchase_price, 2)) ?></td>
									<?php } ?>
									<td data-title="Stock"><?php echo escape(number_format($s->invqty)) ?></td>
									<td data-title=""><?php echo escape(number_format($s->min_qty)) ?></td>
									<?php if($user->hasPermission('supplier_sim')) { ?>
										<td>
											<a class='btn btn-primary' href='supplier_item_edit.php?edit=<?php echo escape(Encryption::encrypt_decrypt('encrypt', $s->id)); ?>' title='Edit Items'><span class='glyphicon glyphicon-pencil' ></span></a>
											<a class='btn btn-primary deleteSupplierItem' href='#' id='<?php echo escape(Encryption::encrypt_decrypt('encrypt', $s->id)); ?>' title='Delete Items'><span class='glyphicon glyphicon-trash' ></span></a>
										</td>
									<?php } ?>
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

	function alertList($args, $cid) {
		// pages,
		$user = new User();
		$alertcls = new Alert_item();
		$search = addslashes(Input::get('search'));


		?>
		<div id="no-more-tables">
			<div class="table-responsive">
				<table class='table' id='tblSales'>
					<thead>
					<tr>
						<TH>Item</TH>
						<TH>Alert Days</TH>
						<th>Alert Message</th>
						<TH>Date Created</TH>
						<th>Alerted Position</th>
						<?php if($user->hasPermission('alert_m')) { ?>
							<th></th>
						<?php } ?>
					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 20;
						$countRecord = $alertcls->countRecord($cid, $search);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_inv = $alertcls->get_alert_record($cid, $start, $limit, $search);
						getpagenavigation($page, $total_pages, $limit, $stages);
						if($company_inv) {
							foreach($company_inv as $s) {

								?>
								<tr>
									<td data-title="Item"><?php echo escape($s->item_code) . "<br><span class='text-center'><small>" . escape($s->description) . "</small></span>" ?></td>
									<td data-title="Alert Days"><?php echo escape($s->alert_days) ?></td>
									<td data-title="Alert Msg"><?php echo escape($s->alert_msg) ?></td>
									<td style='min-width:175px;' data-title="Created"><?php echo escape(date('m/d/Y H:i:s A', $s->created)) ?></td>
									<td data-title="Position">
										<?php
											$poscls = new Position();
											$posstr = '';
											if(strpos($s->position_id, ',')) {
												$poslist = explode(',', $s->position_id);
												foreach($poslist as $pos) {
													$pname = $poscls->getName($pos);
													$posstr .= $pname->position . ", ";
												}
												$posstr = rtrim($posstr, ", ");
											} else {
												$pname = $poscls->getName($s->position_id);
												$posstr = $pname->position;
											}
											echo $posstr;


										?>
									</td>
									<?php if($user->hasPermission('alert_m')) { ?>
										<td>
											<a class='btn btn-primary' href='addalert.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt', $s->id); ?>' title='Edit Alerts'><span class='glyphicon glyphicon-pencil'></span></a>
											<a class='btn btn-primary deleteAlert' href='#' id='<?php echo Encryption::encrypt_decrypt('encrypt', $s->id); ?>' title='Delete Alerts'><span class='glyphicon glyphicon-trash'></span></a>
										</td>
									<?php } ?>
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

	function memberList($args, $cid) {
		// pages,
		$user = new User();
		$mem = new Member();
		$search = addslashes(Input::get('search'));
		$salestype = addslashes(Input::get('salestype'));
		$char = addslashes(Input::get('char'));
		$agent_id = addslashes(Input::get('agent_id'));
		$region = addslashes(Input::get('region'));
		$date_from = (Input::get('date_from'));
		$date_to = (Input::get('date_to'));


	    $salestype= ($salestype) ? $salestype : 0;
	    $char= ($char) ? $char : 0;

		?>
		<div id="no-more-tables">
			<div class="table-responsive">
				<table class='table' id='tblSales'>
					<thead>
					<tr>
						<TH>Name</TH>
						<TH><?php echo MEMBER_LABEL; ?> Since</TH>
						<TH>Date Created</TH>
						<th>Agent</th>

						<?php if($user->hasPermission('member_m') && !$user->hasPermission('wh_agent')) { ?>
							<TH>Hold/Unhold</TH>
						<?php } ?>
							<TH>Other details</TH>
							<?php if($user->hasPermission('member_m') || $user->hasPermission('wh_agent')) { ?>
							<th></th>
						<?php } ?>

					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 20;
						$countRecord = $mem->countRecord($cid, $search,$salestype,$char,$agent_id,$region,$date_from,$date_to);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_inv = $mem->get_member_record($cid, $start, $limit, $search,$salestype,$char,0,$agent_id,$region,$date_from,$date_to);
						getpagenavigation($page, $total_pages, $limit, $stages);
						if($company_inv) {
							foreach($company_inv as $s) {
								$blacklist = '';
								if($s->is_blacklisted == 1) {
									$blacklist = 'checked';
								}
								if($s->member_since){
									$member_since = date('m/d/Y', $s->member_since);
								} else {
									$member_since = "N/A";
								}

								$agent_ids = $s->agent_id;
								$explodes = explode(',',$agent_ids);
								$agent_name = "";
								if(count($explodes) > 0){
									foreach($explodes as $agent_id){
										if(is_numeric($agent_id) && $agent_id){
											$agent_user = new User($agent_id);
											if(isset($agent_user->data()->id)){
											$agent_name .= "<small class='span-block'>".$agent_user->data()->lastname . ", " .$agent_user->data()->firstname ."</small>";
										}
									}
									}
								}


								?>
								<tr>
									<td data-title="Name" style='width:350px;'>
									<?php
									 echo ucwords(escape($s->lastname));
									 if($s->region){

									  echo "<small class='text-danger span-block'>$s->region</small>";

									 }

									?>
									<smal class='span-block text-danger'><?php echo $s->personal_address; ?></smal>
									</td>
									<td data-title="<?php echo MEMBER_LABEL; ?> Since"><?php echo escape($member_since) ?></td>
									<td style='min-width:175px;' data-title="Created"><?php echo escape(date('m/d/Y H:i:s A', $s->created)) ?></td>
									<td><?php echo ($agent_name) ?></td>
									<?php if($user->hasPermission('member_m') && !$user->hasPermission('wh_agent')) { ?>
										<td data-title="Hold">
											<input type="checkbox" data-member_id='<?php echo $s->id; ?>' class='chkBlockList' <?php echo $blacklist; ?>/> Hold
										</td>

									<?php } ?>
										<td>
										<span class='span-block'>
											Credit limit:
											<strong class='text-danger span-block'><?php echo   ($s->credit_limit) ? $s->credit_limit : "N/A"; ?></strong>
										</span>
										<span class='span-block'>
											Terms:
											<strong class='text-danger span-block'><?php echo ($s->terms) ? $s->terms : "N/A"; ?></strong>
										</span>
										</td>
											<?php if($user->hasPermission('member_m') || $user->hasPermission('wh_agent')) { ?>
										<td>
											<?php if($user->hasPermission('wh_agent') && !$user->hasPermission('wh_all_member')){
											?>
											<a class='btn btn-primary' href='purchase_details.php?id=<?php echo Encryption::encrypt_decrypt('encrypt', $s->id); ?>' title='View Transaction'><span class='glyphicon glyphicon-list'></span></a>

											<?php
											} else {
											?>
												<a href='upload.php?r=<?php echo Encryption::encrypt_decrypt('encrypt','members')?>&id=<?php echo Encryption::encrypt_decrypt('encrypt',$s->id)?>&p=<?php echo Encryption::encrypt_decrypt('encrypt','members.php')?>' class='btn btn-primary' title='Add Image'><span class='glyphicon glyphicon-file'></span></a>
												<a class='btn btn-primary' href='purchase_details.php?id=<?php echo Encryption::encrypt_decrypt('encrypt', $s->id); ?>' title='View Transaction'><span class='glyphicon glyphicon-list'></span></a>
												<a class='btn btn-primary' href='addmember.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt', $s->id); ?>' title='Edit <?php echo MEMBER_LABEL; ?>'><span class='glyphicon glyphicon-pencil'></span></a>
												<a href='#' class='btn btn-primary deleteMember' id="<?php echo Encryption::encrypt_decrypt('encrypt', $s->id); ?>" title='Delete <?php echo MEMBER_LABEL; ?>'><span class='glyphicon glyphicon-remove'></span></a>

											<?php
											}?>

										</td>
										<?php } ?>

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
	function docColorList($args, $cid) {
		// pages,
		$user = new User();
		$doc = new Doc_color();
		$search = addslashes(Input::get('search'));


		?>
		<div id="no-more-tables">
			<div class="table-responsive">
				<table class='table' id='tblSales'>
					<thead>
					<tr>
						<th>Type</th>
						<TH>Color</TH>
						<TH>Date Created</TH>
						<?php if($user->hasPermission('sales')) { ?>
						<th></th>
						<?php } ?>
					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 20;
						$countRecord = $doc->countRecord($cid, $search);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_inv = $doc->get_doc_record($cid, $start, $limit, $search);
						getpagenavigation($page, $total_pages, $limit, $stages);
						if($company_inv) {
							foreach($company_inv as $s) {
								$arrdoc = ['','Invoice','DR','IR'];
								?>
								<tr>
									<td data-title='Title'><?php echo (escape($arrdoc[$s->doc_type])) ?></td>
									<td data-title="Name" class='text-danger'><?php echo ucwords(escape($s->name)) ?></td>
									<td data-title="Created"><?php echo escape(date('m/d/Y H:i:s A', $s->created)) ?></td>
									<?php if($user->hasPermission('sales')) { ?>
										<td>
										<a class='btn btn-primary' href='adddoccolor.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt', $s->id); ?>' title='Edit Doc'><span class='glyphicon glyphicon-pencil'></span></a>
										<a href='#' class='btn btn-primary deleteDoc' id="<?php echo Encryption::encrypt_decrypt('encrypt', $s->id); ?>" title='Delete Doc'><span class='glyphicon glyphicon-remove'></span></a>
										</td>
									<?php } ?>
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
function calculateCreditAging($member_id){
		$id = $member_id;
		$user = new User();
		$member_credit = new Member_credit();
		$credit_details = $member_credit->getMemberCreditDetials($id);

		// list ng items
		$sales = new Sales();
		$items = $sales->salesTransactionBaseOnPaymentId($credit_details->payment_id);
		$over1 = 0; $over2 = 0; $over3= 0; $over4 = 0;
		$all_total = 0;
		if(count($items) > 0){

			foreach($items as $item){
			$adjustment = $item->adjustment;
			$qtys = $item->qtys;
			$price = $item->price;
			if($adjustment){
				$per_item_adjustment = $adjustment / $qtys;
				$price = $price + $per_item_adjustment;
			}
			$total = ($qtys * $price) + $item->member_adjustment;
			$all_total += $total;
			$sold = $item->sold_date;
			$terms = $item->terms;
			if(!$terms){
				$terms = $credit_details->def_terms; // default terms of member
			}

			$end_of_terms = strtotime(date('F d, Y',$sold) . " $terms days");
			$now = strtotime(date('F d, Y'));

			if($now > $end_of_terms){
				 // over
				 // 86400 per day
			    $over = $now - $end_of_terms;
			    $over_days = $over / 86400;
			    $over_days = floor($over_days);
			    if($over_days >= 1 && $over_days <= 30){
			        $over1 += $total;
			    } else if($over_days >= 31 && $over_days <= 60){
			      $over2 += $total;
			    }else if($over_days >= 61 && $over_days <= 90){
			      $over3 += $total;
			    }else if($over_days >= 91){
			        $over4 += $total;
			    }
			}

			}

		}


		// progress bar payment
		$total_due = $credit_details->amount;
		$total_paid = $credit_details->amount_paid;
		$percentage = ($total_paid / $total_due) * 100;
		$perc = number_format($percentage,2);

		$matchpayment = $sales->matchPaymentSales($user->data()->company_id,$credit_details->payment_id);
		$m_total = $matchpayment->ttotal;
		$m_cash = $matchpayment->cashamount;
		$m_cheque = $matchpayment->chequeamount;
		$m_bt = $matchpayment->btamount;
		$m_cc =  $matchpayment->ccamount;
	    $m_pc = $matchpayment->pcamount;
	    $m_pcf = $matchpayment->pcfamount;
		$other_payment  = $m_cash + $m_cheque + $m_bt + $m_cc + $m_pc + $m_pcf;



			$paid = $total_paid;
			if($all_total != $credit_details->amount){
				$paid += $other_payment;
			}
			if($over4){
				$over4 = $over4 - $paid;
				if($over4 < 0){
					$paid = abs($over4);
					$over4 = 0;
				} else {
					$paid = 0;
				}
			}
			if($over3){
				$over3 = $over3 - $paid;
				if($over3 < 0){
					$paid = abs($over3);
					$over3 = 0;
				} else {
					$paid = 0;
				}
			}
			if($over2){
				$over2 = $over2 - $paid;
				if($over2 < 0){
					$paid = abs($over2);
					$over2 = 0;
				} else {
					$paid = 0;
				}
			}
			if($over1){
				$over1 = $over1 - $paid;
				if($over1 < 0){
					$paid = abs($over1);
					$over1 = 0;
				} else {
					$paid = 0;
				}
			}

		$aging = ['over1' => $over1 , 'over2' => $over2,'over3' => $over3,'over4' => $over4];
		return $aging;
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
						<th>Invoice/Dr</th>
						<TH>Credit</TH>
						<th>Date</th>
						<TH>Paid</TH>
						<TH>Remaining</TH>
						<th>Payment Details</th>
						<TH></TH>
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
									if($type == 2){
										/*$aging = calculateCreditAging($s->id);
										$over1 += $aging['over1'];
										$over2 += $aging['over2'];
										$over3 += $aging['over3'];
										$over4 += $aging['over4'];
										if( $aging['over1'] ||  $aging['over2'] ||  $aging['over3'] ||  $aging['over3']){
											$bgwarn = "bg-danger";
										} */
									}
									$to_branch_name='';
									if($s->to_branch_name){
										$to_branch_name = $s->to_branch_name;
									}

								?>
								<tr class='<?php echo $bgwarn; ?>' data-total='<?php echo $s->amount - $s->amount_paid; ?>'>
									<td style='<?php echo $bordertop; ?>' data-title="Name" class='text-danger'>
									<?php echo ucwords(escape($s->lastname . ", " . $s->firstname . " " . $s->middlename)) . " "; ?>
									<small class='span-block'><?php echo ucwords($s->station_name); ?></small>
									<small class='span-block'><?php echo ucwords($to_branch_name) . " "; ?></small>
									<small class='span-block'><?php echo ($s->wh_remarks) ? "<span class='text-muted'>Remarks: " . $s->wh_remarks ."</span>": ""; ?></small>
									<small class='text-muted' style='display:block;'><?php echo ucwords(escape($s->ufn . " " . $s->uln)); ?></small>
									<?php if($s->cr_number){
										?>
										<small class='span-block'>CR Number: <span class='text-muted'><?php echo $s->cr_number; ?></span></small>
										<small class='span-block'>CR Date: <span class='text-muted'><?php echo date('m/d/Y',$s->cr_date); ?></span></small>
										<?php
									}?>

									</td>
									<td style='<?php echo $bordertop; ?>'>
									<?php echo $ctrnum; ?>
									<?php echo $doc_receive; ?>

									</td>
									<td style='<?php echo $bordertop; ?>' data-title="Total Credit"><?php echo number_format(escape($s->amount),2); ?></td>
									<td style='<?php echo $bordertop; ?>' data-title="Date"><?php echo date('m/d/Y',escape($s->solddate)); ?></td>
									<td style='<?php echo $bordertop; ?>' data-title="Paid"><?php echo number_format(escape($s->amount_paid),2); ?> </td>
									<td style='<?php echo $bordertop; ?>' data-title="Remaining">
									<?php echo number_format(escape($s->amount - $s->amount_paid),2); ?>
									<?php if($s->charges != 0.00){
									?>
									<small style='margin-top:10px;' class='span-block text-danger'>
									Unpaid Freight: <?php echo  number_format($s->charges,2); ?>
									<button style='margin-left:10px;font-size:12px;' class='btn btn-default btn-sm btnFreightPayment' data-payment_id='<?php echo $s->payment_id; ?>' ><i class='fa fa-check'></i> Mark as Paid</button>
									</small>

									<?php
									}?>
									</td>
									<td style='<?php echo $bordertop; ?>' >
										<?php echo $detpay; ?>
										<small class='text-muted' style='display:block;'><?php echo escape($n); ?></small>
									</td>
									<td style='<?php echo $bordertop; ?>'>
									<?php if($user->hasPermission('member_credit_payment')){
									?>
										<?php echo $labelstat; ?>
										<a  href='#' style='width:130px;margin:2px;' data-id='<?php echo Encryption::encrypt_decrypt('encrypt',$s->id); ?>' class='btn btn-default btn-sm btnAddName'><i class='fa fa-pencil'></i> Remarks</a>
										<a  href='#' style='width:130px;margin:2px;' data-id='<?php echo Encryption::encrypt_decrypt('encrypt',$s->id); ?>' class='btn btn-default btn-sm btnTerms'><i class='fa fa-list'></i> Item Details</a>

										<?php

										if($s->amount_paid > 0){
										?>
										<a  style='margin:2px;width:130px;' href='#' data-payment_id='<?php echo $s->payment_id; ?>' class='btn btn-default btn-sm paymentDetails'><i class='fa fa-money'></i> Payment Details</a>
										<?php
										}
										if($user->hasPermission('credit_all') && $s->amount_paid > 0){
										?>

										<div  style='margin-top:5px;'>

											<?php
												if($s->status == -1){
													?>
														<a  href='#' data-id='<?php echo Encryption::encrypt_decrypt('encrypt',$s->id); ?>' class='btn btn-primary btn-sm btnApproveCredit'><i class='fa fa-check'></i> Approve</a>
														<a  href='#' data-id='<?php echo Encryption::encrypt_decrypt('encrypt',$s->id); ?>' class='btn btn-danger btn-sm btnDeclineCredit'><i class='fa fa-remove'></i> Decline</a>
													<?php
												}
											?>

										</div>
										<?php
										}
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
				<?php if($type == 2):?>
				<div class="panel panel-default" style='display:none;'>
				<div class="panel-body">
				<h4>Aging</h4>
				<table class="table table-bordered">
						<thead>
							<tr><th>30 days</th><th>60 days</th><th>90 days</th><th>Over 90 days</th></tr>
						</thead>
						<tbody>
							<tr>
							<td><?php echo number_format($over1,2)?></td>
							<td><?php echo number_format($over2,2)?></td>
							<td><?php echo number_format($over3,2)?></td>
							<td><?php echo number_format($over4,2)?></td>
							</tr>
						</tbody>
					</table>
				</div>
				</div>
				<?php endif; ?>
			</div>
		</div>
	<?php
	}
	function witnessList($args, $cid) {
		// pages,
		$user = new User();
		$witness = new Witness();
		$search = addslashes(Input::get('search'));


		?>
		<div id="no-more-tables">
			<div class="table-responsive">
				<table class='table table-bordered' id='tblForApproval'>
					<thead>
					<tr>
						<TH>Name</TH>
						<TH>Date Created</TH>
						<?php if($user->hasPermission('witness_m')) { ?>

							<th></th>
						<?php } ?>
					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 20;
						$countRecord = $witness->countRecord($cid, $search);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_inv = $witness->get_witness_record($cid, $start, $limit, $search);
						getpagenavigation($page, $total_pages, $limit, $stages);
						if($company_inv) {
							foreach($company_inv as $s) {

								?>
								<tr>
									<td data-title="Name" class='text-danger'><?php echo ucwords(escape($s->lastname . ", " . $s->firstname . " " . $s->middlename)) ?></td>
									<td style='min-width:175px;' data-title="Created"><?php echo escape(date('m/d/Y H:i:s A', $s->created)) ?></td>
									<?php if($user->hasPermission('witness_m')) { ?>


										<td>
											<a class='btn btn-primary' href='addwitness.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt', $s->id); ?>' title='Edit Witness'><span class='glyphicon glyphicon-pencil'></span></a>
											<a href='#' class='btn btn-primary deleteWitness' id="<?php echo Encryption::encrypt_decrypt('encrypt', $s->id); ?>" title='Delete Witness'><span class='glyphicon glyphicon-remove'></span></a>
										</td>
									<?php } ?>
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

	function logList($args, $cid) {
		// pages,
		$user = new User();
		$log = new Log();
		$search = addslashes(Input::get('search'));
		$user_id = Input::get('user_id');


		?>
		<div id="no-more-tables">
			<div class="table-responsive">
				<table class='table' id='tblWithBorder'>
					<thead>
					<tr>
						<TH>User</TH>
						<TH>Created</TH>
						<th>Remarks</th>
					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 20;
						$countRecord = $log->countRecord($cid,$search,$user_id);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_inv = $log->get_log_record($cid, $start, $limit,$search,$user_id);
						getpagenavigation($page, $total_pages, $limit, $stages);
						if($company_inv) {
							foreach($company_inv as $s) {

								?>
								<tr>
									<td data-title="Name" class='text-danger'><?php echo ucwords(escape($s->lastname . ", " . $s->firstname . " " . $s->middlename)) ?></td>
									<td  data-title="Created"><?php echo escape(date('m/d/Y H:i:s A', $s->created)) ?></td>

									<?php
									$extralbl = '';
										if(strpos($s->remarks,"||") > 0){
											 $firstpart = substr($s->remarks,0,strpos($s->remarks,"||"));
											$extracted = substr($s->remarks,strpos($s->remarks,"||")+2);
											$exploded = explode(':',$extracted);

											if($exploded[0] == 'items'){
												if(is_numeric($exploded[1])){
												$logcls = new Product($exploded[1]);
												$extralbl = $logcls->data()->description . "(". $logcls->data()->item_code.")";
												}

											} else if($exploded[0] == 'display_location'){
												$logcls = new Display_location($exploded[1]);
												$extralbl = $logcls->data()->name;
											}else if($exploded[0] == 'categories'){
												$logcls = new Category($exploded[1]);
												$extralbl = $logcls->data()->name;
											}else if($exploded[0] == 'characteristics'){
												$logcls = new Characteristics($exploded[1]);
												$extralbl = $logcls->data()->name;
											}else if($exploded[0] == 'units'){
												$logcls = new Unit($exploded[1]);
												$extralbl = $logcls->data()->name;
											}else if($exploded[0] == 'queue'){
												$logcls = new Queu($exploded[1]);
												$extralbl = $logcls->data()->name;
											}
											 $fstr = $firstpart;
										} else {
											$fstr = $s->remarks;
										}
									?>
									<td  data-title="Remarks"><?php echo ucwords((strtolower($fstr))) . " " .$extralbl ?></td>
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
	//recycleBinPaginate
	function recycleBinPaginate($args, $cid) {
		// pages,
		$user = new User();
		$rec = new Recycle_bin();
		$tbl = Input::get('tbl');
		if($tbl == 'users'){
			$ths = ["First name", 'Middle name','Last name','Username'];
			$tds = ['firstname','middlename','lastname','username'];
		} else if ($tbl == 'items'){
			$ths = ["Barcode", 'Item Code','Description','Deleted By','Date'];
			$tds = ['barcode','item_code','description','deleted_by','modified'];
		} else if ($tbl == 'terminals'){
			$ths = ["Terminal", "Invoice", "Dr"];
			$tds = ['name','invoice','dr'];
		}else if ($tbl == 'branches'){
			$ths = ["Branch Name", "Description", "Address"];
			$tds = ['name','description','address'];
		} else if ($tbl == 'members'){
			$ths = ["First name", 'Middle name','Last name'];
			$tds = ['firstname','middlename','lastname'];
		}  else if ($tbl == 'stations'){
			$ths = ["Station"];
			$tds = ['name'];
		}else {
		$ths = [];
		$tds = [];
		}
		?>
		<div id="no-more-tables">
			<div class="table-responsive">
				<?php if(count($ths) > 0 ){ ?>

				<table class='table' id='tblSales'>
					<thead>
					<tr>
						<?php
							foreach($ths as $th){
							echo "<th>$th</th>";
							}
						?>
						<th>Action</th>
					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 20;
						$countRecord = $rec->countRecord($cid,$tbl);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_inv = $rec->get_rec_record($cid, $start, $limit,$tbl);
						getpagenavigation($page, $total_pages, $limit, $stages);
						if($company_inv) {
							foreach($company_inv as $s) {
								?>
								<tr>
							<?php

								foreach($tds as $td){
									$n = "";
									if($td == 'deleted_by')
									{
										$cur_user = $s->$td;
										if(is_numeric($cur_user) && $cur_user){
											$cur = new User($cur_user);
											$n = ucwords($cur->firstname . " " . $cur->lastname);
											echo "<td>".$n."</td>";
										} else {
											echo "<td><i class='fa fa-ban'></i></td>";
										}

									} else {
										echo "<td>".$s->$td."</td>";
									}
								}

							?>
								<td>
									<button class='btn btn-default btnRestore' data-table='<?php echo $tbl; ?>' data-id='<?php echo $s->id?>' ><span class='glyphicon glyphicon-refresh'></span> Restore</button>
									<!-- <button  class='btn btn-default btnDelete' data-id='<?php echo $s->id?>'><span class='glyphicon glyphicon-trash'></span> Delete Permanently</button> -->
								</td>
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
				<?php }?>
			</div>
		</div>
	<?php
	}
	function stationList($args, $cid) {
		// pages,
		$user = new User();
		$stacls = new Station();
		$cf = new Custom_field();
		$cfd = new Custom_field_details();

		$getstationdet = $cf->getcustomform('stations',$user->data()->company_id);

		$label_name = isset($getstationdet->label_name)? strtoupper($getstationdet->label_name):'STATION';

		$description = $cfd->getIndData('description',$user->data()->company_id);
		$region = $cfd->getIndData('region',$user->data()->company_id);
		$brand = $cfd->getIndData('brand',$user->data()->company_id);
		$package = $cfd->getIndData('package',$user->data()->company_id);
		$otherfield = isset($getstationdet->other_field)?$getstationdet->other_field:'';

		$desc_label = 'Address';
		$desc_visible = '';
		if(($description)){
			if($description->field_label){
				$desc_label = $description->field_label;
			}
			if($description->is_visible == 0){
				$desc_visible = 'display:none;';
			}
		}

		$region_label = 'Region';
		$region_visible = '';
		if(($region)){
			if($region->field_label){
				$region_label = $region->field_label;
			}
			if($region->is_visible == 0){
				$region_visible = 'display:none;';
			}
		}



		$brand_label = 'Brand';
		$brand_visible = '';
		if(($brand)){
			if($brand->field_label){
				$brand_label = $brand->field_label;
			}
			if($brand->is_visible == 0){
				$brand_visible = 'display:none;';
			}else {

			}
		}

		$package_label = 'Package';
		$package_visible = '';
		if(($package)){
			if($package->field_label){
				$package_label = $package->field_label;
			}
			if($package->is_visible == 0) {
				$package_visible = 'display:none;';
			}
		}






		?>
		<div id="no-more-tables">
			<div class="table-responsive">
				<table class='table'>
					<thead>
					<tr>
						<TH>Name</TH>
						<TH><?php echo $desc_label;?></TH>
						<TH><?php echo MEMBER_LABEL; ?></TH>
						<TH>Created</TH>
						<?php
							if($user->hasPermission('station_m')) {
								?>
								<TH>Actions</TH>
							<?php } ?>
					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 20;
						$search = Input::get('s');
						$countRecord = $stacls->countRecord($cid,$search);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_inv = $stacls->get_sales_record($cid, $start, $limit,$search);
						getpagenavigation($page, $total_pages, $limit, $stages);
						if($company_inv) {
							foreach($company_inv as $s) {

								?>
								<tr>
									<td data-title="Station name"><?php echo escape($s->name) ?></td>
									<td data-title="Address" class='text-danger'><?php echo escape($s->address) ?></td>
									<td data-title="Name" style='min-width:160px;'><?php echo $s->lastname . ", " . $s->firstname ?></td>
									<td data-title="Created"><?php echo escape(date('m/d/Y H:i:s A', $s->created)) ?></td>
									<?php
										if($user->hasPermission('station_m')) {
											?>
											<td style='width:200px;'>
												<a href='upload.php?r=<?php echo Encryption::encrypt_decrypt('encrypt','stations')?>&id=<?php echo Encryption::encrypt_decrypt('encrypt',$s->id)?>&p=<?php echo Encryption::encrypt_decrypt('encrypt','station.php')?>' class='btn btn-primary' title='Add Image'><span class='glyphicon glyphicon-file'></span></a>
												<a  class='btn btn-primary' href='addstation.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt', $s->id); ?>' title='Edit Station'><span class='glyphicon glyphicon-pencil'></span></a>
												<a href='#' class='btn btn-primary deleteStation' id="<?php echo Encryption::encrypt_decrypt('encrypt', $s->id); ?>" title='Delete Station'><span class='glyphicon glyphicon-remove'></span></a>
											</td>
										<?php } ?>
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
	function chequeList($args, $cid) {
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

		if(count($branch_id)){

		}
		?>

					<?php
						//$targetpage = "paging.php";
						if($search){
							$limit = 200;
						} else {
							$limit = 100;
						}


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
				<table class='table' id='tblSales'>
					<thead>
					<tr>


						<TH>Control Number</TH>
						<TH>Sold date</TH>
						<TH>Client</TH>
						<TH>Details</TH>
						<th>Maturity</th>
						<th></th>
						<th></th>
					</tr>
					</thead>
					<tbody>
						<?php
							$sales = new Sales();
							$prevPayment = '';
							$total_check = 0;
							foreach($company_inv as $s) {
									if($prevPayment != $s->payment_id){
										$borderTop = "border-top:1px solid #ccc";
										$btn = "<button data-payment_id='$s->payment_id' class='btn btn-default btn-sm getPTDetails'><i class='fa fa-list'></i></button>";
									} else {
										$borderTop = '';
										$btn ='';
									}
									$prevPayment = $s->payment_id;
									$total_check += $s->amount;
									$station_name = '';
									if($s->station_name){
									$station_name = "<span class='span-block'><strong>Station:</strong> ".$s->station_name."</span>";
									}
									$s->salestype_name = ($s->salestype_name) ? $s->salestype_name : 'N/A';
									$user_name = "N/A";
									if($s->ufn && $s->uln){
										$user_name = ucwords($s->ufn ." ". $s->uln);
									}
									$bg_color = "";
									if($s->status == 3){
										$bg_color = "bg-danger";
									}

								?>
								<tr class='<?php echo $bg_color; ?>'>

									<td data-title='Invoice' style='<?php echo $borderTop?>'>
									<span class='span-block'><?php echo INVOICE_LABEL?>: <?php echo (isset($s->invoice) && $s->invoice != 0) ? "<strong class='text-danger'>" . escape($s->invoice) . "</strong>" : '<i class="fa fa-ban"></i>'; ?></span >
									<span class='span-block'><?php echo DR_LABEL?>: <?php echo (isset($s->dr) && $s->dr != 0) ? "<strong class='text-danger'>" . escape($s->dr) . "</strong>" : '<i class="fa fa-ban"></i>'; ?></span >
									<span class='span-block'><?php echo PR_LABEL?>: <?php echo (isset($s->ir) && $s->ir != 0) ? "<strong class='text-danger'>" . escape($s->ir) . "</strong>" : '<i class="fa fa-ban"></i>'; ?></span >
									<?php if(Configuration::isAquabest()){
									?>
									<span class='span-block'><?php echo "SR"?>: <?php echo (isset($s->sr) && $s->sr != 0) ? "<strong class='text-danger'>" . escape($s->sr) . "</strong>" : '<i class="fa fa-ban"></i>'; ?></span >
<?php
									}?>

									</td>
									<td data-title='Sold' style='<?php echo $borderTop?>'>
									<?php echo escape(date('m/d/Y', $s->sold_date)) ?>
									<small class='span-block text-danger'><?php echo "Terms: "; echo  ($s->terms) ? $s->terms: 'N/A'; ?></small>
									</td>

									<td data-title='Client' style='<?php echo $borderTop?>' >
									<?php echo "<span class='span-block'><strong>Sales type:</strong> " . ucwords(escape ($s->salestype_name)) . "</span>"; ?>

									<?php
									echo "<span class='span-block'><strong>Client: </strong>" . ucwords(escape ($s->mln)) . "</span>";
									echo $station_name;
									?>
									<?php echo "<span class='span-block'><strong>Agent/Cashier: </strong>" .$user_name . "</span>"; ?>

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
									<td data-title='Name' style='<?php echo $borderTop?>' >
									<span class='span-block'><strong>Name:</strong> <?php echo $chname ?></span>
									<span class='span-block'><strong>Check #:</strong> <?php echo escape($s->check_number) ?></span>
									<span class='span-block'><strong>Maturity: </strong><?php echo escape(date('m/d/Y', $s->payment_date)) ?></span>
									<span class='span-block'><strong>Amount:</strong> <?php echo number_format(escape($s->amount), 2); ?></span>
									<span class='span-block'><strong>Bank:</strong> <?php echo escape($s->bank) ?></span>
									</td>
									<td data-title='Status' style='<?php echo $borderTop?>'>
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
									<td data-default_value="<?php echo $s->status; ?>" data-title='' style='width:150px;<?php echo $borderTop?>'>
										<div><input type='radio' value='1' name='rdChequeType<?php echo $s->id ?>' data-id='<?php echo $s->id ?>' class='rdChequeType' <?php echo ($s->status == 1) ? 'checked' : ''; ?>> Ok</div>

										<div><input type='radio' value='2' name='rdChequeType<?php echo $s->id ?>' data-id='<?php echo $s->id ?>' class='rdChequeType' <?php echo ($s->status == 2) ? 'checked' : ''; ?>> DAIF</div>
										<div><input type='radio' value='3' name='rdChequeType<?php echo $s->id ?>' data-id='<?php echo $s->id ?>' class='rdChequeType' <?php echo ($s->status == 3) ? 'checked' : ''; ?>> Bounce</div>
										<div><input type='radio' value='4' name='rdChequeType<?php echo $s->id ?>' data-id='<?php echo $s->id ?>' class='rdChequeType' <?php echo ($s->status == 4) ? 'checked' : ''; ?>> Others</div>

									</td>
									<td style='<?php echo $borderTop?>'>
										<a title='Remarks' href="#" data-id='<?php echo Encryption::encrypt_decrypt('encrypt',$s->id); ?>' class='btn btn-default btnAddRemarks'><i class='fa fa-pencil'></i></a>
										<?php echo $btn; ?>
									</td>

								</tr>
							<?php
							}
								?>
									</tbody>
								</table>
							</div>
						</div>
						<div class='panel panel-default'>
						<div class="panel-body">
							<h4>Total: <?php echo number_format($total_check,2); ?></h4>
						</div>
						</div>
						<?php
						} else {
							?>
							<h3><span class='label label-info'>No Record Found...</span></h3>

						<?php
						}

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
	function criticalLevelPaginate($args, $cid) {
		// pages,
		$user = new User();
		$inv = new Inventory();
		$search = Input::get('search');

		?>
		<div id="no-more-tables">


				<table class='table' id='tblSales'>
					<thead>
					<tr>
						<th>Branch</th>
						<th>Item code</th>
						<th>Current Qty</th>
						<th>Order to</th>
						<th>Critical Level</th>
						<th>To Order</th>
						<th></th>

					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 20;
						$month = date('m');
						$branch = $user->data()->branch_id;
						$countRecord = $inv->countRecordCrit($branch,$month, $search);

						$total_pages = $countRecord->cnt;

						$stages = 4;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}
						$branch_cls = new Branch();
						$branches = $branch_cls->get_active('branches',['company_id','=',$user->data()->company_id]);
						$company_items = $inv->get_crit_record($branch,$month, $start, $limit, $search);
						getpagenavigation($page, $total_pages, $limit, $stages);
						$optbranch = "<select class='form-control choosebranch'>";
						$optbranch .= "<option value=''>Choose branch</option>";
						foreach($branches as $br){
							if($br->id != $user->data()->branch_id){
								$optbranch .= "<option value='$br->id'>$br->name</option>";
							}
						}
						$optbranch .= "</select>";
						if($company_items) {

							foreach($company_items as $s) {
								$supplistopt = '';
								if(isset($s->od2)){ // 1 -- 12
									$orderqty =  $s->odqty2;
									$orderpoint = $s->od2;
									$orderbranch = $s->ob2; // orderto_branch_id
									$supid = $s->os2;
									if($orderbranch && $orderbranch != -2 && $orderbranch != -1){ // -2 = blank branch
										$mybranch = new Branch($orderbranch);
										$branchname = $mybranch->data()->name;
									} else {

										if($supid == 1){
											$supitemcls = new Supplier_item();
											$supplist = $supitemcls->getItemSupBaseOnProducId($user->data()->company_id, $s->item_id);

											if($supplist){
												$supplistopt = "<select class='form-control supchoices'>";
												$supplistopt .= "<option></option>";
												foreach($supplist as $sup){
													$supplistopt .= "<option data-cost='".$sup->purchase_price."' value='".$sup->sid."'>".$sup->supname.":".number_format($sup->purchase_price,2)."</option>";
												}
												$supplistopt .= "<select class='form-control supchoices'> <span class='suppur' style='display:inline-block;'></span>";
											} else {
												$supplistopt = "No available supplier";
											}
										} else {
											if($orderbranch == -2){
												$supplistopt = $optbranch;
											} else {
											$supplistopt = Configuration::getValue('assemble');
											}

										}

										$branchname = $supplistopt;

									}

								} else { // 13
									$orderqty =  $s->odqty1;
									$orderpoint = $s->od1;
									$orderbranch = $s->ob1;
									$supid =$s->os1;
									if($orderbranch){
										$mybranch = new Branch($orderbranch);
										$branchname = $mybranch->data()->name;
									} else {

										$supitemcls = new Supplier_item();
										$supplist = $supitemcls->getItemSupBaseOnProducId($user->data()->company_id, $s->item_id);

										if($supplist){
											$supplistopt = "<select class='form-control supchoices'>";
											$supplistopt .= "<option></option>";
											foreach($supplist as $sup){
												$supplistopt .= "<option data-cost='".$sup->purchase_price."' value='".$sup->sid."'>".$sup->supname.":".number_format($sup->purchase_price,2)."</option>";
											}
											$supplistopt .= "<select class='form-control supchoices'><span class='suppur' style='display:inline-block;'></span>";
										} else {
											$supplistopt = "No available supplier";
										}
										$branchname = $supplistopt;
									}
								}
								$uniqid = uniqid();
								$randnum = rand(0,1000);
								?>
								<tr id='<?php echo  $s->id . $branch .$supid.$orderbranch; ?>' data-assemblelbl='<?php echo Configuration::getValue('assemble'); ?>' data-is_sup='<?php echo $supid; ?>' data-item_id='<?php echo $s->id; ?>' data-branch_id='<?php echo $branch; ?>' data-supplier_id='<?php echo $supid; ?>' data-to_branch='<?php echo $orderbranch; ?>'>
									<td data-title='Branch'><?php echo $s->bname; ?></td>
									<td data-title='Item'><?php echo $s->item_code . "<br><small class='text-danger'>".$s->description."</small>"; ?></td>
									<td data-title='Current Qty'><?php echo $s->qty; ?></td>
									<td data-title='Order To'><?php echo $branchname; ?></td>
									<td data-title='Critical Level'><?php echo $orderpoint; ?></td>
									<td data-title='Order Qty'><input type="text" value="<?php echo $orderqty; ?>" class='form-control'></td>
									<td><button class='btn btn-default btnAdd'><span class='glyphicon glyphicon-plus'></span></button></td>
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
							</tbody>
						</table>
					</div>
				<?php
									?>

							<h3><span class='label label-info'>No Record Found...</span></h3>
							<?php
						}
	}
	function refunds($args, $cid) {
		$page = new Pagination(new Refund());
		$page->setCompanyId($cid);
		$page->setPageNum($args);
		$page->paginate();
	}

	function itemUnits($args, $cid) {
		$page = new Pagination(new Item_unit());
		$page->setCompanyId($cid);
		$page->setPageNum($args);
		$page->paginate();
	}
	function memberCategoryPaginate($args, $cid) {
		$page = new Pagination(new Member_category_discount());
		$page->setCompanyId($cid);
		$page->setPageNum($args);
		$page->paginate();
	}
	function smsToSend($args, $cid) {
		$page = new Pagination(new Sms_to_send());
		$page->setCompanyId($cid);
		$page->setPageNum($args);
		$page->paginate();
	}
	function itemsPaginate($args, $cid) {
		$page = new Pagination(new Product());
		$page->setCompanyId($cid);
		$page->setPageNum($args);
		$page->paginate();
	}
	function memberDeposits($args, $cid){
		$page = new Pagination(new User_credit());
		$page->setCompanyId($cid);
		$page->setPageNum($args);
		$page->paginate();
	}

	function memberEquipmentRequest($args, $cid) {
		$page = new Pagination(new Member_equipment());
		$page->setCompanyId($cid);
		$page->setPageNum($args);
		$page->paginate();
	}

	function cashPaginate($args, $cid) {
		$page = new Pagination(new Cash());
		$page->setCompanyId($cid);
		$page->setPageNum($args);
		$page->paginate();
	}
	function creditPaginate($args, $cid) {
		$page = new Pagination(new Credit());
		$page->setCompanyId($cid);
		$page->setPageNum($args);
		$page->paginate();
	}
	function bankPaginate($args, $cid) {
		$page = new Pagination(new Bank_transfer());
		$page->setCompanyId($cid);
		$page->setPageNum($args);
		$page->paginate();
	}
	function memberEquipmentLog($args, $cid) {
		$page = new Pagination(new Member_equipment_log());
		$page->setCompanyId($cid);
		$page->setPageNum($args);
		$page->paginate();
	}
	function seniorPaginate($args, $cid) {

		$page = new Pagination(new Senior_discount());
		$page->setCompanyId($cid);
		$page->setPageNum($args);
		$page->paginate();

	}
	function pettycashLog($args, $cid) {
		// pages,
		$user = new User();
		$search = Input::get('search');
		$branch_id = Input::get('branch_id');
		$pettylog = new Pettycash_log();

		?>
		<div id="no-more-tables">
			<div class="table-responsive">

				<table class='table' id='tblSales'>
					<thead>
					<tr>
						<TH>Id</TH>
						<th>Branch</th>
						<TH>User</TH>
						<TH>Created</TH>
						<TH>Prev Amount</TH>
						<th>Amount</th>
						<TH>New Amount</TH>
						<th>Remarks</th>
					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 30;
						$countRecord = $pettylog->countRecord($cid, $search,$branch_id);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_reservation = $pettylog->get_record($cid, $start, $limit, $search,$branch_id);
						getpagenavigation($page, $total_pages, $limit, $stages);

						if($company_reservation) {

							foreach($company_reservation as $s) {

								?>
								<tr>
									<td data-title='Id'><span class='badge'><?php echo escape($s->id); ?></span></td>
									<td data-title='Branch'><?php echo escape($s->branch_name); ?></td>
									<td data-title='User'><?php echo escape(ucwords($s->lastname . ", " . $s->firstname . " " . $s->middlename)); ?></td>
									<td data-title='Created'><?php echo escape(date('F d, Y',$s->created)); ?></td>
									<td data-title='Prev Amount'><?php echo escape(number_format($s->prev_amount,2)); ?></td>
									<td data-title='Amount'><?php echo escape(number_format($s->amount,2)); ?></td>
									<td data-title='New Amount'><?php echo escape(number_format($s->new_amount,2)); ?></td>
									<td data-title='Remarks'><?php echo escape($s->remarks); ?></td>

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
	function pettycashRequest($args, $cid) {
		// pages,
		$user = new User();
		$branch_id = Input::get('branch_id');
		$status = Input::get('status');

		$pettyrequest = new Pettycash_request();
		?>
		<div id="no-more-tables">
			<div class="table-responsive">

				<table class='table' id='tblSales'>
					<thead>
					<tr>
						<TH>Id</TH>
						<th>Branch</th>
						<TH>User</TH>
						<TH>Created</TH>
						<th>Amount Requested</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 30;

						if($user->hasPermission('is_franchisee')){
							$branch_id = $user->data()->branch_id ;

						}
						$countRecord = $pettyrequest->countRecord($cid, $status,$branch_id);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;

						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_reservation = $pettyrequest->get_record($cid, $start, $limit, $status,$branch_id);

						getpagenavigation($page, $total_pages, $limit, $stages);

						if($company_reservation) {

							foreach($company_reservation as $s) {
								?>
								<tr>
									<td data-title='Id'><span class='badge'><?php echo escape($s->id); ?></span></td>
									<td data-title='Branch'><?php echo escape($s->branch_name); ?></td>
									<td data-title='User'><?php echo escape(ucwords($s->lastname. ", " . $s->firstname . " " . $s->middlename)); ?></td>
									<td data-title='Created'><?php echo escape(date('F d, Y',$s->created)); ?></td>
									<td data-title='Amount Requested'><?php echo escape(number_format($s->amount,2)); ?></td>
									<td><button data-branch_id='<?php echo $s->branch_id; ?>' class='btn btn-sm btn-default btnBreakdown' data-id="<?php echo Encryption::encrypt_decrypt('encrypt',$s->id); ?>">Breakdown</button></td>
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
			</div>
		</div>
		<?php
	}
	//reservationPaginate
	function pickupPaginate($args, $cid) {
		// pages,
		$user = new User();
		$search = Input::get('search');
		$type = Input::get('type');
		$pickupcls = new Pickup();

		?>
		<div id="no-more-tables">
			<div class="table-responsive">

				<table class='table' id='tblSales'>
					<thead>
					<tr>
						<TH>Id</TH>
						<th>Branch</th>
						<TH>From Branch</TH>
						<TH>Created</TH>
						<TH>Item</TH>
						<th>Quantity</th>
						<TH>Inv/Dr</TH>
						<TH>Member</TH>
						<th>Cashier</th>
						<?php if($type == 2) {
							?>
							<th>Processed by</th>
							<th>Processed date</th>
							<?php
						} else if (!$type || $type == 1){
							?>
							<th></th>
							<?php
						}?>


					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 30;
						$countRecord = $pickupcls->countRecord($cid, $search,$type);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_reservation = $pickupcls->get_pickup_record($cid, $start, $limit, $search,$type);
						getpagenavigation($page, $total_pages, $limit, $stages);

						if($company_reservation) {
							$sales = new Sales();

							foreach($company_reservation as $s) {
								$singlesale = $sales->getsinglesale($s->payment_id);
								$invlbl = '';
								$drlbl = '';
								if($singlesale->invoice){
									$invlbl = "Inv# ".$singlesale->invoice;
								}
								if($singlesale->dr){
									$drlbl = "Dr# ".$singlesale->dr;
								}
								?>
								<tr>
									<td data-title='Id'><span class='badge'><?php echo escape($s->id); ?></span></td>
									<td data-title='Branch'><?php echo escape($s->branch_name); ?></td>
									<td data-title='From'><?php echo escape($s->src_branch_name); ?></td>
									<td data-title='Created'><?php echo escape(date('m/d/Y',$s->created)); ?></td>
									<td data-title='Item'><?php echo escape($s->item_code) . "<br><small class='text-danger'>".$s->description."</small>"; ?></td>
									<td data-title='Quantity'><strong><?php echo escape(number_format($s->qty)); ?></strong></td>
									<td data-title='Ref'><span style='font-size:0.8em' class='label label-danger'><?php echo escape($invlbl . " " . $drlbl); ?></span></td>
									<td data-title='Member'><span class='glyphicon glyphicon-user'></span> <?php echo escape(ucwords($s->mln . ", " . $s->mfn)); ?></td>
									<td data-title='Cashier'><span class='glyphicon glyphicon-user'></span> <?php echo escape(ucwords($s->uln . ", " . $s->ufn));?></td>
									<?php if($s->status == 2) {
										?>
										<td data-title='Processed'><span class='glyphicon glyphicon-user'></span> <?php echo escape(ucwords($s->uln2 . ", " . $s->ufn2));?></td>
										<td data-title='Date'><?php echo escape(date('m/d/Y',$s->processed_date)); ?></td>
										<?php
									} else if ($s->status == 1) {
										?>
										<td>
										<button data-id='<?php echo escape($s->id);?>' class='btn btn-primary btnPickUp'><span class='glyphicon glyphicon-ok'></span></button>
										<button data-id='<?php echo escape($s->id);?>' class='btn btn-danger btnPickUpCancel'><span class='glyphicon glyphicon-remove'></span></button>
										</td>
										<?php
									}?>
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
	function measurementPaginate($args, $cid) {
		// pages,
		$user = new User();
		$member_id = Input::get('member_id');
		$measurement = new Body_measurement();

		?>
		<div id="no-more-tables">
			<div class="table-responsive">

				<table class='table table-bordered' id='tblWithBorder'>
					<thead>
					<tr>
						<TH>Date</TH>
						<th>Member Name</th>
						<TH>Height</TH>
						<TH>Weight</TH>
						<TH>Chest</TH>
						<th>Left Arm</th>
						<th>Right Arm</th>
						<TH>Waist</TH>
						<TH>Abdomen</TH>
						<th>Hips</th>
						<th>Left Thigh</th>
						<th>Right Thigh</th>
						<th>Left Calf</th>
						<th>Right Calf</th>
						<th>Remarks</th>
					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 30;
						$countRecord = $measurement->countRecord($cid, $member_id);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_reservation = $measurement->get_record($cid, $start, $limit, $member_id);
						getpagenavigation($page, $total_pages, $limit, $stages);

						if($company_reservation) {


							foreach($company_reservation as $s) {
									$height_feet = floor($s->height / 12);
									$height_inches = floor($s->height % 12);
								?>
								<tr>
									<td data-title='Date'><?php echo escape(date('m/d/Y',$s->created)); ?></td>
									<td data-title='Member'><?php echo escape($s->member_name); ?></td>
									<td data-title='Height'><?php echo escape($height_feet . "'" . $height_inches); ?></td>
									<td data-title='Weight'><?php echo escape($s->weight . ' lbs'); ?></td>
									<td data-title='Chest'><?php echo escape($s->chest) ; ?></td>
									<td data-title='Left Arm'><?php echo escape($s->l_upperarm) ; ?></td>
									<td data-title='Right Arm'><?php echo escape($s->r_upperarm) ; ?></td>
									<td data-title='Waist'><?php echo escape($s->waist) ; ?></td>
									<td data-title='Abdomen'><?php echo escape($s->abdomen) ; ?></td>
									<td data-title='Hips'><?php echo escape($s->hips) ; ?></td>
									<td data-title='Left Thigh'><?php echo escape($s->l_mid_thigh) ; ?></td>
									<td data-title='Right Thigh'><?php echo escape($s->r_mid_thigh) ; ?></td>
									<td data-title='Left Calf'><?php echo escape($s->l_calf) ; ?></td>
									<td data-title='Right Calf'><?php echo escape($s->r_calf) ; ?></td>
									<td data-title='Remarks'><?php echo escape($s->remarks) ; ?></td>

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
	//reservationPaginate
	function reservationPaginate($args, $cid) {
		// pages,
		$user = new User();
		$order = new Order();
		$search = Input::get('search');
		$status = Input::get('status');
		$b = Input::get('b');

		if(!$user->hasPermission('order')) {
			$user_id = $user->data()->id;
		} else {
			$user_id = 0;
		}

		?>
		<div id="no-more-tables">
			<div class="table-responsive">

				<table class='table' id='tblSales'>
					<thead>
					<tr>
						<TH>Order Id</TH>
						<TH>Ordered By</TH>
						<TH>Branch</TH>
						<TH>Created</TH>
						<TH>Status</TH>
						<th>Sold to</th>
						<TH>Action</TH>

					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 20;
						$countRecord = $order->countRecord($cid, $search, $status, $b, $user_id);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_reservation = $order->get_reservation_record($cid, $start, $limit, $search, $b, $status, $user_id);
						getpagenavigation($page, $total_pages, $limit, $stages);

						if($company_reservation) {
							$orderStatus = array('', 'Pending', 'Sold');
							foreach($company_reservation as $s) {
								$branch = new Branch($s->branch_id);
								$whoorder = new User($s->user_id);
								$membername = 'None';
								if($s->member_id) {
									$member = new Member($s->member_id);
									$membername = escape(ucfirst($member->data()->lastname . ", " . $member->data()->firstname . " " . $member->data()->middlename));
								}
								$stationname = 'None';
								if($s->station_id) {
									$station = new Station($s->station_id);
									$stationname = $station->data()->name;
								}

								?>
								<tr>
									<td data-title="Order id"><?php echo escape($s->id) ?></td>
									<td data-title="Order by"><?php echo escape(ucwords($whoorder->data()->lastname . ", " . $whoorder->data()->firstname)) ?></td>
									<td data-title="Branch"><?php echo escape($branch->data()->name) ?></td>
									<td data-title="Date Created"><?php echo escape(date('m/d/Y H:i:s A', $s->created)) ?></td>
									<td data-title="Status"><?php echo ($s->status == -1) ? 'Decline' : (escape($orderStatus[$s->status])); ?></td>
									<td data-title="Member Name"><?php echo $membername . "</br><small class='text-danger'>$stationname</small>"; ?></td>
									<td>
										<input type="button" class='btn btn-default getorder' value='Show Details' id="<?php echo $s->id; ?>" />
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

	//caravn
	function caravanPaginate($args, $cid) {
		// pages,
		$user = new User();
		$request = new Agent_request();
		$search = Input::get('search');
		$status = Input::get('status');
		$b = Input::get('b');


		if(!$user->hasPermission('caravan_manage')) {
			//by user id
			$user_id = $user->data()->id;
		} else {
			// by company id
			$user_id = 0;

		}

		?>
		<div id="no-more-tables">

				<table class='table' id='tblSales'>
					<thead>
					<tr>
						<TH>Request Id</TH>
						<TH>Ordered By</TH>
						<TH>Branch</TH>
						<TH>Created</TH>
						<TH>Status</TH>
						<TH>Action</TH>
					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";
						$limit = 20;
						$countRecord = $request->countRecord($cid, $search, $status, $b, $user_id);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_req = $request->get_request_record($cid, $start, $limit, $search, $b, $status, $user_id);
						getpagenavigation($page, $total_pages, $limit, $stages);

						if($company_req) {
							$orderStatus = array('', 'Pending', 'Released', 'Liquidated Item', 'Verified', 'Liquidated Sale', 'Approved');
							foreach($company_req as $s) {
								$branch = new Branch($s->branch_id);
								$whoorder = new User($s->user_id);
								if($s->witness) {
									$witness = "<br><small class='text-danger'>" . escape("Witness: " . $s->witness) . "</small>";
								} else {
									$witness = '';
								}
								?>
								<tr>
									<td data-title="Order Id"><?php echo escape($s->id) ?></td>
									<td data-title="Order by"><?php echo escape(ucwords($whoorder->data()->lastname . ", " . $whoorder->data()->firstname)) . $witness; ?></td>
									<td data-title="Branch"><?php echo escape($branch->data()->name) ?></td>
									<td data-title="Created"><?php echo escape(date('m/d/Y H:i:s A', $s->created)) ?></td>
									<td data-title="Status"><?php echo ($s->status == -1) ? 'Decline' : escape($orderStatus[$s->status]) ?></td>
									<td data-title="Action">

										<button  class='btn btn-default getorder' id="<?php echo $s->id; ?>"><span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>Show Details</span></button>
										<button class='btn btn-success timelog' id="<?php echo $s->id; ?>" ><span class='glyphicon glyphicon-time'></span> <span class='hidden-xs'>Time Log</span></button>

									</td>

								</tr>
							<?php
							}
							?>
							</tbody>
							</table>
							</div>

							<?php
						}
						else {
							?>
							</tbody>
							</table>
							</div>

							<h3><span class='label label-info'>No Record...</span></h3>

						<?php
						}
					?>

	<?php
	}

	function terminalMoneyMonitoring($args, $cid) {
		// pages, //aqua
		$user = new User();
		$p_type = Input::get('type');
		$terminal_mon = new Terminal_mon();
		$terminal_id = Input::get('terminal_id');
		$dt1 = Input::get('dt1');
		$dt2 = Input::get('dt2');
		?>
		<div id="no-more-tables">
			<div class="table-responsive">
				<table class='table' id='tblSales'>
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
						$limit = 20;
						$countRecord = $terminal_mon->countRecord($cid, $p_type, $terminal_id,$dt1,$dt2);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_req = $terminal_mon->get_record($cid, $start, $limit, $p_type, $terminal_id,$dt1,$dt2);
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

	function r2Pagination($args, $cid) {
		$include_cancel = Input::get('include_cancel');
		$payment_method = Input::get('payment_method');
		$branch = Input::get('branch');
		$release_branch_id = Input::get('release_branch_id');
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
		$doc_type = Input::get('doc_type');

		$region = Input::get('region');
		$rdRegion = Input::get('rdRegion');
		$report_type = Input::get('report_type');
		$sort_by = Input::get('sort_by');
		$custom_string_query = Input::get('custom_string_query');
		$date_type = Input::get('date_type');

		$filterinfo = "";
		if(!($dateStart && $dateEnd)){
			echo "<div class='alert alert-danger'>Please choose dates first.</div>";
			exit();
		}

		if($report_type == 1) {
			$sales = new Sales();
			$user = new User();
			// cash cheque
			$crud = new Crud();
			$classSalestype = new Sales_type();
			$salestypelist = $classSalestype->get_active('salestypes', array('company_id', '=', $user->data()->company_id));

			//$inconsistent = $sales->getInconsistentData($cid,$user->data()->branch_id);
			$inconsistent = [];
			if($inconsistent){

				echo "<div class='well'>";
				echo "<p class='text-danger'><strong>You have unmatched sales total and payment total.</strong></p>";
				echo "<hr>";
				foreach($inconsistent as $incon){
					$invlabel='';
					$drlabel ='';
					$irlabel ='';
					if($incon->invoice){
						$invlabel="Invoice#".$incon->invoice;
					}
					if($incon->dr){
						$drlabel="Dr#".$incon->dr;
					}
					if($incon->ir){
						$irlabel="Ir#".$incon->ir;
					}
					$sdunmatched = date('m/d/Y',$incon->sold_date);
					$alltotal = $incon->cashamount + $incon->chequeamount + $incon->btamount + $incon->ccamount + $incon->mcamount + $incon->pcamount+ $incon->pcfamount+ $incon->deduction;
					$alltotal = number_format($alltotal,2);
					echo "<p> $sdunmatched $invlabel $drlabel $irlabel Sales Total=".$incon->ttotal.", Payment Total=$alltotal <a class='btn btn-default pull-right' href='sales_crud.php?id=".Encryption::encrypt_decrypt('encrypt',$incon->payment_id)."'><span class='glyphicon glyphicon-pencil'> Edit</a></p>";
					echo "<p><small>(Cash=<span class='text-danger'>$incon->cashamount</span>, Cheque=<span class='text-danger'>$incon->chequeamount</span>, Credit Card=<span class='text-danger'>$incon->ccamount</span>, Bank Transfer=<span class='text-danger'>$incon->btamount</span>, Member Credit=<span class='text-danger'>$incon->mcamount</span>, Consumable=<span class='text-danger'>$incon->pcamount</span>, Consumable freebies=<span class='text-danger'>$incon->pcfamount</span>, Deduction=<span class='text-danger'>$incon->deduction</span>)</small></p>";
					echo "<hr>";
				}
				echo "</div>";
			}




			?>

			<div id="no-more-tables">
			<div class="table-responsive">

			<table class='table' id='tblSales'>
			<thead>
			<tr>
				<th title='Sort by branch' data-sort='order by b.name ' class='page_sortby'>Branch</th>
				<th title='Sort by member' data-sort='order by m.lastname ' class='page_sortby'><?php echo MEMBER_LABEL; ?></th>
				<TH title='Sort by invoice' data-sort=' order by IF (IFNULL(s.invoice,0) = 0, 1, 0), s.invoice * 1 ' class='page_sortby'><?php echo INVOICE_LABEL; ?></TH>
				<TH title='Sort by dr' data-sort=' order by IF (IFNULL(s.dr,0) = 0, 1, 0), s.dr * 1 ' class='page_sortby'> <?php echo DR_LABEL; ?></TH>
				<TH title='Sort by dr' data-sort=' order by IF (IFNULL(s.ir,0) = 0, 1, 0), s.ir * 1 ' class='page_sortby'><?php echo PR_LABEL; ?></TH>
				<TH title='Sort by dr' data-sort=' order by IF (IFNULL(s.sv,0) = 0, 1, 0), s.sv * 1 ' class='page_sortby'><?php echo "SV"; ?></TH>
				<TH title='Sort by item' data-sort='order by it.item_code ' class='page_sortby'>Item Code</TH>
				<TH title='Sort by price' data-sort='order by pr.price ' class='page_sortby text-right'>Price</TH>
				<TH title='Sort by quantity' data-sort='order by s.qtys ' class='page_sortby text-right'>Qty</TH>
				<TH title='Sort by discount' data-sort='order by s.discount ' class='page_sortby text-right'>Discount</TH>
				<TH  class='text-right'>Adjustment</TH>
				<TH title='Sort by total' data-sort='order by ((s.qtys*pr.price)-s.discount) ' class='page_sortby text-right'>Total</TH>
<TH title='Sort by date'>Date Sold</TH>
					<?php if(Configuration::thisCompany('cebuhiq')){
							?>
								<TH title='Sort by date'>Del Date</TH>
							<?php } ?>
				<th></th>
				<th></th>
			</tr>
			</thead>
			<tbody>
			<?php
				//$targetpage = "paging.php";
				$limit = 100;
				$countRecord = $sales->countRecordR2($cid, $payment_method, $branch, $terminal, $item_type, $category, $memid, $stationid, $dateStart, $dateEnd, $cashier, $item_id, $sales_type,$from_od,$from_service,$doc_type,$custom_string_query,$release_branch_id,$date_type);
				if($dateStart && $dateEnd){
				$totalCash = $sales->getTotalSalesR2($cid, $payment_method, $branch, $terminal, $item_type, $category, $memid, $stationid, $dateStart, $dateEnd, $cashier, $item_id, $sales_type, 'cash',$from_od,$from_service,$doc_type,$custom_string_query,$release_branch_id,$date_type);
				$totalCaravan = $sales->getTotalSalesR2($cid, $payment_method, $branch, $terminal, $item_type, $category, $memid, $stationid, $dateStart, $dateEnd, $cashier, $item_id, $sales_type, 'caravan',$from_od,$from_service,$doc_type,$custom_string_query,$release_branch_id,$date_type);
				$totalCheque = $sales->getTotalSalesR2($cid, $payment_method, $branch, $terminal, $item_type, $category, $memid, $stationid, $dateStart, $dateEnd, $cashier, $item_id, $sales_type, 'cheque',$from_od,$from_service,$doc_type,$custom_string_query,$release_branch_id,$date_type);
				$totalCreditCard = $sales->getTotalSalesR2($cid, $payment_method, $branch, $terminal, $item_type, $category, $memid, $stationid, $dateStart, $dateEnd, $cashier, $item_id, $sales_type, 'credit_card',$from_od,$from_service,$doc_type,$custom_string_query,$release_branch_id,$date_type);
				$totalBankTransfer = $sales->getTotalSalesR2($cid, $payment_method, $branch, $terminal, $item_type, $category, $memid, $stationid, $dateStart, $dateEnd, $cashier, $item_id, $sales_type, 'bank_transfer',$from_od,$from_service,$doc_type,$custom_string_query,$release_branch_id,$date_type);
				$totalConsumables = $sales->getTotalSalesR2($cid, $payment_method, $branch, $terminal, $item_type, $category, $memid, $stationid, $dateStart, $dateEnd, $cashier, $item_id, $sales_type, 'payment_consumable',$from_od,$from_service,$doc_type,$custom_string_query,$release_branch_id,$date_type);
				$totalConsumablesFreebies = $sales->getTotalSalesR2($cid, $payment_method, $branch, $terminal, $item_type, $category, $memid, $stationid, $dateStart, $dateEnd, $cashier, $item_id, $sales_type, 'payment_consumable_freebies',$from_od,$from_service,$doc_type,$custom_string_query,$release_branch_id,$date_type);
				$totalMemberCredit = $sales->getTotalSalesR2($cid, $payment_method, $branch, $terminal, $item_type, $category, $memid, $stationid, $dateStart, $dateEnd, $cashier, $item_id, $sales_type, 'member_credit',$from_od,$from_service,$doc_type,$custom_string_query,$release_branch_id,$date_type);
				$totalDeduction = $sales->getTotalSalesR2($cid, $payment_method, $branch, $terminal, $item_type, $category, $memid, $stationid, $dateStart, $dateEnd, $cashier, $item_id, $sales_type, 'deductions',$from_od,$from_service,$doc_type,$custom_string_query,$release_branch_id,$date_type);

				}

				$tablestring2 = "<ul class='list-group'>";
				$totalstl = 0;
				$arrtype=[];
				if($sales_type){
					 $arrtype = $sales_type;
				}
				if($salestypelist){
					foreach($salestypelist as $stl) {
						if(count($arrtype) > 0){
							if (!in_array($stl->id,$arrtype)){
								continue;
							}
						}
						$stlres = $sales->getTotalSalesPerSalesType($cid, $dateStart, $dateEnd, $stl->id,$memid,$from_od,0,$from_service,$doc_type);
						$stlamount = (isset($stlres->saletotal)) ? $stlres->saletotal : 0;
						$totalstl += $stlamount;
						$tablestring2 .= "<li class='list-group-item'> <span class='pull-right text-danger'> " . number_format($stlamount, 2) . "</span><strong> $stl->name</strong></li>";
					}
				}


					if(count($arrtype) > 0){

						if (in_array(-1,$arrtype) && Configuration::isAquabest()){
						$stlrescaravan = $sales->getTotalSalesPerSalesType($cid, $dateStart, $dateEnd, -1);
						$stlamountcaravan = (isset($stlrescaravan->saletotal)) ? $stlrescaravan->saletotal : 0;
							$tablestring2 .= "<li class='list-group-item'> <span class='pull-right text-danger'> " . number_format($stlamountcaravan, 2) . "</span><strong>Caravan</strong></li>";
						} else {
						$stlamountcaravan = 0;
						}
						if (in_array(0,$arrtype)){
							$stlresnotype = $sales->getTotalSalesPerSalesType($cid, $dateStart, $dateEnd, 0);
							$stlresnotypeamount = (isset($stlresnotype->saletotal)) ? $stlresnotype->saletotal : 0;
							$tablestring2 .= "<li class='list-group-item'> <span class='pull-right text-danger'> " . number_format($stlresnotypeamount, 2) . "</span><strong>No Type</strong></li>";
						} else {
							$stlresnotypeamount = 0;
						}
					} else {
						if(Configuration::isAquabest()){
							$stlrescaravan = $sales->getTotalSalesPerSalesType($cid, $dateStart, $dateEnd, -1);
							$stlamountcaravan = (isset($stlrescaravan->saletotal)) ? $stlrescaravan->saletotal : 0;
							$tablestring2 .= "<li class='list-group-item'> <span class='pull-right text-danger'> " . number_format($stlamountcaravan, 2) . "</span><strong>Caravan</strong></li>";
						}

						$stlresnotype = $sales->getTotalSalesPerSalesType($cid, $dateStart, $dateEnd, 0);
						$stlresnotypeamount = (isset($stlresnotype->saletotal)) ? $stlresnotype->saletotal : 0;
						$tablestring2 .= "<li class='list-group-item'> <span class='pull-right text-danger'> " . number_format($stlresnotypeamount, 2) . "</span><strong>No Type</strong></li>";
					}

				$totalstl += $stlamountcaravan;

				$totalstl += $stlresnotypeamount;


				$tablestring2 .= "<li class='list-group-item'> <strong><span class='pull-right'> " . number_format($totalstl, 2) . "</span></strong><strong>TOTAL</strong></li>";
				$tablestring2 .= "</ul>";


				$total_pages = $countRecord->cnt;

				$stages = 3;
				$page = ($args);
				$page = (int) $page;
				if($page) {
					$start = ($page - 1) * $limit;
				} else {
					$start = 0;
				}


				$company_sales = $sales->getSalesR2($cid, $start, $limit, $payment_method, $branch, $terminal, $item_type, $category, $memid, $stationid, $dateStart, $dateEnd, $cashier, $item_id, $sales_type, $sort_by,$from_od,$from_service,$doc_type,$custom_string_query,$release_branch_id,$date_type);

				getpagenavigation($page, $total_pages, $limit, $stages);

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
						$p_length = $sss->countPaymentLength($s->payment_id, $start, $limit);
						if($prevpid != $s->payment_id) {
							$bordertop = "style='border-top:1px solid #ccc;'";
						} else {
							$bordertop = '';
						}
						$totalsales += (($s->qtys * $price->price) + $s->adjustment) - ($s->discount + $s->store_discount);

						$ind_adjustment =  0;
						if($s->adjustment){
						$ind_adjustment = $s->adjustment / $s->qtys;
						}
						$bundle_tr ="";
						if($s->is_bundle){
							$bundle = new Bundle();
							$bundle_list = $bundle->getBundleItem($s->item_id);
							if($bundle_list){
								foreach($bundle_list as $bundle_item){
									$bundle_tr .= "<tr>";
									$bundle_tr .= "<td></td><td></td><td></td><td></td><td></td><td></td>";
									$bundle_desc = "<small class='text-danger'>" . $bundle_item->description . "</small>";
									$bundle_tr .= "<td>". $bundle_item->item_code." $bundle_desc</td>";
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
						<tr <?php echo $bordertop; ?> >
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
							<td data-title='<?php echo MEMBER_LABEL; ?>'>
								<?php
									if(!$s->member_name) {
										$mem_name = "N/A";
									} else {
										$mem_name = $s->member_name;
									}
									echo "<strong>" . capitalize($mem_name) . "</strong>";
								?>
							</td>
							<td data-title='Invoice'>
								<?php echo ($s->invoice) ? escape(returnWithPrefAndSuf($s,1)) : "<i class='fa fa-ban'></i>"; ?>
							</td>

							<td data-title='Dr'>
								<?php echo ($s->dr) ? escape(returnWithPrefAndSuf($s,2)) : "<i class='fa fa-ban'></i>" ?>
							</td>
							<td data-title='PR'>
								<?php echo ($s->ir) ? escape(returnWithPrefAndSuf($s,3)) : "<i class='fa fa-ban'></i>"; ?>
							</td>
							<td data-title='SV'>
								<?php echo ($s->sv) ? escape(returnWithPrefAndSuf($s,4)) : "<i class='fa fa-ban'></i>"; ?>
							</td>

							<td data-title='Item'><?php echo escape($pd->data()->item_code) . "<br><small class='text-danger'>" . escape($pd->data()->description) . "</small>"; ?></td>
							<td data-title='Price' class='text-right'><?php echo escape(number_format(($price->price + $ind_adjustment), 2)); ?>
							</td>
							<td data-title='Quantity' class='text-right'><?php echo formatQuantity($s->qtys) ?></td>
							<td data-title='Discount' class='text-right'><?php echo escape(number_format($s->discount+$s->store_discount, 2)) ?></td>
							<td data-title='Adjustment' class='text-right'><?php echo escape(number_format($s->member_adjustment, 2)) ?></td>
							<td data-title='Total' class='text-danger text-right'>
								<strong><?php echo escape(number_format((($s->qtys * $price->price) + $s->adjustment + $s->member_adjustment) - ($s->discount + $s->store_discount), 2)) ?></strong>
							</td>
							<td data-title='Date sold'><?php echo escape(date('m/d/Y ', $s->sold_date)); ?></td>
							<?php if(Configuration::thisCompany('cebuhiq')){
							?>
							<td data-title='Delivery Date'><?php echo escape(date('m/d/Y ', $s->is_scheduled)); ?></td>
							<?php } ?>
							<td>
								<?php
									if($s->status == 1) {
									echo "<span class='text-danger'>Cancelled</span>";
								}
								?>
							</td>
							<td <?php
									if($prevpid != $s->payment_id)  { ?>
									data-title='Action'
									<?php } ?>
									>
								<?php
									if($prevpid != $s->payment_id) {
										$matchpayment = $sales->matchPaymentSales($cid,$s->payment_id);
										$m_total = $matchpayment->ttotal;
										$m_allpayment = $matchpayment->cashamount  + $matchpayment->chequeamount + $matchpayment->btamount + $matchpayment->ccamount + $matchpayment->mcamount + $matchpayment->pcamount+ $matchpayment->pcfamount+ $matchpayment->deduction;
										?>
										<button title='Payment Details' data-payment_id='<?php echo $s->payment_id ?>' class='btn btn-default btn-sm paymentDetails '>
											<i class='fa fa-list'></i>
										</button>
										<br>
										<?php
										if($m_total != $m_allpayment){
											$m_allpayment = number_format($m_allpayment,2);
											$m_total = number_format($m_total,2);
											//echo "<br><span style='font-size:1em;' class='label label-danger'>Total amount and payment amount did not matched. (Total=$m_total, Payment=$m_allpayment)</span>";
										}
										?>
										<?php
										$prevpid = $s->payment_id;
									} else {

											echo "<button style='visibility:hidden;'></button>";

									}
								?>
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
			<hr />
			<?php if($dateStart && $dateEnd){ ?>
			<div class="row">
				<div class="col-md-6">
					<ul class="list-group">
						<li class="list-group-item">
							<span class="pull-right text-danger"><?php echo number_format(($totalCash->totalamount + $totalCaravan->totalamount), 2); ?></span>
							<strong>Cash</strong>
						</li>
						<!--	<li class="list-group-item">
					<span class="pull-right text-danger"><?php echo number_format($totalCaravan->totalamount, 2); ?></span>
					<strong>Caravan</strong>
				</li> -->
						<li class="list-group-item">
							<span class="pull-right text-danger"><?php echo number_format($totalCheque->totalamount, 2); ?></span>
							<strong>Cheque</strong>
						</li>
						<li class="list-group-item">
							<span class="pull-right text-danger"><?php echo number_format($totalCreditCard->totalamount, 2); ?></span>
							<strong>Credit Card</strong>
						</li>
						<li class="list-group-item">
							<span class="pull-right text-danger"><?php echo number_format($totalBankTransfer->totalamount, 2); ?></span>
							<strong>Bank Transfer</strong>
						</li>
						<li class="list-group-item">
							<span class="pull-right text-danger"><?php echo number_format($totalConsumables->totalamount, 2); ?></span>
							<strong>Consumables</strong>
						</li>
						<li class="list-group-item">
							<span class="pull-right text-danger"><?php echo number_format($totalConsumablesFreebies->totalamount, 2); ?></span>
							<strong>Freebies</strong>
						</li>
						<li class="list-group-item">
							<span class="pull-right text-danger"><?php echo number_format($totalMemberCredit->totalamount, 2); ?></span>
							<strong>Member Credit</strong>
						</li>
						<li class="list-group-item">
							<span class="pull-right text-danger"><?php echo number_format($totalDeduction->totalamount, 2); ?></span>
							<strong>Deduction</strong>
						</li>
						<?php $superdupertotal = number_format(($totalCash->totalamount + $totalCaravan->totalamount + $totalCheque->totalamount + $totalCreditCard->totalamount + $totalBankTransfer->totalamount + $totalConsumables->totalamount + $totalConsumablesFreebies->totalamount + $totalMemberCredit->totalamount + $totalDeduction->totalamount), 2); ?>
						<li class="list-group-item">
							<span class="pull-right"><strong><?php echo $superdupertotal; ?></strong></span>
							<strong>Grand Total</strong>
						</li>
					</ul>
				</div>
				<div class="col-md-6"><?php echo $tablestring2; ?></div>
			</div>
			<?php } ?>
		<?php
		} else if($report_type == 2) {
			$sales = new Sales();
			$user = new User();
			// cash cheque
			$crud = new Crud();
			$classSalestype = new Sales_type();
			$salestypelist = $classSalestype->get_active('salestypes', array('company_id', '=', $user->data()->company_id));
			?>
			<div id="no-more-tables">

					<table class='table' id='tblSales'>
						<thead>
						<tr>
							<th data-sort=' order by b.name ' class='page_sortby'>Branch</th>
							<TH data-sort='order by IF (IFNULL(s.invoice,0) = 0, 1, 0), s.invoice * 1 ' class='page_sortby'><?php echo INVOICE_LABEL; ?></TH>
							<TH data-sort='order by IF (IFNULL(s.dr,0) = 0, 1, 0), s.dr * 1 ' class='page_sortby'><?php echo DR_LABEL; ?></TH>
							<TH data-sort='order by IF (IFNULL(s.ir,0) = 0, 1, 0), s.ir * 1 ' class='page_sortby'><?php echo PR_LABEL; ?></TH>
							<TH data-sort='order by IF (IFNULL(s.sv,0) = 0, 1, 0), s.sv * 1 ' class='page_sortby'><?php echo "SV"; ?></TH>
							<TH data-sort=' order by totalamount ' class='page_sortby' >Total Sales</TH>
							<?php if(Configuration::thisCompany('cebuhiq')){
							?>
							<TH data-sort=' order by s.sold_date ' class='page_sortby'>Delivery Date</TH>
							<?php
							} ?>
							<TH data-sort=' order by s.sold_date ' class='page_sortby'>Date sold</TH>
							<th>Sold to</th>
							<th>Sales type</th>
							<th></th>
						</tr>
						</thead>
						<tbody>
						<?php
							//$targetpage = "paging.php";
							$limit = 100;
							$countRecord = $sales->countRecordR2v2($cid, $payment_method, $branch, $terminal, $item_type, $category, $memid, $stationid, $dateStart, $dateEnd, $cashier, $item_id, $sales_type,$sort_by,$from_od,$from_service,$doc_type,$release_branch_id,$date_type,$include_cancel);
						if($dateStart && $dateEnd){
							$totalCash = $sales->getTotalSalesR2($cid, $payment_method, $branch, $terminal, $item_type, $category, $memid, $stationid, $dateStart, $dateEnd, $cashier, $item_id, $sales_type, 'cash',$from_od,$from_service,$doc_type,'',$release_branch_id,$date_type);
							$totalCaravan = $sales->getTotalSalesR2($cid, $payment_method, $branch, $terminal, $item_type, $category, $memid, $stationid, $dateStart, $dateEnd, $cashier, $item_id, $sales_type, 'caravan',$from_od,$from_service,$doc_type,'',$release_branch_id,$date_type);
							$totalCheque = $sales->getTotalSalesR2($cid, $payment_method, $branch, $terminal, $item_type, $category, $memid, $stationid, $dateStart, $dateEnd, $cashier, $item_id, $sales_type, 'cheque',$from_od,$from_service,$doc_type,'',$release_branch_id,$date_type);
							$totalCreditCard = $sales->getTotalSalesR2($cid, $payment_method, $branch, $terminal, $item_type, $category, $memid, $stationid, $dateStart, $dateEnd, $cashier, $item_id, $sales_type, 'credit_card',$from_od,$from_service,$doc_type,'',$release_branch_id,$date_type);
							$totalBankTransfer = $sales->getTotalSalesR2($cid, $payment_method, $branch, $terminal, $item_type, $category, $memid, $stationid, $dateStart, $dateEnd, $cashier, $item_id, $sales_type, 'bank_transfer',$from_od,$from_service,$doc_type,'',$release_branch_id,$date_type);
							$totalConsumables = $sales->getTotalSalesR2($cid, $payment_method, $branch, $terminal, $item_type, $category, $memid, $stationid, $dateStart, $dateEnd, $cashier, $item_id, $sales_type, 'payment_consumable',$from_od,$from_service,$doc_type,'',$release_branch_id,$date_type);
							$totalConsumablesFreebies = $sales->getTotalSalesR2($cid, $payment_method, $branch, $terminal, $item_type, $category, $memid, $stationid, $dateStart, $dateEnd, $cashier, $item_id, $sales_type, 'payment_consumable_freebies',$from_od,$from_service,$doc_type,'',$release_branch_id,$date_type);
							$totalMemberCredit = $sales->getTotalSalesR2($cid, $payment_method, $branch, $terminal, $item_type, $category, $memid, $stationid, $dateStart, $dateEnd, $cashier, $item_id, $sales_type, 'member_credit',$from_od,$from_service,$doc_type,'',$release_branch_id,$date_type);
							$totalDeduction = $sales->getTotalSalesR2($cid, $payment_method, $branch, $terminal, $item_type, $category, $memid, $stationid, $dateStart, $dateEnd, $cashier, $item_id, $sales_type, 'deductions',$from_od,$from_service,$doc_type,'',$release_branch_id,$date_type);
							}
							$tablestring2 = "<ul class='list-group'>";
							$totalstl = 0;
							$arrtype=[];

							if($sales_type){
								 $arrtype = $sales_type;
							}

							foreach($salestypelist as $stl) {
									if(count($arrtype) > 0){
										if (!in_array($stl->id,$arrtype)){
											continue;
										}
									}
								$stlres = $sales->getTotalSalesPerSalesType($cid, $dateStart, $dateEnd, $stl->id,$memid,$from_od,$branch,$from_service);
								$stlamount = (isset($stlres->saletotal)) ? $stlres->saletotal : 0;
								$totalstl += $stlamount;
								$tablestring2 .= "<li class='list-group-item'> <span class='pull-right text-danger'> " . number_format($stlamount, 2) . "</span><strong> $stl->name</strong></li>";
							}
							$stlrescaravan = $sales->getTotalSalesPerSalesType($cid, $dateStart, $dateEnd, -1);
							$stlamountcaravan = (isset($stlrescaravan->saletotal)) ? $stlrescaravan->saletotal : 0;
							$totalstl += $stlamountcaravan;
							$stlresnotype = $sales->getTotalSalesPerSalesType($cid, $dateStart, $dateEnd, 0);
							$stlresnotypeamount = (isset($stlresnotype->saletotal)) ? $stlresnotype->saletotal : 0;
							$totalstl += $stlresnotypeamount;
							$tablestring2 .= "<li class='list-group-item'> <span class='pull-right text-danger'> " . number_format($stlamountcaravan, 2) . "</span><strong>Caravan</strong></li>";
							$tablestring2 .= "<li class='list-group-item'> <span class='pull-right text-danger'> " . number_format($stlresnotypeamount, 2) . "</span><strong>No Type</strong></li>";
							$tablestring2 .= "<li class='list-group-item'> <strong><span class='pull-right'> " . number_format($totalstl, 2) . "</span></strong><strong>TOTAL</strong></li>";
							$tablestring2 .= "</ul>";


							$total_pages = $countRecord->cnt;

							$stages = 3;
							$page = ($args);
							$page = (int)$page;
							if($page) {
								$start = ($page - 1) * $limit;
							} else {
								$start = 0;
							}

							$company_sales = $sales->getSalesR2v2($cid, $start, $limit, $payment_method, $branch, $terminal, $item_type, $category, $memid, $stationid, $dateStart, $dateEnd, $cashier, $item_id, $sales_type,$sort_by,$from_od,$from_service,$doc_type,$release_branch_id,$date_type,$include_cancel);

							getpagenavigation($page, $total_pages, $limit, $stages);

							if($company_sales) {

								$prevpid = 0;
								$totalsales = 0;

								foreach($company_sales as $s) {
									$inv = "<i class='fa fa-ban'></i>";
									$dr =  "<i class='fa fa-ban'></i>";
									$ir =  "<i class='fa fa-ban'></i>";
									$sv =  "<i class='fa fa-ban'></i>";

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
									if($s->invoice) {
										$inv = returnWithPrefAndSuf($s,1);
									}
									if($s->dr) {
										$dr  = returnWithPrefAndSuf($s,2);
									}
									if($s->ir) {
										$ir  = returnWithPrefAndSuf($s,3);
									}
									if($s->sv) {
										$sv  = returnWithPrefAndSuf($s,4);
									}
									$member_name = 'None';
									if($s->mln ) {
										$member_name = $s->mln;
									}
									$reserved_by = "";
									if(isset($s->reserved_by)) {
										$reservedby = new User($s->reserved_by);
										$reserved_by = $reservedby->data()->lastname . ", " . $reservedby->data()->firstname;
									}
									$cashier = "";
									if(isset($s->cashier_id)) {
										$cashiercls = new User($s->cashier_id);
										$cashier = $cashiercls->data()->lastname . ", " . $cashiercls->data()->firstname;
									}
									$cancel_status = "";
									if($s->status == 1){
										$cancel_status = 'Cancelled';
									}
									?>
									<tr>
										<td data-title='Branch' style='border-bottom:1px solid #ccc;'>
											<strong><?php echo ($s->bname) ? escape($s->bname) :'Caravan';  ?></strong></td>
										<td data-title='Invoice' style='border-bottom:1px solid #ccc;'><?php echo ($inv) ?></td>
										<td data-title='Dr' style='border-bottom:1px solid #ccc;'><?php echo ($dr) ?></td>
										<td data-title='PR' style='border-bottom:1px solid #ccc;'><?php echo ($ir) ?></td>
										<td data-title='SV' style='border-bottom:1px solid #ccc;'><?php echo ($sv) ?></td>
										<td data-title='Total' style='border-bottom:1px solid #ccc;' class='text-danger'>
											<strong><?php echo escape(number_format($s->totalamount, 2)) ?></strong>
										</td>
											<?php if(Configuration::thisCompany('cebuhiq')){	?>
										<td data-title='Delivery Date' style='border-bottom:1px solid #ccc;'><?php echo escape(date('m/d/Y', $s->is_scheduled)); ?></td>
										<?php } ?>
										<td data-title='Date sold' style='border-bottom:1px solid #ccc;'><?php echo escape(date('m/d/Y', $s->sold_date)); ?></td>
										<td data-title='Sold To' style='border-bottom:1px solid #ccc;' class='text-success'>
										<?php echo escape(ucwords($member_name)) ?>
										<span class='text-danger span-block'><?php echo $cancel_status; ?></span>
										</td>
										<td data-title='Reserved by' style='border-bottom:1px solid #ccc;' class=''>
											<?php
												echo escape(ucwords($s->sales_type_name));
											?>
										</td>
										<td style='border-bottom:1px solid #ccc;'>

											<button data-payment_id='<?php echo $s->payment_id; ?>' class='btn btn-default btn-sm getPTDetails'>
												<i class='fa fa-list'></i></button>

													<button data-payment_id='<?php echo $s->payment_id; ?>' class='btn btn-default btn-sm paymentDetails'>
												<i class='fa fa-money'></i></button>
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

			<hr />
<?php if($dateStart && $dateEnd){ ?>
			<div class="row">
				<div class="col-md-6">
					<ul class="list-group">
						<li class="list-group-item">
							<span class="pull-right text-danger"><?php echo number_format(($totalCash->totalamount + $totalCaravan->totalamount), 2); ?></span>
							<strong>Cash</strong>
						</li>
						<!--	<li class="list-group-item">
					<span class="pull-right text-danger"><?php echo number_format($totalCaravan->totalamount, 2); ?></span>
					<strong>Caravan</strong>
				</li> -->
						<li class="list-group-item">
							<span class="pull-right text-danger"><?php echo number_format($totalCheque->totalamount, 2); ?></span>
							<strong>Cheque</strong>
						</li>
						<li class="list-group-item">
							<span class="pull-right text-danger"><?php echo number_format($totalCreditCard->totalamount, 2); ?></span>
							<strong>Credit Card</strong>
						</li>
						<li class="list-group-item">
							<span class="pull-right text-danger"><?php echo number_format($totalBankTransfer->totalamount, 2); ?></span>
							<strong>Bank Transfer</strong>
						</li>
						<li class="list-group-item">
							<span class="pull-right text-danger"><?php echo number_format($totalConsumables->totalamount, 2); ?></span>
							<strong>Consumables</strong>
						</li>
						<li class="list-group-item">
							<span class="pull-right text-danger"><?php echo number_format($totalConsumablesFreebies->totalamount, 2); ?></span>
							<strong>Freebies</strong>
						</li>
						<li class="list-group-item">
							<span class="pull-right text-danger"><?php echo number_format($totalMemberCredit->totalamount, 2); ?></span>
							<strong>Member Credit</strong>
						</li>
						<li class="list-group-item">
							<span class="pull-right text-danger"><?php echo number_format($totalDeduction->totalamount, 2); ?></span>
							<strong>Deductions</strong>
						</li>
						<?php $superdupertotal = number_format(($totalCash->totalamount + $totalCaravan->totalamount + $totalCheque->totalamount + $totalCreditCard->totalamount + $totalBankTransfer->totalamount + $totalConsumables->totalamount + $totalConsumablesFreebies->totalamount + $totalMemberCredit->totalamount+ $totalDeduction->totalamount), 2); ?>
						<li class="list-group-item">
							<span class="pull-right"><strong><?php echo $superdupertotal; ?></strong></span>
							<strong>Grand Total</strong>
						</li>
					</ul>
				</div>
				<div class="col-md-6"><?php echo $tablestring2; ?></div>
			</div>
			<?php }  ?>
		<?php
		}
	}
	function r3Pagination($args, $cid) {
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
			$list = $sales->getSalesR3v3($cid,$dateStart,$dateEnd,$sales_type);
			if($list){
//<th class='text-right'>Cash</th><th class='text-right'>Cheque</th><th class='text-right'>Credit</th><th  class='text-right'>Bank Transfer</th><th class='text-right'>Deduction</th><th class='text-right'>Unpaid</th>
				echo "<table class='table' id='tblForApproval'>";
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
			//echo "<td class='text-right'>".number_format($bt,2)."</td>";
			//echo "<td class='text-right'>".number_format($deduction,2)."</td>";
			//echo "<td class='text-right'>".number_format($member_credit,2)."</td>";
				echo "</tr>";
				}

				echo "</tbody>";
				echo "<tr><th style='border-top: 1px solid #ccc;'></th><th style='border-top: 1px solid #ccc;'></th><th style='border-top: 1px solid #ccc;'></th><th style='border-top: 1px solid #ccc;'></th><th style='border-top: 1px solid #ccc;'></th>";
				echo "<th class='text-right' style='border-top: 1px solid #ccc;'>".number_format($total_sales,2)."</th>";
				//echo "<th class='text-right'  style='border-top: 1px solid #ccc;'>".number_format($total_cash,2)."</th>";
				//echo "<th class='text-right'  style='border-top: 1px solid #ccc;'>".number_format($total_cheque,2)."</th>";
				//echo "<th class='text-right'   style='border-top: 1px solid #ccc;'>".number_format($total_credit,2)."</th>";
				//echo "<th class='text-right'  style='border-top: 1px solid #ccc;'>".number_format($total_bt,2)."</th>";
				//echo "<th class='text-right'  style='border-top: 1px solid #ccc;'>".number_format($total_deduction,2)."</th>";
				//echo "<th class='text-right'  style='border-top: 1px solid #ccc;'>".number_format($total_member,2)."</th>";
				echo "</tr>";
				echo "<tr><th style='border-top: 1px solid #ccc;'></th><th style='border-top: 1px solid #ccc;'></th><th style='border-top: 1px solid #ccc;'></th><th style='border-top: 1px solid #ccc;'></th><th style='border-top: 1px solid #ccc;'></th><th style='border-top: 1px solid #ccc;'></th>";
			//	echo "<th class='text-right' style='border-top: 1px solid #ccc;display:none;'>".number_format($total_cash + $total_cheque + $total_credit + $total_bt + $total_deduction + $total_member ,2)."</th>";
				//echo "<th class='text-right'  style='border-top: 1px solid #ccc;'></th>";
				//echo "<th class='text-right'  style='border-top: 1px solid #ccc;'></th>";
				//echo "<th class='text-right'   style='border-top: 1px solid #ccc;'></th>";
				//echo "<th class='text-right'  style='border-top: 1px solid #ccc;'></th>";
				//echo "<th class='text-right'  style='border-top: 1px solid #ccc;'></th>";
				//echo "<th class='text-right'  style='border-top: 1px solid #ccc;'></th>";
				//echo "<th class='text-right'  style='border-top: 1px solid #ccc;'></th>";
				echo "</tr>";
				echo "</table>";
			} else {
			echo "No record";
			}
		} else if($report_type == 2) {

		}
	}
function discountPaginate($args,$cid){
			$search = Input::get('search');
			$user = new User();
			$discount = new Discount();
			$date_start = Input::get('date_start');
			$date_end = Input::get('date_end');
			$branch_id = Input::get('branch_id');


			?>
			<div id="no-more-tables">


					<table class='table' id='tblSales'>
						<thead>
						<tr>
							<th>Item</th>
							<TH>Start</TH>
							<TH>End</TH>
							<TH>Amount</TH>
							<th></th>
						</tr>
						</thead>
						<tbody>
						<?php
							//$targetpage = "paging.php";
							$limit = 50;
							$countRecord = $discount->countRecord($cid,$search,$date_start,$date_end,$branch_id);


							$total_pages = $countRecord->cnt;

							$stages = 3;
							$page = ($args);
							$page = (int)$page;
							if($page) {
								$start = ($page - 1) * $limit;
							} else {
								$start = 0;
							}

							$company_sales = $discount->get_record($cid, $start, $limit,$search,$date_start,$date_end,$branch_id);

							getpagenavigation($page, $total_pages, $limit, $stages);

							if($company_sales) {

								foreach($company_sales as $s) {

									?>
									<tr>
										<td data-title='Item'>
											<?php echo $s->item_code . "<small style='display:block;' class='text-danger'>".$s->description."</small>"?>
											</td>
										<td data-title='Start'>
										<?php echo escape(date('M d, Y',$s->date_start)) ?><br>

										</td>
										<td data-title='End'>
										<?php echo escape(date('M d, Y',$s->date_end)) ?><br>
										</td>
											<td data-title='Amount'>
										<?php echo escape(number_format($s->amount,2)); ?><br>
										</td>
										<td>
										<?php if($user->hasPermission('discount_m')){
											?>
											<a class='btn btn-primary' href='adddiscount.php?edit=<?php echo escape(Encryption::encrypt_decrypt('encrypt', $s->id)); ?>' title='Edit Discount'><span class='glyphicon glyphicon-pencil'></span></a>
											<a href='#' class='btn btn-primary deleteDiscount' id="<?php echo escape(Encryption::encrypt_decrypt('encrypt', $s->id)); ?>" title='Delete Discount'><span class='glyphicon glyphicon-remove'></span></a>

											<?php
										}?>
										</td>
									</tr>
								<?php
								}
							} else {
								?>
								<tr>
									<td colspan='5'><h3><span class='label label-info'>No Record Found...</span></h3>
									</td>
								</tr>
							<?php
							}
						?>
						</tbody>
					</table>
			<?php

	}
	function itemPriceAdjustmentPaginate($args,$cid){


			$item_price_adjustment = new Item_price_adjustment();

			$branch_id = (Input::get('branch_id')) ? Input::get('branch_id') : 0;
			$price_group_id = (Input::get('price_group_id')) ? Input::get('price_group_id') : 0;

			$search_item = Input::get('search_item');
			$limit_by = Input::get('limit_by');

			$dt_from = Input::get('dt_from');
			$dt_to = Input::get('dt_to');


			?>
			<div id="no-more-tables">


					<table class='table' id='tblSales'>
						<thead>
						<tr>
							<th>Branch</th>
							<th>Remarks</th>
							<th>Batch</th>
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
							$limit = 30;
							}

							$countRecord = $item_price_adjustment->countRecord($cid,$search_item,$branch_id,$dt_from,$dt_to);


							$total_pages = $countRecord->cnt;

							$stages = 3;
							$page = ($args);
							$page = (int)$page;

							if($page) {
								$start = ($page - 1) * $limit;
							} else {
								$start = 0;
							}

							$company_sales = $item_price_adjustment->get_record($cid, $start, $limit,$search_item,$branch_id,$dt_from,$dt_to);

							getpagenavigation($page, $total_pages, $limit, $stages);


							if($company_sales) {
								$prodcls = new Product();
								$user = new User();
								if($branch_id){
									$branchCls = new Branch($branch_id);
									$branch_name = $branchCls->data()->name;
								}
								foreach($company_sales as $s) {
									$price = $prodcls->getPrice($s->id);
									$adjustment = 0;
									$edit_id = 0;
									$remarks = "<i class='fa fa-ban'></i>";
									$batch_dt = "<i class='fa fa-ban'></i>";
									if(!$branch_id && !$price_group_id){
										$branch_name = "Masterlist";
									} else if($branch_id){


										$adjustment = ($s->adjustment) ? $s->adjustment : 0;
										$edit_id =  ($s->ipid) ? $s->ipid : 0;

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
									<tr
									data-branch_id='<?php echo $branch_id; ?>'
									data-price_group_id='<?php echo $price_group_id; ?>'
									data-item_id='<?php echo $s->id; ?>'
									data-id='<?php echo $edit_id; ?>'
									data-adjustment='<?php echo $adjustment; ?>'
									data-orig-price='<?php echo $price->price; ?>'
									data-adjusted-price='<?php echo $adjustment + $price->price; ?>'
									>
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
											<?php echo $s->item_code . "<small style='display:block;' class='text-danger'>".$s->description."</small>"?>

											<?php if($s->modified) echo "Last Modified: <small class='span-block text-success'>" . date("m/d/Y H:s:i A",$s->modified) . "</small>" ?>

										</td>
										<td data-title='Original Price'  style='border-top:1px solid #ccc;'>
										<?php echo escape(number_format($price->price,2)); ?><br>
										</td>
										<td data-title='Adjustment'  style='border-top:1px solid #ccc;'>
										<?php echo escape(number_format($adjustment,2)); ?><br>
										</td>
										<td data-title='Adjusted Price'  style='border-top:1px solid #ccc;'>
										<?php echo escape(number_format($adjustment + $price->price,2)); ?><br>
										</td>
										<td  style='border-top:1px solid #ccc;'>
											<?php if(($branch_id || $price_group_id) && $user->hasPermission('item_adj_m')){
											?>
											<button class='btn btn-default btnEdit'><span class='glyphicon glyphicon-pencil'></span></button>

											<?php
											}?>
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
	function itemPriceAdjustmentLogPaginate($args,$cid){


			$log = new Item_price_adjustment_log();
			$branch_id = Input::get('branch_id');
			$search_item = Input::get('search_item');


			?>
			<div id="no-more-tables">


					<table class='table' id='tblSales'>
						<thead>
						<tr>
							<th>Branch</th>
							<th>Item</th>
							<TH>From</TH>
							<th>To</th>
							<th>Modified by</th>
							<th>Date</th>
						</tr>
						</thead>
						<tbody>
						<?php
							//$targetpage = "paging.php";
							$limit = 30;
							$countRecord = $log->countRecord($cid,$search_item,$branch_id);


							$total_pages = $countRecord->cnt;

							$stages = 3;
							$page = ($args);
							$page = (int)$page;
							if($page) {
								$start = ($page - 1) * $limit;
							} else {
								$start = 0;
							}

							$company_sales = $log->get_record($cid, $start, $limit,$search_item,$branch_id);

							getpagenavigation($page, $total_pages, $limit, $stages);

							if($company_sales) {
								$prodcls = new Product();
								foreach($company_sales as $s) {


									?>
									<tr>
										<td data-title='Branch'>
											<?php echo escape($s->branch_name); ?>
										</td>
										<td data-title='Item'>
											<?php echo $s->item_code . "<small style='display:block;' class='text-danger'>".$s->description."</small>"?>
											</td>
										<td data-title='From'>
										<?php echo escape(number_format($s->from_price,2)); ?><br>
										</td>
										<td data-title='To'>
										<?php echo escape(number_format($s->to_price,2)); ?><br>
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

	function allDocList($args,$cid){

			$search = Input::get('search');
			$dt_from = Input::get('dt_from');
			$dt_to = Input::get('dt_to');
			$branch_id = Input::get('branch_id');

			$sales = new Sales();
			$user = new User();

			$crud = new Crud();
			$classSalestype = new Sales_type();
			$docs = new Doc_color();

			?>
			<div id="no-more-tables">
				<table class='table' id='tblSales'>
						<thead>
						<tr>
							<th>Branch</th>
							<TH>Invoice</TH>
							<TH>Dr</TH>
							<TH>Pr</TH>
							<TH>Total Sales</TH>
							<TH>Date sold</TH>
							<th style='width:200px;'>Sold to</th>
							<th>Cashier</th>
							<th></th>
						</tr>
						</thead>
						<tbody>
						<?php
							//$targetpage = "paging.php";
							$limit = 50;
							$countRecord = $sales->countRecordR2v3($cid,$search,$dt_from,$dt_to,$branch_id);

							$total_pages = $countRecord->cnt;

							$stages = 3;
							$page = ($args);
							$page = (int)$page;

							if($page) {
								$start = ($page - 1) * $limit;
							} else {
								$start = 0;
							}

							$company_sales = $sales->getSalesR2v3($cid, $start, $limit,$search,$dt_from,$dt_to,$branch_id);

							getpagenavigation($page, $total_pages, $limit, $stages);

							if($company_sales) {

								// inv
								$invoices = $docs->getDocs($user->data()->company_id,1);
								// dr
								$drs = $docs->getDocs($user->data()->company_id,2);
								// ir
								$irs = $docs->getDocs($user->data()->company_id,3);
								// color option

								// checkbox save db

								foreach($company_sales as $s) {
									$inv = 'No Invoice';
									$dr = 'No Dr';
									$ir = 'No Pr';
									$clsPayment = new Payment($s->payment_id);
									if($clsPayment->data()->isFinal == 1){
										$chkdisabled = 'disabled';
										$btnDisabled = "display:none;";
									} else {
										$chkdisabled = '';
										$btnDisabled = "";
									}
									$arrDocs=[];
									if($clsPayment->data()->docs){
											if(strpos($clsPayment->data()->docs,',') > 0){
											$arrDocs = explode(',',$clsPayment->data()->docs);
											} else {
											$arrDocs[0] = $clsPayment->data()->docs;
											}
									}
										$chkInv = "";
									if($s->invoice) {
										$inv = $s->invoice;
										if($invoices){
										foreach($invoices as $ind_inv){
										$isCheck = (in_array($ind_inv->id,$arrDocs)) ? "checked" : '';
										$chkInv .= "<span class='span-block text-danger'><input $chkdisabled data-pid='$s->payment_id' name='chk{$s->payment_id}' class='invClass' $isCheck type='checkbox' value='".($ind_inv->id)."'> ".($ind_inv->name) . " </span>";
										}
										}

									}
										$chkDr= "";
									if($s->dr) {
										$dr = $s->dr;
										if($drs){
										foreach($drs as $ind_dr){
										$isCheck = (in_array($ind_dr->id,$arrDocs)) ? "checked" : '';
										$chkDr .= "<span class='span-block text-danger'><input $chkdisabled data-pid='$s->payment_id' name='chk{$s->payment_id}' class='invClass' $isCheck type='checkbox' value='".($ind_dr->id)."'> ".($ind_dr->name) . " </span>";
										}
										}

									}
										$chkIr= "";
									if($s->ir) {
										$ir = $s->ir;
										if($irs){
										foreach($irs as $ind_ir){
											$isCheck = (in_array($ind_ir->id,$arrDocs)) ? "checked" : '';
											$chkDr .= "<span class='span-block text-danger'><input $chkdisabled data-pid='$s->payment_id' name='chk{$s->payment_id}' class='invClass' $isCheck type='checkbox' value='".($ind_ir->id)."'> ".($ind_ir->name) . " </span>";
										}
										}

									}
									$member_name = 'None';
									if($s->mln && $s->mfn) {
										$member_name = $s->mln . ", " . $s->mfn;
									}
									$reserved_by = "";
									if(isset($s->reserved_by)) {
										$reservedby = new User($s->reserved_by);
										$reserved_by = $reservedby->data()->lastname . ", " . $reservedby->data()->firstname;
									}
									$cashier = "";
									if(isset($s->cashier_id)) {
										$cashiercls = new User($s->cashier_id);
										$cashier = $cashiercls->data()->lastname . ", " . $cashiercls->data()->firstname;
									}
									?>
									<tr>
										<td data-title='Branch' style='border-bottom:1px solid #ccc;'>
											<strong><?php echo ($s->bname) ? escape($s->bname) :'Caravan';  ?></strong></td>
										<td data-title='Invoice' style='border-bottom:1px solid #ccc;width:130px;'>
										<?php echo escape($inv) ?><br>
										<?php
											if($user->hasPermission('doc_util')){
											 echo $chkInv;
											}
										 ?>
										</td>
										<td data-title='Dr' style='border-bottom:1px solid #ccc;width:130px;'>
										<?php echo escape($dr) ?><br>
										<?php
										 if($user->hasPermission('doc_util')){
											 echo $chkDr;
										}
										 ?>
										</td>
										<td data-title='Pr' style='border-bottom:1px solid #ccc;width:130px;'>
										<?php echo escape($ir) ?><br>
										<?php
										 if($user->hasPermission('doc_util')){
											 echo $chkIr;
										}
										 ?>
										</td>
										<td data-title='Total' style='border-bottom:1px solid #ccc;' class='text-danger'>
											<strong><?php echo escape(number_format($s->totalamount, 2)) ?></strong>
										</td>
										<td data-title='Date sold' style='border-bottom:1px solid #ccc;'><?php echo escape(date('m/d/Y', $s->sold_date)); ?></td>
										<td data-title='Sold To' style='border-bottom:1px solid #ccc;width:200px;' class='text-success'><?php echo escape(ucwords($member_name)) ?></td>
										<td data-title='Reserved by' style='border-bottom:1px solid #ccc;' class=''>
											<?php
												echo escape(ucwords($cashier));
												if($reserved_by) {
													echo "<br><span class='label label-primary'>Reserved by: $reserved_by</span>";
												}
											?>
										</td>
										<td style='border-bottom:1px solid #ccc;'>
										<?php
										 if($user->hasPermission('lock_doc_util')){
										 ?>
										 	<button data-pid='<?php echo $s->payment_id; ?>' style='<?php echo $btnDisabled; ?>' class='btn btn-default btnFinalize'><span class='glyphicon glyphicon-ok'></span> Finalize</button>
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
									<td colspan='8'><h3><span class='label label-info'>No Record Found...</span></h3>
									</td>
								</tr>
							<?php
							}
						?>
						</tbody>
					</table>
			<?php

	}
	function reportPagination($args, $cid) {
		if(Input::get('payment_method') && !Input::get('pt')) {
			$payment_method = Input::get('payment_method');
			$b = Input::get('branch');
			$mem_id = Input::get('member');
			$dateStart = Input::get('dateStart');
			$dateEnd = Input::get('dateEnd');
			$user = new User();
			$sales = new Sales();
			?>
			<table class='table' id='tblSales'>
				<thead>
				<tr>
					<TH>Invoice</TH>
					<TH>Dr</TH>
					<TH>Barcode</TH>
					<TH>Item Code</TH>
					<TH>Price</TH>
					<TH>Qty</TH>
					<TH>Discount</TH>
					<TH>Total</TH>
					<TH>Date sold</TH>
					<TH>Cashier</TH>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php
					//$targetpage = "paging.php";
					$limit = 20;
					$countRecord = $sales->countRecordBaseOnPaymentMethod($cid, $payment_method, $b, $mem_id, $dateStart, $dateEnd);
					$stotal = $sales->totalSaleBaseOnPaymentMethod($cid, $payment_method, $b, $mem_id, $dateStart, $dateEnd);
					$total_pages = $countRecord->cnt;

					$stages = 3;
					$page = ($args);
					$page = (int)$page;
					if($page) {
						$start = ($page - 1) * $limit;
					} else {
						$start = 0;
					}

					$company_sales = $sales->get_sales_record_baseOnPayment($cid, $start, $limit, $payment_method, $b, $mem_id, $dateStart, $dateEnd);

					getpagenavigation($page, $total_pages, $limit, $stages);

					if($company_sales) {

						$prevpid = 0;
						$totalsales = 0;
						foreach($company_sales as $s) {
							$cashier = new User($s->cashier_id);
							$pd = new Product($s->item_id);
							$price = $pd->getPriceByPriceId($s->price_id);
							$sss = new Sales();
							$p_length = $sss->countPaymentLength($s->payment_id, $start, $limit);
							if($prevpid != $s->payment_id) {
								$bordertop = "style='border-top:1px solid #ccc;'";
							} else {
								$bordertop = '';
							}
							$totalsales += ($s->qtys * $price->price) - $s->discount;
							?>
							<tr <?php echo $bordertop; ?> >
								<td><span class='badge'>
									<?php echo ($s->invoice) ? escape($s->invoice) : "No invoice"; ?>
								</span>
								</td>
								<td><?php echo ($s->dr) ? escape($s->dr) : "No Dr" ?></td>
								<td class='text-danger'><?php echo escape($pd->data()->barcode) ?></td>
								<td><?php echo escape($pd->data()->item_code) ?></td>
								<td><?php echo escape(number_format($price->price, 2)); ?>
								</td>
								<td><?php echo escape($s->qtys) ?></td>
								<td><?php echo escape(number_format($s->discount, 2)) ?></td>
								<td class='text-danger'>
									<strong><?php echo escape(number_format(($s->qtys * $price->price) - $s->discount, 2)) ?></strong>
								</td>
								<td><?php echo escape(date('m/d/Y ', $s->sold_date)); ?></td>
								<td>
									<span class='label label-primary'><?php echo ucfirst(escape($cashier->data()->lastname . ", " . $cashier->data()->firstname)) ?></span>
								</td>

								<td>
									<?php
										if($prevpid != $s->payment_id) {
											?>
											<input type="button" value="Payment Details" data-payment_id='<?php echo $s->payment_id ?>' class='btn btn-default btn-sm paymentDetails' />
											<?php
											$prevpid = $s->payment_id;
										} else {
											echo "<button style='visibility:hidden;'></button>";
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
			<div>
				<h3 class='text-right' style='margin-right:80px;'>
					<span class="label label-default">
						<strong>Total Sales: <?php echo (isset($stotal)) ? number_format($stotal->stotal, 2) : 0.00; ?></strong>
					</span>
				</h3>
			</div>
		<?php
		} /*else if(Input::get('pt')){
			$pt = Input::get('pt');
			 $b =Input::get('branch');
			$mem_id = Input::get('member');
			$dateStart = Input::get('dateStart');
			$dateEnd = Input::get('dateEnd');
			if($pt==1){
				//cash
				$user = new User();
				$sales = new Sales();
				?>
				<h3>Cash Transaction</h3>
				<table class="table">
					<tr>
						<th >Payment ID</th>
						<th class=''>Invoice</th>
						<th class=''>Dr</th>
						<th class='text-right'>Amount</th>
						<th class='text-center'>Date</th>
						<th></th>
					</tr>
				<?php
					//$targetpage = "paging.php";
					$limit = 20;
					$countRecord = $sales->countRecordTransaction($cid,$pt,$b,$mem_id,$dateStart,$dateEnd);
					$stotal = $sales->totalAmountTransaction($cid,$pt,$b,$mem_id,$dateStart,$dateEnd);
					$total_pages =$countRecord->cnt;

					$stages = 3;
					$page = ($args);
					$page = (int) $page;
					if($page){
						$start = ($page - 1) * $limit;
					}else{
						$start = 0;
					}

					$company_sales = $sales->get_sales_record_transaction($cid,$start,$limit,$pt,$b,$mem_id,$dateStart,$dateEnd);
					getpagenavigation($page,$total_pages,$limit,$stages);
					if($company_sales){
						foreach($company_sales as $c){
							?>
							<tr>
								<td><span class='badge'><?php echo escape($c->payment_id); ?></span></td>
								<td class='text-danger'><?php echo ($c->invoice) ? escape($c->invoice) : 'No Invoice'; ?></td>
								<td><?php echo ($c->dr) ? escape($c->dr): 'No Dr'; ?></td>
								<td class='text-right'><em><?php echo escape(number_format($c->amount,2)); ?></em></td>
								<td class='text-center text-success'><?php echo escape(date('m/d/Y',$c->created)); ?></td>
								<td><input type="button" class='btn btn-default transactionDetails' data-payment_id='<?php echo $c->payment_id ?>' value='Transaction Details'/></td>
							</tr>
							<?php
						}
						?>

						<?php
					} else {
						?>
						<td colspan='6'><h3><span class='label label-info'>No Record Found...</span></h3></td>
						<?php
					}

				?>
				</table>
				<div>
					<h3 class='text-right' style='margin-right:80px;'>
					<span class="label label-default">
						<strong>Total Sales: <?php echo (isset($stotal)) ? number_format($stotal->stotal,2): 0.00; ?></strong>
					</span>
					</h3>
				</div>
			<?php
			} else if($pt==2){
				// credit
				$user = new User();
				$sales = new Sales();
				?>
				<h3>Credit Card Transaction</h3>
				<table class="table">
					<tr>
						<th>Payment ID</th>
						<th>Card Holder</th>
						<th>Card Number</th>
						<th>Bank</th>
						<th class='text-right'>Amount</th>
						<th class='text-center'>Date</th>
						<th></th>
					</tr>
					<?php
						//$targetpage = "paging.php";
						$limit = 20;
						$countRecord = $sales->countRecordTransaction($cid,$pt,$b);
						$stotal = $sales->totalAmountTransaction($cid,$pt,$b);
						$total_pages =$countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int) $page;
						if($page){
							$start = ($page - 1) * $limit;
						}else{
							$start = 0;
						}

						$company_sales = $sales->get_sales_record_transaction($cid,$start,$limit,$pt,$b);
						getpagenavigation($page,$total_pages,$limit,$stages);
						if($company_sales){
							foreach($company_sales as $c){
								?>
								<tr>
									<td><span class='badge'><?php echo escape($c->payment_id); ?></span></td>
									<td class='text-danger'><?php echo ucwords($c->lastname . ", " .$c->firstname . " " . $c->middlename); ?></td>
									<td><?php echo escape($c->card_number) ?></td>
									<td><?php echo escape($c->bank_name) ?></td>
									<td class='text-right'><em><?php echo escape(number_format($c->amount,2)); ?></em></td>
									<td class='text-center text-success'><?php echo escape(date('m/d/Y',$c->created)); ?></td>
									<td><input type="button" class='btn btn-default transactionDetails' data-payment_id='<?php echo $c->payment_id ?>' value='Transaction Details'/></td>
								</tr>
							<?php
							}
						}else {
							?>
							<td colspan='7'><h3><span class='label label-info'>No Record Found...</span></h3></td>
						<?php
						}

					?>
				</table>
				<div>
					<h3 class='text-right' style='margin-right:80px;'>
					<span class="label label-default">
						<strong>Total Sales: <?php echo (isset($stotal)) ? number_format($stotal->stotal,2): 0.00; ?></strong>
					</span>
					</h3>
				</div>
			<?php
			}else if($pt==3){
				// bt
				$user = new User();
				$sales = new Sales();
				?>
				<h3>Bank Transfer Transaction</h3>
				<table class="table">
					<tr>
						<th>Payment ID</th>
						<th>Account Name</th>
						<th>Account Number</th>
						<th>Bank</th>
						<th>Receiving Account Number</th>
						<th>Receiving Bank</th>
						<th class='text-right'>Amount</th>
						<th class='text-center'>Date</th>
						<th></th>
					</tr>
					<?php
						//$targetpage = "paging.php";
						$limit = 20;
						$countRecord = $sales->countRecordTransaction($cid,$pt,$b);
						$stotal = $sales->totalAmountTransaction($cid,$pt,$b);
						$total_pages =$countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int) $page;
						if($page){
							$start = ($page - 1) * $limit;
						}else{
							$start = 0;
						}

						$company_sales = $sales->get_sales_record_transaction($cid,$start,$limit,$pt,$b);
						getpagenavigation($page,$total_pages,$limit,$stages);
						if($company_sales){
							foreach($company_sales as $c){
								?>
								<tr>
									<td><span class='badge'><?php echo escape($c->payment_id); ?></span></td>
									<td class='text-danger'><?php echo ucwords($c->lastname . ", " .$c->firstname . " " . $c->middlename); ?></td>
									<td><?php echo escape($c->bankfrom_account_number) ?></td>
									<td><?php echo escape($c->bankfrom_name) ?></td>
									<td><?php echo escape($c->bankto_account_number) ?></td>
									<td><?php echo escape($c->bankto_name) ?></td>
									<td class='text-right'><em><?php echo escape(number_format($c->amount,2)); ?></em></td>
									<td class='text-center text-success'><?php echo escape(date('m/d/Y',$c->created)); ?></td>
									<td><input type="button" class='btn btn-default transactionDetails' data-payment_id='<?php echo $c->payment_id ?>' value='Transaction Details'/></td>
								</tr>
							<?php
							}
						}else {
							?>
							<td colspan='9'><h3><span class='label label-info'>No Record Found...</span></h3></td>
						<?php
						}

					?>
				</table>
				<div>
					<h3 class='text-right' style='margin-right:80px;'>
					<span class="label label-default">
						<strong>Total Sales: <?php echo (isset($stotal)) ? number_format($stotal->stotal,2): 0.00; ?></strong>
					</span>
					</h3>
				</div>

			<?php
			}else if($pt==4){
					// credit
				$user = new User();
				$sales = new Sales();
				?>
				<h3>Credit Card Transaction</h3>
				<table class="table">
					<tr>
						<th>Payment ID</th>
						<th>Cheque Owner</th>
						<th>Cheque Number</th>
						<th>Bank</th>
						<th class='text-right'>Amount</th>
						<th class='text-center'>Date</th>
						<th></th>
					</tr>
					<?php
						//$targetpage = "paging.php";
						$limit = 20;
						$countRecord = $sales->countRecordTransaction($cid,$pt,$b);
						$stotal = $sales->totalAmountTransaction($cid,$pt,$b);
						$total_pages =$countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int) $page;
						if($page){
							$start = ($page - 1) * $limit;
						}else{
							$start = 0;
						}

						$company_sales = $sales->get_sales_record_transaction($cid,$start,$limit,$pt,$b);
						getpagenavigation($page,$total_pages,$limit,$stages);
						if($company_sales){
							foreach($company_sales as $c){
								?>
								<tr>
									<td><span class='badge'><?php echo escape($c->payment_id); ?></span></td>
									<td class='text-danger'><?php echo ucwords($c->lastname . ", " .$c->firstname . " " . $c->middlename)?></td>
									<td><?php echo escape($c->check_number) ?></td>
									<td><?php echo escape($c->bank) ?></td>
									<td class='text-right'><em><?php echo escape(number_format($c->amount,2)); ?></em></td>
									<td class='text-center text-success'><?php echo escape(date('m/d/Y',$c->payment_date)); ?></td>
									<td><input type="button" class='btn btn-default transactionDetails' data-payment_id='<?php echo $c->payment_id ?>' value='Transaction Details'/></td>
								</tr>
							<?php
							}
						}else {
							?>
							<td colspan='7'><h3><span class='label label-info'>No Record Found...</span></h3></td>
						<?php
						}

					?>
				</table>
				<div>
					<h3 class='text-right' style='margin-right:80px;'>
					<span class="label label-default">
						<strong>Total Sales: <?php echo (isset($stotal)) ? number_format($stotal->stotal,2): 0.00; ?></strong>
					</span>
					</h3>
				</div>
			<?php
			}else if($pt==5){
				// con

				$user = new User();
				$sales = new Sales();
				?>
				<h3>Cash Transaction</h3>
				<table class="table">
					<tr>
						<th >Payment ID</th>
						<th>Invoice</th>
						<th>Dr</th>
						<th class='text-right'>Amount</th>
						<th class='text-center'>Date</th>
						<th></th>
					</tr>
				<?php
					//$targetpage = "paging.php";
					$limit = 20;
					$countRecord = $sales->countRecordTransaction($cid,$pt,$b);
					$stotal = $sales->totalAmountTransaction($cid,$pt,$b);
					$total_pages =$countRecord->cnt;

					$stages = 3;
					$page = ($args);
					$page = (int) $page;
					if($page){
						$start = ($page - 1) * $limit;
					}else{
						$start = 0;
					}

					$company_sales = $sales->get_sales_record_transaction($cid,$start,$limit,$pt,$b);
					getpagenavigation($page,$total_pages,$limit,$stages);
					if($company_sales){
						foreach($company_sales as $c){
							?>
							<tr>
								<td><span class='badge'><?php echo escape($c->payment_id); ?></span></td>
								<td class='text-danger'><?php echo ($c->invoice) ? escape($c->invoice) : 'No Invoice'; ?></td>
								<td><?php echo ($c->dr) ? escape($c->dr): 'No Dr'; ?></td>
								<td class='text-right'><em><?php echo escape(number_format($c->amount,2)); ?></em></td>
								<td class='text-center text-success'><?php echo escape(date('m/d/Y',$c->created)); ?></td>
								<td><input type="button" class='btn btn-default transactionDetails' data-payment_id='<?php echo $c->payment_id ?>' value='Transaction Details'/></td>
							</tr>
							<?php
						}
					}else {
						?>
						<td colspan='6'><h3><span class='label label-info'>No Record Found...</span></h3></td>
					<?php
					}

				?>
				</table>
				<div>
					<h3 class='text-right' style='margin-right:80px;'>
					<span class="label label-default">
						<strong>Total Sales: <?php echo (isset($stotal)) ? number_format($stotal->stotal,2): 0.00; ?></strong>
					</span>
					</h3>
				</div>
			<?php
			}
		} */ else if(Input::get('item_type') || Input::get('category') || Input::get('char')) {

			//	$b =Input::get('branch');
			//	$dateStart = Input::get('dateStart');
			//	$dateEnd = Input::get('dateEnd');
			$item_type = Input::get('item_type');
			$category = Input::get('category');
			$char = Input::get('char');
			$user = new User();
			$sales = new Sales();
			?>
			<table class='table' id='tblSales'>
				<thead>
				<tr>
					<TH>Invoice</TH>
					<TH>Dr</TH>
					<TH>Barcode</TH>
					<TH>Item Code</TH>
					<TH>Price</TH>
					<TH>Qty</TH>
					<TH>Discount</TH>
					<TH>Total</TH>
					<TH>Date sold</TH>
					<TH>Cashier</TH>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php
					//$targetpage = "paging.php";
					$limit = 20;
					$countRecord = $sales->countRecordBaseOnItem($cid, $item_type, $category, $char);
					$stotal = $sales->totalSaleBaseOnItem($cid, $item_type, $category, $char);
					$total_pages = $countRecord->cnt;

					$stages = 3;
					$page = ($args);
					$page = (int)$page;
					if($page) {
						$start = ($page - 1) * $limit;
					} else {
						$start = 0;
					}

					$company_sales = $sales->get_sales_record_baseOnItem($cid, $start, $limit, $item_type, $category, $char);

					getpagenavigation($page, $total_pages, $limit, $stages);

					if($company_sales) {

						$prevpid = 0;
						$totalsales = 0;
						foreach($company_sales as $s) {
							$cashier = new User($s->cashier_id);
							$pd = new Product($s->item_id);
							$price = $pd->getPriceByPriceId($s->price_id);
							$sss = new Sales();
							$p_length = $sss->countPaymentLength($s->payment_id, $start, $limit);
							if($prevpid != $s->payment_id) {
								$bordertop = "style='border-top:1px solid #ccc;'";
							} else {
								$bordertop = '';
							}
							$totalsales += ($s->qtys * $price->price) - $s->discount;
							?>
							<tr <?php echo $bordertop; ?> >
								<td><span class='badge'>
									<?php echo ($s->invoice) ? escape($s->invoice) : "No invoice"; ?>
								</span>
								</td>
								<td><?php echo ($s->dr) ? escape($s->dr) : "No Dr" ?></td>
								<td class='text-danger'><?php echo escape($pd->data()->barcode) ?></td>
								<td><?php echo escape($pd->data()->item_code) ?></td>
								<td><?php echo escape(number_format($price->price, 2)); ?>
								</td>
								<td><?php echo escape($s->qtys) ?></td>
								<td><?php echo escape(number_format($s->discount, 2)) ?></td>
								<td class='text-danger'>
									<strong><?php echo escape(number_format(($s->qtys * $price->price) - $s->discount, 2)) ?></strong>
								</td>
								<td><?php echo escape(date('m/d/Y ', $s->sold_date)); ?></td>
								<td>
									<span class='label label-primary'><?php echo ucfirst(escape($cashier->data()->lastname . ", " . $cashier->data()->firstname)) ?></span>
								</td>

								<td>
									<?php
										if($prevpid != $s->payment_id) {
											?>
											<input type="button" value="Payment Details" data-payment_id='<?php echo $s->payment_id ?>' class='btn btn-default paymentDetails' />
											<?php
											$prevpid = $s->payment_id;
										}
									?>
								</td>
							</tr>
						<?php
						}

					} else {
						?>
						<td colspan='6'><h3><span class='label label-info'>No Record Found...</span></h3></td>
					<?php
					}
				?>
			</table>
			<div>
				<h3 class='text-right' style='margin-right:80px;'>
					<span class="label label-default">
						<strong>Total Sales: <?php echo (isset($stotal)) ? number_format($stotal->stotal, 2) : 0.00; ?></strong>
					</span>
				</h3>
			</div>
		<?php

		}
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

			$paginate .= "<div style='padding:3px;' class='text-right imgonnastick'><ul class='pagination' >";

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
	function returnWithPrefAndSuf($s,$type){
		if($s){
			if($type == 1){
				return $s->pref_inv . padLeft($s->invoice) . $s->suf_inv;
			} else if($type == 2){
				return $s->pref_dr . padLeft($s->dr) . $s->suf_dr;
			} else if($type == 3){
				return $s->pref_ir . padLeft($s->ir) . $s->suf_ir;
			} else if($type == 4){
				return $s->pref_sv . padLeft($s->sv) . $s->suf_sv;
			}
		}
	}
	function getTimeDiff($time){
		$lbl = "";
		if($time < 60){
			//seconds
			$res= ($time>1) ?  "secs $lbl" : "sec $lbl";
			return floor($time) . " $res";
		} else if ($time < (60*60)) {
			//minutes
			$del = number_format($time/60,2);
			$res = (floor($del) > 1) ? "mins $lbl" : "min $lbl";
			return floor($del) . " $res";
		} else if ($time < (60*60) * 24){
			// hrs
			$del = number_format(($time/60)/60,2);
			$res = (floor($del) > 1) ? "hrs $lbl" : "hr $lbl";
			return  floor($del). " $res";
		} else{
			// if ($time < (((60 *60) * 24) * 30))
			$del = number_format((($time/60)/60)/24,2);
			$res = (floor($del) > 1) ? "days $lbl" : "day $lbl";
			return  floor($del). " $res";
		}

	}
	function getOrderIdOnRemarks($a=''){
		if(strpos($a,'Deduct inventory from rack (Order id #') !== false){
			$start = strpos($a,'#') + 1;
			$end = strpos($a,')');
			$length = $end - $start;
			$id = substr($a,strpos($a,'#') + 1, $length);
			return $id;
		} else {
			return 0;
		}
	}