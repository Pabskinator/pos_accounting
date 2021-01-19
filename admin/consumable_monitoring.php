<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('cheque_monitoring')) {
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
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Consumable Monitoring </h1>

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
							<div class="col-md-6">List</div>
							<div class="col-md-6 text-right">
								<button id='btnDownloadExcel' title='Download Excel' class='btn btn-default btn-sm'><i class='fa fa-download'></i></button>
							</div>
						</div>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon"><span class='glyphicon glyphicon-search'></span></span>
										<input type="text" id="searchCheque" class='form-control' placeholder='Search..'/>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon"><span class='glyphicon glyphicon-calendar'></span></span>
										<input type="text" id="dt1" class='form-control' placeholder='Date From'/>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon"><span class='glyphicon glyphicon-calendar'></span></span>
										<input type="text" id="dt2" class='form-control' placeholder='Date To'/>
									</div>
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
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id='mtitle'></h4>
				</div>
				<div class="modal-body" id='mbody'>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<script>

		$(document).ready(function() {

			$('#dt1').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt1').datepicker('hide');
				getPage(0);
			});
			$('#dt2').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt2').datepicker('hide');
				getPage(0);
			});

			getPage(0);

			$('body').on('click','.paging',function(){
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});

			var timer_ajax;
			$("#searchCheque").keyup(function(){

				clearTimeout(timer_ajax);
				timer_ajax = setTimeout(function() {
					getPage(0);
				}, 1000);
			});
			$("#check_type,#with_terms,#agent_id").change(function(){
				getPage(0);
			});

			function getPage(p){
				var search = $('#searchCheque').val();

				var dt1 = $('#dt1').val();
				var dt2 = $('#dt2').val();

				$.ajax({
					url: '../ajax/ajax_paging_2.php',
					type:'post',
					beforeSend: function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,dt1:dt1,dt2:dt2,functionName:'consumablePaginate',cid: <?php echo $user->data()->company_id; ?>,search:search},
					success: function(data){

						$('#holder').html(data);

					}
				});
			}

			$('body').on('click','#btnDownloadExcel',function(){
				var search = $('#searchCheque').val();

				var dt1 = $('#dt1').val();
				var dt2 = $('#dt2').val();

				window.open(
					'excel_downloader_2.php?downloadName=consumableMon&search='+search+'&dt1='+dt1+'&dt2='+dt2,
					'_blank' //
				);
			});

		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>