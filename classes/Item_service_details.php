<?php
	class Item_service_details extends Crud{
		protected $_table='item_service_details';
		public function __construct($item = NULL){
			parent::__construct($item);
		}

		public function getService($service_type_id = 0,$dt_from = 0,$dt_to=0,$is_done=0){
			$parameters = array();

			$whereServiceType = "";
			$whereDt= "";
			$whereStatus= "";

			if($service_type_id){
				$parameters[] = $service_type_id;
				$whereServiceType = " and isr.service_type_id = ? ";
			}
			if($dt_from && $dt_to){

				$dt_from = strtotime($dt_from);
				$dt_to = strtotime($dt_to . "1 day -1 sec");
				$parameters[] = $dt_from;
				$parameters[] = $dt_to;
				$whereDt = " and isr.created >= ? and isr.created <= ? ";

			}

			if($is_done){
				$parameters[] = $is_done;
				$whereStatus = " and s.is_done = ? ";
			}

			 $q= "		Select i.item_code, i.barcode,st.name as service_type_name, i.description ,isr.status, s.*,isr.client_po, wop.amount as amount_paid, m.lastname as member_name
                        from item_service_details s
                        left join items i on i.id=s.item_id
                        left join item_service_request isr on isr.id = s.service_id
                        left join service_types st on st.id = isr.service_type_id
                        left join members m on m.id = isr.member_id
                        left join wh_order_payments wop on wop.client_po = isr.client_po and wop.item_name = i.description and wop.transaction_type = 'Orders-Item Charges'
                        where s.is_done in (101,102) $whereServiceType $whereDt $whereStatus
                        ";
			$data = $this->_db->query($q, $parameters);

			if($data->count()){
				return $data->results();
			}


		}

		public function getDetails($service_id = 0){
			$parameters = array();

			if($service_id){

				$parameters[] = $service_id;
				 $q= "Select i.item_code, i.barcode, i.description , s.* from item_service_details s left join items i on i.id=s.item_id where s.service_id=?";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function updateDetNeededSp($req_id=0,$item_id,$needed_sp){
			if($req_id && $item_id && $needed_sp){

				$parameters[] = $needed_sp;
				$parameters[] = $req_id;
				$parameters[] = $item_id;

				$q= "update item_service_details set sp_needed=? where service_id=? and item_id=?";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return true;
				}
			}
		}

	}
?>