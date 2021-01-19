<?php
	class Notification extends Crud{
		protected $_table = 'notified';
		public function __construct($n=null){
			parent::__construct($n);
		}
		public function countRecord($company_id,$position_id,$user_id,$search='',$m=0){
			if($position_id && $company_id){
				$parameters = [];
				$parameters[] = $user_id;
				$position_id = (int) $position_id;
				$parameters[] = $company_id;
				$parameters[] = $user_id;
				$searchwhere = '';
				if($search){
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$searchwhere = " and (i.item_code like ? or i.description like ? or s.invoice like ? or s.dr like ? )";

				}
				$memberWhere = '';
				if($m){
					$parameters[] = $m;
					$memberWhere = " and s.member_id = ?";
				}
				 $q = "SELECT count(distinct(s.payment_id)) AS cnt
						FROM item_alert a
						LEFT JOIN sales s ON s.item_id = a.item_id
						LEFT JOIN items i on i.id = s.item_id
						LEFT JOIN notified n ON n.item_id = s.item_id AND n.payment_id = s.payment_id AND n.user_id=?
						AND n.payment_id = s.payment_id
						WHERE CONCAT( ',', a.position_id, ',' ) LIKE '%,$position_id,%'
						AND s.company_id = ? and (s.member_id != 0)
						AND (
						n.user_id =?
						) $searchwhere $memberWhere group by s.item_id";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
				return false;
			}
		}
		public function get_sales_record($company_id,$start,$limit,$position_id,$user_id,$search='',$m=0){
			if($position_id && $company_id){
				$parameters = [];
				$parameters[] = $user_id;
				$position_id = (int) $position_id;
				$parameters[] = $company_id;
				$parameters[] = $user_id;
				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}
				$searchwhere = '';

				if($search){
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$searchwhere = " and (i.item_code like ? or i.description like ? or s.invoice like ? or s.dr like ? )";

				}


				$memberWhere = '';
				if($m){
					$parameters[] = $m;
					$memberWhere = " and s.member_id = ?";
				}
				$q = "SELECT s.id as sid, s.payment_id,s.invoice,s.ir,s.sr,s.sold_date,s.dr,s.item_id,m.lastname as mln, m.firstname as mfn, m.middlename as mmn, i.item_code,i.description,a.alert_days,a.alert_msg,n.id as nid
						FROM item_alert a
						LEFT JOIN sales s ON s.item_id = a.item_id
						LEFT JOIN notified n ON n.item_id = s.item_id AND n.payment_id = s.payment_id AND n.user_id=?
						LEFT JOIN members m on m.id=s.member_id
						LEFT JOIN items i on i.id=s.item_id
						WHERE CONCAT( ',', a.position_id, ',' ) LIKE '%,$position_id,%'
						AND s.company_id = ? and (s.member_id != 0)
						AND (
						n.user_id =?
						) $searchwhere $memberWhere group by s.payment_id, s.item_id order by n.id desc $l";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
				return false;
			}
		}
	} // end class
?>