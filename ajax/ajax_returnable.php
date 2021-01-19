<?php
	include 'ajax_connection.php';


	$functionName = Input::get("functionName");

	if(function_exists($functionName)){
		$functionName();
	}

	function getRecord(){
		$returnable = new Returnable();

		$returnables = $returnable->getRecords();
		?>
		<table class="table table-bordered">
			<thead>
			<tr>
				<th>Item</th>
				<th>Item when return</th>
				<th>Created At</th>
			</tr>
			</thead>
			<tbody>
			<?php

				foreach($returnables as $ret){
					?>
					<tr>
						<td style='border-top:1px solid #ccc;'><?php echo $ret->item_code; ?></td>
						<td style='border-top:1px solid #ccc;'><?php echo $ret->ret_item_code; ?></td>
						<td style='border-top:1px solid #ccc;'><?php echo date('m/d/Y H:i:s A',$ret->created); ?></td>
					</tr>
					<?php
				}

			?>
			</tbody>
		</table>
		<?php
	}

	function addNew(){

		$item_id = Input::get('item_id');
		$ret_item_id = Input::get('ret_item_id');
		$add_inv = Input::get('add_inv');
		$returnable = new Returnable();

		$ret_item_id = ($ret_item_id) ? $ret_item_id : $item_id;

		$returnable->create(
			[
				'item_id' => $item_id,
				'ret_item_id' => $ret_item_id,
				'add_inv' => $add_inv,
				'created' => time(),
				'is_active' => 1,
			]
		);

		echo "Added successfully.";

	}

	function saveRefund(){

		$id = Input::get('id');
		$amount = Input::get('amount');

		$refund = new Refund();
		$refund->update(
			['amount' => $amount] , $id
		);
		echo "Updated successfully.";


	}
