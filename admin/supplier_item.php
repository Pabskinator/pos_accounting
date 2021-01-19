<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('supplier_si')){
		// redirect to denied page
		Redirect::to(1);
	}


	$supplier = new Supplier();
	$suppliers = $supplier->get_active('suppliers',array('company_id' ,'=',$user->data()->company_id));


?>



	<!-- Page content -->
<div id="page-content-wrapper">




	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<?php include 'includes/supplier_nav.php'; ?>
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				 Supplier Item
			</h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')){
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('flash')."</div>";
			}
		?>
		<div class="row">
			<div class="col-md-12">



				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading"></div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<select name="supplier_id" id="supplier_id" class='form-control'>
										<option value=""></option>
										<?php
											foreach($suppliers as $sup):
										?>
											<option value="<?php echo $sup->id; ?>"><?php echo $sup->name; ?></option>
												<?php endforeach;?>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="hidden" class='form-control selectitem' id='item_id'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<button class='btn btn-default' id='btnSubmit'>Submit</button>
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

		$(document).ready(function(){
			$('#supplier_id').select2({
				placeholder:'Supplier Name',
				allowClear: true
			});
			getPage(0,'');
			$('body').on('click','.paging',function(){
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});
			$("#search").keyup(function(){

				getPage(0);
			});

			$('body').on('click','#btnSubmit',function(){
				getPage(0);
			});

			function getPage(p){
				var search = $('#search').val();
				var item_id =  $('#item_id').val();
				var supplier_id =  $('#supplier_id').val();

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,item_id:item_id,supplier_id:supplier_id,functionName:'supplierItemPaginate',cid: <?php echo $user->data()->company_id; ?>,search:search},
					success: function(data){
						$('#holder').html(data);
					}
				});
			}

			$('body').on('click', '.deleteSupplierItem', function(e) {
				e.preventDefault();
				if(confirm("Are you sure you want to delete this record?")) {
					var id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php', {id: id, table: 'supplier_item'}, function(data) {
						if(data == "true") {
							location.reload();
						}
					});
				}
			});
		});



	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>