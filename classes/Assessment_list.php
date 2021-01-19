<?php
	class Assessment_list extends Crud{
		protected $_table='assessment_list';
		public function __construct($o=null){
			parent::__construct($o);
		}
		public function getAssessment($id = 0){
			$parameters = array();
			$whereId = "";
			if($id){
				$parameters[] = $id;
				$whereId = " and a.disc_id = ? ";
			}

			$q= "Select a.*, o.name as disc_name from assessment_list a left join offered_services o on o.id = a.disc_id where 1=1 $whereId order by o.name asc, a.grp asc, a.name asc";

			$data = $this->_db->query($q, $parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}

		}

		public function getHistory($member_id = 0){
			$parameters = array();
			$whereId = "";
			if($member_id){
				$whereId = " and a.member_id = $member_id ";
			}

			$q = "Select a.*, co.name as coach_name , m.lastname as member_name, os.name as disc_name from assessments a left join coaches co on co.id = a.coach_id left join offered_services os on os.id = a.disc_id
					left join members m on m.id = a.member_id where 1 = 1 $whereId order by created desc";

			$data = $this->_db->query($q, $parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}

		}
		public function getDetailsAssessment($id = 0){
			$parameters = array();

			$parameters[] = $id;

			$q= "Select am.*,al.name as aname, al.grp from assessment_members am left join assessment_list al on al.id = am.assessment_id where am.parent_id = ? ";

			$data = $this->_db->query($q, $parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}

		}
	}
