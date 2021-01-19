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
			<h1> <span id="menu-toggle" class='glyphicon glyphicon-list'></span> Senior Discount List </h1>
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
					<div class="panel-heading">Senior Discount List</div>
					<div class="panel-body">

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

			$('body').on('click','.paging',function(){

				var page = $(this).attr('page');

				$('#hiddenpage').val(page);

				getPage(page);

			});

			function getPage(p){

				var search = $('#search').val();

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html('Loading...');
					},
					data:{page:p,search:search,functionName:'seniorPaginate',cid: <?php echo $user->data()->company_id; ?>},
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


		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>