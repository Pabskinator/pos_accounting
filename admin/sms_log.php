<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('sms_log')) {
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
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> SMS LOG </h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}
		?>
		<div class="form-group">
			<button id='btnCodes' class='btn btn-default'>SHOW ITEM CODES</button>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">
						<div class='row'>
							<div class='col-md-6'>Log</div>
							<div class='col-md-6 text-right'>
								<button class='btn btn-default' id='btnAdd'><i class='fa fa-plus'></i></button>
							</div>
						</div>
					</div>
					<div class="panel-body">
						<div id='test2'></div>
						<div class="row">

							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon"><span class='glyphicon glyphicon-search'></span></span>
										<input type="text" id="search" class='form-control' placeholder='Search..'/>
									</div>
								</div>
							</div>
							<div class="col-md-3" style='display:none;'>
								<div class="form-group">
									<select id="branch_id" name="branch_id" class="form-control">
										<option value=''></option>
										<?php
											$branch = new Branch();
											$branches =  $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));
											foreach($branches as $b){
												$a = isset($id) ? $terminal->data()->branch_id : escape(Input::get('branch_id'));

												if($a==$b->id){
													$selected='selected';
												} else {
													$selected='';
												}
												?>
												<option value='<?php echo $b->id ?>' <?php echo $selected ?>><?php echo $b->name;?> </option>
												<?php
											}
										?>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select id="type" name="type" class="form-control">
										<option value='0'>Pending</option>
										<option value='1'>Processed</option>
									</select>
								</div>
							</div>
							<div class="col-md-3">

							</div>
						</div>
						<div class="row">

						</div>

						<input type="hidden" id="hiddenpage" />
						<div id="holder"></div>

					</div>


				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id='mtitle'></h4>
					</div>
					<div class="modal-body" id='mbody'>
					</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<div class="modal fade" id="myModalNew" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title">Add Order Item</h4>
					</div>
					<div class="modal-body">
						<strong>Item</strong> <input type="text" class='form-control selectitem' id='new_item_id'>
						<strong>Qty</strong> <input type="text" class='form-control' id='new_qty'>
						<strong>Date Received</strong> <input type="text" class='form-control' id='new_received_data'>
						<strong>Branch</strong>
						<select id="new_branch_id" name="new_branch_id" class="form-control">
							<option value=''></option>
							<?php
								$branch = new Branch();
								$branches = $branch->branchJSON($user->data()->company_id);
								foreach($branches as $b){

									?>
									<option value='<?php echo $b->id ?>'><?php echo $b->name;?> </option>
									<?php
								}
							?>
						</select>
						<br>
						<button class='btn btn-default' id='btnNewItem'>Submit</button>
					</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<div class="modal fade" id="myModalUpdateDate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" ></h4>
					</div>
					<div class="modal-body">
						<input type="hidden" id='sms_id' >
						<strong>Date:</strong>
						<input type="text" id='sms_date_report' class='form-control'>
						<hr>
						<button class='btn btn-default' id='btnUpdateSms'>Save</button>
					</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<div class="modal fade" id="myModalCodes" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id=''>Manual Item Codes</h4>
					<p>Use for manual reporting</p>
				</div>
				<div class="modal-body" id=''>
					<table id='tblBordered' class='table table-bordered'>
						<tr>
							<th>Item</th><th>Code</th>
						</tr>
						<tr><td style='border-top:1px solid #ccc;'>C - Adobo Skin</td><td style='border-top:1px solid #ccc;'><strong>CAS</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>C - Adobo Skinless</td><td style='border-top:1px solid #ccc;'><strong>CAS2</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>C - Spicy Adobo Skin</td><td style='border-top:1px solid #ccc;'><strong>CSAS</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>C - Spicy Adobo Skinless</td><td style='border-top:1px solid #ccc;'><strong>CSAS2</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>C - Roasted Skin</td><td style='border-top:1px solid #ccc;'><strong>CRS</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>C - Roasted Skinless</td><td style='border-top:1px solid #ccc;'><strong>CRS2</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>C - Cracker Nuts</td><td style='border-top:1px solid #ccc;'><strong>CCN</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>C - Spicy Cracker Nuts</td><td style='border-top:1px solid #ccc;'><strong>CSCN</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>C - Super Garlic</td><td style='border-top:1px solid #ccc;'><strong>CSG</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>C - Mixed Nuts</td><td style='border-top:1px solid #ccc;'><strong>CMN</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>C - Pop Beans</td><td style='border-top:1px solid #ccc;'><strong>CPB</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>C - Cashew Nuts</td><td style='border-top:1px solid #ccc;'><strong>CCN2</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>C - Chichacorn</td><td style='border-top:1px solid #ccc;'><strong>CC</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>C - Adobong Bawang</td><td style='border-top:1px solid #ccc;'><strong>CAB</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>C - Sung-sung</td><td style='border-top:1px solid #ccc;'><strong>CSS</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>C - Curry Flavored Nuts</td><td style='border-top:1px solid #ccc;'><strong>CCFN</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>C - Coated Green Peas</td><td style='border-top:1px solid #ccc;'><strong>CCGP</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Peanut Brittle</td><td style='border-top:1px solid #ccc;'><strong>PB</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>C - Candy Coated Peanut</td><td style='border-top:1px solid #ccc;'><strong>CCCP</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>C - Roasted Almonds</td><td style='border-top:1px solid #ccc;'><strong>CRA</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Peanut Bar</td><td style='border-top:1px solid #ccc;'><strong>PB2</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>C - Honey Coated Peanut (S)</td><td style='border-top:1px solid #ccc;'><strong>CHCPS</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>C - Honey Coated Peanut (M)</td><td style='border-top:1px solid #ccc;'><strong>CHCPM</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Chicken Skin</td><td style='border-top:1px solid #ccc;'><strong>CS</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Chicken Isaw</td><td style='border-top:1px solid #ccc;'><strong>CI</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Chicken Butchi</td><td style='border-top:1px solid #ccc;'><strong>CB</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Chicharon Bulaklak</td><td style='border-top:1px solid #ccc;'><strong>CB2</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Chicharon Bituka</td><td style='border-top:1px solid #ccc;'><strong>CB3</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Cebu Laman (makapal)</td><td style='border-top:1px solid #ccc;'><strong>CLM</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Cebu Laman (manipis)</td><td style='border-top:1px solid #ccc;'><strong>CLM2</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Fish Skin Chicharon</td><td style='border-top:1px solid #ccc;'><strong>FSC</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Spicy Fish Skin Chicharon</td><td style='border-top:1px solid #ccc;'><strong>SFSC</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Mushroom Chicharon</td><td style='border-top:1px solid #ccc;'><strong>MC</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Plain Fish Skin with Vinegar</td><td style='border-top:1px solid #ccc;'><strong>PFSV</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Spicy Fish Skin with Vinegar</td><td style='border-top:1px solid #ccc;'><strong>SFSV</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Cocktail</td><td style='border-top:1px solid #ccc;'><strong>CT</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>FRT</td><td style='border-top:1px solid #ccc;'><strong>FRT</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Cornicks</td><td style='border-top:1px solid #ccc;'><strong>CN</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Green Peas</td><td style='border-top:1px solid #ccc;'><strong>GP</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Skin</td><td style='border-top:1px solid #ccc;'><strong>SKIN</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Garlic</td><td style='border-top:1px solid #ccc;'><strong>GL</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Skinless</td><td style='border-top:1px solid #ccc;'><strong>SL</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Pellets</td><td style='border-top:1px solid #ccc;'><strong>PL</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Salt</td><td style='border-top:1px solid #ccc;'><strong>SALT</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Sprinkle</td><td style='border-top:1px solid #ccc;'><strong>SP</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Spicies</td><td style='border-top:1px solid #ccc;'><strong>SPC</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Curry Powder</td><td style='border-top:1px solid #ccc;'><strong>CP</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Lid</td><td style='border-top:1px solid #ccc;'><strong>LID</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Zip Lock</td><td style='border-top:1px solid #ccc;'><strong>ZL</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Zip Lock (small)</td><td style='border-top:1px solid #ccc;'><strong>ZLS</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Zip Lock (medium)</td><td style='border-top:1px solid #ccc;'><strong>ZLM</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>R - Pop Beans</td><td style='border-top:1px solid #ccc;'><strong>RPB</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>R - Cashew Nuts</td><td style='border-top:1px solid #ccc;'><strong>RCN</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>R - Chichacorn</td><td style='border-top:1px solid #ccc;'><strong>RC</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>R - Sung</td><td style='border-top:1px solid #ccc;'><strong>RS</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>R - Coated Green Peas</td><td style='border-top:1px solid #ccc;'><strong>RCGP</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>R - Cracker Nuts</td><td style='border-top:1px solid #ccc;'><strong>RCN2</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>R - Spicy Cracker Nuts</td><td style='border-top:1px solid #ccc;'><strong>RSCN</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>R - Candy Coated</td><td style='border-top:1px solid #ccc;'><strong>RCC</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Adobo Skin</td><td style='border-top:1px solid #ccc;'><strong>AS</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Adobo Skinless</td><td style='border-top:1px solid #ccc;'><strong>AS2</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Super Garlic</td><td style='border-top:1px solid #ccc;'><strong>SG</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Spicy Adobo Skin</td><td style='border-top:1px solid #ccc;'><strong>SAS</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Spicy Adobo Skinless</td><td style='border-top:1px solid #ccc;'><strong>SAS2</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Mixed Nuts</td><td style='border-top:1px solid #ccc;'><strong>MN</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Pop Beans</td><td style='border-top:1px solid #ccc;'><strong>PB3</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Cashew Nuts</td><td style='border-top:1px solid #ccc;'><strong>CN2</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Chichacorn</td><td style='border-top:1px solid #ccc;'><strong>C2</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Roasted Skin</td><td style='border-top:1px solid #ccc;'><strong>RS2</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Roasted Skinless</td><td style='border-top:1px solid #ccc;'><strong>RS3</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Cracker Nuts</td><td style='border-top:1px solid #ccc;'><strong>CN3</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Spicy Cracker Nuts</td><td style='border-top:1px solid #ccc;'><strong>SC3</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Sung Sung</td><td style='border-top:1px solid #ccc;'><strong>SS</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Adobong Bawang</td><td style='border-top:1px solid #ccc;'><strong>AB</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Curry Flavored Peanuts</td><td style='border-top:1px solid #ccc;'><strong>CFP</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Coated Green Peas</td><td style='border-top:1px solid #ccc;'><strong>CGP</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Peanut Bar</td><td style='border-top:1px solid #ccc;'><strong>PB4</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Candy Coated Peanut</td><td style='border-top:1px solid #ccc;'><strong>CCP</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Roasted Almonds</td><td style='border-top:1px solid #ccc;'><strong>CA</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Honey Coated Peanuts (S)</td><td style='border-top:1px solid #ccc;'><strong>CCPS</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Honey Coated Peanuts (M)</td><td style='border-top:1px solid #ccc;'><strong>CCPM</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>R - Almonds</td><td style='border-top:1px solid #ccc;'><strong>RA</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Chicharon May Laman</td><td style='border-top:1px solid #ccc;'><strong>CML</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Candy Coated</td><td style='border-top:1px solid #ccc;'><strong>CC2</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>SM Bottled Water</td><td style='border-top:1px solid #ccc;'><strong>SBW</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Salted Egg Potato Chips</td><td style='border-top:1px solid #ccc;'><strong>SEPC</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Apron</td><td style='border-top:1px solid #ccc;'><strong>APRON</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>PW Yellow Polo Shirt (2xl)</td><td style='border-top:1px solid #ccc;'><strong>PWPS1</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>PW Yellow Polo Shirt (3xl)</td><td style='border-top:1px solid #ccc;'><strong>PWPS2</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>PW Yellow Polo Shirt (large)</td><td style='border-top:1px solid #ccc;'><strong>PWPS3</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Plain Vinegar</td><td style='border-top:1px solid #ccc;'><strong>PV</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Spicy Vinegar</td><td style='border-top:1px solid #ccc;'><strong>SV</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Combo Wrapper - Small</td><td style='border-top:1px solid #ccc;'><strong>CWS</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Combo Wrapper - Medium</td><td style='border-top:1px solid #ccc;'><strong>CWM</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Combo Wrapper - Large</td><td style='border-top:1px solid #ccc;'><strong>CWL</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Brown Wrapper - K1/4</td><td style='border-top:1px solid #ccc;'><strong>BW14</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Brown Wrapper - K1/2</td><td style='border-top:1px solid #ccc;'><strong>BW12</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Brown Wrapper - 3/4</td><td style='border-top:1px solid #ccc;'><strong>BW34</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Brown Wrapper - #2</td><td style='border-top:1px solid #ccc;'><strong>BW2</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Brown Wrapper - #4</td><td style='border-top:1px solid #ccc;'><strong>BW4</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Brown Wrapper - #10</td><td style='border-top:1px solid #ccc;'><strong>BW10</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Printed Plastic (PP) - 7X12</td><td style='border-top:1px solid #ccc;'><strong>PP712</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Printed Plastic (PP) - 8x14</td><td style='border-top:1px solid #ccc;'><strong>PP814</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Printed Plastic (PE) - 7X12</td><td style='border-top:1px solid #ccc;'><strong>PE712</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Printed Plastic (PE) - 8x14</td><td style='border-top:1px solid #ccc;'><strong>PE814</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Plastic Bag - Mini</td><td style='border-top:1px solid #ccc;'><strong>PBM</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Plastic Bag - Tiny</td><td style='border-top:1px solid #ccc;'><strong>PBT</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Plastic Bag - Medium</td><td style='border-top:1px solid #ccc;'><strong>PBM</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Plastic Bag - Large</td><td style='border-top:1px solid #ccc;'><strong>PBL</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Plastic - 5x8</td><td style='border-top:1px solid #ccc;'><strong>P58</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Plastic - 16x20</td><td style='border-top:1px solid #ccc;'><strong>P1620</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Clear Plastic - 1/2 kilo</td><td style='border-top:1px solid #ccc;'><strong>CP12</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Clear Plastic - 1 kilo</td><td style='border-top:1px solid #ccc;'><strong>CP1</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Vinegar Cups</td><td style='border-top:1px solid #ccc;'><strong>VC</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Vinegar Plastics - 1 1/4x10</td><td style='border-top:1px solid #ccc;'><strong>VP</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Alcohol</td><td style='border-top:1px solid #ccc;'><strong>ALC</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Report Paper</td><td style='border-top:1px solid #ccc;'><strong>RP</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Tissue</td><td style='border-top:1px solid #ccc;'><strong>TIS</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Gloves</td><td style='border-top:1px solid #ccc;'><strong>GLV</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Rug</td><td style='border-top:1px solid #ccc;'><strong>RUG</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Empty Container</td><td style='border-top:1px solid #ccc;'><strong>EC</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Sabaku (003) - 6x9</td><td style='border-top:1px solid #ccc;'><strong>SAB</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Crave Logo/Sticker - 60x46</td><td style='border-top:1px solid #ccc;'><strong>CLS</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Greenstone Boxes</td><td style='border-top:1px solid #ccc;'><strong>GB</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Skinless Boxes</td><td style='border-top:1px solid #ccc;'><strong>SB</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Coke</td><td style='border-top:1px solid #ccc;'><strong>COKE</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Cocktail (medium)</td><td style='border-top:1px solid #ccc;'><strong>CTM</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Cocktail (large)</td><td style='border-top:1px solid #ccc;'><strong>CTL</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Blueberry Lemonade (BL)</td><td style='border-top:1px solid #ccc;'><strong>BL</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Cranberry Lemonade (CRL)</td><td style='border-top:1px solid #ccc;'><strong>CRL</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Cucumber Lemonade (CUL)</td><td style='border-top:1px solid #ccc;'><strong>CUL</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Fresh Lemon</td><td style='border-top:1px solid #ccc;'><strong>FL</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Ginger Lemonade (GL)</td><td style='border-top:1px solid #ccc;'><strong>GIL</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Green Apple Lemonade (GAL)</td><td style='border-top:1px solid #ccc;'><strong>GAL</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Green Tea Lemonade (GTL)</td><td style='border-top:1px solid #ccc;'><strong>GTL</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Jasmine Tea Lemonade (JTL)</td><td style='border-top:1px solid #ccc;'><strong>JTL</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Kiwi Lemonade (KL)</td><td style='border-top:1px solid #ccc;'><strong>KL</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Minty Lemonade (ML)</td><td style='border-top:1px solid #ccc;'><strong>ML</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Orange Lemonade (OL)</td><td style='border-top:1px solid #ccc;'><strong>OL</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Passion Fruit Lemonade</td><td style='border-top:1px solid #ccc;'><strong>PFL</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Strawberry Lemonade (SL)</td><td style='border-top:1px solid #ccc;'><strong>STL</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Yoghurt Lemonade (YL)</td><td style='border-top:1px solid #ccc;'><strong>YL</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Syrup</td><td style='border-top:1px solid #ccc;'><strong>SYR</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Plastic Cups 16 0z</td><td style='border-top:1px solid #ccc;'><strong>CUP</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Raw lemon</td><td style='border-top:1px solid #ccc;'><strong>RL</strong></td></tr>
						<tr><td style='border-top:1px solid #ccc;'>Plastic cups lid 16 oz</td><td style='border-top:1px solid #ccc;'><strong>CLID</strong></td></tr>

					</table>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<script>

		$(document).ready(function() {
			var is_njl = 0;
			$('body').on('click','#btnCodes',function(){
				$('#myModalCodes').modal('show')
			});
			$('body').on('click','#btnUpdateSms',function(){

				var id = $('#sms_id').val();
				var dt = $('#sms_date_report').val();

				$.ajax({
				    url:'../ajax/ajax_sms.php',
				    type:'POST',
				    data: {functionName:'updateSmsDate',id:id,dt:dt},
				    success: function(data){
				        tempToast('info',data,'Info');
					    getPage($('#hiddenpage').val())
				    },
				    error:function(){

				    }
				});

			});



			$('body').on('click','.btnUpdateDate',function(){

				var con = $(this);

				var id = con.attr('data-id');
				var dt = con.attr('data-dt');

				$('#sms_date_report').val(dt);
				$('#sms_id').val(id);

				$('#myModalUpdateDate').val('');
				$('#myModalUpdateDate').modal('show');


			});

			$('#received_date').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#received_date').datepicker('hide');
			});

			$('#new_received_data').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#new_received_data').datepicker('hide');
			});

			$('body').on('click','#btnNewItem',function(){
				var item_id = $('#new_item_id').val();
				var qty = $('#new_qty').val();
				var received_date = $('#new_received_data').val();
				var new_branch_id = $('#new_branch_id').val();
				var con = $(this);

				button_action.start_loading(con);

				$.ajax({
				    url:'../ajax/ajax_sms.php',
				    type:'POST',
				    data: {functionName:'smBottles',item_id:item_id,qty:qty,branch_id:new_branch_id,received_date:received_date},
				    success: function(data){
				        tempToast('info',data,'Info')
					    $('#myModalNew').modal('hide');
					    button_action.end_loading(con);
				    },
				    error:function(){
					    button_action.end_loading(con);
				    }
				});

			});

			$('body').on('click','#btnAdd',function(){
			    $('#new_item_id').select2('val',null);
			    $('#new_branch_id').select2('val',null);
				$('#new_qty').val('');
				$('#new_received_data').val('');
				$('#myModalNew').modal('show');
			});


			$('#branch_id').select2({
				placeholder:'Choose branch',
				allowClear:true
			});
			$('#new_branch_id').select2({
				placeholder:'Choose branch',
				allowClear:true
			});
			$('#type').select2({
				placeholder:'Choose Type',
				allowClear:true
			});

			getPage(0);
			$('body').on('click','.paging',function(e){
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});
			var timer;
			$("#search").keyup(function(){

				var searchtxt = $("#search");

				clearTimeout(timer);
				timer = setTimeout(function() {
					if(searchtxt.val()){
						searchtxt.val(searchtxt.val().trim());
					}
					getPage(0);
				}, 1000);
			});
			function getPage(p){
				var search = $('#search').val();
				var b = $('#branch_id').val();
				var type= $('#type').val();
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,type:type,functionName:'smsLogPaginate',cid: <?php echo $user->data()->company_id; ?>,search:search,b:b},
					success: function(data){
						$('#holder').html(data);
					}
				});
			}

			$('body').on('change','#branch_id,#type',function(){
				getPage(0);
			});

			$('body').on('click','.btnShowData',function(){

				var con = $(this);
				var number = con.attr('data-number');
				var msg = con.attr('data-message');
				var id = con.attr('data-id');
				var status = con.attr('data-status');
				var name = con.attr('data-name');
				var date_received = con.attr('data-received');
				var branch_description = con.attr('data-branch_description');
				if(branch_description == 'Not Just Lemons'){
					is_njl = 1;
					getMsgNJL(number,msg,id,status,date_received,name);
				} else {
					is_njl = 0;
					getMsg(number,msg,id,status,date_received,name);
				}



			});
			$('body').on('click','.btnShowDataNJL',function(){

				var con = $(this);
				var number = con.attr('data-number');
				var msg = con.attr('data-message');
				var id = con.attr('data-id');
				var status = con.attr('data-status');
				var name = con.attr('data-name');
				var date_received = con.attr('data-received');
				getMsgNJL(number,msg,id,status,date_received,name);

			});



			function getMsgNJL(number,msg,id,status,date_received,name){
				$('#myModal').modal('show');
				$('#mbody').html('Loading...');
				var total_sold = 0;

				var fresh_lemon_id = 196;



				$.ajax({
					url:'../ajax/ajax_sms.php',
					type:'POST',
					dataType:'json',
					data: {functionName:'messageDetails',number:number,msg:msg,id:id,date_received:date_received},
					success: function(data){

						var branch = data.branch_name;
						var branch_id = data.branch_id;
						var expenses = data.expenses;
						var sales_deposit = data.sales_deposit;
						var data = data.data;
						var total_expenses = 0;


						if(data.details){

							var expense_form = "<strong class='span-block'>Expense:</strong><div class='row'>";
							expense_form += "<div class='col-md-3'>";
							expense_form += "<input type='text' class='form-control' id='txtExpenseAmount' placeholder='Amount'>";
							expense_form += "</div>";
							expense_form += "<div class='col-md-3'>";
							expense_form += "<input type='text' class='form-control' id='txtExpenseDesc' placeholder='Description'>";
							expense_form += "</div>";
							expense_form += "<div class='col-md-3'>";
							expense_form += "<button data-branch_id='"+branch_id+"' data-id='"+id+"' data-old_msg='"+msg+"' data-number='"+number+"' data-date_received='"+date_received+"'  class='btn btn-default' id='btnAddExpense'>Add Expense</butotn>";
							expense_form += "</div>";
							expense_form += "</div><br>";


							var deposit_form = "<strong class='span-block'>Sales Deposit:</strong><div class='row'>";
							deposit_form += "<div class='col-md-3'>";
							deposit_form += "<input type='text' class='form-control' id='txtSalesDeposit' placeholder='Sales Deposit'>";
							deposit_form += "</div>";
							deposit_form += "<div class='col-md-3'>";
							deposit_form += "<button data-branch_id='"+branch_id+"' data-id='"+id+"' data-old_msg='"+msg+"' data-number='"+number+"' data-date_received='"+date_received+"'  class='btn btn-default' id='btnSalesDeposit'>Update Sales Deposit</butotn>";
							deposit_form += "</div>";
							deposit_form += "</div><br>";



							var det = data.details;
							var ret = "<h3>"+branch+"</h3>";

							ret += expense_form;
							ret += deposit_form;

							ret += "<p class='text-right'><button data-name='"+name+"' data-date='"+date_received+"' data-branch='"+branch+"' class='btn btn-default btn-sm' id='btnPrint'>Print</button><button class='btn btn-default btn-sm' data-id='"+id+"' data-old_msg='"+msg+"' data-number='"+number+"' data-date_received='"+date_received+"'  id='btnResave'>Save Changes</button></p>";
							ret += "<div id='printtable'>";
							ret += "<table id='tblSummaryReportDicer' class='table table=condensed table-bordered'>";
							if(status == 0){
								ret += "<thead><tr><th>Item</th><th>Price</th><th>Beg</th><th>Order</th><th>Total Beg</th><th>Ending</th><th>Sold Cups</th><th>Sold Lemons</th><th>Total Amount</th></tr></thead>";
							} else {
								ret += "<thead><tr><th>Item</th><th>Reported</th><th>Sold Cups</th><th>Sold Lemons</th></tr></thead>";
							}

							ret += "<tbody>";
							var arr_sold = [];
							if(data.sold){
								var data_sold = data.sold;
								for(var asold in data.sold){
									arr_sold[data_sold[asold].item_id] = data_sold[asold].qty;
								}
							}
							var arr_badorder = [];
							if(data.badorder){
								var data_bo = data.badorder;
								for(var abadorder in data.badorder){
									arr_badorder[data_bo[abadorder].item_id] = data_bo[abadorder].qty;
								}
							}


							for(var i in det){
								console.log(det[i].item_id + " " + det[i].cur_order);
								if(det[i].total_inv == '0' && det[i].reported == '0' && det[i].cur_order == '0'){
									continue;
								}
								var total =parseFloat(det[i].diff) * parseFloat(det[i].price);
								total_sold = parseFloat(total_sold) + parseFloat(total);
								if(status == 0){
									var cls_warn = '';
									if(det[i].diff < 0){
										cls_warn ='bg-warning';
									}
									var current_bo = '';
									if(arr_badorder[det[i].item_id]){
										current_bo= arr_badorder[det[i].item_id];
									}

									var lemon_qty = 0;
									if(fresh_lemon_id == det[i].item_id){
										lemon_qty = det[i].diff / 2;
										det[i].diff = 0;
									} else {
										if(det[i].diff && (det[i].item_code).indexOf('Lemonade') > 0){
											lemon_qty = det[i].diff / 2;
										}

									}

									ret += "<tr class='"+cls_warn+"' " +
										"data-price='"+det[i].price+"' " +
										"id='tr_"+det[i].item_id+"'>" +
										"<td>"+det[i].item_code+"</td>" +
										"<td>"+det[i].price+"</td>" +
										"<td>"+det[i].cur_inv+" <i data-number='"+number+"' data-name='"+name+"' data-status='"+status+"' data-msg='"+msg+"' data-id='"+id+"' data-date_received='"+date_received+"' data-qty='"+det[i].cur_inv+"' data-item_id='"+det[i].item_id+"' data-branch_id='"+branch_id+"' class='fa fa-pencil cpointer btnCurrentInventory'></i></td>" +
										"<td>"+det[i].cur_order+"</td>" +
										"<td>0</td>" +
										"<td><input type='text' data-id='"+det[i].item_id+"' class='updateThisItem' value='"+det[i].reported+"'></td>" +
										"<td data-total_sold='"+det[i].diff+"'><strong class='text-danger'>"+ number_format(det[i].diff,2)+"</strong></td>" +
										"<td>"+lemon_qty+"</td>" +
										"<td class='text-right text-danger' data-orig_total='"+total+"'>"+ number_format(total,2) +"</td>" +
										"</tr>";
								} else {
									var cur_sold = 0;
									if(arr_sold[det[i].item_id]){
										cur_sold = arr_sold[det[i].item_id];
									}
									var lemon_qty = 0;
									if(fresh_lemon_id == det[i].item_id){
										lemon_qty = cur_sold / 2;
										cur_sold = 0;
									} else {
										if(cur_sold && (det[i].item_code).indexOf('Lemonade') > 0){
											lemon_qty = cur_sold / 2;
										}
									}
									ret += "<tr><td>"+det[i].item_code+"</td><td>"+det[i].reported+"</td><td>"+cur_sold+"</td><td>"+lemon_qty+"</td></tr>";
								}
							}
							ret += "</tbody>";
							ret += "</table>";
							ret += "</div>";

							ret += "<div id='print_others'>";

							if(expenses.length > 0){
								var expenses_list = "<table id='tblSummaryOP' class='table table table=condensed table-bordered'>";
								expenses_list += "<tr><th>Description</th><th>Amount</th><th></th></tr>";
								for(var e in expenses){
									total_expenses = parseFloat(total_expenses) + parseFloat(expenses[e].amount);
									expenses_list += "<tr><td>"+expenses[e].description+"</td><td>"+expenses[e].amount+"</td><td><button data-expid='"+expenses[e].id+"' data-id='"+id+"' data-old_msg='"+msg+"' data-number='"+number+"' data-date_received='"+date_received+"' class='btn btn-danger btnDelExpense'><i class='fa fa-trash'></i></button></td></tr>";
								}
								expenses_list += "</table>";
								ret += expenses_list;
								ret += "<h4>Total Expense: <span id='spanExpense'>"+number_format(total_expenses,2)+"</span></h4>";
							}
							if(status == 0){
								ret += "<h4 class='text-danger' id='badOrderContainer'><strong>Sales Deposit "+number_format(sales_deposit,2)+"</strong></h4>";
								ret += "<h4 class='text-danger' id='badOrderContainer'><strong>Bad Order: <span id='spanBadOrder'>"+number_format('0',2)+"</span></strong></h4>";
								ret += "<h4 class='text-danger'><strong>Total Sold: <span id='spanTotal' data-orig_total='"+total_sold+"' >"+number_format(total_sold,2)+"</span></strong></h4>";
								ret += "<h4 class='text-danger'><strong>Net: <span id='spanNet' data-orig_net='"+(total_sold-total_expenses)+"'>"+number_format(total_sold-total_expenses,2)+"</span></strong></h4>";
								ret += "</div>"; // end print others
								ret += "<button data-type='1' data-id='"+id+"' data-number='"+number+"' data-msg='"+msg+"' class='btn btn-default' id='btnProcessedItem'>Process</button>";
								ret += " <button data-type='1' data-id='"+id+"' data-number='"+number+"' data-msg='"+msg+"' class='btn btn-danger' id='btnDecline'>Decline</button>";

							} else {
								ret += "</div>";  // end print others
							}

							$('#mbody').html(ret);
						}
					},
					error:function(){

					}
				});
			}

			function getMsg(number,msg,id,status,date_received,name){
				$('#myModal').modal('show');
				$('#mbody').html('Loading...');
				var total_sold = 0;



				$.ajax({
					url:'../ajax/ajax_sms.php',
					type:'POST',
					dataType:'json',
					data: {functionName:'messageDetails',number:number,msg:msg,id:id,date_received:date_received},
					success: function(data){

						var branch = data.branch_name;
						var branch_id = data.branch_id;
						var expenses = data.expenses;
						var sales_deposit = data.sales_deposit;
						var data = data.data;
						var total_expenses = 0;

						var expense_form = "<strong class='span-block'>Expense:</strong><div class='row'>";
						expense_form += "<div class='col-md-3'>";
						expense_form += "<input type='text' class='form-control' id='txtExpenseAmount' placeholder='Amount'>";
						expense_form += "</div>";
						expense_form += "<div class='col-md-3'>";
						expense_form += "<input type='text' class='form-control' id='txtExpenseDesc' placeholder='Description'>";
						expense_form += "</div>";
						expense_form += "<div class='col-md-3'>";
						expense_form += "<button data-branch_id='"+branch_id+"' data-id='"+id+"' data-old_msg='"+msg+"' data-number='"+number+"' data-date_received='"+date_received+"'  class='btn btn-default' id='btnAddExpense'>Add Expense</butotn>";
						expense_form += "</div>";
						expense_form += "</div><br>";


						var deposit_form = "<strong class='span-block'>Sales Deposit:</strong><div class='row'>";
						deposit_form += "<div class='col-md-3'>";
						deposit_form += "<input type='text' class='form-control' id='txtSalesDeposit' placeholder='Sales Deposit'>";
						deposit_form += "</div>";
						deposit_form += "<div class='col-md-3'>";
						deposit_form += "<button data-branch_id='"+branch_id+"' data-id='"+id+"' data-old_msg='"+msg+"' data-number='"+number+"' data-date_received='"+date_received+"'  class='btn btn-default' id='btnSalesDeposit'>Update Sales Deposit</butotn>";
						deposit_form += "</div>";
						deposit_form += "</div><br>";


						if(data.details){
							var det = data.details;
							var ret = "<h3>"+branch+"</h3>";

							ret += expense_form;
							ret += deposit_form;

							ret += "<p class='text-right'><button data-name='"+name+"' data-date='"+date_received+"' data-branch='"+branch+"' class='btn btn-default btn-sm' id='btnPrint'>Print</button> <button class='btn btn-default btn-sm' data-id='"+id+"' data-old_msg='"+msg+"' data-number='"+number+"' data-date_received='"+date_received+"'  id='btnResave'>Save Changes</button></p>"
							ret += "<div id='printtable'>";
							ret += "<table id='tblSummaryReportDicer' class='table table=condensed table-bordered'>";
							if(status == 0){
								ret += "<thead><tr><th>Item</th><th>Price</th><th>Cur Inv</th><th>Cur Order</th><th>Bad Order</th><th>Total</th><th>Reported</th><th>Sold</th><th>Total Amount</th></tr></thead>";
							} else {
								ret += "<thead><tr><th>Item</th><th>Reported</th><th>Sold</th></tr></thead>";
							}

							ret += "<tbody>";
							var arr_sold = [];
							if(data.sold){
								var data_sold = data.sold;
								for(var asold in data.sold){
									arr_sold[data_sold[asold].item_id] = data_sold[asold].qty;
								}
							}
							var arr_badorder = [];
							if(data.badorder){
								var data_bo = data.badorder;
								for(var abadorder in data.badorder){
									arr_badorder[data_bo[abadorder].item_id] = data_bo[abadorder].qty;
								}
							}


							for(var i in det){
								console.log(det[i].item_id + " " + det[i].cur_order);
								if(det[i].total_inv == '0' && det[i].reported == '0' && det[i].cur_order == '0'){
									continue;
								}
								var total =parseFloat(det[i].diff) * parseFloat(det[i].price);
								total_sold = parseFloat(total_sold) + parseFloat(total);
								if(status == 0){
									var cls_warn = '';
									if(det[i].diff < 0){
										cls_warn ='bg-warning';
									}
									var current_bo = '';
									if(arr_badorder[det[i].item_id]){
										current_bo= arr_badorder[det[i].item_id];
									}
									ret += "<tr class='"+cls_warn+"' data-price='"+det[i].price+"' id='tr_"+det[i].item_id+"'><td>"+det[i].item_code+"</td><td>"+det[i].price+"</td><td>"+det[i].cur_inv+" <i data-number='"+number+"' data-name='"+name+"' data-status='"+status+"' data-msg='"+msg+"' data-id='"+id+"' data-date_received='"+date_received+"' data-qty='"+det[i].cur_inv+"' data-item_id='"+det[i].item_id+"' data-branch_id='"+branch_id+"' class='fa fa-pencil cpointer btnCurrentInventory'></i></td><td>"+det[i].cur_order+"</td><td><input type='text' class='txtBadOrder' value='"+current_bo+"' data-item_id='"+det[i].item_id+"'></td><td>"+det[i].total_inv+"</td><td><input type='text' data-id='"+det[i].item_id+"' class='updateThisItem' value='"+det[i].reported+"'></td><td data-total_sold='"+det[i].diff+"'><strong class='text-danger'>"+ number_format(det[i].diff,2)+"</strong></td><td class='text-right text-danger' data-orig_total='"+total+"'>"+ number_format(total,2) +"</td></tr>";
								} else {
									var cur_sold = 0;
									if(arr_sold[det[i].item_id]){
										cur_sold = arr_sold[det[i].item_id];
									}
									ret += "<tr><td>"+det[i].item_code+"</td><td>"+det[i].reported+"</td><td>"+cur_sold+"</td></tr>";
								}
							}
							ret += "</tbody>";
							ret += "</table>";
							ret += "</div>";

							ret += "<div id='print_others'>";

							if(expenses.length > 0){
								var expenses_list = "<table id='tblSummaryOP' class='table table table=condensed table-bordered'>";
								expenses_list += "<tr><th>Description</th><th>Amount</th><th></th></tr>";
								for(var e in expenses){
									total_expenses = parseFloat(total_expenses) + parseFloat(expenses[e].amount);
									expenses_list += "<tr><td>"+expenses[e].description+"</td><td>"+expenses[e].amount+"</td><td><button data-expid='"+expenses[e].id+"' data-id='"+id+"' data-old_msg='"+msg+"' data-number='"+number+"' data-date_received='"+date_received+"' class='btn btn-danger btnDelExpense'><i class='fa fa-trash'></i></button></td></tr>";
								}
								expenses_list += "</table>";
								ret += expenses_list;
								ret += "<h4>Total Expense: <span id='spanExpense'>"+number_format(total_expenses,2)+"</span></h4>";
							}
							if(status == 0){
								ret += "<h4 class='text-danger' id='badOrderContainer'><strong>Sales Deposit "+number_format(sales_deposit,2)+"</strong></h4>";
								ret += "<h4 class='text-danger' id='badOrderContainer'><strong>Bad Order: <span id='spanBadOrder'>"+number_format('0',2)+"</span></strong></h4>";
								ret += "<h4 class='text-danger'><strong>Total Sold: <span id='spanTotal' data-orig_total='"+total_sold+"' >"+number_format(total_sold,2)+"</span></strong></h4>";
								ret += "<h4 class='text-danger'><strong>Net: <span id='spanNet' data-orig_net='"+(total_sold-total_expenses)+"'>"+number_format(total_sold-total_expenses,2)+"</span></strong></h4>";
								ret += "</div>"; // end print others
								ret += "<button data-id='"+id+"' data-number='"+number+"' data-msg='"+msg+"' class='btn btn-default' id='btnProcessedItem'>Process</button>";
								ret += " <button data-id='"+id+"' data-number='"+number+"' data-msg='"+msg+"' class='btn btn-danger' id='btnDecline'>Decline</button>";

							} else {
								ret += "</div>";  // end print others
							}

							$('#mbody').html(ret);
						}
					},
					error:function(){

					}
				});
			}

			$('body').on('click','.btnCurrentInventory',function(){
				var con = $(this);
				var branch_id = con.attr('data-branch_id');
				var item_id = con.attr('data-item_id');
				var qty = con.attr('data-qty');
				var uqty = prompt("Enter updated inventory: ",qty);
				var id = con.attr('data-id');
				var msg = con.attr('data-msg');
				var number = con.attr('data-number');
				var date_received = con.attr('data-date_received');
				var name = con.attr('data-name');
				var status = con.attr('data-status');



				if(!uqty || qty == uqty){
					tempToast('error','No changes made.','Warning');
					return;
				}

				$.ajax({
				    url:'../ajax/ajax_sms.php',
				    type:'POST',
				    data: {functionName:'updateCurrentInventory',qty:uqty,item_id:item_id,branch_id:branch_id},
				    success: function(data){
					    getMsg(number,msg,id,status,date_received,name);
				    },
				    error:function(){

				    }
				});

			});

			$('body').on('keyup','.txtBadOrder',function(){
				var con = $(this);
				var spanTotal = $('#spanTotal');
				var spanNet = $('#spanNet');
				var spanExpense = $('#spanExpense');

				var row = con.parents('tr');
				var price = row.attr('data-price');
				var qty = con.val();
				qty = (qty) ? qty : 0;
				var total;
				var cur_total = row.children().eq(8).attr('data-orig_total');
				var cur_qty_sold = row.children().eq(7).attr('data-total_sold');
				cur_total = replaceAll(cur_total,",","");
				cur_qty_sold = replaceAll(cur_qty_sold,",","");


				var g_total =  replaceAll(spanTotal.attr('data-orig_total'),",","");
				var n_total =  replaceAll(spanNet.attr('data-orig_net'),",","");

				cur_qty_sold = parseFloat(cur_qty_sold) - parseFloat(qty);



				total = qty * price;
				cur_total = parseFloat(cur_total) - parseFloat(total);
				row.children().eq(8).text(number_format(cur_total,2));
				row.children().eq(7).html("<strong class='text-danger'>" + number_format(cur_qty_sold,2) + "</strong>");


				var totalBadOrder = getBadOrderTotal();

				g_total =  parseFloat(g_total) - parseFloat(totalBadOrder);
				n_total =  parseFloat(n_total) - parseFloat(totalBadOrder);

				spanTotal.text(number_format(g_total,2));
				spanNet.text(number_format(n_total,2));


				if(parseFloat(totalBadOrder) > 0){
					$('#badOrderContainer').show();
					$('#spanBadOrder').html(number_format(totalBadOrder,2));
				} else {
					$('#badOrderContainer').hide();
					$('#spanBadOrder').html(number_format('0',2));
				}

			});
			function getBadOrderTotal(){
				var total = 0;
				$('.txtBadOrder').each(function(){
					var con = $(this);
					var row = con.parents('tr');
					var v = con.val();
					v = (v) ? v :0;

					if(v){
						total += parseFloat(v) * parseFloat(row.attr('data-price'));
						console.log(total);
					}
				});
				return total;
			}
			function getBadOrderItems(){
				var arr = [];
				$('.txtBadOrder').each(function(){
					var con = $(this);
					var v = con.val();
					v = (v) ? v :0;
					if(v){
						var item_id = con.attr('data-item_id');
						arr.push({item_id:item_id,qty:v});
					}
				});
				return arr;
			}
			$('body').on('click','#btnProcessedItem',function(){

				var con = $(this);
				var number = con.attr('data-number');
				var msg = con.attr('data-msg');
				var id = con.attr('data-id');
				var type = con.attr('data-type');
				button_action.start_loading(con);
				var total_badorder = getBadOrderTotal();
				var total_badorder_items = getBadOrderItems();

				$.ajax({
				    url:'../ajax/ajax_sms.php',
				    type:'post',
				    data: {functionName:'processedData',type:type,number:number,msg:msg,id:id,total_badorder:total_badorder,total_badorder_items:JSON.stringify(total_badorder_items)},
				    success: function(data){
						alertify.alert(data,function(){
							location.href='sms_log.php';
							button_action.end_loading(con);
						});
				    },
				    error:function(){
					    button_action.end_loading(con);
				    }
				});

			});

			$('body').on('click','#btnDecline',function(){
				var con = $(this);

				var id = con.attr('data-id');
				button_action.start_loading(con);
				alertify.confirm("Are you sure you want to decline this request?",function(e){
					if(e){
						$.ajax({
							url:'../ajax/ajax_sms.php',
							type:'post',
							data: {functionName:'declineData',id:id},
							success: function(data){
								location.href='sms_log.php';
							},
							error:function(){
								button_action.end_loading(con);
							}
						});
					}

				});

			});
			//updateInventory("2 1 3 3 4 7",6,23);
			function updateInventory(orig,item_id,qty){
				var sp = orig.split(" ");
				if(sp.length > 0){
					var ctr = 1;
					var tmp = 0;
					var arr= [];

					for(var i=0;i<= sp.length;i++){

						if(ctr % 2 == 0){
							arr[tmp] = sp[i];
						} else {
							tmp = sp[i];
						}
						ctr++;
					}

					if(arr[item_id]){
						arr[item_id] = qty;
					}

					var ret_val = "";
					for(var i in arr){
						ret_val += " " + i + " " + arr[i];
					}
					return ret_val.trim();
				}
			}

			$('body').on('click','#btnSalesDeposit',function(){
				var btncon = $(this);
				button_action.start_loading(btncon);

				var id = btncon.attr('data-id');
				var branch_id = btncon.attr('data-branch_id');
				var date_received = btncon.attr('data-date_received');
				var number = btncon.attr('data-number');
				var old_msg = btncon.attr('data-old_msg');
				var amount = $('#txtSalesDeposit').val();


				if(amount && branch_id){

					$.ajax({
						url:'../ajax/ajax_sms.php',
						type:'POST',
						data: {functionName:'updateSalesDeposit',branch_id:branch_id,date_received:date_received,amount:amount},
						success: function(data){
							if(data == 1){
								if(is_njl == 1){

									getMsgNJL(number,old_msg,id,0,date_received);
								} else {
									getMsg(number,old_msg,id,0,date_received);
								}

							} else {
								tempToast('error','Invalid request.','Error');
							}
						},
						error:function(){

						}
					});
					button_action.end_loading(btncon);
				} else {
					tempToast('error','Invalid request.','Error');
					button_action.end_loading(btncon);
				}

			});

			$('body').on('click','#btnAddExpense',function(){
				var btncon = $(this);
				button_action.start_loading(btncon);

				var id = btncon.attr('data-id');
				var branch_id = btncon.attr('data-branch_id');
				var date_received = btncon.attr('data-date_received');
				var number = btncon.attr('data-number');
				var old_msg = btncon.attr('data-old_msg');
				var amount = $('#txtExpenseAmount').val();
				var description = $('#txtExpenseDesc').val();
				if(amount && description && !isNaN(amount) && branch_id){
					$.ajax({
						url:'../ajax/ajax_sms.php',
						type:'POST',
						data: {functionName:'addNewExpense',dt:date_received, amount:amount,branch_id:branch_id,description:description},
						success: function(data){
							if(data == 1){
								if(is_njl == 1){
									getMsgNJL(number,old_msg,id,0,date_received);
								} else {
									getMsg(number,old_msg,id,0,date_received);
								}

							} else {
								tempToast('error','Invalid request.','Error');
							}
						},
						error:function(){

						}
					});
					button_action.end_loading(btncon);
				} else {
					tempToast('error','Invalid request.','Error');
					button_action.end_loading(btncon);
				}




			});

			$('body').on('click','.btnDelExpense',function(){
				var btncon = $(this);
				button_action.start_loading(btncon);
				var expid = btncon.attr('data-expid');
				var id = btncon.attr('data-id');
				var date_received = btncon.attr('data-date_received');
				var number = btncon.attr('data-number');
				var old_msg = btncon.attr('data-old_msg');

				$.ajax({
					url:'../ajax/ajax_sms.php',
					type:'POST',
					data: {functionName:'deleteExpense',expid:expid},
					success: function(data){
						if(data == '1'){
							if(is_njl == 1){

								getMsgNJL(number,old_msg,id,0,date_received);
							} else {
								getMsg(number,old_msg,id,0,date_received);
							}

						} else {
							alert("Invalid request");
						}
						button_action.end_loading(btncon);
					},
					error:function(){

					}
				});

			});
			$('body').on('click','#btnResave',function(){
				var ret = " ";
				var btncon = $(this);
				button_action.start_loading(btncon);
				var id = btncon.attr('data-id');
				var date_received = btncon.attr('data-date_received');
				var number = btncon.attr('data-number');
				var old_msg = btncon.attr('data-old_msg');
				$('.updateThisItem').each(function(){
					var con = $(this);
					var item_id = con.attr('data-id');
					var qty = con.val();
					ret += item_id + " " + qty + " ";

				});
				ret = ret.trim();



				$.ajax({
				    url:'../ajax/ajax_sms.php',
				    type:'POST',
				    data: {functionName:'updateReportedInv',id:id,msg:ret,old_msg:old_msg},
				    success: function(data){
				        if(data == '1'){
					        if(is_njl == 1){
						        getMsgNJL(number,ret,id,0,date_received);
					        } else {
						        getMsg(number,ret,id,0,date_received);
					        }

				        }
					    button_action.end_loading(btncon);
				    },
				    error:function(){

				    }
				});
			});



			$('body').on('click','#btnPrint',function(){
				var con = $(this);
				var name = con.attr('data-name');
				var dt = con.attr('data-date');
				var branch = con.attr('data-branch');

				var html = "<h3 class='text-center'>Inventory Report</h3>";
				html += "<div class='row'>";
				html += "<div class='col-md-6'>";
				html += "<p>Branch: "+branch+"</p>";
				html += "<p>In charge: "+name+"</p>";

				html += "</div>";
				html += "<div class='col-md-6'>";
				html += "<p>Date: "+dt+"</p>";
				html += "</div>";
				html += "</div>";

				html += "<table  class='table table=condensed table-bordered'>";

				html += "<thead><tr><th>Item</th><th>Price</th><th>Prev Inv</th><th>Order</th><th>Bad Order</th><th>Total</th><th>Reported</th><th>Sold</th><th>Total Amount</th></tr></thead>";
				$('#tblSummaryReportDicer tbody tr').each(function(){
					var row = $(this);
					var item = row.children().eq(0).text();
					var price = row.children().eq(1).text();
					var cur_inv = row.children().eq(2).text();
					var cur_order = row.children().eq(3).text();
					var bo = row.children().eq(4).find('input').val();
					var total =  row.children().eq(5).text();
					var reported = row.children().eq(6).find('input').val();
					var sold =  row.children().eq(7).text();;
					var total_amount =   row.children().eq(8).text();

					html += "<tr><td>"+item+"</td><td>"+price+"</td><td>"+cur_inv+"</td><td>"+cur_order+"</td><td>"+bo+"</td><td>"+total+"</td><td>"+reported+"</td><td>"+sold+"</td><td>"+total_amount+"</td></tr>";


				});
				html += "</table>";
				var others = $('#print_others').html();
				html += others;
				printWithStyle(html);
			});

			function printWithStyle(data){
				var mywindow = window.open('', 'new div', '');
				mywindow.document.write('<html><head><title></title><style></style>');
				mywindow.document.write('<link rel="stylesheet" href="../css/bootstrap.css" type="text/css" />');
				mywindow.document.write('</head><body style="padding:0;margin:0;;font-family: Arial, Helvetica, sans-serif;">');
				mywindow.document.write(data);
				mywindow.document.write('</body></html>');
				setTimeout(function() {
					mywindow.print();
					mywindow.close();
				}, 300);
				return true;
			}



		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>