<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('agent_sales')) {
		// redirect to denied page
		Redirect::to(1);
	}


	// my sales type

	$salestype = new Sales_type();
	$my = $salestype->getMySalesType($user->data()->id);

?>

	<!-- Page content -->
	<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>Sales Agent</h1>

		</div>

		<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
			<a data-con="1" class='btn btn-default btnNav'>
				<span class='glyphicon glyphicon-plus'></span>
				<span class='hidden-xs'>Sales</span>
			</a>
			<a data-con="2" class='btn btn-default btnNav'>
				<span class='glyphicon glyphicon-plus'></span>
				<span class='hidden-xs'>My Clients</span>
			</a>
		</div>

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading"></div>
					<div class="panel-body">
							<div id="con1">
								<div class="row">
									<div class="col-md-8">
										<p>
											My Sales Type :
											<?php
												foreach($my as $st){
													?>
													<span class='label label-primary'><?php echo $st->name; ?></span>
													<?php
												}
											?>
										</p>
									</div>
									<div class="col-md-4">
										<button class='btn btn-default' id='btnDL'>Download</button>
									</div>
								</div>
																<br>
								<div class="row">
									<div class="col-md-3">
										<select name="sales_type" id="sales_type" class='form-control'>
											<?php

												foreach($my as $st){
													?>
													<option value='<?php echo $st->id; ?>' ><?php echo $st->name; ?></option>
													<?php
												}
											?>
										</select>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<input type="text" class='form-control' id='date_from' placeholder="Date From">
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<input type="text" class='form-control' id='date_to' placeholder="Date To">
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<button class='btn btn-default' id='btnSubmit'>Submit</button>
										</div>
									</div>
								</div>
								<div id="content-1"></div>
							</div>
							<div id="con2" style='display:none;'>
								<div class="row">
									<input type="hidden" id='agent_id' value='<?php echo $user->data()->id; ?>'>

									<div class="col-md-3">
										<div class="form-group">
											<input type="text" class='form-control' id='search' placeholder='Search Record'>
										</div>
									</div>
								</div>
								<div id="content-2"></div>
							</div>
					</div>
				</div>
			</div>

	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function() {

			$('#date_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#date_from').datepicker('hide');
			});
			getSales();
			$('#date_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#date_to').datepicker('hide');
			});

			$('body').on('click','#btnSubmit',function(){
				getSales();
			}); 
			$('body').on('keyup','#search',function(){
				getMyMembers();
			});
			$('body').on('click','.btnNav',function(){
				var con = $(this);
				var t = con.attr('data-con');
				var con1 = $('#con1');
				var con2 = $('#con2');
				con1.hide();
				con2.hide();
				if(t == 1){
					con1.fadeIn(300);
					getSales();
				} else if (t == 2){
					con2.fadeIn(300);
					getMyMembers();
				}
			});

			function getMyMembers(){


					var agent_id = $('#agent_id').val();
					var search = $('#search').val();

					$('#content-2').html('<br><p>Loading...</p>');
					$.ajax({
						url: '../ajax/ajax_paging.php',
						type:'post',
						data:{page:0,functionName:'memberList',agent_id:agent_id,cid: <?php echo $user->data()->company_id; ?>,search:search},
						success: function(data){
							$('#content-2').html(data);

						},
						error: function(){


						}
					});

			}

			function getSales(){
				var date_from = $('#date_from').val();
				var date_to = $('#date_to').val();

				var sales_type = $('#sales_type').val();


				$.ajax({
				    url:'../ajax/ajax_paging.php',
				    type:'POST',
				    data: {functionName:'r3Pagination',cid:localStorage['company_id'],report_type:1,sales_type:sales_type,dateStart:date_from,dateEnd:date_to},
				    success: function(data){
				        $('#content-1').html(data);
				    },
				    error:function(){

				    }
				});

			}
			$('body').on('click','#btnDL',function(){

				var date_from = $('#date_from').val();
				var date_to = $('#date_to').val();
				var sales_type = $('#sales_type').val();
				window.open(
					'excel_downloader.php?downloadName=r3Pagination&dateStart='+date_from+'&dateEnd='+date_to+'&sales_type='+sales_type+'&report_type=1',
					'_blank' //
				);
			});
		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>