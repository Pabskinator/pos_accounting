<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('wh_reports')) {
		// redirect to denied page
		Redirect::to(1);
	}

?>

	<!-- Page content -->
	<div id="page-content-wrapper">
		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					Reports
				</h1>
			</div>
			<div style='margin-bottom:15px;'>
			<div class="btn-group" role="group" aria-label="...">
				<button type="button" id='navCon1'class="btn btn-default"><i class='fa fa-home'></i> Home</button>
				<button type="button"  id='navCon2' class="btn btn-default"><i class='fa fa-list'></i> Order Reports</button>
				<button type="button"  id='navCon3' class="btn btn-default"><i class='fa fa-arrow-left'></i> Back load reports</button>
				<?php if(Configuration::getValue('order_pending_member') == 1){
					?>
					<button type="button"  id='navCon4' class="btn btn-default"><i class='fa fa-list-alt'></i> Member to order</button>
					<?php
				}?>
				<button type="button"  id='navCon5' class="btn btn-default"><i class='fa fa-barcode'></i> Items</button>

				<!-- <button type="button"  id='navCon3'class="btn btn-default"><i class='fa fa-pencil'></i></button>-->
			</div>
			</div>
			<div id="con1" style='display:none;'>
				<div class="panel panel-default">
					<div class="panel-heading"><strong>Order count for the last ten days</strong></div>
					<div class="panel-body">
						<div id="wh_lastTen" class='col-md-12' style='height:400px;'></div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading"><strong>Pending Orders</strong></div>
					<div class="panel-body">
						<div id="wh_pendingOrders" class='col-md-12' style='height:400px;'></div>
					</div>
				</div>
				<hr />
				<div class="row">
					<div class="col-md-6">
						<div class="panel panel-default">
							<div class="panel-heading"><strong>Top Agent Base On Order</strong></div>
							<div class="panel-body">
								<div id="wh_topAgent" class='col-md-12' style='height:300px;'></div>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="panel panel-default">
							<div class="panel-heading"><strong>Top Member Base On Order</strong></div>
							<div class="panel-body">
								<div id="wh_topMember" class='col-md-12' style='height:300px;'></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="con2" style='display:none;'>
				<p><strong class='text-danger'>Filters: </strong></p>
				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<input type="text" placeholder='Search...' class='form-control' id='txtSearch'>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<select id="branch_id" name="branch_id" class="form-control">
								<option value=""></option>
								<?php
									$branch = new Branch();
									$branches =  $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));
									foreach($branches as $b){
										?>
										<option value='<?php echo $b->id ?>' ><?php echo $b->name;?> </option>
										<?php
									}
								?>
							</select>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<input type="text" class='form-control' id='member_id'>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<select id='status' class='form-control'>
								<option></option>
								<option value="1">For approval</option>
								<option value="2">Shipping</option>
								<option value="3">Warehouse</option>
								<option value="4">Deliveries</option>
								<option value="-4">Pickup</option>
								<option value="5">Declined</option>
							</select>
						</div>
					</div>
					<div class="col-md-3">
							<div class="form-group">
								<select id="user_id" name="user_id" class="form-control">
									<option value=""></option>
									<?php
										$usercls = new User();
										$user_list =  $usercls->get_active('users',array('company_id' ,'=',$user->data()->company_id));
										foreach($user_list as $u){
											?>
											<option value='<?php echo $u->id ?>' ><?php echo $u->lastname . ", " . $u->firstname . " " . $u->middelname;?> </option>
											<?php
										}
									?>
								</select>
							</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<input type="text" placeholder='From' class='form-control' id='txtFrom'>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<input type="text" placeholder='To' class='form-control' id='txtTo'>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">

						</div>
					</div>
				</div>
				<input type="hidden" id="hiddenpage" />
				<div id="holder"></div>
			</div>
			<div id="con3" style='display:none;'>
				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<input type="text" class='form-control' id='backload_search' placeholder='Search Order ID'>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<select id="backload_branch_id" name="backload_branch_id" class="form-control">
								<option value=''></option>
								<?php
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

							</select>
						</div>
					</div>
					<div class="col-md-3"><button id='btnSubmitBackload' title='Submit' class='btn btn-default btn-sm'>Submit</button></div>
					<div class="col-md-3 text-right">
						<button id='btnDownloadExcel' title='Download Excel' class='btn btn-default btn-sm'><i class='fa fa-download'></i></button>
					</div>
				</div>
				<input type="hidden" id="hiddenpage2" />
				<div id="holder2"></div>
			</div>
			<div id="con4" style='display:none;'>
				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<input type="text" class='form-control' id='pending_member_search' placeholder='Search'>
						</div>
					</div>

				</div>
				<input type="hidden" id="hiddenpage3" />
				<div id="holder3"></div>
			</div>
			<div id="con5" style='display:none;'>
				<div class="row">
					<div class="col-md-3">
						<div class="form-group">

						</div>
					</div>

				</div>
				<input type="hidden" id="hiddenpage4" />
				<div id="holder4">
					<h4>Delivered Orders</h4>
					<div class="row">
						<div class="col-md-8"></div>
						<div class="col-md-4">
							<select id="delivered_branch_id" name="delivered_branch_id" class="form-control">
								<option value=''></option>
								<?php
									foreach($branches as $b){
										$selected='';
										?>
										<option value='<?php echo $b->id ?>' <?php echo $selected ?>><?php echo $b->name;?> </option>
										<?php
									}
								?>
							</select>
						</div>
					</div>
					<div id="item_delivered" style='height:400px;'></div>

					<h4>Fast And Slow Moving Items</h4>
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<select id="moving_branch_id" name="moving_branch_id" class="form-control">
									<option value=''></option>
									<?php
										foreach($branches as $b){
											$selected='';
											?>
											<option value='<?php echo $b->id ?>' <?php echo $selected ?>><?php echo $b->name;?> </option>
											<?php
										}
									?>

								</select>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<input type="text" placeholder="Select Month" class='form-control' id='moving_month'>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<select id="moving_sort" name="moving_sort" class="form-control">
									<option value='0'>Fast Moving</option>
									<option value='1'>Slow Moving</option>
								</select>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<select id="moving_type" name="moving_type" class="form-control">
									<option value='0'>By Item</option>
									<option value='1'>By Category</option>
								</select>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<select id="moving_limit" name="moving_limit" class="form-control">
									<option value='10'>By 10</option>
									<option value='50'>By 50</option>
									<option value='100'>By 100</option>
									<option value='500'>By 500</option>
									<option value='1000'>By 1000</option>
								</select>
							</div>
						</div>
					</div>
					<div id="fast_moving"  style='height:400px;'></div>
					<br>
					<div id="fast_moving_table"></div>

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
	<div class="modal fade" id="myModalRemarks" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id=''>Edit Remarks</h4>
				</div>
				<div class="modal-body" id=''>
					<input type="hidden" id='edit_id' value='0'>

					<strong>Remarks: </strong>
					<input type="text" value='' id='edit_remarks' class='form-control' placeholder='Enter Remarks'> <br>
					<input type="text" value='' id='edit_received_date' class='form-control' placeholder='Enter Date Received'> <br>
					<select id="sales_type" name="sales_type" class="form-control">
						<option value="0">Choose sales type</option>
						<?php
							$salestype = new Sales_type();
							$salestypes = $salestype->get_active('salestypes',array('company_id','=',$user->data()->company_id));
							foreach ($salestypes as $st):
								?>
								<option value='<?php echo $st->id ?>'><?php echo $st->name ?> </option>
								<?php
							endforeach;
						?>
					</select> <br>
					<div class="text-right">
						<button id='btnUpdateRemarks' class='btn btn-primary'>Update</button>
					</div>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<script>
		$(function(){
			var mem_select2 = $('#member_id');
			mem_select2.select2({
				placeholder: 'Search client',
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
			$('body').on('click','.showUpdateRemarks',function(){
				var con = $(this);
				$('#edit_id').val(con.attr('data-id'));
				$('#edit_remarks').val(con.attr('data-remarks'));
				$('#edit_received_date').val(con.attr('data-received_date'));
				$('#sales_type').val(con.attr('data-sales_type'));
				$('#myModalRemarks').modal('show');
			});
			$('body').on('click','#btnUpdateRemarks',function(){
				var id = $('#edit_id').val();
				var remarks = $('#edit_remarks').val();
				var received_date = $('#edit_received_date').val();
				var sales_type = $('#sales_type').val();
				if(id){
					$('#myModalRemarks').modal('hide');
					$.ajax({
					    url:'../ajax/ajax_wh_order.php',
					    type:'POST',
					    data: {functionName:'updateRemarks',sales_type:sales_type, id:id,received_date:received_date, remarks:remarks},
					    success: function(data){
					        tempToast('info',data,'Info');
						    getPage(0);
					    },
					    error:function(){

					    }
					});
				}
			});
			$('#branch_id').select2({
				allowClear: true,
				placeholder:'Select Branch'
			});
			$('#backload_branch_id').select2({
				allowClear: true,
				placeholder:'Select Branch'
			});
			$('#delivered_branch_id').select2({
				allowClear: true,
				placeholder:'Select Branch'
			});
			$('#user_id').select2({
				allowClear: true,
				placeholder:'Select User'
			});

			$('#status').select2({
				allowClear: true,
				placeholder:'Select Status'
			});

			$('#txtFrom').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#txtFrom').datepicker('hide');
				getPage(0);
			});
			$('#edit_received_date').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#edit_received_date').datepicker('hide');
			});

			$('#txtTo').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#txtTo').datepicker('hide');
				getPage(0);
			});
			var timer;

			$("#pending_member_search").keyup(function() {
				var searchtxt = $("#pending_member_search");
				clearTimeout(timer);
				timer = setTimeout(function() {
					if(searchtxt.val()){
						searchtxt.val(searchtxt.val().trim());
					}
					getPendingMember(0);
				}, 1000);

			});

			initCon1();
			navCon(true,false,false);
			function initCon1(){
				getPast10(0);
				topAgent(0);
				topMember(0);
				getPendingOrders(0);
			}
			$('body').on('click','#navCon1',function(){
				navCon(true,false,false,false,false);
			});
			$('body').on('click','#navCon2',function(){
				navCon(false,true,false,false,false);
				getPage(0);
			});
			$('body').on('click','#navCon3',function(){
				navCon(false,false,true,false,false);
				getBackloads(0);
			});
			$('body').on('click','#navCon4',function(){
				navCon(false,false,false,true,false);
				getPendingMember(0);
			});
			$('body').on('click','#navCon5',function(){
				navCon(false,false,false,false,true);
				getItemStats();
				getMovingItems();
			});
			$('body').on('change','#delivered_branch_id',function(){
				var branch_id = $(this).val();
				getItemStats(branch_id);
			});
			$('body').on('change','#moving_branch_id,#moving_sort,#moving_type,#moving_limit',function(){
				getMovingItems();
			});
			$('#moving_month').datepicker({
				format: "mm-yyyy",
				viewMode: "months",
				minViewMode: "months",
				autoclose:true
			}).on('changeDate', function(ev){
				$('#moving_month').datepicker('hide');
				setTimeout(function(){
					getMovingItems();
				},500);

			});
			function getMovingItems(){
				var branch_id = $('#moving_branch_id').val();
				var month = $('#moving_month').val();
				var sort = $('#moving_sort').val();
				var type = $('#moving_type').val();
				var limit = $('#moving_limit').val();
				$('#fast_moving').html('Loading...');
				$.ajax({
					url:'../ajax/ajax_wh_order.php',
					type:'post',
					dataType:'json',
					data: {functionName:'topItemBranch',limit:limit,branch_id:branch_id,month:month,sort:sort,by_categ:type},
					success: function(data){

						if (data.error){
							$('#fast_moving').html('No data found.');
						} else {
							$('#fast_moving').html('');
							Morris.Bar({
								element: 'fast_moving',
								data: data,
								xkey: 'y',
								ykeys: ['a'],
								labels: ['Count'],
								padding: 40,
								hoverCallback: function(index, options, content) {
									var data = options.data[index];
									return("<p> "+data.y + data.description+ "<br>"+data.a +"</p>");
								}
							});

							var ret ="<table class='table table-bordered'>";
							ret +="<thead><tr><th>Name</th><th>Qty</th></tr></thead>";
							ret +="<tbody>";

							for(var i in data){
								ret += "<tr><td style='border-top:1px solid #ccc;'>" + data[i].y +  data[i].description + "</td><td  style='border-top:1px solid #ccc;'>"+data[i].a+"</td></tr>";
							}

							ret +="</tbody>";
							ret +="</table>";
							$('#fast_moving_table').html(ret);
						}
					},
					error:function(){

					}
				});
			}
			function getItemStats(branch_id){
				$('#item_delivered').html('Loading...');
				$.ajax({
					url:'../ajax/ajax_wh_order.php',
					type:'post',
					dataType:'json',
					data: {functionName:'monthlyDelivered',branch_id:branch_id},
					success: function(data){

						if (data.error){
							$('#item_delivered').html('No data found.');
						} else {
							$('#item_delivered').html('');
							Morris.Line({
								element: 'item_delivered',
								data: data,
								xkey: 'y',
								ykeys: ['a'],
								labels: ['Order'],
								padding: 40,
								parseTime: false
							});
						}
					},
					error:function(){

					}
				});
			}
			function getPendingMember(p){
				$('#holder3').html('Loading...');
				var search = $('#pending_member_search').val();
				$.ajax({
					url:'../ajax/ajax_wh_order.php',
					type:'POST',
					data: {functionName:'getPendingMemberOrder',page:p,search:search},
					success: function(data){
						$('#holder3').html(data);
					},
					error:function(){

					}
				});
			}
			function getBackloads(p){
				$('#holder2').html('Loading...');
				var search = $('#backload_search').val();
				var branch_id = $('#backload_branch_id').val();
				$.ajax({
				    url:'../ajax/ajax_wh_order.php',
				    type:'POST',
				    data: {functionName:'getBackloadList',page:p,search:search,branch_id:branch_id},
				    success: function(data){
					    $('#holder2').html(data);
				    },
				    error:function(){
				        
				    }
				})
			}
			$('body').on('click','#btnSubmitBackload',function(){
				getBackloads(0);
			});
			$('body').on('click','#btnDownloadExcel',function(){
				var search = $('#backload_search').val();
				var branch_id = $('#backload_branch_id').val();
				
				window.open(
					'excel_downloader.php?downloadName=backload&search='+search+'&branch_id='+branch_id,
					'_blank' //
				);
			});

			var current_container = 1;
			function navCon(c1,c2,c3,c4,c5){
				$('#con1').hide();
				$('#con2').hide();
				$('#con3').hide();
				$('#con4').hide();
				$('#con5').hide();
				if(c1){
					$('#con1').fadeIn(300);
					current_container = 1;
				} else if (c2){
					$('#con2').fadeIn(300);
					current_container = 2;
				} else if (c3){
					$('#con3').fadeIn(300);
					current_container = 3;
				} else if (c4){
					$('#con4').fadeIn(300);
					current_container = 4;
				} else if (c5){
					$('#con5').fadeIn(300);
					current_container = 5;
				}
			}
			function getPendingOrders(branch_id){
				$.ajax({
					url:'../ajax/ajax_query2.php',
					type:'post',
					dataType:'json',
					data: {branch_id:branch_id,functionName:'whPendingOrders'},
					success: function(data){
						$('#wh_pendingOrders').html('');
						if (data.error){
							$('#wh_pendingOrders').html('No data found.');
						} else {
							Morris.Bar({
								element: 'wh_pendingOrders',
								data: data,
								xkey: 'y',
								ykeys: ['a'],
								labels: ['Order'],
								xLabelAngle: 35,
								padding: 40
							});
						}
					},
					error:function(){

					}
				});
			}
			function getPast10(branch_id){
				$.ajax({
					url:'../ajax/ajax_query2.php',
					type:'post',
					dataType:'json',
					data: {branch_id:branch_id,functionName:'whOrderLastTenDays'},
					success: function(data){
						$('#wh_lastTen').html('');
						if (data.error){
							$('#wh_lastTen').html('No data found.');
						} else {
							Morris.Line({
								element: 'wh_lastTen',
								data: data,
								xkey: 'y',
								ykeys: ['a'],
								labels: ['Sales'],
								xLabelAngle: 35,
								padding: 40,
								parseTime: false,
								hoverCallback: function(index, options, content) {
									var data = options.data[index];
									return("<p> Order on "+data.y + "<br><span class='text-danger'>Count: " + number_format(data.a) +"</span></p>");
								}
							});
						}
					},
					error:function(){

					}
				});
			}
			function topAgent(branch_id){
				$.ajax({
					url:'../ajax/ajax_query2.php',
					type:'post',
					dataType:'json',
					beforeSend: function(){
						$('#wh_topAgent').html('<h3 class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</h3>');
					},
					data: {functionName:'topAgentOrder',branch_id:branch_id},
					success: function(data){
						$('#wh_topAgent').html('');
						if (data.error){
							$('#wh_topAgent').html('No data found.');
						} else {
							var a =0;
							Morris.Donut({
								element: 'wh_topAgent',
								data: data,
								formatter: function (value, data) {
									return "\n" + number_format(value);
								}
							});
						}

					},
					error:function(){

					}
				});
			}
			function topMember(branch_id){
				$.ajax({
					url:'../ajax/ajax_query2.php',
					type:'post',
					dataType:'json',
					beforeSend: function(){
						$('#wh_topMember').html('<h3 class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</h3>');
					},
					data: {functionName:'topMemberOrder',branch_id:branch_id},
					success: function(data){
						$('#wh_topMember').html('');
						if (data.error){
							$('#wh_topMember').html('No data found.');
						} else {
							var a =0;
							Morris.Donut({
								element: 'wh_topMember',
								data: data,
								formatter: function (value, data) {
									return "\n" + number_format(value);
								}
							});
						}

					},
					error:function(){

					}
				});
			}
			$('body').on('change','#branch_id,#member_id,#status,#user_id',function(){
				getPage(0);
			});




			var timer;
			$("#txtSearch").keyup(function(){

				var searchtxt = $("#txtSearch");

				clearTimeout(timer);
				timer = setTimeout(function() {

					if(searchtxt.val()){
						searchtxt.val(searchtxt.val().trim());
					}

					getPage(0);

				}, 1000);

			});

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
			function getPage(p){
				var search = $('#txtSearch').val();
				var branch_id = $('#branch_id').val();
				var member_id = $('#member_id').val();
				var status = $('#status').val();
				var user_id = $('#user_id').val();
				var txtFrom = $('#txtFrom').val();
				var txtTo = $('#txtTo').val();
				var extra_filter = $('#extra_filter').val();
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend: function(){
						$('#holder').html('Loading...');
					},
					data:{page:p,functionName:'whOrdersPaginate',user_id:user_id,txtFrom:txtFrom,txtTo:txtTo,status:status,member_id:member_id,cid: <?php echo $user->data()->company_id; ?>,search:search,branch_id:branch_id},
					success: function(data){
						$('#holder').html(data);
					},
					error:function(){
						alert('Something went wrong. The page will be refresh.');
						location.href='wh_reports.php';

					}
				});
			}
			$('body').on('click','.paging',function(e){
				e.preventDefault();
				var page = $(this).attr('page');

				if(	current_container == 2){
					$('#hiddenpage').val(page);
					getPage(page);
				} else if (	current_container == 3){
					$('#hiddenpage2').val(page);
					getBackloads(page);
				}else if (	current_container == 4){
					$('#hiddenpage3').val(page);
					getPendingMember(page);
				}else if (	current_container == 5){
					//$('#hiddenpage4').val(page);
					getItemStats();
				}

			});
			$('body').on('click','#btnDetails',function(){
				var id = $(this).attr('data-id');
				var ret_html = '';

				$('#myModal').modal('show');
				$.ajax({
					url:'../ajax/ajax_query2.php',
					type:'POST',
					beforeSend:function(){
						$('#mbody').html('Loading...');
					},
					dataType:'json',
					data: {functionName:'getWhOrdersDetails',order_id:id},
					success: function(data){

							var order = JSON.parse(data.order);
							ret_html += "<h5>Order ID: "+id+"</h5>";
							ret_html += "<table class='table'>";
							ret_html += "<thead>";
							ret_html += "<tr>";
							ret_html += "<th>Item</th><th>Price</th><th>Qty</th><th>Total</th><th></th>";
							ret_html += "</tr>";
							ret_html += "</thead>";
							ret_html += "<tbody>";
							for(var i in order){
								ret_html += "<tr>";
								ret_html += "<td>"+order[i].item_code+" <span class='span-block text-danger'>"+order[i].description+"</span></td>";
								ret_html += "<td>"+order[i].adjusted_price+"</td>";
								ret_html += "<td>"+order[i].qty+"</td>";
								ret_html += "<td>"+order[i].total+"</td>";
								ret_html += "<td></td>";
								ret_html += "</tr>";
							}
							ret_html += "</tbody>";
							ret_html += "</html>";
						$('#mbody').html(ret_html);


					},
					error:function(){

					}
				});
			});
		})
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?><?php
/**
 * Created by PhpStorm.
 * User: temp
 * Date: 4/26/2016
 * Time: 1:35 PM
 */