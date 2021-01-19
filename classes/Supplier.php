<?php
	class Supplier extends Crud{
		protected $_table = 'suppliers';
		public function __construct($branch=null){
			parent::__construct($branch);
		}

		public function isSupplierExist($name='',$companyid=0,$getid=false){
			$parameters = array();
			if($name){
				$parameters[] = $name;
				$parameters[] =$companyid;
				$q= 'Select id from suppliers  where  name=? and is_active=1 and company_id=?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return ($getid) ? $e->first() : true;
				}
				return false;
			}
		}
		public function countSupplier($companyid=0){
			$parameters = array();
			if($companyid){
				$parameters[] =$companyid;
				$q= 'Select count(id) as cnt from suppliers  where  is_active=1 and company_id=?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->first();
				}
			}
		}
		public function getSuppliers($companyid){
			$parameters = array();
			if($companyid){
				$parameters[] =$companyid;
				$q= 'Select * from suppliers  where  is_active=1 and company_id=?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
			}
		}

	}
?>