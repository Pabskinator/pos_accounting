<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	ini_set('memory_limit', '-1');
	error_reporting(E_ALL);

	require_once '../libs/phpexcel/Classes/PHPExcel.php';
	require_once '../includes/admin/page_head2.php';


	if(!$user->hasPermission('settings')) {
		// redirect to denied page
		Redirect::to(1);
	}

	function toUnixTimeStamp2($date){
		return ($date-32662)*24*60*60+612806400;
	}
	function removeExcessSpace($s){
		$s = trim($s);
		$s = str_replace('    ',' ',$s);
		$s = str_replace('   ',' ',$s);
		$s = str_replace('  ',' ',$s);
		return $s;
	}
	function removeUnwatedChar($s){
		$s = trim($s);
		$s = str_replace('mr.','',strtolower($s));
		$s = str_replace('mr','',strtolower($s));
		return strtolower($s);
	}

	$arragent = [
		'Ocampo, Mary Noel' => 103,
		'Falco, Karen'=>93,
		'Gonzales, John' => 107,
		'Mendoza, Elma' =>109 ,
		'Salvacion, Carlos'=>108,
		'Falco, Dorothy' => 106,
		'Nogas, Iya' => 0,
		'Alonzo, Ruben'=> 0,
		'Gomez, Annaliza' => 0,
		'Magbago, Lanniene' => 111,
		'Medina, Niño' => 0
	];

	$testitems = new Crud();

	$items_list = $testitems->get_active('items',['1','=','1']);
	$member_list = $testitems->get_active('members',['1','=','1']);
	$item_arr = [];
	$member_arr =[];
	foreach($items_list as $il){
		$item_arr[$il->description] =  $il->id;
	}
	foreach($member_list as $ml){
		$ln = strtolower(trim($ml->lastname));
		$member_arr[$ln] =  $ml->id;
	}
	$arr_use_username = [];

