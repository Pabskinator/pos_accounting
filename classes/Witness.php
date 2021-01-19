<?php
	class Witness extends Crud{
		protected $_table = 'witnesses';
		public function __construct($w=null){
			parent::__construct($w);
		}
		public function countRecord($cid,$search=''){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;

				if($search) {
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$likewhere = " and (lastname like ? or firstname like ? or middlename like ? ) ";
				} else {
					$likewhere = "";
				}


				$q = "Select count(id) as cnt from witnesses where company_id=?  $likewhere";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function get_witness_record($cid,$start,$limit,$search=''){
			$parameters = array();
			if($cid){
				$parameters[] = $cid;
				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}
				if($search){
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$likewhere = " and (lastname like ? or firstname like ? or middlename like ? ) ";
				} else {
					$likewhere='';
				}


				$q= "Select * from witnesses  where company_id=? and is_active=1 $likewhere $l  ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
	}
?>