<?php
	class Spare_type extends Crud{
		protected $_table = 'spare_type';
		public function __construct($s=null) {
			parent::__construct($s);
		}
		public function getType($item_id = 0){
			$parameters = [];
			if($item_id){
				$parameters[] = $item_id;
				$q = "select s.name from items i left join spare_type s on s.id = i.spare_type where i.id=?";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
				return false;
			}
			return false;
		}
	}