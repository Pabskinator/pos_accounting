<?php
	class Custom_field extends Crud{
		protected $_table = 'custom_form';
		public function __construct($c=null){
			parent::__construct($c);
		}
		public function isExistsTable($name='',$company_id=0){
			$parameters = array();
			if($name && $company_id){
				$parameters[] = $name;
				$parameters[] =$company_id;
				$q= 'Select id from custom_form  where  table_name=? and is_active=1 and company_id=?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return true;
				}
				return false;
			}
		}
		public function getcustomform($tbl,$company_id){
			$parameters = array();
			if($tbl && $company_id){
				$parameters[] = $tbl;
				$parameters[] =$company_id;
				 $q= 'Select * from custom_form  where  `table_name`=? and is_active=1 and company_id=?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->first();
				}
				return false;
			}
		}
		public function getIdCustom($name='',$company_id=0){
			$parameters = array();
			if($name && $company_id){
				$parameters[] = $name;
				$parameters[] =$company_id;
				$q= 'Select id from custom_form  where  table_name=? and is_active=1 and company_id=?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->first();
				}
				return false;
			}
		}
	}
?>