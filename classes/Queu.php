<?php
	class Queu extends Crud{
		protected $_table = 'queus';
		public function __construct($q=null){
			parent::__construct($q);
		}
		public function byQueueId($ids = 0){
			$parameters = array();
			if($ids){

				$q= "Select ql.* , q.name
				from queu_lists ql
				left join queus q on q.id = ql.queu_id
				where ql.id in ($ids)";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function getQueues($branch_id = 0){
			$parameters = array();
			if($branch_id){
				$parameters[] = $branch_id;
				$q= 'Select id,name from queus where branch_id=? and is_active=1';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function getQueueList($branch_id = 0){
			$parameters = array();
			if($branch_id){
				$parameters[] = $branch_id;
				$now = time();
				$q= "select q.*, qs.name
				from queu_lists q
				left join queus qs on qs.id = q.queu_id
				where q.checkout > $now or q.checkout = 0";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function insertQeueList($qid=0,$start=0,$end=0,$companyid=0,$branchid=0,$agent_id=0,$orig_checkout=0){
			$parameters = array();
			$parameters[] = $qid;
			$parameters[] =$start;
			$parameters[] =$end;
			$parameters[] =$companyid;
			$parameters[] =$branchid;
			$parameters[] =$agent_id;
			$chk = $end;
			if($orig_checkout){
				$chk =$orig_checkout;
			}

			//	$q= 'Insert into queu_lists(`queu_id`,`checkin`,`checkout`,`company_id`,`branch_id`,`user_id`)values(?,?,?,?,?,?)';
			$this->_db->insert('queu_lists',
				[
					'queu_id' => $qid,
					'checkin' => $start,
					'checkout' => $end,
					'company_id' => $companyid,
					'branch_id' => $branchid,
					'user_id' => $agent_id,
					'orig_checkout' => $chk,
				]
			);



			return $this->_db->lastInsertedId();

		}
		public function markAsComplete($id = 0 , $checkout = 0){
			$parameters = array();
			$parameters[] =$checkout;
			$parameters[] =$id;

				$q= "update queu_lists set checkout = ?  where id = ? limit 1";
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return true;
				}
		}
	}
?>