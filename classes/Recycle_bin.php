<?php
	class Recycle_bin  extends Crud {
		protected $_table='users';

		public function __construct($l = NULL){
			parent::__construct($l);
		}

		public function countRecord($cid,$tbl){
			$parameters = array();
			if($cid) {

				$parameters[] = $cid;
				$nocom = ['terminals'];
				if(in_array($tbl,$nocom)){
					$where= "";
				} else {
					$where= " and company_id=? ";
				}
				$tbl = addslashes($tbl);
				$tbl = substr($tbl,0,12);
				$q = "Select count(id) as cnt from $tbl where 1=1 $where and is_active=0 ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function get_rec_record($cid,$start,$limit,$tbl){
			$parameters = array();
			if($cid){

				$parameters[] = $cid;
				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}
				$nocom = ['terminals'];
				if(in_array($tbl,$nocom)){
					$where= "";
				} else {
					$where= " and company_id=? ";
				}

				$tbl = addslashes($tbl);
				$tbl = substr($tbl,0,12);
				$q= "Select * from $tbl where 1=1 $where and is_active=0 order by created desc $l  ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function restoreItem($id,$tbl){
			$parameters = array();
			if($id) {

				$parameters[] = $id;
				$tbl = addslashes($tbl);
				$tbl = substr($tbl,0,12);
				$q = "update $tbl  set is_active=1 where 1=1 and id=? ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return true;
				}
				return false;
			}
		}
	}
?>