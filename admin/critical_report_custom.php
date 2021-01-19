<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';

	if(!$user->hasPermission('inv_forecast')) {
		// redirect to denied page
		Redirect::to(1);
	}

	$n = date('n');

	if($n > 6){
		$m = "01";
		$y = date('Y');
	} else {
		$m = "07";
		$y = date('Y') - 1;
	}

	$dt1 = date("$m/01/$y");
	$dt2 = strtotime($dt1 . "6 months -1 min");
	$dt2 = date('m/d/Y',$dt2);

	$branch = new Branch();
	$branches = $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));


?>


	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>Critical Order</h1>

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
								<button class='btn btn-default btn-sm' id='btnDownload'><i class=' fa fa-download'></i></button>
							</div>
						</div>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<select class='form-control' id='branch_id'>

										<?php
											if($branches){
												foreach($branches as $b){
													echo "<option value='$b->id'>$b->name</option>";
												}
											}
										?>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' value='<?php echo $dt1; ?>' placeholder='Date From' id='dt1' >
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' value='<?php echo $dt2; ?>' placeholder='Date To' id='dt2' >
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<button class='btn btn-default' id='btnSubmit'>Submit</button>
								</div>
							</div>
							<div class="col-md-3"></div>
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

			$('body').on('click','#btnDownload',function(){
				var dt1 = $('#dt1').val();
				var dt2 = $('#dt2').val();
				var branch_id = $('#branch_id').val();
				window.open(
					'excel_downloader_2.php?downloadName=criticalOrderCustom&search='+branch_id+'&dt1='+dt1+'&dt2='+dt2,
					'_blank' //
				);

			});

			getCritical();
			$('#branch_id').select2({placeholder: 'Search Branch' ,allowClear: true});
			$('#dt1').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt1').datepicker('hide');
			});
			
			$('#dt2').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt2').datepicker('hide');
			});
			$('body').on('click','#btnSubmit',function(){
				getCritical();
			});

			function getCritical(){

				var dt1 = $('#dt1').val();
				var dt2 = $('#dt2').val();
				var branch_id = $('#branch_id').val();

				$.ajax({
					url:'../ajax/ajax_inventory.php',
					type:'POST',
					data: {functionName:'criticalOrderCustom',dt1:dt1,dt2:dt2,branch_id:branch_id},
					success: function(data){
						$('#holder').html(data);
					},
					error:function(){

					}
				});
				
			}


		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>