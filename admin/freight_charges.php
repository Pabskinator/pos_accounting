<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head
	require_once '../includes/admin/page_head2.php';
	if(false) {
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
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Freight Charges
			</h1>
		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading"></div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-4">
								<input type="text" class='form-control' id='txtSearch' placeholder='Search...'>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<select class='form-control' name="status" id="status">
										<option value="0">Pending</option>
										<option value="1">Paid</option>
									</select>
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
	<script>

		$(document).ready(function() {

			getPage(0);

			$('body').on('click','.paging',function(e){
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});

			$('body').on('change','#status',function(){
				getPage(0);
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
				var status = $('#status').val();
				var search = $('#txtSearch').val();
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,functionName:'freight_charges',search:search,status:status,cid: <?php echo $user->data()->company_id; ?>},
					success: function(data){

						$('#holder').html(data);

					}
				});
			}
			$('body').on('click','.btnPay',function(){
				var con = $(this);
				alertify.confirm("Are you sure you want to continue this action?" , function(e){
					if(e){
						var id = con.attr('data-id');
						$.ajax({
							url:'../ajax/ajax_sales_query.php',
							type:'POST',
							data: {functionName:'freightPaid',id:id},
							success: function(data){
								alertify.alert(data);
								getPage($('#hiddenpage').val());
							},
							error:function(){

							}
						});
					}
				});


			});

		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>