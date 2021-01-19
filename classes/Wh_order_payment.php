<?php

	class Wh_order_payment extends Crud implements  PagingInterface {

		protected $_table = 'wh_order_payments';

		public function __construct($w = null) {
			parent::__construct($w);
		}
		public function SOASummary($dt_from,$dt_to){

			$parameters = array();
			$whereDt = "";
			if($dt_from && $dt_to){
				$parameters[] = $dt_from;
				$parameters[] = $dt_to;
				$whereDt = " and  w.transaction_date >= ? and w.transaction_date <= ? ";
			}
			 $q= "Select sum(abs(w.amount)) as amt , w.fee_name, w.transaction_type, st.name,w.soa_ref
				from wh_order_payments w
				 left join salestypes st on st.id = w.sales_type_id
				 where 1=1 $whereDt
				 group by st.name, w.soa_ref ";

			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				// return the data if exists
				return $data->results();
			}
		}
		public function checkerLazada($order_number='', $order_item_id=''){
			$parameters = array();

			$parameters[] = $order_number;
			$parameters[] = $order_item_id;



			$q= "Select * from wh_order_payments where client_po = ? and order_item_id = ? limit 1";

			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				// return the data if exists
				return $data->first();
			}

		}

		public function countRecord($cid, $search,$type,$fee_name,$dt_from,$dt_to){

			$parameters = array();
			$whereSearch = "";
			$whereType = "";
			$whereFeeName = "";
			$whereDate= "";


			if($search){
				$parameters[] = "%$search%";
				$whereSearch = " and  w.client_po like ? ";
			}
			if($type){

				$parameters[] = $type;
				$whereType = " and  w.transaction_type = ? ";
			}
			if($fee_name){
				$parameters[] = $fee_name;
				$whereFeeName = " and  w.fee_name = ? ";
			}
			if($dt_from && $dt_to){
				$dt_from = strtotime($dt_from);
				$dt_to = strtotime($dt_to . "1 day -1 sec");
				$parameters[] = $dt_from;
				$parameters[] = $dt_to;
				$whereDate = " and  w.transaction_date >= ?   and  w.transaction_date <= ?";
			}

			$q= "Select count(wh.id) as cnt from wh_order_payments wh
				where 1 = 1 $whereSearch $whereType $whereFeeName $whereDate ";

			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->first();
			}

		}

		public function get_active_record($cid,$start=0,$limit=0,$search,$type,$fee_name,$dt_from,$dt_to){

			$parameters = array();

			if($limit){
				$l = " LIMIT $start,$limit";
			} else {
				$l='';
			}

			$whereSearch = "";
			$whereType = "";
			$whereFeeName = "";
			$whereDate= "";


			if($search){
				$parameters[] = "%$search%";
				$whereSearch = " and  w.client_po like ? ";
			}
			if($type){

				$parameters[] = $type;
				$whereType = " and  w.transaction_type = ? ";
			}
			if($fee_name){
				$parameters[] = $fee_name;
				$whereFeeName = " and  w.fee_name = ? ";
			}
			if($dt_from && $dt_to){
				$dt_from = strtotime($dt_from);
				$dt_to = strtotime($dt_to . "1 day -1 sec");
				$parameters[] = $dt_from;
				$parameters[] = $dt_to;
				$whereDate = " and  w.transaction_date >= ?   and  w.transaction_date <= ?";
			}



			// prepare the query
			$q= "Select w.*, st.name as sales_type_name
					from  wh_order_payments w
					left join salestypes st on w.sales_type_id = st.id
					where 1 = 1 $whereSearch $whereType $whereFeeName $whereDate $l ";
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
			$type = Input::get('type');
			$fee_name = Input::get('fee_name');
			$dt_from = Input::get('dt_from');
			$dt_to = Input::get('dt_to');
			$countRecord = $this->countRecord($cid,$search,$type,$fee_name,$dt_from,$dt_to);
			$total_pages = $countRecord->cnt;
			$stages = 3;
			$page = ($args);
			$page = (int)$page;

			if($page) {
				$start = ($page - 1) * $limit;
			} else {
				$start = 0;
			}

			$list = $this->get_active_record($cid, $start, $limit,$search,$type,$fee_name,$dt_from,$dt_to);

			$this->getPageNavigation($page, $total_pages, $limit, $stages);
			if($list) {
				?>
				<table class='table' id='tblBordered'>
				<thead>
				<tr><th>Type</th><th>Soa Ref</th><th>Client PO</th><th>Transaction Type</th><th>Transaction Date</th><th>Fee Name</th><th>Amount</th></tr>
				</thead>
				<tbody>
					<?php
					foreach($list as $l) {
						?>
						<tr>
						<td><?php echo $l->sales_type_name; ?></td>
						<td><?php echo $l->soa_ref; ?></td>
						<td><?php echo $l->client_po; ?></td>
						<td><?php echo $l->transaction_type; ?></td>
						<td><?php echo date('m/d/Y',$l->transaction_date); ?></td>
						<td><?php echo $l->fee_name; ?></td>
						<td><?php echo $l->amount; ?></td>
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