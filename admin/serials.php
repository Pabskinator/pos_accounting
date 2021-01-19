<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('serials')) {
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
			<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Issued Serials </h1>


	</div>
	<?php
		// get flash message if add or edited successfully
		if(Session::exists('flash')) {
			echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
		}
	?>

		<div id="test"></div>
		<div class="row">
			<div class="col-md-12">
				<a href="duplicate-barcode.php" class='btn btn-default btn-sm' >Duplicate Serial/Barcode</a>
			</div>
		</div>
		<br>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-primary">
					<div class='panel-heading'>
						<div class="row">
							<div class="col-md-6">Serials</div>
							<div class="col-md-6 text-right">
								<button class='btn btn-default btn-sm' id='btnDownload'><i class='fa fa-download'></i></button>
							</div>
						</div>

					</div>
					<div class="panel-body">
						<div class="row">

							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon" id="basic-addon1"><i class='fa fa-search'></i></span>
										<input type="text" class='form-control' id='txtSearch' placeholder='Search Serial/Invoice/DR/PR'>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon" id="basic-addon1"><i class='fa fa-calendar-o'></i></span>
										<input type="text" name='dateStart' class='form-control' id='dateStart' placeholder='Date Start' />
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon" id="basic-addon1"><i class='fa fa-calendar-o'></i></span>
										<input type="text" name='dateEnd' class='form-control' id='dateEnd' placeholder='Date End' />
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
								<select  name="branch_id" id="branch_id" v-model="request.branch_id_to" class='form-control'>
									<option value=""></option>
									<?php
										$crud= new Crud();
										$branches = $crud->get_active('branches',array('company_id','=',$user->data()->company_id));
										if($branches){
											$bmfirst = true;
											foreach($branches as $b){
												//if($b->id == $user->data()->branch_id) continue;
												$bmselected ='';
												if($bmfirst){
													$bmfirst = false;
													//$bmselected='selected';
												}
												?>
												<option <?php echo $bmselected; ?> value="<?php echo $b->id; ?>"><?php echo $b->name; ?></option>
												<?php
											}
										}
									?>
								</select>
									</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='selectitem' id='item_id'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' id='member_id'>
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
	</div> <!-- end page content wrapper-->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style='width:95%;'>
			<div class="modal-content" >
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Payment Details</h4>
				</div>
				<div class="modal-body" id='mbody'>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<script>
		$(function(){
			$('#member_id').select2({
				placeholder: 'Search client' , allowClear: true, minimumInputLength: 2,

				ajax: {
					url: '../ajax/ajax_json.php', dataType: 'json', type: "POST", quietMillis: 50, data: function(term) {
						return {
							q: term, functionName: 'members'
						};
					}, results: function(data) {
						return {
							results: $.map(data, function(item) {

								return {
									text: item.lastname,
									slug: item.lastname + ", " + item.firstname + " " + item.middlename,
									id: item.id
								}
							})
						};
					}
				}
			});
			getPage(0);
			$('#branch_id').select2({
				placeholder:'Search Branch',
				allowClear:true
			});
			$('body').on('click','.paging',function(e){
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});
			$('#dateStart').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dateStart').datepicker('hide');
				getPage(0);
			});

			$('#dateEnd').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dateEnd').datepicker('hide');
				getPage(0);
			});
			$('body').on('change','#branch_id,#item_id,#member_id',function(){
				getPage(0);
			});
			$("body").on('click','.paymentDetails',function(){
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
			var timer;
			$("#txtSearch").keyup(function(){

				var searchtxt = $("#txtSearch");

				clearTimeout(timer);
				timer = setTimeout(function() {
					if(searchtxt.val()){
						searchtxt.val(searchtxt.val().trim());
					}
					getPage(0);
				}, 1000);
			});
			$('body').on('click','#btnDownload',function(){

				var search = $('#txtSearch').val();
				var dateStart = $('#dateStart').val();
				var dateEnd = $('#dateEnd').val();
				var branch_id = $('#branch_id').val();
				var member_id = $('#member_id').val();
				var item_id = $('#item_id').val();

				window.open(
					'excel_downloader_2.php?downloadName=serials&search='+search+'&branch_id='+branch_id+'&member_id='+member_id+'&item_id='+item_id+'&dateStart='+dateStart+'&dateEnd='+dateEnd,
					'_blank' //
				);

			});
			function getPage(p) {
				var search = $('#txtSearch').val();
				var dateStart = $('#dateStart').val();
				var dateEnd = $('#dateEnd').val();
				var branch_id = $('#branch_id').val();
				var member_id = $('#member_id').val();
				var item_id = $('#item_id').val();


				$.ajax({
					url: '../ajax/ajax_paging.php',
					type: 'post',
					beforeSend: function() {
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data: {
						page: p,
						functionName: 'serialsPaginate',
						cid: <?php echo $user->data()->company_id; ?>,
						search: search,
						dateStart: dateStart,
						dateEnd: dateEnd,
						branch_id: branch_id,
						member_id: member_id,
						item_id: item_id

					},
					success: function(data) {
						$('#holder').html(data);
					}
				});
			}
		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>