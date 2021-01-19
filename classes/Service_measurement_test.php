<?php
	class Service_measurement_test extends Crud{
		protected $_table = 'service_measurement_test';
		public function __construct($s=null){
			parent::__construct($s);
		}


		public  function  getMeasurement($id){

			$parameters = array();
			if($id){

				$parameters[] = $id;
				 $q = "Select s.*, sm.name,sm.grp from service_measurement_test s left join service_measurements sm on sm.id = s.service_measurement_id where s.service_id = ?";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}

		}

		public  function  getBlank(){

			$parameters = array();


			$q = "Select sm.name,sm.grp from service_measurements sm where 1=1 and sm.is_active = 1";
			$data = $this->_db->query($q, $parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}


		}

	}
?>