<?php

	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require_once '../includes/monitoring/page_head.php';
	if(!$user->hasPermission('dashboard')){
		// redirect to denied page
		//	Redirect::to(1);
	}

?>
	<style>
		.paper{
			color: rgba(0, 0, 0, 0.870588);
			transition: all 450ms cubic-bezier(0.23, 1, 0.32, 1) 0ms;
			box-sizing: border-box;
			font-family: Roboto, sans-serif;
			-webkit-tap-highlight-color: rgba(0, 0, 0, 0);
			box-shadow: rgba(0, 0, 0, 0.156863) 0px 3px 10px, rgba(0, 0, 0, 0.227451) 0px 3px 10px;
			border-radius: 2px;
			height: 150px;
			width: 100%;
			margin: 20px;
			text-align: center;
			display: inline-block;
			background-color: rgb(255, 255, 255);
		}
		.paper > div{
			position:relative;
			top:45%;
		}
		.paper a{
			color:#fff;
			text-shadow: 1px 1px #000;
			font-size: 1.5em;
		}
	</style>

	<!-- Sidebar -->
<?php include_once '../includes/monitoring/sidebar.php';?>
	<!-- Page content -->
	<div id="page-content-wrapper" style='padding-top: 30px;'>
		<div class="container">
			<?php
				$colorsbg = ['#434a54'];
				$length = count($colorsbg);
				$ctr = rand(0,$length);
				$myFm = new FormRequest();
				$myRequest = $myFm->get_who_can_request();
				if($myRequest){
					?>
					<h1>Create Request</h1>
					<div class="row" style='width:100%;padding:0px;margin:0px;'>
						<?php


							foreach ($myRequest as $req) {
								$belongform = false;
								$whos_can_res = explode(',' , $req->who_can_request);
								foreach ($whos_can_res as $value) {
									if($value == $user->data()->position_id){
										$belongform = true;
										break;
									}
								}
								if(!$belongform){
									continue;
								}
								?>
								<div class="col-md-3">
									<div class='paper' style='background-color: #434a54;'>
										<div>
											<a  href="createRequest.php?process=<?php echo $req->process_id; ?>"><?php echo $req->process_name; ?></a>
										</div>
									</div>
								</div>
								<?php
								$ctr++;
								if($ctr == $length+1){
									$ctr = rand(0,$length);
								}
							}
						?>
					</div>
					<?php
				} else {
					echo "<div class='alert alert-info'>You can't create request at the moment.</div>";
				}
			?>

			<!-- Keep all page content within the page-content inset div! -->
			<?php
				$dprocess = new Process();
				$dprocesses = $dprocess->get_active('processes',array('company_id' ,'=',$user->data()->company_id));
				if($dprocesses){
					?>
					<h1>Monitoring</h1>
					<div class="row" style='width:100%;padding:0px;margin:0px;'>
						<?php
							$hasMon = false;
							foreach($dprocesses as $p){
								$dStep = new Steps();
								$dSteps = $dStep->getMyStep($p->id);
								$approve_auth = $dStep->hasAuth($p->id,$user->data()->position_id);

								if(!(isset($approve_auth->cnt) && !empty($approve_auth->cnt))){
									continue;
								}
								if(!$dSteps){
									continue;
								}
								$hasMon = true;
								?>
								<div class="col-md-12">
									<div>
										<h3> <?php  echo escape($p->name); ?></h3>
									</div>
								</div>

								<?php
								$ctr = rand(0,6);
								foreach($dSteps as $s){
									$belongstep = false;
									$whos_res = explode(',' , $s->whos_responsible);
									foreach ($whos_res as $value) {
										if($value == $user->data()->position_id){
											$belongstep = true;
											break;
										}
									}
									if(!$belongstep){
										continue;
									}

									$countPending = new Monitoring();
									$countPendingOnStep = $countPending->countPending($p->id,$s->step_number);

									?>
									<div class="col-md-3">
										<div class='paper' style='background-color: #434a54;'>
											<div>
												<a href="main_monitoring.php?process=<?php echo $p->id; ?>&step=<?php echo $s->id; ?>"><?php echo escape($s->name)?> (<?php echo $countPendingOnStep->countPending; ?>)</a>
											</div>
										</div>
									</div>
									<?php
									$ctr++;
									if($ctr == 7){
										$ctr = rand(0,6);
									}
								}
							}
						?>
					</div>
					<?php
					if(!$hasMon){
						echo "<div class='alert alert-info'>No monitoring request</div>";
					}
					?>

					<?php
				} else {
					?>
					<div class="container">
						<div class="alert alert-info">
							No Request Yet.
						</div>
					</div>
					<?php
				}
			?>

		</div>
	</div>
	<script type="text/javascript">
		$(function(){


		})

	</script>


<?php require_once '../includes/monitoring/page_tail.php'; ?>