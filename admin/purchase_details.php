<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('member') && $user->data()->member_id == 0) {
		// redirect to denied page
		Redirect::to(1);
	}
	$doctors = [];
	$nurses = [];
	$hasdiag = false;
	 if($user->hasPermission('med_doctor') || $user->hasPermission('med_diag')) {
		 $doctor = new Med_doctor();
		 $doctors = $doctor->get_active('med_doctors',['company_id','=',$user->data()->company_id]);
		 $hasdiag = true;
	 }
	if($user->hasPermission('med_nurse') || $user->hasPermission('med_diag')) {
		$nurse = new Med_nurse();
		$nurses = $nurse->get_active('med_nurses',['company_id','=',$user->data()->company_id]);
		$hasdiag = true;
	}
	$cf = new Custom_field();
	$cfd = new Custom_field_details();
	$getmember = $cf->getcustomform('members',$user->data()->company_id);
	$otherfield = isset($getmember->other_field)?$getmember->other_field:'';
	if($otherfield){
		$otherfield = json_decode($otherfield,true);
	}
	$alldata = $cfd->getAllData($user->data()->company_id,$getmember->id);

	foreach($alldata as $data){
		$f_label = $data->field_label;
		$c_visible = $data->is_visible;
		$name = $data->field_name;
		$f = "f_".$name;
		$c= "c_".$name;
		$$f = $f_label;
		$$c = $c_visible;
	}
 if(Input::get('id')){
	$id = Encryption::encrypt_decrypt('decrypt',Input::get('id'));
	if (is_numeric($id) == false ){
		Session::flash('flash','Unable to get the necessary information.');
		Redirect::to('members.php');
		exit();
	}
	$mem_transaction = new Member($id);

	?>

	<input type="hidden" id='mem_id' value='<?php echo $id ?>' />
	<input type="hidden" id='enc_mem' value='<?php echo Input::get('id') ?>' />
	<?php
}  else {
	Redirect::to('members.php');
}
?>

	<!-- Page content -->
