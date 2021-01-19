<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('inbox')) {
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
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Contact form </h1>

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
					<div class="panel-heading">
						<div class='row'>
							<div class='col-md-6'>List</div>
							<div class='col-md-6 text-right'>
							</div>
						</div>
					</div>
					<div class="panel-body">

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
			$("#search").keyup(function(){
				getPage(0);
			});
			function getPage(p){

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,functionName:'contactFormMessages',cid: <?php echo $user->data()->company_id; ?>},
					success: function(data){
						$('#holder').html(data);
					}
				});
			}


		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>