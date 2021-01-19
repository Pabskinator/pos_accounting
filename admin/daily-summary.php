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
						Daily Summary
					</h1>
				</div>

			</div>
		</div>
		<?php require_once 'includes/report_nav.php'; ?>
		<div class="panel panel-primary">
			<div class="panel-heading">
				</div>
			<div class="panel-body">
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
							<button class='btn btn-default btnPrint'>Print</button>
							<button class='btn btn-default btnDownload'>Download</button>
							<button class='btn btn-default btnEmail'>Email</button>
						</div>
					</div>
				</div>

				<br>
				<div id="holder"></div>

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
			$('body').on('click','.btnEmail',function(){
				$('#email').val('');
				$('#subject').val('');
				$('#addtl_message').val('');
				$('#myModal').modal('show');
			});
			$('body').on('click','#btnSubmitEmail',function(){
				var email = $('#email').val();
				var subject = $('#subject').val();
				var addtl_message = $('#addtl_message').val();
				var body = $('#holder').html();
				var date = $('#dt').val();
				var branch_name = $('#branch_id').select2('data').text;
				var con = $(this);
				button_action.start_loading(con);

				$.ajax({
					url:'../ajax/ajax_sales_query.php',
					type:'POST',
					data: {functionName:'emailReport', dt:date,branch_name:branch_name,email:email,addtl_message:addtl_message,subject:subject,body:body},
					success: function(data){
						tempToast('info',data,'Info');
						button_action.end_loading(con);
						$('#myModal').modal('hide');
					},
					error:function(){
						button_action.end_loading(con);
					}
				});

			});
			$('#branch_id').select2({
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
			$('body').on('click','#btnSubmit',function(){
				getSummary(0);
			});
			$('body').on('click','.btnDownload',function(){
				getSummary(1);
			});
			$('body').on('click','.btnPrint',function(){
				var html = $('#print_me').html();
				printWithStyle(html);
			});
			function printWithStyle(data){
				var mywindow = window.open('', 'new div', '');
				mywindow.document.write('<html><head><title></title><style></style>');
				mywindow.document.write('<link rel="stylesheet" href="../css/bootstrap.css" type="text/css" />');
				mywindow.document.write('</head><body style="padding:0;margin:0;;font-family: Arial, Helvetica, sans-serif;">');
				mywindow.document.write(data);
				mywindow.document.write('</body></html>');
				setTimeout(function() {
					mywindow.print();
					mywindow.close();
				}, 300);
				return true;

			}
			getSummary(0);
			function getSummary(dl){

				var dt = $('#dt').val();
				var branch_id = $('#branch_id').val();

				if(dl == 1){

					window.open(
						'../ajax/ajax_sales_query.php?functionName=getDaily&dl='+dl+'&branch_id='+branch_id+'&dt='+dt,
						'_blank' //
					);

				} else {

					$.ajax({
						url:'../ajax/ajax_sales_query.php',
						type:'POST',
						beforeSend:function(){
							$('#holder').html('Loading records...');
						},
						data: {functionName:'getDaily',dl:dl,dt:dt, branch_id:branch_id,},
						success: function(data){
							$('#holder').html(data);
						},
						error:function(){

						}
					});

				}
			}

		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>