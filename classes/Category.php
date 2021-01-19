<?php
	class Category extends Crud{
		protected $_table = 'categories';
		public function __construct($categ=null){
			parent::__construct($categ);
		}
		public function getAllChild($categ = 0){
			$parameters = array();
			if($categ){
				$parameters[] = $categ;
				$q = "SELECT t1.id AS lev1, t2.id as lev2, t3.id as lev3, t4.id as lev4 FROM categories AS t1 LEFT JOIN categories AS t2 ON t2.parent = t1.id LEFT JOIN categories AS t3 ON t3.parent = t2.id LEFT JOIN categories AS t4 ON t4.parent = t3.id WHERE t1.id =?";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function getNoparent($company_id = 0){
			$parameters = array();
			if($company_id){
				$parameters[] = $company_id;
				$q= 'Select id,name from categories where company_id=? and is_active=1 and parent=0';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function hasChild($company_id = 0,$id = 0){
			if($company_id && $id){
				$parameters[] = $company_id;
				$parameters[] = $id;
				$q= 'Select id from categories where company_id=? and is_active=1 and parent=?';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return true;
				}
				return false;
			}
		}
		public function getProductCategory($company_id = 0,$id = 0){
			if($company_id && $id){
				$parameters[] = $company_id;
				$parameters[] = $id;
				$q= 'Select name from categories where company_id=? and is_active=1 and id=?';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
				return false;
			}
		}
		public function getCategory($company_id = 0,$for_selling_only=false){
			if($company_id){
				$parameters[] = $company_id;
				if($for_selling_only){
					$whereExtra= "and for_selling = 1";
				} else {
					$whereExtra = "";
				}
				$q= "Select id,name,parent from categories where company_id=? and is_active=1 $whereExtra order by parent asc";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function getCategoryByName($company_id = 0,$name){
			if($company_id){
				$parameters[] = $company_id;
				$parameters[] = $name;
				$q= 'Select id,name,parent from categories where company_id=? and is_active=1 and name=? order by id desc limit 1';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
			}
		}

	}
?>