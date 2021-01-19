<?php
	class Offered_service_history extends Crud{
		protected $_table='offered_services_history';
		public function __construct($o=null){
			parent::__construct($o);
		}
		public function getServiceConsumed($id= 0){
			if($id) {
				$parameters = array();
				$parameters[] = $id;

				$q = "select o.name as service_name, oo.* from offered_services_history oo left join offered_services o
					on o.id=oo.service_id where oo.att_id = ?";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

	}
