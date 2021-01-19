<?php
	class Branch_discount extends Crud{
		protected $_table = 'branch_discounts';
		public function __construct($branch=null){
			parent::__construct($branch);
		}
		public function getDiscount($branch_id_src=0,$branch_id_req=0){
			if($branch_id_src && $branch_id_req) {
				$parameters = array();
				$parameters[] = $branch_id_src;
				$parameters[] = $branch_id_req;

				$q = "Select * from branch_discounts where branch_id_src = ? and branch_id_req = ? and is_active = 1";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function getAll(){

			$parameters = array();
			$parameters[] = 1;
			$q = "Select b.*, b1.name as branch_req, b2.name as branch_src from branch_discounts b left join branches b1 on b1.id = b.branch_id_req left join branches b2 on b2.id=b.branch_id_src where b.is_active = ?";

			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				// return the data if exists
				return $data->results();
			}
			return [];

		}


	}
?>