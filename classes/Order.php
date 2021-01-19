<?php
	class Order extends Crud{
		protected $_table='orders';

		public function __construct($order=null){
			parent::__construct($order);
		}
		public function countRecord($cid,$search='',$status=0,$b=0,$user_id=0){
			$parameters = array();
			if ($cid) {
				$parameters[] = $cid;

				if($search) {
					$parameters[] = "%$search%";
					$likewhere = " and o.id like ? ";
				} else {
					$likewhere = '';
				}
				if($b) {
					$parameters[] = $b;
					$branchwhere = " and o.branch_id=? ";
				} else {
					$branchwhere = "";
				}
				if($status) {
					$parameters[] = $status;
					$statusWhere = " and o.status=? ";
				} else {
					$statusWhere = "";
				}
				if($user_id){
					$parameters[] = $user_id;
					$userWhere = " and o.user_id=? ";
				} else {
					$userWhere = '';
				}
				$q = "Select count(o.id) as cnt from orders o where o.company_id=? and o.is_active=1 $likewhere $branchwhere $statusWhere $userWhere";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function get_reservation_record($cid,$start,$limit,$search='',$b=0,$status=0,$user_id=0){
			$parameters = array();
			$parameters = array();
			if ($cid) {
				$parameters[] = $cid;
				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}
				if($search) {
					$parameters[] = "%$search%";
					$likewhere = " and o.id like ? ";
				} else {
					$likewhere = '';
				}
				if($b) {
					$parameters[] = $b;
					$branchwhere = " and o.branch_id=? ";
				} else {
					$branchwhere = "";
				}
				if($status) {
					$parameters[] = $status;
					$statusWhere = " and o.status=? ";
				} else {
					$statusWhere = "";
				}

				if($user_id){
					$parameters[] = $user_id;
					$userWhere = " and o.user_id=? ";
				} else {
					$userWhere = '';
				}
				$q = "Select * from orders o  where o.company_id=? and o.is_active=1 $likewhere $branchwhere $statusWhere $userWhere $l";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function getOrderForOffline($branch,$company_id){

			$parameters = array();
			if ($branch && $company_id) {
					$parameters[] = $company_id;
					$parameters[] = $branch;

				 $q = "Select o.*,u.firstname,u.lastname,u.middlename,m.lastname as mln , m.firstname as mfn from orders o left join members m on m.id=o.member_id left join users u on u.id=o.user_id  where o.company_id=? and o.branch_id=? and o.is_active=1 and o.status=1";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function getOrderDetails($order_id){

			$parameters = array();
			if ($order_id) {
				$parameters[] = $order_id;


				$q = "Select od.item_id,od.qty,od.price_adjustment,od.discount,i.item_code,i.description,i.barcode,i.item_type,od.ss_json ,od.branch_json from order_details od left join items i on i.id = od.item_id  where order_id=?";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function countPending($cid){
			$parameters = array();
			if ($cid) {
				$parameters[] = $cid;
				$q = "Select count(id) as cnt from orders  where company_id=? and is_active=1 and status=1;";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function getDateMysql(){

		}
	}
?>