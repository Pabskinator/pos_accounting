<?php
	class Rack_audit_sp extends Crud{
		protected $_table = 'rack_audit_sp';
		public function __construct($r=null){
			parent::__construct($r);
		}

		public function getRackAudit($r,$b,$c){
			$parameters = array();
				if($c && $b){
					// set the company
					$parameters[] = $r;
					$parameters[] = $b;
					$parameters[] = $c;
					
					
					$q = 'Select * from rack_audit_sp where rack_id=? and branch_id=? and company_id=? order by created desc limit 1';
				
					// submit the query to DB class
					$data = $this->_db->query($q, $parameters);
					if($data->count()){
						// return the data if exists
						return $data->first();
					}
				}
		}


		public function getAuditHis($r,$b,$c){
			$parameters = array();
				if($c && $b){
					// set the company
					$parameters[] = $r;
					$parameters[] = $b;
					$parameters[] = $c;
					
					
					$q = 'Select * from rack_audit_sp where rack_id=? and branch_id=? and company_id=? and status != 1 order by created desc';
				
					// submit the query to DB class
					$data = $this->_db->query($q, $parameters);
					if($data->count()){
						// return the data if exists
						return $data->results();
					}
				}
		}

		public function isAuditing($r,$b){
				$parameters = array();
				if($r && $b){
					// set the company
					$parameters[] = $r;
					$parameters[] = $b;
					
					$q = 'Select count(*) as cnt from rack_audit_sp where rack_id=? and branch_id=? and status=1';
				
					// submit the query to DB class
					$data = $this->_db->query($q, $parameters);
					if($data->count()){
						// return the data if exists
						return $data->first();
					}
				}
		}
		public function getStillAuditing($b){
			$parameters = array();
			if($b){
				// set the company

				$parameters[] = $b;
				 $q = 'Select  rs.id, r.rack,rs.rack_id from rack_audit_sp rs left join racks r on r.id=rs.rack_id where rs.branch_id=? and rs.status=1';

				// submit the query to DB class
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}

			}
		}

	}
