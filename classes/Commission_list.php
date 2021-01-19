<?php


	class Commission_list extends Crud{
		protected $_table = 'commission_list';
		public function __construct($c=null){
			parent::__construct($c);
		}
		public function getCommission($status=0,$dt1=0,$dt2=0){
			$parameters = array();
			if($status == 1){
				$group_by = "group by com.agent_id, com.pay_date ";
				$order_by="com.pay_date";
			} else {
				$group_by = "group by com.agent_id";
				$order_by="com.agent_id";
			}
			$whereDate = "";
			if($dt1 && $dt2){
				$dt1 = strtotime($dt1);
				$dt2 = strtotime($dt2 . "1 day -1 min");
				$whereDate = " and com.created >= $dt1 and com.created <= $dt2";
			}

			$status = (int) $status;

			$q= "Select u.firstname, u.lastname, sum(com.amount) as total_pending,com.agent_id, com.pay_date
					from commission_list com
					left join users u on u.id = com.agent_id
					where com.status = $status
					$whereDate
					$group_by
					order by $order_by
					";

			$data = $this->_db->query($q, $parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}

		}

		public function payCommission($id,$now,$dt1,$dt2){
			$parameters = array();
			$parameters[] = $now;
			$parameters[] = $id;
			$whereDate = "";
			if($dt1 && $dt2){
				$dt1 = strtotime($dt1);
				$dt2 = strtotime($dt2 . "1 day -1 min");
				$whereDate = " and created >= $dt1 and created <= $dt2";
			}
			$q= "update commission_list set status =1 , pay_date = ? where agent_id = ? and status = 0 $whereDate";

			$data = $this->_db->query($q, $parameters);
			if($data->count()){
				// return the data if exists
				return true;
			}
			return false;

		}
		public function getDetails($id,$pay_date,$status,$dt1,$dt2){
			if($id){
				$parameters = array();
				$parameters[] = $id;
				$parameters[] = $pay_date;
				$parameters[] = $status;
				$whereDate = "";
				if(!$status){
					if($dt1 && $dt2){
						$dt1 = strtotime($dt1);
						$dt2 = strtotime($dt2 . "1 day -1 min");
						$whereDate = " and com.created >= $dt1 and com.created <= $dt2";
					}

				}

				 $q= "Select com.*, i.item_code, i.description from commission_list com left join items i on i.id = com.item_id
					where com.agent_id = ? and com.pay_date= ? and com.status = ? $whereDate ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}
	}
