<?php
	class Discount extends Crud{
		protected $_table = 'discounts';
		public function __construct($d=null){
			parent::__construct($d);
		}
		public function getDiscount($item_id=0,$branch_id=0){
			$now = time();
			$parameters = array();

			$parameters[] = $item_id;
			$parameters[] = $branch_id;
			 $q = "Select * from discounts where item_id=? and branch_id=? and is_active=1 and $now >= date_start and $now  <= date_end ";

			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				// return the data if exists
				return $data->results();
			}
		}
		public function countRecord($cid,$search='',$date_start=0,$date_end=0,$branch_id=0){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;

				if($search) {

					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$likewhere = " and (i.item_code like ? or i.barcode like ? or i.description like ? ) ";
				} else {
					$likewhere = "";
				}
				if($date_start && $date_end){
					$date_start = strtotime($date_start);
					$date_end = strtotime($date_end);
					$parameters[] = $date_start;
					$parameters[] = $date_end;
					$dtWhere = " and d.date_start >= ? and d.date_end <=? ";
				} else {
					$dtWhere = "";
				}
				if($branch_id){
					$parameters[] = $branch_id;
					$branchWhere = " and d.branch_id = ? ";
				} else {
					$branchWhere = "";
				}

				$q = "Select count(d.id) as cnt from discounts d left join items i  on i.id = d.item_id where d.company_id=? and d.is_active=1 $likewhere $dtWhere $branchWhere";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function get_record($cid,$start,$limit,$search='',$date_start=0,$date_end=0,$branch_id=0){
			$parameters = array();
			if($cid){
				$parameters[] = $cid;

				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}
				if($search){
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$likewhere = " and (i.item_code like ? or i.barcode like ? or i.description like ? ) ";
				} else {
					$likewhere='';
				}
				if($date_start && $date_end){
					$date_start = strtotime($date_start);
					$date_end = strtotime($date_end);
					$parameters[] = $date_start;
					$parameters[] = $date_end;
					$dtWhere = " and d.date_start >= ? and d.date_end <=? ";
				} else {
					$dtWhere = "";
				}
				if($branch_id){
					$parameters[] = $branch_id;
					$branchWhere = " and d.branch_id = ? ";
				} else {
				$branchWhere = "";
				}
				$q= "Select i.item_code, i.description, d.* from discounts d left join items i  on i.id = d.item_id where d.company_id=? and d.is_active=1 $likewhere $dtWhere $branchWhere $l";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
	}
