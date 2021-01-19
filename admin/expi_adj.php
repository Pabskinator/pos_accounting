<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('m_exp')) {
		// redirect to denied page
	//	Redirect::to(1);
	}

?>

	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Experience List</h1>

		</div>

		<div class="row">
			<div class="col-md-12">

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">
						<div class='row'>
							<div class='col-md-6'>Log</div>
							<div class='col-md-6 text-right'>
								<button id='btnAddExpi' title='Update Experience' class='btn btn-default btn-sm'><i class='fa fa-plus'></i></button>
							</div>
						</div>
					</div>

					<div class="panel-body">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon"><span class='glyphicon glyphicon-search'></span></span>
										<input type="text" id="txtSearch" class='form-control' placeholder='Search..'/>
									</div>
								</div>
							</div>
							<div class="col-md-3">

							</div>
							<div class="col-md-3">

							</div>
							<div class="col-md-3">

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
		<div class="modal-dialog ">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id='mtitle'></h4>
				</div>
				<div class="modal-body" id='mbody'>
					<strong>Member</strong>
					<input type="text" class='form-control' id='member_id' class='form-control'>
					<strong>Expi</strong>
					<input type="text" class='form-control' id='exp' class='form-control'> <br>
					<button class='btn btn-default' id='btnUpdateExpi'>Submit</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<script>

		$(document).ready(function() {
			$("#member_id").select2({
				placeholder: 'Search Member' ,
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
									text: item.lastname + ", " + item.sales_type_name,
									slug: item.lastname + ", " + item.firstname + " " + item.middlename,
									id: item.id
								}
							})
						};
					}
				}
			});
			$('body').on('click','#btnUpdateExpi',function(){
				var con = $(this);
				var exp = $('#exp').val();
				var member_id = $('#member_id').val();
				button_action.start_loading(con);
				$.ajax({
				    url:'../ajax/ajax_member_service.php',
				    type:'POST',
				    data: {functionName:'addExpiMember',exp:exp,member_id:member_id},
				    success: function(data){
				        tempToast('info',data,'Info');
					    $('#myModal').modal('hide');
					    getPage(0);
					    button_action.end_loading(con);
				    },
				    error:function(){
				        
				    }
				});
			});
			$('body').on('click','#btnAddExpi',function(){
				$('#member_id').select2('val',null);
				$('#exp').val('');
				$('#myModal').modal('show');

			});
			getPage(0);
			$('body').on('click','.paging',function(e){
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
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

			function getPage(p){
				var search  = $('#txtSearch').val();

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,functionName:'expiPaginate',cid: <?php echo $user->data()->company_id; ?>,search:search},
					success: function(data){
						$('#holder').html(data);
					}
				});
			}



		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>