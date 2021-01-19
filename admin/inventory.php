<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';

	if(!$user->hasPermission('inventory')) {
		// redirect to denied page
		Redirect::to(1);
	}
	$inv = new Inventory();

	$ccc = new Category();
	$noparent = $ccc->getNoparent($user->data()->company_id);
	$categselect = '';
	if($noparent){
		foreach($noparent as $cat){
			$categselect .= "<option value='$cat->id'>$cat->name</option>";
		}
	}
	$user_permbranch = $user->hasPermission('inventory_all');

?>


	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1><span id="menu-toggle" class='glyphicon glyphicon-list'></span> Manage Inventories </h1>
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
											<input type="text" id="searchItem" class='form-control' placeholder='Search..'/>
										</div>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">

									<select  <?php echo (!$user_permbranch) ? 'disabled' : ''; ?> id="branch_id" name="branch_id" class="form-control" multiple>
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
									<div class="form-group">
										<select style='display:none;' id="supplier_id" name="supplier_id" class="form-control">
											<option value=''></option>
											<?php
												$supcls = new Supplier();
												$supplierlist =  $branch->get_active('suppliers',array('company_id' ,'=',$user->data()->company_id));
												foreach($supplierlist as $sup){
													?>
													<option value='<?php echo $sup->id ?>'><?php echo $sup->name;?> </option>
												<?php
												}
											?>
										</select>
									</div>
								</div>
								</div>
							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" class='form-control' id='txtRack' placeholder='Search Rack Group'>
										<span class='help-block'>Search for rack group (1A-) or specific rack (1a-A1a)</span>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<select id="category_id" name="category_id" class="form-control ">
											<option value=""></option>
											<?php echo $categselect; ?>
										</select>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<input id="rack_tag_id" name="rack_tag_id" class="form-control ">

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
				var cur_branch = $('#branch_id').val();
				if(cur_branch.length == 1){

					$.ajax({
						url:'../ajax/ajax_query2.php',
						type:'POST',
						data: {functionName:'getBranchRack',branch_id:cur_branch[0]},
						success: function(data){
							$('#rack_id').html(data);
						},
						error:function(){

						}
					});
				} else {
					$('#rack_id').html('');
				}

			}

			$('#branch_id').select2({
				placeholder:'Choose branch',
				allowClear:true
			});
			$('#rack_id').select2({
				placeholder:'Choose rack',
				allowClear:true
			});
			$('#supplier_id').select2({
				placeholder:'Choose Supplier',
				allowClear:true
			});

			$("#category_id").select2({
				placeholder: 'Choose Category',
				allowClear: true
			});

			$('#rack_tag_id').select2({
				placeholder: 'Search tags',
				allowClear: true,
				minimumInputLength: 2,
				ajax: {
					url: '../ajax/ajax_json.php',
					dataType: 'json',
					type: "POST",
					quietMillis: 50,
					data: function (term) {
						return {
							q: term,
							functionName:'rack_tags'
						};
					},
					results: function (data) {
						return {
							results: $.map(data, function (item) {
								return {
									text: item.tag_name,
									slug: item.tag_name,
									id: item.id
								}
							})
						};
					}
				}
			});
				getPage(0);
				$('body').on('click','.paging',function(e){
					e.preventDefault();
					var page = $(this).attr('page');
					$('#hiddenpage').val(page);
					var search = $('#searchItem').val();
					var b = $('#branch_id').val();
					var r = $('#rack_id').val();
					var s = $('#supplier_id').val();
					getPage(page,search,b,r,s);
				});
				var timer;
				$("#searchItem,#txtRack").keyup(function(){

					var searchtxt = $("#searchItem");

					var search = searchtxt.val();

					var b = $('#branch_id').val();
					var r = $('#rack_id').val();
					var s = $('#supplier_id').val();

					clearTimeout(timer);
						timer = setTimeout(function() {

							if(searchtxt.val()){
								searchtxt.val(searchtxt.val().trim());
							}

							getPage(0,search,b,r,s);

					}, 1000);

				});

				function getPage(p,search,b,r,s){

					var txtRack  = $('#txtRack').val();
					var category_id = $("#category_id").val();
					var rack_tag_id = $("#rack_tag_id").val();
					b = $("#branch_id").val();

					$.ajax({
						url: '../ajax/ajax_paging.php',
						type:'post',
						beforeSend:function(){
							$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
						},
						data:{page:p,rack_tag_id:rack_tag_id,txtRack:txtRack,category_id:category_id,functionName:'inventoryPaginate',cid: <?php echo $user->data()->company_id; ?>,search:search,b:JSON.stringify(b),r:r,s:s},
						success: function(data){
							$('#holder').html(data);
						}
					});

				}

				$('body').on('change','#branch_id,#category_id,#rack_tag_id',function(){
					var search = $('#searchItem').val();
					var b = $('#branch_id').val();
					var r = $('#rack_id').val();
					var s =  $('#supplier_id').val();
					if(b.length == 1){
						$.ajax({
							url:'../ajax/ajax_query2.php',
							type:'POST',
							data: {functionName:'getBranchRack',branch_id:b[0]},
							success: function(data){
								$('#rack_id').html(data);
							},
							error:function(){

							}
						});
					} else {
						$('#rack_id').html('');
					}

					getPage(0,search,b,r,s);
				});

				$('body').on('change','#rack_id',function(){
					var search = $('#searchItem').val();
					var b = $('#branch_id').val();
					var r = $('#rack_id').val();
					var s =  $('#supplier_id').val();
					getPage(0,search,b,r,s);
				});
			$('body').on('change','#supplier_id',function(){
				var search = $('#searchItem').val();
				var b = $('#branch_id').val();
				var r = $('#rack_id').val();
				var s =  $('#supplier_id').val();

				getPage(0,search,b,r,s);
			});
			$('body').on('click','#btnDownloadExcel',function(){
				var search = $('#searchItem').val();
				var b = $('#branch_id').val();
				var r = $('#rack_id').val();
				var s =  $('#supplier_id').val();
				var txtRack  = $('#txtRack').val();
				var category_id = $("#category_id").val();

				b = JSON.stringify(b);

				window.open(
					'excel_downloader.php?downloadName=inventories&search='+search+'&b='+b+'&r='+r+'&s='+s+'&txtRack='+txtRack+'&category_id='+category_id,
					'_blank' //
				);
			});
			
			$('body').on('click','.btnPrint',function(){


			});
			
			$('body').on('click','.btnReorderDetails',function(e){
				e.preventDefault();
				var con = $(this);
				var item_id = con.attr('data-item_id');
				var branch_id = con.attr('data-branch_id');
				$('#myModal').modal('show');
				$('#mbody').html('Loading...');
				$.ajax({
				    url:'../ajax/ajax_query.php',
				    type:'POST',
				    data: {functionName:'getReOrderDetails',item_id:item_id,branch_id:branch_id},
				    success: function(data){
					    $('#mbody').html(data);
				    },
				    error:function(){

				    }
				})
			});

		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>