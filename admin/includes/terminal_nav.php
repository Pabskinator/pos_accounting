<?php
	$page_name =  basename($_SERVER['PHP_SELF']);
	if($page_name == 'addterminal.php'){
		$disable_deposit = "disabled";
	} else {
		$disable_deposit = '';
	}
?>
<div class="btn-group hidden-xs" role="group" aria-label="...">
	<a href='terminal.php' class='btn btn-default' title='Manage Terminal'>
		<span class='glyphicon glyphicon-home'></span>
							<span class='hidden-xs'>
								Manage Terminal
							</span>

	</a>
	<?php if($user->hasPermission('terminal_m')) { ?>

		<a href='addterminal.php' class='btn btn-default' title='Add Terminal'>
			<span class='glyphicon glyphicon-plus'></span>

								<span class='hidden-xs'>
								Add Terminal
							</span>
		</a>
	<?php } ?>

	<?php if($user->hasPermission('terminal_mon')) { ?>
		<a href='terminal-mon.php'  title='Monitoring' class='btn btn-default'>
			<i class='fa fa-line-chart'></i>
								<span class='hidden-xs'>
							Monitoring
							</span>
		</a>
	<?php } ?>
</div>
<div class='visible-xs'>
	<button id='btnShowNavigationContainer' class='btn btn-default'><i class='fa fa-bars'></i></button>
	<div class='card-nav card-nav-2' id='secondNavigationContainer' style='display:none;'>
		<button class='btn btn-default btn-sm' id='btnRemoveSecondNavigationContainer'><i class='fa fa-remove'></i></button>
		<a href='terminal.php' class='btn btn-default btn-second-nav' title='Manage Terminal'>
			<span class='glyphicon glyphicon-home'></span>
							<span class='title'>
								Manage Terminal
							</span>

		</a>
		<?php if($user->hasPermission('terminal_m')) { ?>

			<a href='addterminal.php' class='btn btn-default btn-second-nav' title='Add Terminal'>
				<span class='glyphicon glyphicon-plus'></span>

								<span class='title'>
								Add Terminal
							</span>
			</a>
		<?php } ?>

		<?php if($user->hasPermission('terminal_mon')) { ?>
			<a href='terminal-mon.php'  title='Monitoring' class='btn btn-default btn-second-nav'>
				<i class='fa fa-line-chart'></i>
								<span class='title'>
							Monitoring
							</span>
			</a>
		<?php } ?>
	</div>
</div>