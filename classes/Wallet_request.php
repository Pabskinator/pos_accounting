<?php
	class Wallet_request extends Crud{
		protected $_table = 'wallet_request';
		public function __construct($w=null){
			parent::__construct($w);
		}
		public function getRequest($cid=0,$uid=0,$status=0){

			if($cid){
				$parameters[] = $cid;
				$whereUser = "";
				$whereStatus =" and w.status = 1 ";
				if($uid){
					$parameters[] = $uid;
					$whereUser = " and w.user_id = ? ";
				}
				if($status){
					$parameters[] = $status;
					$whereStatus = " and w.status = ? ";
				}


				$q= "Select w.*, u.lastname, u.firstname, u.middlename, b.name as branch_name from wallet_request w left join users u on u.id = w.user_id left join branches b on b.id = u.branch_id where w.is_active=1 and w.company_id = ? $whereUser $whereStatus";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}

		}
	}