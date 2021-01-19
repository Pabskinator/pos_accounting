<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
	<?php if($user->hasPermission('p_point')){ // change later
		?>
		<a class='btn btn-default btn_nav' data-con='1' title='Points' href='#'>
			<span class='glyphicon glyphicon-home'></span>
			<span class='hidden-xs'>My Points</span>
		</a>
		<?php
	}?>
	<?php if($user->hasPermission('p_point_sell')){ // change later
		?>
		<a class='btn btn-default btn_nav' data-con='2' title='Points Log'  href='#'>
			<span class='fa fa-money'></span> <span class='hidden-xs'>Sell/Buy Points</span>
		</a>
		<?php
	}?>
	<?php if($user->hasPermission('p_point_transfer')){ // change later
		?>
		<a class='btn btn-default btn_nav' data-con='3' title='User Points'  href='#'>
			<span class='fa fa-share'></span> <span class='hidden-xs'>Transfer</span>
		</a>
		<?php
	}?>
	<?php if(true){ // change later
		?>
		<a class='btn btn-default btn_nav' data-con='4' title='User Points Log'  href='#'>
			<span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>User Points Log</span>
		</a>
		<?php
	}?>
</div>