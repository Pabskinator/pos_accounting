<?php
	class Pickup extends Crud{
		protected $_table = 'pickups';
		public function __construct($p=null){
			parent::__construct($p);
		}
		public function countRecord($cid,$search='',$type=0){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				if($type){
					$parameters[] = $type;
					$wheretype= " and p.status=?";
				} else {
					$wheretype= " and p.status=1";
				}
				if($search) {
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$likewhere = " and (i.item_code like ? or i.barcode like ? or i.description like ? ) ";
				} else {
					$likewhere = "";
				}

				$q = "Select count(p.id) as cnt from pickups p left join items  i  on p.item_id = i.id where p.company_id=? $wheretype $likewhere";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function get_pickup_record($cid,$start,$limit,$search='',$type=0){
			$parameters = array();
			if($cid){
				$parameters[] = $cid;
				if($type){
					$parameters[] = $type;
					$wheretype= " and p.status=?";
				} else {
					$wheretype= " and p.status=1";
				}
				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}
				if($search){
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$likewhere = " and (i.item_code like ? or i.barcode like ? or i.description like ? ) ";

				} else {
					$likewhere='';
				}

				$q= "Select p.*,i.item_code, i.description,b.name as branch_name,b2.name as src_branch_name , m.lastname as mln , m.firstname as mfn, u.lastname as uln , u.firstname as ufn, u2.lastname as uln2 , u2.firstname as ufn2  from pickups p left join items i  on p.item_id = i.id left join branches b on b.id=p.branch_id left join branches b2 on b2.id=p.src_branch left join members m on m.id=p.member_id left join users u on u.id=p.cashier_id left join users u2 on u2.id = p.out_by where p.is_active = 1 and p.company_id=? $wheretype $likewhere $l";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}

		public function processPickup($id = 0,$user=0){
			$parameters = array();
			if($id) {
				$parameters[] = $user;
				$parameters[] = $id;
				$now = time();
				$q = "update pickups set status = 2 ,  out_by=?, processed_date=$now where id=? ";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return true;
				}
				return false;
			}
		}
	}
?>