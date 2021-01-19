<?php
	class Appointment extends Crud {
		protected $_table='appointments';
		public function __construct($i = NULL){
			parent::__construct($i);
		}
		public function getDoctorSchedule($doctor_id = 0, $start = 0,$limit = 5){
			$parameters = array();
			if($doctor_id) {

				$parameters[] = $doctor_id;

				$this->where("a.doctor_id = ?");

				return $this->select("a.*, d.name as doctor_name, b.name as branch_name, s.name as type_name, m.lastname as member_name")
					->from("appointments a")
					->join("left join med_doctors d on d.id = a.doctor_id")
					->join(" left join surgery_types s on s.id = a.surgery_type")
					->join("left join branches b on b.id = a.branch_id")
					->join(" left join members m on m.id = a.member_id")
					->orderBy("a.desired_date desc,a.desired_time desc")
					->limit("$start, $limit")
					->get($parameters)
					->all();

			}
		}

		public function getAppointments($doctor_id = 0,$status=0){
			$parameters = array();

			$this->where("1=1");

			if($doctor_id){
				$this->where(" and a.doctor_id = ? ");
				$parameters[] = $doctor_id;
			}
			if($status){
				$this->where(" and a.status = ? ");
				$parameters[] = $status;
			}

			return $this->select("a.*, d.name as doctor_name, b.name as branch_name, s.name as type_name, m.lastname as member_name")
				->from("appointments a")
				->join(" left join med_doctors d on d.id = a.doctor_id")
				->join("left join surgery_types s on s.id = a.surgery_type")
				->join("left join branches b on b.id = a.branch_id")
				->join("left join members m on m.id = a.member_id")
				->orderBy("a.desired_date desc,a.desired_time desc")
				->get($parameters)
				->all();



		}
		public function getAppointmentsWeekly($dt1 = 0,$dt2=0,$branch_id =0){
			$parameters = array();
			$parameters[] = $dt1;
			$parameters[] = $dt2;
			$this->where("a.desired_date >= ? and a.desired_date <= ?");
			if($branch_id){
				$this->where(" and a.branch_id = ? ");
				$parameters[] = $branch_id;
			}
			return $this->select("a.*, d.name as doctor_name, b.name as branch_name, s.name as type_name, m.lastname as member_name")
				->from("appointments a")
				->join(" left join med_doctors d on d.id = a.doctor_id")
				->join("left join surgery_types s on s.id = a.surgery_type")
				->join("left join branches b on b.id = a.branch_id")
				->join("left join members m on m.id = a.member_id")
				->orderBy("a.desired_date asc,a.desired_time asc;");



		}

		public function getUpcoming($dt1 = 0,$dt2=0,$branch_id =0){

			$parameters = array();

			$parameters[] = $dt1;
			$parameters[] = $dt2;
			$this->where("a.status in (2,3) and a.desired_date >= ? and a.desired_date <= ?");
			$parameters[] = $branch_id;
			$this->where(" and a.branch_id = ? ");

			return $this->select("a.*, d.name as doctor_name, b.name as branch_name, s.name as type_name, m.lastname as member_name")
					->from("appointments a")
					->join(" left join med_doctors d on d.id = a.doctor_id")
					->join("left join surgery_types s on s.id = a.surgery_type")
					->join("left join branches b on b.id = a.branch_id")
					->join("left join members m on m.id = a.member_id")
					->orderBy("a.desired_date asc,a.desired_time asc")
					->get($parameters)
					->all();


		}

	}