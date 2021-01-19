<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';


?>


	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">

			<div class="content-header">
				<h1><span id="menu-toggle" class='glyphicon glyphicon-list'></span> Rebates</h1>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
						<a class='btn btn-default btn_nav' data-con='1' title='Detailed List' href='#'>
							<span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>Detailed List</span>
						</a>
						<a class='btn btn-default btn_nav' data-con='2' title='Summary ' href='#'>
							<span class='glyphicon glyphicon-list-alt'></span> <span class='hidden-xs'>Summary List</span>
						</a>
					</div>
				</div>
			</div>
			<div class="panel panel-primary">
				<div class="panel-heading">

				</div>
				<div class="panel-body">

					<div id='con1'>
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<?php
										$sales_type = new Sales_type();
										$sales_types = $sales_type->get_active('salestypes',['1','=','1']);
										if($sales_types){
											?>

											<select name="sales_type_id" id="sales_type_id" class='form-control'>
												<option value="">Select Type</option>
												<?php foreach($sales_types as $st){
													?>
													<option value="<?php echo $st->id; ?>"><?php echo $st->name; ?></option>
													<?php
												} ?>
											</select>
											<?php
										}
									?>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" autocomplete="off" class='form-control' id='dt_from' placeholder='Date From'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" autocomplete="off" class='form-control' id='dt_to' placeholder='Date To'>
								</div>
							</div>
						</div>

						<div id="holder"></div>
					</div>

					<div style='display:none;' id="con2">
							<div class="row">
								<div class="col-md-3">
									<input autocomplete="off" placeholder='Enter Year' type="text" id='year' class='form-control'>
								</div>
							</div>
						<div id="holder2"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end page content wrapper-->

	<script>

		$(document).ready(function() {

			showCon(1);

			$("#year").datepicker( {
				format: " yyyy",
				viewMode: "years",
				minViewMode: "years"
			}).on('changeDate', function(e){
				$('#year').datepicker('hide');
				setTimeout(function(){
					getSummary();
				},500);

			});

			$('body').on('click','.btn_nav',function(){
				var con = $(this);
				var c = con.attr('data-con');
				showCon(c);
			});

			function showCon(c){
				var con1 = $('#con1');
				var con2 = $('#con2');
				var con3 = $('#con3');
				con1.hide();
				con2.hide();
				con3.hide();
				if ( c == 1 ){
					con1.show();
				} else if ( c == 2 ){
					con2.show();
					getSummary();
				} else if ( c == 3 ){
					con3.show();
				}
			}

			function getSummary(){

				var year = $('#year').val();
				$('#holder2').html('Fetching records. Please wait...');
				$.ajax({
				    url:'../ajax/ajax_avision.php',
				    type:'POST',
					dataType:'json',
				    data: {functionName:'getRebateSummary',year:year},
				    success: function(data){
						var types = data.types;
					    var items = data.items;
					    var html = "";
					    var months_arr = ['','Jan','Feb','March','April','May','June','July','Aug','Sept','Oct','Nov','Dec'];
						if(types.length){
							html += "<br><table class='table table-bordered table-condensed'>";
							html += "<tr>";
							html += "<th>Store Type</th>";

							for(var i =1; i<=12 ; i++){
								html += "<th>" + months_arr[i]+ "</th>";
							}
							html += "</tr>";
							console.log(items);
							for(var t in types){
								html += "<tr>";
								html += "<td style='border-top:1px solid #ccc;'>"+types[t]+"</td>";
								for(var i =1; i<=12 ; i++){
									html += "<td style='border-top:1px solid #ccc;'>"+items[types[t]][i]+"</td>";
								}
								html += "</tr>";
							}
							html += "</table>";
							$('#holder2').html(html);
						} else {
							$('#holder2').html("<br><div class='alert alert-info'>No record found</div>");
						}

				    },
				    error:function(){

				    }
				});

			}

			$('#dt_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_from').datepicker('hide');
				changeDate();
			});

			$('#dt_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_to').datepicker('hide');
				changeDate();
			});

			getList();



			var timer;

			$("#search").keyup(function(){
				var searchtxt = $("#search");
				clearTimeout(timer);
				timer = setTimeout(function() {
					if(searchtxt.val()){
						searchtxt.val(searchtxt.val().trim());
					}
					getList();
				}, 1000);

			});

			$('body').on('change','#sales_type_id',function(){
				getList();
			});

			function changeDate(){

				if($('#dt_from').val() && $('#dt_to').val()){
					getList();
				}

			}
			function displayDetails(data){

				var tbl = "";
				tbl += "<table id='tblForApproval' class='table table-bordered'>";
				tbl += "<tr><th>Type</th><th>Client</th><th>Date</th><th>Amount</th></tr>";
				var total = 0;

				for(var i in data){
						tbl += "<tr>";
						tbl += "<td>"+data[i].sales_type_name+"</td>";
						tbl += "<td>"+data[i].member_name+"</td>";
						tbl += "<td>"+data[i].created_at+"</td>";
						tbl += "<td>"+data[i].rebate+"</td>";
						tbl += "</tr>";

					total = parseFloat(total) + parseFloat(data[i].rebate);
				}

				tbl += "</table>";
				tbl += "<p>Total: <strong>" + number_format(total,2) + "</strong></p>";
				$('#holder').html(tbl);

			}
			function getList(){

				var dt_from = $('#dt_from').val();
				var dt_to = $('#dt_to').val();
				var sales_type_id = $('#sales_type_id').val();

				$.ajax({
				    url:'../ajax/ajax_avision.php',
				    type:'POST',
					dataType:'json',
				    data: {functionName:'getRebateList',dt_from:dt_from,dt_to:dt_to,sales_type_id:sales_type_id},
				    success: function(data){
						if(data.length){
							displayDetails(data);
						}else {
							$('#holder').html("No Record");
						}
				    },
				    error:function(){

				    }
				});
			}

		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>