<?php

	class Feedback extends  Crud {
		protected $_table = 'feedback_list';
		public function __construct($d=null){
			parent::__construct($d);
		}
		public function countRecord($cid,$status=0) {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$parameters[] = $status;

				$q = "Select count(f.id) as cnt from feedback_list f  where f.company_id = ? and f.status = ?";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function get_record($cid, $start, $limit,$status=0) {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$parameters[] = $status;
				if($limit) {
					$l = " LIMIT $start,$limit";
				} else {
					$l = '';
				}

				$q = "Select  f.*, u.firstname, u.lastname from feedback_list f left join users u on u.id= f.user_id where f.company_id = ?  and f.status = ? $l ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}
	}