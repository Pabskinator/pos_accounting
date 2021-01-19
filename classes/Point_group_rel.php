<?php
	class Point_group_rel extends Crud{
		protected $_table = 'point_group_rel';
		public function __construct($p=null){
			parent::__construct($p);
		}
		public function removeRel($pg_id=0){
			if($pg_id ){
				$parameters = array();
				$parameters[] = $pg_id;

				$q = "Delete from point_group_rel where pg_id=?";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					return true;
				}
			}
		}
		public function getRel($pg_id=0){
			if($pg_id ){
				$parameters = array();
				$parameters[] = $pg_id;
				$q = "Select * from point_group_rel where pg_id=?";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					return $data->results();
				}
			}
		}
	}
?>