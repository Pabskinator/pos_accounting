<?php
	class Inventory_ammend extends Crud{
		protected $_table = 'inventory_ammend';
		public function __construct($i=null){
			parent::__construct($i);
		}
		public function getInventoryAmmendByAuditId($r,$b,$c,$item_id,$auditid){
			if ($r && $b && $c && $item_id && $auditid){
				$parameters = array();
				$parameters[] = $r;
				$parameters[] = $b;
				$parameters[] = $c;
				$parameters[] = $item_id;
				$parameters[] = $auditid;
				$q = "Select * from inventory_ammend where rack_id = ? and branch_id=? and company_id=? and item_id=? and audit_id=?";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function isAuditedBefore($r,$b,$c,$item_id){
			if ($r && $b && $c && $item_id ){
				$parameters = array();
				$parameters[] = $r;
				$parameters[] = $b;
				$parameters[] = $c;
				$parameters[] = $item_id;
				$q = "Select count(id) as cnum from inventory_ammend where rack_id = ? and branch_id=? and company_id=? and item_id=?";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function countRecord($cid, $search = '', $b = 0, $r = 0,$date_from=0,$date_to=0) {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$cur_month =  (int) date('m');
				if($search) {
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";

					$likewhere = " and (i.item_code like ? or i.barcode like ? or i.description like ? ) ";
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
					if($r == -1) {
						$rack = new Rack();
						$rackdis = $rack->getRackDisplayId($cid);
						$parameters[] = $rackdis->id;
						$rackwhere = " and inv.rack_id != ? ";
					} else {
						$parameters[] = $r;
						$rackwhere = " and inv.rack_id = ? ";
					}

				} else {
					$rackwhere = "";
				}
				$whereDate = '';
				if($date_from && $date_to){
					$date_from = strtotime($date_from);
					$date_to = strtotime($date_to . " 1 day -1 min");
					$whereDate = " and inv.created >= $date_from  and inv.created <= $date_to";
				}

				$q = "Select count(inv.id) as cnt from inventory_ammend inv left join items i  on i.id = inv.item_id left join branches b on b.id=inv.branch_id left join racks r on r.id = inv.rack_id where inv.rack_id !=0 and i.is_active = 1 and b.company_id=?  $likewhere $branchwhere $rackwhere $whereDate ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}


		public function get_record($cid, $start, $limit, $search = '', $b = 0, $r = 0,$date_from=0,$date_to=0) {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;

				if($limit) {
					$l = " LIMIT $start,$limit";
				} else {
					$l = '';
				}
				if($search) {
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";

					$likewhere = " and (i.item_code like ? or i.barcode like ? or i.description like ? ) ";
				} else {
					$likewhere = '';
				}
				if($b) {
					$parameters[] = $b;
					$branchwhere = " and inv.branch_id=? ";

				} else {
					$branchwhere = "";
					$addcolOrderpoint = "";
				}
				if($r) {

					if($r == -1) {
						$rack = new Rack();
						$rackdis = $rack->getRackDisplayId($cid);
						$parameters[] = $rackdis->id;
						$rackwhere = " and inv.rack_id != ? ";

					} else {
						$parameters[] = $r;
						$rackwhere = " and inv.rack_id = ? ";
					}
				} else {
					$rackwhere = "";
				}
				$whereDate = '';
				if($date_from && $date_to){
					$date_from = strtotime($date_from);
					$date_to = strtotime($date_to . " 1 day -1 min");
					$whereDate = " and inv.created >= $date_from  and inv.created <= $date_to";
				}

				$q = "Select  inv.*,ic.name as category_name,i.item_code,i.barcode,i.description,i.display_location,
						r.rack,b.name, u.name as unit_name, ur.firstname , ur.lastname
						from inventory_ammend inv
						left join (Select rack_id, branch_id, item_id, user_id,created from inventory_monitoring order by created desc) invm on invm.rack_id = inv.rack_id and invm.branch_id = inv.branch_id and invm.item_id = inv.item_id  and invm.created = inv.created
						left join users ur on ur.id = invm.user_id
						left join items i  on i.id = inv.item_id
						left join categories ic on ic.id=i.category_id
						left join units u on u.id=i.unit_id
						left join racks r on r.id=inv.rack_id
						left join branches b on b.id=inv.branch_id
						where  inv.rack_id !=0 and
						b.company_id=? $likewhere $branchwhere $rackwhere $whereDate  order by inv.id desc $l ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}
	}
?>