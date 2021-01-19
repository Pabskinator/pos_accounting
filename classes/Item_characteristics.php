<?php
	class Item_characteristics extends Crud{
		protected $_table='item_characteristics';
		public function __construct($item_characteristics = NULL){
			parent::__construct($item_characteristics);
		}
		public function getMyCharacteristicsd($id){
		$parameters = array();
		if($id){
			// set the price id
			$parameters[] = $id;
			$q= 'Select characteristics_id from item_characteristics where  item_id=? ';
			$data = $this->_db->query($q, $parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}
		}
	}
		public function deleteMyCharacteristics($id){
			$parameters = array();
			if($id){

				$parameters[] = $id;
				$q= 'delete  from item_characteristics where  item_id=? ';

				if($this->_db->query($q, $parameters)){
					// return the data if exists
					return true;
				}
				return false;
			}
		}
	}
?>