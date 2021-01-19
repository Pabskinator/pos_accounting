<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('discount')) {
		// redirect to denied page
		Redirect::to(1);
	}


?>


	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">

		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Discount List
			</h1>
		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}
		?>

		<div class="row">
			<div class="col-md-12">
				<div class="form-group">
					<a class='btn btn-default' href='adddiscount.php'><span class='glyphicon glyphicon-plus'></span> Discount</a>
				</div>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Discounts</div>
					<div class="panel-body">
						<div class="row">

							<div class="col-md-3">
								<div class="input-group">
									<span class="input-group-addon"><span class='glyphicon glyphicon-search'></span></span>
									<input type="text" id="search" class='form-control' placeholder='Search..'/>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' name='dateFrom' placeholder='From' id='dateFrom'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' name='dateTo' placeholder='To' id='dateTo'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select  class='form-control' name="branch_id" id="branch_id" style='width:100%;'>
										<option value=""></option>
										<?php
											$pbranch = new Branch();
											$pbranches = $pbranch->get_active('branches',array('company_id','=',$user->data()->company_id));
											if($pbranches){
												foreach($pbranches as $brow){
													?>
													<option value="<?php echo $brow->id; ?>"><?php echo $brow->name; ?></option>
													<?php
												}
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
		<div id="test"></div>
	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function() {
			$('#branch_id').select2({
				allowClear: true,
				placeholder:'Branch'
			});
			getPage(0);
			$('body').on('click','.paging',function(){
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				var search = $('#search').val();
				var date_start= $('#dateFrom').val();
				var date_end= $('#dateTo').val();
				var branch_id = $('#branch_id').val();
				getPage(page,search,date_start,date_end,branch_id);
			});
			$("#search").keyup(function(){
				var search = $('#search').val();
				var date_start= $('#dateFrom').val();
				var date_end= $('#dateTo').val();
				var branch_id = $('#branch_id').val();
				getPage(0,search,date_start,date_end,branch_id);
			});
			$("#branch_id").change(function(){
				var search = $('#search').val();
				var date_start= $('#dateFrom').val();
				var date_end= $('#dateTo').val();
				var branch_id = $('#branch_id').val();
				getPage(0,search,date_start,date_end,branch_id);
			});

			function getPage(p,search,date_start,date_end,branch_id){
				$('.loading').show();
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					data:{page:p,date_start:date_start,date_end:date_end,branch_id:branch_id, functionName:'discountPaginate',cid: <?php echo $user->data()->company_id; ?>,search:search},
					success: function(data){
						$('#holder').empty();
						$('#holder').append(data);
						$('.loading').hide();
					},
					error: function(){
						alert('Something went wrong. The page will be refresh.');
						$('.loading').hide();
					}
				});
			}

			$('#dateFrom').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dateFrom').datepicker('hide');
				var search = $('#search').val();
				var date_start= $('#dateFrom').val();
				var date_end= $('#dateTo').val();
				var branch_id = $('#branch_id').val();
				getPage(0,search,date_start,date_end,branch_id);
			});
			$('#dateTo').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dateTo').datepicker('hide');
				var search = $('#search').val();
				var date_start= $('#dateFrom').val();
				var date_end= $('#dateTo').val();
				var branch_id = $('#branch_id').val();
				getPage(0,search,date_start,date_end,branch_id);
			});

		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>