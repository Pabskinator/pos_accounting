<?php
	class FormRequest extends Crud{
		protected $_table = 'request_forms';
		public function __construct($r=null){
			parent::__construct($r);
		}
		public function getForms($process_id = 0){

			$parameters = array();

			$parameters[] = $process_id;
			$q= "Select * from request_forms where is_active = 1 and process_id = ? order by `order` asc";
			$e = $this->_db->query($q, $parameters);
			if($e->count()){
				return $e->results();
			}
			return false;


		}
		public function get_who_can_request(){

			$parameters = array();
		
				$parameters[] = 1;
				$parameters[] = 1;
				$q= 'SELECT distinct(p.id) as process_id ,p.name AS process_name,r.who_can_request
					FROM processes p
					LEFT JOIN request_forms r ON r.process_id = p.id
					WHERE r.is_active=? and p.is_active = ?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
				return false;
			
		
		}

		public function updateProcess($id = 0,$order= 0){

			$parameters = array();
			if($id){
				$parameters[] = $order;
				$parameters[] = $id;
				$q= 'update request_forms set `order` = ? where id = ?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return true;
				}
				return false;
			}
			return false;

		}
		public function updatePosition($id = 0,$position= ''){

			$parameters = array();
			if($id && $position){
				$parameters[] = $position;
				$parameters[] = $id;
				$q= 'update request_forms set `who_can_request` = ? where process_id = ?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return true;
				}
				return false;
			}
			return false;

		}
	}
?>