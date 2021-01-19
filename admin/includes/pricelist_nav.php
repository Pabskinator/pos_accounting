<div class="btn-group hidden-xs" role="group" aria-label="..." style='margin-bottom:10px;'>
	<a class='btn btn-default' href='item-price-adjustment.php' title='Price list'> <span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>Price list</span> </a>
	<?php if($user->hasPermission('item_adj_m')) { ?>
	<a class='btn btn-default' href='add-item-price-adjustment.php' title='Add price adjustment'> <span class='glyphicon glyphicon-plus'></span> <span class='hidden-xs'>Add Price adjustment</span> </a>

	<a class='btn btn-default' href='item-price-adjustment-log.php' title='Log'> <span class='glyphicon glyphicon-list-alt'></span> <span class='hidden-xs'>log</span> </a>
	<?php } ?>
</div>
<div class='visible-xs'>
	<button id='btnShowNavigationContainer' class='btn btn-default'><i class='fa fa-bars'></i></button>
	<div class='card-nav card-nav-2' id='secondNavigationContainer' style='display:none;'>
		<button class='btn btn-default btn-sm' id='btnRemoveSecondNavigationContainer'><i class='fa fa-remove'></i></button>
		<a class='btn btn-default btn-second-nav' href='item-price-adjustment.php' title='Price list'> <span class='glyphicon glyphicon-list'></span> <span class='title'>Price list</span> </a>
		<?php if($user->hasPermission('item_adj_m')) { ?>
			<a class='btn btn-default btn-second-nav' href='add-item-price-adjustment.php' title='Add price adjustment'> <span class='glyphicon glyphicon-plus'></span> <span class='title'>Add Price adjustment</span> </a>
		<?php } ?>
		<a class='btn btn-default btn-second-nav' href='item-price-adjustment-log.php' title='Log'> <span class='glyphicon glyphicon-list-alt'></span> <span class='title'>log</span> </a>

	</div>
</div>