<?php
	class Wh_order_pending extends Crud{
		protected $_table = 'wh_order_pending';
		public function __construct($w=null){
			parent::__construct($w);
		}
		public function getPending($member_id = 0 , $branch_id = 0){
			$parameters = array();
			if($member_id && $branch_id){
				$parameters[] = $member_id;
				$parameters[] = $branch_id;
				$q= "Select w.*, i.item_code, i.description from wh_order_pending w left join items i on i.id = w.item_id where w.member_id = ? and w.branch_id = ? and w.status = 1";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function countRecord($cid,$search=''){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;

				if($search){
					$parameters[] = "%$search%";
					$likewhere = " and (m.lastname like ? ) ";
				} else {
					$likewhere='';
				}

				$q= "Select count(o.id) as cnt from wh_order_pending o left join members m on m.id = o.member_id left join items i on i.id = o.item_id where 1=1 and o.company_id=? $likewhere";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function get_record($cid,$start,$limit,$search=''){
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
					$likewhere = " and (m.lastname like ? ) ";
				} else {
					$likewhere='';
				}


				$q= "Select i.item_code,i.description,o.*,m.lastname as mln, m.firstname  as mfn,m.middlename  as mmn from wh_order_pending o left join items i on i.id = o.item_id left join members m on m.id=o.member_id  where 1=1 and o.company_id=? $likewhere order by o.id desc $l ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
	}
?>