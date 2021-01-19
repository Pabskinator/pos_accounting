<?php

	class Item_post_spec extends Crud {
		protected $_table = 'item_post_specs';

		public function __construct($i = null) {
			parent::__construct($i);
		}
		public function getSpecs($c){
			if($c){
				$parameters = array();
				$parameters[] = $c;

				$q= "select ips.*, i.item_code,i.description, isp.name as spec_name from item_post_specs ips left join item_specifications isp on isp.id = ips.spec_id left join items i on i.id = ips.item_post_id where ips.company_id = ? order by i.item_code asc, isp.name asc";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function deleteSpecs($item_id){
			if($item_id){
				$parameters = array();
				$parameters[] = $item_id;

				$q= "Delete from item_post_specs where item_post_id = $item_id";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return true;
				}
			}
		}
	}
