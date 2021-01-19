<?php
	class Cr_log_ids extends Crud {
		protected $_table = 'cr_log_ids';

		public function __construct($cr = null) {
			parent::__construct($cr);
		}

		public function deleteCrDetails($payment_id = 0,$type_id =0){

			if($payment_id && is_numeric($payment_id) && $type_id && is_numeric($type_id)){

				$parameters = [];
				$parameters[] = $payment_id;
				$parameters[] = $type_id;

				$q= 'delete from cr_log_ids where payment_id = ? and type_id = ? limit 1';

				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return true;
				}
				return false;

			}

		}

		public function getByPaymentIds($pids = ""){

			if($pids){

				$parameters = [];

				$q= 'Select * from cr_log_ids  where  payment_id in ('.$pids.')';

				$e = $this->_db->query($q, $parameters);

				if($e->count()){
					return $e->results();
				}

				return false;
			}

		}

		public function getByPaymentId($pid = 0){

			if($pid){
				$parameters[] = $pid;

				$q= 'Select * from cr_log_ids  where  payment_id=?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
				return false;
			}
		}

		public function deleteCr($cr_num = 0){

			if($cr_num){
				$parameters[] = $cr_num;

				$q= 'delete from cr_log_ids where id = ? ';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return true;
				}
				return false;
			}
		}

	}