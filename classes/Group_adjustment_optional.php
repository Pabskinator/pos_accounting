<?php

	class Group_adjustment_optional extends Crud {
		protected $_table = 'group_adjustment_optional';

		public function __construct($f = null) {
			parent::__construct($f);
		}
		public function getRecord($active= 1){

			$parameters = array();
			$parameters[] = $active;

			$q= "Select * from group_adjustment_optional where is_active = ? order by name asc";


			$data = $this->_db->query($q, $parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}

		}
	}