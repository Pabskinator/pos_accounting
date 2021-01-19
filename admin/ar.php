<?php
	// $user have all the properties and method of the current user
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('ar')) {
		// redirect to denied page
		Redirect::to(1);
	}
	$barcodeClass = new Barcode();
	$barcode_format = $barcodeClass->get_cr_format($user->data()->company_id);
	$styles =  '';

	if($barcode_format){
		$styles =  $barcode_format->styling;
	}

	$sales_type = new Sales_type();
	$sales_types = $sales_type->get_active('salestypes',array('company_id','=',$user->data()->company_id));


?>
	<!-- Page content -->
	<div id="page-content-wrapper">
		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<?php include 'includes/ar_nav.php' ?>
			<!-----------------    AR --------------------->

			<div id="con_ar" style='display:none;'>
				<?php
					if(Configuration::thisCompany('cebuhiq')){
						?>
						<div class="row">
							<div class="col-md-12 text-right">
								<button class='btn btn-default' id='btnPrintAllSalesMan'>AR All Sales Man</button>
							</div>
						</div>
					<?php
					}
				?>
				<h3>Account receivable</h3>

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Accounting</div>
					<div class="panel-body">
						<div class="row" >
							<div class="col-md-3">
								<?php
									if(!$user->hasPermission('credit_all')){
										$sales_type_ar_cls = new Sales_type();
										$types_ar = $sales_type_ar_cls->getMySalesType($user->data()->id);
									} else {

										$types_ar = $sales_types;
									}

								?>
								<div class="form-group">
									<select name="salestype_ar" id="salestype_ar" class='form-control'>
										<?php
											$is_ar_first = true;
											foreach($types_ar as $st){
												if($is_ar_first) {
													$selected = 'selected';
													$is_ar_first = false;
												} else {
													$selected = '';
												}
												echo  "<option value='$st->id' $selected>$st->name</option>";
											}
											if($http_host == 'cebuhiq.apollosystems.com.ph'){
												echo  "<option value='-1'>All</option>";
											}
										?>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select name="is_service_ar" id="is_service_ar" class='form-control'>
										<option value="0">Sales</option>
										<option value="1">Service</option>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' placeholder='Date From' id='dtFromAr'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' placeholder='Date To' id='dtToAr'>
								</div>
							</div>
							<?php
								if(!$user->hasPermission('wh_agent')){


								?>
									<div class="col-md-3">
										<div class="form-group">
											<input type="text" class='form-control'  id='userIdAr'>
										</div>
									</div>
							<?php
								}
							?>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' id='ar_branch_id' >
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select name="date_type" id="date_type" class='form-control'>
										<option value="0">All Status</option>
										<option value="1">Delivered and Picked Up Only</option>
									</select>
								</div>
							</div>
						</div>
						<div id='container1'></div>
					</div>
				</div>
			</div>

			<!----------------- END AR --------------------->

			<!-----------------    AR SUMMARY --------------------->
			<div id="con_ar_summary" style='display:none;'>
				<h3>AR Summary</h3>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Summary</div>
					<div class="panel-body" >
						<div class="row">
							<div class="col-md-4">
								<input type="text" class='form-control' id='branch_id'>
							</div>
							<div class="col-md-4">
								<button class='btn btn-default' id='btnARSummary'>Submit</button>
							</div>
							<div class="col-md-4"></div>
						</div>
						<br>
						<div id='container2'></div>
					</div>
				</div>

			</div>
			<!----------------- END AR SUMMARY --------------------->

			<!-----------------    BY TYPE  SUMMARY --------------------->
			<div id="con_ar_type_summary" style='display:none;'>
				<h3>AR Type Summary</h3>
				<div class='text-right'>
					<button class='btn btn-default' id='btnTypeSummary'>Print</button>
					<button class='btn btn-default' id='btnDlTypeSummary'>Download</button>
				</div>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Summary</div>
					<div class="panel-body" >
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<input name="st_branch_id" class='form-control' id="st_branch_id">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" placeholder='Date From' class='form-control' id='st_dt_from'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" placeholder='Date To' class='form-control' id='st_dt_to'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select type="text"  class='form-control' id='st_date_type'>
										<option value="0">All Status</option>
										<option value="1">Delivered and Picked Up Only</option>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<button class='btn btn-default' id='btnGetByType'>Submit</button>
							</div>
						</div>

						<br>
						<div id='container5'></div>
					</div>
				</div>

			</div>
			<!----------------- END AR SUMMARY --------------------->

			<!-----------------    agent SUMMARY --------------------->
			<div id="con_ar_agent" style='display:none;'>
				<h3>AR Agent Summary</h3>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">
						<div class="row">
							<div class="col-md-6">Agent</div>
							<div class="col-md-6 text-right">
								<button class='btn btn-default btn-sm' id='btnDownloadArByAgent'><i class='fa fa-download'></i></button>
							</div>
						</div>

					</div>
					<div class="panel-body" >
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' id='agent_id'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" placeholder='Date From' class='form-control' id='agent_dt_from'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" placeholder='Date To' class='form-control' id='agent_dt_to'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select type="text"  class='form-control' id='agent_date_type'>
										<option value="0">All Status</option>
										<option value="1">Delivered and Picked Up Only</option>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' id='agent_branch_id'>
								</div>
							</div>
							<div class="col-md-3">
								<button class='btn btn-default' id='btnGetAgent'>Submit</button>
							</div>

						</div>
						<br>
						<div id='container3'></div>
					</div>
				</div>

			</div>
			<!----------------- END agent SUMMARY --------------------->
			<!-----------------    aging SUMMARY --------------------->
			<div id="con_aging" style='display:none;'>
				<div class="row">
					<div class="col-md-12 text-right">
						<button class='btn btn-default' id='btnPrintAging'>Print Aging</button>
						<button class='btn btn-default' id='btnDownloadAging'>Download Aging</button>
					</div>
				</div>
				<h3>Aging</h3>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Aging</div>
					<div class="panel-body" >
						<div class="row">

							<div class="col-md-3">
								<input name="aging_branch_id" class='form-control' id="aging_branch_id">
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' placeholder='Date From' id='aging_dt_from'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' placeholder='Date To' id='aging_dt_to'>
								</div>
							</div>

							<div class="col-md-3">
								<div class="form-group">
									<select name="aging_date_type" id="aging_date_type" class='form-control'>
										<option value="0">All Status</option>
										<option value="1">Delivered and Picked Up Only</option>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<input type="button" id='btnSubmitAging' class='btn btn-primary' value='Submit'>
							</div>

							<div class="col-md-3">

							</div>
						</div>
						<br>
						<div id="container4"></div>
					</div>
				</div>

			</div>
			<!----------------- END agent SUMMARY --------------------->


		</div>
	</div> <!-- end page content wrapper-->

	<script>
		$(function() {
			$("#agent_id").select2({
				placeholder: 'Search Agent',
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
			$("#userIdAr").select2({
				placeholder: 'Search Agent',
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
			$('#aging_branch_id').select2({
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
			$('#st_branch_id').select2({
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
			$('#agent_branch_id').select2({
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
			$('#ar_branch_id').select2({
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
			$('#salestype').select2({
				placeholder: 'Search Sales Type',
				allowClear: true
			});

			$('body').on('click','#btnPrintAging',function(){
				var branch_id = $('#aging_branch_id').val();
				var dt_from = $('#aging_dt_from').val();
				var dt_to = $('#aging_dt_to').val();
				var date_type = $('#aging_date_type').val();

				$.ajax({
					url:'../ajax/ajax_sales_query.php',
					type:'POST',
					data: {functionName:'aging',dl:0,date_type:date_type,dt_from:dt_from,dt_to:dt_to,branch_id:branch_id},
					success: function(data){
						var header = "";
						var company_name = localStorage['company_name'];
						var header = "<h1>" + company_name + "</h1>";
						header += "<p>Summary of Account Receivables</p>";
						header += "<p>As of "+currentDate()+"</p>";

						popUpPrintWithStyle(header + data);
					},
					error:function(){

					}
				});
			});
			$('body').on('click','#btnDownloadAging',function(){
				var branch_id = $('#aging_branch_id').val();
				var dt_from = $('#aging_dt_from').val();
				var dt_to = $('#aging_dt_to').val();
				var date_type = $('#aging_date_type').val();

				window.open(
					'../ajax/ajax_sales_query.php?functionName=aging&branch_id='+branch_id+'&dt_from='+dt_from+'&dt_to='+dt_to+'&date_type='+date_type+'&dl=1',
					'_blank' //
				);

			});
			$('body').on('change','#userIdAr',function(){
				getAR();
			});

			showContainer(true, false,false,false,false);
			function currentDate(){
				var today = new Date();
				var dd = today.getDate();
				var mm = today.getMonth() + 1; //January is 0!
				var yyyy = today.getFullYear();

				if (dd < 10) {
					dd = '0' + dd;
				}

				if (mm < 10) {
					mm = '0' + mm;
				}

				today = mm + '/' + dd + '/' + yyyy;
				return today;
			}

			$('body').on('click', '.btn_nav', function(e) {
				e.preventDefault();
				var con = $(this).attr('data-con');
				if(con == 1) {
					showContainer(true, false, false,false,false);
				} else if(con == 2) {
					showContainer(false, true, false,false,false);
				} else if(con == 3) {
					showContainer(false, false,true,false,false);
				} else if (con == 4){
					showContainer(false, false,false,true,false);
				}else if (con == 5){
					showContainer(false, false,false,false,true);
				}
			});



			$('body').on('click','#btnARSummary',function(){
				getARSummary();
			});
			$('body').on('click','#btnGetAgent',function(){
				getArAgent();
			});
			function showContainer(c1, c2,c3,c4,c5) {
				var con_ar = $('#con_ar');
				var con_ar_summary = $('#con_ar_summary');
				var con_ar_agent = $('#con_ar_agent');
				var con_aging = $('#con_aging');
				var con_ar_type_summary = $('#con_ar_type_summary');

				con_ar.hide();
				con_ar_summary.hide();
				con_ar_agent.hide();
				con_aging.hide();
				con_ar_type_summary.hide();

				if(c1) {
					con_ar.fadeIn(300);
					getAR();
				} else if(c2) {
					con_ar_summary.fadeIn(300);
					getARSummary();
				}else if(c3) {
					con_ar_agent.fadeIn(300);
					getArAgent();
				}else if(c4) {
					con_aging.fadeIn(300);
					getAging();
				}else if(c5) {
					con_ar_type_summary.fadeIn(300);
					getTypes();
				}
			}
			$('body').on('click','#btnTypeSummary',function(){
				printTypes();
			});
			$('body').on('click','#btnDlTypeSummary',function(){
				dlTypeSummary();
			});
			function dlTypeSummary(){

				var dt_from = $('#st_dt_from').val();
				var dt_to = $('#st_dt_to').val();
				var date_type = $('#st_date_type').val();
				var branch_id = $('#st_branch_id').val();

				window.open(
					'../ajax/ajax_sales_query.php?functionName=creditSummaryByType&dt_from='+dt_from+'&dt_to='+dt_to+'&date_type='+date_type+'&branch_id='+branch_id+'&dl='+1,
					'_blank' // <- This is what makes it open in a new window.
				);
			}

			function printTypes(){
				var con = $('#btnTypeSummary');
				button_action.start_loading(con);
				var company_name = localStorage['company_name'];
				var header = "<h1>" + company_name + "</h1>";
				header += "<p>Summary of Account Receivables</p>";
				header += "<p>As of "+currentDate()+"</p>";
				$.ajax({
					url:'../ajax/ajax_sales_query.php',
					type:'POST',
					data: {functionName:'creditSummaryByType'},
					success: function(data){
						button_action.end_loading(con);
						popUpPrintWithStyle(header+data);
					},
					error:function(){

					}
				});
			}
			$('body').on('click','#btnGetByType',function(){
				getTypes();
			});
			function getTypes(){
				var dt_from = $('#st_dt_from').val();
				var dt_to = $('#st_dt_to').val();
				var date_type = $('#st_date_type').val();
				var branch_id = $('#st_branch_id').val();

				var con = $('#btnTypeSummary');
				button_action.start_loading(con);
				$('#container5').html('Loading, please wait...');
					$.ajax({
					    url:'../ajax/ajax_sales_query.php',
					    type:'POST',
					    data: {functionName:'creditSummaryByType',branch_id:branch_id,dt_from:dt_from,dt_to:dt_to,date_type:date_type},
					    success: function(data){
						    $('#container5').html(data);
						    button_action.end_loading(con);
					    },
					    error:function(){

					    }
					});

			}
			function getAging(){
				var branch_id = $('#aging_branch_id').val();
				var dt_from = $('#aging_dt_from').val();
				var dt_to = $('#aging_dt_to').val();
				var date_type = $('#aging_date_type').val();
				$('#container4').html('Loading');
				$.ajax({
					url:'../ajax/ajax_sales_query.php',
					type:'POST',
					data: {functionName:'aging',date_type:date_type,dt_from:dt_from,dt_to:dt_to,branch_id:branch_id},
					success: function(data){
						$('#container4').html(data);
					},
					error:function(){

					}
				});
			}

			$('body').on('click','#btnSubmitAging',function(){
				getAging();
			});
			function getArAgent(){
					var agent_id = $('#agent_id').val();
					var dt_from = $('#agent_dt_from').val();
					var dt_to = $('#agent_dt_to').val();
					var date_type = $('#agent_date_type').val();
					var branch_id = $('#agent_branch_id').val();
					$('#container3').html('Loading...');
					$.ajax({
						url:'../ajax/ajax_accounting.php',
						type:'POST',
						data: {functionName:'arBYSalesman',agent_id: agent_id,branch_id:branch_id, dt_from:dt_from,dt_to:dt_to,date_type:date_type},
						success: function(data){
							$('#container3').html(data);
						},
						error:function(){

						}
					})

			}
			$('body').on('click','#btnDownloadArByAgent',function(){

				getArAgentExcel();
			});
			function getArAgentExcel(){

				var agent_id = $('#agent_id').val();
				var dt_from = $('#agent_dt_from').val();
				var dt_to = $('#agent_dt_to').val();
				var date_type = $('#agent_date_type').val();
				var branch_id = $('#agent_branch_id').val();
				if(!agent_id){
					alert("Please Choose agent first.");
					return;
				}
				window.open(
					'../ajax/ajax_accounting.php?functionName=arBYSalesman&agent_id='+agent_id+'&dt_from='+dt_from+'&dt_to='+dt_to+'&date_type='+date_type+'&branch_id='+branch_id+'&dl='+1,
					'_blank' // <- This is what makes it open in a new window.
				);
			}
			function getARSummary(){
				$('#container2').html('Loading...');
				$.ajax({
					url:'../ajax/ajax_accounting.php',
					type:'POST',
					data: {functionName:'arSummary',branch_id: $('#branch_id').val()},
					success: function(data){
						$('#container2').html(data);
					},
					error:function(){

					}
				})
			}


			$('body').on('click','#btnSubmitAR',function(){
				getAR();
			});

			$('body').on('change','#salestype_ar,#is_service_ar,#ar_branch_id,#date_type',function(){
				getAR();
			});


			function getAR(){

				var dt1 = $('#dtFromAr').val();
				var dt2 = $('#dtToAr').val();
				var salestype_ar = $('#salestype_ar').val();
				var is_service_ar = $('#is_service_ar').val();
				var agent_id = $('#userIdAr').val();
				var branch_id = $('#ar_branch_id').val();
				var date_type = $('#date_type').val();

				agent_id = agent_id ? agent_id :0;
				if(dt1 && !dt2){
					return;
				}
				if(dt2 && !dt1){
					return;
				}

				$.ajax({
					url:'../ajax/ajax_accounting.php',
					type:'POST',
					beforeSend: function(){
						$('#container1').html('Loading...');
					},
					data: {functionName:'ar2',date_type:date_type,dt1:dt1,dt2:dt2,branch_id:branch_id,salestype_ar:salestype_ar,is_service_ar:is_service_ar,agent_id:agent_id},
					success: function(data){


						$('#container1').html(data);

					},
					error:function(){

					}
				});

			}
			
			$('body').on('click','#btnPrintAllSalesMan',function(){
				printARSummary();
			});
			
			function printARSummary(){

				var dt1 = $('#dtFromAr').val();
				var dt2 = $('#dtToAr').val();
				var salestype_ar = $('#salestype_ar').val();
				var is_service_ar = $('#is_service_ar').val();
				var agent_id = $('#userIdAr').val();
				var branch_id = $('#ar_branch_id').val();
				var date_type = $('#date_type').val();

				agent_id = agent_id ? agent_id :0;
				if(dt1 && !dt2){
					return;
				}
				if(dt2 && !dt1){
					return;
				}



				$.ajax({
					url:'../ajax/ajax_accounting.php',
					type:'POST',
					beforeSend: function(){

					},
					data: {functionName:'printUnpaidCredit',date_type:date_type,dt1:dt1,dt2:dt2,branch_id:branch_id,salestype_ar:salestype_ar,is_service_ar:is_service_ar,agent_id:agent_id},
					success: function(data){
						var company_name = localStorage['company_name'];
						var header = "<h1>" + company_name + "</h1>";

						popUpPrintWithStyle(header + data);

					},
					error:function(){

					}
				});

			}





			$('#st_dt_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#st_dt_from').datepicker('hide');
			});
			$('#st_dt_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#st_dt_to').datepicker('hide');
			});
			$('#agent_dt_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#agent_dt_from').datepicker('hide');
			});
			$('#agent_dt_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#agent_dt_to').datepicker('hide');
			});
			$('#aging_dt_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#aging_dt_to').datepicker('hide');
			});
			$('#aging_dt_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#aging_dt_from').datepicker('hide');
			});

			$('#dtFromAr').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dtFromAr').datepicker('hide');
				getAR();
			});
			$('#dtToAr').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dtToAr').datepicker('hide');
				getAR();
			});
			$('#ar_dt1').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#ar_dt1').datepicker('hide');
			});

			$('#ar_dt2').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#ar_dt2').datepicker('hide');
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



			function arExcel(){
				var sales_type_ar =  $('#salestype_ar').val();
				var is_service_ar = $('#is_service_ar').val();
				var dt1 = $('#dtFromAr').val();
				var dt2 = $('#dtToAr').val();
				var branch_id = $('#ar_branch_id').val();
				var date_type = $('#date_type').val();

				window.open(
					'../ajax/ajax_accounting.php?functionName=excelAr2&salestype_ar='+sales_type_ar+'&is_service_ar='+is_service_ar+'&dt1='+dt1+'&dt2='+dt2+'&branch_id='+branch_id+'&date_type='+date_type,
					'_blank' // <- This is what makes it open in a new window.
				);
			}

			$('body').on('click','#btnArExcel',function(){
				arExcel();
			});



		});
	</script><?php require_once '../includes/admin/page_tail2.php'; ?>