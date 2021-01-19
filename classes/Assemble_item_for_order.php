<?php
	class Assemble_item_for_order extends Crud{
		protected $_table = 'assemble_item_for_orders';
		public function __construct($w=null){
			parent::__construct($w);
		}
		public function getItem($id){
			$parameters = array();
			if($id){
				$parameters[] = $id;
				$q= "Select item_id , min_qty from assemble_item_for_orders where item_id = ? and is_active = 1 ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
			}
		}
	}
