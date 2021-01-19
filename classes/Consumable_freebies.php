<?php
	class Consumable_freebies extends Crud{
		protected $_table = 'consumable_freebies';
		public function __construct($c = null){
			parent::__construct($c);
		}
		public function getConsumableFreebiesAmount($item_id=0){
				$parameters = array();
				if($item_id){
					$parameters[] = $item_id;
					$q= 'Select amount from con_freebies_amount where item_id=?';
					$data = $this->_db->query($q, $parameters);
					if($data->count()){
						// return the data if exists
						return $data->first();
					}
				}
		}
		public function insertConFreeAmount($item_id=0,$amount=0,$cid=0){
				$parameters = array();
				if($item_id && $amount && $cid){
					$parameters[] = $item_id;
					$parameters[] = $amount;
					$parameters[] = $cid;
					$q= 'INSERT INTO `con_freebies_amount`(`item_id`, `amount`, `company_id`) VALUES (?,?,?)';
					$data = $this->_db->query($q, $parameters);
					if($data->count()){
						// return the data if exists
						return true;
					}
				}
		}

		public function updateConFreeAmount($item_id=0,$amount=0){
			$parameters = array();
			if($item_id && $amount){
				$parameters[] = $amount;
				$parameters[] = $item_id;

				$q= 'update con_freebies_amount set amount=? where item_id=?';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return true;
				}
			}
		}
		public function countRecord($cid,$like=''){
			$parameters = array();
			$parameters[] = $cid;
			if($like){
				$parameters[] = "%$like%";
				$likewhere = " and CONCAT(m.lastname,', ',m.firstname) like ? ";
			} else {
				$likewhere='';
			}


			$q= "Select count(c.id) as cnt from consumable_freebies c left join members  m on m.id=c.member_id left join payments p on p.id=c.payment_id where p.company_id = ?  $likewhere ";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->first();
			}
		}
		public function get_active_record($cid,$start=0,$limit=0,$like=''){

			$parameters = array();
			$parameters[] = $cid;
			if($limit){
				$l = " LIMIT $start,$limit";
			} else {
				$l='';
			}
			if($like){
				$parameters[] = "%$like%";
				$likewhere = " and CONCAT(m.lastname,', ',m.firstname) like ? ";
			} else {
				$likewhere='';
			}

			// prepare the query
			$q= "Select c.*,m.lastname,m.firstname from consumable_freebies c left join members  m on m.id=c.member_id left join payments p on p.id=c.payment_id where p.company_id = ?  $likewhere order by c.member_id  $l  ";
			//submit the query
			$data = $this->_db->query($q, $parameters);
			// return results if there is any
			if($data->count()){
				return $data->results();
			}
		}
		public function getAmountConsumablefree($memid){

			$parameters = array();
			$parameters[] = $memid;
			$q= "select amount,id from consumable_freebies where member_id=? order by id desc limit 1";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->first();
			}
		}
		public function updateConsumable($amount,$memid){
			if($amount && $memid){
				$parameters = array();
				$prevamt = $this->getAmountConsumablefree($memid);
				$newamt = $prevamt->amount + $amount;
				$parameters[] = $newamt;
				$parameters[] = $prevamt->id;
				$parameters[] = $memid;
				$q= "update consumable_freebies set amount = ? where id=? and member_id=?";
				$data = $this->_db->query($q,$parameters);
				if($data->count()){
					// return the data if exists
					return true;
				}
			}
		}
	}
?>