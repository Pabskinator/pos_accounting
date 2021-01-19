<?php
	class Pettycash_request extends Crud{
		protected $_table = 'pettycash_request';
		public function __construct($p=null){
			parent::__construct($p);
		}
		public function countRecord($cid,$status=1,$branch_id=0){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				if($branch_id){
					$parameters[] = $branch_id;
					$whereBranch= " and p.branch_id=?";
				} else {
					$whereBranch= "";
				}
				$parameters[] = $status;
				$whereStatus = " and p.status=?";

				$q = "Select count(p.id) as cnt from pettycash_request p where p.is_active=1 and p.company_id=? $whereBranch $whereStatus";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function get_record($cid,$start,$limit,$status=1,$branch_id=0){
			$parameters = array();
			if($cid){
				$parameters[] = $cid;
				if($branch_id){
					$parameters[] = $branch_id;
					$whereBranch= " and p.branch_id=?";
				} else {
					$whereBranch= "";
				}
				$parameters[] = $status;
				$whereStatus = " and p.status=?";
				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}


				$q = "Select p.*, b.name as branch_name , u.lastname, u.firstname,u.middlename from pettycash_request p left join branches b on b.id=p.branch_id left join users u on u.id=p.user_id where p.is_active=1 and  p.company_id=? $whereBranch $whereStatus order by p.id desc $l";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
	}
?>