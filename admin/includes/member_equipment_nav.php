<div class="btn-group hidden-xs" role="group" aria-label="..." style='margin-bottom:10px;'>
	<?php
		if(true) {
			?>
			<a class='btn btn-default' href='member_equipment.php'  title='Member Equipment'>
				<span class='glyphicon glyphicon-book'></span> <span class='hidden-xs'>Member Equipment</span></span></a>
			<?php
		}
	?>
	<?php
		if(true) {
			?>
			<a class='btn btn-default' href='member_equipment_request.php'  title='Request'>
				<span class='glyphicon glyphicon-pencil'></span> <span class='hidden-xs'>Request</span>
				</span>
			</a>
			<?php
		}
	?>
	<?php
		if(true) {
			?>
			<a class='btn btn-default' href='member_equipment_log.php'  title='Log'>
				<span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>Log</span></span></a>
			<?php
		}
	?>
	<?php
		if(true) {
			?>
			<a class='btn btn-default' href='returnables.php'  title='Log'>
				<span class='glyphicon glyphicon-refresh'></span> <span class='hidden-xs'>Returnables</span></span></a>
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
				<a class='btn btn-default btn-second-nav' href='member_equipment_request.php'  title='Request'>
					<span class='glyphicon glyphicon-pencil'></span> <span class='title'>Request</span></span></a>
				<?php
			}
		?>
		<?php
			if(true) {
				?>
				<a class='btn btn-default btn-second-nav' href='member_equipment.php'  title='Member Request'>
					<span class='glyphicon glyphicon-book'></span> <span class='title'>Member Request</span></span></a>
				<?php
			}
		?>
		<?php
			if(true) {
				?>
				<a class='btn btn-default btn-second-nav' href='member_equipment_log.php'  title='Log'>
					<span class='glyphicon glyphicon-list'></span> <span class='title'>Log</span></span></a>
				<?php
			}
		?>
		<?php
			if(true) {
				?>
				<a class='btn btn-default btn-second-nav' href='returnables.php'  title='Log'>
					<span class='glyphicon glyphicon-refresh'></span> <span class='title'>Returnables</span></span></a>
				<?php
			}
		?>
	</div>
</div>