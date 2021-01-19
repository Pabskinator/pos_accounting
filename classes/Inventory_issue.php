<?php
	class Inventory_issue extends Crud{
		protected $_table = 'inventory_issues';

		public function __construct($inventory=null){
			parent::__construct($inventory);
		}

		function getTotalIssuesAmount($branch_id = 0,$company_id=0,$status = ""){
			$parameters = array();
			if($branch_id) {
				$parameters[] = $branch_id;
				$parameters[] = $company_id;
				$now = time();

				if(!$status){
					$status = "";
					$whereStatus = " and i.status in (1,2,3,4)";
				} else {
					$lstat = "";
					if(is_array($status)){

						foreach($status as $s){
							$lstat .="?,";
							$parameters[] = $s;
						}
						$lstat = rtrim($lstat,",");
					} else {
						$lstat = "?";
						$parameters[] = $status;
					}
					$whereStatus = " and i.status in ($lstat)";
				}

				$q = "select sum(p.price * i.qty) as totalAmount , i.branch_id,b.name as branch_name from inventory_issues i left join branches b on b.id = i.branch_id left join items it on it.id = i.item_id left join
							( Select a.item_id, a.effectivity, p.price, p.id as price_id from
							(Select p.item_id, max(p.effectivity) as effectivity  from prices p left join items i on i.id=p.item_id  where i.company_id=$company_id  and p.effectivity <= $now group by p.item_id) a
							left join prices p on p.item_id = a.item_id where a.effectivity = p.effectivity) p on p.item_id = i.item_id where 1=1  $whereStatus group by i.branch_id";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function countRecord($cid,$search='',$b=0,$r=0,$t=0){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;

				if($search) {
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$likewhere = " and (i.item_code like ? or i.barcode like ?  or i.description like ? ) ";
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


					if($r == -1){
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
				if($t) {
					$parameters[] = $t;
					$typewhere = " and inv.status=? ";
				} else {
					$typewhere = "";
				}

				$q = "Select count(inv.id) as cnt from inventory_issues inv left join items i  on i.id = inv.item_id left join branches b on b.id=inv.branch_id where b.company_id=? and inv.qty != 0 $likewhere $branchwhere $rackwhere $typewhere";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function get_sales_record($cid,$start,$limit,$search='',$b=0,$r=0,$t=0){
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

					$likewhere = " and (i.item_code like ? or i.barcode like ? or i.description like ? ) ";
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

					if($r == -1){
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
				if($t) {
					$parameters[] = $t;
					$typewhere = " and inv.status=? ";
				} else {
					$typewhere = "";
				}
				$now = time();
				$q= "Select inv.*,i.item_code,i.barcode,i.description,i.display_location,r.rack,b.name, u.name as unit_name from inventory_issues inv left join items i  on i.id = inv.item_id left join units u on u.id=i.unit_id  left join racks r on r.id=inv.rack_id left join branches b on b.id=inv.branch_id where b.company_id=?  and inv.qty != 0 $likewhere $branchwhere $rackwhere $typewhere $l ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}

		public function checkIfItemExist($item_id,$branch_id,$companyid,$rackid,$status){

			$parameters = array();
			if($companyid){
				// set the company
				$parameters[] = $companyid;
				$parameters[] = $item_id;
				$parameters[] = $branch_id;
				$parameters[] = $rackid;
				$parameters[] = $status;
				$q = 'SELECT i.id FROM inventory_issues i LEFT JOIN branches b ON b.id = i.branch_id left join items it on i.item_id=it.id left join racks r on r.id=i.rack_id WHERE b.company_id =? and it.id=? and b.id=? and i.rack_id=? and b.is_active=1 and it.is_active=1 and i.status=?';

				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return true;
				}
				return false;
			}
		}
		
		public function addInventory($itemid,$branchid,$qty,$isinsert=false,$rack_id,$status){

			if($itemid && $branchid && $qty){
					$parameters = array();

					if($isinsert){

						$parameters[] = $itemid;
						$parameters[] = $qty;
						$parameters[] = $branchid;
						$parameters[] = $rack_id;
						$parameters[] = $status;
						$q = 'insert into inventory_issues(`item_id`,`qty`,`branch_id`,`rack_id`,`status`) values(?,?,?,?,?)';

					}else {

						$oldqty = $this->getQty($itemid,$branchid,$rack_id,$status);
						$newqty = $qty + $oldqty->qty;
						$parameters[] = $newqty;
						$parameters[] = $itemid;
						$parameters[] = $branchid;
						$parameters[] = $rack_id;
							$parameters[] = $status;
						$q = 'Update inventory_issues set qty =? where item_id=? and branch_id=? and rack_id=? and status=?';

					}
					$data = $this->_db->query($q, $parameters);
					if($data->count()){
						// return the data if exists
						return true;
					}
					return false;
			}

		}

		public function subtractInventory($itemid,$branchid,$qty,$rack_id,$status){
			if($itemid && $branchid && $qty){

					$oldqty = $this->getQty($itemid,$branchid,$rack_id,$status);
					$newqty =  $oldqty->qty - $qty;
					$parameters[] = $newqty;
					$parameters[] = $itemid;
					$parameters[] = $branchid;
					$parameters[] = $rack_id;
					$parameters[] = $status;
					$q = 'Update inventory_issues set qty =? where item_id=? and branch_id=? and rack_id=? and status=?';

				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return true;
				}
				return false;
			}
		}

		public function getQty($item,$branch,$rack_id,$status){
			if($item && $branch ){
				$parameters = array();
				$parameters[] = $item;
				$parameters[] = $branch;
				$parameters[] = $rack_id;
				$parameters[] = $status;
				$q = 'Select qty,id from inventory_issues where item_id=? and branch_id=? and rack_id=? and status=?';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					return $data->first();
				}
				return false;
			}
		}

		public function get_racking($item_id = 0, $branch_id = 0, $status = 0) {
			$parameters = array();
			if($item_id && $branch_id) {

				$parameters[] = $item_id;
				$parameters[] = $branch_id;
				$parameters[] = $status;


				 $q = "Select IFNULL(sum(i.qty),0) as rack_qty,i.rack_id,
						r.rack,r.description as rack_description,
						r.stock_man,it.item_code,it.barcode, it.description
						from inventory_issues i
						left join items it on it.id=i.item_id
						left join racks r on r.id=i.rack_id
						where   i.item_id= ? and i.branch_id=? and i.status = ? group by r.rack
 					ORDER BY CASE WHEN r.rack not like '%Display%' then 1 else 2 end ,CASE WHEN r.description not like '%Display%' then 1 else 2 end  ";
				//ORDER BY CASE WHEN r.rack not like '%Display%' then 1 else 2 end, r.rack ASC
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}

		public function getAllQuantity($item_id = 0, $branch_id = 0,$excempt_tags=0,$status=0) {

			if($item_id && $branch_id) {
				$parameters = array();
				$parameters[] = $item_id;
				$parameters[] = $branch_id;
				$parameters[] = $status;
				$wheretags = "";
				if($excempt_tags){
					$parameters[] = $excempt_tags;
					$wheretags = " and r.rack_tag != ? ";
				}
				$q = ' Select sum(i.qty) as totalQty
				from inventory_issues i
				left join racks r on r.id = i.rack_id
				where i.item_id=? and i.branch_id=?  and i.status = ? ' .$wheretags;
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					return $data->first();
				}
				return false;
			}

		}

		public function getPendingOrderQty($item_id = 0, $branch_id = 0) {
			$parameters = array();
			if($item_id && $branch_id) {
				$parameters[] = $item_id;
				$parameters[] = $branch_id;

				$q = "Select IFNULL(sum(od.qty),0) as od_qty
					from wh_orders o
					left join wh_order_details od on od.wh_orders_id=o.id
					where
					od.item_id=? and o.branch_id=?
					and o.is_active=1 and o.status in(1,2,3) and o.stock_out=0 and od.is_surplus = 1 ";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->first();
				}
			}
		}
	
	}
?>