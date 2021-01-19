<?php
	// $user have all the properties and method of the current user


	require_once '../includes/admin/page_head2.php';

	if(!$user->hasPermission('item_service_r')) {
		// redirect to denied page
		Redirect::to(1);
	}
	$http_host = $_SERVER['HTTP_HOST'];

	$is_cebuhiq = Configuration::thisCompany('cebuhiq');

?>

	<!-- Page content -->

	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->

	<div class="page-content inset">
	<div class="content-header">
		<h1>
			<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Service Log
		</h1>
	</div>


	<?php

		// get flash message if add or edited successfully

		if(Session::exists('flash')) {
			echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
		}

	?>

	<?php include 'includes/service_nav.php'; ?>

	<div id="test"></div>

	<input type="hidden" value="<?php echo  $http_host; ?>" id='_HOST'>
	<input type="hidden" value="<?php echo ($is_cebuhiq) ? 1 : 0; ?>" id='is_cebuhiq'>
	<div class="row">
		<div class="col-md-12">


			<div class="panel panel-primary">
				<!-- Default panel contents -->
				<div class="panel-heading">Service Log</div>
				<div class="panel-body">
					<input type="hidden" id='is_cebuhiq' value='<?php echo Configuration::thisCompany('cebuhiq') ? 1 : 0; ?>'>
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<select id="branch_id" name="branch_id" class="form-control">
									<option value=''></option>
									<?php
										$branch = new Branch();
										$branches =  $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));
										foreach($branches as $b){

											?>
											<option value='<?php echo $b->id ?>'><?php echo $b->name;?> </option>
											<?php
										}
									?>
								</select>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<select name="service_type" id="service_type" class='form-control'>
									<option value=""></option>
									<option value="1">Walk In Service</option>
									<option value="2">For Pullout</option>
									<option value="3">On site service</option>
								</select>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<input type="text" id='member_id' class='form-control' >
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<input type="text" id='user_id' class='form-control' >
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<input id='technician_id' type="text" class='form-control'>

							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<input type="text" class='form-control' id='date_from' placeholder='Date From'>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<input type="text" class='form-control' id='date_to' placeholder='Date To'>
							</div>
						</div>
						<?php
							$service_type = new Service_type();
							$service_types = $service_type->get_active('service_types',['1','=','1']);
							if($service_types){
								?>
								<div class="col-md-3">
									<div class="form-group">
										<select name="service_type_2" id="service_type_2" class='form-control'>
											<option value=""></option>
											<?php foreach($service_types as $st2){
												?>
												<option value="<?php echo $st2->id; ?>"><?php echo $st2->name; ?></option>
												<?php
											} ?>
										</select>
									</div>
								</div>
								<?php
							}
						?>

						<div class="col-md-3">
							<div class="form-group">
								<button id='btnFilter' class='btn btn-default'>Filter</button>
								<button id='btnPrint' class='btn btn-default'>Print</button>
								<button id='btnDownload' class='btn btn-default'>Download</button>
							</div>
						</div>
					</div>
					<input type="hidden" id="hiddenpage" />
					<div id="holder"></div>
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


	<script>

		$(function(){
			var is_cebuhiq = $('#is_cebuhiq').val();

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
			$('#service_type').select2({
				placeholder: 'Search Type',
				allowClear: true
			});
			$('#service_type_2').select2({
				placeholder: 'Request Type',
				allowClear: true
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
									text: item.lastname + ", " + item.firstname + " " + item.middlename,
									slug: item.lastname + ", " + item.firstname + " " + item.middlename,
									id: item.id
								}
							})
						};
					}
				}
			});
			$("#user_id").select2({
				placeholder: 'Search User',
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
				placeholder:'Choose branch',
				allowClear:true
			});

			getPage(0);

			$('body').on('click','.paging',function(e){
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});

			$('body').on('click','#btnFilter',function(){
				getPage(0);
			});
			$('body').on('click','.btnDetails',function(){
				var id = $(this).attr('data-id');
				if(id){
					$('#myModal').modal('show');
					getDetails(id);
				}
			});
			function getDetails(id){
				$('#myModal').modal('show');
				var terminal_id = localStorage['terminal_id'];

				$.ajax({
					url:'../ajax/ajax_query2.php',
					type:'POST',
					beforeSend:function(){
						$('#mbody').html('Loading...');
					},
					data: {functionName:'itemServiceDetails',id:id,terminal_id:terminal_id,isLog:1},
					success: function(data){
						$('#mbody').html(data);
					},
					error:function(){

						$('.myModal').modal('hide');
					}
				})
			}

			function getPage(p){
				var b = $('#branch_id').val();
				var member_id = $('#member_id').val();
				var user_id = $('#user_id').val();
				var date_from = $('#date_from').val();
				var date_to = $('#date_to').val();
				var service_type = $('#service_type').val();
				var service_type_2 = $('#service_type_2').val();
				var technician_id = $('#technician_id').val();

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,b:b,service_type:service_type,service_type_2:service_type_2,technician_id:technician_id,user_id:user_id,date_from:date_from,date_to:date_to,member_id:member_id,functionName:'serviceItemLog',cid: <?php echo $user->data()->company_id; ?> },
					success: function(data){

						$('#holder').html(data);

					}
				});
			}
			function popUpWithStyle(data){
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
			function popUpPrint(data){
				var mywindow = window.open('', 'new div', '');
				mywindow.document.write('<html><head><title></title><style></style>');
				mywindow.document.write('</head><body style="padding:0;margin:0;">');
				mywindow.document.write(data);
				mywindow.document.write('</body></html>');

				mywindow.print();
				mywindow.close();

				return true;
			}
			$('body').on('click','#btnPrint',function(){
				var b = $('#branch_id').val();
				var member_id = $('#member_id').val();
				var user_id = $('#user_id').val();
				var date_from = $('#date_from').val();
				var date_to = $('#date_to').val();
				var service_type = $('#service_type').val();
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('.loading').show();
					},
					data:{page:0,for_printing:1,b:b,service_type:service_type,user_id:user_id,date_from:date_from,date_to:date_to,member_id:member_id,functionName:'serviceItemLog',cid: <?php echo $user->data()->company_id; ?> },
					success: function(data){
						$('.loading').hide();
						popUpWithStyle(data);
					}
				});
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
					console.log(data)
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
							if(price != 14){
								discount =  ((t/ details[i]['qty']) -  (details[i]['orig_price'] / details[i]['qty'])) * details[i]['qty'];
							}

							if(details[i]['orig_price'] != '0.00'){
								price = details[i]['orig_price'] / details[i]['qty'];
							} else {
								price = details[i]['price'];
							}

						}
						html += "<td class='text-right'>" + number_format(price, 2) + "</td><td class='text-right'>" + number_format(discount, 2) + "</td><td class='text-right'>" + number_format(t, 2) + "</td></tr>";



					}
					html += "<tr><td></td><td></td><td></td><td></td>";

					html += "<td class='text-right'>Total: </td><td class='text-right'>" + number_format(total, 2) + "</td></tr>";
					html += "</table>";
					html += "<br>";
					html += "<br>";

					html += "<p style='float:left;width:33%;' >Prepared by: ____________________</p>";


					html += "<div sytle='clear:both;'>";


				} else {

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
					html += "<th>Qty</th><th>Item</th><th>Description</th><th>Remarks</th><th>Price</th>";



					html += "<th>Total</th></tr>";
					var total = 0;
					for(var i in details) {
						var t = (parseFloat(details[i]['price']) * parseFloat(details[i]['qty']));
						total = parseFloat(t) + parseFloat(total);
						html += "<tr><td>" + number_format(details[i]['qty']) + "</td><td>" + details[i]['item_code'] + "</td><td>" + details[i]['description'] + "</td><td>" + details[i]['remarks'] + "</td>";

						html += "<td class='text-right'>" + number_format(details[i]['price'], 2) + "</td><td class='text-right'>" + number_format(t, 2) + "</td></tr>";


					}
					html += "<tr><td></td><td></td><td></td><td></td>";

					html += "<td class='text-right'>Total: </td><td class='text-right'>" + number_format(total, 2) + "</td></tr>";
					html += "</table>";
					html += "<br>";
					html += "<br>";

					html += "<p style='float:left;width:33%;' >Returned by: ____________________</p>";
					html += "<p style='float:left;width:33%'>Received by: ____________________</p>";
					html += "<p style='float:left;width:33%'>Approved by: ____________________</p>";

					html += "<div sytle='clear:both;'>";

					if(is_credit == 1) html += "<h4 class='text-right'> Total: " + number_format(credited_amount, 2) + "</h4>";

				}
				popUpPrintWithStyle(html);

			}
			$('body').on('click','#btnDownload',function(){
				var b = $('#branch_id').val();
				var member_id = $('#member_id').val();
				var user_id = $('#user_id').val();
				var date_from = $('#date_from').val();
				var date_to = $('#date_to').val();
				var service_type = $('#service_type').val();
				var service_type_2 = $('#service_type_2').val();

				window.open(
					'excel_downloader.php?downloadName=serviceLog&b='+b+'&member_id='+member_id+'&user_id='+user_id+'&date_from='+date_from+'&date_to='+date_to+'&service_type='+service_type+'&service_type_2='+service_type_2,
					'_blank' // <- This is what makes it open in a new window.
				);

			});

			$('#date_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#date_from').datepicker('hide');
			});
			$('#date_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#date_to').datepicker('hide');
			});
			$("#technician_id").select2({
				placeholder: 'Search Technician',
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
			$('body').on('click','#btnSubmitOrder',function(){
				var con = $(this);
				var id = con.attr('data-id');
				var chk = $('#chkCashierTransaction').is(':checked');
				var walkin = $('#chkWalkInApproval').is(':checked');
				var chk_issue_sr = $('#chkIssueServiceReceipt').is(':checked');
				var chk_issue_ts = $('#chkIssueTS').is(':checked');
				var is_service_item = $('#is_service_item').val();

				var cashier_trans = 0;
				var walk_in = 0;
				var issue_sr = 0;
				var issue_ts = 0;

				if(chk){
					cashier_trans = 1;
				}

				if(walkin){
					walk_in = 1;
				}

				if(chk_issue_sr){
					issue_sr = 1;
				}
				if(chk_issue_ts){
					issue_ts = 1;
				}


				var terminal_id = localStorage['terminal_id'];

				if(is_cebuhiq == 1 && (!terminal_id || terminal_id == '0')){
					alert("Please set terminal first.");
					return;
				}

				$.ajax({
					url:'../ajax/ajax_query2.php',
					type:'POST',
					data: {functionName:'submitOrderService',issue_ts:issue_ts,issue_sr:issue_sr,terminal_id:terminal_id,walk_in:walk_in,id:id,cashier_trans:cashier_trans,is_service_item:is_service_item},
					success: function(data){
						if(is_cebuhiq == 1 && (issue_sr == 1 || issue_ts == 1)){

							if(issue_sr == 1){
								printElemSr(data);
							}

							if(issue_ts == 1){
								printElemTs(data);
							}

							location.href = "item-service-log.php";
						} else {
							alertify.alert(data,function(){
								location.href = "item-service-log.php";
							});
						}



					},
					error:function(){

					}
				});
			});

			getLayout();
			getInvoiceDrPr();
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


			function getLayout() {

				$.ajax({
					url: "../ajax/ajax_query2.php",
					type: "POST",
					dataType: 'json',
					data: {functionName: 'getTrucks'},
					success: function(data) {

						localStorage["invoice_format"] = data.invoice;
						localStorage["dr_format"] = data.dr;
						localStorage["ir_format"] = data.ir;
						localStorage["sv_format"] = data.sv;
						localStorage["sr_format"] = data.sr;
						localStorage["ts_format"] = data.ts;
						localStorage["news_format"] = data.extra;

					}
				});
			}
			$('body').on('click','#btnSCS',function(){
				var data = $('#print_data').val();
				try{
					data = JSON.parse(data);
					var form = {
						date:data['main'].date,
						customer_name:data['main'].member_name,
						address:data['main'].member_address,
						tel_number:data['main'].member_contact,
						model:'',
						serial_number:'',
						date_sold:'',
						si_dr:'',
						outlet:'',
						complains:{
							no_cold:false,poor_cooling:false,water: false,no_hot:false,motor:false,water_con:false,no_power:false,
							crack: false, poor_flowing:false, leaking:false,malfunction:false,other:false
						},
						accessories:'',
						dents:'',
						under_warranty: false,
						received_by:'',
						conforme:''
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
					var form = {
						date:data['main'].date,
						customer_name:data['main'].member_name,
						address:data['main'].member_address,
						tel_number:data['main'].member_contact,
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
						conforme:''
					};
					var arr = { service_id:data['main'].service_id,form:form};
					localStorage['sar_form'] = JSON.stringify(arr);
					location.href = 'sar.php';
				} catch(e){
					console.log("Invalid Data");
				}
			});


			function printElemSr(print_data) {

				print_data = JSON.parse(print_data);
				var data = print_data;
				var member_name = data.member_name;
				var cashier_name = data.cashier_name;
				var layout = JSON.parse(localStorage['sr_format']);
				var remarks = data.remarks;
				var station_address = data.station_address;
				var station_id = data.station_id;
				var station_name = data.station_name;
				var member_id_test = data.member_id;
				var output = data.date_sold;
				var printhtml = "";
				if(!member_name) member_name = '';
				if(!station_address) station_address = '';
				if(!station_id) station_id = '';
				if(!station_name) station_name = '';

				var mem_name_split;
				mem_name_split = member_name.split(',');
				member_name = mem_name_split[0];


				var nextsr = parseInt(localStorage['sr']) + 1;
				var control_num = nextsr;
				if(data.sr && data.sr != "" && data.sr != "0" ){
					control_num = data.sr;
				}

				var model_number = "";
				var serial_number = "";
				var scs = "";
				var sar = "";
				var client_name = member_name;
				var dt = output;
				var dr = control_num;


				var itemtablestyle = "style='position:absolute;top:" + layout['itemtable'].top+"px;left:"+layout['itemtable'].left+"px;font-size:"+layout['itemtable'].fontSize+"px;'";
				var model_number_style = "style='position:absolute;top:" + layout['terms'].top+"px;left:"+layout['terms'].left+"px;font-size:"+layout['terms'].fontSize+"px;'";
				var serial_number_style = "style='position:absolute;top:" + layout['ponum'].top+"px;left:"+layout['ponum'].left+"px;font-size:"+layout['ponum'].fontSize+"px;'";
				var scs_number_style = "style='position:absolute;top:" + layout['tin'].top+"px;left:"+layout['tin'].left+"px;font-size:"+layout['tin'].fontSize+"px;'";
				var sar_number_style = "style='position:absolute;top:" + layout['lbl'].top+"px;left:"+layout['lbl'].left+"px;font-size:"+layout['lbl'].fontSize+"px;'";
				var member_number_style = "style='position:absolute;top:" + layout['membername'].top+"px;left:"+layout['membername'].left+"px;font-size:"+layout['membername'].fontSize+"px;'";
				var total_style = "style='position:absolute;top:" + layout['payments'].top+"px;left:"+layout['payments'].left+"px;font-size:"+layout['payments'].fontSize+"px;'";
				var date_style = "style='position:absolute;top:" + layout['date'].top+"px;left:"+layout['date'].left+"px;font-size:"+layout['date'].fontSize+"px;'";
				var num_style = "style='position:absolute;top:" + layout['drnum'].top+"px;left:"+layout['drnum'].left+"px;font-size:"+layout['drnum'].fontSize+"px;'";

				var company_id = localStorage['company_id'];

				var printhtml = "";
				var fontFamily = "font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif;";
				printhtml = printhtml + "<div id='maindivforprinting' style='page-break-before: always;position:relative;"+fontFamily+"'>&nbsp;";
				printhtml= printhtml +  "<div "+model_number_style+">"+  model_number+ " </div><div style='clear:both;'></div>";
				printhtml= printhtml +  "<div "+serial_number_style+">"+  serial_number+ " </div><div style='clear:both;'></div>";
				printhtml= printhtml +  "<div "+scs_number_style+">"+  scs+ " </div><div style='clear:both;'></div>";
				printhtml= printhtml +  "<div "+sar_number_style+">"+  sar+ " </div><div style='clear:both;'></div>";
				printhtml= printhtml +  "<div "+member_number_style+">"+  client_name+ " </div><div style='clear:both;'></div>";
				printhtml= printhtml +  "<div "+date_style+">"+  dt+ " </div><div style='clear:both;'></div>";
				printhtml= printhtml +  "<div "+num_style+">"+  dr+ " </div><div style='clear:both;'></div>";
				printhtml += "<table "+itemtablestyle+">";



				var grand_total = 0;
				var testdata = data.item_list;
				for(var i in testdata) {
					var itemcode = testdata[i].item_code;
					var description = testdata[i].description;
					var b = testdata[i].barcode;
					var unit_name = testdata[i].unit_name;
					unit_name = (unit_name) ? unit_name : '';
					var qty = testdata[i].qty + "<td style='width:60px;'>" + unit_name + "</td>";
					var price = testdata[i].price;
					var discount = testdata[i].discount;
					var total = testdata[i].total;
					var origtotal = total;


					grand_total = parseFloat(total) + parseFloat(grand_total);
					printhtml += "<tr>";
					printhtml += "<td style='width:"+layout['tdqty'].width+"px;padding-left:"+layout['tdqty'].left+"px;'>"+qty+"</td>";
					printhtml += "<td style='width:"+layout['tddescription'].width+"px;padding-left:"+layout['tddescription'].left+"px;'>"+description+"</td>";
					printhtml += "<td style='width:"+layout['tdprice'].width+"px;padding-left:"+layout['tdprice'].left+"px;'>"+number_format(price,2)+"</td>";
					printhtml += "<td style='width:"+layout['tdtotal'].width+"px;padding-left:"+layout['tdtotal'].left+"px;'>"+number_format(total,2)+"</td>";
					printhtml += "</tr>";
				}

				printhtml= printhtml +  "<div "+total_style+">"+  number_format(grand_total,2)+ " </div><div style='clear:both;'></div>";
				printhtml += "</table>";
				popUpPrint(printhtml);

			}
			function printElemTs(print_data) {

				print_data = JSON.parse(print_data);
				var data = print_data;
				var member_name = data.member_name;
				var cashier_name = data.cashier_name;
				var layout = JSON.parse(localStorage['ts_format']);
				var remarks = data.remarks;
				var station_address = data.station_address;
				var station_id = data.station_id;
				var station_name = data.station_name;
				var member_id_test = data.member_id;
				var output = data.date_sold;
				var printhtml = "";
				if(!member_name) member_name = '';
				if(!station_address) station_address = '';
				if(!station_id) station_id = '';
				if(!station_name) station_name = '';

				var mem_name_split;
				mem_name_split = member_name.split(',');
				member_name = mem_name_split[0];


				var nextts = parseInt(localStorage['ts']) + 1;
				var control_num = nextts;
				if(data.ts && data.ts != "" && data.ts != "0" ){
					control_num = data.ts;
				}

				var model_number = "";
				var serial_number = "";
				var scs = "";
				var sar = "";
				var client_name = member_name;
				var dt = output;
				var dr = control_num;


				var itemtablestyle = "style='position:absolute;top:" + layout['itemtable'].top+"px;left:"+layout['itemtable'].left+"px;font-size:"+layout['itemtable'].fontSize+"px;'";
				var model_number_style = "style='position:absolute;top:" + layout['terms'].top+"px;left:"+layout['terms'].left+"px;font-size:"+layout['terms'].fontSize+"px;'";
				var serial_number_style = "style='position:absolute;top:" + layout['ponum'].top+"px;left:"+layout['ponum'].left+"px;font-size:"+layout['ponum'].fontSize+"px;'";
				var scs_number_style = "style='position:absolute;top:" + layout['tin'].top+"px;left:"+layout['tin'].left+"px;font-size:"+layout['tin'].fontSize+"px;'";
				var sar_number_style = "style='position:absolute;top:" + layout['lbl'].top+"px;left:"+layout['lbl'].left+"px;font-size:"+layout['lbl'].fontSize+"px;'";
				var member_number_style = "style='position:absolute;top:" + layout['membername'].top+"px;left:"+layout['membername'].left+"px;font-size:"+layout['membername'].fontSize+"px;'";
				var total_style = "style='position:absolute;top:" + layout['payments'].top+"px;left:"+layout['payments'].left+"px;font-size:"+layout['payments'].fontSize+"px;'";
				var date_style = "style='position:absolute;top:" + layout['date'].top+"px;left:"+layout['date'].left+"px;font-size:"+layout['date'].fontSize+"px;'";
				var num_style = "style='position:absolute;top:" + layout['drnum'].top+"px;left:"+layout['drnum'].left+"px;font-size:"+layout['drnum'].fontSize+"px;'";

				var company_id = localStorage['company_id'];

				var printhtml = "";
				var fontFamily = "font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif;";
				printhtml = printhtml + "<div id='maindivforprinting' style='page-break-before: always;position:relative;"+fontFamily+"'>&nbsp;";
				printhtml= printhtml +  "<div "+model_number_style+">"+  model_number+ " </div><div style='clear:both;'></div>";
				printhtml= printhtml +  "<div "+serial_number_style+">"+  serial_number+ " </div><div style='clear:both;'></div>";
				printhtml= printhtml +  "<div "+scs_number_style+">"+  scs+ " </div><div style='clear:both;'></div>";
				printhtml= printhtml +  "<div "+sar_number_style+">"+  sar+ " </div><div style='clear:both;'></div>";
				printhtml= printhtml +  "<div "+member_number_style+">"+  client_name+ " </div><div style='clear:both;'></div>";
				printhtml= printhtml +  "<div "+date_style+">"+  dt+ " </div><div style='clear:both;'></div>";
				printhtml= printhtml +  "<div "+num_style+">"+  dr+ " </div><div style='clear:both;'></div>";
				printhtml += "<table "+itemtablestyle+">";



				var grand_total = 0;
				var testdata = data.item_list;
				for(var i in testdata) {
					var itemcode = testdata[i].item_code;
					var description = testdata[i].description;
					var b = testdata[i].barcode;
					var unit_name = testdata[i].unit_name;
					unit_name = (unit_name) ? unit_name : '';
					var qty = testdata[i].qty + "<td style='width:60px;'>" + unit_name + "</td>";
					var price = testdata[i].price;
					var discount = testdata[i].discount;
					var total = testdata[i].total;
					var origtotal = total;


					grand_total = parseFloat(total) + parseFloat(grand_total);
					printhtml += "<tr>";
					printhtml += "<td style='width:"+layout['tdqty'].width+"px;padding-left:"+layout['tdqty'].left+"px;'>"+qty+"</td>";
					printhtml += "<td style='width:"+layout['tddescription'].width+"px;padding-left:"+layout['tddescription'].left+"px;'>"+description+"</td>";
					printhtml += "<td style='width:"+layout['tdprice'].width+"px;padding-left:"+layout['tdprice'].left+"px;'>"+number_format(price,2)+"</td>";
					printhtml += "<td style='width:"+layout['tdtotal'].width+"px;padding-left:"+layout['tdtotal'].left+"px;'>"+number_format(total,2)+"</td>";
					printhtml += "</tr>";
				}

				printhtml= printhtml +  "<div "+total_style+">"+  number_format(grand_total,2)+ " </div><div style='clear:both;'></div>";
				printhtml += "</table>";
				popUpPrint(printhtml);

			}


		});

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>