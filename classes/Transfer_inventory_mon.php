<?php
	class Transfer_inventory_mon extends Crud{
		protected $_table='transfer_inventory_mon';
		public function __construct($t = NULL){
			parent::__construct($t);
		}
		public function getUnread($cid= 0 , $branch = 0){
			$parameters = array();
			if($cid && $branch){
				$parameters[] =$cid;
				$parameters[] =$branch;
				$q= "Select count(id) as cnt from transfer_inventory_mon where notified = 0 and company_id = ? and branch_from = ? and status = 1 and get_stock = 0";
				$data = $this->_db->query($q,$parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function updateNotif($cid= 0 , $branch = 0,$user=0){
			$parameters = array();
			if($cid && $branch){
				$parameters[] =$user;
				$parameters[] =$cid;
				$parameters[] =$branch;
				$q= "update transfer_inventory_mon set notified = ? where notified = 0 and company_id = ? and branch_from = ? and status = 1 and get_stock = 0";
				$data = $this->_db->query($q,$parameters);
				if($data->count()){
					// return the data if exists
					return true;
				}
			}
		}
		public function getStatusOne($cid= 0 , $branch = 0){
			$parameters = array();
			if($cid){
				$parameters[] =$cid;
				$whereBranch = "";
				if($branch){
					$parameters[] =$branch;
					$whereBranch = " and t.branch_id = ?";
				}

				 $q= "Select wh2.wh_id, wh.dr,wh.invoice,wh.pr , b.name,t.id,t.created,t.from_where,t.branch_from,t.supplier_id,t.from_where, t.get_stock,b2.name as name2, sup.name as supname, t.del_schedule, t.driver, t.helpers,
 						t.truck_id,t.branch_id,t.payment_id, t.remarks
 						from transfer_inventory_mon t
 						left join (select id,dr,invoice,pr,payment_id from wh_orders group by payment_id) wh on wh.payment_id = t.payment_id
 						left join (select id as wh_id from wh_orders) wh2 on wh2.wh_id = t.from_od  left join branches b on b.id = t.branch_id
 						left join branches b2 on b2.id=t.branch_from left join suppliers sup on sup.id=supplier_id
 						where t.status=1 and t.company_id=? $whereBranch and t.is_active=1 order by t.id asc";
				$data = $this->_db->query($q,$parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function getStatusOneFrom($cid= 0 , $branch = 0){
			$parameters = array();
			if($cid && $branch){
				$parameters[] =$cid;
				$parameters[] =$branch;
				$q= "Select b.name,t.id,t.created,t.from_where ,t.branch_from,t.supplier_id,b2.name as name2, sup.name as supname, t.del_schedule, t.driver, t.helpers, t.truck_id from transfer_inventory_mon t left join branches b on b.id = t.branch_id left join branches b2 on b2.id=t.branch_from left join suppliers sup on sup.id=supplier_id where t.status=1 and t.company_id=? and t.branch_from = ? and t.is_active=1";
				$data = $this->_db->query($q,$parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function countRecord($cid=0,$branch_id = 0,$status = 2){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$where_branch = "";
				$where_status="";
				if($branch_id){
					$parameters [] = $branch_id;
					$where_branch = " and t.branch_id = ? ";
				}
				if($status){
					$status = (int) $status;
					$where_status = " and t.status = $status";
				}
				$q = "Select count(t.id) as cnt from transfer_inventory_mon t left join branches b on b.id = t.branch_id where  t.company_id=? $where_branch  $where_status";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function get_sales_record($cid,$start,$limit,$branch_id = 0,$status=2){
			$parameters = array();
			if($cid){
				$parameters[] = $cid;
				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}
				$where_branch = "";
				$where_status = "";
				if($branch_id){
					$parameters [] = $branch_id;
					$where_branch = " and t.branch_id = ? ";
				}
				if($status){
					$status = (int) $status;
					$where_status = " and t.status = $status";
				}
				$q= "Select b.name,t.id,t.created,t.from_where,t.branch_from,t.supplier_id,b2.name as name2, sup.name as supname, t.remarks  from transfer_inventory_mon t left join branches b on b.id = t.branch_id  left join branches b2 on b2.id=t.branch_from left join suppliers sup on sup.id=supplier_id  where t.company_id=? $where_branch $where_status $l";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}

		public function getOrderItems($cid= 0,$id = 0, $is_backload = 0){
			$parameters = array();
			if($cid && $id){
				$parameters[] =$cid;
				$parameters[] =$id;

				if($is_backload == 1){
					$leftjoin = "	left join wh_orders wh on wh.payment_id = t.payment_id and t.payment_id != 0";
				} else {
					$leftjoin = "	left join wh_orders wh on wh.id = t.from_od and t.from_od != 0";
				}
				$q= "SELECT td.*, t.id as tid, t.remarks as t_ref_number , t.created as dt_created,b.name as bname,wh.remarks as wh_remarks,
 						b.address as baddress,b2.name as b2name, b2.address as b2address ,
 						 	i.item_code,i.description,c.name as cname,c.address as caddress, m.lastname as member_name, st.name as station_name
 						 	FROM transfer_inventory_details td
 						 	left join transfer_inventory_mon t on t.id = td.transfer_inventory_id
 						 	left join branches b on b.id=t.branch_id
 						 	left join branches b2 on b2.id=t.branch_from
 						 	left join items i on i.id = td.item_id
 						 	left join companies c on c.id = t.company_id
							$leftjoin
 						 	left join members m on m.id = wh.member_id
 						 	left join stations st on st.id = wh.station_id
 						 	where t.company_id = ? and t.id = ?";
				$data = $this->_db->query($q,$parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function getTransfer($from = 0,$to= 0,$branch_id=0,$item_id=0){
			$parameters = array();
			$branch_id = (int) $branch_id;
			$whereItem = "";
			if($item_id){
				$item_id = (int) $item_id;
				$whereItem = "and i.id = $item_id ";

			}
			$q= "SELECT td.*, t.id as tid, t.remarks as t_ref_number , t.created as dt_created,
                        i.item_code,i.description, r1.rack as rack_from, r2.rack as rack_to
                        FROM transfer_inventory_details td
                        left join transfer_inventory_mon t on t.id = td.transfer_inventory_id
                        left join racks r1 on r1.id = td.rack_id_from
                        left join racks r2 on r2.id = td.rack_id_to
                        left join items i on i.id = td.item_id
                        where t.created >= $from and t.created <= $to and t.branch_id = $branch_id and t.from_where = 'From transfer' $whereItem ";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}

		}

		public function getTransferAssembly($from = 0,$to= 0,$branch_id=0,$item_id=0){
			$parameters = array();
			$branch_id = (int) $branch_id;
			$whereItem = "";
			if($item_id){
				$item_id = (int) $item_id;
				$whereItem = "and i.id = $item_id ";
			}

			$q= "SELECT td.*, t.id as tid, t.remarks as t_ref_number , t.created as dt_created,
                        i.item_code,i.description, r1.rack as rack_from,
                        r2.rack as rack_to, rt1.tag_name as tag_name_from, rt2.tag_name as tag_name_to
                        FROM transfer_inventory_details td
                        left join transfer_inventory_mon t on t.id = td.transfer_inventory_id
                        left join racks r1 on r1.id = td.rack_id_from
                        left join racks r2 on r2.id = td.rack_id_to
                        left join rack_tags rt1 on rt1.id = r1.rack_tag
                        left join rack_tags rt2 on rt1.id = r2.rack_tag
                        left join items i on i.id = td.item_id
                        where (rt1.id = 1 or rt2.id = 1) and t.created >= $from and t.created <= $to and t.branch_id = $branch_id and t.from_where = 'From transfer'  $whereItem";

			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}

		}

		public function getServiceReturn($from = 0,$to= 0,$branch_id=0,$item_id=0){
			$parameters = array();
			$branch_id = (int) $branch_id;

			$whereItem = "";
			if($item_id){
				$item_id = (int) $item_id;
				$whereItem = "and i.id = $item_id ";
			}

			$q= "SELECT td.*, t.id as tid, t.remarks as t_ref_number , t.created as dt_created,
                        i.item_code,i.description, r1.rack as rack_from, r2.rack as rack_to
                        FROM transfer_inventory_details td
                        left join transfer_inventory_mon t on t.id = td.transfer_inventory_id
                        left join racks r1 on r1.id = td.rack_id_from
                        left join racks r2 on r2.id = td.rack_id_to
                        left join items i on i.id = td.item_id
                        where t.created >= $from and t.created <= $to and t.branch_id = $branch_id and t.from_where = 'From service return item' $whereItem ";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}

		}
	}
?>