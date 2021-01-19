<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('reports')) {
		// redirect to denied page
		Redirect::to(1);
	}
function get_nested($array,$child = FALSE,$iischild=''){

		$str = '';

		if (count($array)){
			$iischild .= $child == FALSE ? '' : '-';

			foreach ($array as $item){


				if(isset($item['children']) && count($item['children'])){

					$str .= '<option value="'.$item['id'].'">'.$iischild.$item['name'].'</option>';
					$str .= get_nested($item['children'], true, $iischild);
				} else {
					if($child == false) $iischild='';
					$str .= '<option value="'.$item['id'].'">'.$iischild.($item['name']).'</option>';
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
	$ccc = new Category();
	$cc = objectToArray($ccc->getCategory($user->data()->company_id));

	$ss = new Sales();

	$branch = new Branch();
	$branches =  $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));

?>

	<input type="hidden" id='MEMBER_LABEL' value='<?php echo MEMBER_LABEL; ?>'>
	<!-- Page content -->
<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<div class="row">

					<h1>
						<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Reports
					</h1>

			</div>


		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('salesflash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('salesflash') . "</div>";
			}
		?>
	<div class="row">
		<div class="col-md-6">
		<div class="btn-group" role="group" aria-label="...">
			<input type="hidden" value='1' id='whereami'>
		  <button type="button" class="btn btn-default" id='btnHome'><span class='glyphicon glyphicon-home'></span> </button>
		  <button type="button" class="btn btn-default" id='btnGraph'><span class='glyphicon glyphicon-stats'></span> </button>
			<button type="button" class="btn btn-default" id='btnSales'><span class='glyphicon glyphicon-barcode'></span> </button>

		</div>
		</div>
		<div class="col-md-6 text-right">
			<div class="btn-group" role="group" aria-label="...">
				<button class='btn btn-default' id='filter'><span class='glyphicon glyphicon-filter'></span> <span class='hidden-xs'>Filter</span></button>
				<button  class="btn btn-default" id='btnPerItem'><i class='fa fa-cube'></i> <span class='hidden-xs'>Per Item</span></button>
				<button  class="btn btn-default" id='btnPerTransaction'><i class='fa fa-cubes'></i> <span class='hidden-xs'>Per Transaction</span></button>
				<?php if($user->hasPermission('dl_sales')){ ?>
				<button  class="btn btn-default" id='btnDownloadExcel'><i class='fa fa-download'></i> <span class='hidden-xs'>Download</span></button>
				<?php } ?>
			</div>
		</div>
	</div>
		<div id="filters"></div>
		<input type="hidden" id='ascdesc' value='1' />
		<input type="hidden" id='sort_by' value='' />
		<input type="hidden" id='report_type' value='1' />
			<input type="hidden" id="hiddenpage" />


					<hr />
		<div id="homeCon">
			<div id="holder">
				<div class='text-center'><i class='fa fa-circle-o-notch fa-spin fa-2x'></i></div>
			</div>
		</div>
		<div id="graphCon" style='display: none;' >
			<br/>
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Sales</strong></div>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-6">
							<div id="salestypegraph" class='col-md-12' style='height:400px;'></div>
						</div>
						<div class="col-md-6">
							<br/><br/>
							<div id="salestypegraphlabel"></div>
						</div>
					</div>
				</div>
			</div>
			<br/>
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Top Branch</strong></div>
				<div class="panel-body">
			<div class="row">
				<div class="col-md-6">
							<div id="myfirstchart" class='col-md-12' style='height:400px;'></div>
				</div>
				<div class="col-md-6">
					<br/><br/>
					<div id="myfirstchartlabel"></div>
				</div>
				</div>
			</div>
			</div>
			<br/>
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Top Items Base on Sales</strong></div>
				<div class="panel-body">
					<div class="col-md-6">
						<div id="salesGraphLabel" ></div>
					</div>
					<div id="salesGraph" class='col-md-6' style='height:400px;'></div>

				</div>
			</div>
			<br/>
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Top Items Base on Qty</strong></div>
				<div class="panel-body">
					<div id="salesGraph2" class='col-md-6' style='height:400px;'></div>
					<div class="col-md-6">
						<div id="salesGraph2Label" ></div>
					</div>
				</div>
			</div>
			<br/>
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Sales for the past ten days</strong></div>
				<div class="panel-body">
					<div id="salesPastTenDays" class='col-md-12' style='height:400px;'></div>
				</div>
			</div>
			<br/>
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Best Cashier</strong></div>
				<div class="panel-body">
					<div class="col-md-4">
						<br/><br/><br/>
						<div id="cashierLabel" ></div>
					</div>

					<div id="cashierGraph" class='col-md-8' style='height:400px;'></div>

				</div>
			</div>
		</div>
		<div id="salesCon">

		</div>
		<div id="imagecon" >
			<img src="" alt="Image" />
		</div>
	</div>

</div> <!-- end page content wrapper-->

	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style='width:95%'>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="mtitle"></h4>
				</div>
				<div class="modal-body" id="mbody">
				</div>

			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style='width:95%;' >
			<div class="modal-content"  >
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id='ftitle'></h4>
				</div>
				<div class="modal-body" id='fbody'>
						<div class="row">

							<div class="col-md-12">

								<strong>Timeframe</strong>
							</div>
							<br>
							<div class="col-md-4">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon" id="basic-addon1"><i class='fa fa-calendar-o'></i></span>
										<input type="text" name='dateStart' class='form-control' id='dateStart' placeholder='Date Start' />
									</div>
									<span class='help-block'>Enter start date</span>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
								<div class="input-group">
									<span class="input-group-addon" id="basic-addon1"><i class='fa fa-calendar-o'></i></span>
									<input type="text" name='dateEnd' class='form-control' id='dateEnd' placeholder='Date End' />
								</div>
									<span class='help-block'>Enter end date</span>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<select name="date_type" id="date_type" class='form-control'>
										<option value="0">All Status</option>
										<option value="1">Delivered and Picked Up Only</option>
									</select>
									<span class='help-block'>Date type</span>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
								<select id="sales_type" name="sales_type" class="form-control" multiple>
									<option value=""></option>
									<?php
										if(Configuration::isAquabest()){
													?>
									<option value="-1">Caravan</option>
									<?php } ?>
									<?php
										$salestype = new Sales_type();
										$salestypes = $salestype->get_active('salestypes',array('company_id','=',$user->data()->company_id));
										foreach ($salestypes as $st):
											?>
											<option value='<?php echo $st->id ?>'><?php echo $st->name ?> </option>
										<?php
										endforeach;
									?>
								</select>
									<span class='help-block'>Choose sales type</span>
									</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<select name="from_od" id="from_od" class='form-control'>
										<option value="0">-Select Transaction type-</option>
										<option value="1">Walk In</option>
										<option value="2">From Order</option>
									</select>
									<span class='help-block'>Walk in or delivery</span>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<select name="with_serial" id="with_serial" class='form-control'>
										<option value="0">-Select item type-</option>
										<option value="1">With serial</option>
										<option value="2">Without serial</option>
									</select>
									<span class='help-block'>With or without serial</span>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<select name="from_service" id="from_service" class='form-control'>
										<option value="0">-Main Or Service-</option>
										<option value="1">Main Item</option>
										<option value="2">Service Item</option>
									</select>
									<span class='help-block'>Choose Item Type</span>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<select name="doc_type" id="doc_type" class='form-control'>
										<option value="0">All</option>
										<option value="1"><?php echo INVOICE_LABEL; ?> only</option>
										<option value="2"><?php echo DR_LABEL; ?>  only</option>
										<option value="3"><?php echo PR_LABEL; ?> only</option>
									</select>
									<span class='help-block'>Document type</span>
								</div>
							</div>
							<div class="col-md-4">
								<select id="release_branch_id" name="release_branch_id" class="form-control" >
									<option value="">Select Branch</option>
									<?php
										foreach($branches as $b){

											?>
											<option value='<?php echo $b->id ?>'><?php echo $b->name;?> </option>
											<?php
										}
									?>
								</select>
								<span class='help-block'>Release from</span>
							</div>

						</div>

							<hr>
						<div class="row">
							<div class="col-md-6">
								<strong>Payment Type</strong>
							</div>
							<div class="col-md-6 text-right">
								<input type="radio" name="rdstrict" value='1' id='rdstrict1' /> <label for='rdstrict1'>Strict</label>
								<input type="radio" name="rdstrict" value='0' id='rdstrict0' checked/> <label for='rdstrict0'>Not Strict</label>
							</div>
							<br>
							<div class="row">
								<div class="col-md-3">
									<input type='checkbox' style='margin:5px;' class='chkPayment' name='chkPaymentType' value='5' id='chkAll'> <label for='chkAll'>All</label>
								</div>
								<div class="col-md-3">
								<input type='checkbox' style='margin:5px;' class='chkPayment' name='chkPaymentType' value='1' id='chkCash'> <label for='chkCash'>Cash </label>
								</div>
								<div class="col-md-3">
								<input type='checkbox' style='margin:5px;' class='chkPayment' name='chkPaymentType' value='2' id='chkCheck'> <label for='chkCheck'>Check </label>
								</div>
								<div class="col-md-3">
								<input type='checkbox' style='margin:5px;' class='chkPayment' name='chkPaymentType' value='3' id='chkCreditCard'> <label for='chkCreditCard'> Credit Card</label>
								</div>
								<div class="col-md-3">
								<input type='checkbox' style='margin:5px;' class='chkPayment' name='chkPaymentType' value='4' id='chkBankTransfer'> <label for='chkBankTransfer'> Bank Transfer</label>
								</div>
								<div class="col-md-3">
								<input type='checkbox' style='margin:5px;' class='chkPayment' name='chkPaymentType' value='6' id='chkConAmount'> <label for='chkConAmount'> Consumable Amount</label>
								</div>
								<div class="col-md-3">
								<input type='checkbox' style='margin:5px;' class='chkPayment' name='chkPaymentType' value='7' id='chkConFreebies'> <label for='chkConFreebies'> Consumable Freebies</label>
								</div>
								<div class="col-md-3">
									<input type='checkbox' style='margin:5px;' class='chkPayment' name='chkPaymentType' value='8' id='chkMemberCredit'> <label for='chkMemberCredit'> <?php echo MEMBER_LABEL; ?> Credit</label>
								</div>
								<div class="col-md-3">
									<input type='checkbox' style='margin:5px;' class='chkPayment' name='chkPaymentType' value='9' id='chkDeduction'> <label for='chkDeduction'> Deduction </label>
								</div>
								</div>
						</div>
						<hr>
						<div class="row">
							<div class="col-md-12">
								<strong>Branch and Terminals</strong>
							</div>
							<br/>
							<div class="col-md-6">

									<select id="branch_id" name="branch_id" class="form-control" multiple>

										<?php
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
							<div class="col-md-6">
									<div id="terminalholder">
										<p class='text-info'>Please choose branch first</p>
									</div>
							</div>
						</div>	
						<hr>
						<div class="row">
							<div class="col-md-12"><strong>Cashiers, Members and Stations</strong></div>
							<br/>
							<div class="row">
							<div class="col-md-4">
								<div class="form-group">
								<select id='cashier_id' class='form-control' multiple>
									<option></option>
									<?php

										$allusers = $user->get_active('users',array('company_id','=',$user->data()->company_id));
										foreach ($allusers as $m):
											?>
											<option value='<?php echo $m->id ?>'><?php echo $m->lastname . ", " . $m->firstname . " " . $m->middlename ?> </option>
										<?php
										endforeach;
									?>
								</select>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
										<input id='select_member' class='form-control' >

									</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
								<div id="stationholder">
								</div>
								</div>
							</div>
							</div>
							<div class="row">
							<div class="col-md-4">
								<div class="form-group">
								<select class='form-control' id='region' name='region' multiple>
									<option value=''>--Select Region--</option>
									<option value='NCR'>NCR</option>
									<option value='CAR'>CAR</option>
									<option value='REGION I'>REGION I</option>
									<option value='REGION II'>REGION II</option>
									<option value='REGION III'>REGION III</option>
									<option value='REGION IV-A'>REGION IV-A</option>
									<option value='REGION IV-B'>REGION IV-B</option>
									<option value='REGION V'>REGION V</option>
									<option value='REGION VI'>REGION VI</option>
									<option value='REGION VII'>REGION VII</option>
									<option value='REGION VIII'>REGION VIII</option>
									<option value='REGION IX'>REGION IX</option>
									<option value='REGION X'>REGION X</option>
									<option value='REGION XI'>REGION X</option>
									<option value='REGION XII'>REGION XII</option>
									<option value='REGION XIII'>REGION XIII</option>
									<option value='ARMM'>ARMM</option>
								</select>
									</div>
							</div>
							<div class="col-md-8" id='regionHolder' style='display:none;'>
								<div class="form-group">
									<div class='row'>
										<div class='col-md-4'>
								REGION: &nbsp; &nbsp;
											</div>
										<div class='col-md-4'>
								<input type="radio" name="rdRegion" value='member' id='rdRegion1' checked/> <label for='rdRegion1'>Base on Member</label>
										</div>
										<div class='col-md-4'>
											<input type="radio" name="rdRegion" value='station' id='rdRegion2' /> <label for='rdRegion2'>Base on Station</label>
								</div>	</div>
							</div>
						</div>
								</div>
						<hr>
						<div class="row">
						
								<div class="col-md-12">
									<strong>Item Type and Category</strong>
								</div>

								<div class="col-md-4">
									<div class="form-group">
									<select name="item_type" id="item_type" class='form-control' multiple>
												<option value=""></option>
												<option value="-1">With Inventory</option>
												<option value="1">Without Inventory</option>
												<option value="2">Consumable By Quantity</option>
												<option value="4">Consumable Amount</option>
												<option value="3">Subscription</option>
									</select>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<select id="category_id" name="category_id" class="form-control " multiple>
												<option value=""></option>
												<?php echo get_nested(makeRecursive($cc)); ?>
										</select>
									</div>
								</div>
							</div>

							<div class='row'>
								<div class="col-md-4">
									<div class="form-group">
										<input type="text" id='item_id' name='item_id' class='form-control'>
									</div>
								</div>
							<div class="col-md-4">
								<div class="form-group">
								<select id="char_id" name="char_id" class="form-control " multiple>
									<option value=""></option>
									<?php
										$char = new Characteristics();
										$chars = $char->get_active('characteristics', array('company_id', '=', $user->data()->company_id));
										if($chars){
												foreach($chars as $c){
													?>
													<option value="<?php echo $c->id; ?>"><?php echo $c->name; ?></option>
													<?php
												}
										}

									?>
								</select>
							</div>
							</div>
						</div>
							<div class="row">
								<div class="col-md-8">
									<div class="form-group">
									<strong>Custom Query:</strong> <input type="text" class='form-control' id='custom_string_query' placeholder='Enter Search String'>
									</div>
								</div>
							</div>

				</div>
				<div class="modal-footer">
					<p id='chkIncludeCancelCon'> <input type="checkbox" id='chkIncludeCancel'> <label for="chkIncludeCancel">Include Cancel Transaction</label> </p>
					<button type="button" title='Close' class="btn btn-default" data-dismiss="modal"><i class='fa fa-close'></i> <span class='hidden-xs'>Close</span></button>
					<button type="button" title='Apply' class="btn btn-default" id='applyFilter'><i class='fa fa-filter'></i><span class='hidden-xs'>Apply</span></button>

				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->



<script type="text/javascript">
$(function(){
	var MEMBER_LABEL = $('#MEMBER_LABEL').val();
	// navigation
	$('#btnHome').click(function(){
		$('#whereami').val(1);
		$('#homeCon').fadeIn();
		$('#graphCon').hide();
		$('#salesCon').hide();
	});
	$('#btnSales').click(function(){
		$('#homeCon').hide();
		$('#graphCon').hide();
		$('#salesCon').fadeIn();
		$('#whereami').val(3);
		var dateStart =  $("#dateStart").val();
		var dateEnd =  $("#dateEnd").val();
		var branch = $("#branch_id").val();
		salesBreakDown(dateStart,dateEnd,branch);
	});
	$('#btnGraph').click(function(){
		$('#whereami').val(2);
		$('#homeCon').hide();
		$('#graphCon').fadeIn();
		$('#salesCon').hide();
		var dateStart =  $("#dateStart").val();
		var dateEnd =  $("#dateEnd").val();
		topBranch(dateStart,dateEnd);
		salesTypeTotal(dateStart,dateEnd);
		topItemBaseOnSales(dateStart,dateEnd);
		topItemBaseOnQty(dateStart,dateEnd);
		getPast10();
		topCashier(dateStart,dateEnd);

	});
	function salesBreakDown(dt1,dt2,branch){
		var parray = [];
		var sales_type = $("#sales_type").val();
		$('input:checkbox.chkPayment').each(function () {
			var sThisVal = (this.checked ? $(this).val() : "");
			if(sThisVal){
				parray.push(sThisVal);
			}
		});
		var payment_method = '';
		if(parray.length > 0){
			 payment_method = parray;
		}

		$.ajax({
			url:'../ajax/ajax_query.php',
			type:'post',
			beforeSend: function(){
				$('#salesCon').html('<h3 class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</h3>');
			},
			data: {functionName:'salesBreakDown',dt1:dt1,dt2:dt2,branch:branch,payment_method:payment_method,sales_type:sales_type},
			success: function(data){
				$('#salesCon').html('');
				$('#salesCon').html(data);
			},
			error:function(){

			}
		});
	}
	function topBranch(dt1 , dt2){
		$.ajax({
		    url:'../ajax/ajax_query.php',
		    type:'post',
			dataType:'json',
			beforeSend: function(){
			$('#myfirstchart').html('<h3 class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</h3>');
			},
		    data: {functionName:'topBranch',dt1:dt1,dt2:dt2,type:1},
		    success: function(data){
			    $('#myfirstchart').html('');
			    if (data.error){
				    $('#myfirstchart').html('No data found.');
			    } else {
				    var a =0;
				    Morris.Donut({
					    element: 'myfirstchart',
					    data: data,
					    formatter: function (value, data) {
						    return "\n" + number_format(value,2);
					    }
				    });
			    }

		    },
		    error:function(){

		    }
		});
		$.ajax({
			url:'../ajax/ajax_query.php',
			type:'post',
			data: {functionName:'topBranch',dt1:dt1,dt2:dt2,type:2},
			success: function(data){
				$('#myfirstchartlabel').html(data);
			},
			error:function(){

			}
		});
	}
	function salesTypeTotal(dt1 , dt2){
		$.ajax({
			url:'../ajax/ajax_query.php',
			type:'post',
			dataType:'json',
			beforeSend: function(){
				$('#salestypegraph').html('<h3 class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</h3>');
			},
			data: {functionName:'salesTypeTotal',dt1:dt1,dt2:dt2,type:1},
			success: function(data){
				$('#salestypegraph').html('');
				if (data.error){
					$('#salestypegraph').html('No data found.');
				} else {
					var a =0;
					Morris.Donut({
						element: 'salestypegraph',
						data: data,
						formatter: function (value, data) {
							return "\n" + number_format(value,2);
						}
					});
				}

			},
			error:function(){

			}
		});
		$.ajax({
			url:'../ajax/ajax_query.php',
			type:'post',
			data: {functionName:'salesTypeTotal',dt1:dt1,dt2:dt2,type:2},
			success: function(data){
				$('#salestypegraphlabel').html(data);
			},
			error:function(){

			}
		});
	}
	function topCashier(dt1 , dt2){
		$.ajax({
			url:'../ajax/ajax_query.php',
			type:'post',
			dataType:'json',
			data: {functionName:'topCashier',dt1:dt1,dt2:dt2,type:1},
			success: function(data){
				$('#cashierGraph').html('');
				if (data.error){
					$('#cashierGraph').html('No data found');
				} else {
					Morris.Donut({
						element: 'cashierGraph',
						data: data,
						formatter: function (value, data) {
							return "\n" + number_format(value,2);
						}
					});
				}

			},
			error:function(){

			}
		});
		$.ajax({
			url:'../ajax/ajax_query.php',
			type:'post',
			data: {functionName:'topCashier',dt1:dt1,dt2:dt2,type:2},
			success: function(data){
				$('#cashierLabel').html(data);
			},
			error:function(){

			}
		});
	}
	function topItemBaseOnSales(dt1 , dt2){
		$.ajax({
			url:'../ajax/ajax_query.php',
			type:'post',
			dataType:'json',
			data: {functionName:'topItemsSales',dt1:dt1,dt2:dt2,type:1},
			success: function(data){
				$('#salesGraph').html('');
				if (data.error){
					$('#salesGraph').html('No data found');
				} else {
				var a =0;
				Morris.Bar({
					element: 'salesGraph',
					data: data,
					xkey: 'y',
					ykeys: ['a'],
					labels: ['Sales'],
					xLabelAngle: 35,
					padding: 40,
					hideHover: 'auto',
					barOpacity: 0.9,
					barRadius: [10, 10, 5, 5],
					barColors: function(row, series, type) {
						a = a + 1;
						if(a % 2 == 0) return "#B21516"; else return "#1531B2";
					},
					hoverCallback: function(index, options, content) {
						var data = options.data[index];
						return("<p> "+data.y + "<br><span class='text-danger'> P. " + number_format(data.a,2) +"</span></p>");
					}
				});
				}
			},
			error:function(){

			}
		});
		$.ajax({
			url:'../ajax/ajax_query.php',
			type:'post',
			data: {functionName:'topItemsSales',dt1:dt1,dt2:dt2,type:2},
			success: function(data) {
				$('#salesGraphLabel').html(data);
			},
			error:function(){

			}
		});
	}
	function topItemBaseOnQty(dt1 , dt2) {
		$.ajax({
			url: '../ajax/ajax_query.php',
			type: 'post',
			dataType: 'json',
			data: {functionName: 'topItemsQty', dt1: dt1, dt2: dt2, type: 1},
			success: function(data) {
				$('#salesGraph2').html('');
				if (data.error){
					$('#salesGraph2').html('No data found');
				} else {
				var a = 0;
				Morris.Bar({
					element: 'salesGraph2',
					data: data,
					xkey: 'y',
					ykeys: ['a'],
					labels: ['Qty'],
					xLabelAngle: 35,
					padding: 40,
					hideHover: 'auto',
					barOpacity: 0.9,
					barRadius: [10, 10, 5, 5],
					barColors: function(row, series, type) {
						a = a + 1;
						if(a % 2 == 0) return "#B21516"; else return "#1531B2";
					},
					hoverCallback: function(index, options, content) {
						var data = options.data[index];
						return("<p> "+data.y + "<br><span class='text-danger'> " + number_format(data.a) +"</span></p>");
					}
				});
				}
			},
			error: function() {

			}
		});
		$.ajax({
			url: '../ajax/ajax_query.php',
			type: 'post',
			data: {functionName: 'topItemsQty', dt1: dt1, dt2: dt2, type: 2},
			success: function(data) {
				$('#salesGraph2Label').html(data);
			},
			error: function() {

			}
		});
	}
	$('#filter').click(function(){
		$('#filterModal').modal('show');
	});
	$(".chkPayment").change(function(){
		var checkitem = $(this).val();
		if(checkitem == '5'){
			$(".chkPayment").not('#chkAll').attr('checked',false);
		} else {
			$("#chkAll").attr('checked',false);
		}
	});

	$("#branch_id").select2({
		placeholder: 'Choose Branch',
		allowClear: true
	});
	$("#region").select2({
		placeholder: 'Choose Region',
		allowClear: true
	});
	$("#sales_type").select2({
		placeholder: 'Choose Sales Type',
		allowClear: true
	});
	$("#select_member").select2({
		placeholder: 'Search ' + MEMBER_LABEL,
		allowClear: true,
		multiple: true,
		minimumInputLength: 2,
		ajax: {
			url: '../ajax/ajax_json.php',
			dataType: 'json',
			type: "POST",
			quietMillis: 50,
			data: function (term) {
				return {
					q: term,
					functionName:'members'
				};
			},
			results: function (data) {
				return {
					results: $.map(data, function (item) {
						return {
							text: item.lastname + ", " + item.firstname + " " + item.middlename,
							slug: item.lastname + ", " + item.firstname + " " + item.middlename,
							id: item.id
						}
					})
				};
			}
		}
	});
	$("#cashier_id").select2({
		placeholder: 'Choose cashier',
		allowClear: true
	});
	$("#station_id").select2({
		placeholder: 'Choose Stations',
		allowClear: true
	});
	$("#item_type").select2({
				placeholder: 'Choose Item Type',
				allowClear: true
	});
	$("#category_id").select2({
		placeholder: 'Choose Category',
		allowClear: true
	});
	$("#char_id").select2({
		placeholder: 'Choose Characteristics',
		allowClear: true
	});
	function formatItem(o) {
		if (!o.id)
			return o.text; // optgroup
		else {
			var r = o.text.split(':');
			return "<span> "+r[0]+"</span> <span style='margin-left:10px'>" + r[1] + "</span><span style='display:block;margin-top:5px;'  class='text-danger'><small class='testspanclass'>"+r[2]+"</small></span>";
		}
	}
	$("#item_id").select2({
		placeholder: 'Item code',
		allowClear: true,
		minimumInputLength: 2,
		multiple: true,
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

	$('#region').change(function(){
		var region = $(this).val();
		if(region){
			$('#regionHolder').fadeIn();
		} else {
			$('#regionHolder').fadeOut();
		}

	});
	$("#branch_id").change(function(){
		var bid = $(this).val();

		$.ajax({
			url:'../ajax/ajax_query.php',
			type:'post',
			data:{functionName:'getTerminals',branch_id:bid},
			success: function(data){
				$('#terminalholder').html(data);
			},
			error: function(){

			}
		});
	});

	$("#select_member").change(function(){
		var mid = $(this).val();
		 mid = returnArrayVal(',',mid);
		if(mid){
			$.ajax({
				url:'../ajax/ajax_query.php',
				type:'post',
				data:{functionName:'getStations',member_id:mid},
				success: function(data){
					$('#stationholder').html(data);
				},
				error: function(){

				}
			});
		} else {
			$('#stationholder').html('');
		}

	});
		function returnArrayVal(delimeter,s){
			if(s){
				var arr = [];
				if(s.indexOf(delimeter) > 0){
					arr = s.split(delimeter);
				} else {
					arr.push(s);
				}
				return arr;
			}
		}

 		$('#dateStart').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dateStart').datepicker('hide');
		});

		$('#dateEnd').datepicker({
				autoclose:true
		}).on('changeDate', function(ev){
			$('#dateEnd').datepicker('hide');
		});

		$('#btnDownloadExcel').click(function(){
			var rdStrict = $('input[name=rdstrict]:checked').val();
			var page = $(this).attr('page');
			$('#hiddenpage').val(page);
			// branch , Terminal,
			var branch = $("#branch_id").val();
			var terminal = $("#terminal_id").val();
			// payment method
			var parray = [];

			$('input:checkbox.chkPayment').each(function () {
				var sThisVal = (this.checked ? $(this).val() : "");
				if(sThisVal){
					parray.push(sThisVal);
				}
			});

			if(parray.length > 0){
				var payment_method = parray;
			} else {
				payment_method='';
			}

			//item_type,category,char
			var item_type = $("#item_type").val();
			var category = $("#category_id").val();
			var char = $("#char_id").val();
			var item_id = $("#item_id").val();
			var sales_type = $("#sales_type").val();
			//member
			var member = $("#select_member").val();
			var station = $('#station_id').val();
			var dateStart =  $("#dateStart").val();
			var dateEnd =  $("#dateEnd").val();
			var cashier = $("#cashier_id").val();
			var rdStrict = $('input[name=rdstrict]:checked').val();
			var rdRegion = $('input[name=rdRegion]:checked').val();
			var region = $('#region').val();
			var report_type = $('#report_type').val();

			var sortby = $('#sort_by').val();
			var ascdesc = $('#ascdesc').val();

			if(!ascdesc) {
				if(sortby) sortby = sortby + 'asc';
			} else {
				if(sortby)  sortby = sortby + 'desc';
			}
			getDownload(report_type,sortby,branch,terminal,payment_method,item_type,category,member,station,dateStart,dateEnd,cashier,rdStrict,char,item_id,sales_type,region,rdRegion);

		});
		// getting reports...
		$('#applyFilter').click(function(){
				var rdStrict = $('input[name=rdstrict]:checked').val();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				// branch , Terminal,
				var branch = $("#branch_id").val();
				var terminal = $("#terminal_id").val();
				// payment method
				var parray = [];
			var whereami = 	$('#whereami').val();
				$('input:checkbox.chkPayment').each(function () {
		       	var sThisVal = (this.checked ? $(this).val() : "");
		       	if(sThisVal){
		       		parray.push(sThisVal);
		       	}
		  		});
				if(parray.length > 0){
					var payment_method = parray;
				} else {
					payment_method='';
				}

				//item_type,category,char
				var item_type = $("#item_type").val();
				var category = $("#category_id").val();
				var char = $("#char_id").val();
				var item_id = $("#item_id").val();
				var sales_type = $("#sales_type").val();
				//member
				var member = $("#select_member").val();
				var station = $('#station_id').val();
				var dateStart =  $("#dateStart").val();
				var dateEnd =  $("#dateEnd").val();
				var cashier = $("#cashier_id").val();
				var rdStrict = $('input[name=rdstrict]:checked').val();
				var rdRegion = $('input[name=rdRegion]:checked').val();
				var region = $('#region').val();
				var report_type = $('#report_type').val();
				var sortby = $('#sort_by').val();
				var ascdesc = $('#ascdesc').val();

				if(!ascdesc) {
					if(sortby) sortby = sortby + 'asc';
				} else {
					if(sortby)  sortby = sortby + 'desc';
				}
				if(whereami == 1){
					getPage(page,report_type,sortby,branch,terminal,payment_method,item_type,category,member,station,dateStart,dateEnd,cashier,rdStrict,char,item_id,sales_type,region,rdRegion);

				} else if(whereami == 2){
					topBranch(dateStart,dateEnd);
					salesTypeTotal(dateStart,dateEnd);
					topItemBaseOnSales(dateStart,dateEnd);
					topItemBaseOnQty(dateStart,dateEnd);
					getPast10();
					topCashier(dateStart,dateEnd);
				}  else if(whereami == 3){
					salesBreakDown(dateStart,dateEnd,branch);

				}

				//
				$('#filterModal').modal('hide');
		});
		getPage(0);
	$('body').on('click','.page_sortby',function(e){
		e.preventDefault();
		var sortlabel = $(this).attr('data-sort');
		$('#sort_by').val(sortlabel);
		var page = $(this).attr('page');
		var rdStrict = $('input[name=rdstrict]:checked').val();
		$('#hiddenpage').val(page);
		// branch , Terminal,
		var branch = $("#branch_id").val();
		var terminal = $("#terminal_id").val();
		// payment method
		var parray = [];
		$('input:checkbox.chkPayment').each(function () {
			var sThisVal = (this.checked ? $(this).val() : "");
			if(sThisVal){
				parray.push(sThisVal);
			}
		});
		if(parray.length > 0){
			var payment_method = parray;
		} else {
			payment_method='';
		}
		//item_type,category,char
		var item_type = $("#item_type").val();
		var category = $("#category_id").val();
		var char = $("#char_id").val();
		var item_id = $("#item_id").val();
		var sales_type = $("#sales_type").val();
		//member
		var member = $("#select_member").val();
		var station = $('#station_id').val();
		var dateStart =  $("#dateStart").val();
		var dateEnd =  $("#dateEnd").val();
		var cashier = $("#cashier_id").val();
		var rdStrict = $('input[name=rdstrict]:checked').val();
		var rdRegion = $('input[name=rdRegion]:checked').val();
		var region = $('#region').val();
		var report_type = $('#report_type').val();
		var sortby = $('#sort_by').val();
		var ascdesc = $('#ascdesc').val();
		if(ascdesc) {
			if(sortby) sortby = sortby + 'asc';
			$('#ascdesc').val('');
		} else {
			if(sortby) sortby = sortby + 'desc';
			$('#ascdesc').val(1);
		}
		getPage(page,report_type,sortby,branch,terminal,payment_method,item_type,category,member,station,dateStart,dateEnd,cashier,rdStrict,char,item_id,sales_type,region,rdRegion);
	});
		$('body').on('click','.paging',function(e){
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				// branch , Terminal,
				var branch = $("#branch_id").val();
				var terminal = $("#terminal_id").val();
				// payment method
				var parray = [];
				$('input:checkbox.chkPayment').each(function () {
		       	var sThisVal = (this.checked ? $(this).val() : "");
		       	if(sThisVal){
		       		parray.push(sThisVal);
		       	}
		  		});
				if(parray.length > 0){
					var payment_method = parray;
				} else {
					payment_method='';
				}
				//item_type,category,char
				var item_type = $("#item_type").val();
				var category = $("#category_id").val();
				var char = $("#char_id").val();
				var item_id = $("#item_id").val();
				var sales_type = $("#sales_type").val();
				//member
				var member = $("#select_member").val();
				var station = $('#station_id').val();
				var dateStart =  $("#dateStart").val();
				var dateEnd =  $("#dateEnd").val();
				var cashier = $("#cashier_id").val();
				var rdStrict = $('input[name=rdstrict]:checked').val();

				var rdRegion = $('input[name=rdRegion]:checked').val();
				var region = $('#region').val();
				var report_type = $('#report_type').val();
				var sortby = $('#sort_by').val();
				var ascdesc = $('#ascdesc').val();

				if(!ascdesc) {
					if(sortby) sortby = sortby + 'asc';
				} else {
					if(sortby) sortby = sortby + 'desc';
				}
				getPage(page,report_type,sortby,branch,terminal,payment_method,item_type,category,member,station,dateStart,dateEnd,cashier,rdStrict,char,item_id,sales_type,region,rdRegion);
		});

		function getPage(p,report_type,sort_by,branch,terminal,payment_method,item_type,category,member,station,dateStart,dateEnd,cashier,rdStrict,char,item_id,sales_type,region,rdRegion){
			item_id = (item_id) ? item_id.split(',') : [];

			member = returnArrayVal(',',member);
			var filterlist = "<div class='row'>";
			if(!report_type) report_type = 1;
				if(dateStart && dateEnd){
					filterlist += "<div  class='col-md-3'>From:<span style='display:block;' class='text-danger'>"+dateStart+" To "+dateEnd+"</span></div>";
				}
				if(sales_type){
					var select2data = $("#sales_type").select2('data');
					var select2Salestype = "";
					for(var s in select2data){
						select2Salestype += " <span class='' style='margin-left:3px;display:block;'>"+select2data[s].text+"</span> ";
					}
					filterlist += "<div  class='col-md-3'>Sales type: <span class='text-danger'>"+select2Salestype+"</span></div>";
				}
				if(payment_method){
					var parr = ["","Cash","Check","Credit Card","Bank Transfer","All","Consumable amount","Freebies",MEMBER_LABEL +" Credit"];
					var pmethodCheck = "";
					for(var pm in payment_method){
						var vcheck = payment_method[pm];
						pmethodCheck += " <span class='' style='margin-left:3px;display:block;'>"+parr[vcheck]+"</span> ";
					}
					filterlist += "<div  class='col-md-3'>Payment Method: <span class='text-danger'>"+pmethodCheck+"</span></div>";
				}
				if(branch){
					var select2branch = $("#branch_id").select2('data');
					var datalist = "";
					for(var s_b in select2branch){
						datalist += " <span class='' style='margin-left:3px;display:block;'>"+select2branch[s_b].text+"</span> ";
					}
					filterlist += "<div  class='col-md-3'>Branch:<span class='text-danger'> "+datalist+"</span></div>";
				}
				if(terminal){
					var select2terminal = $("#terminal_id").select2('data');
					var datalistTerminal = "";
					for(var s_t in select2terminal){
						datalistTerminal += " <span class='' style='margin-left:3px;display:block;'>"+select2terminal[s_t].text+"</span> ";
					}
					filterlist += "<div  class='col-md-3'>Terminal:<span class='text-danger'>"+datalistTerminal+"</span></div>";
				}
			if(cashier){
				var select2cashier = $("#cashier_id").select2('data');
				var datalistCashier = "";
				for(var s_c in select2cashier){
					datalistCashier += " <span class='' style='margin-left:3px;display:block;'>"+select2cashier[s_c].text+"</span> ";
				}
				filterlist += "<div  class='col-md-3'>Cashier: <span class='text-danger'>"+datalistCashier+"</span></div>";
			}
			//select_member
			if(member){
				var select2member = $("#select_member").select2('data');
				var datalistmember = "";
				for(var s_m in select2member){
					datalistmember += " <span class='' style='margin-left:3px;display:block;'>"+select2member[s_m].text+"</span> ";
				}
				filterlist += "<div  class='col-md-3'>Member:<span class='text-danger'> "+datalistmember+"</span></div>";
			}
			if(station){
				var select2station = $("#station_id").select2('data');
				var dataliststation = "";
				for(var s_s in select2station){
					dataliststation += " <span class='' style='margin-left:3px;display:block;'>"+select2station[s_s].text+"</span> ";
				}
				filterlist += "<div  class='col-md-3'>Station:<span class='text-danger'> "+dataliststation+"</span></div>";
			}
			if(region){
				var select2region = $("#region").select2('data');
				var datalistregion = "";
				for(var s_r in select2region){
					datalistregion += " <span class='' style='margin-left:3px;display:block;'>"+select2region[s_r].text+"</span> ";
				}
				filterlist += "<div  class='col-md-3'>Region:<span class='text-danger'>"+dataliststation+"</span></div>";
			}
			var from_od = $('#from_od').val();
			if(from_od == 1){
				filterlist += "<div  class='col-md-3'>Type :<span class='text-danger'>Walk In</span></div>";
			} else if(from_od == 2){
				filterlist += "<div  class='col-md-3'>Type :<span class='text-danger'>From Order</span></div>";
			}
			if(filterlist){
				filterlist += "</div>";
				$('#filters').html("<br><p><strong>Filters</strong></p>"+filterlist+"");
			}
			var with_serial = $('#with_serial').val();
			var from_service = $('#from_service').val();
			var doc_type = $('#doc_type').val();
			var custom_string_query = $('#custom_string_query').val();
			var release_branch_id = $('#release_branch_id').val();
			var date_type = $('#date_type').val();
			var include_cancel = 0;
			if ($('#chkIncludeCancel').is(':checked')){
				include_cancel = 1;
			}

			$.ajax({
				url: '../ajax/ajax_paging.php',
				type:'post',
				beforeSend: function(){
					$('#holder').html("Fetching records...");
				},
				data:{include_cancel:include_cancel,release_branch_id:release_branch_id,date_type:date_type,custom_string_query:custom_string_query,with_serial:with_serial,doc_type:doc_type,from_service:from_service,page:p,functionName:'r2Pagination',cid: <?php echo $user->data()->company_id; ?>,branch:branch,terminal:terminal,payment_method:payment_method,item_type:item_type,category:category,member:member,station:station,dateStart:dateStart,dateEnd:dateEnd,cashier:cashier,rdStrict:rdStrict,char:char,item_id:item_id,sales_type:sales_type,region:region,rdRegion:rdRegion,report_type:report_type,sort_by:sort_by,from_od:from_od},
				success: function(data){
					$('#holder').html(data);
					$('.loading').hide();
				},
				error:function(){
					alert('Something went wrong. The page will be refresh.');
					location.href='reports2.php';
					$('.loading').hide();
				}
			});

		}

	function getDownload(report_type,sort_by,branch,terminal,payment_method,item_type,category,member,station,dateStart,dateEnd,cashier,rdStrict,char,item_id,sales_type,region,rdRegion){
		if(!report_type) report_type = 1;
		branch = JSON.stringify(branch);
		terminal = JSON.stringify(terminal);
		payment_method = JSON.stringify(payment_method);
		item_type = JSON.stringify(item_type);
		category = JSON.stringify(category);
		member = returnArrayVal(',',member);
		member = JSON.stringify(member);
		station = JSON.stringify(station);
		cashier = JSON.stringify(cashier);
		char = JSON.stringify(char);
		item_id = JSON.stringify(item_id);
		sales_type = JSON.stringify(sales_type);
		region = JSON.stringify(region);
		var from_od = $('#from_od').val();
		var from_service = $('#from_service').val();
		var doc_type = $('#doc_type').val();
		var custom_string_query = $('#custom_string_query').val();
		var release_branch_id = $('#release_branch_id').val();
		var date_type = $('#date_type').val();
		var include_cancel = 0;
		if ($('#chkIncludeCancel').is(':checked')){
			include_cancel = 1;
		}


		//console.log('excel_downloader.php?downloadName=reports&report_type='+report_type+'&sort_by='+sort_by+'&branch='+branch+'&terminal='+terminal+'&payment_method='+payment_method+'&item_type='+item_type+'&category='+category+'&member='+member+'&station='+station+'&dateStart='+dateStart+'&dateEnd='+dateEnd+'&cashier='+cashier+'&rdStrict='+rdStrict+'&char='+char+'&item_id='+item_id+'&sales_type='+sales_type+'&region='+region+'&rdRegion='+rdRegion+'&from_od='+from_od);
		window.open(
			'excel_downloader.php?downloadName=reports&report_type='+report_type+'&sort_by='+sort_by+'&branch='+branch+'&terminal='+terminal+'&payment_method='+payment_method+'&item_type='+item_type+'&category='+category+'&member='+member+'&station='+station+'&dateStart='+dateStart+'&dateEnd='+dateEnd+'&cashier='+cashier+'&rdStrict='+rdStrict+'&char='+char+'&item_id='+item_id+'&sales_type='+sales_type+'&region='+region+'&rdRegion='+rdRegion+'&from_od='+from_od+'&from_service='+from_service+'&doc_type='+doc_type+'&custom_string_query='+custom_string_query+'&release_branch_id='+release_branch_id+'&date_type='+date_type+'&include_cancel='+include_cancel,
			'_blank' // <- This is what makes it open in a new window.
		);

	}


	$("body").on('click','.paymentDetails',function(){
		var payment_id = $(this).attr('data-payment_id');
		$.ajax({
			url: '../ajax/ajax_paymentDetails.php',
			type: 'POST',
			beforeSend: function(){
				$('#right-pane-container').html('Fetching record. Please wait.');
			},
			data: {id:payment_id},
			success: function(data){
				$('#right-pane-container').html(data);
				$('.right-panel-pane').fadeIn(100);
			}
		});
	});

	function getPast10(){
		$.ajax({
		    url:'../ajax/ajax_query.php',
		    type:'post',
			dataType:'json',
		    data: {functionName:'getPast10'},
		    success: function(data){
			    $('#salesPastTenDays').html('');
			    Morris.Line({
				    element: 'salesPastTenDays',
				    data: data,
				    xkey: 'y',
				    ykeys: ['a'],
				    labels: ['Sales'],
				    xLabelAngle: 35,
				    padding: 40,
				    parseTime: false
			    });
		    },
		    error:function(){

		    }
		});
	}

	function number_format(number, decimals, dec_point, thousands_sep) {
		number = (number + '')
			.replace(/[^0-9+\-Ee.]/g, '');
		var n = !isFinite(+number) ? 0 : +number,
			prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
			sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
			dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
			s = '',
			toFixedFix = function(n, prec) {
				var k = Math.pow(10, prec);
				return '' + (Math.round(n * k) / k)
					.toFixed(prec);
			};
		// Fix for IE parseFloat(0.55).toFixed(0) = 0;
		s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
			.split('.');
		if (s[0].length > 3) {
			s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
		}
		if ((s[1] || '')
				.length < prec) {
			s[1] = s[1] || '';
			s[1] += new Array(prec - s[1].length + 1)
				.join('0');
		}
		return s.join(dec);
	}
	$('body').on('click','#btnPrintDiv',function(){
			Popup($('#printablediv').html());
	});
	$('#btnPerTransaction').click(function(){
			$('#report_type').val(2);
			getPage(0,2);
	});
	$('#btnPerItem').click(function(){
		$('#report_type').val(1);
		getPage(0,1);
	});
	$('body').on('change','#doc_type',function(){
		getPage(0);
	});
	$('body').on('click','.getPTDetails',function(){
		var payment_id = $(this).attr('data-payment_id');
		$.ajax({
			url: '../ajax/ajax_query.php',
			type: 'POST',
			data: {id:payment_id,functionName:'getPTDetails'},
			success: function(data){

				$('#right-pane-container').html(data);
				$('.right-panel-pane').fadeIn(100);

			}
		});
	});
	function Popup(data)
	{
		var mywindow = window.open('', 'new div', '');

		/*optional stylesheet*/ mywindow.document.write('<link rel="stylesheet" href="pos/css/bootstrap.css" type="text/css" />');
		mywindow.document.write('<html><head><title></title><style>table {font-size:0.7em}</style>');
		mywindow.document.write('</head><body style="padding:0;margin:0;">');
		mywindow.document.write(data);
		mywindow.document.write('</body></html>');
		setTimeout(function(){
			mywindow.print();
			mywindow.close();
			return true;
		},1000);

	}

});


</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>