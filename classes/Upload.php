<?php
	class Upload extends Crud{
		protected $_table = 'uploads';
		public function __construct($u=null){
			parent::__construct($u);
		}
		public function getAllImage($company_id=0,$ref_table='',$ref_id=0){
			$parameters = array();
			if($company_id && $ref_table && $ref_id){
				$parameters[] = $company_id;
				$parameters[] = $ref_table;
				$parameters[] = $ref_id;
				 $q= "Select * from uploads where company_id=? and ref_table=? and ref_id=? ";
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
				return false;
			}
		}
		public function deleteFile($id=0){
			$parameters = array();
			if($id && $id && $id){
				$parameters[] = $id;

				$q= "Delete  from uploads where id=? ";
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return true;
				}
				return false;
			}
		}

		public function getAllTags($company_id=0,$ref_table=''){
			$parameters = array();
			if($company_id && $ref_table){
				$parameters[] = $company_id;
				$parameters[] = $ref_table;

				$q= "Select DISTINCT(tags) as tags from uploads where company_id=? and ref_table=?";
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
				return false;
			}
		}
		public function deletePicture($id=0){
			$parameters = array();
			if($id ){

				$parameters[] = $id;
				$q= "Delete from uploads where id = ? limit 1 ";
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return true;
				}
				return false;
			}
		}
		public function updateIsMain($ref_table='',$ref_id=0){
			$parameters = array();
			if($ref_table && $ref_id){
				$parameters[] = $ref_table;
				$parameters[] = $ref_id;
				 $q= "update uploads set is_main = 0 where ref_table=? and ref_id = ? ";
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return true;
				}
				return false;
			}
		}
		public function markAsMain($id = 0){
			$parameters = array();
			if($id){
				$parameters[] = $id;
				$q= "update uploads set is_main = 1 where id = ? ";
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return true;
				}
				return false;
			}
		}
		public function getImages($dt1, $dt2, $ref_table='', $limit = 0,$user_id = 0){
			$parameters = array();
			if( $ref_table){

				$parameters[] = $ref_table;
				$where = "";
				if($dt1 && $dt2){
					$parameters[] = $dt1;
					$parameters[] = $dt2;
					$where = "and created >= ? and created <= ?  ";
				}
				$limitBy = '';
				if($limit){
					$limitBy = " order by created desc limit $limit ";
				}
				$whereUser = '';
				if($user_id){
					//  i add user id in thumbnail column because it is unused
					$whereUser = " and (thumbnail = '' or CONCAT( ',',thumbnail, ',' ) LIKE '%,$user_id,%' )";
				}

				$q= "Select * from uploads where ref_table=? $where $whereUser $limitBy";
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
				return false;
			}
		}
	}
?>