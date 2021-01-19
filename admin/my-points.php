<?php
	// $user have all the properties and method of the current user
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('p_point')){
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
		<?php include 'includes/my_points_nav.php' ?>
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
								<div class="col-md-4"></div><div class="col-md-4"></div>
								<div class="col-md-4 text-right">
									<div class="form-group">
										<button id='btnSellPoints' class='btn btn-default'>Sell Points</button>
									</div>
								</div>
							</div>
							<div id="holder2"></div>
							<input type="hidden" id='hiddenpage2'/>
						</div>
						<div id="con3"  style='display: none;'>
							<div class="row">
								<div class="col-md-4">
									<select name="t_type" id="t_type" class='form-control'>
										<option value="1">Pending</option>
										<option value="2">Transferred</option>
										<option value="6">Cancelled</option>
									</select>
								</div>
								<div class="col-md-4"></div>
								<div class="col-md-4 text-right"><button id='btnRequestTransfer' class='btn btn-default btn-sm'><i class='fa fa-plus'></i> Request transfer</button></div>
							</div>

							<div id="holder3"></div>
							<input type="hidden" id='hiddenpage3'/>
						</div>
						<div id="con4"  style='display: none;'>
							<div class="row">
								<div class="col-md-8"></div>
								<div class="col-md-4"><div id="filter_holder_log"></div></div>
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
					</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<script>
		$(document).ready(function(){
			var body =  $('body');
			var whereami = 1;
			body.on('click','.t_process',function(){
				var con = $(this);
				var id = con.attr('data-id');
				button_action.start_loading(con);
				alertify.confirm("Are you sure you want to process this request?",function(e){
					if(e){
						$.ajax({
						    url:'../ajax/ajax_points.php',
						    type:'POST',
						    data: {functionName:'processTransferPoints',id:id},
						    success: function(data){
						        alertify.alert(data);
							    getTransferPoints();
						    },
						    error:function(){

						    }
						});
						button_action.end_loading(con);
					} else {
						button_action.end_loading(con);
					}
				});
			});
			body.on('change','#t_type',function(){
				getPage3(0);
			});
			body.on('click','.t_cancel',function(){
				var con = $(this);
				var id = con.attr('data-id');
				button_action.start_loading(con);
				alertify.confirm("Are you sure you want to cancell this request?",function(e){
					if(e){
						$.ajax({
							url:'../ajax/ajax_points.php',
							type:'POST',
							data: {functionName:'cancelTransferPoints',id:id},
							success: function(data){
								alertify.alert(data);
								getTransferPoints();
							},
							error:function(){

							}
						});
						button_action.end_loading(con);
					} else {
						button_action.end_loading(con);
					}
				});
			});
			body.on('click','#btnSellPoints',function(){
				sellPointsModal();
			});
			body.on('click','#btnRequestTransfer',function(){
				getAvailablePoints();
			});
			showContainer(true, false, false,false);
			body.on('click', '.btn_nav', function(e) {
				e.preventDefault();
				var con = $(this).attr('data-con');
				if(con == 1){
					showContainer(true, false, false, false);
					whereami = 1;
				} else if(con == 2) {
					showContainer(false, true, false, false);
					whereami = 2;
				} else if(con == 3) {
					showContainer(false, false, true, false);
					whereami = 3;
				} else if(con == 4) {
					showContainer(false, false, false,true);
					whereami = 4;
				}
			});
			body.on('click','#t_point_type',function(){
				var con = $(this);
				if(!con.val()){
					$('#t_value').val('');
				}
			});
			body.on('keyup','#t_value',function(){
				var con = $('#t_point_type');
				var v =con.val();
				var opt = $('#t_point_type > option:selected');
				var thiscon = $(this);
				var point_val = thiscon.val();
				var available = opt.attr('data-value');
				if(v){
					if(isNaN(point_val) || parseFloat(point_val) > parseFloat(available)){
						tempToast('error', '<p>Invalid value.</p>', "<h4>Error!</h4>");
						thiscon.val('');
					}
				} else {
					tempToast('error', '<p>Add Type First.</p>', "<h4>Error!</h4>");
					thiscon.val('');
				}
			});
			body.on('click','#t_submit',function(){
				var type = $('#t_point_type').val();
				var member_id = $('#t_member_id').val();
				var point_value = $('#t_value').val();
				var remarks = $('#t_remarks').val();
				var con = $(this);
				button_action.start_loading(con);
				if(validTransfer(type,member_id,point_value)){
					$.ajax({
						url:'../ajax/ajax_points.php',
						type:'POST',
						data: {functionName:'transferPoint',remarks:remarks,type:type,member_id:member_id,point_value:point_value},
						success: function(data){
							alertify.alert(data);
							$('#myModal').modal('hide');
							getTransferPoints();
							button_action.end_loading(con);
						},
						error:function(){
							button_action.end_loading(con);
						}
					});
				} else {
					button_action.end_loading(con);
					tempToast('error', '<p>Invalid Request</p>', "<h4>Error!</h4>");
				}
			});
			body.on('click','#s_point_type',function(){
				var con = $(this);
				if(!con.val()){
					$('#s_value').val('');
				}
			});
			body.on('keyup','#s_value',function(){
				var con = $('#s_point_type');
				var v =con.val();
				var opt = $('#s_point_type > option:selected');
				var thiscon = $(this);
				var point_val = thiscon.val();
				var available = opt.attr('data-value');
				if(v){
					if(isNaN(point_val) || parseFloat(point_val) > parseFloat(available)){
						tempToast('error', '<p>Invalid value.</p>', "<h4>Error!</h4>");
						thiscon.val('');
					}
				} else {
					tempToast('error', '<p>Add Type First.</p>', "<h4>Error!</h4>");
					thiscon.val('');
				}
			});
			body.on('click','#s_submit',function(){
				var type = $('#s_point_type').val();
				var point_value = $('#s_value').val();
				var amount = $('#s_amount').val();
				var remarks = $('#s_remarks').val();
				var con = $(this);
				button_action.start_loading(con);
				if(type && point_value && amount ){
					$.ajax({
						url:'../ajax/ajax_points.php',
						type:'POST',
						data: {functionName:'createSellPoint',remarks:remarks,type:type,amount:amount,point_value:point_value},
						success: function(data){
							alertify.alert(data);
							$('#myModal').modal('hide');
							getSellPoints();
							button_action.end_loading(con);
						},
						error:function(){
							button_action.end_loading(con);
						}
					});
				} else {
					button_action.end_loading(con);
					tempToast('error', '<p>Invalid Request</p>', "<h4>Error!</h4>");
				}
			});
			body.on('click','.btnBidList',function(){
				var con = $(this);
				var id = con.attr('data-id');
				$('#myModal').modal('show');
				var mbody = $('#mbody');
				mbody.html('Loading');
				$.ajax({
					url:'../ajax/ajax_points.php',
					type:'POST',
					data: {functionName:'getBidList',id:id},
					success: function(data){
						mbody.html(data);
					},
					error:function(){

					}
				});
			});
			function validTransfer(t,m,v){
				return (t && m && v);
			}
			body.on('change','#filter_point',function(){
				getPage4();
			});
			body.on('click','.btnBid',function(){
				var con = $(this);
				var id = con.attr('data-id');
				$('#myModal').modal('show');
				var mbody = $('#mbody');
				mbody.html('Loading');
				$.ajax({
				    url:'../ajax/ajax_points.php',
				    type:'POST',
				    data: {functionName:'getBidForm',id:id},
				    success: function(data){
				        mbody.html(data);
				    },
				    error:function(){

				    }
				});
			});
			body.on('click','#b_submit',function(){
				var con = $(this);
				var id = $('#b_hid_id').val();
				var remarks = $('#b_remarks').val();
				var amount = $('#b_amount').val();
				button_action.start_loading(con);
				$.ajax({
				    url:'../ajax/ajax_points.php',
				    type:'POST',
				    data: {functionName:'saveBidList',id:id,remarks:remarks,amount:amount},
				    success: function(data){
					    alertify.alert(data);
					    button_action.end_loading(con);
				    },
				    error:function(){
					    button_action.end_loading(con);
				    }
				})

			});
			body.on('click','.btnBidCancel',function(){
				var con = $(this);
				var id = con.attr('data-id');
				$.ajax({
				    url:'../ajax/ajax_points.php',
				    type:'POST',
				    data: {functionName:'bidCancel',id:id},
				    success: function(data){
				        alertify.alert(data);
					    getSellPoints();
				    },
				    error:function(){
				        
				    }
				});
			});
			body.on('click','.paging',function(e){
				e.preventDefault();
				var page=0;
				if(whereami == 1){


				} else if (whereami == 2){
					 page = $(this).attr('page');
					$('#hiddenpage2').val(page);
					getPage2(page);
				}else if (whereami == 3){
					page = $(this).attr('page');
					$('#hiddenpage3').val(page);
					getPage3(page);
				}else if (whereami == 4){
					page = $(this).attr('page');
					$('#hiddenpage4').val(page);
					getPage4(page);
				}

			});
			function getAvailablePoints(){
				$('#t_point_type').val('');
				$('#t_member_id').val('');
				$('#t_value').val('');
				$('#t_remarks').val('');
				$('#myModal').modal('show');
				var mbody = $('#mbody');
				mbody.html('Loading');
				$.ajax({
				    url:'../ajax/ajax_points.php',
				    type:'POST',
				    data: {functionName:'getAvailablePoints'},
				    success: function(data){
					    mbody.html(data);
					    $('#t_member_id').select2({
						    placeholder: 'Search to',
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
				    },
				    error:function(){
				        
				    }
				});
			}
			function sellPointsModal(){
				$('#s_point_type').val('');
				$('#s_value').val('');
				$('#s_amount').val('');
				$('#s_remarks').val('');
				$('#myModal').modal('show');
				var mbody = $('#mbody');
				mbody.html('Loading');
				$.ajax({
					url:'../ajax/ajax_points.php',
					type:'POST',
					data: {functionName:'sellingPointsModal'},
					success: function(data){
						mbody.html(data);
					},
					error:function(){

					}
				});
			}
			function getSellPoints(){
				getPage2();

			}
			function getMyPoints(){
				$.ajax({
				    url:'../ajax/ajax_points.php',
				    type:'POST',
				    data: {functionName:'getMyPoints'},
				    success: function(data){
				        $('#con1').html(data);
				    },
				    error:function(){
				        
				    }
				});
			}
			function getUserPointsLog(){

				try{
					var jsonlist= $('#mypoint_list').val();
					jsonlist = JSON.parse(jsonlist);
					var ret = '';
					ret +="<select id='filter_point' class='form-control'>";
					ret +="<option value=''>Select type</option>";
					for(var i in jsonlist){
						ret +="<option value='"+jsonlist[i].point_id+"'>"+jsonlist[i].point_name+"</option>";
					}
					ret +="</select>";
					$('#filter_holder_log').html(ret);
				}catch(e){

				}
				getPage4();
			}
			function getTransferPoints(){
				getPage3();
			}
			function getPage2(){
				$.ajax({
				    url:'../ajax/ajax_points.php',
				    type:'POST',
				    data: {functionName:'getSellPoints'},
				    success: function(data){
						$('#holder2').html(data);
				    },
				    error:function(){
				        
				    }
				});
			}
			function getPage3(p){
				var status = $('#t_type').val();
				$.ajax({
					url: '../ajax/ajax_points.php',
					type:'post',
					beforeSend:function(){
						$('#holder3').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,status:status,functionName:'getTransferPoints'},
					success: function(data){
						$('#holder3').html(data);
					}
				});
			}
			function getPage4(p){
				var filter_point = $('#filter_point').val();
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder4').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,filter_point:filter_point,functionName:'userPointsLog',cid: <?php echo $user->data()->company_id; ?>, user_view:1},
					success: function(data){
						$('#holder4').html(data);
					}
				});
			}
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
					getMyPoints();
				} else if(c2) {
					con2.fadeIn(300);
					getSellPoints();
				} else if(c3) {
					con3.fadeIn(300);
					getTransferPoints();
				} else if(c4) {
					con4.fadeIn(300);
					getUserPointsLog();

				}
			}

		});



	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>