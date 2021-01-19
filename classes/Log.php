<?php
	class Log  extends Crud {
		protected $_table='logs';

		public function __construct($l = NULL){
			parent::__construct($l);

		}
		public static function addLog($user_id,$company_id,$remarks,$page){
			$parameters = array();
			$now = time();
			$parameters[]=$user_id;
			$parameters[]=$remarks;
			$parameters[]=$page;
			$parameters[]=$company_id;

			$db = DB::getInstance();
			$q = "INSERT INTO `logs`(`user_id`, `remarks`, `page`, `company_id`, `is_active`, `created`) VALUES (?,?,?,?,1,$now)";
			$data = $db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return true;
			}
		}
		public function countRecord($cid,$search='',$user_id=0){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;

				$whereSearch = "";
				$whereUser = "";
				if($search){
					$parameters[] = "%$search%";
					$whereSearch = " and remarks like ? ";
				}
				if($user_id){
					$parameters[] = $user_id;
					$whereUser = " and user_id = ? ";
				}
				$q = "Select count(id) as cnt from logs where company_id=?  $whereSearch $whereUser";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function get_log_record($cid,$start,$limit,$search='',$user_id=0){
			$parameters = array();
			if($cid){
				$parameters[] = $cid;
				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}
				$whereSearch = "";
				$whereUser = "";
				if($search){
					$parameters[] = "%$search%";
					$whereSearch = " and l.remarks like ? ";
				}

				if($user_id){
					$parameters[] = $user_id;
					$whereUser = " and l.user_id = ? ";
				}



				$q= "Select l.*, u.firstname, u.middlename,u.lastname
				from logs l left join users u on u.id=l.user_id
				where l.company_id=? and l.is_active=1 $whereSearch $whereUser order by l.created desc $l  ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
	}
?>