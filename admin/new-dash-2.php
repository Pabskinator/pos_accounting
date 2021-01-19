<?php
	$sales = new Sales();
	$dateStart = date('m/01/Y');
	$dateEnd = date('m/d/Y',strtotime(date('m/d/Y')));

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


	$totalCash = $sales->totalByPaymentMethod('cash',$dateStart,$dateEnd);
	$totalCheque =  $sales->totalByPaymentMethod('cheque',$dateStart,$dateEnd);
	$totalCreditCard = $sales->totalByPaymentMethod('credit_card',$dateStart,$dateEnd);
	$totalBankTransfer= $sales->totalByPaymentMethod('bank_transfer',$dateStart,$dateEnd);
	$totalMemberCredit= $sales->totalByPaymentMethod('member_credit',$dateStart,$dateEnd);


	$total = $totalCash->totalamount + $totalCheque->totalamount + $totalCreditCard->totalamount + $totalBankTransfer->totalamount + $totalMemberCredit->totalamount;


	/* last month */
	$dateStartPrev =  date('m/d/Y',strtotime(date('m/01/Y') . "-1 month"));
	$dateEndPrev = date('m/d/Y',strtotime(date('m/01/Y') . "-1 sec"));

	$totalCashPrev = $sales->totalByPaymentMethod('cash',$dateStartPrev,$dateEndPrev);
	$totalChequePrev = $sales->totalByPaymentMethod('cheque',$dateStartPrev,$dateEndPrev);
	$totalCreditCardPrev =  $sales->totalByPaymentMethod('credit_card',$dateStartPrev,$dateEndPrev);
	$totalBankTransferPrev=  $sales->totalByPaymentMethod('bank_transfer',$dateStartPrev,$dateEndPrev);
	$totalMemberCreditPrev= $sales->totalByPaymentMethod('member_credit',$dateStartPrev,$dateEndPrev);

	$totalCashPrev->totalamount = ($totalCashPrev->totalamount) ? $totalCashPrev->totalamount : 0;
	$totalCreditCardPrev->totalamount = ($totalCreditCardPrev->totalamount) ? $totalCreditCardPrev->totalamount : 0;
	$totalChequePrev->totalamount = ($totalChequePrev->totalamount) ? $totalChequePrev->totalamount : 0;




	if($totalCash->totalamount &&  $totalCashPrev->totalamount){
		$cashStatus = ( $totalCash->totalamount / $totalCashPrev->totalamount) * 100 ;
		$cashStatus = $cashStatus  - 100;
	} else if ($totalCash->totalamount &&  !$totalCashPrev->totalamount){
		$cashStatus = 100;
	}else if (!$totalCash->totalamount &&  $totalCashPrev->totalamount){
		$cashStatus = -100;
	}else {
		$cashStatus = 0;
	}

	if($totalCreditCard->totalamount &&  $totalCreditCardPrev->totalamount){
		$creditCardStatus = ( $totalCreditCard->totalamount / $totalCreditCardPrev->totalamount) * 100 ;
		$creditCardStatus = $creditCardStatus  - 100;
	} else if ($totalCreditCard->totalamount &&  !$totalCreditCardPrev->totalamount){
		$creditCardStatus = 100;
	}else if (!$totalCreditCard->totalamount &&  $totalCreditCardPrev->totalamount){
		$creditCardStatus = -100;
	}else {
		$creditCardStatus = 0;
	}


	if($totalCheque->totalamount &&  $totalChequePrev->totalamount){
		$chequeStatus = ( $totalCheque->totalamount / $totalChequePrev->totalamount) * 100 ;
		$chequeStatus = $chequeStatus  - 100;
	} else if ($totalCheque->totalamount &&  !$totalChequePrev->totalamount){
		$chequeStatus = 100;
	}else if (!$totalCheque->totalamount &&  $totalChequePrev->totalamount){
		$chequeStatus = -100;
	}else {
		$chequeStatus = 0;
	}

	if($totalBankTransfer->totalamount &&  $totalBankTransferPrev->totalamount){
		$bankTransferStatus = ( $totalBankTransfer->totalamount / $totalBankTransferPrev->totalamount) * 100 ;
		$bankTransferStatus = $bankTransferStatus  - 100;
	} else if ($totalBankTransfer->totalamount &&  !$totalBankTransferPrev->totalamount){
		$bankTransferStatus = 100;
	}else if (!$totalBankTransfer->totalamount &&  $totalBankTransferPrev->totalamount){
		$bankTransferStatus = -100;
	}else {
		$bankTransferStatus = 0;
	}

	if($totalMemberCredit->totalamount &&  $totalMemberCreditPrev->totalamount){
		$memberCreditStatus = ( $totalMemberCredit->totalamount / $totalMemberCreditPrev->totalamount) * 100 ;
		$memberCreditStatus = $memberCreditStatus  - 100;
	} else if ($totalMemberCredit->totalamount &&  !$totalMemberCreditPrev->totalamount){
		$memberCreditStatus = 100;
	}else if (!$totalMemberCredit->totalamount &&  $totalMemberCreditPrev->totalamount){
		$memberCreditStatus = -100;
	} else {
		$memberCreditStatus = 0;
	}



	$totalPrev = $totalCashPrev->totalamount + $totalChequePrev->totalamount + $totalCreditCardPrev->totalamount + $totalBankTransferPrev->totalamount + $totalMemberCreditPrev->totalamount;

	if($total &&  $totalPrev){
		$totalStatus = ( $total /$totalPrev) * 100 ;
		$totalStatus = $totalStatus  - 100;
	} else if ($total &&  !$totalPrev){
		$totalStatus = 100;
	}else if (!$total &&  $totalPrev){
		$totalStatus = -100;
	} else {
		$totalStatus = 0;
	}




