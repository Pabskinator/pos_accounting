<?php
	class Assemble_request extends Crud{
		protected $_table = 'assemble_request';
		public function __construct($w=null){
			parent::__construct($w);
		}

		public function getFullDetails($id = 0){
			$parameters = array();
			if($id){
				$parameters[] = $id;
				$q= "Select o.*,b.name as branch_name, u.lastname as uln, u.firstname as ufn, u.middlename as umn from assemble_request o left join branches b on b.id=o.branch_id left join users u on u.id=o.user_id where o.id=? and o.is_active=1 ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->first();
				}
			}
		}
		public function getAssembleRequest($cid,$status=1,$branch_id=0,$dt_from='',$dt_to=''){
			$parameters = array();
			if($cid){
				$parameters[] = $cid;
				$parameters[] = $status;
				$this->where("o.company_id=? and o.is_active=1 and o.status = ?");
				if($branch_id){
					$parameters[] = $branch_id;
					$this->where("and o.branch_id = ?");


				}
				if($dt_from && $dt_to){
					$dt_from = strtotime($dt_from);
					$dt_to = strtotime($dt_to . "1 day -1 sec");
					$this->where("and o.created >= $dt_from  and o.created <= $dt_to");
				}

				return $this->select("o.*,b.name as branch_name, u.lastname as uln, u.firstname as ufn, u.middlename as umn")
					->from("assemble_request o")
					->join("left join branches b on b.id=o.branch_id")
					->join(" left join users u on u.id=o.user_id")
					->orderBy("o.id desc")
					->limitBy("500")
					->get($parameters)
					->all();

			}
		}
		public function getAssembleItem($status=1,$branch_id=0,$dt_from='',$dt_to='',$member_id=0){
			$parameters = array();


				$parameters[] = $status;
				$whereBranch = "";
				$whereDate = "";
				if($branch_id){
					$whereBranch = " and o.branch_id = ? ";
					$parameters[] = $branch_id;
				}
				if($dt_from && $dt_to){

					$whereDate = " and o.created >= $dt_from  and o.created <= $dt_to";
				}

				 $q= "Select d.*,m.lastname as member_name , o.wh_id,wh.invoice,wh.dr,wh.pr, i.item_code, i.description, b.name as branch_name, u.lastname as uln, u.firstname as ufn, u.middlename as umn
				from assemble_details d
				left join  items i on i.id = d.item_id_set
				 left join assemble_request o on o.id = d.assemble_id
				left join branches b on b.id=o.branch_id left join users u on u.id=o.user_id
				left join wh_orders wh on wh.id = o.wh_id
				left join members m on m.id = wh.member_id
				where o.is_active=1 and o.status = ?  $whereBranch $whereDate
				 ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}

		}

		public function getDataWeekly($dt1=0,$dt2=0){
			$parameters = array();
			$whereDate = "";

			if($dt1 && $dt2){
				$whereDate = " and d.created >= $dt1 and d.created <= $dt2 ";
			}

			$q= "Select d.*, week(from_unixtime(d.created),1) as week_number
				from assemble_details d
				left join  items i on i.id = d.item_id_set
				left join assemble_request o on o.id = d.assemble_id
				where  o.status != 4  $whereDate
				 ";
			$data = $this->_db->query($q, $parameters);
			// return results if there is any
			if($data->count()){
				return $data->results();
			}

		}


	}
?>