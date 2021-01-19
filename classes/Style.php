<?php
	class Style extends Crud{
		protected $_table='styles';
		public function __construct($s = NULL){
			parent::__construct($s);
		}
		public function getActivatedStyle($companyid=0){
			$parameters = array();
			if($companyid){
				$parameters[] =$companyid;
				$q= 'Select * from styles  where  is_active=1 and is_set = 1 and company_id=?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->first();
				}
			}
		}
		public function unsetTheme($companyid=0){
			$parameters = array();
			if($companyid){
				$parameters[] =$companyid;
				$q= 'update styles set is_set=0 where company_id=?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return true;
				}
			}
		}
		public function setTheme($id=0){
			$parameters = array();
			if($id){
				$parameters[] =$id;
				$q= 'update styles set is_set=1 where id=?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return true;
				}
			}
		}

	}
?>