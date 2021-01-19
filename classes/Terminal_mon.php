<?php
	class Terminal_mon extends Crud{
		protected $_table='terminal_mon';
		public function __construct($t = NULL){
			parent::__construct($t);
		}
		//aqua
		public function countRecord($cid,$p_type,$terminal_id,$dt1=0,$dt2=0){
			$parameters = array();
			if ($cid) {
				$parameters[] = $cid;

				if($p_type) {
					$parameters[] = $p_type;
					$typeWhere = " and t.p_type = ? ";
				} else {
					$typeWhere = '';
				}

				if($terminal_id) {
					$parameters[] = $terminal_id;
					$terminalwhere = " and t.terminal_id=? ";
				} else {
					$terminalwhere = "";
				}
				if($dt1 && $dt2) {
					$dt1 = strtotime($dt1);
					$dt2 = strtotime($dt2 . "1 day -1 sec");
					$dateWhere = " and t.created >=$dt1 and t.created<=$dt2 ";
				} else {
					$dateWhere = "";
				}


				 $q = "Select count(t.id) as cnt from terminal_mon t  where t.company_id=? and t.is_active=1 $typeWhere $terminalwhere $dateWhere";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function get_record($cid,$start,$limit,$p_type,$terminal_id,$dt1=0,$dt2=0){

			$parameters = array();
			if ($cid) {
				$parameters[] = $cid;
				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}
				if($p_type) {
					$parameters[] = $p_type;
					$typeWhere = " and t.p_type = ? ";
				} else {
					$typeWhere = '';
				}

				if($terminal_id) {
					$parameters[] = $terminal_id;
					$terminalwhere = " and t.terminal_id=? ";
				} else {
					$terminalwhere = "";
				}
				if($dt1 && $dt2) {
					$dt1 = strtotime($dt1);
					$dt2 = strtotime($dt2 . "1 day -1 sec");
					$dateWhere = " and t.created >=$dt1 and t.created<=$dt2 ";
				} else {
					$dateWhere = "";
				}
				$q = "Select t.*,tl.name as tname,u.lastname,u.firstname,u.middlename from terminal_mon t left join users u on u.id=t.user_id left join terminals tl on tl.id=t.terminal_id  where t.company_id=? and t.is_active=1 $typeWhere $terminalwhere $dateWhere order by t.created desc $l";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
	}
?>