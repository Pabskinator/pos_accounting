<?php
	class Inventory_issues_monitoring extends Crud{
		protected $_table = 'inventory_issues_monitoring';
		public function __construct($i=null){
			parent::__construct($i);
		}
		public function countRecord($cid,$search='',$b=0,$r=0,$type=0){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;

				if($search) {
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$likewhere = " and (i.item_code like ? or i.barcode like ? or i.description like ?) ";
				} else {
					$likewhere = "";
				}
				if($b) {
					$parameters[] = $b;
					$branchwhere = " and inv.branch_id=? ";
				} else {
					$branchwhere = "";
				}
				if($r) {
					$parameters[] = $r;
					$rackwhere = " and inv.rack_id=? ";
				} else {
					$rackwhere = "";
				}
				if($type) {
					$parameters[] = $type;
					$typewhere = " and inv.type=? ";
				} else {
					$typewhere = "";
				}
				 $q = "Select count(inv.id) as cnt from inventory_issues_monitoring inv left join items i  on i.id = inv.item_id left join branches b on b.id=inv.branch_id where b.company_id=?  $likewhere $branchwhere $rackwhere $typewhere";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function get_sales_record($cid,$start,$limit,$search='',$b=0,$r=0,$type=0){
			$parameters = array();
			if($cid){
				$parameters[] = $cid;
				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}
				if($search){
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";

					$likewhere = " and (i.item_code like ? or i.barcode like ? or i.description like ?) ";
				} else {
					$likewhere='';
				}
				if($b){
					$parameters[] = $b;
					$branchwhere = " and inv.branch_id=? ";
				} else {
					$branchwhere = "";
				}
				if($r) {
					$parameters[] = $r;
					$rackwhere = " and inv.rack_id=? ";
				} else {
					$rackwhere = "";
				}
				if($type) {
					$parameters[] = $type;
					$typewhere = " and inv.type=? ";
				} else {
					$typewhere = "";
				}
				$now = time();
				$q= "Select inv.*,i.item_code,i.barcode,i.description,r.rack,b.name, u.firstname, u.lastname
					from inventory_issues_monitoring inv
					left join items i  on i.id = inv.item_id
					left join racks r on r.id=inv.rack_id
					left join branches b on b.id=inv.branch_id
					left join users u on u.id = inv.user_id
					where b.company_id=?  $likewhere $branchwhere $rackwhere $typewhere order by inv.created desc $l ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
	}
?>