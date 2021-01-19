<?php
	include 'ajax_connection.php';

	$functionName = Input::get("functionName");
	if(function_exists($functionName)) $functionName();

	function getSupplementary(){
		$member_id = Input::get('member_id');
		if(is_numeric($member_id)){
			$member_details = new Member($member_id);
			$membership_id = $member_details->data()->membership_id;
			$sup_no = 0;
			$purchase_needed_per_month = 0;
			$needed_ind = 0;
			if($membership_id){

				$pg = new Point_group($member_details->data()->membership_id);
				$sup_no = $pg->data()->supplementary;
				$sup_no += 1;
				$purchase_needed_per_month = $pg->data()->needed_purchase_amount;
				$needed_ind = $purchase_needed_per_month / $sup_no;
			}

			$supplementary = new Supplementary();
			$supps = $supplementary->getAll($member_id);
			$cur = count($supps);
			$member_sales = computeUSDPV($member_id);
			$mem_arr =[];
			$gain = 0;
			$gain_binary_pv = 0;
			$gain_uni_level_pv = 0;
			$gains = [];
			if($member_sales){
				foreach($member_sales as $msales){
					if($msales->saletotal && $needed_ind){
						$floor = floor($msales->saletotal/ $needed_ind);
						$gains[] = $floor;
					}
					$mem_arr[$msales->member_id] = $msales->saletotal;
				}
			}

			if($gains){
				$gain = min($gains);
				$pg = new Point_group($membership_id);
				$gain_binary_pv = $pg->data()->binary_pv_total * $gain;
				$gain_uni_level_pv = $pg->data()->uni_level_pv_total * $gain;


			}

			if($sup_no == $cur){
				echo "<input type='hidden' id='hid_disable_add_suplementary' value='1'>";
			}
			echo "<p>Maximum number of supplementaries: <strong class='text-success'>$sup_no</strong></p>";
			echo "<p>Needed purchase per month: <strong class='text-success'>".number_format($purchase_needed_per_month,2)."</strong></p>";

			if($supps){
				echo "<ul class='list-group'>";
				$val_main = isset($mem_arr[$member_id]) ? $mem_arr[$member_id] : 0;
				echo "<li class='list-group-item active' style='background: #337ab7;color:#fff;'><i class='fa fa-user'></i> ".$member_details->data()->lastname."  <span class='span-block text-danger'> <i class='fa fa-money'></i>  ".number_format($val_main,2)."</li>";
				foreach($supps as $s){
					$val = isset($mem_arr[$s->child_member_id]) ? $mem_arr[$s->child_member_id] : 0;
					echo "<li class='list-group-item'><i class='fa fa-user'></i> $s->lastname  <span class='span-block text-danger'> <i class='fa fa-money'></i>  ".number_format($val,2)."</li>";
				}
				echo "</ul>";
			} else {
				echo "<div class='alert alert-warning'>No supplementary</div>";
			}
		}
	}
	function computeUSDPV($member_id = 0){
		$user = new User();
		$sales = new Sales();
		$supplementary = new Supplementary();
		$supps = $supplementary->getAll($member_id);
		if($supps){
			$list = [];
			$list[] = $member_id;
			foreach($supps as $sup){
				$sup_member_id = $sup->child_member_id;
				$list[] = $sup_member_id;
			}
			if($list){
				$dt1 = date('F Y');
				$dt2 = date('F d, Y' , strtotime($dt1 . "1 month -1 sec"));
				$dt1 = strtotime($dt1);
				$dt2 = strtotime($dt2);
				$member_sales = $sales->getTotalSalesPerMember($user->data()->company_id,$dt1,$dt2,$list);
				if($member_sales){
					return $member_sales;
				} else {
					return [];
				}
			}
		}
	}
	function saveSupplementary(){
		$member_id = Input::get('member_id');
		$sup_id = Input::get('sup_id');
		if(is_numeric($member_id) && is_numeric($sup_id)){
			$user = new User();
			$supplementary = new Supplementary();
			$has_sup = $supplementary->hasSup($sup_id);
			if(isset($has_sup->cnt) && $has_sup->cnt == 0){
				$supplementary->create(array(
					'parent_member_id' => $member_id,
					'child_member_id' => $sup_id,
					'created' => time(),
					'company_id' => $user->data()->company_id,
					'user_id' => $user->data()->id,
					'is_active' =>1
				));
				echo "Supplementary added successfully.";
			} else {
				echo "Record already exists";
			}
		}else {
			echo "Invalid request.";
		}

	}
	function sellingPointsModal(){
		$point = new Point();
		$user = new User();
		$points =$point->getActiveUserPoint($user->data()->member_id);

		?>
		<?php if($points) {?>
			<div class="form-group">

				<strong>Type: </strong>
				<select name="s_point_type" id="s_point_type" class="form-control">
					<option value="">Choose type</option>
					<?php
						foreach($points as $point){
							?>
							<option data-value='<?php echo $point->points; ?>' value="<?php echo $point->point_id; ?>"><?php echo $point->point_name; ?> (<?php echo $point->points; ?>)</option>
							<?php
						}
					?>
				</select>
			</div>
			<div class="form-group">
				<strong>Transfer value:</strong>
				<input type="text" id='s_value' class='form-control'>
			</div>
			<div class="form-group">
				<strong>Starting Bid:</strong>
				<input type="text" id='s_amount' class='form-control'>
			</div>
			<div class="form-group">
				<strong>Remarks:</strong>
				<input type="text" id='s_remarks' class='form-control'>
			</div>
			<div class="form-group text-right">
				<button class='btn btn-default' id='s_submit'>Submit</button>
			</div>
			<?php
		} else echo "No registered membership.";
	}
	function getBidForm(){
		$id = Input::get('id');
		?>
		<input type="hidden" id='b_hid_id' value='<?php echo $id; ?>'>
		<div class="form-group">
			<strong>Bid Amount</strong>
			<input type="text" id='b_amount' class='form-control'>
		</div>
		<div class="form-group">
			<strong>Remarks</strong>
			<input type="text" id='b_remarks' class='form-control'>
		</div>
		<div class="form-group text-right">
			<button class='btn btn-default' id='b_submit'>Submit</button>
		</div>
		<?php

	}
	function getAvailablePoints(){
		$point = new Point();
		$user = new User();
		$points =$point->getActiveUserPoint($user->data()->member_id);

		?>
		<?php if($points) {?>
		<div class="form-group">

				<strong>Type: </strong>
				<select name="t_point_type" id="t_point_type" class="form-control">
					<option value="">Choose type</option>
					<?php
						foreach($points as $point){
							?>
							<option data-value='<?php echo $point->points; ?>' value="<?php echo $point->point_id; ?>"><?php echo $point->point_name; ?> (<?php echo $point->points; ?>)</option>
							<?php
						}
					?>
				</select>


		</div>
		<div class="form-group">
			<strong>Transfer value:</strong>
			<input type="text" id='t_value' class='form-control'>
		</div>
		<div class="form-group">
			<strong>Transfer to:</strong>
			<input type="text" id='t_member_id' class='form-control'>
		</div>
		<div class="form-group">
			<strong>Remarks:</strong>
			<input type="text" id='t_remarks' class='form-control'>
		</div>
		<div class="form-group text-right">
			<button class='btn btn-default' id='t_submit'>Submit</button>
		</div>
			<?php
		} else echo "No registered membership.";
	}
	function saveBidList(){
		$id = Input::get('id');
		$remarks = Input::get('remarks');
		$amount= Input::get('amount');
		$id = Encryption::encrypt_decrypt('decrypt',$id);
		$user  = new User();
		$member_id = $user->data()->member_id;
		if($amount && $member_id && $id && is_numeric($member_id) && is_numeric($id) && is_numeric($amount)){
			$bid = new Bid_list();
			$bid->create([
				'company_id'=> $user->data()->company_id,
				'status'=>1,
				'created'=>time(),
				'member_id'=>$member_id,
				'is_active'=>1,
				'remarks'=>$remarks,
				'amount'=>$amount,
				'sell_point_id'=>$id,
			]);
			echo "Bid submitted successfully";
		} else {
			echo "Invalid data.";
		}

	}
	function createSellPoint(){
		$type = Input::get('type');
		$point_value = Input::get('point_value');
		$amount = Input::get('amount');
		$remarks = Input::get('remarks');
		if(is_numeric($type) && is_numeric($point_value) && is_numeric($amount)){
			$point = new Point();
			$user = new User();
			$points =$point->getActiveUserPoint($user->data()->member_id,$type);
			$selling_point = new Selling_point();
			$cur_point = $points->points;

			if($cur_point < $point_value){
				die("Invalid value");
			}

			$selling_point->create_sell_request($user,$type,$point_value,$amount,$remarks);

		} else {
			echo "Invalid request";
		}

	}
	function transferPoint(){
		$type = Input::get('type');
		$member_id = Input::get('member_id');
		$point_value = Input::get('point_value');
		$remarks = Input::get('remarks');
		if($type && $member_id && $point_value){
			$point = new Point();
			$user = new User();
			$points =$point->getActiveUserPoint($user->data()->member_id,$type);
			$tranfer_point = new Transfer_point();
			$cur_point = $points->points;
			 $unit_name = $points->unit_name;
			if($cur_point < $point_value){
				die("Invalid value");
			}
			$same_unit = $point->getSameUnit($member_id,$unit_name);
			if($same_unit){
				// check pending // timeout 5mins
				// deduct points
				$tranfer_point->transfer_points($user,$member_id,$type,$point_value,$same_unit->id,$remarks);

			}
		}

	}
	function addMemGroup(){
		$name = Input::get('name');
		$point_list = Input::get('point_list');
		$sup_count = Input::get('sup_count');
		$purchase_per_month = Input::get('purchase_per_month');
		$binary_pv_total = Input::get('binary_pv_total');
		$uni_level_pv_total = Input::get('uni_level_pv_total');
		$user = new User();
		$point_group = new Point_group();
		if($name){
			$point_group->create(array(
				'user_id' => $user->data()->id,
				'company_id' => $user->data()->company_id,
				'name' => $name,
				'supplementary' => $sup_count,
				'needed_purchase_amount' => $purchase_per_month,
				'binary_pv_total' => $binary_pv_total,
				'uni_level_pv_total' => $uni_level_pv_total,
				'created' => time(),
				'is_active' => 1
			));
			$lastid = $point_group->getInsertedId();
			$point_list = json_decode($point_list,true);
			if($point_list){
					$pg_rel =  new Point_group_rel();
				foreach($point_list as $pl){
					$pg_rel->create(array(
							'pg_id' => $lastid,
							'point_id' => $pl,
							'created' => time(),
							'is_active' =>1,
							'company_id' =>$user->data()->company_id
					));
				}
			}
			echo "Updated successfully";
		}
	}
	function updateGroup(){
		$id = Input::get('id');
		$name = Input::get('name');
		$sup_count = Input::get('sup_count');
		$value = json_decode(Input::get('value'),true);
		$user = new User();
		if($id && $name && $value){
			$p_grp = new Point_group();
			$p_group_rel = new Point_group_rel();
			$p_grp->update(array('name'=>$name,'supplementary'=>$sup_count),$id);
			$p_group_rel->removeRel($id);
			foreach($value as $v){
				$p_group_rel->create(array(
					'pg_id' => $id,
					'point_id' => $v,
					'created' => time(),
					'is_active' =>1,
					'company_id' =>$user->data()->company_id
				));
			}
			echo "Updated successfully.";
		} else {
			echo "Invalid data";
		}
	}
	function getMyPoints(){
		$user = new User();
		$member_id  = $user->data()->member_id;
		if($member_id){
			$point = new Point();
			$myPoints = $point->getActiveUserPoint($member_id);
			echo "<input type='hidden' value='".json_encode($myPoints)."' id='mypoint_list'/>";
			$pg_name = "";
			$body = "";
			if($myPoints){
				$body = "<table class='table'>";
				$body .= "<tr><th>Type</th><th>Points</th></tr>";
				foreach($myPoints as $p){
					$pg_name = $p->pg_name;
					$body .= "<tr><td style='border-top: 1px solid #ccc;'>{$p->point_name}</td><td style='border-top: 1px solid #ccc;'>{$p->points}</td></tr>";
				}
				$logs = $point->get_record_user_log($user->data()->company_id,0,10,$member_id);
				$logs_body = "";
				if($logs){
					$logs_body .= "<table class='table table-bordered' id='tblSales'><thead><tr><th>Type</th><th>From</th><th>To</th><th>Created</th><th></th></tr></thead><tbody>";
					foreach($logs as $l){
						$logs_body .= "<tr><td>$l->point_name</td><td>$l->from_points</td><td>$l->to_points</td><td>".date('m/d/Y H:i:s A',$l->created)."</td><td class='text-danger'>".escape($l->remarks)."</td></tr>";
					}
					$logs_body .= "</tbody></table>";
				} else {
					$logs_body = "<div class='alert alert-info'>No result found.</div>";
				}
				$body .= "</table>";
				$html = "<div class='row'>";
				$html .= "<div class='col-md-4'>"; // col md 4
				$html .= "<h4>My Points</h4>";
				$html .= "<div class='panel panel-default'>";
				$html .= "<div class='panel-heading'>";
				$html .= $pg_name;
				$html .= "</div>";
				$html .= "<div class='panel-body'>";
				$html .= $body;
				$html .= "</div>";
				$html .= "</div>";
				$html .= "</div>"; // end col md 4
				$html .= "<div class='col-md-8'>"; // col md 8
				$html .= "<h4>Recent transaction</h4>";
				$html .= "<div class='panel panel-default'>";
				$html .= "<div class='panel-heading'>";
				$html .= "Logs";
				$html .= "</div>";
				$html .= "<div class='panel-body'>";
				$html .= $logs_body;
				$html .= "</div>";
				$html .= "</div>";
				$html .= "</div>";
				$html .= "</div>";
				echo $html;
			} else {
				echo "<p>No result found.</p>";
			}
		} else {
			echo "<p>No result found.</p>";
		}
	}
	function getTransferPoints(){
		$status = Input::get('status');
		if(!$status) $status = 1;
		$user = new User();
		$transfer_point = new Transfer_point();
		$limit = 50;
		$cid = $user->data()->company_id;
		$member_id = $user->data()->member_id;
		$countRecord = $transfer_point->countRecord($cid,$member_id,$status);
		$total_pages = $countRecord->cnt;
		$stages = 3;
		$page = Input::get('p');

		if($page) {
			$start = ($page - 1) * $limit;
		} else {
			$start = 0;
		}
		$data = $transfer_point->get_transfer($cid, $start, $limit,$member_id,$status);
		getpagenavigation($page, $total_pages, $limit, $stages);
		if($data){
			?>
		<div id="no-more-tables">
			<table class='table' id='tblSales'>
				<thead>
				<tr>
					<th>From</th>
					<TH>To</TH>
					<TH>Type</TH>
					<TH>Value</TH>
					<th></th>
				</tr>
				</thead>
				<tbody>
			<?php
			foreach($data as $d){
				?>
				<tr>
					<td data-title="From"><i class='fa fa-user'></i> <?= capitalize($d->from_name); ?></td>
					<td data-title="To"><i class='fa fa-user'></i>  <?= capitalize($d->to_name); ?></td>
					<td data-title="Type"><?= capitalize($d->unit_name); ?></td>
					<td data-title="Value"><?= $d->point_value; ?></td>
					<td data-title="">
						<?php if($d->status == 1){ ?>
						<button data-id="<?php echo Encryption::encrypt_decrypt('encrypt',$d->id); ?>"  class='btn btn-default btn-sm t_process'><i class='fa fa-cog'></i> Process</button>
						<button data-id="<?php echo Encryption::encrypt_decrypt('encrypt',$d->id); ?>" class='btn btn-danger btn-sm t_cancel'><i class='fa fa-remove'></i> Cancel</button>
						<?php } else if($d->status == 2) { ?>
								<span class='label label-default'>Transferred</span>
						<?php } else if($d->status == 6) { ?>
								<span class='label label-danger'>Cancelled</span>
						<?php } ?>
					</td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>
		<?php
		} else {
			echo "<br><div class='alert alert-info'>No item found.</div>";
		}
	}
	function getSellPoints(){
		$status = Input::get('status');
		if(!$status) $status = 1;
		$user = new User();
		$selling_point = new Selling_point();
		$limit = 50;
		$cid = $user->data()->company_id;
		$member_id = $user->data()->member_id;
		$countRecord = $selling_point->countRecord($cid);
		$total_pages = $countRecord->cnt;
		$stages = 3;
		$page = Input::get('p');

		if($page) {
			$start = ($page - 1) * $limit;
		} else {
			$start = 0;
		}
		$data = $selling_point->get_record($cid, $start, $limit);
		getpagenavigation($page, $total_pages, $limit, $stages);
		if($data){
			$arr_stats = ['','Pending','Closed','Reserved','Reserved','Reserved','Cancelled'];
			?>
			<div id="no-more-tables">
			<table class='table' id='tblSales'>
				<thead>
				<tr>
					<th>Posted by</th>
					<th>Status</th>
					<TH>Type</TH>
					<TH>Points</TH>
					<TH>Starting Bid</TH>
					<th>Remarks</th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php
					$cur_member = $user->data()->member_id;
					foreach($data as $d){
						?>
						<tr>
							<td data-title="Posted by"><i class='fa fa-user'></i> <?= capitalize($d->from_name); ?></td>
							<td><?php echo $arr_stats[$d->status]; ?></td>
							<td data-title="Type"><?= capitalize($d->point_name); ?></td>
							<td data-title="Points"><?= number_format($d->point_value,3); ?></td>
							<td data-title="Selling amount"><?= number_format($d->selling_amount,2); ?></td>
							<td data-title="Remarks"><?=  escape($d->remarks) ?></td>
							<td>
								<?php if($d->status == 1){
									?>
									<button  data-id='<?php echo Encryption::encrypt_decrypt('encrypt',$d->id)?>' class='btn btn-default btn-sm btnBidList'>Bid List</button>
									<?php if($cur_member != $d->member_id_from){
										?>
										<button data-id='<?php echo Encryption::encrypt_decrypt('encrypt',$d->id)?>' class='btn btn-default btn-sm btnBid'>Bid</button>
										<?php
									} else {
										?>
										<button  data-id='<?php echo Encryption::encrypt_decrypt('encrypt',$d->id)?>' class='btn btn-danger btn-sm btnBidCancel'>Cancel</button>
										<?php
									}?>
									<?php
								} else if ($d->status == 2){

								}  else {

								}?>


							</td>
						</tr>
						<?php
					}
				?>
				</tbody>
			</table>
			<?php
		} else {
			echo "No item found.";
		}
	}
	function bidCancel(){
		$id = Encryption::encrypt_decrypt('decrypt', Input::get('id'));
		if($id && is_numeric($id)){
			$sell =  new Selling_point($id);
				if($sell->data()->status == 1){
					$point = new Point;
					$user = new User();
					$point->updateUserPoint(
						$sell->data()->member_id_from,
						$user,
						0,
						0,
						$sell->data()->point_id,
						$sell->data()->point_value,
						0,
						2
					);
					$sell->update(array('status'=> 6),$id);
					echo "Cancelled successfully.";
			}
		}
	}
	function getBidList(){
		$id = Input::get('id');
		$id = Encryption::encrypt_decrypt('decrypt',$id);
		if(is_numeric($id)){
			$bidList = new Bid_list();
			$sell_point = new Selling_point($id);
			$data = $bidList->bidList($id);
			$user = new User();
			$member_id = $user->data()->member_id;
			if($data){

				foreach($data as $d){
					echo "<div class='panel panel-default'>";
					echo "<div class='panel-body'>";
					echo "<p><strong>".$d->member_name."</strong> <span class='span-block'>$d->contact_number</span></p>";
					echo "<p><span class='h5'>".number_format($d->amount,2)."</span></p>";
					echo "<p class='text-danger'>".$d->remarks."</p>";
					if($member_id == $sell_point->data()->member_id_from){
						echo "<div class='text-right'><button class='btn btn-default'><i class='fa fa-thumbs-up'></i></button> <button  class='btn btn-default'><i class='fa fa-thumbs-down'></i></button></div>";
					}
					echo "</div>";
					echo "</div>";
				}

			} else {
				echo "<div class='alert alert-info'>No bid yet.</div>";
			}
		} else {
			echo "<div class='alert alert-info'>Invalid request</div>";
		}
	}
	function processTransferPoints(){

		$id = Encryption::encrypt_decrypt('decrypt',Input::get('id'));
		if(is_numeric($id)){
			$tranfer =  new Transfer_point($id);
			if($tranfer->data()->status == 1){
				$point = new Point;
				$user = new User();
				$point->updateUserPoint(
					$tranfer->data()->member_id_to,
					$user,
					0,
					0,
					$tranfer->data()->point_type_to,
					$tranfer->data()->point_valuem,
					0,
					1
				);
				$tranfer->update(array('status'=> 2),$id);
				echo "Transferred successfully.";
			}
		} else {
			echo "Invalid request.";
		}
	}
	function cancelTransferPoints(){
		$id = Encryption::encrypt_decrypt('decrypt',Input::get('id'));
		if(is_numeric($id)){
			$tranfer =  new Transfer_point($id);
			if($tranfer->data()->status == 1){
				$point = new Point;
				$user = new User();
				$point->updateUserPoint(
					$tranfer->data()->member_id_from,
					$user,
					0,
					0,
					$tranfer->data()->point_type,
					$tranfer->data()->point_value,
					0,
					1
				);
				$tranfer->update(array('status'=> 6),$id);
				echo "Cancelled successfully.";
			}
		}
	}
	function getpagenavigation($page, $total_pages, $limit, $stages) {
		if($page == 0) {
			$page = 1;
		}
		$prev = $page - 1;
		$next = $page + 1;
		$lastpage = ceil($total_pages / $limit);
		$LastPagem1 = $lastpage - 1;


		$paginate = '';
		if($lastpage > 1) {

			$paginate .= "<div style='padding:3px;' class='text-right'><ul class='pagination' >";

			if($page > 1) {
				$paginate .= "<li><a href='#'  class='paging' page='$prev' style='padding:5px'><span class='hidden-xs'>PREV</span><span class='visible-xs'><span class='glyphicon glyphicon-chevron-left'></span></span></a></li>";
			} else {
				$paginate .= "<li class='disabled'><span class='disabled' style='padding:5px'><span class='hidden-xs'>PREV</span><span class='visible-xs'><span class='glyphicon glyphicon-chevron-left'></span></span></span></span></li>";
			}


			if($lastpage < 7 + ($stages * 2)) {
				for($counter = 1; $counter <= $lastpage; $counter++) {
					if($counter == $page) {
						$paginate .= "<li class='active'><span class='current' style='padding:5px'>$counter</span></li>";
					} else {
						$paginate .= "<li><a href='#'  class='paging' page='$counter' style='padding:5px'>$counter</a></li>";
					}
				}
			} elseif($lastpage > 5 + ($stages * 2)) {

				if($page < 1 + ($stages * 2)) {
					for($counter = 1; $counter < 4 + ($stages * 2); $counter++) {
						if($counter == $page) {
							$paginate .= "<li class='active'><span class='current' style='padding:5px'>$counter</span></li>";
						} else {
							$paginate .= "<li><a href='#'  class='paging' page='$counter' style='padding:5px'>$counter</a></li>";
						}
					}
					$paginate .= "<li><span style='padding:5px'>...</span></li>";
					$paginate .= "<li><a href='#'   class='paging' page='$LastPagem1' style='padding:5px'>$LastPagem1</a></li>";
					$paginate .= "<li><a href='#' class='paging' page='$lastpage' style='padding:5px'>$lastpage</a></li>";
				} elseif($lastpage - ($stages * 2) > $page && $page > ($stages * 2)) {
					$paginate .= "<li><a href='#' class='paging' page='1'  style='padding:5px'>1</a></li>";
					$paginate .= "<li><a href='#' class='paging' page='2'  style='padding:5px'>2</a></li>";
					$paginate .= "<li><span style='padding:5px'>...</span></li>";
					for($counter = $page - $stages; $counter <= $page + $stages; $counter++) {
						if($counter == $page) {
							$paginate .= "<li class='active'><span class='current' style='padding:5px'>$counter</span></li>";
						} else {
							$paginate .= "<li><a href='#' class='paging' page='$counter'  style='padding:5px'>$counter</a></li>";
						}
					}
					$paginate .= "<li><span  style='padding:5px'>...</span></li>";
					$paginate .= "<li><a href='#' class='paging' page='$LastPagem1' style='padding:5px'>$LastPagem1</a></li>";
					$paginate .= "<li><a  href='#'  class='paging' page='$lastpage' style='padding:5px'>$lastpage</a></li>";
				} else {
					$paginate .= "<li><a href='#' class='paging' page='1' style='padding:5px'>1</a></li>";
					$paginate .= "<li><a href='#' class='paging' page='2' style='padding:5px'>2</a></li>";
					$paginate .= "<li><span style='padding:5px'>...</span></li>";
					for($counter = $lastpage - (2 + ($stages * 2)); $counter <= $lastpage; $counter++) {
						if($counter == $page) {
							$paginate .= "<li class='active'><span class='current' style='padding:5px'>$counter</span></li>";
						} else {
							$paginate .= "<li><a href='#' class='paging' page='$counter'  style='padding:5px'>$counter</a></li>";
						}
					}
				}
			}


			if($page < $counter - 1) {
				$paginate .= "<li><a href='#' class='paging' page='$next' style='padding:5px'><span class='hidden-xs'>NEXT</span><span class='visible-xs'><span class='glyphicon glyphicon-chevron-right'></span></span></a></li>";
			} else {
				$paginate .= "<li class='disabled'><span class='disabled' style='padding:5px'><span class='hidden-xs'>NEXT</span><span class='visible-xs'><span class='glyphicon glyphicon-chevron-right'></span></span></span></li>";
			}

			$paginate .= "</ul></div><div style='clear: both;'></div>";


		}
		// echo $total_pages.' Results';
		echo $paginate;
	}