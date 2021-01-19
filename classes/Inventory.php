<?php

	class Inventory extends Crud {
		protected $_table = 'inventories';

		public function __construct($inventory = null) {
			parent::__construct($inventory);
		}

		function getTotalWhAmount($branch_id = 0,$company_id=0){
			$parameters = array();
			if($branch_id) {

				$parameters[] = $branch_id;
				$parameters[] = $company_id;
				$now = time();
				$q = "select sum(p.price * i.qty) as totalAmount , i.branch_id,b.name as branch_name from inventories i left join branches b on b.id = i.branch_id left join items it on it.id = i.item_id
							left join
							( Select a.item_id, a.effectivity, p.price, p.id as price_id from
							(Select p.item_id, max(p.effectivity) as effectivity  from prices p left join items i on i.id=p.item_id  where i.company_id=$company_id  and p.effectivity <= $now group by p.item_id) a
							left join prices p on p.item_id = a.item_id where a.effectivity = p.effectivity) p on p.item_id = i.item_id group by i.branch_id";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function getAllInventories($companyid = 0, $branchid = 0) {
			$parameters = array();
			if($companyid) {
				// set the company
				$parameters[] = $companyid;
				$q = 'SELECT i.qty,b.name,it.barcode,it.item_code,i.critical_level,i.rack_id, i.item_id
					FROM inventories i LEFT JOIN branches b ON b.id = i.branch_id left join items it on i.item_id=it.id WHERE b.company_id =? ';
				// if branch id is set, get specific branch only
				if($branchid) {
					$parameters[] = $branchid;
					$q .= ' and i.branch_id=?';

				}
				$q .= " ORDER BY b.id,it.barcode";
				// submit the query to DB class
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function getRackName($rack_id = 0, $company_id = 0,$branch_id=0) {
			if($rack_id) {
				$parameters[] = $rack_id;
				if(!is_numeric($rack_id)) {
					$parameters[] = $company_id;
					$wherebranch = "";
					if($branch_id){
						$parameters[] = $branch_id;
						$wherebranch = " and branch_id = ?";
					}
					$q = "SELECT id FROM racks  WHERE rack=? and is_active=1 and company_id=?  $wherebranch";
				} else {

					$q = 'SELECT r.rack FROM racks r left join inventories i on r.id=i.rack_id  WHERE r.id=? and  r.is_active=1';
				}
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					return $data->first();
				}
			} else {
				return false;
			}
		}

		public function checkIfItemExist($item_id, $branch_id, $companyid, $rackid) {

			$parameters = array();
			if($companyid) {
				// set the company
				$parameters[] = $companyid;
				$parameters[] = $item_id;
				$parameters[] = $branch_id;
				$parameters[] = $rackid;

				$q = 'SELECT i.id FROM inventories i LEFT JOIN branches b ON b.id = i.branch_id
			left join items it on i.item_id=it.id left join racks r on r.id=i.rack_id
				WHERE b.company_id =? and it.id=? and b.id=? and i.rack_id=? and b.is_active=1 and it.is_active=1';

				$data = $this->_db->query($q, $parameters);

				if($data->count()) {
					// return the data if exists
					return true;
				}

				return false;
			}
		}

		public function getQty($item, $branch, $rack_id) {
			if($item && $branch) {
				$parameters = array();
				$parameters[] = $item;
				$parameters[] = $branch;
				$parameters[] = $rack_id;
				$q = 'Select qty,id from inventories where item_id=? and branch_id=? and rack_id=?';
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					return $data->first();
				}

				return false;
			}
		}

		public function addInventory($itemid, $branchid, $qty, $isinsert = false, $rack_id) {
			if($itemid && $branchid && $qty) {
				$parameters = array();
				if($isinsert) {

					$parameters[] = $itemid;
					$parameters[] = $qty;
					$parameters[] = $branchid;
					$parameters[] = $rack_id;
					 $q = 'insert into inventories(`item_id`,`qty`,`branch_id`,`rack_id`) values(?,?,?,?)';
				} else {
					$oldqty = $this->getQty($itemid, $branchid, $rack_id);
					$newqty = $qty + $oldqty->qty;
					$parameters[] = $newqty;
					$parameters[] = $itemid;
					$parameters[] = $branchid;
					$parameters[] = $rack_id;
					$q = 'Update inventories set qty =? where item_id=? and branch_id=? and rack_id=?';
				}
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return true;
				}

				return false;
			}
		}

		public function subtractInventory($itemid, $branchid, $qty, $rack_id) {
			if($itemid && $branchid && $qty) {

				$oldqty = $this->getQty($itemid, $branchid, $rack_id);
				$newqty = $oldqty->qty - $qty;
				$parameters[] = $newqty;
				$parameters[] = $itemid;
				$parameters[] = $branchid;
				$parameters[] = $rack_id;
				$q = 'Update inventories set qty =? where item_id=? and branch_id=? and rack_id=?';

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return true;
				}

				return false;
			}
		}

		public function getRackInventory($item_id = 0, $branch_id = 0, $rack_id = 0) {
			if($item_id && $branch_id) {
				$parameters = array();
				$parameters[] = $item_id;
				$parameters[] = $branch_id;
				$parameters[] = $rack_id;
				 $q = "Select r.id,r.rack,r.description,i.qty from racks r left join inventories i on i.rack_id=r.id where  i.qty > 0 and i.item_id=? and i.branch_id=? and r.id !=?";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function getInventoryOfCompany($item_id = 0, $company_id = 0) {
			if($item_id && $company_id) {
				$parameters = array();
				$parameters[] = $item_id;
				$parameters[] = $company_id;
				$q = "Select r.id,r.rack,i.qty,b.id as bid,b.name as bname from inventories i left join racks r on i.rack_id=r.id left join branches b on b.id=i.branch_id where  i.item_id=? and b.company_id=? and i.qty > 0";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function getAllQuantity($item_id = 0, $branch_id = 0,$excempt_tags=0) {

			if($item_id && $branch_id) {
				$parameters = array();
				$parameters[] = $item_id;
				$parameters[] = $branch_id;
				$wheretags = "";
				if($excempt_tags){
					$parameters[] = $excempt_tags;
					$wheretags = " and r.rack_tag != ? ";
				}
				 $q = 'Select sum(i.qty) as totalQty
				from inventories i
				left join racks r on r.id = i.rack_id
				where i.item_id=? and i.branch_id=? ' .$wheretags;
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					return $data->first();
				}
				return false;
			}

		}

		public function receiveInventory($results) {
			// get rack display id
			$rackDisplay = new Rack();
			$inv_mon = new Inventory_monitoring();
			$user = new User();
			$dis = $rackDisplay->getRackDisplayId($results[0]->company_id);

			//check if my stock sa kukuhaan
			if($results[0]->orderto_branch_id == 0) {
				$curinventory = $this->getQty($results[0]->item_id, $results[0]->orderby_branch_id, $dis->id);
				$this->addInventory($results[0]->item_id, $results[0]->orderby_branch_id, $results[0]->qty, $isinsert = false, $dis->id);
				// monitoring
				$newqty = $curinventory->qty + $results[0]->qty;
				$inv_mon->create(array('item_id' => $results[0]->item_id, 'rack_id' => $dis->id, 'branch_id' => $results[0]->orderby_branch_id, 'page' => 'classes/Inventory', 'action' => 'Update', 'prev_qty' => $curinventory->qty, 'qty_di' => 1, 'qty' => $results[0]->qty, 'new_qty' => $newqty, 'created' => time(), 'user_id' => $user->data()->id, 'remarks' => 'Receive inventory from order', 'is_active' => 1, 'company_id' => $user->data()->company_id));

				return true;
			} else {
				$checkStock = $this->getQty($results[0]->item_id, $results[0]->orderto_branch_id, $dis->id);
				if($checkStock) {
					if($checkStock->qty > $results[0]->qty) {
						$curinventoryFrom = $this->getQty($results[0]->item_id, $results[0]->orderto_branch_id, $dis->id);
						// bawas sa kinuhaan
						$this->subtractInventory($results[0]->item_id, $results[0]->orderto_branch_id, $results[0]->qty, $dis->id);
						// monitoring 
						$newqty = $curinventoryFrom->qty - $results[0]->qty;
						$inv_mon->create(array('item_id' => $results[0]->item_id, 'rack_id' => $dis->id, 'branch_id' => $results[0]->orderto_branch_id, 'page' => 'classes/Inventory', 'action' => 'Update', 'prev_qty' => $curinventoryFrom->qty, 'qty_di' => 2, 'qty' => $results[0]->qty, 'new_qty' => $newqty, 'created' => time(), 'user_id' => $user->data()->id, 'remarks' => 'Receive inventory from order', 'is_active' => 1, 'company_id' => $user->data()->company_id));
						$curinventoryTo = $this->getQty($results[0]->item_id, $results[0]->orderby_branch_id, $dis->id);

						// dagdag sa umorder
						$this->addInventory($results[0]->item_id, $results[0]->orderby_branch_id, $results[0]->qty, $isinsert = false, $dis->id);
						// monitoring
						$newqty = $curinventoryTo->qty + $results[0]->qty;
						$inv_mon->create(array('item_id' => $results[0]->item_id, 'rack_id' => $dis->id, 'branch_id' => $results[0]->orderby_branch_id, 'page' => 'classes/Inventory', 'action' => 'Update', 'prev_qty' => $curinventoryTo->qty, 'qty_di' => 1, 'qty' => $results[0]->qty, 'new_qty' => $newqty, 'created' => time(), 'user_id' => $user->data()->id, 'remarks' => 'Receive inventory from order', 'is_active' => 1, 'company_id' => $user->data()->company_id));

						return true;
					} else {
						return false;
					}
				} else {
					return false;
				}
			}


		}

		public function countRecord($cid, $search = '', $b = 0, $r = 0, $si = 0, $racktxt = '', $categ = "",$tag_id=0) {
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
				$leftjoinOrderPoint="";
				if($b) {
					$explodedb = explode(',',$b);
					$listb = "";
					foreach($explodedb as $eb){
						$listb .= "?,";
						$parameters[] = $eb;
					}
					$listb = rtrim($listb,",");
					$branchwhere = " and inv.branch_id in ($listb) ";
					//$leftjoinOrderPoint = "left join (Select item_id,order_point from reorder_points where orderby_branch_id=$b  and `month` = $cur_month and is_active=1) rp on rp.item_id =inv.item_id ";
					$addcolOrderpoint = "";
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
				if($si) {
					$parameters[] = $si;
					$leftjoin = "left join supplier_item si on si.item_id = inv.item_id";
					$wheresi = "and si.supplier_id=?";
				} else {
					$leftjoin = "";
					$wheresi = "";
				}
				if($racktxt) {
					$parameters[] = "%$racktxt%";
					$racktxtWh = " and r.rack like ? ";
				} else {
					$racktxtWh = "";
				}

				$cwhere = '';
				if($categ) {
					$explodedcateg = explode(',',$categ);
					$listcateg = "";
					foreach($explodedcateg as $ec){
						$listcateg .= "?,";
						$parameters[] = $ec;
					}
					$listcateg = rtrim($listcateg,",");
					$cwhere = "and i.category_id in($listcateg) ";
				}
				$cur_month =  (int) date('m');
				if($tag_id) {
					$parameters[] = $tag_id;
					$tag_where = " and r.rack_tag = ? ";
				} else {
					$tag_where = "";
				}
				$q = "Select count(inv.id) as cnt from inventories inv left join items i  on i.id = inv.item_id $leftjoin left join branches b on b.id=inv.branch_id left join racks r on r.id = inv.rack_id $leftjoinOrderPoint where inv.qty != 0 and inv.rack_id !=0 and i.is_active = 1 and b.company_id=?  $likewhere $branchwhere $rackwhere $wheresi $racktxtWh $cwhere $tag_where";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function get_sales_record($cid, $start, $limit, $search = '', $b = 0, $r = 0, $si = 0, $racktxt = '', $categ = 0,$tag_id=0) {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$cur_month =  (int) date('m');
				$leftjoinOrderPoint = "";
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
					$likewhere = "";
				}
				$leftjoinOrderPoint="";
				if($b) {
					$explodedb = explode(',',$b);
					$listb = "";
					foreach($explodedb as $eb){
						$listb .= "?,";
						$parameters[] = $eb;
					}
					$listb = rtrim($listb,",");
					$branchwhere = " and inv.branch_id in ($listb) ";
					//$leftjoinOrderPoint = "left join (Select item_id,order_point from reorder_points where orderby_branch_id=$b  and `month` = $cur_month and is_active=1) rp on rp.item_id =inv.item_id ";
					$addcolOrderpoint = "";
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
				if($si) {
					$parameters[] = $si;
					$leftjoin = "left join supplier_item si on si.item_id = inv.item_id";
					$wheresi = "and si.supplier_id=?";
				} else {
					$leftjoin = "";
					$wheresi = "";
				}
				if($racktxt) {
					$parameters[] = "%$racktxt%";
					$racktxtWh = " and r.rack like ? ";
				} else {
					$racktxtWh = "";
				}

				$cwhere = '';
				if($categ) {
					$explodedcateg = explode(',',$categ);
					$listcateg = "";
					foreach($explodedcateg as $ec){
						$listcateg .= "?,";
						$parameters[] = $ec;
					}
					$listcateg = rtrim($listcateg,",");
					$cwhere = "and i.category_id in($listcateg) ";
				}
				$cur_month =  (int) date('m');
				if($tag_id) {
					$parameters[] = $tag_id;
					$tag_where = " and r.rack_tag = ? ";
				} else {
					$tag_where = "";
				}
				 $q = "Select $addcolOrderpoint inv.*,ic2.name as parent_name, ic.name as category_name,i.item_code,i.barcode,i.description,i.display_location,i.product_cost,
						r.rack,b.name, u.name as unit_name
						from inventories inv
						left join items i  on i.id = inv.item_id
						left join categories ic on ic.id=i.category_id
						left join categories ic2 on ic2.id = ic.parent
						left join units u on u.id=i.unit_id $leftjoin
						left join racks r on r.id=inv.rack_id
						left join branches b on b.id=inv.branch_id  $leftjoinOrderPoint
						where inv.qty != 0 and inv.rack_id !=0 and i.is_active = 1 and
						b.company_id=? $likewhere $branchwhere $rackwhere $wheresi $racktxtWh $cwhere $tag_where $l ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}

		public function countRecordAudit($cid, $search = '', $b = 0) {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;

				if($search) {
					$search = addslashes($search);
					$likewhere = " and (i.item_code like '%$search%' or i.barcode like '%$search%' ) ";
				} else {
					$likewhere = "";
				}

				if($b) {
					$parameters[] = $b;
					$branchwhere = " and inv.branch_id=? ";
				} else {
					$branchwhere = "";
				}
				$q = "Select count(DISTINCT(inv.item_id)) as cnt from inventories inv left join items i  on i.id = inv.item_id  left join branches b on b.id=inv.branch_id where i.is_active = 1 and b.company_id=?  $likewhere $branchwhere  ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function get_audit_record($cid, $start, $limit, $search = '', $b = 0) {
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

				}

				//$q = "Select  inv.*,ic.name as category_name,i.item_code,i.barcode,i.description,i.display_location,r.rack,b.name, u.name as unit_name from inventories inv left join items i  on i.id = inv.item_id left join categories ic on ic.id=i.category_id left join units u on u.id=i.unit_id  left join racks r on r.id=inv.rack_id left join branches b on b.id=inv.branch_id   where inv.rack_id !=0 and i.is_active = 1 and b.company_id=? $likewhere $branchwhere  order by i.description asc $l ";
				$q = "Select  sum(inv.qty) as qty,inv.item_id, inv.branch_id,ic.name as category_name,i.item_code,i.barcode,i.description,i.display_location,b.name, u.name as unit_name from inventories inv left join items i  on i.id = inv.item_id left join categories ic on ic.id=i.category_id left join units u on u.id=i.unit_id  left join branches b on b.id=inv.branch_id   where i.is_active = 1 and b.company_id=? $likewhere $branchwhere  group by inv.item_id order by i.description asc $l ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}
		public function countRecordReport($cid, $search = '',$dt1=0,$dt2=0,$b=0,$r=0,$g ='',$is_cebu_hiq = false) {
			$parameters = array();
			if($cid) {

				$parameters[] = $cid;

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
					if($r == -1) $r = 0;
					$parameters[] = $r;
					$rackwhere = " and inv.rack_id=? ";
				} else {
					$rackwhere = "";
				}
				if($g){
					$group_by = "";
					$col = "count(distinct(inv.item_id))";
				} else {
					$group_by ="";
					$col = "count(inv.id)";
				}
				 $q = "Select $col as cnt
			from inventories inv
			left join items i  on i.id = inv.item_id
			left join branches b on b.id=inv.branch_id
			left join racks r on r.id = inv.rack_id where i.is_active= 1 and b.company_id=?  $likewhere $branchwhere $rackwhere  $group_by";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function get_report_record($cid, $start, $limit, $search = '',$dt1=0,$dt2=0,$b=0,$r=0,$g ='',$is_cebu_hiq = false) {
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
				$order_by = "";
				$leftjoinOut ='';
				$leftjoinOutAmend ='';
				$leftjoinIn = '';
				$leftjoinOut2 ='';
				$leftjoinOutAmend2 ='';
				$leftjoinIn2 = '';
				$leftJoinEnding = '';

				$now = time();

				if($dt1 && $dt2){
					if(!$g){
						$leftjoinOut = "left join (select sum(qty) as out_qty, item_id, rack_id , branch_id from inventory_monitoring where created >= $dt1 and created <= $dt2 and company_id=$cid and qty_di = 2 and remarks != 'Ammend inventory' group by item_id,rack_id,branch_id) outinv on outinv.item_id = inv.item_id and outinv.rack_id=inv.rack_id and outinv.branch_id=inv.branch_id ";
						$leftjoinOutAmend = "left join (select sum(qty) as amend_qty, item_id, rack_id , branch_id from inventory_monitoring where created >= $dt1 and created <= $dt2 and company_id=$cid and qty_di = 3 and remarks = 'Ammend inventory' group by item_id,rack_id,branch_id) amendinv on amendinv.item_id = inv.item_id and amendinv.rack_id=inv.rack_id and amendinv.branch_id=inv.branch_id ";
						$leftjoinIn = "left join (select sum(qty) as in_qty, item_id, rack_id , branch_id from inventory_monitoring where created >= $dt1 and created <= $dt2 and company_id=$cid and qty_di = 1 group by item_id,rack_id,branch_id) ininv on ininv.item_id = inv.item_id and ininv.rack_id=inv.rack_id and ininv.branch_id=inv.branch_id ";

						$leftjoinOut2 = "left join (select sum(qty) as out_qty2, item_id, rack_id , branch_id from inventory_monitoring where created >= $dt1 and created <= $now and company_id=$cid and qty_di = 2 and remarks != 'Ammend inventory' group by item_id,rack_id,branch_id) outinv2 on outinv2.item_id = inv.item_id and outinv2.rack_id=inv.rack_id and outinv2.branch_id=inv.branch_id ";
						$leftjoinOutAmend2 = "left join (select sum(qty) as amend_qty2, item_id, rack_id , branch_id from inventory_monitoring where created >= $dt1 and created <= $now and company_id=$cid and qty_di = 3 and remarks = 'Ammend inventory' group by item_id,rack_id,branch_id) amendinv2 on amendinv2.item_id = inv.item_id and amendinv2.rack_id=inv.rack_id and amendinv2.branch_id=inv.branch_id ";
						$leftjoinIn2 = "left join (select sum(qty) as in_qty2, item_id, rack_id , branch_id from inventory_monitoring where created >= $dt1 and created <= $now and company_id=$cid and qty_di = 1 group by item_id,rack_id,branch_id) ininv2 on ininv2.item_id = inv.item_id and ininv2.rack_id=inv.rack_id and ininv2.branch_id=inv.branch_id ";


					} else {
						$leftjoinOut = "left join (select sum(qty) as out_qty, item_id , branch_id from inventory_monitoring where created >= $dt1 and created <= $dt2 and company_id=$cid and qty_di = 2 and remarks != 'Ammend inventory' group by item_id,branch_id) outinv on outinv.item_id = inv.item_id  and outinv.branch_id=inv.branch_id ";
						$leftjoinOutAmend = "left join (select sum(qty) as amend_qty, item_id , branch_id from inventory_monitoring where created >= $dt1 and created <= $dt2 and company_id=$cid and qty_di = 3 and remarks = 'Ammend inventory' group by item_id,branch_id) amendinv on amendinv.item_id = inv.item_id  and amendinv.branch_id=inv.branch_id ";
						$leftjoinIn = "left join (select sum(qty) as in_qty, item_id , branch_id from inventory_monitoring where created >= $dt1 and created <= $dt2 and company_id=$cid and qty_di = 1 group by item_id,branch_id) ininv on ininv.item_id = inv.item_id and ininv.branch_id=inv.branch_id ";

						$leftjoinOut2 = "left join (select sum(qty) as out_qty2, item_id , branch_id from inventory_monitoring where created >= $dt1 and created <= $now and company_id=$cid and qty_di = 2 and remarks != 'Ammend inventory' group by item_id,branch_id) outinv2 on outinv2.item_id = inv.item_id  and outinv2.branch_id=inv.branch_id ";
						$leftjoinOutAmend2 = "left join (select sum(qty) as amend_qty2, item_id , branch_id from inventory_monitoring where created >= $dt1 and created <= $now and company_id=$cid and qty_di = 3 and remarks = 'Ammend inventory' group by item_id,branch_id) amendinv2 on amendinv2.item_id = inv.item_id  and amendinv2.branch_id=inv.branch_id ";
						$leftjoinIn2 = "left join (select sum(qty) as in_qty2, item_id , branch_id from inventory_monitoring where created >= $dt1 and created <= $now and company_id=$cid and qty_di = 1 group by item_id,branch_id) ininv2 on ininv2.item_id = inv.item_id and ininv2.branch_id=inv.branch_id ";

						if($is_cebu_hiq){

							$dtcbu = strtotime(date('m/d/Y',$dt2));
							$order_by = " order by parent_name asc, category_name asc ";
							 $leftJoinEnding = "left join inventory_ending ending on ending.branch_id= $b and ending.item_id = inv.item_id and ending.branch_id=inv.branch_id and ending.report_date = $dtcbu ";
						}


					}

				}
				if($b) {
					$parameters[] = $b;
					$branchwhere = " and inv.branch_id=? ";
				} else {
					$branchwhere = "";
				}
				if($r) {
					if($r == -1) $r = 0;
					$parameters[] = $r;
					$rackwhere = " and inv.rack_id=? ";
				} else {
					$rackwhere = "";
				}
				if($g){
					$group_by = " group by inv.item_id";
					$col = "amendinv2.amend_qty2,outinv2.out_qty2 ,ininv2.in_qty2,amendinv.amend_qty,outinv.out_qty ,ininv.in_qty, sum(inv.qty) as qty,ic.name as category_name,ic2.name as parent_name, i.product_cost,i.item_code,i.barcode,i.description,i.display_location,r.rack,b.name, u.name as unit_name";
					if($is_cebu_hiq){
						$col .= ",ending.qty as ending_qty";
					}
				} else {
					$group_by ="";
					$col = "amendinv2.amend_qty2,outinv2.out_qty2 ,ininv2.in_qty2,amendinv.amend_qty,outinv.out_qty ,ininv.in_qty, inv.*,ic.name as category_name,ic2.name as parent_name, i.product_cost,i.item_code,i.barcode,i.description,i.display_location,r.rack,b.name, u.name as unit_name";
				}

				 $q = "
						Select $col
						from inventories inv
						$leftjoinIn
						$leftjoinOut
						$leftjoinOutAmend
						$leftjoinIn2
						$leftjoinOut2
						$leftjoinOutAmend2
						$leftJoinEnding
						left join items i  on i.id = inv.item_id
						left join categories ic on ic.id=i.category_id
						left join categories ic2 on ic2.id = ic.parent
						left join units u on u.id=i.unit_id
						left join racks r on r.id=inv.rack_id
						left join branches b on b.id=inv.branch_id
						where  i.is_active= 1 and b.company_id=? $likewhere $branchwhere $rackwhere $group_by $order_by $l
						";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}

		public function getQtyOnDate($created=0, $item_id=0, $branch_id=0 ,$rack_id=0){

			/*if($rack_id){
				$whereRack = " and rack_id = $rack_id ";
			} else {

			}
			$q = "
				SELECT new_qty
				FROM `inventory_monitoring`
				WHERE created <= $created
				and item_id = $item_id
				and branch_id = $branch_id
				$whereRack
				order by created desc limit 1
			   ";
			*/

		}

		public function getRackItemsAndInventory($r, $b) {
			$parameters = array();
			if($r && $b) {
				$parameters[] = $r;
				$parameters[] = $b;

				$q = "Select inv.*, i.item_code from inventories inv
						left join racks r on r.id = inv.rack_id
						left join items i on i.id = inv.item_id
						where inv.rack_id=? and inv.branch_id=? and inv.qty > 0";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}
		public function getSurplusAvailable($r, $b,$i) {
			$parameters = array();
			if($r && $b) {
				$parameters[] = $r;
				$parameters[] = $b;
				$parameters[] = $i;

				$q = "Select sum(inv.qty) as totalqty, det.qty as pending_qty from inventories inv
						left join racks r on r.id = inv.rack_id
						left join
								(select sum(det.qty) as qty, det.item_id
								from wh_orders wh
								left join wh_order_details det on det.wh_orders_id = wh.id
								where det.item_id = $i and wh.status in (1,2,3) and wh.stock_out = 0 and det.is_surplus = 1) det on det.item_id = inv.item_id
						where inv.rack_id=$r and inv.branch_id=$b and inv.item_id =$i and inv.qty > 0";
				$data = $this->_db->query($q, $parameters);

				// return results if there is any
				if($data->count()) {
					return $data->first();
				}
			}
		}

		public function getCountItemInInventory($r, $b) {
			$parameters = array();
			if($r && $b) {
				$parameters[] = $r;
				$parameters[] = $b;

				$q = "Select count(*) as cnt from inventories where rack_id=? and branch_id=? and qty > 0";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->first();
				}
			}
		}

		public function updateInventory($r, $b, $i, $qty) {
			if($r && $b && $i) {
				if(!$qty) {
					$qty = 0;
				}
				$parameters[] = $qty;
				$parameters[] = $r;
				$parameters[] = $b;
				$parameters[] = $i;
				$q = "Update inventories set qty=? where rack_id=? and branch_id=? and item_id=?";
				$data = $this->_db->query($q, $parameters);

				if($data->count()) {
					return true;
				}
			}
		}

		public function allStockBaseOnItem($item_id = 0, $cid = 0, $bid = 0) {
			$parameters = array();
			if($item_id && $cid) {
				$parameters[] = $cid;
				$parameters[] = $item_id;
				if($bid) {
					$parameters[] = $bid;
					$branchwhere = " and b.id=?";
				} else {
					$branchwhere = '';
				}
				$q = "Select i.*,r.rack,b.name as bname,it.item_code, it.description
					from inventories i
					left join items it on it.id=i.item_id
					left join branches b on b.id=i.branch_id
					left join companies  c on c.id=b.company_id
					left join racks r on r.id=i.rack_id
					where   c.id=? and i.item_id=? and i.qty>0 $branchwhere";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}

		public function getNegativeQuantity($cid = 0) {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;

				$q = "Select i.*,r.rack,b.name as bname,it.item_code,it.barcode, it.description from inventories i left join items it on it.id=i.item_id left join branches b on b.id=i.branch_id left join companies  c on c.id=b.company_id left join racks r on r.id=i.rack_id where   c.id=?  and i.qty<0";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}

		public function countRecordCrit($branch = 0, $month = 0, $search) {
			$parameters = array();
			if($branch && $month) {

				$branch = (int)$branch;
				$month = (int)$month;

				$parameters[] = $branch;
				$parameters[] = $month;


				if($search) {
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$likewhere = " and (i.item_code like ? or i.barcode like ? or i.description like ? ) ";
				} else {
					$likewhere = '';
				}
				$q = " Select count(inv.qty) as cnt from
 					(Select sum(inv.qty) as qty, i.item_code, i.description, o.order_point as od1, o.order_qty as oq1, o2.order_point as od2,o2.order_qty as oq2 , tim.tidqty,sid.sidqty,wh.whqty
 						from items i
 						left join inventories inv on i.id=inv.item_id and inv.branch_id=?
 						left join (Select * from reorder_points where month=13 and is_active = 1) o on o.item_id=i.id and o.orderby_branch_id = $branch
 						left join (Select * from reorder_points where month=? and is_active = 1) o2 on o2.item_id=i.id and o2.orderby_branch_id = $branch
 						left join (Select sum(tid.qty) as tidqty, tid.item_id from transfer_inventory_details tid left join transfer_inventory_mon tim on tim.id=tid.transfer_inventory_id where tim.status=1 and tim.branch_id=$branch group by tid.item_id) tim on tim.item_id=i.id
 						left join (Select sum(wh.qty) as whqty, wh.item_id from wh_order_details wh left join wh_orders w  on w.id=wh.wh_orders_id where w.status in (1,2,3) and w.to_branch_id=$branch group by wh.item_id) wh on wh.item_id=i.id
 						left join (Select sum(sid.qty-sid.get_qty) as sidqty, si.item_id from supplier_order_details sid left join supplier_item si on si.id=sid.supplier_item_id left join supplier_orders so on so.id=sid.supplier_order_id where so.status=1 and so.branch_to=$branch group by si.item_id) sid on sid.item_id=i.id
 						where 1=1  $likewhere and i.is_active = 1
 						group by i.id
 						having  (ifnull(sum(inv.qty),0) + ifnull(tim.tidqty,0) + ifnull(wh.whqty,0) + ifnull(sid.sidqty,0)) < o.order_point or (ifnull(sum(inv.qty),0)+ ifnull(tim.tidqty,0)+ ifnull(wh.whqty,0)+ ifnull(sid.sidqty,0)) < o2.order_point) inv";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->first();
				}
			}
		}

		public function get_crit_record($branch = 0, $m = 0, $start, $limit, $search) {
			$parameters = array();
			if($branch && $m) {
				$branch = (int) $branch;
				$m = (int) $m;
				$parameters[] = $branch;
				$parameters[] = $m;

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
				$q = "Select ifnull(sum(inv.qty),0) as qty,inv.branch_id,b.name as bname,i.id, i.item_code, i.description,inv.item_id, o.order_point as od1, o.order_qty as odqty1, o.orderto_branch_id as ob1, o.orderto_supplier_id as os1, o2.order_point as od2,o2.order_qty as odqty2 , o2.orderto_branch_id as ob2, o2.orderto_supplier_id as os2,
					  tim.tidqty, sid.sidqty,wh.whqty
					  from items i
 					  left join inventories inv on i.id=inv.item_id and inv.branch_id=?
					  left join (Select * from reorder_points where month=13 and is_active = 1) o on o.item_id=i.id and o.orderby_branch_id = $branch
					  left join (Select * from reorder_points where month=? and is_active = 1) o2 on o2.item_id=i.id and o2.orderby_branch_id = $branch

						left join branches b on b.id=$branch
					  left join (Select sum(tid.qty) as tidqty, tid.item_id from transfer_inventory_details tid left join transfer_inventory_mon tim on tim.id=tid.transfer_inventory_id where tim.status=1 and tim.branch_id=$branch group by tid.item_id) tim on tim.item_id=i.id
					  left join (Select sum(wh.qty) as whqty, wh.item_id from wh_order_details wh left join wh_orders w  on w.id=wh.wh_orders_id where w.status in (1,2,3) and w.to_branch_id=$branch group by wh.item_id) wh on wh.item_id=i.id
 						left join (Select sum(sid.qty-sid.get_qty) as sidqty, si.item_id from supplier_order_details sid left join supplier_item si on si.id=sid.supplier_item_id left join supplier_orders so on so.id=sid.supplier_order_id where so.status=1 and so.branch_to=$branch group by si.item_id) sid on sid.item_id=i.id
					  where 1=1  $likewhere and i.is_active = 1 group by i.id  having   (ifnull(sum(inv.qty),0) + ifnull(tim.tidqty,0)+ ifnull(wh.whqty,0)+ ifnull(sid.sidqty,0)) < o.order_point or (ifnull(sum(inv.qty),0)+ ifnull(tim.tidqty,0)+ ifnull(wh.whqty,0) + ifnull(sid.sidqty,0)) < o2.order_point $l";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}

		public function get_racking($item_id = 0, $branch_id = 0, $rack_tag = 0,$item_ex=0,$specific_rack_id=0,$surplus_rack=0) {
			$parameters = array();
			if($item_id && $branch_id) {

				$parameters[] = $item_id;
				$parameters[] = $branch_id;
				$wheretags = "";
				$leftJoinItemEx = "";
				$whereSpecificRack="";
				$whereItemEx = "";
				$whereSurplus = "";
				if($rack_tag){
					$parameters[] = $rack_tag;
					$wheretags = " and r.rack_tag != ? ";
				}
				if($specific_rack_id){
					$parameters[] = $specific_rack_id;
					$whereSpecificRack = " and i.rack_id = ? ";
				}
				$get_stock_order = Configuration::getValue('get_stock_order');
				$order_rack = "1 = 1";
				if(isset($get_stock_order) && $get_stock_order == 0 ){
					$order_rack = " r.rack asc ";
				} else if (isset($get_stock_order) && $get_stock_order == 1 ){
					$order_rack = " rack_qty asc ";
				}
				if($item_ex){
					$leftJoinItemEx = " left join allowed_items_on_rack allowed on allowed.item_id = i.item_id and allowed.tag_id = r.rack_tag ";
					$whereItemEx = " and allowed.id is null ";
				}
				if($surplus_rack){
					$surplus_rack= (int)$surplus_rack;
					$whereSurplus = " and i.rack_id != $surplus_rack ";
				}


				   $q = "Select IFNULL(sum(i.qty),0) as rack_qty,i.rack_id,r.rack,r.description as rack_description,r.stock_man,b.name as bname,it.item_code,it.barcode, it.description from inventories i left join items it on it.id=i.item_id left join branches b on b.id=i.branch_id left join racks r on r.id=i.rack_id $leftJoinItemEx
					where   i.item_id= ? $whereItemEx and i.branch_id=?  $whereSurplus $wheretags $whereSpecificRack group by   r.rack
 					ORDER BY CASE WHEN r.rack not like '%Display%' then 1 else 2 end ,CASE WHEN r.description not like '%Display%' then 1 else 2 end , $order_rack ";
				//ORDER BY CASE WHEN r.rack not like '%Display%' then 1 else 2 end, r.rack ASC
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}

		public function truncateTable($tblName = '') {
			$parameters = array();
			if($tblName) {
				$q = "TRUNCATE TABLE $tblName";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return true;
				}

				return false;
			}
		}

	}

?>