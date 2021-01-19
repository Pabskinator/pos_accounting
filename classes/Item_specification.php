<?php
	class Item_specification extends Crud {
		protected $_table = 'item_specifications';

		public function __construct($i = null) {
			parent::__construct($i);
		}

		public function specType(){
			$parameters = array();

				$parameters[] = 1;
				$q= "Select spec_type from item_specifications where 1 = ? group by spec_type";

				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}


	}