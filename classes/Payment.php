<?php
	class Payment extends Crud implements PagingInterface{
		protected $_table = 'payments';
		public function __construct($p=null){
			parent::__construct($p);
		}
		public function getCash($p=0){
			$parameters = array();
			$parameters[] = $p;
			$q= "Select * from cash where payment_id = ?";
			$e = $this->_db->query($q, $parameters);
			if($e->count()){
				return $e->results();
			}
			return false;
		}
		public function getCreditCard($p=0){
			$parameters = array();
			$parameters[] = $p;
			$q= "Select * from credit_card where payment_id = ?";
			$e = $this->_db->query($q, $parameters);
			if($e->count()){
				return $e->results();
			}
			return false;
		}
		public function getCheque($p=0){
			$parameters = array();
			$parameters[] = $p;
			$q= "Select * from cheque where payment_id = ?";
			$e = $this->_db->query($q, $parameters);
			if($e->count()){
				return $e->results();
			}
			return false;
		}
		public function getBT($p=0){
			$parameters = array();
			$parameters[] = $p;
			$q= "Select * from bank_transfer where payment_id = ?";
			$e = $this->_db->query($q, $parameters);
			if($e->count()){
				return $e->results();
			}
			return false;
		}
		public function getConsumable($p=0){
			$parameters = array();
			$parameters[] = $p;
			$q= "Select * from payment_consumable where payment_id = ?";
			$e = $this->_db->query($q, $parameters);
			if($e->count()){
				return $e->results();
			}
			return false;
		}
		public function getConsumableFreebies($p=0){
			$parameters = array();
			$parameters[] = $p;
			$q= "Select * from payment_consumable_freebies where payment_id = ?";
			$e = $this->_db->query($q, $parameters);
			if($e->count()){
				return $e->results();
			}
			return false;
		}
		public function getDeduction($p=0){
			$parameters = array();
			$parameters[] = $p;
			$q= "Select * from deductions where payment_id = ?";
			$e = $this->_db->query($q, $parameters);
			if($e->count()){
				return $e->results();
			}
			return false;
		}

		public function getCRSum($cr = ''){
			$cr= trim($cr);
			$parameters = array();


			 $q = "Select cl.cr_number, sum(cl.receipt_amount) as receipt_amount, sum(cl.freight) as freight,
                  sum(cl.deduction) as deduction,sum(cl.paid_amount) as paid_amount, crep.override_cr_date
                  from cr_log cl
                  left join (Select * from collection_reports group by ref_id) crep on crep.ref_id = cl.cr_number
	              where TRIM(cl.cr_number) = '$cr' ";

			$e = $this->_db->query($q, $parameters);
			if($e->count()){
				return $e->first();
			}
			return false;

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
 					IFNULL(cr_sum.freight,0) as freight,
 					IFNULL(cr_sum.deduction,0) as deduction, ss.branch_id
 					 from payments p
					  left join (Select * from collection_reports group by ref_id) crep on crep.ref_id = p.cr_number
 					 left join
 					  (
	                      Select cr_number, sum(receipt_amount) as receipt_amount,
	                      sum(deduction) as deduction,sum(paid_amount) as paid_amount,sum(freight) as freight
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
		public function getByCr($cr_number =''){

			if($cr_number){
				$parameters = array();
				$parameters[] = "%$cr_number%";
				$q= "Select * from payments where cr_number like ? ";
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
			}
			return false;
		}

		public function getAllPayment($dt1=0,$dt2=0,$salestype=0,$terminal_id=0,$user_id=0,$cr_num=0,$from_service=0,$paid_by=0,$show_with_cr=0,$cr_include_dr='',$cr_include_ir='',$agent_id=0){

				$parameters = array();

					$where_dt= "";
					$where_salestype= "";
					$where_terminal= "";
					$where_user= "";
					$where_crnum ='';
					$wheremcdate ='';
					$whereInclude ='';

					if($from_service){
						$where_svc = " and (CASE WHEN wh.id IS NULL THEN s.is_service != 0 ELSE wh.from_service != 0  END) ";
					} else {
						$where_svc = " and CASE WHEN wh.id IS NOT NULL  THEN wh.from_service = 0 ELSE s.is_service =0 END";
					}

					if($dt1 && $dt2){

						//$where_dt= " and ((sp.sold_date >= $dt1 and sp.sold_date <= $dt2) or( mc.modified >= $dt1 and mc.modified <= $dt2)) ";
						$where_dt= " and ((s.sold_date >= $dt1 and s.sold_date <= $dt2) or ( mc.modified >= $dt1 and mc.modified <= $dt2) ) ";
						$wheremcdate = " and modified >= $dt1 and modified <= $dt2 ";

					}


					if($terminal_id){
						$parameters[] = $terminal_id;
						$where_terminal= " and s.terminal_id = ? ";
					}

					if($user_id){
						$user_id = (int) $user_id;
						$where_user= " and s.cashier_id in ($user_id) ";
					}

					if($salestype && is_array($salestype)){
						$stholder="";
						foreach($salestype as $st_id){
							$st_id = (int) $st_id;
							$stholder .= "$st_id,";
						}
						$stholder = rtrim($stholder,',');

						$where_salestype = " and s.sales_type in ($stholder) ";
					}

					$wherePaidBy = "";

					if($paid_by){
						$lpaid = "";
						$explode_paid = explode(",",$paid_by);
						$extra_paidby_clause="";
						foreach($explode_paid as $ep){
							$ep = (int) $ep;
							$extra_paidby_clause .= "CONCAT( ',', mc.user_ids, ',' ) LIKE '%,$ep,%' or ";

							$lpaid .= "$ep,";
						}
						$extra_paidby_clause = "or (" . rtrim($extra_paidby_clause,'or ') . ")";
						$lpaid = rtrim($lpaid,',');
						$wherePaidBy = " and CASE WHEN mc.id IS NULL THEN s.cashier_id in ($lpaid) ELSE mc.paid_by in ($lpaid) $extra_paidby_clause END";
					}


					if(!$cr_num){
						if(!$show_with_cr){
							$where_crnum= " and pp.cr_number = '' ";
						}
					} else {
						$parameters[] = "%,$cr_num,%";
						$where_crnum= "  and CONCAT( ',', pp.cr_number, ',' ) LIKE ? ";
					}

					$where_agent="";
					if($agent_id){
						$agent_id = (int) $agent_id;
						$where_agent = "and  CONCAT( ',', m.agent_id, ',' ) LIKE '%,$agent_id,%'";
					}

					if($cr_include_dr || $cr_include_ir){
						$wheredr = "";
						$whereir = "";
						if(strpos($cr_include_dr,",") > 0){
							$ex_dr = explode(",",$cr_include_dr);
							$tempdr = "";
							foreach($ex_dr as $ex){
								$ex = (int) $ex;
								$tempdr .= "'$ex',";
							}
							$tempdr = rtrim($tempdr,",");
							$wheredr = " s.dr in ($tempdr) and pp.cr_number = '' " ;
						} else if($cr_include_dr) {
							$cr_include_dr = (int) $cr_include_dr;
							$wheredr = "  s.dr='$cr_include_dr' and pp.cr_number = '' ";
						}

						if(strpos($cr_include_ir,",") > 0){
							$ex_ir = explode(",",$cr_include_ir);
							$tempir = "";
							foreach($ex_ir as $ex){
								$ex = (int) $ex;
								$tempir .= "'$ex',";
							}
							$tempir = rtrim($tempir,",");
							$whereir = " s.ir in ($tempir) and pp.cr_number = '' " ;
						} else if($cr_include_ir) {
							$cr_include_ir = (int) $cr_include_ir;
							$whereir = " s.ir = '$cr_include_ir'  and pp.cr_number = '' ";
						}

						if($wheredr && $whereir){
							$whereAll = "and ($wheredr or $whereir)";
						} else if ($wheredr && !$whereir){
							$whereAll = "and ($wheredr)";
						} else if ($whereir && !$wheredr){
							$whereAll = "and ($whereir)";
						}


					} else {
						$whereAll = " $where_dt $where_salestype $where_user  $where_terminal $where_crnum $where_svc $wherePaidBy  $where_agent";
					}


					   $q = "Select
						freight.freight_charge,pp.*,cat.name as category_name,cat.id as category_id, s.from_od,s.payment_id,s.invoice,s.dr,s.ir,s.member_id,
						sum(((s.qtys * p.price) + s.adjustment + s.member_adjustment)- (s.discount + s.store_discount)) as stotal,
						s.sold_date ,m.lastname,m.terms,wh.is_scheduled, wh.from_service
						from sales s
						left join prices p on s.price_id = p.id
						left join (Select id, category_id from items) i on i.id = s.item_id
						left join categories cat on cat.id = i.category_id
						left join (select id,payment_id, modified, user_id as paid_by, user_ids from member_credit where 1=1 $wheremcdate ) mc on mc.payment_id = s.payment_id
						left join payments pp on pp.id = s.payment_id
						left join (Select id, terms,lastname,agent_id from members) m on m.id = s.member_id
						left join (Select id, from_service, is_scheduled,payment_id from wh_orders) wh on wh.payment_id = s.payment_id
						left join (select sum(charge + freight_adjustment) as freight_charge, payment_id from freight_charges group by payment_id) freight on freight.payment_id = s.payment_id
						where s.status=0 $whereAll
						group by s.payment_id having stotal != 0 order by lastname asc";

					$e = $this->_db->query($q, $parameters);
					if($e->count()){
						return $e->results();
					}
					return false;

		}
		public function updateCROfPayment($cr_num='',$payment_ids){
			if($payment_ids){
				$parameters = array();
				$parameters[] =  trim($cr_num);


				$payment_ids = addslashes($payment_ids);
				$explodeids = explode(",",$payment_ids);
				$lid = "";
				foreach($explodeids as $id){
					$id = (int) $id;
					$lid .= $id.",";
				}
				$lid = rtrim($lid,",");
				$now = time();

				 $q= "update payments set cr_number = ? , cr_date=$now where id in ($lid)";
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
				return false;
			}
		}

		public function updateCrDate($cr_num='',$cr_date=''){
			if($cr_num){
				$parameters = array();

				$parameters[] =strtotime($cr_date);

				$parameters[] = trim($cr_num);

				$q= "update payments set cr_date = ? where cr_number=?";
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return true;
				}
				return false;
			}
		}

		public function updateCrDateOverride($cr_num='',$cr_date=''){
			if($cr_num){
				$parameters = array();

				$parameters[] =strtotime($cr_date);
				$parameters[] = trim($cr_num);
				$q= "update collection_reports set override_cr_date = ? where ref_id=?";
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return true;
				}
				return false;
			}
		}

		public function getSalesByDoc($member_id=0,$type=0,$ctrl_num=0){
			$parameters = array();
			$parameters[] = $member_id;
			$parameters[] = $ctrl_num;
			if($type == 1){
				$whereDoc = "and invoice = ?";
			} else if ($type == 2){
				$whereDoc = "and dr = ?";
			}else if ($type == 3){
				$whereDoc = "and ir = ?";
			}else if ($type == 4){
				$whereDoc = "and sr = ?";
			}else if ($type == 5){
				$whereDoc = "and ts = ?";
			}
			$q= "Select count(*) as cnt from sales where member_id = ?  and status=0 $whereDoc";
			$e = $this->_db->query($q, $parameters);
			if($e->count()){
				return $e->first();
			}
			return false;
		}


		public function getCollection($type_id,$dt_from,$dt_to,$agent_id=0){
			$parameters = array();


				$whereAgent = "";
				$whereDate = "";
				$whereType = "";

				if($dt_from && $dt_to){
					$dt_from = strtotime($dt_from);
					$dt_to = strtotime($dt_to . "1 day -1 min");
					$whereDate = "and p.cr_date >= $dt_from and p.cr_date <= $dt_to ";
				}
				if($agent_id){
					$whereAgent = " and CONCAT( ',', m.agent_id, ',' ) LIKE '%,$agent_id,%'";
				}
				if($type_id){
					$whereType = " and  m.salestype = $type_id ";
				}


				 $q= "SELECT  m.salestype,sum(IFNULL(c.receipt_amount,0)) as receipt_amount,sum(IFNULL(c.deduction,0)) as deduction, sum(IFNULL(c.paid_amount,0)) as paid_amount, st.name as sales_type_name, c.cr_number,p.cr_date
						FROM `cr_log` c
						left join (select id , cr_number , cr_date from payments p group by  cr_number) p on p.cr_number = c.cr_number
						left join members m on TRIM(m.lastname)  = TRIM(c.client_name)
						left join salestypes st on st.id = m.salestype
						where 1=1 $whereType $whereDate $whereAgent
						group by c.cr_number, m.salestype
						order by m.salestype, c.cr_number asc  limit 0,5000";
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}

			return false;


		}

		public function countRecord($type_id,$dt_from,$dt_to){


		}
		public function getPageNavigation($page, $total_pages, $limit, $stages) {
			getpagenavigation($page, $total_pages, $limit, $stages);
		}

		public function paginate($cid, $args) {
			$sales_type_id = Input::get('sales_type_id');
			$dt_from = Input::get('date_from');
			$dt_to = Input::get('date_to');
			$agent_id = Input::get('agent_id');

			if(!($dt_to && $dt_from)){
				$dt_from = date('m/01/Y');
				$dt_to = date('m/d/Y', strtotime($dt_from . "1 month -1 min"));
			}



			$sales = $this->getCollection($sales_type_id,$dt_from,$dt_to,$agent_id);
			echo "<p>Date <strong>".$dt_from."</strong> - <strong>".$dt_to."</strong></p>";
			if($sales){
				$total = 0;

				echo "<table id='tblForApproval' class='table table-bordered table-border-top'>";
				echo "<thead>";
				echo "<tr><th>Type</th><th>Cr Number</th><th>Date</th><th>Receipt Amount</th><th>Deduction</th><th>Paid Amount</th></tr>";
				echo "</thead>";
				echo "<tbody>";
				$prev = '';
				$total_type_paid = 0;
				$total_type_receipt = 0;
				$total_type_deduct = 0;
				foreach($sales as $s){
					$total += $s->paid_amount;

					$s->sales_type_name = $s->sales_type_name ? $s->sales_type_name : 'No Type';
					$type = $s->sales_type_name;
					if($type == $prev){
						$type ='';
						$total_type_paid += $s->paid_amount;
						$total_type_receipt += $s->receipt_amount;
						$total_type_deduct += $s->deduction;
					}else {

						if($prev){
							echo "<tr>";
							echo "<td>Total $prev</td>";
							echo "<td></td>";
							echo "<td></td>";
							echo "<td class='text-right'><strong>".number_format($total_type_receipt,2)."</strong></td>";
							echo "<td  class='text-right'><strong>".number_format($total_type_deduct,2)."</strong></td>";
							echo "<td  class='text-right'><strong>".number_format($total_type_paid,2)."</strong></td>";

							echo "</tr>";
						}

						$total_type_paid = $s->paid_amount;
						$total_type_receipt = $s->receipt_amount;
						$total_type_deduct = $s->deduction;

					}
					$prev = $s->sales_type_name;

					echo "<tr>";
					echo "<td>".$type."</td>";
					echo "<td  class='text-right'>".$s->cr_number."</td>";
					echo "<td>".date('m/d/Y',$s->cr_date)."</td>";
					echo "<td  class='text-right'>".$s->receipt_amount."</td>";
					echo "<td  class='text-right'>".$s->deduction."</td>";
					echo "<td  class='text-right'>".$s->paid_amount."</td>";

					echo "</tr>";
				}
				if($total_type_paid){
					echo "<tr>";
					echo "<td>Total $prev</td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td  class='text-right'><strong>".number_format($total_type_receipt,2)."</strong></td>";
					echo "<td  class='text-right'><strong>".number_format($total_type_deduct,2)."</strong></td>";
					echo "<td  class='text-right'><strong>".number_format($total_type_paid,2)."</strong></td>";

					echo "</tr>";
				}

				echo "</tbody>";
				echo "</table>";

			} else {
				echo "<div class='alert alert-info'>No record found.</div>";
			}


		}
	}



