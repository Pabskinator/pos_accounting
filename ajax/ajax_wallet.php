<?php
	include 'ajax_connection.php';
	$functionName = Input::get("functionName");
	$functionName();
	function getCurrentWalletUser(){
		$wallet = new Wallet();
		$user = new User();
		$myWallet = $wallet->myWallet(2,$user->data()->id);
		$arr=[];
		if($myWallet){
			$arr = ['wallet' => $myWallet->amount,'id'=>$myWallet->id];
		}
		echo json_encode($arr);
	}
	function getCompanyWallet(){
		$wallet = new Wallet();
		$user = new User();
		$wallets = $wallet->getCompanyWallet(1,$user->data()->company_id);
		$arr = [];
		if($wallets){
			foreach($wallets as $w){
				$arr[] = $w;
			}
		}
		echo json_encode($arr);
	}
	function getUserWallet(){
		$wallet = new Wallet();
		$user = new User();
		$wallets = $wallet->getUserWallet(2,$user->data()->company_id);
		$arr = [];
		if($wallets){
			foreach($wallets as $w){
				$w->fullName = capitalize($w->firstname . " " . $w->lastname);
				$arr[] = $w;
			}
		}
		echo json_encode($arr);
	}
	function sendRequest(){
		$request = json_decode(Input::get('data'));

		if($request){
			if($request->amount && is_numeric($request->amount)){
				$wallet = new Wallet_request();
				$user = new User();
				$wallet->create(
					array(
						'user_id' => $user->data()->id,
						'remarks' => $request->remarks,
						'amount' => $request->amount,
						'company_id' => $user->data()->company_id,
						'is_active' => 1,
						'status' => 1,
						'created' => time()
					)
				);
				$arr = ['success' => true,'message'=>"Request was sent successfully."];
			} else {
				$arr = ['success' => false,'message'=>"Invalid amount"];
			}
		} else {
			$arr = ['success' => false,'message'=>"Invalid request"];
		}
		echo json_encode($arr);
	}

	function getRequest(){
		$wallet = new Wallet_request();
		$user = new User();
		$user_id = $user->data()->id;
		$status = 1;
		$permission = $user->hasPermission('wallet_manage');
		if($permission){
			$user_id = 0;
		}
		$wallet_request = $wallet->getRequest($user->data()->company_id,$user_id,$status);
		$arr = [];
		$status = ['','Pending','Processed','History'];
		$payment_arr = ['','Cash','Credit Card','Cheque','Bank Deposit','Paypal'];
		if($wallet_request){
			foreach($wallet_request as $item){
				$item->fullName = capitalize($item->firstname .  " " . $item->lastname);
				$item->branchName = $item->branch_name;
				$item->amount_formatted =number_format($item->amount,2);
				$item->current_status = $status[$item->status];
				$item->action=0;
				$item->payment_method_label=$payment_arr[$item->payment_method];
				if($permission){
					$item->action = 1;
				}
				$arr[]=$item;
			}
		}

		echo json_encode($arr);
	}
	function processRequest(){
		$id = Input::get('request_id');
		if($id && is_numeric($id)){
			$wallet_request = new Wallet_request($id);
			$wallet = new Wallet();
			$user = new User();

			$amount_to_add = $wallet_request->data()->amount;
			$user_id = $wallet_request->data()->user_id;

			if($wallet_request->data()->status == 1){
				$wallet->updateUserWallet($user,$user_id,$amount_to_add,0,"Add wallet from Request Id # " .$id);
				$wallet_request->update(array('status'=>2),$id);

				$walletFor = $wallet->getDeductLoad();
				if(isset($walletFor->id)){
						$wallet->updateCompanyWallet($user,$walletFor->id,$amount_to_add,"Deduct wallet from Request Id #".$id,1);
				}
				echo "Processed successfully.";
			}
		}

	}
	function cancelRequest(){
		$id = Input::get('request_id');
		if($id && is_numeric($id)){
			$wallet_request = new Wallet_request($id);
			if($wallet_request->data()->status == 1){
				$wallet_request->update(array('status'=>6),$id);
				echo "Cancelled successfully.";
			}
		}
	}
	function saveWalletUser(){
		$data = json_decode(Input::get('data'));
		if($data){
			$wallet = new Wallet();
			$user = new User();
			// history ?
			if($data->id){
				// update
				if($data->amount){
					$wallet->updateUserWallet($user,$data->user_id, $data->amount,0,'Update in admin page',0);
				}
				if($data->usd_pv){
					$wallet->updateUserWallet($user,$data->user_id, $data->usd_pv,0,'Update in admin page',1);
				}
				if($data->binary_pv){
					$wallet->updateUserWallet($user,$data->user_id, $data->binary_pv,0,'Update in admin page',2);
				}
				if($data->uni_level_pv){
					$wallet->updateUserWallet($user,$data->user_id, $data->uni_level_pv,0,'Update in admin page',3);
				}

				/*$wallet->update(array(
					'user_id' => $data->user_id,
					'amount' => $data->amount
				),$data->id);*/
			} else {
				// insert

				if($data->amount){
					$wallet->updateUserWallet($user,$data->user_id, $data->amount,0,'Update in admin page',0);
				}
				if($data->usd_pv){
					$wallet->updateUserWallet($user,$data->user_id, $data->usd_pv,0,'Update in admin page',1);
				}
				if($data->binary_pv){
					$wallet->updateUserWallet($user,$data->user_id, $data->binary_pv,0,'Update in admin page',2);
				}
				if($data->uni_level_pv){
					$wallet->updateUserWallet($user,$data->user_id, $data->uni_level_pv,0,'Update in admin page',3);
				}
				/*
				$wallet->create(array(
					'user_id' => $data->user_id,
					'amount' => $data->amount,
					'type' => 2,
					'created_by' => $user->data()->id,
					'company_id' => $user->data()->company_id,
					'is_active' =>1,
					'created' =>time()
				));*/
			}
		}
		echo json_encode(['success'=> true,'message'=>"Processed successfully."]);
	}
	function saveWallet(){
		$data = json_decode(Input::get('data'));
		if($data){
			$wallet = new Wallet();
			$user = new User();
			$add_affiliate = 0;
			$deduct_orders = 0;
			$deduct_load= 0;
			$add_paybills= 0;
			if($data->add_affiliate){
				// toggle all company affiliate
				$wallet->updateAffiliateStatus(0);
				$add_affiliate = 1;
			}
			if($data->deduct_load){
				// toggle all company deduct_orders
				$wallet->updateLoadStatus(0);
				$deduct_load = 1;
			}
			if($data->deduct_orders){
				// toggle all company deduct_load
				$wallet->updateOrderStatus(0);
				$deduct_orders = 1;
			}
			if($data->add_paybills){
				// toggle all company deduct_load
				$wallet->updatePayBills(0);
				$add_paybills = 1;
			}

			if($data->id){
				// update
				$wallet->update(array(
					'label' => $data->label,
					'amount' => $data->amount,
					'add_affiliate' => $add_affiliate,
					'deduct_load' => $deduct_load,
					'deduct_orders' => $deduct_orders,
					'add_paybills' => $add_paybills,
				),$data->id);
			} else {
				// insert
				$wallet->create(array(
					'label' => $data->label,
					'amount' => $data->amount,
					'type' => 1,
					'created_by' => $user->data()->id,
					'company_id' => $user->data()->company_id,
					'is_active' =>1,
					'add_affiliate' => $add_affiliate,
					'deduct_load' => $deduct_load,
					'deduct_orders' => $deduct_orders,
					'add_paybills' => $add_paybills,
					'created' =>time()
				));
			}
		}
		echo json_encode(['success'=> true,'message'=>"Processed successfully."]);
	}
	function saveConfig(){
		$request = Input::get('request');
		$request = json_decode($request);
		$wallet_config = new Wallet_config();
		$wallet_config->update(['value'=>$request->value],$request->id);
		echo json_encode(['success' => true,'msg'=>"Updated successfully"]);
	}
	function getWalletConfigurations(){
		$config = new Wallet_config();
		$configs = $config->get_active('wallet_configuration',['company_id','=','1']);
		$arr = [];
		foreach($configs as $c){
			$c->is_edit = 0;
			$c->value_old = $c->value;
			$label = str_replace('_',' ',$c->key);
			$label = capitalize($label);
			$c->label = $label;
			$arr[]= $c;
		}
		echo json_encode($arr);
	}
	function deleteUserWallet(){
		$id = Input::get('id');
		$wallet = new Wallet();
		if($id && is_numeric($id)){
			$wallet->update(['is_active'=>0],$id);
			echo json_encode(['success'=> true,'message'=>"Deleted successfully."]);
		} else {
			echo json_encode(['success'=> false,'message'=>"Invalid request."]);
		}
	}
	function deleteWallet(){
		$id = Input::get('id');
		$wallet = new Wallet();
		if($id && is_numeric($id)){
			$wallet->update(['is_active'=>0],$id);
			echo json_encode(['success'=> true,'message'=>"Deleted successfully."]);
		} else {
			echo json_encode(['success'=> false,'message'=>"Invalid request."]);
		}
	}
	function getHistory(){
		$user = new User();
		$wallet = new Wallet();
		$search = Input::get('search');
		$histories = $wallet->get_history($user->data()->company_id,$search);
		$arr = [];
		if($histories){
			foreach($histories as $his){
				$his->fullName = capitalize($his->firstname . " " . $his->lastname);
				$his->date_created = date('F d, Y H:i:s A',$his->created);
				$arr[] = $his;
			}
		}
		echo json_encode($arr);
	}