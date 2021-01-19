<?php
	include 'ajax_connection.php';

	$functionName = Input::get('functionName');
	$functionName();


	function getOrderDetails() {
		$user = new User();
		$order_id = Input::get('id');
		$bid = Input::get('branch_id');
		$od = new OrderDetails();
		$details = $od->get_active('order_details', array('order_id', "=", $order_id));
		$order = new Order($order_id);
		?>
		<div id="no-more-tables">
		<table class="table">
			<thead>
			<tr>
				<th>Barcode</th>
				<th>Item Code</th>
				<th>Price</th>
				<th>Quantity</th>
				<th>Total</th>
			</tr>
			</thead>
			<?php
				$total = 0;
				$process = true;
				$topos = "";
				$toposmember = "";
				$toposstation = "";
				$topossalestype = "";
				foreach($details as $index => $d) {

					$inventory = new Inventory();
					$rack = new Rack();

					$item = new Product($d->item_id);
					$rack_id = $rack->getRackForSelling($user->data()->branch_id);
					$curinventory = $inventory->getQty($item->data()->id,$order->data()->branch_id,$rack_id->id);

					$price = $item->getPrice($item->data()->id);
					if($item->data()->item_type == 2 ||$item->data()->item_type == 3 || $item->data()->item_type == 4 || $item->data()->item_type == 5 ){
						$con = new Consumable();
						$pcon = $con->getConsumableByItemId($item->data()->id);
						$days = $pcon->days;
						$cqty = $pcon->qty;
					} else {
						$days = -1;
						$cqty = -1;

					}
					$topos .= "<tr data-itemcode='".$item->data()->item_code."' data-order_id='$order_id' id='".$item->data()->id."' c-qty='$cqty' c-days='$days' data-barcode='".$item->data()->barcode."'> <td><input readonly type='text' class='form-control circletextbox cartqty' value='".$d->qty."'></td>	<td>".$item->data()->item_code."<br><small class='text-danger'>".$item->data()->description."</small></td><td id='".$price->id."'>".$price->price."</td><td><input type='text' class='form-control circletextbox cartdiscount' value='0' disabled></td><td>".$d->qty*$price->price."</td><td></td></tr>";

					$notyourbranch = '';
					if($bid != $order->data()->branch_id){
						$notyourbranch = '<span class="label label-default">Unable to process. This order does not belong to your branch.</span>';
					}
					?>
					<tr>
						<td data-title='Barcode'><?php echo $item->data()->barcode; ?></td>
						<td data-title='Item'><?php echo $item->data()->item_code; ?> <br> <small class='text-danger'><?php echo $item->data()->description; ?></small></td>
						<td data-title='Price'><?php echo $price->price ?></td>
						<td data-title='Qty'>
							<?php
								if($order->data()->status ==1){
									$textwarning="";
									if($curinventory){
										$curinv = $curinventory->qty;
									} else {
										$curinv = 0;
									}
									if ($d->qty > $curinv){
										$process = false;
										$textwarning="text-danger";
									}
									echo "<span class='$textwarning'>Order Quantity:<strong > ". $d->qty . "</strong>  <br/>Stock:<strong>   ". $curinv."</strong></span>";
								} else if($order->data()->status ==2){
									echo "Order Quantity:<strong > ". $d->qty . "</strong>";
								}
							?></td>
						<td data-title='Total'>
							<?php
								echo number_format($d->qty * $price->price,2);
								$total += $d->qty * $price->price;
							?>
						</td>
					</tr>
				<?php
				}

				$toposmember = $order->data()->member_id;
				$topossalestype =$order->data()->sales_type;
				$toposstation = $order->data()->station_id;
			?>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td><span class='label label-default' style='font-size:1.1em;'>Total: <?php echo number_format($total,2); ?></span></td>
			</tr>
		</table>
		</div>
		<br>
		<div class='text-right'>
			<?php
				if($order->data()->status == 1) {
					if($user->hasPermission('order')) {
						if(!$notyourbranch){
						if($process) {
							?>
							<input  style='display:none;' type="button" class='btn btn-success' id='processOrder' data-order_id="<?php echo $order_id; ?>" value="Process Order" />
						<?php
						} else {
							?>
							<span  style='display:none;' class='label label-default'>Unable to process. Not Enough Stock</span>
						<?php
						}
						?>
						<input type="button" class='btn btn-danger' id='declineorder' data-order_id="<?php echo $order_id; ?>" value="Decline Order" />
					<?php
					} else {
						echo $notyourbranch ;
					}
					}
					?>
				<?php
				} else if ($order->data()->status == 2){

				}
			?>
		</div>
		<script>


			$('body').on('click','#declineorder',function(){
				if(confirm("Are you sure you want to decline this order?")){
					var id = $(this).attr('data-order_id');
					$.ajax({
						url: '../ajax/ajax_changestatus.php',
						type:'post',
						data:{class:'Order',status:'-1',id:id},
						success: function(data){
							location.reload();
						}
					});
				}

			});
		</script>
	<?php
	}
?>