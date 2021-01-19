<?php
	class Add_batch_inv_detail extends Crud{
		protected $_table = 'add_inv_batch_details';

		public function __construct($a=null){
			parent::__construct($a);
		}

		public function getDetails($id=0,$company_id=0){
			$parameters=[];
			if($id){

				$now = time();
				$parameters[] = $id;

				$this->where("a.batch_id = ?");

				$this->select("a.* , i.item_code, i.description, r.rack, p.price")
					->from("add_inv_batch_details a")
					->join("left join items i on i.id=a.item_id")
					->join("left join racks r on r.id=a.rack_id");

				$largeJoin = "left join
				 		    ( Select a.item_id, a.effectivity, p.price, p.id as price_id from
							(
							Select p.item_id, max(p.effectivity) as effectivity
							from prices p left join items i on i.id=p.item_id
							 where i.company_id=$company_id  and p.effectivity <= $now group by p.item_id
							 ) a left join prices p on p.item_id = a.item_id
							 where a.effectivity = p.effectivity
							 ) p on p.item_id = i.id";

				return  $this->join($largeJoin)->get($parameters)->all();

			}
		}

	}
?>