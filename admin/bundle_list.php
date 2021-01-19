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
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Bundles </h1>

			</div> <?php
				// get flash message if add or edited successfully
				if(Session::exists('flash')) {

					echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
				}
			?>
			<div style='margin-bottom: 5px;'>
				<?php if($user->hasPermission('bundles_m')){
					?>
					<a class='btn btn-default' href="addbundles.php">Add Bundles</a>
					<?php
				}?>

			</div>
			<div class="row">
				<div class="col-md-12">

					<div class="panel panel-primary">
						<!-- Default panel contents -->
						<div class="panel-heading">
							<div class='row'>
								<div class='col-md-6'>Bundles</div>
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


	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id='mtitle'></h4>
				</div>
				<div class="modal-body" id='mbody'>
					<input type="hidden" id='edit_id'>

					<p id='edit_name'></p>

					<div class="form-group">
						<input type="text" class='form-control' value='' id='edit_qty'>
					</div>
					<div class="form-group">
						<button class='btn btn-default' id='btnSubmitEdit'><i class='fa fa-save'></i> SAVE</button>
					</div>
				</div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<script>

		$(document).ready(function() {
			$('body').on('click', '.btnEdit', function() {
				var con = $(this);
				var row = con.parents('tr');
				var id = row.attr('data-id');
				var qty = row.children().eq(2).text();
				var item_name = row.attr('data-item_name');
				$('#edit_name').html(item_name);
				$('#edit_id').val(id);
				$('#edit_qty').val(qty);
				$('#myModal').modal('show');
			});
			$('body').on('click', '.btnDelete', function() {
				var con = $(this);
				var row = con.parents('tr');
				var id = row.attr('data-id');
				alertify.confirm("Are you sure you want to delete this record?", function(e) {
					if(e) {
						$.ajax({
							url: '../ajax/ajax_product.php',
							type: 'POST',
							data: {functionName: 'deleteBundleChild', id: id},
							success: function(data) {
								getPage($('#hiddenpage').val());
							},
							error: function() {

							}
						})
					} else {

					}
				});
			});
			$('body').on('click', '#btnSubmitEdit', function() {
				var id = $('#edit_id').val();
				var qty = $('#edit_qty').val();
				$.ajax({
					url: '../ajax/ajax_product.php',
					type: 'POST',
					data: {functionName: 'saveEditBundleQty', id: id, qty, qty},
					success: function(data) {
						alertify.alert(data, function() {
							getPage($('#hiddenpage').val());
						});
					},
					error: function() {

					}
				})
			});
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
						page: p,txtSearch:txtSearch, functionName: 'bundleList', cid: '<?php echo $user->data()->company_id; ?>'
					}, success: function(data) {
						$('#holder').html(data);
					}
				});
			}
		});


	</script><?php require_once '../includes/admin/page_tail2.php'; ?>