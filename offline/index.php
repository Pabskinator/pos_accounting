<?php
	include "includes/page_head.php";

	?>

	<div class="row" id='app'>
		<div class="col-md-3" v-for="nav in navs">
			<div class="panel panel-default">
				<div class="panel-body">
					<h3 class='text-center'>{{ nav.label }} </h3>
				</div>
			</div>
		</div>
	</div>

	<script>
		var vm = new Vue({
				el:'#app',
				data: {
						navs: [
							{label:'POS', page:'pos.php'},
							{label:'Order Item', page:'order_item.php'},
							{label:'Add Item', page:'add_item.php'},
							{label:'Item Service', page:'item_service.php'},
							{label:'Item Service', page:'item_service.php'}
						]
				}
			});

	</script>
<?php
	include "includes/page_tail.php";


