<?php
	include 'ajax_connection.php';

	$functionName = Input::get("functionName");
	if(function_exists($functionName)) $functionName();

	function getDetails(){
		$id = Input::get('id');
		$member_equipment = new Member_equipment();

		$data = $member_equipment->requestData($id);

		$list = $member_equipment->getRequestDetails($id);
		if($list){
			?>
				<table class="table" id="tblSales">
					<thead>
					<tr>
						<th>Item</th>
						<th>Qty</th>
						<th>Return Qty</th>

					</tr>
					</thead>
					<tbody>
						<?php
							foreach($list as $item){
								?>
								<tr>
									<td>
										<?php echo $item->item_code; ?>
										<span class='span-block text-danger'><?php echo $item->description; ?></span>
									</td>
									<td><?php echo $item->qty; ?></td>
									<td>
										<?php
											if($data->status == 0){
												?>
												<input type="text"
												       class='form-control txtQty'
												       data-item_id='<?php echo $item->item_id; ?>'
												       data-orig_qty='<?php echo $item->qty; ?>'
												       placeholder='Enter Return Qty'

													>
												<?php
											}
										?>
									</td>

								</tr>
								<?php
							}

						?>
					</tbody>
				</table>
			<hr>
			<?php
				if($data->status == 0){
					?>
					<button data-id="<?php echo $id; ?>" class='btn btn-primary btnProcess'>Process</button>
					<?php
				}
			?>
			<?php
		}


	}



	function processRequest(){

		$id = Input::get('id');
		$data = Input::get('data');
		$member_equipment = new Member_equipment();

		$data = json_decode($data);

		if(count($data)){

			$request = $member_equipment->requestData($id);
			$transfer = new Transfer_inventory_mon();
			$now = time();
			$user = new User();
			$transfer->create(array(
				'status' => 1,
				'is_active' =>1,
				'branch_id' =>$request->branch_id,
				'company_id' =>$user->data()->company_id,
				'created' => $now,
				'modified' => $now,
				'from_where' => 'Return equipment'
			));
			$lastid = $transfer->getInsertedId();
			foreach($data as $d){
				$item_id = $d->item_id;
				$qty = $d->qty;
				$orig_qty = $d->orig_qty;

				if(is_numeric($item_id) && is_numeric($qty)){
					$transfer_details = new Transfer_inventory_details();
					$transfer_details->create(array(
						'transfer_inventory_id' => $lastid,
						'rack_id_from' => 0,
						'rack_id_to' => 0,
						'item_id' =>$item_id,
						'qty' =>$qty,
						'is_active' => 1
					));
				}

				$left = $orig_qty - $qty;
				if($left){
					addMemberEquipment($request->member_id,$item_id,$left);
				}
			}
		}

		$member_equipment->processRequest($id);
		echo "Request processed successfully.";
	}

	function addMemberEquipment($member_id = 0,$item_id = 0,$qty= 0){
		$mem = new Member_equipment();
		$user = new User();
		if(is_numeric($member_id) && is_numeric($item_id) && is_numeric($qty)){
			// check if existing
			$mem_log = new Member_equipment_log();
			$chk = $mem->checkEquipment($member_id,$item_id);
			if(isset($chk->borrowed_qty)){
				// update
				$mem->updateEquipment($member_id,$item_id,$qty);
				$mem_log->create(
					[
						'member_id' => $member_id,
						'item_id' => $item_id,
						'from_borrowed_qty' => $chk->borrowed_qty,
						'to_borrowed_qty' => ($chk->borrowed_qty +$qty),
						'created' => time(),
						'status' => 0,
						'user_id' => $user->data()->id,
						'company_id' => $user->data()->company_id
					]
				);
			} else {
				// insert

				$mem->create(
					[
						'member_id' => $member_id,
						'item_id' => $item_id,
						'borrowed_qty' => $qty,
						'is_active' => 1,
						'created' => time(),
						'company_id' => $user->data()->id
					]
				);
				$mem_log->create(
					[
						'member_id' => $member_id,
						'item_id' => $item_id,
						'from_borrowed_qty' =>0,
						'to_borrowed_qty' => $qty,
						'created' => time(),
						'status' => 0,
						'user_id' => $user->data()->id,
						'company_id' => $user->data()->company_id
					]
				);
			}
		}
	}