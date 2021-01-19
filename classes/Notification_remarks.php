<?php
	class Notification_remarks extends Crud{
		protected $_table = 'notification_remarks';
		public function __construct($n=null){
			parent::__construct($n);
		}
		public function getNotificationRemarks($c=0,$i=0,$p=0){
			$parameters = array();
			if($i && $c && $p) {
				$parameters[] = $c;
				$parameters[] = $i;
				$parameters[] = $p;
				$q = "Select n.created, u.lastname , u.firstname, i.item_code, i.description,n.remarks from notification_remarks n left join items i on i.id = n.item_id left join users u on u.id=n.user_id  where n.company_id=? and n.item_id=? and n.payment_id=?";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}
	} // end class
?>