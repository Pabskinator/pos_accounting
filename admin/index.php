<?php

	// fix and check
	require_once '../includes/admin/page_head2.php';

	if(!$user->hasPermission('dashboard')){
		// redirect to denied page
		Redirect::to(1);
	}
	if($user->hasPermission('dashboard_tm')){
		// redirect to denied page
		Redirect::to('dashboard_tm.php');
		die();
	}
	$with_new_dash = [
		'pw.apollosystems.com.ph',
		'cebuhiq.apollosystems.com.ph',
		'sh.apollosystems.com.ph',
		'cn.apollosystems.com.ph',
		'dev.apollo.ph:81',

		'localhost'
	];

	$newdash = false;
	if(Configuration::thisCompany('cebuhiq')){

		$newdash = true;


	}
	if(Configuration::thisCompany('pw')){
		$newdash = true;
	}
	if(Configuration::thisCompany('avision')){
		$newdash = true;
	}
	if(Configuration::thisCompany('zamaryan')){
		$newdash = true;
	}

	if($_SERVER['HTTP_HOST'] == 'localhost:81'){
		$newdash = false;
	}

	if($newdash){

		include_once('new-dash.php');


	} else {


		$gsales = new Sales();

		for($i=0;$i>-10;$i--){

		$monthStart = strtotime(date('F Y') . "$i month" );
		$temp = $i + 1;
		$monthEnd = strtotime(date('F Y').  "$temp month -1 day");
		$msale = $gsales->getSalesCompany($user->data()->company_id,$monthStart,$monthEnd);
		$msale = ($msale->saletotal) ? $msale->saletotal : 0;
		$arrMon[] = date('m/d/Y',$monthStart);
		$arrTotal[] =$msale;
		}

		$saleslist = '[';
		$currentsale = "";

		for($i=0;$i<count($arrTotal);$i++){
		$saleslist .= "{y:'" . date('F Y' ,strtotime($arrMon[$i])) . "', a:".number_format($arrTotal[$i], 2, '.', '')."},";
		if(date('F Y' ,strtotime($arrMon[$i]))  == date('F Y') ){
		$currentsale = number_format($arrTotal[$i],2);
		}
		}

		$saleslist = rtrim($saleslist,",");
		$saleslist .= ']';

		$cbranch = new Branch();
		$cbranch = $cbranch->countBranch($user->data()->company_id);
		$cbranch = $cbranch->cnt;
		$cterminals = new Terminal();
		$cterminals = $cterminals->countTerminal($user->data()->company_id);
		$cterminals = $cterminals->cnt;
		$cmember = new Member();
		$cmember = $cmember->countMember($user->data()->company_id);
		$cmember = $cmember->cnt;
		$cproducts = new Product();
		$cproducts = $cproducts->countProduct($user->data()->company_id);
		$cproducts = $cproducts->cnt;


		$cf = new Custom_field();
		$getstationdet = $cf->getcustomform('stations',$user->data()->company_id);
		$custom_station_name = isset($getstationdet->label_name)? strtoupper($getstationdet->label_name):'STATION';
		$custom_station_name = ucfirst(strtolower($custom_station_name));

		$checkPendingOrderPoint = new Reorder_item();
		$pending_reorder_item = $checkPendingOrderPoint->countPending($user->data()->company_id);
		if($pending_reorder_item){
		$pending_reorder_item= " <span class='badge'>" . $pending_reorder_item->cnt ."</span>";
		} else {
		$pending_reorder_item = " <span class='badge'>0</span>";
		}

		if($user->hasPermission('caravan_manage')){
		$checkpending = new Agent_request();
		$caravan_pending = $checkpending->countPending($user->data()->company_id);
		if($caravan_pending){
		$caravan_pending = " <span class='badge'>".$caravan_pending->cnt."</span>";
		} else {
		$caravan_pending = " <span class='badge'>0</span>";
		}
		} else {
		$caravan_pending = "";
		}
		if ($user->hasPermission('order')){

		$checkpendingReservation = new Order();
		$reservation_pending = $checkpendingReservation->countPending($user->data()->company_id);
		if($reservation_pending){
		$reservation_pending = " <span class='badge'>".$reservation_pending->cnt."</span>";
		} else {
		$reservation_pending = " <span class='badge'>0</span>";
		}
		} else {
		$reservation_pending = "";
		}
		$saleslist = "[]";
		?>
		<style>
			.nav-content{
				height: 100px;
				padding-top: 30px;
				text-align: center;
				border:1px solid;
				font-size: 23px;
				background: #2c3e50;
				margin-bottom: 2px;

			}
			.nav-content a {
				color:#fff;
			}
		</style>

		<!-- Page content -->
		<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->

		<div class="page-content inset">

			<div class="content-header">
				<div class="row">
					<div class="col-md-6">
						<span id="menu-toggle" class='glyphicon  glyphicon-circle-arrow-right'></span>
						<span class='h1'>Dashboard</span>
					</div>
					<div class="col-md-6 text-right">
				<span class='h1'>
					<?php if($user->hasPermission('mainpos')) { ?>
						<a style='color:#434a54;' href="pos.php"><i  class='fa fa-home'></i></a>
					<?php } ?>
					<?php if($user->hasPermission('inventory')) { ?>
						<a style='color:#434a54;'  href="for-releasing.php"><i class='fa fa-list'></i></a>
					<?php } ?>
					<?php if(Configuration::isGym()) { ?>
						<a style='color:#434a54;'  href="daily_report.php"><i class='fa fa-money'></i></a>
					<?php } ?>
				</span>
					</div>
				</div>
			</div>

			<?php

				if(Session::exists('homeflash')) {
					echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('homeflash') . "</div><br/>";
				}


			?>

			<?php
				$http_host = $_SERVER['HTTP_HOST'];
				if($http_host != 'localhost:81' && Configuration::thisCompany('calayan')){
				?>
				<div class="alert alert-info">
					<strong>Basic Security Tips</strong>
					<ul>
						<li>Use hard to guess password. Combination of letters, numbers and special characters. Avoid using dictionary words. Change it every month if necessary. </li>
						<li>Always check the URL when you try to login on our site. The official login page is
							<strong>calayan.apollosystems.com.ph/login.php</strong>.
						    Type it directly on your browser and don't just click any malicious link from untrusted person.
						</li>
						<li>Sometimes a malicious user will try to redirect you to a different site that looks like our site. They will send you link like <strong>ca1ayan.apollosystems.com.ph</strong>. Always pay attention to the URL spelling. </li>
						<li>Keep your anti-virus updated and scan your computer/phone regularly.</li>
						<li>Don't save password on your browser. If you lose your phone/computer/laptop, someone may access it using your saved password on your browser. </li>
						<li>Limit users who can see sensitive information.</li>
						<li>Don't give your username and password to anyone. If there are people who will ask for it pretending to be from Apollo Systems, ignore it and report it to us immediately. We will NEVER ask for your login credentials. </li>
					</ul>
					<br>
					<p>If you have questions, concerns, or suggestions, you may reach me at jayson.temporas@gmail.com or you may call me at 6877111 local 88. Monday to Friday, 9:00 AM to 7:00 PM. </p>
					<p><strong>Browse Safely! Thank you!</strong></p>
				</div>

				<?php
			}?>

			<div class="row">

				<div class="col-md-4">
					<div class="panel panel-primary" >
						<div class="panel-heading">
							<i class='fa fa-home'></i> Branches
						</div>
						<div class="panel-body">
							<h3>
								<?php echo $cbranch; ?>
							</h3>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<i class='fa fa-map-marker'></i>  Terminals
						</div>
						<div class="panel-body">
							<h3>
								<?php echo $cterminals; ?>
							</h3>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<i class='fa fa-barcode'></i>  Products
						</div>
						<div class="panel-body">
							<h3>
								<?php echo $cproducts; ?>
							</h3>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
		<?php if(
			$user->hasPermission('r_item') ||
			$user->hasPermission('r_client') ||
			$user->hasPermission('r_order') ||
			$user->hasPermission('daily_sales') ||
			$user->hasPermission('st_sum_sales')||
			$user->hasPermission('deduction_summary')||
			$user->hasPermission('r_quota')||
			$user->hasPermission('r_freebie')
		)


		{
			?>

		<div class="col-md-4">
					<div class="panel panel-primary">
						<!-- Default panel contents -->
						<div class="panel-heading"><i class='fa fa-bell'></i> Links</div>
						<div class="panel-body">
							<ul class="list-group">
								<?php 	if($user->hasPermission('r_item')){ ?>
									<li class="list-group-item">
										<a  href="report-item.php"> Item report</a>
									</li>
								<?php } ?>
								<?php 	if($user->hasPermission('r_client')){ ?>
									<li class="list-group-item">
										<a  href="report-member.php"> Client report</a>
									</li>
								<?php } ?>
								<?php 	if($user->hasPermission('r_order')){ ?>
									<li class="list-group-item">
										<a  href="report-order.php"> Order report</a>
									</li>
								<?php } ?>

								<?php 	if($user->hasPermission('daily_sales')){ ?>
									<li class="list-group-item">
										<a  href="daily-summary.php"> Daily Sales</a>
									</li>
								<?php } ?>
								<?php 	if($user->hasPermission('st_sum_sales')){ ?>
									<li class="list-group-item">
										<a  href="sales-type-summary.php"> Sales Type Summary</a>
									</li>
								<?php } ?>

								<?php 	if($user->hasPermission('deduction_summary')){ ?>
									<li class="list-group-item">
										<a  href="deduction-summary.php"> Deduction Summary</a>
									</li>
								<?php } ?>

								<?php 	if($user->hasPermission('r_quota')){ ?>
									<li class="list-group-item">
										<a  href="branch-quotas.php"> Quota Report</a>
									</li>
								<?php } ?>

								<?php 	if($user->hasPermission('r_freebie')){ ?>
									<li class="list-group-item">
										<a  href="branch-quotas.php"> Freebies Report</a>
									</li>
								<?php } ?>
							</ul>
						</div>
					</div>
				</div>
		 <?php } ?>

				<?php if($user->hasPermission('fm_view')){
						$upload = new Upload();
						$upload_list = $upload->getImages(0,0,'file_manager',12);

						if($upload_list){
							?>
							<div class="col-md-8">
								<div class=''><h5>Recently Uploaded Files</h5></div>
							<div class="row">


							<?php
							foreach($upload_list as $ul){
								$filename = $ul->filename;
								$ex = explode('.',$filename);
								$ext = strtolower($ex[count($ex) -1]);
								$type_src = '';
								if($ext == 'pdf'){
									$type_src = '../css/img/icon-pdf.png';
								} else if ($ext == 'xls' || $ext == 'xlsx'){
									$type_src = '../css/img/icon-excel.png';
								} else if ($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'bmp'){
									$type_src = '../css/img/icon-image.png';
								} else if ($ext == 'doc' || $ext == 'docx'){
									$type_src = '../css/img/icon-word.png';
								}
								$ul->description = ($ul->description) ? $ul->description : 'No Description';
							?>
								<div class="col-md-3">
									<div class="thumbnail">
										<a href="#" class='btnTrackFile' data-description='<?php echo $ul->description; ?>' data-url='../uploads/<?php echo $ul->filename; ?>' target="_blank">

											<div title='<?php echo $ul->title; ?>' style='width: 100%;white-space: nowrap;overflow: hidden; text-overflow: ellipsis;' class="caption truncate">
												<img src="<?php echo $type_src; ?>" style='height:50px' alt="...">
												<strong><?php echo $ul->title; ?></strong>
												<small class='help-block truncate' style='width: 100%;white-space: nowrap;overflow: hidden; text-overflow: ellipsis;' title="<?php echo $ul->description; ?>"><?php echo $ul->description; ?></small>
												<small class='help-block'><?php echo date('m/d/y',$ul->created); ?></small>
											</div>
										</a>
									</div>
								</div>
							<?php
							}
								?>
							</div>
								<div class='text-right'><a href="file_manager.php" target="_parent">View all</a></div>

							</div>
								<?php
						} else {
							?>
							<div class="col-md-8">

							<div class="alert alert-info">No uploaded file.
							<?php if($user->hasPermission('fm_manage')){
								?>								<a href="file_manager.php" target="_parent">Upload now</a>
								<?php
							} ?>

							</div>
							</div>
							<?php
						}
					?>

					<?php
				}?>

			</div>

			<!-- nav -->

			<div>
				<br/>
				<div >

					<?php

						if($user->hasPermission('item') || $user->hasPermission('category') || $user->hasPermission('characteristics') || $user->hasPermission('unit') || $user->hasPermission('queue') || $user->hasPermission('item_adj')) { ?>
							<div class="panel panel-primary">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a  href="#collapseTwo"><span class="fa fa-barcode">
                            </span> Products </a>
									</h4>
								</div>
								<div id="collapseTwo"  >
									<div class="panel-body">
									<div class='row'>
											<?php  if($user->hasPermission('item')) {  ?>
												<div class="col-md-3">
													<div class='nav-content'>
														<a class='dash-link' href='product.php'>Product list</a>
														</div>
												</div>
											<?php } ?>
											<?php  if($user->hasPermission('item_post') ) {  ?>
										<div class="col-md-3">
											<div class='nav-content'>
														<a class='dash-link' href='item_posting.php'>Post Item</a>
												</div>
											</div>

											<div class="col-md-3">
												<div class='nav-content'>
														<a class='dash-link' href='item_spec.php'>Item Specification</a>
												</div>
												</div>
											<?php } ?>
											<?php  if($user->hasPermission('category')) {  ?>
										<div class="col-md-3">
											<div class='nav-content'>
														<a class='dash-link' href="category.php">Categories</a>
												</div>
													</div>
											<?php } ?>
											<?php  if($user->hasPermission('characteristics')) {  ?>
										<div class="col-md-3">
											<div class='nav-content'>
														<a class='dash-link' href="characteristics.php">Characteristics</a>
												</div>
												</div>
											<?php } ?>
											<?php  if($user->hasPermission('unit')) {  ?>
											<div class="col-md-3">
												<div class='nav-content'>
														<a class='dash-link' href="unit.php">Units</a>
													</div>
													</div>
											<?php } ?>
											<?php  if($user->hasPermission('queue')) {  ?>
										<div class="col-md-3">
											<div class='nav-content'>
														<a class='dash-link' href="queu.php">Queues</a>
												</div>
													</div>
											<?php } ?>
											<?php  if($user->hasPermission('item_adj')) {  ?>
										<div class="col-md-3">
											<div class='nav-content'>
														<a class='dash-link' href="item-price-adjustment.php">Pricelist</a>
												</div>
												</div>
											<?php } ?>
											<?php  if($user->hasPermission('pr_adj_categ')) {  ?>
										<div class="col-md-3">
											<div class='nav-content'>
														<a class='dash-link' href="member_category_discount.php">Adjustment By Category</a>
												</div>
												</div>
											<?php } ?>
											<?php  if($user->hasPermission('bundles')) {  ?>
										<div class="col-md-3">
											<div class='nav-content'>
														<a class='dash-link' href='bundle_list.php'>Bundles</a>
												</div>
													</div>
											<?php } ?>
											<?php  if($user->hasPermission('price_group')) {  ?>
										<div class="col-md-3">
											<div class='nav-content'>
														<a class='dash-link' href='price_group.php'>Price Group</a>
												</div>
													</div>
											<?php } ?>

											<?php  if($user->hasPermission('freebie')) {  ?>
										<div class="col-md-3">
											<div class='nav-content'>
														<a class='dash-link' href='item_freebie.php'>Item Freebie</a>
												</div>
												</div>
											<?php } ?>
											<?php  if($user->hasPermission('quotation')) {  ?>
										<div class="col-md-3">
											<div class='nav-content'>
														<a class='dash-link' href='quotation.php'>Quotation</a>
												</div>
													</div>
											<?php } ?>
											<?php  if($user->hasPermission('item_commission')) {  ?>
												<div class='col-md-3'>
													<div class='nav-content'>
														<a class='dash-link' href='commission_item.php'>Item commission</a>
													</div>
												</div>
											<?php } ?>
										</div>
									</div>
								</div>
							</div>

						<?php } ?>
					<?php if($user->hasPermission('inventory') ||  $user->hasPermission('inventory_transfer')  ||  $user->hasPermission('inventory_receive')  ||  $user->hasPermission('order_inv_m') ||  $user->hasPermission('pickup_inv') || $user->hasPermission('spare_part') || $user->hasPermission('inventory_issues') || $user->hasPermission('req_sup') || $user->hasPermission('serials')) { ?>

						<div class="panel panel-primary" >
							<div class="panel-heading">
								<h4 class="panel-title">
									<a href="#collapseThree"><span class="fa fa-tags">
                            </span>  Inventories</a>
								</h4>
							</div>
							<div id="collapseThree" >
								<div class="panel-body">
									<div class='row'>
										<?php if($user->hasPermission('inventory') ){
											?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="inventory.php"> Manage Inventory</a>
												</div>
											</div>

											<?php
										}?>

										<?php if($user->hasPermission('inventory_transfer')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="transfer.php"> <?php echo TRANSFER_LABEL; ?></a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('inventory_receive')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="transfer_monitoring.php"> <?php echo REC_INV_LABEL; ?></a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('bad_order')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="bad_order.php"> Bad Order</a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('pickup_inv')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="pickup_mon.php"> Item Pickup</a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('spare_part')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="spare-parts.php"> <?php echo SPAREPART_LABEL; ?></a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('inventory_issues')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="inventory_issues.php">Item issues</a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('req_sup')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="supplies.php">Supplies</a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('item_swap')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="swap.php">Swapping</a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('inv_rep')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="warehouse_reports.php">Report</a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('serials') ){
											?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="serials.php"> Serial</a>
												</div>
											</div>

											<?php
										}?>

										<?php if($user->hasPermission('mem_equipment') ){
											?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="member_equipment.php"> Borrowed Item</a>
												</div>
											</div>

											<?php
										}?>
										</div>
								</div>
							</div>
						</div>

					<?php  } ?>

					<?php if($user->hasPermission('ship_v') || $user->hasPermission('branch') || $user->hasPermission('supplier') || $user->hasPermission('terminal') || $user->hasPermission('subcom') || $user->hasPermission('pettycash')) { ?>

						<div class="panel panel-primary" >
							<div class="panel-heading" >
								<h4 class="panel-title">
									<a  href="#collapseFour"><span class="fa fa-map-marker">
                            </span> Branch</a>
								</h4>
							</div>
							<div id="collapseFour"  >
								<div class="panel-body">
									<div class='row'>
										<?php if($user->hasPermission('branch')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="branch.php"> Manage Branch</a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('supplier')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="supplier.php"> Manage Supplier</a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('terminal')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="terminal.php"> Manage Terminal</a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('subcom')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="sub-company.php"> Manage <?php echo Configuration::getValue('sub_company'); ?></a>
												</div>
											</div>
										<?php } ?>

										<?php if($user->hasPermission('pettycash')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="pettycash.php"> Petty Cash</a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('ship_v')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="shipping-company.php"> Shipping Company</a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('city_m')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="delivery_charges_matrix.php"> Manage Cities</a>
												</div>
											</div>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>

					<?php } ?>


					<?php if($user->hasPermission('member') || $user->hasPermission('subscription') || $user->hasPermission('station') || $user->hasPermission('m_char') || $user->hasPermission('m_terms')|| $user->hasPermission('m_terms_request') || $user->hasPermission('med_doctor')) { ?>

						<div class="panel panel-primary">
							<div class="panel-heading" >
								<h4 class="panel-title">
									<a href="#collapseNine"><span class="fa fa-users"></span>
										<?php echo MEMBER_LABEL; ?>
									</a>
								</h4>
							</div>
							<div id="collapseNine" >
								<div class="panel-body">
									<div class='row'>
										<?php  if($user->hasPermission('subscription')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="subscription.php"> <?php echo  MEMBER_LABEL; ?> Classes</a>
												</div>
											</div>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="member_consumable.php"> <?php echo  MEMBER_LABEL; ?> Private Training</a>
												</div>
											</div>
										<?php } ?>

										<?php  if($user->hasPermission('member')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="members.php"> <?php echo MEMBER_LABEL; ?> List</a>
												</div>
											</div>
										<?php } ?>
										<?php  if($user->hasPermission('tblast')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="sms_module.php"> Text Blast</a>
												</div>
											</div>
										<?php } ?>

										<?php  if($user->hasPermission('m_ref')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="member-report.php">Referrals</a>
												</div>
											</div>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="member-attendance-summary.php"> Attendance summary</a>
												</div>
											</div>
										<?php  }?>
										<?php  if($user->hasPermission('m_exp')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="expi_adj.php"> Experience Adjustment</a>
												</div>
											</div>


										<?php } ?>
										<?php  if($user->hasPermission('exp_tbl')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="expi_table.php"> Experience Table</a>
												</div>
											</div>
										<?php } ?>

										<?php  if($user->hasPermission('wo_mod')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="workout_module.php"> Workout Module</a>
												</div>
											</div>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="assessment_list.php"> Assessment</a>
												</div>
											</div>
										<?php } ?>
										<?php  if($user->hasPermission('affiliate')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="affiliates.php"> Affiliate </a>
												</div>
											</div>
										<?php } ?>
										<?php  if($user->hasPermission('station')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="station.php"> <?php echo $custom_station_name; ?></a>
												</div>
											</div>
										<?php } ?>

										<?php  if($user->hasPermission('m_char')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="member_char.php"> Characteristics</a>
												</div>
											</div>
										<?php } ?>
										<?php  if($user->hasPermission('m_terms_request')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="member_terms.php"> Terms</a>
												</div>
											</div>
										<?php } ?>
										<?php  if($user->hasPermission('med_doctor')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="med_doctor.php"> Doctor</a>
												</div>
											</div>
										<?php } ?>

										<?php  if($user->hasPermission('med_nurse')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="med_nurse.php"> Nurse</a>
												</div>
											</div>
										<?php } ?>
										<?php  if($user->hasPermission('med_history')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="med_history.php"> History</a>
												</div>
											</div>
										<?php } ?>

										<?php  if($user->hasPermission('e_bills_request')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="e_bills.php"> Easy Bills Pay</a>
												</div>
											</div>
										<?php } ?>
										<?php  if($user->hasPermission('m_dues')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="monthly_due.php">Monthly Dues</a>
												</div>
											</div>
										<?php } ?>

									</div>
								</div>
							</div>
						</div>

					<?php } ?>

					<?php if($user->hasPermission('user') || $user->hasPermission('position')) { ?>

						<div class="panel panel-primary">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a href="#collapseSix"><span class="fa fa-user">
                            </span> Users</a>
								</h4>
							</div>
							<div id="collapseSix" >
								<div class="panel-body">
									<div class='row'>
										<?php if($user->hasPermission('user')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="user.php"> Manage User</a>
												</div>
											</div>

										<?php } ?>
										<?php if($user->hasPermission('position')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="position.php"> Manage Position</a>
												</div>
											</div>

										<?php } ?>
									</div>
								</div>
							</div>
						</div>

					<?php } ?>

					<?php if($user->hasPermission('orderpoint') || $user->hasPermission('wh_request') || $user->hasPermission('item_service_r') || $user->hasPermission('item_service_s') || $user->hasPermission('item_service_p') || $user->hasPermission('item_service_l') ) { ?>
						<div class="panel panel-primary" >
							<div class="panel-heading" >
								<h4 class="panel-title">
									<a  href="#collapseOrder"><span class="fa fa-list">
                            </span> Orders</a>
								</h4>
							</div>
							<div id="collapseOrder"  >
								<div class="panel-body">
									<div class='row'>
										<?php if($user->hasPermission('orderpoint'))
										{
											?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="orderpoint.php"> Manage Order Point</a>
												</div>
											</div>
										<?php } ?>


										<?php
											$checkPendingOrderPoint = new Reorder_item();
											$pending_reorder_item = $checkPendingOrderPoint->countPending($user->data()->company_id);
											if($pending_reorder_item){
												$pending_reorder_item= " <span class='badge'>" . $pending_reorder_item->cnt ."</span>";
											} else {
												$pending_reorder_item = " <span class='badge'>0</span>";
											}
										?>
										<?php if($user->hasPermission('orderpoint_p')) { ?>

											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="to_order.php"> Critical Order </a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('wh_request')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="wh-order.php"> Order Item</a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('item_service_r') || $user->hasPermission('item_service_p') || $user->hasPermission('item_service_s')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="item-service.php">Item Service</a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('wh_reports')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="wh_reports.php"> Order Reports</a>
												</div>
											</div>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="truck-report.php"> Truck Reports</a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('truck')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="truck.php"> Manage Trucks</a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('del_helper')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="delivery_helper.php"> Deliver Helper</a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('del_helper')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="driver.php"> Driver</a>
												</div>
											</div>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>

					<?php } ?>
					<?php if($user->hasPermission('p_point') || $user->hasPermission('wallet_req')|| $user->hasPermission('ez_bills')) { ?>
						<div class="panel panel-primary" >
							<div class="panel-heading" >
								<h4 class="panel-title">
									<a  href="#collapseEasyBills"><span class="fa fa-book">
                            </span> Easy Bills</a>
								</h4>
							</div>
							<div id="collapseEasyBills" >
								<div class="panel-body">
									<div class='row'>
										<?php  if($user->hasPermission('p_point')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="my-points.php"> My Points</a>
												</div>
											</div>
										<?php } ?>
										<?php  if($user->hasPermission('p_point_manage')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="point.php"> Manage Points</a>
												</div>
											</div>
										<?php } ?>

										<?php  if($user->hasPermission('ez_bills')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="e_bills.php"> Pay Bills</a>
												</div>
											</div>
										<?php } ?>
										<?php  if($user->hasPermission('wallet_req')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="wallet_user.php"> My Wallet</a>
												</div>
											</div>
										<?php } ?>

										<?php  if($user->hasPermission('wallet_manage')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="wallet_company.php"> Manage Wallet</a>
												</div>
											</div>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>

					<?php } ?>
					<?php if($user->hasPermission('caravan_request') || $user->hasPermission('caravan_manage')) { ?>
						<div class="panel panel-primary" >
							<div class="panel-heading" >
								<h4 class="panel-title">
									<a href="#collapseCaravan"><span class="fa fa-globe">
                            </span> Caravan</a>
								</h4>
							</div>
							<div id="collapseCaravan" >
								<div class="panel-body">
									<div class='row'>
										<?php  if($user->hasPermission('caravan_request')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="caravan_request.php"> Request Item</a>
												</div>
											</div>
										<?php } ?>
										<?php
											if($user->hasPermission('caravan_manage')){
												$checkpending = new Agent_request();
												$caravan_pending = $checkpending->countPending($user->data()->company_id);
												if($caravan_pending){
													$caravan_pending = " <span class='badge'>".$caravan_pending->cnt."</span>";
												} else {
													$caravan_pending = " <span class='badge'>0</span>";
												}
											} else {
												$caravan_pending = "";
											}
										?>
										<div class='col-md-3'>
											<div class='nav-content'>
												<a class='dash-link' href="manage_caravan.php"> Caravans<?php echo $caravan_pending; ?></a>
											</div>
										</div>
										<?php
											if($user->hasPermission('mc_liquidate_sales') && $user->hasPermission('caravan_manage')) {
												?>
												<div class='col-md-3'>
													<div class='nav-content'>
														<a class='dash-link' href="caravan_issues.php"> Caravan Issues</a>
													</div>
												</div>

												<?php
											}
										?>
									</div>
								</div>
							</div>
						</div>

					<?php } ?>

					<?php if($user->hasPermission('sales') || $user->hasPermission('order') || $user->hasPermission('createorder') || $user->hasPermission('discount') || $user->hasPermission('agent_sales')) { ?>
						<div class="panel panel-primary">
							<div class="panel-heading"  >
								<h4 class="panel-title">
									<a  href="#collapseSeven"><span class="fa fa-ruble">
                            </span> Sales</a>
								</h4>
							</div>
							<div id="collapseSeven"  >
								<div class="panel-body">
									<div class='row'>
										<?php if($user->hasPermission('sales') || $user->hasPermission('cheque_monitoring') ||$user->hasPermission('credit_monitoring') ) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="sales.php"> Manage Sales</a>
												</div>
											</div>
											<?php if(Configuration::allowedPermission('vit') && ($user->hasPermission('cnp') || $user->hasPermission('daina') || $user->hasPermission('mastra') || $user->hasPermission('service_sales') || $user->hasPermission('assembly_sales'))){
												?>
												<div class='col-md-3'>
													<div class='nav-content'>
														<a class='dash-link' href="custom_reports.php"> Custom Report</a>
													</div>
												</div>
												<?php
											}?>



												<?php if($user->hasPermission('credit_monitoring')){
													?>
												<div class='col-md-3'>
													<div class='nav-content'>
														<a class='dash-link' href="member_credits.php"> Credits</a>
													</div>
												</div>
													<?php
												} ?>


												<?php if($user->hasPermission('refund')){
													?>
												<div class='col-md-3'>
													<div class='nav-content'>
														<a class='dash-link' href="refund.php"> Refund</a>
													</div>
												</div>
													<?php
												} ?>


											<?php if($user->hasPermission('cheque_monitoring')){
												?>
												<div class='col-md-3'>
													<div class='nav-content'>
														<a class='dash-link' href="cheque_monitoring.php"> Cheque Monitoring</a>
													</div>
												</div>
												<div class='col-md-3'>
													<div class='nav-content'>
														<a class='dash-link' href="cash_monitoring.php"> Cash Monitoring</a>
													</div>
												</div>
												<div class='col-md-3'>
													<div class='nav-content'>
														<a class='dash-link' href="credit_monitoring.php"> Credit Card Monitoring</a>
													</div>
												</div>
												<div class='col-md-3'>
													<div class='nav-content'>
														<a class='dash-link' href="bank_monitoring.php"> Bank Transfer Monitoring</a>
													</div>
												</div>


												<?php

											}?>


										<?php } ?>

										<?php if($user->hasPermission('ar')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="accounting.php"> Accounts Receivable</a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('cr_agent')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="cr_agent.php"> Agent CR</a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('discount')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="discount.php"> Discount list</a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('agent_sales')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="by-agent.php"> Agent sales</a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('reports')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="reports2.php"> Reports</a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('deduction_type')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="deduction_list.php"> Deduction type</a>
												</div>
											</div>
										<?php } ?>

										<?php if($user->hasPermission('deductions')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="deductions.php"> Deduction list</a>
												</div>
											</div>
										<?php } ?>
										<?php  if($user->hasPermission('m_terms_request')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="deposits.php"> Deposits</a>
												</div>
											</div>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>

					<?php } ?>
					<?php if($user->hasPermission('sms_num') || $user->hasPermission('sms_log')) { ?>
						<div class="panel panel-primary">
							<div class="panel-heading"  >
								<h4 class="panel-title">
									<a  href="#collapseSMS"><span class="fa fa-mobile">
                            </span> SMS</a>
								</h4>
							</div>
							<div id="collapseSMS"  >
								<div class="panel-body">
									<div class='row'>
										<?php if($user->hasPermission('sms_num')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="sms_mobile.php"> Sms Number</a>
												</div>
											</div>
											<?php
										} ?>
										<div class='col-md-3'>
											<?php if($user->hasPermission('sms_log')){
												?>
												<div class='nav-content'>
													<a class='dash-link' href="sms_log.php"> Log</a>
												</div>
												<?php
											} ?>
										</div>
										<?php if($user->hasPermission('sms_num')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="sms_report.php"> Summary</a>
												</div>
											</div>
											<?php
										} ?>
										<div class='col-md-3'>
											<?php if($user->hasPermission('sms_log')){
												?>
												<div class='nav-content'>
													<a class='dash-link' href="dicer_deposit.php"> Dicer deposits</a>
												</div>
												<?php
											} ?>
										</div>
										<div class='col-md-3'>
											<?php if($user->hasPermission('sms_log')){
												?>
												<div class='nav-content'>
													<a class='dash-link' href="sms_ro_report.php"> Dicer Received Order</a>
												</div>
												<?php
											} ?>
										</div>
										<div class='col-md-3'>
											<?php if($user->hasPermission('sms_log')){
												?>
												<div class='nav-content'>
													<a class='dash-link' href="sms_no_report.php"> No Report Monitoring</a>
												</div>
												<?php
											} ?>

										</div>
										<div class='col-md-3'>
											<?php if($user->hasPermission('sms_log')){
												?>
												<div class='nav-content'>
													<a class='dash-link' href="report-category.php"> Reports</a>
												</div>
												<?php
											} ?>

										</div>
									</div>
								</div>
							</div>
						</div>

					<?php } ?>

					<?php if($user->hasPermission('settings') || $user->hasPermission('inbox')) { ?>

						<div class="panel panel-primary">
							<div class="panel-heading" >
								<h4 class="panel-title">
									<a  href="#collapseEight"> <span class='fa fa-wrench'></span> Settings</a>
								</h4>
							</div>
							<div id="collapseEight" >
								<div class="panel-body">
									<div class='row'>
										<!-- <div class='col-md-3'>
											<div class='nav-content'>
												<a href="addcompany.php"> Add Company</a>
											</div>
										</div> -->

										<?php if($user->hasPermission('config')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="company_info.php"> Company Info</a>
												</div>
											</div>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="config.php">Configurations</a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('inbox')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="contact-us.php"> Inbox</a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('p_point_manage')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="point.php">Points</a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('themes')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="style_config.php"> Themes</a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('station_settings')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="station-settings.php"> Station settings</a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('station_settings')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="member-settings.php"> Member settings</a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('supplier_settings')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="supplier-settings.php"> Supplier settings</a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('recycle')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="recycle_bin.php">Recycle bin</a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('consumable_admin')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="consumable-admin.php">Consumable</a>
												</div>
											</div>
										<?php } ?>
										<?php if($user->hasPermission('consumablefree_admin')) { ?>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="consumablefree-admin.php">Freebies Admin</a>
												</div>
											</div>
											<div class='col-md-3'>
												<div class='nav-content'>
													<a class='dash-link' href="upload_utilities.php">Upload utilities</a>
												</div>
											</div>
										<?php } ?>

									</div>
								</div>
							</div>
						</div>

					<?php } ?>

				</div>
			</div>

			<!-- end nav -->
		</div>

		<div class="modal fade" id="btSetup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h3 class="modal-title">Branch and Terminal</h3>
						<p>You need to set up first your branch and terminal</p>
					</div>
					<div class="modal-body">
						<form class="form-horizontal">
							<fieldset>

								<div class="form-group">
									<label class="col-md-4 control-label" for="branches">Select Branch</label>
									<div class="col-md-4" id='branchitemholder'>

									</div>
								</div>

								<!-- Select Basic -->
								<div class="form-group">
									<label class="col-md-4 control-label" for="terminals">Select Terminal</label>
									<div class="col-md-4"  id='terminalitemholder'>
										<span class="label label-danger">Choose branch first..</span>
									</div>
								</div>

							</fieldset>
						</form>

					</div>
					<div class="modal-footer">
						<button type="button" id='submitbt' class="btn btn-primary">Save </button>
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->

		<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<script type="text/javascript">
			$(function(){
				$('.loading-n').show();
				$('#allcontent').show();
				//	alertify.alert('Test');

				$('body').on('click','.btnTrackFile',function(e){
					e.preventDefault();
					var src = $(this).attr('data-url');
					var description = $(this).attr('data-description');
					var f = {src: src, description:description};
					window.open(src, '_blank');
					$.ajax({
						url:'../ajax/ajax_product.php',
						type:'POST',
						data: {functionName:'logUser',file_info:JSON.stringify(f)},
						success: function(data){

						},
						error:function(){

						}
					});
				});
				function checkBranchTerminalSetup(){
					if(localStorage["branch_id"] == null || localStorage["terminal_id"] == null){
						// get all the branch and terminal of a company
						branchTerminal(localStorage["company_id"],1);
						// prevent the modal to be close
						$('#btSetup').modal({
							backdrop: 'static',
							keyboard: false
						});
						$("#btSetup").modal("show");
					}
				}
				function branchTerminal(cid,type){
					$.ajax({
						url: "../ajax/ajax_get_branchAndTerminal.php",
						type:"POST",
						data:{cid:cid,type:type},
						success: function(data){

							if(type == 1) {

								$("#branchitemholder").empty();
								$("#branchitemholder").append(data);

							} else {

								$("#terminalitemholder").empty();
								$("#terminalitemholder").append(data);

							}
						},
						error: function(){
							alert('Problem Occurs');
						}
					});
				}
				$('body').on('change','#branches',function(){
					branchTerminal($('#branches').val(),2);
				});

				$('#submitbt').click(function(){
					// if no item selected
					if($("#branches").val() == "" || $("#terminals").val()=="" ){
						showToast('error','<p>Please Choose Branch and Terminal first</p>','<h3>WARNING!</h3>','toast-bottom-right');
					} else {
						var terminalarr = $('#terminals').val().split(",");
						// assign terminal and branch to the computer
						localStorage["branch_id"] = $("#branches").val();
						localStorage["branch_name"] = $("#branches option:selected").text();
						localStorage["terminal_name"]=$("#terminals option:selected").text();
						localStorage["terminal_id"] = terminalarr[0];
						localStorage["invoice"] = terminalarr[1];
						$("#btSetup").modal("hide");

					}
				});
				<?php

				 if($saleslist != "[]"){
				?>
				$('#chart_div').html('');
				Morris.Bar({
					element: 'chart_div',
					data: <?php echo $saleslist; ?>,
					xkey: 'y',
					ykeys: ['a'],
					labels: ['Sales'],
					xLabelAngle: 35,
					padding: 40,
					hideHover: 'auto',
					barOpacity: 0.9,
					barRadius: [10, 10, 5, 5]
				});
				<?php
				} else {
				?>
				$('#chart_div').html('No Data Yet.');
				<?php
				}
				?>

			});

		</script>
		<?php

	} // end pw

	?>
<?php require_once '../includes/admin/page_tail2.php'; ?>