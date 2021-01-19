<?php
	class Reorder_item extends Crud{
		protected $_table = 'reorder_items';
		public function __construct($r=null){
			parent::__construct($r);
		}
		public function countRecord($cid,$like='',$b=0,$status=0){
			$parameters = array();
			$parameters[] = $cid;
			if($like){
				$parameters[] = "%$like%";
				$likewhere = " and i.item_code like ?";
			} else {
				$likewhere='';
			}
			if($b){
				$b = (int) $b;
				$branchwhere = " and r.orderby_branch_id=$b ";
			} else {
				$branchwhere = "";
			}
			if($status){
				$status = (int) $status;
				$statuswhere = " and r.status=$status ";
			} else {
				$statuswhere = "";
			}

			$q= "Select count(r.id) as cnt from reorder_items r left join items i on i.id=r.item_id where r.company_id = ? and r.is_active=1 $likewhere $branchwhere $statuswhere";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->first();
			}
		}
		public function get_active_record($cid,$start=0,$limit=0,$like='',$b=0,$status=0){

			$parameters = array();
			$parameters[] = $cid;
			if($limit){
				$l = " LIMIT $start,$limit";
			} else {
				$l='';
			}
			if($like){

				$parameters[] = "%$like%";
				$likewhere = " and i.item_code like ?";
			} else {
				$likewhere='';
			}
			if($b){
				$b = (int) $b;
				$branchwhere = " and r.orderby_branch_id=$b ";
			} else {
				$branchwhere = "";
			}
			if($status){
				$status= (int) $status;
				$statuswhere = " and r.status=$status ";
			} else {
				$statuswhere = "";
			}
			// prepare the query
			$q= "Select r.*,i.item_code,i.barcode from reorder_items r left join items i on i.id=r.item_id where r.company_id = ? and r.is_active=1 $likewhere $branchwhere $statuswhere $l  ";
			//submit the query
			$data = $this->_db->query($q, $parameters);
			// return results if there is any
			if($data->count()){
				return $data->results();
			}

		}
		public function receiveorder($oid){

			// prepare the query
			$parameters[] = $oid;
			$q= "SELECT * from reorder_items WHERE id=? LIMIT 1";
			$data = $this->_db->query($q, $parameters);
			// return results if there is any
			if($data->count()){
				$results = $data->results();
				$updateinventory = new Inventory();
				return $updateinventory->receiveInventory($results);
			}
			return false;
		}
		public function checkItemOrderPoint($item_id=0,$branch=0,$cid=0){

				if($item_id && $branch &&  $cid ) {
					$parameters = array();
					$parameters[] = $cid;
					$parameters[] = $item_id;
					$parameters[] = $branch;
					$q = "Select count(id) as cnt from reorder_items where company_id = ? and is_active=1 and item_id=? and orderby_branch_id=? and (status=1 or status=2)";
					$data = $this->_db->query($q, $parameters);
					if($data->count()) {
						// return the data if exists
						return $data->first();
					}
				}
		}
		public function countPending($cid){
			$parameters = array();
			if ($cid) {
				$parameters[] = $cid;
				$q = "Select count(id) as cnt from reorder_items  where company_id=? and is_active=1 and status=1;";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

	}
?>