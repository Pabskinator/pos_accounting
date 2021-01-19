<?php
	class Consumable_supply_details extends Crud{
		protected $_table = 'consumable_supply_details';
		public function __construct($c=null){
			parent::__construct($c);
		}
		public function getDetails($id = 0) {
			$parameters = array();
			$parameters[] = $id;
			$q = "Select d.*, i.item_code,i.description from consumable_supply_details d left join items i on i.id=d.item_id where consumable_supply_id=?";
			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				// return the data if exists
				return $data->results();
			}
		}
		public function updateRacks($racking = '',$item_id = 0,$id = 0) {
			$parameters = array();
			$parameters[] = $racking;
			$parameters[] = $item_id;
			$parameters[] = $id;
			$q = "update consumable_supply_details set racking=? where item_id = ? and consumable_supply_id=?";
			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				// return the data if exists
				return $data->results();
			}
		}
		public function updateConsumption($conqty = 0,$item_id = 0,$id = 0) {
			$parameters = array();
			$parameters[] = $conqty;
			$parameters[] = $item_id;
			$parameters[] = $id;
			$q = "update consumable_supply_details set consume_qty=? where item_id = ? and consumable_supply_id=?";
			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				// return the data if exists
				return true;
			}
		}
	}