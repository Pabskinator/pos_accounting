<?php
	class Service_attendance extends Crud{
		protected $_table='service_attendance';

		public function __construct($serv = NULL){
			parent::__construct($serv);
		}
		public function countRecord($dt=0){
			$parameters = array();

				$parameters[] = 1;
				$where_dt = "";
				if($dt){
					$dt1 = strtotime($dt);
					$dt2 = strtotime($dt . "1 day -1 min");
					$where_dt = " and s.time_in >= $dt1 and s.time_in <= $dt2 ";
				}
				$q = "Select count(s.id) as cnt from service_attendance s where 1 = ? $where_dt ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}

		}
		public function get_record($start, $limit,$dt=0,$dt2=0) {
			$parameters = array();

				$parameters[] = 1;
				$where_dt = "";
				if($dt && !$dt2){
					$dt1 = strtotime($dt);
					$dt2 = strtotime($dt . "1 day -1 min");
					$where_dt = " and s.time_in >= $dt1 and s.time_in <= $dt2 ";
				} else if ($dt && $dt2){
					$where_dt = " and s.time_in >= $dt and s.time_in <= $dt2 ";
				}
				if($limit) {
					$l = " LIMIT $start,$limit";
				} else {
					$l = '';
				}

				$q = "Select  s.*, m.lastname as member_name, i.item_code, co.name as coach_name from service_attendance s left join coaches co on co.id = s.coach_id left join items i on i.id = s.item_id left join members m on m.id = s.member_id where 1 = ?  $where_dt order by s.id desc $l ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}

		}
		public function getAllSignIn($company_id){
			$parameters = array();

			if($company_id){
				$parameters[] = $company_id;

				$q= "Select s.*, m.lastname as member_name from service_attendance s left join members m on m.id = s.member_id where s.time_out = 0 ";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function alreadySignedIn($member_id){
			$parameters = array();

			if($member_id){
				$parameters[] = $member_id;
				$now = date('Y-m-d',time());
				$q= "Select count(*) as cnt from service_attendance where DATE(FROM_UNIXTIME(time_in)) = '$now' and time_out = 0 and member_id = ? ";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function addtlExpi(){

			$parameters = array();


			$q= "Select id,sum(exp) as exp,member_id from addtl_experience group by member_id";
			$data = $this->_db->query($q, $parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}
		}
		public function expiSummary(){
			$parameters = array();


			$q= "Select
			count(s.id) as cnt, s.member_id ,s.is_con , m.lastname
			from service_attendance s
			left join members m on m.id = s.member_id
			group by member_id,is_con ";
			$data = $this->_db->query($q, $parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}

		}

		public function getAttendanceByMember($member_id=0) {
			$parameters = array();

			$parameters[] = $member_id;


			$q = "Select  s.*, m.lastname as member_name, i.item_code, co.name as coach_name from service_attendance s left join coaches co on co.id = s.coach_id left join items i on i.id = s.item_id left join members m on m.id = s.member_id where s.member_id = ? order by s.id desc ";
			$data = $this->_db->query($q, $parameters);
			// return results if there is any
			if($data->count()) {
				return $data->results();
			}

		}

	}
?>