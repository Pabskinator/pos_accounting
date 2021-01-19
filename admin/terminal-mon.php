<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('terminal')) {
		// redirect to denied page
		Redirect::to(1);
	}
	$branch = new Branch();
	$branches = $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));

?>
<?php if($branches){
	$terminaloption = '';
	foreach($branches as $b){
		$terminal = new Terminal();
		$terminals = $terminal->get_active('terminals',array('branch_id' ,'=',$b->id));

		if(!$terminals){
			continue;
		}

		foreach($terminals as $t) {
			$terminaloption .= "<option data-t_amount='$t->t_amount' data-t_amount_cc='$t->t_amount_cc' data-t_amount_ch='$t->t_amount_ch' data-t_amount_bt='$t->t_amount_bt' value='$t->id'>$b->name $t->name</option>";
		}
	}
}
?>


	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>Manage Terminals</h1>

			</div>
			<?php
				// get flash message if add or edited successfully
				if(Session::exists('flash')) {
					echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
				}
			?>
			<div class="row">
				<div class="col-md-12">
					<?php include 'includes/terminal_nav.php'; ?>
					<br><br>
					<div class="panel panel-primary">
						<!-- Default panel contents -->
						<div class="panel-heading">
							<div class="row">
								<div class="col-md-6">Terminal Monitoring</div>
								<div class="col-md-6 text-right">
									<button id='btnDownloadExcel' class='btn btn-default btn-sm'><i class='fa fa-download'></i></button>
								</div>
							</div>

						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" id='dt1' placeholder='Date From' class='form-control'>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" id='dt2' placeholder='Date To' class='form-control'>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<select class='form-control' name="terminal_id" id="terminal_id">
											<option value=""></option>
											<?php echo $terminaloption; ?>
										</select>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<select class='form-control' name="p_type" id="p_type">
											<option value=""></option>
											<option value="1">Cash</option>
											<option value="2">Credit Card</option>
											<option value="3">Cheque</option>
											<option value="4">Bank Transfer</option>
										</select>
									</div>
								</div>

							</div>
					<br>
							<input type="hidden" id="hiddenpage" />
							<div id="holder"></div>
						</div>



					</div>

				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->


	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style='width:95%'>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id='mtitle'>&nbsp;</h4>

				</div>
				<div class="modal-body" id='mbody'>
					<p id="amountlabel"></p>
					<div class="row">

						<div class="col-md-4">
							<div class="form-group">
								<select name="t_payment_type" id="t_payment_type" class='form-control'>
									<option value=""></option>
									<option value="1">Cash</option>
									<option value="2">Credit Card</option>
									<option value="3">Cheque</option>
									<option value="4">Bank Transfer</option>
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<select name="t_terminal" id="t_terminal" class='form-control'>
									<option value=""></option>
									<?php echo $terminaloption; ?>
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<select name="d_type" id="d_type" class='form-control'>
									<option value=""></option>
									<option value="1">Add/Replenish</option>
									<option value="2">Deposit</option>

								</select>
							</div>
						</div>

					</div>

					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<input type="text" placeholder='Amount' id='t_amount'  class='form-control' />
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<input type="text" placeholder='Remarks (Optional)' id='t_remarks'  class='form-control' />
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<button id='t_submit'  class='btn btn-default'> <span class='glyphicon glyphicon-save'></span> SUBMIT
								</button>
							</div>
						</div>

					</div>

				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<script>

		$(document).ready(function() {

			//aqua
			$('#terminal_id').select2({
				placeholder :'Search Terminal',
				allowClear: true
			});
			$('#p_type').select2({
				placeholder:'Search Type',
				allowClear: true
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
			getPage(0,'','');
			$('#p_type,#terminal_id').change(function(){
				var terminal_id = $('#terminal_id').val();
				var type = $('#p_type').val();
				getPage(0,type,terminal_id);
			});
			$('body').on('click','.paging',function(e){
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				var terminal_id = $('#terminal_id').val();
				var type = $('#p_type').val();
				getPage(page,type,terminal_id);
			});
			function getPage(p,type,terminal_id){
				var dt1 = $('#dt1').val();
				var dt2 = $('#dt2').val();

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,dt1:dt1,dt2:dt2,type:type,functionName:'terminalMoneyMonitoring',cid: <?php echo $user->data()->company_id; ?>,terminal_id:terminal_id},
					success: function(data){

						$('#holder').html(data);

					}
				});
			}

			$('body').on('change','#branch_id',function(){
				branchTerminal($('#branch_id').val(),2);
				var search = $('#searchSales').val();
				getPage(0,search,'');
			});
			$('body').on('change','#terminals',function(){
				var search = $('#searchSales').val();
				getPage(0,search,'');
			});
			$("#t_terminal").select2({
				placeholder: 'Choose Terminal',
				allowClear: true
			});

			$("#d_type").select2({
				placeholder: 'Choose Action',
				allowClear: true
			});

			$("#t_payment_type").select2({
				placeholder: 'Choose type',
				allowClear: true
			});

			$('#btnDep').click(function(){
				$('#myModal').modal('show');
			});

			$('#t_submit').click(function(){
				var terminal_id = $('#t_terminal').val();
				var type = $('#d_type').val();
				var amount = replaceAll($('#t_amount').val(),',','');
				var remarks =$('#t_remarks').val();
				var payment_type =$('#t_payment_type').val();
				var btncon = $(this);
				var btnoldval = btncon.html();
				btncon.html('Loading...');
				if(!terminal_id || !type || !amount ){
					alertify.alert('Please Complete the Form');
					btncon.html(btnoldval);
					return;
				}
				if(isNaN(amount) || parseInt(amount) < 1){
					alertify.alert('Invalid amount.');
					btncon.html(btnoldval);
					return;
				}

				$.ajax({
					url:'../ajax/ajax_query.php',
					type:'post',
					data: {payment_type:payment_type,terminal_id:terminal_id,type:type,amount:amount,remarks:remarks,functionName:'updateTerminalAmountOnHand'},
					success: function(data){
						alertify.alert(data,function(){
							location.href='terminal.php';
						});
					},
					error:function(){


					}
				});
			});

			$('body').on('click','#btnDownloadExcel',function(){

				var dt1 = $('#dt1').val();
				var dt2 = $('#dt2').val();
				var terminal_id = $('#terminal_id').val();
				var type = $('#p_type').val();


				window.open(
					'excel_downloader.php?downloadName=terminalMonitoring&terminal_id='+terminal_id+"&type="+type+"&dt1="+dt1+"&dt2="+dt2,
					'_blank' //
				);
			});

		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>