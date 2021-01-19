<?php
	class Item_service_request extends Crud{
		protected $_table='item_service_request';
		private $id= 0;
		private $item_to_receive= [];
		private $payment = [];
		public function __construct($item = NULL){
			parent::__construct($item);
		}
		public function setId($id = 0){
			if($id){
				$this->id = $id;
			}

		}
		public function toStatusGood(){

		}
		public function repairedWithWarranty(){

		}
		public function repairedWithoutWarranty(){

		}
		public function replacementJunk(){

		}
		public function replacementSurplus(){

		}
		public function changeItemJunk(){

		}
		public function changeItemSurplus(){

		}
		public function toReceiving(){

		}
		public function giveCreditToMember(){

		}
		public function payment(){

		}

		public function countTechRecord($cid=0,$technician_id=0,$status=0){

			$parameters = [];
			$parameters[] = $cid;
			$whereTechnician = "";
			$whereStatus = "";
			if($status){
				$status = (int) $status;
				$whereStatus = " and i.status = $status ";

			}
			if($technician_id){
				$technician_id = (int) $technician_id;
				$whereTechnician = " and CONCAT( ',', i.technician_id, ',' ) LIKE '%,$technician_id,%' ";
			}

				$q = " Select count(i.id) as cnt from item_service_request i
					left join users u on u.id=i.user_id
					left join members m on m.id = i.member_id
					left join branches b on b.id=i.branch_id
					where i.company_id=? $whereTechnician  $whereStatus";

			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				// return the data if exists
				return $data->first();
			}

		}

		public function get_technician_record($cid=0,$start,$limit,$technician_id=0,$status=0){
			$parameters = array();

			if($cid){
				$parameters[] = $cid;
				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}

				$whereTechnician="";
				$leftjointech = "";
				$coltech = "";
				$whereStatus = "";
				if($status){
					$status = (int) $status;
					$whereStatus = " and i.status = $status ";

				}

				if($technician_id){
					$coltech = ", tc.name as technician_name ";
					$technician_id = (int) $technician_id;
					$whereTechnician = " and CONCAT( ',', i.technician_id, ',' ) LIKE '%,$technician_id,%' ";
					$leftjointech = " left join technicians tc on tc.id = $technician_id";
				}
				$q = "Select i.*, b.name as branch_name, m.lastname as mln, m.firstname as mfn, m.middlename as mmn, u.lastname, u.firstname, u.middlename $coltech
							from item_service_request i
							left join users u on u.id=i.user_id
							left join members m on m.id = i.member_id
							left join branches b on b.id=i.branch_id
							$leftjointech
							where i.company_id=? $whereTechnician $whereStatus
							order by i.id desc $l";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}

		public function changeStatusItem($id,$item_id,$status){
			$parameters = array();
			if($id && $status){
				$status = (int) $status;
				$parameters[] = $status;
				$parameters[] = $id;
				$parameters[] = $item_id;

				$q= "Update item_service_details set is_done = ?,status_history = CONCAT(status_history,',','$status') where service_id = ? and item_id = ?";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return true;
				}
			}
		}
		public function getRequestWithDetails($status=0,$technician_id=0){
			$parameters = array();




				$where_tech= "";
				$where_status = "";

				if($technician_id){

					$technician_id = (int) $technician_id;
					$where_tech = " and CONCAT( ',', isr.technician_id, ',' ) LIKE '%,$technician_id,%' ";

				}

				if($status){
					$parameters[] = $status;
					$where_status = " and isr.status=? ";
				}

				$q= "select i.*, it.item_code, it.description
					from item_service_details i
					left join items it on i.item_id = it.id
					left join item_service_request isr on isr.id = i.service_id
						where 1=1 $where_tech $where_status ";

				$data = $this->_db->query($q, $parameters);


				if($data->count()){
					return $data->results();
				}

		}
		public function getRequest($cid = 0,$status = 1,$second_status=false,$date_from=0,$date_to=0,$branch_id=0,$service_type_id=0){
			$parameters = array();
			if($cid){

				$parameters[] = $cid;
				$where_second_status = "";
				$where_branch = "";
				$where_dt = "";
				$whereService='';
				if($service_type_id){
					$parameters[] = $service_type_id;
					$whereService = "and i.service_type_id = ? ";
				} else {
					if($second_status === false){
						if($status == 1){
							$wherestatus = " and i.status in (1,2,3)";
						} else {
							$parameters[] = $status;
							$wherestatus = "and i.status = ?";
						}
					}
					if($second_status !== false){
						$second_status = (int) $second_status;
						$where_second_status = " and i.second_status = $second_status ";
					}
				}

				if($date_from && $date_to){
					$date_from = strtotime($date_from);
					$date_to = strtotime($date_to . "1 day -1 min");
					$where_dt = " and i.created >= $date_from  and i.created <= $date_to";
				}

				if($branch_id){
					$parameters[] = $branch_id;
					$where_branch = " and i.branch_id = ? ";
				}


				 $q= "select st.name as service_type_name, i.* , b.name as branch_name, m.lastname as mln, m.firstname as mfn, m.middlename as mmn, u.lastname, u.firstname, u.middlename, sri.status as item_req_status
						from item_service_request i left join users u on u.id=i.user_id
						left join members m on m.id = i.member_id
						left join branches b on b.id=i.branch_id
						left join service_types st on st.id = i.service_type_id
						left join (select service_id , status from service_request_items group by service_id) sri on sri.service_id = i.id
					where i.company_id=? $whereService $wherestatus $where_second_status $where_dt $where_branch order by i.id asc";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}

			}
		}

		public function countRecord($cid,$branch_id=0,$user_id,$member_id,$service_type,$date_from,$date_to,$technician_id,$service_type_2=0){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$whereBranch = "";
				$whereUser = "";
				$whereMember = "";
				$whereDate = "";
				$whereService = "";
				$whereTechnician = "";
				$whereService2= "";
				if($branch_id){
					$parameters[] = $branch_id;
					$whereBranch = " and i.branch_id = ? ";
				}
				if($user_id){
					$parameters[] = $user_id;
					$whereUser = " and i.user_id = ? ";
				}
				if($member_id){
					$parameters[] = $member_id;
					$whereMember = " and i.member_id = ? ";
				}

				if($date_from && $date_to){
					$date_from = strtotime($date_from);
					$date_to = strtotime($date_to . "1 day -1 sec");
					$whereDate = " and i.created >= $date_from and i.created <= $date_to ";
				}

				if($service_type){
					if($service_type == 1){
						$whereService = " and i.request_type = 1";
					} else if ($service_type == 2){
						$whereService = " and i.request_type = 2";
					} else if ($service_type == 3){
						$whereService = " and i.request_type = 3";
					}
				}

				if($technician_id){
					$technician_id = (int) $technician_id;
					$whereTechnician = " and CONCAT( ',', i.technician_id, ',' ) LIKE '%,$technician_id,%' ";
				}
				if($service_type_2){
					$service_type_2 = (int) $service_type_2;
					$whereService = " and i.service_type_id = $service_type_2 ";
				}

				$q = "Select count(i.id) as cnt from item_service_request i
					left join users u on u.id=i.user_id
					left join members m on m.id = i.member_id
					left join branches b on b.id=i.branch_id
					where i.company_id=? and i.status = 4
					$whereBranch $whereUser $whereMember $whereDate $whereService $whereTechnician $whereService";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function get_record($cid,$start,$limit,$branch_id=0,$user_id,$member_id,$service_type,$date_from,$date_to,$technician_id,$service_type_2=0){
			$parameters = array();
			if($cid){
				$parameters[] = $cid;
				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}
				$whereBranch = "";
				$whereUser = "";
				$whereMember = "";
				$whereDate = "";
				$whereService = "";
				$whereTechnician="";
				$whereService2= "";
				if($branch_id){
					$parameters[] = $branch_id;
					$whereBranch = " and i.branch_id = ? ";
				}
				if($user_id){
					$parameters[] = $user_id;
					$whereUser = " and i.user_id = ? ";
				}
				if($member_id){
					$parameters[] = $member_id;
					$whereMember = " and i.member_id = ? ";
				}

				if($date_from && $date_to){
					$date_from = strtotime($date_from);
					$date_to = strtotime($date_to . "1 day -1 sec");
					$whereDate = " and i.created >= $date_from and i.created <= $date_to ";
				}
				if($service_type){
					if($service_type == 1){
						$whereService = " and i.request_type = 1";
					} else if ($service_type == 2){
						$whereService = " and i.request_type = 2";
					} else if ($service_type == 3){
						$whereService = " and i.request_type = 3";
					}
				}
				if($technician_id){
					$technician_id= (int) $technician_id;
					$whereTechnician = " and CONCAT( ',', i.technician_id, ',' ) LIKE '%,$technician_id,%' ";
				}
				if($service_type_2){
					$service_type_2 = (int) $service_type_2;
					$whereService = " and i.service_type_id = $service_type_2 ";
				}
				$q = "Select st.name as service_type_name,i.*, b.name as branch_name, m.lastname as mln, m.firstname as mfn, m.middlename as mmn, u.lastname, u.firstname, u.middlename
					from item_service_request i
					left join users u on u.id=i.user_id
					left join members m on m.id = i.member_id
					left join branches b on b.id=i.branch_id
					left join service_types st on st.id = i.service_type_id
					 where i.company_id=? and i.status = 4 $whereBranch $whereUser $whereMember $whereDate $whereService $whereTechnician $whereService order by i.id desc $l";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}

		public function getStatuses($id = 0){
			$parameters = array();
			if($id){
				$parameters[] = $id;

				$q= "Select DISTINCT(is_done) as status from item_service_details  where service_id = ?";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}

		public function getFullDetails($id=0){
			$parameters = array();
			if($id) {
				$parameters[] = $id;

				$q = "Select i.*, m.lastname as member_name ,u.firstname, u.lastname, b.name as branch_name,
						m.personal_address, st.name as service_type_name
						 from item_service_request i
					 	left join members m on m.id = i.member_id
						left join service_types st on st.id = i.service_type_id
						left join branches b on b.id = i.branch_id
						left join users u on u.id = i.user_id
						where i.id = ? ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function getTechnicians($ids=''){

			$parameters = array();

			$q = "Select * from technicians where id in ($ids))";

			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				// return the data if exists
				return $data->results();
			}

		}

	}
?>
