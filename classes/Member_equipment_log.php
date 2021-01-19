<?php

	class Member_equipment_log extends Crud implements PagingInterface{
		protected $_table = 'member_equipment_logs';

		public function __construct($u=null){
			parent::__construct($u);
		}



		public function countRecord($cid=0, $like='',$member_id=0){
			$likewhere='';
			$memberWhere ='';
			$parameters = array();
			$parameters[] = $cid;
			if($like){

				$parameters[] = "%$like%";
				$likewhere = " and (i.item_code like ? ) ";

			}
			if($member_id){

				$parameters[] = $member_id;
				$memberWhere = " and me.member_id = ? ";

			}






			$q= "Select count(me.id) as cnt from member_equipment_logs me
				left join members  m on m.id=me.member_id
				left join items i on i.id = me.item_id
				where me.company_id = ? $likewhere  $memberWhere  ";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->first();
			}
		}

		public function get_active_record($cid,$start=0,$limit=0,$like='',$member_id=0){
			$parameters = array();
			$parameters[] = $cid;
			if($limit){
				$l = " LIMIT $start,$limit";
			} else {
				$l='';
			}
			$likewhere='';
			$memberWhere = '';
			if($like){
				$parameters[] = "%$like%";
				$likewhere = " and (i.item_code like ? ) ";
			}

			if($member_id){
				$parameters[] = $member_id;
				$memberWhere = " and me.member_id = ? ";
			}





			// prepare the query
			$q= "Select me.* , m.lastname as member_name , i.item_code, i.description from member_equipment_logs me
				left join members  m on m.id=me.member_id
				left join items i on i.id = me.item_id
				where me.company_id = ? $likewhere $memberWhere order by me.created desc $l  ";
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
			$member_id = Input::get('member_id');

			$limit = 100;
			$countRecord = $this->countRecord($cid, $search,$member_id);
			$total_pages = $countRecord->cnt;
			$stages = 3;
			$page = ($args);
			$page = (int)$page;
			if($page) {
				$start = ($page - 1) * $limit;
			} else {
				$start = 0;
			}
			$company_op = $this->get_active_record($cid, $start, $limit, $search,$member_id);
			$this->getPageNavigation($page, $total_pages, $limit, $stages);
			if($company_op) {
				?>
				<div id="no-more-tables">
					<table class='table table-bordered table-condensed' id='tblSummaryOP'>
						<thead>
						<tr>

							<TH>Member</TH>
							<TH>Item</TH>
							<TH>From Qty</TH>
							<th>To Qty</th>
							<th>Created</th>
						</tr>
						</thead>
						<tbody>
						<?php

							foreach($company_op as $o) {


								?>
								<tr>

									<td>
										<strong><?php echo escape($o->member_name); ?></strong>
									</td>
									<td>
										<?php echo escape($o->item_code); ?>
										<span class='span-block text-danger'><?php echo $o->description; ?></span>
									</td>
									<td><?php echo escape($o->from_borrowed_qty); ?></td>
									<td><?php echo escape($o->to_borrowed_qty); ?></td>
									<td><?php echo date('F d, Y H:i:s A',$o->created); ?></td>

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
				<div class="alert alert-info">No record.</div>
				<?php
			}
		}


	}
?>