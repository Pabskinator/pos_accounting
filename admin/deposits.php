<?php
	// $user have all the properties and method of the current user
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('m_terms_request')) {
		Redirect::to(1);
	}

?>



	<!-- Page content -->
	<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1><span id="menu-toggle" class='glyphicon glyphicon-list'></span> <?php echo MEMBER_LABEL; ?> Deposits</h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}
		?>
		<div class="row">
			<div class="col-md-12 text-right">
				<a class='btn btn-default' href="deposit_cr.php">Collection Report</a>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading"><?php echo MEMBER_LABEL; ?> Deposits</div>
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
			var timer;

			$("#txtSearch").keyup(function() {
				var searchtxt = $("#txtSearch");

				clearTimeout(timer);
				timer = setTimeout(function() {
					if(searchtxt.val()){
						searchtxt.val(searchtxt.val().trim());
					}
					getPage(0);
				}, 1000);

			});

			$('body').on('click','.paging',function(){

				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);

			});
			function getPage(p){
				var search = $('#txtSearch').val();

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html('Loading...');
					},
					data:{page:p,search:search,functionName:'memberDeposits',cid: <?php echo $user->data()->company_id; ?>},
					success: function(data){
						$('#holder').html(data);

					},
					error: function(){
						alert('Something went wrong. The page will be refresh.');
						location.href='station.php';
						$('.loading').hide();
					}
				});
			}

			$('body').on('click','.btnDelete',function(){
				var con = $(this);
				var id = con.attr('data-id');
				alertify.confirm("Are you sure you want to delete this record?",function(e){
					if(e){
						$.ajax({
						    url:'../ajax/ajax_deletepermanent.php',
						    type:'POST',
						    data: {table:'user_credits',id:id},
						    success: function(data){
							    getPage(0);

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