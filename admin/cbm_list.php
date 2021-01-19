<?php
	// $user have all the properties and method of the current user


	require_once '../includes/admin/page_head2.php';

	if(!$user->hasPermission('item_service_r')) {
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
			<span id="menu-toggle" class='glyphicon glyphicon-list'></span>CBM and Weight Summary
		</h1>
	</div>


	<?php

		// get flash message if add or edited successfully

		if(Session::exists('flash')) {
			echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
		}

	?>



	<div id="test"></div>

	<div class="row">
		<div class="col-md-12">

			<?php include 'includes/product_nav.php'; ?>
			<div class="panel panel-primary">
				<!-- Default panel contents -->
				<div class="panel-heading">CBM and Weight</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<input type="text" class='form-control' id='search' placeholder='Search Item'>
							</div>
						</div>
					</div>
					<input type="hidden" id="hiddenpage" />
					<div id="holder"></div>
				</div>
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
					<input type="hidden" class='form-control' id='update_id'>
					<div class="form-group">
						<input type="text" class='form-control' id='update_cbm_l'>
						<span class='help-block'>CBM Length</span>
					</div>
					<div class="form-group">
						<input type="text" class='form-control' id='update_cbm_w'>
						<span class='help-block'>CBM Width</span>
					</div>
					<div class="form-group">
						<input type="text" class='form-control' id='update_cbm_h'>
						<span class='help-block'>CBM Height</span>
					</div>
					<div class="form-group">
						<input type="text" class='form-control' id='update_item_weight'>
						<span class='help-block'>Item Weight</span>
					</div>
					<div class="form-group">
						<button class='btn btn-primary' id='btnSave'>Save</button>
					</div>

				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->


	<script>

		$(function(){
			$('body').on('click','#btnSave',function(){

				var id = $('#update_id').val();
				var cbm_l = $('#update_cbm_l').val();
				var cbm_w = $('#update_cbm_w').val();
				var cbm_h = $('#update_cbm_h').val();
				var item_weight = $('#update_item_weight').val();

				$.ajax({
				    url:'../ajax/ajax_product.php',
				    type:'POST',
				    data: {functionName:'updateCBM',id:id,cbm_l:cbm_l,cbm_w:cbm_w,cbm_h:cbm_h, item_weight:item_weight},
				    success: function(data){
					    var page =   $('#hiddenpage').val();
					    getPage(page);
					    $('#myModal').modal('hide');
					    tempToast('info',data,'Info');
				     },
				    error:function(){

				    }
				});

			});
			$('body').on('click','.btnUpdate',function(){
				var con = $(this);

				var id = con.attr('data-id');
				var cbm_l = con.attr('data-cbm_l');
				var cbm_w = con.attr('data-cbm_w');
				var cbm_h = con.attr('data-cbm_h');
				var item_weight = con.attr('data-item_weight');
				$('#update_id').val(id);
				$('#update_cbm_l').val(cbm_l);
				$('#update_cbm_w').val(cbm_w);
				$('#update_cbm_h').val(cbm_h);
				$('#update_item_weight').val(item_weight);

				$('#myModal').modal('show');
			});

			$('body').on('click','.paging',function(e){
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});

			getPage();

			function getPage(p){

				var s = $('#search').val();

				$.ajax({
					url: '../ajax/ajax_paging_2.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,functionName:'cbmList',s:s,cid: <?php echo $user->data()->company_id; ?> },
					success: function(data){

						$('#holder').html(data);

					}
				});
			}


		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>