<?php
	class Station extends Crud{
		protected $_table = 'stations';
		public function __construct($s=null){
			parent::__construct($s);
		}
		public function getAllStation($c=0){
			if($c){
				$parameters = array();
				$parameters[] = $c;
				$q = "Select s.*,m.lastname as mln,m.firstname as mfn from stations s left join members m on s.member_id=m.id where s.company_id=? and s.is_active=1 order by s.name asc";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function getStationByMember($m=0){
			if($m){
				$parameters = array();
				$parameters[] = $m;
				$q = "Select * from stations where member_id=? and is_active=1";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function getMemberId($s=0){
			if($s){
				$parameters = array();
				$parameters[] = $s;
				$q = "Select member_id from stations where id=? and is_active=1";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function countRecord($cid,$search=''){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$where='';
				if($search){
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$where = " and (s.name like ? or CONCAT(m.lastname, ', ', m.firstname) like ?)";
				}
				$q = "Select count(s.id) as cnt from stations s left join members m on s.member_id = m.id where s.company_id=? and s.is_active=1 $where order by s.member_id ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function get_sales_record($cid,$start,$limit,$search=''){
			$parameters = array();
			if($cid){
				$parameters[] = $cid;
				$where='';
				if($search){
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$where = " and (s.name like ? or CONCAT(m.lastname, ', ', m.firstname) like ?)";
				}
				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}

				$q= "Select s.*, m.lastname,m.firstname from stations s left join members m on s.member_id = m.id where s.company_id=? and s.is_active=1 $where order by s.member_id $l";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}

	}
?>