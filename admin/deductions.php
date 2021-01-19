<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';

	$del = new Deduction_list();

	$deductions = $del->get_active('deduction_list',array('company_id' ,'=',$user->data()->company_id));

?>


	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">

		<div class="content-header">
			<h3>Deductions</h3>
		</div>


		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="col-md-6">

					</div>
					<div class="col-md-6 text-right">
						<a class='btn btn-default' href='deduction-summary.php' id='btnReports' >Reports</a>
					</div>
				</div>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Deductions</div>
					<div class="panel-body">
						<div class="row">

							<div class="col-md-3">
								<div class="input-group">
									<span class="input-group-addon"><span class='glyphicon glyphicon-search'></span></span>
									<input type="text" id="search" class='form-control' placeholder='Search..'/>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" autocomplete="off" class='form-control' placeholder='Date From' id='dt_from'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" autocomplete="off" class='form-control' placeholder='Date To' id='dt_to'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select class='form-control'  name="deduction_name" id="deduction_name">
										<option value="">Choose type</option>
										<?php
											if($deductions){
												foreach($deductions as $dec){
													echo "<option value='$dec->name'>$dec->name</option>";
												}
											}
										?>
									</select>
								</div>
							</div>
							<?php if(Configuration::thisCompany('cebuhiq')){
								?>
								<div class="col-md-3">
									<div class="form-group">
										<select class='form-control'  name="status" id="status">
											<option value="">All</option>
											<option value="1">For Approval</option>

										</select>
									</div>
								</div>
								<?php
							}?>

						</div>
						<input type="hidden" id="hiddenpage" />
						<div id="aaa"></div>
					</div>
				</div>
			</div>
		</div>


	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function() {

			getPage(0);

			$('body').on('click','.deduction_id',function(){

			});

			$('body').on('click','.paging',function(){

				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);

			});

			$('body').on('change','#deduction_name,#status',function(){
				getPage(0);
			});

			var timer;

			$("#search").keyup(function(){

				var searchtxt = $("#search");

				clearTimeout(timer);
				timer = setTimeout(function() {
					if(searchtxt.val()){
						searchtxt.val(searchtxt.val().trim());
					}
					getPage(0);
				}, 1000);
			});

			$('#dt_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_from').datepicker('hide');
				if($('#dt_from').val() && $('#dt_to').val() ){
					getPage(0);
				}
			});

			$('#dt_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_to').datepicker('hide');
				if($('#dt_from').val() && $('#dt_to').val() ){
					getPage(0);
				}

			});

			$('body').on('click','.btnApproved',function(){
				var con = $(this);
				var id = con.attr('data-id');
				$.ajax({
				    url:'../ajax/ajax_member_service.php',
				    type:'POST',
				    data: {functionName:'approvedDeduction', id:id},
				    success: function(data){
				        tempToast('info',data,'Info');
					    getPage(0);
				    },
				    error:function(){
				        
				    }
				});


			});

			function getPage(p){

				var search = $('#search').val();
				var con = $('#aaa');
				var dt_from = $('#dt_from').val();
				var dt_to = $('#dt_to').val();
				var deduction_name = $('#deduction_name').val();
				var status = $('#status').val();

				con.html('Loading...');

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'POST',
					data:{page:p,functionName:'deductions',status:status,deduction_name:deduction_name,dt_from:dt_from,dt_to:dt_to,cid: <?php echo $user->data()->company_id; ?>,search:search},
					success: function(d){
						con.html(d);
					}
				});
			}

		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>