<?php
	class Monthly_dues extends Crud{
		protected $_table='monthly_dues';
		public function __construct($m=null){
			parent::__construct($m);
		}

		public function deleteDue($id=0){
			$parameters = array();
			if($id && is_numeric($id)){
				$parameters[] = $id;
				$q = "delete from monthly_dues where id = ?  limit 1";
				// submit the query to DB class
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return true;
				}
			}
		}
		public function deleteDetails($id=0){
			$parameters = array();
			if($id && is_numeric($id)){
				$parameters[] = $id;
				$q = "delete from monthly_due_details where id = ?  limit 1";
				// submit the query to DB class
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return true;
				}
			}
		}
		public function showDetails($id=0) {
			$parameters = array();
			if($id && is_numeric($id)){
				$parameters[] = $id;
				$q = "Select * from monthly_due_details where due_id = ? ";
				// submit the query to DB class
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}

		}
		public function getRecord($status = 1,$member_id=0,$date_from='',$date_to='',$profit_center=''){

			$parameters = array();
			$parameters[] = $status;
			$whereMember = "";
			$whereDate = "";
			$whereProfitCenter = "";
			if($member_id){
				$parameters[] = $member_id;
				$whereMember = " and d.member_id = ? ";
			}

			if($date_from && $date_to){

				$date_from = strtotime($date_from);
				$date_to = strtotime($date_to . " 1 day -1 sec");

				$whereDate = " and d.created >= $date_from and d.created <= $date_to ";

			}
			if($profit_center){
				$parameters[] = $profit_center;
				$whereProfitCenter = " and d.profit_center=?";
			}

			 $q = "SELECT d.* , m.lastname as member_name,IFNULL(md.total_paid,0) as total_paid , st.name as station_name
					FROM monthly_dues d
					left join (Select sum(amount) as total_paid,due_id
					from monthly_due_details where status ='Processed' group by due_id) md on d.id= md.due_id
					left join members m on m.id = member_id
					left join stations st on st.id = d.station_id
					where d.status = ? $whereMember $whereDate $whereProfitCenter ";
			// submit the query to DB class
			$data = $this->_db->query($q, $parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}
		}
		public function getRecordDetails($member_id=0,$date_from='',$date_to='',$profit_center='',$payment_type=0){

			$parameters = array();
			$whereMember = "";
			$whereDate = "";
			$wherePaymentType = "";
			if($member_id){
				$parameters[] = $member_id;
				$whereMember = " md.member_id = ? ";
			}

			if($date_from && $date_to){

				$date_from = strtotime($date_from);
				$date_to = strtotime($date_to . " 1 day -1 sec");

				$whereDate = " and d.dt_collected >= $date_from and d.dt_collected <= $date_to ";

			}
			if($profit_center){
				$parameters[] = $profit_center;
				$whereProfitCenter = " and md.profit_center=?";
			}

			if($payment_type){
				$parameters[] = $payment_type;
				$wherePaymentType = " and d.payment_type = ? ";
			}

			 $q = "SELECT d.* , m.lastname as member_name, md.remarks as mremarks,md.dues,md.profit_center,md.ctrl_num
					FROM monthly_due_details d
					left join  monthly_dues md on d.due_id= md.id
					left join members m on m.id = md.member_id
					where 1=1 $whereMember $whereDate $whereProfitCenter $wherePaymentType";
			// submit the query to DB class
			$data = $this->_db->query($q, $parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}
		}

		public function receiveDue($due) {
			$parameters = array();
			$parameters[] = $due->id;
			$parameters[] = $due->per_month;
			$parameters[] = time();
			$q = "INSERT INTO `monthly_due_details`(`due_id`, `amount`, `created`) VALUES (?,?,?)";
			$data = $this->_db->query($q, $parameters);
			if($data->count()){
				// return the data if exists
				return true;
			}

		}
		public function insertDetails($due_id = 0,$amount=0,$status='',$dt_collected=0,$payment_type=1,$cheque_number='',$date_matured='',$remarks='',$date_received=0,$cc_bank='') {
			$parameters = array();

			$parameters[] = $due_id;
			$parameters[] = $amount;
			$parameters[] = time();
			$parameters[] = $status;
			$parameters[] = $dt_collected;
			$parameters[] = $payment_type;
			$parameters[] = $cheque_number;
			$parameters[] = $date_matured;
			$parameters[] = $remarks;
			$parameters[] = $date_received;
			$parameters[] = $cc_bank;

			$q = "INSERT INTO `monthly_due_details`(`due_id`, `amount`, `created`,`status`,`dt_collected`,`payment_type`,`check_number`,`date_matured`,`remarks`,`date_received`,`cc_bank`) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
			$data = $this->_db->query($q, $parameters);
			if($data->count()){
				// return the data if exists
				return true;
			}

		}

		public function updateDetail($id = 0, $status ='',$remarks='',$dt_collected=''){
			$parameters = array();
			$parameters[] = $status;
			$parameters[] = $remarks;
			$parameters[] = $id;
			$parameters[] = $dt_collected;


			$q = "update `monthly_due_details` set status = ? , remarks=?, dt_collected = ? where id = ?";
			$data = $this->_db->query($q, $parameters);
			if($data->count()){
				// return the data if exists
				return true;
			}
		}
	}
