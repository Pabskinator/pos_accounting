<?php
	class Supplementary extends Crud{
		protected $_table='supplementaries';
		public function __construct($s = NULL){
			parent::__construct($s);
		}
		public function hasSup($sup_id = 0){
			if($sup_id){
				$parameters = [];
				$parameters[] = $sup_id;
				$q = "SELECT count(*) as cnt from supplementaries where child_member_id = ? ";
				$data = $this->_db->query($q,$parameters);
				if($data->count()){
					return $data->first();
				}
				return false;
			}
			return false;
		}
		public function getAll($member_id = 0){
			if($member_id){
				$parameters = [];
				$parameters[] = $member_id;
				 $q = "SELECT s.* , m.lastname from supplementaries s left join members m on m.id = s.child_member_id where s.parent_member_id = ? ";
				$data = $this->_db->query($q,$parameters);
				if($data->count()){
					return $data->results();
				}
				return [];
			}
			return [];
		}
	}