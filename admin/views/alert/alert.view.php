
<!-- Page content -->
<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Alerts </h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}
		?>

		<div class="row">
			<div class="col-md-12">
				<?php include 'includes/product_nav.php'; ?>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Alert</div>
					<div class="panel-body">
						<div class="row">

							<div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon"><span class='glyphicon glyphicon-search'></span></span>
									<input type="text" id="searchItem" class='form-control' placeholder='Search..' />
								</div>
							</div>
							<div class="col-md-4">

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
			$('body').on('click', '.paging', function() {
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				var search = $('#searchItem').val();
				getPage(page, search);
			});
			$("#searchItem").keyup(function() {
				var search = $('#searchItem').val();
				getPage(0, search);
			});
			function getPage(p, search) {
				$('.loading').show();
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type: 'post',
					data: {
						page: p,
						functionName: 'alertList',
						cid: <?php echo $user->data()->company_id; ?>,
						search: search
					},
					success: function(data) {
						$('#holder').empty();
						$('#holder').append(data);
						$('.loading').hide();
					},
					error: function() {
						alert('Something went wrong. The page will be refresh.');
						location.reload();
						$('.loading').hide();
					}
				});
			}
			$('body').on('click', '.deleteAlert', function() {
				if(confirm("Are you sure you want to delete this record?")) {
					id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php', {id: id, table: 'item_alert'}, function(data) {
						if(data == "true") {
							location.reload();
						}
					});
				}
			});
		});


	</script>