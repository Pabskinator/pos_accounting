<div class="btn-group hidden-xs" role="group" aria-label="..." style='margin-bottom:10px;'>
	<?php
		if(true) {
			?>
			<a class='btn btn-default' href='inventory_issues.php'  title='Item issues'>
				<span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>Item issues</span></span></a>
			<?php
		}
	?>
	<?php
		if(true) {
			?>
			<a class='btn btn-default' href='issues_log.php'  title='Issues Log'>
				<span class='glyphicon glyphicon-book'></span> <span class='hidden-xs'>Issues Log</span></span></a>
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
				<a class='btn btn-default btn-second-nav' href='inventory_issues.php'  title='Item issues'>
					<span class='glyphicon glyphicon-list'></span> <span class='title'>Item issues</span></span></a>
				<?php
			}
		?>
		<?php
			if(true) {
				?>
				<a class='btn btn-default btn-second-nav' href='issues_log.php'  title='Issues Log'>
					<span class='glyphicon glyphicon-book'></span> <span class='title'>Issues Log</span></span></a>
				<?php
			}
		?>
	</div>
</div>