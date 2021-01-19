<?php
	class Data extends Crud{
		protected $_table = 'data';
		public function __construct($data=null){
			parent::__construct($data);
		}
		public function getLabels($p = 0){
			$parameters = array();
			if($p){

				$parameters[] = $p;

				$q = "SELECT r.* FROM  request_forms r where r.process_id=? and r.element_name='form_label' and r.is_active = 1  order by `r`.`order`";

				// submit the query to DB class
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}

		}
		public function getData($monitoring_id = 0){
				$parameters = array();
				if($monitoring_id){

					$parameters[] = $monitoring_id;

					$q = 'SELECT d.content, r.* FROM `data` d left join request_forms r on d.request_form_id=r.id where d.monitoring_id=?  and r.is_active = 1  order by `r`.`order`';

					// submit the query to DB class
					$data = $this->_db->query($q, $parameters);
					if($data->count()){
						// return the data if exists
						return $data->results();
					}
				}

		}
		public function processData($monitoring_id = 0,$isfinal=0){
			$parameters = array();
			if($monitoring_id){

				$parameters[] = $monitoring_id;
				if($isfinal){
					$wherestep = 'from_step = -2, current_step = -2';
				} else {
					$wherestep = 'from_step = current_step+1, current_step = current_step+1';
				}
				$q = 'update monitorings set '.$wherestep.' where id=?';

				// submit the query to DB class
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return true;
				}
			}

		}
		public function declineData($monitoring_id = 0){
			$parameters = array();
			if($monitoring_id){

				$parameters[] = $monitoring_id;
			
				$q = 'update monitorings set current_step = -1 where id=?';
				// submit the query to DB class
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return true;
				}
			}

		}
		public function updateDetail($monitoring_id = 0,$form_id=0,$value=''){
			$parameters = array();
			if($monitoring_id && $form_id ){

				$parameters[] = $value;
				$parameters[] = $monitoring_id;
				$parameters[] = $form_id;

				$q = 'update `data` set `content` = ? where monitoring_id=? and request_form_id=?';
				// submit the query to DB class
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return true;
				}
			}

		}
	}
?>