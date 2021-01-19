<div class="btn-group hidden-xs" role="group" aria-label="..." style='margin-bottom:10px;'>
	<?php if(true){ // change later
		?>
		<a class='btn btn-default btn_nav' data-con='1' title='Report' href='#'> <span class='glyphicon glyphicon-ok'></span> <span class='hidden-xs'>Good Item </span></a>
		<?php
	}?>
	<?php if(true){ // change later
		?>
		<a class='btn btn-default btn_nav' data-con='2' title='Issues'  href='#'> <span class='glyphicon glyphicon-wrench'></span> <span class='hidden-xs'>Issues </span></a>
		<?php
	}?>
</div>
<div class='visible-xs'>
	<button id='btnShowNavigationContainer' class='btn btn-default'><i class='fa fa-bars'></i></button>
	<div class='card-nav card-nav-2' id='secondNavigationContainer' style='display:none;'>
		<button class='btn btn-default btn-sm' id='btnRemoveSecondNavigationContainer'><i class='fa fa-remove'></i></button>
		<?php if(true){ // change later
			?>
			<a class='btn btn-default btn_nav btn-second-nav' data-con='1' title='Report' href='#'> <span class='glyphicon glyphicon-ok'></span> <span class='title'>Good Item </span></a>
			<?php
		}?>
		<?php if(true){ // change later
			?>
			<a class='btn btn-default btn_nav btn-second-nav' data-con='2' title='Issues'  href='#'> <span class='glyphicon glyphicon-wrench'></span> <span class='title'>Issues </span></a>
			<?php
		}?>
	</div>
</div>