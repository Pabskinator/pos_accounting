<?php
	class Timelog extends Crud{

		protected $_table='timelogs';

		public function __construct($t = NULL){
			parent::__construct($t);
		}

		public function getTimelogTechnician($id){
			$parameters = array();
			if($id){
				// set the price id
				$parameters[] = $id;
				$q= "Select * from timelogs where ref_id = ? ";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}

	}
?>