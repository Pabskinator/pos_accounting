<?php
	class Technician extends Crud{
		protected $_table = 'technicians';
		public function __construct($t=null){
			parent::__construct($t);
		}
		public function techJSON($cid = 0 , $search = ''){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$whereSearch = '';
				if($search){
					$parameters[] = "%$search%";
					$whereSearch = " and (name like ? )";
				}

				$q = "Select * from technicians where is_active = 1 and company_id = ? $whereSearch";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function getTech($ids = 0){
			if($ids) {
				$parameters[] = $ids;


				$q = "Select * from technicians where is_active = 1 and id in ($ids)";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

	}
?>