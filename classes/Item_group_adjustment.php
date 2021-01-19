<?php

	class Item_group_adjustment extends Crud {
		protected $_table = 'item_group_adjustment';

		public function __construct($f = null) {
			parent::__construct($f);
		}
		public function checkIfExists($item_id=0,$group_id){

			$parameters = array();
			if($item_id && $group_id){


				$parameters[] = $item_id;
				$parameters[] = $group_id;

				$q= 'Select count(id) as cnt  from item_group_adjustment where  item_id=? and group_adjustment_id= ?';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
			}

		}
		public function countRecord($cid,$search_item='',$group_id=0){
			$parameters = array();
			$whereGroup = '';
			$whereItem = '';
			$whereDt = '';

			if($cid) {

				if($search_item){
					$parameters[] = "%$search_item%";
					$parameters[] = "%$search_item%";
					$whereItem = " and (i.item_code like ? or i.description like ? ) ";
				}






				if($group_id){

					$parameters[] = $group_id;
					$whereGroup = " and ip.group_adjustment_id = ? ";
				}


				$q= "Select count(ip.id) as cnt
					from item_group_adjustment ip
 					left join items i on ip.item_id=i.id
 					 where 1=1 $whereItem $whereGroup order by ip.created desc";



				//$q = "Select count(i.id) as cnt from items i where i.company_id=? and i.is_active=1  $whereItem";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function get_record($cid,$start,$limit,$search_item='',$group_id=0){
			$parameters = array();

			$whereItem = '';

			$whereGroup ='';

			if($cid){



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


				if($group_id){

					$parameters[] = $group_id;
					$whereGroup = " and ip.group_adjustment_id = ? ";
				}

				$q= "Select i.item_code, i.description,ip.created, i.id ,ip.adjustment, ip.id as ipid, u.lastname, u.firstname, g.name
					from item_group_adjustment ip
					left join group_adjustment_optional g on g.id = ip.group_adjustment_id
					left join users u on u.id = ip.user_id
 					left join items i on ip.item_id=i.id where 1=1 $whereItem  $whereGroup order by ip.created desc  $l";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}

		function getAdjustment($item_id=0){
			$parameters = array();
			if($item_id){


				$parameters[] = $item_id;
				$q = "	Select ig.adjustment, g.name
						from item_group_adjustment  ig
						left join group_adjustment_optional g on g.id = ig.group_adjustment_id
						where ig.item_id=? ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
				return false;

			}
		}
	}