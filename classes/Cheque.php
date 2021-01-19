<?php
	class Cheque extends Crud{
		protected $_table = 'cheque';
		public function __construct($c=null){
			parent::__construct($c);
		}
		public function getByPids($pids = ""){

			if($pids){
				$parameters = [];

				$q= 'Select * from cheque  where  payment_id in ('.$pids.') and is_active = 1';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
				return false;
			}
		}
		public function changeStatus($id=0,$val=0){
			$parameters = array();
			if($id && $val) {
				$parameters[] = $val;
				$parameters[] = $id;

				$q = "update cheque set status=? where id=?";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return true;
				}
			}
		}

		public function getBounceCheck($memid=0){
				$parameters = array();
			if($memid) {
				$parameters[] = $memid;
			
				$q = "Select sum(c.amount) as camount from cheque c left join consumable_amount ca on ca.payment_id=c.payment_id  where ca.member_id= ? and c.status=3";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function getBounceList($dt_from , $dt_to){

			$parameters = array();
			$whereDate = "";
			if($dt_from && $dt_to){

				$dt_from = strtotime($dt_from);
				$dt_to = strtotime($dt_to . "1 day -1 min");
				$whereDate = " and ch.payment_date >= $dt_from and ch.payment_date <= $dt_to ";

			}

			$q = "
    					Select
 						ch.*, m.lastname as member_name, s.*, mc.amount_paid,
 						ch2.check_number as check_number2,
 						ch2.bank as bank2,
 						ch2.amount as amount2,
 						ch2.payment_date as payment_date2,
 						ca.amount as cash_amount
 						from cheque ch
 						left join (select payment_id, member_id, invoice,dr,ir from sales group by payment_id) s on s.payment_id = ch.payment_id
 					    left join members m on m.id = s.member_id
 					    left join member_credit mc on mc.ref_check_number = ch.id
 					    left join cheque ch2 on ch2.from_credit = mc.id
 					    left join cash ca on ca.from_credit = mc.id
 						where ch.status=3 $whereDate

 						";

			$data = $this->_db->query($q, $parameters);

			if($data->count()) {
				return $data->results();
			}


		}

		public function countRecord($cid,$search='',$cheque_type = '',$mem_id=0, $dt1=0,$dt2=0,$branch=0,$terminal=0,$sales_type=0,$with_terms=-1){
			$parameters = array();
			if($cid) {
				$wherebranch ='';
				$whereterminal='';
				$whereSalesType = '';

				if($with_terms == -1){
					$whereTerms = '';
				} else if($with_terms == 1){
					$whereTerms = " and m.terms != ''";
				}  else if ($with_terms == 0){
					$whereTerms = " and m.terms = ''";
				}
				if ($branch || $terminal){
					if (!$terminal){
						$tempb='';
						$caravan = "";
						foreach($branch as $b){
							if($b == -1){
								$caravan = "s.terminal_id = 0";
							} else {
								$parameters[] = $b;
								$tempb  .='?,';
							}

						}
						if($tempb){
							$tempb = rtrim($tempb,',');
							if($caravan){
								$caravan = " or $caravan";
							}

							$wherebranch = " and (s.branch_id in ($tempb) $caravan)";
						} else {
							if($caravan){
								$caravan = " and $caravan";
							}
							$wherebranch = $caravan;
						}

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
				$parameters[] = $cid;
				if($cheque_type){
					$parameters[] = $cheque_type;
					$cheque_type_where = " and c.status=? ";
				} else {
					$cheque_type_where='';
				}
				if($search) {
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$likewhere = " and (m.lastname like ? or s.invoice  like ?  or s.sr  like ? or s.dr  like ? or s.ir  like ? or c.check_number  like ? ) ";
				} else {
					$likewhere = "";
				}
				if($mem_id){
					$parameters[] = $mem_id;
					$member_where = " and s.member_id=? ";
				} else {
					$member_where='';
				}
				if($dt1 && $dt2){
					$dt1 = strtotime($dt1);
					$dt2= strtotime($dt2);
					$whereDT = " and c.payment_date >= $dt1 and c.payment_date <= $dt2";
				} else {
					$whereDT = "";
				}


				$q = "Select count(c.id) as cnt
						from cheque c
						left join payments p on p.id=c.payment_id
						left join (select s.member_id,s.payment_id,s.invoice,s.dr,s.terminal_id,b.id as branch_id, s.sales_type,s.ir,s.sr
										from sales s
										left join terminals t on t.id = s.terminal_id
										left join branches b on b.id=t.branch_id where 1=1
										group by s.payment_id) s on s.payment_id=p.id
						left join members m on m.id = s.member_id
						where 1=1 $wherebranch $whereterminal $whereSalesType and p.company_id=?  $likewhere $cheque_type_where $member_where $whereDT $whereTerms";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function get_cheque_record($cid,$start,$limit,$search='',$cheque_type='',$mem_id=0, $dt1=0,$dt2=0,$branch=0,$terminal=0,$sales_type=0,$with_terms=-1){
			$parameters = array();
			if($cid){
				$wherebranch ='';
				$whereterminal='';
				$whereSalesType='';
				if($with_terms == -1){
					$whereTerms = '';
				} else if($with_terms == 1){
					$whereTerms = " and m.terms != ''";
				}  else if ($with_terms == 0){
					$whereTerms = " and m.terms = ''";
				}
				if ($branch || $terminal){
					if (!$terminal){
						$tempb='';
						$caravan = "";
						foreach($branch as $b){
							if($b == -1){
								$caravan = "s.terminal_id = 0";
							} else {
								$parameters[] = $b;
								$tempb  .='?,';
							}

						}
						if($tempb){
							$tempb = rtrim($tempb,',');
							if($caravan){
								$caravan = " or $caravan";
							}

							$wherebranch = " and (t.branch_id in ($tempb) $caravan)";
						} else {
							if($caravan){
								$caravan = " and $caravan";
							}
							$wherebranch = $caravan;
						}
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

				$parameters[] = $cid;
				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}
				if($cheque_type){
					$parameters[] = $cheque_type;
					$cheque_type_where = " and c.status=? ";
				} else {
					$cheque_type_where='';
				}
				if($search){
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$likewhere = " and (m.lastname like ? or s.invoice  like ?  or s.sr  like ? or s.dr  like ? or s.ir  like ? or c.check_number  like ? ) ";
				} else {
					$likewhere='';
				}
				if($mem_id){
					$parameters[] = $mem_id;
					$member_where = " and s.member_id=? ";
				} else {
					$member_where='';
				}
				if($dt1 && $dt2){
					$dt1 = strtotime($dt1);
					$dt2= strtotime($dt2);
					$whereDT = " and c.payment_date >= $dt1 and c.payment_date <= $dt2";
				} else {
					$whereDT = "";
				}
				$q = "Select u.firstname as ufn, u.lastname as uln, sts.name as station_name,wh.remarks as wh_remarks, c.* ,s.invoice,s.dr,m.terms, m.lastname as mln,sts.name as salestype_name, s.sold_date,s.ir,s.sr from cheque c
				left join payments p on p.id=c.payment_id
				left join (select user_id,remarks,payment_id from wh_orders) wh on wh.payment_id = c.payment_id
				left join (select sold_date,member_id,payment_id,invoice,dr,terminal_id,sales_type,ir,sr,station_id from sales where status = 0 group by payment_id ) s on s.payment_id = c.payment_id
				left join terminals t on t.id = s.terminal_id
				left join branches b on b.id=t.branch_id
				left join stations st on st.id = s.station_id
				left join members m on m.id = s.member_id
				left join salestypes sts on sts.id=s.sales_type
				left join users u on u.id = wh.user_id
				where 1=1 $wherebranch $whereterminal $whereSalesType and p.company_id=? and p.is_active=1 $likewhere $cheque_type_where $member_where $whereDT $whereTerms
				order by c.payment_id desc $l
				";
				 /*$q= "Select u.firstname as ufn, u.lastname as uln, s.station_name,wh.remarks as wh_remarks, c.* ,s.invoice,s.dr,m.terms, m.lastname as mln,st.name as salestype_name, s.sold_date,s.ir,s.sr
					from cheque c
					left join payments p on p.id=c.payment_id
					left join wh_orders wh on wh.payment_id  = c.payment_id
					left join (select s.sold_date,s.member_id,s.payment_id,s.invoice,s.dr,s.terminal_id,b.id as branch_id,s.sales_type,s.ir,s.sr , st.name as station_name
							from sales s left join terminals t on t.id = s.terminal_id
							left join branches b on b.id=t.branch_id
							left join stations st on st.id = s.station_id
							where 1=1  group by s.payment_id) s on s.payment_id=p.id
							left join members m on m.id = s.member_id
							left join salestypes st on st.id=s.sales_type
							left join users u on u.id = wh.user_id
							where 1=1 $wherebranch $whereterminal $whereSalesType and p.company_id=? and p.is_active=1 $likewhere $cheque_type_where $member_where $whereDT $whereTerms order by c.payment_id desc $l  "; */
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}

		}

		public function getChequeBaseOnPayment($p=0){
			$parameters = array();
			if($p){
				$parameters[] = $p;
				$q= "Select *  from cheque where payment_id=? and  status=1 and is_active=1";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function getMemberCheque($p=0){
			$parameters = array();
			if($p){
				$parameters[] = $p;
				$q= "Select *  from cheque where payment_id=? and is_active=1";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function getMemberChequeByPaymentID($id=0){
			if($id){
				$parameters = [];
				$parameters[] = $id;
				$q= "Select  * from cheque  where payment_id = ? ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}

			}
		}


		public function countRecord2($cid,$search='', $dt1=0,$dt2=0){
			$parameters = array();
			if($cid) {

				$parameters[] = $cid;

				if($search) {
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$likewhere = " and (m.lastname like ? or s.invoice  like ?  or s.sr  like ? or s.dr  like ? or s.ir  like ? or c.check_number  like ? ) ";
				} else {
					$likewhere = "";
				}

				if($dt1 && $dt2){
					$dt1 = strtotime($dt1);
					$dt2= strtotime($dt2);
					$whereDT = " and c.payment_date >= $dt1 and c.payment_date <= $dt2";
				} else {
					$whereDT = "";
				}


				$q = "Select count(c.id) as cnt from cheque c left join payments p on p.id=c.payment_id left join (select s.member_id,s.payment_id,s.invoice,s.dr,s.terminal_id,b.id as branch_id, s.sales_type,s.ir,s.sr from sales s left join terminals t on t.id = s.terminal_id left join branches b on b.id=t.branch_id where 1=1  group by s.payment_id) s on s.payment_id=p.id left join members m on m.id = s.member_id where 1=1   and p.company_id=?  $likewhere   $whereDT ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function get_cheque_record2($cid,$start,$limit,$search='', $dt1=0,$dt2=0){
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
					$parameters[] = "%$search%";
					$likewhere = " and (m.lastname like ? or s.invoice  like ?  or s.sr  like ? or s.dr  like ? or s.ir  like ? or c.check_number  like ? ) ";
				} else {
					$likewhere='';
				}

				if($dt1 && $dt2){
					$dt1 = strtotime($dt1);
					$dt2= strtotime($dt2);
					$whereDT = " and c.payment_date >= $dt1 and c.payment_date <= $dt2";
				} else {
					$whereDT = "";
				}

				$q= "Select u.firstname as ufn, u.lastname as uln, s.station_name,wh.remarks as wh_remarks, c.* ,s.invoice,s.dr,m.terms, m.lastname as mln,st.name as salestype_name, s.sold_date,s.ir,s.sr  from cheque c left join payments p on p.id=c.payment_id left join wh_orders wh on wh.payment_id  = c.payment_id left join (select s.sold_date,s.member_id,s.payment_id,s.invoice,s.dr,s.terminal_id,b.id as branch_id,s.sales_type,s.ir,s.sr , st.name as station_name from sales s left join terminals t on t.id = s.terminal_id left join branches b on b.id=t.branch_id left join stations st on st.id = s.station_id where 1=1  group by s.payment_id) s on s.payment_id=p.id left join members m on m.id = s.member_id left join salestypes st on st.id=s.sales_type  left join users u on u.id = wh.user_id where 1=1   and p.company_id=? and p.is_active=1 $likewhere   $whereDT  order by c.payment_id desc $l  ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}

		}
		public function paginate2($cid,$args){
			// pages,
			$user = new User();

			$search = Input::get('search');
			$mem_id = Input::get('member_id');
			$dt1 = Input::get('dt1');
			$dt2 = Input::get('dt2');

			?>

			<?php
			//$targetpage = "paging.php";
			if($search){
				$limit = 200;
			} else {
				$limit = 100;
			}


			$countRecord = $this->countRecord2($cid, $search, $dt1,$dt2);

			$total_pages = $countRecord->cnt;

			$stages = 3;
			$page = ($args);
			$page = (int)$page;
			if($page) {
				$start = ($page - 1) * $limit;
			} else {
				$start = 0;
			}

			$company_inv = $this->get_cheque_record2($cid, $start, $limit, $search,  $dt1,$dt2);
			getpagenavigation($page, $total_pages, $limit, $stages);
			if($company_inv) {
				?>
				<div id="no-more-tables">
					<div class="table-responsive">
						<table class='table table-bordered table-condensed' id='tblSales'>
							<thead>
							<tr>



								<TH>Client</TH>
								<TH><?php echo INVOICE_LABEL; ?></TH>
								<TH><?php echo DR_LABEL; ?></TH>
								<TH><?php echo PR_LABEL; ?></TH>
								<th></th>
								<th></th>
							</tr>
							</thead>
							<tbody>
							<?php
								$sales = new Sales();
								$prevPayment = '';
								$total_check = 0;
								foreach($company_inv as $s) {

									?>
									<tr>
										<td  style='border-top: 1px solid #ccc;'><?php echo $s->mln; ?></td>
										<td  style='border-top: 1px solid #ccc;' data-title='Invoice'>
											<?php echo $s->invoice; ?>
										</td>
										<td  style='border-top: 1px solid #ccc;' data-title='Invoice'>
											<?php echo $s->dr; ?>
										</td>
										<td  style='border-top: 1px solid #ccc;' data-title='Invoice'>
											<?php echo $s->ir; ?>
										</td>
										<td  style='border-top: 1px solid #ccc;' data-title='Invoice'>
											<?php echo $s->amount; ?>
										</td>
										<td  style='border-top: 1px solid #ccc;'></td>
									</tr>
									<?php
								}
							?>
							</tbody>
						</table>
					</div>
				</div>

				<?php
			} else {
				?>
				<h3><span class='label label-info'>No Record Found...</span></h3>

				<?php
			}

		}


		public function getPostDated($cid,$branch_id = 0){
			$parameters = array();
			if($cid){
				$parameters[] = $cid;
					$dt1 = time();
					$whereDT = " and c.payment_date >= $dt1 ";
					$whereBranch="";
					if($branch_id){
						$parameters[] = $branch_id;
						$whereBranch = " and t.branch_id = ? ";
					}

				 $q = "
					Select b.name as branch_name, u.firstname as ufn, u.lastname as uln, sts.name as station_name,wh.remarks as wh_remarks, c.* ,s.invoice,s.dr,m.terms, m.lastname as mln,sts.name as salestype_name, s.sold_date,s.ir,s.sr from cheque c
					left join payments p on p.id=c.payment_id
					left join (select user_id,remarks,payment_id from wh_orders) wh on wh.payment_id = c.payment_id
					left join (select sold_date,member_id,payment_id,invoice,dr,terminal_id,sales_type,ir,sr,station_id,status from sales where status = 0 group by payment_id ) s on s.payment_id = c.payment_id
					left join terminals t on t.id = s.terminal_id
					left join branches b on b.id=t.branch_id
					left join stations st on st.id = s.station_id
					left join members m on m.id = s.member_id
					left join salestypes sts on sts.id=s.sales_type
					left join users u on u.id = wh.user_id
					where 1=1  and p.company_id=? and p.is_active=1 and s.status = 0 $whereDT  $whereBranch
					order by c.payment_id desc
				";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
				return false;
			}

		}
	}
