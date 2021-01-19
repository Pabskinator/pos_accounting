<?php
	class Branch extends Crud{
		protected $_table = 'branches';
		public function __construct($branch=null){
			parent::__construct($branch);
		}

		public function branchMember($branch_id){
			if($branch_id) {
				$parameters = array();
				$parameters[] = $branch_id;

				$q = "select b.id, b.name, m.lastname, b.member_id, m.k_type from branches b left join members m on m.id=b.member_id where b.id = ?";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function canOrderTo($in_id =''){
			if($in_id) {
				$parameters = array();


				$q = "Select * from branches where branch_tag in ($in_id)";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function getMemberBranch($member_id=0){
			if($member_id) {
				$parameters = array();
				$parameters[] = $member_id;

				$q = "Select id,name from branches where is_active = 1 and member_id = ? ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function branchJSON($cid = 0 , $search = ''){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$whereSearch = '';
				if($search){
					$parameters[] = "%$search%";
					$whereSearch = "and name like ? ";
				}

				$q = "Select * from branches where is_active = 1 and company_id = ? $whereSearch order by name";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function isBranchExist($name='',$companyid=0,$getid=false){
			$parameters = array();
			if($name){
				$parameters[] = $name;
				$parameters[] =$companyid;
				$q= 'Select id from branches  where  name=? and is_active=1 and company_id=?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return ($getid) ? $e->first() : true;
				}
				return false;
			}
		}
		public function countBranch($companyid=0){
			$parameters = array();
			if($companyid){
				$parameters[] =$companyid;
				$q= 'Select count(id) as cnt from branches  where  is_active=1 and company_id=?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->first();
				}
			}
		}
		public function branchIn($bid=0){
			$parameters = array();
			if($bid){
				$bid = explode(',',$bid);
				$l = "";
				foreach($bid as $b){
					$parameters[] = $b;
					$l .= "?,";
				}

				$l =rtrim($l,",");

				 $q= "Select `name` from branches  where  id in ($l)";
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
			}
		}

	}
?>