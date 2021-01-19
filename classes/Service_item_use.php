<?php
	class Service_item_use extends Crud{
		protected $_table='service_item_used';
		public function __construct($serv = NULL){
			parent::__construct($serv);
		}

		public function updateUsedItems($id){

			$parameters = array();
			if($id){

				$parameters[] = $id;
				$q = "update service_item_used set status = 1 where service_id = ? ";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}

		}

		public function getUsedItems($id,$stats=0,$technician_id=0){
			$parameters = array();
			if($id){

				$parameters[] = $id;
				$where_stats = "";
				$where_techinician = "";
				if($stats){
					$parameters[] = $stats;
					$where_stats = " and s.status=? ";
				}
				if($technician_id){
					$technician_id = (int) $technician_id;
					$whereTechnician = " and CONCAT( ',', isr.technician_id, ',' ) LIKE '%,$technician_id,%' ";
				}
				 $q = "Select s.* , i.item_code, i.description , i.barcode,isr.branch_id from  service_item_used s left join item_service_request isr on isr.id=s.service_id left join items i on i.id = s.item_id where s.service_id = ? $where_stats $whereTechnician";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function getUsedItemsAll($stats=0,$technician_id=0){
			$parameters = array();



				$where_stats = "";
				$whereTechnician = "";
				if($stats){
					$parameters[] = $stats;
					$where_stats = " and isr.status=? ";
				}
				if($technician_id){
					$technician_id = (int) $technician_id;
					$whereTechnician = " and CONCAT( ',', isr.technician_id, ',' ) LIKE '%,$technician_id,%' ";
				}
				 $q = "Select s.* , i.item_code, i.description , i.barcode,isr.branch_id from  service_item_used s left join item_service_request isr on isr.id=s.service_id left join items i on i.id = s.item_id where 1=1 $where_stats $whereTechnician";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}

		}

		public function getUsedItemsMember($id){
			$parameters = array();
			if($id){

				$parameters[] = $id;
				$q = "Select s.* , i.item_code, i.description , i.barcode, u.name as unit_name from  service_item_used s left join items i on i.id = s.item_id LEFT JOIN units u on u.id = i.unit_id where  s.member_id = ? and s.status = 0";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function countRecord($cid,$s = '') {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;

				if($s) {
					$parameters[] = "%$s%";
					$memberWhere  = " and m.lastname like ? ";
				} else {
					$memberWhere  = "";
				}

				$q = "Select count(it.id) as cnt from service_item_used it left join members m  on it.member_id = m.id  where it.company_id=? and it.is_active=1 $memberWhere";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}



		public function get_record($cid, $start, $limit,$s='') {
			$parameters = array();
			if($cid) {

				$parameters[] = $cid;
				if($limit) {
					$l = " LIMIT $start,$limit";
				} else {
					$l = '';
				}

				if($s) {
					$parameters[] = "%$s%";
					$memberWhere  = " and m.lastname like ? ";
				} else {
					$memberWhere  = "";
				}

				$q = "Select it.* ,m.lastname as member_name , i.item_code, i.description , isr.technician_id
						from service_item_used it
						left join members m on m.id = it.member_id
						left join items  i  on i.id = it.item_id
						left join item_service_request isr on isr.id = it.service_id
						where it.company_id=? and it.is_active=1 $memberWhere $l";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}

		}

		public function countRecordRequested($cid,$s = '',$date_from=0,$date_to=0,$type=0) {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$whereDate = "";
				$whereType = "";
				if($s) {
					$parameters[] = "%$s%";
					$memberWhere  = " and m.lastname like ? ";
				} else {
					$memberWhere  = "";
				}

				if($date_from  && $date_to){
					$date_from = strtotime($date_from);
					$date_to = strtotime($date_to . "1 day -1 min");
					$whereDate = " and it.created >= $date_to and it.created <= $date_from";
				}

				if($type){
					$parameters[] = $type;
					$whereType = " and isr.service_type_id = ?";
				}

				$q = "
						Select count(it.id) as cnt from item_service_details it
						left join item_service_request isr on isr.id = it.service_id
						left join members m on m.id = isr.member_id

						where it.item_id != 0 and it.company_id=? and it.is_active=1 $memberWhere $whereDate $whereType
					";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}



		public function get_record_requested($cid, $start, $limit,$s='',$date_from=0,$date_to=0,$type=0) {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				if($limit) {
					$l = " LIMIT $start,$limit";
				} else {
					$l = '';
				}

				if($s) {
					$parameters[] = "%$s%";
					$memberWhere  = " and m.lastname like ? ";
				} else {
					$memberWhere  = "";
				}

				$whereDate = "";
				$whereType = "";

				if($date_from  && $date_to){
					$date_from = strtotime($date_from);
					$date_to = strtotime($date_to . "1 day -1 min");
					$whereDate = " and it.created >= $date_to and it.created <= $date_from";
				}
				if($type){
					$parameters[] = $type;
					$whereType = " and isr.service_type_id = ?";
				}
				$q = "Select it.* ,m.lastname as member_name , i.item_code, i.description,isr.technician_id, isr.invoice,isr.dr
					from item_service_details it
					left join item_service_request isr on isr.id = it.service_id
					left join members m on m.id = isr.member_id
					left join items  i  on i.id = it.item_id
					where it.item_id != 0 and it.company_id=? and it.is_active=1
					$memberWhere  $whereDate $whereType order by it.service_id desc $l";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}
	}
?>