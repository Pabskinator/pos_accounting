
<!-- Page content -->
<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">

		<div class="content-header">
			<div class="row">
				<div class="col-md-6">
					<h1>
						<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
						<?php echo isset($editid) && !empty($editid) ? "EDIT PRODUCT" : "ADD PRODUCT"; ?>
					</h1>
				</div>
				<div class="col-md-6 text-right">
					<a class='btn btn-default hidden-xs' href="product.php">Back to Products</a>
				</div>
			</div>
		</div>

		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">

					<?php
						$edit_bind_with = [];
						$edit_bind_name = '';
						if(isset($editid) && !empty($editid)) {
							// edit
							// if edit , decrypt the id that came from get request
							$id = Encryption::encrypt_decrypt('decrypt', $editid);
							// get the data base of the product
							$editProd = new Product($id);
							//
							$editChar = new Item_characteristics();
							$pedit =  $editProd->getPrice($id);
							$myChar = $editChar->getMyCharacteristicsd($id);
							$con = new Consumable();
							$editCon = $con->getConsumableByItemId($id);

							if($editProd->data()->item_type == 5){
								$clsConFree = new Consumable_freebies();
								$editconfreeamount = $clsConFree->getConsumableFreebiesAmount($editProd->data()->id);
							}

							if(Configuration::getValue('open_bundle') == 1){
								if($editProd->data()->bind_with){
									$updatebind = explode(',',$editProd->data()->bind_with);
									foreach($updatebind as $ubind){
										$open_bundle = new Product($ubind);
										$edit_bind_with[] = ['text' => $open_bundle->data()->barcode .":".$open_bundle->data()->item_code .":".$open_bundle->data()->description,'id' => $ubind];
									}
								}
							}

						}

						// if submitted
						if (Input::exists()){

							// check token if match to our token
							if(Token::check(Input::get('token'))){
								// process here
								$validation_list = array(
									'barcode' => array(
										'required'=> true,
										'max' => 50
									),
									'item_code' => array(
										'required'=> true,
										'max' => 50
									),
									'unit_id' => array(
										'required'=> true,
										'max' => 50
									),
									'description' => array(
										'required'=> true,
										'max' => 100
									),
									'effectivity' => array(
										'max' => 20
									),
									'warranty' => array(
										'isnumber' => true
									),
									'category_id' => array(
										'required'=> true,
										'max' => 50
									)
								);

								if(!Input::get('edit')) {
									$additionalvalidation = array('unique' => 'items');
									$finalvalidation=array_merge($validation_list['barcode'],$additionalvalidation);
									$validation_list['barcode'] = $finalvalidation;
									$additionalvalidation = array('unique' => 'items');
									$finalvalidation=array_merge($validation_list['item_code'],$additionalvalidation);
									$validation_list['item_code'] = $finalvalidation;
									$additionalvalidation = array('unique' => 'items');
									$finalvalidation=array_merge($validation_list['description'],$additionalvalidation);
									$validation_list['description'] = $finalvalidation;
									$additemtypereq = array(
										'item_type' => array(
											'required'=> true
										)
									);
									$validation_list = array_merge($validation_list,$additemtypereq);
								}

								if(Input::get('item_type') == 2){
									$add1 = array(
										'qty' => array(
											'required'=> true
										),
										'days' => array(
											'required'=> true
										)
									);
									$validation_list = array_merge($validation_list,$add1);
								}

								if(Input::get('item_type') == 3 || Input::get('item_type') == 4){
									$add1 = array(
										'days' => array(
											'required'=> true
										)
									);
									$validation_list = array_merge($validation_list,$add1);
								}

								if(Input::get('item_type') == 5){
									$add1 = array(
										'days' => array(
											'required'=> true
										),
										'con_free_amount' => array(
											'required'=> true
										)
									);
									$validation_list = array_merge($validation_list,$add1);
								}

								$validate = new Validate();
								$validate->check($_POST, $validation_list);
								$bundle_length = count(Input::get('bundle_item'));

								if(Input::get('char') == '' ){
									$validate->addError("There should be at least 1 characteristics");
								}

								if(!empty($_FILES['item_img']['name'])){
									if ($_FILES["item_img"]["error"] > 0) {
										$validate->addError("There is a problem in your images");
									}
								}

								$priceitem = removeComma(Input::get('price'));
								$priceitem = ($priceitem) ? $priceitem : 0;
								if($validate->passed()){
									$prod = new Product();
									$prodPrice = new Price();
									$prdChar = new Item_characteristics();
									$consumable = new Consumable();
									$product_cost = 0;
									$item_weight = Input::get('item_weight');

									if($http_host == 'cebuhiq.apollosystems.com.ph'){
										$product_cost = Input::get('product_cost');
									}

									$bind_with = '';

									if(Configuration::getValue('open_bundle') == 1){
										$bind_with = Input::get('bind_with') ? Input::get('bind_with') : '';
										if($bind_with){
											$explode_bind = explode(',',$bind_with);
											foreach($explode_bind as $ebind){
												if(is_numeric($ebind) && $ebind){
													$prod->update(['has_open_bundle' => 1],$ebind);
												}
											}
										}
									}

									if(Input::get('edit')){
										$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
										try{
											// query if price change
											if(!empty($_FILES['item_img']['name'])){

												move_uploaded_file($_FILES["item_img"]["tmp_name"],
													"../item_images/" .$id . ".jpg");
											}
											$prod = new Product();
											$myprice = $prod->getPrice($id);
											$now = strtotime(date('m/d/Y'));
											$eff = strtotime(Input::get('effectivity'));
											$is_spare = (Input::get('is_spare')) ? Input::get('is_spare'): 0 ;
											$spare_type = (Input::get('spare_type')) ? Input::get('spare_type'): 0 ;
											if(!$is_spare){
												$spare_type = 0;
											}
											$warranty = (Input::get('warranty')) ? Input::get('warranty'): 0 ;
											if($priceitem != $myprice->price){
												$prodPrice->create(array(
													'price' => $priceitem,
													'item_id' => $id,
													'effectivity' => $eff,
													'unit_id' => Input::get('unit_id'),
													'created' => strtotime(date('m/d/Y'))
												));
											}
											$iprodterm = '';
											if(count(Input::get('terms'))){
												$insertProductTerminals = Input::get('terms');
												foreach($insertProductTerminals as $term){
													$term = substr($term,1);
													$iprodterm.=$term .",";
												}
												$iprodterm = rtrim($iprodterm,",");
											}
											$proddisplaylocation = '';
											if(count(Input::get('chkdis'))){
												$insertDisplayLocation = Input::get('chkdis');
												foreach($insertDisplayLocation as $indd){
													$indd = substr($indd,3);
													$proddisplaylocation.=$indd .",";
												}
												$proddisplaylocation = rtrim($proddisplaylocation,",");
											}

											$is_bundle = (Input::get('is_bundle')) ? Input::get('is_bundle'): 0 ;

											$prod->update(array(
												'barcode' => Input::get('barcode'),
												'item_code' => Input::get('item_code'),
												'description' => Input::get('description'),
												'category_id' => Input::get('category_id'),
												'company_id' => $user->data()->company_id,
												'modified' => strtotime(date('Y/m/d H:i:s')),
												'item_type' => Input::get('item_type'),
												'for_freebies' => Input::get('for_freebies'),
												'product_terminals' => $iprodterm,
												'display_location' => $proddisplaylocation,
												'unit_id' => Input::get('unit_id'),
												'is_spare' => $is_spare,
												'spare_type' => $spare_type,
												'has_serial' =>  Input::get('has_serial'),
												'is_franchisee_product' =>  Input::get('is_franchisee_product'),
												'has_certificate' =>  Input::get('has_certificate'),
												'cbm_l' =>  Input::get('cbm_l'),
												'cbm_w' =>  Input::get('cbm_w'),
												'cbm_h' =>  Input::get('cbm_h'),
												'for_selling' =>Input::get('for_selling'),
												'warranty' => $warranty,
												'product_cost' => $product_cost,
												'item_weight' => $item_weight,
												'is_bundle' => $is_bundle,
												'bind_with' => $bind_with
											), $id);

											if(Input::get('item_type') == 2){
												$days = Input::get('days');
												$consumable->update(array(
													'qty' => Input::get('qty'),
													'days' => $days
												),$editCon->id);
											}

											if(Input::get('item_type') == 3 || Input::get('item_type') == 4){
												$days = Input::get('days');
												$consumable->update(array(
													'qty' => 10000,
													'days' => $days
												),$editCon->id);
											}

											if(Input::get('item_type') == 5 ){
												$clsConFree->updateConFreeAmount($editProd->data()->id,Input::get('con_free_amount'));
											}

											if($prdChar->deleteMyCharacteristics($id)){
												foreach(Input::get('char') as $c){
													$prdChar->create(array(
														'item_id' => $id,
														'characteristics_id' => $c
													));
												}
											}

											Log::addLog($user->data()->id,$user->data()->company_id,"Update product info ||items:".$id,'admin/addproduct.php');
											Session::flash('productflash','Product information has been successfully updated' . "|" . $page . "|" . $id. "|" . $prev_search. "|" . $prev_categ);
											Redirect::to('product.php');

										} catch(Exception $e) {
											die($e->getMessage());
										}
									} else {
										// insert codes

										try {
											$iprodterm = '';
											if(count(Input::get('terms'))){
												$insertProductTerminals = Input::get('terms');
												foreach($insertProductTerminals as $term){
													$term = substr($term,1);
													$iprodterm.=$term .",";
												}
												$iprodterm = rtrim($iprodterm,",");
											}
											$proddisplaylocation = '';
											if(count(Input::get('chkdis'))){
												$insertDisplayLocation = Input::get('chkdis');
												foreach($insertDisplayLocation as $indd){
													$indd = substr($indd,3);
													$proddisplaylocation.=$indd .",";
												}
												$proddisplaylocation = rtrim($proddisplaylocation,",");
											}

											$mycom = new Company($user->data()->company_id);
											$lastbc = $prod->getLastBarcode($user->data()->company_id,$mycom->data()->bc_prefix);
											$prefix = $mycom->data()->bc_prefix;
											$num = substr($lastbc->barcode, 2);
											$last = $prefix . str_pad($num + 1, 6, "0", STR_PAD_LEFT);
											$is_spare = (Input::get('is_spare')) ? Input::get('is_spare'): 0 ;
											$spare_type = (Input::get('spare_type')) ? Input::get('spare_type'): 0 ;
											if(!$is_spare){
												$spare_type = 0;
											}

											$warranty = (Input::get('warranty')) ? Input::get('warranty'): 0 ;

											$is_bundle = (Input::get('is_bundle')) ? Input::get('is_bundle'): 0 ;

											$prod->create(array(
												'barcode' => $last,
												'item_code' => Input::get('item_code'),
												'description' => Input::get('description'),
												'category_id' => Input::get('category_id'),
												'company_id' => $user->data()->company_id,
												'is_active' => 1,
												'created' => strtotime(date('Y/m/d H:i:s')),
												'modified' => strtotime(date('Y/m/d H:i:s')),
												'item_type' => Input::get('item_type'),
												'for_freebies' => Input::get('for_freebies'),
												'product_terminals' =>$iprodterm,
												'display_location' => $proddisplaylocation,
												'unit_id' => Input::get('unit_id'),
												'is_spare' => $is_spare,
												'spare_type' => $spare_type,
												'has_serial' =>  Input::get('has_serial'),
												'is_franchisee_product' =>  Input::get('is_franchisee_product'),
												'has_certificate' =>  Input::get('has_certificate'),
												'cbm_l' =>  Input::get('cbm_l'),
												'cbm_w' =>  Input::get('cbm_w'),
												'cbm_h' =>  Input::get('cbm_h'),
												'for_selling' =>Input::get('for_selling'),
												'warranty' => $warranty,
												'is_bundle' => $is_bundle,
												'product_cost' => $product_cost,
												'bind_with' => $bind_with
											));
											$lastid = $prod->getInsertedId();
											$ef = strtotime(Input::get('effectivity'));

											$prodPrice->create(array(
												'price' => $priceitem,
												'item_id' => $lastid,
												'effectivity' => $ef,
												'unit_id' => Input::get('unit_id'),
												'created' => strtotime(date('Y/m/d H:i:s'))
											));
											if($is_bundle == 1){

												$bundle = new Bundle();
												$bundle_item = Input::get('bundle_item');
												$bundle_qty = Input::get('bundle_qty');

												for($bi = 0;$bi < $bundle_length; $bi++){
													$child_bundle_id = $bundle_item[$bi];
													$child_bundle_qty = $bundle_qty[$bi];
													$bundle->create([
														'item_id_parent' => $lastid,
														'item_id_child' => $child_bundle_id,
														'child_qty'=> $child_bundle_qty,
														'company_id' =>$user->data()->company_id,
														'is_active' =>1,
														'created' =>time()
													]);
												}


												// insert bundle
											}
											if(Input::get('item_type') == 2){
												$days = Input::get('days');
												$consumable->create(array(
													'qty' => Input::get('qty'),
													'item_id' => $lastid,
													'days' => $days
												));
											}
											if(Input::get('item_type') == 3 || Input::get('item_type') == 4){
												$days = Input::get('days');
												$consumable->create(array(
													'qty' => 10000,
													'item_id' => $lastid,
													'days' => $days
												));
											}
											if(Input::get('item_type') == 5){
												$days = Input::get('days');
												$con_free = new Consumable_freebies();
												$consumable->create(array(
													'qty' => 10000,
													'item_id' => $lastid,
													'days' => $days
												));
												$con_free_amount = Input::get('con_free_amount');
												$con_free->insertConFreeAmount($lastid,$con_free_amount,$user->data()->company_id);
											}
											if(Input::get('char')){
												foreach(Input::get('char') as $c){
													$prdChar->create(array(
														'item_id' => $lastid,
														'characteristics_id' => $c
													));
												}
											}

											if(!empty($_FILES['item_img']['name'])) {
												move_uploaded_file($_FILES["item_img"]["tmp_name"], "../item_images/" . $lastid . ".jpg");
											}


										} catch(Exception $e){
											die($e);
										}
										Log::addLog($user->data()->id,$user->data()->company_id,"Insert product ||items:".$id,'admin/addproduct.php');
										Session::flash('productflash','You have successfully added a Product');
										Redirect::to('product.php');
									}
								} else {
									$el ='';
									echo "<div class='alert alert-danger'>";
									foreach($validate->errors() as $error){
										$el.= escape($error) . "<br/>" ;
									}
									echo "$el</div>";
								}
							}
						}
						$newbc = new Product();
						$mycom = new Company($user->data()->company_id);
						$lastbc = $newbc->getLastBarcode($user->data()->company_id,$mycom->data()->bc_prefix);
						$prefix = $mycom->data()->bc_prefix;
						$num = substr($lastbc->barcode, 2);

						$num += 1;
						$last =$prefix . str_pad($num, 6, "0", STR_PAD_LEFT);



					?>

					<form class="form-horizontal" action="" method="POST" enctype="multipart/form-data">
						<fieldset>
							<legend>Item Information</legend>
							<?php
							    if(isset($id)){
								    $edit_categ_id = $editProd->data()->category_id;
							    } else {
								    if(Input::get('category_id')){
									    $edit_categ_id =  Input::get('category_id');
								    } else {
									    $edit_categ_id = 0;
								    }
							    }
							?>
							<input type="hidden" id='edit_categ_id' value='<?php echo $edit_categ_id; ?>'>

							<div class="form-group">
								<label class="col-md-1 control-label" for="barcode">Barcode</label>
								<div class="col-md-3">
									<input id="barcode" name="barcode" placeholder="Barcode" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($editProd->data()->barcode) : escape($last); ?>">
									<span class="help-block">Barcode of the product</span>
								</div>
								<label class="col-md-1 control-label" for="item_code">Item Name</label>
								<div class="col-md-3">
									<input id="item_code" name="item_code" <?php echo isset($id) ? 'readonly' : ''; ?> placeholder="Item Code" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($editProd->data()->item_code) : escape(Input::get('item_code')); ?>">
									<span class="help-block">Name of the product</span>
								</div>
								<label class="col-md-1 control-label" for="item_code">For Freebies</label>
								<div class="col-md-3">
									<input type='radio' value='1' name='for_freebies'
										<?php
											if (isset($id)) {
												if ($editProd->data()->for_freebies == 1)
												{
													echo 'checked';
												} else
												{
													echo '';
												}
											} else
											{
												echo '';
											}
										?> > Yes
									<input type='radio' value='0' name='for_freebies'
										<?php
											if (isset($id))
											{
												if ($editProd->data()->for_freebies == 0) {
													echo 'checked';
												} else {
													echo '';
												}
											} else {
												echo 'checked';
											} ?> > No
									<span class="help-block">Can be purchase as freebies</span>
								</div>

							</div>
							<div class="form-group">
								<label class="col-md-1 control-label" for="price">Price</label>
								<div class="col-md-3">
									<input id="price" name="price" data-id="<?php echo (isset($id))? escape($id):''; ?>" placeholder="Price" class="form-control input-md addcomma" type="text"
									       value="<?php
										       if(isset($id)){
											       echo  escape(number_format($pedit->price,2,'.',''));
										       } else{
											       echo escape(Input::get('price'));
										       }
									       ?>">
									<span class="help-block">Price of the product</span>
								</div>
								<label class="col-md-1 control-label" for="unit_id">Unit</label>
								<div class="col-md-3">
									<?php
										$unit = new Unit();
										$units= $unit->get_active('units', array('company_id', '=', $user->data()->company_id));

									?>
									<select id="unit_id" name="unit_id" class="form-control" >
										<option value="">--Choose Unit--</option>
										<?php foreach($units as $u):?>
											<option value="<?php echo $u->id; ?>"
												<?php
													if(isset($id)){
														echo ($editProd->data()->unit_id==$u->id) ? 'selected' :'';

													}
												?>
												><?php echo escape($u->name); ?></option>
										<?php endforeach; ?>
									</select>
									<span class="help-block">Unit for the product</span>
								</div>
								<div class='hiddenEffectivity' <?php echo isset($id) ? "style='visibility:hidden;'" : ''; ?>>
									<label class="col-md-1 control-label" for="price">Price Effectivity</label>
									<div class="col-md-3">
										<input autocomplete="off" id="effectivity" name="effectivity" placeholder="Effectivity" class="form-control input-md" type="text" >
										<span class="help-block">Effectivity of the price</span>
									</div>
								</div>
							</div>
							<?php if(isset($id)){
								$priceHistory = $editProd->getPriceHistory($id);
								if(count($priceHistory) > 1){
									?>
									<div class="form-group">
										<div class="row">
											<div class="col-md-1">
											</div>
											<div class="col-md-3">
												<div class="panel panel-default">
													<div class="panel-body">

														<table class='table'>
															<thead>
															<tr><th colspan="2">Price History</th></tr>
															<tr><th>Price</th><th>Effectivity</th></tr>
															</thead>
															<tbody>
															<?php
																foreach($priceHistory as $ph){
																	echo "<tr><td> " .number_format($ph->price,2) . "</td><td>".date('F d, Y',$ph->effectivity)."</td></tr>";
																}
															?>
															</tbody>
														</table>

													</div>
												</div>
											</div>
											<div class="col-md-8">

											</div>
										</div>

									</div>
									<?php
								}
							}?>
							<div class="form-group">
								<label class="col-md-1 control-label" for="description">Item Description</label>
								<div class="col-md-7">
									<input id="description" name="description" placeholder="Item Description" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($editProd->data()->description) : escape(Input::get('description')); ?>">
									<span class="help-block">Description of the product</span>
								</div>
								<label class="col-md-1 control-label" for="warranty">Warranty</label>
								<div class="col-md-3">
									<input id="warranty" name="warranty" placeholder="Warranty" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($editProd->data()->warranty) : escape(Input::get('warranty')); ?>">
									<span class="help-block">Product warranty in months (optional)</span>
								</div>
							</div>

							<div class="form-group">
								<label class="col-md-1 control-label" for="category_id">Category</label>
								<div class="col-md-7">
									<?php

										$ccc = new Category();
										$cc = objectToArray($ccc->getCategory($user->data()->company_id));
										$array = array();

										$_SESSION['test'] =[];
										function get_nested($array,$child = FALSE,$iischild='',$selectedid=0){

											$str = '';
											$mycateg = new Category();
											$thisuser = new User();
											if (count($array)){
												$iischild .= $child == FALSE ? '' : ' --> ';

												foreach ($array as $item){
													$haschild = $mycateg->hasChild($thisuser->data()->company_id,$item['id']);
													$disabledme='';
													if($haschild){
														$disabledme = ''; // disabled
													}
													if($selectedid){
														if($selectedid == $item['id']){
															$selected = 'selected';
															$selectedid=0;
														}
													} else {
														$selected = '';
													}


													if(isset($item['children']) && count($item['children'])){
														//$_SESSION['test'][$iischild.$item['name']] = $item['id'];
														$str .= '<option value="'.$item['id'].'" '.$disabledme.' '.$selected.'>'.$iischild.$item['name'].'</option>';
														$str .= get_nested($item['children'], true, $iischild . $item['name'],$selectedid);

													} else {
														if($child == false) $iischild='';
														//	$_SESSION['test'][$iischild.$item['name']] = $item['id'];
														$str .= '<option value="'.$item['id'].'" '.$disabledme.' '.$selected.'>'.$iischild.($item['name']).'</option>';
													}

												}
											}

											return $str;
										}

										function objectToArray ($object) {
											if(!is_object($object) && !is_array($object))
												return $object;

											return array_map('objectToArray', (array) $object);
										}
										function makeRecursive($d, $r = 0, $pk = 'parent', $k = 'id', $c = 'children') {
											$m = array();
											foreach ($d as $e) {
												isset($m[$e[$pk]]) ?: $m[$e[$pk]] = array();
												isset($m[$e[$k]]) ?: $m[$e[$k]] = array();
												$m[$e[$pk]][] = array_merge($e, array($c => &$m[$e[$k]]));
											}

											return $m[$r]; // remove [0] if there could be more than one root nodes
										}

									?>
									<select id="category_id" name="category_id" class="form-control hasChild" >
										<option value=""></option>
										<?php
											if(isset($id)){
												echo get_nested(makeRecursive($cc), FALSE,'',$editProd->data()->category_id);
											} else {
												echo get_nested(makeRecursive($cc));
											}
										?>


									</select>
									<span class="help-block">Category of the product</span>
								</div>
								<label class="col-md-1 control-label" for="has_serial">Has Serial?</label>
								<div class="col-md-3">
									<select id="has_serial" name="has_serial" class="form-control" >
										<option value="0"
											<?php
												if(isset($id)){
													echo (isset($editProd->data()->has_serial) && $editProd->data()->has_serial == 0) ? ' selected' : '';
												}
											?>
											>No</option>
										<option value="1"
											<?php
												if(isset($id)){
													echo (isset($editProd->data()->has_serial) && $editProd->data()->has_serial == 1) ? ' selected' : '';
												}
											?>
											>Yes</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-1 control-label" for="for_selling">Merchandise Type</label>
								<div class="col-md-3">
									<select id="for_selling" name="for_selling" class="form-control" >
										<option value="0"
											<?php
												if(isset($id)){
													echo (isset($editProd->data()->for_selling) && $editProd->data()->for_selling == 0) ? ' selected' : '';
												}
											?>
											>Merchandise Item</option>
										<option value="1"
											<?php
												if(isset($id)){
													echo (isset($editProd->data()->for_selling) && $editProd->data()->for_selling == 1) ? ' selected' : '';
												}
											?>
											>Non-Merchandise Item</option>
									</select>
								</div>
								<label class="col-md-1 control-label" for="is_spare">FG/RM</label>
								<div class="col-md-3">
									<select id="is_spare" name="is_spare" class="form-control" >
										<option value="0"
											<?php
												if(isset($id)){
													echo (isset($editProd->data()->is_spare) && $editProd->data()->is_spare == 0) ? ' selected' : '';
												}
											?>
											>Finished Goods</option>
										<option value="1"
											<?php
												if(isset($id)){
													echo (isset($editProd->data()->is_spare) && $editProd->data()->is_spare == 1) ? ' selected' : '';
												}
											?>
											>Raw Materials</option>
									</select>
								</div>
								<div id='sparetypeholder'
									<?php if(isset($id) &&  isset($editProd->data()->is_spare) && $editProd->data()->is_spare == 1){

									} else {
										?>
										style='display:none;'
										<?php
									} ?>
									>
									<label class="col-md-1 control-label" for="spare_type">Raw Material type</label>
									<div class="col-md-3">
										<?php
											$sparetype = new Spare_type();
											$alltypes = $sparetype->get_active('spare_type',['company_id','=',$user->data()->company_id]);
											$options = "";
											$curtype = 0;
											if(isset($id) && isset($editProd->data()->is_spare) && $editProd->data()->is_spare == 1){
												$curtype = $editProd->data()->spare_type;
											}
											if($alltypes){
												$options .= "<select class='form-control' name='spare_type'>";
												foreach($alltypes as $type){
													$selectedsptype = '';
													if($curtype == $type->id){
														$selectedsptype = 'selected';
													}
													$options .= "<option $selectedsptype value='$type->id'>$type->name</option>";
												}
												$options .= "</select>";
												echo $options;
											} else {
												echo "<p><a href='spare-type.php'>Please add raw material type first.</a></p>";
											}
										?>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-1 control-label" for="is_bundle">Is Bundle</label>
								<div class="col-md-3">
									<select id="is_bundle" name="is_bundle" class="form-control" <?php echo isset($id) ?  "" : '' ?>>
										<option value="0"
											<?php
												if(isset($id)){
													echo (isset($editProd->data()->is_bundle) && $editProd->data()->is_bundle == 0) ? ' selected' : '';
												}
											?>
											>No</option>
										<option value="1"
											<?php
												if(isset($id)){
													echo (isset($editProd->data()->is_bundle) && $editProd->data()->is_bundle == 1) ? ' selected' : '';
												}
											?>
											>Yes</option>
									</select>
								</div>
								<div id="bundleCon" style='display: none;'>
									<label class="col-md-1 control-label" for="bundle_list">Bundle List</label>
									<div class="col-md-3">
										<input id="bundle_list" name="bundle_list" placeholder="Bundle List" class="form-control input-md" type="text" >
										<span class="help-block">List of items in this bundle</span>
									</div>
								</div>
								<div id='alltypecon'>
									<label class="col-md-1 control-label" for="item_type">Item Type</label>
									<div class="col-md-3">

										<select id="item_type" name="item_type" class="form-control" <?php echo isset($id) ?  "disabled" : '' ?> >

											<option value="-1"
												<?php
													if(isset($id)){
														echo (isset($editProd->data()->item_type) && $editProd->data()->item_type == -1) ? ' selected' : '';
													} else {
														echo (Input::get('item_type') == -1) ? 'selected' :'';
													}
												?>
												>With Inventory</option>
											<option value="1"
												<?php
													if(isset($id)){
														echo (isset($editProd->data()->item_type) && $editProd->data()->item_type == 1) ? ' selected' : '';
													} else {
														echo (Input::get('item_type') == 1) ? 'selected' :'';
													}
												?>
												>Without Inventory</option>
											<option value="2"
												<?php
													if(isset($id)){
														echo (isset($editProd->data()->item_type) && $editProd->data()->item_type == 2) ? ' selected' : '';
													} else {
														echo (Input::get('item_type') == 2) ? 'selected' :'';
													}
												?>
												>Consumable</option>

											<option value="3"
												<?php
													if(isset($id)){
														echo (isset($editProd->data()->item_type) && $editProd->data()->item_type == 3) ? ' selected' : '';
													} else {
														echo (Input::get('item_type') == 3) ? 'selected' :'';
													}
												?>
												>Subscription</option>
											<option value="4"
												<?php
													if(isset($id)){
														echo (isset($editProd->data()->item_type) && $editProd->data()->item_type == 4) ? ' selected' : '';
													} else {
														echo (Input::get('item_type') == 4) ? 'selected' :'';
													}
												?>
												>Consumable Amount</option>
											<option value="5"
												<?php
													if(isset($id)){
														echo (isset($editProd->data()->item_type) && $editProd->data()->item_type == 5) ? ' selected' : '';
													} else {
														echo (Input::get('item_type') == 5) ? 'selected' :'';
													}
												?>
												>Consumable Freebies</option>
										</select>
										<?php
											if(isset($id)){
												?>
												<input type="hidden" name="item_type" value="<?php echo $editProd->data()->item_type; ?>" />
												<?php
											}
										?>
										<span class="help-block">Type of the Item</span>
									</div>

									<div id='subscriptionholder'

										<?php
											if(isset($editid) && !empty($editid) &&  ($editProd->data()->item_type == 3 || $editProd->data()->item_type == 5 ||$editProd->data()->item_type == 4 ||  $editProd->data()->item_type == 2)){
												echo "style='display:block'";
											} else {
												echo "style='display:none'";
											}
										?>>
										<label class="col-md-1 control-label" for="days">Valid Until</label>
										<div class="col-md-3">
											<input id="days" name="days" placeholder="Days" class="form-control input-md" type="text" value="<?php echo isset($id) ? $editCon->days : escape(Input::get('days')); ?>" >
											<span class="help-block">Validity of the item</span>
										</div>
									</div>
									<div id='consumableholder'

										<?php
											if(isset($editid) && !empty($editid) &&  $editProd->data()->item_type == 2){
												echo "style='display:block'";
											} else {
												echo "style='display:none'";
											}
										?>
										>
										<label class="col-md-1 control-label" for="qty">Consumable Quantity</label>
										<div class="col-md-3">
											<input id="qty" name="qty" placeholder="Quantity" class="form-control input-md" type="text" value="<?php echo isset($id) ? $editCon->qty : escape(Input::get('qty')); ?>">
											<span class="help-block">Consumable quantity of the Item</span>
										</div>

									</div>

									<div id="consumableamountholder"
										<?php
											if(isset($editid) && !empty($editid) &&  $editProd->data()->item_type == 5){
												echo "style='display:block'";
											} else {
												echo "style='display:none'";
											}
										?>
										>
										<label class="col-md-1 control-label" for="qty">Consumable Amount</label>
										<div class="col-md-3">
											<input id="con_free_amount" name="con_free_amount" placeholder="Amount" class="form-control input-md" type="text" value="<?php echo isset($id) ? $editconfreeamount->amount : escape(Input::get('con_free_amount')); ?>">
											<span class="help-block">Consumable amount of the Item</span>
										</div>
									</div>
								</div>
								<label style='<?php echo $displaynone; ?>' class="col-md-1 control-label" for="is_franchisee_product">Franchisee & Company Product</label>
								<div class="col-md-3" style='<?php echo $displaynone; ?>'>
									<select id="is_franchisee_product" name="is_franchisee_product" class="form-control" >
										<option value="0"
											<?php
												if(isset($id)){
													echo (isset($editProd->data()->is_franchisee_product) && $editProd->data()->is_franchisee_product == 0) ? ' selected' : '';
												}
											?>
											>Company Only</option>
										<option value="1"
											<?php
												if(isset($id)){
													echo (isset($editProd->data()->is_franchisee_product) && $editProd->data()->is_franchisee_product == 1) ? ' selected' : '';
												}
											?>
											>Franchisee Only</option>
										<option value="2"
											<?php
												if(isset($id)){
													echo (isset($editProd->data()->is_franchisee_product) && $editProd->data()->is_franchisee_product == 2) ? ' selected' : '';
												}
											?>
											>Company and Franchisee</option>
										<option value="3"
											<?php
												if(isset($id)){
													echo (isset($editProd->data()->is_franchisee_product) && $editProd->data()->is_franchisee_product == 3) ? ' selected' : '';
												}
											?>
											>Not applicable for reporting</option>
									</select>
								</div>
							</div>
							<div class="row">
								<label style='<?php echo $displaycerf; ?>' class="col-md-1 control-label" for="has_certificate">Has Certificate</label>
								<div class="col-md-3" style='<?php echo $displaycerf; ?>'>
									<select id="has_certificate" name="has_certificate" class="form-control" >
										<option value="0"
											<?php
												if(isset($id)){
													echo (isset($editProd->data()->has_certificate) && $editProd->data()->has_certificate == 0) ? ' selected' : '';
												}
											?>
											>No</option>
										<option value="1"
											<?php
												if(isset($id)){
													echo (isset($editProd->data()->has_certificate) && $editProd->data()->has_certificate == 1) ? ' selected' : '';
												}
											?>
											>Yes</option>

									</select>
								</div>

								<label style='<?php echo $displayProductCost; ?>' class="col-md-1 control-label" for="product_cost">Product cost</label>
								<div class="col-md-3" style='<?php echo $displayProductCost; ?>'>
									<input type="text" class='form-control' placeholder='Cost' id='product_cost' name='product_cost'  value="<?php echo isset($id) ? $editProd->data()->product_cost : escape(Input::get('product_cost')); ?>">
								</div>

								<?php
									if(Configuration::getValue('open_bundle') == 1){
										?>
										<label class="col-md-1 control-label" for="bind_with">Bundle To</label>
										<div class="col-md-3">
											<input type="text" class='form-control' id='bind_with' name='bind_with'  >
										</div>
										<?php
									}
								?>
							</div>
							<div style='clear: both;'></div>
							<br>
							<div class="row">

									<label class="col-md-1 control-label" for="item_weight">Item Weight</label>
									<div class="col-md-3" >
										<input type="text" placeholder='Item Weight' class='form-control' id='item_weight' name='item_weight'  value="<?php echo isset($id) ? $editProd->data()->item_weight : escape(Input::get('item_weight')); ?>">
									</div>

							</div>

							<br>
							<legend>CBM (Optional)</legend>
							<div class="row">

								<label class="col-md-1 control-label" for="cbm_l">Length</label>
								<div class="col-md-3">
									<input id="cbm_l" name="cbm_l" placeholder="Length" class="form-control input-md" type="text" value="<?php echo isset($id) ? $editProd->data()->cbm_l : escape(Input::get('cbm_l')); ?>">
									<span class="help-block">Meter</span>
								</div>
								<label class="col-md-1 control-label" for="cbm_w">Width</label>
								<div class="col-md-3">
									<input id="cbm_w" name="cbm_w" placeholder="Width" class="form-control input-md" type="text" value="<?php echo isset($id) ? $editProd->data()->cbm_w : escape(Input::get('cbm_w')); ?>">
									<span class="help-block">Meter</span>
								</div>
								<label class="col-md-1 control-label" for="cbm_h">Height</label>
								<div class="col-md-3">
									<input id="cbm_h" name="cbm_h" placeholder="Height" class="form-control input-md" type="text" value="<?php echo isset($id) ? $editProd->data()->cbm_h : escape(Input::get('cbm_h')); ?>">
									<span class="help-block">Meter</span>
								</div>
							</div>
							<div id="bundle_list_container" style='display: none;'>
								<table class='table' id='tbl_bundle_list'>
									<thead>
									<tr>
										<th>Item</th>
										<th>Qty</th>
										<th></th>
									</tr>
									</thead>
									<tbody></tbody>
								</table>
							</div>
							<div class="form-group">

							</div>
							<div style="clear:both;"></div>

							<legend>Characteristics</legend>
							<div class="form-group">
								<?php

									$char = new Characteristics();
									$chars = $char->getChars($user->data()->company_id);
									if($chars){
										$prev_char = "";

										foreach($chars as $c):

											if($prev_char != $c->tag){
												echo "<div class='col-md-12'><h5>$c->tag</h5></div>";
												echo "<div style='clear: both;'></div> ";
											}

											$prev_char = $c->tag;

											?>
											<div class="col-md-3">
												<label class="checkbox-inline" for="<?php echo $c->id; ?>">
													<input class='charcheckbox' name="char[]" id="<?php echo $c->id; ?>" value="<?php echo $c->id; ?>" type="checkbox"
														<?php
															if(isset($myChar)){
																foreach($myChar as $cc)
																{
																	echo ($cc->characteristics_id == $c->id) ? 'checked' :'';
																}
															}else {
																if($c->name == "None") echo 'checked';
															}
														?>
														>
													<span><?php echo $c->name; ?></span>
												</label>
											</div>
											<?php

										endforeach;
									} else {
										?>
										<div class="alert alert-info">No Characteristics Yet</div>
										<?php
									}
								?>
							</div>

							<br/>
							<hr/>

							<div style="clear:both;"></div>
							<legend>Terminals (optional)</legend>
							<div class="form-group">
								<div id="terminalholder">
								</div>
							</div>

							<br/>
							<hr/>
							<div style="clear:both;"></div>
							<legend>Display Location (optional)</legend>
							<div class="form-group">
								<div id="displaylocationholder">
								</div>
							</div>
							<div style="clear:both;"></div>
							<legend>Supplier List (optional)</legend>
							<div class="form-group">
								<div id="supplierlistholder">
									<?php echo print_r($suppliers); ?>
								</div>
							</div>
							<div class="col-md-0">
								<?php  if( isset($id)){
									?>
									<input type="button" class='btn btn-info' data-purchase_price='<?php echo $editProd->data()->product_cost; ?>' data-item_code='<?php echo $editProd->data()->item_code; ?>' data-id='<?php echo escape(Encryption::encrypt_decrypt('encrypt',$id)); ?>' id='addsupplieritem' value='Add Supplier' class='btn btn-default'/>
									<?php
								}
								?>
							</div>
							<br/>
							<hr/>
							<?php  if(isset($id) && file_exists("../item_images/{$id}.jpg")){

							?>
							<div class='row'>
								<div class="col-md-4">
									<div  class="text-right text-danger">
										<a data-id='<?php echo Encryption::encrypt_decrypt('encrypt',$id); ?>' href="#" id='btnRemoveImage'><span class='fa fa-remove'></span></a></div>
									<img style='width:100%;' src="<?php echo "../item_images/{$id}.jpg"; ?>" alt="">
								</div>
								<div class="col-md-4"></div>
								<div class="col-md-4">
								</div>
							</div>
								<br>
								<?php
							}
							?>
							<div class="form-group">
								<label class="col-md-1 control-label" for="item_img">Image</label>
								<div class="col-md-7">
									<input class="btn bg-info" id="item_img" name="item_img" placeholder="Item Description"  type="file">
									<span class="help-block">Image of the product</span>
								</div>

							</div>
							<!-- Button (Double) -->
							<div class="form-group">

								<div class="col-md-6">
									<input type='submit' class='btn btn-success' name='btnSave' id='btnSave' value='SAVE'/>
									<input type='hidden' name='token' value=<?php echo Token::generate(); ?>>
									<input type='hidden' name='edit' value=<?php echo isset($id) ? escape(Encryption::encrypt_decrypt('encrypt',$id)): 0; ?>>
								</div>
							</div>

						</fieldset>
					</form>
				</div>

			</div>
		</div>
	</div>
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" >
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id='mtitle'>Add Supplier Item</h4>
				</div>
				<div class="modal-body" id='mbody'>
					<!--
					<div class="panel panel-default">
						<div class="panel-body">
							<div id="supholder"></div>
						</div>
					</div>
					<hr />
                    -->
					<form class="form-horizontal" action="" method="POST">
						<fieldset>

							<?php
								$supplier = new Supplier();
								$suppliers = $supplier->get_active('suppliers',array('company_id' ,'=',$user->data()->company_id));

							?>
							<div class="form-group">
								<label class="col-md-4 control-label" for="p_supplier_id">Supplier</label>
								<div class="col-md-8">
									<select name="p_supplier_id" id="p_supplier_id" class='form-control'>
										<option value=""></option>
										<?php foreach($suppliers as $sup):?>
											<option value="<?php echo $sup->id; ?>"><?php echo $sup->name; ?></option>
										<?php endforeach;?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="p_item_code">Item</label>
								<div class="col-md-8">
									<input type="text" class='form-control' id='p_item_code' placeholder='ITEM'  value='' disabled/>
									<input type='hidden' id='p_item_id'>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="p_sitem_code">Item Code</label>
								<div class="col-md-8">
									<input id="p_sitem_code" name="p_sitem_code" placeholder="Supplier's Item Code" class="form-control input-md" type="text" value="">
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="p_description">Description</label>
								<div class="col-md-8">
									<input id="p_description" name="p_description" placeholder="Supplier's Description" class="form-control input-md" type="text" value="">
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="p_purchase_price">Purchase Price</label>
								<div class="col-md-8">
									<input id="p_purchase_price" name="p_purchase_price" placeholder="Purchase Price" class="form-control input-md" type="text" value="">
								</div>
							</div>

							<div class="form-group">
								<label class="col-md-4 control-label" for="p_min_qty">Min Order Quantity</label>
								<div class="col-md-8">
									<input id="p_min_qty" name="p_min_qty" placeholder="Min Quantity" class="form-control input-md" type="text" value="0">
								</div>
							</div>

							<!-- Button (Double) -->
							<div class="form-group">
								<label class="col-md-4 control-label" for="button1id"></label>
								<div class="col-md-8 text-right">
									<input type='button' class='btn btn-success' id='p_savesup' value='SAVE'/>

								</div>
							</div>

						</fieldset>
					</form>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

