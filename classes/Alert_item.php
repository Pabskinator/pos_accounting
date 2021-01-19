<?php
	class Alert_item extends Crud{
		protected $_table='item_alert';
		public function __construct($i = NULL){
			parent::__construct($i);
		}
		public function countRecord($cid,$search=''){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$this->where("a.company_id=? and a.is_active=1");
				if($search) {
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					 $this->where("and (i.barcode like ? or i.item_code like ? or i.description like ? )");
				}
				return $this->select("count(a.id) as cnt")
					->from("item_alert a")
					->join("left join items i on i.id=a.item_id")
					->get($parameters)
					->first();

			}
		}
		public function get_alert_record($cid,$start,$limit,$search=''){
			$parameters = array();
			if($cid){
				$parameters[] = $cid;
				$this->where("a.company_id=? and a.is_active=1");
				if($limit){
					 $this->limitBy("$start,$limit");
				}
				if($search){
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$this->where("and (i.barcode like ? or i.item_code like ? or i.description like ? )");
				}

				return $this->select("a.*,i.item_code,i.description")
					->from("item_alert a")
					->join("left join items i on i.id=a.item_id")
					->get($parameters)
					->all();

			}
		}
		public function getAlert($position_id,$company_id,$user_id){
			if($position_id && $company_id){
				$parameters = [];
				$parameters[] = $user_id;
				$position_id = (int) $position_id;
				$parameters[] = $company_id;
				$parameters[] = $user_id;

				return $this->select("count( s.id ) AS cnt")
					->from("item_alert a")
					->join("LEFT JOIN sales s ON s.item_id = a.item_id")
					->join("LEFT JOIN notified n ON n.item_id = s.item_id  AND n.payment_id = s.payment_id AND n.user_id=? AND n.payment_id = s.payment_id")
					->where("CONCAT( ',', a.position_id, ',' ) LIKE '%,$position_id,%'")
					->where("AND a.is_active=1")
					->where("AND  s.company_id = ?")
					->where("AND s.member_id != 0")
					->where("AND DATEDIFF( CURDATE( ) , FROM_UNIXTIME( s.sold_date ) ) >= a.alert_days")
					->where("AND ( n.user_id !=? OR n.user_id IS NULL )")
					->get($parameters)
					->first();


			}
		}

		public function getAlertMsg($position_id,$company_id,$user_id){
			if($position_id && $company_id){
				$parameters = [];
				$parameters[] = $user_id;
				$position_id = (int) $position_id;
				$parameters[] = $company_id;
				$parameters[] = $user_id;

				return $this->select("s.id as sid, s.payment_id,s.invoice,s.sold_date,s.dr,s.item_id,m.lastname as mln, m.firstname as mfn, m.middlename as mmn, i.item_code,i.description,a.alert_days,a.alert_msg")
					->from("item_alert a")
					->join("LEFT JOIN sales s ON s.item_id = a.item_id")
					->join("LEFT JOIN notified n ON n.item_id = s.item_id AND n.payment_id = s.payment_id AND n.user_id=?")
					->join("LEFT JOIN members m on m.id=s.member_id")
					->join("LEFT JOIN items i on i.id=s.item_id")
					->where("CONCAT( ',', a.position_id, ',' ) LIKE '%,$position_id,%'")
					->where("AND a.is_active=1")
					->where("AND s.company_id = ?")
					->where("AND s.member_id != 0")
					->where("AND DATEDIFF( CURDATE( ) , FROM_UNIXTIME( s.sold_date ) ) >= a.alert_days")
					->where("AND ( n.user_id !=? OR n.user_id IS NULL)")
					->get($parameters)
					->all();



			}
		}
	}
?>