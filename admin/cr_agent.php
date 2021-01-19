<?php
	// $user have all the properties and method of the current user
	require_once '../includes/admin/page_head2.php';
	include 'constants.php';


	$is_agent = 0;
	$agent_name ='';
	if($user->hasPermission('wh_agent')){
		$is_agent = 1;
		$agent_name = ucwords($user->data()->firstname . " ". $user->data()->lastname);
	}
	$sales_type = new Sales_type();
	$sales_types = $sales_type->get_active('salestypes',array('company_id','=',$user->data()->company_id));

	$barcodeClass = new Barcode();
	$barcode_format = $barcodeClass->get_cr_format($user->data()->company_id);
	$styles =  '';



	$by_branch = $barcodeClass->get_format_by_branch($user->data()->branch_id,"CR");

	if($by_branch){

		$barcode_format = $by_branch;
	}

	$by_user = $barcodeClass->get_format_by_user($user->data()->id,"CR");

	if($by_user){

		$barcode_format = $by_user;

	}
	if($barcode_format){
		$styles =  $barcode_format->styling;
	}





?>
	<!-- Page content -->
	<div id="page-content-wrapper">
		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">

			<!-----------------    AR --------------------->
			<input type="hidden" id='is_agent' value='<?php echo $is_agent; ?>'>
			<input type="hidden" id='agent_name' value='<?php echo $agent_name; ?>'>
			<input type="hidden" id='cr_cr_num' value=''>

			<div class="btn-group hidden-xs" role="group" aria-label="..." style='margin-bottom:10px;'>

					<a class='btn btn-default btnCon' data-con='1' href='#'  title='Transaction'>
							<span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>Transaction</span></span></a>
					<a class='btn btn-default btnCon'  data-con='2' href='#'  title='CR list'>
							<span class='glyphicon glyphicon-book'></span> <span class='hidden-xs'>CR List</span></span></a>

			</div>




			<div id="con_collection" >
				<h3>Collection report</h3>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Accounting</div>
					<div class="panel-body"  >
						<div class="row">
							<div class="col-md-6"></div>
							<div class="col-md-6 text-right">

							</div>
						</div>
						<div id='transaction_list_container'>
							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
										<select name="salestype" id="salestype" class='form-control' multiple>
											<option value=""></option>
											<?php
												foreach($sales_types as $st){
													$curid = (isset($id)) ?  $editMem->data()->salestype : 0;
													if($st->id == $curid) {
														$selected = 'selected';
													} else {
														$selected = '';
													}
													echo  "<option value='$st->id' $selected>$st->name</option>";
												}
											?>
										</select>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" class='form-control' id='cr_date1' placeholder='Date From'>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" class='form-control' id='cr_date2' placeholder='Date To'>
									</div>
								</div>


										<input type='hidden' name="cr_agent_id" id="cr_agent_id" >

								<div class="col-md-3">
									<div class="form-group">
										<select name="cr_type" id="cr_type" class='form-control' multiple>
											<option value="1">Cash</option>
											<option value="2">Credit Card</option>
											<option value="3">Check</option>
											<option value="4">Bank Transfer</option>
										</select>
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group">
										<select name="show_with_cr" id="show_with_cr" class='form-control'>
											<option value="0">Hide with CR number</option>
											<option value="1">Show with CR number</option>
										</select>
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group">
										<button class='btn btn-default' id='btnFilterReport'><i class='fa fa-search'></i> Filter</button>
									</div>
								</div>
							</div>
							<div id='container3'></div>
						</div>
						<!----------------- END COLLECTION --------------------->
						<div id='con_list' style='display:none;'>
							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" class='form-control' id='cr_list_from' placeholder='Date From'>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" class='form-control' id='cr_list_to' placeholder='Date To'>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<button class='btn btn-default' id='btnCrListFilter'>Submit</button>

									</div>
								</div>

							</div>
							<div id="container4"></div>
						</div>
						<!----------------- END LIST --------------------->
					</div>
				</div>
			</div>



		</div>
	</div> <!-- end page content wrapper-->

	<script>
		$(function() {
			var styles = '<?php echo $styles; ?>';

			var is_agent = '<?php if($user->hasPermission('wh_agent')) echo 1; else echo 0; ?>';
			var agent_id = '<?php echo $user->data()->id; ?>';

			$('body').on('click','.btnCon',function(){
				var con = $(this);
				var num = con.attr('data-con');
				var con1 = $('#transaction_list_container');
				var con2 = $('#con_list');
				con1.hide();
				con2.hide();
				if(num == 1){
					con1.fadeIn(300);
					$('#cr_cr_num').val('');
				} else if (num == 2){
					con2.fadeIn(300);
					showCRList();
				}
			});

			$('#cr_agent_id').val(agent_id);


			$('#salestype').select2({
				placeholder: 'Search Sales Type',
				allowClear: true
			});
			$('#cr_type').select2({
				placeholder: 'Search Payment Type',
				allowClear: true
			});








			$('#cr_date1').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#cr_date1').datepicker('hide');
			});

			$('#cr_date2').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#cr_date2').datepicker('hide');
			});

			$('body').on('click','#btnSaveCRNumber',function(){
				var has_pending_for_approval = $('#has_pending_for_approval').val();
				if(has_pending_for_approval == 1){
					//alert("Request should be approve first.");
					//return;
				}
				var crNumber = $('#crNumber').val();
				var cr_payment_ids = $('#cr_payment_ids').val();
				var cr_log_ids = $('#cr_log_ids').val();
				var type = $('#cr_type').val();
				var dt = $('#cr_date1').val();
				var dt_to = $('#cr_date2').val();
				var user_id = $('#cr_user_id').val();
				var paid_by = $('#cr_paid_by').val();
				var cr_include_dr = $('#cr_include_dr').val();
				var cr_include_ir = $('#cr_include_ir').val();
				var from_service = $('#from_service').val();
				var cr_override = $('#overrided_item_per_page').val()
				var agent_id = $('#cr_agent_id').val()

				if(crNumber && cr_payment_ids){
					var det_arr = [];
					$('#table-collection-report > table > tbody > tr').each(function(){
						var row = $(this);
						var id = row.attr('data-id');
						var method = row.attr('data-method');
						var pid = row.attr('data-pid');
						var com =  row.children().eq(11).text();
						com = (com) ? com : 0;
						det_arr.push({
							delivery_date: row.children().eq(0).text(),
							delivery_receipt: row.children().eq(1).text(),
							sales_invoice: row.children().eq(2).text(),
							client_name: row.children().eq(3).text(),
							receipt_amount: row.children().eq(4).text(),
							deduction: row.children().eq(5).text(),
							paid_amount: row.children().eq(6).text(),
							bank_name: row.children().eq(7).text(),
							check_no: row.children().eq(8).text(),
							check_date: row.children().eq(9).text(),
							terms: row.children().eq(10).text(),
							comm: com,
							id: id,
							method: method,
							pid: pid
						});
					});



					alertify.confirm("Are you sure you want to continue?", function(e){
						if(e){
							$.ajax({
								url:'../ajax/ajax_accounting.php',
								type:'POST',
								data: {functionName:'updateCrNumber',agent_id:agent_id,cr_log_ids:cr_log_ids,cr_override:cr_override,det_arr: JSON.stringify(det_arr),from_service:from_service,cr_include_dr:cr_include_dr,cr_include_ir:cr_include_ir,dt_to:dt_to,user_id:user_id,paid_by:paid_by,type:type,dt:dt,payment_ids:cr_payment_ids,crNumber:crNumber},
								success: function(data){
									alertify.alert(data);
									getCollectionReport();
								},
								error:function(){

								}
							});
						}
					});
				} else {
					alertify.alert("Please enter CR number.");
				}

			});


			getCollectionReport();


			function getCollectionReport(){
				var salestype = $('#salestype').val();
				var date_from = $('#cr_date1').val();
				var date_to = $('#cr_date2').val();
				var terminal_id = $('#cr_terminal_id').val();
				var user_id = $('#cr_user_id').val();
				var agent_id = $('#cr_agent_id').val();
				var paid_by = $('#cr_paid_by').val();
				var type = $('#cr_type').val();
				var cr_num = $('#cr_cr_num').val();
				var from_service = $('#from_service').val();
				var show_with_cr = $('#show_with_cr').val();
				var cr_include_dr = $('#cr_include_dr').val();
				var cr_include_ir = $('#cr_include_ir').val();

				$('.loading', window.parent.document).show();
				$.ajax({
					url:'../ajax/ajax_accounting.php',
					type:'POST',
					beforeSend:function(){
						$('#container3').html('Loading...');
					},
					data: {functionName:'collectionReport',agent_id:agent_id,cr_include_ir:cr_include_ir,cr_include_dr:cr_include_dr,show_with_cr:show_with_cr,from_service:from_service,cr_num:cr_num,paid_by:paid_by,user_id:user_id,terminal_id:terminal_id,type:type,dt1:date_from,dt2:date_to,salestype:salestype},
					success: function(data){
						$('#container3').html(data);
						$('.loading', window.parent.document).hide();
					},
					error:function(){

					}
				});
			}
			$('#cr_list_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#cr_list_from').datepicker('hide');
			});
			$('#cr_list_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#cr_list_to').datepicker('hide');
			});

			function showCRList(){
				$('#container4').html('Loading...');
				var dt_from  = $('#cr_list_from').val();
				var dt_to  = $('#cr_list_to').val();
				var agent_id = $('#cr_agent_id').val();


				$.ajax({
					url:'../ajax/ajax_accounting.php',
					type:'POST',
					data: {functionName:'collectionReportList',agent_id:agent_id,dt_from:dt_from,dt_to:dt_to},
					success: function(data){
						$('#container4').html(data);
					},
					error:function(){

					}
				});
			}
			$('body').on('click','#btnFilterReport',function(){
				$('#cr_cr_num').val('');
				getCollectionReport()
			});

			$('body').on('click','.btnShowDataCR',function(){

				$('#cr_date1').val('');
				$('#cr_date2').val('');

				$('#cr_include_dr').val('');
				$('#cr_include_ir').val('');

				$('#cr_type').select2('val',null);
				$('#cr_user_id').select2('val',null);
				$('#cr_paid_by').select2('val',null);

				var cr_number = $(this).attr('data-cr_number');

				$('#cr_cr_num').val(cr_number);
				getCollectionReport();
				$('#con_list').hide();
				$('#transaction_list_container').fadeIn(300);
			});

			$('body').on('click','#btnPrintCollectionReport',function(){
				button_action.start_loading($('#btnPrintCollectionReport'));
				printCR();
			});

			function printCR(){
				var salestype = $('#salestype').val();
				var date_from = $('#cr_date1').val();
				var date_to = $('#cr_date2').val();
				var terminal_id = $('#cr_terminal_id').val();
				var user_id = $('#cr_user_id').val();
				var type = $('#cr_type').val();
				var cr_num = $('#cr_cr_num').val();
				/*
				window.open(
					'../ajax/ajax_accounting.php?functionName=printCollectionReport&dt1='+date_from+'&dt2='+date_to+'&salestype='+salestype+'&terminal_id='+terminal_id+'&user_id='+user_id+'&type='+type+'&cr_num='+cr_num,
					'_blank' // <- This is what makes it open in a new window.
				); */
				printEmptyCR();

			}

			function printEmptyCR(){

				var det_arr = [];

				$('#table-collection-report > table > tbody > tr').each(function(){
					var row = $(this);
					var sales_invoice = replaceAll(row.children().eq(2).text(),'Gal.','');
					sales_invoice = replaceAll(sales_invoice,'gal.','');
					sales_invoice = (sales_invoice) ? sales_invoice : '';
					sales_invoice = sales_invoice.trim();
					var ar_number =  row.children().eq(13).find('input').val();
					ar_number = (ar_number) ? ar_number : '';
					det_arr.push(
						{
							delivery_date: row.children().eq(0).text(),
							delivery_receipt: row.children().eq(1).text(),
							sales_invoice: sales_invoice,
							client_name: row.children().eq(3).text(),
							receipt_amount: row.children().eq(4).text(),
							deduction: row.children().eq(5).text(),
							paid_amount: row.children().eq(6).text(),
							bank_name: row.children().eq(7).text(),
							check_no: row.children().eq(8).text(),
							check_date: row.children().eq(9).text(),
							terms: row.children().eq(10).text(),
							ar_number:ar_number,
						}
					);

				});

				var receipt_amount = $('#footer_receipt_amount').text();
				var deduction = $('#footer_deduction').text();
				var collected_amount = $('#footer_collected_amount').text();

				$.ajax({
					url:'../ajax/ajax_accounting.php',
					type:'POST',
					dataType:'json',
					data: {functionName:'crEmpty', items: JSON.stringify(det_arr)},
					success: function(data){
						button_action.end_loading($('#btnPrintCollectionReport'));
						prepareCollectionReport(data.result,data.type,collected_amount,receipt_amount,deduction);
					},
					error:function(){
						button_action.end_loading($('#btnPrintCollectionReport'));
					}
				})
			}

			function prepareCollectionReport(data,type,total_collected,total_receipt,total_deduction){
				var cur_date = Date.now() /1000;
				var d = new Date(cur_date * 1000);
				var month = d.getMonth()+1;
				var day = d.getDate();
				var salestype = $('#salestype option:selected').text();
				var date_output = (month<10 ? '0' : '') + month + '/' +
					(day<10 ? '0' : '') + day + '/' + d.getFullYear();

				var layout;
				try{
					layout = JSON.parse(styles);
				} catch(e){
					layout = false;
				}
				var itemtablestyle = "style='position:absolute;top:" + layout['itemtable'].top+"px;left:"+layout['itemtable'].left+"px;font-size:"+layout['itemtable'].fontSize+"px;'";
				var datestyle = "style='position:absolute;top:" + layout['date'].top+"px;left:"+layout['date'].left+"px;font-size:"+layout['date'].fontSize+"px;'";
				var salestypestyle = "style='position:absolute;top:" + layout['salestype'].top+"px;left:"+layout['salestype'].left+"px;font-size:"+layout['salestype'].fontSize+"px;'";
				var totalamountstyle = "style='position:absolute;top:" + layout['totalamount'].top+"px;left:"+layout['totalamount'].left+"px;font-size:"+layout['totalamount'].fontSize+"px;'";
				var totalreceiptstyle = "style='position:absolute;top:" + layout['totalreceipt'].top+"px;left:"+layout['totalreceipt'].left+"px;font-size:"+layout['totalreceipt'].fontSize+"px;'";
				var deductionstyle = "style='position:absolute;top:" + layout['deduction'].top+"px;left:"+layout['deduction'].left+"px;font-size:"+layout['deduction'].fontSize+"px;'";
				var printhtml = "";
				var fontFamily = "font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif;";
				printhtml = printhtml + "<div id='maindivforprinting' style='page-break-before: always;position:relative;"+fontFamily+"'>&nbsp;";
				printhtml= printhtml +  "<div "+datestyle+">"+  date_output+ " </div><div style='clear:both;'></div>";
				printhtml= printhtml +  "<div  "+salestypestyle+">"+  salestype+ " </div><div style='clear:both;'></div>";
				printhtml += "<table "+itemtablestyle+">";
				var page_arr = [];

				var num_per_page = parseInt($('#overrided_item_per_page').val()) + 1;
				num_per_page = (num_per_page) ? num_per_page : 23;
				if(isNaN(num_per_page)){
					alertify.alert("Invalid number of items per page.");
					return;
				}
				var page_head = printhtml;
				var page_tail = "";
				page_tail += "</div>";
				console.log("Ok");
				if(data.length){
					console.log("Ok1");
					var ctr = 1;
					var content = "";
					var cur_total_collected = 0;
					var cur_total_receipt = 0;
					var cur_total_deduction = 0;

					for(var i in data){
						var footer ='';
						if( ctr % num_per_page == 0){
							// footer = "<tr><td></td><td></td><td></td><td></td><td>"+number_format(cur_total_receipt,2)+"</td><td>"+number_format(cur_total_deduction,2)+"</td><td>"+number_format(cur_total_collected,2)+"</td><td></td><td></td><td></td><td></td></tr>";
							footer = "</table>";
							footer += "<div  "+totalreceiptstyle+">"+number_format(cur_total_receipt,2)+" </div><div style='clear:both;'></div>";
							footer += "<div  "+deductionstyle+">"+number_format(cur_total_deduction,2)+" </div><div style='clear:both;'></div>";
							footer += "<div  "+totalamountstyle+">"+number_format(cur_total_collected,2)+" </div><div style='clear:both;'></div>";
							footer += "</div>";
							cur_total_collected = 0;
							cur_total_receipt = 0;
							cur_total_deduction = 0;
							page_arr.push(page_head + content + footer + page_tail);
							content = "";
						}
						var row = "<tr style='line-height:20px;'>";

						for(var j in data[i]){

							var style_col = "col" + (parseInt(j) + 1);
							var v = data[i][j];
							var td_styles="";

							if(layout[style_col]){
								td_styles += "width:"+layout[style_col].width+"px;padding-left:"+layout[style_col].left+"px;";
							}
							if(type == 1){ // NORMAL VIEW
								if(j == 3){  // client
									if(v && v.length > 16){
										v = v.substr(0,16)
									}
								}

								if(j == 4){ // receipt amount
									if(v && !isNaN(v)){
										cur_total_receipt = parseFloat(cur_total_receipt) + parseFloat(v);

									} else {
										v = 0;
									}
									//console.log(cur_total_receipt  + " + " + parseFloat(v) + " = " + cur_total_receipt );
									v = number_format(v,2);
									td_styles += "text-align:right;";
								}

								if(j == 5){ // DEDUCTION
									if(v){
										var tempdeduction = v.split('|');
										v = tempdeduction[0];
										if(v && !isNaN(v)){
											cur_total_deduction = parseFloat(cur_total_deduction) + parseFloat(v);
										}

										if(v == '0.00'){
											v= '';
										}
										if(v == '0'){
											v= '';
										}
										if(tempdeduction[1]){
											v += tempdeduction[1];
										}

										td_styles += "text-align:center;";
									} else {
										v= '';
									}

								}

								if(j == 6){ // paid amount
									var temp = replaceAll(v,'Cash'," ");
									temp = replaceAll(temp,'Cheque'," ");

									var splitp = temp.split(":");
									var amount = 0;
									//console.log(splitp);
									splitp = splitp.map(function(value){
										return value.trim();
									});
									if(splitp[0]){
										amount = replaceAll(splitp[0],",","") + parseFloat(amount);
									}
									if(splitp[1]){
										amount = replaceAll(splitp[1],",","") + parseFloat(amount);
									}
									if(splitp[2]){
										amount = parseFloat(replaceAll(splitp[2],",","")) + parseFloat(amount);
									}
									if(splitp[3]){
										amount = parseFloat(replaceAll(splitp[3],",","")) + parseFloat(amount);
									}
									cur_total_collected = parseFloat(cur_total_collected) + parseFloat(amount);
									td_styles += "text-align:right;";

									if(!isNaN(v)){
										v = number_format(v,2);
									}
								}

							} else if (type == 2){

								if(j == 1){  // client
									if(v && v.length > 16){
										v = v.substr(0,16) + "..."
									}
								}

								if(j == 4){  // receipt amount
									if(v && !isNaN(v)){
										cur_total_receipt = parseFloat(cur_total_receipt) + parseFloat(v);

									} else {
										v = 0;
									}
									//console.log(cur_total_receipt  + " + " + parseFloat(v) + " = " + cur_total_receipt );
									v = number_format(v,2);
									td_styles += "text-align:right;";
								}

								if(j == 5){ // DEDUCTION
									if(v){
										var tempdeduction = v.split('|');
										v = tempdeduction[0];
										if(v && !isNaN(v)){
											cur_total_deduction = parseFloat(cur_total_deduction) + parseFloat(v);

										}

										if(v == '0.00'){
											v= '';
										}
										if(v == '0'){
											v= '';
										}
										if(tempdeduction[1]){
											v += tempdeduction[1];
										}

										td_styles += "text-align:center;";
									} else {
										v= '';
									}

								}

								if(j == 6){  // paid amount
									var temp = replaceAll(v,'Cash'," ");
									temp = replaceAll(temp,'Cheque'," ");

									var splitp = temp.split(":");
									var amount = 0;
									//console.log(splitp);
									splitp = splitp.map(function(value){
										return value.trim();
									});
									if(splitp[0]){
										amount = replaceAll(splitp[0],",","") + parseFloat(amount);
									}
									if(splitp[1]){
										amount = replaceAll(splitp[1],",","") + parseFloat(amount);
									}
									if(splitp[2]){
										amount = parseFloat(replaceAll(splitp[2],",","")) + parseFloat(amount);
									}
									if(splitp[3]){
										amount = parseFloat(replaceAll(splitp[3],",","")) + parseFloat(amount);
									}
									cur_total_collected = parseFloat(cur_total_collected) + parseFloat(amount);
									td_styles += "text-align:right;";

									if(!isNaN(v)){
										v = number_format(v,2);
									}

								}
							}


							var td = "<td style='"+td_styles+"'>";

							td +=v;
							td += "</td>";
							row += td;

						}
						row += "</tr>";
						//console.log(row);
						content += row;
						ctr++;
					}
					if(content != ""){
						while(ctr % num_per_page != 0){
							content += "<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
							ctr ++;
						}
						//var f  = "<tr><td></td><td></td><td></td><td></td><td>"+number_format(cur_total_receipt,2)+"</td><td>"+number_format(cur_total_deduction,2)+"</td><td>"+number_format(cur_total_collected,2)+"</td><td></td><td></td><td></td><td></td></tr>";
						var f = "</table>";
						f += "<div  "+totalreceiptstyle+">"+number_format(cur_total_receipt,2)+" </div><div style='clear:both;'></div>";
						f += "<div  "+deductionstyle+">"+number_format(cur_total_deduction,2)+" </div><div style='clear:both;'></div>";
						f += "<div  "+totalamountstyle+">"+number_format(cur_total_collected,2)+" </div><div style='clear:both;'></div>";
						f += "</div>";

						page_arr.push(page_head + content +  f +page_tail);
						content = "";
					}
				}

				var all_pages = "";
				for(var p in page_arr){
					all_pages += page_arr[p];
				}
				Popup(all_pages);
			}

			function Popup(data)
			{

				var mywindow = window.open('', 'new div', '');
				mywindow.document.write('<html><head><title></title><style></style>');
				mywindow.document.write('</head><body style="padding:0;margin:0;">');
				mywindow.document.write(data);
				mywindow.document.write('</body></html>');
				mywindow.print();
				mywindow.close();
				return true;

			}


		});
	</script><?php require_once '../includes/admin/page_tail2.php'; ?>