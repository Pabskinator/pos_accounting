<?php
	class Consumable_amount extends Crud{
		protected $_table = 'consumable_amount';
		public function __construct($c = null){
			parent::__construct($c);
		}
		public function selectConsumableByServiceId($id){
			$parameters=[];
			if($id){
				$parameters[] = $id;
				$q = "select * from consumable_amount where from_payment_id = ? limit 1 ";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}

		}
	}
?>