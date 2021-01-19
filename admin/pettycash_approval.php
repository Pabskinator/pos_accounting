<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('pettycash')){
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
				Petty Cash Request
			</h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')){
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('flash')."</div>";
			}
		?>
		<?php include 'includes/petty_nav.php'; ?>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading"></div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<select name="status" id="status" class="form-control">
										<option value="1">Pending</option>
										<option value="2">Approved</option>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<?php if(!$user->hasPermission('is_franchisee')){
									?>
									<div class="form-group">
										<select id="branch_id" name="branch_id" class="form-control">
											<option value=''></option>
											<?php
												$branch = new Branch();
												$branches =  $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));
												foreach($branches as $b){
													?>
														<option value='<?php echo $b->id ?>'><?php echo $b->name;?> </option>
													<?php
												}
											?>
										</select>
									</div>
									<?php
								}?>

							</div>
							<div class="col-md-3">
								<div class="form-group">

								</div>
							</div>
						</div>
						<input type="hidden" id="hiddenpage" />
						<div id="holder"></div>
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
			getPage(0);
			$('#branch_id').select2({
				placeholder:'Choose branch',
				allowClear:true
			});
			$('body').on('click', '.paging', function(e) {
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});
			$('body').on('change','#status,#branch_id',function(){
				getPage(0);
			});
			function getPage(p) {
				var status = $('#status').val();
				var branch_id = $('#branch_id').val();

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type: 'post',
					beforeSend: function() {
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data: {
						page: p,
						status:status,
						branch_id:branch_id,
						functionName: 'pettycashRequest',
						cid: <?php echo $user->data()->company_id; ?>
					},
					success: function(data) {
						$('#holder').html(data);

					}
				});
			}
			$('body').on('click','.btnBreakdown',function(){
				var id = $(this).attr('data-id');
				var branch_id = $(this).attr('data-branch_id');
				$('#myModal').modal('show');
				$.ajax({
				    url:'../ajax/ajax_query2.php',
				    type:'POST',
					beforeSend:function(){
						$('#mbody').html('Waiting for server response...');
					},
				    data: {functionName:'getPettyExpense',request_id:id,branch_id:branch_id},
				    success: function(data){
					    $('#mbody').html(data);
				    },
				    error:function(){

				    }
				});
			});
			$('body').on('click','#btnApprovedPetty',function(){
				var btn = $(this);
				var id = btn.attr('data-id');
				var oldval = btn.html();
				btn.attr('disabled',true);
				btn.html('Loading...');
				alertify.confirm('Are you sure you want to process this request?',function(e){
					if(e){
						$.ajax({
						    url:'../ajax/ajax_query2.php',
						    type:'POST',
						    data: {functionName:'approvedPettycash',id:id},
						    success: function(data){
							    $('#myModal').modal('hide');
							    $('#mbody').html('');
							    getPage(0);
							    tempToast('info',"<p>" + data + "</p>","<h4>Information!</h4>");
						    },
						    error:function(){

						    }
						})
					} else {
						btn.attr('disabled',false);
						btn.html(oldval);
					}
				});
			});
			$('body').on('click','#btnReturnPetty',function(){
				var btn = $(this);
				var id = btn.attr('data-id');
				var oldval = btn.html();
				btn.attr('disabled',true);
				btn.html('Loading...');
					alertify.confirm('Are you sure you want to return this request?',function(e){
						if(e){
							$.ajax({
								url:'../ajax/ajax_query2.php',
								type:'POST',
								data: {functionName:'returnPettycash',id:id},
								success: function(data){
									$('#myModal').modal('hide');
									$('#mbody').html('');
									getPage(0);
									tempToast('info',"<p>" + data + "</p>","<h4>Information!</h4>");
								},
								error:function(){

								}
							})
						} else {
							btn.attr('disabled',false);
							btn.html(oldval);
						}
				});
			});
		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>