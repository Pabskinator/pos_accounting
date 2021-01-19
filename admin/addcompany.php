<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('settings')) {
		// redirect to denied page
		Redirect::to(1);
	}


?>

	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				    ADD COMPANY
				</h1>
			</div>
			<?php
				if(Session::exists('companyflash')) {
					echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('companyflash') . "</div><br/>";
				}
			?>
			<div class="row">
				<div class="col-md-12">

					<?php


						// if submitted
						if (Input::exists()){
							// check token if match to our token
							if(Token::check(Input::get('token'))){
								$validation_list = array(
									'username' => array(
										'required'=> true,
										'min' => 6,
										'max' => 50
									),
									'password' => array(
										'required'=> true,
										'min' => 6,
										'max' => 50
									),
									'name' => array(
										'required'=> true,
										'max' => 50
									),
									'description' => array(
										'required'=> true,
										'max' => 100
									),
									'address' => array(
										'required'=> true
									),
									'bc_prefix' => array(
										'required'=> true
									),
									'admin_fn' => array(
										'required'=> true
									),
									'admin_mn' => array(
										'required'=> true
									),
									'admin_ln' => array(
										'required'=> true
									),
									'plan_id' => array(
										'required'=> true
									),

								);

								$validate = new Validate();
								$validate->check($_POST, $validation_list);
								if($validate->passed()){
										$company = new Company();
										$position = new Position();
										$usercreate = new User();
										$unit = new Unit();
										$char = new Characteristics();
										$category =  new Category();
										$rack = new Rack();
										$branch = new Branch();
										$temp_gen = new Barcode();
										$styles = new Style();
										$terminal = new Terminal();
										$product = new Product();
										$pricecls = new Price();
										$inventory = new Inventory();
										$salesType = new Sales_type();
										$member = new Member();
										$station = new Station();
										$invoice_default = '{"date":{"visible":true,"bold":false,"top":"148","left":"673.5","height":"","width":"","fontSize":"14"},"membername":{"visible":true,"bold":false,"top":"75","left":"117","height":"","width":"","fontSize":"14"},"memberaddress":{"visible":true,"bold":false,"top":"138","left":"106","width":"300","fontSize":"14"},"stationname":{"visible":true,"bold":false,"top":"68","left":"443","height":"","width":"","fontSize":"14"},"stationaddress":{"visible":true,"bold":false,"top":"93","left":"442","height":"","width":"300","fontSize":"14"},"itemtable":{"visible":true,"bold":false,"top":"271","left":"39","height":"","width":"","fontSize":"14"},"payments":{"visible":true,"bold":false,"top":"646","left":"627","height":"","width":"","fontSize":"14"},"payments2":{"visible":true,"bold":false,"top":"670","left":"626.5","height":"","width":"","fontSize":"14"},"payments3":{"visible":true,"bold":false,"top":"730","left":"624","height":"","width":"","fontSize":"14"},"cashier":{"visible":true,"bold":false,"top":"829","left":"113","height":"","width":"","fontSize":"14"},"remarks":{"visible":true,"bold":false,"top":"861","left":"111.5","height":"","width":"","fontSize":"14"},"reserved":{"visible":true,"bold":false,"top":"892","left":"109","height":"","width":"","fontSize":"14"},"tdbarcode":{"width":"100","left":"5"},"tdqty":{"width":"90","left":"5"},"tddescription":{"width":"330","left":"5"},"tdtotal":{"width":"150","left":"5"},"tdprice":{"width":"100","left":"5"}}';
										$dr_default = '{"date":{"visible":true,"bold":false,"top":"6","left":"687","height":"","width":"","fontSize":"14"},"membername":{"visible":true,"bold":false,"top":"114","left":"77.5","height":"","width":"","fontSize":"14"},"memberaddress":{"visible":true,"bold":false,"top":"77","left":"78","width":"300","fontSize":"14"},"stationname":{"visible":true,"bold":false,"top":"75","left":"498","height":"","width":"","fontSize":"14"},"stationaddress":{"visible":true,"bold":false,"top":"106","left":"492","height":"","width":"300","fontSize":"14"},"itemtable":{"visible":true,"bold":false,"top":"339","left":"42","height":"","width":"","fontSize":"14"},"payments":{"visible":true,"bold":false,"top":"757","left":"572","height":"","width":"","fontSize":"14"},"payments2":{"visible":true,"bold":false,"top":"770","left":"573.5","height":"","width":"","fontSize":"14"},"payments3":{"visible":true,"bold":false,"top":"785","left":"573","height":"","width":"","fontSize":"14"},"cashier":{"visible":true,"bold":false,"top":"842","left":"111","height":"","width":"","fontSize":"14"},"remarks":{"visible":true,"bold":false,"top":"862","left":"111","height":"","width":"","fontSize":"14"},"reserved":{"visible":true,"bold":false,"top":"882","left":"108","height":"","width":"","fontSize":"14"},"tdbarcode":{"width":"100","left":"5"},"tdqty":{"width":"90","left":"5"},"tddescription":{"width":"330","left":"5"},"tdtotal":{"width":"150","left":"5"},"tdprice":{"width":"100","left":"5"}}';
										$ir_default = '{"date":{"visible":true,"bold":false,"top":"38","left":"700.5","height":"","width":"","fontSize":"14"},"membername":{"visible":true,"bold":false,"top":"36","left":"69","height":"","width":"","fontSize":"14"},"memberaddress":{"visible":true,"bold":false,"top":"70","left":"67","width":"300","fontSize":"14"},"stationname":{"visible":true,"bold":false,"top":"36","left":"453","height":"","width":"","fontSize":"14"},"stationaddress":{"visible":true,"bold":false,"top":"66","left":"453","height":"","width":"300","fontSize":"14"},"itemtable":{"visible":true,"bold":false,"top":"339","left":"42","height":"","width":"","fontSize":"14"},"payments":{"visible":true,"bold":false,"top":"757","left":"572","height":"","width":"","fontSize":"14"},"payments2":{"visible":true,"bold":false,"top":"770","left":"573.5","height":"","width":"","fontSize":"14"},"payments3":{"visible":true,"bold":false,"top":"785","left":"573","height":"","width":"","fontSize":"14"},"cashier":{"visible":true,"bold":false,"top":"842","left":"111","height":"","width":"","fontSize":"14"},"remarks":{"visible":true,"bold":false,"top":"862","left":"111","height":"","width":"","fontSize":"14"},"reserved":{"visible":true,"bold":false,"top":"882","left":"108","height":"","width":"","fontSize":"14"},"tdbarcode":{"width":"100","left":"5"},"tdqty":{"width":"90","left":"5"},"tddescription":{"width":"330","left":"5"},"tdtotal":{"width":"150","left":"5"},"tdprice":{"width":"100","left":"5"}}';
										$barcode_default = '{"title":{"top":"3.0555579382324254","left":"84.39236900390625","value":"SAMPLE","fontSize":"12","letterSpacing":"6"},"bar":{"top":"24","left":"7","height":"25"},"barLabel":{"top":"44.5","left":"17","fontSize":"13","backgroundColor":"white","display":true,"fontWeight":true,"letterSpacing":"5"},"extraDesc":{"top":"55.5","left":"17.5","fontSize":"11","display":false,"fontWeight":false,"value":""},"itemcode":{"top":"60.5","left":"9","fontSize":"11","display":true,"fontWeight":true},"category":{"top":"","left":"9","fontSize":"11","display":true,"fontWeight":true},"price":{"top":"58.541668625488285","left":"97.89931175292969","fontSize":"11","display":true,"fontWeight":true},"supcateg":{"top":"","left":"","fontSize":"","display":false,"fontWeight":false},"storecode":{"top":"53.5","left":"95","fontSize":"11","display":false,"fontWeight":false,"value":"","rotate":""},"date":{"top":"63.5","left":"80","fontSize":"11","display":false,"fontWeight":false},"container":{"top":"-15","left":"-10","width":"140","height":"90"},"settings":{"howmany":"2","type":"code128"}}';
										if(Input::get('plan_id') == 2){
											$json = '{"dashboard":1,"mainpos":1,"mainpos_sr":1,"mainpos_ar":1,"mainpos_mr":1,"branch":1,"branch_m":1,"terminal":1,"terminal_m":1,"deposit_add_m":1,"terminal_mon":1,"user":1,"user_m":1,"position":1,"position_m":1,"inventory":1,"inventory_add":1,"inventory_transfer":1,"inventory_receive":1,"inventory_adj":1,"order_inv_m":1,"pickup_inv":1,"inv_mon":1,"rack":1,"rack_m":1,"rack_display":1,"rack_other":1,"witness":1,"witness_m":1,"display_location_m":1,"orderpoint":1,"orderpoint_m":1,"orderpoint_p":1,"supplier":1,"supplier_m":1,"supplier_o":1,"supplier_ol":1,"supplier_si":1,"supplier_sim":1,"item":1,"item_m":1,"category":1,"category_m":1,"characteristics":1,"characteristics_m":1,"unit":1,"unit_m":1,"barcode_m":1,"barcode_p":1,"alert":1,"alert_m":1,"notification":1,"notification_rm":1,"queue":1,"queue_m":1,"member":1,"member_m":1,"m_char":1,"m_char_m":1,"station":1,"station_m":1,"subscription":1,"package":1,"brand":1,"sales":1,"order":1,"createorder":1,"dr_layout":1,"pr_layout":1,"invoice_layout":1,"sales_type":1,"sales_type_m":1,"cheque_monitoring":1,"doc_util":1,"lock_doc_util":1,"caravan_request":1,"caravan_manage":1,"mc_pending":1,"mc_approve":1,"mc_processed":1,"mc_liquidate_sales":1,"mc_liquidate_item":1,"mc_verify":1,"settings":1,"sales_crud":1,"reports":1,"station_settings":1,"supplier_settings":1,"themes":1,"recycle":1,"consumable_admin":1,"consumablefree_admin":1}';
										} else {
											// "pickup_inv":1,"orderpoint":1,"orderpoint_m":1,"orderpoint_p":1,"supplier_m":1,"supplier_o":1,"supplier_ol":1,"supplier_si":1,"supplier_sim":1,"alert":1,"alert_m":1,"notification":1,"notification_rm":1,"caravan_request":1,"caravan_manage":1,"mc_pending":1,"mc_approve":1,"mc_processed":1,"mc_liquidate_sales":1,"mc_liquidate_item":1,"mc_verify":1,"supplier":1
											$json = '{"dashboard":1,"mainpos":1,"mainpos_sr":1,"mainpos_ar":1,"mainpos_mr":1,"branch":1,"branch_m":1,"terminal":1,"terminal_m":1,"deposit_add_m":1,"terminal_mon":1,"user":1,"user_m":1,"position":1,"position_m":1,"inventory":1,"inventory_add":1,"inventory_transfer":1,"inventory_receive":1,"inventory_adj":1,"order_inv_m":1,"inv_mon":1,"rack":1,"rack_m":1,"rack_display":1,"rack_other":1,"witness":1,"witness_m":1,"display_location_m":1,"item":1,"item_m":1,"category":1,"category_m":1,"characteristics":1,"characteristics_m":1,"unit":1,"unit_m":1,"barcode_m":1,"barcode_p":1,"queue":1,"queue_m":1,"member":1,"member_m":1,"m_char":1,"m_char_m":1,"station":1,"station_m":1,"subscription":1,"package":1,"brand":1,"sales":1,"order":1,"createorder":1,"dr_layout":1,"pr_layout":1,"invoice_layout":1,"sales_type":1,"sales_type_m":1,"cheque_monitoring":1,"doc_util":1,"lock_doc_util":1,"settings":1,"sales_crud":1,"reports":1,"station_settings":1,"supplier_settings":1,"themes":1,"recycle":1,"consumable_admin":1,"consumablefree_admin":1}';

										}


										$darkBlueTheme = '{"sidebar_background_color":"#22313f","sidebar_text_color":"#ffffff","sidebar_link_color":"#c0c0c0","header_background_color":"#2c3e50","header_link_color":"#ffffff","header_hover_color":"#22303c","panel_head_color":"#2c3e50","panel_border_color":"#2c3e50","btnp_background_color":"#2c3e50","btnp_hover_color":"#22303c"}';
										$redTheme = '{"sidebar_background_color":"#96281b","sidebar_text_color":"#ffffff","sidebar_link_color":"#ffffff","header_background_color":"#c0392b","header_link_color":"#ffffff","header_hover_color":"#a43326","panel_head_color":"#c0392b","panel_border_color":"#c0392b","btnp_background_color":"#c0392b","btnp_hover_color":"#a43326"}';
										$orangeTheme = '{"sidebar_background_color":"#f9690e","sidebar_text_color":"#ffffff","sidebar_link_color":"#ffffff","header_background_color":"#d35400","header_link_color":"#ffffff","header_hover_color":"#bb4a00","panel_head_color":"#d35400","panel_border_color":"#d35400","btnp_background_color":"#d35400","btnp_hover_color":"#bb4a00"}';
										$violetTheme = '{"sidebar_background_color":"#5b3256","sidebar_text_color":"#ffffff","sidebar_link_color":"#ffffff","header_background_color":"#763568","header_link_color":"#ffffff","header_hover_color":"#64325a","panel_head_color":"#6c3568","panel_border_color":"#6c3568","btnp_background_color":"#6c3568","btnp_hover_color":"#64325a"}';
										$pinkTheme = '{"sidebar_background_color":"#c93756","sidebar_text_color":"#ffffff","sidebar_link_color":"#fcfcfc","header_background_color":"#f47983","header_link_color":"#ffffff","header_hover_color":"#f36d77","panel_head_color":"#f47983","panel_border_color":"#f47983","btnp_background_color":"#f47983","btnp_hover_color":"#f36d77"}';
										$cyanTheme = '{"sidebar_background_color":"#006442","sidebar_text_color":"#ffffff","sidebar_link_color":"#ffffff","header_background_color":"#049372","header_link_color":"#ffffff","header_hover_color":"#037e7b","panel_head_color":"#049372","panel_border_color":"#049372","btnp_background_color":"#049372","btnp_hover_color":"#037e7b"}';
										$themes = ['Dark Blue' => $darkBlueTheme, 'Red' => $redTheme, 'Orange' => $orangeTheme, 'Violet' => $violetTheme,'Pink' =>$pinkTheme , 'Cyan' => $cyanTheme];

									try{
										$company->create(array(
											'name' => Input::get('name'),
											'description' => Input::get('description'),
											'address' => Input::get('address'),
											'bc_prefix' => Input::get('bc_prefix'),
											'created' => strtotime(date('Y/m/d H:i:s')),
											'modified' => strtotime(date('Y/m/d H:i:s')),
											'plan_id' => Input::get('plan_id')
										));
										$company_id = $company->getInsertedId();
										foreach($themes as $key => $val){
											$issetTheme = 0;
											if($key == 'Dark Blue'){
												$issetTheme = 1;
											}
											$styles->create(array(
												'created' => strtotime(date('Y/m/d H:i:s')),
												'modified' => strtotime(date('Y/m/d H:i:s')),
												'company_id' => $company_id,
												'styles' => $val,
												'name' => $key,
												'is_set' => $issetTheme,
												'is_active' => 1
											));
										}
										$branch->create(array(
											'name' => 'Main Branch',
											'description' => 'Main Branch',
											'address' => 'Main Branch Address',
											'created' => strtotime(date('Y/m/d H:i:s')),
											'modified' => strtotime(date('Y/m/d H:i:s')),
											'company_id' => $company_id
										));
										$lastIdBranch = $branch->getInsertedId();


										// terminal
										$terminal->create(array(
											'name' => 'Main Terminal',
											'branch_id' => $lastIdBranch,
											'created' => strtotime(date('Y/m/d H:i:s')),
											'modified' => strtotime(date('Y/m/d H:i:s')),
											'invoice' => 0,
											'end_invoice' => 100,
											'dr' => 0,
											'end_dr' => 100,
											'ir' => 0,
											'end_ir' => 100,
											'invoice_limit' => 100,
											'dr_limit' => 100,
											'ir_limit' => 100
										));

										//barcode
										$temp_gen->create(array(
											'family' =>'Barcode',
											'styling' => $barcode_default,
											'company_id' => $company_id,
											'is_active' => 1
										));

										//invoice
										$temp_gen->create(array(
											'family' =>'INVOICE',
											'styling' => $invoice_default,
											'company_id' => $company_id,
											'is_active' => 1
										));

										//dr
										$temp_gen->create(array(
											'family' =>'DR',
											'styling' => $dr_default,
											'company_id' => $company_id,
											'is_active' => 1
										));

										//ir
										$temp_gen->create(array(
											'family' =>'IR',
											'styling' => $ir_default,
											'company_id' => $company_id,
											'is_active' => 1
										));


										$position->create(array(
											'position' => "Admin",
											'permisions' => $json,
											'company_id' => $company_id,
											'is_active' => 1,
											'created' => strtotime(date('Y/m/d H:i:s')),
											'modified' => strtotime(date('Y/m/d H:i:s'))
										));
										$position_id = $position->getInsertedId();
										$usercreate->create(array(
											'username' => Input::get('username'),
											'password' => Hash::make(Input::get('password')),
											'lastname' => Input::get('admin_ln'),
											'firstname' =>Input::get('admin_fn'),
											'middlename' => Input::get('admin_mn'),
											"position_id"=>$position_id,
											"company_id" =>$company_id,
											"branch_id" =>$lastIdBranch,
											"is_active" =>1,
											'created' => strtotime(date('Y/m/d H:i:s')),
											'modified' => strtotime(date('Y/m/d H:i:s'))
										));
										$unit->create(array(
											'name' => "None",
											"company_id" =>$company_id,
											"is_active" =>1,
											'created' => strtotime(date('Y/m/d H:i:s')),
											'modified' => strtotime(date('Y/m/d H:i:s'))
										));
										$lastidUnit = $unit->getInsertedId();
										$char->create(array(
											'name' => "None",
											"company_id" =>$company_id,
											"is_active" =>1,
											'created' => strtotime(date('Y/m/d H:i:s')),
											'modified' => strtotime(date('Y/m/d H:i:s'))
										));
										$lastidChar = $char->getInsertedId();
										$category->create(array(
											'name' => "None",
											"company_id" =>$company_id,
											"is_active" =>1,
											'parent' => 0,
											'created' => strtotime(date('Y/m/d H:i:s')),
											'modified' => strtotime(date('Y/m/d H:i:s'))
										));
										$lastidCateg = $category->getInsertedId();
										$rack->create(array(
											'rack' => "Display",
											"company_id" =>$company_id,
											"is_active" =>1,
											'created' => strtotime(date('Y/m/d H:i:s')),
											'modified' => strtotime(date('Y/m/d H:i:s'))
										));
										$lastidRack = $rack->getInsertedId();
										for($i = 1; $i<=3; $i++){
											$product->create(array(
												'barcode' => Input::get('bc_prefix') . "00000" . $i,
												'item_code' => 'Product ' . $i ,
												'description' => 'Text Product ' . $i ,
												'category_id' => $lastidCateg,
												'company_id' => $company_id,
												'is_active' => 1,
												'created' => strtotime(date('Y/m/d H:i:s')),
												'modified' => strtotime(date('Y/m/d H:i:s')),
												'item_type' => -1,
												'for_freebies' => 0,
												'unit_id' => $lastidUnit
											));
											$lastidProd = $product->getInsertedId();
											$ef = strtotime(date('m/d/Y'));
											$priceItem = $i * 100;
											$pricecls->create(array(
												'price' => $priceItem,
												'item_id' => $lastidProd,
												'effectivity' => $ef,
												'unit_id' => $lastidUnit,
												'created' => strtotime(date('Y/m/d H:i:s'))
											));
											$inventory->addInventory($lastidProd,$lastIdBranch,100,true,$lastidRack);
										}

										$salesType->create(array(
											'name' => 'Sales POS',
											'description' => 'Every day sales',
											'created' => strtotime(date('Y/m/d H:i:s')),
											'modified' => strtotime(date('Y/m/d H:i:s')),
											'company_id' => $company_id,
											'is_active' => 1,
											'is_default' => 1
										));

										$member->create(array(
											'lastname' => 'Dela Cruz',
											'firstname' => 'Juan',
											'middlename' => 'Santos',
											'personal_address' => '144 Test St. Pasig City',
											'email' => 'juandelacruz@email.com',
											'sg_year' => 0,
											'contact_number' => '2394382',
											'birthdate' => '631123200',
											'region' => 0,
											'company_id' => $company_id,
											'is_active' => 1,
											'created' => strtotime(date('Y/m/d H:i:s')),
											'modified' => strtotime(date('Y/m/d H:i:s'))
										));
										$lastidMember = $member->getInsertedId();
										$station->create(array(
											'name' => 'Station 1',
											'member_id' => $lastidMember,
											'created' => strtotime(date('Y/m/d H:i:s')),
											'modified' => strtotime(date('Y/m/d H:i:s')),
											'is_active' => 1,
											'company_id' => $company_id
										));

										Session::flash('companyflash','You have successfully added a Company');
										Redirect::to('addcompany.php');
									} catch(Exception $e) {
										die($e->getMessage());
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
					?>

					<form class="form-horizontal" action="" method="POST">
						<fieldset>


							<legend>New Company</legend>

							<h4>Company Information</h4>

							<div class="form-group">
								<label class="col-md-4 control-label" for="name">Company Name</label>
								<div class="col-md-4">
									<input id="name" name="name" placeholder="Company name" class="form-control input-md" type="text" value="<?php echo  escape(Input::get('name')); ?>">
									<span class="help-block">Name of your company</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="description">Description</label>
								<div class="col-md-4">
									<input id="description" name="description" placeholder="Description" class="form-control input-md" type="text" value="">
									<span class="help-block">Description of your company</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="address">Address</label>
								<div class="col-md-4">
									<input id="address" name="address" placeholder="Address" class="form-control input-md" type="text" value="">
									<span class="help-block">Address of your company</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="bc_prefix">Barcode prefix</label>
								<div class="col-md-4">
									<input id="bc_prefix" name="bc_prefix" placeholder="Barcode Prefix" class="form-control input-md" type="text" value="">
									<span class="help-block">Barcode prefix</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="plan_id">Features</label>
								<div class="col-md-4">
									<select name="plan_id" id="plan_id" class='form-control'>
										<option value="1">Basic</option>
										<option value="2">Pro</option>
									</select>
								</div>
							</div>
							<hr>
							<h4>Admin </h4>
							<div class="form-group">
								<label class="col-md-4 control-label" for="username">Username</label>
								<div class="col-md-4">
									<input id="username" name="username" placeholder="User Name" class="form-control input-md" type="text" value="<?php echo escape(Input::get('username')); ?>">
									<span class="help-block">Username of the admin</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="firstname">Password</label>
								<div class="col-md-4">
									<input id="password" name="password" placeholder="Password" class="form-control input-md" type="password" value="">
									<span class="help-block">Password of the admin</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="admin_ln">Last Name</label>
								<div class="col-md-4">
									<input id="admin_ln" name="admin_ln" placeholder="Last Name" class="form-control input-md" type="text" value="">
									<span class="help-block">Admin Last Name</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="admin_fn">First Name</label>
								<div class="col-md-4">
									<input id="admin_fn" name="admin_fn" placeholder="First Name" class="form-control input-md" type="text" value="">
									<span class="help-block">Admin First Name</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="admin_mn">Middle Name</label>
								<div class="col-md-4">
									<input id="admin_mn" name="admin_mn" placeholder="Middle Name" class="form-control input-md" type="text" value="">
									<span class="help-block">Admin Middle Name</span>
								</div>
							</div>


							<!-- Button (Double) -->
							<div class="form-group">
								<label class="col-md-4 control-label" for="button1id"></label>
								<div class="col-md-8">
									<input type='submit' class='btn btn-success' name='btnSave' value='SAVE'/>
									<input type='hidden' name='token' value=<?php echo Token::generate(); ?>>
									<input type='hidden' name='edit' value=<?php echo isset($id) ? escape(Encryption::encrypt_decrypt('encrypt',$id)): 0; ?>>

								</div>
							</div>

						</fieldset>
					</form>
				</div>

			</div>
		</div>
	</div> <!-- end page content wrapper-->


<?php require_once '../includes/admin/page_tail2.php'; ?>