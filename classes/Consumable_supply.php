<?php
	class Consumable_supply extends Crud{
		protected $_table = 'consumable_supplies';
		public function __construct($c=null){
			parent::__construct($c);
		}
		public function getForApproval($cid = 0,$status = 1) {
			$parameters = array();
			$parameters[] = $cid;
			$parameters[] = $status;
			$q = 'Select s.*,b.name as bname,b.address as baddress, u.lastname,u.firstname,u.middlename ,
				 u2.lastname as for_lastname,u2.firstname as for_firstname ,u2.middlename as for_middlename, m.lastname as mln,
				   m.firstname as mfn,m.middlename as mmn, b2.name as user_branch
  				from consumable_supplies s
  				left join branches b on s.branch_id=b.id

  				left join members m on m.id = s.member_id
  				left join users u on u.id = s.user_id
  				left join users u2 on u2.id = s.for_user_id
  				left join branches b2 on b2.id = u2.branch_id
  				where  s.company_id = ? and s.status =?  order by s.id desc ';
			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				// return the data if exists
				return $data->results();
			}
		}
		public function getInfo($id=0) {
			$parameters = array();
			$parameters[] = $id;

			$q = 'Select s.*,b.name as bname,b.address as baddress, u.lastname,u.firstname,u.middlename ,
				 u2.lastname as for_lastname,u2.firstname as for_firstname ,u2.middlename as for_middlename, m.lastname as mln,
				   m.firstname as mfn,m.middlename as mmn, b2.name as user_branch
  				from consumable_supplies s
  				left join branches b on s.branch_id=b.id

  				left join members m on m.id = s.member_id
  				left join users u on u.id = s.user_id
  				left join users u2 on u2.id = s.for_user_id
  				left join branches b2 on b2.id = u2.branch_id
  				where  s.id = ? limit 1';
			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				// return the data if exists
				return $data->first();
			}
		}
	}
?>