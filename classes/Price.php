<?php
	class Price extends Crud{
		protected $_table='prices';
		public function __construct($price = NULL){
			parent::__construct($price);
		}
		public function getPriceAll($cid = 0){
			if($cid){
				$parameters = array();
				$parameters[] = $cid;
				$now = time();
				  $q="Select a.item_id, a.effectivity, p.price, p.id as price_id from (Select p.item_id, max(p.effectivity) as effectivity  from prices p left join items i on i.id=p.item_id  where i.company_id=?  and p.effectivity <= $now group by p.item_id) a  left join prices p on p.item_id = a.item_id where a.effectivity = p.effectivity";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

	}
?>