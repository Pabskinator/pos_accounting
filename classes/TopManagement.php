<?php
	class TopManagement extends Crud{

		public function __construct($t = NULL){
			parent::__construct($t);
		}

		public function getSalesByBranch($dt_from=0,$dt_to=0){
			$parameters=[];
			$parameters[] = $dt_from;
			$parameters[] = $dt_to;

			$whereDate= " and s.sold_date >= ? and s.sold_date <= ? ";

			 $q= "Select sum(((s.qtys * p.price) + s.adjustment + s.member_adjustment)-(s.discount + s.store_discount)) as saletotal,
					b.name as branch_name
					from sales s
					left join prices p on p.id=s.price_id
					left join terminals t on t.id=s.terminal_id
					left join branches b on b.id=t.branch_id
					where 1=1 and s.status=0 $whereDate group by b.id";

			$data = $this->_db->query($q,$parameters);

			if($data->count()){
				// return the data if exists
				return $data->results();
			}
		}

		public function getSalesBySalesType($dt_from=0,$dt_to=0){

			$parameters=[];
			$parameters[] = $dt_from;
			$parameters[] = $dt_to;

			$whereDate= " and s.sold_date >= ? and s.sold_date <= ? ";

			$q= "Select sum(((s.qtys * p.price) + s.adjustment + s.member_adjustment)-(s.discount + s.store_discount)) as saletotal,
					st.name as sales_type_name
					from sales s
					left join prices p on p.id=s.price_id
					left join salestypes st on st.id=s.sales_type

					where 1=1 and s.status=0  $whereDate group by st.id";

			$data = $this->_db->query($q,$parameters);

			if($data->count()){
				// return the data if exists
				return $data->results();
			}
		}

		public function getTotal($dt_from=0,$dt_to=0){

			$parameters=[];
			$parameters[] = $dt_from;
			$parameters[] = $dt_to;

			$whereDate= " and s.sold_date >= ? and s.sold_date <= ? ";

			$q= "Select sum(((s.qtys * p.price) + s.adjustment + s.member_adjustment)-(s.discount + s.store_discount)) as saletotal
					from sales s
					left join prices p on p.id=s.price_id
					left join salestypes st on st.id=s.sales_type
					where 1=1 and s.status=0  $whereDate ";

			$data = $this->_db->query($q,$parameters);

			if($data->count()){
				// return the data if exists
				return $data->first();
			}
		}

		public function stockValue(){

			$parameters=[];



			$now = time();

			$q= "select sum(p.price * i.qty) as total_amount , i.branch_id,b.name as branch_name
					from inventories i
					left join branches b on b.id = i.branch_id
					left join items it on it.id = i.item_id
					left join
								( Select a.item_id, a.effectivity, p.price, p.id as price_id from
									(Select p.item_id, max(p.effectivity) as effectivity
									from prices p
									left join items i on i.id=p.item_id
									where p.effectivity <= $now group by p.item_id) a
									left join prices p on p.item_id = a.item_id
								where a.effectivity = p.effectivity) p on p.item_id = i.item_id
						where i.item_id != 4560
						group by i.branch_id ";

			$data = $this->_db->query($q,$parameters);

			if($data->count()){
				// return the data if exists
				return $data->results();
			}
		}

		public function totalCredit(){

			$parameters=[];


			$q = "
					select sum(m.amount - m.amount_paid)  as total_amount
					from member_credit m left join
					(Select status, payment_id from sales group by payment_id) s on s.payment_id = m.payment_id
					where m.status = 0 and s.status = 0
				";

			$data = $this->_db->query($q,$parameters);

			if($data->count()){
				// return the data if exists
				return $data->first();
			}
		}
		public function totalCreditByTime($dt1=0,$dt2=0){

			$parameters=[];



			$q = "
					select sum(m.amount - m.amount_paid)  as total_amount
					from member_credit m left join
					(Select status, payment_id from sales group by payment_id) s on s.payment_id = m.payment_id
					where m.status = 0 and s.status = 0 and m.created >= $dt2 and m.created <= $dt1

				";

			$data = $this->_db->query($q,$parameters);

			if($data->count()){
				// return the data if exists
				return $data->first();
			}
		}
		public function totalPendingRequest(){

			$parameters=[];


			$q = " Select count(wh.id) as total, b.name as branch_name
 					 from wh_orders  wh
 					left join branches b on b.id = wh.branch_id
 					where wh.is_active= 1
 					 and wh.status in (1,2,3)
 					 group by wh.branch_id
 					 ";

			$data = $this->_db->query($q,$parameters);

			if($data->count()){
				// return the data if exists
				return $data->results();
			}
		}
		public function getCollection($dt1 = 0, $dt2=0){

			$parameters=[];

			$where_date = " and cr.created >= $dt1  and cr.created <= $dt2  ";

			$q = "  Select sum(cr.paid_amount) as pamount, cr.cr_number, col.created
					from cr_log cr
					left join (Select ref_id , created from collection_reports) col on col.ref_id = cr.cr_number
					where 1=1 $where_date
					group by cr.cr_number
					order by cr.id
					";

			$data = $this->_db->query($q,$parameters);

			if($data->count()){
				// return the data if exists
				return $data->results();
			}
		}

		public function getAllCr($from = 0, $to= 0,$num_from=0,$num_to=0,$agent_id =0,$sort_type=1,$branch_id=0){
			$whereDt = "";
			$whereCRRange = "";
			$whereAgent = "";
			$whereBranch= "";

			if($from && $to){
				$from = strtotime($from);
				$to = strtotime($to . " 1 day -1 sec" );
				$whereDt = " and p.cr_date >= $from and p.cr_date <= $to ";
			}

			if($num_from && $num_to){
				$num_from = (int) $num_from;
				$num_to = (int) $num_to;
				$whereCRRange = " and (p.cr_number * 1) >= $num_from and  (p.cr_number * 1) <= $num_to";
			}

			if($sort_type == 1){
				$sort = " p.cr_date desc ";
			} else if ($sort_type == 2){
				$sort = " p.cr_date asc ";
			}else if ($sort_type == 3){
				$sort = " p.cr_number * 1 desc ";
			}else if ($sort_type == 4){
				$sort = " p.cr_number * 1 asc ";
			}else {
				$sort = " p.cr_date desc ";
			}


			if($agent_id){
				$agent_id= (int) $agent_id;
				$whereAgent = " and crep.agent_id = $agent_id ";
			}

			if($branch_id){
				$branch_id= (int) $branch_id;
				$whereBranch = " and ss.branch_id = $branch_id ";
			}

			$parameters = array();

			$q= "   Select p.*,
 					IFNULL(cr_sum.paid_amount,0) as paid_amount,
 					IFNULL(cr_sum.receipt_amount,0) as receipt_amount,
 					IFNULL(cr_sum.deduction,0) as deduction, ss.branch_id
 					 from payments p
					  left join (Select * from collection_reports group by ref_id) crep on crep.ref_id = p.cr_number
 					 left join
 					  (
	                      Select cr_number, sum(receipt_amount) as receipt_amount,
	                      sum(deduction) as deduction,sum(paid_amount) as paid_amount
	                      from cr_log
	                      group by cr_number
 					  ) cr_sum on cr_sum.cr_number = p.cr_number
 					  left join (
 					  	select s.payment_id , t.branch_id
 					  	from sales s
 					  	left join terminals t on t.id = s.terminal_id group by s.payment_id
 					  	 ) ss on ss.payment_id = p.id
 					  where
 					  p.cr_number != '' $whereDt $whereCRRange $whereAgent $whereBranch
 					  group by p.cr_number order by $sort ";

			$e = $this->_db->query($q, $parameters);
			if($e->count()){
				return $e->results();
			}

			return false;

		}

		public function getCollectionReport($cr_number='') {
			$parameters = array();

			$parameters[] = $cr_number;

			$q = "Select * from cr_log where cr_number = ?";

			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				// return the data if exists
				return $data->results();
			}
		}
	}

?>