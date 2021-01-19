<div class="btn-group hidden-xs" role="group" aria-label="..." '>
	<?php if($user->hasPermission('pettycash')){ // change later
		?>
		<a class='btn btn-default' title='Request' href='pettycash.php'> <span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>Petty Cash</span></a>
		<?php
	}?>
	<?php if($user->hasPermission('pettycash_r') || $user->hasPermission('pettycash_m')){ // change later
		?>
		<a class='btn btn-default' title='For approval'  href='pettycash_approval.php'> <span class='glyphicon glyphicon-list-alt'></span> <span class='hidden-xs'>Request Monitoring</span></a>
		<?php
	}?>
	<?php if($user->hasPermission('pettycash_l')){ // change later
		?>
		<a class='btn btn-default' title='Petty cash monitoring'  href='pettycash_log.php'> <span class='glyphicon glyphicon-book'></span> <span class='hidden-xs'>Petty cash log</span></a>
		<?php
	}?>
	<?php if($user->hasPermission('acc_v')){ // change later
		?>
		<a class='btn btn-default' title='Account titles'  href='account-titles.php'> <span class='glyphicon glyphicon-tag'></span> <span class='hidden-xs'>Account title</span></a>
		<?php
	}?>
</div>
<div class='visible-xs'>
	<button id='btnShowNavigationContainer' class='btn btn-default'><i class='fa fa-bars'></i></button>
	<div class='card-nav card-nav-2' id='secondNavigationContainer' style='display:none;'>
		<button class='btn btn-default btn-sm' id='btnRemoveSecondNavigationContainer'><i class='fa fa-remove'></i></button>

		<?php if($user->hasPermission('pettycash')){ // change later
			?>
			<a class='btn btn-default btn-second-nav' title='Request' href='pettycash.php'> <span class='glyphicon glyphicon-list'></span> <span class='title'>Petty Cash</span></a>
			<?php
		}?>
		<?php if($user->hasPermission('pettycash_r') || $user->hasPermission('pettycash_m')){ // change later
			?>
			<a class='btn btn-default btn-second-nav' title='For approval'  href='pettycash_approval.php'> <span class='glyphicon glyphicon-list-alt'></span> <span class='title'>Request Monitoring</span></a>
			<?php
		}?>
		<?php if($user->hasPermission('pettycash_l')){ // change later
			?>
			<a class='btn btn-default btn-second-nav' title='Petty cash monitoring'  href='pettycash_log.php'> <span class='glyphicon glyphicon-book'></span> <span class='title'>Petty cash log</span></a>
			<?php
		}?>
		<?php if($user->hasPermission('acc_v')){ // change later
			?>
			<a class='btn btn-default btn-second-nav' title='Account titles'  href='account-titles.php'> <span class='glyphicon glyphicon-tag'></span> <span class='title'>Account title</span></a>
			<?php
		}?>
	</div>
</div>