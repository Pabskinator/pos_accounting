<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';

	if(!$user->hasPermission('sales')) {
		// redirect to denied page
			Redirect::to(1);
	}

	$billing_remarks = (Configuration::getValue('billing_remarks')) ? Configuration::getValue('billing_remarks') : '';

	$dt1 = date('m/01/Y');
	$dt2 = date('m/d/Y',strtotime($dt1 . "1 month -1 day"));


?>

	<style>
		#freight-payment-container{
			position: fixed;
			width: 500px;
			height: 300px;
			top: 50%;
			left: 50%;
			background: #eee;
			margin-top: -180px; /* Negative half of height. */
			margin-left: -250px; /* Negative half of width. */
			padding-right: 30px;
			padding-left: 30px;
			padding-top: 70px;
			z-index:9999999;
			display:none;
		}
	</style>
	<!-- Page content -->
	<div id="page-content-wrapper">
		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
	<div class="row">
		<div class="col-md-12">
			<?php include 'includes/sales_nav.php'; ?>
		</div>

	</div>
	<div class="row">
		<div class="col-md-12 text-right">
			<?php if($user->hasPermission('freight')){

				?>
				<a class='btn btn-default' href="freight_charges.php"><span class='fa fa-truck'></span> Freights</a>
				<?php
			}?>

			<?php if($user->hasPermission('dl_sales')){ ?>
			<button class='btn btn-default' id='btnDownload'><span class='glyphicon glyphicon-download'></span> Download</button>
			<?php } ?>
		</div>
	</div>

			<div class="content-header">

					<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Manage Sales </h1>


			</div>
			<?php
				// get flash message if add or edited successfully
				if(Session::exists('salesflash')) {
					echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('salesflash') . "</div>";
				}


			?>

			<div id="test"></div>
			<div class="row">
				<div class="col-md-12">
					<input type="hidden" id='sort_by' />
					<input type="hidden" id='ascdesc' value='1' />

					<div class="panel panel-primary">
						<!-- Default panel contents -->
						<div class="panel-heading">Sales</div>
						<div class="panel-body">
							<div class="row">

							<div class="col-md-3">
								<div class="form-group">
								<div class="input-group">
									<span class="input-group-addon"><span class='glyphicon glyphicon-search'></span></span>
									<input type="text" id="searchSales"  class='form-control' placeholder='Search Ctrl Number..'/>
								</div>
								</div>
							</div>

								<div class="col-md-3">
									<div class="form-group">
										<select class='form-control' id='sales_type'>
											<option value='0'>Sold</option>
											<option value='1'>Cancelled</option>
										</select>
									</div>
								</div>
							<div class="col-md-3">
									<div class="form-group">
										<select name="tran_type" id="tran_type" class='form-control'>
											<option value="">Select sales type</option>
											<?php
												$sales_type = new Sales_type();
												$sales_types = $sales_type->get_active('salestypes',array('company_id','=',$user->data()->company_id));
												$sales_type_ar_cls = new Sales_type();
												$types_ar = $sales_type_ar_cls->getMySalesType($user->data()->id);
												if($types_ar){
													foreach($types_ar as $st){
														echo  "<option value='$st->id'>$st->name</option>";
													}
												} else {
													foreach($sales_types as $st){
														echo  "<option value='$st->id'>$st->name</option>";
													}
												}

											?>
										</select>
								</div>
							</div>
							<div class="col-md-3">
								<?php if(!$user->hasPermission('is_franchisee')){
									?>
									<div class="form-group">
										<select id="branch_id" name="branch_id" class="form-control" multiple>
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
											<option value='-1'>Caravan</option>
										</select>
									</div>
									<?php
								}?>

							</div>
								<div class="col-md-3"><div id='terminalitemholder'></div></div>


							</div>
							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" class='form-control' value='<?php echo $dt1; ?>' placeholder='Date From'  id='date_from'>
										<div class="row">
											<div class="col-md-6"><span class='help'>Date From</span> </div>
											<div class="col-md-6 text-right"><a href="#" id='btnClear'>Clear Date</a></div>
										</div>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" class='form-control' value='<?php echo $dt2; ?>' placeholder='Date From' id='date_to'>
										<span class='help'>Date To</span>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" class='form-control' id='member_id'>
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group">
										<input type="text" class='form-control' placeholder='Search Item'  autocomplete="off" id='item_id'>
									</div>
								</div>

							</div>

							<div id="testtest"></div>
							<input type="hidden" id="hiddenpage" />
							<div id="holder"></div>

						</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<div id='freight-payment-container'>

		<div class='text-center'>
			<h5>Freight Payment</h5>
			<input type="hidden" id='freight_id'>
		<div class="form-group">
			<input type="text" class='form-control' id='freight_paid_amount' placeholder='Amount'>
		</div>
		<div class="form-group">
			<button class='btn btn-primary' id='btnPayFreight'>Submit</button>
		</div>
		</div>
	</div>
	<div class="modal" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content" style='width:650px;' >
				<div class="modal-header">
					<h4 class="modal-title">Details</h4>
				</div>
				<div class="modal-body" id='ebody'>

				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style='width:95%;'>
			<div class="modal-content" >
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Payment Details</h4>
				</div>
				<div class="modal-body" id='mbody'>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<script>
	//$('#tblSales').dataTable({
	//	iDisplayLength: 50
	//});
	$(function(){
		$('#branch_id').select2({allowClear:true, placeholder:'Select branch'});

		getPage(0);
		getLayout();
		$('body').on('click','#btnClear',function(e){
			e.preventDefault();
			$('#date_from').val('');
			$('#date_to').val('');
			getPage(0);
		});

		$('body').on('click','#btnPayFreight',function(){
			var con = $(this);
			button_action.start_loading(con);
			var id = $('#freight_id').val();
			var amount = $('#freight_paid_amount').val();
			$.ajax({
			    url:'../ajax/ajax_sales_query.php',
			    type:'POST',
			    data: {functionName:'payFreight',id:id,amount:amount},
			    success: function(data){
					if(data == 'Invalid request' || data == 'Invalid Amount'){
						alert(data);
					} else {
						$('#freight-payment-container').hide();
						getFreights(data);
						alert("Payment added successfully.");
					}
				    button_action.end_loading(con);
			    },
			    error:function(){
				    button_action.end_loading(con);
			    }
			});


		});
		$('body').on('click','.btn-freight-payment',function(){
			var con = $(this);
			var id = con.attr('data-id');
			$('#freight_id').val(id);
			$('#freight_paid_amount').val('');
			$('#freight-payment-container').show();
		});

		$('#member_id').select2({
			placeholder: 'Search Member' ,
			allowClear: true,
			minimumInputLength: 2,
			ajax: {
				url: '../ajax/ajax_json.php', dataType: 'json', type: "POST", quietMillis: 50, data: function(term) {
					return {
						q: term, functionName: 'members'
					};
				}, results: function(data) {
					return {
						results: $.map(data, function(item) {

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
		$('#date_from').datepicker({
			autoclose:true
		}).on('changeDate', function(ev){
			$('#date_from').datepicker('hide');
			dateFilter();
		});
		$('#date_to').datepicker({
			autoclose:true
		}).on('changeDate', function(ev){
			$('#date_to').datepicker('hide');
			dateFilter();
		});

		function dateFilter(){

			var date_from = $('#date_from').val();
			var date_to = $('#date_to').val();

			if(date_to && date_from){
				var dt1 =  new Date(date_from).getTime() / 1000;
				var dt2 =  new Date(date_to).getTime() / 1000;
				if(dt1 > dt2){
					tempToast('error','Date From should be earlier than Date To','Error');
				} else {
					getPage(0);
				}


			}

		}
		function getLayout(){

			$.ajax({
				url: "../ajax/ajax_query.php",
				type:"POST",
				data:{cid:'<?php echo $user->data()->company_id?>',functionName:'getInvoiceFormat'},
				success: function(data){
					localStorage["invoice_format"] = data;
				}
			});

			$.ajax({
				url: "../ajax/ajax_query.php",
				type:"POST",
				data:{cid:'<?php echo $user->data()->company_id?>',functionName:'getDrFormat'},
				success: function(data){
					localStorage["dr_format"] = data;
				}
			});

		}

		$('body').on('click','.paging',function(e){
			e.preventDefault();
			var page = $(this).attr('page');
			$('#hiddenpage').val(page);
			getPage(page);
		});

		var timer;

		$("#searchSales").keyup(function(){
			var searchtxt = $("#searchSales");
			var search = searchtxt.val();
			clearTimeout(timer);
			timer = setTimeout(function() {
				if(searchtxt.val()){
					searchtxt.val(searchtxt.val().trim());
				}
				getPage(0);
			}, 1000);
		});


		var timer2;

		$("#item_id").keyup(function(){
			var searchtxt = $("#item_id");

			clearTimeout(timer2);
			timer2 = setTimeout(function() {
				if(searchtxt.val()){
					searchtxt.val(searchtxt.val().trim());
				}
				getPage(0);
			}, 1000);
		});


		$("body").on('click','.page_sortby',function(){
			var sortlabel = $(this).attr('data-sort');
			$('#sort_by').val(sortlabel);
			getPage(0);
		});

		$('body').on('click','#btnSubmitEmail',function(){
			var member_id = $('#billing_member_id').val();
			var payment_id = $('#billing_payment_id').val();
			var email = $('#txtBillingEmail').val();
			var subject = $('#txtBillingSubject').val();
			var remarks = $('#txtBillingRemarks').val();
			var content = $('#bill_to_email').html();
			$.ajax({
				url: '../ajax/ajax_sales_query.php',
				type: 'POST',
				data: {functionName: 'sendBillingEmail', member_id:member_id,payment_id:payment_id,email: email, subject: subject,content:content,remarks:remarks},
				success: function(data) {
					$('#emailModal').modal('hide');
					alertify.alert(data);
				}
			});
		});
		$('body').on('click','.btnShowEmailModal',function(){
			var payment_id = $(this).attr('data-payment_id');
			var member_id = $(this).attr('data-member_id');
			$('#emailModal').modal('show');
			$('#ebody').html('Loading...');

			printBillingStatement(payment_id,member_id,1);

		});

		$('body').on('click','.btnPrintBillingStatement',function(){
			var payment_id = $(this).attr('data-payment_id');
			var member_id = $(this).attr('data-member_id');
			printBillingStatement(payment_id,member_id);
		});

		$('body').on('click','#main-img-email',function(){

			var con = $(this);
			var host_name = '<?php echo $_SERVER['HTTP_HOST']; ?>';
			var src='http://'+host_name+'/css/img/';
			if(con.attr('data-img') == 'logo.jpg'){
				src='http://'+host_name+'/css/img/logo-2.jpg';
				con.attr('src',src);
				con.attr('data-img','logo-2.jpg');
			} else if(con.attr('data-img') == 'logo-2.jpg'){
				src='http://'+host_name+'/css/img/logo-3.jpg';
				con.attr('src',src);
				con.attr('data-img','logo-3.jpg');
			} else {
				src='http://'+host_name+'/css/img/logo.jpg';
				con.attr('src',src);
				con.attr('data-img','logo.jpg');
			}

		});

		function printBillingStatement(payment_id,member_id,submit_email){
			var host_name = '<?php echo $_SERVER['HTTP_HOST']; ?>';
			var extra_remarks = '<?php echo $billing_remarks; ?>';


			$.ajax({
			    url:'../ajax/ajax_sales_query.php',
			    type:'POST',
			    dataType:'json',
			    data: {functionName:'billingStatementData',payment_id:payment_id,member_id:member_id},
			    success: function(data){
				    var ret_html ="";
				    var tbl_style="width:100%;";
				    var text_align = "text-align:right;";
				    var logo_src = "http://"+host_name+"/css/img/logo.jpg";
					if(data.logo2 == '2'){
						logo_src = "http://"+host_name+"/css/img/logo-2.jpg";
					} else if(data.logo2 == '3'){
						logo_src = "http://"+host_name+"/css/img/logo-3.jpg";
					}
				 ret_html += "<table  style='"+tbl_style+"clear:both;' class='table table-condensed-2'>";
				 ret_html += "<tr><td style='width:250px;' class='text-center'><br><img id='main-img-email' data-img='logo.jpg' src='"+logo_src+"' alt='Logo'></td>";
				 ret_html += "<td style='font-size:12px;'><br>";
				 ret_html += "Address "+ data.company_address+"<br>";
				 ret_html += "Contact "+data.company_contact+"<br>";
				 ret_html += "Email "+data.company_email+"<br>";
				 ret_html += "Website "+data.company_website+"<br>";
			     ret_html += "</td>";
			     ret_html += "</tr>";
				 ret_html += "</table>";
			     ret_html += "<p class='text-center' style='font-size:12px;'>Billing Statement</p>";
			      ret_html += "<table style='font-size:10px;"+tbl_style+"clear:both;' class='table  table-condensed-2'>";
			       ret_html += "<tr  style='height:12px;'>";
				    ret_html += "<td style='border-top:none;width:60%;'>Bill To: "+data.member_name+" </td>";
				    ret_html += "<td style='border-top:none;text-align: right;'>Statement Date:</td><td style='border-top:none;'>"+data.statement_date+"</td>";
				    ret_html += "</tr>";
				    ret_html += "<tr  style='height:12px;'>";
				    ret_html += "<td style='border-top:none;width:60%;'>Address: "+data.station_name+"</td>";
				    ret_html += "<td style='border-top:none;text-align: right;'>Order Date: </td><td style='border-top:none;'>"+data.order_date+"</td>";
				    ret_html += "</tr>";
				    ret_html += "<tr  style='height:12px;'>";
				    ret_html += "<td style='border-top:none;width:60%;'>P.O #: "+data.order_id+"</td>";
				    ret_html += "<td style='border-top:none;text-align: right;'>Delivery Date: </td><td style='border-top:none;'>"+data.delivery_date+"</td>";
				    ret_html += "</tr>";
				     ret_html += "</table>";
				    var data_m = data.item_list_m;
				    var data_nm = data.item_list_nm;
					var grand_total = 0;
				    if(data_m.length){
					    var total_merc = 0;
					    ret_html += "<table class='table table-bordered table-condensed-2' style='font-size:10px;"+tbl_style+"clear:both;'>";
					    ret_html += "<thead><tr ><th colspan='5' align='left'>Merchandised Item</th></tr></thead>";
					    ret_html += "<tbody>";
					    var prev_categ = "";
					    var cur_total = 0;
					    var first = true;
					    for(var i in data_m){
						    if(prev_categ != data_m[i].category_name){
							    if(first){
								    first = false;
							    } else {
								    ret_html += "<tr><td colspan='4'>Sub Total:</td><td style='text-align:right;'><strong>"+number_format(cur_total,2)+"</strong></td></tr>";
								    cur_total = 0;
							    }
							    ret_html += "<tr><td colspan='5' align='left'><strong>"+data_m[i].category_name+"</strong></td></tr>";
							    ret_html += "<tr><th style='text-align:left;'>Qty</th><th style='text-align:left;'>Unit</th><th style='text-align:left;'>Product Name</th><th style='text-align:right;'>Unit Price</th><th style='text-align:right;'>Amount</th></tr>";

						    }
						    cur_total = parseFloat(cur_total) + parseFloat(data_m[i].total);
						    prev_categ = data_m[i].category_name;
						    ret_html += "<tr><td style='text-align:left;'>"+data_m[i].qty+"</td><td style='text-align:left;'>"+data_m[i].unit_name+"</td><td style='text-align:left;'>"+data_m[i].item_code+"</td><td style='text-align:right;'>"+number_format(data_m[i].price,2)+"</td><td style='text-align:right;'>"+number_format(data_m[i].total,2)+"</td></tr>";
						    total_merc = parseFloat(total_merc) + parseFloat(data_m[i].total);

					     }

					    if(cur_total > 0){
						    ret_html += "<tr><td colspan='4' align='left'>Sub Total:</td><td style='text-align:right;'><strong> "+number_format(cur_total,2)+"</strong></td></tr>";
					    }

					    ret_html += "</tbody>";
					    ret_html += "<tfoot><tr><th colspan='4' align='left'>Sub MDSE:</th><th style='text-align:right;'>"+number_format(total_merc,2)+"</th></tr></tfoot>";
					    ret_html += "</table>";

					    grand_total = parseFloat(total_merc) + parseFloat(grand_total);

				    }

				    if(data_nm.length){
					    var total_non_merc = 0;
					    ret_html += "<table class='table table-bordered table-condensed-2' style='font-size:10px;"+tbl_style+"clear:both;'>";
					    ret_html += "<thead><tr ><th colspan='5' align='left'>Non-Merchandised Item</th></tr></thead>";
					    ret_html += "<tbody>";
					    for(var i in data_nm){
						    ret_html += "<tr><td style='text-align:right;'>"+data_nm[i].qty+"</td><td style='text-align:right;'>"+data_nm[i].unit_name+"</td><td>"+data_nm[i].item_code+"</td><td style='text-align:right;'>"+number_format(data_nm[i].price,2)+"</td><td style='text-align:right;'>"+number_format(data_nm[i].total,2)+"</td></tr>";
						    total_non_merc = parseFloat(total_non_merc) + parseFloat(data_nm[i].total);
					    }
					    ret_html += "</tbody>";
					    ret_html += "<tfoot><tr><th colspan='4' align='left'>Sub NON-MDSE:</th><th style='text-align:right;'>"+number_format(total_non_merc,2)+"</th></tr></tfoot>";
					    ret_html += "</table>";

					    grand_total = parseFloat(total_non_merc) + parseFloat(grand_total);
				    }

				    ret_html += data.freight_tbl;

				    ret_html += data.bad_order_tbl;

				   // grand_total = parseFloat(data.total_freight)+ parseFloat(grand_total) - parseFloat(data.total_bad_order);
				    grand_total = parseFloat(data.total_freight)+ parseFloat(grand_total);
				    var beginning = parseFloat(data.member_credit.credit_amount) - parseFloat(grand_total) +  parseFloat(data.total_freight);
				    var amount_paid = data.member_credit.credit_amount_paid;
				    var amount_due = parseFloat(beginning) + parseFloat(grand_total) - parseFloat(amount_paid);
				    ret_html += "<p  style='text-align:right;clear:both;'><strong>Grand Total: "+number_format(grand_total,2)+"</strong></p>";
				    ret_html += "<table  style='"+tbl_style+"font-size:12px;clear:both;' class='table table-condensed-2'>";
				    ret_html += "<tr><th style='text-align:right;'>Beginning Balance</th><th style='text-align:right;'>Payment</th><th style='text-align:right;'>Current Charges</th><th style='text-align:right;' >Amount Due</th></tr>";
				    ret_html += "<tr><td style='text-align:right;'>"+number_format(beginning,2)+"</td><td style='text-align:right;'>"+number_format(amount_paid,2)+"</td><td style='text-align:right;'>"+number_format(grand_total,2)+"</td><td style='text-align:right;'>"+number_format(amount_due,2)+"</td></tr>";
				    ret_html += "</table>";
				    ret_html += "<p  style='font-size:12px;'><span class='text-muted'>"+extra_remarks+"</strong></p>";

					if(submit_email == 1){
						ret_html = "<div style='width:600px;margin:0 auto; background: #fff;'>" + ret_html + "</div>";
						$.ajax({
							url:'../ajax/ajax_sales_query.php',
							type:'POST',
							data: {functionName:'getBillingData',payment_id: payment_id,member_id:member_id, ret_html:ret_html},
							success: function(data){
								$('#ebody').html(data);
								$('#txtBillingRemarks').html('').tinymce({
									height: 150
								});
							},
							error:function(){

							}
						});
					} else {
						PopupWithStyle(ret_html);
					}
			    },
			    error:function(){

			    }
			});
		}
		function getPage(p){

			var tran_type = $('#tran_type').val();
			var search = $('#searchSales').val();
			var b = $('#branch_id').val();
			var t = $('#terminals').val();
			var type = $('#sales_type').val();
			var sortby = $('#sort_by').val();
			var ascdesc = $('#ascdesc').val();
			var member_id = $('#member_id').val();
			var item_id = $('#item_id').val();
			var date_from = $('#date_from').val();
			var date_to = $('#date_to').val();

			if(!ascdesc) {
				if(sortby) 	sortby = sortby + 'asc';
			} else {
				if(sortby) sortby = sortby + 'desc';
			}

			$.ajax({
				url: '../ajax/ajax_paging.php',
				type:'post',
				beforeSend: function(){
					$('#holder').html("Fetching records...");
				},
				data:{page:p,sortby:sortby,tran_type:tran_type,date_from:date_from,date_to:date_to,functionName:'salesPaginate',cid: <?php echo $user->data()->company_id; ?>,search:search,b:b,t:t,type:type,mem_id:member_id,item_id:item_id},
				success: function(data){
					$('#holder').html(data);
				},
				error:function(){
					alertify.alert('You have poor connection. Please try again.',function(){
						location.href='sales.php';
					});
				}
			});
		}
		function branchTerminal(cid,type){
			$.ajax({
				url: "../ajax/ajax_get_branchAndTerminal.php",
				type:"POST",
				data:{cid:cid,type:type},
				success: function(data){
					$("#terminalitemholder").empty();
					$("#terminalitemholder").append(data);
				},
				error: function(){
					alert('Problem Occurs');
				}
			});
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

		$("body").on('click','.cancelPayment',function(){
			if(confirm("Are you sure you want to cancel this transaction?")){
				var payment_id = $(this).attr('data-payment_id');
				$.ajax({
					url: '../ajax/ajax_cancelpayment.php',
					type: 'POST',
					data: {id:payment_id},
					success: function(data){
						//$('#test').html(data);
						location.href = 'sales.php';
					}
				});
			}
		});

		$("body").on('click','.editTransaction',function(){
				var payment_id = $(this).attr('data-payment_id');
				location.href='sales_crud.php?id='+payment_id;
			});

			$('body').on('change','#branch_id,#member_id',function(){
				if($('#branch_id').val() != '-1'){
			//		branchTerminal($('#branch_id').val(),6);
				}
				getPage(0);
			});

		$('body').on('change','#terminals',function(){
			getPage(0);
		});
		$('body').on('change','#sales_type,#tran_type',function(){
			getPage(0);
		});

		var invoicelimit = localStorage['invoice_limit'];
		var drlimit = localStorage['dr_limit'];
		var irlimit = localStorage['ir_limit'];

		$('body').on('click','.reprintVoucher',function(){
			var payment_id = $(this).attr('data-payment_id');
			$.ajax({
			    url:'../ajax/ajax_query2.php',
			    type:'POST',
				dataType:'json',
			    data: {payment_id:payment_id,functionName:'reprintItem'},
			    success: function(data){
				   // $('#test').html(data);
				    PrintElem(data,invoicelimit);
			    },
			    error:function(){

			    }
			})
		});
		$('body').on('click','.reprintDr',function(){
			var payment_id = $(this).attr('data-payment_id');
			$.ajax({
				url:'../ajax/ajax_query2.php',
				type:'POST',
				dataType:'json',
				data: {payment_id:payment_id,functionName:'reprintItem',type:1},
				success: function(data){
					// $('#test').html(data);
					PrintElemDr(data,drlimit);
					newsPrint(data,1);
				},
				error:function(){

				}
			})
		});

		$('body').on('click','.reprintIr',function(){

			var payment_id = $(this).attr('data-payment_id');
			$.ajax({
				url:'../ajax/ajax_query2.php',
				type:'POST',
				dataType:'json',
				data: {payment_id:payment_id,functionName:'reprintItem',type:2},
				success: function(data){
					// $('#test').html(data);
					PrintElemIr(data,irlimit);
					newsPrint(data,2);

				},
				error:function(){

				}
			});


		});

		function PrintElem(data,invoice_limit)
		{

			var member_name = data.member_name;
			var cashier_name = data.cashier_name;
			var styling = JSON.parse(localStorage['invoice_format']);
			var remarks = data.remarks;
			var station_address=data.station_address;
			var station_id = data.station_id;
			var station_name =data.station_name;
			var output = data.date_sold;
			var printhtml="";
			if(!member_name) member_name = '';
			if(!station_address) station_address = '';
			if(!station_id) station_id = '';
			if(!station_name) station_name = '';

			var datevisible = (styling['date']['visible']) ? 'display:block;' : 'display:none;';
			var membernamevisible = (styling['membername']['visible']) ? 'display:block;' : 'display:none;';
			var memberaddressvisible = (styling['memberaddress']['visible']) ? 'display:block;' : 'display:none;';
			var stationnamevisible = (styling['stationname']['visible']) ? 'display:block;' : 'display:none;';
			var stationaddressvisible = (styling['stationaddress']['visible']) ? 'display:block;' : 'display:none;';
			var itemtablevisible = (styling['itemtable']['visible']) ? 'display:block;' : 'display:none;';
			var paymentsvisible = (styling['payments']['visible']) ? 'display:block;' : 'display:none;';
			var payments2visible = (styling['payments2']['visible']) ? 'display:block;' : 'display:none;';
			var payments3visible = (styling['payments3']['visible']) ? 'display:block;' : 'display:none;';
			var cashiervisible = (styling['cashier']['visible']) ? 'display:block;' : 'display:none;';
			var remarksvisible = (styling['remarks']['visible']) ? 'display:block;' : 'display:none;';
			var reservedvisible = (styling['reserved']['visible']) ? 'display:block;' : 'display:none;';
			var tdbarcodevisible = (styling['tdbarcode']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tdqtyvisible = (styling['tdqty']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tddescriptionvisible = (styling['tddescription']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tdpricevisible = (styling['tdprice']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tdtotalvisible = (styling['tdtotal']['visible']) ? 'display:inline-block;' : 'display:none;';

			var dateBold = (styling['date']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var membernameBold = (styling['membername']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var memberaddressBold = (styling['memberaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var stationnameBold = (styling['stationname']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var stationaddressBold = (styling['stationaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var itemtableBold = (styling['itemtable']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var paymentsBold = (styling['payments']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var payments2Bold = (styling['payments2']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var payments3Bold = (styling['payments3']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var cashierBold = (styling['cashier']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var remarksBold = (styling['remarks']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var reservedBold = (styling['reserved']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdbarcodeBold = (styling['tdbarcode']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdqtyBold = (styling['tdqty']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tddescriptionBold = (styling['tddescription']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdpriceBold = (styling['tdprice']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdtotalBold = (styling['tdtotal']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';

			printhtml = printhtml + "<div id='maindivforprinting' style='page-break-before: always;position:relative;'>";
			printhtml= printhtml +  "<div style='"+datevisible+dateBold+"position:absolute;top:"+styling['date']['top']+"px; left:"+styling['date']['left']+"px;font-size:"+styling['date']['fontSize']+"px;'> <br/><br/>"+  output+ " </div><div style='clear:both;'></div>";
			printhtml= printhtml +  "<div style='"+membernamevisible+membernameBold+"position:absolute;top:"+styling['membername']['top']+"px; left:"+styling['membername']['left']+"px;font-size:"+styling['membername']['fontSize']+"px;'>"+member_name+"</div>";
			printhtml= printhtml +  "<div style='"+memberaddressvisible+memberaddressBold+"position:absolute;top:"+styling['memberaddress']['top']+"px; left:"+styling['memberaddress']['left']+"px;width:"+styling['memberaddress']['width']+"px;font-size:"+styling['memberaddress']['fontSize']+"px;'>"+station_name+"</div>";
			printhtml= printhtml +  "<div style='"+stationnamevisible+stationnameBold+"position:absolute;top:"+styling['stationname']['top']+"px; left:"+styling['stationname']['left']+"px;font-size:"+styling['stationname']['fontSize']+"px;'>"+station_id+"</div>";
			printhtml= printhtml +  "<div style='"+stationaddressvisible+stationaddressBold+"position:absolute;top:"+styling['stationaddress']['top']+"px; left:"+styling['stationaddress']['left']+"px;width:"+styling['stationaddress']['width']+"px;font-size:"+styling['stationaddress']['fontSize']+"px;'>"+station_address+"</div>";
			printhtml= printhtml + "<table id='itemscon' style='"+itemtablevisible+itemtableBold+"position:absolute;top:"+styling['itemtable']['top']+"px;left:"+styling['itemtable']['left']+"px;font-size:"+styling['itemtable']['fontSize']+"px;'> ";

			var countallitem = 	$('#cart > tbody > tr').length;
			var invoicelimit = parseFloat(localStorage['invoice_limit']);
			var drlimit = localStorage['dr_limit'];
			var lamankadainvoice =[];
			var pagectr = 1;
			var rowctr = 1;
			var pagesubtotal = 0;
			var pagetax=0;
			var pagegrandtotal = 0;
			var vat = 1.12;
			invoicelimit = parseInt(invoicelimit) + 1;
			var testdata =data.item_list;
			for(var i in testdata){
				var itemcode = testdata[i].item_code;
				var description = testdata[i].description;
				var b = testdata[i].barcode;
				var qty = testdata[i].qty;
				var price =testdata[i].price;
				var discount = testdata[i].discount;
				var total =testdata[i].total;

				var origtotal = parseFloat(qty) * parseFloat(price);
				if(parseFloat(discount) > 0){
					var perunitdisc = parseFloat(discount) / parseFloat(qty);
					var labeldisc = "<br/>(Disc. " + number_format(perunitdisc,2) + ")";
					var labeldisc2 = "<br/>("+number_format(discount,2)+")";
				} else {
					var labeldisc ='';
					var labeldisc2 ='';
				}
				if(rowctr % invoicelimit == 0){
					var subtotal = (pagesubtotal / vat);
					var vatable = parseFloat(pagesubtotal) - parseFloat(subtotal);
					subtotal = subtotal.toFixed(2);
					vatable = vatable.toFixed(2);
					pagesubtotal = pagesubtotal.toFixed(2);
					lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "</table>";
					lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "<div style='"+paymentsvisible+paymentsBold+"position:absolute; list-style-type: none; left:"+styling['payments']['left']+"px;top:"+styling['payments']['top']+"px;font-size:"+styling['payments']['fontSize']+"px;'>"+subtotal+"</div>";
					lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "<div style='"+payments2visible+payments2Bold+"position:absolute; list-style-type: none; left:"+styling['payments2']['left']+"px;top:"+styling['payments2']['top']+"px;font-size:"+styling['payments2']['fontSize']+"px;'>"+vatable+"</div>";
					lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "<div style='"+payments3visible+payments3Bold+"position:absolute; list-style-type: none; left:"+styling['payments3']['left']+"px;top:"+styling['payments3']['top']+"px;font-size:"+styling['payments3']['fontSize']+"px;'>"+pagesubtotal+"</div>";
					pagectr = parseInt(pagectr) + 1;
					pagesubtotal=0;
				}
				pagesubtotal = parseFloat(pagesubtotal) + parseFloat(total);
				lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "<tr ><td style='"+tdbarcodevisible+tdbarcodeBold+"position:relative;width:"+styling['tdbarcode']['width']+"px;padding-left:"+styling['tdbarcode']['left']+"px;'>"+itemcode+"</td><td style='"+tdqtyvisible+tdqtyBold+"position:relative;width:"+styling['tdqty']['width']+"px;padding-left:"+styling['tdqty']['left']+"px;'>"+qty+"</td><td style='"+tddescriptionvisible+tddescriptionBold+"position:relative;width:"+styling['tddescription']['width']+"px;padding-left:"+styling['tddescription']['left']+"px;'> "+ description +" <span style='padding-left:20px;'>"+labeldisc+"</span> </td><td style='"+tdpricevisible+tdpriceBold+"position:relative;width:"+styling['tdprice']['width']+"px;padding-left:"+styling['tdprice']['left']+"px;'>"+number_format(price,2)+"</td><td style='"+tdtotalvisible+tdtotalBold+"position:relative;width:"+styling['tdtotal']['width']+"px;padding-left:"+styling['tdtotal']['left']+"px;'>"+number_format(origtotal,2)+" "+labeldisc2+"</td></tr>";
				rowctr = parseInt(rowctr) +1;
			};
			if(pagesubtotal > 0){
				var subtotal = (pagesubtotal / vat);
				var vatable = parseFloat(pagesubtotal) - parseFloat(subtotal);
				subtotal = subtotal.toFixed(2);
				vatable = vatable.toFixed(2);
				pagesubtotal = pagesubtotal.toFixed(2);
				lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "</table>";
				lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "<div style='"+paymentsvisible+paymentsBold+"position:absolute; list-style-type: none; left:"+styling['payments']['left']+"px;top:"+styling['payments']['top']+"px;font-size:"+styling['payments']['fontSize']+"px;'>"+subtotal+"</div>";
				lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "<div style='"+payments2visible+payments2Bold+"position:absolute; list-style-type: none; left:"+styling['payments2']['left']+"px;top:"+styling['payments2']['top']+"px;font-size:"+styling['payments2']['fontSize']+"px;'>"+vatable+"</div>";
				lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "<div style='"+payments3visible+payments3Bold+"position:absolute; list-style-type: none; left:"+styling['payments3']['left']+"px;top:"+styling['payments3']['top']+"px;font-size:"+styling['payments3']['fontSize']+"px;'>"+pagesubtotal+"</div>";
			}
			var printhtmlend = "";
			printhtmlend = printhtmlend + "<div style='"+cashiervisible+cashierBold+"position:absolute;left:"+styling['cashier']['left']+"px;top:"+styling['cashier']['top']+"px;font-size:"+styling['cashier']['fontSize']+"px;'>"+localStorage['current_lastname'] + ", "  + localStorage['current_firstname'] +"</div>";
			printhtmlend = printhtmlend + "<div style='"+remarksvisible+remarksBold+"position:absolute;left:"+styling['remarks']['left']+"px;top:"+styling['remarks']['top']+"px;font-size:"+styling['remarks']['fontSize']+"px;'>"+remarks+"</div>";
			//printhtmlend = printhtmlend + "<div style='"+reservedvisible+reservedBold+"position:absolute;left:"+styling['reserved']['left']+"px;top:"+styling['reserved']['top']+"px;font-size:"+styling['reserved']['fontSize']+"px;'>"+reservedbyname+"</div>";
			printhtmlend = printhtmlend + "</div>";
			var finalprint = "";
			for(var i in lamankadainvoice ){
				finalprint = finalprint + printhtml + lamankadainvoice[i] + printhtmlend;
			}
			console.log(finalprint);
			Popup(finalprint);
		}
		function PrintElemDr(data,dr_limit)
		{
			var member_name = data.member_name;
			var cashier_name = data.cashier_name;
			var remarks =  data.remarks;
			var station_address=data.station_address;
			var station_id = data.station_id;
			var station_name =data.station_name; // member address to
			var output = data.date_sold;
			var printhtml="";
			if(!member_name) member_name = '';
			if(!station_address) station_address = '';
			if(!station_id) station_id = '';
			if(!station_name) station_name = '';

			var styling = JSON.parse(localStorage['dr_format']);
			var salestype = data.sales_type_name;

			var datevisible = (styling['date']['visible']) ? 'display:block;' : 'display:none;';
			var membernamevisible = (styling['membername']['visible']) ? 'display:block;' : 'display:none;';
			var memberaddressvisible = (styling['memberaddress']['visible']) ? 'display:block;' : 'display:none;';
			var stationnamevisible = (styling['stationname']['visible']) ? 'display:block;' : 'display:none;';
			var stationaddressvisible = (styling['stationaddress']['visible']) ? 'display:block;' : 'display:none;';
			var itemtablevisible = (styling['itemtable']['visible']) ? 'display:block;' : 'display:none;';
			var paymentsvisible = (styling['payments']['visible']) ? 'display:block;' : 'display:none;';
			var payments2visible = (styling['payments2']['visible']) ? 'display:block;' : 'display:none;';
			var payments3visible = (styling['payments3']['visible']) ? 'display:block;' : 'display:none;';
			var cashiervisible = (styling['cashier']['visible']) ? 'display:block;' : 'display:none;';
			var remarksvisible = (styling['remarks']['visible']) ? 'display:block;' : 'display:none;';
			var reservedvisible = (styling['reserved']['visible']) ? 'display:block;' : 'display:none;';
			var tdbarcodevisible = (styling['tdbarcode']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tdqtyvisible = (styling['tdqty']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tddescriptionvisible = (styling['tddescription']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tdpricevisible = (styling['tdprice']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tdtotalvisible = (styling['tdtotal']['visible']) ? 'display:inline-block;' : 'display:none;';

			var dateBold = (styling['date']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var membernameBold = (styling['membername']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var memberaddressBold = (styling['memberaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var stationnameBold = (styling['stationname']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var stationaddressBold = (styling['stationaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var itemtableBold = (styling['itemtable']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var paymentsBold = (styling['payments']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var payments2Bold = (styling['payments2']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var payments3Bold = (styling['payments3']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var cashierBold = (styling['cashier']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var remarksBold = (styling['remarks']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var reservedBold = (styling['reserved']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdbarcodeBold = (styling['tdbarcode']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdqtyBold = (styling['tdqty']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tddescriptionBold = (styling['tddescription']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdpriceBold = (styling['tdprice']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdtotalBold = (styling['tdtotal']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';

			var drnumvisible = (styling['drnum']['visible']) ? 'display:block;' : 'display:none;';
			var drnumBold = (styling['drnum']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';

			var printhtml="";
			printhtml = printhtml + "<div id='maindivforprinting' style='page-break-before: always;position:relative;'>";
			printhtml= printhtml +  "<div style='"+datevisible+dateBold+"position:absolute;top:"+styling['date']['top']+"px; left:"+styling['date']['left']+"px;font-size:"+styling['date']['fontSize']+"px;'><br/><br/>"+  output+ " </div><div style='clear:both;'></div>";
			printhtml= printhtml +  "<div style='"+membernamevisible+membernameBold+"position:absolute;top:"+styling['membername']['top']+"px; left:"+styling['membername']['left']+"px;font-size:"+styling['membername']['fontSize']+"px;'>"+member_name+"</div>";
			printhtml= printhtml +  "<div style='"+memberaddressvisible+memberaddressBold+"position:absolute;top:"+styling['memberaddress']['top']+"px; left:"+styling['memberaddress']['left']+"px;width:"+styling['memberaddress']['width']+"px;font-size:"+styling['memberaddress']['fontSize']+"px;'>"+station_name+"</div>";
			printhtml= printhtml +  "<div style='"+stationnamevisible+stationnameBold+"position:absolute;top:"+styling['stationname']['top']+"px; left:"+styling['stationname']['left']+"px;font-size:"+styling['stationname']['fontSize']+"px;'>"+station_id+"</div>";
			printhtml= printhtml +  "<div style='"+stationaddressvisible+stationaddressBold+"position:absolute;top:"+styling['stationaddress']['top']+"px; left:"+styling['stationaddress']['left']+"px;width:"+styling['stationaddress']['width']+"px;font-size:"+styling['stationaddress']['fontSize']+"px;'>"+station_address+"</div>";
			printhtml= printhtml + "<table id='itemscon' style='position:absolute;top:"+styling['itemtable']['top']+"px;left:"+styling['itemtable']['left']+"px;font-size:"+styling['itemtable']['fontSize']+"px;'> ";
			var countallitem = 	$('#cart > tbody > tr').length;
			var drlimit = localStorage['dr_limit'];
			var lamankadadr =[];
			var pagectr = 1;
			var rowctr = 1;
			var pagesubtotal = 0;
			var pagetax=0;
			var pagegrandtotal = 0;
			var vat = 1.12;
			drlimit = parseInt(drlimit) + 1;
			var testdata =data.item_list;

			tdpricevisible = 'display:none;';
			tdtotalvisible = 'display:none;';

			for(var i in testdata){
				var itemcode = testdata[i].item_code;
				var description = testdata[i].description;
				var b = testdata[i].barcode;
				var qty = testdata[i].qty;
				var price =testdata[i].price;
				var discount = testdata[i].discount;
				var total =testdata[i].total;
				var origtotal = parseFloat(qty) * parseFloat(price);
				if(parseFloat(discount) > 0){
					var perunitdisc = parseFloat(discount) / parseFloat(qty);
					var labeldisc = "<br/>(Disc. " + number_format(perunitdisc,2) + ")";
					var labeldisc2 = "<br/>("+number_format(discount,2)+")";
				} else {
					var labeldisc ='';
					var labeldisc2 ='';
				}
				if(rowctr % drlimit == 0){
					var subtotal = (pagesubtotal / vat);
					var vatable = parseFloat(pagesubtotal) - parseFloat(subtotal);
					subtotal = subtotal.toFixed(2);
					vatable = vatable.toFixed(2);
					pagesubtotal = pagesubtotal.toFixed(2);
					lamankadadr[pagectr] = lamankadadr[pagectr] + "</table>";
					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+paymentsvisible+paymentsBold+"position:absolute; list-style-type: none; left:"+styling['payments']['left']+"px;top:"+styling['payments']['top']+"px;font-size:"+styling['payments']['fontSize']+"px;'>"+subtotal+"</div>";
					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments2visible+payments2Bold+"position:absolute; list-style-type: none; left:"+styling['payments2']['left']+"px;top:"+styling['payments2']['top']+"px;font-size:"+styling['payments2']['fontSize']+"px;'>"+vatable+"</div>";
					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments3visible+payments3Bold+"position:absolute; list-style-type: none; left:"+styling['payments3']['left']+"px;top:"+styling['payments3']['top']+"px;font-size:"+styling['payments3']['fontSize']+"px;'>"+pagesubtotal+"</div>";
					pagectr = parseInt(pagectr) + 1;
					pagesubtotal=0;
				}
				pagesubtotal = parseFloat(pagesubtotal) + parseFloat(total);
				lamankadadr[pagectr] = lamankadadr[pagectr] + "<tr ><td style='"+tdbarcodevisible+tdbarcodeBold+"position:relative;width:"+styling['tdbarcode']['width']+"px;padding-left:"+styling['tdbarcode']['left']+"px;'>"+itemcode+"</td><td style='"+tdqtyvisible+tdqtyBold+"position:relative;width:"+styling['tdqty']['width']+"px;padding-left:"+styling['tdqty']['left']+"px;'>"+qty+"</td><td style='"+tddescriptionvisible+tddescriptionBold+"position:relative;width:"+styling['tddescription']['width']+"px;padding-left:"+styling['tddescription']['left']+"px;'> "+ description +" <span style='padding-left:20px;'>"+labeldisc+"</span> </td><td style='"+tdpricevisible+tdpriceBold+"position:relative;width:"+styling['tdprice']['width']+"px;padding-left:"+styling['tdprice']['left']+"px;'>"+number_format(price,2)+"</td><td style='"+tdtotalvisible+tdtotalBold+"position:relative;width:"+styling['tdtotal']['width']+"px;padding-left:"+styling['tdtotal']['left']+"px;'>"+number_format(origtotal,2)+" "+labeldisc2+"</td></tr>";
				rowctr = parseInt(rowctr) +1;
			}
			if(pagesubtotal > 0){
				var subtotal = (pagesubtotal / vat);
				var vatable = parseFloat(pagesubtotal) - parseFloat(subtotal);
				subtotal = subtotal.toFixed(2);
				vatable = vatable.toFixed(2);
				pagesubtotal = pagesubtotal.toFixed(2);
				lamankadadr[pagectr] = lamankadadr[pagectr] + "</table>";
				lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+paymentsvisible+paymentsBold+"position:absolute; list-style-type: none; left:"+styling['payments']['left']+"px;top:"+styling['payments']['top']+"px;font-size:"+styling['payments']['fontSize']+"px;'>"+subtotal+"</div>";
				lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments2visible+payments2Bold+"position:absolute; list-style-type: none; left:"+styling['payments2']['left']+"px;top:"+styling['payments2']['top']+"px;font-size:"+styling['payments2']['fontSize']+"px;'>"+vatable+"</div>";
				lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments3visible+payments3Bold+"position:absolute; list-style-type: none; left:"+styling['payments3']['left']+"px;top:"+styling['payments3']['top']+"px;font-size:"+styling['payments3']['fontSize']+"px;'>"+pagesubtotal+"</div>";
			}
			var printhtmlend = "";
			var reservedbyname ='';
			reservedbyname = salestype + " " + reservedbyname;
			var drnumctr =data.ctrnum;
			var cashier = data.cashier_name;
			printhtmlend = printhtmlend + "<div style='"+cashiervisible+cashierBold+"position:absolute;left:"+styling['cashier']['left']+"px;top:"+styling['cashier']['top']+"px;font-size:"+styling['cashier']['fontSize']+"px;'>"+cashier+"</div>";
			printhtmlend = printhtmlend + "<div style='"+remarksvisible+remarksBold+"position:absolute;left:"+styling['remarks']['left']+"px;top:"+styling['remarks']['top']+"px;font-size:"+styling['remarks']['fontSize']+"px;'>"+remarks+"</div>";
			printhtmlend = printhtmlend + "<div style='"+reservedvisible+reservedBold+"position:absolute;left:"+styling['reserved']['left']+"px;top:"+styling['reserved']['top']+"px;font-size:"+styling['reserved']['fontSize']+"px;'>"+reservedbyname+"</div>";
			printhtmlend = printhtmlend + "<div style='"+drnumvisible+drnumBold+"position:absolute;left:"+styling['drnum']['left']+"px;top:"+styling['drnum']['top']+"px;font-size:"+styling['drnum']['fontSize']+"px;'>"+drnumctr+"</div>";


			var termstxt =data.terms;
			var ponumtxt ='';
			var tintxt =data.tin_no;



			var termsvisible = (styling['terms']['visible']) ? 'display:inline-block;' : 'display:none;';
			var ponumvisible = (styling['ponum']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tinvisible = (styling['tin']['visible']) ? 'display:inline-block;' : 'display:none;';
			var termsbold = (styling['terms']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var ponumbold = (styling['ponum']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tinbold = (styling['tin']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';

			printhtmlend = printhtmlend + "<div style='"+termsvisible+termsbold+"position:absolute;left:"+styling['terms']['left']+"px;top:"+styling['terms']['top']+"px;font-size:"+styling['terms']['fontSize']+"px;'>"+termstxt+"</div>";
			printhtmlend = printhtmlend + "<div style='"+ponumvisible+ponumbold+"position:absolute;left:"+styling['ponum']['left']+"px;top:"+styling['ponum']['top']+"px;font-size:"+styling['ponum']['fontSize']+"px;'>"+ponumtxt+"</div>";
			printhtmlend = printhtmlend + "<div style='"+tinvisible+tinbold+"position:absolute;left:"+styling['tin']['left']+"px;top:"+styling['tin']['top']+"px;font-size:"+styling['tin']['fontSize']+"px;'>"+tintxt+"</div>";

			printhtmlend = printhtmlend + "</div>";
			var finalprint = "";
			for(var i in lamankadadr ){
				finalprint = finalprint + printhtml + lamankadadr[i] + printhtmlend;
			}
			Popup(finalprint);
		}


		function PrintElemIr(data,dr_limit)
		{
			var member_name = data.member_name;
			var cashier_name = data.cashier_name;
			var remarks =  data.remarks;
			var station_address=data.station_address;
			var station_id = data.station_id;
			var station_name =data.station_name; // member address to
			var output = data.date_sold;
			var printhtml="";
			if(!member_name) member_name = '';
			if(!station_address) station_address = '';
			if(!station_id) station_id = '';
			if(!station_name) station_name = '';

			var styling = JSON.parse(localStorage['ir_format']);
			var salestype = data.sales_type_name;

			var datevisible = (styling['date']['visible']) ? 'display:block;' : 'display:none;';
			var membernamevisible = (styling['membername']['visible']) ? 'display:block;' : 'display:none;';
			var memberaddressvisible = (styling['memberaddress']['visible']) ? 'display:block;' : 'display:none;';
			var stationnamevisible = (styling['stationname']['visible']) ? 'display:block;' : 'display:none;';
			var stationaddressvisible = (styling['stationaddress']['visible']) ? 'display:block;' : 'display:none;';
			var itemtablevisible = (styling['itemtable']['visible']) ? 'display:block;' : 'display:none;';
			var paymentsvisible = (styling['payments']['visible']) ? 'display:block;' : 'display:none;';
			var payments2visible = (styling['payments2']['visible']) ? 'display:block;' : 'display:none;';
			var payments3visible = (styling['payments3']['visible']) ? 'display:block;' : 'display:none;';
			var cashiervisible = (styling['cashier']['visible']) ? 'display:block;' : 'display:none;';
			var remarksvisible = (styling['remarks']['visible']) ? 'display:block;' : 'display:none;';
			var reservedvisible = (styling['reserved']['visible']) ? 'display:block;' : 'display:none;';
			var tdbarcodevisible = (styling['tdbarcode']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tdqtyvisible = (styling['tdqty']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tddescriptionvisible = (styling['tddescription']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tdpricevisible = (styling['tdprice']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tdtotalvisible = (styling['tdtotal']['visible']) ? 'display:inline-block;' : 'display:none;';

			var dateBold = (styling['date']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var membernameBold = (styling['membername']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var memberaddressBold = (styling['memberaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var stationnameBold = (styling['stationname']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var stationaddressBold = (styling['stationaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var itemtableBold = (styling['itemtable']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var paymentsBold = (styling['payments']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var payments2Bold = (styling['payments2']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var payments3Bold = (styling['payments3']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var cashierBold = (styling['cashier']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var remarksBold = (styling['remarks']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var reservedBold = (styling['reserved']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdbarcodeBold = (styling['tdbarcode']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdqtyBold = (styling['tdqty']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tddescriptionBold = (styling['tddescription']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdpriceBold = (styling['tdprice']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdtotalBold = (styling['tdtotal']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';

			var drnumvisible = (styling['drnum']['visible']) ? 'display:block;' : 'display:none;';
			var drnumBold = (styling['drnum']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';




			var printhtml="";
			printhtml = printhtml + "<div id='maindivforprinting' style='page-break-before: always;position:relative;'>";
			printhtml= printhtml +  "<div style='"+datevisible+dateBold+"position:absolute;top:"+styling['date']['top']+"px; left:"+styling['date']['left']+"px;font-size:"+styling['date']['fontSize']+"px;'><br/><br/>"+  output+ " </div><div style='clear:both;'></div>";
			printhtml= printhtml +  "<div style='"+membernamevisible+membernameBold+"position:absolute;top:"+styling['membername']['top']+"px; left:"+styling['membername']['left']+"px;font-size:"+styling['membername']['fontSize']+"px;'>"+member_name+"</div>";
			printhtml= printhtml +  "<div style='"+memberaddressvisible+memberaddressBold+"position:absolute;top:"+styling['memberaddress']['top']+"px; left:"+styling['memberaddress']['left']+"px;width:"+styling['memberaddress']['width']+"px;font-size:"+styling['memberaddress']['fontSize']+"px;'>"+station_name+"</div>";
			printhtml= printhtml +  "<div style='"+stationnamevisible+stationnameBold+"position:absolute;top:"+styling['stationname']['top']+"px; left:"+styling['stationname']['left']+"px;font-size:"+styling['stationname']['fontSize']+"px;'>"+station_id+"</div>";
			printhtml= printhtml +  "<div style='"+stationaddressvisible+stationaddressBold+"position:absolute;top:"+styling['stationaddress']['top']+"px; left:"+styling['stationaddress']['left']+"px;width:"+styling['stationaddress']['width']+"px;font-size:"+styling['stationaddress']['fontSize']+"px;'>"+station_address+"</div>";
			printhtml= printhtml + "<table id='itemscon' style='position:absolute;top:"+styling['itemtable']['top']+"px;left:"+styling['itemtable']['left']+"px;font-size:"+styling['itemtable']['fontSize']+"px;'> ";
			var countallitem = 	$('#cart > tbody > tr').length;
			var drlimit = localStorage['ir_limit'];
			var lamankadadr =[];
			var pagectr = 1;
			var rowctr = 1;
			var pagesubtotal = 0;
			var pagetax=0;
			var pagegrandtotal = 0;
			var vat = 1.12;
			drlimit = parseInt(drlimit) + 1;
			var testdata =data.item_list;

			tdpricevisible = 'display:none;';
			tdtotalvisible = 'display:none;';

			for(var i in testdata){
				var itemcode = testdata[i].item_code;
				var description = testdata[i].description;
				var b = testdata[i].barcode;
				var qty = testdata[i].qty;
				var price =testdata[i].price;
				var discount = testdata[i].discount;
				var total =testdata[i].total;
				var origtotal = parseFloat(qty) * parseFloat(price);
				if(parseFloat(discount) > 0){
					var perunitdisc = parseFloat(discount) / parseFloat(qty);
					var labeldisc = "<br/>(Disc. " + number_format(perunitdisc,2) + ")";
					var labeldisc2 = "<br/>("+number_format(discount,2)+")";
				} else {
					var labeldisc ='';
					var labeldisc2 ='';
				}
				if(rowctr % drlimit == 0){
					var subtotal = (pagesubtotal / vat);
					var vatable = parseFloat(pagesubtotal) - parseFloat(subtotal);
					subtotal = subtotal.toFixed(2);
					vatable = vatable.toFixed(2);
					pagesubtotal = pagesubtotal.toFixed(2);
					lamankadadr[pagectr] = lamankadadr[pagectr] + "</table>";
					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+paymentsvisible+paymentsBold+"position:absolute; list-style-type: none; left:"+styling['payments']['left']+"px;top:"+styling['payments']['top']+"px;font-size:"+styling['payments']['fontSize']+"px;'>"+subtotal+"</div>";
					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments2visible+payments2Bold+"position:absolute; list-style-type: none; left:"+styling['payments2']['left']+"px;top:"+styling['payments2']['top']+"px;font-size:"+styling['payments2']['fontSize']+"px;'>"+vatable+"</div>";
					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments3visible+payments3Bold+"position:absolute; list-style-type: none; left:"+styling['payments3']['left']+"px;top:"+styling['payments3']['top']+"px;font-size:"+styling['payments3']['fontSize']+"px;'>"+pagesubtotal+"</div>";
					pagectr = parseInt(pagectr) + 1;
					pagesubtotal=0;
				}
				pagesubtotal = parseFloat(pagesubtotal) + parseFloat(total);
				lamankadadr[pagectr] = lamankadadr[pagectr] + "<tr ><td style='"+tdbarcodevisible+tdbarcodeBold+"position:relative;width:"+styling['tdbarcode']['width']+"px;padding-left:"+styling['tdbarcode']['left']+"px;'>"+itemcode+"</td><td style='"+tdqtyvisible+tdqtyBold+"position:relative;width:"+styling['tdqty']['width']+"px;padding-left:"+styling['tdqty']['left']+"px;'>"+qty+"</td><td style='"+tddescriptionvisible+tddescriptionBold+"position:relative;width:"+styling['tddescription']['width']+"px;padding-left:"+styling['tddescription']['left']+"px;'> "+ description +" <span style='padding-left:20px;'>"+labeldisc+"</span> </td><td style='"+tdpricevisible+tdpriceBold+"position:relative;width:"+styling['tdprice']['width']+"px;padding-left:"+styling['tdprice']['left']+"px;'>"+number_format(price,2)+"</td><td style='"+tdtotalvisible+tdtotalBold+"position:relative;width:"+styling['tdtotal']['width']+"px;padding-left:"+styling['tdtotal']['left']+"px;'>"+number_format(origtotal,2)+" "+labeldisc2+"</td></tr>";
				rowctr = parseInt(rowctr) +1;
			}
			if(pagesubtotal > 0){
				var subtotal = (pagesubtotal / vat);
				var vatable = parseFloat(pagesubtotal) - parseFloat(subtotal);
				subtotal = subtotal.toFixed(2);
				vatable = vatable.toFixed(2);
				pagesubtotal = pagesubtotal.toFixed(2);
				lamankadadr[pagectr] = lamankadadr[pagectr] + "</table>";
				lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+paymentsvisible+paymentsBold+"position:absolute; list-style-type: none; left:"+styling['payments']['left']+"px;top:"+styling['payments']['top']+"px;font-size:"+styling['payments']['fontSize']+"px;'>"+subtotal+"</div>";
				lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments2visible+payments2Bold+"position:absolute; list-style-type: none; left:"+styling['payments2']['left']+"px;top:"+styling['payments2']['top']+"px;font-size:"+styling['payments2']['fontSize']+"px;'>"+vatable+"</div>";
				lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments3visible+payments3Bold+"position:absolute; list-style-type: none; left:"+styling['payments3']['left']+"px;top:"+styling['payments3']['top']+"px;font-size:"+styling['payments3']['fontSize']+"px;'>"+pagesubtotal+"</div>";
			}
			var printhtmlend = "";
			var reservedbyname ='';
			reservedbyname = salestype + " " + reservedbyname;
			var drnumctr =data.ctrnum;
			var cashier = data.cashier_name;
			printhtmlend = printhtmlend + "<div style='"+cashiervisible+cashierBold+"position:absolute;left:"+styling['cashier']['left']+"px;top:"+styling['cashier']['top']+"px;font-size:"+styling['cashier']['fontSize']+"px;'>"+cashier+"</div>";
			printhtmlend = printhtmlend + "<div style='"+remarksvisible+remarksBold+"position:absolute;left:"+styling['remarks']['left']+"px;top:"+styling['remarks']['top']+"px;font-size:"+styling['remarks']['fontSize']+"px;'>"+remarks+"</div>";
			printhtmlend = printhtmlend + "<div style='"+reservedvisible+reservedBold+"position:absolute;left:"+styling['reserved']['left']+"px;top:"+styling['reserved']['top']+"px;font-size:"+styling['reserved']['fontSize']+"px;'>"+reservedbyname+"</div>";
			printhtmlend = printhtmlend + "<div style='"+drnumvisible+drnumBold+"position:absolute;left:"+styling['drnum']['left']+"px;top:"+styling['drnum']['top']+"px;font-size:"+styling['drnum']['fontSize']+"px;'>"+drnumctr+"</div>";


			var termstxt =data.terms;
			var ponumtxt ='';
			var tintxt =data.tin_no;



			var termsvisible = (styling['terms']['visible']) ? 'display:inline-block;' : 'display:none;';
			var ponumvisible = (styling['ponum']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tinvisible = (styling['tin']['visible']) ? 'display:inline-block;' : 'display:none;';
			var termsbold = (styling['terms']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var ponumbold = (styling['ponum']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tinbold = (styling['tin']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';

			printhtmlend = printhtmlend + "<div style='"+termsvisible+termsbold+"position:absolute;left:"+styling['terms']['left']+"px;top:"+styling['terms']['top']+"px;font-size:"+styling['terms']['fontSize']+"px;'>"+termstxt+"</div>";
			printhtmlend = printhtmlend + "<div style='"+ponumvisible+ponumbold+"position:absolute;left:"+styling['ponum']['left']+"px;top:"+styling['ponum']['top']+"px;font-size:"+styling['ponum']['fontSize']+"px;'>"+ponumtxt+"</div>";
			printhtmlend = printhtmlend + "<div style='"+tinvisible+tinbold+"position:absolute;left:"+styling['tin']['left']+"px;top:"+styling['tin']['top']+"px;font-size:"+styling['tin']['fontSize']+"px;'>"+tintxt+"</div>";

			printhtmlend = printhtmlend + "</div>";
			var finalprint = "";
			for(var i in lamankadadr ){
				finalprint = finalprint + printhtml + lamankadadr[i] + printhtmlend;
			}
			Popup(finalprint);
		}

		function PopupWithStyle(data)
		{
			var mywindow = window.open('', 'new div', '');
			mywindow.document.write('<html><head><title></title><style></style>');
			/*optional stylesheet*/
			mywindow.document.write('<link rel="stylesheet" href="../css/bootstrap.css" type="text/css" />');
			mywindow.document.write('<style>.table-condensed-2{font-size:10px; } .table-condensed-2 > thead > tr > th, .table-condensed-2 > tbody > tr > th, .table-condensed-2 > tfoot > tr > th, .table-condensed-2 > thead > tr > td, .table-condensed-2 > tbody > tr > td, .table-condensed-2 > tfoot > tr > td {  padding: 1px;  }</style>');
			mywindow.document.write('</head><body style="padding:0;margin:0;">');
			mywindow.document.write(data);
			mywindow.document.write('</body></html>');
			setTimeout(function(){
				mywindow.print();
				mywindow.close();
				return true;
			},1000);

		}

		function Popup(data)
		{
			var mywindow = window.open('', 'new div', '');
			mywindow.document.write('<!DOCTYPE html><html><head><title></title><style></style>');
			/*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
			mywindow.document.write('</head><body style="padding:0;margin:0;">');
			mywindow.document.write(data);
			mywindow.document.write('</body></html>');
			mywindow.print();
			mywindow.close();
			return true;
		}


		$('body').on('click','#btnDownload',function(){
			var search = $('#searchSales').val();
			search = (search) ? search : 0;
			var b = $('#branch_id').val();
			b = (b) ? b : 0;
			var t = $('#terminals').val();
			t = (t) ? t : 0;
			var type = $('#sales_type').val();
			var tran_type = $('#tran_type').val();
			var item_id = $('#item_id').val();
			var member_id = $('#member_id').val();
			type = (type) ? type : 0;
			tran_type = (tran_type) ? tran_type : 0;
			var sortby = $('#sort_by').val();
			var ascdesc = $('#ascdesc').val();

			if(!ascdesc) {
				if(sortby) 	sortby = sortby + 'asc';
			} else {
				if(sortby) sortby = sortby + 'desc';
			}

			sortby = (sortby) ? sortby : 0;

			var date_from = $('#date_from').val();
			var date_to = $('#date_to').val();

			if(date_from && date_to){
				window.open(
					'excel_downloader.php?downloadName=sales&search='+search+'&b='+b+'&t='+t+'&type='+type+'&sortby='+sortby+'&tran_type='+tran_type+'&mem_id='+member_id+'&item_id='+item_id+'&date_from='+date_from+'&date_to='+date_to,
					'_blank' // <- This is what makes it open in a new window.
				);
			} else {
				alert("Please filter date when downloading sales record.")
			}



		});

		$('body').on('click','.btnFreight',function(){
			var payment_id = $(this).attr('data-payment_id');
			getFreights(payment_id);
		});

		function newsPrint(data,newsprint_type) {

			var member_name = data.member_name;

			var terms = data.terms;

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

			var memlisttest = '';
			if(localStorage['members']) {
				memlisttest = JSON.parse(localStorage['members']);
			}

			if(memlisttest) {
				for(var i in memlisttest) {
					var cur = memlisttest[i];
					if(cur.id == member_id_test) {
						station_name = cur.personal_address + "<br>Contact Person: " + cur.firstname + " " + cur.lastname + "<br>Contact #: " + cur.contact_number;
					}
				}
			}

			var styling = JSON.parse(localStorage['news_format']);
			//var fontFamily = "font-family: \"Times New Roman\", Times, serif;letter-spacing:1px;";
			var fontFamily = "font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif;";
			var datevisible = (styling['date']['visible']) ? 'display:block;' : 'display:none;';
			var membernamevisible = (styling['membername']['visible']) ? 'display:block;' : 'display:none;';
			var memberaddressvisible = (styling['memberaddress']['visible']) ? 'display:block;' : 'display:none;';
			var stationnamevisible = (styling['stationname']['visible']) ? 'display:block;' : 'display:none;';
			var stationaddressvisible = (styling['stationaddress']['visible']) ? 'display:block;' : 'display:none;';
			var itemtablevisible = (styling['itemtable']['visible']) ? 'display:block;' : 'display:none;';
			var paymentsvisible = (styling['payments']['visible']) ? 'display:block;' : 'display:none;';
			var payments2visible = (styling['payments2']['visible']) ? 'display:block;' : 'display:none;';
			var payments3visible = (styling['payments3']['visible']) ? 'display:block;' : 'display:none;';
			var cashiervisible = (styling['cashier']['visible']) ? 'display:block;' : 'display:none;';
			var remarksvisible = (styling['remarks']['visible']) ? 'display:block;' : 'display:none;';
			var reservedvisible = (styling['reserved']['visible']) ? 'display:block;' : 'display:none;';
			var tdbarcodevisible = (styling['tdbarcode']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tdqtyvisible = (styling['tdqty']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tddescriptionvisible = (styling['tddescription']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tdpricevisible = (styling['tdprice']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tdtotalvisible = (styling['tdtotal']['visible']) ? 'display:inline-block;' : 'display:none;';

			var dateBold = (styling['date']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var membernameBold = (styling['membername']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var memberaddressBold = (styling['memberaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var stationnameBold = (styling['stationname']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var stationaddressBold = (styling['stationaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var itemtableBold = (styling['itemtable']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var paymentsBold = (styling['payments']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var payments2Bold = (styling['payments2']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var payments3Bold = (styling['payments3']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var cashierBold = (styling['cashier']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var remarksBold = (styling['remarks']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var reservedBold = (styling['reserved']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdbarcodeBold = (styling['tdbarcode']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdqtyBold = (styling['tdqty']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tddescriptionBold = (styling['tddescription']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdpriceBold = (styling['tdprice']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdtotalBold = (styling['tdtotal']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var howMany = 1;
			var combinePage = "";

			for(var countPage = 1; countPage <= howMany; countPage++) { // all page

				var printhtml = "";
				printhtml = printhtml + "<div id='maindivforprinting' style='page-break-before: always;position:relative;"+fontFamily+"'>&nbsp;";
				printhtml = printhtml + "<div style='" + datevisible + dateBold + "position:absolute;top:" + styling['date']['top'] + "px; left:" + styling['date']['left'] + "px;font-size:" + styling['date']['fontSize'] + "px;'><br/><br/>" + output + " </div><div style='clear:both;'></div>";
				printhtml = printhtml + "<div style='" + membernamevisible + membernameBold + "position:absolute;top:" + styling['membername']['top'] + "px; left:" + styling['membername']['left'] + "px;font-size:" + styling['membername']['fontSize'] + "px;'>" + member_name + "</div>";
				printhtml = printhtml + "<div style='" + memberaddressvisible + memberaddressBold + "position:absolute;top:" + styling['memberaddress']['top'] + "px; left:" + styling['memberaddress']['left'] + "px;width:" + styling['memberaddress']['width'] + "px;font-size:" + styling['memberaddress']['fontSize'] + "px;'>" + station_name + "</div>";
				printhtml = printhtml + "<div style='" + stationnamevisible + stationnameBold + "position:absolute;top:" + styling['stationname']['top'] + "px; left:" + styling['stationname']['left'] + "px;font-size:" + styling['stationname']['fontSize'] + "px;'>" + station_id + "</div>";
				printhtml = printhtml + "<div style='" + stationaddressvisible + stationaddressBold + "position:absolute;top:" + styling['stationaddress']['top'] + "px; left:" + styling['stationaddress']['left'] + "px;width:" + styling['stationaddress']['width'] + "px;font-size:" + styling['stationaddress']['fontSize'] + "px;'>" + station_address + "</div>";
				printhtml = printhtml + "<table id='itemscon' style='position:absolute;top:" + styling['itemtable']['top'] + "px;left:" + styling['itemtable']['left'] + "px;font-size:" + styling['itemtable']['fontSize'] + "px;'> ";

				var drlimit = localStorage['dr_limit'];
				var lamankadadr = [];
				var pagectr = 1;
				var rowctr = 1;
				var pagesubtotal = 0;

				var vat = 1.12;
				drlimit = parseInt(drlimit) + 1;

				var testdata = data.item_list;
				for(var i in testdata) {
					var itemcode = testdata[i].item_code;
					var description = testdata[i].description;
					var b = testdata[i].barcode;
					var unit_name = testdata[i].unit_name;
					unit_name = (unit_name) ? unit_name : '';
					var qty = testdata[i].qty + "<td style='width:60px;'>"+unit_name+"</td>";
					var price = testdata[i].price;
					var discount = testdata[i].discount;
					var total = testdata[i].total;
					var origtotal = total;
					if(parseFloat(discount) > 0) {
						var perunitdisc = parseFloat(discount) / parseFloat(qty);
						var labeldisc = "<br/>(Disc. " + number_format(perunitdisc, 2) + ")";
						var labeldisc2 = "<br/>(" + number_format(discount, 2) + ")";
					} else {
						var labeldisc = '';
						var labeldisc2 = '';
					}
					labeldisc = '';
					labeldisc2 = '';

					if(rowctr % drlimit == 0) {
						var subtotal = (pagesubtotal / vat);
						var vatable = parseFloat(pagesubtotal) - parseFloat(subtotal);
						subtotal = subtotal.toFixed(2);
						vatable = vatable.toFixed(2);
						pagesubtotal = pagesubtotal.toFixed(2);
						lamankadadr[pagectr] = lamankadadr[pagectr] + "</table>";
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + paymentsvisible + paymentsBold + "position:absolute; list-style-type: none; left:" + styling['payments']['left'] + "px;top:" + styling['payments']['top'] + "px;font-size:" + styling['payments']['fontSize'] + "px;'>" + subtotal + "</div>";
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + payments2visible + payments2Bold + "position:absolute; list-style-type: none; left:" + styling['payments2']['left'] + "px;top:" + styling['payments2']['top'] + "px;font-size:" + styling['payments2']['fontSize'] + "px;'>" + vatable + "</div>";
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + payments3visible + payments3Bold + "position:absolute; list-style-type: none; left:" + styling['payments3']['left'] + "px;top:" + styling['payments3']['top'] + "px;font-size:" + styling['payments3']['fontSize'] + "px;'>&nbsp;&nbsp; Grand Total: " + number_format(pagesubtotal,2) + "</div>";
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments3visible+payments3Bold+"position:absolute; list-style-type: none; left:"+styling['payments3']['left']+"px;top:"+ (parseInt(styling['payments3']['top']) + parseInt(12)) +"px;font-size:"+styling['payments3']['fontSize']+"px;width:255px;border-bottom:1px solid #000;'>&nbsp;</div>";
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments3visible+payments3Bold+"position:absolute; list-style-type: none; left:"+styling['payments3']['left']+"px;top:"+(parseInt(styling['payments3']['top']) + parseInt(15))+"px;font-size:"+styling['payments3']['fontSize']+"px;width:255px;border-bottom:1px solid #000;'>&nbsp;</div>";
						pagectr = parseInt(pagectr) + 1;
						pagesubtotal = 0;
					}
					pagesubtotal = parseFloat(pagesubtotal) + parseFloat(total);
					lamankadadr[pagectr] = lamankadadr[pagectr] + "<tr ><td style='" + tdqtyvisible + tdqtyBold + "position:relative;width:" + styling['tdqty']['width'] + "px;padding-left:" + styling['tdqty']['left'] + "px;'>" + qty + "</td><td style='" + tdbarcodevisible + tdbarcodeBold + "position:relative;width:" + styling['tdbarcode']['width'] + "px;padding-left:" + styling['tdbarcode']['left'] + "px;'>" + itemcode + "</td><td style='" + tddescriptionvisible + tddescriptionBold + "position:relative;width:" + styling['tddescription']['width'] + "px;padding-left:" + styling['tddescription']['left'] + "px;'> " + description + " <span style='padding-left:20px;'>" + labeldisc + "</span> </td><td style='" + tdpricevisible + tdpriceBold + "position:relative;width:" + styling['tdprice']['width'] + "px;padding-left:" + styling['tdprice']['left'] + "px;text-align:right;'>" + number_format(price, 2) + "</td><td style='" + tdtotalvisible + tdtotalBold + "position:relative;width:" + styling['tdtotal']['width'] + "px;padding-left:" + styling['tdtotal']['left'] + "px;text-align:right;'>" + number_format(origtotal, 2) + " " + labeldisc2 + "</td></tr>";
					rowctr = parseInt(rowctr) + 1;
				}

				if(pagesubtotal > 0) {
					var consumable_payment =  data.consumable_total;
					if(parseFloat(consumable_payment) > 0){
						pagesubtotal = pagesubtotal - consumable_payment;
					}
					var subtotal = (pagesubtotal / vat);
					var vatable = parseFloat(pagesubtotal) - parseFloat(subtotal);
					subtotal = subtotal.toFixed(2);
					vatable = vatable.toFixed(2);
					pagesubtotal = pagesubtotal.toFixed(2);
					lamankadadr[pagectr] = lamankadadr[pagectr] + "</table>";
					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+paymentsvisible+paymentsBold+"position:absolute; list-style-type: none; left:"+styling['payments']['left']+"px;top:"+((styling['payments']['top']) - 12) +"px;font-size:"+styling['payments']['fontSize']+"px;'>("+consumable_payment+")</div>";

					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + paymentsvisible + paymentsBold + "position:absolute; list-style-type: none; left:" + styling['payments']['left'] + "px;top:" + styling['payments']['top'] + "px;font-size:" + styling['payments']['fontSize'] + "px;'>" + subtotal + "</div>";
					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + payments2visible + payments2Bold + "position:absolute; list-style-type: none; left:" + styling['payments2']['left'] + "px;top:" + styling['payments2']['top'] + "px;font-size:" + styling['payments2']['fontSize'] + "px;'>" + vatable + "</div>";
					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + payments3visible + payments3Bold + "position:absolute; list-style-type: none; left:" + styling['payments3']['left'] + "px;top:" + styling['payments3']['top'] + "px;font-size:" + styling['payments3']['fontSize'] + "px;'>&nbsp;&nbsp; Grand Total: " + number_format(pagesubtotal,2) + "</div>";
					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments3visible+payments3Bold+"position:absolute; list-style-type: none; left:"+styling['payments3']['left']+"px;top:"+ (parseInt(styling['payments3']['top']) + parseInt(12)) +"px;font-size:"+styling['payments3']['fontSize']+"px;width:255px;border-bottom:1px solid #000;'>&nbsp;</div>";
					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments3visible+payments3Bold+"position:absolute; list-style-type: none; left:"+styling['payments3']['left']+"px;top:"+(parseInt(styling['payments3']['top']) + parseInt(15))+"px;font-size:"+styling['payments3']['fontSize']+"px;width:255px;border-bottom:1px solid #000;'>&nbsp;</div>";
				}
				var printhtmlend = "";
				var reservedbyname = '';
				reservedbyname = data.sales_type + " " + reservedbyname;
				printhtmlend = printhtmlend + "<div style='" + cashiervisible + cashierBold + "position:absolute;left:" + styling['cashier']['left'] + "px;top:" + styling['cashier']['top'] + "px;font-size:" + styling['cashier']['fontSize'] + "px;'>" + localStorage['current_lastname'] + ", " + localStorage['current_firstname'] + "</div>";
				printhtmlend = printhtmlend + "<div style='" + remarksvisible + remarksBold + "position:absolute;left:" + styling['remarks']['left'] + "px;top:" + styling['remarks']['top'] + "px;font-size:" + styling['remarks']['fontSize'] + "px;'>" + remarks + "</div>";
				printhtmlend = printhtmlend + "<div style='" + reservedvisible + reservedBold + "position:absolute;left:" + styling['reserved']['left'] + "px;top:" + styling['reserved']['top'] + "px;font-size:" + styling['reserved']['fontSize'] + "px;'>" + reservedbyname + "</div>";
				//additional
				//additional
				var cdr = $('#custom_dr').val();
				var cpr = $('#custom_pr').val();
				var nextdr = parseInt(localStorage['dr']) + 1;
				var nextir = parseInt(localStorage['ir']) + 1;
				var control_num = '';
				if(newsprint_type == 1){
					var drnumctr =  nextdr;
					drnumctr = (cdr) ? cdr : nextdr;
					if(data.ctrnum && data.ctrnum != "" && data.ctrnum != "0" ){
						drnumctr = data.ctrnum;
					}
					console.log("Dr ctr" + drnumctr);
					var pref_dr = (localStorage['pref_dr']) ? localStorage['pref_dr'] : '';

					drnumctr =   drnumctr;
					control_num =  drnumctr;
				} else if(newsprint_type == 2){

					var irctrnum =  nextir;
					irctrnum = (cpr) ? cpr : nextir;
					if(data.ctrnum && data.ctrnum != "" && data.ctrnum != "0"){
						irctrnum = data.ctrnum;
					}
					console.log("IR ctr" + irctrnum);
					var pref_ir = (localStorage['pref_ir']) ? localStorage['pref_ir'] : '';

					irctrnum = irctrnum;
					control_num = irctrnum;
				}
				if(data.order_id){
					control_num += "<span style='display:block;'>Order ID: " +  data.order_id+"</span>";
				}

				var termstxt = data.terms;
				var ponumtxt = data.client_po;
				var tintxt = data.tin_no;

				var termsvisible = (styling['terms']['visible']) ? 'display:inline-block;' : 'display:none;';
				var drnumvisible = (styling['drnum']['visible']) ? 'display:inline-block;' : 'display:none;';
				var ponumvisible = (styling['ponum']['visible']) ? 'display:inline-block;' : 'display:none;';
				var tinvisible = (styling['tin']['visible']) ? 'display:inline-block;' : 'display:none;';
				var drnumbold = (styling['drnum']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var termsbold = (styling['terms']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var ponumbold = (styling['ponum']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var tinbold = (styling['tin']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';

				printhtmlend = printhtmlend + "<div style='" + drnumvisible + drnumbold + "position:absolute;left:" + styling['drnum']['left'] + "px;top:" + styling['drnum']['top'] + "px;font-size:" + styling['drnum']['fontSize'] + "px;'>" + control_num + "</div>";
				printhtmlend = printhtmlend + "<div style='" + termsvisible + termsbold + "position:absolute;left:" + styling['terms']['left'] + "px;top:" + styling['terms']['top'] + "px;font-size:" + styling['terms']['fontSize'] + "px;'>" + termstxt + "</div>";
				printhtmlend = printhtmlend + "<div style='" + ponumvisible + ponumbold + "position:absolute;left:" + styling['ponum']['left'] + "px;top:" + styling['ponum']['top'] + "px;font-size:" + styling['ponum']['fontSize'] + "px;'>" + ponumtxt + "</div>";
				printhtmlend = printhtmlend + "<div style='" + tinvisible + tinbold + "position:absolute;left:" + styling['tin']['left'] + "px;top:" + styling['tin']['top'] + "px;font-size:" + styling['tin']['fontSize'] + "px;'>" + tintxt + "</div>";


				printhtmlend = printhtmlend + "</div>";
				var finalprint = "";
				for(var i in lamankadadr) {
					finalprint = finalprint + printhtml + lamankadadr[i] + printhtmlend;
				}
				finalprint = replaceAll(finalprint, 'undefined', '');
				combinePage += "<div>" + finalprint + "</div>";

			}
			Popup(combinePage);
		}

		function getFreights(payment_id){
			var ret_html = "";
			$('.right-panel-pane').fadeIn(100);
			$('#right-pane-container').html('Fetching data...');
			$.ajax({
				url:'../ajax/ajax_sales_query.php',
				type:'POST',
				data: {functionName:'addFreight', payment_id:payment_id},
				success: function(data){
					ret_html += data;
					ret_html += "<input id='freight_payment_id' type='hidden' value='"+payment_id+"' />";
					ret_html += "<h4>Add Freight Charge</h4>";
					ret_html += "<div class='form-group'>";
					ret_html += "<strong>Amount:</strong> ";
					ret_html += "<input type='text'  id='freight_amount' class='form-control'> ";
					ret_html += "</div>";
					ret_html += "<div class='form-group'>";
					ret_html += "<strong>Remarks:</strong> ";
					ret_html += "<input type='text' id='freight_remarks' class='form-control'> ";
					ret_html += "</div>";
					ret_html += "<div class='form-group'>";
					ret_html += "<strong>Adjustment/Discount:</strong> ";
					ret_html += "<input type='text' id='freight_adjustment' class='form-control'> ";
					ret_html += "</div>";
					ret_html += "<div class='form-group'>";
					ret_html += "<strong>Charge:</strong> ";
					ret_html += "<input type='text' id='freight_total' disabled class='form-control'> ";
					ret_html += "</div>";
					ret_html += "<div class='form-group'>";
					ret_html += "";
					ret_html += "<button id='btnSubmitFreight' class='btn btn-default'>Submit</button> ";
					ret_html += "</div>";
					$('#right-pane-container').html(ret_html);
				},
				error:function(){

				}
			});
		}
		$('body').on('change','#freight_amount,#freight_adjustment',function(){
			var amount = $('#freight_amount').val();
			var adjustment = $('#freight_adjustment').val();
			if(!amount || isNaN(amount) || parseFloat(amount) < 0){
				amount = 0;
			}
			if(!adjustment || isNaN(adjustment)){
				adjustment = 0;
			}
			var total = parseFloat(amount) + parseFloat(adjustment);
			$('#freight_total').val(number_format(total,2));
		});
		$('body').on('click','#btnSubmitFreight',function(){
			var con = $(this);
			button_action.start_loading(con);
			var payment_id = $('#freight_payment_id').val();
			var amount = $('#freight_amount').val();
			var remarks = $('#freight_remarks').val();
			var adjustment = $('#freight_adjustment').val();
			if(payment_id && amount && remarks){
				$.ajax({
				    url:'../ajax/ajax_sales_query.php',
				    type:'POST',
				    data: {functionName:'saveFreight',adjustment:adjustment,payment_id:payment_id,amount:amount,remarks:remarks},
				    success: function(data){
				       alertify.alert(data);
					    getFreights(payment_id);
					    button_action.end_loading(con);
				    },
				    error:function(){
					    button_action.end_loading(con);
				    }
				})
			} else {
				alertify.alert('Invalid data.');
				button_action.end_loading(con);
			}

		});
	});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>