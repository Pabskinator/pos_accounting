<?php
	class Price_group extends Crud{
		protected $_table='price_groups';
		public function __construct($price = NULL){
			parent::__construct($price);
		}


		public function getPG(){
			$parameters = array();
			$cid = 1;
			if($cid) {
				$parameters[] = $cid;

				$q = "Select * from price_groups where 1=1 order by name desc";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

	}
?>