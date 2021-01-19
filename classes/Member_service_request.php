<?php
	class Member_service_request extends Crud{
		protected $_table = 'member_service_request';
		public function __construct($m=null){
			parent::__construct($m);
		}
		public function getRequest($status = 1){
			$parameters = array();
			if($status){
				$parameters[] = $status;

				$q= "	Select msr.*,m.lastname as member_name, os.name as class_name
						from member_service_request msr left join members m on m.id = msr.member_id
 						left join offered_services os on os.id = msr.class_id where msr.status = ?";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}

		public function getBookings($from=0,$to=0){
			if($from && $to){
				$parameters = [];

				$q= "Select msr.*,m.lastname as member_name, os.name as class_name, ch.name as coach_name
					from member_service_request msr
					left join members m on m.id = msr.member_id
					left join offered_services os on os.id = msr.class_id
					left join coaches ch on ch.id = msr.coach_id
					 where msr.schedule_date >= $from and msr.schedule_date <= $to";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function getReport($search='',$service_id =0,$item_id = 0){
			$whereSearch = "";
			$whereService = "";
			$leftJoinItem = "";
			$whereItem = "";
			$parameters=[];
			if($search){
				$parameters[] = "%$search%";
				$whereSearch = " and m.lastname like ? ";
			}
			if($service_id){
				$service_id = addslashes($service_id);
				$parameters[] = $service_id;
				$whereService = " and msr.service_id = ? ";
			}

			if($item_id){
				$now = time();
				$leftJoinItem = " left join services s on s.item_id = $item_id and s.member_id = msr.member_id ";
				$whereItem = " and s.item_id = $item_id and s.consumable_qty != 0 and s.end_date <= $now";
			}

			$q = "Select m.lastname as member_name , msr.*, os.name
					from offered_services_history msr
					left join members m on m.id = msr.member_id
					left join offered_services os on os.id = msr.service_id
					$leftJoinItem
					where 1=1 $whereSearch $whereService $whereItem group by msr.member_id , msr.service_id order by m.lastname ";
			$data = $this->_db->query($q, $parameters);
			// return results if there is any
			if($data->count()){
				return $data->results();
			}
		}

	} // end class
?>