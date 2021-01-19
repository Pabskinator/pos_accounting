<?php
	class Commission_item extends Crud{
		protected $_table = 'commission_items';
		public function __construct($c=null){
			parent::__construct($c);
		}

		public function hasComission($item_id,$agent_id=0){
			$parameters = array();
			$parameters[] = $item_id;
			$whereAgent = "";
			if($agent_id){
				$whereAgent =  " and agent_id = ? ";
				$parameters[] = $agent_id;
			}
			$q= "Select amount, perc,id from commission_items where item_id = ? and is_active = 1 $whereAgent  order by id desc limit 1";

			$data = $this->_db->query($q, $parameters);
			if($data->count()){
				// return the data if exists
				return $data->first();
			}
			return false;

		}

		public function getCommissions(){

			$parameters = array();



			$q= "Select ci.*, i.item_code, i.description, u.lastname, u.firstname
 					from commission_items ci
 					left join items i on i.id = ci.item_id
 					left join users u on u.id = ci.agent_id
 					where ci.is_active = 1";

			$data = $this->_db->query($q, $parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}
			return false;

		}
	}
?>