<?php
	class Not_receive_item extends Crud {
		protected $_table = 'not_receive_items';

		public function __construct($m = null) {
			parent::__construct($m);
		}

		public function checkItem($supplier_id=0,$item_id=0){
			$parameters = array();
			if($supplier_id && $item_id ) {
				$parameters[] = $supplier_id;
				$parameters[] = $item_id;


				 $q = "Select * from not_receive_items where supplier_order_id = ? and supplier_item_id = ? ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function countRecord($cid,$search = ''){
			$parameters = array();
			if($cid) {

				$whereSearch ="";
				if($search){
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";

					$whereSearch = " and (si.item_code like ? or si.description like ?)";
				}
				$q = "
					  	Select count(nr.*) from not_receive_items nr
						left join supplier_item si on si.id = nr.supplier_item_id
						where 1=1 $whereSearch

					  ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function get_record($cid,$start,$limit,$search = ''){
			$parameters = array();
			if($cid){



				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}

				$whereSearch ="";

				if($search){
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$whereSearch = " and (si.item_code like ? or si.description like ?)";
				}

				$q= "
						Select nr.*, si.item_code
						from not_receive_items nr
						left join supplier_item si on si.id = nr.supplier_item_id
						left join suppliers s on s.id = si.supplier_id
						where 1=1 $whereSearch
						$l

					";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}

	}

