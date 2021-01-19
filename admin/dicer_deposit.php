<?php
	// $user have all the properties and method of the current user
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('m_terms_request')) {
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
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>Dicer deposit</h1>

		</div>


		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">List</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<input type="text" class='form-control' placeholder='Search...' id='txtSearch'>
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
					data:{functionName:'dicerDeposits',page:p,cid: <?php echo $user->data()->company_id; ?>,search:search},
					success: function(data){
						$('#holder').html(data);

					}
				});
			}
			$('body').on('click','.btnDelete',function(e){
				e.preventDefault();
				var id = $(this).attr('data-id');
				alertify.confirm("Are you sure you want to delete this record?",function(e){
					if(e){
						$.ajax({
						    url:'../ajax/ajax_sms.php',
						    type:'POST',
						    data: {functionName:'deleteDicerDeposits',id:id},
						    success: function(data){
						        tempToast('info',data,'Info');
							    getPage($('#hiddenpage').val());
						    },
						    error:function(){

						    }
						})
					}
				});
			});

		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>