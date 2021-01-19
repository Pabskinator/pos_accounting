<?php
	class Biller_request_data extends Crud{
		protected $_table = 'biller_request_data';
		public function __construct($b=null){
			parent::__construct($b);
		}
		public function getRequest($cid=0,$status=0,$user_id){
			if($cid && $status){
				$parameters[] = $cid;
				$parameters[] = $status;
				$whereUser = "";
				if($user_id){
					$parameters[] = $user_id;
					$whereUser = " and b.user_id = ? ";
				}
				$q= "Select b.* , bn.name as biller_name, u.lastname  , u.firstname , u.middlename from biller_request_data b left join biller_names as bn on bn.id = b.biller_id left join users u on u.id = b.user_id where b.company_id = ? and b.status = ? $whereUser ";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}
	}