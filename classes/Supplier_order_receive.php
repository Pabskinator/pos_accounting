<?php
	class Supplier_order_receive extends Crud {
		protected $_table = 'supplier_order_receive';

		public function __construct($s = null) {
			parent::__construct($s);
		}

		public function receiveDetails($id =0){
			$parameters = array();
			if($id) {
				$parameters[] = $id;


				$q = "
					Select od.*, i.item_code, i.description from supplier_order_receive od
						left join items i on i.id = od.item_id
						where od.supplier_order_id = ? order by od.dt desc
				";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function receiveInfo($id =0){
			$parameters = array();
			if($id) {
				$parameters[] = $id;


				$q = "
					Select od.* from supplier_order_receive od
						where od.supplier_order_id = ? group by od.dt  order by od.dt desc
				";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}
	}