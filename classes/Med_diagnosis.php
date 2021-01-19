<?php
	class Med_diagnosis extends Crud{
		protected $_table='med_diagnosis';
		public function __construct($m = NULL){
			parent::__construct($m);
		}
		public function countRecord($cid, $search = '',$m = 0,$type = 1) {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				if($search) {

					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$likewhere = " and (m.lastname like ? or m.firstname like ? ) ";

				} else {
					$likewhere = "";
				}
				if($m) {
					$parameters[] = $m;
					$memberWhere  = " and d.member_id = ? ";
				} else {
					$memberWhere  = "";
				}
				if($type == 1){
					$wheretype = " and d.doctor_id != 0 ";
				} else if ($type == 2){
					$wheretype = " and d.nurse_id != 0 ";
				}
				 $q = "Select count(d.id) as cnt from med_diagnosis d left join members m  on m.id = d.member_id  where d.company_id=? and d.is_active=1 $likewhere $memberWhere  $wheretype";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function get_record($cid, $start, $limit, $search = '',$m=0,$type = 1) {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				if($limit) {
					$l = " LIMIT $start,$limit";
				} else {
					$l = '';
				}
				if($search) {

					$parameters[] = "%$search%";
					$parameters[] = "%$search%";

					$likewhere = " and (m.lastname like ? or m.firstname like ? ) ";
				} else {
					$likewhere = "";
				}
				if($m) {
					$parameters[] = $m;
					$memberWhere  = " and d.member_id = ? ";
				} else {
					$memberWhere  = "";
				}
				if($type == 1){
					$wheretype = " and d.doctor_id != 0 ";
				} else if ($type == 2){
					$wheretype = " and d.nurse_id != 0 ";
				} else {
					$wheretype = "";
				}

				 $q = "Select d.* ,n.name as nurse_name, m.lastname, m.firstname, m.middlename, med.name as doctor_name from med_diagnosis d left join members m  on m.id = d.member_id left join med_doctors med on med.id=d.doctor_id  left join med_nurses n on n.id=d.nurse_id  where d.company_id=? and d.is_active=1 $likewhere $memberWhere $wheretype order by d.id desc $l";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}
	}
?>