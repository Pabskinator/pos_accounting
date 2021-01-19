<?php
	class Member_price_group  extends  Crud {
		protected $_table = 'member_price_groups';
		public function __construct($e=null){
			parent::__construct($e);
		}
		public function getMemberPriceGroups($pid= 0){
			$parameters = array();
			$cid = 1;
			if($cid) {
				$parameters[] = $cid;
				$wherePID = "";
				if($pid){
					$parameters[] = $pid;
					$wherePID = " and g.id = ?";
				}
				$q = "Select p.*, m.lastname as member_name,g.name as group_name
			from member_price_groups p
			left join members m on m.id = p.member_id left join price_groups g on g.id = p.price_group_id where p.company_id=? $wherePID and p.is_active = 1";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function getPriceGroup($member_id = 0){
			$parameters = array();

			if($member_id) {
				$parameters[] = $member_id;

				$q = "Select p.price_group_id from member_price_groups p where p.member_id=? and p.is_active = 1";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
	}