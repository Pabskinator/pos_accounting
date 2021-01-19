<?php
	class Sms_receive extends Crud{
		protected $_table = 'sms_receive';
		public function __construct($s=null) {
			parent::__construct($s);
		}
		public function insertSMSData($cid,$n,$m,$d,$id,$c,$is_order,$terminal_id=0,$expense=0,$dont_insert=0){
			$parameters = array();
			if($cid && $m && $n){
				$parameters[] =$n;
				$parameters[] =$m;
				$parameters[] =$id;
				$parameters[] =$c;
				$parameters[] =$d;
				$parameters[] = $cid;
				$parameters[] = $is_order;
				$parameters[] = $terminal_id;
				$parameters[] = $expense;
				if(!$dont_insert){
					$q= "INSERT INTO `sms_receive`(`number`, `message`, `unique_id`, `created`, `date_received`, `company_id`, `is_active`,`is_order`,`terminal_id`,`is_expense`) VALUES (?,?,?,?,?,?,1,?,?,?)";
					$e = $this->_db->query($q, $parameters);
					if($e->count()){
						return true;
					}
				}

				return true;
			}
			return false;
		}



		public function isMesasageExists($cid,$n,$m,$d,$is_order,$is_expense){
			$parameters = array();
			if($cid){
				$parameters[] =$n;
				$parameters[] =$m;
				$parameters[] =$d;
				$parameters[] = $cid;
				$parameters[] = $is_order;
				$parameters[] = $is_expense;

				$q= "Select count(*) as num from `sms_receive` where `number`=? and `message` = ? and `date_received` = ? and company_id = ? and is_order = ? and is_expense = ?";
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->first();
				}
			}
		}
		public function countRecord($cid,$search='',$b=0,$type=0){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$parameters[] = $type;
				$whereSearch = "";
				if($search){
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$whereSearch = " and (t.name like ? or t2.name like ?) ";
				}
				$q = "Select count(s.id) as cnt from  sms_receive s left join sms_gateway sg on sg.mobile_number = s.number left join terminals t on t.id = s.terminal_id left join terminals t2 on t2.id = sg.terminal_id  where  s.company_id=? and s.status = ?  and s.is_order = 0  and s.is_expense = 0 $whereSearch";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function get_record($cid,$start,$limit,$search='',$b=0,$type=0){
			$parameters = array();
			if($cid){
				$parameters[] = $cid;
				$parameters[] = $type;
				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}
				$whereSearch = "";
				if($search){
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$whereSearch = " and (t.name like ? or t2.name like ?) ";

				}

				$q= "Select s.*,sg.name,t.branch_id as branch_id1, t2.branch_id as branch_id2, t.name as terminal_name, t2.name as terminal_name2
				from sms_receive s
				left join sms_gateway sg on sg.mobile_number = s.number
				left join terminals t on t.id = s.terminal_id
				left join terminals t2 on t2.id = sg.terminal_id
				where  s.company_id=? and s.status=?   and s.is_order = 0 and s.is_expense = 0  $whereSearch
				order by s.id desc $l ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function getSummary($branch_id, $date_from,$date_to){
			$parameters = array();

				$parameters[] = $branch_id;

				$branch_id = (int) $branch_id;

				 $q= "Select s.*,sg.name, t.name as terminal_name from sms_receive s left join sms_gateway sg on sg.mobile_number = s.number left join terminals t on t.id = s.terminal_id left join terminals t2 on t2.id like sg.terminal_id where   (t.branch_id = ? or t2.branch_id = $branch_id) and s.is_order = 0 and s.is_expense = 0  and  UNIX_TIMESTAMP(s.date_received) >= $date_from and  UNIX_TIMESTAMP(s.date_received) <= $date_to";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}

		}

		public function terminalHasReport($dt='', $tid =0 ){
			$parameters = array();

			$parameters[] = $dt;
			$parameters[] = $tid;
			$parameters[] = $tid;



			 $q= "Select s.* from sms_receive s left join sms_gateway sg on sg.mobile_number = s.number  where s.date_received = ? and (s.terminal_id = ? or sg.terminal_id = ?)and s.is_expense = 0 and s.is_order = 0 ";
			$data = $this->_db->query($q, $parameters);
			// return results if there is any
			if($data->count()){
				return true;
			}

		}
	}