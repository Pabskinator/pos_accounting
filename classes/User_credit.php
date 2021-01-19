<?php
	/*
	   If you�re reading this,
	   that means you have been put in charge of my previous project.
	   I am so, so sorry for you.
	   This code sucks, you know it and I know it. Move on and call me an idiot later
	   God speed.
	*/

	class User_credit extends Crud implements PagingInterface{
		protected $_table = 'user_credits';

		public function __construct($u=null){
			parent::__construct($u);
		}


		public  function getCRList($dt1=0,$dt2=0){

			if($dt1 && $dt2){
				$parameters = array();

				$q= "SELECT sum(amount) as cr_total,cr_number, dt_created FROM `cr_log_deposits`
				 where
					dt_created >= $dt1 and dt_created <= $dt2 group by cr_number
 				order by cr_number ";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}
		public  function getCRDepositDetails($cr_number=''){

			if($cr_number){
				$parameters = array();
				$parameters[] = $cr_number;
				$q= "SELECT cl.* , uc.created as dep_created_at FROM `cr_log_deposits` cl
					left join user_credits uc on uc.id = cl.deposit_id
				 where
					cl.cr_number= ?
 				";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}
		public  function getCRDeposits($dt1=0,$dt2=0,$member_id = 0){

			if($dt1 && $dt2){
				$parameters = array();
				//$parameters[] = $dt1;
				//$parameters[] = $dt2;
				$whereMember = "";
				if($member_id){
					$parameters[] = $member_id;
					$whereMember = " and uc.member_id = ? ";
				}
				$q= "Select uc.*, m.lastname as member_name
 					from user_credits uc
 					left join members m on m.id = uc.member_id
					where
					uc.created >= $dt1 and uc.created <= $dt2
					and uc.cr_number  = '' $whereMember

					";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}

		public  function addCrLog($member_name,$total,$id,$cr_number){

				$parameters = array();
				$parameters[] = $member_name;
				$parameters[] = time();
				$parameters[] = $total;
				$parameters[] = $cr_number;
				$parameters[] = $id;

					$q= "INSERT INTO `cr_log_deposits`(`member_name`, `dt_created`, `amount`, `cr_number`,`deposit_id`) VALUES (?,?,?,?,?)";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return true;
				}

		}

		public  function getCredit($mid){
			if($mid){
				$parameters = array();
				$parameters[] = $mid;
				$q= 'Select * from user_credits where  member_id=? and is_used=0';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}

		public  function depositCrDeposit($cr_number){
			if($cr_number){
				$parameters = array();
				$parameters[] = $cr_number;
				$q= "select sum(total) as total from user_credits where cr_number= ?";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
			}
		}

		public  function updateCredit($ids,$cr_number){
			if($ids && $cr_number){
				$parameters = array();
				$parameters[] = $cr_number;
				$q= "update user_credits set cr_number=? where id in ($ids)";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					return true;
				}
				return false;
			}
		}

		public function countRecord($cid=0, $like=''){
			$parameters = array();
			$parameters[] = $cid;
			if($like){
				$parameters[] = "%$like%";
				$likewhere = " and m.lastname like ? ";
			} else {
				$likewhere='';
			}


			$q= "Select count(uc.id) as cnt from user_credits uc left join members  m on m.id=uc.member_id  where uc.company_id = ?  $likewhere ";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->first();
			}
		}

		public function getDeposits($from =0, $to=0){
			$parameters = array();

			// prepare the query
			$whereDT = "";

			if($from && $to){
				$whereDT = " and uc.created >= $from and uc.created <= $to ";
			}

			$q= "Select uc.*,m.lastname,m.firstname, u.firstname as ufn, u.lastname as uln
				from user_credits uc
				left join users u on u.id = uc.user_id
				left join members  m on m.id=uc.member_id
				where 1=1 and uc.cr_number='' $whereDT ";

			//submit the query
			$data = $this->_db->query($q, $parameters);

			// return results if there is any
			if($data->count()){
				return $data->results();
			}

		}

		public function get_active_record($cid,$start=0,$limit=0,$like=''){
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

			// prepare the query
			$q= "Select uc.*,m.lastname,m.firstname, u.firstname as ufn, u.lastname as uln from user_credits uc left join users u on u.id = uc.user_id left join members  m on m.id=uc.member_id where uc.company_id = ?  $likewhere order by uc.created desc $l  ";
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
			$limit = 20;
			$countRecord = $this->countRecord($cid, $search);
			$total_pages = $countRecord->cnt;
			$stages = 3;
			$page = ($args);
			$page = (int)$page;
			if($page) {
				$start = ($page - 1) * $limit;
			} else {
				$start = 0;
			}
			$company_op = $this->get_active_record($cid, $start, $limit, $search);
			$this->getPageNavigation($page, $total_pages, $limit, $stages);
			if($company_op) {
				?>
				<div id="no-more-tables">
				<table class='table table-bordered table-condensed' id='tblSummaryOP'>
				<thead>
				<tr>
					<TH>ID</TH>
					<TH>Member</TH>
					<TH>Payment</TH>
					<TH>Data</TH>
					<th>Added by</th>
					<TH>Status</TH>
					<th>Created</th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php
				$cheque = new Cheque();
				$now = time();
				foreach($company_op as $o) {
					$types = ['','Cash','Credit Card','Check','Bank Transfer'];
					$used_arr  = ['Not Used','Used'];
					$type_name = isset($types[$o->status]) ? $types[$o->status] : 'Unknown payment';
					$payment_ids = "";
					$datalbl = "";
					$remaininglbl = "";
					$temp_holder = [];
					$used_total = $o->used_total;
					if($o->status == 1){
						$datalbl .= "Amount: " . $o->json_data;
						$remaininglbl .= "Remaining " . number_format(($o->json_data - $used_total),2);

					} else if ($o->status == 2){
						$temp_holder = json_decode($o->json_data);
						$total = 0;
						if($temp_holder){
							foreach($temp_holder as $c){
								$datalbl .= "<p>Card: " .$c->card_type."</p>";
							$datalbl .= "<p>Trance Number: " .$c->trace_number."</p>";
							$datalbl .= "<p>Date: " .$c->date."</p>";
							$datalbl .= "<p>Amount: " .$c->amount."</p>";
							$datalbl .= "<hr>";
							$total = $total +$c->amount;
							}
							$datalbl .= "Total : " . number_format($total,2);
						}
						$remaininglbl .= "Remaining " . number_format(($total - $used_total),2);
					} else if ($o->status == 3){

						$temp_holder = json_decode($o->json_data);
						$total = 0;
						if($temp_holder){
							foreach($temp_holder as $c){
								$datalbl .= "<p>Ctrl#: " .$c->cheque_number."</p>";
							$datalbl .= "<p>Date: " .$c->date."</p>";
							$datalbl .= "<p>Amount: " .$c->amount."</p>";
							$datalbl .= "<hr>";
							$total = $total +$c->amount;
							}
							$datalbl .= "Total : " . number_format($total,2);
							$remaininglbl .= "Remaining " . number_format(($total - $used_total),2);
						}

					} else if ($o->status == 4){

						$temp_holder = json_decode($o->json_data);
						$total = 0;
						if($temp_holder){
							foreach($temp_holder as $c){
								$datalbl .= "<p>Ctrl#: " . $c->credit_number ."</p>";
							$datalbl .= "<p>Date: " . $c->date. "</p>";
							$datalbl .= "<p>Bank: " . $c->bank_name . "</p>";
							$datalbl .= "<p>Amount: " . $c->amount. "</p>";
							$datalbl .= "<hr>";
							$total = $total +$c->amount;
							}
							$datalbl .= "Total : " . number_format($total,2);
							$remaininglbl .= "Remaining " . number_format(($total - $used_total),2);
						}

					}
					$ret_use_payment = "";
					if($o->payment_id){
						$payment_arr = json_decode($o->payment_id,true);
						if($payment_arr){
							foreach($payment_arr as $aa){
								$sales = new Sales();
								$det = $sales->getsinglesale($aa['payment_id']);
								$cur="";
								if(isset($det->invoice) && $det->invoice){
									$cur .= " <span class='label label-info'>Inv: $det->invoice</span> ";
								}

								if(isset($det->dr) && $det->dr){
									$cur .= " <span class='label label-info'>DR:  $det->dr</span> ";
								}

								if(isset($det->ir) && $det->ir){
									$cur .= " <span class='label label-info'>PR: $det->ir</span> ";
								}

								if($aa['amount']){
									$cur .= " <span class='label label-warning'>Paid Amount: " . number_format($aa['amount'],2) . "</span> ";
								}
								$ret_use_payment .= $payment_arr = "<span class='span-block'>$cur</span>";

							}
						}
					}
					?>
					<tr>
						<td><strong><?php echo escape($o->id); ?></strong></td>
						<td><strong><?php echo escape($o->lastname); ?> 		<span class='span-block'><?php echo $ret_use_payment; ?></span></strong>
							<span class='text-muted span-block'><?php echo escape($o->remarks); ?></span>
						</td>
						<td><?php echo escape($type_name); ?></td>
						<td>
							<?php echo $datalbl; ?>
							<span class='span-block'><?php echo "Used: " . number_format($o->used_total,2); ?></span>
							<span class='span-block'><?php echo $remaininglbl; ?></span>

						</td>
						<td><?php echo capitalize($o->ufn . " " . $o->uln); ?></td>
						<td class='text-danger'><?php echo $used_arr[$o->is_used]; ?></td>
						<td><?php echo date('F d, Y H:i:s A',$o->created); ?></td>
						<td><button class='btn btn-danger btnDelete' data-id='<?php echo Encryption::encrypt_decrypt('encrypt',$o->id); ?>' >Delete</button></td>
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