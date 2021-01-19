<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('member')) {
		// redirect to denied page
		Redirect::to(1);
	}

	$mem_cls = new Member();
	$distinct_regions = $mem_cls->distinctRegion();


	$is_agent = false;

	if($user->hasPermission('wh_agent') && !$user->hasPermission('wh_all_member')) {
		$is_agent = true;
	}

?>


	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> <?php echo MEMBER_LABEL; ?> </h1>

		</div> <?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}
		?>

		<div class="row">
			<div class="col-md-12">
				<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
					<?php if($user->hasPermission('member_m')) { ?>

						<a class='btn btn-default' href='addmember.php' title='Add <?php echo MEMBER_LABEL; ?>'>
							<span class='glyphicon glyphicon-plus'></span>
							<span class='hidden-xs'>Add <?php echo MEMBER_LABEL; ?></span> </a>


					<?php } ?>
					<?php if($user->hasPermission('body_measure')) { ?>
						<a class='btn btn-default' href='offered_service.php' title='<?php echo MEMBER_LABEL; ?> Service'>
							<span class='glyphicon glyphicon-plus'></span> <span class='hidden-xs'>Discipline</span>
						</a>
						<a class='btn btn-default' href='member_service_report.php' title='Class/Discipline List'>
							<span class='glyphicon glyphicon-plus'></span>
							<span class='hidden-xs'>Class/Discipline List</span> </a>
						<a class='btn btn-default' href='body-measurements.php' title='Body Measurements'>
							<span class='glyphicon glyphicon-plus'></span>
							<span class='hidden-xs'>Body Measurements</span> </a>
						<a class='btn btn-default' href='measurement-history.php' title='Body Measurements'>
							<span class='glyphicon glyphicon-plus'></span>
							<span class='hidden-xs'>Measurement History</span> </a>					<?php } ?>

					<?php if($user->hasPermission('m_ref')) { ?>

						<a class='btn btn-default' href='coach.php' title='Coach'>
							<span class='glyphicon glyphicon-plus'></span> <span class='hidden-xs'>Coach</span> </a>
						<a class='btn btn-default' href='class_schedule.php' title='Coach'>
							<span class='glyphicon glyphicon-plus'></span> <span class='hidden-xs'>Schedule Class</span>
						</a>
						<a class='btn btn-default' href='booking_request.php' title='Booking'>
							<span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>Booking list</span>
						</a>

					<?php } ?>
				</div> <?php if($user->hasPermission('body_measure')) { ?>
					<div class="row">
						<div class="col-md-12 text-right">
							<a class='btn btn-default' href='online_web_inquiry.php' title='Booking'>
								<span class='glyphicon glyphicon-list'></span>
								<span class='hidden-xs'>Bookings request from Web</span> </a>
							<a class='btn btn-default' href='booking_request.php' title='Booking'>
								<span class='glyphicon glyphicon-list'></span>
								<span class='hidden-xs'>Booking request from members</span> </a>
						</div>
					</div>                    <br>				<?php } ?>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">
						<div class="row">
							<div class='col-md-6'><?php echo MEMBER_LABEL; ?></div>
							<div class='col-md-6 text-right'>
								<?php if($user->hasPermission('dl_member')) { ?>
									<button id='btnDownloadExcel' title='Download Excel' class='btn btn-default btn-sm'>
										<i class='fa fa-download'></i></button>							<?php } ?>
							</div>
						</div>
					</div>
					<div class="panel-body">
						<div class="row">

							<div class="col-md-3">
								<div class="input-group">
									<span class="input-group-addon"><span class='glyphicon glyphicon-search'></span></span>
									<input type="text" id="searchSales" class='form-control' placeholder='Search..' />

								</div>
								<span class='help-block'>Search Member Name</span>
							</div>
							<div class="col-md-3">
								<select name="salestype" id="salestype" class='form-control'>
									<option value="">All</option> <?php
										$sales_type = new Sales_type();
										$sales_types = $sales_type->get_active('salestypes', array('company_id', '=', $user->data()->company_id));
										foreach($sales_types as $st) {
											$curid = (isset($id)) ? $editMem->data()->salestype : 0;
											if($st->id == $curid) {
												$selected = 'selected';
											} else {
												$selected = '';
											}
											echo "<option value='$st->id' $selected>$st->name</option>";
										}
									?>
								</select> <span class='help-block'>Type</span>
							</div>
							<div class="col-md-3">
								<select name="char" id="char" class='form-control'>
									<option value="">All</option> <?php
										$memberChar = new Member_char_list();
										$memberChars = $memberChar->get_active('member_characteristics_list', array('company_id', '=', $user->data()->company_id));
										foreach($memberChars as $char) {

											echo "<option value='$char->id' >$char->name</option>";
										}
									?>
								</select> <span class='help-block'>Characteristics</span>
							</div> <?php if($distinct_regions) {
								?>
								<div class="col-md-3">
									<select name="region" id="region" class='form-control'>
										<option value="">All</option> <?php
											foreach($distinct_regions as $dis) {

												echo "<option value='$dis->region' >$dis->region</option>";
											}
										?>
									</select> <span class='help-block'>Location</span>
								</div>								<?php
							} ?>


							<?php
								if($is_agent) {
									?>
									<input type='hidden' name="agent_id" id="agent_id" value='<?php echo $user->data()->id; ?>'>											<?php
								} else {
									$crudcls = new Crud();
									$allusers = $crudcls->get_active('users', array('company_id', '=', $user->data()->company_id));
									if($allusers) {
										?>
										<div class="col-md-3">
											<div class="form-group">
												<select name="agent_id" id="agent_id" class='form-control input-md'>
													<option value=""></option> <?php
														foreach($allusers as $p) {
															$pos_cur = new Position($p->position_id);
															$curpermissions = json_decode($pos_cur->data()->permisions, true);
															if(!isset($curpermissions['wh_agent'])) continue;
															?>
															<option value="<?php echo $p->id; ?>"> <?php echo ucwords($p->lastname . ", " . $p->firstname); ?></option>                                <?php
														}
													?>
												</select> <span class='help-block'>Select Agent</span>
											</div>
										</div>											<?php }
								}
							?>


							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' id='date_from' placeholder='Date From'>
									<span class='help-block'>Filter member by date of membership</span>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' id='date_to' placeholder='Date To'>
								</div>
							</div>
						</div>
						<input type="hidden" id="hiddenpage" />

						<div id="holder"></div>
					</div>


				</div>
			</div>
		</div>
	</div>
	<!-- end page content wrapper-->
	<script>

		$(document).ready(function() {

			localStorage.removeItem("mem_scan");
			scanListener();
			if(!$('#agent_id').val()) {
				$('#agent_id').select2({
					allowClear: true, placeholder: "Search Agent"
				});
			}


			function scanListener() {

				var millis = 100;

				document.onkeypress = function(e) {

					e = e || window.event;

					var charCode = (typeof e.which == "number") ? e.which : e.keyCode;

					if(localStorage.getItem("mem_scan") && localStorage.getItem("mem_scan") != 'null') {
						localStorage.setItem("mem_scan", localStorage.getItem("mem_scan") + String.fromCharCode(charCode));
					} else {
						localStorage.setItem("mem_scan", String.fromCharCode(charCode));
						setTimeout(function() {
							localStorage.removeItem("mem_scan");
						}, millis);
					}

					if(localStorage.getItem("mem_scan").length >= 5) {
						var mscan = (localStorage.getItem("mem_scan")) ? localStorage.getItem("mem_scan") : '';
						$('#searchSales').val(mscan.trim());
						getPage(0);
					}

				}

			}

			$('#date_from').datepicker({
				autoclose: true
			}).on('changeDate', function(ev) {
				$('#date_from').datepicker('hide');
				if($('#date_from').val() && $('#date_to').val()) {
					getPage(0);
				}
			});

			$('#date_to').datepicker({
				autoclose: true
			}).on('changeDate', function(ev) {
				$('#date_to').datepicker('hide');
				if($('#date_from').val() && $('#date_to').val()) {
					getPage(0);
				}
			});

			getPage(0);

			$('body').on('click', '.paging', function() {
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});


			var timer;
			$("#searchSales").keyup(function() {
				var searchtxt = $("#searchSales");

				clearTimeout(timer);
				timer = setTimeout(function() {
					if(searchtxt.val()) {
						searchtxt.val(searchtxt.val().trim());
					}
					getPage(0);
				}, 1000);
			});

			$("#salestype,#char,#agent_id,#region").change(function() {
				getPage(0);
			});
			function getPage(p) {
				var search = $('#searchSales').val();
				var salestype = $('#salestype').val();
				var char = $('#char').val();
				var agent_id = $('#agent_id').val();
				var region = $('#region').val();
				var date_from = $('#date_from').val();
				var date_to = $('#date_to').val();
				$('.loading').show();
				$('#holder').html('<br><p>Loading...</p>');
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type: 'post',
					data: {
						page: p,
						functionName: 'memberList',
						date_from: date_from,
						date_to: date_to,
						region: region,
						agent_id: agent_id,
						char: char,
						salestype: salestype,
						cid: <?php echo $user->data()->company_id; ?>,
						search: search
					},
					success: function(data) {
						$('#holder').html(data);
						$('.loading').hide();
					},
					error: function() {
						//alert('Something went wrong. The page will be refresh.');
						location.href = 'members.php';
						$('.loading').hide();
					}
				});
			}

			$('body').on('click', '.deleteMember', function() {
				if(confirm("Are you sure you want to delete this record?")) {
					id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php', {id: id, table: 'members'}, function(data) {
						if(data == "true") {
							location.reload();
						}
					});
				}
			});

			$('body').on('change', '.chkBlockList', function() {
				var blacklist = 0;
				var memid = $(this).attr('data-member_id');
				if($(this).is(':checked')) {
					blacklist = 1;
				}
				$.ajax({
					url: '../ajax/ajax_memberblack.php',
					type: 'post',
					data: {isblack: blacklist, memid: memid},
					success: function(data) {

					}
				});
			});

			$('body').on('click', '#btnDownloadExcel', function() {
				var search = $('#searchSales').val();
				var salestype = $('#salestype').val();
				var char = $('#char').val();
				var agent_id = $('#agent_id').val();
				var region = $('#region').val();
				var date_from = $('#date_from').val();
				var date_to = $('#date_to').val();
				region = (region ) ? region : 0;
				window.open('excel_downloader.php?downloadName=members&search=' + search + '&salestype=' + salestype + '&char=' + char + '&agent_id=' + agent_id + '&region=' + region + '&date_from=' + date_from + '&date_to=' + date_to, '_blank' //
				);
			});
		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>