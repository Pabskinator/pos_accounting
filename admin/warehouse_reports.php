<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('acc_m')) {
		// redirect to denied page
		Redirect::to(1);
	}


?>


	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<?php include 'includes/warehouse_nav.php' ?>

			<!-----------------    WH REPORTS --------------------->
			<div id="con_wh" style='display:none;'>
				<h3>Warehouse Reports</h3>

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading"><i class='fa fa-pie-chart'></i></div>
					<div class="panel-body"  id='container1'>
						<div class="row">
							<div class="col-md-6">
								<div id="myfirstchart" class='col-md-12' style='height:400px;'></div>
							</div>
							<div class="col-md-6">
								<br/><br/>
								<div id="myfirstchartlabel"></div>
							</div>
						</div>
					</div>
				</div>

			</div>
			<!----------------- END WH REPORTS --------------------->
			<!-----------------    ISSUES REPORTS --------------------->
			<div id="con_issues" style='display:none;'>
				<h3>Issues Reports</h3>

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading"><i class='fa fa-pie-chart'></i></div>
					<div class="panel-body"  id='container2'>
						<div class="row">
							<div class="col-md-4">
								<select name="status" id="status" class='form-control' multiple>
									<option value=""></option>
									<option value='1'><?php echo DAMAGE_LABEL; ?></option>
									<option value='2'><?php echo MISSING_LABEL; ?></option>
									<option value='4'><?php echo INCOMPLETE_LABEL; ?></option>
									<option value='3'>Disposed</option>
								</select>
							</div>
							<div class="col-md-4">
								<button id='btnSubmitIssue' class='btn btn-default'><i class='fa fa-ok'></i> Submit</button>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div id="issues_donut" class='col-md-12' style='height:400px;padding-top:10px;'></div>
							</div>
							<div class="col-md-6">
								<br/><br/>
								<div id="issues_label"></div>
							</div>
						</div>
					</div>
				</div>

			</div>
			<!----------------- END ISSUES REPORTS --------------------->

		</div>
	</div> <!-- end page content wrapper-->

	<script>
		$(function() {
			$('#status').select2({
				placeholder: "Search type",
				allowClear: true
			});
			showContainer(true, false, false);
			$('body').on('click', '.btn_nav', function(e) {
				e.preventDefault();
				var con = $(this).attr('data-con');
				if(con == 1) {
					showContainer(true, false, false);
				} else if(con == 2) {
					showContainer(false, true, false);
				} else if(con == 3) {
					showContainer(false, false, true);
				}
				$('#secondNavigationContainer').hide();
			});
			function showContainer(c1, c2, c3) {
				var con_wh = $('#con_wh');
				var con_issues = $('#con_issues');
				con_wh.hide();
				con_issues.hide();
				if(c1) {
					con_wh.fadeIn(300);
					getWHAmount();

				} else if(c2) {
					con_issues.fadeIn(300);
					getIssuesAmount();

				} else if(c3) {

				}
			}
			function getIssuesAmount(){
				var s = $('#status').val();

				$.ajax({
					url:'../ajax/ajax_warehouse.php',
					type:'POST',
					dataType:'json',
					data: {functionName:'getIssuesAmount',s:s},
					success: function(data){
						$('#issues_donut').html('');
						if (data.error){
							$('#issues_donut').html("<div class='alert alert-info'>No data found</div>");
							$('#issues_label').html('');
						} else {
							var a =0;
							Morris.Donut({
								element: 'issues_donut',
								data: data.donut,
								formatter: function (value, data) {
									return "\n" + number_format(value,2);
								}
							});
							$('#issues_label').html(data.label);

						}
					},
					error:function(){

					}
				});
			}
			function getWHAmount(){
				$.ajax({
				    url:'../ajax/ajax_warehouse.php',
				    type:'POST',
					dataType:'json',
				    data: {functionName:'getWHAmount'},
				    success: function(data){
					    $('#myfirstchart').html('');
					    if (data.error){
						    $('#myfirstchart').html("<div class='alert alert-info'>No data found</div>");
						    $('#myfirstchartlabel').html('');
					    } else {
						    var a =0;
						    Morris.Donut({
							    element: 'myfirstchart',
							    data: data.donut,
							    formatter: function (value, data) {
								    return "\n" + number_format(value,2);
							    }
						    });
						    $('#myfirstchartlabel').html(data.label);
					    }
				    },
				    error:function(){

				    }
				});
			}
			$('body').on('click','#btnSubmitIssue',function(){
				getIssuesAmount();
			});

		});
	</script><?php require_once '../includes/admin/page_tail2.php'; ?>