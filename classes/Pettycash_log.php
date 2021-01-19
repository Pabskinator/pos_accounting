<?php
	class Pettycash_log extends Crud{
		protected $_table = 'pettycash_log';
		public function __construct($p=null){
			parent::__construct($p);
		}
		public function countRecord($cid,$search='',$branch_id=0){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;

				if($branch_id){
					$parameters[] = $branch_id;
					$whereBranch= " and p.branch_id=?";
				} else {
					$whereBranch= "";
				}


				$q = "Select count(p.id) as cnt from pettycash_log p where p.company_id=? $whereBranch";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function get_record($cid,$start,$limit,$search='',$branch_id=0){
			$parameters = array();
			if($cid){
				$parameters[] = $cid;
				if($branch_id){
					$parameters[] = $branch_id;
					$whereBranch= " and p.branch_id=?";
				} else {
					$whereBranch= "";
				}
				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}


				$q = "Select p.*, b.name as branch_name , u.lastname, u.firstname, u.middlename from pettycash_log p left join branches b on b.id=p.branch_id left join users u on u.id = p.user_id where p.company_id=? $whereBranch order by p.id desc $l";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
	}
?>