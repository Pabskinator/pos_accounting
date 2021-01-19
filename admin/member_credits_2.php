<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('credit_monitoring')) {
		// redirect to denied page
		Redirect::to(1);
	}

?>


	<input type="hidden" id='MEMBER_LABEL' value='<?php echo MEMBER_LABEL; ?>'>
	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Document Tracking</h1>

		</div>


		<div class="row">
			<div class="col-md-12">

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">
						<div class="row">
							<div class="col-md-6">

							</div>
							<div class="col-md-6 text-right">

							</div>
						</div>
					</div>
					<div class="panel-body">
						<div class="row">

							<div class="col-md-3">
								<div class="input-group">
									<span class="input-group-addon"><span class='glyphicon glyphicon-search'></span></span>
									<input type="text" id="searchSales" class='form-control' placeholder='Search..'/>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon"><span class='glyphicon glyphicon-calendar'></span></span>
										<input type="text" id="dt_from" class='form-control' placeholder='Date From'/>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon"><span class='glyphicon glyphicon-calendar'></span></span>
										<input type="text" id="dt_to" class='form-control' placeholder='Date To'/>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<select name="paid_type" id="paid_type" class="form-control">
									<option value="">Choose type</option>
									<option value="1">Fully paid</option>
									<option value="2">Not yet paid</option>
									<?php if(Configuration::getValue('credit_approval') == 1){ ?>
										<option value="3">For approval</option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="row">

							<?php if($user->hasPermission('credit_all')){
								?>
								<div class="col-md-3">
									<div class="form-group">
										<select id="branch_id" name="branch_id" class="form-control" multiple>
											<?php
												if(Configuration::isAquabest()){
													?>
													<option value="-1">Caravan</option>
													<?php
												}
												$branch = new Branch();
												$branches =  $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));
												foreach($branches as $b){

													$a = isset($id) ? $terminal->data()->branch_id : escape(Input::get('branch_id'));
													if($a == $b->id){
														$selected = 'selected';
													} else {
														$selected = '';
													}

													?>
													<option value='<?php echo $b->id ?>' <?php echo $selected ?>><?php echo $b->name;?> </option>
													<?php
												}
											?>
										</select>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<div id="terminalholder">
											<p class='text-info'></p>
										</div>
									</div>
								</div>
								<?php
							}
							?>
						</div>
						<input type="hidden" id="hiddenpage" />
						<div id="holder"></div>

					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id='mtitle'></h4>
					</div>
					<div class="modal-body" id='mbody'>
						<input type="hidden" id='hidden_id'>
						<div class="form-group">
							<strong>Name: </strong> <input id='name' type="text" class='form-control'>
						</div>
						<div class="form-group">
							<strong>Add Remarks: </strong>
							<select name="remarks" id="remarks" class='form-control'>
								<option value="Receive <?php echo DR_LABEL; ?>">Receive <?php echo DR_LABEL; ?></option>
								<option value="Receive <?php echo INVOICE_LABEL; ?>">Receive <?php echo INVOICE_LABEL; ?></option>
								<option value="Receive <?php echo PR_LABEL; ?>">Receive <?php echo PR_LABEL; ?></option>
								<option value="Return <?php echo DR_LABEL; ?>">Return <?php echo DR_LABEL; ?></option>
								<option value="Return <?php echo INVOICE_LABEL; ?>">Return <?php echo INVOICE_LABEL; ?></option>
								<option value="Return <?php echo PR_LABEL; ?>">Return <?php echo PR_LABEL; ?></option>
							</select>
						</div>
						<div class="form-group">
							<button class='btn btn-primary' id='btnSubmit'>Submit</button>
						</div>
					</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->




	<script>
		//SELECT * FROM  `sales` WHERE  `item_id` =589 AND sold_date <1475251200

		$(document).ready(function() {


			$('body').on('click','.paging',function(){
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});




			var timer;
			$("#searchSales").keyup(function(){

				var searchtxt = $("#searchSales");




				clearTimeout(timer);
					timer = setTimeout(function() {
					if(searchtxt.val()){
						searchtxt.val(searchtxt.val().trim());
					}
					getPage(0);

				}, 1000);

			});

			$("#paid_type").change(function(){
				getPage(0);
			});


			getPage(0);

			function getPage(p){

				var search = $('#searchSales').val();
				var paid_type =  $('#paid_type').val();
				var dt_from = $('#dt_from').val();
				var dt_to = $('#dt_to').val();
				var branch_id = $('#branch_id').val();

				$.ajax({
					url: '../ajax/ajax_paging_2.php',
					type:'post',
					beforeSend:function(){

					},
					data:{page:p,functionName:'memberCreditList',branch_id:branch_id,dt_from:dt_from,dt_to:dt_to,cid: <?php echo $user->data()->company_id; ?>,search:search,paid_type:paid_type},
					success: function(data){

						$('#holder').html(data);
						$('.loading').hide();
					},
					error: function(){
						alert('Something went wrong. The page will be refresh.');
						location.href='member_credits.php';
						$('.loading').hide();
					}
				});

			}

			$('#dt_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_from').datepicker('hide');
				getPage(0);
			});

			$('#dt_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_to').datepicker('hide');
				getPage(0);
			});

			$("#branch_id").select2({
				placeholder: 'Choose Branch',
				allowClear: true
			});


			$("body").on('click','.paymentDetails',function(e){
				e.preventDefault();
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

			$("#name").select2({
				placeholder: 'Search User',
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
							functionName:'users'
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


			$('body').on('click','#btnSubmit',function(e){
				e.preventDefault();
				var name = $('#name').select2('data').text;
				var id = $('#hidden_id').val();
				var remarks = $('#remarks').val();

				$.ajax({
				    url:'../ajax/ajax_member_service.php',
				    type:'POST',
				    data: {functionName:'saveCreditHolder',name:name,id:id,remarks:remarks},
				    success: function(data){
					    getPage(0);
					    $('#myModal').modal('hide');
						tempToast('info',data,'Info');
				    },
				    error:function(){

				    }
				});

			});


			$('body').on('click','.btnAddDetails',function(){
				var con = $(this);
				var id = con.attr('data-id');
				$('#name').select2('val',null);
				$('#remarks').val('');
				$('#hidden_id').val(id);
				$('#myModal').modal('show');

			});



		});




	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>