?>
<link rel="stylesheet" href="../css/custom.css">
<div class="container-fluid">



	<div class="row tile_count">
		<div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
			<span class="count_top"><i class="fa fa-user"></i> Total Cash</span>
			<div class="count"><?php echo number_format($totalCash->totalamount,2); ?></div>
			<span class="count_bottom"><i title='<?php echo  number_format($totalCashPrev->totalamount,0); ?>' class="<?php echo ($cashStatus > 0 ) ? "green" : "red"; ?>"><i class='fa fa-sort-asc'></i> <?php echo number_format($cashStatus,0); ?>% </i> From last month</span>
		</div>
		<div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
			<span class="count_top"><i class="fa fa-user"></i> Total Credit Card</span>
			<div class="count"><?php echo number_format($totalCreditCard->totalamount,2); ?></div>
			<span class="count_bottom"><i title='<?php echo  number_format($totalCreditCardPrev->totalamount,0); ?>' class="<?php echo ($creditCardStatus > 0 ) ? "green" : "red"; ?>"><i class='fa fa-sort-asc'></i> <?php echo number_format($creditCardStatus,0); ?>% </i> From last month</span>
		</div>
		<div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
			<span class="count_top"><i class="fa fa-user"></i> Total Cheque</span>
			<div class="count"><?php echo number_format($totalCheque->totalamount,2); ?></div>
			<span class="count_bottom"><i title='<?php echo  number_format($totalChequePrev->totalamount,0); ?>'  class="<?php echo ($chequeStatus > 0 ) ? "green" : "red"; ?>"><i class='fa fa-sort-desc'></i> <?php echo number_format($chequeStatus,0); ?>% </i> From last month</span>
		</div>
		<div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
			<span class="count_top"><i class="fa fa-user"></i> Total Bank Transfer</span>
			<div class="count"><?php echo number_format($totalBankTransfer->totalamount,2); ?></div>
			<span class="count_bottom"><i title='<?php echo  number_format($totalBankTransferPrev->totalamount,0); ?>' class="<?php echo ($bankTransferStatus > 0 ) ? "green" : "red"; ?>"><?php echo number_format($bankTransferStatus,0); ?>% </i> From last month</span>
		</div>
		<div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
			<span class="count_top"><i class="fa fa-user"></i> Total Client Credit</span>
			<div class="count"><?php echo number_format($totalMemberCredit->totalamount,2); ?></div>
			<span class="count_bottom"><i title='<?php echo  number_format($totalMemberCreditPrev->totalamount,0); ?>' class="<?php echo ($memberCreditStatus > 0 ) ? "green" : "red"; ?>"><?php echo number_format($memberCreditStatus,0); ?>% </i> From last month</span>
		</div>
		<div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
			<span class="count_top"><i class="fa fa-user"></i> Grand Total</span>
			<div class="count green"><?php echo number_format($total,2); ?></div>
			<span class="count_bottom"><i title='<?php echo  number_format($totalPrev,0); ?>' class="<?php echo ($totalStatus > 0 ) ? "green" : "red"; ?>"><i class='fa fa-sort-asc'></i> <?php echo number_format($totalStatus,0); ?>% </i> From last month</span>
		</div>
	</div>

	<!-- graphs -->
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="x_panel tile fixed_height_320">
				<div class="x_title">
					<h2>Sales</h2>
					<ul class="nav navbar-right panel_toolbox">
						<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
						</li>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
							<ul class="dropdown-menu" role="menu">
								<li><a href="#">Settings 1</a>
								</li>
								<li><a href="#">Settings 2</a>
								</li>
							</ul>
						</li>
						<li><a class="close-link"><i class="fa fa-close"></i></a>
						</li>
					</ul>
					<div class="clearfix"></div>
				</div>
				<div class="x_content">
					<div id="line-example"  style='height: 220px;'></div>
				</div>
			</div>
		</div>
		<!-- Donut -->
		<div class="col-md-4 col-sm-4 col-xs-12" style='display:none;'>
			<div class="x_panel tile fixed_height_320 overflow_hidden">
				<div class="x_title">
					<h2>Pending Order</h2>
					<ul class="nav navbar-right panel_toolbox">
						<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
						</li>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
							<ul class="dropdown-menu" role="menu">
								<li><a href="#">Settings 1</a>
								</li>
								<li><a href="#">Settings 2</a>
								</li>
							</ul>
						</li>
						<li><a class="close-link"><i class="fa fa-close"></i></a>
						</li>
					</ul>
					<div class="clearfix"></div>
				</div>
				<div class="x_content">

					<div id="donut-example" style='height: 220px;'></div>

				</div>
			</div>
		</div>
		<!-- end donut-->

	</div> <!-- end graph -->

	<!-- Product, User stats-->
	<div class="row top_titles">

		<div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
			<div class="tile-stats">
				<div class="icon"><i class="fa fa-barcode"></i></div>
				<div class="count">&nbsp;</div>
				<h3><a href="product.php">Products</a></h3>
				<p>All items in our system</p>
			</div>
		</div>


		<div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
			<div class="tile-stats">
				<div class="icon"><i class="fa fa-user"></i></div>
				<div class="count">&nbsp;</div>
				<h3><a href="members.php">Clients</a></h3>
				<p>Client list</p>
			</div>
		</div>

		<div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
			<div class="tile-stats">
				<div class="icon"><i class="fa fa-home"></i></div>
				<div class="count">&nbsp;</div>
				<h3><a href="reports2.php">Sales</a></h3>
				<p>Detailed sales report</p>
			</div>
		</div>
		<div class="animated flipInY col-lg-3 col-md-6 col-sm-6 col-xs-12">
			<div class="tile-stats">
				<div class="icon"><i class="fa fa-map-marker"></i></div>
				<div class="count">&nbsp;</div>
				<h3><a href="report-item.php">Reports</a></h3>
				<p>Summary Report</p>
			</div>
		</div>
	</div>
	<!-- Enb Product, User stats-->

	<!-- By Date Stats-->
	<?php if(!Configuration::isGym()){
		?>

		<div class="row">
			<div class="col-md-4">
				<div class="x_panel">
					<div class="x_title">
						<h2>Order request <small></small></h2>
						<ul class="nav navbar-right panel_toolbox">
							<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
							</li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
								<ul class="dropdown-menu" role="menu">
									<li><a href="#">Settings 1</a>
									</li>
									<li><a href="#">Settings 2</a>
									</li>
								</ul>
							</li>
							<li><a class="close-link"><i class="fa fa-close"></i></a>
							</li>
						</ul>
						<div class="clearfix"></div>
					</div>
					<div class="x_content">
						<div id="order_container"></div>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="x_panel">
					<div class="x_title">
						<h2>Service request <small></small></h2>
						<ul class="nav navbar-right panel_toolbox">
							<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
							</li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
								<ul class="dropdown-menu" role="menu">
									<li><a href="#">Settings 1</a>
									</li>
									<li><a href="#">Settings 2</a>
									</li>
								</ul>
							</li>
							<li><a class="close-link"><i class="fa fa-close"></i></a>
							</li>
						</ul>
						<div class="clearfix"></div>
					</div>
					<div class="x_content">
						<div id="service_container"></div>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="x_panel">
					<div class="x_title">
						<h2>Term request <small></small></h2>
						<ul class="nav navbar-right panel_toolbox">
							<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
							</li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
								<ul class="dropdown-menu" role="menu">
									<li><a href="#">Settings 1</a>
									</li>
									<li><a href="#">Settings 2</a>
									</li>
								</ul>
							</li>
							<li><a class="close-link"><i class="fa fa-close"></i></a>
							</li>
						</ul>
						<div class="clearfix"></div>
					</div>
					<div class="x_content">
						<div id='terms_container'></div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}?>
	<!-- End By Date Stats-->

