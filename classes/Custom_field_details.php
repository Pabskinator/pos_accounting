<?php
	class Custom_field_details extends Crud{
		protected $_table = 'custom_form_details';
		public function __construct($c=null){
			parent::__construct($c);
		}
		public function isExistsDet($name='',$company_id=0,$getid=0){
			$parameters = array();
			if($name && $company_id){
				$parameters[] = $name;
				$parameters[] =$company_id;
				$parameters[] =$getid;
				$q= 'Select id from custom_form_details  where  field_name=? and is_active=1 and company_id=? and cf_id=?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return true;
				}
				return false;
			}
		}
		public function updateDet($name='',$label,$is_visble,$cf_id,$company_id=0){
			$parameters = array();
			if($name && $company_id  && $cf_id){


				$parameters[] = $label;
				$parameters[] = $is_visble;
				$parameters[] = $name;
				$parameters[] = $cf_id;
				$parameters[] =$company_id;

				 $q= 'update custom_form_details set  field_label=?,is_visible=? where field_name=? and cf_id=? and is_active=1 and company_id=?';
			//	 echo $q= "update custom_form_details set  field_label=$label,is_visible=$is_visble where field_name=$name and cf_id=$cf_id and is_active=1 and company_id=$co";

				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return true;
				}
				return false;
			}
		}
		public function getFormDetails($id){
			$parameters = array();
			if( $id){
				$parameters[] = $id;
				$q= 'Select * from custom_form_details  where  cf_id=?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
				return false;
			}
		}

		public function getIndData($name , $company_id,$id=0){
			$parameters = array();
			if($name && $company_id){
				$parameters[] = $name;
				$parameters[] = $company_id;
				$parameters[] = $id;
				$q= 'Select * from custom_form_details  where  field_name=? and company_id=? and cf_id=?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->first();
				}
				return false;
			}
		}
		public function getAllData($company_id,$id=0){
			$parameters = array();
			if($company_id){

				$parameters[] = $company_id;
				$parameters[] = $id;
				$q= 'Select * from custom_form_details  where company_id=? and cf_id=?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
				return false;
			}
		}
	}
?>