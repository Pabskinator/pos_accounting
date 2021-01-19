<?php
	class Supplier_item extends Crud{
		protected $_table = 'supplier_item';
		public function __construct($s=null){
			parent::__construct($s);
		}
		public function getSupplierItem($company_id,$supplier_id,$search){
			$parameters = array();
			if($company_id) {
				$parameters[] = $company_id;
				$whereSupplier = '';
				$whereSearch = '';
				if($supplier_id) {
					$parameters[] = $supplier_id;
					$whereSupplier = "and s.supplier_id = ?";
				}
				if($search) {
					$parameters[] = "%$search%";
					$whereSearch = "and s.item_code like ? ";
				}

				$q = "Select s.* from supplier_item s left join items i on i.id = s.supplier_id where s.company_id = ? $whereSupplier $whereSearch";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function countRecord($cid,$search='',$branch,$item_id=0,$supplier_id=0){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$whereItem = "";
				$whereSupplier = "";
				if($item_id){
					$whereItem = " and s.item_id = ?";
					$parameters[] = $item_id;
				}
				if($supplier_id){
					$whereSupplier = " and s.supplier_id = ?";
					$parameters[] = $supplier_id;
				}

				if($search) {
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$likewhere = " and (s.item_code like ? or s.description like ? ) ";
				} else {
					$likewhere = "";
				}
				$q = "Select count(s.id) as cnt from supplier_item  s where s.company_id=?  $likewhere $whereItem $whereSupplier and s.is_active=1";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function get_sales_record($cid,$start,$limit,$search='',$branch,$item_id=0,$supplier_id=0){
			$parameters = array();
			if($cid){
				$parameters[] = $cid;
				$whereItem = "";
				$whereSupplier = "";
				if($item_id){
					$whereItem = " and si.item_id = ?";
					$parameters[] = $item_id;
				}
				if($supplier_id){
					$whereSupplier = " and si.supplier_id = ?";
					$parameters[] = $supplier_id;
				}
				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}
				if($search){
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$likewhere = " and (si.item_code like ? or si.description like ? ) ";
				} else {
					$likewhere='';
				}
				if($branch){
					$branch = (int) $branch;
					$branchwhere = " and branch_id = $branch";
				} else {
					$branchwhere='';
				}
				$q= "Select si.*,s.name as supname  , i.item_code as ic, i.description as des,inv.invqty from supplier_item si left join suppliers s on s.id=si.supplier_id  left join items i on i.id = si.item_id left join (Select sum(qty) as invqty,item_id,branch_id from inventories where 1=1 $branchwhere group by item_id) inv on inv.item_id = si.item_id where si.company_id=?  $whereItem $whereSupplier $likewhere  and si.is_active=1   $l ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function getitemssup($cid=0,$sup_id=0){
			$parameters = array();
			if($cid && $sup_id){
				$parameters[] = $cid;
				$parameters[] = $sup_id;
				 $q= "Select si.*,s.name as supname,i.barcode,i.item_code as ic, i.description as des,u.name as unit_name, u.is_decimal from supplier_item si left join suppliers s on s.id=si.supplier_id  left join items i on i.id= si.item_id left join units u on u.id=i.unit_id where si.company_id=? and s.id=? and si.item_id !=0 ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function getItemSupBaseOnProducId($cid=0,$item_id=0){
			$parameters = array();
			if($cid && $item_id){
				$parameters[] = $cid;
				$parameters[] = $item_id;
				 $q= "Select si.*,s.name as supname, s.id as sid from supplier_item si left join suppliers s on s.id=si.supplier_id   where si.company_id=? and si.item_id=?  and si.is_active=1";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function checkIfItemOnSupExists($cid=0,$supplier_id=0,$item_id=0){
			$parameters = array();
			if($cid && $item_id && $supplier_id){
				$parameters[] = $cid;
				$parameters[] = $item_id;
				$parameters[] = $supplier_id;
				 $q= "Select count(id) as cnt from supplier_item where company_id = ? and item_id = ? and supplier_id = ?";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->first();
				}
			}
		}
		public function getSupplierItemId($cid=0,$supplier_id=0,$item_id=0){
			$parameters = array();
			if($cid && $item_id && $supplier_id){
				$parameters[] = $cid;
				$parameters[] = $item_id;
				$parameters[] = $supplier_id;
				$q= "Select id from supplier_item where company_id = ? and item_id = ? and supplier_id = ?";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->first();
				}
			}
		}
	}
?>