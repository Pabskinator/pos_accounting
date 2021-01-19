<?php
	class Bad_order extends Crud{
		protected $_table = 'bad_orders';

		public function    __construct($b=null){
			parent::__construct($b);
		}

		public function getForApproval($cid = 0,$status = 1) {
			$parameters = array();
			$parameters[] = $cid;
			$parameters[] = $status;
			$q = 'Select s.*,b.name as bname,b.address as baddress from bad_orders s left join branches b on s.branch_id=b.id  where  s.company_id = ? and s.status =? and from_dicer=0 ';
			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				// return the data if exists
				return $data->results();
			}
		}

		public function getBOTotal($rid = 0) {
			$parameters = array();
			$parameters[] = $rid;

			$q = 'Select total_bo from bad_orders   where  supplier_order_id = ? and from_dicer = 1 ';
			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				// return the data if exists
				return $data->first();
			}
		}

		public function getBadOrderSummary($cid=0,$dt1=0,$dt2=0,$branch_id){
			if($cid && $dt1 && $dt2 ){
				$parameters = [];
				$parameters[] = $cid;

				$dt1 = strtotime($dt1);
				$dt2 = strtotime($dt2 . "1 day -1 sec");
				$whereBranch = "";

				if($branch_id){
					$whereBranch = " and  branch_id = ? ";
					$parameters[]= $branch_id;
				}

				$q= "Select sum(total_bo) as totalamount, DAY(FROM_UNIXTIME(created)) as d from bad_orders
 				where company_id= ? and created >= $dt1 and created <= $dt2 $whereBranch group by  DAY(FROM_UNIXTIME(created))";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function badorderDicer($dt1=0,$dt2=0,$branch_id){
			if($dt1 && $dt2 ){
				$parameters = [];

				$whereBranch = "";

				if($branch_id){
					$whereBranch = " and  b.branch_id = ? ";
					$parameters[]= $branch_id;
				}

				$q= "Select b.*, bd.item_id,bd.qty
 				from bad_orders b
				left join bad_order_details bd on bd.bad_order_id = b.id
 				where b.company_id= ? and b.created >= $dt1 and b.created <= $dt2 $whereBranch";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function deleteBadorder($dt1=0,$dt2=0,$branch_id){
			if($dt1 && $dt2 && $branch_id){
				$parameters = [];



				$whereBranch = " and branch_id = ? ";
				$parameters[]= $branch_id;


				$q= "Delete from bad_orders where 1=1 $whereBranch and created >= $dt1 and created <= $dt2";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}
	}
