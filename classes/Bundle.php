<?php
	class Bundle extends Crud{
		protected $_table = 'bundles';
		public function __construct($b=null){
			parent::__construct($b);
		}
		public function getBundleItem($item_id = 0){
			$parameters = array();
			if($item_id){
				$parameters[] = $item_id;
				$q= "Select b.* , i.item_code, i.description,i.barcode ,u.name as unit_name
					from bundles b
					left join items i on i.id = b.item_id_child
					LEFT JOIN units u on u.id = i.unit_id
					where b.item_id_parent = ?";

				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function countBundleItem($item_id = 0){
			$parameters = array();
			if($item_id){
				$parameters[] = $item_id;
				$q= "Select count(id) as cnt from bundles  where item_id_parent = ?";

				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function countRecord($cid,$search = ''){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$whereSearch ="";
				if($search){
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$whereSearch = " and (i.item_code like ? or i.description like ? or i2.item_code like ? or i2.description like ?)";
				}
				$q = "Select count(b.id) as cnt from bundles b left join items i on i.id=b.item_id_parent left join items i2 on i2.id=b.item_id_child where  b.company_id=?  $whereSearch ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function get_record($cid,$start,$limit,$search = ''){
			$parameters = array();
			if($cid){
				$parameters[] = $cid;
				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}
				$whereSearch ="";
				if($search){
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$whereSearch = " and (i.item_code like ? or i.description like ? or i2.item_code like ? or i2.description like ?)";
				}
				 $q= "Select b.*, i.item_code,i.description,i2.item_code as item_code_child, i2.description as description_child from bundles b left join items i on i.id=b.item_id_parent left join items i2 on i2.id=b.item_id_child where  b.company_id=? $whereSearch order by b.item_id_parent $l ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function deleteChild($id){
			$parameters = array();
			if($id) {
				$parameters[] = $id;

				$q = "Delete from bundles where id=?";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return true;
				}
			}
		}
		public function checkIfExists($cid=0,$item_set=0,$item_spare =0){
			$parameters = array();
			if($cid && $item_set && $item_spare) {
				$parameters[] = $cid;
				$parameters[] = $item_set;
				$parameters[] = $item_spare;

				$q = "Select count(id) as cnt from bundles  where company_id=? and item_id_parent = ? and item_id_child = ? and is_active=1 ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
	}
?>