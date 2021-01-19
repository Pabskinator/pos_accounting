<?php
	class Workout_module_member  extends  Crud {
		protected $_table = 'workout_module_members';
		public function __construct($e=null){
			parent::__construct($e);
		}
		public function getModules(){
			$parameters = array();
			$cid = 1;
			if($cid) {
				$parameters[] = $cid;

				$q = "Select w.*, m.lastname as member_name,wm.name as module_name from workout_module_members w left join members m on m.id = w.member_id left join workout_module wm on wm.id = w.module_id where w.company_id=? and w.is_active = 1";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}
	}