</div>

<script>
	$(function(){
		/*Morris.Donut({
			element: 'donut-example',
			data: [
				{label: "For Approval", value: 12},
				{label: "Warehouse", value: 30},
				{label: "Shipping", value: 20}
			]
		}); */



		setTimeout(function(){
			getPast10();
		},1000)

		function getPast10(){
			$.ajax({
				url:'../ajax/ajax_query.php',
				type:'post',
				dataType:'json',
				data: {branch:localStorage['branch_id'],functionName:'getPast10'},
				success: function(data){
					$('#salesPastTenDays').html('');
					Morris.Line({
						element: 'line-example',
						data: data,
						xkey: 'y',
						ykeys: ['a'],
						labels: ['Sales'],
						xLabelAngle: 35,
						padding: 40,
						parseTime: false
					});
				},
				error:function(){

				}
			});
		}

		getRequestCount();
		function getRequestCount(){
			$.ajax({
				url:'../ajax/ajax_sales_query.php',
				type:'POST',
				dataType:'json',
				data: {functionName:'getRequestNew'},
				success: function(data){

					if(data.orders.length > 0){

						var html_order = "";
						for(var i in data.orders){
							var id = data.orders[i].id;
							var branch_name = data.orders[i].branch_name;
							var mln = data.orders[i].mln;
							if(!mln){
								mln = data.orders[i].to_branch_name;
								if(!mln){
									mln = "";
								}
							}
							mln += "<small class='green span-block'>"+data.orders[i].created+"</small>";
							html_order += '<article class="media event"><a class="pull-left date"><p class="month">'+id+'</p></a><div class="media-body"><a class="title" href="#">'+branch_name+'</a><p>'+mln+'</p></div></article>';
						}
						$('#order_container').html(html_order);
					}

					if(data.services.length > 0){

						var html_service= "";
						for(var i in data.services){
							var id = data.services[i].id;
							var branch_name = data.services[i].branch_name;
							var mln = data.services[i].mln;
							if(!mln){

								mln = "";

							}
							mln += "<small class='green span-block'>"+data.services[i].created+"</small>";
							html_service += '<article class="media event"><a class="pull-left date"><p class="month">'+id+'</p></a><div class="media-body"><a class="title" href="#">'+branch_name+'</a><p>'+mln+'</p></div></article>';
						}
						$('#service_container').html(html_service);
					}

					if(data.terms.length > 0){
						console.log(data.terms);
						var html_terms= "";
						for(var i in data.terms){
							var id = data.terms[i].id;
							var branch_name =  data.terms[i].item_code + " ("+data.terms[i].adjustment+")";
							var mln = data.terms[i].lastname;

							if(!mln){
								mln = "";
							}

							mln += "<small class='green span-block'>"+data.terms[i].created+"</small>";
							html_terms += '<article class="media event"><a class="pull-left date"><p class="month">'+id+'</p></a><div class="media-body"><a class="title" href="#">'+branch_name+'</a><p>'+mln+'</p></div></article>';
						}
						$('#terms_container').html(html_terms);
					}
				},
				error:function(){

				}
			});
		}
	});

</script>
