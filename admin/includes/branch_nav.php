<div class="btn-group hidden-xs" role="group" aria-label="..." style='margin-bottom:10px;'>
	<a class='btn btn-default' href='branch.php' title='Branch'>
		<span class='glyphicon glyphicon-list-alt'></span>
		<span class='hidden-xs'>Branch</span>
	</a>
	<a class='btn btn-default' href='addbranch.php' title='Add Branch'>
		<span class='glyphicon glyphicon-plus'></span>
		<span class='hidden-xs'>Add Branch</span>
	</a>
<?php if(Configuration::getValue('branch_tag') == 1){?>
	<a class='btn btn-default' href='branch-tag.php' title='Branch Tags'>
		<span class='glyphicon glyphicon-list'></span>
		<span class='hidden-xs'>Branch Tags</span>
	</a>
	<a class='btn btn-default' href='addbranchtags.php' title='Branch Tags'>
		<span class='glyphicon glyphicon-plus'></span>
		<span class='hidden-xs'>Add Branch Tags</span>
	</a>
<?php } ?>
</div>
<div class='visible-xs'>
	<button id='btnShowNavigationContainer' class='btn btn-default'><i class='fa fa-bars'></i></button>
	<div class='card-nav card-nav-2' id='secondNavigationContainer' style='display:none;'>
		<button class='btn btn-default btn-sm' id='btnRemoveSecondNavigationContainer'><i class='fa fa-remove'></i></button>
		<a class='btn btn-default btn-second-nav' href='branch.php' title='Branch'>
			<span class='glyphicon glyphicon-list-alt'></span>
			<span class='title'>Branch</span>
		</a>
		<a class='btn btn-default btn-second-nav' href='addbranch.php' title='Add Branch'>
			<span class='glyphicon glyphicon-plus'></span>
			<span class='title'>Add Branch</span>
		</a>
		<?php if(Configuration::getValue('branch_tag') == 1){?>
			<a class='btn btn-default btn-second-nav' href='branch-tag.php' title='Branch Tags'>
				<span class='glyphicon glyphicon-list'></span>
				<span class='title'>Branch Tags</span>
			</a>
			<a class='btn btn-default btn-second-nav' href='addbranchtags.php' title='Branch Tags'>
				<span class='glyphicon glyphicon-plus'></span>
				<span class='title'>Add Branch Tags</span>
			</a>
		<?php } ?>
	</div>
</div>