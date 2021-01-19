<?php

	if($user->data()->member_id != 0){
		Redirect::to("purchase_details.php?id=".Encryption::encrypt_decrypt('encrypt',$user->data()->member_id));
		exit();
	}
	if(!$user->hasPermission('dashboard')){
		// redirect to denied page
		Redirect::to(1);
	}
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
?>

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
					<?php if($user->hasPermission('mainpos')){
						?>
						<a style='color:#434a54;' href="pos.php"><i  class='fa fa-home'></i></a>
						<?php
					} ?>
					<?php if($user->hasPermission('inventory')){
						?>
						<a style='color:#434a54;'  href="for-releasing.php"><i class='fa fa-list'></i></a>
						<?php
					} ?>
					&nbsp;
				</span>
				</div>
			</div>
		</div>
		<?php

			if(Session::exists('homeflash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('homeflash') . "</div><br/>";
			}
		?>

		<div class="row">
			<div class="col-md-3">
				<div class="panel panel-default">
					<div class="panel-heading">
						<i class='fa fa-money'></i> Sales
						<span class='pull-right'><?php echo date('F Y'); ?></span>
					</div>
					<div class="panel-body">
						<h3><?php echo $currentsale;?></h3>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="panel panel-default">
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
			<div class="col-md-3">
				<div class="panel panel-default">
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
			<div class="col-md-3">
				<div class="panel panel-default">
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
			<div class="col-md-8">
				<div class="panel panel-default">
					<!-- Default panel contents -->
					<div class="panel-heading"><i class='fa fa-bar-chart'></i>  Monthly Sales Graph</div>
					<div class="panel-body">
						<?php if($saleslist != "[]") { ?>
							<div id="chart_div" style="width:90%; height: 450px;"></div>
						<?php } else {
							?>
							<p>No Data Yet.</p>
							<?php
						} ?>
					</div>
				</div>
			</div>
			<?php if($user->hasPermission('caravan_manage') || $user->hasPermission('orderpoint') || $user->hasPermission('order')){ ?>
				<div class="col-md-4">
					<div class="panel panel-default">
						<!-- Default panel contents -->
						<div class="panel-heading"><i class='fa fa-bell'></i> Notifications</div>
						<div class="panel-body">
							<ul class="list-group">
								<?php 	if($user->hasPermission('caravan_manage')){ ?>
									<li class="list-group-item">
										<?php echo $caravan_pending; ?>
										<a class='notif_link' href="manage_caravan.php"> Pending on Caravans</a>
									</li>
								<?php } ?>

								<?php if($user->hasPermission('order')){ ?>
									<li class="list-group-item">
										<?php echo $reservation_pending; ?>
										<a class='notif_link' href="manageorder.php">  Pending on Reservation </a>
									</li>
								<?php } ?>
								<?php if($user->hasPermission('notification')){ ?>
									<li class="list-group-item">
										<span class='badge'><?php echo $sb_alert_count->cnt; ?></span>

										<a  class='notif_link' href="notification.php"> Notification </a>
									</li>
								<?php } ?>
							</ul>
						</div>
					</div>
				</div>
			<?php } ?>
			<?php
				/*	$col = "C";
					$startrow = 4;
					$end = 180;
					$retstring = "=";
					for($ic = $startrow; $ic <= $end; $ic++){
						$retstring .= $col.$ic . '&","&';
					}
					echo $retstring; */
			?>
		</div>

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

			checkBranchTerminalSetup();
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