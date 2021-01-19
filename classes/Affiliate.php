<?php
	class Affiliate extends Crud{

		protected $_table = 'affiliates';
		public function __construct($agent_request=null){
			parent::__construct($agent_request);
		}

		public function countRecord($cid,$search=''){
			$parameters = array();
			if ($cid) {

				$parameters[] = $cid;
				$this->where(" company_id = ? and is_active = 1");
				if($search) {
					$parameters[] = "%$search%";
					$this->where("and name like ?");
				}
				return $this->select("count(id) as cnt")
					->from("affiliates")
					->get($parameters)
					->first();

			}
		}

		public function get_record($cid,$start,$limit,$search=''){
			$parameters = array();
			if ($cid) {

				$parameters[] = $cid;
				$this->where("company_id = ? and is_active = 1");

				if($limit){
					$this->limitBy("$start,$limit");
				}
				if($search) {
					$parameters[] = "%$search%";
					$this->where("and name like ?");

				}
				$this->select()
					->from("affiliates")
					->get($parameters)
					->all();


			}
		}
		public function deductPoints($id=0,$v=0){
			if($id && $v && is_numeric($v)){


				$parameters = array();

				$parameters[] = $id;

				$this->where("id = ?");

				$this->updateTable("affiliates")

					->setValue("current_wallet = current_wallet - $v");

				return $this->updateQuery($parameters);

			}
		}
	}
?>