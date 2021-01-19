<div style='margin:5px;' class="btn-group hidden-xs" role="group" aria-label="...">
	<?php if($user->hasPermission('spare_part')) { // edit auth here ?>
		<a class='btn btn-default' href='spare-parts.php' title='<?php echo Configuration::getValue('spare_part')?>'>
			<i class='fa fa-list'></i> <span class='hidden-xs'><?php echo  Configuration::getValue('spare_part')?></span>
		</a>
	<?php } ?>
	<?php if($user->hasPermission('spare_part_add')) { // edit auth here ?>
		<a class='btn btn-default' href='add-composite-item.php' title='Add Spare Part'>
			<i class='fa fa-plus'></i> <span class='hidden-xs'>Add Item</span>
		</a>
	<?php } ?>
	<?php if($user->hasPermission('spare_part_sim')) { // edit auth here ?>
		<a class='btn btn-default' href='assembly_item_for_orders.php' title='Item for Orders'>
			<i class='fa fa-list-alt'></i> <span class='hidden-xs'>Item for Orders</span>
		</a>
	<?php } ?>
	<?php if($user->hasPermission('spare_part_a')) { // edit auth here ?>
		<a class='btn btn-default' href='assemble-composite-item.php' title='Assemble'>
			<i class='fa fa-wrench'></i> <span class='hidden-xs'><?php echo  Configuration::getValue('assemble')?></span>
		</a>
	<?php } ?>
	<?php if($user->hasPermission('spare_part_a')) { // edit auth here ?>
		<a class='btn btn-default' href='assemble-list.php' title='List'>
			<i class='fa fa-list'></i> <span class='hidden-xs'><?php echo  Configuration::getValue('assemble')?> List</span>
		</a>
	<?php } ?>
	<?php if($user->hasPermission('sp_forecast')) { // edit auth here ?>
		<a class='btn btn-default' href='raw_mats.php' title='Forecast'>
			<i class='fa fa-calendar'></i> <span class='hidden-xs'><?php echo  Configuration::getValue('assemble')?> Forecast</span>
		</a>
	<?php } ?>
	<?php if($user->hasPermission('assemble_items')) { // edit auth here ?>
		<a class='btn btn-default' href='assemble_items.php' title='History'>
			<i class='fa fa-list-alt'></i> <span class='hidden-xs'><?php echo  Configuration::getValue('assemble')?> History</span>
		</a>
	<?php } ?>
	<?php if(Configuration::getValue('disassemble_view') == 1 || !Configuration::getValue('disassemble_view')){
		?>
		<?php if($user->hasPermission('spare_part_d')) { // edit auth here ?>
			<a class='btn btn-default' href='disassemble-composite-item.php' title='Assemble'>
				<i class='fa fa-chain-broken'></i> <span class='hidden-xs'><?php echo  Configuration::getValue('disassemble')?></span>
			</a>
		<?php } ?>
		<?php if($user->hasPermission('spare_part_d')) { // edit auth here ?>
			<a class='btn btn-default' href='disassemble-list.php' title='Disassemble'>
				<i class='fa fa-list'></i> <span class='hidden-xs'><?php echo  Configuration::getValue('disassemble')?> list</span>
			</a>
		<?php } ?>
		<?php if($user->hasPermission('borrow_part')) { // edit auth here ?>
			<a class='btn btn-default' href='borrowed_parts.php' title='Disassemble'>
				<i class='fa fa-cog'></i> <span class='hidden-xs'>Borrow Parts</span>
			</a>
		<?php } ?>
	<?php
	}?>

	<?php if($user->hasPermission('spare_part')) { // edit auth here ?>
		<a class='btn btn-default' href='spare-type.php' title='Type'>
			<i class='fa fa-list'></i> <span class='hidden-xs'>Type</span>
		</a>
	<?php } ?>
</div>
<div class='visible-xs'>
	<button id='btnShowNavigationContainer' class='btn btn-default'><i class='fa fa-bars'></i></button>
	<div class='card-nav card-nav-2' id='secondNavigationContainer' style='display:none;'>
		<button class='btn btn-default btn-sm' id='btnRemoveSecondNavigationContainer'><i class='fa fa-remove'></i></button>
		<?php if($user->hasPermission('spare_part')) { // edit auth here ?>
			<a class='btn btn-default btn-second-nav' href='spare-parts.php' title='<?php echo Configuration::getValue('spare_part')?>'>
				<i class='fa fa-list'></i> <span class='title'><?php echo  Configuration::getValue('spare_part')?></span>
			</a>
		<?php } ?>
		<?php if($user->hasPermission('spare_part_add')) { // edit auth here ?>
			<a class='btn btn-default btn-second-nav' href='add-composite-item.php' title='Add Spare Part'>
				<i class='fa fa-plus'></i> <span class='title'>Add Item</span>
			</a>
		<?php } ?>
		<?php if($user->hasPermission('spare_part_sim')) { // edit auth here ?>
			<a class='btn btn-default  btn-second-nav' href='assembly_item_for_orders.php' title='Item for Orders'>
				<i class='fa fa-list-alt'></i> <span class='title'>Item for Orders</span>
			</a>
		<?php } ?>
		<?php if($user->hasPermission('spare_part_a')) { // edit auth here ?>
			<a class='btn btn-default btn-second-nav' href='assemble-composite-item.php' title='Assemble'>
				<i class='fa fa-wrench'></i> <span class='title'><?php echo  Configuration::getValue('assemble')?></span>
			</a>
		<?php } ?>
		<?php if($user->hasPermission('spare_part_a')) { // edit auth here ?>
			<a class='btn btn-default btn-second-nav' href='assemble-list.php' title='List'>
				<i class='fa fa-list'></i> <span class='title'><?php echo  Configuration::getValue('assemble')?> List</span>
			</a>
		<?php } ?>
		<?php if(Configuration::getValue('disassemble_view') == 1 || !Configuration::getValue('disassemble_view')){
			?>
			<?php if($user->hasPermission('spare_part_d')) { // edit auth here ?>
				<a class='btn btn-default btn-second-nav' href='disassemble-composite-item.php' title='Assemble'>
					<i class='fa fa-chain-broken'></i> <span class='title'><?php echo  Configuration::getValue('disassemble')?> Item</span>
				</a>
			<?php } ?>
			<?php if($user->hasPermission('spare_part_d')) { // edit auth here ?>
				<a class='btn btn-default btn-second-nav' href='disassemble-list.php' title='Disassemble'>
					<i class='fa fa-list'></i> <span class='title'><?php echo  Configuration::getValue('disassemble')?> list</span>
				</a>
			<?php } ?>
			<?php
		}?>
		<?php if($user->hasPermission('spare_part')) { // edit auth here ?>
			<a class='btn btn-default btn-second-nav' href='spare-type.php' title='Type'>
				<i class='fa fa-list'></i> <span class='title'>Type</span>
			</a>
		<?php } ?>
	</div>
</div>