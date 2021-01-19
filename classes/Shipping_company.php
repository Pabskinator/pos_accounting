<?php
	class Shipping_company extends Crud implements PagingInterface{
		protected $_table='shipping_companies';
		public function __construct($s = NULL){
			parent::__construct($s);
		}
		public function countRecord(){
			$parameters = array();

			return $this->select("count(id) as cnt")
				->from("shipping_companies")
				->get($parameters)
				->first();


		}
		public function get_record($start,$limit){



			return  $this->select("*")
				->from($this->_table)
				->orderBy("id desc")
				->limitBy(("$start,$limit"))
				->get()
				->all();


		}
		public function getPageNavigation($page, $total_pages, $limit, $stages) {
			getpagenavigation($page, $total_pages, $limit, $stages);
		}

		public function paginate($cid, $args) {



			?>
			<div id="no-more-tables">
				<table class='table withBorder' id='tblSales'>
					<thead>
					<tr>

						<TH>Name</TH>
						<TH>Description</TH>
						<TH>Address</TH>
						<th>Created AT</th>
						<th></th>

					</tr>
					</thead>
					<tbody>
					<?php
						//$targetpage = "paging.php";

						$limit = 30;
						$countRecord = $this->countRecord();

						$total_pages = $countRecord->cnt;

						$stages = 3;
						$page = ($args);
						$page = (int)$page;
						if($page) {
							$start = ($page - 1) * $limit;
						} else {
							$start = 0;
						}

						$shipping_companies = $this->get_record($start, $limit);
						$this->getPageNavigation($page, $total_pages, $limit, $stages);

						if($shipping_companies) {

							foreach($shipping_companies as $o) {

								?>
								<tr id='row<?php echo $o->id?>'>
									<td style='border-top:1px solid #ccc;' data-title='Name'><?php echo capitalize(escape($o->name)) ?></td>

									<td><?php echo $o->description; ?></td>
									<td><?php echo $o->address; ?></td>
									<td><?php echo date('m/d/Y h:i:s A',$o->created); ?></td>
									<td>
										<a class='btn btn-primary' href='addcompanyshipping.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt',$o->id);?>' title='Edit Shipping'><span class='glyphicon glyphicon-pencil'></span></a>
										<a href='#' class='btn btn-primary deleteShipping' id="<?php echo Encryption::encrypt_decrypt('encrypt',$o->id);?>" title='Delete Shipping'><span class='glyphicon glyphicon-remove'></span></a>
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
?>