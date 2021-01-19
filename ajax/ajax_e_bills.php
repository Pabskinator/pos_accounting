<?php
	include 'ajax_connection.php';

	$functionName = Input::get("functionName");


	if(function_exists($functionName)){
		$functionName();
	}
	function getCategory(){
		$biller = new Biller_category();
		$user = new User();
		$biller_category = $biller->get_active('biller_categories',['company_id','=',$user->data()->company_id]);
		if($biller_category){
			echo "<div id='no-more-tables'>";
			echo "<table class='table'>";
			echo "<thead><tr><th>Name</th><th>Date Created</th></tr></thead>";
			echo "<tbody>";
			foreach($biller_category as $bill){

				echo "<tr>";
				echo "<td data-title='Name' style='border-top:1px solid #ccc;'>$bill->name</td>";
				echo "<td  data-title='Created' style='border-top:1px solid #ccc;'>".date('F d, Y H:i:s A',$bill->created)."</td>";
				echo "<td style='border-top:1px solid #ccc;'>";
				echo "<button data-name='{$bill->name}' data-id='".Encryption::encrypt_decrypt('encrypt',$bill->id)."' class='btn btn-primary btn-sm btnUpdateCategory'><i class='fa fa-pencil'></i></button> <button  data-id='".Encryption::encrypt_decrypt('encrypt',$bill->id)."' class='btn btn-danger btn-sm btnDeleteCategory'><i class='fa fa-remove'></i></button>";
				echo "</td>";
				echo "</tr>";
			}
			echo "</tbody>";
			echo "</table>";
			echo "</div>";
		} else {
			echo "<div class='form-control'>No record yet.</div>";
		}
	}

	function addCategory(){
		$id = Input::get('id');
		$name = Input::get('name');
		$biller_category = new Biller_category();
		$user = new User();
		if($id){
			// update
			$id = Encryption::encrypt_decrypt('decrypt',$id);
			if(is_numeric($id)){
				$biller_category->update(array(
					'name'=> $name
				),$id);
				echo "Record updated successfully.";
			}
		} else {
			// insert
			$biller_category->create(array(
				'name' => $name,
				'created' => time(),
				'is_active' =>1,
				'company_id' =>$user->data()->company_id
			));
			echo "Record inserted successfully.";
		}
	}
	function deleteCategory(){
		$id = Input::get('id');
		$biller_category = new Biller_category();
		if($id){
			// update
			$id = Encryption::encrypt_decrypt('decrypt',$id);
			if(is_numeric($id)){
				$biller_category->update(array(
					'is_active'=> 0
				),$id);
				echo "Record deleted successfully.";
			}
		}
	}
	function deleteBillername(){
		$id = Input::get('id');
		$biller_name = new Biller_name();
		if($id){
			// update
			$id = Encryption::encrypt_decrypt('decrypt',$id);
			if(is_numeric($id)){
				$biller_name->update(array(
					'is_active'=> 0
				),$id);
				echo "Record deleted successfully.";
			}
		}
	}

	function getBillerName(){
		$biller = new Biller_name();
		$user = new User();
		$biller_category = $biller->get_active('biller_categories',['company_id','=',$user->data()->company_id]);
		$biller_names = $biller->get_active('biller_names',['company_id','=',$user->data()->company_id]);
		echo "<input type='hidden' value='".json_encode($biller_category)."' id='biller_categ_json'>";
		if($biller_names){
			echo "<div id='no-more-tables'>";
			echo "<table class='table'>";
			echo "<thead><tr><th>Name</th><th>Category</th><th>Date Created</th><th></th></tr></thead>";
			echo "<tbody>";
			foreach($biller_names as $bill){
				$category_name = 'None';
				if($bill->category_id){
					$categ = new Biller_category($bill->category_id);
					if($categ->data()->name){
						$category_name = $categ->data()->name;
					}
				}


				echo "<tr>";
				echo "<td data-title='Name' style='border-top:1px solid #ccc;'>$bill->name</td>";
				echo "<td data-title='Category' style='border-top:1px solid #ccc;'>$category_name</td>";
				echo "<td data-title='Created' style='border-top:1px solid #ccc;'>".date('F d, Y H:i:s A',$bill->created)."</td>";
				echo "<td style='border-top:1px solid #ccc;'>";
				echo "<button data-name='{$bill->name}' data-category_id='{$bill->category_id}' data-id='".Encryption::encrypt_decrypt('encrypt',$bill->id)."' class='btn btn-primary btn-sm btnUpdateBillerName'><i class='fa fa-pencil'></i></button> <button  data-id='".Encryption::encrypt_decrypt('encrypt',$bill->id)."' class='btn btn-danger btn-sm btnDeleteBillerName'><i class='fa fa-remove'></i></button>";
				$bill_data = new Biller_data_name();
				$bill_rec = $bill_data->get_active('biller_name_data',['biller_name_id','=',$bill->id]);
				if(!$bill_rec){
					echo " <button data-name='{$bill->name}' data-id='{$bill->id}' class='btn btn-default btn-sm btnFields'><i class='fa fa-list'></i></button>";
				}
				echo "</td>";
				echo "</tr>";
			}
					echo "</tbody>";
			echo "</table>";
			echo "</div>";
		} else {
			echo "<div class='form-control'>No record yet.</div>";
		}
	}
	function addBillerName(){
		$id = Input::get('id');
		$name = Input::get('name');
		$category_id = Input::get('category_id');
		$biller_name = new Biller_name();
		$user = new User();
		if($id){
			// update
			$id = Encryption::encrypt_decrypt('decrypt',$id);
			if(is_numeric($id)){
				$biller_name->update(array(
					'name'=> $name,
					'category_id'=> $category_id,
				),$id);
				echo "Record updated successfully.";
			}
		} else {
			// insert
			$biller_name->create(array(
				'name' => $name,
				'category_id' => $category_id,
				'created' => time(),
				'is_active' =>1,
				'company_id' =>$user->data()->company_id
			));
			echo "Record inserted successfully.";
		}
	}

	function saveFields(){
		$data = Input::get('data');
		$biller_id = Input::get('biller_id');

		if($data){
			$data = json_decode($data);
			if(count($data)){
				$biller = new Biller_data_name();
				$user = new User();
				foreach($data as $d){
					$element_name = $d->element_name;
					$choices = $d->choices;
					$data_type = $d->data_type;
					$label = $d->label;
					$is_required = $d->is_required;
					if($element_name && $data_type && $label){
						$biller->create(array(
							'biller_name_id' => $biller_id,
							'element' => $element_name,
							'label' => $label,
							'data_type' => $data_type,
							'choices' => $choices,
							'is_required' => $is_required,
							'is_active' => 1,
							'company_id' => $user->data()->company_id
						));
					}
				}
				echo "Updated successfully.";
			}
		}
	}
	function getCompanyByCategory(){
		$id = Input::get('id');
		if($id && is_numeric($id)){
			$biller = new Biller_name();
			$billers = $biller->get_active('biller_names',['category_id','=',$id]);
			if($billers){
				$ret = "<option value=''>Choose company</option>";
				foreach($billers as $bill){
					$ret .= "<option value='$bill->id'>$bill->name</option>";
				}
				echo $ret;
			} else {
				echo "2";
			}
		} else {
			echo "1";
		}
	}
	function showFormCompany(){
		$id = Input::get('id');
		if($id && is_numeric($id)){
			$user = new User();

			$biller = new Biller_name();
			$forms = $biller->get_active('biller_name_data',['biller_name_id','=',$id]);
			if($forms){
				$arrOldVal = [];
				$arrhaserror = [];
				?>
				<form action="" id='main_form_bills'>
				<?php
				$con = getConvenienceFee();
				$fee = 0;
				if($con){
					$fee = $con;
				}
				foreach($forms as $f){
								$amountfieldClass ="";
								if($f->label == 'Amount'){
									$amountfieldClass='txtAmount';
								}
								if ($f->element == 'form_label') {
									?>
									<div class="form-group">
									<h3 class="col-md-8 text-center" ><?php echo $f->label ?></h3>
									</div>
									<?php
									continue;
								}

								?>
								<div id='group_<?php echo  $f->id; ?>' class="form-group <?php echo in_array($f->id,$arrhaserror) ?  'has-error' : ''; ?>">
									<input type="hidden" value='<?php echo json_encode($f); ?>' id='rule_<?php echo $f->id ?>'>

									<label  for="<?php echo $f->id ?>"><?php echo $f->label ?></label>
									<?php if ($f->element == 'text') { ?>
									<input data-label='<?php echo $f->label ?>'  value='<?php echo (isset($arrOldVal[$f->id]) && ! empty($arrOldVal[$f->id])) ? $arrOldVal[$f->id] : ''; ?>' <?php echo ($f->is_required== 1) ? 'required' : ''; ?> id="<?php echo $f->id ?>" name="<?php echo $f->id ?>" placeholder="<?php echo $f->label ?>" class="form-control input-md <?php echo($f->data_type=='date') ? 'dts' : ''; ?> form_bill_inputs <?php echo $amountfieldClass; ?>" type="<?php echo $f->element ?>">
									<?php } else if ($f->element == 'textarea') { ?>
									<<?php echo $f->element; ?> <?php echo ($f->is_required== 1) ? 'required' : ''; ?> id="<?php echo $f->id ?>" name="<?php echo $f->id ?>" class='form-control form_bill_inputs' type='textarea' data-label='<?php echo $f->label ?>'><?php echo (isset($arrOldVal[$f->id]) && ! empty($arrOldVal[$f->id])) ? $arrOldVal[$f->id] : ''; ?></<?php echo $f->element; ?>>
									<?php } else if($f->element == 'select') { ?>
									<<?php echo $f->element; ?> class='form-control form_bill_inputs' <?php echo ($f->is_required== 1) ? 'required' : ''; ?> id="<?php echo $f->id ?>" name="<?php echo $f->id ?>" data-label='<?php echo $f->label ?>'>
									<?php
										$choices = explode(",",$f->choices);
										foreach($choices as $c){
											$ischeckedopt = '';
											if (isset($arrOldVal[$f->id]) && ! empty($arrOldVal[$f->id]) && $arrOldVal[$f->id] == $c ){
												$ischeckedopt ='selected';
											}
											?>
											<option value ='<?php  echo $c; ?>' <?php echo $ischeckedopt; ?>> <?php  echo $c; ?></option>
											<?php
										}
									?>
									</<?php echo $f->element?>>
									<?php } else if ($f->element == 'radio'){ ?>
										<?php
										$choices = explode(",",$f->choices);
										foreach($choices as $c){
											$ischecked = '';
											if (isset($arrOldVal[$f->id]) && ! empty($arrOldVal[$f->id]) && $arrOldVal[$f->id] == $c ){
												$ischecked ='checked';
											}
											?>
												<input class='form_bill_inputs' <?php echo $ischecked; ?> type='<?php echo $f->element; ?>' value='<?php echo $c; ?>' <?php echo ($f->is_required== 1) ? 'required' : ''; ?> id="<?php echo $f->id ?>" name="<?php echo $f->id ?>"> <?php echo $c; ?>
											<?php
										}
										?>
									<?php } ?>
									<span class="help-block text-danger" id='msg_error_<?php echo $f->id; ?>'></span>
							</div>
								<?php
								}
							?>
							<div class="form-group">
								<label for="form_convenience_fee">Convenience Fee</label>
								<input disabled type="text" value="<?php echo $fee; ?>" id='form_convenience_fee' class='form-control' placeholder='Convenience fee''>
							</div>
							<div class="form-group">
							<label for="form_grand_total">Total</label>
								<input disabled type="text" value="" id='form_grand_total' class='form-control' placeholder='Total'>
							</div>
							<div class="text-right">
							<button class='btn btn-default' id='btnSubmitRequest'>Submit</button>
							</div>
							</form>
					<?php
			} else {
				echo "<div class='alert alert-danger'>This bill type is not yet available.</div>";
			}
		}
	}

	function saveRequestBills(){
		$user = new User();
		$raw = Input::get('data');
		$biller_id = Input::get('biller_id');
		$decoded = json_decode($raw);

		$convinience_fee = getConvenienceFee();
		$profit = getProfit();
		$usdPV = getUSDPV();
		 $additionalDeduction = $convinience_fee - $profit;
		if($decoded){
			$amount = 0;
			$is_valid = true;
			foreach($decoded as $dec){
				$biller_data = new Biller_data_name($dec->id);
				if(strtolower($dec->label) == "amount"){
					$amount = $dec->value;
				}
			}
			if(!$amount){
				$is_valid = false;
			}
			if($is_valid){
				$bill_request = new Biller_request_data();
				$now = time();
				$orig_amount = $amount;
				$amount = $amount + $additionalDeduction;
				$bill_request->create(
					array(
						'company_id' => $user->data()->company_id,
						'is_active' => 1,
						'status' => 1,
						'created' => $now,
						'user_id' => $user->data()->id,
						'json_data' => $raw,
						'biller_id' => $biller_id
					)
				);
				$wallet = new Wallet();
				$wallet->updateUserWallet($user,$user->data()->id,$amount,1,"Deduct Wallet. Pay bills."); // deduct
				$wallet->updateUserWallet($user,$user->data()->id,$usdPV,0,"Add USD PV.",1); // add usd pv
				// insert to motherboard
				$walletFor = $wallet->getForPayBills();
				if(isset($walletFor->id)){
					$wallet->updateCompanyWallet($user,$walletFor->id,$orig_amount,"Add wallet from pay bills.");
				}
				echo "Request submitted successfully";
			} else {
				echo "Invalid form. Please contact the administrator.";
			}
		}
	}
	function pendingRequest(){
		$stats = Input::get('status');
		$stats = ($stats) ? $stats : 1;
		$user = new User();
		$user_type = (Input::get('user_type')) ? Input::get('user_type') : 0;
		if($user_type == 1){
			$user_id = $user->data()->id;
		} else {
		    $user_id = 0;
		}
		$biller_request = new Biller_request_data();
		$list = $biller_request->getRequest($user->data()->company_id,$stats,$user_id);
		if($list){
			echo "<div id='no-more-tables'>";
			echo "<table class='table'>";
			echo "<thead><tr><th>ID</th><th>Company</th><th>Request By</th><th>Created</th><th>Request Data</th><th></th></tr></thead>";
			echo "<tbody>";
			$stats_arr = ['','Pending','Process','Decline'];
			foreach($list as $l){
				$company_name = "";
				$user_name = "";
				if($l->biller_id){
					$company_name = $l->biller_name;
				}
				if($l->user_id){

					$user_name= capitalize($l->firstname .  " " .$l->lastname);

				}
				$data = json_decode($l->json_data);
				if($data){
					$list_group = "<ul class='list-group'>";
					foreach($data as $d){
					$list_group .= "<li class='list-group-item'>$d->label : <strong class='text-danger'>$d->value</strong></li>";
					}
					$list_group .= "</ul>";
				}
				echo "<tr>";
				echo "<td data-title='Id' style='border-top:1px solid #ccc;'><strong>$l->id</strong></td>";
				echo "<td data-title='Company'  style='border-top:1px solid #ccc;'>$company_name</td>";
				echo "<td data-title='User'  style='border-top:1px solid #ccc;'>$user_name</td>";
				echo "<td  data-title='Created'  style='border-top:1px solid #ccc;'>".date('m/d/Y H:i:s A',$l->created)."</td>";
				echo "<td data-title='Data'  style='border-top:1px solid #ccc;'>$list_group</td>";
				echo "<td  style='border-top:1px solid #ccc;'>";
				if($l->status == 1 && $user_id === 0 ){
					echo "<button class='btn btn-default btn-sm btnProcessRequest' data-id='$l->id'><i class='fa fa-check'></i></button>";
					echo " <button class='btn btn-danger btn-sm btnRemoveRequest' data-id='$l->id'><i class='fa fa-remove'></i></button>";
				} else {
					echo $stats_arr[$l->status];
				}
				echo "</td>";
				echo "</tr>";
			}
			echo "</tbody>";
			echo "</table>";
			echo "</div>";
		} else {
			echo "<div class='alert alert-info'>No record found.</div>";
		}
	}

	function sameForms(){
		$id = Input::get('id');
		$cur_id = Input::get('cur_id');
		if($id){
			$biller_data = new Biller_data_name();
			$data = $biller_data->get_active('biller_name_data',['biller_name_id','=',$id]);
			if($data){
				$biller = new Biller_data_name();
				$user = new User();
				foreach($data as $d){
					$biller->create(array(
							'biller_name_id' =>$cur_id,
							'element' =>  $d->element,
							'label' =>  $d->label,
							'data_type' =>  $d->data_type,
							'choices' =>  $d->choices,
							'is_required' =>  $d->is_required,
							'is_active' => 1,
							'company_id' => $user->data()->company_id
					));
				}
				echo "Request submitted successfully.";
			}
		}
	}

	function processRequest(){
		$id = Input::get('id');
		if($id && is_numeric($id)){
			$request = new Biller_request_data();
			$request->update(array('status'=>2),$id);
			echo "Processed successfully.";
		} else {
			echo "Request failed. Please try again.";
		}
	}
	function removeRequest(){
		$id = Input::get('id');
		if($id && is_numeric($id)){
			$request = new Biller_request_data();
			$request->update(array('status'=>3),$id);
			echo "Declined successfully.";
			// add to user wallet?
		} else {
			echo "Request failed. Please try again.";
		}
	}

	function getConvenienceFee(){
			$k_type = $_SESSION['k_type'];
			if($k_type == 1){ // distributor
				return $_SESSION['wallet_config']['paybills_convenience_fee_distributor'];
			} else if($k_type == 2){ // franchisee
				return $_SESSION['wallet_config']['paybills_convenience_fee_franchisee'];
			} else if($k_type == 3){ // agent
				return $_SESSION['wallet_config']['paybills_convenience_fee_agent'];
			}
			return false;

	}
	function getUSDPV(){
			$k_type = $_SESSION['k_type'];
			if($k_type == 1){ // distributor
				return $_SESSION['wallet_config']['paybills_usd_pv_distributor'];
			} else if($k_type == 2){ // franchisee
				return $_SESSION['wallet_config']['paybills_usd_pv_franchisee'];
			} else if($k_type == 3){ // agent
				return $_SESSION['wallet_config']['paybills_usd_pv_agent'];
			}
			return false;
	}
	function getProfit(){
			$k_type = $_SESSION['k_type'];
			if($k_type == 1){ // distributor
				return $_SESSION['wallet_config']['paybills_profit_franchisee'];
			} else if($k_type == 2){ // franchisee
				return $_SESSION['wallet_config']['paybills_usd_pv_franchisee'];
			} else if($k_type == 3){ // agent
				return $_SESSION['wallet_config']['paybills_profit_agent'];
			}
			return false;
	}