<?php
	class Item_price_adjustment extends Crud{
		protected $_table='item_price_adjustment';
		public function __construct($item = NULL){
			parent::__construct($item);
		}
		public function checkIfExists($branch_id=0,$item_id=0){

				$parameters = array();
				if($branch_id && $item_id){
					// set the price id
					$parameters[] = $branch_id;
					$parameters[] = $item_id;
					$q= 'Select count(id) as cnt  from item_price_adjustment where  branch_id=? and item_id=? ';
					$data = $this->_db->query($q, $parameters);
					if($data->count()){
						// return the data if exists
						return $data->first();
					}
				}

		}
		function getAdjustment($branch_id=0,$item_id=0,$price_group_id=0){
			$parameters = array();
			if($item_id){
				$whereBranch = '';
				if($branch_id){
					$parameters[] = $branch_id;
					$whereBranch = " and branch_id=?";
				} else if ($price_group_id){
					$parameters[] = $price_group_id;
					$whereBranch = " and price_group_id=?";
				}

				$parameters[] = $item_id;

				 $q = "Select * from item_price_adjustment  where 1=1 $whereBranch  and item_id=? and is_active=1";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
				return false;

			}
		}
		function getAdjustmentPriceGroup($item_id=0,$price_group_id=0){
			$parameters = array();
			if($item_id){



				$parameters[] = $price_group_id;
				$parameters[] = $item_id;


				  $q = "Select i.* from item_price_adjustment i
					where  i.price_group_id = ? and i.item_id=? and i.is_active=1";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
				return false;

			}
		}


		function getAdjustmentPriceGroupAll(){

			$parameters = array();
			$q = "Select i.* from item_price_adjustment i
				where  i.price_group_id != 0 and i.branch_id = 0";
			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				return $data->results();
			}
			return false;

		}

		public function countRecord($cid,$search_item='',$branch_id=0, $dt_from=0, $dt_to=0){
			$parameters = array();
			$whereBranch = '';
			$whereItem = '';
			$whereDt = '';

			if($cid) {
				$parameters[] = $cid;

				if($search_item){
					$parameters[] = "%$search_item%";
					$parameters[] = "%$search_item%";
					$whereItem = " and (i.item_code like ? or i.description like ? ) ";
				}

				$leftJoin = "";
				$addtlCol = "";
				$orderBy =" order by i.item_code asc ";

				if($branch_id){

					$branch_id = (int) $branch_id;
					$leftJoin = " left join item_price_adjustment ip on ip.item_id=i.id and ip.branch_id = $branch_id";
					$orderBy = " order by ip.modified desc ";
					$addtlCol= ", ip.modified,ip.adjustment, ip.id as ipid ";

					if($dt_from && $dt_to){
						$dt_from = strtotime($dt_from);
						$dt_to = strtotime($dt_to);
						$whereDt = " and (ip.modified >= $dt_from and ip.modified <= $dt_to) ";
					}

				}

				$q= "Select count(i.id) as cnt
					from items i
 					$leftJoin where i.company_id=? and i.is_active=1 $whereItem $whereDt $orderBy";



				//$q = "Select count(i.id) as cnt from items i where i.company_id=? and i.is_active=1  $whereItem";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function get_record($cid,$start,$limit,$search_item='', $branch_id = 0, $dt_from=0, $dt_to=0 ){
			$parameters = array();
			$whereBranch ='';
			$whereItem = '';
			$whereDt ='';

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

				$leftJoin = "";
				$addtlCol = "";
				$orderBy =" order by i.item_code asc ";

				if($branch_id){

					$branch_id = (int) $branch_id;
					$leftJoin = " left join item_price_adjustment ip on ip.item_id=i.id and ip.branch_id = $branch_id";
					$orderBy = " order by ip.modified desc ";
					$addtlCol= ", ip.modified,ip.adjustment, ip.id as ipid ";

					if($dt_from && $dt_to){
						$dt_from = strtotime($dt_from);
						$dt_to = strtotime($dt_to);
						$whereDt = " and (ip.modified >= $dt_from and ip.modified <= $dt_to) ";
					}

				}


				$now = time();



/*
                left join
				  	( Select a.item_id, a.effectivity, p.price, p.id as price_id from
						(Select p.item_id, max(p.effectivity) as effectivity  from prices p left join items i on i.id=p.item_id  where i.company_id=$cid  and p.effectivity <= $now group by p.item_id) a
						left join prices p on p.item_id = a.item_id where a.effectivity = p.effectivity)
					  p on p.item_id = i.id
 */

				 $q= "Select i.item_code, i.description, i.id $addtlCol
					from items i
 					$leftJoin where i.company_id=? and i.is_active=1 $whereItem $whereDt $orderBy  $l";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function forDownload($cid,$start,$limit,$search_item='', $branch_id = 0, $dt_from=0, $dt_to=0 ){
			$parameters = array();
			$whereBranch ='';
			$whereItem = '';
			$whereDt ='';

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

				$leftJoin = "";
				$addtlCol = "";
				$orderBy =" order by i.item_code asc ";

				if($branch_id){

					$branch_id = (int) $branch_id;
					$leftJoin = " left join item_price_adjustment ip on ip.item_id=i.id and ip.branch_id = $branch_id";
					$orderBy = " order by ip.modified desc ";
					$addtlCol= ", ip.modified,ip.adjustment, ip.id as ipid ";

					if($dt_from && $dt_to){
						$dt_from = strtotime($dt_from);
						$dt_to = strtotime($dt_to);
						$whereDt = " and (ip.modified >= $dt_from and ip.modified <= $dt_to) ";
					}

				}


				$now = time();


				$q= "Select p.price,i.item_code, i.description, i.id $addtlCol
					from items i
					left join
						  ( Select a.item_id, a.effectivity, p.price, p.id as price_id from
							(Select p.item_id, max(p.effectivity) as effectivity  from prices p left join items i on i.id=p.item_id  where i.company_id=$cid  and p.effectivity <= $now group by p.item_id) a
							left join prices p on p.item_id = a.item_id where a.effectivity = p.effectivity)
						  p on p.item_id = i.id
 					$leftJoin where i.company_id=? and i.is_active=1 $whereItem $whereDt $orderBy  $l";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
	}
?>