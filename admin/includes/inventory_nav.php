<div class="btn-group hidden-xs" role="group" aria-label="..." style='margin-bottom:10px;'>
	<?php if($user->hasPermission('inventory_add')) { ?>
		<a class='btn btn-default' href='addinventory.php' title='Add Inventory'>
			<span class='glyphicon glyphicon-plus'></span> <span class='hidden-xs'>Add Inventory</span> </a>
	<?php } ?>
	<?php if($user->hasPermission('inventory_adj')) { ?>
		<a class='btn btn-default' href="add-inv-log.php" title='Inventory Log'><i class='fa fa-plus-circle' ></i> <span class='hidden-xs'>Add Inventory Log</span> </a>
	<?php } ?>
	<?php if($user->hasPermission('inventory_adj') || $user->hasPermission('inventory_add')) { ?>
		<a class='btn btn-default' href="inventory_adjustments.php" title='Inventory Adjustments'>
			<span class='glyphicon glyphicon-check'></span> <span class='hidden-xs'>Inventory Audit</span>
		</a>
		<a class='btn btn-default' href="audit_log.php" title='Audit Report'>
			<span class='glyphicon glyphicon-dashboard'></span> <span class='hidden-xs'>Audit Report</span>
		</a>
	<?php } ?>
	<?php if($user->hasPermission('rack')) { ?>
		<a class='btn btn-default' href="rack.php" title='Manage Rack'><span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>Manage Rack</span> </a>
	<?php } ?>
	<?php if($user->hasPermission('inv_mon')) { ?>
		<a class='btn btn-default' href="inventory_monitoring.php"  title='Inventory Monitoring'><i class='fa fa-line-chart'></i> <span class='hidden-xs'>Inventory Monitoring</span> </a>
	<?php } ?>
	<?php if($user->hasPermission('inv_mon')) { ?>
		<a class='btn btn-default' href="inventory_report.php"  title='Report'><i class='fa fa-pie-chart'></i> <span class='hidden-xs'>Report</span> </a>
	<?php } ?>
	<?php if($user->hasPermission('in_out')) { ?>
		<a class='btn btn-default' href="in-out.php"  title='IN/OUT'><i class='fa fa-refresh'></i> <span class='hidden-xs'>In/Out</span> </a>
	<?php } ?>
	<?php if($user->hasPermission('inv_mon')) { ?>
		<a class='btn btn-default' href="audit-all.php"  title='Audit Summary'><i class='fa fa-line-chart'></i> <span class='hidden-xs'>Audit Summary</span> </a>
	<?php } ?>
	<?php if($user->hasPermission('witness')) { ?>
		<a class='btn btn-default' href="witness.php" title='Witness'><i class='fa fa-users' ></i> <span class='hidden-xs'>Witness</span> </a>
	<?php } ?>
	<?php if(false) { ?>
		<a class='btn btn-default' href="order_form_generator.php" title='Order Layout'><i class='fa fa-users' ></i> <span class='hidden-xs'>Form Layout</span> </a>
	<?php } ?>
</div>

<div class='visible-xs'>
	<button id='btnShowNavigationContainer' class='btn btn-default'><i class='fa fa-bars'></i></button>
	<div class='card-nav card-nav-2' id='secondNavigationContainer' style='display:none;'>
		<button class='btn btn-default btn-sm' id='btnRemoveSecondNavigationContainer'><i class='fa fa-remove'></i></button>

		<?php if($user->hasPermission('inventory_add')) { ?>
			<a class='btn btn-default btn-second-nav' href='addinventory.php' title='Add Inventory'>
				<span class='glyphicon glyphicon-plus'></span> <span class='title'>Add Inventory</span> </a>
		<?php } ?>
		<?php if($user->hasPermission('inventory_add')) { ?>
			<a class='btn btn-default btn-second-nav' href="add-inv-log.php" title='Inventory Log'><i class='fa fa-plus-circle' ></i> <span class='title'>Add Inventory Log</span> </a>
		<?php } ?>
		<?php if($user->hasPermission('inventory_adj')) { ?>
			<a class='btn btn-default btn-second-nav' href="inventory_adjustments.php" title='Inventory Adjustments'>
				<span class='glyphicon glyphicon-check'></span> <span class='title'>Inventory Audit</span>
			</a>
		<?php } ?>
		<?php if($user->hasPermission('rack')) { ?>
			<a class='btn btn-default btn-second-nav' href="rack.php" title='Manage Rack'><span class='glyphicon glyphicon-list'></span> <span class='title'>Manage Rack</span> </a>
		<?php } ?>
		<?php if($user->hasPermission('inv_mon')) { ?>
			<a class='btn btn-default btn-second-nav' href="inventory_monitoring.php"  title='Inventory Monitoring'><i class='fa fa-line-chart'></i> <span class='title'>Inventory Monitoring</span> </a>
		<?php } ?>
		<?php if($user->hasPermission('inv_mon')) { ?>
			<a class='btn btn-default btn-second-nav' href="inventory_report.php"  title='Report'><i class='fa fa-pie-chart'></i> <span class='title'>Report</span> </a>
		<?php } ?>

		<?php if($user->hasPermission('inv_mon')) { ?>
			<a class='btn btn-default btn-second-nav' href="audit-all.php"  title='Audit Summary'><i class='fa fa-line-chart'></i> <span class='title'>Audit Summary</span> </a>
		<?php } ?>
		<?php if($user->hasPermission('witness')) { ?>
			<a class='btn btn-default btn-second-nav' href="witness.php" title='Witness'><i class='fa fa-users' ></i> <span class='title'>Witness</span> </a>
		<?php } ?>
		<?php if(false) { ?>
			<a class='btn btn-default btn-second-nav' href="order_form_generator.php" title='Order Layout'><i class='fa fa-users' ></i> <span class='title'>Form Layout</span> </a>
		<?php } ?>
		</div>


</div>

