<?php
	class Product extends Crud implements PagingInterface{
		protected $_table = 'items';
		public function __construct($item=null){
			parent::__construct($item);
		}
		public function getPrice($id){
		$parameters = array();
		if($id){
			$now = strtotime(date('m/d/Y'));
			$parameters[] = $id;
			 $q= 'Select p.price,p.unit_id,p.id from prices p where  p.item_id=? and  p.effectivity <='.$now. " order by  p.effectivity desc limit 1";

			$data = $this->_db->query($q, $parameters);
			if($data->count()){
				// return the data if exists
				return $data->first();
			}
			}
		}
		public function itemALLJSON($cid = 0 ){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;

				$q = "Select * from items where is_active = 1 and company_id = ? limit 15";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function bundleOrAssemble($id){
			$parameters = array();
			if($id){

				$parameters[] = $id;
				$q= "Select i.id,ci.set_id from items i left join (Select DISTINCT(item_id_set) as set_id from composite_items)) ci on ci.set_id = i.id where i.id = ? and i.is_bundle = 1 ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function getPriceHistory($id){
			$parameters = array();
			if($id){

				$parameters[] = $id;
				$q= 'Select p.price,p.unit_id,p.id, p.effectivity from prices p where  p.item_id=?  order by  p.effectivity desc';

				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function getPriceByPriceId($id){
			$parameters = array();
			if($id){

				$parameters[] = $id;
				$q= 'Select price,unit_id,item_id,id from prices where  id=?';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function getLastBarcode($cid,$bc){
			$parameters = array();
			if($cid){

				$parameters[] = $cid;
				$parameters[] = "$bc%";

				 $q= "Select barcode from items where  company_id=? and barcode like ? order by barcode desc limit 1";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function changeCurrent($id){
			$parameters = array();
			if($id){
				$parameters[] = $id;
				$q= 'update prices set is_current=0 where  item_id=? and is_current=1';
				if($this->_db->query($q, $parameters)){
					return true;
				}
				return false;
			}
		}
		public function isProductExist($name='',$companyid=0,$getid=false){
			$parameters = array();
			if($name){
				$parameters[] = $name;
				$parameters[] = $companyid;
				$q= 'Select id,item_code from items  where item_code=? and is_active=1  and company_id = ?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return ($getid) ? $e->first(): true;
				}
				return false;
			}
		}
		public function isProductExistBydesc($name='',$companyid=0,$getid=false){
			$parameters = array();
			if($name){
				$parameters[] = $name;
				$parameters[] = $companyid;
				$q= 'Select id,item_code from items  where  description=? and is_active=1  and company_id = ?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return ($getid) ? $e->first(): true;
				}
				return false;
			}
		}
		public function getItemsAndInventories($branchid=0,$company_id=0,$rack_id=0,$bind_with=''){
			$parameters = array();
			if($branchid && $company_id){
				$parameters[] = $branchid;
				$parameters[] = $company_id;
				$whereRack = '';
				$whereBind= '';
				$branchid = (int) $branchid;
				$company_id = (int) $company_id;
				if($rack_id){
					$rack_id = (int) $rack_id;
					$whereRack = " and inv.rack_id = $rack_id";
				}

				if($bind_with){
					$bind_with = (int) $bind_with;
					$whereBind = " and CONCAT( ',', it.bind_with, ',' ) LIKE '%,$bind_with,%'";
				}
				$now  =time();
				//
				 $q= "SELECT p.price, p.price_id,ip.adjustment,
 				 	u.is_decimal,u.name as unit_name, it.id AS item_id, it.barcode,
 					 it.item_code, it.is_bundle,it.description,it.warranty,it.item_type,
 					 it.product_terminals,it.for_freebies, inv.id AS inventory_id,
 					 inv.qty, inv.rack_id, inv.branch_id, r.rack, it.category_id as categ_id,
 					 it.has_open_bundle
					FROM items it
					LEFT JOIN units u on u.id = it.unit_id
					LEFT JOIN inventories inv ON inv.item_id = it.id AND inv.branch_id =?
					LEFT JOIN racks r ON r.id = inv.rack_id
					LEFT JOIN item_price_adjustment ip on ip.item_id = it.id AND ip.branch_id = $branchid
					LEFT JOIN
						( Select a.item_id, a.effectivity, p.price, p.id as price_id from
							(Select p.item_id, max(p.effectivity) as effectivity  from prices p left join items i on i.id=p.item_id  where i.company_id=$company_id  and p.effectivity <= $now group by p.item_id) a
							left join prices p on p.item_id = a.item_id where a.effectivity = p.effectivity) p on p.item_id = it.id
					WHERE  it.company_id =? and it.is_active=1 $whereRack $whereBind order by it.item_code";
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
				return false;
			}
		}
		public function getItemsAndInventoriesPOS($branchid=0,$company_id=0,$rack_id=0,$bind_with=''){
			$parameters = array();
			if($branchid && $company_id){
				$parameters[] = $branchid;
				$parameters[] = $company_id;
				$whereRack = '';
				$whereBind= '';
				$branchid = (int) $branchid;
				$company_id = (int) $company_id;
				if($rack_id){
					$rack_id = (int) $rack_id;
					$whereRack = " and inv.rack_id = $rack_id";
				}

				if($bind_with){
					$bind_with = (int) $bind_with;
					$whereBind = " and CONCAT( ',', it.bind_with, ',' ) LIKE '%,$bind_with,%'";
				}
				$now  =time();
				//or ( it.is_active=1 and it.item_type != -1 )
				 $q= "SELECT p.price, p.price_id,ip.adjustment,
 				 	u.is_decimal,u.name as unit_name, it.id AS item_id, it.barcode,
 					 it.item_code, it.is_bundle,it.description,it.warranty,it.item_type,
 					 it.product_terminals,it.for_freebies, inv.id AS inventory_id,
 					 inv.qty, inv.rack_id, inv.branch_id, r.rack, it.category_id as categ_id,
 					 it.has_open_bundle
					FROM items it
					LEFT JOIN units u on u.id = it.unit_id
					LEFT JOIN inventories inv ON inv.item_id = it.id AND inv.branch_id =?
					LEFT JOIN racks r ON r.id = inv.rack_id
					LEFT JOIN item_price_adjustment ip on ip.item_id = it.id AND ip.branch_id = $branchid
					LEFT JOIN
						( Select a.item_id, a.effectivity, p.price, p.id as price_id from
							(Select p.item_id, max(p.effectivity) as effectivity  from prices p left join items i on i.id=p.item_id  where i.company_id=$company_id  and p.effectivity <= $now group by p.item_id) a
							left join prices p on p.item_id = a.item_id where a.effectivity = p.effectivity) p on p.item_id = it.id
					WHERE  it.company_id =? and it.is_active=1 $whereRack $whereBind order by it.item_code";
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
				return false;
			}
		}

		public function  priceMatrix($branchid = 0) {

			$parameters = array();
			$now = time();
			$q= "SELECT p.price, p.price_id,ip.adjustment,
						u.is_decimal,u.name as unit_name, it.id AS item_id, it.barcode, it.item_code,
						 it.is_bundle,it.description,it.warranty,it.item_type,it.product_terminals,
						 it.for_freebies, it.category_id as categ_id, it.has_open_bundle, cat.name as category_name
					FROM items it
					LEFT JOIN categories cat on cat.id = it.category_id
					LEFT JOIN units u on u.id = it.unit_id

					LEFT JOIN item_price_adjustment ip on ip.item_id = it.id AND ip.branch_id = $branchid
					LEFT JOIN
						( Select a.item_id, a.effectivity, p.price, p.id as price_id from
							(Select p.item_id, max(p.effectivity) as effectivity  from prices p left join items i on i.id=p.item_id  where  p.effectivity <= $now group by p.item_id) a
							left join prices p on p.item_id = a.item_id where a.effectivity = p.effectivity) p on p.item_id = it.id
					WHERE  1=1 and it.is_active=1  order by cat.name , it.item_code";


			$e = $this->_db->query($q, $parameters);
			if($e->count()){
				return $e->results();
			}

			return false;

		}
		public function getItemsAndInventoriesReserve($company_id=0){
			if($company_id){
				$parameters = array();
				$parameters[] = $company_id;
				$q= 'SELECT it.id AS item_id, it.barcode, it.item_code, it.description,it.item_type,it.product_terminals,it.for_freebies
					FROM items it
					WHERE it.company_id =? and it.is_active=1 ';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
				return false;
			}
		}
		public function getItemsWithInventories($company_id=0){
			$parameters = array();
			if($company_id){
				$parameters[] = $company_id;
				 $q= 'SELECT i.* from items i left join inventories inv on inv.item_id = i.id left join branches b on b.id=inv.branch_id where i.is_active=1 and i.company_id=? and inv.qty>0 group by inv.item_id';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
				return false;
			}
		}

		public function searchItem($c,$s,$f = false){
			if($c && $s){
				$parameters = array();
				$parameters[] = $c;
				$whereF = "";
				if($f == true){
					$whereF = " and i.is_franchisee_product in (1,2)";
				}
				$parameters[] = "%$s%";
				$parameters[] = "%$s%";
				$parameters[] = "%$s%";

				$q= "Select i.id,i.barcode,i.description,i.item_code,i.is_bundle, u.name as unit_name, i.item_type
					from items i left join units u on u.id= i.unit_id
					where i.company_id=?
					and (i.description like ? or i.barcode like ? or i.item_code like ?)
					and i.is_active=1 $whereF
					order by i.description limit 100";
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
				return false;
			}
		}
		public function searchSpare($c,$s){
			if($c && $s){
				$parameters = array();
				$parameters[] = $c;
				$parameters[] = "%$s%";
				$parameters[] = "%$s%";
				$parameters[] = "%$s%";
				$q= "Select id,barcode,description,item_code from items where company_id=? and (description like ? or barcode like ? or item_code like ?) and is_active=1 and is_spare=1 order by description";
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
				return false;
			}
		}
		public function checkConsumable($cid){
			$parameters = array();
			if($cid){

				$parameters[] = $cid;
				$q= 'Select count(id) from products where  company_id=? and item_type=2';
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return true;
				} else {
					return false;
				}
			}
		}
		public function getType($id){
			$parameters = array();
			if($id){
				$parameters[] = $id;
				$q= 'Select item_type from items where id=?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->first();
				}
				return false;
			}
		}
		public function countRecord($cid,$search='',$sortby='',$category=0,$franchisee=false,$dt_from=0,$dt_to=0){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;

				if($search) {
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$likewhere = " and (i.item_code like ? or i.barcode like ? or i.description like ? ) ";
				} else {
					$likewhere = "";
				}
				$wherecateg='';
				if($category){
					$parameters[] = $category;
					$wherecateg = " and i.category_id=? ";
				}
				$wherefranchisee='';
				if($franchisee){
					$wherefranchisee = " and i.is_franchisee_product in (1,2) ";
				}
				$whereDate = "";
				if($dt_from && $dt_to){
					$dt_from = strtotime($dt_from);
					$dt_to = strtotime($dt_to . "1 day -1 min");
					$whereDate = " and i.created >= $dt_from and i.created <= $dt_to";
				}

			 $q = "Select count(i.id) as cnt from items i left join categories c  on c.id = i.category_id where i.company_id=? and i.is_active=1 $likewhere $wherecateg  $wherefranchisee $whereDate $sortby";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function get_product_record($cid,$start,$limit,$search='',$sortby='',$category=0,$branch_id = 0, $franchisee=false,$dt_from=0,$dt_to=0){
			$parameters = array();
			if($cid){
				$parameters[] = $cid;
				$branch_id =(int)$branch_id;
				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}
				if($search){
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$likewhere = " and (i.item_code like ? or i.barcode like ? or i.description like ? ) ";
				} else {
					$likewhere='';
				}


				$wherecateg='';
				if($category){
					$parameters[] = $category;
					$wherecateg = " and i.category_id=? ";
				}
				$wherefranchisee='';
				if($franchisee){
					$wherefranchisee = " and i.is_franchisee_product in (1,2) ";
				}
				$whereDate = "";
				if($dt_from && $dt_to){
					$dt_from = strtotime($dt_from);
					$dt_to = strtotime($dt_to . "1 day -1 min");
					$whereDate = " and i.created >= $dt_from and i.created <= $dt_to";
				}
				if(!$sortby){
					$sortby = "order by i.item_code asc";
				} else {
					$sortby = trim($sortby);
					$arr_valid = ['order by i.barcode asc','order by i.barcode desc','order by i.item_code asc','order by i.item_code desc'];
					if(!in_array($sortby,$arr_valid)){
						$sortby = "order by i.item_code asc";
					}
				}


				$q= "Select i.*,c.name, ip.modified as last_price_update, ip.adjustment,cp.name as parent_name, cpp.name as parent_parent_name
					from items i
					left join categories c  on c.id = i.category_id
					left join categories cp on cp.id = c.parent
					left join categories cpp on cpp.id = cp.parent
					left join  ( select item_id, adjustment, modified from item_price_adjustment where branch_id=$branch_id) ip on  i.id = ip.item_id where i.company_id=? and i.is_active=1 $likewhere $wherecateg $wherefranchisee $whereDate $sortby $l";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function countCbmRecord($cid,$search=''){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;

				if($search) {
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$likewhere = " and (i.item_code like ? or i.barcode like ? or i.description like ? ) ";
				} else {
					$likewhere = "";
				}


				$q = "Select count(i.id) as cnt from items i left join categories c  on c.id = i.category_id where i.company_id=? and i.is_active=1 $likewhere ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function get_cbm_record($cid,$start,$limit,$search=''){
			$parameters = array();
			if($cid){
				$parameters[] = $cid;
				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}
				if($search){
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$likewhere = " and (i.item_code like ? or i.barcode like ? or i.description like ? ) ";
				} else {
					$likewhere='';
				}





				$q= "Select i.*,c.name,cp.name as parent_name,
				cpp.name as parent_parent_name
				from items i left join categories c  on c.id = i.category_id
				left join categories cp on cp.id = c.parent
				left join categories cpp on cpp.id = cp.parent
				 where i.company_id=? and i.is_active=1 $likewhere $l";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}

		public function countProduct($companyid=0){
			$parameters = array();
			if($companyid){
				$parameters[] =$companyid;
				$q= 'Select count(id) as cnt from items  where  is_active=1 and company_id=?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->first();
				}
			}
		}
		public function getItemChar($i=0){
			$parameters = array();
			if($i) {
				$parameters[] = $i;
				$q = "Select i.*,c.name from item_characteristics i left join characteristics c on c.id=i.characteristics_id  where i.item_id=?";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function getItemWithoutSupplier($supplier_id,$company_id){
			$parameters = array();
			if($supplier_id) {
				$parameters[] = $supplier_id;
				$parameters[] = $company_id;
			 	$q = "Select id, item_code , description,barcode from items where id not in (select item_id from supplier_item where supplier_id=?) and company_id=?";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
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
			$sortby = Input::get('sortby');
			$limit_by = Input::get('limit_by');
			$category_id = Input::get('category_id');
			$dt_from = Input::get('dt_from');
			$dt_to = Input::get('dt_to');

			$is_franchisee = false;
			if($user->hasPermission('is_franchisee')){
				$is_franchisee = true;
			}
			?>
			<div id="no-more-tables">
				<div class="table-responsive">

					<table class='table table_border_top' id='tblSales'>
						<thead>
						<tr>
							<TH data-sort='order by i.barcode ' class='page_sortby'>
								Barcode
							</TH>
							<TH data-sort='order by i.item_code ' class='page_sortby'>
								Item Code
							</TH>
							<TH>Price</TH>
							<TH>Category</TH>
							<TH>Created</TH>
							<TH>Actions</TH>

						</tr>
						</thead>
						<tbody>
						<?php
							//$targetpage = "paging.php";
							if($limit_by){
								$limit = $limit_by;
							} else {
								$limit = 20;
							}

							$countRecord = $this->countRecord($cid, $search, $sortby,$category_id,$is_franchisee,$dt_from,$dt_to);

							$total_pages = $countRecord->cnt;

							$stages = 4;
							$page = ($args);
							$page = (int)$page;

							if($page) {
								$start = ($page - 1) * $limit;
							} else {
								$start = 0;
							}

							$company_items = $this->get_product_record($cid, $start, $limit, $search, $sortby,$category_id,$user->data()->branch_id,$is_franchisee,$dt_from,$dt_to);
							$this->getPageNavigation($page, $total_pages, $limit, $stages);
							if($company_items) {
								foreach($company_items as $s) {
									$pd = new Product($s->id);
									$price = $pd->getPrice($s->id);
									$itemchar = $pd->getItemChar($s->id);
									$priceHistory = $pd->getPriceHistory($s->id);
									if($itemchar) {
										$itemcharjson = json_encode($itemchar);
									} else {
										$itemcharjson = '';
									}
									$adjustment = ($s->adjustment) ?$s->adjustment: 0;
									$categ_label = "";

									if($s->parent_parent_name){
										$categ_label = $s->parent_parent_name." <i class='text-danger fa fa-long-arrow-right'></i> " .$s->parent_name . " <i class='text-danger fa fa-long-arrow-right'></i> " . $s->name;
									} else if($s->parent_name){
										$categ_label = $s->parent_name . " <i class='text-danger fa fa-long-arrow-right'></i> " . $s->name;
									} else {
										$categ_label =  $s->name;
									}

									?>
									<tr  id='item_<?php echo $s->id; ?>'>
										<td data-title="Barcode"><?php echo escape($s->barcode) ?></td>
										<td data-title="Item">
											<?php echo escape($s->item_code) . "<br><small class='text-danger'>" . escape($s->description) . "</small>"; ?>
											<?php if($s->cbm_l != 0){
												?>
												<small class='span-block'>
													CBM: <?php echo $s->cbm_l . " X " .$s->cbm_w . " X ". $s->cbm_h ; ?>
													=
													<strong class='text-danger'>
														<?php echo number_format(($s->cbm_l  * $s->cbm_w  *  $s->cbm_h),3) ; ?>
													</strong>
												</small>
												<?php
											} ?>
											
										</td>
										<td data-title="Price">
											<?php echo escape(number_format($price->price+$adjustment, 2)); ?>
											<?php 
												if($s->last_price_update){
													?>
													<small class='span-block text-danger'>Last update: <?php echo date('m/d/Y', $s->last_price_update); ?></small>
													<?php
												}
											?>
										</td>
										<td data-title="Category"><?php echo $categ_label; ?></td>
										<td data-title="Date Created"><?php echo escape(date('m/d/Y H:i:s A', $s->created)); ?></td>

										<td>

											<a href='#' class='btn btn-primary productInfo' data-id="<?php echo escape($s->id); ?>" title='Product Info'><span class='glyphicon glyphicon-list'></span></a>
											<?php if($user->hasPermission('item_m')) { ?>
												<a class='btn btn-primary' href='addproduct.php?edit=<?php echo escape(Encryption::encrypt_decrypt('encrypt', $s->id)); ?>&page=<?php echo $page; ?>&search=<?php echo $search; ?>&categ=<?php echo $category_id; ?>' title='Edit Item'><span class='glyphicon glyphicon-pencil'></span></a>
												<a href='#' class='btn btn-primary deleteProduct' id="<?php echo escape(Encryption::encrypt_decrypt('encrypt', $s->id)); ?>" title='Delete Item'><span class='glyphicon glyphicon-remove'></span></a>
											<?php } ?>
											<?php
												if($user->hasPermission('barcode_p')) {
													?>
													<a class='btn btn-primary' href='barcode_main.php?id=<?php echo escape($s->id); ?>' title='Print Barcode'><span class='glyphicon glyphicon-print'></span></a>
												<?php } ?>
											<?php if($user->hasPermission('item_m')) { ?>
												<?php
												$s->hasimage = 0;
												if(file_exists("../item_images/" . $s->id . ".jpg")) {
													$s->hasimage = 1;
													?>
													<a href='#' class='btn btn-primary showImages' id='<?php echo escape($s->id); ?>' name='<?php echo escape($s->item_code); ?>' title='View Image'><span class='glyphicon glyphicon-picture'></span></a>

													<?php
												}

												?>
											<?php }

												$s->price = $price->price + $adjustment;
												$s->charjson = $itemcharjson;
												$ph = [];
												if($priceHistory){

													foreach($priceHistory as $indprice){
														$indprice->date = date('F d, Y',$indprice->effectivity);
														$ph[] = $indprice;
													}
												}

												$s->priceHistory = json_encode($ph);
											?>

											<input id='hidproduct<?php echo $s->id; ?>' type="hidden" value='<?php echo json_encode($s); ?>' />
										</td>

									</tr>
									<?php
								}
							} else {
								?>
								<tr>
									<td colspan='8'><h3><span class='label label-info'>No Record Found...</span></h3></td>
								</tr>
								<?php
							}
						?>
						</tbody>
					</table>
				</div>
			</div>
			<?php
		}

		public function test(){
				$parameters = array();
				$parameters[] = 14;
				//14 household
				$q= "Select i.id,p.price from items i left join prices p on p.item_id = i.id where i.category_id in (Select id from categories where parent = ? )";
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
				return false;

		}

		public function openBundleList($id = 0){
			$parameters = array();
			$parameters[] = $id;
			//14 household
			$q= "Select id, item_code, description from items where bind_with = ?";
			$e = $this->_db->query($q, $parameters);
			if($e->count()){
				return $e->results();
			}
			return false;

		}

	}
?>