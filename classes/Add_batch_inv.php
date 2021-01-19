<?php
	class Add_batch_inv extends Crud{
		protected $_table = 'add_inv_batch';

		public function __construct($a=null){
			parent::__construct($a);
		}

		public function countRecordDetails($cid, $branch_id=0,$dt_from=0,$dt_to=0,$item_id=0,$categ='') {
			$parameters = array();
			if($cid) {


				$parameters[] = $cid;

				$this->where("a.company_id = ? ");
				if($branch_id){
					$parameters[] = $branch_id;
					$this->where(" and a.to_branch_id = ?");
				}
				if($dt_from && $dt_to){
					$dt_from = strtotime($dt_from);
					$dt_to = strtotime($dt_to . "1 day -1 min");
					$this->where("and a.created >= $dt_from and a.created <= $dt_to ");
				}

				if($item_id){
					$parameters[] = $item_id;
					$this->where(" and i.id = ? ");
				}


				if($categ) {
					$explodedcateg = explode(',',$categ);
					$listcateg = "";
					foreach($explodedcateg as $ec){
						$listcateg .= "?,";
						$parameters[] = $ec;
					}
					$listcateg = rtrim($listcateg,",");
					$this->where("and i.category_id in($listcateg) ");

				}

				return $this->select("count(det.id) as cnt")
						->from("add_inv_batch_details det")
						->join("left join items i on i.id = det.item_id")
						->join("left join add_inv_batch a on a.id = det.batch_id")
						->get($parameters)
						->first();


			}
		}

		public function get_record_details($cid, $start, $limit, $branch_id=0,$dt_from=0,$dt_to=0,$item_id=0,$categ='') {
			$parameters = array();
			if($cid) {


				$parameters[] = $cid;
				$this->where("a.company_id = ?");
				if($limit) {
					$this->limitBy("$start,$limit");
				}

				if($branch_id){
					$parameters[] = $branch_id;
					$this->where(" and a.to_branch_id = ?");
				}
				if($dt_from && $dt_to){
					$dt_from = strtotime($dt_from);
					$dt_to = strtotime($dt_to . "1 day -1 min");

					$this->where("and a.created >= $dt_from and a.created <= $dt_to");
				}
				if($item_id){
					$parameters[] = $item_id;
					$this->where(" and i.id = ? ");
				}


				if($categ) {
					$explodedcateg = explode(',',$categ);
					$listcateg = "";
					foreach($explodedcateg as $ec){
						$listcateg .= "?,";
						$parameters[] = $ec;
					}
					$listcateg = rtrim($listcateg,",");
					$this->where("and i.category_id in($listcateg) ");
				}

				return $this->select("det.*,r.rack,i.item_code,i.description, a.packing_list_num,a.ref_num,a.date_receive, a.created, b.name as branch_name, s.name as supplier_name, u.lastname, u.firstname")
					->from("add_inv_batch_details det")
					->join("left join add_inv_batch a  on a.id = det.batch_id")
					->join("left join items i on i.id = det.item_id")
					->join("left join racks r on r.id = det.rack_id")
					->join("left join branches b on b.id = a.to_branch_id ")
					->join("left join suppliers s on s.id = a.supplier_id")
					->join("left join users u on u.id= a.user_id")
					->orderBy("det.id desc")
					->get($parameters)
					->all();



			}
		}

		public function countRecord($cid, $branch_id=0,$dt_from=0,$dt_to=0) {
			$parameters = array();
			if($cid) {



				$parameters[] = $cid;
				$this->where("a.company_id = ?");

				if($branch_id){
					$parameters[] = $branch_id;
					$this->where("and a.to_branch_id = ?");
				}
				if($dt_from && $dt_to){
					$dt_from = strtotime($dt_from);
					$dt_to = strtotime($dt_to . "1 day -1 min");

					$this->where(" and a.created >= $dt_from and a.created <= $dt_to ");
				}
				return $this->select("count(a.id) as cnt")
					->from("add_inv_batch a")
					->get($parameters)
					->first();

			}
		}

		public function get_record($cid, $start, $limit, $branch_id=0,$dt_from=0,$dt_to=0) {
			$parameters = array();

			if($cid) {



				$parameters[] = $cid;
				$this->where("a.company_id = ?");

				if($limit) {
					$this->limitBy("$start,$limit");
				}

				if($branch_id){
					$parameters[] = $branch_id;
					$this->where(" and a.to_branch_id = ?");
				}

				if($dt_from && $dt_to){

					$dt_from = strtotime($dt_from);
					$dt_to = strtotime($dt_to . "1 day -1 min");
					$this->where("and a.created >= $dt_from and a.created <= $dt_to");

				}

				return $this->select("a.*, b.name as branch_name, s.name as supplier_name, u.lastname, u.firstname")
					->from("add_inv_batch a")
					->join("left join branches b on b.id = a.to_branch_id")
					->join("left join suppliers s on s.id = a.supplier_id")
					->join("left join users u on u.id= a.user_id")
					->orderBy("a.id desc")
					->get($parameters)
					->all();


			}
		}


	}
?>