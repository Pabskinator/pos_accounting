<div class="row">
	<div class="col-md-8">
		<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>

			<?php if($user->hasPermission('item_service_r') || $user->hasPermission('item_service_s') || $user->hasPermission('item_service_p')){ // change later
				?>
				<a class='btn btn-default' title='Service List' href='item-service.php'> <span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>For Servicing</span></a>
				<?php
			}?>

			<?php if($user->hasPermission('item_service_r')){ // change later
				?>
				<a class='btn btn-default' title='For approval'  href='item-service-request.php'> <span class='glyphicon glyphicon-pencil'></span> <span class='hidden-xs'>Request Service</span></a>
				<?php
			}?>

			<?php if($user->hasPermission('item_service_l')){ // change later
				?>
				<a class='btn btn-default' title='Log'  href='item-service-log.php'> <span class='glyphicon glyphicon-list-alt'></span> <span class='hidden-xs'>Service Log</span></a>
			<?php
			} ?>
			<?php if($user->hasPermission('item_service_l') && Configuration::thisCompany('avision')){ // change later
				?>
				<a class='btn btn-default' title='Claims and Refund'  href='avision_service_mon.php'> <span class='fa fa-money'></span> <span class='hidden-xs'>Claims/Refund</span></a>
			<?php
			} ?>


		</div>
	</div>
	<div class="col-md-4">
		<div class="btn-group" role="group" aria-label="...">
			<?php if($user->hasPermission('item_service_l') || $user->hasPermission('item_service_t')){ ?>
				<div class="btn-group" role="group">
				<div class="dropdown">
					<a class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
						Reports
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
						<?php if($user->hasPermission('item_service_l')){ // change later
							?>
							<li><a  title='Tech Log'  href='technician_report.php'>  <span class='hidden-xs'>Technician Log</span></a></li>
							<?php
						}?>
						<?php if($user->hasPermission('item_service_t')){ // change later
							?>
							<li><a  title='Item Used'  href='item-service-used.php'> <span class='hidden-xs'>Item Used</span></a></li>
							<li><a  title='Item Requested'  href='item-service-requested.php'> <span class='hidden-xs'>Item Requested</span></a></li>
							<li><a  title='Unliquidated'  href='service-unliquidated.php'> <span class='hidden-xs'>Unliquidated</span></a></li>
							<?php if(Configuration::isAquabest()){ // change later
							?>
							<li><a  title='Summary'  href='service_summary.php'> <span class='hidden-xs'>Summary</span></a></li>
							<li><a  title='Summary'  href='service_type_summary.php'> <span class='hidden-xs'>Service type</span></a></li>
							<?php }?>
							<?php
						}?>
					</ul>
				</div>
				</div>
			<?php
				}
			?>
			<?php if($user->hasPermission('item_service_t') || $user->hasPermission('item_service_t') || $user->hasPermission('measure')){ ?>
			<div class="btn-group" role="group">
		<div class="dropdown">
			<a class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
				Utilities
				<span class="caret"></span>
			</a>
			<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
				<?php if($user->hasPermission('item_service_t')){ // change later
					?>
					<li><a title='Technician'  href='technician.php'> <span class='hidden-xs'>Technicians</span></a></li>
					<?php
				}?>
				<?php if($user->hasPermission('item_service_t')){ // change later
					?>
					<li><a  title='Form Layout'  href='service_form_generator.php'>  <span class='hidden-xs'>Service form</span></a></li>
					<?php
				}?>
				<?php if($user->hasPermission('item_service_t')){ // change later
					?>

					<li><a title='Service Type'  href='service_type.php'> <span class='hidden-xs'>Service Type</span></a></li>

					<?php
				}?>

				<?php if($user->hasPermission('measure')){ // change later
					?>
					<li><a  title='Service Type'  href='service_measurement.php'> <span class='hidden-xs'>Service Measurement</span></a></li>
					<?php
				}?>
			</ul>
		</div>
		</div>
				<?php
			}?>
			</div>

	</div>
</div>

