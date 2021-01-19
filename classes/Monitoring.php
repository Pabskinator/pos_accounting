<?php
	class Monitoring extends Crud{
		protected $_table = 'monitorings';
		public function __construct($monitoring=null){
			parent::__construct($monitoring);
		}
		public function getMonitoring($process_id = 0, $cur_step = 0){
			$parameters = array();
			if($process_id){

				$parameters[] = $process_id;
				if($cur_step){
					$parameters[] = $cur_step;
					$wherecurstep = 'and current_step=?';
				} else {
					$wherecurstep ='';
				}
				

				$q = 'SELECT * FROM monitorings where process_id=? '.$wherecurstep.' and is_active=1';

				// submit the query to DB class
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}

		}
		public function countPending($process_id = 0, $cur_step = 0){
			$parameters = array();
			if($process_id && $cur_step){

				$parameters[] = $process_id;
				$parameters[] = $cur_step;
				$q = 'SELECT count(id) as countPending FROM monitorings where process_id=? and current_step=? and is_active=1';

				// submit the query to DB class
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
			}

		}
		public function getMyRequest($user_id= 0){
			$parameters = array();
			if($user_id ){

				$parameters[] = $user_id;


				$q = 'SELECT * FROM monitorings where who_request=? and is_active=1 and current_step not in (-1,-2) order by process_id';

				// submit the query to DB class
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function allRequest(){
			$parameters = array();
				$parameters[] = 1;
				 $q = 'SELECT m.*,u.lastname,u.firstname, u.middlename, p.name as process_name FROM monitorings m left join processes p on p.id = m.process_id left join users u on u.id= m.who_request where m.is_active=1 and 1=? order by m.process_id';
				// submit the query to DB class
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
		}
	public function getDeclineRequest($user_id= 0){
			$parameters = array();
			if($user_id ){

				$parameters[] = $user_id;
				$q = 'SELECT * FROM monitorings where who_request=? and is_active=1 and current_step=-1 order by process_id';
				// submit the query to DB class
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}

		}
	
	public function getApprovedRequest($user_id= 0){
			$parameters = array();
			if($user_id ){
				$parameters[] = $user_id;
				$q = 'SELECT * FROM monitorings where who_request=? and is_active=1 and current_step=-2 order by process_id';

				// submit the query to DB class
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}

		}
	public function countRecord($cid,$p,$s){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;

				if($p) {
					$parameters[] = $p;
					$processwhere = " and m.process_id = ?";
				
				} else {
					$processwhere = '';
				}

				if($s) {
					$parameters[] = $s;
					$stepwhere = " and s.id=? ";
				} else {
					$stepwhere = "";
				}
			
				$q = "Select count(m.id) as cnt from monitorings m left join processes p on p.id=m.process_id left join steps s on  m.current_step = s.step_number where m.company_id=? and m.is_active=1 $processwhere $stepwhere";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function get_mon_record($cid,$start,$limit,$p,$s){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;

				if($p) {
					$parameters[] = $p;
					$processwhere = " and m.process_id = ?";
				
				} else {
					$processwhere = '';
				}

				if($s) {
					$parameters[] = $s;
					if($s == -1 || $s == -2){
						$stepwhere = " and m.current_step=? ";
					} else {
						$stepwhere = " and s.id=? ";
					}
					
				} else {
					$stepwhere = "";
				}
			
				$q = "Select m.*,p.name as pname, s.name as sname from monitorings m left join processes p on p.id=m.process_id left join steps s on  m.current_step = s.step_number where m.company_id=? and m.is_active=1 $processwhere $stepwhere";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

	} // end class
?>