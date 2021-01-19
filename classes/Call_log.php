<?php
	class Call_log extends Crud implements PagingInterface{
		protected $_table = 'call_logs';
		public function __construct($c=null){
			parent::__construct($c);
		}


		public function countRecord($search='',$type=1,$date_from = 0, $date_to = 0){
			$parameters = array();


				if($search) {
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$likewhere = " and (phone_number like ? or person_calling like ?) ";
				} else {
					$likewhere = "";
				}

				if($date_from && $date_to){
					$parameters[] = $date_from;
					$parameters[] = $date_to;
					$whereDate = "DATE(created) >= ? and DATE(created) <= ? ";
				}
				$parameters[] = $type;
				$wheretype = "and type = ? ";
				$q = "Select count(id) as cnt from call_logs where 1=1 $likewhere $whereDate $wheretype";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}

		}

		public function get_record($start=0,$limit=0,$search='',$type=1,$date_from = 0, $date_to = 0){
				$parameters = array();
				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}
				if($search) {
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$likewhere = " and (cl.phone_number like ? or cl.person_calling like ?) ";
				} else {
					$likewhere = "";
				}

				if($date_from && $date_to){
					$parameters[] = $date_from;
					$parameters[] = $date_to;
					$whereDate = " and DATE(cl.created) >= ? and DATE(cl.created) <= ? ";
				}

				$parameters[] = $type;
				$wheretype = "and cl.type = ? ";

				 $q = "Select cl.* from call_logs cl   where 1=1 $likewhere $whereDate $wheretype $l";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
		}


		public function getPageNavigation($page, $total_pages, $limit, $stages) {
			getpagenavigation($page, $total_pages, $limit, $stages);
		}

		public function paginate($cid, $args) {
			// pages,
			$user = new User();
			$search = Input::get('search');
			$dt_from = Input::get('dt_from');
			$dt_to = Input::get('dt_to');
			$type = Input::get('type');
			if($dt_from && $dt_to){
				$dt_from = date('Y-m-d',strtotime($dt_from));
				$dt_to = date('Y-m-d',strtotime($dt_to));
			}
			$type = ($type) ? $type : 1;

							$limit = 30;


							$countRecord = $this->countRecord($search,$type,$dt_from,$dt_to);

							$total_pages = $countRecord->cnt;

							$stages = 4;
							$page = ($args);
							$page = (int)$page;

							if($page) {
								$start = ($page - 1) * $limit;
							} else {
								$start = 0;
							}

							$records = $this->get_record($start, $limit, $search,$type,$dt_from,$dt_to);
							$this->getPageNavigation($page, $total_pages, $limit, $stages);

							if($records) {

								if($type == 1){
									$lbl1 = "Person Calling";
									$lbl3 = "Answered By";
								} else if ($type == 2){
									$lbl1 = "Contact";
									$lbl3 = "Caller";
								}
								?>
								<div class="container-fluid">
								<div id="no-more-tables">
								<div class="table-responsive">

								<table class='table table_border_top' id='tblSales'>
								<thead>
								<tr>
									<TH>Details</TH>
									<th>Remarks</th>
									<TH>Date</TH>
									<th>Attachment</th>
									<th></th>
								</tr>
								</thead>
								<tbody>

								<?php

									$upls = new Upload();

								foreach($records as $s) {

									$links = $upls->getAllImage($user->data()->company_id,'call_log',$s->id);

									?>
									<tr>
										<td data-title='Person calling'>
											<span class='span-block'><strong><?php echo $lbl1; ?>:</strong> <?php echo $s->person_calling; ?></span>
											<span class='span-block'><strong>Number:</strong> <?php echo $s->phone_number; ?></span>
											<span class='span-block'><strong><?php echo $lbl3; ?>:</strong> <?php echo ucwords($s->answered_by); ?></span>
											<span class='span-block'><strong>Technician: </strong> <?php echo ucwords($s->technician); ?></span>
										</td>
										<td data-title='Remarks' style='width:200px;'><?php echo $s->remarks; ?></td>
										<td data-title='Date'>

											<span class='span-block'><strong>Created: </strong> <?php echo substr($s->created,0,10); ?></span>
											<span class='span-block'><strong>Closed: </strong>
												<?php
													if($s->close_time){
														echo date('m/d/Y', $s->close_time);
													} else {
														?>
														<button data-id="<?php echo $s->id; ?>"  class='btn btn-default btn-sm btnDate' ><i class='fa fa-pencil'></i></button>
														<?php
													}
												?>


											</span>
										</td>
										<td>
										<?php
											if($links){
												foreach($links as $l){
													echo "<a class='btn btn-default btn-sm btnShowImage' data-url='$l->filename' href='../uploads/$l->filename' target='_blank' style='display:block;margin-bottom:3px;border-radius: 10px;width:180px;'>$l->filename</a>";
												}
											}
										?>
										</td>
										<td>
											<button data-id='<?php echo $s->id; ?>' class='btn btn-default btnRemarks'>Remarks</button>
										</td>
									</tr>
									<?php
								}
								?>
								</tbody>
								</table>
								</div>
								</div>
								</div>
								<?php
							} else {
								?>
								<div class='alert alert-info'>No record</div>
								<?php
							}
		}
	}
?>