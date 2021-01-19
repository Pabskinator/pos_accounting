<?php

	class City_mun extends Crud implements PagingInterface{
		protected $_table = 'refcitymun';
		public function __construct($c=null){
			parent::__construct($c);
		}



		public function countRecord($search='',$province=''){
			$parameters = array();

				$whereSearch ='';
				$whereProvince='';

				if($search){
					$parameters[] = "%$search%";
					$whereSearch = " and rc.citymunDesc like ? ";
				}
				if($province){
					$parameters[] = "%$province%";
					$whereProvince = " and rp.provDesc like ? ";
				}

				$q = "Select count(rc.id) as cnt from refcitymun rc left join refprovince rp on rp.provCode = rc.provCode where 1=1 $whereSearch $whereProvince order by rc.citymunDesc asc";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}

		}
		public function get_record($start,$limit,$search='',$province=''){
			$parameters = array();
			$whereSearch ='';
			$whereProvince ='';
			if($limit){
				$l = " LIMIT $start,$limit";
			} else {
				$l='';
			}
			if($search){
				$parameters[] = "%$search%";
			$whereSearch = " and rc.citymunDesc like ? ";
			}
			if($province){
				$parameters[] = "%$search%";
				$whereProvince = " and rp.provDesc like ? ";
			}
			$q = "Select rc.*,rp.provDesc from refcitymun rc left join refprovince rp on rp.provCode = rc.provCode where 1=1 $whereSearch $whereProvince order by rc.citymunDesc asc $l";
			$data = $this->_db->query($q, $parameters);
			// return results if there is any
			if($data->count()){
				return $data->results();
			}

		}
		public function getProvinces(){
			$parameters = array();


			$q = "Select * from refprovince order by provDesc asc";
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
			$search = Input::get('search');
			$province = Input::get('province');

			$city = new City_mun();
			?>
			<div id="no-more-tables">
				<table class='table' id='tblSales'>
					<thead>
					<tr>

						<TH>Name</TH>
						<TH>Delivery Charge Cash</TH>
						<TH>Delivery Charge BT</TH>
						<th></th>

					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";

						$limit = 1;
						$countRecord = $this->countRecord($search,$province);

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$company_op = $this->get_record($start, $limit, $search,$province);
						$this->getPageNavigation($page, $total_pages, $limit, $stages);

						if($company_op) {

							foreach($company_op as $o) {

								?>
								<tr id='row<?php echo $o->id?>'>
									<td style='border-top:1px solid #ccc;' data-title='Name'><?php echo capitalize(escape($o->citymunDesc)) ?></td>
									<td style='border-top:1px solid #ccc;' data-title='Charge Cash '><?php echo number_format($o->del_charge_cash,2) ?></td>
									<td style='border-top:1px solid #ccc;' data-title='Charge BT'><?php echo number_format($o->del_charge_bt,2) ?></td>
									<td style='border-top:1px solid #ccc;' data-title='Action'>
										<button  data-cityname="<?php echo addslashes($o->citymunDesc); ?>" data-chargeBT="<?php echo $o->del_charge_bt ?>" data-chargeCash="<?php echo $o->del_charge_cash ?>" data-id='<?php echo escape($o->id) ?>' class='btn btn-default btn-sm btnEditCharge'>
											Edit
										</button>

									</td>

								</tr>
								<?php
							}
						} else {
							?>
							<tr>
								<td colspan='4' class='text-left'><h3>
										<span class='label label-info'>No Record Found...</span></h3></td>
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
