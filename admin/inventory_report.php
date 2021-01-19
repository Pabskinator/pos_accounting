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
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Reports
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
							<div class='col-md-6'>Report</div>
							<div class='col-md-6 text-right'>
								<?php if(Configuration::thisCompany('cebuhiq')){ ?>
								<a href='inventory_ending.php' class='btn btn-default btn-sm'><i class='fa fa-plus'></i></a>
								<?php } ?>

								<?php if($user->hasPermission('dl_inv_report')){ ?>
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
									<span class='help-block'>Search Item, Description</span>
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
									<span class='help-block'>Search Branch</span>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<select id="rack_id" name="rack_id" class="form-control">
										<option value=''></option>
										<option value='-1'>No Rack</option>
										<?php
											$rack = new Rack();
											$racks =  $rack->getBranchRacks($user->data()->branch_id);
											foreach($racks as $b){
												$descrack='';
												if(!$b->rack) continue;
												if($b->description){
													$descrack = " (" . $b->description . ")";
												}

												?>
												<option value='<?php echo $b->id ?>'><?php echo $b->rack;?> <?php echo $descrack; ?></option>
												<?php
											}
										?>

									</select>
									<span class='help-block'>Search Rack</span>

								</div>
							</div>

						</div>
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<input type="text" id="txtFrom" class='form-control' placeholder='Date From'/>
									<span class='help-block'>Start Date</span>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<input type="text" id="txtTo" class='form-control' placeholder='Date To'/>
									<span class='help-block'>End Date</span>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<select id="group_by" name="group_by" class="form-control">

										<option value='0'>By Rack</option>
										<option value='1'>By Item</option>
									</select>
									<span class='help-block'>Group By</span>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<select name="display_type" id="display_type" class='form-control'>
										<option value="1">Detailed Info</option>
										<option value="2">Current Inventory</option>
									</select>
									<span class='help-block'>Display Type</span>
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
			$('#rack_id').select2({
				allowClear:true,
				placeholder:'Select Rack'
			});
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
				})
				getPage(0);
			});

			$('body').on('change','#rack_id,#group_by,#display_type',function(){
				getPage(0);
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
			getPage(0);
			function getPage(p){
				var search = $('#searchSales').val();
				var b = $('#branch_id').val();
				var r = $('#rack_id').val();
				var from = $('#txtFrom').val();
				var to = $('#txtTo').val();
				var group_by = $('#group_by').val();
				var display_type = $('#display_type').val();

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{group_by:group_by,page:p,display_type:display_type,from:from,to:to,functionName:'inventoryReportPaginate',cid: <?php echo $user->data()->company_id; ?>,search:search,b:b,r:r},
					success: function(data){
						$('#holder').html(data);
					}
				});
			}
			$('body').on('click','#btnDownloadExcel',function(){
				var search = $('#searchSales').val();
				var b = $('#branch_id').val();
				var r = $('#rack_id').val();
				var from = $('#txtFrom').val();
				var to = $('#txtTo').val();
				var group_by = $('#group_by').val();
				var display_type = $('#display_type').val();
				window.open(
					'excel_downloader.php?downloadName=inventoryReport&search='+search+'&b='+b+'&r='+r+'&from='+from+'&to='+to+'&group_by='+group_by+'&display_type='+display_type,
					'_blank' //
				);
			});
			$('body').on('click','.paging',function(e){
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page,'');
			});
		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>