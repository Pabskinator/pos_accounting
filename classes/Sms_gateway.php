<?php
	class Sms_gateway extends Crud{
		protected $_table = 'sms_gateway';
		public function __construct($s=null) {
			parent::__construct($s);
		}
		public function getItems($id,$whereF ='') {
			$parameters = array();
			if($id){
				$parameters[] = $id;
				$exploded = explode(",",$whereF);
				$lib = "";
				foreach($exploded as $ex){
					$ex = (int) $ex;
					$lib .= $ex.",";

				}
				$lib = rtrim($lib,",");

				$q= "Select * from items where is_active = 1 and company_id=? and is_franchisee_product in ($lib) ";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function  getBranchByNumber($num) {
			$parameters = array();
			if($num){
				$parameters[] = $num;
				 $q= 'Select s.*, b.id as branch_id ,b.member_id, b.name as branch_name from sms_gateway s left join terminals t on t.id = s.terminal_id left join branches b on b.id = t.branch_id where s.mobile_number = ? limit 1';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function  getDataByNumber($num) {
			$parameters = array();
			if($num){
				$parameters[] = $num;
				$q= 'Select s.* from sms_gateway s where s.mobile_number = ? limit 1';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
			}
		}
	}