<div class="btn-group hidden-xs" role="group" aria-label="..." style='margin-bottom:10px;'>
	<a class='btn btn-default' href='station.php' title='Station'> <span class='glyphicon glyphicon-map-marker'></span> <span class='hidden-xs'>Manage Station</span> </a>
	<?php if($user->hasPermission('station_m')) { ?>
		<a class='btn btn-default' href='addstation.php' title='Add <?php echo $label_name; ?>'> <span class='glyphicon glyphicon-plus'></span> <span class='hidden-xs'>Add <?php echo $label_name; ?></span> </a>
	<?php } ?>
	<?php if($user->hasPermission('package')) { ?>
		<a class='btn btn-default' href='package.php' title='Package'> <span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>Package</span> </a>
	<?php } ?>
	<?php if($user->hasPermission('brand')) { ?>
		<a class='btn btn-default' href='brand.php' title='Brand'> <span class='glyphicon glyphicon-tag'></span> <span class='hidden-xs'>Brand</span> </a>
	<?php } ?>
</div>
<div class='visible-xs'>
	<button id='btnShowNavigationContainer' class='btn btn-default'><i class='fa fa-bars'></i></button>
	<div class='card-nav card-nav-2' id='secondNavigationContainer' style='display:none;'>
		<button class='btn btn-default btn-sm' id='btnRemoveSecondNavigationContainer'><i class='fa fa-remove'></i></button>
		<a class='btn btn-default btn-second-nav' href='station.php' title='Station'> <span class='glyphicon glyphicon-map-marker'></span> <span class='title'>Manage Station</span> </a>
		<?php if($user->hasPermission('station_m')) { ?>
			<a class='btn btn-default btn-second-nav' href='addstation.php' title='Add <?php echo $label_name; ?>'> <span class='glyphicon glyphicon-plus'></span> <span class='title'>Add <?php echo $label_name; ?></span> </a>
		<?php } ?>
		<?php if($user->hasPermission('package')) { ?>
			<a class='btn btn-default btn-second-nav' href='package.php' title='Package'> <span class='glyphicon glyphicon-list'></span> <span class='title'>Package</span> </a>
		<?php } ?>
		<?php if($user->hasPermission('brand')) { ?>
			<a class='btn btn-default btn-second-nav' href='brand.php' title='Brand'> <span class='glyphicon glyphicon-tag'></span> <span class='title'>Brand</span> </a>
		<?php } ?>
	</div>
</div>