<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head
	require_once '../includes/admin/page_head2.php';

	if(!$user->hasPermission('inventory_issues')) {
		Redirect::to(1);
	}


?>

	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Issues Log
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
				<?php include 'includes/issues_nav.php'; ?>

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">
						<div class="row">
							<div class="col-md-6">
								Issues Log
							</div>
							<div class="col-md-6 text-right">
								<button class='btn btn-default btn-sm' id='btnDownloadExcel'><i class='fa fa-download'></i></button>
							</div>
						</div>


					</div>
					<div class="panel-body">
						<div class="row">

							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon"><span class='glyphicon glyphicon-search'></span></span>
										<input type="text" id="searchSales" class='form-control' placeholder='Search..'/>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select id="branch_id" name="branch_id" class="form-control">
										<option value=''></option>
										<?php
											$branch = new Branch();
											$branches =  $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));
											foreach($branches as $b){
												$a = isset($id) ? $terminal->data()->branch_id : escape(Input::get('branch_id'));

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
										<?php
											$rack = new Rack();
											$racks =  $branch->get_active('racks',array('company_id' ,'=',$user->data()->company_id));
											foreach($racks as $b){
												?>
												<option value='<?php echo $b->id ?>'><?php echo $b->rack;?> </option>
												<?php
											}
										?>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select id="type" name="type" class="form-control">
										<option value=''></option>
										<option value='1'><?php echo DAMAGE_LABEL; ?></option>
										<option value='2'><?php echo MISSING_LABEL; ?></option>
										<option value='4'><?php echo INCOMPLETE_LABEL; ?></option>
										<?php
											if(OTHER_ISSUE_LABEL){
											?>
												<option value='5'><?php echo OTHER_ISSUE_LABEL; ?></option>
											<?php
											}
										?>
										<option value='3'>Disposed</option>
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
				placeholder:'Choose branch',
				allowClear:true
			});

			$('#rack_id').select2({
				placeholder:'Choose rack',
				allowClear:true
			});

			$('#type').select2({
				placeholder:'Choose type',
				allowClear:true
			});

			getPage(0);

			$('body').on('click','.paging',function(){
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});

			$("#searchSales").keyup(function(){
				getPage(0);
			});

			function getPage(p){

				var search = $('#searchSales').val();
				var b = $('#branch_id').val();
				var r = $('#rack_id').val();
				var type = $('#type').val();

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,functionName:'issuesLogPaginate',cid: <?php echo $user->data()->company_id; ?>,search:search,b:b,r:r,type:type},
					success: function(data){
						$('#holder').html(data);
					}
				});
			}

			$('body').on('change','#branch_id,#type,#rack_id',function(){
				getPage(0);
			});

			$('body').on('click','#btnDownloadExcel',function(){

				var search = $('#searchSales').val();
				var b = $('#branch_id').val();
				var r = $('#rack_id').val();
				var type = $('#type').val();

				window.open(
					'excel_downloader.php?downloadName=issues_log&search='+search+'&b='+b+'&r='+r+'&type='+type,
					'_blank' //
				);

			});


		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>