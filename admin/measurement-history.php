<?php
	// $user have all the properties and method of the current user
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('body_measure')) {
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
					Body Measurement
				</h1>

			</div>
			<div class="panel-panel-default">
				<div class="panel-body">
					<div class="row">
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">

									<input type="text" name='member_id' id='member_id' class='form-control'>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<button class='btn btn-submit' id='btnSubmit'>Search</button>
								</div>
							</div>
						</div>
					</div>
					<input type="hidden" id="hiddenpage" />
					<div id="holder"></div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="row">
						<div class="col-md-6">
							<h4 class='text-center'>Weight Movement</h4>
							<div id="bar-graph" style='height: 400px;width:100%;'></div>
						</div>
						<div class="col-md-6">
							<h4 class='text-center'>Arm Size</h4>
							<div id="area-graph" style='height: 400px;width:100%;'></div>
						</div>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="row">
						<div class="col-md-6">
							<h4 class='text-center'>Calf</h4>
							<div id="area2-graph" style='height: 400px;width:100%;'></div>
						</div>
						<div class="col-md-6">
							<h4 class='text-center'>Thigh </h4>
							<div id="line-graph" style='height: 400px;width:100%;'></div>
						</div>

					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="row">
						<div class="col-md-6">
							<h4 class='text-center'>Service consumed</h4>
							<div id="donut-graph" style='height: 400px;width:100%;'></div>
						</div>
						<div class="col-md-6">

						</div>

					</div>
				</div>
			</div>
		</div>
		<!-- end page content wrapper-->
		<script>

			$(document).ready(function() {
				setTimeout(function(){
					Morris.Line({
						element: 'bar-graph',
						data: [
							{ y: 'October 27, 2016', a: 150 },
							{ y: 'November 11, 2016', a: 145 },
							{ y: 'November 30, 2016', a: 140  },
							{ y: 'December 10, 2016', a: 138  },
							{ y: 'December 30 2017', a: 160   },
							{ y: 'January 7, 2017', a: 155   },
							{ y: 'January 14, 2017', a: 150 },
						],

						xkey: 'y',
						ykeys: ['a'],
						labels: ['Sales'],
						xLabelAngle: 25,
						padding: 40,
						parseTime: false,
						hoverCallback: function(index, options, content) {
							var data = options.data[index];
							return("<p> "+data.y + "<br><span class='text-danger'>" + data.a +" lbs</span></p>");
						}
					});
					Morris.Line({
						element: 'line-graph',
						data: [
							{ y: 'October 2016', a: 100, b: 90 },
							{ y: 'November 2016', a: 75,  b: 65 },
							{ y: 'December 2016', a: 50,  b: 40 },
							{ y: 'January 2017', a: 75,  b: 65 },
							{ y: 'February 2017', a: 50,  b: 40 },
						],
						xkey: 'y',
						ykeys: ['a', 'b'],
						labels: ['Left Calf', 'Right Calf'],
						parseTime: false,
					});
					Morris.Area({
						element: 'area-graph',
						data: [
							{ y: 'October 2016', a: 100, b: 90 },
							{ y: 'November 2016', a: 75,  b: 65 },
							{ y: 'December 2016', a: 50,  b: 40 },
							{ y: 'January 2017', a: 75,  b: 65 },
							{ y: 'February 2017', a: 50,  b: 40 },
						],
						xkey: 'y',
						ykeys: ['a', 'b'],
						labels: ['Left Arm', 'Right Arm'],
						parseTime: false,
					});
					Morris.Area({
						element: 'area2-graph',
						data: [
							{ y: 'October 2016', a: 100, b: 90 },
							{ y: 'November 2016', a: 75,  b: 65 },
							{ y: 'December 2016', a: 50,  b: 40 },
							{ y: 'January 2017', a: 75,  b: 65 },
							{ y: 'February 2017', a: 50,  b: 40 },
						],
						xkey: 'y',
						ykeys: ['a', 'b'],
						labels: ['Left Thigh', 'Right Thigh'],
						parseTime: false,
					});
					Morris.Donut({
						element: 'donut-graph',
						data: [
							{label: "Boxing", value: 12},
							{label: "Muay Thai", value: 30},
							{label: "MMA", value: 20}
						]
					});

				},1000);

				$('body').on('click','#btnSubmit',function(e){
					getPage(0);
				});

				$("#member_id").select2({
					placeholder: 'Search member' ,
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
				getPage(0);
				$('body').on('click','.paging',function(e){
					e.preventDefault();
					var page = $(this).attr('page');
					$('#hiddenpage').val(page);
					getPage(page);
				});

				function getPage(p){
					var member_id  = $('#member_id').val();


					$.ajax({
						url: '../ajax/ajax_paging.php',
						type:'post',
						beforeSend:function(){
							$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
						},
						data:{page:p,functionName:'measurementPaginate',cid: <?php echo $user->data()->company_id; ?>,member_id:member_id},
						success: function(data){

							$('#holder').html(data);

						}
					});
				}
			});


		</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>