<?php
	class Referral extends Crud {

		protected $_table = 'referrals';

		public function __construct($r = null) {
			parent::__construct($r);
		}

		public function get_all(){

				$parameters = array();

				$q= "Select r.*, m1.lastname as member_name, m2.lastname as referred_by from referrals r left join members m1 on m1.id = r.member_id left join members m2 on m2.id = r.referred_by where 1=1";
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
				return false;

		}

	}
