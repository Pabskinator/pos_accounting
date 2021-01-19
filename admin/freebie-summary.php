<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('terminal')){
		// redirect to denied page
		Redirect::to(1);
	}

	$branch = new Branch();
	$branches = $branch->branchJSON($user->data()->company_id);


?>



	<!-- Page content -->
	<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<div class="row">
				<div class="col-md-12">
					<h1>
						<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
						Freebie Summary
					</h1>
				</div>

			</div>
		</div>
		<?php require_once 'includes/report_nav.php'; ?>
		<div class="panel panel-primary">
			<div class="panel-heading">
			</div>
			<div class="panel-body">
				<div class="btn-group hidden-xs" role="group" aria-label="..." style='margin-bottom:10px;'>
					<a class='btn btn-default btnNav' data-con='1'  title='Monthly Report'>
						Monthly
					</a>
					<a class='btn btn-default btnNav' data-con='2'  title='Yearly Report'>
						Yearly
					</a>
				</div>
				<div id="con1">
				<div class="row">
					<div class="col-md-3">
						<input type="text" class='form-control' name='dt' id='dt' placeholder='Choose Month'>
					</div>
					<div class="col-md-3">
						<select name="branch_id" class='form-control' id="branch_id">
							<option value=""></option>
							<?php
								foreach($branches as $b){
									echo "<option value='$b->id'>$b->name</option>";
								}
							?>
						</select>
					</div>
					<div class="col-md-3"><input type="button" id='btnSubmit' class='btn btn-primary' value='Submit'></div>

					<div class="col-md-3">
						<div class="text-right">

							<button class='btn btn-default btnDownload'>Download</button>

						</div>
					</div>
				</div>

				<br>
				<div id="holder"></div>
				</div> <!-- end con 1-->

				<div id="con2" style='display: none;'>
					<div class="row">
						<div class="col-md-3">
							<input type="text" class='form-control' name='dt_year' id='dt_year' placeholder='Enter year'>
						</div>
						<div class="col-md-3">
							<select name="branch_id_year" class='form-control' id="branch_id_year">
								<option value=""></option>
								<?php
									foreach($branches as $b){
										echo "<option value='$b->id'>$b->name</option>";
									}
								?>
							</select>
						</div>
						<div class="col-md-3"><input type="button" id='btnSubmitYear' class='btn btn-primary' value='Submit'></div>

						<div class="col-md-3">
							<div class="text-right">

								<button class='btn btn-default btnDownloadYear'>Download</button>

							</div>
						</div>
					</div>

					<br>
					<div id="holder2"></div>
				</div> <!-- end con 2-->

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
					<strong>Email:</strong> <input type="text" class='form-control' id='email'>
					<strong>Subject:</strong> <input type="text" class='form-control' id='subject'>
					<strong>Addtl Message:</strong> <input type="text" class='form-control' id='addtl_message'> <br>
					<div>
						<button class='btn btn-default' id='btnSubmitEmail'>Submit</button>
					</div>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->


	<script>

		$(document).ready(function(){

			$('body').on('click','.btnNav',function(){
				var con = $(this).attr('data-con');
				var con1 = $('#con1');
				var con2 = $('#con2');
				con1.hide();
				con2.hide();
				if(con == 1){
					con1.fadeIn(300);
				} else if (con == 2){
					con2.fadeIn(300);
					getSummaryYear(0);
				}
			});


			$('#branch_id').select2({
				allowClear:true,
				placeholder:'Select Branch'
			});
			$('#branch_id_year').select2({
				allowClear:true,
				placeholder:'Select Branch'
			});

			$('#dt').datepicker({
				autoclose:true,
				format: "mm-yyyy",
				viewMode: "months",
				minViewMode: "months"
			}).on('changeDate', function(ev){
				$('#dt').datepicker('hide');
			});

			$('#dt_year').datepicker({
				autoclose:true,
				format: " yyyy",
				viewMode: "years",
				minViewMode: "years"
			}).on('changeDate', function(ev){
				$('#dt_year').datepicker('hide');
			});

			$('body').on('click','#btnSubmit',function(){
				getSummary(0);
			});

			$('body').on('click','.btnDownload',function(){
				getSummary(1);
			});

			$('body').on('click','#btnSubmitYear',function(){
				getSummaryYear(0);
			});

			$('body').on('click','.btnDownloadYear',function(){
				getSummaryYear(1);
			});



			getSummary(0);

			function getSummary(dl){

				var dt = $('#dt').val();
				var branch_id = $('#branch_id').val();

				if(dl == 1){

					window.open(
						'../ajax/ajax_sales_query.php?functionName=getSalesTypeSummary&dl='+dl+'&branch_id='+branch_id+'&dt='+dt,
						'_blank' //
					);

				} else {

					$.ajax({
						url:'../ajax/ajax_sales_query.php',
						type:'POST',
						beforeSend:function(){
							$('#holder').html('Loading records...');
						},
						data: {functionName:'freebieSummary',dl:dl,dt:dt, branch_id:branch_id},
						success: function(data){
							$('#holder').html(data);
						},
						error:function(){

						}
					});

				}
			}
			function getSummaryYear(dl){

				var dt = $('#dt_year').val();
				var branch_id = $('#branch_id_year').val();

				if(dl == 1){

					window.open(
						'../ajax/ajax_sales_query.php?functionName=getSalesTypeSummary&dl='+dl+'&branch_id='+branch_id+'&dt='+dt,
						'_blank' //
					);

				} else {

					$.ajax({
						url:'../ajax/ajax_sales_query.php',
						type:'POST',
						beforeSend:function(){
							$('#holder2').html('Loading records...');
						},
						data: {functionName:'freebieSummaryYear',dl:dl,dt:dt, branch_id:branch_id},
						success: function(data){
							$('#holder2').html(data);
						},
						error:function(){

						}
					});

				}
			}

		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>