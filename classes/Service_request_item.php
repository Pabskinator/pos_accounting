<?php
	class Service_request_item extends Crud{
		protected $_table='service_request_items';
		public function __construct($serv = NULL){
			parent::__construct($serv);
		}
		public function getDetails($id,$status = 0) {
			$parameters = array();
			if($id) {

				$parameters[] = $id;
				$where_stats = "";

				if($status) {

					if($status == 4) {
						$where_stats = " and s.status in (2,3) ";
					} else {
						$parameters[] = $status;
						$where_stats = " and s.status=? ";
					}

				}

				$q = "Select s.* , i.item_code, i.description , i.barcode, u.lastname, u.firstname,i.is_bundle  from  service_request_items s left join items i on i.id = s.item_id left join users u on u.id = s.user_id where s.service_id = ? $where_stats";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}

			}
		}

		public function getSummaryService($dt1= 0,$dt2=0,$branch_id=0){
			$parameters = array();

			$where_date = "";
			$where_branch = "";
			if($dt1 && $dt2){
				$dt1= strtotime($dt1);
				$dt2= strtotime($dt2 . " 1 day -1 sec");
				$where_date = " and s.created >= $dt1 and s.created <= $dt2 ";
			}
			if($branch_id){
				$branch_id = (int) $branch_id;
				$where_branch = " and s.branch_id = $branch_id";
			}
			$q = "Select count(s.id) as cnt, s.service_type_id from item_service_request s where 1=1  $where_date $where_branch group by s.service_type_id";
			$data = $this->_db->query($q, $parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}



		}
			public function getSummary($dt1= 0,$dt2=0,$branch_id=0){
				$parameters = array();

				$where_date = "";
				$where_branch = "";
				if($dt1 && $dt2){
					$dt1= strtotime($dt1);
					$dt2= strtotime($dt2 . " 1 day -1 sec");
					$where_date = " and s.created >= $dt1 and s.created <= $dt2 ";
				}
				if($branch_id){
					$where_branch = " and s.branch_id = $branch_id";
				}
					 $q = "Select count(s.id) as cnt, s.second_status from item_service_request s where s.second_status in (0,1,2,3,4,5,6) $where_date $where_branch group by s.second_status";
					$data = $this->_db->query($q, $parameters);
					if($data->count()){
						// return the data if exists
						return $data->results();
					}



			}
		public function listItem($status = 0,$dt1=0,$dt2=0,$branch_id=0){

			$parameters = array();
			$parameters[] = $status;
			$where_stats = " and s.status=? ";
			$where_date = "";
			$where_branch = "";
			if($dt1 && $dt2){
				$dt1= strtotime($dt1);
				$dt2= strtotime($dt2 . " 1 day -1 sec");
				$where_date = " and s.created >= $dt1 and s.created <= $dt2 ";
			}
			if($branch_id){
				$where_branch = " and si.branch_id = $branch_id";
			}

			$q = "Select b.name as branch_name, si.created as created_at ,  s.* , i.item_code, i.description , i.barcode, u.lastname, u.firstname,i.is_bundle  from  service_request_items s left join item_service_request si on si.id = s.service_id left join members m on m.id = si.member_id left join branches b on b.id = si.branch_id left join items i on i.id = s.item_id left join users u on u.id = s.user_id where 1=1 $where_stats $where_date $where_branch order by s.service_id asc";
			$data = $this->_db->query($q, $parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}


		}
		public function getStatus($id){
			$parameters = array();
			if($id){


				$parameters[] = $id;
				$q = "Select status from service_request_items where service_id = ? limit 1";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
				return false;
			}
		}
		public function stillPending($id){
			$parameters = array();
			if($id){


				$parameters[] = $id;
				$q = "Select count(*) as cnt from service_request_items where service_id = ? and status = 1";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
				return false;
			}
		}
		public function updateStatus($id,$status){
			$parameters = array();
			if($id){

				$parameters[] = $status;
				$parameters[] = $id;
				$q = "Update service_request_items set status = ? where service_id = ? ";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return true;
				}
				return false;
			}
		}
		public function getPendingRequest($item_id=0,$branch_id=0){
			$parameters = array();
			if($item_id && $branch_id){
				$parameters[] = $item_id;
				$parameters[] = $branch_id;
				$q= "Select IFNULL(sum(r.qty),0) as service_qty from service_request_items r
		left join item_service_request s on s.id=r.service_id
		where r.item_id=? and s.branch_id=? and r.status in(1) and s.status = 1";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->first();
				}
			}
		}

	}
?>