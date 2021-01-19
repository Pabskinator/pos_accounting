<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
	<?php if(!$user->hasPermission('wh_agent')){ // change later
		?>
		<a class='btn btn-default btn_nav' data-con='3' title='Collection'  href='#'> <span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>Collection report</span></a>
		<?php
	}?>
	<?php if(true){ // change later
		?>
		<a class='btn btn-default' data-con='1' title='AR' href='ar.php'> <span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>AR </span></a>
		<?php
	}?>
	<?php if(true){ // change later
		?>
		<a class='btn btn-default btn_nav' data-con='2' title='SOA'  href='#'> <span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>SOA</span></a>
		<?php
	}?>

	<?php if(!$user->hasPermission('wh_agent')){ // change later
		?>
		<a class='btn btn-default btn_nav' data-con='4' title='DSS'  href='#'> <span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>Series Summary</span></a>
		<?php
	}?>

	<?php if(!$user->hasPermission('wh_agent')){ // change later
		?>
		<a class='btn btn-default btn_nav' data-con='5' title='Sales type summary'  href='#'> <span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>Sales Type Summary</span></a>
		<?php
	}?>

	<?php if(!$user->hasPermission('wh_agent')){ // change later
		?>
		<a class='btn btn-default btn_nav' data-con='6' title='Yearly summary'  href='#'> <span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>Yearly Summary</span></a>
		<?php
	}?>
	<?php if(!$user->hasPermission('wh_agent')){ // change later
		?>
		<a class='btn btn-default btn_nav' data-con='7' title='Top Clients'  href='#'> <span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>Top Client</span></a>
		<?php
	}?>
	<?php if(!$user->hasPermission('wh_agent')){ // change later
		?>
		<a class='btn btn-default' href='report-member.php' > <span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>Client Summary</span></a>
		<?php
	}?>
	<?php if(true){ // change later
		?>
		<a class='btn btn-default btn_nav' data-con='8' title='Area summary'  href='#'> <span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>Area Summary</span></a>
		<?php
	}?>
</div>