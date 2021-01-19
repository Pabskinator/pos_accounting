<?php
	class Bid_list extends Crud{
		protected $_table = 'bid_list';
		public function __construct($b=null){
			parent::__construct($b);
		}
		// get bid list (bid_id)
		public function bidList($bid_id =0){
			if($bid_id){
				$parameters = [];
				$parameters[] = $bid_id;
				$q = "Select b.*, m.lastname as member_name, m.contact_number from bid_list b left join members m on m.id = b.member_id where b.sell_point_id=?";
				$data = $this->_db->query($q,$parameters);
				if($data->count()){
					return $data->results();
				}
				return false;
			}
			return false;
		}
		// changeStatus (bid_id , status)
		public function changeStatus($bid_id = 0,$status = 0){
			if($bid_id && $status){
				$parameters = [];
				$parameters[] = $status;
				$parameters[] = $bid_id;
				$q = "update bid_list set status=? where sell_point_id= ?";
				$data = $this->_db->query($q,$parameters);
				if($data->count()){
					return true;
				}
				return false;
			}
			return false;
		}
	}
?>