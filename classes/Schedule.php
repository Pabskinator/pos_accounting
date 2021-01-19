<?php
	class Schedule extends Crud {
		protected $_table = 'schedules';

		public function __construct($s = null) {
			parent::__construct($s);
		}

		public function getRecord() {

				$parameters = array();

				$q = "Select s.*, b.name as branch_name, md.name as doctor_name
					 from schedules s
					 left join med_doctors md on md.id = s.doctor_id
					 left join branches b on b.id = s.branch_id
					 ";
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
				return false;

		}
	}
