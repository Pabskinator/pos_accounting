<?php
	class Serial extends Crud{
		protected $_table = 'serials';
		public function __construct($s=null){
			parent::__construct($s);
		}

		public function getSerialIn($p=""){
			$parameters = array();

			if($p){

				return $this->select("*")
					->from("serials")
					->where("payment_id in (".$p.")")
					->get($parameters)
					->all();

			}
		}
		public function countSerials($p=0,$item_id = 0){
			$parameters = array();

			if($p && $item_id ){
				$parameters[] = $item_id;
				$parameters[] = $p;

				$q= "Select count(id) as cnt from serials  where item_id = ? and payment_id = ?";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function getItemSerials($p=0,$item_id = 0){
			$parameters = array();

			if($p && $item_id ){
				$parameters[] = $item_id;
				$parameters[] = $p;

				$q= "Select s.* from serials s where s.item_id = ? and s.payment_id = ?";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function checkIfExists($p=0,$item_id = 0,$serial=''){
			$parameters = array();

			if($p && $item_id ){
				$parameters[] = $item_id;
				$parameters[] = $p;
				$parameters[] = $serial;

				$q= "Select count(id) as cnt from serials  where item_id = ? and payment_id = ? and serial_no = ? ";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function countRecord($cid,$search='',$branch_id=0,$dateStart=0,$dateEnd=0,$member_id=0,$item_id=0,$assembly_only=0){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;

				if($search) {
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$likewhere = " and (s.serial_no like ? or ss.invoice like ? or ss.dr like ? or ss.ir like ? ) ";
				} else {
					$likewhere = "";
				}
				$branchWhere = "";
				$dtWhere = "";
				$memberWhere = '';
				$itemWhere='';
				if($branch_id){
					$parameters[] = $branch_id;
					$branchWhere = " and ss.branch_id = ?";
				}

				if($dateStart && $dateEnd){
					$dateStart = strtotime($dateStart);
					$dateEnd = strtotime($dateEnd);

					$dtWhere = " and ss.sold_date >= $dateStart and ss.sold_date <= $dateEnd ";
				}
				if($member_id){
					$parameters[] = $member_id;
					$memberWhere = " and ss.member_id = ?";
				}
				if($item_id){
					$parameters[] = $item_id;
					$itemWhere = " and s.item_id = ?";
				}

				$whereAssembly = "";
				$colAssembly= "";
				$leftJoinAssembly= "";
				if($assembly_only){
					$colAssembly = "wh.id as wh_id,";
					$leftJoinAssembly = "
					left join wh_orders wh on wh.payment_id = s.payment_id
					left join assemble_request ar on ar.wh_id = wh.id ";
					$whereAssembly = " and ar.id is not null ";
				}

				 $q = "Select count(s.id) as cnt
						from serials s
						left join items i on i.id = s.item_id
						left join (Select s.member_id,s.sold_date,t.branch_id,s.payment_id,s.invoice,s.dr ,s.ir from sales s left join terminals t on t.id = s.terminal_id left join branches b on b.id = t.branch_id group by s.payment_id) ss on ss.payment_id = s.payment_id
						$leftJoinAssembly
						where s.company_id=? $likewhere $branchWhere $dtWhere $memberWhere $itemWhere $whereAssembly";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function get_record($cid,$start,$limit,$search='',$branch_id=0,$dateStart=0,$dateEnd=0,$member_id=0,$item_id=0,$assembly_only=0){
			$parameters = array();
			if($cid){
				$parameters[] = $cid;

				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}

				if($search){
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$likewhere = " and (s.serial_no like ? or ss.invoice like ? or ss.dr like ? or ss.ir like ? )";

				} else {
					$likewhere='';
				}

				$branchWhere = "";
				$dtWhere = "";
				$memberWhere = '';
				$itemWhere='';
				if($branch_id){
					$parameters[] = $branch_id;
					$branchWhere = " and ss.branch_id = ?";
				}

				if($dateStart && $dateEnd){
					$dateStart = strtotime($dateStart);

					$dateEnd = strtotime($dateEnd . "1 day -1 sec");

					$dtWhere = " and ss.sold_date >= $dateStart and ss.sold_date <= $dateEnd ";
				}

				if($member_id){
					$parameters[] = $member_id;
					$memberWhere = " and ss.member_id = ?";
				}
				if($item_id){
					$parameters[] = $item_id;
					$itemWhere = " and s.item_id = ?";
				}
				$whereAssembly = "";
				$colAssembly= "";
				$leftJoinAssembly= "";
				if($assembly_only){
					$colAssembly = "wh.id as wh_id,";
					$leftJoinAssembly = "
					left join wh_orders wh on wh.payment_id = s.payment_id
					left join assemble_request ar on ar.wh_id = wh.id ";
					$whereAssembly = " and ar.id is not null ";
				}
				$q= " Select  $colAssembly ss.sold_date, m.lastname as member_name,s.*,ss.invoice,ss.dr,ss.ir,i.item_code,i.description
				from serials s
				left join items i on i.id = s.item_id
				left join
				(Select s.sold_date,t.branch_id,s.payment_id,s.invoice,s.dr ,s.ir, s.member_id
					from sales s
					left join terminals t on t.id = s.terminal_id
					left join branches b on b.id = t.branch_id
					group by s.payment_id
				) ss on ss.payment_id = s.payment_id
				$leftJoinAssembly

				left join members m on m.id = ss.member_id
				 where s.company_id = ? $likewhere $branchWhere $dtWhere $memberWhere $itemWhere $whereAssembly order by s.payment_id desc $l";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}

	}
