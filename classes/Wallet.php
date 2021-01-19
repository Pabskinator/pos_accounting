<?php
	class Wallet extends Crud{
		protected $_table = 'wallets';
		public function __construct($w=null){
			parent::__construct($w);
		}
		public function myWallet($type=0,$id=0,$status=0){
			if($type && $id){
				$parameters[] = $id;
				$parameters[] = $type;

				$q= 'Select * from wallets where user_id=? and `type`=? and status = 0 and is_active = 1';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function getCompanyWallet($type=0,$cid=0){
			if($type && $cid){
				$parameters[] = $cid;
				$parameters[] = $type;
				$q= 'Select * from wallets where `company_id` = ? and `type`=? and is_active = 1';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function updateAffiliateStatus($status=0){
				$parameters[] = $status;
				$q= 'Update wallets set add_affiliate = ? where `type` = 1';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return true;
				}
		}
		public function updateLoadStatus($status=0){
				$parameters[] = $status;
				$q= 'Update wallets set deduct_load = ? where `type` = 1';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return true;
				}
		}
		public function updatePayBills($status=0){
				$parameters[] = $status;
				$q= 'Update wallets set add_paybills = ? where `type` = 1';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return true;
				}
		}
		public function updateOrderStatus($status=0){
				$parameters[] = $status;
				$q= 'Update wallets set deduct_orders = ? where `type` = 1';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return true;
				}
		}
		public function getForAffilate(){
				$parameters=[];
				$q= 'Select id from wallets where type = 1 and add_affiliate = 1 limit 1';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
		}
		public function getDeductLoad(){
				$parameters=[];
				$q= 'Select id from wallets where type = 1 and deduct_load = 1 limit 1';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
		}
		public function getDeductOrders(){
			$parameters=[];
			$q= 'Select id from wallets where type = 1 and deduct_orders = 1 limit 1';
			$data = $this->_db->query($q, $parameters);
			if($data->count()){
				// return the data if exists
				return $data->first();
			}
		}
		public function getForPayBills(){
				$parameters=[];
				$q= 'Select id from wallets where type = 1 and add_paybills = 1 limit 1';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
		}
		public function getUserWallet($type=0,$cid=0){
			if($type && $cid){
				$parameters[] = $cid;
				$parameters[] = $type;
				$q= 'Select w.*, u.lastname, u.firstname,u.middlename from wallets w left join users u on u.id = w.user_id where w.company_id = ? and w.type=? and w.is_active = 1';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function addHistory(){
			//INSERT INTO `wallet_history`(`user_id`, `from_amount`, `to_amount`, `company_id`, `created`, `is_active`, `remarks`)
			// VALUES ()

		}
		public function updateCompanyWallet(User $user,$id=0,$amount,$remarks,$deductType = 0){
			$wallet = new Wallet($id);
			if($wallet->data()){
				$parameters = [];
				$user_id = $user->data()->id;
				$company_id = $user->data()->company_id;
				$now = time();
				$cur_wallet = $wallet->data()->amount;
				if($deductType == 1){
					$to_amount = $wallet->data()->amount - $amount;
					$q = "update wallets set amount = amount - $amount where id = $id;";
				} else {
					$to_amount = $wallet->data()->amount + $amount;
					$q = "update wallets set amount = amount + $amount where id = $id;";
				}

				$q .= "INSERT INTO `wallet_history`(`user_id`, `from_amount`, `to_amount`, `company_id`, `created`, `is_active`, `remarks`,`type`,`company_wallet_id`)
							VALUES($user_id,$cur_wallet,$to_amount,$company_id,$now,1,'$remarks',1,$id);";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return true;
				}
			}
		}
		public function updateUserWallet(User $user,$user_id=0,$amount,$updateType=0,$remarks,$status=0){
			// updateType 0 = increment existing, 1= decrement
			// status  0 = e wallet , 1 = usd_pv , 2 = binary pv , 3 = uni level
			$type = 2;
			$amount = (float) $amount;
			$myWallet = $this->myWallet(2,$user_id,$status);
			$company_id =(int) $user->data()->company_id;
			$created_by =(int) $user->data()->id;
			$now = time();
			$parameters = [];
			if($myWallet){
				// update
				$updateCol = 'amount';
				if($status == 1){
					$updateCol ='usd_pv';
				} else if ($status == 2){
					$updateCol ='binary_pv';
				} else if ($status == 3){
					$updateCol ='uni_level_pv';
				}
				$operation = "+";
				$cur_wallet = $myWallet->$updateCol;
				if($updateType == 1){
					$operation = "-";
					$to_amount = $myWallet->$updateCol - $amount;
				} else {
					$to_amount = $myWallet->$updateCol + $amount;
				}

				$q = "update wallets set $updateCol = $updateCol $operation $amount where user_id = $user_id and `type` = $type;";
				$q .= "INSERT INTO `wallet_history`(`user_id`, `from_amount`, `to_amount`, `company_id`, `created`, `is_active`, `remarks`,`type`,`status`)
							VALUES($user_id,$cur_wallet,$to_amount,$company_id,$now,1,'$remarks',$type,$status);";

			} else {
				// insert
				$cur_wallet = 0;
				$to_amount = $amount;
				$q = "INSERT INTO `wallets`(`type`, `user_id`, `amount`, `company_id`, `is_active`, `created`, `created_by`)
						VALUES ($type,$user_id,$amount,$company_id,1,$now,$created_by);";
				$q .= "INSERT INTO `wallet_history`(`user_id`, `from_amount`, `to_amount`, `company_id`, `created`, `is_active`, `remarks`,`status`)
							VALUES($user_id,$cur_wallet,$to_amount,$company_id,$now,1,'$remarks','$status');";
			}
			$data = $this->_db->query($q, $parameters);
			if($data->count()){
				// return the data if exists
				return true;
			}
		}

		public function get_history($cid=1,$search=''){
			if($cid ){
				$parameters[] = $cid;
				$whereUser = "";
				$search = trim($search);
				if($search){
					$parameters[] = "%$search%";
					$whereUser = " and CONCAT(u.firstname, ' ' , u.lastname) like ? ";
				}
				$q="Select w.*, u.lastname,u.firstname,u.middlename from wallet_history w left join users u on u.id =w.user_id where w.company_id = ? $whereUser order by w.id desc limit 1000 ";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}
	}