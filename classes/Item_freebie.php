<?php
	class Item_freebie extends Crud {
		protected $_table = 'item_freebies';

		public function __construct($i = null) {
			parent::__construct($i);
		}
		public function getRecord($company_id = 1){

			$parameters = array();
			$parameters[] = $company_id;

			$q= "Select ip.*, i.item_code,i.description ,i2.item_code as item_code_freebie, i2.description as description_freebie, ifd.discount from item_freebies ip
				left join items i on i.id = ip.item_id
 				left join item_freebie_details ifd on ifd.if_id = ip.id
 				left join items i2 on i2.id = ifd.item_id
 				where ip.company_id = ? ";


			$data = $this->_db->query($q, $parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}
		}

		public function getFreebies($item_id=0,$qty=0,$branch_id=0){
			$parameters = array();
			$parameters[] = $item_id;
			$parameters[] = $qty;
			$now = time();
			 $q= "Select ifd.*, i.item_code, i.description,p.price, inv.inv_qty, ip.qty as need_qty from item_freebie_details ifd
					left join item_freebies ip on ip.id = ifd.if_id
					left join items i on i.id = ifd.item_id
					left join
							( Select a.item_id, a.effectivity, p.price, p.id as price_id from
							(Select p.item_id, max(p.effectivity) as effectivity  from prices p left join items i on i.id=p.item_id  where p.effectivity <= $now group by p.item_id) a
							left join prices p on p.item_id = a.item_id where a.effectivity = p.effectivity) p on p.item_id = i.id
					left join (Select sum(qty) as inv_qty, item_id from inventories where item_id = $item_id and branch_id = $branch_id) inv on inv.item_id = i.id
					where ip.item_id = ? and ip.qty <= ?

					";


			$data = $this->_db->query($q, $parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}
		}
	}