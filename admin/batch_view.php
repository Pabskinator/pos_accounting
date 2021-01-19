<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('serials')) {
		// redirect to denied page
		Redirect::to(1);
	}
	$sales_type = new Sales_type();
	$types = $sales_type->get_active('salestypes',[1,'=',1]);

?>

	<style>
		.bg-grey{
			background: #eee;
		}
		.breadcrumb {
			padding: 15px 15px 10px 20px;
			background-color: #2c3e50;

		}

		.breadcrumb > li {
			color: #fff;
			padding: 5px 10px;
		}

		.breadcrumb > li.active {
			color: #ccc;
			font-weight: bold;
			border-bottom: 2px solid #ccc;
		}

		.breadcrumb > li + li:after {
			content: '';
		}

		#batch-list > .col-md-4 {
			cursor: pointer;
		}

		.displayDetails{
			cursor: pointer;
		}

		.displayDetails .panel:hover{
			border-color: #434343;
		}

		#batch-list > .col-md-4 > .panel:hover {
			border-color: #434343;
		}

		.desc {
			margin-top: 5px;
		}

		.desc.inline > div {
			display: inline-block;
			margin-right: 20px;
		}

		.desc > div > span {
			color: #434343;
		}

		.desc > div > span:last-child {
			font-weight: bold;
		}

		.get-serial	{
			right: 0;
			bottom: 0;
			margin: 20px;
			position: fixed;
		}

		.current-item {
			border: 1px solid #aaa;
		}
		.dnone{
			display:none;
		}

	</style>
	<!-- Page content -->
	<div id="page-content-wrapper">
		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">


			<div class="content-header">

				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Request Monitoring  </h1>


			</div>
			<?php
				// get flash message if add or edited successfully
				if(Session::exists('flash')) {
					echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
				}
			?>

			<div id='con'>
				<ol class="breadcrumb">
					<li class="active"><a href="#" id='btnList'>List</a></li>
					<li>Get Stocks</li>
					<li>Add Serial</li>
					<li>Print DR</li>
					<li>Print Manifest</li>
					<li>Add Schedule</li>
					<li><a href="#" id='btnShipOut'>Ship out</a></li>
				</ol>

				<div class="container">
					<div id="list">

						<h4>Select a Batch</h4>
						<div class="row">
							<div class="col-md-3">
								<select name="status" id="status" class='form-control' required>
									<option value="">All</option>
									<option value="1">Get Stock</option>
									<option value="2">Add Serial</option>
									<option value="3">Print DR</option>
									<option value="4">Print Manifest</option>
									<option value="5">Add Schedule</option>
								</select>

							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select name="sales_type_list" id="sales_type_list" class='form-control' required>
										<option value="">Select Store Type</option>
										<?php foreach($types as $t){
											echo "<option value='$t->id'>$t->name</option>";
										} ?>
									</select>

								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' id='list_dt_from' placeholder='Date From'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' id='list_dt_to'  placeholder='Date To'>
								</div>
							</div>
						</div>
						<div class="row" id="batch-list"></div>
					</div>

					<div id="get-stocks">

						<div class="row" id="stock-list">
						</div>

						<div class="row">
							<div class="col-md-6">
								<button class="btn btn-primary" id="btnPrintStock">Print Stock</button>
								<button class="btn btn-primary" id="btnGetStock">Get Stock</button>
							</div>

							<div class="col-md-6 text-right">
								<button class="btn btn-danger btnCancel">Cancel</button>
							</div>

						</div>


					</div>

					<div id="add-serial">
						<h4>Add Serial</h4>
						<div class="container">
							<table class="table withBorder" id="orders-table">
								<thead>
								<tr>
									<td>ORDER</td>
									<td>ITEMS</td>
									<td>SERIALS</td>
								</tr>
								</thead>

								<tbody>

								</tbody>
							</table>

							<div class="get-serial">
								<input type="text" id="input-serial">

								<button class="btn btn-primary">GET SERIAL</button>
							</div>
						</div>

						<button class="btn btn-primary dnone" id="btnSaveSerials">Save Serials</button>

					</div>

					<div id="print-dr">
						<div id="dr-container"></div>
						<div id='valid-dr' style='display: none;'>
							<div class="alert alert-info">Please make sure you have enough A5 papers to print these records.</div>
							<div class='row'>
								<div class="col-md-6">
									<button class="btn btn-primary" id="btnPrintDr">Print DR</button>
									<span class='span-block mt10'>
										<input type="checkbox" id='withRebate'>
										<label for="withRebate">With Rebate Label</label>
									</span>

								</div>
								<div class="col-md-6 text-right">
									<button class="btn btn-primary" id="btnToManifest">Next</button>
								</div>
							</div>

						</div>
						<div id='invalid-dr' style='display: none;'>
							<div class="alert alert-info">
								Invalid Request. Please set your computer as <a href="terminal.php"><strong>Terminal</strong></a> first.
							</div>
						</div>
					</div>

					<div id="print-manifest">
						<h4>Print Manifest</h4>

						<div class="alert alert-info">
							Please make sure you have enough papers to print these records.
						</div>
						<div id="con-manifest"></div>
						<div class="row">
							<div class="col-md-6">
								<button id='printManifest' class='btn btn-primary'>Print Manifest</button>
							</div>
							<div class="col-md-6 text-right">
								<button class="btn btn-primary" id="btn5">Next</button>
							</div>
						</div>

					</div>

					<div id="add-schedule">
						<h4>Add Schedule</h4>

						<div class="row">
							<div class="col-md-6">
								<div class="panel panel-default">
									<div class="panel-body">

										<div class="form-group">
											<label>Date</label>
											<input type="text" class="form-control" id='dt_schedule'>
										</div>




									</div>

									<div class="panel-footer">
										<button class="btn btn-primary" id="btnShip">Ship Out</button>
									</div>
								</div>
							</div>
						</div>

					</div>

					<div id="ship-out">
						<h4>Ship Out</h4>
						<div class="row">
							<div class="col-md-3"></div>
							<div class="col-md-3">
								<div class="form-group">
									<select name="sales_type" id="sales_type" class='form-control' required>
										<option value="">Select Store Type</option>
										<?php foreach($types as $t){
											echo "<option value='$t->id'>$t->name</option>";
										} ?>
									</select>

								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' id='log_dt_from' placeholder='Date From'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' id='log_dt_to'  placeholder='Date To'>
								</div>
							</div>
						</div>
						<div id="batch-log"></div>
					</div>
				</div>
			</div>

		</div>
	</div> <!-- end page content wrapper-->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
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


		/* serial logic */
		var orders=[];
		var currentOrderIndex = 0;
		var currentOrderItemIndex = 0;
		var ajax_running = false;
		var statuses = ['','Get Stock','Add Serial','Print DR','Print Manifest','Add Schedule','Ship Out'];



		$(function() {
			var stepsId = [
				'list', 'get-stocks', 'add-serial',
				'print-dr', 'print-manifest', 'add-schedule', 'ship-out'
			];
			var set_id = 0;
			var print_dr_html = "";
			var data_for_printing = null;

			fetchBatches();
			getInvoiceDrPr();
			toggleSteps('list');

			$('#list_dt_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#list_dt_from').datepicker('hide');
				filterWithDate(0);
			});

			$('#list_dt_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#list_dt_to').datepicker('hide');
				filterWithDate(0);
			});

			$('#log_dt_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#log_dt_from').datepicker('hide');
				filterWithDate(1);
			});

			$('#log_dt_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#log_dt_to').datepicker('hide');
				filterWithDate(1);
			});

			$('#btn1').click(function() {
				toggleSteps('get-stocks');
			});

			$('#dt_schedule').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_schedule').datepicker('hide');
			});

			$('body').on('click','#btnShip',function(){
				var dt = $('#dt_schedule').val();
				if(ajax_running) return;
				ajax_running = true;
				var con = $(this);
				button_action.start_loading(con);
				$.ajax({
					url:'../ajax/ajax_avision.php',
					type:'POST',
					data: {functionName:'shipOut',batch_id:set_id,dt:dt},
					success: function(data){
						ajax_running = false;
						button_action.end_loading(con);
						toggleSteps('ship-out');
						logBatches();
					},
					error:function(){
						ajax_running = false;
						button_action.end_loading(con);
					}
				});
			});
			$('body').on('click','#btnPrintStock',function(){
				var html = "";
					for(var i in data_for_printing){
						html += "<h4>PO number: " + data_for_printing[i].client_po +" Customer: "+data_for_printing[i].client_name+"</h4>";
						var items = data_for_printing[i].items;
						html += "<table style='font-size:12px;' class='table table-bordered table-condensed'>";
						html += "<thead><tr><th>Item</th><th>Description</th><th>Qty</th></tr></thead>";
						html += "<tbody>";
						for(var j in items){
							html += "<tr><td>"+items[j].item_code+"</td><td>"+items[j].description+"</td><td>"+items[j].qty+"</td></tr>";
						}
						html += "</tbody>";
						html  += "</table>";
					}
				popUpPrintWithStyle(html);


			});
			$('#btnGetStock').click(function() {
				var con = $(this);
				button_action.start_loading(con);
				if(set_id != 0 ){
					alertify.confirm("Are you sure you want to continue this action?", function(e){
						if(e){
							$.ajax({
								url:'../ajax/ajax_avision.php',
								type:'POST',
								data: {functionName:'deductStocks',batch_id:set_id},
								success: function(data){
									tempToast("info","Stocks updated successfully.","Info");
									button_action.end_loading(con);
									if(data == 2){

										toggleSteps('add-serial');
										fetchSerials();
									} else {

										toggleSteps('print-dr');
										prepareDr(0);
									}

								},
								error:function(){
									button_action.end_loading(con);
								}
							})

						} else {
							button_action.end_loading(con);
						}
					});
				}


			});

			$('#btnSaveSerials').click(function() {
				$.ajax({
					url:'../ajax/ajax_avision.php',
					type:'POST',
					data: {functionName:'saveSerials',items:JSON.stringify(orders),batch_id:set_id},
					success: function(data){
						prepareDr(0);
						toggleSteps('print-dr');
					},
					error:function(){

					}
				})

			});


			$('#btnPrintDr').click(function() {
				print_dr_html = "";
				prepareDr(1);
			});
			$('body').on('click','#printManifest',function(){
				$.ajax({
					url:'../ajax/ajax_avision.php',
					type:'POST',
					dataType:'json',
					data: {functionName:'printManifest',batch_id:set_id},
					success: function(data){
						generateManifest(data);
					},
					error:function(){

					}
				});
			});
			$('body').on('click','#btn5',function(){
				$.ajax({
					url:'../ajax/ajax_avision.php',
					type:'POST',
					data: {functionName:'changeStatus',batch_id:set_id,to_status:5, from_status:4},
					success: function(data){
						toggleSteps('add-schedule');
					},
					error:function(){

					}
				});
			});

			$('body').on('click','#btnShipOut',function(){
				toggleSteps('ship-out');
				logBatches();
			});

			$('body').on('click','#btnList',function(){
				set_id = 0;
				toggleSteps('list');
				fetchBatches();

			});

			$('body').on('click','#btnToManifest',function(){

				$.ajax({
					url:'../ajax/ajax_avision.php',
					type:'POST',
					data: {functionName:'changeStatus',batch_id:set_id,to_status:4, from_status:3},
					success: function(data){

						toggleSteps('print-manifest');
					},
					error:function(){

					}
				});


			});


			$('#btn5').click(function() {
				toggleSteps('add-schedule');
			});

			$('#btn6').click(function() {
				toggleSteps('ship-out');

			});

			$('body').on('click','.btnCancel',function(){
				set_id=0
				toggleSteps('list');
			});


			function toggleSteps(stepId) {
				stepsId.forEach(function(id, index) {
					var stepTitle = $(`.breadcrumb > li:nth-child(${index + 1})`);

					if(stepId == id) {
						stepTitle.addClass('active');
						$(`#${id}`).show();
					} else {
						stepTitle.removeClass('active');
						$(`#${id}`).hide();
					}
				});
			}
			$('body').on('change','#sales_type_list,#status',function(){
				fetchBatches();
			});

			$('body').on('change','#sales_type',function(){
				logBatches();
			});

			function filterWithDate(t){
				if(t == 1){
					var dt_from = $('#log_dt_from').val();
					var dt_to = $('#log_dt_to').val();
					if(dt_from && dt_to){
						logBatches();
					}
				} else {
					var dt_from = $('#list_dt_from').val();
					var dt_to = $('#list_dt_to').val();
					if(dt_from && dt_to){
						fetchBatches();
					}
				}

			}
			function logBatches(){
				var dt_from = $('#log_dt_from').val();
				var dt_to = $('#log_dt_to').val();
				var sales_type_name ='';
				if( $('#sales_type').val()){
					sales_type_name = $('#sales_type > option:selected').text();
				}

				$.ajax({
					url:'../ajax/ajax_avision.php',
					type:'POST',
					dataType:'json',
					data: {functionName:'getLogBatch',sales_type_name:sales_type_name,dt_from:dt_from,dt_to:dt_to},
					success: function(data){
						renderLogBatches(data)
					},
					error:function(){

					}
				});
			}
			function fetchBatches(){
				var dt_from = $('#list_dt_from').val();
				var dt_to = $('#list_dt_to').val();
				var status = $('#status').val();
				var sales_type_name ='';
				if( $('#sales_type_list').val()){
					sales_type_name = $('#sales_type_list > option:selected').text();
				}
				$.ajax({
					url:'../ajax/ajax_avision.php',
					type:'POST',
					dataType:'json',
					data: {functionName:'getPendingBatch',dt_from:dt_from,dt_to:dt_to,status:status,sales_type_name:sales_type_name},
					success: function(data){
						renderBatches(data)
					},
					error:function(){

					}
				});
			}
			function fetchStocks(){
				$.ajax({
					url:'../ajax/ajax_avision.php',
					type:'POST',
					dataType:'json',
					data: {functionName:'getStocks',batch_id:set_id},
					success: function(data){
						data_for_printing = data;
						renderGetStocks(data);
					},
					error:function(){

					}
				});
			}
			function fetchSerials(){
				$.ajax({
					url:'../ajax/ajax_avision.php',
					type:'POST',
					dataType:'json',
					data: {functionName:'getSerials',batch_id:set_id},
					success: function(data){
						orders = data;
						renderGetSerials();
					},
					error:function(){

					}
				});
			}
			$('body').on('click','.displayDetails',function(){
				var id = $(this).attr('data-id');

				$('#myModal').modal('show');
				$('#mbody').html("Loading...");
				renderDetails(id);

			});
			function renderDetails(id){
				$.ajax({
					url:'../ajax/ajax_avision.php',
					type:'POST',
					data: {functionName:'displayItems', batch_id: id},
					success: function(data){
						$('#mbody').html(data);
					},
					error:function(){

					}
				});
			}
			function renderLogBatches(batches) {

				if(batches.length){
					$('#batch-log').html("");
					batches.forEach(function(batch) {
						var status_name = statuses[batch.status];
						$('#batch-log').append(`
							<div class="col-md-4 displayDetails" data-id="${batch.id}" >
								<div class="panel panel-default">
									<div class="panel-heading">
										${batch.batch_name}
									</div>

									<div class="panel-body">
										<p>${batch.store_type_name}</p>
										<p>Status: ${status_name}</p>
									</div>
								</div>
							</div>
						`);
					});
				} else {
					$('#batch-log').html("No pending log.");
				}


			}
			function renderBatches(batches) {

				if(batches.length == 0) {
					$('#batch-list').html("<div class='alert alert-info'>No pending batch</div>");
					return;
				}

				$('#batch-list').html("");
				batches.forEach(function(batch) {
					var status_name = statuses[batch.status];

					$('#batch-list').append(`
					<div class="col-md-4 batch" data-id="${batch.id}" data-status="${batch.status}" >
						<div class="panel panel-default">
							<div class="panel-heading">
								${batch.batch_name}
							</div>

							<div class="panel-body">
								<p>${batch.store_type_name}</p>
								<p>Status: <strong>${status_name}</strong></p>
							</div>
						</div>
					</div>
				`);
				});

				$('body').on('click', '.batch', function() {
					var status = $(this).attr('data-status');
					set_id = $(this).attr('data-id');
					var stepId = '';
					switch(parseInt(status)) {
						case 1:
							stepId = 'get-stocks';
							fetchStocks();
							break;

						case 2:
							stepId = 'add-serial';
							fetchSerials();
							break;

						case 3:
							stepId = 'print-dr';
							prepareDr(0);
							break;

						case 4:
							stepId = 'print-manifest';
							break;

						case 5:
							stepId = 'add-schedule';
							break;

						case 6:
							stepId = 'ship-out';
							break;
					}

					toggleSteps(stepId);
				});
			}

			function renderGetStocks(stocks) {
				$('#stock-list').html('');
				stocks.forEach(function(order) {
					$('#stock-list').append(`
					<div class="col-md-12">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<div class="row">
								<div class="col-md-4">PO Number: ${order.client_po}</div>
								<div class="col-md-4">
									Client name:
									<span>
										<strong>
										${order.client_name}
										</strong>
									</span>
								</div>
								</div>
							</div>

							<div class="panel-body">
								<h5>Items</h5>

								${renderStockItems(order.items)}
							</div>
						</div>
					</div>
				`);

				});
			}

			function renderStockItems(items) {

				var html = '';

				items.forEach(function(item) {
					var inventoryLabel = !item.with_inventory
						? '<label class="label label-danger">No inventory</label>'
						: '';

					html += `
					<div class="panel panel-default item">
						<div class="panel-body">
							${inventoryLabel}

							<div class="desc">
								<div>
									<span>Item code:</span>
									<span>${item.item_code}</span>
								</div>

								<div>
									<span>Description: </span>
									<span>${item.description}</span>
								</div>

								<div>
									<span>Quantity</span>
									<span>${item.qty}</span>
								</div>
							</div>

							<hr />
							<div>
								${renderRacks(item.racks)}
							</div>
						</div>
					</div>
				`;
				});

				return html;
			}

			function renderRacks(racks) {
				var html = '';

				racks.forEach(function(rack) {
					html += `
					<div class="desc inline">
						<div>
							<span>Rack: </span>
							<span>${rack.rack} </span>
						</div>

						<div>
							<span>Quantity: </span>
							<span>${rack.qty} </span>
						</div>
					</div>
				`;
				});

				return html;
			}

			function renderGetSerials() {
				refreshTable();
			}

			function prepareDr(p) {
				if(ajax_running) return;

				ajax_running = true;

				var terminal_id = localStorage['terminal_id'];


				if(terminal_id && terminal_id != '0'){
					var dr_number = localStorage['dr'];
					$.ajax({
						url:'../ajax/ajax_avision.php',
						type:'POST',
						dataType:'json',
						data: {functionName:'prepareDR',dr: dr_number,terminal_id:terminal_id,batch_id:set_id,print:p},
						success: function(data){
							ajax_running = false;

							var dr_container= "<h5>Ready for printing</h5>";
							dr_container += "<table class='table withBorder'>";
							dr_container += "<tr><th>Client</th><th>Po Number</th><th>DR</th></tr>";

							for(var item in data) {

								if(p == 0){

									dr_number = parseInt(dr_number) + parseInt(1);

									dr_container +="<tr><td>"+data[item]['member_name']+"</td><td>"+data[item]['client_po']+"</td><td>"+data[item]['dr']+"</td></tr>";

								} else {

									printAvisionDr(data[item],'Delivery Receipt', dr_number++);

								}

							}
							if(p == 1){
								popUpPrintWithStyle(print_dr_html);
								fetchBatches();
								getInvoiceDrPr();
							}

							dr_container += "</table>";

							if(p == 0){
								$('#dr-container').html(dr_container);

							}
							$('#valid-dr').show();
							$('#invalid-dr').hide();
						},
						error:function(){
							ajax_running = false;
						}
					});


				} else {
					$('#valid-dr').hide();
					$('#invalid-dr').show();
					ajax_running = false;
				}

			}

			function renderPrintManifest() {

			}


			$('#input-serial').val('');

			$('body').on('click', '.get-serial > button', function() {
				var serial_number = $('#input-serial').val();
				if(!serial_number){
					alert("Please enter serial number");
					return;
				}
				$('#input-serial').val('');
				getSerial(serial_number);
			});

			$('body').on('click', 'tr.item', function() {
				var orderIndex = $(this).attr('data-order-index');
				var itemIndex = $(this).attr('data-item-index');

				if(orders[orderIndex].items[itemIndex].serials.length < orders[orderIndex].items[itemIndex].qty) {
					currentOrderIndex = $(this).attr('data-order-index');
					currentOrderItemIndex = $(this).attr('data-item-index');
					refreshTable();
				}
			});

			$('body').on('click', '.serial > p > span:nth-child(2)', function() {
				var p = $(this).parents('p');
				var orderIndex = p.attr('data-order-index');
				var itemIndex = p.attr('data-item-index');
				var serialIndex = p.attr('data-serial-index');

				orders[orderIndex]
					.items[itemIndex]
					.serials
					.splice(serialIndex, 1);

				refreshTable();

			});

			function refreshTable() {
				var ordersTable = $('#orders-table > tbody');
				var innerHTML = '';

				orders.forEach(function(order, index) {
					innerHTML += `
					<tr>
						<td colspan="3">PO Number: ${order.client_po}</td>
					</tr>

					${displayItems(order.items, index)}
				`;
				});

				ordersTable.html(innerHTML);
			}

			function displayItems(items, orderIndex) {
				var html = '';

				items.forEach(function(item, index) {
					var current_bg= "";
					var  completed_bg ="";
					if(currentOrderIndex == orderIndex && currentOrderItemIndex == index){
						current_bg = "bg-grey";
					}
					if(parseInt(item.qty) == item.serials.length  ){
						completed_bg = "bg-success";
					}
					html += `
					<tr
						class="item ${completed_bg} ${current_bg}"
						data-order-index="${orderIndex}"
						data-item-index="${index}"
						id="order${orderIndex}item${index}"
						>

						<td>&nbsp;</td>

						<td>
							<p>Item id: ${item.item_id}</p>
							<p>Desc: ${item.description}</p>
							<p>Qty: ${item.qty}</p>
						</td>

						<td class="serial">${displaySerials(item, orderIndex, index)}</td>
					</tr>
				`;
				});

				return html;
			}

			function displaySerials(item, orderIndex, itemIndex) {
				var html = '';

				if(item.with_serial == '0') {
					html += '<label class="label label-danger">No serial</label>'
				} else {
					item.serials.forEach(function(serial, index) {
						html += `
						<p
							data-order-index="${orderIndex}"
							data-item-index="${itemIndex}"
							data-serial-index="${index}">

							<span>${serial}</span>
							<span class="glyphicon glyphicon-remove"></span>
						</p>
					`;
					});


				}

				return html;
			}
			function isFinishedAddingSerials(){
				var answer = true;
				for( var o in orders){
					var items = orders[o].items;
					for( var i in items){

						if(items[i].with_serial == '1' && items[i].qty != items[i].serials.length){
							answer = false;
							break;
						}

					}
				}
				return answer;
			}

			function getSerial(serial_number) {
				if(currentOrderIndex < orders.length) {
					while(orders[currentOrderIndex].items[currentOrderItemIndex].with_serial == '0') {
						currentOrderItemIndex++;
						incrementOrder();
					}
					appendSerials(serial_number);
					var f = isFinishedAddingSerials()
					if(f){
						$('#btnSaveSerials').show();
						$('.get-serial').hide();
					} else {
						$('#btnSaveSerials').hide();
						$('.get-serial').show();
					}
				} else {
					alert("Done");
				}
			}

			function appendSerials(serial_number) {
				var currentOrderItem = orders[currentOrderIndex].items[currentOrderItemIndex];

				if(currentOrderItem.serials.length < currentOrderItem.qty) {
					currentOrderItem.serials.push(serial_number);
					$('html, body').animate({
						scrollTop: $('#order'+currentOrderIndex+"item"+currentOrderItemIndex).offset().top - 20
					}, 50);

					refreshTable();

					if(currentOrderItem.serials.length == currentOrderItem.qty) {
						currentOrderItemIndex++;

						incrementOrder();
					}
				}
			}

			function incrementOrder() {
				if(currentOrderItemIndex == orders[currentOrderIndex].items.length) {
					currentOrderIndex++;
					currentOrderItemIndex = 0;
				}
			}
			barcodeListener();
			function barcodeListener(){
				var millis = 300;
				var self = this;
				document.addEventListener('keydown',function(event)
				{


					if(event.ctrlKey && event.keyCode==74)
					{
						event.preventDefault();
						console.log('Entered ctrl+j');
					}

				});
				document.onkeypress = function(e) {
					e = e || window.event;
					var charCode = (typeof e.which == "number") ? e.which : e.keyCode;

					if(localStorage.getItem("scan") && localStorage.getItem("scan") != 'null') {
						localStorage.setItem("scan", localStorage.getItem("scan") + String.fromCharCode(charCode));
					} else {
						localStorage.setItem("scan", String.fromCharCode(charCode));
						setTimeout(function() {
							localStorage.removeItem("scan");
						}, millis);
					}
					if (e.keyCode === 13) {
						if(localStorage.getItem("scan").length >= 8) {
							getSerial(localStorage.getItem("scan"));
							localStorage.removeItem("scan");
						}
					}

				}
			}
			/* end serial logic */


			/* PRINT DR LOGIC */



			function printAvisionDr(data, title, dr_number){

				var date_obj = new Date();
				var current_date = (parseInt(date_obj.getMonth()) + parseInt(1)) + "/" + date_obj.getDate() + "/" + date_obj.getFullYear();
				var po_number = data.client_po;

				var html = "";
				var ctr = 0;
				var page = 1;
				var grand_total = 0;
				var page_content = {page:0,content:[]};
				var pages = [];
				var item_per_page = 10;
				var cdr = $('#custom_dr').val();
				var nextdr = parseInt(dr_number) + 1;
				var control_num = (cdr) ? cdr : nextdr;


				if(data.dr && data.dr != '0'){
					control_num = data.dr;
				}

				for(var i in data.item_list){
					if(!data.item_list[i].qty) continue;

					ctr++;

					var total = data.item_list[i].total;
					grand_total += parseFloat(total);
					page_content.page = page;

					if(ctr >= item_per_page) {
						page_content.content.push(data.item_list[i]);
						creatNewPage();
					}else {
						page_content.content.push(data.item_list[i]);
					}

					var serials = data.item_list[i].serials;
					for(var index in serials) {
						ctr++;
						if(ctr >= item_per_page){
							page_content.content.push(formatSerials(serials[index]));
							creatNewPage();
						}else {
							page_content.content.push(formatSerials(serials[index]));
						}
					}
				}

				if(page_content.pages != 0 && page_content.content.length > 0){
					pages.push(page_content);
				}

				for(var p in pages){
					var paging = "";
					var page_subtotal = 0;
					var page_rebate = 0;
					var address =data.personal_address ? data.personal_address : 'N/A';
					var sales_type_name =data.sales_type_name ? data.sales_type_name : 'N/A';

					paging += "<div style='height:49%;position:relative;margin-top:10px;'>";
					paging += "<div style='position:absolute;top:5px;right: 10px;'>"+current_date+"</div>";
					paging += "<div style='position:absolute;top:5px;left: 10px;'>"+po_number+"</div>";
					paging += "<h4 class='text-center'>"+title+"<span style='display:block;font-size:12px;'>"+sales_type_name+"</span></h4>";
					paging += "<table style='font-size:10px;' class='table table-bordered table-condensed'>";
					paging += "<tr><td style='width:50%;'>Client: <strong>"+data.member_name+"</strong></td><td style='width:50%;'>DR #: <strong>"+control_num+"</strong></td></tr>";
					paging += "<tr><td style='width:100%;' colspan='2'>Address: <strong>"+address+"</strong></td></tr>";
					paging += "</table>";
					paging += "<table style='font-size:10px;' class='table table-condensed table-bordered'>";
					paging += "<tr><th>Qty</th><th>Item</th><th>Price</th><th>Total</th></tr>";
					var cur_page  = pages[p].content;
					var ctr = 0;

					for(var cp in cur_page){

						ctr++;
						var description = cur_page[cp].description;
						var qty = cur_page[cp].qty;
						var price = cur_page[cp].price;
						var total = cur_page[cp].total;


						page_subtotal += parseFloat(total);

						paging += "<tr><td style='width:10%;'>"+qty+"</td><td style='width:60%;'>"+description+"</td><td style='width:15%;'>"+number_format(price,2)+"</td><td style='width:15%;'>"+number_format(total,2)+"</td></tr>";
					}

					if(ctr < item_per_page){
						for(var j = ctr; j < item_per_page;j++){
							paging += "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
						}
					}

					page_rebate = parseFloat(data.rebate);
					if(!$('#withRebate').is(":checked")){
						page_rebate = ''
					}

					paging += "<tr><td></td><td>&nbsp;</td><td>&nbsp;</td><td>"+number_format(page_rebate,2)+"</td></tr>";
					paging += "<tr><td>Page "+pages[p].page+" of "+pages.length+" </td><td>&nbsp;</td><td>&nbsp;</td><td>"+number_format(page_subtotal,2)+"</td></tr>";
					paging += "</table>";
					paging += "<table style='font-size:10px;' class='table table-bordered table-condensed'>";
					paging += "<tr><th style='width:32%'>Released By:  </th><th style='width:32%'>Checked By: </th><th style='width:32%'>Received By: </th></tr>";
					paging += "</table>";

					paging += "</div>";

					html += "<div style='page-break-after: always'>"+ paging+"</div>";
				}
				print_dr_html += html;


				function creatNewPage() {
					ctr = 0;
					page++;
					pages.push(page_content);
					page_content = {page: page,content:[]};
				}


			}

			function popUpPrintWithStyle(data) {

				var mywindow = window.open('', 'new div', '');
				mywindow.document.write('<html><head><title></title><style></style>');
				mywindow.document.write('<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" type="text/css" />');
				mywindow.document.write('<style>table.table-bordered tr td,table.table-bordered tr th {border : 1px solid #000 !important;}</style>');
				mywindow.document.write('</head><body style="padding:0;margin:0;;font-family: Arial, Helvetica, sans-serif;">');
				mywindow.document.write(data);
				mywindow.document.write('</body></html>');
				setTimeout(function() {
					mywindow.print();
					mywindow.close();

				}, 300);
				return true;
			}

			function number_format(number, decimals, dec_point, thousands_sep) {
				number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
				if(number == 0) {
					return "";
				}else {
					var n = !isFinite(+number) ? 0 : +number, prec = !isFinite(+decimals) ? 0 : Math.abs(decimals), sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep, dec = (typeof dec_point === 'undefined') ? '.' : dec_point, s = '', toFixedFix = function(n, prec) {
						var k = Math.pow(10, prec);
						return '' + (Math.round(n * k) / k).toFixed(prec);
					};
					// Fix for IE parseFloat(0.55).toFixed(0) = 0;
					s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
					if(s[0].length > 3) {
						s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
					}
					if((s[1] || '').length < prec) {
						s[1] = s[1] || '';
						s[1] += new Array(prec - s[1].length + 1).join('0');
					}
					return s.join(dec);
				}
			}

			function formatSerials(serials) {
				var serialObject = {
					"description": "Serial #: " + serials,
					"qty": "",
					"price":0,
					"total":0
				};

				return serialObject;
			}
			/** END SERIAL LOGIC **/

			/** MANIFEST **/
			function getInvoiceDrPr() {
				if(localStorage['terminal_id']) {

					$.ajax({
						url: "../ajax/ajax_get_branchAndTerminal.php",
						type: "POST",
						data: {cid: localStorage['terminal_id'], type: 3},
						success: function(data) {
							var invarr = data.split(":");
							localStorage["invoice"] = invarr[0];
							localStorage["end_invoice"] = invarr[1];
							localStorage["dr"] = invarr[2];
							localStorage["end_dr"] = invarr[3];
							localStorage["invoice_limit"] = invarr[4];
							localStorage["dr_limit"] = invarr[5];
							localStorage["ir"] = invarr[6];
							localStorage["end_ir"] = invarr[7];
							localStorage["ir_limit"] = invarr[8];
							localStorage["speed_opt"] = invarr[9];
							localStorage["use_printer"] = invarr[10];
							localStorage["data_sync"] = invarr[11];
							localStorage["news_print"] = invarr[12];
							localStorage["print_inv"] = invarr[13];
							localStorage["print_dr"] = invarr[14];
							localStorage["print_ir"] = invarr[15];
							localStorage["pref_inv"] = invarr[16];
							localStorage["pref_dr"] = invarr[17];
							localStorage["pref_ir"] = invarr[18];
							localStorage["suf_inv"] = invarr[19];
							localStorage["suf_dr"] = invarr[20];
							localStorage["suf_ir"] = invarr[21];
							localStorage["sv"] = invarr[22];
							localStorage["sv_limit"] = invarr[23];
							localStorage["suf_sv"] = invarr[24];
							localStorage["pref_sv"] = invarr[25];
							localStorage["sr"] = invarr[26];
							localStorage["sr_limit"] = invarr[27];
							localStorage["suf_sr"] = invarr[28];
							localStorage["pref_sr"] = invarr[29];
							localStorage["ts"] = invarr[30];
							localStorage["ts_limit"] = invarr[31];
							localStorage["suf_ts"] = invarr[32];
							localStorage["pref_ts"] = invarr[33];


						}
					});

				} else {

				}
			}
			function generateManifest(data){

				var arr = data.ids;
				var type_name = data.type_name;


				if(arr.length){
					alertify.confirm("Are you sure you want to print this manifest?",function(e){
						if(e){
							$.ajax({
								url:'../ajax/ajax_wh_order.php',
								type:'POST',
								data: {functionName:'showCarrierManifest',type_name:type_name,arr: JSON.stringify(arr)},
								success: function(data){
									popUpPrintWithStyle(data);
								},
								error:function(){

								}
							})
						}
					});

				} else {
					tempToast('error', "<p>Please choose transactions to process first..</p>", "<h4>Error!!</h4>");
				}
			}

			/** Cancel order status 5 **/
			$('body').on('click','.btnDecline',function(){

				var con = $(this);
				alertify.confirm("Are you sure you want to cancel this record?",function(e){
					if(e){
						var id = con.attr('data-id');
						var batch_id = con.attr('data-batch_id');

						$.ajax({
						    url:'../ajax/ajax_avision.php',
						    type:'POST',
						    data: {functionName:'cancelOrder',order_id:id},
						    success: function(data){
							    renderDetails(batch_id);
						    },
						    error:function(){
						        
						    }
						})
					}
				});


			});
		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>