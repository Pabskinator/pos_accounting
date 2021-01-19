<?php
	class Agent_request extends Crud{
		protected $_table = 'agent_request';
		public function __construct($agent_request=null){
			parent::__construct($agent_request);
		}

		public function countRecord($cid,$search='',$status=0,$b=0,$user_id=0){
			$parameters = array();
			if ($cid) {


				$parameters[] = $cid;
				$this->where("o.company_id=? and o.is_active=1");

				if($search) {
					$parameters[] = "%$search%";
					$this->where("and o.id like ?");
				}

				if($b) {
					$parameters[] = $b;
					$this->where("and o.branch_id=?");
				}
				if($status) {
					$parameters[] = $status;
					$this->where("and o.status=?");
				}
				if($user_id){
					$parameters[] = $user_id;
					$this->where(" and o.user_id=? ");
				}

				return $this->select("count(o.id) as cnt")
					->from("agent_request o")
					->get($parameters)
					->first();

			}
		}
		public function get_request_record($cid,$start,$limit,$search='',$b=0,$status=0,$user_id=0){

			$parameters = array();
			if ($cid) {


				$parameters[] = $cid;

				$this->where("o.company_id=? and o.is_active=1");

				if($limit){
					$this->limitBy("$start,$limit");
				}

				if($search) {
					$parameters[] = "%$search%";
					$this->where("and o.id like ?");
				}

				if($b) {
					$parameters[] = $b;
					$this->where(" and o.branch_id=? ");
				}

				if($status) {
					$parameters[] = $status;
					$this->where(" and o.status=? ");
				}

				if($user_id){
					$parameters[] = $user_id;
					$this->where(" and o.user_id=? ");
				}
				return $this->select("o.*")
					->from("agent_request o")
					->get($parameters)
					->all();

			}
		}
		public function countPending($cid){
			$parameters = array();
			if ($cid) {

				$parameters[] = $cid;

				return $this->select("count(id) as cnt")
					->from("agent_request")
					->where("company_id=? and is_active=1 and status=1")
					->get($parameters)
					->first();


			}
		}
	}
?>