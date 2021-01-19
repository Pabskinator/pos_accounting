<div class="btn-group hidden-xs" role="group" aria-label="..." style='margin-bottom:10px;'>
	<?php if($user->hasPermission('ez_bills')) { ?>
		<a class='btn btn-default btn_nav' data-con='1' title='Request' href='#'>
			<span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>Request</span>
		</a>
	<?php } ?>
	<?php if($user->hasPermission('ez_bills')) { ?>
		<a class='btn btn-default btn_nav' data-con='5' title='My Request' href='#'>
			<span class='glyphicon glyphicon-user'></span> <span class='hidden-xs'>My Request</span>
		</a>
	<?php } ?>
	<?php if($user->hasPermission('ez_bills_process')) { ?>
		<a class='btn btn-default btn_nav' data-con='2' title='Pending' href='#'>
			<span class='glyphicon glyphicon-list-alt'></span> <span class='hidden-xs'>Process</span>
		</a>
	<?php } ?>
	<?php if($user->hasPermission('ez_bills_categ')) { ?>
		<a class='btn btn-default btn_nav' data-con='3' title='Biller Category' href='#'>
			<span class='fa fa-book'></span> <span class='hidden-xs'>Manage Biller Category</span>
		</a>
	<?php } ?>

	<?php if($user->hasPermission('ez_bills_company')) { ?>
		<a class='btn btn-default btn_nav' data-con='4' title='Biller Name' href='#'>
			<span class='fa fa-tag'></span> <span class='hidden-xs'>Manage Company</span>
		</a>
	<?php } ?>
</div>
<div class='visible-xs'>
	<button id='btnShowNavigationContainer' class='btn btn-default'><i class='fa fa-bars'></i></button>
	<div class='card-nav card-nav-2' id='secondNavigationContainer' style='display:none;'>
		<button class='btn btn-default btn-sm' id='btnRemoveSecondNavigationContainer'><i class='fa fa-remove'></i></button>
		<?php if($user->hasPermission('ez_bills')) { ?>
			<a class='btn btn-default btn_nav btn-second-nav' data-con='1' title='Request' href='#'>
				<span class='glyphicon glyphicon-list'></span> <span class='title'>Request</span>
			</a>
		<?php } ?>
		<?php if($user->hasPermission('ez_bills')) { ?>
			<a class='btn btn-default btn_nav btn-second-nav' data-con='5' title='My Request' href='#'>
				<span class='glyphicon glyphicon-user'></span> <span class='title'>My Request</span>
			</a>
		<?php } ?>
		<?php if($user->hasPermission('ez_bills_process')) { ?>
			<a class='btn btn-default btn_nav btn-second-nav' data-con='2' title='Pending' href='#'>
				<span class='glyphicon glyphicon-list-alt'></span> <span class='title'>Process</span>
			</a>
		<?php } ?>
		<?php if($user->hasPermission('ez_bills_categ')) { ?>
			<a class='btn btn-default btn_nav btn-second-nav' data-con='3' title='Biller Category' href='#'>
				<span class='fa fa-book'></span> <span class='title'>Manage Biller Category</span>
			</a>
		<?php } ?>

		<?php if($user->hasPermission('ez_bills_company')) { ?>
			<a class='btn btn-default btn_nav btn-second-nav' data-con='4' title='Biller Name' href='#'>
				<span class='fa fa-tag'></span> <span class='title'>Manage Company</span>
			</a>
		<?php } ?>
	</div>
</div>