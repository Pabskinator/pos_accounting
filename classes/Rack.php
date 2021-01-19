<?php

	class Rack extends Crud {
		protected $_table = 'racks';

		public function __construct($rack = null) {
			parent::__construct($rack);
		}

		public function rackJSON($cid = 0, $branch_id = 0, $search = '', $tags = "") {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$whereBranch = '';
				$whereSearch = '';
				if($branch_id) {
					$parameters[] = $branch_id;
					$whereBranch = "and r.branch_id = ?";
				}
				if($search) {
					$parameters[] = "%$search%";
					$whereSearch = "and r.rack like ? ";
				}
				$lefttags = "";
				$wheretag = "";
				if($tags) {

					$lefttags = "left join rack_tags rt on rt.id = r.rack_tag";
					$explode = explode(',',$tags);
					$lid = "";
					foreach($explode as $ex){
						$ex = (int) $ex;
						$lid .= $ex . ",";
					}
					rtrim($lid,",");
					$wheretag = " and r.rack_tag in ($tags)";
				}

				$q = "Select r.* from racks r $lefttags  where r.is_active = 1 and r.company_id = ? $whereBranch $whereSearch $wheretag";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function isRackExists($name = '', $companyid = 0, $getid = false, $branch_id = 0) {
			$parameters = array();
			if($name) {
				$parameters[] = $name;
				$parameters[] = $companyid;
				$branch_id_where = "";
				if($branch_id) {
					$branch_id_where = " and branch_id = ? ";
					$parameters[] = $branch_id;
				}
				$q = "Select id,rack from racks  where  rack=? and is_active=1 and company_id=? $branch_id_where ";
				$e = $this->_db->query($q, $parameters);
				if($e->count()) {
					return ($getid) ? $e->first() : true;
				}

				return false;
			}
		}

		public function rackGroup($c) {
			$parameters = array();
			if($c) {

				$parameters[] = $c;
				$q = "Select DISTINCT(SUBSTRING(rack,1,2)) as rack from racks  where  rack like '%-%' and is_active=1 and company_id=? order by rack";
				$e = $this->_db->query($q, $parameters);
				if($e->count()) {
					return $e->results();
				}

				return false;
			}
		}
		public function rackGroupInformation($rack,$branch_id) {
			$parameters = array();
			if($rack && $branch_id) {

				$parameters[] = $rack;
				$parameters[] = $branch_id;

				$q = "Select *  from racks where SUBSTRING(rack,1,2) like ?  and is_active=1 and branch_id = ? order by rack";
				$e = $this->_db->query($q, $parameters);
				if($e->count()) {
					return $e->results();
				}

				return false;
			}
		}

		public function isRackDefaultExists($branch_id = 0) {
			$parameters = array();
			if($branch_id) {
				$parameters[] = $branch_id;
				$q = 'Select count(*) as cnt from default_racks where branch_id = ?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()) {
					return $e->first();
				}

				return false;
			}
		}

		public function updateRackDefault($branch_id = 0) {
			$parameters = array();
			if($branch_id) {
				$parameters[] = $branch_id;
				$q = 'update racks set is_default = 0 where branch_id = ? and is_default=1';
				$e = $this->_db->query($q, $parameters);
				if($e->count()) {
					return true;
				}

				return false;
			}
		}

		public function getRackForSelling($branch_id = 0) {
			$parameters = array();
			if($branch_id) {
				$parameters[] = $branch_id;
				$q = 'select rack,id from racks  where branch_id = ? and is_default = 1 limit 1';
				$e = $this->_db->query($q, $parameters);
				if($e->count()) {
					return $e->first();
				}

				return false;
			}
		}

		public function getRackDefaults($branch_id = 0) {
			$parameters = array();
			if($branch_id) {
				$parameters[] = $branch_id;
				$q = 'Select d.*, r1.rack as rack_good, r2.rack as rack_issues, r3.rack as rack_surplus,r4.rack as rack_bo
						from default_racks d left join racks r1 on r1.id = d.good_rack
						left join racks r2 on r2.id = d.issues_rack
						left join racks r3 on r3.id = d.surplus_rack
						left join racks r4 on r4.id = d.bo_section
						where d.branch_id = ?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()) {
					return $e->first();
				}

				return false;
			}
		}

		public function updateDefault($branch_id = 0, $good_rack = 0, $issues_rack = 0, $surplus_rack = 0,$bo=0) {
			$parameters = array();
			if($branch_id && $good_rack && $issues_rack) {
				$parameters[] = $good_rack;
				$parameters[] = $issues_rack;
				$parameters[] = $surplus_rack;
				$parameters[] = $bo;
				$parameters[] = $branch_id;
				$q = 'update default_racks set good_rack = ? , issues_rack = ?, surplus_rack = ? , bo_section = ? where branch_id = ?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()) {
					return true;
				}

				return false;
			}
		}

		public function insertDefault($branch_id = 0, $good_rack = 0, $issues_rack = 0, $company_id = 0, $surplus_rack = 0,$bo=0) {
			$parameters = array();
			if($branch_id && $good_rack && $issues_rack) {
				$parameters[] = $branch_id;
				$parameters[] = $good_rack;
				$parameters[] = $issues_rack;
				$parameters[] = $surplus_rack;
				$parameters[] = $bo;
				$parameters[] = $company_id;
				$q = 'INSERT INTO `default_racks`(`branch_id`, `good_rack`, `issues_rack`, `surplus_rack`,`bo_section`, `company_id`, `is_active`) VALUES (?,?,?,?,?,?,1)';
				$e = $this->_db->query($q, $parameters);
				if($e->count()) {
					return true;
				}

				return false;
			}
		}

		function getRackDisplayId($cid = 0) {
			if($cid) {
				$parameters = array();
				$parameters[] = $cid;
				$q = 'Select id from racks  where  rack="Display" and is_active=1 and company_id=?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()) {
					return $e->first();
				}
			}
		}

		public function getAllRacks($cid) {
			if($cid) {
				$parameters = array();
				$parameters[] = $cid;
				$q = 'Select * from racks  where is_active=1 and company_id=? order by rack';
				$e = $this->_db->query($q, $parameters);
				if($e->count()) {
					return $e->results();
				}
			}
		}
		public function getDetailedRacks($cid,$branch_id=0) {
			if($cid) {
				$parameters = array();
				$parameters[] = $cid;
				$branchWhere = "";
				$method = "results";
				if($branch_id){
					$parameters[] = $branch_id;
					$branchWhere = " and r.branch_id = ?";

				}
				$q = "Select r.*,rt.tag_name,b.name as branch_name from racks r left join rack_tags rt on rt.id=r.rack_tag left join branches b on b.id = r.branch_id where r.is_active=1 and r.company_id=? $branchWhere order by r.rack";
				$e = $this->_db->query($q, $parameters);
				if($e->count()) {
					return $e->$method();
				}
			}
		}
		public function getBranchRacks($b) {
			if($b) {
				$parameters = array();
				$parameters[] = $b;
				$q = 'Select r.id,r.rack
		from inventories i left join racks r on r.id = i.rack_id where i.branch_id = ? group by i.rack_id order by r.rack';
				$e = $this->_db->query($q, $parameters);
				if($e->count()) {
					return $e->results();
				}
			}
		}

		public function getRackName($id = 0) {
			if($id) {
				$parameters = array();
				$parameters[] = $id;
				$q = 'Select rack,id from racks  where id=?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()) {
					return $e->first();
				}
			}
		}

		public function getAssignedPerson($rack_id) {
			if($rack_id) {
				$parameters = array();
				$parameters[] = $rack_id;
				$q = 'Select u.lastname, u.firstname, u.middlename from racks r left join users u on u.id=r.user_id where r.id=?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()) {
					return $e->first();
				}
			}
		}
	}

?>