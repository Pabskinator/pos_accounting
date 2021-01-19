<?php
	class Member extends Crud{
		protected $_table='members';
		public function __construct($m = NULL){
			parent::__construct($m);
		}
		public function distinctRegion(){
			$parameters = array();


			$q = "Select distinct(region) from members where region != '' order by region desc";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}
			return 0;
		}
		public function getByLastname($mem){

			$parameters = array();
			$parameters[] = "%$mem%";

			$q = "SELECT id from members where TRIM(LOWER(lastname)) like ?  limit 1";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->first();
			}
			return 0;
		}
		public function getMembers($cid=0,$member_id = 0){

			$parameters = array();
			$parameters[]=$cid;
			$whereMember = '';
			if($member_id){
				$parameters[]=$member_id;
				$whereMember = "and m.id = ? ";
			}
			$now = time();
			 $q= 'SELECT m.* , c.amt,f.freebiesamount, u.id as user_id FROM members m LEFT JOIN (SELECT member_id, sum( amount ) AS amt FROM consumable_amount GROUP BY member_id) c ON c.member_id = m.id LEFT JOIN (SELECT member_id, sum( amount ) AS freebiesamount FROM consumable_freebies GROUP BY member_id) f ON f.member_id = m.id left join users u on u.member_id = m.id WHERE m.company_id =? ' . $whereMember. ' AND m.is_active =1 order by m.lastname asc';

			//	echo "SELECT sum( amount )FROM cheque WHERE payment_id =27 AND payment_date <$now";


			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}
		}
		public function getNotYetValidCheque($payment_id){
			$parameters = array();
			$parameters[]=$payment_id;
			$now = time();
			$q = "SELECT sum( amount ) as cheque_amount FROM cheque WHERE payment_id = ? AND payment_date > $now AND status";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->first();
			}
		}
		public function salesType($cid){

			$parameters = array();
			$parameters[]=$cid;
			$q = "Select distinct(salestype) as st from members where company_id = ?";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}

		}
		public function salesByType($type,$start_date,$end_date,$member=0,$b=0,$user_id=0,$has_cancel=0,$show_all=0,$orderby_type=0){
			$parameters = array();
			if($start_date && $end_date){
				$wheretype = "";
				if($type){
					$parameters[]=$type;
					$wheretype = " and m.salestype = ? ";
				}
				$wheremem = "";
				if($member){
					$parameters[]=$member;
					$wheremem = " and s.member_id = ? ";
				}
				$wherebranch = "";
				if($b){
					$b = (int) $b;
					$wherebranch = " and b.id = $b";
				}
				$whereuser = "";
				if($user_id){
					$user_id = (int) $user_id;
					$whereuser = " and (wh.user_id = $user_id or (s.cashier_id = $user_id and s.from_od = 0))";
				}
				$wheredt = '';
				//$wheredt = " and s.sold_date >= $start_date and s.sold_date <= $end_date";
				//$q = "Select  sum(((s.qtys * p.price) + s.adjustment + s.member_adjustment)- (s.discount +s.store_discount)) as saletotal,s.*,m.lastname,mc.amount as pending_amount, mc.amount_paid,st.name as type_name from members m left join salestypes st on st.id=m.salestype  left join sales s on s.member_id = m.id left join member_credit mc on mc.payment_id = s.payment_id left join prices p on p.id=s.price_id  where m.salestype = ?  and mc.amount !=0 $wheredt group by s.payment_id order by s.member_id , s.payment_id ";
				$now = time();
				if($has_cancel){
					$whereStatus = "";
				} else {
					$whereStatus = "  and s.status = 0 ";
				}
				if($show_all){
					$whereCredit = "";
				} else {
					$whereCredit = "  and ((mc.amount != 0 ) or ch_table.valid_cheque != 0 )  and (mc.amount - mc.amount_paid  + IFNULL(ch_table2.invalid_cheque,0)) != 0  ";
				}
				if($orderby_type == 1){
					$whereOrderBy = "ORDER BY
								CASE
								   WHEN wh.is_scheduled is not null and wh.is_scheduled != 0 THEN wh.is_scheduled
								   WHEN wh.is_scheduled is null or wh.is_scheduled = 0 THEN s.sold_date
								END asc";
				} else {
					$whereOrderBy = " order by s.member_id , s.payment_id ";
				}
				/* $q = "Select
						sum(((s.qtys * p.price) + s.adjustment + s.member_adjustment)- (s.discount +s.store_discount)) as saletotal,
						s.*,
						m.lastname, m.terms,
					    mc.amount as pending_amount,
						mc.amount_paid,
						st.name as type_name,
						ch_table.valid_cheque,
						ch_table2.invalid_cheque,
						wh.is_scheduled,
						wh.client_po
						from members m
						left join salestypes st on st.id=m.salestype
						left join sales s on s.member_id = m.id
						left join terminals t on t.id = s.terminal_id
						left join branches b on b.id = t.branch_id
						left join member_credit mc on mc.payment_id = s.payment_id
						left join prices p on p.id=s.price_id

						left join (Select sum(amount) as valid_cheque,payment_id,payment_date
									from cheque where payment_date <= $now and status = 1
									group by payment_id) ch_table
									on ch_table.payment_id = s.payment_id
						left join (Select sum(amount) as invalid_cheque,payment_id,payment_date
									from cheque where (payment_date >= $now and status = 1) or status in (2,3)
									group by payment_id) ch_table2 on ch_table2.payment_id = s.payment_id
						left join (select payment_id, is_scheduled,client_po,user_id from wh_orders) wh on wh.payment_id = s.payment_id
						where 1=1 $wheretype $wheremem $wheredt and ((mc.amount != 0 ) or ch_table.valid_cheque != 0) and s.status = 0 $wherebranch $whereuser
						group by s.payment_id order by s.member_id , s.payment_id"; */
				 $q = "Select freight.freight_charge, sum(((s.qtys * p.price) + s.adjustment + s.member_adjustment)- (s.discount +s.store_discount)) as saletotal,
		        m.lastname, m.terms, mc.amount as pending_amount, mc.amount_paid, st.name as type_name, ch_table.valid_cheque,
		         ch_table2.invalid_cheque, wh.is_scheduled, wh.client_po , s.status, s.payment_id, s.sold_date, s.invoice,s.dr,s.ir,s.member_id,s.cashier_id,s.from_od
		         from  (select qtys, adjustment, member_adjustment, discount, store_discount , status,payment_id,sold_date,invoice,dr,ir,member_id,cashier_id,from_od ,terminal_id,price_id from sales) s
		         left join (select id,lastname, terms, salestype from members) m on m.id = s.member_id
		        left join salestypes st on st.id=m.salestype
		         left join terminals t on t.id = s.terminal_id
		         left join branches b on b.id = t.branch_id
		         left join member_credit mc on mc.payment_id = s.payment_id
		         left join prices p on p.id=s.price_id
		         left join (Select sum(amount) as valid_cheque,payment_id,payment_date from cheque where payment_date <= $now and status = 1 group by payment_id) ch_table on ch_table.payment_id = s.payment_id
		         left join (Select sum(amount) as invalid_cheque,payment_id,payment_date from cheque where (payment_date >= $now and status = 1) or status in (2,3) group by payment_id) ch_table2 on ch_table2.payment_id = s.payment_id
		         left join (select payment_id, is_scheduled,client_po,user_id from wh_orders) wh on wh.payment_id = s.payment_id
		         left join (select sum(charge + freight_adjustment - paid_amount) as freight_charge, payment_id from freight_charges where status = 0 group by payment_id) freight on freight.payment_id = s.payment_id

		         where 1=1 $wheretype $wheremem $wheredt

		          $whereStatus
				  $whereCredit
				  $wherebranch
				  $whereuser

		          group by s.payment_id $whereOrderBy ";
				$data = $this->_db->query($q,$parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function memberCreditUnpaid($type=0,$b=0,$user_id=0,$is_service_ar=0,$dt1=0,$dt2=0,$agent_id=0,$date_type=0){
			// date type 0 = all , 1 = delivered and picked up only

			$parameters = array();
			$whereType = '';
			$wherebranch = "";
			$whereuser = "";
			$whereAgent = "";
			if($type){
				$parameters[]=$type;
				$whereType = " and m.salestype = ? ";
			}


			if($b){
				$b = (int) $b;
				$wherebranch = " and (CASE WHEN wh.id IS NULL THEN  1 ELSE  wh.branch_id = $b END) ";
			}


			if($user_id){
				$user_id = (int) $user_id;
				$whereuser = " and (wh.user_id = $user_id or (s.cashier_id = $user_id and s.from_od = 0))";
			}
			if($agent_id){
				$agent_id = (int) $agent_id;
				$whereAgent = " and CONCAT( ',', m.agent_id , ',' ) LIKE '%,$agent_id,%'";
			}
			$whereService = "";
			if($is_service_ar){
				$whereService = " and s.is_service = 1 ";
			}
			$whereDate = "";
			if($date_type == 1){
				if($dt1 && $dt2){
					$dt1 = strtotime($dt1);
					$dt2 = strtotime($dt2 . "1 day -1 sec");

					$whereDate = " and (CASE WHEN wh.id IS NULL THEN  s.sold_date >= $dt1 and s.sold_date <= $dt2 ELSE  wh.is_scheduled >= $dt1 and wh.is_scheduled <= $dt2 and wh.status = 4  END) ";

				} else {
					$whereDate = " and (CASE WHEN wh.id IS NULL THEN 1 ELSE wh.status = 4 END ) ";

				}
			} else {

				if($dt1 && $dt2){
					$dt1 = strtotime($dt1);
					$dt2 = strtotime($dt2 . "1 day -1 sec");
					$whereDate = " and s.sold_date >= $dt1 and s.sold_date <= $dt2";
				}
			}



			$now = time();

			 $q = "
						Select mc.*, st.name as sales_type_name,s.*,m.lastname as member_name, m.terms as member_terms, m.credit_limit,m.region,
	                    ch_table.valid_cheque, ch_table2.invalid_cheque, wh.is_scheduled
	                    from member_credit mc left join members m on m.id = mc.member_id
						left join (Select invoice,dr,ir,sold_date,member_id,payment_id,status,terms,cashier_id,from_od,is_service from sales group by payment_id) s on s.payment_id = mc.payment_id
						left join salestypes st on st.id = m.salestype
						left join (Select sum(amount) as valid_cheque,payment_id,payment_date from cheque where payment_date <= $now and status = 1 group by payment_id) ch_table on ch_table.payment_id = mc.payment_id
		                left join (Select sum(amount) as invalid_cheque,payment_id,payment_date from cheque where (payment_date >= $now and status = 1) or status in (2,3) group by payment_id) ch_table2 on ch_table2.payment_id = mc.payment_id
				        left join (Select id,user_id,payment_id,branch_id,is_scheduled,status from wh_orders ) wh on wh.payment_id = mc.payment_id
				  		where
					    (mc.amount - mc.amount_paid + IFNULL(ch_table2.invalid_cheque,0)) != 0
					    and s.status = 0 $whereDate $whereuser $whereType $whereService $whereAgent $wherebranch
					    order by member_name, s.sold_date
					";

			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}

		}

		public function getExactAmount($member_id,$amount){
			$parameters = array();
			$parameters[]=$member_id;
			$parameters[]=$amount;

			$q = "SELECT * FROM consumable_amount WHERE member_id = ? and amount = ? limit 1";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->first();
			}
		}

		public function getMyConsumableAmount($member_id){
			$parameters = array();
			$parameters[]=$member_id;

			$q = "SELECT * FROM consumable_amount WHERE member_id = ? ";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}
		}
		public function getMyTotalConsumableAmount($member_id){
			$parameters = array();
			$parameters[]=$member_id;
			$q = "SELECT sum(amount) as totalConsumable FROM consumable_amount WHERE member_id = ? ";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->first();
			}
		}
		public function hasUserMember($member_id){
			$parameters = array();
			$parameters[]=$member_id;
			$q = "SELECT count(*) as cnt from users where member_id = ? ";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->first();
			}
		}
		public function getMyTotalBounceCheck($member_id){
			$parameters = array();
			$parameters[]=$member_id;
			 $q = "SELECT sum(c.amount) as totalBounce FROM cheque c left join (Select distinct(payment_id),member_id from sales) s on s.payment_id=c.payment_id WHERE s.member_id = ?  and c.status=3 ";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->first();
			}
		}
		public function getNotYetCollected($member_id){
			$parameters = array();
			$parameters[]=$member_id;
			$now = time();
			$q = "SELECT sum(c.amount) as totalNotCollected FROM cheque c left join (Select distinct(payment_id),member_id from sales) s on s.payment_id=c.payment_id WHERE s.member_id = ?  and c.status=1 and c.payment_date > $now ";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->first();
			}
		}
		public function getMyConsumableFreebies($member_id){
			$parameters = array();
			$parameters[]=$member_id;

			$q = "SELECT * FROM consumable_freebies WHERE member_id = ? ";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}
		}
		public function getMyTotalConsumableFreebies($member_id){
			$parameters = array();
			$parameters[]=$member_id;

			$q = "SELECT sum(amount) as totalFreebies FROM consumable_freebies WHERE member_id = ? ";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->first();
			}
		}
		public function getServices($cid=0){
			$now = strtotime(date('m/d/Y') . '1 day');
			$parameters = array();
			$parameters[]=$cid;
			$q= "Select * from services  where company_id=? and end_date >= $now and consumable_qty > 0";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}
		}
		public function countRecord($cid,$search='',$salestype=0,$char=0,$agent_id=0,$region='',$date_from=0,$date_to=0){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$wheretype = "";
				$likewhere = "";
				$wherechar = "";
				$wheredate = "";
				if($search) {
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";

					$likewhere = " and ( m.lastname like ? or  m.firstname like ? or  m.middlename like ? or  m.member_num = ? or m.personal_address like ?) ";
				}
				if($salestype) {
					$parameters[] = $salestype;
					$wheretype = " and  m.salestype = ? ";
				}
				$leftJoin = "";
				if($char){
					$char = (int) $char;
					$leftJoin = "left join (select member_id from member_characteristics where mem_char_id = $char ) charlist on charlist.member_id = m.id";
					$wherechar = " and charlist.member_id is not null";
				}

				$where_agent="";
				if($agent_id){
					$agent_id = (int) $agent_id;
					$where_agent = "and  CONCAT( ',', m.agent_id, ',' ) LIKE '%,$agent_id,%'";
				}
				$where_region="";
				if($region){
					$parameters[] = $region;
					$where_region = " and m.region = ?";
				}
				if($date_from && $date_to){
					$date_from = strtotime($date_from);
					$date_to = strtotime($date_to . "1 day -1 sec");
					$wheredate = " and m.member_since >= $date_from and m.member_since <= $date_to ";
				}

				 $q = "Select count(m.id) as cnt from members m $leftJoin where m.company_id=? and m.is_active = 1 $likewhere $wheretype $wherechar $where_agent $where_region $wheredate";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function get_member_record($cid,$start,$limit,$search='',$salestype=0,$char=0,$order_by=0,$agent_id=0,$region='',$date_from=0,$date_to=0){
			$parameters = array();
			if($cid){
				$parameters[] = $cid;
				$likewhere = "";
				$wherechar = "";
				$l='';
				$wheretype = "";
				$wheredate="";
				if($limit){
					$l = " LIMIT $start,$limit";
				}
				if($search){

					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";

					$likewhere = " and ( m.lastname like ? or  m.firstname like ? or  m.middlename like ? or  m.member_num = ? or m.personal_address like ?) ";

				}
				if($salestype) {
					$parameters[] = $salestype;
					$wheretype = " and m.salestype = ? ";
				}
				$leftJoin = "";
				if($char){
					$char = (int) $char;
					$leftJoin = "left join (select member_id from member_characteristics where mem_char_id = $char ) charlist on charlist.member_id = m.id";
					$wherechar = " and charlist.member_id is not null";
				}
				$whereOrder = "";
				if($order_by){
					$whereOrder = " order by m.lastname asc";
				}
				$where_agent="";
				if($agent_id){
					$agent_id = (int) $agent_id;
					$where_agent = "and  CONCAT( ',', m.agent_id, ',' ) LIKE '%,$agent_id,%'";
				}
				$where_region="";
				if($region){
					$parameters[] = $region;
					$where_region = " and m.region = ? ";
				}
				if($date_from && $date_to){
					$date_from = strtotime($date_from);
					$date_to = strtotime($date_to . "1 day -1 sec");
					$wheredate = " and m.member_since >= $date_from and m.member_since <= $date_to ";
				}
			 $q= "Select m.*,st.name as st_name from members m  $leftJoin left join salestypes st on st.id = m.salestype where m.company_id=? and m.is_active=1 $likewhere $wheretype $wherechar $whereOrder $where_agent  $where_region $wheredate $l  ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function countMember($companyid=0){
			$parameters = array();
			if($companyid){
				$parameters[] =$companyid;
				$q= 'Select count(id) as cnt from members  where  is_active=1 and company_id=?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->first();
				}
			}
		}
		public function changeBlacklistStatus($isblack,$memid){
			$parameters = array();
			if($memid){
				$parameters[] =$isblack;
				$parameters[] =$memid;
				$q= 'update members set is_blacklisted=? where id=?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return true;
				}
			}
		}
		public function getLastSold($cid=0,$memid=0){
			$parameters = array();
			if($cid && $memid){
				$parameters[] = $memid;
				$parameters[] = $memid;
				$parameters[] = $cid;
				$q= "SELECT s.*,it.id AS item_id, it.barcode, it.item_code, it.description,it.item_type,it.product_terminals
					FROM sales s left join items it on it.id=s.item_id
					WHERE s.member_id =?
					AND s.payment_id = (
					SELECT payment_id
					FROM sales
					WHERE member_id =?
					ORDER BY payment_id DESC
					LIMIT 1 ) and s.company_id=?";
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
				return false;
			}
		}
		public function getLastSoldFree($cid=0,$memid=0){
			$parameters = array();
			if($cid && $memid){
				$parameters[] = $memid;
				$parameters[] = $memid;
				$parameters[] = $cid;
				$parameters[] = $cid;
				$q= "SELECT s.*,it.id AS item_id, it.barcode, it.item_code, it.description,it.item_type,it.product_terminals
					FROM sales s left join items it on it.id=s.item_id
					WHERE s.member_id =?
					AND s.payment_id = (SELECT s.payment_id
					FROM sales s
					LEFT JOIN items i ON i.id = s.item_id
					WHERE i.for_freebies =1 and s.member_id=? and s.company_id=?
					ORDER BY s.payment_id DESC limit 1 ) and s.company_id=?";

				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
				return false;
			}
		}
		public function totalUtang($memid=0){
			$parameters = array();
			if($memid) {
				$parameters[] = $memid;

				$q = "Select sum(amount - amount_paid) as camount from member_credit where member_id= ? and is_active=1";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function kTypeReport($cid=0){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;

				$q = "Select k_type, count(id) as cnt from members where company_id = ? and k_type != 0 group by k_type";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function memberJSON($cid = 0 , $search = '',$user_id = 0,$k_type=0){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$whereSearch = '';
				$whereKType = '';
				if($search){
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";

					$whereSearch = " and (m.lastname like ? or m.firstname like ? or m.middlename like ? )";
				}
				$where_user="";
				if($user_id){
					$user_id = (int) $user_id;
					$where_user = "and  CONCAT( ',', m.agent_id, ',' ) LIKE '%,$user_id,%'";
				}
				if($k_type){
					$parameters[] = $k_type;
					$whereKType = "and  m.k_type = ? ";
				}

				$q = "Select m.* ,IFNULL(st.name,'') as sales_type_name from members m left join salestypes st on st.id = m.salestype where m.is_active = 1 and m.company_id = ? $whereSearch $where_user $whereKType limit 20";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}
	}
?>