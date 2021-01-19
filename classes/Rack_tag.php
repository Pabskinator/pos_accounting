<?php
	class Rack_tag extends Crud{
		protected $_table = 'rack_tags';
		public function __construct($r=null){
			parent::__construct($r);
		}
		public function rackTags($id){
			$parameters = array();
			if($id ){
				$parameters[] = $id;
				$q = "SELECT id,tag_name FROM `rack_tags`where is_active = 1 and company_id = ? ";
				// submit the query to DB class
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function get_my_tags($id =0 ){
			$parameters = array();
			if($id ){
				$parameters[] = "%,$id,%";

				 $q = "SELECT id,tag_name FROM `rack_tags` where   CONCAT( ',', assign_to, ',' ) LIKE ? and is_active = 1";
				// submit the query to DB class
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function insert_tags_ex($ref='',$tag_id=0,$cid=0){
			$parameters = array();
			if( $ref && $tag_id && $cid ){
				$parameters[] = $ref;
				$parameters[] = $tag_id;
				$parameters[] = $cid;
				$now = time();
				$q = "INSERT INTO `excempt_tags`(`ref_table`, `tag_id`, `company_id`, `is_active`, `created`) VALUES (?,?,?,1,$now)";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){

					return true;
				}
				return false;
			}
		}
		public function delete_tags_ex($ref='',$tag_id=0,$cid=0){
			$parameters = array();
			if( $ref && $tag_id && $cid ){
				$parameters[] = $ref;
				$parameters[] = $tag_id;
				$parameters[] = $cid;
				$q = "DELETE FROM `excempt_tags` WHERE ref_table=? and tag_id=? and company_id=?";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					return true;
				}
				return false;
			}
		}
		public function get_tags_ex($ref='',$cid=0,$branch_id=0){
			$parameters = array();
			if( $ref  && $cid &&  $branch_id ){
				$parameters[] = $ref;
				$parameters[] = $cid;
				$parameters[] = $branch_id;
				$q = "Select * FROM `excempt_tags` WHERE ref_table=?  and company_id=? and branch_id=? limit 1";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					return $data->first();
				}
				return false;
			}
		}
		public function get_all_tags_ex($cid=0){

			$parameters = array();
			if( $cid ){

				$parameters[] = $cid;
				$q = "SELECT * FROM `excempt_tags` WHERE  company_id=?";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					return $data->results();
				}
				return false;
			}
		}

	}
?>