</div> <!-- end page content wrapper-->

<script>
	$(function(){
		var edit_id = "<?php echo isset($id) ? $id : '' ?>";

		function formatItem(o) {

			if(!o.id)
				return o.text; // optgroup
			else {
				var r = o.text.split(':');

				return "<span> " + r[0] + "</span> <span style='margin-left:10px'>" + r1 + "</span><span style='display:block;margin-top:5px;'  class='text-danger'><small class='testspanclass'>" + r2 + "</small></span>";
			}
		}


		$("#bind_with").select2({
			placeholder: 'Item code',
			allowClear: true,
			minimumInputLength: 2,
			multiple:true,
			formatResult: formatItem,
			formatSelection: formatItem,
			escapeMarkup: function(m) {
				return m;
			},
			ajax: {
				url: '../ajax/ajax_query.php',
				dataType: 'json',
				type: "POST",
				quietMillis: 50,
				data: function(term) {
					return {
						search: term, functionName: 'searchItemJSON'
					};
				},
				results: function(data) {
					return {
						results: $.map(data, function(item) {
							return {
								text: item.barcode + ":" + replaceAll(item.item_code,':','') + ":" + replaceAll(item.description,':','') + ":" + item.price,
								slug: item.description,
								is_bundle: item.is_bundle,
								unit_name: item.unit_name,
								id: item.id
							}
						})
					};
				}

			}
		}).on("select2-close", function(e) {

		}).on("select2-highlight", function(e) {

		});
		try{
			var item_data = JSON.parse('<?php echo json_encode($edit_bind_with);?>');

			if(item_data.length){
				$('#bind_with').select2('data',item_data);
			}

		} catch(e){
			console.log("Binding error");
		}

		$('#bind_with').change(function(){
			console.log($(this).val());
		});
		$('body').on('click','#btnRemoveImage',function(e){
			e.preventDefault();
			var con = $(this);
			var id = con.attr('data-id');
			alertify.confirm("Are you sure you want to delete this image?",function(e){
				if(e){
					$.ajax({
						url:'../ajax/ajax_service.php',
						type:'POST',
						data: {functionName:'deleteImageProduct', id: id},
						success: function(data){
							alertify.alert(data,function(){
								location.href='addproduct.php?edit='+id;
							});
						},
						error:function(){

						}
					});
				}
			});

		});
		$('#addsupplieritem').click(function(){
			var btn= $(this);
			var item_id = btn.attr('data-id');
			var item_code = btn.attr('data-item_code');
			var purchase = btn.attr('data-purchase_price');
			$('#p_item_id').val(item_id);
			$('#p_item_code').val(item_code);
			$('#p_purchase_price').val(purchase);
			/*
			$.ajax({
				url:'../ajax/ajax_query2.php',
				type:'post',
				data: {functionName:'getSupplierItem',item_id:item_id},
				success: function(data){
					$('#supholder').html(data);
					$('#myModal').modal('show');
				},
				error:function(){

				}
			});
			*/
			$('#myModal').modal('show');
		});
		$('#p_supplier_id').select2({
			placeholder:'Select Supplier',
			allowClear: true
		});

		$('#p_savesup').click(function(){
			var p_supplier_id = $('#p_supplier_id').val();
			var p_item_id =$('#p_item_id').val();
			var p_sitem_code =$('#p_sitem_code').val();
			var p_description = $('#p_description').val();
			var p_purchase_price = $('#p_purchase_price').val();
			var p_min_qty = $('#p_min_qty').val();
			$.ajax({
				url:'../ajax/ajax_query2.php',
				type:'post',
				data: {
					functionName:'addSupplierItem',
					p_item_id:p_item_id,
					p_supplier_id:p_supplier_id,
					p_sitem_code:p_sitem_code,
					p_description:p_description,
					p_purchase_price:p_purchase_price,
					p_min_qty:p_min_qty
				},
				success: function(data){
					$('#myModal').modal('hide');
					alertify.alert(data);
					$.ajax({
						url:'../ajax/ajax_query2.php',
						type:'post',
						data: {functionName:'getSupplierItem',item_id:p_item_id},
						success: function(d){
							$('#supplierlistholder').html(d);
							$('#p_supplier_id').select2('val',null);
							$('#p_sitem_code').val('');
							$('#p_description').val('');
							$('#p_min_qty').val('');
						}
					});
				},
				error:function(){

				}
			});
		});
		$('#price').keyup(function(){
			price = $(this).val();
			id = $(this).attr('data-id');
			$.post('../ajax/price_change.php', {id: id, price:price}, function(data) {
				if(data != 'true'){
					$('.hiddenEffectivity').css({"visibility":"visible"});
					$('#effectivity').prop('required', true);
				} else {
					$('.hiddenEffectivity').css({"visibility":"hidden"});
					$('#effectivity').prop('required', false);
				}
			});
		});
		var samp = $('#effectivity').datepicker({
			autoclose:true
		}).on('changeDate', function(ev){
			$('#effectivity').datepicker('hide');
		});

		$('#item_type').change(function(){
			if($(this).val() == 2){
				$('#subscriptionholder').fadeIn();
				$('#consumableholder').fadeIn();
				$('#consumableamountholder').fadeOut();
			} else if ($(this).val() == 3 || $(this).val() == 4 ){
				$('#subscriptionholder').fadeIn();
				$('#consumableholder').fadeOut();
				$('#consumableamountholder').fadeOut();
			} else if ($(this).val() == 5){
				$('#subscriptionholder').fadeIn();
				$('#consumableamountholder').fadeIn();
			} else {
				$('#subscriptionholder').fadeOut();
				$('#consumableholder').fadeOut();
				$('#consumableamountholder').fadeOut();
			}
		});

		$(".charcheckbox").change(function(){

			var checkitem = $(this).next().text();
			console.log(checkitem);
			if(checkitem == 'None'){
				$(".charcheckbox").each(function(){
					if($(this).next().text() != 'None'){
						$(this).attr('checked',false);
					}
				});
			} else {
				$(".charcheckbox").each(function(){
					if($(this).next().text() == 'None'){
						$(this).attr('checked',false);
					}
				});
			}
		});


		$.post('../ajax/getterminalbybranch.php',{branch_id:localStorage['branch_id'],editmoko:'<?php echo (isset($id)) ? $editProd->data()->product_terminals :0; ?>'},function(data){
			$("#terminalholder").html(data);
		});
		$.post('../ajax/getdisplaylocation.php',{company_id:localStorage['company_id'],editmoko:'<?php echo (isset($id)) ? $editProd->data()->display_location :0; ?>'},function(data){
			$("#displaylocationholder").html(data);
		});
		$.post('../ajax/ajax_query2.php',{functionName:'getSupplierItem',item_id:$('#addsupplieritem').attr('data-id')},function(data){
			$("#supplierlistholder").html(data);
		});

		$("#category_id").select2({
			placeholder: 'Category',
			allowClear: true,

			formatResult: formatCateg,
			formatSelection: formatCateg,
			escapeMarkup: function(m) {
				return m;
			}});
		var edit_categ_id = $('#edit_categ_id').val();
		if(edit_categ_id){
			$('#category_id').select2('val',edit_categ_id);
		}
		$('body').on('change','#is_bundle',function(){
			var v = $(this).val();
			if(!edit_id){

				if(v == 1){
					$('#bundleCon').show();
					$('#alltypecon').hide()
					$('#item_type').val(-1);
				} else {
					$('#bundleCon').hide();
					$('#alltypecon').show();
					$('#item_type').val('');
				}

			}


		});
		$('body').on('change','#is_spare',function(){
			var v = $(this).val();
			if(v == 1){
				$('#sparetypeholder').show();
			} else {
				$('#sparetypeholder').hide();
			}
		});
		function formatItem(o) {
			if(!o.id)
				return o.text; // optgroup
			else {
				var r = o.text.split(':');
				return "<span> " + r[0] + "</span> <span style='margin-left:10px'>" + r[1] + "</span><span style='display:block;margin-top:5px;'  class='text-danger'><small class='testspanclass'>" + r[2] + "</small></span>";
			}
		}
		$("#bundle_list").select2({
			placeholder: 'Item code',
			allowClear: true,
			minimumInputLength: 2,
			formatResult: formatItem,
			formatSelection: formatItem,
			escapeMarkup: function(m) {
				return m;
			},
			ajax: {
				url: '../ajax/ajax_query.php',
				dataType: 'json',
				type: "POST",
				quietMillis: 50,
				data: function(term) {
					return {
						search: term, functionName: 'searchItemJSON'
					};
				},
				results: function(data) {
					return {
						results: $.map(data, function(item) {
							return {
								text: item.barcode + ":" + item.item_code + ":" + item.description + ":" + item.price,
								slug: item.description,
								id: item.id
							}
						})
					};
				}

			}
		}).on("select2-close", function(e) {


		}).on("select2-highlight", function(e) {

		});
		$('#bundle_list').change(function(){
			var it = $(this);
			var data = it.select2("data");
			var v = it.val();
			console.log(data.text);
			if(data.text){
				var splitted = (data.text).split(':');
				$('#tbl_bundle_list tbody').append("<tr id='bundle"+v+"'><td>"+splitted[2]+" <input type='hidden' value='"+v+"' name='bundle_item[]'></td><td><input type='text' name='bundle_qty[]' class='form-control'></td><td><button  class='btn btn-danger btn-sm btnRemove'><span class='glyphicon glyphicon-remove'></span></button></td></tr>");
			}
			toggleBundleCon();
			it.select2('val',null);
		});
		$('body').on('click','.btnRemove',function(e){
			e.preventDefault();
			var row = $(this).parents('tr');
			row.remove();
			toggleBundleCon();
		});
		function toggleBundleCon(){
			if($('#tbl_bundle_list > tbody > tr').length > 0){
				$('#bundle_list_container').show();
			} else {
				$('#bundle_list_container').hide();
			}
		}
		$('body').on('click','#btnSave',function(e){
			e.preventDefault();
			var is_bundle = $('#is_bundle').val();
			<?php
				if(!isset($id)){
			?>
			if(is_bundle == 1){
				var validqty = true;
				if($('#tbl_bundle_list > tbody > tr').length == 0){
					alertify.alert("Invalid bundle items");
					return;
				} else {
					$('#tbl_bundle_list > tbody > tr').each(function(){
						var row = $(this)
						var qty = row.children().eq(1).find("input").val();
						if(!qty || isNaN(qty) || parseInt(qty) == 0 ){
							validqty = false;
						}
					});
				}
				if(!validqty){
					alertify.alert("Invalid bundle qty");
					return;
				}
			}

			<?php
			}

		?>
			$(this).parents('form').submit();

		});
		function formatCateg(o) {

			if(!o.id)
				return o.text; // optgroup
			else {
				var r = o.text.split('-->');
				var count = r.length;
				var ret = "";

				for(var i in r){
					var con = "";
					var margin = "";
					var indent = "";
					if(count > 1 && i == 0){
						var v = (count-1) * 20;
						indent = "<span style='margin-left:"+v+"px;'></span>";
					}
					var arrow = "";
					if(i == 0){
						margin = "font-weight:bold;";
					} else if (parseInt(i) + 1 == count){
						margin = "margin-left:15px;";
					} else {
						margin = "margin-left:5px;";
						arrow = "";
					}

					if(parseInt(i) + 1 == count){
						con = "<span style='"+margin+"'>"+r[i]+"</span>";
					} else {
						con = "<small class='text-danger' style='"+margin+"font-size:10px;'>"+r[i]+"</small>";
					}
					ret += indent + arrow + con;
				}
				return ret;
			}
		}
	});


</script>