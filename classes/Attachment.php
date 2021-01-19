<?php
	class Attachment extends Crud{
		protected $_table = 'attachments';
		public function __construct($attachment=null){
			parent::__construct($attachment);
		}
		public function getAttachments($mon_id){
			if($mon_id){
				$parameters = array();
				$parameters[] = $mon_id;
				 $q = "Select * from attachments where monitoring_id = ? and is_active=1";
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
			}
		}

	}
?>