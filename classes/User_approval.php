<?php
/*
   If you’re reading this,
   that means you have been put in charge of my previous project.
   I am so, so sorry for you.
   This code sucks, you know it and I know it. Move on and call me an idiot later
   God speed.
*/
	
class User_approval extends Crud{
		protected $_table = 'user_approvals';
		public function __construct($user_approval=null){
			parent::__construct($user_approval);
		}
		public function getLog($mon_id=0){
			if($mon_id){
				$parameters = array();
				$parameters[] = $mon_id;
				$q = 'Select f.lastname,f.firstname,f.middlename, s.name,u.created, u.user_id,u.step_id,u.remarks from user_approvals u left join users f on f.id=u.user_id left join steps s on u.step_id = s.id where u.monitoring_id = ?';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
						// return the data if exists
						return $data->results();
				}
			}
		}
	}
?>