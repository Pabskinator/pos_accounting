<?php
	class Member_category_discount extends Crud implements PagingInterface{
		protected $_table = 'member_category_discount';

		public function __construct($m = null) {
			parent::__construct($m);
		}
		public function countRecord($search=''){
			$parameters = array();


			$parameters[] = 1;
			if($search) {
				$parameters[] = "%$search%";
				$likewhere = " and ( m.lastname like ? ) ";
			} else {
				$likewhere = "";
			}

			$q = "Select count(mc.id) as cnt from member_category_discount mc
				left join members  m on m.id = mc.member_id
				left join categories  categ on categ.id = mc.category_id
				where 1 = ? and mc.is_active = 1  $likewhere ";

			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				// return the data if exists
				return $data->first();
			}

		}

		public  function hasDiscount($item_id = 0,$member_id = 0){
			$parameters= [];

			$q = "
					Select mc.* from items i
					left join categories c1 on c1.id = i.category_id
					left join categories c2 on c2.id=  c1.parent
					left join member_category_discount mc on mc.member_id = $member_id
					where i.id = $item_id and (i.category_id = mc.category_id or c2.id = mc.category_id)

					";

			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				// return the data if exists
				return $data->first();
			}

		}

		public function get_record($start,$limit,$search=''){
			$parameters = array();



			if($limit){
				$l = " LIMIT $start,$limit";
			} else {
				$l='';
			}
			if($search){
				$parameters[] = "%$search%";
				$likewhere = " and (m.lastname like ? ) ";
			} else {
				$likewhere='';
			}

			$q= "Select mc.* , m.lastname as member_name, categ.name as category_name from member_category_discount mc
			left join members m on m.id = mc.member_id
			left join categories categ on categ.id = mc.category_id where mc.is_active = 1  $likewhere order by m.lastname $l";
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


			?>
			<div id="no-more-tables">
				<div class="table-responsive">


					<?php
						//$targetpage = "paging.php";

						$limit = 20;


						$countRecord = $this->countRecord($search);

						$total_pages = $countRecord->cnt;

						$stages = 4;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_items = $this->get_record($start, $limit, $search);
						$this->getPageNavigation($page, $total_pages, $limit, $stages);
						if($company_items) {
							?>
							<table class='table table_border_top' id='tblSales'>
								<thead>
								<tr>
									<TH>Member</TH>
									<TH>Category</TH>
									<TH>Discount 1</TH>
									<TH>Discount 2</TH>
									<TH>Discount 3</TH>
									<TH>Discount 4</TH>
									<th></th>
								</tr>
								</thead>
								<tbody>
								<?php

									foreach($company_items as $s) {
										?>
										<tr >
											<td><?php echo $s->member_name; ?></td>
											<td><?php echo $s->category_name; ?></td>
											<td><?php echo formatQuantity($s->discount_1); ?></td>
											<td><?php echo formatQuantity($s->discount_2); ?></td>
											<td><?php echo formatQuantity($s->discount_3); ?></td>
											<td><?php echo formatQuantity($s->discount_4); ?></td>
											<td>
												<button data-info='<?php echo json_encode($s); ?>'  class='btn btn-default btn-sm btnUpdate'>Update</button>
											</td>
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

	}
