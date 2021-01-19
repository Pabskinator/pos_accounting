<?php
	class Wh_order extends Crud {
		protected $_table = 'wh_orders';

		public function __construct($w = null) {
			parent::__construct($w);
		}

		public function get_record_pending($cid, $start, $limit, $s = '', $branch_id = 0) {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$parameters[] = 1;
				$whereSearch = "";
				$whereBranch = "";

				if($limit) {
					$l = " LIMIT $start,$limit";
				} else {
					$l = '';
				}
				if($s) {
					$parameters[] = "%$s%";
					$parameters[] = "%$s%";
					$whereSearch = " and (i.item_code like ? or i.description like ?)";
				}
				if($branch_id) {
					$parameters[] = $branch_id;
					$whereBranch = " and o.branch_id = ? ";
				}

				$q = "Select od.*, o.dr, o.invoice,o.pr, i.item_code,i.description , i.is_bundle, ci.item_id_set
					from wh_order_details od
					left join wh_orders o on od.wh_orders_id=o.id
					left join items i on i.id = od.item_id
					left join (Select DISTINCT(item_id_set) as item_id_set from composite_items) ci on ci.item_id_set = od.item_id
					where o.company_id= ? and o.is_active=? and o.status in(1,2,3) and o.stock_out=0 $whereSearch $whereBranch $l ";


				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}

		public function countRecordPending($cid = 0, $s = '', $branch_id = 0) {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$parameters[] = 1;
				$whereSearch = "";
				$whereBranch = "";
				if($s) {
					$parameters[] = "%$s%";
					$parameters[] = "%$s%";

					$whereSearch = " and (i.item_code like ? or i.description like ? ) ";
				}
				if($branch_id) {
					$parameters[] = $branch_id;
					$whereBranch = " and o.branch_id = ? ";
				}
				$q = "Select count(od.id) as cnt  from wh_order_details od left join wh_orders o on od.wh_orders_id=o.id left join items i on i.id = od.item_id where o.company_id = ? and o.is_active=? and o.status in(1,2,3) and o.stock_out=0  $whereSearch $whereBranch";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function backloadByUsed($payment_id = 0) {
			$parameters = array();
			if($payment_id) {
				$parameters[] = $payment_id;
				$q = "Select o.*,i.item_code,i.description ,i.barcode, p.price, (o.price_adjustment + p.price) as adjusted_price
					from wh_orders w
					left join wh_order_details o on o.wh_orders_id = w.id
					left join items i on i.id = o.item_id left join prices p on p.id=o.price_id  where o.is_use=?";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}

		public function backloadNotUse($member_id = 0) {
			$parameters = array();
			if($member_id) {
				$parameters[] = $member_id;
				$q = "Select o.*,i.item_code,i.description ,i.barcode, p.price, (o.price_adjustment + p.price) as adjusted_price from wh_orders w left join wh_order_details o on o.wh_orders_id = w.id left join items i on i.id = o.item_id left join prices p on p.id=o.price_id  where w.member_id = ? and o.is_use=0 and o.backload_qty > 0 ";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}

		public function getFullDetails($id = 0) {
			$parameters = array();
			if($id) {
				$parameters[] = $id;
				$q = "Select sc.name as shipping_company_name,
						o.*,b.name as branch_name,b2.name as branch_name2, m.lastname as mln,m.cel_number,
						m.firstname as mfn, m.middlename as mmn ,m.salestype,m.personal_address,
					    u.lastname as uln, u.firstname as ufn, u.middlename as umn, m.personal_address,
					    m.id as member_id, m.terms,m.tin_no,
					    m.region,st.name as sales_type_name, m.contact_number
					    from wh_orders o
					    left join branches b on b.id=o.branch_id
					    left join branches b2 on b2.id=o.to_branch_id
					    left join members m on m.id = o.member_id
					    left join salestypes st on st.id = m.salestype
					    left join users u on u.id=o.user_id
					    left join shipping_companies sc on sc.id = o.shipping_company_id
					    where o.id=? and o.is_active=1 ";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->first();
				}
			}
		}

		public function getFullDetailsByPayment($id = 0) {
			$parameters = array();
			if($id) {
				$parameters[] = $id;
				$q = "Select o.*,b2.name as branch_name_to, b2.description as description_to, b.name as branch_name, m.lastname as mln, m.firstname as mfn, m.middlename as mmn ,
			m.salestype, u.lastname as uln, u.firstname as ufn, u.middlename as umn, m.personal_address, m.id as member_id
			from wh_orders o
			left join branches b on b.id=o.branch_id
			left join branches b2 on b2.id = o.to_branch_id
			left join members m on m.id = o.member_id
			left join users u on u.id=o.user_id
			where o.payment_id=? and o.is_active=1 ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->first();
				}
			}
		}

		public function countRecordOrder($cid, $user_id = 0, $member_id = 0, $branch_ids = 0, $my_id, $status, $dt1 = 0, $dt2 = 0,$branch_id=0,$salestype=0,$for_pickup=0,$assemble=0,$search=''){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$where_user = '';
				$where_member = '';
				$where_branch = '';
				$where_dt = '';
				$where_status_join = '';
				if($user_id) {
					$parameters[] = $user_id;
					$where_user = " and o.user_id=? ";
				}
				if($member_id) {
					$parameters[] = $member_id;
					$where_member = " and o.member_id=? ";
				}
				if($branch_ids) {
					$my_id = (int) $my_id;

					$exbranch = explode(",",$branch_ids);
					$list_branch = "";
					foreach($exbranch as $bid){
						$bid = (int) $bid;
						$list_branch .= $bid . ",";
					}
					$list_branch = rtrim($list_branch,",");
					$where_branch = " and (o.branch_id in ($list_branch) or o.user_id=$my_id) ";
				}
				if($status) {
					$status = (int) $status;
					$where_status = "and o.status = $status";
					$where_status_join = "and od.status = $status";
				} else {
					$where_status = "and o.status in (1,2,3)";
					$where_status_join = "and od.status in (1,2,3)";
				}
				if($dt1 && $dt2) {
					$where_dt = " and o.approved_date >= $dt1 and o.approved_date <= $dt2";
				}
				$whereBranch = "";
				$whereType="";
				$wherePickup="";
				$whereAssemble="";
				if($branch_id){
					$branch_id = (int)$branch_id;
					$whereBranch = "and o.branch_id = $branch_id ";
				}
				if($salestype){
					$salestype = (int) $salestype;
					$whereType = "and st.id = $salestype ";
				}
				if($for_pickup){
					if($for_pickup == 1){
						$p = 1;
					} else if($for_pickup == 2){
						$p = 0;
					}  else if ($for_pickup == 3){
						$p = 2;
					}
					$wherePickup = " and o.for_pickup = $p ";
				}
				if($assemble){
					if($assemble == 1){
						$whereAssemble = " and o.has_assemble_item = 1";
					} else if ($assemble == 2){
						$whereAssemble = " and o.has_assemble_item = 0";
					}
				}
				$whereSearch = '';

				if($search){

					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$whereSearch = "and (o.id like ? or o.invoice like ? or o.dr like ? or o.pr like ?) ";
				}
				$q = "Select count(*) as cnt
						from wh_orders o
						left join branches b on b.id=o.branch_id
						left join branches b2 on b2.id=o.to_branch_id
						left join shipping_companies sc on sc.id = o.shipping_company_id
						left join members m on m.id = o.member_id
						left join salestypes st on st.id = m.salestype
						left join users u on u.id = o.user_id
						left join trucks t on t.id = o.truck_id
						where o.company_id=? $where_user $where_member $where_branch and o.is_active=1 $where_status $where_dt $whereBranch $whereType $wherePickup $whereAssemble $whereSearch order by o.id desc";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->first();
				}
			}
		}
		public function getOrders($cid, $user_id = 0, $member_id = 0, $branch_ids = 0, $my_id, $status, $dt1 = 0, $dt2 = 0,$start, $limit,$branch_id=0,$salestype=0,$for_pickup=0,$assemble=0,$search='') {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$where_user = '';
				$where_member = '';
				$where_branch = '';
				$where_dt = '';
				$where_status_join = '';
				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}
				if($user_id) {
					$parameters[] = $user_id;
					$where_user = " and o.user_id=? ";
				}
				if($member_id) {
					$parameters[] = $member_id;
					$where_member = " and o.member_id=? ";
				}
				if($branch_ids) {
					$my_id = (int)$my_id;
					$exbranch = explode(",",$branch_ids);
					$list_branch = "";
					foreach($exbranch as $bid){
						$bid = (int) $bid;
						$list_branch .= $bid . ",";
					}
					$list_branch = rtrim($list_branch,",");
					$where_branch = " and (o.branch_id in ($list_branch) or o.user_id=$my_id) ";
				}
				if($status) {
					$status = (int)$status;
					$where_status = "and o.status = $status";
					$where_status_join = "and od.status = $status";
				} else {
					$where_status = "and o.status in (1,2,3)";
					$where_status_join = "and od.status in (1,2,3)";
				}
				if($dt1 && $dt2) {
					$where_dt = " and o.approved_date >= $dt1 and o.approved_date <= $dt2";
				}
				$whereBranch = "";
				$whereType="";
				$wherePickup="";
				$whereAssemble="";
				if($branch_id){
					$branch_id = (int)$branch_id;
					$whereBranch = "and o.branch_id = $branch_id ";
				}
				if($salestype){
					$salestype = (int)$salestype;
					$whereType = "and st.id = $salestype ";
				}
				if($for_pickup){
					if($for_pickup == 1){
						$p = 1;
					} else if($for_pickup == 2){
						$p = 0;
					}  else if ($for_pickup == 3){
						$p = 2;
					}
					$wherePickup = " and o.for_pickup = $p ";
				}
				if($assemble){
					if($assemble == 1){
						$whereAssemble = " and o.has_assemble_item = 1";
					} else if ($assemble == 2){
						$whereAssemble = " and o.has_assemble_item = 0";
					}
				}
				$whereSearch = '';

				if($search){

					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$whereSearch = "and (o.id like ? or o.invoice like ? or o.dr like ? or o.pr like ?) ";
				}

				$colPriceGroup="";
				$joinPriceGroup="";
				if(Configuration::thisCompany('cebuhiq')){

					$colPriceGroup=", pg.name as price_group_name ";
					$joinPriceGroup="  left join price_groups pg on pg.id = o.price_group_id ";
				}

				 $q = "Select o.*,sc.name as shipping_name, mc.is_cod,
				t.name as truck_name, t.description as truck_description,b.name as branch_name,
						b2.name as branch_name_to,b2.member_id as b2_member_id, m.lastname as mln, m.firstname as mfn,
						m.personal_address,m.salestype, m.with_inv, wh.total_price, u.lastname, u.firstname, u.middlename,
						st.name as sales_type_name $colPriceGroup
						from wh_orders o
						inner join (Select o.wh_orders_id, sum(((o.price_adjustment + p.price) * o.qty) + o.member_adjustment)  as total_price
						 				from wh_order_details o
						 				left join wh_orders od on od.id = o.wh_orders_id
						 				left join items i on i.id = o.item_id
						 				left join prices p on p.id=o.price_id
						 				where o.is_active=1 $where_status_join
						 				group by o.wh_orders_id)
						 				wh on wh.wh_orders_id=o.id
						left join branches b on b.id=o.branch_id
						$joinPriceGroup
						left join branches b2 on b2.id=o.to_branch_id
						left join shipping_companies sc on sc.id = o.shipping_company_id
						left join members m on m.id = o.member_id
						left join salestypes st on st.id = m.salestype
						left join users u on u.id = o.user_id
						left join trucks t on t.id = o.truck_id
						left join (select is_cod,payment_id from member_credit group by payment_id order by payment_id desc) mc on mc.payment_id = o.payment_id
						where o.company_id=? $where_user $where_member $where_branch and o.is_active=1 $where_status $where_dt $whereBranch $whereType $wherePickup $whereAssemble $whereSearch order by o.id desc $l";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}

		}

		public function getOrderCount($cid, $user_id = 0, $member_id = 0, $branch_ids = 0, $my_id) {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$where_user = '';
				$where_member = '';
				$where_branch = '';
				if($user_id) {
					$parameters[] = $user_id;
					$where_user = " and o.user_id=? ";
				}
				if($member_id) {
					$parameters[] = $member_id;
					$where_member = " and o.member_id=? ";
				}
				if($branch_ids) {
					$my_id = (int)$my_id;
					$exbranch = explode(",",$branch_ids);
					$list_branch = "";
					foreach($exbranch as $bid){
						$bid = (int) $bid;
						$list_branch .= $bid . ",";
					}
					$list_branch = rtrim($list_branch,",");
					$where_branch = " and (o.branch_id in ($list_branch) or o.user_id=$my_id) ";

				}

				$q = "Select count(o.id) as cnt, o.status from wh_orders o  where o.company_id=? $where_user $where_member $where_branch and o.is_active=1 and o.status in(1,2,3) group by o.status";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}

		public function getOrdersLog($cid, $user_id = 0, $member_id = 0, $from = 0, $to = 0, $order_type = '', $search = '', $branch_id = 0, $truck_id = 0) {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$where_user = '';
				$where_member = '';
				$wheretimeframe = '';
				$where_od_type = "";
				$where_search = "";
				$where_truck = "";

				if($user_id) {
					$parameters[] = $user_id;
					$where_user = " and o.user_id=? ";
				}
				if($member_id) {
					$parameters[] = $member_id;
					$where_member = " and o.member_id=? ";
				}

				if($from && $to) {
					$dateStart = strtotime($from);
					$dateEnd = strtotime($to . '1 day -1 sec');
					$wheretimeframe = " and o.is_scheduled >=$dateStart and o.is_scheduled <= $dateEnd ";
				}
				if($search) {
					if(is_numeric($search)) {
						$where_search = "and (o.id = $search or o.invoice = $search or o.dr = $search or o.pr = $search) ";
					} else {
						$parameters[] = "%$search%";
						$where_search = "and (m.lastname like ?) ";
					}

				}


				if($order_type == 1 && !$member_id) { // branch to branch
					$where_od_type = " and o.member_id = 0 ";
				} else if($order_type == 2 && !$member_id) { // branch to member
					$where_od_type = " and o.member_id != 0 ";
				}

				if($branch_id) {
					$branch_id = (int) $branch_id;
					$where_branch = " and (o.branch_id = $branch_id or (o.to_branch_id = $branch_id and o.member_id =0 ))";
				} else {
					$where_branch = "";
				}

				if($truck_id) {
					$truck_id = (int) $truck_id;
					$where_truck = " and o.truck_id = $truck_id";
				}

				$colPriceGroup="";
				$joinPriceGroup="";
				if(Configuration::thisCompany('cebuhiq')){

					$colPriceGroup=", pg.name as price_group_name ";
					$joinPriceGroup="  left join price_groups pg on pg.id = o.price_group_id ";
				}


				$q = "Select o.*,sc.name as shipping_name,t.name as truck_name, t.description as truck_description,
						b.name as branch_name,b2.name as branch_name_to,
						 m.lastname as mln, m.firstname as mfn,m.personal_address,
						  wh.total_price, u.lastname, u.firstname, u.middlename, st.name as sales_type_name $colPriceGroup
						  from wh_orders o
						  left join (Select o.wh_orders_id, sum(((o.price_adjustment + p.price) * o.qty) + o.member_adjustment) as total_price
						  	from wh_order_details o left join items i on i.id = o.item_id left join prices p on p.id=o.price_id  where o.is_active=1
						  	group by o.wh_orders_id)
						  wh on wh.wh_orders_id=o.id
						  left join branches b on b.id=o.branch_id
						  left join branches b2 on b2.id=o.to_branch_id
						  $joinPriceGroup
						  left join shipping_companies sc on sc.id = o.shipping_company_id
						  left join members m on m.id = o.member_id
						  left join salestypes st on st.id = m.salestype
						  left join users u on u.id = o.user_id
						   left join trucks t on t.id = o.truck_id where o.company_id=? $where_user $where_member $wheretimeframe and o.is_active=1 and o.status = 4 and o.for_pickup = 0 and o.from_service = 0  $where_od_type $where_search $where_branch $where_truck order by o.is_scheduled desc, o.truck_id desc  limit 100";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}

		public function countOrdersLog($cid, $user_id = 0, $member_id = 0, $from = 0, $to = 0, $order_type = '') {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$where_user = '';
				$where_member = '';
				$wheretimeframe = '';
				$where_od_type = "";
				if($user_id) {
					$parameters[] = $user_id;
					$where_user = " and o.user_id=? ";
				}
				if($member_id) {
					$parameters[] = $member_id;
					$where_member = " and o.member_id=? ";
				}

				if($from && $to) {
					$dateStart = strtotime($from);
					$dateEnd = strtotime($to . '1 day -1 sec');
					$wheretimeframe = " and o.is_scheduled >=$dateStart and o.is_scheduled <= $dateEnd ";
				}

				if($order_type == 1 && !$member_id) { // branch to branch
					$where_od_type = " and o.member_id = 0 ";
				} else if($order_type == 2 && !$member_id) { // branch to member
					$where_od_type = " and o.member_id != 0 ";
				}

				$q = "Select count(o.*) as cnt from wh_orders o left join (Select o.wh_orders_id, sum(((o.price_adjustment + p.price) * o.qty) + o.member_adjustment) as total_price from wh_order_details o left join items i on i.id = o.item_id left join prices p on p.id=o.price_id  where o.is_active=1 group by o.wh_orders_id) wh on wh.wh_orders_id=o.id left join branches b on b.id=o.branch_id  left join branches b2 on b2.id=o.to_branch_id left join shipping_companies sc on sc.id = o.shipping_company_id left join members m on m.id = o.member_id  left join users u on u.id = o.user_id left join trucks t on t.id = o.truck_id where o.company_id=? $where_user $where_member $wheretimeframe and o.is_active=1 and o.status = 4 and o.for_pickup = 0 and o.from_service != 0  $where_od_type ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->first();
				}
			}
		}

		public function getOrdersPickup($cid, $user_id = 0, $member_id = 0, $from = 0, $to = 0, $pickup_filter_type = '', $search = '', $branch_id = 0) {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$where_user = '';
				$where_member = '';
				$wheretimeframe = '';
				$where_od_type = '';
				$where_search = '';
				if($user_id) {
					$parameters[] = $user_id;
					$where_user = " and o.user_id=? ";
				}
				if($member_id) {
					$parameters[] = $member_id;
					$where_member = " and o.member_id=? ";
				}

				if($from && $to) {
					$dateStart = strtotime($from);
					$dateEnd = strtotime($to . '1 day -1 sec');

					$wheretimeframe = " and o.is_scheduled >=$dateStart and o.is_scheduled <= $dateEnd ";
				}
				if($pickup_filter_type == 1 && !$member_id) { // branch to branch
					$where_od_type = " and o.member_id = 0 ";
				} else if($pickup_filter_type == 2 && !$member_id) { // branch to member
					$where_od_type = " and o.member_id != 0 ";
				}
				if($search) {
					if(is_numeric($search)) {
						$where_search = "and (o.id = $search or o.invoice = $search or o.dr = $search or o.pr = $search) ";
					} else {
						$parameters[] = "%$search%";
						$where_search = "and (m.lastname like ?) ";
					}
				}
				if($branch_id) {
					$branch_id = (int) $branch_id;
					$where_branch = " and (o.branch_id = $branch_id or (o.to_branch_id = $branch_id and o.member_id =0 ))";
				} else {
					$where_branch = "";
				}

				$q = "Select o.*,sc.name as shipping_name,t.name as truck_name, t.description as truck_description,b.name as branch_name,b2.name as branch_name_to, m.lastname as mln, m.firstname as mfn,m.personal_address, wh.total_price, u.lastname, u.firstname, u.middlename from wh_orders o left join (Select o.wh_orders_id, sum(((o.price_adjustment + p.price) * o.qty) + o.member_adjustment) as total_price from wh_order_details o left join items i on i.id = o.item_id left join prices p on p.id=o.price_id  where o.is_active=1 group by o.wh_orders_id) wh on wh.wh_orders_id=o.id left join branches b on b.id=o.branch_id  left join branches b2 on b2.id=o.to_branch_id left join shipping_companies sc on sc.id = o.shipping_company_id left join members m on m.id = o.member_id  left join users u on u.id = o.user_id left join trucks t on t.id = o.truck_id where o.company_id=? $where_user $where_member $wheretimeframe and o.is_active=1 and o.status = 4 and o.for_pickup in (1,2) and o.from_service = 0  $where_od_type $where_search $where_branch order by o.is_scheduled desc, o.truck_id limit 100";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}

		public function getOrdersService($cid, $user_id = 0, $member_id = 0, $from = 0, $to = 0, $pickup_filter_type = '', $search = '', $branch_id = 0) {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$where_user = '';
				$where_member = '';
				$wheretimeframe = '';
				$where_od_type = '';
				$where_search = '';
				if($user_id) {
					$parameters[] = $user_id;
					$where_user = " and o.user_id=? ";
				}
				if($member_id) {
					$parameters[] = $member_id;
					$where_member = " and o.member_id=? ";
				}

				if($from && $to) {
					$dateStart = strtotime($from);
					$dateEnd = strtotime($to . '1 day -1 sec');

					$wheretimeframe = " and o.is_scheduled >=$dateStart and o.is_scheduled <= $dateEnd ";
				}
				if($pickup_filter_type == 1 && !$member_id) { // branch to branch
					$where_od_type = " and o.member_id = 0 ";
				} else if($pickup_filter_type == 2 && !$member_id) { // branch to member
					$where_od_type = " and o.member_id != 0 ";
				}
				if($search) {
					if(is_numeric($search)) {
						$where_search = "and (o.id = $search or o.invoice = $search or o.dr = $search or o.pr = $search) ";
					} else {
						$parameters[] = "%$search%";
						$where_search = "and (m.lastname like ?) ";
					}
				}
				if($branch_id) {
					$branch_id = (int)$branch_id;
					$where_branch = " and (o.branch_id = $branch_id or (o.to_branch_id = $branch_id and o.member_id =0 ))";
				} else {
					$where_branch = "";
				}

				$q = "Select o.*,sc.name as shipping_name,t.name as truck_name, t.description as truck_description,b.name as branch_name,b2.name as branch_name_to, m.lastname as mln, m.firstname as mfn,m.personal_address, wh.total_price, u.lastname, u.firstname, u.middlename from wh_orders o left join (Select o.wh_orders_id, sum(((o.price_adjustment + p.price) * o.qty) + o.member_adjustment ) as total_price from wh_order_details o left join items i on i.id = o.item_id left join prices p on p.id=o.price_id  where o.is_active=1 group by o.wh_orders_id) wh on wh.wh_orders_id=o.id left join branches b on b.id=o.branch_id  left join branches b2 on b2.id=o.to_branch_id left join shipping_companies sc on sc.id = o.shipping_company_id left join members m on m.id = o.member_id  left join users u on u.id = o.user_id left join trucks t on t.id = o.truck_id where o.company_id=? $where_user $where_member $wheretimeframe and o.is_active=1 and o.status = 4 and o.for_pickup in (0,1,2) and o.from_service != 0 $where_od_type $where_search $where_branch order by o.id desc, o.truck_id desc limit 100";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}

		public function updateTransactionDetails($payment_id = 0, $inv = 0, $dr = 0, $dt = 0, $ir = 0, $sv = 0, $from_service = 0) {
			$parameters = array();
			if($payment_id && $dt) {
				$sv = ($sv) ? $sv : 0;
				$inv = ($inv) ? $inv : 0;
				$dr = ($dr) ? $dr : 0;
				$ir = ($ir) ? $ir : 0;

				$parameters[] = $inv;
				$parameters[] = $dr;
				$parameters[] = $ir;
				$parameters[] = $sv;
				$parameters[] = $from_service;
				$parameters[] = $payment_id;

			}
			$q = "update wh_orders set invoice=?, dr=?  , pr=?, sv=?, from_service=? where payment_id =? limit 1";
			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				// return the data if exists
				return true;
			}
		}

		public function getPendingOrderQty($item_id = 0, $branch_id = 0, $order_id = 0) {
			$parameters = array();
			if($item_id && $branch_id) {

				$parameters[] = $item_id;
				$parameters[] = $branch_id;
				$parameters[] = $order_id;

				$q = "Select IFNULL(sum(od.qty),0) as od_qty from wh_orders o left join wh_order_details od on od.wh_orders_id=o.id where od.item_id=? and o.branch_id=? and o.id < ? and o.is_active=1 and o.status in(1,2,3) and o.stock_out=0 ";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->first();
				}
			}
		}


		public function pendingBundles($item_id = 0, $branch_id = 0) {
			//SELECT sum(wd.qty * b.child_qty) FROM `wh_orders` wh left join wh_order_details wd on wd.wh_orders_id = wh.id left join items i on i.id = wd.item_id left join bundles b on b.item_id_parent = i.id where wh.stock_out = 0 and i.is_bundle = 1 and b.item_id_child = 4502
			$parameters = array();
			if($item_id && $branch_id) {
				$parameters[] = $item_id;
				$parameters[] = $branch_id;
				$q = "SELECT
						IFNULL(sum(wd.qty * b.child_qty),0) as pending_qty FROM `wh_orders` wh
						left join wh_order_details wd on wd.wh_orders_id = wh.id
						left join items i on i.id = wd.item_id
						left join bundles b on b.item_id_parent = i.id
						where wh.status in (1,2,3) and wh.stock_out = 0
						and i.is_bundle = 1 and b.item_id_child = ?
						and wh.branch_id = ?";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->first();
				}
			}
		}

		public function pendingSpare($item_id = 0, $branch_id = 0) {
			//SELECT sum(wd.qty * b.child_qty) FROM `wh_orders` wh left join wh_order_details wd on wd.wh_orders_id = wh.id left join items i on i.id = wd.item_id left join bundles b on b.item_id_parent = i.id where wh.stock_out = 0 and i.is_bundle = 1 and b.item_id_child = 4502
			$parameters = array();
			if($item_id && $branch_id) {
				$parameters[] = $item_id;
				$parameters[] = $branch_id;
			/*	$q = "SELECT IFNULL(sum(wd.qty * b.qty),0) as pending_qty
				FROM `wh_orders` wh
				left join wh_order_details wd on wd.wh_orders_id = wh.id
				left join items i on i.id = wd.item_id
				left join composite_items b on b.item_id_set = i.id
				where wh.status in (1,2,3) and wh.stock_out = 0  and b.item_id_raw = ? and wh.branch_id = ?";
				*/
				$q ="SELECT IFNULL(sum(wd.qty * b.qty),0) as pending_qty
					FROM `composite_items` b
					left join (
									select item_id, qty, wh_orders_id
									 from wh_order_details
							  ) wd on wd.item_id = b.item_id_set
					 left join (
					 				select id,status,stock_out,branch_id
					 				from wh_orders
					 			) wh on wh.id = wd.wh_orders_id
					 left join (Select sum(qty) as inv_qty, item_id from inventories where branch_id = $branch_id) inv on inv.item_id = wd.item_id
					 where b.is_active = 1 and b.item_id_raw = ? and wh.status !=5 and wh.stock_out = 0 and wh.branch_id = ?";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->first();
				}
			}
		}

		public function pendingSpareBatch($ids = 0, $branch_id = 0) {

			$parameters = array();
			if($ids && $branch_id) {

				$parameters[] = $branch_id;

				$q ="SELECT IFNULL(sum(wd.qty * b.qty),0) as pending_qty
					FROM `composite_items` b
					left join (
									select item_id, qty, wh_orders_id
									 from wh_order_details
							  ) wd on wd.item_id = b.item_id_set
					 left join (
					 				select id,status,stock_out,branch_id
					 				from wh_orders
					 			) wh on wh.id = wd.wh_orders_id
					 left join (Select sum(qty) as inv_qty, item_id from inventories where branch_id = $branch_id) inv on inv.item_id = wd.item_id
					 where b.is_active = 1 and b.item_id_raw = ? and wh.status !=5 and wh.stock_out = 0 and wh.branch_id = ?";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->first();
				}
			}
		}

		public function spareWithAssemble($item_id_raw=0,$branch_id=0){
			$parameters = array();
			if( $item_id_raw & $branch_id) {
				$parameters[] = $item_id_raw;
				$parameters[] = $branch_id;
				$q = "select sum(inv.qty * ci.qty) as assemble_qty from inventories inv
					left join composite_items ci on ci.item_id_set = inv.item_id
					where ci.item_id_raw = ?  and inv.branch_id = ? and ci.is_active = 1 ";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->first();
				}
			}
		}
		public function getPendingOrderList() {
			$parameters = array();

			$parameters[] = 1;

			$q = "Select od.*, o.dr, o.invoice,o.pr, i.item_code,i.description from wh_orders o left join wh_order_details od on od.wh_orders_id=o.id left join items i on i.id = od.item_id where o.is_active=? and o.status in(1,2,3) and o.stock_out=0 ";

			$data = $this->_db->query($q, $parameters);
			// return results if there is any
			if($data->count()) {
				return $data->results();
			}

		}

		public function getPendingOrder($item_id = 0, $branch_id = 0) {
			$parameters = array();
			if($item_id && $branch_id) {
				$parameters[] = $item_id;
				$parameters[] = $branch_id;
				$q = "Select IFNULL(sum(od.qty),0) as od_qty from wh_orders o
						left join wh_order_details od on od.wh_orders_id=o.id
						where od.item_id=? and o.branch_id=? and o.is_active=1 and o.status in(1,2,3) and o.stock_out=0 ";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->first();
				}
			}
		}

		function getOrderCountLastTenDays($cid = 0, $branch_id = 0) {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$whereBranch = '';
				if($branch_id) {
					$parameters[] = $branch_id;
					$whereBranch = " and o.branch_id = ? ";
				}
				$q = "Select count(o.id) as total_count, DATE(FROM_UNIXTIME(o.created)) as dt from wh_orders o where o.company_id = ? $whereBranch group by DATE(FROM_UNIXTIME(o.created)) ORDER BY DATE(FROM_UNIXTIME(o.created)) desc limit 10";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}

		}

		function getPendingOrders($cid = 0, $branch_id = 0) {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$whereBranch = '';
				if($branch_id) {
					$parameters[] = $branch_id;
					$whereBranch = " and branch_id = ? ";
				}
				$q = "Select count(id) as total_count, status from wh_orders where is_active= 1 and company_id = ?  and is_scheduled=0 $whereBranch group by status";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}

		}

		function monthlyDelivered($cid = 0, $branch_id = 0) {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$whereBranch = '';
				if($branch_id) {
					$parameters[] = $branch_id;
					$whereBranch = " and branch_id = ? ";
				}
				$q = "Select count(id) as total_count, MONTH(FROM_UNIXTIME(is_scheduled)) as m, YEAR(FROM_UNIXTIME(is_scheduled)) as y from wh_orders where is_active= 1 and company_id = ?  and is_scheduled !=0 $whereBranch group by MONTH(FROM_UNIXTIME(is_scheduled)), YEAR(FROM_UNIXTIME(is_scheduled)) order by YEAR(FROM_UNIXTIME(is_scheduled)) desc, MONTH(FROM_UNIXTIME(is_scheduled)) desc limit 12";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}

		}

		public function topItemBranch($branch_id = 0, $dt1 = 0, $dt2 = 0, $sorty = "desc", $group_by = "",$limit=10) {
			$parameters = array();
			$parameters[] = $branch_id;

			$wheredate = '';

			if($dt1 && $dt2) {
				$wheredate = " and o.approved_date >= $dt1 and o.approved_date <= $dt2";
			}
			// $groupby and sortby is explicitly defined no sql injection prob

			$q = "Select sum(od.qty) as total_qty, od.item_id,ct.name as category_name, i.item_code,i.description
				 from wh_orders o left join wh_order_details od on od.wh_orders_id = o.id
				 left join items i on i.id = od.item_id left join categories ct on ct.id = i.category_id
				 where o.is_active= 1 and o.status in (1,2,3,4) and o.payment_id != 0 and o.branch_id=? $wheredate  group by $group_by order by total_qty $sorty limit $limit";

			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				// return the data if exists
				return $data->results();
			}
		}

		public function topAgentOrder($cid = 0, $branch_id = 0) {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$whereBranch = '';
				if($branch_id) {
					$parameters[] = $branch_id;
					$whereBranch = " and o.branch_id = ? ";
				}
				$q = "Select count(o.id) as total_count, u.lastname,u.firstname,u.middlename from wh_orders o left join users u on u.id=o.user_id where o.company_id = ? $whereBranch group by o.user_id ORDER BY total_count desc limit 5";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function topMemberOrder($cid = 0, $branch_id = 0) {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$whereBranch = '';
				if($branch_id) {
					$parameters[] = $branch_id;
					$whereBranch = " and o.branch_id = ? ";
				}
				$q = "Select count(o.id) as total_count, m.lastname,m.firstname,m.middlename from wh_orders o left join members m on m.id=o.member_id where o.company_id = ? $whereBranch group by o.member_id ORDER BY total_count desc limit 5";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}

		}

		public function countRecordBackload($cid, $search = '', $b = 0, $m = 0, $s = 0, $user_id = 0, $from = 0, $to = 0, $is_pickup = 0) {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;

				if($search) {
					$parameters[] = $search;
					$likewhere = " and (o.id = ?) ";
				} else {
					$likewhere = '';
				}
				if($b) {
					$parameters[] = $b;
					$branchwhere = " and o.branch_id=? ";
				} else {
					$branchwhere = "";
				}
				if($m) {
					$parameters[] = $m;
					$memberWhere = " and o.member_id=? ";
				} else {
					$memberWhere = "";
				}
				if($s) {
					$parameters[] = $s;
					$statusWhere = " and o.status=? ";
				} else {
					$statusWhere = "";
				}
				if($user_id) {
					$parameters[] = $user_id;
					$userWhere = " and o.user_id=? ";
				} else {
					$userWhere = "";
				}
				if($from && $to) {
					$from = strtotime($from);
					$to = strtotime($to . "1 day -1 sec");
					$dateWhere = " and o.created >= $from and o.created <= $to ";
				} else {
					$dateWhere = "";
				}
				if($is_pickup) {
					$wherePickup = " and o.for_pickup = 1";
				} else {
					$wherePickup = " and o.for_pickup = 0";
				}

				$q = "Select count(wd.id) as cnt from wh_orders o left join wh_order_details as wd on wd.wh_orders_id = o.id left join items i on i.id = wd.item_id left join users u on u.id=o.user_id left join members m on m.id=o.member_id left join branches b on b.id=o.branch_id where 1=1 and o.company_id=? and wd.backload_qty >0 $likewhere $branchwhere  $memberWhere $statusWhere $userWhere $dateWhere $wherePickup";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function get_record_backload($cid, $start, $limit, $search = '', $b = 0, $m = 0, $s = 0, $user_id = 0, $from = 0, $to = 0) {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				if($limit) {
					$l = " LIMIT $start,$limit";
				} else {
					$l = '';
				}
				if($search) {
					$parameters[] = $search;
					$likewhere = " and (o.id = ?) ";
				} else {
					$likewhere = '';
				}
				if($b) {
					$parameters[] = $b;
					$branchwhere = " and o.branch_id=? ";
				} else {
					$branchwhere = "";
				}
				if($m) {
					$parameters[] = $m;
					$memberWhere = " and o.member_id=? ";
				} else {
					$memberWhere = "";
				}
				if($s) {
					$parameters[] = $s;
					$statusWhere = " and o.status=? ";
				} else {
					$statusWhere = "";
				}
				if($user_id) {
					$parameters[] = $user_id;
					$userWhere = " and o.user_id=? ";
				} else {
					$userWhere = "";
				}
				if($from && $to) {
					$from = strtotime($from);
					$to = strtotime($to . "1 day -1 sec");
					$dateWhere = " and o.created >= $from and o.created <= $to ";
				} else {
					$dateWhere = "";
				}

				 $q = "Select i.item_code,i.description,wd.*,o.invoice,o.dr,o.pr, u.lastname,b.name as branch_name,b2.name as to_branch_name, u.firstname,u.middlename,m.lastname as mln, m.firstname  as mfn,m.middlename  as mmn from wh_orders o left join wh_order_details as wd on wd.wh_orders_id = o.id left join items i on i.id = wd.item_id left join users u on u.id=o.user_id left join members m on m.id=o.member_id left join branches b on b.id=o.branch_id left join branches b2 on b2.id=o.to_branch_id where 1=1 and o.company_id=? and wd.backload_qty >0 $likewhere $branchwhere  $memberWhere $statusWhere $userWhere $dateWhere order  by o.id desc $l ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}

		public function countRecord($cid, $search = '', $b = 0, $m = 0, $s = 0, $user_id = 0, $from = 0, $to = 0, $is_pickup = 0, $ebranch = 0, $fromService = 0) {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;

				if($search) {
					$parameters[] = $search;
					$parameters[] = $search;
					$parameters[] = $search;
					$parameters[] = $search;
					$likewhere = " and (o.id = ? or o.dr = ? or o.invoice = ? or o.pr = ?) ";
				} else {
					$likewhere = '';
				}
				if($b) {
					$parameters[] = $b;
					$branchwhere = " and o.branch_id=? ";
				} else {
					$branchwhere = "";
				}
				if($m) {
					$parameters[] = $m;
					$memberWhere = " and o.member_id=? ";
				} else {
					$memberWhere = "";
				}
				if($s) {
					$parameters[] = $s;
					$statusWhere = " and o.status=? ";
				} else {
					$statusWhere = "";
				}
				if($user_id) {
					$parameters[] = $user_id;
					$userWhere = " and o.user_id=? ";
				} else {
					$userWhere = "";
				}

				if($from && $to) {
					$from = strtotime($from);
					$to = strtotime($to . "1 day -1 sec");
					$dateWhere = " and o.created >= $from and o.created <= $to ";
				} else {
					$dateWhere = "";
				}

				if($is_pickup && !$fromService) {
					$wherePickup = " and o.for_pickup in (1,2) and o.from_service = 0";
				} else if(!$is_pickup && !$fromService) {
					$wherePickup = " and o.for_pickup in (0) and o.from_service = 0 ";
				} else if($fromService) {
					$wherePickup = " and o.from_service != 0 ";
				}
				if($ebranch) {
					$ebranch = (int) $ebranch;
					$whereBranch = " and (o.branch_id = $ebranch or (o.to_branch_id = $ebranch and o.member_id =0 )) ";
				} else {
					$whereBranch = "";
				}

				$q = "Select count(o.id) as cnt from wh_orders o left join users u on u.id=o.user_id left join members m on m.id=o.member_id left join branches b on b.id=o.branch_id where 1=1 and o.company_id=? $likewhere $branchwhere  $memberWhere $statusWhere $userWhere $dateWhere $wherePickup $whereBranch";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function get_record($cid, $start, $limit, $search = '', $b = 0, $m = 0, $s = 0, $user_id = 0, $from = 0, $to = 0, $is_pickup = 0) {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				if($limit) {
					$l = " LIMIT $start,$limit";
				} else {
					$l = '';
				}
				if($search) {
					$parameters[] = $search;
					$parameters[] = $search;
					$parameters[] = $search;
					$parameters[] = $search;
					$likewhere = " and (o.id = ? or o.dr = ? or o.invoice = ? or o.pr = ?) ";
				} else {
					$likewhere = '';
				}
				if($b) {
					$parameters[] = $b;
					$branchwhere = " and o.branch_id=? ";
				} else {
					$branchwhere = "";
				}
				if($m) {
					$parameters[] = $m;
					$memberWhere = " and o.member_id=? ";
				} else {
					$memberWhere = "";
				}
				if($s) {
					$parameters[] = $s;
					$statusWhere = " and o.status=? ";
				} else {
					$statusWhere = "";
				}
				if($user_id) {
					$parameters[] = $user_id;
					$userWhere = " and o.user_id=? ";
				} else {
					$userWhere = "";
				}
				if($from && $to) {
					$from = strtotime($from);
					$to = strtotime($to . "1 day -1 sec");
					$dateWhere = " and o.created >= $from and o.created <= $to ";
				} else {
					$dateWhere = "";
				}
				if($is_pickup) {
					$wherePickup = " and o.for_pickup = 1";
				} else {
					$wherePickup = " and o.for_pickup in (0,1,2)";
				}

				$q = "Select o.*,st.name as sales_type_name, u.lastname,b.name as branch_name,b2.name as to_branch_name,
 						u.firstname,u.middlename,m.lastname as mln, m.firstname  as mfn,m.middlename  as mmn
 						from wh_orders o
 						left join users u on u.id=o.user_id
 						left join members m on m.id=o.member_id
 						left join branches b on b.id=o.branch_id
 						left join branches b2 on b2.id=o.to_branch_id
 						left join salestypes st on st.id = o.gen_sales_type
 						where 1=1 and o.company_id=? $likewhere $branchwhere  $memberWhere $statusWhere $userWhere $dateWhere $wherePickup order  by o.id desc $l ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}

		public function hasAssembleItem($itemids = 0) {
			if($itemids) {
				$parameters = array();

				$tempt = '';
				foreach($itemids as $t) {
					$parameters[] = $t;
					$tempt .= '?,';
				}
				$tempt = rtrim($tempt, ',');
				$whereids = " and item_id_set in ($tempt)";

				$q = "Select count(id) as cnt from composite_items where 1=1 $whereids";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function getMemberOrderWh($member_id = 0, $status = 0, $item_id = 0) {
			$parameters = array();
			if($member_id && $status) {
				$parameters[] = $member_id;
				$parameters[] = $status;
				$parameters[] = $item_id;


				$q = "Select od.* from wh_orders o left join wh_order_details od on od.wh_orders_id=o.id where o.member_id = ? and o.status = ? and od.item_id=? and o.payment_id = 0";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function getItemToAssemble($order_id = 0) {
			$parameters = array();
			if($order_id) {
				$parameters[] = $order_id;


				$q = "select od.* from wh_order_details od left join wh_orders o on o.id=od.wh_orders_id left join (Select DISTINCT(item_id_set) from composite_items ) ci on ci.item_id_set = od.item_id where o.id= ? and o.has_assemble_item = 1 and ci.item_id_set = od.item_id order by o.id asc";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function getPendingToAssemble($cid, $branch_id) {
			$parameters = array();
			if($cid && $branch_id) {
				$parameters[] = $cid;
				$parameters[] = $branch_id;

				//	$q= "Select o.*,b.name as branch_name, m.lastname as mln, m.firstname as mfn, wh.total_price, u.lastname,u.firstname,u.middlename from wh_orders o left join (Select o.wh_orders_id, sum(((o.price_adjustment + p.price) * o.qty) + o.member_adjustment) as total_price from wh_order_details o left join items i on i.id = o.item_id left join prices p on p.id=o.price_id where o.is_active=1 group by o.wh_orders_id) wh on wh.wh_orders_id=o.id left join branches b on b.id=o.branch_id left join members m on m.id = o.member_id left join users u on u.id = o.user_id where o.company_id=? $where_user $where_member and o.is_active=1 and o.status in (1,2,3)";
				$q = "Select o.*,sc.name as shipping_name, mc.is_cod,t.name as truck_name, t.description as truck_description,b.name as branch_name, m.lastname as mln, m.firstname as mfn,m.personal_address,m.salestype, m.with_inv, wh.total_price, u.lastname, u.firstname, u.middlename from wh_orders o left join (Select o.wh_orders_id, sum((o.price_adjustment + p.price) * o.qty) + o.member_adjustment as total_price from wh_order_details o left join items i on i.id = o.item_id left join prices p on p.id=o.price_id  where o.is_active=1 group by o.wh_orders_id) wh on wh.wh_orders_id=o.id left join branches b on b.id=o.branch_id left join shipping_companies sc on sc.id = o.shipping_company_id left join members m on m.id = o.member_id  left join users u on u.id = o.user_id left join trucks t on t.id = o.truck_id left join member_credit mc on mc.payment_id = o.payment_id where o.company_id=? and o.branch_id = ? and o.is_active=1 and o.status in(2,3) and o.has_assemble_item = 1 and o.stock_out = 0 order by o.id asc";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}

		public function getBranchOrderScheduleToday($branch_id = 0, $dt = 0) {
			$parameters = array();
			if($branch_id) {
				$parameters[] = $branch_id;
				$dt = strtotime($dt);
				$parameters[] = $dt;

				$q = "Select od.* from wh_orders o left join wh_order_details od on od.wh_orders_id = o.id where o.to_branch_id = ? and date_format(from_unixtime(o.received_date),'%Y-%m-%d') = date_format(from_unixtime(?),'%Y-%m-%d')  and o.is_received = 1";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}

		public function getReceived() {
			$parameters = array();
			$q = "Select wh.*,u.lastname,u.firstname, b.name as branch_name
			from wh_orders wh
			left join branches b on b.id = wh.to_branch_id
			left join users u on u.id = wh.user_id
			where wh.is_received = 1 order by id desc limit 1000";
			$data = $this->_db->query($q, $parameters);
			// return results if there is any
			if($data->count()) {
				return $data->results();
			}

		}

		public function getByReceivedDate($branch_id = 0, $received_date = 0) {
			$parameters = array();
			$parameters[] = $branch_id;
			$parameters[] = $received_date;

			$q = "Select wh.*,u.lastname,u.firstname, b.name as branch_name from wh_orders wh left join branches b on b.id = wh.to_branch_id left join users u on u.id = wh.user_id where wh.is_received = 1 and wh.to_branch_id = ? and date_format(from_unixtime(wh.received_date),'%Y-%m-%d') = date_format(from_unixtime(?),'%Y-%m-%d') order by id desc";
			$data = $this->_db->query($q, $parameters);
			// return results if there is any
			if($data->count()) {
				return $data->results();
			}

		}

		public function pendingInBundle($item_id = 0, $branch_id = 0) {
			$parameters = array();
			$parameters[] = $item_id;
			$parameters[] = $branch_id;
			$q = "select cir.item_id_child, sum(wd.qty) as pending_qty from wh_orders wh
							left join wh_order_details wd on wd.wh_orders_id = wh.id
							left join (Select DISTINCT(item_id_parent) as item_id_parent
									   from bundles) ci on ci.item_id_parent = wd.item_id
							left join bundles cir on cir.item_id_parent = ci.item_id_parent
							where wh.status in (1,2,3) and wh.stock_out = 0 and cir.item_id_child is not null and cir.item_id_child = ? and wh.branch_id = ?";

			$data = $this->_db->query($q, $parameters);
			// return results if there is any
			if($data->count()) {
				return $data->first();
			}

		}

		public function pendingInAssemble($item_id = 0, $branch_id = 0) {
			$parameters = array();
			$parameters[] = $item_id;
			$parameters[] = $branch_id;
			$q = "
							select cir.item_id_raw, sum(wd.qty) as pending_qty from wh_orders wh
							left join wh_order_details wd on wd.wh_orders_id = wh.id
							left join (Select DISTINCT(item_id_set) as item_id_set
									   from composite_items) ci on ci.item_id_set = wd.item_id
							left join composite_items cir on cir.item_id_set = ci.item_id_set
							where
							 wh.status in (1,2,3) and
							 wh.stock_out = 0 and
							 cir.item_id_raw is not null
							 and cir.item_id_raw = ?
							 and wh.branch_id = ? ";

			$data = $this->_db->query($q, $parameters);
			// return results if there is any
			if($data->count()) {
				return $data->first();
			}

		}

		public function getItemOrder($month = 0, $year = 0, $item_id = 0, $branch_id = 0) {
			$parameters = array();


			$parameters[] = $month;
			$parameters[] = $year;
			$parameters[] = $item_id;
			$whereBranch = "";
			if($branch_id) {
				$whereBranch = " and  wh.to_branch_id = ?";
				$parameters[] = $branch_id;
			}


			$q = "Select sum(wd.qty) as totalquantity, DAY(FROM_UNIXTIME(wh.received_date)) as d from wh_orders wh left join wh_order_details wd on wd.wh_orders_id = wh.id where MONTH(FROM_UNIXTIME(wh.received_date)) = ? and YEAR(FROM_UNIXTIME(wh.received_date)) = ? and wd.item_id = ? $whereBranch group by  DAY(FROM_UNIXTIME(wh.received_date))";

			$data = $this->_db->query($q, $parameters);
			// return results if there is any
			if($data->count()) {
				return $data->results();
			}

		}

		public function getSummaryOrder($dt1 = 0, $dt2 = 0, $branch_id = 0, $branch_id_except = 0) {
			$parameters = array();

			$where_branch = "";
			if($branch_id) {
				$branch_id = (int) $branch_id;
				$where_branch = " and wh.to_branch_id = $branch_id ";
			} else if($branch_id_except) {
				$branch_id_except = (int) $branch_id_except;
				$where_branch_except = " and wh.to_branch_id != $branch_id_except ";
			}

			$q = "Select sum(wd.qty) as totalquantity, i.item_code, i.description from wh_orders wh left join wh_order_details wd on wd.wh_orders_id = wh.id left join items i on i.id = wd.item_id where wh.is_scheduled >= $dt1 and wh.is_scheduled <= $dt2 $where_branch $where_branch_except group by  wd.item_id ";

			$data = $this->_db->query($q, $parameters);
			// return results if there is any
			if($data->count()) {
				return $data->results();
			}

		}

		public function getTruckSummary($date_from = 0, $date_to = 0) {

			$parameters = array();
			if($date_from && $date_to) {
				$date_from = strtotime($date_from);
				$date_to = strtotime($date_to . "1 day -1 sec");

				$whereDate = " and ss.sold_date >= $date_from and ss.sold_date<=$date_to ";
			}

			$q = "		Select t.name as truck_name,
					sum(((o.price_adjustment + p.price) * o.qty) + o.member_adjustment)  as total_price,
					count(o.id) as po_count
					from wh_orders od
					left join (Select payment_id, sold_date from sales  group by payment_id ) as ss on ss.payment_id = od.payment_id
					left join wh_order_details o on od.id = o.wh_orders_id
					left join items i on i.id = o.item_id left join prices p on p.id=o.price_id
					left join trucks t on t.id = od.truck_id
					where
					1=1
					and od.status in (2,3,4)
					and od.truck_id != 0
					$whereDate
					group by od.truck_id";
			$data = $this->_db->query($q, $parameters);
			// return results if there is any
			if($data->count()) {
				return $data->results();
			}

		}

		public function getSoldFromBranch($branch_id = 0,$from= 0 , $to = 0,$item_id=0,$sales_date_type=1){
			$parameters = array();
			if($from && $to) {

				$branch_id = (int) $branch_id;

				if($sales_date_type == 1){
					$whereDate = " and s.sold_date >= $from and s.sold_date <= $to";
				} else {
					 $whereDate = " and wh.is_scheduled >= $from and wh.is_scheduled <= $to";
				}


				$whereBranch = "and wh.branch_id = $branch_id ";
				$whereItem = "";

				if($item_id){
					$item_id = (int) $item_id;
					$whereItem = "and i.id = $item_id ";
				}
				//where sold_date >= $from and sold_date <= $to
			  $q = "Select wd.*, i.item_code, i.description,i.is_bundle from wh_order_details wd
				left join wh_orders wh on wh.id = wd.wh_orders_id
				left join items i on i.id = wd.item_id
				left join (Select sold_date , invoice, dr , payment_id
							from sales
						group by payment_id) s on  s.payment_id = wh.payment_id
				where 1=1 and wh.payment_id != 0 and wh.stock_out = 1 and wh.status in (1,2,3,4) $whereBranch $whereDate $whereItem";

				$data = $this->_db->query($q, $parameters);

				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}

		}

		public function getTransferToBranch($branch_id = 0,$from= 0 , $to = 0,$item_id=0){
			$parameters = array();
			if($from && $to) {
				$branch_id = (int) $branch_id;
				$whereDate = " and wh.is_scheduled >= $from and wh.is_scheduled <= $to";
				$whereBranch = "and wh.branch_id = $branch_id ";
				$whereItem = "";
				if($item_id){
					$item_id = (int) $item_id;
					$whereItem = "and i.id = $item_id ";
				}
				$q = "Select wd.*, i.item_code, i.description,i.is_bundle,wh.invoice, wh.dr,wh.pr, b.name as branch_name
					from wh_order_details wd
					left join wh_orders wh on wh.id = wd.wh_orders_id
					left join branches b on b.id = wh.to_branch_id
					left join items i on i.id = wd.item_id
					where 1=1 and wh.payment_id = 0 and wh.stock_out = 1 and wh.status in (1,2,3,4) $whereBranch $whereDate $whereItem";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}

		public function getTransferIn($branch_id = 0,$from= 0 , $to = 0,$item_id=0){
			$parameters = array();
			if($from && $to) {
				$branch_id = (int) $branch_id;
				$whereDate = " and wh.is_scheduled >= $from and wh.is_scheduled <= $to";
				$whereBranch = "and wh.to_branch_id = $branch_id ";
				$whereItem = "";
				if($item_id){
					$item_id = (int) $item_id;
					$whereItem = "and i.id = $item_id ";

				}
				$q = "Select wd.*, i.item_code, i.description,i.is_bundle, b.name as branch_name
					from wh_order_details wd
					left join wh_orders wh on wh.id = wd.wh_orders_id
					left join branches b on b.id = wh.branch_id
					left join items i on i.id = wd.item_id
					where 1=1 and wh.payment_id = 0 and wh.stock_out = 1 and wh.status in (1,2,3,4) $whereBranch $whereDate $whereItem";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}


		public function getSummaryOfOutItems($dt1=0, $dt2=0,$branch_id=0,$type=0){

			$parameters = array();
			$whereType = "";

			if($type){
				if($type == 1){
					$whereType = " and wh.member_id = 0 ";
				} else if($type == 2){
					$whereType = " and wh.member_id != 0 ";
				}
			}

			$q = "
					SELECT
					wh.id ,
					wh.status,
					 sum(wd.original_qty) as original_qty ,
					 wd.item_id,
					  i.item_code ,
					  wh.created,DAY(from_unixtime(wh.created)) as d
					from wh_orders wh
					left join wh_order_details wd on wh.id = wd.wh_orders_id
					left join items i on i.id = wd.item_id
					where wh.status in (1,2,3,4)
					and wh.created >= $dt1
					and wh.created <= $dt2
					and wh.branch_id = $branch_id
					$whereType
					group by wd.item_id, DAY(from_unixtime(wh.created))
					";

			$data = $this->_db->query($q, $parameters);
			// return results if there is any
			if($data->count()) {
				return $data->results();
			}
		}

		public function getSummaryOfOutItems2($dt1=0, $dt2=0,$branch_id=0,$type=0){

			$parameters = array();
			$whereType = "";

			if($type){
				if($type == 1){
					$whereType = " and wh.member_id = 0 ";
				} else if($type == 2){
					$whereType = " and wh.member_id != 0 ";
				}
			}

			$q = "
					SELECT
					wh.id ,
					wh.status,
					  sum(wd.qty) as qty ,
					 wd.item_id,
					  i.item_code ,
					  wh.created,DAY(from_unixtime(wh.created)) as d
					from wh_orders wh
					left join wh_order_details wd on wh.id = wd.wh_orders_id
					left join items i on i.id = wd.item_id
					where wh.status in (1,2,3,4)
					and wh.stock_out = 1
					and wh.created >= $dt1
					and wh.created <= $dt2
					and wh.branch_id = $branch_id
					$whereType
					group by wd.item_id, DAY(from_unixtime(wh.created))
					";

			$data = $this->_db->query($q, $parameters);
			// return results if there is any
			if($data->count()) {
				return $data->results();
			}
		}


		public function getServiceNotif($cid, $user_id = 0, $member_id = 0, $branch_ids = 0, $my_id, $status, $dt1 = 0, $dt2 = 0,$start, $limit,$branch_id=0,$salestype=0,$for_pickup=0,$assemble=0,$search='',$service_notif=0) {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$where_user = '';
				$where_member = '';
				$where_branch = '';
				$where_dt = '';
				$where_status_join = '';
				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}
				if($user_id) {
					$parameters[] = $user_id;
					$where_user = " and o.user_id=? ";
				}
				if($member_id) {
					$parameters[] = $member_id;
					$where_member = " and o.member_id=? ";
				}
				if($branch_ids) {
					$my_id = (int)$my_id;
					$exbranch = explode(",",$branch_ids);
					$list_branch = "";
					foreach($exbranch as $bid){
						$bid = (int) $bid;
						$list_branch .= $bid . ",";
					}
					$list_branch = rtrim($list_branch,",");
					$where_branch = " and (o.branch_id in ($list_branch) or o.user_id=$my_id) ";
				}
				if($status) {
					$status = (int)$status;
					$where_status = "and o.status = $status";
					$where_status_join = "and od.status = $status";
				} else {
					$where_status = "and o.status in (1,2,3,4)";
					$where_status_join = "and od.status in (1,2,3,4)";
				}
				if($dt1 && $dt2) {
					$where_dt = " and o.approved_date >= $dt1 and o.approved_date <= $dt2";
				}
				$whereBranch = "";
				$whereType="";
				$wherePickup="";
				$whereAssemble="";
				if($branch_id){
					$branch_id = (int)$branch_id;
					$whereBranch = "and o.branch_id = $branch_id ";
				}
				if($salestype){
					$salestype = (int)$salestype;
					$whereType = "and st.id = $salestype ";
				}
				if($for_pickup){
					if($for_pickup == 1){
						$p = 1;
					} else if($for_pickup == 2){
						$p = 0;
					}  else if ($for_pickup == 3){
						$p = 2;
					}
					$wherePickup = " and o.for_pickup = $p ";
				}
				if($assemble){
					if($assemble == 1){
						$whereAssemble = " and o.has_assemble_item = 1";
					} else if ($assemble == 2){
						$whereAssemble = " and o.has_assemble_item = 0";
					}
				}
				$whereSearch = '';

				if($search){

					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$whereSearch = "and (o.id like ? or o.invoice like ? or o.dr like ? or o.pr like ?) ";
				}

				if($service_notif){
					$service_notif = (int) $service_notif;
					$whereNotifService = " and o.for_notif_service = $service_notif ";
				}

				 $q = "Select o.*,b.name as branch_name,
						m.lastname as mln, m.firstname as mfn,
						m.personal_address,m.salestype, m.with_inv, wh.total_price, u.lastname, u.firstname, u.middlename,
						st.name as sales_type_name
						from wh_orders o
						inner join (Select o.wh_orders_id, sum(((o.price_adjustment + p.price) * o.qty) + o.member_adjustment)  as total_price
						 				from wh_order_details o
						 				left join wh_orders od on od.id = o.wh_orders_id
						 				left join items i on i.id = o.item_id
						 				left join prices p on p.id=o.price_id
						 				where o.is_active=1 $where_status_join
						 				group by o.wh_orders_id)
						 				wh on wh.wh_orders_id=o.id
						left join branches b on b.id=o.branch_id
						left join members m on m.id = o.member_id
						left join salestypes st on st.id = m.salestype
						left join users u on u.id = o.user_id
						where o.company_id=? $where_user $where_member $where_branch and o.is_active=1 $where_status $where_dt $whereBranch $whereType $wherePickup $whereAssemble $whereSearch $whereNotifService order by o.id desc $l";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}

		public function isOrderExistByPO($po_num='',$getid=false){
			$parameters = array();
			if($po_num){
			//	$parameters[] = $po_num;
				$q= "Select wh.id, wh.client_po, wh.payment_id, m.amount, m.amount_paid, m.id as member_credit_id
						from wh_orders wh
						left join member_credit m on m.payment_id = wh.payment_id
						where  wh.client_po = '$po_num'";
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return ($getid) ? $e->first(): true;
				}
				return false;
			}
		}

		public function copyOrder($id=0){

			if($id && is_numeric($id)){
				$cols = "`sv_range`, `user_id`, `branch_id` , `member_id` , `to_branch_id`, `status` , `created` , `is_active`, `company_id` , `stock_out` , `remarks` , `payment_id` , `invoice` , `dr`, `is_scheduled` , `pr` , `is_priority` , `truck_id`, `has_assemble_item` , `helpers` , `driver`, `shipping_company_id`, `client_po`, `for_pickup` , `approved_date` , `is_reserve` , `reserved_date`, `from_service` , `file_name` , `station_id` , `for_approval_walkin`, `walkin_info` , `gen_sales_type`, `pref_payment` , `cancel_remarks` , `dragonpay_status` , `dragonpay_refno` , `dr_range` , `invoice_range`, `pr_range`, `sv`, `is_received`, `received_date` , `warranty_card_number` , `ts`, `sr` , `price_group_id`, `for_notif_service`";
				$parameters = array();
				$parameters[] = $id;

				$q = "INSERT INTO wh_orders ($cols) Select  $cols from wh_orders where id = ?";


				$data = $this->_db->query($q, $parameters,true);
				// return results if there is any
				if($data->count()) {
					return $data->lastInsertedId();
				}
			}
		}

		public function forServiceNotif(){

			$parameters = array();

			$q = "
					SELECT wh.member_id, wh.branch_id, s.*, m.lastname as member_name, b.name as branch_name
					FROM `wh_service_date` s
					left join wh_orders wh on wh.id = s.wh_order_id
					left join members m on m.id = wh.member_id
					left join branches b on b.id = wh.branch_id
					where s.start_date != 0
				";

			$data = $this->_db->query($q, $parameters,true);

			if($data->count()) {
				return $data->results();
			}

		}

		public function rebateDetails($sales_type_id = 0, $date_from  = 0, $date_to = 0){

			$parameters = array();
			$this->where("1=1");
			if($sales_type_id){

				$parameters[] = $sales_type_id;
				$this->where("and m.salestype = ? ");
			}

			if($date_from && $date_to){

				$date_from = strtotime($date_from);
				$date_to = strtotime($date_to . " 1 day -1 min");
				$parameters[] = $date_from;
				$parameters[] = $date_to;
				$this->where(" and wh.created >= $date_from  and wh.created <= $date_to ");
			}


			return $this->select("st.name as sales_type_name,wh.rebate,wh.created, m.lastname as member_name")
				->from("wh_orders wh")
				->join("left join members m on m.id = wh.member_id")
				->join("left join salestypes st on st.id = m.salestype")
				->get($parameters)
				->all();



		}

		public function rebateSummary( $year  = 0){

			$parameters = array();
			$this->where("1=1");


			$parameters[] = $year;
			$this->where("and YEAR(FROM_UNIXTIME(wh.created)) = ? ");



			return  $this->select("st.name as sales_type_name,sum(wh.rebate) as total_rebate,MONTH(FROM_UNIXTIME(wh.created)) AS m")
				->from("wh_orders wh")
				->join("left join members m on m.id = wh.member_id")
				->join("left join salestypes st on st.id = m.salestype")
				->groupBy("st.name, MONTH(FROM_UNIXTIME(wh.created))")
				->get($parameters)
			    ->all();



		}

	}
