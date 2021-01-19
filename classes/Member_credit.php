<?php
	class Member_credit extends Crud{
		protected $_table = 'member_credit';
		public function __construct($m=null){
			parent::__construct($m);
		}
		public function getByPids($pids = ""){

			if($pids){
				$parameters = [];

				$q= 'Select * from member_credit  where  payment_id in ('.$pids.') and is_active = 1';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
				return false;
			}
		}
		public function countRecord($cid,$search='',$type=0,$dt_from=0,$dt_to=0,$branch=0,$terminal=0,$sales_type=0,$user_id=0){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$typeWhere = '';
				$likewhere = "";
				$dateWhere = "";
				if($search) {
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$likewhere = " and (mm.lastname like ? or s.sr like ? or s.dr like ? or s.invoice like ? or s.ir like ? ) ";
				}
				if($type == 1){
					$typeWhere = " and m.amount = m.amount_paid and m.status = 1 ";
				} else if($type == 2) {
					$typeWhere = " and m.amount != m.amount_paid and m.status = 0 ";
				} else if($type == 3) {
					$typeWhere = " and m.amount = m.amount_paid and m.status = -1 ";
				}
				if($dt_to && $dt_from){
					$dt_to = strtotime($dt_to . " 23:99");
					$dt_from = strtotime($dt_from );
					$dateWhere = " and m.created >= $dt_from and m.created<= $dt_to";
				}
				$wherebranch ='';
				$whereterminal='';
				$whereSalesType = '';
				if ($branch || $terminal){
					if (!$terminal){
						$tempb='';
						foreach($branch as $b){

							$parameters[] = $b;
							$tempb  .='?,';

						}
						$tempb = rtrim($tempb,',');
						$wherebranch = " and s.branch_id in ($tempb)";
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
				if($sales_type){
					$temps='';
					foreach($sales_type as $st){
						$parameters[] = $st;
						$temps  .='?,';
					}
					$temps = rtrim($temps,',');
					$whereSalesType = " and s.sales_type in ($temps)";
				}

				$where_user="";
				if($user_id){
					$user_id = (int)$user_id;
					$where_user = "and  CONCAT( ',', mm.agent_id, ',' ) LIKE '%,$user_id,%'";
				}

			 $q = "Select IFNULL(count(m.id),0) as cnt from member_credit m
				left join payments p on p.id=m.payment_id
				left join (select s.ir, s.sr, s.payment_id,s.invoice,s.dr,s.terminal_id,b.id as branch_id,s.sales_type, s.status as sales_status
				 	from sales s left join terminals t on t.id = s.terminal_id
				 	left join branches b on b.id=t.branch_id where 1=1  group by s.payment_id) s on s.payment_id=p.id
				left join members mm on mm.id=m.member_id
				where m.is_active=1 and p.company_id=? and s.sales_status=0  $likewhere $wherebranch $whereterminal $whereSalesType  $typeWhere $dateWhere $where_user";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function get_member_record($cid,$start,$limit,$search='', $type =0,$dt_from=0,$dt_to=0,$branch=0,$terminal=0,$sales_type=0,$user_id=0){
			$parameters = array();

			if($cid){
				$parameters[] = $cid;
				$likewhere = "";
				$typeWhere = "";
				$dateWhere = "";

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
					$likewhere = " and (mm.lastname like ? or s.sr like ? or s.dr like ? or s.invoice like ? or s.ir like ? ) ";
				}
				if($type == 1){
					$typeWhere = " and m.amount = m.amount_paid and m.status = 1 ";
				} else if($type == 2) {
					$typeWhere = " and m.amount != m.amount_paid and m.status = 0 ";
				} else if($type == 3) {
					$typeWhere = " and m.amount = m.amount_paid and m.status = -1 ";
				}
				if($dt_to && $dt_from){
					$dt_to = strtotime($dt_to);
					$dt_from = strtotime($dt_from );
					$dateWhere = " and m.created >= $dt_from and m.created<= $dt_to";
				}

				$wherebranch ='';
				$whereterminal='';
				$whereSalesType='';
				if ($branch || $terminal){
					if (!$terminal){
						$tempb='';
						foreach($branch as $b){
							$parameters[] = $b;
							$tempb  .='?,';
						}
						$tempb = rtrim($tempb,',');
						$wherebranch = " and s.branch_id in ($tempb)";
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

				if($sales_type){
					$temps='';
					foreach($sales_type as $st){
						$parameters[] = $st;
						$temps  .='?,';
					}
					$temps = rtrim($temps,',');
					$whereSalesType = " and s.sales_type in ($temps)";
				}

				$where_user="";
				if($user_id){

					$user_id = (int) $user_id;

					$where_user = "and  CONCAT( ',', mm.agent_id, ',' ) LIKE '%,$user_id,%'";

				}

				 $q= "Select
							p.cr_number, p.cr_date, p.docs,s.station_name,wh.remarks as wh_remarks, ag.firstname as  ufn,
							ag.lastname as uln , fc.charges, whb.name as to_branch_name, mm.lastname,mm.firstname,
							mm.middlename, m.*, m.created as solddate, s.invoice,s.dr,s.ir,s.sr, s.sales_type_name,s.sold_date
							from member_credit m
							left join payments p on p.id=m.payment_id
							left join (select payment_id, sum(charge - paid_amount + freight_adjustment) as charges from freight_charges where status = 0 group by payment_id) fc on fc.payment_id = p.id
							left join (
										select s.sr, s.payment_id,s.invoice,s.dr,s.ir,s.terminal_id,
										b.id as branch_id,s.sales_type, s.status as sales_status,
										 st.name as station_name, stp.name as sales_type_name,s.sold_date
										 from sales s
										 left join terminals t on t.id = s.terminal_id
										 left join branches b on b.id=t.branch_id
										 left join stations st on st.id = s.station_id
										 left join salestypes stp on stp.id = s.sales_type
										  where 1=1  group by s.payment_id) s on s.payment_id=p.id
						  left join members mm on mm.id=m.member_id
						  left join wh_orders wh on wh.payment_id = m.payment_id
						  left join users ag on ag.id = wh.user_id
						  left join branches whb on whb.id = wh.to_branch_id
						  where m.is_active = 1 and p.company_id=? and s.sales_status=0 $likewhere $wherebranch $whereterminal $whereSalesType  $typeWhere $dateWhere $where_user $l  ";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function get_credit($dt_from=0,$dt_to=0,$branch=0,$date_type=0){
			$parameters = array();



				$dateWhere = "";
				$branchWhere="";
				if($date_type){
					if($dt_to && $dt_from){
						$dt_from = strtotime($dt_from);
						$dt_to = strtotime($dt_to . "1 day -1 sec");

						$dateWhere = " and (CASE WHEN wh.id IS NULL THEN  s.sold_date >= $dt_from and s.sold_date <= $dt_to ELSE  wh.is_scheduled >= $dt_from and wh.is_scheduled <= $dt_to and wh.status = 4  END) ";

					} else {
						$dateWhere = " and (CASE WHEN wh.id IS NULL THEN 1 ELSE wh.status = 4 END ) ";

					}
				} else {
					if($dt_to && $dt_from){
						$dt_to = strtotime($dt_to);
						$dt_from = strtotime($dt_from );
						$dateWhere = " and s.sold_date >= $dt_from and s.sold_date<= $dt_to";
					}
				}
			if($branch){
				$branch = (int) $branch;
				$branchWhere = " and wh.branch_id = $branch ";
			}



				 $q= "Select
							 m.*,mm.lastname, m.created as solddate, s.invoice,s.dr,s.ir,s.sr ,s.sold_date, st.name as sales_type_name
							from member_credit m
							left join (select branch_id,id,payment_id, status, is_scheduled from wh_orders ) wh on wh.payment_id = m.payment_id
							left join (
									 select s.sr, s.payment_id,s.invoice,s.dr,s.ir,s.terminal_id,
									 s.sales_type, s.status as sales_status,s.sold_date
									 from sales s
									 where 1=1  group by s.payment_id
									 ) s on s.payment_id=m.payment_id
						  left join salestypes st on st.id = s.sales_type
						  left join members mm on mm.id=m.member_id
						  where m.is_active = 1  and
						   (m.amount - m.amount_paid) != 0
						   and s.sales_status=0 $dateWhere $branchWhere ";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}

		}
		public function getCreditForTheDay($dt1=0,$dt2=0) {

			$parameters = array();

			$parameters[] = 1;

			$where_dt = "";

			if($dt1 && $dt2){
				$where_dt = " and m.modified >= $dt1 and m.modified <= $dt2 ";
			}

			$q = "Select  m.*, mm.lastname as member_name from member_credit m left join members mm on mm.id = m.member_id  where 1 = ? $where_dt";

			$data = $this->_db->query($q, $parameters);
			// return results if there is any
			if($data->count()) {
				return $data->results();
			}

		}
		public function getMemberCreditPayment($member_id){
			$parameters = array();
			if($member_id){
				$parameters[] = $member_id;

				$q= "Select wh.to_branch_id,wh.branch_id as w_branch_id, mm.lastname,mm.firstname, mm.middlename, m.* from member_credit m left join payments p on p.id=m.payment_id left join (Select status,payment_id from sales group by payment_id) st on st.payment_id = m.payment_id  left join wh_orders wh on wh.payment_id = m.payment_id left join members mm on mm.id=m.member_id where m.member_id = ? and st.status = 0 ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function getPendingCredit($member_id =0,$branch_id=0){
				$parameters = array();
				$whereMember="";
				$whereBranch="";

				if($member_id){
					$parameters[] = $member_id;
					$whereMember = " and m.member_id = ? ";
				}
				if($branch_id){
					$parameters[] = $branch_id;
					$whereBranch = " and st.branch_id = ? ";
				}

				 $q= "  Select
						 st.sold_date,st.invoice,st.dr,st.ir, wh.to_branch_id,wh.branch_id as w_branch_id,
						 mm.lastname,mm.firstname, mm.middlename, m.*, b.name as branch_name
						 from member_credit m
						 left join payments p on p.id=m.payment_id
						 left join (Select invoice,dr,ir,status,payment_id ,sold_date, terminal_id from sales group by payment_id)
						 st on st.payment_id = m.payment_id
						 left join wh_orders wh on wh.payment_id = m.payment_id
						 left join terminals t on t.id = st.terminal_id
						 left join branches b on b.id = t.branch_id
						 left join members mm on mm.id=m.member_id
						 where 1=1 $whereMember $whereBranch and m.status = 0 and st.status = 0 ";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
				return false;
			}

		public function getMemberCreditDetials($id=0){
			if($id){
				$parameters = [];
				$parameters[] = $id;
				$q= "Select mm.terms as def_terms,mm.lastname,mm.firstname, mm.middlename,s.invoice,s.dr,s.ir, m.*,p.created as sold_date,s.terminal_id from member_credit m left join payments p on p.id=m.payment_id  left join (select s.payment_id,s.invoice,s.dr,s.ir,s.terminal_id,b.id as branch_id,s.sales_type from sales s left join terminals t on t.id = s.terminal_id left join branches b on b.id=t.branch_id where 1=1  group by s.payment_id) s on s.payment_id=p.id left join members mm on mm.id=m.member_id where m.id = ? ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->first();
				}
			}
		}
		public function getMemberCreditByPaymentID($id=0){
			if($id){
				$parameters = [];
				$parameters[] = $id;
				$q= "Select  m.* from member_credit m where m.payment_id = ? ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->first();
				}

			}
		}

		public function topMemberCredit($cid = 0,$dt1=0,$dt2=0,$limit=10){
			$parameters = array();
			$parameters[] = $cid;
			$wheredate = '';

			if($dt1 && $dt2){
				$wheredate =" and mc.created>=$dt1 and mc.created<=$dt2 ";
			}

			$q= "Select sum(mc.amount-mc.amount_paid) as credittotal, m.lastname as member_name , st.name as sales_type_name
				from member_credit mc
				left join members m on m.id=mc.member_id
				left join (select payment_id, status from sales where status = 0 group by payment_id ) s on s.payment_id = mc.payment_id
				 left join salestypes st on st.id = m.salestype
				 where  s.status=0 $wheredate group by mc.member_id order by credittotal desc limit $limit ";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}
		}

		public function getByPaymentId($pid = 0){
			$parameters = array();
			if($pid){
				$parameters[] = $pid;

				$q= "Select * from member_credit where payment_id = ?";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->first();
				}
			}
		}

		public function getByBranch($branch_id = 0){
			$parameters = array();
			if($branch_id){
				$parameters[] = $branch_id;

				$q= "Select wh.branch_id, sum(m.amount - m.amount_paid) as total_credit, st.name as sales_type_name
					from member_credit m
						left join payments p on p.id=m.payment_id
						left join
							(Select invoice,dr,ir,status,payment_id,sales_type from sales group by payment_id)
								s on s.payment_id = m.payment_id
						left join salestypes st on st.id = s.sales_type
						left join wh_orders wh on wh.payment_id = m.payment_id
						where wh.branch_id = ? and m.status = 0 and s.status = 0 group by st.name order by st.name asc ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function typeSummary($dt_from=0,$dt_to=0,$date_type=0,$branch_id=0){

			$parameters = array();

			$dateWhere = "";
			$branchWhere = "";

			if($date_type){
				if($dt_to && $dt_from){
					$dt_from = strtotime($dt_from);
					$dt_to = strtotime($dt_to . "1 day -1 sec");

					$dateWhere = " and (CASE WHEN wh.id IS NULL THEN  s.sold_date >= $dt_from and s.sold_date <= $dt_to ELSE  wh.is_scheduled >= $dt_from and wh.is_scheduled <= $dt_to and wh.status = 4  END) ";

				} else {
					$dateWhere = " and (CASE WHEN wh.id IS NULL THEN 1 ELSE wh.status = 4 END ) ";

				}
			} else {
				if($dt_to && $dt_from){
					$dt_to = strtotime($dt_to . "1 day -1 sec");
					$dt_from = strtotime($dt_from );
					$dateWhere = " and s.sold_date >= $dt_from and s.sold_date <= $dt_to";
				}
			}

			if($branch_id){
				$branch_id = (int) $branch_id;
				$branchWhere = "and wh.branch_id = $branch_id";
			}
			$q= "Select wh.branch_id, sum(m.amount - m.amount_paid) as total_credit, st.name as sales_type_name
				from member_credit m
					left join payments p on p.id=m.payment_id
					left join
						(Select invoice,dr,ir,status,payment_id,sales_type,sold_date from sales group by payment_id)
							s on s.payment_id = m.payment_id
					left join salestypes st on st.id = s.sales_type
					left join wh_orders wh on wh.payment_id = m.payment_id
					where m.amount != m.amount_paid  and s.status = 0 $dateWhere $branchWhere group by st.name order by st.name asc ";
			$data = $this->_db->query($q, $parameters);
			// return results if there is any
			if($data->count()){
				return $data->results();
			}

		}
		public function getByAgent($agent_id = 0,$branch_id = 0,$dt_from=0,$dt_to=0,$date_type=0){
			$parameters = array();
			if($agent_id){
				$parameters[] = $agent_id;

				$dateWhere = "";
				$branchWhere = "";
				if($date_type){
					if($dt_to && $dt_from){
						$dt_from = strtotime($dt_from);
						$dt_to = strtotime($dt_to . "1 day -1 sec");

						$dateWhere = " and (CASE WHEN wh.id IS NULL THEN  s.sold_date >= $dt_from and s.sold_date <= $dt_to ELSE  wh.is_scheduled >= $dt_from and wh.is_scheduled <= $dt_to and wh.status = 4  END) ";

					} else {
						$dateWhere = " and (CASE WHEN wh.id IS NULL THEN 1 ELSE wh.status = 4 END ) ";

					}
				} else {
					if($dt_to && $dt_from){
						$dt_to = strtotime($dt_to . "1 day -1 sec");
						$dt_from = strtotime($dt_from );
						$dateWhere = " and s.sold_date >= $dt_from and s.sold_date <= $dt_to";
					}
				}

				if($branch_id){
					$parameters[] = $branch_id;
					$branchWhere = "and wh.branch_id = ? ";
				}

				 $q= "Select m.*, st.name as sales_type_name,mm.credit_limit, mm.lastname as member_name, mm.terms, s.dr,s.invoice, s.ir, s.sold_date, wh.is_scheduled
					from member_credit m
						left join payments p on p.id=m.payment_id
						left join
							(Select invoice,dr,ir,status,payment_id,sales_type, member_id ,sold_date from sales group by payment_id)
								s on s.payment_id = m.payment_id
						left join salestypes st on st.id = s.sales_type
						left join wh_orders wh on wh.payment_id = m.payment_id
						left join members mm on mm.id = m.member_id
						where mm.agent_id = ? and m.status = 0 and s.status = 0 $dateWhere $branchWhere order by mm.lastname asc ";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}

		public function checkerBounce($id = 0, $ref=0){
			if($ref){
				$parameters = [];
				$parameters[] = $id;
				$parameters[] = $ref;
				$q= "Select count(*) as cnt from member_credit where payment_id = ? and ref_check_number=?";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->first();
				}
			}
		}

	} // end class
