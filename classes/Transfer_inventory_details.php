<?php
	class Transfer_inventory_details extends Crud{
		protected $_table='transfer_inventory_details';
		public function __construct($t = NULL){
			parent::__construct($t);
		}
		public function getDetails($id = 0){
			$parameters = array();
			if($id){
				$parameters[] =$id;
				 $q= "Select r.rack_tag, i.description,i.item_code,i.id as itemid,t.rack_id_from,t.rack_id_to,t.qty,t.id,t.racking from transfer_inventory_details t left join racks r on r.id = t.rack_id_to left join items i on i.id = t.item_id  where t.transfer_inventory_id=?";
				$data = $this->_db->query($q,$parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}
	}
?>