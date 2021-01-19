<?php
	class Collection_report extends Crud{
		protected $_table = 'collection_reports';
		public function __construct($b=null){
			parent::__construct($b);
		}

		public function getData($cr_num){

				$parameters = array();
				$cr_num = trim(addslashes($cr_num));
				$parameters[] = $cr_num;
				$q= "Select * from collection_reports where ref_id = ? ";
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->first();
				}
				return false;

		}

		public function deleteCr($cr_num){

			$parameters = array();
			if($cr_num){
				$cr_num = trim(addslashes($cr_num));
				$parameters[] = $cr_num;
				$q= "Delete from collection_reports where ref_id = ? ";
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return true;
				}
			}
			return false;
		}

	}
?>