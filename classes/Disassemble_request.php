<?php
	class Disassemble_request extends Crud{
		protected $_table = 'disassemble_request';
		public function __construct($w=null){
			parent::__construct($w);
		}

		public function getFullDetails($id = 0){
			$parameters = array();
			if($id){
				$parameters[] = $id;
				$q= "Select o.*,b.name as branch_name, u.lastname as uln, u.firstname as ufn, u.middlename as umn from disassemble_request o left join branches b on b.id=o.branch_id left join users u on u.id=o.user_id where o.id=? and o.is_active=1 ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->first();
				}
			}
		}
		public function getDisassembleRequest($cid,$status=1){
			$parameters = array();
			if($cid){
				$parameters[] = $cid;
				$parameters[] = $status;
				$q= "Select o.*,b.name as branch_name, u.lastname as uln, u.firstname as ufn, u.middlename as umn  from disassemble_request o left join branches b on b.id=o.branch_id left join users u on u.id=o.user_id where o.company_id=? and o.is_active=1 and o.status = ? ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}

	}
?>