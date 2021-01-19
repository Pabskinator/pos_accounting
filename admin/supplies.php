<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';
	if(!($user->hasPermission('req_sup') ||$user->hasPermission('app_sup') ||$user->hasPermission('liq_sup'))) {
		// redirect to denied page
		Redirect::to(1);
	}


?>


	<input type="hidden" value='<?php echo MEMBER_LABEL; ?>' id='MEMBER_LABEL'>
	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">

		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}
		?>
		<div class="row">
			<input type="hidden" id='is_cebuhiq' value="<?php echo Configuration::thisCompany('cebuhiq') ? 1 : 0; ?>">
			<div class="col-md-12">
				<?php include 'includes/supply_nav.php'; ?>
				<!-- REQUEST -->
				<div id="con_req" style='display:none;'>
					<h3>Request <?php echo SUPPLY_LABEL; ?></h3>
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<select name="for_whom" id="for_whom" class='form-control'>
									<option value="0">For whom</option>
									<option value="1"><?php echo MEMBER_LABEL; ?></option>
									<option value="2"><?php echo "Employee"; ?></option>
								</select>
							</div>
						</div>
						<div class="col-md-3" style='display:none;' id='mem_con'>
							<div class="form-group">
								<input type="text" class='form-control' id='member_id'>
							</div>
						</div>
						<div class="col-md-3" style='display:none;' id='user_con'>
							<div class="form-group">
								<input type="text" class='form-control' id='user_id'>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<input type="text" class='form-control' id='branch_id'>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<input type="text" class='form-control' id='remarks' placeholder='Remarks'>
							</div>
						</div>
						<?php
							if(Configuration::thisCompany('cebuhiq')){
								?>
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" class='form-control' id='ref_id' placeholder='Ref Number'>
									</div>
								</div>
								<?php
							}
						?>

					</div>
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<input type="text" class='form-control selectitem' id='item_id'>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<input type="text" class='form-control' id='qty' placeholder='Quantity'>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<input type="text" class='form-control' id='rack_id' placeholder='Rack'>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<button class='btn btn-default' id='btnAdd'>Add Item</button>
							</div>
						</div>
					</div>
					<div id="no-more-tables">
						<table id='cart' class='table' style='font-size:1em'>
							<thead>
							<tr>
								<th>BARCODE</th>
								<th>ITEM CODE</th>
								<th>QTY</th>
								<th></th>
							</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
					<hr />
					<div class="text-right">
						<button id='save' class='btn btn-success'><span class='glyphicon glyphicon-save'></span> SAVE</button>
					</div>
					<div id="test"></div>
				</div>
				<!-- END REQUEST -->
				<!-- FOR APPROVAL -->
				<div id="con_app" style='display:none;'>
					<div class="panel panel-primary">
						<div class="panel-heading">For approval</div>
						<div class="panel-body">
							<div id="content_app"></div>
						</div>
					</div>
				</div>
				<!-- END FOR APPROVAL -->
				<!-- LIQUIDATION -->
				<div id="con_liq" style='display:none;'>
					<div class="panel panel-primary">
						<div class="panel-heading">Liquidation</div>
						<div class="panel-body">
							<div id="content_liq"></div>
						</div>
					</div>
				</div>
				<div id="con_log" style='display:none;'>
					<div class="panel panel-primary">
						<div class="panel-heading">Log</div>
						<div class="panel-body">
							<div id="content_log"></div>
						</div>
					</div>
				</div>
				<!-- END LIQUIDATION -->

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
	<script>

		$(document).ready(function() {
			// LABELS
			var MEMBER_LABEL = $('#MEMBER_LABEL').val();
			showContainer(true,false,false,false);

			$('body').on('click','#printSupplies',function(){
				var con = $(this);
				var items = con.attr('data-list');
				var ref_id = con.attr('data-ref_id');
				var name = con.attr('data-name');
				var branch_name = con.attr('data-branch');
				var user_branch = con.attr('data-user_branch');
				var remarks = con.attr('data-remarks');
				var id = con.attr('data-id');
				try{
					items = JSON.parse(items);
					printSupplies(items,name,id,ref_id,branch_name,user_branch,remarks);
				} catch(e){
					alert("Invalid data list");
				}


			});

			function printSupplies(items,name,id,ref_id,branch_name,user_branch,remarks) {

				var html = '';
				var company_name = localStorage['company_name'];

				var lbl_title = 'Supplies Request Form';
				var is_cebuhiq = $('#is_cebuhiq').val();

				var date_obj = new Date();
				var  curDate = (parseInt(date_obj.getMonth()) + parseInt(1) ) + "/" + date_obj.getDate() + "/" + date_obj.getFullYear();
				var reflbl = "&nbsp;";
				if(is_cebuhiq == 1){
					reflbl =  "Ref Number: " +ref_id;
				}

				html += "<h3 class='text-center'><img src='../css/img/logo.jpg' alt=''> &nbsp;&nbsp;" + company_name + "</h3>";

				html += "<p class='text-center'>" + lbl_title + "</p>";
				html += "<p style='float:left;width:80%'>Name: "+name+"</p>";
				html += "<p style='float:left;width:19%;' >ID: " + id+ "</p>";
				html += "<p style='float:left;width:80%'>Remarks: "+remarks+"</p>";
				html += "<p style='float:left;width:19%;'>Branch: " + branch_name + "</p>";
				html += "<p style='float:left;width:80%'>"+reflbl+"</p>";
				html += "<p style='float:left;width:19%;'>Date: " + curDate + "</p>";

				html += "<div sytle='clear:both;'></div>";
				html += "<br>";
				html += "<table class='table table-bordered table-condensed'>";
				html += "<tr><th>Item Code</th><th>Description</th><th>Quantity</th></tr>";

				for(var i in items) {
					html += "<tr><td>"+items[i].item_code+"</td><td>"+items[i].description+"</td><td>"+items[i].qty+"</td></tr>";
				}

				html += "</table>";
				html += "<br>";
				html += "<br>";

				html += "<p style='float:left;width:31%;' >Request by:<br> ____________________</p>";
				html += "<p style='float:left;width:31%'>Approved by:<br> ____________________</p>";
				html += "<p style='float:left;width:31%'>Received by:<br> ____________________</p>";

				html += "<div sytle='clear:both;'></div>";


				popUpPrintWithStyle(html);
			}

			function popUpPrintWithStyle(data){

				var mywindow = window.open('', 'new div', '');
				mywindow.document.write('<html><head><title></title><style></style>');
				mywindow.document.write('<link rel="stylesheet" href="../css/bootstrap.css" type="text/css" />');
				mywindow.document.write('</head><body style="padding:0;margin:0;">');
				mywindow.document.write(data);
				mywindow.document.write('</body></html>');
				setTimeout(function(){
					mywindow.print();
					mywindow.close();
				},300);

				return true;

			}

			function showContainer(c1,c2,c3,c4){
				var con_req = $('#con_req');
				var con_app = $('#con_app');
				var con_liq = $('#con_liq');
				var con_log= $('#con_log');
				con_req.hide();
				con_app.hide();
				con_liq.hide();
				con_log.hide();
				if(c1){
					con_req.fadeIn(300);
				} else if(c2){
					con_app.fadeIn(300);
					getForApproval();
				} else if(c3){
					con_liq.fadeIn(300);
					getForLiquidation();
				} else if(c4){
					con_log.fadeIn(300);
					getLog();
				}
			}
			$('body').on('click','.btn_nav',function(e){
				e.preventDefault();
				var con = $(this).attr('data-con');
				if(con == 1){
					showContainer(true,false,false,false);
				} else if(con == 2){
					showContainer(false,true,false,false);
				}else if(con == 3){
					showContainer(false,false,true,false);
				}else if(con == 4){
					showContainer(false,false,false,true);
				}
				$('#secondNavigationContainer').hide();
			});

			 /* select 2*/
			$('#member_id').select2({
				placeholder: "Search " + MEMBER_LABEL,
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
									text: item.lastname + ", " + item.firstname + " " + item.middlename,
									slug: item.lastname + ", " + item.firstname + " " + item.middlename,
									id: item.id
								}
							})
						};
					}
				}
			});

			$('#user_id').select2({
				placeholder: 'Search Employee',
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
							functionName:'users'
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
			$('#branch_id').select2({
				placeholder: 'From Branch',
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
							functionName:'branches'
						};
					},
					results: function (data) {
						return {
							results: $.map(data, function (item) {
								return {
									text: item.name ,
									slug: item.name ,
									id: item.id
								}
							})
						};
					}
				}
			});
			// cart
			//cart

			noItemInCart();
			function noItemInCart() {
				if(!$("#cart tbody").children().length) {
					$("#cart tbody").append("<td colspan='3' id='noitem' style='padding-top:10px;' data-title='Item'><span class='text-danger'>NO ITEMS IN CART</span></td>");
				}
			}
			function removeNoItemLabel() {
				$("#noitem").remove();
			}
			$('body').on('click', '.removeItem', function() {
				$(this).parents('tr').remove();
				noItemInCart();
			});

			$("#btnAdd").click(function() {
				var member_id = $('#member_id').val();
				var branch_id = $('#branch_id').val();
				var remarks = $('#remarks').val();
				var item_id_con =  $('#item_id');
				var qty_con =  $('#qty');
				var rack_con =  $('#rack_id');
				var item_id = item_id_con.val();
				var qty = qty_con.val();
				var rack_id = rack_con.val();
				var rack_name ="";
				if(rack_id){
					 rack_name = rack_con.select2('data').text;
				}

				var isoncart = false;

				$('#cart >tbody > tr').each(function(){
					var row_id = $(this).attr('id');
					if(row_id == item_id){
						isoncart = true;
						return;
					}
				});
				if(isoncart){
					tempToast('error','<p>Item is already in cart</p>','<h3>WARNING!</h3>');
					return;
				}
				if(branch_id  && item_id && qty){
					if(!qty || isNaN(qty) || parseFloat(qty) < 0){
						tempToast('error','<p>Invalid quantity</p>','<h3>WARNING!</h3>')
						return;
					}
					var sdata =  item_id_con.select2('data');
					var item_code =sdata.text;
					var arrcode = item_code.split(':');
					removeNoItemLabel();
					var item_bc = arrcode[0];
					$('#cart > tbody').append("<tr data-rack_id='"+rack_id+"' data-rack_name='"+rack_name+"' id='" + item_id + "'><td data-title='Barcode'>" + item_bc + " <span class='span-block text-danger'>"+rack_name+"</span></td><td data-title='Item'>" + arrcode[1] + "<br><small class='text-danger'>"+arrcode[2]+"</small></td><td data-title='Quantity'>"+qty+"</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
					item_id_con.select2('val',null);
					qty_con.val('');
				} else {
					tempToast('error','<p>Please complete the form</p>','<h3>WARNING!</h3>')
				}

			});

			$('#save').click(function(){
				if($("#cart tbody tr").children().length) {
					var member_id = $('#member_id').val();
					var user_id = $('#user_id').val();
					var branch_id = $('#branch_id').val();
					var remarks = $('#remarks').val();
					var ref_id = $('#ref_id').val();
					if((member_id || user_id) && branch_id ){
						var toOrder =[];
						var foundNoqty =0;
						$('#cart >tbody > tr').each(function(index) {
							var row = $(this);
							var item_id = row.prop('id');
							var qty = row.children().eq(2).text();
							var rack_id = row.attr('data-rack_id');
							var rack_name = row.attr('data-rack_name');
							if(qty == '' || qty == undefined){
								qty= 0;
							}
							if(isNaN(qty) || qty == 0){
								foundNoqty = parseInt(foundNoqty) + 1;
							}

							toOrder[index] = {
								item_id: item_id, qty: qty, rack_id:rack_id, rack_name:rack_name
							}
						});
						if(foundNoqty > 0) {
							tempToast('error','<p>Please Indicate the Quantity of the items</p>','<h3>WARNING!</h3>')
						} else {
							$('.loading').show();
							toOrder = JSON.stringify(toOrder);
							$.ajax({
								url: "../ajax/ajax_query.php",
								type: "POST",
								data: {
									toOrder: toOrder,
									member_id: member_id,
									user_id: user_id,
									branch_id: branch_id,
									remarks: remarks,
									ref_id: ref_id,
									functionName:'requestSupplies'
								},
								success: function(data) {
									alertify.alert(data,function(){
										location.href = "supplies.php";
									});
								},
								error: function() {
									alert('Saving transaction error');
									location.href = "supplies.php";
								}
							});
						}
					} else {
						tempToast('error','<p>Please choose either client or employee</p>','<h3>WARNING!</h3>')
					}
				} else {
					tempToast('error','<p>No items in cart</p>','<h3>WARNING!</h3>')
				}
			});

			/* FOR APPROVAL */
			function getForApproval(){
				$.ajax({
				    url:'../ajax/ajax_query.php',
				    type:'POST',
					beforeSend: function(){
						$('#content_app').html("Loading content. Please wait...");
					},
				    data: {functionName:'getForApprovalSupplies'},
				    success: function(data){
				        $('#content_app').html(data);
				    },
				    error:function(){

				    }
				});
			}

			function getForLiquidation(){
				$.ajax({
					url:'../ajax/ajax_query.php',
					type:'POST',
					beforeSend: function(){
						$('#content_liq').html("Loading content. Please wait...");
					},
					data: {functionName:'getForLiquidationSupplies'},
					success: function(data){
						$('#content_liq').html(data);
						$('#tblLiquidation').dataTable({
							iDisplayLength: 200,
							"aaSorting": []
						});
					},
					error:function(){

					}
				});
			}

			function getLog(){
				$.ajax({
					url:'../ajax/ajax_query.php',
					type:'POST',
					beforeSend: function(){
						$('#content_log').html("Loading content. Please wait...");
					},
					data: {functionName:'getSuppliesLog'},
					success: function(data){
						$('#content_log').html(data);

					},
					error:function(){

					}
				});
			}
			$('body').on('click','.btnDetails',function(){
				var id = $(this).attr('data-id');
				$('#myModal').modal('show');
				var mbody = $('#mbody');
				mbody.html('Loading content. Please Wait...');
				$.ajax({
				    url:'../ajax/ajax_query.php',
				    type:'POST',
				    data: {functionName:'getSupplyRequestDetails',id:id},
				    success: function(data){
					    mbody.html(data);
				    },
				    error:function(){

				    }
				});
			});
			$('body').on('click','#btnApproveRequest',function(){
				var btncon = $(this);
				var btnoldval = btncon.html();
				var id = btncon.attr('data-id');
				var racks = btncon.attr('data-racks');
				btncon.attr('disabled',true);
				btncon.html('Loading...');
				if(id && racks){
					alertify.confirm("Are you sure you want to approve this request?",function(e){
						if(e){
							$.ajax({
								url:'../ajax/ajax_query.php',
								type:'POST',
								data: {functionName:'approveSupplyRequest',id:id,racks:racks},
								success: function(data){
									tempToast('info','<p>'+data+'</p>','<h3>Information!</h3>');
									$('#myModal').modal('hide');
									btncon.attr('disabled',false);
									btncon.html(btnoldval);
									getForApproval();
								},
								error:function(){

									btncon.attr('disabled',false);
									btncon.html(btnoldval);
									getForApproval();
								}
							});
						} else {
							btncon.attr('disabled',false);
							btncon.html(btnoldval);
						}
					});

				} else {
					tempToast('error','<p>Invalid details</p>','<h3>Warning!</h3>');
				}
			});

			$('body').on('click','#btnDeclineRequest',function(){
				var btncon = $(this);
				var btnoldval = btncon.html();
				var id = btncon.attr('data-id');
				btncon.attr('disabled',true);
				btncon.html('Loading...');
				if(id){
					alertify.confirm("Are you sure you want to decline this request?",function(e){
						if(e){
							$.ajax({
								url:'../ajax/ajax_query.php',
								type:'POST',
								data: {functionName:'declineSupplyRequest',id:id},
								success: function(data){
									tempToast('info','<p>'+data+'</p>','<h3>Information!</h3>');
									$('#myModal').modal('hide');
									btncon.attr('disabled',false);
									btncon.html(btnoldval);
									getForApproval();
								},
								error:function(){

									btncon.attr('disabled',false);
									btncon.html(btnoldval);
									getForApproval();
								}
							});
						} else {
							btncon.attr('disabled',false);
							btncon.html(btnoldval);
						}
					});

				}
			});
			$('body').on('click','#btnLiquidateRequest',function(){
				var btncon = $(this);
				var btnoldval = btncon.html();
				var id = btncon.attr('data-id');
				btncon.attr('disabled',true);
				btncon.html('Loading...');
				if(id){
					alertify.confirm("Are you sure you want to continue?",function(e){
							if(e){
								var arr = [];
								$('#tblSupplyForApproval > tbody > tr').each(function(){
									var row = $(this);
									var item_id = row.attr('data-item_id');
									var qty = row.attr('data-qty');
									var consume_qty = row.children().eq(3).find('input').val();
									arr.push({item_id:item_id,consume_qty:consume_qty,qty:qty});
								});
								if(arr.length > 0){
									$.ajax({
									    url:'../ajax/ajax_query.php',
									    type:'POST',
									    data: {functionName:'liquidateSupply',id:id,arr:JSON.stringify(arr)},
									    success: function(data){
										    tempToast('info','<p>'+data+'</p>','<h3>Info!</h3>');
										    $('#myModal').modal('hide');
										    btncon.attr('disabled',false);
										    btncon.html(btnoldval);
										    getForLiquidation();
									    },
									    error:function(){

									    }
									})
								}
							} else {
								btncon.attr('disabled',false);
								btncon.html(btnoldval);
							}
						}
					)
				}
			});
			$('body').on('keyup','.txtConQty',function(){
				var con = $(this);
				var row = con.parents('tr');
				var qty = con.val();
				var cur_qty = row.attr('data-qty');
				if(isNaN(qty) || parseFloat(qty) > parseFloat(cur_qty) ){
					con.val(cur_qty);
					tempToast('error','<p>Invalid quantity</p>','<h3>Warning!</h3>');
				}

			});
			$('body').on('change','#for_whom',function(){
				$('#member_id').select2('val',null);
				$('#user_id').select2('val',null);
				var v = $(this).val();
				$('#mem_con').hide();
				$('#user_con').hide();
				if(v == 1){
					$('#mem_con').show();
				} else if(v == 2){
					$('#user_con').show();
				}
			});
			$('#rack_id').select2({
				placeholder: 'Search rack',
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
							functionName:'racks',
							branch_id: $('#bid').val()
						};
					},
					results: function (data) {
						return {
							results: $.map(data, function (item) {
								return {
									text: item.rack,
									slug: item.rack,
									id: item.id
								}
							})
						};
					}
				}
			});
		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>