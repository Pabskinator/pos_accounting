<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('inv_mon')) {
		// redirect to denied page
		Redirect::to(1);
	}

	$user_permbranch = $user->hasPermission('inventory_all');

?>



	<!-- Page content -->
<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
			<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Inventory Monitoring
			</h1>
		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash'))
			{
				 echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}
		?>
		<div class="row">
			<div class="col-md-12">
				<?php include 'includes/inventory_nav.php'; ?>

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">
						<div class='row'>
							<div class='col-md-6'>Monitoring</div>
							<div class='col-md-6 text-right'>
								<?php if($user->hasPermission('dl_inv_mon')){ ?>
								<button id='btnDownloadExcel' title='Download Excel' class='btn btn-default btn-sm'><i class='fa fa-download'></i></button>
								<?php } ?>
							</div>
						</div>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon"><span class='glyphicon glyphicon-search'></span></span>
										<input type="text" id="searchSales" class='form-control' placeholder='Search..'/>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<select  <?php echo (!$user_permbranch) ? 'disabled' : ''; ?> id="branch_id" name="branch_id" class="form-control">
										<option value=''></option>
										<?php
											$branch = new Branch();
											$branches =  $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));
											foreach($branches as $b){
												$a = $user->data()->branch_id;
												if($a==$b->id){
													$selected='selected';
												} else {
													$selected='';
												}
												?>
												<option value='<?php echo $b->id ?>' <?php echo $selected ?>><?php echo $b->name;?> </option>
											<?php
											}
										?>
									</select>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<select id="rack_id" name="rack_id" class="form-control">
										<option value=''></option>
										<option value='-1'>No Rack</option>
										<?php
											$rack = new Rack();
											$racks =  $rack->getAllRacks($user->data()->company_id);
											foreach($racks as $b){
												$descrack='';
												if($b->description){
													$descrack = " (" . $b->description . ")";
												}

												?>
												<option value='<?php echo $b->id ?>'><?php echo $b->rack;?> <?php echo $descrack; ?></option>
											<?php
											}
										?>

									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
										<input type="text" id="txtFrom" class='form-control' placeholder='Date From'/>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<input type="text" id="txtTo" class='form-control' placeholder='Date To'/>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<select   id="branch_id2" name="branch_id2" class="form-control">
										<option value=''></option>
										<?php

											foreach($branches as $b){

												?>
												<option value='<?php echo $b->id ?>'><?php echo $b->name;?> </option>
												<?php
											}
										?>
									</select>
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
			$('#branch_id').select2({
				allowClear:true,
				placeholder:'Select Branch'
			});
			$('#branch_id2').select2({
				allowClear:true,
				placeholder:'Transfer To'
			});
			$('#rack_id').select2({
				allowClear:true,
				placeholder:'Select Rack'
			});
			getPage(0);
			getBranchRack();



			$('body').on('click','.paging',function(){
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});

			var timer;

			$("#searchSales").keyup(function(){

				var searchtxt = $("#searchSales");

				clearTimeout(timer);

				timer = setTimeout(function() {
					if(searchtxt.val()){
						searchtxt.val(searchtxt.val().trim());
					}
					getPage(0);
				}, 1000);

			});

			function getPage(p){
				var search = $('#searchSales').val();
				var b = $('#branch_id').val();
				var branch_id2 = $('#branch_id2').val();
				var r = $('#rack_id').val();
				var from = $('#txtFrom').val();
				var to = $('#txtTo').val();
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,from:from,to:to,branch_id2:branch_id2,functionName:'inventoryMonitoringPaginate',cid: <?php echo $user->data()->company_id; ?>,search:search,b:b,r:r},
					success: function(data){

						$('#holder').html(data);

					}
				});
			}

			$('body').on('change','#branch_id',function(){
				var b = $(this).val();
				$.ajax({
					url:'../ajax/ajax_query2.php',
					type:'POST',
					data: {functionName:'getBranchRack',branch_id:b},
					success: function(data){
						$('#rack_id').html(data);
					},
					error:function(){

					}
				});
				getPage(0);
			});
			function getBranchRack(){
				var b = $('#branch_id').val();
				if(b){
					$.ajax({
						url:'../ajax/ajax_query2.php',
						type:'POST',
						data: {functionName:'getBranchRack',branch_id:b},
						success: function(data){
							$('#rack_id').html(data);
						},
						error:function(){

						}
					});
				}

			}
			$('body').on('change','#rack_id,#branch_id2',function(){
				getPage(0);
			});
			$('#txtFrom').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#txtFrom').datepicker('hide');
				if($('#txtFrom').val() && $('#txtTo').val()){
					getPage(0)
				}
			});
			$('#txtTo').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#txtTo').datepicker('hide');
				if($('#txtFrom').val() && $('#txtTo').val()){
					getPage(0)
				}
			});
			$('body').on('click','#btnDownloadExcel',function(){
				var search = $('#searchSales').val();
				var b = $('#branch_id').val();
				var r = $('#rack_id').val();
				var from = $('#txtFrom').val();
				var to = $('#txtTo').val();
				var branch_id2 = $('#branch_id2').val();

				if(from && to){
					window.open(
						'excel_downloader.php?downloadName=inventoryMonitoring&search='+search+'&b='+b+'&r='+r+'&from='+from+'&to='+to+'&branch_id2='+branch_id2,
						'_blank' //
					);
				} else {
					alert("Please add dates when downloading records.");
				}

			});
		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>