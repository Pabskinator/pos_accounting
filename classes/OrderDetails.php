<?php
	class OrderDetails extends Crud{
		protected $_table='order_details';

		public function __construct($od=null){
			parent::__construct($od);
		}
		public function getOrders($order_id =0){
			if($order_id){
				$parameters = array();
				$parameters[] = $order_id;
				$q= 'Select od.*, i.item_code,i.barcode from order_details od left join items i on i.id= od.item_id  where  order_id=?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return  $e->results();
				}
			}
		}
	}
?>