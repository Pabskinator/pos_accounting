<?php
	class Addtl_experience extends Crud{
		protected $_table = 'addtl_experience';
		public function __construct($a=null){
			parent::__construct($a);
		}

		public function countRecord($cid,$search=''){
			$parameters = array();
			if ($cid) {


				$parameters[] = $cid;
				$this->where("a.company_id = ?");

				if($search) {
					$parameters[] = "%$search%";
					$this->where(" and m.lastname like ? ");
				}
				$this->select("count(a.id) as cnt")
					->from("addtl_experience a")
					->join("left join members m on  m.id = a.member_id");

				return $this->get($parameters)->first();
			}
		}

		public function get_record($cid,$start,$limit,$search=''){
			$parameters = array();
			if ($cid) {


				$parameters[] = $cid;

				$this->where("a.company_id = ?");

				if($limit){
					$this->limitBy("$start,$limit");
				}
				if($search) {
					$parameters[] = "%$search%";
					$this->where(" and m.lastname like ?");
				}
				$this->select(" a.*, m.lastname")
					->from("addtl_experience a")
					->join("left join members m on m.id = a.member_id")
					->orderBy("a.id");



				return $this->get($parameters)->all();

			}
		}
	}