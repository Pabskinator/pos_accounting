<?php
	class Sms_to_send extends Crud  implements PagingInterface{
		protected $_table = 'sms_to_send';
		public function __construct($s=null) {
			parent::__construct($s);
		}
		public function countRecord($search='',$status=0){
			$parameters = array();

				if($search) {
					$parameters[] = "%$search%";
					$likewhere = " and ( msg like ? ) ";
				} else {
					$likewhere = "";
				}
				$parameters[] = $status;

				$q = "Select count(id) from sms_to_send where 1 = 1 $likewhere and status = ?";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}

		}

		public function get_record($start,$limit,$search='',$status=0){
			$parameters = array();



				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}
				if($search){
					$parameters[] = "%$search%";
					$likewhere = " and ( msg like ? ) ";

				} else {
					$likewhere='';
				}
			$parameters[] = $status;
				$q= "Select * from sms_to_send where 1=1 $likewhere and status = ? order by id desc $l";
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

			$search = Input::get('search');
			$type = Input::get('type');

			?>
			<div id="no-more-tables">
				<div class="table-responsive">


						<?php
							//$targetpage = "paging.php";

								$limit = 20;


							$countRecord = $this->countRecord($search,$type);

							$total_pages = $countRecord->cnt;

							$stages = 4;
							$page = ($args);
							$page = (int)$page;
							if($page) {
								$start = ($page - 1) * $limit;
							} else {
								$start = 0;
							}

							$company_items = $this->get_record($start, $limit, $search,$type);
							$this->getPageNavigation($page, $total_pages, $limit, $stages);
							if($company_items) {
								?>
								<table class='table table_border_top' id='tblSales'>
									<thead>
									<tr>

										<TH>ID</TH>
										<TH>Message</TH>
										<TH>Number</TH>
										<TH>Status</TH>
										<th></th>

									</tr>
									</thead>
									<tbody>
								<?php
									$arr_status = ['Pending','Processed'];
								foreach($company_items as $s) {
									?>
										<tr>
											<td><?php echo $s->id; ?></td>
											<td><?php echo $s->msg; ?></td>
											<td><?php echo $s->number; ?></td>
											<td><?php echo $arr_status[$s->status]; ?></td>
											<td></td>
										</tr>
									<?php
								}
								?>
									</tbody>
								</table>
								<?php
							} else {
								?>
								<div class="alert alert-info">No record found</div>
								<?php
							}
						?>

				</div>
			</div>
			<?php
		}


		public function getPending(){
			$parameters = array();
			$parameters[] = 0;
			$q= "Select * from sms_to_send where 1=1 and status = ? order by id asc ";
			$data = $this->_db->query($q, $parameters);
			// return results if there is any
			if($data->count()){
				return $data->results();
			}
		}

		public function updateReceived($ids =''){
			$parameters = array();
			$parameters[] = 0;
			if($ids){
				$explode = explode(",",$ids);
				$listid = "";
				foreach($explode as $id){
					$id = (int) $id;
					$listid .= $id. ",";
				}
				$listid= rtrim($listid,",");
				$q= "Update sms_to_send set status = 1 where id in ($listid) and status = ?";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					return true;
				}
			}
		}
	}

