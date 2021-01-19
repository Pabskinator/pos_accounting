<?php

	/**
	 * Created by PhpStorm.
	 * User: temp
	 * Date: 4/4/2018
	 * Time: 10:27 AM
	 */
	class Refund extends  Crud implements PagingInterface{
		protected $_table = 'refunds';
		public function __construct($p=null){
			parent::__construct($p);
		}
		public function countRecord($cid,$search='',$dt_from=0,$dt_to=0,$branch_id=0) {

				$parameters = array();
				$parameters[] = $cid;
				$where_search = "";
				$where_created="";
				$where_branch = "";

				if($search){
					$where_search = " and m.lastname like '%$search%'";
				}

				if($branch_id){
					$branch_id = (int) $branch_id;
					$where_branch = " and b.id = $branch_id";
				}

				if($dt_from && $dt_to){
					$dt_from = strtotime($dt_from);
					$dt_to = strtotime($dt_to . "1 day -1 min");
					$where_created = " and r.created >= $dt_from and r.created <= $dt_to ";
				}

				$q = " Select count(r.id) as cnt
						from refunds r
						left join item_service_request i on i.id = r.service_id
						left join branches b on b.id = i.branch_id
						left join members m on m.id = i.member_id
						where  1=1  $where_search $where_created $where_branch ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {

					return $data->first();
				}

		}

		public function get_record($cid, $start, $limit,$search="",$dt_from=0,$dt_to=0,$branch_id=0) {
			$parameters = array();

			if($cid) {


				if($limit) {
					$l = " LIMIT $start,$limit ";
				} else {
					$l = '';
				}

				$where_search = "";
				$where_created="";
				$where_branch="";

				if($search){
					$where_search = " and m.lastname like '%$search%'";
				}

				if($branch_id){
					$branch_id = (int) $branch_id;
					$where_branch = " and b.id = $branch_id";
				}

				if($dt_from && $dt_to){
					$dt_from = strtotime($dt_from);
					$dt_to = strtotime($dt_to . "1 day -1 min");
					$where_created = " and r.created >= $dt_from and r.created <= $dt_to ";
				}

				$q = " Select  r.*, m.lastname as member_name, b.name as branch_name, i.remarks,i.invoice, i.dr, i.ir
						from refunds r
						left join item_service_request i on i.id = r.service_id
						left join branches b on b.id = i.branch_id
						left join members m on m.id = i.member_id
						where 1=1 and r.amount != 0 $where_search $where_created $where_branch $l ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}

			}

		}

		public function getPageNavigation($page, $total_pages, $limit, $stages) {
			getpagenavigation($page, $total_pages, $limit, $stages);
		}

		public function paginate($cid, $args) {

			$search = Input::get('search');
			$dt_from = Input::get('dt_from');
			$dt_to = Input::get('dt_to');
			$branch_id = Input::get('branch_id');



			?>
			<div id="no-more-tables">
				<div class="table-responsive">

					<table class='table table_border_top' id='tblSales'>
						<thead>
						<tr>
							<th>Id</th>
							<th>Service ID</th>
							<th>Client</th>
							<th>Branch</th>
							<th>Remarks</th>
							<th>Ctrl Number</th>
							<th>Amount</th>
							<th>Date</th>
							<th></th>
						</tr>
						</thead>
						<tbody>
						<?php

							$limit = 20;


							$countRecord = $this->countRecord($cid,$search,$dt_from,$dt_to,$branch_id);

							$total_pages = $countRecord->cnt;

							$stages = 4;
							$page = ($args);
							$page = (int)$page;

							if($page) {
								$start = ($page - 1) * $limit;
							} else {
								$start = 0;
							}

							$company_items = $this->get_record($cid, $start, $limit,$search,$dt_from,$dt_to,$branch_id);
							$this->getPageNavigation($page, $total_pages, $limit, $stages);
							if($company_items) {
								foreach($company_items as $s) {

									?>
									<tr>
										<td><?php echo $s->id; ?></td>
										<td><?php echo $s->service_id; ?></td>
										<td><?php echo $s->member_name; ?></td>
										<td><?php echo $s->branch_name; ?></td>
										<td><?php echo ($s->remarks) ? $s->remarks : '<i class="fa fa-ban"></i>'; ?></td>
										<td>
											<span class='span-block'>
												<?php
													$withctrl = false;
													if($s->invoice){
														$withctrl = true;
														echo INVOICE_LABEL . ":" . $s->invoice;
													}
												?>
											</span>
												<span class='span-block'>
												<?php
													if($s->dr){
														$withctrl = true;
														echo DR_LABEL . ":" . $s->dr;
													}
												?>
											</span>
												<span class='span-block'>
												<?php
													if($s->ir){
														$withctrl = true;
														echo PR_LABEL . ":" . $s->ir;
													}
												?>
											</span>
											<span class='span-block'>
												<?php
													if(!$withctrl){
														echo "<i class='fa fa-ban'></i>";
													}
												?>
											</span>
										</td>

										<td><strong class='text-danger'><?php echo $s->amount; ?></strong></td>

										<td><?php echo date('m/d/Y',$s->created); ?></td>

										<td>
											<button class='btn btn-default btn-sm btnUpdate'  data-id='<?php echo $s->id; ?>' data-amount='<?php echo $s->amount; ?>' >Update</button>
										</td>
									</tr>
									<?php
								}
							} else {
								?>
								<tr>
									<td colspan='4'><h3><span class='label label-info'>No Record Found...</span></h3></td>
								</tr>
								<?php
							}
						?>
						</tbody>
					</table>
				</div>
			</div>
			<?php
		}
	}