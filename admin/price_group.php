<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('price_group')){
		// redirect to denied page
		Redirect::to(1);
	}

	// get all branch base on company
	$price_group = new Price_group();
	$price_groups = $price_group->get_active('price_groups',array('1' ,'=','1'));

?>



	<!-- Page content -->
	<div id="page-content-wrapper">



	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Price Group
			</h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')){
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('flash')."</div><br>";
			}
		?>
		<div class="row">
			<div class="col-md-12">

				<?php include 'includes/price_group_nav.php'; ?>


				<?php
					if ($price_groups){
				?>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">
						<div class="row">
							<div class="col-md-6">	Price Group</div>
							<div class="col-md-6 text-right">
								<button class='btn btn-default btn-sm' id='btnDownload'><span class='fa fa-download'></span></button>
							</div>
						</div>

					</div>
					<div class="panel-body">
						<div id="no-more-tables">
							<table class='table' id='tblbrands'>
								<thead>
								<tr>
									<TH>Name</TH>
									<TH>Data Created</TH>
									<TH>Actions</TH>
								</tr>
								</thead>
								<tbody>
								<?php
									foreach($price_groups as $b){
										?>
										<tr>
											<td data-title='Name'><?php echo escape($b->name); ?></td>
											<td data-title='Created'><?php echo escape(date('m/d/Y H:i:s A',$b->created)); ?></td>
											<td data-title='Action'>
												<a class='btn btn-primary' href='add_price_group.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt',$b->id);?>' title='Edit'><span class='glyphicon glyphicon-pencil'></span></a>
												<a href='#' class='btn btn-primary deletePriceGroup' id="<?php echo Encryption::encrypt_decrypt('encrypt',$b->id);?>" title='Delete'><span class='glyphicon glyphicon-remove'></span></a>
											</td>

										</tr>
										<?php
									}
								?>
								</tbody>
							</table>
						</div>
					</div>
					<?php
						} else {
						?>
						<div class='alert alert-info'>There is no current item at the moment.</div>
						<?php
					}
					?>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function(){
			$(".deletePriceGroup").click(function(){
				if(confirm("Are you sure you want to delete this record? \n ")){
					var id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php',{id:id,table:'price_groups'},function(data){
						if(data == "true"){
							location.reload();
						}
					});
				}
			});
			$('body').on('click','#btnDownload',function(){
				var price_group_id = $('#price_group_id').val();
				var search_item = $('#search_item').val();
				var limit_by = $('#limit_by').val();
				var dt_from = $('#dt_from').val();
				var dt_to = $('#dt_to').val();
				window.open(
					'../ajax/ajax_reports.php?functionName=getPriceMatrix',
					'_blank' // <- This is what makes it open in a new window.
				);
			});

		});


		$('#tblbrands').dataTable({
			iDisplayLength: 50
		});

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>