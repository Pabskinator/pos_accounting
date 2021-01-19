<?php
	include 'ajax_connection.php';

	$functionName = Input::get("functionName");
	$functionName();
	function saveEditBundleQty(){
		$id= Input::get('id');
		$qty= Input::get('qty');
		$bundle = new Bundle();
		$user = new User();
		$bundle->update(array('child_qty'=> $qty),$id);

		Log::addLog($user->data()->id,$user->data()->company_id,"Update Bundle Child Qty ID $id","ajax_product.php");


		echo "Updated successfully";
	}

	function deleteBundleChild(){
		$id= Input::get('id');
		$bundle = new Bundle($id);
		$user = new User();
		$bundle->deleteChild($id);
		Log::addLog($user->data()->id,$user->data()->company_id,
			"Delete Bundle Child Parent: " .$bundle->data()->item_id_parent . " Child: " .$bundle->data()->item_id_child,"ajax_product.php");

	}

	function selectSerials(){
		$payment_id = Input::get('payment_id');
		$item_id = Input::get('item_id');
		$qty = Input::get('qty');
		$serial = new Serial();
		$serials = $serial->getItemSerials($payment_id,$item_id);
		$arr = [];
		if($serials){
			foreach($serials as $s){
				if($s->serial_no){
					$arr[] = ['id'=> $s->id,'serial_no' => $s->serial_no,'item_id'=>$item_id];
				}
			}
			$count_left = $qty - count($arr);
		} else {
			$count_left = $qty;
		}
		if($count_left){
			for($i =1; $i<=$count_left;$i++){
				$arr[] = ['id'=> 0, 'serial_no' => '','item_id'=>$item_id];
			}
		}
		echo json_encode($arr);
	}
	function showSerialsAssembly(){
		$details_id = Input::get('details_id');
		$item_id = Input::get('item_id');
		$qty = Input::get('qty');
		$itemService = new Assemble_details($details_id);

		$serials = $itemService->data()->serial_numbers;
		$arr = [];
		if($serials){
			$serials = json_decode($serials);

			foreach($serials as $s){
				if($s->serial_no){
					$arr[] = ['id'=> 0,'serial_no' => $s->serial_no,'item_id'=>$item_id];
				}
			}
			$count_left = $qty - count($arr);
		} else {
			$count_left = $qty;

		}

		if($count_left){
			for($i =1; $i<=$count_left;$i++){
				$arr[] = ['id'=> 0, 'serial_no' => '','item_id'=>$item_id];
			}
		}


		echo json_encode($arr);
	}

	function saveSerials(){
		$payment_id = Input::get('payment_id');
		$details = Input::get('details');
		$details = json_decode($details);
		$user = new User();
		if($details){
			$serial = new Serial();
			$now = time();
			foreach($details as $det){
				if($det->serial_no){
					if($det->id){
						$serial->update(array('serial_no' => $det->serial_no),$det->id);
					} else {
						$serial->create(array(
							'serial_no' => 	$det->serial_no,
							'item_id' => $det->item_id,
							'payment_id' => $payment_id,
							'company_id' => $user->data()->company_id,
							'user_id' => $user->data()->id,
							'created' => $now,
							'is_active' => 1
						));
					}

				}
			}
			echo "Serials updated successfully.";
		}
	}
	function saveSerialsAssembly(){
		$details_id = Input::get('details_id');
		$details = Input::get('details');
		$details = json_decode($details);

		if($details){
			$assembly_details = new Assemble_details($details_id);

			$arr = [];
			foreach($details as $det){
				if($det->serial_no){
					$arr[] = $det;
				}
			}

			$assembly_details->update(
				['serial_numbers' => json_encode($arr)] , $details_id
			);

			echo "Serials updated successfully.";
		}
	}

	function getAssembleItemList(){
		$status = Input::get('status');
		$branch_id = Input::get('branch_id');
		$dt_from= Input::get('dt_from');
		$dt_to= Input::get('dt_to');
		$member_id= Input::get('member_id');
		$is_dl= Input::get('is_dl');

		$border = "";
		if($is_dl == 1){
			$filename = "assemble-item-" . date('m-d-Y-H-i-s-A') . ".xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");
			$border = "border=1";
		}

		if($dt_from && $dt_to){
			$dt_from = strtotime($dt_from);
			$dt_to = strtotime($dt_to . "1 day -1 sec");
		} else {
			$dt_from = strtotime(date('F Y'));
			$dt_to = strtotime(date('F Y'). "1 month -1 min");
		}

		$user = new User();
		$assemble_cls = new Assemble_request();
		$items = $assemble_cls->getAssembleItem($status,$branch_id,$dt_from,$dt_to,$member_id);

		if($items){
			?>
			<p>
			   Showing records from
				<strong class='text-danger'><?php echo date('m/d/Y H:i:s A',$dt_from); ?></strong>
			   to
				<strong class='text-danger'><?php echo date('m/d/Y H:i:s A',$dt_to); ?></strong>
			</p>
			<div id="no-more-tables">
				<table class="table" <?php echo $border; ?>>
					<thead>
					<tr>
						<th>Assemble Id</th>
						<th>Member</th>
						<th>Branch</th>
						<th>Status</th>
						<th>Item</th>
						<th>Qty</th>
						<th>Date Created</th>
						<th>Ctrl #</th>

					</tr>
					</thead>
					<tbody>
					<?php
						$arrType = ['',Configuration::getValue('a_step1'), Configuration::getValue('a_step2'),Configuration::getValue('a_step3'),'Cancelled'];
						foreach($items as $item){
							$rem = 'No remarks';
							if($item->remarks) $rem = $item->remarks;
							$orderid = "N/A";
							if($item->wh_id) $orderid = "ORDER ID# ".$item->wh_id;
							?>
							<tr>
								<td style='border-top:1px solid #ccc;' data-title='Id'><?php echo escape($item->assemble_id); ?></td>
								<td style='border-top:1px solid #ccc;' data-title='Member'><?php echo ($item->member_name) ? escape($item->member_name) : 'N/A'; ?></td>
								<td style='border-top:1px solid #ccc;' data-title='Branch'><?php echo escape($item->branch_name); ?></td>
								<td style='border-top:1px solid #ccc;' data-title='Status'><?php echo escape($arrType[$item->status]); ?></td>
								<td style='border-top:1px solid #ccc;' data-title='Item'>
									<?php echo $item->item_code ?>
									<small class='span-block text-danger'><?php echo $item->description; ?></small>
								</td>
								<td style='border-top:1px solid #ccc;' data-title='Qty'><?php echo formatQuantity($item->qty); ?></td>
								<td style='border-top:1px solid #ccc;' data-title='Created'><?php echo date('F d, Y',$item->created); ?></td>
								<td style='border-top:1px solid #ccc;' data-title='Ctrl #'>
									<?php if($item->wh_id){
										?>
										<span style='display: block'>Order : <?php echo $item->wh_id; ?></span>
										<span style='display: block'><?php echo INVOICE_LABEL; ?>: <?php echo $item->invoice; ?></span>
										<span style='display: block'><?php echo DR_LABEL; ?>: <?php echo $item->dr; ?></span>
										<span style='display: block'><?php echo PR_LABEL; ?>: <?php echo $item->pr; ?></span>
										<?php
									} else {
										?>
										<span>No <?php echo INVOICE_LABEL; ?>/<?php echo DR_LABEL; ?>/<?php echo PR_LABEL; ?></span>
										<?php
									}?>

								</td>
							</tr>
							<?php
						}
					?>
					</tbody>
				</table>
			</div>
			<?php
		} else {
			?>
			<div class="alert alert-info">No record from <strong class='text-danger'><?php echo date('m/d/Y H:i:s A',$dt_from); ?></strong>
			                              to
				<strong class='text-danger'><?php echo date('m/d/Y H:i:s A',$dt_to); ?></strong>.</div>
			<?php
		}

	}


	function updateCBM(){

		$id = Input::get('id');
		$cbm_l = Input::get('cbm_l');
		$cbm_w = Input::get('cbm_w');
		$cbm_h = Input::get('cbm_h');
		$item_weight = Input::get('item_weight');

		$prod = new Product();
		$user = new User();

		$prod->update(
			[
				'cbm_l' => $cbm_l,
				'cbm_w' => $cbm_w,
				'cbm_h' => $cbm_h,
				'item_weight' => $item_weight,
			], $id
		);

		Log::addLog($user->data()->id,$user->data()->company_id,"Update CBM and Weight Item ID " . $id,'ajax_product.php');

		echo "Updated successfully.";

	}

	function uploadFiles(){

		$tempFile = $_FILES['file']['tmp_name'];          //3
		$description = Input::get('description');
		$targetPath = "../uploads/" ;


		$name = uniqid();
		$path = $_FILES['file']['name'];
		$ext = pathinfo($path, PATHINFO_EXTENSION);
		$targetFile =  $targetPath.$name . ".".$ext;  //5
		$res = move_uploaded_file($tempFile,$targetFile); //6

		$tbl = 'file_manager';
		if($res){
			$upcls = new Upload();
			$now = time();
			$user = new User();
			$user_ids = json_decode(Input::get('user_ids'),true);
			if($user_ids)			{
				$user_ids = implode(',',$user_ids);
			} else {
				$user_ids = '';
			}
			$upcls->create(array(
				'filename' =>$name. "." . $ext,
				'thumbnail' =>$user_ids,
				'ref_table' =>$tbl,
				'ref_id' => 0,
				'company_id' => $user->data()->company_id,
				'is_active' => 1,
				'created' => $now,
				'modified' => $now,
				'tags' => '',
				'title' => $path,
				'description' => $description,

			));
		}




	}
	function human_filesize($bytes, $decimals = 2) {
		$sz = 'BKMGTP';
		$factor = floor((strlen($bytes) - 1) / 3);
		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
	}
	function getFiles(){

		$upcls = new Upload();
		$user = new User();

		$list = $upcls->getImages(0,0,'file_manager',500,$user->data()->id);
		$arr = [];
		$total_file_size = 0;
		if($list){
			$user_lists = $user->getUsers($user->data()->company_id);
			$users = [];
			$users_data = [];
			$positions = [];
			foreach($user_lists as $u){
				$users_data[] = ['id' => $u->id, 'name' => ucwords(strtolower($u->firstname . " " . $u->lastname)),'position_id' => $u->position_id, 'position' => $u->position];
				$positions[] = ['position' => $u->position,'id' => $u->position_id];
				$users[$u->id] = ucwords(strtolower($u->firstname . " " . $u->lastname));


			}
			foreach($list as $l){
				$filename = $l->filename;
				$ex = explode('.',$filename);
				$ext = strtolower($ex[count($ex) -1]);
				$type_src = '';
				if($ext == 'pdf'){
					$type_src = '../css/img/icon-pdf.png';
				} else if ($ext == 'xls' || $ext == 'xlsx'){
					$type_src = '../css/img/icon-excel.png';
				} else if ($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'bmp'){
					$type_src = '../css/img/icon-image.png';
				}else if ($ext == 'doc' || $ext == 'docx'){
					$type_src = '../css/img/icon-word.png';
				}
				$bytes = filesize('../uploads/'.$filename);
				$file_size = 	human_filesize($bytes);
				$total_file_size  += $bytes;
				$l->description = ($l->description) ? $l->description : 'No Description';
				$user_names = "All";
				if($l->thumbnail){
					$explode_user =  explode(',',$l->thumbnail);
					$user_names = "";
					if($explode_user){
						foreach($explode_user as $ex){
							$user_names .= "$users[$ex] \n";
						}
					}
				}

				$arr[] = ['id' =>$l->id, 'users' => $user_names, 'description'=>$l->description,'size'=>$file_size,'created' => date('m/d/y',$l->created),'src_type' => $type_src,'src' => '../uploads/'.$filename,'title' => $l->title];
			}
			$total_file_size = number_format((($total_file_size / 1024) / 1024),2,'.','');

		}
		$positions = array_unique($positions, SORT_REGULAR);
		echo json_encode(['files' => $arr, 'users' => $users_data, 'positions' => $positions,'total_file_size' => $total_file_size ]);

	}

	function fileManagerPermission(){
		$user = new User();
		$p = 0;
		if($user->hasPermission('fm_manage')){
			$p = 1;
		}
		echo $p;
	}

	function logUser(){
		$user = new User();
		$info = Input::get('file_info');
		if($info){
			$info = json_decode($info);
			$title = $info->description;
			$src = $info->src;
			$att = "<a href='$src' target='_blank'>$title</a>";
			Log::addLog($user->data()->id,$user->data()->company_id,"View File ( $att )","file_manager");

		}

	}

	function deleteFileManager(){
		$id = Input::get('id');
		if($id && is_numeric($id)){
			$user = new User();
			$upload = new Upload($id);
			$path = '../uploads/'.$upload->data()->filename;
			if(file_exists($path)){
				$upload->deleteFile($id);
				unlink($path);
				$desc = $upload->data()->description ? $upload->data()->description : 'No Description';
				Log::addLog($user->data()->id,$user->data()->company_id,"Delete File ( ".$desc." )","file_manager");
				echo "Deleted successfully";
			} else {
				$upload->deleteFile($id);
				echo "File not found";
			}
		} else {
			echo "Invalid request";
		}

	}