<?php
	class Supplier_order_details extends Crud {
		protected $_table = 'supplier_order_details';
		public function __construct($s=null){
			parent::__construct($s);
		}
		public function deleteItem($id =0){
			$parameters = array();
			if($id) {
				$parameters[] = $id;


				$q = "delete from supplier_order_details where id = ? ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return true;
				}
			}
		}
	}