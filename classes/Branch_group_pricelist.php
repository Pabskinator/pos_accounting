<?php

	class Branch_group_pricelist extends Crud {

		protected $_table = 'branch_group_pricelist';

		public function __construct($b=null){
			parent::__construct($b);
		}

		public function countRecord($group_id,$search = ''){
			$parameters = array();
			if($group_id) {
				$parameters[] =$group_id ;
				$whereSearch ="";
				if($search){
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";

					$whereSearch = " and (i.item_code like ? or i.description like ?)";
				}
				$q = "Select
 						count(b.id) as cnt from branch_group_pricelist b
 						 left join items i on i.id=b.item_id
 						where  b.branch_group_id = ?  $whereSearch ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}


		public function get_record($group_id,$start,$limit,$search = ''){
			$parameters = array();
			if($group_id){
				$parameters[] = $group_id;
				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}
				$whereSearch ="";
				if($search){
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$whereSearch = " and (i.item_code like ? or i.description like ? )";
				}
				$q= "Select b.*, i.item_code,i.description, bg.name as group_name
					from branch_group_pricelist b
					left join branch_groups bg on bg.id = b.branch_group_id
					left join items i on i.id=b.item_id
					where  b.branch_group_id=? $whereSearch
					order by b.branch_group_id $l ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}

		public function checkerEx($branch_group_id=0,$item_id=0){
			$parameters = [];


			 $q = "Select count(id) as cnt from branch_group_pricelist
 						where  branch_group_id = $branch_group_id  and item_id = $item_id limit 1";

			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				// return the data if exists
				return $data->first();
			}
		}

	}
