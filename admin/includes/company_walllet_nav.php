<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
	<?php if($user->hasPermission('wallet_manage')){ // change later
		?>
		<a class='btn btn-default btn_nav' data-con='1' title='Company E-Wallet' href='#' @click="showContainer(1)">
			<span class='glyphicon glyphicon-home'></span>
			<span class='hidden-xs'>Company E-Wallet</span>
		</a>
		<a class='btn btn-default btn_nav' data-con='2' title='User E-Wallet' href='#' @click="showContainer(2)">
			<span class='glyphicon glyphicon-user'></span>
			<span class='hidden-xs'>User E-Wallet</span>
		</a>
		<a class='btn btn-default btn_nav' data-con='3' title='Configurations' href='#' @click="showContainer(3)">
			<span class='glyphicon glyphicon-cog'></span>
			<span class='hidden-xs'>Configurations</span>
		</a>
		<?php
	}?>
</div>