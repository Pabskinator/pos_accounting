<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';

	if(!$user->hasPermission('item_service_p') && !$user->hasPermission('item_service_s') && !$user->hasPermission('item_service_r')) {
		// redirect to denied page
		Redirect::to(1);
	}

	$item_service = new Item_service_request();
	$service_list = $item_service->getRequest($user->data()->company_id, 1);

	$for_pullout = [];
	$for_homeservice = [];
	$walk_in_customer = [];
	if($service_list) {
		foreach($service_list as $it) {
			if($it->request_type == 2) {
				$for_pullout[] = $it;
			} else if($it->request_type == 3) {
				$for_homeservice[] = $it;
			} else {
				$walk_in_customer[] = $it;
			}
		}
		$nopending = "";
	} else {
		$nopending = "<h4>No pending item.</h4>";
	}

	$barcodeClass = new Barcode();
	$barcode_format = $barcodeClass->getFormat($user->data()->company_id, "SERVICE");

	$order_styles = $barcode_format->styling;
	$status_arr = ['', // 0
		'Repairing', // 1
		'Good',// 2
		'Repair with warranty',  // 3
		'Repair without warranty', // 4
		'Replacement(Junk)', // 5
		'Replacement(Surplus)', // 6
		'Change Item(Junk)',// 7
		'Change Item(Surplus)', // 8
		'Cancelled', // 9
		'Scheduled', // 10
		'Received', // 11
		'Repairing', // 12
		'Installing', // 13
	];

	//
	//                       _oo0oo_
	//                      o8888888o
	//                      88" . "88
	//                      (| -_- |)
	//                      0\  =  /0
	//                    ___/`---'\___
	//                  .' \\|     |// '.
	//                 / \\|||  :  |||// \
	//                / _||||| -:- |||||- \
	//               |   | \\\  -  /// |   |
	//               | \_|  ''\---/''  |_/ |
	//               \  .-\__  '-'  ___/-. /
	//             ___'. .'  /--.--\  `. .'___
	//          ."" '<  `.___\_<|>_/___.' >' "".
	//         | | :  `- \`.;`\ _ /`;.`/ - ` : | |
	//         \  \ `_.   \_ __\ /__ _/   .-` /  /
	//     =====`-.____`.___ \_____/___.-`___.-'=====
	//                       `=---='
	//


	$secondary = [
		'Service Report Validation Schedule',
		'SO Creation And Dispatching',
		'For Reporting',
		'CCD Verification',
		'Close',
		'Hold',
		'Cancelled'
	];


	$is_aquabest = Configuration::isAquabest();

