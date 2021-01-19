<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('item')) {
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
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Notifications </h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}
		?>

		<div class="row">
			<div class="col-md-12">
				<div class="btn-group" role="group" aria-label="..." style='margin-bottom: 5px;'>
					<button type="button" class="btn btn-default" id='nav_unread'>Unread(<?php echo $sb_alert_count->cnt; ?>)</button>
					<button type="button" class="btn btn-default" id='nav_read'>Read</button>

				</div>

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Notifications</div>
					<div class="panel-body">
						<div id="unreadcon">
						<?php
							$n_alertcls = new Alert_item();
							$list = $n_alertcls->getAlertMsg($user->data()->position_id,$user->data()->company_id,$user->data()->id);
							if($list){
								?>
								<table class="table">
									<thead>
									<tr>
										<th>Item</th>
										<th>Invoice/Dr</th>
										<th>Sold Date</th>
										<th>Member</th>
										<th>Alert Message</th>
										<th></th>
									</tr>
									</thead>
									<?php
										$noti = new Notification();
										$now = time();
										foreach($list as $det){

												$noti->create(array(
													'user_id' => $user->data()->id,
													'payment_id' => $det->payment_id,
													'item_id' => $det->item_id,
													'company_id' => $user->data()->company_id,
													'is_active' => 1,
													'created' => $now,
													'modified' => $now
												));


											$inv='';
											$dr ='';
											if($det->invoice){
												$inv = $det->invoice;
											}
											if($det->dr){
												$dr = $det->dr;
											}
											?>
											<tr>
												<td><?php echo escape($det->item_code). "<br> <small class='text-danger'>" . escape($det->description) . "</small>" ?></td>
												<td><?php echo escape($inv . " " . $dr); ?></td>
												<td><?php echo escape(date('m/d/Y',$det->sold_date))?></td>
												<td><?php echo escape(ucwords($det->mln . ", " . $det->mfn)); ?></td>
												<td><?php echo  escape($det->alert_msg); ?></td>
												<td></td>
											</tr>
										<?php
										}
									?>
								</table>
								<?php
							} else {
								echo "<p class='text-danger'>No unread notification</p>";
							}
						?>
						</div>
						<div id="readcon" style='display:none;'>
							<div class="row">
								<div class="col-md-4">
									<input  type='text' id='searchItem' placeholder='Search...' class='form-control'>
								</div>
								<div class="col-md-4">
									<input  type='text' id='dt1' placeholder='Date From' class='form-control'>
								</div>
								<div class="col-md-4">
									<input  type='text' id='dt2' placeholder='Date To' class='form-control'>
								</div>

							</div>
							<br>
							<input type="hidden" id="hiddenpage" />
							<div id="holder"></div>
						</div>
					</div>


				</div>
			</div>
		</div>
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog" style=''>
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id='mtitle'></h4>
					</div>
					<div class="modal-body" id='mbody'>
						<div class="panel panel-default">
							<div class="panel-body">
								<div id="addremarksholder"></div>
							</div>
						</div>
						<hr />
						<input type='hidden' id='a_item_id'>
						<input type='hidden' id='a_payment_id'>
						<?php if($user->hasPermission('notification_rm')){
							?>
							<div class="row">
								<div class="col-md-3">
									Remarks
								</div>
								<div class="col-md-9">
									<input type='text' placeholder='Remarks' id='a_remarks' class='form-control'>
								</div>
							</div>
							<hr />
							<div class="text-right">
								<button class='btn btn-default' id='a_save'><span class='glyphicon glyphicon-floppy-save'></span> Save</button>
							</div>
							<?php
						}?>

					</div>

				</div>
				<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div><!-- /.modal -->
	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function() {
			$('#nav_unread').click(function(){
				location.href='notification.php';
			});
			$('#nav_read').click(function(){
				$('#unreadcon').hide();
				$('#readcon').fadeIn();
				getPage(0);
			});
			getPage(0);
			$('body').on('click','.paging',function(){
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});

			function getPage(p,s){
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,search:s,functionName:'unreadNotification',cid: <?php echo $user->data()->company_id; ?>},
					success: function(data){
						$('#holder').html(data);
					}
				});
			}
			$('#searchItem').keyup(function(){
				var txt = $(this).val();
				getPage(0,txt);
			});
			$('body').on('click','.addrm',function(){
				var btn = $(this);
				var payment_id = btn.attr('data-payment_id');
				var item_id = btn.attr('data-item_id');
				$('#a_item_id').val(item_id);
				$('#a_payment_id').val(payment_id);
				$('.loading').show();
				$.ajax({
				    url:'../ajax/ajax_query2.php',
				    type:'post',
				    data: {payment_id:payment_id,item_id:item_id,functionName:'getNotificationRemarks'},
				    success: function(data){
				        $('#addremarksholder').html(data);

					    $('#myModal').modal('show');
					    $('.loading').hide();
				    },
				    error:function(){

					    $('.loading').hide();
				    }
				});

			});
			$('#a_save').click(function(){
				var item_id = $('#a_item_id').val();
				var payment_id = $('#a_payment_id').val();
				var remarks = $('#a_remarks').val();
				if(!remarks){
					alertify.alert('Please add remarks first.');
					$('#a_remarks').focus();
					return;
				}
				alertify.confirm('Are you sure you want to add this remarks?',
						function(s){
							if(s){
								$.ajax({
									url:'../ajax/ajax_query2.php',
									type:'POST',
									data: {item_id:item_id,payment_id:payment_id,remarks:remarks,functionName:'saveNotificationRemarks'},
									success: function(data){
										alertify.alert(data);
										$('#a_remarks').val('');
										$.ajax({
											url:'../ajax/ajax_query2.php',
											type:'post',
											data: {payment_id:payment_id,item_id:item_id,functionName:'getNotificationRemarks'},
											success: function(data){
												$('#addremarksholder').html(data);

											},
											error:function(){

											}
										});

									},
									error:function(){

									}
								});
							}
						}
					);
			});
		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>