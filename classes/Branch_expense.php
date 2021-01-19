<?php
	class Branch_expense extends Crud{
		protected $_table = 'branch_expenses';
		public function __construct($branch=null){
			parent::__construct($branch);
		}

		public function getExpenseSummary($cid=0,$month=0,$year=0,$branch_id = 0){
			if($cid && $month && $year ){
				$parameters = [];
				$parameters[] = $cid;
				$parameters[] = $month;
				$parameters[] = $year;
				$whereBranch = "";
				if($branch_id){
					$whereBranch = " and branch_id = ? ";
					$parameters[] = $branch_id;
				}

				$q= "Select  DAY(FROM_UNIXTIME(created)) as d, sum(amount) as totalamount from branch_expenses  where company_id = ? and MONTH(FROM_UNIXTIME(created)) = ? and YEAR(FROM_UNIXTIME(created)) = ?   $whereBranch group by DAY(FROM_UNIXTIME(created)) order by  DAY(FROM_UNIXTIME(created))";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function getPending($branch_id = 0,$dt=''){


			if($branch_id ) {
				$parameters = array();
				$parameters[] = $branch_id;
				$parameters[] = $dt;

				$q = "Select id,description,amount from branch_expenses where branch_id = ?  and status = 0 and date_format(from_unixtime(created),'%Y-%m-%d') = ? ";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function updatePending($branch_id = 0,$ref_id=0,$dt=''){


			if($branch_id ) {
				$parameters = array();
				$parameters[] = $ref_id;
				$parameters[] = $branch_id;
				$parameters[] = $dt;

				$q = "update branch_expenses set status = 1 , ref_id = ? where branch_id = ?  and status = 0 and date_format(from_unixtime(created),'%Y-%m-%d') = ? ";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return true;
				}
			}
		}

		public function getTotalExpense($ref_id=0){


			if($ref_id ) {
				$parameters = array();
				$parameters[] = $ref_id;

				$q = "select sum(amount) as amt  from branch_expenses where  ref_id = ? ";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

	}
?>