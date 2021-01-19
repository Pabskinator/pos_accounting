<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';



?>


	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Logs </h1>

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
						<div class="row">
							<div class="col-md-6">Logs</div>
							<div class="col-md-6 text-right"><button class='btn btn-default' id='btnRefresh'><i class='fa fa-refresh'></i></button></div>
						</div>

					</div>
					<div class="panel-body">
						<div class="row">

							<div class="col-md-4">
								<div class="form-group">
									<input type="text" autocomplete="off" class='form-control' id='search' placeholder='Enter Keyword'>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<input type='text' name="user_id" id="user_id" class='form-control'>
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
			$("#user_id").select2({
				placeholder: 'Search User',
				allowClear: true,
				minimumInputLength: 2,
				ajax: {
					url: '../ajax/ajax_json.php',
					dataType: 'json',
					type: "POST",
					quietMillis: 50,
					data: function (term) {
						return {
							q: term,
							functionName:'users'
						};
					},
					results: function (data) {
						return {
							results: $.map(data, function (item) {
								return {
									text: item.lastname + ", " + item.firstname + " " + item.middlename,
									slug: item.lastname + ", " + item.firstname + " " + item.middlename,
									id: item.id
								}
							})
						};
					}
				}
			});
			$('body').on('click','#btnRefresh',function(){
				getPage(0);
			});
			$('body').on('change','#user_id',function(){
				getPage(0);
			});

			getPage(0);
			$('body').on('click','.paging',function(){
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});
			var timer;
			$("#search").keyup(function(){

				var searchtxt = $("#search");



				clearTimeout(timer);
				timer = setTimeout(function() {

					if(searchtxt.val()){
						searchtxt.val(searchtxt.val().trim());
					}

					getPage(0);

				}, 1000);

			});
			function getPage(p){
				var search = $('#search').val();
				var user_id = $('#user_id').val();

				$('#holder').html('Loading...')
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					data:{page:p,functionName:'logList',user_id:user_id,search:search,cid: <?php echo $user->data()->company_id; ?>},
					success: function(data){

						$('#holder').html(data);

					},
					error: function(){
						alert('Something went wrong. The page will be refresh.');
						location.href='logs.php';

					}
				});
			}

		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>