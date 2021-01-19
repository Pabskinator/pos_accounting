<div id="navhider" xmlns="http://www.w3.org/1999/html"><i class='fa fa-gear'></i></div>
<div id="sidebar-wrapper">
	<div style='width:220px;padding-bottom:40px; '>
		<br/>
		<div class="panel-group" id="accordion">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title" style=''>
						<a  href="index.php"> Dashboard</a>
					</h4>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion" href="#collapsetest">Monitoring </a>
					</h4>
				</div>
				<div id="collapsetest" class="panel-collapse collapse">
					<div class="panel-body">
						<?php
							$myProcess = new Process();
							$myProcesses = $myProcess->get_active('processes',array('company_id' ,'=',$user->data()->company_id));
							if($myProcesses){

								foreach($myProcesses as $p){
									$myStep = new Steps();
									$mySteps = $myStep->getMyStep($p->id);
									$approve_auth = $myStep->hasAuth($p->id,$user->data()->position_id);

									if(!(isset($approve_auth->cnt) && !empty($approve_auth->cnt))){
										continue;
									}
									if(!$mySteps){
										continue;
									}


									?>
									<div class="panel panel-default" >
										<div class="panel-heading" >
											<h4 class="panel-title">
												<a data-toggle="collapse" data-parent="#collapsetest" href="#<?php echo escape($p->id)?>"><?php echo escape($p->name)?> </a>
											</h4>
										</div>
										<div id="<?php echo escape($p->id)?>" class="panel-collapse collapse">
											<div class="panel-body">
												<table class="table">

													<?php
														foreach($mySteps as $s){
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
															<tr>
																<td>
																	<a href="main_monitoring.php?process=<?php echo $p->id; ?>&step=<?php echo $s->id; ?>"><?php echo escape($s->name)?> <span class="badge badge-danger"><?php echo $countPendingOnStep->countPending; ?></span></a>
																</td>
															</tr>
															<?php
														}
													?>

												</table>
											</div>
										</div>
									</div>

									<?php

								}
							} else {

							}
						?>
					</div>
				</div>
			</div>
			<?php
				$myProcess = new Process();
				$myProcesses = $myProcess->get_active('processes',array('company_id' ,'=',$user->data()->company_id));
				if($myProcesses){

				foreach($myProcesses as $p){
					$myStep = new Steps();
					$mySteps = $myStep->getMyStep($p->id);
					$approve_auth = $myStep->hasAuth($p->id,$user->data()->position_id);

					if(!(isset($approve_auth->cnt) && !empty($approve_auth->cnt))){
						continue;
					}
					if(!$mySteps){
						continue;
					}


					?>
					<div class="panel panel-default" >
						<div class="panel-heading" >
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion" href="#<?php echo escape($p->id)?>"><?php echo escape($p->name)?> </a>
							</h4>
						</div>
						<div id="<?php echo escape($p->id)?>" class="panel-collapse collapse">
							<div class="panel-body">
								<table class="table">

									<?php
										foreach($mySteps as $s){
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
											<tr>
												<td>
													<a href="main_monitoring.php?process=<?php echo $p->id; ?>&step=<?php echo $s->id; ?>"><?php echo escape($s->name)?> <span class="badge badge-danger"><?php echo $countPendingOnStep->countPending; ?></span></a>
												</td>
											</tr>
										<?php
										}
									?>

								</table>
							</div>
						</div>
					</div>

				<?php

				}
			} else {

			}
			?>

			<!-- start  -->

			<!-- end  -->
			<?php
				$myFm = new FormRequest();
				$myRequest = $myFm->get_who_can_request();
				if($myRequest){
					?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion" href="#collapseRequest"> Create Request </a>
							</h4>
						</div>
						<div id="collapseRequest" class="panel-collapse collapse">
							<div class="panel-body">
								<table class="table">
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
											<tr>
												<td>
													<a href="createRequest.php?process=<?php echo $req->process_id; ?>"><?php echo $req->process_name; ?></a>
												</td>
											</tr>
										<?php
										}
									?>
								</table>
							</div>
						</div>
					</div>
				<?php
				}
			?>
			<!-- end  -->
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion" href="#collapsePending">My Request </a>
					</h4>
				</div>
				<div id="collapsePending" class="panel-collapse collapse">
					<div class="panel-body">
						<table class="table">
							<tr>
								<td>
									<a  href="my_request.php"> Pending Request</a>
								</td>
							</tr>
							<tr>
								<td>
									<a  href="approved.php"> Approved Request</a>
								</td>
							</tr>
							<tr>
								<td>
									<a  href="decline.php"> Decline Request</a>
								</td>
							</tr>
							<tr>
								<td>
									<a href="request_monitoring.php">Request Monitoring</a>
								</td>
							</tr>
						</table>
					</div>
			</div>
				</div>

			<?php if($user->hasPermission('tools_mon')){
				?>

			<div class="panel panel-default" >
				<div class="panel-heading" >
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion" href="#collapseThree"> Tools </a>
					</h4>
				</div>
				<div id="collapseThree" class="panel-collapse collapse" >
					<div class="panel-body">
						<table class="table">
							<tr>
								<td>
									<a href="process.php">Manage Process</a>
								</td>
							</tr>
							<tr>
								<td>
									<a href="steps.php">Manage Steps</a>
								</td>
							</tr>
							<tr>
								<td>
									<a href="forms.php">Manage Forms</a>
								</td>
							</tr>
							<tr>
								<td>
									<a href="requestForm.php">Request Forms</a>
								</td>
							</tr>


						</table>
					</div>
				</div>
			</div>
				<?php
			}?>
		</div>
	</div>

</div>

