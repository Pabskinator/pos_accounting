<?php
	class Payment_consumable_freebies extends Crud{
		protected $_table = 'payment_consumable_freebies';
		public function __construct($c=null){
			parent::__construct($c);
		}
		public function getByPids($pids = ""){

			if($pids){
				$parameters = [];

				$q= 'Select * from payment_consumable_freebies  where  payment_id in ('.$pids.') and is_active = 1';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
				return false;
			}
		}
	}
?>