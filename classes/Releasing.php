<?php
	class Releasing extends Crud{
		protected $_table = 'releasing';
		public function __construct($r=null){
			parent::__construct($r);
		}
		public function getPending($branch_id){
			$parameters = array();
			if($branch_id){
				$parameters[] = $branch_id;
				$q= "Select r.*,i.is_bundle, i.item_code, i.description,s.invoice, s.dr, s.ir,s.sold_date,ci.item_id_set from releasing r left join items i on i.id = r.item_id left join (Select s.invoice, s.dr, s.ir, s.payment_id, t.branch_id,s.sold_date  from sales s left join terminals t on t.id=s.terminal_id group by s.payment_id) s on s.payment_id = r.payment_id left join (Select DISTINCT(item_id_set) from composite_items ) ci on ci.item_id_set = r.item_id  where s.branch_id = ? and r.status = 1 order by r.payment_id desc";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function deleteReleasing($id){
			$parameters = array();
			if($id){
				$parameters[] = $id;
				$q= "Delete from releasing where id = ? ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return true;
				}
			}
		}
		public function getByPayment($pid){
			$parameters = array();
			if($pid){
				$parameters[] = $pid;
				$q= "Select r.*,i.is_bundle, i.item_code, i.description,s.invoice, s.dr, s.ir,s.branch_id, ci.item_id_set from releasing r left join items i on i.id = r.item_id left join (Select s.invoice, s.dr, s.ir, s.payment_id, t.branch_id  from sales s left join terminals t on t.id=s.terminal_id group by s.payment_id) s on s.payment_id = r.payment_id left join (Select DISTINCT(item_id_set) from composite_items ) ci on ci.item_id_set = r.item_id  where r.payment_id = ? and r.status = 1 order by r.id desc";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function getForCancel($pid){
			$parameters = array();
			if($pid){
				$parameters[] = $pid;
				$q= "Select * from releasing where payment_id = ? ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function getSingle($pid){
			$parameters = array();
			if($pid){
				$parameters[] = $pid;
				$q= "Select * from releasing where payment_id = ? limit 1 ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->first();
				}
			}
		}
		public function cancelByPaymentId($pid){
			$parameters = array();
			if($pid){
				$parameters[] = $pid;
				$q= "update releasing set status = 3 where payment_id = ? ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return true;
				}
			}
		}
		public function countRecord($cid, $search=''){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$whereItem = '';
				if($search){
					$search = addslashes($search);
					$whereItem = " and (s.dr like '%$search%' or s.invoice like '%$search%' or s.ir like '%$search%')";
				}
				$q= "Select count(r.id) as cnt from releasing r left join items i on i.id = r.item_id left join (Select s.invoice, s.dr, s.ir, s.payment_id, t.branch_id  from sales s left join terminals t on t.id=s.terminal_id group by s.payment_id) s on s.payment_id = r.payment_id where r.company_id = ? and r.status = 2 $whereItem order by r.id desc";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function get_record($cid, $start, $limit, $search=''){
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
					$search = addslashes($search);
					$whereItem = " and (s.dr like '%$search%' or s.invoice like '%$search%' or s.ir like '%$search%')";
				}

				$q= "Select r.*, i.item_code, i.description,i.has_serial,s.invoice, s.dr, s.ir from releasing r left join items i on i.id = r.item_id left join (Select s.invoice, s.dr, s.ir, s.payment_id, t.branch_id  from sales s left join terminals t on t.id=s.terminal_id group by s.payment_id) s on s.payment_id = r.payment_id where r.company_id = ? and r.status = 2 $whereItem order by r.id desc $l";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
	}
?>