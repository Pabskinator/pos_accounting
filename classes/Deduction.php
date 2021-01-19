<?php
	class Deduction extends Crud implements  PagingInterface{

		protected $_table = 'deductions';

		public function __construct($w=null){
			parent::__construct($w);
		}
		public function getByPids($pids = ""){

			if($pids){
				$parameters = [];

				$q= 'Select * from deductions  where  payment_id in ('.$pids.') and is_active = 1';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
				return false;
			}
		}
		public function getDeductedBackload($pid =0){
			$parameters = [];
			$parameters[] = $pid;
			$q = "Select sum(amount) as total_deduction_backload from deductions where payment_id = ? and (remarks = 'Back load item' or remarks ='Freight Expense') ";
			$data = $this->_db->query($q, $parameters);
			// return results if there is any
			if($data->count()){
				return $data->first();
			}
		}

		public function getDiscount($pid =0){
			$parameters = [];
			$parameters[] = $pid;
			$q = " Select sum(amount) as total_deduction from deductions where payment_id = ? and remarks = 'discount' ";
			$data = $this->_db->query($q, $parameters);
			// return results if there is any
			if($data->count()){
				return $data->first();
			}
		}

	public function getSummaryBySalestype($month =0, $year=0,$branch_id=0,$date_type=0){

			$parameters = [];
			$where_branch = "";

			if($branch_id){
				$branch_id = (int) $branch_id;
				$where_branch = " and ss.branch_id = $branch_id ";
			}

			$month = (int) $month;
			$year = (int) $year;

		if($date_type == 1){

				$where_date = " and (CASE WHEN wh.id IS NULL THEN   MONTH(FROM_UNIXTIME(ss.sold_date)) = $month and MONTH(FROM_UNIXTIME(ss.sold_date)) = $year ELSE  MONTH(FROM_UNIXTIME(wh.is_scheduled)) = $month and YEAR(FROM_UNIXTIME(wh.is_scheduled)) = $year and wh.status = 4  END) ";

		} else {
			$where_date = " and MONTH(FROM_UNIXTIME(ss.sold_date)) = $month and YEAR(FROM_UNIXTIME(ss.sold_date)) = $year ";
		}

			$q = "
						Select
						st.name as sales_type_name, d.remarks,
						sum(d.amount) as total_deduction
						from deductions d
						left join (select  s.payment_id , s.sales_type,s.sold_date, t.branch_id
											from sales s
											left join terminals t on t.id = s.terminal_id
											group by s.payment_id
								  ) ss on ss.payment_id = d.payment_id
						left join salestypes st on st.id = ss.sales_type
						 left join (Select id,status,is_scheduled, from_service , payment_id from wh_orders) wh on wh.payment_id = ss.payment_id
						where 1=1 $where_date $where_branch
						group by sales_type_name, d.remarks

						";
			$data = $this->_db->query($q, $parameters);
			// return results if there is any
			if($data->count()){
				return $data->results();
			}
		}

		public function countRecord($cid,$like='',$dt_from=0,$dt_to=0,$type=0,$branch_id=0,$status=0){
			$whereDate ='';
			$likewhere='';
			$parameters = array();
			$whereType = '';
			$whereBranch = '';
			$whereStatus = '';
			if($like){
				$parameters[] = "%$like%";
				$parameters[] = "%$like%";
				$parameters[] = "%$like%";
				$parameters[] = "%$like%";
				$likewhere = " and (m.lastname like ? or s.invoice like ? or s.dr like ? or s.ir like ? )";
			}





			if($dt_from && $dt_to){

				$dateStart = strtotime($dt_from);
				$dateEnd = strtotime($dt_to . '1 day -1 sec');
				$whereDate = " and s.sold_date >=$dateStart and s.sold_date <= $dateEnd";
			}

			if($type){
				$parameters[] = $type;
				$whereType = " and d.remarks=? ";
			}

			if($branch_id){
				$parameters[] = $branch_id;
				$whereBranch = " and s.branch_id = ? ";
			}

			if($status){
				$parameters[] = $branch_id;
				$whereStatus = " and d.status = ? ";
			}

			$q= "Select count(d.id) as cnt from deductions d
				left join members m on m.id = d.member_id
				left join (select s.invoice,  s.dr,  s.ir ,  s.payment_id,  s.sold_date, s.sales_type,t.branch_id from sales s left join terminals t on t.id = s.terminal_id group by s.payment_id)
				s on  s.payment_id = d.payment_id
				left join salestypes st on st.id = s.sales_type
					where 1 = 1 $whereDate $likewhere  $whereType $whereBranch $whereStatus ";

			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->first();
			}

		}

		public function get_active_record($cid,$start=0,$limit=0,$like='',$dt_from=0,$dt_to=0,$type='',$branch_id=0,$status=0,$date_type=0){

			$parameters = array();

			$likewhere='';
			$whereDate = '';
			$whereType='';
			$whereBranch = '';
			$whereStatus = '';
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
				$likewhere = " and (m.lastname like ? or s.invoice like ? or s.dr like ? or s.ir like ? )";

			}
			if($date_type == 1){
				if($dt_from && $dt_to) {

					$dateStart = strtotime($dt_from);
					$dateEnd = strtotime($dt_to . '1 day -1 sec');
					$whereDate = " and (CASE WHEN wh.id IS NULL THEN   and s.sold_date >=$dateStart and s.sold_date <= $dateEnd ELSE  wh.is_scheduled >= $dateStart and wh.is_scheduled <= $dateEnd and  and wh.status = 4  END) ";
				} else {
					$whereDate = " and (CASE WHEN wh.id IS NULL THEN  1 ELSE  wh.status = 4 END) ";
				}
			} else {
				if($dt_from && $dt_to){

					$dateStart = strtotime($dt_from);
					$dateEnd = strtotime($dt_to . '1 day -1 sec');

					$whereDate = " and s.sold_date >=$dateStart and s.sold_date <= $dateEnd";

				}
			}

			if($type){
				$parameters[] = $type;
				$whereType = " and d.remarks=? ";
			}
			if($branch_id){
				$parameters[] = $branch_id;
				$whereBranch = " and s.branch_id = ? ";
			}
			if($status){
				$parameters[] = $status;
				$whereStatus = " and d.status = ? ";
			}

			// prepare the query
			$q= "Select d.*, m.lastname as member_name, s.*,p.cr_number, st.name  as sales_type_name
				from deductions d
				left join members m on m.id = d.member_id
				left join (select s.invoice,  s.dr,  s.ir ,  s.payment_id,  s.sold_date, s.sales_type,t.branch_id from sales s left join terminals t on t.id = s.terminal_id group by s.payment_id)
				s on  s.payment_id = d.payment_id
				left join salestypes st on st.id = s.sales_type
				left join payments p on p.id = d.payment_id
				left join (Select id,status,is_scheduled, from_service , payment_id from wh_orders) wh on wh.payment_id = s.payment_id
				where 1=1 $likewhere  $whereDate $whereType $whereBranch $whereStatus order by st.name asc $l";
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
			$dt_from = Input::get('dt_from');
			$dt_to = Input::get('dt_to');
			$deduction_name = Input::get('deduction_name');
			$status = Input::get('status');

			$limit = 20;
			$countRecord = $this->countRecord($cid, $search,$dt_from,$dt_to,$deduction_name,0,$status);
			$total_pages = $countRecord->cnt;
			$stages = 3;
			$page = ($args);
			$page = (int)$page;
			if($page) {
				$start = ($page - 1) * $limit;
			} else {
				$start = 0;
			}

			$company_op = $this->get_active_record($cid, $start, $limit, $search,$dt_from,$dt_to,$deduction_name,0,$status);
			$this->getPageNavigation($page, $total_pages, $limit, $stages);

			if($company_op) {
				?>
				<div id="no-more-tables">
				<table class='table' id='tblSales'>
				<thead>
				<tr>
					<TH>Member</TH>
					<th>Date</th>
					<th>Details</th>
				</tr>
				</thead>
				<tbody>
				<?php

				foreach($company_op as $o) {

					$for_approval = "";
					$container_approval_button = "";
					if($o->status == 1){
						$for_approval = "For Approval";
						$container_approval_button = "<button data-id='$o->id'  class='btn btn-default btn-sm btnApproved pull-right'>Approved</button>";
					}

					?>
					<tr>
						<td style='border-top: 1px solid #ccc;' >
							<strong><?php echo $o->member_name; ?></strong>
							<small class='span-block'><strong>Invoice: </strong><?php echo ($o->invoice) ? $o->invoice:'N/A'; ?></small>
							<small class='span-block'><strong>DR: </strong><?php echo ($o->dr) ? $o->dr:'N/A'; ?></small>
							<small class='span-block'><strong>PR: </strong><?php echo ($o->ir) ? $o->ir:'N/A'; ?></small>
							<small class='span-block'><strong class='text-danger'> <?php echo $for_approval; ?> <?php echo $container_approval_button; ?></strong></small>
						</td>
						<td style='border-top: 1px solid #ccc;' >
							<span class='span-block'><strong>Created at: </strong><?php echo date('m/d/Y H:i:s A',$o->created); ?></span>
							<span  class='span-block'><strong>Date sold: </strong><?php echo date('m/d/Y H:i:s A',$o->sold_date); ?></span>
						</td>
						<td style='border-top: 1px solid #ccc;' >
							<span class='span-block'><strong>Type:</strong> <?php echo ($o->remarks) ? $o->remarks : 'N/A'; ?></span>
							<span class='span-block'><strong>Addtl Remarks:</strong> <?php echo ($o->addtl_remarks) ? $o->addtl_remarks : 'N/A'; ?></span>
							<span class='span-block'><strong>Amount:</strong> <?php echo number_format($o->amount,2); ?></span>
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
				No record
				<?php
			}
			?>

			<?php
		}
	}
?>