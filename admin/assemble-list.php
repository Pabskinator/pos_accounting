<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	//if(!$user->hasPermission('createorder')) {
	// redirect to denied page
	//	Redirect::to(1);
	//}

?>



	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Item list </h1>
		</div>
		<div id="orderholder">

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}
		?>
	<?php include 'includes/spare_nav.php'; ?>
		<div class="panel panel-primary">
			<!-- Default panel contents -->
			<div class="panel-heading">
				<div class="row">
					<div class="col-md-6">Assemble item list</div>
					<div class="col-md-6 text-right">
						<button class='btn btn-default' id='btnDownload'><i class='fa fa-download'></i></button>
					</div>
				</div>

			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
						<select name="status" id="status" class='form-control'>
							<option value="1"><?php echo (Configuration::getValue('a_step1')) ? Configuration::getValue('a_step1') : 'Pending'; ?></option>
							<option value="2"><?php echo (Configuration::getValue('a_step2')) ? Configuration::getValue('a_step2') : 'For Assembly'; ?></option>
							<option value="3"><?php echo (Configuration::getValue('a_step3')) ? Configuration::getValue('a_step3') : 'Assembled'; ?></option>
							<option value="4">Cancelled</option>
						</select>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<input type="text" id='dt_from' placeholder='Start Date' class='form-control'>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<input type="text" id='dt_to' placeholder='End Date' class='form-control'>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<input type="text" id='branch_id'  class='form-control'>
						</div>
					</div>

				</div>

				<div id="con_list" style='margin-top:10px;'>

				</div>

			</div>
		</div>

	<!-- end page content wrapper-->
	<div class="modal fade" id="myModalSerial" style='z-index:999999' tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id='serialtitle'>Add Serial</h4>
				</div>
				<div class="modal-body" id='serialbody'>
					<input type="hidden" id='serial_type' value='1'>
					<table id='tblSerials' class='table'>

					</table>
					<div class='text-right'><button id='saveSerials' class='btn btn-default'>Save</button></div>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

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
		$(function() {
			$('#branch_id').select2({
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
			$('#dt_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_from').datepicker('hide');
				withDate();
			});
			$('#dt_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_to').datepicker('hide');
				withDate();
			});
			$('body').on('click','#btnDownload',function(){
				var status = $('#status').val();
				if(!status) status = 1;

				var branch_id = $('#branch_id').val();
				var dt_from = $('#dt_from').val();
				var dt_to = $('#dt_to').val();


				window.open(
					'../ajax/ajax_query2.php?functionName=getAssembleList&status='+status+'&branch_id='+branch_id+'&dt_from='+dt_from+'&dt_to='+dt_to+'&is_dl=1',
					'_blank'
				);


			});
			function withDate(){
				if($('#dt_to').val() && $('#dt_from').val()){
					getList();
				}
			}
			$('body').on('change','#branch_id',function(){
				getList()
			});
			getList();
			$('body').on('change','#status',function(){

				getList();
			});


			$('body').on('click','.btnAssembleItem',function(){
				var con= $(this);
				var item_id = con.attr('data-item_id');
				var payment_id = con.attr('data-payment_id');
				var qty = con.attr('data-qty');
				$.ajax({
					url: '../ajax/ajax_product.php',
					type: 'POST',
					data: {functionName: 'selectSerials', payment_id: payment_id, qty: qty, item_id: item_id},
					dataType: 'json',
					success: function(data) {
						var html = '';
						$('#serial_type').val(1);
						if(data.length){
							for(var i in data){
								var cnt = parseInt(i) + 1;
								html += "<tr data-item_id='"+data[i].item_id+"' data-payment_id='"+payment_id+"' data-id='"+data[i].id+"' ><td>"+cnt+"</td><td><input class='form-control' value='"+data[i].serial_no+"'></td></tr>";
							}
						}
						$('#tblSerials').html(html);
						$('#myModalSerial').modal('show');
					},
					error: function() {

					}
				});
			});

			$('body').on('click','.btnAssembleItemNoPaymentID',function(){
				var con= $(this);
				var item_id = con.attr('data-item_id');
				var details_id = con.attr('data-details_id');
				var qty = con.attr('data-qty');
				$.ajax({
					url: '../ajax/ajax_product.php',
					type: 'POST',
					data: {functionName: 'showSerialsAssembly', details_id: details_id, qty: qty, item_id: item_id},
					dataType: 'json',
					success: function(data) {
						var html = '';
						$('#serial_type').val(2);
						if(data.length){
							for(var i in data){
								var cnt = parseInt(i) + 1;
								html += "<tr data-item_id='"+data[i].item_id+"' data-details_id='"+details_id+"' ><td>"+cnt+"</td><td><input class='form-control' value='"+data[i].serial_no+"'></td></tr>";
							}
						}
						$('#tblSerials').html(html);
						$('#myModalSerial').modal('show');
					},
					error: function() {

					}
				});
			});

			$('body').on('click','#saveSerials',function(){
				var serial_type = $('#serial_type').val();
				var serials = [];
				if(serial_type == 1){

					var payment_id =0;
					$('#tblSerials tr').each(function(){
						var row = $(this);
						var serial_no = row.children().eq(1).find('input').val();
						var id = row.attr('data-id');
						var item_id = row.attr('data-item_id');
						payment_id = row.attr('data-payment_id');
						serials.push({item_id:item_id,id:id,serial_no:serial_no});
					});

					$.ajax({
						url: '../ajax/ajax_product.php',
						type: 'POST',
						data: {
							functionName: 'saveSerials',
							payment_id: payment_id,
							details: JSON.stringify(serials)
						},
						success: function(data) {
							alertify.alert(data);
							$('#myModalSerial').modal('hide');

						},
						error: function() {

						}
					});
				} else {

					var details_id =0;
					$('#tblSerials tr').each(function(){
						var row = $(this);
						var serial_no = row.children().eq(1).find('input').val();
						var id = row.attr('data-id');
						var item_id = row.attr('data-item_id');
						details_id = row.attr('data-details_id');
						serials.push({item_id:item_id,id:id,serial_no:serial_no});
					});

					$.ajax({
						url: '../ajax/ajax_product.php',
						type: 'POST',
						data: {
							functionName: 'saveSerialsAssembly',
							details_id: details_id,
							details: JSON.stringify(serials)
						},
						success: function(data) {
							alertify.alert(data);
							$('#myModalSerial').modal('hide');

						},
						error: function() {

						}
					});
				}

			});
			
			$('body').on('click','#saveWhOrder',function(){
				var order_id = $('#wh_id_number').val();
				var assembly_id = $('#saveWhOrder').attr('data-id');

				$.ajax({
				    url:'../ajax/ajax_service_item.php',
				    type:'POST',
				    data: {functionName:'saveWhOrderIdNumber',order_id: order_id,assembly_id:assembly_id},
				    success: function(data){
						alert(data);

				    },
				    error:function(){

				    }
				});
			});

			function getList(){
				var status = $('#status').val();
				if(!status) status = 1;

				var branch_id = $('#branch_id').val();
				var dt_from = $('#dt_from').val();
				var dt_to = $('#dt_to').val();



				$.ajax({
				    url:'../ajax/ajax_query2.php',
				    type:'POST',
				    data: {functionName:'getAssembleList',status:status,branch_id:branch_id,dt_from:dt_from,dt_to:dt_to},
					beforeSend: function(){
						$('.loading').show();
						$('#con_list').html('Loading...');
					},
				    success: function(data){
					    $('#con_list').html(data);
					    $('.loading').hide();
				    },
				    error:function(){

					    $('#con_list').html('Error fetching data.');
					    $('.loading').hide();
				    }
				})
			}
			$('body').on('click','#btnCancel',function(){
				var con = $(this);
				var oldval = con.html();
				alertify.confirm("Are you sure you want to cancel this transaction?",function(e){
					var id = con.attr('data-id');

					if(e){
						con.html('Loading...');
						con.attr('disabled',true);
						$.ajax({
							url:'../ajax/ajax_query2.php',
							type:'POST',
							data: {functionName:'assembleListCancel',id:id},
							success: function(data){
								$('#myModal').modal('hide');
								alertify.alert(data);

								getList();

							},
							error:function(){

							}
						});
					}



				});

			});

			$('body').on('click','.btnDetails',function(){
				var id = $(this).attr('data-id');
				var row = $(this).parents('tr');
				var cur_branch = row.children().eq(1).text();
				$('#myModal').modal('show');

				$.ajax({
				    url:'../ajax/ajax_query2.php',
				    type:'POST',
					beforeSend: function(){
						$('#mbody').html('Loading...');
					},
				    data: {functionName:'getAssembleDetails',id:id,b_name:cur_branch },
				    success: function(data){
					    stop();
					    $('#mbody').html(data);
					    var timediff = $('#timeDiff').val();
					    if(timediff != '0'){
						    display_c (timediff);
					    }

				    },
				    error:function(){

				    }
				});

			});


			$('body').on('click','#btnConvert',function(){
				var btncon = $(this);
				var id = btncon.attr('data-id');
				var oldval = btncon.html();
				btncon.attr('disabled',true);
				btncon.html('Loading...');

				alertify.confirm('Are you sure you want to convert raw materials to set items?',function(e){
					if(e){
						var lst = [];
						$('#tblDetails tbody > tr').each(function(){
							var row = $(this);
							var item_id = row.attr('data-item_id');
							var det_id = row.attr('data-det_id');
							var qty = row.attr('data-qty');
							var output_qty = row.children().eq(4).find('input').val();
							if(item_id && qty && output_qty){
								var rack_id = row.children().eq(3).find('select').val();
								lst.push({item_id:item_id,qty:qty,rack_id:rack_id,output_qty:output_qty,det_id:det_id});
							}
						});

						lst = JSON.stringify(lst);
						$.ajax({
						    url:'../ajax/ajax_query2.php',
						    type:'POST',
						    data: {functionName:'convertSpareparts',id:id,lst:lst},
						    success: function(data){
							    tempToast('info',"<p>"+data+"</p>","<h4>Information!</h4>");
							      $('#myModal').modal('hide');

							      btncon.attr('disabled',false);
							      btncon.html(oldval);
							      getList();

						    },
						    error:function(){

						    }
						});
					} else {
						btncon.attr('disabled',false);
						btncon.html(oldval);
					}
				});

			});

			function sortByStockman(a,b){
				var aName = a.stock_man.toLowerCase();
				var bName = b.stock_man.toLowerCase();
				return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
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
			$('body').on('click','#btnPrintRacks',function(){
				var rack_location = $('#hid_rack_location').val();
				var con = $(this);
				var b_name = con.attr('data-b_name');
				var print_id = con.attr('data-print_id');
				var wh_id = con.attr('data-wh_id');
				var machine_list = con.attr('data-machine');
				var list = "";
				if(machine_list){
					if(machine_list.indexOf(',')>0){
						machine_list = machine_list.split(",");
						for(var i in machine_list){
							if(i == 0){
								list += machine_list[i] + "<br>"
							} else {
								list += "<span style='display: inline-block;margin-left: 60px;' >" + machine_list[i] + "</span><br>"
							}
							
						}
					} else {
						list = machine_list;
					}
				}


				var per_page = 1000;
				try{
					rack_location = JSON.parse(rack_location);
					rack_location.sort(sortByStockman);

					var date_obj = new Date();
					var  curDate = date_obj.getMonth() + "/" + date_obj.getDay() + "/" + date_obj.getFullYear();
					var page = "<div class='perpage' style='page-break-after:always;' >";

					page += "<h1 class='text-center'>"+localStorage['company_name']+"</h1>";

					page += "<p style='float:left;width:60%'>";
					page += "Date: " + curDate + "<br>";
					page += "Branch: "+b_name + "<br>";
					page += "Machine: "+list;
					page +=	"</p>";
					page += "<p style='float:left;width:35%' class='text-right'>";
					page += "ASSEMBLE ID# " +print_id+"<br>";
					page += "ORDER ID# " +wh_id;
					page += "</p>";
					page += "<div class='' style='font-size:10px;' >";

					page += "<table style='font-size:10px;'  class='table table-bordered table-condensed'>";
					page += "<tr style='min-height:18px;'><th>Item</th><th>Quantity</th><th>Racking</th></tr>";




					var pageitem = [];
					var strholder ='';
					var ctr = 1;

					for(var j in rack_location){

						strholder += "<tr style='min-height:18px;'><td style='width:500px;'>" + rack_location[j].description + "</td><td>"+rack_location[j].qty+"</td><td style='width:300px;'>";
						strholder += "<div>" +rack_location[j].rack+"</div>";
						strholder += "</td></tr>";
						if(ctr % per_page == 0) {
							pageitem.push(strholder);
							strholder = '';
						}
						ctr += 1;

					}

					var num = Math.ceil((ctr / per_page) * per_page);
					if(ctr < per_page) {
						while(ctr != num + 1) {
							strholder += "<tr style='height:18px;'><td></td><td></td><td></td></tr>";
							ctr += 1;
						}
						pageitem.push(strholder);
						strholder = '';
					} else {
						while(ctr != num + 1) {
							strholder += "<tr style='height:18px;'><td></td><td></td><td></td></tr>";
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
				} catch(e){

				}

			});
			$('body').on('click','#btnPrepare',function(){
				var btncon = $(this);
				var id = btncon.attr('data-id');
				var oldval = btncon.html();
				btncon.attr('disabled',true);
				btncon.html('Loading...');

				alertify.confirm('Are you sure you want to process request?',function(e){
					if(e){

						$.ajax({
							url:'../ajax/ajax_query2.php',
							type:'POST',
							data: {functionName:'prepareSpareparts',id:id},
							success: function(data){
								tempToast('info',"<p>"+data+"</p>","<h4>Information!</h4>");
								$('#myModal').modal('hide');
								btncon.attr('disabled',false);
								btncon.html(oldval);
								getList();
							},
							error:function(){

							}
						});
					} else {
						btncon.attr('disabled',false);
						btncon.html(oldval);
					}

				});
			});
			var mytime;
			function display_c (start) {
				window.start = parseFloat(start);
				var end = 0 // change this to stop the counter at a higher value
				var refresh = 1000; // Refresh rate in milli seconds
				if( window.start >= end ) {
					mytime = setTimeout( display_ct,refresh )
				} else {
					alert("Time Over ");
				}
			}

			var  display_ct = function() {
				// Calculate the number of days left
				var days = Math.floor(window.start / 86400);
				// After deducting the days calculate the number of hours left
				var hours = Math.floor((window.start - (days * 86400 ))/3600)
				// After days and hours , how many minutes are left
				var minutes = Math.floor((window.start - (days * 86400 ) - (hours *3600 ))/60)
				// Finally how many seconds left after removing days, hours and minutes.
				var secs = Math.floor((window.start - (days * 86400 ) - (hours *3600 ) - (minutes*60)))

				var daysLabel = "";
				var hrsLabel = "";
				var minutesLabel = "";
				var secondLabel = "";

				if(days > 0){
					var dayUnit = (days > 1)  ? "days" : "day";
					daysLabel += days + " "+dayUnit+" ";
				}
				if(hours > 0){
					var hrUnit = (hours > 1)  ? "hours" : "hour";
					hrsLabel += hours + " "+hrUnit+" ";
				}
				if(minutes > 0){
					var minUnit = (minutes > 1)  ? "minutes" : "minute";
					minutesLabel += minutes + " "+minUnit+" ";
				}
				if(secs > 0){
					var secUnit = (secs > 1)  ? "seconds" : "second";
					secondLabel += secs + " "+secUnit+" ";
				}

				var x = "Pending " + daysLabel + hrsLabel + minutesLabel + secondLabel + " ago";

				$('#timeCtr').html(x);
				window.start = window.start + 1;

				display_c(window.start);
			}

			function stop() {
				clearTimeout(mytime);
			}
		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>