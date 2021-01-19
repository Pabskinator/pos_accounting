<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
	<?php if(true){ // change later
		?>
		<a class='btn btn-default btn_nav' data-con='1' title='Request' href='#' @click.prevent="showContainer(1)">
			<span class='glyphicon glyphicon-pencil'></span>
			<span class='hidden-xs'>Create Request</span>
		</a>
		<?php
	}?>
	<?php if(true){ // change later
		?>
		<a class='btn btn-default btn_nav' data-con='2' title='Pending' href='#' @click.prevent="showContainer(2)">
			<span class='glyphicon glyphicon-list'></span>
			<span class='hidden-xs'>Process Request</span>
		</a>
		<?php
	}?>
	<?php if(true){ // change later
		?>
		<a class='btn btn-default btn_nav' data-con='3' title='History' href='#' @click.prevent="showContainer(3)">
			<span class='glyphicon glyphicon-list-alt'></span>
			<span class='hidden-xs'>History</span>
		</a>
		<?php
	}?>
</div>