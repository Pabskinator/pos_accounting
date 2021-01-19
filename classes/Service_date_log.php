<?php
	class Service_date_log extends Crud {
		protected $_table = 'service_date_logs';

		public function __construct($serv = null) {
			parent::__construct($serv);
		}
		public function getList($id){
			$parameters = array();
			if($id){

				$parameters[] = $id;
				$q = "Select s.*, u.firstname , u.lastname from service_date_logs s left join users u on u.id = s.user_id where s.service_id = ?";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}

	}