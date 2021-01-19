<?php
	class Service extends Crud{
		protected $_table='services';


		public function __construct($serv = NULL){
			parent::__construct($serv);
		}
		public  function deleteSubs($id = 0){
			$parameters = array();
			if($id){
				$parameters[] = $id;
				$q= 'delete from services where id=?';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return true;
				}
			}
		}
		public function getServices($company_id = 0,$member_id =0){
			$parameters = array();
			$now = time();
			if($company_id){
				$parameters[] = $company_id;
				$member_where = '';
				if($member_id) {
					$parameters[] = $member_id;
					$member_where = " and s.member_id = ?";
				}
				$q= 'Select s.*, i.item_code,con.qty as con_qty, m.lastname from services s left join items i on i.id = s.item_id left join members m on m.id=s.member_id left join consumables con on con.item_id = i.id where s.company_id=? '.$member_where.' and s.consumable_qty != 10000 and s.consumable_qty>0 ';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function getSubsciption($company_id = 0,$member_id=0){
			$parameters = array();

			if($company_id){
				$parameters[] = $company_id;
				$member_where = '';
				if($member_id) {
					$parameters[] = $member_id;
					$member_where = " and s.member_id = ?";
				}
				$q= 'Select s.member_id,s.item_id,s.id, s.start_date,s.end_date,m.lastname,m.firstname,m.middlename,i.item_code from services s left join members m on m.id=s.member_id left join items i on i.id=s.item_id where s.company_id=? '.$member_where.' and s.consumable_qty = 10000 and i.item_type=3 order by s.end_date desc';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function getConsumableAmount($company_id = 0){
			$parameters = array();
			if($company_id){
				$parameters[] = $company_id;
				$q= 'Select s.id, s.start_date,s.end_date,m.lastname,m.firstname,m.middlename,i.item_code,i.id as item_id from services s left join members m on m.id=s.member_id left join items i on i.id=s.item_id where s.company_id=? and s.consumable_qty = 10000 and i.item_type=4';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}

	}
?>