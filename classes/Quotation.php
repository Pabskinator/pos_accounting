<?php
	class Quotation extends Crud  implements PagingInterface{
		protected $_table = 'quotations';
		public function __construct($q=null){
			parent::__construct($q);
		}

		public function countRecord($cid,$search='',$status=1,$user_id=0) {

			$parameters = array();

			$parameters[] = $status;
			$where_search = "";
			$where_user = "";
			if($search){
				$parameters[] = "%$search%";
				$parameters[] = "%$search%";
				$where_search = " and (q.quotation_for like ? or q.company_name like ? )";
			}
			if($user_id){
				$parameters[] = $user_id;
				$where_user = " and q.user_id = ? ";
			}
			$q = " Select count(q.id) as cnt
						from quotations q

						left join members m on m.id = q.member_id
						where  1=1  and q.status = ? $where_search $where_user ";

			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				return $data->first();
			}

		}

		public function get_record($cid, $start, $limit,$search="",$status=1,$user_id=0) {
			
			$parameters = array();
			$parameters[] = $status;
			if($cid) {

				if($limit) {
					$l = " LIMIT $start,$limit ";
				} else {
					$l = '';
				}

				$where_search = "";
				$where_user = "";

				if($search){
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$where_search = " and (q.quotation_for like ? or q.company_name like ? )";
				}

				if($user_id){
					$parameters[] = $user_id;
					$where_user = " and q.user_id = ? ";
				}

				$q = " Select  q.*, wh.invoice,wh.dr,wh.pr
					   from quotations q
					   left join members m on m.id = q.member_id
					   left join wh_orders wh on wh.id = q.order_id
					   where 1=1 and q.status= ?  $where_search $where_user $l ";

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
			$status = Input::get('status');
			$user = new User();
			$user_id = $user->data()->id;
			if($user->hasPermission('quotation_m')){
				$user_id = 0;
			}

			?>
			<div id="no-more-tables">
				<div class="table-responsive">

					<table class='table table_border_top' id='tblSales'>
						<thead>
							<tr>
								<th>Id</th>
								<th>Quotation For</th>
								<th>Company Name</th>
								<th>Date</th>
								<th>Status</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
						<?php

							$limit = 50;


							$countRecord = $this->countRecord($cid,$search,$status,$user_id);

							$total_pages = $countRecord->cnt;

							$stages = 4;
							$page = ($args);
							$page = (int)$page;

							if($page) {
								$start = ($page - 1) * $limit;
							} else {
								$start = 0;
							}

							$company_items = $this->get_record($cid, $start, $limit,$search,$status,$user_id);
							$this->getPageNavigation($page, $total_pages, $limit, $stages);
							if($company_items) {
								$arr_status = ['','For Approval','Approved','Declined','Ordered'];
								foreach($company_items as $s) {

									?>
									<tr>
										<td><strong><?php echo $s->id; ?></strong></td>
										<td>
											<span class='text-muted'><?php echo $s->quotation_for; ?></span>
									<?php
									if($s->status == 4 ){
										?>
											<span class='span-block'>Order ID: <span class='text-danger'><?php echo $s->order_id; ?></span></span>
										<?php if($s->invoice){
											?>
											<span class='span-block'><?php echo INVOICE_LABEL; ?>: <span class='text-danger'><?php echo $s->invoice; ?></span></span>
											<?php
										}?>
										<?php if($s->dr){
											?>
											<span class='span-block'><?php echo DR_LABEL; ?>: <span class='text-danger'><?php echo $s->dr?></span></span>
											<?php
										}?>
										<?php if($s->pr){
											?>
											<span class='span-block'><?php echo PR_LABEL; ?>: <span class='text-danger'><?php echo $s->pr?></span></span>
											<?php
										}?>




										<?php
									}
									?>

									</td>
									<td><?php echo $s->company_name; ?></td>
									<td><?php echo $s->quote_date; ?></td>
									<td class='text-danger'>
										<?php echo $arr_status[$s->status]; ?>
									</td>
									<td>
									<?php
									if($s->status != 3 ){
										?>
											<button class='btn btn-default btnReprint' data-id='<?php echo $s->id; ?>' >Reprint</button>
										<?php
									}
									?>
											<?php
												if($s->status == 2 ){
													?>
													<button class='btn btn-default btnDetails' data-member_name='<?php echo $s->quotation_for; ?>'  data-member_id='<?php echo $s->member_id; ?>'  data-id='<?php echo $s->id; ?>' >Submit Order</button>
													<?php
												}
											?>
											<?php
												if($s->status == 1 && $user_id == 0){
													?>

													<button class='btn btn-primary btnApprove' data-id='<?php echo $s->id; ?>' >Approve</button>
													<button class='btn btn-danger btnDecline' data-id='<?php echo $s->id; ?>' >Decline</button>
													<button class='btn btn-warning btnUpdate' data-id='<?php echo $s->id; ?>' >Update</button>
													<?php
												}
											?>
										</td>

									</tr>
									<?php
								}
							} else {
								?>
								<tr>
									<td colspan='6'><h3><span class='label label-info'>No Record Found...</span></h3></td>
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
?>