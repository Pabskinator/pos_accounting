<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('ship_v')){
		// redirect to denied page
		Redirect::to(1);
	}

?>



	<!-- Page content -->
	<div id="page-content-wrapper">




	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1 class='hidden-xs'>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Manage Shipping Companies
			</h1>
			<h4 class='visible-xs'><strong>Shipping Companies</strong></h4>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')){
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('flash')."</div>";
			}
		?>
		<div class="row">
			<div class="col-md-12">
				<?php if($user->hasPermission('ship_m')) { ?>
					<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
						<a class='btn btn-default' href='addcompanyshipping.php' title='Add Shipping Company'>
							<span class='glyphicon glyphicon-plus'></span>
							<span class='hidden-xs'>Add Shipping Company</span>
						</a>
					</div>
				<?php } ?>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Shipping Companies</div>
					<div class="panel-body">

								<input type="hidden" id="hiddenpage" />
								<div id="holder"></div>

					</div>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function(){
			$(".deleteShipping").click(function(){
				if(confirm("Are you sure you want to delete this record? \n ")){
					id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php',{id:id,table:'shipping_companies'},function(data){
						if(data == "true"){
							location.reload();
						}
					});
				}
			});
		// start pagination
			getPage(0);
			function getPage(p){
				var search = $('#search').val();
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,functionName:'shippingCompany',cid: <?php echo $user->data()->company_id; ?>},
					success: function(data){
						$('#holder').html(data);
					}
				});
			}
			$('body').on('click','.paging',function(e){
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});

			// end pagination
		});




	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>