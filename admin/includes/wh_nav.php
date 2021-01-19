<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
	<?php if($user->hasPermission('wh_request') || $user->data()->is_member == 1){ // change later
		?>
		<button class='btn btn-default' title='Request' v-bind:class="[nav.request ? nav_active : '']"  v-on:click="showRequestForm"> <span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>Request</span></button>
		<?php
	}?>
	<?php if($user->hasPermission('wh_approval') || $user->data()->is_member == 1){ // change later
		?>
		<button class='btn btn-default' title='For Approval' v-bind:class="[nav.approve ? nav_active : '']"  v-on:click="showApproval"> <span class='glyphicon glyphicon-ok'></span> <span class='hidden-xs'>For Approval</span> <span class='badge' v-cloak>{{pending_counts.for_approval}}</span></button>
		<?php
	}?>

	<?php if($user->hasPermission('wh_warehouse') || $user->data()->is_member == 1){ // change later
		?>
		<button class='btn btn-default' title='For Dr/Invoice' v-bind:class="[nav.warehouse ? nav_active : '']"  v-on:click="showApproved"> <span class='glyphicon glyphicon-home'></span> <span class='hidden-xs'>Warehouse</span>  <span class='badge' v-cloak>{{pending_counts.warehouse}}</span></button>
		<?php
	}?>
	<?php if($user->hasPermission('wh_shipping') || $user->data()->is_member == 1){ // change later
		?>
		<button class='btn btn-default' title='Shipping' v-bind:class="[nav.shipping ? nav_active : '']"  v-on:click="showShipping"> <span class='glyphicon glyphicon-share'></span> <span class='hidden-xs'><?php echo (Configuration::getValue('shipping_lbl')) ? Configuration::getValue('shipping_lbl') : 'Shipping';?></span> <span class='badge' v-cloak>{{pending_counts.shipping}}</span></button>
		<?php
	}?>

	<?php if($user->hasPermission('wh_log') || $user->data()->is_member == 1){ // change later
		?>
		<button class='btn btn-default' title='History' v-bind:class="[nav.del ? nav_active : '']"  v-on:click="showLog"> <span class='glyphicon glyphicon-share-alt'></span> <span class='hidden-xs'>Deliveries</span> <span class='badge' v-cloak>{{ countdel}}</span></button>
		<?php
	}?>
	<?php if($user->hasPermission('wh_log') || $user->data()->is_member == 1){ // change later
		?>
		<button class='btn btn-default' title='Pickup History' v-bind:class="[nav.pickup ? nav_active : '']"  v-on:click="showPickup"> <span class='glyphicon glyphicon-user'></span> <span class='hidden-xs'>Pickups</span> <span class='badge' v-cloak>{{countpickup}}</span></button>
		<?php
	}?>

	<?php
		if($user->hasPermission('wh_log') || $user->data()->is_member == 1){ // change later
	?>
		<button class='btn btn-default' title='Service History' v-bind:class="[nav.service ? nav_active : '']"  v-on:click="showService"> <span class='glyphicon glyphicon-list-alt'></span> <span class='hidden-xs'>Service</span> <span class='badge' v-cloak> {{countservice}} </span></button>
		<?php
	}?>
</div>