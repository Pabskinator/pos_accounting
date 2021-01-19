<div class="btn-group hidden-xs" role="group" aria-label="..." style='margin-bottom:10px;'>
	<?php
		if($user->hasPermission('r_item')) {
			?>
			<a class='btn btn-default' href='report-item.php'  title='Item Report'>
				<span class='glyphicon glyphicon-barcode'></span> <span class='hidden-xs'>Item</span></span></a>
			<?php
		}
	?>

	<?php
		if($user->hasPermission('r_client')) {
			?>
			<a class='btn btn-default' href='report-member.php'  title='Member Report'>
				<span class='fa fa-users'></span> <span class='hidden-xs'>Client</span></span></a>
			<?php
		}
	?>
	<?php
		if($user->hasPermission('r_order')) {
			?>
			<a class='btn btn-default' href='report-order.php'  title='Order Report'>
				<span class='fa fa-home'></span> <span class='hidden-xs'>Order</span></span></a>
			<?php
		}
	?>
	<?php
		if($user->hasPermission('daily_sales')) {
			?>
			<a class='btn btn-default' href='daily-summary.php'  title='Daily Summary'>
				<span class='fa fa-calendar'></span> <span class='hidden-xs'>Daily Summary</span></span></a>
			<?php
		}
	?>
	<?php
		if($user->hasPermission('st_sum_sales')) {
			?>
			<a class='btn btn-default' href='sales-type-summary.php'  title='Sales Type Summary'>
				<span class='fa fa-book'></span> <span class='hidden-xs'>Sales Type Summary</span></span></a>
			<?php
		}
	?>
	<?php
		if($user->hasPermission('deduction_summary')) {
			?>
			<a class='btn btn-default' href='deduction-summary.php'  title='Deduction Summary'>
				<span class='fa fa-minus'></span> <span class='hidden-xs'>Deduction Summary</span></span>
			</a>
			<?php
		}
	?>

	<?php
		if($user->hasPermission('r_quota')) {
			?>
			<a class='btn btn-default' href='branch-quotas.php'  title='Quota Summary'>
				<span class='fa fa-line-chart'></span> <span class='hidden-xs'>Quota Report</span></span>
			</a>
			<?php
		}
	?>
	<?php
		if($user->hasPermission('r_freebie')) {
			?>
			<a class='btn btn-default' href='freebie-summary.php'  title='Freebie Report'>
				<span class='fa fa-money'></span> <span class='hidden-xs'>Freebies Summary</span></span></a>
			<?php
		}
	?>
</div>

<div class='visible-xs'>
	<button id='btnShowNavigationContainer' class='btn btn-default'><i class='fa fa-bars'></i></button>
	<div class='card-nav card-nav-2' id='secondNavigationContainer' style='display:none;'>
		<button class='btn btn-default btn-sm' id='btnRemoveSecondNavigationContainer'><i class='fa fa-remove'></i></button>
		<?php
			if(true) {
				?>

				<?php
			}
		?>

	</div>
</div>