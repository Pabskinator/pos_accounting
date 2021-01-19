<?php
	class Body_measurement extends Crud{
		protected $_table = 'body_measurements';
		public function __construct($b=null){
			parent::__construct($b);
		}
		public function countRecord($cid,$member_id = 0){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				if($member_id){
					$parameters[] = $member_id;
					$wheremember= " and b.member_id=?";
				} else {
					$wheremember= "";
				}

				$q = "Select count(b.id) as cnt from body_measurements b left join members  m  on m.id = b.member_id where b.company_id=?  $wheremember";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function get_record($cid,$start,$limit,$member_id){
			$parameters = array();
			if($cid){
				$parameters[] = $cid;
				if($member_id){
					$parameters[] = $member_id;
					$wheremember= " and b.member_id=?";
				} else {
					$wheremember= "";
				}

				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}

				$q= "Select  m.lastname as member_name, b.*  from body_measurements b left join members m on m.id=b.member_id  where b.company_id=? $wheremember  $l";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
	}