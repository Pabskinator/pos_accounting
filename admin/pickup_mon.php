<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('user')){
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
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				For Pick up
			</h1>
		</div>
		<?php
			if(Session::exists('flash')){
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('flash')."</div>";
			}
		?>
		<div class="row">
			<div class="col-md-12">

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">To pickup</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<input type="text" placeholder='Search' class='form-control' id='search'>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
								<select class='form-control' id='type'>
									<option value=""></option>
									<option value="1">Pending</option>
									<option value="2">Processed</option>
									<option value="3">Cancelled</option>
								</select>
								</div>
							</div>
						</div>
						<hr>
						<input type="hidden" id="hiddenpage" />
						<div id="holder"></div>
					</div>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<script>
		$(document).ready(function(){
			$('#type').select2({
				placeholder:'Search type',
				allowClear:true
			});
			$('#type').change(function(){
				var search = $('#search').val();
				var type = $('#type').val();
				getPage(0,search,type);

			});
			$('body').on('click','.btnPickUpCancel',function(){
				var id = $(this).attr('data-id');
				alertify.confirm("Are you sure you want to cancel this request?", function (asc) {
					if(asc){
						$.ajax({
						    url:'../ajax/ajax_query2.php',
						    type:'POST',
						    data: {functionName:'cancelPickupRequest',id:id},
						    success: function(data){
						        alertify.alert(data);
							    getPage(0);
						    },
						    error:function(){

						    }
						})
					}
				});
			});
			$('body').on('click','.btnPickUp',function(){
				var id = $(this).attr('data-id');
					alertify.confirm("Are you sure you want to process this request?", function (asc) {
					if (asc) {
						// update

						$.ajax({
						    url:'../ajax/ajax_query2.php',
						    type:'post',
						    data: {functionName:'updatePickup',id:id},
						    success: function(data){
							    if(data == 1){
								    alertify.success("<h3>Record is updated.</h3>");
								    location.href='pickup_mon.php';
							    } else if(data == 0) {
								    alertify.error("<h3>Unable to process this record.</h3>");
								    location.href='pickup_mon.php';
							    }

						    },
						    error:function(){
							    alertify.error("<h3>Unable to process this record</h3>");
							    location.href='pickup_mon.php';
						    }
						})

					} else {

					}
				}).set('modal',false);
			});

			getPage(0,'');
			$('body').on('keyup','#search',function(){
				var search = $('#search').val();
				var type = $('#type').val();
				getPage(0,search);
			});
			$('body').on('click', '.paging', function(e) {
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				var search = $('#search').val();
				var type = $('#type').val();
				getPage(page, search,type);
			});
			function getPage(p, search,type) {

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type: 'post',
					beforeSend: function() {
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data: {
						page: p,
						functionName: 'pickupPaginate',
						cid: <?php echo $user->data()->company_id; ?>,
						search: search,
						type:type
					},
					success: function(data) {

						$('#holder').html(data);

					}
				});
			}
		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>