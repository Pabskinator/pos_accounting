<?php
	class Unliquidated extends Crud{
		protected $_table='unliquidated';
		public function __construct($u = NULL){
			parent::__construct($u);
		}
		public function getUnliquidated($companyid=0,$ctype=0){
			$parameters = array();
			if($companyid){
				$parameters[] =$companyid;
				$parameters[] = $ctype;
				$q= 'select a.* from agent_request a left join unliquidated u on u.request_id = a.id where a.company_id=? and a.is_active=1 and u.status = ? group by u.request_id';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
			}
		}
		public function receivePayment($req_id=0){
			$parameters = array();
			if($req_id){
				$parameters[] =$req_id;
				$q= 'Update unliquidated set status = 2 where status = 1 and request_id=?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return true;
				}
				return false;
			}
		}
	}
?>