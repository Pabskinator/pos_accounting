<?php
	class Bad_order_detail extends Crud{
		protected $_table = 'bad_order_details';
		public function __construct($b=null){
			parent::__construct($b);
		}
		public function getDetails($id = 0) {
			$parameters = array();
			$parameters[] = $id;
			$q = "Select d.*, i.item_code,i.description, r.rack from bad_order_details d left join items i on i.id=d.item_id left join racks r on r.id=d.rack_id where bad_order_id=?";
			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				// return the data if exists
				return $data->results();
			}
		}

		public function countRecord($cid, $search = '') {
			$parameters = array();
			if($cid) {

				$whereSearch= "";

				if($search){
					$parameters[] = "%$search%";
					$whereSearch  = " and (i.item_code like ? ) ";
				}

				$q = "Select count(bd.*) as cnt from bad_order_details bd
						left join items i on i.id = bd.item_id
						where 1=1 $whereSearch

						";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function get_record($cid, $start, $limit, $search = '') {
			$parameters = array();
			if($cid) {

				if($limit) {
					$l = " LIMIT $start,$limit";
				} else {
					$l = '';
				}
				$whereSearch="";
				if($search){
					$parameters[] = "%$search%";
					$whereSearch  = " and ( i.item_code like ? ) ";
				}

				$q = "Select bd.* , i.item_code , b.name as branch_name from bad_order_details bd
					  left join bad_orders bo on bo.id = bd.bad_order_id
					  left join items i on i.id =bd.item_id
					  left join branches b on b.id = bo.branch_id
					  where 1=1 $whereSearch
					  order by bd.id desc $l";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}
	}