?>
	<style>
		.timeline {
			list-style: none;
			padding: 20px 0 20px;
			position: relative;
		}

		.timeline:before {
			top: 0;
			bottom: 0;
			position: absolute;
			content: " ";
			width: 3px;
			background-color: #eeeeee;
			left: 50%;
			margin-left: -1.5px;
		}

		.timeline > li {
			margin-bottom: 20px;
			position: relative;
		}

		.timeline > li:before,
		.timeline > li:after {
			content: " ";
			display: table;
		}

		.timeline > li:after {
			clear: both;
		}

		.timeline > li:before,
		.timeline > li:after {
			content: " ";
			display: table;
		}

		.timeline > li:after {
			clear: both;
		}

		.timeline > li > .timeline-panel {
			width: 46%;
			float: left;
			border: 1px solid #d4d4d4;
			border-radius: 2px;
			padding: 20px;
			position: relative;
			-webkit-box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175);
			box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175);
		}

		.timeline > li > .timeline-panel:before {
			position: absolute;
			top: 26px;
			right: -15px;
			display: inline-block;
			border-top: 15px solid transparent;
			border-left: 15px solid #ccc;
			border-right: 0 solid #ccc;
			border-bottom: 15px solid transparent;
			content: " ";
		}

		.timeline > li > .timeline-panel:after {
			position: absolute;
			top: 27px;
			right: -14px;
			display: inline-block;
			border-top: 14px solid transparent;
			border-left: 14px solid #fff;
			border-right: 0 solid #fff;
			border-bottom: 14px solid transparent;
			content: " ";
		}

		.timeline > li > .timeline-badge {
			color: #fff;
			width: 50px;
			height: 50px;
			line-height: 50px;
			font-size: 1.4em;
			text-align: center;
			position: absolute;
			top: 16px;
			left: 50%;
			margin-left: -25px;
			background-color: #999999;
			z-index: 100;
			border-top-right-radius: 50%;
			border-top-left-radius: 50%;
			border-bottom-right-radius: 50%;
			border-bottom-left-radius: 50%;
		}

		.timeline > li.timeline-inverted > .timeline-panel {
			float: right;
		}

		.timeline > li.timeline-inverted > .timeline-panel:before {
			border-left-width: 0;
			border-right-width: 15px;
			left: -15px;
			right: auto;
		}

		.timeline > li.timeline-inverted > .timeline-panel:after {
			border-left-width: 0;
			border-right-width: 14px;
			left: -14px;
			right: auto;
		}

		.timeline-badge.primary {
			background-color: #2e6da4 !important;
		}

		.timeline-badge.success {
			background-color: #3f903f !important;
		}

		.timeline-badge.warning {
			background-color: #f0ad4e !important;
		}

		.timeline-badge.danger {
			background-color: #d9534f !important;
		}

		.timeline-badge.info {
			background-color: #5bc0de !important;
		}

		.timeline-title {
			margin-top: 0;
			color: inherit;
		}

		.timeline-body > p,
		.timeline-body > ul {
			margin-bottom: 0;
		}

		.timeline-body > p + p {
			margin-top: 5px;
		}
	</style>

	<!-- Page content -->
	<div id="page-content-wrapper">	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">



	<div class="content-header">

		<h1>
			<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Service Request </h1>
	</div> <?php
		// get flash message if add or edited successfully
		if(Session::exists('flash')) {
			echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
		}

	?>
	<?php include 'includes/service_nav.php'; ?>
	<div id="test"></div>
	<input type="hidden" id='is_cebuhiq' value='<?php echo (Configuration::thisCompany('cebuhiq')) ? 1 : 0; ?>'>
	<div class="row">
		<div class="col-md-12">


			<div class="panel panel-primary">
				<!-- Default panel contents -->
				<div class="panel-heading">Service Request</div>
				<div class="panel-body">
					<div id="main-container"></div>
				</div>
			</div>
		</div>
	</div>
	<!-- end page content wrapper-->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id='mtitle'></h4>
				</div>
				<div class="modal-body" id='mbody'></div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<!-- /.modal -->
	<div class="modal fade" id="myModalRequest" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id='rtitle'></h4>
				</div>
				<div class="modal-body" id='rbody'>
					<input type="hidden" id='request_id'>

					<div class="row">
						<div class="col-md-12">
							<div id="service_request_container">
								<h3>Request Item</h3>
								<input type="hidden" id='hid_request_branch_id'>
								<div class="row">
									<div class="col-md-4">
										<input type="text" class='form-control selectitem' id='request_item_id'></div>
									<div class="col-md-4">
										<input type="text" placeholder="Quantity" class='form-control' id='request_qty'>
									</div>
									<div class="col-md-4">
										<button class='btn btn-default' id='btnAddRequestItem'>Add Item</button>
									</div>
								</div>
								<br>

								<div id="no-more-tables">
									<table id='cart_request' class='table' style='font-size:1em'>
										<thead>
										<tr>
											<th>BARCODE</th>
											<th>ITEM CODE</th>
											<th>QTY</th>
											<th></th>
										</tr>
										</thead>
										<tbody></tbody>
									</table>
								</div>
								<hr />
								<div class="text-right">
									<button id='saveItemRequest' class='btn btn-success'>
										<span class='glyphicon glyphicon-save'></span> SAVE
									</button>
								</div>
							</div>
						</div>

					</div>



				</div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<!-- /.modal -->
	<div class="modal fade" id="getpricemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style='width:95%'>
			<div class="modal-content">
				<div class="modal-body">
					<div id='paymethods'>
						<input type="hidden" id='service_item_list'> <input type="hidden" id="hid_sr_cur_payment" />
						<input type="hidden" id="hid_member_id" /> <input type="hidden" id="hidcashpayment" />
						<input type="hidden" id="hidcreditpayment" />
						<input type="hidden" id="hidbanktransferpayment" />
						<input type="hidden" id="hidchequepayment" /> <input type="hidden" id="hidconsumablepayment" />
						<input type="hidden" id="hidconsumablepaymentfreebies" />
						<input type="hidden" id="hidmembercredit" />
						<span id='totalOfAllPayment' style='padding-left:10px;'></span>
						<input type="hidden" id="hidTotalOfAllPayment" />
						<span id='amountdue' style='padding-left:10px;'></span>
						<input type="hidden" id="hidamountdue" /> <input type="hidden" id="to_credit_member" />
					</div>
					<hr>
					<ul class="nav nav-tabs">
						<li class="active"><a href="#tab_a" data-toggle="tab">Cash
								<span id='totalcashpayment' class='badge'></span></a></li>
						<li><a href="#tab_b" data-toggle="tab">Credit Card
								<span id='totalcreditpayment' class='badge'></span></a></li>
						<li><a href="#tab_c" data-toggle="tab">Bank Transfer
								<span id='totalbanktransferpayment' class='badge'></span></a></li>
						<li><a href="#tab_d" data-toggle="tab">Check <span id='totalchequepayment' class='badge'></span></a>
						</li>
						<li class='need-member'><a href="#tab_e" data-toggle="tab">Consumable Amount
								<span id='totalconsumablepayment' class='badge'></span> </a></li>
						<li class='need-member'><a href="#tab_f" data-toggle="tab">Consumable Freebies
								<span id='totalconsumablepaymentfreebies' class='badge'></span> </a></li>
						<li class='need-member'><a href="#tab_g" data-toggle="tab">Credit
								<span id='totalmembercredit' class='badge'></span> </a></li>
					</ul>
					<div class="tab-content">
						<br> <?php include 'includes/payment_module.php'; ?>
					</div>
					<!-- tab content -->
				</div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<!-- /.modal -->
	<div class="modal fade" id="myModalDetailed" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id='dttitle'></h4>
					</div>
					<div class="modal-body" id='dtbody'>
						<div>
							<!-- Nav tabs -->
							<ul class="nav nav-tabs" role="tablist">
								<li role="presentation" class="active"><a href="#dtinfo_request" aria-controls="Request Info" role="tab" data-toggle="tab">Request Info</a></li>
								<li role="presentation"><a href="#dtinfo_remarks" aria-controls="Remarks" role="tab" data-toggle="tab">Remarks</a></li>
								<li role="presentation"><a href="#dtinfo_timelog" aria-controls="Timelog" role="tab" data-toggle="tab">Time log</a></li>
								<li role="presentation"><a href="#dtinfo_itemused" aria-controls="Item Used" role="tab" data-toggle="tab">Item Used</a></li>
								<li role="presentation"><a href="#dtinfo_measurement" aria-controls="Measurement" role="tab" data-toggle="tab">Measurement</a></li>
							</ul>

							<!-- Tab panes -->
							<div class="tab-content container-fluid">
								<br>
								<div role="tabpanel" class="tab-pane fade in active" id="dtinfo_request">
									<div class="panel panel-default">
										<div class="panel-body">
											<div id="dtinfo_request_body"></div>
										</div>
									</div>
								</div>
								<div role="tabpanel" class="tab-pane" id="dtinfo_remarks">
									<div class="panel panel-default">
										<div class="panel-body">
											<div id="dtinfo_remarks_body"></div>
										</div>
									</div>
								</div>
								<div role="tabpanel" class="tab-pane" id="dtinfo_timelog">
									<div class="panel panel-default">
										<div class="panel-body">
											<div id="dtinfo_timelog_body"></div>
										</div>
									</div>
								</div>
								<div role="tabpanel" class="tab-pane" id="dtinfo_itemused">
									<div class="panel panel-default">
										<div class="panel-body">
											<div id="dtinfo_itemused_body"></div>
										</div>
									</div>
								</div>
								<div role="tabpanel" class="tab-pane" id="dtinfo_measurement">
									<div class="panel panel-default">
										<div class="panel-body">
											<div id="dtinfo_measurement_body"></div>
										</div>
									</div>
								</div>
							</div>

						</div>
					</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<div class="modal fade" id="myModalBundle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id='btitle'></h4>
				</div>
				<div class="modal-body" id='bbody'>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<script>
		//$('#tblSales').dataTable({
		//	iDisplayLength: 50
		//});
		$(function() {

			var secondary = [
				'Service Report Validation Schedule',
				'SO Creation And Dispatching',
				'For Reporting',
				'CCD Verification',
				'Close',
				'Hold',
				'Cancelled'
			];

			$('body').on('click','.btnCCD',function(){
				var con = $(this);
				var id = con.attr('data-id');

				$('#dtinfo_request_body').html('');
				$('#dtinfo_remarks_body').html('');
				$('#dtinfo_timelog_body').html('');
				$('#dtinfo_itemused_body').html('');
				$('#dtinfo_measurement_body').html('');

				$.ajax({
				    url:'../ajax/ajax_service_item.php',
				    type:'POST',
					dataType:'json',
				    data: {functionName:'detailedInfo', id:id},
				    success: function(data){
					    var ret = "<div class='row'>";
					     ret += "<div class='col-md-4'>";
					     ret += "<p><strong>ID:</strong> "+data.request.id+"</p>";
					     ret += "</div>";
					     ret += "<div class='col-md-4'>";
					     ret += "<p><strong>Requested by:</strong> "+data.request.name+"</p>";
					     ret += "</div>";
					    ret += "<div class='col-md-4'>";
					     ret += "<p><strong>Created at:</strong> "+data.request.created+"</p>";
					     ret += "</div>";
					     ret += "</div>";
					    ret += "<div class='row'>";
					     ret += "<div class='col-md-4'>";
					     ret += "<p><strong>Branch:</strong> "+data.request.branch_name+"</p>";
					     ret += "</div>";
					     ret += "<div class='col-md-4'>";
					     ret += "<p><strong>Client name:</strong> "+data.request.member_name+"</p>";
					     ret += "</div>";
					    ret += "<div class='col-md-4'>";
					     ret += "<p><strong>Client Address:</strong> "+data.request.personal_address+"</p>";
					     ret += "</div>";
					     ret += "</div>";
					     ret += "<div class='row'>";
					     ret += "<div class='col-md-12'>";
					     ret += "<p><strong>Technicians:</strong> "+data.request.tech+"</p>";
					     ret += "</div>";
					     ret += "</div>";
					    ret += "<div class='row'>";
					    ret += "<div class='col-md-4'>";
					    ret += "<p><strong>CCD Remarks:</strong><br> "+data.request.remarks+"</p>";
					    ret += "</div>";
					    ret += "<div class='col-md-4'>";
					    ret += "<p><strong>Troubleshooting Details:</strong><br> "+data.request.troubleshooting_details+"</p>";
					    ret += "</div>";
					    ret += "<div class='col-md-4'>";
					    ret += "<p><strong>Technical Remarks:</strong> "+data.request.technical_remarks+"</p>";
					    ret += "</div>";
					    ret += "</div>";

					    $('#dtinfo_request_body').html(ret);
					    $('#dtinfo_remarks_body').html(data.remarks);
					    $('#dtinfo_timelog_body').html(data.timelog);
					    $('#dtinfo_itemused_body').html(data.used_item);
					    $('#dtinfo_measurement_body').html(data.measurement);
				    },
				    error:function(){

				    }
				});

				$('#myModalDetailed').modal('show');

			});

			$('body').on('click','.secondary-status-change',function(){
				var con = $(this);
				var status = con.attr('data-status');
				var id = con.attr('data-id');
				var next_status;
				if(status == 5){
					next_status = ''
				} else {
					 next_status = "Next status: <span class='text-danger'>" + secondary[parseInt(status) + 1] +"</span>.";
				}

				alertify.confirm("Are you sure you want to process this request?<br><br>"+next_status,function(e){
					if(e){

						$.ajax({
						    url:'../ajax/ajax_member_service.php',
						    type:'POST',
						    data: {functionName:'changeStatusSecondary',id:id,status:status},
						    success: function(data){
								getRequest();

						    },
						    error:function(){
						        
						    }
						}) ;
					} else {

					}
				});
			});
			$('body').on('click','.secondary-status-change-cancel',function(){
				var con = $(this);
				var status = con.attr('data-status');
				var id = con.attr('data-id');
				var next_status = secondary[6];
				/*alertify.confirm("Are you sure you want to process this request?<br><br>Next status: <span class='text-danger'>" + next_status +"</span>.",function(e){
					if(e){

						$.ajax({
						    url:'../ajax/ajax_member_service.php',
						    type:'POST',
						    data: {functionName:'changeStatusSecondary',id:id,status:status,is_cancel:1},
						    success: function(data){
								getRequest();

						    },
						    error:function(){

						    }
						}) ;
					} else {

					}
				}); */

				 alertify.prompt("You are about to cancel this request. Tell us why;",function(e,value){
					 if(e ){
						 if(value){
							 $.ajax({
								 url:'../ajax/ajax_member_service.php',
								 type:'POST',
								 data: {functionName:'changeStatusSecondary',id:id,status:status,is_cancel:1,msg:value},
								 success: function(data){
									 getRequest();

								 },
								 error:function(){

								 }
							 }) ;
						 } else {
							 alert("Please enter a valid remarks.");
						 }

					 }
				});
			});
			$('body').on('click','.secondary-status-change-hold',function(){
				var con = $(this);
				var status = con.attr('data-status');
				var id = con.attr('data-id');
				var next_status = secondary[5];

				alertify.prompt("You are about to hold this request. Tell us why;",function(e,value){
					if(e ){
						if(value){
							$.ajax({
								url:'../ajax/ajax_member_service.php',
								type:'POST',
								data: {functionName:'changeStatusSecondary',id:id,status:status,is_hold:1,hold_msg:value},
								success: function(data){
									getRequest();

								},
								error:function(){

								}
							}) ;
						} else {
							alert("Please enter a valid remarks.");
						}

					}
				});
			});
			getRequest();
			function getRequest(){
				$('#main-container').html('Loading...');
				$.ajax({
					url:'../ajax/ajax_member_service.php',
					type:'POST',
					data: {functionName:'getServiceRequest'},
					success: function(data){
						$('#main-container').html(data);
						showCurrent();
					},
					error:function(){

					}
				}) ;
			}
			$('body').on('click','#btnAddBranch',function(){
				var id = $('#add_service_id').val();
				var branch_id = $('#add_branch_id').val();
				if(id && branch_id){
					$('#mbody').html('');
					$('#myModal').modal('hide');
					$.ajax({
					    url:'../ajax/ajax_member_service.php',
					    type:'POST',
					    data: {functionName:'assignBranch',id:id,branch_id:branch_id},
					    success: function(data){
					        tempToast('info',data,'Info');
						    getRequest();
					    },
					    error:function(){

					    }
					});
				} else {
					tempToast('error',"Invalid request",'Error');
				}

			});
			$('body').on('click','.btnAddBranch',function(){
				var con = $(this);
				var branch_id = con.attr('data-id');
				$('#mbody').html('Loading...');
				$('#myModal').modal('show');
				var html = "<input type='hidden' value='"+branch_id+"' id='add_service_id'>";
				html += "<p class='text-center'>Assign branch</p>";
				html += "<div class='row'><div class='col-md-3'></div><div class='col-md-6'><input type='text' id='add_branch_id' class='form-control'></div><div class='col-md-3'></div></div>";
				html += "<br><p class='text-center'><button class='btn btn-default' id='btnAddBranch'>Submit</button></p>";
				$('#mbody').html(html);
				setTimeout(function(){
					$('#add_branch_id').select2({
						placeholder: 'Branch',
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
				},300);

			});

			$('body').on('click','#btnAddOnsiteSubmit',function(){
				var service_id = $('#onsite_service_id').val();
				var onsite_date = $('#onsite_date').val();

				$.ajax({
				    url:'../ajax/ajax_member_service.php',
				    type:'POST',
				    data: {functionName:'updateOnsiteDate', service_id:service_id,onsite_date:onsite_date},
				    success: function(data){
						alert(data);
					    getRequest();
				    },
				    error:function(){

				    }
				});
			});

			$('body').on('click','.btnAddOnsiteSchedule',function(){
				var con = $(this);
				var id = con.attr('data-id');
				$('#mbody').html('Loading...');
				$('#myModal').modal('show');
				var html = "<input type='hidden' value='"+id+"' id='onsite_service_id'>";
				html += "<p class='text-center'>On-site Schedule</p>";
				html += "<div class='row'><div class='col-md-3'></div><div class='col-md-6'><input type='text' id='onsite_date' class='form-control'></div><div class='col-md-3'></div></div>";
				html += "<br><p class='text-center'><button class='btn btn-default' id='btnAddOnsiteSubmit'>Submit</button></p>";
				$('#mbody').html(html);
				
				$('#onsite_date').datepicker({
					autoclose:true
				}).on('changeDate', function(ev){
					$('#onsite_date').datepicker('hide');
				});

			});
			$('body').on('click', '#saveSpare', function() {
				var id = $('#hid_det_id').val();
				var no_sp = true;
				var final_arr = [];
				var btn = $(this);
				$('#tblDet > tbody > tr').each(function() {
					var row = $(this);
					var tbl = row.children().eq(4).find('.table');
					var tblid = tbl.attr('id');
					var item_id = row.attr('data-item_id');
					var sp_arr = [];
					$('#' + tblid + ' tbody tr').each(function() {
						var sparerow = $(this);
						var raw_id = sparerow.attr('data-item_id');
						var raw_qty = sparerow.children().eq(1).find('input').val();
						var raw_item_code = sparerow.children().eq(0).text();
						if(raw_id && raw_qty && !isNaN(raw_qty) && parseInt(raw_qty) > 0) {
							sp_arr.push({raw_id: raw_id, raw_qty: raw_qty, raw_item_code: raw_item_code});
							no_sp = false;
						}
					});
					final_arr.push({item_id: item_id, sp_arr: JSON.stringify(sp_arr)});
				});
				if(no_sp) {
					alertify.confirm("Are you sure this item will not require spare parts?", function(e) {
						if(e) {
							processSaveSp(id, JSON.stringify(final_arr), btn);
						}
					})
				} else {
					alertify.confirm("Are you sure you want to continue this transaction?", function(e) {
						if(e) {
							processSaveSp(id, JSON.stringify(final_arr), btn);
						}
					});
				}
			});
			function processSaveSp(id, dt, btn) {
				var btnoldval = btn.html();
				btn.attr('disabled', true);
				btn.html('Loading...');
				$.ajax({
					url: '../ajax/ajax_query2.php',
					type: 'POST',
					data: {functionName: 'saveSparepartsService', id: id, dt: dt},
					success: function(data) {
						alertify.alert(data);
						getDetails(id);
						btn.attr('disabled', false);
						btn.html(btnoldval);
					},
					error: function() {

						btn.attr('disabled', false);
						btn.html(btnoldval);
					}
				})
			}

			function formatItem(o) {
				if(!o.id)
					return o.text; // optgroup
				else {
					var r = o.text.split(':');
					return "<span> " + r[0] + "</span> <span style='margin-left:10px'>" + r[1] + "</span><span style='display:block;margin-top:5px;'  class='text-danger'><small class='testspanclass'>" + r[2] + "</small></span>";
				}
			}

			$('body').on('click', '.btnDetails', function() {

				var id = $(this).attr('data-id');
				var member_id = $(this).attr('data-member_id');

				$('#hid_member_id').val(member_id);

				if(member_id == '0') {
					$('.need-member').hide();
				} else {
					$('.need-member').show();
				}

				getDetails(id);

			});

			$('body').on('click','#btnSCS',function(){

				var data = $('#print_data').val();

				try{

					data = JSON.parse(data);

					var contact_person = (data['main'].contact_person) ? data['main'].contact_person :'';
					var contact_number = (data['main'].contact_number) ? data['main'].contact_number :'';
					var contact_address = (data['main'].contact_address) ? data['main'].contact_address :'';

					var member_name = data['main'].member_name;
					var member_address = data['main'].member_address;
					var member_contact = data['main'].member_contact;

					if(member_name && contact_person){
						member_name += " / " +contact_person;
					}  else if (!member_name && contact_person){
						member_name = contact_person;
					}

					if(member_contact && contact_number){
						member_contact += " / " +contact_number;
					} else if (!member_contact && contact_number){
						member_contact = contact_number;
					}

					if(member_address && contact_address){
						member_address += " / " +contact_address;
					} else if (!member_address && contact_address){
						member_address = contact_address;
					}


					var form = {
							date: data['main'].date,
							customer_name: member_name,
							address: member_address,
							tel_number: member_contact,
							model:'',
							serial_number:'',
							date_sold:'',
							si_dr:'',
							outlet:'',
							complain_other:'',
							complains:{
								no_cold:false,poor_cooling:false,water: false,no_hot:false,motor:false,water_con:false,no_power:false,
								crack: false, poor_flowing:false, leaking:false,malfunction:false,other:false
							},
							accessories:'',
							dents:'',
							under_warranty: false,
							received_by:'',
							conforme:'',
							ref_number:''
						};

					var arr = { service_id:data['main'].service_id,form:form};
					localStorage['scs_form'] = JSON.stringify(arr);
					location.href = 'layout_test.php';

				} catch(e){
					console.log("Invalid Data");
				}

			});

			$('body').on('click','#btnSAR',function(){

				var data = $('#print_data').val();

				try{
					data = JSON.parse(data);

					var contact_person = (data['main'].contact_person) ? data['main'].contact_person :'';
					var contact_number = (data['main'].contact_number) ? data['main'].contact_number :'';
					var contact_address = (data['main'].contact_address) ? data['main'].contact_address :'';

					var member_name = data['main'].member_name;
					var member_address = data['main'].member_address;
					var member_contact = data['main'].member_contact;

					if(member_name && contact_person){
						member_name += " / " +contact_person;
					}  else if (!member_name && contact_person){
						member_name = contact_person;
					}

					if(member_contact && contact_number){
						member_contact += " / " +contact_number;
					} else if (!member_contact && contact_number){
						member_contact = contact_number;
					}

					if(member_address && contact_address){
						member_address += " / " +contact_address;
					} else if (!member_address && contact_address){
						member_address = contact_address;
					}

					var form = {
						date:data['main'].date,
						customer_name:member_name,
						address:member_contact,
						tel_number:member_address,
						model:'',
						serial_number:'',
						date_sold:'',
						si_dr:'',
						complains:'',
						work_done:'',
						findings:'',
						parts_needed:'',
						labor_charge:'',
						parts_charge:'',
						other_charge:'',
						overall_charge:'',
						technician:'',
						conforme:'',
						ref_number:''
					};
					var arr = { service_id:data['main'].service_id,form:form};
					localStorage['sar_form'] = JSON.stringify(arr);
					location.href = 'sar.php';
				} catch(e){
					console.log("Invalid Data");
				}

			});

			function getDetails(id) {
				$('#myModal').modal('show');
				var terminal_id = localStorage['terminal_id'];
				$.ajax({
					url: '../ajax/ajax_query2.php',
					type: 'POST',
					beforeSend: function() {
						$('#mbody').html('Loading...');
					},
					data: {functionName: 'itemServiceDetails', id: id, terminal_id: terminal_id},
					success: function(data) {
						$('#mbody').html(data);
						$("#serviceItem").select2({
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
							// fired to the original element when the dropdown closes


						}).on("select2-highlight", function(e) {

						});

						$('#update_pullout_schedule').datepicker({
							autoclose:true
						}).on('changeDate', function(ev){
							$('#update_pullout_schedule').datepicker('hide');
						});
					},
					error: function() {

						$('.myModal').modal('hide');
					}
				})
			}

			$('body').on('click', '#btnPayment', function() {
				var btnthis = $(this);
				var credit_amount = btnthis.attr('data-credit_amount');
				var override_credit = $('#override_credited').val();
				finalizeProcess(override_credit);
			});

			$('body').on('keyup','.txt-overwrite-price',function(){
				computeTotalPrice();
			});

			function computeTotalPrice(){
				var total = 0;
				$('#tblDet tbody tr').each(function(e){
					var row = $(this);
					var txt  = row.children().eq(6).find('input');
					var overwrite_price = txt.val();
					if(overwrite_price && overwrite_price.trim()){
						total = parseFloat(total) + parseFloat(overwrite_price);
					} else {
						total = parseFloat(total) + parseFloat(txt.attr('data-orig'));
					}

				});
				$('#override_credited').val(total);

			}
			function finalizeProcess(credit_amount) {

				var member_id = $('#hid_member_id').val();
				var hid_details_data = $('#hid_details_data').val();
				var id = $('#hid_det_id').val();
				var refund_amount = $('#refund_amount').val();
				var arr = [];
				var used_items = [];
				$('#tblDet tbody tr').each(function(e){

					var row = $(this);
					var txt =  row.children().eq(6).find('input');
					var overwrite_price = txt.val();
					var id = txt.attr('data-id');
					var orig_price = txt.attr('data-orig');


					if(overwrite_price){
						overwrite_price.trim();
					}

					if(overwrite_price === '0' || overwrite_price === '0.00'){
						overwrite_price = -0.01;
					}

					arr.push( { id:id , price: overwrite_price,orig_price:orig_price });

				});
				$('#tbl-used-items tbody tr').each(function(e){

					var row = $(this);
					var id = row.attr('data-id');
					var price =  row.children().eq(3).find('input').val();

					used_items.push( { id:id , price: price});

				});
				console.log(used_items);

				alertify.confirm("Are you sure you want to process this request", function(e) {

					if(e) {
						$.ajax({
							url: '../ajax/ajax_query2.php',
							type: 'POST',
							data: {
								functionName: 'creditToMember',
								id: id,
								details_data: hid_details_data,
								amount: credit_amount,
								refund_amount: refund_amount,
								member_id: member_id,
								used_items: JSON.stringify(used_items),
								overwrite_arr: JSON.stringify(arr)
							},
							success: function(data) {
								alertify.alert(data,function(){
									location.href = 'item-service.php';
								});
							},
							error: function() {

							}
						});

					} else {

					}
				});

			}

			$('body').on('change', '#serviceItem', function() {
				if($(this).val()) {
					var data = $(this).select2("data").text;
					var splitted = data.split(':');
					if(splitted[3]) {
						$('#servicePrice').val(splitted[3]);
					}
				} else {
					$('#servicePrice').val('0.00');
				}
				computeTotalPayment();
			});

			$('body').on('change', '#serviceDiscount', function() {
				computeTotalPayment();
			});

			$('body').on('change', '.chk_spare', function() {
				computeTotalPayment();
			});

			function computeTotalPayment() {
				var servicePrice = $('#servicePrice').val();
				var serviceDiscount = $('#serviceDiscount').val();
				serviceDiscount = (serviceDiscount) ? serviceDiscount : 0;
				var finalPrice = parseFloat(servicePrice) - parseFloat(serviceDiscount);
				var spcost = 0;

				var splist = [];
				$('#tblDet > tbody > tr').each(function() {
					var row = $(this);
					var tbl = row.children().eq(4).find('.table');
					var tblid = tbl.attr('id');
					var item_id = row.attr('data-item_id');
					$('#' + tblid + ' tbody tr').each(function() {
						var sparerow = $(this);
						var ischeck = sparerow.children().eq(0).find('.chk_spare');
						var sp_id = sparerow.attr('data-item_id');

						if(ischeck.is(':checked')) {
							var spprice = sparerow.attr('data-price');
							var qty = sparerow.children().eq(2).text();
							spprice = parseFloat(spprice) * parseFloat(qty);
							spcost = parseFloat(spcost) + parseFloat(spprice);
							splist.push({sp_id: sp_id, qty: qty})
						}
					});
				});

				finalPrice = parseFloat(finalPrice) + parseFloat(spcost);
				$('#totalPayment').text(number_format(finalPrice, 2));
				$('#hid_totalPayment').val(finalPrice);
				$('#service_item_list').val(JSON.stringify(splist));

			}

			function showCurrent(){
				var c = localStorage['nav_service_last'];
				if(c){
					if(c == 1){
						showContainer(true, false, false);
					} else if(c == 2){
						showContainer(false, true, false);
					}else if(c == 3){
						showContainer(false, false, true);
					}
				}
			}


			$('body').on('click','#nav_walkin',function(){
				showContainer(true, false, false);
			});
			$('body').on('click','#nav_pullout',function(){
				showContainer(false, true, false);
			});
			$('body').on('click','#nav_homeservice',function(){
				showContainer(false, false, true);
			});
			function showContainer(c1, c2, c3) {
				$('#con_walkin').hide();
				$('#con_pullout').hide();
				$('#con_homeservice').hide();
				if(c1) {
					$('#con_walkin').fadeIn(300);
					localStorage['nav_service_last'] = 1;
				} else if(c2) {
					$('#con_pullout').fadeIn(300);
					localStorage['nav_service_last'] = 2;
				} else if(c3) {
					$('#con_homeservice').fadeIn(300);
					localStorage['nav_service_last'] = 3;
				}
			}



			/*$('body').on('click','.btnPayment',function(){
				var row = $(this).parents('tr');
				var total = row.attr('data-total');
				var order_id = row.attr('data-id');
				$('#payment_order_id').val(order_id);
				showpricemodal('0',total.toString());
			});*/
			/* end payment logic */

			$('body').on('click', '#cancelService', function() {
				var id = $('#hid_det_id').val();

				alertify.confirm("Are you sure you want to delete this request?", function(e) {
					if(e) {
						$.ajax({
							url: '../ajax/ajax_query2.php',
							type: 'POST',
							data: {functionName: 'cancelItemService', id: id},
							success: function(data) {
								alertify.alert(data,function(){
									location.href='item-service.php';
								});
							},
							error: function() {

							}
						})
					}
				})
			});

			$('body').on('click', '#printData', function() {
				var data = $('#print_data').val();
				try {
					data = JSON.parse(data);
					PrintElemDr(data);
				} catch(e) {

				}
			});


			var order_style = '<?php echo $order_styles; ?>';

			function PrintElemDr(data) {
				var data_info = data.main;
				var data_details = data.details;
				var member_name = data_info.member_name;
				var member_address = data_info.member_address;
				var service_type_name = data_info.service_type_name;
				var styling = JSON.parse(order_style);
				var remarks = ''; //
				var reservedbyname = "";
				var station_name = data_info.member_contact;
				var station_address = "";
				var output = data_info.date;

				var datevisible = (styling['date']['visible']) ? 'display:block;' : 'display:none;';
				var membernamevisible = (styling['membername']['visible']) ? 'display:block;' : 'display:none;';
				var memberaddressvisible = (styling['memberaddress']['visible']) ? 'display:block;' : 'display:none;';
				var stationnamevisible = (styling['stationname']['visible']) ? 'display:block;' : 'display:none;';
				var servicetypevisible = (styling['servicetype']['visible']) ? 'display:block;' : 'display:none;';
				var stationaddressvisible = (styling['stationaddress']['visible']) ? 'display:block;' : 'display:none;';
				var itemtablevisible = (styling['itemtable']['visible']) ? 'display:block;' : 'display:none;';
				var paymentsvisible = (styling['payments']['visible']) ? 'display:block;' : 'display:none;';
				var payments2visible = (styling['payments2']['visible']) ? 'display:block;' : 'display:none;';
				var payments3visible = (styling['payments3']['visible']) ? 'display:block;' : 'display:none;';
				var cashiervisible = (styling['cashier']['visible']) ? 'display:block;' : 'display:none;';
				var remarksvisible = (styling['remarks']['visible']) ? 'display:block;' : 'display:none;';
				var reservedvisible = (styling['reserved']['visible']) ? 'display:block;' : 'display:none;';
				var drnumvisible = (styling['drnum']['visible']) ? 'display:block;' : 'display:none;';
				var tdbarcodevisible = (styling['tdbarcode']['visible']) ? 'display:inline-block;' : 'display:none;';
				var tdqtyvisible = (styling['tdqty']['visible']) ? 'display:inline-block;' : 'display:none;';
				var tddescriptionvisible = (styling['tddescription']['visible']) ? 'display:inline-block;' : 'display:none;';
				var tdpricevisible = (styling['tdprice']['visible']) ? 'display:inline-block;' : 'display:none;';
				var tdtotalvisible = (styling['tdtotal']['visible']) ? 'display:inline-block;' : 'display:none;';

				var dateBold = (styling['date']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var membernameBold = (styling['membername']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var memberaddressBold = (styling['memberaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var stationnameBold = (styling['stationname']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var servicetypeBold = (styling['servicetype']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var stationaddressBold = (styling['stationaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var itemtableBold = (styling['itemtable']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var paymentsBold = (styling['payments']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var payments2Bold = (styling['payments2']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var payments3Bold = (styling['payments3']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var cashierBold = (styling['cashier']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var remarksBold = (styling['remarks']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var reservedBold = (styling['reserved']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var drnumBold = (styling['drnum']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var tdbarcodeBold = (styling['tdbarcode']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var tdqtyBold = (styling['tdqty']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var tddescriptionBold = (styling['tddescription']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var tdpriceBold = (styling['tdprice']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var tdtotalBold = (styling['tdtotal']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';

				var printhtml = "";
				printhtml = printhtml + "<div id='maindivforprinting' style='page-break-before: always;position:relative;'>";
				printhtml = printhtml + "<div style='" + datevisible + dateBold + "position:absolute;top:" + styling['date']['top'] + "px; left:" + styling['date']['left'] + "px;font-size:" + styling['date']['fontSize'] + "px;'><br/><br/>" + output + " </div>";
				printhtml = printhtml + "<div style='" + membernamevisible + membernameBold + "position:absolute;top:" + styling['membername']['top'] + "px; left:" + styling['membername']['left'] + "px;font-size:" + styling['membername']['fontSize'] + "px;'>" + member_name + "</div>";
				printhtml = printhtml + "<div style='" + memberaddressvisible + memberaddressBold + "position:absolute;top:" + styling['memberaddress']['top'] + "px; left:" + styling['memberaddress']['left'] + "px;width:" + styling['memberaddress']['width'] + "px;font-size:" + styling['memberaddress']['fontSize'] + "px;'>" + member_address + "</div>";
				printhtml = printhtml + "<div style='" + stationnamevisible + stationnameBold + "position:absolute;top:" + styling['stationname']['top'] + "px; left:" + styling['stationname']['left'] + "px;font-size:" + styling['stationname']['fontSize'] + "px;'>" + station_name + "</div>";
				printhtml = printhtml + "<div style='" + servicetypevisible + servicetypeBold + "position:absolute;top:" + styling['servicetype']['top'] + "px; left:" + styling['servicetype']['left'] + "px;font-size:" + styling['servicetype']['fontSize'] + "px;'>" + service_type_name + "</div>";
				printhtml = printhtml + "<div style='" + stationaddressvisible + stationaddressBold + "position:absolute;top:" + styling['stationaddress']['top'] + "px; left:" + styling['stationaddress']['left'] + "px;width:" + styling['stationaddress']['width'] + "px;font-size:" + styling['stationaddress']['fontSize'] + "px;'>" + station_address + "</div>";
				printhtml = printhtml + "<table id='itemscon' style='position:absolute;top:" + styling['itemtable']['top'] + "px;left:" + styling['itemtable']['left'] + "px;font-size:" + styling['itemtable']['fontSize'] + "px;'>";
				var drlimit = 15;
				var lamankadadr = [];
				var pagectr = 1;
				var rowctr = 1;
				drlimit = parseInt(drlimit) + 1;
				var ctrperpage = 0;
				for(var i in data_details) {
					var itemcode = data_details[i].item_code + "<small style='display:block;'>" + data_details[i].description + "</small>";
					var description = data_details[i].remarks;
					var qty = data_details[i].qty + "<span style='margin-left:5px;'>pc(s)</span>";
					if(rowctr % drlimit == 0) {
						lamankadadr[pagectr] = lamankadadr[pagectr] + "</table>";
						pagectr = parseInt(pagectr) + 1;
						ctrperpage = 0;
					}
					ctrperpage = parseFloat(ctrperpage) + parseFloat(1);
					lamankadadr[pagectr] = lamankadadr[pagectr] + "<tr ><td style='" + tdbarcodevisible + tdbarcodeBold + "position:relative;width:" + styling['tdbarcode']['width'] + "px;padding-left:" + styling['tdbarcode']['left'] + "px;'>" + itemcode + "</td><td style='" + tdqtyvisible + tdqtyBold + "position:relative;width:" + styling['tdqty']['width'] + "px;padding-left:" + styling['tdqty']['left'] + "px;'>" + qty + "</td><td style='" + tddescriptionvisible + tddescriptionBold + "position:relative;width:" + styling['tddescription']['width'] + "px;padding-left:" + styling['tddescription']['left'] + "px;'> " + itemcode + " <span style='padding-left:20px;'></span> </td><td style='" + tdpricevisible + tdpriceBold + "position:relative;width:" + styling['tdprice']['width'] + "px;padding-left:" + styling['tdprice']['left'] + "px;'>" + description + "</td><td style='" + tdtotalvisible + tdtotalBold + "position:relative;width:" + styling['tdtotal']['width'] + "px;padding-left:" + styling['tdtotal']['left'] + "px;'></td></tr>";
					rowctr = parseInt(rowctr) + 1;
				}
				if(rowctr > 0) {
					lamankadadr[pagectr] = lamankadadr[pagectr] + "</table>";
				}
				var printhtmlend = "";
				var cashier = "";
				var drnumctr = data_info.id;
				printhtmlend = printhtmlend + "<div style='" + cashiervisible + cashierBold + "position:absolute;left:" + styling['cashier']['left'] + "px;top:" + styling['cashier']['top'] + "px;font-size:" + styling['cashier']['fontSize'] + "px;'>" + cashier + "</div>";
				printhtmlend = printhtmlend + "<div style='" + remarksvisible + remarksBold + "position:absolute;left:" + styling['remarks']['left'] + "px;top:" + styling['remarks']['top'] + "px;font-size:" + styling['remarks']['fontSize'] + "px;'>" + remarks + "</div>";
				printhtmlend = printhtmlend + "<div style='" + reservedvisible + reservedBold + "position:absolute;left:" + styling['reserved']['left'] + "px;top:" + styling['reserved']['top'] + "px;font-size:" + styling['reserved']['fontSize'] + "px;'>" + reservedbyname + "</div>";
				printhtmlend = printhtmlend + "<div style='" + drnumvisible + drnumBold + "position:absolute;left:" + styling['drnum']['left'] + "px;top:" + styling['drnum']['top'] + "px;font-size:" + styling['drnum']['fontSize'] + "px;'>" + drnumctr + "</div>";
				printhtmlend = printhtmlend + "</div>";
				var finalprint = "";
				for(var i in lamankadadr) {
					finalprint = finalprint + printhtml + lamankadadr[i] + printhtmlend;
				}
				finalprint = replaceAll(finalprint, 'undefined', '');
				Popup(finalprint);

			}

			function Popup(data) {

				var mywindow = window.open('', 'new div', '');
				mywindow.document.write('<html><head><title></title>');

				mywindow.document.write('</head><body style="padding:0px;margin:0px;">');
				mywindow.document.write(data);
				mywindow.document.write('</body></html>');

				setTimeout(function() {
					mywindow.print();
					mywindow.close();
					return true;
				}, 1000);

			}

			$('body').on('click', '.btnAssignTech', function() {

				var id = $(this).attr('data-id');
				if(id) {
					var hid = "<input type='hidden' id='hid_tech_id' value='" + id + "'>";
					var input = "<input type='text' id='technician_id' class='form-control'>";
					var button = "<button id='btnSaveTech' class='btn btn-default'>SAVE</button>";
					var all = "<div class='row'><div class='col-md-12'><div class='form-group'>" + input + hid + "</div></div></div>"
					all += "<div class='row'><div class='col-md-12'><div class='form-group'>" + button + "</div></div></div>"
					$('#mbody').html(all);
					$('#myModal').modal('show');
					$("#technician_id").select2({
						placeholder: 'Search Technician',
						allowClear: true,
						minimumInputLength: 2,
						multiple: true,
						ajax: {
							url: '../ajax/ajax_json.php',
							dataType: 'json',
							type: "POST",
							quietMillis: 50,
							data: function(term) {
								return {
									q: term, functionName: 'technicians'
								};
							},
							results: function(data) {
								return {
									results: $.map(data, function(item) {
										return {
											text: item.name, slug: item.name, id: item.id
										}
									})
								};
							}
						}
					});
				}

			});

			$('body').on('click', '.btnUpdateTech', function() {

				var id = $(this).attr('data-id');
				var tech = $(this).attr('data-tech');
				if(id) {
					var hid = "<input type='hidden' id='hid_tech_id' value='" + id + "'>";
					var input = "<input type='text' id='technician_id' class='form-control'>";
					var button = "<button id='btnSaveTech' class='btn btn-default'>SAVE</button>";
					var all = "<div class='row'><div class='col-md-12'><div class='form-group'>" + input + hid + "</div></div></div>"
					all += "<div class='row'><div class='col-md-12'><div class='form-group'>" + button + "</div></div></div>"
					$('#mbody').html(all);
					$('#myModal').modal('show');
					$("#technician_id").select2({
						placeholder: 'Search Technician',
						allowClear: true,
						minimumInputLength: 2,
						multiple: true,
						ajax: {
							url: '../ajax/ajax_json.php',
							dataType: 'json',
							type: "POST",
							quietMillis: 50,
							data: function(term) {
								return {
									q: term, functionName: 'technicians'
								};
							},
							results: function(data) {
								return {
									results: $.map(data, function(item) {
										return {
											text: item.name, slug: item.name, id: item.id
										}
									})
								};
							}
						}
					});
					try{
						tech = JSON.parse(tech);
						if(tech.length){
							$('#technician_id').select2('data',tech);
						}
					} catch(e){
						console.log("Invalid json")
					}
				}

			});

			$('body').on('click', '#btnSaveTech', function() {
				var technician_id = $('#technician_id').val();
				var id = $('#hid_tech_id').val();
				if(technician_id && id) {
					$.ajax({
						url: '../ajax/ajax_query2.php',
						type: 'POST',
						data: {functionName: 'updateTechnician', id: id, technician_id: technician_id},
						success: function(data) {
							alertify.alert(data, function() {
								location.reload();
							});
						},
						error: function() {

						}
					});
				} else {
					alertify.alert('Please choose tehcnician first.');
				}
			});

			$('body').on('click', '#btnProccessService', function() {
				var id = $('#hid_det_id').val();
				var btncon = $(this);
				var btnoldval = btncon.html();
				var cartlength = $("#cart tbody tr").children().length;

				btncon.attr('disabled', true);
				btncon.html('Loading...');
				var items = [];
				alertify.confirm("Are you sure you want to continue this action?", function(e) {
					if(e) {
						$('#tblDet > tbody > tr').each(function() {
							var row = $(this);
							var item_id = row.attr('data-item_id');
							var status = row.children().eq(5).find('select').val();
							items.push({item_id: item_id, status: status});
						});
						var usedItem = [];
						if(cartlength > 0) {
							$('#cart >tbody > tr').each(function(index) {
								var row = $(this);
								var item_id = $(this).prop('id');
								var qty = row.children().eq(2).text();
								usedItem[index] = {
									item_id: item_id, qty: qty
								}
							});
						}

						$.ajax({
							url: '../ajax/ajax_query.php',
							type: 'POST',
							data: {
								functionName: 'changeStatusService',
								id: id,
								usedItem: JSON.stringify(usedItem),
								items: JSON.stringify(items)
							},
							success: function(data) {
								if(data == '1'){
									alertify.alert("Processed successfully",function(){
										location.reload();
									});
								} else {
									tempToast('info',data,'Info');
									getDetails(id);
									btncon.attr('disabled', false);
									btncon.html(btnoldval);
								}

							},
							error: function() {

								btncon.attr('disabled', false);
								btncon.html(btnoldval);
							}
						})
					} else {
						btncon.attr('disabled', false);
						btncon.html(btnoldval);
					}
				});
			});
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

			// SPARE USE LOGIC
			$('body').on('change', '.typecls', function() {
				var v = $(this).val();
				if(v == 3 || v == 4) {
					$('#itemSpareContainer').show();
					noItemInCart();
					$("#spare_use").select2({
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
											is_bundle: item.is_bundle,
											id: item.id
										}
									})
								};
							}

						}
					}).on("select2-close", function(e) {

					}).on("select2-highlight", function(e) {

					});
				} else {
					$('#itemSpareContainer').hide();
					$("#cart").find("tr:gt(0)").remove();
				}
			});

			$('body').on('click', '#add_spare_use', function() {
				var item_id_con = $('#spare_use');
				var qty_con = $('#spare_qty');
				var item_id = item_id_con.val();
				var qty = qty_con.val();
				var isoncart = false;

				$('#cart >tbody > tr').each(function() {
					var row_id = $(this).attr('id');
					if(row_id == item_id) {
						isoncart = true;
						return;
					}
				});
				if(isoncart) {
					tempToast('error', '<p>Item is already in cart</p>', '<h3>WARNING!</h3>');
					return;
				}
				if(item_id && qty) {
					if(!qty || isNaN(qty) || parseFloat(qty) < 0) {
						tempToast('error', '<p>Invalid quantity</p>', '<h3>WARNING!</h3>')
						return;
					}
					var sdata = item_id_con.select2('data');
					var item_code = sdata.text;
					var arrcode = item_code.split(':');
					removeNoItemLabel();
					var item_bc = arrcode[0];
					$('#cart > tbody').append("<tr id='" + item_id + "'><td data-title='Barcode'>" + item_bc + "</td><td data-title='Item'>" + arrcode[1] + "<br><small class='text-danger'>" + arrcode[2] + "</small></td><td data-title='Quantity'>" + qty + "</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
					item_id_con.select2('val', null);
					qty_con.val('');
				} else {
					tempToast('error', '<p>Please complete the form</p>', '<h3>WARNING!</h3>')
				}
			});
			$('body').on('click', '.btnAddRemarks', function() {
				var con = $(this);
				var id = con.attr('data-id');
				if(id) {
					var hid = "<input type='hidden' id='hid_remarks_id' value='" + id + "'>";
					var input = "<input type='text' placeholder='Remarks' id='cur_remarks' class='form-control'>";
					var button = "<button id='btnSaveRemarks' class='btn btn-default'>SAVE</button>";
					var all = "<div class='row'><div class='col-md-8'><div class='form-group'>" + input + hid + "</div></div><div class='col-md-4'><div class='form-group'>" + button + "</div></div></div>"
					all += "<br><div id='remarks_container'></div>";
					$('#mbody').html(all);
					$('#myModal').modal('show');
					getServiceRemarks(id);

				}
			});

			$('body').on('click', '.btnAddMeasurement', function() {
				var con = $(this);
				var id = con.attr('data-id');
				getMeasurement(id);
			});

			function getMeasurement(id){
				if(id) {
					$('#mbody').html('Loading...');
					$('#myModal').modal('show');
					$.ajax({
						url:'../ajax/ajax_service_item.php',
						type:'POST',
						data: {functionName:'addMeasurement',id:id},
						success: function(data){
							$('#mbody').html(data);
							$('#date_measured').datepicker({
								autoclose:true
							}).on('changeDate', function(ev){
								$('#date_measured').datepicker('hide');
							});
							$('#date_measured_to').datepicker({
								autoclose:true
							}).on('changeDate', function(ev){
								$('#date_measured_to').datepicker('hide');
							});
						},
						error:function(){

						}
					});
				}
			}
			$('body').on('click','#btnSubmitMeasurement',function(){
				var con = $(this);
				var id = con.attr('data-id');

				var date_measured = $('#date_measured').val();
				var time_measured = $('#time_measured').val();
				var date_measured_to = $('#date_measured_to').val();
				var time_measured_to = $('#time_measured_to').val();
				var troubleshooting_details = $('#troubleshooting_details').val();
				var technical_remarks = $('#technical_remarks').val();
				if(!time_measured || !time_measured_to || !date_measured || !date_measured_to){
					tempToast('error','Invalid date/time format.','Error');
					return;
				}
				var valid1 = (time_measured.search(/^\d{2}:\d{2}$/) != -1);
				var valid2 = (time_measured_to.search(/^\d{2}:\d{2}$/) != -1);
				if(!valid1 || !valid2){
					tempToast('error','Invalid time format. Must be 24 hr format (07:35 or 15:45)','Error');
					return;
				}


				var arr = [];
				$('.formMeasurement').each(function(){
					var input = $(this);
					var service_id = input.attr('data-id');
					var val = input.val();
					arr.push({id:service_id,val:val});
				});
				if(arr){
					$.ajax({
						url:'../ajax/ajax_service_item.php',
						type:'POST',
						data: {functionName:'submitMeasurement',id:id,time_measured:time_measured,time_measured_to:time_measured_to,technical_remarks:technical_remarks,troubleshooting_details:troubleshooting_details,date_measured_to:date_measured_to,date_measured:date_measured,arr:JSON.stringify(arr)},
						success: function(data){
							tempToast('info',data,'Info');
							getMeasurement(id);
						},
						error:function(){

						}
					})
				}

			});
			function getServiceRemarks(id) {
				$.ajax({
					url: '../ajax/ajax_query.php', type: 'POST', beforeSend: function() {
						$('#remarks_container').html('Loading...')
					}, data: {functionName: 'getServiceRemarks', id: id}, success: function(data) {
						$('#remarks_container').html(data);
					}, error: function() {

					}
				})
			}

			$('body').on('click', '#btnSaveRemarks', function() {
				var remarks = $('#cur_remarks').val();
				var id = $('#hid_remarks_id').val();
				var btncon = $(this);
				var oldval = btncon.html();
				btncon.attr('disabled', true);
				btncon.html('Loading...');
				if(!remarks) {
					tempToast('error', '<p>Invalid remarks</p>', '<h3>WARNING!</h3>')
				} else {
					$.ajax({
						url: '../ajax/ajax_query.php',
						type: 'POST',
						data: {functionName: 'addServiceRemarks', id: id, remarks: remarks},
						success: function(data) {
							tempToast('info', '<p>' + data + '</p>', '<h3>Info!</h3>');
							$('#cur_remarks').val('');
							getServiceRemarks(id);
							btncon.attr('disabled', false);
							btncon.html(oldval);
						},
						error: function() {
							tempToast('error', '<p>Error occur. Please try again.</p>', '<h3>Info!</h3>');
							btncon.attr('disabled', false);
							btncon.html(oldval);
						}
					})
				}
			});
			$('body').on('click', '.btnRequestItem', function() {
				var con = $(this);
				var id = con.attr('data-id');
				var branch_id = con.attr('data-branch_id');
				$('#myModalRequest').modal('show');
				$('#request_id').val(id);
				$('#hid_request_branch_id').val(branch_id);
				$("#cart_request").find("tr:gt(0)").remove();
				noItemInCartRequest();
			});

			$('body').on('click','.btnRequestedItem',function(){
				var id = $(this).attr('data-id');
				var branch_name = $(this).attr('data-branch_name');
				$('#mbody').html('Loading...');
				$('#myModal').modal('show');
				$.ajax({
					url: '../ajax/ajax_service_item.php',
					type: 'POST',
					data: {functionName: 'getServiceDetails', id: id,branch_name:branch_name},
					success: function(data) {
						$('#mbody').html(data);
					},
					error: function() {

					}
				});
			});

			$('body').on('click', '#btnAddRequestItem', function() {
				var item_con = $('#request_item_id');
				var qty_con = $('#request_qty');
				var item_id = item_con.val();
				var qty = qty_con.val();
				var con = $(this);
				var oldval = con.html();
				var sdata = item_con.select2('data');
				var item_code = sdata.text;
				var arrcode = item_code.split(':');
				var item_bc = arrcode[0];
				var branch_id = $('#hid_request_branch_id').val();
				var length = $('#cart_request > tbody > tr').length;
				if(parseFloat(length) < 15){
					// check first the stocks
					$.ajax({
						url:'../ajax/ajax_service_item.php',
						type:'POST',
						data: {functionName:'checkAvailabilityOfItem',item_id:item_id,branch_id:branch_id,qty:qty},
						success: function(data){
							if(data == 1){
								removeNoItemLabelRequest();
								qty_con.val('');
								item_con.select2('val', null);
								var btn_bundle = '';
								if(sdata.is_bundle == 1){
									btn_bundle = "<button class='btn btn-default btn-sm btnShowBundle'><i class='fa fa-list'></i></button>";
								}
								$('#cart_request > tbody').append("<tr id='" + item_id + "'><td data-title='Barcode'>" + item_bc + "</span></td><td data-title='Item'>" + arrcode[1] + "<br><small class='text-danger'>" + arrcode[2] + "</small></td><td data-title='Quantity'>" + qty + "</td><td><button class='btn btn-default btn-sm removeItemRequest'><span  class='glyphicon glyphicon-remove-sign'></span></button> "+btn_bundle+"</td></tr>");

							} else {
								alertify.alert(data);
							}
						},
						error:function(){

						}
					});
				} else {
					alertify.alert("Maximum number of items reach.");
				}

			});
			$('body').on('click','.btnShowBundle',function(){
				var row = $(this).parents('tr');
				var item_id = row.attr('id');
				$('#myModalBundle').modal('show');
				$('#bbody').html("Loading...");
				$.ajax({
					url: '../ajax/ajax_query2.php',
					type: 'POST',
					data: {functionName: 'getBundleItem', item_id: item_id},
					success: function(data) {
						$('#bbody').html(data);
					},
					error: function() {
						alert('Error Occur');
					}
				});
			});

			$('body').on('click', '#saveItemRequest', function() {

				var id = $('#request_id').val();
				var btncon = $(this);
				var cartlength = $("#cart_request tbody tr").children().length;

				if(cartlength > 0) {
					button_action.start_loading(btncon);
					var arr = [];
					$('#cart_request tbody tr').each(function() {
						var row = $(this);
						var item_id = row.attr('id');
						var is_bundle = row.attr('is_bundle');
						var qty = row.children().eq(2).text();
						arr.push({item_id: item_id, qty: qty,is_bundle:is_bundle});
					});
					if(arr.length > 0) {
						$.ajax({
							url: '../ajax/ajax_service_item.php',
							type: 'POST',
							data: {functionName: 'saveServiceItem', arr: JSON.stringify(arr), id: id},
							success: function(data) {
								alertify.alert(data,function(){
									location.href='item-service.php';
								});

							},
							error: function() {

							}
						});
					}
				} else {
					tempToast('error', '<p>Enter item first</p>', '<h3>Info!</h3>');
				}
			});

			function noItemInCartRequest() {
				if(!$("#cart_request tbody").children().length) {
					$("#cart_request tbody").append("<td colspan='3' id='noitem_request' style='padding-top:10px;' data-title='Item'><span class='text-danger'>NO ITEMS IN CART</span></td>");
				}
			}

			function removeNoItemLabelRequest() {
				$("#noitem_request").remove();
			}

			$('body').on('click', '.removeItemRequest', function() {
				$(this).parents('tr').remove();
				noItemInCartRequest();
			});

			$('body').on('click','.btnReleaseItem',function(){
				var id = $(this).attr('data-id');
				var branch_name = $(this).attr('data-branch_name');


				$('#mbody').html('Loading...');
				$('#myModal').modal('show');
				$.ajax({
					url:'../ajax/ajax_service_item.php',
					type:'POST',
					data: {functionName:'serviceRelease',id:id,branch_name:branch_name},
					success: function(data){
						$('#mbody').html(data);
					},
					error:function(){

					}
				})
			});

			function sortByStockman(a,b){
				var aName = a.stock_man.toLowerCase();
				var bName = b.stock_man.toLowerCase();
				return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
			}
			$('body').on('click','#btnReleasePrint',function(){
				var con = $(this);
				var id = con.attr('data-id');
				var member = con.attr('data-client');
				var address = con.attr('data-address');
				var racks = con.attr('data-racks');
				var branch_name = con.attr('data-branch_name');
				var tech = con.attr('data-tech');
				var date = con.attr('data-date');
				var page = "<div class='perpage' style='page-break-after:always;' >";
				page += "<h1 class='text-center'>"+localStorage['company_name']+"</h1>";
				page += "<p class='text-center text-muted'></p>";
				page += "<p style='font-size:10px;'  class='text-right'>SERVICE ID# <span style='width:80px;display:inline-block;margin-left:5px;' class='text-left'>" +id+"</span></p>";
				page += "<div class=''>";
				page += "<div class='pull-right'>";
				page += "<p style='font-size:10px;' >Date: <span style='width:270px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:13px;'>" + date + "</span></p>";
				page += "</div>";
				page += "<p style='font-size:10px;' >Branch: <span style='width:270px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:13px;'>"+branch_name+"</span></p>";
				page += "</div>";
				page += "<div class=''>";
				page += "<div class='pull-right'>";
				page += "<p style='font-size:10px;' >Requested by: <span style='width:270px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:13px;'>" + tech + "</span></p>";
				page += "</div>";
				page += "<p style='font-size:10px;' >Technician: <span style='width:270px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:13px;'>"+tech+"</span></p>";
				page += "<p style='font-size:10px;' >Client: <span style='width:680px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:13px;'>"+member+"</span></p>";
				page += "<p style='font-size:10px;' >Address: <span style='width:660px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:13px;'>"+address+"</span></p>";
				page += "</div>";
				page += "<hr>";
				page += "<table style='font-size:10px;'  class='table table-bordered'>";
				page += "<tr><th>Item</th><th>Quantity</th><th>Racking</th></tr>";
				var pageitem = [];
				var ctr = 1;
				var strholder = '';
				var arrStockman = [];
				var finalarr = [];
				var cur_order = JSON.parse(racks);
				for(var i in cur_order){
					finalarr.push({stock_man:cur_order[i].stock_man,rack:cur_order[i].rack,qty:cur_order[i].qty,item_code:cur_order[i].item_code,description:cur_order[i].description});
				}
				finalarr.sort(sortByStockman);

				for(var j in finalarr){
					strholder += "<tr style='min-height:50px;'><td style='width:250px;'>" +finalarr[j].item_code+"<br><small class='text-danger'>" + finalarr[j].description + "</small></td><td>"+finalarr[j].qty+"</td><td style='width:400px;'>";
					strholder += "<div>"+finalarr[j].stock_man+" : " +finalarr[j].rack+"</div>";
					strholder += "</td></tr>";
					if(ctr % 12 == 0) {
						pageitem.push(strholder);
						strholder = '';
					}
					ctr += 1;
				}
				var num = Math.ceil((ctr / 12) * 12);
				if(ctr < 12) {
					while(ctr != num + 1) {
						strholder += "<tr style='height:50px;'><td></td><td></td><td></td></tr>";
						ctr += 1;
					}
					pageitem.push(strholder);
					strholder = '';
				} else {
					while(ctr != num + 1) {
						strholder += "<tr style='height:50px;'><td></td><td></td><td></td></tr>";
						ctr += 1;
					}
					pageitem.push(strholder);
					strholder = '';
				}
				var endtable = '</table>';
				var pageend = "";
				pageend += "<p>Released By: <span style='width:300px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:5px;'></span></p>";
				pageend += "<p>Checked By: <span style='width:300px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:13px;'></span></p>";
				pageend += "</div>";
				var countpages = pageitem.length;
				var pageof = 1;
				var finalhtml = "";
				for(var j in pageitem) {
					finalhtml += page;
					finalhtml += pageitem[j];
					finalhtml += endtable;
					finalhtml +=  "<p class='text-center' style='color:#ccc;font-size:0.8em;'>Page "+pageof+" of "+countpages+"</p>";
					pageof += 1;
					finalhtml += pageend;
				}
				popUpPrintWithStyle(finalhtml);
			});

			$('body').on('click','#btnReleaseItem',function(){
				var con = $(this);
				var id = con.attr('data-id');
				var racks = con.attr('data-racks');
				button_action.start_loading(con);

				$.ajax({
					url:'../ajax/ajax_service_item.php',
					type:'POST',
					data: {functionName:'releaseItem',id:id,racks:racks},
					success: function(data){
						alertify.alert(data,function(){
							location.href='item-service.php';
						});
					},
					error:function(){

					}
				});

			});


			$('body').on('click','#btnLiquidate',function(){

				var len = $('#tbl_service_request tbody tr').length;
				var con = $(this);
				var id = con.attr('data-id');

				if(len > 0){

					var is_valid =  true;
					var arr = [];

					$('#tbl_service_request tbody tr').each(function(){
						var row = $(this);
						var item_id = row.attr('data-item_id');
						var qty = row.children().eq(2).text();
						var con_qty = row.children().eq(3).find('input').val();
						if(con_qty == '' || isNaN(con_qty)){
							is_valid = false;
						}
						arr.push({item_id:item_id,qty:qty,con_qty:con_qty});
					});

					if(is_valid){

						button_action.start_loading(con);

						$.ajax({
							url:'../ajax/ajax_service_item.php',
							type:'POST',
							data: {functionName:'liquidateItem',id:id,arr:JSON.stringify(arr)},
							success: function(data){
								alertify.alert(data,function(){
									location.href='item-service.php';
								});
							},
							error:function(){

							}
						});

					} else {
						tempToast('error', '<p>Invalid quantity</p>', '<h3>Error!</h3>');
					}

				}

			});

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

			$('body').on('click','#btnPrintCreditMemo',function(){
				var data = $('#print_data').val();
				var is_credit = $(this).attr('data-credit');
				try {

					data = JSON.parse(data);
					PrintCreditMemo(data,is_credit);

				} catch(e) {

				}
			});
			function PrintCreditMemo(data,is_credit){
				var main = data.main;
				var details = data.details;
				var company_name = localStorage['company_name'];
				var html = "";
				var credited_amount = $('#override_credited').val();
				var date_obj = new Date();
				var  curDate = (parseInt(date_obj.getMonth()) + parseInt(1) ) + "/" + date_obj.getDate() + "/" + date_obj.getFullYear();
				var lbl_title='Service Request';
				var service_type_name = main['service_type_name'];
				var sales_type_name = main['sales_type_name'];

				if(is_credit == 1){
					lbl_title = 'Credit Memo';
				}
				if(service_type_name == 'MRF'){
					service_type_name = 'Merchandise Return Form';
					lbl_title = '';
				} else if (service_type_name =='IF'){
					service_type_name = 'Installation Form';
					lbl_title = '';
				}

				var img = "";
				var areaform = "&nbsp;";
				var backload_ref_number ="";
				var is_cebuhiq = $('#is_cebuhiq').val();
				if(is_cebuhiq == 1) {



					img = "<img style='position:fixed;top:10px;left:10px;' src='http://" + $('#_HOST').val() + "/pos/css/img/logo.jpg' />";
					areaform = "Area: " + main['sales_type_name'];
					if(main['backload_ref_number']) {
						backload_ref_number = "Ref Number: " + main['backload_ref_number'];
					}


					html += img;
					html += "<h3 class='text-center'>" + company_name + "</h3>";
					html += "<p class='text-center'>" + service_type_name + "</p>";
					html += "<p class='text-center'>" + lbl_title + "</p>";
					html += "<p style='float:left;width:80%'>&nbsp;</p>";

					html += "<p style='float:left;width:19%;' >ID: " + main['service_id'] + "</p>";
					html += "<p style='float:left;width:80%'>&nbsp;</p>";
					html += "<p style='float:left;width:19%;'>Date: " + curDate + "</p>";
					html += "<div sytle='clear:both;'>";
					html += "<br>";
					html += "<div style='float:left;width:47%;'>";
					html += "<p> Client: " + main['member_name'] + "</p>";
					html += "</div>";
					html += "<div style='float:left;width:47%;'>";
					html += "<p> Contact: " + main['member_contact'] + "</p>";
					html += "</div>";
					html += "<div style='float:left;width:47%;'>";
					html += "<p> Address: " + main['member_address'] + "</p>";
					html += "</div>";
					html += "<div style='float:left;width:47%;'>";
					html += "<p>" + areaform + "</p>";
					html += "</div>";
					html += "<div style='float:left;width:47%;'>";
					html += "<p>" + backload_ref_number + "</p>";
					html += "</div>";
					html += "<br>";
					html += "<table class='table table-bordered table-condensed'>";
					html += "<tr>";
					html += "<th>Description</th><th>Qty</th><th>Unit</th><th>Price</th>";
					html += "<th>Discount</th>";



					html += "<th>Total</th></tr>";
					var total = 0;
					for(var i in details) {
						var t = (parseFloat(details[i]['price']) * parseFloat(details[i]['qty']));
						total = parseFloat(t) + parseFloat(total);

						html += "<tr><td>" + details[i]['description'] + "</td><td>" + number_format(details[i]['qty']) + "</td><td>" + details[i]['remarks'] + "</td>";

						var discount = 0;
						var price = details[i]['price'];

						if(details[i]['price'] != details[i]['orig_price']) {
							discount =  (t) -  (details[i]['price'] * details[i]['qty']);
							price = details[i]['price'];
						}

						html += "<td class='text-right'>" + number_format(price, 2) + "</td><td class='text-right'>" + number_format(discount, 2) + "</td><td class='text-right'>" + number_format(t, 2) + "</td></tr>";

					}
					html += "<tr><td></td><td></td><td></td><td></td>";

					html += "<td class='text-right'>Total: </td><td class='text-right'>" + number_format(total, 2) + "</td></tr>";
					html += "</table>";
					html += "<br>";
					html += "<br>";

					html += "<p style='float:left;width:33%;' >Prepared by: ____________________</p>";
					html += "<p style='float:left;width:33%'>Received by: ____________________</p>";
					html += "<p style='float:left;width:33%'>Approved by: ____________________</p>";

					html += "<div sytle='clear:both;'>";


				} else {

					html += "<h3 class='text-center'>"+company_name+"</h3>";
					html += "<p class='text-center'>"+service_type_name+"</p>";
					html += "<p class='text-center'>"+lbl_title+"</p>";
					html += "<p style='float:left;width:80%'>&nbsp;</p>";

					html += "<p style='float:left;width:19%;' >ID: "+main['service_id']+"</p>";
					html += "<p style='float:left;width:80%'>&nbsp;</p>";
					html += "<p style='float:left;width:19%;'>Date: "+curDate+"</p>";
					html += "<div sytle='clear:both;'>";
					html += "<br>";
					html += "<h4>"+main['member_name']+"</h4>";
					html += "<p>"+main['member_address']+"</p>";
					html += "<p>"+main['member_contact']+"</p>";
					html += "<br>";
					html += "<table class='table table-bordered table-condensed'>";
					html += "<tr><th>Qty</th><th>Item</th><th>Description</th><th></th><th></th></tr>";
					for(var i in details){
						html += "<tr><td>"+details[i]['qty']+"</td><td>"+ (details[i]['item_code'] ? details[i]['item_code'] : 'No item')+"</td><td>"+details[i]['description']+"</td><td>"+number_format(details[i]['price'],2)+"</td><td>"+number_format((parseFloat(details[i]['price']) * parseFloat(details[i]['qty'])) ,2)+"</td></tr>";
					}
					html += "</table>";
					html += "<br>";
					if(is_credit == 1) html += "<h4 class='text-right'> Total: "+number_format(credited_amount,2)+"</h4>";
				}
				popUpPrintWithStyle(html);

			}



			function PrintCreditMemoBak(data,is_credit){
				var main = data.main;
				var details = data.details;
				var company_name = localStorage['company_name'];
				var html = "";
				var credited_amount = $('#override_credited').val();
				var date_obj = new Date();
				var  curDate = (parseInt(date_obj.getMonth()) + parseInt(1) ) + "/" + date_obj.getDate() + "/" + date_obj.getFullYear();
				var lbl_title='Service Request';
				if(is_credit == 1){
					lbl_title = 'Credit Memo';
				}
				var service_type_name = main['service_type_name'];

				if(service_type_name == 'MRF'){
					service_type_name = 'Merchandise Return Form';
					lbl_title = '';
				} else if (service_type_name =='IF'){
					service_type_name = 'Installation Form';
					lbl_title = '';
				}

				html += "<h3 class='text-center'>"+company_name+"</h3>";
				html += "<p class='text-center'>"+service_type_name+"</p>";
				html += "<p class='text-center'>"+lbl_title+"</p>";
				html += "<p style='float:left;width:80%'>&nbsp;</p>";

				html += "<p style='float:left;width:19%;' >ID: "+main['service_id']+"</p>";
				html += "<p style='float:left;width:80%'>&nbsp;</p>";
				html += "<p style='float:left;width:19%;'>Date: "+curDate+"</p>";
				html += "<div sytle='clear:both;'>";
				html += "<br>";
				html += "<h4>"+main['member_name']+"</h4>";
				html += "<p>"+main['member_address']+"</p>";
				html += "<p>"+main['member_contact']+"</p>";
				html += "<br>";
				html += "<table class='table table-bordered table-condensed'>";
				html += "<tr><th>Qty</th><th>Item</th><th>Description</th><th></th><th></th></tr>";
				for(var i in details){
					html += "<tr><td>"+details[i]['qty']+"</td><td>"+ (details[i]['item_code'] ? details[i]['item_code'] : 'No item')+"</td><td>"+details[i]['description']+"</td><td>"+number_format(details[i]['price'],2)+"</td><td>"+number_format((parseFloat(details[i]['price']) * parseFloat(details[i]['qty'])) ,2)+"</td></tr>";
				}
				html += "</table>";
				html += "<br>";
				if(is_credit == 1) html += "<h4 class='text-right'> Total: "+number_format(credited_amount,2)+"</h4>";
				popUpPrintWithStyle(html);

			}

			$('body').on('click','#printRequestedItem',function(){

				var date_obj = new Date();
				var  curDate = (parseInt(date_obj.getMonth()) + parseInt(1) ) + "/" + date_obj.getDate() + "/" + date_obj.getFullYear();
				var con = $(this);
				var branch_name = con.attr('data-branch_name');
				var technician = con.attr('data-tech');
				var client_name = con.attr('data-client');
				var request_by = $('#service_request_by').html();
				var rf_id = $('#rf_id').val();
				var id = con.attr('data-id');
				var member_address = con.attr('data-address');

				var contact_person = con.attr('data-contact_person');
				var contact_number = con.attr('data-contact_number');
				var contact_address = con.attr('data-contact_address');


				if(client_name && contact_person){
					client_name += " / " +contact_person;
				}  else if (!client_name && contact_person){
					client_name = contact_person;
				}


				if(member_address && contact_address){
					member_address += " / " +contact_address;
				} else if (!member_address && contact_address){
					member_address = contact_address;
				}

				$.ajax({
				    url:'../ajax/ajax_service_item.php',
				    type:'POST',
				    data: {functionName:'updateRFID',id:id,rf_id:rf_id},
				    success: function(data){

				    },
				    error:function(){

				    }
				});

				var page = "<div class='perpage' style='page-break-after:always;' >";
				page += "<h1 class='text-center'>"+localStorage['company_name']+"</h1>";
				page += "<p class='text-center text-muted'></p>";
				page += "<p class='text-right'>SERVICE ID# <span style='width:80px;display:inline-block;margin-left:5px;' class='text-left'>" +id+"</span></p>";
				page += "<div class=''>";
				page += "<div class='pull-right'>";
				page += "<p>Date: <span style='width:270px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:13px;'>" + curDate + "</span></p>";
				page += "</div>";
				page += "<p>Branch: <span style='width:270px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:13px;'>   "+branch_name+"</span></p>";
				page += "</div>";
				page += "<div class=''>";
				page += "<div class='pull-right'>";
				page += "<p>Request By: <span style='width:270px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:13px;'>" + request_by + "</span></p>";
				page += "</div>";
				page += "<p>Technician: <span style='width:270px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:13px;'>"+technician+"</span></p>";
				page += "</div>";
				page += "<div class=''>";
				page += "<div class='pull-left'>";
				page += "<p>Client: <span style='width:600px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:13px;'>" + client_name + "</span></p>";
				page += "</div>";
				page += "</div>";
				page += "<div class=''>";
				page += "<div class='pull-left'>";
				page += "<p>Address: <span style='width:590px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:13px;'>" + member_address + "</span></p>";
				page += "</div>";
				page += "</div>";
				page += "<div style='clear:both;'></div>";
				page += "<table class='table table-bordered' style='font-size:10px;'>";
				page += "<tr><th>Item</th><th>Quantity</th><th>Used Item</th><th>Unused Item</th><th>Price</th><th>Total</th></tr>";

				var pageitem = [];
				var ctr = 1;
				var strholder = '';
				var arrStockman = [];
				var is_cebuhiq = $('#is_cebuhiq').val();

				$('#tbl_service_request tbody tr').each(function(){
					var row = $(this);
					var item_code = row.children().eq(0).text();
					var description =  row.children().eq(1).text();
					var qty = row.children().eq(2).text();
					var price ='';
					var total = '';

					if(is_cebuhiq == 1){

						price = row.attr('data-price');
						total = parseFloat(price) * parseFloat(qty);
						price = number_format(price,2);
						total = number_format(total,2);

					} else {

						price ='';
						total = '';

					}


					strholder += "<tr style='min-height:50px;'><td style='width:250px;'>" +item_code+"<br><small class='text-danger'>" + description + "</small></td><td>"+qty+"</td><td></td><td></td><td>"+price+"</td><td>"+total+"</td>";
					strholder += "</tr>";

					if(ctr % 10 == 0) {
						pageitem.push(strholder);
						strholder = '';
					}

					ctr += 1;

				});

				var num = Math.ceil((ctr / 10) * 10);
				if(ctr < 10) {
					while(ctr != num + 1) {
						strholder += "<tr style='height:50px;'><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
						ctr += 1;
					}
					pageitem.push(strholder);
					strholder = '';
				} else {
					while(ctr != num + 1) {
						strholder += "<tr style='height:50px;'><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
						ctr += 1;
					}
					pageitem.push(strholder);
					strholder = '';
				}
				var endtable = '</table>';
				var pageend = "";
				pageend += "<br><p>Processed By: <span style='width:300px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:5px;'></span></p>";
				pageend += "<br><p>Received By: <span style='width:300px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:5px;'></span></p>";
				pageend += "<br><p>Released By: <span style='width:300px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:5px;'></span></p>";
				//	pageend += "<p>Received By: <span style='width:300px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:13px;'></span></p>";
				pageend += "</div>";
				var countpages = pageitem.length;
				var pageof = 1;
				var finalhtml = "";
				for(var j in pageitem) {
					finalhtml += page;
					finalhtml += pageitem[j];
					finalhtml += endtable;
					finalhtml +=  "<p class='text-center' style='color:#ccc;font-size:0.8em;'>Page "+pageof+" of "+countpages+"</p>";
					pageof += 1;
					finalhtml += pageend;
				}


				popUpPrintWithStyle(finalhtml);
			});
			$('body').on('click','#btn_update_pullout_schedule',function(){
				var con = $(this);
				var dt = $('#update_pullout_schedule').val();
				var id = con.attr('data-id');
				if(!dt){
					tempToast('error', '<p>Please enter pullout date.</p>', '<h3>Warning!</h3>');
				}else {
					button_action.start_loading(con);
					alertify.confirm("Are you sure you want to continue with this action?",function(e){
						if(e){
							$.ajax({
								url:'../ajax/ajax_service_item.php',
								type:'POST',
								data: {functionName:'updatePulloutSchedule',dt:dt,id:id},
								success: function(data){
									tempToast('info', '<p>'+data+'</p>', '<h3>Info!</h3>');
									button_action.end_loading(con);
									location.href='item-service.php';
								},
								error:function(){
									button_action.end_loading(con);
								}
							});
						} else {
							button_action.end_loading(con);
						}
					});
				}
			});

		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>