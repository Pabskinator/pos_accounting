<?php
	class Caravan_liquidation extends Crud{
		protected $_table = 'caravan_liquidations';
		public function __construct($c=null){
			parent::__construct($c);
		}
		public function get_caravan_request($reqid = 0){

			$parameters = array();
			// if table is set and where is 3
			if($reqid) {
				// get the value
				$parameters[] = $reqid;

				// prepare the query
				$q= "Select * from `caravan_liquidations` where agent_request_id= ? and is_active=1 order by sr, item_id";
				//submit the query
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
	}
?>