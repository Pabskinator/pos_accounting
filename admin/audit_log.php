<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('inventory')) {
		// redirect to denied page
		Redirect::to(1);
	}
	$inv = new Inventory();

	$user_permbranch = $user->hasPermission('inventory_all');

?>



	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Audit log</h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('inventoryflash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('inventoryflash') . "</div>";
			}

		?>
		<div class="row">
			<div class="col-md-12">
				<?php include 'includes/inventory_nav.php'; ?>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">
						<div class='row'>
							<div class='col-md-6'>Inventories</div>
							<div class='col-md-6 text-right'>
								<?php if($user->hasPermission('dl_inv')){ ?>
									<button id='btnDownloadExcel' title='Download Excel' class='btn btn-default btn-sm'><i class='fa fa-download'></i></button>
								<?php } ?>
							</div>
						</div>
					</div>
					<div class="panel-body">
						<div class="row">

							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon"><span class='glyphicon glyphicon-search'></span></span>
										<input type="text" id="search" class='form-control' placeholder='Search..'/>
									</div>
								</div>
							</div>
							<div class="col-md-3">
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
							<div class="col-md-3">
								<div class="form-group">
									<select id="rack_id" name="rack_id" class="form-control">
										<option value=''></option>
										<option value='-1'>Warehouse racks</option>
										<?php
											$rack = new Rack();
											$racks =  $rack->getAllRacks($user->data()->company_id);
											foreach($racks as $b){
												$descrack='';
												if($b->description){
													$descrack = " (" . $b->description . ")";
												}

												?>
												<option value='<?php echo $b->id ?>'><?php echo $b->rack;?>  <?php echo $descrack; ?></option>
												<?php
											}
										?>
									</select>
								</div>
							</div>
							<div class="col-md-3">

							</div>
						</div>
						<div class="row">
							<div class="col-md-3">
							    <div class="form-group">
							        <input type="text" id='date_from' name='date_from'  placeholder='From' class='form-control'>
							    </div>
							</div>
							<div class="col-md-3">
							    <div class="form-group">
							        <input type="text" id='date_to' name='date_to'  placeholder='To' class='form-control'>
							    </div>
							</div>
							<div class="col-md-3">

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
		<div class="modal-dialog modal-lg">
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


			if($('#branch_id').val()){
				$.ajax({
					url:'../ajax/ajax_query2.php',
					type:'POST',
					data: {functionName:'getBranchRack',branch_id:$('#branch_id').val()},
					success: function(data){
						$('#rack_id').html(data);
					},
					error:function(){

					}
				});
			}

			$('#date_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#date_from').datepicker('hide');
				withDate();
			});

			$('#date_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#date_to').datepicker('hide');
				withDate();
			});


			$('#branch_id').select2({
				placeholder:'Choose branch',
				allowClear:true
			});
			$('#rack_id').select2({
				placeholder:'Choose rack',
				allowClear:true
			});

			function withDate(){
				if($('#date_from').val() && $('#date_to').val()){
					getPage(0);
				}
			}


			getPage(0);
			$('body').on('click','.paging',function(e){
				e.preventDefault();
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
				var b = $('#branch_id').val();
				var r = $('#rack_id').val();
				var date_from = $('#date_from').val();
				var date_to = $('#date_to').val();

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,functionName:'amendPaginate',date_from:date_from, date_to:date_to,cid: <?php echo $user->data()->company_id; ?>,search:search,b:b,r:r},
					success: function(data){
						$('#holder').html(data);
					}
				});
			}

			$('body').on('change','#branch_id',function(){

				var b = $('#branch_id').val();
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

			$('body').on('change','#rack_id',function(){
				getPage(0);
			});

			$('body').on('click','#btnDownloadExcel',function(){

				var search = $('#search').val();
				var b = $('#branch_id').val();
				var r = $('#rack_id').val();
				var date_from = $('#date_from').val();
				var date_to = $('#date_to').val();

				window.open(
					'excel_downloader.php?downloadName=audit_log&search='+search+'&b='+b+'&r='+r+'&date_from='+date_from+'&date_to='+date_to,
					'_blank' //
				);

			});





		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>