<?php
	class Pettycash_holder extends Crud{
		protected $_table = 'pettycash_holder';
		public function __construct($p=null){
			parent::__construct($p);
		}
		public function getHolder($cid,$branch_id=0){
			$parameters = [];
			if($cid){
				$parameters[] = $cid;
				$whereBranch = '';
				if($branch_id){
					$parameters[] = $branch_id;
					$whereBranch = " and p.branch_id=?";
					$method = "first";
				} else {
					$method = "results";
				}
				$q= "Select p.*, b.name as branch_name, u.lastname,u.firstname,u.middlename from pettycash_holder p left join branches b on b.id=p.branch_id left join users u on u.id=p.user_id where p.company_id=? $whereBranch";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->$method();
				}
			}
		}
		public function deductPettycash($cid=0,$branch_id=0,$amount=0,$user_id=0,$desc){
			$parameters = [];
			if($branch_id && $amount){
				$current_data = $this->getHolder($cid,$branch_id);
				$remainingAmount = $current_data->amount - $amount;
				$parameters[]=$remainingAmount;
				$parameters[]=$branch_id;
				// insert log
				$newLog = new Pettycash_log();
				$now = time();

				$newLog->create(array(
					'branch_id' => $branch_id,
					'company_id' => $cid,
					'is_active' => 1,
					'user_id' => $user_id,
					'prev_amount' => $current_data->amount,
					'amount' => $amount,
					'new_amount' => $remainingAmount,
					'created' => $now,
					'modified' => $now,
					'remarks' => $desc
				));
				// update
				$q= "update pettycash_holder set amount = ? where branch_id=?";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return true;
				}
			}
		}
		public function addPettycash($cid=0,$branch_id=0,$amount=0,$user_id=0,$desc){
			$parameters = [];
			if($branch_id && $amount){
				$current_data = $this->getHolder($cid,$branch_id);
				$remainingAmount = $current_data->amount + $amount;
				$parameters[]=$remainingAmount;
				$parameters[]=$branch_id;
				// insert log
				$newLog = new Pettycash_log();
				$now = time();

				$newLog->create(array(
					'branch_id' => $branch_id,
					'company_id' => $cid,
					'is_active' => 1,
					'user_id' => $user_id,
					'prev_amount' => $current_data->amount,
					'amount' => $amount,
					'new_amount' => $remainingAmount,
					'created' => $now,
					'modified' => $now,
					'remarks' => $desc
				));
				// update
				$q= "update pettycash_holder set amount = ? where branch_id=?";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return true;
				}
			}
		}


	}
?>