<?php
	class Sales extends Crud implements PagingInterface{
		protected $_table = 'sales';
		public function __construct($inventory=null){
			parent::__construct($inventory);
		}
		public function getSales($branch_id=0,$company_id=0,$terminal_id=0){
			$parameters = array();
			$parameters[] = $branch_id;
			$parameters[] = $company_id;
			$parameters[] = $terminal_id;

			$currentMonth = date('F Y');
			$monthStart = strtotime($currentMonth);
			$monthEnd = strtotime($currentMonth . "15 days");
			$q= "Select s.status,st.name as station_name, b.name as branch_name ,t.name as terminal_name, i.item_code,i.barcode,i.description, p.price, u.lastname,u.firstname,u.middlename, m.lastname as mln,m.firstname as mfn,m.middlename as mmn,s.id as sales_id,s.invoice,s.dr,s.ir,s.qtys,s.discount,s.store_discount,s.adjustment,s.member_adjustment,com.name as company_name,s.sold_date ,s.payment_id from sales s left join items i on i.id = s.item_id left join users u on u.id = s.cashier_id left join members m on m.id = s.member_id left join companies com on com.id=s.company_id left join branches b on b.id=? left join terminals t on t.id=s.terminal_id left join prices p on p.id=s.price_id left join stations st on st.id=s.station_id where  s.sold_date >=$monthStart and s.sold_date <=$monthEnd and s.company_id=? and t.id= ? order by s.sold_date desc, s.invoice desc";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}

		}

		public function getStoreSales($dt1=0,$dt2=0,$company_id=0,$branch=0,$type=0,$sales_type=0,$noCancel= false){
		//	echo $dt1 . " " .$dt2 . " " . $company_id . " " .$branch;
			$parameters = array();
			$parameters[] = $branch;
			$parameters[] = $dt1;
			$parameters[] = $dt2;
			$parameters[] = $company_id;
			$orderWhere = " order by s.sold_date desc ";
			if($type == 1){
				$wherep = "and s.invoice != 0 ";
				$orderWhere = " order by s.invoice * 1 desc ";
			}else if($type == 2){
				$wherep = "and s.dr != 0";
				$orderWhere = "  order by s.dr * 1 desc ";
			}else if($type == 3){
				$wherep = "and s.sr != 0 ";
				$orderWhere = "  order by s.sr * 1 desc ";
			} else  if($type == 4){
				$wherep = "";
			} else  if($type == 5){
				$wherep = "and s.ir != 0 ";
				$orderWhere = "  order by s.ir * 1 desc ";
			} else {
				$wherep = "and 1=2";
			}
			$wheresalestype ='';
			if ($sales_type){
				$tempsalestype = "";

				foreach($sales_type as $ca){
					$parameters[] = $ca;
					$tempsalestype .= "?,";
				}
				$tempsalestype = rtrim($tempsalestype,',');
				$wheresalestype = " and s.sales_type in ($tempsalestype)";
			}
			$cancel = "";
			if($noCancel){
				$cancel = " and s.status = 0";
			}

			$q= "Select wh.remarks as wh_remarks, uwh.lastname as whlastname, uwh.firstname as whfirstname , s.status,st.name as station_name, b.name as branch_name , u.lastname,u.firstname,u.middlename, m.lastname as mln,m.firstname as mfn,m.middlename as mmn,s.id as sales_id,s.ir,s.invoice,s.dr,sum(((s.qtys * p.price) + s.adjustment + s.member_adjustment)- (s.discount + s.store_discount)) as stotal,s.sold_date,s.payment_id,s.sr, od.user_id as reserved_by, s.member_id from sales s left join orders od on od.payment_id = s.payment_id left join items i on i.id = s.item_id left join users u on u.id = s.cashier_id left join members m on m.id = s.member_id  left join branches b on b.id=? left join terminals t on t.id=s.terminal_id left join prices p on p.id=s.price_id left join stations st on st.id=s.station_id left join (Select user_id as wh_request_by ,payment_id, remarks from wh_orders) wh on wh.payment_id = s.payment_id left join users uwh on uwh.id = wh.wh_request_by where  s.sold_date >=? and s.sold_date <=? and s.company_id=? $cancel $wherep $wheresalestype group by s.payment_id  $orderWhere";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}
		}

		public function getSalesCompany($company_id=0,$from= 0 ,$to=0){
			$parameters = array();
			$parameters[] = $company_id;
			$parameters[] = $from;
			$parameters[] = $to;
			 $q= "Select sum(((s.qtys * p.price) + s.adjustment + s.member_adjustment)-s.discount + s.store_discount) as saletotal from sales s left join prices p on p.id=s.price_id where s.company_id = ? and s.sold_date >= ? and s.sold_date <= ?";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->first();
			}
		}

		public function getSalesBranch($branch=0,$from= 0 ,$to=0){
			$parameters = array();

			$parameters[] = $from;
			$parameters[] = $to;
			$whereBranch = "";
			if($branch){
				$parameters[] = $branch;
				$whereBranch = "and t.branch_id = ? ";
			}
			$q= "Select sum(((s.qtys * p.price) + s.adjustment + s.member_adjustment)-(s.discount + s.store_discount)) as saletotal from sales s left join prices p on p.id=s.price_id left join terminals t on t.id=s.terminal_id left join branches b on b.id=t.branch_id where s.sold_date >= ? and s.sold_date <= ? $whereBranch";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->first();
			}
		}

		public function getSalesTotalBaseOnPayment($payment_id=0,$type=0){
			$parameters = array();
			$parameters[] = $payment_id;
			$payment_id = (int) $payment_id;
			if($type == 1){ // cash
				$type = "cash";
			} else if($type == 2){
				$type = "credit_card";
			} else if($type == 3){
				$type = "cheque";
			} else if($type == 4){
				$type = "bank_transfer";
			}
			$q = "select sum(amount) as st from $type where payment_id=$payment_id";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->first();
			}
		}

		public function getSalesMember($member=0){
			$parameters = array();
			$parameters[] = $member;

			$q= "Select sum(((s.qtys * p.price) +s.adjustment + s.member_adjustment)-(s.discount + s.store_discount)) as saletotal from sales s left join prices p on p.id=s.price_id  where s.member_id = ? and s.status=0 ";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->first();
			}
		}

		public function getAll($dt1=0,$dt2=0){

			$parameters = array();

			$q= "
					Select s.invoice,s.dr,s.ir, t.name as terminal_name, s.status
					from sales s
					left join terminals t on t.id = s.terminal_id
					where  s.sold_date >= $dt1 and s.sold_date <= $dt2
					group by s.payment_id

					";

			$data = $this->_db->query($q,$parameters);

			if($data->count()){
				// return the data if exists
				return $data->results();
			}

		}

		public function getSalesMember10($member=0){
			$parameters = array();
			$parameters[] = $member;

			$q= "Select sum(((s.qtys * p.price) +s.adjustment + s.member_adjustment)-(s.discount + s.store_discount)) as saletotal,s.sold_date from sales s left join prices p on p.id=s.price_id  where s.member_id = ? and s.status=0 group by s.payment_id desc limit 10 ";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}
		}

		public function topStationMember($member=0,$dt1=0,$dt2=0){
			$parameters = array();
			$parameters[] = $member;
			$wheredate = '';
			if($dt1 && $dt2){
				$dt1 =strtotime($dt1);
				$dt2 =strtotime($dt2);
				$wheredate =" and s.sold_date>=$dt1 and s.sold_date<=$dt2 ";
			}
			$q= "Select sum(((s.qtys * p.price) +s.adjustment + s.member_adjustment)-(s.discount + s.store_discount)) as saletotal, st.name  from sales s left join prices p on p.id=s.price_id left join stations st on st.id=s.station_id where s.member_id = ? and s.status=0 $wheredate group by s.station_id desc limit 5 ";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}
		}

		public function topClientSales($cid = 0,$dt1=0,$dt2=0,$limit = 10,$branch_id=0, $sales_type_id = 0,$date_type=0){
			$parameters = array();
			$parameters[] = $cid;
			$wheredate = '';
			$where_branch = '';
			$where_salestype = '';
			$limit = (int) $limit;
			if($date_type){
				if($dt1 && $dt2){

					$wheredate = " and (CASE WHEN wh.id IS NULL THEN  s.sold_date >= $dt1 and s.sold_date <= $dt2 ELSE  wh.is_scheduled >= $dt1 and wh.is_scheduled <= $dt2 and wh.status = 4  END) ";

				} else {
					$wheredate = " and (CASE WHEN wh.id IS NULL THEN 1 ELSE wh.status = 4 END ) ";

				}
			} else {
				if($dt1 && $dt2){
					$wheredate =" and s.sold_date>=$dt1 and s.sold_date<=$dt2 ";
				}
			}


			if($branch_id){
				$parameters[] = $branch_id;
				$where_branch= "and t.branch_id = ? ";
			}

			if($sales_type_id){
				$parameters[] = $sales_type_id;
				$where_salestype= "and s.sales_type = ? ";
			}


			  $q= "Select st.name  as sales_type_name,sum(((s.qtys * p.price) +s.adjustment + s.member_adjustment)-(s.discount + s.store_discount)) as saletotal, m.lastname as member_name
					from sales s
					 left join terminals t on t.id = s.terminal_id
					left join prices p on p.id=s.price_id
					left join salestypes st on st.id = s.sales_type
					left join members m on m.id=s.member_id
					left join (select
					id,user_id,payment_id,from_service,branch_id,
					 for_pickup,is_scheduled,status
					from wh_orders ) wh on wh.payment_id = s.payment_id

					where s.member_id != 0 and s.company_id = ? and s.status=0 $wheredate $where_branch $where_salestype
					group by s.member_id order by saletotal desc limit $limit ";

			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}

		}

		public function topItemMember($member=0,$dt1=0,$dt2=0){
			$parameters = array();
			$parameters[] = $member;
			$wheredate = '';
			if($dt1 && $dt2){
				$dt1 =strtotime($dt1);
				$dt2 =strtotime($dt2);
				$wheredate =" and s.sold_date>=$dt1 and s.sold_date<=$dt2 ";
			}
			 $q= "Select sum(((s.qtys * p.price) +s.adjustment + s.member_adjustment)-(s.discount + s.store_discount)) as saletotal, i.item_code,i.description  from sales s left join prices p on p.id=s.price_id left join items i on i.id=s.item_id where s.member_id = ? and s.status=0 $wheredate group by s.item_id desc limit 5 ";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}
		}

		public function statsMemberPerTransaction($member=0){
			$parameters = array();
			$parameters[] = $member;

			$q= "SELECT min(((s.qtys * p.price) + s.adjustment + s.member_adjustment) - (s.discount + s.store_discount)) AS mintotal, max(((s.qtys * p.price) + s.adjustment + s.member_adjustment) - (s.discount + s.store_discount)) AS maxtotal, avg( ((s.qtys * p.price) + s.adjustment + s.member_adjustment) - (s.discount + s.store_discount) ) AS avgtotal FROM sales s LEFT JOIN prices p ON p.id = s.price_id WHERE s.qtys !=0 AND p.price !=0 AND s.member_id =? AND s.status =0 ";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->first();
			}
		}

		public function countRecord($cid,$search='',$b=0,$t=0,$m=0,$type=0,$sort_by=0,$tran_type=0,$user_id = 0,$item_id='',$date_from=0,$date_to=0){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;

				if($search) {

					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";

					$likewhere = " and (s.invoice like ? or s.dr like ? or s.sr like ? or s.ir like ? )";

				} else {
					$likewhere = '';
				}
				if($b) {

					if($b == -1){
						$parameters[] = 0;
						$branchwhere = " and s.terminal_id=? ";
					} else {
						$inbs = '';
						foreach($b as $bs){

							$parameters[] = $bs;
							$inbs .="?,";
						}
						$inbs = rtrim($inbs,',');
						$branchwhere = "and b.id in($inbs)";
					}

				} else {
					$branchwhere = "";
				}
				if($t) {
					$parameters[] = $t;
					$terminalWhere = " and t.id=? ";
				} else {
					$terminalWhere = "";
				}
				if($m){
					$parameters[] = $m;
					$memberWhere = " and s.member_id=? ";
				} else {
					$memberWhere = "";
				}
				if($type==0){
					$typeWhere = " and s.status=0";
				} else if($type==1) {
					$typeWhere = " and s.status=1";
				}
				if(is_array($tran_type)){
					$curt = "";
					foreach($tran_type as $t){
						$t = (int)$t;
						$curt.= $t.",";
					}
					$curt = rtrim($curt,",");
					$trantypeWhere = " and s.sales_type in ($curt)";
				} else {
					if($tran_type!=0){
						$parameters[] = $tran_type;
						$trantypeWhere = " and s.sales_type=?";
					} else  {
						$trantypeWhere = "";
					}
				}

				if($user_id){
					$user_id = (int) $user_id;
					$whereuser = " and (wh.user_id = $user_id or (s.cashier_id = $user_id and s.from_od = 0))";
				} else {
					$whereuser = "";
				}

				if($item_id) {

					$parameters[] = "%$item_id%";
					$parameters[] = "%$item_id%";

					$whereItem = " and (i.item_code like ? or i.description like ?)";

				} else {
					$whereItem = "";
				}
				$whereDate = "";
				if($date_from && $date_to){
					$dt1= strtotime($date_from);
					$dt2= strtotime($date_to . "1 day -1 min");
					$whereDate = " and s.sold_date >= $dt1 and s.sold_date <= $dt2 ";
				}
				  $q= "Select count(s.id) as cnt
					from sales s left join terminals t  on t.id = s.terminal_id
					left join items i on i.id = s.item_id
					left join members m on m.id = s.member_id
					left join stations stat on stat.id=s.station_id
					left join prices pr on pr.id=s.price_id
					left join branches b on b.id=t.branch_id
					left join (select id,payment_id,for_pickup,user_id from wh_orders) wh on wh.payment_id = s.payment_id
					where s.company_id=? and s.is_active=1 $typeWhere $likewhere $branchwhere $terminalWhere $memberWhere $trantypeWhere  $whereuser  $whereItem $whereDate ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function get_sales_record($cid,$start,$limit,$search='',$b=0,$t=0,$m=0,$type=0,$sort_by=0,$tran_type=0,$user_id=0,$item_id='',$date_from=0,$date_to=0){
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

					$likewhere = " and (s.invoice like ? or s.dr like ? or s.sr like ? or s.ir like ? )";

				} else {
					$likewhere='';
				}
				if($b){
					if($b == -1){
						$parameters[] = 0;
						$branchwhere = " and s.terminal_id=? ";
					} else {
						$inbs = '';
						foreach($b as $bs){
							$parameters[] = $bs;
							$inbs .="?,";
						}
						$inbs = rtrim($inbs,',');
						$branchwhere = "and b.id in($inbs)";
					}
				} else {
					$branchwhere = "";
				}
				if($t){
					$parameters[] = $t;
					$terminalWhere  = " and s.terminal_id=? ";
				}else {
					$terminalWhere ="";
				}
				if($m){
					$parameters[] = $m;
					$memberWhere = " and s.member_id=? ";
				} else {
					$memberWhere = "";
				}
				if($type==0){
					$typeWhere = " and s.status=0";
				} else if($type==1) {
					$typeWhere = " and s.status=1";
				}
				if($sort_by){
					$sort_by = trim($sort_by);
					$arr_valid = ['order by m.lastname desc',
						'order by IF (IFNULL(s.invoice,0) = 0, 1, 0), s.invoice * 1 desc',
						'order by IF (IFNULL(s.dr,0) = 0, 1, 0), s.dr * 1 desc',
						'order by IF (IFNULL(s.sr,0) = 0, 1, 0), s.sr * 1 desc',
						'order by IF (IFNULL(s.ir,0) = 0, 1, 0), s.ir * 1 desc',
						'order by IF (IFNULL(s.sv,0) = 0, 1, 0), s.sv * 1 desc',
						'order by i.item_code desc','order by pr.price desc',
						'order by s.qtys desc',
						'order by s.discount desc',
						'order by ((s.qtys * price)-s.discount) desc',
						'order by s.sold_date desc'];
					if(!in_array($sort_by,$arr_valid)){
						$sort_by = "";
					}
				} else{
					$sort_by = "order by s.payment_id desc, s.invoice desc, s.dr desc";
				}
				if(is_array($tran_type)){
					$curt = "";
					foreach($tran_type as $t){
						$t = (int)$t;
						$curt.= $t.",";
					}
					$curt = rtrim($curt,",");
					$trantypeWhere = " and s.sales_type in ($curt)";
				} else {
					if($tran_type!=0){
						$parameters[] = $tran_type;
						$trantypeWhere = " and s.sales_type=?";
					} else  {
						$trantypeWhere = "";
					}
				}

				if($user_id){
					$user_id= (int) $user_id;
					$whereuser = " and (wh.user_id = $user_id or (s.cashier_id = $user_id and s.from_od = 0))";
				} else {
					$whereuser = "";
				}

				if($item_id) {

					$parameters[] = "%$item_id%";
					$parameters[] = "%$item_id%";


					$whereItem = " and (i.item_code like ? or i.description like ?)";

				} else {
					$whereItem = "";
				}
				$whereDate = "";
				if($date_from && $date_to){
					$dt1= strtotime($date_from);
					$dt2= strtotime($date_to . "1 day -1 min");
					$whereDate = " and s.sold_date >= $dt1 and s.sold_date <= $dt2 ";
				}
				 $q= "Select mem_adj.remarks as adjustment_remarks, mem_adj.adjustment as mem_adjustment,
 					p.addtl_remarks, p.cr_number,b.name as branch_name,wh.client_po,wh.remarks as wh_remarks,
 					wh.id as wh_id,wh.from_service, wh.for_pickup, s.*,i.item_code,i.description,pr.price,
 					 m.lastname as member_name
 						from sales s
 						left join payments p on p.id = s.payment_id
 						left join terminals t  on t.id = s.terminal_id
 						left join items i on i.id = s.item_id
 						left join members m on m.id = s.member_id
 						left join
 							(
 							  Select * from member_adjustments where status = 2
 							  group by  member_id , item_id
 							) mem_adj on mem_adj.member_id = s.member_id and mem_adj.item_id = s.item_id
 						left join stations stat on stat.id=s.station_id
 						left join prices pr on pr.id=s.price_id
 						left join branches b on b.id=t.branch_id
 						left join (Select id, for_pickup,payment_id,user_id,remarks,from_service,client_po from wh_orders) wh on wh.payment_id = s.payment_id
 						where s.company_id=? and s.is_active=1 $typeWhere $likewhere $branchwhere $terminalWhere $memberWhere $trantypeWhere $whereuser $whereItem $whereDate $sort_by  $l ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function get_warranty_record($cid,$start,$limit,$search='',$b=0){
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
					$parameters[] = "%$search%";


					$likewhere = " and (s.invoice like ? or s.dr like ? or s.sr like ? or i.item_code like ? or i.description like ? )";
				} else {
					$likewhere='';
				}
				if($b){
					$parameters[] = $b;
					$branchwhere = " and b.id=?";
				} else {
					$branchwhere = "";
				}

				 $q= "Select s.*,
					i.item_code,i.description,pr.price,
					DATE_ADD(FROM_UNIXTIME(s.sold_date),INTERVAL s.warranty MONTH) as dueWarranty,
					DATE_ADD(FROM_UNIXTIME(wh.is_scheduled),INTERVAL s.warranty MONTH) as dueWarrantyWH,
					wh.is_scheduled
					from sales s
					left join wh_orders wh on wh.payment_id = s.payment_id
					left join terminals t  on t.id = s.terminal_id
					left join items i on i.id = s.item_id
					left join prices pr on pr.id=s.price_id
					where s.company_id=? and s.is_active=1 and s.warranty !=0 $likewhere $branchwhere  $l ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function countRecordWarranty($cid,$search='',$b=0){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;

				if($search) {

					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";



					$likewhere = " and (s.invoice like ? or s.dr like ? or s.sr like ? or i.item_code like ? or i.description like ?  )";

				} else {
					$likewhere = '';
				}
				if($b) {
					$parameters[] = $b;
					$branchwhere = " and b.id=?";
				} else {
					$branchwhere = "";
				}

				$q = "Select count(s.id) as cnt from sales s left join terminals t  on t.id = s.terminal_id left join items i on i.id = s.item_id  left join branches b on b.id=t.branch_id where s.company_id=? and s.warranty != 0 and s.is_active=1  $likewhere $branchwhere  ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function countRecordBaseOnPaymentMethod($cid=0,$payment_method=0,$b=0,$mem_id=0,$dateStart=0,$dateEnd=0){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$q='';
				$bwhere ='';
				$mwhere ='';
				$datewhere='';
				if($b){

					$inbs = '';
					foreach($b as $bs){
						$parameters[] = $bs;
						$inbs .="?,";
					}
					$inbs = rtrim($inbs,',');
				$bwhere = "and t.branch_id in($inbs)";
				}
				if($mem_id){
					$inmems = '';
					foreach($mem_id as $ms){
						$parameters[] = $ms;
						$inmems .="?,";
					}
					$inmems = rtrim($inmems,',');
					$mwhere = " and s.member_id in ($inmems)";
				}
				if($dateStart && $dateEnd){
					$parameters[] = strtotime($dateStart);
					$parameters[] = strtotime($dateEnd . "1 day -1sec");
					$datewhere = ' and s.sold_date >=? and s.sold_date <= ?';
				}

				if($payment_method == 1){
					// cash
					  $q = "Select count(s.id) as cnt from cash c left join sales s on c.payment_id=s.payment_id left join terminals t on t.id = s.terminal_id left join branches b on b.id=t.branch_id where s.company_id=? and s.is_active=1 $bwhere $mwhere $datewhere ";

				}else if($payment_method == 2){
					// credit
					 $q = "Select count(s.id) as cnt from credit_card c left join sales s on c.payment_id=s.payment_id  left join terminals t on t.id = s.terminal_id left join branches b on b.id=t.branch_id  where s.company_id=? and s.is_active=1 $bwhere $mwhere $datewhere";

				}else if($payment_method == 3){
					//bt
					 $q = "Select count(s.id) as cnt from bank_transfer c left join sales s on c.payment_id=s.payment_id left join terminals t on t.id = s.terminal_id left join branches b on b.id=t.branch_id where s.company_id=? and s.is_active=1 $bwhere $mwhere $datewhere";

				}else if($payment_method == 4){
					// cheque
					 $q = "Select count(s.id) as cnt from cheque c left join sales s on c.payment_id=s.payment_id left join terminals t on t.id = s.terminal_id left join branches b on b.id=t.branch_id where s.company_id=? and s.is_active=1 $bwhere $mwhere $datewhere ";

				}else if($payment_method == 5){
					// con
					 $q = "Select count(s.id) as cnt from payment_consumable c left join sales s on c.payment_id=s.payment_id left join terminals t on t.id = s.terminal_id left join branches b on b.id=t.branch_id  where s.company_id=? and s.is_active=1 $bwhere $mwhere $datewhere";

				} else {
					 $q= "Select count(s.id) as cnt from sales s left join terminals t  on t.id = s.terminal_id left join branches b on b.id=t.branch_id where s.company_id=? and s.is_active=1  $bwhere $mwhere $datewhere ";
				}

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function countRecordBaseOnItem($cid=0,$item_type=0,$categ=0,$char=0){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$q='';
				$typewhere ='';
				$categwhere ='';
				$charwhere='';

				if($item_type){
					$itms ='';
					foreach($item_type as $it){
						$parameters[] = $it;
						$itms .= "?,";
					}
					$itms = rtrim($itms,",");
					$typewhere = " and i.item_type in ($itms)";
				}

				if($categ){
					$ctms ='';
					foreach($categ as $it){
						$parameters[] = $it;
						$ctms .= "?,";
					}
					$ctms = rtrim($ctms,",");

					$categwhere = " and i.category_id in($ctms)";

				}

				 $q= "Select count(s.id) as cnt from sales s left join items i on i.id = s.item_id where s.company_id=? and s.is_active=1 $typewhere $categwhere  order by s.payment_id desc  ";


				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function totalSaleBaseOnPaymentMethod($cid=0,$payment_method=0,$b=0,$mem_id=0,$dateStart=0,$dateEnd=0){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$q='';
				$bwhere ='';
				$mwhere ='';
				$datewhere='';
				if($b){

					$inbs = '';
					foreach($b as $bs){
						$parameters[] = $bs;
						$inbs .="?,";
					}
					$inbs = rtrim($inbs,',');
					$bwhere = "and t.branch_id in($inbs)";
				}
				if($mem_id){
					$inmems = '';
					foreach($mem_id as $ms){
						$parameters[] = $ms;
						$inmems .="?,";
					}
					$inmems = rtrim($inmems,',');
					$mwhere = " and s.member_id in ($inmems)";
				}
				if($dateStart && $dateEnd){
					$parameters[] = strtotime($dateStart);
					$parameters[] = strtotime($dateEnd . "1 day -1sec");
					$datewhere = ' and s.sold_date >=? and s.sold_date <= ?';
				}
				if($payment_method == 1){
					// cash
					$q = "Select sum((s.qtys * (select price from prices where id = s.price_id))-s.discount) as stotal from cash c left join sales s on c.payment_id=s.payment_id left join terminals t on t.id = s.terminal_id left join branches b on b.id=t.branch_id where s.company_id=? and s.is_active=1 $bwhere $mwhere $datewhere ";

				}else if($payment_method == 2){
					// credit
					$q = "Select sum((s.qtys * (select price from prices where id = s.price_id))-s.discount) as stotal  from credit_card c left join sales s on c.payment_id=s.payment_id  left join terminals t on t.id = s.terminal_id left join branches b on b.id=t.branch_id  where s.company_id=? and s.is_active=1 $bwhere $mwhere $datewhere";

				}else if($payment_method == 3){
					//bt
					$q = "Select sum((s.qtys * (select price from prices where id = s.price_id))-s.discount) as stotal  from bank_transfer c left join sales s on c.payment_id=s.payment_id left join terminals t on t.id = s.terminal_id left join branches b on b.id=t.branch_id where s.company_id=? and s.is_active=1 $bwhere $mwhere $datewhere";

				}else if($payment_method == 4){
					// cheque
					$q = "Select sum((s.qtys * (select price from prices where id = s.price_id))-s.discount) as stotal from cheque c left join sales s on c.payment_id=s.payment_id left join terminals t on t.id = s.terminal_id left join branches b on b.id=t.branch_id where s.company_id=? and s.is_active=1 $bwhere $mwhere $datewhere ";

				}else if($payment_method == 5){
					// con
					$q = "Select sum((s.qtys * (select price from prices where id = s.price_id))-s.discount) as stotal  from payment_consumable c left join sales s on c.payment_id=s.payment_id left join terminals t on t.id = s.terminal_id left join branches b on b.id=t.branch_id  where s.company_id=? and s.is_active=1 $bwhere $mwhere $datewhere";

				} else {
					$q= "Select sum((s.qtys * (select price from prices where id = s.price_id))-s.discount) as stotal from sales s left join terminals t  on t.id = s.terminal_id left join branches b on b.id=t.branch_id where s.company_id=? and s.is_active=1  $bwhere  $mwhere $datewhere  ";

				}

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function totalSaleBaseOnItem($cid=0,$item_type=0,$categ=0,$char=0){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$q='';
				$typewhere ='';
				$categwhere ='';
				$charwhere='';
				if($item_type){
					$itms ='';
					foreach($item_type as $it){
						$parameters[] = $it;
						$itms .= "?,";
					}
					$itms = rtrim($itms,",");
					$typewhere = " and i.item_type in ($itms)";
				}
				if($categ){

					$ctms ='';
					foreach($categ as $it){
						$parameters[] = $it;
						$ctms .= "?,";
					}
					$ctms = rtrim($ctms,",");
					$categwhere = " and i.category_id in($ctms)";

				}
				$q= "Select  sum((s.qtys * (select price from prices where id = s.price_id))-s.discount) as stotal  from sales s left join items i on i.id = s.item_id where s.company_id=? and s.is_active=1 $typewhere $categwhere  order by s.payment_id desc  ";


				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}

			}
		}
		public function countRecordTransaction($cid=0,$pt=0,$b=0,$mem_id=0,$dateStart=0,$dateEnd=0){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$q='';
				$bwhere ='';
				$mwhere='';
				$datewhere='';
				if($b){

					$inbs = '';
					foreach($b as $bs){
						$parameters[] = $bs;
						$inbs .="?,";
					}
					$inbs = rtrim($inbs,',');
					$bwhere = "and t.branch_id in($inbs)";
				}
				if($mem_id){
					$inmems = '';
					foreach($mem_id as $ms){
						$parameters[] = $ms;
						$inmems .="?,";
					}
					$inmems = rtrim($inmems,',');
					$mwhere = " and s.member_id in ($inmems)";
				}
				if($dateStart && $dateEnd){
					$parameters[] = strtotime($dateStart);
					$parameters[] = strtotime($dateEnd . "1 day -1sec");
					$datewhere = ' and s.sold_date >=? and s.sold_date <= ?';
				}
				if($pt == 1){
					// cash
					$q = "Select count(c.id) as cnt from cash c left join sales s on c.payment_id=s.payment_id left join terminals t on t.id = s.terminal_id left join branches b on b.id=t.branch_id where s.company_id=? and s.is_active=1 $bwhere $mwhere $datewhere";

				}else if($pt == 2){
					// credit
					$q = "Select count(c.id) as cnt from credit_card c left join sales s on c.payment_id=s.payment_id  left join terminals t on t.id = s.terminal_id left join branches b on b.id=t.branch_id where s.company_id=? and s.is_active=1 $bwhere $mwhere $datewhere";

				}else if($pt == 3){
					//bt
					$q = "Select count(c.id) as cnt from bank_transfer c left join sales s on c.payment_id=s.payment_id left join terminals t on t.id = s.terminal_id left join branches b on b.id=t.branch_id where s.company_id=? and s.is_active=1 $bwhere $mwhere $datewhere ";

				}else if($pt == 4){
					// cheque
					$q = "Select count(c.id) as cnt from cheque c left join sales s on c.payment_id=s.payment_id left join terminals t on t.id = s.terminal_id left join branches b on b.id=t.branch_id where s.company_id=? and s.is_active=1 $bwhere $mwhere $datewhere";

				}else if($pt == 5){
					// con
					$q = "Select count(c.id) as cnt from payment_consumable c left join sales s on c.payment_id=s.payment_id left join terminals t on t.id = s.terminal_id left join branches b on b.id=t.branch_id where s.company_id=? and s.is_active=1 $bwhere $mwhere $datewhere";

				}

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function totalAmountTransaction($cid=0,$pt=0,$b=0,$mem_id=0,$dateStart=0,$dateEnd=0){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$q='';
				$bwhere ='';
				$mwhere = '';
				$datewhere ='';
				if($b){

					$inbs = '';
					foreach($b as $bs){
						$parameters[] = $bs;
						$inbs .="?,";
					}
					$inbs = rtrim($inbs,',');
					$bwhere = "and t.branch_id in($inbs)";
				}
				if($mem_id){
					$inmems = '';
					foreach($mem_id as $ms){
						$parameters[] = $ms;
						$inmems .="?,";
					}
					$inmems = rtrim($inmems,',');
					$mwhere = " and s.member_id in ($inmems)";
				}
				if($dateStart && $dateEnd){
					$parameters[] = strtotime($dateStart);
					$parameters[] = strtotime($dateEnd . "1 day -1sec");
					$datewhere = ' and s.sold_date >=? and s.sold_date <= ?';
				}
				if($pt == 1){
					// cash
					$q = "Select sum(amount) as stotal from cash c left join sales s on c.payment_id=s.payment_id left join terminals t on t.id = s.terminal_id left join branches b on b.id=t.branch_id where s.company_id=? and s.is_active=1 $bwhere $mwhere $datewhere";

				}else if($pt == 2){
					// credit
					$q = "Select sum(amount) as stotal from credit_card c left join sales s on c.payment_id=s.payment_id  left join terminals t on t.id = s.terminal_id left join branches b on b.id=t.branch_id where s.company_id=? and s.is_active=1 $bwhere $mwhere $datewhere";

				}else if($pt == 3){
					//bt
					$q = "Select sum(amount) as stotal from bank_transfer c left join sales s on c.payment_id=s.payment_id left join terminals t on t.id = s.terminal_id left join branches b on b.id=t.branch_id where s.company_id=? and s.is_active=1 $bwhere $mwhere $datewhere";

				}else if($pt == 4){
					// cheque
					$q = "Select sum(amount) as stotal from cheque c left join sales s on c.payment_id=s.payment_id left join terminals t on t.id = s.terminal_id left join branches b on b.id=t.branch_id where s.company_id=? and s.is_active=1 $bwhere $mwhere $datewhere";

				}else if($pt == 5){
					// con
					$q = "Select sum(amount) as stotal from payment_consumable c left join sales s on c.payment_id=s.payment_id left join terminals t on t.id = s.terminal_id left join branches b on b.id=t.branch_id where s.company_id=? and s.is_active=1 $bwhere $mwhere $datewhere";

				}

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function countPaymentLength($payment_id=0,$start=0,$limit=0){
			if($payment_id){
				$parameters = array();
				$parameters[] = $payment_id;
				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}
				$q= "Select count(a.id) as pcount from (select id from sales where payment_id=? $l) as a ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->first();
				}
			}
		}
		public function salesTransactionBaseOnPaymentId($payment_id=0,$activeonly=0,$sort=0){
			if($payment_id){
				$parameters = array();
				$parameters[] = $payment_id;
				$statuswhere='';
				$sort_by='';
				if($activeonly){
					$statuswhere = " and s.status=0";
				}
				if($sort){
					$sort_by = " order by i.for_selling, cg.order_by,i.sort_by, cg.name";
				}
			    $q= " Select con.qty as con_qty, whb.id as wh_branch_destionation_id, whb.name as whbranch, wh.id as wh_id, wh.from_service,wh.remarks as wh_remarks,
 					  cg.name as category_name, stn.name as sales_type_name,un.name as unit_name, s.*,
 					  pm.remarks as premarks, i.is_bundle,i.item_code,i.barcode, i.description, p.price,sn.name as station_name,
 					  sn.id as station_id, sn.address as station_address,st.name as terminal_name,st.id as terminal_id,
 					  b.name as branch_name,m.lastname as mln, m.firstname as mfn, m.middlename as mmn,u.lastname as uln,
 					  u.firstname as ufn, u.middlename as umn ,
 					  m.terms,m.tin_no, m.personal_address, i.for_selling,pm.addtl_remarks
						from sales s
						left join items i on i.id=s.item_id
						left join consumables con on con.item_id = i.id
						left join units un on un.id = i.unit_id
						left join categories cg on cg.id = i.category_id
						left join prices p on p.id=s.price_id
						left join terminals st on st.id=s.terminal_id
						left join stations sn on sn.id=s.station_id
						left join members m on m.id=s.member_id
						left join users u on u.id=s.cashier_id
						left join payments pm on pm.id=s.payment_id
						left join branches b on b.id=st.branch_id
						left join salestypes stn on stn.id = s.sales_type
						left join wh_orders wh on wh.payment_id = s.payment_id
						left join branches whb on whb.id = wh.to_branch_id
						where s.payment_id = ? $statuswhere $sort_by";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}

		public function get_sales_record_baseOnPayment($cid=0,$start,$limit,$payment_method=0,$b=0,$mem_id=0,$dateStart=0,$dateEnd=0){
			$parameters = array();
			if($cid){
				$parameters[] = $cid;
				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}
				$bwhere ='';
				$mwhere='';
				$datewhere='';
				if($b){
					$inbs = '';
					foreach($b as $bs){
						$parameters[] = $bs;
						$inbs .="?,";
					}
					$inbs = rtrim($inbs,',');
					$bwhere = "and t.branch_id in($inbs)";
				}
				if($mem_id){
					$inmems = '';
					foreach($mem_id as $ms){
						$parameters[] = $ms;
						$inmems .="?,";
					}
					$inmems = rtrim($inmems,',');
					$mwhere = " and s.member_id in ($inmems)";
				}
				if($dateStart && $dateEnd){
					$parameters[] = strtotime($dateStart);
					$parameters[] = strtotime($dateEnd . "1 day -1sec");
					$datewhere = ' and s.sold_date >=? and s.sold_date <= ?';
				}
				if($payment_method == 1){
					// cash

					$q = "Select s.* from cash c left join sales s on c.payment_id=s.payment_id left join terminals t on t.id = s.terminal_id left join branches b on b.id=t.branch_id where s.company_id=? and s.is_active=1 $bwhere $mwhere $datewhere order by s.payment_id desc $l";

				}else if($payment_method == 2){
					// credit
					$q = "Select s.* from credit_card c left join sales s on c.payment_id=s.payment_id left join terminals t on t.id = s.terminal_id left join branches b on b.id=t.branch_id where s.company_id=? and s.is_active=1 $bwhere $mwhere $datewhere order by s.payment_id desc  $l";

				}else if($payment_method == 3){
					//bt
					$q = "Select s.* from bank_transfer c left join sales s on c.payment_id=s.payment_id left join terminals t on t.id = s.terminal_id left join branches b on b.id=t.branch_id where s.company_id=? and s.is_active=1 $bwhere $mwhere $datewhere order by s.payment_id desc  $l";

				}else if($payment_method == 4){
					// cheque
					$q = "Select s.* from cheque c left join sales s on c.payment_id=s.payment_id left join terminals t on t.id = s.terminal_id left join branches b on b.id=t.branch_id where s.company_id=? and s.is_active=1 $bwhere $mwhere $datewhere order by s.payment_id desc  $l";

				}else if($payment_method == 5){
					// con
					$q = "Select s.* from payment_consumable c left join sales s on c.payment_id=s.payment_id left join terminals t on t.id = s.terminal_id left join branches b on b.id=t.branch_id where s.company_id=? and s.is_active=1 $bwhere $mwhere $datewhere order by s.payment_id desc  $l";

				} else {
					 $q= "Select s.* from sales s left join terminals t  on t.id = s.terminal_id left join branches b on b.id=t.branch_id where s.company_id=? and s.is_active=1 $bwhere $mwhere $datewhere order by s.payment_id desc $l ";
				}

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function get_sales_record_baseOnItem($cid=0,$start,$limit,$item_type=0,$categ=0,$char=0){
			$parameters = array();
			if($cid){
				$parameters[] = $cid;
				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}
				$typewhere ='';
				$categwhere='';
				$charwhere='';

				if($item_type){
					$itms ='';
					foreach($item_type as $it){
						$parameters[] = $it;
						$itms .= "?,";
					}
					$itms = rtrim($itms,",");
					$typewhere = " and i.item_type in ($itms)";
				}
				if($categ){
					$ctms ='';
					foreach($categ as $it){
						$parameters[] = $it;
						$ctms .= "?,";
					}
					$ctms = rtrim($ctms,",");
					$categwhere = " and i.category_id in($ctms)";
				}

				$q= "Select s.* from sales s left join items i on i.id = s.item_id where s.company_id=? and s.is_active=1 $typewhere $categwhere  order by s.payment_id desc $l ";

				$data = $this->_db->query($q, $parameters);

				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function get_sales_record_transaction($cid=0,$start,$limit,$pt=0,$b=0,$mem_id=0,$dateStart=0,$dateEnd=0){
			$parameters = array();
			if($cid){
				$parameters[] = $cid;
				$bwhere ='';
				$mwhere='';
				$datewhere='';
				if($b){

					$inbs = '';
					foreach($b as $bs){
						$parameters[] = $bs;
						$inbs .="?,";
					}
					$inbs = rtrim($inbs,',');
					$bwhere = "and t.branch_id in($inbs)";
				}
				if($mem_id){
					$inmems = '';
					foreach($mem_id as $ms){
						$parameters[] = $ms;
						$inmems .="?,";
					}
					$inmems = rtrim($inmems,',');
					$mwhere = " and s.member_id in ($inmems)";
				}
				if($dateStart && $dateEnd){
					$parameters[] = strtotime($dateStart);
					$parameters[] = strtotime($dateEnd . "1 day -1sec");
					$datewhere = ' and s.sold_date >=? and s.sold_date <= ?';
				}
				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}
				if($pt == 1){
					// cash
					$q = "Select c.*,s.invoice,s.dr from cash c left join sales s on c.payment_id=s.payment_id left join terminals t on t.id = s.terminal_id left join branches b on b.id=t.branch_id where s.company_id=? and s.is_active=1  $bwhere $mwhere $datewhere order by s.payment_id desc  $l";

				}else if($pt == 2){
					// credit
					$q = "Select c.* from credit_card c left join sales s on c.payment_id=s.payment_id left join terminals t on t.id = s.terminal_id left join branches b on b.id=t.branch_id where s.company_id=? and s.is_active=1 $bwhere $mwhere $datewhere order by s.payment_id desc  $l";

				}else if($pt == 3){
					//bt
					 $q = "Select c.* from bank_transfer c left join sales s on c.payment_id=s.payment_id left join terminals t on t.id = s.terminal_id left join branches b on b.id=t.branch_id where s.company_id=? and s.is_active=1  $bwhere $mwhere $datewhere order by s.payment_id desc $l";

				}else if($pt == 4){
					// cheque
					$q = "Select c.* from cheque c left join sales s on c.payment_id=s.payment_id left join terminals t on t.id = s.terminal_id left join branches b on b.id=t.branch_id where s.company_id=? and s.is_active=1 $bwhere $mwhere $datewhere order by s.payment_id desc $l";

				}else if($pt == 5){
					// con
					$q = "Select c.*,s.invoice,s.dr from payment_consumable c left join sales s on c.payment_id=s.payment_id left join terminals t on t.id = s.terminal_id left join branches b on b.id=t.branch_id where s.company_id=? and s.is_active=1 $bwhere $mwhere $datewhere order by s.payment_id desc  $l";

				}
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function get_active_record($table='',$where=array(),$start=0,$limit=0,$like=''){
			$parameters = array();
			// if table is set and where is 3
			if($table && count($where) == 3) {
				// get the value
				$parameters[] = $where[2];
				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}
				if($like){
					$parameters[] = "%$like%";
					$likewhere = " and invoice like ? ";
				} else {
					$likewhere='';
				}
				// prepare the query
				$q= "Select * from `$table` where $where[0] $where[1] ? and is_active=1 $likewhere $l ";
				//submit the query
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function getTotalSalesPerBranch($company_id=0,$d1=0,$d2=0){
				$parameters = array();
				$parameters[] = $company_id;
				$wheredt='';
				if($d1 && $d2){
					$parameters[] = strtotime($d1);
					$parameters[] = strtotime($d2 . "1 day -1sec");
					$wheredt = ' and s.sold_date >= ? and s.sold_date <= ?';
				}
				$q= "Select sum(((s.qtys * p.price) + s.adjustment + s.member_adjustment)- (s.discount +s.store_discount)) as saletotal,b.name from sales s left join prices p on p.id=s.price_id left join terminals t on t.id=s.terminal_id left join branches b on b.id=t.branch_id where s.company_id = ? $wheredt group by b.id  order by saletotal desc limit 10 ";
				$data = $this->_db->query($q,$parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
		}
		public function getTotalSalesPerMember($company_id=0,$d1=0,$d2=0,$members=[]){
			$parameters = array();
			$parameters[] = $company_id;
			$wheredt='';
			$wheremem = '';
			if($d1 && $d2){
				$parameters[] = $d1;
				$parameters[] = $d2;
				$wheredt = ' and s.sold_date >= ? and s.sold_date <= ?';
			}
			if($members){
				$mm = '';
				foreach($members as $m){
					$mm .= "?,";
					$parameters[] = $m;
				}
				$mm = rtrim($mm,',');
				$wheremem = " and s.member_id in ($mm)";
			}
			 $q= "Select sum(((s.qtys * p.price) + s.adjustment + s.member_adjustment)- (s.discount +s.store_discount)) as saletotal,m.lastname,s.member_id from sales s left join prices p on p.id=s.price_id left join members m on m.id = s.member_id where s.company_id = ? $wheredt  $wheremem group by s.member_id  order by saletotal";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}
		}
		public function getTotalSalesPerSalesType($company_id=0,$d1=0,$d2=0,$type=0,$mem_id= 0,$from_od=0,$branch=0,$from_service=0,$doc_type=0){
			$parameters = array();
			$parameters[] = $company_id;
			$wheredt='';
			$wheredoctype='';
			if($doc_type == 1){
				$wheredoctype =" and s.invoice != '' and s.invoice != '0' ";
			} else if ($doc_type == 2){
				$wheredoctype =" and s.dr != '' and s.dr != '0' ";
			}else if ($doc_type == 3){
				$wheredoctype =" and s.ir != '' and s.ir != '0' ";
			}
			if($d1 && $d2){
				$parameters[] = strtotime($d1);
				$parameters[] = strtotime($d2 . "1 day -1sec");
				$wheredt = ' and s.sold_date >= ? and s.sold_date <= ?';
			}
			if($type){
				$parameters[] =$type;
				$wheretype =' and s.sales_type= ?';
			} else {
				$wheretype =' and s.sales_type= 0';
			}


			if($mem_id){
				$inmems = '';
				foreach($mem_id as $ms){
					$parameters[] = $ms;
					$inmems .="?,";
				}
				$inmems = rtrim($inmems,',');
				$mwhere = " and s.member_id in ($inmems)";
			} else {
				$mwhere = "";
			}
			/* join wh table */
			if($from_od == 1){
				$wherefromOd = " and (s.from_od = 0 or wh.for_pickup = 2)";
			} else if ($from_od == 2){
				$wherefromOd = " and s.from_od = 1 and wh.for_pickup != 2 ";
			} else {
				$wherefromOd="";
			}
			if($branch){
				$inbranch = '';
				foreach($branch as $b){
					$parameters[] = $b;
					$inbranch .="?,";
				}
				$inbranch = rtrim($inbranch,',');
				$branch_where = " and t.branch_id in ($inbranch)";
			} else {
				$branch_where = "";
			}
			$wherefromservice ="";
			if($from_service == 1){
				$wherefromservice = " and wh.from_service = 0 ";
				$wherefromservice = " and it.item_code not like 'P%'";
			} else if ($from_service == 2){
				$wherefromservice = "and wh.from_service != 0  ";
				$wherefromservice = " and it.item_code  like 'P%'";
			}

			 $q= "Select sum(((s.qtys * p.price) + s.adjustment + s.member_adjustment)-(s.discount + s.store_discount)) as saletotal
					from sales s
					left join (Select id, for_pickup,payment_id,user_id,from_service from wh_orders) wh on wh.payment_id = s.payment_id
					left join prices p on p.id=s.price_id
					left join terminals t on t.id=s.terminal_id
					left join branches b on b.id=t.branch_id
					left join items it on it.id = s.item_id
					where s.company_id = ? $wheredt $wheretype and s.status=0  $mwhere $wherefromOd $branch_where $wherefromservice $wheredoctype";

			$data = $this->_db->query($q,$parameters);

			if($data->count()){
				// return the data if exists
				return $data->first();
			}
		}
		public function getTotalSalesPerCashier($company_id=0,$d1=0,$d2=0){

			$parameters = array();
			$parameters[] = $company_id;
			$wheredt='';

			if($d1 && $d2){

				$parameters[] = strtotime($d1);
				$parameters[] = strtotime($d2 . "1 day -1sec");
				$wheredt = ' and s.sold_date >= ? and s.sold_date <= ? ';

			}

			$q= "Select sum(((s.qtys * p.price) + s.adjustment + s.member_adjustment)-(s.discount + s.store_discount)) as saletotal,u.firstname,u.middlename,u.lastname from sales s left join prices p on p.id=s.price_id left join users u on u.id=s.cashier_id where s.company_id = ? $wheredt group by s.cashier_id  order by saletotal desc limit 10 ";

			$data = $this->_db->query($q,$parameters);

			if($data->count()){
				// return the data if exists
				return $data->results();
			}

		}

		public function getTotalSalesBaseOnItem($company_id=0,$d1=0,$d2=0){

			$parameters = array();
			$parameters[] = $company_id;
			$wheredt='';

			if($d1 && $d2){
				$parameters[] = strtotime($d1);
				$parameters[] = strtotime($d2 . "1 day -1sec");
				$wheredt = ' and s.sold_date >= ? and s.sold_date <= ?';
			}

			$q= "Select sum(((s.qtys * p.price) + s.adjustment + s.member_adjustment)-(s.discount + s.store_discount)) as saletotal, i.item_code,i.description from sales s left join prices p on p.id=s.price_id left join terminals t on t.id=s.terminal_id left join branches b on b.id=t.branch_id left join items i on i.id=s.item_id where s.company_id = ? $wheredt group by s.item_id order by saletotal desc limit 10";

			$data = $this->_db->query($q,$parameters);

			if($data->count()){
				// return the data if exists
				return $data->results();
			}

		}

		public function getTotalSalesBaseOnItemQty($company_id=0,$d1=0,$d2=0){
			$parameters = array();
			$parameters[] = $company_id;
			$wheredt='';
			if($d1 && $d2){
				$parameters[] = strtotime($d1);
				$parameters[] = strtotime($d2 . "1 day -1sec");
				$wheredt = ' and s.sold_date >= ? and s.sold_date <= ?';
			}
			$q= "Select sum(s.qtys) as qtytotal, i.item_code,i.description
					from sales s
					left join prices p on p.id=s.price_id
					left join terminals t on t.id=s.terminal_id
					left join branches b on b.id=t.branch_id
					left join items i on i.id=s.item_id
					where s.company_id = ? $wheredt
					group by s.item_id order by qtytotal desc limit 10";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}
		}

		public function cancelPayment($payment_id=0){
			$parameters = array();
			if($payment_id){
				$parameters[] = $payment_id;
			}
			$q= "update sales set status=1 where payment_id =?";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return true;
			}
		}

		public function saveEditedSales($payment_id=0,$inv=0,$dr=0,$dt='',$ir='',$sv='',$from_service=0,$salestype=0){
			$parameters = array();

			if($payment_id && $dt ){
				$parameters[] = $inv;
				$parameters[] = $dr;
				$parameters[] = strtotime($dt);
				$parameters[] = $ir;
				$parameters[] = $sv;
				$parameters[] = $from_service;
				$parameters[] = $salestype;
				$parameters[] = $payment_id;
			}

			$q= "update sales set invoice=?, dr=? , sold_date=? , ir=?, sv=?, is_service=?, sales_type=? where payment_id =?";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return true;
			}
		}
		public function updateInvoiceDr($payment_id=0,$inv=0,$dr=0,$pr=0){
			$parameters = array();
			if($payment_id){

				$parameters[] = $inv;
				$parameters[] = $dr;
				$parameters[] = $pr;
				$parameters[] = $payment_id;
			}
			$q= "update sales set invoice=?, dr=?, ir=? where payment_id =?";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return true;
			}
		}

		public function updateSalestype($payment_id=0,$st=0){

			$parameters = array();
			if($payment_id){

				$parameters[] = $st;
				$parameters[] = $payment_id;
			}
			$q= "update sales set sales_type=? where payment_id =?";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return true;
			}

		}


		public function countRecordR2($cid=0,$payment_method=0,$branch=0,$terminal=0,$item_type=0,$category=0,$memid=0,$stationid=0,$dateStart=0,$dateEnd=0,$cashier=0,$item_id=0,$sales_type=0,$from_od=0,$from_service=0,$doc_type=0,$custom_string_query='',$release_branch_id=0,$date_type=0,$include_cancel=0){

			if($cid){
				$parameters = array();
				$parameters[] = $cid;
				$leftjoincash ='';
				$leftjoincheque ="";
				$leftjoincreditcard ="";
				$leftjoinbanktransfer ="";
				$leftjoinconsumableamount="";
				$whereconsumableamount="";
				$leftjoinconsumablefreebies="";
				$leftjoinmembercredit = '';
				$wheremembercredit = '';
				$whereconsumablefreebies="";
				$wherecash = "";
				$wherecheque = "";
				$wherecreditcard="";
				$wherebanktransfer = "";
				$pwStart = "";
				$pwEnd ="";
				$wherebranch='';
				$whereterminal='';
				$wheremember = '';
				$wherestation = '';
				$wheretimeframe ='';
				$wherecategory ='';
				$whereitemtype='';
				$wherecashier='';
				$whereitemid ='';
				$wheresalestype='';
				$leftjoindeduction ='';
				$wherededuction = '';
				$wherefromservice = '';
				$wheredoctype='';
				$wherestringquery='';
				if($doc_type == 1){
					$wheredoctype =" and s.invoice != '' and s.invoice != '0' ";
				} else if ($doc_type == 2){
					$wheredoctype =" and s.dr != '' and s.dr != '0' ";
				}else if ($doc_type == 3){
					$wheredoctype =" and s.ir != '' and s.ir != '0' ";
				}
				if ($payment_method && !in_array('5',$payment_method)){
					$pwStart = " and (";
					$pwEnd = " )";
					if(in_array('1',$payment_method)){
						$leftjoincash = " left join cash c1 on c1.payment_id = p.id";
						$wherecash = " s.payment_id = c1.payment_id ";
					}
					if(in_array('2',$payment_method)){
						$leftjoincheque = " left join cheque c2 on c2.payment_id = p.id";
						if ($wherecash){
							$wherecheque =  " or s.payment_id = c2.payment_id ";
						} else {
							$wherecheque =  " s.payment_id = c2.payment_id ";
						}

					}
					if(in_array('3',$payment_method)){
						$leftjoincreditcard = " left join credit_card c3 on c3.payment_id = p.id";

						if($wherecash || $wherecheque){
								$wherecreditcard =  " or s.payment_id = c3.payment_id ";
						} else {
							$wherecreditcard =  " s.payment_id = c3.payment_id ";
						}
					}
					if(in_array('4',$payment_method)){
						$leftjoinbanktransfer = " left join bank_transfer c4 on c4.payment_id = p.id";

						if($wherecash || $wherecheque || $wherecreditcard){
							$wherebanktransfer =  " or s.payment_id = c4.payment_id ";
						} else {
							$wherebanktransfer =  " s.payment_id = c4.payment_id ";
						}
					}

					if(in_array('6',$payment_method)){
						$leftjoinconsumableamount = " left join payment_consumable c6 on c6.payment_id = p.id";

						if($wherecash || $wherecheque || $wherecreditcard || $wherebanktransfer){
							$whereconsumableamount =  " or s.payment_id = c6.payment_id ";
						} else {
							$whereconsumableamount =  " s.payment_id = c6.payment_id ";
						}
					}
					if(in_array('7',$payment_method)){
						$leftjoinconsumablefreebies = " left join payment_consumable_freebies c7 on c7.payment_id = p.id";

						if($wherecash || $wherecheque || $wherecreditcard || $wherebanktransfer || $whereconsumableamount){
							$whereconsumablefreebies =  " or s.payment_id = c7.payment_id ";
						} else {
							$whereconsumablefreebies =  " s.payment_id = c7.payment_id ";
						}
					}
					if(in_array('8',$payment_method)){
						$leftjoinmembercredit = " left join member_credit c8 on c8.payment_id = p.id";

						if($wherecash || $wherecheque || $wherecreditcard || $wherebanktransfer || $whereconsumableamount || $leftjoinconsumablefreebies){
							$wheremembercredit =  " or s.payment_id = c8.payment_id ";
						} else {
							$wheremembercredit =  " s.payment_id = c8.payment_id ";
						}
					}

					if(in_array('9',$payment_method)){
						$leftjoindeduction = " left join deductions c9 on c9.payment_id = p.id";

						if($wherecash || $wherecheque || $wherecreditcard || $wherebanktransfer || $whereconsumableamount || $leftjoinconsumablefreebies || $wheremembercredit){
							$wherededuction =  " or s.payment_id = c9.payment_id ";
						} else {
							$wherededuction =  " s.payment_id = c9.payment_id ";
						}
					}
				}
				if ($branch || $terminal){
					if (!$terminal){
						$tempb='';
						foreach($branch as $b){
							$parameters[] = $b;
							$tempb  .='?,';
						}
						$tempb = rtrim($tempb,',');
						$wherebranch = " and t.branch_id in ($tempb)";
					} else {

							$tempt='';
							foreach($terminal as $t){
								$parameters[] = $t;
								$tempt  .='?,';
							}
							$tempt = rtrim($tempt,',');
							$whereterminal = " and s.terminal_id in ($tempt)";

					}
				}
				if ($memid){
					$tempm = '';
					foreach($memid as $m){
						$parameters[] = $m;
						$tempm  .='?,';
					}
					$tempm = rtrim($tempm,',');
					$wheremember = " and s.member_id in ($tempm)";
				}
				if ($stationid){
					$tempst = '';
					foreach($stationid as $st){
						$parameters[] = $st;
						$tempst  .='?,';
					}
					$tempst = rtrim($tempst,',');
					$wherestation = " and s.station_id in ($tempst)";
				}
				if($dateStart && $dateEnd){
					if($date_type == 1){
						if($dateStart && $dateEnd){
							$dateStart = strtotime($dateStart);
							$dateEnd = strtotime($dateEnd . "1 day -1 sec");

							$wheretimeframe = " and (CASE WHEN wh.id IS NULL THEN  s.sold_date >= $dateStart and s.sold_date <= $dateEnd ELSE  wh.is_scheduled >= $dateStart and wh.is_scheduled <= $dateEnd and wh.status = 4  END) ";

						} else {
							$wheretimeframe = " and (CASE WHEN wh.id IS NULL THEN 1 ELSE wh.status = 4 END ) ";

						}
					} else {
						$dateStart = strtotime($dateStart);
						$dateEnd = strtotime($dateEnd . '1 day -1 sec');
						$parameters[] = $dateStart;
						$parameters[] = $dateEnd;
						$wheretimeframe = " and s.sold_date >= ? and s.sold_date <= ?";
					}

				}
				if(!$item_id){
					if ($item_type){
						$tempit = "";
						foreach($item_type as $it){
							$parameters[] = $it;
							$tempit .= "?,";
						}
						$tempit = rtrim($tempit,',');
						$whereitemtype = " and it.item_type in ($tempit)";
					}
					if ($category){
						$tempic = "";
						foreach($category as $c){
							$parameters[] = $c;
							$tempic .= "?,";
						}
						$tempic = rtrim($tempic,',');
						$wherecategory = " and it.category_id in ($tempic)";
					}
					if($custom_string_query){
						$parameters[] = "%$custom_string_query%";
						$parameters[] = "%$custom_string_query%";
						$wherestringquery = " and (it.item_code like ? or it.description like ? )";
					}
				} else {
					$tempitemid = "";
					foreach($item_id as $c){
						$parameters[] = $c;
						$tempitemid .= "?,";
					}
					$tempitemid = rtrim($tempitemid,',');
					$whereitemid = " and s.item_id in ($tempitemid)";
				}


				if ($cashier){
					$tempcas = "";

					foreach($cashier as $ca){
						$parameters[] = $ca;
						$tempcas .= "?,";
					}
					$tempcas = rtrim($tempcas,',');
					$wherecashier = " and s.cashier_id in ($tempcas)";
				}
				if ($sales_type){
					$tempsalestype = "";

					foreach($sales_type as $ca){
						$parameters[] = $ca;
						$tempsalestype .= "?,";
					}
					$tempsalestype = rtrim($tempsalestype,',');
					$wheresalestype = " and s.sales_type in ($tempsalestype)";
				}
				if($from_od == 1){
					$wherefromOd = " and (s.from_od = 0 or wh.for_pickup = 2)";
				} else if ($from_od == 2){
					$wherefromOd = " and s.from_od = 1 and wh.for_pickup != 2 ";
				} else {
					$wherefromOd="";
				}
				if($from_service == 1){
					$wherefromservice = " and wh.from_service = 0 ";
					$wherefromservice = " and it.item_code not like 'P%'";
				} else if ($from_service == 2){
					$wherefromservice = "and wh.from_service != 0  ";
					$wherefromservice = " and it.item_code  like 'P%'";
				}

				if($release_branch_id){
					$release_branch_id = (int) $release_branch_id;
					$where_release = " and wh.branch_id = $release_branch_id ";
				} else {
					$where_release = "";
				}
				$whereStatus =" and s.status=0 ";
				if($include_cancel){
					$whereStatus = "";
				}


				$q= "Select count(s.id) as cnt from sales s left join payments p on p.id = s.payment_id left join (Select id,for_pickup,payment_id,user_id,from_service,branch_id,is_scheduled from wh_orders) wh on wh.payment_id = s.payment_id left join terminals t on t.id=s.terminal_id left join branches b on b.id=t.branch_id left join items it on it.id=s.item_id $leftjoincash $leftjoincheque $leftjoincreditcard $leftjoinbanktransfer $leftjoinconsumableamount $leftjoinconsumablefreebies $leftjoinmembercredit $leftjoindeduction  left join items i on i.id = s.item_id where s.company_id=? $wherefromOd $wherebranch $whereterminal $wheremember $wherestation $wheretimeframe $whereitemtype $wherecategory $wherestringquery $whereitemid $wherecashier $wheresalestype $wherefromservice $wheredoctype  $where_release and s.is_active=1 $pwStart $wherecash $wherecheque $wherecreditcard $wherebanktransfer  $whereconsumableamount $whereconsumablefreebies $wheremembercredit $wherededuction $pwEnd  order by s.payment_id desc";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function totalByPaymentMethod($payment_method='',$dateStart=0,$dateEnd=0){
			$parameters = array();
			if($dateStart && $dateEnd){
				$dateStart = strtotime($dateStart);
				$dateEnd = strtotime($dateEnd . '1 day -1 sec');
				$parameters[] = $dateStart;
				$parameters[] = $dateEnd;
				$wheretimeframe = " and allpt.sold_date >= ? and allpt.sold_date <= ?";
			}
			if($payment_method == 'cash'){
				$payment_method = 'cash';
				$whereptype = ' and allpt.terminal_id != 0  ';
				$colname = "pt.amount";
			} else if($payment_method == 'caravan'){
				$payment_method ='cash';
				$whereptype = ' and allpt.terminal_id = 0 ';
				$colname = "pt.amount";
			} else if ($payment_method == 'cheque'){
				$wherematurity = " and  pt.status in (1,2)";
				$colname = "pt.amount";
			}else if($payment_method == 'member_credit'){
				$colname = "pt.amount - pt.amount_paid";
			} else {
				$colname = "pt.amount";
			}

			$ptype = $payment_method . " pt ";
			// $q= "Select sum(amount) as totalamount from $ptype where payment_id in(Select s.payment_id from sales s left join payments p on p.id = s.payment_id left join terminals t on t.id=s.terminal_id left join branches b on b.id=t.branch_id left join items it on it.id=s.item_id $leftjoincash $leftjoincheque $leftjoincreditcard $leftjoinbanktransfer $leftjoinconsumableamount $leftjoinconsumablefreebies $leftjoinmembercredit  left join items i on i.id = s.item_id where s.company_id=? $wherebranch $whereterminal $wheremember $wherestation $wheretimeframe $whereitemtype $wherecategory $whereitemid $wherecashier $whereptype $wheresalestype and s.is_active=1 and s.status=0 $pwStart $wherecash $wherecheque $wherecreditcard $wherebanktransfer  $whereconsumableamount $whereconsumablefreebies $wheremembercredit $pwEnd group by s.id)  ";
			$q= "Select sum($colname) as totalamount from $ptype left join (Select s.payment_id,s.from_od,s.member_id,s.sales_type, s.sold_date,s.is_active, s.status, s.terminal_id from sales s left join payments p on p.id = s.payment_id group by s.payment_id) allpt on allpt.payment_id = pt.payment_id where 1=1  $wherematurity  $wheretimeframe  $whereptype and allpt.is_active=1 and allpt.status=0   ";

			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				// return the data if exists
				return $data->first();
			}
		}
		public function getTotalSalesR2($cid=0,$payment_method=0,$branch=0,$terminal=0,$item_type=0,$category=0,$memid=0,$stationid=0,$dateStart=0,$dateEnd=0,$cashier=0,$item_id=0,$sales_type=0,$ptype='',$from_od=0,$from_service=0,$doc_type=0,$custom_string_query='',$release_branch_id=0,$date_type=0){

			if($cid){
				$parameters = array();
				$parameters[] = $cid;
				$leftjoincash ='';
				$leftjoincheque ="";
				$leftjoincreditcard ="";
				$leftjoinbanktransfer ="";
				$leftjoinconsumableamount="";
				$whereconsumableamount="";
				$leftjoinconsumablefreebies="";
				$whereconsumablefreebies="";
				$leftjoinmembercredit = '';
				$wheremembercredit = '';
				$wherecash = "";
				$wherecheque = "";
				$wherecreditcard="";
				$wherebanktransfer = "";
				$pwStart = "";
				$pwEnd ="";
				$wherebranch='';
				$whereterminal='';
				$wheremember = '';
				$wherestation = '';
				$wheretimeframe ='';
				$wherecategory ='';
				$whereitemtype='';
				$wherecashier='';
				$whereitemid ='';
				$wheresalestype='';
				$leftjoindeduction ='';
				$wherededuction = '';
				$wherefromservice = "";
				$wheredoctype = "";
				$wherestringquery="";
				if($doc_type == 1){
					$wheredoctype =" and s.invoice != '' and s.invoice != '0' ";
				} else if ($doc_type == 2){
					$wheredoctype =" and s.dr != '' and s.dr != '0' ";
				}else if ($doc_type == 3){
					$wheredoctype =" and s.ir != '' and s.ir != '0' ";
				}
				if ($payment_method && !in_array('5',$payment_method)){
					$pwStart = " and (";
					$pwEnd = " )";
					if(in_array('1',$payment_method)){
						$leftjoincash = " left join cash c1 on c1.payment_id = p.id";
						$wherecash = " s.payment_id = c1.payment_id ";
					}
					if(in_array('2',$payment_method)){
						$leftjoincheque = " left join cheque c2 on c2.payment_id = p.id";
						if ($wherecash){
							$wherecheque =  " or s.payment_id = c2.payment_id ";
						} else {
							$wherecheque =  " s.payment_id = c2.payment_id ";
						}

					}
					if(in_array('3',$payment_method)){
						$leftjoincreditcard = " left join credit_card c3 on c3.payment_id = p.id";

						if($wherecash || $wherecheque){
							$wherecreditcard =  " or s.payment_id = c3.payment_id ";
						} else {
							$wherecreditcard =  " s.payment_id = c3.payment_id ";
						}
					}
					if(in_array('4',$payment_method)){
						$leftjoinbanktransfer = " left join bank_transfer c4 on c4.payment_id = p.id";

						if($wherecash || $wherecheque || $wherecreditcard){
							$wherebanktransfer =  " or s.payment_id = c4.payment_id ";
						} else {
							$wherebanktransfer =  " s.payment_id = c4.payment_id ";
						}
					}

					if(in_array('6',$payment_method)){
						$leftjoinconsumableamount = " left join payment_consumable c6 on c6.payment_id = p.id";

						if($wherecash || $wherecheque || $wherecreditcard || $wherebanktransfer){
							$whereconsumableamount =  " or s.payment_id = c6.payment_id ";
						} else {
							$whereconsumableamount =  " s.payment_id = c6.payment_id ";
						}
					}
					if(in_array('7',$payment_method)){
						$leftjoinconsumablefreebies = " left join payment_consumable_freebies c7 on c7.payment_id = p.id";

						if($wherecash || $wherecheque || $wherecreditcard || $wherebanktransfer || $whereconsumableamount){
							$whereconsumablefreebies =  " or s.payment_id = c7.payment_id ";
						} else {
							$whereconsumablefreebies =  " s.payment_id = c7.payment_id ";
						}
					}
					if(in_array('8',$payment_method)){
						$leftjoinmembercredit = " left join member_credit c8 on c8.payment_id = p.id";

						if($wherecash || $wherecheque || $wherecreditcard || $wherebanktransfer || $whereconsumableamount || $leftjoinconsumablefreebies){
							$wheremembercredit =  " or s.payment_id = c8.payment_id ";
						} else {
							$wheremembercredit =  " s.payment_id = c8.payment_id ";
						}
					}
					if(in_array('9',$payment_method)){
						$leftjoindeduction = " left join deductions c9 on c9.payment_id = p.id";

						if($wherecash || $wherecheque || $wherecreditcard || $wherebanktransfer || $whereconsumableamount || $leftjoinconsumablefreebies || $wheremembercredit){
							$wherededuction =  " or s.payment_id = c9.payment_id ";
						} else {
							$wherededuction =  " s.payment_id = c9.payment_id ";
						}
					}
				}
				if ($branch || $terminal){
					if (!$terminal){
						$tempb='';
						foreach($branch as $b){
							$parameters[] = $b;
							$tempb  .='?,';
						}
						$tempb = rtrim($tempb,',');
						$wherebranch = " and t.branch_id in ($tempb)";

					} else {

							$tempt='';
							foreach($terminal as $t){
								$parameters[] = $t;
								$tempt  .='?,';
							}
							$tempt = rtrim($tempt,',');
							$whereterminal = " and s.terminal_id in ($tempt)";

					}
				}
				if ($memid){
					$tempm = '';
					foreach($memid as $m){
						$parameters[] = $m;
						$tempm  .='?,';
					}
					$tempm = rtrim($tempm,',');
					$wheremember = " and s.member_id in ($tempm)";
				}
				if ($stationid){
					$tempst = '';
					foreach($stationid as $st){
						$parameters[] = $st;
						$tempst  .='?,';
					}
					$tempst = rtrim($tempst,',');
					$wherestation = " and s.station_id in ($tempst)";
				}

				if($dateStart && $dateEnd){
					if($date_type == 1){
						if($dateStart && $dateEnd){
							$dateStart = strtotime($dateStart);
							$dateEnd = strtotime($dateEnd . "1 day -1 sec");

							$wheretimeframe = " and (CASE WHEN wh.id IS NULL THEN  s.sold_date >= $dateStart and s.sold_date <= $dateEnd ELSE  wh.is_scheduled >= $dateStart and wh.is_scheduled <= $dateEnd and wh.status = 4  END) ";

						} else {
							$wheretimeframe = " and (CASE WHEN wh.id IS NULL THEN 1 ELSE wh.status = 4 END ) ";

						}
					} else {
						$dateStart = strtotime($dateStart);
						$dateEnd = strtotime($dateEnd . '1 day -1 sec');
						$parameters[] = $dateStart;
						$parameters[] = $dateEnd;
						$wheretimeframe = " and s.sold_date >= ? and s.sold_date <= ?";
					}

				}

				if(!$item_id){
					if ($item_type){
						$tempit = "";
						foreach($item_type as $it){
							$parameters[] = $it;
							$tempit .= "?,";
						}
						$tempit = rtrim($tempit,',');
						$whereitemtype = " and it.item_type in ($tempit)";
					}
					if ($category){
						$tempic = "";
						foreach($category as $c){
							$parameters[] = $c;
							$tempic .= "?,";
						}
						$tempic = rtrim($tempic,',');
						$wherecategory = " and it.category_id in ($tempic)";
					}
					if($custom_string_query){

						$parameters[] = "%$custom_string_query%";
						$parameters[] = "%$custom_string_query%";

						$wherestringquery = " and (it.item_code like ? or it.description like ? )";
					}
				} else {
					$tempitemid = "";
					foreach($item_id as $c){
						$parameters[] = $c;
						$tempitemid .= "?,";
					}
					$tempitemid = rtrim($tempitemid,',');
					$whereitemid = " and s.item_id in ($tempitemid)";
				}


				if ($cashier){
					$tempcas = "";

					foreach($cashier as $ca){
						$parameters[] = $ca;
						$tempcas .= "?,";
					}
					$tempcas = rtrim($tempcas,',');
					$wherecashier = " and s.cashier_id in ($tempcas)";
				}
				if ($sales_type){
					$tempsalestype = "";

					foreach($sales_type as $ca){
						$parameters[] = $ca;
						$tempsalestype .= "?,";
					}
					$tempsalestype = rtrim($tempsalestype,',');
					$wheresalestype = " and s.sales_type in ($tempsalestype)";
				}
				$whereptype ='';
				$wherematurity = '';
				if($ptype == 'cash'){
					$ptype = 'cash';
					$whereptype = ' and s.terminal_id != 0  ';
					$colname = "pt.amount";
				} else if($ptype == 'caravan'){
					$ptype ='cash';
					$whereptype = ' and s.terminal_id = 0 ';
					$colname = "pt.amount";
				} else if ($ptype == 'cheque'){
					$now = strtotime(date('m/d/Y'),time());
					$wherematurity = " and  pt.status in (1,2)";
					$colname = "pt.amount";
				}else if($ptype == 'member_credit'){
					$colname = "pt.amount - pt.amount_paid";
				} else {
					$colname = "pt.amount";
				}
				if($from_od == 1){
					$wherefromOd = " and (s.from_od = 0 or wh.for_pickup = 2)";
				} else if ($from_od == 2){
					$wherefromOd = " and s.from_od = 1 and wh.for_pickup != 2 ";
				}else {
					$wherefromOd="";
				}

				if($from_service == 1){
					$wherefromservice = " and wh.from_service = 0 ";
					$wherefromservice = " and it.item_code not like 'P%'";
				} else if ($from_service == 2){
					$wherefromservice = "and wh.from_service != 0  ";
					$wherefromservice = " and it.item_code like 'P%'";
				}

				if($release_branch_id){
					$release_branch_id = (int) $release_branch_id;
					$where_release = " and wh.branch_id = $release_branch_id ";
				} else {
					$where_release = "";
				}

				$whereptcash = " and pt.payment_id = allpt.payment_id ";
				$ptype = $ptype . " pt ";
				// $q= "Select sum(amount) as totalamount from $ptype where payment_id in(Select s.payment_id from sales s left join payments p on p.id = s.payment_id left join terminals t on t.id=s.terminal_id left join branches b on b.id=t.branch_id left join items it on it.id=s.item_id $leftjoincash $leftjoincheque $leftjoincreditcard $leftjoinbanktransfer $leftjoinconsumableamount $leftjoinconsumablefreebies $leftjoinmembercredit  left join items i on i.id = s.item_id where s.company_id=? $wherebranch $whereterminal $wheremember $wherestation $wheretimeframe $whereitemtype $wherecategory $whereitemid $wherecashier $whereptype $wheresalestype and s.is_active=1 and s.status=0 $pwStart $wherecash $wherecheque $wherecreditcard $wherebanktransfer  $whereconsumableamount $whereconsumablefreebies $wheremembercredit $pwEnd group by s.id)  ";
				 $q= "Select
					sum($colname) as totalamount
					from $ptype
					left join (
					Select s.payment_id,s.from_od,s.member_id,s.sales_type
					from sales s
					left join payments p on p.id = s.payment_id
					left join (Select id, for_pickup,payment_id,user_id,from_service, branch_id, is_scheduled, status from wh_orders)
					wh on wh.payment_id = s.payment_id
					left join terminals t on t.id=s.terminal_id
					left join branches b on b.id=t.branch_id
					left join items it on it.id=s.item_id
					$leftjoincash
					$leftjoincheque
					$leftjoincreditcard
					$leftjoinbanktransfer
					$leftjoinconsumableamount
					$leftjoinconsumablefreebies
					$leftjoinmembercredit
					$leftjoindeduction
					left join items i on i.id = s.item_id
					where s.company_id=?
					$wherefromOd
					$wherebranch
					$whereterminal
					$wheremember
					$wherestation
					$wheretimeframe
					$whereitemtype
					$wherecategory
					$wherestringquery
					$whereitemid
					$wherecashier
					$whereptype
					$wheresalestype
					$wherefromservice
					$wheredoctype
					$where_release
					and s.is_active=1
					and s.status=0
					$pwStart
					$wherecash
					$wherecheque
					$wherecreditcard
					$wherebanktransfer
					$whereconsumableamount
					$whereconsumablefreebies
					$wheremembercredit
					$wherededuction
					$pwEnd
					group by s.payment_id)
					allpt
					on allpt.payment_id = pt.payment_id
					where 1=1
					$whereptcash
					$wherematurity   ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function matchPaymentSales($cid=0,$payment_id=0){
			if($payment_id && $cid){
				$parameters = array();
				$parameters[] = $cid;
				$payment_id = (int) $payment_id;
				 $q = "Select
					round(sum(((s.qtys * p.price) + s.adjustment + s.member_adjustment) - (s.discount + s.store_discount)),2) as ttotal,
					round(coalesce(c.cashamount,0),2) as cashamount,
					round(coalesce(ch.chequeamount,0),2) as chequeamount,
					round(coalesce(bt.btamount,0),2) as btamount,
					round(coalesce(cc.ccamount,0),2) as ccamount,
					round(coalesce(mc.mcamount,0),2) as mcamount,
					round(coalesce(dd.deduction,0),2) as deduction,
					round(coalesce(pc.pcamount,0),2) as pcamount,
					round(coalesce(pcf.pcfamount,0),2) as pcfamount
					from sales s
					left join prices p on p.id = s.price_id
					left join (Select sum(amount) as cashamount,payment_id from cash where payment_id=$payment_id) c on c.payment_id = s.payment_id
					left join (Select sum(amount) as chequeamount,payment_id from cheque where payment_id=$payment_id and status =1) ch on ch.payment_id = s.payment_id
					left join (Select sum(amount) as btamount,payment_id from bank_transfer where payment_id=$payment_id) bt on bt.payment_id = s.payment_id
					left join (Select sum(amount) as ccamount,payment_id from credit_card where payment_id=$payment_id) cc on cc.payment_id = s.payment_id
					left join (Select sum(amount) as deduction,payment_id from deductions where payment_id=$payment_id) dd on dd.payment_id = s.payment_id
					left join (Select sum(amount - amount_paid) as mcamount,payment_id from member_credit where payment_id=$payment_id) mc on mc.payment_id = s.payment_id
					left join (Select sum(amount) as pcamount,payment_id from payment_consumable where payment_id=$payment_id) pc on pc.payment_id = s.payment_id
					left join (Select sum(amount) as pcfamount,payment_id from payment_consumable_freebies where payment_id=$payment_id) pcf on pcf.payment_id = s.payment_id
					where s.company_id = $cid and s.status = 0 and s.payment_id =$payment_id";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function getSalesForDownload($cid=0,$payment_method=0,$branch=0,$terminal=0,$item_type=0,$category=0,$memid=0,$stationid=0,$dateStart=0,$dateEnd=0,$cashier=0,$item_id=0,$sales_type=0,$sort_by,$from_od=0,$query_string="",$from_service=0,$doc_type=0,$custom_string_query='',$release_branch_id=0,$date_type=0){
			if($cid){
				$parameters = array();
				$parameters[] = $cid;
				$leftjoincash ='';
				$leftjoincheque ="";
				$leftjoincreditcard ="";
				$leftjoinbanktransfer ="";
				$leftjoinconsumableamount="";
				$whereconsumableamount="";
				$leftjoinconsumablefreebies="";
				$whereconsumablefreebies="";
				$leftjoinmembercredit = '';
				$wheremembercredit = '';
				$wherecash = "";
				$wherecheque = "";
				$wherecreditcard="";
				$wherebanktransfer = "";
				$pwStart = "";
				$pwEnd ="";
				$wherebranch='';
				$whereterminal='';
				$wheremember = '';
				$wherestation = '';
				$wheretimeframe='';
				$wherecategory ='';
				$whereitemtype='';
				$wherecashier = '';
				$whereitemid='';
				$wheresalestype='';
				$leftjoindeduction = '';
				$wherededuction = '';
				$wherefromservice = "";
				$wheredoctype='';
				$wherestringquery ='';
				if($doc_type == 1){
					$wheredoctype =" and s.invoice != '' and s.invoice != '0' ";
				} else if ($doc_type == 2){
					$wheredoctype =" and s.dr != '' and s.dr != '0' ";
				}else if ($doc_type == 3){
					$wheredoctype =" and s.ir != '' and s.ir != '0' ";
				}
				if($sort_by){
					$sort_by = trim($sort_by);
					$arr_valid = ['order by m.lastname desc',
						'order by IF (IFNULL(s.invoice,0) = 0, 1, 0), s.invoice * 1 desc',
						'order by IF (IFNULL(s.dr,0) = 0, 1, 0), s.dr * 1 desc',
						'order by IF (IFNULL(s.sr,0) = 0, 1, 0), s.sr * 1 desc',
						'order by IF (IFNULL(s.ir,0) = 0, 1, 0), s.ir * 1 desc',
						'order by IF (IFNULL(s.sv,0) = 0, 1, 0), s.sv * 1 desc',
						'order by i.item_code desc','order by pr.price desc',
						'order by s.qtys desc',
						'order by s.discount desc',
						'order by ((s.qtys * price)-s.discount) desc',
						'order by s.sold_date desc'];
					if(!in_array($sort_by,$arr_valid)){
						$sort_by = "";
					}
				} else {
					$sort_by = "order by s.payment_id desc, s.invoice desc, s.dr desc";
				}
				if ($payment_method && !in_array('5',$payment_method)){
					$pwStart = " and (";
					$pwEnd = " )";
					if(in_array('1',$payment_method)){
						$leftjoincash = " left join cash c1 on c1.payment_id = p.id";
						$wherecash = " s.payment_id = c1.payment_id ";
					}
					if(in_array('2',$payment_method)){
						$leftjoincheque = " left join cheque c2 on c2.payment_id = p.id";
						if ($wherecash){
							$wherecheque =  " or s.payment_id = c2.payment_id ";
						} else {
							$wherecheque =  " s.payment_id = c2.payment_id ";
						}

					}
					if(in_array('3',$payment_method)){
						$leftjoincreditcard = " left join credit_card c3 on c3.payment_id = p.id";

						if($wherecash || $wherecheque){
							$wherecreditcard =  " or s.payment_id = c3.payment_id ";
						} else {
							$wherecreditcard =  " s.payment_id = c3.payment_id ";
						}
					}
					if(in_array('4',$payment_method)){
						$leftjoinbanktransfer = " left join bank_transfer c4 on c4.payment_id = p.id";

						if($wherecash || $wherecheque || $wherecreditcard){
							$wherebanktransfer =  " or s.payment_id = c4.payment_id ";
						} else {
							$wherebanktransfer =  " s.payment_id = c4.payment_id ";
						}
					}

					if(in_array('6',$payment_method)){
						$leftjoinconsumableamount = " left join payment_consumable c6 on c6.payment_id = p.id";

						if($wherecash || $wherecheque || $wherecreditcard || $wherebanktransfer){
							$whereconsumableamount =  " or s.payment_id = c6.payment_id ";
						} else {
							$whereconsumableamount =  " s.payment_id = c6.payment_id ";
						}
					}
					if(in_array('7',$payment_method)){

						$leftjoinconsumablefreebies = " left join payment_consumable_freebies c7 on c7.payment_id = p.id";

						if($wherecash || $wherecheque || $wherecreditcard || $wherebanktransfer || $whereconsumableamount){
							$whereconsumablefreebies =  " or s.payment_id = c7.payment_id ";
						} else {
							$whereconsumablefreebies =  " s.payment_id = c7.payment_id ";
						}
					}
					if(in_array('8',$payment_method)){
						$leftjoinmembercredit = " left join member_credit c8 on c8.payment_id = p.id";

						if($wherecash || $wherecheque || $wherecreditcard || $wherebanktransfer || $whereconsumableamount || $leftjoinconsumablefreebies){
							$wheremembercredit =  " or s.payment_id = c8.payment_id ";
						} else {
							$wheremembercredit =  " s.payment_id = c8.payment_id ";
						}
					}

					if(in_array('9',$payment_method)){
						$leftjoindeduction = " left join deductions c9 on c9.payment_id = p.id";

						if($wherecash || $wherecheque || $wherecreditcard || $wherebanktransfer || $whereconsumableamount || $leftjoinconsumablefreebies || $wheremembercredit){
							$wherededuction =  " or s.payment_id = c9.payment_id ";
						} else {
							$wherededuction =  " s.payment_id = c9.payment_id ";
						}
					}

				}

				if ($branch || $terminal){
					if (!$terminal){
						$tempb='';
						foreach($branch as $b){
							$parameters[] = $b;
							$tempb  .='?,';
						}
						$tempb = rtrim($tempb,',');
						$wherebranch = " and t.branch_id in ($tempb)";
					} else {

						$tempt='';
						foreach($terminal as $t){
							$parameters[] = $t;
							$tempt  .='?,';
						}
						$tempt = rtrim($tempt,',');
						$whereterminal = " and s.terminal_id in ($tempt)";
					}
				}
				if ($memid || $stationid){
					if (!$stationid){
						$tempm = '';
						foreach($memid as $m){
							$parameters[] = $m;
							$tempm  .='?,';
						}
						$tempm = rtrim($tempm,',');
						$wheremember = " and s.member_id in ($tempm)";
					} else {
						$tempst = '';
						foreach($stationid as $st){
							$parameters[] = $st;
							$tempst  .='?,';
						}
						$tempst = rtrim($tempst,',');
						$wherestation = " and s.station_id in ($tempst)";
					}
				}


				if($date_type == 1){
					if($dateStart && $dateEnd){
						$dateStart = strtotime($dateStart);
						$dateEnd = strtotime($dateEnd . "1 day -1 sec");
						$wheretimeframe = " and (CASE WHEN wh.id IS NULL THEN s.sold_date >= $dateStart and s.sold_date <= $dateEnd ELSE  wh.is_scheduled >= $dateStart and wh.is_scheduled <= $dateEnd and wh.status = 4  END) ";

					} else {
						$wheretimeframe = " and (CASE WHEN wh.id IS NULL THEN 1 ELSE wh.status = 4 END ) ";
					}
				} else {
					$dateStart = strtotime($dateStart);
					$dateEnd = strtotime($dateEnd . '1 day -1 sec');
					$parameters[] = $dateStart;
					$parameters[] = $dateEnd;
					$wheretimeframe = " and s.sold_date >= ? and s.sold_date <= ?";
				}

				if(!$item_id){
					if ($item_type){
						$tempit = "";
						foreach($item_type as $it){
							$parameters[] = $it;
							$tempit .= "?,";
						}
						$tempit = rtrim($tempit,',');
						$whereitemtype = " and it.item_type in ($tempit)";
					}
					if ($category){
						$tempic = "";
						foreach($category as $c){
							$parameters[] = $c;
							$tempic .= "?,";
						}
						$tempic = rtrim($tempic,',');
						$wherecategory = " and it.category_id in ($tempic)";
					}
					if($custom_string_query){
						$parameters[] = "%$custom_string_query%";
						$parameters[] = "%$custom_string_query%";
						$wherestringquery = " and (it.item_code like ? or it.description like ?)";
					}
				} else {

					$tempitemid = "";
					foreach($item_id as $c){
						$parameters[] = $c;
						$tempitemid .= "?,";
					}
					$tempitemid = rtrim($tempitemid,',');
					$whereitemid = " and s.item_id in ($tempitemid)";

				}
				if ($cashier){
					$tempcas = "";

					foreach($cashier as $ca){
						$parameters[] = $ca;
						$tempcas .= "?,";
					}
					$tempcas = rtrim($tempcas,',');
					$wherecashier = " and s.cashier_id in ($tempcas)";
				}
				if ($sales_type){
					$tempsalestype = "";

					foreach($sales_type as $ca){
						$parameters[] = $ca;
						$tempsalestype .= "?,";
					}
					$tempsalestype = rtrim($tempsalestype,',');
					$wheresalestype = " and s.sales_type in ($tempsalestype)";
				}
				if($from_od == 1){
					$wherefromOd = " and (s.from_od = 0 or wh.for_pickup = 2)";
				} else if ($from_od == 2){
					$wherefromOd = " and s.from_od = 1 and wh.for_pickup != 2 ";
				} else {
					$wherefromOd="";
				}
				$where_string = "";
				if($query_string){
					if($query_string == "A"){

						$where_string = " and it.item_code like 'A%' ";
					} else {
						$parameters[] = "%$query_string%";
						$where_string = " and it.description like ? ";
					}

				}
				if($from_service == 1){
					// $wherefromservice = " and wh.from_service = 0 ";
					$wherefromservice = " and it.item_code not like 'P%'";
				} else if ($from_service == 2){
					// $wherefromservice = "and wh.from_service != 0  ";
					$wherefromservice = " and it.item_code like 'P%'";
				} else if ($from_service == 3){
					$wherefromservice = "and (wh.from_service != 0  or s.is_service = 1 )";

				}

				if($release_branch_id){
					$release_branch_id = (int) $release_branch_id;
					$where_release= " and wh.branch_id = $release_branch_id ";
				} else {
					$where_release="";
				}

				$q= "Select s.*,m.lastname as member_name, t.name as tname,b.name as bname,it.is_bundle,it.item_code,it.description,pr.price, st.name as sales_type_name, wh.is_scheduled
					from sales s
					left join payments p on p.id = s.payment_id
	 				left join (Select id, for_pickup,payment_id,user_id,from_service,branch_id,is_scheduled,status from wh_orders) wh on wh.payment_id = s.payment_id
	 				left join terminals t on t.id=s.terminal_id left join branches b on b.id=t.branch_id
	 				left join items it on it.id=s.item_id left join prices pr on pr.id=s.price_id left join salestypes st on st.id = s.sales_type left join members m on m.id = s.member_id  $leftjoincash $leftjoincheque $leftjoincreditcard $leftjoinbanktransfer $leftjoinconsumableamount $leftjoinconsumablefreebies $leftjoinmembercredit $leftjoindeduction  where s.company_id=?  and s.status = 0 $where_release $wherefromOd $wherebranch $whereterminal $wheremember $wherestation $wheretimeframe $whereitemtype $wherecategory $wherestringquery $whereitemid $wherecashier $wheresalestype $wherefromservice $wheredoctype  and s.is_active=1 $pwStart $wherecash $wherecheque $wherecreditcard $wherebanktransfer $whereconsumableamount $whereconsumablefreebies $wheremembercredit $wherededuction $pwEnd $where_string group by s.id $sort_by ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function getSalesForDownload2($cid=0,$payment_method=0,$branch=0,$terminal=0,$item_type=0,$category=0,$memid=0,$stationid=0,$dateStart=0,$dateEnd=0,$cashier=0,$item_id=0,$sales_type=0,$sort_by='',$from_od=0,$from_service =0,$release_branch_id=0,$doc_type=0,$custom_string_query='',$date_type=0,$include_cancel=0){
			if($cid){
				$parameters = array();
				$parameters[] = $cid;

				$leftjoincash ='';
				$leftjoincheque ="";
				$leftjoincreditcard ="";
				$leftjoinbanktransfer ="";
				$leftjoinconsumableamount="";
				$whereconsumableamount="";
				$leftjoinconsumablefreebies="";
				$whereconsumablefreebies="";
				$leftjoinmembercredit = '';
				$wheremembercredit = '';
				$wherecash = "";
				$wherecheque = "";
				$wherecreditcard="";
				$wherebanktransfer = "";
				$pwStart = "";
				$pwEnd ="";
				$wherebranch='';
				$whereterminal='';
				$wheremember = '';
				$wherestation = '';
				$wheretimeframe='';
				$wherecategory ='';
				$whereitemtype='';
				$wherecashier = '';
				$whereitemid='';
				$wheresalestype='';
				$leftjoindeduction = '';
				$wherededuction ='';
				$wherefromservice ='';
				$wheredoctype='';
				if($doc_type == 1){
					$wheredoctype =" and s.invoice != '' and s.invoice != '0' ";
				} else if ($doc_type == 2){
					$wheredoctype =" and s.dr != '' and s.dr != '0' ";
				}else if ($doc_type == 3){
					$wheredoctype =" and s.ir != '' and s.ir != '0' ";
				}
				if($sort_by){
					$sort_by = trim($sort_by);
					$arr_valid = ['order by m.lastname desc',
						'order by IF (IFNULL(s.invoice,0) = 0, 1, 0), s.invoice * 1 desc',
						'order by IF (IFNULL(s.dr,0) = 0, 1, 0), s.dr * 1 desc',
						'order by IF (IFNULL(s.sr,0) = 0, 1, 0), s.sr * 1 desc',
						'order by IF (IFNULL(s.ir,0) = 0, 1, 0), s.ir * 1 desc',
						'order by IF (IFNULL(s.sv,0) = 0, 1, 0), s.sv * 1 desc',
						'order by i.item_code desc','order by pr.price desc',
						'order by s.qtys desc',
						'order by s.discount desc',
						'order by ((s.qtys * price)-s.discount) desc',
						'order by s.sold_date desc'];
					if(!in_array($sort_by,$arr_valid)){
						$sort_by = "";
					}
				} else {
					$sort_by = "order by s.payment_id desc, s.invoice desc, s.dr desc";
				}
				if ($payment_method && !in_array('5',$payment_method)){
					$pwStart = " and (";
					$pwEnd = " )";
					if(in_array('1',$payment_method)){
						$leftjoincash = " left join cash c1 on c1.payment_id = p.id";
						$wherecash = " s.payment_id = c1.payment_id ";
					}
					if(in_array('2',$payment_method)){
						$leftjoincheque = " left join cheque c2 on c2.payment_id = p.id";
						if ($wherecash){
							$wherecheque =  " or s.payment_id = c2.payment_id ";
						} else {
							$wherecheque =  " s.payment_id = c2.payment_id ";
						}

					}
					if(in_array('3',$payment_method)){
						$leftjoincreditcard = " left join credit_card c3 on c3.payment_id = p.id";

						if($wherecash || $wherecheque){
							$wherecreditcard =  " or s.payment_id = c3.payment_id ";
						} else {
							$wherecreditcard =  " s.payment_id = c3.payment_id ";
						}
					}
					if(in_array('4',$payment_method)){
						$leftjoinbanktransfer = " left join bank_transfer c4 on c4.payment_id = p.id";

						if($wherecash || $wherecheque || $wherecreditcard){
							$wherebanktransfer =  " or s.payment_id = c4.payment_id ";
						} else {
							$wherebanktransfer =  " s.payment_id = c4.payment_id ";
						}
					}

					if(in_array('6',$payment_method)){
						$leftjoinconsumableamount = " left join payment_consumable c6 on c6.payment_id = p.id";

						if($wherecash || $wherecheque || $wherecreditcard || $wherebanktransfer){
							$whereconsumableamount =  " or s.payment_id = c6.payment_id ";
						} else {
							$whereconsumableamount =  " s.payment_id = c6.payment_id ";
						}
					}
					if(in_array('7',$payment_method)){

						$leftjoinconsumablefreebies = " left join payment_consumable_freebies c7 on c7.payment_id = p.id";

						if($wherecash || $wherecheque || $wherecreditcard || $wherebanktransfer || $whereconsumableamount){
							$whereconsumablefreebies =  " or s.payment_id = c7.payment_id ";
						} else {
							$whereconsumablefreebies =  " s.payment_id = c7.payment_id ";
						}
					}
					if(in_array('8',$payment_method)){
						$leftjoinmembercredit = " left join member_credit c8 on c8.payment_id = p.id";

						if($wherecash || $wherecheque || $wherecreditcard || $wherebanktransfer || $whereconsumableamount || $leftjoinconsumablefreebies){
							$wheremembercredit =  " or s.payment_id = c8.payment_id ";
						} else {
							$wheremembercredit =  " s.payment_id = c8.payment_id ";
						}
					}

					if(in_array('9',$payment_method)){
						$leftjoindeduction = " left join deductions c9 on c9.payment_id = p.id";

						if($wherecash || $wherecheque || $wherecreditcard || $wherebanktransfer || $whereconsumableamount || $leftjoinconsumablefreebies || $wheremembercredit){
							$wherededuction =  " or s.payment_id = c9.payment_id ";
						} else {
							$wherededuction =  " s.payment_id = c9.payment_id ";
						}
					}
				}
				if ($branch || $terminal){
					if (!$terminal){
						$tempb='';
						foreach($branch as $b){
							$b = (int) $b;
							$tempb  .=$b.',';
						}
						$tempb = rtrim($tempb,',');
						$wherebranch = " and t.branch_id in ($tempb)";
					} else {

						$tempt='';
						foreach($terminal as $t){
							$t = (int) $t;
							$tempt  .=$t.',';
						}
						$tempt = rtrim($tempt,',');
						$whereterminal = " and s.terminal_id in ($tempt)";
					}
				}
				if($date_type == 1){
					if($dateStart && $dateEnd){
						$dateStart = strtotime($dateStart);
						$dateEnd = strtotime($dateEnd . "1 day -1 sec");
						$wheretimeframe = " and (CASE WHEN wh.id IS NULL THEN s.sold_date >= $dateStart and s.sold_date <= $dateEnd ELSE  wh.is_scheduled >= $dateStart and wh.is_scheduled <= $dateEnd and wh.status = 4  END) ";

					} else {
						$wheretimeframe = " and (CASE WHEN wh.id IS NULL THEN 1 ELSE wh.status = 4 END ) ";
					}
				} else {
					$dateStart = strtotime($dateStart);
					$dateEnd = strtotime($dateEnd . '1 day -1 sec');
					$parameters[] = $dateStart;
					$parameters[] = $dateEnd;
					$wheretimeframe = " and s.sold_date >= ? and s.sold_date <= ?";
				}
				if ($memid || $stationid){
					if (!$stationid){
						$tempm = '';
						foreach($memid as $m){
							$parameters[] = $m;
							$tempm  .='?,';
						}
						$tempm = rtrim($tempm,',');
						$wheremember = " and s.member_id in ($tempm)";
					} else {
						$tempst = '';
						foreach($stationid as $st){
							$parameters[] = $st;
							$tempst  .='?,';
						}
						$tempst = rtrim($tempst,',');
						$wherestation = " and s.station_id in ($tempst)";
					}
				}
				if($from_od == 1){
					$wherefromOd = " and (s.from_od = 0 or wh.for_pickup = 2)";
				} else if ($from_od == 2){
					$wherefromOd = " and s.from_od = 1 and wh.for_pickup != 2 ";
				} else {
					$wherefromOd="";
				}
				if($from_service == 1){
					$wherefromservice = " and wh.from_service = 0 ";
					$wherefromservice = " and it.item_code not like 'P%'";
				} else if ($from_service == 2){
					$wherefromservice = "and wh.from_service != 0  ";
					$wherefromservice = " and it.item_code like 'P%'";
				} else if ($from_service == 3){
					$wherefromservice = "and (wh.from_service != 0  or s.is_service = 1 )";

				}
				$wheresalestype="";
				if ($sales_type){
					$tempsalestype = "";

					foreach($sales_type as $ca){
						$parameters[] = $ca;
						$tempsalestype .= "?,";
					}
					$tempsalestype = rtrim($tempsalestype,',');
					$wheresalestype = " and s.sales_type in ($tempsalestype)";
				}
				if($release_branch_id){
					$release_branch_id = (int) $release_branch_id;
					$where_release= " and wh.branch_id = $release_branch_id ";
				} else {
					$where_release="";
				}

				if($custom_string_query){
					$parameters[] = "%$custom_string_query%";
					$parameters[] = "%$custom_string_query%";
					$wherestringquery = " and (it.item_code like ? or it.description like ?)";
				}

				$whereStatus =" and s.status=0 ";
				if($include_cancel){
					$whereStatus = "";
				}

				$q= "Select
						sum(((s.qtys * pr.price) + s.adjustment + s.member_adjustment)- (s.discount + s.store_discount)) as totalamount,
						 s.payment_id,s.invoice,s.dr,s.pref_inv,s.pref_dr,s.pref_ir,s.sold_date,s.ir,s.sv,s.pref_sv,s.suf_sv,
						 t.name as tname,
						 b.name as bname,
						  m.lastname as mln, m.firstname as mfn,m.tin_no, m.personal_address,
						  s.cashier_id,od.user_id as reserved_by ,
						  st.name as sales_type_name ,
						  u.lastname as reserved_by_lastname,
						  u.firstname as reserved_by_firstname,
						  stat.name as station_name,
						  wh.wh_remarks, wh.is_scheduled, s.status
						  from sales s
						  left join payments p on p.id = s.payment_id
						  left join orders od on od.payment_id = s.payment_id
						  left join (select remarks as wh_remarks,id,user_id,payment_id,from_service,branch_id, for_pickup,is_scheduled,status from wh_orders ) wh on wh.payment_id = s.payment_id
						  left join users u on u.id = wh.user_id
						  left join terminals t on t.id=s.terminal_id
						  left join branches b on b.id=t.branch_id
						  left join items it on it.id=s.item_id
						  left join items i on i.id = s.item_id
						  left join prices pr on pr.id=s.price_id
						  left join members m on m.id = s.member_id
						  left join salestypes st on st.id=s.sales_type
						  left join stations stat on stat.id = s.station_id
						  $leftjoincash $leftjoincheque $leftjoincreditcard $leftjoinbanktransfer $leftjoinconsumableamount $leftjoinconsumablefreebies $leftjoinmembercredit $leftjoindeduction
						  where s.company_id=? and s.is_active=1 $whereStatus $where_release $wherefromservice
						   $pwStart $wherecash $wherecheque $wherecreditcard $wherebanktransfer $whereconsumableamount $whereconsumablefreebies $wheremembercredit $wherededuction $pwEnd
						  $wheretimeframe $wherebranch $whereterminal $wheremember $wherestation $wherefromOd $wheresalestype $wheredoctype $wherestringquery
						  group by s.payment_id $sort_by ";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function getSalesR2($cid=0,$start,$limit,$payment_method=0,$branch=0,$terminal=0,$item_type=0,$category=0,$memid=0,$stationid=0,$dateStart=0,$dateEnd=0,$cashier=0,$item_id=0,$sales_type=0,$sort_by,$from_od=0,$from_service=0,$doc_type=0,$custom_string_query='',$release_branch_id=0,$date_type=0){
			if($cid){
				$parameters = array();
				$parameters[] = $cid;
				$leftjoincash ='';
				$leftjoincheque ="";
				$leftjoincreditcard ="";
				$leftjoinbanktransfer ="";
				$leftjoinconsumableamount="";
				$whereconsumableamount="";
				$leftjoinconsumablefreebies="";
				$whereconsumablefreebies="";
				$leftjoinmembercredit = '';
				$wheremembercredit = '';
				$wherecash = "";
				$wherecheque = "";
				$wherecreditcard="";
				$wherebanktransfer = "";
				$pwStart = "";
				$pwEnd ="";
				$wherebranch='';
				$whereterminal='';
				$wheremember = '';
				$wherestation = '';
				$wheretimeframe='';
				$wherecategory ='';
				$whereitemtype='';
				$wherecashier = '';
				$whereitemid='';
				$wheresalestype='';
				$leftjoindeduction = '';
				$wherededuction ='';
				$wherefromservice = '';
				$wheredoctype='';
				$wherestringquery='';
				if($doc_type == 1){
					$wheredoctype =" and s.invoice != '' and s.invoice != '0' ";
				} else if ($doc_type == 2){
					$wheredoctype =" and s.dr != '' and s.dr != '0' ";
				}else if ($doc_type == 3){
					$wheredoctype =" and s.ir != '' and s.ir != '0' ";
				}
				if($sort_by){
					$sort_by = trim($sort_by);
					$arr_valid = ['order by m.lastname desc',
						'order by IF (IFNULL(s.invoice,0) = 0, 1, 0), s.invoice * 1 desc',
						'order by IF (IFNULL(s.dr,0) = 0, 1, 0), s.dr * 1 desc',
						'order by IF (IFNULL(s.sr,0) = 0, 1, 0), s.sr * 1 desc',
						'order by IF (IFNULL(s.ir,0) = 0, 1, 0), s.ir * 1 desc',
						'order by IF (IFNULL(s.sv,0) = 0, 1, 0), s.sv * 1 desc',
						'order by i.item_code desc','order by pr.price desc',
						'order by s.qtys desc',
						'order by s.discount desc',
						'order by ((s.qtys * price)-s.discount) desc',
						'order by s.sold_date desc'];
					if(!in_array($sort_by,$arr_valid)){
						$sort_by = "";
					}
				} else {
					$sort_by = "order by s.payment_id desc, s.invoice desc, s.dr desc";
				}
				if ($payment_method && !in_array('5',$payment_method)){
					$pwStart = " and (";
					$pwEnd = " )";
					if(in_array('1',$payment_method)){
						$leftjoincash = " left join cash c1 on c1.payment_id = p.id";
						$wherecash = " s.payment_id = c1.payment_id ";
					}
					if(in_array('2',$payment_method)){
						$leftjoincheque = " left join cheque c2 on c2.payment_id = p.id";
						if ($wherecash){
							$wherecheque =  " or s.payment_id = c2.payment_id ";
						} else {
							$wherecheque =  " s.payment_id = c2.payment_id ";
						}

					}
					if(in_array('3',$payment_method)){
						$leftjoincreditcard = " left join credit_card c3 on c3.payment_id = p.id";

						if($wherecash || $wherecheque){
								$wherecreditcard =  " or s.payment_id = c3.payment_id ";
						} else {
							$wherecreditcard =  " s.payment_id = c3.payment_id ";
						}
					}
					if(in_array('4',$payment_method)){
						$leftjoinbanktransfer = " left join bank_transfer c4 on c4.payment_id = p.id";

						if($wherecash || $wherecheque || $wherecreditcard){
							$wherebanktransfer =  " or s.payment_id = c4.payment_id ";
						} else {
							$wherebanktransfer =  " s.payment_id = c4.payment_id ";
						}
					}

					if(in_array('6',$payment_method)){
						$leftjoinconsumableamount = " left join payment_consumable c6 on c6.payment_id = p.id";

						if($wherecash || $wherecheque || $wherecreditcard || $wherebanktransfer){
							$whereconsumableamount =  " or s.payment_id = c6.payment_id ";
						} else {
							$whereconsumableamount =  " s.payment_id = c6.payment_id ";
						}
					}
					if(in_array('7',$payment_method)){

						$leftjoinconsumablefreebies = " left join payment_consumable_freebies c7 on c7.payment_id = p.id";

						if($wherecash || $wherecheque || $wherecreditcard || $wherebanktransfer || $whereconsumableamount){
							$whereconsumablefreebies =  " or s.payment_id = c7.payment_id ";
						} else {
							$whereconsumablefreebies =  " s.payment_id = c7.payment_id ";
						}
					}
					if(in_array('8',$payment_method)){
						$leftjoinmembercredit = " left join member_credit c8 on c8.payment_id = p.id";

						if($wherecash || $wherecheque || $wherecreditcard || $wherebanktransfer || $whereconsumableamount || $leftjoinconsumablefreebies){
							$wheremembercredit =  " or s.payment_id = c8.payment_id ";
						} else {
							$wheremembercredit =  " s.payment_id = c8.payment_id ";
						}
					}

					if(in_array('9',$payment_method)){
						$leftjoindeduction = " left join deductions c9 on c9.payment_id = p.id";

						if($wherecash || $wherecheque || $wherecreditcard || $wherebanktransfer || $whereconsumableamount || $leftjoinconsumablefreebies || $wheremembercredit){
							$wherededuction =  " or s.payment_id = c9.payment_id ";
						} else {
							$wherededuction =  " s.payment_id = c9.payment_id ";
						}
					}

				}

				if ($branch || $terminal){
					if (!$terminal){
						$tempb='';
						foreach($branch as $b){
							$parameters[] = $b;
							$tempb  .='?,';
						}
						$tempb = rtrim($tempb,',');
						$wherebranch = " and t.branch_id in ($tempb)";
					} else {

							$tempt='';
							foreach($terminal as $t){
								$parameters[] = $t;
								$tempt  .='?,';
							}
							$tempt = rtrim($tempt,',');
							$whereterminal = " and s.terminal_id in ($tempt)";
					}
				}
				if ($memid || $stationid){
					if (!$stationid){
						$tempm = '';
						foreach($memid as $m){
							$parameters[] = $m;
							$tempm  .='?,';
						}
						$tempm = rtrim($tempm,',');
						$wheremember = " and s.member_id in ($tempm)";
					} else {
						$tempst = '';
						foreach($stationid as $st){
							$parameters[] = $st;
							$tempst  .='?,';
						}
						$tempst = rtrim($tempst,',');
						$wherestation = " and s.station_id in ($tempst)";
					}
				}


				if($date_type == 1){
					if($dateStart && $dateEnd){
						$dateStart = strtotime($dateStart);
						$dateEnd = strtotime($dateEnd . "1 day -1 sec");
						$wheretimeframe = " and (CASE WHEN wh.id IS NULL THEN s.sold_date >= $dateStart and s.sold_date <= $dateEnd ELSE  wh.is_scheduled >= $dateStart and wh.is_scheduled <= $dateEnd and wh.status = 4  END) ";

					} else {
						$wheretimeframe = " and (CASE WHEN wh.id IS NULL THEN 1 ELSE wh.status = 4 END ) ";
					}
				} else {
					$dateStart = strtotime($dateStart);
					$dateEnd = strtotime($dateEnd . '1 day -1 sec');
					$parameters[] = $dateStart;
					$parameters[] = $dateEnd;
					$wheretimeframe = " and s.sold_date >= ? and s.sold_date <= ?";
				}

				if(!$item_id){
					if ($item_type){
						$tempit = "";
						foreach($item_type as $it){
							$parameters[] = $it;
							$tempit .= "?,";
						}
						$tempit = rtrim($tempit,',');
						$whereitemtype = " and it.item_type in ($tempit)";
					}
					if ($category){
						$tempic = "";
						foreach($category as $c){
							$parameters[] = $c;
							$tempic .= "?,";
						}
						$tempic = rtrim($tempic,',');
						$wherecategory = " and it.category_id in ($tempic)";
					}
					if($custom_string_query){
						$wherestringquery = " and (it.item_code like '%$custom_string_query%' or it.description like '%$custom_string_query%')";
					}
				} else {
					$tempitemid = "";
					foreach($item_id as $c){
						$parameters[] = $c;
						$tempitemid .= "?,";
					}
					$tempitemid = rtrim($tempitemid,',');
					$whereitemid = " and s.item_id in ($tempitemid)";
				}
				if ($cashier){
					$tempcas = "";

					foreach($cashier as $ca){
						$parameters[] = $ca;
						$tempcas .= "?,";
					}
					$tempcas = rtrim($tempcas,',');
					$wherecashier = " and s.cashier_id in ($tempcas)";
				}
				if ($sales_type){
					$tempsalestype = "";

					foreach($sales_type as $ca){
						$parameters[] = $ca;
						$tempsalestype .= "?,";
					}
					$tempsalestype = rtrim($tempsalestype,',');
					$wheresalestype = " and s.sales_type in ($tempsalestype)";
				}
				if($from_od == 1){
					$wherefromOd = " and (s.from_od = 0 or wh.for_pickup = 2)";
				} else if ($from_od == 2){
					$wherefromOd = " and s.from_od = 1 and wh.for_pickup != 2 ";
				} else {
					$wherefromOd="";
				}

				if($from_service == 1){
					$wherefromservice = " and wh.from_service = 0 ";
					$wherefromservice = " and it.item_code not like 'P%'";
				} else if ($from_service == 2){
					$wherefromservice = "and wh.from_service != 0  ";
					$wherefromservice = " and it.item_code like 'P%'";
				}
				if($release_branch_id){
					$release_branch_id = (int) $release_branch_id;
					$where_release= " and wh.branch_id = $release_branch_id ";
				} else {
					$where_release="";
				}

				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}
				$q= "Select s.*,t.name as tname,b.name as bname,it.is_bundle,it.item_code,it.description,pr.price, m.lastname as member_name , p.remarks as p_remarks, wh.is_scheduled
from sales s left join payments p on p.id = s.payment_id
left join (Select id, for_pickup,payment_id,user_id,from_service,branch_id,is_scheduled,status from wh_orders)
 wh on wh.payment_id = s.payment_id
  left join members m on m.id=s.member_id left join terminals t on t.id=s.terminal_id
  left join branches b on b.id=t.branch_id
  left join items it on it.id=s.item_id
   left join prices pr on pr.id=s.price_id $leftjoincash $leftjoincheque $leftjoincreditcard $leftjoinbanktransfer $leftjoinconsumableamount $leftjoinconsumablefreebies $leftjoinmembercredit $leftjoindeduction  where s.company_id=?  $wherefromOd $wherebranch $whereterminal $wheremember $wherestation $wheretimeframe $whereitemtype $wherecategory $whereitemid $wherecashier $wheresalestype $wherefromservice $wheredoctype $wherestringquery $where_release and s.is_active=1 $pwStart $wherecash $wherecheque $wherecreditcard $wherebanktransfer $whereconsumableamount $whereconsumablefreebies $wheremembercredit $wherededuction $pwEnd group by s.id $sort_by  $l";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}
		function getDownloadRecord($cid,$search='',$b=0,$t=0,$m=0,$type=0,$sort_by=0,$tran_type=0,$item_id=0,$date_from=0,$date_to=0){
			$parameters = array();
			if($cid){
				$parameters[] = $cid;

				if($search){
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";

					$likewhere = " and (s.invoice like ? or s.dr like ? or s.sr like ? or s.sr like ? )";
				} else {
					$likewhere='';
				}
				if($b){
					if($b == -1){
						$parameters[] = 0;
						$branchwhere = " and s.terminal_id=? ";
					} else {
						$parameters[] = $b;
						$branchwhere = " and b.id=? ";
					}
				} else {
					$branchwhere = "";
				}
				if($t){
					$parameters[] = $t;
					$terminalWhere  = " and s.terminal_id=? ";
				}else {
					$terminalWhere ="";
				}
				if($m){
					$parameters[] = $m;
					$memberWhere = " and s.member_id=? ";
				} else {
					$memberWhere = "";
				}
				$typeWhere ="";

				/* if($type==0){
					$typeWhere = " and s.status=0";
				} else if($type==1) {
					$typeWhere = " and s.status=1";
				} */



				if(is_array($tran_type)){
					$curt = "";
					foreach($tran_type as $t){
						$curt.= $t.",";
					}
					$curt = rtrim($curt,",");
					$trantypeWhere = " and s.sales_type in ($curt)";
				} else {
					if($tran_type!=0){
						$parameters[] = $tran_type;
						$trantypeWhere = " and s.sales_type=?";
					} else  {
						$trantypeWhere = "";
					}
				}



				if($item_id){
					$parameters[] = "%$item_id%";
					$parameters[] = "%$item_id%";

					$whereItem = " and ( i.item_code = ? or i.description = ? )";
				} else {
					$whereItem='';
				}

				if($sort_by){
					$sort_by = trim($sort_by);
					$arr_valid = ['order by m.lastname desc',
						'order by IF (IFNULL(s.invoice,0) = 0, 1, 0), s.invoice * 1 desc',
						'order by IF (IFNULL(s.dr,0) = 0, 1, 0), s.dr * 1 desc',
						'order by IF (IFNULL(s.sr,0) = 0, 1, 0), s.sr * 1 desc',
						'order by IF (IFNULL(s.ir,0) = 0, 1, 0), s.ir * 1 desc',
						'order by IF (IFNULL(s.sv,0) = 0, 1, 0), s.sv * 1 desc',
						'order by i.item_code desc','order by pr.price desc',
						'order by s.qtys desc',
						'order by s.discount desc',
						'order by ((s.qtys * price)-s.discount) desc',
						'order by s.sold_date desc'];
					if(!in_array($sort_by,$arr_valid)){
						$sort_by = "";
					}
				} else {
					$sort_by = "order by s.payment_id desc, s.invoice desc, s.dr desc";
				}

				$whereDate = "";
				if($date_from && $date_to){
					$dt1= strtotime($date_from);
					$dt2= strtotime($date_to . "1 day -1 min");
					$whereDate = " and s.sold_date >= $dt1 and s.sold_date <= $dt2 ";
				}

				$q= "Select s.*,i.item_code,i.description,pr.price,st.name as sales_type_name, m.lastname as member_name
					from sales s left join terminals t  on t.id = s.terminal_id
					left join items i on i.id = s.item_id
					left join members m on m.id = s.member_id
					left join stations stat on stat.id=s.station_id
					left join prices pr on pr.id=s.price_id
					left join branches b on b.id=t.branch_id
					left join salestypes st on st.id = s.sales_type
					where s.company_id=? and s.is_active=1
					$likewhere $typeWhere  $branchwhere $terminalWhere $memberWhere $trantypeWhere $whereItem $whereDate $sort_by limit 2000";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}

		public function getInvoiceDr($payment_id,$single=false){
			if($payment_id){
				$parameters = array();
				$parameters[] = $payment_id;


				if($single){
					$row = "first";
				} else {
					$row = 'results';
				}
				$q= "select dr, invoice,ir from sales where payment_id = ?";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->$row();
				}
			}
		}
		public function countRecordR2v2($cid=0,$payment_method=0,$branch=0,$terminal=0,$item_type=0,$category=0,$memid=0,$stationid=0,$dateStart=0,$dateEnd=0,$cashier=0,$item_id=0,$sales_type=0,$sort_by='',$from_od=0,$from_service=0,$doc_type=0,$release_branch_id=0,$date_type=0,$include_cancel=0){

			if($cid) {
				$parameters = array();
				$parameters[] = $cid;
				$wheretimeframe ='';
				$wheremember='';
				$wherestation='';
				$leftjoincash ='';
				$leftjoincheque ="";
				$leftjoincreditcard ="";
				$leftjoinbanktransfer ="";
				$leftjoinconsumableamount="";
				$whereconsumableamount="";
				$leftjoinconsumablefreebies="";
				$leftjoinmembercredit = '';
				$wheremembercredit = '';
				$whereconsumablefreebies="";
				$wherecash = "";
				$wherecheque = "";
				$wherecreditcard="";
				$wherebanktransfer = "";
				$pwStart = "";
				$pwEnd ="";
				$wherebranch='';
				$whereterminal='';
				$wheremember = '';
				$wherestation = '';
				$wheretimeframe ='';
				$wherecategory ='';
				$whereitemtype='';
				$wherecashier='';
				$whereitemid ='';
				$wheresalestype='';
				$leftjoindeduction = '';
				$wherededuction ='';
				$wherefromservice="";
				$wheredoctype='';
				if($doc_type == 1){
					$wheredoctype =" and s.invoice != '' and s.invoice != '0' ";
				} else if ($doc_type == 2){
					$wheredoctype =" and s.dr != '' and s.dr != '0' ";
				}else if ($doc_type == 3){
					$wheredoctype =" and s.ir != '' and s.ir != '0' ";
				}
				if ($payment_method && !in_array('5',$payment_method)){
					$pwStart = " and (";
					$pwEnd = " )";
					if(in_array('1',$payment_method)){
						$leftjoincash = " left join cash c1 on c1.payment_id = p.id";
						$wherecash = " s.payment_id = c1.payment_id ";
					}
					if(in_array('2',$payment_method)){
						$leftjoincheque = " left join cheque c2 on c2.payment_id = p.id";
						if ($wherecash){
							$wherecheque =  " or s.payment_id = c2.payment_id ";
						} else {
							$wherecheque =  " s.payment_id = c2.payment_id ";
						}

					}
					if(in_array('3',$payment_method)){
						$leftjoincreditcard = " left join credit_card c3 on c3.payment_id = p.id";

						if($wherecash || $wherecheque){
							$wherecreditcard =  " or s.payment_id = c3.payment_id ";
						} else {
							$wherecreditcard =  " s.payment_id = c3.payment_id ";
						}
					}
					if(in_array('4',$payment_method)){
						$leftjoinbanktransfer = " left join bank_transfer c4 on c4.payment_id = p.id";

						if($wherecash || $wherecheque || $wherecreditcard){
							$wherebanktransfer =  " or s.payment_id = c4.payment_id ";
						} else {
							$wherebanktransfer =  " s.payment_id = c4.payment_id ";
						}
					}

					if(in_array('6',$payment_method)){
						$leftjoinconsumableamount = " left join payment_consumable c6 on c6.payment_id = p.id";

						if($wherecash || $wherecheque || $wherecreditcard || $wherebanktransfer){
							$whereconsumableamount =  " or s.payment_id = c6.payment_id ";
						} else {
							$whereconsumableamount =  " s.payment_id = c6.payment_id ";
						}
					}
					if(in_array('7',$payment_method)){
						$leftjoinconsumablefreebies = " left join payment_consumable_freebies c7 on c7.payment_id = p.id";

						if($wherecash || $wherecheque || $wherecreditcard || $wherebanktransfer || $whereconsumableamount){
							$whereconsumablefreebies =  " or s.payment_id = c7.payment_id ";
						} else {
							$whereconsumablefreebies =  " s.payment_id = c7.payment_id ";
						}
					}
					if(in_array('8',$payment_method)){
						$leftjoinmembercredit = " left join member_credit c8 on c8.payment_id = p.id";

						if($wherecash || $wherecheque || $wherecreditcard || $wherebanktransfer || $whereconsumableamount || $leftjoinconsumablefreebies){
							$wheremembercredit =  " or s.payment_id = c8.payment_id ";
						} else {
							$wheremembercredit =  " s.payment_id = c8.payment_id ";
						}
					}
					if(in_array('9',$payment_method)){
						$leftjoindeduction = " left join deductions c9 on c9.payment_id = p.id";

						if($wherecash || $wherecheque || $wherecreditcard || $wherebanktransfer || $whereconsumableamount || $leftjoinconsumablefreebies || $wheremembercredit){
							$wherededuction =  " or s.payment_id = c9.payment_id ";
						} else {
							$wherededuction =  " s.payment_id = c9.payment_id ";
						}
					}
				}
				if($sort_by){
					$sort_by = trim($sort_by);
					$arr_valid = ['order by m.lastname desc',
						'order by IF (IFNULL(s.invoice,0) = 0, 1, 0), s.invoice * 1 desc',
						'order by IF (IFNULL(s.dr,0) = 0, 1, 0), s.dr * 1 desc',
						'order by IF (IFNULL(s.sr,0) = 0, 1, 0), s.sr * 1 desc',
						'order by IF (IFNULL(s.ir,0) = 0, 1, 0), s.ir * 1 desc',
						'order by IF (IFNULL(s.sv,0) = 0, 1, 0), s.sv * 1 desc',
						'order by i.item_code desc','order by pr.price desc',
						'order by s.qtys desc',
						'order by s.discount desc',
						'order by ((s.qtys * price)-s.discount) desc',
						'order by s.sold_date desc'];
					if(!in_array($sort_by,$arr_valid)){
						$sort_by = "";
					}
				} else {
					$sort_by = "order by s.payment_id desc, s.invoice desc, s.dr desc";
				}
				if ($branch || $terminal){
					if (!$terminal){
						$tempb='';
						foreach($branch as $b){

							$tempb  .="$b,";
						}
						$tempb = rtrim($tempb,',');
						$wherebranch = " and t.branch_id in ($tempb)";
					} else {

						$tempt='';
						foreach($terminal as $t){

							$tempt  .=$t.',';
						}
						$tempt = rtrim($tempt,',');
						$whereterminal = " and s.terminal_id in ($tempt)";
					}
				}
				if($dateStart && $dateEnd){
					if($date_type == 1){
						if($dateStart && $dateEnd){
							$dateStart = strtotime($dateStart);
							$dateEnd = strtotime($dateEnd . "1 day -1 sec");

							$wheretimeframe = " and (CASE WHEN wh.id IS NULL THEN  s.sold_date >= $dateStart and s.sold_date <= $dateEnd ELSE  wh.is_scheduled >= $dateStart and wh.is_scheduled <= $dateEnd and wh.status = 4  END) ";

						} else {
							$wheretimeframe = " and (CASE WHEN wh.id IS NULL THEN 1 ELSE wh.status = 4 END ) ";

						}
					} else {
						$dateStart = strtotime($dateStart);
						$dateEnd = strtotime($dateEnd . '1 day -1 sec');
						$parameters[] = $dateStart;
						$parameters[] = $dateEnd;
						$wheretimeframe = " and s.sold_date >= ? and s.sold_date <= ?";
					}

				}
				if ($memid || $stationid){
					if (!$stationid){
						$tempm = '';
						foreach($memid as $m){
							$parameters[] = $m;
							$tempm  .='?,';
						}
						$tempm = rtrim($tempm,',');
						$wheremember = " and s.member_id in ($tempm)";
					} else {
						$tempst = '';
						foreach($stationid as $st){
							$parameters[] = $st;
							$tempst  .='?,';
						}
						$tempst = rtrim($tempst,',');
						$wherestation = " and s.station_id in ($tempst)";
					}
				}
				if($from_od == 1){
					$wherefromOd = " and (s.from_od = 0 or wh.for_pickup = 2)";
				} else if ($from_od == 2){
					$wherefromOd = " and s.from_od = 1 and wh.for_pickup != 2 ";
				} else {
					$wherefromOd="";
				}
				$wheresalestype ="";
				if ($sales_type){
					$tempsalestype = "";

					foreach($sales_type as $ca){
						$parameters[] = $ca;
						$tempsalestype .= "?,";
					}
					$tempsalestype = rtrim($tempsalestype,',');
					$wheresalestype = " and s.sales_type in ($tempsalestype)";
				}


				if($from_service == 1){
					$wherefromservice = " and wh.from_service = 0 ";
					$wherefromservice = " and it.item_code not like 'P%'";
				} else if ($from_service == 2){
					$wherefromservice = "and wh.from_service != 0  ";
					$wherefromservice = " and it.item_code  like 'P%'";
				}
				if($release_branch_id){
					$release_branch_id = (int) $release_branch_id;
					$where_release = " and wh.branch_id = $release_branch_id ";
				} else {
					$where_release = "";
				}

				$whereStatus =" and s.status=0 ";
				if($include_cancel){
					$whereStatus = "";
				}

				$q= "Select
 						count(distinct(s.payment_id)) as cnt
 						from sales s   left join payments p on p.id = s.payment_id
 						left join items it on it.id = s.item_id
						left join (Select id, for_pickup,payment_id,user_id,from_service,branch_id,is_scheduled,status from wh_orders) wh on wh.payment_id = s.payment_id
 						$leftjoincash $leftjoincheque $leftjoincreditcard $leftjoinbanktransfer $leftjoinconsumableamount $leftjoinconsumablefreebies $leftjoinmembercredit $leftjoindeduction
 						where s.company_id=? $whereStatus $where_release $wherefromservice $wheredoctype $wheretimeframe $wherebranch $whereterminal $wheremember $wherestation $wherefromOd $wheresalestype $pwStart $wherecash $wherecheque $wherecreditcard $wherebanktransfer $whereconsumableamount $whereconsumablefreebies $wheremembercredit  $wherededuction  $pwEnd";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function getSalesR2v2($cid=0,$start,$limit,$payment_method=0,$branch=0,$terminal=0,$item_type=0,$category=0,$memid=0,$stationid=0,$dateStart=0,$dateEnd=0,$cashier=0,$item_id=0,$sales_type=0,$sort_by='',$from_od=0,$from_service=0,$doc_type=0,$release_branch_id=0,$date_type=0,$include_cancel=0){
		if($cid){
			$parameters = array();
			$parameters[] = $cid;
			if($limit){
				$l = " LIMIT $start,$limit";
			} else {
				$l='';
			}
			$leftjoincash ='';
			$leftjoincheque ="";
			$leftjoincreditcard ="";
			$leftjoinbanktransfer ="";
			$leftjoinconsumableamount="";
			$whereconsumableamount="";
			$leftjoinconsumablefreebies="";
			$whereconsumablefreebies="";
			$leftjoinmembercredit = '';
			$wheremembercredit = '';
			$wherecash = "";
			$wherecheque = "";
			$wherecreditcard="";
			$wherebanktransfer = "";
			$pwStart = "";
			$pwEnd ="";
			$wherebranch='';
			$whereterminal='';
			$wheremember = '';
			$wherestation = '';
			$wheretimeframe='';
			$wherecategory ='';
			$whereitemtype='';
			$wherecashier = '';
			$whereitemid='';
			$wheresalestype='';
			$leftjoindeduction='';
			$wherededuction='';
			$wherefromservice = "";
			$wheredoctype='';
			if($doc_type == 1){
				$wheredoctype =" and s.invoice != '' and s.invoice != '0' ";
			} else if ($doc_type == 2){
				$wheredoctype =" and s.dr != '' and s.dr != '0' ";
			}else if ($doc_type == 3){
				$wheredoctype =" and s.ir != '' and s.ir != '0' ";
			}
			if($sort_by){
				$sort_by = trim($sort_by);
				$arr_valid = ['order by m.lastname desc',
					'order by IF (IFNULL(s.invoice,0) = 0, 1, 0), s.invoice * 1 desc',
					'order by IF (IFNULL(s.dr,0) = 0, 1, 0), s.dr * 1 desc',
					'order by IF (IFNULL(s.sr,0) = 0, 1, 0), s.sr * 1 desc',
					'order by IF (IFNULL(s.ir,0) = 0, 1, 0), s.ir * 1 desc',
					'order by IF (IFNULL(s.sv,0) = 0, 1, 0), s.sv * 1 desc',
					'order by i.item_code desc','order by pr.price desc',
					'order by s.qtys desc',
					'order by s.discount desc',
					'order by ((s.qtys * price)-s.discount) desc',
					'order by s.sold_date desc'];
				if(!in_array($sort_by,$arr_valid)){
					$sort_by = "";
				}
			} else {
				$sort_by = "order by s.payment_id desc, s.invoice desc, s.dr desc";
			}
			if ($payment_method && !in_array('5',$payment_method)){
				$pwStart = " and (";
				$pwEnd = " )";
				if(in_array('1',$payment_method)){
					$leftjoincash = " left join cash c1 on c1.payment_id = p.id";
					$wherecash = " s.payment_id = c1.payment_id ";
				}
				if(in_array('2',$payment_method)){
					$leftjoincheque = " left join cheque c2 on c2.payment_id = p.id";
					if ($wherecash){
						$wherecheque =  " or s.payment_id = c2.payment_id ";
					} else {
						$wherecheque =  " s.payment_id = c2.payment_id ";
					}

				}
				if(in_array('3',$payment_method)){
					$leftjoincreditcard = " left join credit_card c3 on c3.payment_id = p.id";

					if($wherecash || $wherecheque){
						$wherecreditcard =  " or s.payment_id = c3.payment_id ";
					} else {
						$wherecreditcard =  " s.payment_id = c3.payment_id ";
					}
				}
				if(in_array('4',$payment_method)){
					$leftjoinbanktransfer = " left join bank_transfer c4 on c4.payment_id = p.id";

					if($wherecash || $wherecheque || $wherecreditcard){
						$wherebanktransfer =  " or s.payment_id = c4.payment_id ";
					} else {
						$wherebanktransfer =  " s.payment_id = c4.payment_id ";
					}
				}

				if(in_array('6',$payment_method)){
					$leftjoinconsumableamount = " left join payment_consumable c6 on c6.payment_id = p.id";

					if($wherecash || $wherecheque || $wherecreditcard || $wherebanktransfer){
						$whereconsumableamount =  " or s.payment_id = c6.payment_id ";
					} else {
						$whereconsumableamount =  " s.payment_id = c6.payment_id ";
					}
				}
				if(in_array('7',$payment_method)){

					$leftjoinconsumablefreebies = " left join payment_consumable_freebies c7 on c7.payment_id = p.id";

					if($wherecash || $wherecheque || $wherecreditcard || $wherebanktransfer || $whereconsumableamount){
						$whereconsumablefreebies =  " or s.payment_id = c7.payment_id ";
					} else {
						$whereconsumablefreebies =  " s.payment_id = c7.payment_id ";
					}
				}
				if(in_array('8',$payment_method)){
					$leftjoinmembercredit = " left join member_credit c8 on c8.payment_id = p.id";

					if($wherecash || $wherecheque || $wherecreditcard || $wherebanktransfer || $whereconsumableamount || $leftjoinconsumablefreebies){
						$wheremembercredit =  " or s.payment_id = c8.payment_id ";
					} else {
						$wheremembercredit =  " s.payment_id = c8.payment_id ";
					}
				}
				if(in_array('9',$payment_method)){
					$leftjoindeduction = " left join deductions c9 on c9.payment_id = p.id";

					if($wherecash || $wherecheque || $wherecreditcard || $wherebanktransfer || $whereconsumableamount || $leftjoinconsumablefreebies || $wheremembercredit){
						$wherededuction =  " or s.payment_id = c9.payment_id ";
					} else {
						$wherededuction =  " s.payment_id = c9.payment_id ";
					}
				}
			}
			if ($branch || $terminal){
				if (!$terminal){
					$tempb='';
					foreach($branch as $b){

						$tempb  .="$b,";
					}
					$tempb = rtrim($tempb,',');
					$wherebranch = " and t.branch_id in ($tempb)";
				} else {

					$tempt='';
					foreach($terminal as $t){

						$tempt  .=$t.',';
					}
					$tempt = rtrim($tempt,',');
					$whereterminal = " and s.terminal_id in ($tempt)";
				}
			}

			if($dateStart && $dateEnd){
				if($date_type == 1){
					if($dateStart && $dateEnd){
						$dateStart = strtotime($dateStart);
						$dateEnd = strtotime($dateEnd . "1 day -1 sec");

						$wheretimeframe = " and (CASE WHEN wh.id IS NULL THEN  s.sold_date >= $dateStart and s.sold_date <= $dateEnd ELSE  wh.is_scheduled >= $dateStart and wh.is_scheduled <= $dateEnd and wh.status = 4  END) ";

					} else {
						$wheretimeframe = " and (CASE WHEN wh.id IS NULL THEN 1 ELSE wh.status = 4 END ) ";

					}
				} else {
					$dateStart = strtotime($dateStart);
					$dateEnd = strtotime($dateEnd . '1 day -1 sec');
					$parameters[] = $dateStart;
					$parameters[] = $dateEnd;
					$wheretimeframe = " and s.sold_date >= ? and s.sold_date <= ?";
				}

			}

			if ($memid || $stationid){
				if (!$stationid){
					$tempm = '';
					foreach($memid as $m){
						$parameters[] = $m;
						$tempm  .='?,';
					}
					$tempm = rtrim($tempm,',');
					$wheremember = " and s.member_id in ($tempm)";
				} else {
					$tempst = '';
					foreach($stationid as $st){
						$parameters[] = $st;
						$tempst  .='?,';
					}
					$tempst = rtrim($tempst,',');
					$wherestation = " and s.station_id in ($tempst)";
				}
			}
			if($from_od == 1){
				$wherefromOd = " and (s.from_od = 0 or wh.for_pickup = 2)";
			} else if ($from_od == 2){
				$wherefromOd = " and s.from_od = 1 and wh.for_pickup != 2 ";
			} else {
				$wherefromOd="";
			}

			if($from_service == 1){
				$wherefromservice = " and wh.from_service = 0 ";
				$wherefromservice = " and it.item_code not like 'P%'";
			} else if ($from_service == 2){
				$wherefromservice = "and wh.from_service != 0  ";
				$wherefromservice = " and it.item_code like 'P%'";
			}

			$wheresalestype ="";
			if ($sales_type){
				$tempsalestype = "";

				foreach($sales_type as $ca){
					$parameters[] = $ca;
					$tempsalestype .= "?,";
				}
				$tempsalestype = rtrim($tempsalestype,',');
				$wheresalestype = " and s.sales_type in ($tempsalestype)";
			}

			if($release_branch_id){
				$release_branch_id = (int) $release_branch_id;
				$where_release = " and wh.branch_id = $release_branch_id ";
			} else {
				$where_release = "";
			}

			$whereStatus =" and s.status=0 ";
			if($include_cancel){
				$whereStatus = "";
			}

		 $q= "Select
				sum(((s.qtys * pr.price) + s.adjustment + s.member_adjustment)- (s.discount + s.store_discount)) as totalamount,
				 s.payment_id,s.invoice,s.dr,s.ir,s.pref_inv,s.pref_dr,s.pref_ir,s.suf_inv,s.suf_dr,s.suf_ir,s.sold_date,s.sv,s.pref_sv,s.suf_sv,
				 t.name as tname,b.name as bname, m.lastname as mln, m.firstname as mfn,s.cashier_id,od.user_id as reserved_by ,
				  st.name as sales_type_name, wh.is_scheduled, s.status

				  from sales  s
				   left join payments p on p.id = s.payment_id
				   left join (Select id, for_pickup,payment_id,user_id,from_service,branch_id,is_scheduled,status from wh_orders) wh on wh.payment_id = s.payment_id
				    left join orders od on od.payment_id = s.payment_id
				     left join terminals t on t.id=s.terminal_id
				     left join branches b on b.id=t.branch_id
				     left join items it on it.id=s.item_id
				     left join items i on i.id = s.item_id
				     left join prices pr on pr.id=s.price_id
				     left join members m on m.id = s.member_id
				     left join salestypes st on st.id=s.sales_type
				     $leftjoincash $leftjoincheque $leftjoincreditcard $leftjoinbanktransfer $leftjoinconsumableamount $leftjoinconsumablefreebies $leftjoinmembercredit $leftjoindeduction
				     where s.company_id=? and s.is_active=1 $whereStatus $where_release $wherefromservice $wheredoctype $wheretimeframe $wherebranch $whereterminal $wheremember $wherestation $wherefromOd $wheresalestype $pwStart $wherecash $wherecheque $wherecreditcard $wherebanktransfer $whereconsumableamount $whereconsumablefreebies $wheremembercredit $wherededuction $pwEnd  group by s.payment_id $sort_by  $l";

			$data = $this->_db->query($q, $parameters);

			if($data->count()) {
				// return the data if exists
				return $data->results();
			}

		}
	}
		public function getSalesR3v3($cid=0,$dateStart=0,$dateEnd=0,$st=[]){
			if($cid){
				$parameters = array();
				$parameters[] = $cid;

				$where_sales_type = "";

				if($dateStart && $dateEnd){
					$dateStart = strtotime($dateStart);
					$dateEnd = strtotime($dateEnd . '1 day -1 sec');
					$parameters[] = $dateStart;
					$parameters[] = $dateEnd;
					$wheretimeframe = " and s.sold_date >= ? and s.sold_date <= ?";
				}

				if($st){
					$lsid= "";
					foreach($st as $stid){
						$stid = (int) $stid;
						$lsid .= $stid . ",";
					}
					$lsid = rtrim($lsid,",");


					$where_sales_type = " and s.sales_type in ($lsid)";
				}

				$left_join_cash = " left join (Select sum(amount) as cash_amount, payment_id from cash group by payment_id) cash on cash.payment_id = s.payment_id";
				$left_join_credit_card = " left join (Select sum(amount) as credit_card_amount, payment_id from credit_card group by payment_id) credit_card on credit_card.payment_id = s.payment_id";
				$left_join_cheque = " left join (Select sum(amount) as cheque_amount, payment_id from cheque group by payment_id) cheque on cheque.payment_id = s.payment_id";
				$left_join_bt = " left join (Select sum(amount) as bt_amount, payment_id from bank_transfer group by payment_id) bank_transfer on bank_transfer.payment_id = s.payment_id";
				$left_join_deduction = " left join (Select sum(amount) as deduction_amount, payment_id from deductions group by payment_id) deductions on deductions.payment_id = s.payment_id";
				$left_join_member_credit= " left join (Select sum(amount - amount_paid) as member_amount, payment_id from member_credit group by payment_id) member_credit on member_credit.payment_id = s.payment_id";

				 $q= "Select
					sum(((s.qtys * pr.price) + s.adjustment + s.member_adjustment)- (s.discount + s.store_discount)) as totalamount,
					 s.payment_id,s.invoice,s.dr,s.ir,s.pref_inv,s.pref_dr,s.pref_ir,s.suf_inv,s.suf_dr,s.suf_ir,s.sold_date,s.sv,s.pref_sv,s.suf_sv,
					 t.name as tname,b.name as bname, m.lastname as mln, m.firstname as mfn,s.cashier_id,od.user_id as reserved_by ,
				 	 st.name as sales_type_name,
				 	 cash.cash_amount,  credit_card.credit_card_amount, cheque.cheque_amount, bank_transfer.bt_amount, deductions.deduction_amount, member_credit.member_amount
				  	from sales  s
				  	 left join payments p on p.id = s.payment_id
				  	 $left_join_cash
				  	 $left_join_credit_card
				  	 $left_join_cheque
				  	 $left_join_bt
				  	 $left_join_deduction
				  	 $left_join_member_credit
				   	left join (Select id, for_pickup,payment_id,user_id,from_service from wh_orders) wh on wh.payment_id = s.payment_id
				   	 left join orders od on od.payment_id = s.payment_id
				     left join terminals t on t.id=s.terminal_id
				     left join branches b on b.id=t.branch_id
				     left join items it on it.id=s.item_id
				     left join items i on i.id = s.item_id
				     left join prices pr on pr.id=s.price_id
				     left join members m on m.id = s.member_id
				     left join salestypes st on st.id=s.sales_type

				     where s.company_id=? and s.is_active=1 and s.status=0  $wheretimeframe $where_sales_type group by s.payment_id ";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function countRecordR2v3($cid=0,$search='',$dt_from=0,$dt_to=0,$branch_id=0){

			if($cid) {
				$parameters = array();
				$parameters[] = $cid;
				$whereDate ='';
				$whereSearch ='';
				$whereBranch='';

				if($search){
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$whereSearch = "and (s.invoice like ? or s.dr like ? or s.ir like ? )";
				}
				if($dt_from && $dt_to){

					$dateStart = strtotime($dt_from);
					$dateEnd = strtotime($dt_to . '1 day -1 sec');

					$whereDate = " and s.sold_date >=$dateStart and s.sold_date <= $dateEnd";

				}
				if($branch_id){
					$parameters[] = $branch_id;
					$whereBranch = " and t.branch_id = ? ";
				}
				$q= "Select count(distinct(s.payment_id)) as cnt from sales s left join terminals t on t.id = s.terminal_id where s.company_id=? and s.status=0 $whereSearch $whereDate $whereBranch";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function getSalesR2v3($cid=0,$start,$limit,$search,$dt_from=0,$dt_to=0,$branch_id=0){
			if($cid){
				$parameters = array();
				$parameters[] = $cid;
				$whereDate ='';
				$whereSearch ='';
				$whereBranch='';
				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}
				if($search){
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$whereSearch = "and (s.invoice like ? or s.dr like ? or s.ir like ? )";
				}
				if($dt_from && $dt_to){

					$dateStart = strtotime($dt_from);
					$dateEnd = strtotime($dt_to . '1 day -1 sec');
					$whereDate = " and s.sold_date >=$dateStart and s.sold_date <= $dateEnd";

				}
				if($branch_id){
					$parameters[] = $branch_id;
					$whereBranch = " and t.branch_id = ? ";
				}

				$q= "Select sum(((s.qtys * pr.price) + s.adjustment + s.member_adjustment)- (s.discount - s.store_discount)) as totalamount, s.payment_id,s.invoice,s.dr,s.sold_date,t.name as tname,b.name as bname, m.lastname as mln, m.firstname as mfn,s.cashier_id,od.user_id as reserved_by from sales s left join payments p on p.id = s.payment_id left join orders od on od.payment_id = s.payment_id left join terminals t on t.id=s.terminal_id left join branches b on b.id=t.branch_id left join items it on it.id=s.item_id  left join items i on i.id = s.item_id left join prices pr on pr.id=s.price_id left join members m on m.id = s.member_id where s.company_id=? and s.is_active=1 and s.status=0  $whereSearch $whereDate $whereBranch group by s.payment_id order by s.payment_id desc  $l";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function getPaymentId($invoice){
			if($invoice){
				$parameters = array();
				$parameters[] = $invoice;

				$q= "select payment_id from sales where invoice = ? limit 1";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function getsinglesale($p){
			if($p){
				$parameters = array();
				$parameters[] = $p;

				$q= "select s.*, st.name as sales_type_name
						from sales s
						left join salestypes st on st.id = s.sales_type
						where s.payment_id = ? limit 1";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function getItemBaseOnInvoice($invoice,$company_id,$terminal_id){
			if($invoice && $company_id && $terminal_id){
				$parameters = array();
				$parameters[] = $invoice;
				$parameters[] = $company_id;
				$parameters[] = $terminal_id;

				 $q= "select s.*,i.item_code,i.description,p.price from sales s left join items i on i.id=s.item_id left join prices p on p.id=s.price_id where s.invoice = ? and s.company_id = ? and s.terminal_id=? ";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function getInconsistentData($company_id=0,$branch_id=0,$from=0,$to=0){
			if( $company_id ){
				$parameters = array();
				$parameters[] = $company_id;
				$whereBranch = "";
				$whereDate = "";
				if($branch_id){
					$parameters[] = $branch_id;
					$whereBranch = " and b.id = ? ";
				}
				if($from && $to){

					$whereDate = " and s.sold_date >=  $from and s.sold_date <= $to ";
				}

				$q= "Select s.payment_id, s.invoice,s.dr,s.ir,s.sold_date,s.sr,s.member_id,
					round(sum(((s.qtys * p.price) + s.adjustment + s.member_adjustment) - (s.discount + s.store_discount)),2) as ttotal,
					round(coalesce(c.cashamount,0),2) as cashamount,
					round(coalesce(ch.chequeamount,0),2) as chequeamount,
					round(coalesce(bt.btamount,0),2) as btamount,
					round(coalesce(cc.ccamount,0),2) as ccamount,
					round(coalesce(mc.mcamount,0),2) as mcamount,
					round(coalesce(pc.pcamount,0),2) as pcamount,
					round(coalesce(dd.deduction,0),2) as deduction,
					round(coalesce(pcf.pcfamount,0),2) as pcfamount
					from sales s
					left join prices p on p.id = s.price_id
					left join (Select sum(amount) as cashamount,payment_id from cash group by payment_id) c on c.payment_id = s.payment_id
					left join (Select sum(amount) as chequeamount,payment_id from cheque where status in (1,2) group by payment_id) ch on ch.payment_id = s.payment_id
					left join (Select sum(amount) as btamount,payment_id from bank_transfer group by payment_id) bt on bt.payment_id = s.payment_id
					left join (Select sum(amount) as ccamount,payment_id from credit_card group by payment_id) cc on cc.payment_id = s.payment_id
					left join (Select sum(amount - amount_paid) as mcamount,payment_id from member_credit group by payment_id) mc on mc.payment_id = s.payment_id
					left join (Select sum(amount ) as deduction,payment_id from deductions group by payment_id) dd on dd.payment_id = s.payment_id
					left join (Select sum(amount) as pcamount,payment_id from payment_consumable group by payment_id) pc on pc.payment_id = s.payment_id
					left join (Select sum(amount) as pcfamount,payment_id from payment_consumable_freebies group by payment_id) pcf on pcf.payment_id = s.payment_id
					left join terminals t on t.id=s.terminal_id
					left join branches b on b.id = t.branch_id
					where s.company_id = ? and s.status = 0 $whereBranch $whereDate
					group by s.payment_id
					having  (round(sum(((s.qtys * p.price) + s.adjustment + s.member_adjustment) - (s.discount + s.store_discount)),2) != (round(coalesce(cashamount,0),2) + round(coalesce(chequeamount,0),2) +  round(coalesce(btamount,0),2)  +  round(coalesce(deduction,0),2)  + round(coalesce(ccamount,0),2) + round(coalesce(mcamount,0),2) + round(coalesce(pcamount,0),2)+round(coalesce(pcfamount,0),2)))";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function getSalesInvDrIr($cid = 0,$invoice = 0 , $dr = 0, $ir =0){
			if($invoice ||  $dr ||  $ir){
				$parameters = array();
				$parameters[] = $cid;
				$whereInvoice='';
				$whereDr='';
				$whereIr='';
				if($invoice){
					$parameters[] = $invoice;
					$whereInvoice = " and s.invoice = ? ";
				}
				if($dr){
					$parameters[] = $dr;
					$whereDr = " and s.dr = ? ";
				}
				if($ir){
					$parameters[] = $ir;
					$whereIr = " and s.ir = ? ";
				}
				$q= "select s.*,i.item_code,i.barcode,i.description,p.price from sales s left join items i on i.id=s.item_id left join prices p on p.id=s.price_id where 1=1 and s.company_id = ? $whereInvoice $whereDr $whereIr ";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function getDSSMinMax($cid,$dt,$terminal_id,$type){ // daily series summary
			if($cid && $dt){
				$parameters = array();
				$parameters[] = $cid;
				$parameters[] = $terminal_id;
				$wheretimeframe = "";
				if($type == 1){ // invoice
						$col = "invoice";
				} else if ($type == 2){
					$col = "dr";
				}else if ($type == 3){
					$col = "ir";
				}
				if($dt){
					$dt = strtotime($dt);
					$dt_end = strtotime(date('m/d/Y',$dt) . '1 day -1 sec');

					$wheretimeframe = " and s.sold_date >= $dt and s.sold_date <= $dt_end";
				}

					 $q= "Select IFNULL(MAX(s.{$col} * 1),0) as max_cnt,
							IFNULL(MIN(s.{$col} * 1),0) as min_cnt
						  ,s.sold_date from (Select * from sales group by payment_id) s where  s.company_id= ? and s.terminal_id=? and s.{$col} != '0' $wheretimeframe";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function getStatsDSS($cid,$dt,$terminal_id,$type,$status){ // daily series summary
		if($cid && $dt){
			$parameters = array();
			$parameters[] = $cid;
			$wheretimeframe = "";
			if($dt){
				$dt = strtotime($dt);
				$dt_end = strtotime(date('m/d/Y',$dt) . '1 day -1 sec');

				if($type == 1){
					$extra = " and s.invoice != '0' ";
				} else if($type == 2){
					$extra = " and s.dr != '0' ";
				}else if($type == 3){
					$extra = " and s.ir != '0' ";
				}
				$terminal_id = (int) $terminal_id;
				$status = (int) $status;
				$wheretimeframe = " and s.sold_date >= $dt and s.sold_date <= $dt_end and s.terminal_id=$terminal_id $extra and s.status=$status ";
			}

			$q= "Select IFNULL(count(s.status),0) as num, status from (Select * from sales group by payment_id) s left join terminals t on t.id = s.terminal_id where  s.company_id= ? $wheretimeframe ";
			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				// return the data if exists
				return $data->first();
			}
		}
	}
		public function getDailySummary($cid=0,$month=0,$year=0,$branch_id=0){
			if($cid && $month && $year ){
				$parameters = [];
				$parameters[] = $cid;
				$parameters[] = $month;
				$parameters[] = $year;
				$whereBranch = '';
				if($branch_id){
					$whereBranch = " and t.branch_id = ? ";
					$parameters[]= $branch_id;
				}

				//jump1
				$left_join_cash = " left join (Select sum(amount) as cash_amount,payment_id from cash  group by payment_id) cash1 on cash1.payment_id = s.payment_id";
				$left_join_credit_card = " left join (Select sum(amount) as credit_card_amount, payment_id from credit_card  group by payment_id) credit_card1 on credit_card1.payment_id = s.payment_id";
				$left_join_cheque = " left join (Select sum(amount) as cheque_amount, payment_id from cheque  group by payment_id) cheque1 on cheque1.payment_id = s.payment_id";
				$left_join_bt = " left join (Select sum(amount) as bt_amount, payment_id from bank_transfer  group by payment_id) bank_transfer1 on bank_transfer1.payment_id = s.payment_id";
				$left_join_deduction = " left join (Select sum(amount) as deduction_amount, payment_id from deductions  group by payment_id) deductions1 on deductions1.payment_id = s.payment_id";
				$left_join_member_credit= " left join (Select sum(amount - amount_paid) as member_amount, payment_id from member_credit  group by payment_id) member_credit1 on member_credit1.payment_id = s.payment_id";
/*
                          cash.cash_amount,
						credit_card.credit_card_amount ,
						cheque.cheque_amount ,
						bank_transfer.bt_amount,
						deductions.deduction_amount,
						member_credit.member_amount

---
	sum(cash.cash_amount) as cash_amount,
						sum(credit_card.credit_card_amount) as credit_card_amount,
						sum(cheque.cheque_amount) as cheque_amount,
						sum(bank_transfer.bt_amount) as bt_amount,
						sum(deductions.deduction_amount) as deduction_amount,
						sum(member_credit.member_amount) as member_amount
-----------------------
	sum(cash1.cash_amount) as cash_amount,
						sum(credit_card1.credit_card_amount) as credit_card_amount,
						sum(cheque1.cheque_amount) as cheque_amount,
						sum(bank_transfer1.bt_amount) as bt_amount,
						sum(deductions1.deduction_amount) as deduction_amount,
						sum(member_credit1.member_amount) as member_amount
 */
				$q= "Select   DAY(FROM_UNIXTIME(s.sold_date)) as d,
						sum(((s.qtys * pr.price) + s.adjustment + s.member_adjustment)- (s.discount - s.store_discount)) as totalamount,
						sum(s.qtys * i.product_cost) as purchase_price,
						cash1.cash_amount,
						credit_card1.credit_card_amount,
						cheque1.cheque_amount,
						bank_transfer1.bt_amount,
						deductions1.deduction_amount,
						member_credit1.member_amount,
						s.payment_id

					 from sales s

					 left join prices pr on pr.id = s.price_id
					 left join items i on i.id = s.item_id
					 left join terminals t on t.id = s.terminal_id
 					 $left_join_cash
				  	 $left_join_credit_card
				  	 $left_join_cheque
				  	 $left_join_bt
				  	 $left_join_deduction
				  	 $left_join_member_credit
					 where
					 s.company_id = ? and MONTH(FROM_UNIXTIME(s.sold_date)) = ?

					 and YEAR(FROM_UNIXTIME(s.sold_date)) = ?  and s.status = 0 $whereBranch
					 group by s.payment_id
					 order by  d";

				$data = $this->_db->query($q, $parameters);

				if($data->count()) {
					// return the data if exists
					return $data->results();
				}

			}
		}


		public function getSTSummary($cid=0,$month=0,$year=0,$branch_id=0,$date_type=0){
			if($cid && $month && $year ){
				$parameters = [];
				$parameters[] = $cid;

				$whereBranch = '';
				if($branch_id){
					$whereBranch = " and t.branch_id = ? ";
					$parameters[]= $branch_id;
				}
				if($date_type == 1){
					if($month && $year){
						$wheretimeframe = " and (CASE WHEN wh.id IS NULL THEN  MONTH(FROM_UNIXTIME(s.sold_date)) = '$month' and YEAR(FROM_UNIXTIME(s.sold_date)) = '$year' ELSE   MONTH(FROM_UNIXTIME(wh.is_scheduled)) = '$month' and YEAR(FROM_UNIXTIME(wh.is_scheduled)) = '$year' and wh.status = 4  END) ";
					}
				} else {
					$parameters[] = $month;
					$parameters[] = $year;
					$wheretimeframe = " and MONTH(FROM_UNIXTIME(s.sold_date)) = ? and YEAR(FROM_UNIXTIME(s.sold_date)) = ? ";
				}


				$left_join_cash = " left join (Select sum(amount) as cash_amount,payment_id from cash group by payment_id) cash on cash.payment_id = s.payment_id";
				$left_join_credit_card = " left join (Select sum(amount) as credit_card_amount, payment_id from credit_card group by payment_id) credit_card on credit_card.payment_id = s.payment_id";
				$left_join_cheque = " left join (Select sum(amount) as cheque_amount, payment_id from cheque group by payment_id) cheque on cheque.payment_id = s.payment_id";
				$left_join_bt = " left join (Select sum(amount) as bt_amount, payment_id from bank_transfer group by payment_id) bank_transfer on bank_transfer.payment_id = s.payment_id";
				$left_join_deduction = " left join (Select sum(amount) as deduction_amount, payment_id from deductions group by payment_id) deductions on deductions.payment_id = s.payment_id";
				$left_join_member_credit= " left join (Select sum(amount - amount_paid) as member_amount, payment_id from member_credit group by payment_id) member_credit on member_credit.payment_id = s.payment_id";
				//jump1
				$q= "Select  st.name as sales_type_name,
							 s.payment_id,
						sum(((s.qtys * pr.price) + s.adjustment + s.member_adjustment)- (s.discount - s.store_discount)) as totalamount,
						sum(s.qtys * i.product_cost) as purchase_price,
						cash.cash_amount,
						credit_card.credit_card_amount,
						cheque.cheque_amount,
						bank_transfer.bt_amount,
						deductions.deduction_amount,
						member_credit.member_amount
					 from sales s
					 $left_join_cash
				  	 $left_join_credit_card
				  	 $left_join_cheque
				  	 $left_join_bt
				  	 $left_join_deduction
				  	 $left_join_member_credit
					 left join prices pr on pr.id = s.price_id
					 left join items i on i.id = s.item_id
					 left join terminals t on t.id = s.terminal_id
					 left join salestypes st on st.id = s.sales_type
					 left join (Select id,for_pickup,payment_id,user_id,from_service,branch_id,is_scheduled,status from wh_orders) wh on wh.payment_id = s.payment_id
					 where
					 s.company_id = ? $wheretimeframe  and s.status = 0 $whereBranch
					 group by s.payment_id
					 order by  s.sales_type";

				$data = $this->_db->query($q, $parameters);

				if($data->count()) {
					// return the data if exists
					return $data->results();
				}

			}
		}
		public function getFreebieSummary($month=0,$year=0,$branch_id=0){
			if($month && $year ){
				$parameters = [];

				$parameters[] = $month;
				$parameters[] = $year;
				$whereBranch = '';
				if($branch_id){
					$whereBranch = " and t.branch_id = ? ";
					$parameters[]= $branch_id;
				}



				 $q= "
						Select whd.*, i.item_code,i.description,wh.dr,wh.invoice,wh.pr,m.lastname as member_name from wh_order_details whd
						left join wh_orders wh on wh.id = whd.wh_orders_id
						left join items i on i.id= whd.item_id
						left join members m on m.id= wh.member_id
						left join branches b on b.id =wh.branch_id
						where 1=1 and MONTH(FROM_UNIXTIME(wh.created)) = ? and YEAR(FROM_UNIXTIME(wh.created)) = ? $whereBranch
							and whd.is_freebie = 1
					";



				$data = $this->_db->query($q, $parameters);

				if($data->count()) {
					// return the data if exists
					return $data->results();
				}

			}
		}
		public function getFreebieSummaryYear($year=0,$branch_id=0){
			if(  $year ){
				$parameters = [];


				$parameters[] = $year;
				$whereBranch = '';
				if($branch_id){
					$whereBranch = " and t.branch_id = ? ";
					$parameters[]= $branch_id;
				}



				 $q= "
						Select MONTH(FROM_UNIXTIME(wh.created)) as m, sum(whd.qty) as tqty, i.item_code,i.description from wh_order_details whd
						left join wh_orders wh on wh.id = whd.wh_orders_id
						left join items i on i.id= whd.item_id
						left join branches b on b.id =wh.branch_id
						where
						 1=1 and YEAR(FROM_UNIXTIME(wh.created)) = ? $whereBranch
							and whd.is_freebie = 1
						group by i.item_code, MONTH(FROM_UNIXTIME(wh.created))
					";



				$data = $this->_db->query($q, $parameters);

				if($data->count()) {
					// return the data if exists
					return $data->results();
				}

			}
		}

		public function getSalesTypeSummary($cid=0,$dt=0){
			if($cid && $dt){
				$parameters = [];
				$parameters[] = $cid;
				$parameters[] = $dt;

				 $q= "Select  st.name as sales_type_name, MONTH(FROM_UNIXTIME(s.sold_date)) as m, sum(((s.qtys * pr.price) + s.adjustment + s.member_adjustment)- (s.discount - s.store_discount)) as totalamount, s.sales_type from sales s left join prices pr on pr.id = s.price_id  left join salestypes st on st.id = s.sales_type where s.company_id = ? and YEAR(FROM_UNIXTIME(s.sold_date)) = ? and s.status = 0 group by MONTH(FROM_UNIXTIME(s.sold_date)), s.sales_type order by  MONTH(FROM_UNIXTIME(s.sold_date))";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function getRegionSummary($cid=0,$dt=0){
			if($cid && $dt){
				$parameters = [];
				$parameters[] = $cid;
				$parameters[] = $dt;

				$q= "Select  m.region as region_name, MONTH(FROM_UNIXTIME(s.sold_date)) as m, sum(((s.qtys * pr.price) + s.adjustment + s.member_adjustment)- (s.discount - s.store_discount)) as totalamount from sales s left join prices pr on pr.id = s.price_id  left join members m on m.id = s.member_id where s.company_id = ? and YEAR(FROM_UNIXTIME(s.sold_date)) = ? and s.status = 0 group by MONTH(FROM_UNIXTIME(s.sold_date)), m.region order by  MONTH(FROM_UNIXTIME(s.sold_date))";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function getRegionOA($cid=0,$dt1=0,$dt2=0){
			if($cid){
				$parameters = [];
				$parameters[] = $cid;
				$whereDt = "";
				if($dt1 && $dt2){
					$dt1 = strtotime($dt1);
					$dt2 = strtotime($dt2 . " 1 day -1 sec");

					$whereDt = " and s.sold_date >= $dt1 and s.sold_date <= $dt2";
				}

				$q= "Select  m.region as region_name, sum(((s.qtys * pr.price) + s.adjustment + s.member_adjustment)- (s.discount - s.store_discount)) as totalamount from sales s left join prices pr on pr.id = s.price_id  left join members m on m.id = s.member_id where s.company_id = ? $whereDt and s.status = 0 group by m.region order by m.region ";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function getBranchSummary($cid=0,$dt=0){
			if($cid && $dt){
				$parameters = [];
				$parameters[] = $cid;
				$parameters[] = $dt;

				 $q= "Select  b.id as branch_id,b.name as branch_name, MONTH(FROM_UNIXTIME(s.sold_date)) as m, sum(((s.qtys * pr.price) + s.adjustment + s.member_adjustment)- (s.discount - s.store_discount)) as totalamount from sales s left join prices pr on pr.id = s.price_id  left join terminals t on t.id = s.terminal_id left join branches b on b.id = t.branch_id where s.company_id = ? and YEAR(FROM_UNIXTIME(s.sold_date)) = ? and s.status = 0 group by MONTH(FROM_UNIXTIME(s.sold_date)), b.id order by  MONTH(FROM_UNIXTIME(s.sold_date))";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function getBranchSummaryYearly($cid=0,$branch_id=0){
			if($cid){
				$parameters = [];
				$parameters[] = $cid;
				$parameters[] = $branch_id;

				 $q= "Select  b.id as branch_id,b.name as branch_name, MONTH(FROM_UNIXTIME(s.sold_date)) as m, YEAR(FROM_UNIXTIME(s.sold_date)) as y, sum(((s.qtys * pr.price) + s.adjustment + s.member_adjustment)- (s.discount - s.store_discount)) as totalamount from sales s left join prices pr on pr.id = s.price_id  left join terminals t on t.id = s.terminal_id left join branches b on b.id = t.branch_id where s.company_id = ?  and b.id = ? and s.status = 0 group by  YEAR(FROM_UNIXTIME(s.sold_date)), MONTH(FROM_UNIXTIME(s.sold_date)) order by MONTH(FROM_UNIXTIME(s.sold_date))";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function getServiceSummaryYearly($cid=0,$branch_id=0){
			if($cid){
				$parameters = [];
				$parameters[] = $cid;
				$whereBranch = '';
				if($branch_id){
					$parameters[] = $branch_id;
					$whereBranch = " and b.id = ? ";
				}


				$q= "Select  MONTH(FROM_UNIXTIME(s.sold_date)) as m, YEAR(FROM_UNIXTIME(s.sold_date)) as y, sum(((s.qtys * pr.price) + s.adjustment + s.member_adjustment)- (s.discount - s.store_discount)) as totalamount from sales s left join prices pr on pr.id = s.price_id  left join terminals t on t.id = s.terminal_id  left join (Select from_service, payment_id from wh_orders) wh on wh.payment_id = s.payment_id left join branches b on b.id = t.branch_id where s.company_id = ? $whereBranch and s.status = 0 and wh.from_service != 0 group by MONTH(FROM_UNIXTIME(s.sold_date)),YEAR(FROM_UNIXTIME(s.sold_date)) order by  MONTH(FROM_UNIXTIME(s.sold_date))";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function getServiceSummary($cid=0,$dt=0){
			if($cid && $dt){
				$parameters = [];
				$parameters[] = $cid;
				$parameters[] = $dt;

				$q= "Select b.id as branch_id, b.name as branch_name, MONTH(FROM_UNIXTIME(s.sold_date)) as m, sum(((s.qtys * pr.price) + s.adjustment + s.member_adjustment)- (s.discount - s.store_discount)) as totalamount from sales s left join prices pr on pr.id = s.price_id  left join terminals t on t.id = s.terminal_id  left join (Select from_service, payment_id from wh_orders) wh on wh.payment_id = s.payment_id left join branches b on b.id = t.branch_id where s.company_id = ? and YEAR(FROM_UNIXTIME(s.sold_date)) = ? and s.status = 0 and wh.from_service != 0 group by MONTH(FROM_UNIXTIME(s.sold_date)), b.id order by  MONTH(FROM_UNIXTIME(s.sold_date))";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}



		public function getPageNavigation($page, $total_pages, $limit, $stages) {
			getpagenavigation($page, $total_pages, $limit, $stages);
		}

		public function paginate($cid, $args) {

			$user = new User();
			$search = Input::get('search');
			$b = Input::get('b');
			$t = Input::get('t');
			$type = Input::get('type');
			$tran_type = Input::get('tran_type');
			$m = Input::get('mem_id');
			$sort_by = Input::get('sortby');
			$item_id = Input::get('item_id');

			$date_from = Input::get('date_from');
			$date_to = Input::get('date_to');

			$item_id = Input::get('item_id');

			// $this->getInconsistentData($cid,$user->data()->branch_id);
			$inconsistent = false;
			$is_franchisee = $user->hasPermission('is_franchisee');
			if($is_franchisee){
				$b = [$user->data()->branch_id];
			}
			$is_agent = $user->hasPermission('wh_agent');
			$user_id = 0;
			if($is_agent){
				$user_id = $user->data()->id;
			}
			if($inconsistent){//
				echo "<div class='well'>";
				echo "<p class='text-danger'><strong>You have unmatched sales total and payment total.</strong></p>";
				echo "<hr>";
				foreach($inconsistent as $incon){
					$invlabel='';
					$drlabel ='';
					if($incon->invoice){
						$invlabel="Invoice#".$incon->invoice;
					}
					if($incon->dr){
						$drlabel="Dr#".$incon->dr;
					}
					$alltotal = $incon->cashamount + $incon->chequeamount + $incon->btamount + $incon->ccamount + $incon->mcamount + $incon->pcamount+ $incon->pcfamount+ $incon->deduction;
					$alltotal = number_format($alltotal,2);
					echo "<p>$invlabel $drlabel Sales Total=".$incon->ttotal.", Payment Total=$alltotal <a class='btn btn-default pull-right' href='sales_crud.php?id=".Encryption::encrypt_decrypt('encrypt',$incon->payment_id)."'><span class='glyphicon glyphicon-pencil'> Edit</a></p>";
					echo "<p><small>(Cash=<span class='text-danger'>$incon->cashamount</span>, Cheque=<span class='text-danger'>$incon->chequeamount</span>, Credit Card=<span class='text-danger'>$incon->ccamount</span>, Bank Transfer=<span class='text-danger'>$incon->btamount</span>, Member Credit=<span class='text-danger'>$incon->mcamount</span>, Consumable=<span class='text-danger'>$incon->pcamount</span>, Consumable freebies=<span class='text-danger'>$incon->pcfamount</span>, Deduction=<span class='text-danger'>$incon->deduction</span>)</small></p>";
					echo "<hr>";
				}
				echo "</div>";
			}

			if(is_array($b) && $b[0] == -1){
				$b = -1;
			}

			?>

			<div id="no-more-tables">
				<div class="table-responsive">
					<table class='table' id='tblSales'>
						<thead>
						<tr>
							<TH title='Sort by Member' data-sort=' order by m.lastname ' class='page_sortby'>
								<?php echo MEMBER_LABEL; ?></TH>
							<TH title='Sort by invoice' data-sort=' order by IF (IFNULL(s.invoice,0) = 0, 1, 0), s.invoice * 1 ' class='page_sortby'><?php echo INVOICE_LABEL ; ?></TH>
							<TH title='Sort by dr' data-sort=' order by IF (IFNULL(s.dr,0) = 0, 1, 0), s.dr * 1 ' class='page_sortby'><?php echo DR_LABEL ; ?></TH>
							<TH title='Sort by sr' data-sort=' order by IF (IFNULL(s.sr,0) = 0, 1, 0), s.sr * 1 ' class='page_sortby'><?php echo "SR" ; ?></TH>
							<TH title='Sort by sr' data-sort=' order by IF (IFNULL(s.ir,0) = 0, 1, 0), s.ir * 1 ' class='page_sortby'><?php echo PR_LABEL ; ?></TH>
							<?php if(Configuration::getValue('has_sv') == 1){
								?>
								<TH title='Sort by sv' data-sort=' order by IF (IFNULL(s.sv,0) = 0, 1, 0), s.sv * 1 ' class='page_sortby'><?php echo "SV"; ?></TH>
								<?php
							}?>


							<TH title='Sort by item' data-sort='order by i.item_code ' class='page_sortby'>Item Code</TH>
							<TH title='Sort by price' data-sort='order by pr.price ' class='page_sortby text-right'>Price</TH>
							<TH title='Sort by quantity' data-sort='order by s.qtys ' class='page_sortby text-right'>Qty</TH>
							<TH class='text-right'>Adjustment</TH>
							<TH class='text-right'>Adjusted</TH>
							<TH title='Sort by total' data-sort='order by ((s.qtys * price)-s.discount) ' class='page_sortby text-right'>Total</TH>
							<TH title='Sort by quantity' data-sort='order by s.sold_date ' class='page_sortby'>Date sold</TH>
							<th></th>
						</tr>
						</thead>
						<tbody>
						<?php
							//$targetpage = "paging.php";
							$limit = 50;
							// type -> sales type only
							$sales_type_ar_cls = new Sales_type();
							$types_ar = $sales_type_ar_cls->getMySalesType($user->data()->id);
							if(!$tran_type){
								$arr_sales_type = [];
								if($types_ar){ // agent has sales type
									$user_id = 0;
									foreach($types_ar as $current_type){
										$arr_sales_type[] = $current_type->id;
									}
									$tran_type = $arr_sales_type;
								}
							} else {
								if($types_ar) $user_id = 0; // reset user id if agent has sales type
							}

							$countRecord = $this->countRecord($cid, $search, $b, $t, $m, $type, $sort_by,$tran_type,$user_id,$item_id,$date_from,$date_to);

							$total_pages = $countRecord->cnt;
							//echo "Total $total_pages";
							$stages = 3;
							$page = ($args);
							$page = (int)$page;
							if($page) {
								$start = ($page - 1) * $limit;
							} else {
								$start = 0;
							}

							$company_sales = $this->get_sales_record($cid, $start, $limit, $search, $b, $t, $m, $type, $sort_by,$tran_type,$user_id,$item_id,$date_from,$date_to);
							$this->getPageNavigation($page, $total_pages, $limit, $stages);

							if($company_sales) {
								$prevpid = 0;
								$wh_pickup_arr = ['For deliver','For Pick up','Cashier Transaction'];
								$is_service = false;
								foreach($company_sales as $s) {

									if($s->qtys == 0) continue;



									//$sss = new Sales();
									//$p_length = $sss->countPaymentLength($s->payment_id, $start, $limit);
									$wh_label = "";
									$member_label = "";
									$pickup_label = "";
									$branch_label = "";
									$is_service_label = "";
									$remarks_row = "";
									$client_po = "";
									if($prevpid != $s->payment_id) {
										$is_service_label = "<span class='span-block label label-primary'>Sales</span>";
										$is_service = false;
										if($s->is_service == 1 || $s->from_service != 0){
											$is_service_label = "<span class='span-block label label-danger'>Service</span>";
											$is_service = true;
										}
										$bordertop = "style='border-top:1px solid #ccc;'";
										if($s->wh_id){
											$wh_label = "<span class='span-block text-danger'>Order # $s->wh_id</span>";
											$pickup_label = "<span class='span-block text-danger'>" .$wh_pickup_arr[$s->for_pickup]."</span>";
										}

										$member_label = $s->member_name;
										$branch_label = $s->branch_name;
										if(isset($s->remarks) && $s->remarks){
											$remarks_row = $s->remarks;
										}
										if($s->wh_remarks){
											$remarks_row .= " " . $s->wh_remarks;
										}
										if($remarks_row){
											$remarks_row = "Order # ".$s->wh_id . " Remarks: " .  $remarks_row;
										}

										if($s->cr_number){
											$cr_number = "<br>CR: " . $s->cr_number;
										} else {
											$cr_number = "<br>CR: N/A";
										}
										if($s->client_po){
											$client_po = "<br>P.O: " . $s->client_po;
										} else {
											$client_po = "<br>P.O: N/A";
										}

										if($search){
											$cr_log_ids = new Cr_log_ids();
											$cr_for_this_payment_ids = $cr_log_ids->getByPaymentId($s->payment_id);
											$cr_number = "";
											if($cr_for_this_payment_ids){
												foreach($cr_for_this_payment_ids as $cr_for_this_payment){
													$cr_number .=  "<span class='span-block'>" .$cr_for_this_payment->cr_number."</span>";
												}

											} else {
												$cr_number .=  "<span class='span-block'>NO CR</span>";
											}

										}

									} else {
										$cr_number='';
										$bordertop = '';
										$wh_label="";
										$member_label ="";
										$pickup_label = "";
									}
									$ind_adjustment = 0;
									if($s->adjustment){
										$ind_adjustment = $s->adjustment / $s->qtys;
									}
									$addtl_remarks = "";
									if($s->addtl_remarks){
										$addtl_remarks = $s->addtl_remarks;
									}

									// add sales


									if(Configuration::thisCompany('pw')){
										//$item_id = 119; //tochange

									}else if(Configuration::thisCompany('vitalite')) {
										$item_id = 589; //tochange
									} else {
										//die("You are not allowed to used this.");
										$item_id = 589; //tochange
									}

									//if($s->item_id == $item_id) continue;
									if($remarks_row){
										echo "<tr class='bg-warning'><td colspan='14' style='border-top:1px solid #ccc;'>$remarks_row</td></tr>";
									}

									$adjusted_date = '';
								/*	if($s->member_adjustment){
										$memadj = new Member_term();
										$member_adjustment_data = $memadj->getAdjustmentMember($s->member_id,$s->item_id);

										if($member_adjustment_data){
											$adjusted_date = date('m/d/y',$member_adjustment_data->created);
										}
									} */


									?>
									<tr <?php echo $bordertop; ?> >
										<td data-title="<?php echo MEMBER_LABEL; ?>">
											<?php echo capitalize($member_label); ?>
											<?php echo $wh_label . $pickup_label; ?>
											<?php echo $cr_number;?>
											<?php echo $client_po;?>
											<small class='text-danger span-block'><?php echo $branch_label; ?></small>
											<small style='max-width:200px;' class='text-danger span-block'><?php echo $addtl_remarks; ?></small>
											<?php echo $is_service_label; ?>


										</td>
										<td data-title="Invoice">
											<strong>
												<?php echo ($s->invoice) ? escape($s->pref_inv.padLeft($s->invoice).$s->suf_inv) : "<i class='fa fa-ban'></i>"; ?>
											</strong>
										</td>
										<td data-title="Dr">
											<strong><?php echo ($s->dr) ? escape($s->pref_dr.padLeft($s->dr).$s->suf_dr) : "<i class='fa fa-ban'></i>" ?></strong></td>

										<td data-title="Sr">
											<strong><?php echo ($s->sr) ? escape(padLeft($s->sr)) : "<i class='fa fa-ban'></i>" ?></strong>
										</td>

										<td data-title="PR">
											<strong><?php echo ($s->ir) ? escape($s->pref_ir.padLeft($s->ir).$s->suf_ir) : "<i class='fa fa-ban'></i>" ?></strong>
										</td>
										<?php if(Configuration::getValue('has_sv') == 1){
											?>
										<td data-title="SV">
											<strong><?php echo ($s->sv) ? escape(padLeft($s->sv)) : "<i class='fa fa-ban'></i>" ?></strong>
										</td>
										<?php } ?>
										<td data-title="Item">
											<?php
												if($s->item_id == $item_id && $is_service){
												?>
													Service Sales
												<?php
												} else if($s->item_id == $item_id && !$is_service){
													?>
													Main Sales
													<?php
												} else {
													?>
													<?php echo escape($s->item_code) . "<br><small class='text-danger'>" . escape($s->description) . "</small>"; ?>
													<?php
												}
												$total_current = (($s->qtys * $s->price) + $s->adjustment + $s->member_adjustment) - ($s->discount + $s->store_discount);
											?>
										</td>
										<td data-title="Price" class='text-right'><?php echo escape(number_format(($s->price+$ind_adjustment), 2)); ?>
										</td>
										<td data-title="Quantity" class='text-right'><?php echo formatQuantity($s->qtys) ?></td>
										<td data-title="Adjustment" class='text-right' style='width:200px;'>
											<?php echo escape(number_format($s->member_adjustment, 2)) ?>
											<?php
												if($s->adjustment_remarks){
													echo "<small style='display:block;'>Remarks: $s->adjustment_remarks</small>";
												}
											?>
										</td>
										<td data-title="Adjusted" class='text-right'>
											<?php echo escape(number_format($total_current/$s->qtys, 2)) ?>
											<small class='span-blcok text-danger'>
												<?php
													if($adjusted_date){
														echo "(" .$adjusted_date . ")";
													}
												?>
											</small>
										</td>
										<td data-title="Total" class='text-right'>
											<strong><?php echo escape(number_format($total_current, 2)) ?></strong>
										</td>
										<td data-title="Date"><?php echo escape(date('m/d/Y ', $s->sold_date)); ?></td>
										<td class='text-left'>
											<?php
												if($prevpid != $s->payment_id) {
													?>
													<button title='Details' data-payment_id='<?php echo $s->payment_id ?>' class='btn btn-default btn-sm btn-margin btn-fixed-width paymentDetails'>
														<i class='fa fa-list'></i> Payment </button>
													<?php if($type != 1 &&  !$s->wh_id) {
														?>
														<button title='Cancel' data-payment_id='<?php echo $s->payment_id ?>' class='btn btn-default btn-sm btn-margin btn-fixed-width cancelPayment'>
															<i class='fa fa-close'></i> Cancel</button>
														<?php
													}
													?>

													<?php
													if($user->hasPermission('sales_crud')) {
														?>

														<?php if($type != 1) {
															?>

																<button title='Edit' data-payment_id='<?php echo Encryption::encrypt_decrypt('encrypt', $s->payment_id); ?>' class='btn btn-default btn-sm btn-margin btn-fixed-width editTransaction'>
																	<i class='fa fa-pencil'></i> Edit
																</button>

															<?php
															if($s->invoice){
																?>
																<button title='Re-Print' data-payment_id='<?php echo Encryption::encrypt_decrypt('encrypt', $s->payment_id); ?>' class='btn btn-default btn-sm btn-margin btn-fixed-width reprintVoucher'>
																	<i class='fa fa-print'></i> <?php echo INVOICE_LABEL; ?>
																</button>
																<?php
															}
															if($s->dr){
																?>
																<button title='Re-Print DD' data-payment_id='<?php echo Encryption::encrypt_decrypt('encrypt', $s->payment_id); ?>' class='btn btn-default btn-sm btn-margin btn-fixed-width reprintDr'>
																	<i class='fa fa-print'></i> <?php echo DR_LABEL; ?>
																</button>
																<?php
															}

															if($s->ir){
																?>
																<button title='Re-Print IR' data-payment_id='<?php echo Encryption::encrypt_decrypt('encrypt', $s->payment_id); ?>' class='btn btn-default btn-sm btn-margin btn-fixed-width reprintIr'>
																	<i class='fa fa-print'></i> <?php echo PR_LABEL; ?>
																</button>
																<?php
															}

															?>

															<?php
														}
														?>

														<?php
													}
													if($user->hasPermission('freight')){
													?>
													<button title='Freight' data-payment_id='<?php echo $s->payment_id ?>' class='btn btn-default btn-sm btn-margin btn-fixed-width btnFreight'>
														<i class='fa fa-truck'></i> Freight</button>
													<?php
													}
													if($user->hasPermission('billing_print')){
														?>
														<button title='Print Billing' data-member_id='<?php echo $s->member_id ?>' data-payment_id='<?php echo $s->payment_id ?>' class='btn btn-default btn-sm btn-margin btn-fixed-width btnPrintBillingStatement'>
															<i class='fa fa-print'></i> Print Billing</button>

														<button title='Email Billing' data-member_id='<?php echo $s->member_id ?>' data-payment_id='<?php echo $s->payment_id ?>' class='btn btn-default btn-sm btn-margin btn-fixed-width btnShowEmailModal'>
															<i class='fa fa-envelope-o'></i> Email Billing</button>

														<?php
													}
													$prevpid = $s->payment_id;
												}
											?>
										</td>
									</tr>

									<?php
								}
							} else {
								?>
								<tr>
									<td colspan='8'><h3><span class='label label-info'>No Record Found...</span></h3></td>
								</tr>
								<?php
							}
						?>
						</tbody>
					</table>
				</div>
			</div>
			<?php
		}
		public function isControlNumberExistsInTerminal($terminal_id = 0 ,$type=0,$ctr){
			if($terminal_id && $type && $ctr){
				if($type == 1){
					$col = "invoice";
				} else if ($type == 2){
					$col = "dr";
				} else if ($type == 3){
					$col = "ir";
				}
				$parameters = [];
				$parameters[] = $terminal_id;
				$parameters[] = $ctr;
				$q = "Select count(id) as cnt from sales where terminal_id = ? and $col = ? and status = 0";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function lastNumInTerminal($terminal_id = 0 ,$type=0){
			if($terminal_id && $type){
				if($type == 1){
					$col = "invoice";
				} else if ($type == 2){
					$col = "dr";
				} else if ($type == 3){
					$col = "ir";
				}
				$parameters = [];
				$parameters[] = $terminal_id;
				$q = "Select $col from sales where terminal_id = ? order by $col * 1 desc limit 1";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
			}
		}


		public function getCategorySummary($cid=0,$month=0,$year=0,$branch_id=0){
			if($cid && $month && $year ){
				$parameters = [];
				$parameters[] = $cid;
				$parameters[] = $month;
				$parameters[] = $year;
				$whereBranch = '';
				if($branch_id){
					$whereBranch = " and t.branch_id = ? ";
					$parameters[]= $branch_id;
				}



				$q= "Select  cat.name as category_name, DAY(FROM_UNIXTIME(s.sold_date)) as d,
					sum(((s.qtys * pr.price) + s.adjustment + s.member_adjustment)- (s.discount - s.store_discount)) as totalamount,
					sum(s.qtys * i.product_cost) as purchase_price
					 from sales s
					 left join prices pr on pr.id = s.price_id
					 left join items i on i.id = s.item_id
					 left join categories cat on cat.id = i.category_id
					 left join terminals t on t.id = s.terminal_id

					 where
					 s.company_id = ? and MONTH(FROM_UNIXTIME(s.sold_date)) = ?
					 and YEAR(FROM_UNIXTIME(s.sold_date)) = ?  and s.status = 0 $whereBranch
					 group by DAY(FROM_UNIXTIME(s.sold_date)), i.category_id
					 order by  DAY(FROM_UNIXTIME(s.sold_date))";

				$data = $this->_db->query($q, $parameters);

				if($data->count()) {
					// return the data if exists
					return $data->results();
				}

			}
		}



		public function reportItem($type=1,$branch_id=0,$dt1=0,$dt2=0,$dt_lastyear1=0,$dt_lastyear2=0,$limit_by=10,$from_service=0,$sort_type=1,$dl=0,$date_type=0){

				$parameters = array();

				$where_date = '';
				$where_branch = '';
				$where_service = '';
				$where_date_last_year = '';
				$last_year_on = "";
				$last_year_select = "";

				if($branch_id){
						$parameters[] = $branch_id;
						$where_branch = " and t.branch_id = ?";
				}
				$col_sort = "saletotal";
				if($sort_type == 1){
					$col_sort = "saletotal";
				} else if ($sort_type == 2){
					$col_sort = "qtytotal";
				}

				if($type == 1){
					$col = ",i.product_cost, i.item_code, i.description, s.item_id,cat.name as category_name";
					$group_by_col= "s.item_id";
					$last_year_col = 'item_id';
					$order_by = " order by $col_sort desc";

				} else if($type == 2){
					$col = ",cat.name as category_name, cat.id as category_id";
					$group_by_col= "cat.id";
					$last_year_col = 'category_id';
					$order_by = " order by category_name asc, $col_sort desc ";
				}

			if($dl==1){
				$order_by = " order by parent_name asc, category_name asc";
			}

			if($from_service == 1){
				$where_service = " and i.item_code like 'P%'";
			} else if ($from_service == -1){
				$where_service = "  and i.item_code not like 'P%'";
			}
			if($date_type == 1){

				if($dt1 && $dt1){
					$dt1 =strtotime($dt1);
					$dt2 =strtotime($dt2 . "1 day -1 min");


					$where_date = " and (CASE WHEN wh.id IS NULL THEN  s.sold_date >= $dt1 and s.sold_date <= $dt2 ELSE  wh.is_scheduled >= $dt1 and wh.is_scheduled <= $dt2 and wh.status = 4  END) ";

				} else {
					$dt1 =strtotime(date('F Y'));
					$dt2 =strtotime(date('F Y') . "1 month -1 min");

					$where_date = " and (CASE WHEN wh.id IS NULL THEN s.sold_date>=$dt1 and s.sold_date<=$dt2 ELSE  wh.is_scheduled >= $dt1 and wh.is_scheduled <= $dt2  and wh.status = 4 END ) ";
				}

			} else {
				if($dt1 && $dt2){
					$dt1 =strtotime($dt1);
					$dt2 =strtotime($dt2 . "1 day -1 min");
					$where_date =" and s.sold_date>=$dt1 and s.sold_date<=$dt2 ";
					$where_date_last_year =" and s.sold_date>=$dt_lastyear1 and s.sold_date<=$dt_lastyear2 ";

					/*$last_year_join = "Select
						   sum(((s.qtys * p.price) +s.adjustment + s.member_adjustment)-(s.discount + s.store_discount)) as saletotal,
						   sum(s.qtys) as qtytotal
							$col from  sales s
							left join terminals t on t.id = s.terminal_id
							left join prices p on p.id=s.price_id
							left join items i on i.id=s.item_id
							left join categories cat on cat.id = i.category_id
							where  s.status=0 $where_date_last_year $where_branch group by $group_by_col";
						   $last_year_on = " left join ($last_year_join) last_year on last_year.$last_year_col= $group_by_col ";
						   $last_year_select = ",last_year.saletotal as lysalestotal,last_year.qtytotal as lyqtytotal"; */
				} else {
					$dt1 =strtotime(date('F Y'));
					$dt2 =strtotime(date('F Y') . "1 month -1 min");
					$where_date =" and s.sold_date>=$dt1 and s.sold_date<=$dt2 ";
				}

			}


			 $q= "Select
						sum(((s.qtys * p.price) +s.adjustment + s.member_adjustment)-(s.discount + s.store_discount)) as saletotal,
						sum(s.qtys) as qtytotal, sup.cost,cat2.name as parent_name
						$last_year_select
						 $col
						 from sales s
						 	 left join items i on i.id=s.item_id
						 	  left join (Select avg(purchase_price) as cost , item_id from supplier_item group by item_id) sup on sup.item_id = s.item_id
						 left join categories cat on cat.id = i.category_id
						 left join categories cat2 on cat2.id = cat.parent
						 $last_year_on
						 left join terminals t on t.id = s.terminal_id
						 left join prices p on p.id=s.price_id
						 left join (Select id,status,is_scheduled, from_service , payment_id from wh_orders) wh on wh.payment_id = s.payment_id
						 where  s.status=0 $where_date $where_branch $where_service group by $group_by_col  $order_by limit $limit_by ";
				$data = $this->_db->query($q,$parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}

		}


		public function summaryByItem($from= 0 ,$to=0,$branch_id=0,$branch_id_except=0){
			$parameters = array();
			$where_branch = "";
			$where_branch_except = "";

			if($branch_id){
				$parameters[] = $branch_id;
				$where_branch = " and t.branch_id = ? ";
			} else if ($branch_id_except) {
				$parameters[] = $branch_id_except;
				$where_branch_except = " and t.branch_id != ? ";
			}

			$q= "
				 Select sum(s.qtys) as totalquantity, i.item_code, i.description from sales s
                 left join items i on i.id = s.item_id
                 left join terminals t on t.id = s.terminal_id
				 where
				 s.status = 0 and
				 s.sold_date >= $from
				 and s.sold_date <= $to
				 $where_branch
				 $where_branch_except
				 group by s.item_id
				";

			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}

		}


		public function memberSummary($cid=0,$dt=0,$dt_from=0,$dt_to=0){
			if($cid){
				$parameters = [];
				$parameters[] = $cid;
				$whereYear = "";
				$whereRange = "";
				if($dt){
					$dt = (int) $dt;
					$whereYear = "and YEAR(FROM_UNIXTIME(s.sold_date)) = $dt ";
				} else if ($dt_to && $dt_from){
					$whereRange = " and s.sold_date >= $dt_from and s.sold_date <= $dt_to ";
				}


				$q= "Select st.name as sales_type_name, m.lastname as member_name, MONTH(FROM_UNIXTIME(s.sold_date)) as m, YEAR(FROM_UNIXTIME(s.sold_date)) as y, sum(((s.qtys * pr.price) + s.adjustment + s.member_adjustment)- (s.discount - s.store_discount)) as totalamount from sales s left join prices pr on pr.id = s.price_id  left join members m  on m.id = s.member_id left join salestypes st on st.id = s.sales_type where s.company_id = ? $whereYear  $whereRange and s.status = 0 and s.member_id !=0  group by MONTH(FROM_UNIXTIME(s.sold_date)), s.member_id order by  YEAR(FROM_UNIXTIME(s.sold_date)), MONTH(FROM_UNIXTIME(s.sold_date)) limit  12000";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function memberSummaryByAgent($cid=0,$type=0){
			if($cid){
				$parameters = [];
				$parameters[] = $cid;
				$whereType = "";
				if($type){
					$parameters[] = $type;
					$whereType = " and s.sales_type= ? ";
				}

				$q= "Select
					st.name as sales_type_name, m.lastname as member_name,
					YEAR(FROM_UNIXTIME(s.sold_date)) as y,
					sum(((s.qtys * pr.price) + s.adjustment + s.member_adjustment)- (s.discount - s.store_discount)) as totalamount,
					ls.last_sold_date
					from sales s
					 left join (select member_id , max(sold_date) as last_sold_date, status from sales group by member_id having status = 0) ls on ls.member_id = s.member_id
					left join prices pr on pr.id = s.price_id
					left join members m  on m.id = s.member_id
					left join salestypes st on st.id = s.sales_type
					where s.company_id = ?  and s.status = 0 and s.member_id !=0 $whereType
					group by YEAR(FROM_UNIXTIME(s.sold_date)), s.member_id
					order by  YEAR(FROM_UNIXTIME(s.sold_date)), m.lastname";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function itemSummary($cid=0,$dt=0,$by_what=1,$branch_id=0,$order_by=1){

			if($cid && $dt){
				$parameters = [];
				$parameters[] = $cid;
				$parameters[] = $dt;
				$whereBranch = "";

				if($by_what == 1){
					$col = "sum(((s.qtys * pr.price) + s.adjustment + s.member_adjustment)- (s.discount - s.store_discount)) as totalamount";
				} else if($by_what == 2){
					$col = "sum(s.qtys) as totalamount";
				}

				if($branch_id){
					$parameters[] = $branch_id;
					$whereBranch = " and wh.branch_id = ? ";
				}
				if($order_by == 2){
					$whereOrder = " category_name asc ";
				} else {
					$whereOrder = "  MONTH(FROM_UNIXTIME(s.sold_date)) asc ";
				}

				$q= "Select cat.name as category_name, i.item_code,i.description, MONTH(FROM_UNIXTIME(s.sold_date)) as m, $col
					from sales s left join terminals t on t.id = s.terminal_id
					left join prices pr on pr.id = s.price_id
					left join items i  on i.id = s.item_id
					left join (select id, payment_id, branch_id from wh_orders) wh on wh.payment_id = s.payment_id
					 left join categories cat on cat.id = i.category_id
					 where s.company_id = ? and YEAR(FROM_UNIXTIME(s.sold_date)) = ? and s.status = 0 $whereBranch
					 group by MONTH(FROM_UNIXTIME(s.sold_date)), s.item_id order by  $whereOrder limit  20000";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function getByType($cid=0,$dateStart=0,$dateEnd=0){

			if($cid){

				$parameters = array();
				$parameters[] = $cid;

				if($dateStart && $dateEnd){
					$dateStart = strtotime($dateStart);
					$dateEnd = strtotime($dateEnd . '1 day -1 sec');
					$parameters[] = $dateStart;
					$parameters[] = $dateEnd;
					$wheretimeframe = " and s.sold_date >= ? and s.sold_date <= ?";
				}



				$left_join_cash = " left join (Select sum(amount) as cash_amount, payment_id from cash group by payment_id) cash on cash.payment_id = s.payment_id";
				$left_join_credit_card = " left join (Select sum(amount) as credit_card_amount, payment_id from credit_card group by payment_id) credit_card on credit_card.payment_id = s.payment_id";
				$left_join_cheque = " left join (Select sum(amount) as cheque_amount, payment_id from cheque group by payment_id) cheque on cheque.payment_id = s.payment_id";
				$left_join_bt = " left join (Select sum(amount) as bt_amount, payment_id from bank_transfer group by payment_id) bank_transfer on bank_transfer.payment_id = s.payment_id";
				$left_join_deduction = " left join (Select sum(amount) as deduction_amount, payment_id from deductions group by payment_id) deductions on deductions.payment_id = s.payment_id";
				$left_join_member_credit= " left join (Select sum(amount - amount_paid) as member_amount, payment_id from member_credit group by payment_id) member_credit on member_credit.payment_id = s.payment_id";
				$left_join_member = " left join (Select sum(amount - amount_paid) as member, payment_id from member group by payment_id) member on member.payment_id = s.payment_id";

				$q= "Select
					sum(((s.qtys * pr.price) + s.adjustment + s.member_adjustment)- (s.discount + s.store_discount)) as totalamount,
					 s.payment_id,s.invoice,s.dr,s.ir,s.pref_inv,s.pref_dr,s.pref_ir,s.suf_inv,s.suf_dr,s.suf_ir,s.sold_date,s.sv,s.pref_sv,s.suf_sv,
					 t.name as tname,b.name as bname, m.lastname as mln, m.firstname as mfn,s.cashier_id,od.user_id as reserved_by ,
				 	 st.name as sales_type_name,
				 	 cash.cash_amount,  credit_card.credit_card_amount, cheque.cheque_amount, bank_transfer.bt_amount, deductions.deduction_amount, member_credit.member_amount
				  	from sales  s
				  	 left join payments p on p.id = s.payment_id
				  	 $left_join_cash
				  	 $left_join_credit_card
				  	 $left_join_cheque
				  	 $left_join_bt
				  	 $left_join_deduction
				  	 $left_join_member_credit
				   	left join (Select id, for_pickup,payment_id,user_id,from_service from wh_orders) wh on wh.payment_id = s.payment_id
				   	 left join orders od on od.payment_id = s.payment_id
				     left join terminals t on t.id=s.terminal_id
				     left join branches b on b.id=t.branch_id
				     left join items it on it.id=s.item_id
				     left join items i on i.id = s.item_id
				     left join prices pr on pr.id=s.price_id
				     left join members m on m.id = s.member_id
				     left join salestypes st on st.id=s.sales_type
				     where s.company_id=? and s.is_active=1 and s.status=0  $wheretimeframe group by s.sales_type ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function getForecast($year=0){
			if($year ){
				$parameters = [];
				$parameters[] = $year;

				$q= "Select  b.name as branch_name,
						bq.amount as quota,
						MONTH(FROM_UNIXTIME(s.sold_date)) as m,
						sum(((s.qtys * pr.price) + s.adjustment + s.member_adjustment)- (s.discount - s.store_discount)) as totalamount
						from sales s left join prices pr on pr.id = s.price_id
						left join terminals t on t.id = s.terminal_id
						left join branches b on b.id = t.branch_id
						left join branch_quotas bq on bq.m = MONTH(FROM_UNIXTIME(s.sold_date)) and bq.y = $year and bq.branch_id = t.branch_id
						where
						YEAR(FROM_UNIXTIME(s.sold_date)) = ? and s.status = 0
						group by MONTH(FROM_UNIXTIME(s.sold_date)), b.name order by  b.name, m ";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function dailyForecast($from=0,$to=0){
			if($from && $to ){
				$parameters = [];


				$q= "Select  b.name as branch_name, b.daily_quota,

						DATE(FROM_UNIXTIME(s.sold_date)) as d,
						sum(((s.qtys * pr.price) + s.adjustment + s.member_adjustment)- (s.discount - s.store_discount)) as totalamount
						from sales s left join prices pr on pr.id = s.price_id
						left join terminals t on t.id = s.terminal_id
						left join branches b on b.id = t.branch_id

						where
						s.sold_date >=$from and s.sold_date <= $to and s.status = 0
						group by DATE(FROM_UNIXTIME(s.sold_date)), b.name order by  b.name, d ";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the dsata if exists
					return $data->results();
				}
			}
		}

		public function getLastPrice($item_id=0,$member_id=0){

			if($item_id && $member_id ){
				$parameters = [];
				$parameters[] = $item_id;
				$parameters[] = $member_id;

				$q= "
						Select s.id, s.sold_date, p.price, s.member_adjustment, s.adjustment, s.qtys
						from sales s
						left join prices p on p.id = s.price_id
					  	where s.item_id = ? and s.member_id = ?
					  	order by s.sold_date desc limit 1
					";

				$data = $this->_db->query($q, $parameters);

				if($data->count()) {
					return $data->first();
				}
				return false;
			}
		}

		public function invoiceBranchSalesExists($invoice=0,$branch=0){

			if($invoice && $branch ){
				$parameters = [];
				$parameters[] = $branch;
				$parameters[] = $invoice;



				$q= "Select count(*) as cnt from sales  where branch_id = ? and invoice = ?";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the dsata if exists
					return $data->first();
				}
				return false;
			}
		}

		public function getSalesByDicer($cid=0,$dateStart=0,$dateEnd=0,$cashier=0){
			if($cid){
				$parameters = array();
				$parameters[] = $cid;

				$wheretimeframe = "";

				if($dateStart && $dateEnd){

					$parameters[] = $dateStart;
					$parameters[] = $dateEnd;
					$wheretimeframe = " and s.sold_date >= ? and s.sold_date <= ?";
				}

				if ($cashier){
					$parameters[] = $cashier;
					$wherecashier = " and s.cashier_id = ?";
				}

				$q= "Select s.*,
						sum(((s.qtys * pr.price) + s.adjustment + s.member_adjustment) - (s.discount + s.store_discount)) as totalamount
						from sales s left join payments p on p.id = s.payment_id
						left join prices pr on pr.id=s.price_id
						where s.company_id=?  and s.is_active=1
						$wheretimeframe
						$wherecashier
						group by s.payment_id desc";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

	}

?>
