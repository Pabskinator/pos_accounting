<?php
	// $user have all the properties and method of the current user
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('p_point_manage')){
		// redirect to denied page
		Redirect::to(1);
	}
	$point = new Point();
	$pg_cls = new Point_group();
	$points = $point->get_active('points',['company_id','=',$user->data()->company_id]);
	$point_groups = $pg_cls->get_active('point_groups',['company_id','=',$user->data()->company_id]);

?>

	<style>
		.timeline {
			list-style: none;
			padding: 20px 0 20px;
			position: relative;
		}

		.timeline:before {
			top: 0;
			bottom: 0;
			position: absolute;
			content: " ";
			width: 3px;
			background-color: #eeeeee;
			left: 50%;
			margin-left: -1.5px;
		}

		.timeline > li {
			margin-bottom: 20px;
			position: relative;
		}

		.timeline > li:before,
		.timeline > li:after {
			content: " ";
			display: table;
		}

		.timeline > li:after {
			clear: both;
		}

		.timeline > li:before,
		.timeline > li:after {
			content: " ";
			display: table;
		}

		.timeline > li:after {
			clear: both;
		}

		.timeline > li > .timeline-panel {
			width: 46%;
			float: left;
			border: 1px solid #d4d4d4;
			border-radius: 2px;
			padding: 20px;
			position: relative;
			-webkit-box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175);
			box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175);
		}

		.timeline > li > .timeline-panel:before {
			position: absolute;
			top: 26px;
			right: -15px;
			display: inline-block;
			border-top: 15px solid transparent;
			border-left: 15px solid #ccc;
			border-right: 0 solid #ccc;
			border-bottom: 15px solid transparent;
			content: " ";
		}

		.timeline > li > .timeline-panel:after {
			position: absolute;
			top: 27px;
			right: -14px;
			display: inline-block;
			border-top: 14px solid transparent;
			border-left: 14px solid #fff;
			border-right: 0 solid #fff;
			border-bottom: 14px solid transparent;
			content: " ";
		}

		.timeline > li > .timeline-badge {
			color: #fff;
			width: 50px;
			height: 50px;
			line-height: 50px;
			font-size: 1.4em;
			text-align: center;
			position: absolute;
			top: 16px;
			left: 50%;
			margin-left: -25px;
			background-color: #999999;
			z-index: 100;
			border-top-right-radius: 50%;
			border-top-left-radius: 50%;
			border-bottom-right-radius: 50%;
			border-bottom-left-radius: 50%;
		}

		.timeline > li.timeline-inverted > .timeline-panel {
			float: right;
		}

		.timeline > li.timeline-inverted > .timeline-panel:before {
			border-left-width: 0;
			border-right-width: 15px;
			left: -15px;
			right: auto;
		}

		.timeline > li.timeline-inverted > .timeline-panel:after {
			border-left-width: 0;
			border-right-width: 14px;
			left: -14px;
			right: auto;
		}

		.timeline-badge.primary {
			background-color: #2e6da4 !important;
		}

		.timeline-badge.success {
			background-color: #3f903f !important;
		}

		.timeline-badge.warning {
			background-color: #f0ad4e !important;
		}

		.timeline-badge.danger {
			background-color: #d9534f !important;
		}
		.timeline-badge.info {
			background-color: #5bc0de !important;
		}
		.timeline-title {
			margin-top: 0;
			color: inherit;
		}
		.timeline-body > p,
		.timeline-body > ul {
			margin-bottom: 0;
		}

		.timeline-body > p + p {
			margin-top: 5px;
		}
	</style>

	<!-- Page content -->
	<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Points/Units
			</h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')){
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('flash')."</div>";
			}
		?>
		<?php include 'includes/point_nav.php' ?>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Points/Units</div>
					<div class="panel-body">
						<div id="feedback_holder"></div>
						<div id="con1" style='display: none;'>
							<div id="con"></div>
						</div>

						<div id="con2"  style='display: none;'>
							<div class="row">
								<div class="col-md-8"></div>
								<div class="col-md-4">
									<div class="form-group">
										<div class="input-group">
											<span class="input-group-addon" id="basic-addon1"><i class='fa fa-search'></i></span>
											<input type="text" class="form-control" id='searchPointsLog' placeholder='Search point'>
										</div>
									</div>
								</div>
							</div>
							<div id="holder2"></div>
							<input type="hidden" id='hiddenpage2'/>
						</div>
						<div id="con3"  style='display: none;'>
							<div class="row">
								<div class="col-md-8"></div>
								<div class="col-md-4">
									<div class="form-group">
										<div class="input-group">
											<span class="input-group-addon" id="basic-addon1"><i class='fa fa-search'></i></span>
											<input type="text" class="form-control" id='searchUserPoints' placeholder='Search user'>
										</div>
									</div>
								</div>
							</div>
							<div id="holder3"></div>
							<input type="hidden" id='hiddenpage3'/>
						</div>
						<div id="con4"  style='display: none;'>
							<div class="row">
								<div class="col-md-8"></div>
								<div class="col-md-4">
									<div class="form-group">
										<div class="input-group">
											<span class="input-group-addon" id="basic-addon1"><i class='fa fa-search'></i></span>
											<input type="text" class="form-control" id='searchUserLog' placeholder='Search user'>
										</div>
									</div>
								</div>
							</div>
							<div id="holder4"></div>
							<input type="hidden" id='hiddenpage4'/>
						</div>
					</div>
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
						 <input type="hidden" id='hid_point_id' class='form-control'>
						<strong>Amount:</strong> <input type="text" id='txtAmount' class='form-control'>
						<strong>Point:</strong> <input type="text" id='txtPoint' class='form-control'>
						<hr>
						<div class='text-right'>
							<button id='btnSavePoints' class='btn btn-default'><i class='fa fa-save'></i> SAVE</button>
						</div>
					</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<div class="modal fade" id="myModalAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id='atitle'></h4>
				</div>
				<div class="modal-body" id='abody'>
					<strong>Name:</strong> <input type="text" id='add_name' class='form-control'>
					<strong>Amount:</strong> <input type="text" id='add_amount' class='form-control'>
					<strong>Point:</strong> <input type="text" id='add_point' class='form-control'>
					<strong>Unit:</strong> <input type="text" id='add_unit' class='form-control'>
					<hr>
					<div class='text-right'>
						<button id='submitPoint' class='btn btn-default'><i class='fa fa-save'></i> SAVE</button>
					</div>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<div class="modal fade" id="myModalRegister" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id='atitle'>Register</h4>
				</div>
				<div class="modal-body" id='abody'>

					<strong>Name:</strong> <input type="text" id='reg_name' class='form-control'>
					<strong>Membership:</strong>
					<select class='form-control' name="reg_point_name" id="reg_point_name">
						<?php if($point_groups){
							foreach($point_groups as $pg){
								echo "<option value='$pg->id'>".ucwords($pg->name)."</option>";
							}
						} ?>
					</select>
					<hr>
					<div class='text-right'>
						<button id='submitRegistration' class='btn btn-default'><i class='fa fa-save'></i> SAVE</button>
					</div>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<div class="modal fade" id="myModalAddMemGroup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" ></h4>
				</div>
				<div class="modal-body">
					<strong>Name:</strong> <input type="text" id='name_mem_group' class='form-control'>
					<strong>Points:</strong> 
					<select name="point_list_mem" id="point_list_mem" class='form-control' multiple>
						<option value=""></option>
						<?php
							if($points){
								foreach($points as $ppp){
									?>
									<option value="<?php echo $ppp->id; ?>"><?php echo ucwords($ppp->name); ?></option>
									<?php
								}
							}
						?>
					</select>
					<strong>Supplementary:</strong> <input type="text" id='sup_count' class='form-control'>
					<strong>Purchase Per Month:</strong> <input type="text" id='purchase_per_month' class='form-control'>
					<strong>Total Binary PV:</strong> <input type="text" id='binary_pv_total' class='form-control'>
					<strong>Total Uni Level PV:</strong> <input type="text" id='uni_level_pv_total' class='form-control'>
					<hr>
					<div class='text-right'>
						<button id='submitMemGroup' class='btn btn-default'><i class='fa fa-save'></i> SAVE</button>
					</div>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<div class="modal fade" id="myModalUpdateGroup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" >Update group</h4>
				</div>
				<div class="modal-body">
					<strong>Name:</strong> <input type="text" id='update_group_name' class='form-control'>
					<input type="hidden" id='update_point_group_id' class='form-control'>
					<strong>Points:</strong>
					<select name="update_point_list_mem" id="update_point_list_mem" class='form-control' multiple>
						<option value=""></option>
						<?php
							if($points){
								foreach($points as $ppp){
									?>
									<option value="<?php echo $ppp->id; ?>"><?php echo ucwords($ppp->name); ?></option>
									<?php
								}
							}
						?>
					</select>
					<strong>Supplementary:</strong> <input type="text" id='update_sup_count' class='form-control'>
					<hr>
					<div class='text-right'>
						<button id='updateMemGroup' class='btn btn-default'><i class='fa fa-save'></i> SAVE</button>
					</div>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<div class="modal fade" id="myModalSupplementary" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id='suptitle'> <i class='fa fa-users'></i> Add Supplementary</h4>
					</div>
					<div class="modal-body" id='supbody'>
						<div id="supplementary_holder"></div>
						<input type="hidden" id='supplementary_member_id' value=''>
						<div class="form-group">
						Name:
						<input type="text" class='form-control' placeholder='Enter Name' id='supplementary_id'>
						</div>
						<div class="form-group">
							<button class='btn btn-default' id='btnSubmitSupplemenatry'>Submit</button>
						</div>

					</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<script>
		$(document).ready(function(){
			var whereami = 1;
			$('body').on('click','.paging',function(e){
				e.preventDefault();

				if(whereami == 1){

				} else if (whereami == 2){
					var page = $(this).attr('page');
					$('#hiddenpage2').val(page);
					getPage2(page);
				}else if (whereami == 3){
					var page = $(this).attr('page');
					$('#hiddenpage3').val(page);
					var mem_id = $('#mem_id').val();
					getPage3(page);
				}else if (whereami == 4){
					var page = $(this).attr('page');
					$('#hiddenpage4').val(page);
					var mem_id = $('#mem_id').val();
					getPage4(page);
				}

			});
			$('#point_list_mem').select2({
				placeholder: 'Points',
				allowClear: true
			});
			$('#update_point_list_mem').select2({
				placeholder: 'Points',
				allowClear: true
			});
			// select2
			$('#reg_name').select2({
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
			showContainer(true, false, false);
			$('body').on('click', '.btn_nav', function(e) {
				e.preventDefault();
				var con = $(this).attr('data-con');
				if(con == 1){
					showContainer(true, false, false, false);
				} else if(con == 2) {
					showContainer(false, true, false, false);
				} else if(con == 3) {
					showContainer(false, false, true, false);
				} else if(con == 4) {
					showContainer(false, false, false,true);
				}
			});
			var timer;
			$("#searchUserPoints").keyup(function(){
				var searchtxt = $("#searchUserPoints");
				clearTimeout(timer);
				timer = setTimeout(function() {
					if(searchtxt.val()){
						searchtxt.val(searchtxt.val().trim());
					}
					getPage3(0);
				}, 1000);
			});
			$("#searchPointsLog").keyup(function(){
				var searchtxt = $("#searchPointsLog");
				clearTimeout(timer);
				timer = setTimeout(function() {
					if(searchtxt.val()){
						searchtxt.val(searchtxt.val().trim());
					}
					getPage2(0);
				}, 1000);
			});
			$("#searchUserLog").keyup(function(){
				var searchtxt = $("#searchUserLog");
				clearTimeout(timer);
				timer = setTimeout(function() {
					if(searchtxt.val()){
						searchtxt.val(searchtxt.val().trim());
					}
					getPage4(0);
				}, 1000);
			});
			function showContainer(c1, c2, c3,c4) {

				var con1   = $('#con1');
				var con2   = $('#con2');
				var con3   = $('#con3');
				var con4   = $('#con4');

				con1.hide();
				con2.hide();
				con3.hide();
				con4.hide();

				if(c1) {
					con1.fadeIn(300);
					whereami = 1;
					getPoints();
				} else if(c2) {
					con2.fadeIn(300);
					whereami = 2;
					getPointsLog();

				} else if(c3) {
					con3.fadeIn(300);
					whereami = 3;
					getUserPoints();
				} else if(c4) {
					con4.fadeIn(300);
					whereami = 4;
					getUserPointsLog();
				}
			}
			function getPoints(){
				$.ajax({
					url:'../ajax/ajax_query.php',
					type:'POST',
					beforeSend:function(){
						$('#con').html('Loading...');
					},
					data: {functionName:'getPoints'},
					success: function(data){
						$('#con').html(data);
					},
					error:function(){
						tempToast('error','<p>Please try again</p>','<h3>ERROR!</h3>');
					}
				});
			}
			function getUserPointsLog(){
				getPage4();
			}
			function getUserPoints(){
				getPage3();
			}
			function getPointsLog(){
				getPage2();
			}
			function getPage4(p){
				var s = $('#searchUserLog').val();
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder4').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,s:s,functionName:'userPointsLog',cid: <?php echo $user->data()->company_id; ?>},
					success: function(data){
						$('#holder4').html(data);
					}
				});
			}
			function getPage3(p){
			 var s= $('#searchUserPoints').val();
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder3').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,s:s,functionName:'userPoints',cid: <?php echo $user->data()->company_id; ?>},
					success: function(data){
						$('#holder3').html(data);
					}
				});
			}
			function getPage2(p){
				var s = $('#searchPointsLog').val();
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder2').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,s:s,functionName:'pointsLog',cid: <?php echo $user->data()->company_id; ?>},
					success: function(data){
						$('#holder2').html(data);
					}
				});
			}
			$('body').on('click','.updatePoints',function(){
				var con = $(this);
				var amount = con.attr('data-amount');
				var point = con.attr('data-point');
				var id = con.attr('data-id');
				$('#hid_point_id').val(id);
				$('#txtAmount').val(amount);
				$('#txtPoint').val(point);
				$('#myModal').modal('show');
				
			});
			$('body').on('click','#btnSavePoints',function(){
				var amount = $('#txtAmount').val();
				var point = $('#txtPoint').val();
				var id = $('#hid_point_id').val();
				var con = $(this);
				button_action.start_loading(con);
				alertify.confirm("Are you sure you want to update the pointing system?",function(e){
					if(e){
						updatePoints(amount,point,con,id);
					} else {
						button_action.end_loading(con);
					}
				})
			});
			function updatePoints(amount,point,con,id){
				$.ajax({
				    url:'../ajax/ajax_query.php',
				    type:'POST',
				    data: {functionName:'updatePoints',amount:amount,point:point,id:id},
				    success: function(data){
					    tempToast('info','<p>'+data+'</p>','<h3>INFO!</h3>');
					    button_action.end_loading(con);
					    $('#myModal').modal('hide');
					    getPoints();
				    },
				    error:function(){
					    tempToast('error','<p>Please try again</p>','<h3>ERROR!</h3>');
					    button_action.end_loading(con);
				    }
				})
			}
			function addNewPoint(p,a,n,con,u){
				$.ajax({
				    url:'../ajax/ajax_query.php',
				    type:'POST',
				    data: {functionName:'addNewPoint',p:p,a:a,n:n,u:u},
				    success: function(data){
					    if(data == 1){
						    tempToast('info','<p>Point added successfully.</p>','<h3>INFO!</h3>');
						    button_action.end_loading(con);
						    $('#myModalAdd').modal('hide');
						    getPoints();
					    } else {
						    tempToast('error','<p>'+data+'</p>','<h3>ERROR!</h3>');
						    button_action.end_loading(con);
					    }

				    },
				    error:function(){
					    tempToast('error','<p>Please  try again</p>','<h3>ERROR!</h3>');
				    }
				});
			}
			function registerUser(member_id,point_group,con){
				$.ajax({
					url:'../ajax/ajax_query.php',
					type:'POST',
					data: {functionName:'registerUserPoint',member_id:member_id,point_group:point_group},
					success: function(data){
						if(data == 1){
							tempToast('info','<p>Action completed successfully.</p>','<h3>INFO!</h3>');
							button_action.end_loading(con);
							$('#myModalRegister').modal('hide');
							getPoints();
						} else {
							tempToast('error','<p>'+data+'</p>','<h3>ERROR!</h3>');
							button_action.end_loading(con);
						}

					},
					error:function(){
						tempToast('error','<p>Please  try again</p>','<h3>ERROR!</h3>');
					}
				});
			}
			$('body').on('click','#btnAddPoint',function(){
				$('#add_point').val('');
				$('#add_unit').val('');
				$('#add_amount').val('');
				$('#add_name').val('');
				$('#myModalAdd').modal('show');
			});
			$('body').on('click','#submitPoint',function(){
				var points = $('#add_point').val();
				var amount = $('#add_amount').val();
				var name = $('#add_name').val();
				var unit = $('#add_unit').val();
				var con = $(this);
				button_action.start_loading(con);
				alertify.confirm("Are you sure you want to add this pointing system?",function(e){
					if(e){
						addNewPoint(points,amount,name,con,unit);
					} else {
						button_action.end_loading(con);
					}
				});
			});
			$('body').on('click','#registerUser',function(){

				$('#reg_name').select2('val',null);
				$('#reg_point').val('');
				$('#myModalRegister').modal('show');


			});
			$('body').on('click','#submitRegistration',function(){
				var member_id = 	$('#reg_name').val();
				var point_group = 	$('#reg_point_name').val();
				var con = $(this);

				if(member_id && point_group){
					button_action.start_loading(con);
					alertify.confirm("Are you sure you want to add this person?",function(e){
						if(e){
							registerUser(member_id,point_group,con);
						} else {
							button_action.end_loading(con);
						}
					});
				}
			});
			$('body').on('click','#btnAddMemGroup',function(){
				$('#myModalAddMemGroup').modal('show');
			});
			$('body').on('click','#submitMemGroup',function(){
				var name = $('#name_mem_group').val();
				var point_list_mem = $('#point_list_mem').val();
				var sup_count = $('#sup_count').val();
				var purchase_per_month = $('#purchase_per_month').val();
				var binary_pv_total = $('#binary_pv_total').val();
				var uni_level_pv_total = $('#uni_level_pv_total').val();

				if(name){
					$.ajax({
					    url:'../ajax/ajax_points.php',
					    type:'POST',
					    data: {functionName:'addMemGroup',uni_level_pv_total:uni_level_pv_total,binary_pv_total:binary_pv_total,sup_count:sup_count,purchase_per_month:purchase_per_month,name:name,point_list:JSON.stringify(point_list_mem)},
					    success: function(data){
					        alertify.alert(data,function(){
						        $('#myModalAddMemGroup').modal('hide');
						        getPoints();
					        });
					    },
					    error:function(){
					        
					    }
					});
				}
			});
			$('body').on('click','.updateGroup',function(e){
				e.preventDefault();
				var con = $(this);
				var id = con.attr('data-id');
				var name = con.attr('data-name');
				var sup_count = con.attr('data-sup_count');
				var value = $('#hid_group_'+id).val();
				value = value.split(',');
				$('#update_point_group_id').val(id);
				$('#update_group_name').val(name);
				$('#update_sup_count').val(sup_count);
				$('#update_point_list_mem').select2('val',value);
				$('#myModalUpdateGroup').modal('show');
			});
			$('body').on('click','#updateMemGroup',function(){
				var name = 	$('#update_group_name').val();
				var value = $('#update_point_list_mem').val();
				var id = $('#update_point_group_id').val();
				var sup_count = $('#update_sup_count').val();
				$.ajax({
				    url:'../ajax/ajax_points.php',
				    type:'POST',
				    data: {functionName:'updateGroup',sup_count:sup_count,name:name,value:JSON.stringify(value),id:id},
				    success: function(data){
				        alertify.alert(data,function(){
					        $('#myModalUpdateGroup').modal('hide');
					        getPoints();
				        });
				    },
				    error:function(){
				        
				    }
				})
			});
			function getSupplementary(member_id){
				$('#supplementary_holder').html('Loading...');
				$.ajax({
				    url:'../ajax/ajax_points.php',
				    type:'POST',
				    data: {functionName:'getSupplementary',member_id:member_id},
				    success: function(data){
				        $('#supplementary_holder').html(data);
					    if($('#hid_disable_add_suplementary').length > 0){
						    $('#btnSubmitSupplemenatry').attr('disabled', true);
						    $('#supplementary_id').select2('enable', false);
					    } else {
						    $('#btnSubmitSupplemenatry').attr('disabled', false);
						    $('#supplementary_id').select2('enable', true);
					    }
				    },
				    error:function(){
				        
				    }
				});
			}
			$('#supplementary_id').select2({
				placeholder: 'Search Supplementary',
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
							k_type: 5,
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
			$('body').on('click','.btnAddSupplementary',function(){
				var member_id  = $(this).attr('data-id');
				getSupplementary(member_id);
				$('#supplementary_member_id').val(member_id);
				$('#supplementary_id').select2('val',null);
				$('#myModalSupplementary').modal('show');
			});
			$('body').on('click','#btnSubmitSupplemenatry',function(){
				var member_id = $('#supplementary_member_id').val();
				var sup_id = $('#supplementary_id').val();
				$.ajax({
				    url:'../ajax/ajax_points.php',
				    type:'POST',
				    data: {functionName:'saveSupplementary',member_id:member_id,sup_id:sup_id},
				    success: function(data){
					    tempToast('info','<p>'+data+'</p>','<h3>Info!</h3>');
					    $('#supplementary_id').select2('val',null);
					    getSupplementary(member_id);
				    },
				    error:function(){

				    }
				})
			});
		});



	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>