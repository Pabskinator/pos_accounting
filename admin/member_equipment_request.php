<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('mem_equipment')) {
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
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>Member Equipment Request </h1>

		</div>
		<?php include 'includes/member_equipment_nav.php' ?>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">
						<div class='row'>
							<div class='col-md-6'>List</div>
							<div class='col-md-6 text-right'>
							</div>
						</div>
					</div>
					<div class="panel-body">

						<div id='test2'></div>
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
									<select class='form-control' name="status" id="status">
										<option value="0">Pending</option>
										<option value="1">Processed</option>

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
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
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

			getPage(0);
			$('body').on('change','#status',function(){
				getPage(0);
			});
			$('body').on('click','.paging',function(e){
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});
			$("#search").keyup(function(){
				getPage(0);
			});
			$('#province').select2({
				placeholder:"Select Province",
				allowClear: true
			})
			function getPage(p){
				var search = $('#search').val();
				var status = $('#status').val();

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,status:status,functionName:'memberEquipmentRequest',cid: <?php echo $user->data()->company_id; ?>,search:search},
					success: function(data){
						$('#holder').html(data);
					}
				});
			}

			$('body').on('click','.btnDetails',function(){
				var con = $(this);
				var id = con.attr('data-id');

				$('#myModal').modal('show');
				$('#mbody').html("Loading...");

				$.ajax({
				    url:'../ajax/ajax_member_equipment.php',
				    type:'POST',
				    data: {functionName:'getDetails',id:id},
				    success: function(data){
					    $('#mbody').html(data);
				    },
				    error:function(){

				    }
				});

			});

			$('body').on('keyup','.txtQty',function(){

				var con = $(this);
				var qty = parseFloat(con.val());
				if(!qty) qty = 0;
				var orig_qty = parseFloat(con.attr('data-orig_qty'));

				if(qty > orig_qty){
					tempToast("error","Invalid quantity.","Error")
					con.val('');
				}

			});

			$('body').on('click','.btnProcess',function(){

				var con = $(this);
				var id = con.attr('data-id');
				button_action.start_loading(con);
				var return_arr = [];

				$('.txtQty').each(function(){
					var txt = $(this);
					var qty = parseFloat(txt.val());
					var item_id = txt.attr('data-item_id');
					var orig_qty = txt.attr('data-orig_qty');
					if(!qty) qty = 0;
					return_arr.push({qty:qty,item_id:item_id,orig_qty:orig_qty});

				});

				$.ajax({
					url:'../ajax/ajax_member_equipment.php',
				    type:'POST',
				    data: {functionName:'processRequest',id:id,data:JSON.stringify(return_arr)},
				    success: function(data){
				        tempToast('info',data,'Info');
					    getPage(0);
					    button_action.end_loading(con);
					    $('#myModal').modal('hide');
				    },
				    error:function(){

				    }
				});

			});


		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>