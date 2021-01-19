<?php
	class Borrowed_part extends Crud {
		protected $_table = 'borrowed_parts';

		public function __construct($b = null) {
			parent::__construct($b);
		}

		public function getRecords($status = 1){
			if($status) {
				$parameters = array();
				$parameters[] = $status;

				$q = "
						select b.*, bn.name as branch_name,
						i.item_code, i.description, r.rack, u.firstname ,
						 u.lastname, u2.firstname as firstname2, u2.lastname as lastname2
						from borrowed_parts b
						left join users u on u.id = b.user_id
						left join users u2 on u2.id = b.returned_by
					  	left join branches bn on bn.id = b.branch_id
					  	left join items i on i.id = b.item_id
					  	left join racks r on r.id = b.from_rack_id
						where b.status = ?

					  ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}
	}