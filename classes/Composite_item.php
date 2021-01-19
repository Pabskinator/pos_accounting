<?php
	class Composite_item extends Crud{
		protected $_table = 'composite_items';
		public function __construct($company=null){
			parent::__construct($company);
		}
		public function countRecord($cid,$search=''){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$whereItem = '';
				if($search){
					$search = addslashes($search);
					$whereItem = " and (i2.item_code like '%$search%' or i2.barcode like '%$search%' or i2.description like '%$search%')";
				}
				$q = "Select count(ci.id) as cnt from composite_items ci left join items i1  on i1.id = ci.item_id_raw left join items i2  on i2.id = ci.item_id_set where ci.company_id=? and ci.is_active = 1 $whereItem";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function get_sales_record($cid,$start,$limit,$search=''){
			$parameters = array();
			if($cid){
				$parameters[] = $cid;

				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}

				$whereItem = '';
				if($search){
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$whereItem = " and (i2.item_code like ? or i2.barcode like ? or i2.description like ? )";
				}
 				 $q= "Select ci.*,i1.item_code,i1.barcode,i1.description,i1.spare_type as sptype,i2.item_code as set_item_code,i2.barcode  as set_barcode,i2.description  as set_description from composite_items ci left join items i1  on i1.id = ci.item_id_raw left join items i2  on i2.id = ci.item_id_set where ci.company_id=? and ci.is_active = 1  $whereItem order by ci.item_id_set $l ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}

		public function checkIfExists($cid=0,$item_set=0,$item_spare =0){
			$parameters = array();
			if($cid && $item_set && $item_spare) {
				$parameters[] = $cid;
				$parameters[] = $item_set;
				$parameters[] = $item_spare;

				$q = "Select count(id) as cnt from composite_items  where company_id=? and item_id_set = ? and item_id_raw = ? and is_active=1 ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function countItemWithParts($cid,$search='',$itemlist='',$categ = ''){
			$parameters = array();
			if($cid){
				$parameters[] = $cid;
				$whereItem="";
				$whereItemOrder="";
				$whereCateg="";
				if($search){
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$whereItem = " and (i.item_code like ? or i.description like ? ) ";
				}
				if($itemlist){
					$explodeid = explode(",",$itemlist);
					$lid = "";
					foreach($explodeid as $e){
						$lid .= "?,";
						$parameters[] = $e;
					}
					$lid = rtrim($lid,",");
					$whereItemOrder = " and ci.item_id_set in ($lid) ";
				}
				if($categ){
					$explodecateg = explode(",",$categ);
					$lcateg = "";
					foreach($explodecateg as $e){
						$lcateg .= "?,";
						$parameters[] = $e;
					}
					$lcateg = rtrim($lcateg,",");

					$whereCateg = " and i.category_id in ($lcateg) ";
				}

				$q = "Select COUNT(DISTINCT(ci.item_id_set)) AS cnt from composite_items ci left join items i on i.id=ci.item_id_set where ci.company_id=? and ci.is_active=1 $whereItem $whereItemOrder $whereCateg";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function getItemWithParts($cid,$start,$limit,$search='',$itemlist='',$categ = ''){
			$parameters = array();
			if($cid){
				$parameters[] = $cid;
				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}
				$whereItem="";
				$whereItemOrder="";
				$whereCateg="";
				if($search){
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$whereItem = " and (i.item_code like ? or i.description like ? ) ";
				}
				if($itemlist){
					$explodeid = explode(",",$itemlist);
					$lid = "";
					foreach($explodeid as $e){
						$lid .= "?,";
						$parameters[] = $e;
					}
					$lid = rtrim($lid,",");
					$whereItemOrder = " and ci.item_id_set in ($lid) ";
				}
				if($categ){
					$explodecateg = explode(",",$categ);
					$lcateg = "";
					foreach($explodecateg as $e){
						$lcateg .= "?,";
						$parameters[] = $e;
					}
					$lcateg = rtrim($lcateg,",");

					$whereCateg = " and i.category_id in ($lcateg) ";
				}
				$q = " Select DISTINCT(ci.item_id_set) as item_id, i.item_code,i.description ,u.name as unit_name from composite_items ci left join items i on i.id=ci.item_id_set left join units u on u.id = i.unit_id where ci.company_id=? and ci.is_active=1  $whereItem $whereItemOrder  $whereCateg $l";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function getSpareparts($item_id = 0){
			$parameters = array();
			if($item_id) {
				$parameters[] = $item_id;
				$q = "Select ci.*, i.item_code,i.description,i.spare_type as sptype, u.name as unit_name from composite_items ci left join items i on i.id=ci.item_id_raw left join units u on u.id = i.unit_id where ci.item_id_set=? and ci.is_active=1 order by i.spare_type ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}

		public function hasSpare($item_id = 0){
			$parameters = array();
			if($item_id) {
				$parameters[] = $item_id;
				$q = "Select count(id) as cnt from composite_items  where item_id_set=? and is_active=1 ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function compositeReport($dt1=0,$dt2=0){
			$parameters = array();

			$parameters[] = $dt1;
			$parameters[] = $dt2;

			$q ="select ci.item_id_raw, i.item_code, i2.item_code , ci.qty, sum(wd.qty) as out_qty
				from composite_items ci
				left join items i on i.id = ci.item_id_set
				left join items i2 on i2.id = ci.item_id_raw
				left join wh_order_details wd on wd.item_id = ci.item_id_set
				left join wh_orders wh on wh.id = wd.wh_orders_id
				where wh.stock_out = 1 and wh.created >=$dt1 and wh.created <=$dt2
				group by ci.item_id_raw";

			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				// return the data if exists
				return $data->results();
			}


		}

		public function summaryRaw($y){
			$parameters = array();

			$parameters[] = $y;

			$q ="select ci.item_id_raw, i.item_code, i2.item_code , ci.qty, sum(wd.qty) as out_qty, MONTH(FROM_UNIXTIME(wh.created)) AS m
				from composite_items ci
				left join items i on i.id = ci.item_id_set
				left join items i2 on i2.id = ci.item_id_raw
				left join wh_order_details wd on wd.item_id = ci.item_id_set
				left join wh_orders wh on wh.id = wd.wh_orders_id
				where wh.stock_out = 1 and YEAR(FROM_UNIXTIME(wh.created)) = ?
				group by ci.item_id_raw,ci.qty, MONTH(FROM_UNIXTIME(wh.created))";

			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				// return the data if exists
				return $data->results();
			}


		}

		public function getSpDetails($id){
			$parameters = array();
			if($id){
				$parameters[] = $id;

				$q= "Select ci.*,i1.item_code,i1.barcode,i1.description,
					i1.spare_type as sptype,i2.item_code as set_item_code,i2.barcode  as set_barcode,
					i2.description  as set_description
					from composite_items ci
					left join items i1  on i1.id = ci.item_id_raw
					left join items i2  on i2.id = ci.item_id_set
					where ci.item_id_set = ? and ci.is_active = 1 ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}


	}
?>