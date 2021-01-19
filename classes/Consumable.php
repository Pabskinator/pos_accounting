<?php
	class Consumable extends Crud implements  PagingInterface{
		protected $_table = 'consumables';

		public function __construct($c = null){
			parent::__construct($c);
		}

		public function getConsumableByItemId($id = 0){
			$parameters = array();
			if($id){

				$parameters[] = $id;
				$q= 'Select * from consumables where  item_id=?';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}

			}
		}
		public function countRecord($cid,$member_id=0){

			$parameters = array();
			$parameters[] = $cid;

			if($member_id){
				$parameters[] = $member_id;
				$likewhere = " and m.id = ? ";
			} else {
				$likewhere='';
			}


			$q= "Select count(c.id) as cnt from consumable_amount c left join members  m on m.id=c.member_id left join payments p on p.id=c.payment_id where p.company_id = ?  $likewhere ";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->first();
			}

		}

		public function get_active_record($cid,$start=0,$limit=0,$member_id=0){

			$parameters = array();
			$parameters[] = $cid;
			if($limit){
				$l = " LIMIT $start,$limit";
			} else {
				$l='';
			}
			if($member_id){
				$parameters[] = $member_id;
				$likewhere = " and m.id = ? ";
			} else {
				$likewhere='';
			}

			// prepare the query
			$q= "Select c.*,m.lastname,m.firstname
 				from consumable_amount c
 				left join members  m on m.id=c.member_id
 				left join payments p on p.id=c.payment_id
 				where p.company_id = ?  $likewhere order by c.id desc  $l  ";
			//submit the query
			$data = $this->_db->query($q, $parameters);
			// return results if there is any
			if($data->count()){
				return $data->results();
			}
		}

		public function getAmountConsumable($memid){

			$parameters = array();
			$parameters[] = $memid;

			$q= "select amount,id from consumable_amount where member_id=? order by id desc limit 1";

			$data = $this->_db->query($q,$parameters);

			if($data->count()){
				// return the data if exists
				return $data->first();
			}

		}

		public function updateConsumable($amount,$memid){
			if($amount && $memid){

				$parameters = array();
				$prevamt = $this->getAmountConsumable($memid);
				$newamt = $prevamt->amount + $amount;
				$parameters[] = $newamt;
				$parameters[] = $prevamt->id;
				$parameters[] = $memid;
				$q= "update consumable_amount set amount = ? where id=? and member_id=?";
				$data = $this->_db->query($q,$parameters);
				if($data->count()){
					// return the data if exists
					return true;
				}

			}

		}

		public function getPageNavigation($page, $total_pages, $limit, $stages) {
			getpagenavigation($page, $total_pages, $limit, $stages);
		}


		public function countRecord2($cid,$like='',$dt1=0,$dt2=0){
			$parameters = array();
			$parameters[] = $cid;
			if($like){
				$parameters[] = "%$like%";
				$likewhere = " and m.lastname like ?";
			} else {
				$likewhere='';
			}
			if($dt1 && $dt2){
				$dt1 = strtotime($dt1);
				$dt2 = strtotime($dt2 . "1 day -1 sec");
				$whereDate = " and c.created >= $dt1 and c.created <= $dt2";
			} else {
				$whereDate = "";
			}


			$q= "Select count(c.id) as cnt from consumable_amount c left join members  m on m.id=c.member_id left join payments p on p.id=c.payment_id where p.company_id = ?  $likewhere $whereDate";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->first();
			}

		}

		public function get_active_record2($cid,$start=0,$limit=0,$like='',$dt1=0,$dt2=0){

			$parameters = array();
			$parameters[] = $cid;
			if($limit){
				$l = " LIMIT $start,$limit";
			} else {
				$l='';
			}
			if($like){
				$parameters[] = "%$like%";
				$likewhere = " and m.lastname like ? ";
			} else {
				$likewhere='';
			}
			if($dt1 && $dt2){
				$dt1 = strtotime($dt1);
				$dt2 = strtotime($dt2 . "1 day -1 sec");
				$whereDate = " and c.created >= $dt1 and c.created <= $dt2";
			} else {
				$whereDate = "";
			}

			// prepare the query
			$q= "Select c.*,m.lastname as member_name, s.invoice,s.dr, s.ir
				from consumable_amount c
				left join members  m on m.id=c.member_id
				left join payments p on p.id=c.payment_id
				left join (select * from sales group by payment_id) s on s.payment_id = p.id
				where p.company_id = ?  $likewhere $whereDate
				order by c.id desc  $l  ";
			//submit the query
			$data = $this->_db->query($q, $parameters);
			// return results if there is any
			if($data->count()){
				return $data->results();
			}
		}

		public function paginate($cid,$args) {

						$member_id = Input::get('member_id');
						$limit = 20;
						$countRecord = $this->countRecord($cid,$member_id);
						$total_pages = $countRecord->cnt;
						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}
						$company_op = $this->get_active_record($cid, $start, $limit,$member_id);
						$this->getPageNavigation($page, $total_pages, $limit, $stages);
						if($company_op) {
							?>
							<div id="no-more-tables">
							<table class='table' id='tblSales'>
							<thead>
							<tr>
								<TH>Member</TH>
								<TH>Created At</TH>
								<TH>Remarks</TH>
								<TH>Consumable</TH>
								<TH class='text-right'>Not yet matured</TH>
								<th class='text-right'>Bounce</th>
								<TH class='text-right'>Valid</TH>
								<th></th>
							</tr>
							</thead>
							<tbody>
							<?php
							$cheque = new Cheque();
							$now = time();
							foreach($company_op as $o) {
								// get not yet valid cheque
								// get bounche cheque
								$mycheques = $cheque->getMemberCheque($o->payment_id);
								$notyetmatured = 0;
								$bounce = 0;
								if($mycheques){
									foreach($mycheques as $indc){
										if($now < $indc->payment_date){
											if($indc->status == 1){
												$notyetmatured +=  $indc->amount;
											}
										}
										if($indc->status == 3){
											$bounce +=  $indc->amount;
										}
									}
								}
								$valid =  $o->amount - ($notyetmatured + $bounce);
								$rem = "";
								if($o->remarks == 'From Service:<br>' ){
									$service_id = $o->from_payment_id;
									if($service_id && is_numeric($service_id)){
										$service = new Item_service_details();
										$list = $service->getDetails($service_id);
										
										if($list){
											$rem = "From Service:<br>";
											foreach($list as $l){
												if($l->item_code){
													$rem .= $l->item_code ."<br>". $l->description ."<br>QTY: " . $l->qty ."<br>";
												} else {
													$rem .= "Item not listed<br>QTY: " . $l->qty . "<br>";
												}
												if($l->remarks){
													$rem .=  "Remarks: " . $l->remarks;
												}


											}
										}
									}
								} else {
									$rem = ($o->remarks) ?$o->remarks  : "<i class='fa fa-ban'></i>";
								}
								$ctr_info = "";
								if($o->payment_ids){
									$json_arr = json_decode($o->payment_ids);
									if($json_arr){
										$sale = new Sales();
										foreach($json_arr as $pymnts){
											if($pymnts->payment_id){
												$sale_info = $sale->getsinglesale($pymnts->payment_id);
												$cur_box = "";
												if($sale_info->invoice){
													$cur_box .= "<strong class='span-block text-success'>".INVOICE_LABEL.": ".$sale_info->invoice."</strong>";
												}
												if($sale_info->dr){
													$cur_box .= "<strong class='span-block text-success'>".DR_LABEL.": ".$sale_info->dr."</strong>";
												}
												if($sale_info->ir){
													$cur_box .= "<strong class='span-block text-success'>". PR_LABEL .": ".$sale_info->ir."</strong>";
												}
												if($pymnts->amount){
													if(!$cur_box){
														$cur_box .= "<strong class='span-block text-success'>NO ".INVOICE_LABEL.", ".DR_LABEL.", or ".PR_LABEL." yet</strong>";
													}
													$cur_box .= "<strong class='span-block text-success'>Amount: " .number_format($pymnts->amount,2)."</strong>";
												}


												$ctr_info .= "<div class='panel panel-default'><div class='panel-body'>".$cur_box."</div></div>";

											}
										}
									}
								} else if($o->is_exact){
									$sale = new Sales();
									$sale_info = $sale->getsinglesale($o->is_exact);
									$pay_con = new Payment_consumable();
									$pay_con_record = $pay_con->getRecord($o->is_exact);
									$pay_con_amount = $pay_con_record->amount ? $pay_con_record->amount : 0;
									if($sale_info->invoice){
										$ctr_info .= "<strong class='span-block text-success'>".INVOICE_LABEL.": ".$sale_info->invoice."</strong>";
									}
									if($sale_info->dr){
										$ctr_info .= "<strong class='span-block text-success'>".DR_LABEL.": ".$sale_info->dr."</strong>";
									}	
									if($sale_info->ir){
										$ctr_info .= "<strong class='span-block text-success'>". PR_LABEL .": ".$sale_info->ir."</strong>";
									}
									if($pay_con_record->amount){
										$ctr_info .= "<strong class='span-block text-success'>Amount: ". number_format($pay_con_record->amount,2)."</strong>";
									}

								}

								?>
								<tr>
									<td style='border-top: 1px solid #ccc;'>
										<strong><?php echo $o->lastname . ", " . $o->firstname; ?></strong>
										<?php
											if($member_id){
												echo "<input type='checkbox' data-id='$o->id' class='chkIds' />";
											}
										?>
									</td>
									<td style='border-top: 1px solid #ccc;'><?php echo date('m/d/Y H:i:s A',$o->created); ?></td>
									<td style='border-top: 1px solid #ccc;' class='text-danger'><?php echo $rem; ?>
										<?php
											if($ctr_info){
												echo "<h5 style='color:#444'>Consumable used:</h5>";
												echo  $ctr_info;
											}

										?>
									</td>
									<td style='border-top: 1px solid #ccc;'><input class='form-control' type="text" value="<?php echo $o->amount?>"></td>
									<td style='border-top: 1px solid #ccc;' class='text-right'><?php echo number_format($notyetmatured,2); ?></td>
									<td style='border-top: 1px solid #ccc;' class='text-right'><?php echo  number_format($bounce,2); ?></td>
									<td style='border-top: 1px solid #ccc;' class='text-right'><?php echo  number_format($valid,2); ?></td>
									<td style='border-top: 1px solid #ccc;' >
										<button class='btn btn-default btnUpdate' data-id="<?php echo $o->id; ?>">Update</button>
									</td>
								</tr>
								<?php
							}
						} else {
							?>
							<tr>
								<td colspan='5'><h3><span class='label label-info'>No Record Found...</span></h3></td>
							</tr>
							<?php
						}
					?>
					</tbody>
				</table>
			</div>
			<?php
		}


	public function paginate2($cid,$args) {
			$dt1 = Input::get('dt1');
			$dt2 = Input::get('dt2');

			$search = Input::get('search');
			$limit = 20;
			$countRecord = $this->countRecord2($cid, $search,$dt1,$dt2);
			$total_pages = $countRecord->cnt;
			$stages = 3;
			$page = ($args);
			$page = (int)$page;
			if($page) {
				$start = ($page - 1) * $limit;
			} else {
				$start = 0;
			}
			$company_op = $this->get_active_record2($cid, $start, $limit, $search,$dt1,$dt2);
			$this->getPageNavigation($page, $total_pages, $limit, $stages);
			if($company_op) {
				?>
				<div id="no-more-tables">
				<table class='table table-bordered table-condensed' id='tblSales'>
				<thead>
				<tr>

					<TH><?php echo MEMBER_LABEL; ?></TH>
					<TH>Service ID</TH>
					<TH><?php echo INVOICE_LABEL; ?></TH>
					<TH><?php echo DR_LABEL; ?></TH>
					<TH><?php echo PR_LABEL; ?></TH>
					<TH>Created At</TH>
					<TH>Remarks</TH>
					<TH>Amount</TH>

				</tr>
				</thead>
				<tbody>
				<?php
				$cheque = new Cheque();
				$now = time();
				foreach($company_op as $o) {
					// get not yet valid cheque
					// get bounche cheque


					if($o->remarks == 'From Service:<br>' ){
						$service_id = $o->from_payment_id;
						if($service_id && is_numeric($service_id)){
							$service = new Item_service_details();
							$list = $service->getDetails($service_id);
							if($list){
								$rem = "From Service:<br>";
								foreach($list as $l){
									if($l->item_code){
										$rem .= $l->item_code ."<br>". $l->description ."<br>QTY: " . $l->qty ."<qt>";
									} else {
										$rem .= "Item not listed<br>QTY: " . $l->qty ."<qt>";
									}


								}
							}

						}
					} else {
						$rem = ($o->remarks) ?$o->remarks  : "<i class='fa fa-ban'></i>";
					}

					?>
					<tr>
						<td style='border-top: 1px solid #ccc;'><strong><?php echo $o->member_name; ?></strong></td>
						<td style='border-top: 1px solid #ccc;'><?php echo $o->from_payment_id; ?></td>
						<td style='border-top: 1px solid #ccc;'><?php echo $o->invoice; ?></td>
						<td style='border-top: 1px solid #ccc;'><?php echo $o->dr; ?></td>
						<td style='border-top: 1px solid #ccc;'><?php echo $o->ir; ?></td>
						<td style='border-top: 1px solid #ccc;'><?php echo date('m/d/Y H:i:s A',$o->created); ?></td>
						<td style='border-top: 1px solid #ccc;' class='text-danger'><?php echo $rem; ?></td>
						<td style='border-top: 1px solid #ccc;'><?php echo $o->amount; ?></td>
					</tr>
					<?php
				}
			} else {
				?>
				<tr>
					<td colspan='5'><h3><span class='label label-info'>No Record Found...</span></h3></td>
				</tr>
				<?php
			}
			?>
			</tbody>
			</table>
			</div>
			<?php
		}
	}
?>