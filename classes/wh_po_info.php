<?php
	class Wh_po_info extends Crud implements  PagingInterface{
		protected $_table = 'wh_po_information';
		public function __construct($w=null){
			parent::__construct($w);
		}

		public function checkLazada($client_po = '' ,$order_item_id =''){
			$parameters = array();
			if($client_po && $order_item_id){
				$parameters[] = trim($client_po);
				$parameters[] = trim($order_item_id);
				$q= "Select count(*) as cnt from wh_po_information where client_pos = ? and laz_order_item_id = ? ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->first();
				}
			}
		}

		public function checkShopee($client_po = '' ,$item_name =''){
			$parameters = array();
			if($client_po && $item_name){
				$parameters[] = trim($client_po);
				$parameters[] = trim($item_name);
				$q= "Select count(*) as cnt from wh_po_information where client_pos = ? and item_name = ? ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->first();
				}
			}
		}

		public function getManifest($client_po = ''){
			$parameters = array();
			if($client_po){

				$q= "Select wi.*, wh.id as wh_id, wh.dr, m.lastname as member_name
					from wh_po_information wi
					left join wh_orders wh on wh.client_po = wi.client_po
					left join members m on m.id = wh.member_id
					where wi.client_po in ($client_po)
						 ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}

			}
		}

		public function countRecord($cid, $search,$item_name){

			$parameters = array();

			$whereSearch = "";
			$whereItemName = "";


			if($search){
				$parameters[] = "%$search%";
				$whereSearch = " and  w.client_po like ? ";
			}
			if($item_name){
				$parameters[] = "%$item_name%";
				$whereItemName = " and  w.item_name like  ? ";
			}


			$q= "Select count(wh.id) as cnt from wh_po_information wh
				where 1 = 1 $whereSearch $whereItemName ";

			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->first();
			}

		}

		public function get_active_record($cid,$start=0,$limit=0,$search='',$item_name=''){

			$parameters = array();

			if($limit){
				$l = " LIMIT $start,$limit";
			} else {
				$l='';
			}

			$whereSearch = "";
			$whereItemName = "";




			if($search){
				$parameters[] = "%$search%";
				$whereSearch = " and  w.client_po like ? ";
			}
			if($item_name){
				$parameters[] = "%$item_name%";
				$whereItemName = " and  w.item_name like ? ";
			}



			// prepare the query
		 	$q= "Select w.*
					from  wh_po_information w

					where 1 = 1 $whereSearch $whereItemName $l ";
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


			$limit = 20;
			 $search = Input::get('search');
			 $item_name = Input::get('item_name');

			$countRecord = $this->countRecord($cid,$search,$item_name);
			$total_pages = $countRecord->cnt;
			$stages = 3;
			$page = ($args);
			$page = (int)$page;

			if($page) {
				$start = ($page - 1) * $limit;
			} else {
				$start = 0;
			}

			$list = $this->get_active_record($cid, $start, $limit,$search,$item_name);

			$this->getPageNavigation($page, $total_pages, $limit, $stages);

			if($list) {
				?>
				<table class='table' id='tblBordered'>
					<thead>
					<tr><th>Client PO</th><th>Seller SKU</th><th>Item Name</th><th>Qty</th><th>Unit Price</th><th>Tracking #</th><th>Shipping Company</th></tr>
					</thead>
					<tbody>
					<?php
						foreach($list as $l) {
							?>
							<tr>
								<td><?php echo $l->client_po; ?></td>
								<td><?php echo $l->seller_sku; ?></td>
								<td><?php echo $l->item_name; ?></td>
								<td><?php echo $l->qty; ?></td>
								<td><?php echo $l->unit_price; ?></td>
								<td><?php echo $l->tracking_number; ?></td>
								<td><?php echo $l->shipping_company; ?></td>
							</tr>
							<?php

						}
					?>
					</tbody>
				</table>
				<?php
			} else {

			}
		}
	}