<div class="btn-group  hidden-xs" role="group" aria-label="..." style='margin-bottom:10px;'>
	<?php
		if($user->hasPermission('item_m')) {
			?>
			<a class='btn btn-default' href='addproduct.php'  title='Add Product'>
				<span class='glyphicon glyphicon-plus'></span> <span class='hidden-xs'>Add Product</span></span></a>
			<?php
		}
	?>
	<?php
		if($user->hasPermission('barcode_m')) {
			?>
			<a class='btn btn-default' href='barcode-generator.php' title='Manage Barcode'>
				<span class='glyphicon glyphicon-barcode'></span> <span class='hidden-xs'>Manage Barcode</span></a>
			<?php
		}
	?>
	<?php
		if($user->hasPermission('alert_m')) {
			?>
			<a class='btn btn-default' href='addalert.php' title='Add Alerts'>
				<span class='glyphicon glyphicon-bell'></span> <span class='hidden-xs'>Add Alerts</span></a>
			<?php
		}
	?>
	<?php
		if($user->hasPermission('alert')) {
			?>
			<a class='btn btn-default' href='manage-alert.php' title='Manage Alerts'>
				<i class='fa fa-bell-slash-o'></i> <span class='hidden-xs'>Manage Alerts</span></a>
			<?php
		}
	?>
	<?php if($user->hasPermission('display_location_m')) { ?>
		<a class='btn btn-default' href='adddisplay.php' title='Add Display Location'>
			<span class='glyphicon glyphicon-plus'></span>
							<span class='hidden-xs'>
								Add Display Location
							</span>
		</a>
		<a class='btn btn-default' href="display.php" title='Manage Display Location'><i class='fa fa-location-arrow'></i> <span class='hidden-xs'>Manage Display Location</span> </a>
	<?php } ?>
	<?php
		if($user->hasPermission('item_m')) {
			?>
			<a class='btn btn-default' href='cbm_list.php'  title='CBM list'>
				<span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>CBM</span></span></a>
			<?php
		}
	?>
</div>
<div class='visible-xs'>
	<button id='btnShowNavigationContainer' class='btn btn-default'><i class='fa fa-bars'></i></button>
	<div class='card-nav card-nav-2' id='secondNavigationContainer' style='display:none;'>

		<button class='btn btn-default btn-sm' id='btnRemoveSecondNavigationContainer'><i class='fa fa-remove'></i></button>
		<?php
			if($user->hasPermission('item_m')) {
				?>
				<a class='btn btn-default btn-second-nav' href='addproduct.php'  title='Add Product'>
					<span class='glyphicon glyphicon-plus'></span> <span class='title'>Add Product</span></span></a>
				<?php
			}
		?>
		<?php
			if($user->hasPermission('barcode_m')) {
				?>
				<a class='btn btn-default btn-second-nav' href='barcode-generator.php' title='Manage Barcode'>
					<span class='glyphicon glyphicon-barcode'></span> <span class='title'>Manage Barcode</span></a>
				<?php
			}
		?>
		<?php
			if($user->hasPermission('alert_m')) {
				?>
				<a class='btn btn-default btn-second-nav' href='addalert.php' title='Add Alerts'>
					<span class='glyphicon glyphicon-bell'></span> <span class='title'>Add Alerts</span></a>
				<?php
			}
		?>
		<?php
			if($user->hasPermission('alert')) {
				?>
				<a class='btn btn-default btn-second-nav' href='manage-alert.php' title='Manage Alerts'>
					<i class='fa fa-bell-slash-o'></i> <span class='title'>Manage Alerts</span></a>
				<?php
			}
		?>
		<?php if($user->hasPermission('display_location_m')) { ?>
			<a class='btn btn-default btn-second-nav' href='adddisplay.php' title='Add Display Location'>
				<span class='glyphicon glyphicon-plus'></span>
							<span class='title'>
								Add Display Location
							</span>
			</a>
			<a class='btn btn-default btn-second-nav' href="display.php" title='Manage Display Location'><i class='fa fa-location-arrow'></i> <span class='title'>Display Location</span> </a>
		<?php } ?>
		</div>

</div>