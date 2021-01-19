<?php
	class Amend_upload extends Crud {
		protected $_table='amend_uploads';
		public function __construct($i = NULL){
			parent::__construct($i);
		}

		public function getAttach($aid,$item_id){
			$parameters = array();
			if($aid) {
				$parameters[] = $aid;
				$parameters[] = $item_id;

				return $this->select("path")
					->from("amend_uploads")
					->where("audit_id = ?")
					->where("and item_id = ?")
					->get($parameters)
					->all();

			}
		}

		public function getAttachAllRack($item_id,$rack_id){
			$parameters = array();
			if($item_id) {
				$parameters[] = $item_id;
				$parameters[] = $rack_id;

				$this->select("a.path")
					->from("amend_uploads a")
					->join("left join inventory_ammend i on i.audit_id = a.audit_id")
					->where(" a.item_id = ?")
					->where("and i.rack_id = ?")
					->get($parameters)
					->all();

			}
		}
	}