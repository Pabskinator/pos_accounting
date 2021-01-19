
<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('item_service_r')) {
		// redirect to denied page
		Redirect::to(1);
	}

	// get all branch base on company
	$branch = new Branch();
	$branches = $branch->get_active('branches', array('company_id', '=', $user->data()->company_id));
	$isAquabest = Configuration::isAquabest();
	$css_class= '';

	if($isAquabest){
		$css_class ='dNone';
	}

?>

	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Request
			</h1>
		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}
		?>
		<?php include 'includes/service_nav.php'; ?>
		<div class="row">
			<input type="hidden" id='isAqua' value='<?php echo ($isAquabest) ? 1 : 0; ?>'>
			<div class="col-md-3">
				<div class="form-group">
					<select name="bid" id="bid" class='form-control <?php echo $css_class; ?>'>
						<option value=""></option>
						<?php
							$branch = new Branch();
							$branches = $branch->get_active('branches', array('company_id', '=', $user->data()->company_id));
						?>
						<?php foreach($branches as $b): ?>
							<option value="<?php echo escape($b->id); ?>"><?php echo escape($b->name); ?></option>
						<?php endforeach; ?>
					</select>
					<span class="help-block"></span>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<input type="text" class='form-control <?php echo $css_class; ?>' id='txtInvoice' placeholder='Invoice'>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<input type="text" class='form-control <?php echo $css_class; ?>' id='txtDr'  placeholder='DR'>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<input type="text" class='form-control <?php echo $css_class; ?>' id='txtIr'  placeholder='PR'>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3">
				<div class="form-group">
					<select name="serviceType" id="serviceType" class='form-control'>
						<option value="1">Walk In</option>
						<?php
							if(!Configuration::thisCompany('cebuhiq')){
								?>
								<option value="2">Pullout</option>
								<?php
							}
						?>

						<option value="3">On site repair</option>
					</select>
				</div>
			</div>
			<div class="col-md-3" id='pulloutDateCon' style='display: none;'>
				<div class="form-group">
					<input id='txtPulloutDate' type="text" class='form-control' placeholder='Pull out Schedule'>
					<span class="help-block">If item will be pull out from customer</span>
				</div>
			</div>
			<div class="col-md-3" id='homeRepairDateCon' style='display: none;'>
				<div class="form-group">
					<input id='txtHomeDate' type="text" class='form-control' placeholder='On site repair Schedule'>
					<span class="help-block">Schedule of on site repair</span>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<input id='member_id' type="text" class='form-control'>
					<span class="help-block">Member name</span>
				</div>
			</div>
			<div class="col-md-3  <?php echo $css_class; ?>">
				<div class="form-group">
					<input id='technician_id' type="text" class='form-control'>
					<span class="help-block">You can add multiple technician</span>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<select name="service_type_id" id="service_type_id" class='form-control'>
						<option value=""></option>
						<?php
							$service_type = new Service_type();
							$service_types = $service_type->get_active('service_types', array('company_id', '=', $user->data()->company_id));
						?>
						<?php foreach($service_types as $st): ?>
							<option value="<?php echo escape($st->id); ?>"><?php echo escape($st->name); ?></option>
						<?php endforeach; ?>
					</select>
					<span class="help-block">Service Type</span>
				</div>
			</div>
		</div>

		<div class="row">
			<?php if(Configuration::thisCompany('avision')){
				?>
				<div class="col-md-3">
					<div class="form-group">
						<input  name="client_po" id="client_po" placeholder='PO Number' class='form-control' >
					</div>
				</div>
				<?php
			}?>

			<div class="col-md-3">
				<div class="form-group">
				<input style='display:none;' name="backload_ref_id" id="backload_ref_id" placeholder='Service Ref Number' class='form-control' >
				</div>
		    </div>
			<div class="col-md-3">
				<div class="form-group">
					<select style='display:none;' name="station_id" id="station_id" class='form-control'>

					</select>
				</div>
			</div>

			<div class="col-md-12">
				<div class="form-group">
					<textarea name="service_remarks" id="service_remarks" class='form-control' placeholder='Service Remarks'></textarea>
					<br>
				</div>
			</div>
			<?php
				if(Configuration::thisCompany('cebuhiq')){
					?>
					<div class="col-md-3">
						<div class="form-group">
							<input type="text" placeholder='Contact Person' class='form-control' id='contact_person'>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<input type="text" placeholder='Contact Number' class='form-control' id='contact_number'>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<input type="text" placeholder='Address' class='form-control' id='contact_address'>
						</div>
					</div>
					<?php
				}
			?>
		</div>
		<div class='row'>
			<div class="col-md-3">
				<div class="form-group">
					<input name="txtItem" id="txtItem" class='selectitem'>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<input name="txtQty" id="txtQty" class='form-control' placeholder='Quantity'>
				</div>
			</div>
			<div class="col-md-3" id='item_unit_container' style='display: none;'>
				<div class="form-group">
					<select class='form-control' name="item_unit" id="item_unit">

					</select>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<input name="txtRemarks" id="txtRemarks" class='form-control' placeholder='Item Remarks'>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<button class='btn btn-default' id='btnAdd'>Add Item</button>
				</div>
			</div>
		</div>
		<!-- End Row 1-->		<!-- Start Row 2 -->
		<div class="row">
			<div class="col-md-12">
				<br />
				<div id="no-more-tables">
					<table id='cart' class='table' style='font-size:1em'>
						<thead>
						<tr>
							<th>BARCODE</th>
							<th>ITEM CODE</th>
							<th>QTY</th>
							<th>REMARKS</th>
							<th></th>
						</tr>
						</thead>

						<tbody>

						</tbody>

					</table>
				</div>
			</div>
		</div>

		<!-- end of row 2-->		<!--  start of button row-->
		<div class="row">
			<div class="col-md-12 text-right">
				<input type="button" id='void' value='VOID' class='btn btn-danger' />
				<input type="button" id='save' value='SUBMIT' class='btn btn-success' />
			</div>

		</div>
		<br>
		<!-- end of button row-->

	</div>
	<!-- end page content wrapper-->

	<script>

		$(function() {

			$('#txtItem').change(function(){

				var id =  $(this).val();


				$.ajax({
					url:'../ajax/ajax_wh_order.php',
					type:'POST',
					dataType:'json',
					data: {functionName:'getUnits',item_id:id},
					success: function(data){
						if(data.length){

							$('#item_unit_container').show();
							var html = "<option value=''>Choose Unit</option>";

							for(var i in data){

								html += "<option value='"+data[i].qty+"'>"+data[i].unit_name+"</option>";

							}

							$('#item_unit').html(html);

						} else {
							$('#item_unit_container').hide();
							$('#item_unit').html("<option value=''>Choose Unit</option>");
						}
					},
					error:function(){
						$('#item_unit_container').hide();
						$('#item_unit').html("<option value=''>Choose Unit</option>");
					}
				});


			});

			$("#member_id").select2({
				placeholder: 'Search Member',
				allowClear: true,
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
									text: item.lastname + ", " + item.sales_type_name,
									slug: item.lastname + ", " + item.firstname + " " + item.middlename,
									id: item.id
								}
							})
						};
					}
				}
			});
			$("#technician_id").select2({
				placeholder: 'Search Technician',
				allowClear: true,
				minimumInputLength: 2,
				multiple:true,
				ajax: {
					url: '../ajax/ajax_json.php',
					dataType: 'json',
					type: "POST",
					quietMillis: 50,
					data: function (term) {
						return {
							q: term,
							functionName:'technicians'
						};
					},
					results: function (data) {
						return {
							results: $.map(data, function (item) {
								return {
									text: item.name,
									slug: item.name,
									id: item.id
								}
							})
						};
					}
				}
			});
			$('#txtPulloutDate').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#txtPulloutDate').datepicker('hide');
				$('#txtHomeDate').val('');
			});
			$('#txtHomeDate').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#txtHomeDate').datepicker('hide');
				$('#txtPulloutDate').val('');
			});
			noItemInCart();
			function noItemInCart() {
				if(!$("#cart tbody").children().length) {
					$("#cart tbody").append("<td  colspan='4' id='noitem' style='padding-top:10px;' ><span class='text-danger'>NO ITEMS IN CART</span></td>");
				}
			}
			var isAqua = $('#isAqua').val();
			if(isAqua != 1){
				$("#bid").select2({
					placeholder: 'Select a Branch'
				});
			}


			$("#service_type_id").select2({
				placeholder: 'Select Service Type'
			});
			$('body').on('click', '.removeItem', function() {
				$(this).parents('tr').remove();
				noItemInCart();
			});
			$('body').on('change','#service_type_id',function(){
				var txt= $('#service_type_id').select2('data').text;
				if(txt){
					txt = txt.trim();
					txt = txt.toLowerCase();
					if(txt == 'item service'){
						$('#backload_ref_id').show();
					} else if(txt == 'for credit memo'){
						$('#backload_ref_id').show();
					} else {
						$('#backload_ref_id').hide();
					}
				}
			});
			$('#void').click(function() {
				alertify.confirm("Are you sure you want to remove all items in cart?", function (asc) {
					if(asc){
						$("#cart").find("tr:gt(0)").remove();
						noItemInCart();
					}

				});
			});
			function removeNoItemLabel() {
				$("#noitem").remove();
			}

			$("#btnAdd").click(function() {

				var branch = $("#bid").val();
				var item_id = $("#txtItem").val();
				var remarks = $("#txtRemarks").val();
				var qty = $("#txtQty").val();
				var isoncart = false;
				var allqty = 0;

				$('#cart >tbody > tr').each(function(){
					var row_id = $(this).attr('id');
					if(row_id == item_id){
						isoncart = true;
						return;
					}
				});

				var rgx = /^\d+$/

				if(!rgx.test(qty) || qty == 0){
					$(this).val(1);
					tempToast('error','<p>Invalid quantity</p>','<h3>WARNING!</h3>');
					return;
				}

				if(isoncart){
					tempToast('error','<p>Item is already in cart</p>','<h3>WARNING!</h3>');
					return;
				}

				if( !item_id || !qty) {
					tempToast('error','<p>Please complete the form first</p>','<h3>WARNING!</h3>');
				} else {

					var optdata = $('#txtItem').select2('data');
					var item_code = optdata.text;
					var splitted = item_code.split(':');
					removeNoItemLabel();
					var item_bc = splitted[0];
					var item_price = splitted[3];
					var itemcode = splitted[1];
					var de = splitted[2];


					var item_unit = $('#item_unit').val();
					var unit_name = "";
					if(item_unit && !isNaN(item_unit)){
						qty = parseFloat(qty) * parseFloat(item_unit);
						unit_name = $('#item_unit option:selected').text();
					}

					$('#cart > tbody').append("<tr data-unit_name='"+unit_name+"' data-unit_qty='"+item_unit+"' id='" + item_id + "'><td data-title='Barcode'>" + item_bc + "</td><td data-title='Item'>" + itemcode + "<br><small class='text-danger'>"+de+"</small></td><td data-title='Qty'><input type='text' class='form-control  qty' value='"+qty+"' style='width:80px;'></td><td data-title='Remarks' ><input type='text' class='form-control  remarks' value='"+remarks+"'></td><td ><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");

				}

				$("#txtItem").select2("val", null);
				$('#txtQty').val('');
				$('#txtRemarks').val('');

				$("#"+item_id).children().eq(2).find('input').focus().select();
				saveLocal();
			});
			$('body').on('keyup','.qty',function(e){
				var p = e.keyCode;
				if(p != 8){
					var qty = $(this).val();
					var rgx = /^\d+$/

					if(!rgx.test(qty) || qty == 0){
						$(this).val(1);
						tempToast('error','<p>Invalid quantity</p>','<h3>WARNING!</h3>');
						return;
					}
					$(this).attr('value',qty);
				}
				saveLocal();
			});
			function saveLocal(){
				var cartBody = $('#cart tbody').html();

				var pending = {
					cartBody:cartBody
				};
				localStorage['cache_request_service'] = JSON.stringify(pending);
			}
			checkPendingRequest();
			function checkPendingRequest(){
				if(localStorage['cache_request_service']){
					var pending = JSON.parse(localStorage['cache_request_service']);
					alertify.confirm("You have unsaved request. Do you want to load it?", function (asc) {
						if (asc) {
							$('#cart tbody').html(pending.cartBody);
						}
					}, "");
				}
			}
			$('body').on('change','#txtInvoice',function(){
				//getSales();
			});
			$('body').on('change','#txtDr',function(){
			//	getSales();
			});
			$('body').on('change','#txtIr',function(){
				//getSales();
			});

			function getSales(){
				var invoice = $('#txtInvoice').val();
				var dr = $('#txtDr').val();
				var ir = $('#txtIr').val();
				$.ajax({
				    url:'../ajax/ajax_query2.php',
				    type:'POST',
				    data: {functionName:'getItemForService',invoice:invoice,dr:dr,ir:ir},
					dataType: 'json',
				    success: function(data){
					    if(data.length){
						    $('#cart tbody').html('');
						    for(var i in data){
							    $('#cart > tbody').append("<tr id='" + data[i].item_id + "'><td data-title='Barcode'>" +  data[i].barcode + "</td><td data-title='Item'>" +  data[i].item_code + "<br><small class='text-danger'>"+ data[i].description+"</small></td><td data-title='Qty'><input type='text' class='form-control  qty' value='"+ data[i].qtys+"' style='width:80px;'></td><td data-title='Remarks' ><input type='text' class='form-control  remarks' value=''></td><td ><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
						    }
					    }

				    },
				    error:function(){

				    }
				})
			}



			$('body').on('change','#member_id',function(){
				var v = $(this).val();
				$.ajax({
					url:'../ajax/ajax_wh_order.php',
					type:'POST',
					dataType:'json',
					data: {functionName:'getOwnedBranch',member_id:v},
					success: function(data){
					//	var my_branch = data.branches;
						var my_station = data.stations;
					//	var my_credits = data.credits;

					//	vm.current_credit_list = my_credits;
					//	vm.request.price_group_id =data.price_group_id;
					//	vm.is_hold = data.is_hold;
					//	vm.member_info.contact_number = data.contact_number;
					//	vm.member_info.personal_address = data.personal_address;
					//	vm.member_info.region = data.region;
					//	vm.member_info.terms = data.terms;
					//	vm.member_info.credit_limit = data.credit_limit;


						if(my_station.length > 0){
							var ret = "<option value='0'>Choose Station</option>";
							for(var i in my_station){
								ret += "<option value='"+my_station[i].id+"'>"+my_station[i].name+"</option>";
							}
							$('#station_id').show();
							$('#station_id').html(ret);
							//$('#spec_station_id').html(ret);
						} else {
							$('#station_id').hide();
							$('#station_id').html("<option value='0'>No Station</option>");
							//$('#spec_station_id').html("<option value='0'>No Station</option>");
						}

					},
					error:function(){

					}
				});
			});

			$('body').on('click','#save',function(){

				var btncon = $(this);
				var btnoldval = btncon.val();
				btncon.attr('disabled',true);
				btncon.val('Loading...');


				var arr = [];
				var branch_id = $('#bid').val();
				var service_type_id = $('#service_type_id').val();
				var member_id = $('#member_id').val();
				var station_id = $('#station_id').val();
				var technician_id = $('#technician_id').val();
				var invoice = $('#txtInvoice').val();
				var dr = $('#txtDr').val();
				var ir = $('#txtIr').val();
				var pullout_schedule = $('#txtPulloutDate').val();
				var home_schedule = $('#txtHomeDate').val();
				var serviceType = $('#serviceType').val();
				var service_remarks = $('#service_remarks').val();
				var contact_person = $('#contact_person').val();
				var contact_number = $('#contact_number').val();
				var contact_address = $('#contact_address').val();
				var backload_ref_id = $('#backload_ref_id').val();
				var client_po = $('#client_po').val();

				if(serviceType == 3 && !home_schedule){
				//	alertify.alert('Please enter date first');
				//	btncon.attr('disabled',false);
				//	btncon.val(btnoldval);
				//	return;
				}

				if(!member_id){
					tempToast('error','Please enter member','Error');
					btncon.attr('disabled',false);
					btncon.val(btnoldval);
					return;
				}
				if(!branch_id){
					tempToast('error','Please enter branch','Error');
					btncon.attr('disabled',false);
					btncon.val(btnoldval);
					return;
				}


				/*if(!invoice && !dr && !ir){
					alertify.alert('Please enter Invoice, DR or PR first');
					return;
				} */
				$('#cart tbody tr').each(function(){
						var row = $(this);
						var item_id = row.attr('id');
						var unit_qty = row.attr('data-unit_qty');
						var unit_name = row.attr('data-unit_name');
						var qty = row.children().eq(2).find('input').val();
						var remarks = row.children().eq(3).find('input').val();
						arr.push({unit_name:unit_name,unit_qty:unit_qty,item_id:item_id,qty:qty,remarks:remarks});
				});

				if(arr.length == 0){
					alertify.confirm("No item entered. Are you sure you want to submit this?",function(e){
						if(e){
							arr.push({item_id:0,qty:1,remarks:'No item'});
							$.ajax({
								url:'../ajax/ajax_query2.php',
								type:'POST',
								data: {functionName:'itemServiceRequest',client_po:client_po,backload_ref_id:backload_ref_id,contact_person:contact_person,contact_number:contact_number,contact_address:contact_address,service_remarks:service_remarks,service_type_id:service_type_id,serviceType:serviceType,home_schedule:home_schedule,pullout_schedule:pullout_schedule,req:JSON.stringify(arr),branch_id:branch_id,invoice:invoice,dr:dr,ir:ir,member_id:member_id,technician_id:technician_id},
								success: function(data){
									alertify.alert(data,function(){
										localStorage.removeItem('cache_request_service');
										location.href='item-service.php';
									});
								},
								error:function(){

									location.href='item-service-request.php';
								}
							});
						} else {
							btncon.attr('disabled',false);
							btncon.val(btnoldval);
						}
					})
				} else {
					$.ajax({
						url:'../ajax/ajax_query2.php',
						type:'POST',
						data: {functionName:'itemServiceRequest',client_po:client_po,backload_ref_id:backload_ref_id,contact_person:contact_person,contact_number:contact_number,contact_address:contact_address,service_type_id:service_type_id,serviceType:serviceType,home_schedule:home_schedule,pullout_schedule:pullout_schedule,req:JSON.stringify(arr),branch_id:branch_id,invoice:invoice,dr:dr,ir:ir,member_id:member_id,technician_id:technician_id},
						success: function(data){
							alertify.alert(data,function(){
								localStorage.removeItem('cache_request_service');
								location.href='item-service.php';
							});
						},
						error:function(){

							location.href='item-service-request.php';
						}
					});
				}
			});
			$('body').on('change','#serviceType',function(){
				var v = $(this).val();
				$('#txtPulloutDate').val('');
				$('#txtHomeDate').val('');
				$('#homeRepairDateCon').hide();
				$('#pulloutDateCon').hide();
				 if(v == 2){
					$('#pulloutDateCon').fadeIn(300);
				} else if(v == 3){
					$('#homeRepairDateCon').fadeIn(300);
				}
			});
		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>