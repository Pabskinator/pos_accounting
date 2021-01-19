<?php
	class Sms_number_group extends Crud{

		protected $_table = 'sms_number_group';

		public function __construct($s=null) {
			parent::__construct($s);
		}

		public function  getGroups($s=0) {
			$parameters = array();
			$parameters[] = $s;
			$q= 'Select distinct(group_name) as group_name from sms_number_group where is_active = ? ';
			$data = $this->_db->query($q, $parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}
		}

		public function  getNumberByGroup($n=0) {
			$parameters = array();
			$parameters[] = $n;
			$q= "Select * from sms_number_group where group_name = ? ";
			$data = $this->_db->query($q, $parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}
		}

	}