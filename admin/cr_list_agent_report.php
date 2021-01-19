<?php
	// $user have all the properties and method of the current user
	require_once '../includes/admin/page_head2.php';

?>
	<!-- Page content -->
	<div id="page-content-wrapper">
		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div id="con_collection" >
				<h3>CR By Store Type</h3>
				<div style='margin-bottom: 10px;'>
					<a class='btn btn-default' href="accounting.php">Back To Collection Report</a>
				</div>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">List</div>
					<div class="panel-body"  >
						<div class="row">

							</div>
							<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' autocomplete="off" placeholder='Date From' id='dt_from'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' autocomplete="off" placeholder='Date To'  id='dt_to'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<button class='btn btn-default' id='btnSubmit'>Submit</button>
								</div>
							</div>
						</div>
						<input type="hidden" id="hiddenpage" />
						<div id="holder">Filter type first.</div>
					</div>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->

	<script>
		$(function() {
			$("#agent_id").select2({
				placeholder: 'Search Agent',
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
			$('#dt_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_from').datepicker('hide');
			});
			$('#dt_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_to').datepicker('hide');
			});

			getPage(0);

			$('body').on('click','.paging',function(e){
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});

			var ajax_running = false;

			$('body').on('click','#btnSubmit',function(){

				getPage(0);

			});


			function getPage(p){

				if(ajax_running) return;

				ajax_running = true;

				var sales_type_id = $('#sales_type_id').val();
				var date_from = $('#dt_from').val();
				var date_to = $('#dt_to').val();
				var agent_id = $('#agent_id').val();


				$.ajax({
					url: '../ajax/ajax_accounting.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,functionName:'agentCRList',agent_id:agent_id,date_from:date_from,date_to:date_to,sales_type_id:sales_type_id},
					success: function(data){
						$('#holder').html(data);
						ajax_running= false;
					}
				});

			}

		});
	</script><?php require_once '../includes/admin/page_tail2.php'; ?>