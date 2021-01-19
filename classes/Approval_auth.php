<?php
	class Approval_auth extends Crud {
		protected $_table='approval_auths';
		public function __construct($i = NULL){
			parent::__construct($i);
		}
		public function getMyAuth($id){
			$parameters = array();
			if($id){
				$parameters[] =$id;

				return $this->select(" `id` , `ref_values`")
					->from("approval_auths")
					->where("user_id = ? and is_active=1")
					->get($parameters)
					->first();
			}
		}
	}