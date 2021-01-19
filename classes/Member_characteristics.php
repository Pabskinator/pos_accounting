<?php
	class Member_characteristics extends Crud{
		protected $_table='member_characteristics';
		public function __construct($item_characteristics = NULL){
			parent::__construct($item_characteristics);
		}
		public function getMyCharacteristicsd($id){
			$parameters = array();
			if($id){
				// set the price id
				$parameters[] = $id;
				$q= 'Select mc.mem_char_id, mcl.name from member_characteristics mc left join member_characteristics_list mcl on mcl.id=mc.mem_char_id where  mc.member_id=? ';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function deleteMyCharacteristics($id){
			$parameters = array();
			if($id){

				$parameters[] = $id;
				$q= 'delete  from member_characteristics where  member_id=? ';

				if($this->_db->query($q, $parameters)){
					// return the data if exists
					return true;
				}
				return false;
			}
		}
	}
?>