<?php
	class Other_income extends Crud{
		protected $_table='other_incomes';

		public function __construct($o=null){
			parent::__construct($o);
		}
		public function getByCr($cr_number=0){

			$parameters = array();
			if($cr_number){
				$parameters[] = $cr_number;

				$q= "
					Select o.*, s.invoice, s.dr,s.ir,s.sold_date,m.lastname as member_name
					from other_incomes o
					left join members m on m.id = o.member_id
					left join ( select * from sales group by payment_id ) s on s.payment_id = o.payment_id
					where 1=1  and o.cr_number = ?

				";
				$e = $this->_db->query($q, $parameters);

				if($e->count()){
					return  $e->results();
				}
			}
			return false;


		}
		public function getRecord($dt_from=0,$dt_to=0,$member_id=0){

			$parameters = array();
			$whereDate= "";
			$whereMember= "";


			if($dt_from && $dt_to){
				$dt_from = strtotime($dt_from);
				$dt_to = strtotime($dt_to . "1 day -1 min");
				$whereDate = " and o.created >= $dt_from and o.created <= $dt_to ";
			}

			if($member_id){
				$parameters[] = $member_id;
				$whereMember = " and o.member_id = ? ";
			}


			$q= "
					Select o.*, s.invoice, s.dr,s.ir,s.sold_date,m.lastname as member_name
					from other_incomes o
					left join members m on m.id = o.member_id
					left join ( select * from sales group by payment_id ) s on s.payment_id = o.payment_id
					where 1=1 $whereDate $whereMember

				";
			$e = $this->_db->query($q, $parameters);

			if($e->count()){
				return  $e->results();
			}

		}
	}
?>