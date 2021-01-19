<?php
	class Terminal extends Crud{
		protected $_table='terminals';
		public function __construct($terminal = NULL){
			parent::__construct($terminal);
		}
		public function getTerminalData($id){
			$parameters = array();
			if($id){
				$parameters[] = $id;
				$q= 'Select t.*, b.name as branch_name, b.member_id from terminals t left join branches b on b.id = t.branch_id where t.id=?';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function getInvoice($id){
			$parameters = array();
			if($id){
				$parameters[] = $id;
				$q= 'Select * from terminals where id=?';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function getTAmount($id,$type){
			$parameters = array();
			if($id){
				$parameters[] = $id;
				if($type == 1){
					$col = 't_amount';
				} else if($type == 2){
					$col = 't_amount_cc';
				}else if($type == 3){
					$col = 't_amount_ch';
				}else if($type == 4){
					$col = 't_amount_bt';
				}
				$q= 'Select '.$col.' from terminals where id=?';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function getUnassignTerminal($branch_id = 0){
			$parameters = array();
			if($branch_id){
				$parameters[] = $branch_id;
				$q= 'Select * from terminals where branch_id=? and is_assigned=0';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function getAllTerminal($branch_id = 0){
			$parameters = array();
			if($branch_id){
				$parameters[] = $branch_id;
				$q= 'Select * from terminals where branch_id=? ';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function countTerminal($companyid=0){
			$parameters = array();
			if($companyid){
				$parameters[] =$companyid;
				$q= 'Select count(t.id) as cnt from terminals t left join branches b on b.id = t.branch_id where  t.is_active=1 and b.company_id=?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->first();
				}
			}
		}

		public function getCashier($t=0){
			$parameters[] =$t;
			$q= 'Select * from sales where terminal_id = ? order by id desc limit 1';
			$e = $this->_db->query($q, $parameters);
			if($e->count()){
				return $e->first();
			}
		}


	}
?>