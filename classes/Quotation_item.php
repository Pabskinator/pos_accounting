<?php
	class Quotation_item extends Crud{
		protected $_table = 'quotation_items';
		public function __construct($q=null){
			parent::__construct($q);
		}

		public function getItems($id){
			$parameters = array();
			if ($id) {
				$parameters[] = $id;
				$q = "Select q.*, i.item_code, i.description , q.description as qdesc from quotation_items q
						left join items i on i.id = q.item_id
						where q.quotation_id = ?

						 ";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function deleteItems($id){
			$parameters = array();
			if ($id) {
				$parameters[] = $id;
				$q = "Delete from quotation_items
						where quotation_id = ?

						 ";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return true;
				}
			}
		}



	}
?>