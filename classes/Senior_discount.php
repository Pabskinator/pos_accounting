<?php
	class Senior_discount extends Crud  implements PagingInterface{

		protected $_table = 'senior_discounts';

		public function __construct($d = null) {
			parent::__construct($d);
		}

		public function countRecord($cid, $search = '') {
			$parameters = array();
			if($cid) {

				$whereSearch= "";

				if($search){
					$parameters[] = "%$search%";
					$whereSearch  = " and (s.senior_name like ? ) ";
				}

				$q = "Select count(*) as cnt from senior_discounts s where 1=1 $whereSearch ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function get_record($cid, $start, $limit, $search = '') {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				if($limit) {
					$l = " LIMIT $start,$limit";
				} else {
					$l = '';
				}
				$whereSearch="";
				if($search){

					$parameters[] = "%$search%";
					$whereSearch  = " and (s.senior_name like ? ) ";
				}

				$q = "Select s.* from senior_discounts s  where 1=1 $whereSearch order by s.id desc $l";

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
			// pages,
			$user = new User();
			$search = Input::get('search');
							
			$limit = 50;

			$countRecord = $this->countRecord($cid, $search);

			$total_pages = $countRecord->cnt;

			$stages = 4;
			$page = ($args);
			$page = (int)$page;

			if($page) {
				$start = ($page - 1) * $limit;
			} else {
				$start = 0;
			}

			$company_items = $this->get_record($cid, $start, $limit, $search);
			$this->getPageNavigation($page, $total_pages, $limit, $stages);
			if($company_items) {
				?>
				<div id="no-more-tables">
					<div class="table-responsive">

						<table class='table table_border_top' id='tblSales'>
							<thead>
							<tr>
								<TH>Name</TH>
								<TH>ID Number</TH>
								<TH>Item</TH>
								<TH>Total</TH>
								<TH>Discount</TH>
								<th>Created</th>
								<th></th>
							</tr>
							</thead>
							<tbody>
							<?php
				foreach($company_items as $s) {
					?>
					<tr>
						<td><?php echo $s->senior_name; ?></td>
						<td><?php echo $s->senior_id; ?></td>
						<td>
							<?php



								$str = $s->item_list;
								if($str) {
									$str = explode(' ', $str);
									$arr = [];
									$i = 1;
									$ctr = 1;
									foreach($str as $d) {
										if(isset($arr[$i])) $arr[$i] .= " " . $d; else $arr[$i] = $d;
										if($ctr % 2 == 0) $i++;
										$ctr++;
									}
									foreach($arr as $a) {

										$pair = explode(" ", $a);

										if(!is_numeric($pair[0])) {
											$pair[0] = strtoupper($pair[0]);
											if(isset($arr_codes[$pair[0]])) {
												$pair[0] = $arr_codes[$pair[0]];
											}
										}

										if(!(isset($pair[0]) && isset($pair[1]))) {
											continue;
										}
										$prod = new Product($pair[0]);
										echo $prod->data()->item_code . " <strong>QTY: " .$pair[1] . "</strong><br>";
									}
								}

							?>
						</td>
						<td><?php echo $s->senior_total; ?></td>
						<td><?php echo $s->senior_discount; ?></td>
						<td><?php echo date('m/d/Y',$s->created); ?></td>


					</tr>
					<?php
				}
				?>
				</tbody>
				</table>
				</div>
				</div>
				<?php
			} else {
				echo "<div class='alert alert-info'>No record.</div>";
			}

		}
	}
