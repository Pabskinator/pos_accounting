<?php

	class Freight extends Crud {
		protected $_table = 'freight_charges';

		public function __construct($f = null) {
			parent::__construct($f);
		}

		public function paidFreight($id = 0) {
			$parameters = array();
			if($id) {
				$parameters[] = $id;

				$q = "update freight_charges set status = 1 , paid_amount = (charge + freight_adjustment) where payment_id = ? ";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return true;
				}

				return false;
			}


		}

		public function getPendingFreight($member_id = 0, $branch_id = 0) {
			$parameters = array();
			if($member_id) {
				$parameters[] = $member_id;
				$parameters[] = $branch_id;

				 $q = "
						Select f.*,o.id as wh_id, o.dr,o.invoice,o.pr, o.created, m.lastname as member_name
						from freight_charges f
						left join  wh_orders o on o.payment_id=f.payment_id
						left join members m on m.id = o.member_id
						where  o.member_id = ? and o.to_branch_id = ? and o.id is not null and o.status in (1,2,3,4)
					";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}

		public function get_record($cid, $start, $limit, $status = 0,$search='') {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$parameters[] = $status;


				if($limit) {
					$l = " LIMIT $start,$limit";
				} else {
					$l = '';
				}


				$whereSearch = "";
				if($search){
					$parameters[] = $search;
					$whereSearch = " and m.lastname like ? ";
				}

				$q = "Select f.*,o.id as wh_id, o.dr,o.invoice,o.pr, o.created, m.lastname as member_name
			from freight_charges f
			left join (select id,dr,invoice,pr,created,payment_id,member_id from wh_orders) o on o.payment_id=f.payment_id
			left join (select id,lastname from members) m on m.id = o.member_id
			where f.charge != 0 and  f.company_id = ? and f.status = ? and o.id is not null $whereSearch $l";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}

		public function countRecord($cid = 0, $status = 0,$search='') {
			$parameters = array();
			if($cid) {

				$parameters[] = $cid;
				$parameters[] = $status;
				$whereSearch = "";

				if($search){
					$parameters[] = $search;
					$whereSearch = " and m.lastname like ? ";
				}

				$q = "Select count(f.id) as cnt
						from freight_charges f
						left join (select id,dr,invoice,pr,created,payment_id,member_id from wh_orders) o on o.payment_id=f.payment_id
						left join (select id,lastname from members) m on m.id = o.member_id
						where f.charge != 0 and f.company_id = ? and f.status = ? and o.id is not null  $whereSearch";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
	}

?>