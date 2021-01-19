<div style='margin:5px;' class="btn-group" role="group" aria-label="...">
	<?php if($user->hasPermission('sales')) {?>
		<a class='btn btn-default' href='sales.php' title='Sales'>
			<span class='glyphicon glyphicon-list'></span>
					<span class='hidden-xs'>
						Sales
					</span>
		</a>
	<?php } ?>
	<div class="btn-group" role="group">
		<div class="dropdown">
			<a class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
				Layouts
				<span class="caret"></span>
			</a>
			<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
				<?php if($user->hasPermission('invoice_layout')) {?>
				<li>
					<a  href='invoice_generator.php' title='Invoice Layout'>
					<?php echo INVOICE_LABEL; ?>
					</a>
				</li>
				<?php } ?>
				<?php if($user->hasPermission('dr_layout')) {?>
					<li>
					<a  href='dr_generator.php' title='Dr Layout'>
					<?php echo DR_LABEL; ?>
					</a>
					</li>
				<?php } ?>
				<?php if($user->hasPermission('pr_layout')) { ?>
					<li>
						<a href='ir_generator.php' title='Pr Layout'>
						<?php echo PR_LABEL; ?>
						</a>
					</li>
				<?php } ?>
				<?php if($user->hasPermission('sv_layout')) { ?>
					<li>
						<a  href='sv_generator.php' title='SV Layout'>
						SV
						</a>
					</li>
				<?php } ?>
				<?php if($user->hasPermission('pr_layout')) { ?>
					<li>
						<a  href='print_extra.php' title='Extra'>
							News print
						</a>
					</li>
				<?php } ?>
				<?php if(Configuration::thisCompany('aquabest')) { ?>
					<li>
					<a  href='supplier_form_generator.php' title='Form Generator'>
						Supplier Form
					</a>
					</li>
				<?php } ?>

			</ul>
		</div>
	</div>





	<?php if($user->hasPermission('sales_type')) {?>
		<a class='btn btn-default' href='sales-type.php' title='Sales type list'>
			<span class='glyphicon glyphicon-barcode'></span>
					<span class='hidden-xs'>
					Sales type list
					</span>
		</a>
	<?php } ?>

	<?php if($user->hasPermission('lock_doc_util') || $user->hasPermission('doc_util')) {?>
		<a class='btn btn-default' href='doc-utilities.php' title='Document Utilities'>
			<span class='glyphicon glyphicon-file'></span>
					<span class='hidden-xs'>
					Document Utilities
					</span>
		</a>
	<?php } ?>

	<?php if($user->hasPermission('lock_doc_util') || $user->hasPermission('doc_util')) {?>
		<a class='btn btn-default' href='doc-color.php' title='Document Colors'>
			<span class='glyphicon glyphicon-plus'></span>
					<span class='hidden-xs'>
					 Document colors
					</span>
		</a>
	<?php } ?>

	<?php if($user->hasPermission('serials')) { ?>
		<a class='btn btn-default' href='warranty.php' title='Warranty'>
			<span class='glyphicon glyphicon-wrench'></span>
					<span class='hidden-xs'>
			Warranty
			</span>
		</a>
	<?php } ?>

</div>