<?php
	class Dicer_deposit extends Crud{
		protected $_table = 'dicer_deposits';
		public function __construct($dd=null){
			parent::__construct($dd);
		}
		public function deleteP($id) {
			$parameters = array();
			if($id) {
				$parameters[] = $id;

				$q = "Delete from dicer_deposits where id = ? limit 1";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return true;
				}
			}
		}
		public function countRecord($cid, $search = '') {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$whereSearch="";
				if($search){

					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$whereSearch  = " and (b.name like ? or d.deposit_by like ? ) ";
				}
				$q = "Select count(*) as cnt from dicer_deposits d left join branches b on b.id = d.branch_id where d.company_id = ? and d.is_active = 1 $whereSearch ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function get_record($cid, $start, $limit, $search = '') {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				if($limit) {
					$l = " LIMIT $start,$limit";
				} else {
					$l = '';
				}
				$whereSearch="";
				if($search){

					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$whereSearch  = " and (b.name like ? or d.deposit_by like ? ) ";
				}

				$q = "Select d.*, b.name as branch_name from dicer_deposits d left join branches b on b.id = d.branch_id where d.company_id = ? and d.is_active = 1  $whereSearch order by d.id desc $l";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}
		public function getDeposit($branch_id, $date){

			$parameters = array();

			if($branch_id && $date) {
				$parameters[] = $branch_id;
				$parameters[] = $date;
				$q = "select dd.*,from_unixtime(created) from dicer_deposits dd where dd.branch_id = ? and date_format(date_add(from_unixtime(dd.created),INTERVAL -1 DAY),'%Y-%m-%d') = ? limit 1";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->first();
				}
			}

		}

		public function getDicerDepositSummary($cid=0,$dt1=0,$dt2=0,$branch_id){
			if($cid && $dt1 && $dt2 ){
				$parameters = [];
				$parameters[] = $cid;

				if($dt1 && $dt2){
					$dt1 = strtotime($dt1);
					$dt2 = strtotime($dt2 . "1 day -1 sec");
				}

				$whereBranch = "";

				if($branch_id){
					$whereBranch = " and  branch_id = ? ";
					$parameters[]= $branch_id;
				}

			 	$q= "Select sum(amount) as totalamount, DAY(FROM_UNIXTIME(created)) as d from dicer_deposits
 				where company_id= ? and created >= $dt1 and created <= $dt2 $whereBranch group by  DAY(FROM_UNIXTIME(created))";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					return $data->results();
				}
			}
		}

		public function checkDeposit($branch_id, $date,$amount){

			$parameters = array();
			if($branch_id && $date) {

				$parameters[] = $branch_id;
				$parameters[] = $date;
				$parameters[] = $amount;

				$q = "select count(*) as cnt from dicer_deposits  where branch_id = ? and date_format(from_unixtime(created),'%Y-%m-%d') = ? and amount = ? limit 1";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->first();
				}
			}

		}
	}


