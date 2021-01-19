<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('doc_util') && !$user->hasPermission('lock_doc_util')) {
		// redirect to denied page
		Redirect::to(1);
	}

?>


	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<?php include 'includes/sales_nav.php'; ?>
		<div class="content-header">
			<div class="row">
				<div class="col-md-12">
					<h1>
						<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
						Document Utilities
					</h1>
				</div>

			</div>
		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}
		?>


		<div class="row">
			<div class="col-md-12">

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Doc Utilities</div>
					<div class="panel-body">
						<div class="row">

							<div class="col-md-3">
								<div class="input-group">
									<span class="input-group-addon"><span class='glyphicon glyphicon-search'></span></span>
									<input type="text" id="search" class='form-control' placeholder='Search..'/>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="hidden" class='form-control' id='branch_id'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' placeholder='Date From' id='dt_from'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' placeholder='Date To' id='dt_to'>
								</div>
							</div>
						</div>
						<input type="hidden" id="hiddenpage" />
						<div id="holder"></div>
					</div>
				</div>
			</div>
		</div>
		<div id="test"></div>
	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function() {

			getPage(0);
			$('#branch_id').select2({
				placeholder: 'Branch',
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
							functionName:'branches'
						};
					},
					results: function (data) {
						return {
							results: $.map(data, function (item) {
								return {
									text: item.name ,
									slug: item.name ,
									id: item.id
								}
							})
						};
					}
				}
			});
			$('body').on('click','.paging',function(){
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});
			$('body').on('change','#branch_id',function(){
				getPage(0);
			});

			var timer;
			$("#search").keyup(function(){

				var searchtxt = $("#search");

				clearTimeout(timer);
				timer = setTimeout(function() {
					if(searchtxt.val()){
						searchtxt.val(searchtxt.val().trim());
					}
					getPage(0);
				}, 1000);
			});

			$('#dt_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_from').datepicker('hide');
				if($('#dt_from').val() && $('#dt_to').val() ){
					getPage(0);
				}
			});

			$('#dt_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_to').datepicker('hide');
				if($('#dt_from').val() && $('#dt_to').val() ){
					getPage(0);
				}

			});
			function getPage(p){

				var search = $('#search').val();
				var dt_from = $('#dt_from').val();
				var dt_to = $('#dt_to').val();
				var branch_id = $('#branch_id').val();

				$('#holder').html('Loading...');

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					data:{page:p,functionName:'allDocList',branch_id:branch_id,dt_from:dt_from,dt_to:dt_to,cid: <?php echo $user->data()->company_id; ?>,search:search},
					success: function(data){

						$('#holder').html(data);

					},
					error: function(){
						alert('Something went wrong. The page will be refresh.');

					}
				});
			}
			$('body').on('change','.invClass',function(){
				var row = $(this).parents('tr');
				var payment_id = $(this).attr('data-pid');
				var arrchk = "";
				$('input[name="chk'+payment_id+'"]').each(function(){
					if($(this).is(':checked')){
						arrchk += $(this).val() +",";
					}
				});
				$.ajax({
					url:'../ajax/ajax_query2.php',
					type:'POST',
					data: {functionName:'updateDocList',payment_id:payment_id,arrchk:arrchk},
					success: function(data){

					},
					error:function(){

					}
				})
			});
			$('body').on('click','.btnFinalize',function(){
				var id = $(this).attr('data-pid');
				$.ajax({
					url:'../ajax/ajax_query2.php',
					type:'POST',
					data: {functionName:'finalDocList',payment_id:id},
					success: function(data){
						alertify.alert(data,function(){
							location.href='doc-utilities.php';
						});

					},
					error:function(){

					}
				})
			});
		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>