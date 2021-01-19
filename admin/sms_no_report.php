<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('sms_log')) {
		// redirect to denied page
		Redirect::to(1);
	}
	$arr_to_check = [
		30,
		3,
		15,
		10,
		21,
		22,
		4,
		18,
		12,
		20,
		6,
		7,
		58,
		17,
		23,
		24,
		13,
		5,
		63,
		8
	];



?>


	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>Dicer Report Monitoring</h1>

		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">
						<div class='row'>
							<div class='col-md-6'>Log</div>
							<div class='col-md-6 text-right'>
								<button id='addItem' class='btn btn-default btn-sm'><i class='fa fa-plus'></i></button>
							</div>
						</div>
					</div>
					<div class="panel-body">
						<form action="" method="POST">
							<div class="row">
								<div class="col-md-3"><input type="text" autocomplete="off" id='dt' name='dt' class='form-control' placeholder='Enter Date'></div>
								<div class="col-md-3"><input type="submit" class='btn btn-default' value='Submit' name='btnSubmit'></div>
							</div>
						</form>
						<br>
						<?php
							$dt = Input::get('dt');
							if($dt){
								$dt = date('Y-m-d',strtotime($dt));
							} else {
								$dt = date('Y-m-d');
							}
							echo "<h3>Date of report: ".date('F d, Y',strtotime($dt))."</h3>";
							$withReport ="";
							$withoutReport ="";
							$ctr_no_report = 0;
							$ctr_with_report = 0;
							foreach($arr_to_check as $tid){
								$sms = new Sms_receive();
								$chk = $sms->terminalHasReport($dt,$tid);
								$terminal = new Terminal($tid);
								if($chk){
									$ctr_with_report += 1;
									$withReport .= "<div class='alert alert-info'>".$terminal->data()->name." has already reported</div>";
								} else {
									$ctr_no_report += 1;
									$withoutReport .= "<div class='alert alert-danger'>".$terminal->data()->name." doesn't have a report.</div>";
								}
							}
							echo "<p><strong>With report: $ctr_with_report</strong> <strong>Without report: $ctr_no_report</strong></p>";
							echo $withoutReport;
							echo $withReport;
						?>

					</div>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->

	<script>

		$(document).ready(function() {
			$('#dt').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt').datepicker('hide');
			});
		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>