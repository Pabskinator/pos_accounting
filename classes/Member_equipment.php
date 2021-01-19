<?php

	class Member_equipment extends Crud implements PagingInterface{
		protected $_table = 'member_equipments';

		public function __construct($u=null){
			parent::__construct($u);
		}

		public function updateEquipment($mem_id = 0,$item_id=0,$qty=0){
			$parameters = array();
			$parameters[] = $qty;
			$parameters[] = $mem_id;
			$parameters[] = $item_id;


			$q= "update member_equipments set borrowed_qty = borrowed_qty + ?
 				where member_id = ? and item_id = ? ";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return true;
			}
		}

		public function checkEquipment($mem_id = 0,$item_id=0){
			$parameters = array();
			$parameters[] = $mem_id;
			$parameters[] = $item_id;


			$q= "Select *
 				from member_equipments
 				where member_id = ? and item_id = ? ";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->first();
			}
		}
		public function getMemberEquipments(){
			$parameters = array();
			$parameters[] = 1;


			$q= "Select mq.*, m.lastname as member_name, i.item_code, i.description
 				from member_equipments mq
 				left join members m on m.id = mq.member_id
 				left join items i on i.id = mq.item_id
 				where 1 = ? ";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}
		}

		public function processRequest($id =0 ){

			$parameters = array();
			$parameters[] = $id;


			$q= "update member_equipment_request set status = 1 where id = ? ";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return true;
			}

		}

		public function requestData($id =0){


			$parameters = array();
			$parameters[] = $id;


			$q= "Select mr.*,wh.branch_id from member_equipment_request mr left join wh_orders wh on wh.id = mr.wh_order_id where mr.id = ?";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->first();
			}
		}

		public function countRecord($cid=0, $like='',$status=0){
			$likewhere='';

			$parameters = array();
			$parameters[] = $cid;
			$parameters[] = $status;

			if($like){
				$parameters[] = "%$like%";
				$parameters[] = "$like";
				$likewhere = " and (m.lastname like ? or me.id = ? ) ";
			}





			$q= "Select count(me.id) as cnt from member_equipment_request me
				left join members  m on m.id=me.member_id
				left join wh_orders wh on wh.id = me.wh_order_id
				where me.company_id = ?  and me.status = ? $likewhere ";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->first();
			}
		}

		public function get_active_record($cid,$start=0,$limit=0,$like='',$status=0){
			$parameters = array();

			if($limit){
				$l = " LIMIT $start,$limit";
			} else {
				$l='';
			}

			$likewhere='';
			$parameters[] = $cid;
			$parameters[] = $status;

			if($like){
				$parameters[] = "%$like%";
				$parameters[] = $like;
				$likewhere = " and (m.lastname like ? or me.id = ? ) ";
			}





			// prepare the query
			$q= "Select me.*, m.lastname as member_name, wh.invoice, wh.dr, wh.pr from member_equipment_request me
				left join members  m on m.id=me.member_id
				left join wh_orders wh on wh.id = me.wh_order_id
				where me.company_id = ?  and me.status = ? $likewhere  $l  ";
			//submit the query
			$data = $this->_db->query($q, $parameters);
			// return results if there is any
			if($data->count()){
				return $data->results();
			}
		}
		public function getPageNavigation($page, $total_pages, $limit, $stages) {
			getpagenavigation($page, $total_pages, $limit, $stages);
		}
		public function paginate($cid,$args) {
			$search = Input::get('search');
			$status = Input::get('status');
			$limit = 20;
			$countRecord = $this->countRecord($cid, $search,$status);
			$total_pages = $countRecord->cnt;
			$stages = 3;
			$page = ($args);
			$page = (int)$page;
			if($page) {
				$start = ($page - 1) * $limit;
			} else {
				$start = 0;
			}
			$company_op = $this->get_active_record($cid, $start, $limit, $search,$status);
			$this->getPageNavigation($page, $total_pages, $limit, $stages);
			if($company_op) {
				?>
				<div id="no-more-tables">
				<table class='table table-bordered table-condensed' id='tblSummaryOP'>
				<thead>
				<tr>
					<TH>Id</TH>
					<TH>Member</TH>
					<TH>Invoice</TH>
					<TH>DR</TH>
					<th>PR</th>
					<TH>Status</TH>
					<th>Created</th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php
				$used_arr  = ['Pending','Processed'];
				foreach($company_op as $o) {


					$type_name = isset($used_arr[$o->status]) ? $used_arr[$o->status] : 'Unknown payment';

					?>
					<tr>
						<td><strong><?php echo escape($o->id); ?></strong>
						<td>
							<strong><?php echo escape($o->member_name); ?></strong>
						</td>
						<td><?php echo escape($o->invoice); ?></td>
						<td><?php echo escape($o->dr); ?></td>
						<td><?php echo escape($o->pr); ?></td>
						<td><?php echo $type_name; ?></td>
						<td><?php echo date('F d, Y H:i:s A',$o->created); ?></td>
						<td>
							<button data-id='<?php echo $o->id; ?>' class='btn btn-default btn-sm btnDetails'><i class='fa fa-list'></i> Details</button>

						</td>
					</tr>
					<?php
				}
				?>
				</tbody>
				</table>
				</div>
				<?php
			} else {
				?>
				<div class="alert alert-info">No record.</div>
				<?php
			}
		}


		public function getRequestDetails($id =0){


			$parameters = array();
			$parameters[] = $id;


			$q= "Select rd.* , i.item_code, i.description from member_equipment_request_details rd
 				 left join items i on i.id = rd.item_id where rd.request_id = ? ";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->results();
			}
		}
	}
?>