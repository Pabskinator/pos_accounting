<?php
	class Item_unit extends Crud  implements PagingInterface{
		protected $_table = 'item_units';
		public function __construct($s=null) {
			parent::__construct($s);
		}
		public function countRecord($search=''){
			$parameters = array();


			$parameters[] = 1;
			if($search) {
				$parameters[] = "%$search%";
				$likewhere = " and ( i.item_code like ? ) ";
			} else {
				$likewhere = "";
			}


			$q = "Select count(iu.id) as cnt from item_units iu left join items i on i.id = iu.item_id where 1 = ? and iu.is_active = 1  $likewhere ";

			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				// return the data if exists
				return $data->first();
			}

		}
		public function getUnits($id=0){
			$parameters = array();



			if($id){
				$parameters[] = $id;
				$q= "Select iu.*, u.name as unit_name from item_units iu left join units u on u.id = iu.name where iu.item_id = ?";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
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
				$parameters[]= "%$search%";
				$likewhere = " and (i.item_code like ? )";

			} else {
				$likewhere='';
			}

			$q= "Select iu.id , i.item_code, i.description, iu.qty,u.name as unit_name from item_units iu left join units u on u.id = iu.name left join items i on i.id = iu.item_id where iu.is_active = 1  $likewhere order by i.item_code  $l";
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


									<TH>Item Code</TH>
									<TH>Name</TH>
									<TH>Qty</TH>
									<th></th>

								</tr>
								</thead>
								<tbody>
								<?php

									foreach($company_items as $s) {
										?>
										<tr>
											<td><?php echo $s->description; ?></td>
											<td><?php echo $s->unit_name; ?></td>
											<td><?php echo formatQuantity($s->qty); ?></td>
											<td>
												<a href='#' class='btn btn-primary deleteItemUnit' id="<?php echo escape(Encryption::encrypt_decrypt('encrypt', $s->id)); ?>" title='Delete Item Unit'><span class='glyphicon glyphicon-remove'></span></a>
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

