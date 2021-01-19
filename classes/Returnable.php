<?php
	class Returnable extends Crud{

		protected $_table = 'returnables';

		public function __construct($r=null){
			parent::__construct($r);
		}

		public function getRecords(){

			$parameters = array();
			$parameters[] = 1;

			$q= "Select r.*, i.item_code , i2.item_code as ret_item_code from returnables r left join items i on i.id = r.item_id left join items i2 on i2.id = r.ret_item_id where r.is_active = ?";
			$data = $this->_db->query($q,$parameters);

			if($data->count()){
				// return the data if exists
				return $data->results();
			}

		}

		public function hasReturnables($wh_id = 0){

			$parameters = array();
			$parameters[] = 1;
			$parameters[] = $wh_id;

			$q= "Select r.*, i.item_code , i2.item_code as ret_item_code,whd.qty from returnables r left join items i on i.id = r.item_id left join items i2 on i2.id = r.ret_item_id left join wh_order_details whd on whd.item_id = r.item_id where r.is_active = ? and whd.wh_orders_id = ?";
			$data = $this->_db->query($q,$parameters);

			if($data->count()){
				// return the data if exists
				return $data->results();
			}
			return false;
		}

	}