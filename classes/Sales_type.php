<?php
	class Sales_type extends Crud{
		protected $_table = 'salestypes';
		public function __construct($s=null){
			parent::__construct($s);
		}

		public function isTypeExist($name='',$companyid=0,$getid=false){
			$parameters = array();
			if($name){
				$parameters[] = $name;
				$parameters[] =$companyid;
				$q= 'Select id from salestypes  where  name=? and is_active=1 and company_id=?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return ($getid) ? $e->first() : true;
				}
				return false;
			}
		}

		public function salesTypeDefault($c=0){
			if($c){
				$parameters = array();
				$parameters[] = $c;
				$q = 'Update salestypes set is_default=0 where company_id=?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return  true;
				}
				return false;
			}
		}

		public function getMySalesType($id=0){
			if($id){
				$parameters = array();
				$parameters[] = "%,$id,%";
				$q = "Select * from salestypes where  CONCAT( ',', user_id, ',' ) LIKE ? and is_active=1";
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
				return false;
			}
		}

		public function getSalesType(){
			$parameters = array();
			$parameters[] = 1;
			$q= "Select * from salestypes where 1 = ? order by name asc ";
			$data = $this->_db->query($q, $parameters);
			// return results if there is any
			if($data->count()){
				return $data->results();
			}

		}
	}
?>