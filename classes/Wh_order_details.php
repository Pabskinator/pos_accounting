<?php
	class Wh_order_details extends Crud{
		protected $_table = 'wh_order_details';
		public function __construct($w=null){
			parent::__construct($w);
		}

		public function getOrderDetails($order_id){
			$parameters = array();
			if($order_id){
				$parameters[] = $order_id;
				$orderby=" order by o.id desc ";
				if(!Configuration::thisCompany('cebuhiq')){
					$orderby = "order by ci.item_id_set, ct.name";
				}
				$q= "Select
							stats.name as station_name,
							st.name as sales_type_name,o.*,IFNULL(ct.name,'') as category_name,
							i.item_type,
							i.has_serial,i.is_bundle,i.item_code, un.name as unit_name,
							i.description ,i.barcode, p.price, (o.price_adjustment + p.price) as adjusted_price,
							ci.item_id_set ,i.warranty
							from wh_order_details o
							left join items i on i.id = o.item_id
							left join categories ct on ct.id = i.category_id

							left join units un on un.id=i.unit_id
							left join prices p on p.id=o.price_id
							left join (select DISTINCT(item_id_set) from composite_items) ci on ci.item_id_set = o.item_id
							left join stations stats on stats.id = o.station_id
							left join salestypes st on st.id = o.spec_sales_type
							where o.wh_orders_id=? and o.is_active=1 $orderby ";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function deleteItem($id){
			$parameters = array();
			if($id){
				$parameters[] = $id;
				$q = "Delete from wh_order_details where id = ? ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return true;
				}
			}
		}
		public function updateWhDetails($id,$qty,$adj = 0){
			$parameters = array();
			if($id && $qty){
				$parameters[] = $qty;
				$whereAddtl = "";
				if(Configuration::thisCompany('cebuhiq')){
					$parameters[] = $qty;
					$parameters[] = $qty;
					$whereAddtl = ", original_qty = ? , unit_qty=?";
				}

				$update_adj = '';
				$wddet = new Wh_order_details($id);
				$orig_adj = $wddet->data()->member_adjustment;
				if(number_format($adj,2) != number_format($orig_adj,2)){
					$parameters[] = $adj;
					$update_adj = ", member_adjustment=? ";
				}
				$parameters[] = $id;
				$q = "update wh_order_details set qty = ? $whereAddtl $update_adj where id = ? ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return true;
				}
			}
		}

		public function getOrderForNotif($order_id){
			$parameters = array();
			if($order_id){
				$parameters[] = $order_id;
				$q= "Select
							o.*,IFNULL(ct.name,'') as category_name,
							i.item_type,
							i.has_serial,i.is_bundle,i.item_code, un.name as unit_name,
							i.description ,i.barcode,i.warranty, wsd.start_date,wsd.duration
							from wh_order_details o
							left join items i on i.id = o.item_id
							left join wh_service_date wsd on wsd.wh_order_id = o.wh_orders_id and wsd.item_id = o.item_id
							left join categories ct on ct.id = i.category_id
							left join units un on un.id=i.unit_id

							where o.wh_orders_id=? and o.is_active=1 ";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
	}
?>