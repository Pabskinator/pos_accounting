<?php
	class Assemble_details extends Crud{
		protected $_table = 'assemble_details';
		public function __construct($w=null){
			parent::__construct($w);
		}
		public function getDetails($id = 0){
			if($id){
				$parameters = [];
				$parameters[] = $id;
				$q = "Select a.*, i.item_code,i.description , i.has_serial
				from assemble_details a left join items i on i.id=a.item_id_set where a.assemble_id=?";
				$data = $this->_db->query($q,$parameters);
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function getDataPrint($id= 0){
			$parameters = [];
			$parameters[] = $id;
			$q = "Select a.* from assemble_request a left join wh_orders wh on wh.id = a.wh_id left join members m on m.id=wh.member_id left join sales_type st on ";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				return $data->first();
			}
		}
	}
?>