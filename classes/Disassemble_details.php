<?php
	class Disassemble_details extends Crud{
		protected $_table = 'disassemble_details';
		public function __construct($w=null){
			parent::__construct($w);
		}
		public function getDetails($id = 0){
			if($id){
				$parameters = [];
				$parameters[] = $id;
				$q = "Select a.*, i.item_code,i.description from disassemble_details a left join items i on i.id=a.item_id_set where a.disassemble_id=?";
				$data = $this->_db->query($q,$parameters);
				if($data->count()){
					return $data->results();
				}
			}
		}
	}
?>