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
			<h1 id='main-label'> Cash Monitoring </h1>
		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}

		?>
	<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
		<button class='btn btn-default btn-nav' data-type='1' title='Cash' >
			Cash
		</button>
		<button class='btn btn-default btn-nav' data-type='2' title='Credit Card' >
			Credit Card
		</button>
		<button class='btn btn-default btn-nav' data-type='3' title='Bank Transfer' >
			Bank Transfer
		</button>

		<button class='btn btn-default btn-nav' data-type='5' title='Cheque' >
			Cheque
		</button>
		<button class='btn btn-default btn-nav' data-type='4' title='Consumables' >
			Consumables
		</button>
	</div>

		<div class="row">
			<div class="col-md-12">

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">
						<div class="row">
							<div class="col-md-6">List</div>
							<div class="col-md-6 text-right">

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
			var type = 1;
			$('body').on('click','.btn-nav',function(){
				var con = $(this);
				$('#hiddenpage').val(0);
				type = con.attr('data-type');
				getPage(0);
			});

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
				var lbl = $('#main-label');
				var func = "";
				if(type == 1){
					func = 'cashPaginate';
					lbl.html("Cash Monitoring");
				} else if(type == 2){
					func = 'creditPaginate';
					lbl.html("Credit Card Monitoring");
				} else if(type == 3){
					lbl.html("Bank Transfer Monitoring");
					func = 'bankPaginate';
				} else if(type == 4){
					lbl.html("Consumable Monitoring");
					func = 'consumablesListPaginate2';
				} else if(type == 5){
					lbl.html("Cheque Monitoring");
					func = 'chequePaginate';
				}

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend: function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,dt1:dt1,dt2:dt2,functionName:func,cid: <?php echo $user->data()->company_id; ?>,search:search},
					success: function(data){

						$('#holder').html(data);

					}
				});
			}

			$('body').on('click','#btnDownloadExcel',function(){
				var search = $('#searchCheque').val();

				var dt1 = $('#dt1').val();
				var dt2 = $('#dt2').val();

				/*	window.open(
						'excel_downloader.php?downloadName=checkMon&search='+search+'&check_type='+check_type+'&dt1='+dt1+'&dt2='+dt2+'&member_id='+member_id+'&branch_id='+branch_id+'&terminal_id='+terminal_id+'&sales_type='+sales_type+'&with_terms='+with_terms,
						'_blank' //
					); */
			});

		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>