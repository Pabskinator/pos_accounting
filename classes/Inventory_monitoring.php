<?php
	class Inventory_monitoring extends Crud{
		protected $_table = 'inventory_monitoring';
		public function __construct($i=null){
			parent::__construct($i);
		}
		public function countRecord($cid,$search='',$b=0,$r=0,$date_from=0,$date_to=0,$branch_id2=0){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;

				if($search) {
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$likewhere = " and (i.item_code like ? or i.barcode like ? or i.description like ? or inv.remarks like ?) ";
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
				if($date_from && $date_to){
					$date_from = strtotime($date_from);
					$date_to = strtotime($date_to . "1 day -1 sec");
					$whereDate = " and inv.created >= $date_from and inv.created <= $date_to ";
				} else {
					$whereDate = "";
				}
				if($branch_id2){
					$parameters[] = $branch_id2;
					$whereTransferTo = " and wh.to_branch_id = ? and wh.member_id = 0";
				} else {
					$whereTransferTo = "";
				}
				$q = "Select count(inv.id) as cnt
					from inventory_monitoring inv
					left join items i  on i.id = inv.item_id
					left join branches b on b.id=inv.branch_id
					left join wh_orders wh on wh.id = SUBSTR(inv.remarks,LOCATE('Order id #',inv.remarks)+10,LOCATE(')',inv.remarks) - LOCATE('#',inv.remarks)-1)
					where b.company_id=?  $likewhere $branchwhere $rackwhere $whereDate $whereTransferTo";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function get_sales_record($cid,$start,$limit,$search='',$b=0,$r=0,$date_from,$date_to,$branch_id2=0){
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
					$parameters[] = "%$search%";
					$likewhere = " and (i.item_code like ? or i.barcode like ? or i.description like ? or inv.remarks like ?) ";
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
					if($r == -1) $r = 0;
					$parameters[] = $r;
					$rackwhere = " and inv.rack_id=? ";
				} else {
					$rackwhere = "";
				}
				if($date_from && $date_to){
					$date_from = strtotime($date_from);
					$date_to = strtotime($date_to . "1 day -1 sec");
					$whereDate = " and inv.created >= $date_from and inv.created <= $date_to ";
				} else {
					$whereDate = "";
				}

				if($branch_id2){
					$parameters[] = $branch_id2;
					$whereTransferTo = " and wh.to_branch_id = ? and wh.member_id = 0";
				} else {
					$whereTransferTo = "";
				}

				 $now = time();

				 $q= "Select inv.*,i.item_code,i.barcode,i.description,r.rack,b.name, u.firstname , u.lastname, amend.a_remarks,
						wh.invoice,wh.dr,wh.pr,m.lastname as member_name, wh.id as wh_id, b2.name as branch_name2
						from inventory_monitoring inv
						left join items i  on i.id = inv.item_id
						left join racks r on r.id=inv.rack_id
						left join branches b on b.id=inv.branch_id
						left join users u on u.id = inv.user_id
						left join wh_orders wh on wh.id = SUBSTR(inv.remarks,LOCATE('Order id #',inv.remarks)+10,LOCATE(')',inv.remarks) - LOCATE('#',inv.remarks)-1)
						left join members m on m.id = wh.member_id
						left join branches b2 on b2.id = wh.to_branch_id
						left join (Select item_id, rack_id, branch_id , created , remarks as a_remarks from inventory_ammend)
						amend on amend.item_id = inv.item_id and amend.rack_id = inv.rack_id and amend.branch_id = inv.branch_id and amend.created = inv.created
						where b.company_id=? $likewhere $branchwhere $rackwhere $whereDate $whereTransferTo
						order by inv.created desc, inv.id desc $l ";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}

		public function isFoundItem($item_id = 0 ,$rack_id = 0,$branch_id = 0,$dt_from = 0,$dt_to = 0,$qty = 0){
			$parameters = [];

			if($item_id && $rack_id && $branch_id && $dt_from && $dt_to && $qty){
				$parameters[] = $qty;
				$parameters[] = $item_id;
				$parameters[] = $rack_id;
				$parameters[] = $branch_id;
				$parameters[] = $dt_from;
				$parameters[] = $dt_to;
				$q = "  select count(*) as cnt
 						from inventory_monitoring
						where

						 qty = ?
						and item_id = ?
						and rack_id = ?
						and branch_id = ?
						and created >= ?
						and created <= ?
						and (remarks ='Add found item' or  prev_qty = 0) limit 1";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}

		}

		public function criticalOrder($branchid = 0,$dt1=0,$dt2=0) {

			$parameters = array();


			$whereBranch="";
			if($branchid){
				$whereBranch = " and wh.branch_id = ? ";
				$parameters[] = $branchid;
			}
			$parameters[] = $dt1;
			$parameters[] = $dt2;

			$q = "
				    SELECT  month(from_unixtime(s.sold_date)) as m ,
				    sum(s.qtys) as total_qty, s.item_id,s.terminal_id ,b.name as branch_name, i.item_code,i.description
			      	FROM sales s
			      	left join terminals t on t.id = terminal_id
			      	left join branches b on b.id = t.branch_id
			      	left join items i on i.id = s.item_id
			      	left join (Select payment_id, branch_id from wh_orders) wh on wh.payment_id = s.payment_id
			      	where
			      	1=1 $whereBranch
			      	and s.sold_date >= ? and s.sold_date <= ?
			      	group by month(from_unixtime(s.sold_date)), s.item_id
			   ";

				// submit the query to DB class
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}

		}

	}
?>