<?php
	// $user have all the properties and method of the current user
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
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Not received from supplier </h1>

			</div> <?php
				// get flash message if add or edited successfully
				if(Session::exists('flash')) {

					echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
				}
			?>

			<div class="row">
				<div class="col-md-12">

					<div class="panel panel-primary">
						<!-- Default panel contents -->
						<div class="panel-heading">
							<div class='row'>
								<div class='col-md-6'></div>
								<div class='col-md-6 text-right'></div>
							</div>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-3">
									<input type="text" class='form-control' id='txtSearch' placeholder='Search Item'>
								</div>
							</div>
							<input value='0' type="hidden" id="hiddenpage" />

							<div id="holder"></div>
						</div>

					</div>

				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->


	<script>

		$(document).ready(function() {

			getPage(0);
			$('body').on('click', '.paging', function(e) {
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});

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

			function getPage(p) {
				var txtSearch = $('#txtSearch').val();
				$.ajax({
					url: '../ajax/ajax_paging_2.php', type: 'post', beforeSend: function() {
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					}, data: {
						page: p,txtSearch:txtSearch, functionName: 'notReceived', cid: '<?php echo $user->data()->company_id; ?>'
					}, success: function(data) {
						$('#holder').html(data);
					}
				});
			}

		});


	</script><?php require_once '../includes/admin/page_tail2.php'; ?>