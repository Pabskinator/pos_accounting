<?php
	class Doc_color extends Crud{
		protected $_table = 'doc_colors';
		public function __construct($d=null){
			parent::__construct($d);
		}
		public function countRecord($cid,$search=''){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$likewhere = '';
				if($search) {

				}


				$q = "Select count(id) as cnt from doc_colors where company_id=?  $likewhere";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function get_doc_record($cid,$start,$limit,$search=''){
			$parameters = array();
			if($cid){
				$parameters[] = $cid;
				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}
				if($search){
					$likewhere='';
				} else {
					$likewhere='';
				}


				$q= "Select * from doc_colors where company_id=? and is_active=1 $likewhere order by doc_type $l  ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function getDocs($cid = 0, $type = 0){
			$parameters = array();
			if($cid && $type) {
				$parameters[] = $cid;
				$parameters[] = $type;

				$q = "Select * from doc_colors where company_id=?  and doc_type=?";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}
	}
