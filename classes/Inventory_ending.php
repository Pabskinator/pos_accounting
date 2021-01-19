<?php
	class Inventory_ending extends Crud {
		protected $_table = 'inventory_ending';

		public function __construct($i = null) {
			parent::__construct($i);
		}

		public function getSummary(){

			$parameters = [];

			$q = "  Select i.report_date,i.branch_id, b.name as branch_name
 					from inventory_ending i
 					left join branches b on b.id = i.branch_id
 					group by i.branch_id,  i.report_date order by report_date";

			$data = $this->_db->query($q, $parameters);

			if($data->count()) {
				// return the data if exists
				return $data->results();
			}

		}

		public function getDetails($branch_id=0,$dt=0){

			$parameters = [];
			$parameters[] = $branch_id;
			$parameters[] = $dt;

			$q = "  Select it.item_code, it.description, i.qty
 					from inventory_ending i
  					left join items it on it.id = i.item_id
 					where i.branch_id = ? and i.report_date = ? ";

			$data = $this->_db->query($q, $parameters);

			if($data->count()) {
				// return the data if exists
				return $data->results();

			}

		}
	}