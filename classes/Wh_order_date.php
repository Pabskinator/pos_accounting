<?php
	class Wh_order_date extends Crud{
		protected $_table = 'wh_order_dates';
		public function __construct($w=null){
			parent::__construct($w);
		}
		public function getDates($order_id = 0){
			$parameters = array();
			if($order_id){
				$parameters[] = $order_id;
				$q= "Select od.*, u.lastname, u.firstname,u.middlename from wh_order_dates od left join users u on u.id= od.user_id where od.wh_order_id=? order by od.id desc";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
	}
?>