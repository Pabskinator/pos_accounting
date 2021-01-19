<?php
	class Wh_service_date extends Crud {
		protected $_table = 'wh_service_date';

		public function __construct($w = null) {
			parent::__construct($w);
		}
		public function getInfo($id=0){

			$parameters = array();
			$parameters[] = $id;

			$q = " select * from wh_service_date where wh_order_id = ? ";

			$data = $this->_db->query($q, $parameters);
			// return results if there is any
			if($data->count()) {
				return $data->first();
			}
		}

		public function updateDate($item_id=0,$start_date=0,$id=0){

			$parameters = array();
			$parameters[] = $start_date;
			$parameters[] = $item_id;
			$parameters[] = $id;

			$q = "update wh_service_date set start_date = ? where item_id = ? and wh_order_id = ? ";

			$data = $this->_db->query($q, $parameters);
			// return results if there is any
			if($data->count()) {
				return true;
			}
		}
		public function getNotification(){

			$parameters = array();

			$dt = date('Y-m-d');

			$q = "
				select
				i.item_code, i.description, w.wh_order_id, w.item_id,w.start_date, w.duration, DATE_ADD(from_unixtime(w.start_date), INTERVAL w.duration DAY) as deadline
	            from wh_service_date w
	            left join items i on i.id = w.item_id
	            where
	            DATE_ADD(from_unixtime(w.start_date), INTERVAL w.duration DAY) <= '$dt'
 				";


			$data = $this->_db->query($q, $parameters);
			// return results if there is any
			if($data->count()) {
				return $data->results();
			}
		}
	}