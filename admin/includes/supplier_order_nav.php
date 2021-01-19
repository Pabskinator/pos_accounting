<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>

	<?php if($user->hasPermission('supplier_ol')){ ?>
	<a class='btn btn-default btn-sm' title='Pending' href='#'   v-bind:class="[nav.pending ? nav_active : '']"  @click.prevent="showView(1)"> <span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>Pending <span > ({{order_counts[0]}}) </span></a>
	<?php } ?>
	<?php if($user->hasPermission('supplier_ol')) { ?>
		<a class='btn btn-default btn-sm' title='Processed' href='#' v-bind:class="[nav.process ? nav_active : '']"  @click.prevent="showView(3)"> <span class='glyphicon glyphicon-list-alt'></span> <span class='hidden-xs'>Approved <span>({{order_counts[2]}})</span> </span></a>
	<?php } ?>
	<?php if($user->hasPermission('supplier_ol')) { ?>
	<a class='btn btn-default btn-sm' title='Approved' href='#' v-bind:class="[nav.approve ? nav_active : '']"  @click.prevent="showView(2)"> <span class='glyphicon glyphicon-ok'></span> <span class='hidden-xs'>Receiving ({{order_counts[1]}}) </span></a>
	<?php } ?>
	<?php if($user->hasPermission('supplier_ol')) { ?>
		<a class='btn btn-default btn-sm' title='Received' href='#' v-bind:class="[nav.received ? nav_active : '']"  @click.prevent="showView(4)"> <span class='glyphicon glyphicon-home'></span> <span class='hidden-xs'>Received ({{order_counts[4]}}) </span></a>
	<?php } ?>


</div>
<div class="btn-group pull-right" role="group" aria-label="..." style='margin-bottom:10px;'>

	<?php if($user->hasPermission('supplier_ol')) { ?>
	<a class='btn btn-default btn-sm' title='Returned' href='#' v-bind:class="[nav.return ? nav_active : '']"  @click.prevent="showView(-1)"> <span class='glyphicon glyphicon-repeat'></span> <span class='hidden-xs'>Returned ({{order_counts[-1]}}) </span></a>
	<?php } ?>
	<?php if($user->hasPermission('supplier_ol')) { ?>
	<a class='btn btn-default btn-sm' title='Declined' href='#' v-bind:class="[nav.decline ? nav_active : '']"  @click.prevent="showView(99)"> <span class='glyphicon glyphicon-remove'></span> <span class='hidden-xs'>Declined ({{order_counts[99]}}) </span></a>
	<?php } ?>

</div>
