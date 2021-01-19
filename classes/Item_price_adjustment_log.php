<?php
	class Item_price_adjustment_log extends Crud{
		protected $_table='item_price_adjustment_log';
		public function __construct($item = NULL){
			parent::__construct($item);
		}
		public function countRecord($cid,$search_item='',$branch_id){
			$parameters = array();
			$whereBranch ='';
			$whereItem = '';
			if($cid) {
				$parameters[] = $cid;

				if($search_item){

					$parameters[] = "%$search_item%";
					$parameters[] = "%$search_item%";
					$whereItem = " and (i.item_code like ? or i.description like ? ) ";
				}

				if($branch_id){
					$parameters[] = $branch_id;
					$whereBranch = " and il.branch_id = ?  ";
				}


				$q = "Select count(il.id) as cnt from item_price_adjustment_log il left join items i on i.id=il.item_id where il.company_id=? and il.is_active=1  $whereItem $whereBranch";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function get_record($cid,$start,$limit,$search_item='',$branch_id=0){
			$parameters = array();
			$whereBranch ='';
			$whereItem = '';
			if($cid){
				$parameters[] = $cid;

				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}

				if($search_item){
					$parameters[] = "%$search_item%";
					$parameters[] = "%$search_item%";
					$whereItem = " and (i.item_code like ? or i.description like ? ) ";
				}
				if($branch_id){
					$parameters[] = $branch_id;
					$whereBranch = " and il.branch_id = ?  ";
				}

				$q= "Select i.item_code, i.description, il.from_price,il.to_price,il.created,b.name as branch_name, u.lastname,u.firstname,u.middlename from item_price_adjustment_log il  left join items i on i.id=il.item_id left join branches b on b.id=il.branch_id left join users u on u.id=il.user_id where il.company_id=? and il.is_active=1 $whereItem $whereBranch order by il.created desc $l";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
	}
?>