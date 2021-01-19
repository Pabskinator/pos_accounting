<?php

	class Reorder_point extends Crud {
		protected $_table = 'reorder_points';

		public function __construct($r = null) {
			parent::__construct($r);
		}

		public function countRecord($cid, $like = '', $b = 0) {
			$parameters = array();
			$parameters[] = $cid;
			if($like) {
				$parameters[] = "%$like%";
				$likewhere = " and i.item_code like ? ";
			} else {
				$likewhere = '';
			}
			if($b) {
				$b = (int) $b;
				$branchwhere = " and r.orderby_branch_id=$b ";
			} else {
				$branchwhere = "";
			}

			$q = "Select count(r.id) as cnt from reorder_points r left join items i on i.id=r.item_id where r.company_id = ? and r.is_active=1 $likewhere $branchwhere";
			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				// return the data if exists
				return $data->first();
			}
		}

		public function get_active_record($cid, $start = 0, $limit = 0, $like = '', $b = 0) {

			$parameters = array();
			$parameters[] = $cid;
			if($limit) {
				$l = " LIMIT $start,$limit";
			} else {
				$l = '';
			}
			if($like) {

				$parameters[] = "%$like%";
				$likewhere = " and i.item_code like ? ";
			} else {
				$likewhere = '';
			}
			if($b) {
				$b = (int) $b;
				$branchwhere = " and r.orderby_branch_id=$b ";
			} else {
				$branchwhere = "";
			}
			// prepare the query
			$q = "Select r.*,i.item_code,i.barcode from reorder_points r left join items i on i.id=r.item_id where r.company_id = ? and r.is_active=1 $likewhere $branchwhere order by r.orderby_branch_id,r.item_id,r.month  $l  ";
			//submit the query
			$data = $this->_db->query($q, $parameters);
			// return results if there is any
			if($data->count()) {
				return $data->results();
			}

		}

		public function pointExists($item_id = 0, $branch = 0, $cid = 0, $month = 0) {
			if($item_id && $branch && $cid && $month) {

				$parameters = array();
				$parameters[] = $cid;
				$parameters[] = $item_id;
				$parameters[] = $branch;
				$parameters[] = $month;
				$q = "Select count(id) as cnt from reorder_points where company_id = ? and is_active=1 and item_id=? and orderby_branch_id=? and `month`=?";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function getOrderPoint($item_id = 0, $branch = 0, $cid = 0, $month = 0) {
			if($item_id && $branch && $cid && $month) {

				$parameters = array();
				$parameters[] = $cid;
				$parameters[] = $item_id;
				$parameters[] = $branch;
				$parameters[] = $month;
				$q = "Select * from reorder_points where company_id = ? and is_active=1 and item_id=? and orderby_branch_id=? and `month`=? ";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				} else {
					unset($parameters[3]);

					$q = "Select * from reorder_points where company_id = ? and is_active=1 and item_id=? and orderby_branch_id=? and `month`=13 ";
					$data = $this->_db->query($q, $parameters);
					if($data->count()) {
						return $data->first();
					}
				}
			}
		}

		public function deleteOrderPoint($item, $orderby, $orderto, $month) {
			$parameters = array();
			if($item && $orderby && $month) {

				$parameters[] = $item;
				$parameters[] = $orderby;
				$parameters[] = $month;
				$q = 'update `reorder_points` set `is_active`=0 where  `item_id`=? and `orderby_branch_id`=? and `month`=?';

				if($this->_db->query($q, $parameters)) {
					// return the data if exists
					return true;
				}

				return false;
			}
		}

		public function batchInsert($q) {
			$parameters = array();
			if($this->_db->query($q, $parameters)) {
				// return the data if exists
				return true;
			}
			return false;
		}
	}

?>