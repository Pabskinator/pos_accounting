<div style='margin:5px;' class="btn-group hidden-xs" role="group" aria-label="...">
	<?php if($user->hasPermission('supplier')) { // edit auth here ?>
		<a class='btn btn-default' href='supplier.php' title='Supplier'>
			<i class='fa fa-truck'></i> <span class='hidden-xs'>Supplier</span>
		</a>
	<?php } ?>
	<?php if($user->hasPermission('supplier_m')) { ?>
		<a class='btn btn-default' href='addsupplier.php' title='Add Supplier'>
			<i class='fa fa-plus'></i> <span class='hidden-xs'>Add Supplier</span>
		</a>
	<?php } ?>
	<?php if($user->hasPermission('supplier_ol')) { ?>
		<a class='btn btn-default'  href='supplier_receive_order.php' title='Order List'>
			<i class='fa fa-shopping-cart'></i> <span class='hidden-xs'>Order List</span>
		</a>
	<?php } ?>
	<?php if($user->hasPermission('supplier_o')) { ?>
		<a class='btn btn-default'  href='supplier_order.php' title='Order item'>
			<i class='fa fa-pencil'></i> <span class='hidden-xs'>Order item</span>
		</a>
	<?php } ?>
	<?php if($user->hasPermission('supplier_si')) { ?>
		<a class='btn btn-default'  href='supplier_item.php' title='Supplier Item'>
			<i class='fa fa-barcode'></i> <span class='hidden-xs'>Supplier Item</span>
		</a>
	<?php } ?>
	<?php if($user->hasPermission('supplier_sim')) { ?>
		<a class='btn btn-default'  href='supplier_item_add.php' title='Add Supplier Item'>
			<i class='fa fa-check'></i> <span class='hidden-xs'>Add Supplier Item</span>
		</a>
	<?php } ?>
</div>
<div class='visible-xs'>
	<button id='btnShowNavigationContainer' class='btn btn-default'><i class='fa fa-bars'></i></button>
	<div class='card-nav card-nav-2' id='secondNavigationContainer' style='display:none;'>
		<button class='btn btn-default btn-sm ' id='btnRemoveSecondNavigationContainer'><i class='fa fa-remove'></i></button>
		<?php if($user->hasPermission('supplier')) { // edit auth here ?>
			<a class='btn btn-default btn-second-nav' href='supplier.php' title='Supplier'>
				<i class='fa fa-truck'></i> <span class='title'>Supplier</span>
			</a>
		<?php } ?>
		<?php if($user->hasPermission('supplier_m')) { ?>
			<a class='btn btn-default btn-second-nav' href='addsupplier.php' title='Add Supplier'>
				<i class='fa fa-plus'></i> <span class='title'>Add Supplier</span>
			</a>
		<?php } ?>
		<?php if($user->hasPermission('supplier_ol')) { ?>
			<a class='btn btn-default btn-second-nav'  href='supplier_receive_order.php' title='Order List'>
				<i class='fa fa-shopping-cart'></i> <span class='title'>Order List</span>
			</a>
		<?php } ?>
		<?php if($user->hasPermission('supplier_o')) { ?>
			<a class='btn btn-default btn-second-nav'  href='supplier_order.php' title='Order item'>
				<i class='fa fa-pencil'></i> <span class='title'>Order item</span>
			</a>
		<?php } ?>
		<?php if($user->hasPermission('supplier_si')) { ?>
			<a class='btn btn-default btn-second-nav'  href='supplier_item.php' title='Supplier Item'>
				<i class='fa fa-barcode'></i> <span class='title'>Supplier Item</span>
			</a>
		<?php } ?>
		<?php if($user->hasPermission('supplier_sim')) { ?>
			<a class='btn btn-default btn-second-nav'  href='supplier_item_add.php' title='Add Supplier Item'>
				<i class='fa fa-check'></i> <span class='title'>Add Supplier Item</span>
			</a>
		<?php } ?>
	</div>
</div>