?>


	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<div class="row">
				<div class="col-md-6">
					<h1>
						<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Upload utilities
					</h1>
				</div>
				<div class="col-md-6">

				</div>
			</div>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}
		//	dump($_SESSION['test']);
		?>

		<div class="row">
			<div class="col-md-12">
				<?php
					if (Input::exists()){
						if(Token::check(Input::get('token'))){
							echo "<h3>".$_FILES["file"]["name"]."</h3>";
							$allowedExts = array("xls", "xlsx");
							$temp = explode(".", $_FILES["file"]["name"]);
							$type = $_POST['type'];
							$extension = end($temp);

							if ($_FILES["file"]["type"] == "application/vnd.ms-excel"
								|| $_FILES["file"]["type"] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
								&& in_array($extension, $allowedExts)) {
								$uploads_dir = "../tmp_files/";
								$filename = $_FILES["file"]["name"];
								$uniqid = uniqid();
								$isUploaded = move_uploaded_file($_FILES["file"]["tmp_name"], $uploads_dir . $_FILES["file"]["name"]);
								if($isUploaded) {

									if(!file_exists($uploads_dir . $_FILES['file']['name'])) {
										exit("FILE NOT FOUND!." . PHP_EOL);
									}
									$objPHPExcel = PHPExcel_IOFactory::load($uploads_dir . $_FILES['file']['name']);

									$sheetNames = $objPHPExcel->getSheetNames();
									// start of loop on each sheet

									$productInsert = true;
									$branchInsert = true;
									$memberInsert = false;
									$rackInsert = false;
									$inventoryInsert = true;
									$categoryInsert = false;
									$unitInsert = false;
									$adjustmentInsert = false;
									$diagnosisInsert = false;
									$return_html = "<table class='table'>";
									$ctrerror = 0;
									$ctrok = 0;
									$norack =0;
									$noitem = 0;
									$noboth =0;
									if($type=='inventories'){
										// truncate monitoring
										// truncate inventories
										$inventorytrun = new Inventory();
										//$inventorytrun->truncateTable('inventories');
										//$inventorytrun->truncateTable('inventory_monitoring');
										/*$inventorytrun->truncateTable('racks');
										$inventorytrun->truncateTable('wh_orders');
										$inventorytrun->truncateTable('wh_order_details');
										$inventorytrun->truncateTable('inventory_ammend');
										$inventorytrun->truncateTable('bad_order_details');
										$inventorytrun->truncateTable('add_inv_batch_details');
										$inventorytrun->truncateTable('inventory_issues');
										$inventorytrun->truncateTable('inventory_issues_monitoring');
										$inventorytrun->truncateTable('rack_audit_sp'); */
									}
									$ctr = 1;
									//$query = "INSERT INTO `reorder_points`(`item_id`, `order_point`, `order_qty`, `orderby_branch_id`, `orderto_branch_id`, `month`, `is_active`, `company_id`) VALUES";
									$okmemadj = 0;
									foreach($sheetNames as $index => $name) {
										$objPHPExcel->setActiveSheetIndex($index);
										$lastRow = $objPHPExcel->setActiveSheetIndex($index)->getHighestRow();
										$lastColumn = $objPHPExcel->setActiveSheetIndex($index)->getHighestColumn();
										$startRow = 2;

										for ($row = $startRow; $row <=$lastRow ; $row++){

											if($type == 'products'){ // upload products
												$colBarcode = "A";
												$colItemcode = "B";
												$colDescription = "C";
												$colPrice = "D";
												$colPriceEffectivity = "E";
												$colItemType = "F";
												$colSpare = "G";
												$colWarranty = "H";
												$colCateg = "I";
												$colUnit = "J";
												$units = [];
												$barcode = $objPHPExcel->getActiveSheet()->getCell($colBarcode.$row)->getValue();
												$itemcode = $objPHPExcel->getActiveSheet()->getCell($colItemcode.$row)->getValue();
												$description = $objPHPExcel->getActiveSheet()->getCell($colDescription.$row)->getValue();
												$price = $objPHPExcel->getActiveSheet()->getCell($colPrice.$row)->getValue();
												$priceEffectivity = strtotime('01/01/2016');
												$itemType = $objPHPExcel->getActiveSheet()->getCell($colItemType.$row)->getValue();
												$is_spare = $objPHPExcel->getActiveSheet()->getCell($colSpare.$row)->getValue();
												$warranty = $objPHPExcel->getActiveSheet()->getCell($colWarranty.$row)->getValue();
												$categ = $objPHPExcel->getActiveSheet()->getCell($colCateg.$row)->getValue();
												$unit = $objPHPExcel->getActiveSheet()->getCell($colUnit.$row)->getValue();
												$unit = trim($unit);
												$unitval = 1;

												if($itemType == 1){
													$itemType = -1;
												} else if ($itemType == 2){
													$itemType = 1;
												} else {
													$itemType = -1;
												}

												if(!$barcode) continue;

												if($productInsert){

													$prod = new Product();

													$prodarr = array(
														'barcode' => $barcode,
														'item_code' => trim($itemcode),
														'description' => $description,
														'category_id' => $categ, // tochange
														'company_id' => $user->data()->company_id,
														'is_active' => 1,
														'created' => strtotime(date('Y/m/d H:i:s')),
														'modified' => strtotime(date('Y/m/d H:i:s')),
														'item_type' => $itemType,
														'unit_id' => $unitval,
														'is_spare' => $is_spare,
														'is_bundle' => 0
													);

													$prod->create($prodarr);
													$lastid = $prod->getInsertedId();
													$prodPrice = new Price();
													$pricearr = array(
														'price' => $price,
														'item_id' => $lastid,
														'effectivity' => $priceEffectivity,
														'unit_id' => $unitval,
														'created' => strtotime(date('Y/m/d H:i:s'))
													);
													$prodPrice->create($pricearr);
													dump($prodarr);
													dump($pricearr);

												} else {
													//$return_html .= "<tr><td>$barcode</td><td>$itemcode</td><td>$description</td><td>$price</td><td>".date('m/d/Y',$priceEffectivity)."</td><td>$itemType</td><td>$is_spare</td><td>$warranty</td><td>".$_SESSION['test'][$categ]."</td></tr>";
												}
											}
											else if ($type == 'members'){ // upload members
												echo 1;
												$colLastname = "A";
												$colBday = "D";
												$colCell = "E";
												$colAddress= "F";
												$colEmail = "G";
												$colTerms= "H";
												$colHold= "I";
												$colMemberSince= "J";
												$colTin= "K";
												$colMembership = "N";
												$colMemberCode = "O";
												$colUsername = "P";
												$colPassword = "Q";
												$colBranch = "R";
												$firstname  = "T";
												$lastname = "U";


												$memberLastname = $objPHPExcel->getActiveSheet()->getCell($colLastname.$row)->getValue();
												$firstname = $objPHPExcel->getActiveSheet()->getCell($firstname.$row)->getValue();
												$lastname = $objPHPExcel->getActiveSheet()->getCell($lastname.$row)->getValue();
												$bday = $objPHPExcel->getActiveSheet()->getCell($colBday.$row)->getValue();
												$cell = $objPHPExcel->getActiveSheet()->getCell($colCell.$row)->getValue();
												$address = $objPHPExcel->getActiveSheet()->getCell($colAddress.$row)->getValue();
												$email = $objPHPExcel->getActiveSheet()->getCell($colEmail.$row)->getValue();
												$terms = 0;
												$hold = 0;
												$memberSince =  $objPHPExcel->getActiveSheet()->getCell($colMemberSince.$row)->getValue();
												$tin =  $objPHPExcel->getActiveSheet()->getCell($colTin.$row)->getValue();
												$membership_id =  $objPHPExcel->getActiveSheet()->getCell($colMembership.$row)->getValue();
												$member_code =  $objPHPExcel->getActiveSheet()->getCell($colMemberCode.$row)->getValue();
												$username =  $objPHPExcel->getActiveSheet()->getCell($colUsername.$row)->getValue();
												$password =  $objPHPExcel->getActiveSheet()->getCell($colUsername.$row)->getValue();
												$branch =  $objPHPExcel->getActiveSheet()->getCell($colBranch.$row)->getValue();

												$memberLastname = $memberLastname | '';
												$bday = $bday | '';
												$cell = $cell | '';
												$address = $address | '';
												$email = $email | '';
												$terms = $terms | 0;
												$hold = $hold | 0;
												$memberSince = $memberSince | '';
												$tin = $tin | '';
												$membership_id = $membership_id | '';
												$member_code = $member_code | '';
												$username = $username | '';
												$password = $password | '';
												$branch = $branch | '';
												$firstname = $firstname | '';
												$lastname = $lastname | '';
												$memberSince = toUnixTimeStamp2($memberSince);
												$position_id = 2;
												if(true){

													if(in_array($username,$arr_use_username)) continue;

													$arr_use_username[] = $username;
													$newmem = new Member();
													$arrCreate = array(
														'lastname' => $memberLastname,
														'personal_address' => $address,
														'email' => $email,
														'terms' => $terms,
														'contact_number' => $cell,
														'company_id' => $user->data()->company_id,
														'is_active' => 1,
														'is_blacklisted' => $hold,
														'member_since' => $memberSince,
														'tin_no' => $tin,
														'membership_id' => $membership_id,
														'member_num' => $member_code,
														'created' => strtotime(date('Y/m/d H:i:s')),
														'modified' => strtotime(date('Y/m/d H:i:s'))
													);
													$newmem->create($arrCreate);
													$last_member_id = $newmem->getInsertedId();
													$newUser = new User();
													$user_arr = array(
														'lastname' => $lastname,
														'firstname' => $firstname,
														'middlename' => '',
														'username' => $username,
														'password' => Hash::make($password),
														'is_active' => 1,
														'position_id' => $position_id,
														'branch_id' => 57,
														'is_member' => 1,
														'company_id' => $user->data()->company_id,
														'created' => strtotime(date('Y/m/d H:i:s')),
														'modified' => strtotime(date('Y/m/d H:i:s')),
														'member_id' =>$last_member_id
													);
													$newUser->create($user_arr);
													$pg_rel = new Point_group_rel();
													$results = $pg_rel->getRel($membership_id);
													if($results){
														foreach($results as $res){
															$point_cls = new Point();
															$user = new User();
															$point_cls->updateUserPoint($last_member_id,$user,0,0,$res->point_id,0);
														}
													}
													dump($arrCreate);
													dump($user_arr);
													//$return_html .= "<tr><td>$memberLastname</td><td>$memberSalestype</td><td>$contact2Firstname $contact2Lastname</td><td> $contact1Firstname $contact1Lastname</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>";

												} else {
												}


											} else if ($type == 'branches'){
												$colName = "A";
												$colDesc= "B";
												$colAddress= "C";


												$name = $objPHPExcel->getActiveSheet()->getCell($colName.$row)->getValue();
												$desc = $objPHPExcel->getActiveSheet()->getCell($colDesc.$row)->getValue();
												$address= $objPHPExcel->getActiveSheet()->getCell($colAddress.$row)->getValue();
												$name = trim($name);
												$desc = trim($desc);
												$address = trim($address);
												if($branchInsert){
													$branchcls = new Branch();
													$branchcls->create(array(
														'name' => $name,
														'description' => $desc,
														'address' => $address,
														'company_id' => $user->data()->company_id,
														'is_active' => 1,
														'created' => strtotime(date('Y/m/d H:i:s')),
														'modified' => strtotime(date('Y/m/d H:i:s'))
													));


												} else {
													$return_html .= "<tr><td>$name</td><td>$desc</td></tr>";
												}

											} else if($type == 'stations'){

											}
											else if($type == 'racks'){
												$branch_id = 0; //tochange
												$colRackName = "A";
												$colDescription= "B";
												$colStockman= "C";
												$colBranch= "D";


												$rack = $objPHPExcel->getActiveSheet()->getCell($colRackName.$row)->getValue();
												$description = $objPHPExcel->getActiveSheet()->getCell($colDescription.$row)->getValue();
												$stock_man = $objPHPExcel->getActiveSheet()->getCell($colStockman.$row)->getValue();
												$branch_id = $objPHPExcel->getActiveSheet()->getCell($colBranch.$row)->getValue();
												$rack = trim($rack);
												$description = ($description) ? $description : '';
												$stock_man = ($stock_man) ? $stock_man : '';
												$rack = ($rack) ? $rack : '';
												$rack = strtoupper($rack);
												if(true){
													$rackcls = new Rack();
													if(!$rackcls->isRackExists($rack,$user->data()->company_id,false,$branch_id)){
														 $rackcls->create(array(
															'rack' => $rack,
															'created' => strtotime(date('Y/m/d H:i:s')),
															'company_id' => $user->data()->company_id,
															'modified' => strtotime(date('Y/m/d H:i:s')),
															'is_active' => 1,
															'description' =>$description,
															'stock_man' => $stock_man,
															 'branch_id' => $branch_id
														));
													}
													$return_html .= "<tr><td>$rack</td><td>$description</td><td>$stock_man</td></tr>";
												} else {
													$return_html .= "<tr><td>$rack</td><td>$description</td><td>$stock_man</td></tr>";
												}
											}
											else if($type == 'inventories'){

												$colRack = "A";
												$colItemcode = "B";
												$colQty = "C";
												$colBranch = "D";

												$rack = $objPHPExcel->getActiveSheet()->getCell($colRack.$row)->getValue();
												$itemcode = $objPHPExcel->getActiveSheet()->getCell($colItemcode.$row)->getValue();
												$qty = $objPHPExcel->getActiveSheet()->getCell($colQty.$row)->getValue();
												$branch_id = $objPHPExcel->getActiveSheet()->getCell($colBranch.$row)->getValue();
												$rack = strtoupper(trim($rack));
												$rack = rtrim($rack,'-');
												if(!$rack) continue;
												$rackcls = new Rack();
												$rackres = $rackcls->isRackExists($rack,$user->data()->company_id,true,$branch_id);
												$rack_id = 0;
												$rack_name = "";
												if($rackres){
													$rack_id =  $rackres->id;
													$rack_name =  $rackres->rack;
												}

												$itemcls = new Product();


												$itemcode = str_replace("    ", " ", $itemcode);
												$itemcode = str_replace("   ", " ", $itemcode);
												$itemcode = str_replace("  ", " ", $itemcode);
												$itemcode = trim($itemcode);
												$itemres = $itemcls->isProductExist($itemcode,$user->data()->company_id,true);
												if($itemres){
													$item_id =  $itemres->id;
													$item_name =  $itemres->item_code;
												} else {
													$item_id = 0;
													$item_name = 'No item';
												}

												if($rack_id && $item_id){
													$inventory = new Inventory();
													if($inventoryInsert){
														$qty = trim($qty);
														$qty = (int) $qty;
															if($qty > 0){
																$inventory->addInventory($item_id,$branch_id,$qty,true,$rack_id);
															}

													}

													$return_html .= "<tr><td>$rack_name</td><td>$itemcode</td><td>$qty</td><td>$branch_id</td></tr>";
													$ctrok +=1;
												} else {
													if($rack && $itemcode){
														$ctrerror +=1;
													}
													if(!$rack){
														$norack +=1;
													}
													if(!$itemcode){
														$noitem +=1;
													}
													if(!$itemcode || !$rack){
														$noboth +=1;
													}


													$return_html .= "<tr class='bg-danger'><td>$rack -- $rack_id </td><td>$itemcode -- $item_id</td><td>$qty</td><td>$branch_id</td></tr>";
												}
											} else if($type == 'categories'){
												$colName = "A";
												$colParent= "B";


												$name = $objPHPExcel->getActiveSheet()->getCell($colName.$row)->getValue();
												$parent = $objPHPExcel->getActiveSheet()->getCell($colParent.$row)->getValue();
												$name = trim($name);
												$parent = trim($parent);
												if($categoryInsert){
													$categoryCls = new Category();

														$parentdet = $categoryCls->getCategoryByName($user->data()->company_id,$parent);
														if($parentdet){
															$parent_id = $parentdet->id;
														} else {
															$parent_id = 0;
														}
														$categoryCls->create(array(
															'name' => $name,
															'parent' => $parent_id,
															'company_id' => $user->data()->company_id,
															'is_active' => 1,
															'created' => strtotime(date('Y/m/d H:i:s')),
															'modified' => strtotime(date('Y/m/d H:i:s'))
														));

												} else {
													$return_html .= "<tr><td>$name</td><td>$parent</td></tr>";
												}

											} else if($type == 'units'){
												$colName = "A";
												$colDesc= "B";


												$name = $objPHPExcel->getActiveSheet()->getCell($colName.$row)->getValue();
												$desc = $objPHPExcel->getActiveSheet()->getCell($colDesc.$row)->getValue();
												$name = trim($name);
												$desc = trim($desc);
												if($unitInsert){
													$unitcls = new Unit();
													$unitcls->create(array(
														'name' => $name,
														'description' => $desc,
														'company_id' => $user->data()->company_id,
														'is_active' => 1,
														'is_decimal' => 0,
														'created' => strtotime(date('Y/m/d H:i:s')),
														'modified' => strtotime(date('Y/m/d H:i:s'))
													));


												} else {
													$return_html .= "<tr><td>$name</td><td>$desc</td></tr>";
												}

											} else if($type == 'adjustments'){
												$colItem = "A";
												$colBcomp= "C";
												$colAdjustment= "D";
												$branch_id = 36;

												$item = $objPHPExcel->getActiveSheet()->getCell($colItem.$row)->getValue();
												$adjustment = $objPHPExcel->getActiveSheet()->getCell($colAdjustment.$row)->getValue();
												$bcomp = $objPHPExcel->getActiveSheet()->getCell($colBcomp.$row)->getValue();
												$item = removeExcessSpace($item);

												$adjustment = ($adjustment) ? $adjustment : 0;
												if($bcomp == 0)  $adjustment =0;
												$itemcls = new Product();
												$itemres = $itemcls->isProductExist($item,$user->data()->company_id,true);
												if($itemres){
													$item_id =  $itemres->id;
													$item_name =  $itemres->item_code;
												} else {
													$item_id = 0;
													$item_name = 0;
												}
												if(true){
													$newAdjustment = new Item_price_adjustment();
													if($item_id && $bcomp != 0){
														$now = time();
														$adjarr = array(
															'branch_id' => $branch_id,
															'item_id' => $item_id,
															'adjustment' =>$adjustment,
															'created' => $now,
															'modified' => $now,
															'company_id' => $user->data()->company_id,
															'is_active' => 1
														);
														dump($adjarr);
														$newAdjustment->create($adjarr);
													}

												} else {
													$return_html .= "<tr><td>$item_name - $item_id</td><td>$adjustment</td></tr>";
												}

											} else if($type == 'member_adjustments'){

												$col_item_id = "B";
												$col_qty= "C";
												$col_adjustment = "D";
												$col_member_id = "F";
												$col_type = "L";
												$branch_id = 28;

												$member_id = $objPHPExcel->getActiveSheet()->getCell($col_member_id.$row)->getValue();
												$item_id = $objPHPExcel->getActiveSheet()->getCell($col_item_id.$row)->getValue();
												$adj = $objPHPExcel->getActiveSheet()->getCell($col_adjustment.$row)->getValue();
												$member_terms = new Member_term();

												$ex = $member_terms->memberTermsExist($member_id,$item_id,$adj,1);

												if(isset($ex->cnt) && !empty($ex->cnt) && $ex->cnt > 0){
													echo "Exists <br>";
												} else {
													$user = new User();
													$now = time();
													$arr_create = array(
														'member_id' => $member_id,
														'user_id' => $user->data()->id,
														'company_id' => $user->data()->company_id,
														'branch_id' => 28,
														'qty' => 1,
														'item_id' => $item_id,
														'adjustment' => $adj,
														'type' => 1,
														'discount_type' => 1,
														'transaction_type' => 0,
														'terms' => 0,
														'is_active' => 1,
														'status' => 2,
														'created' => $now,
														'modified' => $now
													);
													$member_terms->create($arr_create);
													echo "Not Exists <br>";
												}


											}else if($type == 'diagnosis'){
												$colmem = "B";
												$coDoc= "D";
												$coRem= "F";
												$coDate= "G";


												$mem = $objPHPExcel->getActiveSheet()->getCell($colmem.$row)->getValue();
												$doc = $objPHPExcel->getActiveSheet()->getCell($coDoc.$row)->getValue();
												$rem = $objPHPExcel->getActiveSheet()->getCell($coRem.$row)->getValue();
												$dt = strtotime($objPHPExcel->getActiveSheet()->getCell($coDate.$row)->getValue());

												if($doc == "Manny Calayan"){
													$doc =  1;
												} else {
													$doc = 2;
												}
												$mem = (int)($mem);
												$rem = trim($rem);

												if(true){
													$meddiag = new Med_diagnosis();
													$meddiag->create(array(
														'remarks' => $rem,
														'member_id' => $mem,
														'doctor_id' => $doc,
														'is_active' => 1,
														'created' => $dt,
														'company_id' => 1
													));


												} else {
													$return_html .= "<tr><td>$name</td><td>$desc</td></tr>";
												}

											}else if($type == 'critical'){
												$colitemcode = "A";
												$colqty= "B";
												$colbranch= "C";



												$item_code = $objPHPExcel->getActiveSheet()->getCell($colitemcode.$row)->getValue();
												$qty = $objPHPExcel->getActiveSheet()->getCell($colqty.$row)->getValue();
												$branch_id = $objPHPExcel->getActiveSheet()->getCell($colbranch.$row)->getValue();

												$item_code = removeExcessSpace($item_code);
												$itemcls = new Product();
												$itemres = $itemcls->isProductExist($item_code,$user->data()->company_id,true);
												$item_id=0;
												if($itemres){
													$item_id =  $itemres->id;
												}
												if($item_id){
													$op = new Reorder_point();

													$query = "INSERT INTO `reorder_points`(`item_id`, `order_point`, `order_qty`, `orderby_branch_id`, `orderto_branch_id`, `month`, `is_active`, `company_id`) VALUES";
													for($i= 1;$i<=12;$i++){
														$odqty =  $qty*2;
														$query .= "($item_id,$qty,$odqty,$branch_id,-2,$i,1,1),";
														/*$op->create(array(
															'item_id' => $item_id,
															'orderby_branch_id' => $branch_id,
															'orderto_branch_id' => -2,
															'orderto_supplier_id' => 0,
															'order_point' => $qty,
															'order_qty' => $qty*2,
															'is_active' => 1,
															'month' => $i,
															'company_id' => $user->data()->company_id
														));*/
													}
													$query = rtrim($query,',');
													$op->batchInsert($query);


														/*
													$op->create(array(
														'item_id' => $item_id,
														'orderby_branch_id' => $branch_office,
														'orderto_branch_id' => 0,
														'orderto_supplier_id' => 1,
														'order_point' => $office,
														'order_qty' => $office,
														'is_active' => 1,
														'month' => 13,
														'company_id' => $user->data()->company_id,
														'created' => strtotime(date('Y/m/d H:i:s')),
														'modified' => strtotime(date('Y/m/d H:i:s')),
													)); */
												}

											}else if($type == 'assemble'){
												$colset = "A";
												$colpart= "B";
												$colneed= "C";



												$set = $objPHPExcel->getActiveSheet()->getCell($colset.$row)->getValue();
												$parts = $objPHPExcel->getActiveSheet()->getCell($colpart.$row)->getValue();
												$needed= $objPHPExcel->getActiveSheet()->getCell($colneed.$row)->getValue();
												if(!$set) continue;

												$set = removeExcessSpace($set);
												$parts = removeExcessSpace($parts);
												$itemcls = new Product();
												$itemset= $itemcls->isProductExist($set,$user->data()->company_id,true);
												$itempart= $itemcls->isProductExist($parts,$user->data()->company_id,true);

												$set_item_id=0;
												if($itemset){
													$set_item_id =  $itemset->id;
												}
												$part_item_id=0;
												if($itempart){
													$part_item_id =  $itempart->id;
												}

												if($set_item_id && $part_item_id){
													$now = time();
													$composite = new Composite_item();
													$arrComp = array(
														'item_id_raw' => $part_item_id,
														'item_id_set' =>$set_item_id,
														'qty' => $needed,
														'created' =>$now,
														'modified' => $now,
														'company_id' => $user->data()->company_id,
														'is_active' => 1
													);
													$composite->create($arrComp);
													dump($arrComp);
												}

											}else if($type == 'bundles'){
												$colset = "A";
												$colpart= "B";
												$colneed= "C";



												$set = $objPHPExcel->getActiveSheet()->getCell($colset.$row)->getValue();
												$parts = $objPHPExcel->getActiveSheet()->getCell($colpart.$row)->getValue();
												$needed= $objPHPExcel->getActiveSheet()->getCell($colneed.$row)->getValue();
												if(!$set) continue;

												$set = removeExcessSpace($set);
												$parts = removeExcessSpace($parts);
												$itemcls = new Product();
												$itemset= $itemcls->isProductExist($set,$user->data()->company_id,true);
												$itempart= $itemcls->isProductExist($parts,$user->data()->company_id,true);

												$set_item_id=0;
												if($itemset){
													$set_item_id =  $itemset->id;
												}
												$part_item_id=0;
												if($itempart){
													$part_item_id =  $itempart->id;
												}

												if($set_item_id && $part_item_id){
													$now = time();
													$composite = new Bundle();
													$arrComp = array(
														'item_id_parent' => $set_item_id,
														'item_id_child' => $part_item_id,
														'child_qty'=> $needed,
														'company_id' =>$user->data()->company_id,
														'is_active' =>1,
														'created' =>time()
													);
													$composite->create($arrComp);
													$produpdate = new Product();
													$produpdate->update(array('is_bundle' => 1),$set_item_id);
													dump($arrComp);
												}

											}
										}
									}
									if($type == 'critical'){

									}


								} else {
									echo "Not uploaded ";
								}
							}
						} else {
							echo "invalid token";
						}
					} else {

					}

				?>

				<div class="form-group">
					<div class="row">
						<form action="" method="POST" enctype="multipart/form-data">
						<div class="col-md-3">
							<select name="type" id="type" class='form-control' required>
								<option value=""></option>
								<option value="products">Products</option>
								<option value="members">Members</option>
								<option value="inventories">Inventories</option>
								<option value="racks">Racks</option>
								<option value="categories">Categories</option>
								<option value="units">Units</option>
								<option value="adjustments">Price adjustment</option>
								<option value="member_adjustments">Member adjustment</option>
								<option value="branches">Branches</option>
								<option value="diagnosis">diagnosis</option>
								<option value="critical">critical</option>
								<option value="assemble">assemble</option>
								<option value="bundles">bundles</option>
							</select>
						</div>
						<div class="col-md-3">
							<input type="file" class='btn btn-default' name='file' id='file' required>
						</div>
						<div class="col-md-3">
							<input type='submit' class='btn btn-primary' name='btnUpload' value='UPLOAD'>
							<input type='hidden' name='token' value=<?php echo Token::generate(); ?>>
						</div>
						</form>
						<div class="col-md-3 text-right">
							<button id='btnDLformat' class='btn btn-default'><span class='glyphicon glyphicon-download'></span> Download Format</button>

						</div>
					</div>
				</div>

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Uploads</div>
					<div class="panel-body">
						<?php
							$branch = new Branch();
							$branches = $branch->get_active('branches',array('company_id','=',$user->data()->company_id));
							foreach($branches as $b){
								echo "<p><strong class='text-danger'>$b->id</strong> $b->name </p>";
							}
						?>
						<?php echo $return_html; ?>
						<?php if($type == 'inventories'){
							?>
							<h3><?php echo "ERROR: " .  $ctrerror . " No rack: " . $norack . " No Item code: " . $noitem   ?></h3>
							<h3><?php echo "SUCCESS: " .  $ctrok; ?></h3>
							<?php
						}?>
						<h4><?php echo $okmemadj; ?></h4>
					</div>


				</div>
			</div>
		</div>
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog" style='width:70%;'>
				<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id='mtitle'>Download Format</h4>
						</div>
						<div class="modal-body" id='mbody'>
							<div class="row text-center">
								<div class="col-sm-6 col-md-4">
									<div class="thumbnail dlExcelFormat" data-type='products' >
										<i class='fa fa-barcode fa-8x'></i>
										<h4>Products</h4>
									</div>
								</div>
								<div class="col-sm-6 col-md-4">
									<div class="thumbnail dlExcelFormat" data-type='members'>
										<i class='fa fa-users fa-8x'></i>
										<h4>Members</h4>
									</div>
								</div>
								<div class="col-sm-6 col-md-4">
									<div class="thumbnail dlExcelFormat" data-type='branches'>
										<i class='fa fa-map-marker fa-8x'></i>
										<h4>Branches</h4>
									</div>
								</div>
								<div class="col-sm-6 col-md-4">
									<div class="thumbnail dlExcelFormat" data-type='racks'>
										<i class='fa fa-bars fa-8x'></i>
										<h4>Racks</h4>
									</div>
								</div>
								<div class="col-sm-6 col-md-4">
									<div class="thumbnail dlExcelFormat" data-type='inventories'>
										<i class='fa fa-shopping-cart fa-8x'></i>
										<h4>Inventories</h4>
									</div>
								</div>
								<div class="col-sm-6 col-md-4">
									<div class="thumbnail dlExcelFormat" data-type='stations'>
										<i class='fa fa-bank fa-8x'></i>
										<h4>Stations</h4>
									</div>
								</div>
								<div class="col-sm-6 col-md-4">
									<div class="thumbnail dlExcelFormat" data-type='categories'>
										<i class='fa fa-list fa-8x'></i>
										<h4>Categories</h4>
									</div>
								</div>
								<div class="col-sm-6 col-md-4">
									<div class="thumbnail dlExcelFormat" data-type='units'>
										<i class='fa fa-list fa-8x'></i>
										<h4>Units</h4>
									</div>
								</div>
							</div>
						</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
	</div> <!-- end page content wrapper-->

	<script>

		$(document).ready(function() {
			$('body').on('click','.dlExcelFormat',function(){
				var type = $(this).attr('data-type');
				window.open(
					'../upload_format/'+type+'.xlsx',
					'_blank' // <- This is what makes it open in a new window.
				);
			});
			$('#type').select2({
				allowClear: true,
				placeholder:'Select Type'
			});
			$('body').on('click','#btnDLformat',function(){
				$('#myModal').modal('show');
			});

		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>