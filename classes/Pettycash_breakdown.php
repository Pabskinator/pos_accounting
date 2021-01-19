<?php
	class Pettycash_breakdown extends Crud{
		protected $_table = 'pettycash_breakdown';
		public function __construct($p=null){
			parent::__construct($p);
		}

		public function getBreakdown($branch_id=0,$request_id=0){
			$parameters = [];
			if($branch_id){
				$parameters[] = $branch_id;
				$parameters[] = $request_id;


				$q= "Select * from pettycash_breakdown where branch_id=? and request_id=?";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function getTotalExpense($branch_id=0,$request_id=0){
			$parameters = [];
			if($branch_id){
				$parameters[] = $branch_id;
				$parameters[] = $request_id;


				$q= "Select sum(amount) as totalExpense from pettycash_breakdown where branch_id=? and request_id=?";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->first();
				}
			}
		}
		public function deletePetty($id){
			$parameters = [];
			if($id){
				$parameters[] = $id;
				$q= "Delete from pettycash_breakdown where id=?";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return true;
				}
			}
		}
		public function updatePettyBreakdown($branch_id=0,$request_id=0,$whatreq=0){
			$parameters = [];
			if($branch_id){
				$parameters[] = $request_id;
				$parameters[] = $branch_id;
				$parameters[] = $whatreq;
				$q= "update pettycash_breakdown set request_id=? where branch_id=? and request_id=?";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return true;
				}
			}
		}
	}
?>