<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
	<div class="content-header">
		<div class='row'>
			<div class="col-md-6">
				<div class="btn-group" role="group" aria-label="..." style='margin-bottom: 5px;'>
					<button class='btn btn-default' id='navHome' ><i class='fa fa-home'></i> Home</button>
					<button class='btn btn-default' id='navAllTransaction' ><i class='fa fa-list'></i> All Transaction</button>
					<button class='btn btn-default' id='navOther' ><i class='fa fa-book'></i> Other information</button>
					<?php  if($hasdiag) { ?>
						<button class='btn btn-default' id='navDiag' ><i class='fa fa-book'></i> Diagnosis</button>
					<?php } ?>
				</div>
			</div>
			<div class="col-md-6 text-right">
				<div class="btn btn-group">

					<?php if ($user->data()->member_id == 0){
						?>
						<a class='btn btn-default' href='members.php'><span class='glyphicon glyphicon-arrow-left'></span> Back to <?php echo MEMBER_LABEL; ?></a>
						<?php
					 } ?>
					<?php if ($mem_transaction->data()->membership_id != 0){
						?>
						<a class='btn btn-default' href='my-points.php'><span class='glyphicon glyphicon-list'></span> My Points</a>
						<?php
					} ?>
					<?php if ($user->hasPermission('ez_bills')){
						?>
						<a class='btn btn-default' href='e_bills.php'><span class='glyphicon glyphicon-arrow-left'></span> Pay Bills</a>
						<?php
					} ?>

					<?php if ($user->hasPermission('wallet_req')){
						?>
						<a class='btn btn-default' href='wallet_user.php'><span class='glyphicon glyphicon-arrow-left'></span> Wallet</a>
						<?php
					} ?>
				<?php if($user->hasPermission('cheque_monitoring')) { ?>
					<button class='btn btn-default' id='navChequeMonitoring' ><i class='fa fa-money'></i> Cheque Monitoring</button>
				<?php } ?>
					<button class='btn btn-default' id='btnFilter'><i class='fa fa-filter'></i> Filter</button>
				</div>
			</div>
		</div>


	</div>

	<?php
		// get flash message if add or edited successfully
		if(Session::exists('salesflash')) {
			echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('salesflash') . "</div>";
		}
	?>

	<div class="row">
		<div class="col-md-12">

			<h3>
				<i class='fa fa-user'></i> <?php echo ucwords($mem_transaction->data()->lastname ); ?>
				<a style='font-size: 0.6em;display: inline-block;cursor: pointer' id='moreInfoToggle'><i class='fa fa-info-circle' ></i> More info</a>
			</h3>
			<div class="row" style='display:none;' id='moreInfoMember'>
				<div class="col-md-4" style='<?php echo (isset($c_address) && !empty($c_address)) ? '' :'display:none'; ?>'>
				<p><i class='fa fa-map-marker'></i> Address: <span class='text-danger'><?php echo ($mem_transaction->data()->address) ? escape($mem_transaction->data()->address) : 'Not available.'?></span></p>
				</div>
				<div class="col-md-4" >
					<p><i class='fa fa-calendar'></i>  Account created date: <span class='text-danger'><?php echo date('M d,Y',$mem_transaction->data()->created); ?></span></p>
				</div>
				<div class="col-md-4">
					<p> <i class='fa fa-certificate'></i>
						Type:
					<span class='text-danger'>
					<?php
						if($id){
							$memberchar = new Member_characteristics();
							$membercharlist = $memberchar->getMyCharacteristicsd($id);
							$mcl = '';
							if($membercharlist){
								foreach($membercharlist as $mc){
									$mcl .= $mc->name . ", ";
								}
								echo rtrim($mcl,', ');
							} else {
								echo "None";
							}

						}
						$withInvarr=['Without Invoice','With Invoice'];
					?>
					</span>
					</p>
				</div>

				<div class="col-md-4" style='<?php echo (isset($c_email) && !empty($c_email)) ? '' :'display:none'; ?>'>
				<p><i class='fa fa-envelope'></i> Email: <span class='text-danger'><?php echo ($mem_transaction->data()->email) ? escape($mem_transaction->data()->email) : 'Not available.'?></span></p>
				</div>


				<div class="col-md-4" style='<?php echo (isset($c_telephone) && !empty($c_telephone)) ? '' :'display:none'; ?>'>	<p><i class='fa fa-map-marker'></i> Area Code : <span class='text-danger'><?php echo ($mem_transaction->data()->area_code1) ? escape($mem_transaction->data()->area_code1) : 'Not available.'?></span></p></div>
				<div class="col-md-4" style='<?php echo (isset($c_telephone) && !empty($c_telephone)) ? '' :'display:none'; ?>'>	<p><i class='fa fa-phone'></i> Contact: <span class='text-danger'><?php echo ($mem_transaction->data()->contact_number) ? escape($mem_transaction->data()->contact_number) : 'Not available.'?></span></p></div>
				<div class="col-md-4" style='<?php echo (isset($c_fax) && !empty($c_fax)) ? '' :'display:none'; ?>'>	<p><i class='fa fa-map-marker'></i> Area Code : <span class='text-danger'><?php echo ($mem_transaction->data()->area_code2) ? escape($mem_transaction->data()->area_code2) : 'Not available.'?></span></p></div>
				<div class="col-md-4" style='<?php echo (isset($c_fax) && !empty($c_fax)) ? '' :'display:none'; ?>'>	<p><i class='fa fa-fax'></i> Fax: <span class='text-danger'><?php echo ($mem_transaction->data()->fax_number) ? escape($mem_transaction->data()->fax_number) : 'Not available.'?></span></p></div>
				<div class="col-md-4" style='<?php echo (isset($c_cellphone) && !empty($c_cellphone)) ? '' :'display:none'; ?>'>	<p><i class='fa fa-mobile'></i> Cel: <span class='text-danger'><?php echo ($mem_transaction->data()->cel_number) ? escape($mem_transaction->data()->cel_number) : 'Not available.'?></span></p></div>
				<div class="col-md-4" style='<?php echo (isset($c_contact1) && !empty($c_contact1)) ? '' :'display:none'; ?>'>	<p><i class='fa fa-user'></i> Contact person 1: <span class='text-danger'><?php echo ($mem_transaction->data()->firstname) ? escape($mem_transaction->data()->firstname . " " . $mem_transaction->data()->middlename) : 'Not available.'?></span></p></div>
				<div class="col-md-4" style='<?php echo (isset($c_contact2) && !empty($c_contact2)) ? '' :'display:none'; ?>'>	<p><i class='fa fa-user'></i> Contact person 2: <span class='text-danger'><?php echo ($mem_transaction->data()->cp_firstname) ? escape($mem_transaction->data()->cp_firstname . " " . $mem_transaction->data()->cp_lastname) : 'Not available.'?></span></p></div>


					<div class="col-md-4" style='<?php echo (isset($c_member_since) && !empty($c_member_since)) ? '' :'display:none'; ?>'><p><i class='fa fa-calendar'></i> <?php echo MEMBER_LABEL; ?> Since: <span class='text-danger'><?php echo ($mem_transaction->data()->member_since) ? escape(date('M d,Y',$mem_transaction->data()->member_since)) : 'Not available.'?></span></p></div>
					<div class="col-md-4" style='display:none;' ><p><i class='fa fa-list'></i> Agreement Year(s): <span class='text-danger'><?php echo ($mem_transaction->data()->sg_year) ? escape($mem_transaction->data()->sg_year) : 'Not available.'?></span></p></div>
					<div class="col-md-4" style='<?php echo (isset($c_invoice) && !empty($c_invoice)) ? '' :'display:none'; ?>' ><p><i class='fa fa-folder'></i> Invoice Type: <span class='text-danger'><?php echo $withInvarr[$mem_transaction->data()->with_inv]; ?></span></p></div>
					<div class="col-md-4" style='<?php echo (isset($c_payment_type) && !empty($c_payment_type)) ? '' :'display:none'; ?>'><p><i class='fa fa-money'></i> Payment Type: <span class='text-danger'><?php  echo ($mem_transaction->data()->payment_type) ? escape($mem_transaction->data()->payment_type) : 'Not available.'?></span></p></div>
				<div class="col-md-4" style='<?php echo (isset($c_terms) && !empty($c_terms)) ? '' :'display:none'; ?>'><p><i class='fa fa-certificate'></i> Terms: <span class='text-danger'><?php  echo ($mem_transaction->data()->terms) ? escape($mem_transaction->data()->terms) : 'Not available.'?></span></p></div>
				<div class="col-md-4" style='<?php echo (isset($c_credit_limit) && !empty($c_credit_limit)) ? '' :'display:none'; ?>'><p><i class='fa fa-ticket'></i> Credit Limit: <span class='text-danger'><?php  echo ($mem_transaction->data()->credit_limit) ? escape($mem_transaction->data()->credit_limit) : 'Not available.'?></span></p></div>
				<div class="col-md-4"style='<?php echo (isset($c_remarks) && !empty($c_remarks)) ? '' :'display:none'; ?>' ><p><i class='fa fa-comment'></i> Remarks: <span class='text-danger'><?php  echo ($mem_transaction->data()->remarks) ? escape($mem_transaction->data()->remarks) : 'Not available.'?></span></p></div>
				<?php
					if($otherfield){
						foreach($otherfield as $cfield){
							if($cfield['field-visibility'] == 1){
								if(isset($id)){
									$jsonind = json_decode($mem_transaction->data()->jsonfield,true);
								}
								$lbl = $cfield['field-label'];
								$val =  isset($jsonind[$cfield['field-id']]) ?  $jsonind[$cfield['field-id']] : 'Not available';
							?>
								<div class="col-md-4" ><p><i class='fa fa-list'></i> <?php echo $lbl; ?>: <span class='text-danger'><?php  echo $val; ?></span></p></div>
								<?php
							}
						}
					}
				?>

			</div>
			<div class="panel panel-primary">
				<!-- Default panel contents -->
				<div class="panel-heading">
					<div class='row'>
						<div class="col-md-8"><?php echo MEMBER_LABEL . " details"; ?></div>
						<div class="col-md-4 text-right">
							<?php if($hasdiag){
								?>
								<button id='btnAddDignosis' class='btn btn-default btn-sm'><i class='fa fa-pencil'></i></button>
								<?php
							} ?>

						</div>
					</div>
				</div>
				<div class="panel-body">

					<div class="row">

					</div>

					<!----------------------- START HOME  --------------------->
					<div id="conHome" style=''>

						<div class="panel panel-default">
							<div class="panel-heading"><strong>Last Ten Transaction</strong></div>
							<div class="panel-body">

								<div id="salesPastTenTransaction" class='col-md-12' style='height:400px;'></div>
							</div>
						</div>
						<hr />
						<div class="row">
							<div class="col-md-6">
								<div class="panel panel-default">
									<div class="panel-heading"><strong>Top <?php echo $custom_station_name; ?></strong></div>
									<div class="panel-body">
										<div id="topMemberStation" class='col-md-12' style='height:300px;'></div>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="panel panel-default">
									<div class="panel-heading"><strong>Top Items Sold </strong></div>
									<div class="panel-body">
										<div id="topMemberSalesPerItem" class='col-md-12' style='height:300px;'></div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!----------------------- END HOME  --------------------->

					<!----------------------- START ALL TRANSACTION  --------------------->
					<div id="conAllTransaction" style='display:none;'>
					<div class="row">
						<div class="col-md-4" >
							<div class="input-group">
								<span class="input-group-addon"><span class='glyphicon glyphicon-search'></span></span>
								<input type="text" id="searchSales" class='form-control' placeholder='Search..'/>
							</div>
						</div>
						<div class='col-md-8 text-right'>
							<button class='btn btn-default' id='btnDownloadMemberSales'><span class='glyphicon glyphicon-download'></span> Download</button>
						</div>
					</div>

						<div style='clear:both;'></div>

					<input type="hidden" id="hiddenpage" />
					<div id="holder"></div>
					<hr />
					<div id="paymentsummary">
						<?php
							$memsales = new Sales();
							$memlist = $memsales->getSalesMember($id);
							$totalconsumable  = $mem_transaction->getMyTotalConsumableAmount($id);
							$totalfree =  $mem_transaction->getMyTotalConsumableFreebies($id);
							$totalbounce =  $mem_transaction->getMyTotalBounceCheck($id);
							$totalnotcollected =  $mem_transaction->getNotYetCollected($id);
							$totalUtang = $mem_transaction->totalUtang($id);
						?>
						<div class="row" >
							<div class="col-md-6">
								<div class="panel panel-default">
								<div class="panel-body">
									<ul class="list-group">
										<li class='list-group-item active'><strong>Breakdown</strong></li>
										<li class='list-group-item'>Total consumables <span class='pull-right'><?php echo number_format($totalconsumable->totalConsumable,2); ?></li>
										<li class='list-group-item'>Total freebies <span class='pull-right'><?php echo number_format($totalfree->totalFreebies,2); ?></li>
										<li class='list-group-item'>Total Bounce check <span class='pull-right'><?php echo number_format($totalbounce->totalBounce,2); ?></li>
										<li class='list-group-item'>For Collection <span class='pull-right'><?php echo number_format($totalnotcollected->totalNotCollected,2); ?></li>
										<li class='list-group-item'>Total Credit <span class='pull-right'><?php echo number_format($totalUtang->camount,2); ?></li>
										<li class='list-group-item'>Total Sales <span class='pull-right'><?php echo number_format($memlist->saletotal,2); ?></li>
									</ul>
								</div>
							</div>
						</div>
							<div class="col-md-6">
								<div class="panel panel-default">
									<div class="panel-body">
										<ul class="list-group">
											<li class='list-group-item active'><strong>Credit payments</strong></li>
										<?php
											$membercredit=  new Member_credit();
											$paymentCreditList = $membercredit->getMemberCreditPayment($id);
											if(count($paymentCreditList)> 0){
												foreach($paymentCreditList as $creditList){
													$arrpayCred= json_decode($creditList->json_payment,true);
													foreach($arrpayCred as $pp){
														echo  "<li class='list-group-item'>$pp[fn] received <span class='text-danger'>". number_format($pp['amount'],2)."</span> on ".date('M d, Y',$pp['date'])."</li>";
													}
												}
											} else {
												echo  "<li class='list-group-item'>No record</li>";
											}
										?>
										</ul>
									</div>
								</div>
							</div>
					</div>
					</div>
					</div>
					<!----------------------- END ALL TRANSACTION  --------------------->

					<!----------------------- START OTHER  --------------------->
					<div id="conOther" style='display: none;'>
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default">
									<div class="panel-heading"><strong>Min,Max and Average Sales Transaction</strong></div>
									<div class="panel-body">
										<div class="col-md-6">
											<div id="statSaleTransaction" class='col-md-12' style='height:300px;'></div>
										</div>
										<div class="col-md-6">
											<div id="statSaleTransactionTbl" class='col-md-12' style='height:300px;'></div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="panel panel-default">
									<div class="panel-heading"><strong>Consumable Quantity</strong></div>
									<div class="panel-body">
										<div id="consumableQuantityHolder"></div>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="panel panel-default">
									<div class="panel-heading"><strong>Subscription</strong></div>
									<div class="panel-body">
										<div id="subscriptionHolder"></div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default">
									<div class="panel-heading"><strong>Notifications</strong></div>
									<div class="panel-body">
										<div id="holder2"></div>
										<input type="hidden" id='hiddenpage2'/>
									</div>
								</div>
							</div>
						</div>

					</div>
					<!----------------------- END OTHER  --------------------->
					<!----------------------- START DIAGNOSTIC --------------------->
					<?php if($hasdiag) { ?>
						<div id="conDiag" style='display: none;'>
							<div class="row">
								<div class="col-md-8">
									<button class='btn btn-default btn-sm' id='btnPatienHistory'>Add History</button>
								</div>
								<div class="col-md-4">
									<select class='form-control' name="filter_diag_type" id="filter_diag_type">
										<option value="1">Doctor's Diagnosis</option>
										<option value="2">Nurse's Diagnosis</option>
									</select>
								</div>
							</div>
							<div id="holder3"></div>
							<input type="hidden" id='hiddenpage3'/>
						</div>
					<?php } ?>

					<!----------------------- END DIAGNOSTIC --------------------->

				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->




	<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"></h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6">
							<strong>Start Date:</strong>
							<input type="text"  class='form-control' id='txtDT1' placeholder= 'Start Date'/>
						</div>
						<div class="col-md-6">
							<strong>End Date:</strong>
							<input type="text"  class='form-control' id='txtDT2' placeholder= 'End Date'/>
						</div>

						<div class="col-md-6"></div>
						<div class="col-md-6 text-right"><br> <button class='btn btn-primary' id='applyFilter'><i class='fa fa-check'></i> Apply</button></div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style=''>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id='mtitle'></h4>
				</div>
				<div class="modal-body" id='mbody'>
				</div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style=''>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id='mtitle2'></h4>
				</div>
				<div class="modal-body" id='mbody2'>
					<div class="panel panel-default">
						<div class="panel-body">
							<div id="addremarksholder"></div>
						</div>
					</div>

				</div>

			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div><!-- /.modal -->
		<?php if($hasdiag) { ?>
	<div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" style=''>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id='mtitle3'>Add Diagnosis</h4>
				</div>
				<div class="modal-body" id='mbody3'>
					<div class="panel panel-default">
						<div class="panel-body">
							<div class="form-group">
								<select name="d_diag_type" id="d_diag_type" class='form-control'>
									<option value="">Diagnosis type</option>
									<option value="1">Doctor Diagnosis</option>
									<option value="2">Nurse Diagnosis</option>
								</select>
							</div>
							<div class="form-group">
								<div id='doctor_holder' style='display:none;'>
									Doctor
									<select name="d_doctor_id" id="d_doctor_id" class='form-control'>
										<option value=""></option>
										<?php if($doctors) {
											foreach($doctors as $doc){
												?>
												<option value="<?php echo $doc->id; ?>"><?php echo $doc->name; ?></option>
												<?php
											}
										}?>
									</select>
								</div>
								<div id='nurse_holder' style='display:none;'>
								Nurse
								<select name="d_nurse_id" id="d_nurse_id" class='form-control'>
									<option value=""></option>
									<?php if($nurses) {
										foreach($nurses as $nur){
											?>
											<option value="<?php echo $nur->id; ?>"><?php echo $nur->name; ?></option>
											<?php
										}
									}?>
								</select>
								</div>
							</div>
							<div class="form-group">
								Diagnosis <textarea name="d_remarks" id="d_remarks" cols="30" rows="7" class='form-control'></textarea>

							</div>
							<div class="form-group text-right">
								<button class='btn btn-primary' id='btnSaveDiagnosis'>SAVE</button>
							</div>
						</div>
					</div>

				</div>

			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<?php } ?>
	<script>
		//$('#tblSales').dataTable({
		//	iDisplayLength: 50
		//});
		$(function(){

			function disableF5(e) { if ((e.which || e.keyCode) == 116) e.preventDefault(); };
			$(document).bind("keydown", disableF5);

			$('#d_remarks').html('').tinymce({
				height: 250
			});

			var mem_id = $('#mem_id').val();
			var whereami = 1;
			$('body').on('click','#moreInfoToggle',function(e){
				e.preventDefault();
				$('#moreInfoMember').slideToggle(300);
			});
			$('body').on('click','#btnDownloadMemberSales',function(){

					var search = $('#searchSales').val();
					search = (search) ? search : 0;



					window.open(
						'excel_downloader.php?downloadName=sales&search='+search+'&mem_id='+mem_id,
						'_blank' // <- This is what makes it open in a new window.
					);



			});
			getPast10(mem_id);
			topStation(mem_id,0,0);
			topItemMember(mem_id,0,0);
			$('#navChequeMonitoring').click(function(){
				var mem_id = $('#enc_mem').val();
				location.href= "cheque_monitoring.php?mem="+mem_id;
			});

			$('#navHome').click(function(){
				showContaion(true,false,false,false);
				getPast10(mem_id);
				topStation(mem_id,0,0);
				topItemMember(mem_id,0,0);
				whereami = 1;
			});
			$('#navAllTransaction').click(function(){
				showContaion(false,true,false,false);
				whereami = 1;
			});
			$('#navDiag').click(function(){
				showContaion(false,false,false,true);
				whereami = 3;
				getPage3(0,mem_id);
			});
			$('#navOther').click(function(){
				showContaion(false,false,true,false);
				statSaleTransaction(mem_id,1);
				statSaleTransaction(mem_id,2);
				whereami = 2;
				getPage2(0,mem_id);
				getSubscription(mem_id);
				getConsumable(mem_id);

			});
			function showContaion (c1,c2,c3,c4){
				$('#conHome').hide();
				$('#conAllTransaction').hide();
				$('#conOther').hide();
				$('#conDiag').hide();
				if(c1){
					$('#conHome').fadeIn();
				}
				 else if(c2){
					$('#conAllTransaction').fadeIn();
				}
				else if(c3){
					$('#conOther').fadeIn();
				}
				else if(c4){
					$('#conDiag').fadeIn();
				}
			}
			function getPast10(mem_id){
				$.ajax({
					url:'../ajax/ajax_query2.php',
					type:'post',
					dataType:'json',
					data: {mem_id:mem_id,functionName:'salesPastTenTransaction'},
					success: function(data){
						$('#salesPastTenTransaction').html('');
						if (data.error){
							$('#salesPastTenTransaction').html('No data found.');
						} else {
							Morris.Line({
								element: 'salesPastTenTransaction',
								data: data,
								xkey: 'y',
								ykeys: ['a'],
								labels: ['Sales'],
								xLabelAngle: 35,
								padding: 40,
								parseTime: false,
								hoverCallback: function(index, options, content) {
									var data = options.data[index];
									return("<p> Sales on "+data.y + "<br><span class='text-danger'>P. " + number_format(data.a,2) +"</span></p>");
								}
							});
						}
					},
					error:function(){

					}
				});
			}
			function topStation(mem_id,dt1,dt2){
				$.ajax({
					url:'../ajax/ajax_query2.php',
					type:'post',
					dataType:'json',
					beforeSend: function(){
						$('#topMemberStation').html('<h3 class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</h3>');
					},
					data: {functionName:'topMemberStation',mem_id:mem_id,dt1:dt1,dt2:dt2},
					success: function(data){
						$('#topMemberStation').html('');
						if (data.error){
							$('#topMemberStation').html('No data found.');
						} else {
							var a =0;
							Morris.Donut({
								element: 'topMemberStation',
								data: data,
								formatter: function (value, data) {
									return "\n" + number_format(value,2);
								}
							});
						}

					},
					error:function(){

					}
				});
			}
			function topItemMember(mem_id,dt1,dt2){
				$.ajax({
					url: '../ajax/ajax_query2.php',
					type: 'post',
					dataType: 'json',
					data: {functionName: 'topItemMember',mem_id:mem_id,dt1:dt1,dt2:dt2},
					success: function(data) {
						$('#topMemberSalesPerItem').html('');
						if (data.error){
							$('#topMemberSalesPerItem').html('No data found');
						} else {
							var a = 0;
							Morris.Bar({
								element: 'topMemberSalesPerItem',
								data: data,
								xkey: 'y',
								ykeys: ['a'],
								labels: ['Total Sales'],
								xLabelAngle: 35,
								padding: 40,
								hideHover: 'auto',
								barOpacity: 0.9,
								barRadius: [10, 10, 5, 5],
								barColors: function(row, series, type) {
									a = a + 1;
									if(a % 2 == 0) return "#B21516"; else return "#1531B2";
								},
								hoverCallback: function(index, options, content) {
									var data = options.data[index];
									return("<p> "+data.y + "<br><span class='text-danger'>P " + number_format(data.a,2) +"</span></p>");
								}
							});
						}
					},
					error: function() {

					}
				});
			}
			function statSaleTransaction(mem_id,type){
				if(type == 1){
					$.ajax({
						url:'../ajax/ajax_query2.php',
						type:'post',
						dataType:'json',
						beforeSend: function(){
							$('#statSaleTransaction').html('<h3 class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</h3>');
						},
						data: {functionName:'statsMemberPerTransaction',mem_id:mem_id,type:type},
						success: function(data){
								$('#statSaleTransaction').html('');
								if(data.error) {
									$('#statSaleTransaction').html('No data found.');
								} else {
									var a = 0;
									Morris.Donut({
										element: 'statSaleTransaction', data: data, formatter: function(value, data) {
											return "\n" + number_format(value, 2);
										}
									});
								}

						},
						error:function(){

						}
					});
				} else if (type == 2){
					$.ajax({
						url:'../ajax/ajax_query2.php',
						type:'post',
						beforeSend: function(){
							$('#statSaleTransactionTbl').html('<h3 class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</h3>');
						},
						data: {functionName:'statsMemberPerTransaction',mem_id:mem_id,type:type},
						success: function(data){
							$('#statSaleTransactionTbl').html('');
							$('#statSaleTransactionTbl').html(data);

						},
						error:function(){

						}
					});
				}

			}
			getPage(0,'',mem_id);
			$('body').on('click','.paging',function(e){
				e.preventDefault();

				if(whereami == 1){
					var page = $(this).attr('page');
					$('#hiddenpage').val(page);
					var search = $('#searchSales').val();
					var mem_id = $('#mem_id').val();
					getPage(page,search,mem_id);
				} else if (whereami == 2){
					var page = $(this).attr('page');
					$('#hiddenpage2').val(page);
					var mem_id = $('#mem_id').val();
					getPage2(page,mem_id);
				}else if (whereami == 3){
					var page = $(this).attr('page');
					$('#hiddenpage3').val(page);
					var mem_id = $('#mem_id').val();
					getPage3(page,mem_id);
				}

			});

			$("#searchSales").keyup(function(){
				var search = $('#searchSales').val();
				var mem_id = $('#mem_id').val();
				getPage(0,search,mem_id);
			});
			function getPage(p,search,mem_id){
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					data:{page:p,functionName:'membersTransactionPaginate',cid: <?php echo $user->data()->company_id; ?>,search:search,b:0,t:0,mem_id:mem_id},
					success: function(data){
						$('#holder').html(data);

					}
				});
			}
			function getPage2(p,m){
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder2').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,member_id:m,functionName:'unreadNotification',cid: <?php echo $user->data()->company_id; ?>},
					success: function(data){
						$('#holder2').html(data);
					}
				});
			}
			function getPage3(p,m){
				var type = $('#filter_diag_type').val();
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder3').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,member_id:m,type:type,functionName:'diagnosisList',cid: <?php echo $user->data()->company_id; ?>},
					success: function(data){
						$('#holder3').html(data);
					}
				});
			}
			$("body").on('click','.paymentDetails',function(){
				var payment_id = $(this).attr('data-payment_id');
				$.ajax({
					url: '../ajax/ajax_paymentDetails.php',
					type: 'POST',
					data: {id:payment_id},
					success: function(data){
						$("#mbody").html(data);
						$("#myModal").modal('show');
					}
				});
			});
			$("body").on('click','.editTransaction',function(){
				var payment_id = $(this).attr('data-payment_id');
				location.href='sales_crud.php?id='+payment_id;
			});
			$('#btnFilter').click(function(){
				$('#filterModal').modal('show');
			});
			$('#txtDT1').datepicker({
				autoClose:true
			}).on('changeDate',function(){
				$('#txtDT1').datepicker('hide');
			});
			$('#txtDT2').datepicker({
				autoClose:true
			}).on('changeDate',function(){
				$('#txtDT2').datepicker('hide');
			});
			$('#applyFilter').click(function(){
				var dt1= $('#txtDT1').val();
				var dt2 = $('#txtDT2').val();
				topStation(mem_id,dt1,dt2);
				topItemMember(mem_id,dt1,dt2);
				$("#filterModal").modal('hide');
			});
			$('body').on('click','.addrm',function(){
				var btn = $(this);
				var payment_id = btn.attr('data-payment_id');
				var item_id = btn.attr('data-item_id');
				$('#a_item_id').val(item_id);
				$('#a_payment_id').val(payment_id);
				$('.loading').show();
				$.ajax({
					url:'../ajax/ajax_query2.php',
					type:'post',
					data: {payment_id:payment_id,item_id:item_id,functionName:'getNotificationRemarks'},
					success: function(data){
						$('#addremarksholder').html(data);

						$('#myModal2').modal('show');
						$('.loading').hide();
					},
					error:function(){

						$('.loading').hide();
					}
				});

			});
			function getSubscription(m){
				$.ajax({
					url:'../ajax/ajax_query2.php',
					type:'post',
					beforeSend: function(){
						$('#subscriptionHolder').html('<h3 class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</h3>');
					},
					data: {functionName:'memberSubscription',mem_id:m},
					success: function(data){
						$('#subscriptionHolder').html(data);
					},
					error:function(){

					}
				});
			}
			function getConsumable(m){
				$.ajax({
					url:'../ajax/ajax_query2.php',
					type:'post',
					beforeSend: function(){
						$('#consumableQuantityHolder').html('<h3 class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</h3>');
					},
					data: {functionName:'memberConsumableQuantity',mem_id:m},
					success: function(data){
						$('#consumableQuantityHolder').html(data);
					},
					error:function(){

					}
				});
			}
			$('body').on('click','#btnPatienHistory',function(){
				$('#mtitle').html('');
				$('#mbody').html('Under construction...');
				$('#myModal').modal('show');
			});
			$('body').on('click','#btnAddDignosis',function(){
				$('#d_doctor_id').val('');
				$('#d_remarks').val('');
				$('#d_diag_type').val('');
				$('#d_nurse_id').val('');
				var doctor_holder = $('#doctor_holder');
				var nurse_holder = $('#nurse_holder');
				doctor_holder.hide();
				nurse_holder.hide();
				$('#myModal3').modal('show');
			});
			$('body').on('change','#d_diag_type',function(){
				var con = $(this);
				var v = con.val();
				$('#d_doctor_id').val('');
				$('#d_nurse_id').val('');
				var doctor_holder = $('#doctor_holder');
				var nurse_holder = $('#nurse_holder');
				doctor_holder.hide();
				nurse_holder.hide();
				if(v == 1){
					doctor_holder.fadeIn(300);
				} else if(v == 2){
					nurse_holder.fadeIn(300);
				} else {

				}
			});
			$('body').on('change','#filter_diag_type',function(){
				getPage3(0);
			});
			$('body').on('click','#btnSaveDiagnosis',function(){
				var doctor_id = $('#d_doctor_id').val();
				var nurse_id = $('#d_nurse_id').val();
				var remarks = $('#d_remarks').val();
				if((doctor_id || nurse_id) && remarks){
					var btncon = $(this);
					var btnoldval = btncon.html();
					btncon.attr('disabled',true);
					btncon.html('Loading...');
					$.ajax({
					    url:'../ajax/ajax_query.php',
					    type:'POST',
					    data: {functionName:'saveDiagnosis',member_id:mem_id,doctor_id:doctor_id,nurse_id:nurse_id,remarks:remarks},
					    success: function(data){
						    tempToast('info',"<p>"+data+"</p>","<h4>Info</h4>");
						    btncon.attr('disabled',false);
						    btncon.html(btnoldval);
						    $('#myModal3').modal('hide');
						    if(whereami == 3){
							    getPage3(0,mem_id);
						    }
					    },
					    error:function(){

						    btncon.attr('disabled',false);
						    btncon.html(btnoldval);
						    $('#myModal3').modal('hide');
					    }
					});
				} else{
					tempToast('error',"<p>Please complete the form.</p>","<h4>Error</h4>");
				}
			});
			$('body').on('click','.btnDeleteDiagnosis',function(){
				var id = $(this).attr('data-id');
				$.ajax({
				    url:'../ajax/ajax_query.php',
				    type:'POST',
				    data: {functionName:'deleteDiagnosis',id:id},
				    success: function(data){
				        alertify.alert(data);
					    getPage3(0,mem_id);
				    },
				    error:function(){

				    }
				})
			});

		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>