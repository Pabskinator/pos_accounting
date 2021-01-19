<?php
	class Item_post extends Crud {
		protected $_table = 'item_posts';

		public function __construct($i = null) {
			parent::__construct($i);
		}
		public function getRecord($s = 1,$company_id = 1){

			$parameters = array();
			$parameters[] = $s;
			$whereQty = "";
			if($s == 1){
				$whereQty = "and (ip.qty - IFNULL(wh.whqty,0)) > 0";
			} else  if($s == 2){
				$whereQty = "and ip.qty = IFNULL(wh.whqty,0)";
			}
			$now = time();
			$q= "Select ip.*, i.item_code,i.description, p.price
				from item_posts ip left join items i on i.id = ip.item_id
				LEFT JOIN
						( Select a.item_id, a.effectivity, p.price, p.id as price_id from
							(Select p.item_id, max(p.effectivity) as effectivity  from prices p left join items i on i.id=p.item_id  where i.company_id=$company_id  and p.effectivity <= $now group by p.item_id) a
							left join prices p on p.item_id = a.item_id where a.effectivity = p.effectivity) p on p.item_id = i.id
				left join (Select sum(d.qty) as whqty, d.item_post_id from wh_order_details d left join wh_orders o on o.id = d.wh_orders_id where o.status in (1,2,3,4) group by d.item_post_id) wh on wh.item_post_id = ip.id
				where ip.is_active = 1 and  ip.status = 1 $whereQty";


			$data = $this->_db->query($q, $parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}

		}
	}