<?php
	class Supplier_order extends Crud{
		protected $_table = 'supplier_orders';
		public function __construct($s=null){
			parent::__construct($s);
		}
		public function orderCount($c=0){
			if($c){
				$parameters = array();

				$parameters[] = $c;

				$q = 'Select count(id) as total, status from supplier_orders GROUP by status';
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function getorderssup($c=0,$status=0){
			if($c){
				$parameters = array();
				$parameters[] = $status;
				$parameters[] = $c;

				$q = 'Select o.*,b.name as bname,b.address as baddress, u.lastname,u.firstname,u.middlename, s.name as sname , s.description as sdesc
					from supplier_orders o
					left join branches b on o.branch_to=b.id
					left join suppliers s on o.supplier_id = s.id
					left join users u on u.id = o.user_id where o.status =? and o.company_id = ? ';
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function isAllReceive($order_id){
			if($order_id){
				$parameters = array();
				$parameters[] = $order_id;
				$q = 'Select count(*) as num from supplier_order_details where supplier_order_id=? and qty != get_qty';
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function getItems($order_id){
			if($order_id){
				$parameters = array();
				$parameters[] = $order_id;
				$q = 'Select * from supplier_order_details where supplier_order_id=?';
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function getOrderDetails($c=0,$branch_id=0){
			if($c){
				$parameters = array();
				$parameters[] = $c;
				$branch_id = (int) $branch_id;
				 $q = "Select inv.stock, od.*,si.id as supid,si.item_id as s_item_id, si.item_code as s_item_code,si.description as s_description, si.purchase_price as spp , i.item_code , i.description,cbm_l,cbm_h,cbm_w
					from supplier_order_details od
					left join supplier_item si on si.id=od.supplier_item_id
					left join items i on i.id=si.item_id
					left join (select sum(qty) as stock , item_id from inventories where branch_id = $branch_id group by item_id) inv on inv.item_id = si.item_id
					where od.supplier_order_id=?";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function getSupInfo($c=0,$id=0){
			if($c){
				$parameters = array();
				$parameters[] = $c;
				$parameters[] = $id;

				$q = 'Select o.*,b.name as bname,b.address as baddress, u.lastname,u.firstname,u.middlename, s.name as sname , s.description as sdesc
					from supplier_orders o
					left join branches b on o.branch_to=b.id
					left join suppliers s on o.supplier_id = s.id
					left join users u on u.id = o.user_id where  o.company_id = ? and o.id = ? ';
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
	}
