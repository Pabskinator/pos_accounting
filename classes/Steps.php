<?php
	class Steps extends Crud{
		protected $_table='steps';
		public function __construct($step = NULL){
			parent::__construct($step);
		}

		public function getMyStep($process_id = 0){
			$parameters = array();
			if($process_id){

		
				$parameters[] = $process_id;
				$q = 'SELECT * FROM `steps` where process_id=? and is_active = 1';

				// submit the query to DB class
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function countSteps($process_id = 0){
			$parameters = array();
			if($process_id){
				$parameters[] = $process_id;
				$q = 'SELECT count(id) as count_step FROM `steps` where  process_id=? and is_active = 1';

				// submit the query to DB class
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
			}

		}
		public function getStepName($process_id =0 ,$step_num =0){
			$parameters = array();
			if($process_id && $step_num){

				$parameters[] = $process_id;
				$parameters[] = $step_num;
				$q = 'SELECT * FROM `steps` where process_id=? and step_number=? and is_active=1';

				// submit the query to DB class
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
			}

		}
		public function hasAuth($process_id =0, $pos_id =0 ){
			$parameters = array();
			if($process_id ){

				$parameters[] = $process_id;
				$pos_id = (int) $pos_id;
				 $q = "SELECT count(*) as cnt FROM `steps` where process_id=? and  CONCAT( ',', whos_responsible, ',' ) LIKE '%,$pos_id,%'";

				// submit the query to DB class
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
			}

		}

	}
?>