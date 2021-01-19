<?php
	class Payment_consumable extends Crud implements PagingInterface{
		protected $_table = 'payment_consumable';
		public function __construct($c=null){
			parent::__construct($c);
		}

		public function getByPids($pids = ""){

			if($pids){
				$parameters = [];

				$q= 'Select * from payment_consumable  where  payment_id in ('.$pids.') and is_active = 1';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
				return false;
			}
		}
		public function getRecord($pid=0){
			$parameters = array();
			$parameters[] = $pid;



			$q= "Select * from payment_consumable where payment_id = ?";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->first();
			}

		}

		public function countRecord($cid=0, $like='',$dt1=0,$dt2=0){
			$parameters = array();

			if($like){
				$parameters[] = "%$like%";
				$parameters[] = "%$like%";
				$parameters[] = "%$like%";
				$parameters[] = "%$like%";
				$likewhere = " and (p.invoice like ? or  p.dr like ? or  p.ir like ? or m.lastname like ? )";
			} else {
				$likewhere='';
			}
			if($dt1 && $dt2){
				$dt1 = strtotime($dt1);
				$dt2 = strtotime($dt2 . "1 day -1 sec");
				$whereDate = " and p.sold_date >= $dt1 and p.sold_date <= $dt2";
			} else {
				$whereDate = "";
			}


			$q= "Select count(cc.id) as cnt from payment_consumable cc
					left join (Select invoice,dr,ir,sold_date,payment_id,member_id from sales group by payment_id)
					p on p.payment_id = cc.payment_id
					left join members m on m.id = p.member_id
					where 1=1 $likewhere $whereDate ";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->first();
			}
		}

		public function get_active_record($cid,$start=0,$limit=0,$like='',$dt1=0,$dt2=0){
			$parameters = array();


			if($limit){
				$l = " LIMIT $start,$limit";
			} else {
				$l='';
			}

			if($like){
				$parameters[] = "%$like%";
				$parameters[] = "%$like%";
				$parameters[] = "%$like%";
				$parameters[] = "%$like%";
				$likewhere = " and (p.invoice like ? or  p.dr like ? or  p.ir like ? or m.lastname like ? )";
			} else {
				$likewhere='';
			}
			if($dt1 && $dt2){
				$dt1 = strtotime($dt1);
				$dt2 = strtotime($dt2 . "1 day -1 sec");
				$whereDate = " and p.sold_date >= $dt1 and p.sold_date <= $dt2";
			} else {
				$whereDate = "";
			}
			// prepare the query
		 	$q= "Select cc.*,p.invoice, p.dr, p.ir , p.sold_date, m.lastname as member_name from payment_consumable cc
			left join	(Select invoice,dr,ir,sold_date,payment_id,member_id from sales group by payment_id )
			p on p.payment_id = cc.payment_id
			left join members m on m.id = p.member_id
			where 1=1 $likewhere $whereDate order by p.sold_date desc $l  ";
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
			$dt1 = Input::get('dt1');
			$dt2 = Input::get('dt2');
			$limit = 20;
			$countRecord = $this->countRecord($cid, $search,$dt1,$dt2);
			$total_pages = $countRecord->cnt;
			$stages = 3;
			$page = ($args);
			$page = (int)$page;
			if($page) {
				$start = ($page - 1) * $limit;
			} else {
				$start = 0;
			}
			$company_op = $this->get_active_record($cid, $start, $limit, $search,$dt1,$dt2);
			$this->getPageNavigation($page, $total_pages, $limit, $stages);
			if($company_op) {
				?>
				<div id="no-more-tables">
					<table class='table table-bordered table-condensed' id='tblSummaryOP'>
						<thead>
						<tr>
							<TH>ID</TH>
							<th><?php echo MEMBER_LABEL; ?></th>
							<TH><?php echo INVOICE_LABEL; ?></TH>
							<TH><?php echo DR_LABEL; ?></TH>
							<TH><?php echo PR_LABEL; ?></TH>
							<th>Sold date</th>
							<TH>Amount</TH>
							<th></th>
						</tr>
						</thead>
						<tbody>
						<?php

							foreach($company_op as $o) {

								if(!$o->sold_date) continue;
								?>
								<tr>
									<td><strong><?php echo escape($o->id); ?></strong></td>
									<td>
										<?php echo $o->member_name; ?>
									</td>
									<td>
										<?php echo $o->invoice; ?>
									</td>
									<td><?php echo $o->dr; ?></td>
									<td><?php echo $o->ir; ?></td>
									<td><?php echo date('F d, Y H:i:s A',$o->sold_date); ?></td>
									<td><?php echo number_format($o->amount,2); ?></td>

									<td></td>
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
				<div class='alert alert-info'>No record found</div>
				<?php
			}
			?>

			<?php
		}
	}
?>