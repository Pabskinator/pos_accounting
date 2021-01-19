<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('cnp') && !$user->hasPermission('daina') && !$user->hasPermission('mastra') && !$user->hasPermission('service_sales') && !$user->hasPermission('assembly_sales')){
		// redirect to denied page
		Redirect::to(1);
	}


	if($user->hasPermission('cnp')){
		$default = 'CNP';
	} else if($user->hasPermission('daina')){
		$default = 'SODAINA';
	} else if($user->hasPermission('mastra')){
		$default = 'MASTRA';
	} else if($user->hasPermission('service_sales')){
		$default = 'SERVICE';
	} else if($user->hasPermission('assembly_sales')){
		$default = 'A';
	} else if($user->hasPermission('tedela')){
		$default = 'AQUA TEDELA';
	} else if($user->hasPermission('black_samurai')){
		$default = 'Black Samurai';
	} else if($user->hasPermission('ebara')){
		$default = 'Ebara';
	}

	$branch = new Branch();

	$branches = $branch->branchJSON($user->data()->company_id);


?>

	<!-- Page content -->
	<div id="page-content-wrapper">



	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				<span class='txtCurrent'></span> Sales Report
			</h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')){
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('flash')."</div>";
			}
		?>
		<!-- Keep all page content within the page-content inset div! -->
		<div class="btn-group hidden-xs" role="group" aria-label="..." style='margin-bottom:10px;'>
			<?php
				if($user->hasPermission('cnp')) {
					?>
					<a class='btn btn-default btn-get-sales' data-qs='CNP'  title='CNP'>
						<span >CNP</span></span></a>
					<?php
				}
			?>

			<?php
				if($user->hasPermission('daina')) {
					?>
					<a class='btn btn-default btn-get-sales'   data-qs='SODAINA' title='SODAINA'>
						<span >SODAINA</span></span></a>
					<?php
				}
			?>

			<?php
				if($user->hasPermission('mastra')) {
					?>
					<a class='btn btn-default btn-get-sales'   data-qs='MASTRA' title='MASTRA'>
						<span>MASTRA</span></span></a>
					<?php
				}
			?>

			<?php
				if($user->hasPermission('service_sales')) {
					?>
					<a class='btn btn-default btn-get-sales'   data-qs='SERVICE' title='SERVICE'>
						<span>SERVICE</span></span></a>
					<?php
				}
			?>

			<?php
				if($user->hasPermission('assembly_sales')) {
					?>
					<a class='btn btn-default btn-get-sales'   data-qs='A' title='ASSEMBLY'>
						<span>ASSEMBLY</span></span></a>
					<?php
				}
			?>
			<?php
				if($user->hasPermission('tedela')) {
					?>
					<a class='btn btn-default btn-get-sales'   data-qs='Aqua Tedela' title='Aqua Tedela'>
						<span>AQUA TEDELA</span></span></a>
					<?php
				}
			?>

			<?php
				if($user->hasPermission('ebara')) {
					?>
					<a class='btn btn-default btn-get-sales'   data-qs='EBARA' title='EBARA'>
						<span>EBARA</span></span></a>
					<?php
				}
			?>

			<?php
				if($user->hasPermission('black_samurai')) {
					?>
					<a class='btn btn-default btn-get-sales'   data-qs='Black Samurai' title='Black Samurai'>
						<span>Black Samurai</span></span></a>
					<?php
				}
			?>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="col-md-12 text-right">
						<a href="custom_reports.php"  class='btn btn-default btn-sm'>Per Item</a>
						<a href="custom_report_summary.php"  class='btn btn-default btn-sm'>Per Transaction</a>
					</div>
				</div>
				<br>
				<div class="panel panel-primary">
					<!-- Default panel contents -->

					<div class="panel-heading">
						<div class="text-right">
							<button id='btnDl' class='btn btn-default' ><i class='fa fa-download'></i> Download</button>
						</div>
					</div>

					<div class="panel-body">
						<h5 class='txtCurrent'></h5>
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
								<select name="branch_id" id="branch_id" class='form-control' multiple>
									<option value="">Select Branch</option>
									<?php
										foreach($branches as $b ){
											$selected='';
											if($b->id == $user->data()->branch_id){
												$selected = 'selected';
											}
											echo "<option $selected value='$b->id'>$b->name</option>";
										}
									?>
								</select>
								</div>
							</div>
							<div class="col-md-3"></div>
							<div class="col-md-3"></div>
							<div class="col-md-3"></div>
						</div>

						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" autocomplete="off" class='form-control' placeholder='From' id='dt_from'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" autocomplete="off" class='form-control' placeholder='To' id='dt_to'>
								</div>
							</div>
							<div class='col-md-3'>
								<div class="form-group">
									<select name="sales_type" id="sales_type" class='form-control'>
										<option value="">Select sales type</option>
										<?php
											$sales_type = new Sales_type();
											$sales_types = $sales_type->get_active('salestypes',array('company_id','=',$user->data()->company_id));
											foreach($sales_types as $st){

												echo  "<option value='$st->id'>$st->name</option>";
											}
											echo  "<option value='-1'>No Agent</option>";
										?>
									</select>
								</div>
							</div>
						</div>
						<div id="con"></div>
					</div>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function(){
			var default_val = "<?php echo $default; ?>";
			var txtCurrent = $('.txtCurrent');

			$('body').on('click','.btn-get-sales',function(){
				default_val = $(this).attr('data-qs');
				getRecord(0);
			});

			getRecord(0);

			$('#branch_id').select2({
				allowClear: true,
				placeholder:'Select Branch'

			});
			$('#branch_id').change(function(){
				getRecord(0);

			});
			function getRecord(t){
				var dt_from = $('#dt_from').val();
				var dt_to = $('#dt_to').val();
				var sales_type = $('#sales_type').val();
				var branch_id = $('#branch_id').val();
				if(default_val == 'A'){
					txtCurrent.html('Assembly');
				} else {
					txtCurrent.html(default_val);
				}

				$('#con').html('Loading...');
				$.ajax({
				    url:'../ajax/ajax_sales_query.php',
				    type:'POST',
				    data: {functionName:'getCustomRecord',branch_id:JSON.stringify(branch_id),query_string: default_val,t:t,dt_from:dt_from,dt_to:dt_to,sales_type:sales_type},
				    success: function(data){
				        $('#con').html(data);
				    },
				    error:function(){
				        
				    }
				});
			}

			$('body').on('change','#sales_type',function(){
				getRecord(0);
			});

			$('#dt_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_from').datepicker('hide');
				getRecord(0);
			});

			$('#dt_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_to').datepicker('hide');
				getRecord(0);
			});

			$('body').on('click','#btnDl',function(){
				downloadRecord();
			});

			function downloadRecord(){

				var dt_from = $('#dt_from').val();
				var dt_to = $('#dt_to').val();
				var sales_type = $('#sales_type').val();
				window.open(
					'../ajax/ajax_sales_query.php?functionName=getCustomRecord&dt_from='+dt_from+'&dt_to='+dt_to+'&sales_type='+sales_type+'&t=1'+'&query_string='+default_val,
					'_blank' // <- This is what makes it open in a new window.
				);

			}


		});




	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>