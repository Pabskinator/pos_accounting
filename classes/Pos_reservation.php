<?php
	class Pos_reservation extends Crud {
		protected $_table = 'pos_reservations';

		public function __construct($position = null) {
			parent::__construct($position);
		}
		public function getReservation($branch_id=0) {
			$parameters = array();
			if($branch_id){
				$parameters[] = $branch_id;

				$q= "Select p.*, q.name as queue_name, u.firstname, u.lastname
					from pos_reservations p
					left join queu_lists ql on ql.id = p.queue_list_id
					left join users u on u.id = ql.user_id
					left join queus q on q.id = ql.queu_id
					where p.branch_id = ? and p.status = 0 ";
				$data = $this->_db->query($q, $parameters);

				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}
	}